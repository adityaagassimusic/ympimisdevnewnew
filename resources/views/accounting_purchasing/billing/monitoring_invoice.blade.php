@extends('layouts.display')
@section('stylesheets')
    <style type="text/css">
        #loading {
            display: none;
        }
    </style>
@stop
@section('header')
@endsection
@section('content')
    <section class="content" style="padding-top: 0;">
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
            <div class="col-xs-3" style="padding-top: 0.25%;">
                <div class="input-group date">
                    <div class="input-group-addon" style="background-color: #ccff90;">
                        <i class="fa fa-calendar-o"></i>
                    </div>
                    <select class="form-control select2" name="vendor" id='vendor' data-placeholder="Select Vendor" style="width: 100%;" onchange="fetchChart()">
                        <option value="">Select Vendor</option>
                        @foreach($vendor as $ven)
                        <option value="{{$ven->vendor_code}}">{{$ven->vendor_code}} - {{$ven->supplier_name}}</option>
                        @endforeach
                    </select>
                    <!-- <input type="text" onchange="fetchChart()" class="form-control" name="vendor"
                        id="vendor" placeholder="Select Vendor"> -->
                </div>
            </div>

            <div id="chart_title" class="col-xs-9" style="background-color: #ccff90;">
                <center>
                    <span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span>
                </center>
            </div>
            
            <div class="col-xs-12" id="chart1" style="margin-top: 1%; height: 80vh;"></div>
        </div>


        <div class="modal fade" id="modalDetail" style="color: black;z-index: 10000;">
          <div class="modal-dialog modal-lg" style="width: 1200px">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_detail"></h4>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-12" id="data-activity">
                    <table id="tableDetail" class="table table-striped table-bordered" style="width: 100%;">
                        <thead>
                            <tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">
                                <th style="padding: 5px;text-align: center;width: 1%">Posting Date</th>
                                <th style="padding: 5px;text-align: center;width: 3%">Surat Jalan</th>
                                <th style="padding: 5px;text-align: center;width: 5%">Supplier</th>
                                <th style="padding: 5px;text-align: center;width: 1%">No Invoice</th>
                            </tr>
                        </thead>
                        <tbody id="bodyTableDetail">
                            
                        </tbody>
                    </table>
                  </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            </div>
          </div>
        </div>
      </div>
    </section>

