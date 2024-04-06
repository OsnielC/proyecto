<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutorM extends Model
{
    protected $table = 'autor';
    protected $primaryKey = 'id_autor';
    public $timestamps=false;

    protected $fillable = [
        'pseudonimo',
        'nombre',
        'apellidos'
    ];

    public function autor(){
      return $this->belongsToMany('App\DocumentoM','cntrl_autor','fk_doc','fk_autor')->withPivot('extra');
    }
}
