<?php

namespace App\Http\Controllers;

use App\Models\CameraAnimation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CameraAnimationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->user()->can_manage_animations) {
            abort(403, 'No tienes permisos para administrar animaciones.');
        }

        $animations = CameraAnimation::all();
        return view('camera_animations.index', compact('animations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!auth()->user()->can_manage_animations) {
            abort(403, 'No tienes permisos para administrar animaciones.');
        }

        return view('camera_animations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can_manage_animations) {
            abort(403, 'No tienes permisos para administrar animaciones.');
        }

        if ($request->hasFile('mp4_file')) {
            $file = $request->file('mp4_file');
            if (!$file->isValid()) {
                dd('ERROR DE SUBIDA DE PHP:', $file->getErrorMessage(), 'Código de error:', $file->getError());
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'mp4_file' => 'required|file|mimes:mp4,mov,qt,webm|max:500000',
        ], [
            'mp4_file.required' => 'Debes seleccionar un archivo de video.',
            'mp4_file.file' => 'Hubo un error al recibir el archivo. Puede que sea demasiado pesado.',
            'mp4_file.mimes' => 'El archivo debe ser un video en formato válido (MP4).',
            'mp4_file.max' => 'El archivo no debe pesar más de 500MB.',
        ]);

        $mp4Name = 'anim_' . time() . '_' . uniqid() . '.' . $request->file('mp4_file')->getClientOriginalExtension();

        $request->file('mp4_file')->storeAs('animations', $mp4Name, 'local');

        CameraAnimation::create([
            'title' => $request->title,
            'mp4_file' => 'animations/' . $mp4Name,
        ]);

        return redirect()->route('camera-animations.index')->with('success', 'Animación subida exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CameraAnimation $cameraAnimation)
    {
        if (!auth()->user()->can_manage_animations) {
            abort(403, 'No tienes permisos para administrar animaciones.');
        }

        return view('camera_animations.edit', compact('cameraAnimation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CameraAnimation $cameraAnimation)
    {
        if (!auth()->user()->can_manage_animations) {
            abort(403, 'No tienes permisos para administrar animaciones.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'mp4_file' => 'nullable|file|mimes:mp4,mov,qt,webm|max:500000',
        ], [
            'mp4_file.file' => 'Hubo un error al recibir el archivo. Puede que sea demasiado pesado.',
            'mp4_file.mimes' => 'El archivo debe ser un video en formato válido (MP4).',
            'mp4_file.max' => 'El archivo no debe pesar más de 500MB.',
        ]);

        $data = ['title' => $request->title];

        if ($request->hasFile('mp4_file')) {
            if ($cameraAnimation->mp4_file && Storage::disk('local')->exists($cameraAnimation->mp4_file)) {
                Storage::disk('local')->delete($cameraAnimation->mp4_file);
            }
            $mp4Name = 'anim_' . time() . '_' . uniqid() . '.' . $request->file('mp4_file')->getClientOriginalExtension();
            $request->file('mp4_file')->storeAs('animations', $mp4Name, 'local');
            $data['mp4_file'] = 'animations/' . $mp4Name;
        }

        $cameraAnimation->update($data);

        return redirect()->route('camera-animations.index')->with('success', 'Animación actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CameraAnimation $cameraAnimation)
    {
        if (!auth()->user()->can_manage_animations) {
            abort(403, 'No tienes permisos para administrar animaciones.');
        }

        if ($cameraAnimation->mp4_file && Storage::disk('local')->exists($cameraAnimation->mp4_file)) {
            Storage::disk('local')->delete($cameraAnimation->mp4_file);
        }

        $cameraAnimation->delete();

        return redirect()->route('camera-animations.index')->with('success', 'Animación eliminada exitosamente.');
    }

    public function stream(CameraAnimation $cameraAnimation, $type = 'mp4')
    {
        $file = $cameraAnimation->mp4_file;

        if (!$file || !Storage::disk('local')->exists($file)) {
            abort(404);
        }

        $path = Storage::disk('local')->path($file);
        $mime = mime_content_type($path);

        return response()->file($path, [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes'
        ]);
    }
}
