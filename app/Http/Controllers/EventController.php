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

    public function dashboard()
    {
        // 1. Ejecutamos la limpieza
        $this->limpiarPapeleraCaducada();

        // 2. Traemos los eventos. 
        // OJO: Cambié Event::all() por un filtro del usuario logueado. 
        // Si usas all(), un usuario podría ver los eventos de otros usuarios.
        $events = Event::where('id_user', Auth::id())->get();

        return view('dashboard', compact('events'));
    }

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

    public function edit($id)
    {
        if (!ctype_xdigit($id) || strlen($id) !== 32) {
            abort(404);
        }

        $event = Event::where('id', hex2bin($id))->firstOrFail();

        $photo = DB::table('photos')
            ->where('id_event', $event->id)
            ->first();

        return view('events.edit', compact('event', 'photo'));
    }

    public function update(Request $request, $id_hex)
    {
        if (!ctype_xdigit($id_hex) || strlen($id_hex) !== 32) {
            abort(404);
        }

        $event = Event::where('id', hex2bin($id_hex))->firstOrFail();

        $validated = $request->validate([
            'name'       => 'required|max:80',
            'monogram'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'typography' => 'nullable|max:40',
            'template'   => 'nullable|integer',
            'date'       => 'required|date',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'song'       => 'nullable|file|mimes:mp3,wav,mp4,mov,webm|max:100200',
            'song_cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'watermark'  => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        try {
            $folderName = $id_hex;

            $eventData = [
                'name'       => $validated['name'],
                'typography' => $validated['typography'] ?? null,
                'template'   => $request->input('template', 0),
                'date'       => $validated['date'],
            ];

            // --- MONOGRAMA ---
            // Si el usuario marcó "quitar monograma", eliminamos el archivo y ponemos null
            if ($request->boolean('remove_monogram')) {
                if ($event->monogram) {
                    Storage::disk('local')->delete($folderName . '/' . $event->monogram);
                }
                $eventData['monogram'] = null;
            } elseif ($request->hasFile('monogram')) {
                // Si subió uno nuevo, reemplazamos el anterior
                if ($event->monogram) {
                    Storage::disk('local')->delete($folderName . '/' . $event->monogram);
                }
                $file = $request->file('monogram');
                $name = substr($file->getClientOriginalName(), -50);
                $file->storeAs($folderName, $name, 'local');
                $eventData['monogram'] = $name;
            }

            // --- CANCIÓN / VIDEO ---
            if ($request->hasFile('song')) {
                $file = $request->file('song');
                $mime = $file->getMimeType();
                $isAudio = str_starts_with($mime, 'audio');
                $isVideo = str_starts_with($mime, 'video');

                if ($event->song) {
                    Storage::disk('local')->delete($folderName . '/' . $event->song);
                }

                if ($isVideo && $event->song_cover) {
                    Storage::disk('local')->delete($folderName . '/' . $event->song_cover);
                    $eventData['song_cover'] = null;
                }

                $name = substr($file->getClientOriginalName(), -50);
                $file->storeAs($folderName, $name, 'local');
                $eventData['song'] = $name;
            }

            // --- PORTADA DE CANCIÓN ---
            if ($request->hasFile('song_cover')) {

                // Detectar si el evento actual es audio o video
                $currentIsVideo = $event->song && preg_match('/\.(mp4|mov|webm)$/i', $event->song);

                // Solo permitir portada si NO es video
                if (!$currentIsVideo) {

                    // Eliminar portada anterior si existe
                    if ($event->song_cover) {
                        Storage::disk('local')->delete($folderName . '/' . $event->song_cover);
                    }

                    $file = $request->file('song_cover');
                    $name = substr($file->getClientOriginalName(), -50);
                    $file->storeAs($folderName, $name, 'local');

                    $eventData['song_cover'] = $name;
                }
            }

            // --- MARCA DE AGUA ---
            if ($request->hasFile('watermark')) {
                $file = $request->file('watermark');
                $name = substr($file->getClientOriginalName(), -50);
                $file->storeAs($folderName, $name, 'local');
                $eventData['watermark'] = $name;
            }

            $event->update($eventData);

            // --- FOTO PRINCIPAL ---
            if ($request->hasFile('main_image')) {
                $file = $request->file('main_image');
                $name = substr($file->getClientOriginalName(), -50);
                $file->storeAs($folderName, $name, 'local');

                DB::table('photos')->updateOrInsert(
                    ['id_event' => $event->id],
                    [
                        'id'  => Str::uuid()->getBytes(),
                        'url' => $name,
                    ]
                );
            }

            return redirect()
                ->route('events.edit', $id_hex)
                ->with('swal_success', 'Evento actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->with('swal_error', 'Error al actualizar el evento.');
        }
    }

    /**
     * Manda el evento a la papelera (Soft Delete)
     */
    public function destroy($id_hex)
    {
        try {
            $id = hex2bin($id_hex);
            $event = Event::findOrFail($id);

            // 1. Apagamos los enlaces como pediste
            $event->is_active = false;
            $event->album_active = false; // Suponiendo que así se llama tu campo de álbum
            $event->save();

            // 2. Lo mandamos a la papelera (esto llena el campo deleted_at automáticamente)
            $event->delete();

            return redirect()->route('dashboard')->with('success', 'Evento enviado a la papelera.');
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->with('swal_error', 'Error al enviar a la papelera.');
        }
    }

    /**
     * Muestra los eventos en la papelera
     */
    public function trash()
    {
        $this->limpiarPapeleraCaducada();
        // onlyTrashed() trae SOLAMENTE los que tienen fecha en deleted_at
        $events = Event::onlyTrashed()->get();
        return view('events.trash', compact('events'));
    }

    /**
     * Restaura un evento de la papelera
     */
    public function restore($id_hex)
    {
        try {
            $id = hex2bin($id_hex);
            // withTrashed() es necesario para encontrarlo, porque está oculto
            $event = Event::withTrashed()->findOrFail($id);
            
            // Esto vuelve a poner deleted_at en NULL
            $event->restore();

            return redirect()->route('events.trash')->with('success', 'Evento restaurado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('events.trash')->with('swal_error', 'Error al restaurar.');
        }
    }

    /**
     * Elimina el evento permanentemente de la base de datos y del servidor
     */
    public function forceDestroy($id_hex)
    {
        try {
            $id = hex2bin($id_hex);
            $event = Event::withTrashed()->findOrFail($id);

            $eventFolder = $id_hex; 
            $albumFolder = 'events/' . bin2hex($event->album); 

            DB::table('photos')->where('id_event', $id)->delete();

            if (Storage::disk('local')->exists($eventFolder)) {
                Storage::disk('local')->deleteDirectory($eventFolder);
            }
            if (Storage::disk('local')->exists($albumFolder)) {
                Storage::disk('local')->deleteDirectory($albumFolder);
            }

            // forceDelete() lo borra permanentemente de la tabla
            $event->forceDelete();

            return redirect()->route('events.trash')->with('success', 'Evento destruido permanentemente.');
        } catch (\Exception $e) {
            return redirect()->route('events.trash')->with('swal_error', 'Error al destruir el evento.');
        }
    }

    private function limpiarPapeleraCaducada()
    {
        // Traemos los eventos ocultos cuya fecha de eliminación fue hace 60 días o más
        $oldEvents = Event::onlyTrashed()->where('deleted_at', '<=', now()->subDays(60))->get();

        foreach ($oldEvents as $event) {
            /** @var \App\Models\Event $event */
            $eventFolder = bin2hex($event->id); 
            $albumFolder = 'events/' . bin2hex($event->album); 

            // Borramos fotos de la BD
            DB::table('photos')->where('id_event', $event->id)->delete();
            
            // Borramos carpetas físicas
            if (Storage::disk('local')->exists($eventFolder)) {
                Storage::disk('local')->deleteDirectory($eventFolder);
            }
            if (Storage::disk('local')->exists($albumFolder)) {
                Storage::disk('local')->deleteDirectory($albumFolder);
            }
            
            // Destrucción total
            $event->forceDelete();
        }
    }
}
