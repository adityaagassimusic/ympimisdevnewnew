@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">
<style type="text/css">
	#detailTable > tbody > tr:hover{
		/*cursor: pointer;*/
		background-color: #7dfa8c !important;
	}
	#resumeTable > tbody > tr > td:hover{
		cursor: pointer;
		background-color: #7dfa8c !important;
	}
	#resumeCategoryTable > tbody > tr:hover{
		cursor: pointer;
		background-color: #7dfa8c !important;
	}
	table.table-bordered{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid black;
		vertical-align: middle;
		padding:  2px 5px 2px 5px;
	}
	#detailTable > tbody > tr > td{
		height: 40px;
	}
	input::-webkit-outer-spin-button, input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
	}
	input[type=number] {
		-moz-appearance:textfield;
	}

	.nmpd-grid {
		border: none; padding: 20px;
	}
	.nmpd-grid>tbody>tr>td {
		border: none;
	}
	#loading { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		@if($role_user->role_code == 'S-MIS' || $role_user->role_code == 'C-MIS' || $role_user->role_code == 'MIS' || $role_user->role_code == 'S-CHM' || $role_user->role_code == 'C-CHM' || $role_user->role_code == 'L-CHM' || $role_user->role_code == 'M') 	

		<a class="btn btn-info pull-right" style="margin-left: 5px" href="{{url('index/sosialisasi/shedule/sds/')}}"> 
			Sosialisasi <i class="fa fa-users"></i>
		</a>
		<a class="btn btn-success pull-right" style="margin-left: 5px; background-color: #BA55D3; border-color: black;" onclick="modalCreate();"> Create New <i class="fa fa-pencil-square-o"></i></a>
		@endif

	</h1>
</section>
@endsection

