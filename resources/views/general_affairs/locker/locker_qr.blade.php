<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>
    @if(isset($title) && isset($title_jp))
    {{$title}} {{$title_jp}}
    @else 
    YMPI 情報システム
    @endif
  </title>
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
  <meta name="apple-mobile-web-app-capable" content="yes" />
  {{-- <link rel="stylesheet" href="{{ url("plugins/pace/pace.min.css")}}"> --}}
  <link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
	<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
	<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">


  <style>
    thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		text-align:left;
		overflow:hidden;
	}
	tbody>tr>td{
		text-align:left;
	}
	tfoot>tr>th{
		text-align:left;
	}
	th:hover {
		overflow: visible;
	}
	td:hover {
		overflow: visible;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:0;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid black;
		padding:0;
	}
	td{
		overflow:hidden;
		text-overflow: ellipsis;
	}
	input::-webkit-outer-spin-button,
	input::-webkit-inner-spin-button {
		/* display: none; <- Crashes Chrome on hover */
		-webkit-appearance: none;
		margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
	}

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

	#tableResume > thead > tr > th {
		padding-left: 5px;
		padding-top: 2px;
		padding-bottom: 2px;
	}

	#tableKaryawan > thead > tr > th {
		padding-left: 5px;
		padding-top: 3px;
		padding-bottom: 3px;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	/*.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}*/
	#loading, #error { display: none; }

	.container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 16px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
  width: 0px;
}

/* Hide the browser's default radio button */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #e8e8e8;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #2cb802;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.container .checkmark:after {
  top: 9px;
  left: 9px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: white;
}

.container_checkmark {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 13px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container_checkmark input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark_checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container_checkmark:hover input ~ .checkmark_checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container_checkmark input:checked ~ .checkmark_checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark_checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container_checkmark input:checked ~ .checkmark_checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container_checkmark .checkmark_checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
.crop2 {
		overflow: hidden;
}
.crop2 img {
	/*height: 120px;*/
	margin: -25% 0 0 0 !important;
}

  </style>
</head>
<body class="hold-transition skin-purple layout-top-nav">
  <div class="wrapper">
    <div class="content-wrapper bg-purple" style="background-color: rgb(60,60,60); padding-top: 10px;">
      <div class="row" style="vertical-align: middle;">
      	<?php if (count($locker) > 0){ ?>
			<div class="col-xs-12" style="padding-bottom: 10px">
				<?php if (str_contains($locker->locker_id,'M')) {
					$color = '#3CB371';
					$mf = 'Male';
				}else{
					$color = '#FF69B4';
					$mf = 'Female';
				} ?>
				<center style="color: <?php echo $color ?>;background-color:white;font-weight: bold;font-size: 2vw">
					{{$mf}} {{ $title }}<small><span class="text-purple">{{ $title_jp }}</span></small>
				</center>
			</div>
			<!-- <div class="col-xs-10 col-xs-offset-1" style="padding-right: 5px;padding-top: 10px;height: 80%;">
				
			</div> -->
			<!-- <div style="border: 1px solid black;"> -->
			<!-- <div class="widget-user-header bg-purple" style="height: 100%;"> -->
			<!-- <div class="widget-user-image crop2"> -->
				<div class="col-xs-5 col-xs-offset-1 col-md-4 col-md-offset-1 crop2" style="text-align: right;">
					<img style="width:100%;margin-bottom: 0px;" src="{{ url('images/avatar/'.$locker->employee_id.'.jpg') }}" alt="">
				</div>
				<div class="col-xs-6 col-md-7" style="display:table-cell;height: 100%">
					<table style="height:37vw">
						<tr>
							<th style="font-size: 5vw; font-weight: bold;margin-top: 0px;vertical-align: middle;">{{$locker->locker_id}}</th>
						</tr>
						<tr>
							<th style="font-size: 3.5vw;margin-bottom: 0px;margin-right: ">{{$locker->employee_id}}</th>
						</tr>
						<tr>
							<th style="font-size: 3.5vw;margin-bottom: 0px">{{$locker->employee_name}}</th>
						</tr>
						<tr>
							<th style="font-size: 3.5vw;margin-bottom: 0px">{{$locker->department_name}}</th>
						</tr>
						<tr>
							<th style="font-size: 3.5vw;margin-bottom: 0px">{{$locker->section_name}}</th>
						</tr>
					</table>
				</div>
			<!-- </div> -->
			<!-- </div> -->
			<!-- </div> -->
		<?php }else{ ?>
			<div class="col-xs-12" style="padding-right: 5px;padding-top: 10px">
				<!-- <div class="widget-user-image crop2"> -->
					<center><h3 style="font-size: 16vw; font-weight: bold;margin-top: 0px;">LOCKER EMPTY</h3></center>
			</div>
		<?php } ?>
      </div>
    </div>
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
  {{-- <script src="{{ url("bower_components/PACE/pace.min.js")}}"></script> --}}
  <script src="{{ url("dist/js/adminlte.min.js")}}"></script>
  <script src="{{ url("dist/js/demo.js")}}"></script>
  {{-- <script>$(document).ajaxStart(function() { Pace.restart(); });</script> --}}
</body>
</html>
