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
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 0;
            padding-bottom: 0;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@endsection
@section('header')
    <section class="content-header">
        <h1>
            List of {{ $page }}s
            <small>it all starts here</small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <!-- <a data-toggle="modal" data-target="#deleteModal" class="btn btn-danger btn-sm" style="color:white">Delete {{ $page }}s</a> -->
                &nbsp;
                <?php if (strtoupper(Auth::user()->username) == 'PI1005001' || str_contains(Auth::user()->role_code, 'MIS')) { ?>
                <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm" style="color:white">Import
                    {{ $page }}s</a>
                <?php } ?>

                <!-- &nbsp;
                                    <a data-toggle="modal" data-target="#createModal" class="btn btn-primary btn-sm" style="color:white">Create {{ $page }}</a> -->
            </li>
        </ol>
    </section>
@endsection


@section('content')
    <?php if (strtoupper(Auth::user()->username) != 'PI1005001' && !str_contains(Auth::user()->role_code, 'MIS') && !str_contains(Auth::user()->role_code, 'PC')) { ?>
    <script>
        window.location.href = '{{ route('home') }}';
    </script>
    <?php } ?>
    <section class="content">
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

        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 3001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-spinner"></i></span>
            </p>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">

                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Pilih Bulan</label>
                                <div class="input-group date" style="width: 100%;">
                                    <input type="text" placeholder="Pilih Bulan" class="form-control datepicker"
                                        name="mon" id="mon">
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>Pilih Item</label>
                                <div class="input-group date" style="width: 100%;">
                                    <select class="form-control select2" name="item_category" id="item_category"
                                        data-placeholder="Pilih Item" style="width: 100%">
                                        <option value=""></option>
                                        <option value="FL51 Key">FL Key</option>
                                        <option value="FL51 Body">FL Body</option>
                                        <option value="SX51 Key">SX Key</option>
                                        <option value="SX51 Body">SX Body</option>
                                        <option value="CL51 Key">CL Key</option>
                                        <option value="ACC">ACC</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-sm btn-primary"
                                        onclick="draw_table()">Filter</button>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <table id="example1" class="table table-bordered table-striped table-hover">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>Material</th>
                                        <th>Material Description</th>
                                        <th>Storage Location</th>
                                        <th>Category</th>
                                        <th>Due Date</th>
                                        <th>Qty</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodyList">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="importForm" method="get" action="{{ url('destroy/assy_schedule') }}">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>From</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="datefrom"
                                            name="datefrom" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>To</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right" id="dateto"
                                            name="dateto" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Origin Group</label>
                                    <select class="form-control select2" multiple="multiple" name="origin_group[]"
                                        id='origin_group' data-placeholder="Select Origin Group" style="width: 100%;"
                                        required>
                                        <option></option>
                                        @foreach ($origin_groups as $origin_group)
                                            <option value="{{ $origin_group->origin_group_code }}">
                                                {{ $origin_group->origin_group_code }} -
                                                {{ $origin_group->origin_group_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                        <button id="modalImportButton" type="submit"
                            onclick="return confirm('Are you sure you want to delete this assy schedule?');"
                            class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal modal-danger fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
                </div>
                <div class="modal-body" id="modalDeleteBody">
                    Are you sure delete?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a id="modalDeleteButton" href="#" type="button" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Create {{ $page }}</h4>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                        <div class="form-group row" align="right">
                            <label class="col-sm-4">Material<span class="text-red">*</span></label>
                            <div class="col-sm-6" align="left">
                                <select class="form-control select2" id="material_number" style="width: 100%;"
                                    data-placeholder="Choose a Material Number..." required>
                                    <option value=""></option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->material_number }}">{{ $material->material_number }}
                                            - {{ $material->material_description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-4">Due Date<span class="text-red">*</span></label>
                            <div class="col-sm-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="due_date"
                                        name="due_date">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input min="1" type="number" class="form-control" id="quantity"
                                        placeholder="Enter Quantity" required>
                                    <span class="input-group-addon">pc(s)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" onclick="create()" class="btn btn-primary" data-dismiss="modal"><i
                            class="fa fa-plus"></i> Create</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="EditModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Create {{ $page }}</h4>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                        <div class="form-group row" align="right">
                            <label class="col-sm-4">Material<span class="text-red">*</span></label>
                            <div class="col-sm-6" align="left">
                                <select class="form-control select2" id="material_number_edit" style="width: 100%;"
                                    data-placeholder="Choose a Material Number..." required disabled>
                                    <option value=""></option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->material_number }}">{{ $material->material_number }}
                                            - {{ $material->material_description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-4">Due Date<span class="text-red">*</span></label>
                            <div class="col-sm-6">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" id="due_date_edit"
                                        name="due_date" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input min="1" type="number" class="form-control" id="quantity_edit"
                                        placeholder="Enter Quantity" required>
                                    <input type="hidden" id="id_edit">
                                    <span class="input-group-addon">pc(s)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" onclick="edit()" class="btn btn-warning" data-dismiss="modal"><i
                            class="fa fa-pencil"></i> Edit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="ViewModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Detail {{ $page }}</h4>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                        <div class="form-group row" align="right">
                            <label class="col-sm-6">Material Number</label>
                            <div class="col-sm-6" align="left" id="material_number_view"></div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-6">Material Description</label>
                            <div class="col-sm-6" align="left" id="material_description_view"></div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-6">Origin Group</label>
                            <div class="col-sm-6" align="left" id="origin_group_view"></div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-6">Due Date</label>
                            <div class="col-sm-6" align="left" id="due_date_view"></div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-6">Quantity</label>
                            <div class="col-sm-6" align="left" id="quantity_view"></div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-6">Created By</label>
                            <div class="col-sm-6" align="left" id="created_by_view"></div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-6">Last Update</label>
                            <div class="col-sm-6" align="left" id="last_updated_view"></div>
                        </div>
                        <div class="form-group row" align="right">
                            <label class="col-sm-6">Created At</label>
                            <div class="col-sm-6" align="left" id="created_at_view"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="importForm" method="post" action="{{ url('import/assy_schedule') }}"
                    enctype="multipart/form-data">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
                        Format: [Material Number][Due Date][Quantity]<br>
                        Sample: <a
                            href="{{ url('download/manual/import_assy_schedule.txt') }}">import_assy_schedule.txt</a>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="item_imp" class="col-sm-2 control-label">Pilih Bulan</label>

                            <div class="col-sm-10">
                                <input type="text" placeholder="Pilih Bulan" class="form-control datepicker"
                                    name="mon_imp" id="mon_imp">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="item_imp" class="col-sm-2 control-label">Pilih Item</label>

                            <div class="col-sm-10">
                                <select class="form-control select2" name="item_imp" id="item_imp"
                                    data-placeholder="Pilih Item" style="width: 100%">
                                    <option value=""></option>
                                    <option value="FL51 Key">FL Key</option>
                                    <option value="FL51 Body">FL Body</option>
                                    <option value="SX51 Key">SX Key</option>
                                    <option value="SX51 Body">SX Body</option>
                                    <option value="CL51 Key">CL Key</option>
                                    <option value="ACC">ACC</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="InputFile" class="col-sm-2 control-label">Pilih File</label>

                            <div class="col-sm-10">
                                <input class="form-control" type="file" name="assy_schedule" id="InputFile"
                                    accept="text/plain" style="width: 100%">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                        <button id="modalImportButton" type="submit" class="btn btn-success" onclick="showLoading()"><i
                                class="fa fa-plus"></i>&nbsp; Import</button>
                    </div>
                </form>
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
            $('.datepicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true
            });

            $('.select2').select2();
        });

        function draw_table() {
            $("#loading").show();

            var category = $("#item_category").val();
            if ($("#item_category").val().includes("Key") || $("#item_category").val().includes("Body")) {
                category = cat.split(" ")[1];
            }

            var cat = $("#item_category").val();
            var data = {
                item_category: cat,
                mon: $("#mon").val(),
            }

            $.get('{{ url('fetch/assy_schedule') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#example1').DataTable().clear();
                    $('#example1').DataTable().destroy();
                    $('#tableBodyList').html("");

                    var tableData = "";
                    $.each(result.picking, function(index, value) {
                        tableData += "<tr>";
                        tableData += "<td>" + value.material_number + "</td>";
                        tableData += "<td>" + value.material_description + "</td>";
                        tableData += "<td>" + value.remark + "</td>";
                        tableData += "<td>" + category + "</td>";
                        tableData += "<td>" + value.due_date + "</td>";
                        tableData += "<td>" + value.quantity + "</td>";
                        tableData += "<td>" + value.created_at + "</td>";
                        tableData += "</tr>";
                    })

                    $('#tableBodyList').append(tableData);

                    $("#loading").hide();

                    $('#example1 tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="cari" style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="20"/>');
                    });

                    var table = $('#example1').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [25, 50, -1],
                            ['25 rows', '50 rows', 'Show all']
                        ],
                        'buttons': {
                            buttons: [{
                                    extend: 'pageLength',
                                    className: 'btn btn-default',
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
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        "order": [
                            [4, 'asc']
                        ]
                    });

                    table.columns().every(function() {
                        var that = this;

                        $('.cari', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('#example1 tfoot tr').appendTo('#example1 thead');
                } else {
                    $("#loading").hide();
                    openErrorGritter('Error!', 'Fill all field');
                }
            })

        }

        function showLoading() {
            $("#loading").show();
        }

        function create() {
            var data = {
                material_number: $("#material_number").val(),
                due_date: $("#due_date").val(),
                quantity: $("#quantity").val()
            };

            $.post('{{ url('create/assy_schedule') }}', data, function(result, status, xhr) {
                if (result.status == true) {
                    $('#example1').DataTable().ajax.reload(null, false);
                    openSuccessGritter("Success", "New Assy schedule has been created.");
                } else {
                    openErrorGritter("Error", "Assy schedule not created.");
                }
            })
        }

        function modalEdit(id) {
            $('#EditModal').modal("show");

            var data = {
                id: id
            };

            $.get('{{ url('edit/assy_schedule') }}', data, function(result, status, xhr) {
                $("#id_edit").val(id);
                $('#material_number_edit').val(result.datas.material_number).trigger('change.select2');
                $("#due_date_edit").val(result.datas.due_date);
                $("#quantity_edit").val(result.datas.quantity);
            })
        }

        function edit() {
            var data = {
                id: $("#id_edit").val(),
                quantity: $("#quantity_edit").val()
            };

            $.post('{{ url('edit/assy_schedule') }}', data, function(result, status, xhr) {
                if (result.status == true) {
                    $('#example1').DataTable().ajax.reload(null, false);
                    openSuccessGritter("Success", "New Assy schedule has been edited.");
                } else {
                    openErrorGritter("Error", "Failed to edit.");
                }
            })
        }

        function modalView(id) {
            $("#ViewModal").modal("show");
            var data = {
                id: id
            }

            $.get('{{ url('view/assy_schedule') }}', data, function(result, status, xhr) {
                $("#material_number_view").text(result.datas[0].material_number);
                $("#material_description_view").text(result.datas[0].material_description);
                $("#origin_group_view").text(result.datas[0].origin_group_name);
                $("#due_date_view").text(result.datas[0].due_date);
                $("#quantity_view").text(result.datas[0].quantity);
                $("#created_by_view").text(result.datas[0].name);
                $("#last_updated_view").text(result.datas[0].updated_at);
                $("#created_at_view").text(result.datas[0].created_at);
            })
        }

        function modalDelete(id) {
            var data = {
                id: id
            };

            if (!confirm("Are you sure want to delete Material schedule ?")) {
                return false;
            }

            $.post('{{ url('delete/assy_schedule') }}', data, function(result, status, xhr) {
                $('#example1').DataTable().ajax.reload(null, false);
                openSuccessGritter("Success", "Delete Material Schedule");
            })
        }

        $(function() {
            $('#datefrom').datepicker({
                autoclose: true
            });
            $('#dateto').datepicker({
                autoclose: true
            });
        })

        function deleteConfirmation(url, name, id) {
            jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
            jQuery('#modalDeleteButton').attr("href", url + '/' + id);
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '3000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '3000'
            });
        }
    </script>

@stop
