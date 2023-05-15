@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.tagsinput.css') }}" rel="stylesheet" />
    <style type="text/css">
        .table>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        .dataTables_filter,
        .dataTables_info {
            display: none;
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
    <section class="content" style="font-size: 0.8vw;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-12" style="">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-body">
                        <form id="formFilter">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Location</label>
                                                    <select class="form-control select2" multiple="multiple"
                                                        id="filterLocation" name="filterLocation[]"
                                                        data-placeholder="Pilih Lokasi" style="width: 100%;">
                                                        <option></option>
                                                        <option value="BPP">BPP</option>
                                                        <option value="WLD">WLD</option>
                                                        <option value="BFF">BFF</option>
                                                        <option value="LCQ">LCQ</option>
                                                        <option value="PLT">PLT</option>
                                                        <option value="FA">FA</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Kategori</label>
                                                    <select class="form-control select2" multiple="multiple"
                                                        id="filterCategory" name="filterCategory[]"
                                                        data-placeholder="Pilih Kategori" style="width: 100%;">
                                                        <option></option>
                                                        <option value="INTRANSIT">INTRANSIT</option>
                                                        <option value="MATERIAL">MATERIAL</option>
                                                        <option value="FINISH">FINISH</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">HPL</label>
                                                    <select class="form-control select2" multiple="multiple" id="filterHpl"
                                                        name="filterHpl[]" data-placeholder="Pilih HPL"
                                                        style="width: 100%;">
                                                        <option></option>
                                                        <option value="FL">FLUTE</option>
                                                        <option value="SX">SAXPOHONE</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label for="exampleInputEmail1">Remark</label>
                                                    <select class="form-control select2" multiple="multiple"
                                                        id="filterRemark" name="filterRemark[]"
                                                        data-placeholder="Pilih Remark" style="width: 100%;">
                                                        <option></option>
                                                        <option value="BELL">BELL</option>
                                                        <option value="BELL&BOW">BELL&BOW</option>
                                                        <option value="BODY">BODY</option>
                                                        <option value="BOW">BOW</option>
                                                        <option value="FOOT">FOOT</option>
                                                        <option value="HEAD">HEAD</option>
                                                        <option value="NECK">NECK</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Material Number</label>
                                            <textarea class="form-control" id="filterMaterialNumber" rows="3" placeholder="Masukkan Material Number"></textarea>
                                            <input id="materialTags" type="text" placeholder="Material Number"
                                                class="form-control tags" name="materialTags" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <button class="btn btn-primary pull-right" style="width: 10%; margin: 5px;"
                            onclick="fetchData()">Search</button>
                        <button class="btn btn-danger pull-right" style="width: 10%; margin: 5px;"
                            onclick="clearAll()">Clear</button>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-body">
                        <table id="tableData" class="table table-bordered table-striped table-hover"
                            style="width: 100%;">
                            <thead style="color: black; background-color: grey;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">#</th>
                                    <th style="width: 1%; text-align: center;">Material</th>
                                    <th style="width: 5%; text-align: left;">Deskripsi</th>
                                    <th style="width: 1%; text-align: left;">Hpl</th>
                                    <th style="width: 1%; text-align: left;">Remark</th>
                                    <th style="width: 1%; text-align: center;">Jumlah</th>
                                    <th style="width: 1%; text-align: center;">Lokasi</th>
                                    <th style="width: 1%; text-align: center;">Kategori</th>
                                    <th style="width: 1%; text-align: center;">Status</th>
                                    <th style="width: 1%; text-align: center;">Last Update</th>
                                </tr>
                            </thead>
                            <tbody id="tableDataBody">
                            </tbody>
                            <tfoot>
                                <tr>
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
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('.select2').select2();

            jQuery('.tags').tagsInput({
                width: 'auto'
            });
            $('#materialTags').hide();
            $('#materialTags_tagsinput').hide();
            initKeyDown();
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

        function fetchData() {}

        function clearAll() {
            location.reload(true);
        }

        function convertMaterialToTags() {
            var data = $('#filterMaterialNumber').val();
            if (data.length > 0) {
                var rows = data.split('\n');
                if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                        var barcode = rows[i].trim();
                        if (barcode.length > 0) {
                            $('#materialTags').addTag(barcode);
                        }
                    }
                    $('#materialTags').hide();
                    $('#materialTags_tagsinput').show();
                    $('#filterMaterialNumber').hide();
                }
            }
        }

        function fetchData() {
            $('#loading').show();
            var locations = $('#filterLocation').val();
            var categories = $('#filterCategory').val();
            var hpls = $('#filterHpl').val();
            var remarks = $('#filterRemark').val();
            var material_numbers = $('#materialTags').val();
            var data = {
                locations: locations,
                categories: categories,
                hpls: hpls,
                remarks: remarks,
                material_numbers: material_numbers,
            }
            $.get('{{ url('fetch/in_out_stock') }}', data, function(result, status, xhr) {
                if (result.status) {

                    var tableDataBody = "";
                    var cnt = 0;
                    $('#tableDataBody').html("");
                    $('#tableData').DataTable().clear();
                    $('#tableData').DataTable().destroy();

                    $.each(result.datas, function(key, value) {
                        cnt += 1;
                        tableDataBody += '<tr>';
                        tableDataBody += '<td style = "width: 0.1%; text-align: center;">' + cnt + '</td>';
                        tableDataBody += '<td style = "width: 1%; text-align: center;">' + value
                            .material_number + '</td>';
                        tableDataBody += '<td style = "width: 5%; text-align: left;">' + value
                            .material_description + '</td>';
                        tableDataBody += '<td style = "width: 1%; text-align: center">' + value
                            .hpl + '</td>';
                        tableDataBody += '<td style = "width: 1%; text-align: center">' + value
                            .remark + '</td>';
                        tableDataBody += '<td style = "width: 1%; text-align: center">' + value
                            .quantity + '</td>';
                        tableDataBody += '<td style = "width: 1%; text-align: center">' + value
                            .location + '</td>';
                        tableDataBody += '<td style = "width: 1%; text-align: center">' + value
                            .category + '</td>';
                        tableDataBody += '<td style = "width: 1%; text-align: center">' + value
                            .status + '</td>';
                        tableDataBody += '<td style = "width: 1%; text-align: center">' + value
                            .updated_at + '</td>';
                        tableDataBody += '</tr>';
                    });

                    $('#tableDataBody').append(tableDataBody);

                    $('#tableData tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search ' +
                            title + '" size="10"/>');
                    });

                    var table = $('#tableData').DataTable({
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
                                .columns([3, 4, 6, 7, 8])
                                .every(function(dd) {
                                    var column = this;
                                    var theadname = $("#tableFinished th").eq([dd]).text();
                                    var select = $(
                                            '<select><option value="" style="font-size:11px;">All</option></select>'
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
                                            if ($("#tableFinished th").eq([dd]).text() ==
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

                    $('#tableData tfoot tr').appendTo('#tableData thead');

                    $('#loading').hide();
                    audio_ok.play();
                    openSuccessGritter('Success!', result.message);
                } else {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function initKeyDown() {
            $('#filterMaterialNumber').keydown(function(event) {
                if (event.keyCode == 13) {
                    convertMaterialToTags();
                    return false;
                }
            });
            $('#filterTag').keydown(function(event) {
                if (event.keyCode == 13) {
                    convertTagToTags();
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
