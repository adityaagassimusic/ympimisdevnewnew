@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css//bootstrap-toggle.min.css') }}" rel="stylesheet">
    <style>
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
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        ::-webkit-input-placeholder {
            color: grey;
        }

        :-ms-input-placeholder {
            color: grey;
        }

        ::placeholder {
            color: grey;
        }

        #loading-animation {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            List of {{ $page }}s
        </h1>
        <ol class="breadcrumb">

            <li>
                <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-sm" style="color:white">Import
                    {{ $page }}s</a>
                &nbsp;
            </li>
        </ol>
    </section>
@endsection

@section('content')
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
        <div id="loading-animation"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th>Sheet No.</th>
                                        <th>Container No.</th>
                                        <th>Seal No.</th>
                                        <th>No. Pol</th>
                                        <th>Dest</th>
                                        <th>Invoice</th>
                                        <th>Stuffing Date</th>
                                        <th>On Or About</th>
                                        <th>Payment</th>
                                        <th>To</th>
                                        <th>Carrier</th>
                                        <th>Action</th>
                                        <th>View</th>
                                        <th>Check</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($time as $nomor => $time)
                                        <tr id="{{ $time->id_checkSheet }}">
                                            <td style="font-size: 14">{{ $time->id_checkSheet }}</td>
                                            <td style="font-size: 14">{{ $time->countainer_number }}</td>
                                            <td style="font-size: 14">{{ $time->seal_number }}</td>
                                            <td style="font-size: 14">{{ $time->no_pol }}</td>
                                            <td style="font-size: 14">{{ $time->destination }}</td>
                                            <td style="font-size: 14">{{ $time->invoice }}</td>
                                            <td style="font-size: 14">{{ $time->Stuffing_date }}</td>
                                            <td style="font-size: 14">{{ $time->etd_sub }}</td>
                                            <td style="font-size: 14">{{ $time->payment }}</td>
                                            <td style="font-size: 14">{{ $time->shipped_to }}</td>
                                            <td style="font-size: 14">
                                                @if (isset($time->shipmentcondition->shipment_condition_name))
                                                    {{ $time->shipmentcondition->shipment_condition_name }}
                                                @else
                                                    Not registered
                                                @endif
                                            </td>
                                            <td>
                                                @if ($time->status == null)
                                                    <a href="javascript:void(0)" class="btn btn-warning btn-xs"
                                                        data-toggle="modal" data-target="#editModal"
                                                        onclick="editConfirmation('{{ url('edit/CheckSheet') }}', '{{ $time['destination'] }}', '{{ $time['id_checkSheet'] }}'); reason('{{ $time['id_checkSheet'] }}');">Edit</a>

                                                    <a data-toggle="modal" data-target="#importModal3"
                                                        class="btn btn-success btn-xs" style="color:white"
                                                        onclick="getid('{{ $time['id_checkSheet'] }}');"><i
                                                            class="fa fa-folder-open-o"></i> Re - Import</a>


                                                    <a href="javascript:void(0)" class="btn btn-danger btn-xs"
                                                        data-toggle="modal" data-target="#myModal"
                                                        onclick="deleteConfirmation('{{ url('delete/CheckSheet') }}', '{{ $time['destination'] }}', '{{ $time['id'] }}');">Delete</a>
                                                    <p id="id_checkSheet_mastera{{ $nomor + 1 }}" hidden>
                                                        {{ $time->id_checkSheet }}</p>
                                                @else
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-info btn-xs"
                                                    href="{{ url('show/CheckSheet', $time['id']) }}">View</a>
                                                <p id="id_checkSheet_master{{ $nomor + 1 }}" hidden>
                                                    {{ $time->id_checkSheet }}</p>
                                            </td>
                                            <td>
                                                @if ($time->status != null)
                                                    <span data-toggle="tooltip" class="badge bg-green"><i
                                                            class="fa fa-fw fa-check"></i></span>
                                                @else
                                                    {{-- @if ($time->destination != 'NINGBO')
                                                        <a class="btn btn-warning btn-xs"
                                                            href="{{ secure_url('check/CheckSheet', $time['id']) }}">Check</a>
                                                    @else
                                                        <a class="btn btn-warning btn-xs"
                                                            href="{{ secure_url('checkmarking/CheckSheet', $time['id']) }}">Check</a>
                                                    @endif --}}
                                                    <a class="btn btn-warning btn-xs"
                                                        href="{{ secure_url('check/CheckSheet', $time['id']) }}">Check</a>
                                                @endif

                                            </td>
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



        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" style="width: 55%;">
                <div class="modal-content">
                    <form id="importForm" method="post" action="{{ url('import/CheckSheet') }}"
                        enctype="multipart/form-data">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">&times;</button>
                            <h3 class="modal-title" id="myModalLabel">Import Confirmation</h3>
                            <u>Format</u> :<br>
                            [<b><i>Order Type</i></b>]
                            [<b><i>Destination</i></b>]
                            [<b><i>Invoice</i></b>]
                            [<b><i>GMC</i></b>]
                            [<b><i>Goods</i></b>]
                            [<b><i>Marking No</i></b>]
                            [<b><i>Package Qty</i></b>]
                            [<b><i>Package Type</i></b>]
                            [<b><i>Qty Qty</i></b>]
                            [<b><i>Qty Set</i></b>]
                            [<b><i>Box/Package</i></b>]
                            <br>
                            <u>Sample</u> :</br>
                            <a
                                href="{{ url('download/manual/import_check_sheet_detail.txt') }}">import_check_sheet_detail.txt</a>
                            Code: #Add
                        </div>

                        <div class="modal-body col-xs-12">

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">SHIPMENT PERIOD<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" class="form-control monthpicker" id="period" name="period"
                                        placeholder="Choose Shipment Period ..." required>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">STUFFING DATE<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" class="form-control" id="Stuffing_date" name="Stuffing_date"
                                        placeholder="Choose Stuffing Date ..." autocomplete="off" required>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">ON OR ABOUT (ETD SUB)<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" class="form-control" ID="etd_sub" name="etd_sub"
                                        placeholder="Choose ETD Sub ..." autocomplete="off" required>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">YCJ REF. NO.</label>
                                <div class="col-xs-7" align="left" id="div_ycj_ref_number">
                                    <select class="form-control select_ycj_ref_number" id="ycj_ref_number"
                                        name="ycj_ref_number" data-placeholder="Select YCJ Ref Number"
                                        style="width: 100%">
                                        <option value=""></option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">SHIPPER<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" name="" class="form-control" value="PT. YMPI" readonly>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">SHIPPED FROM<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" name="shipped_from" class="form-control" id="shipped_from"
                                        value="SURABAYA" readonly>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">CONSIGNEE & ADDRESS<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" name="destination" class="form-control"
                                        placeholder="Input Consignee & Addrress ..." id="destination" required>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">SHIPPED TO<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" name="shipped_to" class="form-control"
                                        placeholder="Input Shipped To ..." id="shipped_to" required>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">DEST. CODE (FOR YMES)<span class="text-red">*</span></label>
                                <div class="col-xs-7" align="left" id="div_destination_code">
                                    <select class="form-control select_destination_code" id="destination_code"
                                        name="destination_code" data-placeholder="Select Destination Code"
                                        style="width: 100%">
                                        <option value=""></option>
                                        @foreach ($destination as $row)
                                            <option value="{{ $row->destination_code }}">{{ $row->destination_code }} -
                                                {{ $row->destination_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">TOWARDS<span class="text-red">*</span></label>
                                <div class="col-xs-7" align="left" id="div_toward">
                                    <select class="form-control select_toward" multiple="multiple"
                                        placeholder="Choose a Toward ..." name="toward[]" id="toward"
                                        style="width:100%">
                                        <option value=""></option>
                                        <option value="YAMAHA MUSIC MANUFACTURING JAPAN CORPORATION BO & GD SECTION">YMMJ
                                        </option>
                                        <option value="XIAOSHAN YAMAHA MUSICAL INSTRUMENT CO.,LTD">XY</option>
                                        <option value="YAMAHA CORPORATION">YCJ/YMJ</option>
                                        <option value="YAMAHA MUSIC EUROPE">YME</option>
                                        <option value="YAMAHA MUSIC KOREA LTD.">YMK</option>
                                        <option value="YAMAHA CORPORATION C/O MOL LOGISTIC S PASIR GUDANG WAREHOUSE">TASCO
                                        </option>
                                        <option value="YCA,BAND&ORCHESTRAL DIV.">YCA</option>
                                        <option value="SIAM MUSIC YAMAHA CO., LTD">SMY</option>
                                        <option value="PT. YAMAHA MUSIK INDONESIA DISTRIBUTOR">YMID</option>
                                        <option value="YAMAHA ELECTRONICS MFG INDONESIA">YEMI</option>
                                        <option value="YAMAHA DE MEXICO S.A. DE C.V.">YDM</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">CARRIER<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left" id="div_carier">
                                    <select class="form-control select_carier" name="carier" id="carier"
                                        data-placeholder="Choose a Carrier ..." style="width: 100%;">
                                        <option value=""></option>
                                        @foreach ($carier as $row)
                                            <option value="{{ $row->shipment_condition_code }}">
                                                {{ $row->shipment_condition_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">CONTAINER SIZE<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left" id="div_ct_size">
                                    <select class="form-control select_ct_size" name="ct_size" id="ct_size"
                                        data-placeholder="Choose a Size ..." style="width: 100%;">
                                        <option value=""></option>
                                        <option value="20FT">20FT</option>
                                        <option value="40FT">40FT</option>
                                        <option value="40FT HC">40FT HC</option>
                                        <option value="TRUCK">TRUCK</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">NO POL</label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" name="nopol" class="form-control" id="nopol"
                                        placeholder="Input No Pol ..." required>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">CONTAINER NO.<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" name="countainer_number" class="form-control"
                                        id="countainer_number" placeholder="Input Container No ..." required>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">SEAL NO</label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" name="seal_number" class="form-control"
                                        placeholder="Input Seal No ..." id="seal_number">
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">DELIVERY ORDER<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" class="form-control" name="do_number" id="do_number"
                                        placeholder="Input Delivery Order ..." required>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">INVOICE NO.<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" class="form-control" name="invoice" id="invoice"
                                        placeholder="Input IV No ..." required>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">INVOICE DATE<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <input type="text" class="form-control" name="invoice_date" id="invoice_date"
                                        placeholder="Choose IV date ..." autocomplete="off" required>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">PAYMENT<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left" id="div_payment">
                                    <select class="form-control select_payment" name="payment" id="payment"
                                        data-placeholder="Choose a Payment ..." style="width: 100%;">
                                        <option value=""></option>
                                        <option value="T/T REMITTANCE">T/T REMITTANCE</option>
                                        <option value="D/P AT SIGHT">D/P AT SIGHT</option>
                                        <option value="D/A 60 DAYS AFTER BL DATE">D/A 60 DAYS AFTER BL DATE</option>
                                        <option value="FREE OF CHARGES">FREE OF CHARGES</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row" align="right">
                                <label class="col-xs-4">CHECKSHEET<span class="text-red">*</span></label>
                                <div class="col-xs-5" align="left">
                                    <center><input type="file" name="check_sheet_import" id="InputFile"
                                            accept="text/plain" required></center>
                                </div>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button id="modalImportButton" type="button" class="btn btn-success"
                                onclick="cektgl()">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal  fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" style="width: 45%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">
                            <p id="myModalLabelt">Edit Confirmation</p>
                        </h4>
                    </div>
                    <div class="modal-body" id="modalDeleteBody">
                        <form id="Editform" method="post" action="" enctype="multipart/form-data">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <div class="modal-body col-xs-12">


                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">SHIPMENT PERIOD<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" class="form-control monthpicker" name="edit_period"
                                            id="edit_period" placeholder="Choose Shipment Period ...	" required>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">STUFFING DATE<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" class="form-control" name="edit_stuffing_date"
                                            id="edit_stuffing_date" placeholder="Choose Stuffing Date ..."
                                            autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">ON OR ABOUT (ETD SUB)<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" class="form-control" name="edit_etd_sub" id="edit_etd_sub"
                                            placeholder="Choose ETD Sub ..." autocomplete="off" required>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">YCJ REF. NO.<span class="text-red">*</span></label>
                                    <div class="col-xs-7" align="left" id="div_edit_ycj_ref_number">
                                        <select class="form-control select_edit_ycj_ref_number"
                                            data-placeholder="Select YCJ Ref Number" name="edit_ycj_ref_number"
                                            id="edit_ycj_ref_number" style="width: 100%">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">SHIPPER<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" name="" class="form-control" name="edit_shipper"
                                            id="edit_shipper" value="PT. YMPI" readonly>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">SHIPPED FROM<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" class="form-control" name="edit_shipped_from"
                                            id="edit_shipped_from" value="SURABAYA" readonly>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">CONSIGNEE & ADDRESS<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" class="form-control"
                                            placeholder="Input Consignee & Addrress ..." name="edit_destination"
                                            id="edit_destination" required>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">SHIPPED TO<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" class="form-control" placeholder="Input Shipped To ..."
                                            name="edit_shipped_to" id="edit_shipped_to" required>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">DEST. CODE (FOR YMES)<span class="text-red">*</span></label>
                                    <div class="col-xs-7" align="left" id="div_edit_destination_code">
                                        <select class="form-control select_edit_destination_code"
                                            id="edit_destination_code" name="edit_destination_code"
                                            data-placeholder="Select Destination Code" style="width: 100%">
                                            <option value=""></option>
                                            @foreach ($destination as $row)
                                                <option value="{{ $row->destination_code }}">{{ $row->destination_code }}
                                                    - {{ $row->destination_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">TOWARDS<span class="text-red">*</span></label>
                                    <div class="col-xs-7" align="left" id="div_edit_toward">
                                        <select class="form-control select_edit_toward" multiple="multiple"
                                            placeholder="Choose a Toward ..." name="edit_toward[]" id="edit_toward"
                                            style="width:100%">
                                            <option value=""></option>
                                            <option value="YAMAHA MUSIC MANUFACTURING JAPAN CORPORATION BO & GD SECTION">
                                                YMMJ</option>
                                            <option value="XIAOSHAN YAMAHA MUSICAL INSTRUMENT CO.,LTD">XY</option>
                                            <option value="YAMAHA CORPORATION">YCJ/YMJ</option>
                                            <option value="YAMAHA MUSIC EUROPE">YME</option>
                                            <option value="YAMAHA MUSIC KOREA LTD.">YMK</option>
                                            <option value="YAMAHA CORPORATION C/O MOL LOGISTIC S PASIR GUDANG WAREHOUSE">
                                                TASCO</option>
                                            <option value="YCA,BAND&ORCHESTRAL DIV.">YCA</option>
                                            <option value="SIAM MUSIC YAMAHA CO., LTD">SMY</option>
                                            <option value="PT. YAMAHA MUSIK INDONESIA DISTRIBUTOR">YMID</option>
                                            <option value="YAMAHA ELECTRONICS MFG INDONESIA">YEMI</option>
                                            <option value="YAMAHA DE MEXICO S.A. DE C.V.">YDM</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">CARRIER<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left" id="div_edit_carier">
                                        <select class="form-control select_edit_carier" name="edit_carier"
                                            id="edit_carier" data-placeholder="Choose a Carrier ..."
                                            style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($carier as $row)
                                                <option value="{{ $row->shipment_condition_code }}">
                                                    {{ $row->shipment_condition_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">CONTAINER SIZE<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left" id="div_edit_ct_size">
                                        <select class="form-control select_edit_ct_size" name="edit_ct_size"
                                            id="edit_ct_size" data-placeholder="Choose a Size ..." style="width: 100%;">
                                            <option value=""></option>
                                            <option value="20FT">20FT</option>
                                            <option value="40FT">40FT</option>
                                            <option value="40FT HC">40FT HC</option>
                                            <option value="TRUCK">TRUCK</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">NO POL<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" name="edit_nopol" class="form-control" id="edit_nopol"
                                            placeholder="Input No Pol ..." required>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">CONTAINER NO.<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" name="edit_countainer_number" class="form-control"
                                            id="edit_countainer_number" placeholder="Input Container No ..." required>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">SEAL NO<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" name="edit_seal_number" class="form-control"
                                            placeholder="Input Seal No ..." id="edit_seal_number">
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">DELIVERY ORDER<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" class="form-control" name="edit_do_number"
                                            id="edit_do_number" placeholder="Input Delivery Order ..." required>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">INVOICE NO.<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" class="form-control" name="edit_invoice" id="edit_invoice"
                                            placeholder="Input IV No ..." required>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">INVOICE DATE<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left">
                                        <input type="text" class="form-control" name="edit_invoice_date"
                                            id="edit_invoice_date" placeholder="Choose IV date ..." autocomplete="off"
                                            required>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">PAYMENT<span class="text-red">*</span></label>
                                    <div class="col-xs-5" align="left" id="div_edit_payment">
                                        <select class="form-control select_edit_payment" name="edit_payment"
                                            id="edit_payment" data-placeholder="Choose a Payment ..."
                                            style="width: 100%;">
                                            <option value=""></option>
                                            <option value="T/T REMITTANCE">T/T REMITTANCE</option>
                                            <option value="D/P AT SIGHT">D/P AT SIGHT</option>
                                            <option value="D/A 60 DAYS AFTER BL DATE">D/A 60 DAYS AFTER BL DATE</option>
                                            <option value="FREE OF CHARGES">FREE OF CHARGES</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row" align="right">
                                    <label class="col-xs-4">REASON<span class="text-red">*</span></label>
                                    <div class="col-xs-7" align="left">
                                        <textarea name="edit_reason" class="form-control" id="edit_reason"></textarea>
                                    </div>
                                </div>
                            </div>
                            <input type="text" name="id_chek" id="id_chek" hidden>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button id="modaleditButton" type="submit" class="btn btn-success">Edit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="importModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="importForm2" method="post" action="{{ url('importDetail/CheckSheet') }}"
                        enctype="multipart/form-data">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                        <div class="modal-header">Re - Import Data</div>
                        <div class="">
                            <div class="modal-body">
                                Are you sure to Re - Import?<br>
                                All Data Will be Delete and Re - Import

                                <center>
                                    <i class="fa fa-spinner fa-spin" id="loading" style="font-size: 80px;"></i>
                                </center>
                                <input type="text" name="idcs" id="idcs" hidden="">
                                <input type="text" name="master_id" value="{{ $time->id_checkSheet }}" hidden>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                                <button id="modalImportButton" type="button" class="btn btn-success"
                                    onclick="deleteReimport()">Re - Import</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="importModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="importForm2" method="post" action="{{ url('importDetail/CheckSheet') }}"
                        enctype="multipart/form-data">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
                            <u>Format</u> :<br>
                            [<b><i>Destination</i></b>]
                            [<b><i>Invoice</i></b>]
                            [<b><i>GMC</i></b>]
                            [<b><i>Goods</i></b>]
                            [<b><i>Marking No</i></b>]
                            [<b><i>Package Qty</i></b>]
                            [<b><i>Package Type</i></b>]
                            [<b><i>Qty Qty</i></b>]
                            [<b><i>Qty Set</i></b>]
                            [<b><i>Box/Package</i></b>]
                            <br>
                            Sample: <a
                                href="{{ url('download/manual/import_check_sheet_detail.txt') }}">import_check_sheet_detail.txt</a>
                            Code: #Truncate
                        </div>
                        <div class="">
                            <div class="modal-body">
                                <center><input type="file" name="check_sheet_import2" id="InputFile"
                                        accept="text/plain" required=""></center>
                                <input type="text" name="idcs2" id="idcs2" hidden="">
                                <input type="text" name="master_id" value="{{ $time->id_checkSheet }}" hidden>
                            </div>
                            <div class="modal-footer">
                                <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
    </section>
@stop

@section('scripts')
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script>
        jQuery(document).ready(function() {

            $(document).ready(function() {
                $('body').toggleClass("sidebar-collapse");
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

            $('#etd_sub').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true
            })

            $('#Stuffing_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true
            })

            $('#invoice_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true
            })

            $('#edit_etd_sub').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true
            })

            $('#edit_stuffing_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true
            })
            $('#edit_invoice_date').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
                todayHighlight: true
            })

            $('.select_ycj_ref_number').select2({
                dropdownParent: $('#div_ycj_ref_number'),
                allowClear: true
            });

            $('.select_destination_code').select2({
                dropdownParent: $('#div_destination_code'),
                allowClear: true
            });

            $('.select_toward').select2({
                dropdownParent: $('#div_toward')
            });

            $('.select_carier').select2({
                dropdownParent: $('#div_carier'),
                allowClear: true
            });

            $('.select_ct_size').select2({
                dropdownParent: $('#div_ct_size'),
                allowClear: true
            });

            $('.select_payment').select2({
                dropdownParent: $('#div_payment'),
                allowClear: true
            });

            $('.select_edit_ycj_ref_number').select2({
                dropdownParent: $('#div_edit_ycj_ref_number'),
                allowClear: true
            });

            $('.select_edit_destination_code').select2({
                dropdownParent: $('#div_edit_destination_code'),
                allowClear: true
            });

            $('.select_edit_toward').select2({
                dropdownParent: $('#div_edit_toward')
            });

            $('.select_edit_carier').select2({
                dropdownParent: $('#div_edit_carier'),
                allowClear: true
            });

            $('.select_edit_ct_size').select2({
                dropdownParent: $('#div_edit_ct_size'),
                allowClear: true
            });

            $('.select_edit_payment').select2({
                dropdownParent: $('#div_edit_payment'),
                allowClear: true
            });

            showTable();

        });
        $(function() {

            $('#example2').DataTable({
                'paging': true,
                'lengthChange': false,
                'searching': false,
                'ordering': true,
                'info': true,
                'autoWidth': false
            })
        })

        function showTable() {
            $('#example1 tfoot th').each(function() {
                var title = $(this).text();
                $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' + title +
                    '" size="3"/>');
            });
            var table = $('#example1').DataTable({
                "order": [],
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
                }
            });

            table.columns().every(function() {
                var that = this;

                $('input', this.footer()).on('keyup change', function() {
                    if (that.search() !== this.value) {
                        that
                            .search(this.value)
                            .draw();
                    }
                });
            });

            $('#example1 tfoot tr').appendTo('#example1 thead');
        }

        function deleteConfirmation(url, name, id) {
            jQuery('#modalDeleteBody').text("Are you sure want to delete '" + name + "'");
            jQuery('#modalDeleteButton').attr("href", url + '/' + id);
        }

        function editConfirmation(url, name, id) {


            $("#loading-animation").show();

            $.get('{{ url('fetch/CheckSheet') }}' + '/' + id, function(result, status, xhr) {
                if (result.status) {

                    document.getElementById("edit_period").value = result.time.period;
                    document.getElementById("edit_stuffing_date").value = result.time.Stuffing_date;
                    document.getElementById("edit_etd_sub").value = result.time.etd_sub;
                    document.getElementById("edit_destination").value = result.time.destination;
                    document.getElementById("edit_shipped_to").value = result.time.shipped_to;

                    var towards = result.time.toward.split('-');
                    console.log(towards);
                    $("#edit_toward").val(towards).trigger('change.select2');

                    if (result.time.destination_code != null) {
                        $("#edit_destination_code").val(result.time.destination_code).trigger('change.select2');
                    }

                    $("#edit_carier").val(result.time.carier).trigger('change.select2');
                    $("#edit_ct_size").val(result.time.ct_size).trigger('change.select2');
                    document.getElementById("edit_nopol").value = result.time.no_pol;
                    document.getElementById("edit_countainer_number").value = result.time.countainer_number;
                    document.getElementById("edit_seal_number").value = result.time.seal_number;
                    document.getElementById("edit_do_number").value = result.time.do_number;
                    document.getElementById("edit_invoice").value = result.time.invoice;
                    document.getElementById("edit_invoice_date").value = result.time.invoice_date;
                    $("#edit_payment").val(result.time.payment).trigger('change.select2');
                    document.getElementById("edit_reason").value = result.time.reason;
                    showPeriod(result.time.ycj_ref_number);

                    document.getElementById("myModalLabelt").innerHTML = "Edit Confirmation " + result.time
                        .id_checkSheet;

                    jQuery('#modaleditButton').attr("href", url + '/' + id);
                    $('#Editform').attr('action', url + '/' + id);

                    $("#loading-animation").hide();

                }
            });


        }

        function reason(id) {
            var data = {
                id: id
            }
            $.get('{{ url('fill/reason') }}', data, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
                if (xhr.status == 200) {
                    if (result.status) {
                        $('#reason').val(result.reason.reason);
                        $('#invoice_dateE').val(result.reason.invoice_date);
                    } else {
                        alert('Attempt to retrieve data failed');
                    }
                } else {
                    alert('Disconnected from server');
                }
            });

        }


        function addInspection(id) {
            var a = id;
            var id = document.getElementById("id_checkSheet_master" + a).innerHTML;

            var data = {
                id: id,
            }

            $.post('{{ url('add/CheckSheet') }}', data, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
            });
        }

        function cektgl() {

            var date = $('#Stuffing_date').val();
            var on_or = $('#etd_sub').val();

            var start = new Date(date),
                end = new Date(on_or),
                diff = new Date(end - start),
                days = diff / 1000 / 60 / 60 / 24;

            var carier = $('#carier').val();
            var ycj_ref_number = $('#ycj_ref_number').val();
            var period = $('#period').val();
            var destination_code = $('#destination_code').val();
            var countainer_number = $('#countainer_number').val();

            if (destination_code == '') {
                alert('Please Input Destination Code');
                return false;
            }

            if (days < 0) {
                alert('Please Check Stuffing date And Date ON OR ABOUT');
                return false;
            }

            if (carier == 'C1' && ycj_ref_number == '') {
                alert('Please Input YCJ REf Number');
                return false;
            }

            if (carier == 'C1' && (countainer_number == '' || countainer_number == '-')) {
                alert('Please Input Container Number');
                return false;
            }

            document.getElementById("importForm").submit();

        }

        function getid(id) {
            var id_chek;
            id_chek = $("#" + id + " td:nth-child(1)").text();
            $('#idcs').val(id_chek);
            $('#idcs2').val(id_chek);
            $('#loading').hide();
        }

        function deleteReimport() {
            var id = $('#idcs').val();

            var data = {
                id: id
            }

            $.get('{{ url('delete/deleteReimport') }}', data, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
                if (xhr.status == 200) {
                    if (result.status) {
                        $('#loading').show();
                        setTimeout(function() {
                            $('#importModal2').modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                            $('#importModal2').modal('show');
                        }, 2000);
                    } else {
                        alert('Attempt to retrieve data failed');
                    }
                } else {
                    alert('Disconnected from server');
                }
            });

        }

        $("#period").change(function() {
            $("#loading").show();

            var period = $(this).val();
            var data = {
                period: period
            }
            $.ajax({
                type: "GET",
                dataType: "html",
                url: "{{ url('fetch/get_ref_number') }}",
                data: data,
                success: function(message) {
                    $("#ycj_ref_number").html(message);
                    $("#loading").hide();
                }
            });
        });


        function showPeriod(ycj_ref_number) {

            $("#edit_ycj_ref_number").html('');

            var period = $("#edit_period").val();
            var data = {
                period: period
            }
            $.ajax({
                type: "GET",
                dataType: "html",
                url: "{{ url('fetch/get_ref_number') }}",
                data: data,
                success: function(message) {
                    $("#edit_ycj_ref_number").html(message);
                    $("#edit_ycj_ref_number").val(ycj_ref_number).trigger('change.select2');
                }
            });

        }
    </script>

@stop
