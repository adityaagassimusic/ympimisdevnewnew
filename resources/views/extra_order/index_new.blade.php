@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}" media="print">
    <style type="text/css">
        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            text-align: center;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 3px;
            padding-bottom: 3px;
            padding-left: 2px;
            padding-right: 2px;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
            vertical-align: middle;
        }

        #tableExtraOrderBody>tr:hover {
            background-color: #7dfa8c;
        }

        #tableExtraOrderDetailBody>tr:hover {
            background-color: #7dfa8c;
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
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
            @if (str_contains(Auth::user()->role_code, 'PC') ||
                    str_contains(Auth::user()->role_code, 'MIS') ||
                    str_contains(Auth::user()->role_code, 'PE') ||
                    Auth::user()->role_code == 'BUYER' ||
                    in_array(Auth::user()->username, ['PI2111045', 'PI1612005', 'PI9905001', 'PI9808012']))
                <button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;"
                    onclick="openModalCreate();">Create Extra Order</button>
            @endif
        </h1>
    </section>
@endsection

@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: white; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <input id="role_code" value="{{ Auth::user()->role_code }}" hidden>
        <input id="now" value="{{ $now }}" hidden>
        <div class="row" style="margin-top: 1%;">
            <div class="col-xs-3">
                <table id="resumeTable" class="table table-bordered table-striped table-hover"
                    style="margin-bottom: 5%; height: 17vh;">
                    <thead style="background-color: rgba(126,86,134,.7);">
                        <tr>
                            <th style="text-align: center; width: 50%; font-size: 0.9vw;">Status<br><span
                                    class="text-purple" style="font-weight: normal; font-size: 0.85vw;">状況</span> </th>
                            <th style="text-align: center; width: 50%; font-size: 0.9vw;">Count Extra Order<br><span
                                    class="text-purple" style="font-weight: normal; font-size: 0.85vw;">エキストラオーダーの数</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 1%; font-weight: bold; font-size: 0.9vw;">All<br><span class="text-purple"
                                    style="font-weight: normal; font-size: 0.85vw;">受取人</span> </td>
                            <td id="count_all"
                                style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="width: 1%; background-color: rgb(254, 204, 254); font-weight: bold; font-size: 0.9vw;">
                                Confirming<br><span class="text-purple"
                                    style="font-weight: normal; font-size: 0.85vw;">BOMや価格を確認中</span> </td>
                            <td id="count_confirming"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(254, 204, 254); font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="width: 1%; background-color: rgb(236, 255, 123); font-weight: bold; font-size: 0.9vw;">
                                Waiting PO<br><span class="text-purple"
                                    style="font-weight: normal; font-size: 0.85vw;">発注書待ち</span> </td>
                            <td id="count_po"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(236, 255, 123); font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="width: 1%; background-color: rgb(255, 189, 68); font-weight: bold; font-size: 0.9vw;">
                                Production Process<br><span class="text-purple"
                                    style="font-weight: normal; font-size: 0.85vw;">生産中</span> </td>
                            <td id="count_production"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(255, 189, 68); font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="width: 1%; background-color: rgb(204, 255, 255); font-weight: bold; font-size: 0.9vw;">
                                Delivery Process<br><span class="text-purple"
                                    style="font-weight: normal; font-size: 0.85vw;">出荷工程</span> </td>
                            <td id="count_delivered"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(204, 255, 255); font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="width: 1%; background-color: rgb(34, 204, 125); font-weight: bold; font-size: 0.9vw;">
                                Complete<br><span class="text-purple"
                                    style="font-weight: normal; font-size: 0.85vw;">SAP上書類完了</span> </td>
                            <td id="count_complete"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(34, 204, 125); font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-xs-9">
                <div id="chart1" style="height: 50vh; width: 100%;"></div>
            </div>

            <div class="col-xs-12">
                <div class="nav-tabs-custom" style="margin-top: 1%;">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Extra Order</a>
                        </li>
                        <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Extra Order Detail</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                            <table id="tableExtraOrder" class="table table-bordered" style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 10%;">Extra Order No.</th>
                                        <th style="width: 5%;">Status</th>
                                        <th style="width: 10%;">Submit Date</th>
                                        <th style="width: 10%;">Order By</th>
                                        <th style="width: 10%;">Recipient</th>
                                        <th style="width: 10%;">PO By</th>
                                        <th style="width: 10%;">Price</th>
                                        <th style="width: 10%;">EO Approval</th>
                                        <th style="width: 10%;">PO</th>
                                        <th style="width: 10%;">Production Result</th>
                                        <th style="width: 10%;">Delivered From YMPI</th>
                                        <th style="width: 5%;">Invoice</th>
                                    </tr>
                                </thead>
                                <tbody id="tableExtraOrderBody" style="vertical-align: middle; text-align: center;">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
                            <table id="tableExtraOrderDetail" class="table table-bordered"
                                style="width: 100%; font-size: 0.8vw;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 10%;">Extra Order No.</th>
                                        <th style="width: 10%;">Submit Date</th>
                                        <th style="width: 10%;">Recipient</th>
                                        <th style="width: 10%;">By</th>
                                        <th style="width: 10%;">Material</th>
                                        <th style="width: 10%;">Description</th>
                                        <th style="width: 10%;">Request Date</th>
                                        <th style="width: 10%;">Qty</th>
                                        <th style="width: 10%;">Price</th>
                                        <th style="width: 10%;">Amount</th>
                                        <th style="width: 10%;">Actual Production</th>
                                        <th style="width: 10%;">Act. Deliv. YMPI</th>
                                    </tr>
                                </thead>
                                <tbody id="tableExtraOrderDetailBody" style="vertical-align: middle; text-align: center;">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetailPrice" data-keyboard="false" data-backdrop="static"
        style="overflow-y: auto;">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Detail Sales Price</h4>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="col-xs-12">
                        <table id="tableDetailPrice" class="table table-bordered" style="width: 100%; font-size: 0.8vw;">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="width: 20%;">GMC Buyer</th>
                                    <th style="width: 20%;">GMC YMPI</th>
                                    <th style="width: 50%;">Description</th>
                                    <th style="width: 10%;">Sales Price (USD)</th>
                                </tr>
                            </thead>
                            <tbody id="tableDetailPriceBody" style="vertical-align: middle; text-align: center;">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                        <div>
                            <p>
                                <span class="text-red"><b>*</b></span>&nbsp;: <b><i>Sales price under calculation</i></b>
                            </p>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning pull-right" data-dismiss="modal" aria-label="Close"
                        style="font-weight: bold; font-size: 0.95vw; width: 20%;">BACK<br>戻る</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailAchievement" data-keyboard="false" data-backdrop="static"
        style="overflow-y: auto;">
        <div class="modal-dialog" style="width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Production and Shipment Achievements</h4>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="col-xs-12">
                        <table id="tableDetailAch" class="table table-bordered" style="width: 100%; font-size: 0.8vw;">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="width: 15%;">GMC Buyer</th>
                                    <th style="width: 15%;">GMC YMPI</th>
                                    <th style="width: 50%;">Description</th>
                                    <th style="width: 10%;">Quantity</th>
                                    <th style="width: 10%;">Act. Production</th>
                                    <th style="width: 10%;">Act. Deliv. YMPI</th>
                                </tr>
                            </thead>
                            <tbody id="tableDetailAchBody" style="vertical-align: middle; text-align: center;">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning pull-right" data-dismiss="modal" aria-label="Close"
                        style="font-weight: bold; font-size: 0.95vw; width: 20%;">BACK<br>戻る</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalUpload" data-keyboard="false" data-backdrop="static" style="overflow-y: auto;">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Upload Order</h4>
                    <span>
                        Format Upload (Copy data from Ms. excel) :<br>
                        [<b><i>GMC YMPI</i></b>]
                        [<b><i>MATERIAL DESCRIPTION</i></b>]
                        [<b><i>QTY</i></b>]
                        <br>
                        <br>
                        <b><i>GMC YMPI</i></b> : Fill with the item code with a character length of 7. If the material is
                        new or don't know gmc, fill it with "<b>NEW</b>"<br>
                    </span>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="form-group">
                        <textarea id="upload" style="height: 100px; width: 100%;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close"
                        style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back<br>戻る</button>
                    <button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                        onclick="uploadItem()">CONFIRM<br>確認</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCreate" aria-hidden="true" data-keyboard="false" data-backdrop="static"
        style="overflow-y: auto;">
        <div class="modal-dialog modal-lg" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Create Your Order<br>予約を作成
                        </h3>
                    </center>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                Order By <span class="text-purple">予約者</span> <span class="text-red">*</span>:
                            </label>
                            <div class="col-sm-5" style="padding-left: 0px;">
                                <input class="form-control" type="text" id="orderByName"
                                    value="{{ ucwords($user->name) }}" disabled>
                                <input class="form-control" type="hidden" id="orderById"
                                    value="{{ strtoupper($user->username) }}" disabled>
                            </div>
                        </div>
                        <div class="form-group" id="addBuyerField">
                            <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                Recipient <span class="text-purple">受取人</span> <span class="text-red">*</span>:
                            </label>
                            <div class="col-sm-5" style="padding-left: 0px;">
                                <select class="form-control select2" name="addBuyer"
                                    id="addBuyer"data-placeholder="Select Recipient" style="width: 100%;"
                                    onchange="checkBuyer(value)">
                                    <option></option>
                                    @foreach ($buyers as $buyer)
                                        <option
                                            value="{{ $buyer->attention }}!{{ $buyer->division }}!{{ $buyer->destination_code }}!{{ $buyer->destination_name }}!{{ $buyer->destination_shortname }}!{{ $buyer->currency }}"
                                            selected>{{ $buyer->attention }}</option>
                                    @endforeach
                                    <option value="new_recipient" selected>Select New Recipient</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" align="right" id="newAddBuyerField">
                            <div class="col-sm-5 col-sm-offset-4" style="padding-left: 0px;">
                                <input class="form-control" type="text" id="newAddBuyer" name="newAddBuyer"
                                    placeholder="Input New Recipient ...">
                            </div>
                        </div>
                        <div class="form-group" id="addPoField">
                            <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                PO By <span class="text-purple">発注書作成者</span> <span class="text-red">*</span>:
                            </label>
                            <div class="col-sm-5" style="padding-left: 0px;">
                                <select class="form-control select2" name="addPo" id="addPo"
                                    data-placeholder="Select PO Uploader" style="width: 100%;">
                                    <option></option>
                                    @foreach ($po_uploaders as $po)
                                        <option value="{{ $po->username }}">{{ $po->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="addDestinationField">
                            <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                Destination <span class="text-purple">仕向け</span> <span class="text-red">*</span>:
                            </label>
                            <div class="col-sm-5" style="padding-left: 0px;">
                                <input class="form-control" type="text" id="addDestination" disabled>
                                <input class="form-control" type="hidden" id="addDestinationName" disabled>
                                <input class="form-control" type="hidden" id="addDestinationShortname" disabled>
                                <input class="form-control" type="hidden" id="addCurrency" disabled>
                            </div>
                        </div>
                        <div class="form-group" id="newAddDestinationField">
                            <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                Destination <span class="text-purple">仕向け</span> <span class="text-red">*</span>:
                            </label>
                            <div class="col-sm-5" style="padding-left: 0px;">
                                <select class="form-control select2" name="newAddDestination"
                                    id="newAddDestination"data-placeholder="Select Destination" style="width: 100%;">
                                    <option></option>
                                    @foreach ($destinations as $dt)
                                        <option value="{{ $dt->destination_code }} - {{ $dt->destination_name }}"
                                            selected>{{ $dt->destination_code }} - {{ $dt->destination_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                Division <span class="text-purple">部門</span> <span class="text-red"> :</span>
                            </label>
                            <div class="col-sm-5" style="padding-left: 0px;">
                                <textarea class="form-control" type="text" rows="2" id="addDivision"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                Ship By <span class="text-purple">出荷方法</span> <span class="text-red">*</span>:
                            </label>
                            <div class="col-sm-3" style="padding-left: 0px;">
                                <select style="width: 100%;" class="form-control select2" id="addShipment"
                                    data-placeholder="Select Ship By" style="width: 100%;">
                                    <option></option>
                                    <option value="SEA">SEA</option>
                                    <option value="AIR">AIR</option>
                                    <option value="TRUCK">TRUCK</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                Attachment <span class="text-purple">添付</span><span class="text-red"> :</span>
                            </label>
                            <div class="col-sm-5" style="padding-left: 0px;">
                                <input type="file" id="addAttachment">
                            </div>
                        </div>
                        <div class="form-group">
                            <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                Note <span class="text-purple">備考</span> <span class="text-red"> :</span>
                            </label>
                            <div class="col-sm-5" style="padding-left: 0px;">
                                <textarea class="form-control" type="text" rows="2" id="addRemark"></textarea>
                            </div>
                        </div>

                    </form>
                    <a class="btn btn-primary pull-right" id="uploadItem" onclick="showModalUpload()"
                        style="margin-bottom: 15px; margin-right: 10px;">
                        Upload Item <br> アップロード &nbsp;&nbsp;<i class="fa fa-upload"></i>
                    </a>
                    <table class="table table-hover table-bordered table-striped" id="tableAddItem">
                        <thead style="background-color: rgba(126,86,134,.7);">
                            <tr>
                                <th style="width: 0.5%;">#</th>
                                <th style="width: 0.5%;">Urgent</th>
                                <th style="width: 1%;">GMC Buyer</th>
                                <th style="width: 1%;">GMC YMPI</th>
                                <th style="width: 5%;">Description</th>
                                <th style="width: 1%;">UoM</th>
                                <th style="width: 1%;">Price (USD)</th>
                                <th style="width: 1%;">ETD</th>
                                <th style="width: 1%;">Qty</th>
                                <th style="width: 1%;">Amount</th>
                                <th style="width: 0.1%;">
                                    <button class="btn btn-success btn-xs" onclick="addItem()">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tableAddItemBody">
                        </tbody>
                    </table>
                    <button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close"
                        style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back<br>戻る</button>
                    <button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                        onclick="confirmOrder()">CONFIRM<br>確認</button>
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
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $(function() {
                $('.select2').select2({
                    dropdownParent: $('#modalCreate')
                });
            });

            fetchExtraOrder();

        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var countAddItem = 0;
        var countAddItems = [];
        var materials = <?php echo json_encode($materials); ?>;
        var buyers = <?php echo json_encode($buyers); ?>;
        var eo_details = [];
        var eo_approvals = [];

        function openModalCreate() {
            $("#addBuyer").prop('selectedIndex', 0).change();
            $('#modalCreate').modal('show');
            $('#addDestination').val("");
            $('#addDestinationName').val("");
            $('#addDestinationShortname').val("");
            $('#addDivision').val("");
            $('#addRemark').val("");
            $('#addCurrency').val("");
            $('#addAttachment').val("");
            $('#tableAddItemBody').html("");
            $('#addDivision').prop('disabled', 'true');
            $("#addShipment").prop('selectedIndex', 0).change();

            $('#addDestinationField').css('display', 'block');

            $('#newAddBuyer').val("");
            $("#newAddDestination").prop('selectedIndex', 0).change();

            $('#newAddBuyerField').css('display', 'none');
            $('#newAddDestinationField').css('display', 'none');

            countAddItem = 0;
            countAddItems = [];

            addItem();

        }

        function checkBuyer(val) {
            var buyer = val.split('!');

            $('#addDestination').val(buyer[2] + ' - ' + buyer[3]);
            $('#addDestinationName').val(buyer[3]);
            $('#addDestinationShortname').val(buyer[4]);
            $('#addDivision').val(buyer[1]);
            $('#addCurrency').val(buyer[5]);
        }

        function checkMaterial(id, val) {
            var id_number = id.split('_');
            var content = val.split('!');

            if (val == '-') {
                return false;
            } else {
                if (content[0] != 'NEW') {
                    checkQuantity(id_number[2]);
                    $('#create_description_' + id_number[2]).prop('disabled', true);
                    $('#create_description_' + id_number[2]).val(content[2]);
                    $('#create_uom_' + id_number[2]).val(content[3]);
                    $('#create_price_' + id_number[2]).val(content[5]);

                    if (id_number[1] == 'materialbuyer') {
                        var check1 = $('#create_materialympi_' + id_number[2]).val();
                        var check2 = content[0] + '!' + content[1] + '!' + content[2] + '!' + content[3] + '!' + content[
                            4] + '!' + content[5] + '!' + content[6];

                        if (check1 != check2) {
                            var value = content[0] + '!' + content[1] + '!' + content[2] + '!' + content[3] + '!' + content[
                                4] + '!' + content[5] + '!' + content[6];
                            $('#create_materialympi_' + id_number[2]).val(value).trigger('change.select2');
                        }

                    } else {
                        var check1 = $('#create_materialbuyer_' + id_number[2]).val();
                        var check2 = content[0] + '!' + content[1] + '!' + content[2] + '!' + content[3] + '!' + content[
                            4] + '!' + content[5] + '!' + content[6];

                        if (content[1] == '-') {
                            $('#create_materialbuyer_' + id_number[2]).val('-').trigger('change.select2');

                        } else {
                            if (check1 != check2) {
                                var value = content[0] + '!' + content[1] + '!' + content[2] + '!' + content[3] + '!' +
                                    content[4] + '!' + content[5] + '!' + content[6];
                                $('#create_materialbuyer_' + id_number[2]).val(value).trigger('change.select2');
                            }
                        }
                    }

                    return false;
                } else {
                    checkQuantity(id_number[2]);
                    $('#create_description_' + id_number[2]).val("");
                    $('#create_uom_' + id_number[2]).val("");
                    $('#create_price_' + id_number[2]).val("");
                    $('#create_description_' + id_number[2]).prop('disabled', false);
                    if ($('#create_materialbuyer_' + id_number[2]).val() != 'NEW') {
                        $('#create_materialbuyer_' + id_number[2]).val('NEW').trigger('change.select2');
                    }
                    if ($('#create_materialympi_' + id_number[2]).val() != 'NEW') {
                        $('#create_materialympi_' + id_number[2]).val('NEW').trigger('change.select2');
                    }
                    return false;
                }
            }

        }

        function confirmOrder() {
            if (confirm("Are you sure to submit this extra order request?")) {
                $('#loading').show();

                var material_buyer = "";
                var material_ympi = "";
                var description = "";
                var uom = "";
                var price = 0;
                var etd = "";
                var ship_by = "";
                var qty = 0;
                var amount = 0;

                var order_by_id = $('#orderById').val();
                var po_by = $('#addPo').val();
                var buyer = $('#addBuyer').val();
                var new_buyer = $('#newAddBuyer').val();
                var destination_code = $('#addDestination').val();
                var destination_name = $('#addDestinationName').val();
                var destination_shortname = $('#addDestinationShortname').val();
                var currency = $('#addCurrency').val();
                var division = $('#addDivision').val();
                var shipment = $('#addShipment').val();
                var remark = $('#addRemark').val();

                if (buyer == '') {
                    audio_error.play();
                    openErrorGritter('Error!', 'Please select recipient<br>宛先を選んでください');
                    $('#loading').hide();
                    return false;
                }

                if (po_by == '') {
                    audio_error.play();
                    openErrorGritter('Error!', 'Please select PIC PO<br>PO担当者を選んでください');
                    $('#loading').hide();
                    return false;
                }

                if (destination_code == '') {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!', 'Please select destination<br>仕向け地を選んでください');
                    $('#loading').hide();
                    return false;
                }

                if (buyer == 'new_recipient') {
                    buyer = $('#newAddBuyer').val();

                    if (buyer == '') {
                        audio_error.play();
                        openErrorGritter('Error!', 'Please enter enter recipient name<br>宛先の名前を入れてください');
                        $('#loading').hide();
                        return false;
                    }

                    destination_code = $('#newAddDestination').val();
                    if (destination_code == '') {
                        $('#loading').hide();
                        audio_error.play();
                        openErrorGritter('Error!', 'Please select destination<br>仕向け地を選んでください');
                        $('#loading').hide();
                        return false;
                    }

                }

                var formData = new FormData();
                var attachment = $('#addAttachment').prop('files')[0];
                var file = $('#addAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

                var extension = file[file.length - 1];
                var file_name = '';
                for (let i = 0; i < (file.length - 1); i++) {
                    file_name += file[i];
                    if (i != (file.length - 2)) {
                        file_name += ' ';
                    }
                }


                formData.append('order_by_id', order_by_id);
                formData.append('buyer', buyer);
                formData.append('po_by', po_by);
                formData.append('destination_code', destination_code);
                formData.append('destination_name', destination_name);
                formData.append('destination_shortname', destination_shortname);
                formData.append('currency', currency);
                formData.append('remark', remark);
                formData.append('division', division);
                formData.append('attachment', attachment);
                formData.append('shipment', shipment);
                formData.append('extension', extension);
                formData.append('file_name', file_name);

                var status = true;
                var message = '';

                $.each(countAddItems, function(key, value) {
                    var material_ympi = $('#create_materialympi_' + value).val();
                    var description = $('#create_description_' + value).val();
                    var uom = $('#create_uom_' + value).val();
                    var price = $('#create_price_' + value).val();
                    var etd = $('#create_requestdate_' + value).val();
                    var qty = $('#create_quantity_' + value).val();
                    var amount = $('#create_amount_' + value).val();
                    var urgent = false;

                    if ($('#urgent_' + value).is(":checked")) {
                        urgent = true;
                    }

                    if (qty == "" || qty == 0) {
                        status = false;
                        message = 'Quantity can not be zero <br>数量に「0」の記入が不可能';
                    }

                    if (description == "") {
                        message = 'Description can not be blank <br>説明の記入は必須';
                        status = false;
                    }

                    if (etd == "") {
                        message = 'ETD can not be blank <br>ETDの記入は必須';
                        status = false;
                    }

                    formData.append('order_lists[' + key + ']', material_ympi + '!!' + description + '!!' + uom +
                        '!!' + price + '!!' + etd + '!!' + qty + '!!' + amount + '!!' + urgent);
                });

                if (!status) {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!', message);
                    order_lists = [];
                    $('#loading').hide();
                    return false;
                }

                $.ajax({
                    url: "{{ url('input/extra_order') }}",
                    method: "POST",
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        if (data.status) {
                            clearAll();
                            fetchExtraOrder()
                            $('#loading').hide();
                            openSuccessGritter('Success!', 'Your request has been submitted <br>リクエスト提出が完了');
                            audio_ok.play();
                        } else {
                            $('#loading').hide();
                            openErrorGritter('Error!', data.message);
                            audio_error.play();
                        }

                    }
                });
            } else {
                return false;
            }
        }

        function remMaterial(id) {
            countAddItems.splice(countAddItems.indexOf(id), 1);
            $('#create_item_' + id).remove();

            var new_index = 0;
            for (let i = 0; i < countAddItems.length; i++) {
                $('#order_' + countAddItems[i]).html(++new_index);
            }

        }

        $('#modalUpload').on('hidden.bs.modal', function() {
            $('#modalCreate').modal('show');

        });

        function showModalUpload() {
            $('#modalCreate').modal('hide');
            $('#modalUpload').modal('show');
            $('#upload').val('');
        }

        function uploadItem() {

            var upload = $('#upload').val();
            var data = {
                upload: upload
            }

            $('#loading').show();
            $.post('{{ url('fetch/extra_order/generate_upload_data') }}', data, function(result, status, xhr) {
                if (result.status) {

                    var updateRow = countAddItem;
                    for (var i = 0; i < result.data.length; i++) {

                        var is_new = true;
                        if (result.data[i].material_number != 'NEW') {
                            var value = '';
                            for (var j = 0; j < materials.length; j++) {
                                if (materials[j].material_number == result.data[i].material_number) {
                                    is_new = false;
                                    value = materials[j].material_number + '!' + materials[j]
                                        .material_number_buyer + '!' + materials[j].description + '!' + materials[j]
                                        .uom + '!' + materials[j].hpl + '!' + materials[j].sales_price + '!' +
                                        materials[j].storage_location;
                                    break;
                                }
                            }
                        }

                        addItem();

                        if (is_new) {
                            $('#create_materialympi_' + updateRow).val('NEW').trigger('change.select2');
                            $('#create_description_' + updateRow).val(result.data[i].material_description);
                        } else {
                            $('#create_materialympi_' + updateRow).val(value).trigger('change.select2');
                        }

                        $('#create_quantity_' + updateRow).val(result.data[i].quantity);

                        checkQuantity(updateRow);
                        updateRow++;

                    }

                    var new_index = 0;
                    for (let i = 0; i < countAddItems.length; i++) {
                        $('#order_' + countAddItems[i]).html(++new_index);
                    }

                    $('#modalUpload').modal('hide');
                    $('#modalCreate').modal('show');

                    $('#loading').hide();

                } else {
                    audio_error.play();
                    openErrorGritter('Error!', result.message);
                    return false;
                }
            });
        }

        function addItem() {
            var tableAddItem = "";
            tableAddItem += '<tr id="create_item_' + countAddItem + '">';

            tableAddItem += '<td style="vertical-align: middle; text-align: center; font-weight: bold;" ';
            tableAddItem += 'id="order_' + countAddItem + '">';
            tableAddItem += (countAddItems.length + 1);
            tableAddItem += '</td>';

            tableAddItem += '<td>';
            tableAddItem += '<center>';
            tableAddItem += '<input type="checkbox" class="minimal" id="urgent_' + countAddItem +
                '" onchange="checkUrgent(id)">';
            tableAddItem += '</center>';
            tableAddItem += '</td>';

            tableAddItem += '<td>';
            tableAddItem += '<div id="selectGmcBuyer' + countAddItem + '">';
            tableAddItem += '<select style= "width: 100%;" class="select2" id="create_materialbuyer_' + countAddItem +
                '" onchange="checkMaterial(id, value)">';
            tableAddItem += '<option value="NEW">NEW</option>';
            tableAddItem += '<option value="-">-</option>';
            $.each(materials, function(key, value) {
                if (value.material_number_buyer != "" && value.material_number_buyer != null && value
                    .material_number_buyer != "NEW" && value.material_number_buyer != "-") {
                    tableAddItem += '<option value="' + value.material_number + '!' + value.material_number_buyer +
                        '!' + value.description + '!' + value.uom + '!' + value.hpl + '!' + value.sales_price +
                        '!' + value.storage_location + '">' + value.material_number_buyer + '</option>';
                }
            });
            tableAddItem += '</select>';
            tableAddItem += '</div>';
            tableAddItem += '</td>';


            tableAddItem += '<td>';
            tableAddItem += '<div id="selectGmcYmpi' + countAddItem + '">';
            tableAddItem += '<select style= "width: 100%;" class="select2" id="create_materialympi_' + countAddItem +
                '" onchange="checkMaterial(id, value)">';
            tableAddItem += '<option value="NEW">NEW</option>';
            $.each(materials, function(key, value) {
                if (value.material_number != "" && value.material_number != null && value.material_number !=
                    "NEW") {
                    tableAddItem += '<option value="' + value.material_number + '!' + value.material_number_buyer +
                        '!' + value.description + '!' + value.uom + '!' + value.hpl + '!' + value.sales_price +
                        '!' + value.storage_location + '">' + value.material_number + '</option>';
                }
            });
            tableAddItem += '</select>';
            tableAddItem += '</div>';
            tableAddItem += '</td>';


            tableAddItem += '<td><input type="text" class="form-control" id="create_description_' + countAddItem +
                '"></td>';
            tableAddItem += '<td><input type="text" class="form-control" id="create_uom_' + countAddItem +
                '" disabled></td>';
            tableAddItem +=
                '<td><input style="text-align: right;" type="text" class="form-control" onchange="checkQuantity(' +
                countAddItem + ')" id="create_price_' + countAddItem + '" disabled></td>';
            tableAddItem +=
                '<td><input style="text-align: right;" type="text" class="form-control datepicker" id="create_requestdate_' +
                countAddItem + '" disabled></td>';


            tableAddItem += '<td><input style="text-align: right;" type="text" class="form-control" id="create_quantity_' +
                countAddItem + '" onkeyup="checkQuantity(' + countAddItem + ')"></td>';
            tableAddItem += '<td><input style="text-align: right;" type="text" class="form-control" id="create_amount_' +
                countAddItem + '" value="0" disabled></td>';
            tableAddItem += '<td style="text-align: center;"><button class="btn btn-danger btn-xs" onclick="remMaterial(' +
                countAddItem + ')" id="tes"><i class="fa fa-trash"></i></button></td>';
            tableAddItem += '</tr>';

            $('#tableAddItem').append(tableAddItem);

            $('#create_requestdate_' + countAddItem + '').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd"
            });

            $('.select2').select2({
                dropdownParent: $('#tableAddItem'),
            });

            var now = new Date($("#now").val());
            var request_date = now.setDate(now.getDate() + 60);
            $("#create_requestdate_" + countAddItem).prop('disabled', true);
            $("#create_requestdate_" + countAddItem).val(formatDate(request_date));

            countAddItems.push(countAddItem);
            countAddItem += 1;


        }

        $("#addBuyer").change(function() {
            var buyer = $(this).val();

            if (buyer == 'new_recipient') {
                $('#addDestinationField').css('display', 'none');

                $('#newAddBuyerField').css('display', 'block');
                $('#newAddDestinationField').css('display', 'block');
                $('#addDivision').prop('disabled', false);

                $('#newAddBuyer').focus();

            } else {
                $('#addDestinationField').css('display', 'block');

                $('#newAddBuyerField').css('display', 'none');
                $('#newAddDestinationField').css('display', 'none');
                $('#addDivision').prop('disabled', true);

                $('#newAddBuyer').val("");
                $("#newAddDestination").prop('selectedIndex', 0).change();

            }

        });

        function clearAll() {
            $('#modalCreate').modal('hide');
            $("#addBuyer").prop('selectedIndex', 0).change();
            $("#addPo").prop('selectedIndex', 0).change();
            $('#addDestination').val("");
            $('#addDestinationName').val("");
            $('#addDestinationShortname').val("");
            $('#addDivision').val("");
            $('#addRemark').val("");
            $('#addCurrency').val("");
            $('#addAttachment').val("");
            countAddItem = 0;
            countAddItems = [];
            $('#tableAddItemBody').html("");
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


        function checkUrgent(id) {
            var row = id.split('_')[1];

            if ($('#' + id).is(":checked")) {
                $("#create_requestdate_" + row).prop('disabled', false);
                $("#create_requestdate_" + row).val('');

            } else {
                var now = new Date($("#now").val());
                var request_date = now.setDate(now.getDate() + 60);
                $("#create_requestdate_" + row).prop('disabled', true);
                $("#create_requestdate_" + row).val(formatDate(request_date));

            }

        }

        function checkQuantity(id) {
            if ($('#create_quantity_' + id).val().match(/^((\d+(\.\d *)?)|((\d*\.)?\d+))$/)) {
                var amount = $('#create_price_' + id).val() * $('#create_quantity_' + id).val();
                $('#create_amount_' + id).val(amount.toFixed(2));
            } else if ($('#create_quantity_' + id).val() == "") {
                return false;
            } else {
                $('#loading').hide();
                audio_error.play();
                openErrorGritter('Error!', 'Please Enter Numeric Value<br>数字を入れてください');
                return false;
            }
        }

        function truncate(str, n) {
            return (str.length > n) ? str.substr(0, n - 1) + '&hellip;' : str;
        };

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

        function detailExtraOrder(eo_number) {
            window.open('{{ url('index/extra_order/detail') }}' + '/' + eo_number, '_blank');
        }

        function ivFormatter(invoice) {
            var invoices = invoice.split(',');
            var content = '';

            for (let i = 0; i < invoices.length; i++) {
                content += invoices[i];
                if (i != (invoice.length - 1)) {
                    content += '<br>';
                }

            }
            return content;
        }


        function poNumberFormatter(po_number, eo_number) {
            var content = '';
            var style = 'style="font-weight: bold; cursor: pointer;"';

            var obj = JSON.parse(po_number);

            for (var i = 0; i < obj.length; i++) {
                content += '<a ' + style + ' onclick="downloadPo(\'' + obj[i] + '\')">' + obj[i].replace(eo_number + '__',
                    '').split('.')[0] + '</a>';

                if (i != obj.length - 1) {
                    content += '<br>';
                }
            }
            return content;
        }

        function downloadPo(po_number) {

            var data = {
                po_number: po_number
            }

            $.get('{{ url('index/extra_order/po_number/') }}', data, function(result, status, xhr) {
                if (result.status) {
                    window.open(result.file_path);
                } else {
                    openErrorGritter('Error!', 'Attempt to retrieve data failed <br>データ取得が失敗');
                }
            });

        }

        function detailPrice(eo_number) {
            $('#tableDetailPrice').DataTable().clear();
            $('#tableDetailPrice').DataTable().destroy();
            $('#tableDetailPriceBody').html("");

            var tableDetailPriceBody = "";
            for (var i = 0; i < eo_details.length; i++) {
                if (eo_details[i].eo_number == eo_number) {
                    tableDetailPriceBody += '<tr>';
                    tableDetailPriceBody += '<td style="width: 15%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
                        .material_number_buyer + '</td>';
                    tableDetailPriceBody += '<td style="width: 15%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
                        .material_number + '</td>';
                    tableDetailPriceBody += '<td style="width: 50%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
                        .description + '</td>';

                    var red = 'background-color: #ffccff;';
                    if (eo_details[i].sales_price <= 0) {
                        tableDetailPriceBody += '<td style="' + red + 'width: 15%; padding-left: 1%; padding-right: 1%;">' +
                            eo_details[i].sales_price + ' <span class="text-red"><b>*</b></span</td>';
                    } else {
                        tableDetailPriceBody += '<td style="width: 15%; padding-left: 1%; padding-right: 1%;">' +
                            eo_details[i].sales_price + '</td>';
                    }
                    tableDetailPriceBody += '</tr>';
                }
            }
            $('#tableDetailPriceBody').append(tableDetailPriceBody);

            $('#tableDetailPrice').DataTable({
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
                    }, {
                        extend: 'copy',
                        className: 'btn btn-success',
                        text: '<i class="fa fa-copy"></i> Copy',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }]
                },
                'ordering': false,
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                'columnDefs': [{
                        "targets": 2,
                        "className": "text-left",
                    },
                    {
                        "targets": 3,
                        "className": "text-right",
                    }
                ]
            });
            $('#modalDetailPrice').modal('show');


        }

        function detailAchievement(eo_number) {
            $('#tableDetailAch').DataTable().clear();
            $('#tableDetailAch').DataTable().destroy();
            $('#tableDetailAchBody').html("");

            var tableDetailAchBody = "";
            for (var i = 0; i < eo_details.length; i++) {
                if (eo_details[i].eo_number == eo_number) {
                    tableDetailAchBody += '<tr>';
                    tableDetailAchBody += '<td style="width: 15%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
                        .material_number_buyer + '</td>';
                    tableDetailAchBody += '<td style="width: 15%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
                        .material_number + '</td>';
                    tableDetailAchBody += '<td style="width: 40%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
                        .description + '</td>';
                    tableDetailAchBody += '<td style="width: 10%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
                        .quantity + '</td>';
                    tableDetailAchBody += '<td style="width: 10%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
                        .production_quantity + '</td>';
                    tableDetailAchBody += '<td style="width: 10%; padding-left: 1%; padding-right: 1%;">' + eo_details[i]
                        .shipment_quantity + '</td>';
                    tableDetailAchBody += '</tr>';
                }
            }
            $('#tableDetailAchBody').append(tableDetailAchBody);

            $('#tableDetailAch').DataTable({
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
                    }, {
                        extend: 'copy',
                        className: 'btn btn-success',
                        text: '<i class="fa fa-copy"></i> Copy',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }]
                },
                'ordering': false,
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                'columnDefs': [{
                        "targets": 2,
                        "className": "text-left",
                    },
                    {
                        "targets": [3, 4, 5],
                        "className": "text-right",
                    }
                ]
            });
            $('#modalDetailAchievement').modal('show');


        }

        function fetchExtraOrder() {

            var data = {

            }

            $.get('{{ url('fetch/extra_order') }}', data, function(result, status, xhr) {
                if (result.status) {

                    eo_details = result.eo_details;
                    eo_approvals = result.eo_approvals;

                    $('#tableExtraOrder').DataTable().clear();
                    $('#tableExtraOrder').DataTable().destroy();
                    $('#tableExtraOrderBody').html("");

                    var role_code = $('#role_code').val();

                    var tableExtraOrderBody = "";
                    var style = 'style="cursor: pointer;"';

                    $.each(result.extra_orders, function(key, value) {
                        tableExtraOrderBody += '<tr>';

                        tableExtraOrderBody += '<td style="width: 10%; ';
                        tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">';
                        tableExtraOrderBody += '<a onclick="detailExtraOrder(\'' + value.eo_number + '\')"';
                        tableExtraOrderBody += 'style="font-weight: bold; cursor: pointer;">';
                        tableExtraOrderBody += value.eo_number;
                        tableExtraOrderBody += '</a>';
                        tableExtraOrderBody += '</td>';

                        tableExtraOrderBody += '<td style="width: 10%; ';
                        tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">';
                        tableExtraOrderBody += value.status;
                        tableExtraOrderBody += '</td>';

                        tableExtraOrderBody += '<td style="width: 7.5%; ';
                        tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">';
                        tableExtraOrderBody += value.submit_date;
                        tableExtraOrderBody += '</td>';

                        for (let i = 0; i < result.eo_users.length; i++) {
                            if (value.eo_number == result.eo_users[i].eo_number) {
                                var destination = 'YMPI';
                                if (result.eo_users[i].order_by.length < '10') {
                                    for (let j = 0; j < result.eo_buyers.length; j++) {
                                        if (result.eo_buyers[j].attention == result.eo_users[i]
                                            .order_by_name) {
                                            destination = result.eo_buyers[j].destination_shortname;
                                            break;
                                        }
                                    }
                                }

                                tableExtraOrderBody += '<td style="width: 12.5%; text-align; left; ';
                                tableExtraOrderBody += 'padding-left: 0.5%; padding-right: 0.5%;">';
                                tableExtraOrderBody += result.eo_users[i].order_by_name + '<br>';
                                tableExtraOrderBody += destination;
                                tableExtraOrderBody += '</td>';
                                break;
                            }
                        }

                        tableExtraOrderBody += '<td style="width: 12.5%; text-align; left; ';
                        tableExtraOrderBody += 'padding-left: 0.5%; padding-right: 0.5%;">';
                        tableExtraOrderBody += value.attention + '<br>' + value.destination_shortname;
                        tableExtraOrderBody += '</td>';

                        for (let i = 0; i < result.eo_users.length; i++) {
                            if (value.eo_number == result.eo_users[i].eo_number) {
                                var destination = 'YMPI';
                                if (result.eo_users[i].po_by.length < '10') {
                                    for (let j = 0; j < result.eo_buyers.length; j++) {
                                        if (result.eo_buyers[j].attention == result.eo_users[i]
                                            .po_by_name) {
                                            destination = result.eo_buyers[j].destination_shortname;
                                            break;
                                        }
                                    }
                                }

                                tableExtraOrderBody += '<td style="width: 12.5%; text-align; left; ';
                                tableExtraOrderBody += 'padding-left: 0.5%; padding-right: 0.5%;">';
                                tableExtraOrderBody += result.eo_users[i].po_by_name + '<br>';
                                tableExtraOrderBody += destination;
                                tableExtraOrderBody += '</td>';
                                break;
                            }
                        }

                        var count_material = 0;
                        var price_complete = 0;
                        for (var i = 0; i < result.eo_details.length; i++) {
                            if (value.eo_number == result.eo_details[i].eo_number) {
                                count_material++;
                                if (result.eo_details[i].sales_price > 0) {
                                    price_complete++;
                                }
                            }
                        }
                        var price_progress = Math.round(parseFloat(price_complete / count_material * 100));
                        tableExtraOrderBody += '<td style="width: 7.5%; ';
                        tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">';
                        tableExtraOrderBody += '<a onclick="detailPrice(\'' + value.eo_number + '\')" ';
                        tableExtraOrderBody += style + '>';
                        tableExtraOrderBody += price_complete + '/' + count_material;
                        tableExtraOrderBody += '</a><br>(<b>' + price_progress + '%</b>)';
                        tableExtraOrderBody += '</td>';

                        var approval_status = '';
                        for (var i = 0; i < result.eo_approvals.length; i++) {
                            if (value.eo_number == result.eo_approvals[i].eo_number) {
                                approval_status = result.eo_approvals[i].status;
                                break;
                            }
                        }

                        if (approval_status != 'Not submitted yet' &&
                            (role_code.includes('PC') || role_code.includes('MIS'))) {
                            tableExtraOrderBody += '<td style="width: 10%; ';
                            tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">';
                            tableExtraOrderBody += '<a onclick="detailExtraOrder(\''
                            tableExtraOrderBody += value.eo_number + '\')" ';
                            tableExtraOrderBody += 'style="font-weight: bold; cursor: pointer;">';
                            tableExtraOrderBody += approval_status;
                            tableExtraOrderBody += '</a></td>';
                        } else {
                            tableExtraOrderBody += '<td style="width: 10%; ';
                            tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">';
                            tableExtraOrderBody += approval_status;
                            tableExtraOrderBody += '</td>';
                        }

                        if (value.po_number != null) {
                            tableExtraOrderBody += '<td style="width: 10%; ';
                            tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">';
                            tableExtraOrderBody += poNumberFormatter(value.po_number, value.eo_number);
                            tableExtraOrderBody += '</td>';
                        } else {
                            tableExtraOrderBody += '<td style="width: 10%; ';
                            tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">-';
                            tableExtraOrderBody += '</td>';
                        }


                        var sum_material = 0;
                        var prod = 0;
                        var ship = 0;
                        for (var i = 0; i < result.eo_details.length; i++) {
                            if (value.eo_number == result.eo_details[i].eo_number) {
                                sum_material += result.eo_details[i].quantity;
                                prod += result.eo_details[i].production_quantity;
                                ship += result.eo_details[i].shipment_quantity;
                            }
                        }
                        var prod_progress = Math.round(parseFloat(prod / sum_material * 100));
                        var ship_progress = Math.round(parseFloat(ship / sum_material * 100));

                        tableExtraOrderBody += '<td style="width: 10%; ';
                        tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">';
                        tableExtraOrderBody += '<a onclick="detailAchievement(\'' + value.eo_number;
                        tableExtraOrderBody += '\')" ' + style + '>';
                        tableExtraOrderBody += prod + '/' + sum_material + '</a>';
                        tableExtraOrderBody += '<br>(<b>' + prod_progress + '%</b>)';
                        tableExtraOrderBody += '</td>';

                        tableExtraOrderBody += '<td style="width: 10%; ';
                        tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">';
                        tableExtraOrderBody += '<a onclick="detailAchievement(\'' + value.eo_number;
                        tableExtraOrderBody += '\')" ' + style + '>';
                        tableExtraOrderBody += ship + '/' + sum_material + '</a>';
                        tableExtraOrderBody += '<br>(<b>' + ship_progress + '%</b>)';
                        tableExtraOrderBody += '</td>';

                        // tableExtraOrderBody += '<td style="width: 5%;">' + (value.iv_number || '-') + '</td>';

                        var invoice = '-';
                        for (let i = 0; i < result.eo_invoices.length; i++) {
                            if (value.eo_number == result.eo_invoices[i].eo_number) {
                                invoice = result.eo_invoices[i].invoice_number;
                            }
                        }
                        tableExtraOrderBody += '<td style="width: 5%; ';
                        tableExtraOrderBody += 'padding-left: 0.3%; padding-right: 0.3%;">';
                        tableExtraOrderBody += ivFormatter(invoice);
                        tableExtraOrderBody += '</td>';

                        tableExtraOrderBody += '</tr>';

                    });

                    $('#tableExtraOrderBody').append(tableExtraOrderBody);

                    $('#tableExtraOrder').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        'ordering': false,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        'columnDefs': [{
                                "targets": [3, 4, 5],
                                "className": "text-left",
                            },
                            {
                                "targets": [6, 9, 10],
                                "className": "text-right",
                            }
                        ]
                    });

                    $('#tableExtraOrderDetail').DataTable().clear();
                    $('#tableExtraOrderDetail').DataTable().destroy();
                    $('#tableExtraOrderDetailBody').html("");
                    var tableExtraOrderDetailBody = "";

                    for (var i = 0; i < result.eo_details.length; i++) {
                        tableExtraOrderDetailBody += '<tr>';
                        tableExtraOrderDetailBody +=
                            '<td style="width: 8%; padding-left: 0.3%; padding-right: 0.3%;">' + result.eo_details[
                                i].eo_number + '</td>';

                        for (var j = 0; j < result.extra_orders.length; j++) {
                            if (result.extra_orders[j].eo_number == result.eo_details[i].eo_number) {
                                tableExtraOrderDetailBody +=
                                    '<td style="width: 6%; padding-left: 0.3%; padding-right: 0.3%;">' + result
                                    .extra_orders[j].submit_date + '</td>';
                                break;
                            }
                        }

                        for (var j = 0; j < result.extra_orders.length; j++) {
                            if (result.extra_orders[j].eo_number == result.eo_details[i].eo_number) {
                                tableExtraOrderDetailBody +=
                                    '<td style="width: 10%; padding-left: 0.3%; padding-right: 0.3%;">' + result
                                    .extra_orders[j].attention + '<br>' + result.extra_orders[j]
                                    .destination_shortname + '</td>';
                                break;
                            }
                        }

                        tableExtraOrderDetailBody +=
                            '<td style="width: 5%; padding-left: 0.3%; padding-right: 0.3%;">' + result.eo_details[
                                i].shipment_by + '</td>';
                        tableExtraOrderDetailBody +=
                            '<td style="width: 5%; padding-left: 0.3%; padding-right: 0.3%;">' + result.eo_details[
                                i].material_number + '</td>';
                        tableExtraOrderDetailBody +=
                            '<td style="width: 25%; padding-left: 0.3%; padding-right: 0.3%;">' + result.eo_details[
                                i].description + '</td>';
                        tableExtraOrderDetailBody +=
                            '<td style="width: 6%; padding-left: 0.3%; padding-right: 0.3%;">' + result.eo_details[
                                i].request_date_formated + '</td>';
                        tableExtraOrderDetailBody +=
                            '<td style="width: 5%; padding-left: 0.3%; padding-right: 0.3%;">' + result.eo_details[
                                i].quantity + '</td>';
                        tableExtraOrderDetailBody +=
                            '<td style="width: 5%; padding-left: 0.3%; padding-right: 0.3%;">' + result.eo_details[
                                i].sales_price + '</td>';
                        tableExtraOrderDetailBody +=
                            '<td style="width: 5%; padding-left: 0.3%; padding-right: 0.3%;">' + Math.round((result
                                .eo_details[i].quantity * result.eo_details[i].sales_price), 2) + '</td>';
                        tableExtraOrderDetailBody +=
                            '<td style="width: 5%; padding-left: 0.3%; padding-right: 0.3%;">' + result.eo_details[
                                i].production_quantity + '</td>';
                        tableExtraOrderDetailBody +=
                            '<td style="width: 5%; padding-left: 0.3%; padding-right: 0.3%;">' + result.eo_details[
                                i].shipment_quantity + '</td>';
                        tableExtraOrderDetailBody += '</tr>';


                    }

                    $('#tableExtraOrderDetailBody').append(tableExtraOrderDetailBody);

                    $('#tableExtraOrderDetail').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        'ordering': false,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        'columnDefs': [{
                                "targets": 2,
                                "className": "text-left",
                            },
                            {
                                "targets": 5,
                                "className": "text-left",
                            },
                            {
                                "targets": 7,
                                "className": "text-right",
                            },
                            {
                                "targets": 8,
                                "className": "text-right",
                            },
                            {
                                "targets": 9,
                                "className": "text-right",
                            },
                            {
                                "targets": 10,
                                "className": "text-right",
                            },
                            {
                                "targets": 11,
                                "className": "text-right",
                            }
                        ]
                    });


                    var confirming = 0;
                    var po = 0;
                    var production = 0;
                    var delivered = 0;
                    var complete = 0;

                    for (var k = 0; k < result.extra_orders.length; k++) {
                        if (result.extra_orders[k].status == 'Confirming') {
                            confirming++;
                        } else if (result.extra_orders[k].status == 'Waiting PO') {
                            po++;
                        } else if (result.extra_orders[k].status == 'Production Process') {
                            production++;
                        } else if (result.extra_orders[k].status == 'Delivery Process') {
                            delivered++;
                        } else if (result.extra_orders[k].status == 'Complete') {
                            complete++;
                        }
                    }


                    $('#count_all').text((confirming + production + delivered + complete));
                    $('#count_confirming').text(confirming);
                    $('#count_po').text(po);
                    $('#count_production').text(production);
                    $('#count_delivered').text(delivered);
                    $('#count_complete').text(complete);


                    var xCategories = [];
                    var confirming = [];
                    var po = [];
                    var production = [];
                    var delivered = [];
                    var complete = [];

                    for (var i = 0; i < result.calendars.length; i++) {
                        xCategories.push(result.calendars[i].month_text);
                        confirming.push(0);
                        po.push(0);
                        production.push(0);
                        delivered.push(0);
                        complete.push(0);
                    }

                    for (var i = 0; i < result.calendars.length; i++) {
                        for (var j = 0; j < result.eo_details.length; j++) {
                            if (result.calendars[i].month == result.eo_details[j].request_date.substr(0, 7)) {
                                var status = '';
                                for (var k = 0; k < result.extra_orders.length; k++) {
                                    if (result.extra_orders[k].eo_number == result.eo_details[j].eo_number) {
                                        status = result.extra_orders[k].status;
                                        break;
                                    }
                                }

                                if (status == 'Confirming') {
                                    confirming[i] += result.eo_details[j].quantity;
                                } else if (status == 'Waiting PO') {
                                    po[i] += result.eo_details[j].quantity;
                                } else if (status == 'Production Process') {
                                    production[i] += result.eo_details[j].quantity;
                                } else if (status == 'Delivery Process') {
                                    delivered[i] += result.eo_details[j].quantity;
                                } else if (status == 'Complete') {
                                    complete[i] += result.eo_details[j].quantity;
                                }
                            }
                        }
                    }


                    Highcharts.chart('chart1', {
                        chart: {
                            backgroundColor: null,
                            type: 'column',
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            title: {
                                text: 'Request Month',
                                style: {
                                    fontWeight: 'bold',
                                },
                            },
                            tickInterval: 1,
                            gridLineWidth: 1,
                            categories: xCategories,
                            crosshair: true
                        },
                        yAxis: [{
                            title: {
                                text: 'Quantity of material',
                                style: {
                                    fontWeight: 'bold',
                                },
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                                    fontSize: '0.8vw'
                                }
                            },
                        }],
                        exporting: {
                            enabled: false
                        },
                        legend: {
                            enabled: true,
                            borderWidth: 1
                        },
                        tooltip: {
                            enabled: true
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: 'black'
                            },
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return (this.y != 0) ? this.y : "";
                                    },
                                    style: {
                                        textOutline: false
                                    }
                                },
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            fetchModal(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Confirming',
                            data: confirming,
                            color: '#feccfe'
                        }, {
                            name: 'Waiting PO',
                            data: po,
                            color: '#ecff7b'
                        }, {
                            name: 'Production Process',
                            data: production,
                            color: '#ffbd44'
                        }, {
                            name: 'Delivery Process',
                            data: delivered,
                            color: '#ccffff'
                        }, {
                            name: 'Complete',
                            data: complete,
                            color: '#00b360'
                        }]
                    });




                } else {
                    alert('Attempt to retrieve data failed');
                }
            });
        }
    </script>
@endsection
