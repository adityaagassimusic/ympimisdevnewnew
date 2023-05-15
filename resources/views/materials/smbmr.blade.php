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
        {{-- <ol class="breadcrumb">
            <li>
                <a data-toggle="modal" onclick="generateNew()" class="btn btn-success btn-md" style="color:white"><i
                        class="fa fa-refresh"></i>Generate New SMBMR</a>
            </li>
        </ol> --}}
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

        <div class="row" style="margin-top: 1%;">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="col-xs-12" style="overflow-x: auto;">
                            <table id="table" class="table table-bordered table-hover" style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                    <tr>
                                        <th style="width: 10%">Last Update</th>
                                        <th style="width: 5%">Material</th>
                                        <th style="width: 30%">Material Description</th>
                                        <th style="width: 5%">Raw Material</th>
                                        <th style="width: 30%">Material Description</th>
                                        <th style="width: 5%">PGr</th>
                                        <th style="width: 5%">Uom</th>
                                        <th style="width: 5%">Usage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($smbmr as $row)
                                        <tr>
                                            <td>{{ $row->updated_at }}</td>
                                            <td>{{ $row->material_parent }}</td>
                                            <td>{{ $row->material_parent_description }}</td>
                                            <td>{{ $row->raw_material }}</td>
                                            <td>{{ $row->raw_material_description }}</td>
                                            <td>{{ $row->pgr }}</td>
                                            <td>{{ $row->uom }}</td>
                                            <td>{{ $row->usage }}</td>
                                        </tr>
                                    @endforeach
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
    </section>

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

            fetchTable();

        });



        function generateNew() {
            if (confirm('Apakah anda yakin untuk men-generate ulang SMBMR ?')) {

                $('#loading').show();
                $.get('{{ url('fetch/material/breakdown_smbmr') }}', function(result, status, xhr) {
                    if (result.status) {
                        $('#table').DataTable().ajax.reload();
                        $('#loading').hide();
                        openSuccessGritter('Success', 'Generate SMBMR Success');

                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error', 'Error');
                    }

                });
            }
        }

        function fetchTable() {

            $('#table').DataTable().destroy();
            $('#table tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" />');
            });

            var table = $('#table').DataTable({
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
                "processing": true,
                "columnDefs": [{
                        "targets": [2, 4],
                        "className": "text-left"
                    },
                    {
                        "targets": [7],
                        "className": "text-right"
                    }
                ],

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
            $('#table tfoot tr').prependTo('#table thead');


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
