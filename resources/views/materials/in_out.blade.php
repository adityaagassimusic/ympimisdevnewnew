@php
function callName($name){
    $new_name = '';
    $blok_m = [ 'M.', 'Moch.', 'Mochammad', 'Moh.', 'Mohamad', 'Mokhamad', 'Much.', 'Muchammad', 'Muhamad', 'Muhammaad', 'Muhammad', 'Mukammad', 'Mukhamad', 'Mukhammad'];

    if( strlen($name) > 0 ){
        if( str_contains($name, ' ') ){
            $name = explode(' ', $name);
            if( in_array($name[0], $blok_m) ){
                $new_name = 'M.';
                for ($i=1; $i < count($name); $i++) { 
                    if($i == 1){
                        $new_name .= ' ';
                        $new_name .= $name[$i];
                    }else{
                        $new_name .= ' ';
                        $new_name .= substr($name[$i], 0, 1) . '.';
                    }
                }
            }else{
                for ($i=0; $i < count($name); $i++) { 
                    if($i == 0){
                        $new_name .= ' ';
                        $new_name .= $name[$i];
                    }else{
                        $new_name .= ' ';
                        $new_name .= substr($name[$i], 0, 1) . '.';
                    }
                }
            }

        }else{
            $new_name = $name;
        }
    }else{
        $new_name = '-';
    }

    return $new_name;
}
@endphp
@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
<style type="text/css">
    #tableDetail > tbody > tr:hover{
        background-color: #7dfa8c !important;
    }
    tbody>tr>td{
        padding: 10px 5px 10px 5px;
    }
    table.table-bordered{
        border:1px solid black;
        vertical-align: middle;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
        vertical-align: middle;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid black;
        vertical-align: middle;
        height: 40px;
        padding:  2px 5px 2px 5px;
    }
    .contr #loading {
        display: none;
    }
    .label-status{
        color: black; 
        font-size: 12px;
        border-radius: 4px;
        padding: 1px 5px 2px 5px; 
        border: 1px solid black;
        min-width: 120px;
        height: 35px;
        vertical-align: middle;
    }

    .label-file{
        color: black; 
        font-size: 12px;
        border-radius: 4px;
        padding: 1px 5px 2px 5px; 
        border: 1px solid black;
        min-width: 120px;
        height: 20px;
        vertical-align: middle;
    }

    .modal-dialog{
        overflow-y: initial !important
    }

    .modal-body{
        max-height: 80vh;
        overflow-y: auto;
    }

</style>
@endsection
@section('header')
<section class="content-header">
    <h1>
        {{ $page }}
    </h1>
    <ol class="breadcrumb">
        <li>
            <a data-toggle="modal" data-target="#inOutModal" class="btn btn-success btn-sm" style="color:white">
                &nbsp;<i class="fa fa-plus-square-o"></i>&nbsp;Add Material In/Out
            </a>
        </li>
    </ol>
