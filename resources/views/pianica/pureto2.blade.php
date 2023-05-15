@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link href="{{ url("css/jquery.numpad.css") }}" rel="stylesheet">


<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		/*text-align:center;*/
		overflow:hidden;
	}
	tbody>tr>td{
		/*text-align:center;*/
		padding-left: 5px !important;
	}
	tfoot>tr>th{
		/*text-align:center;*/
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

	/*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
		background-color: #ffd8b7;
	}*/

	/*.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}*/
	input[type=number] {
		-moz-appearance:textfield; /* Firefox */
	}
	#loading, #error { display: none; }

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
</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }} ~ Line <?php if (!str_contains(Auth::user()->name,'Line')) {
			echo '1 - (Default By Login With NIK)';
		}else{
			echo substr(Auth::user()->name,-1);
		} ?> <small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<input type="hidden" name="started_at" id="started_at">
	<div class="row">
		<div class="col-xs-6">
			<div class="box box-primary">
        		<div class="box box-body">
		          <table width="100%">
		            <tr>
		              <td style="text-align: center; font-size: 30px; background-color: rgba(126,86,134,.7); color: white;">Total Production</td>
		            </tr>
		            <tr>
		              <td style="text-align: center; font-size: 30px; background-color: #F0FFF0; color: black;" id="total">0</td>
		            </tr>
		          </table>
		          <b>Tag RFID</b>  <br>
		          <input type="text" name="rfid" id="rfid" class="form-control"  autofocus style="text-align: center; font-size: 30px; height: 45px" placeholder="RFID"><br>
		          <center><button class="btn btn-lg btn-primary" onclick="rf()">Change</button></center>
		          <span  ><b id="textmodel" style="color:red"> [ Model ] - </b><b class="destroy" id="modelb"></b></span><br>
		            <div class="col-xs-12" style="padding: 0px">
		              @foreach($models as $model) 
		              @if($model == "P-37")
		              <div class="col-xs-3"style="padding: 0px 5px 0px 5px" ><button class="btn btn-lg" onclick="model(this.id)" id="{{$model}}" style="width:100%; background-color:  #800000; color: white">{{$model}}</button></div>
		              @elseif($model == "P-32")
		              <div class="col-xs-3"style="padding: 0px 5px 0px 5px" ><button class="btn btn-lg" onclick="model(this.id)" id="{{$model}}" style="width:100%; background-color:  rgb(135,206,250); color: black">{{$model}}</button></div>
		              @elseif($model == "PS-25F")
		              <div class="col-xs-3"style="padding: 0px 5px 0px 5px" ><button class="btn btn-lg" onclick="model(this.id)" id="{{$model}}" style="width:100%; background-color:  #bdff80; color: black">{{$model}}</button></div>
		              @else
		              <div class="col-xs-3"style="padding: 0px 5px 0px 5px" ><button class="btn btn-lg" onclick="model(this.id)" id="{{$model}}" style="width:100%; background-color:  rgb(240,230,140); color: black">{{$model}}</button></div>
		              @endif
		              @endforeach
		              <br>          
		            </div>
		            &nbsp;
	          	</div>
	        </div>
		</div>
		<div class="col-xs-6">
			<div class="box box-success">
        		<div class="box box-body">
		            <button class="btn btn-warning btn-lg pull-right" onclick="openmodal()">Change Operator Pureto</button>
		            <button class="btn btn-primary btn-lg pull-right" style="margin-right: 10px;" onclick="openModalPerolehan()">Audit Screw</button>
		            <span class="info-box-text" style="font-size: 25px">OPERATOR PURETO</span>
		            <span class="info-box-number" id="p_pureto_nama" style="font-size: 25px; color:blue">[ ]</span><b id="p_pureto" hidden></b> <b id="p_pureto_nik" hidden></b><input type="hidden" name="employee_id" id="employee_id">
		            <span class="info-box-text" style="font-size: 25px">RFID</span>
		            <span class="info-box-number" id="p_rfid" style="font-size: 25px; color:blue">[ ]</span>
		            <span class="info-box-text" style="font-size: 25px">MODEL</span>
		            <span class="info-box-number" id="p_model" style="font-size: 25px;color:blue">[ ]</span>
		            <span class="info-box-text" style="font-size: 25px">OPERATOR BENSUKI</span>
		            <span class="info-box-number" id="p_bensuki" style="font-size: 25px; color:blue">[ ] </span><b id="nikbensuki" hidden></b>
		        </div>
		    </div>
		</div>
		<div class="col-xs-12">
			<div class="box box-warning">
       		<div class="box box-body">
			<span ><b id="opbentetx" style="color:red"> [ Op Bensuki ] - </b> <b class="destroy" id="posisi"></b> <b class="destroy" id="opben"></b></span><br>
	          <div class="table-responsive">

	            <div class="col-xs-4 table-responsive">
	              <table>
	                <tr><td colspan="6" style="padding: 10px" align="center">HIGH</td></tr>
	                <tr>

	                  @foreach($highs  as $nomor => $highs)
	                  @if($highs->warna =="M" )
	                  <td style="padding: 10px"><button class="btn btn-lg btn-danger" id="{{ $highs->nama}}" name="{{ $highs->nik}}"onclick="opben('HIGH',this.id,this.name,this)">
	                    {{$a = explode('-', trim($highs->kode))[0]}}</button></td>
	                    @endif                
	                    @endforeach
	                  </tr>
	                  <tr>
	                    @foreach($high  as $nomor => $high)
	                    @if($high->warna =="H" )
	                    <td style="padding: 10px"><button class="btn btn-lg " style="background-color: black; color: white" id="{{ $high->nama}}" name="{{ $high->nik}}"onclick="opben('HIGH',this.id,this.name,this)">{{$a = explode('-', trim($high->kode))[0]}}</button></td>
	                    @endif                
	                    @endforeach                
	                  </tr>
	                </table>                       
	              </div> 

	              
	              <div class="col-xs-4 table-responsive">
	                <table>
	                  <tr><td colspan="6" style="padding: 10px" align="center">MIDDLE</td></tr>
	                  <tr>

	                    @foreach($middles  as $nomor => $middles)
	                    @if($middles->warna =="M" )
	                    <td style="padding: 10px"><button class="btn btn-lg btn-danger" id="{{ $middles->nama}}" name="{{ $middles->nik}}"  onclick="opben('MIDDLE',this.id,this.name,this)">
	                      {{$a = explode('-', trim($middles->kode))[0]}}</button></td>
	                      @endif                
	                      @endforeach
	                    </tr>
	                    <tr>
	                      @foreach($middle  as $nomor => $middle)
	                      @if($middle->warna =="H" )
	                      <td style="padding: 10px"><button class="btn btn-lg " style="background-color: black; color: white" id="{{ $middle->nama}}" name="{{ $middle->nik}}"onclick="opben('MIDDLE',this.id,this.name,this)">{{$a = explode('-', trim($middle->kode))[0]}}</button></td>
	                      @endif                
	                      @endforeach                
	                    </tr>
	                  </table>
	                </div>

	                <div class="col-xs-4 table-responsive">
	                  <table>
	                    <tr><td colspan="6" style="padding: 10px" align="center">LOW</td></tr>
	                    <tr>                  
	                      @foreach($lows  as $nomor => $lows)
	                      @if($lows->warna =="M" )
	                      <td style="padding: 10px"><button class="btn btn-lg btn-danger" id="{{ $lows->nama}}" name="{{ $lows->nik}}" onclick="opben('LOW',this.id,this.name,this)">
	                        {{$a = explode('-', trim($lows->kode))[0]}}</button></td>
	                        @endif                
	                        @endforeach
	                      </tr>
	                      <tr>
	                        @foreach($low  as $nomor => $low)
	                        @if($low->warna =="H" )
	                        <td style="padding: 10px"><button class="btn btn-lg " style="background-color: black; color: white" id="{{ $low->nama}}" name="{{ $low->nik}}" onclick="opben('LOW',this.id,this.name,this)">{{$a = explode('-', trim($low->kode))[0]}}</button></td>
	                        @endif                
	                        @endforeach                
	                      </tr>
	                    </table>
	                  </div>

	                  <button class="btn btn-lg btn-success pull-right" onclick="simpan()" style="margin: 0px 0px 0px 0px; " >Save</button>             
	                </div>
	              </div>
	            </div>
	        </div> 
		</div>
	</div>

	<div class="modal modal-default fade" id="edit">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Your RFID </h4>
          </div>
          <div class="modal-body" >
            <span>RFID</span>
            <input type="text" name="oppureto" id="oppureto"  class="form-control" autofocus style="text-align: center;  font-size: 30px; height: 45px" placeholder="RFID">
            
          </div>
          <div class="modal-footer">
           <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="display: none" id="ubahpureto2">Close</button>
           <button type="button" class="btn btn-primary pull-right btn-lg" style="display: none" id="ubahpureto" onclick="openpureto()">Change</button>
           {{-- <a id="modalEditButton" href="#" type="button" class="btn btn-outline">Confirm</a> --}}
         </div>
       </div>
     </div>
   </div>

   <div class="modal modal-default fade" id="modalPerolehan">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header" style="background-color: orange;color: white;text-align: center;">
            <h4 class="modal-title" style="font-size: 20px;font-weight: bold;">Audit Screw</h4>
          </div>
          <div class="modal-body" >
            <div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
              <center><span style="font-size: 20px;font-weight: bold;">PEROLEHAN</span></center>
              <table id="tablePerolehan" class="table table-bordered" style="margin:0;margin-top: 10px;">
                <thead style="background-color: rgb(126,86,134); color: #fff;">
                  <tr>
                    <th>Model</th>
                    <th>Qty</th>
                    <th>Screw</th>
                  </tr>
                </thead>
                <tbody id="bodyTablePerolehan">
                  <tr>
                    <td style="text-align: right;padding-right: 7px;">0</td>
                    <td style="text-align: right;padding-right: 7px;">0</td>
                    <td style="text-align: right;padding-right: 7px;">0</td>
                  </tr>
                </tbody>
                <tfoot>
                	<tr>
                		<th colspan="2" style="text-align: right;padding-right: 7px;">TOTAL</th>
                		<th style="text-align: right;padding-right: 7px;" id="totalScrew"></th>
                	</tr>
                </tfoot>
              </table>
            </div>
            <div class="col-xs-6" style="padding-left: 5px;padding-right: 0px;border-left: 2px solid black;">
            	<center><span style="font-size: 20px;font-weight: bold;">COUNTER SCREW</span></center>
            	<label>Counter Screw</label>
            	<br>
            	<input type="number" class="form-control numpad" name="totalCounter" id="totalCounter" style="font-size: 20px;padding: 5px;text-align: center;" placeholder="Counter">
            	<br>
            	<label>Screw NG / Trial</label>
            	<br>
            	<input type="number" class="form-control numpad" name="totalNg" id="totalNg" style="font-size: 20px;padding: 5px;text-align: center;" placeholder="Screw NG / Trial">
            	<br>
            	<label>Auditor</label>
            	<br>
            	<input type="text" class="form-control" name="auditor" id="auditor" style="font-size: 20px;padding: 5px;text-align: center;" placeholder="Input NIK Auditor">
            	<input type="hidden" class="form-control" name="auditor_id" id="auditor_id">
            	<input type="hidden" class="form-control" name="auditor_name" id="auditor_name">
            </div>
          </div>
          <div class="modal-footer">
          	<button class="btn btn-success" style="width: 100%;font-size: 20px;font-weight: bold;margin-top: 20px;" onclick="confirmAudit()">
        		CONFIRM
        	</button>
         </div>
       </div>
     </div>
   </div>
