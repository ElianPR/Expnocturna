<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventShareController extends Controller
{
    protected function findEvent(string $id_evento): Event
    {
        if (! ctype_xdigit($id_evento) || strlen($id_evento) !== 32) {
            abort(404);
        }

        return Event::where('id', hex2bin($id_evento))->firstOrFail();
    }

    public function create(string $id_evento)
    {
        $event = $this->findEvent($id_evento);

        return view('events.share', compact('event'));
    }

    public function store(Request $request, string $id_evento)
    {
        $event = $this->findEvent($id_evento);

        $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,webp,heic,heif,mp4,mov,avi,mkv,webm',
                'max:25600',
            ],
        ], [
            'files.required' => 'Debes seleccionar al menos un archivo.',
            'files.array' => 'El formato de archivos no es válido.',
            'files.min' => 'Debes seleccionar al menos un archivo.',
            'files.*.required' => 'Uno de los archivos no es válido.',
            'files.*.file' => 'Uno de los elementos seleccionados no es un archivo válido.',
            'files.*.mimes' => 'Solo se permiten imágenes o videos válidos.',
            'files.*.max' => 'Cada archivo debe ser menor o igual a 25 MB.',
        ]);

        $eventFolder = 'events/' . $event->album_hex;

        foreach ($request->file('files') as $file) {

            $extension = strtolower($file->getClientOriginalExtension());
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            $safeOriginalName = str($originalName)
                ->ascii()
                ->slug('_')
                ->limit(60, '')
                ->toString();

            $filename = now()->format('Ymd_His') . '_' . uniqid() . '_' . $safeOriginalName . '.' . $extension;

            Storage::disk('s3')->putFileAs(
                $eventFolder,
                $file,
                $filename
            );
        }
        return back()->with('status', 'Archivos subidos correctamente.');
    }
}