<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:80',
            'monogram' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'typography' => 'nullable|max:40',
            'template'   => 'nullable|integer',
            'date'       => 'required|date',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'song'       => 'nullable|file|mimes:mp3,wav,mp4,mov,webm|max:100200',
            'song_cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'watermark'  => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $uuid = Str::uuid();
            $folderName = str_replace('-', '', $uuid->toString());
            $albumUuid = Str::uuid();
            $albumHex = str_replace('-', '', $albumUuid->toString());

            // Preparamos los datos SOLAMENTE para la tabla events
            $eventData = [
                'id'         => $uuid->getBytes(),
                'album'      => hex2bin($albumHex),
                'id_user'    => Auth::id(),
                'name'       => $validated['name'] ?? null,
                'monogram' => $validated['monogram'] ?? null,
                'typography' => $validated['typography'] ?? null,
                'template'   => $request->input('template', 0),
                'date'       => $validated['date'],
            ];

            // Guardar Monograma (imagen)
            if ($request->hasFile('monogram')) {
                $monogramFile = $request->file('monogram');
                $monogramName = substr($monogramFile->getClientOriginalName(), -50);

                $monogramFile->storeAs($folderName, $monogramName, 'local');

                $eventData['monogram'] = $monogramName; // guardas el nombre
            }

            // Guardar Canción o video
            if ($request->hasFile('song')) {
                $songFile = $request->file('song');
                $songName = substr($songFile->getClientOriginalName(), -50);
                $songFile->storeAs($folderName, $songName, 'local');
                $eventData['song'] = $songName;
            }

            // Guardar Portada de la canción
            if ($request->hasFile('song_cover')) {
                $coverFile = $request->file('song_cover');
                $coverName = substr($coverFile->getClientOriginalName(), -50);
                $coverFile->storeAs($folderName, $coverName, 'local');
                $eventData['song_cover'] = $coverName;
            }

            // 1. PRIMERO CREAMOS EL EVENTO
            $event = Event::create($eventData);

            // 2. GUARDAMOS LA FOTO PRINCIPAL
            if ($request->hasFile('main_image')) {
                $mainImageFile = $request->file('main_image');
                $mainImageName = substr($mainImageFile->getClientOriginalName(), -50);

                $mainImageFile->storeAs($folderName, $mainImageName, 'local');

                DB::table('photos')->insert([
                    'id'       => Str::uuid()->getBytes(),
                    'url'      => $mainImageName,
                    'id_event' => $event->id,
                ]);
            }

            return redirect()->route('events.qr', $event->id_hex);
        } catch (\Exception $e) {
        }
    }

    public function qr($id)
    {
        if (!ctype_xdigit($id) || strlen($id) !== 32) {
            abort(404);
        }

        $event = Event::where('id', hex2bin($id))->firstOrFail();

        $url_evento = route('events.show', $event->id_hex);
        $url_album  = route('album.show', $event->album_hex);

        return view('events.qr', compact('url_evento', 'url_album'));
    }

    public function camera($id_evento)
    {
        if (!ctype_xdigit($id_evento) || strlen($id_evento) !== 32) {
            abort(404);
        }

        $event = Event::where('id', hex2bin($id_evento))->firstOrFail();

        return view('events.camera', compact('event'));
    }

    public function show($folder)
    {
        $eventId = hex2bin($folder);
        $event = Event::where('id', $eventId)->firstOrFail();

        // 1. COMPROBAR SI ESTÁ ACTIVO
        if (!$event->is_active) {
            // Si está desactivado, mostramos la pantalla de clausura
            return view('events.thank-you', compact('event'));
        }

        // Si está activo, el flujo sigue normal...
        $photo = DB::table('photos')->where('id_event', $eventId)->first();

        $imageUrl = $photo
            ? route('file.show', ['id_evento' => $folder, 'filename' => $photo->url])
            : asset('images/fondo-papel.jpg');

        return view('events.show', compact('event', 'imageUrl'));
    }

    public function toggleStatus(Request $request, $id_hex)
    {
        try {
            $id = hex2bin($id_hex);
            $event = Event::findOrFail($id);

            // Invertimos el estado (si era true, pasa a false y viceversa)
            $event->is_active = !$event->is_active;
            $event->save();

            return response()->json([
                'success' => true,
                'is_active' => $event->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function serveFile(string $id_evento, string $filename)
    {
        $path = $id_evento . '/' . $filename;

        // Verificamos si el archivo existe
        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        // Sacamos la ruta absoluta del servidor y la enviamos al navegador
        $rutaFisica = Storage::disk('local')->path($path);

        return response()->file($rutaFisica);
    }

    public function music($id_hex)
    {
        try {
            $id = hex2bin($id_hex);
            $event = Event::findOrFail($id);

            // En lugar de una URL pública, le damos la ruta de nuestro "puente seguro"
            $songUrl = $event->song ? route('events.stream-song', $id_hex) : null;

            return view('events.music', compact('event', 'songUrl'));
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('swal_error', 'No se pudo cargar la música de este evento.');
        }
    }

    /**
     * Transmite el archivo de audio privado.
     */
    public function streamSong($id_hex)
    {
        $id = hex2bin($id_hex);
        $event = Event::findOrFail($id);

        if (!$event->song) {
            abort(404);
        }

        $path = storage_path('app/private/' . $id_hex . '/' . $event->song);
        $pathString = storage_path('app/private/' . ((string) $event->id) . '/' . $event->song);

        $finalPath = file_exists($path) ? $path : (file_exists($pathString) ? $pathString : null);

        if (!$finalPath) {
            dd("Sigo sin encontrarlo. Tu archivo se llama: {$event->song}. Busqué en la carpeta: {$id_hex}");
        }

        // Detecta automáticamente si es video o audio para que el navegador no se confunda
        $mime = mime_content_type($finalPath);

        return response()->file($finalPath, [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes'
        ]);
    }

    public function streamCover($id_hex)
    {
        $id = hex2bin($id_hex);
        $event = Event::findOrFail($id);

        if (!$event->song_cover) {
            abort(404);
        }

        $path = storage_path('app/private/' . $id_hex . '/' . $event->song_cover);
        $pathString = storage_path('app/private/' . ((string) $event->id) . '/' . $event->song_cover);

        $finalPath = file_exists($path) ? $path : (file_exists($pathString) ? $pathString : null);

        if (!$finalPath) abort(404);

        $mime = mime_content_type($finalPath);

        return response()->file($finalPath, ['Content-Type' => $mime]);
    }
    public function toggleAlbum($id_hex)
    {
        try {
            $id = hex2bin($id_hex);
            $event = Event::findOrFail($id);

            $event->album_active = !$event->album_active;
            $event->save();

            return response()->json([
                'success' => true,
                'album_active' => $event->album_active
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    public function destroy($id_hex)
    {
        try {
            $id = hex2bin($id_hex);
            $event = Event::findOrFail($id);

            // 1. Obtener las rutas exactas de las carpetas en el disco 'local'
            // El evento está en la raíz del disco: storage/app/private/{id_evento}
            $eventFolder = $id_hex; 
            
            // El álbum está en la subcarpeta: storage/app/private/events/{id_album}
            // Usamos bin2hex() para asegurar que le pasamos la cadena de texto limpia
            $albumFolder = 'events/' . bin2hex($event->album); 

            // 2. Eliminar fotos de la tabla pivote para evitar errores de llave foránea
            DB::table('photos')->where('id_event', $id)->delete();

            // 3. Destruir las carpetas físicas y todo su contenido
            if (Storage::disk('local')->exists($eventFolder)) {
                Storage::disk('local')->deleteDirectory($eventFolder);
            }
            
            if (Storage::disk('local')->exists($albumFolder)) {
                Storage::disk('local')->deleteDirectory($albumFolder);
            }

            // 4. Finalmente, eliminar el evento de la base de datos
            $event->delete();

            return redirect()->route('dashboard')->with('success', 'Evento y archivos eliminados permanentemente.');

        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('swal_error', 'Hubo un problema al intentar eliminar el evento.');
        }
    }
}
