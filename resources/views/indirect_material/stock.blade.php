@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.tagsinput.css') }}" rel="stylesheet">
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
        #error,
        #alert {
            display: none;
        }

        .selected {
            background: gold !important;
        }
    </style>
@stop

@section('header')
    <section class="content-header">
        @foreach (Auth::user()->role->permissions as $perm)
            @php
                $navs[] = $perm->navigation_code;
            @endphp
        @endforeach

        <h1>
            {{ $title }} <span class="text-purple">{{ $title_jp }}</span>
            <small>{{ $subtitle }} <span class="text-purple"> {{ $subtitle_jp }}</span></small>
        </h1>


        <ol class="breadcrumb">
            <li>
                @if (in_array('S42', $navs))
                    <a data-toggle="modal" data-target="#add_material" class="btn btn-success btn-sm" style="color: white;">
                        <i class="fa fa-plus"></i>&nbsp;Add New Material
                    </a>
                @endif

                <a data-toggle="modal" data-target="#info" class="btn btn-default btn-sm" style="border-color: grey">
                    &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-info-circle"></i>&nbsp;Guidance&nbsp;&nbsp;&nbsp;&nbsp;
                </a>

                <a data-toggle="modal" href="{{ url('index/indirect_material_log') }}" class="btn btn-primary btn-sm"
                    style="color: white;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <i class="fa fa-th-list"></i>&nbsp;&nbsp;Logs&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                </a>

                <a data-toggle="modal" href="{{ url('index/indirect_material_monitoring') }}" class="btn btn-info btn-sm"
                    style="color: black;">&nbsp;
                    <i class="fa fa-bar-chart"></i>&nbsp;Expired Monitoring&nbsp;
                </a>
            </li>
        </ol>

    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content" style="font-size: 0.8vw;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="alert alert-warning alert-dismissible" id="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                    <p id="alert-msg"></p>
                </div>
            </div>
            {{-- <div class="col-xs-12">
			<div class="box box-primary">
				<div class="box-header">
					<h3 class="box-title">Filters</h3>
					<h3 id="printer_name" class="box-title pull-right"></h3>
				</div>
				<input type="hidden" value="{{csrf_token()}}" name="_token" />
				<div class="box-body">
					
					<div class="row">
						<div class="col-md-4 col-md-offset-4">
							<div class="form-group">
								<label>Material Number</label>
								<select class="form-control select2" multiple="multiple" data-placeholder="Select Material" id="filter_material_number" style="width: 100%">
									<option style="color:grey;" value="">Select Material</option>
									@foreach ($materials as $material)
									<option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
									@endforeach
								</select>
							</div>
						</div>
					</div>

					<div class="col-md-4 col-md-offset-4">
						<div class="form-group pull-right">
							<a href="javascript:void(0)" onClick="clearConfirmation()" class="btn btn-danger">Clear</a>
							<button id="search" onClick="fetchTable()" class="btn btn-primary">Search</button>
						</div>
					</div>

				</div>
			</div>
		</div> --}}

            <div class="col-xs-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Stock</a></li>
                        <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">New</a></li>
                        <li class="vendor-tab"><a href="#tab_3" data-toggle="tab" id="tab_header_3">Out</a></li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1">
                            <table id="table-material" class="table table-bordered table-striped table-hover"
                                style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>Material</th>
                                        <th>Description</th>
                                        <th>Storage Location</th>
                                        <th>License</th>
                                        <th>Qty</th>
                                        <th>Bun</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody id="body-material">
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
                        <div class="tab-pane" id="tab_2">
                            <table id="table-new" class="table table-bordered table-hover" style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>QR Code</th>
                                        <th>Material</th>
                                        <th>Description</th>
                                        <th>Storage Location</th>
                                        <th>License</th>
                                        <th>In Date</th>
                                        <th>Mfg date</th>
                                        <th>Exp date</th>
                                        <th>Quantity</th>
                                        <th>Bun</th>
                                        <th>Print</th>
                                        <th>Created At</th>
                                        <th>Check</th>
                                    </tr>
                                </thead>
                                <tbody id="body-new">
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
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>

                            <center>
                                <span style="font-weight: bold; font-size: 20px;">Item Picked: </span>
                                <span id="picked" style="font-weight: bold; font-size: 24px; color: red;">0</span>
                            </center>
                            <button class="btn btn-primary" target="_blank"
                                style="margin-left:40%; width: 20%; font-size: 22px; margin-bottom: 30px;"
                                onclick="printJob(this)"><i class="fa fa-print"></i> PRINT</button>

                        </div>
                        <div class="tab-pane" id="tab_3">
                            <table id="table-out" class="table table-bordered table-striped table-hover"
                                style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>QR Code</th>
                                        <th>Material</th>
                                        <th>Description</th>
                                        <th>Storage Location</th>
                                        <th>License</th>
                                        <th>In Date</th>
                                        <th>Mfg date</th>
                                        <th>Exp date</th>
                                        <th>Quantity</th>
                                        <th>Bun</th>
                                        <th>Print</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody id="body-out">
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
    <div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="width: 70%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Add New Material Guidance</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <h2 style="margin-top: 0px;">Type Expired Date :</h2>
                            <b><i>Type 1</i></b> : Expired date dihitung secara otomatis berdasarkan manufacturing date<br>
                            <b><i>Type 2</i></b> : Expired date tertera pada kemasan<br>
                            <b><i>Type 3</i></b> : Expired date dihitung secara otomatis berdasarkan tanggal kedatangan<br>
                            <b><i>Type 4</i></b> : Adjust setelah chemical dibuka
                        </div>

                        <div class="col-xs-12" style="margin-top: 2%;">
                            <table id="table-master" class="table table-bordered table-striped table-hover"
                                style="width: 100%; font-size: 0.8vw;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>Material</th>
                                        <th>Description</th>
                                        <th>Storage Location</th>
                                        <th>Label</th>
                                        <th>Type</th>
                                        <th>Expipred</th>
                                        <th>License</th>
                                        <th>Lot</th>
                                    </tr>
                                </thead>
                                <tbody id="body-master">
                                    @foreach ($materials as $material)
                                        <tr>
                                            <td>{{ $material->material_number }}</td>
                                            <td>{{ $material->material_description }}</td>
                                            <td>{{ $material->storage_location }}</td>
                                            <td>{{ $material->label }}</td>
                                            <td>{{ $material->type }}</td>
                                            <td>{{ $material->expired }} Days</td>
                                            <td>
                                                @if ($material->license == 1)
                                                    {{ 'Yes' }}
                                                @else
                                                    {{ '-' }}
                                                @endif
                                            </td>
                                            <td>{{ $material->lot }} {{ $material->bun }}</td>
                                        </tr>
                                    @endforeach
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
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal modal-default fade" id="add_material">
        <div class="modal-dialog" style="width: 45%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">
                            &times;
                        </span>
                    </button>
                    <h4 class="modal-title">
                        Add New Material
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="box-body">
                                <input type="hidden" value="{{ csrf_token() }}" name="_token" />

                                <div class="form-group row" align="right">
                                    <label class="col-sm-3">Material<span class="text-red">*</span></label>
                                    <div class="col-sm-8" align="left">
                                        <select class="form-control select2" data-placeholder="Select Material"
                                            id="material_number" onchange="checkMaterial()"
                                            style="width: 100%; font-size: 0.8vw;">
                                            <option style="color:grey;" value="">Select Material</option>
                                            @foreach ($materials as $material)
                                                <option
                                                    value="{{ $material->material_number }}_{{ $material->material_description }}_{{ $material->type }}_{{ $material->expired }}_{{ $material->license }}_{{ $material->category }}">
                                                    {{ $material->material_number }} -
                                                    {{ $material->material_description }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-sm-3">In Date<span class="text-red">*</span></label>
                                    <div class="col-sm-4" align="left">
                                        <div class="input-group date">
                                            <div class="input-group-addon bg-blue">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input onchange="checkInDate()" type="text"
                                                class="form-control datepicker" name="in_date" id="in_date"
                                                placeholder="Select In Date" style="font-size: 0.8vw;">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-sm-3">Manufacturing Date<span class="text-red">*</span></label>
                                    <div class="col-sm-4" align="left">
                                        <div class="input-group date">
                                            <div class="input-group-addon bg-blue">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input onchange="checkMfgDate()" type="text"
                                                class="form-control datepicker" name="mfg_date" id="mfg_date"
                                                placeholder="Select Mfg. Date" style="font-size: 0.8vw;">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-sm-3">Expired Date<span class="text-red">*</span></label>
                                    <div class="col-sm-4" align="left">
                                        <div class="input-group date">
                                            <div class="input-group-addon bg-blue">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control datepicker" name="exp_date"
                                                id="exp_date" placeholder="Select Exp. Date" style="font-size: 0.8vw;">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-sm-3">Quantity<span class="text-red">**</span></label>
                                    <div class="col-sm-4" align="left">
                                        <input type="number" class="form-control" placeholder="Input Qty"
                                            id="quantity" style="width: 100%; font-size: 0.8vw;">
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-sm-3">License<span class="text-red">***</span></label>
                                    <div class="col-sm-4" align="left">
                                        <input type="text" class="form-control" placeholder="Input License"
                                            id="license" style="width: 100%; font-size: 0.8vw;">
                                    </div>
                                </div>

                                <div>
                                    <p>
                                        <span class="text-red"><b>*</b></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Field is
                                        required<br>
                                        <span class="text-red"><b>**</b></span>&nbsp;&nbsp;&nbsp;: Labels to be created<br>
                                        <span class="text-red"><b>***</b></span>&nbsp;: Controlled material license must be
                                        filled (<b><i>Q81900122</i></b> & <b><i>Q81900120</i></b>)
                                    </p>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" onclick="addMaterial()"> Add Material</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-default fade" id="upload_material">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form id="importForm" method="post" enctype="multipart/form-data" autocomplete="off">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Upload Confirmation</h4>
                        Format: [Material Number][Quantity]<br>
                        Sample: <a
                            href="{{ url('uploads/indirect_material_chm/sample/import_chemical_stock(200710_09.58).xlsx') }}">import_chemical_stock(200710_09.58).xlsx</a>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row" align="right">
                            <label class="col-sm-3">Date<span class="text-red">*</span></label>
                            <div class="col-sm-4" align="left">
                                <input type="text" class="form-control datepicker" name="upload_date"
                                    id="upload_date" placeholder="Select Date">
                            </div>
                        </div>

                        <div class="form-group row" align="right">
                            <label class="col-sm-3">Upload Excel<br>file here<span class="text-red">*</span></label>
                            <div class="col-sm-4" align="left">
                                <input type="file" name="upload_file" id="upload_file"
                                    accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button id="modalImportButton" type="submit" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="{{ url('js/icheck.min.js') }}"></script>
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

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $('.select2').select2();


            $('.datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });

            $("#in_date").prop('disabled', true);
            $("#mfg_date").prop('disabled', true);
            $("#exp_date").prop('disabled', true);
            $("#quantity").prop('disabled', true);
            $("#license").prop('disabled', true);

            fetchTable();
            fetchTableMaster();


        });

        function clearConfirmation() {
            location.reload(true);
        }

        $("form#importForm").submit(function(e) {
            if ($('#upload_file').val() == '') {
                openErrorGritter('Error!', 'You need to select file');
                return false;
            }

            if ($('#upload_date').val() == '') {
                openErrorGritter('Error!', 'You need to select date');
                return false;
            }

            $("#loading").show();

            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: '{{ url('import/indirect_material_stock') }}',
                type: 'POST',
                data: formData,
                success: function(result, status, xhr) {
                    if (result.status) {
                        $("#loading").hide();

                        $('#table-material').DataTable().ajax.reload();
                        $('#table-new').DataTable().ajax.reload();

                        $("#upload_file").val('');
                        $("#upload_date").val('');

                        $('#upload_material').modal('hide');
                        openSuccessGritter('Success', result.message);

                        if (result.notInsert.length > 0) {
                            $('#alert').show();
                            $('#alert-msg').html();
                            $('#alert-msg').append().empty();

                            var msg = '';
                            msg +=
                                'Material berikut tidak disimpan, karena tidak terdaftar sebagai material chemical :<br>';
                            for (var i = 0; i < result.notInsert.length; i++) {
                                msg += result.notInsert[i].material_number + ' ' + result.notInsert[i]
                                    .material_description + '<br>';
                            }
                            $('#alert-msg').append(msg);
                        }

                    } else {
                        $("#loading").hide();

                        openErrorGritter('Error!', result.message);
                    }
                },
                error: function(result, status, xhr) {
                    $("#loading").hide();

                    openErrorGritter('Error!', result.message);
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        var material_number = '';
        var type = '';
        var expired = '';
        var license = '';
        var category = '';

        $("#add_material").on("hidden.bs.modal", function() {
            $("#material_number").prop('selectedIndex', 0).change();

            $('#in_date').val('');
            $('#mfg_date').val('');
            $('#exp_date').val('');
            $('#quantity').val('');
            $('#license').val('');

            $("#in_date").prop('disabled', true);
            $("#mfg_date").prop('disabled', true);
            $("#exp_date").prop('disabled', true);
            $("#quantity").prop('disabled', true);
            $("#license").prop('disabled', true);

            material_number = '';
            type = '';
            expired = '';
            license = '';
            category = '';

        });

        function checkMaterial() {

            $('#in_date').val('');
            $('#mfg_date').val('');
            $('#exp_date').val('');
            $('#quantity').val('');
            $('#license').val('');

            var data = $('#material_number').val().split("_");

            material_number = data[0];
            type = data[2];
            expired = data[3];
            license = data[4];
            category = data[5];

            $("#quantity").prop('disabled', false);


            if (license == 1) {
                $("#license").prop('disabled', false);
            } else {
                $("#license").prop('disabled', true);
                $("#license").val('-');
            }

            if (type == 1) {
                $("#in_date").prop('disabled', false);
                $("#mfg_date").prop('disabled', false);
                $("#exp_date").prop('disabled', true);

            } else if (type == 2) {
                $("#in_date").prop('disabled', false);
                $("#mfg_date").prop('disabled', false);
                $("#exp_date").prop('disabled', false);

            } else if (type == 3) {
                $("#in_date").prop('disabled', false);
                $("#mfg_date").prop('disabled', true);
                $("#exp_date").prop('disabled', true);

            } else if (type == 4) {
                $("#in_date").prop('disabled', false);
                $("#mfg_date").prop('disabled', true);
                $("#exp_date").prop('disabled', true);
                $("#exp_date").val('-');
            }

        }

        function checkInDate() {
            // koef adalah allowence untuk pengiriman yang angkanya telah di sepakati
            // impor = 45 hari 
            // lokal = 0 hari 
            var koef = 0;
            if (category == 'IMPOR') {
                koef = 45;
            }

            // remove koef
            koef = 0;

            if (type == 3) {
                var in_date = new Date($("#in_date").val());
                var exp_date = in_date.setDate(in_date.getDate() + parseInt(expired - koef));
                $("#exp_date").val(formatDate(exp_date));
                $("#mfg_date").val('-');
            }

        }

        function checkMfgDate() {

            if (type == 1) {
                var mfg_date = new Date($("#mfg_date").val());
                var exp_date = mfg_date.setDate(mfg_date.getDate() + parseInt(expired));
                $("#exp_date").val(formatDate(exp_date));
            }

        }

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
        }


        function addMaterial() {
            var in_date = $('#in_date').val();
            var mfg_date = $('#mfg_date').val();
            var exp_date = $('#exp_date').val();
            var quantity = $('#quantity').val();
            var license = $('#license').val();

            if (material_number == '') {
                openErrorGritter('Error!', 'You need to select material');
                return false;
            }

            if (exp_date == '') {
                openErrorGritter('Error!', 'You need to input all field');
                return false;
            }

            if (quantity == '') {
                openErrorGritter('Error!', 'You need to input quantity');
                return false;
            }

            if (license == '') {
                openErrorGritter('Error!', 'You need to input license');
                return false;
            }

            var data = {
                in_date: in_date,
                mfg_date: mfg_date,
                exp_date: exp_date,
                material_number: material_number,
                quantity: quantity,
                type: type,
                license: license
            }

            $("#loading").show();

            $.post('{{ url('input/indirect_material_stock') }}', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter('Success', result.message);

                    $('#add_material').modal('hide');

                    $("#material_number").prop('selectedIndex', 0).change();

                    $('#in_date').val('');
                    $('#mfg_date').val('');
                    $('#exp_date').val('');
                    $('#quantity').val('');
                    $('#license').val('');

                    $("#in_date").prop('disabled', true);
                    $("#mfg_date").prop('disabled', true);
                    $("#exp_date").prop('disabled', true);
                    $("#quantity").prop('disabled', true);
                    $("#license").prop('disabled', true);

                    material_number = '';
                    type = '';
                    expired = '';
                    license = '';

                    $('#table-material').DataTable().ajax.reload();
                    $('#table-new').DataTable().ajax.reload();

                    $("#loading").hide();

                } else {
                    openErrorGritter('Error', result.message);
                    $("#loading").hide();

                }
            });

        }

        function fetchTableMaster() {

            $('#table-master').DataTable().destroy();
            $('#table-master tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" />');
            });
            var table_material = $('#table-master').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                "pageLength": 10,
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
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                "serverSide": false,
                "columnDefs": [{
                        "width": "35%",
                        className: "text-left",
                        "targets": 1
                    },
                    {
                        "width": "20%",
                        "targets": 2
                    }
                ]
            });
            table_material.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            $('#table-master tfoot tr').prependTo('#table-master thead');

        }

        function fetchTable() {

            $('#table-material').DataTable().destroy();
            $('#table-material tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" />');
            });
            var table_material = $('#table-material').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                "pageLength": 25,
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
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/indirect_material_stock') }}"
                },
                "columns": [{
                        "data": "material_number"
                    },
                    {
                        "data": "material_description",
                        width: "30%",
                        className: "text-left"
                    },
                    {
                        "data": "storage_location"
                    },
                    {
                        "data": "license"
                    },
                    {
                        "data": "quantity",
                        className: "text-right"
                    },
                    {
                        "data": "bun"
                    },
                    {
                        "data": "updated_at"
                    }
                ]
            });
            table_material.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            $('#table-material tfoot tr').prependTo('#table-material thead');






            $('#table-new').DataTable().destroy();
            $('#table-new tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" />');
            });
            var table_new = $('#table-new').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                "pageLength": 25,
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
                'order': [],
                'info': true,
                'autoWidth': false,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/indirect_material_new') }}",
                },
                "columns": [{
                        "data": "qr_code"
                    },
                    {
                        "data": "material_number"
                    },
                    {
                        "data": "material_description",
                        width: "25%",
                        className: "text-left"
                    },
                    {
                        "data": "storage_location"
                    },
                    {
                        "data": "license"
                    },
                    {
                        "data": "in_date"
                    },
                    {
                        "data": "mfg_date"
                    },
                    {
                        "data": "exp_date"
                    },
                    {
                        "data": "quantity",
                        className: "text-right"
                    },
                    {
                        "data": "bun"
                    },
                    {
                        "data": "print"
                    },
                    {
                        "data": "created_at"
                    },
                    {
                        "data": "check"
                    }
                ]
            });
            table_new.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            $('#table-new tfoot tr').appendTo('#table-new thead');





            $('#table-out').DataTable().destroy();
            $('#table-out tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" />');
            });
            var table_out = $('#table-out').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                "pageLength": 25,
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
                'order': [],
                'info': true,
                'autoWidth': false,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/indirect_material_out') }}"
                },
                "columns": [{
                        "data": "qr_code"
                    },
                    {
                        "data": "material_number"
                    },
                    {
                        "data": "material_description",
                        width: "25%",
                        className: "text-left"
                    },
                    {
                        "data": "location"
                    },
                    {
                        "data": "license"
                    },
                    {
                        "data": "in_date"
                    },
                    {
                        "data": "mfg_date"
                    },
                    {
                        "data": "exp_date"
                    },
                    {
                        "data": "quantity",
                        className: "text-right"
                    },
                    {
                        "data": "bun"
                    },
                    {
                        "data": "print"
                    },
                    {
                        "data": "created_at"
                    }
                ]
            });
            table_out.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });
            $('#table-out tfoot tr').appendTo('#table-out thead');

        }

        function print(qr_code) {
            var data = {
                qr_code: qr_code
            }

            window.open('{{ url('print/indirect_material_label') }}' + '/' + qr_code, '_blank');

            $('#table-new').DataTable().ajax.reload();
            openSuccessGritter('Success!', '');

        }

        var arr = [];

        function showSelected(elem) {
            var id = $(elem).attr("id");

            if ($(elem).is(':checked')) {
                arr.push(id);
                // $("#selected1").empty();

                // $.each( arr, function( key, value ) {
                // 	$("#selected1").append(value[0]+" - "+value[1]+"<br>");
                // });

            } else {
                arr.splice($.inArray(id, arr), 1);
                // $("#selected1").empty();

                // $.each( arr, function( key, value ) {
                // 	$("#selected1").append(value[0]+" - "+value[1]+"<br>");
                // });
            }

            $("#picked").html(arr.length);
        }


        function printJob() {

            var qr_code = arr.toString();

            window.open('{{ url('print/indirect_material_label') }}' + '/' + qr_code, '_blank');

            $('#table-new').DataTable().ajax.reload();
            openSuccessGritter('Success!', '');

            arr = [];
            $("#picked").html(arr.length);

        }

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '4000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '4000'
            });
        }
    </script>
@endsection
