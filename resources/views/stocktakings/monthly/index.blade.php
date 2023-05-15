@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        table {
            padding: 0px;
            color: black;
        }

        thead>tr>th {
            text-align: center;
            overflow: hidden;
        }

        tbody>tr>td {
            text-align: center;
        }

        tfoot>tr>th {
            text-align: center;
        }

        th:hover {
            overflow: visible;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-tabs-custom>ul.nav.nav-tabs {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .nav-tabs-custom>ul.nav.nav-tabs>li {
            float: none;
            display: table-cell;
        }

        .nav-tabs-custom>ul.nav.nav-tabs>li>a {
            text-align: center;
        }

        .vendor-tab {
            width: 100%;
        }

        .dataTables_filter {
            float: left !important;
        }

        .button-right {
            float: right;
             !important;
        }

        #loading,
        #error {
            display: none;
        }

        .disabled {
            pointer-events: none;
            cursor: default;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <div class="row">
            <div class="col-xs-12 col-md-9 col-lg-9">
                <h3 style="margin-top: 0px;">{{ $title }}<span class="text-purple"> {{ $title_jp }}</span></h3>
            </div>
            <div class="col-xs-12 col-md-3 col-lg-3">
                <div class="input-group date">
                    <div class="input-group-addon bg-green">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input style="text-align: center;" type="text" class="form-control datepicker"
                        onchange="monthChange()" name="month" id="month" placeholder="Select Month"
                        onchange="changeYmes()" readonly>
                </div>
            </div>
        </div>
    </section>
@stop
@section('content')
    <section class="content" style="padding-top: 0;">

        @foreach (Auth::user()->role->permissions as $perm)
            @php
                $navs[] = $perm->navigation_code;
            @endphp
        @endforeach

        @if (session('error'))
            <input type="text" id="msg_error" value="{{ session('error') }}" hidden>
        @endif

        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="text-align: center; position: absolute; color: white; top: 45%; left: 40%;">
                <span style="font-size: 50px;">Please wait ... </span><br>
                <span style="font-size: 50px;"><i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12 col-md-6 col-lg-6 col-md-offset-3 col-lg-offset-3" style="text-align: center;">
                        <span style="font-size: 20px; color: purple;"><i class="fa fa-angle-double-down"></i> Klik Disini
                            Untuk Melihat Hasil Input <i class="fa fa-angle-double-down"></i></span>
                        <a id="monitoring" onclick="monitoring()" class="btn btn-default btn-block"
                            style="font-size: 15px; border-color: purple;">Stocktaking Monitoring</a>
                    </div>
                </div>
            </div>


            <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        @if (in_array('S36', $navs))
                            <span style="font-size: 20px; color: black;"><i class="fa fa-angle-double-down"></i> Master <i
                                    class="fa fa-angle-double-down"></i></span>

                            <a href="javascript:void(0)" data-toggle="modal" data-target="#importBomModal"
                                class="btn btn-default btn-block" style="border-color: black; font-size: 15px;">Upload Bom
                                Output</a>
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#importMPDLModal"
                                class="btn btn-default btn-block" style="border-color: black; font-size: 15px;">Upload
                                Material Plant Data List</a>
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#importModal"
                                class="btn btn-default btn-block" style="border-color: black; font-size: 15px;">Upload
                                Storage Loc Stock</a>
                            <a href="javascript:void(0)" data-toggle="modal" data-target="#importKiito"
                                class="btn btn-default btn-block" style="border-color: black; font-size: 15px;">Upload Kitto
                                Inventories</a>

                            <a href="{{ url('index/stocktaking/stocktaking_list') }}" class="btn btn-default btn-block"
                                style="border-color: black; font-size: 15px; color:white; background-color: #616161;">Master
                                Stocktaking List</a>
                        @endif

                    </div>
                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 20px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i
                                class="fa fa-angle-double-down"></i></span>

                        <a id="manage_store" href="{{ url('index/stocktaking/summary_new') }}"
                            class="btn btn-default btn-block" style="font-size: 15px; border-color: green;">Print Summary Of
                            Counting</a>

                        <a id="no_use" href="{{ secure_url('index/stocktaking/no_use_new') }}"
                            class="btn btn-default btn-block"
                            style="font-size: 15px; border-color: green; background-color: #ffce5c;">Input No Use</a>

                        @if (str_contains(Auth::user()->role_code, 'PC') ||
                            str_contains(Auth::user()->role_code, 'MIS') ||
                            str_contains(Auth::user()->role_code, 'LOG'))
                            <a id="input_pi_fstk" href="{{ url('index/stocktaking/count_fstk') }}"
                                class="btn btn-default btn-block"
                                style="font-size: 15px; border-color: #68ffff; background-color: #ccffff;">Input Physical
                                Inventory (PI) FSTK</a>
                            <a id="input_pi_scrap" href="{{ url('index/stocktaking/count_scrap') }}"
                                class="btn btn-default btn-block"
                                style="font-size: 15px; border-color: #68ffff; background-color: #C7FFED;">Input Physical
                                Inventory (PI) SCRAP</a>
                        @endif

                        <a id="input_pi" href="{{ secure_url('index/stocktaking/count_new') }}"
                            class="btn btn-default btn-block"
                            style="font-size: 15px; border-color: green; background-color: #ccff90;">Input Physical
                            Inventory (PI)</a>

                        <a id="audit1" href="{{ secure_url('index/stocktaking/audit_new/' . '1') }}"
                            class="btn btn-default btn-block"
                            style="font-size: 15px; border-color: green; background-color: #ccff90;">Audit Internal</a>

                            <a id="revise" href="{{ secure_url('index/stocktaking/revise_user') }}"
                                class="btn btn-default btn-block" style="font-size: 15px; border-color: green;">Revise
                                Physical Inventory (PI)</a>

                        <a id="check_new" href="{{ url('index/stocktaking/check_input_new') }}"
                            class="btn btn-default btn-block" style="font-size: 15px; border-color: green;">Check
                            Input</a>

                    </div>
                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 20px; color: purple;"><i class="fa fa-angle-double-down"></i> Result <i
                                class="fa fa-angle-double-down"></i></span>
                        @if (in_array('S36', $navs))
                            <a id="breakdown" data-toggle="modal" data-target="#modalBreakdown"
                                class="btn btn-default btn-block" style="font-size: 15px; border-color: purple;">Breakdown
                                Physical Inventory (PI)</a>
                        @endif
                        <a id="unmatch" onclick="unmatch()" class="btn btn-default btn-block"
                            style="font-size: 15px; border-color: purple; background-color: #e040fb;">Unmatch Check</a>

                        <form method="GET" action="{{ url('export/stocktaking/inquiry_new') }}">
                            <input type="text" name="month_inquiry" id="month_inquiry" placeholder="Select Month"
                                hidden>
                            <button id="inquiry" type="submit" class="btn btn-default btn-block"
                                style="margin-top: 5px; font-size: 15px; border-color: purple;">Inquiry</button>
                        </form>
                        <form method="GET" action="{{ url('export/stocktaking/variance') }}">
                            <input type="text" name="month_variance" id="month_variance" placeholder="Select Month"
                                hidden>
                            <button id="variance" type="submit" class="btn btn-default btn-block"
                                style="margin-top: 5px; font-size: 15px; border-color: purple; background-color: #e040fb;">Variance
                                Report</button>
                        </form>

                    </div>
                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        {{-- @if (in_array('S36', $navs))
                            <span style="font-size: 20px; color: red;"><i class="fa fa-angle-double-down"></i> Final <i
                                    class="fa fa-angle-double-down"></i></span>
                            <a id="upload_sap" onclick="uploadSap()" class="btn btn-default btn-block"
                                style="font-size: 15px; border-color: red;">Upload Textfile to SAP</a>
                        @endif --}}

                        @if (in_array('S36', $navs))
                            <span style="font-size: 20px; color: red;"><i class="fa fa-angle-double-down"></i> YMES <i
                                    class="fa fa-angle-double-down"></i></span>
                            <a href="{{ url('index/stocktaking/ymes_list') }}" class="btn btn-default btn-block"
                                style="border-color: black; font-size: 15px; color:black; background-color: #eeff00;">Master
                                Stocktaking YMES</a>
                            <a onclick="unmatchYmes()" class="btn btn-default btn-block"
                                style="border-color: black; font-size: 15px; color:black; background-color: #eeff00;">Unmatch
                                List MIRAI VS YMES</a>
                            <a id="breakdown" data-toggle="modal" data-target="#modalExportYmes"
                                class="btn btn-default btn-block"
                                style="border-color: black; font-size: 15px; color:black; background-color: #eeff00;">Inquiry
                                YMES</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalMonth" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body table-responsive no-padding">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Month</label>
                            <div class="input-group date">
                                <div class="input-group-addon bg-green">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input style="text-align: center;" type="text" class="form-control datepicker"
                                    onchange="monthChange()" name="month" id="month" placeholder="Select Month">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBreakdown" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-xs-12" style="background-color: #00a65a;">
                        <h2 style="text-align: center; margin: 2%; font-weight: bold;">Breakdown PI</h2>
                    </div>

                    <div class="col-xs-12" style="margin-top: 3%;">
                        <div class="form-group">
                            <label>Select Group</label><br>
                            <label><input type="checkbox" class="minimal" id="ASSEMBLY">&nbsp;&nbsp;Assembly</label><br>
                            <label><input type="checkbox" class="minimal" id="ST">&nbsp;&nbsp;Surface
                                Treatment</label><br>
                            <label><input type="checkbox" class="minimal" id="WELDING">&nbsp;&nbsp;Welding</label><br>
                            <label><input type="checkbox" class="minimal" id="BPP">&nbsp;&nbsp;Body Parts
                                Process</label><br>
                            <label><input type="checkbox" class="minimal" id="KPP">&nbsp;&nbsp;Key Parts
                                Process</label><br>
                            <label><input type="checkbox" class="minimal" id="EI">&nbsp;&nbsp;Educational
                                Instrument</label><br>
                            <label><input type="checkbox" class="minimal"
                                    id="WAREHOUSE">&nbsp;&nbsp;Warehouse</label><br>
                            <label><input type="checkbox" class="minimal" id="FG">&nbsp;&nbsp;Finished
                                Goods</label><br>
                            <label><input type="checkbox" class="minimal" id="SUBCONT">&nbsp;&nbsp;Subcont</label>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" onclick="countPI()"><i class="fa fa-play"></i>Start</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalExportYmes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-xs-12" style="background-color:#eeff00;">
                        <h2 style="text-align: center; margin: 2%; font-weight: bold;">Export to YMES</h2>
                    </div>
                    <div class="col-xs-12" style="margin-top: 3%;">
                        <div class="form-group">
                            <form method="GET" action="{{ url('fetch/stocktaking/export_ymes_list') }}">
                                <label for="exampleInputEmail1">List No.</label>
                                <div id="selectListNo">
                                    <select class="form-control selectListNo" name="list_no" id='list_no'
                                        data-placeholder="Select List No." style="width: 100%;" onChange="changeYmes()">
                                        <option value="">Select List No.</option>
                                        @foreach ($lists as $list)
                                            <option value="{{ $list->list_no }}">
                                                {{ $list->location }} - {{ $list->list_no }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button class="btn btn-success pull-right" style="margin: 1%;" type="submit"><i
                                        class="fa fa-mail-forward"></i>&nbsp;&nbsp;&nbsp;Export</button>
                            </form>
                            <form method="GET" action="{{ url('fetch/stocktaking/print_ymes_list') }}">
                                <input type="hidden" name="hidden_list_no" id="hidden_list_no">
                                <input type="hidden" name="hidden_month" id="hidden_month">
                                <button class="btn btn-primary pull-right" style="margin: 1%;" type="submit"><i
                                        class="fa fa-print"></i>&nbsp;&nbsp;&nbsp;Print</button>
                            </form>

                        </div>
                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="importForm" method="post" action="{{ url('import/material/storage') }}"
                    enctype="multipart/form-data">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Import Storage Location</h4>
                        Format :
                        [<b><i>Material</i></b>]
                        [<b><i>Description</i></b>]
                        [<b><i>SLoc</i></b>]
                        [<b><i>Unrestricted</i></b>]
                        [<b><i>Download Date</i></b>]
                        [<b><i>Download Time</i></b>]<br>

                        Sample: <a
                            href="{{ url('download/manual/import_storage_location_stock.txt') }}">import_storage_location_stock.txt</a>
                        Code: #Truncate
                    </div>
                    <div class="modal-body">
                        Select Date:
                        <input type="text" class="form-control" id="date_stock" name="date_stock"
                            style="width:25%;"><br>
                        <input type="file" name="storage_location_stock" id="storage_location_stock"
                            accept="text/plain">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button id="modalImportButton" type="submit" class="btn btn-success"
                            onclick="loadingPage()">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importBomModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 40%;">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Import Bom Output</h4>
                    Format :
                    [<b><i>Parent</i></b>]
                    [<b><i>Child</i></b>]
                    [<b><i>Sloc Child</i></b>]
                    [<b><i>SPT Child</i></b>]
                    [<b><i>Valcl Child</i></b>]
                    [<b><i>UOM Child</i></b>]
                    [<b><i>Usage</i></b>]
                    [<b><i>Divider</i></b>]
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1" style="padding: 0px;">
                            <div class="col-xs-12" style="margin-top: 2%;">
                                <label>Bom Output Data :<span class="text-red">*</span></label>
                            </div>
                            <div class="col-xs-12">
                                <textarea id="bom" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="uploadBom()" class="btn btn-success">Upload</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importMPDLModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 60%;">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Import MPDL</h4>
                    Format :
                    [<b><i>GMC</i></b>]
                    [<b><i>Description</i></b>]
                    [<b><i>PGr</i></b>]
                    [<b><i>Bun</i></b>]
                    [<b><i>MRPC</i></b>]
                    [<b><i>SPT</i></b>]
                    [<b><i>Sloc</i></b>]
                    [<b><i>Valcl</i></b>]
                    [<b><i>Price</i></b>]
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1" style="padding: 0px;">
                            <div class="col-xs-12" style="margin-top: 2%;">
                                <label>MPDL Data :<span class="text-red">*</span></label>
                            </div>
                            <div class="col-xs-12">
                                <textarea id="mpdl" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="uploadMpdl()" class="btn btn-success">Upload</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importKiito" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 35%;">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Import Kitto Inventories</h4>
                    Format :
                    [<b><i>GMC</i></b>]
                    [<b><i>Sloc</i></b>]
                    [<b><i>Quantity</i></b>]
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1" style="padding: 0px;">
                            <div class="col-xs-12" style="margin-top: 2%;">
                                <label>Inventories Data :<span class="text-red">*</span></label>
                            </div>
                            <div class="col-xs-12">
                                <textarea id="kitto" style="height: 100px; width: 100%; margin-top: 1%;"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="uploadKitto()" class="btn btn-success">Upload</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.selectListNo').select2({
                dropdownParent: $('#selectListNo'),
                allowClear: true
            });

            $('#date_stock').datepicker({
                autoclose: true,
                todayHighlight: true
            });

            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });

            $('.datepicker').datepicker({
                <?php $tgl_max = date('Y-m'); ?>
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                endDate: '<?php echo $tgl_max; ?>'
            });

            if ($('#month').val() == '') {

                var monthArr = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', ]

                var now = new Date();
                var month = monthArr[now.getMonth()];
                var year = now.getFullYear();

                $('#month').val(year + '-' + month);
                // $('#month').val('2020-11');
            }

            monthChange();

        });

        function loadingPage() {
            $("#loading").show();
        }

        function changeYmes() {
            $('#hidden_month').val($('#month').val());
            $('#hidden_list_no').val($('#list_no').val());
        }

        function printList(eo_number) {
            var list_no = $('#list_no').val();
            var month = $('#month').val();

            if (list_no == '') {
                openErrorGritter('Error', 'Please select YMES list no');
                return false;
            }

            window.open('{{ url('fetch/stocktaking/print_ymes_list') }}' + '/' + month + '/' + list_no, '_blank');
        }

        $('#modalExportYmes').on('hidden.bs.modal', function() {
            $("#list_no").prop('selectedIndex', 0).change();
        });

        function exportYmesList() {

            var list_no = $('#list_no').val();

            if (list_no == '') {
                openErrorGritter('Error', 'Please select YMES list no');
                return false;
            }

            var data = {
                list_no: list_no
            }

            $.get('{{ url('fetch/stocktaking/export_ymes_list') }}', data, function(result, status, xhr) {});

            // $('#modalExportYmes').modal('hide');
        }

        function uploadBom() {

            var upload = $('#bom').val();

            if (upload == '') {
                openErrorGritter('Error', 'All data must be complete');
                return false;
            }

            var data = {
                upload: upload,
            }

            $('#loading').show();
            $.post('{{ url('import/material/bom') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#bom').val('');

                    $('#importBomModal').modal('hide');

                    $('#loading').hide();
                    openSuccessGritter('Success', 'Bom Output Uploaded Successfully');

                    window.open('{{ url('index/bom_output') }}', '_blank');


                } else {
                    $('#loading').hide();
                    openErrorGritter('Error', result.message);
                }

            });
        }

        function uploadMpdl() {

            var upload = $('#mpdl').val();

            if (upload == '') {
                openErrorGritter('Error', 'All data must be complete');
                return false;
            }

            var data = {
                upload: upload,
            }

            $('#loading').show();
            $.post('{{ url('import/material/mpdl') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#mpdl').val('');

                    $('#importMPDLModal').modal('hide');

                    $('#loading').hide();
                    openSuccessGritter('Success', 'MPDL Uploaded Successfully');

                    window.open('{{ url('index/material_plant_data_list') }}', '_blank');


                } else {
                    $('#loading').hide();
                    openErrorGritter('Error', result.message);
                }

            });
        }

        function uploadKitto() {

            var upload = $('#kitto').val();

            if (upload == '') {
                openErrorGritter('Error', 'All data must be complete');
                return false;
            }

            var data = {
                upload: upload,
            }

            $('#loading').show();
            $.post('{{ url('import/inventory/kitto') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#kitto').val('');

                    $('#importKiito').modal('hide');

                    $('#loading').hide();
                    openSuccessGritter('Success', 'Kitto Inventories Uploaded Successfully');

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error', result.message);
                }

            });
        }

        function uploadSap() {

            if (confirm("Are you sure upload stocktaking adjustment to SAP ?")) {
                $("#loading").show();

                var month = $('#month').val();

                var data = {
                    month: month
                }

                $.get('{{ url('export/stocktaking/upload_sap') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        $("#loading").hide();
                        openSuccessGritter('Success', 'Export Log Success');
                    } else {
                        $("#loading").hide();
                        openErrorGritter('Error', 'Export Log Failed');
                    }

                });
            }
        }

        function exportLog() {
            $("#loading").show();

            var month = $('#month').val();

            var data = {
                month: month
            }

            $.get('{{ url('export/stocktaking/log') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();
                    monthChange();
                    openSuccessGritter('Success', 'Export Log Success');
                } else {
                    $("#loading").hide();
                    openErrorGritter('Error', 'Export Log Failed');
                }

            });
        }

        function unmatch() {
            var month = $('#month').val();
            window.open('{{ url('index/stocktaking/unmatch/') }}' + '/' + month, '_blank');
        }

        function unmatchYmes() {
            window.open('{{ url('index/stocktaking/unmatch_ymes_list') }}', '_blank');
        }

        function monitoring() {
            var month = $('#month').val();
            window.open('{{ url('index/stocktaking/monitoring') }}', '_Self');
        }

        function monthChange() {
            var month = $('#month').val();
            // var month = '2020-11';

            $('#month_inquiry').val(month);
            $('#month_variance').val(month);
            $('#month_official_variance').val(month);

            var data = {
                month: month
            }

            // $('#month_text').text(bulanText(month));
            $('#modalMonth').modal('hide');

            $.get('{{ url('fetch/stocktaking/check_month') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#inquiry').removeClass('disabled');
                    $('#variance').removeClass('disabled');

                    if (result.data.status == 'finished') {
                        $('#manage_store').addClass('disabled');
                        $('#summary_of_counting').addClass('disabled');
                        $('#no_use').addClass('disabled');
                        $('#input_pi').addClass('disabled');
                        $('#input_pi_fstk').addClass('disabled');
                        $('#audit1').addClass('disabled');
                        $('#check_new').addClass('disabled');
                        $('#audit2').addClass('disabled');
                        $('#breakdown').addClass('disabled');
                        $('#unmatch').addClass('disabled');
                        $('#revise').addClass('disabled');
                        $('#upload_sap').addClass('disabled');
                        $('#export_log').addClass('disabled');
                        $('#check_new').addClass('disabled');
                    } else {
                        $('#manage_store').removeClass('disabled');
                        $('#summary_of_counting').removeClass('disabled');
                        $('#no_use').removeClass('disabled');
                        $('#input_pi').removeClass('disabled');
                        $('#input_pi_fstk').removeClass('disabled');
                        $('#audit1').removeClass('disabled');
                        $('#check_new').removeClass('disabled');
                        $('#audit2').removeClass('disabled');
                        $('#breakdown').removeClass('disabled');
                        $('#unmatch').removeClass('disabled');
                        $('#revise').removeClass('disabled');
                        $('#upload_sap').removeClass('disabled');
                        $('#export_log').removeClass('disabled');
                        $('#check_new').removeClass('disabled');

                    }
                    // $('#month_text').text(bulanText(month));
                    $('#modalMonth').modal('hide');

                } else {
                    // $('#month_text').text(bulanText(month));
                    $('#modalMonth').modal('hide');
                    openErrorGritter('Error', result.message);

                    $('#manage_store').addClass('disabled');
                    $('#summary_of_counting').addClass('disabled');
                    $('#no_use').addClass('disabled');
                    $('#input_pi').addClass('disabled');
                    $('#input_pi_fstk').addClass('disabled');
                    $('#audit1').addClass('disabled');
                    $('#check_new').addClass('disabled');
                    $('#audit2').addClass('disabled');
                    $('#breakdown').addClass('disabled');
                    $('#unmatch').addClass('disabled');
                    $('#revise').addClass('disabled');
                    $('#upload_sap').addClass('disabled');
                    $('#export_log').addClass('disabled');
                    $('#inquiry').addClass('disabled');
                    $('#variance').addClass('disabled');
                }

            });
        }

        function bulanText(param) {

            var index = param.split('-');
            var bulan = parseInt(index[1]);
            var tahun = parseInt(index[0]);
            var bulanText = ["January", "February", "March", "April", "May", "June", "July", "August", "September",
                "October", "November", "December"
            ];

            return bulanText[bulan - 1] + " " + tahun;
        }

        $("#modalBreakdown").on("hidden.bs.modal", function() {
            $('#ASSEMBLY').iCheck('uncheck');
            $('#ST').iCheck('uncheck');
            $('#WELDING').iCheck('uncheck');
            $('#BPP').iCheck('uncheck');
            $('#KPP').iCheck('uncheck');
            $('#EI').iCheck('uncheck');
            $('#WAREHOUSE').iCheck('uncheck');
            $('#FG').iCheck('uncheck');
        });


        function countPI() {

            if (confirm('Apakah anda yakin untuk melakukan breakdown PI ?')) {
                $("#loading").show();
                var group = [];

                if ($('#ASSEMBLY').is(":checked")) {
                    group.push('ASSEMBLY');
                }
                if ($('#ST').is(":checked")) {
                    group.push('ST');
                }
                if ($('#WELDING').is(":checked")) {
                    group.push('WELDING');
                }
                if ($('#BPP').is(":checked")) {
                    group.push('BPP');
                }
                if ($('#KPP').is(":checked")) {
                    group.push('KPP');
                }
                if ($('#EI').is(":checked")) {
                    group.push('EI');
                }
                if ($('#WAREHOUSE').is(":checked")) {
                    group.push('WAREHOUSE');
                }
                if ($('#FG').is(":checked")) {
                    group.push('FINISHED GOODS');
                }
                if ($('#SUBCONT').is(":checked")) {
                    group.push('SUBCONT');
                }


                if (group.length > 0) {
                    var data = {
                        group: group
                    }

                    $.post('{{ url('index/stocktaking/count_pi_new') }}', data, function(result, status, xhr) {
                        if (result.status) {
                            $("#loading").hide();
                            $("#modalBreakdown").modal('hide');

                            $('#ASSEMBLY').iCheck('uncheck');
                            $('#ST').iCheck('uncheck');
                            $('#WELDING').iCheck('uncheck');
                            $('#BPP').iCheck('uncheck');
                            $('#KPP').iCheck('uncheck');
                            $('#EI').iCheck('uncheck');
                            $('#WAREHOUSE').iCheck('uncheck');
                            $('#FG').iCheck('uncheck');
                            $('#SUBCONT').iCheck('uncheck');

                            // variance();
                            openSuccessGritter('Success', result.message);
                        } else {
                            $("#loading").hide();
                            openErrorGritter('Error', result.message);
                        }

                    });
                } else {
                    $("#loading").hide();
                    openErrorGritter('Error', 'Select Group');
                }
            }

        }

        function exportInquiry() {
            $.get('{{ url('export/stocktaking/inquiry') }}', function(result, status, xhr) {});
        }

        function exportVariance() {
            $.get('{{ url('export/stocktaking/variance') }}', function(result, status, xhr) {});
        }

        function addZero(i) {
            if (i < 10) {
                i = "0" + i;
            }
            return i;
        }

        function getActualFullDate() {
            var d = new Date();
            var day = addZero(d.getDate());
            var month = addZero(d.getMonth() + 1);
            var year = addZero(d.getFullYear());
            var h = addZero(d.getHours());
            var m = addZero(d.getMinutes());
            var s = addZero(d.getSeconds());
            return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s + ")";
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
