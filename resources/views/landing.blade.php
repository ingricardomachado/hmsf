<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Add Your favicon here -->
    <link rel="shortcut icon" href="{{ asset("img/app_ico.ico") }}" />
    <!--<link rel="icon" href="img/favicon.ico">-->
    <title> SmartCond | Tu Condominio Inteligente </title>

    <!-- Bootstrap core CSS -->
    <link href="{{ URL::asset('landing/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Animation CSS -->
    <link href="{{ URL::asset('landing/css/animate.min.css') }}" rel="stylesheet">
    
    <link href="{{ URL::asset('landing/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">    
    <!-- iCheck (customiza los checkbox con un skin predefinido)-->
    <link href="{{ URL::asset('css/plugins/iCheck/skins/flat/green.css') }}" rel="stylesheet">
    <!-- Select2 -->
    <link href="{{ URL::asset('js/plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <!-- *** Custom styles for this template *** -->
    <link href="{{ URL::asset('landing/css/style.css') }}" rel="stylesheet">
    <!-- International Phones -->
    <link href="{{ URL::asset('js/plugins/intl-tel-input-master/build/css/intlTelInput.css') }}" rel="stylesheet">
    <!-- Esta instruccion es para que input del cell sea 100% width-->
    <style type="text/css">
        .iti { width: 100%; }
    </style>
</head>
<body id="page-top">
<div class="navbar-wrapper">
        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container">
                <div class="navbar-header page-scroll">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#contact">Registrate gratis YA!</a>
                </div>
                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a class="page-scroll" href="#page-top">Inicio</a></li>
                        <li><a class="page-scroll" href="#features">Características</a></li>
                        <li><a class="page-scroll" href="#pricing">Planes</a></li>
                        <li><a class="page-scroll" href="#contact">Contáctanos</a></li>
                        <li><a href="{{ route('home') }}"><i class="fa fa-sign-in" aria-hidden="true"></i> Entrar</a></li>
                    </ul>
                </div>
            </div>
        </nav>
</div>
<div id="inSlider" class="carousel carousel-fade" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#inSlider" data-slide-to="0" class="active"></li>
        <li data-target="#inSlider" data-slide-to="1"></li>
    </ol>
    <div class="carousel-inner" role="listbox">
        <div class="item active">
            <div class="container">
                <div class="carousel-caption">
                    <h2 style="font:24px">Smart<b>Cond</b></h2>
                    <h2>Tu Condominio Inteligente<br/>
                        desde tu Móvil,<br/>
                        Tablet o Laptop<h2/>
                    <h4><b>Gestiona el día a día de tu condominio fácil y rápido...</b></h4>
                    <p>
                        <a class="btn btn-lg btn-primary" href="#contact" role="button">Quiero ver el DEMO</a>
                    </p>
                </div>
                <div class="carousel-image wow zoomIn">
                    <img src="{{ url('landing/img/laptop.png') }}" alt="laptop"/>
                </div>
            </div>
            <!-- Set background for slide in css -->
            <div class="header-back one"></div>

        </div>
        <div class="item">
            <div class="container">
                <div class="carousel-caption blank">
                    <h2 style="font:24px">Smart<b>Cond</b></h2>
                    <h2>Tu Condominio Inteligente<br/>
                        desde tu Móvil,<br/>
                        Tablet o Laptop<h2/>
                    <h4><b>Gestiona el día a día de tu condominio fácil y rápido...</b></h4>
                    <p><a class="btn btn-lg btn-primary" href="#contact" role="button">Quiero ver el DEMO</a></p>
                </div>
            </div>
            <!-- Set background for slide in css -->
            <div class="header-back two"></div>
        </div>
    </div>
    <a class="left carousel-control" href="#inSlider" role="button" data-slide="prev">
        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#inSlider" role="button" data-slide="next">
        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>


