@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
    .content {
        color: white;
        font-weight: bold;
    }

    #loading,
    #error {
        display: none;
    }

    .loading {
        margin-top: 8%;
        position: absolute;
        left: 50%;
        top: 50%;
        -ms-transform: translateY(-50%);
        transform: translateY(-50%);
    }

    .button5 {
        border-radius: 50%;
    }

    .button1 {
        background-color: rgb(244, 91, 91);
        border: none;
        color: white;
        padding: 10px;
        text-align: center;
        text-decoration: none;
        font-size: 7px;
    }

    .button2 {
        background-color: rgb(43, 144, 143);
        border: none;
        color: white;
        padding: 10px;
        text-align: center;
        text-decoration: none;
        font-size: 5px;
    }

    .button3 {
        background-color: rgb(144, 238, 126);
        border: none;
        color: white;
        padding: 10px;
        text-align: center;
        text-decoration: none;
        font-size: 5px;
    }

    .button4 {
        background-color: rgb(99, 150, 89);
        border: none;
        color: white;
        padding: 10px;
        text-align: center;
        text-decoration: none;
        font-size: 5px;
    }

    .button6 {
        background-color: #3f51b5;
        border: none;
        color: white;
        padding: 10px;
        text-align: center;
        text-decoration: none;
        font-size: 5px;
    }



    thead>tr>th {
        text-align: center;
        overflow: hidden;
        consok padding: 3px;
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
        border: 1px solid #2a2a2b;
    }

    table.table-bordered>thead>tr>th {
        border: 1px solid #2a2a2b;
        vertical-align: middle;
        text-align: center;
    }

    table.table-bordered>tbody>tr>td {
        border: 1px solid #2a2a2b;
        text-align: center;
        vertical-align: middle;
        padding: 0;
    }

    table.table-bordered>tfoot>tr>th {
        border: 1px solid #2a2a2b;
        padding: 0;
    }

    td {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .urgent {
        background-color: #f56954;
        color: white;
    }
</style>
@endsection
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
    <div class="row">
        <div class="col-xs-12" style="margin:0px;">
            <div class="pull-right" id="last_update"
            style="color: white; margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
        </div>
        <div class="col-xs-12" style="margin-top: 20px;">
            <div class="row" style="margin:0px;">
                <div class="col-xs-2">
                    <div class="input-group date">
                        <div class="input-group-addon bg-green" style="border: none;">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control datepicker" id="date" placeholder="Select Date">
                    </div>
                </div>
                <div class="col-xs-1">
                    <button class="btn btn-success" onclick="fillChart1()">Update Chart</button>
                </div>
                <div class="pull-right" id="last_update"
                style="margin: 0px;padding-top: 0px;padding-right: 0px;font-size: 1vw;"></div>
            </div>
        </div>


        <div class="col-xs-12">

            <div class="col-xs-6" style="padding: 0px; display: none;">
                <div id="mc-workload-shift-3" style="width:100%;"></div>
            </div>
            <div class="col-xs-6" style="padding: 0px; display: none;">
                <div id="mc-workload-shift-2" style="width:100%;"></div>
            </div>


            <div class="col-xs-12" style="padding: 0px;">
                <div id="container" style="width:100%; margin-top: 1%;"></div>
            </div>
            <div class="col-md-12" style="padding-bottom: 10px;">
                <div class="col-md-12" style="text-align:center;">
                    <label style="margin-top: 12px; text-align: center; padding:10px;"><button class="button1 button5"
                        style="text-align: center;"></button>Idle</label>
                        &nbsp;
                        <label style="margin-top: 12px;"><button class="button2 button5"></button> Area GS 1</label>
                        &nbsp;

                        <label style="margin-top: 12px;"><button class="button3 button5"></button> Area GS 2</label>
                        &nbsp;

                        <label style="margin-top: 12px;"><button class="button4 button5"></button> Area GS 3</label>
                        &nbsp;

                        <label style="margin-top: 12px;"><button class="button6 button5"></button> Lain-Lain</label>
                        &nbsp;
                        
                    </div>
                </div>


                <div class="col-md-12" style="margin-top: 5px; padding:0 !important">
                    <div id="chart_op" style="width: 99%"></div>
                </div>

            </div>
        </div>

        <div class="modal fade" id="modal-operator" style="color: black;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" style="text-transform: uppercase; text-align: center;"><b>Operator
                        Productivity Details</b></h4>
                        <h5 class="modal-title" style="text-align: center;" id="judul-operator"></h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table id="operator" class="table table-striped table-bordered"
                                style="width: 100%; margin-bottom: 2%;">
                                <thead id="operator-head" style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>Operator</th>
                                        <th>Status</th>
                                        <th id="Pallet" hidden>No Pallet</th>
                                        <th id="kode_request" hidden>Kode Request</th>
                                        <th>Kode Request</th>
                                        <th>Joblist</th>
                                        <th>Start</th>
                                        <th>End</th>

                                    </tr>
                                </thead>
                                <tbody id="operator-body">
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i
                        class="fa fa-close"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="modalDetail" style="z-index: 10000;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-xs-12" style="background-color: #3c8dbc;">
                        <h1 style="text-align: center; margin:5px; font-weight: bold; color: white">DETAIL JOBLIST GS</h1>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <table id="detail_job" class="table table-hover table-striped table-bordered" style="font-weight: bold;"> 
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>ID</th>
                                        <th>Employee Id</th>
                                        <th>Pekerjaan</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Load Time</th>
                                    </tr>
                                </thead>
                                <tbody id="detail_job_body">
                                </tbody>
                                <tfoot style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th colspan="5" style="color: white">TOTAL</th>
                                        <th colspan="1"style="text-align: right;color: white" id="modalDetailTotal4"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @section('scripts')
    <script src="{{ url('js/highcharts-gantt.js') }}"></script>
    <script src="{{ url('js/highcharts-3d.js') }}"></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var data_all_job = [];

        jQuery(document).ready(function() {
            fillChart1();
            // setInterval(fillChart, 60000);
            $('#date').val("");

            $('.datepicker').datepicker({
                <?php $tgl_max = date('Y-m-d') ?>
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true,   
                endDate: '<?php echo $tgl_max ?>'
            });

        });

        function fillChart1() {
            fillChart();
            var date = $("#date").val();
            
            var data = {
                date : date
            }

            $.get('{{ url("fetch/gs/aktual") }}',data, function(result, status, xhr) {
                if (result.status) {
                    var machine_name = [];
                    var name = [];
                    var pic = [];
                    var time = [];
                    var time1 = [];
                    var time2 = [];
                    var time3 = [];
                    var time5 = [];
                    var time6 = [];

                    var machines = [];
                    var series = [];
                    var unfilled = true;
                    var tot_time1 = "";
                    var tot_time2 = "";
                    var tot_time5 = "";
                    var tot_time6 = "";

                    data_all_job = result.data_all;

                    for (var i = 0; i < result.operators_time.length; i++) {
                        name.push(result.operators_time[i].name);
                        pic.push(result.operators_time[i].employee_id);

                        tot_time1 = parseFloat(result.operators_time[i].st_gs1s);
                        tot_time3 = result.operators_time[i].st_gs2s;
                        tot_time4 = result.operators_time[i].st_gs3s;
                        tot_time5 = result.operators_time[i].st_gs4s;
                        tot_time6 = result.operators_time[i].st_gs5s;

                        time.push(parseFloat(parseFloat(tot_time1).toFixed(2)));
                        time2.push(parseFloat(parseFloat(tot_time3).toFixed(2)));
                        time3.push(parseFloat(parseFloat(tot_time4).toFixed(2)));
                        time5.push(parseFloat(parseFloat(tot_time5).toFixed(2)));
                        time6.push(parseFloat(parseFloat(tot_time6).toFixed(2)));
                    }

                    $('#chart_op').highcharts({
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'General Service Workload',
                            style: {
                                fontSize: '24px',
                                fontWeight: 'bold'
                            }
                        },

                        xAxis: {
                            type: 'category',
                            categories: name,
                            lineWidth: 2,
                            lineColor: '#9e9e9e',
                            gridLineWidth: 1
                        },

                        yAxis: {
                            lineWidth: 2,
                            lineColor: '#fff',
                            type: 'linear',
                            title: {
                                text: 'Minutes(s)'
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                                    color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                                }
                            },
                            
                        },

                        legend: {
                            enabled: true,
                            reversed: true,
                            itemStyle: {
                                color: "white",
                                fontSize: "15px",
                                fontWeight: "bold",

                            },
                        },
                        plotOptions: {
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        fontSize: '15px'
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
                                            showOperatorDetail(this.category, this.series
                                                .name);
                                        }
                                    }
                                },
                            },
                            column: {
                                color: Highcharts.ColorString,
                                stacking: 'normal',
                                borderRadius: 1,
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },

                        tooltip: {
                            formatter: function() {
                                return this.series.name + ' : ' + this.y;
                            }
                        },
                        series: [{
                            name: 'Area GS 1',
                            data: time
                        },
                        {
                            name: 'Area GS 2',
                            data: time2
                        },
                        {
                            name: 'idle',
                            data: time6
                        },
                        {
                            name: 'Area GS 3',
                            data: time3
                        },

                        {
                            name: 'Lain-Lain',
                            data: time5,
                            color: '#3f51b5'
                        }

                        ]
                    });
}
});
}


