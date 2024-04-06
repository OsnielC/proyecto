<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Carbon;

//Modelos
use App\DocumentoM;
use App\CatalogoDocumentoM;
use App\FechaNormalM;
use App\FechaExtraM;
use App\RevistaBoletinM;
use App\PonenciaM;
use App\LibroM;
use App\CapituloLibroM;
use App\TesisM;
use App\AutorDocumento;
use App\AutorM;
use App\EditorDocumento;
use App\EditorM;
use App\TemaM;
use App\VideoM;
use App\TemaDocumento;
use App\Utilidad; //Clase Utilidad para consecutivos en tablas
use Auth;
use Illuminate\Support\Facades\DB;
use Session;
use BBCode;
use App\User;

class DocumentoController extends Controller
{

    public function index()
    {
        //$documentos = Documento::all();
        return view('documento.index');
    }

    private function obtenerTipoDocumento($idTipoDocumento, $idDocumento)
    {
        $result = null;
        $documento = DocumentoM::findOrFail($idDocumento);
        if ($idTipoDocumento == '14' || $idTipoDocumento == '18' ||  $idTipoDocumento == '2'  || $idTipoDocumento == '17' || $idTipoDocumento == '19') {
            $result = $documento->revistaBoletin;
        } elseif ($idTipoDocumento == "15") {
            $result = $documento->capituloLibro;
        } elseif ($idTipoDocumento == "13") {
            $result = $documento->tesis;
        } elseif ($idTipoDocumento == "10") {
            $result = $documento->ponencia;
        } elseif ($idTipoDocumento == "7" || $idTipoDocumento == "8" || $idTipoDocumento == "11") {
            $result = $documento->libro;
        } elseif ($idTipoDocumento == "16") {
            $result = $documento->video;
        }

        return $result;
    }

    //=======REFERENCIAS FUNCIONES =============

    public function tipoReferencia($documento)
    {
        $autores = $documento->autor;
        $editores = $documento->editor;
        $tipo = $documento->tipo;

        if ($tipo == 1) { // Artículos
            $referencia = self::ReferenciaArticulo($documento, $autores, $editores);
        } else if ($tipo == 2) { //Boletines
            $referencia = self::ReferenciaBoletinRevista($documento, $autores, $editores);
        } else if ($tipo == 3) { //Cartas y Oficios
            $referencia = self::ReferenciaCartasOficios($documento, $autores, $editores);
        } else if ($tipo == 4) { //Crónicas
            $referencia = self::ReferenciaCronica($documento, $autores, $editores);
        } else if ($tipo == 5) { //Declaraciones y Comunidados
            $referencia = self::ReferenciaDeclaracionesComunicados($documento, $autores, $editores);
        } else if ($tipo == 6) { //Discursos
            $referencia = self::ReferenciaDiscurso($documento, $autores, $editores);
        } else if ($tipo == 7) { //Informes
            $referencia = self::ReferenciaInformes($documento, $autores, $editores);
        } else if ($tipo == 8) { //Libros
            $referencia = self::ReferenciaLibro($documento, $autores, $editores);
        } else if ($tipo == 9) { //Notas
            $referencia = self::ReferenciaNotas($documento, $autores, $editores);
        } else if ($tipo == 10) { //Ponencias
            $referencia = self::ReferenciaPonencia($documento, $autores, $editores);
        } else if ($tipo == 11) { //Proyectos
            $referencia = self::ReferenciaLibro($documento, $autores, $editores);
        } else if ($tipo == 12) { //Otros
            $referencia = self::ReferenciaArticulo($documento, $autores, $editores);
        } else if ($tipo == 13) { //Tesis
            $referencia = self::ReferenciaTesis($documento, $autores, $editores);
        } else if ($tipo == 14) { //Artículo de Revista
            $referencia = self::ReferenciaArticuloBoletinRevista($documento, $autores, $editores);
        } else if ($tipo == 15) { //Capítulo de Libros
            $referencia = self::ReferenciaCapituloLibro($documento, $autores, $editores);
        } else if ($tipo == 16) { //Videos
            $referencia = self::ReferenciaVideo($documento, $editores);
        } else if ($tipo == 17) { //Revistas
            $referencia = self::ReferenciaBoletinRevista($documento, $autores, $editores);
        } else if ($tipo == 18) { //Artículos de Boletín
            $referencia = self::ReferenciaArticuloBoletinRevista($documento, $autores, $editores);
        } else if ($tipo == 19) { //Artículos de Periódico
            $referencia = self::ReferenciaArticuloPeriodico($documento, $autores, $editores);
        }

        return $referencia;
    }

    public function construirReferenciaSubtemas($id_doc, $input)
    {
        $subs = DB::table('documento')
            ->join('cntrl_sub', 'id_doc', '=', 'fk_doc')
            ->join('subtema', 'id_sub', '=', 'fk_sub')
            ->select('subtema')
            ->where('id_doc', '=', $id_doc)
            ->where('subtema', 'like', "%$input%")
            ->get();

        if (count($subs) == 0) {
            return "";
        } else {
            $referenciaSubtema = "Tags: ";
            for ($i = 0; $i < count($subs) - 1; $i++) {
                $referenciaSubtema = $referenciaSubtema . $subs[$i]->subtema . ", ";
            }
            $referenciaSubtema = $referenciaSubtema . $subs[count($subs) - 1]->subtema;

            return $referenciaSubtema;
        }
    }

    public function construirReferenciaTemas($temas)
    {

        $numTemas = count($temas);

        if ($numTemas == 0) {
            return "";
        } else {
            $referenciaTema = "Temas: ";
            for ($i = 0; $i < $numTemas - 2; $i++) {
                $referenciaTema = $referenciaTema . $temas[$i]->descripcion . ", ";
            }

            $aux = trim($temas[$numTemas - 1]->descripcion);
            $aux = substr($aux, 0, 1);

            if ($aux == "i" || $aux == "I") {
                $separador = " e ";
            } else {
                $separador = " y ";
            }

            if ($numTemas == 1) {
                $referenciaTema = $referenciaTema . $temas[$numTemas - 1]->descripcion . ".";
            } else {
                $referenciaTema = $referenciaTema . $temas[$numTemas - 2]->descripcion . $separador . $temas[$numTemas - 1]->descripcion . ".";
            }

            return $referenciaTema;
        }
    }

