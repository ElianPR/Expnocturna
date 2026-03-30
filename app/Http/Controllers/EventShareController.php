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

        if (!$request->hasFile('files')) {
            return back()->withErrors(['Debes seleccionar al menos un archivo.']);
        }

        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/webp',
            'image/heic', 'image/heif',
            'video/mp4', 'video/quicktime', 'video/x-msvideo',
            'video/x-matroska', 'video/webm'
        ];

        $eventFolder = 'events/' . $event->album_hex;

        $uploaded = 0;
        $errors = [];

        foreach ($request->file('files') as $file) {

            if (!$file->isValid()) {
                $errors[] = $file->getClientOriginalName() . ' está corrupto.';
                continue;
            }

            // 🔥 VALIDACIÓN MANUAL
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                $errors[] = $file->getClientOriginalName() . ' no es compatible.';
                continue;
            }

            if ($file->getSize() > 25 * 1024 * 1024) {
                $errors[] = $file->getClientOriginalName() . ' supera 25MB.';
                continue;
            }

            try {
                $extension = strtolower($file->getClientOriginalExtension());
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $safeOriginalName = str($originalName)
                    ->ascii()
                    ->slug('_')
                    ->limit(60, '')
                    ->toString();

                $filename = now()->format('Ymd_His') . '' . uniqid() . '' . $safeOriginalName . '.' . $extension;

                Storage::disk('s3')->putFileAs(
                    $eventFolder,
                    $file,
                    $filename
                );

                $uploaded++;

            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ' falló al subir.';
            }
        }

        return back()->with([
            'status' => "$uploaded archivo(s) subido(s).",
            'upload_errors' => $errors
        ]);
    }
}