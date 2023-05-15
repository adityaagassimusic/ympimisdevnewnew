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
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
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
        </h1>
    </section>
@endsection

@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <div>
                <center>
                    <span style="font-size: 3vw; text-align: center; position: fixed; top: 45%; left: 42.5%;"><i
                            class="fa fa-spin fa-hourglass-half"></i>&nbsp;&nbsp;&nbsp;Loading ...</span>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="box-header" style="padding-bottom: 0px;">
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Month</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" onchange="fillTable()" class="form-control monthpicker"
                                            name="month" id="month" placeholder="Select Month">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-body" style="padding-top: 0px;">
                        <div class="row">
                            <div class="col-md-6" style="padding-right: 0px;">
                                <div id="container1" style="height: 70vh;"></div>
                            </div>
                            <div class="col-md-6" style="padding-left: 0px;">
                                <div id="container2" style="height: 70vh;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box box-primary">
                    <div class="box-body">
                        <div class="col-md-12">
                            <table id="report" class="table table-bordered table-striped table-hover">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 10%">Tag</th>
                                        <th style="width: 10%">Material Number</th>
                                        <th style="width: 25%">Description</th>
                                        <th style="width: 5%">Model</th>
                                        <th style="width: 5%">Surface</th>
                                        <th style="width: 7.5%">Qty (PCs)</th>
                                        <th style="width: 7.5%">Usage Silver (Gr)</th>
                                        <th style="width: 5%">Status</th>
                                        <th style="width: 10%">Location</th>
                                        <th style="width: 15%">Created at</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyReport">
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
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/highstock.js') }}"></script>
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

            fillTable();
        });

        function clearConfirmation() {
            location.reload(true);
        }

        function fillTable() {
            var data = {
                month: $('#month').val(),
            }

            $('#loading').show();
            $.get('{{ url('fetch/body/rework_result') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#report').DataTable().clear();
                    $('#report').DataTable().destroy();
                    $('#bodyReport').html('')

                    var tableData = '';
                    for (let i = 0; i < result.body_details.length; i++) {
                        for (let j = 0; j < result.materials.length; j++) {
                            if (result.body_details[i].material_number == result.materials[j].material_number) {

                                tableData += '<tr>';

                                tableData += '<td>';
                                tableData += result.body_details[i].tag;
                                tableData += '</td>';

                                tableData += '<td>';
                                tableData += result.body_details[i].material_number;
                                tableData += '</td>';

                                tableData += '<td>';
                                tableData += result.materials[j].material_description;
                                tableData += '</td>';

                                tableData += '<td>';
                                tableData += result.materials[j].model;
                                tableData += '</td>';

                                tableData += '<td>';
                                tableData += result.materials[j].surface;
                                tableData += '</td>';

                                tableData += '<td>';
                                tableData += result.body_details[i].quantity;
                                tableData += '</td>';

                                tableData += '<td>';
                                tableData += Math.round((result.body_details[i].quantity *
                                    (result.materials[j].usage /
                                        result.materials[j].divider)), 3);
                                tableData += '</td>';

                                tableData += '<td>';
                                tableData += result.body_details[i].note;
                                tableData += '</td>';

                                tableData += '<td>';
                                tableData += result.body_details[i].location;
                                tableData += '</td>';

                                tableData += '<td>';
                                tableData += result.body_details[i].created_at;
                                tableData += '</td>';

                                tableData += '</tr>';

                            }
                        }
                    }

                    $('#bodyReport').append(tableData);
                    var tableQty = $('#report').DataTable({
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
                            }, {
                                extend: 'excel',
                                className: 'btn btn-info',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        'columnDefs': [{
                            "targets": [2],
                            "className": "text-left",
                        }],
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });


                    var resumeData = [];
                    for (let i = 0; i < result.body_details.length; i++) {
                        for (let j = 0; j < result.materials.length; j++) {
                            if (result.body_details[i].material_number == result.materials[j].material_number) {
                                var key = '';
                                key += result.body_details[i].created_at.substr(0, 10);
                                key += '_';
                                key += result.materials[j].key;
                                if (!resumeData[key]) {
                                    resumeData[key] = {
                                        'date': result.body_details[i].created_at.substr(0, 10),
                                        'remark': result.materials[j].key,
                                        'quantity': result.body_details[i].quantity,
                                        'usage': result.body_details[i].quantity * (result.materials[j].usage /
                                            result.materials[j].divider),
                                    };
                                } else {
                                    resumeData[key].quantity = resumeData[key].quantity +
                                        result.body_details[i].quantity;

                                    resumeData[key].usage = resumeData[key].usage +
                                        (result.body_details[i].quantity *
                                            (result.materials[j].usage / result.materials[j].divider));
                                }

                                break;

                            }
                        }
                    }

                    var xCategories = [];

                    var headData = [];
                    var footData = [];
                    var bodyData = [];
                    var allData = [];

                    var acc_head = 0;
                    var acc_foot = 0;
                    var acc_body = 0;
                    var acc_all = 0;

                    var headUsage = [];
                    var footUsage = [];
                    var bodyUsage = [];
                    var allUsage = [];

                    var usage_head = 0;
                    var usage_foot = 0;
                    var usage_body = 0;
                    var usage_all = 0;

                    for (let i = 0; i < result.calendars.length; i++) {
                        // xCategories.push(result.calendars[i].week_date);

                        for (var key in resumeData) {
                            if (result.calendars[i].week_date == resumeData[key].date) {
                                if (resumeData[key].remark == 'HEAD') {
                                    acc_head += resumeData[key].quantity;
                                    usage_head += resumeData[key].usage;
                                } else if (resumeData[key].remark == 'FOOT') {
                                    acc_foot += resumeData[key].quantity;
                                    usage_foot += resumeData[key].usage;
                                } else if (resumeData[key].remark == 'BODY') {
                                    acc_body += resumeData[key].quantity;
                                    usage_body += resumeData[key].usage;
                                }
                            }
                        }

                        headData.push([Date.parse(result.calendars[i].week_date), parseInt(acc_head)]);
                        footData.push([Date.parse(result.calendars[i].week_date), parseInt(acc_foot)]);
                        bodyData.push([Date.parse(result.calendars[i].week_date), parseInt(acc_body)]);

                        all = parseInt(acc_head) + parseInt(acc_foot) + parseInt(acc_body);
                        allData.push([Date.parse(result.calendars[i].week_date), parseInt(all)]);

                        headUsage.push([Date.parse(result.calendars[i].week_date), parseInt(usage_head)]);
                        footUsage.push([Date.parse(result.calendars[i].week_date), parseInt(usage_foot)]);
                        bodyUsage.push([Date.parse(result.calendars[i].week_date), parseInt(usage_body)]);

                        usage = parseInt(usage_head) + parseInt(usage_foot) + parseInt(usage_body);
                        allUsage.push([Date.parse(result.calendars[i].week_date), parseInt(usage)]);

                    }

                    Highcharts.stockChart('container1', {
                        rangeSelector: {
                            selected: 0
                        },
                        title: {
                            text: 'ACCUMULATIVE REWORK PLATING FL',
                        },
                        xAxis: {
                            type: 'datetime',
                            tickInterval: 24 * 3600 * 1000
                        },
                        yAxis: {
                            title: {
                                text: 'Quantity'
                            }
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 1,
                                borderColor: '#212121',
                                showInNavigator: true,
                                cursor: 'pointer',
                                // point: {
                                //     events: {
                                //         click: function() {
                                //             modalBalance('Middle', $.date(this.category), this.series
                                //                 .name);
                                //         }
                                //     }
                                // }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            split: false,
                            shared: true,
                            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                        },
                        legend: {
                            enabled: true
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Total',
                            data: allData,
                            color: 'black',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'solid',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Head',
                            data: headData,
                            color: '#1446a0',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'solid',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Body',
                            data: bodyData,
                            color: '#f5d547',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'solid',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Foot',
                            data: footData,
                            color: '#db3069',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'solid',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }],
                    });

                    Highcharts.stockChart('container2', {
                        rangeSelector: {
                            selected: 0
                        },
                        title: {
                            text: 'ACCUMULATIVE REWORK PLATING FL',
                        },
                        xAxis: {
                            type: 'datetime',
                            tickInterval: 24 * 3600 * 1000
                        },
                        yAxis: {
                            title: {
                                text: 'Gram'
                            }
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 1,
                                borderColor: '#212121',
                                showInNavigator: true,
                                cursor: 'pointer',
                                // point: {
                                //     events: {
                                //         click: function() {
                                //             modalBalance('Middle', $.date(this.category), this.series
                                //                 .name);
                                //         }
                                //     }
                                // }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            split: false,
                            shared: true,
                            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                        },
                        legend: {
                            enabled: true
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Total',
                            data: allUsage,
                            color: 'black',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'solid',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Gram</b><br/>',
                            }
                        }, {
                            name: 'Head',
                            data: headUsage,
                            color: '#1446a0',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'solid',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Gram</b><br/>',
                            }
                        }, {
                            name: 'Body',
                            data: bodyUsage,
                            color: '#f5d547',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'solid',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Gram</b><br/>',
                            }
                        }, {
                            name: 'Foot',
                            data: footUsage,
                            color: '#db3069',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'solid',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Gram</b><br/>',
                            }
                        }],
                    });


                    $('#loading').hide();

                } else {
                    $('#loading').hide();
                    alert('Attempt to retrieve data failed');
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
