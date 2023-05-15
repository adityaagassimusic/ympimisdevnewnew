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
                    <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Production Plan</a></li>
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
                <h4 class="modal-title" id="myModalLabel">Upload GRGI Data</h4>
                Format : [Storage Location][Material Number][Quantity][Amount]
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
        
        fetchTable();
        
    });


    function fetchTable() {

        $('#loading').show();
        $.get('{{ url("fetch/material/production_plan") }}',  function(result, status, xhr){
            if(result.status){
                $('#tableQty').DataTable().clear();
                $('#tableQty').DataTable().destroy();
                $('#headQty').html("");
                var headQty = '<tr>';
                headQty += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                headQty += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                for (var i = 0; i < result.interval.length; i++) {
                    headQty += '<th style="vertical-align: middle; text-align: center;">'+result.interval[i].text_month.substr(0, 3)+'-'+result.interval[i].text_year+'</th>';
                }
                headQty += '</tr>';
                $('#headQty').append(headQty);


                $('#bodyQty').html("");
                
                var bodyQty = '';
                for (var i = 0; i < result.material.length; i++) {
                    bodyQty += '<tr>';
                    bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].material_number+'</td>';
                    bodyQty += '<td style="vertical-align: middle; text-align: left;">'+result.material[i].material_description+'</td>';
                    
                    for (var j = 0; j < result.interval.length; j++) {
                        var inserted = false;

                        for (var k = 0; k < result.plan.length; k++) {
                            if((result.plan[k].material_number == result.material[i].material_number) && (result.plan[k].month == result.interval[j].month)){
                                bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.plan[k].quantity.toFixed(2)+'</td>';
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