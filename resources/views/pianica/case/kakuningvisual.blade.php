@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link href="{{ url("css//bootstrap-toggle.min.css") }}" rel="stylesheet">
<style>
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  tr>th{
    text-align:center;
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
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }

  .tipe {
    margin-bottom: 10px;
    font-weight: bold;
    font-size: 2vw;
  }

  b{
    font-size: 25px;
  }
  table{
    font-size: 20px;
    font-weight: bold;
  }
  #loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
    <span class="text-purple"> 外観確認詳細</span>
  </h1>

  <ol class="breadcrumb">
    <span style="font-weight: bold; font-size: 25px">Kode Tanggal : <span style="color: purple;">{{ $date_code[0]->date_code }}</span></span>
  </ol>
</section>
@endsection
@section('content')
<section class="content">
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>

  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="row">
    <div class="col-xs-6 ">
     <div class="info-box">        
      <div class="info-box-content" style="margin:0px">
        <div class="row">
          <div class="col-xs-3">
            <b style="font-size: 20px">Operator :</b> 
          </div>
          <div class="col-xs-7">
            <span class="info-box-number" id="p_pureto_nama" style="font-size: 20px">-</span><b id="p_pureto" hidden></b> <b id="p_pureto_nik" hidden></b> 
          </div>
          <div class="col-xs-2">
            <button class="btn btn-warning pull-right" onclick="openmodal()"><i class="fa fa-refresh"></i> Change Operator</button>
          </div>
          <div class="col-xs-12">
            <hr style="margin-top: 2px; margin-bottom: 2px">
            <b>Tipe Case</b> 
            <b id="textmodel" style="color:red"> </b>
          </div>
          <div class="col-xs-12">
            <center><b class="destroy" id="modelb" style="font-size: 40px"></b></center>
          </div>
        </div>
        <div class="row" id="tipes">
          <div class="col-xs-3">
            <button class="btn btn-default btn-block tipe" style="border-color: blue" onclick="selectType('P25F')">P25F</button>
          </div>
          <div class="col-xs-3">
            <button class="btn btn-default btn-block tipe" style="border-color: blue" onclick="selectType('PS25F')">PS25F</button>
          </div>
          <div class="col-xs-3">
            <button class="btn btn-default btn-block tipe" style="border-color: blue" onclick="selectType('P32D')">P32D</button>
          </div>

          <div class="col-xs-3">
            <button class="btn btn-default btn-block tipe" style="border-color: blue" onclick="selectType('P32DSI')">P32DSI</button>
          </div>
          <div class="col-xs-3">
            <button class="btn btn-default btn-block tipe" style="border-color: blue" onclick="selectType('P32E')">P32E</button>
          </div>
          <div class="col-xs-3">
            <button class="btn btn-default btn-block tipe" style="border-color: blue" onclick="selectType('P37D')">P37D</button>
          </div>
          <div class="col-xs-3">
            <button class="btn btn-default btn-block tipe" style="border-color: blue" onclick="selectType('P37EBR')">P37EBR</button>
          </div>
          <div class="col-xs-3">
            <button class="btn btn-default btn-block tipe" style="border-color: blue" onclick="selectType('P37EBK')">P37EBK</button>
          </div>
          <div class="col-xs-3">
            <button class="btn btn-default btn-block tipe" style="border-color: blue" onclick="selectType('P37ERD')">P37ERD</button>
          </div>
          <div class="col-xs-3">
            <button class="btn btn-default btn-block tipe" style="border-color: blue" onclick="selectType('P32EP')">P32EP</button>
          </div>
        </div>
        <div class="row">
         <div class="col-xs-12">
          <input type="text" name="rfid2" id="rfid2" hidden="">
          <center><button class="btn btn-primary" onclick="changeType()"><i class="fa fa-refresh"></i> Change</button></center> <br>
        </div>
      </div>
    </div>
  </div>

