<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FechaExtraM extends Model
{
    protected $table = 'fecha_extra';
	public $timestamps = false;

	protected $fillable =[
		'fk_doc',
        'mes',
        'mes2',
		'anio'
	]; 
}
