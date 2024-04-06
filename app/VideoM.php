<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideoM extends Model
{
    protected $table = 'video';
    public $timestamps = false;

    protected $fillable = [
        'fk_doc',
        'secundario',
        'director',
        'productor',
        'realizador',
        'conductor',
        'reportero',
        'guionista',
        'fotografia',
        'musica',
        'actores',
        'narrador',
        'fecha_trans',
        'hora_trans',
        'idioma',
        'subtitulo',
        'formato',
        'duracion',
        'programa',
        'canal'
    ];
}
