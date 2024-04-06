<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstitucionM extends Model
{
    protected $table = 'institucion';
	protected $primaryKey = "id_institucion";
	public $timestamps = false;

	protected $fillable =[
		'nombre',
		'siglas',
		'pais',
		'sector',
		'revisado'
	];
}
