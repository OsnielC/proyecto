<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FechaNormalM extends Model
{
    protected $table = 'fecha';
	public $timestamps = false;

	protected $fillable =[
		'fk_doc',
		'fecha'
	]; 
}
