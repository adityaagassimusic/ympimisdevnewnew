@php
    function callName($name)
    {
        $new_name = '';
        $blok_m = ['M.', 'Moch.', 'Mochammad', 'Moh.', 'Mohamad', 'Mokhamad', 'Much.', 'Muchammad', 'Muhamad', 'Muhammaad', 'Muhammad', 'Mukammad', 'Mukhamad', 'Mukhammad'];
    
        if (strlen($name) > 0) {
            if (str_contains($name, ' ')) {
                $name = explode(' ', $name);
                if (in_array($name[0], $blok_m)) {
                    $new_name = 'M.';
                    for ($i = 1; $i < count($name); $i++) {
                        if ($i == 1) {
                            $new_name .= ' ';
                            $new_name .= $name[$i];
                        } else {
                            $new_name .= ' ';
                            $new_name .= substr($name[$i], 0, 1) . '.';
                        }
                    }
                } else {
                    for ($i = 0; $i < count($name); $i++) {
                        if ($i == 0) {
                            $new_name .= ' ';
                            $new_name .= $name[$i];
                        } else {
                            $new_name .= ' ';
                            $new_name .= substr($name[$i], 0, 1) . '.';
                        }
                    }
                }
            } else {
                $new_name = $name;
            }
        } else {
            $new_name = '-';
        }
    
        return $new_name;
    }
@endphp
@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <style type="text/css">
        #tableDetail>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        tbody>tr>td {
            padding: 10px 5px 10px 5px;
        }

        table.table-bordered {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            vertical-align: middle;
            height: 40px;
            padding: 2px 5px 2px 5px;
        }

        .contr #loading {
            display: none;
        }

        .label-status {
            color: black;
            font-size: 12px;
            border-radius: 4px;
            padding: 1px 5px 2px 5px;
            border: 1px solid black;
            min-width: 120px;
            height: 35px;
            vertical-align: middle;
        }

        .label-file {
            color: black;
            font-size: 12px;
            border-radius: 4px;
            padding: 1px 5px 2px 5px;
            border: 1px solid black;
            min-width: 120px;
            height: 20px;
            vertical-align: middle;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">

        <ol class="breadcrumb">
            @if (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'PCH'))
                <li>
                    <a data-toggle="modal" data-target="#modal_update_delivery" class="btn btn-default btn-social"
                        style="border: 1.5px solid orange; background-color: #ffc312; font-size: 12px; border-radius: 10px; width: 100%; margin-bottom: 2%; vertical-align: middle;">
                        <i style="font-size: 18px; margin-top: 3%; margin-bottom: 3%;" class="fa fa-calendar-check-o"></i>
                        <b>Update Delivery Plan</b><br>
                        <i>Breakdown Daily</i>
                    </a>
                </li>
                <li>
                    <a data-toggle="modal" data-target="#modal_delivery" class="btn btn-success btn-social"
                        style="border: 1.5px solid green; background-color:#00d974; font-size: 12px; border-radius: 10px; width: 100%; margin-bottom: 2%; vertical-align: middle;">
                        <i style="font-size: 18px; margin-top: 4%; margin-bottom: 4%;" class="fa fa-plus"></i>
                        <b>Delivery Plan</b><br>
                        <i>Upload PO</i>
                    </a>
                </li>
            @endif

        </ol>
    </section>
@endsection

