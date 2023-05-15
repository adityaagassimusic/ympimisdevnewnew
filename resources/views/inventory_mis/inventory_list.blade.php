@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
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
          vertical-align: middle;
     }
     table.table-bordered > tbody > tr > td{
          border:1px solid black;
          vertical-align: middle;
     }
     table.table-bordered > tfoot > tr > th{
          border:1px solid black;
          vertical-align: middle;
     }
     #loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
     <h1>
          {{ $title }}
          <small><span class="text-purple"> {{ $title_jp }}</span></small>
          <div class="form-group pull-right">
               <a href="{{ url("index/cart_check/inventory_mis")}}" style="font-size: 20px; color: white; line-height: 20px !important; font-weight: bold" class="btn btn-success btn-md"><i class="fa fa-shopping-cart"></i> <span class="badge badge-light" id="countList">0</span></a>
          </div>
     </h1>
     <ol class="breadcrumb">
          <li>
          </li>
     </ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
     <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
          <p style="position: absolute; color: White; top: 45%; left: 35%;">
               <span style="font-size: 40px">Uploading, Please Wait . . . <i class="fa fa-spin fa-refresh"></i></span>
          </p>
     </div>
     <div class="row">
      <div class="col-xs-12">
          <div class="box box-solid">
               <div class="box-header">
               </div>
               <div class="box-body" style="padding-top: 0;">
                <div class="col-xs-12"  style="padding-top: 20px">
                    <div class="col-xs-10" style="padding-right: 0;padding-left: 0px;">
                         <div id="container" style="width: 100%; height: 50vh; margin-bottom: 10px; border: 1px solid black;"></div>
                    </div>

                    <div class="col-xs-2" style="padding-right: 0px;height: 50vh;">
                         <p class="text-center">
                              <strong>Resume MIS Inventory</strong>
                         </p>
                         <div class="progress-group">
                              <span class="progress-text">Total Ipad</span>
                              <span class="progress-number"><b><span id="totalProgressIpad"></span></b>/<span id="totalIpad"></span></span>
                              <div class="progress sm" id="progress-ipad">
                              </div>
                         </div>
                         <div class="progress-group">
                              <span class="progress-text">Total Computer</span>
                              <span class="progress-number"><b><span id="totalProgressComputer"></span></b>/<span id="totalComputer"></span></span>
                              <div class="progress sm" id="progress-computer">
                              </div>
                         </div>

                         <div class="progress-group">
                              <span class="progress-text">Total Mini PC</span>
                              <span class="progress-number"><b><span id="totalProgressMiniPc"></span></b>/<span id="totalMiniPc"> </span></span>
                              <div class="progress sm" id="progress-minipc">
                              </div>
                         </div>
                         <div class="progress-group">
                              <span class="progress-text">Total Laptop</span>
                              <span class="progress-number"><b><span id="totalProgressLaptop"></span></b>/<span id="totalLaptop"> </span></span>
                              <div class="progress sm" id="progress-laptop">
                              </div>
                         </div>
                         <div class="progress-group">
                              <span class="progress-text">Total Barang Lain-Lain</span>
                              <span class="progress-number"><b><span id="totalProgressLain"></span></b>/<span id="totalLain"> </span></span>
                              <div class="progress sm" id="progress-lain">
                              </div>
                         </div>
                         <div class="progress-group">
                              <a href="{{ url('index/history/report/mis')}}"  class="btn btn-primary" style="color:white">
                                   &nbsp;<i class="fa fa-sign-in"></i>&nbsp;&nbsp;&nbsp;RESUME
                              </a>
                         </div>
                    </div>
               </div>
          </div>
     </div>
