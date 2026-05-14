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

        $request->validate([
            'title' => 'required|string|max:255',
            'mov_file' => 'required|file|mimes:mov,qt,mp4|max:50000', // mimes for video
            'webm_file' => 'required|file|mimes:webm|max:50000',
        ]);

        $movName = 'anim_' . time() . '_' . uniqid() . '.' . $request->file('mov_file')->getClientOriginalExtension();
        $webmName = 'anim_' . time() . '_' . uniqid() . '.' . $request->file('webm_file')->getClientOriginalExtension();

        $request->file('mov_file')->storeAs('animations', $movName, 'local');
        $request->file('webm_file')->storeAs('animations', $webmName, 'local');

        CameraAnimation::create([
            'title' => $request->title,
            'mov_file' => 'animations/' . $movName,
            'webm_file' => 'animations/' . $webmName,
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
            'mov_file' => 'nullable|file|mimes:mov,qt,mp4|max:50000',
            'webm_file' => 'nullable|file|mimes:webm|max:50000',
        ]);

        $data = ['title' => $request->title];

        if ($request->hasFile('mov_file')) {
            if ($cameraAnimation->mov_file && Storage::disk('local')->exists($cameraAnimation->mov_file)) {
                Storage::disk('local')->delete($cameraAnimation->mov_file);
            }
            $movName = 'anim_' . time() . '_' . uniqid() . '.' . $request->file('mov_file')->getClientOriginalExtension();
            $request->file('mov_file')->storeAs('animations', $movName, 'local');
            $data['mov_file'] = 'animations/' . $movName;
        }

        if ($request->hasFile('webm_file')) {
            if ($cameraAnimation->webm_file && Storage::disk('local')->exists($cameraAnimation->webm_file)) {
                Storage::disk('local')->delete($cameraAnimation->webm_file);
            }
            $webmName = 'anim_' . time() . '_' . uniqid() . '.' . $request->file('webm_file')->getClientOriginalExtension();
            $request->file('webm_file')->storeAs('animations', $webmName, 'local');
            $data['webm_file'] = 'animations/' . $webmName;
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

        if ($cameraAnimation->mov_file && Storage::disk('local')->exists($cameraAnimation->mov_file)) {
            Storage::disk('local')->delete($cameraAnimation->mov_file);
        }

        if ($cameraAnimation->webm_file && Storage::disk('local')->exists($cameraAnimation->webm_file)) {
            Storage::disk('local')->delete($cameraAnimation->webm_file);
        }

        $cameraAnimation->delete();

        return redirect()->route('camera-animations.index')->with('success', 'Animación eliminada exitosamente.');
    }

    public function stream(CameraAnimation $cameraAnimation, $type)
    {
        $file = $type === 'mov' ? $cameraAnimation->mov_file : $cameraAnimation->webm_file;

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
