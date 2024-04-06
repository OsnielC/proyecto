@extends('layouts.eoloelectricas')

@section('content')
    <h1>Institución</h1>
    <div class="content">
        <table class="table table-borderless md" data-effect="fade">
            <thead><tr></tr></thead>
            
            <tbody>
                @foreach ($institucion = DB::table('institucion')->get();
                as $element)
                <tr>
                    <td>
                        <a>
                            {{ $element->nombre}}
                        </a>    
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>  
@endsection