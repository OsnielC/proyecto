<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InstitucionDocumento extends Model
{
    protected $table = 'cntrl_instit';
  	public $timestamps = false;

	protected $fillable =[
        'fk_doc',
        'fk_instit'
	];
}
