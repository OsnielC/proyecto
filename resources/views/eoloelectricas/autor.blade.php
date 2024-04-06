@extends('layouts.eoloelectricas')

@section('content')
    <h1>Autor</h1>
    <div class="content">
        <table class="table table-borderless md" data-effect="fade">
            <thead><tr></tr></thead>
            
            <tbody>
                @foreach ($ubicaciones= DB::table('autor')->where(function($query) {
                    $query->whereNotNull('nombre')->where('nombre', '!=', '');
                })->orderBy('nombre')->get() as $element)
                <tr>
                    <td>
                        <a>
                            {{ $element->nombre }}, {{ $element->apellidos }}
                        </a>    
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>  
@endsection