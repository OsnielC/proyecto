<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoM extends Model
{
    protected $table = 'documento';
    protected $primaryKey = 'id_doc';
    public $timestamps = false;
	
	protected $fillable =[
		'titulo',
		'lugar_public_pais',
		'lugar_public_edo',
		'derecho_autor',
		'fecha_publi',
		'url',
		'investigador',
		'fecha_consulta',
		'poblacion',
		'tipo',
		'notas',
		'fecha_registro',
		'revisado',
		'linea'
	]; 

	public function fechaNormal()
	{
		return $this->hasOne('App\FechaNormalM','fk_doc','id_doc');
	}

	public function fechaExtra()
	{
		return $this->hasOne('App\FechaExtraM','fk_doc','id_doc');
	}

	public function ponencia(){
		return $this->hasOne('App\PonenciaM','fk_doc','id_doc');
	}

	public function libro(){
		return $this->hasOne('App\LibroM','fk_doc','id_doc');
	}

	public function tesis(){
		return $this->hasOne('App\TesisM','fk_doc','id_doc');
	}

	public function video(){
		return $this->hasOne('App\VideoM','fk_doc','id_doc');
	}
	
	public function revistaBoletin(){
		return $this->hasOne('App\RevistaBoletinM','fk_doc','id_doc');
	}

	public function capituloLibro(){
		return $this->hasOne('App\CapituloLibroM','fk_doc','id_doc');
	}

	public function autor(){
		return $this->belongsToMany('App\AutorM','cntrl_autor','fk_doc','fk_autor')->withPivot('extra');
	}

	public function editor(){
		return $this->belongsToMany('App\EditorM','cntrl_editor','fk_doc','fk_editor');
	}

	/*public function proyecto(){
		return $this->belongsToMany('App\ProyectoM','cntrl_proyec','fk_doc','fk_proyec');
	}*/

	public function institucion(){
		return $this->belongsToMany('App\InstitucionM','cntrl_instit','fk_doc','fk_instit');
	}

	public function persona(){
		return $this->belongsToMany('App\PersonaM','cntrl_persona','fk_doc','fk_persona');
	}

	public function tema(){
		return $this->belongsToMany('App\TemaM','cntrl_tema','fk_doc','fk_tema');
	}

	public function lugar(){
		return $this->belongsToMany('App\LugarM','cntrl_lugar','fk_doc','fk_lugar');
	}
}
