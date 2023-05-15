@php
    use App\EmployeeSync;
    use App\ApprovalPresidentDirectorApprove;
    use Illuminate\Support\Facades\DB;
@endphp


@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">


    <style type="text/css">
        .content {
            background-color: #F3f3f3;
        }

        .content-header {
            background-color: #F3f3f3 !important;
            color: black;
        }

        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            /*text-align:center;*/
            overflow: hidden;
        }

        tbody>tr>td {
            /*text-align:center;*/
            padding-left: 5px !important;
        }

        tfoot>tr>th {
            /*text-align:center;*/
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
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            vertical-align: middle;
            padding: 0;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            padding: 0;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #loading,
        #error {
            display: none;
        }

        .containers {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 15px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            padding-top: 6px;
        }

        /* Hide the browser's default checkbox */
        .containers input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        /* Create a custom checkbox */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #eee;
            margin-top: 4px;
        }

        /* On mouse-over, add a grey background color */
        .containers:hover input~.checkmark {
            background-color: #ccc;
        }

        /* When the checkbox is checked, add a blue background */
        .containers input:checked~.checkmark {
            background-color: #2196F3;
        }

        /* Create the checkmark/indicator (hidden when not checked) */
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the checkmark when checked */
        .containers input:checked~.checkmark:after {
            display: block;
        }

        /* Style the checkmark/indicator */
        .containers .checkmark:after {
            left: 9px;
            top: 5px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            -webkit-transform: rotate(45deg);
            -ms-transform: rotate(45deg);
            transform: rotate(45deg);
        }

        .approver_column {
            cursor: pointer;
        }

        .approver_column_label {
            font-size: 13px;
        }

        .btn-action button {
            margin: 2px 2px;
            width: 100px;
            text-align: left;
        }

        .dt-button-background {
            display: none !important;
            position: :static !important;
        }

        input[type="file"] {
            display: none;
        }

        .custom-file-upload {
            text-align: center;
            position: relative;
            border: 1px solid #ccc;
            display: inline-block;
            /* padding: 6px 12px; */
            cursor: pointer;

            width: 100%;
        }

        .file-upload-box {
            margin-left: -5px;
        }

        .custom-file-upload span {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            font-size: 16px;
            font-weight: bold;
            color: #555;
        }
        .theadInit {
            color: #fff !important;
        }

        .tableHeadSearch {
            color: #333 !important;
        }
        
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }} <small><span class="text-purple">{{ $title_jp }}</span></small>
            <button class="btn btn-success btn-sm pull-right" data-toggle="modal" data-target="#create_modal" style="margin-right: 5px" onclick="clearAll()">
                <i class="fa fa-plus"></i>&nbsp;&nbsp;Buat Pengajuan
            </button>
            @if ($user)
                @if ($user->employee_id == 'PI1212001' /*Eko Junaedi*/)
                    <a class="btn btn-primary btn-sm pull-right" href="{{ route('indexApprovalPresidentDirectorReport') }}" style="margin-right: 5px">
                        <i class="fa fa-book"></i>&nbsp;&nbsp; Log Approval President Director
                    </a>
                @endif
            @endif

            <button class="btn btn-info btn-sm pull-right" style="margin-right: 5px" onclick="fillList()">
                <i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
            </button>
        </h1>
    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>

        <div class="row box box-solid dropShadow" style="margin: 0;">
            <div class="col-xs-12 pull-left" style="padding: 10px 10px;">
                <div style="background-color: #3f51b5; color:white;padding: 5px;text-align: center;margin-bottom: 8px; border-radius: 2px;">
                    {{-- <br /> --}}
                    <span style="font-weight: bold;font-size: 30px">IN PROGRESS</span>
                    {{-- <span class="btn btn-danger">5</span> --}}
                </div>
                <div id="tableProgressContainer"></div>
            </div>
        </div>
        <br />
        <div class="row box box-solid dropShadow" style="margin: 0;">
            <div class="col-xs-12 pull-left" style="padding:10px 10px;">
                <div style="background-color: #32a852; color: white;padding: 5px;text-align: center;margin-bottom: 8px; border-radius: 2px;">
                    <span style="font-weight: bold;font-size: 30px">COMPLETED</span>
                </div>
                <div id="tableCompleteContainer"></div>
            </div>
        </div>

        {{-- modal RFID --}}
        <div class="modal modal-default fade" id="sender_rfid">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                            <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">SCAN RFID</h1>
                        </div>
                    </div>
                    <div class="modal-body">

                    </div>
                </div>
            </div>
        </div>

        {{-- modal hapus --}}
        <div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        Are you sure delete?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- OPTIMIZE modal tambah pengajuan --}}
        <div class="modal modal-default fade" id="create_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                            <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Pengajuan Permohonan Presiden Direktur</h1>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box-body">
                                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                                    <div class="form-group row">
                                        <label class="col-sm-2">Yang Mengajukan<span class="text-red">*</span></label>
                                        <div class="col-sm-6" align="left">
                                            <input type="text" class="form-control" id="add_applicant" placeholder="Requested" required value="<?php if (isset($user->employee_id)) {
                                                echo $user->employee_id;
                                            } else {
                                                echo '';
                                            } ?> - <?php if (isset($user->name)) {
                                                echo $user->name;
                                            } else {
                                                echo '';
                                            } ?>" readonly>
                                        </div>
                                    </div>
                                    @if ($user)                 
                                        @if ($user->employee_id == 'PI1212001' /*Eko Junaedi*/ || $user->employee_id == 'PI2301032' /*Hentong*/)
                                            {{-- $user->department == 'General Affairs Department' || --}}                                            
                                            <div class="form-group row">
                                                <label class="col-sm-2" for="otherAppl">Ajukan Orang Lain <br><small class="text-red">(Admin Only)</small></label>
                                                <div class="col-sm-6 custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="otherAppl">
                                                </div>
                                            </div>
                                            <div class="form-group row" id='otherApplInput' style="display: none">
                                                <label class="col-sm-2"> # <span class="text-red">*</span></label>
                                                <div class="col-sm-6" align="left" id="selectEmpAppl">                                                    
                                                    <select class="form-control selectEmpAppl" data-placeholder="Pilih Karyawan" name="other_employees" id="add_applicant_other" style="width: 100%">
                                                        <option value=""></option>
                                                        @foreach($employees as $emp)
                                                        <option value="{{$emp->employee_id}} - {{$emp->name}}">{{$emp->employee_id}} - {{$emp->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    <hr>                                    
                                    <div class="form-group row">
                                        <label class="col-sm-2">Tujuan Penerima<span class="text-red">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" placeholder="Masukkan Penerima" name="" id="add_recipient">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2">Nama Dokumen<span class="text-red">*</span></label>
                                        <div class="col-sm-6">
                                            <textarea type="text" class="form-control" id="add_document_name" placeholder="Masukkan Nama Dokumen" required style="resize:vertical"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2">Maksud dan Tujuan<span class="text-red">*</span></label>
                                        <div class="col-sm-6">
                                            <textarea type="text" class="form-control" id="add_purpose" placeholder="Masukkan Detail Keperluan" required style="resize:vertical"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2">Jumlah Salinan<span class="text-red">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="number" class="form-control" placeholder="Masukkan Jumlah Hardcopy" id="add_hardcopy_total">
                                        </div>
                                    </div>
                                    {{-- OPTIMIZE --}}
                                    {{-- <small>(Opsional)</small> --}}
                                    <div class="form-group row">
                                        <label class="col-sm-3">Attachment<span class="text-red">*</span></label>
                                        <div class="col-sm-4 file-upload-box" style="padding: 0 !important;">
                                            <label for="file-upload" class="custom-file-upload">
                                                <ol class="jLabel" style="list-style:none; padding: 5px; color:grey;">
                                                    <li><i class="fa fa-upload"></i> Browse File Here...</li>
                                                </ol>
                                            </label>
                                            <input id="file-upload" type="file" multiple />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>
                        <button class="btn btn-success" onclick="createRequest()"><i class="fa fa-plus"></i> Buat Pengajuan</button>
                    </div>
                </div>
            </div>
        </div>

        {{--  Attachment Modal --}}
        <div class="modal modal-default fade" id="attachment_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color: #3C8DBC; padding-right: 1%;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                            <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Attachment</h1>
                        </div>
                    </div>
                    <div class="modal-body" style="background-color:#f3f3f3;">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box-body" id="attachment_data" style="display:flex; align-items:center; justify-content:center; flex-flow: row wrap;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal edit pengajuan --}}
        <div class="modal modal-default fade" id="edit_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color: #F39C12; padding-right: 1%;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                            <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Pengajuan Permohonan Presiden Direktur</h1>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box-body">
                                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                                    <input type="hidden" value="" id="edit_request_id">
                                    <div class="form-group row">
                                        <label class="col-sm-2">Yang Mengajukan<span class="text-red">*</span></label>
                                        <div class="col-sm-6" align="left">
                                            <input type="text" class="form-control" id="edit_applicant" placeholder="Requested" required value="<?php if (isset($user->employee_id)) {
                                                echo $user->employee_id;
                                            } else {
                                                echo '';
                                            } ?> - <?php if (isset($user->name)) {
                                                echo $user->name;
                                            } else {
                                                echo '';
                                            } ?>" readonly>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group row">
                                        <label class="col-sm-2">Penerima<span class="text-red">*</span></label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" placeholder="Masukkan Penerima" name="" id="edit_recipient">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2">Nama Dokumen<span class="text-red">*</span></label>
                                        <div class="col-sm-6">
                                            <textarea type="text" class="form-control" id="edit_document_name" placeholder="Masukkan Nama Dokumen" required style="resize:vertical"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-2">Maksud dan Tujuan<span class="text-red">*</span></label>
                                        <div class="col-sm-6">
                                            <textarea type="text" class="form-control" id="edit_purpose" placeholder="Masukkan Detail Keperluan" required style="resize:vertical"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>
                        <button class="btn btn-warning" onclick="editRequest()"><i class="fa fa-pencil"></i> Edit</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal detail pengajuan --}}
        <div class="modal modal-default fade" id="detail_modal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">
                                    &times;
                                </span>
                            </button>
                            <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Detail Pengajuan Approval</h1>
                        </div>
                    </div>
                    <div class="modal-body" style="padding-top: 0px">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="box-body" style="padding-top: 0px;margin-top: 0px">
                                    <center style="padding-top: 0px">
                                        <div style="width: 60%" style="padding-top: 0px">
                                            <table style="border:1px solid black; border-collapse: collapse;">
                                                <tbody align="center">
                                                    <tr>
                                                        <td colspan="2" style="border:1px solid black; font-size: 20px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Detail Informasi Pengajuan</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                                            Request ID

                                                        </td>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="detail_request_id">

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                                            Penerima

                                                        </td>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="detail_recipient">

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                                            Status

                                                        </td>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; padding: 0 0 5px 0" id="detail_status">

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                                            Department
                                                        </td>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="detail_department">

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                                            Keperluan
                                                        </td>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 50;" id="detail_purpose">

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                                            Attachment
                                                        </td>
                                                        <td style="border:1px solid black; font-size: 13px; width: 20%; height: 50px;" id="detail_attachment">

                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="detail_modal_footer" class="modal-footer">
                    </div>
                </div>
            </div>
        </div>





    </section>
