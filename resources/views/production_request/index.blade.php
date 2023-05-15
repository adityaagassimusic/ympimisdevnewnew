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
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 0;
            padding-bottom: 0;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
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
            {{ $page }}
        </h1>
        <ol class="breadcrumb">
            <li>
                <a data-toggle="modal" data-target="#infoModal" class="btn btn-info btn-md"
                    style="color:white">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i
                        class="fa fa-info"></i>&nbsp;Destination&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                <a data-toggle="modal" data-target="#uploadModal" class="btn btn-success btn-md" style="color:white"><i
                        class="fa fa-upload"></i>Upload Request</a>
            </li>
        </ol>
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
            <div class="col-xs-12" style="padding: 0px;">
                <div class="col-xs-2" style="padding-right: 0px;">
                    <select class="form-control select3" id='category' id='category' data-placeholder="Select Category"
                        style="width: 100%;">
                        <option value=""></option>
                        <option value="REGULER">REGULER</option>
                        <option value="EXTRA ORDER">EXTRA ORDER</option>
                    </select>
                </div>
                <div class="col-xs-2" style="padding-right: 0px;">
                    <div class="input-group date pull-right" style="text-align: center;">
                        <div class="input-group-addon bg-green">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control monthpicker" name="filter_start" id="filter_start"
                            placeholder="Select Start Month">
                    </div>
                </div>
                <div class="col-xs-2" style="padding-right: 0px;">
                    <div class="input-group date pull-right" style="text-align: center;">
                        <div class="input-group-addon bg-green">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control monthpicker" name="filter_end" id="filter_end"
                            placeholder="Select End Month">
                    </div>
                </div>
                <div class="col-xs-2" style="padding-right: 0px;">
                    <button onclick="fetchTable()" class="btn btn-primary">Search</button>
                </div>
            </div>
            <div class="col-xs-12" id="last_update" style="color: black;"></div>
            <div class="col-xs-12">
                <div class="nav-tabs-custom" style="margin-top: 0%;">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Request</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                            <table id="tableQty" class="table table-bordered table-hover" style="width: 100%;">
                                <thead id="headQty"
                                    style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                    <th></th>
                                </thead>
                                <tbody id="bodyQty">
                                    <td></td>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Upload Request</h4>
                    Format : [<b><i>Month</i></b>][<b><i>Categoy</i></b>][<b><i>Material
                            Number</i></b>][<b><i>Destination</i></b>][<b><i>Quantity</i></b>]
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1">
                            <div class="col-xs-12">
                                <label>Request Period :<span class="text-red">*</span></label>
                            </div>
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <input type="text" class="form-control" name="period" id="period"
                                    placeholder="Enter Request Period (Ex : 2108)">
                            </div>
                            <div class="col-xs-12" style="margin-top: 2%;">
                                <label>Select Month :<span class="text-red">*</span></label>
                            </div>
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <div class="input-group date pull-right" style="text-align: center;">
                                    <div class="input-group-addon bg-green">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control monthpicker" name="start" id="start"
                                        placeholder="Start Month">
                                </div>
                            </div>
                            <div class="col-xs-6" style="padding-left: 5px;">
                                <div class="input-group date pull-right" style="text-align: center;">
                                    <div class="input-group-addon bg-green">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control monthpicker" name="end" id="end"
                                        placeholder="End Month">
                                </div>
                            </div>
                            <div class="col-xs-12" style="margin-top: 2%;">
                                <label>Request Data :<span class="text-red">*</span></label>
                            </div>
                            <div class="col-xs-12">
                                <textarea id="upload" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="uploadRequest()" class="btn btn-success">Upload </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Destination Information :</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1">
                            <table class="table table-hover table-bordered table-striped" id="tableList"
                                style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 30%;">Destination</th>
                                        <th style="width: 70%;">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($destination as $dt)
                                        <tr>
                                            <td>{{ $dt->destination_shortname }}</td>
                                            <td>{{ $dt->destination_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
            $('body').toggleClass("sidebar-collapse");

            $('.select3').select2({
                allowClear: true
            });

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

            fetchTable();

        });


        function uploadRequest() {

            var period = $('#period').val();
            var start = $('#start').val();
            var end = $('#end').val();
            var upload = $('#upload').val();

            if (period == '' || start == '' || end == '' || upload == '') {
                openErrorGritter('Error', 'All data must be complete');
                return false;
            }

            var data = {
                period: period,
                start: start,
                end: end,
                upload: upload,
            }

            $('#loading').show();
            $.post('{{ url('upload/production_request') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#period').val('');
                    $('#start').val('');
                    $('#end').val('');
                    $('#upload').val('');

                    $('#uploadModal').modal('hide');

                    $('#loading').hide();
                    openSuccessGritter('Success', 'Upload Request Success');

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error', result.message);
                }

            });
        }

        function fetchTable() {
            var category = $('#category').val();
            var start = $('#filter_start').val();
            var end = $('#filter_end').val();

            var data = {
                category: category,
                start: start,
                end: end
            }

            $('#loading').show();
            $.get('{{ url('fetch/production_request') }}', data, function(result, status, xhr) {
                if (result.status) {

                    if (result.request.length > 0) {
                        $('#last_update').html(
                            '<p class="pull-right" style="margin: 0px; font-size: 10pt;"><i class="fa fa-fw fa-clock-o"></i> Last Updated: ' +
                            result.request[0].created_at + '</p>');
                    } else {
                        $('#last_update').html(
                            '<p class="pull-right" style="margin: 0px; font-size: 10pt;"><i class="fa fa-fw fa-clock-o"></i> Last Updated: - </p>'
                        );
                    }


                    $('#tableQty').DataTable().clear();
                    $('#tableQty').DataTable().destroy();
                    $('#headQty').html("");
                    var headQty = '<tr>';
                    headQty += '<th style="vertical-align: middle; text-align: center;">Categoy</th>';
                    headQty += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                    headQty += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                    headQty += '<th style="vertical-align: middle; text-align: center;">Destination</th>';
                    for (var i = 0; i < result.interval.length; i++) {
                        headQty +=
                            '<th style="vertical-align: middle; text-align: center; background-color: #c475ff; color:white;">' +
                            result.interval[i].text_month.substr(0, 3) + '-' +
                            result.interval[i].text_year.substr(2, 2) + '<br>' +
                            '(' + result.interval[i].remark.substr(0, 4) + ')' +
                            '</th>';
                    }
                    headQty += '</tr>';
                    $('#headQty').append(headQty);


                    $('#bodyQty').html("");

                    var bodyQty = '';
                    for (var i = 0; i < result.material.length; i++) {
                        bodyQty += '<tr>';
                        var color = '';
                        if (result.material[i].category == 'EXTRA ORDER') {
                            color = 'background-color: #fcc33d';
                        }
                        bodyQty += '<td style="vertical-align: middle; text-align: center; ' + color + '">' + result
                            .material[i].category + '</td>';
                        bodyQty += '<td style="vertical-align: middle; text-align: center;">' + result.material[i]
                            .material_number + '</td>';
                        bodyQty += '<td style="vertical-align: middle; text-align: left;">' + result.material[i]
                            .material_description + '</td>';
                        bodyQty += '<td style="vertical-align: middle; text-align: center;">' + result.material[i]
                            .destination_shortname + '</td>';

                        for (var j = 0; j < result.interval.length; j++) {
                            var inserted = false;

                            for (var k = 0; k < result.request.length; k++) {
                                if ((result.request[k].category == result.material[i].category) && (result.request[
                                        k].material_number == result.material[i].material_number) && (result
                                        .request[k].destination_shortname == result.material[i]
                                        .destination_shortname) && (result.request[k].month == result.interval[j]
                                        .month)) {
                                    bodyQty += '<td style="vertical-align: middle; text-align: center;">' + result
                                        .request[k].quantity + '</td>';
                                    inserted = true;
                                }
                            }

                            if (!inserted) {
                                bodyQty += '<td style="vertical-align: middle; text-align: center;">0</td>';
                            }
                        }
                        bodyQty += '</tr>';
                    }
                    $('#bodyQty').append(bodyQty);


                    $('#tableQty').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'excel',
                                className: 'btn btn-info',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'print',
                                className: 'btn btn-warning',
                                text: '<i class="fa fa-print"></i> Print',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        "columnDefs": [{
                            "targets": [1],
                            "className": "text-left"
                        }],
                        "ordering": false,
                        "ordering": false,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });

                    $('#loading').hide();

                }

            });

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
