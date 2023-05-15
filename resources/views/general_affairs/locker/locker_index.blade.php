@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        #tableResumeMale>tbody>tr>td:hover {
            cursor: pointer;
            background-color: #7dfa8c !important;
        }

        #tableResumeFemale>tbody>tr>td:hover {
            cursor: pointer;
            background-color: #7dfa8c !important;
        }

        #tableMale>tbody>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c !important;
        }

        #tableFemale>tbody>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c !important;
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
            padding: 2px 5px 2px 5px;
            height: 40px;
        }

        #qr_item:hover {
            color: #ffffff
        }

        .crop2 {
            overflow: hidden;
        }

        .crop2 img {
            height: 170px !important;
            width: 110px !important;
            margin: -5% 0 0 0 !important;
        }

        #loading {
            display: none;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
            <a class="btn btn-success pull-right" data-toggle="modal" data-target="#modalScan"
                style="margin-left: 5px; width: 10%;"><i class="fa fa-qrcode"></i> Scan QR</a>
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
                <div class="row">
                    <div class="col-xs-6">
                        <div class="box box-solid" style="border: 1px solid black;">
                            <div class="box-header" style="border-bottom: 1px solid black;">
                                <center>
                                    <span style="font-size: 2vw; color: #3CB371; font-weight: bold;">Locker Room Male</span>
                                </center>
                            </div>
                            <div class="box-body" style="display: block;">
                                <div class="col-xs-5">
                                    <table id="tableResumeMale" class="table table-bordered table-hover">
                                        <tbody>
                                            <tr>
                                                <td style="width: 1%; font-weight: bold; font-size: 1.3vw; background-color: RGB(204,255,255);"
                                                    onclick="fetchDetail('Vacant')">VACANT</td>
                                                <td style="width: 1%; font-weight: bold; font-size: 1.3vw; text-align: right;"
                                                    id="cntMaleVacant" onclick="fetchDetail('Vacant')"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 1%; font-weight: bold; font-size: 1.3vw; background-color: RGB(255,204,255);"
                                                    onclick="fetchDetail('Occupied')">OCCUPIED</td>
                                                <td style="width: 1%; font-weight: bold; font-size: 1.3vw; text-align: right;"
                                                    id="cntMaleOccupied" onclick="fetchDetail('Occupied')"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 1%; font-weight: bold; font-size: 1.3vw; background-color: RGB(252, 248, 227);"
                                                    onclick="fetchDetail('total')">TOTAL</td>
                                                <td style="width: 1%; font-weight: bold; font-size: 1.3vw; background-color: RGB(252, 248, 227); text-align: right;"
                                                    id="cntMaleTotal" onclick="fetchDetail('total')"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-xs-7">
                                    <div id="container1" style="height: 40vh;"></div>
                                </div>
                                <table id="tableMale" class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: #3CB371;">
                                        <tr>
                                            <th style="text-align: center;">ID</th>
                                            <th>Employee</th>
                                            <th>Department/Section</th>
                                            <th style="text-align: center;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableMaleBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="box box-solid" style="border: 1px solid black;">
                            <div class="box-header" style="border-bottom: 1px solid black;">
                                <center>
                                    <span style="font-size: 2vw; color: #FF69B4; font-weight: bold;">Locker Room
                                        Female</span>
                                </center>
                            </div>
                            <div class="box-body" style="display: block;">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <table id="tableResumeFemale" class="table table-bordered table-hover">
                                            <tbody>
                                                <tr>
                                                    <td style="width: 1%; font-weight: bold; font-size: 1.3vw; background-color: RGB(204,255,255);"
                                                        onclick="fetchDetail('Vacant')">VACANT</td>
                                                    <td style="width: 1%; font-weight: bold; font-size: 1.3vw; text-align: right;"
                                                        id="cntFemaleVacant" onclick="fetchDetail('Vacant')"></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 1%; font-weight: bold; font-size: 1.3vw; background-color: RGB(255,204,255);"
                                                        onclick="fetchDetail('Occupied')">OCCUPIED</td>
                                                    <td style="width: 1%; font-weight: bold; font-size: 1.3vw; text-align: right;"
                                                        id="cntFemaleOccupied" onclick="fetchDetail('Occupied')"></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 1%; font-weight: bold; font-size: 1.3vw; background-color: RGB(252, 248, 227);"
                                                        onclick="fetchDetail('total')">TOTAL</td>
                                                    <td style="width: 1%; font-weight: bold; font-size: 1.3vw; background-color: RGB(252, 248, 227); text-align: right;"
                                                        id="cntFemaleTotal" onclick="fetchDetail('total')"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xs-7">
                                        <div id="container2" style="height: 40vh;"></div>
                                    </div>
                                </div>
                                <table id="tableFemale" class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: #FF69B4;">
                                        <tr>
                                            <th style="text-align: center;">ID</th>
                                            <th>Employee ID</th>
                                            <th>Employee Name</th>
                                            <th style="text-align: center;">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableFemaleBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalEdit">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #605ca8; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Update Locker<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="editEmployee" class="col-sm-3 control-label">Employee<span
                                            class="text-red">*</span></label>
                                    <div class="col-sm-5">
                                        <input type="hidden" id="editLockerId">
                                        <select class="form-control select2" id="editEmployee" name="editEmployee"
                                            data-placeholder='Select Employee' style="width: 100%" onchange="tes(value)">
                                            <option value=""></option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->employee_id }}">
                                                    {{ $employee->employee_id }} - {{ $employee->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="col-sm-8" style="margin-bottom: 10px;">
                            <button class="btn btn-primary pull-right" id="btnSubmit"
                                onclick="editLocker('occupied')">Submit</button>
                            <button class="btn btn-danger pull-right" id="btnVacant" onclick="editLocker('vacant')"
                                style="margin-right: 10px;">Vacate</button>
                        </div>
                    </div>
                    <span style="font-weight: bold; font-size: 1.2vw;">History Logs</span>
                    <table id="tableModal" class="table table-bordered table-striped table-hover">
                        <thead style="background-color: #605ca8; color: white;">
                            <tr>
                                <th style="width: 0.1%; text-align: center;">#</th>
                                <th style="width: 1%">Employee ID<br>Employee Name</th>
                                <th style="width: 1%">Department<br>Section</th>
                                <th style="width: 0.1%; text-align: right;">From</th>
                                <th style="width: 0.1%; text-align: right;">To</th>
                            </tr>
                        </thead>
                        <tbody id="tableModalBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalScan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3 style="background-color: #00a65a; padding-top: 2%; padding-bottom: 2%; font-weight: bold;">Scan
                            QRCode</h3>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id='scanner' class="col-xs-12">
                            <center>
                                <div id="div_qr_item">
                                    <input id="qr_item" type="text"
                                        style="border:0; width: 100%; text-align: center; height: 1px; color: #3c3c3c;">
                                </div>
                            </center>
                            <div class="col-xs-12">
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
                        </div>
                        <div id="employeeInformation" style="width:100%; padding-left: 2%; padding-right: 2%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/jquery.numpad.js') }}"></script>
    <script src="{{ url('js/jsQR.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            fetchTable();
        });

        $(function() {
            $('#editEmployee').select2({
                dropdownParent: $('#modalEdit')
            });
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

        var lockers = [];
        var locker_logs = [];
        var video;
        var locker_ids = [];
        var employees = <?php echo json_encode($employees); ?>;

        function stopScan() {
            $('#modalScan').modal('hide');
        }

        function videoOff() {
            video.pause();
            video.src = "";
            video.srcObject.getTracks()[0].stop();
        }

        $("#modalScan").on('shown.bs.modal', function() {
            showCheck('123');
            $('#qr_item').show();
            $('#qr_item').focus();

            var width = $('#video').width();
            $('#div_qr_item').css('width', width);
            $('#employeeInformation').html("");
        });

        $('#modalScan').on('hidden.bs.modal', function() {
            videoOff();
        });

        function tes(t) {
            console.log(t);
        }

        function showCheck(kode) {
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
                        // videoOff();
                        fetchEmployee(video, code.data);
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

        $('#qr_item').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                var qr_item = $('#qr_item').val();
                fetchEmployee(video, qr_item);
            }
        });

        function fetchEmployee(video, qr_item) {
            $('#employeeInformation').html("");
            var employeeInformation = "";

            // var str = "http://10.109.52.4/mirai/public/index/ga_control/locker_qr/M0005";
            locker = qr_item.split('/');

            if (jQuery.inArray(locker[8], locker_ids) === -1) {
                openErrorGritter('Data not found');
                audio_error.play();
                return false;
            }

            $.each(lockers, function(key, value) {
                if (value.locker_id == locker[8]) {
                    employeeInformation += '<div class="col-xs-12">';
                    employeeInformation +=
                        '<div class="box box-widget widget-user-2" style="border: 1px solid black;">';
                    employeeInformation += '<div class="widget-user-header bg-purple">';
                    employeeInformation += '<div class="widget-user-image crop2">';
                    employeeInformation += '<img src="{{ url('images/avatar/') }}' + '/' + value.employee_id +
                        '.jpg' + '" alt="">';
                    employeeInformation +=
                        '<h5 class="widget-user-desc" style="font-size: 1.2vw; font-weight: bold; padding-left: 40px;">' +
                        value.locker_id + '<br>' + value.employee_id + '<br>' + value.employee_name + '<br>' + value
                        .department_name + '<br>' + value.section_name + '</h5>';
                    employeeInformation += '</div>';
                    employeeInformation += '</div>';
                    employeeInformation += '</div>';
                    employeeInformation += '</div>';
                }
            });

            $('#employeeInformation').append(employeeInformation);
        }

        function editLocker(category) {
            if (confirm("Are you sure want to update this locker data?")) {
                $('#loading').show();
                var employee_id = $('#editEmployee').val();
                var locker_id = $('#editLockerId').val();

                var data = {
                    category: category,
                    locker_id: locker_id,
                    employee_id: employee_id,
                }
                $.post('{{ url('edit/ga_control/locker') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        $('#loading').hide();
                        $('#modalEdit').modal('hide');
                        openSuccessGritter('Success!', result.message);
                        audio_ok.play();
                        fetchTable();
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error!', result.message);
                        audio_error.play();
                    }
                });
            } else {
                return false;
            }
        }

        function modalEdit(locker_id, employee_id) {
            var tableModalBody = "";
            $('#tableModalBody').html("");
            $('#editLockerId').val(locker_id);

            var cnt = 0;

            $.each(locker_logs, function(key, value) {
                if (value.locker_id == locker_id) {
                    cnt += 1;
                    tableModalBody += '<tr>';
                    tableModalBody += '<td style="text-align: center;">' + cnt + '</td>';
                    tableModalBody += '<td style="text-align: left;">' + value.employee_id + '<br>' + value
                        .employee_name + '</td>';
                    tableModalBody += '<td style="text-align: left;">' + value.department_name + '<br>' + value
                        .section_name + '</td>';
                    tableModalBody += '<td style="text-align: right;">' + value.date_from + '</td>';
                    tableModalBody += '<td style="text-align: right;">' + value.date_to + '</td>';
                    tableModalBody += '</tr>';
                }
            });
            $('#tableModalBody').append(tableModalBody);

            if (employee_id == "") {
                $('#btnSubmit').prop('disabled', false);
                $('#btnVacant').prop('disabled', true);
                $('#editEmployee').val("").change();
                $('#editEmployee').prop('disabled', false);
            } else {
                $('#btnSubmit').prop('disabled', true);
                $('#btnVacant').prop('disabled', false);
                $('#editEmployee').val(employee_id)
                    .change();
                $('#editEmployee').prop('disabled', true);
            }

            $('#modalEdit').modal('show');
        }

        function fetchDetail(category) {
            $('#tableMaleBody').html("");
            $('#tableFemaleBody').html("");
            $('#tableMale').DataTable().clear();
            $('#tableMale').DataTable().destroy();
            $('#tableFemale').DataTable().clear();
            $('#tableFemale').DataTable().destroy();
            var tableMaleBody = "";
            var tableFemaleBody = "";

            $.each(lockers, function(key, value) {
                if (category == 'Vacant') {
                    if (value.employee_id == "") {
                        var locker_id = value.locker_id;

                        if (locker_id.match("^M")) {
                            tableMaleBody += '<tr onclick="modalEdit(\'' + value.locker_id + '\',\'' + value
                                .employee_id + '\')">';
                            tableMaleBody += '<td style="width: 0.1%; text-align: center; font-weight: bold;">' +
                                value.locker_id + '</td>';
                            tableMaleBody += '<td style="width: 0.5%;">' + value.employee_id + '<br>' + value
                                .employee_name + '</td>';
                            tableMaleBody += '<td style="width: 1%;">' + value.department_name + '<br>' + value
                                .section_name + '</td>';
                            if (value.employee_id == "") {
                                cntMaleVacant += 1;
                                tableMaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">VACANT<br>' +
                                    value.last_update + '</td>';
                            }
                            if (value.employee_id != "") {
                                cntMaleOccupied += 1;
                                tableMaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">OCCUPIED<br>' +
                                    value.last_update + '</td>';
                            }
                            tableMaleBody += '</tr>';
                        }
                        if (locker_id.match("^F")) {
                            tableFemaleBody += '<tr onclick="modalEdit(\'' + value.locker_id + '\',\'' + value
                                .employee_id + '\')">';
                            tableFemaleBody += '<td style="width: 0.1%; text-align: center; font-weight: bold;">' +
                                value.locker_id + '</td>';
                            tableFemaleBody += '<td style="width: 0.5%;">' + value.employee_id + '<br>' + value
                                .employee_name + '</td>';
                            tableFemaleBody += '<td style="width: 1%;">' + value.department_name + '<br>' + value
                                .section_name + '</td>';
                            if (value.employee_id == "") {
                                cntFemaleVacant += 1;
                                tableFemaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">VACANT<br>' +
                                    value.last_update + '</td>';
                            }
                            if (value.employee_id != "") {
                                cntFemaleOccupied += 1;
                                tableFemaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">OCCUPIED<br>' +
                                    value.last_update + '</td>';
                            }
                            tableFemaleBody += '</tr>';
                        }
                    }
                } else if (category == 'Occupied') {
                    if (value.employee_id != "") {
                        var locker_id = value.locker_id;

                        if (locker_id.match("^M")) {
                            tableMaleBody += '<tr onclick="modalEdit(\'' + value.locker_id + '\',\'' + value
                                .employee_id + '\')">';
                            tableMaleBody += '<td style="width: 0.1%; text-align: center; font-weight: bold;">' +
                                value.locker_id + '</td>';
                            tableMaleBody += '<td style="width: 0.5%;">' + value.employee_id + '<br>' + value
                                .employee_name + '</td>';
                            tableMaleBody += '<td style="width: 1%;">' + value.department_name + '<br>' + value
                                .section_name + '</td>';
                            if (value.employee_id == "") {
                                cntMaleVacant += 1;
                                tableMaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">VACANT<br>' +
                                    value.last_update + '</td>';
                            }
                            if (value.employee_id != "") {
                                cntMaleOccupied += 1;
                                tableMaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">OCCUPIED<br>' +
                                    value.last_update + '</td>';
                            }
                            tableMaleBody += '</tr>';
                        }
                        if (locker_id.match("^F")) {
                            tableFemaleBody += '<tr onclick="modalEdit(\'' + value.locker_id + '\',\'' + value
                                .employee_id + '\')">';
                            tableFemaleBody += '<td style="width: 0.1%; text-align: center; font-weight: bold;">' +
                                value.locker_id + '</td>';
                            tableFemaleBody += '<td style="width: 0.5%;">' + value.employee_id + '<br>' + value
                                .employee_name + '</td>';
                            tableFemaleBody += '<td style="width: 1%;">' + value.department_name + '<br>' + value
                                .section_name + '</td>';
                            if (value.employee_id == "") {
                                cntFemaleVacant += 1;
                                tableFemaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">VACANT<br>' +
                                    value.last_update + '</td>';
                            }
                            if (value.employee_id != "") {
                                cntFemaleOccupied += 1;
                                tableFemaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">OCCUPIED<br>' +
                                    value.last_update + '</td>';
                            }
                            tableFemaleBody += '</tr>';
                        }
                    }
                } else {
                    var locker_id = value.locker_id;

                    if (locker_id.match("^M")) {
                        tableMaleBody += '<tr onclick="modalEdit(\'' + value.locker_id + '\',\'' + value
                            .employee_id + '\')">';
                        tableMaleBody += '<td style="width: 0.1%; text-align: center; font-weight: bold;">' + value
                            .locker_id + '</td>';
                        tableMaleBody += '<td style="width: 0.5%;">' + value.employee_id + '<br>' + value
                            .employee_name + '</td>';
                        tableMaleBody += '<td style="width: 1%;">' + value.department_name + '<br>' + value
                            .section_name + '</td>';
                        if (value.employee_id == "") {
                            cntMaleVacant += 1;
                            tableMaleBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">VACANT<br>' +
                                value.last_update + '</td>';
                        }
                        if (value.employee_id != "") {
                            cntMaleOccupied += 1;
                            tableMaleBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">OCCUPIED<br>' +
                                value.last_update + '</td>';
                        }
                        tableMaleBody += '</tr>';
                    }
                    if (locker_id.match("^F")) {
                        tableFemaleBody += '<tr onclick="modalEdit(\'' + value.locker_id + '\',\'' + value
                            .employee_id + '\')">';
                        tableFemaleBody += '<td style="width: 0.1%; text-align: center; font-weight: bold;">' +
                            value.locker_id + '</td>';
                        tableFemaleBody += '<td style="width: 0.5%;">' + value.employee_id + '<br>' + value
                            .employee_name + '</td>';
                        tableFemaleBody += '<td style="width: 1%;">' + value.department_name + '<br>' + value
                            .section_name + '</td>';
                        if (value.employee_id == "") {
                            cntFemaleVacant += 1;
                            tableFemaleBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">VACANT<br>' +
                                value.last_update + '</td>';
                        }
                        if (value.employee_id != "") {
                            cntFemaleOccupied += 1;
                            tableFemaleBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">OCCUPIED<br>' +
                                value.last_update + '</td>';
                        }
                        tableFemaleBody += '</tr>';
                    }
                }
            });

            $('#tableMaleBody').append(tableMaleBody);
            $('#tableFemaleBody').append(tableFemaleBody);

            $('#tableMale').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [25, 50, -1],
                    ['25 rows', '50 rows', 'Show all']
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
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });

            $('#tableFemale').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [25, 50, -1],
                    ['25 rows', '50 rows', 'Show all']
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
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });
        }

        function fetchTable() {
            var data = {

            }
            $.get('{{ url('fetch/ga_control/locker') }}', data, function(result, status, xhr) {
                if (result.status) {
                    lockers = result.lockers;
                    locker_logs = result.locker_logs;
                    $('#tableMaleBody').html("");
                    $('#tableFemaleBody').html("");
                    $('#tableMale').DataTable().clear();
                    $('#tableMale').DataTable().destroy();
                    $('#tableFemale').DataTable().clear();
                    $('#tableFemale').DataTable().destroy();
                    var tableMaleBody = "";
                    var tableFemaleBody = "";
                    var cntMaleVacant = 0;
                    var cntMaleOccupied = 0;
                    var cntFemaleVacant = 0;
                    var cntFemaleOccupied = 0;
                    var employee_ids = [];

                    $.each(result.lockers, function(key, value) {
                        var locker_id = value.locker_id;
                        locker_ids.push(value.locker_id);
                        employee_ids.push(value.employee_id);

                        if (locker_id.match("^M")) {
                            tableMaleBody += '<tr onclick="modalEdit(\'' + value.locker_id + '\',\'' + value
                                .employee_id + '\')">';
                            tableMaleBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;">' + value
                                .locker_id + '</td>';
                            tableMaleBody += '<td style="width: 0.5%;">' + value.employee_id + '<br>' +
                                value.employee_name + '</td>';
                            tableMaleBody += '<td style="width: 1%;">' + value.department_name + '<br>' +
                                value.section_name + '</td>';
                            if (value.employee_id == "") {
                                cntMaleVacant += 1;
                                tableMaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">VACANT<br>' +
                                    value.last_update + '</td>';
                            }
                            if (value.employee_id != "") {
                                cntMaleOccupied += 1;
                                tableMaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">OCCUPIED<br>' +
                                    value.last_update + '</td>';
                            }
                            tableMaleBody += '</tr>';
                        }
                        if (locker_id.match("^F")) {
                            tableFemaleBody += '<tr onclick="modalEdit(\'' + value.locker_id + '\',\'' +
                                value.employee_id + '\')">';
                            tableFemaleBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;">' + value
                                .locker_id + '</td>';
                            tableFemaleBody += '<td style="width: 0.5%;">' + value.employee_id + '<br>' +
                                value.employee_name + '</td>';
                            tableFemaleBody += '<td style="width: 1%;">' + value.department_name + '<br>' +
                                value.section_name + '</td>';
                            if (value.employee_id == "") {
                                cntFemaleVacant += 1;
                                tableFemaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(204,255,255);">VACANT<br>' +
                                    value.last_update + '</td>';
                            }
                            if (value.employee_id != "") {
                                cntFemaleOccupied += 1;
                                tableFemaleBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold; background-color: RGB(255,204,255);">OCCUPIED<br>' +
                                    value.last_update + '</td>';
                            }
                            tableFemaleBody += '</tr>';
                        }
                    });

                    var cnt_male = 0;
                    var cnt_female = 0;



                    var total_no_locker = cnt_male + cnt_female;
                    $('#cnt_no_locker').text('(' + total_no_locker + ')');

                    $('#tableMaleBody').append(tableMaleBody);
                    $('#tableFemaleBody').append(tableFemaleBody);

                    $('#cntMaleVacant').text(cntMaleVacant);
                    $('#cntMaleOccupied').text(cntMaleOccupied);
                    $('#cntFemaleVacant').text(cntFemaleVacant);
                    $('#cntFemaleOccupied').text(cntFemaleOccupied);
                    $('#cntMaleTotal').text(cntMaleVacant + cntMaleOccupied);
                    $('#cntFemaleTotal').text(cntFemaleVacant + cntFemaleOccupied);

                    Highcharts.chart('container1', {
                        chart: {
                            backgroundColor: null,
                            type: 'pie'
                        },
                        title: {
                            text: null
                        },
                        plotOptions: {
                            pie: {
                                innerSize: 100,
                                cursor: 'pointer',
                                depth: 45,
                                allowPointSelect: true,
                                edgeWidth: 1,
                                borderColor: 'rgb(126,86,134)',
                                depth: 35,
                                dataLabels: {
                                    distance: -50,
                                    enabled: true,
                                    format: '<b>{point.name}<br>{point.y} item(s)</b><br>{point.percentage:.1f} %',
                                    style: {
                                        fontSize: '0.8vw',
                                        textOutline: 0
                                    },
                                    color: 'black',
                                    connectorWidth: '3px'
                                },
                                showInLegend: false,
                                point: {
                                    events: {
                                        click: function() {
                                            fetchDetail(this.name);
                                        }
                                    }
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Locker Availability',
                            data: [{
                                name: 'Vacant',
                                y: cntMaleVacant,
                                color: 'RGB(204,255,255)'
                            }, {
                                name: 'Occupied',
                                y: cntMaleOccupied,
                                color: 'RGB(255,204,255)'
                            }]
                        }]
                    });

                    Highcharts.chart('container2', {
                        chart: {
                            backgroundColor: null,
                            type: 'pie'
                        },
                        title: {
                            text: null
                        },
                        plotOptions: {
                            pie: {
                                innerSize: 100,
                                cursor: 'pointer',
                                depth: 45,
                                allowPointSelect: true,
                                edgeWidth: 1,
                                borderColor: 'rgb(126,86,134)',
                                depth: 35,
                                dataLabels: {
                                    distance: -50,
                                    enabled: true,
                                    format: '<b>{point.name}<br>{point.y} item(s)</b><br>{point.percentage:.1f} %',
                                    style: {
                                        fontSize: '0.8vw',
                                        textOutline: 0
                                    },
                                    color: 'black',
                                    connectorWidth: '3px'
                                },
                                showInLegend: false,
                                point: {
                                    events: {
                                        click: function() {
                                            fetchDetail(this.name);
                                        }
                                    }
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Locker Availability',
                            data: [{
                                name: 'Vacant',
                                y: cntFemaleVacant,
                                color: 'RGB(204,255,255)'
                            }, {
                                name: 'Occupied',
                                y: cntFemaleOccupied,
                                color: 'RGB(255,204,255)'
                            }]
                        }]
                    });

                    $('#tableMale').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [25, 50, -1],
                            ['25 rows', '50 rows', 'Show all']
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
                        'order': [],
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });

                    $('#tableFemale').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [25, 50, -1],
                            ['25 rows', '50 rows', 'Show all']
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
                        'order': [],
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });

                } else {
                    alert('Attempt to retrieve data failed.');
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
