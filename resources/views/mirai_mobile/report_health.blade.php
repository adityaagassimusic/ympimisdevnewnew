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
    overflow:hidden;
    padding: 3px;
  }
  tbody>tr>td{
    text-align:left;
    vertical-align: middle;
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
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content-header">
  <h1>
    List of {{ $page }}
  </h1>
  <ol class="breadcrumb">
    <!-- <li><a href="{{ url("index/form_experience/create")}}" class="btn btn-success btn-sm" style="color:white"><i class="fa fa-plus"></i>Buat {{ $page }}</a></li> -->
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

  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 45%;">
      <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body">
          <div class="col-xs-2">
            <div class="row">
              <div class="input-group date" style="padding-bottom: 10px;">
                <div class="input-group-addon bg-green" style="border: none; background-color: #605ca8; color: white;">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control datepicker" id="tanggal" name="tanggal" placeholder="Pilih Tanggal">
              </div>
            </div>
          </div>
          <div class="col-xs-2">
            <button class="btn btn-success" onclick="fillTable()">Search</button>
          </div>
          <div style="overflow-x: scroll;padding-left: 0px;padding-right: 0px;" class="col-xs-12">
            <table id="tableResult" class="table table-bordered table-striped table-hover">
              <thead style="background-color: rgba(126,86,134,.7);color: white;">
                <tr>
                  <th style="width: 1%;">Date</th>
                  <th style="width: 1%;">ID</th>
                  <th style="width: 4%;">Name</th>
                  <th style="width: 4%;">Task</th>
                  <th style="width: 4%;">Dept</th>
                  <th style="width: 2%;">Sect</th>
                  <th style="width: 2%;">Group</th>
                  <th style="width: 1%;background-color: #819de6">Timing IN</th>
                  <th style="width: 1%;background-color: #819de6">Village</th>
                  <th style="width: 1%;background-color: #819de6">District</th>
                  <th style="width: 1%;background-color: #819de6">State</th>
                  <th style="width: 1%;background-color: #819de6">Foto</th>
                  <th style="width: 1%;background-color: #819de6">Location</th>
                  <th style="width: 1%;background-color: #7bb068">Timing OUT</th>
                  <th style="width: 1%;background-color: #7bb068">Village</th>
                  <th style="width: 1%;background-color: #7bb068">District</th>
                  <th style="width: 1%;background-color: #7bb068">State</th>
                  <th style="width: 1%;background-color: #7bb068">Foto</th>
                  <th style="width: 1%;background-color: #7bb068">Location</th>
                </tr>
              </thead>
              <tbody id="tableBodyResult">
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>


@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
    $('#tanggal').datepicker({
      autoclose: true,
      todayHighlight: true
    });
    $('body').toggleClass("sidebar-collapse");
    $("#navbar-collapse").text('');
    $('.select2').select2({
      language : {
        noResults : function(params) {

        }
      }
    });
  });


  function clearConfirmation(){
    location.reload(true);
  }

  function fillTable(){
    $('#loading').show();
    var tanggal = $('#tanggal').val();
    if (tanggal == '') {
      $('#loading').hide();
      openErrorGritter('Error','Pilih Tanggal');
      return false;
    }
    var data = {
      tanggal:tanggal
    }

    $.get('{{ url("fetch/mirai_mobile/report_attendance/with_loc") }}', data, function(result, status, xhr){
      $('#tableResult').DataTable().clear();
      $('#tableResult').DataTable().destroy();
      $('#tableBodyResult').html("");
      var tableData = "";
      $.each(result.lists, function(key, value) {
       tableData += '<tr>';     
       tableData += '<td>'+ value.date_in+'</td>';
       tableData += '<td>'+ value.employee_id +'</td>';     
       tableData += '<td>'+ value.name +'</td>';
       tableData += '<td>'+ (value.task || "") +'</td>';
       tableData += '<td>'+ (value.department || "") +'</td>';
       tableData += '<td>'+ (value.section || "") +'</td>';
       tableData += '<td>'+ (value.group || "") +'</td>';
       tableData += '<td>'+ (value.time_in_start || "") +'</td>';
       tableData += '<td>'+ (value.village_start || "") +'</td>';
       tableData += '<td>'+ (value.state_district_start || "") +'</td>';
       tableData += '<td>'+ (value.state_start || "") +'</td>';
       tableData += '<td style="text-align:center;font-size:20px;">';
       if (value.images_start != null) {
        var datas = JSON.parse(value.images_start);
        for (var i = 0; i < datas.length; i++) {
          tableData += '<a href="https://ympi.co.id/ympicoid/public/images/absensi/'+datas[i]+'" target="_blank"><i class="fa fa-paperclip"></i></a>';
        }
       }
       tableData += '</td>';
       tableData += '<td style="text-align:center;font-size:20px;">';
       tableData += '<div class="mapouter"><div class="gmap_canvas"><iframe width="200" height="200" id="gmap_canvas" src="https://maps.google.com/maps?q='+value.latitude+','+value.longitude_start+'&t=&z=10&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://2yu.co">2yu</a><br><style>.mapouter{position:relative;text-align:right;height:200px;width:200px;}</style><a href="https://embedgooglemap.2yu.co">html embed google map</a><style>.gmap_canvas {overflow:hidden;background:none!important;height:200px;width:200px;}</style></div></div>';
       tableData += '<a href="https://maps.google.com/?q='+value.latitude_start+','+value.longitude_start+'" style="font-weight:bold;" target="_blank">CLICK FOR DETAIL <i class="fa fa-map-marker"></i></a>';
       tableData += '</td>';
       if (value.time_in_start != value.time_in_end) {
         tableData += '<td>'+ (value.time_in_end || "") +'</td>';
         tableData += '<td>'+ (value.village_end || "") +'</td>';
         tableData += '<td>'+ (value.state_district_end || "") +'</td>';
         tableData += '<td>'+ (value.state_end || "") +'</td>';
         tableData += '<td style="text-align:center;font-size:20px;">';
         if (value.images_end != null) {
          var datas = JSON.parse(value.images_end);
          for (var i = 0; i < datas.length; i++) {
            tableData += '<a href="https://ympi.co.id/ympicoid/public/images/absensi/'+datas[i]+'" target="_blank"><i class="fa fa-paperclip"></i></a>';
          }
         }
         tableData += '</td>';
         tableData += '<td style="text-align:center;font-size:20px;">';
         tableData += '<div class="mapouter"><div class="gmap_canvas"><iframe width="200" height="200" id="gmap_canvas" src="https://maps.google.com/maps?q='+value.latitude+','+value.longitude_end+'&t=&z=10&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://2yu.co">2yu</a><br><style>.mapouter{position:relative;text-align:right;height:200px;width:200px;}</style><a href="https://embedgooglemap.2yu.co">html embed google map</a><style>.gmap_canvas {overflow:hidden;background:none!important;height:200px;width:200px;}</style></div></div>';
         tableData += '<a href="https://maps.google.com/?q='+value.latitude_end+','+value.longitude_end+'" style="font-weight:bold;" target="_blank">CLICK FOR DETAIL <i class="fa fa-map-marker"></i></a>';
         tableData += '</td>';
       }else{
        tableData += '<td></td>';
        tableData += '<td></td>';
        tableData += '<td></td>';
        tableData += '<td></td>';
        tableData += '<td></td>';
        tableData += '<td></td>';
       }
       tableData += '</tr>';
     });


      $('#tableBodyResult').append(tableData);

      $('#tableResult tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
      } );
      var table = $('#tableResult').DataTable({
        'dom': 'Bfrtip',
        'responsive':true,
        'lengthMenu': [
        [ 5, 10, 25, -1 ],
        [ '5 rows', '10 rows', '25 rows', 'Show all' ]
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
          },
          ]
        },
        'paging': true,
        'lengthChange': true,
        'pageLength': 15,
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


      // table.columns().every( function () {
      //   var that = this;

      //   $( 'input', this.footer() ).on( 'keyup change', function () {
      //     if ( that.search() !== this.value ) {
      //       that
      //       .search( this.value )
      //       .draw();
      //     }
      //   } );
      // } );

      // $('#tableResult tfoot tr').appendTo('#tableResult thead');

      $('#loading').hide();
    })

    // $.get('{{ url("fetch/mirai_mobile/report_attendance") }}', data, function(result, status, xhr){
    //   if(result.status){
    //     $('#tableResult').DataTable().clear();
    //     $('#tableResult').DataTable().destroy();
    //     $('#tableBodyResult').html("");
    //     var tableData = "";
    //     var count = 1;

        // $.each(result.lists, function(key, value) {

    //       var d = new Date(value.answer_date);
    //       var day = d.getDate();
    //       var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    //       var month = months[d.getMonth()];
    //       var year = d.getFullYear();      

    //       tableData += '<tr>';     
    //       tableData += '<td>'+ value.date+'</td>';
    //       tableData += '<td>'+ value.employee_id +'</td>';     
    //       tableData += '<td>'+ value.name +'</td>';
    //       tableData += '<td>'+ (value.department || "") +'</td>';
    //       tableData += '<td>'+ (value.section || "") +'</td>';
    //       tableData += '<td>'+ (value.group || "") +'</td>';
    //       tableData += '<td>'+ value.date_in +'</td>';
    //       // $.each(, function(key2, value2) {
    //         var url = '{{url("")}}';
    //         tableData += '<td><a target="_blank" href="'+url+'/trial_loc2/'+value.location.latitude+'/'+value.location.longitude+'" class="btn btn-warning btn-sm"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;Location</a></td>';
    //       // })
    //       // var data2 = {
    //       //   lat : value.lat_in,
    //       //   lng : value.lng_in
    //       // }
    //       // $.get('{{ url("fetch/location_employee") }}', data2, function(result, status, xhr){
    //       //   if(result.status){

    //       //     console.log(value.data);

    //       //     $.each(result.data, function(key2, value2) {
    //       //       // console.log(value.village);
    //       //       tableData += '<td>'+ value2.village +'</td>';
    //       //     });
    //       //   }
    //       //   else{
    //       //     alert('Attempt to retrieve data failed');
    //       //   }

    //       // });
    //       // tableData += '<td>'+ value.time_out +'</td>';
    //       // tableData += '<td><a target="_blank" href="https://172.17.128.87/miraidev/public/trial3?lat='+value.lat_out+'&long='+value.lng_out+'" class="btn btn-warning btn-sm"><i class="fa fa-map-marker"></i>&nbsp;&nbsp;Location</a></td>';
    //       // tableData += '<td>'+ value.village +'</td>';
    //       // tableData += '<td>'+ value.city +'</td>';
    //       // tableData += '<td>'+ value.remark +'</td>';
    //       tableData += '</tr>';
    //       count += 1;
    //     });

    //     $('#tableBodyResult').append(tableData);

    //     $('#tableResult tfoot th').each( function () {
    //         var title = $(this).text();
    //         $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="20"/>' );
    //       } );
    //     var table = $('#tableResult').DataTable({
    //       'dom': 'Bfrtip',
    //       'responsive':true,
    //       'lengthMenu': [
    //       [ 5, 10, 25, -1 ],
    //       [ '5 rows', '10 rows', '25 rows', 'Show all' ]
    //       ],
    //       'buttons': {
    //         buttons:[
    //         {
    //           extend: 'pageLength',
    //           className: 'btn btn-default',
    //         },
    //         {
    //           extend: 'copy',
    //           className: 'btn btn-success',
    //           text: '<i class="fa fa-copy"></i> Copy',
    //           exportOptions: {
    //             columns: ':not(.notexport)'
    //           }
    //         },
    //         {
    //           extend: 'excel',
    //           className: 'btn btn-info',
    //           text: '<i class="fa fa-file-excel-o"></i> Excel',
    //           exportOptions: {
    //             columns: ':not(.notexport)'
    //           }
    //         },
    //         {
    //           extend: 'print',
    //           className: 'btn btn-warning',
    //           text: '<i class="fa fa-print"></i> Print',
    //           exportOptions: {
    //             columns: ':not(.notexport)'
    //           }
    //         },
    //         ]
    //       },
    //       'paging': true,
    //       'lengthChange': true,
    //       'pageLength': 15,
    //       'searching': true,
    //       'ordering': true,
    //       'order': [],
    //       'info': true,
    //       'autoWidth': true,
    //       "sPaginationType": "full_numbers",
    //       "bJQueryUI": true,
    //       "bAutoWidth": false,
    //       "processing": true
    //     });


    //     table.columns().every( function () {
    //         var that = this;

    //         $( 'input', this.footer() ).on( 'keyup change', function () {
    //           if ( that.search() !== this.value ) {
    //             that
    //             .search( this.value )
    //             .draw();
    //           }
    //         } );
    //       } );

    //       $('#tableResult tfoot tr').appendTo('#tableResult thead');

    //     $('#loading').hide();
    //   }
    //   else{
    //     $('#loading').hide();
    //     alert('Attempt to retrieve data failed');
    //   }

    // });      // $('#judul_table').append().empty();
      // $('#judul_table').append('<center>Pengecekan Tanggal <b>'+tanggal+'</b> dengan Judgement <b>'+jdgm+'</b> (<b>'+remark+'</b>)</center>');
      
    }

    function deleteConfirmation(id) {
      jQuery('#modalDeleteButton').attr("href", '{{ url("index/qc_report/delete") }}'+'/'+id);
    }

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

  @stop