</section>
@endsection
@section('content')
<section class="content" style="font-size: 0.8vw;">
    <div id="loading"
    style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 45%;">
        <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
    </p>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-solid">
            <div class="box-body">
                <div class="col-xs-3" style="padding: 0px;">
                    <div class="box box-primary box-solid" style="margin: 0px;">
                        <div class="box-body">
                            <div class="col-xs-6" style="padding-left: 0px; padding-right: 2px;">
                                <div class="form-group">
                                    <label>Entry From</label>
                                    <div class="input-group date" style="width: 100%;">
                                        <input type="text" placeholder="Select Entry Date" class="form-control datepicker pull-right" id="entry_from" style="font-size: 0.8vw;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6" style="padding-left: 2px; padding-right: 0px;">
                                <div class="form-group">
                                    <label>Entry To</label>
                                    <div class="input-group date" style="width: 100%;">
                                        <input type="text" placeholder="Select Entry Date" class="form-control datepicker pull-right" id="entry_to" style="font-size: 0.8vw;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-3" style="padding-right: 0px;">
                    <div class="box box-primary box-solid" style="margin: 0px;">
                        <div class="box-body">
                            <div class="col-xs-6" style="padding-left: 0px; padding-right: 2px;">
                                <div class="form-group">
                                    <label>Posting From</label>
                                    <div class="input-group date" style="width: 100%;">
                                        <input type="text" placeholder="Select Posting Date" class="form-control datepicker pull-right" id="posting_from" style="font-size: 0.8vw;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-6" style="padding-left: 2px; padding-right: 0px;">
                                <div class="form-group">
                                    <label>Posting To</label>
                                    <div class="input-group date" style="width: 100%;">
                                        <input type="text" placeholder="Select Posting Date" class="form-control datepicker pull-right" id="posting_to" style="font-size: 0.8vw;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-6" style="padding-right: 0px;">
                    <div class="box box-primary box-solid" style="margin: 0px;">
                        <div class="box-body">
                            <div class="col-xs-12">                                
                                <div class="row">
                                    <div class="col-xs-2" style="padding-left: 0px; padding-right: 2px;">
                                        <div class="form-group">
                                            <label>Movement</label>
                                            <select class="form-control" data-placeholder="Select Mvt" id="movement" style="width:100%">
                                                <option value=""></option>
                                                <option value="IN">IN</option>
                                                <option value="OUT">OUT</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-4" style="padding-left: 0px; padding-right: 2px;">
                                        <div class="form-group">
                                            <label>PIC Control</label>
                                            <select class="form-control select2" multiple="multiple" data-placeholder="Select PIC" id="control" style="width:100%">
                                                <option value=""></option>
                                                @foreach($controls as $control) 
                                                <option value="{{ $control->control }}">{{ callName($control->name) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="padding-left: 2px; padding-right: 0px;">
                                        <div class="form-group">
                                            <label>Vendor</label>
                                            <select class="form-control select2" multiple="multiple" data-placeholder="Select Vendor" id="vendor" style="width: 100%;">
                                                <option value=""></option>
                                                @foreach($vendors as $vendor) 
                                                <option value="{{ $vendor->vendor_code }}">{{ $vendor->vendor_code }} - {{ $vendor->vendor_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12" style="margin-top: 0.75%;">
                    <div class="form-group pull-right" style="margin: 0px;">
                        <button onClick="clearConfirmation()" class="btn btn-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Clear&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                        <button onClick="showTable()" class="btn btn-primary"><span class="fa fa-search"></span> Search</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12">
            <table class="table table-bordered table-striped table-hover" id="tableDetail" width="100%">
                <thead style="background-color: #605ca8; color: white;">
                    <tr>
                        <th style="text-align: center;">Entry</th>
                        <th style="text-align: center;">Posting</th>
                        <th style="text-align: center;">Material</th>
                        <th style="text-align: center;">Description</th>
                        <th style="text-align: center;">Vendor</th>
                        <th style="text-align: center;">Vendor Name</th>
                        <th style="text-align: center;">PIC</th>
                        <th style="text-align: center;">PO Number</th>
                        <th style="text-align: center;">Item Line</th>
                        <th style="text-align: center;">Qty</th>
                        <th style="text-align: center;">Remark</th>
                        <th style="text-align: center;">Issue</th>
                        <th style="text-align: center;">Receive</th>
                        <th style="text-align: center;">Doc. BC</th>
                    </tr>
                </thead>
                <tbody id="bodyDetail">
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<div class="modal fade" id="inOutModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload In/Out Material</h4>
                <span>
                    Format Upload:<br>
                    [<b><i>ENTRY DATE</i></b>]
                    [<b><i>POSTING DATE</i></b>]
                    [<b><i>PO NUMBER</i></b>]
                    [<b><i>ITEM LINE</i></b>]
                    [<b><i>BC DOC.</i></b>]
                    [<b><i>GMC</i></b>]
                    [<b><i>MVT</i></b>]
                    [<b><i>SLOC</i></b>]
                    [<b><i>TO LOC/CC</i></b>]
                    [<b><i>QUANTITY</i></b>]
                </span>
            </div>
            <div class="modal-body" style="min-height: 100px">
                <div class="form-group">
                    <textarea id="upload" style="height: 100px; width: 100%;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success pull-right" onclick="uploadData('inout');">Upload</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadResult">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload Result</h4>
            </div>
            <div class="modal-body" style="min-height: 100px">
                <span style="font-size:1.5vw;">Success: <span id="suceess-count" style="font-style:italic; font-weight:bold; color: green;"></span> Row(s)</span>  
                <span style="font-size:1.5vw;"> ~ Error: <span id ="error-count" style="font-style:italic; font-weight:bold; color: red;"></span> Row(s)</span>

                <table id="tableError" style="border: none;">
                    <tbody id="bodyError">
                        <tr>
                            <th></th>
                            <th></th>
                        </tr>
                    </tbody>
                </table> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>                
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('js/buttons.flash.min.js') }}"></script>
<script src="{{ url('js/jszip.min.js') }}"></script>
<script src="{{ url('js/vfs_fonts.js') }}"></script>
<script src="{{ url('js/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/buttons.print.min.js') }}"></script>
<script src="{{ url("js/icheck.min.js")}}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true
        });

        $('.select2').select2();

        $('#movement').select2({
            allowClear: true
        });

        // showTable();
        // showChart();
    });

    function clearConfirmation(){
        location.reload(true);      
    }

    function uploadData(id){
        $('#loading').show();
        var upload = $('#upload').val();
        var data = {
            id:id,
            upload:upload
        }

        if(upload == ""){
            alert('Data upload tidak boleh kosong');
            return false;
        }

        $.post('{{ url("upload/material/material_monitoring") }}', data, function(result, status, xhr) {
            if(result.status){

                $('#upload').val('');
                $('#inOutModal').modal('hide');

                $('#suceess-count').text(result.ok_count.length);
                $('#error-count').text(result.error_count.length);

                $('#bodyError').html("");
                var tableData = "";
                var css = "padding: 0px 5px 0px 5px;";
                for (var i = 0; i < result.error_count.length; i++) {
                    var error = result.error_count[i].split('_');
                    tableData += '<tr>';
                    tableData += '<td style="'+css+' width:20%; text-align:left;">Row '+ error[0] +'</td>';
                    tableData += '<td style="'+css+' width:80%; text-align:left;">: '+ error[1] +'</td>';
                    tableData += '</tr>';
                }

                if(result.error_count.length > 0){
                    $('#bodyError').append(tableData);
                    $('#tableError').show();
                }

                $('#uploadResult').modal('show');
                $('#loading').hide();


                openSuccessGritter('Success!', result.message);
            }
            else{
                $('#loading').hide();
                alert(result.message);
            }
        });
    }


    function showTable() {

        var entry_from = $('#entry_from').val();
        var entry_to = $('#entry_to').val();
        var posting_from = $('#posting_from').val();
        var posting_to = $('#posting_to').val();
        var movement = $('#movement').val();
        var control = $('#control').val();
        var vendor = $('#vendor').val();

        var data = {
            entry_from : entry_from,
            entry_to : entry_to,
            posting_from : posting_from,
            posting_to : posting_to,
            movement : movement,
            control : control,
            vendor : vendor
        }


        $('#loading').show();
        $.get('{{ url("fetch/material/in_out") }}', data, function(result, status, xhr){
            if(result.status){
                $('#loading').show();

                $('#tableDetail').DataTable().clear();
                $('#tableDetail').DataTable().destroy();
                $('#bodyDetail').html("");
                var tableData = "";
                var css = "padding:0px 5px 0px 5px; vertical-align: middle;";
                for (var i = 0; i < result.data.length; i++) {
                    tableData += '<tr>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].entry_date +'</td>';                    
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].posting_date +'</td>';                    
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].material_number +'</td>';
                    tableData += '<td style="'+css+' width:20%; text-align:left;">'+ result.data[i].material_description +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].vendor_code +'</td>';
                    tableData += '<td style="'+css+' width:20%; text-align:left;">'+ result.data[i].vendor_name +'</td>';
                    tableData += '<td style="'+css+' width:10%; text-align:left;">'+ callName(result.data[i].name) +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].po_number +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].item_line +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:right;">'+ result.data[i].quantity +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ ( (result.data[i].receive_location == null) ? 'IN' : 'OUT')  +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].issue_location +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ (result.data[i].receive_location || '') +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ (result.data[i].bc_document || '-') +'</td>';
                    tableData += '</tr>';

                }



                $('#bodyDetail').append(tableData);
                $('#tableDetail').DataTable({
                    'dom': 'Bfrtip',
                    'responsive':true,
                    'lengthMenu': [
                    [ -1 ],
                    [ 'Show all' ]
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
                    'searching': true,
                    'ordering': false,
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true
                });



                $('#loading').hide();
            }

        });

}


