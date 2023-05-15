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
                <a data-toggle="modal" data-target="#nonIndirectModal" class="btn btn-success btn-sm" style="color:white">
                    <i class="fa fa-plus"></i>&nbsp;<b>Direct</b>, <b>Subcont</b> & <b>KD</b>
                </a>
                <a data-toggle="modal" data-target="#indirectModal" class="btn btn-success btn-sm" style="color:white">
                    &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-plus"></i>&nbsp;Indirect&nbsp;&nbsp;&nbsp;&nbsp;
                </a>
                <a data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" style="color:white">
                    &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-trash"></i>&nbsp;Delete&nbsp;&nbsp;&nbsp;&nbsp;
                </a>
                <a data-toggle="modal" data-target="#info" class="btn btn-default btn-sm" style="border-color: grey">
                    <i class="fa fa-info-circle"></i>&nbsp;Guidance
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
            <div class="col-xs-12">
                <div class="nav-tabs-custom" style="margin-top: 1%;">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Direct &
                                Subcont</a></li>
                        <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Indirect</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                            <table id="tableNonIndirect" class="table table-bordered table-hover" style="width: 100%;">
                                <thead id="headNonIndirect"
                                    style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                    <th></th>
                                </thead>
                                <tbody id="bodyNonIndirect">
                                    <td></td>
                                </tbody>
                                <tfoot style="background-color: rgb(252, 248, 227);" id="footNonIndirect">
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
                            <table id="tableIndirect" class="table table-bordered table-hover" style="width: 100%;">
                                <thead id="headIndirect"
                                    style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                    <th></th>
                                </thead>
                                <tbody id="bodyIndirect">
                                    <td></td>
                                </tbody>
                                <tfoot style="background-color: rgb(252, 248, 227);" id="footIndirect">
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Upload Data Guidance</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-3">
                            <h3 class="text-success">Group</h3>
                            Direct<br>
                            Indirect<br>
                            Subcont<br>
                            KD
                        </div>
                        <div class="col-xs-3">
                            <h3 class="text-success">Category</h3>
                            Lokal<br>
                            Import
                        </div>
                        <div class="col-xs-3">
                            <h3 class="text-success">Usage Location</h3>
                            EDUCATIONAL INSTRUMENT<br>
                            KEY PART PROCESS<br>
                            BODY PART PROCESS<br>
                            WELDING PROCESS<br>
                            SURFACE TREATMENT<br>
                            CASE<br>
                            PACKING<br>
                            FINAL ASSY<br>
                            SUBCONT<br>
                            KD<br>
                            INDIRECT<br>
                        </div>
                        <div class="col-xs-3">
                            <h3 class="text-success">Remark<br>(Nickname Material)</h3>
                            ABS ...<br>
                            Brass ...<br>
                            Nickel ...<br>
                            Etc
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="nonIndirectModal">
        <div class="modal-dialog" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Material <b>Direct</b>, <b>Subcont</b> & <b>KD</b></h4>
                    <span>
                        Format Upload:<br>
                        [<b><i>GROUP</i></b>]
                        [<b><i>GMC</i></b>]
                        [<b><i>DESCRIPTION</i></b>]
                        [<b><i>PGR</i></b>]
                        [<b><i>VENDOR CODE</i></b>]
                        [<b><i>VENDOR NAME</i></b>]
                        [<b><i>CATEGORY</i></b>]
                        [<b><i>BUYER</i></b>]
                        [<b><i>PCH CONTROL</i></b>]
                        [<b><i>L/T</i></b>]
                        [<b><i>MPQ</i></b>]
                        [<b><i>MOQ</i></b>]
                        [<b><i>USAGE LOC.</i></b>]
                        [<b><i>MATERIAL NICKNAME</i></b>]
                    </span>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="form-group">
                        <textarea id="upload_non_indirect" style="height: 100px; width: 100%;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success pull-right" onclick="uploadData('non_indirect');">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="indirectModal">
        <div class="modal-dialog" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Material Indirect</h4>
                    <span>
                        Format Upload:<br>
                        [<b><i>GROUP</i></b>]
                        [<b><i>GMC</i></b>]
                        [<b><i>DESCRIPTION</i></b>]
                        [<b><i>PGR</i></b>]
                        [<b><i>VENDOR CODE</i></b>]
                        [<b><i>VENDOR NAME</i></b>]
                        [<b><i>CATEGORY</i></b>]
                        [<b><i>BUYER</i></b>]
                        [<b><i>PCH CONTROL</i></b>]
                        [<b><i>L/T</i></b>]
                        [<b><i>DTS</i></b>]
                        [<b><i>MPQ</i></b>]
                        [<b><i>MOQ</i></b>]
                        [<b><i>USAGE LOC.</i></b>]
                        [<b><i>MATERIAL NICKNAME</i></b>]
                    </span>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="form-group">
                        <textarea id="upload_indirect" style="height: 100px; width: 100%;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success pull-right" onclick="uploadData('indirect');">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span style="color: red;"><b><i>Delete</i></b></span> Material</h4>
                    <span>
                        Format Upload:<br>
                        [<b><i>GMC</i></b>]
                    </span>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="form-group">
                        <textarea id="delete_data" style="height: 100px; width: 100%;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger pull-right" onclick="deleteData();">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadResult" data-keyboard="false" data-backdrop="static" style="overflow-y: auto;">
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

