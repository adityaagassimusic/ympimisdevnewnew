@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
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
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid rgb(211,211,211);
        padding-top: 0;
        padding-bottom: 0;
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid rgb(211,211,211);
    }
    #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
    <h1>
        List of {{ $page }}s
    </h1>
    <ol class="breadcrumb">
        <li>
            <a data-toggle="modal" data-target="#uploadModal" class="btn btn-success btn-sm" style="color:white;">Upload {{ $page }}s</a>
            &nbsp;
            {{-- <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm" style="color:white">Import {{ $page }}s</a> --}}
            {{-- &nbsp; --}}
            <a data-toggle="modal" data-target="#createModal" class="btn btn-primary btn-sm" style="color:white;">Create {{ $page }}</a>

            <a data-toggle="modal" data-target="#infoModal" class="btn btn-default btn-sm" style="color:black; border-color: grey;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-info"></i>&nbsp;Master&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
        </li>
    </ol>
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
    <div class="row">

        <div class="col-xs-12">
            <div class="row" style="margin-bottom: 1%;">
                <div class="col-xs-2" style="padding-right: 0px;">
                    <div class="input-group date pull-right" style="text-align: center;">
                        <div class="input-group-addon bg-green">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control monthpicker" name="month" id="month" placeholder="Select Month">  
                    </div>
                </div>
                <div class="col-xs-2">
                    <button onclick="drawTable()" class="btn btn-primary">Search</button>
                </div>
            </div>

            <div class="box">
                <div class="box-body">
                    <table id="example1" class="table table-bordered table-striped table-hover">
                        <thead style="background-color: rgba(126,86,134,.7);" id="example1head">
                            <tr>
                                <th>ID</th>
                                <th>Ship. Month</th>
                                <th>Ship. Week</th>
                                <th>Sales Order</th>
                                <th>Ship. Cond.</th>
                                <th>Dest</th>
                                <th>Material</th>
                                <th>Description</th>
                                <th>HPL</th>
                                <th>Ship. Date</th>
                                <th>B/L Date</th>
                                <th>Qty</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot id="example1foot">
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
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="createModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Create {{$page}}</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Shipment Month<span class="text-red">*</span></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input class="form-control" id="st_month" placeholder="mm / yyyy" required>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Sales Order<span class="text-red">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="sales_order" placeholder="Enter Sales Order" required>
                        </div>
                    </div>

                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Shipment Condition<span class="text-red">*</span></label>
                        <div class="col-sm-6" align="left">
                            <select class="form-control select2" id="shipment_condition_code" style="width: 100%;" data-placeholder="Choose a Shipment Condition Code..." required>
                                <option value=""></option>
                                @foreach($shipment_conditions as $shipment_condition)
                                <option value="{{ $shipment_condition->shipment_condition_code }}">{{ $shipment_condition->shipment_condition_code }} - {{ $shipment_condition->shipment_condition_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Destination<span class="text-red">*</span></label>
                        <div class="col-sm-6" align="left">
                            <select class="form-control select2" id="destination_code" style="width: 100%;" data-placeholder="Choose a Destination Code..." required>
                                <option value=""></option>
                                @foreach($destinations as $destination)
                                <option value="{{ $destination->destination_code }}">{{ $destination->destination_code }} - {{ $destination->destination_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Material<span class="text-red">*</span></label>
                        <div class="col-sm-6" align="left">
                            <select class="form-control select2" id="material_number" style="width: 100%;" data-placeholder="Choose a Material Number..." required>
                                <option value=""></option>
                                @foreach($materials as $material)
                                <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">HPL<span class="text-red">*</span></label>
                        <div class="col-sm-6" align="left">
                            <select class="form-control select2" id="hpl" style="width: 100%;" data-placeholder="Choose a HPL..." required>
                                <option value=""></option>
                                @foreach($hpls as $hpl)
                                <option value="{{ $hpl }}">{{ $hpl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Shipment Date<span class="text-red">*</span></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="st_date" placeholder="Enter Shipment Date" required>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Bill of Lading Date<span class="text-red">*</span></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="bl_date" placeholder="Enter B/L Date" required>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input min="1" type="number" class="form-control" id="quantity" placeholder="Enter Quantity" required>
                                <span class="input-group-addon">pc(s)</span>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="create()" class="btn btn-primary" data-dismiss="modal"><i class="fa fa-plus"></i> Create</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ViewModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Detail {{$page}}</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <div class="form-group row" align="right">
                        <label class="col-sm-6">Ship. Month</label>
                        <div class="col-sm-6" align="left" id="ship_month_view"></div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-6">Ship. Week</label>
                        <div class="col-sm-6" align="left" id="ship_week_view"></div>
                    </div>          
                    <div class="form-group row" align="right">
                        <label class="col-sm-6">Sales Order</label>
                        <div class="col-sm-6" align="left" id="sales_order_view"></div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-6">Ship. Condition</label>
                        <div class="col-sm-6" align="left" id="ship_condition_view"></div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-6">Destination</label>
                        <div class="col-sm-6" align="left" id="destination_view"></div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-6">Material</label>
                        <div class="col-sm-6" align="left" id="material_view"></div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-6">HPL</label>
                        <div class="col-sm-6" align="left" id="hpl_view"></div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-6">Origin Group</label>
                        <div class="col-sm-6" align="left" id="origin_group_view"></div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-6">Shipment Date</label>
                        <div class="col-sm-6" align="left" id="shipment_date_view"></div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-6">Bill of Lading Date</label>
                        <div class="col-sm-6" align="left" id="bold_view"></div>
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

<div class="modal fade" id="infoModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 65%;">
        <div class="modal-content">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Master Information :</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1">
                        <div class="col-xs-7">
                            <h3>Destination</h3>
                            <table class="table table-hover table-bordered table-striped" id="tableList" style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 30%;">Destination</th>
                                        <th style="width: 70%;">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($destinations as $dt)
                                    <tr>
                                        <td>{{ $dt->destination_shortname }}</td>
                                        <td>{{ $dt->destination_name }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xs-5">
                            <h3>Shipment Condition (Way)</h3>
                            <table class="table table-hover table-bordered table-striped" id="tableList" style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 30%;">Destination</th>
                                        <th style="width: 70%;">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($shipment_conditions as $dt)
                                    <tr>
                                        <td>{{ $dt->shipment_condition_code }}</td>
                                        <td>{{ $dt->shipment_condition_name }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Upload Shipment Schedule</h4>
                Format :
                [<b><i>Sales Order</i></b>]
                [<b><i>Way</i></b>]
                [<b><i>Dest. Code</i></b>]
                [<b><i>GMC</i></b>]
                [<b><i>HPL</i></b>]
                [<b><i>St Date</i></b>]
                [<b><i>Bl Date</i></b>]
                [<b><i>Qty Ekspor</i></b>]
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1">
                        <div class="col-xs-12" style="margin-top: 2%;">
                            <label>Select Month :<span class="text-red">*</span></label>
                        </div>
                        <div class="col-xs-6" style="padding-right: 0px;">
                            <div class="input-group date pull-right" style="text-align: center;">
                                <div class="input-group-addon bg-green">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control monthpicker" name="period" id="period" placeholder="Select Month">  
                            </div>
                        </div>

                        <div class="col-xs-12" style="margin-top: 2%;">
                            <label>Shipment Data :<span class="text-red">*</span></label>
                        </div>
                        <div class="col-xs-12">
                            <textarea id="shipment_data" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
                        </div>
                    </div>
                </div>    
            </div>
            <div class="modal-footer">
                <div class="row" style="margin-top: 7%; margin-right: 2%;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button onclick="uploadShipment()" class="btn btn-success">Upload </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="EditModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Edit {{$page}}</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <input type="hidden" value="{{csrf_token()}}" name="_token" />
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Shipment Month<span class="text-red">*</span></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input class="form-control" id="st_month_edit" placeholder="mm / yyyy" required>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Sales Order<span class="text-red">*</span></label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="sales_order_edit" placeholder="Enter Sales Order" required>
                        </div>
                    </div>

                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Shipment Condition<span class="text-red">*</span></label>
                        <div class="col-sm-6" align="left">
                            <select class="form-control select2" id="shipment_condition_code_edit" style="width: 100%;" data-placeholder="Choose a Shipment Condition Code..." required>
                                <option value=""></option>
                                @foreach($shipment_conditions as $shipment_condition)
                                <option value="{{ $shipment_condition->shipment_condition_code }}">{{ $shipment_condition->shipment_condition_code }} - {{ $shipment_condition->shipment_condition_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Destination<span class="text-red">*</span></label>
                        <div class="col-sm-6" align="left">
                            <select class="form-control select2" id="destination_code_edit" style="width: 100%;" data-placeholder="Choose a Destination Code.." required>
                                <option value=""></option>
                                @foreach($destinations as $destination)
                                <option value="{{ $destination->destination_code }}">{{ $destination->destination_code }} - {{ $destination->destination_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Material<span class="text-red">*</span></label>
                        <div class="col-sm-6" align="left">
                            <select class="form-control select2" id="material_number_edit" style="width: 100%;" data-placeholder="Choose a Material Number..." required>
                                <option value=""></option>
                                @foreach($materials as $material)
                                <option value="{{ $material->material_number }}">{{ $material->material_number }} - {{ $material->material_description }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">HPL<span class="text-red">*</span></label>
                        <div class="col-sm-6" align="left">
                            <select class="form-control select2" id="hpl_edit" style="width: 100%;" data-placeholder="Choose a HPL..." required>
                                <option value=""></option>
                                @foreach($hpls as $hpl)
                                <option value="{{ $hpl }}">{{ $hpl }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Shipment Date<span class="text-red">*</span></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="st_date_edit" placeholder="Enter Shipment Date" required>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Bill of Lading Date<span class="text-red">*</span></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" class="form-control" id="bl_date_edit" placeholder="Enter B/L Date" required>
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-sm-4">Quantity<span class="text-red">*</span></label>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input min="1" type="number" class="form-control" id="quantity_edit" placeholder="Enter Quantity" required>
                                <span class="input-group-addon">pc(s)</span>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>
            <div class="modal-footer">
                <input type="hidden" id="id_edit">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="edit()" class="btn btn-warning" data-dismiss="modal"><i class="fa fa-pencil"></i> Edit</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-conntent">
            <form id ="importForm" method="post" action="{{ url('import/shipment_schedule') }}" enctype="multipart/form-data">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <div class="modal-header">
                    <button type="butto" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
                    Format: [Shipment Month][Sales Order][Shipment Condition Code][Destination Code][Material Number][HPL][Shipment Date][BL Date][Quantity]<br>
                    Sample: <a href="{{ url('download/manual/import_shipment_schedule.txt') }}">import_shipment_schedule.txt</a> Code: #Add
                </div>
                <div class="">
                    <div class="modal-body">
                        <center><input type="file" name="shipment_schedule" id="InputFile" accept="text/plain"></center>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
        drawTable();

        $('body').toggleClass("sidebar-collapse");

        $('#st_month').datepicker({
            autoclose: true,
            format: "mm/yyyy",
            viewMode: "months", 
            minViewMode: "months"
        })
        $('#st_date').datepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
        })
        $('#bl_date').datepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
        })
        $('#st_month_edit').datepicker({
            autoclose: true,
            format: "mm/yyyy",
            viewMode: "months", 
            minViewMode: "months"
        })
        $('#st_date_edit').datepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
        })
        $('#bl_date_edit').datepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
        })
        $('.monthpicker').datepicker({
            format: "yyyy-mm",
            startView: "months", 
            minViewMode: "months",
            autoclose: true,
            todayHighlight: true
        });

        $('.select2').select2();
    });

    function uploadShipment() {

        var period = $('#period').val();
        var st_data = $('#shipment_data').val();

        if(period == '' || st_data == ''){
            openErrorGritter('Error', 'All data must be complete');
            return false;
        }

        var data = {
            period : period,
            st_data : st_data,
        }

        $('#loading').show();
        $.post('{{ url("input/shipment_schedule") }}', data, function(result, status, xhr){
            if(result.status){

                $('#period').val('');
                $('#shipment_data').val('');

                $('#example1').DataTable().ajax.reload();
                
                $('#uploadModal').modal('hide');

                $('#loading').hide();
                openSuccessGritter('Success', 'Shipment Schedule Uploaded Successfully');

            }else {
                $('#loading').hide();
                openErrorGritter('Error', result.message);
            }

        });
    }

    function drawTable() {
        $('#example1').DataTable().clear();
        $('#example1').DataTable().destroy();

        var month = $('#month').val();

        var data = {
            month:month
        }

        $('#example1 thead').html("");
        var head = '';
        head += '<tr>';
        head += '<th>ID</th>';
        head += '<th>Ship. Month</th>';
        head += '<th>Ship. Week</th>';
        head += '<th>Sales Order</th>';
        head += '<th>Ship. Cond.</th>';
        head += '<th>Dest</th>';
        head += '<th>Material</th>';
        head += '<th>Description</th>';
        head += '<th>HPL</th>';
        head += '<th>Ship. Date</th>';
        head += '<th>B/L Date</th>';
        head += '<th>Qty</th>';
        head += '<th>Action</th>';
        head += '</tr>';                        
        $('#example1 thead').append(head);


        $('#example1 tfoot').html("");
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
        foot += '<th></th>';
        foot += '</tr>';
        $('#example1 tfoot').append(foot);


        $('#example1 tfoot th').each( function () {
            var title = $(this).text();
            $(this).html( '<input style="text-align: center;" type="text" placeholder="Search '+title+'" size="6"/>' );
        } );
        var table = $('#example1').DataTable({
            "order": [],
            'dom': 'Bfrtip',
            'responsive': true,
            'lengthMenu': [
            [ 50, 250, 500, 1000, -1 ],
            [ '50 rows', '250 rows', '500 rows', '1000 rows', 'Show all' ]
            ],
            'buttons': {
                buttons:[
                {
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
                },
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
                "type" : "get",
                "url" : "{{ url("fetch/shipment_schedule") }}",
                "data" : data
            },
            "columnDefs": [
            {
                "targets": [5],
                "className": "text-left"
            }
            ],
            "columns": [
            { "data": "id" , "width": "5%"},
            { "data": "st_month" , "width": "5%"},
            { "data": "week_name", "width": "5%"},
            { "data": "sales_order" , "width": "5%"},
            { "data": "shipment_condition_name" , "width": "5%"},
            { "data": "destination_shortname" , "width": "5%"},
            { "data": "material_number" , "width": "5%"},
            { "data": "material_description" , "width": "20%"},
            { "data": "hpl" , "width": "5%"},
            { "data": "st_date" , "width": "5%"},
            { "data": "bl_date" , "width": "5%"},
            { "data": "quantity" , "width": "5%"},
            { "data": "action", "width": "10%" }
            ],
        });

        table.columns().every( function () {
            var that = this;

            $( 'input', this.footer() ).on( 'keyup change', function () {
                if ( that.search() !== this.value ) {
                    that
                    .search( this.value )
                    .draw();
                }
            } );
        } );

        $('#example1 tfoot tr').appendTo('#example1 thead');
    }

    function create() {
        var data = {
            st_month: $("#st_month").val(),
            sales_order: $("#sales_order").val(),
            shipment_condition_code: $("#shipment_condition_code").val(),
            destination_code: $("#destination_code").val(),
            material_number: $("#material_number").val(),
            hpl: $("#hpl").val(),
            st_date: $("#st_date").val(),
            bl_date: $("#bl_date").val(),
            quantity: $("#quantity").val(),
        };

        $.post('{{ url("create/shipment_schedule") }}', data, function(result, status, xhr){
            if (result.status == true) {
                $('#example1').DataTable().ajax.reload(null, false);
                openSuccessGritter("Success","New Shipment Schedule has been created.");
            } else {
                openErrorGritter("Error","Shipment Schedule not created.");
            }

            $("#st_month").val("");
            $("#sales_order").val("");
            $("#shipment_condition_code").select2("val", null);
            $("#destination_code").select2("val", null);
            $("#material_number").select2("val", null);
            $("#hpl").select2("val", null);
            $("#st_date").val("");
            $("#bl_date").val("");
            $("#quantity").val("");
        })
    }

    function modalView(id) {
        $("#ViewModal").modal("show");
        var data = {
            id:id
        };

        $.get('{{ url("view/shipment_schedule") }}', data, function(result, status, xhr){
            $("#ship_month_view").text(result.datas[0].st_month);
            $("#ship_week_view").text(result.datas[0].week_name);
            $("#sales_order_view").text(result.datas[0].sales_order);
            $("#ship_condition_view").text(result.datas[0].shipment_condition);
            $("#destination_view").text(result.datas[0].destination);
            $("#material_view").text(result.datas[0].material);
            $("#hpl_view").text(result.datas[0].hpl);
            $("#origin_group_view").text(result.datas[0].origin_group);
            $("#shipment_date_view").text(result.datas[0].st_date);
            $("#bold_view").text(result.datas[0].bl_date);
            $("#quantity_view").text(result.datas[0].quantity);
            $("#created_by_view").text(result.datas[0].name);
            $("#last_updated_view").text(result.datas[0].updated_at);
            $("#created_at_view").text(result.datas[0].created_at);
        })
    }

    function modalEdit(id) {
        $('#EditModal').modal("show");

        var data  = {
            id:id
        };

        $.get('{{ url("edit/shipment_schedule") }}', data, function(result, status, xhr){
            $("#id_edit").val(id);
            $("#st_month_edit").val(result.datas.st_month);
            $("#sales_order_edit").val(result.datas.sales_order);
            $("#shipment_condition_code_edit").val(result.datas.shipment_condition_code).trigger('change.select2');
            $("#destination_code_edit").val(result.datas.destination_code).trigger('change.select2');
            $("#material_number_edit").val(result.datas.material_number).trigger('change.select2');
            $("#hpl_edit").val(result.datas.hpl).trigger('change.select2');
            $("#st_date_edit").val(result.datas.st_date);
            $("#bl_date_edit").val(result.datas.bl_date);
            $("#quantity_edit").val(result.datas.quantity);


        })
    }

    function modalDelete(id, material_number, ship_date) {
        var data = {
            id: id
        };

        if (!confirm("Are you sure want to delete shipment schedule ' "+material_number+" ' in "+ship_date+" ?")) {
            return false;
        }

        $.post('{{ url("delete/shipment_schedule") }}', data, function(result, status, xhr){
            $('#example1').DataTable().ajax.reload(null, false);
            openSuccessGritter("Success","Shipment Scedule ' "+material_number+" ' in "+ship_date+" has been deleted.");
        })
    }

    function edit() {
        var data = {
            id: $("#id_edit").val(),
            st_month: $("#st_month_edit").val(),
            sales_order: $("#sales_order_edit").val(),
            shipment_condition_code: $("#shipment_condition_code_edit").val(),
            destination_code: $("#destination_code_edit").val(),
            material_number: $("#material_number_edit").val(),
            hpl: $("#hpl_edit").val(),
            st_date: $("#st_date_edit").val(),
            bl_date: $("#bl_date_edit").val(),
            quantity: $("#quantity_edit").val()
        };

        $.post('{{ url("edit/shipment_schedule") }}', data, function(result, status, xhr){
            if (result.status == true) {
                $('#example1').DataTable().ajax.reload(null, false);
                openSuccessGritter("Success","Initial Safety Stock has been edited.");
            } else {
                openErrorGritter("Error","Failed to edit initial safety stock.");
            }
        })
    }

    function deleteConfirmation(url, name, id) {
        jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
        jQuery('#modalDeleteButton').attr("href", url+'/'+id);
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