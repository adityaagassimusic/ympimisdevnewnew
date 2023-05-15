@extends('layouts.notification')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        #loading,
        #error {
            display: none;
        }

        .gritter-title {
            font-family: 'Source Sans Pro', sans-serif !important;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
    </section>
@endsection
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: white; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>

        <input type="text" id="remark" name="remark" value="{{ $remark }}" hidden>
        <input type="text" id="employee_id" name="employee_id" hidden>

        <div class="row">
            <div class="col-md-8">

                <div class="col-md-6">
                    <div class="panel panel-default ">
                        <div class="panel-heading" style="background-color:  #008d4c; color: #dff0d8;">
                            <h4 class="panel-title"
                                style="text-transform: uppercase; text-align: center; font-weight: bold; font-size: 20pt;">
                                {{ $title }}<br>{{ $title_jp }}
                            </h4>
                        </div>
                        <div class="panel-body">
                            <button id="submit" style="margin-bottom: 10px; display: none;"
                                class="btn btn-lg btn-primary btn-block">&#9655;&nbsp;Mulai</button>
                            <button id="cancel" style="margin-bottom: 10px; display: none;"
                                class="btn btn-lg btn-warning btn-block"><i class="fa fa-close"></i>&nbsp;Batal</button>

                            <input style="display: none; text-align:center;" id="employee_tag" type="text"
                                placeholder="Tap ID Card" class="form-control input-lg" name="employee_tag" disabled>
                            <center>
                                <p id="employee_name"
                                    style="font-size:18px; text-align: center; color: #3c3c3c; padding: 0px; font-weight: bold; text-transform: uppercase;">
                                </p>
                            </center>

                            <h3 id="header-type" style="color: #005a30; font-weight: bold;"></h3>
                            <input style="margin-bottom: 10px; display: none; text-align:center;" id="barcode"
                                type="text" placeholder="Scan Kanban atau Slip" class="form-control input-lg"
                                name="barcode">

                            <button id="finish" style="display: none;" class="btn btn-lg btn-success btn-block"><i
                                    class="fa fa-save"></i>&nbsp;Simpan</button>

                        </div>
                    </div>
                </div>

                <div class="col-md-6" style="padding: 0px;">
                    <div class="col-md-12" id="scan-resume" style="padding:0px; font-size: 10pt; display: none;">
                        <div class="panel panel-default ">
                            <div class="panel-heading">
                                <h6 class="panel-title" style="text-transform: uppercase; font-weight: bold;">
                                    RESUME
                                </h6>
                            </div>
                            <div class="panel-body" style="padding: 10px;">
                                <table class="table table-bordered table-stripped">
                                    <thead style="background-color: #f5f5f5;">
                                        <tr>
                                            <th style="text-align: center; width: 40%;">Category</th>
                                            <th style="text-align: center; width: 40%;">Model</th>
                                            <th style="text-align: center; width: 20%;">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableResumeBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="scan-list" style="padding:0px; font-size: 10pt; display: none;">
                        <div class="panel panel-default ">
                            <div class="panel-heading">
                                <h6 class="panel-title" style="text-transform: uppercase; font-weight: bold;">
                                    LIST
                                </h6>
                            </div>
                            <div class="panel-body" style="padding: 10px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-stripped">
                                        <thead style="background-color: #f5f5f5;">
                                            <tr>
                                                <th style="text-align: center; width: 10%;">#</th>
                                                <th style="text-align: center; width: 30%;">Kanban/Slip</th>
                                                <th style="text-align: center; width: 50%;">Description</th>
                                                <th style="text-align: center; width: 10%;">Qty</th>
                                                <th style="text-align: center; width: 10%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableListBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title" style="text-transform: uppercase; font-weight: bold; font-size: 20pt;">
                            INTRANSIT
                            <button class="btn btn-default btn-sm pull-right" onclick="fillIntransitTable()">
                                &nbsp;<i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
                            </button>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="col-xs-12" id="last_update" style="padding: 0%;">
                            <p class="pull-right" style="margin: 0px; font-size: 10pt;">
                                <i class="fa fa-fw fa-clock-o"></i>Last Updated:
                                <span id="last_updated"></span>
                            </p>
                        </div>

                        <div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
                            <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                onclick="showIntransit('KANBAN')">
                                <span><i class="fa fa-credit-card"></i>&nbsp;&nbsp;KANBAN</span>
                                <br>
                                <span id="countIntransitKanban" style="font-size: 4vw;">0</span>
                            </button>
                        </div>
                        <div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
                            <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                onclick="showIntransit('RETURN')">
                                <span><i class="fa fa-reply-all"></i></i>&nbsp;&nbsp;RETURN</span>
                                <br>
                                <span id="countIntransitReturn" style="font-size: 4vw;">0</span>
                            </button>
                        </div>
                        <div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
                            <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                onclick="showIntransit('SCRAP')">
                                <span><i class="fa fa-trash"></i></i>&nbsp;&nbsp;SCRAP</span>
                                <br>
                                <span id="countIntransitScrap" style="font-size: 4vw;">0</span>
                            </button>
                        </div>
                        @if (in_array(strtoupper($remark), ['BPP-IN', 'BPP-OUT', 'WLD-IN', 'WLD-OUT', 'BFF-IN', 'BFF-OUT']))
                            <div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
                                <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                    onclick="showIntransit('REPAIR')">
                                    <span><i class="fa fa-wrench"></i></i>&nbsp;&nbsp;REPAIR</span>
                                    <br>
                                    <span id="countIntransitRepair" style="font-size: 4vw;">0</span>
                                </button>
                            </div>
                        @endif
                        <div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
                            <button class="btn btn-default"
                                style="width: 100%; font-weight: bold; padding-left: 5px; padding-right: 5px;"
                                onclick="showIntransit('EXTRA ORDER')">
                                <span><i class="fa fa-plus-square"></i></i>&nbsp;&nbsp;EXTRA ORDER</span>
                                <br>
                                <span id="countIntransitExtraOrder" style="font-size: 4vw;">0</span>
                            </button>
                        </div>
                        <div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
                            <button class="btn btn-default"
                                style="width: 100%; font-weight: bold; padding-left: 5px; padding-right: 5px;"
                                onclick="showIntransitKhusus('KHUSUS')">
                                <span><i class="fa fa-list-alt"></i></i>&nbsp;SLIP KHUSUS</span>
                                <br>
                                <span id="countIntransitSlipKhusus" style="font-size: 4vw;">0</span>
                            </button>
                        </div>
                        @if (in_array(strtoupper($remark), ['BPP-OUT', 'WLD-OUT', 'FA-OUT']))
                            <div class="col-xs-4" style="padding: 10px 5px 10px 5px;">
                                <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                    onclick="showIntransit('EXPORT')">
                                    <span><i class="fa fa-ship"></i></i>&nbsp;&nbsp;EXPORT</span>
                                    <br>
                                    <span id="countIntransitExport" style="font-size: 4vw;">0</span>
                                </button>
                            </div>
                        @endif

                        {{-- <h4 style="font-weight: bold; color:#3e763d; margin: 0px;">RESUME</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-stripped" id="tableIntransitResume">
                                <thead style="background-color: #dff0d8;">
                                    <tr>
                                        <th style="text-align: center; width: 40%;">Category</th>
                                        <th style="text-align: center; width: 40%;">Model</th>
                                        <th style="text-align: center; width: 20%;">Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="tableIntransitResumeBody">
                                </tbody>
                            </table>
                        </div>
                        <h4 style="font-weight: bold; color:#3e763d; margin: 0px;">LIST</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-stripped" id="tableIntransitList">
                                <thead style="background-color: #dff0d8;">
                                    <tr>
                                        <th style="text-align: center; width: 10%;">#</th>
                                        <th style="text-align: center; width: 25%;">Kanban/Slip</th>
                                        <th style="text-align: center; width: 55%;">Description</th>
                                        <th style="text-align: center; width: 10%;">Qty</th>
                                    </tr>
                                </thead>
                                <tbody id="tableIntransitListBody">
                                </tbody>
                            </table>
                        </div> --}}
                    </div>

                </div>
            </div>

        </div>
    </section>

    <div id="modalHistory" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                    <h3 class="modal-title" style="font-weight: bold;">Pencatatan {{ $title }}</h3>
                </div>
                <div class="modal-body">
                    <div class="row" id="histories">
                        <div class="col-md-12">
                            <h3 id="totalTransaction" style="font-weight: bold; font-size: 1.5vw; color: purple;">30</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered table-stripped">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">#</th>
                                            <th style="text-align: center;">Kanban/Slip</th>
                                            <th style="text-align: left;">Description</th>
                                            <th style="text-align: center;">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableHistoryBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modalError" class="modal fade modal-danger" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                    <center>
                        <h3 class="modal-title">Error!</h3>
                    </center>
                </div>
                <div class="modal-body">
                    <center>
                        <h4 id="modalErrorMessage"></h4>
                    </center>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalType" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog" style="width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-xs-12" style="background-color: #333333; color:white">
                        <h2 style="text-align: center; margin: 2%; font-weight: bold; text-transform: uppercase;">
                            Pilih Tipe Transaksi
                        </h2>
                    </div>
                    <div class="col-xs-12" style="margin-top: 3%; padding: 15px;">
                        <div class="col-xs-10 col-xs-offset-1">
                            <div class="col-xs-3" style="padding: 10px;">
                                <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                    onclick="selectType('KANBAN', 'fa-credit-card')">
                                    <i class="fa fa-credit-card" style="font-size: 5vw;"></i>
                                    <br>
                                    <br>
                                    <span>KANBAN</span>
                                </button>
                            </div>
                            <div class="col-xs-3" style="padding: 10px;">
                                <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                    onclick="selectType('RETURN', 'fa-reply-all')">
                                    <i class="fa fa-reply-all" style="font-size: 5vw;"></i>
                                    <br>
                                    <br>
                                    <span>RETURN</span>
                                </button>
                            </div>
                            <div class="col-xs-3" style="padding: 10px;">
                                <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                    onclick="selectType('SCRAP', 'fa-trash')">
                                    <i class="fa fa-trash" style="font-size: 5vw;"></i>
                                    <br>
                                    <br>
                                    <span>SCRAP</span>
                                </button>
                            </div>
                            @if (in_array(strtoupper($remark), ['BPP-IN', 'BPP-OUT', 'WLD-IN', 'WLD-OUT', 'BFF-IN', 'BFF-OUT']))
                                <div class="col-xs-3" style="padding: 10px;">
                                    <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                        onclick="selectType('REPAIR', 'fa-wrench')">
                                        <i class="fa fa-wrench" style="font-size: 5vw;"></i>
                                        <br>
                                        <br>
                                        <span>REPAIR</span>
                                    </button>
                                </div>
                            @endif
                            <div class="col-xs-3" style="padding: 10px;">
                                <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                    onclick="selectType('EXTRA ORDER', 'fa-plus-square')">
                                    <i class="fa fa-plus-square" style="font-size: 5vw;"></i>
                                    <br>
                                    <br>
                                    <span>EXTRA ORDER</span>
                                </button>
                            </div>
                            <div class="col-xs-3" style="padding: 10px;">
                                <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                    onclick="selectType('SLIP KHUSUS', 'fa-list-alt')">
                                    <i class="fa fa-list-alt" style="font-size: 5vw;"></i>
                                    <br>
                                    <br>
                                    <span>SLIP KHUSUS</span>
                                </button>
                            </div>
                            @if (in_array(strtoupper($remark), ['BPP-OUT', 'WLD-OUT', 'FA-OUT']))
                                <div class="col-xs-3" style="padding: 10px;">
                                    <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                        onclick="selectType('EXPORT', 'fa-ship')">
                                        <i class="fa fa-ship" style="font-size: 5vw;"></i>
                                        <br>
                                        <br>
                                        <span>EXPORT</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalIntransit">
        <div class="modal-dialog" style="width: 75%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #dff0d8; color: #3c7651; font-weight: bold; padding: 1%; margin-top: 0; text-transform: uppercase;">
                            INTRANSIT {{ $title }}
                        </h2>
                    </center>
                </div>
                <div class="modal-body table-responsive" style="min-height: 100px; padding-bottom: 25px;">
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped" id="tableIntransit">
                                <thead style="background-color: #dff0d8;">
                                    <tr>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            #
                                        </th>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            Kategori
                                        </th>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            Kanban/Slip
                                        </th>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            GMC
                                        </th>
                                        <th style="width: 25%; text-align: center; vertical-align: middle;">
                                            Deskripsi
                                        </th>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            Qty
                                        </th>
                                        <th style="width: 15%; text-align: center; vertical-align: middle;">
                                            Lokasi
                                        </th>
                                        <th style="width: 10%; text-align: center; vertical-align: middle;">
                                            PIC Material
                                        </th>
                                        <th style="width: 10%; text-align: center; vertical-align: middle;">
                                            PIC Out
                                        </th>
                                        <th style="width: 10%; text-align: center; vertical-align: middle;">
                                            Verikasi Pada
                                        </th>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tableIntransitBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalIntransitKhusus">
        <div class="modal-dialog" style="width: 75%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #f2dede; color: #a94442; font-weight: bold; padding: 1%; margin-top: 0; text-transform: uppercase;">
                            INTRANSIT {{ $title }}
                        </h2>
                    </center>
                </div>
                <div class="modal-body table-responsive" style="min-height: 100px; padding-bottom: 25px;">
                    <div class="col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped" id="tableIntransitKhusus">
                                <thead style="background-color: #f2dede;">
                                    <tr>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            #
                                        </th>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            Kategori
                                        </th>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            Slip
                                        </th>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            GMC
                                        </th>
                                        <th style="width: 25%; text-align: center; vertical-align: middle;">
                                            Deskripsi
                                        </th>
                                        <th style="width: 5%; text-align: center; vertical-align: middle;">
                                            Qty
                                        </th>
                                        <th style="width: 10%; text-align: center; vertical-align: middle;">
                                            Lokasi
                                        </th>
                                        <th style="width: 10%; text-align: center; vertical-align: middle;">
                                            PIC Material
                                        </th>
                                        <th style="width: 10%; text-align: center; vertical-align: middle;">
                                            PIC Out
                                        </th>
                                        <th style="width: 10%; text-align: center; vertical-align: middle;">
                                            Verikasi Pada
                                        </th>
                                        <th style="width: 10%; text-align: center; vertical-align: middle;">
                                            Slip Valid Sampai
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tableIntransitBodyKhusus">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $("#submit").css('display', 'block');
            $("#cancel").css('display', 'none');
            $("#finish").css('display', 'none');

            $("#scan-resume").css('display', 'none');
            $("#scan-list").css('display', 'none');

            fillIntransitTable();

            remark = '';
            category = '';

            list = [];
            resume = [];
            listed_barcode = [];
            intransit = [];

        });

        // var isAjax = false;

        var delay = (function() {
            var timer = 0;
            return function(callback, ms) {
                clearTimeout(timer);
                timer = setTimeout(callback, ms);
            };
        })();

        $("#employee_tag").on("input", function() {
            delay(function() {
                if ($("#employee_tag").val().length < 9) {
                    $("#employee_tag").val("");
                }
            }, 100);
        });

        $("#barcode").on("input", function() {
            delay(function() {
                if ($("#barcode").val().length < 5) {
                    $("#barcode").val("");
                }
            }, 100);
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

        var remark = '';
        var category = '';

        var list = [];
        var resume = [];
        var listed_barcode = [];


        var employees = <?php echo json_encode($employees); ?>;


        $('#submit').on('click', function() {
            $('#employee_name').text('');
            $('#employee_tag').val('');
            $("#employee_tag").css('display', 'block');
            $("#employee_tag").prop('disabled', false);
            $('#employee_tag').focus();

            $("#submit").css('display', 'none');
            $("#cancel").css('display', 'block');
            $("#finish").css('display', 'none');

            category = '';

        });

        $('#modalError').on('hidden.bs.modal', function() {
            $('#barcode').val('');
            $('#barcode').prop('disabled', false);
            $('#barcode').focus();
        });

        $('#employee_tag').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                if ($("#employee_tag").val().length >= 9 && $("#employee_tag").val().length <= 10) {
                    var found = false;
                    var name = '';

                    if ($("#employee_tag").val().length == 9) {
                        $.each(employees, function(key, value) {
                            if (value.employee_id == $('#employee_tag').val()) {
                                $('#employee_id').val(value.employee_id);
                                found = true;
                                name = value.name;
                                return false;
                            }
                        });
                    }

                    if ($("#employee_tag").val().length == 10) {
                        $.each(employees, function(key, value) {
                            if (value.tag == $('#employee_tag').val()) {
                                $('#employee_id').val(value.employee_id);
                                found = true;
                                name = value.name;
                                return false;
                            }
                        });
                    }

                    if (found == false) {
                        $("#employee_tag").val("");
                        $("#employee_tag").focus();
                        openErrorGritter('Error!', 'Data karyawan tidak ditemukan');
                        audio_error.play();
                        return false;
                    }

                    $('#employee_name').text('OPERATOR : ' + name);
                    $("#employee_tag").prop('disabled', true);

                    $("#type").css('display', 'block');
                    $('#modalType').modal('show');


                } else {
                    openErrorGritter('Error!', 'ID Card tidak valid.');
                    $("#employee_tag").val('');
                    audio_error.play();
                    return false;
                }
            }
        });

        function selectType(type, fa) {

            $('#barcode').val('');
            $("#barcode").css('display', 'block');
            $("#barcode").prop('disabled', false);
            $('#barcode').focus();

            $("#submit").css('display', 'none');
            $("#cancel").css('display', 'block');
            $("#finish").css('display', 'block');
            $("#finish").prop('disabled', false);

            list = [];
            resume = [];
            listed_barcode = [];

            $("#type").css('display', 'none');
            $("#modalType").modal('hide');

            category = type;
            var header = '';
            header += '<center>';
            header += '<h3 style="font-weight: bold; font-size: 20pt; margin-top:40px;" class="box-title">';
            header += '<i style="font-weight: bold; font-size: 20pt;" class="fa ' + fa + '"></i>&nbsp;&nbsp;';
            header += type.toUpperCase() + '</h3>';
            header += '<center>';

            $('#header-type').html(header);
        }

        $('#barcode').keydown(function(event) {
            if (event.keyCode == 13) {
                scanBarcode();
            }
        });

        function scanBarcode() {

            var remark = $('#remark').val();
            var tag = $('#barcode').val();

            if (listed_barcode.includes(tag)) {
                $('#modalError').modal('show');
                $('#modalErrorMessage').text('Kanban/slip sudah discan');
                audio_error.play();
                return false;
            }

            var data = {
                remark: remark,
                category: category,
                tag: tag,
            }

            $('#loading').show();

            $.get('{{ url('fetch/material_in_out') }}', data, function(result, status, xhr) {
                if (result.status) {

                    if (result.kanbans.length == 0) {
                        $('#barcode').val('');
                        $('#barcode').prop('disabled', false);
                        $('#barcode').focus();

                        $('#modalError').modal('show');
                        $('#modalErrorMessage').text('Kanban/slip tidak ditemukan');
                        $('#loading').hide();
                        audio_error.play();
                        return false
                    }

                    listed_barcode.push(tag);
                    // START UPDATE TABLE
                    for (let i = 0; i < result.kanbans.length; i++) {
                        var model = '';
                        var remark = '';
                        for (let j = 0; j < result.materials.length; j++) {
                            if (result.kanbans[i].material_number == result.materials[j].material_number) {
                                model = result.materials[j].model;
                                remark = result.materials[j].remark;
                                break;
                            }
                        }

                        if (category == 'SCRAP') {
                            list.push({
                                'tag': tag,
                                'material_number': result.kanbans[i].material_number,
                                'material_description': result.kanbans[i].material_description,
                                'model': model,
                                'remark': remark,
                                'issue_location': result.kanbans[i].issue_location,
                                'receive_location': result.kanbans[i].receive_location,
                                'scrap_category': result.kanbans[i].category,
                                'quantity': result.kanbans[i].quantity
                            });
                        } else {
                            list.push({
                                'tag': tag,
                                'material_number': result.kanbans[i].material_number,
                                'material_description': result.kanbans[i].material_description,
                                'model': model,
                                'remark': remark,
                                'issue_location': result.kanbans[i].issue_location,
                                'receive_location': result.kanbans[i].receive_location,
                                'quantity': result.kanbans[i].quantity
                            });
                        }

                    }
                    drawTable();
                    // END UPDATE TABLE

                    $('#barcode').val('');
                    $('#barcode').prop('disabled', false);
                    $('#barcode').focus();

                    $('#loading').hide();
                    openSuccessGritter('Success', 'Kanban/slip ditemukan');

                } else {

                    $('#modalError').modal('show');
                    $('#modalErrorMessage').text(result.message);
                    $('#loading').hide();

                }
            });

        }

        function drawTable() {

            $("#scan-resume").css('display', 'block');
            $("#scan-list").css('display', 'block');

            $('#tableListBody').html('');
            $('#tableResumeBody').html('');

            var bodyData = '';
            var loop = '';
            resume = [];
            for (let i = 0; i < list.length; i++) {
                bodyData += '<tr id="row_' + list[i].tag + '">';
                bodyData += '<td style="text-align: center;">' + (++loop) + '</td>';
                bodyData += '<td style="text-align: center;">' + list[i].tag + '</td>';
                bodyData += '<td style="text-align: left;">' + list[i].material_description + '</td>';
                bodyData += '<td style="text-align: right;">' + list[i].quantity + '</td>';
                bodyData += '<td style="text-align: center;">';
                bodyData += '<button style="padding-top: 0px; padding-bottom: 0px;" class="btn btn-sm btn-danger" ';
                bodyData += 'onClick="deleteList(id)" id="' + list[i].tag + '"><i class="fa fa-close"></i>';
                bodyData += '</button>';
                bodyData += '</td>';
                bodyData += '</tr>';

                var key = list[i].remark + '_' + list[i].model;
                if (!resume[key]) {
                    resume[key] = {
                        'key': key,
                        'model': list[i].model,
                        'remark': list[i].remark,
                        'quantity': list[i].quantity,
                    };
                } else {
                    resume[key].quantity = resume[key].quantity + list[i].quantity;
                }
            }
            $('#tableListBody').html(bodyData);


            resume.sort(function(a, b) {
                return a.key - b.key
            });

            var bodyData = '';
            for (var key in resume) {
                bodyData += '<tr>';
                bodyData += '<td style="text-align: center;">' + resume[key].remark + '</td>';
                bodyData += '<td style="text-align: center;">' + resume[key].model + '</td>';
                bodyData += '<td style="text-align: right;">' + resume[key].quantity + '</td>';
                bodyData += '</tr>';
            }
            $('#tableResumeBody').html(bodyData);

        }

        function deleteList(id) {

            $('#row_' + id).remove();
            for (var i = 0; i < list.length; i++) {
                if (list[i].tag == id) {
                    list.splice(i, 1);
                }
            }

            for (var i = 0; i < listed_barcode.length; i++) {
                if (listed_barcode[i] == id) {
                    listed_barcode.splice(i, 1);
                }
            }


            drawTable();

        }

        function fillIntransitTable() {

            var remark = $('#remark').val();
            var data = {
                remark: remark,
            }

            $('#loading').show();
            $.get('{{ url('fetch/material_in_out/intransit') }}', data, function(result, status, xhr) {
                if (result.status) {

                    intransit = result.intransit;
                    $('#last_updated').text(result.last_updated);

                    var countIntransitKanban = 0;
                    var countIntransitReturn = 0;
                    var countIntransitScrap = 0;
                    var countIntransitRepair = 0;
                    var countIntransitExtraOrder = 0;
                    var countIntransitSlipKhusus = 0;
                    var countIntransitExport = 0;

                    for (let i = 0; i < result.intransit.length; i++) {
                        var split = result.intransit[i].location.split('-');

                        if (split[2] == 'KANBAN') {
                            countIntransitKanban++;
                        } else if (split[2] == 'RETURN') {
                            countIntransitReturn++;
                        } else if (split[2] == 'SCRAP') {
                            countIntransitScrap++;
                        } else if (split[2] == 'REPAIR') {
                            countIntransitRepair++;
                        } else if (split[2] == 'EXTRA ORDER') {
                            countIntransitExtraOrder++;
                        } else if (split[2] == 'KHUSUS') {
                            countIntransitSlipKhusus++;
                        } else if (split[2] == 'EXPORT') {
                            countIntransitExport++;
                        }

                    }

                    $('#countIntransitKanban').text(countIntransitKanban);
                    $('#countIntransitReturn').text(countIntransitReturn);
                    $('#countIntransitScrap').text(countIntransitScrap);
                    $('#countIntransitRepair').text(countIntransitRepair);
                    $('#countIntransitExtraOrder').text(countIntransitExtraOrder);
                    $('#countIntransitSlipKhusus').text(countIntransitSlipKhusus);
                    $('#countIntransitExport').text(countIntransitExport);


                    // $('#tableIntransitListBody').html('');
                    // $('#tableIntransitResumeBody').html('');
                    // $('#last_updated').text(result.last_updated);

                    // var resumeIntransit = [];
                    // var bodyDataList = '';
                    // var loop = '';
                    // for (let i = 0; i < result.intransit.length; i++) {
                    //     bodyDataList += '<tr>';
                    //     bodyDataList += '<td style="text-align: center;">' + (++loop) + '</td>';
                    //     bodyDataList += '<td style="text-align: center;">' + result.intransit[i].tag + '</td>';
                    //     bodyDataList += '<td style="text-align: left;">' + result.intransit[i]
                    //         .material_description + '</td>';
                    //     bodyDataList += '<td style="text-align: right;">' + result.intransit[i].quantity +
                    //         '</td>';
                    //     bodyDataList += '</tr>';

                    //     var key = result.intransit[i].remark + '_' + result.intransit[i].model;
                    //     if (!resumeIntransit[key]) {
                    //         resumeIntransit[key] = {
                    //             'key': key,
                    //             'model': result.intransit[i].model,
                    //             'remark': result.intransit[i].remark,
                    //             'quantity': result.intransit[i].quantity,
                    //         };
                    //     } else {
                    //         resumeIntransit[key].quantity = resumeIntransit[key].quantity + result.intransit[i]
                    //             .quantity;
                    //     }
                    // }


                    // var bodyDataResume = '';
                    // if (result.intransit.length > 0) {
                    //     resumeIntransit.sort(function(a, b) {
                    //         return a.key - b.key
                    //     });

                    //     for (var key in resumeIntransit) {
                    //         bodyDataResume += '<tr>';
                    //         bodyDataResume += '<td style="text-align: center;">' + resumeIntransit[key].remark +
                    //             '</td>';
                    //         bodyDataResume += '<td style="text-align: center;">' + resumeIntransit[key].model +
                    //             '</td>';
                    //         bodyDataResume += '<td style="text-align: right;">' + resumeIntransit[key]
                    //             .quantity +
                    //             '</td>';
                    //         bodyDataResume += '</tr>';
                    //     }
                    // } else {

                    //     bodyDataList =
                    //         '<tr><td style="text-align: center;" colspan="4">Tidak ada data intransit</td></tr>'
                    //     bodyDataResume =
                    //         '<tr><td style="text-align: center;" colspan="4">Tidak ada data intransit</td></tr>'

                    // }

                    // $('#tableIntransitListBody').html(bodyDataList);
                    // $('#tableIntransitResumeBody').html(bodyDataResume);

                    $('#loading').hide();

                }
            });

        }

        function showIntransitKhusus(params) {

            var remark = $('#remark').val();
            var data = {
                remark: remark,
            }

            $.get('{{ url('fetch/material_in_out/intransit_khusus') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#tableIntransitBodyKhusus').html('');
                    var bodyDataList = '';
                    var loop = 0;

                    for (let i = 0; i < result.intransit.length; i++) {
                        bodyDataList += '<tr>';
                        bodyDataList += '<td style="vertical-align: middle; text-align: center;">' + (++loop) +
                            '</td>';
                        bodyDataList += '<td style="vertical-align: middle; text-align: center;">' + params +
                            '</td>';

                        bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                        bodyDataList += result.intransit[i].tag;
                        bodyDataList += '</td>';

                        bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                        bodyDataList += result.intransit[i].material_number;
                        bodyDataList += '</td>';

                        bodyDataList += '<td style="vertical-align: middle; text-align: left;">';
                        bodyDataList += result.intransit[i].material_description;
                        bodyDataList += '</td>';

                        bodyDataList += '<td style="vertical-align: middle; text-align: right;">';
                        bodyDataList += result.intransit[i].quantity;
                        bodyDataList += '</td>';

                        bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                        bodyDataList += result.intransit[i].issue_location;
                        if (params == 'KHUSUS') {
                            bodyDataList += ' <i class="fa fa-exchange"></i> ';
                        } else {
                            bodyDataList += ' <i class="fa fa-long-arrow-right"></i> ';
                        }
                        bodyDataList += result.intransit[i].receive_location;
                        bodyDataList += '</td>';

                        bodyDataList += '<td style="vertical-align: middle; text-align: left;">';
                        bodyDataList += result.intransit[i].transaction_by + '<br>';
                        bodyDataList += callName(result.intransit[i].transaction_by_name);
                        bodyDataList += '</td>';

                        bodyDataList += '<td style="vertical-align: middle; text-align: left;">';
                        bodyDataList += result.intransit[i].created_by + '<br>';
                        bodyDataList += callName(result.intransit[i].created_by_name);
                        bodyDataList += '</td>';

                        bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                        bodyDataList += result.intransit[i].created_at;
                        bodyDataList += '</td>';

                        bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                        bodyDataList += result.intransit[i].valid_to;
                        bodyDataList += '</td>';

                        bodyDataList += '</tr>';

                    }


                    if (loop == 0) {
                        openErrorGritter('Tidak Ada Data Intransit!');
                        return false;
                    }

                    $('#tableIntransitBodyKhusus').html(bodyDataList);
                    $('#modalIntransitKhusus').modal('show');

                }
            });

        }

        function showIntransit(category) {

            $('#tableIntransitBody').html('');

            var bodyDataList = '';
            var loop = 0;
            for (let i = 0; i < intransit.length; i++) {
                var split = intransit[i].location.split('-');

                if (category == split[2]) {
                    bodyDataList += '<tr>';
                    bodyDataList += '<td style="vertical-align: middle; text-align: center;">' + (++loop) + '</td>';
                    bodyDataList += '<td style="vertical-align: middle; text-align: center;">' + category + '</td>';

                    bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                    bodyDataList += intransit[i].tag;
                    bodyDataList += '</td>';

                    bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                    bodyDataList += intransit[i].material_number;
                    bodyDataList += '</td>';

                    bodyDataList += '<td style="vertical-align: middle; text-align: left;">';
                    bodyDataList += intransit[i].material_description;
                    bodyDataList += '</td>';

                    bodyDataList += '<td style="vertical-align: middle; text-align: right;">';
                    bodyDataList += intransit[i].quantity;
                    bodyDataList += '</td>';

                    bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                    bodyDataList += intransit[i].issue_location;
                    if (category == 'KHUSUS') {
                        bodyDataList += ' <i class="fa fa-exchange"></i> ';
                    } else {
                        bodyDataList += ' <i class="fa fa-long-arrow-right"></i> ';
                    }
                    bodyDataList += intransit[i].receive_location;
                    bodyDataList += '</td>';

                    bodyDataList += '<td style="vertical-align: middle; text-align: left;">';
                    bodyDataList += intransit[i].transaction_by + '<br>';
                    bodyDataList += callName(intransit[i].transaction_by_name);
                    bodyDataList += '</td>';

                    bodyDataList += '<td style="vertical-align: middle; text-align: left;">';
                    bodyDataList += intransit[i].created_by + '<br>';
                    bodyDataList += callName(intransit[i].created_by_name);
                    bodyDataList += '</td>';

                    bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                    bodyDataList += intransit[i].created_at;
                    bodyDataList += '</td>';

                    if (category == 'KANBAN' && remark.includes('OUT')) {
                        bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                        bodyDataList += '<button style="padding-top: 0px; padding-bottom: 0px;" ';
                        bodyDataList += 'class="btn btn-sm btn-danger" ';
                        bodyDataList += 'onClick="cancelIntransit(id)" id="' + intransit[i].id + '">';
                        bodyDataList += '<i class="fa fa-trash"></i>';
                        bodyDataList += '</button>';
                        bodyDataList += '</td>';
                    } else {
                        bodyDataList += '<td style="vertical-align: middle; text-align: center;">';
                        bodyDataList += '-';
                        bodyDataList += '</td>';
                    }

                    bodyDataList += '</tr>';

                }
            }

            if (loop == 0) {
                openErrorGritter('Tidak Ada Data Intransit!');
                return false;
            }

            $('#tableIntransitBody').html(bodyDataList);
            $('#modalIntransit').modal('show');

        }


        function cancelIntransit(id) {

            if (confirm('Apakah anda yakin untuk membatalkan verifikasi transaksi ini?')) {
                $('#loading').show();
                var data = {
                    id: id,
                }
                $.post('{{ url('delete/material_in_out') }}', data, function(result, status, xhr) {
                    if (result.status) {

                        fillIntransitTable();
                        $('#modalIntransit').modal('hide');
                        $('#loading').hide();
                        openSuccessGritter('Success', result.message);

                    } else {

                        audio_error.play();
                        $('#modalError').modal('show');
                        $('#modalErrorMessage').text(result.message);
                        $('#loading').hide();

                    }
                });
            }

        }


        $('#finish').on('click', function() {
            if (list.length <= 0) {
                $('#modalError').modal('show');
                $('#modalErrorMessage').text('Belum ada kanban/slip yang discan');
                audio_error.play();
                return false;
            } else {
                $("#finish").prop('disabled', true);
                $("#finish").css('display', 'none');

                submit();
            }
        });

        function submit(params) {
            // if (isAjax) {
            //     return false;
            // }
            // isAjax = true;
            var remark = $('#remark').val();
            var employee_id = $('#employee_id').val();
            var employee_name = $('#employee_name').text().split(" : ")[1];


            var data = {
                remark: remark,
                category: category,
                list: list,
                transaction_by: employee_id,
                transaction_by_name: employee_name,
            }

            $('#loading').show();
            $.post('{{ url('input/material_in_out') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $("#finish").prop('disabled', true);
                    $("#finish").css('display', 'none');

                    $('#employee_tag').val("");
                    $("#employee_tag").blur();
                    $("#employee_tag").prop('disabled', false);
                    $("#employee_tag").css('display', 'none');
                    $('#employee_name').text('');

                    $('#header-type').html('');

                    $("#barcode").css('display', 'none');
                    $("#barcode").text('');

                    $("#submit").css('display', 'block');
                    $("#cancel").css('display', 'none');
                    $("#finish").css('display', 'none');

                    $("#scan-resume").css('display', 'none');
                    $("#scan-list").css('display', 'none');

                    fillIntransitTable();
                    $('#loading').hide();
                    openSuccessGritter('Success', result.message);
                    // isAjax = false;


                } else {

                    audio_error.play();
                    $('#modalError').modal('show');
                    $('#modalErrorMessage').text(result.message);
                    $('#loading').hide();
                    // isAjax = false;

                }
            });

        }

        $('#cancel').on('click', function() {

            $('#employee_tag').val("");
            $("#employee_tag").blur();
            $("#employee_tag").prop('disabled', false);
            $("#employee_tag").css('display', 'none');
            $('#employee_name').text('');

            $('#header-type').html('');

            $("#barcode").css('display', 'none');
            $("#barcode").text('');

            $("#submit").css('display', 'block');
            $("#cancel").css('display', 'none');
            $("#finish").css('display', 'none');

            $("#scan-resume").css('display', 'none');
            $("#scan-list").css('display', 'none');

        });

        function callName(name) {
            var new_name = '';
            var blok_m = [
                'M.',
                'Mas',
                'Moch',
                'Moch.',
                'Mochamad',
                'Mochammad',
                'Mohammad',
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
