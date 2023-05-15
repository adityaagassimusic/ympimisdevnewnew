@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="<?php echo e(url('css/jquery.numpad.css')); ?>" rel="stylesheet">
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

        #ngTemp {
            height: 200px;
            overflow-y: scroll;
        }

        #ngList2 {
            height: 385px;
            overflow-y: scroll;
            padding-top: 5px;
        }

        #loading,
        #error {
            display: none;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            /* display: none; <- Crashes Chrome on hover */
            -webkit-appearance: none;
            margin: 0;
            /* <-- Apparently some margin are still there even though it's hidden */
        }

        input[type=number] {
            -moz-appearance: textfield;
            /* Firefox */
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 90px;
            height: 48px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 40px;
            width: 40px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #2196F3;
        }

        input:focus+.slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked+.slider:before {
            -webkit-transform: translateX(40px);
            -ms-transform: translateX(40px);
            transform: translateX(40px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@stop
@section('header')
@endsection
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>
        <input type="hidden" id="location" value="{{ $loc }}">
        <input type="hidden" id="start_time" value="">
        <input type="hidden" id="employee_id" value="">

        <div class="row" style="padding-left: 10px; padding-right: 10px;">
            <div class="col-xs-6" style="padding-right: 0; padding-left: 0">
                <div class="col-xs-8">
                    <div class="row">
                        <table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1">
                            <tbody>
                                <tr>
                                    <th colspan="2"
                                        style=" background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;">
                                        Operator Kensa</th>
                                </tr>
                                <tr>
                                    <td style="background-color: #14213d; color: white; text-align: center; font-size:15px; width: 30%;"
                                        id="op">-</td>
                                    <td style="background-color: #fca311; text-align: center; color: #14213d; font-size: 15px;"
                                        id="op2">-</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xs-4" style="padding-left: 5px;padding-right: 0px">
                    <div class="input-group">
                        <input type="text" style="text-align: center; border-color: black;" class="form-control input-lg"
                            id="tag" name="tag" placeholder="Scan RFID Card..." required>
                        <div class="input-group-addon" id="icon-serial" style="font-weight: bold; border-color: black;">
                            <i class="glyphicon glyphicon-credit-card"></i>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered" style="width: 100%; margin-bottom: 5px;border: 0">
                    <tbody>
                        <tr>
                            <td colspan="3"
                                style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 20px;font-weight: bold;">
                                MATERIAL
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
                                Material Number
                            </td>
                            <td colspan="2"
                                style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
                                Material Description
                            </td>
                        </tr>
                        <tr>
                            <td id="material_number"
                                style="background-color: #3490d1; text-align: center; color: white; font-size: 20px;">-
                            </td>
                            <td colspan="2" id="material_description"
                                style="background-color: #34ced1; text-align: center; color: white; font-size: 20px;">-
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"
                                style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
                                Quantity
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" id="quantity"
                                style="background-color:#ffff66; text-align: center; color: black; font-size: 20px;">-
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
                                History NG
                            </td>
                            <td colspan="2"
                                style="background-color: #d1d1d1; text-align: center; color: #14213d; padding:0;font-size: 15px;font-weight: bold;">
                                Current NG
                            </td>
                        </tr>
                        <tr>
                            <td id="ng_before"
                                style="background-color:rgb(204,255,255); text-align: center; color: black; font-size: 20px;">
                                -
                            </td>
                            <td colspan="2" id="ng_count"
                                style="background-color:rgb(255,204,255); text-align: center; color: black; font-size: 20px;">
                                -
                            </td>
                        </tr>
                    </tbody>
                </table>

                @if ($loc == 'plt-incoming-body-fl')
                    <div class="row">
                        <div class="col-xs-12">
                            {{-- <div class="col-xs-6" style="padding: 0px;">
                                <table class="table table-bordered" style="width: 100%; margin-bottom: 5px; border: 0;">
                                    <tbody>
                                        <tr>
                                            <td colspan="3"
                                                style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;">
                                                NEW</td>
                                        </tr>
                                        <tr>
                                            <td id="minus" onclick="minus_type('new')" class="unselectable"
                                                style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer; width: 30%;">
                                                -
                                            </td>
                                            <td style="font-size: 45px; width: 40%;">
                                                <input id="count_new" type="number" class="numpad form-control"
                                                    value="0"
                                                    style="font-weight: bold; font-size: 40px; background-color: rgb(100,100,100); color: yellow; width: 100%; height: 100%; text-align: center;">
                                            </td>
                                            <td id="plus" onclick="plus_type('new')" class="unselectable"
                                                style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer; width: 30%;">
                                                +
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> --}}
                            <div class="col-xs-6 pull-right" style="padding: 0px;">
                                <table class="table table-bordered" style="width: 100%; margin-bottom: 5px; border: 0;">
                                    <tbody>
                                        <tr>
                                            <td colspan="3"
                                                style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;">
                                                REWORK</td>
                                        </tr>
                                        <tr>
                                            <td id="minus" onclick="minus_type('rework')" class="unselectable"
                                                style="background-color: rgb(255,204,255); font-weight: bold; font-size: 45px; cursor: pointer; width: 30%;">
                                                -
                                            </td>
                                            <td style="font-size: 45px; width: 40%;">
                                                <input id="count_rework" type="number" class="numpad form-control"
                                                    value="0"
                                                    style="font-weight: bold; font-size: 40px; background-color: rgb(100,100,100); color: yellow; width: 100%; height: 100%; text-align: center;">
                                            </td>
                                            <td id="plus" onclick="plus_type('rework')" class="unselectable"
                                                style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer; width: 30%;">
                                                +
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($loc == 'plt-kensa-body-sx' || $loc == 'plt-kensa-body-fl')
                    <button style="width: 100%;font-weight: bold;font-size: 30px;" class="btn btn-danger"
                        onclick="openModalCuciEnthol($('#op').text(),'selesai')">
                        FINISH CUCI ENTHOLE
                    </button>
                @endif

                {{-- @if ($loc == 'plt-incoming-body-sx' || $loc == 'plt-incoming-body-fl')
                    <button style="width: 100%;font-weight: bold;font-size: 30px;margin-top: 10px;" class="btn btn-success"
                        onclick="openModalCuciEnthol($('#op').text(),'mulai')">
                        MULAI CUCI ENTHOLE
                    </button>
                @endif --}}

            </div>



            <div class="col-xs-6" style="padding-right: 0;">
                <div id="ngList2">
                    <table class="table table-bordered" style="width: 100%; margin-bottom: 2px;" border="1"
                        id="tableNgList">
                        <thead>
                            <tr>
                                <th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">#
                                </th>
                                <th style="width: 65%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">NG
                                    Name</th>
                                <th style="width: 10%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">#
                                </th>
                                <th style="width: 15%; background-color: rgb(220,220,220); padding:0;font-size: 20px;">
                                    Count
                                </th>
                            </tr>
                        </thead>
                        <tbody id="bodyTableNgList">
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
                                    <td id="ng{{ $nomor + 1 }}" style="font-size: 25px;">{{ $ng_list->ng_name }}
                                    </td>
                                    <td id="plus" onclick="plus({{ $nomor + 1 }})"
                                        style="background-color: rgb(204,255,255); font-weight: bold; font-size: 45px; cursor: pointer;"
                                        class="unselectable">+</td>
                                    <td
                                        style="font-weight: bold; font-size: 45px; background-color: rgb(100,100,100); color: yellow;">
                                        <span id="count{{ $nomor + 1 }}">0</span>
                                    </td>
                                </tr>
                                <?php $no += 1; ?>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-right: 5px">
                    <button class="btn btn-danger" id="btn_cancel" onclick="cancelAll()"
                        style="font-size: 25px;font-weight: bold;width: 100%">
                        CANCEL
                    </button>
                </div>
                <div class="col-xs-6" style="padding: 0px;padding-top: 10px;padding-left: 5px">
                    <button class="btn btn-success" id="btn_confirm" onclick="confirmNgLog()"
                        style="font-size: 25px;font-weight: bold;width: 100%">
                        CONFIRM
                    </button>
                </div>
                <div class="col-xs-12" style="padding: 0px;padding-top: 10px;padding-left: 0px">
                    <button style="width: 100%;font-weight: bold;font-size: 30px;margin-top: 10px;"
                        class="btn btn-primary" onclick="modalPerolehan($('#op').text())">
                        PEROLEHAN
                    </button>
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

    <div class="modal fade" id="modalPerolehan">
        <div class="modal-dialog modal-sm" style="width: 500px">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        @if ((str_contains($loc, 'incoming') || str_contains($loc, 'kensa')) &&
                            !str_contains($loc, 'acc') &&
                            !str_contains($loc, 'cl') &&
                            !str_contains($loc, 'fl'))
                            <div style="padding-top: 5px;">
                                <table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
                                    <thead>
                                        <tr>
                                            <th colspan="2"
                                                style="background-color: #ffd480; text-align: center; color: black; font-weight: bold; font-size:1.5vw;">
                                                BODY</th>
                                            <th colspan="2"
                                                style="background-color: #85b4ff; text-align: center; color: black; font-weight: bold; font-size:1.5vw;">
                                                ALTO</th>
                                        </tr>
                                        <tr>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                                Check</th>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                                OK</th>
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
                                            <td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="AScheck">0</td>
                                            <td style="background-color: rgb(171, 255, 166); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="ASresult">0</td>
                                            <td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="ASnotGood">0</td>
                                            <td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="ASngRate">0%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="padding-top: 5px;">
                                <table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
                                    <thead>
                                        <tr>
                                            <th colspan="2"
                                                style="background-color: #ffd480; text-align: center; color: black; font-weight: bold; font-size:1.5vw;">
                                                BODY</th>
                                            <th colspan="2"
                                                style="background-color: rgb(157, 255, 105); text-align: center; color: black; font-weight: bold; font-size:1.5vw;">
                                                TENOR</th>
                                        </tr>
                                        <tr>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                                Check</th>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                                OK</th>
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
                                            <td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="TScheck">0</td>
                                            <td style="background-color: rgb(171, 255, 166); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="TSresult">0</td>
                                            <td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="TSnotGood">0</td>
                                            <td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="TSngRate">0%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div>
                                <hr style="border: 2px solid red">
                            </div>

                            <div style="padding-top: 5px;">
                                <table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
                                    <thead>
                                        <tr>
                                            <th colspan="2"
                                                style="background-color: #ffb3ff; text-align: center; color: black; font-weight: bold; font-size:1.5vw;">
                                                BELLBOW</th>
                                            <th colspan="2"
                                                style="background-color: #85b4ff; text-align: center; color: black; font-weight: bold; font-size:1.5vw;">
                                                ALTO</th>
                                        </tr>
                                        <tr>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                                Check</th>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                                OK</th>
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
                                            <td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="AScheckBell">0</td>
                                            <td style="background-color: rgb(171, 255, 166); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="ASresultBell">0</td>
                                            <td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="ASnotGoodBell">0</td>
                                            <td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="ASngRateBell">0%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div style="padding-top: 5px;">
                                <table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
                                    <thead>
                                        <tr>
                                            <th colspan="2"
                                                style="background-color: #ffb3ff; text-align: center; color: black; font-weight: bold; font-size:1.5vw;">
                                                BELLBOW</th>
                                            <th colspan="2"
                                                style="background-color: rgb(157, 255, 105); text-align: center; color: black; font-weight: bold; font-size:1.5vw;">
                                                TENOR</th>
                                        </tr>
                                        <tr>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                                Check</th>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;">
                                                OK</th>
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
                                            <td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="TScheckBell">0</td>
                                            <td style="background-color: rgb(171, 255, 166); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="TSresultBell">0</td>
                                            <td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="TSnotGoodBell">0</td>
                                            <td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="TSngRateBell">0%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div style="padding-top: 5px;">
                                <table class="table table-bordered" style="width: 100%; margin-bottom: 5px;">
                                    <thead>
                                        <tr>
                                            <th colspan="5"
                                                style="background-color: rgb(120, 146, 240); text-align: center; color: white; font-weight: bold; font-size:1.5vw;">
                                                RESUME</th>
                                        </tr>
                                        <tr>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border-bottom: 2px solid black">
                                                Cat</th>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border-bottom: 2px solid black">
                                                Check</th>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border-bottom: 2px solid black">
                                                OK</th>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border-bottom: 2px solid black">
                                                Not Good</th>
                                            <th
                                                style="width:15%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;font-size: 20px;border-bottom: 2px solid black">
                                                Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td
                                                style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 1.5vw;font-weight: bold;border-bottom: 2px solid black">
                                                BODY</td>
                                            <td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1.5vw;border-bottom: 2px solid black"
                                                id="FLcheck">0</td>
                                            <td style="background-color: rgb(171, 255, 166); text-align: center; color: #000000; font-size: 1.5vw;border-bottom: 2px solid black"
                                                id="FLresult">0</td>
                                            <td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 1.5vw;border-bottom: 2px solid black"
                                                id="FLnotGood">0</td>
                                            <td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1.5vw;border-bottom: 2px solid black"
                                                id="FLngRate">0%</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="background-color: rgb(220,220,220); text-align: center; color: black; font-size: 1.5vw;font-weight: bold">
                                                FOOT</td>
                                            <td style="background-color: rgb(204,255,255); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="FLcheckFoot">0</td>
                                            <td style="background-color: rgb(171, 255, 166); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="FLresultFoot">0</td>
                                            <td style="background-color: rgb(255,204,255); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="FLnotGoodFoot">0</td>
                                            <td style="background-color: rgb(255,255,102); text-align: center; color: #000000; font-size: 1.5vw;"
                                                id="FLngRateFoot">0%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCuciEnthol" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <div style="padding-top: 5px;">
                            <input type="hidden" id="enthol_type">
                            <div class="col-xs-10"
                                style="background-color: green;padding: 10px;text-align: center;font-size: 20px;">
                                <span style="color: white;text-align: center;font-weight: bold;" id="title">CUCI
                                    ENTHOLE</span>
                            </div>
                            <div class="col-xs-2" style="padding-left: 0px;padding-right: 0px;">
                                <button id="close"
                                    onclick="$('#modalCuciEnthol').modal('hide');$('#tag').removeAttr('disabled');$('#tag').focus()"
                                    style="font-weight: bold;font-size: 20px;width: 100%;height: 48px;"
                                    class="btn btn-danger pull-right">CLOSE</button>
                            </div>
                            <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
                                <div class="input-group">
                                    <input type="text" style="text-align: center; border-color: black;"
                                        class="form-control input-lg" id="tag_enthol" name="tag_enthol"
                                        placeholder="Scan Kanban ..." required>
                                    <div class="input-group-addon" id="icon-serial"
                                        style="font-weight: bold; border-color: black;">
                                        <i class="glyphicon glyphicon-credit-card"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;margin-top: 10px">
                                <button onclick="fetchEntholLog();" style="font-weight: bold;margin-bottom: 10px;"
                                    class="btn btn-primary"><i class="fa fa-refresh"></i> REFRESH</button>
                                <table class="table table-bordered"
                                    style="width: 100%; margin-bottom: 5px;margin-top: 10px;" id="tableEnthol">
                                    <thead>
                                        <tr>
                                            <th
                                                style="width:5%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">
                                                #</th>
                                            <th
                                                style="width:20%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">
                                                Material</th>
                                            <th
                                                style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">
                                                Loc</th>
                                            <th
                                                style="width:5%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">
                                                Qty</th>
                                            <th
                                                style="width:10%; background-color: rgb(220,220,220); text-align: center; color: black; padding:0;">
                                                At</th>
                                        </tr>
                                    </thead>
                                    <tbody id="bodyEntholLog">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="<?php echo e(url('js/jquery.numpad.js')); ?>"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <!-- <script src="{{ url('js/jqbtk.js') }}"></script> -->

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var hour;
        var minute;
        var second;
        var intervalTime;
        var intervalUpdate;

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

        jQuery(document).ready(function() {
            $('#enthol_type').val('');
            $('#modalOperator').modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#operator").val('');
            $("#operator").focus();
            $('.select2').select2({
                language: {
                    noResults: function(params) {
                        return "There is no date";
                    }
                }
            });
            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });

            $('.numpad2').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });
            $('.datepicker').datepicker({
                <?php $tgl_max = date('Y-m-d'); ?>
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true,
                endDate: '<?php echo $tgl_max; ?>'
            });
            cancelAll();
        });

        function modalPerolehan(employee_id) {
            fetchBodyKensa(employee_id);
        }

        function openModalCuciEnthol(employee_id, type) {
            $('#modalCuciEnthol').modal('show')
            $('#tag').prop('disabled', true);
            $('#modalCuciEnthol').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#enthol_type').val('');
            $('#enthol_type').val(type);
            $('#title').html(type.toUpperCase() + ' CUCI ENTHOL');

            $('#modalCuciEnthol').on('shown.bs.modal', function() {
                $('#tag_enthol').focus();
            });
            fetchEntholLog();
        }

        function fetchEntholLog() {
            $('#loading').show();
            var data = {
                location: $("#location").val(),
            }
            $.get('{{ url('fetch/enthol/log') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#tableEnthol').DataTable().clear();
                    $('#tableEnthol').DataTable().destroy();
                    $('#bodyEntholLog').html('');
                    var bodyEnthol = '';

                    for (var i = 0; i < result.enthol.length; i++) {
                        bodyEnthol += '<tr>';
                        bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">' + (
                            i + 1) + '</td>';
                        bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">' +
                            result.enthol[i].material_number + ' - ' + result.descs[i] + '</td>';
                        bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">' +
                            result.enthol[i].location + '</td>';
                        bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">' +
                            result.enthol[i].quantity + '</td>';
                        bodyEnthol += '<td style="text-align: center; color: #000000;background-color:white">' +
                            result.enthol[i].created_at + '</td>';
                        bodyEnthol += '</tr>';
                    }
                    $('#bodyEntholLog').append(bodyEnthol);
                    $('#loading').hide();

                    var table = $('#tableEnthol').DataTable({
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
                        "processing": true
                    });
                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function cancelAll() {
            $('#tag').removeAttr('disabled');
            $('#tag').val('');
            $('#material_number').html('-');
            $('#material_description').html('-');
            // $('#model').html('-');
            // $('#hpl').html('-');
            $('#quantity').html('-');
            $("#tag").focus();
            $('#ng_count').html('-');
            $('#ng_before').html('-');
            count_ng = 0;
            var loop = $('#loop').val();
            for (var i = 1; i <= loop; i++) {
                $('#count' + i).html(0);
            }

            $('#count_new').val(0);
            $('#count_rework').val(0);

        }

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

        $('#modalOperator').on('shown.bs.modal', function() {
            $('#operator').focus();
        });

        $('#operator').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                if ($("#operator").val().length >= 8) {
                    var data = {
                        employee_id: $("#operator").val(),
                    }

                    $.get('{{ url('scan/body/operator') }}', data, function(result, status, xhr) {
                        if (result.status) {
                            openSuccessGritter('Success!', result.message);
                            $('#modalOperator').modal('hide');
                            $('#op').html(result.employee.employee_id);
                            $('#op2').html(result.employee.name);
                            $('#employee_id').val(result.employee.employee_id);
                            $('#tag').focus();
                            $('#tag').val('');
                            // fetchBodyKensa(result.employee.employee_id);
                        } else {
                            audio_error.play();
                            openErrorGritter('Error', result.message);
                            $('#operator').val('');
                        }
                    });
                } else {
                    openErrorGritter('Error!', 'Employee ID Invalid.');
                    audio_error.play();
                    $("#operator").val("");
                }
            }
        });

        $('#tag').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                if ($("#tag").val().length >= 8) {
                    var data = {
                        tag: $("#tag").val(),
                        loc: $("#location").val(),
                    }

                    $.get('{{ url('scan/body/kanban') }}', data, function(result, status, xhr) {
                        if (result.status) {
                            openSuccessGritter('Success!', result.message);
                            $('#tag').prop('disabled', true);
                            $('#material_number').html(result.tags.material_number);
                            $('#material_description').html(result.tags.material_description);
                            // $('#model').html(result.tags.model);
                            // $('#hpl').html(result.tags.hpl);
                            $('#quantity').html(result.tags.quantity);

                            if (result.temp_ng.qty_ng != null) {
                                $('#ng_before').html(result.temp_ng.qty_ng);
                            } else {
                                $('#ng_before').html(0);
                            }
                        } else {
                            audio_error.play();
                            openErrorGritter('Error', result.message);
                            $('#tag').removeAttr('disabled');
                            $('#tag').val('');
                        }
                    });
                } else {
                    openErrorGritter('Error!', 'Tag Invalid.');
                    audio_error.play();
                    $('#tag').removeAttr('disabled');
                    $('#tag').val('');
                }
            }
        });

        $('#tag_enthol').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                if ($("#tag_enthol").val().length >= 8) {
                    var data = {
                        tag: $("#tag_enthol").val(),
                        loc: $("#location").val(),
                        employee_id: $('#op').text(),
                        type: $('#enthol_type').val(),
                    }

                    $.get('{{ url('scan/enthol/kanban') }}', data, function(result, status, xhr) {
                        if (result.status) {
                            openSuccessGritter('Success!', result.message);
                            $('#tag_enthol').val('');
                            $('#tag_enthol').focus();
                        } else {
                            audio_error.play();
                            openErrorGritter('Error', result.message);
                            $('#tag_enthol').val('');
                        }
                    });
                } else {
                    openErrorGritter('Error!', 'Tag Invalid.');
                    audio_error.play();
                    $('#tag').removeAttr('disabled');
                    $('#tag').val('');
                }
            }
        });

        function fetchBodyKensa(employee_id) {
            $('#loading').show();
            var data = {
                employee_id: employee_id,
                location: $("#location").val(),
            }

            $.get('{{ url('fetch/body/kensa') }}', data, function(result, status, xhr) {
                if (result.status) {
                    var checks = 0;
                    var checks_foot = 0;
                    var check_as = 0;
                    var check_ts = 0;
                    var check_as_bell = 0;
                    var check_ts_bell = 0;
                    var results = 0;
                    var results_foot = 0;
                    var result_as = 0;
                    var result_ts = 0;
                    var result_as_bell = 0;
                    var result_ts_bell = 0;
                    var ng = 0;
                    var ng_foot = 0;
                    var ng_as = 0;
                    var ng_ts = 0;
                    var ng_as_bell = 0;
                    var ng_ts_bell = 0;
                    var ratio = 0;
                    var ratio_as_bell = 0;
                    var ratio_ts_bell = 0;
                    var location = $("#location").val();


                    for (var i = 0; i < result.results.length; i++) {
                        if (location.match(/sx/gi)) {
                            if (result.results[i].model == null) {
                                if (result.results[i].key.match(/BODY/gi)) {
                                    check_as = check_as + parseInt(result.results[i].check);
                                    result_as = result_as + parseInt(result.results[i].ok);
                                    ng_as = ng_as + parseInt(result.results[i].ng);
                                } else {
                                    check_as_bell = check_as_bell + parseInt(result.results[i].check);
                                    result_as_bell = result_as_bell + parseInt(result.results[i].ok);
                                    ng_as_bell = ng_as_bell + parseInt(result.results[i].ng);
                                }
                            } else if (result.results[i].model.charAt(0) == 'A') {
                                if (result.results[i].key.match(/BODY/gi)) {
                                    check_as = check_as + parseInt(result.results[i].check);
                                    result_as = result_as + parseInt(result.results[i].ok);
                                    ng_as = ng_as + parseInt(result.results[i].ng);
                                } else {
                                    check_as_bell = check_as_bell + parseInt(result.results[i].check);
                                    result_as_bell = result_as_bell + parseInt(result.results[i].ok);
                                    ng_as_bell = ng_as_bell + parseInt(result.results[i].ng);
                                }
                            } else if (result.results[i].model.charAt(0) == 'T') {
                                if (result.results[i].key.match(/BODY/gi)) {
                                    check_ts = check_ts + parseInt(result.results[i].check);
                                    result_ts = result_ts + parseInt(result.results[i].ok);
                                    ng_ts = ng_ts + parseInt(result.results[i].ng);
                                } else {
                                    check_ts_bell = check_ts_bell + parseInt(result.results[i].check);
                                    result_ts_bell = result_ts_bell + parseInt(result.results[i].ok);
                                    ng_ts_bell = ng_ts_bell + parseInt(result.results[i].ng);
                                }
                            } else if (result.results[i].model == 'YDS') {
                                if (result.results[i].key.match(/BODY/gi)) {
                                    check_as = check_as + parseInt(result.results[i].check);
                                    result_as = result_as + parseInt(result.results[i].ok);
                                    ng_as = ng_as + parseInt(result.results[i].ng);
                                } else {
                                    check_as_bell = check_as_bell + parseInt(result.results[i].check);
                                    result_as_bell = result_as + parseInt(result.results[i].ok);
                                    ng_as_bell = ng_as_bell + parseInt(result.results[i].ng);
                                }
                            } else {
                                if (result.results[i].key.match(/BODY/gi)) {
                                    check_as = check_as + parseInt(result.results[i].check);
                                    result_as = result_as + parseInt(result.results[i].ok);
                                    ng_as = ng_as + parseInt(result.results[i].ng);
                                } else {
                                    check_as_bell = check_as_bell + parseInt(result.results[i].check);
                                    result_as_bell = result_as + parseInt(result.results[i].ok);
                                    ng_as_bell = ng_as_bell + parseInt(result.results[i].ng);
                                }
                            }
                        } else {
                            if (result.results[i].key == 'BODY') {
                                checks = checks + parseInt(result.results[i].check);
                                results = results + parseInt(result.results[i].ok);
                                ng = ng + parseInt(result.results[i].ng);
                            } else if (result.results[i].key == 'FOOT') {
                                checks_foot = checks_foot + parseInt(result.results[i].check);
                                results_foot = results_foot + parseInt(result.results[i].ok);
                                ng_foot = ng_foot + parseInt(result.results[i].ng);
                            } else {
                                checks = checks + parseInt(result.results[i].check);
                                results = results + parseInt(result.results[i].ok);
                                ng = ng + parseInt(result.results[i].ng);
                            }
                        }
                    }

                    // for (var i = 0; i < result.ng.length; i++) {
                    // 	if (location.match(/sx/gi)) {
                    // 		if(result.ng[i].model == null){
                    // 			// if (result.ng[i].quantity_lot == 1) {
                    // 			// 	ng_as = ng_as + parseInt(result.ng[i].quantity);
                    // 			// }else{
                    // 				ng_as = ng_as + parseInt(result.ng[i].quantity);
                    // 			// }
                    // 		}else if (result.ng[i].model.charAt(0) == 'A') {
                    // 			// if (result.ng[i].quantity_lot == 1) {
                    // 			// 	ng_as = ng_as + parseInt(result.ng[i].quantity);
                    // 			// }else{
                    // 				ng_as = ng_as + parseInt(result.ng[i].quantity);
                    // 			// }
                    // 		}else if(result.ng[i].model.charAt(0) == 'T'){
                    // 			// if (result.ng[i].quantity_lot == 1) {
                    // 			// 	ng_ts = ng_ts + parseInt(result.ng[i].quantity);
                    // 			// }else{
                    // 				ng_ts = ng_ts + parseInt(result.ng[i].quantity);
                    // 			// }
                    // 		}else if(result.ng[i].model == 'YDS'){
                    // 			// if (result.ng[i].quantity_lot == 1) {
                    // 			// 	ng_as = ng_as + parseInt(result.ng[i].quantity);
                    // 			// }else{
                    // 				ng_as = ng_as + parseInt(result.ng[i].quantity);
                    // 			// }
                    // 		}else{
                    // 			// if (result.ng[i].quantity_lot == 1) {
                    // 			// 	ng_as = ng_as + parseInt(result.ng[i].quantity);
                    // 			// }else{
                    // 				ng_as = ng_as + parseInt(result.ng[i].quantity);
                    // 			// }
                    // 		}
                    // 	}else{
                    // 		ng = ng + parseInt(result.ng[i].quantity);
                    // 	}
                    // }

                    if (location.match(/sx/gi)) {

                        //BODY

                        $('#AScheck').html(check_as);
                        $('#ASresult').html(result_as);
                        $('#ASnotGood').html(ng_as);

                        if (check_as == 0 && result_as == 0 && ng_as == 0) {
                            $('#ASngRate').html('0 %');
                        } else {
                            $('#ASngRate').html(((ng_as / check_as) * 100).toFixed(1) + ' %');
                        }

                        $('#TScheck').html(check_ts);
                        $('#TSresult').html(result_ts);
                        $('#TSnotGood').html(ng_ts);
                        // $('#TSngRate').html(((ng_ts/check_ts)*100).toFixed(1)+' %');

                        if (check_ts == 0 && result_ts == 0 && ng_ts == 0) {
                            $('#TSngRate').html('0 %');
                        } else {
                            $('#TSngRate').html(((ng_ts / check_ts) * 100).toFixed(1) + ' %');
                        }

                        //BELL

                        $('#AScheckBell').html(check_as_bell);
                        $('#ASresultBell').html(result_as_bell);
                        $('#ASnotGoodBell').html(ng_as_bell);

                        if (check_as_bell == 0 && result_as_bell == 0 && ng_as_bell == 0) {
                            $('#ASngRateBell').html('0 %');
                        } else {
                            $('#ASngRateBell').html(((ng_as_bell / check_as_bell) * 100).toFixed(1) + ' %');
                        }

                        $('#TScheckBell').html(check_ts_bell);
                        $('#TSresultBell').html(result_ts_bell);
                        $('#TSnotGoodBell').html(ng_ts_bell);
                        // $('#TSngRate').html(((ng_ts/check_ts)*100).toFixed(1)+' %');

                        if (check_ts_bell == 0 && result_ts_bell == 0 && ng_ts_bell == 0) {
                            $('#TSngRateBell').html('0 %');
                        } else {
                            $('#TSngRateBell').html(((ng_ts_bell / check_ts_bell) * 100).toFixed(1) + ' %');
                        }
                    } else {
                        $('#FLcheck').html(checks);
                        $('#FLresult').html(results);
                        $('#FLnotGood').html(ng);
                        if (checks == 0 && results == 0 && ng == 0) {
                            $('#FLngRate').html('0 %');
                        } else {
                            $('#FLngRate').html(((ng / checks) * 100).toFixed(1) + ' %');
                        }

                        $('#FLcheckFoot').html(checks_foot);
                        $('#FLresultFoot').html(results_foot);
                        $('#FLnotGoodFoot').html(ng_foot);
                        if (checks_foot == 0 && results_foot == 0 && ng_foot == 0) {
                            $('#FLngRateFoot').html('0 %');
                        } else {
                            $('#FLngRateFoot').html(((ng_foot / checks_foot) * 100).toFixed(1) + ' %');
                        }
                    }
                    $('#modalPerolehan').modal('show');
                    $('#loading').hide();
                } else {
                    audio_error.play();
                    openErrorGritter('Error', result.message);
                    return false;
                }
            });
        }


        var count_ng = 0;

        function plus(id) {
            var count = $('#count' + id).text();
            if ($('#material_number').text() != "-") {
                $('#count' + id).text(parseInt(count) + 1);
                count_ng++;
                $('#ng_count').html(count_ng);
            } else {
                audio_error.play();
                openErrorGritter('Error!', 'Scan material first.');
                $("#tag").val("");
                $("#tag").focus();
            }
        }

        function minus(id) {
            var count = $('#count' + id).text();
            if ($('#material_number').text() != "-") {
                if (count > 0) {
                    $('#count' + id).text(parseInt(count) - 1);
                    count_ng--;
                    $('#ng_count').html(count_ng);
                }
            } else {
                audio_error.play();
                openErrorGritter('Error!', 'Scan material first.');
                $("#tag").val("");
                $("#tag").focus();
            }
        }

        function plus_type(id) {
            var count = $('#count_' + id).val();
            if ($('#material_number').text() != "-") {
                $('#count_' + id).val(parseInt(count) + 1);
            } else {
                audio_error.play();
                openErrorGritter('Error!', 'Scan material first.');
                $("#tag").val("");
                $("#tag").focus();
            }
        }

        function minus_type(id) {
            var count = $('#count_' + id).val();
            if ($('#material_number').text() != "-") {
                if (parseInt(count) >= 1) {
                    $('#count_' + id).val(parseInt(count) - 1);
                } else {
                    audio_error.play();
                    openErrorGritter('Error!', 'Qty < 0');
                }
            } else {
                audio_error.play();
                openErrorGritter('Error!', 'Scan material first.');
                $("#tag").val("");
                $("#tag").focus();
            }
        }

        function confirmNgLog() {
            if ($('#material_number').text() == "-") {
                audio_error.play();
                openErrorGritter('Error!', 'Scan material first.');
                $("#tag").val("");
                $("#tag").focus();
                return false;
            }

            $('#loading').show();
            var tag = $("#tag").val();
            var material_number = $('#material_number').text();
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
            var count_rework = $('#count_rework').val();
            var loc = $('#location').val();
            var model = $('#model').text();
            var employee_id = $('#op').text();
            var data = {
                tag: tag,
                material_number: material_number,
                ng: ng,
                loc: loc,
                model: model,
                employee_id: employee_id,
                count_rework: count_rework,
            }

            $.post('{{ url('input/body/kanban') }}', data, function(result, status, xhr) {
                if (result.status) {
                    cancelAll();
                    $('#loading').hide();
                    // fetchBodyKensa($('#op').text());
                    openSuccessGritter('Success!', result.message);
                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                    audio_error.play();
                    return false;
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

        function addZero(i) {
            if (i < 10) {
                i = "0" + i;
            }
            return i;
        }

        function getActualFullDate() {
            var d = new Date();
            var day = addZero(d.getDate());
            var month = addZero(d.getMonth() + 1);
            var year = addZero(d.getFullYear());
            var h = addZero(d.getHours());
            var m = addZero(d.getMinutes());
            var s = addZero(d.getSeconds());
            return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
        }
    </script>
@endsection
