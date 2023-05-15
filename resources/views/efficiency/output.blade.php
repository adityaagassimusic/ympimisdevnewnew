@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        th,
        td {
            white-space: nowrap;
        }

        div.dataTables_wrapper {
            margin: 0 auto;
        }

        tr:hover td {
            background-color: #7dfa8c !important;
            color: black !important;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            padding-top: 2px;
            padding-bottom: 2px;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            padding-top: 2px;
            padding-bottom: 2px;
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
        <div class="row" style="padding-top: 20px; min-height: 100px;">
            <div class="col-xs-2">
                <div class="input-group date pull-right" style="text-align: center;">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control monthpicker" name="filterPeriod" id="filterPeriod"
                        placeholder="Pilih Periode" onchange="fetchData()">
                </div>
                <a class="btn btn-success" onclick="modalAddMaterial()" style="width: 100%; margin-top: 5px;"><i
                        class="fa fa-user-plus"></i> Tambah Material</a>
                <a class="btn btn-danger" onclick="modalUploadMaterial()" style="width: 100%; margin-top: 5px;"><i
                        class="fa fa-upload"></i> Upload Material</a>
                <div class="input-group pull-right" style="margin-bottom: 5px; margin-top: 10px;">
                    <div class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </div>
                    <select class="form-control select2" id="searchRemark" style="width: 100%;"
                        data-placeholder="Cari Remark">
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
                <div class="input-group pull-right" style="margin-bottom: 5px;">
                    <div class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </div>
                    <input class="form-control" placeholder="Cari ID Karyawan" style="width: 100%;" type="text"
                        id="searchEmployeeId">
                </div>
                <div class="input-group pull-right" style="margin-bottom: 5px;">
                    <div class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </div>
                    <input class="form-control" placeholder="Cari Nama Karyawan" style="width: 100%;" type="text"
                        id="searchEmployeeName">
                </div>
                <div class="input-group pull-right" style="margin-bottom: 5px;">
                    <div class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </div>
                    <input class="form-control" placeholder="Cari Lokasi" style="width: 100%;" type="text"
                        id="searchLocation">
                </div>
            </div>
            <div class="col-xs-5">
                <table id="tableResume" class="table table-bordered table-hover">
                    <thead style="background-color: #605ca8; color: white;">
                        <tr>
                            <th style="width: 3%; text-align: center;">Resume</th>
                            <th style="width: 1%; text-align: center;">Total Perolehan</th>
                            <th style="width: 1%; text-align: center;">Total Jam</th>
                        </tr>
                    </thead>
                    <tbody id="tableResumeBody">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: center; background-color: #fffcb7;">Total</th>
                            <th style="text-align: center; background-color: #fffcb7;"></th>
                            <th style="text-align: center; background-color: #fffcb7;"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="box box-solid">
            <div class="box-body">
                <center>
                    <span
                        style="background-color: #00a65a; color: white; font-weight: bold; border: 1px solid black; font-size: 1vw;">
                        &nbsp;&nbsp;&nbsp;Total Perolehan&nbsp;&nbsp;
                    </span>&nbsp;&nbsp;&nbsp;
                    <span
                        style="background-color: #dd4b39; color: white; font-weight: bold; border: 1px solid black; font-size: 1vw;">
                        &nbsp;&nbsp;&nbsp;Total Jam&nbsp;&nbsp;
                    </span>
                </center>
                <table id="tableResult" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <table id="tableDetail" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 1%; text-align: center;">Move Type</th>
                                    <th style="width: 1%; text-align: center;">Material</th>
                                    <th style="width: 4%; text-align: left;">Deskripsi</th>
                                    <th style="width: 1%; text-align: center;">Std Time</th>
                                    <th style="width: 1%; text-align: center;">Jumlah</th>
                                    <th style="width: 1%; text-align: center;">Output</th>
                                    <th style="width: 1%; text-align: right;">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody id="tableDetailBody">
                            </tbody>
                            <tfoot id="tableDetailFoot" style="background-color: #fffcb7">
                                <tr>
                                    <th style="text-align: center;" colspan="4">Total</th>
                                    <th style="text-align: center;" id="tableDetailTotalQuantity"></th>
                                    <th style="text-align: center;" id="tableDetailTotalOutput"></th>
                                    <th style="text-align: center;"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditMaterial">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <input type="hidden" id="editId">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-4 control-label">Material<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="editMaterialNumber" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-4 control-label">Deskripsi<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="editMaterialDescription" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-4 control-label">Standard
                                        Time<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-xs-3">
                                        <input type="text" class="form-control" id="editStandardTime"
                                            placeholder="Masukkan Standard Time" style="width: 100%;">
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
                                onclick="removeMaterial()">HAPUS</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.1vw; width: 20%;"
                                onclick="editMaterial()">UBAH</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddMaterial">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Material<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-xs-8">
                                        <select class="form-control select2" id="addMaterialNumber"
                                            data-placeholder="Pilih Material" style="width: 100%;">
                                            <option></option>
                                            @foreach ($materials as $material)
                                                <option value="{{ $material->material_number }}">
                                                    {{ $material->material_number }} -
                                                    {{ $material->material_description }}
                                                    - {{ $material->storage_location }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Standard
                                        Time<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-xs-3">
                                        <input type="text" class="form-control" id="addStandardTime"
                                            placeholder="Masukkan Standard Time" style="width: 100%;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Remark<span class="text-red">*</span>
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
                                    onclick="addMaterial()">TAMBAH</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalUploadMaterial">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-2 control-label text-red">Upload<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-xs-10">
                                        <textarea rows="4" placeholder="Format copy dari excel: [remark][gmc][description][sloc][std_time]"
                                            id="uploadMaterial" style="width: 100%; border: 1px solid red;"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <div style="margin-top: 20px;">
                                <button class="btn btn-danger pull-right"
                                    style="font-weight: bold; font-size: 1.1vw; width: 20%;"
                                    onclick="uploadMaterial()">UPLOAD</button>
                            </div>
                        </div>
                    </div>
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
    <script src="{{ url('js/dataTables.fixedColumns.min.js') }}"></script>
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
            $('#searchRemark').select2({
                allowClear: true,
            });
            fetchData();
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var department = "{{ $department }}";
        var period = "";
        var materials = [];
        var production_results = [];
        var outputs = [];
        var calendars = [];
        var resumes = [];
        var table;

        $(function() {
            $('#editRemark').select2({
                dropdownParent: $('#modalEditMaterial'),
                minimumResultsForSearch: -1
            });
            $('#addMaterialNumber').select2({
                dropdownParent: $('#modalAddMaterial')
            });
        });

        function modalUploadMaterial() {
            $('#uploadFiscalYear').prop('selectedIndex', 0).change();
            $('#uploadMaterial').val();
            $('#modalUploadMaterial').modal('show');
        }

        function uploadMaterial() {
            if (confirm(
                    "Semua data " + period + " akan dihapus dan digantikan data baru"
                )) {
                $('#loading').show();
                var upload_material = $('#uploadMaterial').val();

                var data = {
                    upload_material: upload_material,
                    period: period,
                    department: department,
                }
                $.post('{{ url('upload/efficiency/material') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchData();
                        $('#modalUploadMaterial').modal('hide');
                        openSuccessGritter('Berhasil!', result.message);
                        audio_ok.play();
                        $('#loading').hide();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        $('#loading').hide();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function modalAddMaterial() {
            $('#addEmployeeId').val('');
            $('#addRemark').prop('selectedIndex', 0).change();
            $('#modalAddMaterial').modal('show');
        }

        function addMaterial() {
            if (confirm("Apakah anda yakin akan menambahkan data material ini?")) {
                $('#loading').show();
                var material_number = $('#addMaterialNumber').val();
                var standard_time = $('#addStandardTime').val();
                var remark = $('#addRemark').val();

                if (material_number == "" || remark == "" || standard_time == "") {
                    openErrorGritter('Gagal!', 'Semua data harus terisi');
                    audio_error.play();
                    $('#loading').hide();
                    return false;
                }

                var data = {
                    material_number: material_number,
                    remark: remark,
                    department: department,
                    period: period,
                    standard_time: standard_time,
                }
                $.post('{{ url('edit/efficiency/material_add') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchData();
                        $('#addMaterialNumber').prop('selectedIndex', 0).change();
                        $('#modalAddMaterial').modal('hide');
                        openSuccessGritter('Berhasil!', result.message);
                        audio_ok.play();
                        $('#loading').hide();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        $('#loading').hide();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function modalEditMaterial(id) {
            $.each(materials, function(key, value) {
                if (value.id == id) {
                    $('#editId').val(value.id);
                    $('#editMaterialNumber').val(value.material_number);
                    $('#editMaterialDescription').val(value.material_description);
                    $('#editStandardTime').val(value.standard_time);
                    $('#editRemark').val(value.remark).change();
                    $('#modalEditMaterial').modal('show');
                    return false;
                }
            });
        }

        function modalDetail(material_number, material_description, std_time, result_date) {
            $('#loading').show();
            var data = {
                material_number: material_number,
                result_date: result_date,
                period: period,
            }
            $.get('{{ url('fetch/efficiency/output_detail') }}', data, function(result, status, xhr) {
                if (result.status) {
                    var tableDetailBody = "";
                    $('#tableDetailBody').html("");
                    $('#tableDetail').DataTable().clear();
                    $('#tableDetail').DataTable().destroy();
                    tableDetailTotalQuantity = 0;
                    tableDetailTotalOutput = 0;

                    $.each(result.production_results, function(key, value) {
                        tableDetailBody += '<tr>';
                        tableDetailBody += '<td style="width: 1%; text-align: center;">' + value.category +
                            '</td>';
                        tableDetailBody += '<td style="width: 1%; text-align: center;">' + material_number +
                            '</td>';
                        tableDetailBody += '<td style="width: 4%; text-align: left;">' +
                            material_description + '</td>';
                        tableDetailBody += '<td style="width: 1%; text-align: center;">' +
                            std_time + '</td>';
                        tableDetailBody += '<td style="width: 1%; text-align: center;">' + parseFloat(value
                                .quantity) +
                            '</td>';
                        tableDetailBody += '<td style="width: 1%; text-align: center;">' + parseFloat(value
                                .quantity) * parseFloat(std_time) +
                            '</td>';
                        tableDetailBody += '<td style="width: 1%; text-align: right;">' + value
                            .result_date +
                            '</td>';
                        tableDetailBody += '</tr>';
                        tableDetailTotalQuantity += parseFloat(value.quantity);
                        tableDetailTotalOutput += parseFloat(value.quantity) * parseFloat(std_time);
                    });
                    $('#tableDetailBody').append(tableDetailBody);

                    $('#tableDetailTotalQuantity').text(tableDetailTotalQuantity);
                    $('#tableDetailTotalOutput').text(tableDetailTotalOutput);

                    $('#modalDetail').modal('show');
                    $('#loading').hide();
                } else {
                    openErrorGritter('Gagal!', result.message);
                    audio_error.play();
                    $('#loading').hide();
                    return false;
                }
            });
        }

        function editMaterial() {
            if (confirm("Apakah anda yakin akan mengubah data material ini?")) {
                $('#loading').show();
                var id = $('#editId').val();
                var material_number = $('#editMaterialNumber').val();
                var standard_time = $('#editStandardTime').val();
                var remark = $('#editRemark').val();
                var data = {
                    id: id,
                    material_number: material_number,
                    remark: remark,
                    department: department,
                    period: period,
                    standard_time: standard_time,
                }
                $.post('{{ url('edit/efficiency/material_edit') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchData();
                        $('#modalEditMaterial').modal('hide');
                        openSuccessGritter('Berhasil!', result.message);
                        audio_ok.play();
                        $('#loading').hide();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        $('#loading').hide();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function removeMaterial() {
            if (confirm("Apakah anda yakin akan menghapus data material ini?")) {
                $('#loading').show();
                var id = $('#editId').val();
                var data = {
                    id: id,
                }
                $.post('{{ url('edit/efficiency/material_remove') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchData();
                        $('#modalEditMaterial').modal('hide');
                        openSuccessGritter('Berhasil!', result.message);
                        audio_ok.play();
                        $('#loading').hide();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        $('#loading').hide();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function fetchData() {
            $('#loading').show();
            period = $('#filterPeriod').val();
            var data = {
                period: period,
                department: department,
            }
            $.get('{{ url('fetch/efficiency/output') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#period').html(result.period_title);
                    period = result.period;
                    materials = result.materials;
                    production_results = result.production_results;
                    outputs = result.outputs;
                    calendars = result.calendars;
                    resumes = result.resumes;

                    $('#tableResume').DataTable().clear();
                    $('#tableResume').DataTable().destroy();
                    var tableResumeBody = "";
                    $('#tableResumeBody').html("");

                    $.each(resumes, function(key, value) {
                        tableResumeBody += '<tr>';
                        tableResumeBody += '<td style="width: 3%; text-align: center;">' + value
                            .remark +
                            '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + value
                            .total_result + '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + value
                            .total_hour + '</td>';
                        tableResumeBody += '</tr>';
                    });

                    $('#tableResumeBody').append(tableResumeBody);


                    $('#tableResult').DataTable().clear();
                    $('#tableResult').DataTable().destroy();
                    var tableResult = "";
                    $('#tableResult').html("");

                    tableResult += '<thead>';
                    tableResult += '<tr>';
                    tableResult +=
                        '<th style="width: 150px; font-size: 12px; text-align: center; z-index: 100; background-color: #605ca8; color: white;">Remark</th>';
                    tableResult +=
                        '<th style="width: 70px; font-size: 12px; text-align: center; z-index: 100; background-color: #605ca8; color: white;">Material</th>';
                    tableResult +=
                        '<th style="width: 210px; font-size: 12px; text-align: left; z-index: 100; background-color: #605ca8; color: white;">Deskripsi</th>';
                    tableResult +=
                        '<th style="width: 70px; font-size: 12px; text-align: center; z-index: 100; background-color: #605ca8; color: white;">Lokasi</th>';
                    tableResult +=
                        '<th style="width: 70px; font-size: 12px; text-align: right; z-index: 100; background-color: #605ca8; color: white;">Std</th>';

                    $.each(calendars, function(key, value) {
                        tableResult +=
                            '<th style="vertical-align: top; width: 15px; font-size: 12px; text-align: center; background-color: #00a65a; color: white;">' +
                            value.header + '</th>';
                    });

                    $.each(calendars, function(key, value) {
                        tableResult +=
                            '<th style="vertical-align: top; width: 15px; font-size: 12px; text-align: center; background-color: #dd4b39; color: white;">' +
                            value.header + '</th>';
                    });

                    tableResult += '</tr>';
                    tableResult += '</thead>';

                    tableResult += '<tbody style="background-color: white;">';

                    for (var i = 0; i < materials.length; i++) {
                        tableResult += '<tr>';
                        tableResult +=
                            '<td style="background-color: white; font-size: 12px; width: 150px; text-align: center;">' +
                            materials[i].remark + '</td>';
                        tableResult +=
                            '<td style="background-color: white; font-size: 12px; width: 70px; text-align: center;"><a href="javascript:void(0)" onclick="modalEditMaterial(\'' +
                            materials[i].id + '\')">' + materials[i].material_number +
                            '</a></td>';
                        tableResult +=
                            '<td style="background-color: white; font-size: 12px; width: 210px; text-align: left;">' +
                            materials[i].material_description + '</td>';
                        tableResult +=
                            '<td style="background-color: white; font-size: 12px; width: 70px; text-align: center;">' +
                            materials[i].issue_location + '</td>';
                        tableResult +=
                            '<td style="background-color: white; font-size: 12px; width: 70px; text-align: right;">' +
                            materials[i].standard_time.toFixed(3) + '</td>';

                        for (var j = 0; j < calendars.length; j++) {
                            var color = "";
                            if (calendars[j].remark == 'H') {
                                color = "background-color: grey; color: white;";
                            }
                            var found = false;

                            for (var k = 0; k < outputs.length; k++) {
                                if (calendars[j].week_date == outputs[k].result_date && materials[i]
                                    .material_number == outputs[k].material_number) {
                                    if (outputs[k].quantity != 0) {
                                        color = "background-color: #ff6090; color: black;";
                                    }
                                    tableResult +=
                                        '<td style="vertical-align: top; width: 15px; font-size: 12px; text-align: center; ' +
                                        color + ' cursor: pointer;" onclick="modalDetail(\'' + outputs[k]
                                        .material_number +
                                        '\',\'' + outputs[k].material_description + '\',\'' + materials[i]
                                        .standard_time + '\',\'' + calendars[j]
                                        .week_date +
                                        '\')">' + outputs[k].quantity +
                                        '</td>';
                                    found = true;
                                }
                            }
                            if (found == false) {
                                tableResult +=
                                    '<td style="vertical-align: top; width: 15px; font-size: 12px; text-align: center; ' +
                                    color + '">0</td>';
                            }
                        }

                        for (var j = 0; j < calendars.length; j++) {
                            var color = "";
                            if (calendars[j].remark == 'H') {
                                color = "background-color: grey; color: white;";
                            }
                            var found = false;

                            for (var k = 0; k < outputs.length; k++) {
                                if (calendars[j].week_date == outputs[k].result_date && materials[i]
                                    .material_number == outputs[k].material_number) {
                                    if (outputs[k].quantity != 0) {
                                        color = "background-color: #ff6090; color: black;";
                                    }
                                    tableResult +=
                                        '<td style="vertical-align: top; width: 15px; font-size: 12px; text-align: center; ' +
                                        color + ' cursor: pointer;" onclick="modalDetail(\'' + outputs[k]
                                        .material_number +
                                        '\',\'' + outputs[k].material_description + '\',\'' + materials[i]
                                        .standard_time + '\',\'' + calendars[j]
                                        .week_date + '\')">' + outputs[k].output_hour +
                                        '</td>';
                                    found = true;
                                }
                            }
                            if (found == false) {
                                tableResult +=
                                    '<td style="vertical-align: top; width: 15px; font-size: 12px; text-align: center; ' +
                                    color + '">0</td>';
                            }
                        }

                        tableResult += '</tr>';
                    }

                    tableResult += '</tbody>';

                    $('#tableResult').append(tableResult);

                    table = $('#tableResult').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [25, 50, 100, -1],
                            ['25 rows', '50 rows', '100 rows', 'Show all']
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
                        "ordering": false,
                        "scrollY": "500px",
                        "scrollX": true,
                        "scrollCollapse": true,
                        "paging": true,
                        "fixedColumns": {
                            left: 5,
                        },
                    });

                    $('#tableResume').DataTable({
                        "searching": false,
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        "ordering": false,
                        "paging": false,
                        "footerCallback": function(tfoot, data, start, end, display) {
                            var intVal = function(i) {
                                return typeof i === 'string' ?
                                    i.replace(/[\$,]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                            };
                            var api = this.api();

                            var Packing = api.column(1).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(1).footer()).html(Packing.toLocaleString());

                            var act = api.column(2).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(2).footer()).html(act.toLocaleString());
                        }
                    });

                    $('#loading').hide();
                } else {
                    openErrorGritter('Gagal!', result.message);
                    audio_error.play();
                    $('#loading').hide();
                    return false;
                }
            });
        }

        $('#searchRemark').on('change', function() {
            table.column(0).search($(this).val()).draw();
        });
        $('#searchMaterial').keyup(function() {
            table.column(1).search($(this).val()).draw();
        });
        $('#searchDescription').keyup(function() {
            table.column(2).search($(this).val()).draw();
        });
        $('#searchLocation').keyup(function() {
            table.column(3).search($(this).val()).draw();
        });

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
