@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <style type="text/css">
        #tableDetail>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        tbody>tr>td {
            padding: 10px 5px 10px 5px;
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
            height: 40px;
            padding: 2px 5px 2px 5px;
        }

        .contr #loading {
            display: none;
        }

        .label-status {
            color: black;
            font-size: 0.8vw;
            border-radius: 4px;
            padding: 3px 10px 5px 10px;
            border: 1.5px solid black;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <section class="content">
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

        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <input id="role_code" value="{{ Auth::user()->role_code }}" hidden>
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">Filter<span class="text-purple"></span></h3>
                    </div>
                    <div class="box-body">
                        <div class="col-xs-6 col-xs-offset-3" style="padding: 0px;">
                            <div class="box box-primary box-solid" style="margin: 0px;">
                                <div class="box-body">
                                    <div class="col-xs-4" style="padding-left: 0px; padding-right: 2px;">
                                        <div class="form-group">
                                            <label>Check In Date From</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <input type="text" placeholder="Select Date"
                                                    class="form-control datepicker pull-right" id="check_in_from">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4" style="padding-left: 0px; padding-right: 2px;">
                                        <div class="form-group">
                                            <label>Check In Date To</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <input type="text" placeholder="Select Date"
                                                    class="form-control datepicker pull-right" id="check_in_to">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-4">
                                        <div class="form-group">
                                            <label>Category</label>
                                            <select class="form-control select2" multiple="multiple"
                                                data-placeholder="Select Category" id="category" style="width: 100%;">
                                                <option value="EXPORT">EXPORT</option>
                                                <option value="IMPORT">IMPORT</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xs-6 col-xs-offset-2" style="margin-top: 0.75%;">
                            <div class="form-group pull-right" style="margin: 0px;">
                                <button onclick="clearConfirmation()"
                                    class="btn btn-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Clear&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                <button onclick="showTable()" class="btn btn-primary"><span class="fa fa-search"></span>
                                    Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12">
            <div class="form-group pull-right">
                <a href="{{ url('index/security_check/') . '/' . 'check-in' }}" class="btn btn-primary" style="color:white">
                    &nbsp;<i class="fa fa-sign-in"></i>&nbsp;&nbsp;&nbsp;Check In
                </a>
            </div>
        </div>

        <div class="col-xs-12">
            <table class="table table-bordered table-striped table-hover" id="tableDetail" width="100%"
                style="font-size: 0.85vw;">
                <thead style="background-color: #605ca8; color: white;">
                    <tr>
                        <th style="text-align: center;">Category</th>
                        <th style="text-align: center;">Checklist ID</th>
                        <th style="text-align: center;">Driver Name</th>
                        <th style="text-align: center;">No. Pol</th>
                        <th style="text-align: center;">No. Container</th>
                        <th style="text-align: center;">Check In</th>
                        <th style="text-align: center;">Check Out</th>
                        <th style="text-align: center;">Report</th>
                    </tr>
                </thead>
                <tbody id="tableDetailBody">
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
                    </tr>
                </tfoot>
            </table>
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
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
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

            showTable();
        });

        function showTable() {

            var check_in_from = $('#check_in_from').val();
            var check_in_to = $('#check_in_to').val();
            var category = $('#category').val();

            var data = {
                check_in_from: check_in_from,
                check_in_to: check_in_to,
                category: category,
            }


            $('#loading').show();
            $.get('{{ url('fetch/checklist_container_security') }}', data, function(
                result, status, xhr) {
                if (result.status) {
                    $('#loading').hide();

                    $('#tableDetail').DataTable().clear();
                    $('#tableDetail').DataTable().destroy();

                    $('#tableDetail thead').html("");
                    var head = '';
                    head += '<tr>';
                    head += '<th style="text-align: center;">Category</th>';
                    head += '<th style="text-align: center;">Checklist ID</th>';
                    head += '<th style="text-align: center;">Driver Name</th>';
                    head += '<th style="text-align: center;">No.Pol</th>';
                    head += '<th style="text-align: center;">No.Container</th>';
                    head += '<th style="text-align: center;">Check In</th>';
                    head += '<th style="text-align: center;">Check Out</th>';
                    head += '<th style="text-align: center;">Report</th>';
                    head += '</tr>';
                    $('#tableDetail thead').append(head);

                    $('#tableDetail tfoot').html("");
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
                    foot += '</tr>';
                    $('#tableDetail tfoot').append(foot);

                    $('#tableDetailBody').html("");
                    var body = '';
                    for (var i = 0; i < result.checklist.length; i++) {

                        body += '<tr>';

                        body += '<td style="width:5%; text-align:center;">' +
                            result.checklist[i].category +
                            '</td>';

                        body += '<td style="width:5%; text-align:center;">' +
                            result.checklist[i].checklist_id +
                            '</td>';

                        body += '<td style="width:5%; text-align:center;">' +
                            result.checklist[i].driver_name +
                            '</td>';

                        body += '<td style="width:5%; text-align:center;">' +
                            result.checklist[i].vehicle_registration_number +
                            '</td>';

                        body += '<td style="width:10%; text-align:center;">' +
                            result.checklist[i].container_number +
                            '</td>';

                        body += '<td style="width:15%; text-align:center;">';
                        body += result.checklist[i].check_in_at;
                        body += '<br>';
                        var name = 'PIC : -';
                        for (let x = 0; x < result.security.length; x++) {
                            if (result.checklist[i].check_in_by == result.security[x].employee_id) {
                                name = 'PIC : ' + result.security[x].name;
                                break;
                            }
                        }
                        body += name;
                        body += '</td>';

                        if (result.checklist[i].check_out_at == null) {

                            body += '<td style="width:15%; text-align:center;">';
                            body += '<a class="btn btn-danger btn-md" ';
                            body += 'onclick="showCheckOut(\'' + result.checklist[i].checklist_id + '\')" ';
                            body += 'style="padding: 6px 12px 6px 12px;" >';
                            body += '&nbsp;<i class="fa fa-sign-in"></i>&nbsp;&nbsp;&nbsp;Check Out</a>';
                            body += '</td>';

                        } else {
                            body += '<td style="width:15%; text-align:center;">';
                            body += result.checklist[i].check_out_at;
                            body += '<br>';
                            var name = 'PIC : -';
                            for (let x = 0; x < result.security.length; x++) {
                                if (result.checklist[i].check_out_by == result.security[x].employee_id) {
                                    name = 'PIC : ' + result.security[x].name;
                                    break;
                                }
                            }
                            body += name;
                            body += '</td>';
                        }

                        body += '<td style="width:15%; text-align:center;">';
                        body += '<a class="btn btn-default btn-md" ';
                        body += 'onclick="showReport(\'' + result.checklist[i].checklist_id + '\')" ';
                        body += 'style="padding: 6px 12px 6px 12px;" >';
                        body += '&nbsp;<i class="fa fa-eye"></i>&nbsp;&nbsp;&nbsp;Show Checklist</a>';
                        body += '</td>';

                        body += '</tr>';
                    }
                    $('#tableDetailBody').append(body);

                    $('#tableDetail tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input style="text-align: center; width: 100%; color: grey;" type="text" placeholder="Search ' +
                            title + '" size="3"/>');
                    });

                    var table = $('#tableDetail').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [10, 25, 50, -1],
                            ['10 rows', '25 rows', '50 rows',
                                'Show all'
                            ]
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
                        "bAutoWidth": false
                    });

                    table.columns().every(function() {
                        var that = this;
                        $('input', this.footer()).on('keyup change',
                            function() {
                                if (that.search() !== this.value) {
                                    that
                                        .search(this.value)
                                        .draw();
                                }
                            });
                    });
                    $('#tableDetail tfoot tr').prependTo('#tableDetail thead');

                    $('#loading').hide();
                }

            });

        }

        function showCheckOut(checklist) {
            window.open('{{ url('index/security_check') }}' + '/' + checklist, '_self');
        }

        function showReport(checklist) {
            window.open('{{ url('index/security_check_report') }}' + '/' + checklist, '_blank');
        }


        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}')

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '5000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '5000'
            });
        }
    </script>
@endsection
