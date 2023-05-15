@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
    #bodyTablePayment > tr:hover {
        cursor: pointer;
        background-color: #7dfa8c;
    }
    .bigdrop {
        width: 400px !important;
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

    table.table-bordered > tfoot > tr > th{
        text-align: right;
    }

    .container {
      display: block;
      position: relative;
      padding-left: 35px;
      margin-bottom: 12px;
      cursor: pointer;
      font-size: 16px;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }

    /* Hide the browser's default checkbox */
    .container input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
      height: 0;
      width: 0;
    }

    #loading { display: none; }
</style>
@stop
@section('header')
    <div class="page-breadcrumb" style="padding: 7px">
        <div class="row align-items-center">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="page-title" style="margin:0">{{$title}}<span class="text-purple"> {{$title_jp}}</span></h3>
            </div>
            <div class="col-md-3 col-8 align-self-right" style="text-align: right;">
            </div>
            <div class="col-md-3 col-8 align-self-right" style="text-align: right;">
                <a class="btn btn-success pull-right" style="width: 100%;color: white" onclick="newData('new')"><i class="fa fa-plus"></i> &nbsp;Create Jurnal</a>
            </div>
        </div>
    </div>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid" style="padding: 7px;min-height: 100vh">
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>

        <div class="row" style="padding: 5px">
            <div class="col-sm-12">

                <table id="TablePayment" class="table table-bordered table-striped table-hover">
                    <thead style="background-color: rgba(126,86,134,.7);">
                        <tr>
                            <th>#</th>
                            <th>Jurnal Date</th>
                            <th>Vendor</th>
                            <th>Bank Name</th>
                            <th>Beneficiary</th>
                            <th>Invoice</th>
                            <th>Currency</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="bodyTablePayment">
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
                        <th></th>
                        <th></th>
                    </tr>
                </tfoot>
                </table>

            </div>
     </div>
 </div>

 <div class="modal fade" id="modalNew">
    <div class="modal-dialog" style="width: 1200px !important">
        <div class="modal-content">
            <div class="modal-header" style="padding-top: 0;">
                <div class="row">
                    <input type="hidden" id="id_edit">
                    <center><h3 style="font-weight: bold; padding: 10px;" id="modalNewTitle"></h3></center>
                    
                    <div class="col-md-6">

                        <div class="col-md-6" style="margin-bottom: 5px;">
                            <label for="date_payment" class="col-sm-12 control-label">Date Payment<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <!-- <input type="text" class="form-control" value="<?= date('d-M-Y') ?>" disabled=""> -->
                                <input type="text" class="form-control pull-right datepicker" id="date_payment" name="date_payment">
                            </div>
                        </div>
                        
                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="supplier_code" class="col-sm-12 control-label">Vendor Name<span class="text-red">*</span></label>
                            <div class="col-sm-12" id="div_supplier">
                                <select class="form-control select4" id="supplier_code" name="supplier_code" data-placeholder='Choose Supplier Name' style="width: 100%" onchange="getPayment(this)">
                                    <option value="">&nbsp;</option>
                                    @foreach($vendor_jurnal as $ven)
                                    <option value="{{$ven->supplier_name}}">{{$ven->supplier_name}}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" class="form-control" id="supplier_name" name="supplier_name" readonly="">
                            </div>
                        </div>
                        
                        
                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="bank" class="col-sm-12 control-label">Bank ID<span class="text-red">*</span></label>
                            <div class="col-sm-12" id="div_bank">
                                <select class="form-control select4" id="bank" name="bank" data-placeholder='Choose Bank' style="width: 100%" onchange="getBank(this)">
                                    <option value="">&nbsp;</option>
                                    @foreach($bank as $pt)
                                    <option value="{{$pt->id}}">{{$pt->vendor}} | {{$pt->currency}} | {{$pt->branch}} | {{$pt->rekening_no}} | {{$pt->rekening_nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="branch_name" class="col-sm-12 control-label">Bank Branch Name<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="branch_name" name="branch_name" readonly="">
                            </div>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="beneficiary_name" class="col-sm-12 control-label">Beneficiary Name<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="beneficiary_name" name="beneficiary_name" readonly="">
                            </div>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="beneficiary_no" class="col-sm-12 control-label">Beneficiary No<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="beneficiary_no" name="beneficiary_no" readonly="">
                            </div>
                        </div>
                        <div class="col-md-4" style="margin-bottom: 5px">
                            <label for="Currency" class="col-sm-12 control-label">Currency<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="currency" name="currency" readonly="">
                            </div>
                        </div>

                        <div class="col-md-4" style="margin-bottom: 5px;">
                            <label for="switch_code" class="col-sm-12 control-label" style="padding:0">Info to Bank (Swift Code)</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="switch_code" name="switch_code" readonly>
                                <input type="hidden" class="form-control" id="internal" name="internal">
                                <input type="hidden" class="form-control" id="foreign" name="foreign">
                            </div>
                        </div>

                         <div class="col-md-4" style="margin-bottom: 5px;">
                            <label for="bank_charge" class="col-sm-12 control-label">Bank Charge<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="bank_charge" name="bank_charge" readonly>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-6">
                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="invoice" class="col-sm-12 control-label">Invoice<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="invoice" name="invoice" >
                            </div>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="remark" class="col-sm-12 control-label">Remark<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="remark" name="remark" readonly>
                            </div>
                        </div>

                        <div class="col-md-4" style="margin-bottom: 5px;">
                            <label for="exchange_method" class="col-sm-12 control-label">Exchange Method</label>
                            <div class="col-sm-12" id="div_exchange_method">
                                 <select class="form-control" id="exchange_method" name="exchange_method" data-placeholder='Choose Exchange Method' style="width: 100%">
                                    <option value="">&nbsp;</option>
                                    <option value="SPOT">SPOT</option>
                                    <!-- <option value=""></option> -->
                                    <option value="CONT">CONT</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4" style="margin-bottom: 5px;">
                            <label for="iban" class="col-sm-12 control-label" style="padding:0">IBAN</label>
                            <div class="col-sm-12" style="padding:0">
                                <input type="text" class="form-control" id="iban" name="iban">
                            </div>
                        </div>
                        
                        <div class="col-md-4" style="margin-bottom: 5px;">
                            <label for="purpose_remit" class="col-sm-12 control-label">Purpose Remit</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="purpose_remit" name="purpose_remit">
                            </div>
                        </div>

                        <div class="col-md-7" style="margin-bottom: 5px;">
                            <label for="id_payment" class="col-sm-12 control-label">ID Payment<span class="text-red">*</span></label>
                            <div class="col-sm-12" id="div_id_payment">
                                <select class="form-control select4" id="id_payment" name="id_payment" data-placeholder='Choose ID Payment Here' style="width: 100%" onchange="getInvoice(this)">
                                </select>
                            </div>
                        </div>

                        <div class="col-md-5" style="margin-bottom: 5px;">
                            <label for="amount_bank_charge" class="col-sm-12 control-label">Amount (Bank Charge)<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="amount_bank_charge" name="amount_bank_charge" readonly>
                            </div>
                        </div>

                        <div class="col-md-12" style="margin-bottom: 5px;">
                            <label for="amount" class="col-sm-12 control-label">Amount<span class="text-red">*</span></label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="amount" name="amount" readonly>
                            </div>
                        </div>
                       
                    </div>


                    <div class="col-md-12" style="padding:0" style="">

                        <div class="row detail_create" style="padding:20px">
                            <div class="col-md-2" style="padding:5px;">
                                <b>Vendor</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Invoice No</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>Currency</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Amount Invoice</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>PPN</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>WH Tax</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Net Payment</b>
                            </div>
                        
                            <div id="verifikasi_invoice" >
                                
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div style="width: 100%; font-weight: bold; font-size: 1.5vw;text-align: center;background-color: yellow;">Jurnal Section</div>
                            </div>
                        </div>

                        <div class="row jurnal" style="padding:20px">

                            <div class="col-md-12" style="padding:0">
                                <div class="col-md-7" style="padding: 5px;">
                                   <b>A : AP Other VAT &nbsp;&nbsp; B: AP Other &nbsp;&nbsp; C: Meal &nbsp;&nbsp; D: Meal VAT &nbsp;&nbsp; E:Other &nbsp;&nbsp; F: AP Trade &nbsp;&nbsp; G: AP Trade</b>
                                </div>

                                <div class="col-md-3" style="padding: 5px;">
                                </div>

                                <div class="col-md-2" style="padding: 5px;">
                                    <input type="text" id="bank_outflow" name="bank_outflow" class="form-control" placeholder="Bank Outflow" readonly>
                                </div>
                            </div>

                            <div class="col-md-1" style="padding: 5px;">
                                <input type="checkbox" id="A" name="jurnal_select" value="A" onclick="checkJurnal(this.value)">
                                <label for="A">&nbsp;&nbsp;&nbsp; A</label><br>
                            </div>

                            <div class="col-md-1" style="padding: 5px;">
                                <input type="checkbox" id="B" name="jurnal_select" value="B" onclick="checkJurnal(this.value)">
                                <label for="B">&nbsp;&nbsp;&nbsp; B</label><br>
                            </div>

                            <div class="col-md-1" style="padding: 5px;">
                                <input type="checkbox" id="C" name="jurnal_select" value="C" onclick="checkJurnal(this.value)">
                                <label for="C">&nbsp;&nbsp;&nbsp; C</label><br>
                            </div>

                            <div class="col-md-1" style="padding: 5px;">
                                <input type="checkbox" id="D" name="jurnal_select" value="D" onclick="checkJurnal(this.value)">
                                <label for="D">&nbsp;&nbsp;&nbsp; D</label><br>
                            </div>

                            <div class="col-md-1" style="padding: 5px;">
                                <input type="checkbox" id="E" name="jurnal_select" value="E" onclick="checkJurnal(this.value)">
                                <label for="E">&nbsp;&nbsp;&nbsp; E</label><br>
                            </div>

                            <div class="col-md-1" style="padding: 5px;">
                                <input type="checkbox" id="F" name="jurnal_select" value="F" onclick="checkJurnal(this.value)">
                                <label for="F">&nbsp;&nbsp;&nbsp; F</label><br>
                            </div>

                            <div class="col-md-1" style="padding: 5px;">
                                <input type="checkbox" id="G" name="jurnal_select" value="G" onclick="checkJurnal(this.value)">
                                <label for="G">&nbsp;&nbsp;&nbsp; G</label><br>
                            </div>

                            <div class="col-xs-12" style="padding:5px;">
                                <a type="button" class="btn btn-success" onclick='tambah("tambah");'><i class='fa fa-plus'></i></a>
                            </div>  

                            <div class="col-md-1" style="padding:5px;">
                                <b>Seq</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>Reference</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>CC</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>Pst Key</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>GL Account</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Desc Account</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>Currency</b>
                            </div>
                             <div class="col-md-1" style="padding:5px;">
                                <b>Amount</b>
                            </div>
                            <div class="col-md-2" style="padding:5px;">
                                <b>Text</b>
                            </div>
                            <div class="col-md-1" style="padding:5px;">
                                <b>Action</b>
                            </div>

                            <div id="verifikasi_jurnal" >
                                
                            </div>
                        </div>
                            
                    </div>
                    <div class="col-md-12">
                        <a class="btn btn-success pull-right" onclick="Save('new')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton">CREATE</a>
                        <a class="btn btn-info pull-right" onclick="Save('update')" style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="updateButton">UPDATE</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


 <div class="modal fade" id="modaldelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="modaldeletehide()">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                Apakah anda ingin menghapus payment request ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="modaldeletehide()">Cancel</button>
                <a id="a" name="modalButtonDelete" type="button"  onclick="delete_payment_request(this.id)" class="btn btn-danger" style="color:white">Delete</a>
            </div>
        </div>
    </div>
</div>

@stop
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/jquery.tagsinput.min.js") }}"></script>

<script type="text/javascript">
    var total_baris_invoice = 0;
    var no = 2;
    var nomor_awal = 0;
    invoice_list = "";
    jurnal_type = [];
    cost_center_all = [];
    gl_account_all = [];


    $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });


    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");
        fetchData();

        $("#invoice").keyup(function(){
          $("#remark").val($("#invoice").val());
        });

        // $("#main-wrapper").attr("data-sidebartype","mini-sidebar");
        // document.getElementById("main-wrapper").setAttribute('data-sidebartype','mini-sidebar');
        // document.getElementById("logo-yamaha").setAttribute('height','20px');
        // document.getElementById("logo-yamaha").style.setProperty('height', '20px', 'important');
        // document.getElementsByClassName('logo-icon')[0].style.setProperty('padding-left', '0px', 'important');
        // document.getElementsByClassName('hide-menu')[0].style.setProperty('display', 'none', 'important');
        // document.getElementsByClassName('sidebar-item active selected')[0].style.setProperty('width', '65px', 'important');
    });

    getJurnal();
    getCostCenter();
    getAccount();

   $('.datepicker').datepicker({
        autoclose: true,
        format: "yyyy-mm-dd",
        todayHighlight: true,   
    });

   $('.select2').select2({
        dropdownAutoWidth : true,
        allowClear: true
    });

    $(function () {
        $('#bank').select2({
            allowClear:true,
            dropdownAutoWidth : true,
            dropdownParent: $('#div_bank')
        });

        $('#supplier_code').select2({
            allowClear:true,
            dropdownAutoWidth : true,
            dropdownParent: $('#div_supplier')
        });

        $('#exchange_method').select2({
            allowClear:true,
            dropdownAutoWidth : true,
            dropdownParent: $('#div_exchange_method')
        });

        $('#id_payment').select2({
            allowClear:true,
            dropdownAutoWidth : true,
            dropdownParent: $('#div_id_payment')
        });

    })


    function getJurnal(){
        $.ajax({
            url: "{{ url('fetch/jurnal_type') }}", 
            type : 'GET', 
            success : function(data){
                var obj = jQuery.parseJSON(data);
                for (var i = 0; i < obj.length; i++) {
                    jurnal_type.push({
                        'id_seq' :  obj[i].id_seq,
                        'model' :  obj[i].model, 
                        'gl_account' :  obj[i].gl_account,
                        'type' :  obj[i].type,
                        'currency' :  obj[i].currency,
                        'cost_center' :  obj[i].cost_center,
                    });
                }
            }
        });
    }

    function getCostCenter(){
        $.ajax({
            url: "{{ url('fetch/cost_center/data') }}", 
            type : 'GET', 
            success : function(data){
                var obj = jQuery.parseJSON(data);
                for (var i = 0; i < obj.length; i++) {
                    cost_center_all.push({
                        'cost_center' :  obj[i].cost_ctr, 
                        'short_text' :  obj[i].short_text
                    });
                }
            }
        });
    }

    function getAccount(){
        $.ajax({
            url: "{{ url('fetch/gl_account/data') }}", 
            type : 'GET', 
            success : function(data){
                var obj = jQuery.parseJSON(data);
                for (var i = 0; i < obj.length; i++) {
                    gl_account_all.push({
                        'gl_account' :  obj[i].gl_account,
                        'gl_description' :  obj[i].gl_description, 
                        'acc_type' :  obj[i].acc_type
                    });
                }
            }
        });
    }

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

    function getFormattedDate(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;
        
        return day + '-' + monthNames[month] + '-' + year;
    }


    function getBank(elem){
        $.ajax({
            url: "{{ url('fetch/bank/data') }}?id="+elem.value,
            method: 'GET',
            success: function(data) {
                var json = data,
                obj = JSON.parse(json);
                $('#branch_name').val(obj.branch);
                $('#beneficiary_name').val(obj.rekening_nama);
                $('#beneficiary_no').val(obj.rekening_no);
                $('#currency').val(obj.currency);
                $('#bank_charge').val(obj.bank_charge);
                $('#switch_code').val(obj.switch_code);
                $('#internal').val(obj.internal);
                $('#foreign').val(obj.ln);

            } 
        });
    }

    function getPayment(elem){

        var data = {
            supplier_code:$('#supplier_code').val()
        }

        $.get('{{ url("fetch/bank/id_payment") }}',data, function(result, status, xhr){
            if(result.status){
              $('#id_payment').html("");
              var id_pay = "";
              id_pay += '<option value="">&nbsp;</option>';
              $.each(result.payment, function(key, value) {
                    id_pay += '<option value="'+value.payment_id+'">'+value.payment_id+'</option>';
              });

              $('#id_payment').append(id_pay);
              $('#supplier_name').val(result.vendor.supplier_name);
            }else{
                // openErrorGritter('Error', result.message);
            }
        });
    }

    function getInvoice(elem){

        var data = {
            id_payment:$('#id_payment').val()
        }

        $.get('{{ url("fetch/invoice/verification") }}',data, function(result, status, xhr){
            if(result.status){

                $('#verifikasi_invoice').html("");
                var invoice = "";
                var total_amount = 0;
                var total_ppn = 0;
                var total_pph = 0;
                var total_net_payment = 0;

                total_baris_invoice = 0;

                $.each(result.invoice, function(key, value) {

                    if (value.ppn == null) {
                        value.ppn = 0;
                    }else{
                        value.ppn = value.amount * 11 / 100;
                    }

                    if (value.pph == null) {
                        value.pph = 0;
                    }else{
                        value.pph = value.amount_service * value.pph/100;
                    }

                    invoice += '<div class="col-md-2" style="padding:5px;"> '+value.supplier_name+' <input type="hidden" id="invoice_supplier_name_'+key+'" name="invoice_supplier_name_'+key+'" value="'+value.supplier_name+'"> </div><div class="col-md-2" style="padding:5px;"> '+value.invoice+' <input type="hidden" id="invoice_id_'+key+'" name="invoice_id_'+key+'" value="'+value.id_invoice+'"><input type="hidden" id="invoice_number_'+key+'" name="invoice_number_'+key+'" value="'+value.invoice+'"></div><div class="col-md-1" style="padding:5px;"> '+value.currency+' <input type="hidden" id="invoice_currency_'+key+'" name="invoice_currency_'+key+'" value="'+value.currency+'"></div><div class="col-md-2" style="padding:5px;text-align:right"> '+parseInt(value.amount).toLocaleString()+' <input type="hidden" id="invoice_amount_'+key+'" name="invoice_amount_'+key+'" value="'+value.amount+'"> </div><div class="col-md-2" style="padding:5px;text-align:right"> '+parseInt(value.ppn).toLocaleString()+' <input type="hidden" id="invoice_ppn_'+key+'" name="invoice_ppn_'+key+'" value="'+value.ppn+'"></div><div class="col-md-1" style="padding:5px;text-align:right">  -'+parseInt(value.pph).toLocaleString()+' <input type="hidden" id="invoice_pph_'+key+'" name="invoice_pph_'+key+'" value="'+value.pph+'"></div><div class="col-md-2" style="padding:5px;text-align:right"> '+parseInt(value.net_payment).toLocaleString()+' <input type="hidden" id="invoice_net_payment_'+key+'" name="invoice_net_payment_'+key+'" value="'+value.net_payment+'"></div>';

                    total_amount += parseInt(value.amount);
                    total_ppn += parseInt(value.ppn);
                    total_pph += parseInt(value.pph);
                    total_net_payment += parseInt(value.net_payment);
                    total_baris_invoice++;
                });
                invoice += '<div class="col-md-12"><hr></div>';
                invoice += '<div class="col-md-2" style="padding:5px;"><b>Total</b></div><div class="col-md-2" style="padding:5px;"></div><div class="col-md-1" style="padding:5px;"></div><div class="col-md-2" style="padding:5px;text-align:right"><b> '+total_amount.toLocaleString()+' </b></div><div class="col-md-2" style="padding:5px;text-align:right"><b> '+total_ppn.toLocaleString()+' </b></div><div class="col-md-1" style="padding:5px;text-align:right">  -'+total_pph.toLocaleString()+' </b></div><div class="col-md-2" style="padding:5px;text-align:right"><b> '+total_net_payment.toLocaleString()+' </b></div>';

                $('#verifikasi_invoice').append(invoice);
                $('#amount').val(total_net_payment.toLocaleString());

                var amount_bank_charge = 0;
                var currency = $("#currency").val();
                var internal = $("#internal").val();
                var foreign = $("#foreign").val();

                if (currency == "IDR") {
                    if (internal == "TRUE") {
                        amount_bank_charge = 0;
                    }else if (internal == "FALSE"){
                        if (total_net_payment <= 500000000) {
                            amount_bank_charge = 600;                            
                        }else{
                            amount_bank_charge = 3000;
                        }

                    }
                }else if(currency == "USD"){
                    if (foreign == "FALSE") {
                        amount_bank_charge = 0;
                    }else if (foreign == "TRUE"){
                        amount_bank_charge = 15000;
                    }
                }else if(currency == "JPY"){
                    amount_bank_charge = 0;
                }

                $("#amount_bank_charge").val(amount_bank_charge);

                var bank_outflow = 0;

                bank_outflow = total_net_payment + amount_bank_charge;

                $("#bank_outflow").val(bank_outflow.toLocaleString());

            }

            else{
                // openErrorGritter('Error', result.message);
            }
        });
    }


    function Save(id){  
        $('#loading').show();

        if(id == 'new'){
            if($('#supplier_name').val() == null || $('#bank').val() == "" || $('#invoice').val() == "" || $('#remark').val() == "" ){
                
                $('#loading').hide();
                openErrorGritter('Error', "Please fill field with (*) sign.");
                return false;
            }

            var formData = new FormData();
            formData.append('jurnal_date', $("#date_payment").val());
            formData.append('supplier_code', $("#supplier_code").val());
            formData.append('supplier_name', $("#supplier_name").val());
            formData.append('bank_id', $("#bank").val());
            formData.append('bank_branch', $("#branch_name").val());
            formData.append('bank_beneficiary_name', $("#beneficiary_name").val());
            formData.append('bank_beneficiary_no', $("#beneficiary_no").val());
            formData.append('currency', $("#currency").val());
            formData.append('internal', $("#internal").val());
            formData.append('foreign', $("#foreign").val());
            formData.append('switch_code', $("#switch_code").val());
            formData.append('bank_charge', $("#bank_charge").val());
            formData.append('invoice', $("#invoice").val());
            formData.append('remark', $("#remark").val());
            formData.append('exchange_method', $("#exchange_method").val());
            formData.append('contract_number', $("#contract_number").val());
            formData.append('iban', $("#iban").val());
            formData.append('purpose_remit', $("#purpose_remit").val());
            formData.append('id_payment', $("#id_payment").val());
            formData.append('amount_bank_charge', $("#amount_bank_charge").val());
            formData.append('amount', $("#amount").val());
            formData.append('total_baris_invoice', total_baris_invoice);
            formData.append('total_baris_jurnal', nomor_awal);
            // formData.append('jumlah', no);
            
            for (var i = 0; i < total_baris_invoice; i++) {
                formData.append('invoice_supplier_name_'+i, $("#invoice_supplier_name_"+i).val());
                formData.append('invoice_id_'+i, $("#invoice_id_"+i).val());
                formData.append('invoice_number_'+i, $("#invoice_number_"+i).val());
                formData.append('invoice_currency_'+i, $("#invoice_currency_"+i).val());
                formData.append('invoice_amount_'+i, $("#invoice_amount_"+i).val());
                formData.append('invoice_ppn_'+i, $("#invoice_ppn_"+i).val());
                formData.append('invoice_pph_'+i, $("#invoice_pph_"+i).val());
                formData.append('net_payment_'+i, $("#invoice_net_payment_"+i).val());
            }

            for (var i = 0; i < nomor_awal; i++) {
                formData.append('seq_'+i, $("#seq_"+i).val());
                formData.append('reference_'+i, $("#reference_"+i).val());
                formData.append('cost_center_'+i, $("#cost_center_"+i).val());
                formData.append('type_'+i, $("#type_"+i).val());
                formData.append('gl_account_'+i, $("#gl_account_"+i).val());
                formData.append('gl_desc_'+i, $("#gl_desc_"+i).val());
                formData.append('currency_'+i, $("#currency_"+i).val());
                formData.append('amount_'+i, $("#amount_"+i).val());
                formData.append('note_'+i, $("#note_"+i).val());
            }

            $.ajax({
                url:"{{ url('create/jurnal') }}",
                method:"POST",
                data:formData,
                dataType:'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success:function(data)
                {
                    if (data.status) {
                        openSuccessGritter('Success', data.message);
                        $('#loading').hide();
                        $('#modalNew').modal('hide');
                        clearNew();
                        fetchData();
                    }else{
                        openErrorGritter('Error!',data.message);
                        $('#loading').hide();
                        audio_error.play();
                    }

                }
            });
        }
    }

    function clearNew(){
        $('#id_edit').val('');
        $("#supplier_code").val('').trigger('change');
        $("#supplier_name").val('');
        $("#bank").val('').trigger('change');
        $('#currency').val('');
        $("#invoice").val('');
        $('#remark').val('');
        $("#exchange_method").val('');
        $("#contract_number").val('');
        $("#iban").val('');
        $("#purpose_remit").val('');
        $("#id_payment").val('');
        $("#amount").val('');
        $("#amount_bank_charge").val('');
    }

    function fetchData() {
        $.get('{{ url("fetch/jurnal") }}',  function(result, status, xhr){
            if(result.status){
                $('#TablePayment').DataTable().clear();
                $('#TablePayment').DataTable().destroy();
                $("#bodyTablePayment").html('');
                var listTableBody = '';
                
                $.each(result.jurnal, function(key, value){
                    listTableBody += '<tr>';
                    listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
                    listTableBody += '<td style="width:1%;">'+getFormattedDate(new Date(value.jurnal_date))+'</td>';
                    listTableBody += '<td style="width:3%;">'+value.supplier_code+' - '+value.supplier_name+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.bank_branch+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.bank_beneficiary_name+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.invoice+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.currency+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.amount+'</td>';
                    listTableBody += '<td style="width:2%;"><a class="btn btn-md btn-danger" target="_blank" href="{{ url("report/jurnal") }}/'+value.id+'"><i class="fa fa-file-pdf-o"></i> </a></center></td>';

                    listTableBody += '</tr>';
                });

                $('#bodyTablePayment').append(listTableBody);

                $('#TablePayment tfoot th').each( function () {
                    var title = $(this).text();
                    $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
                } );

                var table = $('#TablePayment').DataTable({
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
                        }
                        ]
                    },
                    'paging': true,
                    'lengthChange': true,
                    'pageLength': 10,
                    'searching': true   ,
                    'ordering': true,
                    'order': [],
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true,
                    initComplete: function() {
                    this.api()
                        .columns([2,3])
                        .every(function(dd) {
                            var column = this;
                            var theadname = $("#TablePayment th").eq([dd]).text();
                            var select = $(
                                    '<select style="width:100%"><option value="" style="font-size:11px;">All</option></select>'
                                )
                                .appendTo($(column.footer()).empty())
                                .on('change', function() {
                                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                    column.search(val ? '^' + val + '$' : '', true, false)
                                        .draw();
                                });
                            column
                                .data()
                                .unique()
                                .sort()
                                .each(function(d, j) {
                                    var vals = d;
                                    if ($("#TablePayment th").eq([dd]).text() == 'Category') {
                                        vals = d.split(' ')[0];
                                    }
                                    select.append('<option style="font-size:11px;" value="' +
                                        d + '">' + vals + '</option>');
                                });
                        });
                    },
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

                $('#TablePayment tfoot tr').appendTo('#TablePayment thead');

                $('#loading').hide();
            }else{
                openErrorGritter('Error!','Failed Get Data');
            }
        });
    }

    var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
    var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

    function newData(id){
        if(id == 'new'){
            $('#modalNewTitle').text('Create Jurnal');
            $('#newButton').show();
            $('#updateButton').hide();
            clearNew();
            $('#modalNew').modal('show');

            $('.detail_create').show();
            $('.detail_edit').hide();

        }
    }

    function checkJurnal(elem){

        var jurnal_select = [];
        var currency_master = $('#currency').val();

        $("input[name='jurnal_select']:checked").each(function (i) {
            jurnal_select[i] = $(this).val();
        });

        $('#verifikasi_jurnal').html("");
        nomor_awal = 0;
        
        for (z = 0; z < jurnal_select.length; z++) {
            for (var i = 0; i < jurnal_type.length; i++) {
                var id_seq = jurnal_type[i].id_seq; 
                var model = jurnal_type[i].model; 
                var gl_account = jurnal_type[i].gl_account; 
                var type = jurnal_type[i].type; 
                var currency = jurnal_type[i].currency; 
                var cost_center = jurnal_type[i].cost_center; 

                if (model == jurnal_select[z]) {
                
                    var jurnal_isi = "";

                    jurnal_isi += '<div id="'+nomor_awal+'" class="col-md-12" style="padding: 0;">';
                    jurnal_isi += '<div class="col-md-1" style="padding:5px;"> '+id_seq+' <input type="hidden" class="form-control" id="seq_'+nomor_awal+'"  name="seq_'+nomor_awal+'" value="'+id_seq+'"></div>';
                    jurnal_isi += '<div class="col-md-1" style="padding:5px;"> <input type="text" class="form-control" pattern="\d*" maxlength="16" id="reference_'+nomor_awal+'" name="reference_'+nomor_awal+'"> </div>';
                    jurnal_isi += '<div class="col-md-1" style="padding:5px;" id="div_cost_center"><select class="form-control select6" id="cost_center_'+nomor_awal+'" name="cost_center_'+nomor_awal+'" data-placeholder="Cost Center" style="width: 100%;"><option></option>';

                    for (var x = 0; x < cost_center_all.length; x++) {
                        if (cost_center == cost_center_all[x].cost_center) {
                            jurnal_isi += '<option value="'+cost_center_all[x].cost_center+'" selected>'+cost_center_all[x].cost_center+'</option>';
                        }else{
                            jurnal_isi += '<option value="'+cost_center_all[x].cost_center+'">'+cost_center_all[x].cost_center+'</option>';
                        }
                    }
                    jurnal_isi += '</select></div>';
                    jurnal_isi += '<div class="col-md-1" style="padding:5px;"><select class="form-control select6" id="type_'+nomor_awal+'" name="type_'+nomor_awal+'" data-placeholder="type" style="width: 100%;"><option></option>';
                        if (type == "Dr") {
                            jurnal_isi += '<option value="Dr" selected>Dr</option>';
                            jurnal_isi += '<option value="Cr">Cr</option>';
                        }else if (type == "Cr"){
                            jurnal_isi += '<option value="Cr" selected>Cr</option>';
                            jurnal_isi += '<option value="Dr">Dr</option>';
                        }else{
                            jurnal_isi += '<option value="Dr">Dr</option>';
                            jurnal_isi += '<option value="Cr">Cr</option>';
                        }
                    jurnal_isi += '</select></div>';


                    jurnal_isi += '<div class="col-md-1" style="padding:5px;"><select class="form-control select6" id="gl_account_'+nomor_awal+'" name="gl_account_'+nomor_awal+'" data-placeholder="GL Account" style="width: 100%;" onchange="check_gl_desc(this)"><option></option>';

                    var gl_description = "";

                    for (var x = 0; x < gl_account_all.length; x++) {
                        if (gl_account == gl_account_all[x].gl_account) {
                            jurnal_isi += '<option value="'+gl_account_all[x].gl_account+'" selected>'+gl_account_all[x].gl_account+'  - '+gl_account_all[x].gl_description+'</option>';
                            gl_description = gl_account_all[x].gl_description;
                        }else{
                            jurnal_isi += '<option value="'+gl_account_all[x].gl_account+'">'+gl_account_all[x].gl_account+'  - '+gl_account_all[x].gl_description+'</option>';
                        }
                    }
                    jurnal_isi += '</select></div>';
                    jurnal_isi += '<div class="col-md-2" style="padding:5px;"> <input type="text" class="form-control" id="gl_desc_'+nomor_awal+'" name="gl_desc_'+nomor_awal+'" value="'+gl_description+'"></div>';


                    jurnal_isi += '<div class="col-md-1" style="padding:5px;"><select class="form-control select6" id="currency_'+nomor_awal+'" name="currency_'+nomor_awal+'" data-placeholder="Cur" style="width: 100%;"><option></option>';
                        
                        if (currency_master == null || currency_master == "") {
                            jurnal_isi += '<option value="USD">USD</option>';
                            jurnal_isi += '<option value="IDR">IDR</option>';
                            jurnal_isi += '<option value="JPY">JPY</option>';
                        }else if(currency_master == "USD"){
                            jurnal_isi += '<option value="USD" selected>USD</option>';
                            jurnal_isi += '<option value="IDR">IDR</option>';
                            jurnal_isi += '<option value="JPY">JPY</option>';
                        }else if(currency_master == "IDR"){
                            jurnal_isi += '<option value="USD">USD</option>';
                            jurnal_isi += '<option value="IDR" selected>IDR</option>';
                            jurnal_isi += '<option value="JPY">JPY</option>';
                        }else if(currency_master == "JPY"){
                            jurnal_isi += '<option value="USD">USD</option>';
                            jurnal_isi += '<option value="IDR">IDR</option>';
                            jurnal_isi += '<option value="JPY" selected>JPY</option>';
                        }

                    jurnal_isi += '</select></div>';

                    // jurnal_isi += '<div class="col-md-1" style="padding:5px;"> <input type="text" class="form-control" id="currency" name="currency"> </div>';
                    jurnal_isi += '<div class="col-md-1" style="padding:5px;"> <input type="text" class="form-control" id="amount_'+nomor_awal+'" name="amount_'+nomor_awal+'"> </div>';
                    jurnal_isi += '<div class="col-md-2" style="padding:5px;"> <input type="text" class="form-control" id="note_'+nomor_awal+'" name="note_'+nomor_awal+'"> </div>';
                    jurnal_isi += '<div class="col-md-1" style="padding:5px;">&nbsp;<button onclick="kurang(this);" class="btn btn-danger"><i class="fa fa-close"></i> </button>'
                    jurnal_isi += '</div>'; 

                    $('#verifikasi_jurnal').append(jurnal_isi);

                    $(function () {
                        $('.select6').select2({
                            allowClear:true,
                            dropdownAutoWidth : true,
                            dropdownParent: $("#div_cost_center"),
                            dropdownCssClass : 'bigdrop'
                        });
                    })

                    nomor_awal++;
                }
            }
        }
    }

    function tambah(id){
        

        
        var id = id;
        var currency_master = $('#currency').val();
        // $('#verifikasi_jurnal').html("");
        var jurnal_isi = "";
        jurnal_isi += '<div id="'+nomor_awal+'" class="col-md-12" style="padding: 0;">';
        jurnal_isi += '<div class="col-md-1" style="padding:5px;"> <input type="text" class="form-control" id="seq_'+nomor_awal+'"  name="seq_'+nomor_awal+'"> </div>';
        jurnal_isi += '<div class="col-md-1" style="padding:5px;"> <input type="text" pattern="\d*" maxlength="16" class="form-control" id="reference_'+nomor_awal+'" name="reference_'+nomor_awal+'"> </div>';
        jurnal_isi += '<div class="col-md-1" style="padding:5px;" id="div_cost_center"><select class="form-control select6" id="cost_center_'+nomor_awal+'" name="cost_center_'+nomor_awal+'" data-placeholder="Cost Center" style="width: 100%;"><option></option>';
        for (var x = 0; x < cost_center_all.length; x++) {
            jurnal_isi += '<option value="'+cost_center_all[x].cost_center+'">'+cost_center_all[x].cost_center+'</option>';
        }
        jurnal_isi += '</select></div>';
        jurnal_isi += '<div class="col-md-1" style="padding:5px;"><select class="form-control select6" id="type_'+nomor_awal+'" name="type_'+nomor_awal+'" data-placeholder="type" style="width: 100%;"><option></option>';
        jurnal_isi += '<option value="Dr">Dr</option>';
        jurnal_isi += '<option value="Cr">Cr</option>';
        jurnal_isi += '</select></div>';
        jurnal_isi += '<div class="col-md-1" style="padding:5px;" id="div_gl_account"><select class="form-control select6" id="gl_account_'+nomor_awal+'" name="gl_account_'+nomor_awal+'" data-placeholder="GL Account" style="width: 100%;" onchange="check_gl_desc(this)"><option></option>';

        var gl_description = "";

        for (var x = 0; x < gl_account_all.length; x++) {
            jurnal_isi += '<option value="'+gl_account_all[x].gl_account+'">'+gl_account_all[x].gl_account+' - '+gl_account_all[x].gl_description+'</option>'; 
        }
        jurnal_isi += '</select></div>';
        jurnal_isi += '<div class="col-md-2" style="padding:5px;"> <input type="text" class="form-control" id="gl_desc_'+nomor_awal+'" name="gl_desc_'+nomor_awal+'"></div>';

        jurnal_isi += '<div class="col-md-1" style="padding:5px;"><select class="form-control select6" id="currency_'+nomor_awal+'" name="currency_'+nomor_awal+'" data-placeholder="Cur" style="width: 100%;"><option></option>';
            
            if (currency_master == null || currency_master == "") {
                jurnal_isi += '<option value="USD">USD</option>';
                jurnal_isi += '<option value="IDR">IDR</option>';
                jurnal_isi += '<option value="JPY">JPY</option>';
            }else if(currency_master == "USD"){
                jurnal_isi += '<option value="USD" selected>USD</option>';
                jurnal_isi += '<option value="IDR">IDR</option>';
                jurnal_isi += '<option value="JPY">JPY</option>';
            }else if(currency_master == "IDR"){
                jurnal_isi += '<option value="USD">USD</option>';
                jurnal_isi += '<option value="IDR" selected>IDR</option>';
                jurnal_isi += '<option value="JPY">JPY</option>';
            }else if(currency_master == "JPY"){
                jurnal_isi += '<option value="USD">USD</option>';
                jurnal_isi += '<option value="IDR">IDR</option>';
                jurnal_isi += '<option value="JPY" selected>JPY</option>';
            }

        jurnal_isi += '</select></div>';

        jurnal_isi += '<div class="col-md-1" style="padding:5px;"> <input type="text" class="form-control" id="amount_'+nomor_awal+'" name="amount_'+nomor_awal+'"> </div>';
        jurnal_isi += '<div class="col-md-2" style="padding:5px;"> <input type="text" class="form-control" id="note_'+nomor_awal+'" name="note_'+nomor_awal+'"> </div>';
        jurnal_isi += '<div class="col-md-1" style="padding:5px;">&nbsp;<button onclick="kurang(this);" class="btn btn-danger"><i class="fa fa-close"></i> </button>';
        jurnal_isi += '</div>'; 

        $('#verifikasi_jurnal').append(jurnal_isi);

        $(function () {
            $('.select6').select2({
                allowClear:true,
                dropdownAutoWidth : true,
                dropdownParent: $("#div_cost_center"),
                dropdownCssClass : 'bigdrop'
            });
        })

        nomor_awal++;
       
    }

    function kurang(elem) {

        var ids = $(elem).parent('div').parent('div').attr('id');
        var oldid = ids;
        $(elem).parent('div').parent('div').remove();
        var newid = parseInt(ids) + 1;

        $("#"+newid).attr("id",oldid);
        $("#seq_"+newid).attr("name","seq_"+oldid);
        $("#seq_"+newid).attr("id","seq_"+oldid);
        $("#reference_"+newid).attr("name","reference_"+oldid);
        $("#reference_"+newid).attr("id","reference_"+oldid);
        $("#cost_center_"+newid).attr("name","cost_center_"+oldid);
        $("#cost_center_"+newid).attr("id","cost_center_"+oldid);
        $("#type_"+newid).attr("name","type_"+oldid);
        $("#type_"+newid).attr("id","type_"+oldid);
        $("#gl_account_"+newid).attr("name","gl_account_"+oldid);
        $("#gl_account_"+newid).attr("id","gl_account_"+oldid);
        $("#gl_desc_"+newid).attr("name","gl_desc_"+oldid);
        $("#gl_desc_"+newid).attr("id","gl_desc_"+oldid);
        $("#currency_"+newid).attr("name","currency_"+oldid);
        $("#currency_"+newid).attr("id","currency_"+oldid);
        $("#amount_"+newid).attr("name","amount_"+oldid);
        $("#amount_"+newid).attr("id","amount_"+oldid);
        $("#note_"+newid).attr("name","note_"+oldid);
        $("#note_"+newid).attr("id","note_"+oldid);

        nomor_awal -= 1;
        var a = nomor_awal-1;

        for (var i = ids; i <= a; i++) {   
            var newid = parseInt(i) + 1;
            var oldid = newid - 1;

            $("#"+newid).attr("id",oldid);
            $("#seq_"+newid).attr("name","seq_"+oldid);
            $("#seq_"+newid).attr("id","seq_"+oldid);
            $("#reference_"+newid).attr("name","reference_"+oldid);
            $("#reference_"+newid).attr("id","reference_"+oldid);
            $("#cost_center_"+newid).attr("name","cost_center_"+oldid);
            $("#cost_center_"+newid).attr("id","cost_center_"+oldid);
            $("#type_"+newid).attr("name","type_"+oldid);
            $("#type_"+newid).attr("id","type_"+oldid);
            $("#gl_account_"+newid).attr("name","gl_account_"+oldid);
            $("#gl_account_"+newid).attr("id","gl_account_"+oldid);
            $("#gl_desc_"+newid).attr("name","gl_desc_"+oldid);
            $("#gl_desc_"+newid).attr("id","gl_desc_"+oldid);
            $("#currency_"+newid).attr("name","currency_"+oldid);
            $("#currency_"+newid).attr("id","currency_"+oldid);
            $("#amount_"+newid).attr("name","amount_"+oldid);
            $("#amount_"+newid).attr("id","amount_"+oldid);
            $("#note_"+newid).attr("name","note_"+oldid);
            $("#note_"+newid).attr("id","note_"+oldid);
        }

    }

    function check_gl_desc(elem){
        var nomor = elem.id.split("_");

        for (var x = 0; x < gl_account_all.length; x++) {
            if (elem.value == gl_account_all[x].gl_account) {
                $("#gl_desc_"+nomor[2]).val(gl_account_all[x].gl_description);    
            }
        }


    }

    function sendEmail(id) {
      var data = {
        id:id
      };

      if (!confirm("Apakah anda yakin ingin mengirim Payment Request ini ke Manager?")) {
        return false;
      }
      else{
        $("#loading").show();
      }

      $.get('{{ url("email/payment_request") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Berhasil Terkirim");
        $("#loading").hide();
        setTimeout(function(){  window.location.reload() }, 2500);
      })
    }

    function closemodal(){
        $('#modalNew').modal('hide');
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