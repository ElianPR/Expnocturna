<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class TermsController extends Controller
{
    public function index($id_hex = null)
    {
        $themes = [
            1 => [
                'sidebar' => '#436c00',
                'title' => '#305820',
            ],

            2 => [
                'sidebar' => '#092d51',
                'title' => '#092d51',
            ],

            3 => [
                'sidebar' => '#a8792b',
                'title' => '#a8792b',
            ],
        ];

        $event = null;

        if ($id_hex) {
            $event = Event::whereRaw('HEX(id) = ?', [strtoupper($id_hex)])
                ->first();
        }

        $template = $event?->template ?? 2;

        $theme = $themes[$template] ?? $themes[2];

        return view('terms', compact('theme'));
    }
}
