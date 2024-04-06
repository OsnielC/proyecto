<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutorDocumento extends Model
{
    protected $table = 'cntrl_autor';
	protected $primaryKey = 'orden';
	public $timestamps = false;

	protected $fillable =[
        'orden',
        'fk_doc',
        'fk_autor',
        'extra'
	];
}
