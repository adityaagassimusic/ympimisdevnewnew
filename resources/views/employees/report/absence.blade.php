@extends('layouts.display')
@section('stylesheets')

    <style type="text/css">
        table>thead>tr>th {
            text-align: center;
            vertical-align: middle;
        }

        table>tfoot>tr>th {
            text-align: center;
        }

        table>tbody>tr>td {
            padding-left: 2px;
            padding-right: 2px;
        }

        #tableResume>tbody>tr>td {
            color: #f39c12;
            background-color: #524a4e;
        }

        #tableDepartment>tbody>tr>td {
            color: #f39c12;
            background-color: #524a4e;
        }

        .dataTables_info {
            color: #f39c12;
        }

        .dataTables_filter {
            color: #f39c12;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
    </section>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: white; top: 45%; left: 50%;">
                <span style="font-size: 40px"><i class="fa fa-spinner fa-spin" id="loadingDetail"
                        style="font-size: 80px;"></i></span>
            </p>
        </div>
        <div class="row">
            <div id="period_title" class="col-xs-9" style="background-color: rgba(248,161,63,0.9);">
                <center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center>
            </div>
            <div class="col-xs-3">
                <div class="input-group date">
                    <div class="input-group-addon" style="background-color: rgba(248,161,63,0.9);">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="datepicker" name="datepicker"
                        onchange="fetchData(value)">
                </div>
            </div>
            <div class="col-md-6" style="margin-top: 10px">
                <table style="border-collapse: collapse; width: 60%; color: #f39c12;">
                    <tbody>
                        <tr>
                            <td style="width: 60%; font-weight: bold;">WFO Total (工場出勤)</td>
                            <td style="width: 0.1%; font-weight: bold;">:</td>
                            <td style="width: 20%; text-align: right; font-weight: bold;" id="wfo_total"></td>
                        </tr>
                        <tr>
                            <td style="width: 60%; font-weight: bold;">WFO Office (事務系の出勤比率)</td>
                            <td style="width: 0.1%; font-weight: bold;">:</td>
                            <td style="width: 20%; text-align: right; font-weight: bold;" id="wfo_office"></td>
                        </tr>
                        <tr>
                            <td style="width: 60%; font-weight: bold;">WFO Production (生産系の出勤比率)</td>
                            <td style="width: 0.1%; font-weight: bold;">:</td>
                            <td style="width: 20%; text-align: right; font-weight: bold;" id="wfo_production"></td>
                        </tr>
                        <tr>
                            <td style="width: 60%;">WFO Production Shift 1</td>
                            <td style="width: 0.1%;">:</td>
                            <td style="width: 20%; text-align: right;" id="wfo_production_shift_1"></td>
                        </tr>
                        <tr>
                            <td style="width: 60%;">WFO Production Shift 2</td>
                            <td style="width: 0.1%;">:</td>
                            <td style="width: 20%; text-align: right;" id="wfo_production_shift_2"></td>
                        </tr>
                        <tr>
                            <td style="width: 60%;">WFO Production Shift 3</td>
                            <td style="width: 0.1%;">:</td>
                            <td style="width: 20%; text-align: right;" id="wfo_production_shift_3"></td>
                        </tr>
                        <tr>
                            <td style="width: 60%; font-weight: bold;">Masuk WFH/SBH (自宅待機)</td>
                            <td style="width: 0.1%; font-weight: bold;">:</td>
                            <td style="width: 20%; text-align: right; font-weight: bold;" id="masuk_wfh_sbh"></td>
                        </tr>
                        <tr>
                            <td style="width: 60%; font-weight: bold;">Tidak Masuk (欠勤)</td>
                            <td style="width: 0.1%; font-weight: bold;">:</td>
                            <td style="width: 20%; text-align: right; font-weight: bold;" id="tidak_masuk"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-4" style="margin-top: 10px">
                <table style="border-collapse: collapse; width: 50%; color: #f39c12;">
                    <tbody>
                        <tr>
                            <td style="font-weight: bold;" colspan="3">Jumlah Karyawan 従業員人数</td>
                        </tr>
                        <tr>
                            <td style="width: 50%;">Japanese (駐在員)</td>
                            <td style="width: 0.1%;">:</td>
                            <td style="width: 20%; text-align: right;" id="japanese"></td>
                        </tr>
                        <tr>
                            <td style="width: 50%;">Tetap (正社員)</td>
                            <td style="width: 0.1%;">:</td>
                            <td style="width: 20%; text-align: right;" id="tetap"></td>
                        </tr>
                        <tr>
                            <td style="width: 50%;">Kontrak (契約社員)</td>
                            <td style="width: 0.1%;">:</td>
                            <td style="width: 20%; text-align: right;" id="kontrak"></td>
                        </tr>
                        <tr>
                            <td style="width: 50%;">Total (総人数)</td>
                            <td style="width: 0.1%;">:</td>
                            <td style="width: 20%; text-align: right;" id="total"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-12" style="padding-top: 10px;">
                <table id="tableResume"
                    style="border-color: black; width: 80%; border-collapse: collapse; border: 1px solid black;">
                    <thead style="background-color: #2a2628;">
                        <tr style="color: #f39c12;">
                            <th style="width: 1%; border:1px solid black;" colspan="3">#</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 1<br>1直</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 2<br>2直</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 3<br>3直</th>
                            <th style="width: 0.1%; border:1px solid black;">OFF<br>オフ</th>
                            <th style="width: 0.1%; border:1px solid black;">Total<br>全部</th>
                            <th style="width: 0.1%; border:1px solid black;">Ratio<br>確率</th>
                        </tr>
                    </thead>
                    <tbody id="tableResumeBody">
                    </tbody>
                    <tfoot id="tableResumeFoot">
                    </tfoot>
                </table>
            </div>
            <div class="col-md-12" style="padding-top: 10px;">
                <table class="" id="tableDepartment"
                    style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black;">
                    <thead style="background-color: #2a2628;">
                        <tr style="color: #f39c12;">
                            <th style="width: 0.1%; border:1px solid black;">#</th>
                            <th style="width: 1%; border:1px solid black;">Department</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 1<br>Hadir</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 1<br>Tidak Hadir</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 1<br>Libur</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 2<br>Hadir</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 2<br>Tidak Hadir</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 2<br>Libur</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 3<br>Hadir</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 3<br>Tidak Hadir</th>
                            <th style="width: 0.1%; border:1px solid black;">Shift 3<br>Libur</th>
                            <th style="width: 0.1%; border:1px solid black;">OFF<br>Hadir</th>
                            <th style="width: 0.1%; border:1px solid black;">OFF<br>Tidak Hadir</th>
                            <th style="width: 0.1%; border:1px solid black;">OFF<br>Libur</th>
                        </tr>
                    </thead>
                    <tbody id="tableDepartmentBody">
                    </tbody>
                    <tfoot id="tableDepartmentFoot">
                        <tr style="background-color: RGB(252, 248, 227);">
                            <th></th>
                            <th style="text-align: center;">TOTAL</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>


    <div class="modal fade" id="modalDetail" data-keyboard="false">
        <div class="modal-dialog modal-lg" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <table id="tableDetail"
                            style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black;">
                            <thead style="background-color: #2a2628; height: 40px;">
                                <tr style="color: #f39c12;">
                                    <th style="width: 0.1%; border:1px solid black; text-align: center;">#</th>
                                    <th style="width: 0.1%; border:1px solid black;">ID</th>
                                    <th style="width: 0.1%; border:1px solid black;">Nama</th>
                                    <th style="width: 0.1%; border:1px solid black;">Department</th>
                                    <th style="width: 0.1%; border:1px solid black;">Section</th>
                                    <th style="width: 0.1%; border:1px solid black;">Group</th>
                                    <th style="width: 0.1%; border:1px solid black;">Shift</th>
                                    <th style="width: 0.1%; border:1px solid black;">Attend Code</th>
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

@endsection

@section('scripts')
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
            $('#datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            $('#datepicker').datepicker('setDate', "");
        });

        var attendances = [];

        function fetchDetail(location, shift, status) {
            console.log(status);
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();
            $('#tableDetailBody').html("");
            var tableDetailBody = "";
            var count = 0;

            $.each(attendances, function(key, value) {
                if (value.location == location && value.shift == shift && status == 'hadir') {
                    if (value.hadir > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
                if (value.location == location && value.shift == shift && status == 'wfh') {
                    if (value.wfh > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
                if (value.location == location && value.shift == shift && status == 'cuti') {
                    if (value.cuti > 0 || value.izin > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
                if (value.location == location && value.shift == shift && status == 'sakit') {
                    if (value.sakit > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
                if (value.location == location && value.shift == shift && status == 'covid') {
                    if (value.covid > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
                if (value.location == location && value.shift == shift && status == 'isoman') {
                    if (value.isoman > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
                if (value.location == location && value.shift == shift && status == 'libur') {
                    if (value.libur > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
                if (value.location == location && value.shift == shift && status == 'absen') {
                    if (value.absen > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
            });

            $('#tableDetailBody').append(tableDetailBody);

            $('#tableDetail').DataTable({
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
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });

            $('#modalDetail').modal('show');
        }

        function fetchDetail2(department, shift, status) {
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();
            $('#tableDetailBody').html("");
            var tableDetailBody = "";
            var count = 0;

            $.each(attendances, function(key, value) {
                if (value.department == department && value.shift == shift && status == 'hadir') {
                    if (value.hadir > 0 || value.wfh > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
                if (value.department == department && value.shift == shift && status == 'tidak_hadir') {
                    if (value.sakit > 0 || value.cuti > 0 || value.izin > 0 || value.isoman > 0 || value.covid >
                        0 || value.absen > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
                if (value.department == department && value.shift == shift && status == 'libur') {
                    if (value.libur > 0) {
                        count += 1;
                        tableDetailBody += '<tr>';
                        tableDetailBody +=
                            '<td style="width: 0.1%; border: 1px solid black; text-align: center;">' + count +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.emp_no +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.3%; border: 1px solid black;">' + value.full_name +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.5%; border: 1px solid black;">' + value.department +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.sections +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.2%; border: 1px solid black;">' + value.groups +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value.shift +
                            '</td>';
                        tableDetailBody += '<td style="width: 0.1%; border: 1px solid black;">' + value
                            .attend_code + '</td>';
                        tableDetailBody += '</tr>';
                    }
                }
            });

            $('#tableDetailBody').append(tableDetailBody);

            $('#tableDetail').DataTable({
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
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });

            $('#modalDetail').modal('show');
        }

        function fetchData(date) {
            $('#loading').show();
            var data = {
                date: date
            }

            $.get('{{ url('fetch/report/absence') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#title_text').text('YMPI日常出勤まとめ 日付: ' + result.now);
                    var h = $('#period_title').height();
                    $('#datepicker').css('height', h);

                    attendances = result.attendances;

                    var total_shift_1 = 0;
                    var total_shift_2 = 0;
                    var total_shift_3 = 0;
                    var total_off = 0;

                    var cuti_tidak_masuk_shift_1 = 0;
                    var cuti_tidak_masuk_shift_2 = 0;
                    var cuti_tidak_masuk_shift_3 = 0;
                    var cuti_tidak_masuk_off = 0;

                    var sakit_tidak_masuk_shift_1 = 0;
                    var sakit_tidak_masuk_shift_2 = 0;
                    var sakit_tidak_masuk_shift_3 = 0;
                    var sakit_tidak_masuk_off = 0;

                    var isoman_tidak_masuk_shift_1 = 0;
                    var isoman_tidak_masuk_shift_2 = 0;
                    var isoman_tidak_masuk_shift_3 = 0;
                    var isoman_tidak_masuk_off = 0;

                    var covid_tidak_masuk_shift_1 = 0;
                    var covid_tidak_masuk_shift_2 = 0;
                    var covid_tidak_masuk_shift_3 = 0;
                    var covid_tidak_masuk_off = 0;

                    var libur_tidak_masuk_shift_1 = 0;
                    var libur_tidak_masuk_shift_2 = 0;
                    var libur_tidak_masuk_shift_3 = 0;
                    var libur_tidak_masuk_off = 0;

                    var absen_tidak_masuk_shift_1 = 0;
                    var absen_tidak_masuk_shift_2 = 0;
                    var absen_tidak_masuk_shift_3 = 0;
                    var absen_tidak_masuk_off = 0;

                    var tidak_masuk_shift_1 = 0;
                    var tidak_masuk_shift_2 = 0;
                    var tidak_masuk_shift_3 = 0;
                    var tidak_masuk_off = 0;

                    var ofc_total_wfo = 0;
                    var ofc_total_wfh = 0;
                    var ofc_total_cuti = 0;
                    var ofc_total_sakit = 0;
                    var ofc_total_covid = 0;
                    var ofc_total_isoman = 0;
                    var ofc_total_libur = 0;
                    var ofc_total_absen = 0;
                    var ofc_total = 0;

                    var ofc_shift_1_wfo = 0;
                    var ofc_shift_1_wfh = 0;
                    var ofc_shift_1_cuti = 0;
                    var ofc_shift_1_sakit = 0;
                    var ofc_shift_1_covid = 0;
                    var ofc_shift_1_isoman = 0;
                    var ofc_shift_1_libur = 0;
                    var ofc_shift_1_absen = 0;
                    var ofc_shift_1_total = 0;

                    var ofc_shift_2_wfo = 0;
                    var ofc_shift_2_wfh = 0;
                    var ofc_shift_2_cuti = 0;
                    var ofc_shift_2_sakit = 0;
                    var ofc_shift_2_covid = 0;
                    var ofc_shift_2_isoman = 0;
                    var ofc_shift_2_libur = 0;
                    var ofc_shift_2_absen = 0;
                    var ofc_shift_2_total = 0;

                    var ofc_shift_3_wfo = 0;
                    var ofc_shift_3_wfh = 0;
                    var ofc_shift_3_cuti = 0;
                    var ofc_shift_3_sakit = 0;
                    var ofc_shift_3_covid = 0;
                    var ofc_shift_3_isoman = 0;
                    var ofc_shift_3_libur = 0;
                    var ofc_shift_3_absen = 0;
                    var ofc_shift_3_total = 0;

                    var ofc_off_wfo = 0;
                    var ofc_off_wfh = 0;
                    var ofc_off_cuti = 0;
                    var ofc_off_sakit = 0;
                    var ofc_off_covid = 0;
                    var ofc_off_isoman = 0;
                    var ofc_off_libur = 0;
                    var ofc_off_absen = 0;
                    var ofc_off_total = 0;

                    var prd_total_wfo = 0;
                    var prd_total_wfh = 0;
                    var prd_total_cuti = 0;
                    var prd_total_sakit = 0;
                    var prd_total_covid = 0;
                    var prd_total_isoman = 0;
                    var prd_total_libur = 0;
                    var prd_total_absen = 0;
                    var prd_total = 0;

                    var prd_shift_1_wfo = 0;
                    var prd_shift_1_wfh = 0;
                    var prd_shift_1_cuti = 0;
                    var prd_shift_1_sakit = 0;
                    var prd_shift_1_covid = 0;
                    var prd_shift_1_isoman = 0;
                    var prd_shift_1_libur = 0;
                    var prd_shift_1_absen = 0;
                    var prd_shift_1_total = 0;

                    var prd_shift_2_wfo = 0;
                    var prd_shift_2_wfh = 0;
                    var prd_shift_2_cuti = 0;
                    var prd_shift_2_sakit = 0;
                    var prd_shift_2_covid = 0;
                    var prd_shift_2_isoman = 0;
                    var prd_shift_2_libur = 0;
                    var prd_shift_2_absen = 0;
                    var prd_shift_2_total = 0;

                    var prd_shift_3_wfo = 0;
                    var prd_shift_3_wfh = 0;
                    var prd_shift_3_cuti = 0;
                    var prd_shift_3_sakit = 0;
                    var prd_shift_3_covid = 0;
                    var prd_shift_3_isoman = 0;
                    var prd_shift_3_libur = 0;
                    var prd_shift_3_absen = 0;
                    var prd_shift_3_total = 0;

                    var prd_off_wfo = 0;
                    var prd_off_wfh = 0;
                    var prd_off_cuti = 0;
                    var prd_off_sakit = 0;
                    var prd_off_covid = 0;
                    var prd_off_isoman = 0;
                    var prd_off_libur = 0;
                    var prd_off_absen = 0;
                    var prd_off_total = 0;

                    var total_japanese = 0;
                    var total_tetap = 0;
                    var total_kontrak = 0;
                    var total = 0;

                    $.each(result.attendances, function(key, value) {
                        if (value.grade_code == 'J0-') {
                            total_japanese += 1;
                        } else if (value.employ_code == 'PERMANENT') {
                            total_tetap += 1;
                        } else {
                            total_kontrak += 1;
                        }
                    });

                    $.each(result.resume, function(key, value) {
                        if (value.location == 'office') {
                            ofc_total_wfo += parseInt(value.hadir);
                            ofc_total_wfh += parseInt(value.wfh);
                            ofc_total_cuti += parseInt(value.cuti) + parseInt(value.izin);
                            ofc_total_sakit += parseInt(value.sakit);
                            ofc_total_covid += parseInt(value.covid);
                            ofc_total_isoman += parseInt(value.isoman);
                            ofc_total_libur += parseInt(value.libur);
                            ofc_total_absen += parseInt(value.absen);
                            ofc_total += parseInt(value.hadir) + parseInt(value.wfh) + parseInt(value
                                .cuti) + parseInt(value.izin) + parseInt(value.sakit) + parseInt(value
                                .covid) + parseInt(value.isoman) + parseInt(value.libur) + parseInt(
                                value.absen);

                            if (value.shift == 'shift_1') {
                                ofc_shift_1_wfo += parseInt(value.hadir);
                                ofc_shift_1_wfh += parseInt(value.wfh);
                                ofc_shift_1_cuti += parseInt(value.cuti) + parseInt(value.izin);
                                ofc_shift_1_sakit += parseInt(value.sakit);
                                ofc_shift_1_covid += parseInt(value.covid);
                                ofc_shift_1_isoman += parseInt(value.isoman);
                                ofc_shift_1_libur += parseInt(value.libur);
                                ofc_shift_1_absen += parseInt(value.absen);
                                ofc_shift_1_total += parseInt(value.hadir) + parseInt(value.wfh) + parseInt(
                                        value.cuti) + parseInt(value.izin) + parseInt(value.sakit) +
                                    parseInt(value.covid) + parseInt(value.isoman) + parseInt(value.libur) +
                                    parseInt(value.absen);
                            }
                            if (value.shift == 'shift_2') {
                                ofc_shift_2_wfo += parseInt(value.hadir);
                                ofc_shift_2_wfh += parseInt(value.wfh);
                                ofc_shift_2_cuti += parseInt(value.cuti) + parseInt(value.izin);
                                ofc_shift_2_sakit += parseInt(value.sakit);
                                ofc_shift_2_covid += parseInt(value.covid);
                                ofc_shift_2_isoman += parseInt(value.isoman);
                                ofc_shift_2_libur += parseInt(value.libur);
                                ofc_shift_2_absen += parseInt(value.absen);
                                ofc_shift_2_total += parseInt(value.hadir) + parseInt(value.wfh) + parseInt(
                                        value.cuti) + parseInt(value.izin) + parseInt(value.sakit) +
                                    parseInt(value.covid) + parseInt(value.isoman) + parseInt(value.libur) +
                                    parseInt(value.absen);
                            }
                            if (value.shift == 'shift_3') {
                                ofc_shift_3_wfo += parseInt(value.hadir);
                                ofc_shift_3_wfh += parseInt(value.wfh);
                                ofc_shift_3_cuti += parseInt(value.cuti) + parseInt(value.izin);
                                ofc_shift_3_sakit += parseInt(value.sakit);
                                ofc_shift_3_covid += parseInt(value.covid);
                                ofc_shift_3_isoman += parseInt(value.isoman);
                                ofc_shift_3_libur += parseInt(value.libur);
                                ofc_shift_3_absen += parseInt(value.absen);
                                ofc_shift_3_total += parseInt(value.hadir) + parseInt(value.wfh) + parseInt(
                                        value.cuti) + parseInt(value.izin) + parseInt(value.sakit) +
                                    parseInt(value.covid) + parseInt(value.isoman) + parseInt(value.libur) +
                                    parseInt(value.absen);
                            }
                            if (value.shift == 'off') {
                                ofc_off_wfo += parseInt(value.hadir);
                                ofc_off_wfh += parseInt(value.wfh);
                                ofc_off_cuti += parseInt(value.cuti) + parseInt(value.izin);
                                ofc_off_sakit += parseInt(value.sakit);
                                ofc_off_covid += parseInt(value.covid);
                                ofc_off_isoman += parseInt(value.isoman);
                                ofc_off_libur += parseInt(value.libur);
                                ofc_off_absen += parseInt(value.absen);
                                ofc_off_total += parseInt(value.hadir) + parseInt(value.wfh) + parseInt(
                                        value.cuti) + parseInt(value.izin) + parseInt(value.sakit) +
                                    parseInt(value.covid) + parseInt(value.isoman) + parseInt(value.libur) +
                                    parseInt(value.absen);
                            }

                        }
                        if (value.location == 'production') {
                            prd_total_wfo += parseInt(value.hadir);
                            prd_total_wfh += parseInt(value.wfh);
                            prd_total_cuti += parseInt(value.cuti) + parseInt(value.izin);
                            prd_total_sakit += parseInt(value.sakit);
                            prd_total_covid += parseInt(value.covid);
                            prd_total_isoman += parseInt(value.isoman);
                            prd_total_libur += parseInt(value.libur);
                            prd_total_absen += parseInt(value.absen);
                            prd_total += parseInt(value.hadir) + parseInt(value.wfh) + parseInt(value
                                .cuti) + parseInt(value.izin) + parseInt(value.sakit) + parseInt(value
                                .covid) + parseInt(value.isoman) + parseInt(value.libur) + parseInt(
                                value.absen);

                            if (value.shift == 'shift_1') {
                                prd_shift_1_wfo += parseInt(value.hadir);
                                prd_shift_1_wfh += parseInt(value.wfh);
                                prd_shift_1_cuti += parseInt(value.cuti) + parseInt(value.izin);
                                prd_shift_1_sakit += parseInt(value.sakit);
                                prd_shift_1_covid += parseInt(value.covid);
                                prd_shift_1_isoman += parseInt(value.isoman);
                                prd_shift_1_libur += parseInt(value.libur);
                                prd_shift_1_absen += parseInt(value.absen);
                                prd_shift_1_total += parseInt(value.hadir) + parseInt(value.wfh) + parseInt(
                                        value.cuti) + parseInt(value.izin) + parseInt(value.sakit) +
                                    parseInt(value.covid) + parseInt(value.isoman) + parseInt(value.libur) +
                                    parseInt(value.absen);
                            }
                            if (value.shift == 'shift_2') {
                                prd_shift_2_wfo += parseInt(value.hadir);
                                prd_shift_2_wfh += parseInt(value.wfh);
                                prd_shift_2_cuti += parseInt(value.cuti) + parseInt(value.izin);
                                prd_shift_2_sakit += parseInt(value.sakit);
                                prd_shift_2_covid += parseInt(value.covid);
                                prd_shift_2_isoman += parseInt(value.isoman);
                                prd_shift_2_libur += parseInt(value.libur);
                                prd_shift_2_absen += parseInt(value.absen);
                                prd_shift_2_total += parseInt(value.hadir) + parseInt(value.wfh) + parseInt(
                                        value.cuti) + parseInt(value.izin) + parseInt(value.sakit) +
                                    parseInt(value.covid) + parseInt(value.isoman) + parseInt(value.libur) +
                                    parseInt(value.absen);
                            }
                            if (value.shift == 'shift_3') {
                                prd_shift_3_wfo += parseInt(value.hadir);
                                prd_shift_3_wfh += parseInt(value.wfh);
                                prd_shift_3_cuti += parseInt(value.cuti) + parseInt(value.izin);
                                prd_shift_3_sakit += parseInt(value.sakit);
                                prd_shift_3_covid += parseInt(value.covid);
                                prd_shift_3_isoman += parseInt(value.isoman);
                                prd_shift_3_libur += parseInt(value.libur);
                                prd_shift_3_absen += parseInt(value.absen);
                                prd_shift_3_total += parseInt(value.hadir) + parseInt(value.wfh) + parseInt(
                                        value.cuti) + parseInt(value.izin) + parseInt(value.sakit) +
                                    parseInt(value.covid) + parseInt(value.isoman) + parseInt(value.libur) +
                                    parseInt(value.absen);
                            }
                            if (value.shift == 'off') {
                                prd_off_wfo += parseInt(value.hadir);
                                prd_off_wfh += parseInt(value.wfh);
                                prd_off_cuti += parseInt(value.cuti) + parseInt(value.izin);
                                prd_off_sakit += parseInt(value.sakit);
                                prd_off_covid += parseInt(value.covid);
                                prd_off_isoman += parseInt(value.isoman);
                                prd_off_libur += parseInt(value.libur);
                                prd_off_absen += parseInt(value.absen);
                                prd_off_total += parseInt(value.hadir) + parseInt(value.wfh) + parseInt(
                                        value.cuti) + parseInt(value.izin) + parseInt(value.sakit) +
                                    parseInt(value.covid) + parseInt(value.isoman) + parseInt(value.libur) +
                                    parseInt(value.absen);
                            }
                        }

                        if (value.shift == 'shift_1') {
                            total_shift_1 += parseInt(value.isoman) + parseInt(value.covid) + parseInt(value
                                .sakit) + parseInt(value.izin) + parseInt(value.cuti) + parseInt(value
                                .wfh) + parseInt(value.hadir) + parseInt(value.libur) + parseInt(value
                                .absen);

                            cuti_tidak_masuk_shift_1 += parseInt(value.izin) + parseInt(value.cuti);
                            sakit_tidak_masuk_shift_1 += parseInt(value.sakit);
                            isoman_tidak_masuk_shift_1 += parseInt(value.isoman);
                            covid_tidak_masuk_shift_1 += parseInt(value.covid);
                            libur_tidak_masuk_shift_1 += parseInt(value.libur);
                            absen_tidak_masuk_shift_1 += parseInt(value.absen);
                            tidak_masuk_shift_1 += parseInt(value.izin) + parseInt(value.cuti) + parseInt(
                                    value.sakit) + parseInt(value.isoman) + parseInt(value.covid) +
                                parseInt(value.libur) + parseInt(value.absen);

                        }
                        if (value.shift == 'shift_2') {
                            total_shift_2 += parseInt(value.isoman) + parseInt(value.covid) + parseInt(value
                                .sakit) + parseInt(value.izin) + parseInt(value.cuti) + parseInt(value
                                .wfh) + parseInt(value.hadir) + parseInt(value.libur) + parseInt(value
                                .absen);

                            cuti_tidak_masuk_shift_2 += parseInt(value.izin) + parseInt(value.cuti);
                            sakit_tidak_masuk_shift_2 += parseInt(value.sakit);
                            isoman_tidak_masuk_shift_2 += parseInt(value.isoman);
                            covid_tidak_masuk_shift_2 += parseInt(value.covid);
                            libur_tidak_masuk_shift_2 += parseInt(value.libur);
                            absen_tidak_masuk_shift_2 += parseInt(value.absen);
                            tidak_masuk_shift_2 += parseInt(value.izin) + parseInt(value.cuti) + parseInt(
                                    value.sakit) + parseInt(value.isoman) + parseInt(value.covid) +
                                parseInt(value.libur) + parseInt(value.absen);
                        }
                        if (value.shift == 'shift_3') {
                            total_shift_3 += parseInt(value.isoman) + parseInt(value.covid) + parseInt(value
                                .sakit) + parseInt(value.izin) + parseInt(value.cuti) + parseInt(value
                                .wfh) + parseInt(value.hadir) + parseInt(value.libur) + parseInt(value
                                .absen);

                            cuti_tidak_masuk_shift_3 += parseInt(value.izin) + parseInt(value.cuti);
                            sakit_tidak_masuk_shift_3 += parseInt(value.sakit);
                            isoman_tidak_masuk_shift_3 += parseInt(value.isoman);
                            covid_tidak_masuk_shift_3 += parseInt(value.covid);
                            libur_tidak_masuk_shift_3 += parseInt(value.libur);
                            absen_tidak_masuk_shift_3 += parseInt(value.absen);
                            tidak_masuk_shift_3 += parseInt(value.izin) + parseInt(value.cuti) + parseInt(
                                    value.sakit) + parseInt(value.isoman) + parseInt(value.covid) +
                                parseInt(value.libur) + parseInt(value.absen);
                        }
                        if (value.shift == 'off') {
                            total_off += parseInt(value.isoman) + parseInt(value.covid) + parseInt(value
                                .sakit) + parseInt(value.izin) + parseInt(value.cuti) + parseInt(value
                                .wfh) + parseInt(value.hadir) + parseInt(value.libur) + parseInt(value
                                .absen);

                            cuti_tidak_masuk_off += parseInt(value.izin) + parseInt(value.cuti);
                            sakit_tidak_masuk_off += parseInt(value.sakit);
                            isoman_tidak_masuk_off += parseInt(value.isoman);
                            covid_tidak_masuk_off += parseInt(value.covid);
                            libur_tidak_masuk_off += parseInt(value.libur);
                            absen_tidak_masuk_off += parseInt(value.absen);
                            tidak_masuk_off += parseInt(value.izin) + parseInt(value.cuti) + parseInt(value
                                .sakit) + parseInt(value.isoman) + parseInt(value.covid) + parseInt(
                                value.libur) + parseInt(value.absen);
                        }
                    });

                    total = parseInt(total_shift_1) + parseInt(total_shift_2) + parseInt(total_shift_3) + parseInt(
                        total_off);
                    total_tidak_masuk = parseInt(cuti_tidak_masuk_shift_1) + parseInt(sakit_tidak_masuk_shift_1) +
                        parseInt(isoman_tidak_masuk_shift_1) + parseInt(covid_tidak_masuk_shift_1) + parseInt(
                            cuti_tidak_masuk_shift_2) + parseInt(sakit_tidak_masuk_shift_2) + parseInt(
                            isoman_tidak_masuk_shift_2) + parseInt(covid_tidak_masuk_shift_2) + parseInt(
                            cuti_tidak_masuk_shift_3) + parseInt(sakit_tidak_masuk_shift_3) + parseInt(
                            isoman_tidak_masuk_shift_3) + parseInt(covid_tidak_masuk_shift_3) + parseInt(
                            cuti_tidak_masuk_off) + parseInt(sakit_tidak_masuk_off) + parseInt(
                            isoman_tidak_masuk_off) + parseInt(covid_tidak_masuk_off) + parseInt(
                            libur_tidak_masuk_shift_1) + parseInt(libur_tidak_masuk_shift_2) + parseInt(
                            libur_tidak_masuk_shift_3) + parseInt(libur_tidak_masuk_off) + parseInt(
                            absen_tidak_masuk_shift_1) + parseInt(absen_tidak_masuk_shift_2) + parseInt(
                            absen_tidak_masuk_shift_3) + parseInt(absen_tidak_masuk_off);

                    $('#wfo_total').text(ofc_total_wfo + prd_total_wfo + ' (' + (((ofc_total_wfo + prd_total_wfo) /
                        total) * 100).toFixed(1) + '%)');
                    $('#wfo_office').text(ofc_total_wfo + ' (' + ((ofc_total_wfo / (ofc_total_wfo + ofc_total_wfh +
                        ofc_total_cuti + ofc_total_sakit + ofc_total_covid + ofc_total_isoman +
                        ofc_total_libur + ofc_total_absen)) * 100).toFixed(1) + '%)');

                    $('#wfo_production').text(prd_total_wfo + ' (' + ((prd_total_wfo / (prd_total_wfo +
                        prd_total_wfh + prd_total_cuti + prd_total_sakit + prd_total_covid +
                        prd_total_isoman + prd_total_libur + prd_total_absen)) * 100).toFixed(1) + '%)');
                    $('#wfo_production_shift_1').text(prd_shift_1_wfo + ' (' + ((prd_shift_1_wfo / (prd_total_wfo +
                        prd_total_wfh + prd_total_cuti + prd_total_sakit + prd_total_covid +
                        prd_total_isoman + prd_total_libur + prd_total_absen)) * 100).toFixed(1) + '%)');
                    $('#wfo_production_shift_2').text(prd_shift_2_wfo + ' (' + ((prd_shift_2_wfo / (prd_total_wfo +
                        prd_total_wfh + prd_total_cuti + prd_total_sakit + prd_total_covid +
                        prd_total_isoman + prd_total_libur + prd_total_absen)) * 100).toFixed(1) + '%)');
                    $('#wfo_production_shift_3').text(prd_shift_3_wfo + ' (' + ((prd_shift_3_wfo / (prd_total_wfo +
                        prd_total_wfh + prd_total_cuti + prd_total_sakit + prd_total_covid +
                        prd_total_isoman + prd_total_libur + prd_total_absen)) * 100).toFixed(1) + '%)');

                    $('#masuk_wfh_sbh').text(ofc_total_wfh + prd_total_wfh + ' (' + (((ofc_total_wfh +
                        prd_total_wfh) / total) * 100).toFixed(1) + '%)');
                    $('#tidak_masuk').text(total_tidak_masuk + ' (' + ((total_tidak_masuk / total) * 100).toFixed(
                        1) + '%)');

                    $('#japanese').text(total_japanese + ' 人');
                    $('#tetap').text(total_tetap + ' 人');
                    $('#kontrak').text(total_kontrak + ' 人');
                    $('#total').text(total + ' 人');

                    $('#tableResumeBody').html("");
                    var tableResumeBody = "";
                    $('#tableResumeFoot').html("");
                    var tableResumeFoot = "";

                    var isset = function(variable) {
                        return typeof(variable) !== "undefined" && variable !== null && variable !== '';
                    }

                    var shifts = ['officeshift_1', 'officeshift_2', 'officeshift_3', 'officeoff',
                        'productionshift_1', 'productionshift_2', 'productionshift_3', 'productionoff'
                    ];

                    var res = [];

                    var resumes = result.resume;

                    for (var i = 0; i < shifts.length; i++) {
                        var hadir = 0;
                        var wfh = 0;
                        var cuti = 0;
                        var sakit = 0;
                        var covid = 0;
                        var isoman = 0;
                        var libur = 0;
                        var absen = 0;

                        $.each(resumes, function(key, value) {
                            if (shifts[i] == value.location + value.shift) {
                                hadir = value.hadir;
                                wfh = value.wfh;
                                cuti = parseInt(value.cuti + value.izin);
                                sakit = value.sakit;
                                covid = value.covid;
                                isoman = value.isoman;
                                libur = value.libur;
                                absen = value.absen;
                            }
                        });

                        var key = shifts[i];

                        res[key] = {
                            'location': shifts[i],
                            'hadir': hadir,
                            'wfh': wfh,
                            'cuti': cuti,
                            'sakit': sakit,
                            'covid': covid,
                            'isoman': isoman,
                            'libur': libur,
                            'absen': absen
                        };

                    }

                    var ofc_shift_1_hadir = parseFloat(res['officeshift_1']['hadir']);
                    var ofc_shift_2_hadir = parseFloat(res['officeshift_2']['hadir']);
                    var ofc_shift_3_hadir = parseFloat(res['officeshift_3']['hadir']);
                    var ofc_off_hadir = parseFloat(res['officeoff']['hadir']);

                    var prd_shift_1_hadir = parseFloat(res['productionshift_1']['hadir']);
                    var prd_shift_2_hadir = parseFloat(res['productionshift_2']['hadir']);
                    var prd_shift_3_hadir = parseFloat(res['productionshift_3']['hadir']);
                    var prd_off_hadir = parseFloat(res['productionoff']['hadir']);

                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="border: 1px solid black;" rowspan="8">Office<br>事務所</td>';
                    tableResumeBody += '<td style="border: 1px solid black;" rowspan="3">Masuk<br>出勤</td>';
                    tableResumeBody += '<td style="text-align: left; border: 1px solid black;">WFO 出社</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_1\',\'hadir\')">' +
                        ofc_shift_1_hadir + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_2\',\'hadir\')">' +
                        ofc_shift_2_hadir + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_3\',\'hadir\')">' +
                        ofc_shift_3_hadir + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'off\',\'hadir\')">' +
                        ofc_off_hadir + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        ofc_shift_1_hadir + ofc_shift_2_hadir + ofc_shift_3_hadir + ofc_off_hadir) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        ofc_total_wfo / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';

                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="text-align: left; border: 1px solid black;">WFH 在宅勤務</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_1\',\'wfh\')">' +
                        ofc_shift_1_wfh + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_2\',\'wfh\')">' +
                        ofc_shift_2_wfh + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_3\',\'wfh\')">' +
                        ofc_shift_3_wfh + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'off\',\'wfh\')">' +
                        ofc_off_wfh + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;"">' + parseFloat(
                        ofc_shift_1_wfh + ofc_shift_2_wfh + ofc_shift_3_wfh + ofc_off_wfh) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        ofc_total_wfh / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody +=
                        '<td style="text-align: left; border: 1px solid black;">WFO Ratio 出社の比率</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(((
                            ofc_shift_1_hadir / notZero(ofc_shift_1_hadir + ofc_shift_1_wfh +
                                ofc_shift_1_sakit + ofc_shift_1_cuti + ofc_shift_1_covid +
                                ofc_shift_1_isoman + ofc_shift_1_libur + ofc_shift_1_absen)) * 100)).toFixed(1) +
                        '%</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(((
                            ofc_shift_2_hadir / notZero(ofc_shift_2_hadir + ofc_shift_2_wfh +
                                ofc_shift_2_sakit + ofc_shift_2_cuti + ofc_shift_2_covid +
                                ofc_shift_2_isoman + ofc_shift_2_libur + ofc_shift_2_absen)) * 100)).toFixed(1) +
                        '%</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(((
                            ofc_shift_3_hadir / notZero(ofc_shift_3_hadir + ofc_shift_3_wfh +
                                ofc_shift_3_sakit + ofc_shift_3_cuti + ofc_shift_3_covid +
                                ofc_shift_3_isoman + ofc_shift_3_libur + ofc_shift_3_absen)) * 100)).toFixed(1) +
                        '%</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(((
                        ofc_off_hadir / notZero(ofc_off_hadir + ofc_off_wfh + ofc_off_sakit +
                            ofc_off_cuti + ofc_off_covid + ofc_off_isoman + ofc_off_libur +
                            ofc_off_absen)) * 100)).toFixed(1) + '%</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;"></td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;"></td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="border: 1px solid black;" rowspan="4">Tidak Masuk<br>不在</td>';
                    tableResumeBody +=
                        '<td style="text-align: left; border: 1px solid black;">Izin/Cuti 休暇・有休</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_1\',\'cuti\')">' +
                        ofc_shift_1_cuti + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_2\',\'cuti\')">' +
                        ofc_shift_2_cuti + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_3\',\'cuti\')">' +
                        ofc_shift_3_cuti + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'off\',\'cuti\')">' +
                        ofc_off_cuti + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        ofc_shift_1_cuti + ofc_shift_2_cuti + ofc_shift_3_cuti + ofc_off_cuti) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        ofc_total_cuti / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="text-align: left; border: 1px solid black;">Sakit 病欠</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_1\',\'sakit\')">' +
                        ofc_shift_1_sakit + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_2\',\'sakit\')">' +
                        ofc_shift_2_sakit + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_3\',\'sakit\')">' +
                        ofc_shift_3_sakit + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'off\',\'sakit\')">' +
                        ofc_off_sakit + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        ofc_shift_1_sakit + ofc_shift_2_sakit + ofc_shift_3_sakit + ofc_off_sakit) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        ofc_total_sakit / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    // tableResumeBody += '<tr>';
                    // tableResumeBody +=
                    //     '<td style="color: rgb(255,204,255); text-align: left; border: 1px solid black;">Covid (PCR)<br>コロナ（PCR）</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_1\',\'covid\')">' +
                    //     ofc_shift_1_covid + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_2\',\'covid\')">' +
                    //     ofc_shift_2_covid + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_3\',\'covid\')">' +
                    //     ofc_shift_3_covid + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'off\',\'covid\')">' +
                    //     ofc_off_covid + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                    //     ofc_shift_1_covid + ofc_shift_2_covid + ofc_shift_3_covid + ofc_off_covid) + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                    //     ofc_total_covid / total) * 100).toFixed(1) + '%</td>';
                    // tableResumeBody += '</tr>';
                    // tableResumeBody += '<tr>';
                    // tableResumeBody +=
                    //     '<td style="color: rgb(255,204,255); text-align: left; border: 1px solid black;">Isoman (Non Covid)<br>自主隔離（コロナ以外）</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_1\',\'isoman\')">' +
                    //     ofc_shift_1_isoman + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_2\',\'isoman\')">' +
                    //     ofc_shift_2_isoman + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_3\',\'isoman\')">' +
                    //     ofc_shift_3_isoman + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'off\',\'isoman\')">' +
                    //     ofc_off_isoman + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                    //     ofc_shift_1_isoman + ofc_shift_2_isoman + ofc_shift_3_isoman + ofc_off_isoman) + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                    //     ofc_total_isoman / total) * 100).toFixed(1) + '%</td>';
                    // tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="text-align: left; border: 1px solid black;">Libur 休日</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_1\',\'libur\')">' +
                        ofc_shift_1_libur + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_2\',\'libur\')">' +
                        ofc_shift_2_libur + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_3\',\'libur\')">' +
                        ofc_shift_3_libur + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'off\',\'libur\')">' +
                        ofc_off_libur + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        ofc_shift_1_libur + ofc_shift_2_libur + ofc_shift_3_libur + ofc_off_libur) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        ofc_total_libur / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody +=
                        '<td style="text-align: left; border: 1px solid black;">Belum Konfirmasi 未確認</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_1\',\'absen\')">' +
                        ofc_shift_1_absen + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_2\',\'absen\')">' +
                        ofc_shift_2_absen + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_3\',\'absen\')">' +
                        ofc_shift_3_absen + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'off\',\'absen\')">' +
                        ofc_off_absen + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        ofc_shift_1_absen + ofc_shift_2_absen + ofc_shift_3_absen + ofc_off_absen) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        ofc_total_absen / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody +=
                        '<td style="text-align: left; border: 1px solid black;" colspan="2">Total 全部</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_1\',\'total\')">' +
                        ofc_shift_1_total + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_2\',\'total\')">' +
                        ofc_shift_2_total + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_3\',\'total\')">' +
                        ofc_shift_3_total + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'off\',\'total\')">' +
                        ofc_off_total + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        ofc_shift_1_total + ofc_shift_2_total + ofc_shift_3_total + ofc_off_total) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((ofc_total /
                        total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="border: 1px solid black;" rowspan="8">Produksi<br>生産職場</td>';
                    tableResumeBody += '<td style="border: 1px solid black;" rowspan="3">Masuk<br>出勤</td>';
                    tableResumeBody += '<td style="text-align: left; border: 1px solid black;">WFO 出社</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_1\',\'hadir\')">' +
                        prd_shift_1_hadir + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_2\',\'hadir\')">' +
                        prd_shift_2_hadir + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_3\',\'hadir\')">' +
                        prd_shift_3_hadir + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'off\',\'hadir\')">' +
                        prd_off_hadir + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        prd_shift_1_hadir + prd_shift_2_hadir + prd_shift_3_hadir + prd_off_hadir) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        prd_total_wfo / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="text-align: left; border: 1px solid black;">SBH 自宅待機</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_1\',\'wfh\')">' +
                        prd_shift_1_wfh + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_2\',\'wfh\')">' +
                        prd_shift_2_wfh + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_3\',\'wfh\')">' +
                        prd_shift_3_wfh + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'off\',\'wfh\')">' +
                        prd_off_wfh + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        prd_shift_1_wfh + prd_shift_2_wfh + prd_shift_3_wfh + prd_off_wfh) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        prd_total_wfh / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody +=
                        '<td style="text-align: left; border: 1px solid black;">WFO Ratio 出社の比率</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(((
                            prd_shift_1_hadir / notZero(prd_shift_1_hadir + prd_shift_1_wfh +
                                prd_shift_1_sakit + prd_shift_1_cuti + prd_shift_1_covid +
                                prd_shift_1_isoman + prd_shift_1_libur + prd_shift_1_absen)) * 100)).toFixed(1) +
                        '%</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(((
                            prd_shift_2_hadir / notZero(prd_shift_2_hadir + prd_shift_2_wfh +
                                prd_shift_2_sakit + prd_shift_2_cuti + prd_shift_2_covid +
                                prd_shift_2_isoman + prd_shift_2_libur + prd_shift_2_absen)) * 100)).toFixed(1) +
                        '%</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(((
                            prd_shift_3_hadir / notZero(prd_shift_3_hadir + prd_shift_3_wfh +
                                prd_shift_3_sakit + prd_shift_3_cuti + prd_shift_3_covid +
                                prd_shift_3_isoman + prd_shift_3_libur + prd_shift_3_absen)) * 100)).toFixed(1) +
                        '%</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(((
                        prd_off_hadir / notZero(prd_off_hadir + prd_off_wfh + prd_off_sakit +
                            prd_off_cuti + prd_off_covid + prd_off_isoman + prd_off_libur +
                            prd_off_absen)) * 100)).toFixed(1) + '%</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;"></td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;"></td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="border: 1px solid black;" rowspan="4">Tidak Masuk<br>不在</td>';
                    tableResumeBody +=
                        '<td style="text-align: left; border: 1px solid black;">Izin/Cuti 休暇・有休</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_1\',\'cuti\')">' +
                        prd_shift_1_cuti + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_2\',\'cuti\')">' +
                        prd_shift_2_cuti + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_3\',\'cuti\')">' +
                        prd_shift_3_cuti + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'off\',\'cuti\')">' +
                        prd_off_cuti + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        prd_shift_1_cuti + prd_shift_2_cuti + prd_shift_3_cuti + prd_off_cuti) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        prd_total_cuti / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="text-align: left; border: 1px solid black;">Sakit 病欠</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_1\',\'sakit\')">' +
                        prd_shift_1_sakit + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_2\',\'sakit\')">' +
                        prd_shift_2_sakit + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_3\',\'sakit\')">' +
                        prd_shift_3_sakit + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'off\',\'sakit\')">' +
                        prd_off_sakit + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        prd_shift_1_sakit + prd_shift_2_sakit + prd_shift_3_sakit + prd_off_sakit) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        prd_total_sakit / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    // tableResumeBody += '<tr>';
                    // tableResumeBody +=
                    //     '<td style="color: rgb(255,204,255); text-align: left; border: 1px solid black;">Covid (PCR)<br>コロナ（PCR）</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_1\',\'covid\')">' +
                    //     prd_shift_1_covid + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_2\',\'covid\')">' +
                    //     prd_shift_2_covid + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_3\',\'covid\')">' +
                    //     prd_shift_3_covid + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'off\',\'covid\')">' +
                    //     prd_off_covid + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                    //     prd_shift_1_covid + prd_shift_2_covid + prd_shift_3_covid + prd_off_covid) + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                    //     prd_total_covid / total) * 100).toFixed(1) + '%</td>';
                    // tableResumeBody += '</tr>';
                    // tableResumeBody += '<tr>';
                    // tableResumeBody +=
                    //     '<td style="color: rgb(255,204,255); text-align: left; border: 1px solid black;">Isoman (Non Covid)<br>自主隔離（コロナ以外）</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_1\',\'isoman\')">' +
                    //     prd_shift_1_isoman + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_2\',\'isoman\')">' +
                    //     prd_shift_2_isoman + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_3\',\'isoman\')">' +
                    //     prd_shift_3_isoman + '</td>';
                    // tableResumeBody +=
                    //     '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'off\',\'isoman\')">' +
                    //     prd_off_isoman + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                    //     prd_shift_1_isoman + prd_shift_2_isoman + prd_shift_3_isoman + prd_off_isoman) + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                    //     prd_total_isoman / total) * 100).toFixed(1) + '%</td>';
                    // tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="text-align: left; border: 1px solid black;">Libur 休日</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_1\',\'libur\')">' +
                        prd_shift_1_libur + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_2\',\'libur\')">' +
                        prd_shift_2_libur + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_3\',\'libur\')">' +
                        prd_shift_3_libur + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'off\',\'libur\')">' +
                        prd_off_libur + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        prd_shift_1_libur + prd_shift_2_libur + prd_shift_3_libur + prd_off_libur) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        prd_total_libur / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody +=
                        '<td style="text-align: left; border: 1px solid black;">Belum Konfirmasi 未確認</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_1\',\'absen\')">' +
                        prd_shift_1_absen + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_2\',\'absen\')">' +
                        prd_shift_2_absen + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'shift_3\',\'absen\')">' +
                        prd_shift_3_absen + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'production\',\'off\',\'absen\')">' +
                        prd_off_absen + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        prd_shift_1_absen + prd_shift_2_absen + prd_shift_3_absen + prd_off_absen) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((
                        prd_total_absen / total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody +=
                        '<td style="text-align: left; border: 1px solid black;" colspan="2">Total 全部</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_1\',\'total\')">' +
                        prd_shift_1_total + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_2\',\'total\')">' +
                        prd_shift_2_total + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'shift_3\',\'total\')">' +
                        prd_shift_3_total + '</td>';
                    tableResumeBody +=
                        '<td style="border: 1px solid black; text-align: center; cursor: pointer;" onclick="fetchDetail(\'office\',\'off\',\'total\')">' +
                        prd_off_total + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        prd_shift_1_total + prd_shift_2_total + prd_shift_3_total + prd_off_total) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + ((prd_total /
                        total) * 100).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="border: 1px solid black;" rowspan="5">All<br>全部</td>';
                    tableResumeBody += '<td style="border: 1px solid black;" rowspan="4">Tidak Masuk<br>不在</td>';
                    tableResumeBody += '<td style="text-align: left;">Izin/Cuti 休暇・有休</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        cuti_tidak_masuk_shift_1 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        cuti_tidak_masuk_shift_2 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        cuti_tidak_masuk_shift_3 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        cuti_tidak_masuk_off + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        cuti_tidak_masuk_shift_1 + cuti_tidak_masuk_shift_2 + cuti_tidak_masuk_shift_3 +
                        cuti_tidak_masuk_off) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + (parseFloat(((
                        cuti_tidak_masuk_shift_1 + cuti_tidak_masuk_shift_2 +
                        cuti_tidak_masuk_shift_3 + cuti_tidak_masuk_off) / total) * 100)).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="text-align: left; border: 1px solid black;">Sakit 病欠</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        sakit_tidak_masuk_shift_1 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        sakit_tidak_masuk_shift_2 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        sakit_tidak_masuk_shift_3 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        sakit_tidak_masuk_off + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        sakit_tidak_masuk_shift_1 + sakit_tidak_masuk_shift_2 + sakit_tidak_masuk_shift_3 +
                        sakit_tidak_masuk_off) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + (parseFloat(((
                            sakit_tidak_masuk_shift_1 + sakit_tidak_masuk_shift_2 +
                            sakit_tidak_masuk_shift_3 + sakit_tidak_masuk_off) / total) * 100)).toFixed(1) +
                        '%</td>';
                    tableResumeBody += '</tr>';
                    // tableResumeBody += '<tr>';
                    // tableResumeBody +=
                    //     '<td style="color: rgb(255,204,255); text-align: left; border: 1px solid black;">Covid (PCR)<br>コロナ（PCR）</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                    //     covid_tidak_masuk_shift_1 + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                    //     covid_tidak_masuk_shift_2 + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                    //     covid_tidak_masuk_shift_3 + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                    //     covid_tidak_masuk_off + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                    //     covid_tidak_masuk_shift_1 + covid_tidak_masuk_shift_2 + covid_tidak_masuk_shift_3 +
                    //     covid_tidak_masuk_off) + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + (parseFloat(((
                    //         covid_tidak_masuk_shift_1 + covid_tidak_masuk_shift_2 +
                    //         covid_tidak_masuk_shift_3 + covid_tidak_masuk_off) / total) * 100)).toFixed(1) +
                    //     '%</td>';
                    // tableResumeBody += '</tr>';
                    // tableResumeBody += '<tr>';
                    // tableResumeBody +=
                    //     '<td style="color: rgb(255,204,255); text-align: left; border: 1px solid black;">Isoman (Non Covid)<br>自主隔離（コロナ以外）</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                    //     isoman_tidak_masuk_shift_1 + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                    //     isoman_tidak_masuk_shift_2 + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                    //     isoman_tidak_masuk_shift_3 + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                    //     isoman_tidak_masuk_off + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                    //     isoman_tidak_masuk_shift_1 + isoman_tidak_masuk_shift_2 + isoman_tidak_masuk_shift_3 +
                    //     isoman_tidak_masuk_off) + '</td>';
                    // tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + (parseFloat(((
                    //         isoman_tidak_masuk_shift_1 + isoman_tidak_masuk_shift_2 +
                    //         isoman_tidak_masuk_shift_3 + isoman_tidak_masuk_off) / total) * 100)).toFixed(1) +
                    //     '%</td>';
                    // tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody += '<td style="text-align: left; border: 1px solid black;">Libur 休日</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        libur_tidak_masuk_shift_1 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        libur_tidak_masuk_shift_2 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        libur_tidak_masuk_shift_3 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        libur_tidak_masuk_off + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        libur_tidak_masuk_shift_1 + libur_tidak_masuk_shift_2 + libur_tidak_masuk_shift_3 +
                        libur_tidak_masuk_off) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + (parseFloat(((
                            libur_tidak_masuk_shift_1 + libur_tidak_masuk_shift_2 +
                            libur_tidak_masuk_shift_3 + libur_tidak_masuk_off) / total) * 100)).toFixed(1) +
                        '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody +=
                        '<td style="text-align: left; border: 1px solid black;">Belum Konfirmasi 未確認</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        absen_tidak_masuk_shift_1 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        absen_tidak_masuk_shift_2 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        absen_tidak_masuk_shift_3 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        absen_tidak_masuk_off + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                        absen_tidak_masuk_shift_1 + absen_tidak_masuk_shift_2 + absen_tidak_masuk_shift_3 +
                        absen_tidak_masuk_off) + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + (parseFloat(((
                            absen_tidak_masuk_shift_1 + absen_tidak_masuk_shift_2 +
                            absen_tidak_masuk_shift_3 + absen_tidak_masuk_off) / total) * 100)).toFixed(1) +
                        '%</td>';
                    tableResumeBody += '</tr>';
                    tableResumeBody += '<tr>';
                    tableResumeBody +=
                        '<td style="text-align: left; border: 1px solid black;" colspan="2">Total 全部</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        tidak_masuk_shift_1 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        tidak_masuk_shift_2 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        tidak_masuk_shift_3 + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' +
                        tidak_masuk_off + '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + parseFloat(
                            tidak_masuk_shift_1 + tidak_masuk_shift_2 + tidak_masuk_shift_3 + tidak_masuk_off) +
                        '</td>';
                    tableResumeBody += '<td style="border: 1px solid black; text-align: center;">' + (parseFloat(((
                        tidak_masuk_shift_1 + tidak_masuk_shift_2 + tidak_masuk_shift_3 +
                        tidak_masuk_off) / total) * 100)).toFixed(1) + '%</td>';
                    tableResumeBody += '</tr>';


                    tableResumeFoot += '<tr style="background-color: RGB(252, 248, 227);">';
                    tableResumeFoot += '<th style="border: 1px solid black;" colspan="3">TOTAL</th>';
                    tableResumeFoot += '<th style="border: 1px solid black;">' + total_shift_1 + '</th>';
                    tableResumeFoot += '<th style="border: 1px solid black;">' + total_shift_2 + '</th>';
                    tableResumeFoot += '<th style="border: 1px solid black;">' + total_shift_3 + '</th>';
                    tableResumeFoot += '<th style="border: 1px solid black;">' + total_off + '</th>';
                    tableResumeFoot += '<th style="border: 1px solid black;">' + total + '</th>';
                    tableResumeFoot += '<th style="border: 1px solid black;"></th>';
                    tableResumeFoot += '</tr>';

                    $('#tableResumeBody').append(tableResumeBody);
                    $('#tableResumeFoot').append(tableResumeFoot);

                    var array = result.attendances;
                    var result = [];
                    var result2 = [];

                    array.reduce(function(res, value) {
                        if (!res[value.department]) {
                            res[value.department] = {
                                department: value.department,
                                shift: value.shift,
                                shift_1_hadir: 0,
                                shift_1_tidak_hadir: 0,
                                shift_1_libur: 0,
                                shift_2_hadir: 0,
                                shift_2_tidak_hadir: 0,
                                shift_2_libur: 0,
                                shift_3_hadir: 0,
                                shift_3_tidak_hadir: 0,
                                shift_3_libur: 0,
                                off_hadir: 0,
                                off_tidak_hadir: 0,
                                off_libur: 0
                            };
                            result.push(res[value.department])
                        }
                        if (value.shift == 'shift_1') {
                            res[value.department].shift_1_hadir += parseFloat(value.hadir) + parseFloat(
                                value.wfh);
                            res[value.department].shift_1_tidak_hadir += parseFloat(value.isoman) +
                                parseFloat(value.covid) + parseFloat(value.sakit) + parseFloat(value.izin) +
                                parseFloat(value.cuti) + parseFloat(value.absen);
                            res[value.department].shift_1_libur += parseFloat(value.libur);
                        }
                        if (value.shift == 'shift_2') {
                            res[value.department].shift_2_hadir += parseFloat(value.hadir) + parseFloat(
                                value.wfh);
                            res[value.department].shift_2_tidak_hadir += parseFloat(value.isoman) +
                                parseFloat(value.covid) + parseFloat(value.sakit) + parseFloat(value.izin) +
                                parseFloat(value.cuti) + parseFloat(value.absen);
                            res[value.department].shift_2_libur += parseFloat(value.libur);
                        }
                        if (value.shift == 'shift_3') {
                            res[value.department].shift_3_hadir += parseFloat(value.hadir) + parseFloat(
                                value.wfh);
                            res[value.department].shift_3_tidak_hadir += parseFloat(value.isoman) +
                                parseFloat(value.covid) + parseFloat(value.sakit) + parseFloat(value.izin) +
                                parseFloat(value.cuti) + parseFloat(value.absen);
                            res[value.department].shift_3_libur += parseFloat(value.libur);
                        }
                        if (value.shift == 'off') {
                            res[value.department].off_hadir += parseFloat(value.hadir) + parseFloat(value
                                .wfh);
                            res[value.department].off_tidak_hadir += parseFloat(value.isoman) + parseFloat(
                                    value.covid) + parseFloat(value.sakit) + parseFloat(value.izin) +
                                parseFloat(value.cuti) + parseFloat(value.absen);
                            res[value.department].off_libur += parseFloat(value.libur);
                        }
                        return res;
                    }, {});

                    // array.reduce(function(res, value) {
                    // 	if (!res[value.department]) {
                    // 		res[value.department] = { department: value.department, isoman: 0, covid: 0, sakit: 0, izin: 0, cuti: 0, wfh: 0, hadir: 0, absen: 0, libur: 0 };
                    // 		result2.push(res[value.department])
                    // 	}
                    // 	res[value.department].isoman += parseFloat(value.isoman);
                    // 	res[value.department].covid += parseFloat(value.covid);
                    // 	res[value.department].sakit += parseFloat(value.sakit);
                    // 	res[value.department].izin += parseFloat(value.izin);
                    // 	res[value.department].cuti += parseFloat(value.cuti);
                    // 	res[value.department].wfh += parseFloat(value.wfh);
                    // 	res[value.department].hadir += parseFloat(value.hadir);
                    // 	res[value.department].absen += parseFloat(value.absen);
                    // 	res[value.department].libur += parseFloat(value.libur);
                    // 	return res;
                    // }, {});

                    $('#tableDepartment').DataTable().clear();
                    $('#tableDepartment').DataTable().destroy();
                    $('#tableDepartmentBody').html("");
                    var tableDepartmentBody = "";
                    var count = 0;

                    $.each(result, function(key, value) {
                        count += 1;
                        tableDepartmentBody += '<tr>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black;">' +
                            count + '</td>';
                        if (value.department == null) {
                            tableDepartmentBody +=
                                '<td style="width: 1%; text-align: left; border: 1px solid black;">Management</td>';
                        } else {
                            tableDepartmentBody +=
                                '<td style="width: 1%; text-align: left; border: 1px solid black;">' + value
                                .department + '</td>';
                        }
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'shift_1\',\'hadir\')">' + value.shift_1_hadir +
                            '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'shift_1\',\'tidak_hadir\')">' + value
                            .shift_1_tidak_hadir + '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'shift_1\',\'libur\')">' + value.shift_1_libur +
                            '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'shift_2\',\'hadir\')">' + value.shift_2_hadir +
                            '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'shift_2\',\'tidak_hadir\')">' + value
                            .shift_2_tidak_hadir + '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'shift_2\',\'libur\')">' + value.shift_2_libur +
                            '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'shift_3\',\'hadir\')">' + value.shift_3_hadir +
                            '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'shift_3\',\'tidak_hadir\')">' + value
                            .shift_3_tidak_hadir + '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'shift_3\',\'libur\')">' + value.shift_3_libur +
                            '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'off\',\'hadir\')">' + value.off_hadir + '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'off\',\'tidak_hadir\')">' + value.off_tidak_hadir +
                            '</td>';
                        tableDepartmentBody +=
                            '<td style="width: 0.1%; text-align: center; border: 1px solid black; cursor: pointer;" onclick="fetchDetail2(\'' +
                            value.department + '\',\'off\',\'libur\')">' + value.off_libur + '</td>';
                        tableDepartmentBody += '</tr>';
                    });

                    $('#tableDepartmentBody').append(tableDepartmentBody);

                    $('#tableDepartment').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [10, 25, 50, -1],
                            ['10 rows', '25 rows', '50 rows', 'Show all']
                        ],
                        'buttons': {
                            buttons: [{
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
                        'paging': false,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': false,
                        'order': [],
                        'info': false,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        "footerCallback": function(tfoot, data, start, end, display) {
                            var intVal = function(i) {
                                return typeof i === 'string' ?
                                    i.replace(/[\$,]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                            };
                            var api = this.api();

                            var total_shift_1_hadir = api.column(2).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(2).footer()).html(total_shift_1_hadir.toLocaleString());

                            var total_shift_1_tidak_hadir = api.column(3).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(3).footer()).html(total_shift_1_tidak_hadir.toLocaleString());

                            var total_shift_1_libur = api.column(4).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(4).footer()).html(total_shift_1_libur.toLocaleString());

                            var total_shift_2_hadir = api.column(5).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(5).footer()).html(total_shift_2_hadir.toLocaleString());

                            var total_shift_2_tidak_hadir = api.column(6).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(6).footer()).html(total_shift_2_tidak_hadir.toLocaleString());

                            var total_shift_2_libur = api.column(7).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(7).footer()).html(total_shift_2_libur.toLocaleString());

                            var total_shift_3_hadir = api.column(8).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(8).footer()).html(total_shift_3_hadir.toLocaleString());

                            var total_shift_3_tidak_hadir = api.column(9).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(9).footer()).html(total_shift_3_tidak_hadir.toLocaleString());

                            var total_shift_3_libur = api.column(10).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(10).footer()).html(total_shift_3_libur.toLocaleString());

                            var total_off_hadir = api.column(11).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(11).footer()).html(total_off_hadir.toLocaleString());

                            var total_off_tidak_hadir = api.column(12).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(12).footer()).html(total_off_tidak_hadir.toLocaleString());

                            var total_off_libur = api.column(13).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(13).footer()).html(total_off_libur.toLocaleString());
                        },
                    });
                    $('#loading').hide();
                } else {
                    $('#loading').hide();
                    alert('Attempt to retrieve data failed');
                }
            });
        }

        function notZero(n) {
            n = +n; // Coerce to number.
            if (!n) { // Matches +0, -0, NaN
                return 1;
            }
            return n;
        }
    </script>
@stop
