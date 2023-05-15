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
						<h1><i class="fa fa-file-text-o"></i> Approval Number (承認番号) {{ $data->no_transaction }} </h1>
						<p>
							@if($data->remark == "Rejected")
							<h2 class="text-red">
								<i class="fa fa-times"></i> Already Rejected (却下済み)<br>
							</h2>
							@elseif($data->remark == "Hold & Comment (ホールド＆コメント)")
							<h2 class="text-blue">
								<i class="fa fa-times"></i> {{ $data->remark }} <br>
							</h2>
							@elseif($data->remark == "Send Aplicant Hold & Comment")
							<h2 class="text-green">
								<i class="fa fa-check-circle fa-lg"></i> Successfully Send To Applicant (申請者に送信済み)<br>
							</h2>
							@elseif($data->remark == "Open")
							<h2 class="text-green">
								<i class="fa fa-check-circle fa-lg"></i> Successfully Approved (承認済み)<br>
							</h2>
							@elseif($data->remark == "Close")
							<h2 class="text-green">
								<i class="fa fa-check-circle fa-lg"></i> Successfully Approved (承認済み)<br>
							</h2>
							@endif
							<br>
							@if($data->remark == "Hold & Comment")
							<form role="form" method="post" id="formNote" action="{{url('adagio/hold/comment/'.$data->no_transaction)}}">
								<div class="col-xs-12 " style="text-align: center;" id="show">
									<p>
										<input type="hidden" value="{{csrf_token()}}" name="_token" />
										<div class="col-xs-12">
											<h2>Give Question To Applicant (申請者に質問する) :</h2>
										</div> 
										<div class="col-xs-12">
											<textarea class="form-control" id="question" name="question"></textarea>
											<input type="hidden" class="form-control" id="posisi" name="posisi" value="" required=""></textarea>
										</div>
										<div class="col-xs-12">
											<br>
											<button class="btn btn-success btn-lg" type="Submit">Submit & Send Email</button>
										</div>
									</p>
								</div>
							</form>
							@endif
						</p>
					</div>


				</section>
			</div>
			@include('layouts.footer')
		</div>
		  <script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
		  <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
		  <script src="{{ url("bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>
		  <script src="{{ url("bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")}}"></script>
		  <script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
		  <script src="{{ url("bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")}}"></script>
		  <script src="{{ url("bower_components/jquery-slimscroll/jquery.slimscroll.min.js")}}"></script>
		  <script src="{{ url("plugins/iCheck/icheck.min.js")}}"></script>
		  <script src="{{ url("bower_components/fastclick/lib/fastclick.js")}}"></script>
		  <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
		  <script src="{{ url("dist/js/adminlte.min.js")}}"></script>
		  <script src="{{ url("dist/js/demo.js")}}"></script>
		  @yield('scripts')

		  <script type="text/javascript">

		  	$('#show').show();
		  	$('#hide').hide();

		  	$("#formNote").submit(function(){
		  		$("#loading").show();
		  		this.submit();
		  	});

		  	function ShowHide(){
		  		$('#show').hide();
		  		$('#hide').show();
		  	}

		  	function reset(){
		  		$("#question").val('');
		  	}

		  </script>
	</body>
	</html>