<section id="features" class="container services">
    <div class="row">
        
          <!-- show erros -->
          @if (count($errors) > 0)
            <div class="alert alert-danger fade in">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <i class="fa fa-exclamation-triangle"></i> <strong>Disculpe!</strong>
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
            </div>
          @endif
          

          <!-- show erros -->
          @if (count($messages) > 0)
            <div class="alert alert-success fade in">
              <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
              <i class="fa fa-check-circle"></i> <strong>Gracias por contactarnos!</strong>
              <ul>
                  @for ($i=0; $i<count($messages); $i++)
                      <li>{!! $messages[$i] !!}</li>
                  @endfor
              </ul>
              <small><strong>Importante:</strong> Si ves que no llega el correo inmediatamente, revisa tu bandeja de correos no deseados o spam.</small>
            </div>
          @endif

        <div class="col-sm-3">
            <h2>Software como servicio (SaaS)</h2>
            <p>SaaS (Software as a Service) El producto se ofrece como un servicio, esto implica que la información siempre es tuya y nosotros proveemos la infraestructura tecnológica para ayudarte a manejarla a cambio de un costo mensual.</p>
        </div>        
        <div class="col-sm-3">
            <h2>Mobile Friendly</h2>
            <p>Ajusta su presentación (resolución y tamaño de pantalla adecuados) para uso cómodo desde dispositivos móviles y tablets.</p>
        </div>
        <div class="col-sm-3">
            <h2>Multi Condominios</h2>
            <p>Pensada para manejar desde un condominio pequeño hasta empresas administradoras que deseen manejar multiples condominios con centenares de propiedades, todo desde una sola aplicación.</p>
        </div>        
        <div class="col-sm-3">
            <h2>Single Sign On</h2>
            <p>El propietario podrá acceder a todas sus propiedades de diferentes condominios administradas por la aplicación con una sola instancia de identificación.</p>
        </div>
    </div>
</section>

<section  class="container features">
    <div class="row">
        <div class="col-lg-12 text-center">
            <div class="navy-line"></div>
            <h1>Smart<b>Cond</b> <br/><span class="navy">Todas lo que necesitas en una sola aplicación</span> </h1>
            <p>Con Smart<b>Cond</b> serás más organizado y efectivo en la gestión de las relaciones con tus propietarios o clientes. </p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 text-center wow fadeInLeft">
            <div>
                <i class="fa fa-tachometer features-icon"></i>
                <h2>Panel de Novedades (Dashboard)</h2>
                <p>Brinda una pantalla inicial que permite visualizar la información más importante para monitorear, analizar y administrar el desempeño del condominio de manera más efectiva.</p>
            </div>
            <div class="m-t-lg">
                <i class="fa fa-pencil-square-o features-icon"></i>
                <h2>Registra Cuotas Condominales y Recargos</h2>
                <p>Registra por los adeudos de cada vivienda, el sistema te guiará para clasificar las cuotas en categorías y así poder tener la organizada en partidas presupuestarias.<br/>
                De igual forma registra penalizaciones a cuotas que se recibieron de forma tardía o penalizaciones extraordinarias.</p>
            </div>
        </div>
        <div class="col-md-6 text-center  wow zoomIn">
            <img src="{{ url('landing/img/perspective.png') }}" alt="dashboard" class="img-responsive">
        </div>
        <div class="col-md-3 text-center wow fadeInRight">
            <div>
                <i class="fa fa-bar-chart features-icon"></i>
                <h2>Registra Ingresos y Egresos</h2>
                <p>Registra los Ingresos (asociados a coutas u extraordinarios) y los Egresos (asociados a proveedores o grastos extraordinarios) de la manera mas simple y organizada adjuntando un soporte (factura, nota de entrega) en formatos (pfd, docx, xlsx, jpeg, png).
                </p>
            </div>
            <div class="m-t-lg">
                <i class="fa fa-credit-card features-icon"></i>
                <h2>Cuentas Bancarias y Fondos</h2>
                <p>Se pueden referenciar los ingresos y egresos a múltiples cuentas bancarias y/o cajas chicas para tener mejor control de la ubicación de los recursos en todo momento.<br/>
                De igual forma se pueden definir fondos o bolsas de dinero con la finalidad de destinar recursos a tareas específicas como reparaciones extraordinarias, ahorro, mejoras a las instalaciones, entre otras.</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 text-center">
            <div class="navy-line"></div>
            <h1>Descubre las grandes ventajas que te ofrece la aplicación</h1>
            <p>Desarrollado con tecnología de punta, con soporte y actualizaciones constantes sin costo adicional. </p>
        </div>
    </div>
    <div class="row features-block">
        <div class="col-lg-6 features-text wow fadeInLeft">
            <i class="fa fa-money features-icon"></i>
            <h2>Registro de Pagos</h2>
            <p>Registra tus pagos indicando las cuotas condominales que estás cancelando, que metodo de pago utlizaste y a que cuenta transferiste. Al administrador le llegará una notificación al momento de tu registro de pago. Al momento que el administrador confirme el pago, te llegará una notificación de aceptación o rechazo del pago.</p>
            <a href="#contact" class="btn btn-primary">Quiero ver el DEMO</a>
        </div>
        <div class="col-lg-6 text-right wow fadeInRight">
            <img src="{{ url('landing/img/dashboard.png') }}" alt="dashboard" class="img-responsive pull-right">
        </div>
    </div>
