@extends('layouts.master')
@section('stylesheets')
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
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
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
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
            KD Parts Stock <span class="text-purple">KD部品在庫</span>
            <small>By Each Location <span class="text-purple">ロケーション毎</span></small>
        </h1>
        <ol class="breadcrumb" id="last_update">
        </ol>
    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border" id="boxTitle">
                    </div>
                    <div class="box-body">
                        <div id="container" style="width:100%; height:450px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <div class="box-body">
                        <table id="tableStock" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="width : 1%">Material Number</th>
                                    <th style="width : 4%">Description</th>
                                    <th style="width : 1%">HPL</th>
                                    <th style="width : 1%">Destination</th>
                                    <th style="width : 1%">Location</th>
                                    <th style="width : 1%; text-align: right;">Quantity</th>
                                    <th style="width : 1%">Base Unit</th>
                                </tr>
                            </thead>
                            <tbody id="tableStockBody">
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
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalStock">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"></h4>
                    <div class="modal-body table-responsive no-padding">
                        <table class="table table-hover">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <th>Material</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>m&sup3;</th>
                            </thead>
                            <tbody id="tableBody">
                            </tbody>
                            <tfoot style="background-color: RGB(252, 248, 227);">
                                <th>Total</th>
                                <th></th>
                                <th id="totalQty"></th>
                                <th id="totalM3"></th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/exporting.js') }}"></script>
    <script src="{{ url('js/export-data.js') }}"></script>
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
            fillChart();
        });

        var volume = <?php echo json_encode($volume); ?>;
        var stocks = [];

        function addZero(i) {
            if (i < 10) {
                i = "0" + i;
            }
            return i;
        }

        function getActualFullDate() {
            var d = new Date();
            var day = addZero(d.getDate());
            var month = addZero(d.getMonth() + 1);
            var year = addZero(d.getFullYear());
            var h = addZero(d.getHours());
            var m = addZero(d.getMinutes());
            var s = addZero(d.getSeconds());
            return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s + ")";
        }

        function fillChart() {
            $.get('{{ url('fetch/kd_stock') }}', function(result, status, xhr) {
                if (xhr.status == 200) {
                    if (result.status) {
                        $('#tableStock').DataTable().clear();
                        $('#tableStock').DataTable().destroy();
                        $('#last_update').html('<b>Last Updated: ' + getActualFullDate() + '</b>');
                        stocks = result.stocks;

                        $('#tableStockBody').html("");

                        var tableStockData = '';

                        $.each(result.stocks, function(key, value) {
                            tableStockData += '<tr>';
                            tableStockData += '<td style="width: 1%;">' + value.material_number + '</td>';
                            tableStockData += '<td style="width: 4%;">' + value.material_description +
                                '</td>';
                            tableStockData += '<td style="width: 1%;">' + value.hpl + '</td>';
                            tableStockData += '<td style="width: 1%;">' + value.destination + '</td>';
                            tableStockData += '<td style="width: 1%;">' + value.location + '</td>';
                            tableStockData += '<td style="width: 1%; text-align: right;">' + value
                                .quantity + '</td>';
                            tableStockData += '<td style="width: 1%;">' + value.base_unit + '</td>';
                            tableStockData += '</tr>';
                        });

                        $('#tableStockBody').append(tableStockData);
                        $('#tableStock tfoot th').each(function() {
                            var title = $(this).text();
                            $(this).html(
                                '<input style="text-align: center;" type="text" placeholder="Search ' +
                                title + '" size="8"/>');
                        });

                        var table = $('#tableStock').DataTable({
                            'dom': 'Bfrtip',
                            'responsive': true,
                            "pageLength": 25,
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
                                    },
                                ]
                            }
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

                        $('#tableStock tfoot tr').appendTo('#tableStock thead');

                        var array = result.stocks;
                        var result = [];
                        array.reduce(function(res, value) {
                            if (!res[value.destination]) {
                                res[value.destination] = {
                                    destination: value.destination,
                                    production: 0,
                                    intransit: 0,
                                    warehouse: 0,
                                    production_m3: 0,
                                    intransit_m3: 0,
                                    warehouse_m3: 0,
                                    total_stock: 0,
                                    total_volume: 0
                                };
                                result.push(res[value.destination])
                            }
                            if (value.location == 'Production') {
                                res[value.destination].production += value.quantity;
                                res[value.destination].production_m3 += value.m3;
                            }
                            if (value.location == 'InTransit') {
                                res[value.destination].intransit += value.quantity;
                                res[value.destination].intransit_m3 += value.m3;
                            }
                            if (value.location == 'Warehouse') {
                                res[value.destination].warehouse += value.quantity;
                                res[value.destination].warehouse_m3 += value.m3;
                            }
                            res[value.destination].total_stock += value.quantity;
                            res[value.destination].total_volume += value.m3;
                            return res;
                        }, {});

                        function SortByName(a, b) {
                            var aName = a.total_volume;
                            var bName = b.total_volume;
                            return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
                        }

                        result.sort(SortByName);

                        var xAxis = [];
                        var productionCount = [];
                        var inTransitCount = [];
                        var fstkCount = [];
                        var total_stock = 0;
                        var total_volume = 0;

                        for (i = 0; i < result.length; i++) {
                            xAxis.push(result[i].destination);
                            productionCount.push(result[i].production_m3);
                            inTransitCount.push(result[i].intransit_m3);
                            fstkCount.push(result[i].warehouse_m3);
                            total_stock += result[i].total_stock;
                            total_volume += result[i].total_volume;
                        }

                        var chart;
                        chart = new Highcharts.Chart({
                            colors: ['rgba(119, 152, 191, 0.80)', 'rgba(144, 238, 126, 0.80)',
                                'rgba(247, 163, 92, 0.80)'
                            ],
                            chart: {
                                renderTo: 'container',
                                type: 'column',
                            },
                            title: {
                                text: 'KD Parts Stock By Location Chart'
                            },
                            xAxis: {
                                categories: xAxis,
                                gridLineWidth: 1,
                                scrollbar: {
                                    enabled: true
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'Volume (m&sup3);'
                                },
                                stackLabels: {
                                    enabled: true,
                                    style: {
                                        fontWeight: 'bold',
                                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                                    },
                                    formatter: function() {
                                        return Highcharts.numberFormat(this.total, 2) + " m&sup3";
                                    }
                                }
                            },
                            credits: {
                                enabled: false
                            },
                            plotOptions: {
                                series: {
                                    borderColor: '#303030',
                                    cursor: 'pointer',
                                    stacking: 'normal',
                                    point: {
                                        events: {
                                            click: function() {
                                                modalStock(this.category, this.series.name);
                                            }
                                        }
                                    },
                                    minPointLength: 1
                                }
                            },
                            tooltip: {
                                formatter: function() {
                                    return '<b>' + this.x + '</b><br/>' +
                                        this.series.name + ': ' + this.y + ' m&sup3<br/>' +
                                        'Total: ' + this.point.stackTotal + ' m&sup3';
                                }
                            },
                            series: [{
                                name: 'Warehouse',
                                data: fstkCount,
                            }, {
                                name: 'InTransit',
                                data: inTransitCount,
                            }, {
                                name: 'Production',
                                data: productionCount,
                            }]
                        });

                        $('#boxTitle').html(
                            '<i class="fa fa-info-circle"></i><h4 class="box-title">Total Stock: <b>' +
                            total_stock + ' pc(s)</b> &#8786; <b>' + total_volume.toFixed(2) +
                            ' m&sup3;</b> (<b>' + (total_volume / 52).toFixed(2) + ' container(s)</b>)</h4>');
                        $('#boxTitle').append('<div class="pull-right"><b>1 Container &#8786; 52 m&sup3</b></div>');
                    } else {
                        alert('Attempt to retrieve data failed');
                    }
                } else {
                    alert('Disconnected from server');
                }
            });
        }

        function modalStock(destination, location) {
            $('#tableBody').html("");
            $('.modal-title').html("");
            $('.modal-title').html('Location <b>' + location + '</b> for Destination <b>' + destination + '</b>');

            var tableData = '';
            var totalQty = 0;
            var totalM3 = 0;

            console.log(destination);
            console.log(location);

            $.each(stocks, function(key, value) {
                if (value.location == location && value.destination == destination) {
                    totalQty += value.quantity;
                    totalM3 += value.m3;
                    tableData += '<tr>';
                    tableData += '<td>' + value.material_number + '</td>';
                    tableData += '<td>' + value.material_description + '</td>';
                    tableData += '<td>' + value.quantity + '</td>';
                    tableData += '<td>' + value.m3 + '</td>';
                    tableData += '</tr>';
                }
            });
            $('#tableBody').append(tableData);
            $('#modalStock').modal('show');
            $('#totalQty').html('');
            $('#totalQty').append(totalQty.toLocaleString());
            $('#totalM3').html('');
            $('#totalM3').append(totalM3.toFixed(2).toLocaleString());
        }
    </script>
@endsection
