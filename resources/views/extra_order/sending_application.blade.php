@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <style type="text/css">
        #tableDetail>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        tbody>tr>td {
            padding: 10px 5px 10px 5px;
        }

        table.table-bordered {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            vertical-align: middle;
            height: 40px;
            padding: 2px 5px 2px 5px;
        }

        .contr #loading {
            display: none;
        }

        .label-status {
            color: black;
            font-size: 0.8vw;
            border-radius: 4px;
            padding: 3px 10px 5px 10px;
            border: 1.5px solid black;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <input id="role_code" value="{{ Auth::user()->role_code }}" hidden>
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-header">
                        <h3 class="box-title">Sending Application Filter<span class="text-purple"></span></h3>
                    </div>
                    <div class="box-body">
                        <div class="col-xs-3 col-xs-offset-2" style="padding: 0px;">
                            <div class="box box-primary box-solid" style="margin: 0px;">
                                <div class="box-body">
                                    <div class="col-xs-6" style="padding-left: 0px; padding-right: 2px;">
                                        <div class="form-group">
                                            <label>Issue From</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <input type="text" placeholder="Select Issue Date"
                                                    class="form-control datepicker pull-right" id="issue_from">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="padding-left: 2px; padding-right: 0px;">
                                        <div class="form-group">
                                            <label>Issue To</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <input type="text" placeholder="Select Issue Date"
                                                    class="form-control datepicker pull-right" id="issue_to">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="padding-left: 0px; padding-right: 2px;">
                                        <div class="form-group">
                                            <label>Stuffing From</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <input type="text" placeholder="Select Stuffing Date"
                                                    class="form-control datepicker pull-right" id="stuffing_from">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-6" style="padding-left: 2px; padding-right: 0px;">
                                        <div class="form-group">
                                            <label>Stuffing To</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <input type="text" placeholder="Select Stuffing Date"
                                                    class="form-control datepicker pull-right" id="stuffing_to">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-5" style="padding-right: 0px;">
                            <div class="box box-primary box-solid" style="margin: 0px;">
                                <div class="box-body">
                                    <div class="col-xs-12">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label>Status</label>
                                                    <select class="form-control select2" multiple="multiple"
                                                        data-placeholder="Select Status" id="search_status"
                                                        style="width: 100%;">
                                                        <option value="1">Requested</option>
                                                        <option value="3">Checked by WH</option>
                                                        <option value="4">Scheduled</option>
                                                        <option value="5">Delivered</option>
                                                        <option value="6">Complete</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label>Payment Term</label>
                                                    <select class="form-control select2" multiple="multiple"
                                                        data-placeholder="Select Payment Term" id="search_payment_term"
                                                        style="width:100%">
                                                        <option></option>
                                                        @foreach ($payment_terms as $payment_term)
                                                            <option value="{{ $payment_term }}">{{ $payment_term }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label>Destination</label>
                                                    <select class="form-control select2" multiple="multiple"
                                                        data-placeholder="Select Destination" id="search_destination"
                                                        style="width: 100%;">
                                                        <option value=""></option>
                                                        @foreach ($destinations as $dt)
                                                            <option value="{{ $dt->destination_code }}">
                                                                {{ $dt->destination_shortname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="form-group">
                                                    <label>Ship By</label>
                                                    <select class="form-control select2" multiple="multiple"
                                                        data-placeholder="Select Ship By" id="search_ship_by"
                                                        style="width: 100%;">
                                                        <option></option>
                                                        @foreach ($shipment_conditions as $shipment_condition)
                                                            <option value="{{ $shipment_condition }}">
                                                                {{ $shipment_condition }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-8 col-xs-offset-2" style="margin-top: 0.75%;">
                            <div class="form-group pull-right" style="margin: 0px;">
                                <button onclick="clearConfirmation()"
                                    class="btn btn-danger">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Clear&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                <button onclick="showTable()" class="btn btn-primary"><span class="fa fa-search"></span>
                                    Search</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (str_contains(Auth::user()->role_code, 'PC') || str_contains(Auth::user()->role_code, 'MIS'))
            <div class="col-xs-12">
                <div class="form-group pull-right">
                    <a data-toggle="modal" data-target="#modalCreate" class="btn btn-success" style="color:white">
                        &nbsp;<i class="fa fa-plus"></i>&nbsp;&nbsp;&nbsp;Create Send App
                    </a>
                </div>
            </div>
        @endif

        <div class="col-xs-12">
            <table class="table table-bordered table-striped table-hover" id="tableDetail" width="100%"
                style="font-size: 0.85vw;">
                <thead style="background-color: #605ca8; color: white;">
                    <tr>
                        <th style="text-align: center;">Send App No.</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center;">EO No.</th>
                        <th style="text-align: center;">Issue Date</th>
                        <th style="text-align: center;">Attention</th>
                        <th style="text-align: center;">Condition</th>
                        <th style="text-align: center;">Ship By</th>
                        <th style="text-align: center;">Payment Term</th>
                        <th style="text-align: center;">Checksheet</th>
                        <th style="text-align: center;">IV No.</th>
                        <th style="text-align: center;">St Date</th>
                        <th style="text-align: center;">Bl Date</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody id="tableDetailBody">
                </tbody>
                <tfoot style="background-color: RGB(252, 248, 227);">
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
        </div>

    </section>
    <div class="modal fade" id="modalCreate" data-keyboard="false" data-backdrop="static" style="overflow-y: auto;">
        <div class="modal-dialog modal-lg" style="width: 65%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #605ca8; font-weight: bold; padding: 1%; margin-top: 0; color: white;">
                            CREATE SENDING APPLICATION
                        </h2>
                    </center>

                    <div class="col-xs-12" style="margin-bottom: 3%;">
                        <div class="col-xs-6 col-xs-offset-3">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-left: 0; text-align: left;" for=""
                                        class="col-xs-12 control-label">
                                        EO Number </span>
                                    </label>
                                    <div class="col-xs-12" style="padding-left: 0px;">
                                        <select class="form-control select2" id="create_eo_number"
                                            data-placeholder="Select EO Number" style="width: 100%;"
                                            onchange="fillEoData(value)">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                    Destination<span class="text-red">*</span>:
                                </label>
                                <div class="col-xs-8" style="padding-left: 0px;">
                                    <input class="form-control" type="text" id="create_destination" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                    Recipient<span class="text-red">*</span>:
                                </label>
                                <div class="col-xs-8" style="padding-left: 0px;">
                                    <input class="form-control" type="text" id="create_attention" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                    Division<span class="text-red"> :</span>
                                </label>
                                <div class="col-xs-8" style="padding-left: 0px;">
                                    <textarea class="form-control" type="text" rows="3" id="create_division" readonly></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-6">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                    Ship By <span class="text-red">*</span>:
                                </label>
                                <div class="col-xs-5" style="padding-left: 0px;">
                                    <select class="form-control select2" name="create_shipment_by"
                                        id="create_shipment_by" data-placeholder="Select Ship By" style="width: 100%;">
                                        <option></option>
                                        @foreach ($shipment_conditions as $shipment_condition)
                                            <option value="{{ $shipment_condition }}">{{ $shipment_condition }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                    Payment Term <span class="text-red">*</span>:
                                </label>
                                <div class="col-xs-5" style="padding-left: 0px;">
                                    <select class="form-control select2" id="create_payment_term"
                                        data-placeholder="Select Payment Term" style="width:100%;">
                                        <option></option>
                                        @foreach ($payment_terms as $payment_term)
                                            <option value="{{ $payment_term }}">{{ $payment_term }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                    Freight <span class="text-red">*</span>:
                                </label>
                                <div class="col-xs-5" style="padding-left: 0px;">
                                    <select class="form-control select2" id="create_freight"
                                        data-placeholder="Select Freight" style="width: 100%;">
                                        <option></option>
                                        @foreach ($freights as $freight)
                                            <option value="{{ $freight }}">{{ $freight }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                    Condition<span class="text-red">*</span>:
                                </label>
                                <div class="col-xs-5" style="padding-left: 0px;">
                                    <select class="form-control select2" id="create_condition"
                                        data-placeholder="Select Condition" style="width: 100%;">
                                        <option></option>
                                        <option value="URGENT">URGENT</option>
                                        <option value="NORMAL">NORMAL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                    CITES Regulation<span class="text-red">*</span>:
                                </label>
                                <div class="col-xs-5" style="padding-left: 0px;">
                                    <select class="form-control select2" id="create_regulation"
                                        data-placeholder="Select Regulation" style="width: 100%;">
                                        <option></option>
                                        <option value="Need CITES Regulation">NEED</option>
                                        <option value="No Need CITES Regulation">NO NEED</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-10 col-xs-offset-1" style="margin-top: 3%;">
                        <p id="selected_extra_order" style="font-size: 1.2vw;">Selected Extra Order : </p>
                        <p style="font-size: 1.2vw;">List Material</p>
                        <div class="box box-primary">
                            <div class="box-body">
                                <table class="table table-hover table-bordered table-striped" id="tableList">
                                    <thead style="background-color: rgba(126,86,134,.7);">
                                        <tr>
                                            <th style="width: 10%; text-align: center;">Sequence</th>
                                            <th style="width: 10%; text-align: center;">Material</th>
                                            <th style="width: 45%; text-align: center;">Description</th>
                                            <th style="width: 5%; text-align: center;">Uom</th>
                                            <th style="width: 10%; text-align: center;">Qty FSTK</th>
                                            <th style="width: 10%; text-align: center;">Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBodyList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close"
                        style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back</button>
                    <button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                        onclick="submitSendApp()">SUBMIT</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetail" data-keyboard="false" style="overflow-y: auto;">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #f2f2f2; font-weight: bold; padding: 1%; margin-top: 0; color: black; border: 1px solid black;">
                            DETAIL SENDING APPLICATION
                        </h2>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Send App No.<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="detail_send_app_no" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Destination<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="detail_destination" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Recipient<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="detail_attention" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Division<span class="text-red"> :</span>
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <textarea class="form-control" type="text" rows="3" id="detail_division" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-6">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Ship By <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="detail_shipment_by" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Payment Term <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="detail_payment_term" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Freight <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="detail_freight" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Condition<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="detail_condition" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Regulation<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="detail_regulation" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-8 col-xs-offset-2" style="margin-top: 3%;">
                            <table class="table table-hover table-bordered" id="tabelTimeline">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 25%; text-align: center;">Applicant</th>
                                        <th style="width: 25%; text-align: center;">Packing Check 1</th>
                                        <th style="width: 25%; text-align: center;">Packing Check 2</th>
                                        <th style="width: 25%; text-align: center;">Shipment Schedule</th>
                                    </tr>
                                </thead>
                                <tbody id="bodyTimeline">
                                </tbody>
                            </table>
                        </div>

                        <div class="col-xs-12" style="margin-top: 3%;">
                            <p style="font-size: 1.2vw;">Selected Extra Order : <span
                                    id="detail_selected_extra_order"></span></p>
                            <p style="font-size: 1.2vw;">List Material</p>
                            <div class="box box-primary">
                                <div class="box-body">
                                    <table class="table table-hover table-bordered table-striped" id="tabelDetailSendApp"
                                        style="font-size: 10pt;">
                                        <thead style="background-color: rgba(126,86,134,.7);">
                                            <tr>
                                                <th style="width: 10%; text-align: center;">PO Number</th>
                                                <th style="width: 10%; text-align: center;">Sequence</th>
                                                <th style="width: 10%; text-align: center;">Material</th>
                                                <th style="width: 45%; text-align: center;">Description</th>
                                                <th style="width: 5%; text-align: center;">Uom</th>
                                                <th style="width: 10%; text-align: center;">Qty</th>
                                                <th style="width: 10%; text-align: center;">Price</th>
                                                <th style="width: 10%; text-align: center;">Amount</th>
                                                <th style="width: 5%; text-align: center;">Pkg No.</th>
                                                <th style="width: 5%; text-align: center;">Pkg Type</th>
                                                <th style="width: 5%; text-align: center;">Length (cm)</th>
                                                <th style="width: 5%; text-align: center;">Width (cm)</th>
                                                <th style="width: 5%; text-align: center;">Height (cm)</th>
                                                <th style="width: 5%; text-align: center;">Weight (kg)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyDetailSendApp">
                                            <tr>
                                                <th></th>
                                            </tr>
                                        </tbody>
                                        <tfoot style="background-color: antiquewhite;">
                                            <tr>
                                                <th colspan="7" style="text-align: right">Total Amount</th>
                                                <th id="total_amount" style="font-weight: bold;"></th>
                                                <th colspan="6"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEdit" data-keyboard="false" style="overflow-y: auto;">
        <div class="modal-dialog modal-lg" style="width: 65%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #ffc10c; font-weight: bold; padding: 1%; margin-top: 0; color: black; border: 1px solid black;">
                            EDIT SENDING APPLICATION
                        </h2>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Send App No.<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="edit_send_app_no" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Destination<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="edit_destination" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Recipient<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="edit_attention" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Division<span class="text-red"> :</span>
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <textarea class="form-control" type="text" rows="3" id="edit_division" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-6">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Ship By <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <select class="form-control select2" id="edit_shipment_by"
                                            data-placeholder="Select Ship By" style="width: 100%;">
                                            <option></option>
                                            @foreach ($shipment_conditions as $shipment_condition)
                                                <option value="{{ $shipment_condition }}">{{ $shipment_condition }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Payment Term <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <select class="form-control select2" id="edit_payment_term"
                                            data-placeholder="Select Payment Term" style="width:100%;">
                                            <option></option>
                                            @foreach ($payment_terms as $payment_term)
                                                <option value="{{ $payment_term }}">{{ $payment_term }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Freight <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <select class="form-control select2" id="edit_freight"
                                            data-placeholder="Select Freight" style="width: 100%;">
                                            <option></option>
                                            @foreach ($freights as $freight)
                                                <option value="{{ $freight }}">{{ $freight }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Condition<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <select class="form-control select2" id="edit_condition"
                                            data-placeholder="Select Condition" style="width: 100%;">
                                            <option></option>
                                            <option value="URGENT">URGENT</option>
                                            <option value="NORMAL">NORMAL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12" style="margin-top: 3%;">
                            <p style="font-size: 1.2vw;">List Material</p>
                            <div class="box box-primary">
                                <div class="box-body">
                                    <table class="table table-hover table-bordered table-striped" id="tabelEditSendApp">
                                        <thead style="background-color: rgba(126,86,134,.7);">
                                            <tr>
                                                <th style="width: 10%; text-align: center;">PO Number</th>
                                                <th style="width: 10%; text-align: center;">Sequence</th>
                                                <th style="width: 10%; text-align: center;">Material</th>
                                                <th style="width: 45%; text-align: center;">Description</th>
                                                <th style="width: 5%; text-align: center;">Uom</th>
                                                <th style="width: 10%; text-align: center;">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyEditSendApp">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close"
                        style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back</button>
                    <button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                        onclick="submitEdit()">SUBMIT</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalMeasure" data-keyboard="false" style="overflow-y: auto;">
        <div class="modal-dialog modal-lg" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #ecff7b; font-weight: bold; padding: 1%; margin-top: 0; color: black; border: 1px solid black;">
                            MATERIAL MEASUREMENT
                        </h2>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Send App No.<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="measure_send_app_no" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Destination<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="measure_destination" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Recipient<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="measure_attention" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                        Division<span class="text-red"> :</span>
                                    </label>
                                    <div class="col-xs-8" style="padding-left: 0px;">
                                        <textarea class="form-control" type="text" rows="3" id="measure_division" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-6">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Ship By <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="measure_shipment_by" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Payment Term <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="measure_payment_term" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Freight <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="measure_freight" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-5 control-label">
                                        Condition<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-5" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="measure_condition" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-2 control-label">
                                        PIC Check 1<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-2" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="tag_check_1"
                                            placeholder="Tap ID Card">
                                    </div>
                                    <div class="col-xs-4" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="pic_check_1" readonly>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-2 control-label">
                                        PIC Check 2<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-2" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="tag_check_2"
                                            placeholder="Tap ID Card">
                                    </div>
                                    <div class="col-xs-4" style="padding-left: 0px;">
                                        <input class="form-control" type="text" id="pic_check_2" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12" style="margin-top: 3%;">
                            <p style="font-size: 1.2vw;">List Material</p>
                            <div class="box box-primary">
                                <div class="box-body">
                                    <table class="table table-hover table-bordered table-striped" id="tabelMeasure">
                                        <thead style="background-color: rgba(126,86,134,.7);">
                                            <tr>
                                                <th style="width: 5%; text-align: center;">Sequence</th>
                                                <th style="width: 5%; text-align: center;">Material</th>
                                                <th style="width: 50%; text-align: center;">Description</th>
                                                <th style="width: 5%; text-align: center;">Uom</th>
                                                <th style="width: 5%; text-align: center;">Qty</th>
                                                <th style="width: 5%; text-align: center;">Pkg No.</th>
                                                <th style="width: 5%; text-align: center;">Pkg Type</th>
                                                <th style="width: 5%; text-align: center;">Length (cm)</th>
                                                <th style="width: 5%; text-align: center;">Width (cm)</th>
                                                <th style="width: 5%; text-align: center;">Height (cm)</th>
                                                <th style="width: 5%; text-align: center;">Weight (kg)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bodyMeasure">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close"
                        style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back</button>
                    <button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                        onclick="submitMeasure()">SUBMIT</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalShipDoc" data-keyboard="false" data-backdrop="static" style="overflow-y: auto;">
        <div class="modal-dialog modal-lg" style="width: 30%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #ccffff; font-weight: bold; padding: 1%; margin-top: 0; color: black; border: 1px solid black;">
                            SHIPMENT DOC.
                        </h2>
                    </center>
                </div>
                <div class="modal-body">
                    <center>
                        <h4><i class="fa fa-book"></i>&nbsp;<span id="ship_send_app_no"></span></h4>
                    </center>
                    <div class="col-xs-12">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                    Checksheet ID <span class="text-red">*</span>:
                                </label>
                                <div class="col-xs-6" style="padding-left: 0px;">
                                    <input type="text" id="container_id" class="form-control"
                                        placeholder="Input checksheet ID ...">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="form-horizontal">
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-xs-4 control-label">
                                    IV No. <span class="text-red">*</span>:
                                </label>
                                <div class="col-xs-6" style="padding-left: 0px;">
                                    <input type="text" id="ship_iv" class="form-control"
                                        placeholder="Input Invoice No ...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-xs-12">
                        <button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close"
                            style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back</button>
                        <button class="btn btn-success pull-right"
                            style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                            onclick="submitShipDoc()">SUBMIT</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalComplete" data-keyboard="false" data-backdrop="static" style="overflow-y: auto;">
        <div class="modal-dialog modal-lg" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #00cc6e; font-weight: bold; padding: 1%; margin-top: 0; color: black; border: 1px solid black;">
                            COMPLETE
                        </h2>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <center>
                                <h2 style="margin-top: 0px; margin-bottom: 3%; font-weight:bold;"><i
                                        class="fa fa-book"></i>&nbsp;<span id="complete_send_app_no"></span></h2>
                            </center>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-3 control-label">
                                        Bl Date <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-6" style="padding-left: 0px;">
                                        <input type="text" id="bl_date" class="form-control datepicker"
                                            placeholder="Select date ...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-3 control-label">
                                        IV Number <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-6" style="padding-left: 0px;">
                                        <input type="text" id="invoice_number" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-3 control-label">
                                        IV File <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-6" style="padding-left: 0px;">
                                        <input type="file" id="invoice_file" accept="application/pdf">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-7 col-xs-offset-3" id="divEmbedInvoice">
                            <embed id="invoiceViewer" width='100%' height='300px'>
                        </div>
                        <div class="col-xs-12" style="margin-bottom: 5%;">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-3 control-label">
                                        Checklist <span class="text-red">*</span>:
                                    </label>
                                    <label style="color: #199fe0; font-weight: bold;">
                                        <input type="checkbox" class="minimal" id="invoice_checklist">
                                        &nbsp;Invoice number already match with invoice document
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-3 control-label">
                                        Way Bill<span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-6" style="padding-left: 0px;">
                                        <input type="text" id="way_bill" class="form-control"
                                            placeholder="Input way bill ...">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-3 control-label">
                                        Way Bill File <span class="text-red">*</span>:
                                    </label>
                                    <div class="col-xs-6" style="padding-left: 0px;">
                                        <input type="file" id="way_bill_file" accept="application/pdf">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-7 col-xs-offset-3" id="divEmbedWayBill">
                            <embed id="wayBillViewer" width='100%' height='300px'>
                        </div>
                        <div class="col-xs-12" style="margin-bottom: 5%;">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-xs-3 control-label">
                                        Checklist <span class="text-red">*</span>:
                                    </label>
                                    <label style="color: #199fe0; font-weight: bold;">
                                        <input type="checkbox" class="minimal" id="way_bill_checklist">
                                        &nbsp;Way bill number already match with way bill document
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-xs-12">
                        <button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close"
                            style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back</button>
                        <button class="btn btn-success pull-right"
                            style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                            onclick="submitComplete()">SUBMIT</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDelete" data-keyboard="false" data-backdrop="static" style="overflow-y: auto;">
        <div class="modal-dialog modal-xs">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h2
                            style="background-color: #ffa89b; font-weight: bold; padding: 1%; margin-top: 0; color: black; border: 1px solid black;">
                            DELETE SEND APP
                        </h2>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <input type="text" name="delete_send_app_no" id="delete_send_app_no" hidden>

                        <div class="col-xs-10 col-xs-offset-1">
                            <label for="" class="col-xs-12 control-label">
                                Reason<span class="text-red"> :</span>
                            </label>
                            <div class="col-xs-12">
                                <textarea class="form-control" type="text" rows="3" id="delete_reason"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-xs-12">
                        <button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close"
                            style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back</button>
                        <button class="btn btn-success pull-right"
                            style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                            onclick="deleteSendApp()">SUBMIT</button>
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
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });

            $('.select2').select2();

            showTable();
        });

        var extra_order = [];
        var extra_order_stock = [];

        var attention = '';
        var selected_extra_order = [];
        var selected_extra_order_detail = [];


        function clearConfirmation() {
            location.reload(true);
        }

        $('#modalCreate').on('shown.bs.modal', function() {
            $('#tableBodyList').html("");
            attention = '';
            selected_extra_order = [];
            selected_extra_order_detail = [];

            refreshStock();

        });

        function submitSendApp() {

            var ship_by = $("#create_shipment_by").val();
            var payment_term = $("#create_payment_term").val();
            var freight = $("#create_freight").val();
            var condition = $("#create_condition").val();
            var regulation = $("#create_regulation").val();

            if (selected_extra_order_detail.length <= 0 || selected_extra_order.length <= 0 || ship_by == '' ||
                payment_term == '' || freight == '' || condition == '' || regulation == '') {
                openErrorGritter('Error!', 'Fill all data');
                return false;
            }

            var data = {
                ship_by,
                payment_term,
                freight,
                condition,
                regulation,
                extra_order: selected_extra_order,
                sequence: selected_extra_order_detail
            }


            $('#loading').show();
            $.post('{{ url('input/extra_order/input_sending_application') }}', data, function(result, status, xhr) {
                if (result.status) {
                    showTable()
                    $('#modalCreate').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success', result.message);

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);

                }
            });


        }

        function remExtraOrder(eo_number_sequence) {

            // Remove Detail
            for (var i = (selected_extra_order_detail.length - 1); i >= 0; i--) {
                if (selected_extra_order_detail[i].eo_number_sequence == eo_number_sequence) {
                    selected_extra_order_detail.splice(i, 1);
                }
            }
            $("#stock_" + eo_number_sequence).remove();


            // Remove EO
            var eo_number = eo_number_sequence.split('-')[0];
            var exist = false;

            for (var i = (selected_extra_order_detail.length - 1); i >= 0; i--) {
                if (selected_extra_order_detail[i].eo_number == eo_number) {
                    exist = true;
                }
            }

            if (!exist) {
                for (var i = (selected_extra_order.length - 1); i >= 0; i--) {
                    if (selected_extra_order[i] == eo_number) {
                        selected_extra_order.splice(i, 1);
                    }
                }
                $("#label-" + eo_number).remove();
            }
        }

        function fillEoData(eo_number) {

            if (eo_number.length > 0) {
                if (!selected_extra_order.includes(eo_number)) {

                    for (var i = 0; i < extra_order_stock.length; i++) {
                        if (extra_order_stock[i].eo_number == eo_number) {

                            if (selected_extra_order.length > 0) {
                                if (attention != extra_order_stock[i].attention) {
                                    $("#create_eo_number").prop('selectedIndex', 0).change();
                                    openErrorGritter('Error!', "Recipient of the selected extra order does not match");
                                    return false;
                                }
                            }

                            attention = extra_order_stock[i].attention;
                            $("#create_destination").val(extra_order_stock[i].destination_name);
                            $("#create_attention").val(extra_order_stock[i].attention);
                            $("#create_division").val(extra_order_stock[i].division);
                            break;
                        }
                    }

                    selected_extra_order.push(eo_number);
                    var label = '<span style="margin-left: 1%;" class="label label-primary" id="label-' + eo_number + '">' +
                        eo_number + '</span>';
                    $('#selected_extra_order').append(label);

                    var tableData = '';
                    for (var i = 0; i < extra_order_stock.length; i++) {
                        if (extra_order_stock[i].eo_number == eo_number) {
                            tableData += '<tr id="stock_' + extra_order_stock[i].eo_number_sequence + '">';

                            tableData += '<td style="text-align: center;">';
                            tableData += extra_order_stock[i].eo_number_sequence;
                            tableData += '</td>';

                            tableData += '<td style="text-align: center;">';
                            tableData += extra_order_stock[i].material_number;
                            tableData += '</td>';

                            tableData += '<td style="text-align: left;">' + extra_order_stock[i].description + '</td>';
                            tableData += '<td style="text-align: center;">' + extra_order_stock[i].uom + '</td>';
                            tableData += '<td style="text-align: right;">' + extra_order_stock[i].quantity + '</td>';

                            tableData += '<td style="text-align: center;">';
                            tableData += '<button id="tes" class="btn btn-danger btn-xs" ';
                            tableData += 'onclick="remExtraOrder(\'' + extra_order_stock[i].eo_number_sequence + '\')">';
                            tableData += '<i class="fa fa-trash"></i></button></td>';
                            tableData += '</tr>';

                            selected_extra_order_detail.push({
                                'id': extra_order_stock[i].id,
                                'eo_number': extra_order_stock[i].eo_number,
                                'eo_number_sequence': extra_order_stock[i].eo_number_sequence,
                                'material_number': extra_order_stock[i].material_number,
                                'description': extra_order_stock[i].description,
                                'sales_price': extra_order_stock[i].sales_price,
                                'uom': extra_order_stock[i].uom,
                                'quantity': extra_order_stock[i].quantity
                            });
                        }
                    }
                    $('#tableBodyList').append(tableData);
                    $("#create_eo_number").prop('selectedIndex', 0).change();

                } else {
                    $("#create_eo_number").prop('selectedIndex', 0).change();
                    openErrorGritter('Error!', 'Extra order already selected');
                    return false;
                }

                if (selected_extra_order.length > 0) {
                    $('#tableList').show();
                } else {
                    $('#tableList').hide();
                }

            }

        }

        function refreshStock() {

            $('#loading').show();
            $.get('{{ url('fetch/extra_order/warehouse_stock') }}', function(result, status, xhr) {
                if (result.status) {
                    extra_order = [];
                    extra_order_with_buyer = [];
                    extra_order_stock = [];
                    $('#create_eo_number').html('');
                    $("#create_destination").val('');
                    $("#create_attention").val('');
                    $("#create_division").val('');
                    $("#create_shipment_by").prop('selectedIndex', 0).change();
                    $("#create_payment_term").prop('selectedIndex', 0).change();
                    $("#create_freight").prop('selectedIndex', 0).change();
                    $("#create_condition").prop('selectedIndex', 0).change();
                    $("#create_regulation").prop('selectedIndex', 0).change();
                    $('#tableList').hide();
                    $('#tableAddItem').hide();

                    extra_order_stock = result.stock;
                    for (var i = 0; i < result.stock.length; i++) {
                        if (!extra_order.includes(result.stock[i].eo_number)) {
                            extra_order.push(result.stock[i].eo_number);
                            extra_order_with_buyer.push({
                                'eo_number': result.stock[i].eo_number,
                                'attention': result.stock[i].attention
                            });
                        }
                    }


                    var option = '';
                    option += '<option value=""></option>';
                    for (var i = 0; i < extra_order_with_buyer.length; i++) {

                        var percentage = 0;
                        for (let x = 0; x < result.percentage.length; x++) {
                            if (extra_order_with_buyer[i].eo_number == result.percentage[x].eo_number) {
                                percentage = result.percentage[x].percentage;
                            }

                        }

                        option += '<option value="' + extra_order_with_buyer[i].eo_number + '">';
                        option += extra_order_with_buyer[i].eo_number + ' (' + percentage + '%)';
                        option += ' - ' + extra_order_with_buyer[i].attention;
                        option += '</option>';
                    }
                    $('#create_eo_number').html(option);

                    $('#loading').hide();
                }
            });

        }

        function sendApp(send_app_no, $remark) {

            if (confirm("Are sure send this application to warehouse ?")) {
                $('#loading').show();
                var data = {
                    send_app_no: send_app_no
                }
                $.post('{{ url('send/extra_order/sending_application') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        showTable();
                        $('#loading').hide();
                        if ($remark == 'SEND') {
                            openSuccessGritter('Success', 'Send application success');
                        } else {
                            openSuccessGritter('Success', 'Resend application success');
                        }
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error!', result.message);
                    }

                });
            }

        }

        function showModalShipDoc(send_app_no) {

            $('#ship_send_app_no').text(send_app_no);
            $('#container_id').val('');
            $('#ship_iv').val('');
            $('#modalShipDoc').modal('show');

        }

        function submitShipDoc() {

            var send_app_no = $('#ship_send_app_no').text();
            var container_id = $('#container_id').val();
            var ship_iv = $('#ship_iv').val();

            if (send_app_no == '' || container_id == '' || ship_iv == '') {
                openErrorGritter('Error!', 'Fill all field');
                return false;
            }

            $('#loading').show();
            var data = {
                send_app_no: send_app_no,
                container_id: container_id,
                ship_iv: ship_iv
            }

            $.post('{{ url('input/extra_order/input_shipping_document') }}', data, function(result, status, xhr) {
                if (result.status) {
                    showTable();
                    $('#modalShipDoc').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success', result.message);

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }

            });

        }

        function showModalComplete(send_app_no) {

            $('#complete_send_app_no').text(send_app_no);
            $('#bl_date').val('');
            $('#invoice_number').val($('#row_send_app_no_' + send_app_no).find('td').eq(8).text());
            $('#invoice_file').val('');
            $('#way_bill').val('');
            $('#way_bill_file').val('');

            $('#divEmbedInvoice').html('');
            $('#divEmbedInvoice').html('<embed id="invoiceViewer" width="100%" height="300px">');

            $('#divEmbedWayBill').html('');
            $('#divEmbedWayBill').html('<embed id="wayBillViewer" width="100%" height="300px">');

            $('#invoice_checklist').iCheck('uncheck');
            $('#way_bill_checklist').iCheck('uncheck');

            $('#modalComplete').modal('show');

        }

        $("#invoice_file").on("change", function(e) {
            var file = e.target.files[0]
            var reader = new FileReader();

            reader.onload = function(e) {
                $("#invoiceViewer").attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });

        $("#way_bill_file").on("change", function(e) {
            var file = e.target.files[0]
            var reader = new FileReader();

            reader.onload = function(e) {
                $("#wayBillViewer").attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        });


        function submitComplete() {

            if ($("#bl_date").val() == '' || $("#invoice_number").val() == '' || $("#way_bill").val() == '') {
                openErrorGritter('Error!', 'Fill all field');
                return false;
            }

            if (!$("#invoice_file").val()) {
                openErrorGritter("Failed", "Please Select File");
                return false;
            }

            if (!$("#way_bill_file").val()) {
                openErrorGritter("Failed", "Please Select File");
                return false;
            }

            if (!$('#invoice_checklist').is(":checked")) {
                openErrorGritter("Failed", "Please check invoice number and document");
                return false;
            }

            if (!$('#way_bill_checklist').is(":checked")) {
                openErrorGritter("Failed", "Please check way bill number and document");
                return false;
            }

            var myFormData = new FormData();
            myFormData.append('send_app_no', $("#complete_send_app_no").text());
            myFormData.append('bl_date', $("#bl_date").val());
            myFormData.append('invoice_number', $("#invoice_number").val());
            myFormData.append('way_bill', $("#way_bill").val());
            myFormData.append('invoice_file', $("#invoice_file").prop('files')[0]);
            myFormData.append('way_bill_file', $("#way_bill_file").prop('files')[0]);


            $('#loading').show();
            $.ajax({
                url: '{{ url('input/extra_order/input_complete_sending') }}',
                type: 'POST',
                processData: false,
                contentType: false,
                dataType: 'json',
                data: myFormData,
                success: function(result, status, xhr) {
                    if (result.status) {
                        showTable();
                        $('#modalComplete').modal('hide');
                        $('#loading').hide();
                        openSuccessGritter('Success', 'Successfully Upload Sales Price');
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error!', result.message);
                    }
                }
            });

        }

        function showModalDetail(send_app) {

            $('#loading').show();
            var data = {
                send_app: send_app
            }
            $.get('{{ url('fetch/extra_order/detail_sending_application') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#bodyTimeline').html('');
                    $('#bodyDetailSendApp').html('');
                    $('#detail_selected_extra_order').html('');

                    $('#detail_send_app_no').val(result.send_app.send_app_no);
                    $('#detail_attention').val(result.send_app.attention);
                    $('#detail_destination').val(result.send_app.destination_name);
                    $('#detail_division').val(result.send_app.division);
                    $('#detail_shipment_by').val(result.send_app.shipment_by);
                    $('#detail_payment_term').val(result.send_app.payment_term);
                    $('#detail_freight').val(result.send_app.freight);
                    $('#detail_condition').val(result.send_app.condition);

                    var note = '-';
                    if (result.send_app.note != null) {
                        note = result.send_app.note.toUpperCase();
                    }

                    $('#detail_regulation').val(note);

                    $('#tabelDetailSendApp').DataTable().clear();
                    $('#tabelDetailSendApp').DataTable().destroy();

                    var extra_order = JSON.parse(result.send_app.document_number);
                    var label = '';
                    for (var i = 0; i < extra_order.length; i++) {
                        label += '<span style="margin-left: 1%;" class="label label-primary">';
                        label += extra_order[i];
                        label += '</span>';
                    }
                    $('#detail_selected_extra_order').append(label);

                    var tableData = '';
                    var current_pkg = '';
                    var total_amount = 0;
                    for (var i = 0; i < result.send_app_detail.length; i++) {
                        tableData += '<tr>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.send_app_detail[i].po_number;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.send_app_detail[i].sequence;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.send_app_detail[i].material_number;
                        tableData += '</td>';

                        tableData += '<td style="text-align: left; width: 35%;">';
                        tableData += result.send_app_detail[i].description;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.send_app_detail[i].uom;
                        tableData += '</td>';

                        tableData += '<td style="text-align: right;">';
                        tableData += result.send_app_detail[i].quantity;
                        tableData += '</td>';

                        tableData += '<td style="text-align: right;">';
                        tableData += result.send_app_detail[i].sales_price;
                        tableData += '</td>';

                        var quantity = result.send_app_detail[i].quantity;
                        var sales_price = result.send_app_detail[i].sales_price;
                        tableData += '<td style="text-align: right;">';
                        tableData += (quantity * sales_price).toFixed(2);
                        tableData += '</td>';
                        total_amount += (quantity * sales_price);


                        tableData += '<td style="text-align: right;">';
                        tableData += (result.send_app_detail[i].package_no || ' ');
                        tableData += '</td>';

                        if (current_pkg == result.send_app_detail[i].package_no) {
                            tableData += '<td style="text-align: right;"></td>';
                            tableData += '<td style="text-align: right;"></td>';
                            tableData += '<td style="text-align: right;"></td>';
                            tableData += '<td style="text-align: right;"></td>';
                            tableData += '<td style="text-align: right;"></td>';

                        } else {

                            tableData += '<td style="text-align: right;">';
                            tableData += (result.send_app_detail[i].package_type || '');
                            tableData += '</td>';

                            tableData += '<td style="text-align: right;">';
                            tableData += (result.send_app_detail[i].length || '');
                            tableData += '</td>';

                            tableData += '<td style="text-align: right;">';
                            tableData += (result.send_app_detail[i].width || '');
                            tableData += '</td>';

                            tableData += '<td style="text-align: right;">';
                            tableData += (result.send_app_detail[i].height || '');
                            tableData += '</td>';

                            tableData += '<td style="text-align: right;">';
                            tableData += (result.send_app_detail[i].weight || '');
                            tableData += '</td>';
                        }

                        current_pkg = result.send_app_detail[i].package_no

                        tableData += '</tr>';
                    }
                    $('#bodyDetailSendApp').append(tableData);
                    $('#total_amount').text(total_amount.toFixed(2));


                    var tableData = '';
                    tableData += '<tr>';
                    for (let i = 1; i <= 4; i++) {
                        tableData += '<th style="text-align: center; width: 25%;">';
                        var is_exist = false;
                        for (let j = 0; j < result.send_app_log.length; j++) {
                            if (i == result.send_app_log[j].status) {
                                var datetime = result.send_app_log[j].updated_at.split(' ');
                                tableData += '<br>' + datetime[0] + '<br>' + datetime[1] + '<br><br>';
                                is_exist = true;
                            }
                        }
                        if (!is_exist) {
                            tableData += '<br>-<br><br><br>';
                        }
                        tableData += '</th>';
                    }
                    tableData += '</tr>';
                    $('#bodyTimeline').append(tableData);

                    var tableData = '';
                    tableData += '<tr>';
                    for (let i = 1; i <= 4; i++) {
                        tableData += '<th style="text-align: center; width: 25%;">';
                        var is_exist = false;
                        for (let j = 0; j < result.send_app_log.length; j++) {
                            if (i == result.send_app_log[j].status) {
                                tableData += result.send_app_log[j].name;
                                is_exist = true;
                            }
                        }
                        if (!is_exist) {
                            tableData += '-';
                        }
                        tableData += '</th>';
                    }
                    tableData += '</tr>';
                    $('#bodyTimeline').append(tableData);

                    var tableQty = $('#tabelDetailSendApp').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [-1],
                            ['Show all']
                        ],
                        'buttons': {
                            buttons: [{
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
                                }
                            ]
                        },
                        'paging': false,
                        'lengthChange': true,
                        'searching': true,
                        'info': false,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });


                    $('#modalDetail').modal('show');
                    $('#loading').hide();

                }
            });
        }

        var edit_send_app_detail = [];

        function showModalEdit(send_app) {

            $('#loading').show();
            var data = {
                send_app: send_app
            }
            $.get('{{ url('fetch/extra_order/detail_sending_application') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#bodyEditSendApp').html('');
                    edit_send_app_detail = [];

                    $('#edit_send_app_no').val(result.send_app.send_app_no);
                    $('#edit_attention').val(result.send_app.attention);
                    $('#edit_destination').val(result.send_app.destination_name);
                    $('#edit_division').val(result.send_app.division);
                    $('#edit_shipment_by').val(result.send_app.shipment_by).trigger('change.select2');
                    $('#edit_payment_term').val(result.send_app.payment_term).trigger('change.select2');
                    $('#edit_freight').val(result.send_app.freight).trigger('change.select2');
                    $('#edit_condition').val(result.send_app.condition).trigger('change.select2');

                    var tableData = '';
                    for (var i = 0; i < result.send_app_detail.length; i++) {
                        tableData += '<tr style="background-color: #f5f5f5;">';

                        tableData += '<td style="text-align: center;">';
                        tableData += '<input style="text-align: center;" onkeyup="checkEditPo(id)" ';
                        tableData += 'id="po_' + result.send_app_detail[i].id + '" type="text" ';
                        tableData += 'value="' + result.send_app_detail[i].po_number + '">';
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.send_app_detail[i].sequence;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.send_app_detail[i].material_number;
                        tableData += '</td>';

                        tableData += '<td style="text-align: left;">';
                        tableData += result.send_app_detail[i].description;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.send_app_detail[i].uom;
                        tableData += '</td>';

                        tableData += '<td style="text-align: right;">';
                        tableData += result.send_app_detail[i].quantity;
                        tableData += '</td>';

                        tableData += '</tr>';

                        edit_send_app_detail.push({
                            'id': result.send_app_detail[i].id,
                            'po_number': result.send_app_detail[i].po_number
                        });
                    }
                    $('#bodyEditSendApp').append(tableData);

                    $('#modalEdit').modal('show');
                    $('#loading').hide();
                }
            });
        }

        function submitEdit() {

            var send_app_no = $('#edit_send_app_no').val();
            var shipment_by = $('#edit_shipment_by').val();
            var payment_term = $('#edit_payment_term').val();
            var freight = $('#edit_freight').val();
            var condition = $('#edit_condition').val();

            if (edit_shipment_by == '' || edit_payment_term == '' || edit_freight == '' || edit_condition == '') {
                openErrorGritter('Error!', 'Fill all field');
                return false;
            }

            $('#loading').show();
            var data = {
                send_app_no: send_app_no,
                shipment_by: shipment_by,
                payment_term: payment_term,
                freight: freight,
                condition: condition,
                edit_send_app_detail: edit_send_app_detail
            }
            $.post('{{ url('edit/extra_order/input_sending_application') }}', data, function(result, status, xhr) {
                if (result.status) {
                    showTable();
                    $('#modalEdit').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success', result.message);

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }

            });

        }

        function checkEditPo(id) {
            var id = id.replace('po_', '');
            for (let i = 0; i < edit_send_app_detail.length; i++) {
                if (edit_send_app_detail[i].id == id) {
                    edit_send_app_detail[i].po_number = $('#po_' + id).val();
                }
            }
        }

        function showModalDelete(send_app_no) {
            $('#delete_reason').val('');
            $('#delete_send_app_no').val(send_app_no);
            $('#modalDelete').modal('show');
        }

        function deleteSendApp() {
            $('#loading').show();

            if ($('#delete_reason').val() == '') {
                openErrorGritter('Error!', 'Fill all field');
                return false;
            }

            var data = {
                send_app_no: $('#delete_send_app_no').val(),
                reason: $('#delete_reason').val()
            }

            $.post('{{ url('delete/extra_order/input_sending_application') }}', data, function(result, status, xhr) {
                if (result.status) {

                    showTable();

                    $('#modalDelete').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success', result.message);

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }

            });
        }

        $('#tag_check_1').keyup(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                if ($("#tag_check_1").val().length == 10) {
                    var data = {
                        tag: $("#tag_check_1").val(),
                    }

                    $('#loading').show();

                    $.get('{{ url('scan/ga_control/uniform/operator') }}', data, function(result, status, xhr) {
                        if (result.status) {
                            var pic_1 = result.employee.employee_id + ' - ' + result.employee.name;

                            if ($('#pic_check_2').val() != '') {
                                if ($('#pic_check_2').val() == pic_1) {
                                    $('#tag_check_1').val('');
                                    $('#pic_check_1').val('');
                                    $('#loading').hide();
                                    openErrorGritter('Error!', 'Chose another PIC');
                                    return false;
                                }
                            }

                            $('#pic_check_1').val(pic_1);
                            $('#loading').hide();
                            openSuccessGritter('Success', 'PIC check 1 added successfully');
                        } else {
                            $('#tag_check_1').val('');
                            $('#pic_check_1').val('');
                            $('#loading').hide();
                            openErrorGritter('Error!', result.message);
                        }
                    })
                } else {
                    $('#tag_check_1').val('');
                    $('#pic_check_1').val('');
                    $('#loading').hide();
                    openErrorGritter('Error!', 'Please tap the correct ID card');
                }
            }
        });

        $('#tag_check_2').keyup(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                if ($("#tag_check_2").val().length == 10) {
                    var data = {
                        tag: $("#tag_check_2").val(),
                    }

                    $.get('{{ url('scan/ga_control/uniform/operator') }}', data, function(result, status, xhr) {
                        if (result.status) {
                            var pic_2 = result.employee.employee_id + ' - ' + result.employee.name;

                            if ($('#pic_check_1').val() != '') {
                                if ($('#pic_check_1').val() == pic_2) {
                                    $('#tag_check_2').val('');
                                    $('#pic_check_2').val('');
                                    $('#loading').hide();
                                    openErrorGritter('Error!', 'Chose another PIC');
                                    return false;
                                }
                            }

                            $('#pic_check_2').val(pic_2);
                            $('#loading').hide();
                            openSuccessGritter('Success', 'PIC check 1 added successfully');
                        } else {
                            $('#tag_check_2').val('');
                            $('#pic_check_2').val('');
                            $('#loading').hide();
                            openErrorGritter('Error!', result.message);
                        }
                    })
                } else {
                    $('#tag_check_2').val('');
                    $('#pic_check_2').val('');
                    $('#loading').hide();
                    openErrorGritter('Error!', 'Please tap the correct ID card');
                }
            }
        });

        var measure_send_app_detail = [];

        function showModalMeasure(send_app) {

            $('#loading').show();
            var data = {
                send_app: send_app
            }
            $.get('{{ url('fetch/extra_order/detail_sending_application') }}', data, function(result, status,
                xhr) {
                if (result.status) {

                    $('#bodyMeasure').html('');
                    measure_send_app_detail = [];

                    $('#tag_check_1').val('');
                    $('#pic_check_1').val('');
                    $('#tag_check_2').val('');
                    $('#pic_check_2').val('');

                    $('#measure_send_app_no').val(result.send_app.send_app_no);
                    $('#measure_attention').val(result.send_app.attention);
                    $('#measure_destination').val(result.send_app.destination_name);
                    $('#measure_division').val(result.send_app.division);
                    $('#measure_shipment_by').val(result.send_app.shipment_by);
                    $('#measure_payment_term').val(result.send_app.payment_term);
                    $('#measure_freight').val(result.send_app.freight);
                    $('#measure_condition').val(result.send_app.condition);

                    var tableData = '';
                    for (var i = 0; i < result.send_app_detail.length; i++) {
                        tableData += '<tr style="background-color: #f5f5f5;"';
                        tableData += 'id="row_' + result.send_app_detail[i].id + '">';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.send_app_detail[i].sequence;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.send_app_detail[i].material_number;
                        tableData += '</td>';

                        tableData += '<td style="text-align: left;">';
                        tableData += result.send_app_detail[i].description;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.send_app_detail[i].uom;
                        tableData += '</td>';

                        tableData += '<td style="text-align: right;">';
                        tableData += result.send_app_detail[i].quantity;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += '<input style="text-align: center; width: 100%;" ';
                        tableData += 'id="pkg_no_' + result.send_app_detail[i].id + '" type="text">';
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += '<select class="form-control select3" onchange="changeMeasure(id)" ';
                        tableData += 'id="pkg_type_' + result.send_app_detail[i].id + '"';
                        tableData += 'data-placeholder="Select..." style="width: 100%;">';
                        tableData += '<option value=""></option>';
                        tableData += '<option value="PL">PL</option>';
                        tableData += '<option value="CT">CT</option>';
                        tableData += '</select>';
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += '<input style="text-align: center; width: 100%;" onkeyup="changeMeasure(id)" ';
                        tableData += 'id="length_' + result.send_app_detail[i].id + '" type="text">';
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += '<input style="text-align: center; width: 100%;" onkeyup="changeMeasure(id)" ';
                        tableData += 'id="width_' + result.send_app_detail[i].id + '" type="text">';
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += '<input style="text-align: center; width: 100%;" onkeyup="changeMeasure(id)" ';
                        tableData += 'id="height_' + result.send_app_detail[i].id + '" type="text">';
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += '<input style="text-align: center; width: 100%;" onkeyup="changeMeasure(id)" ';
                        tableData += 'id="weight_' + result.send_app_detail[i].id + '" type="text">';
                        tableData += '</td>';

                        tableData += '</tr>';

                        measure_send_app_detail.push({
                            'id': result.send_app_detail[i].id
                        });
                    }
                    $('#bodyMeasure').append(tableData);

                    $('.select3').select2();


                    $('#modalMeasure').modal('show');
                    $('#loading').hide();
                }
            });

        }

        function changeMeasure(id) {
            var identifier = id.replace(/[0-9]/g, "")
            var index = id.replace(/[^\d]/g, "");
            var pkg_no = $('#pkg_no_' + index).val();

            if (pkg_no != '') {
                for (var i = 0; i < measure_send_app_detail.length; i++) {
                    if (index != measure_send_app_detail[i].id) {
                        if ($('#pkg_no_' + index).val() == $('#pkg_no_' + measure_send_app_detail[i].id).val()) {
                            if ($('#' + identifier + index).val() != $('#' + identifier + measure_send_app_detail[i].id)
                                .val()) {
                                if (identifier == 'pkg_type_') {
                                    $('#pkg_type_' + measure_send_app_detail[i].id).val($('#pkg_type_' + index).val())
                                        .trigger('change.select2');
                                } else {
                                    $('#' + identifier + measure_send_app_detail[i].id).val($('#' + identifier + index)
                                        .val());
                                }
                            }
                        }
                    }
                }
            }

        }

        function submitMeasure() {

            if ($('#pic_check_1').val() == '' || $('#pic_check_2').val() == '') {
                openErrorGritter('Error!', 'Tap ID card for checker PIC');
                return false;
            }

            var measurement = [];
            for (let i = 0; i < measure_send_app_detail.length; i++) {
                if ($('#pkg_no_' + measure_send_app_detail[i].id).val() == '') {
                    $('#pkg_no_' + measure_send_app_detail[i].id).focus();
                    openErrorGritter('Error!', 'Fill all measurement data');
                    return false;
                }

                if ($('#pkg_type_' + measure_send_app_detail[i].id).val() == '') {
                    $('#pkg_type_' + measure_send_app_detail[i].id).focus();
                    openErrorGritter('Error!', 'Fill all measurement data');
                    return false;
                }

                if ($('#length_' + measure_send_app_detail[i].id).val() == '') {
                    $('#length_' + measure_send_app_detail[i].id).focus();
                    openErrorGritter('Error!', 'Fill all measurement data');
                    return false;
                }

                if ($('#height_' + measure_send_app_detail[i].id).val() == '') {
                    $('#height_' + measure_send_app_detail[i].id).focus();
                    openErrorGritter('Error!', 'Fill all measurement data');
                    return false;
                }

                if ($('#width_' + measure_send_app_detail[i].id).val() == '') {
                    $('#width_' + measure_send_app_detail[i].id).focus();
                    openErrorGritter('Error!', 'Fill all measurement data');
                    return false;
                }

                if ($('#weight_' + measure_send_app_detail[i].id).val() == '') {
                    $('#weight_' + measure_send_app_detail[i].id).focus();
                    openErrorGritter('Error!', 'Fill all measurement data');
                    return false;
                }

                measurement.push({
                    id: measure_send_app_detail[i].id,
                    pkg_no: $('#pkg_no_' + measure_send_app_detail[i].id).val(),
                    pkg_type: $('#pkg_type_' + measure_send_app_detail[i].id).val(),
                    length: $('#length_' + measure_send_app_detail[i].id).val(),
                    height: $('#height_' + measure_send_app_detail[i].id).val(),
                    width: $('#width_' + measure_send_app_detail[i].id).val(),
                    weight: $('#weight_' + measure_send_app_detail[i].id).val(),
                });
            }

            $('#loading').show();
            var data = {
                send_app_no: $('#measure_send_app_no').val(),
                check_1: $('#pic_check_1').val(),
                check_2: $('#pic_check_2').val(),
                measurement: measurement
            }

            $.post('{{ url('input/extra_order/measurement') }}', data, function(result, status, xhr) {
                if (result.status) {
                    showTable();
                    $('#modalMeasure').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success', result.message);

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }

            });

        }

        function downloadSendApp(send_app) {
            window.open('{{ url('index/extra_order/send_app_pdf') }}' + '/' + send_app, '_blank');
        }

        function showButton(send_app) {

            var role_code = $('#role_code').val();

            var button = '';
            button += '<a onclick="showModalDetail(\'' + send_app.send_app_no + '\')" ';
            button += 'class="btn btn-default btn-sm"';
            button += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px;';
            button += 'color:black; background-color: #e7e7e7;">';
            button += '&nbsp;<i class="fa fa-eye"></i>&nbsp;Detail</a>';

            if (send_app.deleted_at == null) {
                button += '<a onclick="downloadSendApp(\'' + send_app.send_app_no + '\')" ';
                button += 'class="btn btn-default btn-sm"';
                button += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px;';
                button += 'color:white; background-color: #605ca8;">';
                button += '&nbsp;<i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Send App Doc.</a>';

                if (send_app.status == 0 && (role_code.includes('PC') || role_code.includes('MIS'))) {
                    var remark = 'SEND';

                    button += '<a onclick="sendApp(\'' + send_app.send_app_no + '\', \'' + remark + '\')" ';
                    button += 'class="btn btn-primary btn-sm" ';
                    button += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px;">&nbsp;';
                    button += '<i class="fa fa-send-o"></i>&nbsp;Send</a>';

                    button += '<a onclick="showModalEdit(\'' + send_app.send_app_no + '\')" ';
                    button += 'class="btn btn-warning btn-sm" ';
                    button += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px;">';
                    button += '&nbsp;<i class="fa fa-pencil-square-o"></i>&nbsp;Edit</a>';

                    button += '<a onclick="showModalDelete(\'' + send_app.send_app_no + '\')" ';
                    button += 'class="btn btn-default btn-sm" style="margin: 1px; padding-top: 2px; padding-bottom: 2px; ';
                    button += 'color:white; background-color: #ee4833;">';
                    button += '&nbsp;<i class="fa fa-trash"></i>&nbsp;Delete</a>';

                } else if (send_app.status == 1 && (role_code.includes('PC') ||
                        role_code.includes('WH') ||
                        role_code.includes('C-LOG') ||
                        role_code.includes('L-LOG') ||
                        role_code.includes('SL-LOG') ||
                        role_code.includes('OP-LOG') ||
                        role_code.includes('MIS'))) {
                    var remark = 'RESEND';

                    button += '<a onclick="sendApp(\'' + send_app.send_app_no + '\', \'' + remark + '\')" ';
                    button += 'class="btn btn-info btn-sm" ';
                    button += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px; color: black;">';
                    button += '&nbsp;<i class="fa fa-send"></i>&nbsp;Resend</a>';

                    button += '<a onclick="showModalMeasure(\'' + send_app.send_app_no + '\')" ';
                    button += 'class="btn btn-primary btn-sm" ';
                    button += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px; '
                    button += 'color:black; background-color: #ecff7b;">';
                    button += '&nbsp;<i class="fa fa-cubes"></i>&nbsp;Measure</a>';

                    if (role_code.includes('PC') || role_code.includes('MIS')) {
                        button += '<a onclick="showModalDelete(\'' + send_app.send_app_no + '\')" ';
                        button += 'class="btn btn-default btn-sm" style="padding-top: 2px; padding-bottom: 2px; ';
                        button += 'color:white; background-color: #ee4833; margin: 1px;">';
                        button += '&nbsp;<i class="fa fa-trash"></i>&nbsp;Delete</a>';
                    }

                } else if (send_app.status == 3) {

                    if ((role_code.includes('MIS') ||
                            role_code.includes('WH') ||
                            role_code.includes('C-LOG') ||
                            role_code.includes('S-LOG'))) {
                        button += '<a onclick="showModalShipDoc(\'' + send_app.send_app_no + '\')" ';
                        button += 'class="btn btn-primary btn-sm" ';
                        button += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px; ';
                        button += 'color:black; background-color: #ccffff;">';
                        button += '&nbsp;<i class="fa fa-cubes"></i>&nbsp;Shipment Doc.</a>';
                    }

                    if (role_code.includes('PC') || role_code.includes('MIS')) {
                        button += '<a onclick="showModalDelete(\'' + send_app.send_app_no + '\')" ';
                        button += 'class="btn btn-default btn-sm" style="padding-top: 2px; padding-bottom: 2px; ';
                        button += 'color:white; background-color: #ee4833; margin: 1px;">';
                        button += '&nbsp;<i class="fa fa-trash"></i>&nbsp;Delete</a>';
                    }

                } else if (send_app.status == 4) {

                } else if (send_app.status == 5 && (role_code.includes('MIS') ||
                        role_code.includes('S-LOG') ||
                        role_code.includes('C-LOG'))) {

                    button += '<a onclick="showModalComplete(\'' + send_app.send_app_no + '\')" ';
                    button += 'class="btn btn-success btn-sm" ';
                    button += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px;">';
                    button += '&nbsp;<i class="fa fa-check-square-o"></i>&nbsp;Complete</a>';
                }
            }

            return button;

        }

        function showStatus(send_app) {

            var status = '';

            if (send_app.deleted_at != null) {
                status =
                    '<label class="label-status" style="background-color: #ff5a36; font-weight: 400;">&nbsp;&nbsp;Deleted&nbsp;&nbsp;</label>';
            } else {
                if (send_app.status == 0) {
                    status =
                        '<label class="label-status" style="background-color: #ecf0f5; font-weight: 400;">Requested</label>';
                } else if (send_app.status == 1) {
                    status =
                        '<label class="label-status" style="background-color: #ecf0f5; font-weight: 400;">Requested</label>';
                } else if (send_app.status == 2 || send_app.status == 3) {
                    status =
                        '<label class="label-status" style="background-color: #ecff7b; font-weight: 400;">&nbsp;Checked&nbsp;</label>';
                } else if (send_app.status == 4) {
                    status =
                        '<label class="label-status" style="background-color: #d6ffa1; font-weight: 400;">Scheduled</label>';
                } else if (send_app.status == 5) {
                    status =
                        '<label class="label-status" style="background-color: #ccffff; font-weight: 400;">Delivered</label>';
                } else if (send_app.status == 6) {
                    status =
                        '<label class="label-status" style="background-color: #22cc7d; font-weight: 400;">Complete</label>';
                }
            }

            return status;

        }

        function showEoNumber(send_app) {

            var value = '';
            var eo_number = JSON.parse(send_app.document_number);

            for (var i = 0; i < eo_number.length; i++) {
                value += eo_number[i];
                if (i != (eo_number.length - 1)) {
                    value += '<br>';
                }
            }

            return value;

        }

        function showTable() {

            var issue_from = $('#issue_from').val();
            var issue_to = $('#issue_to').val();
            var stuffing_from = $('#stuffing_from').val();
            var stuffing_to = $('#stuffing_to').val();
            var search_status = $('#search_status').val();
            var search_payment_term = $('#search_payment_term').val();
            var search_ship_by = $('#search_ship_by').val();
            var search_destination = $('#search_destination').val();

            var data = {
                issue_from: issue_from,
                issue_to: issue_to,
                stuffing_from: stuffing_from,
                stuffing_to: stuffing_to,
                search_status: search_status,
                search_payment_term: search_payment_term,
                search_ship_by: search_ship_by,
                search_destination: search_destination
            }


            $('#loading').show();
            $.get('{{ url('fetch/extra_order/sending_application') }}', data, function(
                result, status, xhr) {
                if (result.status) {
                    $('#loading').hide();

                    $('#tableDetail').DataTable().clear();
                    $('#tableDetail').DataTable().destroy();

                    $('#tableDetail thead').html("");
                    var head = '';
                    head += '<tr>';
                    head += '<th style="text-align: center;">Send App No.<br>Issue Date</th>';
                    head += '<th style="text-align: center;">Status</th>';
                    head += '<th style="text-align: center;">EO No.</th>';
                    head += '<th style="text-align: center;">Attention</th>';
                    head += '<th style="text-align: center;">Condition</th>';
                    head += '<th style="text-align: center;">Ship By</th>';
                    head += '<th style="text-align: center;">Payment Term</th>';
                    head += '<th style="text-align: center;">Checksheet</th>';
                    head += '<th style="text-align: center;">IV No.</th>';
                    head += '<th style="text-align: center;">St Date</th>';
                    head += '<th style="text-align: center;">Bl Date</th>';
                    head += '<th style="text-align: center;">Action</th>';
                    head += '</tr>';
                    $('#tableDetail thead').append(head);

                    $('#tableDetail tfoot').html("");
                    var foot = '';
                    foot += '<tr>'
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '<th></th>';
                    foot += '</tr>';
                    $('#tableDetail tfoot').append(foot);

                    $('#tableDetailBody').html("");
                    var body = '';
                    for (var i = 0; i < result.send_app.length; i++) {
                        body += '<tr id="row_send_app_no_' + result.send_app[i].send_app_no + '">';

                        body += '<td style="width:10%; text-align:center;">' +
                            result.send_app[i].send_app_no +
                            '<br>' + result.send_app[i].submit +
                            '</td>';

                        body += '<td style="width:5%; text-align:center;">' +
                            showStatus(result.send_app[i]) +
                            '</td>';

                        body += '<td style="width:5%; text-align:left;">' +
                            showEoNumber(result.send_app[i]) +
                            '</td>';

                        body += '<td style="width:10%; text-align:left;">' +
                            result.send_app[i].attention +
                            '<br>' +
                            result.send_app[i].destination_shortname +
                            '</td>';

                        body += '<td style="width:5%; text-align:center;">' +
                            result.send_app[i].condition +
                            '</td>';

                        body += '<td style="width:5%; text-align:center;">' +
                            result.send_app[i].shipment_by +
                            '</td>';

                        body += '<td style="width:5%; text-align:center;">' +
                            result.send_app[i].payment_term +
                            '</td>';

                        body += '<td style="width:5%; text-align:center;">' +
                            (result.send_app[i].container_id || '') +
                            '</td>';

                        body += '<td style="width:5%; text-align:center;">' +
                            (result.send_app[i].invoice_number || '') +
                            '</td>';

                        body += '<td style="width:7.5%; text-align:center;">' +
                            (result.send_app[i].st || '') +
                            '</td>';

                        body += '<td style="width:7.5%; text-align:center;">' +
                            (result.send_app[i].bl || '') +
                            '</td>';

                        body += '<td style="width:20%; text-align:center;">' +
                            showButton(result.send_app[i]) +
                            '</td>';

                        body += '</tr>';
                    }
                    $('#tableDetailBody').append(body);

                    $('#tableDetail tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input style="text-align: center; width: 100%; color: grey;" type="text" placeholder="Search ' +
                            title + '" size="3"/>');
                    });

                    var table = $('#tableDetail').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [10, 25, 50, -1],
                            ['10 rows', '25 rows', '50 rows',
                                'Show all'
                            ]
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
                        "bAutoWidth": false
                    });

                    table.columns().every(function() {
                        var that = this;
                        $('input', this.footer()).on('keyup change',
                            function() {
                                if (that.search() !== this.value) {
                                    that
                                        .search(this.value)
                                        .draw();
                                }
                            });
                    });
                    $('#tableDetail tfoot tr').prependTo('#tableDetail thead');

                    $('#loading').hide();
                }

            });

        }


        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

        function refreshAll() {
            location.reload(true);
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '5000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '5000'
            });
        }
    </script>
@endsection