</section>

<section class="features">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="navy-line"></div>
                <h1>Funciones adicionales</h1>
                <p>Funciones que le dan valor agregado a la aplicación. </p>
            </div>
        </div>
        <div class="row features-block">
            <div class="col-lg-3 features-text wow fadeInLeft">
                <i class="fa fa-envelope features-icon"></i>                
                <h2>E-mails</h2>
                <p>Envía e-mails desde la plataforma para múltiples destinatarios seleccionando por vivienda. Algunos de los emails que la aplicacion brinda son:<br/>
                Notificación de creación de usuarios.<br/>
                Avisos de Cobro Mes Condominio.<br/>
                Estados de Cuenta.<br/>
                Notificación de Pago de Cuotas.<br/>
                Notificación de Pago a Proveedor.<br/>
                Notificación de confirmación de pago de cuotas.
                </p>
                <a href="#contact" class="btn btn-primary">Registrate para ingresar al DEMO</a>
            </div>
            <div class="col-lg-6 text-right m-t-n-lg wow zoomIn">
                <img src="{{ url('landing/img/iphone.jpg') }}" class="img-responsive" alt="dashboard">
            </div>
            <div class="col-lg-3 features-text text-right wow fadeInRight">
                <i class="fa fa-file-excel-o features-icon"></i>
                <h2>Facilidad para Exportar Reportes</h2>
                <p>La aplicación en casi todas sus pantallas brinda la posibilidas de poder exportar la data a Excel (xls) para análisis más detallado o a PDF para impresión inmediata.</p>
                <a href="#contact" class="btn btn-primary">Regstrate para ingresar al DEMO</a>
            </div>
        </div>
    </div>

</section>

<section class="features">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="navy-line"></div>
                <h1>Herramientas Colaborativas</h1>
                <p>Smart<b>Cond</b> te brinda servicios colaborativos que facilitan a los propietarios y administradores a comunicarse y trabajar conjuntamente.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5 col-lg-offset-1 features-text">
                <h2>Tareas </h2>
                <i class="fa fa-tasks big-icon pull-right"></i>
                <p>Mejora la comunicación con tus propietarios y mantén a la vista todos los asuntos pendientes de atención. Lleva un control de actividades y porcentaje de avance por cada tarea registrada. La aplicación permite a los propietarios sugerir tareas que deben ser aprobadas por el administrador. Si eres administrador podrás re-asignar la tarea a cualquiera de los propietarios del condominio.</p>
            </div>
            <div class="col-lg-5 features-text">
                <h2>Instalaciones y Reservaciones </h2>
                <i class="fa fa-building-o big-icon pull-right"></i>
                <p>Si el condominio cuenta con salones, canchas deportivas o instalaciones que requieran reservación,la aplicación permite que los propietarios hagan sus reservaciones en línea. Los administradores las pueden aprobar o rechazar. En caso de que las instalaciones generen cargos éstos se aplicarán automáticamente una vez que el administrador apruebe la solicitud.</p>
            </div>
        </div>        
        <div class="row">
            <div class="col-lg-5 col-lg-offset-1 features-text">
                <h2>Encuestas </h2>
                <i class="fa fa-check-square-o big-icon pull-right"></i>
                <p>Crea votaciones para conocer la opinión o decisión de tus residentes de un tema en particular que lo amerite. El sistema mostrará los resultados tabulados y graficados.</p>
            </div>
            <div class="col-lg-5 features-text">
                <h2>Calendario </h2>
                <i class="fa fa-calendar big-icon pull-right"></i>
                <p>Registra cualquier actividad que se requiera y el sistema mostrará a administradores y propietarios notificaciones de los eventos próximos.<br/>Desde el calendario se podrá consultar el planning de ocupación de la instalación que se requiera.</p>            
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5 col-lg-offset-1 features-text">
                <h2>Directorios </h2>
                <i class="fa fa-address-book-o big-icon pull-right"></i>
                <p>Mantén una base de datos con la información que necesites, el sistema te brinda la posibilidad de manejar los siguientes directorios:<br/>
                Servicios Locales (como instituciones, hospitales, clinicas, farmacias, tiendas en general, etc.).<br/>
                Personas de Interés (políticos locales, médicos, abogados, etc).<br/>
                Directorio de Proveedores. Directorio de Empleados.</p>            
            </div>
            <div class="col-lg-5 features-text">
                <h2>Repositorio de Documentos </h2>
                <i class="fa fa-folder-open-o big-icon pull-right"></i>
                <p>Registra en el sistema documentos e imágenes de interés público y organizalos según su categoría (actas, acuerdos, reglamentos)  de esta manera tendrás un repositorio digital de documentos de interés para tu condominio que estará siempre a la mano de los propietarios.</p>            
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5 col-lg-offset-1 features-text">
                <h2>Blog General</h2>
                <i class="fa fa-newspaper-o big-icon pull-right"></i>
                <p>Publica actividades de interés general y clasificarlos en categorías para una mejor organización. (Clasificados, noticias y avisos, solicitudes, entre otros).</p>
            </div>
            <div class="col-lg-5 features-text">
                <h2>Inventario</h2>
                <i class="fa fa-wrench big-icon pull-right"></i>
                <p>Registra el inventario de bienes y activos de tu condominio.</p>            
            </div>
        </div>
    </div>

