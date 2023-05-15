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

  b{
    font-size: 25px;
  }
  #loading, #error { display: none; }
</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    Detail {{ $page }}
    <span class="text-purple"> プレート詳細</span>
  </h1>

  <ol class="breadcrumb">
    {{-- <a href="javascript:void(0)"  data-toggle="modal" data-target="#edit" class="btn btn-warning btn-sm">Input</a> --}}
  </ol>
</section>
@endsection
@section('content')
<section class="content">
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 35%;">
      <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
    </p>
  </div>
  <!-- SELECT2 EXAMPLE -->
  <div class="row">
    <div class="col-xs-6">
      <div class="info-box">        
        <div class="info-box-content" style="margin:0px">
          <table width="100%">
            <tr>
              <td style="text-align: center; font-size: 30px; background-color: rgba(126,86,134,.7); color: white;">Total Production</td>
            </tr>
            <tr>
              <td style="text-align: center; font-size: 30px; background-color: #F0FFF0; color: black;" id="total">0</td>
            </tr>
          </table>
          <b>Tag RFID</b>  <br>
          <input type="text" name="rfid" id="rfid" class="form-control"  autofocus style="text-align: center; font-size: 30px; height: 45px" placeholder="RFID"><br>
          <center><button class="btn btn-lg btn-primary" onclick="rf()">Change</button></center>
          <span  ><b id="textmodel" style="color:red"> [ Model ] - </b><b class="destroy" id="modelb"></span><br>
            <div class="col-xs-12" style="padding: 0px">
              @foreach($models as $model) 
              @if($model == "P-37")
              <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-lg" onclick="model(this.id)" id="{{$model}}" style="width:100%; background-color:  #800000; color: white">{{$model}}</button></div>
              @elseif($model == "P-32")
              <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-lg" onclick="model(this.id)" id="{{$model}}" style="width:100%; background-color:  rgb(135,206,250); color: black">{{$model}}</button></div>
              @else
              <div class="col-xs-4"style="padding: 0px 5px 0px 5px" ><button class="btn btn-lg" onclick="model(this.id)" id="{{$model}}" style="width:100%; background-color:  rgb(240,230,140); color: black">{{$model}}</button></div>
              @endif
              @endforeach
              <br>          
            </div>
            &nbsp;
          </div>
        </div>
      </div>


      <div class="col-xs-6">
       <div class="info-box">
        <div class="info-box-content">
          <div class="col-xs-12">
            <button class="btn btn-warning btn-lg pull-right" onclick="openmodal()">Change Operator Pureto</button>
            <button class="btn btn-primary btn-lg pull-right" style="margin-right: 10px;" onclick="openModalPerolehan()">Audit Screw</button>
          </div>
          <div class="col-xs-12">
            <span class="info-box-text" style="font-size: 25px">OPERATOR PURETO</span>
            <span class="info-box-number" id="p_pureto_nama" style="font-size: 25px; color:blue">[ ]</span><b id="p_pureto" hidden></b> <b id="p_pureto_nik" hidden></b><input type="hidden" name="employee_id" id="employee_id">
            <span class="info-box-text" style="font-size: 25px">RFID</span>
            <span class="info-box-number" id="p_rfid" style="font-size: 25px; color:blue">[ ]</span>
            <span class="info-box-text" style="font-size: 25px">MODEL</span>
            <span class="info-box-number" id="p_model" style="font-size: 25px;color:blue">[ ]</span>
            <span class="info-box-text" style="font-size: 25px">OPERATOR BENSUKI</span>
            <span class="info-box-number" id="p_bensuki" style="font-size: 25px; color:blue">[ ] </span><b id="nikbensuki" hidden></b>
          </div>
        </div>
      </div>
    </div>

    

    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box box-body">
          <span ><b id="opbentetx" style="color:red"> [ Op Bensuki ] - </b> <b class="destroy" id="posisi"></b> <b class="destroy" id="opben"></b></span><br>
          <div class="table-responsive">

            <div class="col-xs-4 table-responsive">
              <table>
                <tr><td colspan="6" style="padding: 10px" align="center">HIGH</td></tr>
                <tr>

                  @foreach($highs  as $nomor => $highs)
                  @if($highs->warna =="M" )
                  <td style="padding: 10px"><button class="btn btn-lg btn-danger" id="{{ $highs->nama}}" name="{{ $highs->nik}}"onclick="opben('HIGH',this.id,this.name,this)">
                    {{$a = explode('-', trim($highs->kode))[0]}}</button></td>
                    @endif                
                    @endforeach
                  </tr>
                  <tr>
                    @foreach($high  as $nomor => $high)
                    @if($high->warna =="H" )
                    <td style="padding: 10px"><button class="btn btn-lg " style="background-color: black; color: white" id="{{ $high->nama}}" name="{{ $high->nik}}"onclick="opben('HIGH',this.id,this.name,this)">{{$a = explode('-', trim($high->kode))[0]}}</button></td>
                    @endif                
                    @endforeach                
                  </tr>
                </table>                       
              </div> 

              
              <div class="col-xs-4 table-responsive">
                <table>
                  <tr><td colspan="6" style="padding: 10px" align="center">MIDDLE</td></tr>
                  <tr>

                    @foreach($middles  as $nomor => $middles)
                    @if($middles->warna =="M" )
                    <td style="padding: 10px"><button class="btn btn-lg btn-danger" id="{{ $middles->nama}}" name="{{ $middles->nik}}"  onclick="opben('MIDDLE',this.id,this.name,this)">
                      {{$a = explode('-', trim($middles->kode))[0]}}</button></td>
                      @endif                
                      @endforeach
                    </tr>
                    <tr>
                      @foreach($middle  as $nomor => $middle)
                      @if($middle->warna =="H" )
                      <td style="padding: 10px"><button class="btn btn-lg " style="background-color: black; color: white" id="{{ $middle->nama}}" name="{{ $middle->nik}}"onclick="opben('MIDDLE',this.id,this.name,this)">{{$a = explode('-', trim($middle->kode))[0]}}</button></td>
                      @endif                
                      @endforeach                
                    </tr>
                  </table>
                </div>

                <div class="col-xs-4 table-responsive">
                  <table>
                    <tr><td colspan="6" style="padding: 10px" align="center">LOW</td></tr>
                    <tr>                  
                      @foreach($lows  as $nomor => $lows)
                      @if($lows->warna =="M" )
                      <td style="padding: 10px"><button class="btn btn-lg btn-danger" id="{{ $lows->nama}}" name="{{ $lows->nik}}" onclick="opben('LOW',this.id,this.name,this)">
                        {{$a = explode('-', trim($lows->kode))[0]}}</button></td>
                        @endif                
                        @endforeach
                      </tr>
                      <tr>
                        @foreach($low  as $nomor => $low)
                        @if($low->warna =="H" )
                        <td style="padding: 10px"><button class="btn btn-lg " style="background-color: black; color: white" id="{{ $low->nama}}" name="{{ $low->nik}}" onclick="opben('LOW',this.id,this.name,this)">{{$a = explode('-', trim($low->kode))[0]}}</button></td>
                        @endif                
                        @endforeach                
                      </tr>
                    </table>
                  </div>

                  <button class="btn btn-lg btn-success pull-right" onclick="simpan()" style="margin: 0px 0px 0px 0px; " >Save</button>             
                </div>
              </div>            
            </div>
          </div>

        <div class="modal modal-default fade" id="edit">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title">Your RFID </h4>
              </div>
              <div class="modal-body" >
                <span>RFID</span>
                <input type="text" name="oppureto" id="oppureto"  class="form-control" autofocus style="text-align: center;  font-size: 30px; height: 45px" placeholder="RFID">
                
              </div>
              <div class="modal-footer">
               <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="display: none" id="ubahpureto2">Close</button>
               <button type="button" class="btn btn-primary pull-right btn-lg" style="display: none" id="ubahpureto" onclick="openpureto()">Change</button>
               {{-- <a id="modalEditButton" href="#" type="button" class="btn btn-outline">Confirm</a> --}}
             </div>
           </div>
         </div>
       </div>

       <div class="modal modal-default fade" id="modalPerolehan">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header" style="background-color: orange;color: white">
                <h4 class="modal-title" style="font-size: 20px;font-weight: bold;">Audit Screw</h4>
              </div>
              <div class="modal-body">
                <div class="col-xs-6" style="padding-left: 0px;padding-right: 5px;">
                  <center><span style="font-size: 20px;font-weight: bold;">PEROLEHAN</span></center>
                  <table id="tablePerolehan" class="table table-bordered" style="margin:0;margin-top: 10px;">
                    <thead>
                      <tr>
                        <th>Model</th>
                        <th>Qty</th>
                        <th>Screw</th>
                      </tr>
                    </thead>
                    <tbody id="bodyTablePerolehan">
                      <tr>
                        <td>0</td>
                        <td>0</td>
                        <td>0</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-xs-4">
                </div>
              </div>
              <div class="modal-footer">
               <button type="button" class="btn btn-primary pull-right btn-lg" style="display: none" id="ubahpureto" onclick="openpureto()">Change</button>
             </div>
           </div>
         </div>
       </div>

       @endsection

       @section('scripts')
       <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
       <script >

        jQuery(document).ready(function() {
          $('#oppureto').focus();
          gettotalng('biasa');
          $('#oppureto').val("");
          $('#rfid').val("");
          $('#ubahpureto').css({'display' : 'none'})
          $('#edit').modal({backdrop: 'static', keyboard: false});
          $('#edit').modal('show');
          $('#edit').on('shown.bs.modal', function() {
            $('#oppureto').focus();
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

        $('#oppureto').keydown(function(event) {
          if (event.keyCode == 13 || event.keyCode == 9) {
            if($("#oppureto").val().length == 10){
              pureto(); 
              getpureto();
              gettotalng('biasa');
              return false;
            }
            else{
              $("#oppureto").val("");
              alert('Error!', 'RFID number invalid.');
            }
          }
        }); 

        $('#rfid').keydown(function(event) {
          if (event.keyCode == 13 || event.keyCode == 9) {
            if($("#rfid").val().length == 10){
              check_tag();
            }
            else{
              $("#rfid").val("");
              alert('Error!', 'RFID number invalid.');
            }
          }
        });

        function openModalPerolehan() {
          gettotalng('perolehan');
          $('#modalPerolehan').modal('show');
        }

        function rf() {            
          $('#rfid').val("");
          $('#rfid').removeAttr('disabled');
          $('#rfid').focus();
          $('#p_rfid').text("[ ]");
        } 

        function check_tag() {
          var data ={
            tag : $('#rfid').val()
          }
          
          $.get('{{ url("fetch/check_tag") }}', data, function(result, status, xhr){
            if (result.tag > 0) {
              openErrorGritter('Error', 'Tag Masih Digunakan');
            } else {
              openSuccessGritter('Sukses', '');
              $('#rfid').prop('disabled', true);
              var id = $('#rfid').val();           
              $('#p_rfid').text(id);
              return false;
            }
          })
        }


        function openmodal() {
          $('#ubahpureto2').css({'display' : 'block'})
          $('#ubahpureto').css({'display' : 'block'})
          $('#edit').modal('show');
          $('#oppureto').prop('disabled', true);
        }         

        function pureto() {
          $("#loading").show();
          var pureto = $('#oppureto').val();
          $('#p_pureto').text(pureto);
          $("#loading").hide();
        }       

        function openpureto() {
          $('#oppureto').val("");
          $('#oppureto').removeAttr('disabled');
          $('#oppureto').focus();
        }
        function model(id) {
          $('#modelb').text(""+id+" ");
          $('#p_model').text(id);
          $('#textmodel').css({'color':'black'})
        }

        function opben(group,id,nik,kode) {
          var code = $(kode).text().trim()+"-"+group;
          $('#opben').text(" "+id+"");
          $('#posisi').text(" "+group+"-");
          $('#nikbensuki').text(nik);
          $('#kode').text(code);
          $('#p_bensuki').text(group+"- "+id);             
          $('#opbentetx').css({'color':'black'});
        }

        function simpan() {
          $("#loading").show();


          var tag = $('#p_rfid').text();
          var model = $('#p_model').text();
          var pureto = $('#p_pureto_nik').text();
          var bensuki = $('#nikbensuki').text();
          var a = "{{Auth::user()->name}}";
          var line = a.substr(a.length - 1);
          var location ="PN_Pureto";
          var qty = 1;
          var status = 1;

          if(tag == '[ ]' || model == '[ ]' || pureto == '' || bensuki == ''){
            $("#loading").hide();
            alert('All field must be filled');  
          }else{
            $("#loading").show();
            var data = {
              tag:tag,
              model:model,
              pureto:pureto,
              bensuki:bensuki,
              line:line,
              location:location,
              qty:qty,
              status:status,
            }
            $.post('{{ url("index/SavePureto") }}', data, function(result, status, xhr){
              if(xhr.status == 200){
                if(result.status){
                  $('#opbentetx').css({'color':'red'});
                  $('#textmodel').css({'color':'red'});
                  $('#opben').text("");
                  $('#posisi').text("");
                  $('#modelb').text("");
                  $('#p_bensuki').text("[ ]");            
                  $('#p_rfid').text("[ ]");
                  $('#p_model').text("[ ]");
                  $('#rfid').val("");
                  $('#rfid').removeAttr('disabled');
                  $('#rfid').focus();
                  openSuccessGritter('Success!', result.message);
                  gettotalng('biasa');
                  $("#loading").hide();
                }
                else{
                  $("#loading").hide();
                  openErrorGritter('Error!', result.message);
                }
              }
              else{
                $("#loading").hide();
                alert("Disconnected from server");
              }
            });
          }        
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


        function getpureto() {
          $("#loading").show();
         var pureto = $('#oppureto').val();
         var data ={
          pureto:pureto,
          op:'Pureto',
        }
        $.get('{{ url("index/op_Pureto") }}', data, function(result, status, xhr){
          if(xhr.status == 200){
            if(result.status){
              $('#p_pureto_nama').text(result.nama);
              $('#p_pureto_nik').text(result.nik);
              $('#employee_id').val(result.nik);
              $('#edit').modal('hide');
              $('#rfid').focus();
                // $('#tag_material').val(result.tag);
                openSuccessGritter('Success!', result.message);
                $("#loading").hide();
              }
              else{
                $("#loading").hide();
               $('#oppureto').val("");
                // $('#oppureto').removeAttr('disabled');
                $('#oppureto').focus();
                openErrorGritter('Error!', result.message);
              }
            }
            else{
              $("#loading").hide();
              alert("Disconnected from server");
            }
          });

      }

      function gettotalng(type) {
        $("#loading").show();
       var tag = $('#rfid').val();
       var a = "{{Auth::user()->name}}";
       var line = a.substr(a.length - 1);
       var data ={
        location:'PN_Pureto',
        line:line,
        employee_id:$('#employee_id').val(),
        type:type

      }
      $.get('{{ url("index/TotalNg") }}', data, function(result, status, xhr){
        if(xhr.status == 200){
          if(result.status){
            $('#total').text(result.total[0].total);
            openSuccessGritter('Success!', result.message);

            if (result.perolehan != null) {
              $("#loading").show();
              $('#bodyTablePerolehan').html('');
              var perolehan = '';

              for(var i = 0; i < result.perolehan.length;i++){
                perolehan += '<tr>';
                perolehan += '<td>'+result.perolehan[i].model+'</td>';
                perolehan += '<td>'+result.perolehan[i].qty+'</td>';
                perolehan += '<td>'+result.perolehan[i].screw+'</td>';
                perolehan += '</tr>';
              }
              $('#bodyTablePerolehan').append(perolehan);
              $("#loading").hide();
            }
            $("#loading").hide();
          }
          else{                
            $("#loading").hide();
            openErrorGritter('Error!', result.message);
          }
        }
        else{
          $("#loading").hide();
          alert("Disconnected from server");
        }
      });

    }




  </script>
  @stop