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
    <div class="col-xs-12">
        <table class="table table-bordered table-striped table-hover" id="tableDetail" width="100%">
            <thead style="background-color: #605ca8; color: white;">
                <tr>
                    <th style="text-align: center;">PIC</th>
                    <th style="text-align: center;">GMC</th>
                    <th style="text-align: center;">Description</th>
                    <th style="text-align: center;">Vendor</th>
                    <th style="text-align: center;">Vendor Name</th>
                    <th style="text-align: center;">PO Number</th>
                    <th style="text-align: center;">Quantity</th>
                    <th style="text-align: center;">ETA YMPI</th>
                    <th style="text-align: center;">Issue Date</th>
                    <th style="text-align: center;">PO Sent</th>
                    <th style="text-align: center;">PO Confirmed</th>
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
                </tr>
            </tbody>
        </table>
    </div>
</div>
</section>
@endsection

@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('js/buttons.flash.min.js') }}"></script>
<script src="{{ url('js/jszip.min.js') }}"></script>
<script src="{{ url('js/vfs_fonts.js') }}"></script>
<script src="{{ url('js/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/buttons.print.min.js') }}"></script>
<script src="{{ url("js/icheck.min.js")}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");

        showTable();
    });

    function showTable() {

        $('#loading').show();
        $.get('{{ url("fetch/material/control_delivery") }}', function(result, status, xhr){
            if(result.status){
                $('#loading').show();
                $('#tableDetail').DataTable().clear();
                $('#tableDetail').DataTable().destroy();
                $('#bodyDetail').html("");
                var tableData = "";

                var css = "padding:0px 5px 0px 5px; vertical-align: middle;";

                for (var i = 0; i < result.data.length; i++) {
                    tableData += '<tr>';
                    tableData += '<td style="'+css+' width:10%; text-align:left;">'+ callName(result.data[i].name) +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].material_number +'</td>';
                    tableData += '<td style="'+css+' width:20%; text-align:left;">'+ result.data[i].material_description +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].vendor_code +'</td>';
                    tableData += '<td style="'+css+' width:20%; text-align:left;">'+ result.data[i].vendor_name +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].po_number +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:right;">'+ result.data[i].quantity +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].eta_date +'</td>';
                    tableData += '<td style="'+css+' width:5%; text-align:center;">'+ result.data[i].issue_date +'</td>';
                    tableData += '<td style="'+css+' width:10%; text-align:center;'+ poSent(result.data[i])  +'</td>';
                    tableData += '<td style="'+css+' width:10%; text-align:center;'+ poConfirm(result.data[i]) +'</td>';
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

    return new_name;
}

function poSent(row) {
    var message = '">';

    if(row.po_send == 1){
        message += '<label class="label-status" style="background-color: #aee571; margin: 0px;">';
        message += '<p style="margin: 0px;">SENT</p>';
        message += '<p style="font-size: 10px; margin: 0px;">'+row.po_send_at+'</p>';
        message += '</label>';
    }else{
        message += '<label class="label-status" style="background-color: #f25450; margin: 0px;">';
        message += '<p style="margin: 0px; padding-top: 5%;">UNSENT</p>';
        message += '</label>';
    }

    return message;
}

function poConfirm(row) {
    var message = '';

    if(row.po_send == 1){
        if(row.po_confirm == 1){
            message += '"><label class="label-status" style="background-color: #aee571; margin: 0px;">';
            message += '<p style="margin: 0px;">CONFIRMED</p>';
            message += '<p style="font-size: 10px; margin: 0px;">'+row.po_confirm_at+'</p>';
            message += '</label>';
        }else{
            message += '"><label class="label-status" style="background-color: #f25450; margin: 0px;">';
            message += '<p style="margin: 0px; padding-top: 5%;">WAITING</p>';
            message += '</label>';
        }
    }else{
        message += 'background-color: #d9d9d9;">&nbsp;';
    }

    return message;
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
