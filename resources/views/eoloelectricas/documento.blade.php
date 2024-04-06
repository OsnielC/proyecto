@extends('layouts.eoloelectricas')

@section('content')
    <h1>Tipos de documentos</h1>
    <div class="content">
        <table class="table table-borderless md" data-effect="fade">
            <thead>
                <tr></tr>
            </thead>
            <tbody>
            @foreach ($tipoDocs = DB::table('catalogo_docu')->orderBy('tipo_doc')->get() as $element)
                <tr>
                    <td>
                        <a class="lista">
                            @if($element->tipo_doc == 'Boletines')
                                {{ $element->tipo_doc }} y Artículos de Boletín
                            @elseif($element->tipo_doc == 'Revistas')
                                {{ $element->tipo_doc }} y Artículos de Revista
                            @elseif($element->tipo_doc == 'Libros')
                                {{ $element->tipo_doc }} y Capítulos de Libro
                            @else
                                {{ $element->tipo_doc }}
                            @endif
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>    
    </div>
@endsection