@extends('layouts.eoloelectricas')

@section('content')
<h1>Temas</h1>
<div class="content">
    <table class="table table-borderless md" data-effect="fade">
        <thead><tr></tr></thead>
        
        <tbody>
            @foreach ($temas = DB::table('temas')->get();
            as $element)
            <tr>
                <td>
                    <a href="">
                        {{ $element->descripcion }}
                    </a>    
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>  
@endsection