<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TesisM extends Model
{
    protected $table = 'tesis';
	public $timestamps = false;

	protected $fillable =[
		'fk_doc',
        'grado',
        'asesor',
        'num_paginas'
	]; 
}