@section('content')
    <section class="content" style="font-size: 0.8vw;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row" style="margin-top: 4.5%;">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-body">
                        <div class="col-xs-4" style="padding: 0px;">
                            <div id="pie" style="width: 100%;"></div>
                        </div>

                        <div class="col-xs-8" style="padding: 0px;">
                            <div class="col-xs-4" style="padding: 0px;">
                                <div class="box box-primary box-solid" style="margin: 0px;">
                                    <div class="box-body">
                                        <div class="col-xs-6" style="padding-left: 0px; padding-right: 2px;">
                                            <div class="form-group">
                                                <label>Issue From</label>
                                                <div class="input-group date" style="width: 100%;">
                                                    <input type="text" placeholder="Select IssueDate"
                                                        class="form-control datepicker pull-right" id="issue_from"
                                                        style="font-size: 0.8vw;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-6" style="padding-left: 2px; padding-right: 0px;">
                                            <div class="form-group">
                                                <label>Issue To</label>
                                                <div class="input-group date" style="width: 100%;">
                                                    <input type="text" placeholder="Select IssueDate"
                                                        class="form-control datepicker pull-right" id="issue_to"
                                                        style="font-size: 0.8vw;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-6" style="padding-left: 0px; padding-right: 2px;">
                                            <div class="form-group">
                                                <label>ETA From</label>
                                                <div class="input-group date" style="width: 100%;">
                                                    <input type="text" placeholder="Select ETA Date"
                                                        class="form-control datepicker pull-right" id="eta_from"
                                                        style="font-size: 0.8vw;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-6" style="padding-left: 2px; padding-right: 0px;">
                                            <div class="form-group">
                                                <label>ETA To</label>
                                                <div class="input-group date" style="width: 100%;">
                                                    <input type="text" placeholder="Select ETA Date"
                                                        class="form-control datepicker pull-right" id="eta_to"
                                                        style="font-size: 0.8vw;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-8" style="padding-right: 0px;">
                                <div class="box box-primary box-solid" style="margin: 0px;">
                                    <div class="box-body">
                                        <div class="col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-12" style="padding-left: 0px; padding-right: 2px;">
                                                    <div class="form-group">
                                                        <label>PIC Control</label>
                                                        <select class="form-control select2" multiple="multiple"
                                                            data-placeholder="Select PIC" id="control" style="width:100%">

                                                            <option value=""></option>
                                                            @foreach ($controls as $control)
                                                                <option value="{{ $control->control }}">
                                                                    {{ callName($control->name) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-xs-12" style="padding-left: 2px; padding-right: 0px;">
                                                    <div class="form-group">
                                                        <label>Vendor</label>
                                                        <select class="form-control select2" multiple="multiple"
                                                            data-placeholder="Select Vendor" id="vendor"
                                                            style="width: 100%;">
                                                            <option value=""></option>
                                                            @foreach ($vendors as $vendor)
                                                                <option value="{{ $vendor->vendor_code }}">
                                                                    {{ $vendor->vendor_code }} - {{ $vendor->vendor_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-top: 0.75%;">
                                <div class="form-group pull-right" style="margin: 0px;">
                                    <button onClick="clearConfirmation()"
                                        class="btn btn-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Clear&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                    <button onClick="showTable()" class="btn btn-primary"><span
                                            class="fa fa-search"></span> Search</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12">
                <table class="table table-bordered table-striped table-hover" id="tableDetail" width="100%">
                    <thead style="background-color: #605ca8; color: white;">
                        <tr>
                            <th style="text-align: center;">PIC</th>
                            <th style="text-align: center;">GMC</th>
                            <th style="text-align: center;">Description</th>
                            <th style="text-align: center;">Vendor</th>
                            <th style="text-align: center;">Vendor Name</th>
                            <th style="text-align: center;">PO Number</th>
                            <th style="text-align: center;">Item Line</th>
                            <th style="text-align: center;">Quantity</th>
                            <th style="text-align: center;">ETA YMPI</th>
                            <th style="text-align: center;">Issue Date</th>
                            <th style="text-align: center;">PO Sent</th>
                            <th style="text-align: center;">PO Confirmed</th>
                            {{-- <th style="text-align: center;">Delivery Plan</th> --}}
                            {{-- <th style="text-align: center;">Doc. Completion Deliv.</th> --}}
                            {{-- <th style="text-align: center;">Doc. BC</th> --}}
                        </tr>
                    </thead>
                    <tbody id="bodyDetail">
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            {{-- <td></td> --}}
                            {{-- <td></td> --}}
                            {{-- <td></td> --}}
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </section>

    <div class="modal fade" id="modal_delivery">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Plan Delivery</h4>
                    <span>Format Upload:
                        [<b><i>ISSUE DATE</i></b>]
                        [<b><i>PO NUMBER</i></b>]
                        [<b><i>ITEM LINE</i></b>]
                        [<b><i>VENDOR CODE</i></b>]
                        [<b><i>GMC</i></b>]
                        [<b><i>ETA YMPI</i></b>]
                        [<b><i>QUANTITY</i></b>]
                    </span>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                        <div class="form-group">
                            <textarea id="upload_delivery" style="height: 100px; width: 100%;"></textarea>
                        </div>
                    </div>
                    <button class="btn btn-success pull-right" onclick="uploadData('delivery');">Upload</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_update_delivery">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Update Plan Delivery</h4>
                    <span>Format Upload:
                        [<b><i>PO NUMBER</i></b>]
                        [<b><i>ITEM LINE</i></b>]
                        [<b><i>VENDOR CODE</i></b>]
                        [<b><i>GMC</i></b>]
                        [<b><i>ETA YMPI</i></b>]
                    </span>
                    [<span style="color: orange; font-weight: bold;"><i>REVISI ETA YMPI</i></span>]
                    [<span style="color: orange; font-weight: bold;"><i>REVISI QUANTITY</i></span>]
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                        <div class="form-group">
                            <textarea id="upload_update_delivery" style="height: 100px; width: 100%;"></textarea>
                        </div>
                    </div>
                    <button class="btn btn-success pull-right" onclick="uploadData('update_delivery');">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadResult" data-keyboard="false" data-backdrop="static" style="overflow-y: auto;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Result</h4>
                </div>
                <div class="modal-body" style="max-height: 400px; overflow-y:auto;">
                    <span style="font-size:1.5vw;">Success: <span id="suceess-count"
                            style="font-style:italic; font-weight:bold; color: green;"></span> Row(s)</span>
                    <span style="font-size:1.5vw;"> ~ Error: <span id="error-count"
                            style="font-style:italic; font-weight:bold; color: red;"></span> Row(s)</span>

                    <table id="tableError" style="border: none;">
                        <tbody id="bodyError">
                            <tr>
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
@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/highcharts-3d.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });

            $('.select2').select2();

            // showTable();
        });

        function clearConfirmation() {
            location.reload(true);
        }

        function uploadData(id) {
            var upload = $('#upload_' + id).val();

            var data = {
                id: id,
                upload: upload,
            }

            if (upload == "") {
                alert('Data upload tidak boleh kosong');
                return false;
            }

            $('#loading').show();
            $.post('{{ url('upload/material/material_monitoring') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#upload_' + id).val('');
                    $('#modal_' + id).modal('hide');

                    $('#suceess-count').text(result.ok_count.length);
                    $('#error-count').text(result.error_count.length);

                    $('#bodyError').html("");
                    var tableData = "";
                    var css = "padding: 0px 5px 0px 5px;";
                    for (var i = 0; i < result.error_count.length; i++) {
                        var error = result.error_count[i].split('_');
                        tableData += '<tr>';
                        tableData += '<td style="' + css + ' width:20%; text-align:left;">Row ' + error[0] +
                            '</td>';
                        tableData += '<td style="' + css + ' width:80%; text-align:left;">: ' + error[1] + '</td>';
                        tableData += '</tr>';
                    }

                    if (result.error_count.length > 0) {
                        $('#bodyError').append(tableData);
                        $('#tableError').show();
                    }

                    $('#uploadResult').modal('show');
                    $('#loading').hide();


                    openSuccessGritter('Success!', result.message);
                } else {
                    $('#loading').hide();
                    alert(result.message);
                }
            });
        }

        function showTable() {

            var issue_from = $('#issue_from').val();
            var issue_to = $('#issue_to').val();
            var eta_from = $('#eta_from').val();
            var eta_to = $('#eta_to').val();
            var control = $('#control').val();
            var vendor = $('#vendor').val();

            var data = {
                issue_from: issue_from,
                issue_to: issue_to,
                eta_from: eta_from,
                eta_to: eta_to,
                control: control,
                vendor: vendor
            }


            $('#loading').show();
            $.get('{{ url('fetch/material/control_delivery') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#loading').show();


                    $('#tableDetail').DataTable().clear();
                    $('#tableDetail').DataTable().destroy();
                    $('#bodyDetail').html("");
                    var tableData = "";

                    var css = "padding:0px 5px 0px 5px; vertical-align: middle;";

                    var confirmed = 0;
                    var unconfirmed = 0;

                    for (var i = 0; i < result.data.length; i++) {
                        tableData += '<tr>';
                        if (result.data[i].name == null) {
                            tableData += '<td style="' + css + ' width:10%; text-align:center;">-</td>';
                        } else {
                            tableData += '<td style="' + css + ' width:10%; text-align:left;">' + callName(result
                                .data[i].name) + '</td>';
                        }
                        tableData += '<td style="' + css + ' width:5%; text-align:center;">' + result.data[i]
                            .material_number + '</td>';
                        tableData += '<td style="' + css + ' width:20%; text-align:left;">' + result.data[i]
                            .material_description + '</td>';
                        tableData += '<td style="' + css + ' width:5%; text-align:center;">' + result.data[i]
                            .vendor_code + '</td>';
                        tableData += '<td style="' + css + ' width:20%; text-align:left;">' + result.data[i]
                            .vendor_name + '</td>';
                        tableData += '<td style="' + css + ' width:5%; text-align:center;">' + result.data[i]
                            .po_number + '</td>';
                        tableData += '<td style="' + css + ' width:5%; text-align:center;">' + (result.data[i]
                            .item_line || '-') + '</td>';
                        tableData += '<td style="' + css + ' width:5%; text-align:right;">' + result.data[i]
                            .quantity + '</td>';
                        tableData += '<td style="' + css + ' width:5%; text-align:center;">' + result.data[i]
                            .eta_date + '</td>';
                        tableData += '<td style="' + css + ' width:5%; text-align:center;">' + (result.data[i]
                            .issue_date || '-') + '</td>';
                        tableData += '<td style="' + css + ' width:10%; text-align:center;' + poSent(result.data[
                            i]) + '</td>';
                        tableData += '<td style="' + css + ' width:10%; text-align:center;' + poConfirm(result.data[
                            i]) + '</td>';
                        // tableData += '<td style="' + css + ' width:10%; text-align:center;' + deliveryPlan(result
                        //     .data[i]) + '</td>';
                        // tableData += '<td style="' + css + ' width:10%; text-align:center;' + doNumberFormatter(
                        //     result.data[i]) + '</td>';
                        // tableData += '<td style="' + css + ' width:10%; text-align:center;' + bcFormatter(result
                        //     .data[i]) + '</td>';
                        tableData += '</tr>';

                        if (result.data[i].po_confirm == 1) {
                            confirmed++;
                        } else {
                            unconfirmed++;
                        }
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

                    Highcharts.chart('pie', {
                        chart: {
                            height: 300,
                            backgroundColor: null,
                            type: 'pie',
                            options3d: {
                                enabled: true,
                                alpha: 45,
                                beta: 0
                            },
                        },
                        title: {
                            text: ''
                        },
                        accessibility: {
                            point: {
                                valueSuffix: '%'
                            }
                        },
                        legend: {
                            enabled: false,
                            symbolRadius: 1,
                            borderWidth: 1
                        },
                        credits: {
                            enabled: false
                        },
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                        },
                        plotOptions: {
                            pie: {
                                allowPointSelect: true,
                                cursor: 'pointer',
                                edgeWidth: 1,
                                edgeColor: 'rgb(126,86,134)',
                                depth: 35,
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}<br>{point.y} PO(s)</b><br>{point.percentage:.1f} %',
                                    style: {
                                        fontSize: '0.8vw',
                                        textOutline: 0
                                    },
                                    color: 'black',
                                    connectorWidth: '3px'
                                },
                                showInLegend: true,
                            }
                        },
                        series: [{
                            type: 'pie',
                            data: [{
                                name: 'Confirmed',
                                y: confirmed,
                                color: '#90ee7e'
                            }, {
                                name: 'Unconfirmed',
                                y: unconfirmed,
                                color: '#d32f2f'
                            }]
                        }]
                    });


                    $('#loading').hide();
                }

            });

        }


        function callName(name) {
            var new_name = '';
            var blok_m = [
                'M.',
                'Moch.',
                'Mochammad',
                'Moh.',
                'Mohamad',
                'Mokhamad',
                'Much.',
                'Muchammad',
                'Muhamad',
                'Muhammaad',
                'Muhammad',
                'Mukammad',
                'Mukhamad',
                'Mukhammad'
            ];


            if (name != null) {

                if (name.includes(' ')) {
                    name = name.split(' ');

                    if (blok_m.includes(name[0])) {
                        new_name = 'M.';
                        for (i = 1; i < name.length; i++) {
                            if (i == 1) {
                                new_name += ' ';
                                new_name += name[i];
                            } else {
                                new_name += ' ';
                                new_name += name[i].substr(0, 1) + '.';
                            }
                        }
                    } else {
                        for (i = 0; i < name.length; i++) {
                            if (i == 0) {
                                new_name += ' ';
                                new_name += name[i];
                            } else {
                                new_name += ' ';
                                new_name += name[i].substr(0, 1) + '.';
                            }
                        }
                    }

                } else {
                    new_name = name;
                }
            } else {
                new_name = '-';
            }

            return new_name;
        }

        function poSent(row) {
            var message = '">';

            if (row.po_send == 1) {
                message += '<label class="label-status" style="background-color: #aee571; margin: 0px;">';
                message += '<p style="margin: 0px;">SENT</p>';
                message += '<p style="font-size: 10px; margin: 0px;">' + (row.po_send_at || '-') + '</p>';
                message += '</label>';
            } else {
                message += '<label class="label-status" style="background-color: #f25450; margin: 0px;">';
                message += '<p style="margin: 0px; padding-top: 5%;">UNSENT</p>';
                message += '</label>';
            }

            return message;
        }

        function poConfirm(row) {
            var message = '';

            if (row.po_send == 1) {
                if (row.po_confirm == 1) {
                    message += '"><label class="label-status" style="background-color: #aee571; margin: 0px;">';
                    message += '<p style="margin: 0px;">CONFIRMED</p>';
                    message += '<p style="font-size: 10px; margin: 0px;">' + (row.po_confirm_at || '-') + '</p>';
                    message += '</label>';
                } else {
                    message += '"><label class="label-status" style="background-color: #f25450; margin: 0px;">';
                    message += '<p style="margin: 0px; padding-top: 5%;">WAITING</p>';
                    message += '</label>';
                }
            } else {
                message += 'background-color: #d9d9d9;">&nbsp;';
            }

            return message;
        }

        function deliveryPlan(row) {
            var message = '';

            if (row.po_confirm == 1) {
                if (row.status != null) {

                    var bg_color = '';
                    if (row.status == 'ON TIME') {
                        bg_color += '#aee571;';
                    } else if (row.status == 'DELAY') {
                        bg_color += '#f25450;';
                    } else if (row.status == 'ACCELERATE') {
                        bg_color += '#44a0ff;';
                    }

                    message += '"><label class="label-status" style="background-color: ' + bg_color + '; margin: 0px;">';
                    message += '<p style="margin: 0px;">' + row.status + '</p>';
                    message += '<p style="font-size: 10px; margin: 0px;">' + (row.due_date || '-') + '</p>';
                    message += '</label>';
                } else {
                    message += '"><label class="label-status" style="background-color: #f25450; margin: 0px;">';
                    message += '<p style="margin: 0px; padding-top: 5%;">UNCONFIRMED</p>';
                    message += '</label>';
                }
            } else {
                message += 'background-color: #d9d9d9;">&nbsp;';
            }

            return message;
        }

        function bcFormatter(row) {
            var message = '';
            var style = 'style="color: black; font-weight: bold; cursor: pointer;"';
            var bg_color = '';
            if (row.bc_send_at != null) {
                bg_color += '#aee571;';
            } else {
                bg_color += '#f2f2f2;';
            }

            if (row.bc_document != null) {
                message += '"><label class="label-file" style="background-color: ' + bg_color + '; margin: 0px;">';
                message += '<a ' + style + ' onclick="downloadBc(\'' + row.bc_document + '_' + row.vendor_code + '\')">' +
                    row.bc_document + '</a>';
                message += '</label>';
                message += '<br>';
                message += '<label class="label-file" style="background-color: ' + bg_color +
                    '; margin: 0px; margin-top: 2%;">';
                message += '<a ' + style + ' onclick="downloadSppb(\'' + row.sppb + '_' + row.vendor_code + '\')">' + row
                    .sppb + '</a>';
                message += '</label>';
            } else {
                message += 'background-color: #d9d9d9;">&nbsp;';
            }

            return message;
        }

        function doNumberFormatter(row) {
            var message = '';

            var style = 'style="color: black; font-weight: bold; cursor: pointer;"';


            if (row.do_number != null) {
                message += '">';
                message += '<label class="label-file" style="background-color: #f2f2f2; margin: 0px;">';
                message += '<a ' + style + ' onclick="downloadDo(\'' + row.do_number + '\')">' + row.do_number + '</a>';
                message += '</label>';

            } else {
                message += 'background-color: #d9d9d9;">&nbsp;';
            }

            return message;
        }

        function downloadDo(file) {
            window.open("http://10.109.52.1:887/miraimobiledev/public/files/raw_material/delivery_order/" + file + ".pdf");
        }

        function downloadBc(file) {
            window.open('{{ asset('bc') }}' + '/' + file + '.pdf');
        }

        function downloadSppb(file) {
            window.open('{{ asset('sppb') }}' + '/' + file + '.pdf');
        }


        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

        function refreshAll() {
            location.reload(true);
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '5000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '5000'
            });
        }
    </script>
@endsection
