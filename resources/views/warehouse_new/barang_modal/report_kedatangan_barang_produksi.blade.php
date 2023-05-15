@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}" media="print">
    <style type="text/css">
        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            text-align: center;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 3px;
            padding-bottom: 3px;
            padding-left: 2px;
            padding-right: 2px;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
            vertical-align: middle;
        }

        #tablePenerimaanBody>tr:hover {
            background-color: #7dfa8c;
        }

        #tablePenerimaanDetailBody>tr:hover {
            background-color: #7dfa8c;
        }

        #loading,
        #error {
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
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: white; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row" style="margin-top: 1%;">
            <div class="col-xs-9">
                <div id="chart1" style="height: 50vh; width: 100%;"></div>
            </div>

            <div class="col-xs-3">
                <table id="resumeTable" class="table table-bordered table-striped table-hover"
                    style="margin-bottom: 5%; height: 17vh;">
                    <thead style="background-color: rgba(126,86,134,.7);">
                        <tr>
                            <th style="text-align: center; width: 50%; font-size: 0.9vw;">Status</th>
                            <th style="text-align: center; width: 50%; font-size: 0.9vw;">Jumlah Kedatangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 1%; font-weight: bold; font-size: 0.9vw;">All</td>
                            <td id="count_all" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr> 
                        <tr>
                            <td
                                style="width: 1%; background-color: rgb(254, 204, 254); font-weight: bold; font-size: 0.9vw;">
                                Diterima Gudang</td>
                            <td id="count_received"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(254, 204, 254); font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 1%; background-color: #ccffff; font-weight: bold; font-size: 0.9vw;">
                                Sudah Dikirim Produksi</td>
                            <td id="count_delivered"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: #ccffff; font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>

                        <tr>
                            <td style="width: 1%; background-color: rgb(255, 189, 68); font-weight: bold; font-size: 0.9vw;">
                                Proses Produksi</td>
                            <td id="count_process"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(255, 189, 68); font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>

                        <tr>
                            <td
                                style="width: 1%; background-color: rgb(236, 255, 123); font-weight: bold; font-size: 0.9vw;">
                                Disposal<br><span class="text-purple" style="font-weight: normal; font-size: 0.85vw;"></span> </td>
                            <td id="count_disposal"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(236, 255, 123); font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <input id="location" name="location" type="hidden" value="{{ $location }}"></div>
            
            <div class="col-xs-12">
                <div class="nav-tabs-custom" style="margin-top: 1%;">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                        <li class="vendor-tab active"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Semua Barang</a>
                        </li>
                        <li class="vendor-tab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Barang Modal</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                            <table id="tablePenerimaan" class="table table-bordered" style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width:8%;">Nama Item</th>
                                        <th style="width:3%;">Nomor PR / Inv</th>
                                        <th style="width:3%;">Tanggal PR / Inv</th>
                                        <th style="width:3%;">Nomor PO</th>
                                        <th style="width:3%;">Tanggal PO</th>
                                        <th style="width:2%;">Tanggal Kedatangan</th>
                                        <th style="width:6%;">Vendor</th>
                                        <th style="width:2%;">Surat Jalan</th>
                                        <th style="width:1%;">Qty</th>
                                        <th style="width:6%;">Status Diterima</th>
                                        <th style="width:6%;">Dokumen BC</th>
                                    </tr>
                                </thead>
                                <tbody id="tablePenerimaanBody" style="vertical-align: middle; text-align: center;">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
                            <table id="tablePenerimaanDetail" class="table table-bordered"
                                style="width: 100%; ">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width:8%;">Nama Item</th>
                                        <th style="width:3%;">Nomor PR / Inv</th>
                                        <th style="width:3%;">Tanggal PR / Inv</th>
                                        <th style="width:3%;">Nomor PO</th>
                                        <th style="width:3%;">Tanggal PO</th>
                                        <th style="width:2%;">Tanggal Kedatangan</th>
                                        <th style="width:6%;">Vendor</th>
                                        <th style="width:2%;">Surat Jalan</th>
                                        <th style="width:1%;">Qty</th>
                                        <th style="width:6%;">Status Diterima</th>
                                    </tr>
                                </thead>
                                <tbody id="tablePenerimaanDetailBody" style="vertical-align: middle; text-align: center;">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetail" data-keyboard="false" data-backdrop="static"
        style="overflow-y: auto;">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail Sales Price</h4>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="col-xs-12">
                        <table id="tableDetailModal" class="table table-bordered" style="width: 100%; font-size: 0.8vw;">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="width: 20%;">Nama Item</th>
                                    <th style="width: 20%;">No PO</th>
                                    <th style="width: 50%;">No PR</th>
                                    <th style="width: 10%;">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody id="tableDetailModalBody" style="vertical-align: middle; text-align: center;">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning pull-right" data-dismiss="modal" aria-label="Close"
                        style="font-weight: bold; font-size: 0.95vw; width: 20%;">BACK<br>戻る</button>
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
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/data.js') }}"></script>
    <script src="{{ url('js/exporting.js') }}"></script>
    <script src="{{ url('js/export-data.js') }}"></script>
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $(function() {
                $('.select2').select2({
                    dropdownParent: $('#modalCreate')
                });
            });

            fetchPenerimaanBarang();
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var countAddItem = 0;
        var countAddItems = [];

    
        $('#modalUpload').on('hidden.bs.modal', function() {
            $('#modalCreate').modal('show');
        });

        function clearAll() {
            $('#modalCreate').modal('hide');
            $("#addBuyer").prop('selectedIndex', 0).change();
            $('#addDestination').val("");
            $('#addDestinationName').val("");
            $('#addDestinationShortname').val("");
            $('#addDivision').val("");
            $('#addRemark').val("");
            $('#addCurrency').val("");
            $('#addAttachment').val("");
            countAddItem = 0;
            countAddItems = [];
            $('#tableAddItemBody').html("");
        }

        



        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: "{{ url('images/image-screen.png') }}",
                sticky: false,
                time: '5000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: "{{ url('images/image-stop.png') }}",
                sticky: false,
                time: '5000'
            });
        }


        function fetchModal(bulan, status) {
            $('#tableDetailModal').DataTable().clear();
            $('#tableDetailModal').DataTable().destroy();
            $('#tableDetailModalBody').html("");

            var tableDetailModalBody = "";
            // for (var i = 0; i < kedatangan_barang_all.length; i++) {
            //         tableDetailModalBody += '<tr>';
            //         tableDetailModalBody += '<td style="width: 15%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
            //             .material_number_buyer + '</td>';
            //         tableDetailModalBody += '<td style="width: 15%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
            //             .material_number + '</td>';
            //         tableDetailModalBody += '<td style="width: 50%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
            //             .description + '</td>';

            //         var red = 'background-color: #ffccff;';
            //         if (eo_details[i].sales_price <= 0) {
            //             tableDetailModalBody += '<td style="' + red + 'width: 15%; padding-left: 1%; padding-right: 1%;">' +
            //                 eo_details[i].sales_price + ' <span class="text-red"><b>*</b></span</td>';
            //         } else {
            //             tableDetailModalBody += '<td style="width: 15%; padding-left: 1%; padding-right: 1%;">' +
            //                 eo_details[i].sales_price + '</td>';
            //         }
            //         tableDetailModalBody += '</tr>';
            // }
            $('#tableDetailModalBody').append(tableDetailModalBody);

            $('#tableDetailModal').DataTable({
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
                    }, {
                        extend: 'copy',
                        className: 'btn btn-success',
                        text: '<i class="fa fa-copy"></i> Copy',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }]
                },
                'ordering': false,
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                'columnDefs': [{
                        "targets": 2,
                        "className": "text-left",
                    },
                    {
                        "targets": 3,
                        "className": "text-right",
                    }
                ]
            });

            $('#modalDetail').modal('show');


        }


        function fetchPenerimaanBarang() {
            $("#loading").show();
            var location = $("#location").val();

            var data = {
                location:location
            }

            $.get('{{ url("fetch/produksi/cek_kedatangan") }}', data, function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();
                    $('#tablePenerimaan').DataTable().clear();
                    $('#tablePenerimaan').DataTable().destroy();
                    $('#tablePenerimaanBody').html("");

                    var tablePenerimaanBody = "";

                    $.each(result.kedatangan_barang, function(key, value) {

                        if (value.pic_date_receive != null) {
                            var tanggal_fix = value.pic_date_receive.replace(/-/g,'/');                     
                        }

                        tablePenerimaanBody += '<tr>';
                        tablePenerimaanBody += '<td style="width: 12.5%;text-align; left;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += value.nama_item;
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += value.no_pr;
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += getFormattedDate(new Date(value.submission_date));
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 7.5%;padding-left: 0.5%; padding-right: 0.5%;">';
                        tablePenerimaanBody += value.no_po;
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += getFormattedTime(new Date(value.tgl_po));
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += getFormattedDate(new Date(value.date_receive));
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 15%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += value.supplier_code+' - '+value.supplier_name;
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += value.surat_jalan;
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 2.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += value.qty_receive;
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 12.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        if (value.pic_date_receive != null) {
                            tablePenerimaanBody += value.pic_receive +' <br> '+getFormattedTime(new Date(tanggal_fix));
                        }else{

                        }

                        if (value.dokumen == null) {
                            tablePenerimaanBody += '<td style="width: 2.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                            tablePenerimaanBody += '-';
                            tablePenerimaanBody += '</td>';
                        }else{

                            tablePenerimaanBody += '<td style="width: 2.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                            for (var i = 0; i < value.dokumen.split(',').length; i++) {
                                tablePenerimaanBody += '<a href="{{url('files/dokumen_bc')}}/'+value.dokumen.split(",")[i]+'" target="_blank" class="fa fa-paperclip"></a>';
                            }

                            tablePenerimaanBody += '</td>';
                        }

                        // tablePenerimaanBody += '<td style="width: 2.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        // tablePenerimaanBody += value.dokumen;
                        // tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '</tr>';

                    });

                    $('#tablePenerimaanBody').append(tablePenerimaanBody);

                    $('#tablePenerimaan').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        'ordering': true,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        'columnDefs': [{
                                "targets": 0,
                                "className": "text-left",
                            },
                            {
                                "targets": 7,
                                "className": "text-left",
                            }
                        ]
                    });

                    $('#tablePenerimaanDetail').DataTable().clear();
                    $('#tablePenerimaanDetail').DataTable().destroy();

                    $('#tablePenerimaanDetailBody').html("");
                    var tablePenerimaanDetailBody = "";

                    $.each(result.kedatangan_barang_all, function(key, value) {
                        if (value.pic_date_receive != null) {
                            var tanggal_fix = value.pic_date_receive.replace(/-/g,'/');                     
                        }

                        tablePenerimaanDetailBody += '<tr>';

                        tablePenerimaanDetailBody += '<td style="width: 12.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanDetailBody += value.nama_item;
                        tablePenerimaanDetailBody += '</td>';

                        tablePenerimaanDetailBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanDetailBody += value.no_pr;
                        tablePenerimaanDetailBody += '</td>';

                        tablePenerimaanDetailBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanDetailBody += getFormattedDate(new Date(value.submission_date));
                        tablePenerimaanDetailBody += '</td>';

                        tablePenerimaanDetailBody += '<td style="width: 7.5%; padding-left: 0.5%; padding-right: 0.5%;">';
                        tablePenerimaanDetailBody += value.no_po;
                        tablePenerimaanDetailBody += '</td>';

                        tablePenerimaanDetailBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanDetailBody += getFormattedTime(new Date(value.tgl_po));
                        tablePenerimaanDetailBody += '</td>';


                        tablePenerimaanDetailBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanDetailBody += getFormattedDate(new Date(value.date_receive));
                        tablePenerimaanDetailBody += '</td>';

                        tablePenerimaanDetailBody += '<td style="width: 15%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanDetailBody += value.supplier_code+' - '+value.supplier_name;
                        tablePenerimaanDetailBody += '</td>';

                        tablePenerimaanDetailBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanDetailBody += value.surat_jalan;
                        tablePenerimaanDetailBody += '</td>';

                        tablePenerimaanDetailBody += '<td style="width: 2.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanDetailBody += value.qty_receive;
                        tablePenerimaanDetailBody += '</td>';

                        tablePenerimaanDetailBody += '<td style="width: 12.5%;padding-left: 0.3%; padding-right: 0.3%;">';

                        if (value.pic_date_receive != null) {
                            tablePenerimaanDetailBody += value.pic_receive +' <br> '+getFormattedTime(new Date(tanggal_fix));
                        } else{
                            
                        }
                        tablePenerimaanDetailBody += '</td>';

                        tablePenerimaanDetailBody += '</tr>';
                    });

                    $('#tablePenerimaanDetailBody').append(tablePenerimaanDetailBody);

                    $('#tablePenerimaanDetail').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        'ordering': true,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        'columnDefs': [
                            {
                                "targets": 0,
                                "className": "text-left",
                            },
                            {
                                "targets": 7,
                                "className": "text-left",
                            }
                        ]
                    });


                    var received = 0;
                    var delivered = 0;
                    var proses = 0;
                    var disposal = 0;

                    for (var k = 0; k < result.kedatangan_barang_all.length; k++) {
                        if (result.kedatangan_barang_all[k].pic_date_receive == null) {
                            received++;
                        } else if (result.kedatangan_barang_all[k].pic_date_receive != null) {
                            delivered++;
                        } else if (result.kedatangan_barang_all[k].pic_date_receive == 'Process') {
                            proses++;
                        }
                    }


                    $('#count_all').text((received + delivered + proses));
                    $('#count_received').text(received);
                    $('#count_delivered').text(delivered);
                    $('#count_process').text(proses);
                    $('#count_disposal').text(disposal);


                    var xCategories = [];
                    var received = [];
                    var delivered = [];
                    var proses = [];
                    var disposal = [];

                    for (var i = 0; i < result.calendars.length; i++) {
                        xCategories.push(result.calendars[i].month_text);
                        received.push(0);
                        delivered.push(0);
                        proses.push(0);
                        disposal.push(0);
                    }

                    for (var i = 0; i < result.calendars.length; i++) {
                        for (var j = 0; j < result.kedatangan_barang_all.length; j++) {
                            if (result.calendars[i].month == result.kedatangan_barang_all[j].date_receive.substr(0, 7)) {
                                var status = '';
                                if (result.kedatangan_barang_all[j].pic_date_receive == null) {
                                    received[i] += 1;
                                } else if (result.kedatangan_barang_all[j].pic_date_receive != null) {
                                    delivered[i] += 1;
                                } else if (result.kedatangan_barang_all[j].pic_date_receive == 'proses') {
                                    proses[i] += 1;
                                }
                            }
                        }
                    }


                    Highcharts.chart('chart1', {
                        chart: {
                            backgroundColor: null,
                            type: 'column',
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            title: {
                                text: 'Penerimaan Barang Per Bulan',
                                style: {
                                    fontWeight: 'bold',
                                },
                            },
                            tickInterval: 1,
                            gridLineWidth: 1,
                            categories: xCategories,
                            crosshair: true
                        },
                        yAxis: [{
                            title: {
                                text: 'Jumlah',
                                style: {
                                    fontWeight: 'bold',
                                },
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                                    fontSize: '0.8vw'
                                }
                            },
                        }],
                        exporting: {
                            enabled: false
                        },
                        legend: {
                            enabled: true,
                            borderWidth: 1
                        },
                        tooltip: {
                            enabled: true
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: 'black'
                            },
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return (this.y != 0) ? this.y : "";
                                    },
                                    style: {
                                        textOutline: false
                                    }
                                },
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            fetchModal(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Diterima Gudang',
                            data: received,
                            color: '#feccfe'
                        }, {
                            name: 'Dikirim Produksi',
                            data: delivered,
                            color: '#ccffff'
                        }, {
                            name: 'Proses Produksi',
                            data: proses,
                            color: '#ffbd44'
                        }, {
                            name: 'Disposal',
                            data: disposal,
                            color: '#ecff7b'
                        }]
                    });


                } else {
                    alert('Attempt to retrieve data failed');
                }
            });
        }

        function getFormattedDate(date) {
              var year = date.getFullYear();

              var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];

              var month = date.getMonth();

              var day = date.getDate().toString();
              day = day.length > 1 ? day : '0' + day;
              
              return day + '-' + monthNames[month] + '-' + year;
        }

        function getFormattedTime(date) {
              var year = date.getFullYear();

              var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];

              var month = date.getMonth();

              var day = date.getDate().toString();
              day = day.length > 1 ? day : '0' + day;

              var hour = date.getHours();
              if (hour < 10) {
                    hour = "0" + hour;
                }

              var minute = date.getMinutes();
              if (minute < 10) {
                    minute = "0" + minute;
                }
              var second = date.getSeconds();
              
              return day + '-' + monthNames[month] + '-' + year +' '+ hour +':'+ minute +':'+ second;
        }
    </script>
@endsection
