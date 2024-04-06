@extends('layouts.eoloelectricas')

@section('content')
    <h1>Fecha</h1>
    <div class="content">
        <table class="table table-borderless md" data-effect="fade">
            <thead><tr></tr></thead>
        
            <tbody>
                @foreach ($fechas= DB::table('fecha')->get(); as $element)
                <tr>
                    <td>
                    @if($element->fecha == 0)
                        <a class="lista">
                            [s.f.]
                        </a>
                    @else
                        <a class="lista">
                            {{ $element->fecha }}
                        </a>
                    @endif    
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection