<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EoloelectricasController extends Controller
{
    public function index(){
        return ('index');
    }
    public function tema(){
        return view('eoloelectricas.tema');
    }

    public function lugar(){
        return view('eoloelectricas.lugar');
    }

    public function tipo(){
        return view('eoloelectricas.tipo');
    }

    public function fecha(){
        return view('eoloelectricas.fecha');
    }

    public function institucion(){
        return view('eoloelectricas.institucion');
    }

    public function actor(){
        return view('eoloelectricas.actor');
    }

    public function autor(){
        return view('eoloelectricas.autor');
    }

    public function documento(){
        return view('eoloelectricas.documento');
    }

    public function titulo(){
        return view('eoloelectricas.titulo');
    }
}
