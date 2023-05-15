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

					@if(!isset($status))
					<div class="error" style="text-align: center;" id="sukses">
						<h1><i class="fa fa-file-text-o"></i> Trial Request Issue Form 試作依頼書のフォーム</h1>
						@if($data_trial->reject_date)
						<h2 class="text-red">
							<i class="fa fa-close fa-lg"></i> Trial Request Already Rejected<br>
						</h2>
						@else
						<p>
							<h2 class="text-green">
								<i class="fa fa-check-circle fa-lg"></i> Successfully Approved<br>
								
							</h2>
						</p>
						@endif
					</div>
					@elseif(isset($status))
					@if($status == 'hold')
					<div class="error" style="text-align: center;" id="sukses">
						<h1><i class="fa fa-file-text-o"></i> Trial Request Issue Form 試作依頼書のフォーム</h1>
						<!-- <form id="location_form" method="post" autocomplete="off" action="{{ url("comment/sakurentsu/trial_request") }}"> -->
							<h2 class="text-green" id="msg" style="display: none">
								<i class="fa fa-check-circle fa-lg"></i> Successfully Holded<br>
								
							</h2>
							<div id="isi">
								@if($data_trial->reject_date)
								<h2 class="text-red">
									<i class="fa fa-close fa-lg"></i> Trial Request Already Rejected<br>
								</h2>
								@else
								<h2 class="text-blue">
									<i class="fa fa-pencil-square fa-lg"></i> Hold & Comment<br>
								</h2>
								<h3>
									Subject : {{ $data_trial->subject }}
								</h3>
								<label>Please add Comment : </label>
								<input type="hidden" value="{{csrf_token()}}" name="_token">
								<input type="hidden" name="status" value="{{ Request::segment(4) }}">
								<input type="hidden" name="form_number" value="{{ Request::segment(5) }}">
								<input type="hidden" name="position" value="{{ Request::segment(6) }}">
								<center><textarea id="comment" name="comment" class="form-control" style="width: 50%; margin-bottom: 5px" placeholder="add your comment here"></textarea></center>
								<button class="btn btn-success" onclick="sendComment()"><i class="fa fa-check"></i> Hold & Send Comment</button>
								@endif
							</div>
							<!-- </form> -->
						</div>
						@elseif($status == 'reject')
						<div class="error" style="text-align: center;" id="sukses">
							<h1><i class="fa fa-file-text-o"></i> Trial Request Issue Form 試作依頼書のフォーム</h1>
							<!-- <form id="location_form" method="post" autocomplete="off" action="{{ url("comment/sakurentsu/trial_request") }}"> -->
								<h2 class="text-green" id="msg" style="display: none">
									<i class="fa fa-check-circle fa-lg"></i> Successfully Rejected<br>

								</h2>
								<div id="isi">
									@if($data_trial->reject_date)
									<h2 class="text-red">
										<i class="fa fa-close fa-lg"></i> Trial Request Already Rejected<br>
									</h2>
									@else
									<p>
										<h2 class="text-red">
											<i class="fa fa-close fa-lg"></i> Reject & Comment<br>
										</h2>
									</p>
									
									<h3>
										Subject : {{ $data_trial->subject }}
									</h3>
									<label>Please add Comment : </label>
									<input type="hidden" value="{{csrf_token()}}" name="_token">
									<input type="hidden" name="status" value="{{ Request::segment(4) }}">
									<input type="hidden" name="form_number" value="{{ Request::segment(5) }}">
									<input type="hidden" name="position" value="{{ Request::segment(6) }}">
									<center><textarea id="comment" name="comment" class="form-control" style="width: 50%; margin-bottom: 5px" placeholder="add your comment here"></textarea></center>
									<button class="btn btn-success" onclick="sendComment()"><i class="fa fa-check"></i> Reject & Send Comment</button>
									@endif
								</div>
								<!-- </form> -->
							</div>
							@endif
							@endif

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
