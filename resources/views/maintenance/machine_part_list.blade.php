@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  thead>tr>th{
    text-align:center;
    overflow:hidden;
    padding: 3px;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  tfoot>tr>td{
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
    background-color: #605ca8;
    color: white;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    background-color: #fffcb7; 
  }

  table.table-bordered > tbody > tr > th{
    border:1px solid black;
    vertical-align: middle;
    padding:0;
    color: white;
    text-align: center;
    background-color: #605ca8;
  }

  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:0;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }
  .dataTable > thead > tr > th[class*="sort"]:after{
    content: "" !important;
  }

  .inprogress {
    background-color: #6ddb5e !important;
  }

  .pending {
    background-color: #e83e27 !important;
  }

</style>
@stop
@section('header')
<section class="content-header">
  <h1>
    {{ $title }}
    <small><span class="text-purple"> {{ $title_jp }}</span></small>
  </h1>
  <ol class="breadcrumb">
    <li>
      <button data-toggle="modal" data-target="#modalAdd" class="btn btn-sm bg-purple" style="color:white"><i class="fa fa-plus"></i> Add Spare Part</button>
    </li>
  </ol>
</section>
@stop
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box box-solid">
        <div class="box-body">
          <!-- <center><h3>Grand Total Price</h3></center> -->
          <table class="table table-bordered" id="table_master" style="width: 100%">
            <thead> 
              <tr>
                <th style="padding: 2px">No. </th>
                <th style="padding: 2px">Departemen</th>
                <th style="padding: 2px">Proses</th>
                <th style="padding: 2px">Product</th>
                <th style="padding: 2px">Nama Mesin / Equipment</th>
                <th style="padding: 2px">Nama Part</th>
                <th style="padding: 2px">Gambar</th>
                <th style="padding: 2px">Jumlah</th>
                <th style="padding: 2px">UoM</th>
                <th style="padding: 2px; width: 8%">Price</th>
                <th style="padding: 2px; width: 8%">Total Price</th>
                <th style="padding: 2px">Budget</th>
                <th style="padding: 2px">Note</th>
                <th style="padding: 2px">#</th>
              </tr>
            </thead>
            <tbody id="body_master"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalAdd">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">Department:<span class="text-red">*</span></span>
              </div>
              <div class="col-xs-6">
                <input type="hidden" id="id_part">
                <select class="form-control select2" id="dept" name="dept" style="width: 100%;" data-placeholder="Select Department">
                  <option value=""></option>
                  @foreach($departments as $data)
                  <option value="{{ $data->department }}">{{ $data->department }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">Process:<span class="text-red">*</span></span>
              </div>
              <div class="col-xs-6">
                <select class="form-control select2" id="proc" name="proc" style="width: 100%;" data-placeholder="Select Process">
                  <option value=""></option>
                  <option value="Sanding">Sanding</option>
                  <option value="Machining">Machining</option>
                  <option value="Senban">Senban</option>
                  <option value="Press">Press</option>
                  <option value="Cuci alkali / asam">Cuci alkali / asam</option>
                  <option value="Cutting">Cutting</option>
                  <option value="Wire Cut">Wire Cut</option>
                  <option value="Shearing">Shearing</option>
                  <option value="Annealing">Annealing</option>
                  <option value="Machining ">Machining </option>
                  <option value="Buffing">Buffing</option>
                  <option value="Sanding">Sanding</option>
                  <option value="Bulge">Bulge</option>
                  <option value="Kanbiki ">Kanbiki </option>
                  <option value="Swaging">Swaging</option>
                  <option value="Pipe bending">Pipe bending</option>
                  <option value="Cutting machine">Cutting machine</option>
                  <option value="Koban nuki">Koban nuki</option>
                  <option value="Onko Hikiage">Onko Hikiage</option>
                  <option value="Onko Joumen">Onko Joumen</option>
                  <option value="Sunpo Giri">Sunpo Giri</option>
                  <option value="Plasma Welding">Plasma Welding</option>
                  <option value="Seam roller">Seam roller</option>
                  <option value="Drilling">Drilling</option>
                  <option value="Grinding">Grinding</option>
                  <option value="Menkiri">Menkiri</option>
                  <option value="Stamp">Stamp</option>
                  <option value="Punch">Punch</option>
                  <option value="Rezoneta heater">Rezoneta heater</option>
                  <option value="Seasoning">Seasoning</option>
                  <option value="High Frequency Welding">High Frequency Welding</option>
                  <option value="Burner">Burner</option>
                  <option value="Drilling ">Drilling </option>
                  <option value="Crane">Crane</option>
                  <option value="Barrel">Barrel</option>
                  <option value="Chiller">Chiller</option>
                  <option value="AHU">AHU</option>
                  <option value="Dryer Painting">Dryer Painting</option>
                  <option value="Paint Booth">Paint Booth</option>
                  <option value="Silver Elektro Plating">Silver Elektro Plating</option>
                  <option value="Nickel Elektro Plating">Nickel Elektro Plating</option>
                  <option value="Dryer Plating">Dryer Plating</option>
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">Product:<span class="text-red">*</span></span>
              </div>
              <div class="col-xs-6">
                <select class="form-control select2" multiple="multiple" name="product" id='product' placeholder="Select Product" style="width: 100%;">
                  <option value=""></option>
                  <option value="Saxophone">Saxophone</option>
                  <option value="Flute">Flute</option>
                  <option value="Clarinet">Clarinet</option>
                  <option value="Recorder">Recorder</option>
                  <option value="Venova">Venova</option>
                  <option value="Mouth Piece">Mouth Piece</option>
                  <option value="Pianica">Pianica</option>
                  <option value="Case">Case</option>
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">Machine Name:<span class="text-red">*</span></span>
              </div>
              <div class="col-xs-6">
                <select class="form-control select2" name="machine" id='machine' data-placeholder="Select Machine / Equipment" style="width: 100%;">
                  <option value=""></option>
                  @foreach($machine_groups as $data)
                  <option value="{{ $data->machine_group }}">{{ $data->machine_group }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">Part Name:</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="part_name" name="part_name" placeholder="Input Part Name">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">Part Picture:</span>
              </div>
              <div class="col-xs-6">
                <input type="file" class="form-control" id="part_picture" name="part_picture">
              </div>
            </div>


            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">Quantity:<span class="text-red">*</span></span>
              </div>
              <div class="col-xs-6">
                <input type="number" class="form-control" id="qty" name="qty" placeholder="Input Quantity" onkeyup="sum_total()">
              </div>
            </div>


            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">UoM:<span class="text-red">*</span></span>
              </div>
              <div class="col-xs-6">
                <select class="form-control select2" id="uom" name="uom" data-placeholder="Select UoM" style="width: 100%;">
                  <option value=""></option>
                  <option value="pc">pc</option>
                  <option value="set">set</option>
                </select>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">Price (Rp):<span class="text-red">*</span></span>
              </div>
              <div class="col-xs-6">
                <input type="number" class="form-control" id="price" name="price" onkeyup="sum_total()" placeholder="Input Price">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">Total Price (Rp):</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="tot_price" name="tot_price" readonly>
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <div class="col-xs-4" style="padding: 0px;" align="right">
                <span style="font-weight: bold; font-size: 16px;">Note:</span>
              </div>
              <div class="col-xs-6">
                <input type="text" class="form-control" id="note" name="note" placeholder="Input Note">
              </div>
            </div>

            <div class="col-xs-12" style="padding-bottom: 1%;">
              <button class="btn btn-sm btn-success pull-right" onclick="save_part()"><i class="fa fa-check"></i> Save</button>
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
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
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
    $('body').toggleClass("sidebar-collapse");

    getData();
  });

  $('.select2').select2({
    dropdownParent: $("#modalAdd")
  });

  function getData() {
    $.get('{{ url("fetch/maintenance/machine/part_list") }}', function(result, status, xhr){
      $('#table_master').DataTable().clear();
      $('#table_master').DataTable().destroy();
      $("#body_master").empty();
      var body = "";
      var no = 1;

      $.each(result.datas, function(index, value){
        body += "<tr>";
        body += "<td>"+no+"</td>";
        body += "<td>"+value.department+"</td>";
        body += "<td>"+value.process+"</td>";
        body += "<td>"+value.product+"</td>";
        body += "<td>"+value.machine_group+"</td>";
        body += "<td>"+(value.part_name || '-')+"</td>";
        if (value.part_picture) {
          body += "<td> <a href='{{ url('mainteannce/machine_part/') }}/"+value.part_picture+"' target='_blank'><i class='fa  fa-file-image-o'></i> Picture</a> </td>";
        } else {
          body += "<td> - </td>";
        }
        body += "<td>"+value.qty+"</td>";
        body += "<td>"+value.uom+"</td>";
        body += "<td>Rp. "+formatUang(value.price,"")+"</td>";
        body += "<td>Rp. "+formatUang(value.total_price,"")+"</td>";
        body += "<td>"+(value.budget || '')+"</td>";
        body += "<td>"+(value.note || '')+"</td>";
        body += "<td><button class='btn btn-warning btn-xs' onclick='openModalEdit("+value.id+")'><i class='fa fa-pencil'></i></button></td>";
        body += "</tr>";

        no++;
      })

      $("#body_master").append(body);

      $('#table_master tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input style="text-align: center; color: #323633; width: 100%" type="text" placeholder="Search '+title+'"/>' );
      } );

      var table = $('#table_master').DataTable({
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
          ]
        },
        'paging': true,
        'lengthChange': true,
        'searching': true,
        'ordering': false,
        'info': true,
        'autoWidth': true,
        "sPaginationType": "full_numbers",
        "bJQueryUI": true,
        "bAutoWidth": false,
        "processing": false,
      });
    })
  }

  function sum_total() {
    var price = $("#price").val();
    var qty = $("#qty").val();

    var tot = parseInt(price) * parseInt(qty);

    $("#tot_price").val(tot);
  }

  function save_part() {
    // var data = {
    //   id : $("#id_part").val(),
    //   dept : $("#dept").val(),
    //   proc : $("#proc").val(),
    //   product : $("#product").val(),
    //   machine : $("#machine").val(),
    //   part_name : $("#part_name").val(),
    //   qty : $("#qty").val(),
    //   uom : $("#uom").val(),
    //   price : $("#price").val(),
    //   tot_price : $("#tot_price").val(),
    //   note : $("#note").val(),
    //   part_picture : $("#part_picture").val()
    // }

    if ($("#dept").val() == "" ||
      $("#proc").val() == "" ||
      $("#product").val() == "" ||
      $("#machine").val() == "" ||
      $("#qty").val() == "" ||
      $("#uom").val() == "" ||
      $("#price").val() == "") {
      openErrorGritter('Error', 'Terdapat Kolom yang Belum Lengkap');
    return false;
  }

  if ($("#part_name").val() == "" && document.getElementById("part_picture").files.length == 0 ) {
    openErrorGritter('Error', 'Terdapat Kolom yang Belum Lengkap');
    return false;
  }

  var myFormData = new FormData();
  myFormData.append('id', $("#id_part").val());
  myFormData.append('dept', $("#dept").val());
  myFormData.append('proc', $("#proc").val());
  myFormData.append('product', $("#product").val());
  myFormData.append('machine', $("#machine").val());
  myFormData.append('part_name', $("#part_name").val());
  myFormData.append('qty', $("#qty").val());
  myFormData.append('uom', $("#uom").val());
  myFormData.append('price', $("#price").val());
  myFormData.append('tot_price', $("#tot_price").val());
  myFormData.append('note', $("#note").val());
  myFormData.append('part_picture', document.getElementById("part_picture").files[0]);

  $.ajax({
    url: '{{ url("post/maintenance/machine/part_list") }}',
    type: 'POST',
    processData: false,
    contentType: false, 
    dataType : 'json',
    data: myFormData,
    success: function(data) {
      openSuccessGritter('Sukses', 'Data Part Berhasil ditambahkan');
      $("#dept").val('').trigger('change');
      $("#proc").val('').trigger('change');
      $("#product").val('');
      $("#machine").val('').trigger('change');
      $("#part_name").val('');
      $("#qty").val('');
      $("#uom").val('').trigger('change');
      $("#price").val('');
      $("#tot_price").val('');
      $("#note").val('');

      $("#modalAdd").modal('hide');
      getData();
    }
  });

  // $.post('{{ url("post/maintenance/machine/part_list") }}', data, function(result, status, xhr){
  //   if (result.status) {

  //   }
  // })
}

