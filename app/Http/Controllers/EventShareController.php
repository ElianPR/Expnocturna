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
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/heic',
            'image/heif',
            'video/mp4',
            'video/quicktime',
            'video/x-msvideo',
            'video/x-matroska',
            'video/webm'
        ];

        $eventFolder = 'events/' . bin2hex($event->album);

        $uploaded = 0;
        $errors = [];

        foreach ($request->file('files') as $file) {

            if (!$file->isValid()) {
                $errors[] = $file->getClientOriginalName() . ' está corrupto.';
                continue;
            }

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

                $filename = now()->format('Ymd_His') . '_' . uniqid() . '_' . $safeOriginalName . '.' . $extension;

                Storage::disk('local')->putFileAs(
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

    public function showAlbum(string $id_album)
    {
        if (! ctype_xdigit($id_album) || strlen($id_album) !== 32) {
            abort(404);
        }

        $event = Event::where('album', hex2bin($id_album))->firstOrFail();

        $eventFolder = 'events/' . $id_album;
        $files = Storage::disk('local')->files($eventFolder);

        $media = [];
        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $isVideo = in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm']);

            $url = route('album.file', [
                'id_album' => $id_album,
                'filename' => basename($file)
            ]);

            $media[] = [
                'url' => $url,
                'is_video' => $isVideo,
            ];
        }
        return view('events.album', compact('event', 'media'));
    }

    public function serveFile(string $id_album, string $filename)
    {
        $path = 'events/' . $id_album . '/' . $filename;

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        $rutaFisica = Storage::disk('local')->path($path);

        return response()->file($rutaFisica);
    }

    public function adminAlbum(string $id_album)
    {
        if (!ctype_xdigit($id_album) || strlen($id_album) !== 32) {
            abort(404);
        }

        $event = Event::where('album', hex2bin($id_album))->firstOrFail();

        $eventFolder = 'events/' . $id_album;
        $files = Storage::disk('local')->files($eventFolder);

        $media = [];
        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $isVideo = in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm']);

            $url = route('album.file', [
                'id_album' => $id_album,
                'filename' => basename($file)
            ]);

            $media[] = [
                'url' => $url,
                'is_video' => $isVideo,
            ];
        }

        return view('events.album-admin', compact('event', 'media'));
    }

    public function deleteMedia(Request $request, string $id_album)
    {
        if (!ctype_xdigit($id_album) || strlen($id_album) !== 32) {
            abort(404);
        }

        $files = $request->input('files', []);

        if (empty($files)) {
            return response()->json(['error' => 'No hay archivos seleccionados'], 422);
        }

        $deleted = 0;

        foreach ($files as $url) {
            $filename = basename(parse_url($url, PHP_URL_PATH));
            $path = 'events/' . $id_album . '/' . $filename;

            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
                $deleted++;
            }
        }

        return response()->json([
            'message' => "$deleted archivo(s) eliminado(s)"
        ]);
    }
}
