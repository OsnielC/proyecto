@extends('layouts.eoloelectricas')

@section('content')
    <h1>Título</h1>
    <div class="content">
        <table class="table table-borderless md" data-effect="fade">
            <thead><tr></tr></thead>
            
            <tbody>
                @foreach ($titulos = DB::table('documento')->get();
                as $element)
                <tr>
                    <td>
                        <a>
                            {{ $element->titulo}}
                        </a>    
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>  
@endsection