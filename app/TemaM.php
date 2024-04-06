<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemaM extends Model
{
    protected $table = 'tema';
	protected $primaryKey = "id_tema";
	public $timestamps = false;
	
	protected $fillable =[
		'descripcion'
	];
}
