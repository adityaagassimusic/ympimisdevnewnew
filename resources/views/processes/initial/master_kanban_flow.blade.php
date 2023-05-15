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
                MASTER KANBAN FLOW
            </h1>
        </center>
        <div class="row">
            <div class="col-xs-12">
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


    <div class="modal fade" id="modalEditShow" style="z-index: 10000;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="nav-tabs-custom tab-danger" align="center">
                        <h2 id="judul_kategori"></h2>
                    </div>
                    <div class="col-md-12" style="padding-top: 10px">
                        <table id="TableDetail" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #BDD5EA; color: black;">
                                <tr>
                                    <th width="1%">No</th>
                                    <th width="5%">Flow Proses</th>
                                    <th width="2%">#</th>
                                </tr>
                            </thead>
                            <tbody id="BodyTableDetail">
                            </tbody>
                        </table>
                    </div>
                    <div class="col-xs-9">
                        <div id="modal_report"></div>
                    </div>
                    <div class="col-xs-3">  
                        <a onclick="TambahProses()" class="btn btn-success btn pull-right"  data-toggle="tooltip" title="Tambah Proses" style="width: 100%"><i class="fa fa-plus-circle"></i> Tambah Proses</a>
                    </div>
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
            $('.select3').select2({
                dropdownParent: $('#modalEditShow'),
                allowClear : true,
            });
            $('.select2').select2({
                allowClear:true,
            }); 
        });

        function fetchTable() {

            $('#loading').show();
            $.get('{{ url('fetch/material_process/kanban_flow') }}', function(result, status, xhr) {
                if (result.status) {

                    // NON INDIRECT
                    $('#tableKanban').DataTable().clear();
                    $('#tableKanban').DataTable().destroy();
                    $('#headKanban').html("");
                    var headKanban = '<tr>';
                    headKanban += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                    headKanban += '<th style="vertical-align: middle; text-align: center;">Material Description</th>';
                    // headKanban += '<th style="vertical-align: middle; text-align: center;">Work Station</th>';
                    headKanban += '<th style="vertical-align: middle; text-align: center;">Flow</th>';
                    headKanban += '<th style="vertical-align: middle; text-align: center;">Aksi</th>';
                    headKanban += '</tr>';
                    $('#headKanban').append(headKanban);


                    $('#bodyKanban').html("");
                    var bodyKanban = '';


                    for (var i = 0; i < result.kanban.length; i++) {

                            var list_flow = result.kanban[i].flow.split(",");
                            var list_ws = result.kanban[i].ws.split(",");

                            bodyKanban += '<tr>';
                            bodyKanban += '<td style="vertical-align: middle; text-align: center;">' + result
                                .kanban[i].material_number + '</td>';
                            bodyKanban += '<td style="vertical-align: middle; text-align: left;">' + result
                                .kanban[i].material_description + '</td>';

                            bodyKanban += '<td style="vertical-align: middle; text-align: left;">';
                            bodyKanban += '<ol>';

                            for(var z = 0; z < list_flow.length; z++){
                                bodyKanban += '<li >';
                                bodyKanban += list_flow[z]+' ('+list_ws[z]+')';
                                bodyKanban += '</li>';
                            };

                            bodyKanban += '</ol>';
                            bodyKanban += '</td>';
                            // bodyKanban += '<td style="vertical-align: middle; text-align: center;">' + result
                            //     .kanban[i].work_station + '</td>';
                            // bodyKanban += '<td style="vertical-align: middle; text-align: center;">' + result
                            //     .kanban[i].name_flow + '</td>';
                            bodyKanban += '<td>';
                            bodyKanban += '<button class="btn btn btn-warning" onclick="ModalEdit(\''+result.kanban[i].material_number+'\')"><i class="fa fa-edit"></i> Edit</button>';
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


        
        function ModalEdit(material){
            $('#modalEditShow').modal('show');
            $('#judul_kategori').html(material);
            $('#jd_kategori').val(material);
            var data = {
                material:material
            }
            $.get('{{ url("fetch/material_process/kanban_flow") }}',data, function(result, status, xhr){
                if(result.status){
                    $('#TableDetail').DataTable().clear();
                    $('#TableDetail').DataTable().destroy();
                    $('#BodyTableDetail').html("");
                    var tableData = "";
                    var index = 1;
                    $.each(result.kanban_detail, function(key, value) {

                        var urutan = value.name_flow.split(",");
                        var q = value.urutan;
                        var w = result.kanban_detail.length;

                        tableData += '<tr>';
                        tableData += '<td width="1%">'+ index++ +'</td>';
                        tableData += '<td width="5%">'+ value.name_flow +'</td>';
                        tableData += '<td style=" text-align: center;" width="2%">';
                        tableData += '<a onclick="DeleteList(\''+value.id+'\', \''+material+'\')" class="btn btn-danger btn pull-right"  data-toggle="tooltip" title="Delete" style="width: 50px;"><i class="fa fa-trash"></i></a>';
                        if (q == 1) {
                            tableData += '&nbsp&nbsp';
                            tableData += '<a onclick="Turunkan(\''+value.id+'\', \''+material+'\')" class="btn btn-info btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-down"></i></a>';
                        }
                        else if(q == w){
                            tableData += '&nbsp&nbsp';
                            tableData += '<a onclick="Naikkan(\''+value.id+'\', \''+material+'\')" class="btn btn-warning btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-up"></i></a>';
                        }
                        else{
                            tableData += '&nbsp&nbsp';
                            tableData += '<a onclick="Naikkan(\''+value.id+'\', \''+material+'\')" class="btn btn-warning btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-up"></i></a>';
                            tableData += '&nbsp&nbsp';
                            tableData += '<a onclick="Turunkan(\''+value.id+'\', \''+material+'\')" class="btn btn-info btn"  data-toggle="tooltip" style="width: 50px"><i class="fa fa-arrow-down"></i></a>';
                        }
                        tableData += '</td>';
                        tableData += '</tr>';

                    });
                    $('#BodyTableDetail').append(tableData);
                }
                else{
                    alert('Attempt to retrieve data failed');
                }
            });
        }


    function DeleteList(id, material){
        if(confirm("Apakah anda yakin akan menghapus list kategori approval ini?")){
            var jenis = 'Hapus List';
            var data = {
                jenis:jenis,
                id:id,
                material:material
            }
            $.post('{{ url("delete/kategori/approval") }}', data, function(result, status, xhr) {
                if(result.status){
                    openSuccessGritter('Success','List Kategori Approval Berhasil Di Hapus!');
                    ModalEdit(material);
                    DataList();
                }else{
                    openErrorGritter('Error!', result.message);
                }
            });
        }else{
            return false;
        }
    }

    function TambahProses(){
        $("#modal_report").show();
        $("#modal_report").html('<div class="col-xs-9" style="padding-left: 0"><select class="form-control select2" id="add_user" name="add_user" data-placeholder="Pilih Nama" style="width: 100%" required><option value="">&nbsp;</option></select></div><div class="col-xs-3" style="padding-left: 0"><select onchange="AddApprover()" class="form-control select2" id="add_header" name="add_header" data-placeholder="Pilih Header" style="width: 100%" required><option value="">&nbsp;</option><option value="Created by/(作られた)">Created by</option><option value="Checked by/(チェック済み)">Checked by</option><option value="Accept by/(承認)">Accept by</option><option value="Approved by/(承認)">Approved by</option><option value="Known by/(承知)">Known by</option><option value="Prepared by/(準備)">Prepared by</option><option value="Received by/(が受信した)">Received by</option></select></div>');

        $('.select2').select2({
            dropdownParent: $('#modalEditShow'),
            allowClear : true,
        });
    }

    function AddApprover(){
        var material = $('#material').html();
        var user = $('#add_user').val();
        var header = $('#add_header').val();
        if (header == '') {
            return false;
        }else{
            var data = {
                material:material,
                user:user,
                header:header
            }
            $.post('{{ url("add/inject/approval") }}', data, function(result, status, xhr) {
                if(result.status){
                    openSuccessGritter('Success','List Kategori Berhasil Di Tambahkan!');
                    ModalEdit(material);
                    DataList();
                    $("#add_user").val('').trigger('change');
                    $("#add_header").val('').trigger('change');
                    $("#modal_report").hide();
                }else{
                    openErrorGritter('Error!', result.message);
                }
            });
        }
    }

    function Naikkan(id, material){
        var jenis = 'Naikkan';
        var data = {
            jenis:jenis,
            material:material,
            id:id
        }
        $.post('{{ url("pindah/posisi/approval") }}', data, function(result, status, xhr) {
            ModalEdit(material);
            DataList();
        });
    }

    function Turunkan(id, material){
        var jenis = 'Turunkan';
        var data = {
            jenis:jenis,
            material:material,
            id:id
        }
        $.post('{{ url("pindah/posisi/approval") }}', data, function(result, status, xhr) {
            ModalEdit(material);
            DataList();
        });
    }

    function SimpanJudul(){
        var judul_before = $('#judul_kategori').html();
        var judul_after = $('#jd_kategori').val();
        var data = {
            judul_before:judul_before,
            judul_after:judul_after
        }
        $.post('{{ url("simpan/judul") }}', data, function(result, status, xhr) {
            if(result.status){
                openSuccessGritter('Success','Judul Berhasil Diperbarui!');
                $('#jd_kategori').val(judul_after);
                ModalEdit(judul_after);
                DataList();
            }else{
                openErrorGritter('Error!', result.message);
            }
        });
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