</section>
<section id="pricing" class="pricing">
    <div class="container">
        <div class="row m-b-lg">
            <div class="col-lg-12 text-center">
                <div class="navy-line"></div>
                <h1>Planes del Servicio</h1>
                <p>Nuestros planes se ajustan a tus necesidades ya que son calculados por propiedad registrada.</p>
            </div>
        </div>
        <div class="row">
        <div class="col-lg-2 wow zoomIn">
        </div>
            <div class="col-lg-4 wow zoomIn">
                <ul class="pricing-plan list-unstyled">
                    <li class="pricing-title">
                        Plan Residencial
                    </li>
                    <li class="pricing-desc">
                        Aplica para todos los condominios de tipo residencial como urbanizaciones (casas, townhouse), edificios entro otros.
                    </li>
                    <li class="pricing-price">
                        VENEZUELA <span>Pago mensual en Bs.</span><br>
                        Por cantidad de propiedadades
                    </li>
                    <li class="pricing-price">
                        OTROS PAÍSES <span>Pago mensual Paypal</span><br>
                        Por cantidad de propiedades
                    </li>                    
                    <li>
                        Propiedades a registrar (Ilimitadas)
                    </li>
                    <li>
                        Administradores (máx 2)
                    </li>
                    <li>
                        Multicondominio
                    </li>                    
                    <li>
                        Todas las funciones disponibles
                    </li>
                    <li>
                        Soporte 24x7
                    </li>
                    <li>
                        <a class="btn btn-primary btn-xs" href="#contact">Solicita un presupesto *</a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-4 wow zoomIn">
                <ul class="pricing-plan list-unstyled">
                    <li class="pricing-title">
                        Plan Comercial
                    </li>
                    <li class="pricing-desc">
                        Aplica para aquellos condominios de tipo comercial como por ejemplo: centros comerciales, locales comerciales, consultorios médicos, terrenos entre otros.
                    </li>
                    <li class="pricing-price">
                        VENEZUELA <span>Pago mensual en Bs.</span><br>
                        Por cantidad de locales
                    </li>
                    <li class="pricing-price">
                        OTROS PAÍSES <span>Pago mensual Paypal</span><br>
                        Por cantidad de locales
                    </li>                    
                    <li>
                        Propiedades a registrar (Ilimitadas)
                    </li>
                    <li>
                        Administradores (Ilimitados)
                    </li>
                    <li>
                        Multicondominio
                    </li>                    
                    <li>
                        Todas las funciones disponibles
                    </li>
                    <li>
                        Soporte 24x7
                    </li>
                    <li>
                        <a class="btn btn-primary btn-xs" href="#contact">Solicita un presupesto *</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-2 wow zoomIn">
        </div>
        
        
        <div class="row m-t-lg">
            <div class="col-lg-8 col-lg-offset-2 text-center m-t-lg">
                <p><span class="navy">*</span> Puedes solicitar un presupuesto llenando el formulario y te lo enviaremos a tu dirección de correo según los datos que nos proporciones.</p>
            </div>
        </div>
    </div>

