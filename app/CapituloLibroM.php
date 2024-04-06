<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapituloLibroM extends Model
{
    protected $table='capitulo_libro';
    public $timestamps=false;

    protected $fillable =[
        'fk_doc',
        'nombre_libro',
        'autorgral',
        'edicion',
        'tomos',
        'volumen',
        'coleccion',
        'nocol',
        'serie',
        'noserie',
        'traductor',
        'paginas',
        'isbn',
        'issn'
    ]; 
}
