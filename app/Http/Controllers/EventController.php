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

    public function store(Request $request)
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

        try {
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
                $songFile->storeAs($folderName, $songName, 'local');
                $eventData['song'] = $songName;
            }

            if ($request->hasFile('watermark')) {
                $watermarkFile = $request->file('watermark');
                $watermarkName = substr($watermarkFile->getClientOriginalName(), -50);
                $watermarkFile->storeAs($folderName, $watermarkName, 'local');
                $eventData['watermark'] = $watermarkName;
            }

            $event = Event::create($eventData);

            return redirect()->route('events.qr', $event->id_hex);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('swal_error', 'Ocurrió un error al crear el evento. Por favor intenta de nuevo.');
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
}
