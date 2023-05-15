@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">
    <style type="text/css">
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .dataTables_filter,
        .dataTables_info {
            display: none;
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

        #loading {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
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
        <input type="hidden" id="printer_name" value="MIS">
        <input type="hidden" id="now" value="{{ $now }}">
        <div class="row" style="padding-top: 10px;">
            <div class="col-xs-6">
                <div class="box box-solid">
                    <div class="box-body">
                        <span style="font-size: 20px; font-weight: bold;">DAFTAR MATERIAL:</span>
                        <table class="table table-hover table-striped table-bordered" id="tableList" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">#</th>
                                    <th style="width: 1%; text-align: center;">Material</th>
                                    <th style="width: 7%; text-align: left;">Description</th>
                                    <th style="width: 1%; text-align: center;">Lokasi</th>
                                    <th style="width: 1%; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableBodyList">
                                <?php $cnt = 0; ?>
                                @foreach ($materials as $material)
                                    <?php $cnt += 1; ?>
                                    <tr>
                                        <td style="width: 0.1%; text-align: center;">{{ $cnt }}</td>
                                        <td style="width: 1%; text-align: center;">{{ $material->material_number }}</td>
                                        <td style="width: 7%; text-align: left;">{{ $material->material_description }}
                                        </td>
                                        <td style="width: 1%; text-align: center;">{{ $material->location }}</td>
                                        <td style="width: 1%; text-align: center;"><button class="btn btn-success btn-sm"
                                                onclick="selectMaterial('{{ $material->material_number }}', '{{ $material->material_description }}')">Pilih</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <th style="width: 0.1%; text-align: center;"></th>
                                <th style="width: 1%; text-align: center;"></th>
                                <th style="width: 7%; text-align: left;"></th>
                                <th style="width: 1%; text-align: center;"></th>
                                <th style="width: 1%; text-align: center;"></th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="box box-solid">
                    <div class="box-body">
                        <span style="font-size: 20px; font-weight: bold;">
                            LIST CETAK (PRINTER: <span id="label_printer"></span>)
                        </span>
                        <table class="table table-hover table-striped table-bordered" id="tablePrint" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 7%; text-align: center;">Material</th>
                                    <th style="width: 1%; text-align: center;">Jumlah</th>
                                    <th style="width: 1%; text-align: center;">Keperluan</th>
                                    <th style="width: 1%; text-align: center;">Berlaku Hingga</th>
                                    <th style="width: 1%; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tablePrintBody">
                            </tbody>
                        </table>
                        <button class="btn btn-success" style="width: 100%; font-weight: bold; font-size: 1.5vw;"
                            onclick="printSlip();">CETAK</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalSelectPrinter" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <span style="font-size: 20px; font-weight: bold;">LIST PRINTER THERMAL:</span>
                            <table id="tablePrinter" class="table table-bordered table-hover table-striped">
                                <thead style="">
                                    <tr>
                                        <th style="width: 0.1%; text-align: center;">#</th>
                                        <th style="width: 1%; text-align: left;">Lokasi</th>
                                        <th style="width: 2%; text-align: left;">Detail</th>
                                        <th style="width: 1%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tablePrinterBody">
                                    <?php $cnt = 0; ?>
                                    @foreach ($printers as $printer)
                                        <?php $cnt += 1; ?>
                                        <tr>
                                            <td style="width: 0.1%; text-align: center;">{{ $cnt }}</td>
                                            <td style="width: 1%; text-align: left;">{{ $printer['location'] }}</td>
                                            <td style="width: 2%; text-align: left;">{{ $printer['location_detail'] }}</td>
                                            <td style="width: 1%; text-align: center;"><button
                                                    class="btn btn-success btn-sm"
                                                    onclick="selectPrinter('{{ $printer['printer_name'] }}', '{{ $printer['location'] }}', '{{ $printer['location_detail'] }}')">Pilih</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSelectMaterial">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <span style="font-size: 20px; font-weight: bold;">DATA MATERIAL:</span>

                        <div class="form-group">
                            <label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Material
                                Number<span class="text-red">*</span> :</label>
                            <div class="col-xs-5">
                                <input type="text" class="form-control" id="selectMaterialNumber" disabled>
                            </div>
                        </div>

                        <div class="form-group">
                            <label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Material
                                Description<span class="text-red">*</span> :</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" id="selectMaterialDescription" value="0"
                                    disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Kategori
                                Keperluan<span class="text-red">*</span> :</label>
                            <div class="col-xs-6">
                                <select class="form-control select2" id="selectCategory" onchange="categoryOnChange()"
                                    data-placeholder="Pilih Kategori Keperluan" style="width: 100%;">
                                    <option value=""></option>
                                    <option value="TRIAL (TRIAL -> TRIAL)">TRIAL</option>
                                    <option value="SAMPLE (SAMPLE -> SAMPLE)">SAMPLE</option>
                                    <option value="REPAIR MEKKI (FA -> PLT)">REPAIR MEKKI (FA -> PLT)</option>
                                    <option value="REPAIR MEKKI (PLT -> BPP)">REPAIR MEKKI (PLT -> BPP)</option>
                                    <option value="REPAIR HOKORI (LCQ -> BFF)">REPAIR HOKORI (LCQ -> BFF)</option>
                                    <option value="REPAIR HANDA (BFF -> WLD)">REPAIR HANDA (BFF -> WLD)</option>
                                    <option value="REPAIR ROME (WLD -> BPP)">REPAIR ROME (WLD -> BPP)</option>
                                    {{-- <option value="REPAIR KIZU (WLD -> BPP)">REPAIR KIZU (WLD -> BPP)</option> --}}
                                    <option value="REPAIR KD (WLD -> BFF)">REPAIR KD (WLD -> BFF)</option>
                                    <option value="REPAIR AFTER PROTOL (WLD -> BFF)">REPAIR AFTER PROTOL (WLD -> BFF)
                                    <option value="REPAIR ROOM (FA -> BFF)">REPAIR ROOM (FA -> BFF)</option>
                                    <option value="REPAIR ROOM (PLT -> BFF)">REPAIR ROOM (PLT -> BFF)</option>
                                    <option value="REPAIR ROOM (BPP -> BFF)">REPAIR ROOM (BPP -> BFF)</option>
                                    {{-- KHUSUS STOCKTAKING --}}
                                    <option value="TUKAR MATERIAL (WLD -> BPP)">TUKAR MATERIAL (WLD -> BPP)</option>
                                    <option value="TUKAR MATERIAL (BFF -> WLD)">TUKAR MATERIAL (BFF -> WLD)</option>
                                    <option value="TUKAR MATERIAL (LCQ -> BFF)">TUKAR MATERIAL (LCQ -> BFF)</option>
                                    <option value="TUKAR MATERIAL (PLT -> BFF)">TUKAR MATERIAL (PLT -> BFF)</option>
                                    <option value="TUKAR MATERIAL (PLT -> BPP)">TUKAR MATERIAL (PLT -> BPP)</option>
                                    <option value="ADJUST AFTER STOCKTAKING (WIP -> WIP)">ADJUST AFTER STOCKTAKING (WIP ->
                                        WIP)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Jumlah
                                Material<span class="text-red">*</span> :</label>
                            <div class="col-xs-4">
                                <input type="text" class="form-control numpad" id="selectQuantity"
                                    style="text-align: center;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Berlaku
                                Sampai<span class="text-red">*</span> :</label>
                            <div class="col-xs-4">
                                <input type="text" class="form-control datepicker" id="selectValidTo"
                                    style="text-align: center;">
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="margin-top: 10px;" for="" class="col-xs-12 control-label">Catatan<span
                                    class="text-red"></span> :</label>
                            <div class="col-xs-12">
                                <textarea class="form-control" id="selectRemark" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-success" style="width: 100%; font-weight: bold; margin-top: 10px;"
                                onclick="addMaterial();">KONFIRMASI</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/jquery.numpad.js') }}"></script>
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

        jQuery(document).ready(function() {
            $('.datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });
            $('#modalSelectPrinter').modal('show');
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var count_material = 0;
        var list_materials = [];

        function selectPrinter(printer_name, location, location_detail) {
            $('#printer_name').val(printer_name);
            $('#label_printer').text(location + " " + location_detail);
            $('#modalSelectPrinter').modal('hide');
        }

        function selectMaterial(material_number, material_description) {
            $('#selectMaterialNumber').val(material_number);
            $('#selectMaterialDescription').val(material_description);
            $('#selectValidTo').prop('disabled', true);
            $('#selectValidTo').val($('#now').val());

            $('#modalSelectMaterial').modal('show');
        }

        function categoryOnChange() {

            var category = $('#selectCategory').val().toUpperCase();
            if (category != '') {
                if (category.includes('REPAIR') || category.includes('TUKAR')) {
                    $('#selectValidTo').prop('disabled', true);
                    $('#selectValidTo').val('Ditentukan sistem');
                } else {
                    $('#selectValidTo').prop('disabled', false);
                    $('#selectValidTo').val('');
                }
            }

        }

        function printSlip() {
            if (list_materials.length == 0) {
                audio_error.play();
                openErrorGritter('Error!', 'Pilih material terlebih dahulu');
                return false;
            }
            if (confirm("Apakah anda yakin akan mencetak slip?")) {
                $('#loading').show();
                var printer_name = $('#printer_name').val();
                var data = {
                    list_materials: list_materials,
                    printer_name: printer_name,
                }
                $.post('{{ url('print/transaction/slip_unusual') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        $('#loading').hide();
                        count_material = 0;
                        list_materials = [];
                        $('#tablePrintBody').html("");
                        audio_ok.play();
                        openSuccessGritter('Success', result.message);
                        return false;
                    } else {
                        $('#loading').hide();
                        audio_error.play();
                        openErrorGritter('Error!', result.message);
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function addMaterial() {
            var material_number = $('#selectMaterialNumber').val();
            var material_description = $('#selectMaterialDescription').val();
            var quantity = $('#selectQuantity').val();
            var category = $('#selectCategory').val();
            var valid_to = $('#selectValidTo').val();
            var remark = $('#selectRemark').val();

            if (material_number == "" || material_description == "" || quantity <= 0 || category == "" ||
                valid_to == "") {
                audio_error.play();
                openErrorGritter('Error!', 'Semua data dengan tanda bintang merah harus dilengkapi.');
                return false;
            }

            count_material += 1;

            var tablePrintBody = "";
            tablePrintBody += '<tr id="tr_' + count_material + '">';
            tablePrintBody += '<td style="width: 1%; text-align: left;">' + material_number + '<br>' +
                material_description + '</td>';
            tablePrintBody += '<td style="width: 1%; text-align: center;">' + quantity + '</td>';
            tablePrintBody += '<td style="width: 1%; text-align: center;">' + category + '</td>';
            // tablePrintBody += '<td style="width: 1%; text-align: center;">' + remark + '</td>';
            tablePrintBody += '<td style="width: 1%; text-align: center;">' + valid_to + '</td>';
            tablePrintBody +=
                '<td style="width: 1%; text-align: center;"><button class="btn btn-danger btn-sm" onclick="remMaterial(\'' +
                count_material +
                '\');"><i class="fa fa-trash"></i></button></td>';
            tablePrintBody += '</tr>';

            list_materials.push({
                count_material: count_material,
                material_number: material_number,
                material_description: material_description,
                quantity: quantity,
                category: category,
                remark: remark,
                valid_to: valid_to,
            });

            $('#tablePrintBody').append(tablePrintBody);
            $('#modalSelectMaterial').modal('hide');
        }

        function remMaterial(id) {
            if (confirm("Apakah anda yakin akan menghapus material ini dari list?")) {
                $('#tr_' + id).remove();
                for (var i = 0; i < list_materials.length; i++) {
                    if (list_materials[i].count_material == id) {
                        list_materials.splice(i, 1);
                        break;
                    }
                }
            } else {
                return false;
            }
        }

        $('#modalSelectMaterial').on('hidden.bs.modal', function() {
            $('#selectMaterialNumber').val("");
            $('#selectMaterialDescription').val("");
            $('#selectQuantity').val(0);
            $('#selectCategory').prop('selectedIndex', 0).change();
            $('#selectValidTo').val("");
            $('#selectRemark').val("");
        });

        $('#tableList tfoot th').each(function() {
            var title = $(this).text();
            $(this).html(
                '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search ' +
                title + '" size="10"/>');
        });

        var table = $('#tableList').DataTable({
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
            'ordering': false,
            initComplete: function() {
                this.api()
                    .columns([3])
                    .every(function(dd) {
                        var column = this;
                        var theadname = $("#tableFinished th").eq([dd]).text();
                        var select = $(
                                '<select><option value="" style="font-size:11px;">All</option></select>'
                            )
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                column.search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });
                        column
                            .data()
                            .unique()
                            .sort()
                            .each(function(d, j) {
                                var vals = d;
                                if ($("#tableFinished th").eq([dd]).text() == 'Category') {
                                    vals = d.split(' ')[0];
                                }
                                select.append('<option style="font-size:11px;" value="' +
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

        $('#tableList tfoot tr').appendTo('#tableList thead');

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
