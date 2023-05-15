@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }

    thead>tr>th {
        text-align: center;
    }

    tbody>tr>td {
        text-align: center;
    }

    tfoot>tr>th {
        text-align: center;
    }

    td:hover {
        overflow: visible;
    }

    table.table-bordered {
        border: 1px solid black;
    }

    table.table-bordered>thead>tr>th {
        border: 1px solid black;
        vertical-align: middle;
    }

    table.table-bordered>tbody>tr>td {
        border: 1px solid rgb(211, 211, 211);
        padding-top: 0;
        padding-bottom: 0;
        vertical-align: middle;
    }

    table.table-bordered>tfoot>tr>th {
        border: 1px solid rgb(211, 211, 211);
        vertical-align: middle;
    }

    .modal-dialog {
        overflow-y: initial !important
    }

    .modal-body {
        max-height: 80vh;
        overflow-y: auto;
    }

    #loading,
    #error {
        display: none;
    }
</style>
@endsection

@section('header')
@endsection
@section('content')
<section class="content" >
    <div id="loading"
    style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 45%;">
        <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
    </p>
</div>

<center>
    <h1
    style="background-color: #a1887f; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 2%; color: white; border: 1px solid darkgrey; border-radius: 5px;">
    MASTER MATERIAL LIST
</h1>
</center>
<div class="row">
    <div class="col-xs-12">
        <a data-toggle="modal" data-target="#create-modal" class="btn btn-success btn-sm" style="color:white;float: right;margin-left: 20px;">
            <i class="fa fa-plus"></i>&nbsp;<b>Register</b> New Material
        </a>
        <table id="tableMaterial" class="table table-bordered table-hover" style="width: 100%;">
            <thead id="headMaterial"
            style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
            <th></th>
        </thead>
        <tbody id="bodyMaterial">
            <td></td>
        </tbody>
        <tfoot style="background-color: rgb(252, 248, 227);" id="footMaterial">
            <th></th>
        </tfoot>
    </table>
</div>
</div>
</section>


