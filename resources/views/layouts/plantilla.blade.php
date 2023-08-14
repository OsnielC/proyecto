<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous">
    </script>
    <title>Empresas Eoloeléctricas y población local de América Latina</title>
</head>

<body>
    <header class="header_area white-header">
        <div class="main_menu">
            <div class="search_input" id="search_input_box" style="display: none;">
                <div class="container">
                    <form method="POST" action="https://www.imezinal.unam.mx/saludynutricion/buscador/2"
                        accept-charset="UTF-8" class="d-flex justify-content-between"><input name="_token"
                            type="hidden" value="L6WapIm0CBoh33oXJsU57n7nTzvWW62BhWvx2I2k">
                        <input name="_token" type="hidden" value="L6WapIm0CBoh33oXJsU57n7nTzvWW62BhWvx2I2k">
                        <input type="text" class="form-control" id="search_input" name="search_input"
                            placeholder="Buscar en el sitio">
                        <input type="hidden" id="impacto" name="impacto" value="2">
                        <button type="submit" class="btn"></button>
                        <span class="ti-close" id="close_search" title="Cerrar búsqueda"></span>
                    </form>
                </div>
            </div>

            <nav class="navbar navbar-expand-lg navbar-light">
                <div class="container">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse offset" id="navbarSupportedContent">
                        <ul class="nav navbar-nav menu_nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="https://www.imezinal.unam.mx/saludynutricion/index">Inicio</a>
                            </li>
                            <li class="nav-item active">
                                <a class="nav-link" href="https://www.imezinal.unam.mx/saludynutricion/tema/2">Tema</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link"
                                    href="https://www.imezinal.unam.mx/saludynutricion/lugar/2">Lugar</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link" href="https://www.imezinal.unam.mx/saludynutricion/tipo/2">Tipo de
                                    Documento</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link"
                                    href="https://www.imezinal.unam.mx/saludynutricion/fecha/2">Fecha</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link"
                                    href="https://www.imezinal.unam.mx/saludynutricion/titulo/2">Título</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link"
                                    href="https://www.imezinal.unam.mx/saludynutricion/institucion/2">Institución</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link" href="https://www.imezinal.unam.mx/saludynutricion/actor/2">Actor
                                    Social</a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link"
                                    href="https://www.imezinal.unam.mx/saludynutricion/autor/2">Autor</a>
                            </li>
                            <li class="nav-item">
                                <a href="#" class="nav-link search" id="search">
                                    <i class="ti-search"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>


    <section class="banner_area">
        <div class="banner_inner d-flex align-items-center">
            <div class="overlay"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12">
                        <div class="banner_content text-center">
                            <h2>Empresas Eoloeléctricas y población local de América Latina</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @yield('content')
</body>

<footer class="footer-area section_gap_top">
    <div class="container">
        <div class="row footer-bottom d-flex">
            <div class="col-lg-4 col-sm-12 text-center">
                <a href="https://www.unam.mx" target="_blank">
                    <img class="images" src="https://www.imezinal.unam.mx/assets/img/unam.png" alt="" width="100px" height="95">
                </a>
                <a href="https://www.nacionmulticultural.unam.mx/" target="_blank">
                    <img class="images" src="https://www.imezinal.unam.mx/assets/img/puic.png" alt="" width="130px" height="95">
                </a>
            </div>
            <div class="col-lg-8 col-md-8 col-sm-12 footer-text m-0 text-white">
                <div class="single-footer-widget ">
                    <ul><li><a href="https://www.imezinal.unam.mx/saludynutricion/creditos/2">Créditos</a></li></ul>
                </div>
                <p>
                    D.R. © 2019 Universidad Nacional Autónoma de México.<br>
                    Programa Universitario de Estudios de la Diversidad Cultural y la Interculturalidad.<br>
                    Esta página puede ser reproducida con fines no lucrativos, siempre y cuando no se mutile, se cite la fuente completa y su dirección electrónica.<br>
                    De otra forma requiere permiso previo por escrito de la institución.
                </p>
    <p>Última actualización: Abril del 2022.</p><br>                        
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 single-footer-widget text-left">
                        <div class="item_icon">
                            <i class="ti-location-arrow"></i> 
                        </div>
                        <div class="widget_ab_item_text">
                            <strong>Sede Ciudad de México</strong><br>
                            Av. Río de la Magdalena # 100, Col. La Otra Banda, C.P. 01090, Del. Álvaro Obregón, Ciudad de México.    
                        </div>
                        
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 single-footer-widget text-left">
                        <div class="item_icon">
                            <i class="ti-location-arrow"></i> 
                        </div>
                        <div class="widget_ab_item_text">
                            <strong>Oficina Oaxaca</strong><br>
                            Alameda de León # 2 Altos, Centro Oaxaca de Juárez, C.P. 68000, Oaxaca, Oax.
                        </div>
                    </div>   
                </div> 
            </div>
        </div>
    </div>
</footer>
</html>
