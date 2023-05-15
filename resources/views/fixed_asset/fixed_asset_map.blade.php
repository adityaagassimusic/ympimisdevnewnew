@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

	.container {width: auto;}
  .content-header>h1 {font-size: 3.5em; font-family: sans-serif; font-weight: bold; color: black;}
  body, .content-wrapper {background-color: #000;}
  .temp-widget {border-radius: 4px; color: black;overflow: auto; border: 1px solid purple; font-size: 0.75em; width: 100px; letter-spacing: 1.1px;}
  .temp-widget-refrigerator {border-radius: 15px 15px 0px 0px; overflow: auto; border: 1px solid white; font-size: 0.75em; width: 25px; letter-spacing: 1.1px;}
  #main-body {overflow: auto;}
  #custom-title {position: absolute; top: 40px; left: 40px; font-size: 1.5em; border: 1px solid black; border-radius: 5px; padding: 10px; background-color: rgba(0, 255, 0, 0.4);}

  .morecontent span {
    display: none;
  }

  .temp-widget-small {
    border-radius: 4px; color: black;overflow: auto; border: 1px solid purple; font-size: 0.75em; width: 40px; letter-spacing: 1.1px;
  }
  
  .morelink {
    display: block;
  }

  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  #listTableBody > tr:hover {
    cursor: pointer;
    background-color: #7dfa8c;
  }
  table.table-bordered{
    border:1px solid black;
    vertical-align: middle;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
    vertical-align: middle;
    text-align: left;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(150,150,150);
    vertical-align: middle;
    text-align: left;
  }


#title {
    position: absolute;
    top: 10px;
    left: 980px;
    font-size: 1.5em;
    border: 1px solid black;
    border-radius: 5px;
    padding: 10px;
    background-color: rgba(255, 255, 0, 0.9);
}

.dataTable > thead > tr > th[class*="sort"]:after{
 content: "" !important;
}

#queueTable.dataTable {
 margin-top: 0px!important;
}
#loading, #error { display: none; }

#parent { 
 position: relative; 
     /*width: 720px; 
     height:500px;*/
     margin-right: auto;
     margin-left: auto; 
     /*border: solid 1px red; */
     font-size: 24px; 
     text-align: center; 
   }

   .square {
    opacity: 0.8;
  }

  .squarex {
    border-radius: 4px;
    overflow: auto;
    border: 1px solid white;
    font-size: 0.75em;
    width: 35px;
    letter-spacing: 1.1px;
  }


  thead>tr>th{
    text-align:center;
  }
  .content-wrapper{
    padding-top: 0 !important;
  }

  .dot {
    height: 5%;
    width: 5%;
    position: absolute;
    z-index: 10;
  }


  .text {
    /*color: white;*/
    font-size: 1.2vw;
    font-weight: bold;
    display: inline-block;
    vertical-align: middle;
  }

  .text2 {
    color: white;
    font-size: 1.6vw;
    font-weight: bold;
  }

  .table-bordered > thead > tr > th {
    border: 1px solid #eee;
  }

  .table-bordered > thead > tr > td {
    border: 1px solid #eee;
  }

  .table-bordered > tbody > tr > td {
    border: 1px solid #eee;
  }

  .sedang {
    -webkit-animation: sedang 1s infinite;  /* Safari 4+ */
    -moz-animation: sedang 1s infinite;  /* Fx 5+ */
    -o-animation: sedang 1s infinite;  /* Opera 12+ */
    animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
  }


  .squarex {
    border-radius: 4px;
    overflow: auto;
    border: 1px solid white;
    font-size: 0.75em;
    width: 15vw;
    letter-spacing: 1.1px;
  }

  @-webkit-keyframes sedang {
    0%, 49% {
      background: #e57373;
    }
    50%, 100% {
      background-color: #ffccff;
    }
  }

  .content{
    padding: 0;
  }


  .widget
  {
    position: relative;
    width: 20vw;
    height: 8vw;
    /*margin: 150px auto;*/
    background-color: #fcfdfd;
    border-radius: 9px;
    padding: 1vw;
    padding-right: 30px;
    box-shadow: 0px 31px 35px -26px #080c21;
  }

  .widget .left-panel
  {
    
  }

  .widget .tanggal
  {
    font-size: 1vw;
    font-weight: bold;
    color: rgba(0,0,0,0.5);
  }

  .widget .kota
  {
    font-size: 1.5vw;
    font-weight: bold;
    text-transform: uppercase;
    padding-top: 5px;
    color: rgba(0,0,0,0.7);
  }

  .widget .right-panel .temp
  {
    /*font-size: 81px;*/
    color: rgba(0,0,0,0.9);
    font-weight: 100;
    margin-left: 1.8vw;
  }

  .widget .panel
  {
    display: inline-block;
    background-color: transparent;
    box-shadow: none;
  }

  .widget .right-panel
  {
    position: absolute;
    float: right;
    top: 0;
    margin-top: 25px;
  }