</div>
<div class="col-xs-12">
     <div class="box box-solid">
          <div class="box-header">
          </div>
          <div class="box-body" style="padding-top: 0;">
               <table id="misTable" class="table table-bordered table-striped table-hover">
                    <thead style="background-color: rgba(126,86,134,.7);">
                         <tr>
                              <th style="width: 1%;color:white;">#</th>
                              <th style="width: 1%;color:white;">Tanggal Kedatangan</th>
                              <th style="width: 1%;color:white;">Tanggal Penerimaan</th>
                              <th style="width: 1%;color:white;">No PO</th>
                              <th style="width: 1%;color:white;">Kategori</th>
                              <th style="width: 5%;color:white;">Deskripsi</th>
                              <th style="width: 1%;color:white;">Jumlah</th>
                              <th style="width: 3%;color:white;">Peruntukan</th>
                              <th style="width: 1%;color:white;">Status</th>
                              <th style="width: 1%;color:white;">Action</th>
                         </tr>
                    </thead>
                    <tbody id="misTableBody">
                    </tbody>
                    <tfoot>
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
                    </tfoot>
               </table>

               <div style="background-color: #00a65a; color: white; padding: 5px; text-align: center; margin-bottom: 8px">
                    <span style="font-weight: bold; font-size: 20px">INVENTORY COMPLETE</span>
               </div>
               <div>
                    <table id="misTableCompleted" class="table table-bordered table-striped table-hover">
                         <thead style="background-color: #3c8dbc;">
                              <tr>
                                   <th style="width: 1%;color:white;">#</th>
                                   <th style="width: 1%;color:white;">Kode ID</th>
                                   <th style="width: 1%;color:white;">Tanggal Kedatangan</th>
                                   <th style="width: 1%;color:white;">Tanggal Penerimaan</th>
                                   <th style="width: 1%;color:white;">No PO</th>
                                   <th style="width: 1%;color:white;">Kategori</th>
                                   <th style="width: 5%;color:white;">Deskripsi</th>
                                   <th style="width: 1%;color:white;">Lokasi</th>
                                   <th style="width: 1%;color:white;">Jumlah</th>
                                   <th style="width: 1%;color:white;">PIC Pengambil</th>
                                   <th style="width: 3%;color:white;">Peruntukan</th>
                                   <th style="width: 1%;color:white;">Status</th>
                              </tr>
                         </thead>
                         <tbody id="misTableBodyCompleted">
                         </tbody>
                         <tfoot>
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
                         </tfoot>
                    </table>
               </div>
          </div>
     </div>
</div>

</div>
</section>

<div class="modal fade" id="modalDetail" style="color: black;z-index: 10000;">
 <div class="modal-dialog modal-md" style="width: 1200px">
   <div class="modal-content">
     <div class="modal-header">
       <h4 class="modal-title" style="text-transform: uppercase; text-align: center;font-weight: bold;" id="judul_detail"></h4>
  </div>
  <div class="modal-body">
       <div class="row" id="divTable4">
       </div>
  </div>
  <div class="modal-footer">
       <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
  </div>
</div>
</div>
</div> 

@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/highcharts.js') }}"></script>
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('js/buttons.flash.min.js') }}"></script>
<script src="{{ url('js/jszip.min.js') }}"></script>
<script src="{{ url('js/vfs_fonts.js') }}"></script>
<script src="{{ url('js/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/buttons.print.min.js') }}"></script>
<script src="{{ url('js/icheck.min.js') }}"></script>


<script>
     $.ajaxSetup({
          headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
     });


     var data_inventory = [];
     var data_complete = [];
     var datas = [];


     jQuery(document).ready(function() {
          $('body').toggleClass("sidebar-collapse");
          get_inv();
          fetchMonitoring();

     });

     $('.select2').select2({
          dropdownAutoWidth : true,
          dropdownParent: $("#modalCreate"),
          allowClear:true,
          tags: true
     });

     $('.select3').select2({
          dropdownAutoWidth : true,
          dropdownParent: $("#updateModal"),
          allowClear:true,
          tags: true
     });

     function selectBarang(ids,dates,no_po,cat,nama_item,qtys,peruntukan,date_to){
          if (confirm('Apakah Anda yakin akan manambahkan di keranjang?')) {
              var data = {
               ids:ids,
               dates : dates,
               no_po : no_po,
               cat : cat,
               nama_item : nama_item,
               qtys : qtys,
               peruntukan : peruntukan,
               date_to : date_to
          }

          $.post('{{ url("update/inventory/mis") }}', data, function(result, status, xhr) {
               openSuccessGritter('Success','New Item Added');
               get_inv();

          })
     }
}