    public function construirReferenciaPoblacion($poblacion)
    {
        if ($poblacion == 0) {
            $referenciaPoblacion = "";
        } else {
            $referenciaPoblacion = "Población: ";
            if ($poblacion == 1)
                $referenciaPoblacion = $referenciaPoblacion . "Afrodescendiente.";
            elseif ($poblacion == 2)
                $referenciaPoblacion = $referenciaPoblacion . "Indígena.";
            elseif ($poblacion == 3)
                $referenciaPoblacion = $referenciaPoblacion . "Afrodescendiente e Indígena.";
        }
        return $referenciaPoblacion;
    }

    private function obtenerEditoresReferencia($editores)
    {
        $editor = '';
        for ($i = 0; $i < sizeof($editores); $i++) {
            if ($i == (sizeof($editores) - 1)) {
                $editor = $editor . $editores[$i]->editor . "";
            } else {
                $editor = $editor . $editores[$i]->editor . ' - ';
            }
        }
        return $editor == '' ? $editor = '[s.e.]' : $editor;
    }

    private function obtenerFechaReferencia($documento)
    {
        $fecha = null;
        $fechaExtra = null;

        if ($documento->fecha_publi == 1) {
            $fecha = $documento->fechaNormal->fecha;
        } else {
            $fechaExtra = $documento->fechaExtra->mes . ' a ' . $documento->fechaExtra->mes2 . ' de ' . $documento->fechaExtra->anio;
        }

        if ($fecha != null) {
            return   Utilidad::getFecha($fecha);
        } else {
            if ($fechaExtra != null) {
                return $documento->fechaExtra->mes . '-' . $documento->fechaExtra->mes2 . ' de ' . $documento->fechaExtra->anio . '';
            } else {
                return "[s.f.] ";
            }
        }
    }

    private function obtenerLugarPublicacionReferenia($documento)
    {
        $estado = $documento->lugar_public_edo;
        $pais =  $documento->lugar_public_pais;

        if ($estado != "" && $pais != "") {
            return $estado . ', ' . $pais;
        } else if ($estado != "" && $pais == "") {
            return $estado;
        } else if ($estado == "" && $pais != "") {
            return $pais;
        } else {
            return '[s.l.]';
        }
    }

