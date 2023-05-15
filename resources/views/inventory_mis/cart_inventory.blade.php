@extends('layouts.notification')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
<style type="text/css">
    #tableDetail>tbody>tr:hover {
        background-color: #7dfa8c !important;
    }

    tbody>tr>td {
        padding: 10px 5px 10px 5px;
    }

    table.table-bordered {
        border: 1px solid black;
        vertical-align: middle;
        text-align: center;
    }

    table.table-bordered>thead>tr>th {
        border: 1px solid black;
        vertical-align: middle;

    }

    table.table-bordered>tbody>tr>td {
        border: 1px solid black;
        vertical-align: middle;
        height: 40px;
        padding: 2px 5px 2px 5px;
    }

    .contr #loading {
        display: none;
    }

    .label-status {
        color: black;
        font-size: 0.8vw;
        border-radius: 4px;
        padding: 3px 10px 5px 10px;
        border: 1.5px solid black;
        vertical-align: middle;
    }

    .radio {
        display: inline-block;
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

    /* Hide the browser's default radio button */
    .radio input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    /* Create a custom radio button */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #ccc;
        border-radius: 50%;
    }
    

    /* On mouse-over, add a grey background color */
    .radio:hover input~.checkmark {
        background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    .radio input:checked~.checkmark {
        background-color: #2196F3;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .radio input:checked~.checkmark:after {
        display: block;
    }

    /* Style the indicator (dot/circle) */
    .radio .checkmark:after {
        left: 9px;
        top: 6px;
        width: 6px;
        height: 11px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }


    .radio1 {
        display: inline-block;
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

    /* Hide the browser's default radio button */
    .radio1 input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    /* Create a custom radio button */
    .checkmark1 {
        position: absolute;
        top: -5px;
        left: 5px;
        height: 25px;
        width: 25px;
        background-color: #ccc;
        border-radius: 50%;
    }
    

    /* On mouse-over, add a grey background color */
    .radio1:hover input~.checkmark1 {
        background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    .radio1 input:checked~.checkmark1 {
        background-color: #2196F3;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark1:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .radio1 input:checked~.checkmark1:after {
        display: block;
    }

    /* Style the indicator (dot/circle) */
    .radio1 .checkmark1:after {
        left: 9px;
        top: 6px;
        width: 6px;
        height: 11px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    

    
</style>
@endsection

@section('content')
<section class="content">
    <div id="loading"
    style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 45%;">
        <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
    </p>
</div>

<div class="row">

    <div class="col-xs-10 col-xs-offset-1" style="margin-top: 1%; padding:0px;">
        <div class="form-group pull-left" style="margin-bottom: 10px">
        <a href="{{ url('index/inventory_mis') }}" class="btn btn-primary" style="color:white">
            &nbsp;<i class="fa fa-backward"></i>&nbsp;&nbsp;&nbsp;Back
        </a>
    </div>
        <h1 style="text-align: center;">MY CART</h1>
        
        @if(count($checklist) == 0)

        <center>
           <span style="font-size: 35px;"><img src="{{ url("images/shopping-empty.png")}}" width="20%" style="margin-bottom: 6px;">&nbsp;<br><b>Belum Ada List Item Yang Dipilih</b></span>
       </center>

       @else
       <br>
       <h3>DETAIL LIST ITEM</h3>
       <div class="table-responsive"  style="padding-bottom:1%;">
           @php
           $count = 0;
           @endphp

           <table class="table no-margin table-bordered table-striped" id="tabel1"">
            <thead style="background-color: #7570ce; color: white;" >
                <tr>
                    <th style="vertical-align: middle;  text-align: center;">#</th>
                    <th style="vertical-align: middle; text-align: center;">TANGGAL KEDATANGAN</th>
                    <th style="vertical-align: middle; text-align: center;">NO PO</th>
                    <th style="vertical-align: middle; text-align: center;">KATEGORI</th>
                    <th style="vertical-align: middle; text-align: center;">DESKRIPSI</th>
                    <th style="vertical-align: middle; text-align: center;">PERUNTUKAN</th>
                    <th style="vertical-align: middle; text-align: center;">JUMLAH</th>
                    <th style="vertical-align: middle; text-align: center;">No Seri</th>
                    <th style="vertical-align: middle; text-align: center;">Note</th>
                    <th style="vertical-align: middle; text-align: center;">CheckList<label class="radio1"><input onClick="checkAll(this)" type="checkbox" id="checkAllBox"/><span class="checkmark1"></span></label></th>
                    <th style="vertical-align: middle; text-align: center;">Aksi</th>

                </tr>
            </thead>
            <tbody id="bodytabel1">
                @for ($i = 0; $i < count($checklist); $i++)
                <tr>
                    <td style="text-align: center;" width="1%">{{ ++$count }}</td>
                    <td width="5%">{{ $checklist[$i]->date_to }}</td>
                    <td width="5%">{{ $checklist[$i]->no_po }}</td>
                    <td width="5%">{{ $checklist[$i]->category }}</td>
                    <td width="5%">{{ $checklist[$i]->nama_item }}</td>
                    <td width="5%">{{ $checklist[$i]->peruntukan }}</td>
                    <td width="5%">{{ $checklist[$i]->qty }}</td>
                    <td height="50"
                    style="text-align: left; border-right: 1px solid #333333;"
                    width="8%">
                    <input class="form-control" type="text" rows="3" onkeyup="myFunction1(this.id)" id="seri_{{ $checklist[$i]->id }}"
                    placeholder="No Seri ..."></input>
                </td>
                <td height="50"
                style="text-align: left; border-right: 1px solid #333333;"
                width="10%">
                <textarea class="form-control" type="text" rows="3" id="note_{{ $checklist[$i]->id }}"
                    placeholder="Note ..."></textarea>
                </td>
                <td width="5%">

                    <label class="radio" style="">
                        <span style="font-weight: bold; color: green;">OK</span>
                        <input type="checkbox"
                        id="result_{{ str_replace(' ', '',$checklist[$i]->id) }}"
                        name="result_{{ str_replace(' ', '', $checklist[$i]->id) }}"
                        value="OK">
                        <span class="checkmark"></span>
                    </label>
                </td>
                <td width="1%"><a href="javascript:void(0)" data-toggle="tooltip" onclick="deleteItem('{{ $checklist[$i]->id }}')" class="badge bg-red" id="n1" style="display: block;" data-original-title="" title=""><i class="fa fa-fw  fa-close"></i></a></td>

            </tr>
            @endfor


        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped" style="margin-bottom:0.5%;">
        <input type="text" id="checklist_id" hidden>
        <thead>
           <tr>
            <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
                Tanggal
            </th>
            <th style="font-size: 16px; width: 70%;">
                <input class="datepicker" style="font-size: 16px; height: 40px; width: 100%;" type="text"
                id="dates" placeholder="Date">
            </th>
        </tr>

        <tr>
            <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
                PIC MIS
            </th>
            <th style="font-size: 16px; width: 70%;">
                <select class="form-control select2" data-placeholder="Pilih PIC MIS" id="pic_mis"
                style="width: 100%; font-size: 16px">
                <option value=""></option>
                @foreach ($emps_mis as $row)
                <option value="{{ $row->employee_id }}_{{ $row->name }}">
                    {{ $row->employee_id }} - {{ $row->name }}
                </option>
                @endforeach
            </select>
        </th>
    </tr>
    <tr>
        <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
            PIC Pengambil
        </th>
        <th style="font-size: 16px; width: 70%;">
            <select class="form-control select2" data-placeholder="Pilih PIC Penerima" id="pic_penerima"
            style="width: 100%; font-size: 16px">
            <option value=""></option>
            @foreach ($emps as $row1)
            <option value="{{ $row1->employee_id }}_{{ $row1->name }}">
                {{ $row1->employee_id }} - {{ $row1->name }}
            </option>
            @endforeach
        </select>
    </th>
</tr>
<tr>
    <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
        Lokasi
    </th>
    <th style="font-size: 16px; width: 70%;">
        <select class="form-control selectArea" data-placeholder="Pilih Lokasi" id="lokasi"
        style="width: 100%; font-size: 16px">
        <option value=""></option>
        @foreach ($data_area as $area)
        <option value="{{ $area->location }}">
            {{ $area->location }}
        </option>
        @endforeach
    </select>
</th>
</tr>
<tr>
    <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
      Peruntukan
  </th>
  <th style="font-size: 16px; width: 70%;">
    <input style="font-size: 16px; height: 40px; width: 100%;" onkeyup="myFunction()" type="text"
    id="peruntukan" placeholder="Isikan Peruntukan ..">
</th>
</tr>
</thead>
</table>
</div>


<div class="col-xs-8 col-xs-offset-2" style="margin-bottom: 10%;">
    <button class="btn btn-lg btn-success" id="submit_checklist"
    style="width: 100%; font-weight: bold; font-size: 25px;" onclick="submitChecklist()">
    <i class="fa fa-save"></i>&nbsp;&nbsp;Submit
</button>
</div>

@endif


</div>

</div>


</section>
@endsection

@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('js/buttons.flash.min.js') }}"></script>
<script src="{{ url('js/jszip.min.js') }}"></script>
<script src="{{ url('js/vfs_fonts.js') }}"></script>
<script src="{{ url('js/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/buttons.print.min.js') }}"></script>
<script src="{{ url('js/icheck.min.js') }}"></script>
<script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");
        $('.select2').select2();

        $('.selectArea').select2({
            allowClear:true,
            tags: true
        });

    });


    $('.datepicker').datepicker({
        autoclose: true,
        format: "dd-mm-yyyy",
        todayHighlight: true,
    });


    var checklist = <?php echo json_encode($checklist); ?>;

    function myFunction1(id) {
        var x = document.getElementById(id);
        x.value = x.value.toUpperCase();  
    }

    function myFunction(st) {
        var x = document.getElementById("peruntukan");
        x.value = x.value.toUpperCase();  
    }

    function deleteItem(id) {
        if (confirm('Apakah Anda yakin akan menghapus list item?')) {

            var data = {
                id:id
            }
            $.get('{{ url("delete/list/item/mis") }}', data, function(result, status, xhr){
                if(result.status){
                    $('#loading').hide();
                    audio_ok.play();
                    openSuccessGritter('Success','Success dihapus');
                    location.reload();

                } else {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!',result.message);
                }
            })
        }
    }


    function checkAll(element){
        var id = $(element).attr("id");
        var checkVal = $('#'+id).is(":checked");
        if(checkVal) {
            total = $('#total').text();
            $('input:checkbox').prop('checked', true);
        }else{
            total = 0;
            $('input:checkbox').prop('checked', false);
        }
        $("#picked").html(total);
    }

    function buttonImage(elem) {
        $(elem).closest("th").find("input").click();
    }

    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                var img = $(input).closest("th").find("img");
                $(img).show();
                $(img).attr('src', e.target.result);
            };

            reader.readAsDataURL(input.files[0]);
        }
        $(input).closest("th").find("button").hide();
    }

    function submitChecklist() {
       if (confirm('Apakah Anda yakin akan menyimpannya?')) {

        $('#loading').show();
        var dates = $("#dates").val();
        var pic_mis = $("#pic_mis").val();
        var pic_penerima = $("#pic_penerima").val();
        var notes = $("#peruntukan").val();
        var area = $("#lokasi").val();
        var tag = [];

        $("input[type=checkbox]:checked").each(function() {
          if (this.id.indexOf("All") >= 0) {

          } else {
            var data = this.id.split('_');

            tag.push({
                'id': data[1],
                'note': $("#note_" + data[1]).val(),
                'no_seri': $("#seri_" + data[1]).val()
            });
        }
    });

        if (tag.length == 0) {
            $('#loading').hide();
            openErrorGritter("Error!", "List Item Tidak Boleh Kosong");
            return false;
        }

        if (dates == '' || pic_mis == '' || pic_penerima == '' || notes == '') {
            $('#loading').hide();
            openErrorGritter("Error!", "Semua data harus diisi");
            return false;
        }

        var formData = new FormData();
        formData.append('dates', dates);
        formData.append('pic_mis', pic_mis);
        formData.append('pic_penerima', pic_penerima);
        formData.append('note_peruntukan', notes);
        formData.append('area', area);
        formData.append('checklist_answer1', JSON.stringify(tag));

        $.ajax({
            url: "{{ url('input/check/item/inventory') }}",
            method: "POST",
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(result, status, xhr) {
                if (result.status) {
                    $('#loading').hide();
                    openSuccessGritter("Success", result.message);
                    setTimeout(function() {
                        window.location.href = "{{url('index/inventory_mis')}}";
                    }, 2000);

                } else {
                    openErrorGritter("Error!", result.message);
                }

            },
            error: function(result, status, xhr) {
                openErrorGritter("Error!", result.message);
                console.log(result.message);
            },
        })
    }

}

var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
var audio_ok = new Audio('{{ url("sounds/sukses.mp3") }}');

function openSuccessGritter(title, message) {
    jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-success',
        image: '{{ url("images/image-screen.png") }}',
        sticky: false,
        time: '5000'
    });
}

function openErrorGritter(title, message) {
    jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-danger',
        image: '{{ url("images/image-stop.png") }}',
        sticky: false,
        time: '5000'
    });
}

</script>
@endsection
