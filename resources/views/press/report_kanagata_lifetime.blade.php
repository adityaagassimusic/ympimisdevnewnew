@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tbody>tr>td{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
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
  border:1px solid rgb(211,211,211);
  padding-top: 0;
  padding-bottom: 0;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $page }} <small><span class="text-purple">プレス機金型寿命</span></small>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 35%;">
			<span style="font-size: 40px">Processing, Please Wait! <i class="fa fa-spin fa-refresh"></i></span>
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
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>
	@endif
	<div class="row">
		<div class="col-xs-12">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
					    <form method="GET" action="{{ url('excel/press/kanagata_last_data') }}" style="padding-top: 0px">
					    <button type="submit" class="btn btn-success pull-right" style="margin-left: 5px"><i class="fa fa-download"></i> Export All Last Data</button>
						</form>
						<button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#create-modal" onclick="cancelAll()">
					        Pendaftaran Kanagata Baru
					    </button>
					</div>
					<div class="col-xs-3 col-xs-offset-4">
						<div class="box-header">
							<h3 class="box-title">Filter</h3>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label>Date From</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="date_from" name="date_from" autocomplete="off" placeholder="Date From">
								</div>
								<label>Date To</label>
								<div class="input-group date">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<input type="text" class="form-control pull-right" id="date_to" name="date_to" autocomplete="off" placeholder="Date To">
								</div>
							</div>
						</div>
						<div class="col-xs-12">
							<div class="form-group">
								<select class="form-control select4" id="process" data-placeholder="Pilih Process" style="width: 100%">
									<option value=""></option>
									<option value="Forging">Forging</option>
									<option value="Bending">Bending</option>
									<option value="Trimming">Trimming</option>
									<option value="Blank Nuki">Blank Nuki</option>
									<option value="Hiraoshi">Hiraoshi</option>
									<option value="Trimming">Trimming</option>
									<option value="Blank Nuki">Blank Nuki</option>
									<option value="Nukishibori">Nukishibori</option>
								</select>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group pull-right">
								<a href="{{ url('index/initial/press') }}" class="btn btn-warning">Back</a>
								<a href="{{ url('index/press/report_kanagata_lifetime') }}" class="btn btn-danger">Clear</a>
								<button class="btn btn-primary" onclick="fetchKanagata()">Search</button>
							</div>
						</div>
					</div>
					
					@if(str_contains($role_code,'MIS'))
					<div class="col-xs-12">
						<div class="col-xs-6">
							<div class="box-header">
								<h3 class="box-title">Edit Kanagata Last <b>Lifetime</b></h3>
							</div>
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12" style="padding-bottom: 14px">
								<div class="form-group">
									<label>Kanagata</label>
									<select class="form-control select3" name="kanagata" id="kanagata" style="width: 100%;" data-placeholder="Pilih Part" required>
					                  <option value=""></option>
					                  <option value="Punch">Punch</option>
					                  <option value="Dies">Dies</option>
					                  <option value="Plate">Plate</option>
					                </select>
					                <label>Kanagata Number</label>
									<input type="text" class="form-control pull-right" id="kanagata_number" name="kanagata_number" autocomplete="off" placeholder="Input Kanagata Number">
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group pull-right">
									<button type="button" class="btn btn-warning col-sm-14" onclick="edit_kanagata('{{ url("index/kanagata/update") }}',$('#kanagata').val(),$('#kanagata_number').val());">
						               Edit
						            </button>
								</div>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="box-header">
								<h3 class="box-title">Reset Kanagata <b>Periodik</b></h3>
							</div>
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12" style="padding-bottom: 14px">
								<div class="form-group">
									<label>Kanagata</label>
									<select class="form-control select3" name="kanagata_reset_periodik" id="kanagata_reset_periodik" style="width: 100%;" data-placeholder="Pilih Part" required>
					                  <option value=""></option>
					                  <option value="Punch">Punch</option>
					                  <option value="Die">Die</option>
					                  <option value="Plate">Plate</option>
					                </select>
					                <label>Kanagata Number</label>
									<input type="text" class="form-control pull-right" id="kanagata_number_periodik" name="kanagata_number_periodik" autocomplete="off" placeholder="Input Kanagata Number">
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group pull-right">
									<button type="button" class="btn btn-danger col-sm-14" onclick="reset_kanagata_periodik()">
						               Reset
						            </button>
								</div>
							</div>
						</div>
					</div>
					@endif
					@if(str_contains($role_code,'MIS') || str_contains($role_code,'L-KPP'))
					<div class="col-xs-12">
						<div class="col-xs-12">
							<div class="box-header">
								<h3 class="box-title">Reset Kanagata <b>Lifetime</b></h3>
							</div>
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							<div class="col-md-12" style="padding-bottom: 14px">
								<div class="form-group">
									<label>Kanagata</label>
									<select class="form-control select3" name="kanagata_reset" id="kanagata_reset" style="width: 100%;" data-placeholder="Pilih Part" required>
					                  <option value=""></option>
					                  <option value="Punch">Punch</option>
					                  <option value="Dies">Dies</option>
					                  <option value="Plate">Plate</option>
					                </select>
					                <label>Kanagata Number</label>
									<input type="text" class="form-control pull-right" id="kanagata_number2" name="kanagata_number2" autocomplete="off" placeholder="Input Kanagata Number">
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group pull-right">
									<button type="button" class="btn btn-danger col-sm-14" onclick="reset_kanagata('{{ url("index/kanagata/reset") }}',$('#kanagata_reset').val(),$('#kanagata_number2').val());">
						               Reset
						            </button>
								</div>
							</div>
						</div>
					</div>
					@endif
				  <div class="row">
				    <div class="col-xs-12">
				      <div class="box box-solid">
				        <div class="box-body" style="overflow-x: scroll;">
				          <table class="table table-bordered table-striped table-hover" id="tableKanagata">
				            <thead style="background-color: lightgrey">
				              <tr>
				              	<th>No</th>
				                <th>Employee</th>
				                <th>Date</th>
				                <th>Shift</th>
				                <th>Product</th>
				                <th>Material</th>
				                <th>Part</th>
				                <th>Process</th>
				                <th>Machine</th>
				                <th>Punch Number</th>
				                <th>Punch Value</th>
				                <th>Punch Plate Number</th>
				                <th>Punch Plate Value</th>
				                <th>Dies Number</th>
				                <th>Dies Value</th>
				                <th>Plate Number</th>
				                <th>Plate Value</th>
				                <th>Stripper Number</th>
				                <th>Stripper Value</th>
				                <th>Drawing Punch Num</th>
				                <th>Drawing Punch Value</th>
				                <th>Drawing Dies Num</th>
				                <th>Drawing Dies Value</th>
				                <th>Snap Ring Number</th>
				                <th>Snap Ring Value</th>
				                <th>Upper Knock Out Number</th>
				                <th>Upper Knock Out Value</th>
				                <th>Lower Knock Out Number</th>
				                <th>Lower Knock Out Value</th>
				                <th>Half Nuki Number</th>
				                <th>Half Nuki Value</th>
				                <th>Die Insert Number</th>
				                <th>Die Insert Value</th>

				                <th>Running Punch</th>
				                <th>Running Punch Plate</th>
				                <th>Running Dies</th>
				                <th>Running Plate</th>
				                <th>Running Stripper</th>
				                <th>Running Drawing Punch</th>
				                <th>Running Drawing Dies</th>
				                <th>Running Snap Ring</th>
				                <th>Running Upper Knock Out</th>
				                <th>Running Lower Knock Out</th>
				                <th>Running Half Nuki</th>
				                <th>Running Die Insert</th>

				                <th>Punch Status</th>
				                <th>Punch Plate Status</th>
				                <th>Dies Status</th>
				                <th>Plate Status</th>
				                <th>Stripper Status</th>
				                <th>Drawing Punch Status</th>
				                <th>Drawing Dies Status</th>
				                <th>Snap Ring Status</th>
				                <th>Upper Knock Out Status</th>
				                <th>Lower Knock Out Status</th>
				                <th>Half Nuki Status</th>
				                <th>Die Insert Status</th>
				              </tr>
				            </thead>
				            <tbody id="bodyTableKanagata">
				            </tbody>
				            <tfoot>
				              <tr>
				                <th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
				              </tr>
				            </tfoot>
				          </table>
				        </div>
				      </div>
				    </div>
				  </div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="create-modal" data-backdrop="static" data-keyboard="false">
	  <div class="modal-dialog modal-lg" style="width: 1200px">
	    <div class="modal-content">
	      <div class="modal-header" style="background-color: lightskyblue">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	        <h4 class="modal-title" align="center"><b>Pendaftaran Kanagata Baru (Manual ke Digital)</b></h4>
	      </div>
	      <div class="modal-body">
	      	<div class="box-body">
	        <div>
	          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
	            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
		            <div class="form-group">
		              <label for="">Date</label>
					  <input type="text" name="inputdate" id="inputdate" class="form-control" value="{{date('Y-m-d')}}" readonly required="required" title="">
		            </div>
		            <div class="form-group">
		             <label>PIC<span class="text-red">*</span></label>
		                <input type="text" name="inputpic" id="inputpic" class="form-control" value="{{$username}}" readonly required="required" title="">
		            </div>
		            <input type="hidden" name="inputshift" id="inputshift" class="form-control" value="Shift 1" readonly required="required" title="">
		            <input type="hidden" name="inputstart_time" id="inputstart_time" class="form-control" value="{{date('Y-m-d H:i:s')}}" readonly required="required" title="">
		            <div class="form-group">
		             <label>Process<span class="text-red">*</span></label>
		                <select class="form-control select2" name="inputprocess" id="inputprocess" style="width: 100%;" data-placeholder="Pilih Process..." required>
		                	<option value=""></option>
		                	@foreach($process as $process)
		                		<option value="{{$process->process_desc}}">{{$process->process_desc}}</option>
		                	@endforeach
		                </select>
		            </div>
		            <div class="form-group">
		             <label>Machine<span class="text-red">*</span></label>
		                <select class="form-control select2" name="inputmachine" id="inputmachine" style="width: 100%;" data-placeholder="Pilih Machine..." required>
		                	<option value=""></option>
		                	<option value="Amada 1">Amada 1</option>
		                	<option value="Amada 2">Amada 2</option>
		                	<option value="Amada 3">Amada 3</option>
		                	<option value="Amada 4">Amada 4</option>
		                	<option value="Amada 5">Amada 5</option>
		                	<option value="Amada 6">Amada 6</option>
		                	<option value="Amada 7">Amada 7</option>
		                	<option value="Komatsu 1">Komatsu 1</option>
		                	<option value="Komatsu 2">Komatsu 2</option>
		                	<option value="Komatsu 3">Komatsu 3</option>
		                	<option value="Komatsu 4">Komatsu 4</option>
		                	<option value="Komatsu 5">Komatsu 5</option>
		                	<option value="Komatsu 6">Komatsu 6</option>
		                </select>
		            </div>
	            </div>
	            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            	<div class="form-group">
		             <label>Kanagata<span class="text-red">*</span></label>
		                <select class="form-control select2" onchange="getkanagata(this.value)" name="inputkanagata" id="inputkanagata" style="width: 100%;" data-placeholder="Pilih Kanagata..." required>
		                	<option value=""></option>
		                	@foreach($kanagata as $kanagata)
		                		<option value="{{$kanagata->part}}_{{$kanagata->product}}_{{$kanagata->material_number}}_{{$kanagata->material_description}}_{{$kanagata->punch_die_number}}_{{$kanagata->id}}">{{$kanagata->part}} - {{$kanagata->material_number}} - {{$kanagata->material_name}} - {{$kanagata->material_description}} - {{$kanagata->punch_die_number}}</option>
		                	@endforeach
		                	<input type="hidden" name="id" id="id">
		                </select>
		            </div>
		            <div class="form-group">
		             <label>Part Type<span class="text-red">*</span></label>
		                <input type="text" name="inputpart" id="inputpart" class="form-control" readonly required="required" title="" placeholder="Part Type">
		            </div>
		            <div class="form-group">
		             <label>Product<span class="text-red">*</span></label>
		                <input type="text" name="inputproduct" id="inputproduct" class="form-control" readonly required="required" title="" placeholder="Product">
		            </div>
	            	<div class="form-group">
		             <label>Material Number<span class="text-red">*</span></label>
		                <input type="text" name="inputmaterial_number" id="inputmaterial_number" class="form-control" readonly required="required" title="" placeholder="Material Number">
		            </div>
		            <div class="form-group">
		             <label>Material Description<span class="text-red">*</span></label>
		                <input type="text" name="inputmaterial_description" id="inputmaterial_description" class="form-control" readonly required="required" title="" placeholder="Material Description">
		            </div>
		            <div class="form-group">
		             <label>Kanagata Number<span class="text-red">*</span></label>
		                <input type="text" name="inputpunch_die_number" id="inputpunch_die_number" class="form-control" readonly required="required" title="" placeholder="Kanagata Number">
		            </div>
	            </div>
	            <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
	            	<div class="form-group" id="punchvalue">
		             <label>Punch Value<span class="text-red">*</span></label>
		                <input type="text" name="inputpunch_value" onkeyup="inputpunch(this.value)" id="inputpunch_value" class="form-control" required="required" title="" placeholder="Punch Value">
		            </div>
		            <div class="form-group" id="punchtotal">
		             <label>Punch Total<span class="text-red">*</span></label>
		                <input type="text" name="inputpunch_total" id="inputpunch_total" class="form-control" readonly required="required" title="" placeholder="Punch Total">
		            </div>
	            	<div class="form-group" id="dievalue">
		             <label>Dies Value<span class="text-red">*</span></label>
		                <input type="text" name="inputdie_value" id="inputdie_value" onkeyup="inputdie(this.value)" class="form-control" required="required" title="" placeholder="Dies Value">
		            </div>
		            <div class="form-group" id="dietotal">
		             <label>Dies Total<span class="text-red">*</span></label>
		                <input type="text" name="inputdie_total" id="inputdie_total" class="form-control" readonly required="required" title="" placeholder="Dies Total">
		            </div>
		            <div class="form-group" id="platevalue">
		             <label>Plate Value<span class="text-red">*</span></label>
		                <input type="text" name="inputplate_value" id="inputplate_value" onkeyup="inputplate(this.value)" class="form-control" required="required" title="" placeholder="Plate Value">
		            </div>
		            <div class="form-group" id="platetotal">
		             <label>Plate Total<span class="text-red">*</span></label>
		                <input type="text" name="inputplate_total" id="inputplate_total" class="form-control" readonly required="required" title="" placeholder="Plate Total">
		            </div>
	            </div>
	          </div>
	          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><b>Close</b></button>
	            <button class="btn btn-success pull-right" onclick="create()"><b>Submit</b></button>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
	<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		            <span aria-hidden="true">&times;</span>
		          </button>
		          <h4 class="modal-title" align="center"><b>Edit Kanagata Last Lifetime</b></h4>
		        </div>
				<div class="modal-body">
			      	<div class="box-body">
			          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
			            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			            	<input type="hidden" name="url_edit" id="url_edit" class="form-control" value="" readonly required="required" title="">
				            <div class="form-group">
				              <label for="">Date</label>
							  <input type="text" name="editdate" id="editdate" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label for="">PIC</label>
							  <input type="text" name="editpic" id="editpic" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Machine</label>
				              <input type="text" name="editmachine" id="editmachine" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Product</label>
				              <input type="text" name="editproduct" id="editproduct" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Material</label>
				              <input type="text" name="editmaterial_number" id="editmaterial_number" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Part</label>
				              <input type="text" name="editpart" id="editpart" class="form-control" value="" readonly required="required" title="">
				            </div>
			            </div>
			            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			            	
				            <div class="form-group">
				              <label>Punch Number</label>
				              <input type="text" name="editpunch_number" id="editpunch_number" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Punch Value</label>
				              <input type="text" name="editpunch_value" id="editpunch_value" class="form-control" value="" readonly required="required" title="" placeholder="Enter Punch Value">
				            </div>
				            <div class="form-group">
				              <label>Punch Total</label>
				              <input type="text" name="editpunch_total" id="editpunch_total" class="form-control" value="" required="required" title="" placeholder="Enter Punch Total">
				            </div>
				            <div class="form-group">
				              <label>Dies Number</label>
				              <input type="text" name="editdies_number" id="editdies_number" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Dies Value</label>
				              <input type="text" name="editdies_value" id="editdies_value" class="form-control" value="" required="required" readonly title="" placeholder="Enter Dies Value">
				            </div>
				            <div class="form-group">
				              <label>Dies Total</label>
				              <input type="text" name="editdies_total" id="editdies_total" class="form-control" value="" required="required" title="" placeholder="Enter Dies Total">
				            </div>
			            </div>
				          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				          	<div class="modal-footer">
				              <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
				              <input type="submit" value="Update" onclick="update()" class="btn btn-primary">
				            </div>
				          </div>
			          </div>
			      </div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="reset-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		            <span aria-hidden="true">&times;</span>
		          </button>
		          <h4 class="modal-title" align="center"><b>Reset Kanagata Last Lifetime</b></h4>
		        </div>
				<div class="modal-body">
			      	<div class="box-body">
			          <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
			            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
			            	<input type="hidden" name="url_reset" id="url_reset" class="form-control" value="" readonly required="required" title="">
				            <div class="form-group">
				              <label>Product</label>
				              <input type="text" name="resetproduct" id="resetproduct" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Material</label>
				              <input type="text" name="resetmaterial_number" id="resetmaterial_number" class="form-control" value="" readonly required="required" title="">
				            </div>
				            <div class="form-group">
				              <label>Part</label>
				              <input type="text" name="resetpart" id="resetpart" class="form-control" value="" readonly required="required" title="">
				            </div>
			            </div>
			            <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
				            <div id="punch_reset">
				            	<div class="form-group">
					              <label>Punch Number</label>
					              <input type="text" name="resetpunch_number" id="resetpunch_number" class="form-control" value="" readonly required="required" title="">
					            </div>
					            <div class="form-group">
					              <label>Punch Value</label>
					              <input type="text" name="resetpunch_value" id="resetpunch_value" class="form-control" value="" readonly required="required" title="" placeholder="Enter Punch Value">
					            </div>
					            <div class="form-group">
					              <label>Punch Total</label>
					              <input type="text" name="resetpunch_total" id="resetpunch_total" class="form-control" value="" required="required" title="" placeholder="Enter Punch Total" readonly>
					            </div>
				            </div>
				            <div id="dies_reset">
				            	<div class="form-group">
					              <label>Dies Number</label>
					              <input type="text" name="resetdies_number" id="resetdies_number" class="form-control" value="" readonly required="required" title="">
					            </div>
					            <div class="form-group">
					              <label>Dies Value</label>
					              <input type="text" name="resetdies_value" id="resetdies_value" class="form-control" value="" required="required" readonly title="" placeholder="Enter Dies Value">
					            </div>
					            <div class="form-group">
					              <label>Dies Total</label>
					              <input type="text" name="resetdies_total" id="resetdies_total" class="form-control" value="" required="required" title="" placeholder="Enter Dies Total" readonly>
					            </div>
				            </div>
			            </div>
				          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				          	<div class="modal-footer">
				              <button type="button" class="btn btn-danger pull-left" data-dismiss="modal">Close</button>
				              <input type="submit" value="Reset" onclick="reset($('#kanagata_reset').val(),$('#kanagata_number2').val())" class="btn btn-success">
				            </div>
				          </div>
			          </div>
			      </div>
			</div>
		</div>
	</div>
