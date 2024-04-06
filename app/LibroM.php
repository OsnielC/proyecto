<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LibroM extends Model
{
    protected $table = 'libro';
	public $timestamps = false;

	protected $fillable =[
		'fk_doc',
        'edicion',
        'traductor',
        'prologo',
        'introduccion',
        'tomos',
        'volumen',
        'coleccion',
        'nocol',
        'serie',
        'noserie',
        'paginalib',
        'isbn',
        'issn'
	]; 
}
