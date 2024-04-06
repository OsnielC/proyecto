<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LugarDocumento extends Model
{
    protected $table = 'cntrl_lugar';
  	public $timestamps = false;

	protected $fillable =[
        'fk_doc',
        'fk_lugar' 
	]; 
}