@endsection
@section('scripts')

    <script src="{{ url('js/moment.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
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

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var employees = [];
        var count = 0;
        var destinations = [];
        var countDestination = 0;
        var storedDataRequest = [];
        var storedDataComplete = [];
        var storedDataApproval = [];

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            fillList();
            clearAll();

            $('.datepicker').datepicker({
                <?php $tgl_max = date('Y-m-d'); ?>
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true,
                startDate: '<?php echo $tgl_max; ?>'
            });

            $('.timepicker').timepicker({
                showInputs: false,
                showMeridian: false,
                defaultTime: '0:00',
            });
            
            $('#otherAppl').on('click', function() {
                if ($(this).is(':checked')) {
                    $('#otherApplInput').show();
                } else {
                    $('#otherApplInput').hide();
                }
            });
        });


        $(function() {
            $('.selectPur').select2({
                dropdownParent: $('#selectPur'),
                allowClear: true
            });
            $('.selectCity').select2({
                dropdownParent: $('#selectCitys'),
                allowClear: true
            });
            $('.selectDet').select2({
                dropdownParent: $('#selectDet'),
                allowClear: true
            });
            $('.selectEmp').select2({
                dropdownParent: $('#selectEmp'),
                allowClear: true
            });

            $('.selectPurEdit').select2({
                dropdownParent: $('#selectPurEdit'),
                allowClear: true
            });
            $('.selectCityEdit').select2({
                dropdownParent: $('#selectCityEdits'),
                allowClear: true
            });
            $('.selectDetEdit').select2({
                dropdownParent: $('#selectDetEdit'),
                allowClear: true
            });
            $('.selectEmpEdit').select2({
                dropdownParent: $('#selectEmpEdit'),
                allowClear: true
            });

            $('.selectEmpAppl').select2({
                dropdownParent: $('#selectEmpAppl'),
                allowClear:true
            });
        });

        document.getElementById("file-upload").onchange = function() {

            $('.jLabel').html('');
            $('.jLabel').css('text-align', 'left');
            $('.jLabel').css('list-style', 'decimal');
            $('.jLabel').css('margin-left', '20px');

            let fileList = [];
            for (var i = 0; i < this.files.length; i++) {
                fileList.push("<li>" + this.files[i].name + "</li>");
            }
            $('.jLabel').append(fileList);

            $('#add_hardcopy_total').val(this.files.length);

        };

        function tableInit() {
            $('#tableProgressContainer').html('');

            var tableProgressInit = '';
            tableProgressInit += '<table id="tableProgress" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">';
            tableProgressInit += '<thead style="background-color: rgb(126,86,134); color: #fff;">';
            tableProgressInit += '<tr>';
            tableProgressInit += '<th class="theadInit" width="1%">Req</th>';
            tableProgressInit += '<th class="theadInit" width="0.1%">Dept</th>';
            tableProgressInit += '<th class="theadInit" width="1%" style="background-color: #3064db">Aplikator</th>';
            tableProgressInit += '<th class="theadInit" width="0.1%" style="background-color: #3064db">GM Dept Pengaju</th>';
            tableProgressInit += '<th class="theadInit" width="2%">Nama Dokumen</th>';
            tableProgressInit += '<th class="theadInit" width="0.2%" style="font-size:14px;">Jumlah Salinan</th>';
            tableProgressInit += '<th class="theadInit" width="1%" style="background-color: #3064db">Tujuan Pengiriman</th>';
            tableProgressInit += '<th class="theadInit" width="2%">Maksud dan Tujuan</th>';
            tableProgressInit += '<th class="theadInit" width="1%">#</th>';
            tableProgressInit += '</tr>';
            tableProgressInit += '</thead>';
            tableProgressInit += '<tbody id="bodyTableProgress">';
            tableProgressInit += '</tbody>';            
            tableProgressInit += '<tfoot style="background-color: rgb(252,248,227);">';
            tableProgressInit += '<tr>';
            tableProgressInit += '<th class="theadInit" width="1%">Req</th>';
            tableProgressInit += '<th class="theadInit" width="0.1%">Dept</th>';
            tableProgressInit += '<th class="theadInit" width="1%" style="background-color: #3064db">Aplikator</th>';
            tableProgressInit += '<th class="theadInit" width="0.1%" style="background-color: #3064db">GM Dept Pengaju</th>';
            tableProgressInit += '<th class="theadInit" width="2%">Nama Dokumen</th>';
            tableProgressInit += '<th class="theadInit" width="0.2%" style="font-size:14px;">Jumlah Salinan</th>';
            tableProgressInit += '<th class="theadInit" width="1%" style="background-color: #3064db">Tujuan Pengiriman</th>';
            tableProgressInit += '<th class="theadInit" width="2%">Maksud dan Tujuan</th>';
            tableProgressInit += '<th class="theadInit" width="1%">#</th>';
            tableProgressInit += '</tr>';
            tableProgressInit += '</tfoot>';            
            tableProgressInit += '</table>';

            $('#tableProgressContainer').append(tableProgressInit);

            $('#tableCompleteContainer').html('');

            var tableCompleteInit = '';
            tableCompleteInit += '<table id="tableComplete" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">';
            tableCompleteInit += '<thead style="background-color: rgb(126,86,134); color: #fff;">';
            tableCompleteInit += '<tr>';
            tableCompleteInit += '<th class="theadInit" width="1%">Req</th>';
            tableCompleteInit += '<th class="theadInit" width="0.1%">Dept</th>';
            tableCompleteInit += '<th class="theadInit" width="1%" style="background-color: #3064db">Aplikator</th>';
            tableCompleteInit += '<th class="theadInit" width="0.1%" style="background-color: #3064db">GM Dept Pengaju</th>';
            tableCompleteInit += '<th class="theadInit" width="2%">Nama Dokumen</th>';
            tableCompleteInit += '<th class="theadInit" width="0.2%">Jumlah Salinan</th>';
            tableCompleteInit += '<th class="theadInit" width="1%" style="background-color: #3064db">Tujuan Pengiriman</th>';
            tableCompleteInit += '<th class="theadInit" width="2%">Maksud dan Tujuan</th>';
            tableCompleteInit += '<th class="theadInit" width="1%">#</th>';
            tableCompleteInit += '</tr>';
            tableCompleteInit += '</thead>';
            tableCompleteInit += '<tbody id="bodyTableComplete">';
            tableCompleteInit += '</tbody>';
            tableCompleteInit += '<tfoot style="background-color: rgb(252,248,227);">';
            tableCompleteInit += '<tr>';
            tableCompleteInit += '<th class="theadInit" width="1%">Req</th>';
            tableCompleteInit += '<th class="theadInit" width="0.1%">Dept</th>';
            tableCompleteInit += '<th class="theadInit" width="1%" style="background-color: #3064db">Aplikator</th>';
            tableCompleteInit += '<th class="theadInit" width="0.1%" style="background-color: #3064db">GM Dept Pengaju</th>';
            tableCompleteInit += '<th class="theadInit" width="2%">Nama Dokumen</th>';
            tableCompleteInit += '<th class="theadInit" width="0.2%">Jumlah Salinan</th>';
            tableCompleteInit += '<th class="theadInit" width="1%" style="background-color: #3064db">Tujuan Pengiriman</th>';
            tableCompleteInit += '<th class="theadInit" width="2%">Maksud dan Tujuan</th>';
            tableCompleteInit += '<th class="theadInit" width="1%">#</th>';
            tableCompleteInit += '</tr>';
            tableCompleteInit += '</tfoot>';            
            tableCompleteInit += '</table>';

            $('#tableCompleteContainer').append(tableCompleteInit);
        }

        function clearAll() {
            $('#add_recipient').val('');
            $('#add_purpose').val('');
            $('#add_document_name').val('');
            $('#add_attachment').val('');
            $('#file-upload').val('');
            $('#add_hardcopy_total').val('');
            $('#otherAppl').prop('checked', false);            
            $('#otherApplInput').hide();
            $('#otherApplInput').val('');

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
                time: '2000'
            });
        }

        function fillList() {
            $("#loading").show();
            
            var applicant = '{{ $user->employee_id }}';

            var data = {                
                'applicant': applicant
            }

            $.post('{{ url('fetch/ga_secretary/president_director/approval') }}', data, function(result, status, xhr) {
                storedDataRequest = result.presdir_request;
                storedDataApproval = result.presdir_approvals;
                storedDataComplete = result.presdir_request_completed;

                if (result.status) {
                    tableInit();

                    var tableDataProgress = "";
                    $.each(result.presdir_request, function(key, value) {
                        tableDataProgress += '<tr>';
                        if (value.status == 'Delivering') {
                            var remarkss = '<span class="label label-primary" style="margin-right:4px;">' + value.status + '...</span>';
                        } else if (value.status == 'Requested') {
                            var remarkss = '<span class="label label-primary" style="margin-right:4px;">' + value.status + '</span>';
                        } else if (value.status == 'Approved') {
                            var remarkss = '<span class="label label-success" style="margin-right:4px;">' + value.status + '</span>';
                        } else if (value.status == 'Rejected') {
                            var remarkss = '<span class="label label-default" style="margin-right:4px;">' + value.status + '</span>';
                        } else if (value.status == 'Cancelled') {
                            var remarkss = '<span class="label label-default" style="margin-right:4px;">' + value.status + '</span>';
                        } else {
                            var remarkss = '<span class="label label-default" style="margin-right:4px;">' + value.status + '</span>';
                        }
                        tableDataProgress += '<td><span style="font-weight:bold;color:red;">' + value.request_id + '</span><br>' + remarkss + '<br>';
                        let newValueUploadDate = new Date(value.created_at);
                        let todayUploadDate = new Date();
                        let differenceInDaysUploadDate = Math.round((todayUploadDate - newValueUploadDate) / (1000 * 60 * 60 * 24));

                        if (differenceInDaysUploadDate == 0) {
                            differenceInDaysUploadDate = 'Today';
                        } else {
                            differenceInDaysUploadDate = differenceInDaysUploadDate + ' days ago';
                        }

                        tableDataProgress += '<span class="approver_column_label">' + value.date + '</span><br>';
                        tableDataProgress += '<span class="approver_column_label">(' + differenceInDaysUploadDate + ')</span>';
                        tableDataProgress += '</td>';

                        tableDataProgress += '<td style="text-align:center; padding:0 !important;">' + value.department_shortname + '</td>';

                        $.each(result.presdir_approvals, function(key, val) {
                            if (val.request_id == value.request_id) {
                                tableDataProgress += '<td class="approver_column"';

                                // column color
                                if (val.status == 'Approved') {
                                    tableDataProgress += 'style="background-color:#00A65A;color:white;font-size:11px;font-weight:bold;">';
                                } else if (val.status == 'Rejected') {
                                    tableDataProgress += 'style="background-color:#2B2B2B;color:white;font-size:11px;font-weight:bold;">';
                                } else {
                                    tableDataProgress += 'style="background-color:#FFF;color:black;font-size:11px;font-weight:bold;">';
                                }

                                // NAME 
                                tableDataProgress += '<span class="approver_column_label">' + val.person_name + '</span><br>';

                            }

                        });

                        tableDataProgress += '<td><b>' + value.document_name + '</b></td>';
                        tableDataProgress += '<td style="text-align:center;"><button class="btn btn-primary" data-toggle="modal" onclick="showAttachment(\'' + value.request_id + '\',\'' + value.remark + '\')" data-target="#attachment_modal">' + (value.hardcopy_total == null ? '-' : value.hardcopy_total) + '</button></td>';
                        tableDataProgress += '<td>' + value.recipient + '</td>';
                        tableDataProgress += '<td>' + value.purpose + '</td>';


                        // TODO ACTION APPLICANT 
                        tableDataProgress += '<td class="btn-action">';

                        @if ($user)
                            @if ($user->employee_id == 'PI1212001' /*Eko Junaedi*/ 
                            || $user->employee_id == 'PI2301032' /*Hentong*/
                                )

                                tableDataProgress += '<button class="btn btn-info btn-xs" data-toggle="modal" data-target="#detail_modal" onclick="showDetail(\'' + value.request_id + '\',\'' + value.department + '\',\'' + value.purpose + '\',\'' + value.document_name + '\',\'' + value.recipient + '\',\'' + value.status + '\',\'' + value.remark + '\')" style="margin-right:5px;"><i class="fa fa-eye"></i> Detail</button>';
                                tableDataProgress += '<button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#edit_modal" onclick="editModal(\'' + value.request_id + '\',\'' + value.recipient + '\',\'' + value.document_name + '\',\'' + value.purpose + '\')" style="margin-right:5px;"><i class="fa fa-pencil"></i> Edit</button>';
                                // tableDataProgress += '<button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#edit_modal" onclick="editRequest(\'' + value.request_id + '\')" style="margin-right:5px;"><i class="fa fa-edit"></i> Edit</button>';
                                // tableDataProgress += '<button class="btn btn-primary btn-xs" onclick="resendEmail(\'' + value.request_id + '\')" style="margin-right:5px;"><i class="fa fa-envelope"></i> Resend Email</button>';
                                tableDataProgress += '<button class="btn btn-success btn-xs" onclick="completeRequest(\'' + value.request_id + '\')" style="margin-right:5px;"><i class="fa fa-check"></i> Complete</button>';
                                tableDataProgress += '<button class="btn btn-danger btn-xs" onclick="cancelRequest(\'' + value.request_id + '\')" style="margin-right:5px;"><i class="fa fa-close"></i> Cancel</button>';
                            @else
                                if (value.applicant == '<?= $user->employee_id ?>') {
                                    tableDataProgress += '<button class="btn btn-info btn-xs" data-toggle="modal" data-target="#detail_modal" onclick="showDetail(\'' + value.request_id + '\',\'' + value.department + '\',\'' + value.purpose + '\',\'' + value.document_name + '\',\'' + value.recipient + '\',\'' + value.status + '\',\'' + value.remark + '\')" style="margin-right:5px;"><i class="fa fa-eye"></i> Detail</button>';
                                    tableDataProgress += '<button class="btn btn-warning btn-xs" data-toggle="modal" data-target="#edit_modal" onclick="editModal(\'' + value.request_id + '\',\'' + value.recipient + '\',\'' + value.document_name + '\',\'' + value.purpose + '\')" style="margin-right:5px;"><i class="fa fa-pencil"></i> Edit</button>';
                                    tableDataProgress += '<button class="btn btn-danger btn-xs" onclick="cancelRequest(\'' + value.request_id + '\')" style="margin-right:5px;"><i class="fa fa-close"></i> Cancel</button>';
                                }
                            @endif
                        @endif

                        tableDataProgress += '</td>';
                        tableDataProgress += '</tr>';
                    });



                    $('#bodyTableProgress').append(tableDataProgress);

                    $('#tableProgress tfoot th').each(function(index) {
                        if (index == 1) { 
                            var title = $(this).text();
                            $(this).html('<select class="tableHeadSearch"><option value="">All</option>@foreach ($departments as $dept)<option value="{{ $dept->department_shortname }}">{{ $dept->department_shortname }}</option>@endforeach</select>');
                        }
                        else if(index == 3) {
                            var title = $(this).text();
                            $(this).html('<select class="tableHeadSearch" style="width:100%;"><option value="">All</option>@foreach ($divisions as $divs)<option value="{{ $divs->division_name }}">{{ $divs->division_name }}</option>@endforeach</select>');
                        }                        
                        else if(index == 8) {
                            $(this).html(' ');
                        }
                        else {
                            var title = $(this).text();
                            $(this).html('<input class="tableHeadSearch" style="text-align: center;" type="text" placeholder="Search ' + title + '" />');						
                        }
                    });


                    var table = $('#tableProgress').DataTable({
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
                                        columns: ':not(.notexport)',
                                        columns: [0, 1, 2, 3, 4, 5, 6, 7]
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

                    table.columns().every(function() {
                        var that = this;

                        $('input', this.footer()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });

                        $('select', this.footer()).on('change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('#tableProgress tfoot tr').appendTo('#tableProgress thead');

                    // OPTIMIZE THIS IS TABLE COMPLETE 

                    var tableDataComplete = '';
                    $.each(result.presdir_request_completed, function(key, value) {
                        tableDataComplete += '<tr>';
                        if (value.status == 'Requesting') {
                            var remarkss = '<span class="label label-primary" style="margin-right:4px;">' + value.status + '</span>';
                        } else if (value.status == 'Cancelled') {
                            var remarkss = '<span class="label label-danger" style="margin-right:4px;">' + value.status + '</span>';
                        } else {
                            var remarkss = '<span class="label label-success" style="margin-right:4px;">' + value.status + '</span>';
                        }
                        tableDataComplete += '<td><span style="font-weight:bold;color:red;">' + value.request_id + '</span><br>' + remarkss + '<br>';

                        let newValueUploadDate = new Date(value.updated_at);
                        let todayUploadDate = new Date();
                        let differenceInDaysUploadDate = Math.round((todayUploadDate - newValueUploadDate) / (1000 * 60 * 60 * 24));

                        if (differenceInDaysUploadDate == 0) {
                            differenceInDaysUploadDate = 'Today';
                        } else {
                            differenceInDaysUploadDate = differenceInDaysUploadDate + ' days ago';
                        }

                        tableDataComplete += '<span class="approver_column_label">' + value.date + '</span><br>';
                        tableDataComplete += '<span class="approver_column_label">Updated (' + differenceInDaysUploadDate + ')</span>';
                        tableDataComplete += '</td>';

                        tableDataComplete += '<td style="text-align:center;">' + value.department_shortname + '</td>';

                        $.each(result.presdir_approvals, function(key, val) {
                            if (val.request_id == value.request_id) {
                                tableDataComplete += '<td class="approver_column"';

                                // column color
                                if (val.status == 'Approved') {
                                    tableDataComplete += 'style="background-color:#00A65A;color:white;font-size:11px;font-weight:bold;">';
                                } else if (val.status == 'Rejected') {
                                    tableDataComplete += 'style="background-color:#2B2B2B;color:white;font-size:11px;font-weight:bold;">';
                                } else {
                                    tableDataComplete += 'style="background-color:#DD4B39;color:white;font-size:11px;font-weight:bold;">';
                                }


                                tableDataComplete += '<span class="approver_column_label">' + val.person_name + '</span><br>';

                                tableDataComplete += '</td>';
                            }
                        });

                        tableDataComplete += '<td>' + value.document_name + '</td>';
                        tableDataComplete += '<td style="text-align:center;"><button class="btn btn-primary" data-toggle="modal" onclick="showAttachment(\'' + value.request_id + '\',\'' + value.remark + '\')" data-target="#attachment_modal">' + (value.hardcopy_total == null ? '-' : value.hardcopy_total) + '</button></td>';
                        tableDataComplete += '<td>' + value.recipient + '</td>';
                        tableDataComplete += '<td>' + value.purpose + '</td>';

                        tableDataComplete += '<td class="btn-action">';
                        tableDataComplete += '<button class="btn btn-info btn-xs" data-toggle="modal" data-target="#detail_modal" onclick="showDetail(\'' + value.request_id + '\',\'' + value.department + '\',\'' + value.purpose + '\',\'' + value.document_name + '\',\'' + value.recipient + '\',\'' + value.status + '\',\'' + value.remark + '\')" style="margin-right:5px;"><i class="fa fa-eye"></i> Detail</button>';
                        tableDataComplete += '</td>';

                        tableDataComplete += '</tr>';
                    });

                    $('#bodyTableComplete').append(tableDataComplete);

                    $('#tableComplete tfoot th').each(function(index) {
                        if (index == 1) { 
                            var title = $(this).text();
                            $(this).html('<select class="tableHeadSearch"><option value="">All</option>@foreach ($departments as $dept)<option value="{{ $dept->department_shortname }}">{{ $dept->department_shortname }}</option>@endforeach</select>');
                        }
                        else if(index == 3) {
                            var title = $(this).text();
                            $(this).html('<select class="tableHeadSearch" style="width:100%;"><option value="">All</option>@foreach ($divisions as $divs)<option value="{{ $divs->division_name }}">{{ $divs->division_name }}</option>@endforeach</select>');
                        }                        
                        else if(index == 8) {
                            $(this).html(' ');
                        }
                        else {
                            var title = $(this).text();
                            $(this).html('<input class="tableHeadSearch" style="text-align: center;" type="text" placeholder="Search ' + title + '" />');						
                        }
                    });

                    var table = $('#tableComplete').DataTable({
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

                    table.columns().every(function() {
                        var that = this;

                        $('input', this.footer()).on('keyup change clear', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });

                        $('select', this.footer()).on('change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('#tableComplete tfoot tr').appendTo('#tableComplete thead');

                    $("#loading").hide();
                } else {
                    $("#loading").hide();
                    alert('Attempt to retrieve data failed');
                }
            });
        }

        function createRequest() {
            if ($('#add_recipient').val() === '') {
                openErrorGritter('Error', 'Penerima Harus Diisi');
                return false;
            }
            if ($('#add_document_name').val() === '') {
                openErrorGritter('Error', 'Nama Dokumen Harus Diisi');
                return false;
            }
            if ($('#add_purpose').val() === '') {
                openErrorGritter('Error', 'Tujuan Harus Diisi');
                return false;
            }            

            var applicant = '';

            if($('#otherAppl').is(':checked')){
                applicant = $('#add_applicant_other').val();
            }else{
                applicant = $('#add_applicant').val();
            }

            var res = applicant.split(' - ');
            var applicant_id = res[0];
            var applicant_name = res[1];            

            var formData = new FormData();

            var att_count = 0;
            for (var i = 0; i < $('#file-upload').prop('files').length; i++) {
                formData.append('file_upload_' + i, $('#file-upload').prop('files')[i]);
                att_count++;
            }

            formData.append('applicant_id', applicant_id);
            formData.append('applicant_name', applicant_name);
            formData.append('recipient', $('#add_recipient').val());
            formData.append('document_name', $('#add_document_name').val());
            formData.append('purpose', $('#add_purpose').val());
            formData.append('hardcopy_total', $('#add_hardcopy_total').val());
            formData.append('att_count', att_count);

            $('#loading').show();
            $.ajax({
                method: "POST",
                dataType: 'JSON',
                url: "{{ url('input/ga_secretary/president_director/applicant') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function(result) {
                    2
                    if (result.status) {
                        openSuccessGritter('Success!', 'Success Add Data');
                        $('#loading').hide();
                        $('#create_modal').modal('hide');
                        clearAll();
                        fillList();
                        $('#loading').hide();
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error!', result.message);
                    }
                }
            });
        }

        function cancelRequest(request_id) {
            if (confirm("Are you sure you want to cancel this request?")) {
                var data = {
                    request_id: request_id
                }

                $.post('{{ url('input/ga_secretary/president_director/cancel') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success', result.message);
                        fillList();
                    } else {
                        openErrorGritter('Error', result.message);
                    }
                });
            } else {
                return false;
            }
        }

        function showDetail(request_id, department, purpose, document_name, recipient, status, remark) {
            console.log(remark);
            $('#detail_request_id').html(request_id);
            $('#detail_department').html(department);
            $('#detail_recipient').html(recipient);
            $('#detail_document_name').html(document_name);
            $('#detail_purpose').html(purpose);
            $('#detail_status').css('font-size', '16px');
            if (status == 'Approved') {
                $('#detail_status').html('<span style="margin:0 0 5px 0;" class="label label-success">' + status + '</span>');
            } else if (status == 'Requesting') {
                $('#detail_status').html('<span style="margin:0 0 5px 0;" class="label label-warning">' + status + '</span>');
            } else if (status == 'Rejected') {
                $('#detail_status').html('<span style="margin:0 0 5px 0;" class="label label-danger">' + status + '</span>');
            } else {
                $('#detail_status').html('<span style="margin:0 0 5px 0;" class="label label-primary">' + status + '</span>');
            }

            if (status == 'Requesting') {
                $('#detail_modal_footer').html('<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button><button class="btn btn-success" onclick="completeRequest()"><i class="fa fa-check"></i> Complete</button>');
            } else {
                $('#detail_modal_footer').html('<button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-remove"></i> Batal</button>');
            }

            if (remark == 'Requested') {
                storedDataRequest.forEach(function(value, index) {
                    if (value.request_id == request_id) {
                        if (value.docs_name) {
                            var docs_name = value.docs_name.split("|");
                            var url = value.url.split("|");
                            var html = '';
                            for (var i = 0; i < docs_name.length; i++) {
                                html += '<a href="{{ url('') }}' + url[i] + '" target="_blank">' + docs_name[i] + '</a><br>';
                            }
                            $('#detail_attachment').html(html);
                        } else {
                            $('#detail_attachment').html('Tidak Ada Lampiran');
                        }
                    }
                });
            } else if (remark == 'Completed') {
                storedDataComplete.forEach(function(value, index) {
                    if (value.request_id == request_id) {
                        if (value.docs_name) {
                            var docs_name = value.docs_name.split("|");
                            var url = value.url.split("|");
                            var html = '';
                            for (var i = 0; i < docs_name.length; i++) {
                                html += '<a href="{{ url('') }}' + url[i] + '" target="_blank">' + docs_name[i] + '</a><br>';
                            }
                            $('#detail_attachment').html(html);
                        } else {
                            $('#detail_attachment').html('Tidak Ada Lampiran');
                        }
                    }
                });
            }
        }

        function editModal(request_id, recipient, document_name, purpose) {
            $('#edit_request_id').val(request_id);
            $('#edit_recipient').val(recipient);
            $('#edit_document_name').val(document_name);
            $('#edit_purpose').val(purpose);

            if ($('#edit_recipient').val() === '') {
                openErrorGritter('Error', 'Penerima Harus Diisi');
                return false;
            }
            if ($('#edit_document_name').val() === '') {
                openErrorGritter('Error', 'Nama Dokumen Harus Diisi');
                return false;
            }
            if ($('#edit_purpose').val() === '') {
                openErrorGritter('Error', 'Tujuan Harus Diisi');
                return false;
            }
        }

        function editRequest() {
            var request_id = $('#edit_request_id').val();
            var recipient = $('#edit_recipient').val();
            var document_name = $('#edit_document_name').val();
            var purpose = $('#edit_purpose').val();

            var data = {
                request_id: request_id,
                recipient: recipient,
                document_name: document_name,
                purpose: purpose,
            }

            $.post('{{ url('input/ga_secretary/president_director/edit') }}', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter('Success', result.message);
                    fillList();
                    $('#edit_modal').modal('hide');
                } else {
                    openErrorGritter('Error', result.message);
                }
            });
        }

        function completeRequest(id) {

            if (confirm("Are you sure you want to complete this request?")) {
                if (id) {
                    var request_id = id;
                } else {
                    var request_id = $('#detail_request_id').html();
                }

                var data = {
                    request_id: request_id
                }

                $.post('{{ url('input/ga_secretary/president_director/complete') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success', result.message);
                        fillList();
                        $('#detail_modal').modal('hide');
                    } else {
                        openErrorGritter('Error', result.message);
                    }
                });
            }
        }

        function showAttachment(request_id, remark) {
            if (remark == 'Requested') {
                storedDataRequest.forEach(function(value, index) {
                    if (value.request_id == request_id) {
                        if (value.docs_name) {
                            var docs_name = value.docs_name.split("|");
                            var url = value.url.split("|");
                            var html = '';
                            for (var i = 0; i < docs_name.length; i++) {
                                html += '<a href="{{ url('') }}' + url[i] + '" target="_blank" class="box box-solid dropShadow" style="width:150px; height:150px; text-align:center; padding:30px 0 0 0; margin:8px 8px; overflow:auto;"><i class="fa fa-file" style="font-size:40px;"></i><br>' + docs_name[i] + '</a>';
                            }


                            $('#attachment_data').html(html);


                        } else {
                            $('#attachment_data').html('Tidak Ada Lampiran');
                        }
                    }
                });
            } else if (remark == 'Completed') {
                storedDataComplete.forEach(function(value, index) {
                    if (value.request_id == request_id) {
                        if (value.docs_name) {
                            var docs_name = value.docs_name.split("|");
                            var url = value.url.split("|");
                            var html = '';
                            for (var i = 0; i < docs_name.length; i++) {
                                html += '<a href="{{ url('') }}' + url[i] + '" target="_blank" class="box box-solid dropShadow" style="width:150px; height:150px; text-align:center; padding:30px 0 0 0; margin:8px 8px; overflow:auto;"><i class="fa fa-file" style="font-size:40px;"></i><br>' + docs_name[i] + '</a>';
                            }

                            $('#attachment_data').html(html);


                        } else {
                            $('#attachment_data').html('Tidak Ada Lampiran');
                        }
                    }
                });
            }
        }

        function getActualFullDate() {
            var today = new Date();

            var date = today.getFullYear() + '-' + addZero(today.getMonth() + 1) + '-' + addZero(today.getDate());
            return date;
        }

        function addZero(number) {
            return number.toString().padStart(2, "0");
        }
    </script>
@endsection
