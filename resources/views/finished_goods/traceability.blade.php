@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.tagsinput.css') }}" rel="stylesheet">
    <style type="text/css">
        input {
            line-height: 22px;
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

        .att {
            cursor: pointer;
            color: lightskyblue;
            font-weight: bold;
            margin-left: 3%;
            margin-right: 3%;
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
            Finished Goods Tracer <span class="text-purple">FG完成品追跡</span>
            <small>Filters <span class="text-purple">フィルター</span></small>
        </h1>
        <ol class="breadcrumb" id="last_update"></ol>
    </section>
@stop
@section('content')
    <div id="loading"
        style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
        <p style="position: absolute; color: White; top: 45%; left: 45%;">
            <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
        </p>
    </div>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Detail Filters</h3>
                    </div>
                    <div class="box-body">
                        <form id="formFilter" method="get" action="{{ url('fetch/fg_traceability') }}">
                            <div class="col-md-4">
                                <div class="box box-primary box-solid">
                                    <div class="box-body">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Prod. Date From</label>
                                                <div class="input-group date">
                                                    <input type="text" placeholder="mm/dd/yyyy"
                                                        class="form-control pull-right" id="prodFrom" name="prodFrom">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Prod. Date To</label>
                                                <div class="input-group date">
                                                    <input type="text" placeholder="mm/dd/yyyy"
                                                        class="form-control pull-right" id="prodTo" name="prodTo">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Ship. Date From</label>
                                                <div class="input-group date">
                                                    <input type="text" placeholder="mm/dd/yyyy"
                                                        class="form-control pull-right" id="shipFrom" name="shipFrom">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Ship. Date To</label>
                                                <div class="input-group date">
                                                    <input type="text" placeholder="mm/dd/yyyy"
                                                        class="form-control pull-right" id="shipTo" name="shipTo">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Actual Ship. Date From</label>
                                                <div class="input-group date">
                                                    <input type="text" placeholder="mm/dd/yyyy"
                                                        class="form-control pull-right" id="blFrom" name="actualFrom">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Actual Ship. Date To</label>
                                                <div class="input-group date">
                                                    <input type="text" placeholder="mm/dd/yyyy"
                                                        class="form-control pull-right" id="blTo" name="actualTo">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box box-primary box-solid">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label>Origin Group</label>
                                            <select class="form-control select2" multiple="multiple" name="originGroup[]"
                                                id="originGroup" data-placeholder="Select Origin Group"
                                                style="width: 100%;">
                                                <option></option>
                                                @foreach ($origin_groups as $origin_group)
                                                    <option value="{{ $origin_group->origin_group_code }}">
                                                        {{ $origin_group->origin_group_code }} -
                                                        {{ $origin_group->origin_group_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Material Number</label>
                                            <select class="form-control select2" multiple="multiple" name="materialNumber[]"
                                                id="materialNumber" data-placeholder="Select Material Number"
                                                style="width: 100%;">
                                                <option></option>
                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->material_number }}">
                                                        {{ $material->material_number }} -
                                                        {{ $material->material_description }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Serial Number</label>
                                            <input type="text" class="form-control" name="serialNumber" id="serialNumber"
                                                placeholder="Enter Serial Number">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="box box-primary box-solid">
                                    <div class="box-body">
                                        <div class="form-group">
                                            <label>FLO Number</label>
                                            <input type="text" class="form-control" name="floNumber" id="floNumber"
                                                placeholder="Enter FLO Number">
                                        </div>
                                        <div class="form-group">
                                            <label>Invoice Number</label>
                                            <input type="text" class="form-control" name="invoiceNumber"
                                                id="invoiceNumber" placeholder="Enter Invoice Number">
                                        </div>
                                        <div class="form-group">
                                            <label>Destination</label>
                                            <select class="form-control select2" multiple="multiple" name="destination[]"
                                                id="destination" data-placeholder="Select Destination"
                                                style="width: 100%;">
                                                <option></option>
                                                @foreach ($destinations as $destination)
                                                    <option value="{{ $destination->destination_code }}">
                                                        {{ $destination->destination_code }}
                                                        ({{ $destination->destination_shortname }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group pull-right">
                                    <a href="javascript:void(0)" onClick="clearConfirmation()"
                                        class="btn btn-danger">Clear</a>
                                    <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span>
                                        Search</button>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <table id="traceabilityTable" class="table table-bordered table-striped table-hover">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 1%;">Prod. Date</th>
                                        <th style="width: 1%;">FLO No.</th>
                                        <th style="width: 1%;">HPL</th>
                                        <th style="width: 1%;">Material</th>
                                        <th style="width: 6%;">Description</th>
                                        <th style="width: 1%;">Serial No.</th>
                                        <th style="width: 1%;">Qty</th>
                                        <th style="width: 1%;">I/V</th>
                                        <th style="width: 1%;">Plan ST Date</th>
                                        <th style="width: 1%;">Act ST Date</th>
                                        <th style="width: 1%;">BL Date</th>
                                        <th style="width: 1%;">Dest.</th>
                                        <th style="width: 1%;">SO</th>
                                        <th style="width: 1%;">Position</th>
                                        <th style="width: 1%;">Evidence Att.</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    @foreach ($flo_details as $tr)
                                        <tr>
                                            <td>{{ $tr->pd_date }}</td>
                                            <td>{{ $tr->flo_number }}</td>
                                            <td>{{ $tr->origin_group_name }}</td>
                                            <td>{{ $tr->material_number }}</td>
                                            <td>{{ $tr->material_description }}</td>
                                            <td>{{ $tr->serial_number }}</td>
                                            <td>{{ $tr->quantity }}</td>
                                            <td>{{ $tr->invoice_number }}</td>
                                            <td>{{ $tr->st_date }}</td>
                                            <td>{{ $tr->actual_st_date }}</td>
                                            <td>{{ $tr->bl_date }}</td>
                                            <td>{{ $tr->destination_shortname }}</td>
                                            <td>{{ $tr->sales_order }}</td>

                                            @if ($tr->status == 'M')
                                                <td>Prod Maedaoshi</td>
                                            @elseif($tr->status == 0)
                                                <td>Prod Packing</td>
                                            @elseif($tr->status == 1)
                                                <td>Prod Packed</td>
                                            @elseif($tr->status == 2)
                                                <td>WH FSTK</td>
                                            @elseif($tr->status == 3)
                                                <td>Stuffing</td>
                                            @elseif($tr->status == 4)
                                                <td>Lading</td>
                                            @endif

                                            @php
                                                $data = [];
                                                foreach ($packing as $pk) {
                                                    if ($tr->origin_group_name == $pk->location && $tr->serial_number == $pk->serial_number) {
                                                        $data = json_decode($pk->photo);
                                                        break;
                                                    }
                                                }
                                                
                                                if (count($data) > 0) {
                                                    print_r('<td>');
                                                    for ($x = 0; $x < count($data); $x++) {
                                                        print_r('<i onclick="openAtt(\'' . $data[$x] . '\')" class="fa fa-paperclip att"></i>');
                                                    }
                                                    print_r('</td>');
                                                } else {
                                                    print_r('<td></td>');
                                                }
                                            @endphp
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
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
    {{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $('#prodFrom').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            $('#prodTo').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            $('#shipFrom').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            $('#shipTo').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            $('#blFrom').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            $('#blTo').datepicker({
                autoclose: true,
                todayHighlight: true
            });
            $('.select2').select2();
            generateDatatables();
        });

        function openAtt(photo) {
            window.open('{{ url('images/packing') }}' + '/' + photo, '_blank');
        }

        function generateDatatables() {

            $('#traceabilityTable').DataTable().destroy();
            var table = $('#traceabilityTable').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                "pageLength": 25,
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
                        }
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

        }

        function clearConfirmation() {
            location.reload(true);
        }

        function addZero(i) {
            if (i < 10) {
                i = "0" + i;
            }
            return i;
        }

        function getActualFullDate() {
            var d = new Date();
            var day = addZero(d.getDate());
            var month = addZero(d.getMonth() + 1);
            var year = addZero(d.getFullYear());
            var h = addZero(d.getHours());
            var m = addZero(d.getMinutes());
            var s = addZero(d.getSeconds());
            return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s + ")";
        }


        packing_data = [];

        function fillTable() {
            $('#loading').modal('show');
            var prodFrom = $('#prodFrom').val();
            var prodTo = $('#prodTo').val();
            var actualFrom = $('#actualFrom').val();
            var actualTo = $('#actualTo').val();
            var shipFrom = $('#shipFrom').val();
            var shipTo = $('#shipTo').val();
            var blFrom = $('#blFrom').val();
            var blTo = $('#blTo').val();
            var originGroup = $('#originGroup').val();
            var materialNumber = $('#materialNumber').val();
            var serialNumber = $('#serialNumber').val();
            var floNumber = $('#floNumber').val();
            var invoiceNumber = $('#invoiceNumber').val();
            var destination = $('#destination').val();
            var data = {
                prodFrom: prodFrom,
                prodTo: prodTo,
                actualFrom: actualFrom,
                actualTo: actualTo,
                shipFrom: shipFrom,
                shipTo: shipTo,
                blFrom: blFrom,
                blTo: blTo,
                originGroup: originGroup,
                materialNumber: materialNumber,
                serialNumber: serialNumber,
                floNumber: floNumber,
                invoiceNumber: invoiceNumber,
                destination: destination,
            }
            $.get('{{ url('fetch/fg_traceability') }}', data, function(result, status, xhr) {
                if (xhr.status == 200) {
                    if (result.status) {
                        $('#last_update').html('<b>Last Updated: ' + getActualFullDate() + '</b>');
                        $('#traceabilityTable').DataTable().clear();
                        $('#traceabilityTable').DataTable().destroy();
                        $('#tableBody').html("");
                        var tableData = '';
                        packing_data = [];

                        $.each(result.packing, function(key2, value2) {
                            packing_data.push({
                                serial_number: value2.serial_number,
                                location: value2.location,
                                photo: value2.photo
                            });
                        });

                        $.each(result.tableData, function(key, value) {
                            tableData += '<tr>';
                            tableData += '<td>' + value.pd_date + '</td>';
                            tableData += '<td>' + value.flo_number + '</td>';
                            tableData += '<td>' + value.origin_group_name + '</td>';
                            tableData += '<td>' + value.material_number + '</td>';
                            tableData += '<td>' + value.material_description + '</td>';
                            tableData += '<td>' + value.serial_number + '</td>';
                            tableData += '<td>' + value.quantity + '</td>';
                            tableData += '<td>' + value.invoice_number + '</td>';
                            tableData += '<td>' + value.st_date + '</td>';
                            tableData += '<td>' + value.actual_st_date + '</td>';
                            tableData += '<td>' + value.bl_date + '</td>';
                            tableData += '<td>' + value.destination_shortname + '</td>';
                            tableData += '<td>' + value.sales_order + '</td>';

                            if (value.status == 'M') {
                                tableData += '<td>Prod Maedaoshi</td>';
                            } else if (value.status == 0) {
                                tableData += '<td>Prod Packing</td>';
                            } else if (value.status == 1) {
                                tableData += '<td>Prod Packed</td>';
                            } else if (value.status == 2) {
                                tableData += '<td>WH FSTK</td>';
                            } else if (value.status == 3) {
                                tableData += '<td>Stuffing</td>';
                            } else if (value.status == 4) {
                                tableData += '<td>Lading</td>';
                            }

                            // if(!value.image){
                            // 	tableData += '<td>-</td>';
                            // }
                            // else{
                            // 	tableData += '<td><img width="240" src="'+ value.image +'"/></td>';
                            // }

                            var status = 0;
                            var data_packing = "";

                            for (var z = 0; z < packing_data.length; z++) {
                                // console.log(packing_data);
                                // var hpl = 0;
                                // if (packing_data[z].location == "Clarinet") {
                                // 	hpl = "042";
                                // }
                                // else if (packing_data[z].location == "Flute") {
                                // 	hpl = "041";
                                // }
                                // else if (packing_data[z].location == "Saxophone") {
                                // 	hpl = "043";
                                // }


                                if (value.origin_group_name == packing_data[z].location && value
                                    .serial_number == packing_data[z].serial_number) {

                                    var data = JSON.parse(packing_data[z].photo);
                                    for (var x = 0; x < data.length; x++) {
                                        data_packing +=
                                            '<a target="_blank" href="{{ url('images/packing') }}/' + data[
                                                x] + '"><i class="fa fa-paperclip"></i>';
                                    }

                                    status = 1;
                                } else {

                                }


                            }

                            if (status == 1) {
                                tableData += '<td>' + data_packing + '</td>'
                            } else {
                                tableData += '<td></td>'
                            }

                            // if( value.att > 0 ){
                            // 	tableData += '<td><a href="javascript:void(0)" id="'+ value.container_id +'" onClick="downloadAtt(id)" class="fa fa-paperclip"> '+ value.att +' attachment(s)</a></td>';
                            // }
                            // else
                            // {
                            // 	tableData += '<td><span id="'+ value.container_id +'" class="fa fa-paperclip"> '+ value.att +' attachment(s)</span></td>';
                            // }
                            tableData += '</tr>';
                        });
                        $('#tableBody').append(tableData);
                        $('#traceabilityTable').DataTable({
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

                        $('#loading').modal('hide');
                    } else {
                        $('#loading').modal('hide');
                        alert('Attempt to retrieve data failed');
                    }
                } else {
                    $('#loading').modal('hide');
                    alert('Disconnected from server');
                }
            });
        }

        function downloadAtt(id) {
            var data = {
                container_id: id
            }
            $.get('{{ url('download/att_container_departure') }}', data, function(result, status, xhr) {
                if (xhr.status == 200) {
                    document.location.href = (result.file_path);
                } else {
                    alert('Disconnected from server');
                }
            });
        }
    </script>
@endsection
