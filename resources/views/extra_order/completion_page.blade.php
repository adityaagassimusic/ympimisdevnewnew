@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">
    <style type="text/css">
        #tableBodyList>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c;
        }

        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        table {
            table-layout: fixed;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        td:hover {
            overflow: visible;
        }

        thead>tr>th {
            text-align: center;
        }

        tbody>tr>td {
            text-align: center;
        }

        tfoot>tr>th {
            text-align: center;
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
            border: 1px solid rgb(211, 211, 211);
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        nmpd-grid {
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
            <small><span class="text-purple"> {{ $title_jp }}</span></small>
        </h1>
    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content" style="font-size: 10pt;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 1%;">
                <div class="row">
                    <div class="col-xs-7">
                        <div class="box box-danger">
                            <div class="box-body">
                                <table style="width: 100%; border: none !important;">
                                    <tr style="border: none !important;">
                                        <thead style="border: none !important;">
                                            <th style="border: none !important; text-align: right;">
                                                <div style="vertical-align: middle;">
                                                    <span class="label"
                                                        style="padding-bottom: 0px; background-color: #feccfe; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                                    <span> = Target tidak sesuai lot</span>
                                                </div>
                                            </th>
                                        </thead>
                                    </tr>
                                </table>
                                <table class="table table-hover table-bordered table-striped" id="tableList">
                                    <thead style="background-color: rgba(126,86,134,.7);">
                                        <tr>
                                            <th style="width: 12%;">DueDate</th>
                                            <th style="width: 15%;">EO No.</th>
                                            <th style="width: 10%;">SLoc</th>
                                            <th style="width: 10%;">Material</th>
                                            <th style="width: 38%;">Description</th>
                                            <th style="width: 5%;">Dest.</th>
                                            <th style="width: 10%;">Target</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBodyList">
                                    </tbody>
                                    <tfoot style="background-color: rgb(252, 248, 227);">
                                        <tr>
                                            <th colspan="6" style="text-align:center;">Total:</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-5" style="padding-left: 0px; padding-right: 10px;">
                        <div class="row">
                            <input type="hidden" id="extra_order_detail_id">

                            <div class="col-xs-6">
                                <span style="font-weight: bold; font-size: 16px;">EO No. :</span>
                            </div>
                            <div class="col-xs-6">
                                <span style="font-weight: bold; font-size: 16px;">Due Date :</span>
                            </div>
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <input type="text" id="eo_number"
                                    style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
                            </div>
                            <div class="col-xs-6">
                                <input type="text" id="due_date"
                                    style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
                            </div>

                            <div class="col-xs-6">
                                <span style="font-weight: bold; font-size: 16px;">Material Number :</span>
                            </div>

                            <div class="col-xs-6">
                                <span style="font-weight: bold; font-size: 16px;">Destination :</span>
                            </div>
                            <div class="col-xs-6" style="padding-right: 0px;">
                                <input type="text" id="material_number"
                                    style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
                            </div>
                            <div class="col-xs-6">
                                <input type="text" id="destination"
                                    style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
                            </div>

                            <div class="col-xs-12">
                                <span style="font-weight: bold; font-size: 16px;">Material Description :</span>
                                <input type="text" id="material_description"
                                    style="width: 100%; height: 50px; font-size: 26px; text-align: center;" disabled>
                            </div>


                            <div class="col-xs-12">
                                <span style="font-weight: bold; font-size: 16px;">Quantity :</span>
                            </div>

                            <div class="col-xs-6" style="padding-right: 0px;">
                                <input type="text" id="quantity" class="numpad" onchange="checkLot()"
                                    style="width: 100%; height: 50px; font-size: 30px; text-align: center;" readonly>
                            </div>
                            <div class="col-xs-6">
                                <button class="btn btn-primary" onclick="addPackingList()"
                                    style="font-size: 35px; width: 100%; font-weight: bold; padding: 0;">
                                    <i class="fa fa-cart-plus"></i>&nbsp;&nbsp;ADD
                                </button>
                            </div>


                            <div class="col-xs-12">
                                <span style="font-size: 20px; font-weight: bold;">PACKING LIST :</span>
                                <table class="table table-hover" id="tablePack">
                                    <thead style="background-color: rgba(126,86,134,.7);">
                                        <tr>
                                            <th style="width: 1%;">No</th>
                                            <th style="width: 20%;">EO No.</th>
                                            <th style="width: 60%;">Material</th>
                                            <th style="width: 9%;">Qty</th>
                                            <th style="width: 10%;">#</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBodyPack">
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tbody>
                                    <tfoot id="tableFootPack" style="background-color: rgb(252, 248, 227);">
                                        <tr>
                                            <th colspan="3">Total</th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                                <button class="btn btn-success" onclick="showPrint()"
                                    style="font-size: 35px; width: 100%; font-weight: bold; padding: 0;">
                                    <i class="fa fa-print"></i>&nbsp;&nbsp;PRINT LABEL
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12" style="margin-bottom: 1%;">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Extra Order
                                Detail</a></li>
                        {{-- <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">KDO</a></li> --}}
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <table id="eo_detail" class="table table-bordered table-striped table-hover"
                                style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 2%">EO No. Seq.</th>
                                        <th style="width: 2%">Material</th>
                                        <th style="width: 5%">Description</th>
                                        <th style="width: 2%">Location</th>
                                        <th style="width: 2%">Destination</th>
                                        <th style="width: 2%">St. Date</th>
                                        <th style="width: 1%">Qty</th>
                                        <th style="width: 3%">Created At</th>
                                        <th style="width: 1%">Reprint</th>
                                        <th style="width: 1%">Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
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
                        <div class="tab-pane" id="tab_2">
                            <table id="kdo_table" class="table table-bordered table-striped table-hover"
                                style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 1%">KDO</th>
                                        <th style="width: 1%">Count Item</th>
                                        <th style="width: 1%">Location</th>
                                        <th style="width: 1%">Created At</th>
                                        <th style="width: 1%">Reprint KDO</th>
                                        <th style="width: 1%">Cancel</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
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
        </div>

        <div class="modal modal-default fade" id="completion_modal">
            <div class="modal-dialog modal-xs">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>
                        <h4 class="modal-title">
                            Print Label Extra Order
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="modal-body">
                            <h5>Apakah anda yakin untuk mencetak label item yang ada di packing list ?</h5>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-success" onclick="completion()"><span><i class="fa fa-print"></i>
                                Submit</span></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalLocation">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <center>
                            <h3
                                style="background-color: #00a65a; padding-top: 2%; padding-bottom: 2%; font-weight: bold; margin: 0px;">
                                Select Location</h3>
                        </center>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <select class="form-control select2" id="storage_location" onchange="fillTable(value)"
                                data-placeholder="Select Location ..." style="width: 100%; font-size: 20px;">
                                <option></option>
                                @foreach ($storage_locations as $storage_location)
                                    <option value="{{ $storage_location->storage_location }}">
                                        {{ $storage_location->storage_location }}</option>
                                @endforeach
                            </select>
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
    <script src="{{ url('js/jquery.numpad.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.fn.numpad.defaults.gridTpl =
            '<table class="table modal-content" style="width: 37.5%; border: 1px solid grey;"></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:20px; height: 50px;"/>';
        $.fn.numpad.defaults.buttonNumberTpl =
            '<button type="button" class="btn btn-default" style="font-size:20px; width:100%;"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl =
            '<button type="button" class="btn" style="font-size:20px; width: 100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function() {
            $(this).find('.del').addClass('btn-default');
            $(this).find('.clear').addClass('btn-default');
            $(this).find('.cancel').addClass('btn-default');
            $(this).find('.done').addClass('btn-success');
        };


        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });

            // $('#modalLocation').modal({
            // 	backdrop: 'static',
            // 	keyboard: false
            // });

            $('.select2').select2({
                allowClear: true,
            });

            fillTable();

        });

        var storage_locations = <?php echo json_encode($storage_locations); ?>;
        var volumes = <?php echo json_encode($volumes); ?>;

        function fillTable() {

            fillTableList();
            fillTablePack();
            fillTableDetail();


            // fillTable();

            $('#modalLocation').modal('hide');
        }

        function fillTableList() {

            $.get('{{ url('fetch/extra_order/completion_target') }}', function(result, status, xhr) {
                $('#tableList').DataTable().clear();
                $('#tableList').DataTable().destroy();
                $('#tableBodyList').html("");

                var tableData = "";
                var total_target = 0;
                var css = "vertical-align: middle; padding-top: 0px; padding-bottom: 0px;";
                $.each(result.target, function(key, value) {
                    tableData += '<tr id="' + value.id + '">';

                    tableData += '<td style="' + css + '" onclick="fillField(\'' + value.id + '\')">';
                    tableData += value.due_date;
                    tableData += '</td>';

                    tableData += '<td style="' + css + '" onclick="fillField(\'' + value.id + '\')">';
                    tableData += value.eo_number;
                    tableData += '</td>';

                    var area = '';
                    for (let i = 0; i < storage_locations.length; i++) {
                        if (storage_locations[i].storage_location == value.storage_location) {
                            area = storage_locations[i].area;
                            break;
                        }
                    }

                    tableData += '<td style="' + css + '" onclick="fillField(\'' + value.id + '\')">';
                    tableData += value.storage_location + '<br>' + area;
                    tableData += '</td>';

                    if (value.is_completion == 1) {
                        tableData += '<td style="' + css + '" >';
                        tableData += '<span onclick="fillField(\'' + value.id + '\')">';
                        tableData += value.material_number + '</span>' + '<br>';
                        tableData += '<a onclick="viewBom(\'' + value.material_number + '\')" ';
                        tableData += 'class="btn btn-default btn-xs"';
                        tableData += 'style="margin: 1px 1px 5px 1px; padding: 1px 5px 1px 5px;';
                        tableData += 'color:black; background-color: #ecff7b; font-size: 10px;">';
                        tableData += '&nbsp;<i class="fa fa-eye"></i>&nbsp;&nbsp;BOM&nbsp;</a>';
                        tableData += '</td>';
                    } else {
                        tableData += '<td style="' + css + '" onclick="fillField(\'' + value.id + '\')">';
                        tableData += value.material_number;
                        tableData += '</td>';
                    }



                    tableData += '<td style="' + css + ' text-align: left;" ';
                    tableData += 'onclick="fillField(\'' + value.id + '\')">';
                    tableData += value.description;
                    tableData += '</td>';

                    tableData += '<td style="' + css + '" onclick="fillField(\'' + value.id + '\')">';
                    tableData += value.destination_shortname;
                    tableData += '</td>';

                    var bgcolor = '';
                    for (let i = 0; i < volumes.length; i++) {
                        if (volumes[i].material_number == value.material_number) {
                            if ((value.target % volumes[i].lot) > 0) {
                                bgcolor = 'background-color: #feccfe;';
                            }
                            break;
                        }
                    }

                    tableData += '<td style="' + css + ' ' + bgcolor + '" ';
                    tableData += 'onclick="fillField(\'' + value.id + '\')">';
                    tableData += value.target;
                    tableData += '</td>';

                    tableData += '</tr>';
                    total_target += value.target;
                });
                $('#tableBodyList').append(tableData);


                $('#tableList').DataTable({
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
                    "footerCallback": function(tfoot, data, start, end, display) {
                        var intVal = function(i) {
                            return typeof i === 'string' ?
                                i.replace(/[\$%,]/g, '') * 1 :
                                typeof i === 'number' ?
                                i : 0;
                        };
                        var api = this.api();
                        var totalPlan = api.column(6).data().reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0)
                        $(api.column(6).footer()).html(totalPlan);
                    },
                    'paging': true,
                    'lengthChange': true,
                    'pageLength': 10,
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
            });
        }

        function viewBom(material_number) {
            window.open('{{ url('index/extra_order/bom_multi_level') }}' + '/' + material_number, '_blank');
        }

        function fillField(id) {

            var due_date = $('#' + id).find('td').eq(0).text();
            var eo_number = $('#' + id).find('td').eq(1).text();
            var material_number = $('#' + id).find('td').eq(3).text().substr(0, 7);
            var material_description = $('#' + id).find('td').eq(4).text();
            var destination = $('#' + id).find('td').eq(5).text();

            $('#extra_order_detail_id').val(id);
            $('#due_date').val(due_date);
            $('#eo_number').val(eo_number);
            $('#material_number').val(material_number);
            $('#material_description').val(material_description);
            $('#destination').val(destination);
            $('#quantity').val('');

        }

        function checkLot() {
            var extra_order_detail_id = $('#extra_order_detail_id').val();
            var material_number = $('#material_number').val();
            var material_description = $('#material_description').val();

            if (material_number.length > 0) {
                var target = $('#' + extra_order_detail_id).find('td').eq(6).text();
                var quantity = $('#quantity').val();

                if (parseFloat(quantity) > parseFloat(target)) {
                    $('#quantity').val('');
                    openErrorGritter('Error!', 'Quantity yang diinput melebihi target');
                    return false;
                }

                for (let i = 0; i < volumes.length; i++) {
                    if (volumes[i].material_number == material_number) {
                        if ((target / volumes[i].lot) > 1) {
                            if ((quantity % volumes[i].lot) != 0) {
                                $('#quantity').val('');
                                openErrorGritter('Error!', 'Mohon masukan quantity sesuai lot. ' + material_number + ' ' +
                                    material_description + ' mempunyai lot = ' + volumes[i].lot);
                            }
                        }
                        break;
                    }
                }

            } else {
                $('#quantity').val('');
                openErrorGritter('Error!', 'Material belum dipilih');
                return false;
            }
        }

        var packing_list = [];

        function addPackingList() {

            var extra_order_detail_id = $('#extra_order_detail_id').val();
            var due_date = $('#due_date').val();
            var eo_number = $('#eo_number').val();
            var material_number = $('#material_number').val();
            var material_description = $('#material_description').val();
            var destination = $('#destination').val();
            var quantity = $('#quantity').val();
            var storage_location = $('#' + extra_order_detail_id).find('td').eq(2).text().substr(0, 4);;

            if (material_number == '') {
                openErrorGritter('Error!', 'Material belum dipilih');
                return false;
            }

            if (quantity == '') {
                openErrorGritter('Error!', 'Quantity belum diinput');
                return false;
            }

            if (quantity <= 0) {
                openErrorGritter('Error!', 'Quantity kurang atau sama dengan 0');
                return false;
            }

            var target = $('#' + extra_order_detail_id).find('td').eq(6).text();
            if (parseFloat(quantity) > parseFloat(target)) {
                openErrorGritter('Error!', 'Quantity yang diinput melebihi target');
                return false;
            }

            for (var i = 0; i < packing_list.length; i++) {
                if (packing_list[i].eo_number != eo_number) {
                    openErrorGritter('Error!', 'Dalam suatu packing list tidak boleh terdapat EO Number yang berbeda');
                    return false;
                }
            }

            packing_list.push({
                'extra_order_detail_id': extra_order_detail_id,
                'eo_number': eo_number,
                'material_number': material_number,
                'material_description': material_description,
                'storage_location': storage_location,
                'destination': destination,
                'quantity': quantity
            });

            $('#extra_order_detail_id').val('');
            $('#due_date').val('');
            $('#eo_number').val('');
            $('#material_number').val('');
            $('#material_description').val('');
            $('#destination').val('');
            $('#quantity').val('');
            var remainder = target - quantity;
            $('#' + extra_order_detail_id).find('td').eq(6).text(remainder);


            openSuccessGritter('Success', 'Sukses menambahkan ke packing list');
            fillTablePack();

        }

        function deletePackingList(id) {
            var target = $('#' + packing_list[id].extra_order_detail_id).find('td').eq(6).text();
            var quantity = packing_list[id].quantity;

            var new_target = parseFloat(target) + parseFloat(quantity);
            $('#' + packing_list[id].extra_order_detail_id).find('td').eq(6).text(new_target);

            packing_list.splice(id, 1);
            openSuccessGritter('Success', 'Item berhasil dihapus dari packing list');
            fillTablePack();

        }

        function fillTablePack() {

            $('#tablePack').DataTable().clear();
            $('#tablePack').DataTable().destroy();
            $('#tableBodyPack').html("");

            var tableData = "";
            var count = 0;
            $.each(packing_list, function(key, value) {
                tableData += '<tr>';
                tableData += '<td style="vertical-align: middle;">' + ++count + '</td>';
                tableData += '<td style="vertical-align: middle;">' + value.eo_number + '</td>';
                tableData += '<td style="vertical-align: middle; text-align: left;">' + value.material_number +
                    '<br>' + value.material_description + '</td>';
                tableData += '<td style="vertical-align: middle;">' + value.quantity + '</td>';
                tableData += '<td style="vertical-align: middle; padding : 0px; ">';
                tableData += '<button onclick="deletePackingList(' + key +
                    ')" class="btn btn-danger" style="padding: 3px 8px 3px 8px;"><i class="fa fa-trash-o"></i></button>';
                tableData += '</td>';
                tableData += '</tr>';
            });
            $('#tableBodyPack').append(tableData);

            $('#tablePack').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'buttons': {
                    buttons: []
                },
                'lengthChange': true,
                'searching': false,
                'ordering': false,
                'info': false,
                'autoWidth': true,
                "paging": false,
                "bJQueryUI": true,
                "bPaginate": false,
                "bAutoWidth": false,
                "processing": true,
                "footerCallback": function(tfoot, data, start, end, display) {
                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$%,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };
                    var api = this.api();
                    var totalPlan = api.column(3).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0)
                    $(api.column(3).footer()).html(totalPlan);
                },
            });
        }

        function showPrint() {
            if (packing_list.length > 0) {
                $("#completion_modal").modal('show');
            } else {
                openErrorGritter('Error!', 'Material belum dipilih');
                return false;
            }
        }

        function completion() {

            var data = {
                packing_list: packing_list,
            }

            $("#loading").show();
            $.post('{{ url('input/extra_order/completion') }}', data, function(result, status, xhr) {
                if (result.status) {

                    for (let i = 0; i < packing_list.length; i++) {
                        if (['PXZP', 'ZPA0'].includes(packing_list[i].storage_location)) {
                            printLabelZpro(
                                ('Tittle_' + packing_list[i].material_number +
                                    '_' + packing_list[i].storage_location +
                                    '_' + packing_list[i].quantity +
                                    '_' + i),
                                packing_list[i].material_number,
                                packing_list[i].quantity
                            );
                        }
                    }

                    packing_list = [];

                    fillTableList();
                    fillTablePack();
                    $('#eo_detail').DataTable().ajax.reload();
                    printLabel(result.eo_number_sequence);

                    $("#completion_modal").modal('hide');
                    $("#loading").hide();
                    openSuccessGritter('Success', result.message);

                } else {
                    $("#loading").hide();
                    openErrorGritter('Error!', result.message);
                }

            });
        }

        function printLabelZpro(tittle, material_number, quantity) {
            window.open('{{ url('index/print_label_zpro_direct') }}' + '/' + material_number + '/' + quantity, tittle,
                'height=250,width=350');

            openSuccessGritter('Success!', "Print Success");
        }

        function fillTableDetail() {

            var data = {
                storage_location: $("#storage_location").val(),
                status: 1
            }

            $('#eo_detail tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" />');
            });
            var table = $('#eo_detail').DataTable({
                'paging': true,
                'dom': 'Bfrtip',
                'responsive': true,
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
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                'order': [],
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/extra_order_detail') }}",
                    "data": data,
                },
                "columns": [{
                        "data": "eo_number_sequence"
                    },
                    {
                        "data": "material_number"
                    },
                    {
                        "data": "description"
                    },
                    {
                        "data": "location"
                    },
                    {
                        "data": "destination_shortname"
                    },
                    {
                        "data": "request_date"
                    },
                    {
                        "data": "quantity"
                    },
                    {
                        "data": "updated_at"
                    },
                    {
                        "data": "reprint"
                    },
                    {
                        "data": "delete"
                    }
                ]
            });

            table.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });

            $('#eo_detail tfoot tr').appendTo('#eo_detail thead');
        }

        function printLabel(eo_number_sequence) {
            newwindow = window.open('{{ url('index/label_extra_order/') }}' + '/' + eo_number_sequence, eo_number_sequence,
                'height=550,width=650');

            if (window.focus) {
                newwindow.focus();
            }

            return false;
        }

        function reprintDetail(eo_number_sequence) {
            if (confirm("Apakah anda ingin mencetak ulang Extra Order Label dari " + eo_number_sequence + " ?")) {
                printLabel(eo_number_sequence);
            }
        }

        function deleteDetail(sequence) {

            var message = 'List item ' + sequence + ' :\n';
            $('#eo_detail tbody tr').each(function() {
                if (sequence == $(this).find('td').eq(0).text()) {
                    var material_number = $(this).find('td').eq(1).text();
                    var description = $(this).find('td').eq(2).text();
                    var quantity = $(this).find('td').eq(6).text();
                    message += material_number + ' _ ' + description + ' _ ' + quantity + '\n';
                }
            });
            message += '\nApa anda yakin akan melakukan cancel completion data tersebut ?';

            if (confirm(message)) {
                $("#loading").show();
                var data = {
                    sequence: sequence,
                    status: 1
                }
                $.post('{{ url('input/extra_order/cancel') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fillTableList();
                        fillTablePack();
                        $('#eo_detail').DataTable().ajax.reload();

                        $("#loading").hide();
                        openSuccessGritter('Success!', result.message);
                    } else {
                        $("#loading").hide();
                        openErrorGritter('Error!', result.message);
                    }
                });
            }

        }



        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '2000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '2000'
            });
        }
    </script>
@endsection
