@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">
    <style type="text/css">
        .table>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        .table-pic tbody>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c !important;
        }

        .table-pic tbody>tr>td {
            height: 40px;
            padding: 5px 5px 5px 5px;
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
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .nmpd-grid {
            border: none;
            padding: 20px;
        }

        .nmpd-grid>tbody>tr>td {
            border: none;
        }

        .crop2 {
            overflow: hidden;
        }

        .crop2 img {
            height: 70px;
            margin: -8% 0 0 0 !important;
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
            @if (str_contains(Auth::user()->role_code, 'INT') || str_contains(Auth::user()->role_code, 'MIS'))
                <button class="btn btn-primary pull-right" style="margin-left: 5px; width: 10%;"
                    onclick="modalCreate('meeting');"><i class="fa fa-users"></i> Meeting</button>
                <a href="{{ url('/index/translation_resume') }}" class="btn btn-info pull-right"
                    style="margin-left: 5px; width: 10%;"><i class="fa fa-line-chart"></i> Resume</a>
            @endif
            <button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;"
                onclick="modalCreate('translation');"><i class="fa fa-file-text"></i> Request</button>
        </h1>
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
        <div class="row">
            <input type="hidden" id="createCategory">
            <input type="hidden" id="editID">
            <div class="col-lg-5" style="padding-right: 0;">
                <div class="box box-solid">
                    <div class="box-body">
                        <div id="container1" style="height: 40vh;"></div>
                        <br>
                        <center><span style="font-weight: bold; font-size: 1.2vw;">Interpreter Workload Detail</span>
                        </center>
                        <div id="translationPics">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="box box-solid">
                    <div class="box-body">
                        <div id="container2" style="height: 40vh;"></div>
                        <br>
                        <center><span style="font-weight: bold; font-size: 1.2vw;">Translation List (あ <i
                                    class="fa fa-exchange"></i> A)</span></center>
                        <table id="tableTranslation" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #605ca8; color: white;">
                                <tr>
                                    <th style="width: 0.5%; text-align: center;">ID/Title</th>
                                    <th style="width: 1%; text-align: left;">Requested By</th>
                                    <th style="width: 0.1%; text-align: right;">Due Date</th>
                                    <th style="width: 0.1%; text-align: right;">Estimated Pages</th>
                                    <th style="width: 0.1%; text-align: center;">Translation</th>
                                    <th style="width: 0.7%; text-align: left;">Assigned/Status</th>
                                </tr>
                            </thead>
                            <tbody id="tableTranslationBody">
                            </tbody>
                        </table>
                        <br>
                        <center><span style="font-weight: bold; font-size: 1.2vw;">Meeting List (<i
                                    class="fa fa-users"></i>)</span></center>
                        <table id="tableMeeting" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #90ed7d; color: black;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">ID</th>
                                    <th style="width: 1%; text-align: left;">Title</th>
                                    <th style="width: 0.3%; text-align: right;">Date</th>
                                    <th style="width: 0.3%; text-align: right;">From</th>
                                    <th style="width: 0.3%; text-align: right;">To</th>
                                    <th style="width: 0.1%; text-align: right;">Duration</th>
                                    <th style="width: 0.7%; text-align: left;">PIC</th>
                                </tr>
                            </thead>
                            <tbody id="tableMeetingBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalTranslation" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" style="width: 90%">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form role="form">
                            <input type="hidden" class="form-control" placeholder="Enter Title" id="translationID"
                                disabled>
                            <div class="col-lg-12">
                                <center>
                                    <table class="table table-bordered" style="width: 50%;">
                                        <tbody>
                                            <tr>
                                                <td
                                                    style="width: 30%; font-weight: bold; background-color: #605ca8; color: white;">
                                                    Translation ID</td>
                                                <td style="width: 70%;" id="info_0"></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="width: 30%; font-weight: bold; background-color: #605ca8; color: white;">
                                                    Requester</td>
                                                <td style="width: 70%;" id="info_1"></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="width: 30%; font-weight: bold; background-color: #605ca8; color: white;">
                                                    Department</td>
                                                <td style="width: 70%;" id="info_2"></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="width: 30%; font-weight: bold; background-color: #605ca8; color: white;">
                                                    Due Date</td>
                                                <td style="width: 70%;" id="info_3"></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="width: 30%; font-weight: bold; background-color: #605ca8; color: white;">
                                                    Total Pages</td>
                                                <td style="width: 70%;" id="info_4"></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="width: 30%; font-weight: bold; background-color: #605ca8; color: white;">
                                                    Estimated Time</td>
                                                <td style="width: 70%;" id="info_5"></td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="width: 30%; font-weight: bold; background-color: #605ca8; color: white;">
                                                    PIC</td>
                                                <td style="width: 70%;" id="info_6"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </center>
                                <table id="tableTranslationDetail" class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: #605ca8; color: white;">
                                        <tr>
                                            <th style="width: 5%; text-align: center;">#</th>
                                            <th style="width: 50%; text-align: center;">Attachment To Translate</th>
                                            <th style="width: 50%; text-align: center;">Translated Attachment</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableTranslationDetailBody">
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="translationText">Text To Translate<span
                                            class="text-red">*</span> :</label>
                                    <textarea class="form-control" rows="3" placeholder="Enter Translation" id="translationText"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="translationTextResult">Translated Text<span
                                            class="text-red">*</span> :</label>
                                    <textarea class="form-control" rows="3" placeholder="Enter Translation" id="translationTextResult"></textarea>
                                </div>
                            </div>
                        </form>
                        <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                            style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
                        <button class="btn btn-success pull-right"
                            style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                            onclick="createTranslation()">UPDATE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Create New Request<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Department<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" id="createDepartment"
                                            data-placeholder="Select Department" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($departments as $department)
                                                <option
                                                    value="{{ $department->department_name }}||{{ $department->department_shortname }}">
                                                    {{ $department->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Category<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" id="createCategoryDocument"
                                            data-placeholder="Select Category" style="width: 100%;">
                                            <option value=""></option>
                                            <option value="Biasa">Biasa (Hanya text, badan email, kalimat)</option>
                                            <option value="Khusus">Khusus (Mengandung tabel, grafik, memerlukan penataan)
                                            </option>
                                            <option value="Rahasia">Rahasia (Mengandung istilah sensitif / teknis)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Title<span class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Title"
                                            id="createTitle">
                                        <span style="padding-bottom: 0; font-size: 0.8vw; color: red;">Max 80 chars. Make
                                            it short!</span>
                                    </div>
                                </div>
                                @if (str_contains(Auth::user()->role_code, 'INT') || str_contains(Auth::user()->role_code, 'MIS'))
                                    <div class="form-group">
                                        <label style="padding-top: 0;" for=""
                                            class="col-sm-3 control-label">PIC<span class="text-red"></span> :</label>
                                        <div class="col-sm-5">
                                            <select class="form-control select2" id="createTranslationPIC"
                                                data-placeholder="Select PIC" style="width: 100%;">
                                                <option value=""></option>
                                                @foreach ($pics as $pic)
                                                    <option value="{{ $pic->employee_id }}||{{ $pic->employee_name }}">
                                                        {{ $pic->employee_id }} - {{ $pic->employee_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Number
                                        of Pages<span class="text-red">*</span> :</label>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <input type="text" value="0" class="numpad form-control"
                                                placeholder="Page(s)" id="createNumberPage">
                                            <div class="input-group-addon" style="">
                                                <i class="fa fa-files-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Due
                                        Date<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control datepicker" id="createRequestDate"
                                            placeholder="   Select Date">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Document
                                        to Translate<span class="text-red"></span> :</label>
                                    <div class="col-sm-5">
                                        <input type="file" id="createAttachment" name="createAttachment[]" multiple>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Text to
                                        Translate<span class="text-red"></span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" placeholder="Enter Text to Translate" id="createTranslationRequest"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Remark<span class="text-red"></span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="2" placeholder="Enter Remark" id="createRemark"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="createRequest()">CREATE</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMeeting" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #3c8dbc; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Add Meeting<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Meeting
                                        Title<span class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Title"
                                            id="meetingDocumentType">
                                        <span style="padding-bottom: 0; font-size: 0.8vw; color: red;">Max 40 chars. Make
                                            it short!</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">PIC<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <select class="form-control select2" id="meetingPIC"
                                            data-placeholder="Select PIC" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($pics as $pic)
                                                <option value="{{ $pic->employee_id }}||{{ $pic->employee_name }}">
                                                    {{ $pic->employee_id }} - {{ $pic->employee_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Department<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" id="meetingDepartment"
                                            data-placeholder="Select Department" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($departments as $department)
                                                <option
                                                    value="{{ $department->department_name }}||{{ $department->department_shortname }}">
                                                    {{ $department->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Due
                                        Date<span class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control datepicker" id="meetingRequestDate"
                                            placeholder="   Select Date">
                                    </div>
                                </div>
                                {{-- <div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Duration<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input type="text" value="0" class="numpad form-control" placeholder="Minute(s)" id="meetingNumberPage">
										<div class="input-group-addon" style="">
											<i class="fa fa-clock-o"></i> Min
										</div>
									</div>
								</div>
							</div> --}}
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Remark<span class="text-red"></span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="2" placeholder="Enter Remark" id="meetingRemark"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="createMeeting()">CREATE</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditMeeting" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #3c8dbc; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Edit Meeting<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">PIC<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <select class="form-control select2" id="editMeetingPIC"
                                            data-placeholder="Select PIC" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($pics as $pic)
                                                <option value="{{ $pic->employee_id }}||{{ $pic->employee_name }}">
                                                    {{ $pic->employee_id }} - {{ $pic->employee_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Meeting
                                        Title<span class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Title"
                                            id="editMeetingDocumentType">
                                        <span style="padding-bottom: 0; font-size: 1vw; color: red;">Max 40 chars. Make it
                                            short!</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Department<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" id="editMeetingDepartment"
                                            data-placeholder="Select Department" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($departments as $department)
                                                <option
                                                    value="{{ $department->department_name }}||{{ $department->department_shortname }}">
                                                    {{ $department->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Due
                                        Date<span class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control datepicker" id="editMeetingRequestDate"
                                            placeholder="   Select Date">
                                    </div>
                                </div>
                                {{-- 	<div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Duration<span class="text-red">*</span> :</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input type="text" value="0" class="numpad form-control" placeholder="Minute(s)" id="editMeetingNumberPage">
										<div class="input-group-addon" style="">
											<i class="fa fa-clock-o"></i> Min
										</div>
									</div>
								</div>
							</div> --}}
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Remark<span class="text-red"></span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="2" placeholder="Enter Remark" id="editMeetingRemark"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <button class="btn btn-danger" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
                            <button class="btn btn-success" style="font-weight: bold; font-size: 1.3vw; width: 63%;"
                                onclick="editMeeting()">UPDATE</button>
                            <button class="btn btn-danger"
                                style="font-weight: bold; font-size: 1.3vw; width: 5%; background-color: black; color: red;"
                                onclick="deleteTranslation()"><i class="fa fa-trash"></i></button>
                        </div>
                        <div class="col-md-12" style="padding-top: 15px;">
                            <center><span style="font-weight: bold; font-size: 1.2vw;">Update Logs</span></center>
                            <table id="tableLogMeeting" class="table table-bordered table-striped table-hover">
                                <thead style="background-color: #90ed7d; color: white;">
                                    <tr>
                                        <th style="width: 0.1%; text-align: center;">#</th>
                                        <th style="width: 0.1%; text-align: left;">Remark</th>
                                        <th style="width: 0.1%; text-align: left;">Status</th>
                                        <th style="width: 1%; text-align: left;">Updated By</th>
                                        <th style="width: 0.1%; text-align: right;">Updated At</th>
                                    </tr>
                                </thead>
                                <tbody id="tableLogMeetingBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditTranslation" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Edit Translation<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Status<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <select class="form-control select2" id="editTranslationStatus"
                                            data-placeholder="Select Status" style="width: 100%;">
                                            <option value="Waiting">Waiting</option>
                                            <option value="Assigned">Assigned</option>
                                            <option value="Finished">Finished</option>
                                            <option value="Rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Department<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" id="editTranslationDepartment"
                                            data-placeholder="Select Department" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($departments as $department)
                                                <option
                                                    value="{{ $department->department_name }}||{{ $department->department_shortname }}">
                                                    {{ $department->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Title<span class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Title"
                                            id="editTranslationTitle">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">PIC<span
                                            class="text-red"></span> :</label>
                                    <div class="col-sm-5">
                                        <select class="form-control select2" id="editTranslationPIC"
                                            data-placeholder="Select PIC" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($pics as $pic)
                                                <option value="{{ $pic->employee_id }}||{{ $pic->employee_name }}">
                                                    {{ $pic->employee_id }} - {{ $pic->employee_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Category<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" id="editTranslationDocumentType"
                                            data-placeholder="Select Category" style="width: 100%;">
                                            <option value=""></option>
                                            <option value="Biasa">Biasa (Hanya text, badan email, kalimat)</option>
                                            <option value="Khusus">Khusus (Mengandung tabel, grafik, memerlukan penataan)
                                            </option>
                                            <option value="Rahasia">Rahasia (Mengandung istilah sensitif / teknis)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Number
                                        of Pages<span class="text-red">*</span> :</label>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <input type="text" value="0" class="numpad form-control"
                                                placeholder="Page(s)" id="editTranslationNumberPage">
                                            <div class="input-group-addon" style="">
                                                <i class="fa fa-files-o"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Due
                                        Date<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control datepicker"
                                            id="editTranslationRequestDate" placeholder="   Select Date">
                                    </div>
                                </div>
                                {{-- <div class="form-group">
								<label style="padding-top: 0;" for="" class="col-sm-3 control-label">Document to Translate<span class="text-red"></span> :</label>
								<div class="col-sm-5">
									<input type="file" id="editTranslationAttachment" name="editTranslationAttachment[]" multiple>
								</div>
							</div> --}}
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Text to
                                        Translate<span class="text-red"></span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" placeholder="Enter Text to Translate"
                                            id="editTranslationTranslationRequest"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Remark<span class="text-red"></span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="2" placeholder="Enter Remark" id="editTranslationRemark"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <button class="btn btn-danger" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
                            <button class="btn btn-success" style="font-weight: bold; font-size: 1.3vw; width: 63%;"
                                onclick="editTranslation()">UPDATE</button>
                            <button class="btn btn-danger"
                                style="font-weight: bold; font-size: 1.3vw; width: 5%; background-color: black; color: red;"
                                onclick="deleteTranslation()"><i class="fa fa-trash"></i></button>
                        </div>
                        <br>
                        <br>
                        <div class="col-md-12" style="padding-top: 15px;">
                            <center><span style="font-weight: bold; font-size: 1.2vw;">Update Logs</span></center>
                            <table id="tableLogTranslation" class="table table-bordered table-striped table-hover">
                                <thead style="background-color: #605ca8; color: white;">
                                    <tr>
                                        <th style="width: 0.1%; text-align: center;">#</th>
                                        <th style="width: 0.1%; text-align: left;">Remark</th>
                                        <th style="width: 0.1%; text-align: left;">Status</th>
                                        <th style="width: 1%; text-align: left;">Updated By</th>
                                        <th style="width: 0.1%; text-align: right;">Updated At</th>
                                    </tr>
                                </thead>
                                <tbody id="tableLogTranslationBody">
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
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/exporting.js') }}"></script>
    <script src="{{ url('js/export-data.js') }}"></script>
    <script src="{{ url('js/jquery.numpad.js') }}"></script>
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script src="{{ url('bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%; z-index: 9999;"></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
        $.fn.numpad.defaults.buttonNumberTpl =
            '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl =
            '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function() {
            $(this).find('.done').addClass('btn-primary');
        };

        var role = '{{ Auth::user()->role_code }}';

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });
            $('#createRequestDate').datepicker({
                autoclose: true,
                timePicker24Hour: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            $('#meetingRequestDate').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 10,
                locale: {
                    format: 'YYYY-MM-DD H:mm'
                }
            });
            $('#editMeetingRequestDate').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                timePickerIncrement: 10,
                locale: {
                    format: 'YYYY-MM-DD H:mm'
                }
            });
            $('#editTranslationRequestDate').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            fetchRequest();
            fetchLoad();
        });

        $(function() {
            $('#editMeetingPIC').select2({
                dropdownParent: $('#modalEditMeeting'),
                minimumResultsForSearch: -1
            });
            $('#meetingPIC').select2({
                dropdownParent: $('#modalMeeting'),
                minimumResultsForSearch: -1
            });
            $('#editMeetingDepartment').select2({
                dropdownParent: $('#modalEditMeeting')
            });
            $('#meetingDepartment').select2({
                dropdownParent: $('#modalMeeting')
            });
            $('#createDepartment').select2({
                dropdownParent: $('#modalCreate')
            });
            $('#createCategoryDocument').select2({
                dropdownParent: $('#modalCreate'),
                minimumResultsForSearch: -1
            });
            $('#editTranslationStatus').select2({
                dropdownParent: $('#modalEditTranslation'),
                minimumResultsForSearch: -1
            });
            $('#editTranslationPIC').select2({
                dropdownParent: $('#modalEditTranslation'),
                minimumResultsForSearch: -1
            });
            $('#createTranslationPIC').select2({
                dropdownParent: $('#modalCreate'),
                minimumResultsForSearch: -1
            });
            $('#editTranslationDocumentType').select2({
                dropdownParent: $('#modalEditTranslation'),
                minimumResultsForSearch: -1
            });
        });
        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var translations = [];
        var translation_attachments = [];
        var translation_logs = [];
        var attachments = [];
        var departments = <?php echo json_encode($departments); ?>;
        var employee_sync = <?php echo json_encode($employee_sync); ?>;

        function modalCreate(category) {
            $('#createCategory').val(category);
            if (category == 'translation') {
                var dept = employee_sync.department_name + '||' + employee_sync.department_shortname;
                $('#createDepartment').val(dept).change();
                $('#createCategoryDocument').prop('selectedIndex', 0).change();
                $('#createTitle').val("");
                $('#createTranslationPIC').prop('selectedIndex', 0).change();
                $('#createNumberPage').val("");
                $('#createRequestDate').val("");
                $('#createTranslationRequest').html(CKEDITOR.instances.createTranslationRequest.setData(""));
                $('#createAttachment').val("");
                $('#createRemark').val("");
                $('#modalCreate').modal('show');
            }
            if (category == 'meeting') {
                $('#meetingNumberPage').val("");
                $('#meetingDocumentType').val("");
                $('#meetingPIC').val("{{ Auth::user()->username }}||{{ Auth::user()->name }}").change();
                $('#meetingRequestDate').val("");
                $('#meetingRemark').val("");
                $('#meetingDepartment').prop('selectedIndex', 0).change();
                $('#modalMeeting').modal('show');
            }
        }

        function createTranslation() {
            if (confirm("Are you sure want to finish this request?")) {
                $('#loading').show();
                var translation_id = $('#translationID').val();
                var translation_request = CKEDITOR.instances.translationText.getData();
                var translation_result = CKEDITOR.instances.translationTextResult.getData();
                var status = true;

                var formData = new FormData();
                formData.append('translation_id', translation_id);
                formData.append('translation_request', translation_request);
                formData.append('translation_result', translation_result);
                formData.append('attachments', attachments);

                $.each(attachments, function(key, value) {
                    if ($('#translationAttachment_' + value).val() == "") {
                        status = false;
                    }

                    var attachment = $('#translationAttachment_' + value).prop('files')[0];
                    var file = $('#translationAttachment_' + value).val().replace(/C:\\fakepath\\/i, '').split(".");
                    formData.append('attachment_' + value, attachment);
                    formData.append('extension_' + value, file[file.length - 1]);
                    formData.append('file_name_' + value, file[0]);
                });

                if (status == false) {
                    $('#loading').hide();
                    openErrorGritter('Error!', 'All translation result must be uploaded');
                    audio_error.play();
                    return false;
                }


                $.ajax({
                    url: "{{ url('input/translation_result') }}",
                    method: "POST",
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        if (data.status) {
                            fetchRequest();
                            fetchLoad();
                            $('#modalTranslation').modal('hide');
                            $('#loading').hide();
                            openSuccessGritter('Success!', data.message);
                            audio_ok.play();
                        } else {
                            $('#loading').hide();
                            openErrorGritter('Error!', data.message);
                            audio_error.play();
                        }

                    }
                });
            } else {
                return false;
            }
        }

        function modalTranslation(translation_id) {
            var tableTranslationDetailBody = "";
            $('#tableTranslationDetailBody').html("");
            attachments = [];
            var cnt = 0;

            $.each(translation_attachments, function(key, value) {
                if (translation_id == value.translation_id) {
                    cnt += 1;
                    tableTranslationDetailBody += '<tr>';
                    tableTranslationDetailBody += '<td style="widht: 1%; text-align: center; height: 40px;">' +
                        cnt + '</td>';
                    tableTranslationDetailBody +=
                        '<td style="widht: 50%; text-align: center; height: 40px;"><a href="{{ asset('files/translation') }}/' +
                        value.file_name + '">' + value.file_name + '</a></td>';
                    if (value.file_name_result == "") {
                        attachments.push(value.id);
                        tableTranslationDetailBody +=
                            '<td style="widht: 50%; text-align: center; height: 40px;"><center><input type="file" id="translationAttachment_' +
                            value.id + '"></center></td>'
                    } else {
                        tableTranslationDetailBody +=
                            '<td style="widht: 50%; text-align: center; height: 40px;"><a href="{{ asset('files/translation') }}/' +
                            value.file_name_result + '">' + value.file_name_result + '</a></td>';
                    }
                    tableTranslationDetailBody += '</tr>';
                }
            });
            $('#tableTranslationDetailBody').append(tableTranslationDetailBody);

            $.each(translations, function(key, value) {
                if (translation_id == value.translation_id) {
                    $('#translationText').html(CKEDITOR.instances.translationText.setData(value
                        .translation_request));
                    $('#translationTextResult').html(CKEDITOR.instances.translationTextResult.setData(value
                        .translation_result));
                    $('#info_0').text(value.translation_id);
                    $('#info_1').text(value.requester_id + ' - ' + value.requester_name);
                    $('#info_2').text(value.department_name);
                    $('#info_3').text(value.request_date);
                    $('#info_4').text(value.number_page + ' page(s)');
                    $('#info_5').text(value.load_time + ' Minute(s)');
                    $('#info_6').text(value.pic_id + ' - ' + value.pic_name);
                }
            });
            $('#translationID').val(translation_id);
            $('#modalTranslation').modal('show');
        }

        function fetchLoad() {
            var data = {

            }
            $.get('{{ url('fetch/translation_load') }}', data, function(result, status, xhr) {
                if (result.status) {
                    var xCategories = [];
                    var series_meeting = [];
                    var series_translation = [];
                    var translationPics = "";
                    $('#translationPics').html("");

                    translationPics += '<div class="row">';
                    $.each(result.loads, function(key, value) {
                        xCategories.push(value.pic_name);
                        series_meeting.push(value.load_time_meeting);
                        series_translation.push(value.load_time_translation);
                        translationPics += '<div class="col-lg-6">';
                        translationPics +=
                            '<div class="box box-widget widget-user-2" style="border: 1px solid black;">';
                        translationPics +=
                            '<div class="widget-user-header bg-purple" style="height: 120px;">';
                        translationPics += '<div class="widget-user-image crop2">';
                        translationPics += '<img src="{{ url('images/avatar/') }}' + '/' + value.pic_id +
                            '.jpg' + '" alt="">';
                        translationPics +=
                            '<h3 class="widget-user-username" style="font-size: 1.1vw; font-weight: bold;">' +
                            value.pic_name + '</h3>';
                        translationPics += '<h5 class="widget-user-desc" style="font-size: 1vw;">' + value
                            .pic_id + '</h5>';
                        translationPics += '</div>';
                        translationPics += '</div>';
                        translationPics += '<div class="box-footer no-padding">';
                        translationPics += '<ul class="nav nav-stacked" id="pic_' + value.pic_id + '">';
                        translationPics += '</ul>';
                        translationPics += '</div>';
                        translationPics += '</div>';
                        translationPics += '</div>';
                    });
                    translationPics += '</div>';

                    $('#translationPics').append(translationPics);

                    $.each(translations, function(key, value) {
                        var stacked = "";
                        if (value.category == 'translation' && value.status == 'Assigned') {
                            stacked += '<li>';
                            stacked += '<table class="table-pic" style="width: 100%;">';
                            stacked += '<tbody>';
                            stacked += '<tr onclick="modalEdit(\'' + value.category + '\',\'' + value
                                .translation_id + '\')">';
                            stacked +=
                                '<td style="width: 20%; font-weight: normal; text-align: center;">あ <i class="fa fa-exchange"></i> A</td>';
                            stacked += '<td style="width: 25%; font-weight: bold;">' + value
                                .translation_id + '</td>';
                            stacked += '<td style="width: 25%; font-weight: bold; text-align: right;">' +
                                value.request_date + '</td>';
                            stacked += '<td style="width: 25%; font-weight: bold; text-align: right;">' +
                                value.load_time + ' Min</td>';
                            stacked += '</tr>';
                            stacked += '</tbody>';
                            stacked += '</table>';
                            stacked += '</li>';
                        }
                        if (value.category == 'translation' && value.status == 'Finished' && value
                            .finished_at == '{{ date('Y-m-d') }}') {
                            stacked += '<li style="background-color: RGB(204,255,255);">';
                            stacked += '<table class="table-pic" style="width: 100%;">';
                            stacked += '<tbody>';
                            stacked += '<tr onclick="modalEdit(\'' + value.category + '\',\'' + value
                                .translation_id + '\')">';
                            stacked +=
                                '<td style="width: 20%; font-weight: normal; text-align: center;">あ <i class="fa fa-exchange"></i> A</td>';
                            stacked += '<td style="width: 25%; font-weight: bold;">' + value
                                .translation_id + '</td>';
                            stacked += '<td style="width: 25%; font-weight: bold; text-align: right;">' +
                                value.request_date + '</td>';
                            stacked += '<td style="width: 25%; font-weight: bold; text-align: right;">' +
                                value.load_time + ' Min</td>';
                            stacked += '</tr>';
                            stacked += '</tbody>';
                            stacked += '</table>';
                            stacked += '</li>';
                        }
                        if (value.category == 'meeting' && value.request_date == '{{ date('Y-m-d') }}') {
                            stacked += '<li>';
                            stacked += '<table class="table-pic" style="width: 100%;">';
                            stacked += '<tbody>';
                            stacked += '<tr onclick="modalEdit(\'' + value.category + '\',\'' + value
                                .translation_id + '\')">';
                            stacked +=
                                '<td style="width: 20%; font-weight: normal; text-align: center;"><i class="fa fa-users"></i></td>';
                            stacked += '<td style="width: 25%; font-weight: bold;">' + value
                                .translation_id + '</td>';
                            stacked += '<td style="width: 25%; font-weight: bold; text-align: right;">' +
                                value.request_date + '</td>';
                            stacked += '<td style="width: 25%; font-weight: bold; text-align: right;">' +
                                value.load_time + ' Min</td>';
                            stacked += '</tr>';
                            stacked += '</tbody>';
                            stacked += '</table>';
                            stacked += '</li>';
                        }
                        $('#pic_' + value.pic_id).append(stacked);
                    });

                    Highcharts.chart('container1', {
                        chart: {
                            backgroundColor: null,
                            type: 'column'
                        },
                        title: {
                            text: '<b>Interpreter Workload</b>'
                        },
                        xAxis: {
                            categories: xCategories
                        },
                        credits: {
                            enabled: false
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Minute(s)'
                            },
                            stackLabels: {
                                enabled: true
                            }
                        },
                        legend: {
                            align: 'right',
                            x: -30,
                            verticalAlign: 'top',
                            y: 25,
                            floating: true,
                            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || null,
                            borderColor: null,
                            borderWidth: 1,
                            shadow: false
                        },
                        tooltip: {
                            headerFormat: '<b>{point.x}</b><br/>',
                            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: [{
                            name: 'Meeting',
                            data: series_meeting,
                            color: '#90ed7d'
                        }, {
                            name: 'Translation',
                            data: series_translation,
                            color: '#605ca8'
                        }]
                    });
                } else {
                    alert('Attempt to retrieve data failed');
                }
            });

        }

        function modalEdit(category, translation_id) {
            $('#createCategory').val(category);
            $('#editID').val(translation_id);
            if (category == 'meeting') {
                $('#modalEditMeeting').modal('show');
                $.each(translations, function(key, value) {
                    if (value.translation_id == translation_id) {
                        $('#editMeetingPIC').val(value.pic_id + '||' + value.pic_name).change();
                        $('#editMeetingDocumentType').val(value.document_type);
                        // $('#editMeetingNumberPage').val(value.load_time);
                        $('#editMeetingRequestDate').val("");
                        $('#editMeetingRequestDate').daterangepicker({
                            timePicker: true,
                            timePicker24Hour: true,
                            timePickerIncrement: 10,
                            locale: {
                                format: 'YYYY-MM-DD H:mm'
                            },
                            startDate: value.request_date_from,
                            endDate: value.request_date_to
                        });
                        $('#editMeetingRemark').val(value.remark);
                        $('#editMeetingDepartment').val(value.department_name + '||' + value.department_shortname)
                            .change();
                    }
                });
                var tableLogMeetingBody = "";
                $('#tableLogMeetingBody').html("");
                var cnt = 0;
                $.each(translation_logs, function(key, value) {
                    if (value.translation_id == translation_id) {
                        cnt += 1;
                        tableLogMeetingBody += '<tr>';
                        tableLogMeetingBody += '<td style="text-align: center;">' + cnt + '</td>';
                        tableLogMeetingBody += '<td>' + value.remark + '</td>';
                        tableLogMeetingBody += '<td>' + value.status + '</td>';
                        tableLogMeetingBody += '<td>' + value.updated_by + '<br>' + value.updated_by_name + '</td>';
                        tableLogMeetingBody += '<td style="text-align: right;">' + value.created_at + '</td>';
                        tableLogMeetingBody += '</tr>';
                    }
                });
                $('#tableLogMeetingBody').append(tableLogMeetingBody);
            }
            if (category == 'translation') {
                $.each(translations, function(key, value) {
                    if (value.translation_id == translation_id) {
                        $('#editTranslationStatus').val(value.status).change();
                        $('#editTranslationTitle').val(value.title);
                        $('#editTranslationDepartment').val(value.department_name + '||' + value
                            .department_shortname).change();
                        $('#editTranslationPIC').val(value.pic_id + '||' + value.pic_name).change();
                        $('#editTranslationDocumentType').val(value.document_type).change();
                        $('#editTranslationNumberPage').val(value.number_page);
                        // $('#editTranslationRequestDate').val(value.request_date);
                        $('#editTranslationRequestDate').datepicker('setDate', value.request_date);
                        $('#editTranslationTranslationRequest').html(CKEDITOR.instances
                            .editTranslationTranslationRequest.setData(value.translation_request));
                        $('#editTranslationRemark').val(value.remark);
                    }
                });

                var tableLogTranslationBody = "";
                $('#tableLogTranslationBody').html("");
                var cnt = 0;
                $.each(translation_logs, function(key, value) {
                    if (value.translation_id == translation_id) {
                        cnt += 1;
                        tableLogTranslationBody += '<tr>';
                        tableLogTranslationBody += '<td style="text-align: center;">' + cnt + '</td>';
                        tableLogTranslationBody += '<td>' + value.remark + '</td>';
                        tableLogTranslationBody += '<td>' + value.status + '</td>';
                        tableLogTranslationBody += '<td>' + value.updated_by + '<br>' + value.updated_by_name +
                            '</td>';
                        tableLogTranslationBody += '<td style="text-align: right;">' + value.created_at + '</td>';
                        tableLogTranslationBody += '</tr>';
                    }
                });
                $('#tableLogTranslationBody').append(tableLogTranslationBody);
                $('#modalEditTranslation').modal('show');
            }
        }

        function deleteTranslation() {
            if (confirm("Are you sure want to delete this translation request?")) {
                $('#loading').show();
                var translation_id = $('#editID').val();

                var data = {
                    translation_id: translation_id,
                }

                $.post('{{ url('delete/translation') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchRequest();
                        fetchLoad();
                        $('#modalEditTranslation').modal('hide');
                        $('#modalEditMeeting').modal('hide');
                        $('#loading').hide();
                        openSuccessGritter('Success!', result.message);
                        audio_ok.play();
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

        function editTranslation() {

            if (confirm("Are you sure want to edit this translation request?")) {
                $('#loading').show();
                var translation_id = $('#editID').val();
                var category = $('#createCategory').val();
                var title = $('#editTranslationTitle').val();
                var department = $('#editTranslationDepartment').val().split('||');
                var department_name = department[0];
                var department_shortname = department[1];
                var status = $('#editTranslationStatus').val();
                var pic = $('#editTranslationPIC').val().split('||');
                var pic_id = pic[0];
                var pic_name = pic[1];
                var document_type = $('#editTranslationDocumentType').val();
                var number_page = $('#editTranslationNumberPage').val();
                var request_date = $('#editTranslationRequestDate').val();
                var translation_request = CKEDITOR.instances.editTranslationTranslationRequest.getData();
                var remark = $('#editTranslationRemark').val();
                if (remark == "") {
                    remark = null;
                }

                if (title.length > 40) {
                    $('#loading').hide();
                    openErrorGritter('Error!', "Title max 40 chars. Make it short!");
                    audio_error.play();
                    return false;
                }

                if (status == "" || title == "" || department_name == "" || number_page <= 0 || request_date == "" ||
                    document_type == "") {
                    $('#loading').hide();
                    openErrorGritter('Error!', "All field with red star must be filled.");
                    audio_error.play();
                    return false;
                }

                var stt = true;

                $.each(translations, function(key, value) {
                    if (value.translation_id == translation_id) {
                        if (
                            value.status == status &&
                            value.document_type == document_type &&
                            value.title == title &&
                            value.department_name == department_name &&
                            value.number_page == number_page &&
                            value.request_date == request_date &&
                            value.department_shortname == department_shortname &&
                            value.pic_id == pic_id &&
                            value.remark == remark) {
                            stt = false;
                        }
                    }
                });

                if (stt == false) {
                    $('#loading').hide();
                    openErrorGritter('Error!', "There is no change were made.");
                    audio_error.play();
                    return false;
                }

                var data = {
                    translation_id: translation_id,
                    category: category,
                    title: title,
                    status: status,
                    department_name: department_name,
                    department_shortname: department_shortname,
                    document_type: document_type,
                    number_page: number_page,
                    request_date: request_date,
                    department_name: department_name,
                    department_shortname: department_shortname,
                    pic_id: pic_id,
                    pic_name: pic_name,
                    remark: remark
                }
                $.post('{{ url('edit/translation') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchRequest();
                        fetchLoad();
                        $('#modalEditTranslation').modal('hide');
                        $('#loading').hide();
                        openSuccessGritter('Success!', result.message);
                        audio_ok.play();
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

        function editMeeting() {
            if (confirm("Are you sure want to add this meeting?")) {
                $('#loading').show();
                var translation_id = $('#editID').val();
                var category = $('#createCategory').val();
                var pic = $('#editMeetingPIC').val().split('||');
                var document_type = $('#editMeetingDocumentType').val();
                // var number_page = $('#editMeetingNumberPage').val();
                var request_date = $('#editMeetingRequestDate').val();
                var remark = $('#editMeetingRemark').val();
                if (remark == "") {
                    remark = null;
                }
                var department = $('#editMeetingDepartment').val().split('||');
                var department_name = department[0];
                var department_shortname = department[1];
                var pic_id = pic[0];
                var pic_name = pic[1];

                if (document_type.length > 40) {
                    $('#loading').hide();
                    openErrorGritter('Error!', "Title max 40 chars. Make it short!");
                    audio_error.play();
                    return false;
                }

                if (category == "" || request_date == "" || department == "" || pic == "") {
                    $('#loading').hide();
                    openErrorGritter('Error!', "All field with red star must be filled.");
                    audio_error.play();
                    return false;
                }

                var status = true;

                $.each(translations, function(key, value) {
                    if (value.translation_id == translation_id) {
                        if (
                            value.document_type == document_type &&
                            value.department_shortname == department_shortname &&
                            value.pic_id == pic_id &&
                            value.request_date_from + ' - ' + value.request_date_to == request_date &&
                            value.remark == remark) {
                            status = false;
                        }
                    }
                });

                if (status == false) {
                    $('#loading').hide();
                    openErrorGritter('Error!', "There is no change were made.");
                    audio_error.play();
                    return false;
                }

                var data = {
                    translation_id: translation_id,
                    category: category,
                    document_type: document_type,
                    // number_page:number_page,
                    request_date: request_date,
                    department_name: department_name,
                    department_shortname: department_shortname,
                    pic_id: pic_id,
                    pic_name: pic_name,
                    remark: remark
                }
                $.post('{{ url('edit/translation_meeting') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchRequest();
                        fetchLoad();
                        $('#modalEditMeeting').modal('hide');
                        $('#loading').hide();
                        openSuccessGritter('Success!', result.message);
                        audio_ok.play();
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

        function fetchRequest() {
            var mon = "";
            var data = {
                mon: mon
            }
            $.get('{{ url('fetch/translation') }}', data, function(result, status, xhr) {
                if (result.status) {
                    var tableTranslationBody = "";
                    var tableMeetingBody = "";
                    var xCategories = [];
                    var series_waiting = [];
                    var series_assigned = [];

                    $('#tableTranslationBody').html("");
                    $('#tableTranslation').DataTable().clear();
                    $('#tableTranslation').DataTable().destroy();
                    $('#tableMeetingBody').html("");
                    $('#tableMeeting').DataTable().clear();
                    $('#tableMeeting').DataTable().destroy();
                    translations = result.translations;
                    translation_attachments = result.translation_attachments;
                    translation_logs = result.translation_logs;

                    var department = "";

                    if ('{{ isset($user->employee_sync->department) }}' == '1') {
                        department = '{{ $user->employee_sync->department }}';
                    }

                    var color = "";
                    $.each(result.translations, function(key, value) {
                        if (value.status == 'Waiting') {
                            color = "background-color: #e53935;";
                        } else if (value.status == 'Assigned') {
                            color = "background-color: orange;";
                        } else {
                            color = "background-color: RGB(204,255,255);";
                        }
                        if (value.category == 'translation') {
                            if (~role.indexOf("INT")) {
                                tableTranslationBody += '<tr>';
                                tableTranslationBody +=
                                    '<td style="width: 0.5%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'translation\',\'' +
                                    value.translation_id + '\')">' + value.translation_id + '<br>' + value
                                    .title + '</a></td>';
                                tableTranslationBody += '<td style="width: 1%; text-align: left;">' + value
                                    .requester_name + '<br>' + value.department_name + '</td>';
                                tableTranslationBody += '<td style="width: 0.1%; text-align: right;">' +
                                    value.request_date + '</td>';
                                tableTranslationBody += '<td style="width: 0.1%; text-align: right;">' +
                                    value.number_page + ' Page(s)</td>';
                                tableTranslationBody +=
                                    '<td style="width: 0.1%; text-align: center;"><a href="javascript:void(0)" onclick="modalTranslation(\'' +
                                    value.translation_id +
                                    '\')">あ <i class="fa fa-exchange"></i> A</a></td>';
                                if (value.pic_name != null) {
                                    var pic_name = value.pic_name.split(' ').slice(0, 2).join(' ');
                                } else {
                                    var pic_name = '';
                                }
                                tableTranslationBody +=
                                    '<td style="cursor: pointer; width: 0.7%; text-align: left; ' + color +
                                    '"><a style="color: black;" href="{{ url('approval/translation') }}?translation_id=' +
                                    value.translation_id +
                                    '&status=Approved"><div style="height:100%;width:100%">(' + value
                                    .status + ') ' + pic_name + '<br>' + value.updated_at +
                                    '</div></a></td>';
                                tableTranslationBody += '</tr>';
                            } else {
                                if (value.department_name == department || value.requester_id ==
                                    '{{ $user->username }}') {
                                    tableTranslationBody += '<tr>';
                                    tableTranslationBody +=
                                        '<td style="width: 0.5%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'translation\',\'' +
                                        value.translation_id + '\')">' + value.translation_id + '<br>' +
                                        value.title + '</a></td>';
                                    tableTranslationBody += '<td style="width: 1%; text-align: left;">' +
                                        value.requester_name + '<br>' + value.department_name + '</td>';
                                    tableTranslationBody += '<td style="width: 0.1%; text-align: right;">' +
                                        value.request_date + '</td>';
                                    tableTranslationBody += '<td style="width: 0.1%; text-align: right;">' +
                                        value.number_page + ' Page(s)</td>';
                                    tableTranslationBody +=
                                        '<td style="width: 0.1%; text-align: center;"><a href="javascript:void(0)" onclick="modalTranslation(\'' +
                                        value.translation_id +
                                        '\')">あ <i class="fa fa-exchange"></i> A</a></td>';
                                    if (value.pic_name != null) {
                                        var pic_name = value.pic_name.split(' ').slice(0, 2).join(' ');
                                    } else {
                                        var pic_name = '';
                                    }
                                    tableTranslationBody +=
                                        '<td style="cursor: pointer; width: 0.7%; text-align: left; ' +
                                        color +
                                        '"><a style="color: black;" href="{{ url('approval/translation') }}?translation_id=' +
                                        value.translation_id +
                                        '&status=Approved"><div style="height:100%;width:100%">(' + value
                                        .status + ') ' + pic_name + '<br>' + value.updated_at +
                                        '</div></a></td>';
                                    tableTranslationBody += '</tr>';
                                }
                            }
                        }
                        if (value.category == 'meeting') {
                            tableMeetingBody += '<tr>';
                            tableMeetingBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'meeting\',\'' +
                                value.translation_id + '\')">' + value.translation_id + '</a></td>';
                            tableMeetingBody += '<td style="width: 1%; text-align: left;">' + value
                                .document_type + '</td>';
                            tableMeetingBody += '<td style="width: 0.1%; text-align: right;">' + value
                                .request_date + '</td>';
                            tableMeetingBody += '<td style="width: 0.1%; text-align: right;">' + value
                                .request_time_from + '</td>';
                            tableMeetingBody += '<td style="width: 0.1%; text-align: right;">' + value
                                .request_time_to + '</td>';
                            tableMeetingBody += '<td style="width: 0.1%; text-align: right;">' + value
                                .load_time + ' Minute(s)</td>';
                            if (value.pic_name != null) {
                                var pic_name = value.pic_name.split(' ').slice(0, 2).join(' ');
                            } else {
                                var pic_name = '';
                            }
                            tableMeetingBody +=
                                '<td style="cursor: pointer; width: 0.7%; text-align: left;">' + pic_name +
                                '<br>' + value.updated_at + '</td>';
                            tableMeetingBody += '</tr>';
                        }
                    });

                    $('#tableMeetingBody').append(tableMeetingBody);
                    $('#tableTranslationBody').append(tableTranslationBody);
                    $('#tableTranslation').DataTable({
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

                    $('#tableMeeting').DataTable({
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

                    var array = result.translations;
                    var result = [];
                    array.reduce(function(res, value) {
                        if (!res[value.department_shortname]) {
                            res[value.department_shortname] = {
                                department_shortname: value.department_shortname,
                                waiting: 0,
                                assigned: 0
                            };
                            result.push(res[value.department_shortname])
                        }
                        if (value.category == 'translation') {
                            if (value.status == 'Waiting') {
                                res[value.department_shortname].waiting += 1;
                            }
                            if (value.status == 'Assigned') {
                                res[value.department_shortname].assigned += 1;
                            }
                        }
                        return res;
                    }, {});

                    $.each(departments, function(key, value) {
                        xCategories.push(value.department_shortname);
                        var waiting = 0;
                        var assigned = 0;
                        for ($i = 0; $i < result.length; $i++) {
                            if (value.department_shortname == result[$i].department_shortname) {
                                waiting = result[$i].waiting;
                                assigned = result[$i].assigned;
                            }
                        }
                        series_waiting.push(waiting);
                        series_assigned.push(assigned);
                    });

                    Highcharts.chart('container2', {
                        chart: {
                            backgroundColor: null,
                            type: 'column'
                        },
                        title: {
                            text: '<b>Outstanding Translation</b>'
                        },
                        xAxis: {
                            categories: xCategories
                        },
                        credits: {
                            enabled: false
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Total Request'
                            },
                            stackLabels: {
                                enabled: true
                            }
                        },
                        legend: {
                            align: 'right',
                            x: -30,
                            verticalAlign: 'top',
                            y: 25,
                            floating: true,
                            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || null,
                            borderColor: null,
                            borderWidth: 1,
                            shadow: false
                        },
                        tooltip: {
                            headerFormat: '<b>{point.x}</b><br/>',
                            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: [{
                            name: 'Waiting',
                            data: series_waiting,
                            color: '#e53935'
                        }, {
                            name: 'Assigned',
                            data: series_assigned,
                            color: 'orange'
                        }]
                    });
                } else {
                    alert('Attempt to retrieve data failed');
                }
            });
        }

        function createMeeting() {
            if (confirm("Are you sure want to add this meeting?")) {
                $('#loading').show();
                var category = $('#createCategory').val();
                var pic = $('#meetingPIC').val().split('||');
                var document_type = $('#meetingDocumentType').val();
                // var number_page = $('#meetingNumberPage').val();
                var request_date = $('#meetingRequestDate').val();
                var remark = $('#meetingRemark').val();
                var department = $('#meetingDepartment').val().split('||');
                var department_name = department[0];
                var department_shortname = department[1];
                var pic_id = pic[0];
                var pic_name = pic[1];

                if (document_type.length > 40) {
                    $('#loading').hide();
                    openErrorGritter('Error!', "Title max 40 chars. Make it short!");
                    audio_error.play();
                    return false;
                }

                if (category == "" || request_date == "" || department == "" || pic == "") {
                    $('#loading').hide();
                    openErrorGritter('Error!', "All field with red star must be filled.");
                    audio_error.play();
                    return false;
                }

                var data = {
                    category: category,
                    document_type: document_type,
                    // number_page:number_page,
                    request_date: request_date,
                    department_name: department_name,
                    department_shortname: department_shortname,
                    pic_id: pic_id,
                    pic_name: pic_name,
                    remark: remark
                }
                $.post('{{ url('input/translation_meeting') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchRequest();
                        fetchLoad();
                        $('#modalMeeting').modal('hide');
                        $('#loading').hide();
                        openSuccessGritter('Success!', result.message);
                        audio_ok.play();
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

        function createRequest() {
            if (confirm("Are you sure want to create this request?")) {
                $('#loading').show();
                var department = $('#createDepartment').val().split('||');
                var department_name = department[0];
                var department_shortname = department[1];
                var category = $('#createCategory').val();
                var title = $('#createTitle').val();
                var pic_id = "";
                var pic_name = "";
                if ($('#createTranslationPIC').length > 0) {
                    var pic = $('#createTranslationPIC').val().split('||');
                    var pic_id = pic[0];
                    var pic_name = pic[1];
                }
                var document_type = $('#createCategoryDocument').val();
                var number_page = $('#createNumberPage').val();
                var request_date = $('#createRequestDate').val();
                var translation_request = CKEDITOR.instances.createTranslationRequest.getData();
                var remark = $('#createRemark').val();

                if (title.length > 80) {
                    $('#loading').hide();
                    openErrorGritter('Error!', "Title max 80 chars. Make it short!");
                    audio_error.play();
                    return false;
                }

                if (category == "" || title == "" || document_type == "" || number_page == "" || number_page == 0) {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!', 'All field with red star must be filled.');
                    return false;
                }

                if (translation_request == "" && $('#createAttachment').val() == "") {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!', 'There must be document or text to translate.');
                    return false;
                }

                var formData = new FormData();
                formData.append('category', category);
                formData.append('title', title);
                formData.append('pic_id', pic_id);
                formData.append('pic_name', pic_name);
                formData.append('department_name', department_name);
                formData.append('department_shortname', department_shortname);
                formData.append('document_type', document_type);
                formData.append('number_page', number_page);
                formData.append('request_date', request_date);
                formData.append('translation_request', translation_request);
                formData.append('remark', remark);

                var cnt = 0;

                $.each($("#createAttachment")[0].files, function(i, file) {
                    cnt += 1;
                    formData.append('attachment_' + i, file);
                    var f = file['name'].replace(/C:\\fakepath\\/i, '').split(".");
                    formData.append('extension_' + i, f[1]);
                    formData.append('file_name_' + i, f[0]);
                });
                formData.append('count_attachment', cnt);

                $.ajax({
                    url: "{{ url('input/translation') }}",
                    method: "POST",
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        if (data.status) {
                            fetchRequest();
                            fetchLoad();
                            $('#modalCreate').modal('hide');
                            $('#loading').hide();
                            openSuccessGritter('Success!', data.message);
                            audio_ok.play();
                        } else {
                            $('#loading').hide();
                            openErrorGritter('Error!', data.message);
                            audio_error.play();
                        }

                    }
                });
            } else {
                return false;
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

        CKEDITOR.replace('createTranslationRequest', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}'
        });

        CKEDITOR.replace('translationText', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}'
        });

        CKEDITOR.replace('translationTextResult', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}'
        });

        CKEDITOR.replace('editTranslationTranslationRequest', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}'
        });
    </script>
@endsection
