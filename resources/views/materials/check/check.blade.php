@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">
    <style type="text/css">
        .table>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(100, 100, 100);
            padding-top: 5px;
            padding-bottom: 5px;
            vertical-align: middle;
            height: 40px;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(100, 100, 100);
            vertical-align: middle;
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
        </h1>
    </section>
@endsection

@section('content')
    <section class="content" style="">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-4">
                        <table class="table table-bordered table-hover" style="">
                            <thead style="">
                                <tr>
                                    <th colspan="2"
                                        style="width: 0.8%; text-align: center; background-color: #605ca8; color: white;">
                                        Informasi Material</th>
                                </tr>
                            </thead>
                            <tbody style="background-color: white;">
                                <tr>
                                    <td style="width: 0.3%; font-weight: bold;">GMC</td>
                                    <td style="width: 1%;">{{ $material_check->material_number }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 0.3%; font-weight: bold;">Deskripsi</td>
                                    <td style="width: 1%;">{{ $material_check->material_description }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 0.3%; font-weight: bold;">Jumlah Sampel</td>
                                    <td style="width: 1%;">{{ $material_check->sample_qty }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 0.3%; font-weight: bold;">UoM</td>
                                    <td style="width: 1%;">{{ $material_check->uom }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 0.3%; font-weight: bold;">Lokasi Cek</td>
                                    <td style="width: 1%;">{{ $material_check->location }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 0.3%; font-weight: bold;">Kedatangan</td>
                                    <td style="width: 1%;">{{ date('l, d F Y', strtotime($material_check->posting_date)) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-8">
                        <table id="tableFinding" class="table table-bordered table-hover">
                            <thead style="">
                                <tr>
                                    <th style="width: 4%; text-align: left; background-color: #605ca8; color: white;">
                                        Penjelasan Temuan</th>
                                    <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">
                                        Jumlah NG</th>
                                    <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">
                                        Bukti Temuan</th>
                                    <th style="width: 0.5%; text-align: left; background-color: #605ca8; color: white;">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="tableFindingBody" style="background-color: white;">
                                <tr>
                                    <td style="width: 4%; text-align: left;">
                                        <textarea style="width: 100%;" class="form-control" rows="2" placeholder="Masukkan penejelasan singkat temuan NG"
                                            id="remark_0"></textarea>
                                    </td>
                                    <td style="width: 1%; text-align: center;">
                                        <input style="width: 100%; text-align: center; font-size: 1.5vw; font-weight: bold;"
                                            type="text" class="numpad" id="ng_qty_0">
                                    </td>
                                    <td style="width: 1%; text-align: center;">
                                        <input accept="image/*" capture="environment" type="file" class="file"
                                            style="display:none" onchange="readURL(this);" id="evidence_file_0">
                                        <button class="btn btn-primary btn-lg" value="Photo" onclick="buttonImage(this)"
                                            style="font-size: 1.5vw;"><i class="fa fa-file-image-o"></i></button>
                                        <img src="" onclick="buttonImage(this)"
                                            style="display: none; height: 100px;" alt="your image" />
                                    </td>
                                    <td style="width: 0.5%; text-align: left;">
                                        <a type="button" class="btn btn-success" onclick="addRow();"><i
                                                class='fa fa-plus'></i></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-4">
                        <button class="btn btn-danger" style="width: 100%; font-weight: bold; font-size: 2vw;"
                            onclick="clearAll();">BATAL</button>
                    </div>
                    <div class="col-xs-8">
                        <button class="btn btn-success" style="width: 100%; font-weight: bold; font-size: 2vw;"
                            onclick="saveCheck();">SIMPAN</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
            $(this).find('.neg').addClass('btn-default');
            $('.neg').css('display', 'block');
        };

        jQuery(document).ready(function() {
            $('#ng_qty_0').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var rows = [0];
        var row = 1;

        function saveCheck() {
            if (confirm("Apakah anda yakin akan membuat poin pengecekan ini?")) {
                var inout_no = '{{ $material_check->inout_no }}';
                var quantity_total = 0;
                var formData = new FormData();
                formData.append('inout_no', inout_no);

                $.each(rows, function(key, value) {
                    var remark = "";
                    var quantity = 0;

                    remark = $('#remark_' + value).val();
                    quantity = $('#ng_qty_' + value).val();
                    quantity_total += $('#ng_qty_' + value).val();

                    formData.append('remark_' + value, remark);
                    formData.append('quantity_' + value, quantity);

                    formData.append('evd_' + value, $('#evidence_file_' + value).prop('files')[0]);
                    var file = $('#evidence_file_' + value).val().replace(/C:\\fakepath\\/i, '').split(".");
                    formData.append('evd_extension_' + value, file[1]);
                    formData.append('evd_file_name_' + value, file[0]);
                });

                formData.append('quantity_total', quantity_total);
                formData.append('rows', rows);

                $.ajax({
                    url: "{{ url('input/material/check') }}",
                    method: "POST",
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        if (data.status) {
                            audio_ok.play();
                            openSuccessGritter('Success!', data.message);
                            clearAll();
                        } else {
                            audio_error.play();
                            openErrorGritter('Error!', data.message);
                            return false;
                        }

                    }
                });
            } else {
                return false;
            }
        }

        function clearAll() {

            var url = "{{ url('index/material/check_monitoring') }}";
            window.location.replace(url);
        }

        function addRow() {
            var add = "";

            add += '<tr id="row_' + row + '">';
            add += '<td style="width: 4%; text-align: left;">';
            add +=
                '<textarea style="width: 100%;" class="form-control" rows="2" placeholder="Masukkan penejelasan singkat temuan NG" id="remark_' +
                row + '"></textarea>';
            add += '</td>';
            add += '<td style="width: 1%; text-align: center;">';
            add +=
                '<input style="width: 100%; text-align: center; font-size: 1.5vw; font-weight: bold;" type="text" class="numpad" id="ng_qty_' +
                row + '">';
            add += '</td>';
            add += '<td style="width: 1%; text-align: center;">';
            add +=
                '<input accept="image/*" capture="environment" type="file" class="file" style="display:none" onchange="readURL(this);" id="evidence_file_' +
                row + '">';
            add +=
                '<button class="btn btn-primary btn-lg" value="Photo" onclick="buttonImage(this)" style="font-size: 1.5vw;"><i class="fa fa-file-image-o"></i></button>';
            add += '<img src="" onclick="buttonImage(this)" style="display: none; height: 100px;" alt="your image"/>';
            add += '</td>';
            add += '<td style="width: 0.5%; text-align: left;">';
            add += '<a type="button" class="btn btn-danger" onclick="remRow(\'' + row +
                '\')" style="margin-right: 5px;"><i class="fa fa-minus"></i></a>';
            add += '<a type="button" class="btn btn-success" onclick="addRow()"><i class="fa fa-plus"></i></a>';
            add += '</td>';
            add += '</tr>';

            $('#tableFindingBody').append(add);

            $('#ng_qty_' + row).numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });

            rows.push(row);
            row += 1;
        }

        function remRow(row) {
            var removeItem = row;
            rows = jQuery.grep(rows, function(value) {
                return value != removeItem;
            });
            $('#row_' + row).remove();
        }

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = $(input).closest("td").find("img");
                    $(img).show();
                    $(img).attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
            $(input).closest("td").find("button").hide();
        }

        function buttonImage(elem) {
            $(elem).closest("td").find("input").click();
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
