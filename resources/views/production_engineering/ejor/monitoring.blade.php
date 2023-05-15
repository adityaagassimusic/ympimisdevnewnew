@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <style type="text/css">
        .table-pic tbody>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c;
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

        .crop2 {
            overflow: hidden;
        }

        .crop2 img {
            height: 70px;
            margin: -5% 0 0 0 !important;
        }

        .nav-tabs-custom>ul.nav.nav-tabs {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .nav-tabs-custom>ul.nav.nav-tabs>li {
            float: none;
            display: table-cell;
        }

        .nav-tabs-custom>ul.nav.nav-tabs>li>a {
            text-align: center;
        }

        #loading {
            display: none;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        @foreach (Auth::user()->role->permissions as $perm)
            @php
                $navs[] = $perm->navigation_code;
            @endphp
        @endforeach
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
            <a href="{{ url('/index/ejor') }}" class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;"><i
                    class="fa fa-pencil-square-o"></i> Buat EJOR</a>
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
            <div class="col-xs-2" id="ticket_pics" style="padding-right: 7px;">

            </div>
            <div class="col-xs-10">
                <div class="row">
                    <div class="col-xs-12" style="padding-left: 0px;">
                        <div class="col-xs-12" style="padding-right: 0; padding-left: 0">
                            <div id="chartOutstanding"
                                style="width: 100%; height: 40vh; margin-bottom: 10px; border: 1px solid black;"></div>
                        </div>
                        <!-- <div class="col-xs-2" style="padding-right: 0px;">
                                  <div class="small-box" style="margin-bottom: 5px; height: 13vh; background-color: #ffeb3b; border: 1px solid black;">
                                   <div class="inner">
                                    <h3 id="totalWaiting" style="font-size: 2vw;">0</h3>
                                    <p style="font-weight: bold; font-size: 1.2vw;">Waiting</p>
                                   </div>
                                   <div class="icon">
                                    <i class="ion ion-android-alarm-clock" style="font-size: 4.5vw;"></i>
                                   </div>
                                  </div>
                                  <div class="small-box" style="margin-bottom: 5px; height: 13vh; background-color: #71b1e5; border: 1px solid black;">
                                   <div class="inner">
                                    <h3 id="totalProgress" style="font-size: 2vw;">0</h3>
                                    <p style="font-weight: bold; font-size: 1.2vw;">InProgress</p>
                                   </div>
                                   <div class="icon">
                                    <i class="ion ion-android-settings" style="font-size: 4.5vw;"></i>
                                   </div>
                                  </div>
                                  <div class="small-box" style="margin-bottom: 5px; height: 13vh; background-color: #aee571; border: 1px solid black;">
                                   <div class="inner">
                                    <h3 id="totalFinish" style="font-size: 2vw;">0</h3>
                                    <p style="font-weight: bold; font-size: 1.2vw;">Finished</p>
                                   </div>
                                   <div class="icon">
                                    <i class="ion ion-android-star-outline" style="font-size: 4.5vw;"></i>
                                   </div>
                                  </div>
                                 </div> -->
                        <table id="ticketTable" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #605ca8; color: white;">
                                <tr>
                                    <th style="width: 0.1%;" rowspan="2">ID</th>
                                    <th style="width: 0.1%;" rowspan="2">Dept.</th>
                                    <th style="width: 10%;" rowspan="2">Title</th>
                                    <th style="width: 0.1%;" rowspan="2">Priority</th>
                                    <th style="width: 9%; text-align: center;" colspan="4">Approval</th>
                                    <th style="width: 0.1%;" rowspan="2">Status</th>
                                    <th style="width: 0.1%;" rowspan="2">PIC</th>
                                    <th style="width: 0.1%;" rowspan="2">Report</th>
                                    <!-- <th style="width: 0.1%;" rowspan="2">Progress</th> -->
                                </tr>
                                <tr>
                                    <th style="width: 3%;">Chief/Foreman</th>
                                    <th style="width: 3%;">Manager</th>
                                    <th style="width: 3%;">Manager PE</th>
                                    <th style="width: 3%;">Chief PE</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th style="text-align:center"></th>
                                    <th style="text-align:center"></th>
                                    <th style="text-align:center"></th>
                                    <th style="text-align:center"></th>
                                    <th style="text-align:center"></th>
                                    <th style="text-align:center"></th>
                                    <th style="text-align:center"></th>
                                    <th style="text-align:center"></th>
                                    <th style="text-align:center"></th>
                                    <th style="text-align:center"></th>
                                    <th style="text-align:center"></th>
                                </tr>
                            </tfoot>
                            <tbody id="ticketTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetail" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            DETAIL EJOR<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Form Number<span
                                            class="text-red">*</span> :</label>

                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="detailFormId" id="detailFormId"
                                            readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Section<span
                                            class="text-red">*</span> :</label>

                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" name="detailSection"
                                            id="detailSection" readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Title<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Title"
                                            id="detailTitle" readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Tipe Pekerjaan<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <select class="form-control select2" name="detailType" id="detailType"
                                            data-placeholder="Select Type" style="width: 100%;" readonly>
                                            <option></option>
                                            @foreach ($types as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Kategori Pekerjaan<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <select class="form-control select2" name="detailCategory" id="detailCategory"
                                            data-placeholder="Select Category" style="width: 100%;"
                                            onchange="getCategory(this)" readonly>
                                            <option></option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category }}">{{ $category }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group" id="note_lain" style="display: none">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Note Kategori
                                        Pekerjaan<span class="text-red">*</span> :</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" name="detailCategoryLain"
                                            id="detailCategoryLain" placeholder="Write Category Note" readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Target Penyelesaian<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <div class="input-group date">
                                            <div class="input-group-addon bg-purple" style="border: none;">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control datepicker" name="detailTarget"
                                                id="detailTarget" placeholder="Select Target Date" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Deskripsi Pekerjaan<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" placeholder="Enter Description" id="detailDescription" readonly></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Tujuan Perbaikan<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" placeholder="Enter Goal" id="detailGoal" readonly></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Kondisi Sekarang<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" placeholder="Enter Condition" id="detailBefore" readonly></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Kondisi Perbaikan<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="3" placeholder="Enter Kaizen Condition" id="detailAfter" readonly></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Lampiran<span
                                            class="text-red"></span> :</label>
                                    <div class="col-sm-5" id="detailLampiran">

                                    </div>
                                </div>
                            </div>
                        </form>
                        <form class="form-horizontal" id="upload_ev" style="display: none">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-12">
                                        <center>---------------------------------&nbsp; Upload Eviden
                                            &nbsp;---------------------------------</center>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Lampiran Eviden<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <input type="file" name="detailEviden[]" id="detailEviden" multiple="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Note :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" id="detailNote"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form class="form-horizontal" id="detail_ev" style="display: none">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-12">
                                        <center>---------------------------------&nbsp; Eviden
                                            &nbsp;---------------------------------</center>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Lampiran Eviden<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-5" id="detailEviden2">

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label style="padding-top: 0;" class="col-sm-3 control-label">Note :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" id="detailNote2" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">CANCEL</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%; display: none;"
                                onclick="saveForm()" id="btn_upload">UPLOAD PROGRESS</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalJob">
        <div class="modal-dialog modal-lg" style="width: 75%">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;"
                            id="pic_shortname"></h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <div class="col-xs-12">
                            <table id="jobTable" class="table table-bordered table-striped table-hover">
                                <thead style="background-color: #605ca8; color: white;">
                                    <tr>
                                        <th style="width: 1%">No.</th>
                                        <th style="width: 1%">ID</th>
                                        <th style="text-align: center; width: 1%">Created at</th>
                                        <th style="text-align: center; width: 1%">Dept.</th>
                                        <th style="text-align: center; width: 5%">Title</th>
                                        <th style="text-align: center; width: 1%">Target Date</th>
                                        <th style="text-align: center; width: 1%">Status</th>
                                        <th style="text-align: center; width: 1%">Report</th>
                                        <th style="text-align: center; width: 1%">Eviden Status</th>
                                        <th style="text-align: center; width: 1%">Eviden</th>
                                        <th style="text-align: center; width: 1%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="jobBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalFile">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="form_number_title" style="text-align: center"></h4><br>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                        <b>Attachment</b>
                        <table class="table table-hover table-striped" id="tableFileAtt"
                            style="margin-bottom: 5px; text-align: center">
                            <tbody id='bodyFilAtt'></tbody>
                        </table>
                        <b>Report</b>
                        <table class="table table-hover table-striped" id="tableFileReport"
                            style="margin-bottom: 5px; text-align: center">
                            <tbody id='bodyFilReport'></tbody>
                        </table>
                        <b>Eviden</b>
                        <table class="table table-hover table-striped" id="tableFileEviden"
                            style="margin-bottom: 5px; text-align: center">
                            <tbody id='bodyFileEviden'></tbody>
                        </table>
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
    <script src="{{ url('js/highcharts-3d.js') }}"></script>
    <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
    <!-- <script src="{{ url('js/exporting.js') }}"></script>
                             <script src="{{ url('js/export-data.js') }}"></script> -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var ejors = [];
        var atts = [];
        var role = "{{ Auth::user()->role_code }}";


        jQuery(document).ready(function() {
            $('.select2').select2();

            $('#detailTarget').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: "yyyy-mm-dd"
            });

            $('body').toggleClass("sidebar-collapse");
            fetchMonitoring();
        });

        function fetchMonitoring() {
            $.get('{{ url('fetch/ejor/monitoring') }}', function(result, status, xhr) {
                if (result.status) {

                    var pics = <?php echo json_encode($pics); ?>;
                    atts = result.atts;

                    var pic_jobs = [];
                    $.each(pics, function(key, value) {
                        pic_jobs.push({
                            'name': value.pic_shortname,
                            'job': 0
                        });
                    })

                    $('#ticketTable').DataTable().clear();
                    $('#ticketTable').DataTable().destroy();

                    $('#ticketTableBody').html('');
                    var body = "";

                    var Categories = [];

                    // var totalWaiting = 0;
                    // var totalProgress = 0;
                    // var totalFinish = 0;

                    var jobCount = [];

                    ejor_approvers = result.ejor_approvers;
                    ejors = result.ejors;
                    approver_count = [];

                    ejor_approvers.reduce(function(res, value) {
                        if (!res[value.form_id]) {
                            res[value.form_id] = {
                                count: 0,
                                form_id: value.form_id
                            };
                            approver_count.push(res[value.form_id])
                        }
                        res[value.form_id].count += 1
                        return res;
                    }, {});

                    $.each(result.ejors, function(key, value) {

                        if (value.pic && (value.status == 'Waiting' || value.status == 'InProgress' || value
                                .status == 'OnHold')) {
                            var stacked = "";
                            stacked += '<li>';
                            stacked += '<table class="table-pic" style="width: 100%;">';
                            stacked += '<tbody>';
                            stacked += '<tr onclick="detailTicket(\'' + value.form_id + '\')">';
                            stacked += '<td style="width: 10%; font-weight: bold;">' + value.form_id +
                                '</td>';
                            stacked += '<td style="width: 60%; font-weight: bold;">' + value.title +
                                '</td>';
                            stacked += '<td style="width: 0.1%; font-weight: bold;">' + (value.priority ||
                                '') + '</td>';
                            stacked +=
                                '<td style="width: 10%; font-weight: bold; text-align: right;"> </td>';
                            if (value.status == 'Waiting') {
                                stacked +=
                                    '<td style="width: 20%; font-weight: bold; text-align: right;"><span class="label" style="color: black; background-color: yellow; border: 1px solid black; text-align: right;">Waiting</span></td>';
                            } else if (value.status == 'InProgress' || value.status == 'Verifying') {
                                stacked +=
                                    '<td style="width: 20%; font-weight: bold; text-align: right;"><span class="label" style="color: black; background-color: #71b1e5; border: 1px solid black; text-align: right;">' +
                                    value.status + '</span></td>';
                            } else {
                                stacked +=
                                    '<td style="width: 20%; font-weight: bold; text-align: right;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black; text-align: right;">OnHold</span></td>';
                            }
                            stacked += '</tr>';
                            stacked += '</tbody>';
                            stacked += '</table>';
                            stacked += '</li>';
                            $('#pic_' + value.pic_id).append(stacked);
                        }


                        if (value.status != 'Finished' || value.status != 'Rejected') {
                            var cnt = 0;
                            body += '<tr>';
                            body +=
                                '<td style="width: 0.1%; font-weight: bold;"><a style="cursor: pointer" onclick="detailTicket(\'' +
                                value.form_id + '\')">' + value.form_id + '</a></td>';
                            body += '<td style="width: 0.1%;">' + value.department_shortname + '</td>';

                            body += '<td style="width: 7%;">(' + value.created_at + ')</br>' + value.title +
                                '</td>';
                            if (value.priority == 'Urgent') {
                                body +=
                                    '<td style="width: 0.1%; text-align: center;"><span class="label" style="color: white; background-color: #e53935; border: 1px solid black;">' +
                                    (value.priority || '') + '</span></td>';

                            } else {
                                body +=
                                    '<td style="width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: white; border: 1px solid black;">' +
                                    (value.priority || '') + '</span></td>';
                            }


                            for (var i = 0; i < result.ejor_approvers.length; i++) {
                                if (result.ejor_approvers[i].form_id == value.form_id) {
                                    cnt += 1;

                                    if (result.ejor_approvers[i].status == 'Approved') {
                                        body +=
                                            '<td style="width: 3%; color: black; background-color: #aee571;">' +
                                            result.ejor_approvers[i].approver_name + '<br>(Approved)<br>' +
                                            result.ejor_approvers[i].approve_at + '</td>';
                                    } else if (result.ejor_approvers[i].status == null) {
                                        // if (typeof result.ejor_approvers[i-1] !== 'undefined') {

                                        // 	if (result.ejor_approvers[i-1].form_id == value.form_id && result.ejor_approvers[i-1].status != null) {
                                        if (value.status == 'Approval') {
                                            body +=
                                                '<td style="width: 3%; color: black; background-color: #e53935;"><a style="color: black;" href="{{ url('index/approval/ejor') }}/' +
                                                value.form_id + '"><div style="height:100%;width:100%">' +
                                                result.ejor_approvers[i].approver_name +
                                                '<br>(Waiting)</div></a></td>';
                                        } else if (value.status == 'Rejected' || value.status == 'OnHold') {
                                            body +=
                                                '<td style="width: 3%; color: white; background-color: black;"><span style="color: white;"><div style="height:100%;width:100%">' +
                                                result.ejor_approvers[i].approver_name +
                                                '</div></span></td>';
                                        } else {
                                            body +=
                                                '<td style="width: 3%; color: black; background-color: #e53935;"><span style="color: black;"><div style="height:100%;width:100%">' +
                                                result.ejor_approvers[i].approver_name +
                                                '<br>(Waiting)</div></span></td>';
                                        }
                                        // } else {
                                        // 	body += '<td style="width: 3%; color: black; background-color: #e53935;"><span style="color: black;"><div style="height:100%;width:100%">'+result.ejor_approvers[i].approver_name+'<br>(Waiting)</div></span></td>';
                                        // }

                                        // if (result.ejor_approvers[i-1].status && result.ejor_approvers[i-1].form_id == value.form_id) {
                                        // body += '<td style="width: 3%; color: black; background-color: #e53935;"><a style="color: black;" href="{{ url('index/approval/ejor') }}/'+value.form_id+'"><div style="height:100%;width:100%">'+result.ejor_approvers[i].approver_name+'<br>(Waiting)</div></a></td>';
                                        // } 
                                        // else if (!result.ejor_approvers[i-1].status && result.ejor_approvers[i-1].form_id == value.form_id) {
                                        // 	body += '<td style="width: 3%; color: black; background-color: #e53935;"><span style="color: black;"><div style="height:100%;width:100%">'+result.ejor_approvers[i].approver_name+'<br>(Waiting)</div></span></td>';
                                        // }
                                        // } else {
                                        // 	body += '<td style="width: 3%; color: black; background-color: #e53935;"><a style="color: black;" href="{{ url('index/approval/ejor') }}/'+value.form_id+'"><div style="height:100%;width:100%">'+result.ejor_approvers[i].approver_name+'<br>(Waiting)</div></a></td>';
                                        // }
                                    } else if (result.ejor_approvers[i].status == 'Rejected') {
                                        body +=
                                            '<td style="width: 3%; color: white; background-color: black;">' +
                                            result.ejor_approvers[i].approver_name + '<br>(Rejected)<br>' +
                                            result.ejor_approvers[i].approve_at + '</td>';
                                    } else if (result.ejor_approvers[i].status == 'OnHold') {
                                        body +=
                                            '<td style="width: 3%; color: black; background-color: #f9a825;">' +
                                            result.ejor_approvers[i].approver_name + '<br>(OnHold)<br>' +
                                            result.ejor_approvers[i].approve_at + '</td>';
                                    }
                                }
                            }
                            if (value.status == 'Approval') {
                                body +=
                                    '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: white; border: 1px solid black;">' +
                                    value.status + '</span></td>';
                            } else if (value.status == 'Waiting') {
                                body +=
                                    '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: yellow; border: 1px solid black;">' +
                                    value.status + '</span></td>';
                            } else if (value.status == 'InProgress') {
                                body +=
                                    '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #71b1e5; border: 1px solid black;">' +
                                    value.status + '</span></td>';
                            } else if (value.status == 'Verifying') {
                                if ('{{ strtoupper(Auth::user()->username) }}' == 'PI1106001' || ~role
                                    .indexOf("MIS")) {
                                    body +=
                                        '<td style="font-weight: bold; width: 0.1%; text-align: center;"><a href="{{ url('index/verify/ejor') }}/' +
                                        value.form_id +
                                        '" class="label" style="color: black; background-color: #71b1e5; border: 1px solid black;">' +
                                        value.status + '</a></td>';
                                } else {
                                    body +=
                                        '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #71b1e5; border: 1px solid black;">' +
                                        value.status + '</span></td>';
                                }
                            } else if (value.status == 'OnHold') {
                                body +=
                                    '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #f9a825; border: 1px solid black;">' +
                                    value.status + '</span></td>';
                            } else if (value.status == 'Finished') {
                                body +=
                                    '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #aee571; border: 1px solid black;">' +
                                    value.status + '</span></td>';
                            } else if (value.status == 'Rejected') {
                                body +=
                                    '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: white; background-color: #e53935; border: 1px solid black;">' +
                                    value.status + '</span></td>';
                            } else {
                                body +=
                                    '<td style="font-weight: bold; width: 0.1%; text-align: center;"><span class="label" style="color: black; background-color: #e0e0e0; border: 1px solid black;">' +
                                    value.status + '</span></td>';
                            }

                            var pic_name = "-";
                            if (value.pic_shortname !== null) {
                                pic_name = value.pic_shortname;
                            }

                            href = "javascript:void(0)";

                            if (value.status == 'Waiting') {
                                href = '{{ url('index/approval/ejor') }}/' + value.form_id;
                            }

                            body += '<td style="width: 0.1%; font-weight: bold;"><a href="' + href + '">' +
                                pic_name + '</a></td>';
                            body += '<td style="width: 0.1%; font-weight: bold;" onclick="getFileInfo(\'' +
                                value.form_id +
                                '\')"><button class="btn btn-danger btn-xs"><i class="fa fa-book"></i> Report(s)</button></td>';
                            body += '</tr>';

                            $.each(pic_jobs, function(key2, value2) {
                                if (value2.name == pic_name && value.status_ev != 'Approved') {
                                    value2.job += 1;
                                }
                            })

                        }
                    });
                    $('#ticketTableBody').append(body);

                    $('#ticketTable tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search ' +
                            title + '" size="10"/>');
                    });

                    var table1 = $('#ticketTable').DataTable({
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
                                {
                                    text: '<i class="fa fa-refresh"></i> Load All Data',
                                    action: function(e, dt, node, config) {
                                        $('.search').each(function(i, obj) {
											$(obj).val('').change();
										});

										$('.search2').each(function(i, obj) {
											$(obj).val('').trigger('change');
										});
                                    }
                                },
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
                        "processing": true,
                        initComplete: function() {
                            this.api()
                                .columns([1])
                                .every(function(dd) {
                                    var column = this;
                                    var theadname = $("#ticketTable th").eq([dd]).text();
                                    var select = $(
                                            '<select class="search2" style="color:black"><option value="" style="font-size:11px;">All</option></select>'
                                        )
                                        .appendTo($(column.footer()).empty())
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(this)
                                                .val());

                                            column.search(val ? '^' + val + '$' : '', true,
                                                    false)
                                                .draw();
                                        });
                                    column
                                        .data()
                                        .unique()
                                        .sort()
                                        .each(function(d, j) {
                                            var vals = d;
                                            if ($("#ticketTable th").eq([dd]).text() ==
                                                'Category') {
                                                vals = d.split(' ')[0];
                                            }
                                            select.append(
                                                '<option style="font-size:11px;" value="' +
                                                d + '">' + vals + '</option>');
                                        });
                                });
                        },
                    });

                    table1.columns().every(function() {
                        var that = this;
                        $('.search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('#ticketTable tfoot tr').appendTo('#ticketTable thead');

                    

                    $('#ticket_pics').html("");
                    var ticketPics = "";

                    function comparator(a, b) {
                        if (a.job > b.job) return -1
                        if (a.job < b.job) return 1
                        return 0
                    }

                    pic_jobs = pic_jobs.sort(comparator);

                    $.each(pic_jobs, function(key2, value2) {
                        var num = 0;
                        $.each(pics, function(key, value) {
                            if (value.pic_shortname == value2.name) {
                                num = value2.job;

                                ticketPics +=
                                    '<div class="box box-widget widget-user-2" style="border: 1px solid black; margin-bottom: 5px; cursor: pointer" onclick="detail_job(\'' +
                                    value.pic_shortname + '\')">';
                                ticketPics +=
                                    '<div class="widget-user-header bg-purple" style="height: 120px;">';
                                ticketPics += '<div class="widget-user-image crop2">';
                                ticketPics += '<img src="{{ url('images/avatar/') }}' + '/' + value
                                    .pic_id + '.jpg' + '" alt="">';
                                ticketPics +=
                                    '<h3 class="widget-user-username" style="font-size: 1.2vw; font-weight: bold;">' +
                                    value.pic_shortname + '<span id=load_' + value.pic_id +
                                    '></span> <br><span style="color:#fac06e"> ' + num +
                                    ' Ejor(s)</span></h3>';
                                ticketPics +=
                                    '<h5 class="widget-user-desc" style="font-size: 1vw;">' + value
                                    .pic_id + ' (' + value.pic_position + ')</h5>';
                                ticketPics += '</div>';
                                ticketPics += '</div>';
                                ticketPics += '</div>';
                            }
                        })

                    });
                    $('#ticket_pics').append(ticketPics);

                    var categories = [];
                    var approval = [];
                    var waiting = [];
                    var inprogress = [];
                    var onhold = [];
                    var rejected = [];
                    var finished = [];


                    $.each(result.charts, function(key, value) {
                        if (categories.indexOf(value.mon) === -1) {
                            categories[categories.length] = value.mon;
                        }
                    })

                    $.each(categories, function(key2, value2) {

                        var sts_app = false;
                        var sts_wait = false;
                        var sts_prog = false;
                        var sts_hold = false;
                        var sts_reject = false;
                        var sts_finish = false;

                        var val_categories = 0;
                        var val_approval = 0;
                        var val_waiting = 0;
                        var val_inprogress = 0;
                        var val_onhold = 0;
                        var val_rejected = 0;
                        var val_finished = 0;



                        $.each(result.charts, function(key, value) {

                            if (value.status == 'Approval' && value2 == value.mon) {
                                val_approval += parseInt(value.jml_ejor);
                                sts_app = true;
                            }
                            if (value.status == 'Waiting' && value2 == value.mon) {
                                val_waiting += parseInt(value.jml_ejor);
                                sts_wait = true;
                            }
                            if ((value.status == 'InProgress' || value.status == 'Verifying') &&
                                value2 == value.mon) {
                                val_inprogress += parseInt(value.jml_ejor);
                                sts_prog = true;
                            }
                            if (value.status == 'OnHold' && value2 == value.mon) {
                                val_onhold += parseInt(value.jml_ejor);
                                sts_hold = true;
                            }
                            if (value.status == 'Rejected' && value2 == value.mon) {
                                val_rejected += parseInt(value.jml_ejor);
                                sts_reject = true;
                            }
                            if (value.status == 'Finished' && value2 == value.mon) {
                                val_finished += parseInt(value.jml_ejor);
                                sts_finish = true;
                            }
                        });

                        if (sts_app == false) {
                            val_approval += parseInt(0);
                        }

                        if (sts_wait == false) {
                            val_waiting += parseInt(0);
                        }

                        if (sts_prog == false) {
                            val_inprogress += parseInt(0);
                        }

                        if (sts_hold == false) {
                            val_onhold += parseInt(0);
                        }

                        if (sts_reject == false) {
                            val_rejected += parseInt(0);
                        }

                        if (sts_finish == false) {
                            val_finished += parseInt(0);
                        }

                        approval.push(val_approval);
                        waiting.push(val_waiting);
                        inprogress.push(val_inprogress);
                        onhold.push(val_onhold);
                        rejected.push(val_rejected);
                        finished.push(val_finished);
                    })

                    Highcharts.chart('chartOutstanding', {
                        chart: {

                        },
                        title: {
                            text: 'Outstanding EJOR'
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            tickInterval: 1,
                            gridLineWidth: 1,
                            categories: categories,
                            crosshair: true
                        },
                        yAxis: [{
                            title: {
                                text: ''
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                                    textOutline: 'none'
                                }
                            }
                        }],
                        legend: {
                            borderWidth: 1
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0;text-shadow: -1px 0 #909090, 0 1px #909090, 1px 0 #909090, 0 -1px #909090;font-size: 16px;font-weight:bold;">{series.name}: </td>' +
                                '<td style="padding:0;font-size:16px;"><b>{point.y:.1f}</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
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
                            name: 'Approval',
                            type: 'column',
                            stack: 'Stock',
                            data: approval,
                            color: 'white'
                        }, {
                            name: 'Waiting',
                            type: 'column',
                            stack: 'Stock',
                            data: waiting,
                            color: '#ffeb3b'
                        }, {
                            name: 'InProgress',
                            type: 'column',
                            stack: 'Stock',
                            data: inprogress,
                            color: '#71b1e5'
                        }]
                    });

                } else {
                    alert('Unidentified Error ' + result.message);

                    return false;
                }
            });
        }

        function compare(a, b) {
            if (a.created_at < b.created_at) {
                return -1;
            }
            if (a.created_at > b.created_at) {
                return 1;
            }
            return 0;
        }

        function detailTicket(form_id) {
            $("#modalJob").modal('hide');
            $("#loading").show();

            var data = {
                form_id: form_id,
                status: 'all',
                remark: 'monitoring'
            }
            $.get('{{ url('fetch/ejor') }}', data, function(result, status, xhr) {
                $("#loading").hide();

                $('#modalDetail').modal('show');

                $("#detailFormId").val(form_id);
                $("#detailSection").val(result.datas[0].section);
                $("#detailTitle").val(result.datas[0].title);
                $("#detailType").val(result.datas[0].job_type).trigger('change');
                $("#detailCategory").val(result.datas[0].job_category).trigger('change');
                $("#detailCategoryLain").val(result.datas[0].job_category_note);
                $("#detailTarget").val(result.datas[0].request_date);

                $("#detailDescription").html(CKEDITOR.instances.detailDescription.setData(result.datas[0]
                    .description));
                $("#detailGoal").html(CKEDITOR.instances.detailGoal.setData(result.datas[0].purpose));
                $("#detailBefore").html(CKEDITOR.instances.detailBefore.setData(result.datas[0].condition_before));
                $("#detailAfter").html(CKEDITOR.instances.detailAfter.setData(result.datas[0].condition_after));

                $("#detailLampiran").empty();
                if (result.datas[0].attachment) {
                    ats = result.datas[0].attachment.split(",");
                    lamp = "";

                    $.each(ats, function(key, value) {
                        lamp += "<a href='{{ url('files/ejor/att') }}/" + value +
                            "' class='btn btn-danger btn-xs' target='_blank'><i class='fa fa-file-pdf-o'></i> " +
                            value + "</a>&nbsp;";
                    })

                    $("#detailLampiran").append(lamp);
                }


                if (result.evidences.length < 1) {
                    $("#upload_ev").show();
                    $("#btn_upload").show();
                    $("#detail_ev").hide();
                } else {
                    if (result.evidences[0].status == 'Approved') {
                        $("#detail_ev").show();
                        $("#upload_ev").hide();
                        $("#btn_upload").hide();

                        atts = result.evidences[0].attachment.split(",");
                        body = "";

                        $.each(atts, function(key, value) {
                            body += "<a href='{{ url('files/ejor/evidence') }}/" + value +
                                "' class='btn btn-danger btn-xs' target='_blank'><i class='fa fa-file-pdf-o'></i> " +
                                value + "</a>";
                        })

                        $("#detailEviden2").append(body);

                        $("#detailNote2").html(CKEDITOR.instances.detailNote2.setData(result.evidences[0].note));

                    } else {
                        $("#upload_ev").show();
                        $("#btn_upload").show();
                        $("#detail_ev").hide();
                    }
                }

            })
        }

        function getCategory(elem) {
            if ($(elem).val() == 'Lain-lain') {
                $("#note_lain").show();
            } else {
                $("#note_lain").hide();
            }
        }

        function saveForm() {
            if ($('#detailEviden').prop('files').length < 1) {
                openErrorGritter('Error', 'Mohon upload Lampiran Eviden');
                return false;
            }

            if (confirm('Anda yakin akan upload evidence dan send mail ke Atasan ?')) {
                $('#loading').show();

                var formData = new FormData();

                var att_count = 0;
                for (var i = 0; i < $('#detailEviden').prop('files').length; i++) {
                    formData.append('att_' + i, $('#detailEviden').prop('files')[i]);
                    att_count++;
                }

                formData.append('form_id', $("#detailFormId").val());
                formData.append('note', CKEDITOR.instances.detailNote.getData());
                formData.append('att_count', att_count);

                $.ajax({
                    url: "{{ url('post/ejor/evidence') }}",
                    method: "POST",
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        if (data.status) {
                            // $('#loading').hide();
                            openSuccessGritter('Success!', data.message);

                            $("#detailEviden").val('');
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        } else {
                            $('#loading').hide();
                            openErrorGritter('Error!', data.message);
                        }
                    }
                });

            }

        }

        function detail_job(short_name) {
            $("#modalJob").modal('show');

            $("#pic_shortname").text('Outstanding Job ' + short_name);
            $('#jobTable').DataTable().clear();
            $('#jobTable').DataTable().destroy();

            $("#jobBody").empty();
            var body = '';
            var no = 1;
            $.each(ejors, function(key, value) {
                if (value.pic_shortname == short_name) {
                    body += '<tr>';
                    body += '<td style="text-align: right">' + no + '</td>';
                    body += '<td>' + value.form_id + '</td>';
                    body += '<td>' + value.created_at.split(' ')[0] + '</td>';
                    body += '<td><center>' + value.department_shortname + '</center></td>';
                    body += '<td>' + value.title + '</td>';
                    body += '<td>' + value.target_date + '</td>';
                    body += '<td><center>' + value.status + '</center></td>';
                    body += '<td><center><a href="{{ url('files/ejor/form') }}/' + value.form_id +
                        '.pdf" class="btn btn-danger btn-xs" target="_blank"><i class="fa fa-file-pdf-o"></i> ' +
                        value.form_id + '</a></center></td>';
                    body += '<td>' + (value.status_ev || '') + '</td>';
                    if (value.att_ev) {
                        body += '<td><center><a href="{{ url('files/ejor/evidence') }}/' + value.att_ev +
                            '" class="btn btn-danger btn-xs" target="_blank"><i class="fa fa-file-pdf-o"></i> EV ' +
                            value.form_id + '</a></center></td>';
                    } else {
                        body += '<td></td>';
                    }

                    if (value.status != "Finished") {
                        body += '<td><center><button class="btn btn-md btn-success" onclick="detailTicket(\'' +
                            value.form_id +
                            '\')"><i class="fa fa-arrow-right"></i> Reporting</button></center></td>';
                    } else {
                        body += '<td></td>';
                    }

                    body += '</tr>';
                    no++;
                }
            });

            $("#jobBody").append(body);
        }

        function getFileInfo(form_number) {
            $("#form_number_title").html("EJOR <b>" + form_number + "</b> Report(s)");

            $("#bodyFile").empty();
            $("#bodyFile").empty();

            $("#bodyFilAtt").empty();
            $("#bodyFilReport").empty();
            $("#bodyFileEviden").empty();

            $.each(atts, function(key, value) {
                if (form_number == value.form_id) {
                    body_file = "";
                    if (value.attachment) {
                        var att = value.attachment.split(',');

                        $.each(att, function(key2, value2) {
                            body_file += "<tr>";
                            body_file += "<td>";
                            body_file += "<a href='" + "{{ url('files/ejor/att/') }}/" + value2 +
                                "' target='_blank'><i class='fa fa-file-pdf-o'></i> " + value2 + "</a>";
                            body_file += "</td>";
                            body_file += "</tr>";
                        })
                    }

                    $("#bodyFilAtt").append(body_file);

                    body_file = "";

                    body_file += "<tr>";
                    body_file += "<td>";
                    body_file += "<a href='" + "{{ url('files/ejor/form/') }}/" + form_number +
                        ".pdf' target='_blank'><i class='fa fa-file-pdf-o'></i> " + form_number + ".pdf</a>";
                    body_file += "</td>";
                    body_file += "</tr>";

                    $("#bodyFilReport").append(body_file);

                    body_file = "";

                    if (value.evv) {
                        var evid = value.evv.split(',');

                        $.each(evid, function(key2, value2) {
                            body_file += "<tr>";
                            body_file += "<td style='border-right: 1px solid #ddd'>Upload at</td>";
                            body_file += "<td>File</td>";
                            body_file += "</tr>";
                            body_file += "<tr>";
                            body_file += "<td style='font-size: 12px; border-right: 1px solid #ddd'>"+value.uploaded_at+"</td>";
                            body_file += "<td>";
                            body_file += "<a href='" + "{{ url('files/ejor/evidence/') }}/" + value2 +
                                "' target='_blank'><i class='fa fa-file-pdf-o'></i> " + value2 + "</a>";
                            body_file += "</td>";
                            body_file += "</tr>";
                        })
                    }

                    $("#bodyFileEviden").append(body_file);
                }
            });

            $("#modalFile").modal('show');
        }

        CKEDITOR.replace('detailBefore', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}',
            toolbar: [{
                    name: 'document',
                    items: ['Source']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                }
            ],
        });

        CKEDITOR.replace('detailAfter', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}',
            toolbar: [{
                    name: 'document',
                    items: ['Source']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                }
            ],
        });

        CKEDITOR.replace('detailDescription', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}',
            toolbar: [{
                    name: 'document',
                    items: ['Source']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                }
            ],
        });

        CKEDITOR.replace('detailGoal', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}',
            toolbar: [{
                    name: 'document',
                    items: ['Source']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                }
            ],
        });

        CKEDITOR.replace('detailNote', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}',
            toolbar: [
                ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
                {
                    name: 'styles',
                    items: ['Styles', 'Format', 'Font', 'FontSize']
                },
                {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic']
                },
                {
                    name: 'insert',
                    items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar']
                },
                {
                    name: 'document',
                    items: ['Source']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                }
            ],
        });

        CKEDITOR.replace('detailNote2', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}',
            toolbar: [
                ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
                {
                    name: 'styles',
                    items: ['Styles', 'Format', 'Font', 'FontSize']
                },
                {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic']
                },
                {
                    name: 'insert',
                    items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar']
                },
                {
                    name: 'document',
                    items: ['Source']
                },
                {
                    name: 'tools',
                    items: ['Maximize']
                }
            ],
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '5000'
            });
            audio_ok.play();
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
            audio_error.play();
        }
    </script>
@endsection
