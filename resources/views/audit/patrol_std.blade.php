@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style type="text/css">

	#loading, #error { display: none; }

	table.table-bordered > thead > tr > th{
		color: white;
		background-color: black;
	}
	table.table-bordered > tbody > tr > td{
		color: black;
		background-color: white;
	}

	#loading { display: none; }


	.radio {
		display: inline-block;
		position: relative;
		padding-left: 35px;
		margin-bottom: 12px;
		cursor: pointer;
		font-size: 16px;
		-webkit-user-select: none;
		-moz-user-select: none;
		-ms-user-select: none;
		user-select: none;
	}

	/* Hide the browser's default radio button */
	.radio input {
		position: absolute;
		opacity: 0;
		cursor: pointer;
	}

	/* Create a custom radio button */
	.checkmark {
		position: absolute;
		top: 0;
		left: 0;
		height: 25px;
		width: 25px;
		background-color: #ccc;
		border-radius: 50%;
	}

	/* On mouse-over, add a grey background color */
	.radio:hover input ~ .checkmark {
		background-color: #ccc;
	}

	/* When the radio button is checked, add a blue background */
	.radio input:checked ~ .checkmark {
		background-color: #2196F3;
	}

	/* Create the indicator (the dot/circle - hidden when not checked) */
	.checkmark:after {
		content: "";
		position: absolute;
		display: none;
	}

	/* Show the indicator (dot/circle) when checked */
	.radio input:checked ~ .checkmark:after {
		display: block;
	}

	/* Style the indicator (dot/circle) */
	.radio .checkmark:after {
		top: 9px;
		left: 9px;
		width: 8px;
		height: 8px;
		border-radius: 50%;
		background: white;
	}

	#tableResult > thead > tr > th {
		border: 1px solid black;
	}

	#tableResult > tbody > tr > td {
		border: 1px solid #b0bec5;
	}

</style>
@stop
@section('header')
<section class="content-header">
	<input type="hidden" id="green">
	<h1>
		EHS & 5S Monthly Patrol
	</h1>
	<ol class="breadcrumb">
		<?php $user = STRTOUPPER(Auth::user()->username) ?>

		<button class="btn btn-success btn-sm" style="margin-right: 5px" onclick="location.reload()">
			<i class="fa fa-refresh"></i>&nbsp;&nbsp;Reload Page
		</button>

	</ol>
