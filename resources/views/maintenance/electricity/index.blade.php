@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">
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
            text-align: center;
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
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $title }} <span class="text-purple">{{ $title_jp }}</span>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a data-toggle="modal" data-target="#consumption" class="btn btn-success btn-sm" style="color: white;">
                    &nbsp;<i class="fa fa-plus"></i>&nbsp;Electricity Consumption
                </a>
            </li>
        </ol>
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

        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-2">
                        <div class="input-group date pull-right" style="text-align: center;">
                            <div class="input-group-addon bg-green">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control monthpicker" id="month"
                                placeholder="Select Month">
                        </div>
                    </div>

                    <div class="col-xs-2" style="padding: 0px;">
                        <button onclick="fillTable()" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>

            <div class="col-xs-12" style="margin-top: 1%;">
                <table class="table table-bordered table-striped table-hover" id="tableDetail" width="100%">
                    <thead style="background-color: #605ca8; color: white;">
                        <tr>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;">Date</th>
                            <th colspan="3" style="text-align: center; vertical-align: middle;">Incoming</th>
                            <th colspan="3" style="text-align: center; vertical-align: middle;">Incoming Consumption</th>
                            <th colspan="4" style="text-align: center; vertical-align: middle;">Outgoing</th>
                            <th colspan="4" style="text-align: center; vertical-align: middle;">Outgoing Consumption</th>
                            {{-- <th rowspan="2" style="text-align: center; vertical-align: middle;">Input By</th> --}}
                            <th rowspan="2" style="text-align: center; vertical-align: middle;">Input At</th>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;">#</th>
                        </tr>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">BP</th>
                            <th style="text-align: center; vertical-align: middle;">LBP</th>
                            {{-- <th style="text-align: center; vertical-align: middle;">LBP2</th> --}}
                            <th style="text-align: center; vertical-align: middle;">KVARH</th>

                            <th style="text-align: center; vertical-align: middle;">WBP (kWh)</th>
                            <th style="text-align: center; vertical-align: middle;">LWBP (kWh)</th>
                            {{-- <th style="text-align: center; vertical-align: middle;">LWBP2 (kWh)</th> --}}
                            <th style="text-align: center; vertical-align: middle;">KVARH</th>

                            <th style="text-align: center; vertical-align: middle;">I</th>
                            <th style="text-align: center; vertical-align: middle;">II</th>
                            <th style="text-align: center; vertical-align: middle;">III</th>
                            <th style="text-align: center; vertical-align: middle;">IV</th>

                            <th style="text-align: center; vertical-align: middle;">I (kWh)</th>
                            <th style="text-align: center; vertical-align: middle;">II (kWh)</th>
                            <th style="text-align: center; vertical-align: middle;">III (kWh)</th>
                            <th style="text-align: center; vertical-align: middle;">IV (kWh)</th>

                        </tr>
                    </thead>
                    <tbody id="bodyDetail">
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

    </section>


    <div class="modal fade" id="consumption" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Add Electricity Consumption</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1">
                            <div class="col-xs-4 col-xs-offset-4">
                                <label>Select Date</label>
                                <div class="input-group date pull-right" style="text-align: center;">
                                    <div class="input-group-addon bg-green">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control datepicker" name="date" id="date"
                                        placeholder="Select Date ..." readonly>
                                </div>
                            </div>

                            <div class="col-xs-6" style="margin-top: 3%; text-align: center;">
                                <label>Stand Meter (Incoming)</label>

                                <div class="col-xs-8 col-xs-offset-2" style="margin-top: 3%;">
                                    <label style="float: left;">BP<span class="text-red">*</span></label>
                                    <input type="number" class="form-control numpad" name="bp" id="bp"
                                        placeholder="Input bP ...">
                                </div>

                                <div class="col-xs-8 col-xs-offset-2" style="margin-top: 3%;">
                                    <label style="float: left;">LBP<span class="text-red">*</span></label>
                                    <input type="number" class="form-control numpad" name="lbp1" id="lbp1"
                                        placeholder="Input lbP1 ...">
                                </div>

                                {{-- <div class="col-xs-8 col-xs-offset-2" style="margin-top: 3%;">
                                <label style="float: left;">LBP2</label>
                                <input type="number" class="form-control numpad" name="lbp2" id="lbp2" placeholder="Input lbP2 ...">
                            </div> --}}

                                <div class="col-xs-8 col-xs-offset-2" style="margin-top: 3%;">
                                    <label style="float: left;">KVARH<span class="text-red">*</span></label>
                                    <input type="number" class="form-control numpad" name="kvarh" id="kvarh"
                                        placeholder="Input kvarh ...">
                                </div>
                            </div>

                            <div class="col-xs-6" style="margin-top: 3%; text-align: center;">
                                <label>Stand Meter (Outgoing)</label>

                                <div class="col-xs-8 col-xs-offset-2" style="margin-top: 3%;">
                                    <label style="float: left;">Outgoing I<span class="text-red">*</span></label>
                                    <input type="number" class="form-control numpad" name="outgoing_i" id="outgoing_i"
                                        placeholder="Input Outgoing I ...">
                                </div>
                                <div class="col-xs-8 col-xs-offset-2" style="margin-top: 3%;">
                                    <label style="float: left;">Outgoing II<span class="text-red">*</span></label>
                                    <input type="number" class="form-control numpad" name="outgoing_ii"
                                        id="outgoing_ii" placeholder="Input Outgoing II ...">
                                </div>
                                <div class="col-xs-8 col-xs-offset-2" style="margin-top: 3%;">
                                    <label style="float: left;">Outgoing III<span class="text-red">*</span></label>
                                    <input type="number" class="form-control numpad" name="outgoing_iii"
                                        id="outgoing_iii" placeholder="Input Outgoing III ...">
                                </div>
                                <div class="col-xs-8 col-xs-offset-2" style="margin-top: 3%;">
                                    <label style="float: left;">Outgoing IV<span class="text-red">*</span></label>
                                    <input type="number" class="form-control numpad" name="outgoing_iv"
                                        id="outgoing_iv" placeholder="Input Outgoing IV ...">
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="saveConsumption()" class="btn btn-success">Submit </button>
                    </div>
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

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

            $('.datepicker').datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true,
                // startDate: '{{ date('Y-m-d') }}'
            });

            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });

            $('body').toggleClass("sidebar-collapse");

            // $('.select2').select2();

            fillTable();

        });

        $('#consumption').on('hidden.bs.modal', function() {
            $('#date').val('');

            $('#lbp1').val('');
            $('#lbp2').val('');
            $('#bp').val('');
            $('#kvarh').val('');

            $('#outgoing_i').val('');
            $('#outgoing_ii').val('');
            $('#outgoing_iii').val('');
            $('#outgoing_iV').val('');
        });

        function saveConsumption(argument) {
            var date = $('#date').val();

            var lbp1 = $('#lbp1').val();
            var lbp2 = $('#lbp2').val();
            var bp = $('#bp').val();
            var kvarh = $('#kvarh').val();

            var outgoing_i = $('#outgoing_i').val();
            var outgoing_ii = $('#outgoing_ii').val();
            var outgoing_iii = $('#outgoing_iii').val();
            var outgoing_iv = $('#outgoing_iv').val();

            if (date == '') {
                openErrorGritter('Error!', 'Tanggal belum dipilih');
                return false;
            }

            if (lbp1 == '') {
                openErrorGritter('Error!', 'lbP belum diinput');
                return false;
            }

            // if(lbp2 == ''){
            //     openErrorGritter('Error!', 'lbP2 belum diinput');
            //     return false;
            // }

            if (bp == '') {
                openErrorGritter('Error!', 'bP belum diinput');
                return false;
            }

            if (kvarh == '') {
                openErrorGritter('Error!', 'KVARH belum diinput');
                return false;
            }

            if (outgoing_i == '') {
                openErrorGritter('Error!', 'Outgoing I belum diinput');
                return false;
            }

            if (outgoing_ii == '') {
                openErrorGritter('Error!', 'Outgoing II belum diinput');
                return false;
            }

            if (outgoing_iii == '') {
                openErrorGritter('Error!', 'Outgoing III belum diinput');
                return false;
            }

            if (outgoing_iv == '') {
                openErrorGritter('Error!', 'Outgoing IV belum diinput');
                return false;
            }

            var data = {
                date: date,
                lbp1: lbp1,
                lbp2: lbp2,
                bp: bp,
                kvarh: kvarh,
                outgoing_i: outgoing_i,
                outgoing_ii: outgoing_ii,
                outgoing_iii: outgoing_iii,
                outgoing_iv: outgoing_iv,
            }

            $("#loading").show();
            $.post('{{ url('input/maintenance/electricity_consumption') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#tableDetail').DataTable().ajax.reload();

                    $("#consumption").modal('hide');

                    $("#loading").hide();
                    openSuccessGritter('Success!', result.message);


                } else {
                    $("#loading").hide();
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function deleteData(id) {

            var data = {
                id: id,
            }

            $("#loading").show();
            $.post('{{ url('delete/maintenance/electricity_consumption') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#tableDetail').DataTable().ajax.reload();

                    $("#loading").hide();
                    openSuccessGritter('Success!', result.message);


                } else {
                    $("#loading").hide();
                    openErrorGritter('Error!', result.message);
                }
            });

        }

        function fillTable() {
            $('#tableDetail').DataTable().destroy();

            var data = {
                month: $("#month").val()
            }

            $('#tableDetail tfoot th').each(function() {
                var title = $(this).text();
                $(this).html(
                    '<input style="text-align: center;" type="text" class="dt-search" placeholder="Search ' +
                    title + '" />');
            });
            var table = $('#tableDetail').DataTable({
                'paging': true,
                'dom': 'Bfrtip',
                'responsive': true,
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
                    "url": "{{ url('fetch/maintenance/electricity') }}",
                    "data": data,
                },
                "columnDefs": [{
                    "targets": [16],
                    "createdCell": function(td, cellData, rowData, row, col) {
                        if (cellData != null) {
                            if (cellData.includes("Holiday")) {
                                $(td).closest("tr").css('background-color', '#ffccff');
                                $(td).css('padding-top', '9px');
                                $(td).css('padding-bottom', '9px');
                            }
                        }
                    }
                }],
                "columns": [{
                        "data": "week_date"
                    },

                    {
                        "data": "bp"
                    },
                    {
                        "data": "lbp1"
                    },
                    // { "data": "lbp2" },
                    {
                        "data": "kvarh"
                    },

                    {
                        "data": "wbp"
                    },
                    {
                        "data": "lwbp1"
                    },
                    // { "data": "lwbp2" },
                    {
                        "data": "consumption_kvarh"
                    },

                    {
                        "data": "outgoing_i"
                    },
                    {
                        "data": "outgoing_ii"
                    },
                    {
                        "data": "outgoing_iii"
                    },
                    {
                        "data": "outgoing_iv"
                    },

                    {
                        "data": "consumption_outgoing_i"
                    },
                    {
                        "data": "consumption_outgoing_ii"
                    },
                    {
                        "data": "consumption_outgoing_iii"
                    },
                    {
                        "data": "consumption_outgoing_iv"
                    },

                    {
                        "data": "created_at"
                    },
                    {
                        "data": "edit"
                    }
                ]
            });

            table.columns().every(function() {
                var that = this;

                $('.dt-search', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });

            $('#tableDetail tfoot tr').appendTo('#tableDetail thead');
        }

        function name(params) {

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
