@extends('layouts.master')
@section('stylesheets')
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

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 0;
            padding-bottom: 0;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
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
            List of {{ $page }}s
        </h1>
        {{-- <ol class="breadcrumb">
            <li>
                <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm" style="color:white">Import
                    {{ $page }}s</a>
                &nbsp;
                <a href="{{ url('create/material_volume') }}" class="btn btn-primary btn-sm" style="color:white">Create
                    {{ $page }}</a>
            </li>
        </ol> --}}
    </section>
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
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped table-hover" style="widows: 100%;">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="vertical-align: middle;" rowspan="2">Material</th>
                                    <th style="vertical-align: middle;" rowspan="2">Description</th>
                                    <th style="vertical-align: middle;" rowspan="2">Category</th>
                                    <th style="vertical-align: middle;" rowspan="2">HPL</th>
                                    <th style="background-color: #b4c6e7;" colspan="5">Pallet</th>
                                    <th style="background-color: #ffd34e;" colspan="5">Carton</th>
                                    <th style="vertical-align: middle;" rowspan="2">Status</th>
                                    <th style="vertical-align: middle;" rowspan="2">Action</th>
                                </tr>
                                <tr>
                                    <th style="background-color: #b4c6e7;">Lot</th>
                                    <th style="background-color: #b4c6e7;">L</th>
                                    <th style="background-color: #b4c6e7;">W</th>
                                    <th style="background-color: #b4c6e7;">H</th>
                                    <th style="background-color: #b4c6e7;">Volume</th>
                                    <th style="background-color: #ffd34e;">Lot</th>
                                    <th style="background-color: #ffd34e;">L</th>
                                    <th style="background-color: #ffd34e;">W</th>
                                    <th style="background-color: #ffd34e;">H</th>
                                    <th style="background-color: #ffd34e;">Volume</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($material_volumes as $material_volume)
                                    @if ($material_volume->cat == 'KD' || $material_volume->cat == 'FG')
                                        <tr>
                                            <td style="width: 1%">{{ $material_volume->material_number }}</td>
                                            <td style="width: 20%">
                                                @if (isset($material_volume->material->material_description))
                                                    {{ $material_volume->material->material_description }}
                                                @else
                                                    Not registered
                                                @endif
                                            </td>
                                            <td style="width: 1%">{{ $material_volume->cat }}</td>
                                            <td style="width: 3%">{{ $material_volume->hpl }}</td>

                                            <td style="width: 5%">{{ $material_volume->lot_pallet }}</td>
                                            <td style="width: 3%">{{ $material_volume->length_pallet }}</td>
                                            <td style="width: 3%">{{ $material_volume->width_pallet }}</td>
                                            <td style="width: 3%">{{ $material_volume->height_pallet }}</td>
                                            <td style="width: 3%">{{ $material_volume->cubic_meter_pallet }}</td>

                                            <td style="width: 5%">{{ $material_volume->lot_carton }}</td>
                                            <td style="width: 3%">{{ $material_volume->length }}</td>
                                            <td style="width: 3%">{{ $material_volume->width }}</td>
                                            <td style="width: 3%">{{ $material_volume->height }}</td>
                                            <td style="width: 3%">{{ $material_volume->cubic_meter }}</td>

                                            @if ($material_volume->lot_pallet == '0' ||
                                                $material_volume->length_pallet == '0' ||
                                                $material_volume->width_pallet == '0' ||
                                                $material_volume->height_pallet == '0' ||
                                                $material_volume->cubic_meter_pallet == '0' ||
                                                $material_volume->lot_carton == '0' ||
                                                $material_volume->length == '0' ||
                                                $material_volume->width == '0' ||
                                                $material_volume->height == '0' ||
                                                $material_volume->cubic_meter == '0')
                                                <td style="width: 1%">NG</td>
                                            @else
                                                <td style="width: 1%">OK</td>
                                            @endif

                                            <td style="width: 10%">
                                                <center>
                                                    {{-- <a class="btn btn-info btn-xs"
                                                        href="{{ url('show/material_volume', $material_volume['id']) }}">
                                                        View
                                                    </a> --}}
                                                    <a href="{{ url('edit/material_volume', $material_volume['id']) }}"
                                                        class="btn btn-warning btn-xs">
                                                        Edit
                                                    </a>
                                                    <a href="javascript:void(0)" class="btn btn-danger btn-xs"
                                                        data-toggle="modal" data-target="#myModal"
                                                        onclick="deleteConfirmation('{{ url('destroy/material_volume') }}', '{{ $material_volume->material_number }}', '{{ $material_volume['id'] }}');">
                                                        Delete
                                                    </a>
                                                </center>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="background-color: #b4c6e7;"></th>
                                    <th style="background-color: #b4c6e7;"></th>
                                    <th style="background-color: #b4c6e7;"></th>
                                    <th style="background-color: #b4c6e7;"></th>
                                    <th style="background-color: #b4c6e7;"></th>
                                    <th style="background-color: #ffd34e;"></th>
                                    <th style="background-color: #ffd34e;"></th>
                                    <th style="background-color: #ffd34e;"></th>
                                    <th style="background-color: #ffd34e;"></th>
                                    <th style="background-color: #ffd34e;"></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
                </div>
                <div class="modal-body" id="modalDeleteBody">
                    Are you sure delete?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="importForm" method="post" action="{{ url('import/material_volume') }}"
                    enctype="multipart/form-data">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
                        Format: [Material Number][Category][Lot Completion][Lot Transfer][Lot Max FLO][Lot per Row][Lot per
                        Pallet][Length][Width][Height]<br>
                        Sample: <a
                            href="{{ url('download/manual/import_material_volume.txt') }}">import_material_volume.txt</a>
                        Code: #Truncate
                    </div>
                    <div class="modal-body">
                        <center><input type="file" name="material_volume" id="InputFile" accept="text/plain">
                        </center>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('#example1 tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" size="4"/>');
            });
            var table = $('#example1').DataTable({
                "order": [],
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [25, 50, 100, -1],
                    ['25 rows', '50 rows', '100 rows', 'Show all']
                ],
                "columnDefs": [{
                    "targets": [1],
                    "className": "text-left"
                }, {
                    "targets": [4, 5, 6, 7, 8, 9, 10, 11, 12, 13],
                    "createdCell": function(td, cellData, rowData, row, col) {
                        if (cellData == '0') {
                            $(td).css('background-color', 'rgb(255, 204, 255)');
                        }
                    }
                }, {
                    "targets": [14],
                    "createdCell": function(td, cellData, rowData, row, col) {
                        if (cellData == 'NG') {
                            $(td).css('background-color', 'rgb(255, 204, 255)');
                        }
                    }
                }],
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
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                initComplete: function() {
                    this.api()
                        .columns([2, 3, 14])
                        .every(function(dd) {
                            var column = this;
                            var theadname = $("#example1 th").eq([dd]).text();
                            var select = $(
                                    '<select><option value="" style="font-size:11px;">All</option></select>'
                                )
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                    column.search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });
                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function(d, j) {
                                    var vals = d;
                                    if ($("#example1 th").eq([dd]).text() == 'Category') {
                                        vals = d.split(' ')[0];
                                    }
                                    select.append('<option style="font-size:11px;" value="' +
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

            $('#example1 tfoot tr').appendTo('#example1 thead');

        });

        function deleteConfirmation(url, name, id) {
            jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
            jQuery('#modalDeleteButton').attr("href", url + '/' + id);
        }
    </script>

@stop
