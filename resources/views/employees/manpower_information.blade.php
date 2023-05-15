@extends('layouts.display')
@section('stylesheets')
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            padding: 2px 5px 2px 5px;
            height: 35px !important;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@endsection
@section('header')
@endsection
@section('content')
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <div>
                <center>
                    <span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-3">
                        <div id="byStatus" style="height: 30vh; margin-bottom: 5px;"></div>
                    </div>
                    <div class="col-xs-3">
                        <div id="byDirect" style="height: 30vh; margin-bottom: 5px;"></div>
                    </div>
                    <div class="col-xs-3">
                        <div id="byIndirect" style="height: 30vh; margin-bottom: 5px;"></div>
                    </div>
                    <div class="col-xs-3">
                        <div id="byUnion" style="height: 30vh; margin-bottom: 5px;"></div>
                    </div>
                    <div class="col-xs-12">
                        <div id="byDepartment" style="height: 30vh; margin-bottom: 5px;"></div>
                    </div>
                    <div class="col-xs-12">
                        <div id="byPosition" style="height: 30vh; margin-bottom: 5px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row">
                        <div class="col-xs-12">
                            <table id="tableDetail" class="table table-bordered table-striped table-hover"
                                style="font-size: 12px;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 0.1%; text-align: center;">#</th>
                                        <th style="width: 0.1%; text-align: left;">ID</th>
                                        <th style="width: 1%; text-align: left;">Nama</th>
                                        <th style="width: 2%; text-align: left;">Dept</th>
                                        <th style="width: 1%; text-align: left;">Sect</th>
                                        <th style="width: 1%; text-align: left;">Group</th>
                                        <th style="width: 1%; text-align: left;">Sub</th>
                                        <th style="width: 0.1%; text-align: left;">Masuk</th>
                                        <th style="width: 0.1%; text-align: left;">Posisi</th>
                                        <th style="width: 0.1%; text-align: left;">Status</th>
                                        <th style="width: 0.1%; text-align: left;">Pekerjaan</th>
                                        <th style="width: 0.1%; text-align: left;">Serikat</th>
                                    </tr>
                                </thead>
                                <tbody id="tableDetailBody">
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot> --}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ url('js/highcharts.js') }}"></script>
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
            fetchData();
        });

        var employees = [];

        function modalStatus(category) {
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.employment_status == category) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.name + '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.section + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.group + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.sub_group + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.hire_date + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalDirect(category) {
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.employment_status == category && value.job_status == 'DIRECT') {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.name + '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.section + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.group + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.sub_group + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.hire_date + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalIndirect(category) {
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.employment_status == category && value.job_status == 'INDIRECT') {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.name + '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.section + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.group + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.sub_group + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.hire_date + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalUnion(category) {
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.union_name == category) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.name + '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.section + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.group + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.sub_group + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.hire_date + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalDepartment(category) {
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.department_shortname == category) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.name + '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.section + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.group + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.sub_group + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.hire_date + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function modalPosition(category) {
            var cnt = 0;
            var tableDetailBody = "";
            $('#tableDetailBody').html();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $.each(employees, function(key, value) {
                if (value.position == category) {
                    cnt += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employee_id + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.name + '</td>';
                    tableDetailBody += '<td style="width: 2%; text-align: left;">' + value.department + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.section + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.group + '</td>';
                    tableDetailBody += '<td style="width: 1%; text-align: left;">' + value.sub_group + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.hire_date + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.position + '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.employment_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.job_status +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: left;">' + value.union_name + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            $('#tableDetailBody').append(tableDetailBody);
            dataTable();
            $('#modalDetail').modal('show');
        }

        function dataTable() {
            // $('#tableDetail tfoot th').each(function() {
            //     var title = $(this).text();
            //     $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' +
            //         title + '" size="8"/>');
            // });

            var table = $('#tableDetail').DataTable({
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
                'pageLength': 20,
                'searching': true,
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                // initComplete: function() {
                //     this.api()
                //         .columns([3, 4, 5, 6, 8, 9, 10, 11])
                //         .every(function(dd) {
                //             var column = this;
                //             var theadname = $("#tableDetail th").eq([dd]).text();
                //             var select = $(
                //                     '<select style="width:100%"><option value="" style="font-size:11px;">All</option></select>'
                //                 )
                //                 .appendTo($(column.footer()).empty())
                //                 .on('change', function() {
                //                     var val = $.fn.dataTable.util.escapeRegex($(this)
                //                         .val());

                //                     column.search(val ? '^' + val + '$' : '', true,
                //                             false)
                //                         .draw();
                //                 });
                //             column
                //                 .data()
                //                 .unique()
                //                 .sort()
                //                 .each(function(d, j) {
                //                     var vals = d;
                //                     if ($("#tableDetail th").eq([dd]).text() ==
                //                         'Category') {
                //                         vals = d.split(' ')[0];
                //                     }
                //                     select.append(
                //                         '<option style="font-size:11px;" value="' +
                //                         d + '">' + vals + '</option>');
                //                 });
                //         });
                // },
            });

            // table.columns().every(function() {
            //     var that = this;

            //     $('input', this.footer()).on('keyup change', function() {
            //         if (that.search() !== this.value) {
            //             that
            //                 .search(this.value)
            //                 .draw();
            //         }
            //     });
            // });

            // $('#tableDetail tfoot tr').appendTo('#tableDetail thead');
        }

        function fetchData() {
            var data = {

            }
            $.get('{{ url('fetch/manpower/information') }}', data, function(result, status, xhr) {
                if (result.status) {
                    employees = result.employees;

                    var total_employee = 0;
                    var total_direct = 0;
                    var total_indirect = 0;
                    var total_outsource = 0;

                    var arr_statuses = result.employees;
                    var arr_direct_statuses = result.employees;
                    var arr_indirect_statuses = result.employees;
                    var arr_unions = result.employees;
                    var arr_genders = result.employees;
                    var arr_departments = result.employees;
                    var arr_positions = result.employees;

                    var by_statuses = [];
                    var by_direct_statuses = [];
                    var by_indirect_statuses = [];
                    var by_unions = [];
                    var by_genders = [];
                    var by_departments = [];
                    var by_positions = [];

                    arr_statuses.reduce(function(res, value) {
                        if (!res[value.employment_status]) {
                            res[value.employment_status] = {
                                category: value.employment_status,
                                count: 0
                            };
                            by_statuses.push(res[value.employment_status]);
                        }
                        res[value.employment_status].count += 1;
                        return res;
                    }, {});

                    arr_direct_statuses.reduce(function(res, value) {
                        if (!res[value.employment_status]) {
                            res[value.employment_status] = {
                                category: value.employment_status,
                                count: 0
                            };
                            by_direct_statuses.push(res[value.employment_status]);
                        }
                        if (value.job_status == 'DIRECT') {
                            res[value.employment_status].count += 1;
                        }
                        return res;
                    }, {});

                    arr_indirect_statuses.reduce(function(res, value) {
                        if (!res[value.employment_status]) {
                            res[value.employment_status] = {
                                category: value.employment_status,
                                count: 0
                            };
                            by_indirect_statuses.push(res[value.employment_status]);
                        }
                        if (value.job_status == 'INDIRECT') {
                            res[value.employment_status].count += 1;
                        }
                        return res;
                    }, {});

                    arr_unions.reduce(function(res, value) {
                        if (!res[value.union_name]) {
                            res[value.union_name] = {
                                category: value.union_name,
                                count: 0
                            };
                            by_unions.push(res[value.union_name]);
                        }
                        res[value.union_name].count += 1;
                        return res;
                    }, {});

                    arr_genders.reduce(function(res, value) {
                        if (!res[value.gender]) {
                            res[value.gender] = {
                                category: value.gender,
                                count: 0
                            };
                            by_genders.push(res[value.gender]);
                        }
                        res[value.gender].count += 1;
                        return res;
                    }, {});

                    arr_departments.reduce(function(res, value) {
                        if (!res[value.department_shortname]) {
                            res[value.department_shortname] = {
                                category: value.department_shortname,
                                count: 0
                            };
                            by_departments.push(res[value.department_shortname]);
                        }
                        res[value.department_shortname].count += 1;
                        return res;
                    }, {});

                    arr_positions.reduce(function(res, value) {
                        if (!res[value.position]) {
                            res[value.position] = {
                                category: value.position,
                                count: 0
                            };
                            by_positions.push(res[value.position]);
                        }
                        res[value.position].count += 1;
                        return res;
                    }, {});

                    function SortByCategory(a, b) {
                        var aCategory = a.category.toLowerCase();
                        var bCategory = b.category.toLowerCase();
                        return ((aCategory < bCategory) ? -1 : ((aCategory > bCategory) ? 1 : 0));
                    }

                    by_statuses.sort(SortByCategory);
                    by_direct_statuses.sort(SortByCategory);
                    by_indirect_statuses.sort(SortByCategory);
                    by_unions.sort(SortByCategory);
                    by_departments.sort(SortByCategory);
                    by_positions.sort(SortByCategory);
                    by_genders.sort(SortByCategory);

                    var by_status_categories = [];
                    var by_direct_categories = [];
                    var by_indirect_categories = [];
                    var by_union_categories = [];
                    var by_department_categories = [];
                    var by_position_categories = [];
                    var by_gender_categories = [];

                    var by_status_series = [];
                    var by_direct_series = [];
                    var by_indirect_series = [];
                    var by_union_series = [];
                    var by_department_series = [];
                    var by_position_series = [];
                    var by_gender_series = [];

                    $.each(by_statuses, function(key, value) {
                        by_status_categories.push(value.category);
                        by_status_series.push(value.count);
                    });

                    $.each(by_direct_statuses, function(key, value) {
                        by_direct_categories.push(value.category);
                        by_direct_series.push(value.count);
                    });

                    $.each(by_indirect_statuses, function(key, value) {
                        by_indirect_categories.push(value.category);
                        by_indirect_series.push(value.count);
                    });

                    $.each(by_unions, function(key, value) {
                        by_union_categories.push(value.category);
                        by_union_series.push(value.count);
                    });

                    $.each(by_departments, function(key, value) {
                        by_department_categories.push(value.category);
                        by_department_series.push(value.count);
                    });

                    $.each(by_positions, function(key, value) {
                        by_position_categories.push(value.category);
                        by_position_series.push(value.count);
                    });

                    $.each(by_genders, function(key, value) {
                        by_gender_categories.push(value.category);
                        by_gender_series.push(value.count);
                    });

                    Highcharts.chart('byStatus', {
                        title: {
                            text: 'By Status (雇用形態別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            title: {
                                text: null
                            }
                        },
                        xAxis: {
                            categories: by_status_categories,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '11px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.93,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalStatus(this.category);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Count',
                            type: 'column',
                            colorByPoint: true,
                            data: by_status_series,
                            showInLegend: false
                        }]
                    });

                    Highcharts.chart('byDirect', {
                        title: {
                            text: 'Direct MP (直接人工)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            type: 'logarithmic',
                            title: {
                                text: null
                            }
                        },
                        xAxis: {
                            categories: by_direct_categories,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '11px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.93,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalDirect(this.category);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Count',
                            type: 'column',
                            colorByPoint: true,
                            data: by_direct_series,
                            showInLegend: false
                        }]
                    });

                    Highcharts.chart('byIndirect', {
                        title: {
                            text: 'Indirect MP (間接人工)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            type: 'logarithmic',
                            title: {
                                text: null
                            }
                        },
                        xAxis: {
                            categories: by_indirect_categories,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '11px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.93,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalIndirect(this.category);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Count',
                            type: 'column',
                            colorByPoint: true,
                            data: by_indirect_series,
                            showInLegend: false
                        }]
                    });

                    Highcharts.chart('byUnion', {
                        title: {
                            text: 'By Union (労働組合別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            type: 'logarithmic',
                            title: {
                                text: null
                            }
                        },
                        xAxis: {
                            categories: by_union_categories,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '11px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.93,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalUnion(this.category);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Count',
                            type: 'column',
                            colorByPoint: true,
                            data: by_union_series,
                            showInLegend: false
                        }]
                    });

                    Highcharts.chart('byDepartment', {
                        title: {
                            text: 'By Department (部門別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            type: 'logarithmic',
                            title: {
                                text: null
                            }
                        },
                        xAxis: {
                            categories: by_department_categories,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '11px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.93,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalDepartment(this.category);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Count',
                            type: 'column',
                            colorByPoint: true,
                            data: by_department_series,
                            showInLegend: false
                        }]
                    });

                    Highcharts.chart('byPosition', {
                        title: {
                            text: 'By Position (役職別)',
                            style: {
                                fontSize: '16px',
                                fontWeight: 'bold'
                            }
                        },
                        yAxis: {
                            type: 'logarithmic',
                            title: {
                                text: null
                            }
                        },
                        xAxis: {
                            categories: by_position_categories,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            labels: {
                                style: {
                                    fontSize: '11px',
                                    textOverflow: 'none'
                                }
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: false,
                                        fontSize: '14px'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.93,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalPosition(this.category);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Count',
                            type: 'column',
                            colorByPoint: true,
                            data: by_position_series,
                            showInLegend: false
                        }]
                    });
                } else {
                    alert(result.message);
                    return false;
                }
            });
        }

        Highcharts.createElement('link', {
            href: '{{ url('fonts/UnicaOne.css') }}',
            rel: 'stylesheet',
            type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
            colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
                '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'
            ],
            chart: {
                backgroundColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 1,
                        y2: 1
                    },
                    stops: [
                        [0, '#2a2a2b'],
                        [1, '#3e3e40']
                    ]
                },
                style: {
                    fontFamily: 'sans-serif'
                },
                plotBorderColor: '#606063'
            },
            title: {
                style: {
                    color: '#E0E0E3',
                    textTransform: 'uppercase',
                    fontSize: '20px'
                }
            },
            subtitle: {
                style: {
                    color: '#E0E0E3',
                    textTransform: 'uppercase'
                }
            },
            xAxis: {
                gridLineColor: '#707073',
                labels: {
                    style: {
                        color: '#E0E0E3'
                    }
                },
                lineColor: '#707073',
                minorGridLineColor: '#505053',
                tickColor: '#707073',
                title: {
                    style: {
                        color: '#A0A0A3'

                    }
                }
            },
            yAxis: {
                gridLineColor: '#707073',
                labels: {
                    style: {
                        color: '#E0E0E3'
                    }
                },
                lineColor: '#707073',
                minorGridLineColor: '#505053',
                tickColor: '#707073',
                tickWidth: 1,
                title: {
                    style: {
                        color: '#A0A0A3'
                    }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                style: {
                    color: '#F0F0F0'
                }
            },
            plotOptions: {
                series: {
                    dataLabels: {
                        color: 'white'
                    },
                    marker: {
                        lineColor: '#333'
                    }
                },
                boxplot: {
                    fillColor: '#505053'
                },
                candlestick: {
                    lineColor: 'white'
                },
                errorbar: {
                    color: 'white'
                }
            },
            legend: {
                itemStyle: {
                    color: '#E0E0E3'
                },
                itemHoverStyle: {
                    color: '#FFF'
                },
                itemHiddenStyle: {
                    color: '#606063'
                }
            },
            credits: {
                style: {
                    color: '#666'
                }
            },
            labels: {
                style: {
                    color: '#707073'
                }
            },

            drilldown: {
                activeAxisLabelStyle: {
                    color: '#F0F0F3'
                },
                activeDataLabelStyle: {
                    color: '#F0F0F3'
                }
            },

            navigation: {
                buttonOptions: {
                    symbolStroke: '#DDDDDD',
                    theme: {
                        fill: '#505053'
                    }
                }
            },

            rangeSelector: {
                buttonTheme: {
                    fill: '#505053',
                    stroke: '#000000',
                    style: {
                        color: '#CCC'
                    },
                    states: {
                        hover: {
                            fill: '#707073',
                            stroke: '#000000',
                            style: {
                                color: 'white'
                            }
                        },
                        select: {
                            fill: '#000003',
                            stroke: '#000000',
                            style: {
                                color: 'white'
                            }
                        }
                    }
                },
                inputBoxBorderColor: '#505053',
                inputStyle: {
                    backgroundColor: '#333',
                    color: 'silver'
                },
                labelStyle: {
                    color: 'silver'
                }
            },

            navigator: {
                handles: {
                    backgroundColor: '#666',
                    borderColor: '#AAA'
                },
                outlineColor: '#CCC',
                maskFill: 'rgba(255,255,255,0.1)',
                series: {
                    color: '#7798BF',
                    lineColor: '#A6C7ED'
                },
                xAxis: {
                    gridLineColor: '#505053'
                }
            },

            scrollbar: {
                barBackgroundColor: '#808083',
                barBorderColor: '#808083',
                buttonArrowColor: '#CCC',
                buttonBackgroundColor: '#606063',
                buttonBorderColor: '#606063',
                rifleColor: '#FFF',
                trackBackgroundColor: '#404043',
                trackBorderColor: '#404043'
            },

            legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
            background2: '#505053',
            dataLabelsColor: '#B0B0B3',
            textColor: '#C0C0C0',
            contrastTextColor: '#F0F0F3',
            maskColor: 'rgba(255,255,255,0.3)'
        };
        Highcharts.setOptions(Highcharts.theme);
    </script>
@endsection
