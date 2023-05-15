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
    MASTER KANBAN LIST
</h1>
</center>
<div class="row">
    <div class="col-xs-12">
        <a data-toggle="modal" data-target="#create-modal" class="btn btn-success btn-sm" style="color:white;float: right;margin-left: 20px;">
            <i class="fa fa-plus"></i>&nbsp;<b>Register</b> New Kanban
        </a>
        <table id="tableKanban" class="table table-bordered table-hover" style="width: 100%;">
            <thead id="headKanban"
            style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
            <th></th>
        </thead>
        <tbody id="bodyKanban">
            <td></td>
        </tbody>
        <tfoot style="background-color: rgb(252, 248, 227);" id="footKanban">
            <th></th>
        </tfoot>
    </table>
</div>
</div>
</section>


<div class="modal modal-default fade" id="create-modal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-xs-12" style="background-color: #00a65a; padding-right: 1%;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                    <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add New Kanban</h1>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box-body">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" />
                            <input type="hidden" name="id" id="id">
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Material<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left" >
                                    <select class="form-control select2" data-placeholder="Select Material" name="edit_material" id="add_material" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($materials as $mat)
                                        <option value="{{$mat->material_number}}_{{$mat->material_description}}">{{$mat->material_number}} - {{$mat->material_description}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Tag<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input type="text" class="form-control" id="add_tag" placeholder="Edit Tag" required>
                                </div>
                            </div>

                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Kanban No<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <!-- <input type="number" class="form-control numpad" id="add_kanban_no" placeholder="Input Quantity" required> -->
                                    <input type="text" class="form-control numpad" id="add_kanban_no" style="width: 100%; font-size: 20px; text-align: left; color: red" placeholder="0" required>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">HPL<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <select class="form-control select2" data-placeholder="Select HPL" name="add_hpl" id="add_hpl" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($hpl as $pl)
                                        <option value="{{$pl->hpl}}">{{$pl->hpl}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Warna<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left" >
                                    <select class="form-control select2" data-placeholder="Select Color" name="add_remark" id="add_remark" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($remark as $rem)
                                        <option value="{{$rem->remark}}">{{$rem->remark}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-header">
                <div class="col-xs-12" style="background-color: #ff6347; padding-right: 1%;">
                    <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Flow Kanban</h1>
                </div>
            </div> -->
            <!-- <div class="row">
                <div class="col-xs-12">
                    <div class="col-xs-12">
                        <div class="box-body">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" />
                            <input type="hidden" name="id" id="id">
                            <div class="form-group row" align="left">
                                <button class="btn btn-success" onclick="tambah_flow()"><i class="fa fa-plus"></i>&nbsp; Add Flow Kanban</button>
                                <table class="table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th style="width: 40%">Urutan</th>
                                            <th style="width: 40%">Flow Kanban</th>
                                            <th style="width: 20%">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body_penerima">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="modal-footer">
                <button class="btn btn-success" onclick="createKanban()"><i class="fa fa-edit"></i> Save</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-times-circle"></i> Close</button>
            </div>
        </div>
    </div>
</div>

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
                    <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Kanban</h1>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box-body">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" />
                            <input type="hidden" name="id_edit" id="id_edit">
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Material<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left" id="divEditMaterial">
                                    <select class="form-control select3" data-placeholder="Select Material" name="edit_material" id="edit_material" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($materials as $materials)
                                        <option value="{{$materials->material_number}}">{{$materials->material_number}} - {{$materials->material_description}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Tag<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input type="text" class="form-control" id="edit_tag" placeholder="Edit Tag" required>
                                </div>
                            </div>

                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Kanban No<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <!-- <input type="number" class="form-control numpad" id="edit_kanban_no" placeholder="Input Quantity" required> -->
                                    <input type="text" class="form-control numpad" id="edit_kanban_no" style="width: 100%; font-size: 20px; text-align: left; color: red" placeholder="0" required>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">HPL<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <select class="form-control select3" data-placeholder="Select HPL" name="edit_hpl" id="edit_hpl" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($hpl as $hpl)
                                        <option value="{{$hpl->hpl}}">{{$hpl->hpl}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Warna<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left" >
                                    <select class="form-control select3" data-placeholder="Select Color" name="edit_remark" id="edit_remark" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($remark as $remark)
                                        <option value="{{$remark->remark}}">{{$remark->remark}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick="updateKanban()"><i class="fa fa-edit"></i> Update</button>
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

    var no_penerima = 1;
    var flow = <?php echo json_encode($flow); ?>;

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
        $.get('{{ url('fetch/material_process/kanban') }}', function(result, status, xhr) {
            if (result.status) {

                    // NON INDIRECT
                $('#tableKanban').DataTable().clear();
                $('#tableKanban').DataTable().destroy();
                $('#headKanban').html("");
                var headKanban = '<tr>';
                headKanban += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                headKanban += '<th style="vertical-align: middle; text-align: center;">Material Description</th>';
                headKanban += '<th style="vertical-align: middle; text-align: center;">Tag</th>';
                headKanban += '<th style="vertical-align: middle; text-align: center;">No Kanban</th>';
                headKanban += '<th style="vertical-align: middle; text-align: center;">HPL</th>';
                headKanban += '<th style="vertical-align: middle; text-align: center;">Warna</th>';
                headKanban += '<th style="vertical-align: middle; text-align: center;">Aksi</th>';
                headKanban += '</tr>';
                $('#headKanban').append(headKanban);


                $('#bodyKanban').html("");
                var bodyKanban = '';
                for (var i = 0; i < result.kanban.length; i++) {
                    bodyKanban += '<tr>';
                    bodyKanban += '<td style="vertical-align: middle; text-align: center;">' + result
                    .kanban[i].material_number + '</td>';
                    bodyKanban += '<td style="vertical-align: middle; text-align: left;">' + result
                    .kanban[i].material_description + '</td>';
                    bodyKanban += '<td style="vertical-align: middle; text-align: center;">' + result
                    .kanban[i].tag + '</td>';
                    bodyKanban += '<td style="vertical-align: middle; text-align: center;">' + result
                    .kanban[i].no_kanban + '</td>';
                    bodyKanban += '<td style="vertical-align: middle; text-align: center;">' + (result
                        .kanban[i].hpl || '') + '</td>';
                    bodyKanban += '<td style="vertical-align: middle; text-align: center;">' + (result
                        .kanban[i].remark || '') + '</td>';
                    bodyKanban += '<td style="vertical-align: middle; text-align: center;">';

                    bodyKanban += '<button class="btn btn btn-warning" onclick="editMaterial(\''+result.kanban[i].id+'\',\''+result.kanban[i].material_number+'\',\''+result.kanban[i].material_description+'\',\''+result.kanban[i].tag+'\',\''+result.kanban[i].no_kanban+'\',\''+result.kanban[i].hpl+'\',\''+result.kanban[i].remark+'\')"><i class="fa fa-edit"></i> Edit</button>';

                    bodyKanban += '</td>';
                    bodyKanban += '</tr>';
                }
                $('#bodyKanban').append(bodyKanban);

                $('#footKanban').html("");
                var footKanban = '';
                footKanban += '<tr>';
                footKanban += '<th></th>';
                footKanban += '<th></th>';
                footKanban += '<th></th>';
                footKanban += '<th></th>';
                footKanban += '<th></th>';
                footKanban += '<th></th>';
                footKanban += '<th></th>';
                footKanban += '</tr>';
                $('#footKanban').append(footKanban);

                $('#tableKanban tfoot th').each(function() {
                    var title = $(this).text();
                    $(this).html(
                        '<input class="filterMaterial" style="text-align: center;" type="text" placeholder="Search ' +
                        title + '" size="3"/>');
                });

                var tableKanban = $('#tableKanban').DataTable({
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

                tableKanban.columns().every(function() {
                    var that = this;
                    $('.filterMaterial', this.footer()).on('keyup change', function() {
                        if (that.search() !== this.value) {
                            that
                            .search(this.value)
                            .draw();
                        }
                    });
                });
                $('#tableKanban tfoot tr').prependTo('#tableKanban thead');


                $('#loading').hide();

            }

        });

}


function cancelAll() {
    $('#id').val('');
    $('#edit_material').val('');
    $('#edit_kanban_no').val('');
    $('#edit_tag').val('');
    $('#edit_hpl').val('').trigger('change');
    $('#edit_remark').val('').trigger('change');
}


function editMaterial(id,material,material_description,tag,kanban_no,hpl,remark) {
    cancelAll();
    $('#id_edit').val(id);
    $('#edit_material').val(material).attr('readonly',true).trigger('change');
    $('#edit_tag').val(tag);
    $('#edit_kanban_no').val(kanban_no);
    $('#edit_hpl').val(hpl).trigger('change');
    $('#edit_remark').val(remark).trigger('change');
    $('#edit-modal').modal('show');
}

function tambah_flow() {
    var body = "";

    var option_dept = "";

    $.each(flow, function(index, value) { 
        option_dept += "<option value='"+value.name_flow+"'>"+value.name_flow+"</option>" ;
    })

    body += '<tr id="tr_'+no_penerima+'">';
    body += '<td style="padding-right: 10px"><input class="form-control urutan" type="text" value="'+no_penerima+'" style="width: 100%; font-size: 20px; text-align: center; color: red" readonly></td>';
    body += '<td style="padding-left: 10px"><select class="form-control select5 flow" id="sec_'+no_penerima+'" data-placeholder="Pilih Flow"><option value=""></option>'+option_dept+'</select></td>';
    body += '<td style="padding-left: 20px"><button class="btn btn-danger btn-sm" onclick="deleteMat(this)"><i class="fa fa-minus"></i></button></td>';
    body += '</tr>';

    $("#body_penerima").append(body);

    $('.select5').select2({
        dropdownAutoWidth : true,
        allowClear: true,
        dropdownParent: $('#tr_'+no_penerima),
    });

    var rowIndex = $("#tr_"+no_penerima+' td').first().parent().parent().children().index($("#tr_"+no_penerima+' td').first().parent());
    $("#tr_"+no_penerima+' td').first().html('<input class="form-control urutan" type="text" value="'+(rowIndex + 1)+'" style="width: 100%; font-size: 20px; text-align: center; color: red" readonly>');

    no_penerima++;
}

function createKanban(){
    // var urutan_flow = [];
    // $.each($('.flow'),function(i, obj) {
    //     var select_urutan = $('.urutan').eq(i).val();
    //     var select_flow = $(obj).val();
    //     urutan_flow.push(select_urutan+'_'+select_flow);
    // });

    var add_material = $("#add_material").val();
    var add_tag = $("#add_tag").val();
    var add_kanban_no = $("#add_kanban_no").val();
    var add_hpl = $("#add_hpl").val();
    var add_remark = $("#add_remark").val();

    if (add_material == '' || add_tag == '' || add_kanban_no == '' || add_hpl == '' || add_remark == '') {
        confirm('Isi data dengan lengkap?')
    }else{
        if (confirm('Apakah anda yakin akan menyimpan data kanban baru?')) {
            var data = {
                add_material:add_material,
                add_tag:add_tag,
                add_kanban_no:add_kanban_no,
                add_hpl:add_hpl,
                add_remark:add_remark,
                // urutan_flow:urutan_flow
            }
            $.post('{{ url("create/new/kanban") }}', data, function(result, status, xhr){
                if(result.status){
                    openSuccessGritter('Success!', result.message);
                    location.reload();
                    $("#add_material").val('').trigger('change');
                    $("#add_tag").val('');
                    $("#add_kanban_no").val('');
                    $("#add_hpl").val('').trigger('change');
                    $("#add_remark").val('').trigger('change');
                }
                else{
                    openErrorGritter('Error', result.message);
                }
            });
        }else{
            return false;
        }
    }
}

function deleteMat(elem) {
    $(elem).closest('tr').remove();
    $("#body_penerima").children().each(function() {
        var rowIndex = $(this).parent().children().index($(this));
        $(this).children('td').first().html('<input class="form-control urutan" type="text" value="'+(rowIndex + 1)+'" style="width: 100%; font-size: 20px; text-align: center; color: red" readonly>');
    })
}

function updateKanban(){
    var id_edit = $("#id_edit").val();
    var edit_material = $("#edit_material").val();
    var edit_tag = $("#edit_tag").val();
    var edit_kanban_no = $("#edit_kanban_no").val();
    var edit_hpl = $("#edit_hpl").val();
    var edit_remark = $("#edit_remark").val();

    if (confirm('Apakah anda yakin ingin menyimpan perubahan?')) {
        var data = {
            id_edit:id_edit,
            edit_material:edit_material,
            edit_tag:edit_tag,
            edit_kanban_no:edit_kanban_no,
            edit_hpl: edit_hpl,
            edit_remark:edit_remark
        }

        console.log(data);
        $.post('{{ url("update/kanban") }}', data, function(result, status, xhr){
            if(result.status){
                openSuccessGritter('Success!', result.message);
                location.reload();
            }
            else{
                openErrorGritter('Error', result.message);
            }
        });
    }else{
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
