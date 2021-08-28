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
                height: 80px;
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
                font-size: 10px;
                /*border: solid red;
                border-width: thin;*/
            }            
            body {
                margin-top: 60px;
                margin-bottom: 0px;
                font-family: "Helvetica Neue", Helvetica, Arial, Roboto, Arial, "Droid Sans", sans-serif;
                font-size: 11px;
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
            .img-rounded {
              border-radius: 6px;
            }
            .img-thumbnail {
              display: inline-block;
              max-width: 100%;
              height: auto;
              padding: 4px;
              line-height: 1.42857143;
              background-color: #fff;
              border: 0.5px solid #ddd;
              border-radius: 4px;
              -webkit-transition: all .2s ease-in-out;
                   -o-transition: all .2s ease-in-out;
                      transition: all .2s ease-in-out;
            }
        </style>
    </head><body>                        
        @stack('stylesheets')                
        <header>
            <table class="table" width="100%" border="0">
                <tbody>
                    <tr>
                        <td width="25%">
                            <img alt="image" style="max-height:130px; max-width:130px;" src="{{ $logo }}" class="img-thumbnail" />
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