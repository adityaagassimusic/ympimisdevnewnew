@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            text-align: center;
        }

        #resumeTable>tbody>tr>td:hover {
            cursor: pointer;
            background-color: #7dfa8c !important;
        }

        #resumeTableInternal>tbody>tr>td:hover {
            cursor: pointer;
            background-color: #7dfa8c !important;
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
            padding-top: 5px;
            padding-bottom: 5px;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        img {
            max-width: 100%
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .nmpd-grid {
            border: none;
            padding: 20px;
        }

        .nmpd-grid>tbody>tr>td {
            border: none;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
            <a class="btn btn-success pull-right" style="color: white;" onclick="modalCreate()">
                <i class="fa fa-pencil-square-o"></i> Buat Baru
            </a>
        </h1>
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
        <div class="row" style="padding-top: 10px;">
            <div class="col-xs-6">
                <div class="box box-solid">
                    <div class="box-body">
                        <div class="col-xs-12" style=" background-color: yellow; margin-bottom: 10px;">
                            <center><span style="font-weight: bold;">KALIBRASI EKSTERNAL</span></center>
                        </div>
                        <div class="col-xs-4">
                            <table id="resumeTable" class="table table-bordered table-striped table-hover"
                                style="height: 30vh;">
                                <tbody>
                                    <tr>
                                        <td onclick="fetchStatus('Aktif')"
                                            style="width: 1%; font-weight: bold; font-size: 1.1vw; text-align: left;">Aktif
                                        </td>
                                        <td onclick="fetchStatus('Aktif')" id="count_active"
                                            style="width: 0.3%; text-align: center; font-weight: bold; font-size: 1.1vw;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td onclick="fetchStatus('Akan Kalibrasi')"
                                            style="width: 1%; background-color: orange; font-weight: bold; font-size: 1.1vw; text-align: left;">
                                            Akan Kalibrasi</td>
                                        <td onclick="fetchStatus('Akan Kalibrasi')" id="count_atrisk"
                                            style="width: 0.3%; text-align: center; font-weight: bold; background-color: orange; font-size: 1.1vw;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td onclick="fetchStatus('Harus Kalibrasi')"
                                            style="width: 1%; background-color: rgb(255,204,255); font-weight: bold; font-size: 1.1vw; text-align: left;">
                                            Harus Kalibrasi</td>
                                        <td onclick="fetchStatus('Harus Kalibrasi')" id="count_expired"
                                            style="width: 0.3%; text-align: center; font-weight: bold; background-color: rgb(255,204,255); font-size: 1.1vw;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td onclick="fetchStatus('Tidak Aktif')"
                                            style="width: 1%; background-color: grey; font-weight: bold; color: black; font-size: 1.1vw; text-align: left;">
                                            Tidak Aktif</td>
                                        <td onclick="fetchStatus('Tidak Aktif')" id="count_inactive"
                                            style="width: 0.3%; text-align: center; font-weight: bold; background-color: grey; color: black; font-size: 1.1vw;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td onclick="fetchStatus('Total')"
                                            style="width: 1%; background-color: #605ca8; font-weight: bold; color: black; font-size: 1.1vw; text-align: left;">
                                            Total</td>
                                        <td onclick="fetchStatus('Total')" id="count_total"
                                            style="width: 0.3%; text-align: center; font-weight: bold; background-color: #605ca8; color: black; font-size: 1.1vw;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xs-8">
                            <div id="container" style="height: 30vh;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="box box-solid">
                    <div class="box-body">
                        <div class="col-xs-12" style=" background-color: #7dfa8c; margin-bottom: 10px;">
                            <center><span style="font-weight: bold;">KALIBRASI INTERNAL</span></center>
                        </div>
                        <div class="col-xs-4">
                            <table id="resumeTableInternal" class="table table-bordered table-striped table-hover"
                                style="height: 30vh;">
                                <tbody>
                                    <tr>
                                        <td onclick="fetchStatus('Aktif')"
                                            style="width: 1%; font-weight: bold; font-size: 1.1vw; text-align: left;">Aktif
                                        </td>
                                        <td onclick="fetchStatus('Aktif')" id="count_active_internal"
                                            style="width: 0.3%; text-align: center; font-weight: bold; font-size: 1.1vw;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td onclick="fetchStatus('Akan Kalibrasi')"
                                            style="width: 1%; background-color: orange; font-weight: bold; font-size: 1.1vw; text-align: left;">
                                            Akan Kalibrasi</td>
                                        <td onclick="fetchStatus('Akan Kalibrasi')" id="count_atrisk_internal"
                                            style="width: 0.3%; text-align: center; font-weight: bold; background-color: orange; font-size: 1.1vw;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td onclick="fetchStatus('Harus Kalibrasi')"
                                            style="width: 1%; background-color: rgb(255,204,255); font-weight: bold; font-size: 1.1vw; text-align: left;">
                                            Harus Kalibrasi</td>
                                        <td onclick="fetchStatus('Harus Kalibrasi')" id="count_expired_internal"
                                            style="width: 0.3%; text-align: center; font-weight: bold; background-color: rgb(255,204,255); font-size: 1.1vw;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td onclick="fetchStatus('Tidak Aktif')"
                                            style="width: 1%; background-color: grey; font-weight: bold; color: black; font-size: 1.1vw; text-align: left;">
                                            Tidak Aktif</td>
                                        <td onclick="fetchStatus('Tidak Aktif')" id="count_inactive_internal"
                                            style="width: 0.3%; text-align: center; font-weight: bold; background-color: grey; color: black; font-size: 1.1vw;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td onclick="fetchStatus('Total')"
                                            style="width: 1%; background-color: #605ca8; font-weight: bold; color: black; font-size: 1.1vw; text-align: left;">
                                            Total</td>
                                        <td onclick="fetchStatus('Total')" id="count_total_internal"
                                            style="width: 0.3%; text-align: center; font-weight: bold; background-color: #605ca8; color: black; font-size: 1.1vw;">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xs-8">
                            <div id="container2" style="height: 30vh;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <table id="tableCalibration" class="table table-bordered table-hover">
                    <thead style="">
                        <tr>
                            <th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">#</th>
                            <th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Gambar
                            </th>
                            <th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">ID</th>
                            <th style="width: 1%; text-align: left; background-color: #605ca8; color: white;">Merk<br>Nama
                                Alat</th>
                            <th style="width: 1%; text-align: left; background-color: #605ca8; color: white;">
                                Tipe<br>No.Seri</th>
                            <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Jarak Ukur
                            </th>
                            <th style="width: 0.1%; text-align: center; background-color: #605ca8; color: white;">Satuan
                            </th>
                            <th style="width: 1%; text-align: left; background-color: #605ca8; color: white;">
                                Lokasi<br>Departemen</th>
                            <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Frekuensi
                            </th>
                            <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Dari</th>
                            <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Hingga</th>
                            <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Status</th>
                            <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Toleransi
                            </th>
                            <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Koreksi
                            </th>
                            <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">
                                Ketidakpastian</th>
                            <th style="width: 1%; text-align: center; background-color: #605ca8; color: white;">Sertifikat
                            </th>
                        </tr>
                    </thead>
                    <tbody id="tableCalibrationBody">
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalEdit" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #605ca8; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            UPDATE ALAT UKUR BARU<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <input type="hidden" id="editId">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Foto
                                        Alat<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="file" id="editImage">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Category<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3 editCategory">
                                        <select class="form-control select2" id="editCategory"
                                            data-placeholder="Select Category" style="width: 100%;">
                                            <option value=""></option>
                                            <option value="Eksternal">Eksternal</option>
                                            <option value="Internal">Internal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Nama
                                        Alat<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" placeholder="Enter Name"
                                            id="editName">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Merk<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="Enter Brand"
                                            id="editBrand">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Tipe<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="Enter Type"
                                            id="editType">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Nomor
                                        Seri<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Enter Serial Number"
                                            id="editSerial">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jarak
                                        Ukur<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Enter Range"
                                            id="editRange">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Satuan<span class="text-red">*</span> :</label>
                                    <div class="col-sm-2 editUnit">
                                        <select class="form-control select2" id="editUnit"
                                            data-placeholder="Select Unit" style="width: 100%;">
                                            <option value=""></option>
                                            <option value='°C/RH'>°C/RH</option>
                                            <option value='°C'>°C</option>
                                            <option value='µm'>µm</option>
                                            <option value='µmRa'>µmRa</option>
                                            <option value='µS/cm'>µS/cm</option>
                                            <option value='bar'>bar</option>
                                            <option value='BRIX'>BRIX</option>
                                            <option value='c.N.m'>c.N.m</option>
                                            <option value='dB'>dB</option>
                                            <option value='g'>g</option>
                                            <option value='gram'>gram</option>
                                            <option value='HR C'>HR C</option>
                                            <option value='InH2O'>InH2O</option>
                                            <option value='kg'>kg</option>
                                            <option value='kgf'>kgf</option>
                                            <option value='kPa'>kPa</option>
                                            <option value='m/s'>m/s</option>
                                            <option value='mm'>mm</option>
                                            <option value='mS/cm'>mS/cm</option>
                                            <option value='mS/m'>mS/m</option>
                                            <option value='mS/m - S/m'>mS/m - S/m</option>
                                            <option value='MΩ'>MΩ</option>
                                            <option value='N'>N</option>
                                            <option value='Nm'>Nm</option>
                                            <option value='Pa'>Pa</option>
                                            <option value='pH'>pH</option>
                                            <option value='ppm'>ppm</option>
                                            <option value='rpm'>rpm</option>
                                            <option value='ton'>ton</option>
                                            <option value='Ω'>Ω</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Departemen<span class="text-red">*</span> :</label>
                                    <div class="col-sm-6 editDepartment">
                                        <select class="form-control select2" id="editDepartment"
                                            data-placeholder="Select Department" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($departments as $row)
                                                <option value="{{ $row->department_name }}">{{ $row->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Lokasi<span class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="Enter Location"
                                            id="editLocation">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Frekuensi Kalibrasi<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-3 editFrequency">
                                        <select class="form-control select2" id="editFrequency"
                                            data-placeholder="Select Category" style="width: 100%;">
                                            <option value=""></option>
                                            <option value="6 Month">6 Month</option>
                                            <option value="1 Year">1 Year</option>
                                            <option value="2 Year">2 Year</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Reminder<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <input type="text" value="0" class="numpad form-control"
                                                placeholder="Reminder" id="editReminder">
                                            <div class="input-group-addon" style="">
                                                <i class="fa fa-clock-o"></i> Day(s) Before Expired
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tanggal
                                        Kalibrasi<span class="text-red">*</span> :</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control datepicker" placeholder="Enter Date"
                                            id="editFrom">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Kalibrasi Berikutnya<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control datepicker" placeholder="Enter Date"
                                            id="editTo" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Toleransi<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Enter Tolerance"
                                            id="editTolerance">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Koreksi<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Enter Correction"
                                            id="editCorrection">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Ketidakpastian<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Enter Result"
                                            id="editResult">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Vendor<span class="text-red">*</span> :</label>
                                    <div class="col-sm-5 editVendor">
                                        <select class="form-control select2" id="editVendor"
                                            data-placeholder="Select Vendor" style="width: 100%;">
                                            <option value=""></option>
                                            <option value='CALTESYS'>CALTESYS</option>
                                            <option value='DIG AKURASI SISTEM'>DIG AKURASI SISTEM</option>
                                            <option value='KALIMAN'>KALIMAN</option>
                                            <option value='MIM'>MIM</option>
                                            <option value="INTERNAL">INTERNAL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Status<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3 editStatus">
                                        <select class="form-control select2" id="editStatus"
                                            data-placeholder="Select Status" style="width: 100%;">
                                            <option value=""></option>
                                            <option value="Aktif">Aktif</option>
                                            <option value="Akan Kalibrasi">Akan Kalibrasi</option>
                                            <option value="Harus Kalibrasi">Harus Kalibrasi</option>
                                            <option value="Rusak">Rusak</option>
                                            <option value="Tidak Aktif">Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Sertifikat<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="file" id="editAttachment">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Remark<span class="text-red"></span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="2" placeholder="Enter Remark" id="editRemark"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12" style="padding-top: 20px; padding-bottom: 20px;">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">KEMBALI</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="editCalibration()">SIMPAN</button>
                        </div>
                        <br>
                        <span style="font-weight: bold; font-size: 1.1vw;">Log Perubahan</span>
                        <table id="tableLog" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #605ca8; color: white;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">#</th>
                                    <th style="width: 1%; text-align: left;">Status</th>
                                    <th style="width: 1%; text-align: left;">Updated By</th>
                                    <th style="width: 1%; text-align: right;">Updated At</th>
                                </tr>
                            </thead>
                            <tbody id="tableLogBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #00a65a; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            INPUT ALAT UKUR BARU<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Foto
                                        Alat<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="file" id="createImage">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Category<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3 createCategory">
                                        <select class="form-control select2" id="createCategory"
                                            data-placeholder="Select Category" style="width: 100%;">
                                            <option value=""></option>
                                            <option value="Eksternal">Eksternal</option>
                                            <option value="Internal">Internal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Nama
                                        Alat<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" placeholder="Enter Name"
                                            id="createName">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Merk<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="Enter Brand"
                                            id="createBrand">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Tipe<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" placeholder="Enter Type"
                                            id="createType">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Nomor
                                        Seri<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Enter Serial Number"
                                            id="createSerial">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jarak
                                        Ukur<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Enter Range"
                                            id="createRange">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Satuan<span class="text-red">*</span> :</label>
                                    <div class="col-sm-2 createUnit">
                                        <select class="form-control select2" id="createUnit"
                                            data-placeholder="Select Unit" style="width: 100%;">
                                            <option value=""></option>
                                            <option value='°C/RH'>°C/RH</option>
                                            <option value='°C'>°C</option>
                                            <option value='µm'>µm</option>
                                            <option value='µmRa'>µmRa</option>
                                            <option value='µS/cm'>µS/cm</option>
                                            <option value='bar'>bar</option>
                                            <option value='BRIX'>BRIX</option>
                                            <option value='c.N.m'>c.N.m</option>
                                            <option value='dB'>dB</option>
                                            <option value='g'>g</option>
                                            <option value='gram'>gram</option>
                                            <option value='HR C'>HR C</option>
                                            <option value='InH2O'>InH2O</option>
                                            <option value='kg'>kg</option>
                                            <option value='kgf'>kgf</option>
                                            <option value='kPa'>kPa</option>
                                            <option value='m/s'>m/s</option>
                                            <option value='mm'>mm</option>
                                            <option value='mS/cm'>mS/cm</option>
                                            <option value='mS/m'>mS/m</option>
                                            <option value='mS/m - S/m'>mS/m - S/m</option>
                                            <option value='MΩ'>MΩ</option>
                                            <option value='N'>N</option>
                                            <option value='Nm'>Nm</option>
                                            <option value='Pa'>Pa</option>
                                            <option value='pH'>pH</option>
                                            <option value='ppm'>ppm</option>
                                            <option value='rpm'>rpm</option>
                                            <option value='ton'>ton</option>
                                            <option value='Ω'>Ω</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Departemen<span class="text-red">*</span> :</label>
                                    <div class="col-sm-6 createDepartment">
                                        <select class="form-control select2" id="createDepartment"
                                            data-placeholder="Select Department" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($departments as $row)
                                                <option value="{{ $row->department_name }}">{{ $row->department_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Lokasi<span class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="Enter Location"
                                            id="createLocation">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Frekuensi Kalibrasi<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-3 createFrequency">
                                        <select class="form-control select2" id="createFrequency"
                                            data-placeholder="Select Category" style="width: 100%;">
                                            <option value=""></option>
                                            <option value="6 Month">6 Month</option>
                                            <option value="1 Year">1 Year</option>
                                            <option value="2 Year">2 Year</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Reminder<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <div class="input-group">
                                            <input type="text" value="0" class="numpad form-control"
                                                placeholder="Reminder" id="createReminder">
                                            <div class="input-group-addon" style="">
                                                <i class="fa fa-clock-o"></i> Day(s) Before Expired
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Tanggal
                                        Kalibrasi<span class="text-red">*</span> :</label>
                                    <div class="col-sm-2">
                                        <input type="text" class="form-control datepicker" placeholder="Enter Date"
                                            id="createFrom">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Toleransi<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Enter Tolerance"
                                            id="createTolerance">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Koreksi<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Enter Correction"
                                            id="createCorrection">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Hasil<span class="text-red">*</span> :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" placeholder="Enter Result"
                                            id="createResult">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Vendor<span class="text-red">*</span> :</label>
                                    <div class="col-sm-5 createVendor">
                                        <select class="form-control select2" id="createVendor"
                                            data-placeholder="Select Vendor" style="width: 100%;">
                                            <option value=""></option>
                                            <option value='CALTESYS'>CALTESYS</option>
                                            <option value='DIG AKURASI SISTEM'>DIG AKURASI SISTEM</option>
                                            <option value='KALIMAN'>KALIMAN</option>
                                            <option value='MIM'>MIM</option>
                                            <option value="INTERNAL">INTERNAL</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Status<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3 createStatus">
                                        <select class="form-control select2" id="createStatus"
                                            data-placeholder="Select Status" style="width: 100%;">
                                            <option value=""></option>
                                            <option value="Aktif">Aktif</option>
                                            <option value="Rusak">Rusak</option>
                                            <option value="Tidak Aktif">Tidak Aktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Sertifikat<span class="text-red"></span> :</label>
                                    <div class="col-sm-7">
                                        <input type="file" id="createAttachment">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Remark<span class="text-red"></span> :</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" rows="2" placeholder="Enter Remark" id="createRemark">-</textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12" style="padding-top: 20px;">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">KEMBALI</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="createCalibration()">SIMPAN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAttachment">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modalAttachmentTitle"></h4>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <table id="tableAttachment" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #605ca8; color: white;">
                                <tr>
                                    <th style="width: 1%; text-align: center;">#</th>
                                    <th style="width: 1%; text-align: center;">Vendor</th>
                                    <th style="width: 1%; text-align: center;">Tanggal Kalibrasi</th>
                                    <th style="width: 1%; text-align: center;">Kalibrasi Berikutnya</th>
                                    <th style="width: 1%; text-align: center;">Attachment</th>
                                    <th style="width: 2%; text-align: center;">Uploaded By</th>
                                </tr>
                            </thead>
                            <tbody id="tableAttachmentBody">
                            </tbody>
                        </table>
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
    <script src="{{ url('js/jquery.numpad.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            fetchData();
            $('body').toggleClass("sidebar-collapse");
            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });
            $('.datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
        });

        $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%;"></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
        $.fn.numpad.defaults.buttonNumberTpl =
            '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl =
            '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function() {
            $(this).find('.done').addClass('btn-primary');
        };

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var calibrations = [];
        var calibration_logs = [];
        var calibration_attachments = [];

        $(function() {
            $('#createCategory').select2({
                dropdownParent: $('.createCategory'),
                minimumResultsForSearch: -1
            });
            $('#createUnit').select2({
                dropdownParent: $('.createUnit'),
                minimumResultsForSearch: -1
            });
            $('#createDepartment').select2({
                dropdownParent: $('.createDepartment')
            });
            $('#createFrequency').select2({
                dropdownParent: $('.createFrequency'),
                minimumResultsForSearch: -1
            });
            $('#createStatus').select2({
                dropdownParent: $('.createStatus'),
                minimumResultsForSearch: -1
            });
            $('#createVendor').select2({
                dropdownParent: $('.createVendor'),
                minimumResultsForSearch: -1
            });
            $('#editCategory').select2({
                dropdownParent: $('.editCategory'),
                minimumResultsForSearch: -1
            });
            $('#editUnit').select2({
                dropdownParent: $('.editUnit'),
                minimumResultsForSearch: -1
            });
            $('#editDepartment').select2({
                dropdownParent: $('.editDepartment')
            });
            $('#editFrequency').select2({
                dropdownParent: $('.editFrequency'),
                minimumResultsForSearch: -1
            });
            $('#editStatus').select2({
                dropdownParent: $('.editStatus'),
                minimumResultsForSearch: -1
            });
            $('#editVendor').select2({
                dropdownParent: $('.editVendor'),
                minimumResultsForSearch: -1
            });
        });

        function fetchData() {
            var data = {

            }
            $.get('{{ url('fetch/standardization/calibration') }}', data, function(result, status, xhr) {
                if (result.status) {
                    calibrations = result.calibrations;
                    calibration_logs = result.calibration_logs;
                    calibration_attachments = result.calibration_attachments;

                    var tableCalibrationBody = "";
                    $('#tableCalibrationBody').html("");
                    $('#tableCalibration').DataTable().clear();
                    $('#tableCalibration').DataTable().destroy();
                    var cnt = 0;
                    var count_active = 0;
                    var count_inactive = 0;
                    var count_atrisk = 0;
                    var count_expired = 0;
                    var count_total = 0;
                    var count_active_internal = 0;
                    var count_inactive_internal = 0;
                    var count_atrisk_internal = 0;
                    var count_expired_internal = 0;
                    var count_total_internal = 0;
                    var url = "{{ url('files/calibrations') }}";
                    $.each(result.calibrations, function(key, value) {
                        cnt += 1;
                        var color = "";
                        if (value.status == 'Aktif') {
                            if (value.category == 'Eksternal') {
                                count_active += 1;
                            }
                            if (value.category == 'Internal') {
                                count_active_internal += 1;
                            }
                            color = "background-color: white;"
                        }
                        if (value.status == 'Tidak Aktif') {
                            if (value.category == 'Eksternal') {
                                count_inactive += 1;
                            }
                            if (value.category == 'Internal') {
                                count_inactive_internal += 1;
                            }
                            color = "background-color: #808080;"
                        }
                        if (value.status == 'Harus Kalibrasi') {
                            if (value.category == 'Eksternal') {
                                count_expired += 1;
                            }
                            if (value.category == 'Internal') {
                                count_expired_internal += 1;
                            }
                            color = "background-color: #ffccff;"
                        }
                        if (value.status == 'Akan Kalibrasi') {
                            if (value.category == 'Eksternal') {
                                count_atrisk += 1;
                            }
                            if (value.category == 'Internal') {
                                count_atrisk_internal += 1;
                            }
                            color = "background-color: #ffa500;"
                        }
                        if (value.category == 'Eksternal') {
                            count_total += 1;
                        }
                        if (value.category == 'Internal') {
                            count_total_internal += 1;
                        }
                        tableCalibrationBody += '<tr>';
                        tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + cnt +
                            '</td>';
                        tableCalibrationBody +=
                            '<td style="width: 1%; text-align: center;"><img style="max-height: 100px;" src="' +
                            url + '/' + value.image_file + '"></td>';
                        tableCalibrationBody +=
                            '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'' +
                            value.calibration_id + '\')">' + value.calibration_id + '</a><br>(' + value
                            .category + ')</td>';
                        tableCalibrationBody += '<td style="width: 2%; text-align: left;">' + value
                            .instrument_brand + '<br>' + value.instrument_name + '</td>';
                        tableCalibrationBody += '<td style="width: 1%; text-align: left;">' + value
                            .instrument_type + '<br>' + value.serial_number + '</td>';
                        tableCalibrationBody += '<td style="width: 1%; text-align: center;">' + value
                            .range + '</td>';
                        tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value
                            .unit + '</td>';
                        tableCalibrationBody += '<td style="width: 3%; text-align: left;"><b>' + value
                            .location + '</b><br>' + value.department + '</td>';
                        tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value
                            .frequency + '</td>';
                        tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value
                            .valid_from + '</td>';
                        tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value
                            .valid_to + '</td>';
                        tableCalibrationBody +=
                            '<td style="width: 0.5%; text-align: center; font-weight: bold;">' + value
                            .status + '<br>(' + value.date_diff + ' Days)</td>';
                        tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value
                            .tolerance + '</td>';
                        tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value
                            .correction + '</td>';
                        tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value
                            .calibration_result + '</td>';
                        tableCalibrationBody +=
                            '<td style="width: 0.7%; text-align: center;"><a href="javascript:void(0)" onclick="modalAttachment(\'' +
                            value.calibration_id + '\')">' + value.vendor_name + '</a></td>';
                        tableCalibrationBody += '</tr>';
                    });

                    $('#count_active').text(count_active);
                    $('#count_inactive').text(count_inactive);
                    $('#count_atrisk').text(count_atrisk);
                    $('#count_expired').text(count_expired);
                    $('#count_total').text(count_total);
                    $('#count_active_internal').text(count_active_internal);
                    $('#count_inactive_internal').text(count_inactive_internal);
                    $('#count_atrisk_internal').text(count_atrisk_internal);
                    $('#count_expired_internal').text(count_expired_internal);
                    $('#count_total_internal').text(count_total_internal);

                    $('#tableCalibrationBody').append(tableCalibrationBody);

                    $('#tableCalibration').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [25, 50, -1],
                            ['25 rows', '50 rows', 'Show all']
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
                        'ordering': true,
                        'order': [],
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });

                    var array = result.calibrations;
                    var result = [];
                    array.reduce(function(res, value) {
                        if (!res[value.month_to + value.category]) {
                            res[value.month_to + value.category] = {
                                month_to: value.month_to,
                                category: value.category,
                                active: 0,
                                atrisk: 0,
                                expired: 0
                            };
                            result.push(res[value.month_to + value.category])
                        }
                        if (value.status == 'Aktif') {
                            res[value.month_to + value.category].active += 1;
                        }
                        if (value.status == 'Akan Kalibrasi') {
                            res[value.month_to + value.category].atrisk += 1;
                        }
                        if (value.status == 'Harus Kalibrasi') {
                            res[value.month_to + value.category].expired += 1;
                        }
                        return res;
                    }, {});


                    function SortByName(a, b) {
                        var aName = a.month_to;
                        var bName = b.month_to;
                        return ((aName < bName) ? -1 : ((aName > bName) ? 1 : 0));
                    }

                    result.sort(SortByName);

                    var categories = [];
                    var series_active = [];
                    var series_atrisk = [];
                    var series_expired = [];
                    var categories_internal = [];
                    var series_active_internal = [];
                    var series_atrisk_internal = [];
                    var series_expired_internal = [];

                    $.each(result, function(key, value) {
                        if (value.category == 'Eksternal') {
                            categories.push(value.month_to);
                            series_active.push(value.active);
                            series_atrisk.push(value.atrisk);
                            series_expired.push(value.expired);
                        }
                        if (value.category == 'Internal') {
                            categories_internal.push(value.month_to);
                            series_active_internal.push(value.active);
                            series_atrisk_internal.push(value.atrisk);
                            series_expired_internal.push(value.expired);
                        }
                    });


                    Highcharts.chart('container', {
                        chart: {
                            backgroundColor: null,
                            type: 'column',
                        },
                        title: {
                            text: 'Monitoring Waktu Kalibrasi',
                            style: {
                                fontSize: '12px',
                                fontWeight: 'bold'
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories: categories
                        },
                        yAxis: {
                            title: null,
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        tooltip: {
                            enabled: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: 'black',
                                stacking: 'normal',
                                dataLabels: {
                                    enabled: false
                                }
                            },
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            fetchStatus(this.series.name, this.category);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Aktif',
                            data: series_active,
                            color: 'white'
                        }, {
                            name: 'Akan Kalibrasi',
                            data: series_atrisk,
                            color: '#ffa500'
                        }, {
                            name: 'Harus Kalibrasi',
                            data: series_expired,
                            color: '#ffccff'
                        }]
                    });

                    Highcharts.chart('container2', {
                        chart: {
                            backgroundColor: null,
                            type: 'column',
                        },
                        title: {
                            text: 'Monitoring Waktu Kalibrasi',
                            style: {
                                fontSize: '12px',
                                fontWeight: 'bold'
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories: categories_internal
                        },
                        yAxis: {
                            title: null,
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold'
                                }
                            }
                        },
                        legend: {
                            enabled: false
                        },
                        tooltip: {
                            enabled: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: 'black',
                                stacking: 'normal',
                                dataLabels: {
                                    enabled: false
                                }
                            },
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            fetchStatus(this.series.name, this.category);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Aktif',
                            data: series_active_internal,
                            color: 'white'
                        }, {
                            name: 'Akan Kalibrasi',
                            data: series_atrisk_internal,
                            color: '#ffa500'
                        }, {
                            name: 'Harus Kalibrasi',
                            data: series_expired_internal,
                            color: '#ffccff'
                        }]
                    });
                } else {
                    alert('Attempt to retrieve data failed.');
                }
            });
        }

        function fetchStatus(status, month_to) {
            var tableCalibrationBody = "";
            $('#tableCalibrationBody').html("");
            $('#tableCalibration').DataTable().clear();
            $('#tableCalibration').DataTable().destroy();
            var cnt = 0;
            var url = "{{ url('files/calibrations') }}";
            $.each(calibrations, function(key, value) {
                if (status == 'Total') {
                    cnt += 1;
                    var color = "";
                    if (value.status == 'Aktif') {
                        color = "background-color: white;"
                    }
                    if (value.status == 'Tidak Aktif') {
                        color = "background-color: #808080;"
                    }
                    if (value.status == 'Harus Kalibrasi') {
                        color = "background-color: #ffccff;"
                    }
                    if (value.status == 'Akan Kalibrasi') {
                        color = "background-color: #ffa500;"
                    }
                    count_total += 1;
                    count_total_internal += 1;

                    tableCalibrationBody += '<tr>';
                    tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + cnt + '</td>';
                    tableCalibrationBody +=
                        '<td style="width: 1%; text-align: center;"><img style="max-height: 100px;" src="' + url +
                        '/' + value.image_file + '"></td>';
                    tableCalibrationBody +=
                        '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'' +
                        value.calibration_id + '\')">' + value.calibration_id + '</a><br>(' + value.category +
                        ')</td>';
                    tableCalibrationBody += '<td style="width: 2%; text-align: left;">' + value.instrument_brand +
                        '<br>' + value.instrument_name + '</td>';
                    tableCalibrationBody += '<td style="width: 1%; text-align: left;">' + value.instrument_type +
                        '<br>' + value.serial_number + '</td>';
                    tableCalibrationBody += '<td style="width: 1%; text-align: center;">' + value.range + '</td>';
                    tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value.unit + '</td>';
                    tableCalibrationBody += '<td style="width: 3%; text-align: left;"><b>' + value.location +
                        '</b><br>' + value.department + '</td>';
                    tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value.frequency +
                        '</td>';
                    tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value.valid_from +
                        '</td>';
                    tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value.valid_to +
                        '</td>';
                    tableCalibrationBody += '<td style="width: 0.5%; text-align: center; font-weight: bold;">' +
                        value.status + '<br>(' + value.date_diff + ' Days)</td>';
                    tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value.tolerance +
                        '</td>';
                    tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value.correction +
                        '</td>';
                    tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value
                        .calibration_result + '</td>';
                    tableCalibrationBody +=
                        '<td style="width: 0.7%; text-align: center;"><a href="javascript:void(0)" onclick="modalAttachment(\'' +
                        value.calibration_id + '\')">' + value.vendor_name + '</a></td>';
                    tableCalibrationBody += '</tr>';
                } else {
                    if (month_to) {
                        if (status == value.status && month_to == value.month_to) {
                            cnt += 1;
                            var color = "";
                            if (value.status == 'Aktif') {
                                color = "background-color: white;"
                            }
                            if (value.status == 'Tidak Aktif') {
                                color = "background-color: #808080;"
                            }
                            if (value.status == 'Harus Kalibrasi') {
                                color = "background-color: #ffccff;"
                            }
                            if (value.status == 'Akan Kalibrasi') {
                                color = "background-color: #ffa500;"
                            }
                            tableCalibrationBody += '<tr>';
                            tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + cnt + '</td>';
                            tableCalibrationBody +=
                                '<td style="width: 1%; text-align: center;"><img style="max-height: 100px;" src="' +
                                url + '/' + value.image_file + '"></td>';
                            tableCalibrationBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'' +
                                value.calibration_id + '\')">' + value.calibration_id + '</a><br>(' + value
                                .category + ')</td>';
                            tableCalibrationBody += '<td style="width: 2%; text-align: left;">' + value
                                .instrument_brand + '<br>' + value.instrument_name + '</td>';
                            tableCalibrationBody += '<td style="width: 1%; text-align: left;">' + value
                                .instrument_type + '<br>' + value.serial_number + '</td>';
                            tableCalibrationBody += '<td style="width: 1%; text-align: center;">' + value.range +
                                '</td>';
                            tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value.unit +
                                '</td>';
                            tableCalibrationBody += '<td style="width: 3%; text-align: left;"><b>' + value
                                .location + '</b><br>' + value.department + '</td>';
                            tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .frequency + '</td>';
                            tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .valid_from + '</td>';
                            tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .valid_to + '</td>';
                            tableCalibrationBody +=
                                '<td style="width: 0.5%; text-align: center; font-weight: bold;">' + value.status +
                                '<br>(' + value.date_diff + ' Days)</td>';
                            tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value
                                .tolerance + '</td>';
                            tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value
                                .correction + '</td>';
                            tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value
                                .calibration_result + '</td>';
                            tableCalibrationBody +=
                                '<td style="width: 0.7%; text-align: center;"><a href="javascript:void(0)" onclick="modalAttachment(\'' +
                                value.calibration_id + '\')">' + value.vendor_name + '</a></td>';
                            tableCalibrationBody += '</tr>';
                        }
                    } else {
                        if (status == value.status) {
                            cnt += 1;
                            var color = "";
                            if (value.status == 'Aktif') {
                                color = "background-color: white;"
                            }
                            if (value.status == 'Tidak Aktif') {
                                color = "background-color: #808080;"
                            }
                            if (value.status == 'Harus Kalibrasi') {
                                color = "background-color: #ffccff;"
                            }
                            if (value.status == 'Akan Kalibrasi') {
                                color = "background-color: #ffa500;"
                            }
                            tableCalibrationBody += '<tr>';
                            tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + cnt + '</td>';
                            tableCalibrationBody +=
                                '<td style="width: 1%; text-align: center;"><img style="max-height: 100px;" src="' +
                                url + '/' + value.image_file + '"></td>';
                            tableCalibrationBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'' +
                                value.calibration_id + '\')">' + value.calibration_id + '</a><br>(' + value
                                .category + ')</td>';
                            tableCalibrationBody += '<td style="width: 2%; text-align: left;">' + value
                                .instrument_brand + '<br>' + value.instrument_name + '</td>';
                            tableCalibrationBody += '<td style="width: 1%; text-align: left;">' + value
                                .instrument_type + '<br>' + value.serial_number + '</td>';
                            tableCalibrationBody += '<td style="width: 1%; text-align: center;">' + value.range +
                                '</td>';
                            tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value.unit +
                                '</td>';
                            tableCalibrationBody += '<td style="width: 3%; text-align: left;"><b>' + value
                                .location + '</b><br>' + value.department + '</td>';
                            tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .frequency + '</td>';
                            tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .valid_from + '</td>';
                            tableCalibrationBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .valid_to + '</td>';
                            tableCalibrationBody +=
                                '<td style="width: 0.5%; text-align: center; font-weight: bold;">' + value.status +
                                '<br>(' + value.date_diff + ' Days)</td>';
                            tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value
                                .tolerance + '</td>';
                            tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value
                                .correction + '</td>';
                            tableCalibrationBody += '<td style="width: 0.3%; text-align: center;">' + value
                                .calibration_result + '</td>';
                            tableCalibrationBody +=
                                '<td style="width: 0.7%; text-align: center;"><a href="javascript:void(0)" onclick="modalAttachment(\'' +
                                value.calibration_id + '\')">' + value.vendor_name + '</a></td>';
                            tableCalibrationBody += '</tr>';
                        }
                    }
                }
            });

            $('#tableCalibrationBody').append(tableCalibrationBody);

            $('#tableCalibration').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [25, 50, -1],
                    ['25 rows', '50 rows', 'Show all']
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
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });
        }

        function clearModal() {
            $('#createCategory').prop('selectedIndex', 0).change();
            $('#createImage').val("");
            $('#createName').val("");
            $('#createBrand').val("");
            $('#createType').val("");
            $('#createSerial').val("");
            $('#createRange').val("");
            $('#createUnit').prop('selectedIndex', 0).change();
            $('#createDepartment').prop('selectedIndex', 0).change();
            $('#createLocation').val("");
            $('#createFrequency').prop('selectedIndex', 0).change();
            $('#createReminder').val(30);
            $('#createFrom').val("");
            $('#createTolerance').val("");
            $('#createCorrection').val("");
            $('#createResult').val("");
            $('#createVendor').prop('selectedIndex', 0).change();
            $('#createAttachment').val("");
            $('#createStatus').prop('selectedIndex', 0).change();
            $('#createRemark').val("");

            $('#editId').val("");
            $('#editImage').val("");
            $('#editCategory').prop('selectedIndex', 0).change();
            $('#editName').val("");
            $('#editBrand').val("");
            $('#editType').val("");
            $('#editSerial').val("");
            $('#editRange').val("");
            $('#editUnit').prop('selectedIndex', 0).change();
            $('#editDepartment').prop('selectedIndex', 0).change();
            $('#editLocation').val("");
            $('#editFrequency').prop('selectedIndex', 0).change();
            $('#editReminder').val(30);
            $('#editFrom').val("");
            $('#editTo').val("");
            $('#editTolerance').val("");
            $('#editCorrection').val("");
            $('#editResult').val("");
            $('#editVendor').prop('selectedIndex', 0).change();
            $('#editAttachment').val("");
            $('#editStatus').prop('selectedIndex', 0).change();
            $('#editRemark').val("");
        }

        function alertLog(id) {
            var calibration_id = "";
            var category = "";
            var instrument_name = "";
            var instrument_brand = "";
            var instrument_type = "";
            var serial_number = "";
            var range = "";
            var unit = "";
            var department = "";
            var location = "";
            var frequency = "";
            var reminder = "";
            var valid_from = "";
            var valid_to = "";
            var tolerance = "";
            var correction = "";
            var calibration_result = "";
            var vendor_name = "";
            var status = "";
            var remark = "";

            $.each(calibration_logs, function(key, value) {
                if (value.id == id) {
                    calibration_id = value.calibration_id;
                    category = value.category;
                    instrument_name = value.instrument_name;
                    instrument_brand = value.instrument_brand;
                    instrument_type = value.instrument_type;
                    serial_number = value.serial_number;
                    range = value.range;
                    unit = value.unit;
                    department = value.department;
                    location = value.location;
                    frequency = value.frequency;
                    reminder = value.reminder;
                    valid_from = value.valid_from;
                    valid_to = value.valid_to;
                    tolerance = value.tolerance;
                    correction = value.correction;
                    calibration_result = value.calibration_result;
                    vendor_name = value.vendor_name;
                    status = value.status;
                    remark = value.remark;
                }
            });

            alert(
                'ID: ' + calibration_id + '\n' +
                'Ketegori: ' + category + '\n' +
                'Nama Alat: ' + instrument_name + '\n' +
                'Merk Alat: ' + instrument_brand + '\n' +
                'Tipe Alat: ' + instrument_type + '\n' +
                'Nomor Seri: ' + serial_number + '\n' +
                'Jarak Ukur: ' + range + '\n' +
                'Satuan: ' + unit + '\n' +
                'Departemen: ' + department + '\n' +
                'Lokasi: ' + location + '\n' +
                'Frekuensi: ' + frequency + '\n' +
                'Pengingat: ' + reminder + '\n' +
                'Tanggal Kalibrasi: ' + valid_from + '\n' +
                'Kalibrasi Berikutnya: ' + valid_to + '\n' +
                'Toleransi: ' + tolerance + '\n' +
                'Koreksi: ' + correction + '\n' +
                'Ketidakpastian: ' + calibration_result + '\n' +
                'Nama Vendor: ' + vendor_name + '\n' +
                'Status: ' + status + '\n' +
                'Catatan: ' + remark
            );
        }

        function modalAttachment(calibration_id) {
            var tableAttachmentBody = "";
            $('#tableAttachmentBody').html("");

            var cnt = 0;
            $.each(calibration_attachments, function(key, value) {
                if (value.calibration_id == calibration_id) {
                    cnt += 1;
                    tableAttachmentBody += '<tr>';
                    tableAttachmentBody += '<td style="text-align: center;">' + cnt + '</td>';
                    tableAttachmentBody += '<td style="text-align: center;">' + value.vendor_name + '</td>';
                    tableAttachmentBody += '<td style="text-align: center;">' + value.valid_from + '</td>';
                    tableAttachmentBody += '<td style="text-align: center;">' + value.valid_to + '</td>';
                    tableAttachmentBody +=
                        '<td style="text-align: center;"><a href="{{ asset('files/calibrations') }}/' + value
                        .file_name + '" target="_blank">' + value.file_name + '</a></td>';
                    tableAttachmentBody += '<td style="text-align: center;">' + value.created_by_name + '</td>';
                    tableAttachmentBody += '</tr>';
                }
            });

            $('#tableAttachmentBody').append(tableAttachmentBody);
            $('#modalAttachment').modal('show');
        }

        function modalEdit(calibration_id) {
            clearModal();
            $.each(calibrations, function(key, value) {
                if (calibration_id == value.calibration_id) {
                    $('#editId').val(value.calibration_id);
                    $('#editCategory').val(value.category).change();
                    $('#editName').val(value.instrument_name);
                    $('#editBrand').val(value.instrument_brand);
                    $('#editType').val(value.instrument_type);
                    $('#editSerial').val(value.serial_number);
                    $('#editRange').val(value.range);
                    $('#editUnit').val(value.unit).change();
                    $('#editDepartment').val(value.department).change();
                    $('#editLocation').val(value.location);
                    $('#editFrequency').val(value.frequency).change();
                    $('#editReminder').val(value.reminder);
                    $('#editFrom').val(value.valid_from);
                    $('#editTo').val(value.valid_to);
                    $('#editTolerance').val(value.tolerance);
                    $('#editCorrection').val(value.correction);
                    $('#editResult').val(value.calibration_result);
                    $('#editVendor').val(value.vendor_name).change();
                    $('#editStatus').val(value.status).change();
                    $('#editRemark').val(value.remark);
                    return false;
                }
            });

            var tableLogBody = "";
            $('#tableLogBody').html("");

            var cnt = 0;
            $.each(calibration_logs, function(ley, value) {
                if (calibration_id == value.calibration_id) {
                    cnt += 1;
                    tableLogBody += '<tr>';
                    tableLogBody += '<td>' + cnt + '</td>';
                    tableLogBody +=
                        '<td style="font-weight: bold;"><a href="javascript:void(0)" onclick="alertLog(\'' + value
                        .id + '\')">' + value.log + '</a></td>';
                    tableLogBody += '<td>' + value.created_by + ' - ' + value.created_by_name + '</td>';
                    tableLogBody += '<td>' + value.updated_at + '</td>';
                    tableLogBody += '</tr>';
                }
            });

            $('#tableLogBody').append(tableLogBody);
            $('#modalEdit').modal('show');
        }

        function modalCreate() {
            clearModal();
            $('#modalCreate').modal('show');
        }

        function editCalibration() {
            if (confirm("Apakah anda yakin akan menambahkan data ini?")) {
                $('#loading').show();
                var calibration_id = $('#editId').val();
                var category = $('#editCategory').val();
                var name = $('#editName').val();
                var brand = $('#editBrand').val();
                var type = $('#editType').val();
                var serial = $('#editSerial').val();
                var range = $('#editRange').val();
                var unit = $('#editUnit').val();
                var department = $('#editDepartment').val();
                var location = $('#editLocation').val();
                var frequency = $('#editFrequency').val();
                var reminder = $('#editReminder').val();
                var from = $('#editFrom').val();
                var tolerance = $('#editTolerance').val();
                var correction = $('#editCorrection').val();
                var result = $('#editResult').val();
                var vendor = $('#editVendor').val();
                var attachment = $('#editAttachment').val();
                var image = $('#editImage').val();
                var status = $('#editStatus').val();
                var remark = $('#editRemark').val();

                if (category == '' || name == '' || brand == '' || type == '' || serial == '' || range == '' || unit ==
                    '' || department == '' || location == '' || frequency == '' || from == '' || tolerance == '' ||
                    correction == '' ||
                    result == '' || vendor == '' || status == '' || reminder == '' || calibration_id == '') {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
                    return false;
                }

                old_category = "";
                old_name = "";
                old_brand = "";
                old_type = "";
                old_serial = "";
                old_range = "";
                old_unit = "";
                old_department = "";
                old_location = "";
                old_frequency = "";
                old_reminder = "";
                old_from = "";
                old_tolerance = "";
                old_correction = "";
                old_result = "";
                old_vendor = "";
                old_status = "";
                old_remark = "";

                $.each(calibrations, function(key, value) {
                    if (value.calibration_id == calibration_id) {
                        old_category = value.category;
                        old_name = value.instrument_name;
                        old_brand = value.instrument_brand;
                        old_type = value.instrument_type;
                        old_serial = value.serial_number;
                        old_range = value.range;
                        old_unit = value.unit;
                        old_department = value.department;
                        old_location = value.location;
                        old_frequency = value.frequency;
                        old_reminder = value.reminder;
                        old_from = value.valid_from;
                        old_tolerance = value.tolerance;
                        old_correction = value.correction;
                        old_result = value.calibration_result;
                        old_vendor = value.vendor_name;
                        old_status = value.status;
                        old_remark = value.remark;
                        return false;
                    }
                });

                if (category == old_category && name == old_name && brand == old_brand && type == old_type && serial ==
                    old_serial && range == old_range && unit == old_unit && department == old_department && location ==
                    old_location && frequency == old_frequency && reminder == old_reminder && from == old_from &&
                    tolerance == old_tolerance && correction == old_correction && result == old_result && vendor ==
                    old_vendor && status == old_status &&
                    remark == old_remark && attachment == "" && image == "") {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!', 'Tidak ada perubahan terdeteksi.');
                    return false;
                }

                var formData = new FormData();
                var attachment = $('#editAttachment').prop('files')[0];
                var file = $('#editAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

                var image = $('#editImage').prop('files')[0];
                var file2 = $('#editImage').val().replace(/C:\\fakepath\\/i, '').split(".");

                formData.append('calibration_id', calibration_id);
                formData.append('category', category);
                formData.append('name', name);
                formData.append('brand', brand);
                formData.append('type', type);
                formData.append('serial', serial);
                formData.append('range', range);
                formData.append('unit', unit);
                formData.append('department', department);
                formData.append('location', location);
                formData.append('frequency', frequency);
                formData.append('remark', remark);
                formData.append('reminder', reminder);
                formData.append('from', from);
                formData.append('tolerance', tolerance);
                formData.append('correction', correction);
                formData.append('result', result);
                formData.append('status', status);
                formData.append('vendor', vendor);
                formData.append('attachment', attachment);
                formData.append('extension', file[file.length - 1]);
                formData.append('file_name', file[0]);
                formData.append('image', image);
                formData.append('image_extension', file2[file2.length - 1]);
                formData.append('image_file_name', file2[0]);

                $.ajax({
                    url: "{{ url('edit/standardization/calibration') }}",
                    method: "POST",
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        if (data.status) {
                            $('#modalEdit').modal('hide');
                            $('#loading').hide();
                            clearModal();
                            fetchData();
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

        function createCalibration() {
            if (confirm("Apakah anda yakin akan menambahkan data ini?")) {
                $('#loading').show();
                var category = $('#createCategory').val();
                var name = $('#createName').val();
                var brand = $('#createBrand').val();
                var type = $('#createType').val();
                var serial = $('#createSerial').val();
                var range = $('#createRange').val();
                var unit = $('#createUnit').val();
                var department = $('#createDepartment').val();
                var location = $('#createLocation').val();
                var frequency = $('#createFrequency').val();
                var reminder = $('#createReminder').val();
                var from = $('#createFrom').val();
                var tolerance = $('#createTolerance').val();
                var correction = $('#createCorrection').val();
                var result = $('#createResult').val();
                var vendor = $('#createVendor').val();
                var attachment = $('#createAttachment').val();
                var image = $('#createImage').val();
                var status = $('#createStatus').val();
                var remark = $('#createRemark').val();

                if (category == '' || name == '' || brand == '' || type == '' || serial == '' || range == '' || unit ==
                    '' || department == '' || location == '' || frequency == '' || from == '' || tolerance == '' ||
                    correction == '' ||
                    result == '' || vendor == '' || status == '' || reminder == '') {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!', 'Semua kolom dengan bintang merah harus diisi.');
                    return false;
                }

                var formData = new FormData();
                var attachment = $('#createAttachment').prop('files')[0];
                var file = $('#createAttachment').val().replace(/C:\\fakepath\\/i, '').split(".");

                var image = $('#createImage').prop('files')[0];
                var file2 = $('#createImage').val().replace(/C:\\fakepath\\/i, '').split(".");

                formData.append('category', category);
                formData.append('name', name);
                formData.append('brand', brand);
                formData.append('type', type);
                formData.append('serial', serial);
                formData.append('range', range);
                formData.append('unit', unit);
                formData.append('department', department);
                formData.append('location', location);
                formData.append('frequency', frequency);
                formData.append('remark', remark);
                formData.append('reminder', reminder);
                formData.append('from', from);
                formData.append('tolerance', tolerance);
                formData.append('correction', correction);
                formData.append('result', result);
                formData.append('status', status);
                formData.append('vendor', vendor);
                formData.append('attachment', attachment);
                formData.append('extension', file[file.length - 1]);
                formData.append('file_name', file[0]);
                formData.append('image', image);
                formData.append('image_extension', file2[file2.length - 1]);
                formData.append('image_file_name', file2[0]);

                $.ajax({
                    url: "{{ url('input/standardization/calibration') }}",
                    method: "POST",
                    data: formData,
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        if (data.status) {
                            $('#modalCreate').modal('hide');
                            $('#loading').hide();
                            clearModal();
                            fetchData();
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
