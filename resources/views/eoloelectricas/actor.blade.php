@extends('layouts.eoloelectricas')

@section('content')
    <h1>Actor</h1>
    <div class="content">
        <table class="table table-borderless md" data-effect="fade">
            <thead><tr></tr></thead>
            
            <tbody>
                @foreach ($persona = DB::table('persona')->get();
                as $element)
                <tr>
                    <td>
                        <a>
                            {{ $element->nombre }}, {{ $element->cargo }}
                        </a>    
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>  
@endsection