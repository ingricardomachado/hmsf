<!DOCTYPE html>
<html>
<style type="text/css">
    body{
        background-image:url('img/company_logo.png');
        background-attachment:fixed;
        background-repeat: no-repeat;
        background-size: cover;
    }    
</style>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name') }} | Login</title>
    <link rel="shortcut icon" href="{{ asset("img/app_ico.ico") }}" />

    <!-- Bootstrap -->
    <link href="{{ asset("css/bootstrap.min.css") }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset("fonts/css/font-awesome.min.css") }}" rel="stylesheet">
    <!-- Animate -->    
    <link href="{{ asset("css/animate.css") }}" rel="stylesheet">
    <!-- Custom Style -->
    <link href="{{ asset("css/style.css") }}" rel="stylesheet">

</head>

<body class="gray-bg">
    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div style="margin-top:200px">
                <h1>
                   <span></span>
                </h1>            
                    <!-- show erros -->
                    @if (count($errors) > 0)
                      <div class="alert alert-danger fade in">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <i class="fa fa-exclamation-triangle"></i> <strong>Disculpe!</strong>
                        <ul>
                          @foreach ($errors->all() as $error)
                            <li>{!! $error !!}</li>
                          @endforeach
                        </ul>
                      </div>
                    @endif
                    <!-- /show erros -->
            </div>

                    <form class="m-t" id="form" role="form" method="post" action="{{ url('/login') }}">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <input type="email" name="email" {{ old('email') }} class="form-control" placeholder="Usuario" required="">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Contrase??a" required="">
                        </div>
                        <button type="submit" id="btn_submit" class="btn btn-primary block full-width m-b">Ingresar</button>
                        <a class="reset_pass" href="{{  url('/password/reset') }}">Olvidaste la contrase??a?</a>
                    </form>
                    <p class="text-center m-t">
                        <small><i class="fa fa-truck" aria-hidden="true"></i> {{ config('app.name') }}&nbsp;&nbsp;Copyright &copy; {{ now()->year }}</small>
                    </p>
                    
                </div>
            </div>
        </div>
    </div>

    
</body>

    <!-- jQuery -->
    <script src="{{ asset("js/jquery-2.1.1.js") }}"></script>    
    <!-- Bootstrap -->
    <script src="{{ asset("js/bootstrap.min.js") }}"></script>
    <!-- Jquery Validate -->
    <script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/jquery.validate.min.js') }}"></script>
    <script src="{{ URL::asset('js/plugins/jquery-validation-1.16.0/messages_es.js') }}"></script>
    <script>
        $(document).ready(function(){
                        
            // Validation
            $("#form").validate({
                submitHandler: function(form) {
                    $("#btn_submit").attr("disabled",true);
                    form.submit();
                }        
            });
            
        });
    </script>

</html>