</section>
@endsection
@section('scripts')

<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	$.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
	$.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
	$.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
	$.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
	$.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
	$.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

	jQuery(document).ready(function() {
		$('#started_at').val('');
		$('.numpad').numpad({
			hidePlusMinusButton : true,
			decimalSeparator : '.'
		});
		$('#oppureto').focus();
	      // gettotalng('biasa');
	      $('#oppureto').val("");
	      $('#rfid').val("");
	      $('#ubahpureto').css({'display' : 'none'})
	      $('#edit').modal({backdrop: 'static', keyboard: false});
	      $('#edit').modal('show');
	      $('#edit').on('shown.bs.modal', function() {
	        $('#oppureto').focus();
	      })
	      $('#entrydate').datepicker({
	        autoclose: true,
	        format: 'yyyy-mm-dd',
	      });
		$('body').toggleClass("sidebar-collapse");
	});

	$('#oppureto').keydown(function(event) {
          if (event.keyCode == 13 || event.keyCode == 9) {
            // if($("#oppureto").val().length == 10){
              pureto(); 
              getpureto();
              gettotalng('biasa');
              // return false;
            // }
            // else{
            //   $("#oppureto").val("");
            //   openErrorGritter('Error!', 'RFID number invalid.');
            // }
          }
        }); 

        $('#rfid').keydown(function(event) {
          if (event.keyCode == 13 || event.keyCode == 9) {
            if($("#rfid").val().length == 10){
              check_tag();
            }
            else{
              $("#rfid").val("");
              alert('Error!', 'RFID number invalid.');
            }
          }
        });

        function openModalPerolehan() {
          gettotalng('perolehan');
          $('#auditor').val('');
          $('#auditor_id').val('');
          $('#auditor_name').val('');
          $('#totalNg').val('');
          $('#totalCounter').val('');
          $('#modalPerolehan').modal('show');
        }

        function rf() {            
          $('#rfid').val("");
          $('#rfid').removeAttr('disabled');
          $('#rfid').focus();
          $('#p_rfid').text("[ ]");
        } 

        function check_tag() {
        	$('#loading').show();
          var data ={
            tag : $('#rfid').val()
          }
          
          $.get('{{ url("fetch/check_tag") }}', data, function(result, status, xhr){
            if (result.tag > 0) {
              openErrorGritter('Error', 'Tag Masih Digunakan');
              $('#loading').hide();
              $('#rfid').val('');
              $('#started_at').val('');
              $('#rfid').focus();
              return false;
            } else {
              $('#started_at').val(result.started_at);
              openSuccessGritter('Sukses', 'Scan Success');
              $('#rfid').prop('disabled', true);
              var id = $('#rfid').val();           
              $('#p_rfid').text(id);
              $('#loading').hide();
              return false;
            }
          })
        }

        $('#auditor').keydown(function(event) {
        	if (event.keyCode == 13 || event.keyCode == 9) {
		        if($("#auditor").val().length > 7){
		          var data = {
		          	employee_id:$('#auditor').val()
		          }
		          $.get('{{ url("scan/pn/qa_audit") }}', data, function(result, status, xhr){
		            if (result.status) {
		            	$('#auditor').val(result.emp.employee_id+' - '+result.emp.name);
		            	$('#auditor_id').val(result.emp.employee_id);
		            	$('#auditor_name').val(result.emp.name);
		            	openSuccessGritter('Success!','Success Input Auditor')
		            } else {
		              $("#auditor_id").val("");
		          	  openErrorGritter('Error!',result.message);
		            }
		          })
		        }
		        else{
		          $("#auditor_id").val("");
		          openErrorGritter('Error!',result.message);
		        }
		    }
        }); 

        function openmodal() {
          $('#ubahpureto2').css({'display' : 'block'})
          $('#ubahpureto').css({'display' : 'block'})
          $('#edit').modal('show');
          $('#oppureto').prop('disabled', true);
        }         

        function pureto() {
        	$('#started_at').val('');
          $("#loading").show();
          var pureto = $('#oppureto').val();
          $('#p_pureto').text(pureto);
          $("#loading").hide();
        }       

        function openpureto() {
          $('#oppureto').val("");
          $('#oppureto').removeAttr('disabled');
          $('#oppureto').focus();
        }
        function model(id) {
          $('#modelb').text(""+id+" ");
          $('#p_model').text(id);
          $('#textmodel').css({'color':'black'})
        }

        function opben(group,id,nik,kode) {
          var code = $(kode).text().trim()+"-"+group;
          $('#opben').text(" "+id+"");
          $('#posisi').text(" "+group+"-");
          $('#nikbensuki').text(nik);
          $('#kode').text(code);
          $('#p_bensuki').text(group+"- "+id);             
          $('#opbentetx').css({'color':'black'});
        }

        function confirmAudit() {
        	$("#loading").show();
        	var screw_counter = $('#totalCounter').val();
        	var screw_system = $('#totalScrew').text();
        	var screw_ng = $('#totalNg').val();
        	var auditor_id = $('#auditor_id').val();
        	var auditor_name = $('#auditor_name').val();
        	var a = "{{Auth::user()->name}}";
          	if (a.match(/line/gi)) {
          		var line = a.substr(a.length - 1);
          	}else{
          		var line = 1;
          	}
        	if (screw_counter == '' || screw_ng == '' || auditor_name == '') {
        		audio_error.play();
        		$("#loading").hide();
        		openErrorGritter('Error!','Isi Semua Data');
        		return false;
        	}

        	var data = {
        		screw_counter:screw_counter,
				screw_system:screw_system,
				screw_ng:screw_ng,
				auditor_id:auditor_id,
				auditor_name:auditor_name,
				line:line,
        	}

        	$.post('{{ url("input/pn/audit_screw") }}', data, function(result, status, xhr){
              if(xhr.status == 200){
                if(result.status){
                	$('#totalCounter').val('');
                	$('#totalNg').val('');
                	$('#auditor').val('');
                	$('#auditor_id').val('');
                	$('#auditor_name').val('');
                	$('#modalPerolehan').modal('hide');
                	openSuccessGritter('Success!','Sukses Input Audit');
                 	$("#loading").hide();
                }
                else{
                  $("#loading").hide();
                  openErrorGritter('Error!', result.message);
                }
              }
              else{
                $("#loading").hide();
                alert("Disconnected from server");
              }
            });
        }

        function simpan() {
          $("#loading").show();


          var tag = $('#p_rfid').text();
          var model = $('#p_model').text();
          var pureto = $('#p_pureto_nik').text();
          var bensuki = $('#nikbensuki').text();
          var a = "{{Auth::user()->name}}";
          if (a.match(/line/gi)) {
      		var line = a.substr(a.length - 1);
      	}else{
      		var line = 1;
      	}
          var location ="PN_Pureto";
          var qty = 1;
          var status = 1;

          if(tag == '[ ]' || model == '[ ]' || pureto == '' || bensuki == ''){
            $("#loading").hide();
            alert('All field must be filled');  
          }else{
            $("#loading").show();
            var data = {
              tag:tag,
              model:model,
              pureto:pureto,
              bensuki:bensuki,
              started_at:$('#started_at').val(),
              line:line,
              location:location,
              qty:qty,
              status:status,
            }
            $.post('{{ url("index/SavePureto") }}', data, function(result, status, xhr){
              if(xhr.status == 200){
                if(result.status){
                  $('#opbentetx').css({'color':'red'});
                  $('#textmodel').css({'color':'red'});
                  $('#opben').text("");
                  $('#posisi').text("");
                  $('#modelb').text("");
                  $('#p_bensuki').text("[ ]");            
                  $('#p_rfid').text("[ ]");
                  $('#p_model').text("[ ]");
                  $('#rfid').val("");
                  $('#rfid').removeAttr('disabled');
                  $('#rfid').focus();
                  $('#started_at').val('');
                  openSuccessGritter('Success!', result.message);
                  gettotalng('biasa');
                  $("#loading").hide();
                }
                else{
                  $("#loading").hide();
                  openErrorGritter('Error!', result.message);
                }
              }
              else{
                $("#loading").hide();
                alert("Disconnected from server");
              }
            });
          }        
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


        function getpureto() {
          $("#loading").show();
         var pureto = $('#oppureto').val();
         var data ={
          pureto:pureto,
          op:'Pureto',
        }
        $.get('{{ url("index/op_Pureto") }}', data, function(result, status, xhr){
          if(xhr.status == 200){
            if(result.status){
              $('#p_pureto_nama').text(result.nama);
              $('#p_pureto_nik').text(result.nik);
              $('#employee_id').val(result.nik);
              $('#edit').modal('hide');
              $('#rfid').focus();
              $('#started_at').val('');
                // $('#tag_material').val(result.tag);
                openSuccessGritter('Success!', result.message);
                $("#loading").hide();
              }
              else{
                $("#loading").hide();
               $('#oppureto').val("");
                // $('#oppureto').removeAttr('disabled');
                $('#oppureto').focus();
                openErrorGritter('Error!', result.message);
              }
            }
            else{
              $("#loading").hide();
              alert("Disconnected from server");
            }
          });

      }

      function gettotalng(type) {
        $("#loading").show();
       var tag = $('#rfid').val();
       var a = "{{Auth::user()->name}}";
       if (a.match(/line/gi)) {
          		var line = a.substr(a.length - 1);
          	}else{
          		var line = 1;
          	}
       var data ={
        location:'PN_Pureto',
        line:line,
        employee_id:$('#employee_id').val(),
        type:type

      }
      $.get('{{ url("index/TotalNg") }}', data, function(result, status, xhr){
        if(xhr.status == 200){
          if(result.status){
            $('#total').text(result.total[0].total);
            openSuccessGritter('Success!', result.message);

            if (result.perolehan != null) {
              $('#bodyTablePerolehan').html('');
              var perolehan = '';

              var total = 0;
              for(var i = 0; i < result.perolehan.length;i++){
                perolehan += '<tr>';
                perolehan += '<td>'+result.perolehan[i].model+'</td>';
                perolehan += '<td>'+result.perolehan[i].qty+'</td>';
                perolehan += '<td>'+result.perolehan[i].screw+'</td>';
                perolehan += '</tr>';
                total = total + parseInt(result.perolehan[i].screw);
              }
              $('#bodyTablePerolehan').append(perolehan);
              $('#totalScrew').html(total);
            }
            $("#loading").hide();
          }
          else{                
            $("#loading").hide();
            openErrorGritter('Error!', result.message);
          }
        }
        else{
          $("#loading").hide();
          alert("Disconnected from server");
        }
      });

    }

	function getActualFullDate() {
		var today = new Date();

		var date = today.getFullYear()+'-'+addZero(today.getMonth()+1)+'-'+addZero(today.getDate());
		return date;
	}

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}


</script>
@endsection