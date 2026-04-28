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

        if (!$event->album_active) {
            return view('events.thank-you', ['event' => $event, 'type' => 'album']);
        }

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
        $path = "events/$id_album/$filename";

        if (!Storage::disk('local')->exists($path)) {
            abort(404);
        }

        return response()->file(Storage::disk('local')->path($path));
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

    public function moveToTrash(Request $request, string $id_album)
    {
        $files = $request->input('files', []);

        foreach ($files as $url) {

            $filename = basename(parse_url($url, PHP_URL_PATH));

            $origin =
                "events/$id_album/$filename";

            $trash =
                "events/$id_album/trash/$filename";

            if (Storage::disk('local')->exists($origin)) {

                Storage::disk('local')->makeDirectory(
                    "events/$id_album/trash"
                );

                Storage::disk('local')->move(
                    $origin,
                    $trash
                );
            }
        }

        return response()->json([
            'message' => 'Movidos a papelera'
        ]);
    }

    public function trash(string $id_album)
    {
        $event = Event::where(
            'album',
            hex2bin($id_album)
        )->firstOrFail();

        $files = Storage::disk('local')
            ->files("events/$id_album/trash");

        $media = [];

        foreach ($files as $file) {

            $ext = strtolower(
                pathinfo($file, PATHINFO_EXTENSION)
            );

            $media[] = [
                'url' => route(
                    'album.file',
                    [
                        'id_album' => $id_album,
                        'filename' => 'trash/' . basename($file)
                    ]
                ),
                'is_video' => in_array(
                    $ext,
                    ['mp4', 'mov', 'avi', 'mkv', 'webm']
                )
            ];
        }

        return view(
            'events.album-trash-admin',
            compact('event', 'media')
        );
    }

    public function restoreMedia(Request $request, string $id_album) {

        $files = $request->input(
            'files',
            []
        );

        foreach ($files as $url) {

            $filename = basename(
                parse_url(
                    $url,
                    PHP_URL_PATH
                )
            );

            $origin =
                "events/$id_album/trash/$filename";

            $dest =
                "events/$id_album/$filename";

            if (
                Storage::disk('local')
                ->exists($origin)
            ) {
                Storage::disk('local')
                    ->move(
                        $origin,
                        $dest
                    );
            }
        }

        return response()->json([
            'message' => 'Restauradas'
        ]);
    }

    public function forceDeleteMedia(Request $request, string $id_album)
    {

        $files = $request->input(
            'files',
            []
        );

        foreach ($files as $url) {

            $filename = basename(
                parse_url(
                    $url,
                    PHP_URL_PATH
                )
            );

            Storage::disk('local')
                ->delete(
                    "events/$id_album/trash/$filename"
                );
        }

        return response()->json([
            'message' =>
            'Eliminadas definitivamente'
        ]);
    }
}