function fetchMonitoring(){

     var date_from = $('#date_from').val();
     var date_to = $('#date_to').val();

     var data = {
      date_from: date_from,
      date_to: date_to
 };

 $.get('{{ url("fetch/grafik/inventory/mis") }}', data, function(result, status, xhr){
     if(result.status){

          var bulan = [];
          var tahun = [];
          var total_ipad = [];
          var total_computer = [];
          var total_minipc = [];
          var total_laptop = [];
          var total_lain = [];

          var totalIpads = 0;
          var totalIpadClose = 0;
          var totalComputers = 0;
          var totalComputerClose = 0;
          var totalMiniPc = 0;
          var totalMiniPcClose = 0;
          var totalLaptop = 0;
          var totalLaptopClose = 0;
          var totalLain = 0;
          var totalLainClose = 0;

          datas = [];
          datas = result.data_mis;

          $.each(result.month_data, function(key, value) {
               bulan.push(value.bulan);
               tahun.push(value.tahun);

               total_ipad.push({y: parseInt(value.total_ipad),key:value.tahun});
               totalIpads += parseInt(value.total_ipad);
               totalIpadClose += parseInt(value.total_ipad_close);
               persen_progress_ipad = parseFloat(totalIpadClose) / parseFloat(totalIpads) * 100;  
               $('#totalIpad').text(totalIpads);
               $('#totalProgressIpad').text(totalIpadClose);
               $('#progress-ipad').html('<div class="progress-bar progress-bar-succes" style="width: '+persen_progress_ipad+'%"></div>');

               total_computer.push({y: parseInt(value.total_computer),key:value.tahun});
               totalComputers += parseInt(value.total_computer);
               totalComputerClose += parseInt(value.total_computer_close);
               persen_progress_computer = parseFloat(totalComputerClose) / parseFloat(totalComputers) * 100;  
               $('#totalComputer').text(totalComputers);
               $('#totalProgressComputer').text(totalComputerClose);
               $('#progress-computer').html('<div class="progress-bar progress-bar-green" style="width: '+persen_progress_computer+'%"></div>');

               total_minipc.push({y: parseInt(value.total_mini_pc),key:value.tahun});
               totalMiniPc += parseInt(value.total_mini_pc);
               totalMiniPcClose += parseInt(value.total_mini_pc_close);
               persen_progress_minipc = parseFloat(totalMiniPcClose) / parseFloat(totalMiniPc) * 100;  
               $('#totalMiniPc').text(totalMiniPc);
               $('#totalProgressMiniPc').text(totalMiniPcClose);
               $('#progress-minipc').html('<div class="progress-bar progress-bar-info" style="width: '+persen_progress_minipc+'%"></div>');

               total_laptop.push({y: parseInt(value.total_laptop),key:value.tahun});
               totalLaptop += parseInt(value.total_laptop);
               totalLaptopClose += parseInt(value.total_laptop_close);
               persen_progress_laptop = parseFloat(totalLaptopClose) / parseFloat(totalLaptop) * 100;  
               $('#totalLaptop').text(totalLaptop);
               $('#totalProgressLaptop').text(totalLaptopClose);
               $('#progress-laptop').html('<div class="progress-bar progress-bar-warning" style="width: '+persen_progress_laptop+'%"></div>');

               total_lain.push({y: parseInt(value.total_lain),key:value.tahun});
               totalLain += parseInt(value.total_lain);
               totalLainClose += parseInt(value.total_lain_close);
               persen_progress_lain = parseFloat(totalLainClose) / parseFloat(totalLain) * 100;  
               $('#totalLain').text(totalLain);
               $('#totalProgressLain').text(totalLainClose);
               $('#progress-lain').html('<div class="progress-bar" style="background-color:#eab676; width: '+persen_progress_lain+'%"></div>');

          });


          $('#container').highcharts({
               chart: {
                 type: 'column',
                 backgroundColor: null
            },
            title: {
                 text: "Outstanding Tickets By Month",
            },
            xAxis: {
                 type: 'category',
                 categories: bulan,
                 lineWidth:2,
                 lineColor:'#000',
                 gridLineWidth: 1,
                 labels: {
                   formatter: function (e) {
                     return ''+ this.value +' '+tahun[(this.pos)];
                }
           }
      },
      yAxis: {
       lineWidth:2,
       lineColor:'#000',
       type: 'linear',
       title: {
         text: 'Total Ticket'
    },
    stackLabels: {
         enabled: true,
         style: {
           fontWeight: 'bold',
           color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
      }
 }
},
legend: {
  itemStyle:{
    color: "black",
    fontSize: "12px",
    fontWeight: "bold",

}
},
plotOptions: {
  series: {
    cursor: 'pointer',
    point: {
      events: {
        click: function () {
          ShowModalBulan(this.category,this.series.name,this.key);
     }
}
},
dataLabels: {
 enabled: false,
 format: '{point.y}'
}
},
column: {
    color:  Highcharts.ColorString,
    stacking: 'normal',
    pointPadding: 0.93,
    groupPadding: 0.93,
    borderWidth: 1,
    dataLabels: {
      enabled: true
 }
}
},
credits: {
  enabled: false
},

tooltip: {
  formatter:function(){
    return this.series.name+' : ' + this.y;
}
},
series: [
{
  name: 'Total iPad',
  data: total_ipad,
  color: '#337ab7'
},
{
  name: 'Total Computer',
  data: total_computer,
  color: '#00a65a'
},
{
  name: 'Total Mini PC',
  data: total_minipc,
  color: '#00c0ef'
},
{
  name: 'Total Laptop',
  data: total_laptop,
  color: '#f39c12'
},
{
  name: 'Total Lain-Lain',
  data: total_lain,
  color: '#eab676'
}
]
})
     }
     else{
          alert('Unidentified Error '+result.message);
          audio_error.play();
          return false;
     }
});
}


