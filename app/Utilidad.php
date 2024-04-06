<?php

namespace App;

use DB;
use Illuminate\Support\Facades\Log;

class Utilidad
{
    public static function getFecha($fecha)
    {
        $dia = substr($fecha,8,2);
        $mes = substr($fecha,5,2);
        $anio = substr($fecha,0,4);
        
        switch ($mes) {
            case 1:
                $mes = "enero";
            break;
            
            case 2:
                $mes = "febrero";
            break;
            
            case 3:
                $mes = "marzo";
            break;
            
            case 4;
                $mes = "abril";
            break;
            
            case 5;
                $mes = "mayo";
            break;
            
            case 6;
                $mes = "junio";
            break;
            
            case 7;
                $mes = "julio";
            break;
            
            case 8;
                $mes = "agosto";
            break;
            
            case 9;
                $mes = "septiembre";
            break;
            
            case 10;
                $mes = "octubre";
            break;
            
            case 11;
                $mes = "noviembre";
            break;
            
            case 12;
                $mes = "diciembre";
            break;
        }

        $fechaRetornada = "";
            /*Obteniendo cadena fecha Fecha*/
        $fechaRetornada = $dia.' de '.$mes.' de '.$anio;
        
        if($dia == "00"){
            $fechaRetornada = $mes.' de '.$anio.'';
        }       
        if($mes == "00"){
            $fechaRetornada = $anio.'';   
        }
        if($anio == "00"){
            $fechaRetornada = '[s.f.]';  
        }
             
        return $fechaRetornada;
    }

    public static function getFechaConsulta($fecha)
    {
        $dia = substr($fecha,8,2);
        $mes = substr($fecha,5,2);
        $anio = substr($fecha,0,4);
        
        return  $dia.'/'.$mes.'/'.$anio.'.';
    }
    
}