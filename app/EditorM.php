<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EditorM extends Model
{
    protected $table = 'editor';
    protected $primaryKey = 'id_editor';
    public $timestamps = false;

    protected $fillable = [
        'editor',
        'pais',
        'estado',
        'der_autor'
    ];

    public function documento(){
		return $this->belongsToMany('App\DocumentoM','cntrl_editor','fk_doc','fk_editor');
	}
}