function callName(name){
    var new_name = '';
    var blok_m = [
    'M.',
    'Moch.',
    'Mochammad',
    'Moh.',
    'Mohamad',
    'Mokhamad',
    'Much.',
    'Muchammad',
    'Muhamad',
    'Muhammaad',
    'Muhammad',
    'Mukammad',
    'Mukhamad',
    'Mukhammad'
    ];


    if(name != null){

        if(name.includes(' ')){
            name = name.split(' ');

            if(blok_m.includes(name[0])){
                new_name = 'M.';
                for (i=1; i < name.length; i++) { 
                    if(i == 1){
                        new_name += ' ';
                        new_name += name[i];
                    }else{
                        new_name += ' ';
                        new_name += name[i].substr(0,1) + '.';
                    }
                }
            }else{
                for (i=0; i < name.length; i++) { 
                    if(i == 0){
                        new_name += ' ';
                        new_name += name[i];
                    }else{
                        new_name += ' ';
                        new_name += name[i].substr(0,1) + '.';
                    }
                }
            }

        }else{
            new_name = name;
        }
    }else{
        new_name = '-';
    }
    
    return new_name;
}


var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

function refreshAll() {
    location.reload(true);
}

function openSuccessGritter(title, message) {
    jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-success',
        image: '{{ url('images/image-screen.png') }}',
        sticky: false,
        time: '5000'
    });
}

function openErrorGritter(title, message) {
    jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-danger',
        image: '{{ url('images/image-stop.png') }}',
        sticky: false,
        time: '5000'
    });
}


</script>

@endsection
