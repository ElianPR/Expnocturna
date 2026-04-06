<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function create()
    {
        return view('events.create');
    }

    public function store (Request $request)
    {
        $validated = $request->validate([
            'name'       => 'nullable|required_without:monogram|max:80',
            'monogram'   => 'nullable|required_without:name|max:40',
            'typography' => 'nullable|max:40',
            'template'   => 'nullable|integer',
            'date'       => 'required|date',
            'song'       => 'nullable|file|mimes:mp3,wav|max:10240',
            'watermark'  => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $uuid = Str::uuid();
        $folderName = str_replace('-', '', $uuid->toString());
        $albumUuid = Str::uuid();
        $albumHex = str_replace('-', '', $albumUuid->toString());

        $eventData = $validated;
        $eventData['id'] = $uuid->getBytes(); 
        $eventData['album'] = hex2bin($albumHex);
        $eventData['id_user'] = Auth::id();
        $eventData['template'] = $request->input('template', 0);

        if ($request->hasFile('song')) {
            $songFile = $request->file('song');
            $songName = substr($songFile->getClientOriginalName(), -50); 
            
            // 🔥 CAMBIO AQUÍ: 'public' a 'local'
            $songFile->storeAs($folderName, $songName, 'local');
            
            $eventData['song'] = $songName;
        }

        if ($request->hasFile('watermark')) {
            $watermarkFile = $request->file('watermark');
            $watermarkName = substr($watermarkFile->getClientOriginalName(), -50);
            
            // 🔥 CAMBIO AQUÍ: 'public' a 'local'
            $watermarkFile->storeAs($folderName, $watermarkName, 'local');
            
            $eventData['watermark'] = $watermarkName;
        }

        Event::create($eventData);

        $baseUrl = rtrim(config('app.url'), '/'); 

        $urlEvento = $baseUrl . "/event/{$folderName}";
        $urlAlbum  = $baseUrl . "/album/{$albumHex}";

        return redirect()->back()->with([
            'success'    => 'Evento y archivos guardados correctamente.',
            'url_evento' => $urlEvento,
            'url_album'  => $urlAlbum
        ]);
    }
}