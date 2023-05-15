@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  input[type=checkbox] {
    transform: scale(1.25);
  }
  thead>tr>th{
    /*text-align:center;*/
    background-color: #7e5686;
    color: white;
    border: none;
    border:1px solid black;
    border-bottom: 1px solid black !important;
  }
  tbody>tr>td{
    /*text-align:center;*/
    border: 1px solid black;
  }
  tfoot>tr>th{
    /*text-align:center;*/
  }
  td:hover {
    overflow: visible;
  }
  table.table-hover > tbody > tr > td{
    border:1px solid #eeeeee;
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
  .isi{
    background-color: #f5f5f5;
    color: black;
    padding: 10px;
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Check & Verifikasi {{ $page }}
    <small>Form PR</small>
  </h1>
  <ol class="breadcrumb">
    <!-- @if($pr->posisi == "staff")
    <button class="btn btn-sm btn-warning pull-right" data-toggle="tooltip" title="Edit" onclick="editPR({{$pr->id}})">Update Purchase Requisition</button>

    @elseif($pr->posisi == "pch")
    <button class="btn btn-sm btn-warning pull-right" data-toggle="tooltip" title="Edit" onclick="editPR({{$pr->id}})">Update Purchase Requisition</button>
    @endif -->
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
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Not Verified!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
      <div class="box-body">

        @if($pr->posisi == "staff")

        <form role="form" method="post" action="{{url('purchase_requisition/checked/'.$pr->id)}}">

          <input type="hidden" value="{{csrf_token()}}" name="_token" />  

          <table class="table table-bordered">
            <tr id="show-att">
              <td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;" colspan="2" id="text_attach_1">
              </td>
            </tr>
          </table>

          <div class="col-sm-12" style="margin-top: 20px;padding: 0">
              <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
          </div>
        </form>

        @elseif($pr->posisi == "pch")

        <form role="form" method="post" action="{{url('purchase_requisition/checked/'.$pr->id)}}">

          <input type="hidden" value="{{csrf_token()}}" name="_token" />  

          <table class="table table-bordered">
            <tr id="show-att">
              <td colspan="9" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 75%;"  id="text_attach_1">
              </td>
              <td colspan="3" style="font-size: 16px;width: 25%;">

                <?php if ($pr->receive_date == null) { ?>

                <div class="col-sm-12" style="margin-top: 20px;padding: 0">
                    <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Receive PR</button>
                </div>

                <div class="col-md-12" style="margin-top:20px;padding: 0;">
                  <div class="panel panel-default">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <input type="hidden"  name="approve" id="approve" value="1" />
                    <div class="panel-heading" style="background-color:orange">Feedback : </div>
                    <div class="panel-body center-text"  style="background-color:orange;padding: 20px">
                      <a data-toggle="modal" data-target="#notapproved" class="btn btn-primary col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Comment</a>
                    </div>
                  </div>
                </div>

                <?php } ?>
              </td>
            </tr>
          </table>

        </form>

        @else

        <table class="table table-bordered">
            <tr id="show-att">
              <td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;" colspan="2" id="text_attach_1">
              </td>
            </tr>
          </table>

        @endif

      </div>
    </form>
  </div>

  <div class="modal modal-default fade" id="notapproved" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('purchase_requisition/comment/'.$pr->id)}}">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="myModalLabel">Give FeedBack</h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <textarea class="form-control" required="" name="alasan" style="height: 250px;"></textarea> 
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Submit Comment</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade in" id="modalEdit">
    <form id ="importFormEdit" name="importFormEdit" method="post" action="{{ url('update/purchase_requisition') }}">
      <input type="hidden" value="{{csrf_token()}}" name="_token" />
      <div class="modal-dialog modal-lg" style="width: 1300px">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">Edit Purchase Requisition</h4>
            <br>
            <h4 class="modal-title" id="modalDetailTitle"></h4>

            <div class="row">
              <div class="col-md-12">

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Identitas</label>
                    <input type="text" class="form-control" id="identitas_edit" name="identitas_edit" placeholder="Identitas">
                  </div>
                  <div class="form-group">
                    <label>No PR</label>
                    <input type="text" class="form-control" id="no_pr_edit" name="no_pr_edit" placeholder="PR Number">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Departemen</label>
                    <input type="text" class="form-control" id="departemen_edit" name="departemen_edit" placeholder="Departemen">
                  </div>
                  <div class="form-group">
                    <label>No Budget</label>
                    <input type="text" class="form-control" id="no_budget_edit" name="no_budget_edit" placeholder="No Budget">
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Tanggal Pengajuan</label>
                    <input type="text" class="form-control" id="tgl_pengajuan_edit" name="tgl_pengajuan_edit" placeholder="Tanggal Pengajuan">
                  </div>
                </div>


              </div>
            </div>
            <div >
              <div class="col-md-12" style="margin-bottom : 5px">
                <div class="col-xs-1" style="padding:5px;">
                  <b>Kode Item</b>
                </div>
                <div class="col-xs-2" style="padding:5px;">
                  <b>Deskripsi</b>
                </div>
                <div class="col-xs-2" style="padding:5px;">
                  <b>Spesifikasi</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>UOM</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Request Date</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Mata Uang</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Harga</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Jumlah</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Total</b>
                </div>
                <div class="col-xs-1" style="padding:5px;">
                  <b>Aksi</b>
                </div>
              </div>

              <div  id="modalDetailBodyEdit">
              </div>
            </div>
            <br>

            <div id="tambah2">
              <input type="text" name="lop2" id="lop2" value="1" hidden="">
              <input type="text" name="looping" id="looping" hidden="">
            </div>
            
          </div>
          <div class="modal-footer">
            <input type="hidden" class="form-control" id="id_edit" name="id_edit" placeholder="ID">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-warning">Update</button>
          </div>
        </div>
      </div>
    </form>
  </div>

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
    no = 2;
    item_list = "";
    limitdate = "";

    $(document).ready(function() {
      getItemList();

      limitdate = new Date();
      limitdate.setDate(limitdate.getDate());

      $("body").on("click",".btn-danger",function(){ 
        $(this).parents(".control-group").remove();
      });

      $('body').toggleClass("sidebar-collapse");

      var showAtt = "{{$pr->file_pdf}}";
      var path = "{{$file_path}}";

      // console.log(path);
      
      if(showAtt.includes('.pdf')){
        $('#text_attach_1').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='800px'>");
      }

      if(showAtt.includes('.png') || showAtt.includes('.PNG')){
        $('#text_attach_1').append("<embed src='"+ path +"' width='100%' height='800px'>");
      }

      if(showAtt.includes('.jp') || showAtt.includes('.JP')){
        $('#text_attach_1').append("<embed src='"+ path +"' width='100%' height='800px'>");
      }
    });


    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    function getItemList() {
      $.get('{{ url("fetch/purchase_requisition/itemlist") }}', function(result, status, xhr) {
        item_list += "<option></option> ";
        $.each(result.item, function(index, value){
          // item.push({
          //  'kode_item' :  value.kode_item, 
          //  'deskripsi' :  value.deskripsi,
          // });
          // item_list += "<option></option>";
          item_list += "<option value="+value.kode_item+">"+value.kode_item+ " - " +value.deskripsi+"</option> ";
        });
        $('#item_code1').append(item_list);

      })
    }

    function tambah(id,lop) {
      var id = id;

      var lop = "";

      if (id == "tambah"){
        lop = "lop";
      }else{
        lop = "lop2";
      }

      var divdata = $("<div id='"+no+"' class='col-md-12' style='margin-bottom : 5px'><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' data-placeholder='Choose Item' name='item_code"+no+"' id='item_code"+no+"' onchange='pilihItem(this)'><option></option></select></div><div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='item_desc"+no+"' name='item_desc"+no+"' placeholder='Description' required=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_spec"+no+"' name='item_spec"+no+"' placeholder='Specification' required=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_stock"+no+"' name='item_stock"+no+"' placeholder='Stock' required=''></div><div class='col-xs-1' style='padding:5px;'><select class='form-control select3' id='uom"+no+"' name='uom"+no+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div><div class='col-xs-1' style='padding:5px;'><div class='input-group date'><div class='input-group-addon'><i class='fa fa-calendar' style='font-size: 10px'></i> </div><input type='text' class='form-control pull-right datepicker' id='req_date"+no+"' name='req_date"+no+"' placeholder='Tanggal' required=''></div></div> <div class='col-xs-1' style='padding: 5px'><select class='form-control select2' id='item_currency"+no+"' name='item_currency"+no+"'data-placeholder='Currency' style='width: 100%' onchange='currency(this)'><option value=''>&nbsp;</option><option value='USD'>USD</option><option value='IDR'>IDR</option><option value='JPY'>JPY</option></select><input type='text' class='form-control' id='item_currency_text"+no+"' name='item_currency_text"+no+"' style='display:none'></div> <div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga"+no+"' style='padding:3px'>?</span><input type='text' class='form-control currency' id='item_price"+no+"' name='item_price"+no+"' placeholder='Harga' data-number-to-fixed='2' data-number-stepfactor='100' required='' style='padding:6px 6px'></div></div><div class='col-xs-1' style='padding:5px;'><input type='number' class='form-control' id='qty"+no+"' name='qty"+no+"' placeholder='Qty' onkeyup='getTotal(this.id)' required=''></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='amount"+no+"' name='amount"+no+"' placeholder='Total' required='' readonly></div><div class='col-xs-1' style='padding:5px;'>&nbsp;<button onclick='kurang(this,\""+lop+"\");' class='btn btn-danger'><i class='fa fa-close'></i> </button> <button type='button' onclick='tambah(\""+id+"\",\""+lop+"\"); ' class='btn btn-success'><i class='fa fa-plus' ></i></button></div></div>");

      $("#"+id).append(divdata);
      $("#item_code"+no).append(item_list);


      $('.datepicker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        startDate: limitdate
      });

      $(function () {
        $('.select3').select2({
          dropdownAutoWidth : true,
          dropdownParent: $("#"+id),
          allowClear:true,
          minimumInputLength: 3
        });
      })

    // $("#"+id).select2().trigger('change');
    document.getElementById(lop).value = no;
    no+=1;
  }

    function editPR(id){
      var isi = "";
      $('#modalEdit').modal("show");
        
        var data = {
          id:id
      };
          
        $.get('{{ url("edit/purchase_requisition") }}', data, function(result, status, xhr){  

        $("#identitas_edit").val(result.purchase_requisition.emp_id+' - '+result.purchase_requisition.emp_name).attr('readonly', true);
        $("#departemen_edit").val(result.purchase_requisition.department).attr('readonly', true);
        $("#no_pr_edit").val(result.purchase_requisition.no_pr).attr('readonly', true);
        $("#no_budget_edit").val(result.purchase_requisition.no_budget).attr('readonly', true);
        $("#tgl_pengajuan_edit").val(result.purchase_requisition.submission_date).attr('readonly', true);
        $("#id_edit").val(result.purchase_requisition.id).attr('readonly', true);

            $('#modalDetailBodyEdit').html('');
            var ids = [];
        $.each(result.purchase_requisition_item, function(key, value) {

          // console.log(result.purchase_requisition_item);
          var tambah2 = "tambah2";
            var lop2 = "lop2";

          isi = "<div id='"+value.id+"' class='col-md-12' style='margin-bottom : 5px'>";
          if (value.item_code != null) {

            isi += "<input type='hidden' class='form-control' id='item_code_edit_hide"+value.id+"' name='item_code_edit_hide"+value.id+"' value="+value.item_code+">";

            isi += "<div class='col-xs-1' style='padding:5px;'><select class='form-control select5 item_code_edit' data-placeholder='Choose Item' name='item_code_edit"+value.id+"' id='item_code_edit"+value.id+"' style='width: 100% height: 35px;' onchange='pilihItemEdit(this)'><option></option></select></div>";

          }else{
            isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_code_edit"+value.id+"' name='item_code_edit"+value.id+"'></div>";
          }
          
          isi += "<div class='col-xs-2' style='padding:5px;'><input type='text' class='form-control' id='item_desc_edit"+value.id+"' name='item_desc_edit"+value.id+"' placeholder='Description' required='' value='"+value.item_desc+"'></div>";
          isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_spec_edit"+value.id+"' name='item_spec_edit"+value.id+"' placeholder='Specification' required='' value='"+value.item_spec+"'></div>";
          isi += "<div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='item_stock_edit"+value.id+"' name='item_stock_edit"+value.id+"' placeholder='Stock' required='' value='"+value.item_stock+"'></div>";
          isi += "<div class='col-xs-1' style='padding:5px;'><input type='hidden' name='uomhide"+value.id+"' id='uomhide"+value.id+"' value='"+value.item_uom+"'><select class='form-control select5' id='uom_edit"+value.id+"' name='uom_edit"+value.id+"' data-placeholder='UOM' style='width: 100%;'><option></option>@foreach($uom as $um)<option value='{{ $um }}'>{{ $um }}</option>@endforeach</select></div>";
          isi += "<div class='col-xs-1' style='padding:5px;'><div class='input-group date'><div class='input-group-addon'><i class='fa fa-calendar' style='font-size: 10px'></i> </div><input type='text' class='form-control pull-right datepicker' id='req_date_edit"+value.id+"' name='req_date_edit"+value.id+"' placeholder='Tanggal' required='' value='"+value.item_request_date+"''></div></div>";
          isi += "<div class='col-xs-1' style='padding: 5px'><input type='text' class='form-control' id='item_currency_edit"+value.id+"' name='item_currency_edit"+value.id+"' value='"+value.item_currency+"'><input type='text' class='form-control' id='item_currency_text_edit"+value.id+"' name='item_currency_text_edit"+value.id+"' style='display:none'></div>";
          isi += "<div class='col-xs-1' style='padding:5px;'><div class='input-group'><span class='input-group-addon' id='ket_harga_edit"+value.id+"' style='padding:3px'>?</span><input type='text' class='form-control currency' id='item_price_edit"+value.id+"' name='item_price_edit"+value.id+"' placeholder='Harga' data-number-to-fixed='2' data-number-stepfactor='100' required='' value="+value.item_price+" style='padding: 6px 6px'></div></div>";
          isi += "<div class='col-xs-1' style='padding:5px;'><input type='number' class='form-control' id='qty_edit"+value.id+"' name='qty_edit"+value.id+"' placeholder='Qty' onkeyup='getTotalEdit(this.id)' required='' value='"+value.item_qty+"'></div><div class='col-xs-1' style='padding:5px;'><input type='text' class='form-control' id='amount_edit"+value.id+"' name='amount_edit"+value.id+"' placeholder='Total' required='' value='"+value.item_amount+"' readonly=''></div>";
          isi += "<div class='col-xs-1' style='padding:5px;'><a href='javascript:void(0);' id='b"+ value.id +"' onclick='deleteConfirmation(\""+ value.item_desc +"\","+value.id +");' class='btn btn-danger' data-toggle='modal' data-target='#modaldanger'><i class='fa fa-close'></i> </a> <button type='button' class='btn btn-success' onclick='tambah(\""+ tambah2 +"\",\""+ lop2 +"\");'><i class='fa fa-plus' ></i></button></div>";
          isi += "</div>";

          ids.push(value.id);

          $('#modalDetailBodyEdit').append(isi);

            $('.item_code_edit').append(item_list);

          if (value.item_currency == "USD") {
              $('#ket_harga_edit'+value.id).text("$");
            }else if (value.item_currency == "JPY") {
              $('#ket_harga_edit'+value.id).text("¥");
            }else if (value.item_currency == "IDR"){
              $('#ket_harga_edit'+value.id).text("Rp.");
            }

          var uom = $('#uomhide'+value.id).val();
            $("#uom_edit"+value.id).val(uom).trigger("change");

            var item_code = $('#item_code_edit_hide'+value.id).val();
            $("#item_code_edit"+value.id).val(item_code).trigger("change");

            var price = document.getElementById("item_price_edit"+value.id).value;
            var prc = price.replace(/\D/g, ""); //get angka saja

            var qty = document.getElementById("qty_edit"+value.id).value;
              var hasil = parseInt(qty) * parseInt(prc); //Dikalikan qty

              if (!isNaN(hasil)) {
              var amount = document.getElementById('amount_edit'+value.id);
              amount.value = rubah(hasil);
            }
            
            $('.datepicker').datepicker({
            autoclose: true,
            format: 'yyyy-mm-dd'
          });

          $(function () {
            $('.select5').select2({
              dropdownAutoWidth : true,
              dropdownParent: $("#"+id),
              allowClear: true,
              minimumInputLength: 3
            });
          })

          $("#looping").val(ids);
        });
      });
    }

    function pilihItem(elem)
  {
    var no = elem.id.match(/\d/g);
    no = no.join("");

    if (elem.value == "kosong") {
      $('#item_code'+no).val("");
      $('#item_desc'+no).val("").attr('readonly', false);
      $('#item_spec'+no).val("").attr('readonly', false);
      $('#item_stock'+no).val("").attr('readonly', false);
      $('#item_price'+no).val("").attr('readonly', false);
      $('#uom'+no).val("").attr('readonly', false);
      $('#item_currency'+no).val("");
      $('#item_currency'+no).next(".select2-container").show();
      $('#item_currency'+no).next(".select3-container").show();
      $('#item_currency'+no).show();
      $('#item_currency_text'+no).val("");
      $('#item_currency_text'+no).hide();
      $('#ket_harga'+no).text("?");
    }

    else{

      $.ajax({
        url: "{{ route('admin.prgetitemdesc') }}?kode_item="+elem.value,
        method: 'GET',
        success: function(data) {
          var json = data,
          obj = JSON.parse(json);
          $('#item_desc'+no).val(obj.deskripsi).attr('readonly', true);
          $('#item_spec'+no).val(obj.spesifikasi).attr('readonly', true);
          $('#item_price'+no).val(obj.price).attr('readonly', true);
          $('#uom'+no).val(obj.uom).change();
          // $('#qty'+no).val("0");
          $('#amount'+no).val("0");
          $('#item_currency'+no).next(".select2-container").hide();
          $('#item_currency'+no).hide();
          $('#item_currency_text'+no).show();
          $('#item_currency_text'+no).val(obj.currency).show().attr('readonly', true);
          if (obj.currency == "USD") {
            $('#ket_harga'+no).text("$");
          }else if (obj.currency == "JPY") {
            $('#ket_harga'+no).text("¥");
          }else if (obj.currency == "IDR"){
            $('#ket_harga'+no).text("Rp.");
          }

          var $datepicker = $('#req_date'+no).attr('readonly', false);
          $datepicker.datepicker();
          $datepicker.datepicker('setDate', limitdate);

        } 
      });

    }
      // alert(sel.value);
  }

    function pilihItemEdit(elem)
    {
      var no = elem.id.match(/\d/g);
      no = no.join("");

      $.ajax({
        url: "{{ route('admin.prgetitemdesc') }}?kode_item="+elem.value,
        method: 'GET',
        success: function(data) {
          var json = data,
          obj = JSON.parse(json);
          $('#item_desc_edit'+no).val(obj.deskripsi).attr('readonly', true);
          $('#item_spec_edit'+no).val(obj.spesifikasi).attr('readonly', true);
          $('#item_price_edit'+no).val(obj.price).attr('readonly', true);
          $('#uom_edit'+no).val(obj.uom).change();
          // $('#qty_edit'+no).val("0");
          // $('#amount_edit'+no).val("0");
          $('#item_currency_edit'+no).next(".select2-container").hide();
          $('#item_currency_edit'+no).hide();
          $('#item_currency_text_edit'+no).show();
          $('#item_currency_text_edit'+no).val(obj.currency).show().attr('readonly', true);
          if (obj.currency == "USD") {
            $('#ket_harga_edit'+no).text("$");
          }else if (obj.currency == "JPY") {
            $('#ket_harga_edit'+no).text("¥");
          }else if (obj.currency == "IDR"){
            $('#ket_harga_edit'+no).text("Rp.");
          }

          var $datepicker = $('#req_date_edit'+no).attr('readonly', false);
          $datepicker.datepicker();
          $datepicker.datepicker('setDate', limitdate);

        } 
      });

        // alert(sel.value);
    }

    function getTotal(id) {
      // console.log(id);
      var num = id.match(/\d/g);
      num = num.join("");

      var price = document.getElementById("item_price"+num).value;
        var prc = price.replace(/\D/g, ""); //get angka saja

        var qty = document.getElementById("qty"+num).value;
          var hasil = parseInt(qty) * parseInt(prc); //Dikalikan qty

          if (!isNaN(hasil)) {

          var amount = document.getElementById('amount'+num);
          amount.value = rubah(hasil);

        }
    }

    function getTotalEdit(id) {
      var num = id.match(/\d/g);
      num = num.join("");

      var price = document.getElementById("item_price_edit"+num).value;
        var prc = price.replace(/\D/g, ""); //get angka saja

        var qty = document.getElementById("qty_edit"+num).value;
          var hasil = parseInt(qty) * parseInt(prc); //Dikalikan qty

          if (!isNaN(hasil)) {

          var amount = document.getElementById('amount_edit'+num);
          amount.value = rubah(hasil);

        }
    }

    function rubah(angka){
      var reverse = angka.toString().split('').reverse().join(''),
      ribuan = reverse.match(/\d{1,3}/g);
      ribuan = ribuan.join('.').split('').reverse().join('');
      return ribuan;
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