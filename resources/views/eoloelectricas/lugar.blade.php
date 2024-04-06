@extends('layouts.eoloelectricas')

@section('content')
    <h1>Lugar</h1>
    <section class="course_details_area section_gap">
        <div class="container">
            <div class="content">
                <ul class="nav nav-tabs" id="myTab">
                    <li class="nav-item"><a class="nav-link active" href="#ubicacion" data-toggle="tab"><i
                                class="fa fa-globe"></i> Localidad / Estado / Provincia</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pais" data-toggle="tab"><i class="fa fa-flag"></i>
                            País </a></li>
                    <li class="nav-item"><a class="nav-link" href="#region" data-toggle="tab"><i
                                class="fa fa-map-marker"></i> Región Geográfica </a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="ubicacion"> <!-- Ubicación  -->
                        <div class="row">
                            <div class="col-lg-12"><br>
                                <table id="ubicaciones" class="table table-borderless md" data-effect="fade">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Localidad / Estado / Provincia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ubicaciones= DB::table('lugar')->where(function($query) {
                                            $query->whereNotNull('ubicacion')->where('ubicacion', '!=', '');
                                        })->orderBy('ubicacion')->get() as $element)
                                            <tr>
                                                <td>
                                                    <a class="lista"
                                                        >
                                                        {{ $element->ubicacion }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="pais"> <!-- País  -->
                        <div class="row">
                            <div class="col-lg-12"><br>
                                <table id="paises" class="table table-borderless md">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>País</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ubicaciones= DB::table('paises')->where(function($query) {
                                            $query->whereNotNull('nombre')->where('nombre', '!=', '');
                                        })->orderBy('nombre')->get() as $element)
                                            <tr>
                                                <td>
                                                    <a class="lista">
                                                        {{ $element->nombre }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="region"> <!-- Región Geográfica  -->
                        <div class="row">
                            <div class="col-lg-12"><br>
                                <table class="table table-borderless md">
                                    <thead></thead>
                                    <tbody>
                                        @foreach ($regiones= DB::table('region')->get(); as $element)
                                            <tr>
                                                <td>
                                                    <a class="lista">
                                                        {{ $element->nombrereg }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
