@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        .table>tbody>tr:hover {
            background-color: #7dfa8c !important;
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
            padding: 2px 5px 2px 5px;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        #loading {
            display: none;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
            <a href="{{ url('/ymes_error') }}" class="btn btn-primary pull-right" style="margin-left: 5px; width: 10%;"><i
                    class="fa fa-download"></i> Download All Error</a>
            <a href="{{ url('/ymes_error_interface') }}" class="btn btn-warning pull-right"
                style="margin-left: 5px; width: 10%;"><i class="fa fa-upload"></i> Upload All Error</a>
        </h1>
    </section>
@endsection

@section('content')
    <section class="content" style="font-size: 0.9vw;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-body">
                        <table id="tableResult" class="table table-bordered table-striped table-hover" style="width: 100%;">
                            <thead style="color: black; background-color: grey;">
                                <tr>
                                    <th style="width: 0.1%; text-align: left;">Category</th>
                                    <th style="width: 0.1%; text-align: right;">Posting</th>
                                    <th style="width: 0.1%; text-align: right;">Entry</th>
                                    <th style="width: 0.1%; text-align: left;">Serial No.</th>
                                    <th style="width: 1%; text-align: left;">Material</th>
                                    <th style="width: 10%; text-align: left;">Description</th>
                                    <th style="width: 1%; text-align: left;">Issue</th>
                                    <th style="width: 1%; text-align: left;">Receive</th>
                                    <th style="width: 1%; text-align: right;">Quantity</th>
                                    <th style="width: 0.1%; text-align: left;">Created By</th>
                                    <th style="width: 1%; text-align: right;">Synced</th>
                                    <th style="width: 1%; text-align: left;">Sync</th>
                                    <th style="width: 1%; text-align: left;">Delete</th>
                                    <th style="width: 10%; text-align: left;">Error</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            fetchDataTable();
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

        function clearAll() {
            location.reload(true);
        }

        function sync(id, action) {
            if (confirm("Do you want to sync this transaction data?")) {
                $('#loading').show();

                var data = {
                    id: id,
                    action: action
                }

                $.post('{{ url('sync/ymes/transaction') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        $('#tableResult').DataTable().ajax.reload(null, false);
                        audio_ok.play();
                        openSuccessGritter('Success!', result.message);
                        $('#loading').hide();
                    } else {
                        audio_error.play();
                        openErrorGritter('Error!', result.message);
                        $('#loading').hide();
                    }
                });
            } else {
                return false;
            }
        }

        function del(id, action) {
            if (confirm("Do you want to delete this transaction data?")) {
                $('#loading').show();

                var data = {
                    id: id,
                    action: action
                }

                $.post('{{ url('delete/ymes/transaction') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        $('#tableResult').DataTable().ajax.reload(null, false);
                        audio_ok.play();
                        openSuccessGritter('Success!', result.message);
                        $('#loading').hide();
                    } else {
                        audio_error.play();
                        openErrorGritter('Error!', result.message);
                        $('#loading').hide();
                    }
                });
            } else {
                return false;
            }
        }

        function fetchDataTable() {
            var data = {

            }

            var table = $('#tableResult').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'buttons': {
                    buttons: [{
                            extend: 'pageLength',
                            className: 'btn btn-default'
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
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/ymes/error') }}",
                    "data": data
                },
                "columns": [{
                        "data": "category"
                    },
                    {
                        "data": "posting_date"
                    },
                    {
                        "data": "entry_date"
                    },
                    {
                        "data": "serial_number"
                    },
                    {
                        "data": "material_number"
                    },
                    {
                        "data": "material_description"
                    },
                    {
                        "data": "issue_location"
                    },
                    {
                        "data": "receive_location"
                    },
                    {
                        "data": "quantity"
                    },
                    {
                        "data": "created_by"
                    },
                    {
                        "data": "synced"
                    },
                    {
                        "data": "sync"
                    },
                    {
                        "data": "del"
                    },
                    {
                        "data": "error"
                    }
                ],
                "columnDefs": [{
                        "className": 'text-left',
                        "targets": [0, 3, 4, 5, 6, 7, 9, 13]
                    },
                    {
                        "className": 'text-center',
                        "targets": [11, 12]
                    },
                    {
                        "className": 'text-right',
                        "targets": [1, 2, 8, 10]
                    },
                    {
                        "width": "20%",
                        "targets": [5, 13]
                    },
                ]
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
@endsection
