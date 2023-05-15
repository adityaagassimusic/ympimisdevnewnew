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
            padding: 3px;
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

        td {
            overflow: hidden;
            text-overflow: ellipsis;
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
        </ol>
    </section>
@endsection
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
                {{ session('success') }}
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
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Uploading, please wait...<i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box no-border">
                            <div class="box-header">
                                <button class="btn btn-success" data-toggle="modal" data-target="#importModal"
                                    style="width: 16%">Import</button>
                            </div>
                            <div class="box-body" style="padding-top: 0;">
                                <table id="bomTable" class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: rgba(126,86,134,.7);">
                                        <tr>
                                            <th style="width:5%;">Material Parent</th>
                                            <th style="width:5%;">Material Child</th>
                                            <th style="width:5%;">Sloc Child</th>
                                            <th style="width:5%;">SPT Child</th>
                                            <th style="width:5%;">Valcl Child</th>
                                            <th style="width:5%;">UOM Child</th>
                                            <th style="width:5%;">Usage</th>
                                            <th style="width:5%;">Divider</th>

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
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="importModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Import Bom Output</h4>
                    Format :
                    [<b><i>Parent</i></b>]
                    [<b><i>Child</i></b>]
                    [<b><i>Sloc Child</i></b>]
                    [<b><i>SPT Child</i></b>]
                    [<b><i>Valcl Child</i></b>]
                    [<b><i>UOM Child</i></b>]
                    [<b><i>Usage</i></b>]
                    [<b><i>Divider</i></b>]
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1" style="padding: 0px;">
                            <div class="col-xs-12" style="margin-top: 2%;">
                                <label>Bom Output Data :<span class="text-red">*</span></label>
                            </div>
                            <div class="col-xs-12">
                                <textarea id="bom" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="uploadBom()" class="btn btn-success">Upload</button>
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
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

        jQuery(document).ready(function() {
            $('.select2').select2();
            fetchTable();
        });

        function clearSearch() {
            location.reload(true);
        }

        function loadingPage() {
            $("#loading").show();
        }

        function fetchTable() {
            $('#bomTable').DataTable().destroy();

            var storage_location = $('#storage_location').val();
            var base_unit = $('#base_unit').val();

            var data = {
                storage_location: storage_location,
                base_unit: base_unit
            }

            $('#bomTable tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" size="20"/>');
            });

            var table = $('#bomTable').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [50, 250, 500, 1000, -1],
                    ['50 rows', '250 rows', '500 rows', '1000 rows', 'Show all']
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
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/bom_output') }}",
                    "data": data
                },
                "columns": [{
                    "data": "material_parent"
                }, {
                    "data": "material_child"
                }, {
                    "data": "storage_location"
                }, {
                    "data": "spt"
                }, {
                    "data": "valcl"
                }, {
                    "data": "uom"
                }, {
                    "data": "usage"
                }, {
                    "data": "divider"
                }]
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

            $('#bomTable tfoot tr').appendTo('#bomTable thead');
        }

        function uploadBom() {

            var upload = $('#bom').val();

            if (upload == '') {
                openErrorGritter('Error', 'All data must be complete');
                return false;
            }

            var data = {
                upload: upload,
            }

            $('#loading').show();
            $.post('{{ url('import/material/bom') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#bom').val('');

                    $('#bomTable').DataTable().ajax.reload();

                    $('#importModal').modal('hide');

                    $('#loading').hide();
                    openSuccessGritter('Success', 'Bom Output Uploaded Successfully');

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
