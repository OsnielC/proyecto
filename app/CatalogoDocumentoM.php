<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CatalogoDocumentoM extends Model
{
    protected $table = 'catalogo_docu';
    protected $primaryKey = 'id_cata_doc';
	public $timestamps = false;

	protected $fillable =[
		'tipo_doc'
	]; 
}
