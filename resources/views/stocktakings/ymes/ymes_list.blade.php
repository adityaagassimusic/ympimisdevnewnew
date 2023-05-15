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
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 0;
            padding-bottom: 0;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        #alert {
            background-color: #66D9F5;
            color: #060606;
            display: none;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        <ol class="breadcrumb">
            <li>
                <a data-toggle="modal" data-target="#upload_material" class="btn btn-success btn-sm" style="color:white">
                    &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-file-excel-o"></i>&nbsp;Upload List Baru&nbsp;&nbsp;&nbsp;&nbsp;
                </a>
                <a data-toggle="modal" data-target="#delete_modal" class="btn btn-danger btn-sm" style="color:white">
                    &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-trash"></i>&nbsp;Delete List No&nbsp;&nbsp;&nbsp;&nbsp;
                </a>
            </li>
        </ol>
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
        </h1>
    </section>
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="col-xs-12">
            <div class="alert alert-dismissible" id="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h4><i class="icon fa fa-info"></i> Information !</h4>
                Data telah di-update, refresh untuk melihat update terbaru.&nbsp;&nbsp;&nbsp;
                <button class="btn btn-success btn-xs" onclick="clearConfirmation()"><i
                        class="fa  fa-refresh"></i>&nbsp;&nbsp;Refresh</button>&nbsp;&nbsp;&nbsp;
                <button data-dismiss="alert"
                    class="btn btn-xs">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nanti&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="row">
                <div class="nav-tabs-custom" style="margin-top: 1%;">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                        <li class="vendor-tab active">
                            <a href="#tab_1" data-toggle="tab" id="tab_header_1">
                                YMES Stocktaking List
                            </a>
                        </li>
                        <li class="vendor-tab">
                            <a href="#tab_2" data-toggle="tab" id="tab_header_2">
                                YMES Stocktaking Resume
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                            <table id="detailTable" class="table table-bordered table-striped table-hover">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="text-align: center; width: 1%;">List No</th>
                                        <th style="text-align: center; width: 2%;">Slip No</th>
                                        <th style="text-align: center; width: 1%;">Sloc</th>
                                        <th style="text-align: center; width: 1%;">Category</th>
                                        <th style="text-align: center; width: 1%;">Material</th>
                                        <th style="text-align: center; width: 6%;">Description</th>
                                        <th style="text-align: center; width: 1%;">Uom</th>
                                        <th style="text-align: center; width: 1%;">Valcl</th>
                                        <th style="text-align: center; width: 1%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="detailTableBody">
                                    @foreach ($ymes_lists as $row)
                                        <tr>
                                            <td style="text-align: center;">{{ $row->list_no }}</td>
                                            <td style="text-align: right;">{{ $row->slip_no }}</td>
                                            <td style="text-align: center;">{{ $row->location }}</td>
                                            <td style="text-align: center;">{{ $row->category }}</td>
                                            <td style="text-align: center;">{{ $row->material_number }}</td>
                                            <td style="text-align: left;">{{ $row->material_description }}</td>
                                            <td style="text-align: center;">{{ $row->uom }}</td>
                                            <td style="text-align: center;">{{ $row->valcl }}</td>
                                            <td style="text-align: center;">{{ $row->plant_spitem_status }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
                            <div class="col-xs-6 col-xs-offset-3">
                                <table id="resumeTable" class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: rgba(126,86,134,.7);">
                                        <tr>
                                            <th style="text-align: center; width: 1%;">List No</th>
                                            <th style="text-align: center; width: 1%;">Sloc</th>
                                            <th style="text-align: center; width: 1%;">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resumeTableBody">
                                        @foreach ($resumes as $row)
                                            <tr>
                                                <td style="text-align: center;">{{ $row->list_no }}</td>
                                                <td style="text-align: center;">{{ $row->location }}</td>
                                                <td style="text-align: right;">{{ $row->count_slip }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="upload_material">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="uploadMaterial" method="post" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Upload Stocktaking List Baru</h4>
                        Format : <b>Sesuai YMES Inquiry</b>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-6 col-xs-offset-3">
                                <input type="file" name="file_list" id="file_list"
                                    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                    style="text-align: right;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="modalImportButton" type="submit" class="btn btn-success pull-right">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"><span style="color: red;"><b><i>Delete</i></b></span> List No</h4>
                    <span>
                        Format Upload:<br>
                        [<b><i>List No</i></b>]
                    </span>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="form-group">
                        <textarea id="delete_list_no" style="height: 100px; width: 100%;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger pull-right" onclick="deleteData();">Delete</button>
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
    <script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('.select2').select2({
                allowClear: true
            });

            generateTable();

        });

        function generateTable() {
            $('#detailTable').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [100, 250, 500, -1],
                    ['100 rows', '250 rows', '500 rows', 'Show all']
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
                "processing": false,
                'columnDefs': [{
                        "targets": [2],
                        "className": "text-left",
                    },
                    {
                        "targets": [5, 7, 8],
                        "className": "text-right",
                    }
                ]
            });

            $('#resumeTable').DataTable({
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
                "processing": false
            });
        }

        function deleteData() {
            $('#loading').show();
            var delete_list_no = $('#delete_list_no').val();

            if (delete_list_no == "") {
                alert('Data upload tidak boleh kosong');
                return false;
            }

            var data = {
                delete_list_no: delete_list_no
            }

            $.post('{{ url('delete/stocktaking/ymes_list') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#delete_list_no').val('');
                    $('#delete_modal').modal('hide');
                    $("#alert").show();
                    $('#loading').hide();
                    openSuccessGritter('Success', 'Jumlah : ' + result.count + ' baris<br>' + result.message);

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }
            });

        }

        $("form#uploadMaterial").submit(function(e) {
            if ($('#file_list').val() == '') {
                openErrorGritter('Error!', 'Pilih file!');
                return false;
            }

            $("#loading").show();

            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: '{{ url('upload/stocktaking/ymes_list') }}',
                type: 'POST',
                data: formData,
                success: function(result, status, xhr) {
                    if (result.status) {

                        $("#file_list").val('');
                        $('#upload_material').modal('hide');
                        $("#alert").show();
                        $("#loading").hide();

                        openSuccessGritter('Success', 'Jumlah : ' + result.count + ' baris<br>' + result
                            .message);

                    } else {
                        $("#loading").hide();
                        openErrorGritter('Error!', 'Baris ke ' + result.count + ' <br>' + result
                            .message);
                    }
                },
                error: function(result, status, xhr) {
                    $("#loading").hide();
                    openErrorGritter('Error!', result.message);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        function clearConfirmation() {
            location.reload(true);
        }

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
