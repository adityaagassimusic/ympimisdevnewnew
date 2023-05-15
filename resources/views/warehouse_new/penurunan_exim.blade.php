@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">

<style type="text/css">
	#stuffingTable tbody > tr {
		cursor: pointer;
	}
	.toolbar {
		float: left;
	}

	table.table-bordered{
		border:1px solid rgb(150,150,150);
	}
	table.table-bordered > thead > tr > th{
		border:1px solid rgb(150,150,150);
		text-align: center;
		vertical-align: middle;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(150,150,150);
		vertical-align: middle;
		text-align: center;
		padding:2px;
	}
	table.table-bordered > tfoot > tr > th{
		border:1px solid rgb(211,211,211);
		padding:0;
		vertical-align: middle;
		text-align: center;
	}
	.content{
		color: black;
		font-weight: bold;
	}
	.progress {
		background-color: rgba(0,0,0,0);
	}
	.aa {
		text-decoration-color: pink !important;
	}
	#div_pallet{
		overflow:auto;
		/*width: 500px;*/
		height: 400px;
	}
	.btn_danger{
		border: 1px solid #cecece;
		border-radius: 3px;
		padding: 3px 10px
		color: white;
		background-color: rgb(138, 173, 212);
	}
	.btn_danger:hover{
		border: 1px solid #8aadd4;
	}

	h4::selection {
		background: red;
		color: #FFFFFF;
	}
