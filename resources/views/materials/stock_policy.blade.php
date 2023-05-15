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
    
    .modal-dialog{
        overflow-y: initial !important
    }

    .modal-body{
        max-height: 80vh;
        overflow-y: auto;
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
            <a data-toggle="modal" data-target="#stockPolicyModal" class="btn btn-success btn-sm" style="color:white">
                &nbsp;<i class="fa fa-plus-square-o"></i>&nbsp;Upload Stock Policy
            </a>
        </li>
    </ol>
</section>
@endsection
@section('content')
<section class="content" style="font-size: 0.8vw;">        
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
                    <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Quantity</a></li>
                    <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Day</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                        <h3 class="monthTable" style="margin-top: 0px; text-align: right;"></h3>
                        <table id="tableQty" class="table table-bordered table-hover" style="width: 100%;">
                            <thead id="headQty" style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                <th></th>
                            </thead>
                            <tbody id="bodyQty">
                                <td></td>
                            </tbody>
                            <tfoot id="footQty">
                                <th></th>
                            </tfoot>
                        </table>
                    </div>
                    <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
                        <h3 class="monthTable" style="margin-top: 0px; text-align: right;"></h3>
                        <table id="tableDay" class="table table-bordered table-hover" style="width: 100%;">
                            <thead id="headDay" style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                <th></th>
                            </thead>
                            <tbody id="bodyDay">
                                <td></td>
                            </tbody>
                            <tfoot id="footDay">
                                <th></th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<div class="modal fade" id="stockPolicyModal">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Upload Stock Policy</h4>
                <span>
                    Format Upload:<br>
                    [<b><i>GMC</i></b>]
                    [<b><i>DESCRIPTION</i></b>]
                    [<b><i>POLICY DAY</i></b>]
                    [<b><i>POLICY QTY</i></b>]
                </span>
            </div>
            <div class="modal-body" style="min-height: 100px">
                <div class="input-group date">
                    <div class="input-group-addon bg-purple" style="border: none;">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control monthpicker" id="policyPeriod" placeholder="Select Month">
                </div>
                <div class="form-group">
                    <textarea id="upload" style="height: 100px; width: 100%;"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success pull-right" onclick="uploadData('policy');">Upload</button>
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

    function uploadData(id){
        $('#loading').show();
        var upload = $('#upload').val();
        var period = $('#policyPeriod').val();

        var data = {
            id:id,
            upload:upload,
            period:period
        }

        if(upload == ""){
            alert('Data upload tidak boleh kosong');
            return false;
        }

        $.post('{{ url("upload/material/material_monitoring") }}', data, function(result, status, xhr) {
            if(result.status){

                $('#upload').val('');
                $('#policyPeriod').val('');
                $('#stockPolicyModal').modal('hide');

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
            }else{
                $('#loading').hide();
                alert(result.message);
            }
        });
    }


    function fetchTable() {

        var month = $('#month').val();

        var data = {
            month:month
        }

        $('#loading').show();
        $.get('{{ url("fetch/raw_material/stock_policy") }}', data, function(result, status, xhr){
            if(result.status){
                $('.monthTable').html(result.month);

                // Quantity
                $('#tableQty').DataTable().clear();
                $('#tableQty').DataTable().destroy();
                $('#headQty').html("");
                var headQty = '<tr>';
                headQty += '<th style="vertical-align: middle; text-align: center;">Group</th>';
                headQty += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                headQty += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                headQty += '<th style="vertical-align: middle; text-align: center;">Bun</th>';
                headQty += '<th style="vertical-align: middle; text-align: center;">PGr</th>';
                headQty += '<th style="vertical-align: middle; text-align: center;">Vendor</th>';
                for (var i = 0; i < result.interval.length; i++) {
                    headQty += '<th style="vertical-align: middle; text-align: center;">'+result.interval[i].text_month.substr(0, 3)+'-'+result.interval[i].text_year+'</th>';
                }
                headQty += '</tr>';
                $('#headQty').append(headQty);


                $('#bodyQty').html("");
                var bodyQty = '';
                for (var i = 0; i < result.material.length; i++) {
                    bodyQty += '<tr>';
                    bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].controlling_group+'</td>';
                    bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].material_number+'</td>';
                    bodyQty += '<td style="vertical-align: middle; text-align: left;">'+result.material[i].material_description+'</td>';

                    var is_desc_filled = false;
                    for (var x = 0; x < result.mpdl.length; x++) {
                        if(result.mpdl[x].material_number == result.material[i].material_number){
                            bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.mpdl[x].bun+'</td>';
                            is_desc_filled = true;
                            break;
                        }
                    }
                    if(!is_desc_filled){
                        bodyQty += '<td style="vertical-align: middle; text-align: center;">-</td>';
                    }
                    
                    bodyQty += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].purchasing_group+'</td>';
                    bodyQty += '<td style="vertical-align: middle; text-align: left;">'+result.material[i].vendor_code+' - '+ result.material[i].vendor_name +'</td>';
                    
                    var month_order = 1;
                    for (var j = 0; j < result.interval.length; j++) {
                        var inserted = false;
                        var bg_color = ''; 
                        if( (month_order % 2) == 0){
                            bg_color = 'background-color: rgb(252, 248, 227);'; 
                        }

                        for (var k = 0; k < result.policies.length; k++) {
                            if((result.policies[k].material_number == result.material[i].material_number) && (result.policies[k].month == result.interval[j].month)){
                                bodyQty += '<td style="'+bg_color+'vertical-align: middle; text-align: right;">'+result.policies[k].policy.toFixed(3).toLocaleString()+'</td>';
                                inserted = true;
                                month_order++;
                            }
                        }

                        if(!inserted){
                            bodyQty += '<td style="'+bg_color+'vertical-align: middle; text-align: right;">-</td>';
                            month_order++;
                        }                        
                    }
                    bodyQty += '</tr>';
                }
                $('#bodyQty').append(bodyQty);

                $('#footQty').html("");
                var footQty = '';
                footQty += '<tr>';
                footQty += '<th></th>';
                footQty += '<th></th>';
                footQty += '<th></th>';
                footQty += '<th></th>';
                footQty += '<th></th>';
                footQty += '<th></th>';
                footQty += '<th></th>';
                footQty += '<th></th>';
                footQty += '<th></th>';
                footQty += '</tr>';
                $('#footQty').append(footQty);

                $('#tableQty tfoot th').each(function (){
                    var title = $(this).text();
                    $(this).html('<input class="filterQty" style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>');
                });

                var tableQty = $('#tableQty').DataTable({
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

                tableQty.columns().every( function () {
                    var that = this;
                    $('.filterQty', this.footer() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                            .search( this.value )
                            .draw();
                        }
                    });
                });
                $('#tableQty tfoot tr').prependTo('#tableQty thead');




                // Day
                $('#tableDay').DataTable().clear();
                $('#tableDay').DataTable().destroy();
                $('#headDay').html("");
                var headDay = '<tr>';
                headDay += '<th style="vertical-align: middle; text-align: center;">Group</th>';
                headDay += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                headDay += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                headDay += '<th style="vertical-align: middle; text-align: center;">PGr</th>';
                headDay += '<th style="vertical-align: middle; text-align: center;">Vendor</th>';
                for (var i = 0; i < result.interval.length; i++) {
                    headDay += '<th style="vertical-align: middle; text-align: center;">'+result.interval[i].text_month.substr(0, 3)+'-'+result.interval[i].text_year+'</th>';
                }
                headDay += '</tr>';
                $('#headDay').append(headDay);


                $('#bodyDay').html("");
                
                var bodyDay = '';
                for (var i = 0; i < result.material.length; i++) {
                    bodyDay += '<tr>';
                    bodyDay += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].controlling_group+'</td>';
                    bodyDay += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].material_number+'</td>';
                    bodyDay += '<td style="vertical-align: middle; text-align: left;">'+result.material[i].material_description+'</td>';
                    bodyDay += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].purchasing_group+'</td>';
                    bodyDay += '<td style="vertical-align: middle; text-align: left;">'+result.material[i].vendor_code+' - '+ result.material[i].vendor_name +'</td>';
                    
                    var month_order = 1;
                    for (var j = 0; j < result.interval.length; j++) {
                        var inserted = false;
                        var bg_color = ''; 
                        if( (month_order % 2) == 0){
                            bg_color = 'background-color: rgb(252, 248, 227);'; 
                        }

                        for (var k = 0; k < result.policies.length; k++) {
                            if((result.policies[k].material_number == result.material[i].material_number) && (result.policies[k].month == result.interval[j].month)){
                                bodyDay += '<td style="'+bg_color+'vertical-align: middle; text-align: right;">'+result.policies[k].day+'</td>';
                                inserted = true;
                                month_order++;
                            }
                        }

                        if(!inserted){
                            bodyDay += '<td style="'+bg_color+'vertical-align: middle; text-align: right;">-</td>';
                            month_order++;
                        }                        
                    }
                    bodyDay += '</tr>';
                }
                $('#bodyDay').append(bodyDay);


                $('#footDay').html("");
                var footDay = '';
                footDay += '<tr>';
                footDay += '<th></th>';
                footDay += '<th></th>';
                footDay += '<th></th>';
                footDay += '<th></th>';
                footDay += '<th></th>';
                footDay += '<th></th>';
                footDay += '<th></th>';
                footDay += '<th></th>';
                footDay += '</tr>';
                $('#footDay').append(footDay);

                $('#tableDay tfoot th').each(function (){
                    var title = $(this).text();
                    $(this).html('<input class="filterDay" style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>');
                });

                var tableDay = $('#tableDay').DataTable({
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

                tableDay.columns().every( function () {
                    var that = this;
                    $('.filterDay', this.footer() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                            .search( this.value )
                            .draw();
                        }
                    });
                });
                $('#tableDay tfoot tr').prependTo('#tableDay thead');

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