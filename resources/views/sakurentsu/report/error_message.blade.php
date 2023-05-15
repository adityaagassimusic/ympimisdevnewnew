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
	<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">

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
					<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
						<p style="position: absolute; color: white; top: 45%; left: 35%;">
							<span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
						</p>
					</div>

					<div class="error" style="text-align: center;" id="sukses">
						<h1><i class="fa fa-file-text-o"></i> {{$title}} </h1>
						<p>
							<h2 class="text-blue">
								<i class="fa fa-exclamation-circle fa-lg"></i> Mohon Maaf, Approval Dalam Perbaikan <br> 
								Lakukan Approval melalui email
							</h2>
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
		<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
		<script>
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			function sendComment() {
				$("#loading").show();
				var formData = new FormData();
				formData.append('status', '{{ Request::segment(4) }}');
				formData.append('form_number', '{{ Request::segment(5) }}');
				formData.append('comment', $("#comment").val());
				formData.append('position', '{{ Request::segment(6) }}');

				$.ajax({
					url: '{{ url("comment/sakurentsu/trial_request") }}',
					type: 'POST',
					data: formData,
					success: function (result, status, xhr) {
						$("#loading").hide();

						openSuccessGritter('Success', result.message);
						$("#isi").hide();
						$("#msg").show();
					},
					error: function(result, status, xhr){
						$("#loading").hide();

						openErrorGritter('Error!', result.message);
					},
					cache: false,
					contentType: false,
					processData: false
				});
			}

			function openSuccessGritter(title, message){
				jQuery.gritter.add({
					title: title,
					text: message,
					class_name: 'growl-success',
					image: '{{ url("images/image-screen.png") }}',
					sticky: false,
					time: '2000'
				});
			}

			function openErrorGritter(title, message) {
				jQuery.gritter.add({
					title: title,
					text: message,
					class_name: 'growl-danger',
					image: '{{ url("images/image-stop.png") }}',
					sticky: false,
					time: '2000'
				});
			}
		</script>
		@yield('scripts')
	</body>
	</html>