@import url(https://fonts.googleapis.com/css?family=Poppins);

</style>
@stop
@section('header')
<section class="content-header" style="padding-top: 0; padding-bottom: 0;">

</section>
@endsection
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <div>
      <center>
        <span style="font-size: 3vw; text-align: center;"><i class="fa fa-spin fa-hourglass-half"></i></span>
      </center>
    </div>
  </div>
  <div class="row">
      <div id="main-body">

        <img src="{{url("images/ympi_map_fixR2.png")}}" alt="My logo" style="opacity: 0.8;margin-top: 10px;" width="1400px">
      
        <a title="3D"  style="cursor:pointer;">
          <div class="temp-widget-small text-center bg-orange-active" style="position: absolute; top: 585px; left: 495px;">
            <div style="padding: 0px 4px;" id="3D" onclick="searchAsset(this)">3D</div>
          </div>
        </a>

        <a title="Analyzing"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 335px; left: 95px;">
            <div style="padding: 0px 4px;" id="Analyzing" onclick="searchAsset(this)">Analyzing</div>
          </div>
        </a>

        <a title="Assy Saxophone"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 435px; left: 245px;">
            <div style="padding: 0px 4px;" id="assy_saxophone" onclick="searchAsset(this)">Assy Saxophone</div>
          </div>
        </a>

        <a title="Assy Clarinet"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 475px; left: 245px;">
            <div style="padding: 0px 4px;" id="assy_clarinet" onclick="searchAsset(this)">Assy Clarinet</div>
          </div>
        </a>

        <a title="Assy Flute"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 510px; left: 245px;">
            <div style="padding: 0px 4px;" id="assy_flute" onclick="searchAsset(this)">Assy Flute</div>
          </div>
        </a>

        <a title="Barrel"  style="cursor:pointer;">
          <div class="temp-widget-small text-center bg-orange-active" style="position: absolute; top: 580px; left: 45px;">
            <div style="padding: 0px 4px;" id="barell" onclick="searchAsset(this)">Barrel</div>
          </div>
        </a>

        <a title="Canteen"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 205px; left: 360px;">
            <div style="padding: 0px 4px;" id="canteen" onclick="searchAsset(this)">Canteen</div>
          </div>
        </a>

        <a title="Utility Room"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 235px; left: 450px;">
            <div style="padding: 0px 4px;" id="utility" onclick="searchAsset(this)">Utility Room</div>
          </div>
        </a>

        <a title="Body Proses Sax"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 505px; left: 420px;">
            <div style="padding: 0px 4px;" id="body_proses_sax" onclick="searchAsset(this)">Body Proses Sax</div>
          </div>
        </a>

        <a title="Buffing"  style="cursor:pointer;">
          <div class="temp-widget-small text-center bg-orange-active" style="position: absolute; top: 580px; left: 125px;">
            <div style="padding: 0px 4px;" id="buffing" onclick="searchAsset(this)">Buffing</div>
          </div>
        </a>

        <a title="Burner"  style="cursor:pointer;">
          <div class="temp-widget-small text-center bg-orange-active" style="position: absolute; top: 620px; left: 245px;">
            <div style="padding: 0px 4px;" id="burner" onclick="searchAsset(this)">Burner</div>
          </div>
        </a>

        <a title="Case"  style="cursor:pointer;">
          <div class="temp-widget-small text-center bg-orange-active" style="position: absolute; top: 620px; left: 545px;">
            <div style="padding: 0px 4px;" id="case" onclick="searchAsset(this)">Case</div>
          </div>
        </a>

        <a title="CL Body"  style="cursor:pointer;">
          <div class="temp-widget-small text-center bg-orange-active" style="position: absolute; top: 550px; left: 465px;">
            <div style="padding: 0px 4px;" id="cl_body" onclick="searchAsset(this)">CL Body</div>
          </div>
        </a>

        <a title="Cuci Asam"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 620px; left: 295px;">
            <div style="padding: 0px 4px;" id="cuci_asam" onclick="searchAsset(this)">Cuci Asam</div>
          </div>
        </a>

        <a title="GTC"  style="cursor:pointer;">
          <div class="temp-widget-small text-center bg-orange-active" style="position: absolute; top: 540px; left: 615px;">
            <div style="padding: 0px 4px;" id="gtc" onclick="searchAsset(this)">GTC</div>
          </div>
        </a>

        <a title="Plating"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 440px; left: 70px;">
            <div style="padding: 0px 4px;" id="plating" onclick="searchAsset(this)">Plating</div>
          </div>
        </a>

        <a title="WWT"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 240px; left: 170px;">
            <div style="padding: 0px 4px;" id="wwt" onclick="searchAsset(this)">WWT</div>
          </div>
        </a>

        <a title="Solder"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 560px; left: 270px;">
            <div style="padding: 0px 4px;" id="solder" onclick="searchAsset(this)">Solder</div>
          </div>
        </a>

        <a title="HTS"  style="cursor:pointer;">
          <div class="temp-widget-small text-center bg-orange-active" style="position: absolute; top: 580px; left: 220px;">
            <div style="padding: 0px 4px;" id="hts" onclick="searchAsset(this)">HTS</div>
          </div>
        </a>

        <a title="Injection"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 620px; left: 1020px;">
            <div style="padding: 0px 4px;" id="injection" onclick="searchAsset(this)">Injection</div>
          </div>
        </a>

        <a title="Shodon"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 450px; left: 1220px;">
            <div style="padding: 0px 4px;" id="shodon" onclick="searchAsset(this)">Khoki Shodon</div>
          </div>
        </a>

        <a title="Pianica"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 450px; left: 920px;">
            <div style="padding: 0px 4px;" id="pianica" onclick="searchAsset(this)">Pianica Lt2</div>
          </div>
        </a>

        <a title="MTC"  style="cursor:pointer;">
          <div class="temp-widget-small text-center bg-orange-active" style="position: absolute; top: 620px; left: 820px;">
            <div style="padding: 0px 4px;" id="mtc" onclick="searchAsset(this)">MTC</div>
          </div>
        </a>

        <a title="Workshop"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 600px; left: 800px;">
            <div style="padding: 0px 4px;" id="workshop" onclick="searchAsset(this)">Workshop</div>
          </div>
        </a>

        <a title="Warehouse"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 500px; left: 850px;">
            <div style="padding: 0px 4px;" id="warehouse" onclick="searchAsset(this)">Warehouse</div>
          </div>
        </a>

        <a title="Material Process"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 500px; left: 1050px;">
            <div style="padding: 0px 4px;" id="tpro" onclick="searchAsset(this)">Material Process</div>
          </div>
        </a>

        <a title="Tanpo"  style="cursor:pointer;">
          <div class="temp-widget-small text-center bg-orange-active" style="position: absolute; top: 620px; left: 495px;">
            <div style="padding: 0px 4px;" id="tanpo" onclick="searchAsset(this)">Tanpo</div>
          </div>
        </a>

        <a title="Stockroom"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 460px; left: 70px;">
            <div style="padding: 0px 4px;" id="stockroom" onclick="searchAsset(this)">Stockroom</div>
          </div>
        </a>

        <a title="Office"  style="cursor:pointer;">
          <div class="temp-widget text-center bg-orange-active" style="position: absolute; top: 255px; left: 550px;">
            <div style="padding: 0px 4px;" id="office" onclick="searchAsset(this)">Office</div>
          </div>
        </a>

        <div class="col-md-12" style="margin-top:20px;">
          <div class="col-md-2">
             <select class="form-control select2" id="location" name="location" data-placeholder="Select Location" style="width: 100%;border-color: #605ca8" >
                <option value=""></option>
                @foreach($location as $loc)
                <option value="{{ $loc->location }}">{{ $loc->location }}</option>
                @endforeach
              </select>
          </div>
        </div>
      </div>
  </div>
</section>


<div class="modal fade" id="modalAsset">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <center style="background-color: green">
          <span style="font-weight: bold; font-size: 1.5vw;color: white">Detail Fixed Asset Lokasi <span id="asset_lokasi"></span></span>
        </center>
        <hr>
        <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
          <form class="form-horizontal">
            <div class="col-xs-12" style="padding-bottom: 5px;">
        <center>
         <table id="listTable" class="table table-bordered table-striped table-hover">
            <thead style="background-color: rgba(126,86,134,.7);">
              <tr>
                <th>#</th>
                <th>SAP Number</th>
                <th>Asset Name</th>
                <th>Category</th>
                <th>Picture</th>
                <th>Invoice Number</th>
                <th>Vendor</th>
              </tr>
            </thead>
            <tbody id="listTableBody">
            </tbody>
            <tfoot>
              <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
              </tr>
            </tfoot>
          </table>
          </center>
        </div>
        </form>
        </div>
      </div>
    </div>
  </div>
</div>


@endsection
@section('scripts')

<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });


  jQuery(document).ready(function() {
    $('.select2').select2({
      dropdownAutoWidth : true,
      allowClear:true,
    });
  });

  $('.dateexport').datepicker({
    format: "yyyy-mm-dd",
    autoclose: true,
    todayHighlight: true
  });

  $(function () {

    $('.select5').select2({
      dropdownAutoWidth : true,
      allowClear:true,
    });

    $('.timepicker').timepicker({
      showInputs: false,
      showMeridian: false,
      defaultTime: '0:00',
    });
      
  });

  var alarm_error = new Audio('{{ url("sounds/alarm_error.mp3") }}');

  function getFormattedDate(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;
        
        return day + '-' + monthNames[month] + '-' + year;
    }

    function getFormattedTime(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;

        var hour = date.getHours();
        if (hour < 10) {
            hour = "0" + hour;
        }

        var minute = date.getMinutes();
        if (minute < 10) {
            minute = "0" + minute;
        }
        var second = date.getSeconds();
        
        return day + '-' + monthNames[month] + '-' + year +' '+ hour +':'+ minute;
    }


  function searchAsset(elem){

    $('#loading').show();

    var location = elem.id;

    var data = {
      location:location
    };

    $.get('{{ url("fetch/fixed_asset/map") }}', data, function(result, status, xhr) {
      if(result.status){
        $('#modalAsset').modal('show');
        $('#asset_lokasi').text(result.location);

        $('#listTable').DataTable().clear();
        $('#listTable').DataTable().destroy();        
        $('#listTableBody').html("");
        var listTableBody = "";

        $.each(result.asset, function(key, value) {
              listTableBody += '<tr>';
              listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
              listTableBody += '<td style="width:1%;">'+value.sap_number+'</td>';
              listTableBody += '<td style="width:1%;">'+value.fixed_asset_name+'</td>';
              listTableBody += '<td style="width:1%;">'+value.classification_category+'</td>';
              listTableBody += '<td style="width:1%;"><img style="width:150px" src="http://10.109.52.4/mirai/public/files/fixed_asset/asset_picture/'+value.picture+'" class="user-image" alt="User image" ></td>';
              listTableBody += '<td style="width:1%;">'+value.invoice_number+'</td>';
              listTableBody += '<td style="width:1%;">'+value.vendor+'</td>';
              listTableBody += '</tr>';
        }); 
        $('#listTableBody').append(listTableBody);

        $('#listTable tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
        } );

        var table = $('#listTable').DataTable({
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

        $('#listTable tfoot tr').appendTo('#listTable thead');
        $('#loading').hide();
      }
    });
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
@endsection