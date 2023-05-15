@extends('layouts.display')
@section('stylesheets')
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            padding: 2px 5px 2px 5px;
            height: 35px !important;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@endsection
@section('header')
@endsection
@section('content')
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <div>
                <center>
                    <span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12" style="padding-top: 10px;">
                <table style="width: 100%;">
                    <tr>
                        <td width="1%">
                            <div class="info-box" style="background-color: #605ca8; color: white;">
                                <span class="info-box-icon"><i class="fa fa-industry"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size: 18px; font-weight: bold;">TOTAL</span>
                                    <span class="info-box-number" id="total">9999</span>
                                </div>
                            </div>
                        </td>
                        <td width="1%" style="padding-left: 20px;">
                            <div class="info-box" style="background-color: #00a65a; color: white;">
                                <span class="info-box-icon"><i class="fa fa-chain"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size: 18px; font-weight: bold;">YMPI</span>
                                    <span class="info-box-number" id="total_ympi">9999</span>
                                </div>
                            </div>
                        </td>
                        <td width="2%">
                            <div class="info-box" style="background-color: #00a65a; color: white;">
                                <span class="info-box-icon"><i class="fa fa-male"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size: 18px; font-weight: bold;">MALE</span>
                                    <span class="info-box-number" id="total_ympi_male">9999</span>
                                </div>
                            </div>
                        </td>
                        <td width="2%">
                            <div class="info-box" style="background-color: #00a65a; color: white;">
                                <span class="info-box-icon"><i class="fa fa-female"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size: 18px; font-weight: bold;">FEMALE</span>
                                    <span class="info-box-number" id="total_ympi_female">9999</span>
                                </div>
                            </div>
                        </td>
                        <td width="1%" style="padding-left: 20px;">
                            <div class="info-box" style="background-color: #ef9a15; color: white;">
                                <span class="info-box-icon"><i class="fa fa-chain-broken"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size: 18px; font-weight: bold;">OUTSOURCE</span>
                                    <span class="info-box-number" id="total_outsource">9999</span>
                                </div>
                            </div>
                        </td>
                        <td width="2%">
                            <div class="info-box" style="background-color: #ef9a15; color: white;">
                                <span class="info-box-icon"><i class="fa fa-male"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size: 18px; font-weight: bold;">MALE</span>
                                    <span class="info-box-number" id="total_outsource_male">9999</span>
                                </div>
                            </div>
                        </td>
                        <td width="2%">
                            <div class="info-box" style="background-color: #ef9a15; color: white;">
                                <span class="info-box-icon"><i class="fa fa-female"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text" style="font-size: 18px; font-weight: bold;">FEMALE</span>
                                    <span class="info-box-number" id="total_outsource_female">9999</span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-2">
                        <div id="byJobStatus"></div>
                    </div>
                    <div class="col-xs-3">
                        <div id="byUnion"></div>
                    </div>
                    <div class="col-xs-2">
                        <div id="byStatus"></div>
                    </div>
                    <div class="col-xs-5">
                        <div id="byStatusDepartment"></div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-4">
                        <div id="byAgeStatus"></div>
                    </div>
                    <div class="col-xs-8">
                        <div id="byAgePosition"></div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div id="byAgeDepartment"></div>
            </div>
            <div class="col-xs-12">
                <div id="byAgeGrade"></div>
            </div>
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-4">
                        <div id="byPosition"></div>
                    </div>
                    <div class="col-xs-5">
                        <div id="byGrade"></div>
                    </div>
                    <div class="col-xs-3">
                        <div id="byContract"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row">
                        <div class="col-xs-12">
                            <table id="tableDetail" class="table table-bordered table-striped table-hover"
                                style="font-size: 12px;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 0.1%; text-align: center;">#</th>
                                        <th style="width: 0.1%; text-align: left;">ID</th>
                                        <th style="width: 1%; text-align: left;">Nama</th>
                                        <th style="width: 0.1%; text-align: center;">Usia</th>
                                        <th style="width: 2%; text-align: left;">Dept</th>
                                        <th style="width: 0.1%; text-align: center;">Masuk</th>
                                        <th style="width: 0.1%; text-align: center;">Masa Kerja</th>
                                        <th style="width: 0.1%; text-align: left;">Grade</th>
                                        <th style="width: 0.1%; text-align: left;">Posisi</th>
                                        <th style="width: 0.1%; text-align: left;">Status</th>
                                        <th style="width: 0.1%; text-align: left;">Pekerjaan</th>
                                        <th style="width: 0.1%; text-align: left;">Serikat</th>
                                    </tr>
                                </thead>
                                <tbody id="tableDetailBody">
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
    <script src="{{ url('js/highcharts.js') }}"></script>
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
            fetchData();
        });

        var employees = [];

        function modalByJobStatus(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.job_status == category && value.employment_status == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByUnion(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.union_name == category && value.employment_status == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByUnionPie(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.union_name == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByStatus(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.employment_status == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByStatusDepartment(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.department_shortname == category && value.employment_status == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByAgeStatus(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.age_category == category && value.employment_status == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByAgePosition(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.position_category == category && value.age_category == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByAgeDepartment(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.department_shortname == category && value.age_category == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByAgeGrade(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.grade_code == category && value.age_category == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByPosition(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.position_category == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByGrade(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.grade_code == name) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalByContract(category, name) {
            console.log(category);
            console.log(name);
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.work_length_category == name && value.employment_status == 'CONTRACT') {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.employee_name + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + Math.floor(value
                            .age_month / 12) +
                        '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.hire_date +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + value.work_length_string +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.grade_code + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function dataTable() {
            var table = $('#tableDetail').DataTable({
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
                'pageLength': 20,
                'searching': true,
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
            });
        }

        function fetchData() {
            var data = {

            }
            $.get('{{ url('fetch/manpower/information_management') }}', data, function(result, status, xhr) {
                if (result.status) {
                    employees = result.employees;

                    function SortByOrder(a, b) {
                        var aOrder = a.order;
                        var bOrder = b.order;
                        return ((aOrder < bOrder) ? -1 : ((aOrder > bOrder) ? 1 : 0));
                    }

                    function SortByCategory(a, b) {
                        var aCategory = a.category.toLowerCase();
                        var bCategory = b.category.toLowerCase();
                        return ((aCategory < bCategory) ? -1 : ((aCategory > bCategory) ? 1 : 0));
                    }

                    function SortByCount(a, b) {
                        var aCount = a.count;
                        var bCount = b.count;
                        return ((aCount > bCount) ? -1 : ((aCount < bCount) ? 1 : 0));
                    }

                    function SortByAgeCategory(a, b) {
                        var aAge = a.age_category;
                        var bAge = b.age_category;
                        return ((aAge < bAge) ? -1 : ((aAge > bAge) ? 1 : 0));
                    }

                    var total = 0;
                    var total_ympi = 0;
                    var total_ympi_male = 0;
                    var total_ympi_female = 0;
                    var total_outsource = 0;
                    var total_outsource_male = 0;
                    var total_outsource_female = 0;

                    $.each(employees, function(key, value) {
                        total += 1;
                        if (value.employment_status == 'OUTSOURCING') {
                            total_outsource += 1;
                            if (value.gender == 'L') {
                                total_outsource_male += 1;
                            } else {
                                total_outsource_female += 1;
                            }
                        } else {
                            total_ympi += 1;
                            if (value.gender == 'L') {
                                total_ympi_male += 1;
                            } else {
                                total_ympi_female += 1;
                            }
                        }
                    });

                    $('#total').html('<span style="font-size: 30px;">' + total + '</span>');
                    $('#total_ympi').html('<span style="font-size: 30px;">' + total_ympi + '</span>');
                    $('#total_ympi_male').html('<span style="font-size: 30px;">' + total_ympi_male +
                        '</span><span style="font-size: 16px;"> (' + (
                            total_ympi_male / total_ympi * 100)
                        .toFixed(0) +
                        '%)</span>');
                    $('#total_ympi_female').html('<span style="font-size: 30px;">' + total_ympi_female +
                        '</span><span style="font-size: 16px;"> (' + (
                            total_ympi_female / total_ympi * 100)
                        .toFixed(0) + '%)</span>');
                    $('#total_outsource').html('<span style="font-size: 30px;">' + total_outsource);
                    $('#total_outsource_male').html('<span style="font-size: 30px;">' + total_outsource_male +
                        '</span><span style="font-size: 16px;"> (' + (
                            total_outsource_male /
                            total_outsource * 100).toFixed(0) + '%)</span>');
                    $('#total_outsource_female').html('<span style="font-size: 30px;">' + total_outsource_female +
                        '</span><span style="font-size: 16px;"> (' +
                        (total_outsource_female /
                            total_outsource * 100).toFixed(0) + '%)</span>');

                    var array_byposition = result.employees;
                    var result_byposition = [];
                    var data_byposition = [];

                    array_byposition.reduce(function(res, value) {
                        if (!res[value.position_category]) {
                            res[value.position_category] = {
                                category: value.position_category,
                                order: parseInt(value.position_order),
                                count: 0
                            };
                            result_byposition.push(res[value.position_category]);
                        }
                        res[value.position_category].count += 1;
                        return res;
                    }, {});

                    result_byposition.sort(SortByOrder);

                    $.each(result_byposition, function(key, value) {
                        if (jQuery.inArray(value.category, ['Sub Leader', 'Leader', 'Chief/Foreman',
                                'Manager', 'DGM', 'GM', 'Director', 'Presdir'
                            ]) !== -1) {
                            data_byposition.push([value.category, value.count]);
                        }
                    });

                    Highcharts.chart('byPosition', {
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Position (役職別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            labels: {
                                enabled: false,
                            },
                            title: {
                                text: null,
                            },
                        },
                        xAxis: {
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none'
                                },
                                formatter: function() {
                                    return this.value.toString().substring(0, 8);
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalByPosition(this.category, this.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Total',
                            type: 'column',
                            colorByPoint: false,
                            showInLegend: false,
                            data: data_byposition,
                            color: '#f39c12',
                        }]
                    });

                    var array_bygrade = result.employees;
                    var result_bygrade = [];
                    var data_bygrade = [];

                    array_bygrade.reduce(function(res, value) {
                        if (!res[value.grade_code]) {
                            res[value.grade_code] = {
                                category: value.grade_code,
                                order: parseInt(value.grade_code_order),
                                count: 0
                            };
                            result_bygrade.push(res[value.grade_code]);
                        }
                        res[value.grade_code].count += 1;
                        return res;
                    }, {});

                    result_bygrade.sort(SortByOrder);

                    $.each(result_bygrade, function(key, value) {
                        data_bygrade.push([value.category, value.count]);
                    });

                    Highcharts.chart('byGrade', {
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Grade (等級別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            labels: {
                                enabled: false,
                            },
                            title: {
                                text: null,
                            },
                        },
                        xAxis: {
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalByGrade(this.category, this.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Total',
                            type: 'column',
                            colorByPoint: false,
                            showInLegend: false,
                            data: data_bygrade,
                            color: '#f39c12',
                        }]
                    });

                    var array_bycontract = result.employees;
                    var result_bycontract = [];
                    var data_bycontract = [];

                    array_bycontract.reduce(function(res, value) {
                        if (!res[value.work_length_category]) {
                            res[value.work_length_category] = {
                                category: value.work_length_category,
                                count: 0
                            };
                            result_bycontract.push(res[value.work_length_category]);
                        }
                        if (value.employment_status == 'CONTRACT') {
                            res[value.work_length_category].count += 1;
                        }
                        return res;
                    }, {});

                    result_bycontract.sort(SortByCategory);

                    $.each(result_bycontract, function(key, value) {
                        data_bycontract.push([value.category, value.count]);
                    });
                    Highcharts.chart('byContract', {
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Contract (契約期間別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            labels: {
                                enabled: false,
                            },
                            title: {
                                text: null,
                            },
                        },
                        xAxis: {
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalByContract(this.category, this.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Total',
                            type: 'column',
                            colorByPoint: false,
                            showInLegend: false,
                            data: data_bycontract,
                            color: '#f39c12',
                        }]
                    });

                    var array_byjobstatus = result.employees;
                    var result_byjobstatus = [];
                    var category_byjobstatus = [];
                    var serie_byjobstatus = [];
                    var name_byjobstatus = [];

                    array_byjobstatus.reduce(function(res, value) {
                        if (!res[value.job_status + value.employment_status]) {
                            res[value.job_status + value.employment_status] = {
                                category: value.job_status,
                                employment_status: value.employment_status,
                                count: 0
                            };
                            result_byjobstatus.push(res[value.job_status + value.employment_status]);
                        }
                        res[value.job_status + value.employment_status].count += 1;
                        return res;
                    }, {});

                    result_byjobstatus.sort(SortByCategory);

                    $.each(result_byjobstatus, function(key, value) {
                        if (value.employment_status != 'OUTSOURCING') {
                            if (category_byjobstatus.indexOf(value.category) === -1) {
                                category_byjobstatus.push(value.category);
                            }
                            if (name_byjobstatus.indexOf(value.employment_status) !== -1) {
                                serie_byjobstatus[name_byjobstatus.indexOf(value.employment_status)].data
                                    .push(
                                        value.count
                                    )
                            } else {
                                name_byjobstatus.push(value.employment_status)
                                serie_byjobstatus.push({
                                    name: value.employment_status,
                                    data: [value.count]
                                })
                            }
                        }
                    });

                    Highcharts.chart('byJobStatus', {
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Job Status (雇用形態別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            labels: {
                                enabled: false,
                            },
                            title: {
                                text: null,
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    color: (
                                        Highcharts.defaultOptions.title.style &&
                                        Highcharts.defaultOptions.title.style.color
                                    ) || 'gray',
                                    textOutline: 'none'
                                }
                            }
                        },
                        xAxis: {
                            categories: category_byjobstatus,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                enabled: true,
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none'
                                }
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none',
                                    color: (
                                        Highcharts.defaultOptions.title.style &&
                                        Highcharts.defaultOptions.title.style.color
                                    ) || 'gray',
                                    textOutline: 'none'
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        fontSize: '12px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalByJobStatus(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: serie_byjobstatus
                    });

                    var array_byunion = result.employees;
                    var result_byunion = [];
                    var category_byunion = [];
                    var serie_byunion = [];
                    var name_byunion = [];

                    array_byunion.reduce(function(res, value) {
                        if (!res[value.union_name + value.employment_status]) {
                            res[value.union_name + value.employment_status] = {
                                category: value.union_name,
                                employment_status: value.employment_status,
                                count: 0
                            };
                            result_byunion.push(res[value.union_name + value.employment_status]);
                        }
                        res[value.union_name + value.employment_status].count += 1;
                        return res;
                    }, {});

                    result_byunion.sort(SortByCategory);

                    $.each(result_byunion, function(key, value) {
                        if (value.employment_status != 'OUTSOURCING') {
                            if (category_byunion.indexOf(value.category) === -1) {
                                category_byunion.push(value.category);
                            }
                            if (name_byunion.indexOf(value.employment_status) !== -1) {
                                serie_byunion[name_byunion.indexOf(value.employment_status)].data
                                    .push(
                                        value.count
                                    )
                            } else {
                                name_byunion.push(value.employment_status)
                                serie_byunion.push({
                                    type: 'column',
                                    name: value.employment_status,
                                    data: [value.count]
                                })
                            }
                        }
                    });

                    var array_byunionpie = result.employees;
                    var result_byunionpie = [];
                    var data_byunionpie = [];

                    array_byunionpie.reduce(function(res, value) {
                        if (!res[value.union_name]) {
                            res[value.union_name] = {
                                category: value.union_name,
                                count: 0
                            };
                            result_byunionpie.push(res[value.union_name]);
                        }
                        if (value.employment_status != 'OUTSOURCING') {
                            res[value.union_name].count += 1;
                        }
                        return res;
                    }, {});

                    result_byunionpie.sort(SortByCategory);

                    $.each(result_byunionpie, function(key, value) {
                        if (value.employment_status != 'OUTSOURCING') {
                            data_byunionpie.push({
                                name: value.category,
                                y: value.count
                            });
                        }
                    });

                    serie_byunion.push({
                        type: 'pie',
                        name: 'Total',
                        borderWidth: 0,
                        data: data_byunionpie,
                        center: [230, 40],
                        size: 140,
                        showInLegend: false,
                        dataLabels: {
                            distance: -30,
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.percentage:.1f}%',
                        },
                        cursor: 'pointer',
                        point: {
                            events: {
                                click: function() {
                                    modalByUnionPie(this.category, this.name);
                                }
                            }
                        }
                    });

                    Highcharts.chart('byUnion', {
                        chart: {
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Union (労働組合別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            labels: {
                                enabled: false,
                            },
                            title: {
                                text: null,
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    color: (
                                        Highcharts.defaultOptions.title.style &&
                                        Highcharts.defaultOptions.title.style.color
                                    ) || 'gray',
                                    textOutline: 'none'
                                }
                            }
                        },
                        xAxis: {
                            categories: category_byunion,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                enabled: true,
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none'
                                }
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none',
                                    color: (
                                        Highcharts.defaultOptions.title.style &&
                                        Highcharts.defaultOptions.title.style.color
                                    ) || 'gray',
                                    textOutline: 'none'
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        fontSize: '12px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalByUnion(this.category, this.series.name);
                                        }
                                    }
                                }
                            },
                        },
                        series: serie_byunion
                    });

                    var array_bystatus = result.employees;
                    var result_bystatus = [];
                    var data_bystatus = [];

                    array_bystatus.reduce(function(res, value) {
                        if (!res[value.employment_status]) {
                            res[value.employment_status] = {
                                category: value.employment_status,
                                count: 0
                            };
                            result_bystatus.push(res[value.employment_status]);
                        }
                        res[value.employment_status].count += 1;
                        return res;
                    }, {});

                    result_bystatus.sort(SortByCategory);

                    $.each(result_bystatus, function(key, value) {
                        data_bystatus.push([value.category, value.count]);
                    });

                    Highcharts.chart('byStatus', {
                        chart: {
                            type: 'pie',
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Status (形態別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        tooltip: {
                            pointFormat: '{series.name}: <b>{point.y} ({point.percentage:.1f}%)</b>'
                        },
                        plotOptions: {
                            pie: {
                                borderWidth: 0,
                                allowPointSelect: true,
                                cursor: 'pointer',
                                dataLabels: {
                                    enabled: true,
                                    format: '<b>{point.name}</b><br>{point.y} ({point.percentage:.1f}%)',
                                    distance: -50,
                                    filter: {
                                        property: 'percentage',
                                        operator: '>',
                                        value: 4
                                    }
                                },
                                point: {
                                    events: {
                                        click: function() {
                                            modalByStatus(this.category, this.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Orang',
                            colorByPoint: true,
                            showInLegend: true,
                            data: data_bystatus,
                        }]
                    });

                    var array_bystatusdepartment = result.employees;
                    var result_bystatusdepartment = [];
                    var category_bystatusdepartment = [];
                    var serie_bystatusdepartment_permanent = [];
                    var serie_bystatusdepartment_contract = [];
                    var serie_bystatusdepartment_permanent_percentage = [];
                    var serie_bystatusdepartment_contract_percentage = [];

                    array_bystatusdepartment.reduce(function(res, value) {
                        if (!res[value.department_shortname]) {
                            res[value.department_shortname] = {
                                category: value.department_shortname,
                                count: 0,
                                permanent: 0,
                                contract: 0,
                                permanent_percentage: 0,
                                contract_percentage: 0,
                            };
                            result_bystatusdepartment.push(res[value.department_shortname]);
                        }
                        if (value.employment_status != 'OUTSOURCING') {
                            res[value.department_shortname].count += 1;
                        }
                        if (value.employment_status == 'PERMANENT') {
                            res[value.department_shortname].permanent += 1;
                        }
                        if (value.employment_status == 'CONTRACT') {
                            res[value.department_shortname].contract += 1;
                        }
                        res[value.department_shortname].permanent_percentage = parseFloat((res[value
                                .department_shortname].permanent / res[value.department_shortname]
                            .count *
                            100).toFixed(1));
                        res[value.department_shortname].contract_percentage = parseFloat((res[value
                                .department_shortname].contract / res[value.department_shortname]
                            .count *
                            100).toFixed(1));
                        return res;
                    }, {});

                    result_bystatusdepartment.sort(SortByCount);

                    $.each(result_bystatusdepartment, function(key, value) {
                        if (category_bystatusdepartment.indexOf(value.category) === -1) {
                            category_bystatusdepartment.push(value.category);
                        }
                        serie_bystatusdepartment_permanent.push(value.permanent);
                        serie_bystatusdepartment_contract.push(value.contract);
                        serie_bystatusdepartment_permanent_percentage.push(value.permanent_percentage);
                        serie_bystatusdepartment_contract_percentage.push(value.contract_percentage);
                    });

                    Highcharts.chart('byStatusDepartment', {
                        chart: {
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Status & Department (雇用形態別と課別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: [{
                            title: {
                                text: 'Orang',
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    color: (
                                        Highcharts.defaultOptions.title.style &&
                                        Highcharts.defaultOptions.title.style.color
                                    ) || 'gray',
                                    textOutline: 'none'
                                }
                            }
                        }, {
                            title: {
                                text: 'Percent',
                            },
                            max: 105,
                            opposite: true
                        }],
                        xAxis: {
                            categories: category_bystatusdepartment,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                enabled: true,
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none'
                                }
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none',
                                    color: (
                                        Highcharts.defaultOptions.title.style &&
                                        Highcharts.defaultOptions.title.style.color
                                    ) || 'gray',
                                    textOutline: 'none'
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        tooltip: {
                            shared: true
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        fontSize: '12px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalByStatusDepartment(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'PERMANENT',
                            type: 'column',
                            yAxis: 0,
                            data: serie_bystatusdepartment_permanent
                        }, {
                            name: 'CONTRACT',
                            type: 'column',
                            yAxis: 0,
                            data: serie_bystatusdepartment_contract
                        }, {
                            name: 'PERMANENT %',
                            type: 'spline',
                            yAxis: 1,
                            data: serie_bystatusdepartment_permanent_percentage,
                            color: '#ff4c50',
                        }, {
                            name: 'CONTRACT %',
                            type: 'spline',
                            yAxis: 1,
                            data: serie_bystatusdepartment_contract_percentage,
                            color: '#f39c12',
                        }]
                    });

                    var array_byagestatus = result.employees;
                    var result_byagestatus = [];
                    var category_byagestatus = [];
                    var serie_byagestatus_permanent = [];
                    var serie_byagestatus_contract = [];

                    array_byagestatus.reduce(function(res, value) {
                        if (!res[value.age_category]) {
                            res[value.age_category] = {
                                category: value.age_category,
                                count_permanent: 0,
                                count_contract: 0
                            };
                            result_byagestatus.push(res[value.age_category]);
                        }
                        if (value.employment_status == 'PERMANENT') {
                            res[value.age_category].count_permanent += 1;
                        }
                        if (value.employment_status == 'CONTRACT') {
                            res[value.age_category].count_contract += 1;
                        }
                        return res;
                    }, {});

                    result_byagestatus.sort(SortByCategory);

                    $.each(result_byagestatus, function(key, value) {
                        category_byagestatus.push(value.category);
                        serie_byagestatus_permanent.push(value.count_permanent);
                        serie_byagestatus_contract.push(value.count_contract);
                    });

                    Highcharts.chart('byAgeStatus', {
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Age & Status (年齢別と雇用形態別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            labels: {
                                enabled: false,
                            },
                            title: {
                                text: null,
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    color: (
                                        Highcharts.defaultOptions.title.style &&
                                        Highcharts.defaultOptions.title.style.color
                                    ) || 'gray',
                                    textOutline: 'none'
                                }
                            }
                        },
                        xAxis: {
                            categories: category_byagestatus,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                enabled: true,
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none'
                                }
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none',
                                    color: (
                                        Highcharts.defaultOptions.title.style &&
                                        Highcharts.defaultOptions.title.style.color
                                    ) || 'gray',
                                    textOutline: 'none'
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        fontSize: '12px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalByAgeStatus(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'PERMANENT',
                            data: serie_byagestatus_permanent,
                        }, {
                            name: 'CONTRACT',
                            data: serie_byagestatus_contract,
                        }]
                    });

                    var array_byageposition = result.employees;
                    var result_byageposition = [];
                    var position_category = ['Sub Leader', 'Leader', 'Chief/Foreman', 'Manager', 'DGM', 'GM',
                        'Director', 'Presdir'
                    ];
                    var grade_category = ['C1', 'C2', 'E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'L1', 'L2',
                        'L3', 'L4', 'M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'M7', 'M8', 'D1', 'D1', 'D3'
                    ];
                    var age_category = ['56+', '51-55', '46-50', '41-45', '36-40', '31-35', '26-30', '21-25',
                        '18-20'
                    ];
                    var data_byageposition = [];
                    var name_byageposition = [];
                    var serie_byageposition = [];

                    array_byageposition.reduce(function(res, value) {
                        if (!res[value.age_category + value.position_category]) {
                            res[value.age_category + value.position_category] = {
                                position_category: value.position_category,
                                age_category: value.age_category,
                                order: parseInt(value.position_order),
                                count: 0
                            };
                            result_byageposition.push(res[value.age_category + value.position_category]);
                        }
                        res[value.age_category + value.position_category].count += 1;
                        return res;
                    }, {});

                    result_byageposition.sort(SortByOrder);

                    for (let i = 0; i < position_category.length; i++) {
                        var val1 = position_category[i];

                        for (let j = 0; j < age_category.length; j++) {
                            var val2 = age_category[j];
                            var val3 = 0;

                            $.each(result_byageposition, function(key, value) {
                                if (value.position_category == val1 && value.age_category == val2) {
                                    val3 = value.count;
                                }
                            });

                            data_byageposition.push({
                                position_category: val1,
                                age_category: val2,
                                count: val3,
                            });
                        }
                    }

                    $.each(data_byageposition, function(key, value) {
                        if (name_byageposition.indexOf(value.age_category) !== -1) {
                            serie_byageposition[name_byageposition.indexOf(value.age_category)].data
                                .push(
                                    value.count
                                )
                        } else {
                            name_byageposition.push(value.age_category)
                            serie_byageposition.push({
                                name: value.age_category,
                                data: [value.count]
                            })
                        }
                    });

                    Highcharts.chart('byAgePosition', {
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Age & Position (年齢別と役職別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            labels: {
                                enabled: false,
                            },
                            title: {
                                text: null,
                            },
                        },
                        xAxis: {
                            categories: position_category,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.99,
                                borderWidth: 0,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalByAgePosition(this.category, this.series.name);
                                        }
                                    }
                                }
                            },
                        },
                        series: serie_byageposition
                    });

                    var array_byagegrade = result.employees;
                    var result_byagegrade = [];
                    var grade_category = ['C1', 'C2', 'E1', 'E2', 'E3', 'E4', 'E5', 'E6', 'E7', 'E8', 'L1', 'L2',
                        'L3', 'L4', 'M1', 'M2', 'M3', 'M4', 'M5', 'M6', 'M7', 'M8', 'D1', 'D1', 'D3'
                    ];
                    var data_byagegrade = [];
                    var name_byagegrade = [];
                    var serie_byagegrade = [];

                    array_byagegrade.reduce(function(res, value) {
                        if (!res[value.age_category + value.grade_code]) {
                            res[value.age_category + value.grade_code] = {
                                grade_category: value.grade_code,
                                age_category: value.age_category,
                                order: parseInt(value.grade_code_order),
                                count: 0
                            };
                            result_byagegrade.push(res[value.age_category + value.grade_code]);
                        }
                        res[value.age_category + value.grade_code].count += 1;
                        return res;
                    }, {});

                    result_byagegrade.sort(SortByOrder);

                    for (let i = 0; i < grade_category.length; i++) {
                        var val1 = grade_category[i];

                        for (let j = 0; j < age_category.length; j++) {
                            var val2 = age_category[j];
                            var val3 = 0;

                            $.each(result_byagegrade, function(key, value) {
                                if (value.grade_category == val1 && value.age_category == val2) {
                                    val3 = value.count;
                                }
                            });

                            data_byagegrade.push({
                                grade_category: val1,
                                age_category: val2,
                                count: val3,
                            });
                        }
                    }

                    $.each(data_byagegrade, function(key, value) {
                        if (name_byagegrade.indexOf(value.age_category) !== -1) {
                            serie_byagegrade[name_byagegrade.indexOf(value.age_category)].data
                                .push(
                                    value.count
                                )
                        } else {
                            name_byagegrade.push(value.age_category)
                            serie_byagegrade.push({
                                name: value.age_category,
                                data: [value.count]
                            })
                        }
                    });

                    Highcharts.chart('byAgeGrade', {
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Age & Grade (年齢別と等級別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            type: 'logarithmic',
                            labels: {
                                enabled: true,
                            },
                            title: {
                                text: null,
                            },
                        },
                        xAxis: {
                            categories: grade_category,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.99,
                                borderWidth: 0,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalByAgeGrade(this.category, this.series.name);
                                        }
                                    }
                                }
                            },
                        },
                        series: serie_byagegrade
                    });

                    var array_byagedepartment = result.employees;
                    var result_byagedepartment = [];
                    var data_byagedepartment = [];
                    var name_byagedepartment = [];
                    var serie_byagedepartment = [];


                    array_byagedepartment.reduce(function(res, value) {
                        if (!res[value.age_category + value.department_shortname]) {
                            res[value.age_category + value.department_shortname] = {
                                department_category: value.department_shortname,
                                age_category: value.age_category,
                                count: 0
                            };
                            result_byagedepartment.push(res[value.age_category + value
                                .department_shortname]);
                        }
                        res[value.age_category + value.department_shortname].count += 1;
                        return res;
                    }, {});

                    for (let i = 0; i < category_bystatusdepartment.length; i++) {
                        var val1 = category_bystatusdepartment[i];

                        for (let j = 0; j < age_category.length; j++) {
                            var val2 = age_category[j];
                            var val3 = 0;

                            $.each(result_byagedepartment, function(key, value) {
                                if (value.department_category == val1 && value.age_category ==
                                    val2) {
                                    val3 = value.count;
                                }
                            });

                            data_byagedepartment.push({
                                category_bystatusdepartment: val1,
                                age_category: val2,
                                count: val3,
                            });
                        }
                    }

                    $.each(data_byagedepartment, function(key, value) {
                        if (name_byagedepartment.indexOf(value.age_category) !== -1) {
                            serie_byagedepartment[name_byagedepartment.indexOf(value.age_category)].data
                                .push(
                                    value.count
                                )
                        } else {
                            name_byagedepartment.push(value.age_category)
                            serie_byagedepartment.push({
                                name: value.age_category,
                                data: [value.count]
                            })
                        }
                    });

                    Highcharts.chart('byAgeDepartment', {
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: 'By Age & Department (年齢別と課別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            type: 'logarithmic',
                            labels: {
                                enabled: true,
                            },
                            title: {
                                text: null,
                            },
                        },
                        xAxis: {
                            categories: category_bystatusdepartment,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '12px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.99,
                                borderWidth: 0,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalByAgeDepartment(this.category, this.series.name);
                                        }
                                    }
                                }
                            },
                        },
                        series: serie_byagedepartment
                    });

                } else {
                    alert(result.message);
                    return false;
                }
            });
        }

        Highcharts.createElement('link', {
            href: '{{ url('fonts/UnicaOne.css') }}',
            rel: 'stylesheet',
            type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
            colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
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
    </script>
@endsection
