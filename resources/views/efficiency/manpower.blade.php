@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        tr:hover {
            background-color: #7dfa8c !important;
        }

        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
            color: black;
            text-align: center;
        }

        thead select {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
            color: black;
            text-align: center;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            padding-top: 5px;
            padding-bottom: 5px;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        td:hover {
            overflow: visible;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
            <span id="period">(Periode {{ date('F Y') }})</span>
        </h1>
    </section>
@endsection
@section('content')
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row" style="padding-top: 20px;">
            <div class="col-xs-2">
                <div class="input-group date pull-right" style="text-align: center;">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control monthpicker" name="filterPeriod" id="filterPeriod"
                        placeholder="Pilih Periode" onchange="fetchData()">
                </div>
                <a class="btn btn-success" onclick="modalAddManpower()" style="width: 100%; margin-top: 5px;"><i
                        class="fa fa-user-plus"></i> Tambah Manpower</a>
            </div>
            <div class="col-xs-2">
                <div class="info-box" style="min-height: 70px;">
                    <span class="info-box-icon"
                        style="background-color: #f5d547; height: 70px; font-size: 40px; line-height: 75px;"><i
                            class="fa fa-chain"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL DIRECT</span>
                        <span class="info-box-number" id="total_direct">90</span>
                    </div>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="info-box" style="min-height: 70px;">
                    <span class="info-box-icon"
                        style="background-color: #dd4b39; height: 70px; font-size: 40px; line-height: 75px;"><i
                            class="fa fa-chain-broken"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL INDIRECT</span>
                        <span class="info-box-number" id="total_indirect">90</span>
                    </div>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="info-box" style="min-height: 70px;">
                    <span class="info-box-icon"
                        style="background-color: #f39c12; height: 70px; font-size: 40px; line-height: 75px;"><i
                            class="fa fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL MANPOWER</span>
                        <span class="info-box-number" id="total_manpower">90</span>
                    </div>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="info-box" style="min-height: 70px;">
                    <span class="info-box-icon"
                        style="background-color: #00a65a; height: 70px; font-size: 40px; line-height: 75px;"><i
                            class="fa fa-street-view"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL SUB LEADER</span>
                        <span class="info-box-number" id="total_subleader">90</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="padding-top: 0px;">
            <div class="col-xs-12">
                <table id="tableManpower" class="table table-bordered table-hover">
                    <thead style="background-color: #605ca8; color: white;">
                        <tr>
                            <th style="width: 0.1%; text-align: center;">ID</th>
                            <th style="width: 2%; text-align: left;">Nama</th>
                            <th style="width: 1%; text-align: center;">Posisi</th>
                            <th style="width: 0.1%; text-align: center;">Jenis Pekerjaan</th>
                            <th style="width: 3%; text-align: left;">Departemen</th>
                            <th style="width: 1%; text-align: center;">Remark</th>
                            <th style="width: 0.1%; text-align: center;">Status</th>
                            <th style="width: 1%; text-align: left;">Last Update By</th>
                            <th style="width: 0.1%; text-align: right;">Last Update At</th>
                        </tr>
                    </thead>
                    <tbody id="tableManpowerBody">
                    </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalAddManpower">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-body">
                            <form class="form-horizontal">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label style="padding-top: 0;" for="" class="col-sm-4 control-label">ID
                                            Karyawan<span class="text-red">*</span>
                                            :</label>
                                        <div class="col-xs-6">
                                            <select class="form-control select2" id="addEmployeeId"
                                                data-placeholder="Pilih ID Karyawan" style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label style="padding-top: 0;" for=""
                                            class="col-sm-4 control-label">Remark<span class="text-red">*</span>
                                            :</label>
                                        <div class="col-sm-6">
                                            <select class="form-control select2" id="addRemark"
                                                data-placeholder="Pilih Remark" style="width: 100%;">
                                                <option value=""></option>
                                                @foreach ($remarks as $remark)
                                                    @if ($remark['department'] == $department)
                                                        @foreach ($remark['remark'] as $row)
                                                            <option value="{{ $row }}">{{ $row }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="col-md-12">
                                <div class="col-md-12" style="margin-top: 20px;">
                                    <button class="btn btn-success pull-right"
                                        style="font-weight: bold; font-size: 1.1vw; width: 20%;"
                                        onclick="addManpower()">TAMBAH</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditManpower">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="modal-body">
                            <form class="form-horizontal">
                                <div class="col-md-12">
                                    <input type="hidden" id="editId">
                                    <div class="form-group">
                                        <label style="padding-top: 0;" for="" class="col-sm-4 control-label">ID
                                            Karyawan<span class="text-red">*</span>
                                            :</label>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control" id="editEmployeeId" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label style="padding-top: 0;" for=""
                                            class="col-sm-4 control-label">Nama<span class="text-red">*</span>
                                            :</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" id="editEmployeeName" disabled>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label style="padding-top: 0;" for=""
                                            class="col-sm-4 control-label">Status
                                            Pekerjaan<span class="text-red">*</span>
                                            :</label>
                                        <div class="col-sm-3">
                                            <select class="form-control select2" id="editJobStatus"
                                                data-placeholder="Pilih Job Status" style="width: 100%;">
                                                <option value=""></option>
                                                <option value="DIRECT">DIRECT</option>
                                                <option value="INDIRECT">INDIRECT</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label style="padding-top: 0;" for=""
                                            class="col-sm-4 control-label">Remark<span class="text-red">*</span>
                                            :</label>
                                        <div class="col-sm-6">
                                            <select class="form-control select2" id="editRemark"
                                                data-placeholder="Pilih Remark" style="width: 100%;">
                                                @foreach ($remarks as $remark)
                                                    @if ($remark['department'] == $department)
                                                        @foreach ($remark['remark'] as $row)
                                                            <option value="{{ $row }}">{{ $row }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="col-md-12" style="margin-top: 20px;">
                                <button class="btn btn-danger pull-left"
                                    style="font-weight: bold; font-size: 1.1vw; width: 20%;"
                                    onclick="removeManpower()">HAPUS</button>
                                <button class="btn btn-success pull-right"
                                    style="font-weight: bold; font-size: 1.1vw; width: 20%;"
                                    onclick="editManpower()">UBAH</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('scripts')
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

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });
            fetchData();
        });

        $(function() {
            $('#addEmployeeId').select2({
                dropdownParent: $('#modalAddManpower')
            });
            $('#addRemark').select2({
                dropdownParent: $('#modalAddManpower'),
                minimumResultsForSearch: -1
            });
            $('#editJobStatus').select2({
                dropdownParent: $('#modalEditManpower'),
                minimumResultsForSearch: -1
            });
            $('#editRemark').select2({
                dropdownParent: $('#modalEditManpower'),
                minimumResultsForSearch: -1
            });
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var department = "{{ $department }}";
        var manpowers = [];
        var period = "";

        function addManpower() {
            if (confirm("Apakah anda yakin akan menambahkan data manpower ini?")) {
                var employee_id = $('#addEmployeeId').val();
                var remark = $('#addRemark').val();

                if (employee_id == "" || remark == "") {
                    openErrorGritter('Gagal!', 'Semua data harus terisi');
                    audio_error.play();
                    return false;
                }

                var data = {
                    employee_id: employee_id,
                    remark: remark,
                    department: department,
                    period: period,
                }
                $.post('{{ url('edit/efficiency/manpower_add') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchData();
                        $('#addEmployeeId').prop('selectedIndex', 0).change();
                        $('#modalAddManpower').modal('hide');
                        openSuccessGritter('Berhasil!', result.message);
                        audio_ok.play();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function editManpower() {
            if (confirm("Apakah anda yakin akan mengubah data manpower ini?")) {
                var id = $('#editId').val();
                var employee_id = $('#editEmployeeId').val();
                var job_status = $('#editJobStatus').val();
                var remark = $('#editRemark').val();
                var data = {
                    id: id,
                    employee_id: employee_id,
                    job_status: job_status,
                    remark: remark,
                    department: department,
                    period: period,
                }
                $.post('{{ url('edit/efficiency/manpower_edit') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchData();
                        $('#modalEditManpower').modal('hide');
                        openSuccessGritter('Berhasil!', result.message);
                        audio_ok.play();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function removeManpower() {
            if (confirm("Apakah anda yakin akan menghapus data manpower ini?")) {
                var id = $('#editId').val();
                var data = {
                    id: id,
                }
                $.post('{{ url('edit/efficiency/manpower_remove') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchData();
                        $('#modalEditManpower').modal('hide');
                        openSuccessGritter('Berhasil!', result.message);
                        audio_ok.play();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function modalAddManpower() {
            $('#addEmployeeId').prop('selectedIndex', 0).change();
            $('#addRemark').prop('selectedIndex', 0).change();
            $('#modalAddManpower').modal('show');
        }

        function modalEditManpower(id) {
            $.each(manpowers, function(key, value) {
                if (value.id == id) {
                    $('#editId').val(value.id);
                    $('#editEmployeeId').val(value.employee_id);
                    $('#editEmployeeName').val(value.employee_name);
                    $('#editJobStatus').val(value.job_status).change();
                    $('#editRemark').val(value.remark).change();
                    $('#modalEditManpower').modal('show');
                    return false;
                }
            });
        }

        function fetchData() {
            period = $('#filterPeriod').val();
            var data = {
                period: period,
                department: department,
            }
            $.get('{{ url('fetch/efficiency/manpower') }}', data, function(result, status, xhr) {
                if (result.status) {
                    manpowers = result.manpowers;
                    period = result.period;
                    $('#tableManpower').DataTable().clear();
                    $('#tableManpower').DataTable().destroy();
                    $('#tableManpowerBody').html("");
                    var tableManpowerBody = "";
                    $('#addEmployeeId').html("");
                    var addEmployeeId = "<option></option>";

                    var total_direct = 0;
                    var total_indirect = 0;
                    var total_manpower = 0;
                    var total_subleader = 0;

                    $('#period').html(result.period_title);

                    $.each(manpowers, function(key, value) {
                        if (value.department == department) {
                            if (value.job_status == 'DIRECT') {
                                total_direct += 1;
                            }
                            if (value.job_status == 'INDIRECT') {
                                total_indirect += 1;
                            }
                            total_manpower += 1;
                            if (value.position == 'Sub Leader') {
                                total_subleader += 1;
                            }
                            tableManpowerBody += '<tr>';
                            tableManpowerBody +=
                                '<td style="width: 0.1%; text-align: center;"><a href="javascript:void(0)" onclick="modalEditManpower(\'' +
                                value.id + '\')">' + value
                                .employee_id +
                                '</a></td>';
                            tableManpowerBody += '<td style="width: 2%; text-align: left;">' + value
                                .employee_name +
                                '</td>';
                            tableManpowerBody += '<td style="width: 1%; text-align: center;">' + value
                                .position +
                                '</td>';
                            tableManpowerBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .job_status +
                                '</td>';
                            tableManpowerBody += '<td style="width: 3%; text-align: left;">' + value
                                .department +
                                '</td>';
                            tableManpowerBody += '<td style="width: 1%; text-align: center;">' + value
                                .remark +
                                '</td>';
                            tableManpowerBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .employment_status + '</td>';
                            tableManpowerBody += '<td style="width: 1%; text-align: left;">' + value
                                .updated_by + '<br>' + value.updated_by_name +
                                '</td>';
                            tableManpowerBody += '<td style="width: 0.1%; text-align: right;">' + value
                                .updated_at +
                                '</td>';
                            tableManpowerBody += '</tr>';
                        } else {
                            addEmployeeId += '<option value="' + value.employee_id + '">' + value
                                .employee_id + ' - ' + value.employee_name + '</option>';
                        }
                    });

                    $('#total_direct').html(total_direct + ' <small>Orang</small>');
                    $('#total_indirect').html(total_indirect + ' <small>Orang</small>');
                    $('#total_manpower').html(total_manpower + ' <small>Orang</small>');
                    $('#total_subleader').html(total_subleader + ' <small>Orang</small>');

                    $('#addEmployeeId').append(addEmployeeId);
                    $('#tableManpowerBody').append(tableManpowerBody);

                    $('#tableManpower tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search ' +
                            title + '" size="10"/>');
                    });

                    var table = $('#tableManpower').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [10, 25, -1],
                            ['10 rows', '25 rows', 'Show all']
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
                        "searching": true,
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        'ordering': true,
                        initComplete: function() {
                            this.api()
                                .columns([2, 3, 5, 6])
                                .every(function(dd) {
                                    var column = this;
                                    var theadname = $("#tableManpower th").eq([dd]).text();
                                    var select = $(
                                            '<select><option value="" style="font-size:11px;">All</option></select>'
                                        )
                                        .appendTo($(column.footer()).empty())
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(
                                                    this)
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
                                            if ($("#tableManpower th").eq([dd]).text() ==
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

                    table.columns().every(function() {
                        var that = this;
                        $('#search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('#tableManpower tfoot tr').appendTo('#tableManpower thead');
                } else {
                    openErrorGritter('Gagal!', result.message);
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
    </script>
@endsection
