@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            text-align: center;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(100, 100, 100);
            padding: 3px;
            vertical-align: middle;
            height: 45px;
            text-align: center;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(100, 100, 100);
            vertical-align: middle;
        }

        .dataTables_info,
        .dataTables_length {
            color: white;
        }

        div.dataTables_filter label,
        div.dataTables_wrapper div.dataTables_info {
            color: white;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
        </h1>
    </section>
@endsection

@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="box-body">
                        <div class="col-md-12" style="padding: 0px;">
                            <form method="GET" action="{{ url('fetch/excel_emp_data') }}">
                                <div class="form-group pull-right">
                                    <button type="submit" class="btn btn-success"><i
                                            class="fa fa-file-excel-o"></i>&nbsp;&nbsp;&nbsp;Excel&nbsp;</button>
                                    <a href="javascript:void(0)" onClick="fillTable()" class="btn btn-primary"><i
                                            class="fa fa-refresh"></i>&nbsp;&nbsp;Reload</a>
                                </div>
                            </form>

                        </div>

                        <div class="row">
                            <div class="col-md-12" style="overflow-x: auto;">
                                <table id="resumeTable" class="table table-bordered table-hover" style="width: 100%">
                                    <thead style="background-color: #605ca8; color: white;">
                                        <tr>
                                            <th style="width: 1%">NIK</th>
                                            <th style="width: 10%">Nama</th>
                                            <th style="width: 5%">No KTP</th>
                                            <th style="width: 1%">Tempat Lahir</th>
                                            <th style="width: 1%">Tanggal Lahir</th>
                                            <th style="width: 5%">Jabatan</th>
                                            <th style="width: 1%">No. HP</th>
                                            <th style="width: 1%">No. BPJS</th>
                                            <!-- <th style="width: 1%">Faskes</th> -->
                                            <th style="width: 1%">No. BPJSTK</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resumeTableBody" style="background-color: #fcf8e3;">
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
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    {{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $('.select2').select2();

            fillTable();
        });

        function clearConfirmation() {
            location.reload(true);
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '3000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '3000'
            });
        }

        function loadingPage() {
            $("#loading").show();
        }

        function fillTable() {
            $('#resumeTable').DataTable().destroy();

            $('#resumeTable tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" size="3"/>');
            });

            var table = $('#resumeTable').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'buttons': {
                    buttons: [{
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
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/emp_data') }}"
                },
                "columns": [{
                        "data": "employee_id"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "card_id"
                    },
                    {
                        "data": "birth_place"
                    },
                    {
                        "data": "birth_date"
                    },
                    {
                        "data": "position_new"
                    },
                    {
                        "data": "phone"
                    },
                    {
                        "data": "BPJS"
                    },
                    {
                        "data": "JP"
                    }
                ]
            });

            table.columns().every(function() {
                var that = this;
                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            $('#resumeTable tfoot tr').appendTo('#resumeTable thead');

            // $.get('<?php echo e(url('fetch/emp_data')); ?>', function(result, status, xhr){
            // 	if(result.status){
            // 		$('#resumeTable').DataTable().clear();
            // 		$('#resumeTable').DataTable().destroy();
            // 		var tableData = '';
            // 		$('#resumeTableBody').html("");
            // 		$('#resumeTableBody').empty();
            // 		$.each(result.resumes, function(key, value) {
            // 			tableData += '<tr>';
            // 			tableData += '<td>'+ value.employee_id +'</td>';
            // 			tableData += '<td>'+ value.name +'</td>';
            // 			tableData += '<td>'+ value.card_id +'</td>';
            // 			tableData += '<td>'+ value.birth_place +'</td>';
            // 			tableData += '<td>'+ value.birth_date +'</td>';
            // 			tableData += '<td>'+ value.position_new +'</td>';
            // 			tableData += '<td>'+ value.phone +'</td>';
            // 			tableData += '<td>'+ value.BPJS +'</td>';
            // 			tableData += '<td>'+ value.JP +'</td>';
            // 			tableData += '</tr>';
            // 		});
            // 		$('#resumeTableBody').append(tableData);

            // 		var table = $('#resumeTable').DataTable({
            // 			'dom': 'Bfrtip',
            // 			'responsive':true,
            // 			'lengthMenu': [
            // 			[ 10, 25, 50, -1 ],
            // 			[ '10 rows', '25 rows', '50 rows', 'Show all' ]
            // 			],
            // 			'buttons': {
            // 				buttons:[
            // 				{
            // 					extend: 'excel',
            // 					className: 'btn btn-info',
            // 					text: '<i class="fa fa-file-excel-o"></i> Excel',
            // 					exportOptions: {
            // 						columns: ':not(.notexport)'
            // 					}
            // 				},
            // 				{
            // 					extend: 'pageLength',
            // 					className: 'btn btn-default',
            // 				},
            // 				]
            // 			},
            // 			'paging': true,
            // 			'lengthChange': true,
            // 			'searching': true,
            // 			'ordering': true,
            // 			'info': true,
            // 			'autoWidth': true,
            // 			"sPaginationType": "full_numbers",
            // 			"bJQueryUI": true,
            // 			"bAutoWidth": false,
            // 			"processing": false,
            // 		});
            // 	}
            // 	else{
            // 		openErrorGritter('Error!', result.message);
            // 	}
            // });
        }
    </script>
@endsection