</div>
<div class="col-xs-6">
  <div class="info-box">        
    <div class="info-box-content" style="margin:0px">
      <!-- <button class="btn btn-warning btn-lg pull-right" onclick="openmodal()"><i class="fa fa-refresh"></i> Change Operator</button>         
      <span class="info-box-text" style="font-size: 25px">OPERATOR Kakuning Visual Case</span>
      <span class="info-box-number" id="p_pureto_nama" style="font-size: 25px">[ ]</span><b id="p_pureto" hidden></b> <b id="p_pureto_nik" hidden></b>  -->
      <div class="table-responsive">
        <table width="100%"  class="table table-bordered table-striped" border="0" style="margin :0px">
          <tr>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black; padding-top: 2px; padding-bottom: 2px" >Total Check (Pcs)</td>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black; padding-top: 2px; padding-bottom: 2px" >OK (Pcs)</td>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black; padding-top: 2px; padding-bottom: 2px" >NG (Pcs)</td>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black; padding-top: 2px; padding-bottom: 2px" >Qty NG</td>
          </tr>
          <tr>
            <td style="font-size: 30px; background-color: #F0FFF0;border: 1px solid black;" valign="middle" id="total">0</td>
            <td style="font-size: 30px; background-color: #F0FFF0;border: 1px solid black;" valign="middle" id="bagus_total">0</td>
            <td style="font-size: 30px; background-color: pink;border: 1px solid black;" valign="middle" id="ng_total_pcs">0</td>
            <td style="font-size: 30px; background-color: pink;border: 1px solid black;" valign="middle" id="ng_total">0</td>
          </tr>
        </table>
        <br>
        <table width="100%"  class="table table-bordered table-striped" border="0" style="margin :0px">
          <tr>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black; padding-top: 2px; padding-bottom: 2px">REPAIR</td>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black; padding-top: 2px; padding-bottom: 2px">RETURN</td>
            <td style="background-color: rgba(126,86,134,.7); color: white;border: 1px solid black; padding-top: 2px; padding-bottom: 2px">SCRAP</td>
          </tr>
          <tr>
            <td style="font-size: 30px; background-color: pink;border: 1px solid black;" valign="middle" id="total_repair">0</td>
            <td style="font-size: 30px; background-color: pink;border: 1px solid black;" valign="middle" id="total_return">0</td>
            <td style="font-size: 30px; background-color: pink;border: 1px solid black;" valign="middle" id="total_scrap">0</td>
          </tr>
        </table>
      </div>
    </div>
  </div>   
</div>
</div>

