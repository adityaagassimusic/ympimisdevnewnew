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
     {{ $page }}s
    <small>it all starts here</small>
  </h1>
  <ol class="breadcrumb">
    <li><a onclick="addOP()" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a></li>
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
          <div id="container">
            
          </div>
        </div>
      </div>
    </div>

      <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div id="container2">
            
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
    ngMesin();
     $('body').toggleClass("sidebar-collapse");
    $('.select2').select2({
      dropdownAutoWidth : true,
      width: '100%',
    });
  });
  
  function ngTotal() {
    $.get('{{ url("index/getTotalNG") }}', function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){

                  var nglist = [];
                  var total = [];
                  var totalH = [];
                  var totalL = [];

                  var total2 = [];
                  var totalH2 = [];
                  var totalL2 = [];
                 
                  for (var i = 0; i < result.ng.length; i++) {                    
                     nglist.push(result.ng[i].ngH);
                     total.push(parseInt(result.ng[i].total));
                     totalH.push(parseInt(result.ngH[i].totalH));
                     totalL.push(parseInt(result.ngL[i].totalL));

                     total2.push(parseInt(result.ng[i].total2)-1);
                     totalH2.push(parseInt(result.ngH[i].totalH2)-1);
                     totalL2.push(parseInt(result.ngL[i].totalL2)-1);
                    } 


    Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TOTAL NG RATE SPOT WELDING'
    },
    subtitle: {
        text: 'Last Update'
    },
    xAxis: {
        categories: nglist,
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'NG TOTAL'
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0,
            dataLabels: {
                enabled: true
            } 
        },

    },
    series: [{
        name: 'Total',
        data: total

    }, {
        name: 'High',
        data: totalH

    }, {
        name: 'Low',
        data: totalL

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


function ngMesin() {
    $.get('{{ url("index/getMesinNg") }}', function(result, status, xhr){
              console.log(status);
              console.log(result);
              console.log(xhr);
              if(xhr.status == 200){
                if(result.status){

                  var nglist = [];                 
                 
                  for (var i = 0; i < result.ng.length; i++) {                    
                     nglist.push(result.ng[i].ng);
                     
                    } 


    Highcharts.chart('container2', {
    chart: {
        type: 'column'
    },
    title: {
        text: 'TOTAL NG RATE MESIN SPOT WELDING'
    },
    subtitle: {
        text: 'Last Update'
    },
    xAxis: {
        categories: [
          'Mesin 1',
          'Mesin 2',
          'Mesin 3',
          'Mesin 4',
          'Mesin 5',
          'Mesin 6',
        ],
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'NG TOTAL'
        }
    },

    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} </b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0,
            dataLabels: {
                enabled: true
            } 
        },

    },
    series: [{
        name: 'Total',
        data: nglist

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