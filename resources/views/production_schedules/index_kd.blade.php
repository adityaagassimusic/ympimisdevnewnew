@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            text-align: center;
        }

        tbody>tr>td {
            text-align: center;
        }

        tfoot>tr>th {
            text-align: center;
        }

        td:hover {
            overflow: visible;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 0;
            padding-bottom: 0;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
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
            {{ $page }}
        </h1>
        <ol class="breadcrumb">
            <li>
                <a data-toggle="modal" data-target="#achievement" class="btn btn-default btn-sm"
                    style="color:black; background-color: #e7e7e7;">
                    &nbsp;<i class="fa fa-file-o"></i>&nbsp;Achievement Report
                </a>
                @if (str_contains(Auth::user()->role_code, 'MIS') ||
                        str_contains(Auth::user()->role_code, 'PRD') ||
                        str_contains(Auth::user()->role_code, 'PC'))
                    <a data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" style="color:white">
                        &nbsp;<i class="fa fa-trash-o"></i>&nbsp;Delete Schedule
                    </a>
                    <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm" style="color:white">
                        &nbsp;<i class="fa fa-plus-square-o"></i>&nbsp;Import Schedule
                    </a>
                @endif
                <a data-toggle="modal" data-target="#info" class="btn btn-info btn-sm" style="color:white">
                    &nbsp;&nbsp;<i class="fa fa-info-circle"></i>&nbsp;Shipment Roles&nbsp;&nbsp;
                </a>
            </li>
        </ol>
    </section>
@endsection


@section('content')

    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: white; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
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

                <div class="row">
                    <div class="col-xs-2" style="padding-right: 0px;">
                        <div class="input-group date pull-right" style="text-align: center;">
                            <div class="input-group-addon bg-green">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control monthpicker" name="month" id="month"
                                placeholder="Select Month">
                        </div>
                    </div>

                    <div class="col-xs-3">
                        <select class="form-control select2" multiple="multiple" id='hpl' id='hpl'
                            data-placeholder="Select Work Center" style="width: 100%;">
                            @foreach ($locations as $location)
                                <option value="{{ $location->hpl }}">KD - {{ $location->hpl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xs-2" style="padding: 0px;">
                        <button onclick="fillAllTable()" class="btn btn-primary">Search</button>
                    </div>
                </div>

                <div class="nav-tabs-custom" style="margin-top: 1%;">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Production
                                Schedule</a></li>
                        <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Packing Schedule</a>
                        </li>
                        {{-- <li class="vendor-tab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">Shipment Schedule</a>
                        </li> --}}
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                            <h3 style="margin-top: 0px;">Step1 : Production Schedule <span class="pull-right"
                                    id="month_prod"></span></h3>
                            <table id="tableProd" class="table table-bordered" style="width: 100%; font-size: 12px;">
                                <thead id="headProdSch" style="background-color: rgba(126,86,134,.7);">
                                    <th></th>
                                </thead>
                                <tbody id="bodyProdSch" style="vertical-align: middle;">
                                    <th></th>
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
                            @if (str_contains(Auth::user()->role_code, 'PC') ||
                                    str_contains(Auth::user()->role_code, 'MIS') ||
                                    str_contains(Auth::user()->role_code, 'PRD'))
                                <div class="row">
                                    <div class="col-xs-4">
                                        <a data-toggle="modal" data-target="#generateModal"
                                            class="btn btn-warning btn-sm" style="color:white"><span
                                                class="fa fa-refresh"></span>&nbsp;&nbsp;&nbsp;Generate Packing Schedule
                                        </a>
                                        <a data-toggle="modal" data-target="#adjustmentModal"
                                            class="btn btn-primary btn-sm" style="color:white">
                                            &nbsp;<i class="fa fa-share-square-o"></i>&nbsp;Launch Schedule
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <h3>Step2 : Packing Schedule <span class="pull-right" id="month_pack"></span></h3>
                            <table id="tablePack" class="table table-bordered" style="width: 100%; font-size: 12px;">
                                <thead id="headLotSch" style="background-color: rgba(126,86,134,.7);">
                                    <th></th>
                                </thead>
                                <tbody id="bodyLotSch" style="vertical-align: middle;">
                                    <th></th>
                                </tbody>
                            </table>
                        </div>

                        {{-- <div class="tab-pane" id="tab_3" style="overflow-x: auto;">
                        @if (str_contains(Auth::user()->role_code, 'PC') || str_contains(Auth::user()->role_code, 'MIS'))
                        <div class="row">
                            <div class="col-xs-2">
                                <a data-toggle="modal" data-target="#shipmentModal" class="btn btn-warning btn-sm" style="color:white"><span class="fa fa-refresh"></span>&nbsp;&nbsp;&nbsp;Generate Shipment Schedule KDs</a>
                            </div>
                        </div>
                        @endif

                        <h3>Step3 : Shipment Schedule <span class="pull-right" id="month_ship"></span></h3>
                        <table id="tableShip" class="table table-bordered" style="width: 100%; font-size: 12px;">
                            <thead id="headShipSch" style="background-color: rgba(126,86,134,.7);">
                                <th></th>
                            </thead>
                            <tbody id="bodyShipSch" style="vertical-align: middle;">
                                <th></th>
                            </tbody>
                        </table>
                    </div> --}}

                    </div>

                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Delete Production Schedule</h4>
                    Delete Production Schedule akan mengahapus schedule yang telah ada<br>
                    Data Production Schedule yang dihapus tidak dapat dikembalikan
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <div class="col-xs-12">
                                <label>Select Month</label>
                                <div class="input-group date pull-right" style="text-align: center;">
                                    <div class="input-group-addon bg-red">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control monthpicker" name="delete_month"
                                        id="delete_month" placeholder="Select Month">
                                </div>

                            </div>
                            <div class="col-xs-12" style="margin-top: 3%;">
                                <label>Select Work Center</label>
                                <select class="form-control select2" multiple="multiple" id='delete_hpl' id='delete_hpl'
                                    data-placeholder="Select Work Center" style="width: 100%;">
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->hpl }}">KD - {{ $location->hpl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="deleteProd()" class="btn btn-danger">Delete </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Shipment Roles</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <h2>YMMJ</h2>
                            YMMJ : Selasa - Kamis export Jumat<br>
                            YMMJ : Jumat - Senin export Selasa
                            <h2>XY</h2>
                            XY : Rabu - Jumat export Senin<br>
                            XY : Senin - Selasa export Rabu
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="achievement" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 70%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Achievement Report</h4>
                </div>
                <div class="modal-body">

                    <div class="row" style="margin-bottom: 2%;">
                        <div class="col-xs-2" style="padding-right: 0px;">
                            <div class="input-group date pull-right" style="text-align: center;">
                                <div class="input-group-addon bg-green">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control monthpicker" name="achievement_month"
                                    id="achievement_month" placeholder="Select Month">
                            </div>
                        </div>

                        <div class="col-xs-4">
                            <select class="form-control select2" multiple="multiple" id='achievement_hpl'
                                id='achievement_hpl' style="width: 100%;">
                                @foreach ($locations as $location)
                                    <option value="{{ $location->hpl }}">KD - {{ $location->hpl }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xs-2" style="padding: 0px;">
                            <button onclick="fillAch()" class="btn btn-primary">Search</button>
                        </div>
                    </div>

                    <table class="table table-hover table-bordered table-striped" id="tableDetail">
                        <thead style="background-color: rgba(126,86,134,.7);">
                            <tr>
                                <th style="width: 10%; text-align: center; vertical-align: middle;">Material</th>
                                <th style="width: 40%; text-align: center; vertical-align: middle;">Description</th>
                                <th style="width: 7.5%; text-align: center; vertical-align: middle;">SLoc</th>
                                <th style="width: 15%; text-align: center; vertical-align: middle;">Work Center</th>
                                <th style="width: 7.5%; text-align: center; vertical-align: middle;">Target</th>
                                <th style="width: 7.5%; text-align: center; vertical-align: middle;">Actual</th>
                                <th style="width: 7.5%; text-align: center; vertical-align: middle;">Diff</th>
                            </tr>
                        </thead>
                        <tbody id="bodyDetail">
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="importForm" method="post" action="{{ url('import/production_schedule_kd') }}"
                    enctype="multipart/form-data">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
                        Format: [Material Number][Due Date][Quantity]<br>
                        Sample: <a
                            href="{{ url('download/manual/import_production_schedule.txt') }}">import_production_schedule.txt</a>
                        Code: #Add
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-6 col-xs-offset-3">
                                <div class="col-xs-12">
                                    <input type="file" name="production_schedule" id="InputFile" accept="text/plain">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Generate Packing Schedule</h4>
                    Generate Packing Schedule akan mengahapus schedule yang telah ada<br>
                    Dan akan diganti dengan hasil generate dari Production Schedule yang terbaru
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <div class="col-xs-12">
                                <label>Select Month</label>
                                <div class="input-group date pull-right" style="text-align: center;">
                                    <div class="input-group-addon bg-green">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control monthpicker" name="generate_month"
                                        id="generate_month" placeholder="Select Month">
                                </div>

                            </div>
                            <div class="col-xs-12" style="margin-top: 3%;">
                                <label>Select Work Center</label>
                                <select class="form-control select2" multiple="multiple" id='generate_hpl'
                                    id='generate_hpl' data-placeholder="Select Work Center" style="width: 100%;">
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->hpl }}">KD - {{ $location->hpl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="generate()" class="btn btn-primary">Generate </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shipmentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Generate Shipment Schedule</h4>
                    Generate Shipment Schedule akan mengahapus schedule yang telah ada<br>
                    Dan akan diganti dengan hasil generate dari Production Schedule yang terbaru
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <div class="col-xs-12">
                                <label>Select Month</label>
                                <div class="input-group date pull-right" style="text-align: center;">
                                    <div class="input-group-addon bg-green">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control monthpicker" name="shipment_month"
                                        id="shipment_month" placeholder="Select Month">
                                </div>

                            </div>
                            <div class="col-xs-12" style="margin-top: 3%;">
                                <label>Select Work Center</label>
                                <select class="form-control select2" multiple="multiple" id='shipment_hpl'
                                    id='shipment_hpl' data-placeholder="Select Work Center" style="width: 100%;">
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->hpl }}">KD - {{ $location->hpl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="shipment()" class="btn btn-primary">Generate </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="adjustmentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Launch Production Schedule</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <div class="col-xs-12">
                                <label>Select Month</label>
                                <div class="input-group date pull-right" style="text-align: center;">
                                    <div class="input-group-addon bg-green">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control monthpicker" name="adjustment_month"
                                        id="adjustment_month" placeholder="Select Month">
                                </div>
                            </div>
                            <div class="col-xs-12" style="margin-top: 3%;">
                                <label>Select Work Center</label>
                                <select class="form-control select2" multiple="multiple" id='adjusment_hpl'
                                    id='delete_hpl' data-placeholder="Select Work Center" style="width: 100%;">
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->hpl }}">KD - {{ $location->hpl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="adjusment()" class="btn btn-primary">Adjustment </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('#adjustment_date').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

            $('body').toggleClass("sidebar-collapse");

            $('.select2').select2();

            // fillAllTable();

        });

        $('#adjustmentModal').on('hidden.bs.modal', function() {
            $('#adjustment_month').val('');
            $('#adjusment_hpl').val('');
            $("#adjusment_hpl").trigger("change");
        });

        $('#generateModal').on('hidden.bs.modal', function() {
            $('#generate_month').val('');
            $("#generate_hpl").val("");
            $("#generate_hpl").trigger("change");
        });


        $('#shipmentModal').on('hidden.bs.modal', function() {
            $('#shipment_month').val('');
            $("#shipment_hpl").val("");
            $("#shipment_hpl").trigger("change");
        });

        $('#achievement').on('shown.bs.modal', function() {
            $('#achievement_month').val('');
            $("#achievement_hpl").val("");
            $("#achievement_hpl").trigger("change");

            $("#achievement_hpl").select2({
                placeholder: "Select Work Center"
            });

            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();
            $('#tableDetail').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [-1],
                    ['Show all']
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
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': false,
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });
        });

        function fillAch() {
            var month = $('#achievement_month').val();
            var hpl = $('#achievement_hpl').val();

            var data = {
                month: month,
                hpl: hpl,
            }

            $('#loading').show();

            $.get('{{ url('fetch/achievement_schedule_kd') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#tableDetail').DataTable().clear();
                    $('#tableDetail').DataTable().destroy();
                    $('#bodyDetail').html("");
                    var tableData = "";
                    var css = "padding: 0px 5px 0px 5px;";

                    for (var i = 0; i < result.data.length; i++) {
                        var bg_color = "";
                        if (result.data[i].diff > 0) {
                            var bg_color = "background-color: rgb(255, 204, 255);";
                        }

                        tableData += '<tr>';
                        tableData += '<td style="' + css + ' width:10%; text-align:center;">' + result.data[i]
                            .material_number + '</td>';
                        tableData += '<td style="' + css + ' width:45%; text-align:left;">' + result.data[i]
                            .material_description + '</td>';
                        tableData += '<td style="' + css + ' width:7.5%; text-align:center;">' + result.data[i]
                            .issue_storage_location + '</td>';
                        tableData += '<td style="' + css + ' width:15%; text-align:center;">' + result.data[i].hpl +
                            '</td>';
                        tableData += '<td style="' + css + ' width:7.5%; text-align:right;">' + result.data[i].qty +
                            '</td>';
                        tableData += '<td style="' + css + ' width:7.5%; text-align:right;">' + result.data[i].act +
                            '</td>';
                        tableData += '<td style="' + css + ' width:7.5%; text-align:right; ' + bg_color + '">' +
                            result.data[i].diff + '</td>';
                        tableData += '</tr>';
                    }

                    $('#bodyDetail').append(tableData);
                    $('#tableDetail').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [-1],
                            ['Show all']
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
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': false,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });

                    $("#loading").hide();
                }

            });
        }


        function adjusment() {

            var month = $('#adjustment_month').val();
            var hpl = $('#adjusment_hpl').val();

            var data = {
                month: month,
                hpl: hpl,
            }

            if (hpl.length < 1 || month == '') {
                openErrorGritter("Error", "Select Month & Work Center");
                return false;
            }

            $("#loading").show();
            $.get('{{ url('fetch/adjusment_production_schedule_kd') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();

                    $('#adjustment_month').val('');
                    $('#adjusment_hpl').val('');
                    $("#adjusment_hpl").trigger("change");

                    $('#adjustmentModal').modal('hide');

                    openSuccessGritter('Success', 'Adjusment Success');
                } else {
                    $("#loading").hide();
                    openErrorGritter('Error', 'Adjusment Failed');
                }
            });
        }

        function fillAllTable() {
            var month = $('#month').val();
            var hpl = $('#hpl').val();


            fillTableSchedule(month, hpl);
            fillTableGenerate(month, hpl);
            // fillTableShipment(month, hpl);
        }

        function fillTableSchedule(month, hpl) {
            var data = {
                month: month,
                hpl: hpl
            }

            $('#loading').show();

            $.get('{{ url('fetch/view_production_schedule_kd') }}', data, function(result, status, xhr) {

                $('#month_prod').text(result.month);
                $('#tableProd').DataTable().clear();
                $('#tableProd').DataTable().destroy();
                $('#headProdSch').html("");
                var ps = [];
                var tableHead = '<tr>';
                tableHead +=
                    '<th style="background-color:#605ca8; color: white; vertical-align: middle; text-align: center;">GMC</th>';
                tableHead +=
                    '<th style="background-color:#605ca8; color: white; vertical-align: middle; text-align: center;">DESC</th>';
                tableHead +=
                    '<th style="background-color:#605ca8; color: white; vertical-align: middle; text-align: center;">WORK CENTER</th>';
                tableHead +=
                    '<th style="background-color:#605ca8; color: white; vertical-align: middle; text-align: center;">LOT</th>';

                for (var i = 0; i < result.dates.length; i++) {
                    if (result.dates[i].remark == 'H') {
                        tableHead +=
                            '<th style="vertical-align: middle; text-align: center; background-color: #ff6969; padding: 0px; width: 20px;">';
                        tableHead += result.dates[i].week_date.slice(8) + '</th>';
                    } else {
                        tableHead +=
                            '<th style="vertical-align: middle; text-align: center; background-color: #00cc6e; padding: 0px; width: 20px;">';
                        tableHead += result.dates[i].week_date.slice(8) + '</th>';
                    }

                    ps.push({
                        'date': result.dates[i].week_date,
                        'quantity': 0
                    });

                }
                tableHead +=
                    '<th style="background-color:#fffcb7; vertical-align: middle; text-align: center;">TOTAL</th>';
                tableHead += '</tr>';
                $('#headProdSch').append(tableHead);


                $('#bodyProdSch').html("");
                var tableBody = '';

                for (var i = 0; i < result.materials.length; i++) {
                    tableBody += '<tr>';
                    tableBody += '<td style="vertical-align: middle; text-align: center;">' + result.materials[i]
                        .material_number + '</td>';
                    tableBody += '<td style="vertical-align: middle; text-align: left; width: 40%;">' + result
                        .materials[i].material_description + '</td>';
                    tableBody += '<td style="vertical-align: middle; text-align: left;">' + result.materials[i]
                        .hpl + '</td>';
                    tableBody += '<td style="vertical-align: middle; text-align: center;">' + result.materials[i]
                        .lot_completion + '</td>';

                    var sum_row = 0;

                    for (var j = 0; j < result.dates.length; j++) {
                        var inserted = false;

                        var bg_color = '';
                        if (result.dates[j].remark == 'H') {
                            bg_color += 'background-color: gainsboro;';
                        }

                        for (var k = 0; k < result.prod_schedules.length; k++) {
                            if ((result.prod_schedules[k].material_number == result.materials[i]
                                    .material_number) && (result.prod_schedules[k].due_date == result.dates[j]
                                    .week_date)) {

                                if ((result.prod_schedules[k].quantity % result.materials[i].lot_completion) == 0) {
                                    tableBody += '<th style="text-align: center; vertical-align: middle; ' +
                                        bg_color + '">' +
                                        result.prod_schedules[k].quantity + '</th>';

                                } else {
                                    tableBody +=
                                        '<td style="text-align: center; vertical-align: middle; background-color: rgb(255, 204, 255);">' +
                                        result.prod_schedules[k].quantity + '</td>';
                                }

                                sum_row += result.prod_schedules[k].quantity;
                                inserted = true;

                            }
                        }

                        if (!inserted) {
                            tableBody +=
                                '<th style="text-align: center; vertical-align: middle; ' +
                                bg_color +
                                '">0</th>';
                        }
                    }
                    tableBody +=
                        '<th style="background-color:#fffcb7; text-align: right; vertical-align: middle;">' +
                        sum_row + '</th>';
                    tableBody += '</tr>';
                }
                $('#bodyProdSch').append(tableBody);

                $('#tableProd').DataTable({
                    'dom': 'Bfrtip',
                    'responsive': true,
                    'lengthMenu': [
                        [-1],
                        ['Show all']
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
                    'paging': true,
                    'lengthChange': true,
                    'searching': true,
                    'ordering': false,
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true

                });

                $('#loading').hide();
            });
        }

        function fillTableGenerate(month, hpl) {

            var data = {
                month: month,
                hpl: hpl,
            }

            $('#loading').show();

            $.get('{{ url('fetch/view_generate_production_schedule_kd') }}', data, function(result, status, xhr) {

                $('#month_pack').text(result.month);
                $('#tablePack').DataTable().clear();
                $('#tablePack').DataTable().destroy();
                $('#headLotSch').html("");
                var ps = [];
                var tableHead = '<tr>';
                tableHead +=
                    '<th style="background-color:#605ca8; color: white; vertical-align: middle; text-align: center;">GMC</th>';
                tableHead +=
                    '<th style="background-color:#605ca8; color: white; vertical-align: middle; text-align: center; width: 30%;">DESC</th>';
                tableHead +=
                    '<th style="background-color:#605ca8; color: white; vertical-align: middle; text-align: center;">WORK CENTER</th>';
                tableHead +=
                    '<th style="background-color:#605ca8; color: white; vertical-align: middle; text-align: center;">LOT</th>';

                for (var i = 0; i < result.dates.length; i++) {
                    if (result.dates[i].remark == 'H') {
                        tableHead +=
                            '<th style="vertical-align: middle; text-align: center; background-color: #ff6969; padding: 0px; width: 20px;">';
                        tableHead += result.dates[i].week_date.slice(8) + '</th>';
                    } else {
                        tableHead +=
                            '<th style="vertical-align: middle; text-align: center; background-color: #00cc6e; padding: 0px; width: 20px;">';
                        tableHead += result.dates[i].week_date.slice(8) + '</th>';
                    }
                    ps.push({
                        'date': result.dates[i].week_date,
                        'quantity': 0
                    });
                }

                tableHead +=
                    '<th style="background-color:#fffcb7; vertical-align: middle; text-align: center;">PLAN PACKING</th>';
                tableHead +=
                    '<th style="background-color:#fffcb7; vertical-align: middle; text-align: center;">TOTAL SCHEDULE</th>';
                tableHead +=
                    '<th style="background-color:#fffcb7; vertical-align: middle; text-align: center;">Diff</th>';
                tableHead += '</tr>';
                $('#headLotSch').append(tableHead);


                $('#bodyLotSch').html("");
                var tableBody = '';

                for (var i = 0; i < result.materials.length; i++) {
                    tableBody += '<tr>';
                    tableBody += '<td style="vertical-align: middle; text-align: center;">' + result.materials[i]
                        .material_number + '</td>';
                    tableBody += '<td style="vertical-align: middle; text-align: left;">' + result.materials[i]
                        .material_description + '</td>';
                    tableBody += '<td style="vertical-align: middle; text-align: left;">' + result.materials[i]
                        .hpl + '</td>';
                    tableBody += '<td style="vertical-align: middle; text-align: center;">' + result.materials[i]
                        .lot_completion + '</td>';

                    var sum_row = 0;
                    var diff = 0;

                    for (var j = 0; j < result.dates.length; j++) {
                        var inserted = false;
                        var bg_color = '';
                        if (result.dates[j].remark == 'H') {
                            bg_color += 'background-color: gainsboro;';
                        }

                        for (var k = 0; k < result.prod_schedules.length; k++) {
                            if ((result.prod_schedules[k].material_number == result.materials[i]
                                    .material_number) && (result.prod_schedules[k].due_date == result.dates[j]
                                    .week_date)) {

                                if ((result.prod_schedules[k].quantity % result.materials[i].lot_completion) ==
                                    0) {
                                    tableBody += '<th style="text-align: center; vertical-align: middle; ' +
                                        bg_color + '">' +
                                        result.prod_schedules[k].quantity + '</th>';
                                } else {
                                    tableBody +=
                                        '<td style="text-align: center; vertical-align: middle; background-color: rgb(255, 204, 255);">' +
                                        result.prod_schedules[k].quantity + '</td>';
                                }

                                sum_row += result.prod_schedules[k].quantity;
                                inserted = true;

                            }
                        }

                        if (!inserted) {
                            tableBody += '<th style="text-align: center; vertical-align: middle;' + bg_color +
                                '">0</th>';
                        }
                    }
                    tableBody +=
                        '<th style="text-align: right; vertical-align: middle;">' +
                        sum_row + '</th>';


                    for (var z = 0; z < result.sum_step_one.length; z++) {
                        if (result.sum_step_one[z].material_number == result.materials[i].material_number) {
                            tableBody +=
                                '<th style="text-align: right; vertical-align: middle;">' +
                                result.sum_step_one[z].quantity + '</th>';
                            diff = sum_row - result.sum_step_one[z].quantity;
                        }
                    }

                    if (diff == 0) {
                        tableBody +=
                            '<th style="text-align: right; vertical-align: middle;">' +
                            diff + '</th>';
                    } else {
                        tableBody +=
                            '<td style="text-align: center; vertical-align: middle;">' +
                            diff + '</td>';
                    }
                    tableBody += '</tr>';
                }

                $('#bodyLotSch').append(tableBody);

                $('#tablePack').DataTable({
                    'dom': 'Bfrtip',
                    'responsive': true,
                    'lengthMenu': [
                        [-1],
                        ['Show all']
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
                    'paging': true,
                    'lengthChange': true,
                    'searching': true,
                    'ordering': false,
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true

                });
                $('#loading').hide();

            });
        }

        function fillTableShipment(month, hpl) {

            var data = {
                month: month,
                hpl: hpl
            }

            $('#loading').show();

            $.get('{{ url('fetch/view_generate_shipment_schedule_kd') }}', data, function(result, status, xhr) {

                $('#month_ship').text(result.month);
                $('#tableShip').DataTable().clear();
                $('#tableShip').DataTable().destroy();
                $('#headShipSch').html("");
                var ps = [];
                var tableHead = '<tr style=>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">DESC</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">WORK CENTER</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">DESTINATION</th>';

                for (var i = 0; i < result.dates.length; i++) {
                    tableHead += '<th style="vertical-align: middle; text-align: center;">' + result.dates[i]
                        .week_date.slice(8) + '</th>';
                    ps.push({
                        'date': result.dates[i].week_date,
                        'quantity': 0
                    });
                }

                tableHead += '<th style="vertical-align: middle; text-align: center;">PLAN SHIPMENT</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">REQUEST</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">DIFF</th>';
                tableHead += '</tr>';
                $('#headShipSch').append(tableHead);


                $('#bodyShipSch').html("");
                var tableBody = '';

                for (var i = 0; i < result.materials.length; i++) {
                    tableBody += '<tr>';
                    tableBody += '<td style="vertical-align: middle; text-align: center;">' + result.materials[i]
                        .material_number + '</td>';
                    tableBody += '<td style="vertical-align: middle; text-align: left;">' + result.materials[i]
                        .material_description + '</td>';
                    tableBody += '<td style="vertical-align: middle; text-align: left;">' + result.materials[i]
                        .hpl + '</td>';
                    tableBody += '<td style="vertical-align: middle; text-align: center;">' + (result.materials[i]
                        .destination_shortname || '-') + '</td>';

                    var sum_row = 0;
                    var diff = 0;

                    for (var j = 0; j < result.dates.length; j++) {
                        var inserted = false;
                        if (result.dates[j].remark == 'H') {
                            tableBody += '<th style="background-color: gainsboro;"></th>';
                            inserted = true;
                        } else {
                            for (var k = 0; k < result.shipments.length; k++) {

                                if ((result.shipments[k].destination_shortname == result.materials[i]
                                        .destination_shortname) && (result.shipments[k].material_number == result
                                        .materials[i].material_number) && (result.shipments[k].st_date == result
                                        .dates[j].week_date)) {
                                    tableBody +=
                                        '<th style="text-align: center; vertical-align: middle; background-color: rgb(204, 255, 255);">' +
                                        result.shipments[k].quantity + '</th>';

                                    sum_row += result.shipments[k].quantity;
                                    inserted = true;
                                }
                            }
                        }
                        if (!inserted) {
                            tableBody += '<th style="text-align: center; vertical-align: middle;">0</th>';
                        }

                    }
                    tableBody += '<th style="text-align: right; vertical-align: middle;">' + sum_row + '</th>';

                    var diff_inserted = false;
                    for (var z = 0; z < result.requests.length; z++) {
                        if ((result.requests[z].destination_shortname == result.materials[i]
                                .destination_shortname) && (result.requests[z].material_number == result.materials[
                                    i]
                                .material_number)) {
                            tableBody += '<th style="text-align: right; vertical-align: middle;">' + result
                                .requests[z].quantity + '</th>';
                            diff = sum_row - result.requests[z].quantity;
                            diff_inserted = true;
                        }
                    }

                    if (!diff_inserted) {
                        tableBody += '<th style="text-align: center; vertical-align: middle;">0</th>';

                    }

                    if (diff == 0) {
                        tableBody += '<th style="text-align: right; vertical-align: middle;">' + diff + '</th>';
                    } else {
                        tableBody +=
                            '<td style="text-align: center; vertical-align: middle; background-color: rgb(255, 204, 255);">' +
                            diff + '</td>';
                    }


                    tableBody += '</tr>';
                }

                $('#bodyShipSch').append(tableBody);

                $('#tableShip').DataTable({
                    'dom': 'Bfrtip',
                    'responsive': true,
                    'lengthMenu': [
                        [-1],
                        ['Show all']
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
                    'paging': true,
                    'lengthChange': true,
                    'searching': true,
                    'ordering': false,
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true

                });

                $('#loading').hide();

            });
        }

        function deleteProd() {
            var month = $('#delete_month').val();
            var hpl = $('#delete_hpl').val();

            var data = {
                month: month,
                hpl: hpl,
            }

            if (hpl.length < 1 || month == '') {
                openErrorGritter("Error", "Select Month & Work Center");
                return false;
            }

            $('#loading').show();

            $.post('{{ url('delete/production_schedule_kd') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#delete_month').val('');
                    $("#delete_hpl").val("");
                    $("#delete_hpl").trigger("change");

                    $('#deleteModal').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter("Success", result.message);

                } else {
                    $('#loading').hide();
                    openErrorGritter("Error", result.message);
                }
            });
        }

        function generate() {

            var month = $('#generate_month').val();
            var hpl = $('#generate_hpl').val();

            var data = {
                month: month,
                hpl: hpl,
            }

            if (hpl.length < 1 || month == '') {
                openErrorGritter("Error", "Select Month & Work Center");
                return false;
            }

            $('#loading').show();

            $.post('{{ url('fetch/generate_production_schedule_kd') }}', data, function(result, status, xhr) {
                if (result.status) {

                    fillTableGenerate(month);

                    $('#generate_month').val('');
                    $("#generate_hpl").val("");
                    $("#generate_hpl").trigger("change");

                    $('#generateModal').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter("Success", "Generate Packing schedule success");

                } else {
                    $('#loading').hide();
                    openErrorGritter("Error", result.message);
                }
            });
        }

        function shipment() {
            var month = $('#shipment_month').val();
            var hpl = $('#shipment_hpl').val();

            var data = {
                month: month,
                hpl: hpl,
            }

            if (hpl.length < 1 || month == '') {
                openErrorGritter("Error", "Select Month & Work Center");
                return false;
            }
            $('#loading').show();

            $.post('{{ url('fetch/generate_shipment_schedule_kd') }}', data, function(result, status, xhr) {
                if (result.status) {

                    fillTableShipment(month);

                    $('#shipment_month').val('');
                    $("#shipment_hpl").val("");
                    $("#shipment_hpl").trigger("change");

                    $('#shipmentModal').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter("Success", "Generate shipment schedule success");

                } else {
                    $('#loading').hide();
                    openErrorGritter("Error", "Generate shipment schedule failed");
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
                    "url": "{{ url('fetch/production_schedule_kd') }}"
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
                        "data": "actual_quantity"
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
                        that.search(this.value).draw();
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

            $.post('{{ url('delete/production_schedule_kd') }}', data, function(result, status, xhr) {
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
                $("#actual_edit").val(result.datas.actual_quantity);
            })
        }

        function edit() {
            var data = {
                id: $("#id_edit").val(),
                quantity: $("#quantity_edit").val()
            };

            $.post('{{ url('edit/production_schedule_kd') }}', data, function(result, status, xhr) {
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
                time: '7000'
            });
        }
    </script>

@stop