@section('content')
<section class="content" style="font-size: 0.9vw;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<input type="hidden" id="document_id">
		<input type="hidden" id="category">
		@if($role_user->role_code == 'S-MIS' || $role_user->role_code == 'C-MIS' || $role_user->role_code == 'MIS' || $role_user->role_code == 'S-CHM' || $role_user->role_code == 'C-CHM' || $role_user->role_code == 'L-CHM' || $role_user->role_code == 'M')

		

		<div class="xol-xs-12 col-md-2 col-lg-1">
			<table id="resumeTable" class="table table-bordered table-striped table-hover" style="margin-bottom: 20px; height: 20vh;">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
						<th style="text-align: left;">Status</th>
						<th style="text-align: right;">Count</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td onclick="fetchStatus('All')" style="width: 1%; font-weight: bold; font-size: 1.2vw;">All</td>
						<td onclick="fetchStatus('All')" id="count_all" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw;"></td>
					</tr>
					<tr>
						<td onclick="fetchStatus('Active')" style="width: 1%; background-color: RGB(204,255,255); font-weight: bold; font-size: 1.2vw;">Active</td>
						<td onclick="fetchStatus('Active')" id="count_active" style="width: 1%; text-align: right; font-weight: bold; background-color: RGB(204,255,255); font-size: 1.2vw;"></td>
					</tr>
					<tr>
						<td onclick="fetchStatus('AtRisk')" style="width: 1%; background-color: orange; font-weight: bold; font-size: 1.2vw;">AtRisk</td>
						<td onclick="fetchStatus('AtRisk')" id="count_atrisk" style="width: 1%; text-align: right; font-weight: bold; background-color: orange; font-size: 1.2vw;"></td>
					</tr>
					<tr>
						<td onclick="fetchStatus('Expired')" style="width: 1%; background-color: #ffccff; font-weight: bold; font-size: 1.2vw;">Expired</td>
						<td onclick="fetchStatus('Expired')" id="count_expired" style="width: 1%; text-align: right; font-weight: bold; background-color: #ffccff; font-size: 1.2vw;"></td>
					</tr>
				</tbody>
			</table>
		</div>

		<div id="container5" class="xol-xs-12 col-md-2 col-lg-3">
		</div>

		<div id="container1" class="xol-xs-12 col-md-8 col-lg-8">
		</div>

		@endif

		@if($role_user->role_code != 'S-MIS' || $role_user->role_code != 'C-MIS' || $role_user->role_code != 'MIS' || $role_user->role_code == 'S-CHM' || $role_user->role_code == 'C-CHM' || $role_user->role_code == 'L-CHM' || $role_user->role_code != 'M')


		<div id="container1" class="xol-xs-12 col-md-8 col-lg-12">
		</div>
		@endif


		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<table style="width: 100%;">
						<tbody>
							<tr style="">
								<td style="text-align: center; width: 5%; font-weight: bold;" rowspan="3">CATATAN SAFETY DATA SHEET</td>
							</tr>
						</tbody>
					</table>
					<hr style="margin-top: 10px; margin-bottom: 10px;">

					<table id="tableDocument" class="table table-bordered table-striped table-hover" >
						<thead style="background-color: #9932CC; color: white;">
							<tr>
								<th style="width: 1%; text-align: center;" rowspan="2">ID</th>
								<th style="width: 1%; text-align: left;" rowspan="2">Item Code</th>
								<th style="width: 3%; text-align: left;" rowspan="2">Judul</th>
								<th style="width: 1%; text-align: center;" rowspan="2">Rev.</th>
								<th style="width: 1.5%; text-align: center;" colspan="3">Valid</th>
								<th style="width: 1%; text-align: center;" rowspan="2">Dokumen <i class="fa fa-paperclip"></i></th>
								<th style="width: 3%; text-align: right;" rowspan="2">Last Update</th>
								<th style="width: 3%; text-align: left;" rowspan="2">Status</th>
							</tr>
							<tr>
								<th style="width: 0.5%; text-align: right; border:1px solid black;">From</th>
								<th style="width: 0.5%; text-align: right; border:1px solid black;">To</th>
								<th style="width: 0.5%; text-align: right; border:1px solid black;">Diff</th>
							</tr>
						</thead>
						<tbody id="tableDocumentBody">
						</tbody>
					</table>

				</div>
			</div>
		</div>

		
		@if($role_user->role_code == 'S-MIS' || $role_user->role_code == 'C-MIS' || $role_user->role_code == 'MIS' || $role_user->role_code == 'S-CHM' || $role_user->role_code == 'C-CHM' || $role_user->role_code == 'L-CHM' || $role_user->role_code == 'M')

		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<table style="width: 100%;">
						<tbody>
							<tr style="">
								<td style="text-align: center; width: 5%; font-weight: bold;" rowspan="3">DISTRIBUSI DOKUMEN SAFETY DATA SHEET</td>
							</tr>
						</tbody>
					</table>
					<hr style="margin-top: 10px; margin-bottom: 10px;">
					<div class="col-xs-12" style="overflow-x: scroll; padding-top: 20px; padding-bottom:20px;">
						<table class="table table-hover table-bordered table-striped" id="tableResume1" style="overflow-y: scroll;">
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
	@endif
	

	<div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<center>
						<h3 style="background-color: #BA55D3; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
							Tambahkan Dokumen Safety Data Sheet Baru<br>
						</h3>
					</center>
					<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
						<form class="form-horizontal">
							<div class="col-md-12">
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tanggal Pembuatan<span class="text-red">*</span> :</label>

									<div class="col-sm-5">
										<div class="input-group date">
											<div class="input-group-addon">	
												<i class="fa fa-calendar"></i>
											</div>
											<input type="text" class="form-control pull-right datepicker"  value="<?= date('d-M-Y') ?>" disabled="">
											<input type="hidden" class="form-control pull-right"  value="{{date('Y-m-d')}}" id="submission_date" name="submission_date">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Item Code<span class="text-red">*</span> :</label>
									<div class="col-sm-7">
										<select class="form-control select2" id="createGmc" onchange="getDescription(this.value)" data-placeholder="Select GMC" style="width: 100%;">
											<option value=""></option>
											@foreach($gmc as $gmcs)
											<option value="{{$gmcs[0]}}_{{$gmcs[1]}}_{{ $gmcs[2] }}">{{ $gmcs[2] }}-{{ $gmcs[0] }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Description<span class="text-red">*</span> :</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" placeholder="Decription Material" id="createDescMaterial" readonly>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Judul Dokumen<span class="text-red">*</span> :</label>
									<div class="col-sm-8">
										<input type="text" class="form-control" placeholder="Enter Document Title" id="createTitle">
									</div>
								</div>

								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">No. Revisi<span class="text-red">*</span> :</label>
									<div class="col-sm-2">
										<div class="input-group">
											<input type="text" class="numpad form-control" placeholder="No Revise" id="createVersion">
										</div>
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tanggal Revisi<span class="text-red">*</span> :</label>
									<div class="col-sm-3">
										<input type="text" class="form-control datepicker" id="createVersionDate" placeholder="   Select Date">
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">SDS Asli<span class="text-red">*</span> :</label>
									<div class="col-sm-5">
										<input type="file" id="createSdsAsli" name="createSdsAsli">
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">SDS Format YMPI<span class="text-red">*</span> :</label>
									<div class="col-sm-5">
										<input type="file" id="createSdsYmpi" name="createSdsYmpi">
									</div>
								</div>

								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Distribusi<span class="text-red">*</span> :</label>
									<div class="col-sm-9">
										<label class="containers" onchange="changeReturn('QA','1')"><a style="border-color: black; color: black;" id="btn_QA" class="btn btn-sm"><input type="checkbox" id="add_return_or_not1" name="add_return_or_not1" value="QA" hidden>QA</a>
										</label>
										<label class="containers" onchange="changeReturn('PLT','2')"><a style="border-color: black; color: black;" id="btn_PLT" class="btn btn-sm"><input type="checkbox" id="add_return_or_not2" name="add_return_or_not2" value="PLT" hidden>PLT</a>
										</label>
										<label class="containers" onchange="changeReturn('PNT','3')"><a style="border-color: black; color: black;" id="btn_PNT" class="btn btn-sm"><input type="checkbox" id="add_return_or_not3" name="add_return_or_not3" value="PNT" hidden>PNT</a>
										</label>
										<label class="containers" onchange="changeReturn('BUFF','4')"><a style="border-color: black; color: black;" id="btn_BUFF" class="btn btn-sm"><input type="checkbox" id="add_return_or_not4" name="add_return_or_not4" value="BUFF" hidden>BUFF</a>
										</label>
										<label class="containers" onchange="changeReturn('SLD','5')"><a style="border-color: black; color: black;" id="btn_SLD" class="btn btn-sm"><input type="checkbox" id="add_return_or_not5" name="add_return_or_not5" value="SLD" hidden>SLD</a>
										</label>
										<label class="containers" onchange="changeReturn('HTS','6')"><a style="border-color: black; color: black;" id="btn_HTS" class="btn btn-sm"><input type="checkbox" id="add_return_or_not6" name="add_return_or_not6" value="HTS" hidden>HTS</a>
										</label>
										<label class="containers" onchange="changeReturn('ASSY_SAX','7')"><a style="border-color: black; color: black;" id="btn_ASSY_SAX" class="btn btn-sm"><input type="checkbox" id="add_return_or_not7" name="add_return_or_not7" value="ASSY_SAX" hidden>ASSY SAX</a>
										</label>
										<label class="containers" onchange="changeReturn('SUB_ASSY_SAX','8')"><a style="border-color: black; color: black;" id="btn_SUB_ASSY_SAX" class="btn btn-sm"><input type="checkbox" id="add_return_or_not8" name="add_return_or_not8" value="SUB_ASSY_SAX" hidden>SUB ASSY SAX</a>
										</label>
										<label class="containers" onchange="changeReturn('ASSY_FL','9')"><a style="border-color: black; color: black;" id="btn_ASSY_FL" class="btn btn-sm"><input type="checkbox" id="add_return_or_not9" name="add_return_or_not9" value="ASSY_FL" hidden>ASSY_FL</a>
										</label>
										<label class="containers" onchange="changeReturn('SUB_ASSY_FL','10')"><a style="border-color: black; color: black;" id="btn_SUB_ASSY_FL" class="btn btn-sm"><input type="checkbox" id="add_return_or_not10" name="add_return_or_not10" value="SUB_ASSY_FL" hidden>SUB ASSY FL</a>
										</label>
										<label class="containers" onchange="changeReturn('ASSY_CL','11')"><a style="border-color: black; color: black;" id="btn_ASSY_CL" class="btn btn-sm"><input type="checkbox" id="add_return_or_not11" name="add_return_or_not11" value="ASSY_CL" hidden>ASSY CL</a>
										</label>
										<label class="containers" onchange="changeReturn('SUB_ASSY_CL','12')"><a style="border-color: black; color: black;" id="btn_SUB_ASSY_CL" class="btn btn-sm"><input type="checkbox" id="add_return_or_not12" name="add_return_or_not12" value="SUB_ASSY_CL" hidden>SUB ASSY CL</a>
										</label>
										<label class="containers" onchange="changeReturn('RP','13')"><a style="border-color: black; color: black;" id="btn_RP" class="btn btn-sm"><input type="checkbox" id="add_return_or_not13" name="add_return_or_not13" value="RP" hidden>RP</a>
										</label>
										<label class="containers" onchange="changeReturn('BPP_SAX','14')"><a style="border-color: black; color: black;" id="btn_BPP_SAX" class="btn btn-sm"><input type="checkbox" id="add_return_or_not14" name="add_return_or_not14" value="BPP_SAX" hidden>BPP SAX</a>
										</label>
										<label class="containers" onchange="changeReturn('BPP_FL','15')"><a style="border-color: black; color: black;" id="btn_BPP_FL" class="btn btn-sm"><input type="checkbox" id="add_return_or_not15" name="add_return_or_not15" value="BPP_FL" hidden>BPP FL</a>
										</label>
										<label class="containers" onchange="changeReturn('CLBODY','16')"><a style="border-color: black; color: black;" id="btn_CLBODY" class="btn btn-sm"><input type="checkbox" id="add_return_or_not16" name="add_return_or_not16" value="CLBODY" hidden>CLBODY</a>
										</label>
										<label class="containers" onchange="changeReturn('CASE','17')"><a style="border-color: black; color: black;" id="btn_CASE" class="btn btn-sm"><input type="checkbox" id="add_return_or_not17" name="add_return_or_not17" value="CASE" hidden>CASE</a>
										</label>
										<label class="containers" onchange="changeReturn('TANPO','18')"><a style="border-color: black; color: black;" id="btn_TANPO" class="btn btn-sm"><input type="checkbox" id="add_return_or_not18" name="add_return_or_not18" value="TANPO" hidden>TANPO</a>
										</label>
										<label class="containers" onchange="changeReturn('PNC','19')"><a style="border-color: black; color: black;" id="btn_PNC" class="btn btn-sm"><input type="checkbox" id="add_return_or_not19" name="add_return_or_not19" value="PNC" hidden>PNC</a>
										</label>
										<label class="containers" onchange="changeReturn('ASSY_REC','20')"><a style="border-color: black; color: black;" id="btn_ASSY_REC" class="btn btn-sm"><input type="checkbox" id="add_return_or_not20" name="add_return_or_not20" value="ASSY_REC" hidden>ASSY REC</a>
										</label>
										<label class="containers" onchange="changeReturn('REC_INJ','21')"><a style="border-color: black; color: black;" id="btn_REC_INJ" class="btn btn-sm"><input type="checkbox" id="add_return_or_not21" name="add_return_or_not21" value="REC_INJ" hidden>REC INJ</a>
										</label>
										<label class="containers" onchange="changeReturn('WRS','22')"><a style="border-color: black; color: black;" id="btn_WRS" class="btn btn-sm"><input type="checkbox" id="add_return_or_not22" name="add_return_or_not22" value="WRS" hidden>WRS</a>
										</label>
										<label class="containers" onchange="changeReturn('MNT','23')"><a style="border-color: black; color: black;" id="btn_MNT" class="btn btn-sm"><input type="checkbox" id="add_return_or_not23" name="add_return_or_not23" value="MNT" hidden>MNT</a>
										</label>
										<label class="containers" onchange="changeReturn('MLD','24')"><a style="border-color: black; color: black;" id="btn_MLD" class="btn btn-sm"><input type="checkbox" id="add_return_or_not24" name="add_return_or_not24" value="MLD" hidden>MLD</a>
										</label>
										<label class="containers" onchange="changeReturn('PRESS','25')"><a style="border-color: black; color: black;" id="btn_PRESS" class="btn btn-sm"><input type="checkbox" id="add_return_or_not25" name="add_return_or_not25" value="PRESS" hidden>PRESS</a>
										</label>
										<label class="containers" onchange="changeReturn('MACHINING','26')"><a style="border-color: black; color: black;" id="btn_MACHINING" class="btn btn-sm"><input type="checkbox" id="add_return_or_not26" name="add_return_or_not26" value="MACHINING" hidden>MACHINING</a>
										</label>
										<label class="containers" onchange="changeReturn('SENBAN','27')"><a style="border-color: black; color: black;" id="btn_SENBAN" class="btn btn-sm"><input type="checkbox" id="add_return_or_not27" name="add_return_or_not27" value="SENBAN" hidden>SENBAN</a>
										</label>
										<label class="containers" onchange="changeReturn('SND','28')"><a style="border-color: black; color: black;" id="btn_SND" class="btn btn-sm"><input type="checkbox" id="add_return_or_not28" name="add_return_or_not28" value="SND" hidden>SND</a>
										</label>
										<label class="containers" onchange="changeReturn('GA','29')"><a style="border-color: black; color: black;" id="btn_GA" class="btn btn-sm"><input type="checkbox" id="add_return_or_not29" name="add_return_or_not29" value="GA" hidden>GA</a>
										</label>
										<label class="containers" onchange="changeReturn('HR','30')"><a style="border-color: black; color: black;" id="btn_HR" class="btn btn-sm"><input type="checkbox" id="add_return_or_not30" name="add_return_or_not30" value="HR" hidden>HR</a>
										</label>
										<label class="containers" onchange="changeReturn('MOUTHPIECE','31')"><a style="border-color: black; color: black;" id="btn_MOUTHPIECE" class="btn btn-sm"><input type="checkbox" id="add_return_or_not31" name="add_return_or_not31" value="MOUTHPIECE" hidden>MOUTHPIECE</a>
										</label>
									</div>
								</div>
							</div>
						</form>
						<div class="col-md-12">
							<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
							<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="inputDocument()">SIMPAN</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalEdit" data-backdrop="static" data-keyboard="false">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<center>
						<h3 style="background-color: #BA55D3; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
							Perbaharui Data Dokumen<br>
						</h3>
					</center>

					<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
						<form class="form-horizontal">
							<div class="col-md-12">
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">GMC Material<span class="text-red">*</span> :</label>
									<div class="col-sm-5">
										<select class="form-control" id="editGmc" name="editGmc" onchange="getEditDescription(this.value)" data-placeholder="Select GMC Material" style="width: 100%;">
											<option value=""></option>
											@foreach($gmc as $gmc)
											<option value="{{$gmc[0]}}" decs="{{$gmc[1]}}" types="{{$gmc[2]}}">{{ $gmc[2] }}-{{ $gmc[0] }}</option>
											@endforeach
										</select>
									</div>
								</div>

								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Description<span class="text-red">*</span> :</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" placeholder="Decription Material" id="editDescMaterial" readonly>
										<input type="hidden" id="doc_ids">
									</div>
								</div>
								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Judul Dokumen<span class="text-red">*</span> :</label>
									<div class="col-sm-5">
										<input type="text" class="form-control" placeholder="Enter Document Title" id="editTitle">
									</div>
								</div>

								<div class="form-group">
									<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Distribusi<span class="text-red">*</span> :</label>
									<div class="col-sm-9" id="distribusi_lokasi">

									</div>
								</div>
							</div>
						</div>
					</form>
					<div class="col-md-12" style="padding-bottom: 10px;">
						<button class="btn btn-danger " data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
						<button class="btn btn-success" style="font-weight: bold; font-size: 1.3vw; width: 63%;" onclick="editDocument()">SIMPAN</button>
						<button class="btn btn-danger" style="font-weight: bold; font-size: 1.3vw; width: 5%; background-color: black; color: red;" onclick="deleteSDS()"><i class="fa fa-trash"></i></button>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modalVersion" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<center>
					<h3 style="background-color: #BA55D3; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
						Daftar Versi Dokumen<br>
					</h3>
				</center>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<form class="form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">GMC Material<span class="text-red">*</span> :</label>
								<div class="col-sm-5">
									<input type="text" class="form-control" placeholder="Enter Document Number" id="versionDocumentGMC" disabled>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Judul Dokumen<span class="text-red">*</span> :</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" placeholder="Enter Document Title" id="versionTitle" disabled>
								</div>
							</div>
							<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">No. Revisi<span class="text-red">*</span> :</label>
								<div class="col-sm-2">
									<div class="input-group">
										<input type="text" class="numpad form-control" placeholder="Revise" id="versionVersion">
									</div>
								</div>
							</div>
							<div class="form-group" id="revisi_date">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tanggal Revisi<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" id="versionVersionDate" placeholder="   Select Date">
								</div>
							</div>
							<div class="form-group" id="sds_asli">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">SDS ASLI<span class="text-red">*</span> :</label>
								<div class="col-sm-5">
									<input type="file" id="versionAttachmentAsli">
								</div>
							</div>
							<div class="form-group" id="sds_new">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">SDS YMPI<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="file" id="versionAttachmentNew">
								</div>
							</div>
						</div>
					</form>
					<div class="col-md-12" style="padding-bottom: 10px;">
						<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
						<button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="versionDocument()">TAMBAH REVISI</button>
					</div>
					<table id="tableVersion" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #9932CC; color: white;">
							<tr>
								<th style="width: 0.1%; text-align: center;">Version</th>
								<th style="width: 0.1%; text-align: right;">Tanggal</th>
								<th style="width: 0.4%; text-align: center;">SDS Asli</th>
								<th style="width: 0.4%; text-align: center;">SDS YMPI</th>
								<th style="width: 0.2%; text-align: left;">Created By</th>
								<th style="width: 0.1%; text-align: center;">Updated At</th>
							</tr>
						</thead>
						<tbody id="tableVersionBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalChart">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
					<table id="tableModal" class="table table-bordered table-striped table-hover">
						<thead style="background-color: #605ca8; color: white;">
							<tr>
								<th>No</th>
								<th>Id</th>
								<th>Item Code</th>
								<th>Expaired</th>
								<th>PCH</th>
							</tr>
						</thead>
						<tbody id="tableModalBody">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>


@endsection
@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/jquery.numpad.js")}}"></script>


<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%; z-index: 9999;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		fetchData();
		clearall();
	});

	var distri = [];
	var arr = [];
	var edit_arr = [];
	var document_sds = [];
	var document_attachments = [];

	$(function () {
		$('#createGmc').select2({
			dropdownParent: $('#modalCreate')
		});
		
		$('#createVersionDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('#editDepartment').select2({
			dropdownParent: $('#modalEdit')
		});
		
		$('#editVersionDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});

		$('#versionVersionDate').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
		
		$('#editGmc').select2({
			dropdownParent: $('#modalEdit')
		});

		$('#versionVersionDate').val("");
		$('#versionAttachmentAsli').val("");
		$('#versionAttachmentNew').val("");

	});

	function changeReturn(cat,no) {
		var returns = '';
		$.each($("input[name='add_return_or_not"+no+"']:checked"), function(){
			returns = $(this).val();
		});

		if (returns == cat) {
			$('#btn_'+cat).css('background-color', '#90ed7d');
			arr.push({
				'loc' :cat
			});

		}else{
			$('#btn_'+cat).css('background-color', 'white');
			var index = arr.findIndex(e => e.loc == cat);
			arr.splice(index, 1);
		}
	}

	function changeEdit(cat,no) {
		var returns = '';
		$.each($("input[name='edit_"+no+"']:checked"), function(){
			$('#edit_'+no).val(cat);
			returns = $(this).val();
		});

		if (returns == cat) {
			$('#btn_edit_'+cat).css('background-color', '#90ed7d');
			edit_arr.push({
				'loc' :cat
			});
		}else{
			$('#btn_edit_'+cat).css('background-color', 'white');
			var index = edit_arr.findIndex(e => e.loc == cat);
			edit_arr.splice(index, 1);

		}
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');
	

	function getDescription(value){
		var decs = value.split('_');
		if (decs[2] == 'GMC') {
			$('#createDescMaterial').val(decs[1]);
		}else{
			$('#createDescMaterial').val(decs[1].slice(decs[1].indexOf(' ') + 1));
		}
	}

	function getEditDescription(value){
		var obj  = document.getElementById('editGmc');
		var descp = obj.options[obj.selectedIndex].getAttribute('decs');
		var types1 = obj.options[obj.selectedIndex].getAttribute('types');

		if (types1 == "GMC") {
			$('#editDescMaterial').val(descp);
		}else{
			$('#editDescMaterial').val(descp.slice(descp.indexOf(' ') + 1));
		}
	}

	function modalCreate(){
		$('#createDepartment').prop('selectedIndex', 0).change();
		$('#btn_IK').css('background-color', 'white');
		$('#btn_DM').css('background-color', 'white');
		$('#btn_DL').css('background-color', 'white');
		$('#category').val("");
		$('#createDocumentNumber').val("");
		$('#createTitle').val("");
		$('#createVersion').val("");
		$('#createVersionDate').val("");
		$('#createAttachmentPDF').val("");
		$('#createAttachmentXLS').val("");
		$('#modalCreate').modal('show');
	}

	function btnCategory(cat){
		$('#btn_IK').css('background-color', 'white');
		$('#btn_DM').css('background-color', 'white');
		$('#btn_DL').css('background-color', 'white');
		
		$('#btn_'+cat).css('background-color', '#90ed7d');
		$('#btn_edit_IK').css('background-color', 'white');
		$('#btn_edit_DM').css('background-color', 'white');
		$('#btn_edit_DL').css('background-color', 'white');
		$('#btn_edit_'+cat).css('background-color', '#90ed7d');

		$('#category').val(cat);
	}

	function versionDocument(){
		$('#loading').show();

		var gmc_material = $('#versionDocumentGMC').val();
		var document_id = $('#document_id').val();
		var version = $('#versionVersion').val();
		var version_date = $('#versionVersionDate').val();
		var attachment_asli = $('#versionAttachmentAsli').prop('files')[0];
		var file_asli = $('#versionAttachmentAsli').val().replace(/C:\\fakepath\\/i, '').split(".");
		var attachment_new = $('#versionAttachmentNew').prop('files')[0];
		var file_new = $('#versionAttachmentNew').val().replace(/C:\\fakepath\\/i, '').split(".");

		if(version == ""){
			$('#loading').hide();
			openErrorGritter('Error!', 'Semua data dengan tanda bintang harus terisi.');
			audio_error.play();
			return false;
		}

		var date_now = new Date(version_date);
		var dates = new Date(version_date);

		dates.setFullYear(dates.getFullYear() + 5);
		var Difference_In_Time = dates.getTime() - date_now.getTime();
		var test =Difference_In_Time / (1000 * 3600 * 24);
		var date_reminder = dates.getFullYear()+'-'+dates.getMonth()+'-'+dates.getDate();

		var formData = new FormData();
		formData.append('gmc_material', gmc_material);
		formData.append('document_id', document_id);
		formData.append('version', version);
		formData.append('version_date', version_date);
		formData.append('attachment_sds_asli', attachment_asli);
		formData.append('extension_asli', file_asli[1]);
		formData.append('file_name_asli', file_asli[0]);
		formData.append('attachment_new', attachment_new);
		formData.append('extension_new', file_new[1]);
		formData.append('file_name_new', file_new[0]);
		formData.append('last_date', date_reminder);

		$.ajax({
			url:"{{ url('version/chemical/document') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					fetchData();
					$('#loading').hide();
					openSuccessGritter('Success!', data.message);
					audio_ok.play();
					$('#versionVersionDate').val("");
					$('#versionAttachmentAsli').val("");
					$('#versionAttachmentNew').val("");
					$('#modalVersion').modal('hide');
					location.reload();
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
					audio_error.play();
				}

			}
		});
	}
	

	function updateDocumentPCH(){
		$('#loading').show();


		var gmc_material = $('#document_id_pch').val();
		var id_pch = $('#id_pch').val();
		var attachment_asli = $('#updateSdsAslipch').prop('files')[0];
		var file_asli = $('#updateSdsAslipch').val().replace(/C:\\fakepath\\/i, '').split(".");

		// if(version == "" || version_date == "" || attachment_asli == "" || attachment_new == ""){
		// 	$('#loading').hide();
		// 	openErrorGritter('Error!', 'Semua data dengan tanda bintang harus terisi.');
		// 	audio_error.play();
		// 	return false;
		// }

		var formData = new FormData();
		formData.append('id_pch', id_pch);
		formData.append('gmc_material', gmc_material);
		formData.append('attachment_asli', attachment_asli);
		formData.append('extension_asli', file_asli[1]);
		formData.append('file_name_asli', file_asli[0]);

		$.ajax({
			url:"{{ url('update/sds/pch') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				if (data.status) {
					fetchData();
					$('#loading').hide();
					openSuccessGritter('Success!', data.message);
					audio_ok.play();
					$('#updateSdsAslipch').val("");
					// $('#modalUpdatePch').modal('hide');
				}
				else{
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
					audio_error.play();
				}

			}
		});
	}

	function editDocument(){
		$('#loading').show();

		var locs = [];

		for (var i = 0; i < edit_arr.length; i++) {
			locs.push(edit_arr[i].loc);
		}

		var editGmc = $('#editGmc').val();
		var editDescMaterial = $('#editDescMaterial').val();
		var editTitle = $('#editTitle').val();

		if(editGmc == "" || editTitle == ""){
			$('#loading').hide();
			openErrorGritter('Error!', 'Semua data dengan tanda bintang harus terisi.');
			audio_error.play();
			return false;
		}

		var status = true;
		var st_distri = "";

		$.each(document_sds, function(key, value){
			if(value.document_id == $('#document_id').val()){
				var distri = value.distribusi.split(',');
				var hasil = locs.map(item => distri.includes(item) ? {loc : item, value : "sama"} : {loc : item, value : "beda"} );

				for (var i = 0; i < hasil.length; i++) {
					if (hasil[i].value == "beda") {
						st_distri = "beda";
					}else{
						st_distri = "sama";
					}
				}
				if(
					value.gmc_material == editGmc&&
					value.title == editTitle && st_distri == "sama" && distri.length == hasil.length)
				{
					status = false;
				}
			}
		});


		if(status == false){
			$('#loading').hide();
			openErrorGritter('Error!', "Tidak ada perubahan pada dokumen.");
			audio_error.play();
			return false;			
		}

		
		var distri_locs = locs.toString();

		var data = {
			document_id:$('#document_id').val(),
			editGmc:editGmc,
			editDescMaterial:editDescMaterial,
			editTitle:editTitle,
			distribusi_edit:distri_locs
		}

		$.post('{{ url("edit/chemical/document") }}', data, function(result, status, xhr){
			if(result.status){
				fetchData();
				$('#modalEdit').modal('hide');
				$('#loading').hide();
				openSuccessGritter('Success!', result.message);
				audio_ok.play();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
				audio_error.play();
			}
		});
	}

	function inputDocument(){
		if(confirm("Apakah Anda Yakin membuat dokumen SDS?")){
			$('#loading').show();

			// if (arr.length == 0) {
			// 	$('#loading').hide();
			// 	openErrorGritter('Error!', 'Data distribusi data tidak boleh kosong.');
			// 	audio_error.play();
			// 	return false;
			// }

			var locs = [];
			for (var i = 0; i < arr.length; i++) {
				locs.push(arr[i].loc);
			}
			locs.push('CHM', 'WWT', 'WH', 'STD');

			var gmc = $('#createGmc').val().split('_')[0];
			var desc = $('#createGmc').val().split('_')[1];
			var type = $('#createGmc').val().split('_')[2];

			if (type != "GMC") {
				var desc_material = desc.slice(desc.indexOf(' ') + 1);
			}else{
				var desc_material = $('#createDescMaterial').val();
			}

			var title = $('#createTitle').val();
			var version = $('#createVersion').val();
			var version_date = $('#createVersionDate').val();
			var attachment_sds_asli = $('#createSdsAsli').prop('files')[0];
			var file_sds_asli = $('#createSdsAsli').val().replace(/C:\\fakepath\\/i, '').split(".");
			var attachment_new = $('#createSdsYmpi').prop('files')[0];
			var file_new = $('#createSdsYmpi').val().replace(/C:\\fakepath\\/i, '').split(".");

			if(gmc == "" || category == "" || desc_material == "" || title == "" || version == "" || version_date == ""){
				$('#loading').hide();
				openErrorGritter('Error!', 'Semua data dengan tanda bintang harus terisi.');
				audio_error.play();
				return false;
			}

			var date_now = new Date(version_date);
			var dates = new Date(version_date);

			dates.setFullYear(dates.getFullYear() + 5);
			var Difference_In_Time = dates.getTime() - date_now.getTime();
			var test =Difference_In_Time / (1000 * 3600 * 24);
			var date_reminder = dates.getFullYear()+'-'+(dates.getMonth() + 1).toString().padStart(2, "0")+'-'+dates.getDate().toString().padStart(2, "0");

			var formData = new FormData();
			formData.append('createGmc', gmc);
			formData.append('desc_material', desc_material);
			formData.append('title', title);
			formData.append('loc', locs);
			formData.append('version', version);
			formData.append('version_date', version_date);
			formData.append('last_date', date_reminder);

			formData.append('attachment_sds_asli', attachment_sds_asli);
			formData.append('extension_asli', file_sds_asli[1]);
			formData.append('file_name_asli', file_sds_asli[0]);
			formData.append('attachment_new', attachment_new);
			formData.append('extension_new', file_new[1]);
			formData.append('file_name_new', file_new[0]);

			$.ajax({
				url:"{{ url('input/sds/document') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						fetchData();
						$('#loading').hide();
						openSuccessGritter('Success!', data.message);
						audio_ok.play();
						$('#modalCreate').modal('hide');
						clearall();
					}
					else{
						$('#loading').hide();
						openErrorGritter('Error!',data.message);
						audio_error.play();
					}
				}
			});
		}
	}

	function clearall(){
		$('#createGmc').val("").trigger('change');
		$('#createDescMaterial').val('');
		$('#createTitle').val('');
		$('#createVersion').val('');
		$('#createVersionDate').val('');
		$('#createSdsAsli').val('');
		$('#createSdsYmpi').val('');
		arr = [];
		$('#btn_QA').css('background-color', 'white');
		$('#btn_PLT').css('background-color', 'white');
		$('#btn_PNT').css('background-color', 'white');
		$('#btn_BUFF').css('background-color', 'white');
		$('#btn_SLD').css('background-color', 'white');
		$('#btn_HTS').css('background-color', 'white');
		$('#btn_ASSY_SAX').css('background-color', 'white');
		$('#btn_SUB_ASSY_SAX').css('background-color', 'white');
		$('#btn_ASSY_FL').css('background-color', 'white');
		$('#btn_SUB_ASSY_FL').css('background-color', 'white');
		$('#btn_ASSY_CL').css('background-color', 'white');
		$('#btn_SUB_ASSY_CL').css('background-color', 'white');
		$('#btn_RP').css('background-color', 'white');
		$('#btn_BPP_SAX').css('background-color', 'white');
		$('#btn_BPP_FL').css('background-color', 'white');
		$('#btn_CLBODY').css('background-color', 'white');
		$('#btn_CASE').css('background-color', 'white');
		$('#btn_TANPO').css('background-color', 'white');
		$('#btn_PNC').css('background-color', 'white');
		$('#btn_ASSY_REC').css('background-color', 'white');
		$('#btn_REC_INJ').css('background-color', 'white');
		$('#btn_WRS').css('background-color', 'white');
		$('#btn_MNT').css('background-color', 'white');
		$('#btn_MLD').css('background-color', 'white');
		$('#btn_PRESS').css('background-color', 'white');
		$('#btn_MACHINING').css('background-color', 'white');
		$('#btn_SENBAN').css('background-color', 'white');
		$('#btn_SND').css('background-color', 'white');
		$('#btn_GA').css('background-color', 'white');
		$('#btn_HR').css('background-color', 'white');
		$('#btn_MOUTHPIECE').css('background-color', 'white');

	}

	function modalEdit(document_id){

		$('#document_id').val(document_id);
		var cob = ['QA','PLT','PNT','BUFF','SLD','HTS','ASSY_SAX','SUB_ASSY_SAX','ASSY_FL','SUB_ASSY FL','ASSY_CL','SUB_ASSY_CL','RP','BPP_SAX','BPP_FL','CLBODY','CASE','TANPO','PNC','ASSY_REC','REC_INJ','WRS','MNT','MLD','PRESS','MACHINING','SENBAN','SND','GA','HR','CHM', 'WWT', 'WH', 'STD','MOUTHPIECE'];
		
		$.each(document_sds, function(key, value){
			if(value.document_id == document_id){
				$('#editTitle').val(value.title);
				$('#editGmc').val(value.gmc_material).trigger("change");

				$('#editDescMaterial').val(value.desc_material);
				$('#doc_ids').val(value.document_id);
				
				var distri = value.distribusi.split(',');
				
				var hasil = cob.map(item => distri.includes(item) ? {loc : item, value : "hijau"} : {loc : item, value : "putih"} );

				var body = "";
				var colors = "";
				var check = "";

				$("#distribusi_lokasi").empty();


				for (var i = 0; i < hasil.length; i++) {
					if (hasil[i].value == "hijau") {
						colors = "background-color: #90ed7d;"
						edit_arr.push({
							'loc' :hasil[i].loc
						});
						check = "checked";

					}else{
						check = "";
						colors = "background-color: #ffffff;"
					}

					body += '<label class="containers" style="padding-right: 2px;" onchange="changeEdit(\''+hasil[i].loc+'\','+i+')"><a style="border-color: black; color: black; '+colors+' " id="btn_edit_'+hasil[i].loc+'" class="btn btn-sm"><input type="checkbox" id="edit_'+i+'"  name="edit_'+i+'" '+check+' hidden>'+hasil[i].loc+'</a></label>';
				}

				$("#distribusi_lokasi").append(body);
			}
		});

		$('#modalEdit').modal('show');
	}

	function modalVersion(document_ids){
		$('#document_id').val(document_ids);

		$('#tableVersionBody').html("");
		var tableVersionBody = "";
		var version = [];

		$.each(document_attachments, function(key, value){
			if(value.document_id == document_ids){
				version.push(value.version);
				tableVersionBody += '<tr>';
				tableVersionBody += '<td style="text-align: center;">'+value.version+'</td>';
				tableVersionBody += '<td style="text-align: right;">'+value.version_date+'</td>';
				tableVersionBody += '<td style="text-align: center;"><a href="{{ asset('files/chemical/documents') }}/'+value.file_name_pdf+'" target="_blank"><i class="fa fa-file-pdf-o"> </i> '+value.file_name_pdf+'</a></td>';
				if(value.file_name_xls != ""){
					tableVersionBody += '<td style="text-align: center;"><a href="{{ asset('files/chemical/documents') }}/'+value.file_name_xls+'" target="_blank"><i class="fa fa-file-excel-o"> </i> '+value.file_name_xls+'</a></td>';
				}
				else{
					tableVersionBody += '<td style="text-align: center;">-</td>';
				}

				tableVersionBody += '<td style="text-align: left;">'+value.created_by_name+'</td>';
				tableVersionBody += '<td style="text-align: right;">'+value.created_at+'</td>';
				tableVersionBody += '</tr>';
			}
		});
		$('#tableVersionBody').append(tableVersionBody);

		$.each(document_sds, function(key, value){
			if(value.document_id == document_ids){

				if (value.file_name_sds == null) {
					$('#versionDocumentGMC').val(value.gmc_material);
					$('#versionTitle').val(value.title);
					$('#versionVersion').val(Math.max.apply(Math,version));
					$('#sds_asli').hide();
					$('#revisi_date').hide();
				}else if (value.file_name_asli == null){
					$('#versionDocumentGMC').val(value.gmc_material);
					$('#versionTitle').val(value.title);
					$('#versionVersion').val(Math.max.apply(Math,version));
					$('#sds_new').hide();
					$('#revisi_date').hide();

				}else{
					$('#versionDocumentGMC').val(value.gmc_material);
					$('#versionTitle').val(value.title);
					$('#versionVersion').val(Math.max.apply(Math,version)+1);
					$('#sds_new').show();
					$('#sds_asli').show();
				}


				// if (value.file_name_sds != null && value.file_name_asli == null) {
				// 	// $('#sds_asli').hide();
				// 	var file_new_ymp = value.file_name_sds.split("_");
				// }

				// if (value.file_name_asli != null) {
				// 	// $('#sds_new').hide();
				// 	var file_asli = value.file_name_asli.split("_");
				// }

				// 	if (value.version != file_asli[2]) {
				// 		$('#sds_asli').hide();
				// 	}
				// 	else if(value.version != file_new_ymp[2]) {
				// 		$('#sds_new').hide();
				// 	}
				// 	else {
				// 		$('#sds_new').show();
				// 		$('#sds_asli').show();
				// 	}


			}
		});

		$('#modalVersion').modal('show');
	}

	function fetchList(){
		var tableResume = "";

		var area = ['QA','PLT','PNT','BUFF','SLD','HTS','ASSY_SAX','SUB_ASSY_SAX','ASSY_FL','SUB_ASSY_FL','ASSY_CL','SUB_ASSY_CL','RP','BPP_SAX','BPP_FL','CLBODY','CASE','TANPO','PNC','ASSY_REC','REC_INJ','WRS','MNT','MLD','PRESS','MACHINING','SENBAN','SND','GA','HR','CHM', 'WWT', 'WH', 'STD','MOUTHPIECE'];

		$('#tableResume1').html("");

		tableResume += '<thead style="background-color: rgba(126,86,134,.7);">';
		tableResume += '<tr>';
		tableResume += '<th style="font-size:0.8vw;">NO</th>';
		tableResume += '<th style="font-size:0.8vw;">ID</th>';
		tableResume += '<th style="width: 20vw; font-size:0.8vw;">Judul</th>';

		for (var i = 0; i < area.length; i++) {
			tableResume += '<th style="font-size:0.8vw;">'+area[i]+'</th>';						
		}
		tableResume += '</tr>';
		tableResume += '</thead>';
		tableResume += '<tbody>';
		var insert = false;
		var cnt = 1;

		for (var i = 0; i < document_sds.length; i++) {
			tableResume += '<tr>';
			tableResume += '<td>'+cnt+'</td>';
			tableResume += '<td>'+document_sds[i].document_id+'</td>';
			tableResume += '<td>'+document_sds[i].title+'</td>';

			var dist_data = document_sds[i].distribusi.split(",");

			for (var k = 0; k < area.length;k++) {
				var stat = 0;

				for (var z = 0; z < dist_data.length; z++) {
					if (dist_data[z] == area[k]) {
						stat = 1;
					}
				}

				if (stat == 1) {
					tableResume += '<td style="width: 0.1%; text-align: center; background-color: rgba(80,80,80,0.5);">&check;</td>';							
				}

				else if(stat == 0){
					tableResume += '<td style="width: 0.1%; text-align: center;"></td>';	
				}
			}
			cnt++;

			tableResume += '</tr>';
		}

		tableResume += '</tbody>';

		$('#tableResume1').append(tableResume);
		$('#tableResume1').DataTable().clear();
		$('#tableResume1').DataTable().destroy();
		$('#tableResume1').html('');
		$('#tableResume1').append(tableResume);


		$('#tableResume1').DataTable({
			'dom': 'Bfrtip',
			'responsive':true,
			'lengthMenu': [
			[ 10, 25, 50, -1 ],
			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
			],
			'buttons': {
				buttons:[
				{
					extend: 'copy',
					className: 'btn btn-success',
					text: '<i class="fa fa-copy"></i> Copy',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				},
				{
					extend: 'excel',
					className: 'btn btn-info',
					text: '<i class="fa fa-file-excel-o"></i> Excel',
					exportOptions: {
						columns: ':not(.notexport)'
					}
				}
				]
			},
			'paging': false,
			'lengthChange': true,
			'searching': true,
			'ordering': false,
			'info': true,
			'autoWidth': true,
			"sPaginationType": "full_numbers",
			"bJQueryUI": true,
			"bAutoWidth": false,
			"processing": true
		});

		
	}

	function fetchFilter(dep, cat){
		$('#tableDocument').DataTable().clear();
		$('#tableDocument').DataTable().destroy();
		$('#tableDocumentBody').html("");
		var tableDocumentBody = "";

		$.each(document_sds, function(key, value){
			if (value.distribusi.indexOf(dep) >= 0) {
				tableDocumentBody += '<tr>';
				if ('{!! auth()->user()->role_code !!}' == 'S-MIS' || '{!! auth()->user()->role_code !!}' == 'C-MIS' ||'{!! auth()->user()->role_code !!}' == 'MIS' || '{!! auth()->user()->role_code !!}' == 'C-CHM' || '{!! auth()->user()->role_code !!}' == 'S-CHM' || '{!! auth()->user()->role_code !!}' == 'L-CHM' || '{!! auth()->user()->role_code !!}' == 'M' ) {
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\''+value.document_id+'\')">'+value.document_id+'</a></td>';	
				}else{
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;">'+value.document_id+'</a></td>';
				}
				tableDocumentBody += '<td style="width: 1%; text-align: left;">'+value.gmc_material+'</td>';
				tableDocumentBody += '<td style="width: 3%; text-align: left;">'+value.title+'</td>';
				if ('{!! auth()->user()->role_code !!}' == 'S-MIS' || '{!! auth()->user()->role_code !!}' == 'C-MIS' || '{!! auth()->user()->role_code !!}' == 'MIS' || '{!! auth()->user()->role_code !!}' == 'C-CHM' || '{!! auth()->user()->role_code !!}' == 'S-CHM' || '{!! auth()->user()->role_code !!}' == 'L-CHM' || '{!! auth()->user()->role_code !!}' == 'M' ) {
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalVersion(\''+value.document_id+'\')"><div style="height: 100%; width: 100%;">'+value.version+'</div></a></td>';
				}else{
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;"><div style="height: 100%; width: 100%;">'+value.version+'</div></a></td>';
				}
				tableDocumentBody += '<td style="width: 0.5%; text-align: right;">'+value.version_date+'</td>';
				tableDocumentBody += '<td style="width: 0.5%; text-align: right;">'+value.last_date+'</td>';				
				if(value.diff <= 0){
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; text-align: right; background-color: RGB(255,204,255);">'+value.diff+' Day(s)</td>';
				}
				else if(value.diff <= 60){
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; text-align: right; background-color: orange;">'+value.diff+' Day(s)</td>';
				}
				else{
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; text-align: right; background-color: RGB(204,255,255);">'+value.diff+' Day(s)</td>';
				}

				

				if (value.file_name_asli == null && value.file_name_sds == null) {

					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<p>-</p>';
					tableDocumentBody += '</td>'

					
				}else if (value.file_name_asli == null) {
					tableDocumentBody += '<td style="width:1%; text-align: center;">'

					tableDocumentBody += '<a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_sds+'"><i class="fa fa-file-pdf-o"></i>';
					tableDocumentBody += '</td>'


				}else if (value.file_name_sds == null) {
					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_asli+'"><i class="fa fa-file-pdf-o"></i>';
					tableDocumentBody += '</td>'

				}else if (value.file_name_asli == null && value.file_name_sds == null) {
					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<p>-</p>';
					tableDocumentBody += '</td>'

				}

				else{

					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_asli+'"><i class="fa fa-file-pdf-o"></i> <a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_sds+'"><i class="fa fa-file-pdf-o"></i>';
					tableDocumentBody += '</td>'
				}

				tableDocumentBody += '<td style="width: 2%; text-align: right;">'+value.updated_at+'</td>';


				if(value.status == 'Active'){
					tableDocumentBody += '<td style="width: 2%; font-weight: bold; background-color: RGB(204,255,255);">'+value.status+'</td>';
				}
				else if(value.status == 'AtRisk'){
					tableDocumentBody += '<td style="width: 2%; font-weight: bold; background-color: orange;">'+value.status+'</td>';
				}
				else if(value.status == 'Expired'){
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; background-color: RGB(255,204,255);">'+value.status+'</td>';
				}
				else{
					tableDocumentBody += '<td style="width: 2%; font-weight: bold; background-color: grey; color: white;">'+value.status+'</td>';
				}


				tableDocumentBody += '</tr>';
			}
		});

$('#tableDocumentBody').append(tableDocumentBody);

$('#tableDocument').DataTable({
	'dom': 'Bfrtip',
	'responsive':true,
	'lengthMenu': [
	[ 10, 25, 50, -1 ],
	[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	],
	'buttons': {
		buttons:[
		{
			extend: 'pageLength',
			className: 'btn btn-default',
		},
		{
			extend: 'copy',
			className: 'btn btn-success',
			text: '<i class="fa fa-copy"></i> Copy',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		{
			extend: 'excel',
			className: 'btn btn-info',
			text: '<i class="fa fa-file-excel-o"></i> Excel',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		{
			extend: 'print',
			className: 'btn btn-warning',
			text: '<i class="fa fa-print"></i> Print',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		]
	},
	'paging': true,
	'lengthChange': true,
	'searching': true,
	'ordering': false,
	'order': [],
	'info': true,
	'autoWidth': true,
	"sPaginationType": "full_numbers",
	"bJQueryUI": true,
	"bAutoWidth": false,
	"processing": true
});

}

function fetchStatus(status){
	if(status == 'All'){
		fetchData();
	}else{

		$('#tableDocument').DataTable().clear();
		$('#tableDocument').DataTable().destroy();
		$('#tableDocumentBody').html("");
		var tableDocumentBody = "";

		$.each(document_sds, function(key, value){
			if(value.status == status){

				tableDocumentBody += '<tr>';
				if ('{!! auth()->user()->role_code !!}' == 'S-MIS' || '{!! auth()->user()->role_code !!}' == 'C-MIS' || '{!! auth()->user()->role_code !!}' == 'MIS' || '{!! auth()->user()->role_code !!}' == 'C-CHM' || '{!! auth()->user()->role_code !!}' == 'S-CHM' || '{!! auth()->user()->role_code !!}' == 'L-CHM' || '{!! auth()->user()->role_code !!}' == 'M') {
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\''+value.document_id+'\')">'+value.document_id+'</a></td>';	
				}else{
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;">'+value.document_id+'</a></td>';
				}
				tableDocumentBody += '<td style="width: 0.1%; text-align: left;">'+value.gmc_material+'</td>';
				tableDocumentBody += '<td style="width: 1%; text-align: left;">'+value.title+'</td>';
				if ('{!! auth()->user()->role_code !!}' == 'S-MIS' || '{!! auth()->user()->role_code !!}' == 'C-MIS' || '{!! auth()->user()->role_code !!}' == 'MIS' || '{!! auth()->user()->role_code !!}' == 'C-CHM' || '{!! auth()->user()->role_code !!}' == 'S-CHM' || '{!! auth()->user()->role_code !!}' == 'L-CHM' || '{!! auth()->user()->role_code !!}' == 'M') {
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalVersion(\''+value.document_id+'\')"><div style="height: 100%; width: 100%;">'+value.version+'</div></a></td>';
				}else{
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;"><div style="height: 100%; width: 100%;">'+value.version+'</div></a></td>';
				}
				tableDocumentBody += '<td style="width: 0.1%; text-align: right;">'+value.version_date+'</td>';
				tableDocumentBody += '<td style="width: 0.1%; text-align: right;">'+value.last_date+'</td>';
				if(value.diff <= 0){
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; text-align: right; background-color: RGB(255,204,255);">'+value.diff+' Day(s)</td>';
				}
				else if(value.diff <= 60){
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; text-align: right; background-color: orange;">'+value.diff+' Day(s)</td>';
				}
				else{
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; text-align: right; background-color: RGB(204,255,255);">'+value.diff+' Day(s)</td>';
				}

				if (value.file_name_asli == null && value.file_name_sds == null) {

					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<p>-</p>';
					tableDocumentBody += '</td>'

					
				}else if (value.file_name_asli == null) {
					tableDocumentBody += '<td style="width:1%; text-align: center;">'

					tableDocumentBody += '<a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_sds+'"><i class="fa fa-file-pdf-o"></i>';
					tableDocumentBody += '</td>'


				}else if (value.file_name_sds == null) {
					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_asli+'"><i class="fa fa-file-pdf-o"></i>';
					tableDocumentBody += '</td>'

				}else if (value.file_name_asli == null && value.file_name_sds == null) {
					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<p>-</p>';
					tableDocumentBody += '</td>'

				}

				else{

					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_asli+'"><i class="fa fa-file-pdf-o"></i> <a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_sds+'"><i class="fa fa-file-pdf-o"></i>';
					tableDocumentBody += '</td>'
				}

				tableDocumentBody += '<td style="width: 0.2%; text-align: right;">'+value.updated_at+'</td>';
				
				if(value.status == 'Active'){
					tableDocumentBody += '<td style="width: 2%; font-weight: bold; background-color: RGB(204,255,255);">'+value.status+'</td>';
				}
				else if(value.status == 'AtRisk'){
					tableDocumentBody += '<td style="width: 2%; font-weight: bold; orange;">'+value.status+'</td>';
				}
				else if(value.status == 'Expired'){
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; background-color: RGB(255,204,255);">'+value.status+'</td>';
				}
				else{
					tableDocumentBody += '<td style="width: 2%; font-weight: bold; background-color: grey; color: white;">'+value.status+'</td>';
				}
				tableDocumentBody += '</tr>';
			}
		});

$('#tableDocumentBody').append(tableDocumentBody);

$('#tableDocument').DataTable({
	'dom': 'Bfrtip',
	'responsive':true,
	'lengthMenu': [
	[ 10, 25, 50, -1 ],
	[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	],
	'buttons': {
		buttons:[
		{
			extend: 'pageLength',
			className: 'btn btn-default',
		},
		{
			extend: 'copy',
			className: 'btn btn-success',
			text: '<i class="fa fa-copy"></i> Copy',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		{
			extend: 'excel',
			className: 'btn btn-info',
			text: '<i class="fa fa-file-excel-o"></i> Excel',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		{
			extend: 'print',
			className: 'btn btn-warning',
			text: '<i class="fa fa-print"></i> Print',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		]
	},
	'paging': true,
	'lengthChange': true,
	'searching': true,
	'ordering': false,
	'order': [],
	'info': true,
	'autoWidth': true,
	"sPaginationType": "full_numbers",
	"bJQueryUI": true,
	"bAutoWidth": false,
	"processing": true
});
}


}

$.date = function(dateObject) {
	var d = new Date(dateObject);
	var day = d.getDate();
	var month = d.getMonth() + 1;
	var year = d.getFullYear();
	if (day < 10) {
		day = "0" + day;
	}
	if (month < 10) {
		month = "0" + month;
	}
	var date = year + "-" + month + "-" + day;

	return date;
};

function fetchData(status){
	
	$.get('{{ url("fetch/chemical/document/sds") }}', function(result, status, xhr){
		if(result.status){
			var cnt_expired = 0;
			var count_active = 0;
			var count_expired = 0;
			var count_atrisk = 0;

			$('#tableDocument').DataTable().clear();
			$('#tableDocument').DataTable().destroy();
			$('#tableDocumentBody').html("");
			var tableDocumentBody = "";
			document_sds = result.documents_sds;
			document_attachments = result.document_attachments_sds;

			fetchList();

			if (result.user.department == "Management Information System Department" || result.user.section == "Chemical Process Control Section") {
				var bagians = 'QA,PLT,PNT,BUFF,SLD,HTS,ASSY_SAX,SUB_ASSY_SAX,ASSY_FL,SUB_ASSY_FL,ASSY_CL,SUB_ASSY_CL,RP,BPP_SAX,BPP_FL,CLBODY,CASE,TANPO,PNC,ASSY_REC,REC_INJ,WRS,MNT,MLD,PRESS,MACHINING,SENBAN,SND,GA,HR,CHM,WWT,WH,STD,MOUTHPIECE';
			}else if (result.user.department == "Quality Assurance Department" || result.user.position == "Manager") {
				var bagians = 'QA,PLT,PNT,BUFF,SLD,HTS,ASSY_SAX,SUB_ASSY_SAX,ASSY_FL,SUB_ASSY_FL,ASSY_CL,SUB_ASSY_CL,RP,BPP_SAX,BPP_FL,CLBODY,CASE,TANPO,PNC,ASSY_REC,REC_INJ,WRS,MNT,MLD,PRESS,MACHINING,SENBAN,SND,GA,HR,CHM,WWT,WH,STD,MOUTHPIECE';
			}
			else if (result.user.department == "General Affairs Department") {
				var bagians = 'GA';
			}else if (result.user.department == "Human Resources Department") {
				var bagians = 'HR';
			}else if (result.user.section == "Assembly FL Process Section") {
				var bagians = 'ASSY_FL,SUB_ASSY FL';
			}else if (result.user.section == "Assembly Sax Process Section") {
				var bagians = 'ASSY_SAX,SUB_ASSY_SAX';
			}else if (result.user.section == "Assembly CL . Tanpo . Case Process Section") {
				var bagians = 'ASSY_CL,SUB_ASSY_CL,TANPO,CLBODY';
			}else if (result.user.section == "Assembly Process Control Section") {
				var bagians = 'ASSY_FL,SUB_ASSY FL,ASSY_SAX,SUB_ASSY_SAX,ASSY_CL,SUB_ASSY_CL,TANPO,CLBODY';
			}else if (result.user.section == "Koshuha Solder Process Section") {
				var bagians = 'SLD';
			}else if (result.user.section == "Handatsuke . Suport Process Section") {
				var bagians = 'HTS';
			}else if (result.user.section == "Welding Control Section") {
				var bagians = 'SLD,HTS';
			}else if (result.user.department == "Maintenance Department") {
				var bagians = 'MNT,WWT';
			}else if (result.user.section == "QA Process Section" || result.user.section == "QA Process Control Section") {
				var bagians = 'QA';
			}else if (result.user.department == "Woodwind Instrument - Body Parts Process (WI-BPP) Department") {
				var bagians = 'BPP_SAX,BPP_FL';
			}
			else if (result.user.department == "Woodwind Instrument - Surface Treatment (WI-ST) Department") {
				var bagians = 'PLT,PNT,BUFF';
			}else if (result.user.department == "Logistic Department") {
				var bagians = 'WH';
			}else if (result.user.section == "NC Process Section") {
				var bagians = 'SENBAN';
			}
			else if (result.user.section == "NC Process Section") {
				var bagians = 'SENBAN,MACHINING';
			}else if (result.user.section == "Press and Sanding Process Section") {
				var bagians = 'PRESS,SND';
			}else if (result.user.department == "Educational Instrument (EI) Department") {
				var bagians = 'RP,CASE,PNC,ASSY_REC,REC_INJ';
			}else if (result.user.department == "Production Engineering Department") {
				var bagians = 'MLD,WRS';
			}
			else if (result.user.section == "Standardization Process Control Section") {
				var bagians = 'STD';
			}
			

			var bagian = bagians.split(',');
			var documentid = "";
			var documentids = "";

			var series = [];
			var categories = [];
			var document_ids = [];
			var series1 = [];
			var categories1 = [];
			var document_id1 = [];

			for(var i = 0; i < bagian.length;i++){
				var count = 0;
				for(var j = 0; j < document_sds.length;j++){
					var re = new RegExp(bagian[i], 'g');
					if(document_sds[j].distribusi.match(re)){
						count++;
						documentid = document_sds[j].document_id;
						document_ids.push(document_sds[j].document_id);
					}
				}
				
				series.push(parseInt(count));
				categories.push(bagian[i]);
			}


			$.each(result.documents_sds, function(key, value){

				cnt_expired += 1;
				tableDocumentBody += '<tr>';
				if ('{!! auth()->user()->role_code !!}' == 'S-MIS' || '{!! auth()->user()->role_code !!}' == 'C-MIS' ||'{!! auth()->user()->role_code !!}' == 'MIS' || '{!! auth()->user()->role_code !!}' == 'C-CHM' || '{!! auth()->user()->role_code !!}' == 'S-CHM' || '{!! auth()->user()->role_code !!}' == 'L-CHM' || '{!! auth()->user()->role_code !!}' == 'M' ) {
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\''+value.document_id+'\')">'+value.document_id+'</a></td>';	
				}else{
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;">'+value.document_id+'</a></td>';
				}
				tableDocumentBody += '<td style="width: 1%; text-align: left;">'+value.gmc_material+'</td>';
				tableDocumentBody += '<td style="width: 3%; text-align: left;">'+value.title+'</td>';
				if ('{!! auth()->user()->role_code !!}' == 'S-MIS' || '{!! auth()->user()->role_code !!}' == 'C-MIS' || '{!! auth()->user()->role_code !!}' == 'MIS' || '{!! auth()->user()->role_code !!}' == 'C-CHM' || '{!! auth()->user()->role_code !!}' == 'S-CHM' || '{!! auth()->user()->role_code !!}' == 'L-CHM' || '{!! auth()->user()->role_code !!}' == 'M') {
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalVersion(\''+value.document_id+'\')"><div style="height: 100%; width: 100%;">'+value.version+'</div></a></td>';
				}else{
					tableDocumentBody += '<td style="width: 1%; text-align: center; font-weight: bold;"><div style="height: 100%; width: 100%;">'+value.version+'</div></a></td>';
				}
				tableDocumentBody += '<td style="width: 0.5%; text-align: right;">'+value.version_date+'</td>';
				tableDocumentBody += '<td style="width: 0.5%; text-align: right;">'+value.last_date+'</td>';

				if(value.diff <= 0){
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; text-align: right; background-color: RGB(255,204,255);">'+value.diff+' Day(s)</td>';
				}
				else if(value.diff <= 60){
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; text-align: right; background-color: orange;">'+value.diff+' Day(s)</td>';
				}
				else{
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; text-align: right; background-color: RGB(204,255,255);">'+value.diff+' Day(s)</td>';
				}


				if (value.file_name_asli == null && value.file_name_sds == null) {
					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<p>-</p>';
					tableDocumentBody += '</td>'

					
				}else if (value.file_name_asli == null) {
					tableDocumentBody += '<td style="width:1%; text-align: center;">'

					tableDocumentBody += '<a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_sds+'"><i class="fa fa-file-pdf-o"></i>';
					tableDocumentBody += '</td>'


				}else if (value.file_name_sds == null) {
					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_asli+'"><i class="fa fa-file-pdf-o"></i>';
					tableDocumentBody += '</td>'

				}else if (value.file_name_asli == null && value.file_name_sds == null) {
					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<p>-</p>';
					tableDocumentBody += '</td>'

				}

				else{

					tableDocumentBody += '<td style="width:1%; text-align: center;">'
					tableDocumentBody += '<a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_asli+'"><i class="fa fa-file-pdf-o"></i> <a target="_blank" href="{{ url("files/chemical/documents") }}/'+value.file_name_sds+'"><i class="fa fa-file-pdf-o"></i>';
					tableDocumentBody += '</td>'
				}
				
				tableDocumentBody += '<td style="width: 2%; text-align: right;">'+value.updated_at+'</td>';

				if(value.status == 'Active'){
					tableDocumentBody += '<td style="width: 2%; font-weight: bold; background-color: RGB(204,255,255);">'+value.status+'</td>';
				}
				else if(value.status == 'AtRisk'){
					tableDocumentBody += '<td style="width: 2%; font-weight: bold; background-color: orange;">'+value.status+'</td>';
				}
				else if(value.status == 'Expired'){
					tableDocumentBody += '<td style="width: 0.5%; font-weight: bold; background-color: RGB(255,204,255);">'+value.status+'</td>';
				}
				else{
					tableDocumentBody += '<td style="width: 2%; font-weight: bold; background-color: grey; color: white;">'+value.status+'</td>';
				}
				tableDocumentBody += '</tr>';

				if(value.status == 'Active'){
					count_active += 1;
				}
				if(value.status == 'Expired'){
					count_expired += 1;
				}
				if(value.status == 'AtRisk'){
					count_atrisk += 1;
				}
			});


$('#count_all').text(cnt_expired);
$('#count_active').text(count_active);
$('#count_expired').text(count_expired);
$('#count_atrisk').text(count_atrisk);

var count = 0;

for(var i = 0; i < result.get_month.length;i++){
	categories1.push(result.get_month[i].mon);
	var ress = 0;
	for(var k = 0; k < result.massa_exp.length;k++){
		if(result.massa_exp[k].month_now == result.get_month[i].mon){
			ress++;
			document_id1.push(result.massa_exp[k].document_id);
		}
	}
	series1.push(ress);

}

var resume_reminder = {};
$.each(result.sds_ex, function(key, value){
	if(value.month_reminder != ''){
		key = Date.parse(value.month_reminder);
		if (!resume_reminder[key]) {
			resume_reminder[key] = 0;
		}
		resume_reminder[key] += 1;
	}
});

var ordered = Object.keys(resume_reminder).sort().reduce(
	(obj, key) => { 
		obj[key] = resume_reminder[key]; 
		return obj;
	}, 
	{}
	);

var xCategories3 = [];
var series3 = [];
var series4 = [];

$.each(ordered, function(key, value){
	if(value.status != 'Discontinue'){
		xCategories3.push($.date(parseFloat(key)));
		series3.push(value);
		series4.push([parseFloat(key), parseFloat(value)]);						
	}
});



$('#tableDocumentBody').append(tableDocumentBody);

if ('{!! auth()->user()->role_code !!}' == 'S-MIS' || '{!! auth()->user()->role_code !!}' == 'C-MIS' ||'{!! auth()->user()->role_code !!}' == 'MIS' || '{!! auth()->user()->role_code !!}' == 'C-CHM' || '{!! auth()->user()->role_code !!}' == 'S-CHM' || '{!! auth()->user()->role_code !!}' == 'M') {


	Highcharts.chart('container5', {
		chart: {
			backgroundColor: null,
			type: 'column',
		},
		title: {
			text: 'SDS Renewal Monitoring'
		},
		credits: {
			enabled: false
		},
		xAxis:{
			tickInterval: 1,
			gridLineWidth: 1,
			categories: xCategories3,
			crosshair: true
		},
		yAxis: [{
			title: {
				text: ''
			}
		}],
		legend: {
			enabled: false,
			borderWidth: 1
		},
		tooltip: {
			enabled: true
		},
		plotOptions: {
			column: {
				pointPadding: 0.93,
				groupPadding: 0.93,
				borderWidth: 0.8,
				borderColor: 'black'
			},
			series: {
				dataLabels: {
					enabled: true,
					format: '{point.y}',
					style:{
						textOutline: false
					}
				},
				cursor: 'pointer',
				point: {
					events: {
						click: function () {
							fetchModal(this.category, this.series.name);
						}
					}
				}
			}	
		},

		series: [{
			name: 'Licenses',
			data: series3,
			color: 'orange'

		}]
	});

}

Highcharts.chart('container1', {
	chart: {
		backgroundColor: null,
		type: 'column',
	},
	title: {
		text: 'Jumlah SDS per Bagian'
	},
	credits: {
		enabled: false
	},
	xAxis:{
		tickInterval: 1,
		gridLineWidth: 1,
		categories: categories,
		crosshair: true
	},
	yAxis: [{
		title: {
			text: ''
		}
	}],
	legend: {
		enabled: false,
		borderWidth: 1
	},
	tooltip: {
		enabled: true
	},
	plotOptions: {
		column: {
			pointPadding: 0.93,
			groupPadding: 0.93,
			borderWidth: 0.8,
			borderColor: 'black'
		},
		series: {
			dataLabels: {
				enabled: true,
				format: '{point.y}',
				style:{
					textOutline: false
				}
			},
			cursor: 'pointer',
			point: {
				events: {
					click: function () {
						fetchFilter(this.category, this.series.name);
					}
				}
			}
		}	
	},
	series: [{
		name: 'SDS',
		data: series,
		color: '#ffccff'
	}]
});



$('#tableDocument').DataTable({
	'dom': 'Bfrtip',
	'responsive':true,
	'lengthMenu': [
	[ 10, 25, 50, -1 ],
	[ '10 rows', '25 rows', '50 rows', 'Show all' ]
	],
	'buttons': {
		buttons:[
		{
			extend: 'pageLength',
			className: 'btn btn-default',
		},
		{
			extend: 'copy',
			className: 'btn btn-success',
			text: '<i class="fa fa-copy"></i> Copy',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		{
			extend: 'excel',
			className: 'btn btn-info',
			text: '<i class="fa fa-file-excel-o"></i> Excel',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		{
			extend: 'print',
			className: 'btn btn-warning',
			text: '<i class="fa fa-print"></i> Print',
			exportOptions: {
				columns: ':not(.notexport)'
			}
		},
		]
	},
	'paging': true,
	'lengthChange': true,
	'searching': true,
	'ordering': false,
	'order': [],
	'info': true,
	'autoWidth': true,
	"sPaginationType": "full_numbers",
	"bJQueryUI": true,
	"bAutoWidth": false,
	"processing": true
});
}
else{
	alert('Attempt to retrieve data failed.');
}
});

}

function deleteSDS(){
	if(confirm("Are you sure want to delete this SDS request?")){
		// $('#loading').show();
		var sds_id = $('#doc_ids').val();
		var gmcs = $('#editGmc').val();
		
		var data = {
			sds_id:sds_id,
			gmcs:gmcs
		}

		$.post('{{ url("delete/sds") }}', data, function(result, status, xhr){
			if(result.status){
				fetchData();
				$('#modalEdit').modal('hide');
				$('#loading').hide();
				openSuccessGritter('Success!',data.message);
				audio_ok.play();
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!',data.message);
				audio_error.play();
			}
		});
	}
	else{
		return false;
	}
}

function fetchModal(cat, name){
	var tableModalBody = "";
	$('#tableModalBody').html("");

	var no = 1 ;
	$.each(document_sds, function(key, value){
		if(value.month_reminder == cat){
			tableModalBody += '<tr>';
			tableModalBody += '<td>'+no+'</td>';
			tableModalBody += '<td>'+value.document_id+'</td>';
			tableModalBody += '<td>'+value.gmc_material+'</td>';
			tableModalBody += '<td>'+value.last_date+'</td>';
			if (value.remark == 1) {
				tableModalBody += '<td style="width: 2%; background-color:#00a65a;color:white;font-weight:bold;">Success</td>';
			}else if (value.remark == 0){
				tableModalBody += '<td style="width: 2%; background-color:#dd4b39;color:white;font-weight:bold;">Wainting</td>';
			}else{
				tableModalBody += '<td style="width: 2%;"></td>';

			}
			no++;
		}
	});

	$('#tableModalBody').append(tableModalBody);
	$('#modalChart').modal('show');
}

function openSuccessGritter(title, message){
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-success',
		image: '{{ url("images/image-screen.png") }}',
		sticky: false,
		time: '3000'
	});
}

function openErrorGritter(title, message) {
	jQuery.gritter.add({
		title: title,
		text: message,
		class_name: 'growl-danger',
		image: '{{ url("images/image-stop.png") }}',
		sticky: false,
		time: '3000'
	});
}

</script>

@endsection
