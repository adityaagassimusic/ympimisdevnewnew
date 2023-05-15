@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }

    input {
        line-height: 24px;
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

    #loading,
    #error {
        display: none;
    }
</style>
@stop

@section('header')
<section class="content-header">
    <h1>
        {{ $title }} <span class="text-purple">{{ $title_jp }}</span>
    </h1>
    <ol class="breadcrumb" id="last_update"></ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="font-size: 0.8vw;">
    <div id="loading"
    style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <div>
        <center>
            <span style="font-size: 3vw; text-align: center; position: fixed; top: 45%; left: 42.5%;"><i
                class="fa fa-spin fa-hourglass-half"></i>&nbsp;&nbsp;&nbsp;Loading ...</span>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="box-body">
                    <div class="row">

                        <div class="col-xs-3 col-xs-offset-2">
                            <div class="box box-primary box-solid">
                                <div class="box-body" style="padding-bottom: 0px;">
                                    <div class="col-xs-6" style="padding-left: 0px; padding-right: 2.5px;">
                                        <div class="form-group">
                                            <label>Submit From</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <input type="text" placeholder="Select Submit Date"
                                                class="form-control datepicker pull-right" id="submit_from"
                                                name="submit_from" style="font-size: 0.8vw;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="padding-left: 2.5px; padding-right: 0px;">
                                        <div class="form-group">
                                            <label>Submit To</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <input type="text" placeholder="Select Submit Date"
                                                class="form-control datepicker pull-right" id="submit_to"
                                                name="submit_to" style="font-size: 0.8vw;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="padding-left: 0px; padding-right: 2.5px;">
                                        <div class="form-group">
                                            <label>Stuffing From</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <input type="text" placeholder="Select Request Date"
                                                class="form-control datepicker pull-right" id="request_from"
                                                name="Request_from" style="font-size: 0.8vw;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="padding-left: 2.5px; padding-right: 0px;">
                                        <div class="form-group">
                                            <label>Stuffing To</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <input type="text" placeholder="Select Request Date"
                                                class="form-control datepicker pull-right" id="request_to"
                                                name="submit_to" style="font-size: 0.8vw;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-5" style="padding-left: 0px;">
                            <div class="box box-primary box-solid">
                                <div class="box-body" style="padding-bottom: 0px;">
                                    <div class="col-xs-6" style="padding-left: 5px; padding-right: 0px;">
                                        <div class="form-group">
                                            <label>Destination</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <select style="width: 100%;" class="form-control select2"
                                                multiple="multiple" id="destination" name="destination"
                                                data-placeholder="Select Destination">
                                                <option value="">&nbsp;</option>
                                                @foreach ($destinations as $destination)
                                                <option value="{{ $destination->destination_shortname }}">
                                                    {{ $destination->destination_shortname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xs-6" style="padding-left: 5px; padding-right: 0px;">
                                        <div class="form-group">
                                            <label>Way</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <select style="width: 100%;" class="form-control select2"
                                                multiple="multiple" id="shipment_by" name="shipment_by"
                                                data-placeholder="Select Shipment Way">
                                                <option value="">&nbsp;</option>
                                                <option value="SEA">SEA</option>
                                                <option value="AIR">AIR</option>
                                                <option value="TRUCK">TRUCK</option>
                                            </select>
                                            <input type="text" name="approval_status" id="approval_status"
                                            hidden>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-6" style="padding-left: 5px; padding-right: 0px;">
                                    <div class="form-group">
                                        <label>Receipt</label>
                                        <div class="input-group date" style="width: 100%;">
                                            <select style="width: 100%;" class="form-control select2"
                                            multiple="multiple" id="receipt" name="receipt"
                                            data-placeholder="Select Receipt">
                                            <option value="">&nbsp;</option>
                                            @foreach ($buyers as $buyers)
                                            <option value="{{ $buyers->attention }}">
                                                {{ $buyers->attention }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="approver_id" id="approver_id" hidden>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-8 col-xs-offset-2">
                        <div class="form-group pull-right" style="margin: 0px;">
                            <button onClick="clearConfirmation()"
                            class="btn btn-danger">&nbsp;&nbsp;&nbsp;&nbsp;Clear&nbsp;&nbsp;&nbsp;&nbsp;</button>
                            <button class="btn btn-primary" onclick="fillTable()"><span
                                class="fa fa-search"></span> Search</button>
                            </div>
                        </div>

                        <div style="padding: 1%;">
                            <div class="col-xs-12" style="width: 100%; overflow-x: auto; padding: 0px; white-space: nowrap;">
                                <table id="shipmentScheduleTable" class="table table-bordered table-striped table-hover"
                                style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="vertical-align: middle; width: 7.5%;">Submit Date</th>
                                        <th style="vertical-align: middle; width: 15%;">EO Number</th>
                                        <th style="vertical-align: middle; width: 1%;">Dest</th>
                                        <th style="vertical-align: middle; width: 40%;">Material</th>
                                        <th style="vertical-align: middle; width: 1%;">Sloc</th>
                                        <th style="vertical-align: middle; width: 7.5%;">Prod. Date</th>
                                        <th style="vertical-align: middle; width: 7.5%;">Stuffing Date</th>
                                        <th style="vertical-align: middle; width: 1%;">Qty Req.</th>
                                        <th style="vertical-align: middle; width: 1%;">Price</th>
                                        <th style="vertical-align: middle; width: 1%;">Amount</th>
                                        <th style="vertical-align: middle; width: 1%;">Act Prod.</th>
                                        <th style="vertical-align: middle; width: 1%;">Diff Act Prod.</th>
                                        <th style="vertical-align: middle; width: 1%;">Act Deliv.</th>
                                        <th style="vertical-align: middle; width: 1%;">Diff Act Deliv.</th>
                                        <th style="vertical-align: middle; width: 1%;">Act Stuff.</th>
                                        <th style="vertical-align: middle; width: 1%;">Diff Stuff.</th>
                                        <th style="vertical-align: middle; width: 1%;">Act Stuff. Date</th>
                                        <th style="vertical-align: middle; width: 1%;">Cont. ID</th>
                                        <th style="vertical-align: middle; width: 1%;">IV No.</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                                <tfoot style="background-color: RGB(252, 248, 227);">
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
<script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true
        });

        $('.select2').select2();

        fillTable();
    });

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

    function fillTable() {
        var submit_from = $('#submit_from').val();
        var submit_to = $('#submit_to').val();
        var request_from = $('#request_from').val();
        var request_to = $('#request_to').val();

        var destination = $('#destination').val();
        var shipment_by = $('#shipment_by').val();
        var receipt = $('#receipt').val();

        var data = {
            submit_from: submit_from,
            submit_to: submit_to,
            request_from: request_from,
            request_to: request_to,
            destination: destination,
            shipment_by: shipment_by,
            receipt: receipt,
        }

        $('#loading').show();
        $.get('{{ url('fetch/extra_order/data') }}', data, function(result, status, xhr) {
            if (xhr.status == 200) {
                if (result.status) {
                    $('#last_update').html('<b>Last Updated: ' + result.last_update + '</b>');
                    $('#shipmentScheduleTable').DataTable().clear();
                    $('#shipmentScheduleTable').DataTable().destroy();

                    $('#shipmentScheduleTable thead').html("");
                    var head = '';
                    head += '<tr>';
                    head += '<th style="vertical-align: middle; width: 7.5%;">Submit Date</th>';
                    head += '<th style="vertical-align: middle; width: 15%;">EO Number</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Dest</th>';
                    head += '<th style="vertical-align: middle; width: 40%;">Material</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Sloc</th>';
                    head += '<th style="vertical-align: middle; width: 7.5%;">Prod. Date</th>';
                    head += '<th style="vertical-align: middle; width: 7.5%;">Stuffing Date</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Qty Req.</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Price</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Amount</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Act Prod.</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Diff Prod.</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Act Deliv.</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Diff Deliv.</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Act Stuff.</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Diff Stuff.</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Stuff. Date</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">Cont. ID</th>';
                    head += '<th style="vertical-align: middle; width: 1%;">IV No.</th>';
                    head += '</tr>';
                    $('#shipmentScheduleTable thead').append(head);


                    $('#shipmentScheduleTable tfoot').html("");
                    var foot = '';
                    foot += '<tr>'
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '</tr>';
                    $('#shipmentScheduleTable tfoot').append(foot);


                    $('#tableBody').html("");
                    var tableData = '';
                    $.each(result.data, function(key, value) {
                        tableData += '<tr>';
                        tableData += '<td>' + value.submission_date + '</td>';
                        tableData += '<td>' + value.eo_number + '<br>' + value.attention + '</td>';
                        tableData += '<td>' + value.destination_shortname + '<br>BY ' + value.shipment_by + '</td>';
                        tableData += '<td style="text-align: left; width: 40%;">' + value.material_number + '<br>' + value.description +
                        '</td>';
                        tableData += '<td>' + value.storage_location + '</td>';
                        tableData += '<td>' + (value.due_date || '') + '</td>';
                        tableData += '<td>' + value.request_date + '</td>';
                        tableData += '<td style="text-align: right;">' + value.quantity + '</td>';
                        tableData += '<td style="text-align: right;">' + value.sales_price.toFixed(2) +
                        '</td>';
                        tableData += '<td style="text-align: right;">' + (value.quantity * value
                            .sales_price).toFixed(2) + '</td>';
                        tableData += '<td style="text-align: right;">' + value.act_prod + '</td>';
                        tableData += '<td style="text-align: right;">' + (value.act_prod - value.quantity) + '</td>';
                        tableData += '<td style="text-align: right;">' + value.act_delivery + '</td>';
                        tableData += '<td style="text-align: right;">' + (value.act_delivery - value.quantity) + '</td>';
                        tableData += '<td style="text-align: right;">' + value.act_stuffing + '</td>';
                        tableData += '<td style="text-align: right;">' + (value.act_stuffing - value.quantity) + '</td>';

                        if (value.act_stuffing > 0) {
                            tableData += '<td>' + value.act_stuffing_date + '</td>';
                        } else {
                            tableData += '<td></td>';
                        }

                        if (value.act_stuffing > 0) {
                            var container = '';
                            for (var i = 0; i < result.container.length; i++) {
                                if (value.id == result.container[i].eo_detail_id) {
                                    if (container.length > 0) {
                                        container += ', ';
                                    }
                                    container += result.container[i].container_id;
                                }
                            }
                            tableData += '<td>' + container + '</td>';
                        } else {
                            tableData += '<td></td>';
                        }

                        if (value.act_stuffing > 0) {
                            var invoice_number = '';
                            for (var i = 0; i < result.invoice_number.length; i++) {
                                if (value.id == result.invoice_number[i].eo_detail_id) {
                                    if (invoice_number.length > 0) {
                                        invoice_number += ', ';
                                    }
                                    invoice_number += result.invoice_number[i].invoice_number;
                                }
                            }
                            tableData += '<td>' + invoice_number + '</td>';
                        } else {
                            tableData += '<td></td>';
                        }

                        tableData += '</tr>';
                    });
                    $('#tableBody').append(tableData);

                    $('#shipmentScheduleTable tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="3"/>');
                    });

                    var table = $('#shipmentScheduleTable').DataTable({
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
                        "rowCallback": function(row, data, index) {
                                // if (data[13] == 0) {
                                //     $(row).find('td').eq(13).css('background-color',
                                //         'RGB(255,204,255)');
                                // } else if (data[13] == data[10]) {
                                //     $(row).find('td').eq(13).css('background-color',
                                //         'RGB(204,255,255)');
                                // } else {
                                //     $(row).find('td').eq(13).css('background-color',
                                //         'RGB(255,226,66)');
                                // }

                                // if (data[14] == 0) {
                                //     $(row).find('td').eq(14).css('background-color',
                                //         'RGB(255,204,255)');
                                // } else if (data[14] == data[10]) {
                                //     $(row).find('td').eq(14).css('background-color',
                                //         'RGB(204,255,255)');
                                // } else {
                                //     $(row).find('td').eq(14).css('background-color',
                                //         'RGB(255,226,66)');
                                // }

                                // if (data[15] == 0) {
                                //     $(row).find('td').eq(15).css('background-color',
                                //         'RGB(255,204,255)');
                                // } else if (data[15] == data[10]) {
                                //     $(row).find('td').eq(15).css('background-color',
                                //         'RGB(204,255,255)');
                                // } else {
                                //     $(row).find('td').eq(15).css('background-color',
                                //         'RGB(255,226,66)');
                                // }

                                if (data[11] < 0) {
                                    $(row).find('td').eq(11).css('background-color',
                                        'RGB(255,204,255)');
                                } else {
                                    $(row).find('td').eq(11).css('background-color',
                                        'RGB(204,255,255)');
                                }

                                if (data[13] < 0) {
                                    $(row).find('td').eq(13).css('background-color',
                                        'RGB(255,204,255)');
                                } else {
                                    $(row).find('td').eq(13).css('background-color',
                                        'RGB(204,255,255)');
                                }

                                if (data[15] < 0) {
                                    $(row).find('td').eq(15).css('background-color',
                                        'RGB(255,204,255)');
                                } else {
                                    $(row).find('td').eq(15).css('background-color',
                                        'RGB(204,255,255)');
                                }

                            }
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
$('#shipmentScheduleTable tfoot tr').prependTo('#shipmentScheduleTable thead');

$('#loading').hide();

} else {
    alert('Attempt to retrieve data failed');
}
} else {
    alert('Disconnected from server');
}
});
}
</script>
@endsection