function get_inv(){

     $.get('{{ url("fetch/inventory_mis/list") }}', function(result, status, xhr) {

          $('#misTable').DataTable().clear();
          $('#misTable').DataTable().destroy();
          $("#misTableBody").empty();
          var body = '';

          $('#countList').html(result.jum[0].jums);
          data_complete = [];

          data_complete.push(result.data_compl);

          data_inventory.push(result.inventory);

          $.each(result.inventory, function(index, value){
               body += "<tr>";
               body += "<td>"+(index+1)+"</td>";
               body += "<td>"+value.tanggal+"</td>";
               body += "<td>"+(value.tanggal_penerimaan || '-')+"</td>";
               body += "<td>"+(value.no_po || '-')+"</td>";
               body += "<td>"+(value.category || 'Other')+"</td>";
               body += "<td>"+(value.nama_item || '-')+"</td>";
               body += "<td>"+(value.qty || '-')+"</td>";
               body += "<td>"+(value.peruntukan || '-')+"</td>";

               if (value.status == 1) {
                    body += "<td><span data-toggle='tooltip' class='badge bg-orange' data-original-title='' title=''></i> Di keranjang</span></td>";
                    body += "<td><span data-toggle='tooltip' class='badge bg-orange' data-original-title='' title=''><i class='fa fa-fw fa-shopping-cart'></i></span></td>";


               }else if (value.status == 2 && value.remark == "sudah diambil") {
                    body += "<td><span data-toggle='tooltip' class='badge bg-green' data-original-title='' title=''>"+(value.remark || '-')+"</span></td>";

                    body += "<td><span data-toggle='tooltip' class='badge bg-green' data-original-title='' title=''><i class='fa fa-fw fa-check'></i></span></td>";
               }
               else{
                    body += "<td>"+(value.remark || '-')+"</td>";
                    body += "<td><button class='btn btn-primary btn-sm' onclick='selectBarang(\""+value.id+"\",\""+value.tanggal+"\",\""+value.no_po+"\",\""+value.category+"\",\""+value.nama_item+"\",\""+value.qty+"\",\""+value.peruntukan+"\",\""+value.date_to+"\")'>Pilih</button></td>";
               }

               body += "</tr>";
          })


          $("#misTableBody").append(body);


          $('#misTable tfoot th').each( function () {
               var title = $(this).text();
               $(this).html( '<input id="search" style="text-align: center;color:black; type="text" placeholder="Search '+title+'" size="10"/>' );
          } );


          var table = $('#misTable').DataTable({
               'dom': 'Bfrtip',
               'responsive':true,
               'lengthMenu': [
               [ 10, 25, 50, -1 ],
               [ '10 rows', '25 rows', '50 rows', 'Show all' ]
               ],
               'buttons': {
                    buttons:[
                    {
                         extend: 'excel',
                         className: 'btn btn-info',
                         text: '<i class="fa fa-file-excel-o"></i> Excel',
                         exportOptions: {
                              columns: ':not(.notexport)'
                         }
                    },
                    {
                         extend: 'pageLength',
                         className: 'btn btn-default',
                    },
                    ]
               },
               initComplete: function() {
                    this.api()
                    .columns([1,4])
                    .every(function(dd) {
                         var column = this;
                         var theadname = $("#misTable th").eq([dd])
                         .text();
                         var select = $(
                              '<select><option value="" style="font-size:11px; color:black;">All</option></select>'
                              )
                         .appendTo($(column.footer()).empty())
                         .on('change', function() {
                              var val = $.fn.dataTable.util
                              .escapeRegex($(this)
                                   .val());

                              column.search(val ? '^' + val + '$' :
                                   '', true,
                                   false)
                              .draw();
                         });
                         column
                         .data()
                         .unique()
                         .sort()
                         .each(function(d, j) {
                              var vals = d;
                              if ($("#misTable th").eq([dd])
                                   .text() ==
                                   'Category') {
                                   vals = d.split(' ')[0];
                         }
                         select.append(
                              '<option style="font-size:12px;"  value="' +
                              d + '">' + vals + '</option>');
                    });
                    });
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
          });

          table.columns().every( function () {
               var that = this;
               $( '#search', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                         that
                         .search( this.value )
                         .draw();
                    }
               } );
          } );

          $('#misTable tfoot tr').appendTo('#misTable thead');
          get_inv2();
     })

}
function get_inv2(){


     $('#misTableCompleted').DataTable().clear();
     $('#misTableCompleted').DataTable().destroy();
     $("#misTableBodyCompleted").empty();
     var body = '';

     $.each(data_complete[0], function(index, value){
          body += "<tr>";
          body += "<td>"+(index+1)+"</td>";
          body += "<td>"+(value.checklist_id || '-')+"</td>";
          body += "<td>"+value.date_to+"</td>";
          body += "<td>"+(value.receive_date || '-')+"</td>";
          body += "<td>"+(value.no_po || '-')+"</td>";
          body += "<td>"+(value.category || 'Other')+"</td>";
          body += "<td>"+(value.nama_item || '-')+"</td>";
          body += "<td>"+(value.location || '-')+"</td>";
          body += "<td>"+(value.qty || '-')+"</td>";
          body += "<td>"+(value.pic_pengambil_name || '-')+"</td>";
          body += "<td>"+(value.peruntukan || '-')+"</td>";
          body += "<td><span data-toggle='tooltip' class='badge bg-green' data-original-title='' title=''>"+(value.remark || '-')+"</span></td>";
          body += "</tr>";
     })


     $("#misTableBodyCompleted").append(body);


     $('#misTableCompleted tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input id="search" style="text-align: center;color:black; type="text" placeholder="Search '+title+'" size="10"/>' );
     } );


     var table = $('#misTableCompleted').DataTable({
          'dom': 'Bfrtip',
          'responsive':true,
          'lengthMenu': [
          [ 10, 25, 50, -1 ],
          [ '10 rows', '25 rows', '50 rows', 'Show all' ]
          ],
          'buttons': {
               buttons:[
               {
                    extend: 'excel',
                    className: 'btn btn-info',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    exportOptions: {
                         columns: ':not(.notexport)'
                    }
               },
               {
                    extend: 'pageLength',
                    className: 'btn btn-default',
               },
               ]
          },
          initComplete: function() {
               this.api()
               .columns([5])
               .every(function(dd) {
                    var column = this;
                    var theadname = $("#misTableCompleted th").eq([dd])
                    .text();
                    var select = $(
                         '<select><option value="" style="font-size:11px; color:black;">All</option></select>'
                         )
                    .appendTo($(column.footer()).empty())
                    .on('change', function() {
                         var val = $.fn.dataTable.util
                         .escapeRegex($(this)
                              .val());

                         column.search(val ? '^' + val + '$' :
                              '', true,
                              false)
                         .draw();
                    });
                    column
                    .data()
                    .unique()
                    .sort()
                    .each(function(d, j) {
                         var vals = d;
                         if ($("#misTableCompleted th").eq([dd])
                              .text() ==
                              'Category') {
                              vals = d.split(' ')[0];
                    }
                    select.append(
                         '<option style="font-size:12px;"  value="' +
                         d + '">' + vals + '</option>');
               });
               });
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
     });

     table.columns().every( function () {
          var that = this;
          $( '#search', this.footer() ).on( 'keyup change', function () {
               if ( that.search() !== this.value ) {
                    that
                    .search( this.value )
                    .draw();
               }
          } );
     } );

     $('#misTableCompleted tfoot tr').appendTo('#misTableCompleted thead');

}

