@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        th,
        td {
            white-space: nowrap;
        }

        div.dataTables_wrapper {
            margin: 0 auto;
        }

        #bodyTb>tr:hover td {
            background-color: #7dfa8c !important;
            color: black !important;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            padding-top: 7.5px;
            padding-bottom: 7.5px;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            padding-top: 2px;
            padding-bottom: 2px;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            vertical-align: middle;
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
            List of {{ $page }}s
        </h1>
        <ol class="breadcrumb">
            <li>
                @if (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'PRD'))
                    <a data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" style="color:white">Delete
                        {{ $page }}s</a>
                    &nbsp;
                    <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm"
                        style="color:white">Import {{ $page }}s</a>
                    &nbsp;
                    <a data-toggle="modal" data-target="#createModal" class="btn btn-primary btn-sm"
                        style="color:white">Create {{ $page }}</a>
                @endif
            </li>
        </ol>
    </section>
@endsection
@section('content')
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

        <div class="row">
            <div class="col-xs-2 no-padding" style="margin-bottom: 1%;">
                <div class="col-xs-12">
                    <label>Select Month</label>
                    <div class="input-group date pull-right" style="text-align: center;">
                        <div class="input-group-addon bg-purple">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control monthpicker" name="month" id="month"
                            onchange="drawTableNew()" placeholder="Select Month">
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-body">
                        <h3 style="margin-top: 0px;" id="month_prod"></h3>
                        <table id="tablePs" class="table table-bordered table-hover table-responsive" style="">
                            <thead id="headTb">
                                <tr>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="bodyTb">
                                <tr>
                                    <th></th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Create {{ $page }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">Material<span class="text-red">*</span></label>
                                <div class="col-sm-6" align="left">
                                    <select class="form-control select2" name="material_number" id="material_number"
                                        style="width: 100%;" data-placeholder="Choose a Material Number..." required>
                                        <option value=""></option>
                                        @foreach ($materials as $material)
                                            <option value="{{ $material->material_number }}">
                                                {{ $material->material_number }} -
                                                {{ $material->material_description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">Due Date<span class="text-red">*</span></label>
                                <div class="col-sm-6">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="due_date"
                                            name="due_date">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input min="1" type="number" class="form-control" id="quantity"
                                            name="quantity" placeholder="Enter Quantity" required>
                                        <span class="input-group-addon">pc(s)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">
                            Cancel
                        </button>
                        <button type="button" onclick="create()" class="btn btn-primary" data-dismiss="modal">
                            <i class="fa fa-plus"></i> Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="ViewModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Detail {{ $page }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <div class="form-group row" align="right">
                                <label class="col-sm-6">Material Number</label>
                                <div class="col-sm-6" align="left" id="material_number_view"></div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-6">Material Description</label>
                                <div class="col-sm-6" align="left" id="material_description_view"></div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-6">Origin Group</label>
                                <div class="col-sm-6" align="left" id="origin_group_view"></div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-6">Due Date</label>
                                <div class="col-sm-6" align="left" id="due_date_view"></div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-6">Quantity</label>
                                <div class="col-sm-6" align="left" id="quantity_view"></div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-6">Created By</label>
                                <div class="col-sm-6" align="left" id="created_by_view"></div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-6">Last Update</label>
                                <div class="col-sm-6" align="left" id="last_updated_view"></div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-6">Created At</label>
                                <div class="col-sm-6" align="left" id="created_at_view"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="EditModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Create {{ $page }}</h4>
                    </div>
                    <div class="modal-body">
                        <div class="box-body">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">Material<span class="text-red">*</span></label>
                                <div class="col-sm-6" align="left">
                                    <select class="form-control select2" id="material_number_edit" style="width: 100%;"
                                        data-placeholder="Choose a Material Number..." required disabled>
                                        <option value=""></option>
                                        @foreach ($materials as $material)
                                            <option value="{{ $material->material_number }}">
                                                {{ $material->material_number }}
                                                - {{ $material->material_description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">Due Date<span class="text-red">*</span></label>
                                <div class="col-sm-6">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="due_date_edit"
                                            name="due_date" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" align="right">
                                <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
                                <div class="col-sm-6">
                                    <div class="input-group">
                                        <input min="1" type="number" class="form-control" id="quantity_edit"
                                            placeholder="Enter Quantity" required>
                                        <input type="hidden" id="id_edit">
                                        <span class="input-group-addon">pc(s)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                        <button type="button" onclick="edit()" class="btn btn-warning" data-dismiss="modal"><i
                                class="fa fa-pencil"></i> Edit</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="importForm" method="get" action="{{ url('destroy/production_schedule') }}"
                        enctype="multipart/form-data">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>From</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" id="datefrom"
                                                name="datefrom" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>To</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" id="dateto"
                                                name="dateto" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Location</label>
                                        <select class="form-control select2" multiple="multiple" name="location[]"
                                            id='location' data-placeholder="Select Location" style="width: 100%;"
                                            required>
                                            <option></option>
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->category }},{{ $location->hpl }}">
                                                    {{ $location->category }} - {{ $location->hpl }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button id="modalImportButton" type="submit"
                                onclick="return confirm('Are you sure you want to delete this production schedule?');"
                                class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
                    </div>
                    <div class="modal-body" id="modalDeleteBody">
                        Are you sure delete?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="importForm" method="post" action="{{ url('import/production_schedule') }}"
                        enctype="multipart/form-data">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
                            Format: [Material Number][Due Date][Quantity]<br>
                            Sample: <a
                                href="{{ url('download/manual/import_production_schedule.txt') }}">import_production_schedule.txt</a>
                            Code: #Add
                        </div>
                        <div class="">
                            <div class="modal-body">
                                <center>
                                    <input type="file" name="production_schedule" id="InputFile" accept="text/plain">
                                </center>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
                            </div>
                    </form>
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
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/dataTables.fixedColumns.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('#due_date').datepicker({
                autoclose: true,
                format: "dd/mm/yyyy",
                todayHighlight: true
            });

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

            $('.select2').select2();

            // drawTable();
            drawTableNew();
        });

        function drawTableNew() {
            var data = {
                month: $('#month').val(),
            }

            $('#loading').show();

            $.get('{{ url('fetch/production_schedule_new') }}', data, function(result, status, xhr) {

                if (result.status) {

                    $('#month_prod').text(result.month);
                    $('#tablePs').DataTable().clear();
                    $('#tablePs').DataTable().destroy();

                    $('#headTb').html("");


                    var mcolor = 'background-color: white;';
                    var tcolor = 'background-color: #fffcb7;';
                    var headcolor = 'background-color: #605ca8; color: white; padding: 0px;';

                    var tableHead = '<tr>';
                    tableHead += '<th style="vertical-align: middle; text-align: center; ';
                    tableHead += headcolor + '">GMC</th>';

                    tableHead += '<th style="vertical-align: middle; text-align: center; ';
                    tableHead += headcolor + '">DESC</th>';

                    tableHead += '<th style="vertical-align: middle; text-align: center; ';
                    tableHead += headcolor + '">HPL</th>';

                    for (var i = 0; i < result.calendars.length; i++) {
                        if (result.calendars[i].remark == 'H') {
                            tableHead +=
                                '<th style="vertical-align: middle; text-align: center; background-color: #ff6969; padding: 0px; width: 20px;">';
                            tableHead += result.calendars[i].week_date.slice(8) + '</th>';
                        } else {
                            tableHead +=
                                '<th style="vertical-align: middle; text-align: center; background-color: #00cc6e; padding: 0px; width: 20px;">';
                            tableHead += result.calendars[i].week_date.slice(8) + '</th>';
                        }
                    }
                    tableHead += '<th style="vertical-align: middle; text-align: center; ';
                    tableHead += tcolor + '">';
                    tableHead += 'TOTAL</th>';

                    tableHead += '</tr>';
                    $('#headTb').append(tableHead);


                    $('#bodyTb').html("");
                    var tableBody = '';

                    for (var i = 0; i < result.materials.length; i++) {
                        tableBody += '<tr>';

                        tableBody += '<td style="vertical-align: middle; text-align: center; ';
                        tableBody += mcolor + '">';
                        tableBody += result.materials[i].material_number + '</td>';

                        tableBody += '<td style="vertical-align: middle; text-align: left; width: 40%; ';
                        tableBody += bgcolor + '">';
                        tableBody += result.materials[i].material_description + '</td>';

                        tableBody += '<td style="vertical-align: middle; text-align: center; ';
                        tableBody += bgcolor + '">';
                        tableBody += result.materials[i].hpl + '</td>';

                        var sum_row = 0;

                        for (var j = 0; j < result.calendars.length; j++) {

                            var bgcolor = 'background-color: white;';
                            if (result.calendars[j].remark == 'H') {
                                bgcolor = 'background-color: gainsboro;';
                            }
                            var inserted = false;

                            for (var k = 0; k < result.prod_schedules.length; k++) {
                                if ((result.prod_schedules[k].material_number == result.materials[i]
                                        .material_number) &&
                                    (result.prod_schedules[k].due_date == result.calendars[j].week_date)) {

                                    tableBody += '<td onclick="modalEdit(id)" ';
                                    tableBody += 'style="text-align: center; vertical-align: middle; ';
                                    tableBody += bgcolor;
                                    tableBody += 'width: 10px; cursor:pointer;" ';
                                    tableBody += 'id="' + result.prod_schedules[k].id + '" >';
                                    tableBody += result.prod_schedules[k].quantity + '</td>';

                                    sum_row += result.prod_schedules[k].quantity;
                                    inserted = true;
                                    break;
                                }
                            }

                            if (!inserted) {
                                tableBody += '<td style="text-align: center; vertical-align: middle; width: 10px;';
                                tableBody += bgcolor + '">';
                                tableBody += '0</td>';
                            }

                        }


                        tableBody += '<td style="text-align: right; vertical-align: middle; ';
                        tableBody += tcolor + '">';
                        tableBody += sum_row + '</td>';

                        tableBody += '</tr>';
                    }

                    $('#bodyTb').append(tableBody);

                    var table = $('#tablePs').DataTable({
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
                                },
                            ]
                        },
                        "searching": true,
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        'ordering': false,
                        'scrollX': true,
                        'scrollCollapse': true,
                        'fixedColumns': {
                            left: 3,
                        }
                    });

                    $('#loading').hide();
                }


            });

        }


        function drawTable() {
            $('#example1 tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" size="16"/>');
            });
            var table = $('#example1').DataTable({
                "order": [],
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
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
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/production_schedule') }}"
                },
                "columns": [{
                        "data": "material_number"
                    },
                    {
                        "data": "material_description"
                    },
                    {
                        "data": "origin_group_name"
                    },
                    {
                        "data": "hpl"
                    },
                    {
                        "data": "due_date"
                    },
                    {
                        "data": "quantity"
                    },
                    {
                        "data": "action"
                    }
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
                        },
                    ]
                }
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

            $('#example1 tfoot tr').appendTo('#example1 thead');
        }

        function create() {
            var data = {
                material_number: $("#material_number").val(),
                due_date: $("#due_date").val(),
                quantity: $("#quantity").val()
            };

            $.post('{{ url('create/production_schedule') }}', data, function(result, status, xhr) {
                if (result.status == true) {
                    $('#example1').DataTable().ajax.reload(null, false);
                    openSuccessGritter("Success", "New Production schedule has been created.");
                } else {
                    openErrorGritter("Error", "Production schedule not created.");
                }
            })
        }

        function modalView(id) {
            $("#ViewModal").modal("show");
            var data = {
                id: id
            }

            $.get('{{ url('view/production_schedule') }}', data, function(result, status, xhr) {
                $("#material_number_view").text(result.datas[0].material_number);
                $("#material_description_view").text(result.datas[0].material_description);
                $("#origin_group_view").text(result.datas[0].origin_group_name);
                $("#due_date_view").text(result.datas[0].due_date);
                $("#quantity_view").text(result.datas[0].quantity);
                $("#created_by_view").text(result.datas[0].name);
                $("#last_updated_view").text(result.datas[0].updated_at);
                $("#created_at_view").text(result.datas[0].created_at);
            })
        }

        function modalDelete(id) {
            var data = {
                id: id
            };

            if (!confirm("Are you sure want to delete Material schedule ?")) {
                return false;
            }

            $.post('{{ url('delete/production_schedule') }}', data, function(result, status, xhr) {
                $('#example1').DataTable().ajax.reload(null, false);
                openSuccessGritter("Success", "Delete Material Schedule");
            })
        }


        function modalEdit(id) {
            $('#EditModal').modal("show");

            var data = {
                id: id
            };

            $.get('{{ url('edit/production_schedule') }}', data, function(result, status, xhr) {
                $("#id_edit").val(id);
                $('#material_number_edit').val(result.datas.material_number).trigger('change.select2');
                $("#due_date_edit").val(result.datas.due_date);
                $("#quantity_edit").val(result.datas.quantity);
            })
        }

        function edit() {
            var data = {
                id: $("#id_edit").val(),
                quantity: $("#quantity_edit").val()
            };

            $.post('{{ url('edit/production_schedule') }}', data, function(result, status, xhr) {
                if (result.status == true) {
                    $('#example1').DataTable().ajax.reload(null, false);
                    openSuccessGritter("Success", "New Production schedule has been edited.");
                } else {
                    openErrorGritter("Error", "Failed to edit.");
                }
            })
        }

        $(function() {
            $('#datefrom').datepicker({
                autoclose: true
            });
            $('#dateto').datepicker({
                autoclose: true
            });
            $('.select2').select2();
        })

        function deleteConfirmation(url, name, id) {
            jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
            jQuery('#modalDeleteButton').attr("href", url + '/' + id);
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
    </script>

@stop
