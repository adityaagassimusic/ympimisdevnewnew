@extends('layouts.visitor')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("css/jqbtk.css")}}">
<style>
table.table-bordered{
	border:1px solid black;
	vertical-align: middle;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
	vertical-align: middle;
	padding: 2px 5px 2px 5px;
	font-size: 1.2vw;
}
table.table-bordered > tbody > tr > td{
	border:1px solid rgb(150,150,150);
	vertical-align: middle;
	padding: 2px 5px 2px 5px;
	font-size: 1.1vw;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(150,150,150);
	vertical-align: middle;
}
#loading, #error { display: none; }
</style>
@endsection


@section('header')
<section class="content-header">
	<h1>
		<center>
			<span style="color: white; font-weight: bold; font-size: 28px; text-align: center;">YMPI Phone List</span>
		</center>		
	</h1>
</section>
@endsection

@section('content') 
<section class="content" style="padding-top: 0;">
	<div class="box box-solid">
		<div class="box-body" id="phoneList">

		</div>
	</div>
	<input type="text" id="tag_visitor" class="form-control" style="background-color: #3c3c3c;border: none;padding-top: 0px">
</section>
@endsection


@section('scripts')

<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script >
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('#tag_visitor').focus();
		setInterval(focusTag,60000);
		$('.select2').select2({
			dropdownAutoWidth : true,
			width: '100%',
		});
		fetchList();
	});

	function fetchList(){
		$.get('{{ url("visitor_confirmation/phone_list") }}', function(result, status, xhr){
			if(result.status){
				$('#phoneList').html("");
				var phoneList = "";
				var count = 1;
				var count2 = 0;
				
				$.each(result.phone_list, function(key, value){
					count2 += 1;
					if(count == 1){
						phoneList += '<div class="col-xs-12 col-sm-6 col-md-4 col-lg-4" >';
						phoneList += '<table class="table table-bordered table-striped">';
						phoneList += '<thead style="background-color: rgba(126,86,134,.7);">';
						phoneList += '<tr>';
						phoneList += '<th style="width: 0.1%; text-align: right;">#</th>';
						phoneList += '<th style="width: 4%;">Nama</th>';
						phoneList += '<th style="width: 1%;">Dept.</th>';
						phoneList += '<th style="width: 0.1%; text-align: right;">Telp</th>';
						phoneList += '</tr>';
						phoneList += '</thead>';
						phoneList += '<tbody>';
					}
					phoneList += '<tr>';
					phoneList += '<td style="width: 0.1%; text-align: right;">'+count2+'</td>';
					phoneList += '<td style="width: 3%;">'+value.person+'</td>';
					phoneList += '<td style="width: 1%;">'+value.dept+'</td>';
					phoneList += '<td style="width: 0.1%; text-align: right; font-weight: bold;">'+value.nomor+'</td>';
					phoneList += '</tr>';
					count += 1;
					if(count2 % 25 === 0){
						count = 1;
						phoneList += '</tbody>';
						phoneList += '</table>';
						phoneList += '</div>';
					}
				});

				$('#phoneList').append(phoneList);
			}
			else{
				alert('Attempt to retrieve data failed.')
			}
		});
	}

	function focusTag() {
		$('#tag_visitor').focus();
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

	$('#tag_visitor').keydown(function(event) {
		if (event.keyCode == 13 || event.keyCode == 9) {
			if($("#tag_visitor").val().length >= 8){
				var data = {
					tag_visitor : $("#tag_visitor").val()
				}

				$.get('{{ url("scan/visitor/lobby") }}', data, function(result, status, xhr){
					if(result.status){
						$('#tag_visitor').val('');
						$('#modal_tamu').modal('show');
						if (result.location == 'Security' && result.destination == 'Office') {
							$('#tamu').html('<center><b>Tunggu Sebentar, '+result.visitor.name+' Akan menemui Anda.</b></center>');
						}else if(result.location == 'Lobby' && result.destination == 'Office'){
							$('#tamu').html('<center><b>Tunggu Sebentar, '+result.visitor.name+' Akan menemui Anda.</b></center>');
						}else if(result.location == 'Security' && result.destination != 'Office'){
							$('#tamu').html('<center><b>Mohon Maaf, tujuan Anda adalah '+result.destination+'. Silahkan menghubungi '+result.visitor.name+' untuk informasi lebih lanjut.</b></center>');
						}
						openSuccessGritter('Success!', result.message);
					}
					else{
						openErrorGritter('Error', result.message);
						$('#tag_visitor').val('');
						$('#tag_visitor').focus();
					}
				});
			}
			else{
				$('#modal_tamu').modal('show');
				$('#tamu').html('<center><b>Tag Anda Tidak Ditemukan</b></center>');
				openErrorGritter('Error!', 'Tag Invalid.');
				$("#tag_visitor").val("");
				$('#tag_visitor').focus();
			}
		}
	});
</script>
@endsection