function showOperatorDetail(cat,st) {

    // for (var i = 0; i < data_all_job.length; i++) {
        $('#detail_job').DataTable().clear();
        $('#detail_job').DataTable().destroy();
        $("#detail_job_body").empty();
        var resultData = "";
        var no = 1;
        var resultTotal4 = 0;
        var cnt_times2 = 0;

        var st_job = "-";
        for (var i = 0; i < data_all_job.length; i++) {

            var namest = data_all_job[i].employee_name.split(' ').slice(0,2).join(' ');

            if(namest == cat && data_all_job[i].status == st && data_all_job[i].finished_at != null){

                if (data_all_job[i].list_job != null) {
                    st_job = data_all_job[i].list_job ;
                }

                cnt_times2 = parseFloat(parseFloat(data_all_job[i].time).toFixed(2));
                resultData += '<tr style="background-color: #adadad;">';
                resultData += '<td style="width: 1%">'+ no +'</td>';
                resultData += '<td style="width: 5%">'+ data_all_job[i].employee_id +'</td>';
                resultData += '<td style="width: 5%">'+ st_job +'</td>';
                resultData += '<td style="width: 2%">'+ data_all_job[i].request_at +'</td>';
                resultData += '<td style="width: 2%">'+ data_all_job[i].finished_at +'</td>';
                resultData += '<td style="width: 1%; font-weight: bold;">'+ cnt_times2 +'</td>';
                resultData += '</tr>';
                resultTotal4 += Math.abs(data_all_job[i].time);
                no++;
            }

        }


        var tot = resultTotal4.toFixed(2);

        $('#modalDetailTotal4').html('');
        $('#modalDetailTotal4').append(tot.toLocaleString());

        $("#detail_job_body").append(resultData);

        $('#detail_job').DataTable( {
            'dom': 'Bfrtip',
            'responsive':true,
            'lengthMenu': [
            [ 20, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            'buttons': {
              buttons:[
              {
                extend: 'pageLength',
                className: 'btn btn-default',
            },

            {
                extend: 'excel',
                className: 'btn btn-info',
                text: '<i class="fa fa-file-excel-o"></i> Excel',
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
      'info': true,
      'autoWidth': true,
      "sPaginationType": "full_numbers",
      "bJQueryUI": true,
      "bAutoWidth": false,
      "processing": false,
      "order": [[ 0, 'asc' ]]

  } );


        $("#modalDetail").modal('show');


    }

    Highcharts.createElement('link', {
        href: '{{ url("fonts/UnicaOne.css") }}',
        rel: 'stylesheet',
        type: 'text/css'
    }, null, document.getElementsByTagName('head')[0]);

    Highcharts.theme = {
        colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
        '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'
        ],
        chart: {
            backgroundColor: null,
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

    Highcharts.setOptions({
        global: {
            useUTC: true,
            timezoneOffset: -420

        }
    });


    function fillChart() {
        var position = $(document).scrollTop();
        if ($("#date").val() == "") {
            var date = new Date().toISOString().slice(0, 10);
        } else {
            var date = $("#date").val();
        }

        var data = {
            date: date
        }


        $.get('{{ url("fetch/gs/aktual") }}', data, function(result, status, xhr) {
            if (result.status) {

                var today = new Date(date);
                var day = 1000 * 60 * 60 * 24;

                var map = Highcharts.map;
                var dateFormat = Highcharts.dateFormat;
                var series = [];
                var series2 = [];
                var machines = [];

                today.setUTCHours(0);
                today.setUTCMinutes(0);
                today.setUTCSeconds(0);
                today.setUTCMilliseconds(0);
                today = today.getTime();


                for (var i = 0; i < result.data_op.length; i++) {

                    var deal = [];

                    var unfilled = true;



                    for (var j = 0; j < result.data_all.length; j++) {
                        if (result.data_all[j].end_job == null) {} else {
                            var to = new Date(result.data_all[j].dt);
                            to = to.addDays(1);

                            if (result.data_op[i].employee_id == result.data_all[j].employee_id && result
                                .data_all[j].status == "idle") {

                               var st = "";

                           if(result.data_all[j].list_job == "Istirahat"){
                            st = result.data_all[j].list_job;
                        }else{
                            st = '-';
                        }
                        unfilled = false;
                        deal.push({
                            mc_name: '-',
                            wjo: result.data_op[i].employee_name,
                            status: "idle",
                            job: st,
                            from: Date.parse(result.data_all[j].dt),
                            to: Date.parse(result.data_all[j].end_job),
                            load_time: parseFloat(parseFloat(result.data_all[j].time).toFixed(2)),
                            color: 'rgb(244,91,91)',


                        });
                    }

                }
            }

            for (var k = 0; k < result.data_all.length; k++) {
                if (result.data_all[k].end_job == null) {

                } else {
                    var to = new Date(result.data_all[k].dt);
                    to = to.addDays(1);

                    if (result.data_op[i].employee_id == result.data_all[k].employee_id && result
                        .data_all[k].status == "Area GS 1") {
                        unfilled = false;
                    deal.push({
                        mc_name: result.data_all[k].employee_name,
                        wjo: result.data_op[i].employee_name,
                        status: "Job Area GS 1",
                        job: result.data_all[k].list_job,
                        from: Date.parse(result.data_all[k].dt),
                        to: Date.parse(result.data_all[k].end_job),
                        load_time: parseFloat(parseFloat(result.data_all[k].time).toFixed(2)),
                        color: 'rgb(68,168,169)'
                    });
                }
            }

        }


        for (var n = 0; n < result.data_all.length; n++) {
            if (result.data_all[n].end_job == null) {

            } else {
                var to = new Date(result.data_all[n].dt);
                to = to.addDays(1);
                if (result.data_op[i].employee_id == result.data_all[n].employee_id && result
                    .data_all[n].status == "Area GS 2") {
                    unfilled = false;
                deal.push({
                    mc_name: result.data_all[n].employee_name,
                    wjo: result.data_op[i].employee_name,
                    status: "Job Area GS 2",
                    job: result.data_all[n].list_job,
                    from: Date.parse(result.data_all[n].dt),
                    to: Date.parse(result.data_all[n].end_job),
                    load_time: parseFloat(parseFloat(result.data_all[n].time).toFixed(2)),
                    color: 'rgb(169,255,151)'
                });
            }
        }

    }

    for (var o = 0; o < result.data_all.length; o++) {
        if (result.data_all[o].end_job == null) {

        } else {
            var to = new Date(result.data_all[o].dt);
            to = to.addDays(1);
            if (result.data_op[i].employee_id == result.data_all[o].employee_id && result
                .data_all[o].status == "Area GS 3") {
                unfilled = false;
            deal.push({
                mc_name: result.data_all[o].employee_name,
                wjo: result.data_op[i].employee_name,
                status: "Job Area GS 3",
                job: result.data_all[o].list_job,
                from: Date.parse(result.data_all[o].dt),
                to: Date.parse(result.data_all[o].end_job),
                load_time: parseFloat(parseFloat(result.data_all[o].time).toFixed(2)),
                color: 'rgb(99, 150, 89)'
            });
        }
    }

}
for (var p = 0; p < result.data_all.length; p++) {
    if (result.data_all[p].end_job == null) {

    } else {
        var to = new Date(result.data_all[p].dt);
        to = to.addDays(1);
        if (result.data_op[i].employee_id == result.data_all[p].employee_id && result
            .data_all[p].status == "Lain-Lain") {
            unfilled = false;
        deal.push({
            mc_name: '-',
            wjo: result.data_op[i].employee_name,
            status: "Lain-Lain",
            job: result.data_all[p].list_job,
            from: Date.parse(result.data_all[p].dt),
            to: Date.parse(result.data_all[p].end_job),
            load_time: parseFloat(parseFloat(result.data_all[p].time).toFixed(2)),
            color: 'rgb(119,152,191)'
        });
    }
}

}

if (unfilled) {
    deal.push({
        wjo: 0
    });
}


machines.push({
    name: result.data_op[i].employee_name,
    current: 0,
    deals: deal
});
}


series = machines.map(function(value, i) {
    var data = value.deals.map(function(deal) {
        return {
            id: 'deal-' + i,
            wjo: deal.wjo,
            mc_name: deal.mc_name,
            status: deal.status,
            job: deal.job,
            start: deal.from,
            end: deal.to,
            loads: deal.load_time,
            color: deal.color,
            borderColor: '#ffffff',
            borderRadius: 3,
            borderWidth: 1,
            y: i
        };
    });
    return {
        name: result.data_op[i].employee_name,
        data: data,
        current: value.deals[value.current]
    };
});


Highcharts.ganttChart('container', {
    series: series,
    chart: {
        events: {
          load: function() {
            const chart = this,
            series = chart.series,
            min = series[series.length -1].processedXData[0],
            max = today + 1 * day;

            chart.xAxis[0].setExtremes(min, max)
        }
    }
},
title: {
    text: 'General Service Productivity Actuals',
    style: {
        fontSize: '24px',
        fontWeight: 'bold'
    }
},
tooltip: {
    pointFormat: 'Status: {point.status}</span><br/><span>Job: {point.job}</span><br/><span>From: {point.start:%e %b %Y, %H:%M}</span><br/><span>To: {point.end:%e %b %Y, %H:%M}</span><br/><span>Load Time: {point.loads}</span>'
},
colors: ['#ffffff'],
xAxis: {
    type: 'datetime',
    tickInterval: day / 24,
    labels: {
        format: '{value:%H}'
    },
    currentDateIndicator: {
        enabled: true,
        color: '#fff',
        label: {
            style: {
                fontSize: '14px',
                color: '#FFB300',
                fontWeight: 'bold'
            }
        }
    },
    scrollbar: {
        enabled: true,
        barBackgroundColor: 'gray',
        barBorderRadius: 6,
        barBorderWidth: 0,
        buttonBackgroundColor: 'gray',
        buttonBorderWidth: 0,
        buttonArrowColor: 'white',
        buttonBorderRadius: 6,
        rifleColor: 'white',
        trackBackgroundColor: 'black',
        trackBorderWidth: 1,
        trackBorderColor: 'silver',
        trackBorderRadius: 6
    },
    tickLength: 0
},
yAxis: {
    type: 'category',
    grid: {
        columns: [{
            title: {
                text: 'OPERATORS',
                style: {
                    fontSize: '18px',
                    fontWeight: 'bold'
                }
            },
            categories: map(series, function(s) {
                return s.name;
            }),
        }]
    }
},
plotOptions: {
    gantt: {
        animation: false,
    },

},
credits: {
    enabled: false
},
exporting: {
    enabled: false
}
});


$(document).scrollTop(position);

}

});
}
Date.prototype.addDays = function(days) {
    var date = new Date(this.valueOf());
    date.setDate(date.getDate() + days);
    return date;
}



</script>
@endsection