    public function construirReferenciaAutor($autor)
    {
        $referenciaAutor = "";
        if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos != "") {
            $referenciaAutor = $autor->apellidos . ", " . $autor->nombre . '';
        } else {
            if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                $referenciaAutor = $autor->nombre . "";
            } else {
                if (($autor->nombre != "" || $autor->apellidos != "") && $autor->pseudonimo != "") {
                    $referenciaAutor =  $autor->pseudonimo . " [" . $autor->nombre . " " . $autor->apellidos . "]";
                } else {
                    $referenciaAutor =  $autor->pseudonimo;
                }
            }
        }
        return $referenciaAutor;
    }

    public function construirReferenciaAutores($autores, $documento)
    {
        $numAutores = count($autores);
        //print($numAutores);
        $referenciaAutores = "";
        if ($numAutores == 0) {
            return "";
        } else {
            if ($numAutores == 1) {
                $referenciaAutores = self::construirReferenciaAutor($autores[0]);
                Log::warning("" . $autores[0]->extra);
                if ($autores[0]->extra != "") {
                    $referenciaAutores = $referenciaAutores . ' (' . $autores[0]->extra . '.)';
                }
                return $referenciaAutores;
            } else {
                if ($numAutores == 2) {
                    $separador = " y ";
                    if ($autores[1]->nombre != "") {
                        //$aux = self::normaliza(substr($autores[1]->nombre, -strlen($autores[1]->nombre)));
                        $aux = trim($autores[1]->nombre);
                        $aux = substr($aux, 0, 1);

                        Log::warning($aux);
                        if ($aux == "i" || $aux == "I") {
                            $separador = " e ";
                        }
                    }

                    if ($autores[0]->extra == $autores[1]->extra && ($autores[0]->extra != "" && $autores[1]->extra != "")) {
                        $referenciaAutores = self::construirReferenciaAutor($autores[0]) . $separador . $autores[1]->nombre . " " . $autores[1]->apellidos;
                        $referenciaAutores = $referenciaAutores . ' (' . $autores[0]->extra . 's.)';
                    } else {
                        if ($autores[0]->extra != "" && $autores[1]->extra != "") {
                            //  $referenciaAutores = self::construirReferenciaAutor($autores[0]) . " " . ' (' . $autores[0]->extra . '.) ';
                            Log::warning("aquiiiiiiii");
                            if ($autores[0]->nombre != "" && $autores[0]->apellidos != "") {
                                $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . ' (' . $autores[0]->extra . ')' . $separador;
                            } else {
                                if ($autores[0]->nombre != "" && $autores[0]->pseudonimo == "" && $autores[0]->apellidos == "") {
                                    $referenciaAutores =  $autores[0]->nombre . ' (' . $autores[0]->extra . ')' . $separador;
                                } else {
                                    if ($autores[0]->nombre == "" && $autores[0]->pseudonimo != "" && $autores[0]->apellidos == "") {
                                        $referenciaAutores =  $autores[0]->pseudonimo . ' (' . $autores[0]->extra . ')' . $separador;
                                    }
                                }
                            }
                            // $referenciaAutores = $referenciaAutores . $autores[1]->nombre . " " . $autores[1]->apellidos . ' (' . $autores[1]->extra . '.)';
                            if ($autores[1]->nombre != "" && $autores[1]->apellidos != "") {
                                $referenciaAutores = $referenciaAutores . $autores[1]->apellidos . ", " . $autores[1]->nombre . ' (' . $autores[1]->extra . ')';
                            } else {
                                if ($autores[1]->nombre != "" && $autores[1]->pseudonimo == "" && $autores[1]->apellidos == "") {
                                    $referenciaAutores = $referenciaAutores . $autores[1]->nombre . '(' . $autores[1]->extra . ')';
                                } else {
                                    if ($autores[1]->nombre == "" && $autores[1]->pseudonimo != "" && $autores[1]->apellidos == "") {
                                        $referenciaAutores = $referenciaAutores . $autores[1]->pseudonimo . '(' . $autores[1]->extra . ')';
                                    }
                                }
                            }
                        } else {
                            // $referenciaAutores = self::construirReferenciaAutor($autores[0]) . $separador;
                            if ($autores[0]->extra != "") {
                                if ($autores[0]->nombre != "" && $autores[0]->apellidos != "") {
                                    $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . ' (' . $autores[0]->extra . ')';
                                } else {
                                    if ($autores[0]->nombre != "" && $autores[0]->pseudonimo == "" && $autores[0]->apellidos == "") {
                                        $referenciaAutores =  $autores[0]->nombre . ' (' . $autores[0]->extra . ')';
                                    } else {
                                        if ($autores[0]->nombre == "" && $autores[0]->pseudonimo != "" && $autores[0]->apellidos == "") {
                                            $referenciaAutores =  $autores[0]->pseudonimo . ' (' . $autores[0]->extra . ')';
                                        }
                                    }
                                }
                            } else {
                                if ($autores[1]->extra != "") {
                                    if ($autores[1]->nombre != "" && $autores[1]->apellidos != "") {
                                        $referenciaAutores = $referenciaAutores . $autores[1]->apellidos . ", " . $autores[1]->nombre . ' (' . $autores[1]->extra . ')';
                                    } else {
                                        if ($autores[1]->nombre != "" && $autores[1]->pseudonimo == "" && $autores[1]->apellidos == "") {
                                            $referenciaAutores = $referenciaAutores . $autores[1]->nombre . '(' . $autores[1]->extra . ')';
                                        } else {
                                            if ($autores[1]->nombre == "" && $autores[1]->pseudonimo != "" && $autores[1]->apellidos == "") {
                                                $referenciaAutores = $referenciaAutores . $autores[1]->pseudonimo . '(' . $autores[1]->extra . ')';
                                            }
                                        }
                                    }
                                } else {
                                    // $referenciaAutores = $referenciaAutores . $autores[1]->nombre . " " . $autores[1]->apellidos;
                                    if ($autores[0]->nombre != "" && $autores[0]->apellidos != "") {
                                        $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . $separador;
                                    } else {
                                        if ($autores[0]->nombre != "" && $autores[0]->pseudonimo == "" && $autores[0]->apellidos == "") {
                                            $referenciaAutores =  $autores[0]->nombre . $separador;
                                        } else {
                                            if ($autores[0]->nombre == "" && $autores[0]->pseudonimo != "" && $autores[0]->apellidos == "") {
                                                $referenciaAutores =  $autores[0]->pseudonimo . $separador;
                                            }
                                        }
                                    }
                                    if ($autores[1]->nombre != "" && $autores[1]->apellidos != "") {
                                        $referenciaAutores = $referenciaAutores . $autores[1]->nombre . " " . $autores[1]->apellidos;
                                    } else {
                                        if ($autores[1]->nombre != "" && $autores[1]->pseudonimo == "" && $autores[1]->apellidos == "") {
                                            $referenciaAutores = $referenciaAutores . $autores[1]->nombre;
                                        } else {
                                            if ($autores[1]->nombre == "" && $autores[1]->pseudonimo != "" && $autores[1]->apellidos == "") {
                                                $referenciaAutores = $referenciaAutores . $autores[1]->pseudonimo;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    return $referenciaAutores;
                } else {
                    if ($numAutores >= 3) {

                        $bandera = true;
                        for ($i = 0; $i < $numAutores - 1; $i++) {
                            if ($autores[$i]->extra == "" || $autores[$i]->extra != $autores[$i + 1]->extra) {
                                $bandera = false;
                                break;
                            }
                        }
                        if ($bandera) {
                            Log::warning("Entro bandera");
                            if ($autores[0]->nombre != "" && $autores[0]->apellidos != "") {
                                $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . ". [et al.], " . '(' . $autores[0]->extra . 's.)';
                            } else {
                                if ($autores[0]->nombre != "" && $autores[0]->pseudonimo == "" && $autores[0]->apellidos == "") {
                                    return $autores[0]->nombre . ". [et al.] " . '(' . $autores[0]->extra . 's.)';
                                } else {
                                    if ($autores[0]->nombre == "" && $autores[0]->pseudonimo != "" && $autores[0]->apellidos == "") {
                                        return $autores[0]->pseudonimo . ". [et al.] " . '(' . $autores[0]->extra . 's.)';
                                    }
                                }
                            }
                            //  $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . " [et al.], " . '(' . $autores[0]->extra . '.s)';
                        } else {
                            $numCargos = 0;
                            for ($i = 0; $i < $numAutores; $i++) {
                                if ($autores[$i]->extra != "") {
                                    $numCargos = $numCargos + 1;
                                }
                            }

                            if ($numCargos == 1) {
                                $autor = null;
                                for ($i = 0; $i < $numAutores; $i++) {
                                    if ($autores[$i]->extra != "") {
                                        $autor = $autores[$i];
                                    }
                                }

                                if ($autor->nombre != "" && $autor->apellidos != "") {
                                    $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                } else {
                                    if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                        $referenciaAutores = $autor->nombre . ". [et al.]" . '(' . $autor->extra . '.)';
                                    } else {
                                        if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                            $referenciaAutores = $autor->pseudonimo . ". [et al.]" . '(' . $autor->extra . '.)';
                                        }
                                    }
                                }
                                // $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autor->extra . '.)';
                            } else {
                                $contEd = 0;
                                for ($i = 0; $i < $numAutores; $i++) {
                                    if ($autores[$i]->extra == "ed") {
                                        $contEd = $contEd + 1;
                                        Log::warning("ContEd " . $contEd . "- " . $numAutores);
                                    }
                                }
                                $contComp = 0;
                                for ($i = 0; $i < $numAutores; $i++) {
                                    if ($autores[$i]->extra == "comp") {
                                        $contComp = $contComp + 1;
                                    }
                                }
                                $contCoord = 0;
                                for ($i = 0; $i < $numAutores; $i++) {
                                    if ($autores[$i]->extra == "coord") {
                                        $contCoord = $contCoord + 1;
                                    }
                                }

                                if ($contEd == 1 && $contComp == 1 && $contCoord == 1) {
                                    Log::warning("Entro if 1-1");
                                    $autor = null;
                                    for ($i = 0; $i < $numAutores; $i++) {
                                        if ($autores[$i]->extra != "") {
                                            $autor = $autores[$i];
                                            break;
                                        }
                                    }
                                    if ($autor !== null) {
                                        if ($autor->nombre != "" && $autor->apellidos != "") {
                                            $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                        } else {
                                            if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                            } else {
                                                if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                    $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . '.)';
                                                }
                                            }
                                        }
                                    }
                                    //$referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.)';
                                } else {
                                    if ($contEd == 0 && $contComp == 1 && $contCoord == 1) {
                                        Log::warning("Entro if 1-2");
                                        $autor = null;
                                        for ($i = 0; $i < $numAutores; $i++) {
                                            if ($autores[$i]->extra != "") {
                                                $autor = $autores[$i];
                                                break;
                                            }
                                        }
                                        if ($autor !== null) {
                                            if ($autor->nombre != "" && $autor->apellidos != "") {
                                                $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                            } else {
                                                if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                    $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                                } else {
                                                    if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                        $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . '.)';
                                                    }
                                                }
                                            }
                                        }
                                        // $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.)';
                                    } else {
                                        if ($contEd == 1 && $contComp == 0 && $contCoord == 1) {
                                            Log::warning("Entro if 1-3");
                                            $autor = null;
                                            for ($i = 0; $i < $numAutores; $i++) {
                                                if ($autores[$i]->extra != "") {
                                                    $autor = $autores[$i];
                                                    break;
                                                }
                                            }
                                            if ($autor !== null) {
                                                if ($autor->nombre != "" && $autor->apellidos != "") {
                                                    $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                                } else {
                                                    if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                        $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                                    } else {
                                                        if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                            $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . '.)';
                                                        }
                                                    }
                                                }
                                            }
                                            // $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.)';
                                        } else {


                                            if ($contEd == 1 && $contComp == 1 && $contCoord == 0) {
                                                Log::warning("Entro if 1-4");
                                                $autor = null;
                                                for ($i = 0; $i < $numAutores; $i++) {
                                                    if ($autores[$i]->extra != "") {
                                                        $autor = $autores[$i];
                                                        break;
                                                    }
                                                }
                                                if ($autor !== null) {
                                                    if ($autor->nombre != "" && $autor->apellidos != "") {
                                                        $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                                    } else {
                                                        if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                            $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . '.)';
                                                        } else {
                                                            if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                                $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . '.)';
                                                            }
                                                        }
                                                    }
                                                }
                                                //  $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.)';
                                            } else {
                                                if ($contEd == 0 && $contComp == 0 && $contCoord == 0) {
                                                    Log::warning("Entro if 1-0");
                                                    if ($autores[0]->nombre != "" && $autores[0]->apellidos != "") {
                                                        $referenciaAutores = $autores[0]->apellidos . ", " . $autores[0]->nombre . ". [et al.]";
                                                    } else {
                                                        if ($autores[0]->nombre != "" && $autores[0]->pseudonimo == "" && $autores[0]->apellidos == "") {
                                                            $referenciaAutores = $autores[0]->nombre . ". [et al.]";
                                                        } else {
                                                            if ($autores[0]->nombre == "" && $autores[0]->pseudonimo != "" && $autores[0]->apellidos == "") {
                                                                $referenciaAutores = $autores[0]->pseudonimo . ". [et al.]";
                                                            }
                                                        }
                                                    }
                                                    //$referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], ";
                                                } else {
                                                    if ($contEd < $contComp) {
                                                        Log::warning("---------------------------------Entro if 1");
                                                        Log::warning("---------------------------------Entro if " . $contEd . "<" . $contComp);
                                                        if ($contComp > $contCoord) {
                                                            $autor = null;
                                                            for ($i = 0; $i < $numAutores; $i++) {
                                                                if ($autores[$i]->extra == "comp") {
                                                                    $autor = $autores[$i];
                                                                    break;
                                                                }
                                                            }
                                                            if ($autor !== null) {
                                                                if ($autor->nombre != "" && $autor->apellidos != "") {
                                                                    $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                } else {
                                                                    if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                                        $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                    } else {
                                                                        if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                                            $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            // $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.s)';
                                                        } else {
                                                            Log::warning("---------------------------------Entro if 2");
                                                            $autor = null;
                                                            for ($i = 0; $i < $numAutores; $i++) {
                                                                if ($autores[$i]->extra == "coord") {
                                                                    $autor = $autores[$i];
                                                                    break;
                                                                }
                                                            }

                                                            if ($autor !== null) {
                                                                if ($autor->nombre != "" && $autor->apellidos != "") {
                                                                    $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                } else {
                                                                    if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                                        $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                    } else {
                                                                        if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                                            $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            //$referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.s)';
                                                        }
                                                    } else {
                                                        if ($contEd > $contCoord) {
                                                            Log::warning("-----------------Entro if 3");
                                                            $autor = null;
                                                            for ($i = 0; $i < $numAutores; $i++) {
                                                                if ($autores[$i]->extra == "ed") {
                                                                    $autor = $autores[$i];
                                                                    break;
                                                                }
                                                            }
                                                            if ($autor !== null) {
                                                                if ($autor->nombre != "" && $autor->apellidos != "") {
                                                                    $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                } else {
                                                                    if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                                        $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                    } else {
                                                                        if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                                            $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            //$referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.s)';
                                                        } else {
                                                            Log::warning("Entro if 4");
                                                            $autor = null;
                                                            for ($i = 0; $i < $numAutores; $i++) {
                                                                if ($autores[$i]->extra == "coord") {
                                                                    $autor = $autores[$i];
                                                                    break;
                                                                }
                                                            }

                                                            if ($autor !== null) {
                                                                if ($autor->nombre != "" && $autor->apellidos != "") {
                                                                    $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                } else {
                                                                    if ($autor->nombre != "" && $autor->pseudonimo == "" && $autor->apellidos == "") {
                                                                        $referenciaAutores = $autor->nombre . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                    } else {
                                                                        if ($autor->nombre == "" && $autor->pseudonimo != "" && $autor->apellidos == "") {
                                                                            $referenciaAutores = $autor->pseudonimo . ". [et al.], " . '(' . $autor->extra . 's.)';
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            // $referenciaAutores = $autor->apellidos . ", " . $autor->nombre . " [et al.], " . '(' . $autores[0]->extra . '.s)';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        return $referenciaAutores;
                    }
                }
            }
        }
    }

    public function ReferenciaAutor($autores, $documento)
    {
        /*   $referenciaReal = "";
        $autores= self::construirReferenciaAutores($autores,$documento);
        if($referenciaAutor!=""){
            $referenciaReal= $referenciaReal . $referenciaAutor . ", ";
        }*/
        $autores = self::construirReferenciaAutores($autores, $documento);
        $autores = $autores != "" ? $autores . ", " : "";
        return $autores;
    }


    //Obtiene todos los datos y retorna la referencia ya construida como cadena
    public function ReferenciaArticulo($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaAutor . ", ";
        }
        $referenciaReal = $referenciaReal . "\"" . $documento->titulo . "\", ";

        if ($referenciaEditorial != "" and $referenciaEditorial != "[s.e.]") {
            $referenciaReal = $referenciaReal . $referenciaEditorial;
        } else if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion;
        }

        Log::warning($referenciaReal);
        return $referenciaReal . ".";
    }

    public function ReferenciaBoletinRevista($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $titulo = $documento->titulo;

        $lugar = self::obtenerLugarPublicacionReferenia($documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $fechaPublicacion = self::obtenerFechaReferencia($documento);
        $boletinRevista = self::obtenerTipoDocumento($documento->tipo, $documento->id_doc);

        $anio = $boletinRevista->anio;
        $volumen  =  $boletinRevista->volumen;
        $numero  =  $boletinRevista->num_revista;
        $pag  =  $boletinRevista->pag;

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaAutor . ", ";
        }
        $referenciaReal = $referenciaReal . $titulo . ", " . $lugar . ", ";

        if ($referenciaEditorial != "" and $referenciaEditorial != "[s.e.]") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($anio != "") {
            $referenciaReal = $referenciaReal . $anio . ", ";
        }
        if ($volumen != "") {
            $referenciaReal = $referenciaReal . $volumen . ", ";
        }
        if ($numero != "") {
            $referenciaReal = $referenciaReal . $numero . ", ";
        }

        if ($fechaPublicacion != "" && $pag != "") {
            $referenciaReal = $referenciaReal . $fechaPublicacion . ', ' . $pag . '.';
        } else if ($fechaPublicacion != "" && $pag == "") {
            $referenciaReal = $referenciaReal . $infoFecha_pag = $fechaPublicacion . '.';
        }

        if ($boletinRevista->isbn != "") {
            $referenciaReal = $referenciaReal . " ISBN: " . $boletinRevista->isbn;
        }

        if ($boletinRevista->isbn != "" and $$boletinRevista->issn != "") {
            $referenciaReal = $referenciaReal . ", ISSN: " . $boletinRevista->issn;
        } else if ($boletinRevista->issn != "") {
            $referenciaReal = $referenciaReal . " ISSN: " . $boletinRevista->issn;
        }

        return $referenciaReal;
    }

    public function ReferenciaCartasOficios($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = $documento->titulo;
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        return $referenciaReal . ".";
    }

    public function ReferenciaCronica($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaAutor . ", ";
        }
        $referenciaReal = $referenciaReal . "\"" . $documento->titulo . "\", ";

        if ($referenciaEditorial != "" and $referenciaEditorial != "[s.e.]") {
            $referenciaReal = $referenciaReal . $referenciaEditorial;
        } else if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion;
        }

        Log::warning($referenciaReal);
        return $referenciaReal . ".";
    }

    public function ReferenciaDeclaracionesComunicados($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = $documento->titulo;
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion;
        }

        //$referenciaReal = $referenciaAutor . ", " . $referenciaTitulo . ", " . $referenciaLugarPublicacion . ", " . $referenciaEditorial . ", " . $referenciaFechPublicacion;
        Log::warning($referenciaReal);
        return $referenciaReal . ".";
    }

    public function ReferenciaDiscurso($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = $documento->titulo;
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }

        $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion;
        }

        return $referenciaReal . ".";
    }

    public function ReferenciaInformes($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = $documento->titulo;
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }

        $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        return $referenciaReal . ".";
    }

    public function ReferenciaNotas($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = "\"" . $documento->titulo . "\"";
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        if ($referenciaTitulo != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }

        if ($referenciaEditorial != "" and $referenciaEditorial != "[s.e.]") {
            $referenciaReal = $referenciaReal . $referenciaEditorial;
        } else if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        return $referenciaReal . ".";
    }

    public function ReferenciaPonencia($documento, $autores, $editores)
    {

        $referenciaReal = "";
        $referenciaTitulo = $documento->titulo;
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $editor = self::obtenerEditoresReferencia($editores);
        $fechaPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $ponencia = self::obtenerTipoDocumento($documento->tipo, $documento->id_doc);

        $evento = $ponencia->evento;
        $lugar = $ponencia->lugar_presentacion;
        $fechaPresentacion = $ponencia->fecha_pesentacion;
        $pag = $ponencia->paginas;

        $info = "";

        if ($evento != "" && $lugar != "" && $fechaPresentacion != "") {
            $info = ' (' . $evento . ' [' . $lugar . ', ' . $fechaPresentacion . ']), ';
        } else if ($evento != "" && $lugar == "" && $fechaPresentacion == "") {
            $info = ' (' . $evento . '), ';
        } else if ($evento != "" && $lugar != "" && $fechaPresentacion == "") {
            $info = ' (' . $evento . ' [' . $lugar . ']), ';
        } else if ($evento != "" && $lugar == "" && $fechaPresentacion != "") {
            $info = ' (' . $evento . ' [' . $fechaPresentacion . ']), ';
        } else if ($evento == "" && $lugar != "" && $fechaPresentacion != "") {
            $info = ' ([' . $lugar . ', ' . $fechaPresentacion . ']), ';
        } else if ($evento == "" && $lugar == "" && $fechaPresentacion != "") {
            $info = ' ([' . $fechaPresentacion . ']), ';
        } else if ($evento == "" && $lugar != "" && $fechaPresentacion == "") {
            $info = ' ([' . $lugar . ']), ';
        }

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }

        $referenciaReal = $referenciaReal . $referenciaTitulo;

        if ($info != "") {
            $referenciaReal = $referenciaReal . " " . $info;
        } else {
            $referenciaReal = $referenciaReal . ", ";
        }

        $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ', ' . $editor . ', ';

        if ($pag != "") {
            $referenciaReal = $referenciaReal . $fechaPublicacion . ', ' . $pag . '. ';
        } else {
            $referenciaReal = $referenciaReal . $fechaPublicacion .  '.';
        }

        return $referenciaReal;
    }

    /*public function ReferenciaProyectos($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = BBCode::parse('[i]'.$documento->titulo.'[/i]');
        $referenciaAutor = self::construirReferenciaAutores($autores,$documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }
        
        $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . "";
        }
        return $referenciaReal.".";
    }*/

    public function ReferenciaTesis($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = $documento->titulo;
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $tesis = self::obtenerTipoDocumento($documento->tipo, $documento->id_doc);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }

        if ($tesis->grado != "") {
            $referenciaReal = $referenciaReal . $referenciaTitulo . " (" . $tesis->grado . ")" . ", ";
        } else {
            $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";
        }

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaFechPublicacion != "" && $tesis->num_paginas != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion . ", ";
        } else {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion;
        }

        $referenciaReal = $referenciaReal . ".";

        if ($tesis->isbn != "") {
            $referenciaReal = $referenciaReal . " ISBN: " . $tesis->isbn;
        }

        if ($tesis->isbn != "" and $tesis->issn != "") {
            $referenciaReal = $referenciaReal . ", ISSN: " . $tesis->issn;
        } else if ($tesis->issn != "") {
            $referenciaReal = $referenciaReal . " ISSN: " . $tesis->issn;
        }

        return $referenciaReal;
    }

    public function ReferenciaCapituloLibro($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = "\"" . $documento->titulo . "\"";
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $capituloLibro = self::obtenerTipoDocumento($documento->tipo, $documento->id_doc);

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }

        $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";

        if ($capituloLibro->autorgral != "") {
            $referenciaReal = $referenciaReal  . 'en ' . $capituloLibro->autorgral . ", ";
        }

        if ($capituloLibro->nombre_libro != "") {
            $referenciaReal = $referenciaReal . "" . $capituloLibro->nombre_libro;
        }

        if ($capituloLibro->edicion != "") {
            $referenciaReal = $referenciaReal . "" . $capituloLibro->edicion . ", ";
        }
        if ($capituloLibro->traductor != "") {
            $referenciaReal = $referenciaReal . "Trad. " . $capituloLibro->traductor . ", ";
        }
        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }
        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }
        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion;
        }

        if ($capituloLibro->tomos != "") {
            $referenciaReal = $referenciaReal . ", " . $capituloLibro->tomos;
        }
        if ($capituloLibro->volumen != "") {
            $referenciaReal = $referenciaReal . ", " . $capituloLibro->volumen;
        }
        if ($capituloLibro->paginas != "") {
            $referenciaReal = $referenciaReal . ", " . $capituloLibro->paginas;
        }
        $referenciaReal = $referenciaReal . ".";

        $coleccion = $capituloLibro->coleccion;
        $noCol = $capituloLibro->nocol;
        $serie = $capituloLibro->serie;
        $noSerie = $capituloLibro->noserie;

        //COleccion y Serie
        if ($coleccion != "" && $noCol != "" && $serie != "" && $noSerie != "") { // Coleccion y Serie 4 tienen datos
            $referenciaReal = $referenciaReal . ' (Col.' . $coleccion . ', ' . $noCol . ', Serie ' . $serie . ', ' . $noSerie . ')';
        } else if ($coleccion != "" && $noCol != "" && $serie == "" && $noSerie == "") { //solo colección y no. coleccion tiene datos
            $referenciaReal = $referenciaReal . ' (Col. ' . $coleccion . ', ' . $noCol . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie != "") { //Solo seríe y no. de serie tiene datos
            $referenciaReal = $referenciaReal . ' (Serie ' . $serie . ', ' . $noSerie . ')';
        } else if ($coleccion != "" && $noCol == "" && $serie != "" && $noSerie == "") { //solo coleccion y serie tiene datos
            $referenciaReal = $referenciaReal . ' (Col. ' . $coleccion . ', Serie ' . $serie . ')';
        } else if ($coleccion != "" && $noCol == "" && $serie == "" && $noSerie == "") { //Solo colección
            $referenciaReal = $referenciaReal . ' (Col. ' . $coleccion . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie == "") { //Solo Serie
            $referenciaReal = $referenciaReal . ' (Serie ' . $serie . ')';
        }

        if ($capituloLibro->isbn != "") {
            $referenciaReal = $referenciaReal . " ISBN: " . $capituloLibro->isbn;
        }

        if ($capituloLibro->isbn != "" and $capituloLibro->issn != "") {
            $referenciaReal = $referenciaReal . ", ISSN: " . $capituloLibro->issn;
        } else if ($capituloLibro->issn != "") {
            $referenciaReal = $referenciaReal . " ISSN: " . $capituloLibro->issn;
        }

        return $referenciaReal;
    }

    public function ReferenciaLibro($documento, $autores, $editores)
    {
        $referenciaReal = "";
        $referenciaTitulo = $documento->titulo;
        $referenciaAutor = self::construirReferenciaAutores($autores, $documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);

        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $libro = self::obtenerTipoDocumento($documento->tipo, $documento->id_doc);

        $edicion = $libro->edicion;
        $traductor = $libro->traductor;
        $prologo = $libro->prologo;
        $introduccion = $libro->introduccion;


        $coleccion = $libro->coleccion;
        $noCol = $libro->nocol;
        $serie = $libro->serie;
        $noSerie = $libro->noserie;
        $isbn = $libro->isbn;
        $issn = $libro->issn;

        $info = "";
        $infoAux = "";

        if ($referenciaAutor != "") {
            $referenciaReal = $referenciaReal . $referenciaAutor . ", ";
        }

        $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";

        $info = "";
        $infoAux = "";

        if ($edicion != "") $referenciaReal = $referenciaReal . $edicion . ', ';
        if ($traductor != "") $referenciaReal = $referenciaReal . 'Trad. ' . $traductor . ', ';
        if ($prologo != "") $referenciaReal = $referenciaReal . 'Pról. ' . $prologo . ', ';
        if ($introduccion != "") $referenciaReal = $referenciaReal . 'Introd. ' . $introduccion . ', ';

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }

        if ($referenciaEditorial != "") {
            $referenciaReal = $referenciaReal . $referenciaEditorial . ", ";
        }

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaFechPublicacion;
        }

        if ($libro->tomos != "") {
            $referenciaReal = $referenciaReal . ", " . $libro->tomos;
        }
        if ($libro->volumen != "") {
            $referenciaReal = $referenciaReal . ", " . $libro->volumen;
        }
        if ($libro->paginalib != "") {
            $referenciaReal = $referenciaReal . ", " . $libro->paginalib;
        }
        $referenciaReal = $referenciaReal . ".";


        //COleccion y Serie
        if ($coleccion != "" && $noCol != "" && $serie != "" && $noSerie != "") { // Coleccion y Serie 4 tienen datos
            $referenciaReal = $referenciaReal . ' (Col.' . $coleccion . ', ' . $noCol . ', Serie ' . $serie . ', ' . $noSerie . ')';
        } else if ($coleccion != "" && $noCol != "" && $serie == "" && $noSerie == "") { //solo colección y no. coleccion tiene datos
            $referenciaReal = $referenciaReal . ' (Col. ' . $coleccion . ', ' . $noCol . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie != "") { //Solo seríe y no. de serie tiene datos
            $referenciaReal = $referenciaReal . ' (Serie ' . $serie . ', ' . $noSerie . ')';
        } else if ($coleccion != "" && $noCol == "" && $serie != "" && $noSerie == "") { //solo coleccion y serie tiene datos
            $referenciaReal = $referenciaReal . ' (Col. ' . $coleccion . ', Serie ' . $serie . ')';
        } else if ($coleccion != "" && $noCol == "" && $serie == "" && $noSerie == "") { //Solo colección
            $referenciaReal = $referenciaReal . ' (Col. ' . $coleccion . ')';
        } else if ($coleccion == "" && $noCol == "" && $serie != "" && $noSerie == "") { //Solo Serie
            $referenciaReal = $referenciaReal . ' (Serie ' . $serie . ')';
        }

        if ($libro->isbn != "") {
            $referenciaReal = $referenciaReal . " ISBN: " . $libro->isbn;
        }

        if ($libro->isbn != "" and $libro->issn != "") {
            $referenciaReal = $referenciaReal . ", ISSN: " . $libro->issn;
        } else if ($libro->issn != "") {
            $referenciaReal = $referenciaReal . " ISSN: " . $libro->issn;
        }

        return $referenciaReal;
    }

    public function ReferenciaVideo($documento, $editores)
    {

        $referenciaReal = "";
        $video = self::obtenerTipoDocumento($documento->tipo, $documento->id_doc);
        $tituloAux  = preg_replace("/[\r\n|\n|\r]+/", " ", $documento->titulo);
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $referenciaFechPublicacion = self::obtenerFechaReferencia($documento);
        $editor = self::obtenerEditoresReferencia($editores);

        $referenciaReal = "Título y Subtítulo: " . $tituloAux;

        if ($video->secundario != "")
            $referenciaReal = $referenciaReal . "<br><br>Título Secundario: " . $video->secundario;

        if ($video->director != "")
            $referenciaReal = $referenciaReal . "<br><br>Director: " . $video->director;

        if ($video->productor != "")
            $referenciaReal = $referenciaReal . "<br><br>Productor: " . $video->productor;

        if ($video->realizador != "")
            $referenciaReal = $referenciaReal . "<br><br>Realizador: " . $video->realizador;

        if ($video->guionista != "")
            $referenciaReal = $referenciaReal . "<br><br>Guionista: " . $video->guionista;

        if ($video->fotografia != "")
            $referenciaReal = $referenciaReal . "<br><br>Fotografía: " . $video->fotografia;

        if ($video->musica != "" && $video->musica != null && $video->musica != " ")
            $referenciaReal = $referenciaReal . "<br><br>Música: " . $video->musica;

        if ($video->conductor != "")
            $referenciaReal = $referenciaReal . "<br><br>Conducción: " . $video->conductor;

        if ($video->reportero != "")
            $referenciaReal = $referenciaReal . "<br><br>Reportaje: " . $video->reportero;

        if ($video->actores != "")
            $referenciaReal = $referenciaReal . "<br><br>Reparto: " . $video->actores;

        if ($video->narrador != "")
            $referenciaReal = $referenciaReal . "<br><br>Narración: " . $video->narrador;

        if ($editor != null && $editor != "[s.e.]")
            $referenciaReal = $referenciaReal . "<br><br>Compañía Productora: " . $editor;

        if ($video->canal != "")
            $referenciaReal = $referenciaReal . "<br><br>Canal Transmisor: " . $video->canal;

        if ($referenciaLugarPublicacion != '')
            $referenciaReal = $referenciaReal . "<br><br>Lugar de Edición o Producción: " . $referenciaLugarPublicacion;

        if ($referenciaFechPublicacion != "") {
            $referenciaReal = $referenciaReal . "<br><br>Fecha de Edición o Producción: " . $referenciaFechPublicacion;
        }

        if ($video->programa != "")
            $referenciaReal = $referenciaReal . "<br><br>Programa: " . $video->programa;

        if ($video->fecha_trans != "")
            $referenciaReal = $referenciaReal . "<br><br>Fecha de Transmisión: " . $video->fecha_trans;

        if ($video->hora_trans != "")
            $referenciaReal = $referenciaReal . "<br><br>Hora de Transmisión: " . $video->hora_trans;

        if ($video->formato != "")
            $referenciaReal = $referenciaReal . "<br><br>Formato: " . $video->formato;

        if ($video->idioma != "")
            $referenciaReal = $referenciaReal . "<br><br>Idioma: " . $video->idioma;

        if ($video->subtitulo != "")
            $referenciaReal = $referenciaReal . "<br><br>Subtítulos: " . $video->subtitulo;

        if ($video->duracion != "")
            $referenciaReal = $referenciaReal . "<br><br>Duración: " . $video->duracion;

        return $referenciaReal;
    }

    public function ReferenciaArticuloBoletinRevista($documento, $autores, $editores)
    {

        $referenciaReal = "";
        $autores = self::construirReferenciaAutores($autores, $documento);
        $referenciaTitulo = "\"" . $documento->titulo . "\"";
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $fechaPublicacion = self::obtenerFechaReferencia($documento);

        $artBoletinRevista = self::obtenerTipoDocumento($documento->tipo, $documento->id_doc);

        if ($autores != "") {
            $referenciaReal = $referenciaReal . $autores . ", ";
        }

        $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";

        if ($artBoletinRevista->nombre_revista != '')
            $referenciaReal = $referenciaReal . $artBoletinRevista->nombre_revista . ", ";

        if ($artBoletinRevista->anio != '')
            $referenciaReal = $referenciaReal . $artBoletinRevista->anio . ", ";

        if ($artBoletinRevista->volumen != '')
            $referenciaReal = $referenciaReal . $artBoletinRevista->volumen . ", ";

        if ($artBoletinRevista->num_revista != '')
            $referenciaReal = $referenciaReal . $artBoletinRevista->num_revista . ", ";

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }

        if ($referenciaEditorial != "" && $referenciaEditorial != "[s.e.]") {
            $referenciaReal = $referenciaReal  . $referenciaEditorial . ", ";
        }

        if ($fechaPublicacion != "") {
            $referenciaReal = $referenciaReal  . $fechaPublicacion;
        }

        if ($artBoletinRevista->pag != "")
            $referenciaReal = $referenciaReal  . ", " . $artBoletinRevista->pag;

        $referenciaReal = $referenciaReal . ".";

        if ($artBoletinRevista->isbn != "") {
            $referenciaReal = $referenciaReal . " ISBN: " . $artBoletinRevista->isbn;
        }

        if ($artBoletinRevista->isbn != "" and $artBoletinRevista->issn != "") {
            $referenciaReal = $referenciaReal . ", ISSN: " . $artBoletinRevista->issn;
        } else if ($artBoletinRevista->issn != "") {
            $referenciaReal = $referenciaReal . " ISSN: " . $artBoletinRevista->issn;
        }

        return $referenciaReal;
    }

    /*public function verPdf($id)
    {

        $filepath = storage_path("/docs/".$id.'.pdf');

        if(file_exists($filepath )){
            return response()->file($filepath);

        }else{
            abort(404);
        }
    }*/

    public function ReferenciaArticuloPeriodico($documento, $autores, $editores)
    {

        $referenciaReal = "";
        $autores = self::construirReferenciaAutores($autores, $documento);
        $referenciaTitulo = "\"" . $documento->titulo . "\"";
        $referenciaLugarPublicacion = self::obtenerLugarPublicacionReferenia($documento);
        $referenciaEditorial = self::obtenerEditoresReferencia($editores);
        $fechaPublicacion = self::obtenerFechaReferencia($documento);

        $artBoletinRevista = self::obtenerTipoDocumento($documento->tipo, $documento->id_doc);

        if ($autores != "") {
            $referenciaReal = $referenciaReal . $autores . ", ";
        }

        $referenciaReal = $referenciaReal . $referenciaTitulo . ", ";

        if ($artBoletinRevista->nombre_revista != '')
            $referenciaReal = $referenciaReal . $artBoletinRevista->nombre_revista . ", ";

        if ($artBoletinRevista->anio != '')
            $referenciaReal = $referenciaReal . $artBoletinRevista->anio . ", ";

        if ($artBoletinRevista->volumen != '')
            $referenciaReal = $referenciaReal . $artBoletinRevista->volumen . ", ";

        if ($artBoletinRevista->num_revista != '')
            $referenciaReal = $referenciaReal . $artBoletinRevista->num_revista . ", ";

        if ($referenciaLugarPublicacion != "") {
            $referenciaReal = $referenciaReal . $referenciaLugarPublicacion . ", ";
        }

        if ($referenciaEditorial != "" && $referenciaEditorial != "[s.e.]") {
            $referenciaReal = $referenciaReal  . $referenciaEditorial . ", ";
        }

        if ($fechaPublicacion != "") {
            $referenciaReal = $referenciaReal  . $fechaPublicacion;
        }

        if ($artBoletinRevista->pag != "")
            $referenciaReal = $referenciaReal  . ", " . $artBoletinRevista->pag;

        if ($artBoletinRevista->columna != "") {
            if (str_contains($artBoletinRevista->columna, '-'))
                $referenciaReal = $referenciaReal  . ", Columnas " . $artBoletinRevista->columna;
            else
                $referenciaReal = $referenciaReal  . ", Columna " . $artBoletinRevista->columna;
        }

        if ($artBoletinRevista->seccion != "")
            $referenciaReal = $referenciaReal  . ", Sección " . $artBoletinRevista->seccion;

        $referenciaReal = $referenciaReal . ".";

        if ($artBoletinRevista->isbn != "") {
            $referenciaReal = $referenciaReal . " ISBN: " . $artBoletinRevista->isbn;
        }

        if ($artBoletinRevista->isbn != "" and $artBoletinRevista->issn != "") {
            $referenciaReal = $referenciaReal . ", ISSN: " . $artBoletinRevista->issn;
        } else if ($artBoletinRevista->issn != "") {
            $referenciaReal = $referenciaReal . " ISSN: " . $artBoletinRevista->issn;
        }

        return $referenciaReal;
    }
}
