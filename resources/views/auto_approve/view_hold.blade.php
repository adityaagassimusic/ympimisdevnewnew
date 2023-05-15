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
						<h1><i class="fa fa-file-text-o"></i> MIRAI Approval </h1>
						<h2 class="text-blue">
							<i class="fa fa-check-circle fa-lg"></i> Sent To Aplicant No Transaction {{ $data->no_transaction }}<br>
						</h2>
						<h3 style="background-color: yellow;">Reason Hold : {{ $data->comment }}</h3>
						<p>
							<form role="form" method="post" id="formNote" action="{{url('adagio/send/comment/'.$data->no_transaction)}}">
								<input type="hidden" name="_token" value="{{ csrf_token() }}">

								<div class="col-xs-12 " style="text-align: center;" id="button">
									<div class="form-group row">
										<a class="btn btn-success" style="font-weight: bold; font-size: 1vw; width: 30%;" onclick="Detail()">Detail</a>
									</div>
									<div class="form-group row">
										<a class="btn btn-success" style="font-weight: bold; font-size: 1vw; width: 30%;" onclick="Comment()">Answer Questions</a>
									</div>
								</div>
								
								<div class="col-xs-12 " style="text-align: center" id="show">
									<div class="form-group row">
										<label class="col-sm-5" style="color: black;">Pemohon (申請者)</label>
										<?php
										$nama = explode("/", $data->nik);
										?>
										<input class="col-xs-3" type="text" name="" value="{{ $nama[0] }} - {{ $nama[1] }}" readonly>
									</div>
									<div class="form-group row">
										<label class="col-sm-5" style="color: black;">Department</label>
										<input class="col-xs-3" type="text" name="" value="{{ $data->department }}" readonly>
									</div>
									<div class="form-group row">
										<label class="col-sm-5" style="color: black;">No Dok. (書類番号)</label>
										<input class="col-xs-3" type="text" name="" value="{{ $data->no_dokumen }}" readonly>
									</div>
									<div class="form-group row">
										<label class="col-sm-5" style="color: black;">Nama Dok. (書類名)</label>
										<input class="col-xs-3" type="text" name="n_dok" id="n_dok" value="{{ $data->description }}">
									</div>
									<div class="form-group row">
										<label class="col-sm-5" style="color: black;">Judul Dok. (件名)</label>
										<input class="col-xs-3" type="text" name="j_dok" id="j_dok" value="{{ $data->judul }}">
									</div>
									<div class="form-group row">
										<label class="col-sm-5" style="color: black;">Tanggal Dibuat (作成日)</label>
										<input class="col-xs-3" type="text" name="" value="{{ $data->created_at }}" readonly>
									</div>
									<div class="form-group row">
										<label class="col-sm-5" style="color: black;">Catatan (備考)</label>
										<input class="col-xs-3" type="text" name="" value="{{ $data->summary }}">
									</div>
									<p>
										<div class="col-xs-12">
											<br>
											<button class="btn btn-success btn-lg" type="Submit">Submit & Send Email</button>
											<a class="btn btn-danger btn-lg" type="button" onclick="reset();">Reset</a>
										</div>
									</p>
								</div>

								<div class="col-xs-12 " style="text-align: center" id="comment">
									<input type="hidden" value="{{csrf_token()}}" name="_token" />
											<div class="col-xs-12">
												<h2>Answer Questions :</h2>
											</div>
											<div class="col-xs-12">
												<textarea class="form-control" id="answer" name="answer"></textarea>
											</div>
									<p>
										<div class="col-xs-12">
											<br>
											<button class="btn btn-success btn-lg" type="Submit">Submit & Send Email</button>
											<a class="btn btn-danger btn-lg" type="button" onclick="reset();">Reset</a>
										</div>
									</p>
								</div>
					
							</form>
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

		  	$('#show').hide();
		  	$('#comment').hide();
		  	$('#hide').hide();

		  	$("#formNote").submit(function(){
		  		$("#loading").show();
		  		this.submit();
		  	});

		  	function Detail(){
		  		$('#show').show();
		  		$('#comment').hide();
		  		$('#button').hide();
		  	}

		  	function Comment(){
		  		$('#comment').show();
		  		$('#button').hide();
		  	}

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