</section>
@endsection


@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	jQuery(document).ready(function() {
		$('#date_from').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('#date_to').datepicker({
			autoclose: true,
			format: 'yyyy-mm-dd',
			todayHighlight: true
		});
		$('.select2').select2({
			dropdownParent: $('#create-modal'),
			allowClear:true
		});
		$('.select3').select2({
			allowClear:true
		});
		$('.select4').select2({
			allowClear:true
		});
	});

	function fetchKanagata() {
		$("#loading").show();
		var data = {
			date_from:$('#date_from').val(),
			date_to:$('#date_to').val(),
			process:$('#process').val(),
		}
		$.get('{{ url("fetch/press/kanagata") }}', data, function(result, status, xhr){
			if(result.status){
				$('#tableKanagata').DataTable().clear();
				$('#tableKanagata').DataTable().destroy();
				$('#bodyTableKanagata').html("");
				var tableData = "";
				var index = 1;
				$.each(result.kanagata, function(key, value) {
					tableData += '<tr>';
					tableData += '<td>'+ index +'</td>';
					tableData += '<td>'+ value.name +'</td>';
					tableData += '<td>'+ value.date +'</td>';
					tableData += '<td>'+ value.shift +'</td>';
					tableData += '<td>'+ value.product +'</td>';
					tableData += '<td>'+ value.material_number +'</td>';
					tableData += '<td>'+ value.material_name +'</td>';
					tableData += '<td>'+ value.process_detail +'</td>';
					tableData += '<td>'+ value.machine +'</td>';
					if (value.process == 'Nukishibori') {
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td>'+ (value.ppl_number || '') +'</td>';
						tableData += '<td>'+ (value.ppl_value || '') +'</td>';
					}else{
						tableData += '<td>'+ (value.punch_number || '') +'</td>';
						tableData += '<td>'+ (value.punch_value || '') +'</td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
					}
					tableData += '<td>'+ (value.die_number || '') +'</td>';
					tableData += '<td>'+ (value.die_value || '') +'</td>';
					if (value.process == 'Nukishibori') {
						tableData += '<td></td>';
						tableData += '<td></td>';
						tableData += '<td>'+ (value.plate_number || '') +'</td>';
						tableData += '<td>'+ (value.plate_value || '') +'</td>';
					}else{
						tableData += '<td>'+ (value.plate_number || '') +'</td>';
						tableData += '<td>'+ (value.plate_value || '') +'</td>';
						tableData += '<td></td>';
						tableData += '<td></td>';
					}
					tableData += '<td>'+ (value.dp_number || '') +'</td>';
					tableData += '<td>'+ (value.dp_value || '') +'</td>';
					tableData += '<td>'+ (value.dd_number || '') +'</td>';
					tableData += '<td>'+ (value.dd_value || '') +'</td>';
					tableData += '<td>'+ (value.snap_number || '') +'</td>';
					tableData += '<td>'+ (value.snap_value || '') +'</td>';
					tableData += '<td>'+ (value.upper_number || '') +'</td>';
					tableData += '<td>'+ (value.upper_value || '') +'</td>';
					tableData += '<td>'+ (value.lower_number || '') +'</td>';
					tableData += '<td>'+ (value.lower_value || '') +'</td>';
					tableData += '<td>'+ (value.half_number || '') +'</td>';
					tableData += '<td>'+ (value.half_value || '') +'</td>';
					tableData += '<td>'+ (value.dinsert_number || '') +'</td>';
					tableData += '<td>'+ (value.dinsert_value || '') +'</td>';
					
					if (value.process == 'Nukishibori') {
						tableData += '<td></td>';
						tableData += '<td>'+ (value.ppl_total || '') +'</td>';
					}else{
						tableData += '<td>'+ (value.punch_total || '') +'</td>';
						tableData += '<td></td>';
					}
					tableData += '<td>'+ (value.die_total || '') +'</td>';
					if (value.process == 'Nukishibori') {
						tableData += '<td></td>';
						tableData += '<td>'+ (value.plate_total || '') +'</td>';
					}else{
						tableData += '<td>'+ (value.plate_total || '') +'</td>';
						tableData += '<td></td>';
					}
					tableData += '<td>'+ (value.dp_total || '') +'</td>';
					tableData += '<td>'+ (value.dd_total || '') +'</td>';
					tableData += '<td>'+ (value.snap_total || '') +'</td>';
					tableData += '<td>'+ (value.upper_total || '') +'</td>';
					tableData += '<td>'+ (value.lower_total || '') +'</td>';
					tableData += '<td>'+ (value.half_total || '') +'</td>';
					tableData += '<td>'+ (value.dinsert_total || '') +'</td>';

					if (value.process == 'Nukishibori') {
						tableData += '<td></td>';
						tableData += '<td>'+ (value.ppl_status || '') +'</td>';
					}else{
						tableData += '<td>'+ (value.punch_status || '') +'</td>';
						tableData += '<td></td>';
					}
					tableData += '<td>'+ (value.die_status || '') +'</td>';
					if (value.process == 'Nukishibori') {
						tableData += '<td></td>';
						tableData += '<td>'+ (value.plate_status || '') +'</td>';
					}else{
						tableData += '<td>'+ (value.plate_status || '') +'</td>';
						tableData += '<td></td>';
					}
					tableData += '<td>'+ (value.dp_status || '') +'</td>';
					tableData += '<td>'+ (value.dd_status || '') +'</td>';
					tableData += '<td>'+ (value.snap_status || '') +'</td>';
					tableData += '<td>'+ (value.upper_status || '') +'</td>';
					tableData += '<td>'+ (value.lower_status || '') +'</td>';
					tableData += '<td>'+ (value.half_status || '') +'</td>';
					tableData += '<td>'+ (value.dinsert_status || '') +'</td>';
					tableData += '</tr>';
					index++;
				});
				$('#bodyTableKanagata').append(tableData);

				var table = $('#tableKanagata').DataTable({
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
						}
						]
					},
					'paging': true,
					'lengthChange': true,
					'pageLength': 10,
					'searching': true	,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"bSortCellsTop": true,
					"bFilter": true,
					"processing": true
				});
				
				

				$('#tableKanagata tfoot th').each(function(){
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				});

				table.columns().every( function () {
					var that = this;
					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#tableKanagata tfoot tr').appendTo('#tableKanagata thead');

				$("#loading").hide();
				openSuccessGritter('Success','Success Get Data');
			} else {
				audio_error.play();
				openErrorGritter('Error',result.message);
			}
		});
	}

	function fetchLastData() {
		// $("#loading").show();
		$.get('{{ url("excel/press/kanagata_last_data") }}',function(result, status, xhr){
			// if(result.status){
			// 	$("#loading").hide();
			// 	openSuccessGritter('Success','Success Get Data');
			// } else {
			// 	audio_error.play();
			// 	openErrorGritter('Error','Success Get Data');
			// }
		});
	}

	function create() {
		$("#loading").show();
		var data = {
			date:$('#inputdate').val(),
			pic:$('#inputpic').val(),
			shift:$('#inputshift').val(),
			product:$('#inputproduct').val(),
			material_number:$('#inputmaterial_number').val(),
			process:$('#inputprocess').val(),
			machine:$('#inputmachine').val(),
			punch_number:$('#inputpunch_die_number').val(),
			die_number:$('#inputpunch_die_number').val(),
			plate_number:$('#inputpunch_die_number').val(),
			punch_value:$('#inputpunch_value').val(),
			die_value:$('#inputdie_value').val(),
			plate_value:$('#inputplate_value').val(),
			punch_total:$('#inputpunch_total').val(),
			die_total:$('#inputdie_total').val(),
			plate_total:$('#inputplate_total').val(),
			start_time:$('#inputstart_time').val(),
			end_time:'{{date("Y-m-d H:i:s")}}',
			punch_status:'Running',
			die_status:'Running',
			part:$('#inputpart').val()
		}

		$.post('{{ url("input/press/kanagata_lifetime") }}', data, function(result, status, xhr){
			if(result.status){
				$("#loading").hide();
				$("#create-modal").modal('hide');
				$('#inputdate').val("{{date('Y-m-d')}}");
				$('#inputpic').val("{{$username}}");
				$('#inputpart').val("");
				$('#inputmaterial_description').val("");
				$('#inputshift').val("Shift 1");
				$('#inputproduct').val("");
				$('#inputmaterial_number').val("");
				$("#inputkanagata").prop('selectedIndex', 0).change();
				$("#inputprocess").prop('selectedIndex', 0).change();
				$("#inputmachine").prop('selectedIndex', 0).change();
				$('#inputpunch_die_number').val("");
				$('#inputpunch_value').val("");
				$('#inputdie_value').val("");
				$('#inputplate_value').val("");
				$('#inputpunch_total').val("");
				$('#inputdie_total').val("");
				$('#inputplate_total').val("");
				$('#inputstart_time').val("");
				openSuccessGritter('Success','Pendaftaran Kanagata Baru Sukses');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Create Kanagata Lifetime Failed');
			}
		});
	}

	function cancelAll() {
		$('#inputdate').val("{{date('Y-m-d')}}");
		$('#inputpic').val("{{$username}}");
		$('#inputpart').val("");
		$('#inputmaterial_description').val("");
		$('#inputshift').val("Shift 1");
		$('#inputproduct').val("");
		$('#inputmaterial_number').val("");
		$("#inputkanagata").prop('selectedIndex', 0).change();
		$("#inputprocess").prop('selectedIndex', 0).change();
		$("#inputmachine").prop('selectedIndex', 0).change();
		$('#inputpunch_die_number').val("");
		$('#inputpunch_value').val("");
		$('#inputdie_value').val("");
		$('#inputplate_value').val("");
		$('#inputpunch_total').val("");
		$('#inputdie_total').val("");
		$('#inputplate_total').val("");
		$('#inputstart_time').val("");
	}

	function getkanagata(id) {
		console.log(id);
		$('#inputpart').val(id.split('_')[0]);
		$('#inputproduct').val(id.split('_')[1]);
		$('#inputmaterial_number').val(id.split('_')[2]);
		$('#inputmaterial_description').val(id.split('_')[3]);
		$('#inputpunch_die_number').val(id.split('_')[4]);
		$('#id').val(id.split('_')[5]);
		// var data = {
		// 	id:id
		// }
		// $.get('{{ url("fetch/press/get_kanagata") }}', data, function(result, status, xhr){
		// 	if(result.status){
		// 		$('#inputpart').val(result.lists.part);
		// 		$('#inputproduct').val(result.lists.product);
		// 		$('#inputmaterial_number').val(result.lists.material_number);
		// 		$('#inputmaterial_description').val(result.lists.material_description);
		// 		$('#inputpunch_die_number').val(result.lists.punch_die_number);
		// 		if (result.lists.part == 'PUNCH' || result.lists.part == 'PUNCH FLAT') {
		// 			$.ajax({
		//                 url: "{{ route('kanagata_lifetime.getkanagatalifetime') }}?kanagata=" + result.lists.part +"&kanagata_number="+result.lists.punch_die_number,
		//                 method: 'GET',
		//                 success: function(data) {
		//                   var json = data;
		//                   var data = data.data;
		//                   // $('#punchvalue').show();
		//                   // $('#punchtotal').show();
		//                   // $('#dievalue').hide();
		//                   // $('#dietotal').hide();
		//                   $('#inputdie_value').attr('readonly', true).val(data.die_value);
		//                   $('#inputdie_total').val(data.die_total);
		//                   $('#inputpunch_value').attr('readonly', false).val("");
		//                   $('#inputpunch_total').val(0);
		//                 }
		//             });
		// 		}else if(result.lists.part == 'DIE'){
		// 			$.ajax({
		//                 url: "{{ route('kanagata_lifetime.getkanagatalifetime') }}?kanagata=" + result.lists.part +"&kanagata_number="+result.lists.punch_die_number,
		//                 method: 'GET',
		//                 success: function(data) {
		//                   var json = data;
		//                   var data = data.data;
		//                   // $('#punchvalue').hide();
		//                   // $('#punchtotal').hide();
		//                   // $('#dievalue').show();
		//                   // $('#dietotal').show();
		//                   $('#inputpunch_value').attr('readonly', true).val(data.punch_value);
		//                   $('#inputpunch_total').val(data.punch_total);
		//                   $('#inputdie_value').attr('readonly', false).val("");
		//                   $('#inputdie_total').val(0);
		//                 }
		//             });
		// 		}
		// 	}
		// 	else{
		// 		audio_error.play();
		// 		openErrorGritter('Error', result.message);
		// 		$('#operator').val('');
		// 	}
		// });
	}

	function inputpunch(value) {
		$('#inputpunch_total').val(value);
	}

	function inputdie(value) {
		$('#inputdie_total').val(value);
	}

	function inputplate(value) {
		$('#inputplate_total').val(value);
	}

	function edit_kanagata(url,kanagata,kanagata_number) {
		if (kanagata_number === '' || kanagata === '') {
			alert('Isi Jenis Kanagata dan Kanagata Number.')
			$("#edit-modal").modal('hide');
		}else{
			$("#edit-modal").modal('show');
			$.ajax({
                url: "{{ route('kanagata_lifetime.getkanagatalifetime') }}?kanagata=" + kanagata +"&kanagata_number="+kanagata_number,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  // obj = JSON.parse(json);
                  // console.log(data.data);
                  var data = data.data;
                  $("#url_edit").val(url+'/'+data.kanagata_log_id);
                  $("#editdate").val(data.date);
                  $("#editpic").val(data.pic_name);
                  $("#editmachine").val(data.machine);
                  $("#editproduct").val(data.product);
                  $("#editmaterial_number").val(data.material_number);
                  $("#editpart").val(data.part);
                  $("#editpunch_number").val(data.punch_number);
                  $("#editpunch_total").val(data.punch_total);
                  $("#editpunch_value").val(data.punch_value);
                  $("#editdies_number").val(data.die_number);
                  $("#editdies_value").val(data.die_value);
                  $("#editdies_total").val(data.die_total);
                }
            });
		}
    }

    function reset_kanagata(url,kanagata,kanagata_number) {
		if (kanagata_number === '' || kanagata === '') {
			alert('Isi Jenis Kanagata dan Kanagata Number.')
			$("#reset-modal").modal('hide');

		}else{
			$("#reset-modal").modal('show');
			$.ajax({
                url: "{{ route('kanagata_lifetime.getkanagatalifetime') }}?kanagata=" + kanagata +"&kanagata_number="+kanagata_number,
                method: 'GET',
                success: function(data) {
                  var json = data;
                  var data = data.data;
                  $("#url_reset").val(url+'/'+data.kanagata_log_id);
                  $("#resetproduct").val(data.product);
                  $("#resetmaterial_number").val(data.material_number);
                  $("#resetpart").val(data.part);
                  if (kanagata === 'Punch') {
                  	$('#punch_reset').show();
                  	$('#dies_reset').hide();
                  	$("#resetpunch_number").val(data.punch_number);
                    $("#resetpunch_total").val(data.punch_total);
                    $("#resetpunch_value").val(data.punch_value);
                  }
                  if (kanagata === 'Dies') {
                  	$('#punch_reset').hide();
                  	$('#dies_reset').show();
                  	$("#resetdies_number").val(data.die_number);
                  	$("#resetdies_value").val(data.die_value);
                  	$("#resetdies_total").val(data.die_total);
                  }
                }
            });
		}
    }

    function reset_kanagata_periodik() {
    	if (confirm("Apakah Anda yakin ingin reset periodik?")) {
    		$("#loading").show();
    		var data = {
	    		part : $("#kanagata_reset_periodik").val(),
	    		part_number : $("#kanagata_number_periodik").val(),
	    	}

	    	$.post('{{ url("index/kanagata/reset/periodik") }}', data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Kanagata Periodik Berhasil Direset');
					window.location.reload();
				} else {
					$("#loading").hide();
					audio_error.play();
					openErrorGritter('Error',result.message);
				}
			});
    	}
    }

    function reset(kanagata,kanagata_number) {
    	if (confirm('Apakah Anda ingin RESET Kanagata?')) {
    		$("#loading").show();
			var data = {
				kanagata:kanagata,
				kanagata_number:kanagata_number
			}
			$.post('{{ url("index/kanagata/reset") }}', data, function(result, status, xhr){
				if(result.status){
					$("#loading").hide();
					openSuccessGritter('Success','Kanagata Lifetime has been reset');
					window.location.reload();
				} else {
					$("#loading").hide();
					audio_error.play();
					openErrorGritter('Error','Reset Kanagata Lifetime Failed');
				}
			});
	    }
    }

    function update(){
		var punch_total = $('#editpunch_total').val();
		var die_total = $('#editdies_total').val();
		var url = $('#url_edit').val();

		var data = {
			punch_total:punch_total,
			die_total:die_total
		}
		
		$.post(url, data, function(result, status, xhr){
			if(result.status){
				$("#edit-modal").modal('hide');
				// $('#example1').DataTable().ajax.reload();
				// $('#example2').DataTable().ajax.reload();
				openSuccessGritter('Success','Kanagata Lifetime has been updated');
				window.location.reload();
			} else {
				audio_error.play();
				openErrorGritter('Error','Update Kanagata Lifetime Failed');
			}
		});
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

	jQuery(document).ready(function() {
		$('#example1 tfoot th').each( function () {
			var title = $(this).text();
			$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
		} );
		var table = $('#example1').DataTable({
			"order": [],
			'dom': 'Bfrtip',
			'responsive': true,
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
			}
		});

		table.columns().every( function () {
			var that = this;

			$( 'input', this.footer() ).on( 'keyup change', function () {
				if ( that.search() !== this.value ) {
					that
					.search( this.value )
					.draw();
				}
			} );
		} );

		$('#example1 tfoot tr').appendTo('#example1 thead');

	});

	
</script>
  <script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
  <script src="{{ url("js/buttons.flash.min.js")}}"></script>
  <script src="{{ url("js/jszip.min.js")}}"></script>
  
  <script src="{{ url("js/buttons.html5.min.js")}}"></script>
  <script src="{{ url("js/buttons.print.min.js")}}"></script>
  <script>
    jQuery(document).ready(function() {
    	$('body').toggleClass("sidebar-collapse");
    });
  </script>
@endsection
			