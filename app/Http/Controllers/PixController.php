<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PixController extends Controller
{
    public function show()
    {
        $titulo = config('pix.titulo');
        $subtitulo = config('pix.subtitulo');
        $pixCopiaECola = config('pix.copia_e_cola');

        return view('pix.show', compact('titulo', 'subtitulo', 'pixCopiaECola'));
    }
}
