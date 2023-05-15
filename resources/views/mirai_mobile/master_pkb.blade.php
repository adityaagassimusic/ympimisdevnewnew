@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">
<link rel="stylesheet" href="{{ url("css/bootstrap-datetimepicker.min.css")}}">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>

<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	thead>tr>th{
		/*text-align:center;*/
		padding: 2px;
		overflow:hidden;
	}
	tbody>tr>td{
		/*text-align:center;*/
		padding-left: 5px;
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

	.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
		background-color: #FFD700;
	}

	#tablePeriode > tbody > tr > td{
		padding: 4px;
	}
	#tableQuestion > tbody > tr > td{
		padding: 4px;
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
	#loading, #error { display: none; }
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
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 50%;">
			<span style="font-size: 60px"><i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>					
	<div class="row">
		<div class="col-xs-12" style="padding-right: 5px">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="row">
							<div class="col-xs-10" style="padding-left: 0px;padding-right: 5px;margin-bottom: 10px">
								<center style="background-color: orange;"><span style="font-weight: bold;font-size: 20px">MASTER PERIODE</span></center>
							</div>
							<div class="col-xs-2" style="padding-right: 0px;padding-left: 5px;margin-bottom: 10px">
								<button class="btn btn-warning" onclick="$('#modalAddPeriode').modal('show')" style="width: 100%;padding: 3.5px"><i class="fa fa-plus"></i> Add Periode</button>
							</div>
							<br>
							<table id="tablePeriode" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="2%">Periode</th>
										<th width="2%">Status</th>
										<th width="1%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTablePeriode">
									<?php $index = 1; ?>
									<?php for ($i=0; $i < count($periode); $i++) { ?>
										<tr>
											<td style="text-align: right;">{{$index}}</td>
											<td>{{$periode[$i]->periode}}</td>
											<td>{{$periode[$i]->status}}</td>
											<td style="text-align: center;">
												<button class="btn btn-warning btn-sm" onclick="editPeriode('{{$periode[$i]->periode}}','{{$periode[$i]->status}}','{{$periode[$i]->id}}')"><i class="fa fa-edit"></i> Edit</button>
												<button class="btn btn-danger btn-sm" style="margin-left: 5px" onclick="deletePeriode('{{$periode[$i]->id}}')"><i class="fa fa-trash"></i> Delete</button>
											</td>
										</tr>
										<?php $index++ ?>
									<?php } ?>
								</tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12" style="padding-left: 5px">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12">
						<div class="row">
							<div class="col-xs-10" style="padding-left: 0px;padding-right: 5px;margin-bottom: 10px">
								<center style="background-color: #46cf6b;"><span style="font-weight: bold;font-size: 20px">MASTER QUESTION</span></center>
							</div>
							<div class="col-xs-2" style="padding-right: 0px;padding-left: 5px;margin-bottom: 10px">
								<button class="btn btn-success" style="width: 100%;padding: 3.5px" onclick="$('#modalAddQuestion').modal('show')"><i class="fa fa-plus"></i> Add Question</button>
							</div>
							<table id="tableQuestion" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
								<thead style="background-color: rgb(126,86,134); color: #FFD700;">
									<tr>
										<th width="1%">#</th>
										<th width="1%">Periode</th>
										<th width="3%">Pertanyaan</th>
										<th width="3%">Jawaban</th>
										<th width="2%">Jawaban Benar</th>
										<th width="2%">Pembahasan</th>
										<th width="1%">Action</th>
									</tr>
								</thead>
								<tbody id="bodyTableQuestion">
									<?php $index = 1; ?>
									<?php for ($i=0; $i < count($question); $i++) { ?>
										<tr>
											<td style="text-align: right;">{{$index}}</td>
											<td>{{$question[$i]->periode}}</td>
											<td>{{$question[$i]->question}}</td>
											<td>
												<?php $answer = explode('_', $question[$i]->answer); ?>
												<?php $index1= 1; ?>
												<?php for ($j=0; $j < count($answer); $j++) { 
													echo $index1.'. '.$answer[$j].'<br>';
													$index1++;
												} ?>
											</td>
											<td>{{$question[$i]->right_answer}}</td>
											<td><?php echo $question[$i]->discussion ?></td>
											<td style="text-align: center;">
												<button class="btn btn-warning btn-sm" onclick="editQuestion('{{$question[$i]->periode}}','{{$question[$i]->question}}','{{$question[$i]->right_answer}}','{{$question[$i]->id}}')"><i class="fa fa-edit"></i> Edit</button>
												<button class="btn btn-danger btn-sm" style="margin-left: 5px" onclick="deleteQuestion('{{$question[$i]->id}}')"><i class="fa fa-trash"></i> Delete</button>
											</td>
										</tr>
										<?php $index++ ?>
									<?php } ?>
								</tbody>
								<tfoot>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalEditQuestion">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div style="background-color: #03adfc;text-align: center;">
						<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalEditQuestionTitle">EDIT QUESTION</h4>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="row">
								<div class="form-group" id="selectPeriode">
									<label>Periode</label><br>
									<select class="form-control select2" style="width: 100%" data-placeholder="Pilih Periode" name="edit_periode" id="edit_periode">
										<option value=""></option>
										@foreach($periode2 as $periode)
										<option value="{{$periode->periode}}">{{$periode->periode}}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label>Pertanyaan</label><br>
									<input type="text" name="edit_question" id="edit_question" class="form-control" style="width: 100%">
								</div>
								<div class="form-group">
									<label>Jawaban</label><br>
									<textarea style="width: 100%" name="edit_answer" id="edit_answer"></textarea>
									<!-- <input type="text" name="edit_answer" id="edit_answer" class="form-control" style="width: 100%"> -->
								</div>
								<div class="form-group">
									<label>Jawaban Benar</label><br>
									<input type="text" name="edit_right_answer" id="edit_right_answer" class="form-control" style="width: 100%">
									<input type="hidden" name="edit_question_id" id="edit_question_id">
								</div>
								<div class="form-group">
									<label>Pembahasan</label><br>
									<textarea style="width: 100%" name="edit_discussion" id="edit_discussion"></textarea>
									<!-- <input type="text" name="edit_answer" id="edit_answer" class="form-control" style="width: 100%"> -->
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
						<button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 20px" onclick="updateQuestion()">Update</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalAddQuestion">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div style="background-color: #46cf6b;text-align: center;">
						<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalEditQuestionTitle">ADD QUESTION</h4>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="row">
								<div class="form-group">
									<label>Periode</label><br>
									<select class="form-control select5" style="width: 100%" data-placeholder="Pilih Periode" name="add_periode_periode" id="add_periode_periode">
										<option value=""></option>
										@foreach($periode2 as $periode)
										<option value="{{$periode->periode}}">{{$periode->periode}}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label>Pertanyaan</label><br>
									<input type="text" name="add_question" id="add_question" class="form-control" style="width: 100%" placeholder="Input Pertanyaan">
								</div>
								<div class="form-group">
									<label>Pilihan Jawaban</label><br>
									<textarea style="width: 100%" name="add_answer" id="add_answer"></textarea>
									<!-- <input type="text" name="edit_answer" id="edit_answer" class="form-control" style="width: 100%"> -->
								</div>
								<div class="form-group">
									<label>Jawaban Benar</label><br>
									<input type="text" name="add_right_answer" id="add_right_answer" class="form-control" style="width: 100%">
								</div>
								<div class="form-group">
									<label>Pembahasan</label><br>
									<textarea style="width: 100%" name="add_discussion" id="add_discussion"></textarea>
									<!-- <input type="text" name="edit_answer" id="edit_answer" class="form-control" style="width: 100%"> -->
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
						<button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 20px" onclick="addQuestion()">Add</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalEditPeriode">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div style="background-color: #03adfc;text-align: center;">
						<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalEditPeriodeTitle">EDIT PERIODE</h4>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="row">
								<div class="form-group">
									<label>Periode</label><br>
									<input type="text" name="edit_periode_periode" id="edit_periode_periode" class="form-control" style="width: 100%">
								</div>
								<div class="form-group" id="">
									<label>Status</label><br>
									<input type="hidden" name="edit_periode_id" id="edit_periode_id">
									<select class="form-control select3" style="width: 100%" data-placeholder="Pilih Status" name="edit_status" id="edit_status">
										<option value=""></option>
										<option value="Active">Active</option>
										<option value="Non-Active">Non-Active</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
						<button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 20px" onclick="updatePeriode()">Update</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalAddPeriode">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<div style="background-color: orange;text-align: center;">
						<h4 class="modal-title" style="font-weight: bold;padding: 10px;font-size: 20px" id="modalEditPeriodeTitle">ADD PERIODE</h4>
					</div>
					<div class="modal-body table-responsive no-padding" style="min-height: 120px;margin-top: 10px">
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="row">
								<div class="form-group">
									<label>Periode</label><br>
									<input type="text" name="add_periode" id="add_periode" class="form-control" style="width: 100%" placeholder="Input Periode">
								</div>
								<div class="form-group" id="">
									<label>Status</label><br>
									<select class="form-control select4" style="width: 100%" data-placeholder="Pilih Status" name="add_status" id="add_status">
										<option value=""></option>
										<option value="Active">Active</option>
										<option value="Non-Active">Non-Active</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer table-responsive no-padding" style="margin-top: 10px">
						<button class="btn btn-success" style="width: 100%;font-weight: bold;font-size: 20px" onclick="addPeriode()">Submit</button>
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
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var arr = [];
	var arr2 = [];

	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");

		CKEDITOR.replace('edit_answer' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });
	    CKEDITOR.replace('add_answer' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('add_discussion' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });

	    CKEDITOR.replace('edit_discussion' ,{
      		filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}'
	    });	    

		$('.datepicker').datepicker({
			<?php $tgl_max = date('Y-m-d') ?>
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
			endDate: '<?php echo $tgl_max ?>'
		});

		$('#tablePeriode').DataTable({
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

		$('#tableQuestion').DataTable({
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
	});


	$(function () {
		$('.select2').select2({
			allowClear:true,
			dropdownParent: $('#selectPeriode'),
		});
		$('.select3').select2({
			allowClear:true,
			dropdownParent: $('#modalEditPeriode'),
		});

		$('.select4').select2({
			allowClear:true,
			dropdownParent: $('#modalAddPeriode'),
		});
		$('.select5').select2({
			allowClear:true,
			dropdownParent: $('#modalAddQuestion'),
		});
	});

	function editQuestion(periode,question,right_answer,id) {
		$('#edit_periode').val(periode).trigger('change');
		$('#edit_question').val(question);
		// $('#edit_answer').(answer);
		$('#edit_right_answer').val(right_answer);
		$('#edit_question_id').val(id);
		$('#modalEditQuestion').modal('show');

		var data = {
			id:id
		}


		$.get('{{ url("fetch/question/pkb") }}',data, function(result, status, xhr){
			if(result.status){
				$("#edit_discussion").html(CKEDITOR.instances.edit_discussion.setData(result.question.discussion));
				var answers = result.question.answer.split('_');
				$("#edit_answer").html(CKEDITOR.instances.edit_answer.setData(answers.join('<br>')));
			}else{
				openErrorGritter('Error!',result.message);
				audio_error.play();
				return false;
			}
		})
	}

	function editPeriode(periode,status,id) {
		$('#edit_periode_periode').val(periode);
		$('#edit_status').val(status).trigger('change');
		$('#edit_periode_id').val(id);
		$('#modalEditPeriode').modal('show');
	}

	function updatePeriode() {
		$('#loading').show();
		var id = $('#edit_periode_id').val();
		var periode = $('#edit_periode_periode').val();
		var status = $('#edit_status').val();

		var formData = new FormData();
		formData.append('id', id);
		formData.append('periode', periode);
		formData.append('status', status);
		
		$.ajax({
			url:"{{ url('update/periode/pkb') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				$('#loading').hide();
				$('#modalEditPeriode').modal('hide');
				openSuccessGritter('Success!','Success Update Periode');
				location.reload();
			},
			error: function(data) {
				$('#loading').hide();
				openErrorGritter('Error!',data.message);
			}
		});
	}

	function deletePeriode(id) {
		if (confirm('Anda yakin akan menghapus periode?')) {
			$('#loading').show();

			var formData = new FormData();
			formData.append('id', id);
			
			$.ajax({
				url:"{{ url('delete/periode/pkb') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					$('#loading').hide();
					openSuccessGritter('Success!','Success Delete Periode');
					location.reload();
				},
				error: function(data) {
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
				}
			});
		}
	}

	function deleteQuestion(id) {
		if (confirm('Anda yakin akan menghapus pertanyaan?')) {
			$('#loading').show();

			var formData = new FormData();
			formData.append('id', id);
			
			$.ajax({
				url:"{{ url('delete/question/pkb') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					$('#loading').hide();
					openSuccessGritter('Success!','Success Delete Question');
					location.reload();
				},
				error: function(data) {
					$('#loading').hide();
					openErrorGritter('Error!',data.message);
				}
			});
		}
	}

	function addPeriode() {
		$('#loading').show();
		var periode = $('#add_periode').val();
		var status = $('#add_status').val();

		if (periode == '' || status == '') {
			audio_error.play();
			$('#loading').hide();
			openErrorGritter('Error!','Semua Data Harus Diisi');
			return false;
		}

		var formData = new FormData();
		formData.append('periode', periode);
		formData.append('status', status);
		
		$.ajax({
			url:"{{ url('add/periode/pkb') }}",
			method:"POST",
			data:formData,
			dataType:'JSON',
			contentType: false,
			cache: false,
			processData: false,
			success:function(data)
			{
				$('#loading').hide();
				$('#modalAddPeriode').modal('hide');
				openSuccessGritter('Success!','Success Add Periode');
				location.reload();
			},
			error: function(data) {
				$('#loading').hide();
				openErrorGritter('Error!',data.message);
			}
		});
	}



	function updateQuestion() {
		$('#loading').show();
		var id = $('#edit_question_id').val();
		var periode = $('#edit_periode').val();
		var question = $('#edit_question').val();
		var right_answer = $('#edit_right_answer').val();
		var answer = CKEDITOR.instances.edit_answer.getData();
		var discussion = CKEDITOR.instances.edit_discussion.getData();

		var answer = answer.replace("<p>",'');
		var answer = answer.replace("</p>\n",'');
		var re = new RegExp('<br />\n', 'g');
		// let res = str.replace(re, "red");
		var answer = answer.replace(re,'_');
		var re = new RegExp('&amp;', 'g');
		var answer = answer.replace(re,'&');

		var data = {
			id:id,
			periode:periode,
			question:question,
			right_answer:right_answer,
			answer:answer,
			discussion:discussion,
		}

		$.post('{{ url("update/question/pkb") }}',data, function(result, status, xhr){
			if(result.status){
				$('#modalEditQuestion').modal('hide');
				$('#loading').hide();
				openSuccessGritter('Success!','Success Update Question');
				location.reload();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
				return false;
			}
		})
	}

	function addQuestion() {
		$('#loading').show();
		var periode = $('#add_periode_periode').val();
		var question = $('#add_question').val();
		var right_answer = $('#add_right_answer').val();
		var answer = CKEDITOR.instances.add_answer.getData();
		var discussion = CKEDITOR.instances.add_discussion.getData();

		var answer = answer.replace("<p>",'');
		var answer = answer.replace("</p>\n",'');
		var re = new RegExp('<br />\n', 'g');
		// let res = str.replace(re, "red");
		var answer = answer.replace(re,'_');
		var re = new RegExp('&amp;', 'g');
		var answer = answer.replace(re,'&');

		var data = {
			periode:periode,
			question:question,
			right_answer:right_answer,
			answer:answer,
			discussion:discussion,
		}

		$.post('{{ url("add/question/pkb") }}',data, function(result, status, xhr){
			if(result.status){
				$('#modalAddQuestion').modal('hide');
				$('#loading').hide();
				openSuccessGritter('Success!','Success Add Question');
				location.reload();
			}else{
				$('#loading').hide();
				audio_error.play();
				openErrorGritter('Error!',result.message);
				return false;
			}
		})
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