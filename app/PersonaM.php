<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaM extends Model
{
    protected $table = 'persona';
	protected $primaryKey = "id_persona";
	public $timestamps=false;

	protected $fillable =[
		'nombre',
		'apellidos',
		'cargo'
	];
}
