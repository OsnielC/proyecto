<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PonenciaM extends Model
{
    protected $table = 'ponencia';
	public $timestamps = false;

	protected $fillable =[
		'fk_doc',
        'evento',
        'lugar_presentacion',
        'fecha_pesentacion',
        'paginas'
	];
}
