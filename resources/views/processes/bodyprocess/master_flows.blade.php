@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/bootstrap-datetimepicker.min.css') }}">
    <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
    <link href="<?php echo e(url('css/jquery.numpad.css')); ?>" rel="stylesheet">
    <style type="text/css">
        .table-bordered>tbody>tr>td {
            border: 1px solid #333 !important;
        }

        .table-bordered>thead>tr>th {
            border: 1px solid #333 !important;
        }

        .tableMaster {
            float: left;
            border-collapse: collapse;
            margin-right: 1px;
            background-color: #fff;
        }

        .tableHeadTh {
            padding: 2px;
            background-color: rgb(126, 86, 134);
            color: #FFD700;
        }

        .tableBodyTd {
            padding: 2px;
            text-transform: uppercase !important;
        }

        .tableBodyTd:hover {
            background-color: rgb(126, 86, 134);
            color: #FFD700;
            cursor: pointer;
        }

        .filters th {
            color: #333 !important;
            background-color: #cacaca;
        }

        .filters th input[type='text'] {
            width: 100%
        }

        .list-flow {
            text-transform:uppercase;     
            cursor: pointer;       
        }        

        .list-flow:hover::after {
            content: " \261A";
            color: red;
            font-weight: bold;
            transform: rotate(180deg);
        }
        

    </style>

@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }} ~ {{ strtoupper($location) }} <small class="text-purple">{{ $title_jp }} <span style="display: none">{{ $origin_group_code }}</span></small>

        </h1>
    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
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
                <table id="tableMaster" class="table table-bordered table-striped table-hover" style="margin-bottom: 5%;">
                    <thead style="background-color: rgb(126,86,134); color: #FFD700;">
                        <tr>
                            <th width="0.1%">#</th>
                            <th width="1%" style="text-align: center;">Material Type</th>
                            <th width="5%" style="text-align: center;">Flows</th>
                            {{-- <th width="5%" style="text-align: center;">Kanban</th> --}}
                            <th width="3%" style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="bodyTableMaster">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Material Type</th>
                            <th>Flows</th>
                            {{-- <th>Kanban</th> --}}
                            <th>Action</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>


        {{-- modal Edit Flow --}}
        <div class="modal modal-default fade" id="edit-flow">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color:#3C8DBC; padding-right: 1%;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                            <h1 style="font-size:20px; text-align: center; margin:5px; font-weight: bold;color: white">Edit Flow</h1>
                            <h1 id="edit-flow-title" style="font-size:20px; text-align: center; margin:5px; font-weight: bold;color: white"></h1>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box-body">
                                    <div class="col-xs-4" style="padding:0;">
                                        <input type="text" class="form-control numpad" id="add-flow-ordering" placeholder="Order Number" readonly>
                                        <div class="row" style="margin:5px 0 0 0 ;">
                                            <div class="col-xs-2" style="padding:0; z-index:100;">
                                                <button onclick="checkPreviousFlow()" class="btn btn-primary"><i class="fa fa-search"></i></button>
                                            </div>
                                            <div class="col-xs-10" style="padding:0;">
                                                <input type="text" class="form-control" id="check-previous-flow" style="text-align: center;" placeholder="-- Check Flow --" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="padding:0;">
                                        <input type="text" class="form-control" id="add-flow-name" placeholder="Flow Name">
                                    </div>
                                    <div class="col-xs-2">
                                        <button type="submit" onclick="addFlow()" class="btn btn-success"><i class="fa fa-code-fork"></i> Add Flow</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box-body">
                                    {{-- Flow tables --}}
                                    <table id="tableEditFlow" class="table table-striped table-hover table-bordered" style="margin-bottom: 0;">
                                        <thead style="">
                                            <tr>
                                                <th width="0.1%">#</th>
                                                <th width="1%">Flow Name</th>
                                                <th width="1%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyTableEditFlow">
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
        </div>

        {{-- modal Edit Kanban --}}        
        <div class="modal modal-default fade" id="edit-kanban">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color:#3C8DBC; padding-right: 1%;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                            <h1 style="font-size:20px; text-align: center; margin:5px; font-weight: bold;color: white">Edit Kanban</h1>
                            <h1 id="edit-kanban-title" style="font-size:20px; text-align: center; margin:5px; font-weight: bold;color: white"></h1>
                        </div>
                    </div>
                    <div class="modal-body">




                    </div>
                </div>
            </div>
        </div>                        


    </section>

