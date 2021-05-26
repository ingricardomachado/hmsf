<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cejas Perfectas | 404 Error</title>

    <link href="{{ asset("css/bootstrap.min.css") }}" rel="stylesheet">
    <link href="{{ asset("fonts/css/font-awesome.min.css") }}" rel="stylesheet">
    <link href="{{ asset("css/animate.css") }}" rel="stylesheet">
    <link href="{{ asset("css/style.css") }}" rel="stylesheet">
    
</head>

<body class="gray-bg">


    <div class="middle-box text-center animated fadeInDown">
        <img src="{{ URL::to('img/logo_company.png') }}" style="max-height:120px; max-width:120px;">
        <h1>404</h1>
        <h3 class="font-bold">Página no encontrada</h3>

        <div class="error-desc">
            Disculpe, pero la pagina no fue encontrada.<br> 
            Verifique la dirección e intente nuevamente.
        </div>
        <div>
            <small><strong>Copyright</strong> {{ Session::get('company_name') }} &copy; 2019</small>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="{{ asset("js/jquery-2.1.1.js") }}"></script>
    <script src="{{ asset("js/bootstrap.min.js") }}"></script>

</body>

</html>
