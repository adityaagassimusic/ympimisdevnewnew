@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/bootstrap-datetimepicker.min.css') }}">

    <style type="text/css">
        thead input {
            width: 100%;
            box-sizing: border-box;
        }

        thead>tr>th {
            text-align: center;
            overflow: hidden;
        }

        tbody>tr>td {
            text-align: center;
        }

        tfoot>tr>th {
            text-align: center;
        }

        th:hover {
            overflow: visible;
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
            border: 1px solid black;
            vertical-align: middle;
            padding-top: 0;
            padding-bottom: 0;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            padding-top: 0;
            padding-bottom: 0;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .table-striped>tbody>tr:nth-child(2n+1)>td,
        .table-striped>tbody>tr:nth-child(2n+1)>th {
            background-color: #ffd8b7;
        }

        .table-hover tbody tr:hover td,
        .table-hover tbody tr:hover th {
            background-color: #FFD700;
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
            {{ $title }}
            <span class="text-purple"> {{ $title_jp }}</span>
        </h1>
    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <div class="row">
            <div class="col-xs-8">
                <h2>LIST MIRAI VS YMES</h2>
                <table id="tableMiraiYmes" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;">
                    <thead style="background-color: rgb(126,86,134); color: #212121;">
                        <tr>
                            <th width="10%">Location</th>
                            <th width="10%">Material</th>
                            <th>Material Description</th>
                            <th width="10%">Category</th>
                            <th width="10%">MIRAI PI</th>
                        </tr>
                    </thead>
                    <tbody id="tableMiraiYmesBody">
                    </tbody>
                    <tfoot>
                        <tr>
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

    </section>
@endsection
@section('scripts')

    <script src="{{ url('js/moment.min.js') }}"></script>
    <script src="{{ url('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
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

            fillTable();

        });

        function fillTable() {
            $('#tableMiraiYmes').DataTable().destroy();
            $('#tableMiraiYmes tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" />');
            });
            var tableMiraiYmes = $('#tableMiraiYmes').DataTable({
                'dom': 'Brtip',
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
                        }
                    ]
                },
                "columnDefs": [{
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                }],
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': false,
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/stocktaking/unmatch_ymes_list') }}",
                },
                'columnDefs': [{
                        "targets": 2,
                        "className": "text-left",
                    },
                    {
                        "targets": 4,
                        "className": "text-right",
                    }
                ],
                "columns": [{
                    "data": "location"
                }, {
                    "data": "material_number"
                }, {
                    "data": "material_description"
                }, {
                    "data": "category"
                }, {
                    "data": "final_count"
                }]
            });
            tableMiraiYmes.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            $('#tableMiraiYmes tfoot tr').appendTo('#tableMiraiYmes thead');

        }
    </script>
@endsection
