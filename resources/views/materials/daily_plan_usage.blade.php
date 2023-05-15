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
    #loading, #error {
        display: none;
    }
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

    <div class="row" style="margin-top: 0.5%;">
        <div class="col-xs-12" style="padding: 0px;">
            <div class="col-xs-2" style="padding-right: 0px;">
                <div class="input-group date pull-right" style="text-align: center;">
                    <div class="input-group-addon bg-green">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control monthpicker" name="month" id="month" placeholder="Select Month">  
                </div>
            </div>
            <div class="col-xs-3" style="padding-right: 0px;">
                <select class="form-control select2" multiple="multiple" id='vendor_code' id='vendor_code' data-placeholder="Select Vendor" style="width: 100%;">
                    @foreach($vendors as $vendor)
                    <option value="{{ $vendor->vendor_code }}">{{ $vendor->vendor_code }} - {{ $vendor->vendor_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-xs-2" style="padding-right: 0px;">
                <select class="form-control select3" id='buyer' id='buyer' data-placeholder="Select Buyer" style="width: 100%;">
                    <option value=""></option>
                    @foreach($buyers as $buyer)
                    <option value="{{ $buyer->employee_id }}">{{ $buyer->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-2" style="padding-right: 0px;">
                <button onclick="fetchTable()" class="btn btn-primary">Search</button>
            </div>
        </div>
        <div class="col-xs-12" style="margin-top: 1%;">
            <div class="box">
                <div class="box-body">
                    <div class="col-xs-12" style="overflow-x: auto; padding: 0px;">
                        <h3><span id="month_pack"></span></h3>
                        <table id="tableUsage" class="table table-bordered table-hover" style="width: 100%;">
                            <thead id="headUsage" style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                <th></th>
                            </thead>
                            <tbody id="bodyUsage">
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

        $('.select2').select2();

        $('.select3').select2({
            allowClear: true
        });

        
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
        var month = $('#month').val();

        var data = {
            month : month
        }

        $.get('{{ url("fetch/material/plan_usage/daily") }}', data,  function(result, status, xhr){
            if(result.status){
                $('#tableUsage').DataTable().clear();
                $('#tableUsage').DataTable().destroy();
                $('#month_pack').text(result.month_text);
                $('#headUsage').html("");
                var headUsage = '<tr>';
                headUsage += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                headUsage += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                headUsage += '<th style="vertical-align: middle; text-align: center;">Vendor Code</th>';
                headUsage += '<th style="vertical-align: middle; text-align: center;">Vendor</th>';
                headUsage += '<th style="vertical-align: middle; text-align: center;">Uom</th>';
                headUsage += '<th style="vertical-align: middle; text-align: center;">L/I</th>';
                headUsage += '<th style="vertical-align: middle; text-align: center;">Category</th>';
                headUsage += '<th style="vertical-align: middle; text-align: center;">Buyer</th>';
                for (var i = 0; i < result.calendar.length; i++) {
                    headUsage += '<th style="vertical-align: middle; text-align: center;">'+result.calendar[i].date+'-'+result.month_text.substr(0,3)+'</th>';
                }
                headUsage += '</tr>';
                $('#headUsage').append(headUsage);


                $('#bodyUsage').html("");
                var bodyUsage = '';
                
                for (var i = 0; i < result.material.length; i++) {
                    bodyUsage += '<tr>';
                    bodyUsage += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].material_number+'</td>';
                    bodyUsage += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].material_description+'</td>';
                    bodyUsage += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].vendor_code+'</td>';
                    bodyUsage += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].vendor_name+'</td>';
                    bodyUsage += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].bun+'</td>';
                    bodyUsage += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].category+'</td>';
                    bodyUsage += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].remark+'</td>';
                    bodyUsage += '<td style="vertical-align: middle; text-align: center;">'+result.material[i].name+'</td>';

                    for (var j = 0; j < result.calendar.length; j++) {
                        var inserted = false;

                        for (var k = 0; k < result.usage.length; k++) {
                            if((result.calendar[j].week_date == result.usage[k].due_date) && (result.material[i].material_number == result.usage[k].raw_material)){
                                bodyUsage += '<td style="vertical-align: middle; text-align: center;">'+result.usage[k].usage.toFixed(4)+'</td>';
                                inserted = true;
                            }
                        }

                        if(!inserted){
                            bodyUsage += '<td style="vertical-align: middle; text-align: center;">0</td>';
                        }
                    }
                    bodyUsage += '</tr>';
                    
                }
                $('#bodyUsage').append(bodyUsage);

                $('#tableUsage').DataTable({
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



            }else {
                $('#loading').hide();
                openErrorGritter('Error', 'Error');
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