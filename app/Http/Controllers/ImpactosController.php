<?php

namespace App\Http\Controllers;

use App\Http\Controllers\DocumentoController;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\DocumentoM; // Modelo
use App\CatalogoDocumentoM; // Modelo
use App\TemaM;
use App\LugarM;
use App\InstitucionM;
use App\PersonaM;
use App\AutorM;
use Illuminate\Support\Facades\DB;
use Session;

use Illuminate\Support\Facades\Input;


class ImpactosController extends Controller
{
    // Index para Página de "Trabajo Esclavo"
    public function index1()
    {
        return view('impactos.index1');
    }

    // Index para Página de "Salud y Nutrición"
    public function index2()
    {
        return view('impactos.index2');
    }

    // Index para Página de "territorios"
    public function index3()
    {
        return view('impactos.index3');
    }

    // Index para Página de "Organizaciones y Movimientos Indígenas"
    public function index4()
    {
        return view('impactos.index4');
    }

    public function introduccion($impacto)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
        }

        return view(
            'impactos.introduccion',
            [
                'impacto' => $impacto,
                'template' => $template,
                'index' => $index
            ]
        );
    }

    public function listaTema($impacto)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/tema';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/tema';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/tema';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/tema';
        }

        $temas = TemaM::join('cntrl_tema', 'id_tema', '=', 'fk_tema')
            ->join('documento', 'id_doc', '=', 'cntrl_tema.fk_doc')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->select('id_tema', 'descripcion')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->groupBy('id_tema')
            ->get();

        return view(
            'impactos.tema',
            [
                'temas' => $temas,
                'impacto' => $impacto,
                'url' => $url,
                'template' => $template,
                'index' => $index
            ]
        );
    }

    public function verTema($impacto, $id)
    {
        //$id = $request->id;  
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/tema';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/tema';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/tema';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/tema';
        }

        $seccion = 'tema';
        $referencia = [];
        $fechas = [];
        $temasDoc = [];
        $poblacionDoc = [];
        $controller = new DocumentoController();

        $tema = TemaM::where('id_tema', $id)->first();

        $documentos = DB::table('documento')
            ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
            ->leftJoin('autor', 'id_autor', '=', 'fk_autor')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('cntrl_tema', 'id_doc', '=', 'cntrl_tema.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->where('fk_tema', '=', $id)
            ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
            ->groupBy('id_doc')
            ->orderByRaw("autores, orden")
            ->get();

        if ($documentos) {
            for ($i = 0; $i < count($documentos); $i++) {
                $documento = DocumentoM::findOrFail($documentos[$i]->id_doc);
                $temas = $documento->tema;
                $poblacion = $documento->poblacion;
                $fechas[$i] = Carbon::parse($documento->fecha_consulta)->format('d/m/Y');
                $referencia[$i] = $controller->tipoReferencia($documento);
                $temasDoc[$i] = $controller->construirReferenciaTemas($temas);
                $poblacionDoc[$i] = $controller->construirReferenciaPoblacion($poblacion);
            }
        }

        return view(
            'impactos.verReferencia',
            [
                'documentos' => $documentos,
                'tema' => $tema,
                'fechas' => $fechas,
                'temasDoc' => $temasDoc,
                'poblacionDoc' => $poblacionDoc,
                'referencia' => $referencia,
                'seccion' => $seccion,
                'url' => $url,
                'impacto' => $impacto,
                'index' => $index,
                'template' => $template
            ]
        );
    }

    public function listaLugar($impacto)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/lugar';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/lugar';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/lugar';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/lugar';
        }

        $ubicaciones = LugarM::join('cntrl_lugar', 'id_lugar', '=', 'fk_lugar')
            ->join('documento', 'id_doc', '=', 'cntrl_lugar.fk_doc')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->where('ubicacion', '!=', '')
            ->select('id_lugar', 'ubicacion')
            ->groupby('id_lugar')
            ->orderBy('ubicacion')
            ->get();

        $paises = DB::table('pais')
            ->join('lugar', 'id_pais', '=', 'pais')
            ->join('cntrl_lugar', 'id_lugar', '=', 'fk_lugar')
            ->join('documento', 'id_doc', '=', 'cntrl_lugar.fk_doc')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->select('id_pais', 'nombre')
            ->groupby('id_pais')
            ->orderBy('nombre')
            ->get();

        $regiones = DB::table('region')
            ->join('lugar', 'id_region', '=', 'region_geografica')
            ->join('cntrl_lugar', 'id_lugar', '=', 'fk_lugar')
            ->join('documento', 'id_doc', '=', 'cntrl_lugar.fk_doc')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->select('id_region', 'nombrereg')
            ->groupby('id_region')
            ->orderBy('nombrereg')
            ->get();

        return view(
            'impactos.lugar',
            [
                'ubicaciones' => $ubicaciones,
                'paises' => $paises,
                'regiones' => $regiones,
                'impacto' => $impacto,
                'url' => $url,
                'template' => $template,
                'index' => $index
            ]
        );
    }

    public function verLugar($impacto, $tipo, $id)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/lugar';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/lugar';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/lugar';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/lugar';
        }

        $seccion = 'lugar';
        $referencia = [];
        $fechas = [];
        $temasDoc = [];
        $poblacionDoc = [];
        $controller = new DocumentoController();

        if ($tipo == 'ubicacion') {
            $documentos = DB::table('documento')
                ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftJoin('autor', 'id_autor', '=', 'fk_autor')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->join('cntrl_lugar', 'id_doc', '=', 'cntrl_lugar.fk_doc')
                ->join('lugar', 'id_lugar', '=', 'fk_lugar')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where('fk_lugar', '=', $id)
                ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
                ->groupBy('id_doc')
                ->orderByRaw("autores, orden")
                ->get();

            $lugar = LugarM::where('id_lugar', $id)->first();
        } elseif ($tipo == 'pais') {
            $documentos = DB::table('documento')
                ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftJoin('autor', 'id_autor', '=', 'fk_autor')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->join('cntrl_lugar', 'id_doc', '=', 'cntrl_lugar.fk_doc')
                ->join('lugar', 'id_lugar', '=', 'fk_lugar')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where('pais', '=', $id)
                ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
                ->groupBy('id_doc')
                ->orderByRaw("autores, orden")
                ->get();

            $lugar = DB::table('pais')->where('id_pais', $id)->select('nombre as ubicacion')->first();
        } elseif ($tipo == 'region') {
            $documentos = DB::table('documento')
                ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftJoin('autor', 'id_autor', '=', 'fk_autor')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->join('cntrl_lugar', 'id_doc', '=', 'cntrl_lugar.fk_doc')
                ->join('lugar', 'id_lugar', '=', 'fk_lugar')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where('region_geografica', '=', $id)
                ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
                ->groupBy('id_doc')
                ->orderByRaw("autores, orden")
                ->get();

            $lugar = DB::table('region')->where('id_region', $id)->select('nombrereg as ubicacion')->first();
        }

        if ($documentos) {
            for ($i = 0; $i < count($documentos); $i++) {
                $documento = DocumentoM::findOrFail($documentos[$i]->id_doc);
                $temas = $documento->tema;
                $poblacion = $documento->poblacion;
                $fechas[$i] = Carbon::parse($documento->fecha_consulta)->format('d/m/Y');
                $referencia[$i] = $controller->tipoReferencia($documento);
                $temasDoc[$i] = $controller->construirReferenciaTemas($temas);
                $poblacionDoc[$i] = $controller->construirReferenciaPoblacion($poblacion);
            }
        }

        return view(
            'impactos.verReferencia',
            [
                'documentos' => $documentos,
                'fechas' => $fechas,
                'temasDoc' => $temasDoc,
                'poblacionDoc' => $poblacionDoc,
                'referencia' => $referencia,
                'seccion' => $seccion,
                'url' => $url,
                'impacto' => $impacto,
                'template' => $template,
                'index' => $index,
                'lugar' => $lugar
            ]
        );
    }

    public function listaTipo($impacto)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/tipo';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/tipo';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/tipo';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/tipo';
        }

        $tipoDocs = DB::table('catalogo_docu')
            ->join('documento', 'id_cata_doc', '=', 'tipo')
            ->join('cntrl_impacto', 'id_doc', '=', 'fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->selectRaw("LTrim( Replace( Replace( Replace( tipo, '18', '2' ) , '14', '17' ) , '15', '8' ) ) AS tipo, LTrim( Replace( Replace( Replace( tipo_doc, 'Artículos de Revista', 'Revistas' ) , 'Artículos de Boletín', 'Boletines' ) , 'Capítulos de Libro', 'Libros' ) ) AS tipo_doc")
            ->distinct()
            ->groupby('catalogo_docu.tipo_doc')
            ->orderBy('tipo_doc')
            ->get();

        return view(
            'impactos.tipoDocumento',
            [
                'tipoDocs' => $tipoDocs,
                'impacto' => $impacto,
                'url' => $url,
                'template' => $template,
                'index' => $index
            ]
        );
    }

    public function verTipo($impacto, $tipo)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/tipo';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/tipo';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/tipo';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/tipo';
        }

        $seccion = 'tipo';
        $referencia = [];
        $fechas = [];
        $temasDoc = [];
        $poblacionDoc = [];
        $controller = new DocumentoController();

        if ($tipo == 'Boletines') {
            $documentos = DB::table('documento')
                ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftJoin('autor', 'id_autor', '=', 'fk_autor')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where(function ($query) {
                    $query->where('tipo', '=', '2')->orWhere('tipo', '=', '18');
                })
                ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
                ->groupBy('id_doc')
                ->orderByRaw("autores, orden")
                ->get();
        } elseif ($tipo == 'Libros') {
            $documentos = DB::table('documento')
                ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftJoin('autor', 'id_autor', '=', 'fk_autor')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where(function ($query) {
                    $query->where('tipo', '=', '8')->orWhere('tipo', '=', '15');
                })
                ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
                ->groupBy('id_doc')
                ->orderByRaw("autores, orden")
                ->get();
        } elseif ($tipo == 'Revistas') {
            $documentos = DB::table('documento')
                ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftJoin('autor', 'id_autor', '=', 'fk_autor')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where(function ($query) {
                    $query->where('tipo', '=', '14')->orWhere('tipo', '=', '17');
                })
                ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
                ->groupBy('id_doc')
                ->orderByRaw("autores, orden")
                ->get();
        } else {
            $documentos = DB::table('documento')
                ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftJoin('autor', 'id_autor', '=', 'fk_autor')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->join('catalogo_docu', 'id_cata_doc', '=', 'tipo')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where('tipo_doc', '=', $tipo)
                ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
                ->groupBy('id_doc')
                ->orderByRaw("autores, orden")
                ->get();
        }

        if ($documentos) {
            for ($i = 0; $i < count($documentos); $i++) {
                $documento = DocumentoM::findOrFail($documentos[$i]->id_doc);
                $temas = $documento->tema;
                $poblacion = $documento->poblacion;
                $fechas[$i] = Carbon::parse($documento->fecha_consulta)->format('d/m/Y');
                $referencia[$i] = $controller->tipoReferencia($documento);
                $temasDoc[$i] = $controller->construirReferenciaTemas($temas);
                $poblacionDoc[$i] = $controller->construirReferenciaPoblacion($poblacion);
            }
        }

        return view(
            'impactos.verReferencia',
            [
                'documentos' => $documentos,
                'fechas' => $fechas,
                'temasDoc' => $temasDoc,
                'poblacionDoc' => $poblacionDoc,
                'referencia' => $referencia,
                'url' => $url,
                'seccion' => $seccion,
                'impacto' => $impacto,
                'tipo' => $tipo,
                'template' => $template,
                'index' => $index
            ]
        );
    }

    public function listaFecha($impacto)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/fecha';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/fecha';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/fecha';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/fecha';
        }

        $sql1 = DB::table('fecha')
            ->join('documento', 'id_doc', '=', 'fecha.fk_doc')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->selectRaw('year(fecha) as anio');

        $sql2 = DB::table('fecha_extra')
            ->join('documento', 'id_doc', '=', 'fecha_extra.fk_doc')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->select('anio')
            ->union($sql1)
            ->distinct('anio')
            ->orderBy('anio')
            ->get();

        return view(
            'impactos.fecha',
            [
                'fechas' => $sql2,
                'impacto' => $impacto,
                'url' => $url,
                'template' => $template,
                'index' => $index
            ]
        );
    }

    public function verFecha($impacto, $fecha)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/fecha';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/fecha';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/fecha';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/fecha';
        }

        $seccion = 'fecha';
        $referencia = [];
        $fechas = [];
        $temasDoc = [];
        $poblacionDoc = [];
        $controller = new DocumentoController();

        $sql1 = DB::table('documento')
            ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
            ->leftJoin('autor', 'id_autor', '=', 'fk_autor')
            ->join('fecha_extra', 'id_doc', '=', 'fecha_extra.fk_doc')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->where('anio', '=', $fecha)
            ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores");

        $documentos = DB::table('documento')
            ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
            ->leftJoin('autor', 'Id_autor', '=', 'fk_autor')
            ->join('fecha', 'id_doc', '=', 'fecha.fk_doc')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->where('fecha', 'like', "%$fecha%")
            ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
            ->groupBy('id_doc')
            ->orderByRaw("autores, orden")
            ->get();

        if ($documentos) {
            for ($i = 0; $i < count($documentos); $i++) {
                $documento = DocumentoM::findOrFail($documentos[$i]->id_doc);
                $temas = $documento->tema;
                $poblacion = $documento->poblacion;
                $fechas[$i] = Carbon::parse($documento->fecha_consulta)->format('d/m/Y');
                $referencia[$i] = $controller->tipoReferencia($documento);
                $temasDoc[$i] = $controller->construirReferenciaTemas($temas);
                $poblacionDoc[$i] = $controller->construirReferenciaPoblacion($poblacion);
            }
        }

        return view(
            'impactos.verReferencia',
            [
                'documentos' => $documentos,
                'fechas' => $fechas,
                'temasDoc' => $temasDoc,
                'poblacionDoc' => $poblacionDoc,
                'referencia' => $referencia,
                'url' => $url,
                'seccion' => $seccion,
                'impacto' => $impacto,
                'template' => $template,
                'index' => $index,
                'fecha' => $fecha
            ]
        );
    }

    public function listaTitulo($impacto)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/titulo';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/titulo';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/titulo';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/titulo';
        }

        return view(
            'impactos.titulo',
            [
                'impacto' => $impacto,
                'url' => $url,
                'template' => $template,
                'index' => $index
            ]
        );
    }

    public function verTitulo($impacto, $id)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/titulo';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/titulo';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/titulo';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/titulo';
        }

        $seccion = 'titulo';
        $referencia = [];
        $fechas = [];
        $temasDoc = [];
        $poblacionDoc = [];
        $controller = new DocumentoController();

        $documentos = DocumentoM::leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
            ->leftJoin('autor', 'Id_autor', '=', 'fk_autor')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->where('id_doc', '=', $id)
            ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
            ->groupBy('id_doc')
            ->orderByRaw("autores, orden")
            ->get();

        if ($documentos) {
            for ($i = 0; $i < count($documentos); $i++) {
                $documento = DocumentoM::findOrFail($documentos[$i]->id_doc);
                $temas = $documento->tema;
                $poblacion = $documento->poblacion;
                $fechas[$i] = Carbon::parse($documento->fecha_consulta)->format('d/m/Y');
                $referencia[$i] = $controller->tipoReferencia($documento);
                $temasDoc[$i] = $controller->construirReferenciaTemas($temas);
                $poblacionDoc[$i] = $controller->construirReferenciaPoblacion($poblacion);
            }
        }

        return view(
            'impactos.verReferencia',
            [
                'documentos' => $documentos,
                'fechas' => $fechas,
                'temasDoc' => $temasDoc,
                'poblacionDoc' => $poblacionDoc,
                'referencia' => $referencia,
                'url' => $url,
                'impacto' => $impacto,
                'template' => $template,
                'index' => $index,
                'seccion' => $seccion
            ]
        );
    }

    public function listaInstitucion($impacto)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/institucion';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/institucion';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/institucion';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/institucion';
        }

        return view(
            'impactos.institucion',
            [
                'impacto' => $impacto,
                'url' => $url,
                'template' => $template,
                'index' => $index
            ]
        );
    }

    public function verInstitucion($impacto, $id)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/institucion';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/institucion';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/institucion';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/institucion';
        }

        $seccion = 'institucion';
        $referencia = [];
        $fechas = [];
        $temasDoc = [];
        $poblacionDoc = [];
        $controller = new DocumentoController();

        $institucion = InstitucionM::where('id_institucion', $id)->first();

        $documentos = DocumentoM::leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
            ->leftJoin('autor', 'Id_autor', '=', 'fk_autor')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('cntrl_instit', 'id_doc', '=', 'cntrl_instit.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->where('fk_instit', '=', $id)
            ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
            ->groupBy('id_doc')
            ->orderByRaw("autores, orden")
            ->get();

        if ($documentos) {
            for ($i = 0; $i < count($documentos); $i++) {
                $documento = DocumentoM::findOrFail($documentos[$i]->id_doc);
                $temas = $documento->tema;
                $poblacion = $documento->poblacion;
                $fechas[$i] = Carbon::parse($documento->fecha_consulta)->format('d/m/Y');
                $referencia[$i] = $controller->tipoReferencia($documento);
                $temasDoc[$i] = $controller->construirReferenciaTemas($temas);
                $poblacionDoc[$i] = $controller->construirReferenciaPoblacion($poblacion);
            }
        }

        return view(
            'impactos.verReferencia',
            [
                'institucion' => InstitucionM::findOrFail($id),
                'institucion' => $institucion,
                'documentos' => $documentos,
                'fechas' => $fechas,
                'temasDoc' => $temasDoc,
                'poblacionDoc' => $poblacionDoc,
                'referencia' => $referencia,
                'url' => $url,
                'seccion' => $seccion,
                'template' => $template,
                'index' => $index,
                'impacto' => $impacto
            ]
        );
    }

    public function listaActor($impacto)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/actor';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/actor';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/actor';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/actor';
        }

        return view(
            'impactos.actor',
            [
                'impacto' => $impacto,
                'template' => $template,
                'index' => $index,
                'url' => $url
            ]
        );
    }

    public function verActor($impacto, $id)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/actor';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/actor';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/actor';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/actor';
        }

        $seccion = 'actor';
        $referencia = [];
        $fechas = [];
        $temasDoc = [];
        $poblacionDoc = [];
        $controller = new DocumentoController();

        $documentos = DocumentoM::leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
            ->leftJoin('autor', 'Id_autor', '=', 'fk_autor')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('cntrl_persona', 'id_doc', '=', 'cntrl_persona.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->where('fk_persona', '=', $id)
            ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
            ->groupBy('id_doc')
            ->orderByRaw("autores, orden")
            ->get();

        if ($documentos) {
            for ($i = 0; $i < count($documentos); $i++) {
                $documento = DocumentoM::findOrFail($documentos[$i]->id_doc);
                $temas = $documento->tema;
                $poblacion = $documento->poblacion;
                $fechas[$i] = Carbon::parse($documento->fecha_consulta)->format('d/m/Y');
                $referencia[$i] = $controller->tipoReferencia($documento);
                $temasDoc[$i] = $controller->construirReferenciaTemas($temas);
                $poblacionDoc[$i] = $controller->construirReferenciaPoblacion($poblacion);
            }
        }

        return view(
            'impactos.verReferencia',
            [
                'persona' => PersonaM::findOrFail($id),
                'documentos' => $documentos,
                'fechas' => $fechas,
                'temasDoc' => $temasDoc,
                'poblacionDoc' => $poblacionDoc,
                'referencia' => $referencia,
                'url' => $url,
                'impacto' => $impacto,
                'template' => $template,
                'index' => $index,
                'seccion' => $seccion,
            ]
        );
    }

    public function listaAutor($impacto)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/autor';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/autor';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/autor';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/autor';
        }

        return view(
            'impactos.autor',
            [
                'impacto' => $impacto,
                'template' => $template,
                'index' => $index,
                'url' => $url
            ]
        );
    }

    public function verAutor($impacto, $id)
    {
        if ($impacto == '1') {
            $template = 'layouts.template1';
            $index = 'trabajoesclavo/index';
            $url = 'trabajoesclavo/autor';
        } elseif ($impacto == '2') {
            $template = 'layouts.template2';
            $index = 'saludynutricion/index';
            $url = 'saludynutricion/autor';
        } elseif ($impacto == '3') {
            $template = 'layouts.template3';
            $index = 'territorios/index';
            $url = 'territorios/autor';
        } elseif ($impacto == '4') {
            $template = 'layouts.template4';
            $index = 'movimientosyorganizaciones/index';
            $url = 'movimientosyorganizaciones/autor';
        }

        $seccion = 'autor';
        $referencia = [];
        $fechas = [];
        $temasDoc = [];
        $poblacionDoc = [];
        $controller = new DocumentoController();

        $autor1 = AutorM::where('id_autor', $id)->first();
        $documentos = DocumentoM::leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
            ->leftJoin('autor', 'Id_autor', '=', 'fk_autor')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->where('fk_autor', '=', $id)
            ->selectRaw("id_doc, titulo,  LTrim (Replace (Replace (Replace (Replace (Replace (Replace (Replace (titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"‘\", \"\" ), \".\", \"\"), \"«\", \"\"), \"#\", \"\")) as orden, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores")
            ->groupBy('id_doc')
            ->orderByRaw("autores, orden")
            ->get();

        if ($documentos) {
            for ($i = 0; $i < count($documentos); $i++) {
                $documento = DocumentoM::findOrFail($documentos[$i]->id_doc);
                $temas = $documento->tema;
                $poblacion = $documento->poblacion;
                $fechas[$i] = Carbon::parse($documento->fecha_consulta)->format('d/m/Y');
                $referencia[$i] = $controller->tipoReferencia($documento);
                $temasDoc[$i] = $controller->construirReferenciaTemas($temas);
                $poblacionDoc[$i] = $controller->construirReferenciaPoblacion($poblacion);
            }
        }

        if ($autor1->pseudonimo != '' && $autor1->apellidos != '' && $autor1->nombre != '') {
            $autor = $autor1->pseudonimo . ' [' . $autor1->nombre . ' ' . $autor1->apellidos . ']';
        } else if ($autor1->pseudonimo == '' && $autor1->apellidos != '' && $autor1->nombre != '') {
            $autor = $autor1->apellidos . ', ' . $autor1->nombre;
        } else if ($autor1->pseudonimo == '' && $autor1->nombre != '' && $autor1->apellidos == '') {
            $autor = $autor1->nombre;
        }

        return view(
            'impactos.verReferencia',
            [
                'autor' => $autor,
                'documentos' => $documentos,
                'fechas' => $fechas,
                'temasDoc' => $temasDoc,
                'poblacionDoc' => $poblacionDoc,
                'referencia' => $referencia,
                'url' => $url,
                'impacto' => $impacto,
                'template' => $template,
                'index' => $index,
                'seccion' => $seccion
            ]
        );
    }

    public function abcTitulo($impacto)
    {
        $variable = '"';
        $var = "'";
        $letras = [];
        for ($i = 65; $i < 91; $i++) {
            $let = chr($i);
            $num = DB::table('documento')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->whereRaw("LTrim( Replace( Replace( Replace (Replace(titulo, \"¿\", \"\" ) , \"¡\",\"\" ) , \"'\", \"\"), \"#\", \"\" ) ) LIKE '$let%'")
                ->count();

            $letras[] = ['let' => $let, 'valor' => $num];
        }

        $documentos = DB::table('documento')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('catalogo_docu', 'id_cata_doc', '=', 'tipo')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->whereRaw("LTrim( Replace( Replace( Replace (Replace(titulo, \"¿\", \"\" ) , \"¡\",\"\" ) , \"'\", \"\"), \"#\", \"\" ) ) LIKE 'A%'")
            ->selectRaw("id_doc, titulo,  LTrim( Replace( Replace( Replace (Replace(titulo, \"¿\", \"\" ) , \"¡\",\"\" ) , \"'\", \"\"), \"#\", \"\" ) ) as orden")
            ->orderBy('orden')
            ->get();

        return response()->json(['abc' => $letras, 'letrasA' => $documentos, 'impacto' => $impacto]);
    }

    public function letTitulo(Request $request)
    {
        $letra = $request->letra;
        $impacto = $request->impacto;
        $documentos = DB::table('documento')
            ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('catalogo_docu', 'id_cata_doc', '=', 'tipo')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->whereRaw("LTrim( Replace( Replace( Replace (Replace(titulo, \"¿\", \"\" ) , \"¡\",\"\" ) , \"'\", \"\"), \"#\", \"\" ) ) LIKE '$letra%'")
            ->selectRaw("id_doc, titulo,  LTrim( Replace( Replace( Replace (Replace(titulo, \"¿\", \"\" ) , \"¡\",\"\" ) , \"'\", \"\"), \"#\", \"\" ) ) as orden")
            ->orderBy('orden')
            ->get();

        return $documentos;
    }

    public function abcInstitucion($impacto)
    {
        $letras = [];
        for ($i = 65; $i < 91; $i++) {
            $let = chr($i);
            $num = DocumentoM::join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->join('cntrl_instit', 'id_doc', '=', 'cntrl_instit.fk_doc')
                ->join('institucion', 'id_institucion', '=', 'fk_instit')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->whereRaw("LTrim (Replace(Replace(Replace(institucion.nombre,\"¿\", ''),\"¡\", ''),\"'\", '')) LIKE '$let%'")
                ->count();
            $letras[] = ['let' => $let, 'valor' => $num];
        }

        $instituciones = DocumentoM::join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('cntrl_instit', 'id_doc', '=', 'cntrl_instit.fk_doc')
            ->join('institucion', 'id_institucion', '=', 'fk_instit')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->whereRaw("LTrim (Replace(Replace(Replace(nombre,\"¿\", ''),\"¡\", ''),\"'\", '')) LIKE 'A%'")
            ->select('id_institucion', 'nombre')
            ->orderByRaw("LTrim( Replace( Replace( Replace(nombre, \"¿\", \"\" ) , \"¡\",\"\" ) , \"'\", \"\" ) )")
            ->groupby('id_institucion')
            ->get();

        return response()->json(['abc' => $letras, 'letrasA' => $instituciones]);
    }

    public function letInstitucion(Request $request)
    {
        $letra = $request->letra;
        $impacto = $request->impacto;
        $instituciones = DocumentoM::join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('cntrl_instit', 'id_doc', '=', 'cntrl_instit.fk_doc')
            ->join('institucion', 'id_institucion', '=', 'fk_instit')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->whereRaw("LTrim (Replace(Replace(Replace(nombre,\"¿\", ''), \"¡\", ''), \"'\", '')) LIKE '$letra%'")
            ->select('id_institucion', 'nombre')
            ->orderByRaw("LTrim( Replace( Replace( Replace(nombre, \"¿\", \"\" ) , \"¡\",\"\" ) , \"'\", \"\" ) )")
            ->groupby('id_institucion')
            ->get();

        return $instituciones;
    }

    public function abcActor($impacto)
    {
        $letras = [];
        for ($i = 65; $i < 91; $i++) {
            $let = chr($i);
            $num = DocumentoM::join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->join('cntrl_persona', 'id_doc', '=', 'cntrl_persona.fk_doc')
                ->join('persona', 'id_persona', '=', 'fk_persona')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where('apellidos', 'LIKE', "$let%")
                ->count();
            $letras[] = ['let' => $let, 'valor' => $num];
        }

        $actores = [];
        $lista = DocumentoM::join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('cntrl_persona', 'id_doc', '=', 'cntrl_persona.fk_doc')
            ->join('persona', 'id_persona', '=', 'fk_persona')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->where('apellidos', 'LIKE', "A%")
            ->selectRaw('id_persona, apellidos, nombre, CONCAT(apellidos, nombre) as personas, cargo')
            ->orderBy('personas')
            ->groupby('id_persona')
            ->get();

        foreach ($lista as $item) {
            if ($item->cargo != "") {
                $actores[] = ['id_persona' => $item->id_persona, 'nombre' => ($item->apellidos . ', ' . $item->nombre . ' [' . $item->cargo . ']')];
            } else {
                $actores[] = ['id_persona' => $item->id_persona, 'nombre' => ($item->apellidos . ', ' . $item->nombre)];
            }
        }

        return response()->json(['abc' => $letras, 'letrasA' => $actores]);
    }

    public function letActor(Request $request)
    {
        $letra = $request->letra;
        $impacto = $request->impacto;
        $actores = [];
        $lista = DocumentoM::join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('cntrl_persona', 'id_doc', '=', 'cntrl_persona.fk_doc')
            ->join('persona', 'id_persona', '=', 'fk_persona')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->where('apellidos', 'LIKE', "$letra%")
            ->selectRaw('id_persona, apellidos, nombre, CONCAT(apellidos, nombre) as personas, cargo')
            ->orderBy('personas')
            ->groupby('id_persona')
            ->get();

        foreach ($lista as $item) {
            if ($item->cargo != "") {
                $actores[] = ['id_persona' => $item->id_persona, 'nombre' => ($item->apellidos . ', ' . $item->nombre . ' [' . $item->cargo . ']')];
            } else {
                $actores[] = ['id_persona' => $item->id_persona, 'nombre' => ($item->apellidos . ', ' . $item->nombre)];
            }
        }

        return $actores;
    }

    public function abcAutor($impacto)
    {
        $letras = [];
        $autores = [];
        for ($i = 65; $i < 91; $i++) {
            $let = chr($i);
            $num = DocumentoM::join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->join('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->join('autor', 'id_autor', '=', 'fk_autor')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->whereRaw('CONCAT(pseudonimo,apellidos,nombre) LIKE ?', "$let%")
                ->count();
            $letras[] = ['let' => $let, 'valor' => $num];
        }

        $autores = [];
        $lista = DocumentoM::join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
            ->join('autor', 'id_autor', '=', 'fk_autor')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->whereRaw('CONCAT(pseudonimo,apellidos,nombre) LIKE ?', "A%")
            ->selectRaw('id_autor, pseudonimo, apellidos, nombre, CONCAT(pseudonimo,apellidos,nombre) as autores')
            ->orderBy('autores')
            ->groupby('id_autor')
            ->get();

        foreach ($lista as $item) {
            if ($item->pseudonimo != '' && $item->apellidos != '' && $item->nombre != '') {
                $autores[] = ['id_autor' => $item->id_autor, 'nombre' => ($item->pseudonimo . ' [' . $item->nombre . ' ' . $item->apellidos . ']')];
            } else if ($item->pseudonimo == '' && $item->apellidos != '' && $item->nombre != '') {
                $autores[] = ['id_autor' => $item->id_autor, 'nombre' => ($item->apellidos . ', ' . $item->nombre)];
            } else if ($item->pseudonimo == '' && $item->nombre != '' && $item->apellidos == '') {
                $autores[] = ['id_autor' => $item->id_autor, 'nombre' => $item->nombre];
            }
        }

        return response()->json(['abc' => $letras, 'letrasA' => $autores]);
    }

    public function letAutor(Request $request)
    {
        $letra = $request->letra;
        $impacto = $request->impacto;
        $autores = [];
        $lista = DocumentoM::join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
            ->join('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
            ->join('autor', 'id_autor', '=', 'fk_autor')
            ->where('linea', '=', '1')
            ->where('fk_impacto', '=', $impacto)
            ->whereRaw('CONCAT(pseudonimo,apellidos,nombre) LIKE ?', "$letra%")
            ->selectRaw('id_autor, pseudonimo, apellidos, nombre, CONCAT(pseudonimo, apellidos, nombre) as autores')
            ->orderBy('autores')
            ->groupby('id_autor')
            ->get();

        foreach ($lista as $item) {
            if ($item->pseudonimo != '' && $item->apellidos != '' && $item->nombre != '') {
                $autores[] = ['id_autor' => $item->id_autor, 'nombre' => ($item->pseudonimo . ' [' . $item->nombre . ' ' . $item->apellidos . ']')];
            } else if ($item->pseudonimo == '' && $item->apellidos != '' && $item->nombre != '') {
                $autores[] = ['id_autor' => $item->id_autor, 'nombre' => ($item->apellidos . ', ' . $item->nombre)];
            } else if ($item->pseudonimo == '' && $item->nombre != '' && $item->apellidos == '') {
                $autores[] = ['id_autor' => $item->id_autor, 'nombre' => $item->nombre];
            }
        }
        return $autores;
    }

    public function buscador(Request $request)
    {
        $impacto = $request->impacto;
        $input = $request->search_input;

        if ($impacto == '1') {
            $url = 'trabajoesclavo/buscador';
            $index = 'trabajoesclavo/index';
            $template = 'layouts.template1';
        } elseif ($impacto == '2') {
            $url = 'saludynutricion/buscador';
            $index = 'saludynutricion/index';
            $template = 'layouts.template2';
        } elseif ($impacto == '3') {
            $url = 'territorios/buscador';
            $index = 'territorios/index';
            $template = 'layouts.template3';
        } elseif ($impacto == '4') {
            $url = 'movimientosyorganizaciones/buscador';
            $index = 'movimientosyorganizaciones/index';
            $template = 'layouts.template4';
        }

        $referencia = [];
        $fechas = [];
        $temasDoc = [];
        $subtemasDoc = [];
        $poblacionDoc = [];
        $controller = new DocumentoController();
        $seccion = 'buscador';
        $documentos = [];

        if ($input != '') {
            $fechaExtra = DB::table('documento')
                ->leftJoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftJoin('autor', 'id_autor', '=', 'fk_autor')
                ->join('fecha_extra', 'id_doc', '=', 'fecha_extra.fk_doc')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where('anio', '=', $input)
                ->selectRaw("id_doc, titulo, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(apellidos, nombre) as autores");

            $lugares = DB::table('documento')
                ->leftjoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftjoin('autor', 'id_autor', '=', 'fk_autor')
                ->leftjoin('cntrl_lugar', 'id_doc', '=', 'cntrl_lugar.fk_doc')
                ->leftjoin('lugar', 'id_lugar', '=', 'fk_lugar')
                ->leftjoin('pais', 'id_pais', '=', 'pais')
                ->leftjoin('region', 'id_region', '=', 'region_geografica')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where(function ($query) use ($input) {
                    $query = $query->orWhere('lugar.ubicacion', 'like', "%$input%");
                    $query = $query->orWhere('pais.nombre', 'like', "%$input%");
                    $query = $query->orWhere('region.nombrereg', 'like', "%$input%");
                });

            $lugares = $lugares->selectRaw("id_doc, titulo, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(autor.apellidos, autor.nombre) as autores")
                ->groupby('id_doc');

            $instituciones = DB::table('documento')
                ->leftjoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftjoin('autor', 'id_autor', '=', 'fk_autor')
                ->leftjoin('cntrl_instit', 'id_doc', '=', 'cntrl_instit.fk_doc')
                ->leftjoin('institucion', 'id_institucion', '=', 'fk_instit')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where(function ($query) use ($input) {
                    $query = $query->orWhere('institucion.nombre', 'like', "%$input%");
                    $query = $query->orWhere('institucion.siglas', 'like', "%$input%");
                });

            $instituciones = $instituciones->selectRaw("id_doc, titulo, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(autor.apellidos, autor.nombre) as autores")
                ->groupby('id_doc');

            $personas = DB::table('documento')
                ->leftjoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftjoin('autor', 'id_autor', '=', 'fk_autor')
                ->leftjoin('cntrl_persona', 'id_doc', '=', 'cntrl_persona.fk_doc')
                ->leftjoin('persona', 'id_persona', '=', 'fk_persona')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where(function ($query) use ($input) {
                    $query = $query->orWhere('persona.nombre', 'like', "%$input%");
                    $query = $query->orWhere('persona.apellidos', 'like', "%$input%");
                    $query = $query->orWhere('persona.cargo', 'like', "%$input%");
                });

            $personas = $personas->selectRaw("id_doc, titulo, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(autor.apellidos, autor.nombre) as autores")
                ->groupby('id_doc');

            $subtemas = DB::table('documento')
                ->leftjoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftjoin('autor', 'id_autor', '=', 'fk_autor')
                ->leftjoin('cntrl_sub', 'id_doc', '=', 'cntrl_sub.fk_doc')
                ->leftjoin('subtema', 'id_sub', '=', 'fk_sub')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where('subtema', 'like', "%$input%")
                ->selectRaw("id_doc, titulo, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(autor.apellidos, autor.nombre) as autores")
                ->groupby('id_doc');

            $documentos = DB::table('documento')
                ->leftjoin('cntrl_autor', 'id_doc', '=', 'cntrl_autor.fk_doc')
                ->leftjoin('autor', 'id_autor', '=', 'fk_autor')
                ->join('cntrl_impacto', 'id_doc', '=', 'cntrl_impacto.fk_doc')
                ->join('cntrl_tema', 'id_doc', '=', 'cntrl_tema.fk_doc')
                ->join('tema', 'id_tema', '=', 'fk_tema')
                ->join('fecha', 'id_doc', '=', 'fecha.fk_doc')
                ->where('linea', '=', '1')
                ->where('fk_impacto', '=', $impacto)
                ->where(function ($query) use ($input) {
                    $query = $query->orWhere('titulo', 'like', "%$input%");
                    $query = $query->orWhere('descripcion', 'like', "%$input%");
                    $query = $query->orWhere('autor.nombre', 'like', "%$input%");
                    $query = $query->orWhere('autor.apellidos', 'like', "%$input%");
                    $query = $query->orWhere('fecha', 'like', "%$input%");
                });

            $documentos = $documentos->selectRaw("id_doc, LTrim( Replace( Replace( Replace (Replace(titulo, \"¿\", \"\" ), \"¡\",\"\" ), \"'\", \"\"), \"#\", \"\" ) ) as titulo, lugar_public_pais, lugar_public_edo, derecho_autor, fecha_publi, url, fecha_consulta, poblacion, tipo, CONCAT(autor.apellidos, autor.nombre) as autores")
                ->union($fechaExtra)
                ->union($lugares)
                ->union($instituciones)
                ->union($personas)
                ->union($subtemas)
                ->groupby('id_doc')
                ->orderByRaw("autores, titulo")
                ->get();

            if ($documentos) {
                for ($i = 0; $i < count($documentos); $i++) {
                    $documento = DocumentoM::findOrFail($documentos[$i]->id_doc);
                    $temas = $documento->tema;
                    $poblacion = $documento->poblacion;
                    $fechas[$i] = Carbon::parse($documento->fecha_consulta)->format('d/m/Y');
                    $referencia[$i] = $controller->tipoReferencia($documento);
                    $temasDoc[$i] = $controller->construirReferenciaTemas($temas);
                    $poblacionDoc[$i] = $controller->construirReferenciaPoblacion($poblacion);
                    $subtemasDoc[$i] = $controller->construirReferenciaSubtemas($documentos[$i]->id_doc, $input);
                }
            }
        }

        return view(
            'impactos.verReferencia',
            [
                'documentos' => $documentos,
                'fechas' => $fechas,
                'temasDoc' => $temasDoc,
                'poblacionDoc' => $poblacionDoc,
                'subtemasDoc' => $subtemasDoc,
                'referencia' => $referencia,
                'seccion' => $seccion,
                'url' => $url,
                'impacto' => $impacto,
                'template' => $template,
                'index' => $index
            ]
        )->with('input', $input);
    }

    public function verCreditos($impacto)
    {
        if ($impacto == '1') {
            $index = 'trabajoesclavo/index';
            $template = 'layouts.template1';
        } elseif ($impacto == '2') {
            $index = 'saludynutricion/index';
            $template = 'layouts.template2';
        } elseif ($impacto == '3') {
            $index = 'territorios/index';
            $template = 'layouts.template3';
        } elseif ($impacto == '4') {
            $index = 'movimientosyorganizaciones/index';
            $template = 'layouts.template4';
        }

        return view(
            'impactos.creditos',
            [
                'index' => $index,
                'template' => $template
            ]
        );
    }
}
