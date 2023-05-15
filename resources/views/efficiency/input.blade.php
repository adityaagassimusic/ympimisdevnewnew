@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        th,
        td {
            white-space: nowrap;
        }

        div.dataTables_wrapper {
            margin: 0 auto;
        }

        tr:hover td {
            background-color: #7dfa8c !important;
            color: black !important;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            padding-top: 2px;
            padding-bottom: 2px;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            padding-top: 2px;
            padding-bottom: 2px;
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
            <span id="period">(Periode {{ date('F Y') }})</span>
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
        <div class="row" style="padding-top: 20px; min-height: 100px;">
            <div class="col-xs-2">
                <div class="input-group date pull-right">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control monthpicker" name="filterPeriod" id="filterPeriod"
                        placeholder="Pilih Periode" onchange="fetchData()">
                </div>
                <a class="btn btn-success" onclick="modalAddManpower()" style="width: 100%; margin-top: 5px;"><i
                        class="fa fa-user-plus"></i> Tambah Manpower</a>
                <a class="btn btn-danger" onclick="modalUploadDiversion()"
                    style="width: 100%; margin-top: 5px; margin-bottom: 10px;"><i class="fa fa-calendar-plus-o"></i> Tambah
                    Pengalihan</a>
                <div class="input-group pull-right" style="margin-bottom: 5px;">
                    <div class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </div>
                    <select class="form-control select2" id="searchRemark" style="width: 100%;"
                        data-placeholder="Cari Remark">
                        <option value=""></option>
                        @foreach ($remarks as $remark)
                            @if ($remark['department'] == $department)
                                @foreach ($remark['remark'] as $row)
                                    <option value="{{ $row }}">{{ $row }}
                                    </option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="input-group pull-right" style="margin-bottom: 5px;">
                    <div class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </div>
                    <input class="form-control" placeholder="Cari ID Karyawan" style="width: 100%;" type="text"
                        id="searchEmployeeId">
                </div>
                <div class="input-group pull-right" style="margin-bottom: 5px;">
                    <div class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </div>
                    <input class="form-control" placeholder="Cari Nama Karyawan" style="width: 100%;" type="text"
                        id="searchEmployeeName">
                </div>
                <div class="input-group pull-right" style="margin-bottom: 5px;">
                    <div class="input-group-addon">
                        <i class="fa fa-search"></i>
                    </div>
                    <select class="form-control select2" id="searchStatus" style="width: 100%;"
                        data-placeholder="Cari Status">
                        <option value=""></option>
                        <option value="DIRECT">DIRECT</option>
                        <option value="INDIRECT">INDIRECT</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-8">
                <table id="tableResume" class="table table-bordered table-hover">
                    <thead style="background-color: #605ca8; color: white;">
                        <tr>
                            <th style="width: 3%; text-align: center;" rowspan="2">Resume</th>
                            <th style="width: 1%; text-align: center;" colspan="5">Manpower</th>
                            <th style="width: 1%; text-align: center;" colspan="4">Jam Kerja</th>
                        </tr>
                        <tr>
                            <th style="width: 1%; text-align: center;">Direct</th>
                            <th style="width: 1%; text-align: center;">Indirect</th>
                            <th style="width: 1%; text-align: center;">Tetap</th>
                            <th style="width: 1%; text-align: center;">Kontrak</th>
                            <th style="width: 1%; text-align: center;">Total</th>
                            <th style="width: 1%; text-align: center;">Kehadiran</th>
                            <th style="width: 1%; text-align: center;">Lembur</th>
                            <th style="width: 1%; text-align: center;">Pengalihan</th>
                            <th style="width: 1%; text-align: center;">Total</th>
                        </tr>
                    </thead>
                    <tbody id="tableResumeBody">
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="background-color: #fffcb7; text-align: center;">Total</th>
                            <th style="background-color: #fffcb7; text-align: center;"></th>
                            <th style="background-color: #fffcb7; text-align: center;"></th>
                            <th style="background-color: #fffcb7; text-align: center;"></th>
                            <th style="background-color: #fffcb7; text-align: center;"></th>
                            <th style="background-color: #fffcb7; text-align: center;"></th>
                            <th style="background-color: #fffcb7; text-align: center;"></th>
                            <th style="background-color: #fffcb7; text-align: center;"></th>
                            <th style="background-color: #fffcb7; text-align: center;"></th>
                            <th style="background-color: #fffcb7; text-align: center;"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="box box-solid">
            <div class="box-body">
                <center>
                    <span
                        style="background-color: #00a65a; color: white; font-weight: bold; border: 1px solid black; font-size: 1vw;">
                        &nbsp;&nbsp;&nbsp;Kehadiran&nbsp;&nbsp;
                    </span>&nbsp;&nbsp;&nbsp;
                    <span
                        style="background-color: #f39c12; color: white; font-weight: bold; border: 1px solid black; font-size: 1vw;">
                        &nbsp;&nbsp;&nbsp;Lembur&nbsp;&nbsp;
                    </span>&nbsp;&nbsp;&nbsp;
                    <span
                        style="background-color: #dd4b39; color: white; font-weight: bold; border: 1px solid black; font-size: 1vw;">
                        &nbsp;&nbsp;&nbsp;Pengalihan&nbsp;&nbsp;
                    </span>
                </center>
                <div class="table-responsive">
                    <table id="tableAttendance" class="table table-bordered table-hover table-responsive" style="">
                        <thead>
                            <tr>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="box box-solid">
            <div class="box-body">
                <div
                    style="background-color: #605ca8; color: white; padding: 5px; text-align: center; margin-bottom: 8px;">
                    <span style="font-weight: bold; font-size: 20px;">Detail List Pengalihan</span>
                </div>
                <div class="table-responsive">
                    <table id="tableDiversionList" class="table table-bordered table-hover table-responsive"
                        style="">
                        <thead>
                            <tr>
                                <th style="width: 1%; text-align: center;">Remark</th>
                                <th style="width: 1%; text-align: center;">ID</th>
                                <th style="width: 4%; text-align: left;">Nama</th>
                                <th style="width: 1%; text-align: center;">Status</th>
                                <th style="width: 1%; text-align: center;">Posisi</th>
                                <th style="width: 1%; text-align: center;">Kategori</th>
                                <th style="width: 3%; text-align: center;">Pengalihan</th>
                                <th style="width: 1%; text-align: right;">Tanggal</th>
                                <th style="width: 1%; text-align: right;">Jam</th>
                                <th style="width: 2%; text-align: left;">Input Oleh</th>
                                <th style="width: 1%; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableDiversionListBody">
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
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalAddDiversion">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Tanggal<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="addDiversionShiftDate" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">ID
                                        Karyawan<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="addDiversionEmployeeId" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Nama<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="addDiversionEmployeeName"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Nama<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="addDiversionRemark" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Keterangan<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2" id="addDiversionCategory"
                                            style="width: 100%;">
                                            <option value=""></option>
                                            <option value='Attendance_Izin Keluar'>Izin Keluar</option>
                                            <option value='Attendance_Klinik'>Klinik</option>
                                            <option value='Attendance_Mutasi'>Mutasi</option>
                                            <option value='Attendance_KYT'>KYT</option>
                                            <option value='Attendance_Pulang Cepat'>Pulang Cepat</option>
                                            <option value='Attendance_Terlambat'>Terlambat</option>
                                            <option value='Attendance_Adjustment'>Adjustment</option>
                                            <option value='Production_Bantu Proses'>Bantu Proses</option>
                                            <option value='Production_Acara Serikat'>Acara Serikat</option>
                                            <option value='Production_COC'>COC</option>
                                            <option value='Production_HUT YMPI'>HUT YMPI</option>
                                            <option value='Production_Kebakaran'>Kebakaran</option>
                                            <option value='Production_Listrik Padam'>Listrik Padam</option>
                                            <option value='Production_Meeting (Praktek Kaizen, dll)'>Meeting (Praktek
                                                Kaizen, dll)</option>
                                            <option value='Production_Safety Riding'>Safety Riding</option>
                                            <option value='Production_Total Drill'>Total Drill</option>
                                            <option value='Production_Training Non Process'>Training Non Process</option>
                                            <option value='Production_YEWO'>YEWO</option>
                                            <option value='Production_Sub Leader'>Sub Leader</option>
                                            <option value='Transfer_Incoming Check Part (Pengecekan Awal)'>Incoming Check
                                                Part (Pengecekan Awal)</option>
                                            <option value='Transfer_Trial / Trial PE'>Trial / Trial PE</option>
                                            <option value='Indirect_5S Bulanan'>5S Bulanan</option>
                                            <option value='Indirect_Aktifitas Sub Leader'>Aktifitas Sub Leader</option>
                                            <option value='Indirect_Chorei Bersama'>Chorei Bersama</option>
                                            <option value='Indirect_Jishu Hozen, Setsubi'>Jishu Hozen, Setsubi</option>
                                            <option value='Indirect_Pembuangan Limbah Cair, Cleaning Duct, Cleaning Mesin'>
                                                Pembuangan Limbah Cair, Cleaning Duct, Cleaning Mesin</option>
                                            <option value='Indirect_Stock Taking'>Stock Taking</option>
                                            <option value='Indirect_Training Proses'>Training Proses</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah
                                        Jam<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="addDiversionWorkHour">
                                    </div>
                                    <div class="col-sm-10 col-sm-offset-3">
                                        <small style="color: red;">*Gunakan minus untuk pengurangan waktu input dalam jam
                                            (Contoh: -3.5)</small>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12" style="margin-top: 10px;">
                            <button class="btn btn-success pull-right" style="font-weight: bold; width: 20%;"
                                onclick="addDiversion()">Tambahkan</button>
                        </div>
                        <div class="col-md-12" style="margin-top: 10px;">
                            <table id="tableDiversion" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 1%; text-align: center;">Tanggal</th>
                                        <th style="width: 1%; text-align: center;">Kategori</th>
                                        <th style="width: 3%; text-align: left;">Keterangan</th>
                                        <th style="width: 1%; text-align: center;">Jam</th>
                                        <th style="width: 1%; text-align: center;">#</th>
                                    </tr>
                                </thead>
                                <tbody id="tableDiversionBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditDiversion">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <input type="hidden" id="editDiversionId">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Tanggal<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="editDiversionShiftDate" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">ID
                                        Karyawan<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="editDiversionEmployeeId" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Nama<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="editDiversionEmployeeName"
                                            disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Keterangan<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2" id="editDiversionCategory"
                                            style="width: 100%;">
                                            <option value=""></option>
                                            <option value='Attendance_Izin Keluar'>Izin Keluar</option>
                                            <option value='Attendance_Klinik'>Klinik</option>
                                            <option value='Attendance_Mutasi'>Mutasi</option>
                                            <option value='Attendance_KYT'>KYT</option>
                                            <option value='Attendance_Pulang Cepat'>Pulang Cepat</option>
                                            <option value='Attendance_Terlambat'>Terlambat</option>
                                            <option value='Attendance_Adjustment'>Adjustment</option>
                                            <option value='Production_Bantu Proses'>Bantu Proses</option>
                                            <option value='Production_Acara Serikat'>Acara Serikat</option>
                                            <option value='Production_COC'>COC</option>
                                            <option value='Production_HUT YMPI'>HUT YMPI</option>
                                            <option value='Production_Kebakaran'>Kebakaran</option>
                                            <option value='Production_Listrik Padam'>Listrik Padam</option>
                                            <option value='Production_Meeting (Praktek Kaizen, dll)'>Meeting (Praktek
                                                Kaizen, dll)</option>
                                            <option value='Production_Safety Riding'>Safety Riding</option>
                                            <option value='Production_Total Drill'>Total Drill</option>
                                            <option value='Production_Training Non Process'>Training Non Process</option>
                                            <option value='Production_YEWO'>YEWO</option>
                                            <option value='Production_Sub Leader'>Sub Leader</option>
                                            <option value='Transfer_Incoming Check Part (Pengecekan Awal)'>Incoming Check
                                                Part (Pengecekan Awal)</option>
                                            <option value='Transfer_Trial / Trial PE'>Trial / Trial PE</option>
                                            <option value='Indirect_5S Bulanan'>5S Bulanan</option>
                                            <option value='Indirect_Aktifitas Sub Leader'>Aktifitas Sub Leader</option>
                                            <option value='Indirect_Chorei Bersama'>Chorei Bersama</option>
                                            <option value='Indirect_Jishu Hozen, Setsubi'>Jishu Hozen, Setsubi</option>
                                            <option value='Indirect_Pembuangan Limbah Cair, Cleaning Duct, Cleaning Mesin'>
                                                Pembuangan Limbah Cair, Cleaning Duct, Cleaning Mesin</option>
                                            <option value='Indirect_Stock Taking'>Stock Taking</option>
                                            <option value='Indirect_Training Proses'>Training Proses</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah
                                        Jam<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="editDiversionWorkHour">
                                    </div>
                                    <div class="col-sm-10 col-sm-offset-3">
                                        <small style="color: red;">*Gunakan minus untuk pengurangan waktu input dalam jam
                                            (Contoh: -3.5)</small>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12" style="margin-top: 10px;">
                            <button class="btn btn-success pull-right" style="font-weight: bold; width: 20%;"
                                onclick="editDiversion()">Perbaharui</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalUploadDiversion">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Tanggal<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-6">
                                        <div class="input-group date pull-right" style="text-align: center;">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control datepicker"
                                                name="uploadDiversionShiftDate" id="uploadDiversionShiftDate"
                                                placeholder="Pilih Tanggal">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Keterangan<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2" id="uploadDiversionCategory"
                                            style="width: 100%;">
                                            <option value=""></option>
                                            <option value='Attendance_Izin Keluar'>Izin Keluar</option>
                                            <option value='Attendance_Klinik'>Klinik</option>
                                            <option value='Attendance_Mutasi'>Mutasi</option>
                                            <option value='Attendance_KYT'>KYT</option>
                                            <option value='Attendance_Pulang Cepat'>Pulang Cepat</option>
                                            <option value='Attendance_Terlambat'>Terlambat</option>
                                            <option value='Attendance_Adjustment'>Adjustment</option>
                                            <option value='Production_Bantu Proses'>Bantu Proses</option>
                                            <option value='Production_Acara Serikat'>Acara Serikat</option>
                                            <option value='Production_COC'>COC</option>
                                            <option value='Production_HUT YMPI'>HUT YMPI</option>
                                            <option value='Production_Kebakaran'>Kebakaran</option>
                                            <option value='Production_Listrik Padam'>Listrik Padam</option>
                                            <option value='Production_Meeting (Praktek Kaizen, dll)'>Meeting (Praktek
                                                Kaizen, dll)</option>
                                            <option value='Production_Safety Riding'>Safety Riding</option>
                                            <option value='Production_Total Drill'>Total Drill</option>
                                            <option value='Production_Training Non Process'>Training Non Process
                                            </option>
                                            <option value='Production_YEWO'>YEWO</option>
                                            <option value='Production_Sub Leader'>Sub Leader</option>
                                            <option value='Transfer_Incoming Check Part (Pengecekan Awal)'>Incoming
                                                Check
                                                Part (Pengecekan Awal)</option>
                                            <option value='Transfer_Trial / Trial PE'>Trial / Trial PE</option>
                                            <option value='Indirect_5S Bulanan'>5S Bulanan</option>
                                            <option value='Indirect_Aktifitas Sub Leader'>Aktifitas Sub Leader</option>
                                            <option value='Indirect_Chorei Bersama'>Chorei Bersama</option>
                                            <option value='Indirect_Jishu Hozen, Setsubi'>Jishu Hozen, Setsubi</option>
                                            <option value='Indirect_Pembuangan Limbah Cair, Cleaning Duct, Cleaning Mesin'>
                                                Pembuangan Limbah Cair, Cleaning Duct, Cleaning Mesin</option>
                                            <option value='Indirect_Stock Taking'>Stock Taking</option>
                                            <option value='Indirect_Training Proses'>Training Proses</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Jumlah
                                        Jam<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="uploadDiversionWorkHour">
                                    </div>
                                    <div class="col-sm-10 col-sm-offset-3">
                                        <small style="color: red;">*Gunakan minus untuk pengurangan waktu input dalam jam
                                            (Contoh: -3.5)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12"
                                style="background-color:#3c8dbc; margin-bottom: 10px; text-align: center; font-weight: bold; color: white; font-size: 16px;">
                                Tambahkan Karyawan
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for=""
                                    class="col-sm-3 control-label">Pekerjaan<span class="text-red"></span>
                                    :</label>
                                <div class="col-sm-4">
                                    <select class="form-control select2" id="uploadDiversionJobStatus"
                                        data-placeholder="Pilih Pekerjaan" style="width: 100%;">
                                        <option value=""></option>
                                        <option value="DIRECT">DIRECT</option>
                                        <option value="INDIRECT">INDIRECT</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Remark<span
                                        class="text-red"></span>
                                    :</label>
                                <div class="col-sm-6">
                                    <select class="form-control select2" id="uploadDiversionRemark"
                                        data-placeholder="Pilih Remark" style="width: 100%;">
                                        <option value=""></option>
                                        @foreach ($remarks as $remark)
                                            @if ($remark['department'] == $department)
                                                @foreach ($remark['remark'] as $row)
                                                    <option value="{{ $row }}">{{ $row }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-sm-3 control-label">ID
                                    Karyawan<span class="text-red"></span>
                                    :</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" rows="4" placeholder="Format copy dari excel: [ID Karyawan]"
                                        id="uploadDiversionEmployeeId" style="width: 100%;"></textarea>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12" style="margin-top: 10px;">
                            <button class="btn btn-primary pull-right" style="font-weight: bold; width: 20%;"
                                onclick="addDiversionEmployee()">Tambahkan</button>
                        </div>
                        <div class="col-md-12" style="margin-top: 10px;">
                            <table id="tableUploadDiversion" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 1%; text-align: center;">#</th>
                                        <th style="width: 1%; text-align: center;">Remark</th>
                                        <th style="width: 1%; text-align: center;">Status</th>
                                        <th style="width: 1%; text-align: center;">ID</th>
                                        <th style="width: 4%; text-align: left;">Nama</th>
                                    </tr>
                                </thead>
                                <tbody id="tableUploadDiversionBody">
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-12" style="margin-top: 10px;">
                            <button class="btn btn-success pull-right" style="font-weight: bold; width: 20%;"
                                onclick="uploadDiversion()">Upload</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAddManpower">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">ID
                                        Karyawan<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-xs-9">
                                        <select class="form-control select2" id="addEmployeeId"
                                            data-placeholder="Pilih ID Karyawan" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->employee_id }}">{{ $employee->employee_id }} -
                                                    {{ $employee->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Remark<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-9">
                                        <select class="form-control select2" id="addRemark"
                                            data-placeholder="Pilih Remark" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($remarks as $remark)
                                                @if ($remark['department'] == $department)
                                                    @foreach ($remark['remark'] as $row)
                                                        <option value="{{ $row }}">{{ $row }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12" style="margin-top: 20px;">
                            <button class="btn btn-success pull-right" style="font-weight: bold; width: 20%;"
                                onclick="addManpower()">Tambahkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditManpower">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <input type="hidden" id="editId">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-4 control-label">ID
                                        Karyawan<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" id="editEmployeeId" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-4 control-label">Nama<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" id="editEmployeeName" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-4 control-label">Status
                                        Pekerjaan<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-3">
                                        <select class="form-control select2" id="editJobStatus"
                                            data-placeholder="Pilih Job Status" style="width: 100%;">
                                            <option value=""></option>
                                            <option value="DIRECT">DIRECT</option>
                                            <option value="INDIRECT">INDIRECT</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-4 control-label">Remark<span class="text-red">*</span>
                                        :</label>
                                    <div class="col-sm-6">
                                        <select class="form-control select2" id="editRemark"
                                            data-placeholder="Pilih Remark" style="width: 100%;">
                                            @foreach ($remarks as $remark)
                                                @if ($remark['department'] == $department)
                                                    @foreach ($remark['remark'] as $row)
                                                        <option value="{{ $row }}">{{ $row }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12" style="margin-top: 20px;">
                            <button class="btn btn-danger pull-left" style="font-weight: bold; width: 30%;"
                                onclick="removeManpower()">Hapus Manpower</button>
                            <button class="btn btn-success pull-right" style="font-weight: bold; width: 30%;"
                                onclick="editManpower()">Perbaharui</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
    <script src="{{ url('js/moment.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/dataTables.fixedColumns.min.js') }}"></script>
    <script src="{{ url('bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });
            $('#uploadDiversionShiftDate').daterangepicker({
                timePicker: false,
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
            $('#searchStatus').select2({
                minimumResultsForSearch: -1,
                allowClear: true,
            });
            $('#searchRemark').select2({
                allowClear: true,
            });
            fetchData();
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var department = "{{ $department }}";
        var manpowers = [];
        var calendars = [];
        var attendances = [];
        var overtimes = [];
        var diversions = [];
        var diversion_details = [];
        var resumes = [];
        var period = "";
        var added_employees = [];
        var upload_employees = [];
        var table;

        $(function() {
            $('#addDiversionCategory').select2({
                dropdownParent: $('#modalAddDiversion')
            });
            $('#uploadDiversionCategory').select2({
                dropdownParent: $('#modalUploadDiversion')
            });
            $('#uploadDiversionJobStatus').select2({
                dropdownParent: $('#modalUploadDiversion'),
                minimumResultsForSearch: -1
            });
            $('#uploadDiversionRemark').select2({
                dropdownParent: $('#modalUploadDiversion'),
            });
            $('#addEmployeeId').select2({
                dropdownParent: $('#modalAddManpower')
            });
            $('#addRemark').select2({
                dropdownParent: $('#modalAddManpower'),
                minimumResultsForSearch: -1
            });
            $('#editJobStatus').select2({
                dropdownParent: $('#modalEditManpower'),
                minimumResultsForSearch: -1
            });
            $('#editRemark').select2({
                dropdownParent: $('#modalEditManpower'),
                minimumResultsForSearch: -1
            });
            $('#editDiversionCategory').select2({
                dropdownParent: $('#modalEditDiversion')
            });
        });

        function modalUploadDiversion() {
            $('#uploadDiversionShiftDate').val("");
            $('#uploadDiversionCategory').prop('selectedIndex', 0).change();
            $('#uploadDiversionWorkHour').val("");
            $('#uploadDiversionJobStatus').prop('selectedIndex', 0).change();
            $('#uploadDiversionRemark').prop('selectedIndex', 0).change();
            $('#uploadDiversionEmployeeId').val("");
            $('#modalUploadDiversion').modal('show');
            $('#tableUploadDiversionBody').html("");
            added_employees = [];
        }

        function addDiversionEmployee() {
            var job_status = $('#uploadDiversionJobStatus').val();
            var remark = $('#uploadDiversionRemark').val();
            var employee_ids = $('#uploadDiversionEmployeeId').val().split('\n');

            added_employees = manpowers;

            if (job_status != "") {
                var added_employees = $.grep(added_employees, function(e) {
                    return e.job_status == job_status;
                });
            }

            if (remark != "") {
                var added_employees = $.grep(added_employees, function(e) {
                    return e.remark == remark;
                });
            }

            if (employee_ids.filter(Boolean).length > 0) {
                var added_employees = $.grep(added_employees, function(e) {
                    return jQuery.inArray(e.employee_id, employee_ids) !== -1;
                });
            }

            if (added_employees.length <= 0) {
                openErrorGritter('Gagal!', 'Tidak ada karyawan yang akan ditambahkan.');
                audio_error.play();
                $('#loading').hide();
                return false;
            }

            var tableUploadDiversionBody = "";
            $('#tableUploadDiversionBody').html("");

            $.each(added_employees, function(key, value) {
                tableUploadDiversionBody += '<tr>';
                tableUploadDiversionBody += '<td style="width: 1%; text-align: center;">' + (key + 1) + '</td>';
                tableUploadDiversionBody += '<td style="width: 1%; text-align: center;">' + value.remark + '</td>';
                tableUploadDiversionBody += '<td style="width: 1%; text-align: center;">' + value.job_status +
                    '</td>';
                tableUploadDiversionBody += '<td style="width: 1%; text-align: center;">' + value.employee_id +
                    '</td>';
                tableUploadDiversionBody += '<td style="width: 4%; text-align: left;">' + value.employee_name +
                    '</td>';
                tableUploadDiversionBody += '</tr>';
            });

            $('#tableUploadDiversionBody').append(tableUploadDiversionBody);
            upload_employees = added_employees;
        }

        function uploadDiversion() {
            if (confirm("Apakah anda yakin akan menambahkan data pengalihan ini?")) {
                $('#c').show();
                var shift_dates = $('#uploadDiversionShiftDate').val();
                var category = $('#uploadDiversionCategory').val().split('_')[0];
                var remark = $('#uploadDiversionCategory').val().split('_')[1];
                var work_hour = $('#uploadDiversionWorkHour').val();
                var employees = upload_employees;

                if (shift_dates == "" || category == "" || remark == "" || work_hour == "" || work_hour == 0 || employees
                    .length <= 0 || work_hour == "") {
                    openErrorGritter('Gagal!', 'Lengkapi data yang akan ditambahkan terlebih dahulu.');
                    audio_error.play();
                    $('#loading').hide();
                    return false;
                }

                var data = {
                    shift_dates: shift_dates,
                    employees: employees,
                    category: category,
                    remark: remark,
                    work_hour: work_hour,
                    period: period,
                    department: department,
                }
                $.post('{{ url('upload/efficiency/diversion') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        // fetchData();
                        openSuccessGritter('Berhasil!', result.message);
                        audio_ok.play();
                        $('#modalUploadDiversion').modal('hide');
                        $('#loading').hide();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        $('#loading').hide();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function modalEditDiversion(id) {
            $.each(diversion_details, function(key, value) {
                if (value.id == id) {
                    $('#editDiversionId').val(value.id);
                    $('#editDiversionShiftDate').val(value.shift_date);
                    $('#editDiversionEmployeeId').val(value.employee_id);
                    $('#editDiversionEmployeeName').val(value.employee_name);
                    $('#editDiversionCategory').val(value.category + "_" + value.diversion).change();
                    $('#editDiversionWorkHour').val(value.work_hour);
                    return false;
                }
            });
            $('#modalEditDiversion').modal('show');
        }

        function editDiversion() {
            $('#loading').show();
            var id = $('#editDiversionId').val();
            var category = $('#editDiversionCategory').val().split('_')[0];
            var diversion = $('#editDiversionCategory').val().split('_')[1];
            var work_hour = $('#editDiversionWorkHour').val();

            var data = {
                id: id,
                category: category,
                diversion: diversion,
                work_hour: work_hour,
            }

            $.post('{{ url('edit/efficiency/diversion') }}', data, function(result, status, xhr) {
                if (result.status) {
                    // fetchData();
                    $('#modalEditDiversion').modal('hide');
                    openSuccessGritter('Berhasil!', result.message);
                    $('#loading').hide();
                    audio_ok.play();
                } else {
                    openErrorGritter('Gagal!', result.message);
                    audio_error.play();
                    $('#loading').hide();
                    return false;
                }
            });

        }

        function modalEditManpower(id) {
            $.each(manpowers, function(key, value) {
                if (value.id == id) {
                    $('#editId').val(value.id);
                    $('#editEmployeeId').val(value.employee_id);
                    $('#editEmployeeName').val(value.employee_name);
                    $('#editJobStatus').val(value.job_status).change();
                    $('#editRemark').val(value.remark).change();
                    $('#modalEditManpower').modal('show');
                    return false;
                }
            });
        }

        function removeManpower() {
            if (confirm("Apakah anda yakin akan menghapus data manpower ini?")) {
                $('#loading').show();
                var id = $('#editId').val();
                var data = {
                    id: id,
                }
                $.post('{{ url('edit/efficiency/manpower_remove') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        // fetchData();
                        $('#modalEditManpower').modal('hide');
                        openSuccessGritter('Berhasil!', result.message);
                        $('#loading').hide();
                        audio_ok.play();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        $('#loading').hide();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function editManpower() {
            if (confirm("Apakah anda yakin akan mengubah data manpower ini?")) {
                $('#loading').show();
                var id = $('#editId').val();
                var employee_id = $('#editEmployeeId').val();
                var job_status = $('#editJobStatus').val();
                var remark = $('#editRemark').val();
                var data = {
                    id: id,
                    employee_id: employee_id,
                    job_status: job_status,
                    remark: remark,
                    department: department,
                    period: period,
                }
                $.post('{{ url('edit/efficiency/manpower_edit') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        // fetchData();
                        $('#modalEditManpower').modal('hide');
                        openSuccessGritter('Berhasil!', result.message);
                        $('#loading').hide();
                        audio_ok.play();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        $('#loading').hide();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function modalAddManpower() {
            $('#addEmployeeId').prop('selectedIndex', 0).change();
            $('#addRemark').prop('selectedIndex', 0).change();
            $('#modalAddManpower').modal('show');
        }

        function addManpower() {
            if (confirm("Apakah anda yakin akan menambahkan data manpower ini?")) {
                $('#loading').show();
                var employee_id = $('#addEmployeeId').val();
                var remark = $('#addRemark').val();

                if (employee_id == "" || remark == "") {
                    openErrorGritter('Gagal!', 'Semua data harus terisi');
                    audio_error.play();
                    $('#loading').hide();
                    return false;
                }

                var data = {
                    employee_id: employee_id,
                    remark: remark,
                    department: department,
                    period: period,
                }
                $.post('{{ url('edit/efficiency/manpower_add') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        // fetchData();
                        $('#addEmployeeId').prop('selectedIndex', 0).change();
                        $('#modalAddManpower').modal('hide');
                        openSuccessGritter('Berhasil!', result.message);
                        $('#loading').hide();
                        audio_ok.play();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        $('#loading').hide();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function addDiversion() {
            if (confirm("Apakah anda yakin akan menambahkan data pengalihan ini?")) {
                $('#loading').show();
                var shift_date = $('#addDiversionShiftDate').val();
                var employee_id = $('#addDiversionEmployeeId').val();
                var category = $('#addDiversionCategory').val().split('_')[0];
                var remark = $('#addDiversionCategory').val().split('_')[1];
                var remark_2 = $('#addDiversionRemark').val();
                var work_hour = $('#addDiversionWorkHour').val();
                var data = {
                    shift_date: shift_date,
                    employee_id: employee_id,
                    category: category,
                    remark: remark,
                    work_hour: work_hour,
                    department: department,
                    remark_2: remark_2,
                }
                $.post('{{ url('input/efficiency/diversion') }}', data, function(result, status, xhr) {
                    if (result.status) {

                        var tableDiversionBody = "";
                        $('#tableDiversionBody').html("");

                        $.each(result.datas, function(key, value) {
                            tableDiversionBody += '<tr>';
                            tableDiversionBody += '<td style="width: 1%; text-align: center;">' + value
                                .shift_date + '</td>';
                            tableDiversionBody += '<td style="width: 1%; text-align: center;">' + value
                                .category + '</td>';
                            tableDiversionBody += '<td style="width: 3%; text-align: left;">' + value
                                .remark +
                                '</td>';
                            tableDiversionBody += '<td style="width: 1%; text-align: center;">' + value
                                .work_hour + '</td>';
                            tableDiversionBody +=
                                '<td style="width: 1%; text-align: center;"><button class="btn btn-danger btn-xs" onclick="removeDiversion(\'' +
                                value.id + '\')"><i class="fa fa-trash"></i></button></td>';
                            tableDiversionBody += '</tr>';
                        });

                        $('#tableDiversionBody').append(tableDiversionBody);
                        // fetchData();
                        openSuccessGritter('Berhasil!', result.message);
                        audio_ok.play();
                        $('#loading').hide();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        $('#loading').hide();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function removeDiversion(id) {
            if (confirm("Apakah anda yakin akan menghapus data pengalihan ini?")) {
                $('#loading').show();
                var data = {
                    id: id,
                }
                $.post('{{ url('delete/efficiency/diversion') }}', data, function(result, status, xhr) {
                    if (result.status) {

                        var whichtr = $('#remove_diversion_' + result.id).closest("tr");
                        whichtr.remove();
                        var whichtr = $('#remove_diversion2_' + result.id).closest("tr");
                        whichtr.remove();
                        // fetchData();
                        openSuccessGritter('Berhasil!', result.message);
                        audio_ok.play();
                        $('#loading').hide();
                    } else {
                        openErrorGritter('Gagal!', result.message);
                        audio_error.play();
                        $('#loading').hide();
                        return false;
                    }
                });
            } else {
                return false;
            }
        }

        function openModal(category, employee_id, employee_name, shift_date, remark) {
            $('#loading').show();
            var data = {
                category: category,
                employee_id: employee_id,
                shift_date: shift_date,
            }
            $('#addDiversionShiftDate').val(shift_date);
            $('#addDiversionEmployeeId').val(employee_id);
            $('#addDiversionEmployeeName').val(employee_name);
            $('#addDiversionRemark').val(remark);
            $('#addDiversionCategory').prop('selectedIndex', 0).change();
            $('#addDiversionWorkHour').val("");
            $.get('{{ url('fetch/efficiency/diversion') }}', data, function(result, status, xhr) {
                if (result.status) {
                    var tableDiversionBody = "";
                    $('#tableDiversionBody').html("");

                    $.each(result.datas, function(key, value) {
                        tableDiversionBody += '<tr>';
                        tableDiversionBody += '<td style="width: 1%; text-align: center;">' + value
                            .shift_date + '</td>';
                        tableDiversionBody += '<td style="width: 1%; text-align: center;">' + value
                            .category + '</td>';
                        tableDiversionBody += '<td style="width: 3%; text-align: left;">' + value
                            .remark +
                            '</td>';
                        tableDiversionBody += '<td style="width: 1%; text-align: center;">' + value
                            .work_hour + '</td>';
                        tableDiversionBody +=
                            '<td style="width: 1%; text-align: center;"><button class="btn btn-danger btn-xs" id="remove_diversion_' +
                            value.id + '" onclick="removeDiversion(\'' +
                            value.id + '\')"><i class="fa fa-trash"></i></button></td>';
                        tableDiversionBody += '</tr>';
                    });

                    $('#tableDiversionBody').append(tableDiversionBody);
                    $('#modalAddDiversion').modal('show');
                    $('#loading').hide();
                } else {
                    openErrorGritter('Gagal!', result.message);
                    audio_error.play();
                    $('#loading').hide();
                    return false;
                }
            });
        }

        function fetchData() {
            $('#loading').show();
            period = $('#filterPeriod').val();
            var data = {
                period: period,
                department: department,
            }
            $.get('{{ url('fetch/efficiency/input') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#period').html(result.period_title);
                    manpowers = result.manpowers;
                    calendars = result.calendars;
                    attendances = result.attendances;
                    overtimes = result.overtimes;
                    diversions = result.diversions;
                    diversion_details = result.diversion_details;
                    resumes = result.resumes;
                    period = result.period;

                    $('#tableResume').DataTable().clear();
                    $('#tableResume').DataTable().destroy();
                    var tableResumeBody = "";
                    $('#tableResumeBody').html("");

                    $.each(resumes, function(key, value) {
                        tableResumeBody += '<tr>';
                        tableResumeBody += '<td style="width: 3%; text-align: center;">' + value.remark +
                            '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + value
                            .total_mp_direct + '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + value
                            .total_mp_indirect + '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + value
                            .total_mp_permanent + '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + value
                            .total_mp_contract + '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + value.total_mp +
                            '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + value
                            .total_work_hour + '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + value
                            .total_overtime_hour + '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + value
                            .total_diversion_hour + '</td>';
                        tableResumeBody += '<td style="width: 1%; text-align: center;">' + (parseFloat(value
                                .total_work_hour) + parseFloat(value.total_overtime_hour) +
                            parseFloat(value.total_diversion_hour)) + '</td>';
                        tableResumeBody += '</tr>';
                    });

                    $('#tableResumeBody').append(tableResumeBody);

                    var array = result.attendances;
                    var result = [];
                    array.reduce(function(res, value) {
                        if (!res[value.employee_id]) {
                            res[value.employee_id] = {
                                employee_id: value.employee_id,
                                total_ct: 0,
                                total_a: 0,
                                total_i: 0,
                                total_sd: 0,
                                total_x: 0,
                            };
                            result.push(res[value.employee_id])
                        }
                        if (value.work_hour == 'CT' || value.work_hour == 'CK') {
                            res[value.employee_id].total_ct += 1;
                        }
                        if (value.work_hour == 'A') {
                            res[value.employee_id].total_a += 1;
                        }
                        if (value.work_hour == 'I') {
                            res[value.employee_id].total_i += 1;
                        }
                        if (value.work_hour == 'SD') {
                            res[value.employee_id].total_sd += 1;
                        }
                        if (value.work_hour == 'X') {
                            res[value.employee_id].total_x += 1;
                        }
                        return res;
                    }, {});


                    $('#tableAttendance').DataTable().clear();
                    $('#tableAttendance').DataTable().destroy();
                    var tableAttendance = "";
                    $('#tableAttendance').html("");

                    tableAttendance += '<thead>';
                    tableAttendance += '<tr>';
                    tableAttendance +=
                        '<th style="width: 15px; font-size: 12px; text-align: center; z-index: 100; background-color: #605ca8; color: white;">Remark</th>';
                    tableAttendance +=
                        '<th style="width: 15px; font-size: 12px; text-align: center; z-index: 100; background-color: #605ca8; color: white;">ID</th>';
                    tableAttendance +=
                        '<th style="width: 15px; font-size: 12px; text-align: left; z-index: 100; background-color: #605ca8; color: white;">Nama</th>';
                    tableAttendance +=
                        '<th style="width: 15px; font-size: 12px; text-align: center; z-index: 100; background-color: #605ca8; color: white;">Status</th>';
                    tableAttendance +=
                        '<th style="width: 15px; font-size: 12px; text-align: center; z-index: 100; background-color: #605ca8; color: white;">Posisi</th>';

                    $.each(calendars, function(key, value) {
                        tableAttendance +=
                            '<th style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #00a65a; color: white;">' +
                            value.header + '</th>';
                    });

                    tableAttendance +=
                        '<th style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #fffcb7; color: black;">CT</th>';
                    tableAttendance +=
                        '<th style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #fffcb7; color: black;">A</th>';
                    tableAttendance +=
                        '<th style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #fffcb7; color: black;">I</th>';
                    tableAttendance +=
                        '<th style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #fffcb7; color: black;">SD</th>';
                    tableAttendance +=
                        '<th style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #fffcb7; color: black;">X</th>';


                    $.each(calendars, function(key, value) {
                        tableAttendance +=
                            '<th style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #f39c12; color: white;">' +
                            value.header + '</th>';
                    });

                    $.each(calendars, function(key, value) {
                        tableAttendance +=
                            '<th style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #dd4b39; color: white;">' +
                            value.header + '</th>';
                    });

                    tableAttendance += '</tr>';
                    tableAttendance += '</thead>';

                    tableAttendance += '<tbody style="background-color: white;">';

                    for (var i = 0; i < manpowers.length; i++) {

                        tableAttendance += '<tr>';
                        tableAttendance +=
                            '<td style="width: 15px; background-color: white; font-size: 12px; text-align: center;">' +
                            manpowers[i]
                            .remark +
                            '</td>';
                        tableAttendance +=
                            '<td style="width: 15px; background-color: white; font-size: 12px; text-align: center;"><a href="javascript:void(0)" onclick="modalEditManpower(\'' +
                            manpowers[i].id + '\')">' +
                            manpowers[i]
                            .employee_id +
                            '</a></td>';
                        tableAttendance +=
                            '<td style="width: 15px; background-color: white; font-size: 12px; text-align: left;">' +
                            manpowers[i]
                            .employee_name + '</td>';
                        tableAttendance +=
                            '<td style="width: 15px; background-color: white; font-size: 12px; text-align: center;">' +
                            manpowers[i]
                            .job_status +
                            '</td>';
                        tableAttendance +=
                            '<td style="width: 15px; background-color: white; font-size: 12px; text-align: center;">' +
                            manpowers[i]
                            .position +
                            '</td>';

                        for (var j = 0; j < calendars.length; j++) {
                            var color = "";
                            if (calendars[j].remark == 'H') {
                                color = "background-color: grey; color: white;";
                            }
                            var found = false;
                            for (var k = 0; k < attendances.length; k++) {
                                if (calendars[j].week_date == attendances[k].shift_date && manpowers[i]
                                    .employee_id == attendances[k].employee_id) {
                                    if (jQuery.inArray(attendances[k].work_hour, ['CT', 'CK', 'A', 'I', 'SD',
                                            'X'
                                        ]) !== -
                                        1) {
                                        color = "background-color: #ff6090; color: black;";
                                    }
                                    tableAttendance +=
                                        '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; ' +
                                        color + '">' +
                                        attendances[k].work_hour + '</td>';
                                    found = true;
                                }
                            }
                            if (found == false) {
                                tableAttendance +=
                                    '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; ' +
                                    color + '"></td>';
                            }
                        }

                        var found_attendance = false;
                        for (var j = 0; j < result.length; j++) {
                            var color_ct = "background-color: #fffcb7; color: black;";
                            var color_a = "background-color: #fffcb7; color: black;";
                            var color_i = "background-color: #fffcb7; color: black;";
                            var color_sd = "background-color: #fffcb7; color: black;";
                            var color_x = "background-color: #fffcb7; color: black;";
                            if (manpowers[i].employee_id == result[j].employee_id) {
                                if (result[j].total_ct > 0) {
                                    color_ct = "background-color: #fffcb7; color: red; font-weight: bold;"
                                }
                                if (result[j].total_a > 0) {
                                    color_a = "background-color: #fffcb7; color: red; font-weight: bold;"
                                }
                                if (result[j].total_i > 0) {
                                    color_i = "background-color: #fffcb7; color: red; font-weight: bold;"
                                }
                                if (result[j].total_sd > 0) {
                                    color_sd = "background-color: #fffcb7; color: red; font-weight: bold;"
                                }
                                if (result[j].total_x > 0) {
                                    color_x = "background-color: #fffcb7; color: red; font-weight: bold;"
                                }
                                tableAttendance +=
                                    '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; ' +
                                    color_ct + '">' + result[j].total_ct + '</td>';
                                tableAttendance +=
                                    '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; ' +
                                    color_a + '">' + result[j].total_a + '</td>';
                                tableAttendance +=
                                    '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; ' +
                                    color_i + '">' + result[j].total_i + '</td>';
                                tableAttendance +=
                                    '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; ' +
                                    color_sd + '">' + result[j].total_sd + '</td>';
                                tableAttendance +=
                                    '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; ' +
                                    color_x + '">' + result[j].total_x + '</td>';
                                var found_attendance = true;
                            }
                        }

                        if (found_attendance == false) {
                            tableAttendance +=
                                '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #fffcb7; color: black;">0</td>';
                            tableAttendance +=
                                '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #fffcb7; color: black;">0</td>';
                            tableAttendance +=
                                '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #fffcb7; color: black;">0</td>';
                            tableAttendance +=
                                '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #fffcb7; color: black;">0</td>';
                            tableAttendance +=
                                '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; background-color: #fffcb7; color: black;">0</td>';
                        }

                        for (var j = 0; j < calendars.length; j++) {
                            var color = "";
                            if (calendars[j].remark == 'H') {
                                color = "background-color: grey; color: white;";
                            }
                            var found = false;
                            for (var k = 0; k < overtimes.length; k++) {
                                if (calendars[j].week_date == overtimes[k].shift_date && manpowers[i]
                                    .employee_id == overtimes[k].employee_id) {
                                    color = "background-color: #ff6090; color: black;";
                                    tableAttendance +=
                                        '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; ' +
                                        color + '">' +
                                        overtimes[k].work_hour + '</td>';
                                    found = true;
                                }
                            }
                            if (found == false) {
                                tableAttendance +=
                                    '<td style="width: 15px; vertical-align: top; font-size: 12px; text-align: center; ' +
                                    color + '"></td>';
                            }
                        }

                        for (var j = 0; j < calendars.length; j++) {
                            var color = "";
                            if (calendars[j].remark == 'H') {
                                color = "background-color: grey; color: white;";
                            }
                            var found = false;
                            for (var k = 0; k < diversions.length; k++) {
                                if (calendars[j].week_date == diversions[k].shift_date && manpowers[i]
                                    .employee_id == diversions[k].employee_id) {
                                    color = "background-color: #ff6090; color: black;";
                                    tableAttendance +=
                                        '<td onclick="openModal(\'diversion\',\'' + manpowers[i].employee_id +
                                        '\',\'' + manpowers[i].employee_name + '\',\'' + calendars[j]
                                        .week_date + '\',\'' + manpowers[i].remark +
                                        '\')" style="width: 15px; cursor: pointer; vertical-align: top; font-size: 12px; text-align: center; ' +
                                        color + '">' +
                                        diversions[k].work_hour + '</td>';
                                    found = true;
                                }
                            }
                            if (found == false) {
                                tableAttendance +=
                                    '<td onclick="openModal(\'diversion\',\'' + manpowers[i].employee_id +
                                    '\',\'' + manpowers[i].employee_name + '\',\'' + calendars[j].week_date +
                                    '\',\'' + manpowers[i].remark +
                                    '\')" style="width: 15px; cursor: pointer; vertical-align: top; font-size: 12px; text-align: center; ' +
                                    color + '"></td>';
                            }
                        }

                        tableAttendance += '</tr>';
                    }
                    tableAttendance += '</tbody>';

                    $('#tableAttendance').append(tableAttendance);

                    table = $('#tableAttendance').DataTable({
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
                        "searching": true,
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        "ordering": false,
                        "scrollY": "500px",
                        "scrollX": true,
                        "scrollCollapse": true,
                        "paging": true,
                        "fixedColumns": {
                            left: 5,
                        },
                    });

                    $('#tableResume').DataTable({
                        'paging': false,
                        'lengthChange': false,
                        'searching': false,
                        'ordering': false,
                        'order': [],
                        'info': false,
                        'autoWidth': true,
                        "footerCallback": function(tfoot, data, start, end, display) {
                            var intVal = function(i) {
                                return typeof i === 'string' ?
                                    i.replace(/[\$,]/g, '') * 1 :
                                    typeof i === 'number' ?
                                    i : 0;
                            };
                            var api = this.api();

                            var Packing = api.column(1).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(1).footer()).html(Packing.toLocaleString());

                            var act = api.column(2).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(2).footer()).html(act.toLocaleString());

                            var wip = api.column(3).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(3).footer()).html(wip.toLocaleString());

                            var h = api.column(4).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(4).footer()).html(h.toLocaleString());

                            var sisa_sub_assy = api.column(5).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(5).footer()).html(sisa_sub_assy.toLocaleString());

                            var stamp = api.column(6).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(6).footer()).html(stamp.toLocaleString());

                            var stamp_kd = api.column(7).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(7).footer()).html(stamp_kd.toLocaleString());

                            var h2 = api.column(8).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(8).footer()).html(h2.toLocaleString());

                            var sisaH2 = api.column(9).data().reduce(function(a, b) {
                                return intVal(a) + intVal(b);
                            }, 0)
                            $(api.column(9).footer()).html(sisaH2.toLocaleString());
                        }
                    });


                    $('#tableDiversionList').DataTable().clear();
                    $('#tableDiversionList').DataTable().destroy();
                    var tableDiversionListBody = "";
                    $('#tableDiversionListBody').html("");

                    $.each(diversion_details, function(key, value) {
                        tableDiversionListBody += '<tr>';
                        tableDiversionListBody += '<td style="width: 1%; text-align: center;">' + value
                            .remark + '</td>';
                        tableDiversionListBody += '<td style="width: 1%; text-align: center;">' + value
                            .employee_id + '</td>';
                        tableDiversionListBody += '<td style="width: 4%; text-align: left;">' + value
                            .employee_name + '</td>';
                        tableDiversionListBody += '<td style="width: 1%; text-align: center;">' + value
                            .job_status + '</td>';
                        tableDiversionListBody += '<td style="width: 1%; text-align: center;">' + value
                            .position + '</td>';
                        tableDiversionListBody += '<td style="width: 1%; text-align: center;">' + value
                            .category + '</td>';
                        tableDiversionListBody += '<td style="width: 3%; text-align: center;">' + value
                            .diversion + '</td>';
                        tableDiversionListBody += '<td style="width: 1%; text-align: right;">' + value
                            .shift_date + '</td>';
                        tableDiversionListBody += '<td style="width: 1%; text-align: right;">' + value
                            .work_hour + '</td>';
                        tableDiversionListBody += '<td style="width: 3%; text-align: left;">' + value
                            .updated_by_name +
                            '</td>';
                        tableDiversionListBody +=
                            '<td style="width: 3%; text-align: center;"><button class="btn btn-danger btn-xs" id="remove_diversion2_' +
                            value.id + '" onclick="removeDiversion(\'' +
                            value.id +
                            '\')"><i class="fa fa-trash"></i> Hapus</button>&nbsp;&nbsp;&nbsp;<button class="btn btn-warning btn-xs" onclick="modalEditDiversion(\'' +
                            value.id + '\')"><i class="fa fa-edit"></i> Ubah</button></td>';
                        tableDiversionListBody += '</tr>';

                    });

                    $('#tableDiversionListBody').append(tableDiversionListBody);


                    $('#tableDiversionList tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="8"/>');
                    });

                    var table2 = $('#tableDiversionList').DataTable({
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
                        'pageLength': 20,
                        'searching': true,
                        'ordering': true,
                        'order': [],
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        initComplete: function() {
                            this.api()
                                .columns([0, 3, 4, 5, 6])
                                .every(function(dd) {
                                    var column = this;
                                    var theadname = $("#tableDiversionList th").eq([dd]).text();
                                    var select = $(
                                            '<select style="width:100%"><option value="" style="font-size:11px;">All</option></select>'
                                        )
                                        .appendTo($(column.footer()).empty())
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(this)
                                                .val());

                                            column.search(val ? '^' + val + '$' : '', true,
                                                    false)
                                                .draw();
                                        });
                                    column
                                        .data()
                                        .unique()
                                        .sort()
                                        .each(function(d, j) {
                                            var vals = d;
                                            if ($("#tableDiversionList th").eq([dd]).text() ==
                                                'Category') {
                                                vals = d.split(' ')[0];
                                            }
                                            select.append(
                                                '<option style="font-size:11px;" value="' +
                                                d + '">' + vals + '</option>');
                                        });
                                });
                        },
                    });

                    table2.columns().every(function() {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('#tableDiversionList tfoot tr').appendTo('#tableDiversionList thead');

                    $('#loading').hide();

                } else {
                    openErrorGritter('Gagal!', result.message);
                    audio_error.play();
                    $('#loading').hide();
                    return false;
                }
            });
        }

        $('#searchRemark').on('change', function() {
            table.column(0).search($(this).val()).draw();
        });
        $('#searchEmployeeId').keyup(function() {
            table.column(1).search($(this).val()).draw();
        });
        $('#searchEmployeeName').keyup(function() {
            table.column(2).search($(this).val()).draw();
        });
        $('#searchStatus').on('change', function() {
            table.column(3).search($(this).val()).draw();
        });

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