</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<!-- <div class="col-md-2 pull-right" style="padding-left: 30px;">
		<button class="btn btn-success pull-right" style=" width: 100%;margin-right: 14px;" onclick="modalCreate();">Create Vendor</button>
	</div> -->
	<div class="col-xs-5" style="padding-top: 5px">

		<div class="box box-solid">
			<div class="box-body" style="background-color: #605ca8 ">
				<div class="col-xs-12" style="background-color: #2f5782;">
					<h1 style="text-align: center; margin:5px; font-weight: bold; color: white">CHOOSE PALLET</h1>
				</div>
				<div class="col-md-12" id="div_pallet" style="padding-top: 30px;">
				</div>
			</div>
		</div>
	</div>

	<div hidden>  
		<span style="font-weight: bold; font-size: 16px;">Tanggal:</span>
		<div class="input-group date">
			<div class="input-group-addon bg-default">
				<i class="fa fa-calendar"></i>
			</div>
			<input type="text" class="form-control" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" readonly>
		</div>
	</div>
	<div class="col-xs-7">
	 <div class="row" style="padding-top: 10px;" >
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-body" style="background-color: #e8daef">
          <div class="col-xs-12" style="background-color: rgb(126,86,134); margin-bottom: 20px;">
            <h2 style="text-align: center; margin:5px; font-weight: bold; color: white">DROP MATERIAL</h2>
          </div>
          <table id="stuffingTable" class="table table-hover table-striped table-bordered">
            <thead style="background-color: rgb(65, 114, 166); color: white;">
              <tr>
               <th width="10%">#</th>
               <th width="10%">No Pallet</th>
               <th width="10%">Vendor</th>
               <th width="10%">Status</th>
             </tr>
           </thead>
           <tbody id="stuffingTableBody">
           </tbody>
         </table>
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
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jsQR.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	jQuery(document).ready(function(){
		// fillTable();
		setInterval(joblist, 10000);
		joblist();
		fetchDropJob();
		setInterval(fetchDropJob, 10000);

		$('#date').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true
		});
	});

	function modalCreate(){
		$('#modalCreate').modal('show');
	}


	function joblist(){

		var tanggal = $('#tanggal').val();
		var data = {
			tanggal : tanggal
		}

		$.get('{{ url("fetch/list/drop/exim") }}',data,function(result, status, xhr){
			if(result.status){	
				$('#div_pallet').html("");
				var btn_pallet = "";
				var status_ven = "";
				button = 0;

				for (var i = 0; i < result.exim.length; i++) {
					if (result.exim[i].no_invoice == null) {
						status_ven = result.exim[i].no_surat_jalan 
					}else{
						status_ven = result.exim[i].no_invoice 
					}
					btn_pallet += '<button  id="but_'+button+'"onclick="save('+i+')" class="btn_danger" background-color:#8aadd4; style="font-size: 1.7vw; width: 100%; font-weight: bold; ">'+status_ven+' || No Pallet : <span style="text-color: #00000; " id="test'+i+'">'+(result.exim[i].no_case || "" || "")+'</span>';
					btn_pallet += '</button> <br><br>';
					
					button += 1;
				}
				$('#div_pallet').append(btn_pallet);
			}else{
				openErrorGritter('Error!', result.message);
			}
		});
	}



	function coba(no){
		$('#myModal').modal('show');
	}


	function save(no) {
		var number_pallet = $('#test'+no).text();
		data = {
			number_pallet : number_pallet
		}

		if (confirm('Apakah No Pallet Sesuai?')) {
			$.post('{{ url("post/drop/exim") }}', data, function(result, status, xhr){
				if(result.status){	
					openSuccessGritter('Success', result.message);	
					audio_ok.play();

				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
				}
			})

		}

	}

	function sumbitvendor(no) {
		var country = $('#from_country').val();
		var vendor = $('#nama_vendor').val();

		data = {
			country : country,
			vendor : vendor
		}

		if (confirm('Apakah sudah Sesuai?')) {
			$.post('{{ url("post/vendor") }}', data, function(result, status, xhr){
				if(result.status){	
					openSuccessGritter('Success', result.message);	
					audio_ok.play();
					$('#modalCreate').modal('hide');
				}
				else{
					audio_error.play();
					openErrorGritter('Error!', result.message);
					$('#modalCreate').modal('hide');

				}
			})

		}

	}

	// function fetchDropJob(){


	// 	$.get('{{ url("fetch/drop/exim") }}', function(result, status, xhr){
	// 		if(result.status){
	// 			$('#tabelDataHistory').DataTable().clear();
	// 			$('#tabelDataHistory').DataTable().destroy();
	// 			$('#stuffingTableBody').html("");
	// 			var tableData = "";
	// 			for (var i = 0; i < result.drop.length; i++) {

	// 				tableData += '<tr>';
	// 				tableData += '<td>'+ result.drop[i].no_case +'</td>';
	// 				tableData += '<td>'+ result.drop[i].country +'</td>';
	// 				tableData += '<td>'+ result.drop[i].status_exim +'</td>';
	// 				tableData += '</tr>';
	// 			}


	// 			$('#stuffingTableBody').append(tableData);

	// 		}
	// 		else{
	// 			openErrorGritter('Error!', result.message);
	// 		}
	// 	});
	// }

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


	function fetchDropJob(){
		$.get('{{ url("fetch/drop/exim") }}', function(result, status, xhr){

			if(result.status){
				var stuffingTableBody = "";
				$('#stuffingTableBody').html("");

				$.each(result.drop, function(index, value){
					stuffingTableBody += "<tr>";
					stuffingTableBody += "<td>"+parseInt(index+1)+"</td>";
					stuffingTableBody += "<td>"+value.no_case+"</td>";
					stuffingTableBody += "<td>"+value.vendor+"</td>";
					stuffingTableBody += "<td>"+value.status_exim+"</td>";
					stuffingTableBody += "</tr>";

				});

				$('#stuffingTableBody').append(stuffingTableBody);
			}
			else{
				alert('Attempt to retrieve data failed.');
			}
		});
	}


	function selectClass(elem){
		var isi = elem.value;
		list = "";
		list += "<option></option> ";
		if (isi == "Jepang") {
			list += "<option value='YMMJ'>YMMJ</option>";
			list += "<option value='YCJ'>YCJ</option>";
			list += "<option value='SHOEI SHOKAI'>SHOEI SHOKAI</option>";          
			list += "<option value='TOYOTA TSUSHO'>TOYOTA TSUSHO</option>";
			list += "<option value='FUJINO'>FUJINO</option>";
			list += "<option value='YAMAGUCHI'>YAMAGUCHI</option>";
			list += "<option value='TANAKA'>TANAKA</option>";
			list += "<option value='SANKYO'>SANKYO</option>";
			list += "<option value='AJINOMOTO'>AJINOMOTO</option>";
			list += "<option value='TOSHIBA MACHINE'>TOSHIBA MACHINE</option>";
			list += "<option value='YOSHIDAWA'>YOSHIDAWA</option>";
			list += "<option value='NIKKO'>YAMAGUCHI</option>";
			list += "<option value='TYOKO YUKAGAKU'>TYOKO YUKAGAKU</option>";

		}
		else if (isi == "China"){
			list += "<option value='NINGBO JINTIAN'>NINGBO JINTIAN</option>";
			list += "<option value='PAN TAIWAN'>PAN TAIWAN</option>";
			list += "<option value='TONGXIANG'>TONGXIANG</option>";  
			list += "<option value='XIAOSHAN'>XIAOSHAN</option>";

		}
		else if (isi == "Singapore"){
			list += "<option value='STATCLEAN'>STATCLEAN</option>"
			list += "<option value='EPE PACKAGING'>EPE PACKAGING</option>"
			list += "<option value='EXIM & MFR'>EXIM & MFR</option>"

		}
		else if (isi == "America"){
			list += "<option value='SCOTT INTERNATIONAL'>SCOTT INTERNATIONAL</option>"
			list += "<option value='D ADDARIO'>D ADDARIO</option>"
			list += "<option value='JL.SMITH'>JL.SMITH</option>"  
		}
		else if (isi == "Italy"){
			list += "<option value='MUSIC CENTER'>MUSIC CENTER</option>"
		}
		else if (isi == "Thailand"){
			list += "<option value='NAFUKO'>NAFUKO</option>"
		} 

		else if (isi == "India"){
			list += "<option value='TALWAR'>TALWAR</option>"
		}

		$('#nama_vendor').html(list);

	}

	$("#modalCreate").on('hide.bs.modal', function(){
		$('#from_country').val("");
		$('#nama_vendor').val("");
	});

	function diff_minutes(dt2, dt1) 
	{
		var diff =(dt2.getTime() - dt1.getTime()) / 1000;
		diff /= 60;
		return Math.abs(Math.round(diff));
	}
</script>
@endsection