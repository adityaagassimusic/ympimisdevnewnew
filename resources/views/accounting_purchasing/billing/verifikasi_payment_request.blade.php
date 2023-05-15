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
    <small>Verifikasi Form PR</small>
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

  <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
      <p style="position: absolute; color: White; top: 45%; left: 35%;">
        <span style="font-size: 40px">Loading, mohon tunggu . . . <i class="fa fa-spin fa-refresh"></i></span>
      </p>
  </div>


  <?php 
    $user = STRTOUPPER(Auth::user()->username);
  ?>
  <!-- SELECT2 EXAMPLE -->
  @if(Auth::user()->role_code == "S-ACC" && ($payment->posisi == "acc_verif" || $payment->posisi == "acc"))
  <div class="box box-primary">
    <div class="box-body" style="overflow-x:scroll;">
      <table id="tablePayment" class="table table-bordered table-striped table-hover">
          <thead style="background-color: rgba(126,86,134,.7);">
            <tr>
              <th>#</th>
              <th>Vendor</th>  
              <th>Invoice No</th>
              <th>Currency</th>
              <th>Amount</th>
              <th>PPN</th>
              <th>PPH</th>
              <th>Net Payment</th>
              <th>Payment Term</th>
              <th>Due Date</th>
              <th>Surat Jalan</th>
              <th>Faktur Pajak</th>
              <th>PO Number</th>
              <th>TT Date</th>
              <th>DO Date</th>
              <th>Dist Date Pch</th>
              <th>Inv Date</th>
              <th>File Invoice</th>   
            </tr>
          </thead>
          <tbody id="bodyTablePayment">
          </tbody>
        </table>
    </div>
    @endif

    <div class="box-body">

       @if(
      (($user == $payment->manager || Auth::user()->role_code == "S-MIS") && $payment->status_manager == null && $payment->posisi == "manager") 
      || (($user == $payment->gm || Auth::user()->role_code == "S-MIS") && $payment->status_gm == null && $payment->posisi == "gm")
      || (($user == "PI0902001" || $user == "PI1505001" || $user == "PI1910003") && $payment->posisi == "acc_verif"))

      <form role="form" id="myForm" method="post" action="{{url('payment_request/approval/'.$payment->id)}}" enctype="multipart/form-data">
        <input type="hidden" value="{{csrf_token()}}" name="_token" />  
        <table class="table table-bordered">
          <tr id="show-att">
            <td colspan="6" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 50%;" colspan="2" id="attach_pdf">
            </td>
            <td colspan="6" style="font-size: 16px;width: 50%;">
              <div class="col-md-12">
                <div class="panel panel-default">
                  <input type="hidden" value="{{csrf_token()}}" name="_token" />
                  <input type="hidden"  name="approve" id="approve" value="1" />
                  <div class="panel-heading">Approval : </div>
                  <div class="panel-body center-text"  style="padding: 20px">
                    <button type="submit" class="btn btn-success col-sm-12" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
                    <a data-toggle="modal" onclick="reject_payment()" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject</a>
                  </div>
                </div>
                <div class="box-group" id="accordion">
                <?php 
                  $no=1;
                  foreach ($invoice_file as $inv) { ?>
                     <div class="panel box box-primary">
                        <div class="box-header with-border">
                          <h4 class="box-title">
                            <a data-toggle="collapse" data-parent="#accordion" href="#<?=$no?>" aria-expanded="false" class="collapsed"> <?= $inv['invoice'] ?> </a>
                          </h4>
                        </div>
                        <div id="<?=$no?>" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                          <div class="box-body"> <embed src="{{$inv['file']}}" width='100%' height='600px'></embed> </div>
                        </div>
                      </div>
                  <?php $no++; } ?>
                </div>
              </div>
            </td>
          </tr>
          <!-- <tr>
            <?php 
              foreach ($invoice_file as $inv) { ?>
                <td colspan="6" style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;width: 50%;">
                  <h1><center><?= $inv['invoice'] ?></center></h1>
                  <embed src="{{$inv['file']}}" width='100%' height='600px'></embed>
                </td>
              <?php } ?>
          </tr> -->
        </table>
      </form>

    @else
      <table class="table table-bordered">
        <tr id="show-att">
          <td style="padding: 0px; color: white; background-color: rgb(50, 50, 50); font-size:16px;" colspan="2" id="attach_pdf">
          </td>
        </tr>
      </table>
    @endif
    </div>
  </div>
