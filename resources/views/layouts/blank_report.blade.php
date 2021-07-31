<!DOCTYPE html>
<html lang="en"><head>        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <style type="text/css">  
            header {
                position: fixed;
                top: -20px;
                left: 0px;
                right: 0px;
                height: 0px;
                text-align: left;
                line-height: 35px;
                /*border: solid red;
                border-width: thin;*/
            }
            footer {
                margin: 0;                
                position: fixed; 
                bottom: -40px; 
                left: 0px; 
                right: 0px;
                height: 30px;
                text-align:center;
                /*border: solid red;
                border-width: thin;*/
            }            
            body {
                margin-top: 0px;
                margin-bottom: 0px;
                font-family: "Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif, "DejaVu Sans Condensed";
                font-size: 10px;
                /*border: solid blue;
                border-width: thin;*/                
            }
            @page {
                margin-top: 2.0em;
                margin-right: 4.0em;
                margin-left: 4.0em;
            }
            small {
                font-size: smaller;
            }    
            .saltopagina{page-break-after:always;
            }
            .text-center {
                text-align: center;
            }
            .text-left {
                text-align: left;
            }
            .text-right {
                text-align: right;
            }
            .well {
                min-height: 20px;
                padding: 9px;
                margin-bottom: 20px;
                background-color: #f5f5f5;
                border: 1px solid #e3e3e3;
                border-radius: 4px;
                -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
                box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
            }
            .symbols{
                font-family:"DeJaVu Sans Mono",monospace;
            }
        </style>
    </head><body>                        
        @stack('stylesheets')                
        <footer>
            Copyright &copy; {{ date("Y") }} {{ config('app.name') }}. Todos los derechos reservados.
        </footer>        
            @yield('content')
        @stack('scripts')
    </body></html>