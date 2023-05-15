@extends('layouts.display')
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
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  th:hover {
    overflow: visible;
  }
  td:hover {
    overflow: visible;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
    font-size: 1.1vw;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:2px;
    font-size: 18px;
    text-align: center;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    font-size: 1vw;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }

  .table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
    background-color: #DDDDDD;
  }

  .table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
    background-color: #CCCDD9;
  }

  .tombol {
    font-weight: bold;
    font-size: 16px;
  }

  .td_tombol {
    padding: 5px;
  }

  .content-wrapper{
    background-color: white !important;
    padding: 0 !important;
  }

  #loading, #error { display: none; }
  
</style>
@endsection

@section('header')
<section class="content-header">
  <h1>
    {{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
  </h1>
  
</section>
@stop

@section('content')
<section class="content" style="padding: 0;">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: white; top: 45%; left: 35%;">
      <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  <div class="row">
    <div class="col-xs-12">
      <div class="box-body" style="background-color: #B3C2F2; color: black;" align="center">
        <span style="font-size: 35px;color: black;width: 100%; font-weight: bold">Material Process<br>Work Instruction Digital System (WINDS)<br> 作業手順書デジタル化</span>
      </div>
    </div>

    <div class="col-xs-12">
      <div class="box box-solid" style="margin:0">
        <div class="box-body">
          <table class="table table-hover table-striped table-bordered" id="tableResume">
            <thead style="background-color: #B3C2F2; color: black;">
              <tr>
                <th width="2%">No</th>
                <th width="10%">GMC YMPI</th>
                <th width="10%">GMC</th>
                <th>Description 名称</th>
                <th width="10%">Location 場所</th>
                <!-- <th width="10%">Drawing 図面</th> -->
                <!-- <th width="10%">QC Kouteihyo QC工程表</th> -->
                <th width="10%">Action</th>
              </tr>
            </thead>
            <tbody id="tableBodyResume">
            </tbody> 
            <tfoot>
              <tr>
                <th>NO</th>
                <th>GMC YMPI</th>
                <th>GMC</th>
                <th>Description</th>
                <th>Location</th>
                <!-- <th></th>
                <th></th> -->
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

  </div>

  <div class="modal fade" id="process" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
     <div class="modal-content">
      <div class="modal-header">
       <div class="col-xs-12" style="background-color: #757ce8;color: white;">
        <h1 style="text-align: center; margin:5px; font-weight: bold;">Processes 工程</h1>
      </div>
      <div class="col-xs-12" style="padding-bottom: 1%; padding-top: 2%;padding-right: 0;padding-left: 0;">
        <center><h4 id="title_proses" style="font-weight: bold; margin-bottom: 10px;font-size: 30px;"></h4></center>
        <table width="100%">
          <tbody align="center" id='body_button'>
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  jQuery(document).ready(function() {
   getData();
 });

  function getData(){
    $.get('<?php echo e(url("winds_mpro/master/fetch")); ?>', function(result, status, xhr){
      if(result.status){
        $('#tableResume').DataTable().clear();
        $('#tableResume').DataTable().destroy();
        var tableData = '';
        $('#tableBodyResume').html("");
        $('#tableBodyResume').empty();
        
        $.each(result.resumes, function(key, value) {

          var detail = '{{ url("winds/detail") }}';
          tableData += '<tr>';
          tableData += '<td>'+ value.order_number +'</td>';
          tableData += '<td>'+ (value.gmc_ympi || '') +'</td>';
          tableData += '<td>'+ (value.gmc || '') +'</td>';
          tableData += '<td style="text-align:left">&nbsp;'+ value.deskripsi +'</td>';
          tableData += '<td>'+ value.lokasi +'</td>';
          // tableData += '<td><a href="{{ url("winds_file/drawing_file") }}/'+ value.gmc +'.png" target="_blank"><i class="fa fa-paperclip"></i> Drawing '+value.gmc+'</a></td>';
          // tableData += '<td><a href="{{ url("winds_file/qc_kouteihyo") }}/'+value.gmc+'.pdf" target="_blank"><i class="fa fa-file-pdf-o" </i> QC Kouteihyo</a></td>';
          tableData += '<td><a class="btn btn-primary btn-md" onclick = "process(\''+value.gmc+'\',\''+value.deskripsi+'\')" style="color:white"><i class="fa fa-random"></i> Process 工程</a></td>';
          tableData += '</tr>';
        });

        $('#tableResume tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input id="search" style="text-align: center;color:black" type="text" placeholder="Search '+title+'" size="20"/>' );
        } );

        $('#tableBodyResume').append(tableData);
        var tableResume = $('#tableResume').DataTable({
          'dom': 'Bfrtip',
          'responsive':true,
          'lengthMenu': [
          [10, 25, 50, -1], [10, 25, 50, "All"]
          ],
          'buttons': {
            buttons:[
            {
              extend: 'pageLength',
              className: 'btn btn-default',
            }
            ]
          },
          'paging': true,
          'lengthChange': false,
          'pageLength': 1000,
          'searching': true,
          'ordering': true,
          // 'order': [],
          'info': true,
          'autoWidth': true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
          "processing": true
        });



        tableResume.columns().every( function () {
          var that = this;
          $( '#search', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {

              console.log(that.search());

              console.log(this.value);

              that
              .search( this.value )
              .draw();
            }
          } );
        } );

        $('#tableResume tfoot tr').appendTo('#tableResume thead');

        // openSuccessGritter('Success!', result.message);
        $('#modalLocation').modal('hide');
      }
      else{
        openErrorGritter('Error!', result.message);
      }
    });
  }

  function process(gmc, deskripsi){
    var data = {
      gmc : gmc
    }

    $("#title_proses").text(gmc+" - "+deskripsi);
    $.get('{{ url("winds/fetch/process_list") }}', data, function(result, status, xhr) {
      $("#body_button").empty();
      $("#process").modal('show');
      var body = "";


      body = "<tr>";
      $.each(result.process, function(key, value) {
        body += "<td class='td_tombol'>";

        if (value.cdm_status == 1) {
          link = "{{ url('winds/index/description_item') }}/"+value.gmc+"/"+value.id+"/"+(value.ik || '1');
        } else if(jQuery.inArray(value.gmc, ['WM44201','ZX31431', 'WM43981', 'W508791', 'W679801']) !== -1 && value.proses == 'Sanding'){
          link = "{{ url('winds_file/ik') }}/IK-"+value.proses+"-Pintop.pdf";
        } else {
          link = "{{ url('winds_file/ik') }}/IK-"+value.proses+".pdf";
        }

        body +="<a style='width:100%' class='btn btn-success tombol' href='"+link+"' target='_blank'>Process "+value.no_proses+"<br> "+value.proses+"</a>";
        body +="</td>";

        if (key % 5 == 0 && key != 0) {
          body += "</tr>";
          body += "<tr>";
        }

        if (key == result.process.length - 1) {
          body += "</tr>";
        }

      });

      $("#body_button").append(body);
    });
  }

  function getAntrian(category) {
    var data = {
      proses : category
    }

    $.get('{{ url("winds/fetch/process_list/antrian") }}', data, function(result, status, xhr) {

    })
  }


  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '4000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '4000'
    });
  }


</script>

@endsection