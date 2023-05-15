@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url('bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
<link rel="stylesheet" href="{{ url('bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}"
media="print">
<style type="text/css">
    table.table-bordered {
        border: 1px solid black;
    }

    table.table-bordered>thead>tr>th {
        font-size: 0.8vw;
        border: 1px solid black;
        padding-top: 5px;
        padding-bottom: 5px;
        vertical-align: middle;
        text-align: center;
    }

    table.table-bordered>tbody>tr>td {
        border: 1px solid black;
        padding-top: 3px;
        padding-bottom: 3px;
        padding-left: 2px;
        padding-right: 2px;
        vertical-align: middle;
    }

    table.table-bordered>tfoot>tr>th {
        font-size: 0.8vw;
        border: 1px solid black;
        padding-top: 0;
        padding-bottom: 0;
        vertical-align: middle;
    }

    #tableExtraOrderBody>tr:hover {
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
        @if (Auth::user()->role_code == 'PC' || Auth::user()->role_code == 'MIS')
        <button class="btn btn-success pull-right" style="margin-left: 5px; width: 10%;" onclick="openModalCreate();">Create Extra Order</button>
        @endif
    </h1>
</section>
@endsection

@section('content')
<section class="content">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
        <p style="position: absolute; color: White; top: 45%; left: 45%;">
            <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
        </p>
    </div>

    <input id="role_code" value="{{ Auth::user()->role_code }}" hidden>


    <div class="row">

        <div class="col-xs-12"  >
            <div class="box">
                <div class="box-body">
                    <table id="tableExtraOrder" class="table table-bordered" style="width: 100%;">
                        <thead style="background-color: rgba(126,86,134,.7);">
                            <tr>
                                <th style="width: 9%;">EO_No</th>
                                <th style="width: 9%;">Attention</th>
                                <th style="width: 5%;">Destination</th>
                                <th style="width: 5%;">Submit_Date</th>
                                <th style="width: 11.5%;">BOM</th>
                                <th style="width: 11.5%;">Price</th>
                                <th style="width: 10%;">EO Approval</th>
                                <th style="width: 11%;">PO</th>
                                <th style="width: 11.5%;">Production_Result</th>
                                <th style="width: 11.5%;">Shipment_Result</th>
                                <th style="width: 5%;">Inv_No</th>
                            </tr>
                        </thead>
                        <tbody id="tableExtraOrderBody" style="vertical-align: middle; text-align: center;">
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
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modalCreate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <center>
                    <h3 style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                        Create Your Order<br>予約を作成
                    </h3>
                </center>
                <form class="form-horizontal">
                    <div class="form-group">
                        <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                            Order By <span class="text-purple">予約者</span> <span class="text-red">* :</span>
                        </label>
                        <div class="col-sm-5" style="padding-left: 0px;">
                            <input class="form-control" type="text" id="orderByName" value="{{ ucwords($user->name) }}" disabled>
                            <input class="form-control" type="hidden" id="orderById" value="{{ strtoupper($user->username) }}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                            Recipient <span class="text-purple">受取人</span> <span class="text-red">* :</span>
                        </label>
                        <div class="col-sm-5" style="padding-left: 0px;">
                            <select class="form-control select2" name="addBuyer" id="addBuyer"data-placeholder="Select Buyer" style="width: 100%;" onchange="checkBuyer(value)">
                                <option></option>
                                @foreach ($buyers as $buyer)
                                <option value="{{ $buyer->attention }}!{{ $buyer->division }}!{{ $buyer->destination_code }}!{{ $buyer->destination_name }}!{{ $buyer->destination_shortname }}!{{ $buyer->currency }}" selected>{{ $buyer->attention }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                            Destination <span class="text-purple">仕向け</span> <span class="text-red">* :</span>
                        </label>
                        <div class="col-sm-5" style="padding-left: 0px;">
                            <input class="form-control" type="text" id="addDestination" disabled>
                            <input class="form-control" type="hidden" id="addDestinationName" disabled>
                            <input class="form-control" type="hidden" id="addDestinationShortname" disabled>
                            <input class="form-control" type="hidden" id="addCurrency" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                            Division <span class="text-purple">部門</span> <span class="text-red"> :</span>
                        </label>
                        <div class="col-sm-5" style="padding-left: 0px;">
                            <textarea class="form-control" type="text" rows="2" id="addDivision" disabled></textarea>
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
                <a class="btn btn-primary pull-right" id="addItem" onclick="addItem()" style="margin-bottom: 15px;">
                    Add Item <br> アイテムを追加 <i class="fa fa-shopping-cart"></i>
                </a>
                <a class="btn btn-info pull-right" id="addItem" onclick="uploadItem()" style="margin-bottom: 15px; margin-right: 10px;">
                    Upload Item <br> アップロード <i class="fa fa-upload"></i>
                </a>
                <table class="table table-hover table-bordered table-striped" id="tableAddItem">
                    <thead style="background-color: rgba(126,86,134,.7);">
                        <tr>
                            <th style="width: 1%;">GMC Buyer</th>
                            <th style="width: 1%;">GMC YMPI</th>
                            <th style="width: 5%;">Description</th>
                            <th style="width: 1%;">UoM</th>
                            <th style="width: 1%;">Price (USD)</th>
                            <th style="width: 1%;">ETD</th>
                            <th style="width: 0.5%;">Ship By</th>
                            <th style="width: 1%;">Qty</th>
                            <th style="width: 1%;">Amount</th>
                            <th style="width: 0.1%;"><i class="fa fa-trash"></i></th>
                        </tr>
                    </thead>
                    <tbody id="tableAddItemBody">
                    </tbody>
                </table>
                <button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close" style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back<br>戻る</button>
                <button class="btn btn-success pull-right" style="font-weight: bold; font-size: 1.3vw; width: 68%;" onclick="confirmOrder()">CONFIRM<br>確認</button>
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
<script src="{{ url('js/highcharts-3d.js') }}"></script>
<script src="{{ url('js/exporting.js') }}"></script>
<script src="{{ url('js/export-data.js') }}"></script>
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
    }

    function checkBuyer(val) {
        var buyer = val.split('!');
        $('#addDestination').val(buyer[2]);
        $('#addDestinationName').val(buyer[3]);
        $('#addDestinationShortname').val(buyer[4]);
        $('#addDivision').val(buyer[1]);
        $('#addCurrency').val(buyer[5]);
    }

    function checkMaterial(id, val) {
        var id_number = id.split('_');
        var content = val.split('!');

        if(val == '-'){
            return false;
        }else{
            if (content[0] != 'NEW') {
                checkQuantity(id_number[2]);
                $('#create_description_' + id_number[2]).prop('disabled', true);
                $('#create_description_' + id_number[2]).val(content[2]);
                $('#create_uom_' + id_number[2]).val(content[3]);
                $('#create_price_' + id_number[2]).val(content[5]);

                if (id_number[1] == 'materialbuyer') {
                    var check1 = $('#create_materialympi_' + id_number[2]).val();
                    var check2 = content[0] + '!' + content[1] + '!' + content[2] + '!' + content[3] + '!' + content[4] + '!' + content[5] + '!' + content[6];

                    if (check1 != check2) {
                        var value = content[0] + '!' + content[1] + '!' + content[2] + '!' + content[3] + '!' + content[4] + '!' + content[5] + '!' + content[6];
                        $('#create_materialympi_' + id_number[2]).val(value).trigger('change.select2');
                    }

                } else {
                    var check1 = $('#create_materialbuyer_' + id_number[2]).val();
                    var check2 = content[0] + '!' + content[1] + '!' + content[2] + '!' + content[3] + '!' + content[4] + '!' + content[5] + '!' + content[6];

                    if(content[1] == '-'){
                        $('#create_materialbuyer_' + id_number[2]).val('-').trigger('change.select2');

                    }else{
                        if ( check1 != check2 ) {
                            var value = content[0] + '!' + content[1] + '!' + content[2] + '!' + content[3] + '!' + content[4] + '!' + content[5] + '!' + content[6];
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
            var buyer = $('#addBuyer').val();
            var destination_code = $('#addDestination').val();
            var destination_name = $('#addDestinationName').val();
            var destination_shortname = $('#addDestinationShortname').val();
            var currency = $('#addCurrency').val();
            var division = $('#addDivision').val();
            var remark = $('#addRemark').val();

            if (buyer == '') {
                audio_error.play();
                openErrorGritter('Please enter buyer/attention name.');
                return false;
            }

            if (destination_code == '') {
                $('#loading').hide();
                audio_error.play();
                openErrorGritter('Please select destination.');
                return false;
            }

            var formData = new FormData();
            var attachment = $('#addAttachment').prop('files')[0];
            var file = $('#addAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

            formData.append('order_by_id', order_by_id);
            formData.append('buyer', buyer);
            formData.append('destination_code', destination_code);
            formData.append('destination_name', destination_name);
            formData.append('destination_shortname', destination_shortname);
            formData.append('currency', currency);
            formData.append('remark', remark);
            formData.append('division', division);
            formData.append('attachment', attachment);
            formData.append('extension', file[1]);
            formData.append('file_name', file[0]);

            $.each(countAddItems, function(key, value) {
                var material_ympi = $('#create_materialympi_' + value).val();
                var description = $('#create_description_' + value).val();
                var uom = $('#create_uom_' + value).val();
                var price = $('#create_price_' + value).val();
                var etd = $('#create_requestdate_' + value).val();
                var ship_by = $('#create_shipment_' + value).val();
                var qty = $('#create_quantity_' + value).val();
                var amount = $('#create_amount_' + value).val();

                if (qty == "" || qty == 0) {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Quantity can`t be zero.');
                    order_lists = [];
                    return false;
                }

                if (description == "") {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Description can`t be blank.');
                    order_lists = [];
                    return false;
                }

                if (etd == "") {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('ETD can`t be blank.');
                    order_lists = [];
                    return false;
                }

                if (ship_by == "") {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Shipment by can`t be blank.');
                    order_lists = [];
                    return false;
                }

                formData.append('order_lists[' + key + ']', material_ympi + '!!' + description + '!!' + uom +
                    '!!' + price + '!!' + etd + '!!' + ship_by + '!!' + qty + '!!' + amount);
            });

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
                        openSuccessGritter('Success!', data.message);
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
        countAddItems.splice($.inArray(id), 1);
        $('#create_item_' + id).remove();
    }

    function addItem() {
        var tableAddItem = "";
        tableAddItem += '<tr id="create_item_' +countAddItem+ '">';

        tableAddItem += '<td>';
        tableAddItem += '<div id="selectGmcBuyer' +countAddItem+ '">';
        tableAddItem += '<select style= "width: 100%;" class="select2" id="create_materialbuyer_' +countAddItem+ '" onchange="checkMaterial(id, value)">';
        tableAddItem += '<option value="NEW">NEW</option>';
        tableAddItem += '<option value="-">-</option>';
        $.each(materials, function(key, value) {
            if (value.material_number_buyer != "" && value.material_number_buyer != null && value.material_number_buyer != "NEW" && value.material_number_buyer != "-") {
                tableAddItem += '<option value="' + value.material_number + '!' + value.material_number_buyer + '!' + value.description + '!' + value.uom + '!' + value.hpl + '!' + value.sales_price + '!' + value.storage_location + '">' + value.material_number_buyer + '</option>';
            }
        });
        tableAddItem += '</select>';
        tableAddItem += '</div>';
        tableAddItem += '</td>';


        tableAddItem += '<td>';
        tableAddItem += '<div id="selectGmcYmpi' +countAddItem+ '">';
        tableAddItem += '<select style= "width: 100%;" class="select2" id="create_materialympi_' + countAddItem + '" onchange="checkMaterial(id, value)">';
        tableAddItem += '<option value="NEW">NEW</option>';
        $.each(materials, function(key, value) {
            if (value.material_number != "" && value.material_number != null && value.material_number != "NEW") {
                tableAddItem += '<option value="' + value.material_number + '!' + value.material_number_buyer + '!' + value.description + '!' + value.uom + '!' + value.hpl + '!' + value.sales_price + '!' + value.storage_location + '">' + value.material_number + '</option>';
            }
        });
        tableAddItem += '</select>';
        tableAddItem += '</div>';
        tableAddItem += '</td>';


        tableAddItem += '<td><input type="text" class="form-control" id="create_description_' + countAddItem +'"></td>';
        tableAddItem += '<td><input type="text" class="form-control" id="create_uom_' + countAddItem +'" disabled></td>';
        tableAddItem += '<td><input style="text-align: right;" type="text" class="form-control" onchange="checkQuantity(' +countAddItem+ ')" id="create_price_' + countAddItem + '" disabled></td>';
        tableAddItem += '<td><input style="text-align: right;" type="text" class="form-control datepicker" id="create_requestdate_' +countAddItem+ '"></td>';

        tableAddItem += '<td>';
        tableAddItem += '<select style= "width: 100%;" class="select2" id="create_shipment_' + countAddItem + '">';
        tableAddItem += '<option></option>';
        tableAddItem += '<option value="SEA">SEA</option>';
        tableAddItem += '<option value="AIR">AIR</option>';
        tableAddItem += '<option value="TRUCK">TRUCK</option>';
        tableAddItem += '</select>';
        tableAddItem += '</td>';


        tableAddItem += '<td><input style="text-align: right;" type="text" class="form-control" id="create_quantity_' +countAddItem+ '" onkeyup="checkQuantity(' + countAddItem + ')"></td>';
        tableAddItem += '<td><input style="text-align: right;" type="text" class="form-control" id="create_amount_' +countAddItem+ '" value="0" disabled></td>';
        tableAddItem += '<td style="text-align: center;"><button class="btn btn-danger btn-xs" onclick="remMaterial(' +countAddItem+ ')" id="tes"><i class="fa fa-trash"></i></button></td>';
        tableAddItem += '</tr>';

        $('#tableAddItem').append(tableAddItem);

        $('#create_requestdate_' + countAddItem + '').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd"
        });

        $('.select2').select2({
            dropdownParent: $('#tableAddItem'),
        });

        countAddItems.push(countAddItem);
        countAddItem += 1;
    }

    function clearAll() {
        $('#modalCreate').modal('hide');
        $("#addBuyer").prop('selectedIndex', 0).change();
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

    function checkQuantity(id) {
        if ($('#create_quantity_' + id).val().match(/^((\d+(\.\d *)?)|((\d*\.)?\d+))$/)) {
            var amount = $('#create_price_' + id).val() * $('#create_quantity_' + id).val();
            $('#create_amount_' + id).val(amount.toFixed(2));
        } else if ($('#create_quantity_' + id).val() == "") {
            return false;
        } else {
            $('#loading').hide();
            audio_error.play();
            openErrorGritter('Please Enter Numeric Value.');
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

    function poNumberFormatter(po_number, eo_number) {
        var content = '';
        var style = 'style="font-weight: bold; cursor: pointer;"';

        var obj = JSON.parse(po_number);

        for (var i = 0; i < obj.length; i++) {
            content += '<a '+style+' onclick="downloadPo(\'' + obj[i] + '\')">' + obj[i].replace(eo_number+'__', '') + '</a>';

            if(i != obj.length -1 ){
                content += '<br>';
            }
        }

        return content;
    }

    function downloadPo(po_number) {

        var data = {
            po_number: po_number
        }

        $.get('{{ url("index/extra_order/po_number/") }}', data, function(result, status, xhr){
            if(result.status){
                window.open(result.file_path);
            }else{
                openErrorGritter('Error!', 'Attempt to retrieve data failed');
            }
        }); 

    }

    function fetchExtraOrder() {

        var data = {

        }

        $.get('{{ url('fetch/extra_order') }}', data, function(result, status, xhr) {
            if (result.status) {
                $('#tableExtraOrder').DataTable().clear();
                $('#tableExtraOrder').DataTable().destroy();
                $('#tableExtraOrderBody').html("");

                var role_code = $('#role_code').val();

                var tableExtraOrderBody = "";
                $.each(result.extra_orders, function(key, value) {
                    tableExtraOrderBody += '<tr>';
                    if( role_code != 'Buyer EO' ){
                        tableExtraOrderBody += '<td style="width: 5%;">';
                        tableExtraOrderBody += '<a onclick="detailExtraOrder(\'' + value.eo_number + '\')" style="font-weight: bold; cursor: pointer;">' + value.eo_number + '</a>';
                        tableExtraOrderBody += '</td>';
                    }else{
                        tableExtraOrderBody += '<td>' + value.eo_number + '</td>';
                    }
                    tableExtraOrderBody += '<td style="width: 15%;">' + value.attention + '</td>';
                    tableExtraOrderBody += '<td style="width: 5%;">' + value.destination_shortname + '</td>';
                    tableExtraOrderBody += '<td style="width: 5%;">' + value.submit_date + '</td>';
                    


                    tableExtraOrderBody += '<td style="width: 11.5%;">';
                    if(parseFloat(value.bom_progress) >= 100){
                        tableExtraOrderBody += '<div class="progress progress-sm" style="border: 1px solid #a1a4ab;">';
                        tableExtraOrderBody += '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' + parseFloat(value.bom_progress) + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + parseFloat(value.bom_progress) + '%;">';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '<span style="font-weight: normal;">Complete: 100%</span>';

                    }else{
                        tableExtraOrderBody += '<div class="progress progress-sm active" style="border: 1px solid #a1a4ab;">';
                        tableExtraOrderBody += '<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="' + parseFloat(value.bom_progress) + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + parseFloat(value.bom_progress) + '%;">';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '<span style="font-weight: normal;">Progress: ' + Math.round(parseFloat(value.bom_progress)) + '%</span>';

                    }
                    tableExtraOrderBody += '</td>';




                    tableExtraOrderBody += '<td style="width: 11.5%;">';
                    if(parseFloat(value.price_progress) >= 100){
                        tableExtraOrderBody += '<div class="progress progress-sm" style="border: 1px solid #a1a4ab;">';
                        tableExtraOrderBody += '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' + parseFloat(value.bom_progress) + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + parseFloat(value.price_progress) + '%;">';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '<span style="font-weight: normal;">Complete: 100%</span>';

                    }else{
                        tableExtraOrderBody += '<div class="progress progress-sm active" style="border: 1px solid #a1a4ab;">';
                        tableExtraOrderBody += '<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="' + parseFloat(value.bom_progress) + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + parseFloat(value.price_progress) + '%;">';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '<span style="font-weight: normal;">Progress: ' + Math.round(parseFloat(value.price_progress)) + '%</span>';

                    }
                    tableExtraOrderBody += '</td>';




                    if (value.approval != 'Not submitted yet' && (role_code == 'PC' || role_code == 'MIS') ) {
                        tableExtraOrderBody += '<td style="width: 10%;"><a onclick="detailExtraOrder(\'' + value.eo_number + '\')" style="font-weight: bold; cursor: pointer;">' + value.approval + '</a></td>';
                    } else {
                        tableExtraOrderBody += '<td style="width: 10%;">' + value.approval + '</td>';
                    }

                    if (value.po_number != null) {
                        tableExtraOrderBody += '<td style="width: 5%;">' + poNumberFormatter(value.po_number, value.eo_number) + '</td>';
                    } else {
                        tableExtraOrderBody += '<td style="width: 5%;">-</td>';
                    }



                    tableExtraOrderBody += '<td style="width: 11.5%;">';
                    if(parseFloat(value.production_progress) >= 100){
                        tableExtraOrderBody += '<div class="progress progress-sm" style="border: 1px solid #a1a4ab;">';
                        tableExtraOrderBody += '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' + parseFloat(value.bom_progress) + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + parseFloat(value.production_progress) + '%;">';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '<span style="font-weight: normal;">Complete: 100%</span>';

                    }else{
                        tableExtraOrderBody += '<div class="progress progress-sm active" style="border: 1px solid #a1a4ab;">';
                        tableExtraOrderBody += '<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="' + parseFloat(value.bom_progress) + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + parseFloat(value.production_progress) + '%;">';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '<span style="font-weight: normal;">Progress: ' + Math.round(parseFloat(value.production_progress)) + '%</span>';
                    }
                    tableExtraOrderBody += '</td>';


                    tableExtraOrderBody += '<td style="width: 11.5%;">';
                    if(parseFloat(value.shipment_progress) >= 100){
                        tableExtraOrderBody += '<div class="progress progress-sm" style="border: 1px solid #a1a4ab;">';
                        tableExtraOrderBody += '<div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" aria-valuenow="' + parseFloat(value.bom_progress) + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + parseFloat(value.shipment_progress) + '%;">';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '<span style="font-weight: normal;">Complete: 100%</span>';

                    }else{
                        tableExtraOrderBody += '<div class="progress progress-sm active" style="border: 1px solid #a1a4ab;">';
                        tableExtraOrderBody += '<div class="progress-bar progress-bar-danger progress-bar-striped" role="progressbar" aria-valuenow="' + parseFloat(value.bom_progress) + '" aria-valuemin="0" aria-valuemax="100" style="width: ' + parseFloat(value.shipment_progress) + '%;">';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '</div>';
                        tableExtraOrderBody += '<span style="font-weight: normal;">Progress: ' + Math.round(parseFloat(value.shipment_progress)) + '%</span>';
                    }
                    tableExtraOrderBody += '</td>';

                    tableExtraOrderBody += '<td style="width: 5%;">' + (value.iv_number || '-') + '</td>';
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
    'ordering': false,
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
} else {
    alert('Attempt to retrieve data failed');
}
});
}
</script>

@endsection