<div class="modal modal-default fade" id="edit-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                    <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Material</h1>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box-body">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" />
                            <input type="hidden" name="id" id="id">
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">GMC<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input type="text" class="form-control" id="edit_gmc" placeholder="Edit Description" required>
                                </div>
                            </div>

                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Description<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input type="text" class="form-control" id="edit_description" placeholder="Edit Description" required>
                                </div>
                            </div>

                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Quantity (Process)<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input type="number" class="form-control numpad" id="edit_qty_process" placeholder="Input Quantity" required>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Quantity (CS)<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input type="number" class="form-control numpad" id="edit_qty_cs" placeholder="Input Quantity" required>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">HPL<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left" id="divEditCategory">
                                    <select class="form-control select3" data-placeholder="Select HPL" name="edit_hpl" id="edit_hpl" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($hpl as $hpl)
                                        <option value="{{$hpl->hpl}}">{{$hpl->hpl}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick="update()"><i class="fa fa-edit"></i> Update</button>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-default fade" id="create-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                    <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add New Material</h1>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box-body">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" />
                            <input type="hidden" name="id" id="id">
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">GMC<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left" >
                                    <select class="form-control select2" id="select_gmc" name="select_gmc" required data-placeholder="Select Material" style="width: 100%" onchange="SelectGMC(this.value)">
                                        <option value="">&nbsp;</option>
                                        @foreach($material as $material)
                                        <option value="{{$material->material_number}}/{{$material->material_description}}">{{$material->material_number}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="gmc">
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Description<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input type="text" class="form-control" id="desc" data-placeholder="Material Description" required readonly>
                                </div>
                            </div>

                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Quantity (Process)<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input class="form-control numpad" type="text" id="qty_process" style="width: 100%; font-size: 20px; color: red" value="0">
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Quantity (CS)<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input class="form-control numpad" type="text" id="qty_cs" style="width: 100%; font-size: 20px; color: red" value="0">
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">HPL<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left" id="divEditCategory">
                                    <select class="form-control select2" data-placeholder="Select HPL" name="select_hpl" id="select_hpl" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($hpl2 as $hpl)
                                        <option value="{{$hpl->hpl}}">{{$hpl->hpl}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick="AddMaterial()"><i class="fa fa-edit"></i> Save</button>
            </div>
        </div>
    </div>
</div>


@stop

@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('js/buttons.flash.min.js') }}"></script>
<script src="{{ url('js/jszip.min.js') }}"></script>
<script src="{{ url('js/vfs_fonts.js') }}"></script>
<script src="{{ url('js/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/buttons.print.min.js') }}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
    $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
    $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
    $.fn.numpad.defaults.buttonNumberTpl =  '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
    $.fn.numpad.defaults.buttonFunctionTpl = '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
    $.fn.numpad.defaults.onKeypadCreate = function(){$(this).find('.done').addClass('btn-primary');};

    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");

        fetchTable();

        $('.select2').select2({
            dropdownParent: $('#create-modal'),
            allowClear : true,
        });

        $('.select3').select2({
            dropdownParent: $('#edit-modal'),
            allowClear : true,
        });

        $('.numpad').numpad({
            hidePlusMinusButton : true,
            decimalSeparator : '.'
        });
    });

    function fetchTable() {

        $('#loading').show();
        $.get('{{ url('fetch/material_process/material') }}', function(result, status, xhr) {
            if (result.status) {

                    // NON INDIRECT
                $('#tableMaterial').DataTable().clear();
                $('#tableMaterial').DataTable().destroy();
                $('#headMaterial').html("");
                var headMaterial = '<tr>';
                headMaterial += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                headMaterial += '<th style="vertical-align: middle; text-align: center;">Material Description</th>';
                headMaterial += '<th style="vertical-align: middle; text-align: center;">Qty (Process)</th>';
                headMaterial += '<th style="vertical-align: middle; text-align: center;">Qty (CS)</th>';
                headMaterial += '<th style="vertical-align: middle; text-align: center;">HPL</th>';
                headMaterial += '<th style="vertical-align: middle; text-align: center;">Aksi</th>';
                headMaterial += '</tr>';
                $('#headMaterial').append(headMaterial);


                $('#bodyMaterial').html("");
                var bodyMaterial = '';
                for (var i = 0; i < result.material.length; i++) {
                    bodyMaterial += '<tr>';
                    bodyMaterial += '<td style="vertical-align: middle; text-align: center;">' + result
                    .material[i].material_number + '</td>';
                    bodyMaterial += '<td style="vertical-align: middle; text-align: left;">' + result
                    .material[i].material_description + '</td>';
                    bodyMaterial += '<td style="vertical-align: middle; text-align: center;">' + result
                    .material[i].qty_process + '</td>';
                    bodyMaterial += '<td style="vertical-align: middle; text-align: center;">' + result
                    .material[i].qty_cs + '</td>';
                    bodyMaterial += '<td style="vertical-align: middle; text-align: center;">' + (result
                        .material[i].hpl || '') + '</td>';
                    bodyMaterial += '<td style="vertical-align: middle; text-align: center;">';

                    bodyMaterial += '<button class="btn btn btn-warning" onclick="editMaterial(\''+result.material[i].id+'\',\''+result.material[i].material_number+'\',\''+result.material[i].material_description+'\',\''+result.material[i].qty_process+'\',\''+result.material[i].qty_cs+'\',\''+result.material[i].hpl+'\')"><i class="fa fa-edit"></i> Edit</button>';

                    bodyMaterial += '&nbsp&nbsp&nbsp&nbsp<button class="btn btn btn-danger" onclick="DeleteMaterial(\''+result.material[i].id+'\')"><i class="fa fa-edit"> Delete</i></button>';

                    bodyMaterial += '</td>';
                    bodyMaterial += '</tr>';
                }
                $('#bodyMaterial').append(bodyMaterial);

                $('#footMaterial').html("");
                var footMaterial = '';
                footMaterial += '<tr>';
                footMaterial += '<th></th>';
                footMaterial += '<th></th>';
                footMaterial += '<th></th>';
                footMaterial += '<th></th>';
                footMaterial += '<th></th>';
                footMaterial += '<th></th>';
                footMaterial += '</tr>';
                $('#footMaterial').append(footMaterial);

                $('#tableMaterial tfoot th').each(function() {
                    var title = $(this).text();
                    $(this).html(
                        '<input class="filterMaterial" style="text-align: center;" type="text" placeholder="Search ' +
                        title + '" size="3"/>');
                });

                var tableMaterial = $('#tableMaterial').DataTable({
                    'dom': 'Bfrtip',
                    'responsive': true,
                    'lengthMenu': [
                        [-1],
                        ['Show all']
                        ],
                    'buttons': {
                        buttons: [{
                            extend: 'pageLength',
                            className: 'btn btn-default',
                        }, {
                            extend: 'copy',
                            className: 'btn btn-success',
                            text: '<i class="fa fa-copy"></i> Copy',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        }, {
                            extend: 'excel',
                            className: 'btn btn-info',
                            text: '<i class="fa fa-file-excel-o"></i> Excel',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        }, {
                            extend: 'print',
                            className: 'btn btn-warning',
                            text: '<i class="fa fa-print"></i> Print',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        }]
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

                tableMaterial.columns().every(function() {
                    var that = this;
                    $('.filterMaterial', this.footer()).on('keyup change', function() {
                        if (that.search() !== this.value) {
                            that
                            .search(this.value)
                            .draw();
                        }
                    });
                });
                $('#tableMaterial tfoot tr').prependTo('#tableMaterial thead');


                $('#loading').hide();

            }

        });

}


function cancelAll() {
    $('#id').val('');
    $('#edit_gmc').val('');
    $('#edit_description').val('');
    $('#edit_qty_process').val('');
    $('#edit_qty_cs').val('');
    $('#edit_hpl').val('').trigger('change');
}


function editMaterial(id,material,material_description,qty_process,qty_cs,hpl) {
    cancelAll();
    $('#id').val(id);
    $('#edit_gmc').val(material).attr('readonly',true);
    $('#edit_description').val(material_description);
    $('#edit_qty_process').val(qty_process);
    $('#edit_qty_cs').val(qty_cs);
    $('#edit_hpl').val(hpl).trigger('change');
    $('#edit-modal').modal('show');
}

function SelectGMC(value){
    var material = value.split("/");
    $('#gmc').val(material[0]);
    $('#desc').val(material[1]);
}

function AddMaterial(){
    var gmc = $('#gmc').val();
    var desc = $('#desc').val();
    var qty_process = $('#qty_process').val();
    var qty_cs = $('#qty_cs').val();
    var select_hpl = $('#select_hpl').val();

    if (gmc == '' || desc == '' || qty_process == 0 || qty_process == '' || qty_cs == 0 || qty_cs == '' || select_hpl == '') {
        confirm("Isi data dengan lengkap.")
    }else{
        $('#create-modal').modal('hide');
        var data = {
            gmc:gmc,
            desc:desc,
            qty_process:qty_process,
            qty_cs:qty_cs,
            select_hpl:select_hpl
        }
        $.post('{{ url("create/new/material") }}', data, function(result, status, xhr){
            if(result.status){
                openSuccessGritter('Success!', result.message);
                $('#gmc').val('');
                $('#desc').val('');
                $('#qty_process').val(0);
                $('#qty_cs').val(0);
                $('#select_hpl').val('');
                fetchTable();
            }
            else{
                openErrorGritter('Error', result.message);
                $('#gmc').val('').trigger('change');
                $('#desc').val('');
                $('#qty_process').val(0);
                $('#qty_cs').val(0);
                $('#select_hpl').val('').trigger('change');
            }
        });
    }   
}

function DeleteMaterial(value){
    if(confirm("Apakah anda yakin akan menghapus material ini di list material KPP?")){
        var data = {
            id:value
        }
        $.post('{{ url("delete/material/material") }}', data, function(result, status, xhr){
            if(result.status){
                openSuccessGritter('Success!', result.message);
                fetchTable();
            }
            else{
                openErrorGritter('Error', result.message);
            }
        });
    }
    else{
        return false;
    }
}

function openSuccessGritter(title, message) {
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
