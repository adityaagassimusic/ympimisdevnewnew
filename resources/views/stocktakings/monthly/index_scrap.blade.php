@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="viewport" content="initial-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <style type="text/css">
        thead>tr>th {
            text-align: center;
            overflow: hidden;
        }

        tbody>tr>td {
            text-align: center;
        }

        tfoot>tr>th {
            text-align: center;
        }

        th:hover {
            overflow: visible;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            padding-top: 0;
            padding-bottom: 0;
            vertical-align: middle;
            color: white;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            padding-top: 9px;
            padding-bottom: 9px;
            vertical-align: middle;
            background-color: white;
        }

        thead {
            background-color: rgb(126, 86, 134);
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #loading,
        #error {
            display: none;
        }

        #slip {
            text-align: center;
            font-weight: bold;
            font-size: 35px;
        }

        .input {
            text-align: center;
            font-weight: bold;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
@stop
@section('header')
@endsection
@section('content')
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <center style="padding-top: 350px;">
                <span style="font-size: 50px; color: white">Loading, mohon tunggu..<i
                        class="fa fa-spin fa-refresh"></i></span>
            </center>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-6 col-lg-6" style="padding-left: 5px; padding-right: 5px">
                <div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
                    <div class="box">
                        <div class="box-body">
                            <p id="inputor_name"
                                style="font-size:18px; text-align: center; color: black; padding: 0px; margin: 0px; font-weight: bold; text-transform: uppercase;">
                            </p>

                            <div class="input-group input-group-lg" style="height: 117px">
                                <div class="input-group-addon" id="icon-serial"
                                    style="font-weight: bold; border-color: none; font-size: 18px;">
                                    <i class="fa fa-qrcode"></i>
                                </div>
                                <center>
                                    <input type="text" class="form-control" placeholder="SCAN SLIP SCRAP" id="slip"
                                        style="height: 115px">
                                </center>
                                <div class="input-group-addon" id="icon-serial"
                                    style="font-weight: bold; border-color: none; font-size: 18px;">
                                    <i class="fa fa-barcode"></i>
                                </div>
                            </div>

                            {{-- <div class="col-xs-12" style="margin-top: 3%;">
                                <div class="col-xs-3"></div>
                                <button style="font-weight: bold; padding: 1%; margin: 1%;" class="col-xs-3 btn btn-default"
                                    onclick="refreshData()"><i class="fa fa-refresh"></i>&nbsp;&nbsp;REFRESH</button>
                                <button style="font-weight: bold; padding: 1%; margin: 1%;" class="col-xs-3 btn btn-primary"
                                    onclick="getMiraiStock()"><i class="fa fa-exchange"></i>&nbsp;&nbsp;GET MIRAI
                                    STOCK</button>
                                <div class="col-xs-3"></div>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-6 col-lg-6" style="padding-left: 5px; padding-right: 5px;">
                <div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
                    <div class="box">
                        <div class="box-body">
                            <table id="table_variance" class="table table-bordered table-hover"
                                style="width: 100%; font-size: 16pt;">
                                <thead id="head_variance" style="vertical-align: middle;">
                                    <tr>
                                        <th style="width: 25%; background-color: #169cdc;">Book Qty</th>
                                        <th style="width: 25%; background-color: #169cdc;">Book Amount</th>
                                        <th style="width: 25%; background-color: #a16eac;">PI Qty</th>
                                        <th style="width: 25%; background-color: #a16eac;">PI Amount</th>
                                        <th style="width: 25%; background-color: #fcf8e3; color: black;">Variance</th>
                                    </tr>
                                </thead>
                                <tbody id="body_variance" style="font-size: 18pt;">
                                    <tr>
                                        <td id="sum_ymes_qty"></td>
                                        <td id="sum_ymes_book"></td>
                                        <td id="sum_pi_qty"></td>
                                        <td id="sum_pi_book"></td>
                                        <td id="variance"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="col-xs-4"></div>
                            <button style="font-weight: bold; padding: 1%; margin: 1%;" class="col-xs-4 btn btn-success"
                                onclick="exportResult()"><i class="fa fa-mail-forward"></i>&nbsp;&nbsp;EXPORT
                                RESULT</button>
                            <div class="col-xs-4"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-6 col-lg-6" style="padding-left: 5px; padding-right: 5px;">
                <div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
                    <div class="box">
                        <div class="box-body">
                            <h3 style="font-weight: bold; margin-top: 0px; margin-bottom: 2%;">LIST SCAN SCRAP MIRAI</h3>
                            <table id="table_1" class="table table-bordered table-hover"
                                style="width: 100%; font-size: 10pt;">
                                <thead id="head_1"
                                    style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                    <th></th>
                                </thead>
                                <tbody id="body_1">
                                    <td></td>
                                </tbody>
                                <tfoot id="foot_1">
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xs-12 col-md-6 col-lg-6" style="padding-left: 5px; padding-right: 5px;">
                <div class="col-xs-12" style="padding-right: 0; padding-left: 0; margin-bottom: 2%;">
                    <div class="box">
                        <div class="box-body">
                            <h3 style="font-weight: bold; margin-top: 0px; margin-bottom: 2%;">BOOK vs PI</h3>
                            <table id="table_2" class="table table-bordered table-hover"
                                style="width: 100%; font-size: 10pt;">
                                <thead id="head_2"
                                    style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                    <th></th>
                                </thead>
                                <tbody id="body_2">
                                    <td></td>
                                </tbody>
                                <tfoot id="foot_2">
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <div class="modal fade" id="modalInputor">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body table-responsive no-padding">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Inputor</label>
                            <select class="form-control select2" name="inputor" onchange="inputorInput()" id='inputor'
                                data-placeholder="Select Inputor" style="width: 100%;">
                                <option value="">Select Inputor</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->employee_id }} - {{ $employee->name }}">
                                        {{ $employee->employee_id }} - {{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalInput">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #28f294; font-weight: bold; padding: 1%; margin-top: 0; color: black; border: 1px solid black;">
                            INPUT PI
                        </h2>
                    </center>
                </div>
                <div class="modal-body table-responsive" style="min-height: 100px; padding-bottom: 25px;">
                    <div class="col-xs-12">
                        <table id="table_input" class="table table-bordered table-hover" style="width: 100%;">
                            <thead id="head_input" style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                <tr>
                                    <th>Slip</th>
                                    <th>Material</th>
                                    <th>Description</th>
                                    <th>PI</th>
                                </tr>
                            </thead>
                            <tbody id="body_input">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="col-xs-4"></div>
                        <button style="font-weight: bold; padding: 1%; margin: 1%;" class="col-xs-4 btn btn-success"
                            onclick="submitPi()">SUBMIT</button>
                        <div class="col-xs-4"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <!-- <script src="{{ url('bower_components/jquery/dist/jquery.min.js') }}"></script> -->
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

            $('.select2').select2({
                minimumInputLength: 3,
                allowClear: 'true'
            });

            $('#modalInputor').modal({
                backdrop: 'static',
                keyboard: false
            });

            $('#slip').val("");

        });


        var mpdl = <?php echo json_encode($mpdl); ?>;
        var data = <?php echo json_encode($data); ?>;

        var mirai_pi = '';
        var ymes_pi = '';
        var sum_ymes_qty = 0;
        var sum_ymes_book = 0;
        var sum_pi_qty = 0;
        var sum_pi_book = 0;


        function exportResult() {
            var inputor_name = $("#inputor").val();
            var data = inputor_name.split(' - ');
            var inputor = data[0];

            var data = {
                employee_id: inputor
            }

            $("#loading").show();
            $.post('{{ url('export/stocktaking/mstk_pi') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();
                    openSuccessGritter('Success', result.message);
                } else {
                    $("#loading").hide();
                    openErrorGritter('Error', result.message);
                }
            });
        }

        $('#slip').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                if ($("#slip").val().length >= 5) {
                    scanSlip($("#slip").val());
                    return false;
                } else {
                    openErrorGritter('Error!', 'Slip tidak sesuai.');
                    $("#slip").val("");
                    audio_error.play();
                }
            }
        });

        var delay = (function() {
            var timer = 0;
            return function(callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();

        $("#slip").on("input", function() {
            delay(function() {
                if ($("#slip").val().length < 6) {
                    $("#slip").val("");
                }
            }, 100);
        });

        $('#modalInput').on('hidden.bs.modal', function() {
            $('#slip').val('');
        });

        var count_row = 0;

        function scanSlip(slip) {
            $("#loading").show();
            var inputor_name = $("#inputor").val();

            var data = {
                slip: slip,
                inputor: inputor_name
            }

            $.get('{{ url('fetch/stocktaking/scrap') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();

                    $('#body_input').html("");
                    var tableData = '';

                    count_row = 0;
                    $.each(result.data, function(key, value) {
                        count_row++;

                        tableData += '<tr style="background-color: #f5f5f5;"';
                        tableData += 'id="row_' + count_row + '">';

                        tableData += '<td style="text-align: center; width: 20%;">';
                        tableData += value.slip;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center; width: 10%;">';
                        tableData += value.material_number;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center; width: 50%;">';
                        tableData += value.material_description;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center; width: 20%;">';
                        tableData += '<input style="text-align: center; width: 100%;" ';
                        tableData += 'id="quantity_' + count_row + '" type="number" ';
                        tableData += 'value="' + result.pi + '">';
                        tableData += '</td>';

                        tableData += '</tr>';

                        $('#quantity_' + value.slip).focus();
                    });

                    $('#body_input').append(tableData);
                    $('#modalInput').modal('show');
                    $("#slip").val("");
                    openSuccessGritter('Success', result.message);
                    reloadTable();
                    $("#loading").hide();
                } else {
                    openErrorGritter('Error', result.message);
                    if (confirm(
                            'Slip sudah di input. Apakah anda yakin untuk merevisi PI sudah yang tersimpan? PI yang sudah tersimpan akan dihapus dan diganti dengan hasil penghitungan yang baru'
                        )) {
                        revisePI(slip);
                    } else {
                        $("#slip").val("");
                        $("#slip").focus();
                        $("#loading").hide();
                        openErrorGritter('Error', result.message);
                    }
                }
            });

        }

        function reloadTable() {
            $.get('<?php echo e(url('fetch/data/scrap')); ?>', function(result, status, xhr) {
                if (result.status) {
                    $('#table_1').DataTable().clear();
                    $('#table_1').DataTable().destroy();
                    $('#head_1').html("");
                    var head_1 = '<tr>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Slip</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Loc.</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Std. Price</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">PI</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Amount</th>';
                    head_1 += '</tr>';
                    $('#head_1').append(head_1);

                    $('#foot_1').html("");
                    var foot_1 = '';
                    foot_1 += '<tr>'
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '</tr>';
                    $('#foot_1').append(foot_1);

                    $('#body_1').html("");
                    var body_1 = '';
                    $.each(result.test, function(key, value) {
                        var padding = 'padding-top: 0px; padding-bottom: 0px; ';
                        var material_description = '';
                        var price = 0;

                        for (let i = 0; i < mpdl.length; i++) {
                            if (mpdl[i].material_number == value.gmc) {
                                material_description = mpdl[i].material_description;
                                price = mpdl[i].standard_price;
                                break;
                            }
                        }

                        body_1 += '<tr>';
                        body_1 += '<td style="' + padding +
                            ' width : 10%; text-align: center;">';
                        body_1 += value.gmc;
                        body_1 += '</td>';
                        body_1 += '<td style="' + padding +
                            ' width : 10%; text-align: center;">';
                        body_1 += value.slip;
                        body_1 += '</td>';
                        body_1 += '<td style="' + padding + ' width : 40%; text-align: left;">';
                        body_1 += value.description;
                        body_1 += '</td>';
                        body_1 += '<td style="' + padding + ' width : 10%; text-align: center;">';
                        body_1 += value.location;
                        body_1 += '</td>';
                        body_1 += '<td style="' + padding + ' width : 10%; text-align: right;">';
                        price = price / 1000;
                        body_1 += price.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                            style: "currency",
                            currency: "USD"
                        });
                        body_1 += '</td>';
                        body_1 += '<td style="' + padding + ' width : 10%; text-align: right;">';
                        body_1 += value.pi;
                        body_1 += '</td>';
                        var bg_color = 'background-color : #ffccff;';
                        if (value.pi == 0) {
                            bg_color = 'background-color : #ccffff;';
                        }
                        body_1 += '<td style="' + padding + ' width : 10%; text-align: right;">';
                        var amt_var = Math.abs(value.pi) * price;
                        body_1 += amt_var.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                            style: "currency",
                            currency: "USD"
                        });
                        body_1 += '</td>';

                        body_1 += '</tr>';
                    });
                    $('#body_1').append(body_1);

                    $('#table_1 tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="tb_search" style="text-align: center; color: black; width: 100%; padding: 1%;" type="text" placeholder="Search ' +
                            title + '"/>');
                    });

                    mirai_pi = $('#table_1').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [-1],
                            ['Show all']
                        ],
                        'buttons': {
                            buttons: [{
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

                    mirai_pi.columns().every(function() {
                        var that = this;
                        $('.tb_search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#table_1 tfoot tr').prependTo('#table_1 thead');


                    $('#table_2').DataTable().clear();
                    $('#table_2').DataTable().destroy();
                    $('#head_2').html("");
                    var head_2 = '<tr>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Loc.</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Std. Price</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Book</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">PI</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Diff</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Amt. Var.</th>';
                    head_2 += '</tr>';
                    $('#head_2').append(head_2);

                    $('#foot_2').html("");
                    var foot_2 = '';
                    foot_2 += '<tr>'
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '</tr>';
                    $('#foot_2').append(foot_2);

                    $('#body_2').html("");
                    var body_2 = '';
                    var sum_book = 0;
                    var sum_amt_var = 0;
                    $.each(result.test2, function(key, value) {
                        var padding = 'padding-top: 0px; padding-bottom: 0px;';
                        var price = 0;
                        for (let i = 0; i < mpdl.length; i++) {
                            if (mpdl[i].material_number == value.material_number) {
                                price = mpdl[i].standard_price;
                                break;
                            }
                        }

                        var pii = 0;
                        for (let m = 0; m < data.length; m++) {
                            if (data[m].gmc == value.material_number && value.storage_location == data[m]
                                .location) {
                                pii += parseFloat(data[m].pi);
                            }
                        }
                        pii = pii.toFixed(3);

                        body_2 += '<tr>';

                        body_2 += '<td style="' + padding + ' width : 10%; text-align: center;">';
                        body_2 += value.material_number;
                        body_2 += '</td>';

                        body_2 += '<td style="' + padding + ' width : 40%; text-align: left;">';
                        body_2 += value.material_description;
                        body_2 += '</td>';

                        body_2 += '<td style="' + padding + ' width : 10%; text-align: center;">';
                        body_2 += value.storage_location;
                        body_2 += '</td>';

                        body_2 += '<td style="' + padding + ' width : 10%; text-align: right;">';
                        price = price / 1000;
                        body_2 += price.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                            style: "currency",
                            currency: "USD"
                        });
                        body_2 += '</td>';

                        body_2 += '<td style="' + padding + ' width : 5%; text-align: right;">';
                        body_2 += value.unrestricted;
                        sum_ymes_qty += value.unrestricted;
                        body_2 += '</td>';

                        sum_book += (price * value.unrestricted);

                        body_2 += '<td style="' + padding + ' width : 5%; text-align: right;">';
                        body_2 += pii;
                        sum_pi_qty += pii;
                        body_2 += '</td>';

                        var diff_qty = pii - value.unrestricted;
                        var bg_color = 'background-color : #ffccff;';
                        if (diff_qty == 0) {
                            bg_color = 'background-color : #ccffff;';
                        }
                        body_2 += '<td style="' + padding + bg_color + ' width : 10%; text-align: right;">';
                        body_2 += diff_qty;
                        body_2 += '</td>';

                        body_2 += '<td style="' + padding + bg_color + ' width : 10%; text-align: right;">';
                        var amt_var = Math.abs(diff_qty) * price;
                        sum_amt_var += Math.abs(diff_qty) * parseFloat(price);

                        sum_ymes_book += value.unrestricted * price;
                        sum_pi_book += pii * price;
                        body_2 += amt_var.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                            style: "currency",
                            currency: "USD"
                        });
                        body_2 += '</td>';

                        body_2 += '</tr>';
                    });
                    $('#body_2').append(body_2);

                    // var sum_book = result.jumlah_book[0].jumlah;
                    var sum_pii = result.jumlah_pi[0].jumlah;

                    var sum_pi = 0;
                    if (sum_pii != null) {
                        sum_pi = sum_pii;
                    } else {
                        sum_pi;
                    }

                    $('#sum_ymes_qty').text(sum_ymes_qty.toLocaleString());
                    $('#sum_ymes_book').text(Math.round(sum_book).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                        style: "currency",
                        currency: "USD"
                    }));
                    $('#sum_pi_qty').text(sum_pi.toLocaleString());
                    $('#sum_pi_book').text(Math.round(sum_pi).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                        style: "currency",
                        currency: "USD"
                    }));
                    var variance = Math.abs((sum_pi - sum_book)) / sum_book * 100;
                    $('#variance').text(variance.toFixed(2) + '%');

                    $('#table_2 tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="tb_search" style="text-align: center; color: black; width: 100%; padding: 1%;" type="text" placeholder="Search ' +
                            title + '"/>');
                    });

                    ymes_pi = $('#table_2').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [-1],
                            ['Show all']
                        ],
                        'buttons': {
                            buttons: [{
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

                    ymes_pi.columns().every(function() {
                        var that = this;
                        $('.tb_search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#table_2 tfoot tr').prependTo('#table_2 thead');
                } else {
                    alert('Attempt to retrieve data failed');
                }
            });
        }

        function revisePI(slip) {

            var data = {
                // stocktaking: stocktaking
                slip: slip
            }

            $("#loading").show();
            $.post('{{ url('delete/stocktaking/mstk_pi') }}', data, function(result, status, xhr) {
                if (result.status) {

                    // for (var i = 0; i < stocktaking.length; i++) {
                    //     mirai_pi.rows().every(function(rowIdx, tableLoop, rowLoop) {
                    //         var data = this.data();

                    //         if (data[1] == stocktaking[i].slip && data[0] == stocktaking[i]
                    //             .material_number) {
                    //             data[5] = parseFloat(data[5]) - parseFloat(stocktaking[i].pi);
                    //             data[6] = parseFloat(data[6]) - parseFloat(stocktaking[i].pi);

                    //             var amt = parseFloat(stocktaking[i].pi) * parseFloat(data[4].replace('$',
                    //                 ''));
                    //             var new_var_amt = parseFloat(data[7]) - amt;
                    //             var new_amt_var_formated = new_var_amt.toLocaleString(undefined, {
                    //                 minimumFractionDigits: 2,
                    //                 maximumFractionDigits: 2,
                    //                 style: "currency",
                    //                 currency: "USD"
                    //             });
                    //             data[7] = new_amt_var_formated;

                    //             if (data[6] == 0) {
                    //                 $('#body_1 tr:eq(' + rowIdx + ') td:eq(6)').css('background-color',
                    //                     '#ccffff');
                    //                 $('#body_1 tr:eq(' + rowIdx + ') td:eq(7)').css('background-color',
                    //                     '#ccffff');
                    //             } else {
                    //                 $('#body_1 tr:eq(' + rowIdx + ') td:eq(6)').css('background-color',
                    //                     '#ffccff');
                    //                 $('#body_1 tr:eq(' + rowIdx + ') td:eq(7)').css('background-color',
                    //                     '#ffccff');
                    //             }

                    //             this.row(rowIdx).data(data);
                    //             mirai_pi.draw();
                    //         }
                    //     });
                    // }

                    // for (var i = 0; i < stocktaking.length; i++) {
                    //     ymes_pi.rows().every(function(rowIdx, tableLoop, rowLoop) {
                    //         var data = this.data();

                    //         if (data[0] == stocktaking[i].material_number) {
                    //             data[4] = parseFloat(data[4]) - parseFloat(stocktaking[i].pi);
                    //             data[5] = parseFloat(data[5]) - parseFloat(stocktaking[i].pi);

                    //             var amt = parseFloat(stocktaking[i].pi) * parseFloat(data[2].replace('$',
                    //                 ''));
                    //             var new_var_amt = parseFloat(data[6]) - amt;
                    //             var new_amt_var_formated = new_var_amt.toLocaleString(undefined, {
                    //                 minimumFractionDigits: 2,
                    //                 maximumFractionDigits: 2,
                    //                 style: "currency",
                    //                 currency: "USD"
                    //             });
                    //             data[6] = new_amt_var_formated;

                    //             sum_pi_qty -= parseFloat(stocktaking[i].pi);
                    //             sum_pi_book -= (parseFloat(stocktaking[i].pi) * parseFloat(data[2].replace(
                    //                 '$', '')));

                    //             if (data[5] == 0) {
                    //                 $('#body_2 tr:eq(' + rowIdx + ') td:eq(5)').css('background-color',
                    //                     '#ccffff');
                    //                 $('#body_2 tr:eq(' + rowIdx + ') td:eq(6)').css('background-color',
                    //                     '#ccffff');
                    //             } else {
                    //                 $('#body_2 tr:eq(' + rowIdx + ') td:eq(5)').css('background-color',
                    //                     '#ffccff');
                    //                 $('#body_2 tr:eq(' + rowIdx + ') td:eq(6)').css('background-color',
                    //                     '#ffccff');
                    //             }


                    //             this.row(rowIdx).data(data);
                    //             ymes_pi.draw();
                    //         }
                    //     });
                    // }

                    // $('#sum_pi_qty').text(sum_pi_qty.toLocaleString());
                    // $('#sum_pi_book').text(Math.round(sum_pi_book).toLocaleString(undefined, {
                    //     minimumFractionDigits: 2,
                    //     maximumFractionDigits: 2,
                    //     style: "currency",
                    //     currency: "USD"
                    // }));
                    // var variance = Math.abs((sum_pi_book - sum_ymes_book)) / sum_ymes_book * 100;
                    // $('#variance').text(variance.toFixed(2) + '%');
                    scanSlip(slip);
                } else {
                    $("#slip").val("");
                    $("#slip").focus();
                    $("#loading").hide();
                    openErrorGritter('Error', result.message);
                }
            });
        }

        function submitPi() {

            var pi = [];
            for (let i = 1; i <= count_row; i++) {

                var slip = $('#row_' + i).find('td').eq(0).text();
                var material_number = $('#row_' + i).find('td').eq(1).text();
                var material_description = $('#row_' + i).find('td').eq(2).text();
                var quantity = $('#quantity_' + i).val();

                if (quantity == '') {
                    openErrorGritter('Error', 'Input PI');
                    return false;
                }

                pi.push({
                    'slip': slip,
                    'material_number': material_number,
                    'material_description': material_description,
                    'quantity': quantity,
                });

            }

            var inputor_name = $("#inputor").val();
            var data = inputor_name.split(' - ');
            var inputor = data[0];

            var data = {
                pi: pi,
                employee_id: inputor
            }

            $("#loading").show();
            $.post('{{ url('input/stocktaking/mstk_pi') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#modalInput').modal('hide');
                    $('#slip').val('');
                    $('#slip').focus();

                    // for (var i = 0; i < pi.length; i++) {
                    //     mirai_pi.rows().every(function(rowIdx, tableLoop, rowLoop) {
                    //         var data = this.data();

                    //         if (data[1] == pi[i].slip && data[0] == pi[i].material_number) {
                    //             data[5] = pi[i].quantity;
                    //             data[6] = parseFloat(data[4]) - parseFloat(pi[i].quantity);

                    //             var diff_abs = parseFloat(Math.abs(data[6]));
                    //             var price = parseFloat(data[4].replace('$', ''));
                    //             var amt_var = diff_abs * price;
                    //             var amt_var_formated = amt_var.toLocaleString(undefined, {
                    //                 minimumFractionDigits: 2,
                    //                 maximumFractionDigits: 2,
                    //                 style: "currency",
                    //                 currency: "USD"
                    //             });
                    //             data[7] = amt_var_formated;

                    //             if (data[6] == 0) {
                    //                 $('#body_1 tr:eq(' + rowIdx + ') td:eq(6)').css('background-color',
                    //                     '#ccffff');
                    //                 $('#body_1 tr:eq(' + rowIdx + ') td:eq(7)').css('background-color',
                    //                     '#ccffff');
                    //             } else {
                    //                 $('#body_1 tr:eq(' + rowIdx + ') td:eq(6)').css('background-color',
                    //                     '#ffccff');
                    //                 $('#body_1 tr:eq(' + rowIdx + ') td:eq(7)').css('background-color',
                    //                     '#ffccff');
                    //             }

                    //             this.row(rowIdx).data(data);
                    //             mirai_pi.draw();
                    //         }
                    //     });
                    // }


                    // for (var i = 0; i < pi.length; i++) {
                    //     ymes_pi.rows().every(function(rowIdx, tableLoop, rowLoop) {
                    //         var data = this.data();

                    //         if (data[0] == pi[i].material_number) {
                    //             data[4] = parseFloat(data[4]) + parseFloat(pi[i].quantity);
                    //             data[5] = parseFloat(data[4]) - parseFloat(data[3]);

                    //             var diff_abs = parseFloat(Math.abs(data[5]));
                    //             var price = parseFloat(data[2].replace('$', ''));
                    //             var amt_var = diff_abs * price;
                    //             var amt_var_formated = amt_var.toLocaleString(undefined, {
                    //                 minimumFractionDigits: 2,
                    //                 maximumFractionDigits: 2,
                    //                 style: "currency",
                    //                 currency: "USD"
                    //             });
                    //             data[6] = amt_var_formated;

                    //             sum_pi_qty += parseFloat(pi[i].quantity);
                    //             sum_pi_book += (parseFloat(pi[i].quantity) * parseFloat(data[2].replace('$',
                    //                 '')));

                    //             if (data[5] == 0) {
                    //                 $('#body_2 tr:eq(' + rowIdx + ') td:eq(5)').css('background-color',
                    //                     '#ccffff');
                    //                 $('#body_2 tr:eq(' + rowIdx + ') td:eq(6)').css('background-color',
                    //                     '#ccffff');
                    //             } else {
                    //                 $('#body_2 tr:eq(' + rowIdx + ') td:eq(5)').css('background-color',
                    //                     '#ffccff');
                    //                 $('#body_2 tr:eq(' + rowIdx + ') td:eq(6)').css('background-color',
                    //                     '#ffccff');
                    //             }

                    //             this.row(rowIdx).data(data);
                    //             ymes_pi.draw();
                    //         }
                    //     });
                    // }

                    // $('#sum_pi_qty').text(sum_pi_qty.toLocaleString());
                    // $('#sum_pi_book').text(Math.round(sum_pi_book).toLocaleString(undefined, {
                    //     minimumFractionDigits: 2,
                    //     maximumFractionDigits: 2,
                    //     style: "currency",
                    //     currency: "USD"
                    // }));
                    // var variance = Math.abs((sum_pi_book - sum_ymes_book)) / sum_ymes_book * 100;
                    // $('#variance').text(variance.toFixed(2) + '%');

                    $("#loading").hide();
                    openSuccessGritter('Success', result.message);
                    reloadTable();
                    // refreshData();
                } else {
                    $("#loading").hide();
                    openErrorGritter('Error', result.message);
                }
            });

        }

        function inputorInput() {
            if ($('#inputor').val() != '') {
                $('#modalInputor').modal('hide');

                var auditor = $('#inputor').val();
                $('#inputor_name').text('');
                $('#inputor_name').text('Inputor : ' + auditor);
                $('#slip').removeAttr('disabled');
                $('#slip').focus();

                fetchCountScrap();

            }
        };

        function refreshData() {
            fetchCountScrap();

        }

        function fetchCountScrap() {
            $("#loading").show();

            $.get('{{ url('fetch/stocktaking/count_scrap') }}', function(result, status, xhr) {
                if (result.status) {

                    $('#table_1').DataTable().clear();
                    $('#table_1').DataTable().destroy();
                    $('#head_1').html("");
                    var head_1 = '<tr>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Slip</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Loc.</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Std. Price</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">PI</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Amt. Var.</th>';
                    head_1 += '</tr>';
                    $('#head_1').append(head_1);

                    $('#foot_1').html("");
                    var foot_1 = '';
                    foot_1 += '<tr>'
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '</tr>';
                    $('#foot_1').append(foot_1);

                    $('#body_1').html("");
                    var body_1 = '';
                    $.each(result.test, function(key, value) {
                        var padding = 'padding-top: 0px; padding-bottom: 0px; ';
                        var material_description = '';
                        var price = 0;

                        for (let i = 0; i < mpdl.length; i++) {
                            if (mpdl[i].material_number == value.gmc) {
                                material_description = mpdl[i].material_description;
                                price = mpdl[i].standard_price;
                                break;
                            }
                        }

                        body_1 += '<tr>';
                        body_1 += '<td style="' + padding + ' width : 10%; text-align: center;">';
                        body_1 += value.gmc;
                        body_1 += '</td>';

                        body_1 += '<td style="' + padding + ' width : 10%; text-align: center;">';
                        body_1 += value.slip;
                        body_1 += '</td>';

                        body_1 += '<td style="' + padding + ' width : 40%; text-align: left;">';
                        body_1 += value.description;
                        body_1 += '</td>';

                        body_1 += '<td style="' + padding + ' width : 10%; text-align: center;">';
                        body_1 += value.location;
                        body_1 += '</td>';

                        body_1 += '<td style="' + padding + ' width : 10%; text-align: right;">';
                        price = price / 1000;
                        body_1 += price.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                            style: "currency",
                            currency: "USD"
                        });
                        body_1 += '</td>';

                        body_1 += '<td style="' + padding + ' width : 10%; text-align: right;">';
                        body_1 += value.pi;
                        body_1 += '</td>';

                        var bg_color = 'background-color : #ffccff;';
                        if (value.pi == 0) {
                            bg_color = 'background-color : #ccffff;';
                        }
                        body_1 += '<td style="' + padding + ' width : 10%; text-align: right;">';
                        var amt_var = Math.abs(value.pi) * price;
                        body_1 += amt_var.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                            style: "currency",
                            currency: "USD"
                        });
                        body_1 += '</td>';

                        body_1 += '</tr>';
                    });
                    $('#body_1').append(body_1);

                    $('#table_1 tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="tb_search" style="text-align: center; color: black; width: 100%; padding: 1%;" type="text" placeholder="Search ' +
                            title + '"/>');
                    });

                    mirai_pi = $('#table_1').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [-1],
                            ['Show all']
                        ],
                        'buttons': {
                            buttons: [{
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

                    mirai_pi.columns().every(function() {
                        var that = this;
                        $('.tb_search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#table_1 tfoot tr').prependTo('#table_1 thead');


                    $('#table_2').DataTable().clear();
                    $('#table_2').DataTable().destroy();
                    $('#head_2').html("");
                    var head_2 = '<tr>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Loc.</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Std. Price</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Book</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">PI</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Diff</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Amt. Var.</th>';
                    head_2 += '</tr>';
                    $('#head_2').append(head_2);

                    $('#foot_2').html("");
                    var foot_2 = '';
                    foot_2 += '<tr>'
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '</tr>';
                    $('#foot_2').append(foot_2);

                    $('#body_2').html("");
                    var body_2 = '';
                    var sum_book = 0;
                    $.each(result.test2, function(key, value) {
                        var padding = 'padding-top: 0px; padding-bottom: 0px;';
                        var price = 0;
                        for (let i = 0; i < mpdl.length; i++) {
                            if (mpdl[i].material_number == value.material_number) {
                                price = mpdl[i].standard_price;
                                break;
                            }
                        }

                        var pii = 0;
                        for (let m = 0; m < data.length; m++) {
                            if (data[m].gmc == value.material_number && value.storage_location == data[m]
                                .location) {
                                pii += parseFloat(data[m].pi);
                            }
                        }
                        pii = pii.toFixed(3);

                        body_2 += '<tr>';
                        body_2 += '<td style="' + padding + ' width : 10%; text-align: center;">';
                        body_2 += value.material_number;
                        body_2 += '</td>';

                        body_2 += '<td style="' + padding + ' width : 40%; text-align: left;">';
                        body_2 += value.material_description;
                        body_2 += '</td>';

                        body_2 += '<td style="' + padding + ' width : 10%; text-align: center;">';
                        body_2 += value.storage_location;
                        body_2 += '</td>';

                        body_2 += '<td style="' + padding + ' width : 10%; text-align: right;">';
                        price = price / 1000;
                        body_2 += price.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                            style: "currency",
                            currency: "USD"
                        });
                        body_2 += '</td>';

                        body_2 += '<td style="' + padding + ' width : 5%; text-align: right;">';
                        body_2 += value.unrestricted;
                        sum_ymes_qty += value.unrestricted;
                        body_2 += '</td>';

                        sum_book += (price * value.unrestricted);

                        body_2 += '<td style="' + padding + ' width : 5%; text-align: right;">';
                        body_2 += pii;
                        sum_pi_qty += pii;
                        body_2 += '</td>';

                        var diff_qty = pii - value.unrestricted;
                        var bg_color = 'background-color : #ffccff;';
                        if (diff_qty == 0) {
                            bg_color = 'background-color : #ccffff;';
                        }
                        body_2 += '<td style="' + padding + bg_color + ' width : 10%; text-align: right;">';
                        body_2 += diff_qty;
                        body_2 += '</td>';

                        body_2 += '<td style="' + padding + bg_color + ' width : 10%; text-align: right;">';
                        var amt_var = Math.abs(diff_qty) * price;
                        sum_ymes_book += value.unrestricted * price;
                        sum_pi_book += pii * price;
                        body_2 += amt_var.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                            style: "currency",
                            currency: "USD"
                        });
                        body_2 += '</td>';

                        body_2 += '</tr>';
                    });
                    $('#body_2').append(body_2);

                    // var sum_book = result.jumlah_book[0].jumlah;
                    var sum_pii = result.jumlah_pi[0].jumlah;

                    var sum_pi = 0;
                    if (sum_pii != null) {
                        sum_pi = sum_pii;
                    } else {
                        sum_pi;
                    }

                    $('#sum_ymes_qty').text(sum_ymes_qty.toLocaleString());
                    $('#sum_ymes_book').text(Math.round(sum_book).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                        style: "currency",
                        currency: "USD"
                    }));
                    $('#sum_pi_qty').text(sum_pi.toLocaleString());
                    $('#sum_pi_book').text(Math.round(sum_pi).toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                        style: "currency",
                        currency: "USD"
                    }));
                    var variance = Math.abs((sum_pi - sum_book)) / sum_book * 100;
                    $('#variance').text(variance.toFixed(2) + '%');

                    $('#table_2 tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="tb_search" style="text-align: center; color: black; width: 100%; padding: 1%;" type="text" placeholder="Search ' +
                            title + '"/>');
                    });

                    ymes_pi = $('#table_2').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [-1],
                            ['Show all']
                        ],
                        'buttons': {
                            buttons: [{
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

                    ymes_pi.columns().every(function() {
                        var that = this;
                        $('.tb_search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#table_2 tfoot tr').prependTo('#table_2 thead');


                    $("#loading").hide();
                }
            });
        }


        function getMiraiStock() {
            $("#loading").show();

            var inputor_name = $("#inputor").val();
            var data = inputor_name.split(' - ');
            var inputor = data[0];

            var data = {
                employee_id: inputor
            }

            $.get('{{ url('fetch/stocktaking/fstk_stock') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();
                    openSuccessGritter('Success', 'Stock FSTK MIRAI exported succesfully');
                }
            });

        }

        var audio_error = new Audio('{{ url('sounds/error_suara.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '4000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '4000'
            });
        }
    </script>
@endsection
