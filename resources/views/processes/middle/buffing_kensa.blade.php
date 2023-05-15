@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">
    <style type="text/css">
        .nmpd-grid {
            border: none;
            padding: 20px;
        }

        .nmpd-grid>tbody>tr>td {
            border: none;
        }

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

        td:hover {
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
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            padding: 0px;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            padding: 0;
            vertical-align: middle;
            background-color: rgb(126, 86, 134);
            color: #FFD700;
        }

        thead {
            background-color: rgb(126, 86, 134);
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #ngList {
            height: 480px;
            overflow-y: scroll;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@stop
@section('header')
@endsection
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content" style="padding-top: 0;">
        <input type="hidden" id="loc" value="{{ $loc }}">
        <input type="hidden" id="started_at">
        <input type="hidden" id="buffing_time">
        <div class="row" style="margin-left: 1%; margin-right: 1%;">
            <div class="col-xs-6" style="padding-right: 0; padding-left: 0">
                <div>
                    <table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
                        <thead>
                            <tr>
                                <th style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;"
                                    colspan="2">Operator Kensa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="background-color: rgb(204,255,255); text-align: center; color: yellow; background-color: rgb(50, 50, 50); font-size:2vw; width: 30%;"
                                    id="op">-</td>
                                <td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;"
                                    id="op2">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="input-group" style="padding-top: 5px;">
                    <div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
                        <i class="glyphicon glyphicon-credit-card"></i>
                    </div>
                    <input type="text" style="text-align: center; border-color: black;" class="form-control input-lg"
                        id="tag" name="tag" placeholder="Scan RFID Card..." required>
                    <div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
                        <i class="glyphicon glyphicon-credit-card"></i>
                    </div>
                </div>
                <div style="padding-top: 5px;">
                    <table style="width: 100%;" border="1">
                        <tbody>
                            <tr>
                                <td
                                    style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">
                                    Model</td>
                                <td id="model"
                                    style="width: 4%; font-size: 25px; font-weight: bold; background-color: rgb(100,100,100); color: yellow; border: 1px solid black"
                                    colspan="2"></td>
                                <td
                                    style="width: 1%; font-weight: bold; font-size: 25px; background-color: rgb(220,220,220);">
                                    Key</td>
                                <td id="key"
                                    style="width: 4%; font-weight: bold; font-size: 25px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black">
                                </td>
                                <input type="hidden" id="material_tag">
                                <input type="hidden" id="material_number">
                                <input type="hidden" id="material_quantity">
                                <input type="hidden" id="employee_id">
                            </tr>
                            <tr>
                                <td colspan="2"
                                    style="font-weight: bold; background-color: rgb(220,220,220); font-size: 25px;">Operator
                                    Buffing</td>
                                <td colspan="3"
                                    style="font-weight: bold; font-size: 25px; background-color: rgb(100,100,100); color: yellow; border: 1px solid black"
                                    id="opbuffing"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if (str_contains($loc, 'sx'))
                    <div style="padding-top: 15px;">
                        <table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
                            <thead>
                                <tr>
                                    <th colspan="3"
                                        style="background-color: #ffff66; text-align: center; color: black; font-weight: bold; font-size:2vw;">
                                        ALTO</th>
                                </tr>
                                <tr>
                                    <th
                                        style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                        Result</th>
                                    <th
                                        style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                        Not Good</th>
                                    <th
                                        style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                        Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;"
                                        id="ASresult">0</td>
                                    <td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;"
                                        id="ASnotGood">0</td>
                                    <td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 2vw;"
                                        id="ASngRate">0%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="padding-top: 5px;">
                        <table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
                            <thead>
                                <tr>
                                    <th colspan="3"
                                        style="background-color: rgb(157, 255, 105); text-align: center; color: black; font-weight: bold; font-size:2vw;">
                                        TENOR</th>
                                </tr>
                                <tr>
                                    <th
                                        style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                        Result</th>
                                    <th
                                        style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                        Not Good</th>
                                    <th
                                        style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                        Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;"
                                        id="TSresult">0</td>
                                    <td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;"
                                        id="TSnotGood">0</td>
                                    <td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 2vw;"
                                        id="TSngRate">0%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @elseif(str_contains($loc, 'cl'))
                    <div style="padding-top: 5px;">
                        <table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
                            <thead>
                                <tr>
                                    <th colspan="3"
                                        style="background-color: rgb(120, 146, 240); text-align: center; color: black; font-weight: bold; font-size:2vw;">
                                        RESUME</th>
                                </tr>
                                <tr>
                                    <th
                                        style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                        Result</th>
                                    <th
                                        style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                        Not Good</th>
                                    <th
                                        style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                        Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 2vw;"
                                        id="CLresult">0</td>
                                    <td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 2vw;"
                                        id="CLnotGood">0</td>
                                    <td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 2vw;"
                                        id="CLngRate">0%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="col-xs-6" style="padding-right: 0;">
                <div id="ngList">
                    <table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
                        <thead>
                            <tr>
                                <th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">#
                                </th>
                                <th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">NG
                                    Name</th>
                                <th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">#
                                </th>
                                <th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">
                                    Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; ?>
                            @foreach ($ng_lists as $nomor => $ng_list)
                                <?php if ($no % 2 === 0) {
                                    $color = 'style="background-color: #fffcb7"';
                                } else {
                                    $color = 'style="background-color: #ffd8b7"';
                                }
                                ?>
                                <input type="hidden" id="loop" value="{{ $loop->count }}">
                                <tr <?php echo $color; ?>>
                                    <td id="minus" onclick="minus({{ $nomor + 1 }})"
                                        style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer;"
                                        class="unselectable">-</td>
                                    <td id="ng{{ $nomor + 1 }}" style="font-size: 20px;">{{ $ng_list->ng_name }} </td>
                                    <td id="plus" onclick="plus({{ $nomor + 1 }})"
                                        style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;"
                                        class="unselectable">+</td>
                                    <td
                                        style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;">
                                        <span id="count{{ $nomor + 1 }}">0</span></td>
                                </tr>
                                <?php $no += 1; ?>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div>
                    <center>
                        <button
                            style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white; width: 32%"
                            onclick="canc()" class="btn btn-danger">CANCEL</button>
                        <button id="rework"
                            style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white; width: 32%"
                            onclick="rework()" class="btn btn-warning">REWORK</button>
                        <button id="conf1"
                            style="width: 100%; margin-top: 10px; font-size: 3vw; padding:0; font-weight: bold; border-color: black; color: white; width: 32%"
                            onclick="conf()" class="btn btn-success">CONFIRM</button>
                    </center>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalOperator">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body table-responsive no-padding">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Employee ID</label>
                            <input class="form-control" style="width: 100%; text-align: center;" type="text"
                                id="operator" placeholder="Scan ID Card" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_check">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 style="margin: 0px; text-align: center; font-weight: bold;">
                        UPDATE KANBAN
                    </h2>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input id="idx" hidden>
                        <div class="col-xs-6" style="padding-right: 0px;">
                            <input
                                style="font-weight: bold; text-align: center; font-size: 3vw; width: 100%; height: 50px;"
                                type="text" id="update_model" readonly>
                        </div>
                        <div class="col-xs-6" style="padding-left: 0px;">
                            <input
                                style="font-weight: bold; text-align: center; font-size: 3vw; width: 100%; height: 50px;"
                                type="text" id="update_key" readonly>
                        </div>
                        <div class="col-xs-12">
                            <input
                                style="font-weight: bold; text-align: center; font-size: 5vw; width: 100%; height: 150px; vertical-align: middle; background-color: #F2F2F2;"
                                type="text" class="numpad" id="no_kanban">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-xs-6" style="padding-right: 0px;">
                        <button style="width: 100%;" type="button" class="btn btn-danger btn-lg"
                            data-dismiss="modal">Close</button>
                    </div>
                    <div class="col-xs-6" style="padding-right: 0px;">
                        <button style="width: 100%;" class="btn btn-success btn-lg" onclick="updateKanban()"><span><i
                                    class="fa fa-save"></i> &nbsp;&nbsp;Update</span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/jquery.numpad.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 60%;"></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:20px; height: 50px;"/>';
        $.fn.numpad.defaults.buttonNumberTpl =
            '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl =
            '<button type="button" class="btn" style="font-size:20px; width: 100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function() {
            $(this).find('.done').addClass('btn-primary');
        };

        jQuery(document).ready(function() {
            $('#modalOperator').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });
            $('#operator').val('');
            $('#tag').val('');
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

        $('#tag').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                getHeader(this.value);
            }
        });

        $('#modalOperator').on('shown.bs.modal', function() {
            $('#operator').focus();
        });

        $('#operator').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {

                var data = {
                    employee_id: $("#operator").val()
                }

                $.get('{{ url('scan/middle/operator/rfid') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success!', result.message);
                        $('#modalOperator').modal('hide');
                        $('#op').html(result.employee.employee_id);
                        $('#op2').html(result.employee.name);
                        $('#employee_id').val(result.employee.employee_id);
                        fillResult(result.employee.employee_id);
                        $('#tag').focus();
                    } else {
                        audio_error.play();
                        openErrorGritter('Error', result.message);
                        $('#operator').val('');
                    }
                });

            }
        });

        function updateKanban() {
            var idx = $("#idx").val();
            var no_kanban = $("#no_kanban").val();

            var data = {
                idx: idx,
                no_kanban: no_kanban
            }

            $("#loading").show();
            $.post('{{ url('update/middle/buffing_kanban') }}', data, function(result, status, xhr) {
                $("#loading").hide();
                if (result.status) {
                    $("#tag").val('');
                    $("#tag").focus();

                    $("#idx").val('');
                    $("#update_model").val('');
                    $("#update_key").val('');
                    $("#no_kanban").val('');

                    $("#modal_check").modal('hide');
                    openSuccessGritter('Success', result.message);

                } else {
                    $("#loading").hide();

                    openErrorGritter('Error!', result.message);
                    audio_error.play();
                    $("#tag").val("");
                    $("#tag").focus();
                }

            });
        }


        function getHeader(tag) {
            var data = {
                location: $('#loc').val(),
                tag: tag
            }

            $.get('{{ url('scan/middle/buffing/kensa/material') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $("#model").text(result.material.model);
                    $("#key").text(result.material.key);
                    $("#opbuffing").text(result.datas.operator_id + " - " + result.operator.name);

                    $('#buffing_time').val(result.datas.updated_at);
                    $('#material_tag').val(result.datas.material_tag_id);
                    $('#material_number').val(result.datas.material_num);
                    $('#started_at').val(result.started_at);
                    $('#material_quantity').val(result.datas.material_qty);
                    $("input").prop('disabled', true);
                    openSuccessGritter('Success', '');

                    if (!result.datas.no_kanban) {
                        $("#update_model").val(result.material.model);
                        $("#update_key").val(result.material.key);
                        $("#idx").val(result.datas.idx);

                        $("#no_kanban").prop('disabled', false);

                        if (result.datas.material_qty == 8) {
                            $("#update_model").css({
                                "background-color": "#07E493"
                            });
                            $("#update_key").css({
                                "background-color": "#07E493"
                            });
                        } else if (result.datas.material_qty == 10) {
                            $("#update_model").css({
                                "background-color": "#FFD10C"
                            });
                            $("#update_key").css({
                                "background-color": "#FFD10C"
                            });
                        } else if (result.datas.material_qty == 15) {
                            $("#update_model").css({
                                "background-color": "#4EB8F5"
                            });
                            $("#update_key").css({
                                "background-color": "#4EB8F5"
                            });
                        }

                        $("#modal_check").modal('show');

                    }
                } else {
                    audio_error.play();
                    openErrorGritter('Error', result.message);
                    $("#tag").val("");
                    $("#tag").focus();
                }
            });
        }

        function fillResult(emp_id) {
            var data = {
                location: $('#loc').val(),
                employee_id: emp_id,
            }
            $.get('{{ url('fetch/middle/kensa') }}', data, function(result, status, xhr) {
                var asQty = 0,
                    tsQty = 0,
                    clQty;

                $.each(result.result, function(index, value) {
                    if (value.hpl == 'ASKEY') {
                        $('#ASresult').text(value.qty);
                        asQty = value.qty;
                    } else if (value.hpl == 'TSKEY') {
                        $('#TSresult').text(value.qty);
                        tsQty = value.qty;
                    } else if (value.hpl == 'CLKEY') {
                        $('#CLresult').text(value.qty);
                        clQty = value.qty;
                    }
                })

                $.each(result.ng, function(index, value) {
                    if (value.hpl == 'ASKEY') {
                        $('#ASnotGood').text(value.qty);
                        $('#ASngRate').text(Math.round((value.qty / asQty) * 100, 2) + '%');
                    } else if (value.hpl == 'TSKEY') {
                        $('#TSnotGood').text(value.qty);
                        $('#TSngRate').text(Math.round((value.qty / tsQty) * 100, 2) + '%');
                    } else if (value.hpl == 'CLKEY') {
                        $('#CLnotGood').text(value.qty);
                        $('#CLngRate').text(Math.round((value.qty / clQty) * 100, 2) + '%');
                    }

                })
            });
        }

        function disabledButton() {
            if ($('#tag').val() != "") {
                var btn = document.getElementById('conf1');
                btn.disabled = true;
                btn.innerText = 'Posting...'
                return false;
            }
        }

        function disabledButtonRework() {
            if ($('#tag').val() != "") {
                var btn = document.getElementById('rework');
                btn.disabled = true;
                btn.innerText = 'Posting...'
                return false;
            }
        }

        function rework() {
            if ($('#tag').val() == "") {
                openErrorGritter('Error!', 'Tag is empty');
                audio_error.play();
                $("#tag").val("");
                $("#tag").focus();

                return false;
            }

            var tag = $('#tag_material').val();
            var loop = $('#loop').val();
            var count_ng = 0;
            var ng = [];
            var count_text = [];
            for (var i = 1; i <= loop; i++) {
                if ($('#count' + i).text() > 0) {
                    ng.push([$('#ng' + i).text(), $('#count' + i).text()]);
                    count_text.push('#count' + i);
                    count_ng += 1;
                }
            }

            var data = {
                loc: $('#loc').val(),
                tag: $('#material_tag').val(),
                material_number: $('#material_number').val(),
                quantity: $('#material_quantity').val(),
                employee_id: $('#employee_id').val(),
                started_at: $('#started_at').val(),
                ng: ng,
                count_text: count_text,
            }
            disabledButtonRework();

            $.post('{{ url('input/middle/rework') }}', data, function(result, status, xhr) {
                if (result.status) {
                    var btn = document.getElementById('rework');
                    btn.disabled = false;
                    btn.innerText = 'REWORK';
                    openSuccessGritter('Success!', result.message);
                    for (var i = 1; i <= loop; i++) {
                        $('#count' + i).text(0);
                    }
                    $('#model').text("");
                    $('#key').text("");
                    $('#material_tag').val("");
                    $('#material_number').val("");
                    $('#material_quantity').val("");
                    $('#tag').val("");
                    $('#tag').prop('disabled', false);
                    fillResult($('#employee_id').val());
                    $('#tag').focus();
                } else {
                    var btn = document.getElementById('rework');
                    btn.disabled = false;
                    btn.innerText = 'REWORK';
                    audio_error.play();
                    openErrorGritter('Error!', result.message);
                    $("#tag").val("");
                    $("#tag").focus();
                }
            });
        }

        function conf() {
            if ($('#model').text() == "") {
                openErrorGritter('Error!', 'Tag is empty');
                audio_error.play();
                $("#model").text("");

                return false;
            }

            var loop = $('#loop').val();
            var count_ng = 0;
            var ng = [];
            var count_text = [];
            for (var i = 1; i <= loop; i++) {
                if ($('#count' + i).text() > 0) {
                    ng.push([$('#ng' + i).text(), $('#count' + i).text()]);
                    count_text.push('#count' + i);
                    count_ng += 1;
                }
            }

            var data = {
                loc: $('#loc').val(),
                tag: $('#material_tag').val(),
                material_number: $('#material_number').val(),
                quantity: $('#material_quantity').val(),
                employee_id: $('#op').text(),
                operator_id: $('#opbuffing').text().split(' ')[0],
                started_at: $('#started_at').val(),
                buffing_time: $('#buffing_time').val(),
                cek: $('#material_quantity').val(),
                ng: ng,
                count_text: count_text,
            }
            disabledButton();

            $.post('{{ url('input/middle/buffing/kensa') }}', data, function(result, status, xhr) {
                if (result.status) {
                    var btn = document.getElementById('conf1');
                    btn.disabled = false;
                    btn.innerText = 'CONFIRM';
                    openSuccessGritter('Success!', result.message);
                    for (var i = 1; i <= loop; i++) {
                        $('#count' + i).text(0);
                    }
                    $('#model').text("");
                    $('#key').text("");
                    $('#material_tag').val("");
                    $('#material_number').val("");
                    $('#material_quantity').val("");
                    $('#opbuffing').text("");
                    $('#tag').val("");
                    $('#tag').prop('disabled', false);
                    fillResult($('#employee_id').val());
                    $('#tag').focus();
                } else {
                    var btn = document.getElementById('conf1');
                    btn.disabled = false;
                    btn.innerText = 'CONFIRM';
                    audio_error.play();
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function canc() {
            var loop = $('#loop').val();
            for (var i = 1; i <= loop; i++) {
                $('#count' + i).text(0);
            };
            $('#model').text("");
            $('#key').text("");
            $('#opbuffing').text("");
            $('#material_tag').val("");
            $('#material_number').val("");
            $('#material_quantity').val("");
            $('#employee_id').val("");
            $('#tag').val("");
            $('#tag').prop('disabled', false);
            $('#tag').focus();

        }

        function plus(id) {
            var count = $('#count' + id).text();
            if ($('#key').text() != "") {
                $('#count' + id).text(parseInt(count) + 1);
            } else {
                audio_error.play();
                openErrorGritter('Error!', 'Scan material first.');
                $("#tag").val("");
                $("#tag").focus();
            }
        }

        function minus(id) {
            var count = $('#count' + id).text();
            if ($('#key').text() != "") {
                if (count > 0) {
                    $('#count' + id).text(parseInt(count) - 1);
                }
            } else {
                audio_error.play();
                openErrorGritter('Error!', 'Scan material first.');
                $("#tag").val("");
                $("#tag").focus();
            }
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

        $.date = function(dateObject) {
            var d = new Date(dateObject);
            var day = d.getDate();
            var month = d.getMonth() + 1;
            var year = d.getFullYear();
            if (day < 10) {
                day = "0" + day;
            }
            if (month < 10) {
                month = "0" + month;
            }
            var date = day + "/" + month + "/" + year;

            return date;
        };
    </script>
@endsection
