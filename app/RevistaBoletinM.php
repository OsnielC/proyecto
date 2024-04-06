<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RevistaBoletinM extends Model
{
    protected $table = 'revista_boletin';
	public $timestamps = false;

	protected $fillable =[
		'fk_doc',
        'num_revista',
        'nombre_revista',
        'pag',
        'volumen',
        'anio',
        'isbn',
        'issn'
	]; 
}