@endsection
@section('scripts')
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/highcharts-3d.js') }}"></script>
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
            fetchChart();

            $('.select2').select2({
                allowClear : true,
            });

        });

        var plan = [];
        var actual = [];
        var data_invoice = [];

        function fetchChart() {
            var id = "{{$id}}";
            var vendor = $('#vendor').val();

            var data = {
                id:id,
                vendor: vendor
            }

            $('#loading').show();

            $.get('{{ url("fetch/tanda_terima/monitoring") }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#title_text').text('MONITORING SURAT JALAN');
                    var h = $('#chart_title').height();
                    $('.select').css('height', h);

                    var xCategories = [];
                    var arr_invoice = [];
                    var arr_act_invoice = [];
                    var temp = [];
                    var resumePlan = [];


                    data_invoice = [];

                    for (let i = 0; i < result.all_invoice.length; i++) {

                        var key = result.all_invoice[i].bulan;
                        var oustanding = 0;
                        var actual = 0;
                        var status = 0;
                        for (let j = 0; j < result.actual_invoice.length; j++) {
                            var all_inv = result.all_invoice[i].doc_text;
                            var acc_inv = result.actual_invoice[j].surat_jalan;
                            // var re = new RegExp(result.all_invoice[i].doc_text, 'g');
                            if (acc_inv != null && acc_inv != "" && all_inv != null && all_inv != "") {
                                var acc_inv = result.actual_invoice[j].surat_jalan.split(',');
                                if (acc_inv.includes(all_inv)) {
                                    actual++;

                                    data_invoice.push({
                                        doc_text:result.all_invoice[i].doc_text,
                                        post_date:result.all_invoice[i].post_date,
                                        supplier_code:result.actual_invoice[j].supplier_code,
                                        supplier_name:result.actual_invoice[j].supplier_name,
                                        bulan:result.all_invoice[i].bulan,
                                        no_invoice:result.actual_invoice[j].invoice_no,
                                        'status' : 'ok'
                                    });
                                    status = 1;
                                }
                            }

                        }

                        if (status == 0) {
                                oustanding++;

                                data_invoice.push({
                                    doc_text:result.all_invoice[i].doc_text,
                                    post_date:result.all_invoice[i].post_date,
                                    supplier_code:result.all_invoice[i].supplier_code,
                                    supplier_name:result.all_invoice[i].supplier_name,
                                    bulan:result.all_invoice[i].bulan,
                                    no_invoice:'-',
                                    'status' : 'not_ok'
                                });
                           
                        }

                        if (!temp[key]) {
                            temp[key] = {
                                'bulan': result.all_invoice[i].bulan,
                                'outstanding': oustanding,
                                'actual': actual,
                            };
                        } else {
                            temp[key].outstanding = temp[key].outstanding + oustanding;
                            temp[key].actual = temp[key].actual + actual;
                        }

                    }

                    for (let key in temp) {
                        resumePlan.push(temp[key]);
                    }

                    // arr_invoice.push(parseInt(oustanding_invoice));
                    // arr_act_invoice.push(parseInt(actual_invoice));

                    for (let j = 0; j < resumePlan.length; j++) {
                        xCategories.push(resumePlan[j].bulan);
                        arr_invoice.push(resumePlan[j].outstanding);
                        arr_act_invoice.push(resumePlan[j].actual);
                    }


                    // END RESUME

                    Highcharts.chart('chart1', {
                        chart: {
                            type: 'column',
                            backgroundColor: null
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true
                        },
                        xAxis: {
                            categories: xCategories,
                            gridLineWidth: 1,
                            labels: {
                                style: {
                                    color: '#ffffff;'
                                },
                            },
                        },
                        yAxis: {
                            title: {
                                text: 'Count Item(s)',
                                style: {
                                    color: '#ffffff;'
                                }
                            },
                            labels: {
                                style: {
                                    color: '#ffffff;'
                                },
                            },
                            gridLineWidth: 0,
                        },
                        tooltip: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                pointPadding: 0.05,
                                groupPadding: 0.1,
                                borderWidth: 0
                            },
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y:.0f}',
                                    style: {
                                        fontSize: '12px;'
                                    }
                                },
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function(event) {
                                            ShowModal(this.category,this.series.name);
                                        }
                                    }
                                },
                            },
                        },
                        series: [{
                            name: 'Sudah Tanda Terima',
                            data: arr_act_invoice,
                            color: '#c2ffb0',
                            stack: 'actual'
                        },
                        {
                            name: 'Oustanding Surat Jalan',
                            data: arr_invoice,
                            color: '#ff6666',
                            stack: 'actual',
                        }]
                    });
                    $('#loading').hide();

                } else {
                    $('#loading').hide();
                    alert('Attempt to retrieve data failed');
                }
            });
        }


        function ShowModal(bulan,status) {
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();

            $('#loading').show();
            $('#bodyTableDetail').html('');
            var tableDetail = '';

            var vendor = $('#vendor').val();


            for(var i = 0; i < data_invoice.length;i++){

                if (data_invoice[i].bulan === bulan) {
                    var stat = "";

                    if (status == "Sudah Tanda Terima") {
                        stat = 'ok';
                    }else if(status == "Oustanding Surat Jalan") {
                        stat = 'not_ok';
                    }

                    if (data_invoice[i].status === stat) {
                        if (vendor == "" || vendor == null) {
                            tableDetail += '<tr>';
                            tableDetail += '<td width="10%">'+data_invoice[i].post_date+'</td>';
                            tableDetail += '<td width="30%">'+data_invoice[i].doc_text+'</td>';
                            tableDetail += '<td width="40%">'+data_invoice[i].supplier_code+' - '+data_invoice[i].supplier_name+'</td>';
                            tableDetail += '<td width="10%">'+data_invoice[i].no_invoice+'</td>';
                            tableDetail += '</tr>';
                        }else{
                            if (vendor == data_invoice[i].supplier_code) {
                                tableDetail += '<tr>';
                                tableDetail += '<td width="10%">'+data_invoice[i].post_date+'</td>';
                                tableDetail += '<td width="30%">'+data_invoice[i].doc_text+'</td>';
                                tableDetail += '<td width="40%">'+data_invoice[i].supplier_code+' - '+data_invoice[i].supplier_name+'</td>';
                                tableDetail += '<td width="10%">'+data_invoice[i].no_invoice+'</td>';
                                tableDetail += '</tr>';
                            }
                        }
                    }
                }
            }
            $('#bodyTableDetail').append(tableDetail);
            $('#tableDetail').DataTable({
              'dom': 'Bfrtip',
              'responsive':true,
              'lengthMenu': [
              [ 10, 25, 50, -1 ],
              [ '10 rows', '25 rows', '50 rows', 'Show all' ]
              ],
              'buttons': {
                buttons:[
                {
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
              "processing": true
            });

            $('#judul_detail').html('Detail '+status+' pada Bulan '+bulan);
            $('#modalDetail').modal('show');
            $('#loading').hide();
        }

        Highcharts.createElement('link', {
            href: "{{ url('fonts/UnicaOne.css') }}",
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
