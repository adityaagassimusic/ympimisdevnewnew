@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
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
        vertical-align: middle;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid rgb(211,211,211);
        padding-top: 0;
        padding-bottom: 0;
        vertical-align: middle;
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid rgb(211,211,211);
        vertical-align: middle;
    }
    #loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
    <h1>
        {{ $page }}
    </h1>
    <ol class="breadcrumb">
        <li>
            <a data-toggle="modal" data-target="#uploadModal" class="btn btn-success btn-md" style="color:white"><i class="fa fa-upload"></i>Upload GRGI Data</a>
        </li>
    </ol>
</section>
@endsection


@section('content')

<section class="content">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
        <p style="position: absolute; color: White; top: 45%; left: 45%;">
            <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
        </p>
    </div>

    @if (session('status'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
        {{ session('status') }}
    </div>   
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-ban"></i> Error!</h4>
        {{ session('error') }}
    </div>   
    @endif

    <div class="row">
        <div class="col-xs-12" style="padding: 0px;">
            <div class="col-xs-2" style="padding-right: 0px;">
                <div class="input-group date pull-right" style="text-align: center;">
                    <div class="input-group-addon bg-green">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control monthpicker" name="month" id="month" placeholder="Select Month">  
                </div>
            </div>
            <div class="col-xs-2" style="padding-right: 0px;">
                <select class="form-control select3" id='Valcl' id='Valcl' data-placeholder="Select Valuation Class" style="width: 100%;">
                    <option value=""></option>
                    <option value="9010">9010 - Finished Products</option>
                    <option value="9030">9030 - Semifinished Products</option>
                    <option value="9040">9040 - Raw Materials</option>
                    <option value="9041">9041 - Indirect Materials</option>
                </select>
            </div>
            <div class="col-xs-2" style="padding-right: 0px;">
                <button onclick="fetchTable()" class="btn btn-primary">Search</button>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="nav-tabs-custom" style="margin-top: 1%;">
                <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                    <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Ending Balance</a></li>
                    <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Ending Balance Amount</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                        <h3 style="margin-top: 0%;"><span id="monthQty"></span></h3>
                        <table id="tableQty" class="table table-bordered table-hover" style="width: 100%;">
                            <thead id="headQty" style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                <th></th>
                            </thead>
                            <tbody id="bodyQty">
                                <td></td>
                            </tbody>
                        </table>
                    </div>

                    <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
                        <h3 style="margin-top: 0%;"><span id="monthAmount"></span></h3>
                        <table id="tableAmount" class="table table-bordered table-hover" style="width: 100%;">
                            <thead id="headAmount" style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                <th></th>
                            </thead>
                            <tbody id="bodyAmount">
                                <td></td>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 45%;">
        <div class="modal-content">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Upload GRGI Data</h4>
                <b>Format</b> :<br>
                [<i><b>Valcl</b></i>] [<i><b>StorageLoc</b></i>] [<i><b>GMC</b></i>] [<i><b>Receipt</b></i>] [<i><b>ReceiptAmount</b></i>] [<i><b>Issue</b></i>] [<i><b>IssueAmount</b></i>] [<i><b>Ending</b></i>] [<i><b>EndingAmount</b></i>]
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1">
                        <div class="col-xs-12">
                            <label>Select Month</label>
                            <div class="input-group date pull-right" style="text-align: center;">
                                <div class="input-group-addon bg-green">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control monthpicker" name="upload_month" id="upload_month" placeholder="Select Month">  
                            </div>
                            <textarea id="upload" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
                        </div>

                    </div>    
                </div>
            </div>
            <div class="modal-footer">
                <div class="row" style="margin-top: 7%; margin-right: 2%;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button onclick="uploadGrgi()" class="btn btn-success">Upload </button>
                </div>
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
<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");

        $('.monthpicker').datepicker({
            format: "yyyy-mm",
            startView: "months", 
            minViewMode: "months",
            autoclose: true,
            todayHighlight: true
        });

        $('.select3').select2({
            allowClear: true
        });
        
        fetchTable();
        
    });


    function uploadGrgi() {

        var month = $('#upload_month').val();
        var upload = $('#upload').val();

        if(month == '' || upload == ''){
            openErrorGritter('Error', 'All data must be complete');
            return false;
        }

        var data = {
            month : month,
            upload : upload,
        }

        $('#loading').show();
        $.post('{{ url("fetch/material/upload/grgi") }}', data, function(result, status, xhr){
            if(result.status){

                $('#upload_month').val('');
                $('#upload').val('');
                $('#uploadModal').modal('hide');

                $('#loading').hide();
                openSuccessGritter('Success', 'Upload GRGI Data Success');

            }else {
                $('#loading').hide();
                openErrorGritter('Error', result.message);
            }

        });
    }

    function fetchTable() {
        var month = $('#month').val();

        var data = {
            month : month
        }

        $('#loading').show();
        $.get('{{ url("fetch/material/report/grgi") }}', data,  function(result, status, xhr){
            if(result.status){
                $('#tableQty').DataTable().clear();
                $('#tableQty').DataTable().destroy();
                $('#monthQty').text(result.month_text);
                $('#headQty').html("");
                var headQty = '<tr>';
                headQty += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                headQty += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                headQty += '<th style="vertical-align: middle; text-align: center;">Valcl</th>';
                for (var i = 0; i < result.location.length; i++) {
                    headQty += '<th style="vertical-align: middle; text-align: center;">'+result.location[i].storage_location+'</th>';

                    var next_index = i + 1;
                    if(next_index >= result.location.length){
                        next_index = result.location.length - 1;
                    }

                    if((i == (result.location.length - 1)) || (result.location[i].category != result.location[next_index].category)){
                        headQty += '<th style="vertical-align: middle; text-align: center; background-color: rgb(252, 248, 227);">'+result.location[i].category+'</th>';
                    }
                }
                headQty += '</tr>';
                $('#headQty').append(headQty);


                $('#bodyQty').html("");
                if(result.grgi.length > 0){
                    var bodyQty = '';
                    for (var i = 0; i < result.material.length; i++) {
                        bodyQty += '<tr>';
                        bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].material_number+'</td>';
                        bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].material_description+'</td>';
                        bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].valcl+'</td>';

                        for (var j = 0; j < result.location.length; j++) {
                            var inserted = false;

                            for (var k = 0; k < result.grgi.length; k++) {
                                if((result.grgi[k].material_number == result.material[i].material_number) && (result.grgi[k].location == result.location[j].storage_location)){
                                    bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.grgi[k].ending_quantity+'</td>';
                                    inserted = true;
                                }
                            }

                            if(!inserted){
                                bodyQty += '<td style="vertical-align: middle; text-align: center;">0</td>';
                            }

                            var next_index = j + 1;
                            if(next_index >= result.location.length){
                                next_index = result.location.length - 1;
                            }
                            if((j == (result.location.length-1)) || (result.location[j].category != result.location[next_index].category)){
                                var inserted = false;

                                for (var z = 0; z < result.resume.length; z++) {
                                    if((result.resume[z].material_number == result.material[i].material_number) && (result.resume[z].category == result.location[j].category)){
                                        bodyQty += '<th style="vertical-align: middle; text-align: center; background-color: rgb(252, 248, 227);">'+result.resume[z].ending_quantity+'</th>';
                                        inserted = true;
                                    }
                                }

                                if(!inserted){
                                    bodyQty += '<td style="vertical-align: middle; text-align: center; background-color: rgb(252, 248, 227);">0</td>';
                                }
                            }
                        }
                        bodyQty += '</tr>';
                    }
                }
                $('#bodyQty').append(bodyQty);


                $('#tableQty').DataTable({
                    'dom': 'Bfrtip',
                    'responsive':true,
                    'lengthMenu': [ [ -1 ], [ 'Show all' ] ],
                    'buttons': {
                        buttons:[
                        {
                            extend: 'pageLength',
                            className: 'btn btn-default',
                        },{
                            extend: 'copy',
                            className: 'btn btn-success',
                            text: '<i class="fa fa-copy"></i> Copy',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        },{
                            extend: 'excel',
                            className: 'btn btn-info',
                            text: '<i class="fa fa-file-excel-o"></i> Excel',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        },{
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
                    'searching': true,
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true
                });












                $('#tableAmount').DataTable().clear();
                $('#tableAmount').DataTable().destroy();
                $('#monthAmount').text(result.month_text);
                $('#headAmount').html("");
                var headAmount = '<tr>';
                headAmount += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                headAmount += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                headAmount += '<th style="vertical-align: middle; text-align: center;">Valcl</th>';

                for (var i = 0; i < result.location.length; i++) {
                    headAmount += '<th style="vertical-align: middle; text-align: center;">'+result.location[i].storage_location+'</th>';

                    var next_index = i + 1;
                    if(next_index >= result.location.length){
                        next_index = result.location.length - 1;
                    }

                    if((i == (result.location.length - 1)) || (result.location[i].category != result.location[next_index].category)){
                        headAmount += '<th style="vertical-align: middle; text-align: center; background-color: rgb(252, 248, 227);">'+result.location[i].category+'</th>';
                    }
                }
                headAmount += '</tr>';
                $('#headAmount').append(headAmount);


                $('#bodyAmount').html("");
                if(result.grgi.length > 0){
                    var bodyAmount = '';
                    for (var i = 0; i < result.material.length; i++) {
                        bodyAmount += '<tr>';
                        bodyAmount += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].material_number+'</td>';
                        bodyAmount += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].material_description+'</td>';
                        bodyAmount += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].valcl+'</td>';

                        
                        for (var j = 0; j < result.location.length; j++) {
                            var inserted = false;

                            for (var k = 0; k < result.grgi.length; k++) {
                                if((result.grgi[k].material_number == result.material[i].material_number) && (result.grgi[k].location == result.location[j].storage_location)){
                                    bodyAmount += '<td style="vertical-align: middle; text-align: center;">'+result.grgi[k].ending_amount+'</td>';
                                    inserted = true;
                                }
                            }

                            if(!inserted){
                                bodyAmount += '<td style="vertical-align: middle; text-align: center;">0</td>';
                            }

                            var next_index = j + 1;
                            if(next_index >= result.location.length){
                                next_index = result.location.length - 1;
                            }
                            if((j == (result.location.length-1)) || (result.location[j].category != result.location[next_index].category)){
                                var inserted = false;

                                for (var z = 0; z < result.resume.length; z++) {
                                    if((result.resume[z].material_number == result.material[i].material_number) && (result.resume[z].category == result.location[j].category)){
                                        bodyAmount += '<th style="vertical-align: middle; text-align: center; background-color: rgb(252, 248, 227);">'+result.resume[z].ending_amount+'</th>';
                                        inserted = true;
                                    }
                                }

                                if(!inserted){
                                    bodyAmount += '<td style="vertical-align: middle; text-align: center; background-color: rgb(252, 248, 227);">0</td>';
                                }
                            }
                        }
                        bodyAmount += '</tr>';
                    }
                }
                $('#bodyAmount').append(bodyAmount);


                $('#tableAmount').DataTable({
                    'dom': 'Bfrtip',
                    'responsive':true,
                    'lengthMenu': [ [ -1 ], [ 'Show all' ] ],
                    'buttons': {
                        buttons:[
                        {
                            extend: 'pageLength',
                            className: 'btn btn-default',
                        },{
                            extend: 'copy',
                            className: 'btn btn-success',
                            text: '<i class="fa fa-copy"></i> Copy',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        },{
                            extend: 'excel',
                            className: 'btn btn-info',
                            text: '<i class="fa fa-file-excel-o"></i> Excel',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        },{
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
                    'searching': true,
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