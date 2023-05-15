@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
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
    MASTER OPERATOR LIST
</h1>
</center>
<div class="row">
    <div class="col-xs-12">
       <a data-toggle="modal" data-target="#create-modal" class="btn btn-success btn-sm" style="color:white;float: right;margin-left: 20px;">
        <i class="fa fa-plus"></i>&nbsp;<b>Register</b> New Operator
    </a>
    <table id="tableOperator" class="table table-bordered table-hover" style="width: 100%;">
        <thead id="headOperator"
        style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
        <th></th>
    </thead>
    <tbody id="bodyOperator">
        <td></td>
    </tbody>
    <tfoot style="background-color: rgb(252, 248, 227);" id="footOperator">
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
                    <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Edit Operator</h1>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box-body">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" />
                            <input type="hidden" name="id" id="id">
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">NIK<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input type="text" class="form-control" id="edit_employee_id" placeholder="Edit NIK" required>
                                </div>
                            </div>

                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Name<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input type="text" class="form-control" id="edit_name" placeholder="Edit Nama" required readonly>
                                </div>
                            </div>

                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Tag<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input type="text" class="form-control" id="edit_tag" placeholder="Input Tag" required>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Location<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left" id="divEditCategory">
                                    <select class="form-control select3" data-placeholder="Select Location" name="edit_location" id="edit_location" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($loc as $loc)
                                        <option value="{{$loc->location}}">{{$loc->location}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick="updateOperator()"><i class="fa fa-edit"></i> Update</button>
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
                    <h1 style="text-align: center; margin:5px; font-weight: bold;color: white">Add New Operator</h1>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box-body">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" />
                            <input type="hidden" name="id" id="id">
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Employee ID<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left" >
                                    <select class="form-control select2" id="select_emp" name="select_emp" required data-placeholder="Select Employee" style="width: 100%" onchange="Employee(this.value)">
                                        <option value="">&nbsp;</option>
                                        @foreach($emp as $emp)
                                        <option value="{{$emp->employee_id}}/{{$emp->name}}">{{$emp->employee_id}} - {{$emp->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Tag Operator<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left">
                                    <input class="form-control" type="text" id="tag_operator" style="width: 100%" data-placeholder="Tag Operator" readonly>
                                </div>
                            </div>
                            <div class="form-group row" align="left">
                                <label class="col-sm-2 col-sm-offset-2">Location<span class="text-red">*</span></label>
                                <div class="col-sm-5" align="left" id="divEditCategory">
                                    <select class="form-control select2" data-placeholder="Select Location" name="select_location" id="select_location" style="width: 100%">
                                        <option value=""></option>
                                        @foreach($loc1 as $loc1)
                                        <option value="{{$loc1->location}}">{{$loc1->location}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick="AddOperator()"><i class="fa fa-edit"></i> Save</button>
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
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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
    });

    function fetchTable() {

        $('#loading').show();
        $.get('{{ url('fetch/material_process/operator') }}', function(result, status, xhr) {
            if (result.status) {

                    // NON INDIRECT
                $('#tableOperator').DataTable().clear();
                $('#tableOperator').DataTable().destroy();
                $('#headOperator').html("");
                var headOperator = '<tr>';
                headOperator += '<th style="vertical-align: middle; text-align: center;">NIK</th>';
                headOperator += '<th style="vertical-align: middle; text-align: center;">Nama</th>';
                headOperator += '<th style="vertical-align: middle; text-align: center;">Tag</th>';
                headOperator += '<th style="vertical-align: middle; text-align: center;">Location</th>';
                headOperator += '<th style="vertical-align: middle; text-align: center;">Aksi</th>';
                headOperator += '</tr>';
                $('#headOperator').append(headOperator);


                $('#bodyOperator').html("");
                var bodyOperator = '';
                for (var i = 0; i < result.operator.length; i++) {
                    var hexToDecimal = hex => parseInt(hex, 16);
                    var ppp = hexToDecimal(result.operator[i].tag);

                    bodyOperator += '<tr>';
                    bodyOperator += '<td style="vertical-align: middle; text-align: center;">' + result.operator[i].employee_id + '</td>';
                    bodyOperator += '<td style="vertical-align: middle; text-align: left;">' + result.operator[i].name + '</td>';
                    bodyOperator += '<td style="vertical-align: middle; text-align: center;">0'+ppp+'</td>';
                    bodyOperator += '<td style="vertical-align: middle; text-align: center;">' + result.operator[i].location + '</td>';
                    bodyOperator += '<td style="vertical-align: middle; text-align: center;">';

                    bodyOperator += '<button class="btn btn btn-warning" onclick="editOperator(\''+result.operator[i].id+'\',\''+result.operator[i].employee_id+'\',\''+result.operator[i].name+'\',\''+result.operator[i].tag+'\',\''+result.operator[i].location+'\')"><i class="fa fa-edit"></i> Edit</button>';

                    bodyOperator += '&nbsp&nbsp&nbsp&nbsp<button class="btn btn btn-danger" onclick="DeleteOperator(\''+result.operator[i].id+'\')"><i class="fa fa-edit"> Delete</i></button>';

                    bodyOperator += '</td>';
                    bodyOperator += '</tr>';
                }
                $('#bodyOperator').append(bodyOperator);

                $('#footOperator').html("");
                var footOperator = '';
                footOperator += '<tr>';
                footOperator += '<th></th>';
                footOperator += '<th></th>';
                footOperator += '<th></th>';
                footOperator += '<th></th>';
                footOperator += '<th></th>';
                footOperator += '</tr>';
                $('#footOperator').append(footOperator);

                $('#tableOperator tfoot th').each(function() {
                    var title = $(this).text();
                    $(this).html(
                        '<input class="filterOperator" style="text-align: center;" type="text" placeholder="Search ' +
                        title + '" size="3"/>');
                });

                var tableOperator = $('#tableOperator').DataTable({
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
                    "processing": true,
                    "order": [[3, 'asc']],
                });

                tableOperator.columns().every(function() {
                    var that = this;
                    $('.filterOperator', this.footer()).on('keyup change', function() {
                        if (that.search() !== this.value) {
                            that
                            .search(this.value)
                            .draw();
                        }
                    });
                });
                $('#tableOperator tfoot tr').prependTo('#tableOperator thead');
                $('#loading').hide();
            }
        });
}

function Employee(value){
    var employee_id = value.split('/');

    var data = {
        employee_id:employee_id[0]
    }
    $.get('{{ url("scan/kd_mouthpiece/operator") }}', data, function(result, status, xhr){
        if(result.status){
            $('#tag_operator').val(result.tag.tag);
        }
        else{
            openErrorGritter('Error', result.message);
        }
    });
}


function cancelAll() {
    $('#id').val('');
    $('#edit_employee_id').val('');
    $('#edit_name').val('');
    $('#edit_tag').val('');
    $('#edit_qty_cs').val('');
    $('#edit_location').val('').trigger('change');
}


function editOperator(id,employee_id,name,tag,loc) {
    cancelAll();
    var hexToDecimal = hex => parseInt(hex, 16);
    var ppp = hexToDecimal(tag);
    $('#id').val(id);
    $('#edit_employee_id').val(employee_id).attr('readonly',true);
    $('#edit_name').val(name);
    $('#edit_tag').val('0' + ppp);
    $('#edit_location').val(loc).trigger('change');
    $('#edit-modal').modal('show');
}

function AddOperator(){
    var emp = $('#select_emp').val();
    var tag = $('#tag_operator').val();
    var loc = $('#select_location').val();

    if (emp == '' || tag == '' || loc == '') {
        confirm("Isi data dengan lengkap.")
    }else{
        $('#create-modal').modal('hide');
        var data = {
            emp:emp,
            tag:tag,
            loc:loc
        }
        $.post('{{ url("create/new/operator") }}', data, function(result, status, xhr){
            if(result.status){
                openSuccessGritter('Success!', result.message);
                fetchTable();
                $('#select_emp').val('').trigger('change');
                $('#tag_operator').val('');
                $('#select_location').val('').trigger('change');
            }
            else{
                openErrorGritter('Error', result.message);
            }
        });
    }
}

function updateOperator(){
    var nik = $('#edit_employee_id').val();
    var name = $('#edit_name').val();
    var tag = $('#edit_tag').val();
    var location = $('#edit_location').val();

    var data = {
        nik:nik,
        name:name,
        tag:tag,
        location:location
    }
    $.post('{{ url("update/tag/operator") }}', data, function(result, status, xhr){
        if(result.status){
            openSuccessGritter('Success!', result.message);
            $('#edit-modal').modal('hide');
            fetchTable();
        }
        else{
            openErrorGritter('Error', result.message);
        }
    });
}

function DeleteOperator(value){
    if(confirm("Apakah anda yakin akan menghapus operator ini di list operator KPP?")){
        var data = {
            id:value
        }
        $.post('{{ url("delete/operator") }}', data, function(result, status, xhr){
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
