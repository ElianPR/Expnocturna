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
            return response()->json([
                'success' => false,
                'message' => 'Debes seleccionar al menos un archivo.'
            ], 422);
        }

        $allowedExtensions = [
            'jpg', 'jpeg', 'png', 'webp', 'heic', 'heif',
            'mp4', 'mov', 'avi', 'mkv', 'webm'
        ];

        $eventFolder = 'events/' . bin2hex($event->album);

        $uploaded = 0;
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                if (!$file->isValid()) {
                    $errors[] = $file->getClientOriginalName() . ' está corrupto.';
                    continue;
                }

                $extension = strtolower($file->getClientOriginalExtension());

                if (!in_array($extension, $allowedExtensions)) {
                    $errors[] = $file->getClientOriginalName() . ' no es compatible.';
                    continue;
                }

                if ($file->getSize() > 500 * 1024 * 1024) {
                    $errors[] = $file->getClientOriginalName() . ' supera 500MB.';
                    continue;
                }

                // Generamos un nombre base seguro
                $baseFilename = now()->format('Ymd_His') . '_' . bin2hex(random_bytes(6));
                $finalFilename = $baseFilename . '.' . $extension;
                
                // Guardamos el archivo directamente (El frontend ya se encargó de que sea un MP4 perfecto)
                $file->storeAs($eventFolder, $finalFilename, 'local');

                $uploaded++;
            } catch (\Throwable $e) {
                report($e);
                $errors[] = $file->getClientOriginalName() . ' falló al subir.';
            }
        }

        return response()->json([
            'success' => true,
            'uploaded' => $uploaded,
            'errors' => $errors
        ]);
    }

    public function showAlbum(string $id_album)
    {
        if (! ctype_xdigit($id_album) || strlen($id_album) !== 32) {
            abort(404);
        }

        $event = Event::where('album', hex2bin($id_album))->firstOrFail();

        if (!$event->album_active) {
            $expirationDate = $event->album_expiration ?? $event->date;
            if ($event->template == 1) {
                if (\Carbon\Carbon::parse($expirationDate)->isFuture() || \Carbon\Carbon::parse($event->album_availability ?? $event->date)->isFuture()) {
                    return view('events.inactive-1', ['event' => $event, 'type' => 'album']);
                }
                return view('events.album-expired-1', ['event' => $event, 'type' => 'album']);
            } elseif ($event->template == 2) {
                if (\Carbon\Carbon::parse($expirationDate)->isFuture() || \Carbon\Carbon::parse($event->album_availability ?? $event->date)->isFuture()) {
                    return view('events.inactive-2', ['event' => $event, 'type' => 'album']);
                }
                return view('events.album-expired-2', ['event' => $event, 'type' => 'album']);
            } elseif ($event->template == 3) {
                if (\Carbon\Carbon::parse($expirationDate)->isFuture() || \Carbon\Carbon::parse($event->album_availability ?? $event->date)->isFuture()) {
                    return view('events.inactive-3', ['event' => $event, 'type' => 'album']);
                }
                return view('events.album-expired-3', ['event' => $event, 'type' => 'album']);
            }
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
        if (!auth()->user()->can_manage_events) {
            abort(403, 'No tienes permisos para gestionar eventos.');
        }

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
        if (!auth()->user()->can_manage_events) {
            abort(403, 'No tienes permisos para gestionar eventos.');
        }

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

                touch(Storage::disk('local')->path($trash));
            }
        }

        return response()->json([
            'message' => 'Movidos a papelera'
        ]);
    }

    public function trash(string $id_album)
    {
        if (!auth()->user()->can_access_trash) {
            abort(403, 'No tienes permisos para acceder a la papelera.');
        }

        $this->cleanExpiredTrashMedia($id_album);

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

    public function restoreMedia(Request $request, string $id_album)
    {
        if (!auth()->user()->can_access_trash) {
            abort(403, 'No tienes permisos para acceder a la papelera.');
        }

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

                touch(Storage::disk('local')->path($dest));
            }
        }

        return response()->json([
            'message' => 'Restauradas'
        ]);
    }

    public function forceDeleteMedia(Request $request, string $id_album)
    {
        if (!auth()->user()->can_access_trash) {
            abort(403, 'No tienes permisos para acceder a la papelera.');
        }

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

    private function cleanExpiredTrashMedia(string $id_album)
    {
        $trashDir = "events/$id_album/trash";
        if (Storage::disk('local')->exists($trashDir)) {
            $files = Storage::disk('local')->files($trashDir);
            foreach ($files as $file) {
                $lastModified = Storage::disk('local')->lastModified($file);
                // 30 días en segundos = 30 * 24 * 60 * 60 = 2592000
                if (now()->timestamp - $lastModified >= 2592000) {
                    Storage::disk('local')->delete($file);
                }
            }
        }
    }
}
