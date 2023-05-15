@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        tr:hover td {
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
            <span id="period">()</span>
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
                <select class="form-control select2" id="filterPeriod" style="width: 100%;"
                    data-placeholder="Pilih Fiscal Year" onchange="fetchData()">
                    <option value=""></option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->fiscal_year }}">{{ $period->fiscal_year }}</option>
                    @endforeach
                </select>
                <a class="btn btn-success" onclick="modalAddMaterial()" style="width: 100%; margin-top: 5px;"><i
                        class="fa fa-user-plus"></i> Tambah Material</a>
                <a class="btn btn-danger" onclick="modalUploadMaterial()" style="width: 100%; margin-top: 5px;"><i
                        class="fa fa-upload"></i> Upload Material</a>
            </div>
            <div class="col-xs-2">
                <div class="info-box" style="min-height: 70px;">
                    <span class="info-box-icon"
                        style="background-color: #f39c12; height: 70px; font-size: 40px; line-height: 75px;"><i
                            class="fa fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">TOTAL MATERIAL</span>
                        <span class="info-box-number" id="total_material">90</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="padding-top: 10px;">
            <div class="col-xs-12">
                <table id="tableMaterial" class="table table-bordered table-hover">
                    <thead style="background-color: #605ca8; color: white;">
                        <tr>
                            <th style="width: 0.1%; text-align: center;">Material</th>
                            <th style="width: 2%; text-align: left;">Deskripsi</th>
                            <th style="width: 3%; text-align: left;">Departemen</th>
                            <th style="width: 1%; text-align: center;">Remark</th>
                            <th style="width: 1%; text-align: center;">Standard Time</th>
                            <th style="width: 1%; text-align: left;">Last Update By</th>
                            <th style="width: 0.1%; text-align: right;">Last Update At</th>
                        </tr>
                    </thead>
                    <tbody id="tableMaterialBody">
                    </tbody>
                    <tfoot>
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
    </section>

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
                                                    {{ $material->material_number }} - {{ $material->material_description }}
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
            $('#filterPeriod').select2({
                minimumResultsForSearch: -1
            });
            fetchData();
        });

        $(function() {
            $('#addMaterialNumber').select2({
                dropdownParent: $('#modalAddMaterial')
            });
            $('#addRemark').select2({
                dropdownParent: $('#modalAddMaterial'),
                minimumResultsForSearch: -1
            });
            $('#editRemark').select2({
                dropdownParent: $('#modalEditMaterial'),
                minimumResultsForSearch: -1
            });
            $('#uploadFiscalYear').select2({
                dropdownParent: $('#modalUploadMaterial'),
                minimumResultsForSearch: -1
            });
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var materials = [];
        var department = "{{ $department }}";
        var period = "";

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

        function modalAddMaterial() {
            $('#addEmployeeId').val('');
            $('#addRemark').prop('selectedIndex', 0).change();
            $('#modalAddMaterial').modal('show');
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

        function fetchData() {
            $('#loading').show();
            period = $('#filterPeriod').val();
            var data = {
                period: period,
                department: department,
            }
            $.get('{{ url('fetch/efficiency/material') }}', data, function(result, status, xhr) {
                if (result.status) {
                    materials = result.materials;
                    period = result.period;
                    $('#tableMaterial').DataTable().clear();
                    $('#tableMaterial').DataTable().destroy();
                    $('#tableMaterialBody').html("");
                    var tableMaterialBody = "";

                    var total_direct = 0;
                    var total_indirect = 0;
                    var total_material = 0;
                    var total_subleader = 0;

                    $('#period').html(result.period_title);

                    $.each(materials, function(key, value) {
                        if (value.department == department) {
                            total_material += 1;

                            tableMaterialBody += '<tr>';
                            tableMaterialBody +=
                                '<td style="width: 0.1%; text-align: center;"><a href="javascript:void(0)" onclick="modalEditMaterial(\'' +
                                value.id + '\')">' + value
                                .material_number +
                                '</a></td>';
                            tableMaterialBody += '<td style="width: 2%; text-align: left;">' + value
                                .material_description +
                                '</td>';
                            tableMaterialBody += '<td style="width: 3%; text-align: left;">' + value
                                .department +
                                '</td>';
                            tableMaterialBody += '<td style="width: 1%; text-align: center;">' + value
                                .remark +
                                '</td>';
                            tableMaterialBody += '<td style="width: 1%; text-align: center;">' + value
                                .standard_time + '</td>';
                            tableMaterialBody += '<td style="width: 1%; text-align: left;">' + value
                                .updated_by + '<br>' + value.updated_by_name +
                                '</td>';
                            tableMaterialBody += '<td style="width: 0.1%; text-align: right;">' + value
                                .updated_at +
                                '</td>';
                            tableMaterialBody += '</tr>';
                        }
                    });

                    $('#total_material').html(total_material + ' <small>Material</small>');

                    $('#tableMaterialBody').append(tableMaterialBody);

                    $('#tableMaterial tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search ' +
                            title + '" size="10"/>');
                    });

                    var table = $('#tableMaterial').DataTable({
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
                                    var theadname = $("#tableMaterial th").eq([dd]).text();
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
                                            if ($("#tableMaterial th").eq([dd]).text() ==
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

                    $('#tableMaterial tfoot tr').appendTo('#tableMaterial thead');
                    $('#loading').hide();
                } else {
                    openErrorGritter('Gagal!', result.message);
                    audio_error.play();
                    $('#loading').hide();
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
