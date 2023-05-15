@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        #main_table {
            color: white;
            border: 1px solid white;
        }

        #main_table>thead>tr>th {
            color: white;
            text-align: center;
            padding: 2px;
        }

        #main_table>tbody>tr>td {
            padding: 2px;
            height: 40px !important;
        }

        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            text-align: center;
        }

        tfoot>tr>th {
            text-align: center;
        }

        td:hover {
            overflow: visible;
        }

        table>thead>tr>th {
            border: 2px solid #f4f4f4;
            color: white;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content" style="padding-top: 0; padding-bottom: 0">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>


        <div class="row">
            <div class="col-xs-12" style="padding: 1px !important">
                <div class="col-xs-2">
                    <div class="input-group date">
                        <div class="input-group-addon bg-green">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control datepicker" id="tglfrom" placeholder="Month From"
                            style="width: 100%;">
                    </div>
                </div>

                <div class="col-xs-2">
                    <div class="input-group date">
                        <div class="input-group-addon bg-green">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control datepicker" id="tglto" placeholder="Month To"
                            style="width: 100%;">
                    </div>
                </div>

                <div class="col-xs-2">
                    <input type="text" class="form-control" id="sk_num" placeholder="Sakurentsu Number"
                        style="width: 100%;">
                </div>

                <div class="col-xs-2">
                    <button class="btn btn-success btn-sm" onclick="getData()"><i class="fa fa-search"></i> Filter</button>
                    <button class="btn btn-primary btn-sm" onclick="refresh_data()"><i class="fa fa-refresh"></i> Show
                        All</button>
                </div>

                <div class="col-xs-3">
                    <a class="btn bg-purple btn-sm pull-right" href="{{ url('index/sakurentsu/summary') }}"><i
                            class="fa fa-database"></i> 3M/ Trial/ Information MIRAI Summary</a>
                </div>
                <div class="col-xs-1">
                    <a class="btn btn-success btn-sm pull-right" href="{{ url('files/SUMMARY 3M.xlsx') }}"><i
                            class="fa fa-file-excel-o"></i> 3M Summary</a>
                </div>
            </div>

            <div class="col-xs-12" style="margin-top: 5px; padding-right: 0;padding-left: 10px">
                <div id="chart" style="width: 99%"></div>
            </div>

            <div class="col-xs-12" style="padding-right: 0;padding-left: 10px; overflow-y:hidden; overflow-x:scroll; "
                id="double-scroll">
                <table id="main_table" border="1">
                    <thead>
                        <tr>
                            <th colspan="8"
                                style="background-color: #e36e14; font-weight: bold; padding: 2px; border-right: 3px solid red; border-left: 2px solid red">
                                Sakurentsu / Internal</th>
                            <th colspan="7"
                                style="background-color: #4287f5; font-weight: bold; padding: 2px; border-right: 3px solid red">
                                Trial Progress</th>
                            <th colspan="4" style="background-color: #0c42ad; font-weight: bold; padding: 2px">3M Form
                            </th>
                            <th colspan="3"
                                style="background-color: #0c42ad; font-weight: bold; padding: 2px; border-right: 3px solid red">
                                Sign 3M</th>
                            <!-- <th style="background-color: #0c42ad; font-weight: bold; padding: 2px; border-right: 2px solid red">STD Receive</th> -->
                            <th style="background-color: #c2a3e6; font-weight: bold; padding: 2px">3M Imp Form</th>
                            <th colspan="2" style="background-color: #c2a3e6; font-weight: bold; padding: 2px">Sign 3M
                                Imp</th>
                            <!-- <th style="background-color: #c2a3e6; font-weight: bold; padding: 2px; border-right: 2px solid red">STD Receive</th> -->
                        </tr>
                        <tr>
                            <th style="border-left: 3px solid red">No</th>
                            <th style="padding-right: 10vw; padding-left: 10vw">Title</th>
                            <th>Target Date</th>
                            <th>Target Implementasi</th>
                            <th>Category</th>
                            <th>Report</th>
                            <th>Interpreter</th>
                            <th style="border-right: 3px solid red">PIC Manager</th>
                            <th>Trial Issue</th>
                            <th>Issue Approval</th>
                            <th>Receive Trial</th>
                            <th>Trial Progress</th>
                            <th>Trial Result</th>
                            <th>QC Report</th>
                            <th style="border-right: 3px solid red">3M Status</th>
                            <th>Proposer</th>
                            <th>Interpreter</th>
                            <th>Meeting</th>
                            <th>Document</th>
                            <!-- <th>PSS Need</th> -->
                            <th>Related Dept</th>
                            <th>DGM / GM</th>
                            <th style="border-right: 3px solid red">Presdir</th>
                            <!-- <th style="border-right: 2px solid red">STD</th> -->
                            <th>Implement Check</th>
                            <th>Related Dept.</th>
                            <!-- <th>DGM / GM</th> -->
                            <th style="border-right: 2px solid red">STD</th>
                        </tr>
                    </thead>
                    <tbody id="body_main"></tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modal_doc">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="text-align: center" id="sk_num_doc"></h4>
                        <table style="width: 100%; font-size: 14px">
                            <tr>
                                <td style="width: 10%; font-weight: bold">No </td>
                                <td id="no_judul4">: -</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Title</td>
                                <td id="judul_judul4">: -</td>
                            </tr>
                        </table>
                        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                            <br>
                            <table class="table table-hover table-bordered table-striped" id="tableFile">
                                <thead>
                                    <tr style="background-color: #a488aa">
                                        <td><b>Document Name</b></td>
                                        <td><b>PIC</b></td>
                                        <td><b>Document Desc</b></td>
                                        <td><b>Document</b></td>
                                    </tr>
                                </thead>
                                <tbody id='bodyFile'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_sign">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Department Approval List</h4>
                        <table style="width: 100%; font-size: 14px">
                          <tr>
                              <td style="width: 10%; font-weight: bold">No </td>
                              <td id="no_judul5">: -</td>
                          </tr>
                          <tr>
                              <td style="font-weight: bold">Title</td>
                              <td id="judul_judul5">: -</td>
                          </tr>
                      </table>
                        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                            <br>
                            <table class="table table-hover table-bordered table-striped" id="tableAppr">
                                <thead>
                                    <tr style="background-color: #a488aa">
                                        <td><b>Department</b></td>
                                        <td><b>Approval</b></td>
                                        <td><b>Status</b></td>
                                    </tr>
                                </thead>
                                <tbody id='bodyAppr'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_sign_gm">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">DGM / GM Approval List</h4>
                        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                            <br><br>
                            <table class="table table-hover table-bordered table-striped" id="tableApprGM">
                                <thead>
                                    <tr style="background-color: #a488aa">
                                        <td><b>Approval</b></td>
                                        <td><b>Position</b></td>
                                        <td><b>Status</b></td>
                                    </tr>
                                </thead>
                                <tbody id='bodyApprGM'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal_detail">
            <div class="modal-dialog modal-lg" style="width: 90%">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="head_modal"></h4>
                        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                            <br><br>
                            <table class="table table-hover table-bordered table-striped" id="tableDetail">
                                <thead>
                                    <tr style="background-color: #a488aa; text-align: center;">
                                        <td rowspan="2"><b>Number</b></td>
                                        <td rowspan="2"><b>Japanese Title</b></td>
                                        <td rowspan="2"><b>Title</b></td>
                                        <td rowspan="2"><b>Applicant</b></td>
                                        <td rowspan="2"><b>Upload Date</b></td>
                                        <td rowspan="2"><b>Target Date</b></td>
                                        <td rowspan="2"><b>PIC</b></td>
                                        <td rowspan="2"><b>Translator</b></td>
                                        <td colspan="2"><b>File</b></td>
                                        <td rowspan="2"><b>Status</b></td>
                                    </tr>
                                    <tr style="background-color: #a488aa">
                                        <td>Original</td>
                                        <td>Translated</td>
                                    </tr>
                                </thead>
                                <tbody id='bodyDetail'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="sign_info">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <center><b id="title_modal"></b></center>
                        </h4>
                        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                            <br><br>
                            <table class="table table-hover table-bordered table-striped" id="tableInfo">
                                <thead>
                                    <tr style="background-color: #a488aa; text-align: center;">
                                        <td><b>Department</b></td>
                                        <td><b>Approval</b></td>
                                        <td><b>Status</b></td>
                                    </tr>
                                </thead>
                                <tbody id='bodyInfo'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="sign_trial">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <center><b id="title_modal_trial"></b></center>
                        </h4>
                        <table style="width: 100%; font-size: 14px">
                            <tr>
                                <td style="width: 10%; font-weight: bold">No </td>
                                <td id="no_judul2">: -</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Title</td>
                                <td id="judul_judul2">: -</td>
                            </tr>
                        </table>
                        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                            <br><br>
                            <table class="table table-hover table-bordered table-striped" id="tableTrial">
                                <thead>
                                    <tr style="background-color: #a488aa; text-align: center;">
                                        <td><b>Approval</b></td>
                                        <td><b>Approve At</b></td>
                                        <td><b>Status</b></td>
                                    </tr>
                                </thead>
                                <tbody id='bodyTrial'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="receive_trial">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <center><b id="title_modal_receive"></b></center>
                        </h4>
                        <table style="width: 100%; font-size: 14px">
                            <tr>
                                <td style="width: 10%; font-weight: bold">No </td>
                                <td id="no_judul3">: -</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Title</td>
                                <td id="judul_judul3">: -</td>
                            </tr>
                        </table>
                        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                            <br>
                            <table class="table table-hover table-bordered table-striped" id="tableReceive">
                                <thead>
                                    <tr style="background-color: #a488aa; text-align: center;">
                                        <td style="width: 20%"><b>Department</b></td>
                                        <td style="width: 20%"><b>Section</b></td>
                                        <td style="width: 20%"><b>Manager</b></td>
                                        <td style="width: 20%"><b>Chief</b></td>
                                        <td><b>Note</b></td>
                                        <td style="width: 1%">Action</td>
                                    </tr>
                                </thead>
                                <tbody id='bodyReceive'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="result_trial">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <center><b id="title_modal_result"></b></center>
                        </h4>
                        <table style="width: 100%; font-size: 14px">
                            <tr>
                                <td style="width: 5%; font-weight: bold">No </td>
                                <td id="no_judul">: -</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Title</td>
                                <td id="judul_judul">: -</td>
                            </tr>
                        </table>
                        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                            <br>
                            <table class="table table-hover table-bordered table-striped" id="tableResult">
                                <thead>
                                    <tr style="background-color: #a488aa; text-align: center;">
                                        <td style="width: 20%"><b>Department</b></td>
                                        <td style="width: 20%"><b>Section</b></td>
                                        <td style="width: 20%"><b>Must Fill</b></td>
                                        <td style="width: 15%"><b>Trial Method</b></td>
                                        <td style="width: 15%"><b>Trial Result</b></td>
                                        <td><b>Filled At</b></td>
                                    </tr>
                                </thead>
                                <tbody id='bodyResult'></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="notulen">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Upload Notulen Meeting</h4>
                        <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                            <br>
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <input type="hidden" id="input_notulen_sknum">
                            <input type="file" id="input_notulen">
                            <br>
                            <span><b>Select PIC <span class="text-red">*</span>:</b></span>
                            <br>
                            <select class="form-control select2" id="input_pic" data-placeholder="Pilih PIC"
                                style="width: 100%">
                                <option value="Production Control Department">Production Control Department</option>
                                <option value="Production Engineering Department">Production Engineering Department
                                </option>
                                <option value="Procurement Department">Procurement Department</option>
                            </select>
                            <br>
                            <div id="file_notulen"></div>
                            <button class="btn btn-success" style="width: 100%" onclick="upload_notulen()"><i
                                    class="fa fa-plus"></i>&nbsp; Upload Notulen</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <!-- <script src="{{ url('js/exporting.js') }}"></script>
                      <script src="{{ url('js/export-data.js') }}"></script> -->
    <!-- <script src="{{ url('js/accessibility.js') }}"></script> -->
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

        var arr_option = [];

        jQuery(document).ready(function() {
            // $('#double-scroll').doubleScroll();

            $('body').toggleClass("sidebar-collapse");

            $('.select2').select2({
                dropdownAutoWidth: true,
                allowClear: true,
            });

            getData();
        });

        function getData() {
            $("#loading").show();
            data = {
                sakurentsu_number: $("#sk_num").val(),
                posisi: '{{ Request::segment(5) }}'
            }

            var body_master = "";
            var body_int = "";
            $("#body_main").empty();

            $.get('{{ url('fetch/sakurentsu/monitoring/3m') }}', data, function(result, status, xhr) {
                $("#loading").hide();
                $(result.data_sakurentsu).each(function(index, value) {
                    body_master += "<tr>";
                    body_master += "<td style='border-left: 2px solid red; text-align: center'>" + value
                        .sakurentsu_number + "</td>";
                    body_master += "<td style='text-align: center'>" + (value.title || value.title_jp) +
                        "</td>";
                    body_master += "<td><label class='label label-warning'>" + (value.target_dt || '') +
                        "</label></td>";
                    body_master += "<td><label class='label label-primary'>" + (value.target_real_dt ||
                        '') + "</label></td>";
                    body_master += "<td style='color:white'><center><label class='label label-default'>" +
                        value.category + "</label></center></td>";

                    var url = '#';
                    var rpt = '';
                    if (value.category == '3M') {
                        url = "{{ url('/detail/sakurentsu/3m/') }}/" + value.id_tiga_em + "/view";

                        if (value.id_tiga_em != null) {
                            rpt = "<a href='" + url +
                                "' class='label label-danger' target='_blank'><i class='fa fa-file-pdf-o'></i> Form</a>";
                        }
                    } else if (value.category == 'Trial') {
                        url = "{{ url('uploads/sakurentsu/trial_req/report/') }}/Report_" + value
                            .form_number + ".pdf";

                        if (value.form_number != null) {
                            rpt = "<a href='" + url +
                                "' class='label label-danger' target='_blank'><i class='fa fa-file-pdf-o'></i> Form</a>";
                        }
                    }

                    body_master += "<td><center>" + rpt + "</center></td>";

                    // ----------------  Translator Sakurentsu  --------------
                    if (value.trans_sk && value.position == 'interpreter2') {
                        body_master += "<td><center><span class='label label-danger'>" + value.trans_sk
                            .split(" ")[0] + " " + value.trans_sk.split(" ")[1] + "</span></center></td>";
                    } else if (value.trans_sk) {
                        body_master += "<td><center><span class='label label-success'>" + value.trans_sk
                            .split(" ")[0] + " " + value.trans_sk.split(" ")[1] + "</span></center></td>";
                    } else if (!value.remark) {
                        body_master +=
                            "<td><center><span class='label label-danger'>Ahmad Subhan</span></center></td>";
                    } else {
                        body_master += "<td></td>";
                    }

                    // -------------------  PIC DEPT  ---------------------
                    if (value.manager_dept && (value.position == "PIC2" || value.position == "3M")) {
                        body_master +=
                            "<td style='border-right: 3px solid red'><center><span class='label label-success'>" +
                            value.manager_dept.split(" ")[0] + " " + value.manager_dept.split(" ")[1] +
                            "</span></center></td>";
                    } else if (!value.remark && value.position == "PC2") {
                        body_master +=
                            "<td style='border-right: 3px solid red'><center><span class='label label-danger' style='cursor:pointer' onclick='openModalNotulen(\"" +
                            value.sakurentsu_number + "\")'>Meeting PC Dept</span></center></td>";
                    } else if (!value.remark && value.position == "PC1" && !value.manager_dept) {
                        body_master +=
                            "<td style='border-right: 3px solid red'><center><span class='label label-danger'>PC Dept</span></center></td>";
                    } else if (value.position == "PIC") {
                        if (value.category == "Information") {
                            var pics = value.pic.split(',');

                            $(result.data_info).each(function(index12, value12) {
                                if (value12.sakurentsu_number == value.sakurentsu_number) {
                                    body_master +=
                                        "<td style='border-right: 3px solid red'><center><span class='label label-danger' style='cursor:pointer' onclick='openModalInfo(\"" +
                                        value.sakurentsu_number +
                                        "\", \"Recipient of Sakurentsu Information\")'>Receive " +
                                        value12.appr_dept + " / " + pics.length +
                                        "</span></center></td>";
                                }
                            })
                        } else if (value.category == "Trial") {
                            body_master += "<td style='border-right: 3px solid red'><center>";
                            body_master +=
                                "<a class='label label-default' href='{{ url('uploads/sakurentsu/trial_req/notulen') }}/" +
                                value.additional_file +
                                "' target='_blank'><i class='fa fa-book'></i> Notulen</a><br>";
                            body_master += "<span class='label label-danger'>" + value.manager_dept.split(
                                " ")[0] + " " + value.manager_dept.split(" ")[1] + "</span>";
                            body_master += "</center></td>";
                        } else {
                            body_master +=
                                "<td style='border-right: 3px solid red'><center><span class='label label-danger'>" +
                                value.manager_dept.split(" ")[0] + " " + value.manager_dept.split(" ")[1] +
                                "</span></center></td>";
                        }
                    } else {
                        body_master += "<td style='border-right: 3px solid red'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // -------------------- TRIAL ISSUE --------------------
                    if (value.category == "Trial") {
                        stat_trial = 0;

                        $(result.data_trial).each(function(index7, value7) {
                            if (value7.sakurentsu_number == value.sakurentsu_number) {
                                stat_trial = 1;
                                var name = value7.proposer;
                                if (value7.remark) {
                                    body_master +=
                                        "<td><center><span class='label label-success'>" + name
                                        .split(" ")[0] + " " + name.split(" ")[1] +
                                        "</span></center></td>";
                                } else {
                                    body_master += "<td><center><span class='label label-danger'>" +
                                        name.split(" ")[0] + " " + name.split(" ")[1] +
                                        "</span></center></td>";
                                }
                            }
                        })

                        if (stat_trial == 0) {
                            body_master += "<td></td>";
                        }
                    } else {
                        body_master += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // ----------------- APPROVAL TRIAL ISSUE ---------------
                    if (value.category == "Trial") {
                        stat_trial = 0;
                        $(result.sign_trial_issue).each(function(index13, value13) {
                            if (value13.sakurentsu_number == value.sakurentsu_number) {
                                stat_trial = 1;
                                if (value13.approval == value13.approved) {
                                    body_master +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='openModalTrial(\"" +
                                        value13.form_number +
                                        "\",\"Approval Issue Trial Request\", \"" + value
                                        .sakurentsu_number + "\", \"" +
                                        value.title + "\")'>Approval " + value13
                                        .approved + " / " + value13.approval +
                                        "</span></center></td>";
                                } else {
                                    body_master +=
                                        "<td><center><span class='label label-danger' style='cursor:pointer' onclick='openModalTrial(\"" +
                                        value13.form_number +
                                        "\",\"Approval Issue Trial Request\", \"" + value
                                        .sakurentsu_number + "\", \"" +
                                        value.title + "\")'>Approval " + value13
                                        .approved + " / " + value13.approval +
                                        "</span></center></td>";
                                }
                            }
                        })

                        if (stat_trial == 0) {
                            body_master += "<td></td>";
                        }
                    } else {
                        body_master += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // ------------------ RECEIVE TRIAL ISSUE -------------
                    if (value.category == "Trial") {
                        stat_trial = 0;

                        $(result.sign_trial_receive).each(function(index8, value8) {
                            if (value8.sakurentsu_number == value.sakurentsu_number) {
                                stat_trial = 1;
                                if (value8.belum == value8.sudah) {
                                    body_master +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='openModalReceive(\"" +
                                        value8.trial_id +
                                        "\",\"Receiving Trial Request\", \"" + value
                                        .sakurentsu_number + "\", \"" +
                                        value.title + "\")'>Receive " + value8.sudah +
                                        " / " + value8.belum + "</span></center></td>";
                                } else if (value8.status != 'approval') {
                                    body_master +=
                                        "<td><center><span class='label label-danger' style='cursor:pointer' onclick='openModalReceive(\"" +
                                        value8.trial_id +
                                        "\",\"Receiving Trial Request\", \"" + value
                                        .sakurentsu_number + "\", \"" +
                                        value.title + "\")'>Receive " + value8.sudah +
                                        " / " + value8.belum + "</span></center></td>";
                                } else {
                                    body_master += "<td></td>";
                                }
                            }
                        })

                        if (stat_trial == 0) {
                            body_master += "<td></td>";
                        }
                    } else {
                        body_master += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // --------------------- TRIAL PROGRESS -----------------
                    if (value.category == "Trial") {
                        stat_trial2 = 0;

                        $(result.data_trial).each(function(index9, value9) {
                            if (value9.sakurentsu_number == value.sakurentsu_number) {
                                stat_trial2 = 1;
                                if (value9.form_status == "received") {
                                    var name = value9.proposer;
                                    body_master += "<td><center><span class='label label-danger'>" +
                                        name.split(" ")[0] + " " + name.split(" ")[1] +
                                        "</span></center></td>";
                                } else if (value9.form_status == "resulting" || value9
                                    .form_status == "reporting" || value9.form_status ==
                                    "approval final" || value9.form_status == "3m_need" || value9
                                    .form_status == "3M" || value9.form_status == "3M Created") {
                                    var name = value9.proposer;
                                    body_master +=
                                        "<td><center><span class='label label-success'>" + name
                                        .split(" ")[0] + " " + name.split(" ")[1] +
                                        "</span></center></td>";
                                } else {
                                    body_master += "<td></td>";
                                }
                            }
                        })

                        if (stat_trial2 == 0) {
                            body_master += "<td></td>";
                        }
                    } else {
                        body_master += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // --------------------- TRIAL RESULT -----------------
                    if (value.category == "Trial") {
                        stat_trial2 = 0;

                        $(result.sign_trial_result).each(function(index10, value10) {
                            if (value10.sakurentsu_number == value.sakurentsu_number) {
                                stat_trial2 = 1;
                                if (value10.must_fill == value10.fill) {
                                    body_master +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='openModalResult(\"" +
                                        value10.trial_id + "\",\"Resulting Trial Request\", \"" +
                                        value.sakurentsu_number + "\", \"" +
                                        value.title + "\")'>" +
                                        value10.fill + " / " + value10.must_fill +
                                        " Fill</span></center></td>";
                                } else {
                                    body_master +=
                                        "<td><center><span class='label label-danger' style='cursor:pointer' onclick='openModalResult(\"" +
                                        value10.trial_id + "\",\"Resulting Trial Request\", \"" +
                                        value.sakurentsu_number + "\", \"" +
                                        value.title + "\")'>" +
                                        value10.fill + " / " + value10.must_fill +
                                        " Fill</span></center></td>";
                                }
                            }
                        })

                        if (stat_trial2 == 0) {
                            body_master += "<td></td>";
                        }
                    } else {
                        body_master += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // --------------------- QC REPORT -----------------
                    if (value.category == "Trial") {
                        stat_trial3 = 0;

                        $(result.data_trial).each(function(index11, value11) {
                            if (value11.sakurentsu_number == value.sakurentsu_number) {
                                stat_trial3 = 1;
                                if ((value11.form_status == 'reporting' || value11.form_status ==
                                        'approval final' || value11.form_status == '3m_need' ||
                                        value11.form_status == '3M' || value11.form_status ==
                                        '3M Created' || value11.form_status == 'close') && (value11
                                        .qc_report_status == 'OK' || value11.qc_report_status ==
                                        'Not OK')) {
                                    body_master +=
                                        "<td><center><span class='label label-success'>" + value11
                                        .qc_report_status + "</span></center></td>";
                                } else if (value11.form_status == 'reporting' && !value11
                                    .qc_report_status) {
                                    body_master +=
                                        "<td><center><span class='label label-danger'>QA Dept</span></center></td>";
                                } else if (value11.form_status == 'reporting' && value11
                                    .qc_report_status == 'Approval') {
                                    body_master +=
                                        "<td><center><a class='label label-danger' style='cursor:pointer' href='{{ url('uploads/sakurentsu/qc_report') }}/QC_Report_" +
                                        value11.form_number +
                                        ".pdf' target='_blank'>Approval QC Report</a></center></td>";
                                } else {
                                    body_master += "<td></td>";
                                }
                            }
                        })

                        if (stat_trial3 == 0) {
                            body_master += "<td></td>";
                        }
                    } else {
                        body_master += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // --------------------- 3M Status -----------------
                    if (value.category == "Trial") {
                        stat_trial4 = 0;

                        $(result.data_trial).each(function(index12, value12) {
                            if (value12.sakurentsu_number == value.sakurentsu_number) {
                                stat_trial4 = 1;
                                if (value12.three_m_status == 'Need 3M' || value12.three_m_status ==
                                    'No Need 3M') {
                                    body_master +=
                                        "<td style='border-right: 3px solid red'><center><span class='label label-success'>" +
                                        value12.three_m_status + "</span></center></td>";
                                } else {
                                    if (value12.form_status == '3m_need') {
                                        body_master +=
                                            "<td style='border-right: 3px solid red'><center><span class='label label-danger'>" +
                                            value12.proposer + "</span></center></td>";
                                    } else {
                                        body_master +=
                                            "<td style='border-right: 3px solid red'></td>";
                                    }
                                }
                            }
                        })

                        if (stat_trial2 == 0) {
                            body_master += "<td style='border-right: 3px solid red'></td>";
                        }
                    } else {
                        body_master +=
                            "<td style='background-color: #c2a3e6; border-right: 3px solid red'></td>";
                    }

                    // ---------------------  3M   --------------------
                    // ------------------   PROPOSER   ----------------
                    if (value.name && value.title_tiga_em) {
                        body_master += "<td><center><span class='label label-success'>" + value.name +
                            "</span></center></td>";
                    } else if ((!value.remark && value.manager_dept && value.category == "3M" && !value
                            .title_tiga_em && value.name) || (value.position == "3M")) {
                        body_master += "<td><center><span class='label label-danger'>" + value.name +
                            "</span></center></td>";
                    } else {
                        var stat_3m = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        $(result.data_trial).each(function(index13, value13) {
                            if (value13.form_status == '3M Created') {
                                $(result.data_tiga_em_trial).each(function(index14, value14) {
                                    if (value13.form_number == value14.trial_id && value
                                        .sakurentsu_number == value13.sakurentsu_number) {
                                        stat_3m =
                                            "<center><span class='label label-success'>" +
                                            value14.name + "</span></center>";
                                    }
                                })
                            }
                        })

                        if (stat_3m == '') {
                            body_master += "<td></td>";
                        } else {
                            body_master += "<td>" + stat_3m + "</td>";
                        }
                    }


                    // ---------------------  3M   --------------------
                    // ------------------   TRANSLATOR   ----------------
                    if (value.trans_m && parseInt(value.remark) >= 2) {
                        body_master += "<td><center><span class='label label-success'>" + value.trans_m
                            .split('/')[1] + "</span></center></td>";
                    } else if (value.trans_m && parseInt(value.remark) == 1) {
                        body_master += "<td><center><span class='label label-danger'>" + value.trans_m
                            .split('/')[1] + "</span></center></td>";
                    } else if (parseInt(value.remark) == 1) {
                        body_master +=
                            "<td><center><span class='label label-danger'>Ahmad Subhan Hidayat</span></center></td>";
                    } else {
                        var stat_3m_dua = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        $(result.data_trial).each(function(index15, value15) {
                            if (value15.form_status == '3M Created') {
                                $(result.data_tiga_em_trial).each(function(index16, value16) {
                                    if (value15.form_number == value16.trial_id && value
                                        .sakurentsu_number == value15.sakurentsu_number) {
                                        if (value16.translator && parseInt(value16
                                                .remark) >= 2) {
                                            stat_3m_dua =
                                                "<td><center><span class='label label-success'>" +
                                                value16.translator.split('/')[1] +
                                                "</span></center></td>";
                                        } else if (value16.translator && parseInt(value16
                                                .remark) == 1) {
                                            stat_3m_dua =
                                                "<td><center><span class='label label-danger'>" +
                                                value16.translator.split('/')[1] +
                                                "</span></center></td>";
                                        } else if (parseInt(value16.remark) == 1) {
                                            stat_3m_dua =
                                                "<td><center><span class='label label-danger'>Ahmad Subhan Hidayat</span></center></td>";
                                        }

                                    }
                                })
                            }
                        })

                        if (stat_3m_dua == '') {
                            body_master += "<td></td>";
                        } else {
                            body_master += stat_3m_dua;
                        }

                    }


                    // ---------------------  3M   --------------------
                    // ------------------   PREMEETING   ----------------
                    if (value.remark > 2) {
                        body_master +=
                            "<td><center><span class='label label-success'>Meeting</span></center></td>";
                    } else if (value.trans_m && value.remark == 2) {
                        body_master +=
                            "<td><center><span class='label label-danger'>Meeting</span></center></td>";
                    } else {
                        var stat_3m_tiga = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        $(result.data_trial).each(function(index17, value17) {
                            if (value17.form_status == '3M Created') {
                                $(result.data_tiga_em_trial).each(function(index18, value18) {
                                    if (value17.form_number == value18.trial_id && value
                                        .sakurentsu_number == value17.sakurentsu_number) {
                                        if (parseInt(value18.remark) > 2) {
                                            stat_3m_tiga =
                                                "<td><center><span class='label label-success'>Meeting</span></center></td>";
                                        } else if (value18.translator && value18.remark ==
                                            '2') {
                                            stat_3m_tiga =
                                                "<td><center><span class='label label-danger'>Meeting</span></center></td>";
                                        }
                                    }
                                })
                            }
                        })

                        if (stat_3m_tiga == '') {
                            body_master += "<td></td>";
                        } else {
                            body_master += stat_3m_tiga;
                        }
                    }

                    // ---------------------  3M   --------------------
                    // ------------------   DOCUMENT   ----------------
                    if (value.remark >= 3) {
                        var stat_doc = 0;
                        $(result.data_doc).each(function(index2, value2) {
                            if (value2.form_id == value.id_tiga_em) {
                                stat_doc = 1;
                                if (value.remark >= 4) {
                                    body_master +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='doc_list(" +
                                        value.id_tiga_em + ", \"" + value.sakurentsu_number +
                                        "\", \"" + value.title + "\")'>" + value2.doc_uploaded +
                                        " / " +
                                        value2.doc_all + "</span></center></td>";
                                } else if (value.remark >= 3) {
                                    body_master +=
                                        "<td><center><span class='label label-warning' style='cursor:pointer' onclick='doc_list(" +
                                        value.id_tiga_em + ", \"" + value.sakurentsu_number +
                                        "\", \"" + value.title + "\")'>" + value2.doc_uploaded +
                                        " / " +
                                        value2.doc_all + "</span></center></td>";
                                }
                            }
                        })

                        if (stat_doc == 0) {
                            body_master +=
                                "<td><center><span class='label label-success'>No Doc</span></center></td>";
                        }
                    } else {
                        var stat_3m_empat = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        $(result.data_trial).each(function(index19, value19) {
                            if (value19.form_status == '3M Created') {
                                $(result.data_tiga_em_trial).each(function(index20, value20) {
                                    if (value19.form_number == value20.trial_id && value
                                        .sakurentsu_number == value19.sakurentsu_number) {
                                        if (value20.remark >= 3) {
                                            var stat_doc = 0;

                                            $(result.data_doc).each(function(index21,
                                                value21) {
                                                if (value21.form_id == value20
                                                    .id_tiga_em) {
                                                    stat_doc = 1;
                                                    if (value20.remark >= 4) {
                                                        stat_3m_empat =
                                                            "<td><center><span class='label label-success' style='cursor:pointer' onclick='doc_list(" +
                                                            value20.id_tiga_em +
                                                            ", \"" + value
                                                            .sakurentsu_number +
                                                            "\", \"" + value.title +
                                                            "\")'>" + value21
                                                            .doc_uploaded + " / " +
                                                            value21.doc_all +
                                                            "</span></center></td>";
                                                    } else if (value20.remark >=
                                                        3) {
                                                        stat_3m_empat =
                                                            "<td><center><span class='label label-warning' style='cursor:pointer' onclick='doc_list(" +
                                                            value20.id_tiga_em +
                                                            ", \"" + value
                                                            .sakurentsu_number +
                                                            "\", \"" + value.title +
                                                            "\")'>" + value21
                                                            .doc_uploaded + " / " +
                                                            value21.doc_all +
                                                            "</span></center></td>";
                                                    }
                                                }
                                            })

                                            if (stat_doc == 0) {
                                                stat_3m_empat =
                                                    "<td><center><span class='label label-success'>No Doc</span></center></td>";
                                            }
                                        }
                                    }
                                })
                            }
                        })

                        if (stat_3m_empat == '') {
                            body_master += "<td></td>";
                        } else {
                            body_master += stat_3m_empat;
                        }

                    }

                    // -------------------  3M   --------------------
                    // ------------------   PSS   ----------------
                    // body_master += "<td></td>";

                    // -------------------  3M   --------------------
                    // -------------   SIGN RELATED DEPARTMENT   ----------------
                    if (value.related_department) {
                        var count_rel = value.related_department.split(",");

                        var stat_apr = 0;
                        $(result.data_approve).each(function(index22, value22) {
                            if (value22.form_id == value.id_tiga_em && value.remark >= 4) {
                                if (parseInt(value22.dpt_app) < count_rel.length) {
                                    body_master +=
                                        "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form(" +
                                        value.id_tiga_em + ", \"approval\", \""+ value.sakurentsu_number +"\", \""+ value.title +"\")'>" + value22.dpt_app +
                                        " / " + count_rel.length + "</span></center></td>";
                                    stat_apr = 1;
                                } else {
                                    body_master +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form(" +
                                        value.id_tiga_em + ", \"approval\", \""+ value.sakurentsu_number +"\", \""+ value.title +"\")'>" + value22.dpt_app +
                                        " / " + count_rel.length + "</span></center></td>";
                                    stat_apr = 1;
                                }
                            }
                        })

                        if (stat_apr == 0 && value.remark >= 5) {
                            body_master +=
                                "<td><center><span class='label label-danger' style='cursor:pointer' onclick='sign_form(" +
                                value.id_tiga_em + ", \"approval\", \""+ value.sakurentsu_number +"\", \""+ value.title +"\")'>0 / " + count_rel.length +
                                "</span></center></td>";
                        } else if (stat_apr == 0) {
                            body_master += "<td></td>";
                        }
                    } else {
                        var stat_3m_lima = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        $(result.data_trial).each(function(index23, value23) {
                            if (value23.form_status == '3M Created') {
                                $(result.data_tiga_em_trial).each(function(index24, value24) {
                                    if (value23.form_number == value24.trial_id && value
                                        .sakurentsu_number == value23.sakurentsu_number) {
                                        var count_rel = value24.related_department.split(
                                            ",");

                                        var stat_apr = 0;
                                        $(result.data_approve).each(function(index25,
                                            value25) {
                                            if (value25.form_id == value24
                                                .id_tiga_em && value24.remark >= 4
                                            ) {
                                                if (parseInt(value25.dpt_app) <
                                                    count_rel.length) {
                                                    stat_3m_lima =
                                                        "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form(" +
                                                        value24.id_tiga_em +
                                                        ", \"approval\", \""+ value.sakurentsu_number +"\", \""+ value.title +"\")'>" +
                                                        value25.dpt_app + " / " +
                                                        count_rel.length +
                                                        "</span></center></td>";
                                                    stat_apr = 1;
                                                } else {
                                                    stat_3m_lima =
                                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form(" +
                                                        value24.id_tiga_em +
                                                        ", \"approval\", \""+ value.sakurentsu_number +"\", \""+ value.title +"\")'>" +
                                                        value25.dpt_app + " / " +
                                                        count_rel.length +
                                                        "</span></center></td>";
                                                    stat_apr = 1;
                                                }
                                            }
                                        })

                                        if (stat_apr == 0 && value24.remark >= 5) {
                                            stat_3m_lima =
                                                "<td><center><span class='label label-danger' style='cursor:pointer' onclick='sign_form(" +
                                                value24.id_tiga_em +
                                                ", \"approval\", \""+ value.sakurentsu_number +"\", \""+ value.title +"\")'>0 / " + count_rel.length +
                                                "</span></center></td>";
                                        } else if (stat_apr == 0) {
                                            stat_3m_lima = "<td></td>";
                                        }
                                    }
                                })
                            }
                        })

                        if (stat_3m_lima == '') {
                            body_master += "<td></td>";
                        } else {
                            body_master += stat_3m_lima;
                        }
                    }

                    // -------------------  3M   --------------------
                    // -------------   SIGN DGM / GM   ----------------
                    var stat_gm = 0;
                    $(result.data_approve).each(function(index26, value26) {
                        if (value26.form_id == value.id_tiga_em && value.remark >= 4) {
                            if (parseInt(value26.dgm_app) >= parseInt(value26.dgm) && parseInt(
                                    value26.gm_app) >= parseInt(value26.gm)) {
                                body_master +=
                                    "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_gm(" +
                                    value.id_tiga_em + ", \"approval_gm\")'>" + (parseInt(value26
                                        .gm_app) + parseInt(value26.dgm_app)) + " / " + (parseInt(
                                        value26.gm) + parseInt(value26.dgm)) +
                                    "</span></center></td>";
                                stat_gm = 1;
                            } else if (parseInt(value26.dgm_app) == 0 || parseInt(value26.gm_app) ==
                                0) {
                                body_master +=
                                    "<td><center><span class='label label-danger' style='cursor:pointer' onclick='sign_form_gm(" +
                                    value.id_tiga_em + ", \"approval_gm\")'>" + (parseInt(value26
                                        .gm_app) + parseInt(value26.dgm_app)) + " / " + (parseInt(
                                        value26.gm) + parseInt(value26.dgm)) +
                                    "</span></center></td>";
                                stat_gm = 1;
                            } else {
                                body_master +=
                                    "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_gm(" +
                                    value.id_tiga_em + ", \"approval_gm\")'>" + (parseInt(value26
                                        .gm_app) + parseInt(value26.dgm_app)) + " / " + (parseInt(
                                        value26.gm) + parseInt(value26.dgm)) +
                                    "</span></center></td>";
                                stat_gm = 1;
                            }
                        }
                    })

                    if (stat_gm == 0) {
                        var stat_3m_enam = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        $(result.data_trial).each(function(index27, value27) {
                            if (value27.form_status == '3M Created') {
                                $(result.data_tiga_em_trial).each(function(index28, value28) {
                                    if (value27.form_number == value28.trial_id && value
                                        .sakurentsu_number == value27.sakurentsu_number) {
                                        var stat_gm = 0;
                                        $(result.data_approve).each(function(index29,
                                            value29) {
                                            if (value29.form_id == value28
                                                .id_tiga_em && value28.remark >= 4
                                            ) {
                                                if (parseInt(value29.dgm_app) >=
                                                    parseInt(value29.dgm) &&
                                                    parseInt(value29.gm_app) >=
                                                    parseInt(value29.gm)) {
                                                    stat_3m_enam =
                                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_gm(" +
                                                        value28.id_tiga_em +
                                                        ", \"approval_gm\")'>" + (
                                                            parseInt(value29
                                                                .gm_app) + parseInt(
                                                                value29.dgm_app)) +
                                                        " / " + (parseInt(value29
                                                            .gm) + parseInt(
                                                            value29.dgm)) +
                                                        "</span></center></td>";
                                                    stat_gm = 1;
                                                } else if (parseInt(value29
                                                        .dgm_app) < parseInt(value29
                                                        .dgm) || parseInt(value29
                                                        .gm_app) < parseInt(value29
                                                        .gm)) {
                                                    stat_3m_enam =
                                                        "<td><center><span class='label label-danger' style='cursor:pointer' onclick='sign_form_gm(" +
                                                        value28.id_tiga_em +
                                                        ", \"approval_gm\")'>" + (
                                                            parseInt(value29
                                                                .gm_app) + parseInt(
                                                                value29.dgm_app)) +
                                                        " / " + (parseInt(value29
                                                            .gm) + parseInt(
                                                            value29.dgm)) +
                                                        "</span></center></td>";
                                                    stat_gm = 1;
                                                } else {
                                                    stat_3m_enam =
                                                        "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_gm(" +
                                                        value28.id_tiga_em +
                                                        ", \"approval_gm\")'>" + (
                                                            parseInt(value29
                                                                .gm_app) + parseInt(
                                                                value29.dgm_app)) +
                                                        " / " + (parseInt(value29
                                                            .gm) + parseInt(
                                                            value29.dgm)) +
                                                        "</span></center></td>";
                                                    stat_gm = 1;
                                                }
                                            }
                                        })
                                    }
                                })
                            }
                        })

                        if (stat_3m_enam == '') {
                            body_master += "<td></td>";
                        } else {
                            body_master += stat_3m_enam;
                        }
                    }

                    // -------------------  3M   --------------------
                    // -------------   SIGN PRESDIR   ----------------
                    var stat_presdir = 0;
                    var stat_gm = 0;
                    var dt_presdir = '';

                    $(result.data_approve).each(function(index30, value30) {
                        if (value30.form_id == value.id_tiga_em && value.remark >= 4) {
                            if ((parseInt(value30.gm_app) + parseInt(value30.dgm_app)) >= (parseInt(
                                    value30.gm) + parseInt(value30.dgm)) && value.remark == 5) {
                                body_master +=
                                    "<td style='border-right: 3px solid red'><center><span class='label label-danger' style='cursor:pointer' onclick='std_receive(" +
                                    value.id_tiga_em +
                                    ",\"presdir\")'>Presdir</span></center></td>";

                                stat_presdir = 1;
                            } else if (value.remark == 6) {
                                body_master +=
                                    "<td style='border-right: 3px solid red'><center><span class='label label-success' style='cursor:pointer' onclick='std_receive(" +
                                    value.id_tiga_em + ",\"finish\")'>" + value30.dt.replace(/,/g,
                                        "") + "</span></center></td>";
                                stat_presdir = 1;
                            } else if (value.remark > 6) {
                                body_master +=
                                    "<td style='border-right: 3px solid red'><center><span class='label label-success'>" +
                                    value30.dt.replace(/,/g, "") + "</span></center></td>";
                                stat_presdir = 1;
                            }
                        }
                    })

                    if (stat_presdir == 0) {
                        var stat_3m_tujuh = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        $(result.data_trial).each(function(index31, value31) {
                            if (value31.form_status == '3M Created') {
                                $(result.data_tiga_em_trial).each(function(index32, value32) {
                                    if (value31.form_number == value32.trial_id && value
                                        .sakurentsu_number == value31.sakurentsu_number) {
                                        var stat_presdir = 0;

                                        $(result.data_approve).each(function(index33,
                                            value33) {
                                            if (value33.form_id == value32
                                                .id_tiga_em && value32.remark >= 4
                                            ) {
                                                if ((parseInt(value31.gm_app) +
                                                        parseInt(value31.dgm_app)
                                                    ) >= (parseInt(value31.gm) +
                                                        parseInt(value31.dgm)) &&
                                                    value32.remark == 5) {
                                                    stat_3m_tujuh =
                                                        "<td style='border-right: 3px solid red'><center><span class='label label-danger' style='cursor:pointer' onclick='std_receive(" +
                                                        value.id_tiga_em +
                                                        ",\"presdir\")'>Presdir</span></center></td>";

                                                    stat_presdir = 1;
                                                } else if (value32.remark == 6) {
                                                    stat_3m_tujuh =
                                                        "<td style='border-right: 3px solid red'><center><span class='label label-success' style='cursor:pointer' onclick='std_receive(" +
                                                        value.id_tiga_em +
                                                        ",\"finish\")'>" + value31
                                                        .trial_dt.replace(/,/g,
                                                            "") +
                                                        "</span></center></td>";

                                                    stat_presdir = 1;
                                                } else if (value32.remark > 6) {
                                                    stat_3m_tujuh =
                                                        "<td style='border-right: 3px solid red'><center><span class='label label-success'>" +
                                                        value31.trial_dt.replace(
                                                            /,/g, "") +
                                                        "</span></center></td>";

                                                    stat_presdir = 1;
                                                }
                                            }
                                        })
                                    }
                                })
                            }
                        })

                        if (stat_3m_tujuh == '') {
                            body_master += "<td style='border-right: 3px solid red'></td>";
                        } else {
                            body_master += stat_3m_tujuh;
                        }
                    }

                    // -------------------  3M   --------------------
                    // -------------   IMPLEMENT CHECK   ----------------
                    if (value.remark == 7) {
                        body_master += "<td><center><span class='label label-danger'>" + value.name +
                            "</span></center></td>";
                    } else if (value.remark >= 8) {
                        body_master += "<td><center><span class='label label-success'>" + value.name +
                            "</span></center></td>";
                    } else {
                        var stat_3m_delapan = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        $(result.data_trial).each(function(index34, value34) {
                            if (value34.form_status == '3M Created') {
                                $(result.data_tiga_em_trial).each(function(index35, value35) {
                                    if (value34.form_number == value35.trial_id && value
                                        .sakurentsu_number == value34.sakurentsu_number) {
                                        if (value35.remark == 7) {
                                            stat_3m_delapan =
                                                "<td><center><span class='label label-danger'>" +
                                                value35.name + "</span></center></td>";
                                        } else if (value35.remark >= 8) {
                                            stat_3m_delapan =
                                                "<td><center><span class='label label-success'>" +
                                                value35.name + "</span></center></td>";
                                        }
                                    }
                                })
                            }
                        })

                        if (stat_3m_delapan == '') {
                            body_master += "<td></td>";
                        } else {
                            body_master += stat_3m_delapan;
                        }
                    }


                    // -------------------  3M   --------------------
                    // -------------   IMPLEMENT SIGN DEPT   ----------------
                    var stat_imp_dpt = 0;
                    $(result.data_sign_imp).each(function(index36, value36) {
                        if (value36.form_id == value.id_tiga_em) {
                            if (value36.dpt > value36.dpt_app) {
                                body_master +=
                                    "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_imp(" +
                                    value.id_tiga_em + ", \"imp_approval\", \""+ value.sakurentsu_number+"\", \""+ value.title+"\")'>" + value36.dpt_app +
                                    " / " + value36.dpt + "</span></center></td>";
                                stat_imp_dpt = 1;
                            } else {
                                body_master +=
                                    "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_imp(" +
                                    value.id_tiga_em + ", \"imp_approval\", \""+ value.sakurentsu_number+"\", \""+ value.title+"\")'>" + value36.dpt_app +
                                    " / " + value36.dpt + "</span></center></td>";
                                stat_imp_dpt = 1;
                            }
                        }
                    })

                    if (stat_imp_dpt == 0) {
                        var stat_3m_sembilan = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        $(result.data_trial).each(function(index37, value37) {
                            if (value37.form_status == '3M Created') {
                                $(result.data_tiga_em_trial).each(function(index38, value38) {
                                    if (value37.form_number == value38.trial_id && value
                                        .sakurentsu_number == value37.sakurentsu_number) {
                                        var stat_imp_dpt = 0;
                                        $(result.data_sign_imp).each(function(index39,
                                            value39) {
                                            if (value39.form_id == value38
                                                .id_tiga_em) {

                                                if (value39.dpt > value39.dpt_app) {
                                                    stat_3m_sembilan =
                                                        "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_imp(" +
                                                        value38.id_tiga_em +
                                                        ", \"imp_approval\", \""+ value.sakurentsu_number+"\", \""+ value.title+"\")'>" +
                                                        value39.dpt_app + " / " +
                                                        value39.dpt +
                                                        "</span></center></td>";
                                                    stat_imp_dpt = 1;
                                                } else {
                                                    stat_3m_sembilan =
                                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_imp(" +
                                                        value38.id_tiga_em +
                                                        ", \"imp_approval\", \""+ value.sakurentsu_number+"\", \""+ value.title+"\")'>" +
                                                        value39.dpt_app + " / " +
                                                        value39.dpt +
                                                        "</span></center></td>";
                                                    stat_imp_dpt = 1;
                                                }
                                            }
                                        })
                                    }
                                })
                            }
                        })

                        if (stat_3m_sembilan == '') {
                            body_master += "<td></td>";
                        } else {
                            body_master += stat_3m_sembilan;
                        }

                    }

                    // -------------------  3M   --------------------
                    // -------------   IMPLEMENT SIGN DGM GM   ----------------

                    // var stat_imp_gm = 0;
                    // $(result.data_sign_imp).each(function(index40, value40) {
                    //   if (value40.form_id == value.id_tiga_em) {
                    //     if (value40.dgm_app == value40.dgm && value40.gm_app == value40.gm) {
                    //       body_master += "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_gm("+value.id_tiga_em+", \"imp_approval_up\")'>"+(parseInt(value40.dgm_app) + parseInt(value40.gm_app))+"/"+(parseInt(value40.dgm) + parseInt(value40.gm))+"</span></center></td>";
                    //       stat_imp_gm = 1;
                    //     } else {
                    //       body_master += "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_gm("+value.id_tiga_em+", \"imp_approval_up\")'>"+(parseInt(value40.dgm_app) + parseInt(value40.gm_app))+"/"+(parseInt(value40.dgm) + parseInt(value40.gm))+"</span></center></td>";
                    //       stat_imp_gm = 1;
                    //     }
                    //   }
                    // })

                    // if (stat_imp_gm == 0) {
                    //   var stat_3m_sepuluh = '';
                    //   // ------------- DARI TRIAL REQUEST ------------
                    //   $(result.data_trial).each(function(index41, value41) {
                    //     if (value41.form_status == '3M Created') {
                    //       $(result.data_tiga_em_trial).each(function(index42, value42) {
                    //         if (value41.form_number == value42.trial_id && value.sakurentsu_number == value41.sakurentsu_number) {
                    //           $(result.data_sign_imp).each(function(index43, value43) {
                    //             if (value43.form_id == value.id_tiga_em) {
                    //               if (value43.dgm_app == value43.dgm && value43.gm_app == value43.gm) {
                    //                 stat_3m_sepuluh = "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_gm("+value.id_tiga_em+", \"imp_approval_up\")'>"+(parseInt(value43.dgm_app) + parseInt(value43.gm_app))+"/"+(parseInt(value43.dgm) + parseInt(value43.gm))+"</span></center></td>";
                    //                 stat_imp_gm = 1;
                    //               } else {
                    //                 stat_3m_sepuluh = "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_gm("+value.id_tiga_em+", \"imp_approval_up\")'>"+(parseInt(value43.dgm_app) + parseInt(value43.gm_app))+"/"+(parseInt(value43.dgm) + parseInt(value43.gm))+"</span></center></td>";
                    //                 stat_imp_gm = 1;
                    //               }
                    //             }
                    //           })
                    //         }
                    //       })
                    //     }
                    //   })

                    //   if (stat_3m_sepuluh == '') {
                    //     body_master += "<td></td>";
                    //   } else {
                    //     body_master += stat_3m_sepuluh;
                    //   }

                    // }

                    // -------------------  3M   --------------------
                    // -------------   STD RECEIVE   ----------------
                    if (value.remark == 9) {
                        var role = "{{ Auth::user()->role_code }}";
                        if ("{{ strtoupper(Auth::user()->username) }}" == "PI1211001" ||
                            "{{ strtoupper(Auth::user()->username) }}" == "PI0904007" || ~role.indexOf(
                                "MIS")) {
                            href = "{{ url('index/sakurentsu/3m/implementation/sign') }}/" + value
                                .id_tiga_em + "/IMPLEMENT STD'";
                        } else {
                            href = "javascript:void(0)";
                        }

                        body_master += "<td style='border-right: 2px solid red'><center><a href='" + href +
                            "' class='label label-primary'>Waiting</a></center></td>";
                    } else if (value.remark == 10) {
                        body_master +=
                            "<td style='border-right: 2px solid red'><center><span class='label label-success'>Close</span></center></td>";
                    } else {
                        var stat_3m_sebelas = '';
                        $(result.data_trial).each(function(index44, value44) {
                            if (value44.form_status == '3M Created') {
                                $(result.data_tiga_em_trial).each(function(index45, value45) {
                                    if (value44.form_number == value45.trial_id && value
                                        .sakurentsu_number == value44.sakurentsu_number) {
                                        if (value45.remark == 9) {
                                            stat_3m_sebelas =
                                                "<td style='border-right: 2px solid red'><center><span class='label label-primary'>Waiting</span></center></td>";
                                        } else if (value45.remark == 10) {
                                            stat_3m_sebelas =
                                                "<td style='border-right: 2px solid red'><center><span class='label label-success'>Close</span></center></td>";
                                        }
                                    }
                                })
                            }
                        });

                        if (stat_3m_sebelas == '') {
                            body_master += "<td style='border-right: 2px solid red'></td>";
                        } else {
                            body_master += stat_3m_sebelas;
                        }
                    }

                    body_master += "</tr>";
                })

                // --------------------- INTERNAL --------------------

                $(result.internal).each(function(ind1, val1) {
                    if (val1.kategori == '3M' && val1.remark == '10') {
                        return;
                    }

                    body_int += "<tr>";
                    body_int += "<td style='border-left: 2px solid red; text-align: center'>" + val1
                        .form_number + "</td>";
                    body_int += "<td style='text-align: center'>" + val1.title + "</td>";
                    body_int += "<td><label class='label label-warning'>" + (val1.target_dt || '') +
                        "</label></td>";
                    body_int += "<td><label class='label label-primary'>" + (val1.target_real_dt || '') +
                        "</label></td>";
                    body_int += "<td style='color:white'><center><label class='label label-default'>" + val1
                        .kategori + "</label></center></td>";

                    var url = '#';
                    var rpt = '';
                    if (val1.kategori == '3M') {
                        url = "{{ url('/detail/sakurentsu/3m/') }}/" + val1.ids + "/view";

                        if (val1.ids != null) {
                            rpt = "<a href='" + url +
                                "' class='label label-danger' target='_blank'><i class='fa fa-file-pdf-o'></i> Form</a>";
                        }
                    } else if (val1.kategori == 'Trial') {
                        url = "{{ url('uploads/sakurentsu/trial_req/report/') }}/Report_" + val1
                            .form_number + ".pdf";

                        if (val1.form_number != null) {
                            rpt = "<a href='" + url +
                                "' class='label label-danger' target='_blank'><i class='fa fa-file-pdf-o'></i> Form</a>";
                        }
                    }

                    body_int += "<td><center>" + rpt + "</center></td>";

                    // if (val1.trans_m) {
                    //   body_int += "<td><center><span class='label label-success'>"+(val1.trans_m.split('/')[1] || '')+"</span></center></td>";
                    // } else {
                    body_int += "<td></td>";
                    // }
                    if (val1.manager) {
                        body_int +=
                            "<td style='border-right: 3px solid red'><center><span class='label label-success'>" +
                            (val1.manager.split('/')[1].split(" ")[0] + " " + val1.manager.split('/')[1]
                                .split(" ")[1] || '') + "</span></center></td>";
                    } else {
                        body_int += "<td style='border-right: 3px solid red'></td>";
                    }

                    // -------------------- TRIAL REQUEST ------------------
                    // -------------------- TRIAL ISSUE --------------------
                    if (val1.kategori == "Trial") {
                        var name = val1.name;
                        if (val1.form_status) {
                            body_int += "<td><center><span class='label label-success'>" + name.split(" ")[
                                0] + " " + name.split(" ")[1] + "</span></center></td>";
                        } else {
                            body_int += "<td><center><span class='label label-danger'>" + name.split(" ")[
                                0] + " " + name.split(" ")[1] + "</span></center></td>";
                        }
                    } else {
                        body_int += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // ----------------- APPROVAL TRIAL ISSUE ---------------
                    if (val1.kategori == "Trial") {
                        stat_trial = 0;
                        $(result.sign_trial_issue).each(function(ind3, val3) {
                            if (val3.form_number == val1.form_number) {
                                stat_trial = 1;
                                if (val3.approval == val3.approved) {
                                    body_int +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='openModalTrial(\"" +
                                        val3.form_number +
                                        "\",\"Approval Issue Trial Request\", \"" + val3
                                        .form_number + "\", \"" +
                                        val1.title + "\")'>Approval " + val3
                                        .approved + " / " + val3.approval + "</span></center></td>";
                                } else {
                                    body_int +=
                                        "<td><center><span class='label label-danger' style='cursor:pointer' onclick='openModalTrial(\"" +
                                        val3.form_number +
                                        "\",\"Approval Issue Trial Request\", \"" + val3
                                        .form_number + "\", \"" +
                                        val1.title + "\")'>Approval " + val3
                                        .approved + " / " + val3.approval + "</span></center></td>";
                                }
                            }
                        })

                        if (stat_trial == 0) {
                            body_int += "<td></td>";
                        }
                    } else {
                        body_int += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // ------------------ RECEIVE TRIAL ISSUE -------------
                    if (val1.kategori == "Trial") {
                        stat_trial = 0;

                        $(result.sign_trial_receive).each(function(ind4, val4) {
                            if (val4.trial_id == val1.form_number) {
                                stat_trial = 1;
                                if (val4.belum == val4.sudah) {
                                    body_int +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='openModalReceive(\"" +
                                        val4.trial_id +
                                        "\",\"Receiving Trial Request\", \"" + val1.form_number +
                                        "\", \"" +
                                        val1.title + "\")'>Receive " + val4.sudah +
                                        " / " + val4.belum + "</span></center></td>";
                                } else if (val4.status != 'approval') {
                                    body_int +=
                                        "<td><center><span class='label label-danger' style='cursor:pointer' onclick='openModalReceive(\"" +
                                        val4.trial_id +
                                        "\",\"Receiving Trial Request\", \"" + val1.form_number +
                                        "\", \"" +
                                        val1.title + "\")'>Receive " + val4.sudah +
                                        " / " + val4.belum + "</span></center></td>";
                                } else {
                                    body_int += "<td></td>";
                                }
                            }
                        })

                        if (stat_trial == 0) {
                            body_int += "<td></td>";
                        }
                    } else {
                        body_int += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // --------------------- TRIAL PROGRESS -----------------
                    if (val1.kategori == "Trial") {
                        if (val1.form_status == "received") {
                            var name = val1.name;
                            body_int += "<td><center><span class='label label-danger'>" + name.split(" ")[
                                0] + " " + name.split(" ")[1] + "</span></center></td>";
                        } else if (val1.form_status == "resulting" || val1.form_status == "reporting" ||
                            val1.form_status == "approval final" || val1.form_status == "3m_need" || val1
                            .form_status == "3M" || val1.form_status == "3M Created") {
                            var name = val1.name;
                            body_int += "<td><center><span class='label label-success'>" + name.split(" ")[
                                0] + " " + name.split(" ")[1] + "</span></center></td>";
                        } else {
                            body_int += "<td></td>";
                        }
                    } else {
                        body_int += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // --------------------- TRIAL RESULT -----------------
                    if (val1.kategori == "Trial") {
                        stat_trial2 = 0;

                        $(result.sign_trial_result).each(function(ind6, val6) {
                            if (val6.trial_id == val1.form_number) {
                                stat_trial2 = 1;
                                if (val6.must_fill == val6.fill) {
                                    body_int +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='openModalResult(\"" +
                                        val6.trial_id + "\",\"Resulting Trial Request\",\"" + val6
                                        .trial_id + "\",\"" + val1.title + "\")'>" + val6
                                        .fill + " / " + val6.must_fill +
                                        " Fill</span></center></td>";
                                } else {
                                    body_int +=
                                        "<td><center><span class='label label-danger' style='cursor:pointer' onclick='openModalResult(\"" +
                                        val6.trial_id + "\",\"Resulting Trial Request\",\"" + val6
                                        .trial_id + "\",\"" + val1.title + "\")'>" + val6
                                        .fill + " / " + val6.must_fill +
                                        " Fill</span></center></td>";
                                }
                            }
                        })

                        if (stat_trial2 == 0) {
                            body_int += "<td></td>";
                        }
                    } else {
                        body_int += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // --------------------- QC REPORT -----------------
                    if (val1.kategori == "Trial") {
                        if ((val1.form_status == 'reporting' || val1.form_status == 'approval final' || val1
                                .form_status == '3m_need' || val1.form_status == '3M' || val1.form_status ==
                                '3M Created' || val1.form_status == 'close') && (val1.qc_report_status ==
                                'OK' || val1.qc_report_status == 'Not OK')) {
                            body_int += "<td><center><span class='label label-success'>" + val1
                                .qc_report_status + "</span></center></td>";
                        } else if (val1.form_status == 'reporting' && !val1.qc_report_status) {
                            body_int +=
                                "<td><center><span class='label label-danger'>QA Dept</span></center></td>";
                        } else if (val1.form_status == 'reporting' && val1.qc_report_status == 'Approval') {
                            body_int +=
                                "<td><center><a class='label label-danger' style='cursor:pointer' href='{{ url('uploads/sakurentsu/qc_report') }}/QC_Report_" +
                                val1.form_number +
                                ".pdf' target='_blank'>Approval QC Report</a></center></td>";
                        } else {
                            body_int += "<td></td>";
                        }
                    } else {
                        body_int += "<td style='background-color: #c2a3e6'></td>";
                    }


                    // -------------------- TRIAL REQUEST ------------------
                    // --------------------- 3M Status -----------------
                    if (val1.kategori == "Trial") {
                        stat_trial4 = 0;

                        // $(result.data_trial).each(function(ind8, val8) {
                        // if (val8.form_number == val1.form_number) {
                        // stat_trial4 = 1;
                        if (val1.remark == 'Need 3M' || val1.remark == 'No Need 3M') {
                            body_int +=
                                "<td style='border-right: 3px solid red'><center><span class='label label-success'>" +
                                val1.remark + "</span></center></td>";
                        } else {
                            if (val1.form_status == '3m_need') {
                                body_int +=
                                    "<td style='border-right: 3px solid red'><center><span class='label label-danger'>" +
                                    val1.name + "</span></center></td>";
                            } else {
                                body_int += "<td style='border-right: 3px solid red'></td>";
                            }
                        }
                        // }
                        // })

                        // if (stat_trial2 == 0) {
                        //   body_int += "<td style='border-right: 3px solid red'></td>";
                        // }
                    } else {
                        body_int +=
                            "<td style='background-color: #c2a3e6; border-right: 3px solid red'></td>";
                    }


                    // ---------------------  3M   --------------------
                    // ------------------   PROPOSER   ----------------

                    if (val1.kategori == "3M") {
                        body_int += "<td><center><span class='label label-success'>" + val1.name +
                            "</span></center></td>";
                    } else {
                        var stat_3m = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        if (val1.form_status == '3M Created') {
                            $(result.data_tiga_em_trial).each(function(ind9, val9) {
                                if (val1.form_number == val9.trial_id) {
                                    stat_3m = "<td><center><span class='label label-success'>" +
                                        val9.name + "</span></center></td>";
                                }
                            })

                            if (stat_3m == '') {
                                stat_3m = "<td><center><span class='label label-danger'>" + val1.name.split(
                                    " ")[0] + " " + val1.name.split(" ")[1] + "</span></center></td>";

                            }
                        }

                        if (stat_3m == '') {
                            body_int += "<td></td>";
                        } else {
                            body_int += stat_3m;
                        }
                    }

                    // ---------------------  3M   --------------------
                    // ------------------   TRANSLATOR   ----------------
                    if (val1.kategori == "3M") {
                        if (val1.trans_m && parseInt(val1.remark) >= 2) {
                            body_int += "<td><center><span class='label label-success'>" + val1.trans_m
                                .split('/')[1] + "</span></center></td>";
                        } else if (val1.trans_m && parseInt(val1.remark) == 1) {
                            body_int += "<td><center><span class='label label-danger'>" + val1.trans_m
                                .split('/')[1] + "</span></center></td>";
                        } else if (parseInt(val1.remark) == 1) {
                            body_int +=
                                "<td><center><span class='label label-danger'>Ahmad Subhan Hidayat</span></center></td>";
                        }
                    } else {
                        var stat_3m_dua = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        if (val1.form_status == '3M Created') {
                            $(result.data_tiga_em_trial).each(function(ind10, val10) {
                                if (val1.form_number == val10.trial_id) {
                                    if (val10.translator && parseInt(val10.remark) >= 2) {
                                        stat_3m_dua =
                                            "<td><center><span class='label label-success'>" + val10
                                            .translator.split('/')[1] + "</span></center></td>";
                                    } else if (val10.translator && parseInt(val10.remark) == 1) {
                                        stat_3m_dua =
                                            "<td><center><span class='label label-danger'>" + val10
                                            .translator.split('/')[1] + "</span></center></td>";
                                    } else if (parseInt(val10.remark) == 1) {
                                        stat_3m_dua =
                                            "<td><center><span class='label label-danger'>Ahmad Subhan Hidayat</span></center></td>";
                                    }

                                }
                            })
                        }

                        if (stat_3m_dua == '') {
                            body_int += "<td></td>";
                        } else {
                            body_int += stat_3m_dua;
                        }
                    }

                    // ---------------------  3M   --------------------
                    // ------------------   PREMEETING   ----------------
                    if (val1.kategori == "3M") {
                        if (val1.remark > 2) {
                            body_int +=
                                "<td><center><span class='label label-success'>Meeting</span></center></td>";
                        } else if (val1.trans_m && val1.remark == 2) {
                            body_int +=
                                "<td><center><span class='label label-danger'>Meeting</span></center></td>";
                        } else {
                            body_int += "<td></td>";
                        }
                    } else {
                        var stat_3m_tiga = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        if (val1.form_status == '3M Created') {
                            $(result.data_tiga_em_trial).each(function(ind11, val11) {
                                if (val1.form_number == val11.trial_id) {
                                    if (parseInt(val11.remark) > 2) {
                                        stat_3m_tiga =
                                            "<td><center><span class='label label-success'>Meeting</span></center></td>";
                                    } else if (val11.translator && val11.remark == '2') {
                                        stat_3m_tiga =
                                            "<td><center><span class='label label-danger'>Meeting</span></center></td>";
                                    }
                                }
                            })
                        }

                        if (stat_3m_tiga == '') {
                            body_int += "<td></td>";
                        } else {
                            body_int += stat_3m_tiga;
                        }
                    }

                    // ---------------------  3M   --------------------
                    // ------------------   DOCUMENT   ----------------
                    if (val1.kategori == "3M") {
                        if (val1.remark >= 3) {
                            var stat_doc = 0;
                            $(result.data_doc).each(function(ind12, val12) {
                                if (val12.form_id == val1.ids) {
                                    stat_doc = 1;
                                    if (val1.remark >= 4) {
                                        body_int +=
                                            "<td><center><span class='label label-success' style='cursor:pointer' onclick='doc_list(" +
                                            val1.ids + ", \"" + val1.form_number + "\", \"" + val1
                                            .title + "\")'>" + val12.doc_uploaded + " / " + val12
                                            .doc_all + "</span></center></td>";
                                    } else if (val1.remark >= 3) {
                                        body_int +=
                                            "<td><center><span class='label label-warning' style='cursor:pointer' onclick='doc_list(" +
                                            val1.ids + ", \"" + val1.form_number + "\", \"" + val1
                                            .title + "\")'>" + val12.doc_uploaded + " / " + val12
                                            .doc_all + "</span></center></td>";
                                    }
                                }
                            })

                            if (stat_doc == 0) {
                                body_int +=
                                    "<td><center><span class='label label-success'>No Doc</span></center></td>";
                            }
                        } else {
                            body_int += "<td></td>";
                        }
                    } else {
                        var stat_3m_empat = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        if (val1.form_status == '3M Created') {
                            $(result.data_tiga_em_trial).each(function(ind13, val13) {
                                if (val1.form_number == val13.trial_id) {
                                    if (val13.remark >= 3) {
                                        var stat_doc = 0;

                                        $(result.data_doc).each(function(ind14, val14) {
                                            if (val14.form_id == val13.id_tiga_em) {
                                                stat_doc = 1;
                                                if (val13.remark >= 4) {
                                                    stat_3m_empat =
                                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='doc_list(" +
                                                        val13.id_tiga_em + ", \"" + val1
                                                        .form_number + "\", \"" + val1
                                                        .title + "\")'>" + val14
                                                        .doc_uploaded + " / " + val14
                                                        .doc_all + "</span></center></td>";
                                                } else if (val13.remark >= 3) {
                                                    stat_3m_empat =
                                                        "<td><center><span class='label label-warning' style='cursor:pointer' onclick='doc_list(" +
                                                        val13.id_tiga_em + ", \"" + val1
                                                        .form_number + "\", \"" + val1
                                                        .title + "\")'>" + val14
                                                        .doc_uploaded + " / " + val14
                                                        .doc_all + "</span></center></td>";
                                                }
                                            }
                                        })

                                        if (stat_doc == 0) {
                                            stat_3m_empat =
                                                "<td><center><span class='label label-success'>No Doc</span></center></td>";
                                        }
                                    }
                                }
                            })
                        }

                        if (stat_3m_empat == '') {
                            body_int += "<td></td>";
                        } else {
                            body_int += stat_3m_empat;
                        }
                    }

                    // -------------------  3M   --------------------
                    // ------------------   PSS   ----------------
                    // body_int += "<td></td>";

                    // -------------------  3M   --------------------
                    // -------------   SIGN RELATED DEPARTMENT   ----------------
                    if (val1.kategori == "3M") {
                        var count_rel = val1.related_department.split(",");

                        var stat_apr = 0;
                        $(result.data_approve).each(function(ind15, val15) {
                            if (val15.form_id == val1.ids && val1.remark >= 4) {
                                if (parseInt(val15.dpt_app) < count_rel.length) {
                                    body_int +=
                                        "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form(" +
                                        val1.ids + ", \"approval\", \""+ val1.form_number +"\", \""+ val1.title +"\")'>" + val15.dpt_app + " / " +
                                        count_rel.length + "</span></center></td>";
                                    stat_apr = 1;
                                } else {
                                    body_int +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form(" +
                                        val1.ids + ", \"approval\", \""+ val1.form_number +"\", \""+ val1.title +"\")'>" + val15.dpt_app + " / " +
                                        count_rel.length + "</span></center></td>";
                                    stat_apr = 1;
                                }
                            }
                        })

                        if (stat_apr == 0 && val1.remark >= 5) {
                            body_int +=
                                "<td><center><span class='label label-danger' style='cursor:pointer' onclick='sign_form(" +
                                val1.ids + ", \"approval\", \""+ val1.form_number +"\", \""+ val1.title +"\")'>0 / " + count_rel.length +
                                "</span></center></td>";
                        } else if (stat_apr == 0) {
                            body_int += "<td></td>";
                        }
                    } else {
                        var stat_3m_lima = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        if (val1.form_status == '3M Created') {
                            $(result.data_tiga_em_trial).each(function(ind16, val16) {
                                if (val1.form_number == val16.trial_id) {
                                    var count_rel = val16.related_department.split(",");

                                    var stat_apr = 0;
                                    $(result.data_approve).each(function(ind17, val17) {
                                        if (val17.form_id == val16.id_tiga_em && val16
                                            .remark >= 4) {
                                            if (parseInt(val17.dpt_app) < count_rel
                                                .length) {
                                                stat_3m_lima =
                                                    "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form(" +
                                                    val16.id_tiga_em + ", \"approval\", \""+ val1.form_number +"\", \""+ val1.title +"\")'>" +
                                                    val17.dpt_app + " / " + count_rel
                                                    .length + "</span></center></td>";
                                                stat_apr = 1;
                                            } else {
                                                stat_3m_lima =
                                                    "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form(" +
                                                    val16.id_tiga_em + ", \"approval\", \""+ val1.form_number +"\", \""+ val1.title +"\")'>" +
                                                    val17.dpt_app + " / " + count_rel
                                                    .length + "</span></center></td>";
                                                stat_apr = 1;
                                            }
                                        }
                                    })

                                    if (stat_apr == 0 && val16.remark >= 5) {
                                        stat_3m_lima =
                                            "<td><center><span class='label label-danger' style='cursor:pointer' onclick='sign_form(" +
                                            val16.id_tiga_em + ", \"approval\", \""+ val1.form_number +"\", \""+ val1.title +"\")'>0 / " + count_rel
                                            .length + "</span></center></td>";
                                    } else if (stat_apr == 0) {
                                        stat_3m_lima = "<td></td>";
                                    }
                                }
                            })
                        }

                        if (stat_3m_lima == '') {
                            body_int += "<td></td>";
                        } else {
                            body_int += stat_3m_lima;
                        }
                    }

                    // -------------------  3M   --------------------
                    // -------------   SIGN DGM / GM   ----------------
                    if (val1.kategori == "3M") {
                        var stat_gm = 0;
                        $(result.data_approve).each(function(ind17, val17) {
                            if (val17.form_id == val1.ids && val1.remark >= 4) {
                                if (parseInt(val17.dgm_app) >= parseInt(val17.dgm) && parseInt(val17
                                        .gm_app) >= parseInt(val17.gm)) {
                                    body_int +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_gm(" +
                                        val1.ids + ", \"approval_gm\")'>" + (parseInt(val17
                                            .gm_app) + parseInt(val17.dgm_app)) + " / " + (parseInt(
                                            val17.gm) + parseInt(val17.dgm)) +
                                        "</span></center></td>";
                                    stat_gm = 1;
                                } else if (parseInt(val17.dgm_app) == 0 || parseInt(val17.gm_app) ==
                                    0) {
                                    body_int +=
                                        "<td><center><span class='label label-danger' style='cursor:pointer' onclick='sign_form_gm(" +
                                        val1.ids + ", \"approval_gm\")'>" + (parseInt(val17
                                            .gm_app) + parseInt(val17.dgm_app)) + " / " + (parseInt(
                                            val17.gm) + parseInt(val17.dgm)) +
                                        "</span></center></td>";
                                    stat_gm = 1;
                                } else {
                                    body_int +=
                                        "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_gm(" +
                                        val1.ids + ", \"approval_gm\")'>" + (parseInt(val17
                                            .gm_app) + parseInt(val17.dgm_app)) + " / " + (parseInt(
                                            val17.gm) + parseInt(val17.dgm)) +
                                        "</span></center></td>";
                                    stat_gm = 1;
                                }
                            }
                        })

                        if (stat_gm == 0) {
                            body_int += "<td></td>";
                        }
                    } else {
                        var stat_3m_enam = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        if (val1.form_status == '3M Created') {
                            $(result.data_tiga_em_trial).each(function(ind18, val18) {
                                if (val1.form_number == val18.trial_id) {
                                    var stat_gm = 0;
                                    $(result.data_approve).each(function(ind19, val19) {
                                        if (val19.form_id == val18.id_tiga_em && val18
                                            .remark >= 4) {
                                            if (parseInt(val19.dgm_app) >= parseInt(val19
                                                    .dgm) && parseInt(val19.gm_app) >=
                                                parseInt(val19.gm)) {
                                                stat_3m_enam =
                                                    "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_gm(" +
                                                    val18.id_tiga_em +
                                                    ", \"approval_gm\")'>" + (parseInt(val19
                                                        .gm_app) + parseInt(val19
                                                        .dgm_app)) + " / " + (parseInt(val19
                                                        .gm) + parseInt(val19.dgm)) +
                                                    "</span></center></td>";
                                                stat_gm = 1;
                                            } else if (parseInt(val19.dgm_app) < parseInt(
                                                    val19.dgm) || parseInt(val19.gm_app) <
                                                parseInt(val19.gm)) {
                                                stat_3m_enam =
                                                    "<td><center><span class='label label-danger' style='cursor:pointer' onclick='sign_form_gm(" +
                                                    val18.id_tiga_em +
                                                    ", \"approval_gm\")'>" + (parseInt(val19
                                                        .gm_app) + parseInt(val19
                                                        .dgm_app)) + " / " + (parseInt(val19
                                                        .gm) + parseInt(val19.dgm)) +
                                                    "</span></center></td>";
                                                stat_gm = 1;
                                            } else {
                                                stat_3m_enam =
                                                    "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_gm(" +
                                                    val18.id_tiga_em +
                                                    ", \"approval_gm\")'>" + (parseInt(val19
                                                        .gm_app) + parseInt(val19
                                                        .dgm_app)) + " / " + (parseInt(val19
                                                        .gm) + parseInt(val19.dgm)) +
                                                    "</span></center></td>";
                                                stat_gm = 1;
                                            }
                                        }
                                    })
                                }
                            })
                        }

                        if (stat_3m_enam == '') {
                            body_int += "<td></td>";
                        } else {
                            body_int += stat_3m_enam;
                        }
                    }

                    // -------------------  3M   --------------------
                    // -------------   SIGN PRESDIR   ----------------
                    if (val1.kategori == "3M") {
                        var stat_presdir = 0;
                        $(result.data_approve).each(function(ind20, val20) {
                            if (val20.form_id == val1.ids && val1.remark >= 4) {
                                if ((parseInt(val20.gm_app) + parseInt(val20.dgm_app)) >= (parseInt(
                                        val20.gm) + parseInt(val20.dgm)) && val1.remark == 5) {
                                    body_int +=
                                        "<td style='border-right: 3px solid red'><center><span class='label label-danger' style='cursor:pointer' onclick='std_receive(" +
                                        val1.ids + ",\"presdir\")'>Presdir</span></center></td>";

                                    stat_presdir = 1;
                                } else if (val1.remark == 6) {
                                    body_int +=
                                        "<td style='border-right: 3px solid red'><center><span class='label label-success' style='cursor:pointer' onclick='std_receive(" +
                                        val1.ids + ",\"finish\")'>" + val20.dt.replace(/,/g, "") +
                                        "</span></center></td>";
                                    stat_presdir = 1;
                                } else if (val1.remark > 6) {
                                    body_int +=
                                        "<td style='border-right: 3px solid red'><center><span class='label label-success'>" +
                                        val20.dt.replace(/,/g, "") + "</span></center></td>";
                                    stat_presdir = 1;
                                }
                            }
                        })

                        if (stat_presdir == 0) {
                            body_int += "<td style='border-right: 3px solid red'></td>";
                        }
                    } else {
                        var stat_3m_tujuh = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        if (val1.form_status == '3M Created') {
                            $(result.data_tiga_em_trial).each(function(ind21, val21) {
                                if (val1.form_number == val21.trial_id) {
                                    var stat_presdir = 0;

                                    $(result.data_approve).each(function(ind22, val22) {
                                        if (val22.form_id == val21.id_tiga_em && val21
                                            .remark >= 4) {
                                            if ((parseInt(val22.gm_app) + parseInt(val22
                                                    .dgm_app)) >= (parseInt(val22.gm) +
                                                    parseInt(val22.dgm)) && val21.remark ==
                                                5) {
                                                stat_3m_tujuh =
                                                    "<td style='border-right: 3px solid red'><center><span class='label label-danger' style='cursor:pointer' onclick='std_receive(" +
                                                    val1.ids +
                                                    ",\"presdir\")'>Presdir</span></center></td>";

                                                stat_presdir = 1;
                                            } else if (val21.remark == 6) {
                                                stat_3m_tujuh =
                                                    "<td style='border-right: 3px solid red'><center><span class='label label-success' style='cursor:pointer' onclick='std_receive(" +
                                                    val1.ids + ",\"finish\")'>" + val22.dt
                                                    .replace(/,/g, "") +
                                                    "</span></center></td>";

                                                stat_presdir = 1;
                                            } else if (val21.remark > 6) {
                                                stat_3m_tujuh =
                                                    "<td style='border-right: 3px solid red'><center><span class='label label-success'>" +
                                                    val22.dt.replace(/,/g, "") +
                                                    "</span></center></td>";

                                                stat_presdir = 1;
                                            }
                                        }
                                    })
                                }
                            })
                        }

                        if (stat_3m_tujuh == '') {
                            body_int += "<td style='border-right: 3px solid red'></td>";
                        } else {
                            body_int += stat_3m_tujuh;
                        }
                    }

                    // -------------------  3M   --------------------
                    // -------------   IMPLEMENT CHECK   ----------------
                    if (val1.kategori == '3M') {
                        if (val1.remark == 7) {
                            body_int += "<td><center><span class='label label-danger'>" + val1.name +
                                "</span></center></td>";
                        } else if (val1.remark >= 8) {
                            body_int += "<td><center><span class='label label-success'>" + val1.name +
                                "</span></center></td>";
                        } else {
                            body_int += "<td></td>";
                        }
                    } else {
                        var stat_3m_delapan = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        if (val1.form_status == '3M Created') {
                            $(result.data_tiga_em_trial).each(function(ind23, val23) {
                                if (val1.form_number == val23.trial_id) {
                                    if (val23.remark == 7) {
                                        stat_3m_delapan =
                                            "<td><center><span class='label label-danger'>" + val23
                                            .name + "</span></center></td>";
                                    } else if (val23.remark >= 8) {
                                        stat_3m_delapan =
                                            "<td><center><span class='label label-success'>" + val23
                                            .name + "</span></center></td>";
                                    }
                                }
                            })
                        }

                        if (stat_3m_delapan == '') {
                            body_int += "<td></td>";
                        } else {
                            body_int += stat_3m_delapan;
                        }
                    }

                    // -------------------  3M   --------------------
                    // -------------   IMPLEMENT SIGN DEPT   ----------------
                    if (val1.kategori == "3M") {
                        var stat_imp_dpt = 0;
                        $(result.data_sign_imp).each(function(ind24, val24) {
                            if (val24.form_id == val1.ids) {
                                if (val24.dpt > val24.dpt_app) {
                                    body_int +=
                                        "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_imp(" +
                                        val1.ids + ", \"imp_approval\", \""+ val1.form_number+"\", \""+ val1.title+"\")'>" + val24.dpt_app + " / " +
                                        val24.dpt + "</span></center></td>";
                                    stat_imp_dpt = 1;
                                } else {
                                    body_int +=
                                        "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_imp(" +
                                        val1.ids + ", \"imp_approval\", \""+ val1.form_number+"\", \""+ val1.title+"\")'>" + val24.dpt_app + " / " +
                                        val24.dpt + "</span></center></td>";
                                    stat_imp_dpt = 1;
                                }
                            }
                        })

                        if (stat_imp_dpt == 0) {
                            body_int += "<td></td>";
                        }
                    } else {
                        var stat_3m_sembilan = '';
                        // ------------- DARI TRIAL REQUEST ------------
                        if (val1.form_status == '3M Created') {
                            $(result.data_tiga_em_trial).each(function(ind25, val25) {
                                if (val1.form_number == val25.trial_id) {
                                    var stat_imp_dpt = 0;
                                    $(result.data_sign_imp).each(function(ind26, val26) {
                                        if (val26.form_id == val25.id_tiga_em) {

                                            if (val26.dpt > val26.dpt_app) {
                                                stat_3m_sembilan =
                                                    "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_imp(" +
                                                    val25.id_tiga_em +
                                                    ", \"imp_approval\", \""+ val1.form_number+"\", \""+ val1.title+"\")'>" + val26
                                                    .dpt_app + " / " + val26.dpt +
                                                    "</span></center></td>";
                                                stat_imp_dpt = 1;
                                            } else {
                                                stat_3m_sembilan =
                                                    "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_imp(" +
                                                    val25.id_tiga_em +
                                                    ", \"imp_approval\", \""+ val1.form_number+"\", \""+ val1.title+"\")'>" + val26
                                                    .dpt_app + " / " + val26.dpt +
                                                    "</span></center></td>";
                                                stat_imp_dpt = 1;
                                            }
                                        }
                                    })
                                }
                            })
                        }

                        if (stat_3m_sembilan == '') {
                            body_int += "<td></td>";
                        } else {
                            body_int += stat_3m_sembilan;
                        }

                    }

                    // -------------------  3M   --------------------
                    // -------------   IMPLEMENT SIGN DGM GM   ----------------

                    // if (val1.kategori == "3M") {
                    //   var stat_imp_gm = 0;
                    //   $(result.data_sign_imp).each(function(ind27, val27) {
                    //     if (val27.form_id == val1.ids) {
                    //       if (val27.dgm_app == val27.dgm && val27.gm_app == val27.gm) {
                    //         body_int += "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_gm("+val1.ids+", \"imp_approval_up\")'>"+(parseInt(val27.dgm_app) + parseInt(val27.gm_app))+"/"+(parseInt(val27.dgm) + parseInt(val27.gm))+"</span></center></td>";
                    //         stat_imp_gm = 1;
                    //       } else {
                    //         body_int += "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_gm("+val1.ids+", \"imp_approval_up\")'>"+(parseInt(val27.dgm_app) + parseInt(val27.gm_app))+"/"+(parseInt(val27.dgm) + parseInt(val27.gm))+"</span></center></td>";
                    //         stat_imp_gm = 1;
                    //       }
                    //     }
                    //   })

                    //   if (stat_imp_gm == 0) {
                    //     body_int += "<td></td>";
                    //   }
                    // } else {
                    //   var stat_3m_sepuluh = '';
                    //   // ------------- DARI TRIAL REQUEST ------------
                    //   if (val1.form_status == '3M Created') {
                    //     $(result.data_tiga_em_trial).each(function(ind28, val28) {
                    //       if (val1.form_number == val28.trial_id) {
                    //         $(result.data_sign_imp).each(function(ind29, val29) {
                    //           if (val29.form_id == val28.id_tiga_em) {
                    //             if (val29.dgm_app == val29.dgm && val29.gm_app == val29.gm) {
                    //               stat_3m_sepuluh = "<td><center><span class='label label-success' style='cursor:pointer' onclick='sign_form_gm("+val28.id_tiga_em+", \"imp_approval_up\")'>"+(parseInt(val29.dgm_app) + parseInt(val29.gm_app))+"/"+(parseInt(val29.dgm) + parseInt(val29.gm))+"</span></center></td>";
                    //               stat_imp_gm = 1;
                    //             } else {
                    //               stat_3m_sepuluh = "<td><center><span class='label label-warning' style='cursor:pointer' onclick='sign_form_gm("+val28.id_tiga_em+", \"imp_approval_up\")'>"+(parseInt(val29.dgm_app) + parseInt(val29.gm_app))+"/"+(parseInt(val29.dgm) + parseInt(val29.gm))+"</span></center></td>";
                    //               stat_imp_gm = 1;
                    //             }
                    //           }
                    //         })
                    //       }
                    //     })
                    //   }

                    //   if (stat_3m_sepuluh == '') {
                    //     body_int += "<td></td>";
                    //   } else {
                    //     body_int += stat_3m_sepuluh;
                    //   }
                    // }

                    // -------------------  3M   --------------------
                    // -------------   STD RECEIVE   ----------------
                    if (val1.kategori == "3M") {
                        if (val1.remark == 9) {
                            body_int +=
                                "<td style='border-right: 2px solid red'><center><span class='label label-primary'>Waiting</span></center></td>";
                        } else if (val1.remark == 10) {
                            body_int +=
                                "<td style='border-right: 2px solid red'><center><span class='label label-success'>Close</span></center></td>";
                        } else {
                            body_int += "<td style='border-right: 2px solid red'></td>"
                        }
                    } else {
                        var stat_3m_sebelas = '';
                        if (val1.form_status == '3M Created') {
                            $(result.data_tiga_em_trial).each(function(ind30, ind30) {
                                if (val1.form_number == ind30.trial_id) {
                                    if (ind30.remark == 9) {
                                        stat_3m_sebelas =
                                            "<td style='border-right: 2px solid red'><center><span class='label label-primary'>Waiting</span></center></td>";
                                    } else if (ind30.remark == 10) {
                                        stat_3m_sebelas =
                                            "<td style='border-right: 2px solid red'><center><span class='label label-success'>Close</span></center></td>";
                                    }
                                }
                            })
                        }

                        if (stat_3m_sebelas == '') {
                            body_int += "<td style='border-right: 2px solid red'></td>";
                        } else {
                            body_int += stat_3m_sebelas;
                        }
                    }
                })
                $("#body_main").append(body_master);
                $("#body_main").append(body_int);

                tiga_open_arr = [];
                tiga_close_arr = [];
                trial_open_arr = [];
                trial_close_arr = [];
                info_open_arr = [];
                info_close_arr = [];
                sk_open_arr = [];
                sk_close_arr = [];
                ctg = [];

                $(result.data_chart).each(function(index8, value8) {
                    sk_open_arr.push(parseInt(value8.sk_open));
                    sk_close_arr.push(parseInt(value8.sk_close));
                    tiga_open_arr.push(parseInt(value8.tiga_open));
                    tiga_close_arr.push(parseInt(value8.tiga_close));
                    trial_open_arr.push(parseInt(value8.trial_open));
                    trial_close_arr.push(parseInt(value8.trial_close));
                    info_open_arr.push(parseInt(value8.info_open));
                    info_close_arr.push(parseInt(value8.info_close));

                    ctg.push(value8.mon);
                })

                Highcharts.chart('chart', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: '<b>Sakurentsu, 3M, Trial Request Monitoring</b>'
                    },
                    subtitle: {
                        text: '作連通、３M、試作依頼監視',
                        style: {
                            fontSize: '1vw',
                            fontWeight: 'bold'
                        }
                    },
                    xAxis: {
                        type: 'category',
                        categories: ctg,
                        gridLineWidth: 1
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Total Document'
                        },
                        stackLabels: {
                            enabled: true,
                        }
                    },
                    legend: {
                        // reversed: true,
                        itemStyle: {
                            color: "white",
                            fontSize: "12px",
                            fontWeight: "bold",

                        },
                    },
                    tooltip: {
                        headerFormat: '<b>{point.x}</b><br/>',
                        pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                    },
                    credits: {
                        enabled: false
                    },
                    plotOptions: {
                        column: {
                            stacking: 'normal',
                            dataLabels: {
                                enabled: true
                            },
                            animation: false
                        },
                        series: {
                            cursor: 'pointer',
                            point: {
                                events: {
                                    click: function() {
                                        showModalDetail(this.series.name, this.category);
                                    }
                                }
                            }
                        }
                    },
                    series: [{
                            name: 'Sakurentsu Open',
                            data: sk_open_arr,
                            stack: 'Sakurentsu'
                        }, {
                            name: 'Sakurentsu Close',
                            data: sk_close_arr,
                            stack: 'Sakurentsu'
                        },
                        {
                            name: '3M Open',
                            data: tiga_open_arr,
                            stack: '3M'
                        }, {
                            name: '3M Close',
                            data: tiga_close_arr,
                            stack: '3M'
                        }, {
                            name: 'Trial Open',
                            data: trial_open_arr,
                            stack: 'Trial'
                        }, {
                            name: 'Trial Close',
                            data: trial_close_arr,
                            stack: 'Trial'
                        }, {
                            name: 'Information Open',
                            data: info_open_arr,
                            stack: 'Information'
                        }, {
                            name: 'Information Close',
                            data: info_close_arr,
                            stack: 'Information'
                        }
                    ]
                });
            })

        }

        function showModalDetail(name, date) {
            var data = {
                category: name,
                date: date
            }

            $("#bodyDetail").empty();
            body = "";

            $.get('{{ url('fetch/sakurentsu/monitoring/chart_detail/') }}', data, function(result, status, xhr) {
                $("#modal_detail").modal("show");
                $("#head_modal").text(name + " on " + date);

                $(result.details).each(function(index, value) {
                    body += "<tr>";
                    body += "<td>" + value.sakurentsu_number + "</td>";
                    body += "<td>" + value.title_jp + "</td>";
                    body += "<td>" + (value.title || '') + "</td>";
                    body += "<td>" + value.applicant + "</td>";
                    body += "<td>" + value.upload_date + "</td>";
                    body += "<td>" + value.target_date + "</td>";
                    body += "<td>" + (value.pic || '') + "</td>";
                    body += "<td>" + (value.translator || '') + "</td>";

                    var file_arr = JSON.parse(value.file);
                    var file_trans_arr = JSON.parse(value.file_translate);

                    var file = "";
                    var file_trans = "";

                    $(file_arr).each(function(index2, value2) {
                        file += "<a href='" + "{{ url('files/translation/') }}/" +
                            value2 + "' target='_blank'><i class='fa fa-file-pdf-o'></i> </a>";
                    })

                    $(file_trans_arr).each(function(index3, value3) {
                        file_trans += "<a href='" +
                            "{{ url('files/translation/') }}/" + value3 +
                            "' target='_blank'><i class='fa fa-file-pdf-o'></i> </a>";
                    })

                    body += "<td>" + file + "</td>";
                    body += "<td>" + file_trans + "</td>";

                    var stat = "";

                    if (value.status == "created") {
                        stat = value.category + " inprogress";
                    } else if (value.status == "determined") {
                        stat = "PIC Dept";
                    } else if (value.status == "translate") {
                        stat = "Translating";
                    } else if (value.status == 'approval') {
                        stat = "Wait PC Dept";
                    } else {
                        stat = value.status;
                    }

                    body += "<td>" + stat + "</td>";
                    body += "</tr>";
                })

                $("#bodyDetail").append(body);
            })
        }

        $('.datepicker').datepicker({
            format: "yyyy-mm",
            startView: "months",
            minViewMode: "months",
            autoclose: true,
        });


        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

        function doc_list(id_tiga_em, sk_num, title_em) {
            $("#no_judul4").text(': ' + sk_num);
            $("#judul_judul4").text(': ' + title_em);
            var body_file = "";

            $.get('{{ url('fetch/sakurentsu/3m/document/') }}/' + id_tiga_em, function(result, status, xhr) {
                $("#bodyFile").empty();
                $("#sk_num_doc").text('Document Requirement 3M'); 
                if (result.status) {
                    body_f = "";
                    $.each(result.docs, function(key, value) {
                        if (value.file_name) {
                            color = "#4caf50";
                        } else {
                            color = "#fc6d62";
                        }

                        body_file += "<tr style='background-color:" + color + "'>";
                        body_file += "<td>" + value.document_name + "</td>";
                        body_file += "<td>" + value.pic + "</td>";
                        body_file += "<td>" + (value.document_description || '') + "</td>";
                        body_file += "<td>";

                        console.log('{{ $dept_user->department }}');
                        if (value.file_name) {
                            body_file += "<a href='" + "{{ url('uploads/sakurentsu/three_m/doc/') }}/" +
                                value.file_name +
                                "' target='_blank' style='color: #fff'><i class='fa fa-file-pdf-o'></i> " +
                                value.file_name + "</a>";
                        } else if ('{{ $dept_user->department }}' == value.department) {
                            body_file +=
                                "<a class='btn btn-primary btn-xs' href='{{ url('index/sakurentsu/3m/document/upload/') }}/" +
                                id_tiga_em +
                                "' target='_blank'><i class='fa fa-upload'></i> Upload Doc</a>";
                        } else {
                            body_file += "-";
                        }

                        body_file += "</td>";
                        body_file += "</tr>";
                    });

                    $("#bodyFile").append(body_file);

                    $("#modal_doc").modal('show');
                } else {
                    $("#modal_doc").modal('show');
                }
            })
        }

        function sign_form(id_tiga_em, stat, sk_num, title_em) {
            $("#modal_sign").modal('show');
            $("#no_judul5").text(': ' + sk_num);
            $("#judul_judul5").text(': ' + title_em);

            $.get('{{ url('fetch/sakurentsu/3m/dept_sign/') }}/' + id_tiga_em + "/" + stat, function(result, status, xhr) {
                $("#bodyAppr").empty();
                if (result.status) {
                    body_sign = "";

                    $.each(result.data_approve, function(key2, value2) {
                        if (value2.app == 0) {
                            style = "style='background-color:#ff8a8a'";
                            if (~value2.approver_name.indexOf("{{ Auth::user()->name }}")) {
                                var url = '';

                                if (stat == 'approval') {
                                    url = "{{ url('index/sakurentsu/sign') }}/" + id_tiga_em +
                                        "/DEPT APPROVAL";
                                } else {
                                    url = "{{ url('index/sakurentsu/3m/implementation/sign') }}/" +
                                        id_tiga_em + "/IMPLEMENT DEPT";
                                }

                                app = '<a class="btn btn-xs btn-primary" href="' + url +
                                    '" target="_blank"><i class="fa fa-exclamation-circle "></i> To Approval</a>';
                            } else {
                                app = '-';
                            }
                        } else {
                            style = "style='background-color:#4caf50'";
                            app = 'Approved';
                        }

                        body_sign += "<tr " + style + ">";
                        body_sign += "<td>" + value2.approver_department + "</td>";
                        body_sign += "<td>" + value2.approver_name + "</td>";
                        body_sign += "<td>" + app + "</td>";
                        body_sign += "</tr>";
                    })

                    $.each(result.related_department, function(key, value) {
                        stat_dept = 0;
                        $.each(result.data_approve, function(key2, value2) {
                            if (value == value2.approver_department) {
                                stat_dept = 1;
                            }
                        })

                        if (stat_dept == 0) {
                            body_sign += "<tr style='background-color:#ff8a8a'>";
                            body_sign += "<td>" + value + "</td>";
                            body_sign += "<td>-</td>";
                            body_sign += "<td>-</td>";
                            body_sign += "</tr>";
                        }
                    });

                    $("#bodyAppr").append(body_sign);

                } else {
                    $("#modal_sign").modal('show');
                }
            })
        }

        function sign_form_imp(id_tiga_em, stat, sk_num, title_em) {
            $("#modal_sign").modal('show');
            $("#no_judul5").text(': ' + sk_num);
            $("#judul_judul5").text(': ' + title_em);

            $.get('{{ url('fetch/sakurentsu/3m/dept_sign/') }}/' + id_tiga_em + "/" + stat, function(result, status, xhr) {
                $("#bodyAppr").empty();
                if (result.status) {
                    body_sign = "";

                    $.each(result.data_approve, function(key2, value2) {
                        if (value2.app == 0) {
                            style = "style='background-color:#ff8a8a'";
                            if (~value2.approver_name.indexOf("{{ Auth::user()->name }}")) {
                                var url = '';

                                if (stat == 'approval') {
                                    url = "{{ url('index/sakurentsu/sign') }}/" + id_tiga_em +
                                        "/DEPT APPROVAL";
                                } else {
                                    url = "{{ url('index/sakurentsu/3m/implementation/sign') }}/" +
                                        id_tiga_em + "/IMPLEMENT DEPT";
                                }

                                app = '<a class="btn btn-xs btn-primary" href="' + url +
                                    '" target="_blank"><i class="fa fa-exclamation-circle "></i> To Approval</a>';
                            } else {
                                app = '-';
                            }
                        } else {
                            style = "style='background-color:#4caf50'";
                            app = 'Approved';
                        }

                        body_sign += "<tr " + style + ">";
                        body_sign += "<td>" + value2.approver_department + "</td>";
                        body_sign += "<td>" + value2.approver_name + "</td>";
                        body_sign += "<td>" + app + "</td>";
                        body_sign += "</tr>";
                    })

                    $("#bodyAppr").append(body_sign);

                } else {
                    $("#modal_sign").modal('show');
                }
            })
        }

        function sign_form_gm(id_tiga_em, stat) {
            $("#modal_sign_gm").modal('show');

            $.get('{{ url('fetch/sakurentsu/3m/dept_sign/') }}/' + id_tiga_em + "/" + stat, function(result, status, xhr) {
                $("#bodyApprGM").empty();
                if (result.status) {
                    body_sign = "";

                    var apr_ok = [];

                    $.each(result.data_approve, function(key, value) {
                        if (value.approve_at) {
                            apr_ok.push(value.approver_id);
                        }
                    })

                    $.each(result.data_approve, function(key2, value2) {
                        if (!value2.approve_at) {
                            style = "style='background-color:#ff8a8a'";
                            if ("{{ Auth::user()->name }}" == value2.approver_name) {
                                var apps = '';

                                $.each(result.dept_approve, function(key3, value3) {
                                    if (!value3.app) {
                                        apps += value3.approver_department;
                                    }
                                })

                                if (("{{ Auth::user()->username }}".toUpperCase() == 'PI9905001' ||
                                        "{{ Auth::user()->username }}".toUpperCase() == 'PI0109004' ||
                                        "{{ Auth::user()->username }}".toUpperCase() == 'PI1206001') &&
                                    apps == '') {

                                    if ("{{ Auth::user()->username }}".toUpperCase() == 'PI0109004') {
                                        var url = '';

                                        if (stat == 'approval_gm') {
                                            url = "{{ url('index/sakurentsu/sign') }}/" + id_tiga_em +
                                                "/SIGNING DGM";
                                        } else {
                                            url = "{{ url('index/sakurentsu/3m/implementation/sign') }}/" +
                                                id_tiga_em + "/IMPLEMENT DGM";
                                        }

                                        app = '<a class="btn btn-xs btn-primary" href="' + url +
                                            '" target="_blank"><i class="fa fa-exclamation-circle"></i> To Approval</a>';
                                    } else if ("{{ Auth::user()->username }}".toUpperCase() ==
                                        'PI1206001' && jQuery.inArray("PI0109004", apr_ok) !== -1) {
                                        var url = '';

                                        if (stat == 'approval_gm') {
                                            url = "{{ url('index/sakurentsu/sign') }}/" + id_tiga_em +
                                                "/SIGNING GM";
                                        } else {
                                            url = "{{ url('index/sakurentsu/3m/implementation/sign') }}/" +
                                                id_tiga_em + "/IMPLEMENT GM";
                                        }

                                        app = '<a class="btn btn-xs btn-primary" href="' + url +
                                            '" target="_blank"><i class="fa fa-exclamation-circle"></i> To Approval</a>';
                                    } else if ("{{ Auth::user()->username }}".toUpperCase() ==
                                        'PI9905001' && jQuery.inArray("PI1206001", apr_ok) !== -1) {
                                        var url = '';

                                        if (stat == 'approval_gm') {
                                            url = "{{ url('index/sakurentsu/sign') }}/" + id_tiga_em +
                                                "/SIGNING DGM 2";
                                        } else {
                                            url = "{{ url('index/sakurentsu/3m/implementation/sign') }}/" +
                                                id_tiga_em + "/IMPLEMENT DGM";
                                        }

                                        app = '<a class="btn btn-xs btn-primary" href="' + url +
                                            '" target="_blank"><i class="fa fa-exclamation-circle"></i> To Approval</a>';
                                    } else if ("{{ Auth::user()->username }}".toUpperCase() ==
                                        'PI9905001') {
                                        var url = '';

                                        if (stat == 'approval_gm') {
                                            url = "{{ url('index/sakurentsu/sign') }}/" + id_tiga_em +
                                                "/SIGNING DGM 2";
                                        } else {
                                            url = "{{ url('index/sakurentsu/3m/implementation/sign') }}/" +
                                                id_tiga_em + "/IMPLEMENT DGM";
                                        }

                                        app = '<a class="btn btn-xs btn-primary" href="' + url +
                                            '" target="_blank"><i class="fa fa-exclamation-circle"></i> To Approval</a>';
                                    } else {
                                        app = '-';
                                    }

                                } else {
                                    app = '-';
                                }
                            } else {
                                app = '-';
                            }
                        } else {
                            style = "style='background-color:#4caf50'";
                            app = 'Approved';
                        }

                        body_sign += "<tr " + style + ">";
                        body_sign += "<td>" + value2.approver_name + "</td>";
                        body_sign += "<td>" + value2.position + "</td>";
                        body_sign += "<td>" + app + "</td>";
                        body_sign += "</tr>";
                    })

                    $("#bodyApprGM").append(body_sign);

                } else {
                    $("#modal_sign_gm").modal('show');
                }
            })
        }

        function openModalInfo(sk_num, title) {
            $("#title_modal").text(title);

            $.get('{{ url('fetch/sakurentsu/information/detail') }}/' + sk_num, function(result, status, xhr) {

                $("#sign_info").modal('show');

                var sign = result.sign.pic.split(',');
                var body = "";
                $("#bodyInfo").empty();

                $.each(sign, function(key, value) {
                    var receive = 0;
                    $.each(result.tot_sign, function(key2, value2) {
                        if (value == value2.department) {
                            receive = 1;
                            body += "<tr style='background-color:#82f586'>";
                            body += "<td>" + value + "</td>";
                            body += "<td>" + value2.approver_name + "</td>";
                            body += "<td>" + value2.remark + "</td>";
                            body += "</tr>";
                        }
                    })

                    if (receive == 0) {
                        body += "<tr style='background-color:#ff8a8a'>";
                        body += "<td>" + value + "</td>";
                        body += "<td>-</td>";
                        body += "<td>-</td>";
                        body += "</tr>";
                    }
                })

                $("#bodyInfo").append(body);
            })
        }

        function openModalTrial(form_number, title, sk_num, title_trial) {
            $("#title_modal_trial").text(title);
            $("#no_judul2").text(': ' + sk_num);
            $("#judul_judul2").text(': ' + title_trial);

            $.get('{{ url('fetch/sakurentsu/trial_request/approval') }}/' + form_number, function(result, status, xhr) {

                $("#sign_trial").modal('show');

                var body = "";
                $("#bodyTrial").empty();

                var appr = result.detail_approval[0].approval.split("_");
                var approval = result.detail_approval[0].approved.split("_");

                if (appr.length == 4) {
                    var posisi = ['Chief Foreman Issue', 'Manager Issue', 'DGM Issue', 'GM Issue'];
                } else if (appr.length == 5) {
                    var posisi = ['Chief Foreman Issue', 'Manager Issue', 'Manager Mechanical', 'DGM Issue',
                        'GM Issue'
                    ];
                } else if (appr.length == 6) {
                    var posisi = ['Chief Foreman Issue', 'Manager Issue', 'DGM Issue', 'GM Issue', 'DGM 2 Issue',
                        'GM 2 Issue'
                    ];
                } else if (appr.length == 7) {
                    var posisi = ['Chief Foreman Issue', 'Manager Issue', 'Manager Mechanical', 'DGM Issue',
                        'GM Issue', 'DGM 2 Issue', 'GM 2 Issue'
                    ];
                }

                var appr_stat = '-';
                var cls = '';

                $.each(appr, function(key, value) {
                    if (typeof approval[key] !== 'undefined') {
                        if (!approval[key]) {
                            if (value.split("/")[0].toUpperCase() == "{{ Auth::user()->username }}"
                                .toUpperCase()) {
                                var url = "{{ url('index/sakurentsu/trial/sign') }}/" + form_number + "/" +
                                    result.pos_act[0].posisi;
                                appr_stat = '<a class="btn btn-xs btn-primary" href="' + url +
                                    '" target="_blank"><i class="fa fa-check"></i> Approval</a>';
                            } else {
                                appr_stat = '-';
                            }

                            appr_at = '-';

                            cls = 'style="background-color:#ff8a8a"';
                        } else {
                            appr_at = approval[key];
                            appr_stat = '<b>Approved</b>';
                            cls = 'style="background-color:#82f586"';
                        }
                    } else {

                        if (value.split("/")[0].toUpperCase() == "{{ Auth::user()->username }}"
                            .toUpperCase()) {
                            var url = "{{ url('index/sakurentsu/trial/sign') }}/" + form_number + "/" +
                                result.pos_act[0].posisi;
                            appr_stat = '<a class="btn btn-xs btn-primary" href="' + url +
                                '" target="_blank"><i class="fa fa-check"></i> Approval</a>';
                        } else {
                            appr_stat = '-';
                        }

                        appr_at = '-';
                        cls = 'style="background-color:#ff8a8a"';
                    }

                    body += '<tr ' + cls + '>';
                    body += '<td>' + value.split("/")[1] + '</td>';
                    body += '<td>' + appr_at + '</td>';
                    body += '<td>' + appr_stat + '</td>';

                    body += '</tr>';
                })

                $("#bodyTrial").append(body);
            })
        }

        function openModalReceive(id_trial, title, sk_num, title_trial) {
            $("#title_modal_receive").text(title);

            $("#no_judul3").text(': ' + sk_num);
            $("#judul_judul3").text(': ' + title_trial);

            $.get('{{ url('fetch/sakurentsu/trial_request/receive') }}/' + id_trial, function(result, status, xhr) {

                $("#receive_trial").modal('show');

                var body = "";
                $("#bodyReceive").empty();

                $.each(result.detail_approval, function(key, value) {
                    var stat_chief = '';
                    var stat_manager = '';
                    var stat = '';
                    var cls = 'style="background-color:#ff8a8a"';
                    var manager_date = '';
                    var chief_date = '';

                    var manager = value.manager.split("/");
                    if (value.manager_date) {
                        manager_date = '(' + value.manager_date.split(" ")[0] + ')';
                    }

                    var chief = value.chief.split("/");
                    if (value.chief_date) {
                        chief_date = '(' + value.chief_date.split(" ")[0] + ')';
                    }

                    if (value.chief_date) {
                        stat_chief = 'Approved';
                        cls = 'style="background-color:#82f586"';
                    }

                    if (value.manager_date) {
                        stat_manager = 'Approved';
                    }

                    body += '<tr ' + cls + '>';
                    body += '<td>' + value.trial_receive_department + '</td>';
                    body += '<td>' + value.trial_receive_section + '</td>';
                    body += '<td>' + manager[1] + '<br><b>' + stat_manager + '</b><br>' + manager_date +
                        '</td>';
                    body += '<td>' + chief[1] + '<br><b>' + stat_chief + '</b><br>' + chief_date + '</td>';
                    body += '<td>' + (value.perbaikan || '') + '</td>';

                    if (manager[0].toUpperCase() == "{{ Auth::user()->username }}".toUpperCase() && !value
                        .manager_date) {
                        var url = "{{ url('index/sakurentsu/trial/sign') }}/" + id_trial +
                            "/Manager Receiving Trial";
                        body += '<td><a class="btn btn-xs btn-primary" href="' + url +
                            '" target="_blank"><i class="fa fa-exclamation-circle"></i> To Approval</a></td>';
                    } else if (chief[0].toUpperCase() == "{{ Auth::user()->username }}".toUpperCase() && !
                        value.chief_date) {
                        var url = "{{ url('index/sakurentsu/trial/sign') }}/" + id_trial +
                            "/Chief Receiving Trial";
                        body += '<td><a class="btn btn-xs btn-primary" href="' + url +
                            '" target="_blank"><i class="fa fa-exclamation-circle"></i> To Approval</a></td>';
                    } else {
                        body += '<td>-</td>';
                    }

                    body += '</tr>';
                })

                $("#bodyReceive").append(body);
            })
        }

        function openModalResult(id_trial, title, sk_num, title_trial) {
            $("#title_modal_result").text(title);
            $("#no_judul").text(': ' + sk_num);
            $("#judul_judul").text(': ' + title_trial);

            $.get('{{ url('fetch/sakurentsu/trial_request/result') }}/' + id_trial, function(result, status, xhr) {

                $("#result_trial").modal('show');

                var body = "";
                $("#bodyResult").empty();

                $.each(result.detail_approval, function(key, value) {
                    var appr_at = '';
                    var cls = 'style="background-color:#ff8a8a"';
                    var fill_by = value.fill_by.split("/")[1];

                    if (value.trial_method) {
                        cls = 'style="background-color:#82f586"';
                        appr_at = value.fill_at;
                    }

                    body += '<tr ' + cls + '>';
                    body += '<td>' + value.department + '</td>';
                    body += '<td>' + value.section + '</td>';
                    body += '<td>' + fill_by + '</td>';
                    body += '<td>' + (value.trial_method || '') + '</td>';
                    body += '<td>' + (value.trial_result || '') + '</td>';
                    body += '<td>' + appr_at + '</td>';
                    body += '</tr>';
                })

                $("#bodyResult").append(body);
            })
        }

        function openModalNotulen(sk_num) {
            $("#notulen").modal("show");
            $("#input_notulen_sknum").val(sk_num);
        }

        function upload_notulen() {
            if ($("#input_pic").val() == "") {
                openErrorGritter("Error", "Please Fill PIC");
                return false;
            }

            $("#loading").show();

            var formData = new FormData();
            formData.append('sk_num', $("#input_notulen_sknum").val());
            formData.append('file_notulen', $("#input_notulen").prop('files')[0]);
            formData.append('pic', $("#input_pic").val());

            var url = '{{ url('upload/sakurentsu/trial_req/notulen') }}';

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $("#loading").hide();
                    console.log(response);
                    openSuccessGritter('Success', 'Notulen has been Uploaded');
                    location.reload();
                },
                error: function(response) {
                    $("#loading").hide();
                    openErrorGritter('Error', '');
                },
                contentType: false,
                processData: false
            });

        }

        function std_receive(id_tiga_em, stat) {
            window.open("{{ url('detail/sakurentsu/3m/') }}/" + id_tiga_em + "/" + stat, '_blank')
        }

        function refresh_data() {
            $("#sk_num").val("");
            getData();
        }

        Highcharts.createElement('link', {
            href: '{{ url('fonts/UnicaOne.css') }}',
            rel: 'stylesheet',
            type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
            colors: ['#4287f5', '#30e386', '#fced62', '#30e386', '#f45b5b', '#30e386', '#f75dfc', '#30e386',
                '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'
            ],
            chart: {
                backgroundColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 1,
                        y2: 1
                    },
                    stops: [
                        [0, '#2a2a2b'],
                        [1, '#3e3e40']
                    ]
                },
                style: {
                    fontFamily: 'sans-serif'
                },
                plotBorderColor: '#606063'
            },
            title: {
                style: {
                    color: '#E0E0E3',
                    textTransform: 'uppercase',
                    fontSize: '20px'
                }
            },
            subtitle: {
                style: {
                    color: '#E0E0E3',
                    textTransform: 'uppercase'
                }
            },
            xAxis: {
                gridLineColor: '#707073',
                labels: {
                    style: {
                        color: '#E0E0E3'
                    }
                },
                lineColor: '#707073',
                minorGridLineColor: '#505053',
                tickColor: '#707073',
                title: {
                    style: {
                        color: '#A0A0A3'

                    }
                }
            },
            yAxis: {
                gridLineColor: '#707073',
                labels: {
                    style: {
                        color: '#E0E0E3'
                    }
                },
                lineColor: '#707073',
                minorGridLineColor: '#505053',
                tickColor: '#707073',
                tickWidth: 1,
                title: {
                    style: {
                        color: '#A0A0A3'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                style: {
                    color: '#F0F0F0'
                }
            },
            plotOptions: {
                series: {
                    dataLabels: {
                        color: 'white'
                    },
                    marker: {
                        lineColor: '#333'
                    }
                },
                boxplot: {
                    fillColor: '#505053'
                },
                candlestick: {
                    lineColor: 'white'
                },
                errorbar: {
                    color: 'white'
                }
            },
            legend: {
                itemStyle: {
                    color: '#E0E0E3'
                },
                itemHoverStyle: {
                    color: '#FFF'
                },
                itemHiddenStyle: {
                    color: '#606063'
                }
            },
            credits: {
                style: {
                    color: '#666'
                }
            },
            labels: {
                style: {
                    color: '#707073'
                }
            },

            drilldown: {
                activeAxisLabelStyle: {
                    color: '#F0F0F3'
                },
                activeDataLabelStyle: {
                    color: '#F0F0F3'
                }
            },

            navigation: {
                buttonOptions: {
                    symbolStroke: '#DDDDDD',
                    theme: {
                        fill: '#505053'
                    }
                }
            },

            rangeSelector: {
                buttonTheme: {
                    fill: '#505053',
                    stroke: '#000000',
                    style: {
                        color: '#CCC'
                    },
                    states: {
                        hover: {
                            fill: '#707073',
                            stroke: '#000000',
                            style: {
                                color: 'white'
                            }
                        },
                        select: {
                            fill: '#000003',
                            stroke: '#000000',
                            style: {
                                color: 'white'
                            }
                        }
                    }
                },
                inputBoxBorderColor: '#505053',
                inputStyle: {
                    backgroundColor: '#333',
                    color: 'silver'
                },
                labelStyle: {
                    color: 'silver'
                }
            },

            navigator: {
                handles: {
                    backgroundColor: '#666',
                    borderColor: '#AAA'
                },
                outlineColor: '#CCC',
                maskFill: 'rgba(255,255,255,0.1)',
                series: {
                    color: '#7798BF',
                    lineColor: '#A6C7ED'
                },
                xAxis: {
                    gridLineColor: '#505053'
                }
            },

            scrollbar: {
                barBackgroundColor: '#808083',
                barBorderColor: '#808083',
                buttonArrowColor: '#CCC',
                buttonBackgroundColor: '#606063',
                buttonBorderColor: '#606063',
                rifleColor: '#FFF',
                trackBackgroundColor: '#404043',
                trackBorderColor: '#404043'
            },

            legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
            background2: '#505053',
            dataLabelsColor: '#B0B0B3',
            textColor: '#C0C0C0',
            contrastTextColor: '#F0F0F3',
            maskColor: 'rgba(255,255,255,0.3)'
        };
        Highcharts.setOptions(Highcharts.theme);

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
