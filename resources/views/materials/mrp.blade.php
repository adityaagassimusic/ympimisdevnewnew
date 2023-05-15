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
            <a data-toggle="modal" data-target="#uploadModal" class="btn btn-success btn-md" style="color:white"><i class="fa fa-upload"></i>&nbsp;Upload Back Order</a>
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
                <button onclick="fetchTable()" class="btn btn-primary">Search</button>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="nav-tabs-custom" style="margin-top: 1%;">
                <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                    <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">MRP</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                        <table id="tableQty" class="table table-bordered table-hover" style="width: 100%;">
                            <thead id="headQty" style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                <th></th>
                            </thead>
                            <tbody id="bodyQty">
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
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Upload Back Order Data Data</h4>
                Format : [Delivery Date][Material Number][Quantity]
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1">
                        <div class="col-xs-12">
                            <label>Select MRP Month</label>
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
        
        fetchTable();
        
    });


    function fetchTable() {

        var month = $('#month').val();
        var data = {
            month:month
        }

        $('#loading').show();
        $.get('{{ url("fetch/material/mrp") }}',  function(result, status, xhr){
            if(result.status){
                $('#tableQty').DataTable().clear();
                $('#tableQty').DataTable().destroy();
                $('#headQty').html("");
                var headQty = '<tr>';
                headQty += '<th rowspan="2" style="vertical-align: middle; text-align: center;">GMC</th>';
                headQty += '<th rowspan="2" style="vertical-align: middle; text-align: center;">Desc.</th>';
                headQty += '<th rowspan="2" style="vertical-align: middle; text-align: center;">LT</th>';
                headQty += '<th rowspan="2" style="vertical-align: middle; text-align: center;">MOQ</th>';
                headQty += '<th rowspan="2" style="vertical-align: middle; text-align: center;">MPQ</th>';
                headQty += '<th rowspan="2" style="vertical-align: middle; text-align: center;">Safety</th>';
                headQty += '<th rowspan="2" style="vertical-align: middle; text-align: center;">Ending</th>';
                for (var i = 0; i < result.interval.length; i++) {
                    headQty += '<th colspan="7" style="vertical-align: middle; text-align: center;">'+result.interval[i].text_month.substr(0, 3)+'-'+result.interval[i].text_year+'</th>';
                }
                headQty += '</tr>';
                headQty += '</tr>';
                for (var i = 0; i < result.interval.length; i++) {
                    headQty += '<th style="vertical-align: middle; text-align: center;">Beginning</th>';
                    headQty += '<th style="vertical-align: middle; text-align: center;">BO</th>';
                    headQty += '<th style="vertical-align: middle; text-align: center;">Usage</th>';
                    headQty += '<th style="vertical-align: middle; text-align: center;">Add</th>';
                    headQty += '<th style="vertical-align: middle; text-align: center;">Date</th>';
                    headQty += '<th style="vertical-align: middle; text-align: center;">Stock Out</th>';
                    headQty += '<th style="vertical-align: middle; text-align: center;">Expired</th>';
                }
                headQty += '</tr>';

                $('#headQty').append(headQty);

                $('#bodyQty').html("");
                
                var bodyQty = '';
                for (var i = 0; i < result.material.length; i++) {
                    bodyQty += '<tr>';
                    bodyQty += '<td style="background-color: rgb(252, 248, 227); vertical-align: middle; text-align: center;">'+result.material[i].material_number+'</td>';
                    bodyQty += '<td style="background-color: rgb(252, 248, 227); vertical-align: middle; text-align: left;">'+result.material[i].material_description+'</td>';
                    bodyQty += '<td style="background-color: rgb(252, 248, 227); vertical-align: middle; text-align: center;">'+result.material[i].lead_time+'</td>';
                    bodyQty += '<td style="background-color: rgb(252, 248, 227); vertical-align: middle; text-align: center;">'+result.material[i].minimum_order+'</td>';
                    bodyQty += '<td style="background-color: rgb(252, 248, 227); vertical-align: middle; text-align: center;">'+result.material[i].multiple_order+'</td>';
                    bodyQty += '<td style="background-color: rgba(255, 209, 0, .5); vertical-align: middle; text-align: center;">'+(Math.ceil(result.material[i].safety * result.material[i].dts)  || '-')+'</td>';


                    var begining = 0;
                    var end_inserted = false;                    
                    for (var a = 0; a < result.grgi.length; a++) {
                        if(result.grgi[a].material_number == result.material[i].material_number){
                            begining = result.grgi[a].ending_quantity;
                            bodyQty += '<td style="background-color: rgb(204, 255, 255); vertical-align: middle; text-align: center;">'+begining+'</td>';
                            end_inserted = true;
                        }
                    }
                    if(!end_inserted){
                        bodyQty += '<td style="background-color: rgb(204, 255, 255); vertical-align: middle; text-align: center;">0</td>';
                    }


                    begining = begining - Math.ceil(result.material[i].safety * result.material[i].dts);
                    for (var j = 0; j < result.interval.length; j++) {
                        bodyQty += '<td style="background-color: #ff99cc; vertical-align: middle; text-align: center;">'+begining+'</td>';

                        var end = begining;

                        var bo = 0;
                        var bo_inserted = false;                    
                        for (var b = 0; b < result.bo.length; b++) {
                            if( (result.bo[b].material_number == result.material[i].material_number) && (result.bo[b].month == result.interval[j].month)){
                                bo = result.bo[b].quantity;
                                bodyQty += '<td style="background-color: #7dfa8c; vertical-align: middle; text-align: center;">'+result.bo[b].quantity+'</td>';
                                bo_inserted = true;
                            }
                        }
                        if(!bo_inserted){
                            bodyQty += '<td style="background-color: #7dfa8c; vertical-align: middle; text-align: center;">0</td>';
                        }
                        begining = begining + bo;


                        var usage = 0;
                        var inserted = false;
                        for (var k = 0; k < result.forecast_usage.length; k++) {
                            if((result.forecast_usage[k].material_number == result.material[i].material_number) && (result.forecast_usage[k].month == result.interval[j].month)){
                                usage = result.forecast_usage[k].quantity;
                                bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.forecast_usage[k].quantity+'</td>';
                                inserted = true;
                            }
                        }
                        if(!inserted){
                            bodyQty += '<td style="vertical-align: middle; text-align: center;">0</td>';
                        }
                        begining = begining - usage;


                        var add = 0;
                        var minus = 0;
                        if(begining < 0){

                            minus = begining * -1;

                            if(minus < result.material[i].minimum_order){
                                add = result.material[i].minimum_order;
                            }else{
                                round = Math.ceil(minus / result.material[i].multiple_order);
                                add = round * result.material[i].multiple_order;
                            }

                            
                            date = Math.ceil(end / (usage / result.interval[j].count));
                            if(date < 1){
                                date = 1;
                            }
                            
                            bodyQty += '<td style="background-color: rgb(0, 176, 240); font-weight: bold; vertical-align: middle; text-align: center;">'+add+'</td>';


                            var months = result.interval[j].txt.split(';');
                            bodyQty += '<td style="font-weight: bold; vertical-align: middle; text-align: center;">'+months[date-1]+'</td>';



                        }else{
                            bodyQty += '<td style="vertical-align: middle; text-align: center;"></td>';
                            bodyQty += '<td style="vertical-align: middle; text-align: center;"></td>';                                                     
                        }

                        begining = begining + add;


                        if(add > 0){
                            if(result.material[i].expired != null){

                                var arrivel_date = new Date(months[date-1]);
                                var expired_remining = result.material[i].expired - 14 -30;
                                var expired_date = arrivel_date.setDate(arrivel_date.getDate() + expired_remining);
                                
                                var out_remaining = (Math.ceil(result.material[i].safety * result.material[i].dts) + end + bo + add) / (usage / 22);
                                var start_date = new Date(result.interval[j].month + '-01');
                                var out_date = start_date.setDate(start_date.getDate() + out_remaining);


                                bodyQty += '<td style="font-weight: bold; vertical-align: middle; text-align: center;"">'+formatDate(out_date)+'</td>';
                                bodyQty += '<td style="font-weight: bold; vertical-align: middle; text-align: center;"">'+formatDate(expired_date)+'</td>';
                            }else{
                                bodyQty += '<td style="vertical-align: middle; text-align: center;"></td>';
                                bodyQty += '<td style="vertical-align: middle; text-align: center;"></td>';
                            }       
                        }else{
                            bodyQty += '<td style="vertical-align: middle; text-align: center;"></td>';
                            bodyQty += '<td style="vertical-align: middle; text-align: center;"></td>';
                        }     


                    }
                    bodyQty += '</tr>';
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
                    columnDefs: [
                    { width: '100%', targets: 1 }
                    ],
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

function formatDate(date) {
    var d = new Date(date),
    month = '' + (d.getMonth() + 1),
    day = '' + d.getDate(),
    year = d.getFullYear();

    if (month.length < 2) 
        month = '0' + month;
    if (day.length < 2) 
        day = '0' + day;

    return [year, month, day].join('-');
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