@stop

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

            fetchTable();

        });

        function deleteData() {
            $('#loading').show();
            var delete_data = $('#delete_data').val();

            if (delete_data == "") {
                alert('Data upload tidak boleh kosong');
                return false;
            }

            var data = {
                delete_data: delete_data
            }

            $.post('{{ url('delete/material/material_monitoring') }}', data, function(result, status, xhr) {
                if (result.status) {

                    fetchTable();

                    $('#delete_data').val('');
                    $('#deleteModal').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success', 'Count Data : ' + result.count + ' Rows(s)<br>' + result.message);

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }
            });

        }

        function uploadData(id) {
            var upload = $('#upload_' + id).val();

            var data = {
                id: id,
                upload: upload,
            }

            if (upload == "") {
                alert('Data upload tidak boleh kosong');
                return false;
            }

            $('#loading').show();
            $.post('{{ url('upload/material/material_monitoring') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#upload_' + id).val('');
                    $('#nonIndirectModal').modal('hide');
                    $('#indirectModal').modal('hide');

                    $('#suceess-count').text(result.ok_count.length);
                    $('#error-count').text(result.error_count.length);

                    $('#bodyError').html("");
                    var tableData = "";
                    var css = "padding: 0px 5px 0px 5px;";
                    for (var i = 0; i < result.error_count.length; i++) {
                        var error = result.error_count[i].split('_');
                        tableData += '<tr>';
                        tableData += '<td style="' + css + ' width:20%; text-align:left;">Row ' + error[0] +
                            '</td>';
                        tableData += '<td style="' + css + ' width:80%; text-align:left;">: ' + error[1] + '</td>';
                        tableData += '</tr>';
                    }

                    if (result.error_count.length > 0) {
                        $('#bodyError').append(tableData);
                        $('#tableError').show();
                    }

                    $('#uploadResult').modal('show');
                    $('#loading').hide();


                    openSuccessGritter('Success!', result.message);
                } else {
                    $('#loading').hide();
                    alert(result.message);
                }
            });
        }


        function fetchTable() {

            var month = $('#month').val();

            var data = {
                month: month
            }

            $('#loading').show();
            $.get('{{ url('fetch/raw_material/list') }}', data, function(result, status, xhr) {
                if (result.status) {

                    // NON INDIRECT
                    $('#tableNonIndirect').DataTable().clear();
                    $('#tableNonIndirect').DataTable().destroy();
                    $('#headNonIndirect').html("");
                    var headNonIndirect = '<tr>';
                    headNonIndirect += '<th style="vertical-align: middle; text-align: center;">Group</th>';
                    headNonIndirect += '<th style="vertical-align: middle; text-align: center;">Material</th>';
                    headNonIndirect += '<th style="vertical-align: middle; text-align: center;">PGr</th>';
                    headNonIndirect += '<th style="vertical-align: middle; text-align: center;">Category</th>';
                    headNonIndirect += '<th style="vertical-align: middle; text-align: center;">Vendor</th>';
                    headNonIndirect += '<th style="vertical-align: middle; text-align: center;">Buyer</th>';
                    headNonIndirect += '<th style="vertical-align: middle; text-align: center;">PIC Control</th>';
                    headNonIndirect += '<th style="vertical-align: middle; text-align: center;">L/T</th>';
                    headNonIndirect += '<th style="vertical-align: middle; text-align: center;">Usage Loc.</th>';
                    headNonIndirect += '<th style="vertical-align: middle; text-align: center;">Nickname</th>';
                    headNonIndirect += '</tr>';
                    $('#headNonIndirect').append(headNonIndirect);


                    $('#bodyNonIndirect').html("");
                    var bodyNonIndirect = '';
                    for (var i = 0; i < result.material.length; i++) {
                        if (result.material[i].controlling_group != 'INDIRECT') {
                            bodyNonIndirect += '<tr>';

                            bodyNonIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyNonIndirect += result.material[i].controlling_group
                            bodyNonIndirect += '</td>';

                            bodyNonIndirect += '<td style="vertical-align: middle; text-align: left;">'
                            bodyNonIndirect += result.material[i].material_number;
                            bodyNonIndirect += '<br>';
                            bodyNonIndirect += result.material[i].material_description;
                            bodyNonIndirect += '</td>';

                            bodyNonIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyNonIndirect += result.material[i].purchasing_group;
                            bodyNonIndirect += '</td>';

                            bodyNonIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyNonIndirect += result.material[i].category;
                            bodyNonIndirect += '</td>';

                            bodyNonIndirect += '<td style="vertical-align: middle; text-align: left;">';
                            bodyNonIndirect += result.material[i].vendor_code;
                            bodyNonIndirect += '<br>';
                            bodyNonIndirect += result.material[i].vendor_name;
                            bodyNonIndirect += '</td>';

                            bodyNonIndirect += '<td style="vertical-align: middle; text-align: left;">';
                            bodyNonIndirect += result.material[i].pic;
                            bodyNonIndirect += '<br>';
                            bodyNonIndirect += callName(result.material[i].buyer_name);
                            bodyNonIndirect += '</td>';

                            bodyNonIndirect += '<td style="vertical-align: middle; text-align: left;">';
                            bodyNonIndirect += result.material[i].control;
                            bodyNonIndirect += '<br>';
                            bodyNonIndirect += callName(result.material[i].control_name);
                            bodyNonIndirect += '</td>';

                            bodyNonIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyNonIndirect += (result.material[i].lead_time || '-');
                            bodyNonIndirect += '</td>';

                            bodyNonIndirect += '<td style="vertical-align: middle; text-align: left;">';
                            bodyNonIndirect += result.material[i].material_category;
                            bodyNonIndirect += '</td>';

                            bodyNonIndirect += '<td style="vertical-align: middle; text-align: left;">';
                            bodyNonIndirect += result.material[i].remark;
                            bodyNonIndirect += '</td>';

                            bodyNonIndirect += '</tr>';
                        }
                    }
                    $('#bodyNonIndirect').append(bodyNonIndirect);

                    $('#footNonIndirect').html("");
                    var footNonIndirect = '';
                    footNonIndirect += '<tr>';
                    footNonIndirect += '<th></th>';
                    footNonIndirect += '<th></th>';
                    footNonIndirect += '<th></th>';
                    footNonIndirect += '<th></th>';
                    footNonIndirect += '<th></th>';
                    footNonIndirect += '<th></th>';
                    footNonIndirect += '<th></th>';
                    footNonIndirect += '<th></th>';
                    footNonIndirect += '<th></th>';
                    footNonIndirect += '<th></th>';
                    footNonIndirect += '</tr>';
                    $('#footNonIndirect').append(footNonIndirect);

                    $('#tableNonIndirect tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="filterNonIndirect" style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="3"/>');
                    });

                    var tableNonIndirect = $('#tableNonIndirect').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'excel',
                                className: 'btn btn-info',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'print',
                                className: 'btn btn-warning',
                                text: '<i class="fa fa-print"></i> Print',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        initComplete: function() {
                            this.api()
                                .columns([0, 2, 3, 8, 9])
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

                    tableNonIndirect.columns().every(function() {
                        var that = this;
                        $('.filterNonIndirect', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#tableNonIndirect tfoot tr').prependTo('#tableNonIndirect thead');


                    // INDIRECT
                    $('#tableIndirect').DataTable().clear();
                    $('#tableIndirect').DataTable().destroy();
                    $('#headIndirect').html("");
                    var headIndirect = '<tr>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">Group</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">Material</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">PGr</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">Category</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">Vendor</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">Buyer</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">PIC Control</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">L/T</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">DTS</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">MPQ</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">MOQ</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">Usage Loc.</th>';
                    headIndirect += '<th style="vertical-align: middle; text-align: center;">Nickname</th>';
                    headIndirect += '</tr>';
                    $('#headIndirect').append(headIndirect);


                    $('#bodyIndirect').html("");
                    var bodyIndirect = '';
                    for (var i = 0; i < result.material.length; i++) {
                        if (result.material[i].controlling_group == 'INDIRECT') {
                            bodyIndirect += '<tr>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyIndirect += result.material[i].controlling_group
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: left;">'
                            bodyIndirect += result.material[i].material_number;
                            bodyIndirect += '<br>';
                            bodyIndirect += result.material[i].material_description;
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyIndirect += result.material[i].purchasing_group;
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyIndirect += result.material[i].category;
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: left;">';
                            bodyIndirect += result.material[i].vendor_code;
                            bodyIndirect += '<br>';
                            bodyIndirect += result.material[i].vendor_name;
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: left;">';
                            bodyIndirect += result.material[i].pic;
                            bodyIndirect += '<br>';
                            bodyIndirect += callName(result.material[i].buyer_name);
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: left;">';
                            bodyIndirect += result.material[i].control;
                            bodyIndirect += '<br>';
                            bodyIndirect += callName(result.material[i].control_name);
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyIndirect += (result.material[i].lead_time || '-');
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyIndirect += (result.material[i].dts || '-');
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyIndirect += (result.material[i].multiple_order || '-');
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: center;">';
                            bodyIndirect += (result.material[i].minimum_order || '-');
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: left;">';
                            bodyIndirect += result.material[i].material_category;
                            bodyIndirect += '</td>';

                            bodyIndirect += '<td style="vertical-align: middle; text-align: left;">';
                            bodyIndirect += result.material[i].remark;
                            bodyIndirect += '</td>';

                            bodyIndirect += '</tr>';
                        }
                    }
                    $('#bodyIndirect').append(bodyIndirect);

                    $('#footIndirect').html("");
                    var footIndirect = '';
                    footIndirect += '<tr>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '<th></th>';
                    footIndirect += '</tr>';
                    $('#footIndirect').append(footIndirect);

                    $('#tableIndirect tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="filterIndirect" style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="3"/>');
                    });

                    var tableIndirect = $('#tableIndirect').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'excel',
                                className: 'btn btn-info',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'print',
                                className: 'btn btn-warning',
                                text: '<i class="fa fa-print"></i> Print',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        initComplete: function() {
                            this.api()
                                .columns([0, 2, 3, 11, 12])
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

                    tableIndirect.columns().every(function() {
                        var that = this;
                        $('.filterIndirect', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#tableIndirect tfoot tr').prependTo('#tableIndirect thead');


                    $('#loading').hide();

                }

            });

        }

        function callName(name) {
            var new_name = '';
            var blok_m = [
                'M.',
                'Moch.',
                'Mochammad',
                'Moh.',
                'Mohamad',
                'Mokhamad',
                'Much.',
                'Muchammad',
                'Muhamad',
                'Muhammaad',
                'Muhammad',
                'Mukammad',
                'Mukhamad',
                'Mukhammad'
            ];


            if (name != null) {

                if (name.includes(' ')) {
                    name = name.split(' ');

                    if (blok_m.includes(name[0])) {
                        new_name = 'M.';
                        for (i = 1; i < name.length; i++) {
                            if (i == 1) {
                                new_name += ' ';
                                new_name += name[i];
                            } else {
                                new_name += ' ';
                                new_name += name[i].substr(0, 1) + '.';
                            }
                        }
                    } else {
                        for (i = 0; i < name.length; i++) {
                            if (i == 0) {
                                new_name += ' ';
                                new_name += name[i];
                            } else {
                                new_name += ' ';
                                new_name += name[i].substr(0, 1) + '.';
                            }
                        }
                    }

                } else {
                    new_name = name;
                }
            } else {
                new_name = '-';
            }

            return new_name;
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