function openModalEdit(id) {
  var data = {
    id : id
  }

  $.get('{{ url("fetch/maintenance/machine/part_list") }}', data,function(result, status, xhr){
    $("#modalAdd").modal('show');
    $("#id_part").val(result.datas[0].id);
    $("#dept").val(result.datas[0].department).trigger('change');
    $("#proc").val(result.datas[0].process).trigger('change');

    $('#product').val(result.datas[0].product.split(", ")).change();

    $("#machine").val(result.datas[0].machine_group).trigger('change');
    $("#part_name").val(result.datas[0].part_name);
    $("#qty").val(result.datas[0].qty);
    $("#uom").val(result.datas[0].uom).trigger('change');
    $("#price").val(result.datas[0].price);
    $("#tot_price").val(result.datas[0].total_price);
    $("#note").val(result.datas[0].note);
  })
}

function formatUang(angka, prefix) {
  var angka_int = parseInt(angka);
  var number_string = angka_int.toString().replace(/[^,\d]/g, ""),
  split = number_string.split(","),
  sisa = split[0].length % 3,
  rupiah = split[0].substr(0, sisa),
  ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    // tambahkan titik jika yang di input sudah menjadi angka ribuan
    if (ribuan) {
      separator = sisa ? "." : "";
      rupiah += separator + ribuan.join(".");
    }

    rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
    return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
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

</script>
@endsection