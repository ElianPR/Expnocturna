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
            'song'       => 'nullable|file|mimes:mp3,wav|max:10240',
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

            // Guardar Canción
            if ($request->hasFile('song')) {
                $songFile = $request->file('song');
                $songName = substr($songFile->getClientOriginalName(), -50);
                $songFile->storeAs($folderName, $songName, 'local');
                $eventData['song'] = $songName;
            }

            // Guardar Marca de Agua
            if ($request->hasFile('watermark')) {
                $watermarkFile = $request->file('watermark');
                $watermarkName = substr($watermarkFile->getClientOriginalName(), -50);
                $watermarkFile->storeAs($folderName, $watermarkName, 'local');
                $eventData['watermark'] = $watermarkName;
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
        // 1. Decodificamos a binario SOLO para buscar en la base de datos
        $id = hex2bin($id_hex);
        $event = Event::findOrFail($id);

        if (!$event->song) {
            abort(404);
        }

        // 2. EL TRUCO: Armamos la ruta usando $id_hex (letras y números limpios), 
        // no el $id binario que rompe las carpetas de Windows.
        $path = storage_path('app/private/' . $id_hex . '/' . $event->song);

        // Por si acaso el sistema lo guardó usando el ID convertido a string en lugar de hex
        $pathString = storage_path('app/private/' . ((string) $event->id) . '/' . $event->song);

        // Determinamos cuál es la ruta correcta donde realmente vive el archivo
        $finalPath = null;
        if (file_exists($path)) {
            $finalPath = $path;
        } elseif (file_exists($pathString)) {
            $finalPath = $pathString;
        }

        if (!$finalPath) {
            // Si esto vuelve a salir, significa que la carpeta física en Windows 
            // se llama distinto a $id_hex. 
            dd("Sigo sin encontrarlo. Tu archivo se llama: {$event->song}. Busqué en la carpeta: {$id_hex}");
        }

        // 3. Enviamos el archivo con las cabeceras correctas para que el celular
        // pueda reproducirlo y adelantar/atrasar sin problema.
        return response()->file($finalPath, [
            'Content-Type' => 'audio/mpeg',
            'Accept-Ranges' => 'bytes'
        ]);
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
}
