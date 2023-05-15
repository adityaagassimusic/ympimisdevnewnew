@extends('layouts.notification')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        .status {
            font-size: 3vw;
            font-weight: bold;
            text-transform: uppercase;
        }

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
            background-color: aliceblue;
            vertical-align: middle;
            padding: 2px 5px 2px 5px;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            Multi-Level BOM
            <span class="text-purple">が却下した</span>
        </h1>
    </section>
@endsection

@section('content')
    <section class="content">
        <div class="row">
            @if ($code == 0)
                <div class="error" style="text-align: center;">
                    <h2>
                        <p class="status">
                            <i style="color : #ee3a43;" class="fa fa-info-circle"></i>
                            &nbsp;&nbsp;Multi-Level BOM Not Found
                        </p>
                    </h2>
                </div>
            @else
                <div class="col-xs-8 col-xs-offset-2">
                    <table class="table table-bordered table-hover" id="tableDetail" width="100%">
                        <thead style="background-color: rgba(126,86,134,.5);">
                            <tr>
                                <th style="text-align: center;">Level</th>
                                <th style="text-align: center;">GMC</th>
                                <th style="text-align: center;">Description</th>
                                <th style="text-align: center;">ValCl</th>
                                <th style="text-align: center;">Sloc</th>
                                <th style="text-align: center;">Total BOM Qty</th>
                                <th style="text-align: center;">UoM</th>
                                <th style="text-align: center;">Phantom Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 0; $i < count($bom); $i++)
                                <tr>
                                    @if ($bom[$i]->level == 0)
                                        <td style="text-align: center;">
                                            {{ $bom[$i]->level }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $bom[$i]->parent }}
                                        </td>
                                        <td style="text-align: left;">
                                            {{ $mpdl[$bom[$i]->parent]->material_description }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $mpdl[$bom[$i]->parent]->valcl }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $mpdl[$bom[$i]->parent]->storage_location }}
                                        </td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: center;">
                                            {{ $mpdl[$bom[$i]->parent]->bun }}
                                        </td>
                                        @if ($mpdl[$bom[$i]->parent]->spt == 50)
                                            <td style="text-align: center;">Phantom</td>
                                        @else
                                            <td style="text-align: center;">Non Phantom</td>
                                        @endif
                                    @else
                                        <td style="text-align: center;">
                                            {{ $bom[$i]->level }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $bom[$i]->child }}
                                        </td>
                                        <td style="text-align: left;">
                                            {{ $mpdl[$bom[$i]->child]->material_description }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $mpdl[$bom[$i]->child]->valcl }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $mpdl[$bom[$i]->child]->storage_location }}
                                        </td>
                                        <td style="text-align: right; padding: 0px 15px 0px 15px !important;">
                                            {{ round($bom[$i]->usage, 6) }}
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $mpdl[$bom[$i]->child]->bun }}
                                        </td>
                                        @if ($mpdl[$bom[$i]->child]->spt == 50)
                                            <td style="text-align: center;">Phantom</td>
                                        @else
                                            <td style="text-align: center;">Non Phantom</td>
                                        @endif
                                    @endif
                                </tr>
                            @endfor
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
            @endif
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
    <script>
        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('#tableDetail tfoot th').each(function() {
                var title = $(this).text();
                $(this).html(
                    '<input style="text-align: center; width: 100%;" type="text" placeholder="Search"/>'
                );
            });
            var table = $('#tableDetail').DataTable({
                "order": [],
                'dom': 'Bfrtip',
                'responsive': true,
                'paging': false,
                'ordering': false,
                'info': false,
                "columnDefs": [{
                    "targets": [7],
                    "createdCell": function(td, cellData, rowData, row, col) {
                        if (cellData == 'Phantom') {
                            $(td).css('color', '#a92028');
                        }
                    }
                }],
                "columns": [{
                        "width": "7.5%"
                    },
                    {
                        "width": "10%"
                    },
                    {
                        "width": "40%"
                    },
                    {
                        "width": "5%"
                    },
                    {
                        "width": "5%"
                    },
                    {
                        "width": "10%"
                    },
                    {
                        "width": "7.5%"
                    },
                    {
                        "width": "15%"
                    },

                ],
                'buttons': {
                    buttons: [{
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
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                initComplete: function() {
                    this.api()
                        .columns([0, 3, 7])
                        .every(function(dd) {
                            var column = this;
                            var theadname = $("#tableDetail th").eq([dd]).text();
                            var select = $(
                                    '<select style="width: 100%;"><option value="" style="font-size:11px;">All</option></select>'
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
                                    if ($("#tableDetail th").eq([dd]).text() == 'Category') {
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

            $('#tableDetail tfoot tr').appendTo('#tableDetail thead');

        });
    </script>
@endsection