@endsection
@section('scripts')

    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    {{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>

    <script src="{{ url('js/moment.min.js') }}"></script>
    <script src="{{ url('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="<?php echo e(url('js/jquery.numpad.js')); ?>"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

        $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
        $.fn.numpad.defaults.buttonNumberTpl =
            '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl =
            '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function() {
            $(this).find('.done').addClass('btn-primary');
        };

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '2000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '2000'
            });
        }

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });

            fillList();
        });

        function onlyUnique(value, index, self) {
            return self.indexOf(value) === index;
        }

        function fillList() {
            $('#loading').show();

            var data = {
                location: '{{ $location }}',
                origin_group_code: '{{ $origin_group_code }}'
            }
            $.get('{{ url('fetch/body_parts_process/master_flow') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#loading').hide();
                } else {
                    $('#loading').hide();
                    alert('Attempt to retrieve data failed');
                }

                $('#bodyTableMaster').html("");

                var bodyTableMaster = "";
                var no = 1;
                $.each(result.flow, function(key, value) {
                    bodyTableMaster += '<tr>';
                    bodyTableMaster += '<td style="text-align:center;">' + no + '</td>';
                    bodyTableMaster += '<td style="text-align:center;">' + value.category + '</td>';
                    bodyTableMaster += '<td><ol>';
                    $.each(value.flow.split(','), function(key, value) {
                        bodyTableMaster += '<li class="list-flow">' + value + '</li>';
                    });
                    bodyTableMaster += '</ol></td>';
                    // bodyTableMaster += '<td><ol>';
                    // if (value.kanban != null) {
                    //     $.each(value.kanban.split(','), function(key, value) {
                    //         bodyTableMaster += '<li style="text-transform:uppercase;">' + value + '</li>';
                    //     });
                    // }
                    // bodyTableMaster += '</ol></td>';
                    bodyTableMaster += '<td>';
                    // bodyTableMaster += '<div class="btn-group">';
                    bodyTableMaster += '<button class="btn btn-warning btn-md" data-toggle="modal" data-target="#edit-flow" onclick="editFlow(\'' + value.category + '\',\'' + value.flow + '\')"><i class="fa fa-edit"></i> Edit Flow</button>';
                    // bodyTableMaster += '<button class="btn btn-primary btn-md" data-toggle="modal" data-target="#edit-kanban"  onclick="editKanban(\'' + value.material_type + '\')"><i class="fa fa-edit"></i> Edit Kanban</button>';
                    // bodyTableMaster += '</div>';
                    bodyTableMaster += '</td>';
                    bodyTableMaster += '</tr>';
                    no += 1;

                });

                $('#bodyTableMaster').append(bodyTableMaster);


                $('#tableMaster tfoot th').each(function() {
                    var title = $(this).text();
                    $(this).html('<input id="search" style="text-align: center; width:100%; color:black;" type="text" placeholder="Search ' + title + '" size="20"/>');
                });

                var table = $('#tableMaster').DataTable({
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
                    // 'scrollY': '500px',
                    // 'scrollCollapse': true,
                    'paging': true,
                    'lengthChange': true,
                    'pageLength': 10,
                    'searching': true,
                    'ordering': true,
                    'order': [],
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true,
                });
                table.columns().every(function() {
                    var that = this;
                    $('#search', this.footer()).on('keyup change', function() {
                        if (that.search() !== this.value) {
                            that
                                .search(this.value)
                                .draw();
                        }
                    });
                });
                $('#tableMaster tfoot tr').appendTo('#tableMaster thead');
            });
        }

        function editFlow(category) {
            $('#edit-flow-title').text(category);

            var data = {
                location: '{{ $location }}',
                origin_group_code: '{{ $origin_group_code }}',
                category: category
            }

            $.get('{{ url('fetch/body_parts_process/singleFlow') }}', data, function(result, status, xhr) {
                if (result.status) {

                    var flow_list = "";
                    $.each(result.singleFlow, function(key, value) {
                        flow_list += '<tr>';
                        flow_list += '<td>' + value.ordering + '</td>';
                        flow_list += '<td style="text-transform: uppercase;"><b>' + value.flow + '</b></td>';
                        flow_list += '<td>';
                        flow_list += '<div class="btn-group">';

                        if (value.ordering == 1) {
                            flow_list += '<button class="btn btn-primary btn-sm" onclick="changeFlow(\'' + value.ordering + '\',\'down\',\'' + value.id + '\')"><i class="fa fa-chevron-down"></i></button>';
                        } else if (value.ordering == result.singleFlow.length) {
                            flow_list += '<button class="btn btn-warning btn-sm" onclick="changeFlow(\'' + value.ordering + '\',\'up\',\'' + value.id + '\')"><i class="fa fa-chevron-up"></i></button>';
                        } else {
                            flow_list += '<button class="btn btn-warning btn-sm" onclick="changeFlow(\'' + value.ordering + '\',\'up\',\'' + value.id + '\')"><i class="fa fa-chevron-up"></i></button>';
                            flow_list += '<button class="btn btn-primary btn-sm" onclick="changeFlow(\'' + value.ordering + '\',\'down\',\'' + value.id + '\')"><i class="fa fa-chevron-down"></i></button>';
                        }
                        flow_list += '</div>';
                        flow_list += '<div class="btn-group" style="margin:0 0 0 5px;">';
                        flow_list += '<button class="btn btn-danger btn-sm" onclick="changeFlow(\'' + value.ordering + '\',\'delete\',\'' + value.id + '\')"><i class="fa fa-trash"></i></button>';
                        flow_list += '</div>';
                        flow_list += '</td>';
                        flow_list += '</tr>';
                    });

                    $('#bodyTableEditFlow').html("");
                    $('#bodyTableEditFlow').append(flow_list);
                } else {
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function checkPreviousFlow() {
            var previousFlow = $('#add-flow-ordering').val() - 1;
            var target = $('#check-previous-flow').val(previousFlow);

            $('#bodyTableEditFlow').find('tr').each(function() {
                var flow = $(this).find('td').eq(0).text();
                if (flow == previousFlow) {
                    // target.val(this flow name )
                    target.val('After ' + $(this).find('td').eq(1).text());
                }
            });


        }

        function addFlow() {
            var data = {
                location: '{{ $location }}',
                origin_group_code: '{{ $origin_group_code }}',
                category: $('#edit-flow-title').text(),
                flow: $('#add-flow-name').val(),
                ordering: $('#add-flow-ordering').val(),
                move: 'add'
            }

            $.post('{{ url('update/body_parts_process/changeFlow') }}', data, function(result, status, xhr) {
                if (result.status) {
                    editFlow($('#edit-flow-title').text());
                    openSuccessGritter('Success!', result.message);
                } else {
                    openErrorGritter('Error!', result.message);
                }
            });
        }






        function changeFlow(ordering, move, id) {
            var data = {
                id: id,
                location: '{{ $location }}',
                origin_group_code: '{{ $origin_group_code }}',
                category: $('#edit-flow-title').text(),
                ordering: ordering,
                move: move
            }

            if (move != 'delete') {


                $.post('{{ url('update/body_parts_process/changeFlow') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        editFlow($('#edit-flow-title').text());
                        openSuccessGritter('Success!', result.message);
                    } else {
                        openErrorGritter('Error!', result.message);
                    }
                });
            } else {

                if (confirm('Are you sure want to delete this flow?')) {
                    $.post('{{ url('update/body_parts_process/changeFlow') }}', data, function(result, status, xhr) {
                        if (result.status) {
                            editFlow($('#edit-flow-title').text());
                            openSuccessGritter('Success!', result.message);
                        } else {
                            openErrorGritter('Error!', result.message);
                        }
                    });
                }
            }
        }
    </script>

@endsection
