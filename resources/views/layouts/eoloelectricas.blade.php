<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Empresas Eoloeléctricas y población local de América Latina</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo03"
                aria-controls="navbarTogglerDemo03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#">LOGO</a>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo03">
                <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <li class="{{ Request::is('eoloelectricas/inicio*') ? 'active' :''}} nav-item">
                        <a class="nav-link" href="#">Inicio<span class="sr-only"></span></a>
                    </li>
                    <li class="{{ Request::is('eoloelectricas/tema*') ? 'active' :''}} nav-item">
                        <a class="nav-link" href="../eoloelectricas/tema">Tema</a>
                    </li>
                    <li class="{{ Request::is('eoloelectricas/lugar*') ? 'active' :''}} nav-item">
                        <a class="nav-link" href="../eoloelectricas/lugar">Lugar</a>
                    </li>
                    <li class="{{ Request::is('eoloelectricas/documento*') ? 'active' :''}} nav-item">
                        <a class="nav-link" href="../eoloelectricas/documento">Tipo de documento</a>
                    </li>
                    <li class="{{ Request::is('eoloelectricas/fecha*') ? 'active' :''}} nav-item">
                        <a class="nav-link" href="../eoloelectricas/fecha">Fecha</a>
                    </li>
                    <li class="{{ Request::is('eoloelectricas/titulo*') ? 'active' :''}} nav-item">
                        <a class="nav-link" href="../eoloelectricas/titulo">Título</a>
                    </li>
                    <li class="{{ Request::is('eoloelectricas/institucion*') ? 'active' :''}} nav-item">
                        <a class="nav-link" href="../eoloelectricas/institucion">Institución</a>
                    </li>
                    <li class="{{ Request::is('eoloelectricas/actor*') ? 'active' :''}} nav-item">
                        <a class="nav-link" href="../eoloelectricas/actor">Actor social</a>
                    </li>
                    <li class="{{ Request::is('eoloelectricas/autor*') ? 'active' :''}} nav-item">
                        <a class="nav-link" href="../eoloelectricas/autor">Autor</a>
                    </li>
                </ul>
                <form action="https://www.google.com/search" method="get" class="search-bar form-inline my-2 my-lg-0">
                    <input class="search-text" type="text" placeholder="Buscar..." name="q">
                    <button type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>
        </nav>

        <!-- Background image -->
        <div id="intro-example" class="text-center bg-image">
            <div class="mask">
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="text-white">
                        <h2 class="titulo">Empresas Eoloeléctricas y población local de América Latina</h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- Background image -->
    </header>
    @yield('content')

    <footer class="py-2 pt-4">
        <div class="container">
            <div class="row">
                <div class="col-sm d-flex justify-content-center align-items-center mb-3"">
                    <a class="m-auto text-center" href="https://www.unam.mx" target="_blank">
                        <img class="images" src="https://www.imezinal.unam.mx/assets/img/unam.png" alt=""
                            width="100px" height="95">
                    </a>
                    <a class="m-auto text-center" href="https://www.nacionmulticultural.unam.mx/" target="_blank">
                        <img class="images" src="https://www.imezinal.unam.mx/assets/img/puic.png" alt=""
                            width="100px" height="95">
                    </a>
                </div>

                <div class="col-sm">
                    <h5>Sede Ciudad de México</h5>
                    <p>Av. Río de la Magdalena # 100, Col. La Otra Banda, C.P. 01090, Del. Álvaro Obregón, Ciudad de
                        México.</p>
                </div>

                <div class="col-sm">
                    <h5>Oficina Oaxaca</h5>
                    <p>Alameda de León # 2 Altos, Centro Oaxaca de Juárez, C.P. 68000, Oaxaca, Oax.</p>
                </div>
            </div>

            <div class="flex-sm-row justify-content-between py-1 my-1 border-top">
                <h5>Créditos</h5>
                <p>D.R. © 2019 Universidad Nacional Autónoma de México. Programa Universitario de Estudios de la
                    Diversidad Cultural y la Interculturalidad. Esta página puede ser reproducida con fines no
                    lucrativos, siempre y cuando no se mutile, se cite la fuente completa y su dirección electrónica. De
                    otra forma requiere permiso previo por escrito de la institución.</p>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>
