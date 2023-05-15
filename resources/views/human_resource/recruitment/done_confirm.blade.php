<!DOCTYPE html>
<html>
<head>
	<link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
	<meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<title>YMPI 情報システム</title>
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
	<link rel="stylesheet" href="{{ url("bower_components/font-awesome/css/font-awesome.min.css")}}">
	<link rel="stylesheet" href="{{ url("bower_components/Ionicons/css/ionicons.min.css")}}">
	<link rel="stylesheet" href="{{ url("bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")}}">
	<link rel="stylesheet" href="{{ url("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">
	<link rel="stylesheet" href="{{ url("plugins/iCheck/all.css")}}">
	<link rel="stylesheet" href="{{ url("bower_components/select2/dist/css/select2.min.css")}}">
	<link rel="stylesheet" href="{{ url("dist/css/AdminLTE.min.css")}}">
	<link rel="stylesheet" href="{{ url("dist/css/skins/skin-purple.css")}}">
	<link rel="stylesheet" href="{{ url("fonts/SourceSansPro.css")}}">
	<link rel="stylesheet" href="{{ url("css/buttons.dataTables.min.css")}}">
	{{-- <link rel="stylesheet" href="{{ url("plugins/pace/pace.min.css")}}"> --}}
	@yield('stylesheets')
</head>


<body class="hold-transition skin-purple layout-top-nav">
	<div class="wrapper">
		<header class="main-header" >
			<nav class="navbar navbar-static-top">
				{{-- <div class="container"> --}}
					<div class="navbar-header">
						<a href="{{ url("/home") }}" class="logo">
							<span style="font-size: 35px"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="45px" style="margin-bottom: 6px;">&nbsp;<b>M I R A I</b></span>
						</a>
					</div>
					<div class="collapse navbar-collapse pull-left" id="navbar-collapse">
						<ul class="nav navbar-nav">
							<li>
								<a style="font-size: 20px; font-weight: bold;" class="text-yellow">
									<?php if (isset($title)) {
										echo $title;
									} ?>
								</a>
							</li>
						</ul>
					</div>
				</nav>
			</header>
			<div class="content-wrapper" style="background-color: #ecf0f5; padding-top: 10px;">
				<section class="content">

					<div class="error" style="text-align: center;">
						<h1><i class="fa fa-file-text-o"></i> Smart Recruitment </h1>
						<p>
							<?php if (ISSET($message) && $message == 'Request ini berhasil disetujui.'){ ?>
								<h2 class="text-green">
									<i class="fa fa-check-circle fa-lg"></i> {{$message}}<br>
								</h2>
							<?php }
							else if (ISSET($message) && $message == 'Request ini berhasil direject.'){ ?>
								<h2 class="text-red">
									<i class="fa fa-times"></i> {{$message}}<br>
								</h2>
								<form role="form" method="post" id="formNote" action="">
									<div class="col-xs-12 " style="text-align: center;" id="show">
										<p>
											<input type="hidden" value="{{csrf_token()}}" name="_token" />
											<div class="col-xs-12">
												<h2>Give Reason For Applicant :</h2>
											</div>
											<div class="col-xs-12">
												<textarea class="form-control" id="question" name="question"></textarea>
												<input type="hidden" class="form-control" id="posisi" name="posisi" value="" required=""></textarea>
											</div>

											<div class="col-xs-12">
												<br>
												<button class="btn btn-success btn-lg" type="Submit">Submit & Send Email</button>
												<a class="btn btn-danger btn-lg" type="button" onclick="reset();">Reset</a>
											</div>
										</p>
									</div>
								</form>
							<?php }
							else{ ?>
								<h2 class="text-red">
									<i class="fa fa-times"></i> {{$message}}<br>
								</h2>
							<?php } ?>
						</p>
					</div>

				</section>
			</div>
			@include('layouts.footer')
		</div>
		<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
		<script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
		<script src="{{ url("dist/js/adminlte.min.js")}}"></script>
		<script src="{{ url("dist/js/demo.js")}}"></script>
		@yield('scripts')
	</body>
	</html>
