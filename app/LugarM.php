<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LugarM extends Model
{
    protected $table = 'lugar';
	protected $primaryKey = "id_lugar";
	public $timestamps = false;

	protected $fillable =[
		'ubicacion',
		'pais',
		'region_geografica',
		'latitud',
		'longitud'
	];
}
