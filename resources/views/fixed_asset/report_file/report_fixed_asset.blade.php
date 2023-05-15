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
    padding: 5px;
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
    <small></small>
  </h1>
  <ol class="breadcrumb">

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
      <div class="box box-solid">
        <div class="box-body">
          <div id="chart"></div>
          <div id="table_result_container"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-default fade" id="detailModal">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">
              &times;
            </span>
          </button>
          <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Fixed Asset Detail</h1>
        </div>
      </div>
      <div class="modal-body" style="padding-top: 0px">
        <div class="row">
          <div class="col-xs-12">
            <div class="box-body" style="padding: 0px;margin-top: 0px">
              <center style="padding-top: 0px">
                <div style="width: 100%" style="padding-top: 0px">
                  <div class="col-xs-5" style="padding-left:0">
                    <table style="border:1px solid black; border-collapse: collapse;">
                      <tbody align="center">
                        <tr>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;padding: 0;">
                            <img src="#" id="picture" alt="Asset Picture" style="max-width: 100%;">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="col-xs-7" style="padding:0">
                    <span id="reason" style="font-weight: bold;font-size: 18px;color: red">

                    </span>
                    <br>
                    <table style="border:1px solid black; border-collapse: collapse;">
                      <tbody align="center">
                        <tr>
                          <td colspan="2" style="border:1px solid black; font-size: 20px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">General Information</td>
                        </tr>
                        <tr>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                            SAP Number
                          </td>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="sap_number">

                          </td>
                        </tr>
                        <tr>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                            Fixet Asset Name
                          </td>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="fixed_asset_name">

                          </td>
                        </tr>
                        <tr>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                            Classification
                          </td>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="classification_category">

                          </td>
                        </tr>
                        {{--   <tr>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                            Original Amount
                          </td>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="original_amount">
                          </td>
                        </tr> --}}
                        <tr>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                            Location
                          </td>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="location">
                          </td>
                        </tr>
                        <tr>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                            Invoice Number
                          </td>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="invoice">
                          </td>
                        </tr>
                        <tr>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                            Vendor
                          </td>
                          <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="vendor">
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <br><br>

                <div style="width: 100%;">
                  <table style="border:1px solid black; border-collapse: collapse;margin-top:240px;width: 100%">
                    <thead align="center">
                      <tr>
                        <td colspan="10" style="border:1px solid black; font-size: 15px; width: 15%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">History Audit Fixed Asset</td>
                      </tr>
                      <tr>
                        <td style="border:1px solid black; font-size: 15px; width: 1%; height: 15; font-weight: bold;background-color: #d4e157;" rowspan="2">
                          ID
                        </td>
                        <td style="border:1px solid black; font-size: 15px; width: 3%; height: 15;font-weight: bold;background-color: #d4e157;" rowspan="2">
                          Periode
                        </td>
                        <td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;" rowspan="2">
                          Availability
                        </td>   
                        <td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;" colspan="4">
                          Condition
                        </td>                           
                        <td style="border:1px solid black; font-size: 15px; width: 3%; height: 15;font-weight: bold;background-color: #d4e157;" rowspan="2">
                          Note
                        </td>
                        <td style="border:1px solid black; font-size: 15px; width: 5%; height: 15;font-weight: bold;background-color: #d4e157;" rowspan="2">
                          Evidence
                        </td>
                        <td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;" rowspan="2">
                          Status
                        </td>
                      </tr>
                      <tr>
                        <td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;">
                          Asset
                        </td>
                        <td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;">
                          Label
                        </td>
                        <td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;">
                          Usable
                        </td>
                        <td style="border:1px solid black; font-size: 15px; width: 1%; height: 15;font-weight: bold;background-color: #d4e157;">
                          Map
                        </td>
                      </tr>
                    </thead>
                    <tbody align="center" id="bodyEmp">

                    </tbody>
                  </table>
                </div>
              </center>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="modalImage">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><center> <b style="font-size: 2vw">Asset Image</b> </center>
        <div class="modal-body no-padding">
          <div class="col-xs-12">
            <form action="{{ url("update/fixed_asset/photo")}}" method="post" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to update image?');" id="form_image" style="display: none">
              <div class="form-group">
                <label for="new_asset_image">Update Asset Image</label>
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <input type="file" id="new_asset_image" name="new_asset_image">
                <input type="hidden" id="sap_number_img" name="sap_number">

                <p class="help-block">Upload a New Image if Needed</p>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary" onclick="loading()"><i class="fa fa-upload"></i> Update Image</button>
              </div>
            </form>
          </div>
          <div class="col-xs-12" id="images" style="padding-top: 20px">

          </div>
        </div>
        <div class="modal-footer">
          <div class="row">
            <button class="btn btn-danger btn-block pull-right" data-dismiss="modal" aria-hidden="true" style="font-size: 20px;font-weight: bold;">
              CLOSE
            </button>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  var role = "{{ Auth::user()->role_code }}";
  var data_all = [];
  var dpts = [];

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

    fillTable();
  });

  function getFormattedDate(date) {
    var year = date.getFullYear();

    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
      "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
      ];

    var month = date.getMonth();

    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;
    
    return day + ' ' + monthNames[month] + ' ' + year;
  }


  function clearConfirmation(){
    location.reload(true);
  }

  function initTable() {
    $("#table_result_container").html("");
    var bd = '';

    bd += '<table id="tableResult" class="table table-bordered table-striped table-hover" >';
    bd += '<thead style="background-color: rgba(126,86,134,.7);">';
    bd += '<tr>';
    bd += '<th style="width: 1%;">SAP Number</th>';
    bd += '<th style="width: 4%;">Name</th>';
    bd += '<th style="width: 1%;">Registration Date</th>';
    bd += '<th style="width: 1%;">Classification</th>';
    bd += '<th style="width: 2%;">Location</th>';
    bd += '<th style="width: 1%;">Image</th>';
    bd += '<th style="width: 1%;">Status</th>';
    bd += '<th style="width: 1%;">Action</th>';
    bd += '</tr>';
    bd += '</thead>';
    bd += '<tbody id="tableBodyResult">';
    bd += '</tbody>';
    bd += '<tfoot>';
    bd += '<tr>';
    bd += '<th></th>';
    bd += '<th></th>';
    bd += '<th></th>';
    bd += '<th></th>';
    bd += '<th></th>';
    bd += '<th></th>';
    bd += '<th></th>';
    bd += '<th></th>';
    bd += '</tr>';
    bd += '</tfoot>';
    bd += '</table>';

    $("#table_result_container").append(bd);
  }

  function fillTable(){
    $('#loading').show();

    initTable();
    $.get('{{ url("fetch/fixed_asset/report") }}', function(result, status, xhr){

      var tableData = "";
      data_all = result.lists;
      dpts = result.dpt_lists;

      $.each(result.lists, function(key, value) {
        if (value.retired_date == null) {

          tableData += '<tr>';     
          tableData += '<td>'+ (value.sap_number || '')+'</td>';
          tableData += '<td>'+ value.fixed_asset_name +'</td>';     
          tableData += '<td>'+ (value.request_date || '') +'</td>';
          tableData += '<td>'+ (value.classification_category || '') +'</td>';
          tableData += '<td>'+ value.location +'</td>';

          var url = "{{ url('files/fixed_asset/asset_picture') }}/"+value.picture;

          var d = '';

          $.each(dpts, function(key2, value2) {
            if (value2.section == value.section) {
              d = value2.department;
            }
          })

          tableData += "<td><img src='"+url+"' style='max-width: 100px; max-height: 100px; cursor:pointer' onclick='modalImage(\""+url+"\", \""+value.sap_number+"\", \""+d+"\")' Alt='Image Not Found'></td>";

          if (value.retired_date == null && value.sap_number) {
            tableData += '<td style="background-color:#00a65a !important;color:#fff;text-align:center">Active</td>';
          }
          else if (value.retired_date == null && !value.sap_number) {
            tableData += '<td style="background-color:#00a65a !important;color:#fff;text-align:center">Registration in Progress</td>';
          }
          else{
            if (value.classification_category == 'Construction in Prog') {
              tableData += '<td style="background-color:#dd4b39 !important;color:#fff;text-align:center">Transfer Asset</td>';
            } else {
              tableData += '<td style="background-color:#dd4b39 !important;color:#fff;text-align:center">Retired</td>';
            }
          }

          if (!value.sap_number) {
            tableData += '<td></td>';
          } else {            
            tableData += '<td style="text-align:center"><button style="margin-right:2px" class="btn btn-md btn-primary" onclick="detailInformation(\''+value.sap_number+'\')"><i class="fa fa-eye"> Detail</button></td>';
          }
          tableData += '</tr>';  
        }   
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
          }
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


      table.columns().every( function () {
        var that = this;

        $( 'input', this.footer() ).on( 'keyup change', function () {
          if ( that.search() !== this.value ) {
            that
            .search( this.value )
            .draw();
          }
        } );
      } );

      $('#tableResult tfoot tr').appendTo('#tableResult thead');

      var categories = [];
      var series = [];

      $.each(result.resume, function(key, value) {
        categories.push(value.section);
        series.push(parseInt(value.jml_asset));
      })

      Highcharts.chart('chart', {
        chart: {
          type: 'column'
        },

        title: {
          text: 'Total All Asset'
        },

        xAxis: {
          categories: categories,
        },

        yAxis: {
          allowDecimals: false,
          min: 0,
          title: {
            text: 'Number of Assets'
          }
        },

        tooltip: {
          formatter: function () {
            return '<b>' + this.x + '</b><br/>' +
            this.series.name + ': ' + this.y
          }
        },

        legend: {
          enabled: false
        },

        plotOptions: {
          column: {
            cursor: 'pointer',
            point: {
              events: {
                click: function () {
                  redrawTable(this.category);
                }
              }
            },
            minPointLength: 3,
            dataLabels: {
              allowOverlap: true,
              enabled: true,
              y: -25,
              style: {
                // color: 'black',
                fontSize: '13px',
                textOutline: false,
                fontWeight: 'bold',
              },
              rotation: -90
            }
          }
        },
        credits : {
          enabled: false
        },

        series: [{
          name: 'Total Asset',
          data: series,
          color: '#00a65a'
        }]
      });

      $('#loading').hide();
    })

}

function modalImage(url, sap_number, dept) {
  $('#images').html('<img style="width:100%" src="'+url+'" class="user-image" alt="User image">');
  $("#sap_number_img").val(sap_number);

  var dpt = <?php echo json_encode($my_dpt) ?>;

  if (dept == dpt || ~role.indexOf("MIS")) {
    $("#form_image").show();
  } else {
    $("#form_image").hide();
  }

  $('#modalImage').modal('show');
}

function detailInformation(sap_number) {
  $("#loading").show();
  var data = {
    sap_number:sap_number
  }

  $.get('{{ url("index/fixed_asset/report/detail") }}', data,function(result, status, xhr){
    if(result.status){
      $("#loading").hide();
      $("#detailModal").modal('show');
      $('#picture').attr('src',"{{url('files/fixed_asset/asset_picture/')}}/"+result.fixed_item.picture);
      $('#sap_number').html(result.fixed_item.sap_number);
      $('#fixed_asset_name').html(result.fixed_item.fixed_asset_name);
      $('#classification_category').html(result.fixed_item.classification_category);
      // $('#original_amount').html("$ "+result.fixed_item.original_amount);
      $('#location').html(result.fixed_item.location);
      $('#vendor').html(result.fixed_item.vendor);
      $('#invoice').html(result.fixed_item.invoice_number);
      $('#reason').html('');

      if (result.fixed_item.retired_date != null) {
        $('#reason').html('Item Not Used Since : '+getFormattedDate(new Date(result.fixed_item.retired_date)));
      }else{
        $('#reason').html('Item Has Been Registered Since : '+getFormattedDate(new Date(result.fixed_item.request_date)));
      }
      $('#bodyEmp').html('');
      var bodyEmp = '';

      console.log(result.fixed_audit);
      for(var i = 0; i < result.fixed_audit.length;i++){
        bodyEmp += '<tr>';
        bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.fixed_audit[i].id+'</td>';
        bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.fixed_audit[i].period+'</td>';                            
        bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.fixed_audit[i].availability+'</td>';
        bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.fixed_audit[i].asset_condition+'</td>';
        bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.fixed_audit[i].label_condition+'</td>';
        bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.fixed_audit[i].usable_condition+'</td>';
        bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.fixed_audit[i].map_condition+'</td>';
        bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;">'+result.fixed_audit[i].note+'</td>';

        var url = '{{url("files/fixed_asset/asset_audit")}}'+'/'+result.fixed_audit[i].result_images;

        bodyEmp += '<td style="border:1px solid black; font-size: 13px;  height: 15;"><img src='+url+' style="max-width:100%"></td>';

        if (result.fixed_audit[i].status == "Close") {
          bodyEmp += '<td style="border:1px solid black; font-size: 13px;background-color:#00a65a;text-align:center;color:white">'+result.fixed_audit[i].status+'</td>';
        }else{
          bodyEmp += '<td style="border:1px solid black; font-size: 13px;background-color:red;text-align:center;color:white">'+result.fixed_audit[i].status+'</td>';
        }

        bodyEmp += '</tr>';
      }

      $('#bodyEmp').append(bodyEmp);
    }
  })

}

function redrawTable(section) {
 initTable();
 var tableData = "";

 $.each(data_all, function(key, value) {
  if (value.retired_date == null) {
    if (value.section == section) {
      tableData += '<tr>';     
      tableData += '<td>'+ value.sap_number+'</td>';
      tableData += '<td>'+ value.fixed_asset_name +'</td>';     
      tableData += '<td>'+ getFormattedDate(new Date(value.request_date)) +'</td>';
      tableData += '<td>'+ value.classification_category +'</td>';
      tableData += '<td>'+ value.location +'</td>';

      var url = "{{ url('files/fixed_asset/asset_picture') }}/"+value.picture;

      var d = '';

      $.each(dpts, function(key2, value2) {
        if (value2.section == value.section) {
          d = value2.department;
        }
      })

      tableData += "<td><img src='"+url+"' style='max-width: 100px; max-height: 100px; cursor:pointer' onclick='modalImage(\""+url+"\", \""+value.sap_number+"\", \""+d+"\")' Alt='Image Not Found'></td>";

      if (value.retired_date == null) {
        tableData += '<td style="background-color:#00a65a !important;color:#fff;text-align:center">Active</td>';
      }
      else{
        tableData += '<td style="background-color:#dd4b39 !important;color:#fff;text-align:center">Retired</td>';
      }
      tableData += '<td style="text-align:center"><button style="margin-right:2px" class="btn btn-md btn-primary" onclick="detailInformation(\''+value.sap_number+'\')"><i class="fa fa-eye"> Detail</button></td>';
      tableData += '</tr>';     
    }
  }
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
    {
      className: 'btn btn-primary',
      text: '<i class="fa fa-refresh"></i> Show All Asset',
      action: function ( e, dt, button, config ) {
        fillTable();
      }  
    }
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


 table.columns().every( function () {
  var that = this;

  $( 'input', this.footer() ).on( 'keyup change', function () {
    if ( that.search() !== this.value ) {
      that
      .search( this.value )
      .draw();
    }
  } );
} );

 $('#tableResult tfoot tr').appendTo('#tableResult thead');
}

function loading() {
  $('#loading').show();
}

function openSuccessGritter(title, message){
  jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-success',
    image: '{{ url("images/image-screen.png") }}',
    sticky: false,
    time: '2000'
  });
}

function openErrorGritter(title, message) {
  jQuery.gritter.add({
    title: title,
    text: message,
    class_name: 'growl-danger',
    image: '{{ url("images/image-stop.png") }}',
    sticky: false,
    time: '2000'
  });
}

// Highcharts.createElement('link', {
//   href: '{{ url("fonts/UnicaOne.css")}}',
//   rel: 'stylesheet',
//   type: 'text/css'
// }, null, document.getElementsByTagName('head')[0]);

// Highcharts.theme = {
//   colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
//   '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
//   chart: {
//     backgroundColor: {
//       linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
//       stops: [
//       [0, '#2a2a2b'],
//       [1, '#3e3e40']
//       ]
//     },
//     style: {
//       fontFamily: 'sans-serif'
//     },
//     plotBorderColor: '#606063'
//   },
//   title: {
//     style: {
//       color: '#E0E0E3',
//       textTransform: 'uppercase',
//       fontSize: '20px'
//     }
//   },
//   subtitle: {
//     style: {
//       color: '#E0E0E3',
//       textTransform: 'uppercase'
//     }
//   },
//   xAxis: {
//     gridLineColor: '#707073',
//     labels: {
//       style: {
//         color: '#E0E0E3'
//       }
//     },
//     lineColor: '#707073',
//     minorGridLineColor: '#505053',
//     tickColor: '#707073',
//     title: {
//       style: {
//         color: '#A0A0A3'

//       }
//     }
//   },
//   yAxis: {
//     gridLineColor: '#707073',
//     labels: {
//       style: {
//         color: '#E0E0E3'
//       }
//     },
//     lineColor: '#707073',
//     minorGridLineColor: '#505053',
//     tickColor: '#707073',
//     tickWidth: 1,
//     title: {
//       style: {
//         color: '#A0A0A3'
//       }
//     }
//   },
//   tooltip: {
//     backgroundColor: 'rgba(0, 0, 0, 0.85)',
//     style: {
//       color: '#F0F0F0'
//     }
//   },
//   plotOptions: {
//     series: {
//       dataLabels: {
//         color: 'white'
//       },
//       marker: {
//         lineColor: '#333'
//       }
//     },
//     boxplot: {
//       fillColor: '#505053'
//     },
//     candlestick: {
//       lineColor: 'white'
//     },
//     errorbar: {
//       color: 'white'
//     }
//   },
//   legend: {
//     itemStyle: {
//       color: '#E0E0E3'
//     },
//     itemHoverStyle: {
//       color: '#FFF'
//     },
//     itemHiddenStyle: {
//       color: '#606063'
//     }
//   },
//   credits: {
//     style: {
//       color: '#666'
//     }
//   },
//   labels: {
//     style: {
//       color: '#707073'
//     }
//   },

//   drilldown: {
//     activeAxisLabelStyle: {
//       color: '#F0F0F3'
//     },
//     activeDataLabelStyle: {
//       color: '#F0F0F3'
//     }
//   },

//   navigation: {
//     buttonOptions: {
//       symbolStroke: '#DDDDDD',
//       theme: {
//         fill: '#505053'
//       }
//     }
//   },

//   rangeSelector: {
//     buttonTheme: {
//       fill: '#505053',
//       stroke: '#000000',
//       style: {
//         color: '#CCC'
//       },
//       states: {
//         hover: {
//           fill: '#707073',
//           stroke: '#000000',
//           style: {
//             color: 'white'
//           }
//         },
//         select: {
//           fill: '#000003',
//           stroke: '#000000',
//           style: {
//             color: 'white'
//           }
//         }
//       }
//     },
//     inputBoxBorderColor: '#505053',
//     inputStyle: {
//       backgroundColor: '#333',
//       color: 'silver'
//     },
//     labelStyle: {
//       color: 'silver'
//     }
//   },

//   navigator: {
//     handles: {
//       backgroundColor: '#666',
//       borderColor: '#AAA'
//     },
//     outlineColor: '#CCC',
//     maskFill: 'rgba(255,255,255,0.1)',
//     series: {
//       color: '#7798BF',
//       lineColor: '#A6C7ED'
//     },
//     xAxis: {
//       gridLineColor: '#505053'
//     }
//   },

//   scrollbar: {
//     barBackgroundColor: '#808083',
//     barBorderColor: '#808083',
//     buttonArrowColor: '#CCC',
//     buttonBackgroundColor: '#606063',
//     buttonBorderColor: '#606063',
//     rifleColor: '#FFF',
//     trackBackgroundColor: '#404043',
//     trackBorderColor: '#404043'
//   },

//   legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
//   background2: '#505053',
//   dataLabelsColor: '#B0B0B3',
//   textColor: '#C0C0C0',
//   contrastTextColor: '#F0F0F3',
//   maskColor: 'rgba(255,255,255,0.3)'
// };
// Highcharts.setOptions(Highcharts.theme);

</script>

@stop