function ShowModalBulan(kategori, status,st) {

     var barang = status.split(' ');

     $('#divTable4').html("");
     var tableData = "";
     tableData += "<div class='col-md-12'>";
     tableData += "<table id='tableDetail' class='table table-bordered table-striped table-hover'>";
     tableData += '<thead>';
     tableData += '<tr style="border-bottom:3px solid black;border-top:3px solid black;background-color:#cddc39">';
     tableData += '<th style="width:1%">Kode ID</th>';
     tableData += '<th style="width:1%">Tanggal Kedatangan</th>';
     tableData += '<th style="width:1%">Tanggal Penerimaan</th>';
     tableData += '<th style="width:1%">No PO</th>';
     tableData += '<th style="width:4%">Kategori</th>';
     tableData += '<th style="width:5%">Deskripsi</th>';
     tableData += '<th style="width:1%">Jumlah</th>';
     tableData += '<th style="width:1%">Status</th>';
     tableData += '</tr>';
     tableData += '</thead>';
     tableData += '<tbody id="bodyTableDetail">';
     tableData += "</tbody>";
     tableData += "<tfoot>";
     tableData += "<tr>";
     tableData += "<th></th>";
     tableData += "<th></th>";
     tableData += "<th></th>";
     tableData += "<th></th>";
     tableData += "<th></th>";
     tableData += "<th></th>";
     tableData += "<th></th>";
     tableData += "<th></th>";
     tableData += "</tr>";
     tableData += "</tfoot>";
     tableData += "</table>";
     tableData += "</div>";
     $('#divTable4').append(tableData);
     ShowModalAll(kategori,barang[1],st);
}

