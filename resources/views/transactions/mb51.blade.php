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
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 0;
            padding-bottom: 0;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
            vertical-align: middle;
        }

        .modal-dialog {
            overflow-y: initial !important
        }

        .modal-body {
            max-height: 80vh;
            overflow-y: auto;
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
            {{ $page }}
        </h1>
        <ol class="breadcrumb">
            <li>
                <a data-toggle="modal" data-target="#uploadModal" class="btn btn-success btn-sm" style="color:white;">
                    &nbsp;<i class="fa fa-plus-square-o"></i>&nbsp;Upload Data
                </a>
                <a data-toggle="modal" data-target="#resumeModal" class="btn btn-default btn-sm"
                    style="color:white; color: black;">
                    &nbsp;<i class="fa fa-list"></i>&nbsp;Resume Data
                </a>
            </li>
        </ol>
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
            <div class="col-xs-12" style="padding: 0px;">
                <form id="formFilter" method="get" action="{{ url('fetch/mb51_transaction') }}">
                    <div class="col-xs-2" style="padding-right: 0px;">
                        <div class="input-group date pull-right" style="text-align: center;">
                            <div class="input-group-addon bg-green">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control datepicker" onchange="checkPostingDate()"
                                id="posting_date" name="posting_date" placeholder="Select Posting Date"
                                value="{{ $posting_date }}">
                        </div>
                    </div>
                    <div class="col-xs-1" style="padding-right: 0px;">
                        <button style="width: 100%;" type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
                <form id="formTxt" method="get" action="{{ url('download/mb51_transaction') }}">
                    <input id="txt_posting_date" name="txt_posting_date" value="{{ $posting_date }}" type="hidden">
                    <div class="col-xs-1" style="padding-right: 0px;">
                        <button style="width: 100%;" type="submit" class="btn btn-default"
                            style="border-color: black; color: black;">Download</button>
                    </div>
                </form>
            </div>
            <div class="col-xs-12" style="margin-top: 1%;">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-body" style="overflow-x: auto; padding-right: 1%; margin-right: 1%;">
                        <table id="tableResult" class="table table-bordered table-hover" style="width: 100%;">
                            <thead style="color: black; background-color: grey;">
                                <tr>
                                    <th style="text-align: center;">Plnt</th>
                                    <th style="text-align: center;">Val. Type.</th>
                                    <th style="text-align: center;">MvT</th>
                                    <th style="text-align: center;">Material</th>
                                    <th style="text-align: center; width:40%;">Material Description</th>
                                    <th style="text-align: center;">SLoc</th>
                                    <th style="text-align: center;">Reference</th>
                                    <th style="text-align: center;">Order</th>
                                    <th style="text-align: center;">User Name</th>
                                    <th style="text-align: center;">Time</th>
                                    <th style="text-align: center;">Doc. Header Text</th>
                                    <th style="text-align: center;">Cost Ctr</th>
                                    <th style="text-align: center;">Item</th>
                                    <th style="text-align: center;">Mat. Doc.</th>
                                    <th style="text-align: center;">Item</th>
                                    <th style="text-align: center;">Reserv.No.</th>
                                    <th style="text-align: center;">PO</th>
                                    <th style="text-align: center;">Vendor</th>
                                    <th style="text-align: center;">Reas.</th>
                                    <th style="text-align: center;">Customer</th>
                                    <th style="text-align: center;">Entry Date</th>
                                    <th style="text-align: center;">Pstng Date</th>
                                    <th style="text-align: center;">Quantity</th>
                                    <th style="text-align: center;">EUn</th>
                                    <th style="text-align: center;">Amount in LC</th>
                                    <th style="text-align: center;">Crcy</th>
                                    <th style="text-align: center;">Doc. Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                    <tr>
                                        <td style="text-align: left;">{{ $row->plnt }}</td>
                                        <td style="text-align: left;">{{ $row->val_type }}</td>
                                        <td style="text-align: left;">{{ $row->mvt }}</td>
                                        <td style="text-align: left;">{{ $row->material }}</td>
                                        <td style="text-align: left; width:40%;">{{ $row->material_description }}</td>
                                        <td style="text-align: left;">{{ $row->sloc }}</td>
                                        <td style="text-align: left;">{{ $row->reference }}</td>
                                        <td style="text-align: left;">{{ $row->order }}</td>
                                        <td style="text-align: left;">{{ $row->user_name }}</td>
                                        <td style="text-align: left;">{{ $row->time }}</td>
                                        <td style="text-align: left;">{{ $row->document_header_text }}</td>
                                        <td style="text-align: left;">{{ $row->cost_ctr }}</td>
                                        <td style="text-align: left;">{{ $row->item_1 }}</td>
                                        <td style="text-align: left;">{{ $row->mat_doc }}</td>
                                        <td style="text-align: left;">{{ $row->item_2 }}</td>
                                        <td style="text-align: left;">{{ $row->reserv_no }}</td>
                                        <td style="text-align: left;">{{ $row->po }}</td>
                                        <td style="text-align: left;">{{ $row->vendor }}</td>
                                        <td style="text-align: left;">{{ $row->reas }}</td>
                                        <td style="text-align: left;">{{ $row->customer }}</td>
                                        <td style="text-align: left;">
                                            {{ date('m-d-Y', strtotime($row->entry_date)) }}
                                        </td>
                                        <td style="text-align: left;">
                                            {{ date('m-d-Y', strtotime($row->posting_date)) }}
                                        </td>
                                        <td style="text-align: right;">{{ $row->quantity }}</td>
                                        <td style="text-align: left;">{{ $row->eun }}</td>
                                        <td style="text-align: right;">{{ $row->amount_in_lc }}</td>
                                        <td style="text-align: left;">{{ $row->crcy }}</td>
                                        <td style="text-align: left;">
                                            {{ date('m-d-Y', strtotime($row->doc_date)) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="uploadModal" aria-hidden="true" data-keyboard="false" data-backdrop="static"
        style="overflow-y: auto;">
        <div class="modal-dialog" style="width: 70%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Transactions</h4>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="form-group">
                        <textarea id="upload" style="height: 100px; width: 100%;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button class="btn btn-success pull-right" onclick="uploadData();">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadResult" aria-hidden="true" data-keyboard="false" data-backdrop="static"
        style="overflow-y: auto;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Result</h4>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <span style="font-size:1.5vw;">Success: <span id="suceess-count"
                            style="font-style:italic; font-weight:bold; color: green;"></span> Row(s)</span>
                    <span style="font-size:1.5vw;"> ~ Error: <span id="error-count"
                            style="font-style:italic; font-weight:bold; color: red;"></span> Row(s)</span>

                    <table id="tableError" style="border: none;">
                        <tbody id="bodyError">
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="resumeModal" aria-hidden="true" data-keyboard="false" data-backdrop="static"
        style="overflow-y: auto;">
        <div class="modal-dialog" style="width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Resume Data</h4>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="col-xs-3">
                        <div class="input-group date pull-right" style="text-align: center;">
                            <div class="input-group-addon bg-grey">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control datepicker" onchange="showResume()" id="year"
                                placeholder="Select Year" style="text-align: center;">
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div id="resume-body"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('#posting_date').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });

            $("#year").datepicker({
                changeYear: true,
                viewMode: "years",
                minViewMode: "years",
                format: "yyyy",
            });

            $('#posting_date').val('');

            fetchDataTable();

        });

        (function() {
            document.getElementById('formFilter').addEventListener('submit', function(event) {
                var posting_date = document.getElementById('posting_date').value.length;

                if (posting_date === 0) {
                    event.preventDefault();
                    openErrorGritter('Error!', 'Select Posting Date');
                }
            }, false);
        })();

        function checkPostingDate() {
            var posting_date = $('#posting_date').val();
            $('#txt_posting_date').val(posting_date);
        }

        function showResume() {
            var year = $('#year').val();

            var data = {
                year: year
            }

            $.get('{{ url('fetch/resume_mb51_transaction') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#resume-body').html('');

                    var master_month = [
                        'MONTH',
                        'JANUARY',
                        'FEBRUARY',
                        'MARCH',
                        'APRIL',
                        'MAY',
                        'JUNE',
                        'JULY',
                        'AUGUST',
                        'SEPTEMBER',
                        'OCTOBER',
                        'NOVEMBER',
                        'DECEMBER',
                    ];

                    var resume = result.resume;
                    var css = 'border: 1px solid black; text-align: center; padding: 0px;';
                    var table = '';
                    for (let repeat_month = 1; repeat_month <= 12; repeat_month++) {

                        if ((repeat_month + 2) % 3 == 0) {
                            table += '<div class="col-xs-12">';
                        }

                        table += '<div class="col-xs-4">';
                        table += '<table class="table">';
                        table += '<tbody>';

                        table += '<tr>';
                        table += '<th colspan="7" style="text-align: center; font-weight: bold;">' + master_month[
                            repeat_month] + '</th>';
                        table += '</tr>';
                        table += '<tr>';
                        table += '<th>MON</th>';
                        table += '<th>TUE</th>';
                        table += '<th>WED</th>';
                        table += '<th>THU</th>';
                        table += '<th>FRI</th>';
                        table += '<th>SAT</th>';
                        table += '<th>SUN</th>';
                        table += '</tr>';

                        var max = 0;
                        for (var x = 0; x < result.count_day.length; x++) {
                            if (repeat_month == result.count_day[x].month) {
                                max = result.count_day[x].count;
                                break;
                            }
                        }


                        var this_month = [];
                        var date = 1;
                        for (let repeat_week = 1; repeat_week <= 6; repeat_week++) {
                            if (date > max) {
                                break;
                            }
                            table += '<tr>';

                            for (let repeat_day = 1; repeat_day <= 7; repeat_day++) {

                                var set_day = false;
                                for (let j = 0; j < result.calendar.length; j++) {
                                    if (repeat_month == result.calendar[j].month && repeat_day == result.calendar[j]
                                        .day && date == result.calendar[j].date) {

                                        if (!this_month.includes(result.calendar[j].date)) {

                                            var css_holiday = '';
                                            if (result.calendar[j].remark == 'H') {
                                                css_holiday = 'color: red; font-weight:bold;';
                                            }

                                            var count = 0;
                                            var css_count = 'background-color: #ffccff;';
                                            for (var k = 0; k < resume.length; k++) {
                                                if (resume[k].posting_date == result.calendar[j].week_date) {
                                                    count = resume[k].count;
                                                    resume.splice(k, 1);
                                                    css_count = 'background-color: #ccfefe;';
                                                    break;
                                                }
                                            }

                                            table += '<th style="' + css + css_holiday + css_count + '">';
                                            table += result.calendar[j].date;
                                            table += '<br><span style="font-size: 12px;">(' + count + ')</span>';
                                            table += '</th>';
                                            set_day = true;
                                            this_month.push(result.calendar[j].date);
                                            date++;
                                            break;
                                        }
                                    }
                                }

                                if (!set_day) {
                                    table += '<th style="' + css + '"></th>';
                                }

                            }
                            table += '</tr>';

                        }

                        table += '</tbody>';
                        table += '</table>';
                        table += '</div>';

                        if (repeat_month % 3 == 0) {
                            table += '</div">';
                        }

                    }

                    $('#resume-body').append(table);



                }
            });

        }

        $('#resumeModal').on('hidden.bs.modal', function() {
            $('#year').val('');
            $('#resume-body').html('');
        });


        function fetchDataTable() {
            $('#tableResult').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [25, 50, -1],
                    ['25 rows', '50 rows', 'Show all']
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
                'autoWidth': false,
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': false,
                'order': [],
                'info': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                'columnDefs': [{
                    "targets": 4,
                    "width": "40%",
                }]
            });
        }

        function uploadData() {
            var upload = $('#upload').val();

            var data = {
                upload: upload,
            }

            if (upload == "") {
                alert('Data upload tidak boleh kosong');
                return false;
            }

            $('#loading').show();
            $.post('{{ url('upload/mb51_transaction') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#upload').val('');
                    $('#uploadModal').modal('hide');

                    $('#suceess-count').text(result.ok_count.length);
                    $('#error-count').text(result.error_count.length);

                    $('#uploadResult').modal('show');
                    $('#loading').hide();

                    openSuccessGritter('Success!', result.message);
                } else {
                    $('#suceess-count').text(0);
                    $('#error-count').text(result.row);

                    $('#bodyError').html("");
                    var tableData = "";
                    var css = "padding: 0px 5px 0px 5px;";
                    for (var i = 0; i < result.posting_date.length; i++) {
                        tableData += '<tr>';
                        tableData += '<td style="' + css + ' width:80%; text-align:left;">';
                        tableData += result.posting_date[i].posting_date;
                        tableData += '</td>';
                        tableData += '</tr>';
                    }

                    if (result.posting_date.length > 0) {
                        $('#bodyError').append(tableData);
                        $('#tableError').show();
                    }

                    $('#uploadResult').modal('show');
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
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
