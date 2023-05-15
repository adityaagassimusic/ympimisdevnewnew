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
                    <input type="text" class="form-control monthpicker" name="filter_start" id="filter_start" placeholder="Select Start Month">  
                </div>
            </div>
            <div class="col-xs-2" style="padding-right: 0px;">
                <div class="input-group date pull-right" style="text-align: center;">
                    <div class="input-group-addon bg-green">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control monthpicker" name="filter_end" id="filter_end" placeholder="Select End Month">  
                </div>
            </div>
            <div class="col-xs-2" style="padding-right: 0px;">
                <button onclick="fetchTable()" class="btn btn-primary">Search</button>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="nav-tabs-custom" style="margin-top: 1%;">
                <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                    <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Request Tools By Month</a></li>
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
        var start = $('#filter_start').val();
        var end = $('#filter_end').val();

        var data = {
            start : start,
            end : end
        }

        $('#loading').show();
        $.get('{{ url("fetch/tools/request") }}', data,  function(result, status, xhr){
            if(result.status){
                $('#tableQty').DataTable().clear();
                $('#tableQty').DataTable().destroy();
                $('#headQty').html("");
                var headQty = '<tr>';
                headQty += '<th style="vertical-align: middle; text-align: center;">Item Code</th>';
                headQty += '<th style="vertical-align: middle; text-align: center;">Description</th>';
                // headQty += '<th style="vertical-align: middle; text-align: center;">Usage</th>';
                // headQty += '<th style="vertical-align: middle; text-align: center;">Qty Target</th>';
                // headQty += '<th style="vertical-align: middle; text-align: center;">Qty Material</th>';
                for (var i = 0; i < result.interval.length; i++) {
                    headQty += '<th style="vertical-align: middle; text-align: center;">'+result.interval[i].text_month.substr(0, 3)+'-'+result.interval[i].text_year+'</th>';

                }
                headQty += '</tr>';
                $('#headQty').append(headQty);


                $('#bodyQty').html("");

                var bodyQty = '';
                for (var i = 0; i < result.tools.length; i++) {

                    bodyQty += '<tr>';
                    bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.tools[i].tools_item+'</td>';
                    bodyQty += '<td style="vertical-align: middle; text-align: left;">'+result.tools[i].tools_description+'</td>';

                    // bodyQty += '<td style="vertical-align: middle; text-align: left;">'+result.forecast[k].usage+'</td>';
                    // bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.forecast[k].qty_target+'</td>';
                    // bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.forecast[k].qty_material+'</td>';

                    for (var j = 0; j < result.interval.length; j++) {

                        var inserted = false;

                        for (var k = 0; k < result.forecast.length; k++) {
                            if((result.forecast[k].tools_item == result.tools[i].tools_item) && (result.forecast[k].month == result.interval[j].month)){
                                var final_harga = 0;
                                var total_harga = 0;
                                if (result.forecast[k].currency == "JPY"){
                                    final_harga = result.forecast[k].harga / 105;
                                } else if (result.forecast[k].currency == "IDR"){
                                    final_harga = result.forecast[k].harga / 14400;
                                } else{
                                    final_harga = result.forecast[k].harga;
                                }

                                total_harga = parseInt(result.forecast[k].total_need) * final_harga;

                                bodyQty += '<td style="vertical-align: middle; text-align: center;">'+Math.ceil(result.forecast[k].total_need)+' <br> <span style="color:red">($ '+total_harga.toFixed(1)+')</span></td>';

                                inserted = true;
                            }
                        }

                        if(!inserted){
                            bodyQty += '<td style="vertical-align: middle; text-align: center;">0</td>';
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
                    "columnDefs": [
                    {
                        "targets": [1],
                        "className": "text-left"
                    }
                    ],
                    "ordering": false,
                    "ordering": false,
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