</section>
@stop
@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	@if ($errors->has('password'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Alert!</h4>
		{{ $errors->first() }}
	</div>   
	@endif
	@if (session('error'))
	<div class="alert alert-danger alert-dismissible">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<h4><i class="icon fa fa-ban"></i> Error!</h4>
		{{ session('error') }}
	</div>	
	@endif

	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Loading, Please Wait . . . <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>

	<div class="row">
		<div class="col-xs-12">
			<div class="col-xs-12" style="padding-right: 0; padding-left: 0;">
				<table class="table table-bordered" style="width: 100%; margin-bottom: 0px">
					<thead>
						<tr>
							<th style="width:15%; background-color: white; color: black; text-align: center; padding:0;font-size: 18px;border: 1px solid black" colspan="3">General Information</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="padding: 0px; background-color: #3f51b5; text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Patrol Date</td>
							<td colspan="2" style="padding: 0px; background-color: #01579b; text-align: center; color: white; font-size: 20px;border: 1px solid black"><?= date("d F Y") ?></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: #3f51b5; text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Auditor</td>
							<td colspan="2" style="padding: 0px; background-color: #01579b; text-align: center; color: white; font-size: 20px;border: 1px solid black" id="employee_name"></td>
						</tr>
						<tr>
							<td style="padding: 0px; background-color: #3f51b5; text-align: center; color: white; font-size:20px; width: 30%;border: 1px solid black">Category</td>
							<td colspan="2" style="padding: 0px; background-color: #01579b; text-align: center; color: white; font-size: 20px;border: 1px solid black" id="category"></td>
						</tr>
					</tbody>
				</table>
			</div>

			<input type="hidden" id="employee_id">

			<div class="col-xs-12" style="overflow-x: scroll;padding: 0">
				<h2>Chemical Storage</h2>
				<table class="table table-bordered" style="width: 100%; color: white;" id="tableResult">
					<thead style="font-weight: bold; background-color: #f20000">
						<tr>
							<th style="border-right: 1px solid white;background-color:#f20000;width: 15%;">Location</th>
							<th style="border-right: 1px solid white;background-color:#f20000;width: 20%;">Patrol Detail / Topic</th>
							<th style="border-right: 1px solid white;background-color:#f20000;width: 20%;">Photo</th>
							<th style="border-right: 1px solid white;background-color:#f20000;width: 25%;">Problem</th>
							<th style="border-right: 1px solid white;background-color:#f20000;width: 20%;">PIC</th>
							<!-- <th style="color: white;border-right: 1px solid white;width: 1%">Act</th> -->
						</tr>
					</thead>
					<tbody id="body_cek">
						<tr>
							<td width="10%">
								<select class="form-control select3" id="chemical_location_1" data-placeholder="Location" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($location as $loc)
									<option value="{{ $loc }}">{{ $loc }}</option>
									@endforeach
								</select>
							</td>
							<td width="10%">
								<span style="color:black;font-weight: bold;">Kesesuaian List dengan Aktual<br>(Chemical Storage)</span>
								<input type="hidden" class="form-control" id="chemical_patrol_detail_1" name="chemical_patrol_detail_1" placeholder="Patrol Detail" value="Kesesuaian List dengan Aktual" required="">
							</td>
							<td width="10%">
								<center>
									<input type="file" onchange="readURLChemical(this,'');" id="chemical_file_1" style="display:none">
									<button class="btn btn-primary btn-lg" id="btnImage1" value="Photo" onclick="buttonImage(this)">Photo</button>
									<img width="150px" id="blah1" src="" style="display: none" alt="your image" />
								</center>
							</td>
							<td width="10%">
								<textarea id="chemical_patrol_note_1" style="height: 40px;" class="form-control"></textarea>
							</td>
							<td width="10%">
								<select class="form-control select2" id="chemical_patrol_pic_1" data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($auditee as $audite)
									<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>
									@endforeach
								</select>
							</td>
						</tr>

						<tr>
							<td width="10%">
								<select class="form-control select3" id="chemical_location_2" data-placeholder="Location" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($location as $loc)
									<option value="{{ $loc }}">{{ $loc }}</option>
									@endforeach
								</select>
							</td>
							<td width="10%">
								<span style="color:black;font-weight: bold;">Kesesuaian Stok (Jumlah Min - Max)<br>(Chemical Storage)</span>
								<input type="hidden" class="form-control" id="chemical_patrol_detail_2" name="chemical_patrol_detail_2" placeholder="Patrol Detail" value="Kesesuaian Stok (Jumlah Min - Max)" required="">
							</td>
							<td width="10%">
								<center>
									<input type="file" onchange="readURLChemical(this,'');" id="chemical_file_2" style="display:none">
									<button class="btn btn-primary btn-lg" id="btnImage2" value="Photo" onclick="buttonImage(this)">Photo</button>
									<img width="150px" id="blah2" src="" style="display: none" alt="your image" />
								</center>
							</td>
							<td width="10%">
								<textarea id="chemical_patrol_note_2" style="height: 40px;" class="form-control"></textarea>
							</td>
							<td width="10%">
								<select class="form-control select2" id="chemical_patrol_pic_2" data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($auditee as $audite)
									<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>
									@endforeach
								</select>
							</td>
						</tr>

						<tr>
							<td width="10%">
								<select class="form-control select3" id="chemical_location_3" data-placeholder="Location" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($location as $loc)
									<option value="{{ $loc }}">{{ $loc }}</option>
									@endforeach
								</select>
							</td>
							<td width="10%">
								<span style="color:black;font-weight: bold;">Kesesuaian Inventory Card<br>(Chemical Storage)</span>
								<input type="hidden" class="form-control" id="chemical_patrol_detail_3" name="chemical_patrol_detail_3" placeholder="Patrol Detail" value="Kesesuaian Inventory Card" required="">
							</td>
							<td width="10%">
								<center>
									<input type="file" onchange="readURLChemical(this,'');" id="chemical_file_3" style="display:none">
									<button class="btn btn-primary btn-lg" id="btnImage3" value="Photo" onclick="buttonImage(this)">Photo</button>
									<img width="150px" id="blah3" src="" style="display: none" alt="your image" />
								</center>
							</td>
							<td width="10%">
								<textarea id="chemical_patrol_note_3" style="height: 40px;" class="form-control"></textarea>
							</td>
							<td width="10%">
								<select class="form-control select2" id="chemical_patrol_pic_3" data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($auditee as $audite)
									<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>
									@endforeach
								</select>
							</td>
						</tr>

						<tr>
							<td width="10%">
								<select class="form-control select3" id="chemical_location_4" data-placeholder="Location" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($location as $loc)
									<option value="{{ $loc }}">{{ $loc }}</option>
									@endforeach
								</select>
							</td>
							<td width="10%">
								<span style="color:black;font-weight: bold;">Kebersihan Area Penyimpanan<br>(Chemical Storage)</span>
								<input type="hidden" class="form-control" id="chemical_patrol_detail_4" name="chemical_patrol_detail_4" placeholder="Patrol Detail" value="Kebersihan Area Penyimpanan" required="">
							</td>
							<td width="10%">
								<center>
									<input type="file" onchange="readURLChemical(this,'');" id="chemical_file_4" style="display:none">
									<button class="btn btn-primary btn-lg" id="btnImage4" value="Photo" onclick="buttonImage(this)">Photo</button>
									<img width="150px" id="blah4" src="" style="display: none" alt="your image" />
								</center>
							</td>
							<td width="10%">
								<textarea id="chemical_patrol_note_4" style="height: 40px;" class="form-control"></textarea>
							</td>
							<td width="10%">
								<select class="form-control select2" id="chemical_patrol_pic_4" data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($auditee as $audite)
									<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>
									@endforeach
								</select>
							</td>
						</tr>

						<tr>
							<td width="10%">
								<select class="form-control select3" id="chemical_location_5" data-placeholder="Location" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($location as $loc)
									<option value="{{ $loc }}">{{ $loc }}</option>
									@endforeach
								</select>
							</td>
							<td width="10%">
								<span style="color:black;font-weight: bold;">Pengecekan Water Gate/Pintu Air<br>(Area Outdoor)</span>
								<input type="hidden" class="form-control" id="chemical_patrol_detail_5" name="chemical_patrol_detail_5" placeholder="Patrol Detail" value="Pengecekan Water Gate" required="">
							</td>
							<td width="10%">
								<center>
									<input type="file" onchange="readURLChemical(this,'');" id="chemical_file_5" style="display:none">
									<button class="btn btn-primary btn-lg" id="btnImage5" value="Photo" onclick="buttonImage(this)">Photo</button>
									<img width="150px" id="blah5" src="" style="display: none" alt="your image" />
								</center>
							</td>
							<td width="10%">
								<textarea id="chemical_patrol_note_5" style="height: 40px;" class="form-control"></textarea>
							</td>
							<td width="10%">
								<select class="form-control select2" id="chemical_patrol_pic_5" data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($auditee as $audite)
									<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>
									@endforeach
								</select>
							</td>
						</tr>

						<tr>
							<td width="10%">
								<select class="form-control select3" id="chemical_location_6" data-placeholder="Location" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($location as $loc)
									<option value="{{ $loc }}">{{ $loc }}</option>
									@endforeach
								</select>
							</td>
							<td width="10%">
								<span style="color:black;font-weight: bold;">Ketersediaan Chemical Spill</span>
								<input type="hidden" class="form-control" id="chemical_patrol_detail_6" name="chemical_patrol_detail_6" placeholder="Patrol Detail" value="Ketersediaan Chemical Spill" required="">
							</td>
							<td width="10%">
								<center>
									<input type="file" onchange="readURLChemical(this,'');" id="chemical_file_6" style="display:none">
									<button class="btn btn-primary btn-lg" id="btnImage6" value="Photo" onclick="buttonImage(this)">Photo</button>
									<img width="150px" id="blah6" src="" style="display: none" alt="your image" />
								</center>
							</td>
							<td width="10%">
								<textarea id="chemical_patrol_note_6" style="height: 40px;" class="form-control"></textarea>
							</td>
							<td width="10%">
								<select class="form-control select2" id="chemical_patrol_pic_6" data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($auditee as $audite)
									<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>
									@endforeach
								</select>
							</td>
						</tr>

					</tbody>
				</table>
				

				<table class="table table-bordered" style="width: 100%; color: white;" id="tableResult">
					<thead style="font-weight: bold; background-color: rgb(220,220,220);">
						<tr>
							<th style="border-right: 1px solid white">Location</th>
							<th style="border-right: 1px solid white">Patrol Detail / Topic</th>
							<th style="border-right: 1px solid white">Photo</th>
							<th style="border-right: 1px solid white">Problem</th>
							<th style="border-right: 1px solid white">PIC</th>
							<!-- <th style="color: white;border-right: 1px solid white;width: 1%">Act</th> -->
						</tr>
					</thead>
					<tbody id="body_cek">
						<tr class="member">
							<td width="10%">
								<select class="form-control select3" id="location" data-placeholder="Location" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($location as $loc)
									<option value="{{ $loc }}">{{ $loc }}</option>
									@endforeach
								</select>
							</td>
							<td width="10%">
								<!-- <input type="text" class="form-control patrol_detail" id="patrol_detail" name="patrol_detail" placeholder="Patrol Detail" required=""> -->
								<select class="form-control select3 patrol_detail" id="patrol_detail" data-placeholder="Patrol Topic" style="width: 100%; font-size: 20px;">
									<option></option>
									<option value="S-Up and 5S">S-Up and 5S</option>
									<option value="Environment">Environment</option>
									<option value="Health">Health</option>
									<option value="Safety">Safety</option>
								</select>
							</td>
							<td width="10%">
								<center>
									<input type="file" onchange="readURL(this,'');" id="file" style="display:none" class="file">
									<button class="btn btn-primary btn-lg" id="btnImage" value="Photo" onclick="buttonImage(this)">Photo</button>
									<img width="150px" id="blah" src="" style="display: none" alt="your image" />
									<br><span class="text-red">*Disarankan Upload Foto Dalam Bentuk Landscape</span>
								</center>
							</td>
							<td width="10%">
								<textarea id="patrol_note" height="100%" class="form-control note"></textarea>
							</td>
							<td width="10%">
								<select class="form-control select2 patrol_pic" id="patrol_pic" data-placeholder="Pilih PIC" style="width: 100%; font-size: 20px;">
									<option></option>
									@foreach($auditee as $audite)
									<option value="{{ $audite->name }}">{{ $audite->employee_id }} - {{ $audite->name }}</option>
									@endforeach
								</select>
								<!-- <input type="hidden" id="patrol_pic_name"> -->
							</td>
							<!-- <td><button class="btn btn-danger" onclick="delete_confirmation(this)"><i class="fa fa-close"></i></button></td> -->
						</tr>
					</tbody>
				</table>
				<br>
				<button class="btn btn-success" style="width: 100%;font-size: 25px" onclick="cek()"><i class="fa fa-check"></i> Submit</button>
			</div>
		</div>
	</div>
</section>
	
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Input Confirmation</h4>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<a  class="btn btn-danger" href="{{ url('') }}">Tutup</button>
					<!-- <a href="{{ url("index/audit_iso/cek_report/")}}" class="btn btn-success btn-sm" target="_blank" style="color:white;margin-right: 5px"><i class="fa fa-file-pdf-o"></i> Cek Laporan Hasil {{ $page }} </a> -->
					<a id="modalDeleteButton" href="#" type="button" class="btn btn-success">Lihat Report Hasil Audit</a>
				</div>
			</div>
		</div>
	</div>
	@endsection
	@section('scripts')
	<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
	<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
	<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
	<script src="{{ url("js/buttons.flash.min.js")}}"></script>
	<script src="{{ url("js/jszip.min.js")}}"></script>
	<script src="{{ url("js/vfs_fonts.js")}}"></script>
	<script src="{{ url("js/buttons.html5.min.js")}}"></script>
	<script src="{{ url("js/buttons.print.min.js")}}"></script>
	<script src="{{ url("js/jquery.gritter.min.js") }}"></script>

	<script>

		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});


		var counter = 2;
		var add_point = [];

		jQuery(document).ready(function() {
			$('body').toggleClass("sidebar-collapse");
			$('.select2').prop('selectedIndex', 0).change();
			$('.select2').select2({
				dropdownAutoWidth : true,
				allowClear:true
			});

			$('.select3').select2({
				minimumResultsForSearch : -1,
				dropdownAutoWidth : true,
				allowClear:true
			});
		})


		$('#category').html("EHS & 5S Patrol");
		$('#employee_name').html("{{$employee->name}}");
		$('#employee_id').val("{{$employee->employee_id}}");

		function get_check() {

			var category = $('#category').text();

			var data = {
				category:category
			}

			$("#loading").show();

			$.get('{{ url("fetch/audit_patrol") }}', data, function(result, status, xhr){
				$("#loading").hide();
				openSuccessGritter("Success","Data Has Been Load");
				var body = "";

				$('#tableResult').DataTable().clear();
				$('#tableResult').DataTable().destroy();
			// $('#body_cek').html("");

			count = 1;

			$.each(result.lists, function(index, value){
				body += "<tr>";
				body += "<td width='1%'>"+count+"</td>";
				body += "<td width='5%' id='klausul_"+count+"'>"+value.klausul+"<input type='hidden' id='id_point_"+count+"' value='"+value.id+"'><input type='hidden' id='jumlah_point_"+count+"' value='"+result.lists.length+"'></td>";
				body += "<td width='15%' id='point_judul_"+count+"'>"+value.point_judul+"</td>";
				body += "<td width='20%' id='point_question_"+count+"'>"+value.point_question+"</td>";
				body += "<td width='20%'><label class='radio' style='margin-top: 5px;margin-left: 5px'>Good<input onclick='goodchoice(this.id)' type='radio' id='status_"+count+"' name='status_"+count+"' value='Good'><span class='checkmark'></span></label><label class='radio' style='margin-top: 5px;margin-left: 5px'>Not Good<input type='radio' id='status_"+count+"' name='status_"+count+"' value='Not Good' onclick='notgoodchoice(this.id)'><span class='checkmark'></span></label></td>";
				body += "<td width='20%'><textarea id='note_"+count+"' height='50%' style='display:none'></textarea></td>";
				var idid = '#file_'+count;
				body += '<td width="15%"><input type="file" style="display:none" onchange="readURL(this,\''+count+'\');" id="file_'+count+'"><button class="btn btn-primary btn-lg" id="btnImage_'+count+'" value="Photo" style="display:none" onclick="buttonImage(this)">Photo</button><img width="150px" id="blah_'+count+'" src="" style="display: none" alt="your image" /></td>';
				body += "</tr>";
				count++;
			})

			$("#body_cek").append(body);

			var table = $('#tableResult').DataTable( {
				responsive: true,
				paging: false,
				searching: false,
				bInfo : false
			} );
		})
		}

		function buttonImage(elem) {
			$(elem).closest("td").find("input").click();
			// console.log(input);
		}

		const compressImage = async (file, {
            quality = 1,
            type = file.type
        }) => {

            const imageBitmap = await createImageBitmap(file);

            const canvas = document.createElement('canvas');
            canvas.width = imageBitmap.width;
            canvas.height = imageBitmap.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(imageBitmap, 0, 0);

            const blob = await new Promise((resolve) =>
                canvas.toBlob(resolve, type, quality)
            );

            return new File([blob], file.name, {
                type: blob.type,
            });
        };

        const input = document.querySelector('.file');
        input.addEventListener('change', async (e) => {            
            const {
                files
            } = e.target;
            
            if (!files.length) return;
            
            const dataTransfer = new DataTransfer();
            
            for (const file of files) {

                if (!file.type.startsWith('image')) {                    
                    dataTransfer.items.add(file);
                    continue;
                }
                
                const compressedFile = await compressImage(file, {
                    quality: 0.3,
                    type: 'image/jpeg',
                });
                
                dataTransfer.items.add(compressedFile);
                
                readURL(compressedFile);
            }

            // Set value of the file input to our new files list
            e.target.files = dataTransfer.files;
        });

        function readURL(compressedFile) {
            var reader = new FileReader();
            var img = $(input).closest("td").find("img");
            reader.onload = function(e) {
                $(img).show();
                $(img).attr('src', e.target.result);
            };
                reader.readAsDataURL(input.files[0]);
            $(input).closest("td").find("button").hide();
        }

		function readURLChemical(input,idfile) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();

				reader.onload = function (e) {
					var img = $(input).closest("td").find("img");
					$(img).show();
					$(img)
					.attr('src', e.target.result);
				};

				reader.readAsDataURL(input.files[0]);
			}

			$(input).closest("td").find("button").hide();
			// $('#btnImage'+idfile).hide();
		}

		function cek() {
			// if (confirm('Apakah Anda yakin?')) {
				$('#loading').show();

				var audit_data = [];
				var patrol_detail = [];
				var note = [];
				var patrol_pic = [];
				var file = [];

				var len = $('.member').length;

				var formData = new FormData();

				formData.append('jumlah', len);				
				formData.append('category', $('#category').text());
				formData.append('location', $('#location').val());
				formData.append('auditor_id', $('#employee_id').val());
				formData.append('auditor_name',  $('#employee_name').text());

				$('.file').each(function(i, obj) {
					formData.append('file_datas_'+i, $(this).prop('files')[0]);
					var file=$(this).val().replace(/C:\\fakepath\\/i, '').split(".");

					formData.append('extension_'+i, file[1]);
					formData.append('foto_name_'+i, file[0]);
				})	

				$('.patrol_detail').each(function(i, obj) {
					formData.append('patrol_detail_'+i, $(this).val());
				})

				$('.note').each(function(i, obj) {
					formData.append('note_'+i, $(this).val());
				})

				$('.patrol_pic').each(function(i, obj) {
					formData.append('patrol_pic_'+i, $(this).val());
				})	

				formData.append('chemical','none');

				for (var i = 1; i <= 6; i++) {
					if ($('#chemical_file_'+i).val().replace(/C:\\fakepath\\/i, '').split(".")[0] == "") {
						// $("#loading").hide();
						// openErrorGritter('Error!', 'Catatan Harus Diisi');
						// return false;
					}else{
						formData.append('chemical','chemical');
						formData.append('chemical_file_'+i, $('#chemical_file_'+i).prop('files')[0]);
						// var file=$(this).val().replace(/C:\\fakepath\\/i, '').split(".");
						// formData.append('chemical_extension_'+i, file[1]);
						// formData.append('chemical_foto_name_'+i, file[0]);
						formData.append('chemical_location_'+i, $("#chemical_location_"+i).val());
						formData.append('chemical_patrol_detail_'+i, $("#chemical_patrol_detail_"+i).val());
						formData.append('chemical_patrol_note_'+i, $("#chemical_patrol_note_"+i).val());
						formData.append('chemical_patrol_pic_'+i, $("#chemical_patrol_pic_"+i).val());
					}
				}


				$.ajax({
					url:"{{ url('post/audit_patrol_file') }}",
					method:"POST",
					data:formData,
					dataType:'JSON',
					contentType: false,
					cache: false,
					processData: false,
					success: function (response) {
						$("#loading").hide();
						$("#patrol_detail").val("").trigger("change");
						$("#patrol_note").val("");
						$("#patrol_pic").val("").trigger("change");
						$("#blah").hide();
						$("#btnImage").show();
						clearChemical();
						openSuccessGritter("Success", "Audit Berhasil Disimpan");
						location.reload();
					},
					error: function (response) {
						console.log(response.message);
					},
				})
			}
		// }

	// function selectemployee(){
	//          var pic = document.getElementById("patrol_pic").value;

	//          $.ajax({
	//           url: "{{ url('index/audit_iso/get_nama') }}?auditee=" +pic, 
	//           type : 'GET', 
	//           success : function(data){
	//            var obj = jQuery.parseJSON(data);
	//            $('#patrol_pic_nama').val(obj[0].name);
	//          }
	//        });      
	//    }

	function clearChemical(){
		$("#blah1").hide();
		$("#btnImage1").show();
		$("#blah2").hide();
		$("#btnImage2").show();
		$("#blah3").hide();
		$("#btnImage3").show();
		$("#blah4").hide();
		$("#btnImage4").show();
		$("#blah5").hide();
		$("#btnImage5").show();
		$("#blah6").hide();
		$("#btnImage6").show();
		$("#chemical_location_1").val("").trigger("change");
		$("#chemical_location_2").val("").trigger("change");
		$("#chemical_location_3").val("").trigger("change");
		$("#chemical_location_4").val("").trigger("change");
		$("#chemical_location_5").val("").trigger("change");
		$("#chemical_location_6").val("").trigger("change");
		// $("#chemical_patrol_detail_1").val("").trigger("change");
		// $("#chemical_patrol_detail_2").val("").trigger("change");
		// $("#chemical_patrol_detail_3").val("").trigger("change");
		// $("#chemical_patrol_detail_4").val("").trigger("change");
		$("#chemical_file_1").val("");
		$("#chemical_file_2").val("");
		$("#chemical_file_3").val("");
		$("#chemical_file_4").val("");
		$("#chemical_file_5").val("");
		$("#chemical_file_6").val("");
		$("#chemical_patrol_note_1").val("");
		$("#chemical_patrol_note_2").val("");
		$("#chemical_patrol_note_3").val("");
		$("#chemical_patrol_note_4").val("");
		$("#chemical_patrol_note_5").val("");
		$("#chemical_patrol_note_6").val("");
		$("#chemical_patrol_pic_1").val("").trigger("change");
		$("#chemical_patrol_pic_2").val("").trigger("change");
		$("#chemical_patrol_pic_3").val("").trigger("change");
		$("#chemical_patrol_pic_4").val("").trigger("change");
		$("#chemical_patrol_pic_5").val("").trigger("change");
		$("#chemical_patrol_pic_6").val("").trigger("change");
	}

	function delete_confirmation(elem) {
		$(elem).closest('tr').remove();
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
@endsection