function ShowModalAll(kategori, status,st) { 

  $('#tableDetail').DataTable().clear();
  $('#tableDetail').DataTable().destroy();
  $('#loading').show();
  $('#bodyTableDetail').html('');
  var tableDetail = '';

  $.each(datas, function(key, value){
     console.log(value.bulans,kategori);
     console.log(value.tahuns,st);
     console.log(value.category,status);

     if (value.bulans == kategori && value.tahuns == st && value.category == status) {
      tableDetail += '<tr>';
      tableDetail += '<td>'+(value.checklist_id || '-')+'</td>';
      tableDetail += '<td>'+value.date_to+'</td>';
      tableDetail += '<td>'+(value.date_receive || '-')+'</td>';
      tableDetail += '<td>'+value.no_po+'</td>';
      tableDetail += '<td>'+value.category+'</td>';
      tableDetail += '<td>'+value.nama_item+'</td>';
      tableDetail += '<td>'+value.qty+'</td>';
      if (value.status == 2) {

          tableDetail += '<td style="background-color:#32a860;">Sudah di Ambil</td>';
     }else{

          tableDetail += '<td style="background-color:white;">-</td>';
     }

     tableDetail += '</tr>';
}
});

  $('#bodyTableDetail').append(tableDetail);

  $('#tableDetail tfoot th').each( function () {
    var title = $(this).text();
    $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
} );


  var table = $('#tableDetail').DataTable({
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
   className: 'btn btn-success',
   text: '<i class="fa fa-file-excel-o"></i> Excel',
   exportOptions: {
     columns: ':not(.notexport)'
}
}

]
},
initComplete: function() {
 this.api()
 .columns([4])
 .every(function(dd) {
   var column = this;
   var theadname = $("#tableDetail th").eq([dd])
   .text();
   var select = "";
   var select = $(
     '<select><option value="" style="font-size:11px;">All</option></select>'
     )
   .appendTo($(column.footer()).empty())
   .on('change', function() {
     var val = $.fn.dataTable.util
     .escapeRegex($(this)
       .val());

     column.search(val ? '^' + val + '$' :
       '', true,
       false)
     .draw();
});
   column
   .data()
   .unique()
   .sort()
   .each(function(d, j) {
     var vals = d;
     if ($("#tableDetail th").eq([dd])
       .text() ==
       'Category') {
       vals = d.split(' ')[0];
}
select.append(
     '<option style="font-size:12px;"  value="' +
     d + '">' + vals + '</option>');
});
});
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


  table.columns().every( function () {
    var that = this;
    $( '#search', this.footer() ).on( 'keyup change', function () {
      if ( that.search() !== this.value ) {
        that
        .search( this.value )
        .draw();
   }
} );
} );

  $('#tableDetail tfoot tr').appendTo('#tableDetail thead');

  $('#judul_detail').html('Detail MIS Inventory '+status);
  $('#modalDetail').modal('show');
  $('#loading').hide();
}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

function openSuccessGritter(title, message){
     jQuery.gritter.add({
          title: title,
          text: message,
          class_name: 'growl-success',
          image: '{{ url("images/image-screen.png") }}',
          sticky: false,
          time: '3000'
     });
}

function openErrorGritter(title, message) {
     jQuery.gritter.add({
          title: title,
          text: message,
          class_name: 'growl-danger',
          image: '{{ url("images/image-stop.png") }}',
          sticky: false,
          time: '3000'
     });
}

</script>
@endsection