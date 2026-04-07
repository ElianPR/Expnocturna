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
            'name'       => 'nullable|required_without:monogram|max:80',
            'monogram'   => 'nullable|required_without:name|max:40',
            'typography' => 'nullable|max:40',
            'template'   => 'nullable|integer',
            'date'       => 'required|date',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'song'       => 'nullable|file|mimes:mp3,wav|max:10240',
            'watermark'  => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

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
            'monogram'   => $validated['monogram'] ?? null,
            'typography' => $validated['typography'] ?? null,
            'template'   => $request->input('template', 0),
            'date'       => $validated['date'],
        ];

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
        Event::create($eventData);

        // 2. GUARDAMOS LA FOTO PRINCIPAL EN LA TABLA 'photos'
        if ($request->hasFile('main_image')) {
            $mainImageFile = $request->file('main_image');
            
            // Aseguramos que el nombre no pase de 50 caracteres (por tu columna varchar 50)
            $mainImageName = substr($mainImageFile->getClientOriginalName(), -50); 
            
            // Físicamente la guardamos en la carpeta local del evento
            $mainImageFile->storeAs($folderName, $mainImageName, 'local');
            
            // La registramos en la base de datos
            DB::table('photos')->insert([
                'id'       => Str::uuid()->getBytes(), // Nuevo UUID para la foto (varbinary 16)
                'url'      => $mainImageName,          // Nombre del archivo (varchar 50)
                'id_event' => $uuid->getBytes(),       // ID del evento al que pertenece (varbinary 16)
            ]);
        }

        // Generamos las URLs para la sesión
        $baseUrl = rtrim(config('app.url'), '/'); 
        $urlEvento = $baseUrl . "/event/{$folderName}";
        $urlAlbum  = $baseUrl . "/album/{$albumHex}";

        return redirect()->back()->with([
            'success'    => 'Evento y archivos guardados correctamente.',
            'url_evento' => $urlEvento,
            'url_album'  => $urlAlbum
        ]);
    }

    public function show($folder)
    {
        $eventId = hex2bin($folder);
        $event = Event::where('id', $eventId)->firstOrFail();

        // Buscamos la foto ligada al evento
        $photo = DB::table('photos')->where('id_event', $eventId)->first();

        // Generamos la URL usando el nombre de la ruta que definiste
        $imageUrl = $photo 
            ? route('file.show', ['id_evento' => $folder, 'filename' => $photo->url]) 
            : asset('images/fondo-papel.jpg');

        return view('events.show', compact('event', 'imageUrl'));
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
}
