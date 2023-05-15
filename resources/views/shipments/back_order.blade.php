@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        #bodyTb>tr:hover td {
            background-color: #7dfa8c !important;
            color: black !important;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            padding-top: 7.5px;
            padding-bottom: 7.5px;
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
            Back Order Sales
        </h1>
        <ol class="breadcrumb">
            <li>
                @if (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'PC'))
                    <a data-toggle="modal" data-target="#uploadModal" class="btn btn-success btn-sm" style="color:white">
                        Import
                    </a>
                @endif
            </li>
        </ol>
    </section>
@endsection
@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        @if (session('status'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-xs-2 no-padding" style="margin-bottom: 1%;">
                <div class="col-xs-12">
                    <label>Select Month</label>
                    <div class="input-group date pull-right" style="text-align: center;">
                        <div class="input-group-addon bg-purple">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control monthpicker" name="month" id="month"
                            onchange="drawTableNew()" placeholder="Select Month">
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-body">
                        <table id="tablePs" class="table table-bordered table-hover table-responsive" style="">
                            <thead id="headTb">
                                <tr>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="bodyTb">
                                <tr>
                                    <th></th>
                                </tr>
                            </tbody>
                            <tfoot id="footTb">
                                <tr>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Upload Back Order</h4>
                        Format :
                        [<b><i>Material Number</i></b>]
                        [<b><i>Sales to Party</i></b>]
                        [<b><i>Quantity</i></b>]
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-10 col-xs-offset-1">
                                <div class="col-xs-12" style="margin-top: 2%;">
                                    <label>Back Order Month :<span class="text-red">*</span></label>
                                </div>
                                <div class="col-xs-6" style="padding-right: 0px;">
                                    <div class="input-group date pull-right" style="text-align: center;">
                                        <div class="input-group-addon bg-green">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control monthpicker" name="upload_month"
                                            id="upload_month" placeholder="Select Month">
                                    </div>
                                </div>
                                <div class="col-xs-12" style="margin-top: 2%;">
                                    <label>Data :<span class="text-red">*</span></label>
                                </div>
                                <div class="col-xs-12">
                                    <textarea id="upload_data" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row" style="margin-top: 7%; margin-right: 2%;">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button onclick="uploadData()" class="btn btn-success">Upload </button>
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
    <script src="{{ url('js/dataTables.fixedColumns.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {

            $('#due_date').datepicker({
                autoclose: true,
                format: "dd/mm/yyyy",
                todayHighlight: true
            });

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

            $('.select2').select2();

            drawTable();
        });

        function drawTable() {
            var data = {
                month: $('#month').val(),
            }

            $('#loading').show();

            $.get('{{ url('fetch/back_order_sales') }}', data, function(result, status, xhr) {

                if (result.status) {

                    $('#tablePs').DataTable().clear();
                    $('#tablePs').DataTable().destroy();

                    $('#headTb').html("");
                    var head_style = 'vertical-align: middle; text-align: center; background-color:#a488aa;';
                    var tableHead = '<tr>';
                    tableHead += '<th style="' + head_style + '">MONTH</th>';
                    tableHead += '<th style="' + head_style + '">GMC</th>';
                    tableHead += '<th style="' + head_style + '">DESCRIPTION</th>';
                    tableHead += '<th style="' + head_style + '">CATEGORY</th>';
                    tableHead += '<th style="' + head_style + '">PRICE</th>';
                    tableHead += '<th style="' + head_style + '">DESTINATION</th>';
                    tableHead += '<th style="' + head_style + '">QTY</th>';
                    tableHead += '<th style="' + head_style + '">AMOUNT</th>';
                    tableHead += '</tr>';
                    $('#headTb').append(tableHead);

                    $('#footTb').html("");
                    var tableFoot = '<tr>';
                    tableFoot += '<th style="background-color:#a488aa;"></th>';
                    tableFoot += '<th style="background-color:#a488aa;"></th>';
                    tableFoot += '<th style="background-color:#a488aa;"></th>';
                    tableFoot += '<th style="background-color:#a488aa;"></th>';
                    tableFoot += '<th style="background-color:#a488aa;"></th>';
                    tableFoot += '<th style="background-color:#a488aa;"></th>';
                    tableFoot += '<th style="background-color:#a488aa;"></th>';
                    tableFoot += '<th style="background-color:#a488aa;"></th>';
                    tableFoot += '</tr>';
                    $('#footTb').append(tableFoot);


                    $('#bodyTb').html("");
                    var tableBody = '';

                    for (var i = 0; i < result.backorder.length; i++) {
                        tableBody += '<tr>';

                        tableBody += '<td style="vertical-align: middle; text-align: center;">';
                        tableBody += result.backorder[i].sales_month_txt + '</td>';

                        tableBody += '<td style="vertical-align: middle; text-align: center;">';
                        tableBody += result.backorder[i].material_number + '</td>';

                        tableBody += '<td style="vertical-align: middle; text-align: left; width: 40%; ">';
                        tableBody += result.backorder[i].material_description + '</td>';

                        tableBody += '<td style="vertical-align: middle; text-align: center;">';
                        tableBody += result.backorder[i].category + '</td>';

                        tableBody += '<td style="vertical-align: middle; text-align: right;">';
                        tableBody += result.backorder[i].price.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                            style: "currency",
                            currency: "USD"
                        });
                        tableBody += '</td>';

                        tableBody += '<td style="vertical-align: middle; text-align: center;">';
                        tableBody += result.backorder[i].destination_shortname + '</td>';

                        tableBody += '<td style="vertical-align: middle; text-align: right;">';
                        tableBody += result.backorder[i].quantity + '</td>';

                        var amount = result.backorder[i].quantity * result.backorder[i].price;
                        tableBody += '<td style="vertical-align: middle; text-align: right;">';
                        tableBody += amount.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                            style: "currency",
                            currency: "USD"
                        });
                        tableBody += '</td>';

                        tableBody += '</tr>';
                    }

                    $('#bodyTb').append(tableBody);


                    $('#tablePs tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input style="text-align: center; width: 100%;" type="text" placeholder="Search ' +
                            title +
                            '" size="4"/>');
                    });
                    var table = $('#tablePs').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [-1],
                            ['Show all']
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
                                    var theadname = $("#example1 th").eq([dd]).text();
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
                                            if ($("#example1 th").eq([dd]).text() ==
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

                        $('input', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('#tablePs tfoot tr').appendTo('#tablePs thead');

                    $('#loading').hide();
                }


            });

        }

        function uploadData() {

            var month = $('#upload_month').val();
            var upload = $('#upload_data').val();

            if (month == '' || data == '') {
                openErrorGritter('Error', 'All data must be complete');
                return false;
            }

            var data = {
                month: month,
                upload: upload,
            }

            $('#loading').show();
            $.post('{{ url('input/back_order_sales') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#upload_data').val('');
                    $('#upload_month').val('');

                    $('#uploadModal').modal('hide');
                    drawTable();

                    $('#loading').hide();
                    openSuccessGritter('Success', 'Upload Back Order Data Success');

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error', result.message);
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

@stop
