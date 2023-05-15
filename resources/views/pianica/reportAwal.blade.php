@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tbody>tr>td{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table.table-bordered{
  border:1px solid black;
}
table.table-bordered > thead > tr > th{
  border:1px solid black;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(211,211,211);
  padding-top: 0;
  padding-bottom: 0;
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
     {{ $page }}
    <span class="text-purple"> 最初検査リポート</span>

     <div class="col-xs-2 input-group date pull-right ">
                  <div class="input-group-addon bg-blue">
                    <i class="fa fa-calendar  "></i>
                  </div>
                  <input type="text" onchange="ngTotal();" class="form-control pull-right" id="datefrom2" name="datefrom2">
                </div>
  </h1>
  <ol class="breadcrumb">
    <!-- <li><a onclick="addOP()" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li> -->
  </ol>
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
  <div class="row">

       <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div class="col-xs-6" id="container2">
            
          </div>

          <div class="col-xs-6" id="container3">
            
          </div>
        </div>
      </div>
    </div>

    
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div id="container">
            
          </div>
        </div>
      </div>
    </div>

   


  </div>
</section>



@stop

@section('scripts')
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
        <script src="{{ url("js/exporting.js")}}"></script>
        <script src="{{ url("js/export-data.js")}}"></script>
        <script src="{{ url("js/highcharts-3d.js")}}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  jQuery(document).ready(function() { 
    ngTotal();
    recall();
    
     $('body').toggleClass("sidebar-collapse");
    $('.select2').select2({
      dropdownAutoWidth : true,
      width: '100%',
    });

    $('#datefrom2').datepicker({
      autoclose: true, 
      format :'yyyy-mm-dd',
    });
  });

   function recall() {
            ngTotal();
            setTimeout(recall, 6000);
          }
  
  function ngTotal() {
    var datep = $('#datefrom2').val();    
    
    var data = {
      datep:datep,
      
    }
    $.get('{{ url("index/getKensaAwalALL") }}',data, function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){

                  var nglist = [];
                  var total = [];
                  var totallas = [];
                  var totalJenisNGA = 0;
                  var totalJenisNGAlas = 0;
                  
                  var totalCek = result.total[0].total;
                  var totalOK = result.total[0].total - result.total[0].ng;

                  var totalCeklas = result.totallas[0].total;
                  var totalOKlas = result.totallas[0].total - result.totallas[0].ng;

                  for (var i = 0; i < result.ng.length; i++) {    
                     totalJenisNGA += parseInt(result.ng[i].total);
                     totalJenisNGAlas += parseInt(result.nglas[i].total);
                    } 
                     var totalJenisNGC = totalJenisNGA - (totalCek - totalOK);
                     var PengurangC = Math.round( totalJenisNGC / 4);

                     var totalJenisNGClas = totalJenisNGAlas - (totalCeklas - totalOKlas);
                     var PengurangClas = Math.round( totalJenisNGClas / 4);

                    for (var i = 0; i < result.ng.length; i++) {                    
                     nglist.push(result.ng[i].ng_name);
                     total.push(parseInt(result.ng[i].total - PengurangC));
                     if (parseInt(result.nglas[i].total - PengurangClas) > 0) {
                     totallas.push(parseInt(result.nglas[i].total - PengurangClas));
                   }else{
                    totallas.push(parseInt(0));
                   }
                     
                    } 

                    var tgl ="";
                    var tgly ="";

                    if (result.tgly == "") {
                      tgly = " No Data";
                    }else{
                      tgly =result.tgly[0].tgl;
                    }

                    if (result.tgl == "") {
                      tgl = " No Data";
                    }else{
                      tgl =result.tgl[0].tgl;
                    }



                    
    Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TOTAL NG KENSA AWAL'
    },
    subtitle: {
        text: 'Last Update '+tgl
    },
    xAxis: {
        categories: nglist
    },
    yAxis: {
        min: 0,
        title: {
            text: 'TOTAL NG'
        }
    },
    
    tooltip: {
        shared: false
    },
    plotOptions: {
        column: {
            grouping: false,
            shadow: false,
            borderWidth: 0,
             dataLabels: {
                enabled: true
            }
        }

    },
    credits:{
                enabled:false,
              },
    series: [{
      animation: false,
        name: 'Total 3 Month Before',
        color: 'rgba(165,170,217,1)',
        data: totallas,
        pointPadding: 0.3,
        // pointPlacement: -0.3
    }, {
      animation: false,
        name: 'Total today',
        color: 'rgba(126,86,134,.9)',
        data: total,
        pointPadding: 0.4,
        // pointPlacement: -0.3
    }

    ]
});


    //pie 1
    Highcharts.chart('container2', {
              chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                // options3d: {
                //   enabled: true,
                //   alpha: 45,
                //   beta: 0
                // }
              },
              title: {
                text: 'TOTAL NG RATE KENSA AWAL YESTERDAY'
              },
              subtitle: {
        text: 'Total 3 Month Before '+tgl
    },
              tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
              },
              plotOptions: {
                pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  depth: 35,
                  dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                    style: {
                      fontSize: '1vw'
                    },
                    distance: -50,
                    filter: {
                      property: 'percentage',
                      operator: '>',
                      value: 4
                    }
                  },
                  showInLegend: false
                }
              },
              credits:{
                enabled:false,
              },
              exporting:{
                enabled:false,
              },

              series: [{

                animation: false,
                name: 'Percentage',
                colorByPoint: true,
                data: [{
                  name: 'Biri',
                  y: result.nglas[0].total - PengurangClas
                  // y: 1                 
                },
                {
                  name: 'Oktaf',
                  y: result.nglas[1].total - PengurangClas
                  // y: 1                 
                },
                {
                  name: 'T. Rendah',
                  y: result.nglas[2].total - PengurangClas
                  // y: 1                 
                }
                ,
                {
                  name: 'T. Tinggi',
                  y: result.nglas[3].total - PengurangClas
                  // y: 1                 
                }]
              }]
            });


    //pie 2
    Highcharts.chart('container3', {
              chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                // options3d: {
                //   enabled: true,
                //   alpha: 45,
                //   beta: 0
                // }
              },
              title: {
                text: 'TOTAL NG RATE KENSA AWAL TODAY'
              },
              subtitle: {
        text: 'Last Update'+tgl
    },
              tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
              },
              plotOptions: {
                pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  depth: 35,
                  dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b><br>{point.percentage:.1f} %',
                    style: {
                      fontSize: '1vw'
                    },
                    distance: -50,
                    filter: {
                      property: 'percentage',
                      operator: '>',
                      value: 4
                    }
                  },
                  showInLegend: false
                }
              },
              credits:{
                enabled:false,
              },
              exporting:{
                enabled:false,
              },

              series: [{

                animation: false,
                name: 'Percentage',
                colorByPoint: true,
                data: [{
                  name: 'Biri',
                  y: result.ng[0].total - PengurangC                 
                },
                {
                  name: 'Oktaf',
                  y: result.ng[1].total - PengurangC                 
                },
                {
                  name: 'T. Rendah',
                  y: result.ng[2].total - PengurangC                 
                }
                ,
                {
                  name: 'T. Tinggi',
                  y: result.ng[3].total - PengurangC                 
                }]
              }]
            });
    }
              else{                
                // openErrorGritter('Error!', result.message);
              }
            }
            else{

              alert("Disconnected from server");
            }
          });
  }


</script>

@stop