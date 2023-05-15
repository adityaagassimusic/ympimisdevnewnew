@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead input {
		width: 100%;
		padding: 3px;
		box-sizing: border-box;
	}
	#bodyTablePayment > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
	}

	#bodyTablePaymentAfter > tr:hover {
		cursor: pointer;
		background-color: #7dfa8c;
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
		border:1px solid rgb(150,150,150);
		vertical-align: middle;

	}
	#loading { display: none; }
</style>
@stop

@section('header')
<section class="content-header">
	<h1>
		{{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
	</h1>
	<ol class="breadcrumb">
	</ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
			</center>
		</div>
	</div>
	<div class="box">
		<div class="box-body" style="overflow-x:scroll;">
			<div class="row">
				<div class="col-sm-12" style="margin-top:20px">
                    <span style="color:red;font-size: 20px;"> Outstanding Invoice Without ID Payment </span>
                </div>
			</div>
			<table id="tablePayment" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
                        <th>#</th>
                        <th>Vendor</th>  
                        <th>Invoice No</th>
                        <th>Currency</th>
                        <th>Amount</th>
                        <th>PPN</th>
                        <th>PPH</th>
                        <th>Net Payment</th>
                        <th>Payment Term</th>
                        <th>Due Date</th>
                        <th>Surat Jalan</th>
                        <th>Faktur Pajak</th>
                        <th>PO Number</th>
                        <th>TT Date</th>
                        <th>DO Date</th>
                        <th>Dist Date Acc</th> 
                        <th>Dist Date Pch</th>
                        <th>Inv Date</th>
                        <th>File Invoice</th>                           
                        <th>ID Payment</th>
					</tr>
				</thead>
				<tbody id="bodyTablePayment">
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
					</tr>
				</tfoot>
			</table>
            <button class="btn btn-success" style="float:right;margin-top: 20px;" onclick="cek()"><i class="fa fa-save"></i> Save</button>
        </div>
		<div class="box-body" style="overflow-x:scroll;">
			<div class="row">
				<div class="col-sm-12" style="margin-top:20px">
	                <span style="color:blue;font-size: 20px;"> Outstanding Invoice Not Jurnal</span>
	            </div>
			</div>
			<table id="tablePaymentAfter" class="table table-bordered table-striped table-hover">
				<thead style="background-color: rgba(126,86,134,.7);">
					<tr>
	                    <th>#</th>
                        <th>Vendor</th>  
                        <th>Invoice No</th>
                        <th>Currency</th>
                        <th>Amount</th>
                        <th>PPN</th>
                        <th>PPH</th>
                        <th>Net Payment</th>
                        <th>Payment Term</th>
                        <th>Due Date</th>
                        <th>Surat Jalan</th>
                        <th>Faktur Pajak</th>
                        <th>PO Number</th>
                        <th>TT Date</th>
                        <th>DO Date</th>
                        <th>Dist Date Acc</th> 
                        <th>Dist Date Pch</th>
                        <th>Inv Date</th>
                        <th>File Invoice</th>                          
                        <th>ID Payment</th>
					</tr>
				</thead>
				<tbody id="bodyTablePaymentAfter">
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
					</tr>
				</tfoot>
			</table>
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
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
    	$('body').toggleClass("sidebar-collapse");
		fetchTable();
		fetchDataPayment();
	});

	$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd",
			todayHighlight: true,	
		});

	$('.select2').select2({
		dropdownAutoWidth : true,
		allowClear: true
	});

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
	var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

	function clearNew(){
	}


    var counts = 0;

	function fetchTable(){
		$('#loading').show();
		$.get('{{ url("fetch/check_payment_request") }}', function(result, status, xhr){
			if(result.status){
				$('#tablePayment').DataTable().clear();
				$('#tablePayment').DataTable().destroy();				
				$('#bodyTablePayment').html("");
				var listTableBody = "";
				var ppn = 0;
				var pph = 0;

				$.each(result.payment, function(key, value){
					if (value.ppn != null) {
						ppn = value.amount*value.ppn/100;
					}else{
						ppn = 0;
					}

					if (value.pph != null) {
						pph = value.amount*value.pph/100;
					}else{
						pph = 0;
					}

					listTableBody += '<tr class="member">';
                    listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.supplier_name+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.invoice+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.currency+'</td>';
                    listTableBody += '<td style="width:1%;">'+(value.amount || '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+(ppn.toFixed(1) || '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+(pph.toFixed(1) || '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.net_payment+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.payment_term+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.payment_due_date+'</td>';
                    listTableBody += '<td style="width:0.1%;">'+(value.surat_jalan|| '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+(value.faktur_pajak|| '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+(value.po_number|| '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.tt_date))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.do_date))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.status_gm.split('/')[1]))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.dist_date_pch))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.inv_date))+'</td>';
                    listTableBody += '<td style="width:1%;"><a target="_blank" href="{{ url("files/invoice") }}/'+value.file+'"><i class="fa fa-paperclip"></i></a></td>';
                    listTableBody += '<input type="hidden" class="form-control" id="id_'+key+'" value="'+value.id_payment_detail+'">';
                    listTableBody += '<td style="width:5%;"><input type="text" class="form-control payment" id="payment_acc_'+key+'" name="payment_acc_'+key+'"></td>';
                    // listTableBody += '<td style="width:2%;"></td>';
                    listTableBody += '</tr>';
                    counts++;
				});

				$('#bodyTablePayment').append(listTableBody);

				$('#tablePayment tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

				var table = $('#tablePayment').DataTable({
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
					'pageLength': 20,
					'searching': true,
					'ordering': true,
					'order': [],
					'info': true,
					'autoWidth': true,
					"sPaginationType": "full_numbers",
					"bJQueryUI": true,
					"bAutoWidth": false,
					"processing": true,
					initComplete: function() {
                    this.api()
                        .columns([1,2,9,13,14,15,17])
                        .every(function(dd) {
                            var column = this;
                            var theadname = $("#tablePayment th").eq([dd]).text();
                            var select = $(
                                    '<select style="width:100%"><option value="" style="font-size:11px;">All</option></select>'
                                )
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                    column.search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });
                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function(d, j) {
                                    var vals = d;
                                    if ($("#tablePayment th").eq([dd]).text() == 'Category') {
                                        vals = d.split(' ')[0];
                                    }
                                    select.append('<option style="font-size:11px;" value="' +
                                        d + '">' + vals + '</option>');
                                });
                        });
                	},
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

				$('#tablePayment tfoot tr').appendTo('#tablePayment thead');

				$('#loading').hide();

			}
			else{
				audio_error.play();
				openErrorGritter('Error', result.message);
				$('#loading').hide();
			}
		});
	}


    function fetchDataPayment() {
        $.get('{{ url("fetch/check_payment_request_after") }}',  function(result, status, xhr){
            if(result.status){
                $('#tablePaymentAfter').DataTable().clear();
                $('#tablePaymentAfter').DataTable().destroy();
                $("#bodyTablePaymentAfter").html('');
                var listTableBody = '';
                var ppn = 0;
                var pph = 0;

                $.each(result.payment, function(key, value){

                	if (value.ppn != null) {
						ppn = value.amount*value.ppn/100;
					}else{
						ppn = 0;
					}

					if (value.pph != null) {
						pph = value.amount*value.pph/100;
					}else{
						pph = 0;
					}

                    listTableBody += '<tr class="member">';
                    listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.supplier_name+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.invoice+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.currency+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.amount+'</td>';
                    listTableBody += '<td style="width:2%;">'+ppn+'</td>';
                    listTableBody += '<td style="width:2%;">'+pph+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.net_payment+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.payment_term+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.payment_due_date+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.surat_jalan+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.faktur_pajak+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.po_number+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.tt_date))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.do_date))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.status_gm.split('/')[1]))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.dist_date_pch))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.inv_date))+'</td>';
                    listTableBody += '<td style="width:1%;"><a target="_blank" href="{{ url("files/invoice") }}/'+value.file+'"><i class="fa fa-paperclip"></i></a></td>';
                    listTableBody += '<td style="width:2%;">'+value.acc_payment+'</td>';
                    listTableBody += '</tr>';
                    counts++;
                });

                $('#bodyTablePaymentAfter').append(listTableBody);

                $('#tablePaymentAfter tfoot th').each( function () {
					var title = $(this).text();
					$(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
				} );

                var table2 = $('#tablePaymentAfter').DataTable({
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
                    'searching': true   ,
                    'ordering': true,
                    'order': [],
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true,
                    initComplete: function() {
                    this.api()
                        .columns([1,2,9,13,14,15,17])
                        .every(function(dd) {
                            var column = this;
                            var theadname = $("#tablePaymentAfter th").eq([dd]).text();
                            var select = $(
                                    '<select style="width:100%"><option value="" style="font-size:11px;">All</option></select>'
                                )
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                    column.search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });
                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function(d, j) {
                                    var vals = d;
                                    if ($("#tablePaymentAfter th").eq([dd]).text() == 'Category') {
                                        vals = d.split(' ')[0];
                                    }
                                    select.append('<option style="font-size:11px;" value="' +
                                        d + '">' + vals + '</option>');
                                });
                        });
                	},
                });

                table2.columns().every( function () {
					var that = this;

					$( 'input', this.footer() ).on( 'keyup change', function () {
						if ( that.search() !== this.value ) {
							that
							.search( this.value )
							.draw();
						}
					} );
				} );

				$('#tablePaymentAfter tfoot tr').appendTo('#tablePaymentAfter thead');

				$('#loading').hide();
            }else{
                openErrorGritter('Error!','Failed Get Data');
            }
        });
    }

	function getFormattedDate(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;
        
        return day + '-' + monthNames[month] + '-' + year;
    }

    function cek() {
        if (confirm('Apakah Anda yakin?')) {
            $('#loading').show();

            var id_payment = [];
            var payment = [];

            var formData = new FormData();

            for(var i = 0; i < counts;i++){
                id_payment.push($('#id_'+i).val());
                payment.push($('#payment_acc_'+i).val());
            }
            
            formData.append('id_payment', id_payment);
            formData.append('payment', payment);

            $.ajax({
                url:"{{ url('post/accounting/payment_request') }}",
                method:"POST",
                data:formData,
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function (response) {
                    if (response.status) {
                        $("#loading").hide();
                        openSuccessGritter("Success", "Data Berhasil Disimpan");
                        location.reload();
                    }else{
                        $("#loading").hide();
                        openErrorGritter("Error", "ID Payment Sudah ada");
                    }
                //     console.log(response.message);
                },
                error: function (response) {
                    console.log(response.message);
                },
            })
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

</script>
@endsection