<div class="row">
  <div class="col-xs-12">
    <div class="box box-solid">
      <div class="box box-body"> 
       <div class="nav-tabs-custom">
        <ul class="nav nav-tabs" style="font-size: 25px;">
          <li class="active" style="width: 14%"><center><a href="#tab_1" data-toggle="tab" style="color: black" id="Pianica-HardCase">NG HardCase</a></center></li>
          <li style="width: 14%"><center><a href="#tab_2" data-toggle="tab" style="color: black" id="Pianica-SoftCase">NG SoftCase</a></center></li>

          <li class="pull-right"><button class="btn btn-lg btn-success" onclick="simpan()"><i class="fa fa-save"></i>&nbsp;Save</button></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">
            <input id="ng" hidden>
            <div class="row">
              <?php for ($i=0; $i < count($ng_lists); $i++) { 
                if ($ng_lists[$i]->location == 'Pianica-HardCase') { ?>
                  <div style="padding-left: 5px; padding-right: 7px; margin-bottom: 10px; width: 14%; display: inline-block;">
                    <button style="width: 100%; white-space: normal; height: 50px; font-weight: bold; border-color: blue; font-size: 16px" class="btn btn-default ngs" onclick="low(this)" id="{{ str_replace('/', '', preg_replace('/\s+/', '_', $ng_lists[$i]->ng_name)) }}">{{ $ng_lists[$i]->ng_name }}</button>
                    <center class='stat' style="font-size: 12px; background-color: #debaf2">&nbsp;</center>
                  </div>
                <?php } } ?>
              </div>
            </div>

            <div class="tab-pane" id="tab_2">
              <div class="row">
                <?php for ($i=0; $i < count($ng_lists); $i++) { 
                  if ($ng_lists[$i]->location == 'Pianica-SoftCase') { ?>
                    <div style="padding-left: 5px; padding-right: 7px; margin-bottom: 10px; width: 14%; display: inline-block;">
                      <button style="width: 100%; white-space: normal; height: 50px; font-weight: bold; border-color: blue; font-size: 16px" class="btn btn-default ngs" onclick="low(this)" id="{{ str_replace('/', '', preg_replace('/\s+/', '_', $ng_lists[$i]->ng_name)) }}">{{ $ng_lists[$i]->ng_name }}</button>
                      <center class='stat' style="font-size: 12px; background-color: #debaf2">&nbsp;</center>
                    </div>
                  <?php } }?>
                </div>
              </div>
            </div>    
          </div>            
        </div>
      </div>
    </div>
  </div>

  <div class="modal modal-default fade" id="edit">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><center>YOUR RFID</center></h4>
        </div>
        <div class="modal-body" >
          <input type="text" name="op" id="op"  class="form-control" autofocus style="text-align: center;  font-size: 30px; height: 45px" placeholder="TAP RFID USER CARD HERE">
          <center><span style="color: red;font-weight: bold;font-size: 20px" id="pesan_skill"></span></center>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="display: none" id="ubahpureto2">Close</button>
          <button type="button" class="btn btn-primary pull-right btn-lg" style="display: none" id="ubahpureto" onclick="openpureto()" ><i class="fa fa-refresh"></i> Change</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal modal-default fade" id="status_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" style="font-weight: bold"><center>PILIH PENANGANAN NG</center></h4>
        </div>
        <div class="modal-body">
          <center>
            <input type="hidden" id="ng_name">
            <button class="btn btn-primary" id="rpr_btn" onclick="set_status_ng('repair')" style="width: 30%; font-weight: bold; font-size: 30px; margin-right: 2%">REPAIR</button>
            <button class="btn btn-warning" onclick="set_status_ng('return')" style="width: 30%; font-weight: bold; font-size: 30px; margin-right: 2%">RETURN</button>
            <button class="btn btn-danger" onclick="set_status_ng('scrap')" style="width: 30%; font-weight: bold; font-size: 30px">SCRAP</button>
          </center>
        </div>
      </div>
    </div>
  </div>

  @endsection

  @section('scripts')
  <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
  <script >

    var repair_item = [];
    var ngar = new Array();
    var ng_statuses = [];
    var ng_lists = <?php echo json_encode($ng_lists); ?>;
    var case_location = 'Pianica-HardCase';

    jQuery(document).ready(function() {
      gettotalng();
      $('.ngs').each(function(i, obj) {
        $(obj).attr('disabled', 'disabled');
      });

      $('#pesan_skill').html('');
      $('#op').val("");
      $('#ubahpureto').css({'display' : 'none'})
      $('#edit').modal({backdrop: 'static', keyboard: false});
      $('#edit').modal('show');
      $('#edit').on('shown.bs.modal', function() {
        $('#op').focus();
      })
      $('#entrydate').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
      });
      $('body').toggleClass("sidebar-collapse");

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
    });

    function low(elem) {
      var ng = $(elem).text();

      if(jQuery.inArray(ng, ngar) !== -1) {
        ngar = jQuery.grep(ngar, function(value) {
          return value != ng;
        });

        $('#ng').val(ngar);
        $(elem).css('background-color', '#f4f4f4');

        repair_item = repair_item.filter(function( obj ) {
          return obj.ng_name !== ng;
        });

        $(elem).parent().children('.stat').html("&nbsp;");
      } else {
        ngar.push(ng);
        $('#ng').val(ngar);
        $(elem).css('background-color', '#bad5f2');

        $('#status_modal').modal({backdrop: 'static', keyboard: false})  
        $("#ng_name").val(ng);

        $(ng_lists).each(function(index, value) {
          if (value.ng_name == ng && value.remark == 'with return') {
            $("#rpr_btn").show();
            return false;
          } else {
            $("#rpr_btn").hide();
          }
        })

      }
    }  


    $('#op').keydown(function(event) {
      if (event.keyCode == 13 || event.keyCode == 9) {
        // if($("#op").val().length == 10){
        var pureto = $('#op').val();
        $('#p_pureto').text(pureto);

        getpureto();        
        // }
        // else{
        //   $("#op").val("");
        //   alert('Error!', 'RFID number invalid.');
        // }
      }
    }); 

    function selectType(tipe) {
      $('#modelb').text(tipe);
      $('.tipe').prop('disabled', true);
      $('#tipes').hide();

      var stts = false;

      $(ng_statuses).each(function(index, value) {
        if (value.operator == $('#p_pureto_nik').text() && value.type == $('#modelb').text()) {
          stts = true;
          $("#total_repair").text(value.repair);
          $("#total_return").text(value.return);
          $("#total_scrap").text(value.scrap);
        }
      })

      if (!stts) {
        $("#total_repair").text(0);
        $("#total_return").text(0);
        $("#total_scrap").text(0);
      }

      var softcase = ['P32DSI','P37ERD', 'P37EBK', 'P37EBR'];

      if (softcase.includes(tipe)) {
        $('#Pianica-SoftCase').trigger('click');
      } else {
        $('#Pianica-HardCase').trigger('click');
      }

      $('.ngs').each(function(i, obj) {
        $(obj).removeAttr('disabled');
      });
    }

    function changeType() {
      $('.tipe').prop('disabled', false);
      $('#modelb').text("");
      $('#tipes').show();

      $('.ngs').each(function(i, obj) {
        $(obj).attr('disabled', 'disabled');
      });
    } 


    function openmodal() {
      $('#pesan_skill').html('');
      $('#ubahpureto2').css({'display' : 'block'})
      $('#ubahpureto').css({'display' : 'block'})
      $('#edit').modal('show');
      $('#op').prop('disabled', true);
    }         

    function openpureto() {
      $('#op').val("");
      $('#op').removeAttr('disabled');
      $('#op').focus();
    }


    function simpan() {
      if ($('#modelb').text() == '') {
        openErrorGritter('Gagal', 'Mohon Pilih Tipe Case');
        return false;
      }

      $( ".stat" ).each(function() {
        $( this ).html( "&nbsp;" );
      });

      $('#loading').show();

      var type = $('#modelb').text();
      var op = $('#p_pureto_nik').text();

      var a = "{{Auth::user()->name}}";
      var line = a.substr(a.length - 1);
      var location = "Case_Kakuning_Visual";
      var qty = 1;
      var ng = $('#ng').val();

      var data = {
        type:type,
        op:op,
        line:line,
        location:location,
        qty:qty,
        ng:ng,
        ng_status:repair_item
      }
      $.post('{{ url("post/case_pn/KakuningVisual") }}', data, function(result, status, xhr){
        if(xhr.status == 200){
          if(result.status){

            $('#ng').val('');

            repair_item = [];
            $("#ng_name").val('');

            ngar = [];

            $('.ngs').each(function(i, obj) {
              $(obj).css('background-color', '#f4f4f4');
            });

            openSuccessGritter('Success!', result.message);
            gettotalng();

            $('#loading').hide();
          }
          else{
            $('#loading').hide();
            openErrorGritter('Error!', result.message);
          }
        }
        else{
          $('#loading').hide();
          alert("Disconnected from server");
        }
      });

    }

    function getpureto() {
     var data = {
      pureto:$('#op').val(),
      op:'Kakuning Case',
    }
    $('#loading').show();
    $.get('{{ url("index/op_Pureto") }}', data, function(result, status, xhr){

      if(xhr.status == 200){
        if(result.status){
          $("#edit").modal('hide');
          $('#p_pureto_nama').text(result.nama);
          $('#p_pureto_nik').text(result.nik);
          $('#loading').hide();
          openSuccessGritter('Success!', result.message);
        }
        else{
          $('#loading').hide();
          $('#op').val("");
          $('#op').focus();
          openErrorGritter('Error!', result.message);
        }
      }
      else{
        $('#loading').hide();
        alert("Disconnected from server");
      }
    });

  }

  function gettotalng() {
    var a = "{{Auth::user()->name}}";
    var line = a.substr(a.length - 1);
    var data = {
      location : 'Case_Kakuning_Visual',
      line : line

    }

    $.get('{{ url("fetch/case_pn/total_ng") }}', data, function(result, status, xhr){
      if(xhr.status == 200){
        if(result.status){
          if (result.total[0].total) {
            $('#total').text(result.total[0].total);
            $('#bagus_total').text(result.total[0].oke);
            $('#ng_total_pcs').text(result.total[0].ng);
            $('#ng_total').text(result.total_ng[0].ng);
          }

          if (result.ng_status.length > 0) {
            ng_statuses = result.ng_status;

            $(result.ng_status).each(function(index, value) {
              if (value.operator == $('#p_pureto_nik').text() && value.type == $('#modelb').text()) {
                $("#total_repair").text(value.repair);
                $("#total_return").text(value.return);
                $("#total_scrap").text(value.scrap);
              }
            })
          }
        }
        else{                
          openErrorGritter('Error!', result.message);
        }
      }
      else{

        alert("Disconnected from server");
      }
    });

  }

  function set_status_ng(status) {
    ng = $("#ng_name").val();

    ngs = ng.replace(/ /g, "_");
    ngs = ngs.replace("/", "");

    $("#"+ngs).parent().children('.stat').html(status.toUpperCase());

    repair_item.push({'ng_name' : ng, 'status' : status, 'location' : case_location});

    console.log(repair_item);

    $("#status_modal").modal('hide');
  }

  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    case_location = $(e.target).attr('id');
  })

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