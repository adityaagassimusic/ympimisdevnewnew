@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        /*Start CSS Numpad*/
        .nmpd-grid {
            border: none;
            padding: 20px;
        }

        .nmpd-grid>tbody>tr>td {
            border: none;
        }

        /*End CSS Numpad*/

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

        #master:hover {
            cursor: pointer;
        }

        #master {
            font-size: 17px;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            padding-top: 5px;
            padding-bottom: 5px;
            vertical-align: middle;
            color: white;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            padding-top: 5px;
            padding-bottom: 5px;
            vertical-align: middle;
            background-color: white;
        }

        thead {
            background-color: rgb(126, 86, 134);
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #loading,
        #error {
            display: none;
        }

        #qr_code {
            text-align: center;
            font-weight: bold;
        }

        .input {
            text-align: center;
            font-weight: bold;
        }
    </style>
@stop
@section('header')
@endsection
@section('content')
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <input id="role_code" value="{{ Auth::user()->role_code }}" hidden>
        <input id="employee_id" value="{{ strtoupper(Auth::user()->username) }}" hidden>

        <div class="row" style="">
            <div class="col-xs-12" style="">
                <div class="col-xs-2 col-md-1 col-lg-1" style="padding-left: 0; margin-bottom: 1%;">
                    <button style="font-weight: bold;" class="btn btn-lg btn-default" data-toggle="modal"
                        data-target="#scanModal"><i class="fa fa-camera"></i>&nbsp;&nbsp;REVISI</button>
                </div>

                @if (Auth::user()->role_code == 'S-PC' || Auth::user()->role_code == 'S-MIS')
                    <div class="col-xs-2 col-md-1 col-lg-1" style="padding-left: 0; margin-bottom: 1%;">
                        <button style="font-weight: bold;" class="btn btn-lg btn-default" data-toggle="modal"
                            data-target="#uploadModal"><i class="fa fa-upload"></i>&nbsp;UPLOAD</button>
                    </div>
                @else
                    <div class="col-xs-2 col-md-1 col-lg-1" style="padding-left: 0; margin-bottom: 2%;">
                    </div>
                @endif

                <div class="col-xs-12 col-md-10 col-lg-10" id="last_update"
                    style="padding: 0%; color: white; vertical-align: middle;"></div>

            </div>

            <div class="col-xs-12 col-md-12 col-lg-12">
                <div class="box box-primary">
                    <div class="box-body" style="overflow-x: auto;">
                        <div class="col-xs-12" style="margin: 1% 0% 1% 0%; padding: 0px;">
                            <div class="col-xs-12 col-md-offset-3 col-md-1 col-lg-offset-3 col-lg-1"
                                style="margin: 0.5%; padding: 0px;">
                                <a href="javascript:void(0)"
                                    style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: #90ed7d;"
                                    id="btn_ALL" onclick="btnCategory('ALL')" class="btn btn-sm">ALL</a>
                            </div>
                            <div class="col-xs-12 col-md-offset-3 col-md-1 col-lg-offset-3 col-lg-1"
                                style="margin: 0.5%; padding: 0px;">
                                <a href="javascript:void(0)"
                                    style="font-weight: bold; width: 100%; border-color: black; color: black;"
                                    id="btn_UPDATED" onclick="btnCategory('UPDATED')" class="btn btn-sm">UPDATED</a>
                            </div>
                            <div class="col-xs-12 col-md-offset-3 col-md-1 col-lg-offset-3 col-lg-1"
                                style="margin: 0.5%; padding: 0px;">
                                <a href="javascript:void(0)"
                                    style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                                    id="btn_CHECKED" onclick="btnCategory('CHECKED')" class="btn btn-sm">CHECKED</a>
                            </div>
                            <div class="col-xs-12 col-md-offset-3 col-md-1 col-lg-offset-3 col-lg-1"
                                style="margin: 0.5%; padding: 0px;">
                                <a href="javascript:void(0)"
                                    style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                                    id="btn_REQUESTED" onclick="btnCategory('REQUESTED')" class="btn btn-sm">REQUESTED</a>
                            </div>
                            <div class="col-xs-12 col-md-offset-3 col-md-1 col-lg-offset-3 col-lg-1"
                                style="margin: 0.5%; padding: 0px;">
                                <a href="javascript:void(0)"
                                    style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                                    id="btn_REJECTED" onclick="btnCategory('REJECTED')" class="btn btn-sm">REJECTED</a>
                            </div>
                        </div>
                        <div id="divTableDetail"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal modal-default fade" id="scanModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title text-center"><b>REVISI PI</b></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id='scanner' class="col-xs-12">
                            <h4 class="text-center"><b>SCAN QR CODE HERE</b></h4>
                            <center>
                                <div id="loadingMessage">
                                    🎥 Unable to access video stream
                                    (please make sure you have a webcam enabled)
                                </div>
                                <video style="max-width: 100% !important;" autoplay muted playsinline
                                    id="video"></video>
                                <div id="output" hidden>
                                    <div id="outputMessage">No QR code detected.</div>
                                </div>
                            </center>
                        </div>

                        <p style="visibility: hidden;">camera</p>

                        <div id="revisi-main-tab" style="display: none;" class="col-xs-10 col-xs-offset-1">
                            <div class="col-xs-12" style="padding: 0px; width: 100%;">
                                <input type="hidden" id="st_id">

                                <div class="col-xs-12 col-md-6 col-lg-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Area</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="area" readonly>
                                </div>

                                <div class="col-xs-12 col-md-6 col-lg-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Storage Location</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="storage_location" readonly>
                                </div>

                                <div class="col-xs-12 col-md-6 col-lg-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Store</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="store" readonly>
                                </div>

                                <div class="col-xs-12 col-md-6 col-lg-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Sub Store</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="sub_store" readonly>
                                </div>

                                <div class="col-xs-12 col-md-6 col-lg-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">GMC</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="material_number" readonly>
                                </div>

                                <div class="col-xs-12 col-md-6 col-lg-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Category</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="category" readonly>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Description</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="material_description" readonly>
                                </div>

                                <div class="col-xs-12 col-md-6 col-lg-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">QTY Sebelum</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="quantity" readonly>
                                </div>

                                <div class="col-xs-12 col-md-6 col-lg-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">QTY Revisi<span
                                            class="text-red">*</span></label>
                                    <input style="text-align: center;" class="form-control" type="number"
                                        id="quantity_revisi" placeholder="Masukan Qty Revisi">
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Reason<span
                                            class="text-red">*</span></label>
                                    <select class="form-control select2" name="reason" id='reason'
                                        style="width: 100%;" data-placeholder="Masukan Reason">
                                        <option value=""></option>
                                        <option value="Salah input PI">
                                            Salah input PI
                                        </option>
                                        <option value="Kesalahan input transaksi return/repair">
                                            Kesalahan input transaksi return/repair
                                        </option>
                                        <option value="Salah hitung">
                                            Salah hitung
                                        </option>
                                        <option value="Belum terhitung">
                                            Belum terhitung
                                        </option>
                                        <option value="Salah identifikasi item single/assy">
                                            Salah identifikasi item single/assy
                                        </option>
                                        <option value="Salah input transaksi loc transfer dari maekotei">
                                            Salah input transaksi loc transfer dari maekotei
                                        </option>
                                    </select>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Note</label>
                                    <textarea id="note" style="height: 100px; width: 100%;" placeholder="Masukan Catatan"></textarea>
                                </div>

                                <div class="col-xs-12" style="margin: 2% 0% 5% 0%;">
                                    <center>
                                        <input type="file" class="file" style="display:none"
                                            onchange="readURL(this);" id="input_photo">
                                        <button class="btn btn-primary btn-lg" id="btn_image" value="Photo"
                                            onclick="buttonImage(this)"
                                            style="font-size: 30px; width: 80%; height: 200px;">
                                            <i class="fa fa-file-image-o"></i>&nbsp;&nbsp;&nbsp;Evidence
                                        </button>
                                        <img width="150px" id="img_photo" src="" onclick="buttonImage(this)"
                                            style="display: none; width: 80% height: 200px;" alt="your image" />
                                    </center>
                                </div>
                                <center>
                                    <button class="btn btn-lg btn-success" id="submit_checklist"
                                        style="width: 50%; font-size: 25px; margin-bottom: 10%;" onclick="submitRevise()">
                                        <i class="fa fa-save"></i>&nbsp;&nbsp;Submit
                                    </button>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-default fade" id="executeModal">
        <div class="modal-dialog" role="document" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title text-center"><b>EKSEKUSI REVISI PI</b></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-1">
                            <div class="col-xs-12" style="padding: 0px; width: 100%;">
                                <input type="hidden" id="execute_id">

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Area</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="execute_area" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Storage Location</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="execute_storage_location" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Store</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="execute_store" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Sub Store</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="execute_sub_store" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">GMC</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="execute_material_number" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Category</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="execute_category" readonly>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Description</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="execute_material_description" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">QTY Sebelum</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="execute_before" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">QTY Revisi</label>
                                    <input style="text-align: center;" class="form-control" type="number"
                                        id="execute_after" readonly>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Reason</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="execute_reason" readonly>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Note</label>
                                    <textarea id="execute_note" style="height: 100px; width: 100%;"></textarea>
                                </div>

                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="col-xs-12" style="margin: 2% 0% 5% 0%;">
                                <center>
                                    <img id="img_execute" src="" style="width: 100%; height: 100%;"
                                        alt="your image" />
                                </center>
                            </div>

                        </div>
                        <div class="col-xs-12">
                            <center>
                                <button class="btn btn-lg btn-success" id="submit_executelist"
                                    style="width: 30%; font-size: 25px; margin: 5% 0% 5% 0%; color:black; background-color: #ff5a36;"
                                    onclick="executeRevise('REJECTED')">
                                    <i class="fa fa-close"></i>&nbsp;&nbsp;Reject
                                </button>
                                <button class="btn btn-lg btn-success" id="submit_executelist"
                                    style="width: 30%; font-size: 25px; margin: 5% 0% 5% 0%; color:black; background-color: #8ade79;"
                                    onclick="executeRevise('OK')">
                                    <i class="fa fa-save"></i>&nbsp;&nbsp;Update PI
                                </button>
                            </center>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-default fade" id="checkModal">
        <div class="modal-dialog" role="document" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title text-center"><b>CHECK REVISI PI</b></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-1">
                            <div class="col-xs-12" style="padding: 0px; width: 100%;">
                                <input type="hidden" id="check_id">

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Area</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="check_area" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Storage Location</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="check_storage_location" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Store</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="check_store" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Sub Store</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="check_sub_store" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">GMC</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="check_material_number" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Category</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="check_category" readonly>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Description</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="check_material_description" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">QTY Sebelum</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="check_before" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">QTY Revisi</label>
                                    <input style="text-align: center;" class="form-control" type="number"
                                        id="check_after" readonly>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Reason</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="check_reason" readonly>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Note</label>
                                    <textarea id="check_note" style="height: 100px; width: 100%;" placeholder="Masukan Catatan"></textarea>
                                </div>

                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="col-xs-12" style="margin: 2% 0% 5% 0%;">
                                <center>
                                    <img id="img_check"src="" style="width: 100%; height: 100%;"
                                        alt="your image" />
                                </center>
                            </div>

                        </div>
                        <div class="col-xs-12">
                            <center>
                                <button class="btn btn-lg btn-success" id="submit_executelist"
                                    style="width: 30%; font-size: 25px; margin: 5% 0% 5% 0%; color:black; background-color: #ff5a36;"
                                    onclick="checkRevise('REJECTED')">
                                    <i class="fa fa-close"></i>&nbsp;&nbsp;Reject
                                </button>
                                <button class="btn btn-lg btn-success" id="submit_executelist"
                                    style="width: 30%; font-size: 25px; margin: 5% 0% 5% 0%; color:black; background-color: #ecff7b;"
                                    onclick="checkRevise('OK')">
                                    <i class="fa fa-save"></i>&nbsp;&nbsp;Check OK
                                </button>
                            </center>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-default fade" id="eviModal">
        <div class="modal-dialog" role="document" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title text-center"><b>EVIDENCE REVISI PI</b></h3>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-1">
                            <div class="col-xs-12" style="padding: 0px; width: 100%;">
                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Area</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="evi_area" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Storage Location</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="evi_storage_location" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Store</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="evi_store" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Sub Store</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="evi_sub_store" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">GMC</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="evi_material_number" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Category</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="evi_category" readonly>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Description</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="evi_material_description" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">QTY Sebelum</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="evi_before" readonly>
                                </div>

                                <div class="col-xs-6" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">QTY Revisi</label>
                                    <input style="text-align: center;" class="form-control" type="number"
                                        id="evi_after" readonly>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Reason</label>
                                    <input style="text-align: center;" class="form-control" type="text"
                                        id="evi_reason" readonly>
                                </div>

                                <div class="col-xs-12" style="padding: 0px 5px 0px 5px;">
                                    <label style="padding: 0px; color: #151515;">Note</label>
                                    <textarea id="evi_note" style="height: 100px; width: 100%;" readonly></textarea>
                                </div>

                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="col-xs-12" style="margin: 2% 0% 15% 0%;">
                                <center>
                                    <img id="img_evi" src="" style="width: 100%; height: 100%;"
                                        alt="your image" />
                                </center>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadModal">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">UPLOAD REVISI STOCKTAKING</h4>
                    <span>
                        Format Upload:<br>
                        [<b><i>ID STOCKTAKING</i></b>]
                        [<b><i>QTY REVISI</i></b>]
                        [<b><i>REASON</i></b>]
                    </span>
                </div>
                <div class="modal-body" style="min-height: 100px">
                    <div class="form-group">
                        <textarea id="upload" style="height: 100px; width: 100%;"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success pull-right" onclick="uploadData()">SUBMIT</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadResult">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">UPLOAD RESULT</h4>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <span style="font-size:1.5vw;">Success: <span id="suceess-count"
                            style="font-style:italic; font-weight:bold; color: green;"></span> Row(s)</span>
                    <span style="font-size:1.5vw;"> ~ Error: <span id="error-count"
                            style="font-style:italic; font-weight:bold; color: red;"></span> Row(s)</span>

                    <table id="tableError" style="border: none;">
                        <tbody id="bodyError">
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
    <script src="{{ url('js/jsQR.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {

            $('.select2').select2();
            refreshData();
            category = '';

        });

        var video;
        var revise_data;
        var filter_status;
        var category;

        function stopScan() {
            $('#scanModal').modal('hide');
        }

        function videoOff() {
            video.pause();
            video.src = "";
            video.srcObject.getTracks()[0].stop();
        }

        $("#scanModal").on('shown.bs.modal', function() {
            showCheck('123');

            $("#scanner").css('display', 'block');
            $("#revisi-main-tab").css('display', 'none');

            $("#quantity_revisi").val('');
            $('#reason').val('').trigger('change.select2');
            $("#input_photo").val('');

            $('#btn_image').css('display', 'block');
            $('#img_photo').css('display', 'none');
            $('#img_photo').attr('src', '');

        });

        $('#scanModal').on('hidden.bs.modal', function() {
            videoOff();
        });


        function showCheck(kode) {
            $(".modal-backdrop").add();
            $('#scanner').show();

            var vdo = document.getElementById("video");
            video = vdo;
            var tickDuration = 200;
            video.style.boxSizing = "border-box";
            video.style.position = "absolute";
            video.style.left = "0px";
            video.style.top = "0px";
            video.style.width = "400px";
            video.style.zIndex = 1000;

            var loadingMessage = document.getElementById("loadingMessage");
            var outputContainer = document.getElementById("output");
            var outputMessage = document.getElementById("outputMessage");

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                }
            }).then(function(stream) {
                video.srcObject = stream;
                video.play();
                setTimeout(function() {
                    tick();
                }, tickDuration);
            });

            function tick() {
                loadingMessage.innerText = "⌛ Loading video..."

                try {

                    loadingMessage.hidden = true;
                    video.style.position = "static";

                    var canvasElement = document.createElement("canvas");
                    var canvas = canvasElement.getContext("2d");
                    canvasElement.height = video.videoHeight;
                    canvasElement.width = video.videoWidth;
                    canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                    var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                    var code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert"
                    });
                    if (code) {
                        console.log(code);

                        outputMessage.hidden = true;
                        videoOff();
                        checkSumOfCount(code.data);

                        console.log('A' + code.data);

                    } else {
                        outputMessage.hidden = false;
                    }
                } catch (t) {
                    console.log("PROBLEM: " + t);
                }

                setTimeout(function() {
                    tick();
                }, tickDuration);
            }

        }

        function checkSumOfCount(code) {

            console.log('B' + code);


            var data = {
                id: code
            }

            $.get('{{ url('fetch/stocktaking/material_detail_new') }}', data, function(result, status, xhr) {

                if (result.status) {

                    if (result.material.length == 0) {
                        openErrorGritter('Error', 'QR Code Tidak Terdaftar');
                        $('#scanModal').modal('hide');
                        return false;
                    }

                    $("#st_id").val(result.material[0].id);
                    $("#area").val(result.material[0].area);
                    $("#storage_location").val(result.material[0].location);
                    $("#store").val(result.material[0].store);
                    $("#sub_store").val(result.material[0].sub_store);
                    $("#material_number").val(result.material[0].material_number);
                    $("#category").val(result.material[0].category);
                    $("#material_description").val(result.material[0].material_description);
                    $("#quantity").val((result.material[0].final_count || '0'));

                    videoOff();
                    $("#scanner").css('display', 'none');
                    $("#revisi-main-tab").css('display', 'block');

                } else {
                    openErrorGritter('Error', 'QR Code Tidak Terdaftar');
                    $('#scanModal').modal('hide');
                    return false;
                }

            });

        }

        function buttonImage(elem) {
            $(elem).closest("center").find("input").click();
        }

        const compressImage = async (file, {
            quality = 1,
            type = file.type
        }) => {

            const imageBitmap = await createImageBitmap(file);

            const canvas = document.createElement('canvas');
            canvas.width = imageBitmap.width;
            canvas.height = imageBitmap.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(imageBitmap, 0, 0);

            const blob = await new Promise((resolve) =>
                canvas.toBlob(resolve, type, quality)
            );

            return new File([blob], file.name, {
                type: blob.type,
            });
        };

        const input = document.querySelector('.file');
        input.addEventListener('change', async (e) => {
            const {
                files
            } = e.target;

            if (!files.length) return;

            const dataTransfer = new DataTransfer();

            for (const file of files) {

                if (!file.type.startsWith('image')) {
                    dataTransfer.items.add(file);
                    continue;
                }

                const compressedFile = await compressImage(file, {
                    quality: 0.3,
                    type: 'image/jpeg',
                });

                dataTransfer.items.add(compressedFile);

                readURL(compressedFile);
            }

            e.target.files = dataTransfer.files;
        });

        function readURL(compressedFile) {
            var reader = new FileReader();
            var img = $(compressedFile).closest("center").find("img");
            reader.onload = function(e) {
                $(img).show();
                $(img).attr('src', e.target.result);
            };
            reader.readAsDataURL(compressedFile.files[0]);
            $(compressedFile).closest("center").find("button").hide();
            // saveImageEvidence(compressedFile);
        }

        function numberValidation(id) {
            var number = /^[0-9.]+$/;

            if (!id.match(number)) {
                return false;
            } else {
                return true;
            }
        }

        function submitRevise() {

            var st_id = $("#st_id").val();
            var quantity_revisi = $("#quantity_revisi").val();
            var reason = $("#reason").val();
            var note = $("#note").val();

            if (!numberValidation(quantity_revisi)) {
                openErrorGritter("Error!", "Qty revisi tidak valid");
                return false;
            }

            if (reason == '') {
                openErrorGritter("Error!", "Reason harus diisi");
                return false;
            }

            if (document.getElementById("input_photo").files.length == 0) {
                openErrorGritter("Error!", "Evidence harus disertakan");
                return false;
            }

            var formData = new FormData();
            formData.append('st_id', st_id);
            formData.append('quantity', quantity_revisi);
            formData.append('reason', reason);
            formData.append('note', note);
            formData.append('file_datas', $('#input_photo').prop('files')[0]);
            var file = $('#input_photo').val().replace(/C:\\fakepath\\/i, '').split(".");

            formData.append('extension', file[1]);
            formData.append('photo_name', file[0]);

            $('#loading').show();

            $.ajax({
                url: "{{ url('input/stocktaking/input_revise_user') }}",
                method: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(result, status, xhr) {
                    if (result.status) {
                        refreshData();
                        $("#scanModal").modal('hide');
                        $('#loading').hide();
                        openSuccessGritter("Success", "Permintaan revisi PI berhasil dikirim");
                    } else {
                        $('#loading').hide();
                        openErrorGritter("Error", result.message);
                    }

                },
                error: function(result, status, xhr) {
                    $('#loading').hide();
                    openErrorGritter("Error", result.message);
                },
            })

        }


        function refreshData() {

            $.get('{{ url('fetch/stocktaking/revise_user') }}', function(result, status, xhr) {
                if (result.status) {
                    $('#last_update').html(
                        '<p class="pull-right" style="margin: 0px; font-size: 10pt;"><i class="fa fa-fw fa-clock-o"></i> Last Updated: ' +
                        result.now + '</p>');

                    revise_data = result.data;

                    if (category == '') {
                        showTable('ALL');
                    } else {
                        showTable(category);
                    }

                }
            });
        }

        function btnCategory(cat) {
            $('#btn_ALL').css('background-color', 'white');
            $('#btn_REQUESTED').css('background-color', 'white');
            $('#btn_CHECKED').css('background-color', 'white');
            $('#btn_UPDATED').css('background-color', 'white');
            $('#btn_REJECTED').css('background-color', 'white');

            $('#btn_' + cat).css('background-color', '#90ed7d');

            category = cat;
            showTable(cat);

        }

        function showTable(status) {
            var role_code = $('#role_code').val();
            var employee_id = $('#employee_id').val();

            var verificator_id = [
                'PI0603010',
                'PI0202001',
                'PI9909004',
                'PI0102003',
                'PI2302031',
                'PI1810012',
                'PI1110001',
                'PI9903003',
                'PI9902018',
                'PI1911001',
                'PI9707010',
                'PI0004011',
                'PI0008008'
            ];

            $('#divTableDetail').html('');

            var tableHead = '';
            tableHead += '<thead style="background-color: rgba(126,86,134,.7);">';
            tableHead += '<tr>';
            tableHead += '<th style="width: 5%; text-align: center;">Status</th>';
            tableHead += '<th style="width: 5%; text-align: center;">ID</th>';
            tableHead += '<th style="width: 5%; text-align: center;">SLoc</th>';
            tableHead += '<th style="width: 15%; text-align: center;">Store</th>';
            tableHead += '<th style="width: 20%; text-align: center;">Material</th>';
            tableHead += '<th style="width: 5%; text-align: center;">Category</th>';
            tableHead += '<th style="width: 5%; text-align: center;">Evidence</th>';
            tableHead += '<th style="width: 5%; text-align: center;">Before</th>';
            tableHead += '<th style="width: 5%; text-align: center;">After</th>';
            tableHead += '<th style="width: 10%; text-align: center;">Created By</th>';
            tableHead += '<th style="width: 10%; text-align: center;">Checked By</th>';
            tableHead += '<th style="width: 10%; text-align: center;">Executed By</th>';
            tableHead += '</tr>';
            tableHead += '</thead>';

            var tableBody = '';
            tableBody += '<tbody id="bodyDetail">';
            for (var i = 0; i < revise_data.length; i++) {
                if (revise_data[i].status == status || status == 'ALL') {

                    tableBody += '<tr>';

                    if (revise_data[i].status == 'REJECTED') {
                        tableBody += '<td style="text-align: center; color: red; font-weight:bold;">';
                        tableBody += revise_data[i].status;
                        tableBody += '</td>';
                    } else if (revise_data[i].status == 'CHECKED') {
                        tableBody += '<td style="text-align: center; color: orange; font-weight:bold;">';
                        tableBody += revise_data[i].status;
                        tableBody += '</td>';
                    } else if (revise_data[i].status == 'UPDATED') {
                        tableBody += '<td style="text-align: center; color: green; font-weight:bold;">';
                        tableBody += revise_data[i].status;
                        tableBody += '</td>';
                    } else {
                        tableBody += '<td style="text-align: center; font-weight:bold;">';
                        tableBody += revise_data[i].status;
                        tableBody += '</td>';
                    }

                    tableBody += '<td style="text-align: center;">';
                    tableBody += 'ST_' + revise_data[i].st_id;
                    tableBody += '</td>';

                    tableBody += '<td style="text-align: center;">';
                    tableBody += revise_data[i].area + '<br>' + revise_data[i].location;
                    tableBody += '</td>';

                    tableBody += '<td style="text-align: left;">';
                    tableBody += revise_data[i].store + '<br>' + revise_data[i].sub_store;
                    tableBody += '</td>';

                    tableBody += '<td style="text-align: left;">';
                    tableBody += revise_data[i].material_number + '<br>' + revise_data[i].material_description;
                    tableBody += '</td>';

                    tableBody += '<td style="text-align: center;">';
                    tableBody += revise_data[i].category;
                    tableBody += '</td>';

                    tableBody += '<td style="text-align: center;">';
                    tableBody += '<a onclick="showModalEvi(\'' + revise_data[i].id + '\')" ';
                    tableBody += 'class="btn btn-primary btn-sm" ';
                    tableBody += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px; '
                    tableBody += 'color:black; background-color: #6cc8f3;">';
                    tableBody += '&nbsp;<i class="fa fa-paperclip"></i>&nbsp;</a>';
                    tableBody += '</td>';

                    tableBody += '<td style="text-align: center;">';
                    tableBody += revise_data[i].before;
                    tableBody += '</td>';

                    tableBody += '<td style="text-align: center;">';
                    tableBody += revise_data[i].quantity;
                    tableBody += '</td>';

                    tableBody += '<td style="text-align: center;">';
                    tableBody += callName(revise_data[i].created_by_name);
                    tableBody += '<br>';
                    tableBody += revise_data[i].created_at;
                    tableBody += '</td>';

                    if (revise_data[i].status == 'REJECTED') {
                        if (revise_data[i].checked_at != null) {
                            tableBody += '<td style="text-align: center;">';
                            tableBody += callName(revise_data[i].checked_by_name);
                            tableBody += '<br>';
                            tableBody += revise_data[i].checked_at;
                            tableBody += '</td>';
                        } else {
                            tableBody += '<td style="text-align: center;">-</td>';
                        }
                    } else {
                        if (revise_data[i].checked_at != null) {
                            tableBody += '<td style="text-align: center;">';
                            tableBody += callName(revise_data[i].checked_by_name);
                            tableBody += '<br>';
                            tableBody += revise_data[i].checked_at;
                            tableBody += '</td>';
                        } else {
                            if (role_code.includes('PRD') ||
                                role_code.includes('MIS')) {
                                tableBody += '<td style="text-align: center;">';
                                tableBody += '<a onclick="showModalCheck(\'' + revise_data[i].id + '\')" ';
                                tableBody += 'class="btn btn-primary btn-sm" ';
                                tableBody += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px; '
                                tableBody += 'color:black; background-color: #ecff7b;">';
                                tableBody += '&nbsp;<i class="fa fa-search"></i>&nbsp;Check</a>';
                                tableBody += '</td>';
                            } else {
                                tableBody += '<td style="text-align: center;">-</td>';
                            }
                        }
                    }

                    if (revise_data[i].status == 'REJECTED') {
                        if (revise_data[i].revised_at != null) {
                            tableBody += '<td style="text-align: center;">';
                            tableBody += callName(revise_data[i].revised_by_name);
                            tableBody += '<br>';
                            tableBody += revise_data[i].revised_at;
                            tableBody += '</td>';
                        } else {
                            tableBody += '<td style="text-align: center;">-</td>';
                        }
                    } else {
                        if (revise_data[i].revised_at != null) {
                            tableBody += '<td style="text-align: center;">';
                            tableBody += callName(revise_data[i].revised_by_name);
                            tableBody += '<br>';
                            tableBody += revise_data[i].revised_at;
                            tableBody += '</td>';
                        } else {
                            if (revise_data[i].checked_at != null) {
                                if (role_code.includes('PC') ||
                                    role_code.includes('MIS') ||
                                    verificator_id.includes(employee_id)) {
                                    tableBody += '<td style="text-align: center;">';
                                    tableBody += '<a onclick="showModalExecute(\'' + revise_data[i].id + '\')" ';
                                    tableBody += 'class="btn btn-success btn-sm" ';
                                    tableBody += 'style="margin: 1px; padding-top: 2px; padding-bottom: 2px; '
                                    tableBody += 'color:black; background-color: #8ade79;">';
                                    tableBody += '&nbsp;<i class="fa fa-check-square-o"></i>&nbsp;Execute</a>';
                                    tableBody += '</td>';
                                } else {
                                    tableBody += '<td style="text-align: center;">-</td>';
                                }
                            } else {
                                tableBody += '<td style="text-align: center;">-</td>';
                            }
                        }
                    }

                    tableBody += '</tr>';
                }
            }
            tableBody += '</tbody>';


            var tableFoot = '';
            tableFoot += '<tfoot id="footDetail">';
            tableFoot += '<tr>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '<th></th>';
            tableFoot += '</tr>';
            tableFoot += '</tfoot>';

            var tableData = '';
            tableData += '<table class="table table-hover table-bordered table-striped" id="tableDetail"';
            tableData += 'style="font-size: 10pt; width: 100%;">';
            tableData += tableHead;
            tableData += tableBody;
            tableData += tableFoot;
            tableData += '</table>';

            $('#divTableDetail').append(tableData);

            $('#tableDetail tfoot th').each(function() {
                var title = $(this).text();
                $(this).html(
                    '<input style="text-align: center; width: 100%; color: black;" type="text" placeholder="Search ' +
                    title +
                    '" size="4"/>');
            });
            var table = $('#tableDetail').DataTable({
                "order": [],
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [25, 50, 100, -1],
                    ['25 rows', '50 rows', '100 rows', 'Show all']
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
                },
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
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

            $('#tableDetail tfoot tr').appendTo('#tableDetail thead');

        }

        function showModalEvi(id) {
            for (let i = 0; i < revise_data.length; i++) {
                if (revise_data[i].id == id) {
                    $("#evi_area").val(revise_data[i].area);
                    $("#evi_storage_location").val(revise_data[i].location);
                    $("#evi_store").val(revise_data[i].store);
                    $("#evi_sub_store").val(revise_data[i].sub_store);
                    $("#evi_material_number").val(revise_data[i].material_number);
                    $("#evi_category").val(revise_data[i].category);
                    $("#evi_material_description").val(revise_data[i].material_description);
                    $("#evi_reason").val(revise_data[i].reason);
                    $("#evi_before").val(revise_data[i].before);
                    $("#evi_after").val(revise_data[i].quantity);
                    $("#evi_note").val((revise_data[i].note || ''));

                    $('#img_evi').attr('src',
                        '{{ url('files/stocktaking/revise_evidence') }}' + '/' + revise_data[i].evidence);

                    break;
                }

            }

            $("#eviModal").modal('show');
        }

        function showModalCheck(id) {

            for (let i = 0; i < revise_data.length; i++) {
                if (revise_data[i].id == id) {
                    $("#check_id").val(revise_data[i].id);
                    $("#check_area").val(revise_data[i].area);
                    $("#check_storage_location").val(revise_data[i].location);
                    $("#check_store").val(revise_data[i].store);
                    $("#check_sub_store").val(revise_data[i].sub_store);
                    $("#check_material_number").val(revise_data[i].material_number);
                    $("#check_category").val(revise_data[i].category);
                    $("#check_material_description").val(revise_data[i].material_description);
                    $("#check_reason").val(revise_data[i].reason);
                    $("#check_before").val(revise_data[i].before);
                    $("#check_after").val(revise_data[i].quantity);
                    $("#check_note").val((revise_data[i].note || ''));

                    $('#img_check').attr('src',
                        '{{ url('files/stocktaking/revise_evidence') }}' + '/' + revise_data[i].evidence);

                    break;
                }

            }

            $("#checkModal").modal('show');

        }

        function checkRevise(status) {

            var id = $("#check_id").val();
            var note = $("#check_note").val();

            var data = {
                id: id,
                status: status,
                note: note
            }

            $('#loading').show();

            $.post('{{ url('input/stocktaking/check_revise_user') }}', data, function(result, status, xhr) {
                if (result.status) {

                    refreshData();
                    $('#checkModal').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success', 'Permohonan revisi PI berhasil diverifikasi');


                } else {
                    $('#loading').hide();
                    openErrorGritter('Error', result.message);

                }
            });
        }

        function showModalExecute(id) {

            for (let i = 0; i < revise_data.length; i++) {
                if (revise_data[i].id == id) {

                    $("#execute_id").val(revise_data[i].id);
                    $("#execute_area").val(revise_data[i].area);
                    $("#execute_storage_location").val(revise_data[i].location);
                    $("#execute_store").val(revise_data[i].store);
                    $("#execute_sub_store").val(revise_data[i].sub_store);
                    $("#execute_material_number").val(revise_data[i].material_number);
                    $("#execute_category").val(revise_data[i].category);
                    $("#execute_material_description").val(revise_data[i].material_description);
                    $("#execute_reason").val((revise_data[i].reason));
                    $("#execute_before").val((revise_data[i].before));
                    $("#execute_after").val((revise_data[i].quantity));
                    $("#execute_note").val((revise_data[i].note || ''));


                    $('#img_execute').attr('src',
                        '{{ url('files/stocktaking/revise_evidence') }}' + '/' + revise_data[i].evidence);

                    break;
                }

            }

            $("#executeModal").modal('show');

        }


        function executeRevise(status) {

            var id = $("#execute_id").val();
            var note = $("#execute_note").val();

            var data = {
                id: id,
                status: status,
                note: note
            }

            $('#loading').show();

            $.post('{{ url('input/stocktaking/execute_revise_user') }}', data, function(result, status, xhr) {
                if (result.status) {

                    refreshData();
                    $('#executeModal').modal('hide');
                    $('#loading').hide();

                    if (status == 'REJECTED') {
                        openSuccessGritter('Success', 'PI berhasil direject');
                    } else {
                        openSuccessGritter('Success', 'PI berhasil diupdate');
                    }

                } else {
                    $('#loading').hide();
                    openErrorGritter('Error', result.message);

                }
            });
        }

        function uploadData(id) {
            $('#loading').show();
            var upload = $('#upload').val();

            if (upload == '') {
                openErrorGritter('Error!', 'Upload data empty');
            }

            var data = {
                upload: upload
            }

            $.post('{{ url('fetch/stocktaking/upload_revise') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#upload').val('');
                    $('#uploadModal').modal('hide');

                    $('#suceess-count').text(result.ok_count.length);
                    $('#error-count').text(result.error_count.length);

                    $('#bodyError').html("");
                    var tableData = "";
                    var css = "padding: 0px 5px 0px 5px;";
                    for (var i = 0; i < result.error_count.length; i++) {
                        var error = result.error_count[i].split('#');
                        tableData += '<tr>';
                        tableData += '<td style="' + css + ' width:20%; text-align:left;">Row ' + error[0] +
                            '</td>';
                        tableData += '<td style="' + css + ' width:80%; text-align:left;">: ' + error[1] + '</td>';
                        tableData += '</tr>';
                    }

                    if (result.error_count.length > 0) {
                        $('#bodyError').append(tableData);
                        $('#tableError').show();
                    }

                    $('#uploadResult').modal('show');
                    $('#loading').hide();

                    openSuccessGritter('Success!', result.message);
                } else {
                    $('#loading').hide();
                    alert(result.message);
                }
            });
        }


        function callName(name) {
            var new_name = '';
            var blok_m = [
                'M.',
                'MAS',
                'MOCH',
                'MOCH.',
                'MOCHAMAD',
                'MOCHAMMAD',
                'MOHAMMAD',
                'MOH.',
                'MOHAMAD',
                'MOKHAMAD',
                'MUCH.',
                'MUCHAMMAD',
                'MUHAMAD',
                'MUHAMMAAD',
                'MUHAMMAD',
                'MUKAMMAD',
                'MUHAMAD',
                'MUKHAMMAD'
            ];

            var blok_r = [
                'RR.',
            ];


            if (name != null) {

                if (name.includes(' ')) {
                    name = name.split(' ');

                    if (blok_m.includes(name[0])) {
                        new_name = 'M.';
                        for (i = 1; i < name.length; i++) {
                            if (i == 1) {
                                new_name += ' ';
                                new_name += name[i];
                            } else {
                                new_name += ' ';
                                new_name += name[i].substr(0, 1) + '.';
                            }
                        }
                    } else if (blok_r.includes(name[0])) {
                        if (blok_r.includes(name[0])) {
                            new_name = 'RR.';
                            for (i = 1; i < name.length; i++) {
                                if (i == 1) {
                                    new_name += ' ';
                                    new_name += name[i];
                                } else {
                                    new_name += ' ';
                                    new_name += name[i].substr(0, 1) + '.';
                                }
                            }
                        }
                    } else {
                        for (i = 0; i < name.length; i++) {
                            if (i == 0) {
                                new_name += ' ';
                                new_name += name[i];
                            } else {
                                new_name += ' ';
                                new_name += name[i].substr(0, 1) + '.';
                            }
                        }
                    }

                } else {
                    new_name = name;
                }
            } else {
                new_name = '-';
            }

            return new_name;
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
            return year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
        }
    </script>
@endsection
