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
</section>
@endsection


@section('content')

<section class="content">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
        <p style="position: absolute; color: White; top: 45%; left: 45%;">
            <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
        </p>
    </div>
    <div class="row" style="margin-top: 0.5%;">
        <div class="col-xs-12" style="margin-top: 1%;">
            <div class="box">
                <div class="box-body">
                    <div class="col-xs-12" style="overflow-x: auto; padding: 0px;">
                        <table id="tableVendor" class="table table-bordered table-hover" style="width: 100%;">
                            <thead id="headVendor" style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                <tr>
                                    <th style="text-align: center;">Code</th>                                    
                                    <th style="text-align: center;">Vendor Name</th>                                    
                                    <th style="text-align: center;">Attention</th>                                    
                                    <th style="text-align: center;">To</th>                                    
                                    <th style="text-align: center;">Cc</th>                                    
                                </tr>
                            </thead>
                            <tbody id="bodyVendor">
                                <tr>
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
        // $('body').toggleClass("sidebar-collapse");

        fetchTable();
        
    });



    function generateNew() {
        if(confirm('Apakah anda yakin untuk men-generate ulang SMBMR ?')){

            $('#loading').show();
            $.get('{{ url("fetch/material/breakdown_smbmr") }}', function(result, status, xhr){
                if(result.status){
                    $('#table').DataTable().ajax.reload();
                    $('#loading').hide();
                    openSuccessGritter('Success', 'Generate SMBMR Success');

                }else {
                    $('#loading').hide();
                    openErrorGritter('Error', 'Error');
                }

            });
        }
    }

    function fetchTable() {
        $('#tableVendor').DataTable().destroy();

        var table = $('#tableVendor').DataTable({
            'dom': 'Bfrtip',
            'responsive': true,
            'lengthMenu': [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            "pageLength": 10,
            'buttons': {
                buttons:[
                {
                    extend: 'pageLength',
                    className: 'btn btn-default'
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
                }
                ]
            },
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': true,
            'order': [],
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "type" : "get",
                "url" : "{{ url("fetch/material/vendor") }}",
            },
            "columns": [
            { "width": "5%", "data": "vendor_code" },
            { "width": "25%", "data": "vendor_name", "className": "text-left" },
            { "width": "15%", "data": "name", "className": "text-left" },
            { "width": "27.5%", "data": "email", "className": "text-left" },
            { "width": "27.5%", "data": "cc_mail", "className": "text-left" }
            ]
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