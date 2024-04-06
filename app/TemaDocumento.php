<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemaDocumento extends Model
{
    protected $table = 'cntrl_tema';
  	public $timestamps = false;

	protected $fillable =[
        'fk_doc',
        'fk_tema'
	]; 
}
