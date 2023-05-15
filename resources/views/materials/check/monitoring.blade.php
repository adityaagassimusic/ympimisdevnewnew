@extends('layouts.display')
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
            background-color: #f9f9f9;
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
            border: 1px solid black;
            padding-top: 0;
            padding-bottom: 5px;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
            vertical-align: middle;
        }

        img {
            max-width: 100%
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
    </section>
@endsection
@section('content')
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-12" style="padding-bottom: 5px;">
                <a data-toggle="modal" data-target="#modalScan" class="btn btn-success pull-right" style="color:white;">
                    &nbsp;<i class="fa fa-camera"></i>&nbsp;&nbsp;&nbsp;Scan QR Check&nbsp;
                </a>
                <a data-toggle="modal" data-target="#modalMaterial" class="btn btn-primary pull-right"
                    style="color:white; margin-right: 10px;">
                    &nbsp;<i class="fa fa-list"></i>&nbsp;&nbsp;&nbsp;List Material&nbsp;
                </a>
            </div>
            <div class="col-xs-12">
                <div style="background-color: #605ca8; color: white; padding: 5px; text-align: center; margin-bottom: 8px;">
                    <span style="font-weight: bold; font-size: 20px;">DALAM PROSES</span>
                </div>
                <table id="tableProgress" class="table table-bordered table-hover">
                    <thead style="">
                        <tr>
                            <th style="width: 0.8%; text-align: center; background-color: #605ca8; color: white;">Tanggal
                                Kedatangan</th>
                            <th style="width: 0.5%; text-align: center; background-color: #605ca8; color: white;">Status
                            </th>
                            <th style="width: 3.5%; text-align: left; background-color: #605ca8; color: white;">Material
                            </th>
                            <th style="width: 0.5%; text-align: center; background-color: #605ca8; color: white;">Jumlah
                                Sample
                            </th>
                            <th style="width: 0.5%; text-align: center; background-color: #605ca8; color: white;">Total
                                Stock WH
                            </th>
                            <th style="width: 2%; text-align: left; background-color: #605ca8; color: white;">Lokasi
                                Cek<br>Dicek Oleh</th>
                            <th style="width: 1%; text-align: center; background-color: #dd4b39; color: white;">Reported
                                By<br>Foreman</th>
                            <th style="width: 1%; text-align: center; background-color: #dd4b39; color: white;">Approved
                                By<br>Manager</th>
                            <th style="width: 1%; text-align: center; background-color: #dd4b39; color: white;">Confirmed
                                By<br>Buyer</th>
                            <th style="width: 1%; text-align: center; background-color: #dd4b39; color: white;">Confirmed
                                By<br>Vendor</th>
                            <th style="width: 1%; text-align: center; background-color: #dd4b39; color: white;">Kedatangan
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableProgressBody">
                    </tbody>
                </table>
                <div style="background-color: #00a65a; color: white; padding: 5px; text-align: center; margin-bottom: 8px">
                    <span style="font-weight: bold; font-size: 20px">SELESAI</span>
                </div>
                <table id="tableFinish" class="table table-bordered table-hover">
                    <thead style="">
                        <tr>
                            <th style="width: 0.8%; text-align: center; background-color: #00a65a; color: white;">Tanggal
                                Kedatangan</th>
                            <th style="width: 0.5%; text-align: center; background-color: #00a65a; color: white;">Status
                            </th>
                            <th style="width: 3.5%; text-align: left; background-color: #00a65a; color: white;">Material
                            </th>
                            <th style="width: 0.1%; text-align: center; background-color: #00a65a; color: white;">Qty Sample
                            </th>
                            <th style="width: 2%; text-align: left; background-color: #00a65a; color: white;">Lokasi
                                Cek<br>Dicek Oleh</th>
                            <th style="width: 1%; text-align: center; background-color: #dd4b39; color: white;">Reported
                                By<br>Foreman</th>
                            <th style="width: 1%; text-align: center; background-color: #dd4b39; color: white;">Approved
                                By<br>Manager</th>
                            <th style="width: 1%; text-align: center; background-color: #dd4b39; color: white;">Confirmed
                                By<br>Buyer</th>
                            <th style="width: 1%; text-align: center; background-color: #dd4b39; color: white;">Confirmed
                                By<br>Vendor</th>
                            <th style="width: 1%; text-align: center; background-color: #dd4b39; color: white;">Kedatangan
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableFinishBody">
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
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalMaterial">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body table-responsive no-padding">
                        <div class="form-group">
                            <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Material<span
                                    class="text-red">*</span> :</label>
                            <div class="col-sm-9">
                                <select class="form-control select2" id="createMaterial" data-placeholder="Pilih Material"
                                    style="width: 100%;">
                                    <option value=""></option>
                                    @foreach ($material_controls as $material_control)
                                        @if ($material_control->incoming != 1)
                                            <option value="{{ $material_control->material_number }}">
                                                {{ $material_control->material_number }} -
                                                {{ $material_control->material_description }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12" style="margin-top: 10px; margin-bottom: 10px;">
                            <button class="btn btn-primary pull-right" onclick="createMaterial()">Tambahkan</button>
                        </div>
                        <table id="tableMaterial" class="table table-bordered table-striped table-hover"
                            style="margin-bottom: 0;">
                            <thead style="">
                                <tr>
                                    <th style="width: 10%;">Material</th>
                                    <th style="width: 1%; text-align: left;">Vendor</th>
                                    <th style="width: 1%; text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableMaterialBody">
                                @foreach ($material_controls as $material_control)
                                    @if ($material_control->incoming == 1)
                                        <tr>
                                            <td style="width: 10%; text-align: left;">
                                                {{ $material_control->material_number }}<br>{{ $material_control->material_description }}
                                            </td>
                                            <td style="width: 1%; text-align: left;">
                                                {{ $material_control->vendor_shortname }}</td>
                                            <td style="width: 1%; text-align: center;"><button class="btn btn-danger"
                                                    id="{{ $material_control->material_number }}"
                                                    onclick="editMaterial(id)"><i class="fa fa-trash"></i></button></td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalForeman" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #dd4b39; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            BUAT LAPORAN KETIDAKSESUAIAN<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <input type="hidden" name="foremanId" id="foremanId">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Kedatangan<span class="text-red"></span> :</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" placeholder=""
                                            id="foremanPostingDate" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Material<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" placeholder="" id="foremanMaterial"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Dicek
                                        Oleh<span class="text-red"></span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="" id="foremanCheckedBy"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah
                                        NG<span class="text-red"></span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="" id="foremanNgCount"
                                            style="color: red; font-weight: bold;" disabled>
                                    </div>
                                </div>
                                <table id="tableForemanDetail" class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: #dd4b39; color: white;">
                                        <tr>
                                            <th style="width: 0.1%; text-align: center;">#</th>
                                            <th style="width: 2.5%; text-align: left;">Detail NG</th>
                                            <th style="width: 0.1%; text-align: center;">Jumlah NG</th>
                                            <th style="width: 1%; text-align: center;">Bukti Foto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableForemanDetailBody">
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-12">Catatan Foreman<span
                                            class="text-red"></span> :</label>
                                    <div class="col-sm-12">
                                        <textarea class="form-control" rows="3" placeholder="Tuliskan Catatan" id="foremanReport"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">KEMBALI</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="createReport('Foreman')">KIRIM LAPORAN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBuyer" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #dd4b39; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            KONFIRMASI KETIDAKSESUAIAN<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <input type="hidden" name="buyerId" id="buyerId">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Kedatangan<span class="text-red"></span> :</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" placeholder="" id="buyerPostingDate"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Material<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" placeholder="" id="buyerMaterial"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Dicek
                                        Oleh<span class="text-red"></span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="" id="buyerCheckedBy"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah
                                        NG<span class="text-red"></span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="" id="buyerNgCount"
                                            style="color: red; font-weight: bold;" disabled>
                                    </div>
                                </div>
                                <table id="tableBuyerDetail" class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: #dd4b39; color: white;">
                                        <tr>
                                            <th style="width: 0.1%; text-align: center;">#</th>
                                            <th style="width: 2.5%; text-align: left;">Detail NG</th>
                                            <th style="width: 0.1%; text-align: center;">Jumlah NG</th>
                                            <th style="width: 1%; text-align: center;">Bukti Foto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBuyerDetailBody">
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-12">Catatan Foreman<span
                                            class="text-red"></span> :</label>
                                    <div class="col-sm-12">
                                        <div class="col-sm-12" id="buyerForeman" style="border: 1px dashed red;">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Unggah
                                        Invoice<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="file" id="buyerInvoice" style="color: red; font-weight: bold;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Unggah
                                        Evidence<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="file" id="buyerEvidence" style="color: red; font-weight: bold;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-12">Catatan Buyer<span
                                            class="text-red"></span> :</label>
                                    <div class="col-sm-12">
                                        <textarea class="form-control" rows="3" placeholder="Tuliskan Catatan" id="buyerReport"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">KEMBALI</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="createReport('Buyer')">KONFIRMASI KETIDAKSESUAIAN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVendor" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #dd4b39; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            KONFIRMASI KETIDAKSESUAIAN<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Kedatangan<span class="text-red"></span> :</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" placeholder="" id="vendorPostingDate"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Material<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" placeholder="" id="vendorMaterial"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Dicek
                                        Oleh<span class="text-red"></span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="" id="vendorCheckedBy"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah
                                        NG<span class="text-red"></span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="" id="vendorNgCount"
                                            style="color: red; font-weight: bold;" disabled>
                                    </div>
                                </div>
                                <table id="tableVendorDetail" class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: #dd4b39; color: white;">
                                        <tr>
                                            <th style="width: 0.1%; text-align: center;">#</th>
                                            <th style="width: 2.5%; text-align: left;">Detail NG</th>
                                            <th style="width: 0.1%; text-align: center;">Jumlah NG</th>
                                            <th style="width: 1%; text-align: center;">Bukti Foto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableVendorDetailBody">
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Unggah
                                        Invoice<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="file" id="vendorInvoice" style="color: red; font-weight: bold;">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Estimasi
                                        Kedatangan<span class="text-red"></span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="vendorETA" style="color: red; font-weight: bold;"
                                            class="form-control datepicker" placeholder="   Pilih Tanggal">
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">KEMBALI</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="createReport('Vendor')">KONFIRMASI KEDATANGAN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalArrived" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #dd4b39; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            KONFIRMASI KETIDAKSESUAIAN<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Kedatangan<span class="text-red"></span> :</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" placeholder=""
                                            id="arrivedPostingDate" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Material<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" placeholder="" id="arrivedMaterial"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Dicek
                                        Oleh<span class="text-red"></span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="" id="arrivedCheckedBy"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah
                                        NG<span class="text-red"></span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="" id="arrivedNgCount"
                                            style="color: red; font-weight: bold;" disabled>
                                    </div>
                                </div>
                                <table id="tableArrivedDetail" class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: #dd4b39; color: white;">
                                        <tr>
                                            <th style="width: 0.1%; text-align: center;">#</th>
                                            <th style="width: 2.5%; text-align: left;">Detail NG</th>
                                            <th style="width: 0.1%; text-align: center;">Jumlah NG</th>
                                            <th style="width: 1%; text-align: center;">Bukti Foto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableArrivedDetailBody">
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tanggal
                                        Kedatangan<span class="text-red"></span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="arrivedDate" style="color: red; font-weight: bold;"
                                            class="form-control datepicker" placeholder="   Pilih Tanggal">
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">KEMBALI</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="createReport('Arrived')">KONFIRMASI KEDATANGAN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #dd4b39; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            KONFIRMASI KETIDAKSESUAIAN<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Kedatangan<span class="text-red"></span> :</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control" placeholder="" id="detailPostingDate"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Material<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" placeholder="" id="detailMaterial"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Dicek
                                        Oleh<span class="text-red"></span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="" id="detailCheckedBy"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah
                                        NG<span class="text-red"></span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="" id="detailNgCount"
                                            style="color: red; font-weight: bold;" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">ETA<span
                                            class="text-red"></span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="" id="detailETA"
                                            style="" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tanggal
                                        Datang<span class="text-red"></span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="" id="detailArrived"
                                            style="" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Evidence
                                        Info<span class="text-red"></span> :</label>
                                    <div class="col-sm-3" id="detailNgEvidence">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Invoice
                                        NG<span class="text-red"></span> :</label>
                                    <div class="col-sm-3" id="detailNgInvoice">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Invoice
                                        OK<span class="text-red"></span> :</label>
                                    <div class="col-sm-3" id="detailOkInvoice">
                                    </div>
                                </div>
                                <table id="tableDetail" class="table table-bordered table-striped table-hover">
                                    <thead style="background-color: #dd4b39; color: white;">
                                        <tr>
                                            <th style="width: 0.1%; text-align: center;">#</th>
                                            <th style="width: 2.5%; text-align: left;">Detail NG</th>
                                            <th style="width: 0.1%; text-align: center;">Jumlah NG</th>
                                            <th style="width: 1%; text-align: center;">Bukti Foto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableDetailBody">
                                    </tbody>
                                </table>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-12">Catatan Foreman<span
                                            class="text-red"></span> :</label>
                                    <div class="col-sm-12">
                                        <div class="col-sm-12" id="detailForeman" style="border: 1px dashed red;">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-12">Catatan Buyer<span
                                            class="text-red"></span> :</label>
                                    <div class="col-sm-12">
                                        <div class="col-sm-12" id="detailBuyer" style="border: 1px dashed red;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalScan">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #605ca8; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Scan QR Material<br>
                        </h3>
                    </center>
                    <div class="modal-body" style="padding-bottom: 5px;">
                        <div class="form-group">
                            <input style="width: 100%; text-align: center;" type="text" class="form-control"
                                placeholder="" id="inout_no" style="">
                        </div>
                        <div id='scanner' class="col-xs-12">
                            <div class="col-xs-12">
                                <center>
                                    <div id="loadingMessage">
                                        🎥 Unable to access video stream
                                        (please make sure you have a webcam enabled)
                                    </div>
                                    <video autoplay muted playsinline id="video"></video>
                                    <div id="output" hidden>
                                        <div id="outputMessage">No QR code detected.</div>
                                    </div>
                                </center>
                            </div>
                        </div>
                        <div class="materialCheck" style="width:100%; padding-left: 2%; padding-right: 2%;">
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
    <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
    <script src="{{ url('js/jsQR.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            fetchData();
            $('#vendorETA').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            $('#arrivedDate').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var material_checks = [];
        var material_check_logs = [];
        var material_check_details = [];
        var material_check_findings = [];
        var inventories = [];
        var stock_policies = [];
        var video;

        $(function() {
            $('#createMaterial').select2({
                dropdownParent: $('#modalMaterial')
            });
        });

        $("#modalMaterial").on('shown.bs.modal', function() {
            $('#createMaterial').prop('selectedIndex', 0).change();
        });

        $("#modalScan").on('shown.bs.modal', function() {
            showCheck('123');
            $('#inout_no').val("");
            $('#inout_no').focus();
        });

        $('#modalScan').on('hidden.bs.modal', function() {
            videoOff();
            $('.materialCheck').html("");
        });

        function createMaterial() {
            if (confirm("Apakah anda yakin akan menambahkan material ke daftar pengecekan?")) {
                var material_number = $('#createMaterial').val();

                if (material_number == "") {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error', 'Pilih material terlebih dahulu');
                    return false;
                }

                var data = {
                    material_number: material_number,
                    remark: 'add'
                }

                $.post('{{ url('edit/material/material_control') }}', data, function(result, status, xhr) {
                    if (result.status) {

                        var tableMaterialBody = "";
                        tableMaterialBody += '<tr>';
                        tableMaterialBody += '<td style="width: 10%; text-align: left;">' + result.material_control
                            .material_number +
                            '<br>' + result.material_control.material_description + '</td>';
                        if (result.material_control.vendor_shortname) {
                            tableMaterialBody += '<td style="width: 1%; text-align: left;">' + result
                                .material_control
                                .vendor_shortname +
                                '</td>';
                        } else {
                            tableMaterialBody += '<td style="width: 1%; text-align: left;"></td>';
                        }
                        tableMaterialBody +=
                            '<td style="width: 1%; text-align: center;"><button class="btn btn-danger" onclick="editMaterial(id)" id="' +
                            result.material_control.material_number +
                            '"><i class = "fa fa-trash" > </i></button></td>';
                        tableMaterialBody += '</tr>';

                        $('#tableMaterialBody').prepend(tableMaterialBody);
                        $("#createMaterial option[value='" + result.material_control.material_number + "']")
                            .remove();

                        $('#loading').hide();
                        openSuccessGritter('Success!', result.message);
                        audio_ok.play();
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error!', result.message);
                        audio_error.play();
                    }
                });
            } else {
                return false;
            }
        }

        function editMaterial(material_number) {
            if (confirm("Apakah anda yakin akan mengeluarkan material dari daftar pengecekan?")) {
                var data = {
                    material_number: material_number,
                    remark: 'rem'
                }

                $.post('{{ url('edit/material/material_control') }}', data, function(result, status, xhr) {
                    if (result.status) {

                        $('#' + result.material_control.material_number).closest("tr").remove();

                        $('#loading').hide();
                        openSuccessGritter('Success!', result.message);
                        audio_ok.play();
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error!', result.message);
                        audio_error.play();
                    }
                });
            } else {
                return false;
            }
        }

        function stopScan() {
            $('#modalScan').modal('hide');
        }

        function videoOff() {
            video.pause();
            video.src = "";
            // video.srcObject.getTracks()[0].stop();
        }

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
            video.style.width = "300px";
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
                loadingMessage.innerText = "⌛ Loading video...";

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
                        outputMessage.hidden = true;
                        videoOff();
                        materialCheck(video, code.data);
                    } else {
                        outputMessage.hidden = false;
                    }
                } catch (t) {
                    console.log("PROBLEM: " + t);
                    return false;
                }

                setTimeout(function() {
                    tick();
                }, tickDuration);
            }
        }

        function materialCheck(video, data) {
            var inout_no = data;
            if (inout_no.length == 15) {
                check(inout_no);
            }
        }

        $('#inout_no').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                var inout_no = $('#inout_no').val();
                if (inout_no.length == 15) {
                    check(inout_no);
                }
            }
        });

        function check(inout_no) {
            $('.materialCheck').html("");
            var materialCheck = "";
            var status = true;

            $.each(material_checks, function(key, value) {
                if (value.inout_no == inout_no) {
                    if (value.status != 'Waiting') {
                        status = false;
                    }
                    materialCheck += '<center>';
                    materialCheck +=
                        '<span style="text-decoration: underline; font-size: 1.8vw;">INDIRECT MATERIAL CHECK</span><br><br>';
                    materialCheck += '<span style="font-size: 1.5vw; font-weight: bold;">' + value.material_number +
                        '</span><br>';
                    materialCheck += '<span style="font-size: 1.5vw; font-weight: bold;">' + value
                        .material_description + '</span><br><br>';
                    materialCheck +=
                        '<span style="font-size: 1.3vw;">Sample Qty: </span><span style="font-size: 1.3vw; font-weight: bold;">' +
                        value.sample_qty + ' ' + value.uom + '</span><br>';
                    materialCheck +=
                        '<span style="font-size: 1.3vw;">Lokasi Cek: </span><span style="font-size: 1.3vw; font-weight: bold;">' +
                        value.location + '</span><br>';
                    materialCheck +=
                        '<span style="font-size: 1.3vw;">Kedatangan: </span><span style="font-size: 1.3vw; font-weight: bold;">' +
                        value.posting_date + '</span><br><br>';
                    materialCheck +=
                        '<button class="btn btn-success btn-lg" style="width: 100%; font-weight: bold; font-size: 1.5vw;" onclick="checkProcess(\'' +
                        value.inout_no + '\')">Lakukan Pengecekan</button>';
                    materialCheck += '</center>';
                }
            });
            if (status) {
                $('.materialCheck').append(materialCheck);
                $('#loading').hide();
            } else {
                $('.materialCheck').html("");
                var materialCheck = "";
                $('#loading').hide();
                openErrorGritter('Gagal!', 'QRCode tidak ditemukan');
                audio_error.play();
                return false;
            }
        }

        function checkProcess(inout_no) {
            window.open('{{ url('index/material/check') }}' + '?inout_no=' + inout_no, '_self');
        }

        function fetchData() {
            var data = {

            }

            $.get('{{ url('fetch/material/check') }}', data, function(result, status, xhr) {
                if (result.status) {
                    material_checks = result.material_checks;
                    material_check_logs = result.material_check_logs;
                    material_check_details = result.material_check_details;
                    material_check_findings = result.material_check_findings;
                    stock_policies = result.stock_policies;
                    inventories = result.inventories;

                    $('#tableProgressBody').html("");
                    $('#tableFinishBody').html("");
                    // $('#tableFinish').DataTable().clear();
                    // $('#tableFinish').DataTable().destroy();

                    for (var i = 0; i < material_checks.length; i++) {
                        var tableBody = "";
                        tableBody += '<tr>';
                        if (material_checks[i].status == 'OK' || (material_checks[i].status == 'NG' &&
                                material_checks[i].ng_status == 'Tiba di YMPI')) {
                            tableBody += '<td style="widht: 0.8%; text-align: center;">' + material_checks[i]
                                .posting_date + '</span></td>';
                        } else {
                            tableBody += '<td style="widht: 0.8%; text-align: center;">' + material_checks[i]
                                .posting_date + '<br><span style="color: red;">(Overdue: ' + material_checks[i]
                                .overdue + ' Days)</span></td>';
                        }
                        var color = "";
                        if (material_checks[i].status == 'Waiting') {
                            color = "background-color: orange;";
                        }
                        if (material_checks[i].status == 'Checking') {
                            color = "background-color: yellow;";
                        }
                        if (material_checks[i].status == 'OK') {
                            color = "color: white; background-color: #00a65a;";
                        }
                        if (material_checks[i].status == 'NG') {
                            color = "color: white; background-color: #dd4b39;";
                        }
                        tableBody +=
                            '<td style="widht: 0.5%; text-align: center; font-size: 16px; font-weight: bold; ' +
                            color + '">' + material_checks[i].status + '</td>';
                        tableBody +=
                            '<td style="widht: 4%; text-align: left;"><span style="font-weight: bold; color: #00a65a;"><a href="javascript:void(0)" onclick=modalReport(\'Detail\',\'' +
                            material_checks[i].inout_no + '\')>' + material_checks[i].material_number + '</a> (' +
                            material_checks[i].vendor_name + ')</span><br>' + material_checks[i]
                            .material_description + '</td>';
                        tableBody += '<td style="widht: 0.5%; text-align: center;"><b>' + material_checks[i]
                            .sample_qty + '</b> ' + material_checks[i].uom + '(s)</td>';
                        var emergency = "";
                        var bg = "";
                        var found = 0;
                        $.each(inventories, function(key, value) {
                            if (value.item_code == material_checks[i].material_number) {
                                found = 1;
                                for (var k = 0; k < stock_policies.length; k++) {
                                    if (stock_policies[k].material_number == material_checks[i]
                                        .material_number && stock_policies[k].period == material_checks[i]
                                        .period && stock_policies[k].policy >= parseInt(value
                                            .quantity_stock)) {
                                        emergency =
                                            '<span style="font-weight: bold;">Emergency (Segera Cek)</span>';
                                        bg = 'background-color: #dd4b39; color: white;'
                                    }
                                }
                                tableBody += '<td style="widht: 0.5%; text-align: center; ' + bg + '"><b>' +
                                    parseInt(
                                        value
                                        .quantity_stock) + '</b> ' +
                                    material_checks[i].uom + '(s)<br>' + emergency + '</td>';
                            }
                        });
                        if (found == 0) {
                            tableBody += '<td style="widht: 0.5%; text-align: center; ' + bg + '"><b>0</b> ' +
                                material_checks[i].uom + '(s)</td>';
                        }
                        var checked_by = '<span style="color: red;">(Belum dilakukan cek)</span>';
                        if (material_checks[i].checked_by) {
                            checked_by = '<span style="color: green;">(' + material_checks[i].checked_by + ' - ' +
                                material_checks[i].checked_by_name + ')<br>(' + material_checks[i].checked_at +
                                ')</span>';
                        }
                        tableBody += '<td style="widht: 1.5%; text-align: left;">' + material_checks[i].location +
                            '</span><br>' + checked_by + '</td>';
                        if (jQuery.inArray(material_checks[i].status, ['Waiting', 'Checking', 'OK']) !== -1) {
                            tableBody +=
                                '<td style="widht: 1%; font-size: 1.2vw; text-align: center; color: white; background-image: linear-gradient(to right, #383838, #707070); font-weight: bold;"></td>';
                            tableBody +=
                                '<td style="widht: 1%; font-size: 1.2vw; text-align: center; color: white; background-image: linear-gradient(to right, #383838, #707070); font-weight: bold;"></td>';
                            tableBody +=
                                '<td style="widht: 1%; font-size: 1.2vw; text-align: center; color: white; background-image: linear-gradient(to right, #383838, #707070); font-weight: bold;"></td>';
                            tableBody +=
                                '<td style="widht: 1%; font-size: 1.2vw; text-align: center; color: white; background-image: linear-gradient(to right, #383838, #707070); font-weight: bold;"></td>';
                            tableBody +=
                                '<td style="widht: 1%; font-size: 1.2vw; text-align: center; color: white; background-image: linear-gradient(to right, #383838, #707070); font-weight: bold;"></td>';
                        } else {
                            var foreman = false;
                            var manager = false;
                            var buyer = false;
                            for (var j = 0; j < material_check_details.length; j++) {
                                if (material_check_details[j].inout_no == material_checks[i].inout_no) {
                                    if (material_check_details[j].confirmed_at) {
                                        tableBody +=
                                            '<td style="widht: 1%; text-align: center; color: green; font-weight: bold;">' +
                                            material_check_details[j].employee_name +
                                            '<br><span class="label label-success">' + material_check_details[j]
                                            .status + '</span><br>' + material_check_details[j].confirmed_at +
                                            '</td>';
                                        if (material_check_details[j].position == 'Foreman') {
                                            foreman = true;
                                        }
                                        if (material_check_details[j].position == 'Manager') {
                                            manager = true;
                                        }
                                        if (material_check_details[j].position == 'Buyer') {
                                            buyer = true;
                                        }
                                    } else {
                                        if (material_check_details[j].position == 'Foreman') {
                                            tableBody +=
                                                '<td style="widht: 1%; text-align: center;"><span style="font-weight: bold; color: red;">By ' +
                                                material_check_details[j].employee_name +
                                                '</span><br><button class="btn btn-danger btn-xs" id="foreman_' +
                                                material_checks[i].inout_no +
                                                '" onclick="modalReport(\'Foreman\',\'' + material_checks[i]
                                                .inout_no +
                                                '\')">Buat Laporan <i class="fa fa-pencil"></i></button></td>';
                                        }
                                        if (material_check_details[j].position == 'Manager') {
                                            if (foreman == true) {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center;"><span style="font-weight: bold; color: red;">' +
                                                    material_check_details[j].employee_name +
                                                    '</span><br><button class="btn btn-danger btn-xs" id="foreman_' +
                                                    material_checks[i].inout_no +
                                                    '" onclick="modalReport(\'Manager\',\'' + material_checks[i]
                                                    .inout_no +
                                                    '\')">Menyetujui <i class="fa fa-pencil"></i></button></td>';
                                            } else {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center; color: #ffa500; font-weight: bold;">' +
                                                    material_check_details[j].employee_name + '<br>(' +
                                                    material_check_details[j].status + ')</td>';
                                            }
                                        }
                                        if (material_check_details[j].position == 'Buyer') {
                                            if (manager == true) {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center;"><span style="font-weight: bold; color: red;">' +
                                                    material_check_details[j].employee_name +
                                                    '</span><br><button class="btn btn-danger btn-xs" id="buyer_' +
                                                    material_checks[i].inout_no +
                                                    '" onclick="modalReport(\'Buyer\',\'' + material_checks[i]
                                                    .inout_no +
                                                    '\')">Mengkonfirmasi <i class="fa fa-pencil"></i></button></td>';
                                            } else {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center; color: #ffa500; font-weight: bold;">' +
                                                    material_check_details[j].employee_name + '<br>(' +
                                                    material_check_details[j].status + ')</td>';
                                            }
                                        }
                                        if (material_check_details[j].position == 'Vendor') {
                                            if (buyer == true) {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center;"><span style="font-weight: bold; color: red;">' +
                                                    material_check_details[j].employee_name +
                                                    '</span><br><button class="btn btn-danger btn-xs" id="buyer_' +
                                                    material_checks[i].inout_no +
                                                    '" onclick="modalReport(\'Vendor\',\'' + material_checks[i]
                                                    .inout_no +
                                                    '\')">Kedatangan <i class="fa fa-pencil"></i></button></td>';
                                            } else {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center; color: #ffa500; font-weight: bold;">' +
                                                    material_check_details[j].employee_name + '<br>(' +
                                                    material_check_details[j].status + ')</td>';
                                            }
                                        }
                                    }
                                }
                            }
                            if (material_checks[i].ng_status == 'Perjalanan ke YMPI') {
                                tableBody +=
                                    '<td style="widht: 1%; text-align: center; font-weight: bold; color: #ffa500;">ETA: ' +
                                    material_checks[i].eta_date +
                                    '<br><a href="javascript:void(0)" onclick=modalReport(\'Arrived\',\'' +
                                    material_checks[i].inout_no + '\')>(' + material_checks[i].ng_status +
                                    ')</a></td>';
                            } else if (material_checks[i].ng_status == 'Tiba di YMPI') {
                                tableBody +=
                                    '<td style="widht: 1%; text-align: center; font-weight: bold; color: #00a65a;">Arrived: ' +
                                    material_checks[i].eta_date + '<br>(' + material_checks[i].ng_status + ')</td>';
                            } else {
                                tableBody +=
                                    '<td style="widht: 1%; text-align: center; font-weight: bold; color: red;">ETA: Unknown<br>(' +
                                    material_checks[i].ng_status + ')</td>';
                            }
                        }
                        tableBody += '</tr>';
                        if (material_checks[i].status == 'OK' || (material_checks[i].status == 'NG' &&
                                material_checks[i].ng_status == 'Tiba di YMPI')) {
                            $('#tableFinishBody').append(tableBody);
                        } else {
                            $('#tableProgressBody').append(tableBody);
                        }
                    }

                    for (var i = 0; i < material_check_logs.length; i++) {
                        var tableBody = "";
                        tableBody += '<tr>';
                        if (material_check_logs[i].status == 'OK' || (material_check_logs[i].status == 'NG' &&
                                material_check_logs[i].ng_status == 'Tiba di YMPI')) {
                            tableBody += '<td style="widht: 0.8%; text-align: center;">' + material_check_logs[i]
                                .posting_date + '</span></td>';
                        } else {
                            tableBody += '<td style="widht: 0.8%; text-align: center;">' + material_check_logs[i]
                                .posting_date + '<br><span style="color: red;">(Overdue: ' + material_check_logs[i]
                                .overdue + ' Days)</span></td>';
                        }
                        var color = "";
                        if (material_check_logs[i].status == 'Waiting') {
                            color = "background-color: orange;";
                        }
                        if (material_check_logs[i].status == 'Checking') {
                            color = "background-color: yellow;";
                        }
                        if (material_check_logs[i].status == 'OK') {
                            color = "color: white; background-color: #00a65a;";
                        }
                        if (material_check_logs[i].status == 'NG') {
                            color = "color: white; background-color: #dd4b39;";
                        }
                        tableBody +=
                            '<td style="widht: 0.5%; text-align: center; font-size: 16px; font-weight: bold; ' +
                            color + '">' + material_check_logs[i].status + '</td>';
                        tableBody +=
                            '<td style="widht: 4%; text-align: left;"><span style="font-weight: bold; color: #00a65a;"><a href="javascript:void(0)" onclick=modalReport(\'Detail\',\'' +
                            material_check_logs[i].inout_no + '\')>' + material_check_logs[i].material_number +
                            '</a> (' +
                            material_check_logs[i].vendor_name + ')</span><br>' + material_check_logs[i]
                            .material_description + '</td>';
                        tableBody += '<td style="widht: 0.1%; text-align: center;"><b>' + material_check_logs[i]
                            .sample_qty + '</b><br>' + material_check_logs[i].uom + '(s)</td>';
                        var checked_by = '<span style="color: red;">(Belum dilakukan cek)</span>';
                        if (material_check_logs[i].checked_by) {
                            checked_by = '<span style="color: green;">(' + material_check_logs[i].checked_by +
                                ' - ' +
                                material_check_logs[i].checked_by_name + ')<br>(' + material_check_logs[i]
                                .checked_at +
                                ')</span>';
                        }
                        tableBody += '<td style="widht: 1.5%; text-align: left;">' + material_check_logs[i]
                            .location +
                            '</span><br>' + checked_by + '</td>';
                        if (jQuery.inArray(material_check_logs[i].status, ['Waiting', 'Checking', 'OK']) !== -1) {
                            tableBody +=
                                '<td style="widht: 1%; font-size: 1.2vw; text-align: center; color: white; background-image: linear-gradient(to right, #383838, #707070); font-weight: bold;"></td>';
                            tableBody +=
                                '<td style="widht: 1%; font-size: 1.2vw; text-align: center; color: white; background-image: linear-gradient(to right, #383838, #707070); font-weight: bold;"></td>';
                            tableBody +=
                                '<td style="widht: 1%; font-size: 1.2vw; text-align: center; color: white; background-image: linear-gradient(to right, #383838, #707070); font-weight: bold;"></td>';
                            tableBody +=
                                '<td style="widht: 1%; font-size: 1.2vw; text-align: center; color: white; background-image: linear-gradient(to right, #383838, #707070); font-weight: bold;"></td>';
                            tableBody +=
                                '<td style="widht: 1%; font-size: 1.2vw; text-align: center; color: white; background-image: linear-gradient(to right, #383838, #707070); font-weight: bold;"></td>';
                        } else {
                            var foreman = false;
                            var manager = false;
                            var buyer = false;
                            for (var j = 0; j < material_check_details.length; j++) {
                                if (material_check_details[j].inout_no == material_check_logs[i].inout_no) {
                                    if (material_check_details[j].confirmed_at) {
                                        tableBody +=
                                            '<td style="widht: 1%; text-align: center; color: green; font-weight: bold;">' +
                                            material_check_details[j].employee_name +
                                            '<br><span class="label label-success">' + material_check_details[j]
                                            .status + '</span><br>' + material_check_details[j].confirmed_at +
                                            '</td>';
                                        if (material_check_details[j].position == 'Foreman') {
                                            foreman = true;
                                        }
                                        if (material_check_details[j].position == 'Manager') {
                                            manager = true;
                                        }
                                        if (material_check_details[j].position == 'Buyer') {
                                            buyer = true;
                                        }
                                    } else {
                                        if (material_check_details[j].position == 'Foreman') {
                                            tableBody +=
                                                '<td style="widht: 1%; text-align: center;"><span style="font-weight: bold; color: red;">By ' +
                                                material_check_details[j].employee_name +
                                                '</span><br><button class="btn btn-danger btn-xs" id="foreman_' +
                                                material_check_logs[i].inout_no +
                                                '" onclick="modalReport(\'Foreman\',\'' + material_check_logs[i]
                                                .inout_no +
                                                '\')">Buat Laporan <i class="fa fa-pencil"></i></button></td>';
                                        }
                                        if (material_check_details[j].position == 'Manager') {
                                            if (foreman == true) {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center;"><span style="font-weight: bold; color: red;">' +
                                                    material_check_details[j].employee_name +
                                                    '</span><br><button class="btn btn-danger btn-xs" id="foreman_' +
                                                    material_check_logs[i].inout_no +
                                                    '" onclick="modalReport(\'Manager\',\'' + material_check_logs[i]
                                                    .inout_no +
                                                    '\')">Menyetujui <i class="fa fa-pencil"></i></button></td>';
                                            } else {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center; color: #ffa500; font-weight: bold;">' +
                                                    material_check_details[j].employee_name + '<br>(' +
                                                    material_check_details[j].status + ')</td>';
                                            }
                                        }
                                        if (material_check_details[j].position == 'Buyer') {
                                            if (manager == true) {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center;"><span style="font-weight: bold; color: red;">' +
                                                    material_check_details[j].employee_name +
                                                    '</span><br><button class="btn btn-danger btn-xs" id="buyer_' +
                                                    material_check_logs[i].inout_no +
                                                    '" onclick="modalReport(\'Buyer\',\'' + material_check_logs[i]
                                                    .inout_no +
                                                    '\')">Mengkonfirmasi <i class="fa fa-pencil"></i></button></td>';
                                            } else {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center; color: #ffa500; font-weight: bold;">' +
                                                    material_check_details[j].employee_name + '<br>(' +
                                                    material_check_details[j].status + ')</td>';
                                            }
                                        }
                                        if (material_check_details[j].position == 'Vendor') {
                                            if (buyer == true) {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center;"><span style="font-weight: bold; color: red;">' +
                                                    material_check_details[j].employee_name +
                                                    '</span><br><button class="btn btn-danger btn-xs" id="buyer_' +
                                                    material_check_logs[i].inout_no +
                                                    '" onclick="modalReport(\'Vendor\',\'' + material_check_logs[i]
                                                    .inout_no +
                                                    '\')">Kedatangan <i class="fa fa-pencil"></i></button></td>';
                                            } else {
                                                tableBody +=
                                                    '<td style="widht: 1%; text-align: center; color: #ffa500; font-weight: bold;">' +
                                                    material_check_details[j].employee_name + '<br>(' +
                                                    material_check_details[j].status + ')</td>';
                                            }
                                        }
                                    }
                                }
                            }
                            if (material_check_logs[i].ng_status == 'Perjalanan ke YMPI') {
                                tableBody +=
                                    '<td style="widht: 1%; text-align: center; font-weight: bold; color: #ffa500;">ETA: ' +
                                    material_check_logs[i].eta_date +
                                    '<br><a href="javascript:void(0)" onclick=modalReport(\'Arrived\',\'' +
                                    material_check_logs[i].inout_no + '\')>(' + material_check_logs[i].ng_status +
                                    ')</a></td>';
                            } else if (material_check_logs[i].ng_status == 'Tiba di YMPI') {
                                tableBody +=
                                    '<td style="widht: 1%; text-align: center; font-weight: bold; color: #00a65a;">Arrived: ' +
                                    material_check_logs[i].eta_date + '<br>(' + material_check_logs[i].ng_status +
                                    ')</td>';
                            } else {
                                tableBody +=
                                    '<td style="widht: 1%; text-align: center; font-weight: bold; color: red;">ETA: Unknown<br>(' +
                                    material_check_logs[i].ng_status + ')</td>';
                            }
                        }
                        tableBody += '</tr>';
                        if (material_check_logs[i].status == 'OK' || (material_check_logs[i].status == 'NG' &&
                                material_check_logs[i].ng_status == 'Tiba di YMPI')) {
                            $('#tableFinishBody').append(tableBody);
                        } else {
                            $('#tableProgressBody').append(tableBody);
                        }
                    }

                    $('#tableFinish tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search ' +
                            title + '" size="10"/>');
                    });

                    var table = $('#tableFinish').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [10, 25, -1],
                            ['10 rows', '25 rows', 'Show all']
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
                        "searching": true,
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        'ordering': true,
                    });

                    table.columns().every(function() {
                        var that = this;
                        $('#search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('#tableFinish tfoot tr').appendTo('#tableFinish thead');

                } else {
                    alert('Gagal memuat data.');
                }
            });
        }

        function createReport(category) {
            if (confirm("Apakah anda yakin akan mengirim laporan?")) {
                $('#loading').show();
                if (category == 'Foreman') {
                    var inout_no = $('#foremanId').val();
                    var report = CKEDITOR.instances.foremanReport.getData();
                    var position = category;
                    var status = 'Laporan Terkirim';

                    var formData = new FormData();
                    formData.append('inout_no', inout_no);
                    formData.append('report', report);
                    formData.append('position', position);
                    formData.append('status', status);

                    $.ajax({
                        url: "{{ url('input/material/check_report') }}",
                        method: "POST",
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(data) {
                            if (data.status) {
                                $('#modalForeman').modal('hide');
                                fetchData();
                                $('#loading').hide();
                                openSuccessGritter('Berhasil!', data.message);
                                audio_ok.play();
                            } else {
                                $('#loading').hide();
                                openErrorGritter('Gagal!', data.message);
                                audio_error.play();
                            }

                        }
                    });
                }
                if (category == 'Buyer') {
                    var inout_no = $('#foremanId').val();
                    var status = 'Terkirim ke Vendor';
                    var position = category;
                    var report = CKEDITOR.instances.buyerReport.getData();

                    if ($('#buyerInvoice').prop('files').length == 0 || $('#buyerEvidence').prop('files').length == 0) {
                        $('#loading').hide();
                        audio_error.play();
                        openErrorGritter('Error!', 'Masukkan evidence terlebih dahulu');
                        return false;
                    }

                    var formData = new FormData();
                    formData.append('inout_no', inout_no);
                    formData.append('report', report);
                    formData.append('invoice', $('#buyerInvoice').prop('files')[0]);
                    var file = $('#buyerInvoice').val().replace(/C:\\fakepath\\/i, '').split(".");
                    formData.append('invoice_extension', file[1]);
                    formData.append('invoice_file_name', file[0]);
                    formData.append('evidence', $('#buyerEvidence').prop('files')[0]);
                    var file = $('#buyerEvidence').val().replace(/C:\\fakepath\\/i, '').split(".");
                    formData.append('evidence_extension', file[1]);
                    formData.append('evidence_file_name', file[0]);
                    formData.append('position', position);
                    formData.append('status', status);

                    $.ajax({
                        url: "{{ url('input/material/check_report') }}",
                        method: "POST",
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(data) {
                            if (data.status) {
                                $('#modalBuyer').modal('hide');
                                fetchData();
                                $('#loading').hide();
                                openSuccessGritter('Berhasil!', data.message);
                                audio_ok.play();
                            } else {
                                $('#loading').hide();
                                openErrorGritter('Gagal!', data.message);
                                audio_error.play();
                            }
                        }
                    });
                }
                if (category == 'Vendor') {
                    var inout_no = $('#foremanId').val();
                    var status = 'Perjalanan ke YMPI';
                    var position = category;
                    var eta = $('#vendorETA').val();

                    var formData = new FormData();
                    formData.append('inout_no', inout_no);
                    formData.append('eta', eta);
                    formData.append('invoice', $('#vendorInvoice').prop('files')[0]);
                    var file = $('#vendorInvoice').val().replace(/C:\\fakepath\\/i, '').split(".");
                    formData.append('invoice_extension', file[1]);
                    formData.append('invoice_file_name', file[0]);
                    formData.append('position', position);
                    formData.append('status', status);

                    $.ajax({
                        url: "{{ url('input/material/check_report') }}",
                        method: "POST",
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(data) {
                            if (data.status) {
                                $('#modalVendor').modal('hide');
                                fetchData();
                                $('#loading').hide();
                                openSuccessGritter('Berhasil!', data.message);
                                audio_ok.play();
                            } else {
                                $('#loading').hide();
                                openErrorGritter('Gagal!', data.message);
                                audio_error.play();
                            }
                        }
                    });
                }
                if (category == 'Arrived') {
                    var inout_no = $('#foremanId').val();
                    var status = 'Tiba di YMPI';
                    var position = category;
                    var arrived_date = $('#arrivedDate').val();

                    var formData = new FormData();
                    formData.append('inout_no', inout_no);
                    formData.append('arrived_date', arrived_date);
                    formData.append('position', position);
                    formData.append('status', status);

                    $.ajax({
                        url: "{{ url('input/material/check_report') }}",
                        method: "POST",
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(data) {
                            if (data.status) {
                                $('#modalArrived').modal('hide');
                                fetchData();
                                $('#loading').hide();
                                openSuccessGritter('Berhasil!', data.message);
                                audio_ok.play();
                            } else {
                                $('#loading').hide();
                                openErrorGritter('Gagal!', data.message);
                                audio_error.play();
                            }
                        }
                    });
                }
            } else {
                return false;
            }
        }

        function modalReport(category, inout_no) {
            $('#foremanId').val(inout_no);
            $.each(material_checks, function(key, value) {
                if (value.inout_no == inout_no) {
                    $('#foremanPostingDate').val(value.posting_date);
                    $('#foremanMaterial').val(value.material_number + ' - ' + value.material_description);
                    $('#foremanCheckedBy').val(value.checked_by + ' - ' + value.checked_by_name + ' (' + value
                        .location + ')');
                    $('#foremanNgCount').val(value.ng_count + ' ' + value.uom + ' (' + ((value.ng_count / value
                        .sample_qty) * 100).toFixed(1) + '%)');

                    $('#buyerPostingDate').val(value.posting_date);
                    $('#buyerMaterial').val(value.material_number + ' - ' + value.material_description);
                    $('#buyerCheckedBy').val(value.checked_by + ' - ' + value.checked_by_name + ' (' + value
                        .location + ')');
                    $('#buyerNgCount').val(value.ng_count + ' ' + value.uom + '(s) (' + ((value.ng_count / value
                        .sample_qty) * 100).toFixed(1) + '%)');

                    $('#vendorPostingDate').val(value.posting_date);
                    $('#vendorMaterial').val(value.material_number + ' - ' + value.material_description);
                    $('#vendorCheckedBy').val(value.checked_by + ' - ' + value.checked_by_name + ' (' + value
                        .location + ')');
                    $('#vendorNgCount').val(value.ng_count + ' ' + value.uom + '(s) (' + ((value.ng_count / value
                        .sample_qty) * 100).toFixed(1) + '%)');

                    $('#arrivedPostingDate').val(value.posting_date);
                    $('#arrivedMaterial').val(value.material_number + ' - ' + value.material_description);
                    $('#arrivedCheckedBy').val(value.checked_by + ' - ' + value.checked_by_name + ' (' + value
                        .location + ')');
                    $('#arrivedNgCount').val(value.ng_count + ' ' + value.uom + '(s) (' + ((value.ng_count / value
                        .sample_qty) * 100).toFixed(1) + '%)');

                    $('#detailPostingDate').val(value.posting_date);
                    $('#detailMaterial').val(value.material_number + ' - ' + value.material_description);
                    $('#detailCheckedBy').val(value.checked_by + ' - ' + value.checked_by_name + ' (' + value
                        .location + ')');
                    if (value.status == 'OK') {
                        $('#detailNgCount').val('(0%)');
                    } else {
                        $('#detailNgCount').val(value.ng_count + ' ' + value.uom + '(s) (' + ((value.ng_count /
                            value.sample_qty) * 100).toFixed(1) + '%)');
                    }
                    $('#detailETA').val(value.eta_date);
                    $('#detailArrived').val(value.arrived_date);
                    $('#detailNgEvidence').html("");
                    $('#detailNgInvoice').html("");
                    $('#detailOkInvoice').html("");
                    var url = "{{ url('files/material_check') }}";
                    var detailNgEvidence = '<a style="width: 180px; padding-top: 5px;" href="' + url + '/' + value
                        .ng_evidence_file + '">' + value.ng_invoice_file + '</a>';
                    var detailNgInvoice = '<a style="width: 180px; padding-top: 5px;" href="' + url + '/' + value
                        .ng_invoice_file + '">' + value.ng_invoice_file + '</a>';
                    var detailOkInvoice = '<a style="width: 180px; padding-top: 5px;" href="' + url + '/' + value
                        .ok_invoice_file + '">' + value.ok_invoice_file + '</a>';
                    $('#detailNgEvidence').append(detailNgInvoice);
                    $('#detailNgInvoice').append(detailNgInvoice);
                    $('#detailOkInvoice').append(detailOkInvoice);
                }
            });
            var tableDetailBody = "";
            var cnt_finding = 0;

            $.each(material_check_findings, function(key, value) {
                if (value.inout_no == inout_no) {
                    cnt_finding += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td>' + cnt_finding + '</td>';
                    tableDetailBody += '<td style="text-align: left;">' + value.remark + '</td>';
                    tableDetailBody += '<td>' + value.quantity + '</td>';
                    var url = "{{ url('files/material_check') }}";
                    var evidence = '<img style="width: 180px; padding-top: 5px;" src="' + url + '/' + value
                        .evidence_file + '">';
                    tableDetailBody += '<td>' + evidence + '</td>';
                    tableDetailBody += '</tr>';
                }
            });

            if (category == 'Foreman') {
                $('#tableForemanDetailBody').html("");
                $('#tableForemanDetailBody').append(tableDetailBody);

                $('#modalForeman').modal('show');
            }

            if (category == 'Buyer') {
                $('#tableBuyerDetailBody').html("");
                $('#buyerForeman').html("");
                $('#buyerReport').html("");
                $('#buyerInvoice').val("");
                $('#buyerEvidence').val("");
                $('#tableBuyerDetailBody').append(tableDetailBody);

                $.each(material_check_details, function(key, value) {
                    if (value.position == 'Foreman' && value.inout_no == inout_no) {
                        $('#buyerForeman').append(value.report);
                    }
                });

                $('#modalBuyer').modal('show');
            }

            if (category == 'Vendor') {
                $('#tableVendorDetailBody').html("");
                $('#tableVendorDetailBody').append(tableDetailBody);
                $('#vendorInvoice').val("");
                $('#vendorETA').val("");

                $('#modalVendor').modal('show');
            }

            if (category == 'Arrived') {
                $('#tableArrivedDetailBody').html("");
                $('#tableArrivedDetailBody').append(tableDetailBody);
                $('#arrivedDate').val("");

                $('#modalArrived').modal('show');
            }

            if (category == 'Detail') {
                $('#tableDetailBody').html("");
                $('#tableDetailBody').append(tableDetailBody);
                $('#detailForeman').html("");
                $('#detailBuyer').html("");

                $.each(material_check_details, function(key, value) {
                    if (value.position == 'Foreman' && value.inout_no == inout_no) {
                        $('#detailForeman').append(value.report);
                    }
                    if (value.position == 'Buyer' && value.inout_no == inout_no) {
                        $('#detailBuyer').append(value.report);
                    }
                });

                $('#modalDetail').modal('show');
            }
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

        $('#tableMaterial').DataTable({
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
            },
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'ordering': false,
            'order': [],
            'info': true,
            'autoWidth': true,
            "sPaginationType": "full_numbers",
            "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true
        });

        CKEDITOR.replace('foremanReport', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}'
        });

        CKEDITOR.replace('buyerReport', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}'
        });
    </script>
@endsection
