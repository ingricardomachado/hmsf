<!DOCTYPE html>
<html lang="en"><head>        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">        
        <style type="text/css">  
            body {
                font-family: "Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;
                font-size: 10px;}
            @page {
                margin-top: 3.0em;
                margin-right: 5.0em;
                margin-left: 5.0em;
            }
            /* Thumnail con CSS porque si se coloca como clase el DomPDF solo muestra la imagen en la pagina 1 */
            img {
                border-radius: 4px;  /* Rounded border */
                width: 110px; /* Set a small width */
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
            @yield('content')
                <footer>    
                    <script type="text/php">
                        $now = new DateTime();
                        $y = $pdf->get_height() - 24;
                        $x = $pdf->get_width() - 100;
                        $font_size = 8;         
                        $text_right = '';    
                        $text_center = 'Copyright '.date("Y").' {{ Session::get('app_name') }}. Todos los derechos reservados.';
                        $text_left = '';
                        $pdf->page_text($x, $y, $text_left, null, $font_size);
                        $pdf->page_text(200, $y, $text_center, null, $font_size);
                        $pdf->page_text(50, $y, $text_right, null, $font_size);
                    </script>
                </footer>            
        @stack('scripts')
    </body></html>