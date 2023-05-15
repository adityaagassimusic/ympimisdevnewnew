@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        #tableBodyList>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c;
        }

        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        table {
            table-layout: fixed;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        td:hover {
            overflow: visible;
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
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        #loading {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}<span class="text-purple"> {{ $title_jp }}</span>
            <small id="location_text"></small>
        </h1>

        <ol class="breadcrumb">
            <li>
                <a data-toggle="modal" data-target="#modalReqOut" class="btn btn-primary btn-sm" style="color: white;">
                    &nbsp;<i class="fa fa-shopping-cart"></i>&nbsp;Scan Out
                </a>

                <a data-toggle="modal" data-target="#modalOut" class="btn btn-danger btn-sm" style="color: white;">
                    &nbsp;<i class="fa fa-trash"></i>&nbsp;Scan Habis
                </a>
            </li>
        </ol>

    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box box-danger">
                            <div class="box-body">
                                <div class="col-xs-12">
                                    <table class="table table-hover table-bordered table-striped" id="tableList">
                                        <thead style="background-color: rgba(126,86,134,.7);">
                                            <tr>
                                                <th style="width: 10%;">Due date</th>
                                                <th style="width: 15%;">Category</th>
                                                <th style="width: 20%;">Larutan/Bak</th>
                                                <th style="width: 10%;">Material</th>
                                                <th style="width: 20%;">Description</th>
                                                <th style="width: 15%;">Storage Loc.</th>
                                                <th style="width: 5%;">Qty</th>
                                                <th style="width: 5%;">Bun</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBodyList">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-5">
                        <div class="row">
                            <div class="col-xs-12">
                                <span style="font-weight: bold; font-size: 20px;">Request</span>
                                <input type="text" id="schedule_id" hidden>
                            </div>
                            <div class="col-xs-12">
                                <span style="font-weight: bold; font-size: 16px;">Larutan/Bak:</span>
                                <input type="text" id="larutan"
                                    style="width: 100%; height: 50px; font-size: 20px; text-align: center;" readonly>
                            </div>


                            <div class="col-xs-7">
                                <span style="font-weight: bold; font-size: 16px;">Category:</span>
                                <input type="text" id="category"
                                    style="width: 100%; height: 50px; font-size: 20px; text-align: center;" readonly>
                            </div>

                            <div class="col-xs-5">
                                <span style="font-weight: bold; font-size: 16px;">Material Number:</span>
                                <input type="text" id="material_number"
                                    style="width: 100%; height: 50px; font-size: 20px; text-align: center;" readonly>
                            </div>

                            <div class="col-xs-7">
                                <span style="font-weight: bold; font-size: 16px;">Storage Location:</span>
                                <input type="text" id="storage_location"
                                    style="width: 100%; height: 50px; font-size: 20px; text-align: center;" readonly>
                            </div>
                            <div class="col-xs-3" style="padding-right: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Request Qty:</span>
                                <input type="text" id="quantity"
                                    style="width: 100%; height: 50px; font-size: 20px; text-align: center;" readonly>
                            </div>
                            <div class="col-xs-2" style="padding-left: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">&nbsp;</span>
                                <input type="text" id="bun"
                                    style="width: 100%; height: 50px; font-size: 20px; text-align: center;" readonly>
                            </div>

                            <div class="col-xs-12">
                                <span style="font-weight: bold; font-size: 16px;">Material Description:</span>
                                <input type="text" id="material_description"
                                    style="width: 100%; height: 50px; font-size: 18px; text-align: center;" readonly>
                                {{-- <input type="text" id="license" style="width: 100%; height: 25px; font-size: 14px; font-weight: bold; font-style: italic; text-align: center; background-color: #e7db60;" value="License : YS100" readonly> --}}
                            </div>

                            <div class="col-xs-12">
                                <span style="font-weight: bold; font-size: 20px;">Stock</span>
                            </div>
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Stock Qty (MSTK):</span>
                            </div>
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Sisa Stock (Process):</span>
                            </div>
                            <div class="col-xs-3" style="padding-right: 0px;">
                                <input type="text" id="stock"
                                    style="width: 100%; height: 50px; font-size: 20px; text-align: center;" readonly>
                            </div>
                            <div class="col-xs-3" style="padding-left: 0px;">
                                <input type="text" id="stock_uom"
                                    style="width: 100%; height: 50px; font-size: 20px; text-align: center;" readonly>
                            </div>
                            <div class="col-xs-3" style="padding-right: 0px;">
                                <input type="text" id="out"
                                    style="width: 100%; height: 50px; font-size: 20px; text-align: center;" readonly>
                            </div>
                            <div class="col-xs-3" style="padding-left: 0px;">
                                <input type="text" id="out_uom"
                                    style="width: 100%; height: 50px; font-size: 20px; text-align: center;" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-7" style="padding-left: 0px;">
                        <div class="row">
                            <div class="col-xs-12" style="margin-top: 5%;">
                                <br>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#modalScan"
                                    style="font-size: 30px; width: 100%; font-weight: bold; padding: 0;"><span><i
                                            class="fa fa-camera"></i></span>&nbsp;&nbsp;&nbsp;SCAN
                                </button>
                            </div>

                            <div class="col-xs-12">
                                <table id="pick" class="table table-bordered table-hover"
                                    style="width: 100%; font-size: 0.8vw;">
                                    <thead style="background-color: white">
                                        <tr>
                                            <th style="width: 10%">Status</th>
                                            <th style="width: 20%">QR Code</th>
                                            <th style="width: 15%">Material</th>
                                            <th style="width: 35%">Description</th>
                                            <th style="width: 10%">Pick</th>
                                            <th style="width: 12%">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pick-body" style="background-color: white">
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-xs-12" style="margin-top: 5%;">
                                <button class="btn btn-success" onclick="saveChm()"
                                    style="font-size: 30px; width: 100%; font-weight: bold; padding: 0;">SUBMIT
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalLocation">
        <div class="modal-dialog" style="width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2 style="background-color: #605ca8; color: white; font-weight: bold; padding: 1%;">SELECT
                            LOCATION</h2>
                    </center>
                </div>
                <div class="modal-body table-responsive">
                    <div class="row">
                        <div class="col-xs-12">
                            @foreach ($locations as $row)
                                <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3">
                                    <div class="row">
                                        <input type="text" id="location" hidden>
                                        <button style="margin-top: 20px; font-weight: bold; width: 90%;"
                                            class="btn btn-success"
                                            onclick="changeLoc('{{ $row->location }}')">{{ $row->location }}</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalScan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2 style="background-color: #605ca8; padding: 1%;">Scan QR Code</h2>
                    </center>
                </div>
                <div class="modal-body table-responsive">
                    <center>
                        <input id="code" type="text"
                            style="border:0; width: 70%; text-align: center; height: 20px; color: white; background-color: #3c3c3c; height: 20px;">
                    </center>

                    <div id='scanner' class="col-xs-12">
                        <center>
                            <div id="loadingMessage">
                                🎥 Unable to access video stream
                                (please make sure you have a webcam enabled)
                            </div>
                            <video autoplay muted playsinline id="video"></video>
                            <div id="output" hidden>
                                <div id="outputMessage">No QR code detected.</div>
                            </div>
                        </center>
                    </div>

                    <p style="visibility: hidden;">camera</p>
                    <input type="hidden" id="code">
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalOut">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2 style="background-color: #f44336; padding: 1%;">Scan QR Code Empty Chemical</h2>
                    </center>
                </div>
                <div class="modal-body table-responsive">
                    <center>
                        <input id="code_out" type="text"
                            style="border:0; width: 70%; text-align: center; height: 20px; color: white; background-color: #3c3c3c; height: 20px;">
                    </center>

                    <div id='scannerOut' class="col-xs-12">
                        <center>
                            <div id="loadingMessageOut">
                                🎥 Unable to access video stream
                                (please make sure you have a webcam enabled)
                            </div>
                            <video autoplay muted playsinline id="videoOut"></video>
                            <div id="outputOut" hidden>
                                <div id="outputMessageOut">No QR code detected.</div>
                            </div>
                        </center>
                    </div>

                    <p style="visibility: hidden;">camera</p>
                    <input type="hidden" id="codeOut">
                    <div id="confirmOut" style="width:100%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalReqOut">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2 style="background-color: #7b83eb; padding: 1%;">Scan QR Code Chemical</h2>
                    </center>
                </div>
                <div class="modal-body table-responsive">
                    <center>
                        <input id="code_req_out" type="text"
                            style="border:0; width: 70%; text-align: center; height: 20px; color: white; background-color: #3c3c3c; height: 20px;">
                    </center>

                    <div id='scannerReqOut' class="col-xs-12">
                        <center>
                            <div id="loadingMessageReqOut">
                                🎥 Unable to access video stream
                                (please make sure you have a webcam enabled)
                            </div>
                            <video autoplay muted playsinline id="videoReqOut"></video>
                            <div id="outputReqOut" hidden>
                                <div id="outputMessageReqOut">No QR code detected.</div>
                            </div>
                        </center>
                    </div>

                    <p style="visibility: hidden;">camera</p>
                    <input type="hidden" id="codeReqOut">
                    <div id="confirmReqOut" style="width:100%;"></div>
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
    <script src="{{ url('js/jsQR.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.select2').select2({
                allowClear: true,
            });

            $('#modalLocation').modal({
                backdrop: 'static',
                keyboard: false
            });

        });

        var video;

        function stopScan() {
            $('#modalScan').modal('hide');
            $('#modalOut').modal('hide');
        }

        function videoOff() {
            video.pause();
            video.src = "";
            video.srcObject.getTracks()[0].stop();
        }

        function videoOn() {
            video.pause();
            video.src = "";
            video.srcObject.getTracks()[0].stop();
        }



        $('#modalOut').on('shown.bs.modal', function() {
            showOut('123');
            $('#code_out').val('');
            $('#confirmOut').html("");
        });

        $('#modalReqOut').on('shown.bs.modal', function() {
            showReqOut('123');
            $('#code_req_out').val('');
            $('#confirmReqOut').html("");
        });

        $('#modalOut').on('hidden.bs.modal', function() {
            videoOff();
        });



        function showOut(kode) {
            console.log('out');

            $(".modal-backdrop").add();
            $('#scannerOut').show();

            var vdo = document.getElementById("videoOut");
            video = vdo;
            var tickDuration = 200;
            video.style.boxSizing = "border-box";
            video.style.position = "absolute";
            video.style.left = "0px";
            video.style.top = "0px";
            video.style.width = "400px";
            video.style.zIndex = 1000;

            var loadingMessage = document.getElementById("loadingMessageOut");
            var outputContainer = document.getElementById("outputOut");
            var outputMessage = document.getElementById("outputMessageOut");

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                }
            }).then(function(stream) {
                video.srcObject = stream;
                video.play();
                setTimeout(function() {
                    tick();
                }, tickDuration);
            });

            function tick() {
                loadingMessage.innerText = "⌛ Loading video..."

                try {

                    loadingMessage.hidden = true;
                    video.style.position = "static";

                    var canvasElement = document.createElement("canvas");
                    var canvas = canvasElement.getContext("2d");
                    canvasElement.height = video.videoHeight;
                    canvasElement.width = video.videoWidth;
                    canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                    var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                    var code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert"
                    });
                    if (code) {
                        outputMessage.hidden = true;
                        videoOff();
                        console.log('out');
                        checkOut(code.data);

                    } else {
                        outputMessage.hidden = false;
                    }
                } catch (t) {
                    console.log("PROBLEM: " + t);
                }

                setTimeout(function() {
                    tick();
                }, tickDuration);
            }
        }

        $('#code_out').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                var code_out = $('#code_out').val();
                checkOut(code_out);
            }
        });

        function checkOut(code) {
            $('#scannerOut').hide();

            var location = $('#location').val();

            var data = {
                qr: code,
                location: location
            }

            $.get('{{ url('fetch/check_out') }}', data, function(result, status, xhr) {
                if (result.status) {
                    var re = "";
                    $('#confirmOut').html("");
                    re += '<table style="text-align: center; width:100%;"><tbody>';
                    re +=
                        '<tr><td style="font-size: 36px; font-weight: bold; background-color:black; color:white;" colspan="2">' +
                        result.data.qr_code + '</td></tr>';
                    re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">' + result.data
                        .material_number + '</td></tr>';
                    re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">' + result.data
                        .material_description + '</td></tr>';
                    re += '<tr>';
                    re +=
                        '<td><button class="btn btn-danger" style="width: 95%; font-size: 30px; font-weight:bold;" data-dismiss="modal">CANCEL</button></td>';
                    re += '<td><button id="' + result.data.qr_code +
                        '" class="btn btn-success" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmOut(id)">SUBMIT</button></td>';
                    re += '</tr>';
                    re += '</tbody></table>';

                    $('#confirmOut').append(re);
                } else {
                    $('#confirmOut').html("");
                    showCheck();
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }

            });
        }

        function confirmOut(id) {
            var location = $('#location').val();

            var data = {
                qr: id,
                location: location,

            }

            $("#loading").show();

            $.post('{{ url('delete/chm_out') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#scanner').hide();
                    $('#modalOut').modal('hide');
                    $(".modal-backdrop").remove();

                    $("#loading").hide();
                    openSuccessGritter('Success', result.message);
                } else {
                    $("#loading").hide();
                    openErrorGritter('Error!', result.message);
                }
            });
        }



        function showReqOut(kode) {
            console.log('Reqout');

            $(".modal-backdrop").add();
            $('#scannerReqOut').show();

            var vdo = document.getElementById("videoReqOut");
            video = vdo;
            var tickDuration = 200;
            video.style.boxSizing = "border-box";
            video.style.position = "absolute";
            video.style.left = "0px";
            video.style.top = "0px";
            video.style.width = "400px";
            video.style.zIndex = 1000;

            var loadingMessage = document.getElementById("loadingMessageReqOut");
            var outputContainer = document.getElementById("outputReqOut");
            var ReqoutputMessage = document.getElementById("outputMessageReqOut");

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                }
            }).then(function(stream) {
                video.srcObject = stream;
                video.play();
                setTimeout(function() {
                    tick();
                }, tickDuration);
            });

            function tick() {
                loadingMessage.innerText = "⌛ Loading video..."

                try {

                    loadingMessage.hidden = true;
                    video.style.position = "static";

                    var canvasElement = document.createElement("canvas");
                    var canvas = canvasElement.getContext("2d");
                    canvasElement.height = video.videoHeight;
                    canvasElement.width = video.videoWidth;
                    canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                    var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                    var code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert"
                    });
                    if (code) {
                        outputMessage.hidden = true;
                        videoOff();
                        console.log('out');
                        checkReqOut(code.data);

                    } else {
                        outputMessage.hidden = false;
                    }
                } catch (t) {
                    console.log("PROBLEM: " + t);
                }

                setTimeout(function() {
                    tick();
                }, tickDuration);
            }
        }

        $('#code_req_out').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                var code_out = $('#code_req_out').val();
                checkReqOut(code_out);
            }
        });

        function checkReqOut(code) {
            console.log('checkReqOut_' + code);
            $('#scannerReqOut').hide();

            var location = $('#location').val();

            var data = {
                qr: code,
                location: location
            }

            $.get('{{ url('fetch/check_req_out') }}', data, function(result, status, xhr) {
                if (result.status) {
                    var re = "";
                    $('#confirmOut').html("");
                    re += '<table style="text-align: center; width:100%;"><tbody>';
                    re +=
                        '<tr><td style="font-size: 36px; font-weight: bold; background-color:black; color:white;" colspan="2">' +
                        result.data.qr_code + '</td></tr>';
                    re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">' + result.data
                        .material_number + '</td></tr>';
                    re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">' + result.data
                        .material_description + '</td></tr>';
                    re += '<tr>';
                    re +=
                        '<td><button class="btn btn-danger" style="width: 95%; font-size: 30px; font-weight:bold;" data-dismiss="modal">CANCEL</button></td>';
                    re += '<td><button id="' + result.data.qr_code +
                        '" class="btn btn-success" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReqOut(id)">SUBMIT</button></td>';
                    re += '</tr>';
                    re += '</tbody></table>';

                    $('#confirmReqOut').append(re);
                } else {
                    $('#confirmReqOut').html("");
                    showCheck();
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }

            });
        }

        function confirmReqOut(id) {
            var location = $('#location').val();

            var data = {
                qr: id,
                location: location,

            }

            $("#loading").show();

            $.post('{{ url('confirm/chm_req_out') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#scanner').hide();
                    $('#modalReqOut').modal('hide');
                    $(".modal-backdrop").remove();

                    $("#loading").hide();
                    openSuccessGritter('Success', result.message);
                } else {
                    $("#loading").hide();
                    openErrorGritter('Error!', result.message);
                }
            });
        }



        $('#modalScan').on('shown.bs.modal', function() {
            showCheck('123');
        });

        $('#modalScan').on('hidden.bs.modal', function() {
            videoOff();
        });

        function showCheck(kode) {
            console.log('cek');

            $(".modal-backdrop").add();
            $('#scanner').show();

            var vdo = document.getElementById("video");
            video = vdo;
            var tickDuration = 200;
            video.style.boxSizing = "border-box";
            video.style.position = "absolute";
            video.style.left = "0px";
            video.style.top = "0px";
            video.style.width = "400px";
            video.style.zIndex = 1000;

            var loadingMessage = document.getElementById("loadingMessage");
            var outputContainer = document.getElementById("output");
            var outputMessage = document.getElementById("outputMessage");

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                }
            }).then(function(stream) {
                video.srcObject = stream;
                video.play();
                setTimeout(function() {
                    tick();
                }, tickDuration);
            });

            function tick() {
                loadingMessage.innerText = "⌛ Loading video..."

                try {

                    loadingMessage.hidden = true;
                    video.style.position = "static";

                    var canvasElement = document.createElement("canvas");
                    var canvas = canvasElement.getContext("2d");
                    canvasElement.height = video.videoHeight;
                    canvasElement.width = video.videoWidth;
                    canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                    var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                    var code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert"
                    });
                    if (code) {
                        outputMessage.hidden = true;
                        videoOff();
                        console.log('out');
                        checkQR(code.data);

                    } else {
                        outputMessage.hidden = false;
                    }
                } catch (t) {
                    console.log("PROBLEM: " + t);
                }

                setTimeout(function() {
                    tick();
                }, tickDuration);
            }


        }

        function checkQR(code) {
            var material_number = $('#material_number').val();
            var location = $('#location').val();
            var schedule_id = $('#schedule_id').val();

            if (material_number != '' && location != '' && schedule_id != '') {
                var data = {
                    qr: code,
                    material_number: material_number,
                    location: location,
                    schedule_id: schedule_id
                }

                var data = {
                    qr: code,
                    material_number: material_number,
                    location: location,
                    schedule_id: schedule_id
                }

                $("#loading").show();

                $.get('{{ url('fetch/check_qr') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fillPicked();
                        $("#loading").hide();
                        openSuccessGritter('Success', result.message);

                    } else {
                        $("#loading").hide();
                        openErrorGritter('Error!', result.message);
                    }

                });

            } else {
                $('#code').val('');
                openErrorGritter('Success', 'Pilih schedule terlebih dahulu');
            }

            $('#scanner').hide();
            $('#modalScan').modal('hide');
            $(".modal-backdrop").remove();
        }

        $('#code').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                var code = $('#code').val();
                manualCheckQR(code);
            }
        });

        function manualCheckQR(code) {
            var material_number = $('#material_number').val();
            var location = $('#location').val();
            var schedule_id = $('#schedule_id').val();

            if (material_number != '' && location != '' && schedule_id != '') {
                var data = {
                    qr: code,
                    material_number: material_number,
                    location: location,
                    schedule_id: schedule_id
                }

                $("#loading").show();

                $.get('{{ url('fetch/check_qr') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fillPicked();
                        $("#loading").hide();
                        $('#code').val('');
                        openSuccessGritter('Success', result.message);

                    } else {
                        $("#loading").hide();
                        $('#code').val('');
                        openErrorGritter('Error!', result.message);
                    }

                });
            } else {
                $('#code').val('');
                openErrorGritter('Success', 'Pilih schedule terlebih dahulu');
            }


        }

        function fillPicked() {
            // $('#pick').show();

            var location = $('#location').val();
            var schedule_id = $('#schedule_id').val();

            var data = {
                location: location,
                schedule_id: schedule_id
            }

            $('#pick').DataTable().destroy();
            var table = $('#pick').DataTable({
                'paging': true,
                'dom': 'Bfrtip',
                'responsive': true,
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'buttons': {
                    buttons: []
                },
                'lengthChange': true,
                'searching': false,
                'ordering': false,
                'info': false,
                'autoWidth': true,
                "paging": false,
                "bJQueryUI": true,
                "bPaginate": false,
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/chm_picked') }}",
                    "data": data,
                },
                "columns": [{
                        "data": "remark"
                    },
                    {
                        "data": "qr_code"
                    },
                    {
                        "data": "material_number"
                    },
                    {
                        "data": "material_description"
                    },
                    {
                        "data": "qty_bun"
                    },
                    {
                        "data": "delete"
                    }
                ]
            });
        }

        function deletePicked(id) {
            data = {
                id: id
            }

            if (confirm("Apa anda yakin akan menghapus data?")) {
                $.post('{{ url('delete/chm_picked') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fillPicked();
                        openSuccessGritter('Success', result.message);

                    } else {
                        openErrorGritter('Error!', result.message);
                    }
                });
            }
        }

        function saveChm() {
            var location = $('#location').val();
            var schedule_id = $('#schedule_id').val();

            var data = {
                location: location,
                schedule_id: schedule_id
            }


            if (confirm(
                    "Apa anda ingin yakin menyimpan pengambilan chemical ?\nData yang disimpan tidak bisa dikembalikan")) {
                $("#loading").show();
                $.post('{{ url('input/chm_picked') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fillTableList();
                        fillPicked();

                        $('#schedule_id').val('');
                        $('#category').val('');
                        $('#larutan').val('');
                        $('#material_number').val('');
                        $('#quantity').val('');
                        $('#bun').val('');
                        $('#material_description').val('');
                        $('#storage_location').val('');
                        $('#stock').val('');
                        $('#stock_uom').val('');
                        $('#out').val('');
                        $('#out_uom').val('');

                        $('#license').css('display', 'none');


                        $("#loading").hide();
                        openSuccessGritter('Success', result.message);

                    } else {
                        $("#loading").hide();
                        openErrorGritter('Error!', result.message);
                    }
                });
            }

        }


        function changeLoc(location) {
            $("#location").val(location);
            $("#location_text").text(location);

            fillTableList();
            fillPicked();
        }


        function fillField(id) {
            data = {
                id: id
            }

            $.get('{{ url('fetch/chm_picking_schedule_detail') }}', data, function(result, status, xhr) {
                if (result.status) {
                    if (result.inventory.length > 0 || result.out.length == 0) {
                        $('#schedule_id').val(id);
                        $('#category').val(result.data.category);
                        $('#larutan').val(result.data.solution_name);
                        $('#material_number').val(result.data.material_number);
                        $('#quantity').val(result.data.quantity);
                        $('#bun').val(result.data.bun);
                        $('#material_description').val(result.data.material_description);
                        $('#storage_location').val(result.data.storage_location);

                        if (result.inventory.length == 0) {
                            $('#stock').val('0');
                            $('#stock_uom').val(result.data.material_bun);
                        } else {
                            $('#stock').val(result.inventory[0].quantity);
                            $('#stock_uom').val(result.inventory[0].bun);
                        }

                        if (result.out.length == 0) {
                            $('#out').val('0');
                            $('#out_uom').val(result.data.material_bun);
                        } else {
                            $('#out').val(result.out[0].quantity);
                            $('#out_uom').val(result.out[0].bun);
                        }

                        if (result.data.license == 1) {

                        }

                        $('#license').css('display', 'none');


                        fillPicked();

                    } else {
                        openErrorGritter('Error!', 'Stock ' + result.data.material_number + ' Habis');
                    }
                } else {
                    openErrorGritter('Error!', 'Belum waktunya mengambil chemical, lihat schedule!');
                    $('#schedule_id').val();
                    $('#category').val();
                    $('#larutan').val();
                    $('#material_number').val();
                    $('#quantity').val();
                    $('#bun').val();
                    $('#material_description').val();
                    $('#storage_location').val();
                    $('#stock_uom').val();
                    $('#out_uom').val();
                }

            });
        }

        function fillTableList() {
            $('#modalLocation').modal('hide');

            var request = 'request';
            var location = $('#location').val();

            var data = {
                request: request,
                location: location
            }

            $.get('{{ url('fetch/chm_picking_schedule') }}', data, function(result, status, xhr) {
                $('#tableList').DataTable().clear();
                $('#tableList').DataTable().destroy();
                $('#tableBodyList').html("");

                var tableData = "";
                $.each(result.data, function(key, value) {
                    tableData += '<tr onclick="fillField(\'' + value.id + '\')">';
                    tableData += '<td>' + value.schedule_date + '</td>';
                    tableData += '<td>' + value.category + '</td>';
                    tableData += '<td>' + value.solution_name + '</td>';
                    tableData += '<td>' + value.material_number + '</td>';
                    tableData += '<td>' + value.material_description + '</td>';
                    tableData += '<td>' + value.storage_location + '</td>';
                    tableData += '<td>' + value.quantity + '</td>';
                    tableData += '<td>' + value.bun + '</td>';
                    tableData += '</tr>';
                });
                $('#tableBodyList').append(tableData);

                var table_list = $('#tableList').DataTable({
                    "language": {
                        "emptyTable": "There is no schedule"
                    },
                    'dom': 'Bfrtip',
                    'responsive': true,
                    'lengthMenu': [
                        [25, 50, 100, -1],
                        ['25 rows', '50 rows', '100 rows', 'Show all']
                    ],
                    "pageLength": 25,
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
            });
        }

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
                time: '7000'
            });
        }
    </script>
@endsection
