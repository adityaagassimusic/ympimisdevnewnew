@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        #listTableBody>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c;
        }

        table.table-bordered {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            text-align: center;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(150, 150, 150);
            vertical-align: middle;
            padding: 0px 3px 0px 3px;
        }

        .datepicker {
            padding: 6px 12px 6px 12px;
        }

        .btn {
            margin: 2px;
        }

        #loading {
            display: none;
        }
    </style>
@stop

@section('header')
    <section class="content-header">
        <h1>
            {{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
        </h1>
        <ol class="breadcrumb">
        </ol>
    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content" style="font-size: 10pt;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <div>
                <center style="position: absolute; top: 45%; left: 41%;">
                    <span style="font-size: 3vw; text-align: center;">
                        <i class="fa fa-spin fa-refresh"></i>
                        &nbsp;Please Wait ...
                    </span>
                </center>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-body">
                        <table id="listTable" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="width: 7.5%">EO Number</th>
                                    <th style="width: 5%">GMC YMPI</th>
                                    <th style="width: 5%">GMC Buyer</th>
                                    <th style="width: 25%">Description</th>
                                    <th style="width: 5%">Uom</th>
                                    <th style="width: 5%">SLoc</th>
                                    <th style="width: 5%">Price</th>
                                    <th style="width: 7.5%">Price Valid Date</th>
                                    <th style="width: 5%">Att</th>
                                    <th style="width: 10%">Remark</th>
                                    <th style="width: 5%">Status</th>
                                    <th style="width: 15%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="listTableBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal_upload">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="sk_num3"></h4>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                        <div class="form-group">
                            <label>3M Need : </label><br>
                            <label class="radio-inline">
                                <input type="radio" name="tiga_em_need" value="Need">Need 3M
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="tiga_em_need" value="No Need">No Need 3M
                            </label>
                        </div>

                        <button class="btn btn-success" style="width: 100%" id="upload-trial"><i
                                class="fa fa-check"></i>&nbsp; Upload Trial File(s)</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBom">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"
                        style="background-color: #3c8dbc; padding-top: 5px; padding-bottom: 5px; color: white ">
                        <center><b>Upload BOM & Standard Time</b></center>
                    </h4>
                    <div class="modal-body">
                        <div class="col-xs-12">
                            <center>
                                <h4 id="id_form" style="font-weight: bold"></h4>
                            </center>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-5 control-label">Material Description<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="item_desc" placeholder="Material Desc"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-5 control-label">No Approval<span class="text-red">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="no_approval" placeholder="No Approval"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-5 control-label">BOM & Std Time Form<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-7">
                                    <input type="file" name="bom_file" id="bom_file" accept="application/pdf">
                                    <span class="help-block">- Upload Form for MIRAI Approval -</span>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-5 control-label">GMC Material<span class="text-red">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="bom_gmc"
                                        placeholder="GMC Material">
                                    <input type="hidden" id="bom_id">
                                    <input type="hidden" id="material_id">
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-5 control-label">Department<span class="text-red">*</span></label>
                                <div class="col-sm-7">
                                    <select class="select2 form-control" id="bom_dept"
                                        data-placeholder="Select Department" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($dept_prod as $dp)
                                            <option value="{{ $dp->department }}">{{ $dp->department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xs-12"
                                style="margin-bottom: 5px;padding-left:10px;padding-right:10px; display: none">
                                <label class="col-sm-5 control-label">Total Quantity<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="bom_qty" onkeyup="sum_total()"
                                        placeholder="Total Quantity">
                                </div>
                            </div>

                            <div class="col-xs-12"
                                style="margin-bottom: 5px;padding-left:10px;padding-right:10px; display: none">
                                <label class="col-sm-5 control-label">Total Standard Time<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="bom_std_time" onkeyup="sum_total()"
                                        placeholder="Total Standard Time">
                                </div>
                            </div>

                            <div class="col-xs-12"
                                style="margin-bottom: 5px;padding-left:10px;padding-right:10px; display: none">
                                <label class="col-sm-5 control-label">Total (Quantity * Standard Time)<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" class="form-control" id="bom_total" placeholder="Total"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <div class="col-sm-12">
                                    <button class="btn btn-success pull-right" onclick="saveBom()"><i
                                            class="fa fa-check"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSales">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"
                        style="background-color: #3c8dbc; padding-top: 5px; padding-bottom: 5px; color: white ">
                        <center><b>Upload Sales Price</b></center>
                    </h4>
                    <div class="modal-body">
                        <div class="col-xs-12">
                            <center>
                                <h4 id="sales_id_form" style="font-weight: bold"></h4>
                            </center>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-4 control-label">Material Description<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="sales_item_desc"
                                        placeholder="Material Desc" readonly>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-4 control-label">GMC Material<span class="text-red">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" id="sales_gmc" placeholder="GMC Material"
                                        readonly>
                                    <input type="hidden" id="sales_id">
                                    <input type="hidden" id="sales_material_id">
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-4 control-label">Sales Price Form<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-6">
                                    <input type="file" name="sales_file" id="sales_file" accept="application/pdf">
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-4 control-label">Sales Price<span class="text-red">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control" name="sales_price" id="sales_price"
                                        placeholder="Sales Price">
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-4 control-label">Valid Date<span class="text-red">*</span></label>
                                <div class="col-sm-6">
                                    <input type="text" class="form-control datepicker" name="sales_valid_date"
                                        id="sales_valid_date" placeholder="Valid Date">
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-4 control-label">Status<span class="text-red">*</span></label>
                                <div class="col-sm-6">
                                    <select class="select3 form-control" name="sales_status" id="sales_status"
                                        data-placeholder="Form Status" style="width: 100%">
                                        <option value=""></option>
                                        <option value="Approval Price">Approval</option>
                                        <option value="Complete">Complete</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <div class="col-sm-12">
                                    <button class="btn btn-success pull-right" onclick="saveSales()"><i
                                            class="fa fa-check"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalExisting">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"
                        style="background-color: #ecf0f4; padding-top: 5px; padding-bottom: 5px; color: black;">
                        <center><b>Update Existing GMC</b></center>
                    </h4>
                    <div class="modal-body">
                        <div class="col-xs-12">
                            <center>
                                <h4 id="existing_id_form" style="font-weight: bold"></h4>
                            </center>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-4 control-label">Material Description<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="existing_item_desc"
                                        placeholder="Material Desc" readonly>
                                </div>
                            </div>

                            <input type="hidden" id="existing_id">
                            <input type="hidden" id="existing_material_id">

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-4 control-label">Existing GMC<span class="text-red">*</span></label>
                                <div class="col-sm-8">
                                    <select class="select5 form-control" name="existing_gmc" id="existing_gmc"
                                        data-placeholder="Select GMC" style="width: 100%" onchange="changeExistGmc()">
                                        <option value=""></option>
                                        @foreach ($mpdl as $row)
                                            <option
                                                value="{{ $row->material_number }}_#_{{ $row->sloc }}_#_{{ $row->valcl }}">
                                                {{ $row->material_number }} -
                                                {{ $row->material_description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-4 control-label">SLoc<span class="text-red">*</span></label>
                                <div class="col-sm-5">
                                    <select class="select6 form-control" name="existing_sloc" id="existing_sloc"
                                        data-placeholder="Select Sloc" style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($storage_location as $row)
                                            <option value="{{ $row->storage_location }}">{{ $row->storage_location }} -
                                                {{ $row->location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label class="col-sm-4 control-label">Remark<span class="text-red">*</span></label>
                                <div class="col-sm-5">
                                    <select class="select6 form-control" name="existing_remark" id="existing_remark"
                                        data-placeholder="Select Remark Material" style="width: 100%">
                                        <option value=""></option>
                                        <option value="REGULER">REGULER</option>
                                        <option value="NON REGULER">NON REGULER</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-xs-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <div class="col-sm-12">
                                    <button class="btn btn-success pull-right" onclick="saveExisting()"><i
                                            class="fa fa-check"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTrial">
        <div class="modal-dialog" style="width: 90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"
                        style="margin-top: 10px">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <center style="background-color: #00a65a;">
                        <h3 style="font-weight: bold; padding: 20px;margin:0; color: white" id="modalNewTitle">Create
                            Trial Request</h3>
                    </center>
                    <div class="row" style="margin-top: 10px">
                        <input type="hidden" id="id_edit">
                        <div class="col-md-6" style="padding-right: 0">
                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0;">
                                <label for="invoice_date" class="col-sm-3 control-label">Date<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-9">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right datepicker"
                                            value="<?= date('d M Y') ?>" placeholder="Submission Date" disabled>
                                        <input type="hidden" class="form-control pull-right datepicker"
                                            id="submission_date" name="submission_date" value="<?= date('Y-m-d') ?>"
                                            placeholder="Submission Date">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0;">
                                <label for="subject" class="col-sm-3 control-label">Subject<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control pull-right" id="subject" name="subject"
                                        placeholder="Trial Request Subject">
                                </div>
                            </div>

                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0;">
                                <label for="department" class="col-sm-3 control-label">Department<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-control select4" id="department" name="department"
                                        data-placeholder='Department To' style="width: 100%">
                                        <option value="">&nbsp;</option>
                                        @foreach ($dept as $dp)
                                            <option value="{{ $dp->department }}">{{ $dp->department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6" style="padding-left: 0">

                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0;">
                                <label for="requester" class="col-sm-3 control-label">Requester<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="Requester"
                                        value="{{ $emp->employee_id }} - {{ $emp->name }}" readonly="">
                                    <input type="hidden" class="form-control" id="requester" name="requester"
                                        placeholder="Requester" value="{{ $emp->employee_id }}">
                                    <input type="hidden" class="form-control" id="requester_name" name="requester_name"
                                        placeholder="Requester Name" value="{{ $emp->name }}">
                                </div>
                            </div>

                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0">
                                <label for="do_date" class="col-sm-3 control-label">Trial Date</label>
                                <div class="col-sm-9">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control pull-right datepicker" id="trial_date"
                                            name="trial_date" placeholder="Trial Date">
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0">
                                <label for="reference_no" class="col-sm-3 control-label">Reference No</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="reference_no" name="reference_no"
                                        placeholder="Reference Number">
                                    {{-- <select class="form-control select4" id="reference_no" name="reference_no"
                                        data-placeholder='Reference Number' style="width: 100%">
                                        <option value="">&nbsp;</option>
                                    </select> --}}
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12" style="padding-right:0px">
                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <hr style="margin-bottom: 10px; margin-top: 10px">
                                <label for="trial_purpose" class="col-sm-12 control-label">Trial Purpose<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="trial_purpose" name="trial_purpose" placeholder="Tujuan Dilakukan Trial"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" style="padding-right:0px">
                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0">
                                <label for="kondisi_sebelum" class="col-sm-12 control-label">Condition Before<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="kondisi_sebelum" name="kondisi_sebelum"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" style="padding-right:0px">
                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:0">
                                <label for="trial" class="col-sm-12 control-label">Trial Condition<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="trial" name="trial" placeholder="Trial"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7" style="padding-right:0px">
                            <div class="col-md-10" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">

                                <label for="trial_location" class="col-sm-12 control-label">Location<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="trial_location" name="trial_location"
                                        placeholder="Lokasi Trial"></textarea>
                                </div>
                            </div>

                            <div class="col-md-2" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label for="action" class="col-sm-12 control-label">Add</label>
                                <div class="col-sm-12">
                                    <button type="button" class="btn btn-md btn-success" onclick="add_mat()"><i
                                            class="fa fa-plus"></i> Add Material</button>
                                </div>
                            </div>

                        </div>


                        <div class="col-md-6" style="padding-right:0px">
                            <div class="col-md-12">
                                <table style="width: 100%; margin-left: 10px; margin-right: 10px">
                                    <thead>
                                        <tr>
                                            <td style="width: 65%"><label class="col-sm-12 control-label">Material<span
                                                        class="text-red">*</span></label></td>
                                            <td style="width: 15%"><label class="col-sm-12 control-label">Quantity<span
                                                        class="text-red">*</span></label></td>
                                            <td><label class="col-sm-12 control-label">Delete</label></td>
                                        </tr>
                                    </thead>
                                    <tbody id="body_mat">
                                        <tr>
                                            <td style="padding-right: 10px"><input type="text"
                                                    class="form-control mat" placeholder="Material" id="mat1"></td>
                                            <td style="padding-left: 10px"><input type="text" class="form-control qty"
                                                    placeholder="Quantity"></td>
                                            <td style="padding-left: 20px"><button class="btn btn-danger btn-sm"
                                                    onclick="deleteMat(this)"><i class="fa fa-minus"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{-- <div class="col-md-6" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label for="material1" class="col-sm-12 control-label">Material<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="material1" name="material1"
                                        placeholder="Material"></textarea>
                                </div>
                            </div>

                            <div class="col-md-4" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <label for="jumlah1" class="col-sm-12 control-label">Jumlah<span
                                        class="text-red">*</span></label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" id="jumlah1" name="jumlah1"
                                        placeholder="Contoh : 2 Pcs"></textarea>
                                </div>
                            </div> --}}
                        </div>



                        <div class="col-md-12" style="padding-right:0px">
                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">

                                <label for="trial_info" class="col-sm-12 control-label">Description /
                                    Specification</label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" id="trial_info" placeholder="Keterangan / Spesifikasi"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" style="padding-right:0px">
                            <div class="col-md-12" style="margin-bottom: 5px;padding-left:10px;padding-right:10px">
                                <span style="font-weight: bold; font-size: 16px;">
                                    <center style="background-color: #00a65a; line-height: 2">RECEIVER TRIAL REQUEST
                                    </center>
                                </span>
                                <div class="col-sm-12">
                                    <br>
                                    <button class="btn btn-success" onclick="add_penerima()"><i
                                            class="fa fa-plus"></i>&nbsp;
                                        Add Receiver Trial</button>
                                    <table class="table" style="width: 80%">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%">Department</th>
                                                <th style="width: 40%">Section</th>
                                                <th style="width: 20%">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody id="body_penerima">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12" style="margin-top: 5px;">
                            <a class="btn btn-success pull-right" onclick="SaveTrial('new')"
                                style="width: 100%; font-weight: bold; font-size: 1.5vw;" id="newButton"><i
                                    class="fa fa-check"></i> CREATE</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
    <script src="{{ url('ckeditor/ckeditor.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');


        var dept = <?php echo json_encode($dept); ?>;
        var section = <?php echo json_encode($section); ?>;

        var no_penerima = 1;

        $('#sales_price').keypress(function(event) {
            if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                event.preventDefault();
            }
        });

        jQuery(document).ready(function() {

            $('body').toggleClass("sidebar-collapse");

            $('.datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true,
            });

            $('.select2').select2({
                dropdownAutoWidth: true,
                allowClear: true,
                dropdownParent: $('#modalBom'),
            });


            $('.select4').select2({
                dropdownAutoWidth: true,
                allowClear: true,
                dropdownParent: $('#modalTrial'),
            });

            $('.select3').select2({
                dropdownAutoWidth: true,
                allowClear: true,
                dropdownParent: $('#modalSales'),
            });

            $('.select5').select2({
                dropdownAutoWidth: true,
                allowClear: true,
                minimumInputLength: 3,
                dropdownParent: $('#modalExisting'),
            });

            $('.select6').select2({
                dropdownAutoWidth: true,
                allowClear: true,
                dropdownParent: $('#modalExisting'),
            });


            CKEDITOR.replace('kondisi_sebelum', {
                filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}'
            });

            CKEDITOR.replace('trial', {
                filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}'
            });

            get_data();
        });

        function get_data() {

            $('#loading').show();
            $.get('{{ url('fetch/sakurentsu/list_material') }}', function(result, status, xhr) {
                $('#listTable').DataTable().clear();
                $('#listTable').DataTable().destroy();
                $("#listTableBody").empty();
                body = "";

                $.each(result.datas, function(key, value) {
                    body += "<tr id=" + value.id + ">";
                    body += '<td style="text-align: center;">' + (value.eo_number || '') + "</td>";

                    var style = 'text-align: center; ';
                    if (value.material_number == 'NEW') {
                        style += 'background-color: #ff8c98;';
                    }

                    body += '<td style="' + style + '">' + value.material_number + "</td>";
                    body += '<td style="text-align: center;">' + value.material_number_buyer + "</td>";
                    body += '<td style="text-align: left;">' + value.description + "</td>";
                    body += '<td style="text-align: center;">' + (value.uom || '-') + "</td>";
                    body += '<td style="text-align: center;">' + (value.storage_location || '-') + "</td>";

                    var style = '';
                    if (value.sales_price == null) {
                        style = 'background-color: #ff8c98';
                    } else if (value.status_price == 'Approval Price') {
                        style = 'background-color: #ffde85';
                    }

                    body += "<td style='text-align: right; " + style + "'>" + (value.sales_price || '') +
                        "</td>";
                    body += "<td style='" + style + "'>" + (value.valid_date || '') + "</td>";

                    if (value.attachment) {
                        body +=
                            '<td style="text-align: center;"><a href="#" class="btn btn-xs btn-primary"><i class="fa fa-file-archive-o"></i>&nbsp;&nbsp;Att.</a></td>';
                    } else {
                        body += '<td style="text-align: center;"> - </td>';
                    }

                    // if (value.reference_form_number) {
                    //     body += "<span class='label label-danger'>Trial : " + value.reference_form_number +
                    //         "</span>";
                    // }

                    // if (value.remark) {
                    //     body += "<span class='label label-primary'>Approval : " + value.remark + "</span>";
                    // }
                    if (value.remark == 'REGULER') {
                        body += '<td style="text-align: center; color: green; font-weight:bold;">';
                        body += value.remark;
                        body += '</td>';
                    } else if (value.remark == 'NON REGULER') {
                        body += '<td style="text-align: center; color: red; font-weight:bold;">';
                        body += value.remark;
                        body += '</td>';
                    } else {
                        body += '<td style="text-align: center; color: grey; font-weight:bold;">-</td>';
                    }

                    var status = '';
                    if (value.status_price == null) {
                        status = (value.status || '');
                    } else {
                        status = value.status_price;
                    }

                    body += "<td>" + status + "</td>";
                    body += '<td style="text-align: center">';

                    // if (value.material_number == 'NEW') {
                    //     body += "<button class='btn btn-danger btn-xs' onclick='openTrialModal(\"" + value
                    //         .description + "\",\"" + value.id + "\")' disabled>Trial Request</button><br>";
                    //     if ('{{ Auth::user()->username }}' == 'PI1106001' ||
                    //         '{{ Auth::user()->role_code }}'.includes('MIS') ||
                    //         '{{ Auth::user()->username }}' == 'PI1102002') {
                    //         body += "<button class='btn btn-primary btn-xs' onclick='openBOMModal(\"" +
                    //             value.description + "\",\"" + value.eo_number + "\",\"" + value.status +
                    //             "\", " + value.id + ")'>Upload BOM & Std Time</button><br>";
                    //     }
                    // }

                    if (('{{ Auth::user()->role_code }}'.includes('MIS') ||
                            '{{ Auth::user()->role_code }}'.includes('PC') ||
                            '{{ Auth::user()->role_code }}'.includes('PE')) &&
                        value.material_number == 'NEW') {

                        body += "<button class='btn btn-default btn-xs' ";
                        body += "onclick='openExistingModal(" + value.id + ")'>";
                        body += "<i class='fa fa-pencil-square-o'></i>&nbsp;&nbsp;Update GMC</button>";
                    }


                    if (value.material_number != 'NEW' &&
                        '{{ Auth::user()->role_code }}'.includes('MIS') ||
                        '{{ Auth::user()->role_code }}'.includes('PC')) {

                        body += "<button class='btn btn-success btn-xs'";
                        body += "onclick='openSalesModal(" + value.id + ")'>";
                        body += "<i class='fa fa-dollar'></i>&nbsp;&nbsp;Upload Price</button>";

                        body += "<button class='btn btn-primary btn-xs' ";
                        body += 'id="sync_' + value.material_number + '" onclick="syncGmc(id)">';
                        body += "<i class='fa fa-refresh'></i>&nbsp;&nbsp;Sync</button>";
                    }
                    body += "</td>";
                })
                $("#listTableBody").append(body);

                var table = $('#listTable').DataTable({
                    'dom': 'Bfrtip',
                    'responsive': true,
                    'lengthMenu': [
                        [10, 25, 50, -1],
                        ['10 rows', '25 rows', '50 rows', 'Show all']
                    ],
                    'buttons': {
                        buttons: [{
                            extend: 'pageLength',
                            className: 'btn btn-default',
                        }, ]
                    },
                    'paging': true,
                    'lengthChange': true,
                    'searching': true,
                    'ordering': true,
                    "order": [],
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true
                });

                $('#loading').hide();

            })
        }

        function syncGmc(id) {

            var material_number = id.split('_')[1];

            if (confirm(
                    "All descriptions of extra order's item whose status is still ``Confirming`` will be updated according to the description of this item. Are you sure to continue this process?"
                )) {

                var data = {
                    material_number: material_number,
                }

                $('#loading').show();
                $.post('{{ url('post/extra_order/existing_description') }}', data, function(result, status, xhr) {
                    if (result.status) {

                        $('#loading').hide();
                        openSuccessGritter('Success', result.message);

                    } else {

                        $('#loading').hide();
                        openErrorGritter('Error', result.message);

                    }
                });

            }

        }

        function openBOMModal(item_desc, form_number, remark, id) {
            $("#modalBom").modal('show');
            $("#bom_id").val(form_number);
            $("#item_desc").val(item_desc);
            $("#material_id").val(id);

            $("#id_form").html('Extra Order Form Number : <i class="fa fa-book"></i> ' + form_number);

            $.get('{{ url('adagio/cek/nomor_file/eo') }}', function(result, status, xhr) {
                $('#no_approval').val(result.no_appr);
            });


        }

        function openTrialModal(item_desc, form_id) {
            $("#modalTrial").modal('show');
            $("#mat1").val(item_desc);
            $("#id_eo").val(form_id);
            no_penerima = 1;
        }

        function openSalesModal(id) {
            var item_desc = $('#' + id).find('td').eq(3).text();
            var form_number = $('#' + id).find('td').eq(0).text();
            var material_number = $('#' + id).find('td').eq(1).text();

            $("#modalSales").modal('show');
            $("#sales_id").val(form_number);
            $("#sales_item_desc").val(item_desc);
            $("#sales_material_id").val(id);
            $("#sales_gmc").val(material_number);

            $("#sales_price").val('');
            $("#sales_valid_date").val('');
            $("#sales_status").val('').trigger('change.select2');

            $("#sales_id_form").html('Extra Order Form Number : <i class="fa fa-book"></i> ' + form_number);

        }

        function openExistingModal(id) {

            var item_desc = $('#' + id).find('td').eq(3).text();
            var form_number = $('#' + id).find('td').eq(0).text();
            var material_number = $('#' + id).find('td').eq(1).text();

            $("#modalExisting").modal('show');
            $("#existing_id").val(form_number);
            $("#existing_item_desc").val(item_desc);
            $("#existing_material_id").val(id);
            $("#existing_gmc").val('').trigger('change.select2');
            $("#existing_sloc").val('').trigger('change.select2');
            $("#existing_remark").val('').trigger('change.select2');

            $("#existing_id_form").html('Extra Order Form Number : <i class="fa fa-book"></i> ' + form_number);

        }

        function saveBom() {
            if (!$("#bom_file").val()) {
                openErrorGritter("Failed", "Please Select File");
                return false;
            }

            if ($("#bom_gmc").val() == '') {
                openErrorGritter("Failed", "Please complete all fields");
                return false;
            }

            if ($("#bom_dept").val() == '') {
                openErrorGritter("Failed", "Please add department");
                return false;
            }


            $('#loading').show();

            var myFormData = new FormData();
            myFormData.append('bom_file', $("#bom_file").prop('files')[0]);
            myFormData.append('id', $("#bom_id").val());
            myFormData.append('id_material', $("#material_id").val());
            myFormData.append('gmc', $("#bom_gmc").val());
            myFormData.append('qty', $("#bom_qty").val());
            myFormData.append('std_time', $("#bom_std_time").val());
            myFormData.append('total', $("#bom_total").val());
            myFormData.append('dept', $("#bom_dept").val());

            $.ajax({
                url: '{{ url('post/sakurentsu/trial_request/bom') }}',
                type: 'POST',
                processData: false,
                contentType: false,
                dataType: 'json',
                data: myFormData,
                success: function(jsonData) {
                    $('#loading').hide();
                    openSuccessGritter('Success', 'Successfully Creating BOM & Standard Time');
                    $("#modalBom").modal('hide');
                    get_data();
                }
            });
        }

        function changeExistGmc() {

            $("#existing_sloc").prop('selectedIndex', 0).change();

            if ($('#existing_gmc').val() != '') {

                var material_number = $('#existing_gmc').val().split('_#_')[0];
                var sloc = $('#existing_gmc').val().split('_#_')[1];
                var valcl = $('#existing_gmc').val().split('_#_')[2];

                if (valcl == '9010') {
                    $("#existing_sloc").val(sloc).trigger('change.select2');
                }

            }

        }

        function saveExisting() {

            if ($("#existing_gmc").val() == '') {
                openErrorGritter("Failed", "Please complete all fields");
                return false;
            }

            if ($("#existing_sloc").val() == '') {
                openErrorGritter("Failed", "Please complete all fields");
                return false;
            }

            if ($("#existing_remark").val() == '') {
                openErrorGritter("Failed", "Please complete all fields");
                return false;
            }

            var existing_id = $('#existing_id').val();
            var existing_material_id = $('#existing_material_id').val();
            var existing_gmc = $('#existing_gmc').val().split('_#_')[0];
            var sloc = $('#existing_sloc').val();
            var valcl = $('#existing_gmc').val().split('_#_')[2];
            var remark = $('#existing_remark').val();

            var data = {
                existing_id: existing_id,
                existing_material_id: existing_material_id,
                existing_gmc: existing_gmc,
                remark: remark,
                sloc: sloc,
                valcl: valcl,
            }

            $.post('{{ url('post/extra_order/existing_material') }}', data, function(result, status, xhr) {

                if (result.status) {

                    get_data();
                    $("#modalExisting").modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success', 'Successfully Creating BOM & Standard Time');

                } else {

                    $('#loading').hide();
                    openErrorGritter('Error', result.message);

                }
            });
        }



        function saveSales() {
            if (!$("#sales_file").val()) {
                openErrorGritter("Failed", "Please Select File");
                return false;
            }

            if ($("#sales_price").val() == '' || $("#sales_valid_date").val() == '' || $("#sales_status").val() == '') {
                openErrorGritter("Failed", "Please complete all fields");
                return false;
            }

            $('#loading').show();

            var myFormData = new FormData();
            myFormData.append('sales_file', $("#sales_file").prop('files')[0]);
            myFormData.append('gmc_material', $("#sales_gmc").val());
            myFormData.append('price', $("#sales_price").val());
            myFormData.append('valid_date', $("#sales_valid_date").val());
            myFormData.append('status', $("#sales_status").val());

            $.ajax({
                url: '{{ url('post/sakurentsu/trial_request/sales_price') }}',
                type: 'POST',
                processData: false,
                contentType: false,
                dataType: 'json',
                data: myFormData,
                success: function(jsonData) {
                    $('#loading').hide();
                    openSuccessGritter('Success', 'Successfully Upload Sales Price');
                    $("#modalSales").modal('hide');
                    get_data();
                }
            });
        }

        function SaveTrial(status) {
            if ($("#submission_date").val() == "" || $('#subject').val() == null || $('#department').val() == null ||
                CKEDITOR.instances.kondisi_sebelum.getData() == "" || CKEDITOR.instances.trial.getData() == "" || $(
                    '#requester').val() == "" || $('#trial_purpose').val() == "" || $('#material1').val() == "" || $(
                    '#jumlah1').val() == "" || $('#trial_location').val() == "") {

                $('#loading').hide();
                openErrorGritter('Error', "Please fill field with (*) sign.");
                return false;
            }

            if ($("#mat1").val() == '') {
                $('#loading').hide();
                openErrorGritter('Error', "Please Add Material");
                return false;
            }

            if ($("#qty1").val() == '') {
                $('#loading').hide();
                openErrorGritter('Error', "Please Add Quatity of Material");
                return false;
            }

            if ($("#mat1").val() == '') {
                $('#loading').hide();
                openErrorGritter('Error', "Please Add Material");
                return false;
            }

            if ($('.dept').length < 1) {
                $('#loading').hide();
                openErrorGritter('Error', "Please Add Penerima");
                return false;
            }

            var mat_arr = [];
            $('.mat').each(function(index, value) {
                mat_arr.push($(this).val());
            });

            var qty_arr = [];
            $('.qty').each(function(index, value) {
                qty_arr.push($(this).val());
            });

            var dept_arr = [];
            $('.dept').each(function(index, value) {
                if ($(this).val() == '') {
                    $('#loading').hide();
                    openErrorGritter('Error', "Please Add Department");
                    return false;
                }

                dept_arr.push($(this).val());
            });

            var sec_arr = [];
            $('.sec').each(function(index, value) {
                if ($(this).val() == '') {
                    $('#loading').hide();
                    openErrorGritter('Error', "Please Add Section");
                    return false;
                }

                sec_arr.push($(this).val());
            });

            var formData = new FormData();
            formData.append('submission_date', $("#submission_date").val());
            formData.append('subject', $("#subject").val());
            formData.append('department', $("#department").val());
            formData.append('kondisi_sebelum', CKEDITOR.instances.kondisi_sebelum.getData());
            formData.append('requester', $("#requester").val());
            formData.append('requester_name', $("#requester_name").val());
            formData.append('trial_date', $("#trial_date").val());
            formData.append('reference_no', $("#reference_no").val());
            formData.append('trial', CKEDITOR.instances.trial.getData());
            formData.append('trial_purpose', $("#trial_purpose").val());
            formData.append('trial_location', $("#trial_location").val());
            formData.append('trial_info', $("#trial_info").val());
            formData.append('extra_order', $("#id_eo").val());

            formData.append('material', mat_arr);
            formData.append('jumlah', qty_arr);

            formData.append('department_receive', dept_arr);
            formData.append('section_receive', sec_arr);

            $.ajax({
                url: "{{ url('create/trial_request') }}",
                method: "POST",
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    if (data.status) {
                        openSuccessGritter('Success', data.message);
                        audio_ok.play();
                        $('#loading').hide();
                        // $('#modalNew').modal('hide');
                        // clearNew();
                        // fetchTable();
                    } else {
                        openErrorGritter('Error!', data.message);
                        $('#loading').hide();
                        audio_error.play();
                    }

                }
            });
        }

        function sum_total() {
            var qty = $("#bom_qty").val();
            var std_time = $("#bom_std_time").val();

            var total = qty * std_time;

            $("#bom_total").val(total);
        }

        function add_mat() {
            var body = "";

            body += '<tr>';
            body +=
                '<td style="padding-right: 10px"><input type="text" class="form-control mat" placeholder="Material"></td>';
            body +=
                '<td style="padding-left: 10px"><input type="text" class="form-control qty" placeholder="Quantity" value="1"></td>';
            body +=
                '<td style="padding-left: 20px"><button class="btn btn-danger btn-sm" onclick="deleteMat(this)"><i class="fa fa-minus"></i></button></td>';
            body += '</tr>';

            $("#body_mat").append(body);
        }

        function add_penerima() {
            var body = "";

            var option_dept = "";

            $.each(dept, function(key, value) {
                option_dept += "<option value='" + value.department + "'>" + value.department + "</option>";
            })

            body += '<tr>';
            body +=
                '<td style="padding-right: 10px"><select type="text" class="form-control select4 dept" data-placeholder="Pilih Departemen" onchange="select_section(this,' +
                no_penerima + ')" id="dept_' + no_penerima + '" ><option value=""></option>' + option_dept +
                '</select></td>';
            body += '<td style="padding-left: 10px"><select class="form-control select4 sec" id="sec_' + no_penerima +
                '" data-placeholder="Pilih Section"><option value=""></option></select></td>';
            body +=
                '<td style="padding-left: 20px"><button class="btn btn-danger btn-sm" onclick="deleteMat(this)"><i class="fa fa-minus"></i></button></td>';
            body += '</tr>';

            $("#body_penerima").append(body);

            $('.select4').select2({
                dropdownAutoWidth: true,
                allowClear: true
            });

            no_penerima++;
        }

        function select_section(elem, no) {
            var isi = $(elem).val();
            var option_sec = "";

            $('#sec_' + no).empty();
            $('#sec_' + no).append("<option value=''></option>");

            $.each(section, function(key, value) {
                if (value.department == isi) {
                    option_sec += "<option value='" + value.section + "'>" + value.section + "</option>";
                }
            });

            $('#sec_' + no).append(option_sec);
        }

        function deleteMat(elem) {
            $(elem).closest('tr').remove();
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
@endsection
