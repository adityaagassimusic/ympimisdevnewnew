@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		padding: 2px;
		overflow:hidden;
	}
	tbody>tr>td{
		padding: 2px !important;
	}
	tfoot>tr>th{
		padding: 2px;
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

	#resultScan_info, #resultScan_filter{
		color: white !important;
	}

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #ffe973;
	}
	#loading, #error { display: none; }

	#tableResume > thead > tr > th {
		/*font-size: 20px;*/
		vertical-align: middle;
	}
	#tableCode > tbody > tr > td{
		background-color: white;
	}
	#tableCode > thead > tr > th{
		/*font-size: 12px;*/
	}
	/*#tableCode_info{
		color: white;
	}
	#tableCode_filter{
		color: white;
	}*/

	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}

	.containers {
	  display: block;
	  position: relative;
	  padding-left: 35px;
	  margin-bottom: 12px;
	  cursor: pointer;
	  font-size: 15px;
	  -webkit-user-select: none;
	  -moz-user-select: none;
	  -ms-user-select: none;
	  user-select: none;
	  padding-top: 6px;
	}

	/* Hide the browser's default checkbox */
	.containers input {
	  position: absolute;
	  opacity: 0;
	  cursor: pointer;
	  height: 0;
	  width: 0;
	}

	/* Create a custom checkbox */
	.checkmark {
	  position: absolute;
	  top: 0;
	  left: 0;
	  height: 25px;
	  width: 25px;
	  background-color: #eee;
	  margin-top: 4px;
	}

	/* On mouse-over, add a grey background color */
	.containers:hover input ~ .checkmark {
	  background-color: #ccc;
	}

	/* When the checkbox is checked, add a blue background */
	.containers input:checked ~ .checkmark {
	  background-color: #2196F3;
	}

	/* Create the checkmark/indicator (hidden when not checked) */
	.checkmark:after {
	  content: "";
	  position: absolute;
	  display: none;
	}

	/* Show the checkmark when checked */
	.containers input:checked ~ .checkmark:after {
	  display: block;
	}

	/* Style the checkmark/indicator */
	.containers .checkmark:after {
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

	.autocomplete {
  position: relative;
  display: inline-block;
}
html {
	  scroll-behavior: smooth;
	}
.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <small class="text-purple">{{ $title_jp }}</small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0px">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	@if (session('status'))
	<div class="alert alert-success alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
		{{ session('status') }}
	</div>
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>   
	@endif						
	<div class="row">
		<div class="col-xs-12">
			<center style="background-color: white;margin-bottom: 10px;padding: 6px;">
				<span style="font-size: 40px;font-weight: bold;">PENDAFTARAN YMPI COMPETITION 2022</span>
			</center>
		</div>
		<div class="col-xs-12">
			<table style="width: 100%">
				<tr>
					<td style="width: 14%">
						<button class="btn btn-default" style="width: 100%;font-weight: bold;font-size: 20px;padding: 20px;" onclick="checkCategory('ml')">
							Mobile Legends
						</button>
					</td>
					<td style="width: 14%">
						<button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 20px;padding: 20px;" onclick="checkCategory('ppa')">
							Penalti Putra
						</button>
					</td>
					<td style="width: 14%">
						<button class="btn btn-primary" style="width: 100%;font-weight: bold;font-size: 20px;padding: 20px;" onclick="checkCategory('ppi')">
							Penalti Putri
						</button>
					</td>
					<td style="width: 14%">
						<button class="btn btn-warning" style="width: 100%;font-weight: bold;font-size: 20px;padding: 20px;" onclick="checkCategory('in')">
							Indiaka
						</button>
					</td>
					<td style="width: 14%">
						<button class="btn btn-info" style="width: 100%;font-weight: bold;font-size: 20px;padding: 20px;" onclick="checkCategory('ka')">
							Karaoke
						</button>
					</td>
					<td style="width: 14%">
						<button class="btn btn-danger" style="width: 100%;font-weight: bold;font-size: 20px;padding: 20px;" onclick="checkCategory('ba')">
							Music Acoustic
						</button>
					</td>
					<td style="width: 14%">
						<button class="btn btn-default" style="width: 100%;font-weight: bold;font-size: 20px;padding: 20px;background-color: #cfabff;border-color: #cfabff" onclick="checkCategory('ta')">
							Senam Taiso
						</button>
					</td>
				</tr>
			</table>
			<!-- <div class="col-xs-2" style="padding: 0px;">
				
			</div>
			<div class="col-xs-2" style="padding: 0px;padding-left: 5px;">
				
			</div>
			<div class="col-xs-2" style="padding: 0px;padding-left: 5px;">
				
			</div>
			<div class="col-xs-2" style="padding: 0px;padding-left: 5px;">
				
			</div>
			<div class="col-xs-2" style="padding: 0px;padding-left: 5px;">
				
			</div>
			<div class="col-xs-2" style="padding: 0px;padding-left: 5px;">
				
			</div> -->
		</div>
		<input type="hidden" name="category" id="category">
		<div class="col-xs-12" id="div_ml" style="margin-top: 10px;">
			<div class="box box-solid">
				<div class="box-header" style="background-color: #e7e7e7;;font-weight: bold;">
					<h3 class="box-title" style="font-weight: bold;">Mobile Legends</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-xs-6">
						<label class="col-xs-3" style="text-align:right">Nama Tim <span class="text-red">*</span></label>
						<div class="col-xs-8">
							<input type="text" name="team_name_ml" id="team_name_ml" style="width: 100%" placeholder="Nama Tim" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">No. HP Ketua Tim <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="number" name="phone_no_ml" id="phone_no_ml" style="width: 100%" placeholder="No. HP Ketua Tim" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;font-size: 12px;">Player 1 (Ketua Tim) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_1_ml" id="player_1_ml" style="width: 100%" placeholder="Masukkan NIK Player 1 (Ketua Tim)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 2 <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_2_ml" id="player_2_ml" style="width: 100%" placeholder="Masukkan NIK Player 2" class="form-control">
						</div>
					</div>
					<div class="col-xs-6">
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 3 <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_3_ml" id="player_3_ml" style="width: 100%" placeholder="Masukkan NIK Player 3" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 4 <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_4_ml" id="player_4_ml" style="width: 100%" placeholder="Masukkan NIK Player 4" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 5 <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_5_ml" id="player_5_ml" style="width: 100%" placeholder="Masukkan NIK Player 5" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 6 (Cadangan)</label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_6_ml" id="player_6_ml" style="width: 100%" placeholder="Masukkan NIK Player 6" class="form-control">
						</div>
						<div class="col-xs-11" style="margin-top: 5px;">
							<button class="btn btn-success pull-right" style="font-weight: bold;" onclick="confirmCompetition('ml')" class="form-control">CONFIRM</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" id="div_ppa" style="margin-top: 10px;">
			<div class="box box-solid">
				<div class="box-header" style="background-color: #008d4c;font-weight: bold;">
					<h3 class="box-title" style="font-weight: bold;color: white;">Penalti Putra</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-xs-6">
						<label class="col-xs-3" style="text-align:right">Nama Tim <span class="text-red">*</span></label>
						<div class="col-xs-8">
							<input type="text" name="team_name_ppa" id="team_name_ppa" style="width: 100%" placeholder="Nama Tim" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Official <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="official_ppa" id="official_ppa" style="width: 100%" placeholder="Masukkan NIK Official" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">No. HP Official <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="number" name="phone_no_ppa" id="phone_no_ppa" style="width: 100%" placeholder="No. HP Official" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 1 (Inti) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_1_ppa" id="player_1_ppa" style="width: 100%" placeholder="Masukkan NIK Player 1 (Inti)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 2 (Inti) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_2_ppa" id="player_2_ppa" style="width: 100%" placeholder="Masukkan NIK Player 2 (Inti)" class="form-control">
						</div>
					</div>
					<div class="col-xs-6">
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 3 (Inti) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_3_ppa" id="player_3_ppa" style="width: 100%" placeholder="Masukkan NIK Player 3 (Inti)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 4 (Inti) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_4_ppa" id="player_4_ppa" style="width: 100%" placeholder="Masukkan NIK Player 4 (Inti)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 5 (Kiper) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_5_ppa" id="player_5_ppa" style="width: 100%" placeholder="Masukkan NIK Player 5 (Kiper)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 6 (Cadangan)</label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_6_ppa" id="player_6_ppa" style="width: 100%" placeholder="Masukkan NIK Player 6 (Cadangan)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 7 (Cadangan)</label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_7_ppa" id="player_7_ppa" style="width: 100%" placeholder="Masukkan NIK Player 7 (Cadangan)" class="form-control">
						</div>
						<div class="col-xs-11" style="margin-top: 5px;">
							<button class="btn btn-success pull-right" style="font-weight: bold;" onclick="confirmCompetition('ppa')" class="form-control">CONFIRM</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" id="div_ppi" style="margin-top: 10px;">
			<div class="box box-solid">
				<div class="box-header" style="background-color: #367fa9;font-weight: bold;">
					<h3 class="box-title" style="font-weight: bold;color: white;">Penalti Putri</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-xs-6">
						<label class="col-xs-3" style="text-align:right">Nama Tim <span class="text-red">*</span></label>
						<div class="col-xs-8">
							<input type="text" name="team_name_ppi" id="team_name_ppi" style="width: 100%" placeholder="Nama Tim" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Official <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="official_ppi" id="official_ppi" style="width: 100%" placeholder="Masukkan NIK Official" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">No. HP Official <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="number" name="phone_no_ppi" id="phone_no_ppi" style="width: 100%" placeholder="No. HP Official" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 1 (Inti) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_1_ppi" id="player_1_ppi" style="width: 100%" placeholder="Masukkan NIK Player 1 (Inti)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 2 (Inti) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_2_ppi" id="player_2_ppi" style="width: 100%" placeholder="Masukkan NIK Player 2 (Inti)" class="form-control">
						</div>
					</div>
					<div class="col-xs-6">
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 3 (Inti) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_3_ppi" id="player_3_ppi" style="width: 100%" placeholder="Masukkan NIK Player 3 (Inti)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 4 (Inti) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_4_ppi" id="player_4_ppi" style="width: 100%" placeholder="Masukkan NIK Player 4 (Inti)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 5 (Kiper) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_5_ppi" id="player_5_ppi" style="width: 100%" placeholder="Masukkan NIK Player 5 (Kiper)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 6 (Cadangan)</label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_6_ppi" id="player_6_ppi" style="width: 100%" placeholder="Masukkan NIK Player 6 (Cadangan)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 7 (Cadangan)</label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_7_ppi" id="player_7_ppi" style="width: 100%" placeholder="Masukkan NIK Player 7 (Cadangan)" class="form-control">
						</div>
						<div class="col-xs-11" style="margin-top: 5px;">
							<button class="btn btn-success pull-right" style="font-weight: bold;" onclick="confirmCompetition('ppi')" class="form-control">CONFIRM</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" id="div_in" style="margin-top: 10px;">
			<div class="box box-solid">
				<div class="box-header" style="background-color: #ec971f;font-weight: bold;">
					<h3 class="box-title" style="font-weight: bold;color: white;">Indiaka</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-xs-6">
						<label class="col-xs-3" style="text-align:right">Nama Tim <span class="text-red">*</span></label>
						<div class="col-xs-8">
							<input type="text" name="team_name_in" id="team_name_in" style="width: 100%" placeholder="Nama Tim" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Official <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="official_in" id="official_in" style="width: 100%" placeholder="Masukkan NIK Official" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">No. HP Official <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="number" name="phone_no_in" id="phone_no_in" style="width: 100%" placeholder="No. HP Official" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 1 (Putra) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_1_in" id="player_1_in" style="width: 100%" placeholder="Masukkan NIK Player 1 (Putra)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 2 (Putra) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_2_in" id="player_2_in" style="width: 100%" placeholder="Masukkan NIK Player 2 (Putra)" class="form-control">
						</div>
					</div>
					<div class="col-xs-6">
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 3 (Putri) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_3_in" id="player_3_in" style="width: 100%" placeholder="Masukkan NIK Player 3 (Putri)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 4 (Putri) <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_4_in" id="player_4_in" style="width: 100%" placeholder="Masukkan NIK Player 4 (Putri)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 5 (Cadangan)</label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_5_in" id="player_5_in" style="width: 100%" placeholder="Masukkan NIK Player 5 (Cadangan)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 6 (Cadangan)</label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_6_in" id="player_6_in" style="width: 100%" placeholder="Masukkan NIK Player 6 (Cadangan)" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Player 7 (Cadangan)</label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_7_in" id="player_7_in" style="width: 100%" placeholder="Masukkan NIK Player 7 (Cadangan)" class="form-control">
						</div>
						<div class="col-xs-11" style="margin-top: 5px;">
							<button class="btn btn-success pull-right" style="font-weight: bold;" onclick="confirmCompetition('in')" class="form-control">CONFIRM</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" id="div_ka" style="margin-top: 10px;">
			<div class="box box-solid">
				<div class="box-header" style="background-color: #00c0ef;font-weight: bold;">
					<h3 class="box-title" style="font-weight: bold;color: white;">Karaoke</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-xs-12">
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Peserta Karaoke <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_1_ka" id="player_1_ka" style="width: 100%" placeholder="Masukkan NIK Peserta" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">No. HP Peserta <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="number" name="phone_no_ka" id="phone_no_ka" style="width: 100%" placeholder="No. HP Peserta" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Lagu yang Dibawakan <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="text" name="song_ka" id="song_ka" style="width: 100%" placeholder="Lagu yang Dibawakan" class="form-control" value="Sayap-Sayap Patah">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Singer (Penyanyi Asli) <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="text" name="singer_ka" id="singer_ka" style="width: 100%" placeholder="Singer (Penyanyi Asli)" class="form-control" value="Rio Irvansyah">
						</div>
						<div class="col-xs-11" style="margin-top: 5px;">
							<button class="btn btn-success pull-right" style="font-weight: bold;" onclick="confirmCompetition('ka')" class="form-control">CONFIRM</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" id="div_ba" style="margin-top: 10px;">
			<div class="box box-solid">
				<div class="box-header" style="background-color: #d73925;font-weight: bold;">
					<h3 class="box-title" style="font-weight: bold;color: white;">Music Acoustic</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-xs-6">
						<label class="col-xs-3" style="text-align:right">Nama Group <span class="text-red">*</span></label>
						<div class="col-xs-8">
							<input type="text" name="team_name_ba" id="team_name_ba" style="width: 100%" placeholder="Nama Group" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">No. HP Anggota <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="number" name="phone_no_ba" id="phone_no_ba" style="width: 100%" placeholder="No. HP Anggota" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Vokalis / Solo <span class="text-red">*</span></label>
						<div class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_1_ba" id="player_1_ba" style="width: 100%" placeholder="Masukkan NIK Vokalis / Solo" class="form-control">
						</div>
						<div class="col-xs-3 col-xs-offset-3">
							<label class="containers">Gitaris 1
							  <input type="checkbox" id="gitaris_1" name="gitaris_1" onclick="changeBand('gitaris_1')">
							  <span class="checkmark"></span>
							</label>
						</div>
						<div class="col-xs-3">
							<label class="containers">Gitaris 2
							  <input type="checkbox" id="gitaris_2" name="gitaris_2" onclick="changeBand('gitaris_2')">
							  <span class="checkmark"></span>
							</label>
						</div>
						<div class="col-xs-3">
							<label class="containers">Bassist
							  <input type="checkbox" id="bassist" name="bassist" onclick="changeBand('bassist')">
							  <span class="checkmark"></span>
							</label>
						</div>
					</div>
					<div class="col-xs-6">
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Lagu yang Dibawakan <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="text" name="song_ba" id="song_ba" style="width: 100%" placeholder="Lagu yang Dibawakan" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Singer (Penyanyi Asli) <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="text" name="singer_ba" id="singer_ba" style="width: 100%" placeholder="Singer (Penyanyi Asli)" class="form-control">
						</div>
						<label id="gitaris_1_a" class="col-xs-3" style="margin-top: 5px;text-align:right;">Gitaris 1</label>
						<div id="gitaris_1_b" class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_2_ba" id="player_2_ba" style="width: 100%" placeholder="Masukkan NIK Gitaris 1" class="form-control">
						</div>
						<label id="gitaris_2_a" class="col-xs-3" style="margin-top: 5px;text-align:right;">Gitaris 2</label>
						<div id="gitaris_2_b" class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_3_ba" id="player_3_ba" style="width: 100%" placeholder="Masukkan NIK Gitaris 2" class="form-control">
						</div>
						<label id="bassist_a" id="" class="col-xs-3" style="margin-top: 5px;text-align:right;">Bassist</label>
						<div id="bassist_b" class="col-xs-8 autocomplete" style="margin-top: 5px;">
							<input type="text" name="player_4_ba" id="player_4_ba" style="width: 100%" placeholder="Masukkan NIK Bassist" class="form-control">
						</div>
						<div class="col-xs-11" style="margin-top: 5px;">
							<button class="btn btn-success pull-right" style="font-weight: bold;" onclick="confirmCompetition('ba')" class="form-control">CONFIRM</button>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-xs-12" id="div_ta" style="margin-top: 10px;">
			<div class="box box-solid">
				<div class="box-header" style="background-color: #cfabff;font-weight: bold;">
					<h3 class="box-title" style="font-weight: bold;color: white;">Senam Taiso</h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					<div class="col-xs-12">
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Office / Produksi <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="text" name="team_name_ta" id="team_name_ta" style="width: 100%" class="form-control" value="{{$ofc}}" readonly="">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Bagian <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="text" name="player_name_ta" id="player_name_ta" style="width: 100%" placeholder="Bagian" class="form-control" readonly value="{{$div_sec}}">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Pendaftar <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="text" name="player_1_ta" id="player_1_ta" style="width: 100%" placeholder="NIK Pendaftar" class="form-control" readonly value="{{$emp->employee_id}} - {{$emp->name}}">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">No. HP Pendaftar <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="number" name="phone_no_ta" id="phone_no_ta" style="width: 100%" placeholder="No. HP Pendaftar" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Lokasi <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="text" name="location_ta" id="location_ta" style="width: 100%" placeholder="Lokasi" class="form-control">
						</div>
						<label class="col-xs-3" style="margin-top: 5px;text-align:right;">Atribut <span class="text-red">*</span></label>
						<div class="col-xs-8" style="margin-top: 5px;">
							<input type="text" name="attribute_ta" id="attribute_ta" style="width: 100%" placeholder="Atribut" class="form-control">
						</div>
						<div class="col-xs-11" style="margin-top: 5px;">
							<button class="btn btn-success pull-right" style="font-weight: bold;" onclick="confirmCompetition('ta')" class="form-control">CONFIRM</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("js/bootstrap-datetimepicker.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var emp = [];
	var emp_putra = [];
	var emp_putri = [];

	var counts = null;

	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear:true,
		});
		$(':input').val('');
		$('#team_name_ta').val('{{$ofc}}');
		$('#player_name_ta').val('{{$div_sec}}');
		$('#player_1_ta').val('{{$emp->employee_id}} - {{$emp->name}}');
		$('body').toggleClass("sidebar-collapse");

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});
		$("#category").val('');

		emp = [];
		emp_putra = [];
		emp_putri = [];
		counts = null;

		$("#div_ml").hide();
		$("#div_ppa").hide();
		$("#div_ppi").hide();
		$("#div_in").hide();
		$("#div_ka").hide();
		$("#div_ba").hide();
		$("#div_ta").hide();

		$("#gitaris_1_a").hide();
        $("#gitaris_1_b").hide();
        $("#gitaris_2_a").hide();
        $("#gitaris_2_b").hide();
        $("#bassist_a").hide();
        $("#bassist_b").hide();

        $("input[name='gitaris_1']").each(function (i) {
            $('#gitaris_1')[i].checked = false;
        });

        $("input[name='gitaris_2']").each(function (i) {
            $('#gitaris_2')[i].checked = false;
        });

        $("input[name='bassist']").each(function (i) {
            $('#bassist')[i].checked = false;
        });

        fetchParticipant();
	});

	function changeBand(values) {
		var pilihan = document.getElementById(values);
		  if (pilihan.checked) {
		    $("#"+values+'_a').show();
        	$("#"+values+'_b').show();
		  } else {
		    $("#"+values+'_a').hide();
        	$("#"+values+'_b').hide();
		  }
		$('#player_2_ba').val('');
	  	$('#player_3_ba').val('');
	  	$('#player_4_ba').val('');
	}

	function checkCategory(values) {
		$(':input').val('');
		$("#div_ml").hide();
		$("#div_ppa").hide();
		$("#div_ppi").hide();
		$("#div_in").hide();
		$("#div_ka").hide();
		$("#div_ba").hide();
		$("#div_ta").hide();
		$('#team_name_ta').val('{{$ofc}}');
		$('#player_name_ta').val('{{$div_sec}}');
		$('#player_1_ta').val('{{$emp->employee_id}} - {{$emp->name}}');
		$("#div_"+values).show();
		$("#category").val(values);
	}

	function fetchParticipant() {
		$('#loading').show();
		$.get('{{ url("fetch/competition/participant") }}', function(result, status, xhr){
			if(result.status){
				emp = [];
				for(var i = 0; i < result.emp.length;i++){
					emp.push(result.emp[i].employee_id+' - '+result.emp[i].name);
				}
				emp_putra = [];
				for(var i = 0; i < result.emp_putra.length;i++){
					emp_putra.push(result.emp_putra[i].employee_id+' - '+result.emp_putra[i].name);
				}
				emp_putri = [];
				for(var i = 0; i < result.emp_putri.length;i++){
					emp_putri.push(result.emp_putri[i].employee_id+' - '+result.emp_putri[i].name);
				}

				autocomplete(document.getElementById("player_1_ml"), emp);
				autocomplete(document.getElementById("player_2_ml"), emp);
				autocomplete(document.getElementById("player_3_ml"), emp);
				autocomplete(document.getElementById("player_4_ml"), emp);
				autocomplete(document.getElementById("player_5_ml"), emp);
				autocomplete(document.getElementById("player_6_ml"), emp);

				autocomplete(document.getElementById("official_ppa"), emp);
				autocomplete(document.getElementById("player_1_ppa"), emp_putra);
				autocomplete(document.getElementById("player_2_ppa"), emp_putra);
				autocomplete(document.getElementById("player_3_ppa"), emp_putra);
				autocomplete(document.getElementById("player_4_ppa"), emp_putra);
				autocomplete(document.getElementById("player_5_ppa"), emp_putra);
				autocomplete(document.getElementById("player_6_ppa"), emp_putra);
				autocomplete(document.getElementById("player_7_ppa"), emp_putra);

				autocomplete(document.getElementById("official_ppi"), emp);
				autocomplete(document.getElementById("player_1_ppi"), emp_putri);
				autocomplete(document.getElementById("player_2_ppi"), emp_putri);
				autocomplete(document.getElementById("player_3_ppi"), emp_putri);
				autocomplete(document.getElementById("player_4_ppi"), emp_putri);
				autocomplete(document.getElementById("player_5_ppi"), emp_putri);
				autocomplete(document.getElementById("player_6_ppi"), emp_putri);
				autocomplete(document.getElementById("player_7_ppi"), emp_putri);

				autocomplete(document.getElementById("official_in"), emp);
				autocomplete(document.getElementById("player_1_in"), emp_putra);
				autocomplete(document.getElementById("player_2_in"), emp_putra);
				autocomplete(document.getElementById("player_3_in"), emp_putri);
				autocomplete(document.getElementById("player_4_in"), emp_putri);
				autocomplete(document.getElementById("player_5_in"), emp);
				autocomplete(document.getElementById("player_6_in"), emp);
				autocomplete(document.getElementById("player_7_in"), emp);

				autocomplete(document.getElementById("player_1_ka"), emp);

				autocomplete(document.getElementById("player_1_ba"), emp);
				autocomplete(document.getElementById("player_2_ba"), emp);
				autocomplete(document.getElementById("player_3_ba"), emp);
				autocomplete(document.getElementById("player_4_ba"), emp);

				$('#loading').hide();
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		})
	}

	function confirmCompetition(category) {
		$('#loading').show();
		var cat = '';
		var data = null;
		var players = [];
		if (category == 'ml') {
			cat = 'Mobile Legends';
			var team_name = $('#team_name_ml').val();
			var phone_no = $('#phone_no_ml').val();
			var player_1 = $('#player_1_ml').val();
			var player_2 = $('#player_2_ml').val();
			var player_3 = $('#player_3_ml').val();
			var player_4 = $('#player_4_ml').val();
			var player_5 = $('#player_5_ml').val();
			var player_6 = $('#player_6_ml').val();

			players.push(player_1);
			players.push(player_2);
			players.push(player_3);
			players.push(player_4);
			players.push(player_5);
			if (player_6 != '') {
				players.push(player_6);
			}

			if (team_name == '' || phone_no == '' || player_1 == '' || player_2 == '' || player_3 == '' || player_4 == '' || player_5 == '') {
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!','Lengkapi Data Anda.');
				return false;
			}

			data = {
				category:cat,
				team_name:team_name,
				phone_no:phone_no,
				player_1:player_1,
				player_2:player_2,
				player_3:player_3,
				player_4:player_4,
				player_5:player_5,
				player_6:player_6,
				players:players,
			}
		}else if (category == 'ppa') {
			cat = 'Penalti Putra';
			var team_name = $('#team_name_ppa').val();
			var official = $('#official_ppa').val();
			var phone_no = $('#phone_no_ppa').val();
			var player_1 = $('#player_1_ppa').val();
			var player_2 = $('#player_2_ppa').val();
			var player_3 = $('#player_3_ppa').val();
			var player_4 = $('#player_4_ppa').val();
			var player_5 = $('#player_5_ppa').val();
			var player_6 = $('#player_6_ppa').val();
			var player_7 = $('#player_7_ppa').val();

			players.push(official);
			players.push(player_1);
			players.push(player_2);
			players.push(player_3);
			players.push(player_4);
			players.push(player_5);
			if (player_6 != '') {
				players.push(player_6);
			}
			if (player_7 != '') {
				players.push(player_7);
			}

			if (team_name == '' || official == '' || phone_no == '' || player_1 == '' || player_2 == '' || player_3 == '' || player_4 == '' || player_5 == '') {
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!','Lengkapi Data Anda.');
				return false;
			}

			data = {
				category:cat,
				team_name:team_name,
				official:official,
				phone_no:phone_no,
				player_1:player_1,
				player_2:player_2,
				player_3:player_3,
				player_4:player_4,
				player_5:player_5,
				player_6:player_6,
				player_7:player_7,
				players:players,
			}
		}else if (category == 'ppi') {
			cat = 'Penalti Putri';
			var team_name = $('#team_name_ppi').val();
			var official = $('#official_ppi').val();
			var phone_no = $('#phone_no_ppi').val();
			var player_1 = $('#player_1_ppi').val();
			var player_2 = $('#player_2_ppi').val();
			var player_3 = $('#player_3_ppi').val();
			var player_4 = $('#player_4_ppi').val();
			var player_5 = $('#player_5_ppi').val();
			var player_6 = $('#player_6_ppi').val();
			var player_7 = $('#player_7_ppi').val();

			players.push(official);
			players.push(player_1);
			players.push(player_2);
			players.push(player_3);
			players.push(player_4);
			players.push(player_5);
			if (player_6 != '') {
				players.push(player_6);
			}
			if (player_7 != '') {
				players.push(player_7);
			}

			if (team_name == '' || official == '' || phone_no == '' || player_1 == '' || player_2 == '' || player_3 == '' || player_4 == '' || player_5 == '') {
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!','Lengkapi Data Anda.');
				return false;
			}

			data = {
				category:cat,
				team_name:team_name,
				official:official,
				phone_no:phone_no,
				player_1:player_1,
				player_2:player_2,
				player_3:player_3,
				player_4:player_4,
				player_5:player_5,
				player_6:player_6,
				player_7:player_7,
				players:players,
			}

		}else if (category == 'in') {
			cat = 'Indiaka';
			var team_name = $('#team_name_in').val();
			var official = $('#official_in').val();
			var phone_no = $('#phone_no_in').val();
			var player_1 = $('#player_1_in').val();
			var player_2 = $('#player_2_in').val();
			var player_3 = $('#player_3_in').val();
			var player_4 = $('#player_4_in').val();
			var player_5 = $('#player_5_in').val();
			var player_6 = $('#player_6_in').val();
			var player_7 = $('#player_7_in').val();

			players.push(official);
			players.push(player_1);
			players.push(player_2);
			players.push(player_3);
			players.push(player_4);
			if (player_5 != '') {
				players.push(player_5);
			}
			if (player_6 != '') {
				players.push(player_6);
			}
			if (player_7 != '') {
				players.push(player_7);
			}

			if (team_name == '' || official == '' || phone_no == '' || player_1 == '' || player_2 == '' || player_3 == '' || player_4 == '') {
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!','Lengkapi Data Anda.');
				return false;
			}

			data = {
				category:cat,
				team_name:team_name,
				official:official,
				phone_no:phone_no,
				player_1:player_1,
				player_2:player_2,
				player_3:player_3,
				player_4:player_4,
				player_5:player_5,
				player_6:player_6,
				player_7:player_7,
				players:players,
			}

		}else if (category == 'ka') {
			cat = 'Karaoke';
			var phone_no = $('#phone_no_ka').val();
			var player_1 = $('#player_1_ka').val();
			var song = $('#song_ka').val();
			var singer = $('#singer_ka').val();

			if (song == '' || singer == '' || phone_no == '' || player_1 == '') {
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!','Lengkapi Data Anda.');
				return false;
			}

			players.push(player_1);

			data = {
				category:cat,
				phone_no:phone_no,
				player_1:player_1,
				song:song,
				singer:singer,
				players:players,
			}

		}else if (category == 'ba') {
			cat = 'Music Acoustic';
			var team_name = $('#team_name_ba').val();
			var phone_no = $('#phone_no_ba').val();
			var player_1 = $('#player_1_ba').val();
			var song = $('#song_ba').val();
			var singer = $('#singer_ba').val();
			var player_2 = $('#player_2_ba').val();
			var player_3 = $('#player_3_ba').val();
			var player_4 = $('#player_4_ba').val();

			players.push(player_1);
			if (player_2 != '') {
				players.push(player_2);
			}
			if (player_3 != '') {
				players.push(player_3);
			}
			if (player_4 != '') {
				players.push(player_4);
			}

			if (phone_no == '' || team_name == '' ||song == '' || singer == '' ||  player_1 == '') {
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!','Lengkapi Data Anda.');
				return false;
			}

			data = {
				category:cat,
				team_name:team_name,
				phone_no:phone_no,
				player_1:player_1,
				player_2:player_2,
				player_3:player_3,
				player_4:player_4,
				song:song,
				singer:singer,
				players:players,
			}
		}else if (category == 'ta') {
			cat = 'Senam Taiso';
			var team_name = $('#team_name_ta').val();
			var player_name = $('#player_name_ta').val();
			var phone_no = $('#phone_no_ta').val();
			var locs = $('#location_ta').val();
			var attribute = $('#attribute_ta').val();
			var player_1 = $('#player_1_ta').val();

			if (player_name == '' || team_name == '' || phone_no == '' || player_1 == '' || locs == '' || attribute == '') {
				audio_error.play();
				$('#loading').hide();
				openErrorGritter('Error!','Lengkapi Data Anda.');
				return false;
			}

			data = {
				category:cat,
				team_name:team_name,
				player_name:player_name,
				phone_no:phone_no,
				player_1:player_1,
				location:locs,
				attribute:attribute,
				players:players,
			}
		}

		if (data != null) {
			$.post('{{ url("input/competition/registration") }}',data, function(result, status, xhr){
			if(result.status){
				location.reload();
				$('#loading').hide();
				openSuccessGritter('Success!','Terima Kasih telah mendaftar di YMPI COMPETITION 2022');
			}else{
				$('#loading').hide();
				openErrorGritter('Error!',result.message);
			}
		})
		}else{
			audio_error.play();
			$('#loading').hide();
			openErrorGritter('Error!','Lengkapi Data Anda.');
			return false;
		}
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

	function autocomplete(inp, arr) {
	  /*the autocomplete function takes two arguments,
	  the text field element and an array of possible autocompleted values:*/
	  var currentFocus;
	  /*execute a function when someone writes in the text field:*/
	  inp.addEventListener("input", function(e) {
	      var a, b, i, val = this.value;
	      closeAllLists();
	      if (!val) { return false;}
	      currentFocus = -1;
	      /*create a DIV element that will contain the items (values):*/
	      a = document.createElement("DIV");
	      a.setAttribute("id", this.id + "autocomplete-list");
	      a.setAttribute("class", "autocomplete-items");
	      /*append the DIV element as a child of the autocomplete container:*/
	      this.parentNode.appendChild(a);
	      /*for each item in the array...*/
	      for (i = 0; i < arr.length; i++) {
	        /*check if the item starts with the same letters as the text field value:*/
	        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
	          /*create a DIV element for each matching element:*/
	          b = document.createElement("DIV");
	          /*make the matching letters bold:*/
	          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
	          b.innerHTML += arr[i].substr(val.length);
	          /*insert a input field that will hold the current array item's value:*/
	          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
	          /*execute a function when someone clicks on the item value (DIV element):*/
	          b.addEventListener("click", function(e) {
	              /*insert the value for the autocomplete text field:*/
	              inp.value = this.getElementsByTagName("input")[0].value;
	              /*close the list of autocompleted values,
	              (or any other open lists of autocompleted values:*/
	              closeAllLists();
	          });
	          a.appendChild(b);
	        }
	      }
	  });
	  /*execute a function presses a key on the keyboard:*/
	  inp.addEventListener("keydown", function(e) {
	      var x = document.getElementById(this.id + "autocomplete-list");
	      if (x) x = x.getElementsByTagName("div");
	      if (e.keyCode == 40) {
	        /*If the arrow DOWN key is pressed,
	        increase the currentFocus variable:*/
	        currentFocus++;
	        /*and and make the current item more visible:*/
	        addActive(x);
	      } else if (e.keyCode == 38) { //up
	        /*If the arrow UP key is pressed,
	        decrease the currentFocus variable:*/
	        currentFocus--;
	        /*and and make the current item more visible:*/
	        addActive(x);
	      } else if (e.keyCode == 13) {
	        /*If the ENTER key is pressed, prevent the form from being submitted,*/
	        e.preventDefault();
	        if (currentFocus > -1) {
	          /*and simulate a click on the "active" item:*/
	          if (x) x[currentFocus].click();
	        }
	      }
	  });
	  function addActive(x) {
	    /*a function to classify an item as "active":*/
	    if (!x) return false;
	    /*start by removing the "active" class on all items:*/
	    removeActive(x);
	    if (currentFocus >= x.length) currentFocus = 0;
	    if (currentFocus < 0) currentFocus = (x.length - 1);
	    /*add class "autocomplete-active":*/
	    x[currentFocus].classList.add("autocomplete-active");
	  }
	  function removeActive(x) {
	    /*a function to remove the "active" class from all autocomplete items:*/
	    for (var i = 0; i < x.length; i++) {
	      x[i].classList.remove("autocomplete-active");
	    }
	  }
	  function closeAllLists(elmnt) {
	    /*close all autocomplete lists in the document,
	    except the one passed as an argument:*/
	    var x = document.getElementsByClassName("autocomplete-items");
	    for (var i = 0; i < x.length; i++) {
	      if (elmnt != x[i] && elmnt != inp) {
	        x[i].parentNode.removeChild(x[i]);
	      }
	    }
	  }
	  /*execute a function when someone clicks in the document:*/
	  document.addEventListener("click", function (e) {
	      closeAllLists(e.target);
	  });
	}

	Highcharts.createElement('link', {
			href: '{{ url("fonts/UnicaOne.css")}}',
			rel: 'stylesheet',
			type: 'text/css'
		}, null, document.getElementsByTagName('head')[0]);

		Highcharts.theme = {
			colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
			'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
			chart: {
				backgroundColor: {
					linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
					stops: [
					[0, '#2a2a2b'],
					[1, '#3e3e40']
					]
				},
				style: {
					fontFamily: 'sans-serif'
				},
				plotBorderColor: '#606063'
			},
			title: {
				style: {
					color: '#E0E0E3',
					textTransform: 'uppercase',
					fontSize: '20px'
				}
			},
			subtitle: {
				style: {
					color: '#E0E0E3',
					textTransform: 'uppercase'
				}
			},
			xAxis: {
				gridLineColor: '#707073',
				labels: {
					style: {
						color: '#E0E0E3'
					}
				},
				lineColor: '#707073',
				minorGridLineColor: '#505053',
				tickColor: '#707073',
				title: {
					style: {
						color: '#A0A0A3'

					}
				}
			},
			yAxis: {
				gridLineColor: '#707073',
				labels: {
					style: {
						color: '#E0E0E3'
					}
				},
				lineColor: '#707073',
				minorGridLineColor: '#505053',
				tickColor: '#707073',
				tickWidth: 1,
				title: {
					style: {
						color: '#A0A0A3'
					}
				}
			},
			tooltip: {
				backgroundColor: 'rgba(0, 0, 0, 0.85)',
				style: {
					color: '#F0F0F0'
				}
			},
			plotOptions: {
				series: {
					dataLabels: {
						color: 'white'
					},
					marker: {
						lineColor: '#333'
					}
				},
				boxplot: {
					fillColor: '#505053'
				},
				candlestick: {
					lineColor: 'white'
				},
				errorbar: {
					color: 'white'
				}
			},
			legend: {
				itemStyle: {
					color: '#E0E0E3'
				},
				itemHoverStyle: {
					color: '#FFF'
				},
				itemHiddenStyle: {
					color: '#606063'
				}
			},
			credits: {
				style: {
					color: '#666'
				}
			},
			labels: {
				style: {
					color: '#707073'
				}
			},

			drilldown: {
				activeAxisLabelStyle: {
					color: '#F0F0F3'
				},
				activeDataLabelStyle: {
					color: '#F0F0F3'
				}
			},

			navigation: {
				buttonOptions: {
					symbolStroke: '#DDDDDD',
					theme: {
						fill: '#505053'
					}
				}
			},

			rangeSelector: {
				buttonTheme: {
					fill: '#505053',
					stroke: '#000000',
					style: {
						color: '#CCC'
					},
					states: {
						hover: {
							fill: '#707073',
							stroke: '#000000',
							style: {
								color: 'white'
							}
						},
						select: {
							fill: '#000003',
							stroke: '#000000',
							style: {
								color: 'white'
							}
						}
					}
				},
				inputBoxBorderColor: '#505053',
				inputStyle: {
					backgroundColor: '#333',
					color: 'silver'
				},
				labelStyle: {
					color: 'silver'
				}
			},

			navigator: {
				handles: {
					backgroundColor: '#666',
					borderColor: '#AAA'
				},
				outlineColor: '#CCC',
				maskFill: 'rgba(255,255,255,0.1)',
				series: {
					color: '#7798BF',
					lineColor: '#A6C7ED'
				},
				xAxis: {
					gridLineColor: '#505053'
				}
			},

			scrollbar: {
				barBackgroundColor: '#808083',
				barBorderColor: '#808083',
				buttonArrowColor: '#CCC',
				buttonBackgroundColor: '#606063',
				buttonBorderColor: '#606063',
				rifleColor: '#FFF',
				trackBackgroundColor: '#404043',
				trackBorderColor: '#404043'
			},

			legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
			background2: '#505053',
			dataLabelsColor: '#B0B0B3',
			textColor: '#C0C0C0',
			contrastTextColor: '#F0F0F3',
			maskColor: 'rgba(255,255,255,0.3)'
		};
		Highcharts.setOptions(Highcharts.theme);



</script>
@endsection