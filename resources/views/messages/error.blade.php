<!DOCTYPE html>
<html lang="">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Cejas Perfectas | Error</title>

		<!-- Bootstrap CSS -->
		<link href="{{ asset("css/bootstrap.min.css") }}" rel="stylesheet">

	</head>
	<body>		
		<br>
		<br>
		<div class="col-md-6 col-sm-12 col-xs-12 col-md-offset-3">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title text-center"><img src="{{ URL::to('img/logo_company.png') }}" style="max-height:120px; max-width:120px;"></h3>
			</div>
			<div class="panel-body">
				<h4><strong>Disculpe</strong></h4>
				<p>Ocurrió un error al intentar cancelar la cita.</p>
			</div>
			<div class="panel-footer text-center">
				<small><strong>Copyright</strong> {{ Session::get('company_name') }} &copy; 2019</small>
			</div>
		</div>
		</div>
	</body>
</html>