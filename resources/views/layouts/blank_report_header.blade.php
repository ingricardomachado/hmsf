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
                height: 50px;
                text-align: left;
                line-height: 35px;
            }
            footer {
                position: fixed; 
                bottom: -60px; 
                left: 0px; 
                right: 0px;
                height: 50px;
                text-align:center;
            }            
            body {
                margin-top: 3.0em;
                font-family: "Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif, "DejaVu Sans Condensed";
                font-size: 10px;}
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
        <header>
            <table class="table" width="100%" border="0">
                <tbody>
                    <tr>
                        <td width="25%">
                            <img alt="image" style="max-height:100px; max-width:100px;" src="{{ $logo }}"/>
                        </td>
                        <td width="50%" style="text-align:center">
                            <h2>{{ $company }}</h2>
                        </td>
                        <td width="25%" style="text-align:left;color:grey"></td>
                    </tr>
                </tbody>
            </table>
        </header>
        <footer>
            Copyright &copy; {{ date("Y") }} {{ config('app.name') }}. Todos los derechos reservados.
        </footer>        
            @yield('content')
        @stack('scripts')
    </body></html>