</section>

<section id="contact" class="gray-section contact">
    <div class="container">
        <div class="row m-b-lg">
            <div class="col-lg-12 text-center">
                <div class="navy-line"></div>
                <h1>Contáctanos</h1>
                <p><b>Te invitamos a registrarte y te enviaremos a tu dirección de correo un usuario y una contraseña para ingresar al DEMO</b><br/> de esta manera podrás conocer todos los beneficios que brinda la aplicación.</p>
            </div>
            <div class="col-lg-12 text-center">
                <address>
                    <strong><span class="navy">Guayoyo Software, C.A.</span></strong><br/>
                    Maturín, Monagas<br/>
                    VENEZUELA<br/>
                    <i class="fa fa-phone" aria-hidden="true"></i> <abbr title="Phone"></abbr>+58 416 785.48.99<br/>                    
                    <i class="fa fa-envelope-o" aria-hidden="true"></i> cliente@smartcond.com.ve
                </address>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-lg-offset-2">
        <form class="form-horizontal" id="form" method="POST" action="{{ route('register') }}">
            {{ csrf_field() }}
            <input type="hidden" name="cell" id="cell" class="form-control" value="">
            <div class="col-sm-4">
                        <div class="col-sm-12">
                            <p>Llene el formulario. <b/>(*) Campos Obligatorios</b></p>
                        </div>                                    
                        <div class="col-sm-12">
                            {{ Form::select('country', $countries, null, ['id' => 'country', 'class'=>'form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                        </div>
                        <div class="col-sm-12" style="padding-top: 1mm">
                            {{ Form::select('state', [], null, ['id' => 'state', 'class'=>'form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                        </div>
                        <div class="col-sm-12" style="padding-top: 1mm">
                          <input id="condominium" class="form-control" name="condominium" placeholder="Nombre del Condominio *" required="required" type="text">
                        </div>
                        <div class="col-sm-12" style="padding-top: 1mm">
                            {{ Form::select('type', ['C' =>'Comercial', 'R' => 'Residencial'], 'R', ['id' => 'type', 'class'=>'form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                        </div>
                        <div class="col-sm-12" style="padding-top: 1mm">
                            {{ Form::select('property_type', $property_types, 1, ['id' => 'property_type', 'class'=>'form-control', 'tabindex'=>'-1', 'placeholder'=>'', 'required'])}}
                        </div>
                        <div class="col-sm-12" style="padding-top: 1mm">
                          <input id="properties" class="form-control" name="properties" placeholder="Cantidad de propiedades *" min="1" max="500" required="required" type="number">
                        </div>
                </div>
                <div class="col-sm-4">
                        <div class="col-sm-12" style="padding-top: 7mm">
                          <input id="contact" class="form-control" name="contact" placeholder="Persona contacto *" required="required" type="text">
                        </div>
                        <div class="col-sm-12" style="padding-top: 1mm">
                          <input id="national_cell" class="form-control" name="national_cell" placeholder="" required="required" type="text">
                          <span id="error-msg" style="color:#cc5965;font-weight:bold"></span>
                        </div>
                        <div class="col-sm-12" style="padding-top: 1mm">
                            <label style="color: #aeaeae;font-size: 9pt">Será su usuario de acceso al sistema</label>
                          <input type="email" id="email" name="email" required="required" class="form-control" placeholder="Correo electrónico *">
                        </div>
                        <div class="col-sm-12" style="padding-top: 1mm">
                          <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" minlength=6 maxlength=10 required>
                        </div>
                        <div class="col-sm-12">                    
                            <br/>
                            <button type="submit" class="btn btn-block btn-primary"><i class="fa fa-sign-in" aria-hidden="true"></i> REGISTRARSE</button>
                        </div>
                </div>
            </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 text-center">

                <p class="m-t-sm">
                    O siguenos en nuestras redes sociales
                </p>
                <ul class="list-inline social-icon">
                    <li><a href="#"><i class="fa fa-twitter"></i></a>
                    </li>
                    <li><a href="#"><i class="fa fa-facebook"></i></a>
                    </li>
                    <li><a href="#"><i class="fa fa-linkedin"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-lg-offset-2 text-center m-t-lg m-b-lg">
                <p><strong>&copy; 2020 <i class="fa fa-coffee" aria-hidden="true"></i> GuayoyoSoftware</p>
            </div>
        </div> 
    </div>
</section>

<script src="{{ URL::asset('landing/js/jquery-2.1.1.js') }}"></script>
<script src="{{ URL::asset('landing/js/pace.min.js') }}"></script>
<script src="{{ URL::asset('landing/js/bootstrap.min.js') }}"></script>
<script src="{{ URL::asset('landing/js/classie.js') }}"></script>
<script src="{{ URL::asset('landing/js/cbpAnimatedHeader.js') }}"></script>
<script src="{{ URL::asset('landing/js/wow.min.js') }}"></script>
<script src="{{ URL::asset('landing/js/inspinia.js') }}"></script>
<!-- iCheck -->
<script src="{{ URL::asset('js/plugins/iCheck/icheck.min.js') }}"></script>
<!-- Select2 -->
<script src="{{ URL::asset('js/plugins/select2/dist/js/select2.full.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/select2/dist/js/i18n/es.js') }}"></script>
<!-- Jquery Validate -->
<script src="{{ URL::asset('js/plugins/jquery-validation-1.19.1/dist/jquery.validate.min.js') }}"></script>
<script src="{{ URL::asset('js/plugins/jquery-validation-1.19.1/dist/localization/messages_es.js') }}"></script>
<!-- International Phones --> 
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/intlTelInput.js') }}"></script>
<script src="{{ URL::asset('js/plugins/intl-tel-input-master/build/js/utils.js') }}"></script>
<script>
   
var input = document.querySelector("#national_cell"),
output = document.querySelector("#error-msg");

var iti = window.intlTelInput(input, {
  onlyCountries: ['ar', 'bo', 'br', 'cl', 'co', 'cr', 'cu', 'sv', 'ec', 'es', 'gt', 'hn', 'mx', 'ni', 'pa', 'py', 'pe', 'pr', 'do', 'uy', 've'],
  initialCountry: "auto",
  geoIpLookup: function(callback) {
    $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
      var countryCode = (resp && resp.country) ? resp.country : "ve";
      callback(countryCode);
    });
  },
  nationalMode: true,
  utilsScript: "../../build/js/utils.js?1590403638580" // just for formatting/placeholders etc
});

var handleChange = function() {
    var text="";
    if(iti.getNumber()!=''){
        (iti.isValidNumber()) ? $('#cell').val(iti.getNumber()) : text="Introduzca un número válido";
        var textNode = document.createTextNode(text);
        output.innerHTML = "";
        output.appendChild(textNode);        
    }else{
        $('#error-msg').html('');
    }
};

// listen to "keyup", but also "change" to update when the user selects a country
input.addEventListener('change', handleChange);
input.addEventListener('keyup', handleChange);


$("#country").change( event => {
  url = `{{URL::to('get_states/')}}/${event.target.value}`;                    
  $.get(url, function( response){
    $("#state").empty();
    response.forEach(element => {
      $("#state").append(`<option value=${element.id}> ${element.name} </option>`);
    });
    $('#state').val(null).trigger('change');
  });
  url = `{{URL::to('countries')}}/${event.target.value}`;                    
  $.get(url, function(response){
    iti.setCountry(response.data.iso);  
  });
});

$(document).ready(function() {
    $("#country").select2({
        language: "es",
        placeholder: "Seleccione un país*",
        allowClear: false,
        width: '100%'
    });
        
    $("#state").select2({
        language: "es",
        placeholder: "Seleccione *",
        allowClear: false,
        width: '100%'
    });

    $("#type").select2({
        language: "es",
        placeholder: "Tipo de condominio *",
        allowClear: false,
        minimumResultsForSearch: -1,
        width: '100%'
    });
  
    $("#property_type").select2({
        language: "es",
        placeholder: "Tipo de propiedad *",
        allowClear: false,
        minimumResultsForSearch: -1,
        width: '100%'
    });

    $("#form").validate({            
        submitHandler: function(form) {
        form.submit();
      }        
  });

});       
</script>


</body>
</html>
