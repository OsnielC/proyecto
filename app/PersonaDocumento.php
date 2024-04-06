<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonaDocumento extends Model
{
    protected $table = 'cntrl_persona';
  	public $timestamps = false;

	protected $fillable =[
        'fk_doc',
        'fk_persona',
        'sector'
	]; 
}
