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
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="row">
                        <div class="col-xs-4 col-xs-offset-4">
                            <textarea id="check_data" placeholder="Copy your FLO & KDO number here ..." style="height: 100px; width: 100%;"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-xs-offset-4">
                            <button style="margin: 1%;" onclick="fetchTable('FSTK')" class="btn btn-primary pull-right">Stock FSTK</button>
                            <button style="margin: 1%;" onclick="fetchTable('Search')" class="btn btn-primary pull-right">Search</button>
                            <button style="margin: 1%;" onclick="clear()" class="btn btn-danger pull-right">Clear</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="col-xs-6">
                        <table id="tableDetail" class="table table-bordered table-hover" style="width: 100%;">
                            <thead id="headDetail" style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                <th></th>
                            </thead>
                            <tbody id="bodyDetail">
                                <td></td>
                            </tbody>
                            <tfoot id="footDetail">
                                <th></th>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-xs-6">
                        <table id="tableResume" class="table table-bordered table-hover" style="width: 100%;">
                            <thead id="headResume" style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                <th></th>
                            </thead>
                            <tbody id="bodyResume">
                                <td></td>
                            </tbody>
                            <tfoot id="footResume">
                                <th></th>
                            </tfoot>
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


    });

    function clear() {
        location.reload();
    }

    function fetchTable(category){
        var check_data = $('#check_data').val();
        var data = {
            check_data:check_data,
            category:category
        }

        if(category != 'FSTK' && check_data == ""){
            alert('Data FLO & KDO tidak boleh kosong');
            return false;
        }


        $('#loading').show();
        $.get('{{ url("fetch/stocktaking/check_flo_kdo") }}', data, function(result, status, xhr) {
            if(result.status){

                $('#check_data').val('');

                $('#tableDetail').DataTable().clear();
                $('#tableDetail').DataTable().destroy();

                $('#headDetail').html("");
                var headDetail = '<tr>';
                headDetail += '<th style="vertical-align: middle; text-align: center;">FLO & KDO</th>';
                headDetail += '<th style="vertical-align: middle; text-align: center;">Material</th>';
                headDetail += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                headDetail += '<th style="vertical-align: middle; text-align: center;">Serial No.</th>';
                headDetail += '<th style="vertical-align: middle; text-align: center;">Qty</th>';
                headDetail += '</tr>';
                $('#headDetail').append(headDetail);


                $('#bodyDetail').html("");
                var bodyDetail = '';
                for (var i = 0; i < result.flo_detail.length; i++) {
                    bodyDetail += '<tr>';
                    bodyDetail += '<td style="vertical-align: middle; text-align: center;">'+result.flo_detail[i].flo_number+'</td>';
                    bodyDetail += '<td style="vertical-align: middle; text-align: center;">'+result.flo_detail[i].material_number+'</td>';

                    var is_desc_filled = false;
                    for (var x = 0; x < result.material.length; x++) {
                        if(result.material[x].material_number == result.flo_detail[i].material_number){
                            bodyDetail += '<td style="vertical-align: middle; text-align: left;">'+result.material[x].material_description+'</td>';
                            is_desc_filled = true;
                            break;
                        }
                    }
                    if(!is_desc_filled){
                        bodyDetail += '<td style="vertical-align: middle; text-align: left;">-</td>';
                    }

                    bodyDetail += '<td style="vertical-align: middle; text-align: center;">'+result.flo_detail[i].serial_number+'</td>';
                    bodyDetail += '<td style="vertical-align: middle; text-align: right;">'+result.flo_detail[i].quantity+'</td>';
                    bodyDetail += '</tr>';

                }

                for (var i = 0; i < result.kdo_detail.length; i++) {
                    bodyDetail += '<tr>';
                    bodyDetail += '<td style="vertical-align: middle; text-align: center;">'+result.kdo_detail[i].kd_number+'</td>';
                    bodyDetail += '<td style="vertical-align: middle; text-align: center;">'+result.kdo_detail[i].material_number+'</td>';

                    var is_desc_filled = false;
                    for (var x = 0; x < result.material.length; x++) {
                        if(result.material[x].material_number == result.kdo_detail[i].material_number){
                            bodyDetail += '<td style="vertical-align: middle; text-align: left;">'+result.material[x].material_description+'</td>';
                            is_desc_filled = true;
                            break;
                        }
                    }
                    if(!is_desc_filled){
                        bodyDetail += '<td style="vertical-align: middle; text-align: left;">-</td>';
                    }

                    bodyDetail += '<td style="vertical-align: middle; text-align: center;">'+result.kdo_detail[i].serial_number+'</td>';
                    bodyDetail += '<td style="vertical-align: middle; text-align: right;">'+result.kdo_detail[i].quantity+'</td>';
                    bodyDetail += '</tr>';
                }

                $('#bodyDetail').append(bodyDetail);


                $('#footDetail').html("");
                var footDetail = '';
                footDetail += '<tr>';
                footDetail += '<th></th>';
                footDetail += '<th></th>';
                footDetail += '<th></th>';
                footDetail += '<th></th>';
                footDetail += '<th></th>';
                footDetail += '</tr>';
                $('#footDetail').append(footDetail);

                $('#tableDetail tfoot th').each(function (){
                    var title = $(this).text();
                    $(this).html('<input class="filterDetail" style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>');
                });

                var tableDetail = $('#tableDetail').DataTable({
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

                tableDetail.columns().every( function () {
                    var that = this;
                    $('.filterDetail', this.footer() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                            .search( this.value )
                            .draw();
                        }
                    });
                });
                $('#tableDetail tfoot tr').prependTo('#tableDetail thead');






                $('#tableResume').DataTable().clear();
                $('#tableResume').DataTable().destroy();

                $('#headResume').html("");
                var headResume = '<tr>';
                headResume += '<th style="vertical-align: middle; text-align: center;">Material</th>';
                headResume += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                headResume += '<th style="vertical-align: middle; text-align: center;">Qty</th>';
                headResume += '</tr>';
                $('#headResume').append(headResume);


                $('#bodyResume').html("");
                var bodyResume = '';
                for (var i = 0; i < result.flo.length; i++) {
                    bodyResume += '<tr>';
                    bodyResume += '<td style="vertical-align: middle; text-align: center;">'+result.flo[i].material_number+'</td>';

                    var is_desc_filled = false;
                    for (var x = 0; x < result.material.length; x++) {
                        if(result.material[x].material_number == result.flo[i].material_number){
                            bodyResume += '<td style="vertical-align: middle; text-align: left;">'+result.material[x].material_description+'</td>';
                            is_desc_filled = true;
                            break;
                        }
                    }
                    if(!is_desc_filled){
                        bodyResume += '<td style="vertical-align: middle; text-align: left;">-</td>';
                    }

                    bodyResume += '<td style="vertical-align: middle; text-align: right;">'+result.flo[i].quantity+'</td>';
                    bodyResume += '</tr>';

                }

                for (var i = 0; i < result.kdo.length; i++) {
                    bodyResume += '<tr>';
                    bodyResume += '<td style="vertical-align: middle; text-align: center;">'+result.kdo[i].material_number+'</td>';

                    var is_desc_filled = false;
                    for (var x = 0; x < result.material.length; x++) {
                        if(result.material[x].material_number == result.kdo[i].material_number){
                            bodyResume += '<td style="vertical-align: middle; text-align: left;">'+result.material[x].material_description+'</td>';
                            is_desc_filled = true;
                            break;
                        }
                    }
                    if(!is_desc_filled){
                        bodyResume += '<td style="vertical-align: middle; text-align: left;">-</td>';
                    }

                    bodyResume += '<td style="vertical-align: middle; text-align: right;">'+result.kdo[i].quantity+'</td>';
                    bodyResume += '</tr>';
                }

                $('#bodyResume').append(bodyResume);


                $('#footResume').html("");
                var footResume = '';
                footResume += '<tr>';
                footResume += '<th></th>';
                footResume += '<th></th>';
                footResume += '<th></th>';
                footResume += '</tr>';
                $('#footResume').append(footResume);

                $('#tableResume tfoot th').each(function (){
                    var title = $(this).text();
                    $(this).html('<input class="filterResume" style="text-align: center;" type="text" placeholder="Search '+title+'" size="3"/>');
                });

                var tableResume = $('#tableResume').DataTable({
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

                tableResume.columns().every( function () {
                    var that = this;
                    $('.filterResume', this.footer() ).on( 'keyup change', function () {
                        if ( that.search() !== this.value ) {
                            that
                            .search( this.value )
                            .draw();
                        }
                    });
                });
                $('#tableResume tfoot tr').prependTo('#tableResume thead');



                $('#loading').hide();


                openSuccessGritter('Success!', result.message);
            }else{
                $('#loading').hide();
                alert(result.message);
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