<div class="modal modal-danger fade" id="notapproved" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form role="form" method="post" action="{{url('payment_request/notapprove/'.$payment->id)}}">
          <div class="modal-header">
                <button type="button" class="close" onclick="modaldeletehide()">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
          </div>
          <div class="modal-body">
            <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <h4>Berikan alasan tidak menyetujui form Payment Request ini</h4>
                <textarea class="form-control" required="" name="alasan" style="height: 250px;"></textarea> 
                *Form Akan Dikirim kembali ke Pembuat Payment Request
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" onclick="modaldeletehide()">Cancel</button>
            <button type="submit" class="btn btn-danger">Not Approved</a>
          </div>
        </form>
      </div>
    </div>
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
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<script>

    var counts = 0;
    $(document).ready(function() {
      fetchTable();
      $("body").on("click",".btn-danger",function(){ 
        $(this).parents(".control-group").remove();
      });

      $('body').toggleClass("sidebar-collapse");

      var showAtt = "{{$payment->pdf}}";
      var path = "{{$file_path}}";


      if(showAtt.includes('.pdf')){
        $('#attach_pdf').append("<embed src='"+ path +"' type='application/pdf' width='100%' height='600px'>");
      }

      if(showAtt.includes('.png') || showAtt.includes('.PNG')){
        $('#attach_pdf').append("<embed src='"+ path +"' width='100%' height='600px'>");
      }

      if(showAtt.includes('.jp') || showAtt.includes('.JP')){
        $('#attach_pdf').append("<embed src='"+ path +"' width='100%' height='600px'>");
      }
    });

    CKEDITOR.replace('alasan' ,{
        filebrowserImageBrowseUrl : '{{ url("kcfinder_master") }}',
        height: '250px'
    });


    function loading(){
      $("#loading").show();
    }

     function reject_payment(id) {
        $('#notapproved').modal('show');
        $('[name=modalButtonDelete]').attr("id","delete_"+id);
    }

    function modaldeletehide(){
        $('#notapproved').modal('hide');
    }


  function fetchTable(){
    
    var id = "{{$payment->id}}";

    var data = {
        id:id
    }

    $('#loading').show();

    $.get('{{ url("fetch/check_payment_request_id") }}', data, function(result, status, xhr){
      if(result.status){
        $('#tablePayment').DataTable().clear();
        $('#tablePayment').DataTable().destroy();       
        $('#bodyTablePayment').html("");
        var listTableBody = "";
        var ppn = 0;
        var pph = 0;

        $.each(result.payment, function(key, value){
          if (value.ppn != null) {
            ppn = value.amount*value.ppn/100;
          }else{
            ppn = 0;
          }

          if (value.pph != null) {
            pph = value.amount*value.pph/100;
          }else{
            pph = 0;
          }

          listTableBody += '<tr class="member">';
                    listTableBody += '<td style="width:0.1%;">'+parseInt(key+1)+'</td>';
                    listTableBody += '<td style="width:1%;">'+value.supplier_name+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.invoice+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.currency+'</td>';
                    listTableBody += '<td style="width:1%;">'+(value.amount || '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+(ppn.toFixed(1) || '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+(pph.toFixed(1) || '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.net_payment+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.payment_term+'</td>';
                    listTableBody += '<td style="width:2%;">'+value.payment_due_date+'</td>';
                    listTableBody += '<td style="width:0.1%;">'+(value.surat_jalan|| '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+(value.faktur_pajak|| '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+(value.po_number|| '')+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.tt_date))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.do_date))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.dist_date_pch))+'</td>';
                    listTableBody += '<td style="width:2%;">'+getFormattedDate(new Date(value.inv_date))+'</td>';
                    listTableBody += '<td style="width:1%;"><a target="_blank" href="{{ url("files/invoice") }}/'+value.file+'"><i class="fa fa-paperclip"></i></a></td>';
                    listTableBody += '</tr>';
                    counts++;
        });

        $('#bodyTablePayment').append(listTableBody);

        $('#tablePayment tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="8"/>' );
        } );

        var table = $('#tablePayment').DataTable({
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
          "processing": true,
          initComplete: function() {
                    this.api()
                        .columns([1,2,9,13,14,15,17])
                        .every(function(dd) {
                            var column = this;
                            var theadname = $("#tablePayment th").eq([dd]).text();
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
                                    if ($("#tablePayment th").eq([dd]).text() == 'Category') {
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

        $('#tablePayment tfoot tr').appendTo('#tablePayment thead');

        $('#loading').hide();

      }
      else{
        audio_error.play();
        openErrorGritter('Error', result.message);
        $('#loading').hide();
      }
    });
  }

  function getFormattedDate(date) {
        var year = date.getFullYear();

        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        var month = date.getMonth();

        var day = date.getDate().toString();
        day = day.length > 1 ? day : '0' + day;
        
        return day + '-' + monthNames[month] + '-' + year;
    }


    // document.getElementById("myForm").addEventListener("submit", loading);
    
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

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