@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            text-align: center;
            overflow: hidden;
        }

        tbody>tr>td {
            text-align: center;
        }

        tfoot>tr>th {
            text-align: center;
        }

        th:hover {
            overflow: visible;
        }

        td:hover {
            overflow: visible;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            vertical-align: middle;
            padding: 0;
            font-size: 13px;
            text-align: center;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            padding: 0;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-striped>tbody>tr:nth-child(2n+1)>td,
        .table-striped>tbody>tr:nth-child(2n+1)>th {
            background-color: #ffd8b7;
        }

        .table-hover tbody tr:hover td,
        .table-hover tbody tr:hover th {
            background-color: #FFD700;
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

        </h1>
    </section>
@endsection

@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Scrap Logs Filters</h3>
                    </div>
                    <div class="box-body">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <form method="GET" action="{{ url('excel/report/excel/scrap') }}">
                        <div class="row">
                            <div class="col-md-4 col-md-offset-2">
                                <div class="form-group">
                                    <label>Diterima Tanggal</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="datefrom" data-placeholder="Select Date" name="datefrom">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Sampai Tanggal</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="dateto" name="dateto" data-placeholder="Select Date">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-md-offset-2">
                                <div class="form-group">
                                    <label>Issue Location</label>
                                    <select class="form-control select2" multiple="multiple" name="issue" id='issue'
                                        data-placeholder="Select Location" style="width: 100%;">
                                        <option value=""></option>
                                        <option value=""></option>
                                        @foreach ($storage_locations as $stor_loc)
                                            <option value="{{ $stor_loc }}">{{ $stor_loc }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Receive Location</label>
                                    <select class="form-control select2" multiple="multiple" name="receive" id='receive'
                                        data-placeholder="Select Location" style="width: 100%;">
                                        <option value=""></option>
                                        @foreach ($reicives as $reicive)
                                            <option value="{{ $reicive }}">{{ $reicive }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-md-offset-2">
                                <div class="form-group">
                                    <label>Material</label>
                                    <select class="form-control select2" multiple="multiple" name="material" id='material'
                                        data-placeholder="Select Material" style="width: 100%; height: 100px;">
                                        <option value=""></option>
                                        @php
                                            $material_number = [];
                                        @endphp
                                        @foreach ($materials as $material)
                                            @if (!in_array($material->material_number, $material_number))
                                                <option value="{{ $material->material_number }}">
                                                    {{ $material->material_number }} - {{ $material->description }}
                                                </option>
                                                @php
                                                    array_push($material_number, $material->material_number);
                                                @endphp
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select class="form-control select2" name="remark" id='remark'
                                        data-placeholder="Select Status" style="width: 100%; height: 100px;">
                                        <option value=""></option>
                                        <option value="pending">Pending</option>
                                        <!-- <option value="qa_check">QA Check</option> -->
                                        <option value="received">Received</option>
                                        <!-- <option value="deleted">Deleted</option> -->
                                        <option value="canceled">Canceled</option>
                                        <!-- <option value="done print slip penarikan">Withdrawal</option>
                                                 <option value="request penarikan">Waiting Withdrawal</option>
                                                 <option value="acc ditarik">Print Withdrawal</option>
                                                 <option value="data sudah di BAP">Reject Withdrawal</option> -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group pull-right">
                                <input class="btn btn-success" type="submit" name="publish" value="Export Excel Without Merge">
                                <input class="btn btn-warning" type="submit" name="save" value="Export Excel With Merge">
                            </div>
                        </div>
                        </form>

                        <div class="col-md-6">
                            <div class="form-group">
                                <a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
                                <!-- <button id="search" onClick="fillTable()" class="btn btn-primary">Search</button> -->
                                <a class="btn btn-primary col-sm-14" href="javascript:void(0)" onClick="fillTable()">Search</a>
                            </div>
                        </div>

                        <!-- <div class="row"> -->
                        <div class="col-md-12" style="overflow-x:scroll">
                            <table id="logTable" class="table table-bordered table-striped table-hover">
                                <thead style="background-color: rgb(126,86,134); color: #FFD700;">
                                    <tr>
                                        <th width = "5%">No Slip</th>
                                        <th width = "5%">Material</th>
                                        <th width = "5%">Material Desc.</th>
                                        <th width = "5%">Issue Loc</th>
                                        <th width = "5%">Cat</th>
                                        <th width = "5%">Type</th>
                                        <th width = "5%">Receive Loc</th>
                                        <th width = "2.5%">Qty</th>
                                        <th width = "5%">Status</th>
                                        <th width = "5%">Reason</th>
                                        <th width = "10%">Summary</th>
                                        <th width = "5%">No Invoice</th>
                                        <th width = "5%">Printed at</th>
                                        <th width = "5%">Printed by</th>
                                        <th width = "5%">WH Received at</th>
                                        <th width = "5%">WH Received by</th>
                                        <th width = "5%">Canceled at</th>
                                        <th width = "5%">Canceled by</th>
                                   <!-- <th width = "5%">Canceled User at</th> -->
                                   <!-- <th width = "5%">Canceled User by</th> -->
                                   <!-- <th width = "5%">Pulled At</th> -->
                                   <!-- <th width = "5%">Pulled By</th> -->
                                   <!-- <th width = "5%">To Location</th> -->
                                   <!-- <th width = "5%">Pulled Reason</th> -->
                                        <th width = "2.5%">Cancel</th>
                                        <th width = "2.5%">Reprint</th>
                                        <th width = "2.5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalPenarikan">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="nav-tabs-custom tab-danger" align="center">
                        <ul class="nav nav-tabs">
                            <span>Request Penarikan Scrap</span>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane active">
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom : 5px">
                                    <input type="hidden" name="id_penarikan" id="id_penarikan">
                                    <span>Ke Lokasi</span>
                                    <select class="form-control select3" id="ke_lokasi" name="ke_lokasi"
                                        data-placeholder='Ke Lokasi' style="width: 100%">
                                        <option value="">&nbsp;</option>
                                        <option value="MSTK">MSTK</option>
                                        <option value="SXA1">SXA1</option>
                                        <option value="SX21">SX21</option>
                                    </select>
                                </div>
                                <div class="col-md-12" style="margin-bottom : 5px">
                                    <span>Reason</span>
                                    <textarea class="form-control" id="penarikan_reason" name="penarikan_reason" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom : 5px" align="center">
                                    <button class="btn"
                                        style="margin-left: 5px; width: 25%; background-color: rgb(126,86,134); color: white;"
                                        onclick="Input();"><i class="fa fa-plus"></i> Request</button>
                                </div>
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
    {{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $('#datefrom').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true
            });
            $('#dateto').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true
            });
            $('.select2').select2();
            $('.select3').select2();
        });

        function clearConfirmation() {
            location.reload(true);
        }

        function fillTable() {
            $('#logTable').DataTable().clear();
            $('#logTable').DataTable().destroy();

            var datefrom = $('#datefrom').val();
            var dateto = $('#dateto').val();
            var issue = $('#issue').val();
            var receive = $('#receive').val();
            var material = $('#material').val();
            var remark = $('#remark').val();


            var data = {
                datefrom: datefrom,
                dateto: dateto,
                issue: issue,
                receive: receive,
                material: material,
                remark: remark
            }

            var table = $('#logTable').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'buttons': {
                    buttons: [{
                            extend: 'pageLength',
                            className: 'btn btn-default'
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
                // 'lengthChange': true,
                // 'searching': true,
                // 'ordering': true,
                'order': [[12, 'asc']],
                // 'info': true,
                // 'autoWidth': false,
                // "sPaginationType": "full_numbers",
                // "bJQueryUI": true,
                // "bAutoWidth": false,
                // "processing": true,
                // "serverSide": true,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/scrap/logs') }}",
                    "data": data
                },
                "columns": [{
                        "data": "order_no"
                    },
                    {
                        "data": "material_number"
                    },
                    {
                        "data": "material_description"
                    },
                    {
                        "data": "issue_location"
                    },
                    {
                        "data": "category"
                    },
                    {
                        "data": "jenis"
                    },
                    {
                        "data": "receive_location"
                    },
                    {
                        "data": "quantity"
                    },
                    {
                        "data": "remark"
                    },
                    {
                        "data": "reason"
                    },
                    {
                        "data": "ppp"
                    },
                    {
                        "data": "no_invoice"
                    },
                    {
                        "data": "printed_at"
                    },
                    {
                        "data": "printed_by"
                    },
                    {
                        "data": "received_at"
                    },
                    {
                        "data": "received_by"
                    },
                    {
                        "data": "canceled_at"
                    },
                    {
                        "data": "canceled_by"
                    },
                    // { "data": "canceled_user_at" },
                    // { "data": "canceled_user" },
                    // { "data": "penarikan_at" },
                    // { "data": "penarikan_name" },
                    // { "data": "ke_lokasi" },
                    // { "data": "penarikan_reason" },
                    {
                        "data": "cancel"
                    },
                    {
                        "data": "reprint"
                    },
                    {
                        "data": "test"
                    }
                ]
            });
        }

        function Input() {
            var id_penarikan = $('#id_penarikan').val();
            var ke_lokasi = $('#ke_lokasi').val();
            var penarikan_name = $('#penarikan_name').val();
            var penarikan_reason = $('#penarikan_reason').val();

            var data = {
                id_penarikan: id_penarikan,
                ke_lokasi: ke_lokasi,
                penarikan_name: penarikan_name,
                penarikan_reason: penarikan_reason
            }

            $.post('{{ url('add/penarikan/scrap') }}', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter('Success', result.message);
                    $("#modalPenarikan").modal('hide');
                    clear();
                    fillTable();
                } else {
                    openErrorGritter('Error', result.message);
                }
            });
        }

        function printScrapPenarikan(id) {
            var ke_lokasi = $('#ke_lokasi').val();
            var data = {
                id: id,
                ke_lokasi: ke_lokasi
            }
            if (confirm("Setelah Slip keluar, ambil barang di Warehouse")) {
                $.post('{{ url('print/scrap/penarikan') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success', result.message);
                        $("#loading").hide();
                        // $('#logTable').DataTable().ajax.reload();
                        fillTable();
                    } else {
                        openErrorGritter('Error', result.message);
                    }
                });
            } else {
                $("#loading").hide();
            }
        }

        function reprintScrap(id) {
            if (confirm("Apakah anda yakin akan mencetak ulang slip scrap ini?")) {
                var data = {
                    id: id,
                    cat: 'received'
                }
                $.get('<?php echo e(url('reprint/scrap')); ?>', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success!', result.message);
                    } else {
                        openErrorGritter('Error!', result.message);
                    }
                });
            } else {
                return false;
            }
        }

        function clear() {
            $('#ke_lokasi').val("").trigger('change');
            $('#penarikan_name').val("").trigger('change');
            $('#penarikan_reason').val("");
        }

        function penarikanScrap(id) {
            $("#modalPenarikan").modal('show');
            $("#id_penarikan").val(id);
        }

        function cancelScrap(id) {
            $("#loading").show();
            var data = {
                id: id
            }
            if (confirm("Scrap akan di batalkan. Pastikan slip tersebut cancel SAP atau tidak?")) {
                $.post('{{ url('cancel/scrap') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success', result.message);
                        $("#loading").hide();
                        $('#logTable').DataTable().ajax.reload();
                    } else {
                        openErrorGritter('Error', result.message);
                    }
                });
            } else {
                $("#loading").hide();
            }
        }

        function CancelScrapUser(id) {
            $("#loading").show();
            var data = {
                id: id
            }
            if (confirm("Scrap akan di batalkan.")) {
                $.get('{{ url('cancel/scrap/user') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success', result.message);
                        $("#loading").hide();
                        $('#logTable').DataTable().ajax.reload();
                    } else {
                        openErrorGritter('Error', result.message);
                    }
                });
            } else {
                $("#loading").hide();
            }
        }

        function deleteScrap(id) {
            $("#loading").show();
            var data = {
                id: id
            }
            if (confirm("Apa anda yakin anda akan mendelete slip scrap?")) {
                $.post('{{ url('delete/scrap') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success!', result.message);
                        $("#loading").hide();
                        $('#logTable').DataTable().ajax.reload();
                    } else {
                        openErrorGritter('Error!', result.message);
                    }
                });
            } else {
                $("#loading").hide();
            }
        }

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '4000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '4000'
            });
        }
    </script>
@endsection
