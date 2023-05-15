@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        #history tbody>tr>td {
            cursor: pointer;
        }

        .timeline-header {
            border: 1px solid black;
        }

        .timeline-body {
            border: 1px solid black;
        }

        .time-label span {
            width: 100%;
            text-align: center;
            font-size: 1.5vw;
        }

        .crop2 {
            overflow: hidden;
        }

        .crop2 img {
            width: 100%;
            margin: -30% 0 -20% 0;
        }

        .list-group-item {
            padding-top: 5px;
            padding-bottom: 5px;
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
            border: 1px solid black;
            vertical-align: middle;
            padding: 5px;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            padding: 5px;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #fill_kaizen>thead>tr>th[class*="sort"]:after {
            content: "" !important;
        }

        #queueTable.dataTable {
            margin-top: 0px !important;
        }

        #loading,
        #error {
            display: none;
        }

        .post .user-block {
            margin-bottom: 5px
        }

        #chat {
            height: 480px;
            overflow-y: scroll;
        }

        #kz_detail_1>tbody>tr>td,
        #kz_detail_2>tbody>tr>td,
        #kz_detail_3>tbody>tr>td,
        #kz_detail_4>tbody>tr>td {
            text-align: left;
        }

        #kz_detail_1,
        #kz_detail_2,
        #kz_detail_3,
        #kz_detail_4 {
            margin-bottom: 10px
        }

        #tabelDetail>tbody>tr>td {
            text-align: left;
        }

        #tabel_Kz>tbody>tr>td {
            text-align: left;
            vertical-align: top;
            padding: 2px;
        }

        #tabel_Kz>tbody>tr>th {
            padding: 2px;
            background-color: #7e5686;
            color: white;
        }

        #tabel_nilai>tbody>tr>td {
            text-align: left;
        }

        #tabel_assess>tbody>tr>td,
        #tabel_assess>tbody>tr>th {
            text-align: center;
        }

        #tabel_assess>tbody>tr>th {
            background-color: #7e5686;
            color: white;
        }

        #tabel_nilai_all tbody>tr>th {
            text-align: center;
            background-color: #7e5686;
            color: white;
        }

        #kz_before>p>img {
            max-width: 420px;
        }

        #kz_after>p>img {
            max-width: 420px;
        }

        span>a {
            color: white
        }

        span>a:hover {
            color: white
        }
    </style>
@stop
@section('header')
    <h1>
        <span class="text-yellow">
            {{ $title }}
        </span>
        <small>
            <span style="color: #FFD700;"> {{ $title_jp }}</span>
        </small>
    </h1>
    <br>
@endsection
@section('content')
    @php
        $avatar = 'images/avatar/' . Auth::user()->avatar;
    @endphp
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content" style="padding-top: 0px;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Please wait a moment...<i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>

        <div class="row">
            <div class="col-md-4" style="padding-right: 0;">
                <div class="box box-solid">
                    <div class="box-body">
                        <div class="col-xs-5 crop2">
                            <img src="{{ url($avatar) }}" style="max-width: 100%;">
                        </div>
                        <div class="col-xs-7"
                            style="padding-left: 0; font-weight: bold; font-size: 1.2vw; padding-right: 0;">
                            <div class="row">
                                <div class="col-xs-6" style="padding-right: 1px; margin-bottom: 2px;">
                                    <a style="width: 100%;" class="btn btn-primary"
                                        href="{{ url('index/update_emp_data/' . strtoupper($emp_id)) }}">
                                        <i class="fa fa-edit"></i>&nbsp; Update Data &nbsp;<i
                                            class="fa fa-angle-double-right"></i>
                                    </a>
                                </div>
                                <div class="col-xs-6" style="padding-left: 1px; margin-bottom: 2px;">
                                    <button style="width: 100%;" class="btn btn-success" onclick="questionForm()"
                                        id="btnTanya"><i class="fa fa-question-circle"></i>&nbsp;
                                        Tanya HR &nbsp;<i class="fa fa-angle-double-right"></i></button>
                                </div>
                                <div class="col-xs-12" style="margin-bottom: 2px">
                                    <a href="{{ url('index/employee/union') }}" style=" color: white; width: 100%;"
                                        class="btn btn-danger">
                                        <i class="fa fa-certificate"></i>&nbsp; Registrasi Kepesertaan Serikat Pekerja
                                        &nbsp;<i class="fa fa-angle-double-right"></i>
                                    </a>
                                </div>
                                <div class="col-xs-6" style="padding-right: 1px; margin-bottom: 2px;">
                                    <a style="width: 100%;" href="{{ url('index/ga_control/bento') }}"
                                        class="btn btn-warning">
                                        <i class="glyphicon glyphicon-cutlery"></i>&nbsp; Bento &nbsp;<i
                                            class="fa fa-angle-double-right"></i>
                                    </a>
                                </div>
                                <div class="col-xs-6" style="padding-left: 1px; margin-bottom: 2px;">
                                    <a style="width: 100%;" href="{{ url('index/ga_control/live_cooking') }}"
                                        class="btn btn-info"><i class="glyphicon glyphicon-cutlery"></i>&nbsp; Live
                                        Cooking
                                        &nbsp;<i class="fa fa-angle-double-right"></i></a>
                                </div>
                                <?php if (str_contains($profil[0]->position, 'Operator')): ?>
                                <div class="col-xs-12" style="margin-bottom: 2px;">
                                    <button style="width: 100%;" class="btn btn-primary" onclick="ekaizen()" id="btnKaizen">
                                        <i class="fa  fa-bullhorn"></i>&nbsp; e - Kaizen &nbsp;<i
                                            class="fa fa-angle-double-right"></i>
                                    </button>
                                </div>
                                <?php endif ?>
                                <div class="col-xs-12" style="margin-bottom: 2px;">
                                    <a href="{{ url('index/perpajakan/' . strtoupper($emp_id)) }}"
                                        style="background-color: #B2FFD6; color: black; width: 100%;border: 1px solid black;"
                                        class="btn">
                                        <i class="fa fa-file-pdf-o"></i>&nbsp; Update Data NPWP 2023 &nbsp;<i
                                            class="fa fa-angle-double-right"></i>
                                    </a>
                                </div>
                                <div class="col-xs-12" style="margin-bottom: 2px;">
                                    <a href="{{ url('training_filosofi') }}"
                                        style="background-color: #555299; color: white; width: 100%;" class="btn">
                                        <i class="fa fa-users"></i>&nbsp; Training Filosofi YAMAHA &nbsp;<i
                                            class="fa fa-angle-double-right"></i>
                                    </a>
                                </div>
                                <!-- <div class="col-xs-12" style="margin-bottom: 2px;">
                                    <a href="{{ url('penambahan/anggota/keluarga') }}"
                                        style="background-color: #566573; color: white; width: 100%;" class="btn">
                                        <i class="fa fa-user-circle-o"></i>&nbsp; BPJS Kesehatan &nbsp;<i
                                            class="fa fa-angle-double-right"></i>
                                    </a>
                                </div> -->
                                <div class="col-xs-12" style="margin-bottom: 2px;">
                                    <!-- <a href="{{ url('index/competition/registration') }}" style="background-color: darkorchid;color: white;" class="btn"> -->
                                    <?php if (ISSET($certificate) && count($certificate) > 0) { ?>
                                    <a style="width: 100%;"
                                        href="{{ url('print/qa/certificate/' . $certificate[0]->certificate_id) }}"
                                        class="btn btn-default" target="_blank">
                                        <i class="fa fa-book"></i>&nbsp; Sertifikat Kensa &nbsp;<i
                                            class="fa fa-angle-double-right"></i>
                                    </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12" style="padding-top: 10px;">
                            <ul class="list-group list-group-unbordered" style="margin:0">
                                <li class="list-group-item">
                                    <b>NIK</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ strtoupper($emp_id) }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Nama</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) == 1 ? $profil[0]->name : Auth::user()->name }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Sisa Cuti</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? round($employee[0]->remaining) . ' Hari' : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Tanggal Masuk</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? date('d F Y', strtotime($profil[0]->hire_date)) : date('d F Y', strtotime(Auth::user()->created_at)) }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Posisi</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->grade_code . ' (' . $profil[0]->grade_name . ') - ' . $profil[0]->position : Auth::user()->role_code }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Division</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->division : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Department</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->department : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Section</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->section : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Group</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->group : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Sub Group</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->sub_group : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Alamat</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->address : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Telepon</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->phone : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>KTP</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->card_id : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>NPWP</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->npwp : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>JP</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->JP : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>BPJS</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->BPJS : '-' }}
                                        </span>
                                    </a>
                                </li>
                                <li class="list-group-item">
                                    <b>Serikat</b>
                                    <a class="pull-right">
                                        <span style="font-weight: bold;">
                                            {{ isset($profil[0]->name) ? $profil[0]->union : '-' }}
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="box box-solid" id="boxing">
                    <div class="box-body">
                        <span style="font-weight: bold; font-size: 16px;">Resume Kehadiran</span>
                        <div class="pull-right" style="padding-bottom: 5px;">
                            <select class="form-control select2" onchange="get_data(this)" data-placeholder='tahun'>
                                <option></option>
                                <option <?php if (isset($_GET['tahun']) && $_GET['tahun'] == '2020') {
                                    echo 'selected';
                                } ?>>2020</option>
                                <option <?php if (isset($_GET['tahun']) && $_GET['tahun'] == '2021') {
                                    echo 'selected';
                                } ?>>2021</option>
                                <option <?php if (isset($_GET['tahun']) && $_GET['tahun'] == '2022') {
                                    echo 'selected';
                                } ?>>2022</option>
                                <option <?php if (isset($_GET['tahun']) && $_GET['tahun'] == '2023') {
                                    echo 'selected';
                                } ?>>2023</option>
                            </select>
                        </div>
                        <table class="table table-bordered table-striped" id="history">
                            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
                                <tr>
                                    <th style="width: 10%; vertical-align: middle;">Periode</th>
                                    <th style="width: 10%; vertical-align: middle;">Mangkir</th>
                                    <th style="width: 10%; vertical-align: middle;">Izin</th>
                                    <th style="width: 10%; vertical-align: middle;">Sakit</th>
                                    <th style="width: 10%; vertical-align: middle;">Terlambat</th>
                                    <th style="width: 10%; vertical-align: middle;">Pulang Cepat</th>
                                    <th style="width: 10%; vertical-align: middle;">Cuti</th>
                                    <th style="width: 10%; vertical-align: middle;">Tunjangan Disiplin</th>
                                    <th style="width: 10%; vertical-align: middle;">Lembur <br>Jam (Satuan)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($presences))
                                    @foreach ($presences as $presence)
                                        <tr>
                                            <td>{{ date('M Y', strtotime($presence['periode'])) }}</td>
                                            <td onclick="cek('Mangkir','{{ $presence['periode'] }}')">
                                                @if ($presence['mangkir'] > 0)
                                                    <span class="badge bg-yellow"><a
                                                            href="javascript:void(0)">{{ $presence['mangkir'] }}</a></span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td onclick="cek('Izin','{{ $presence['periode'] }}')">
                                                @if ($presence['izin'] > 0)
                                                    <span class="badge bg-yellow"><a
                                                            href="javascript:void(0)">{{ $presence['izin'] }}</a></span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td onclick="cek('Sakit','{{ $presence['periode'] }}')">
                                                @if ($presence['sakit'] > 0)
                                                    <span class="badge bg-yellow"><a
                                                            href="javascript:void(0)">{{ $presence['sakit'] }}</a></span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td onclick="cek('Terlambat','{{ $presence['periode'] }}')">
                                                @if ($presence['terlambat'] > 0)
                                                    <span class="badge bg-yellow"><a
                                                            href="javascript:void(0)">{{ $presence['terlambat'] }}</a></span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td onclick="cek('Pulang Cepat','{{ $presence['periode'] }}')">
                                                @if ($presence['pulang_cepat'] > 0)
                                                    <span class="badge bg-yellow"><a
                                                            href="javascript:void(0)">{{ $presence['pulang_cepat'] }}</a></span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td onclick="cek('Cuti','{{ $presence['periode'] }}')">
                                                @if ($presence['cuti'] > 0)
                                                    <span class="badge bg-yellow"><a
                                                            href="javascript:void(0)">{{ $presence['cuti'] }}</a></span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if ($presence['tunjangan'] > 0)
                                                    <i class="fa fa-close" style="color: red"></i>
                                                @else
                                                    <i class="fa fa-check" style="color: #18c40c"></i>
                                                @endif
                                            </td>
                                            <td onclick="cek('Overtime','{{ $presence['periode'] }}')">
                                                @if ($presence['overtime'] > 0)
                                                    <span class="badge bg-yellow">{{ round($presence['overtime'], 2) }}
                                                        ({{ round($presence['satuan_ot'], 2) }})
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="9" style="text-align: right; color: red;">Data lembur hanya yang telah
                                        disetujui secara penuh.</td>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="col-md-12" style="padding-top: 10px;">
                            <ul class="timeline">
                                <li class="time-label">
                                    <span style="background-color: blue; color: white;">
                                        01 Februari 2021
                                    </span>
                                </li>
                                <li>
                                    <i class="fa fa-info-circle" style="background-color: blue; color: white;"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header" style="color: blue; font-weight: bold;">Informasi
                                            Terkait Kebijakan Perusahaan</h3>
                                        <div class="timeline-body">
                                            <a target="_blank"
                                                href="{{ url('files/info/2016 - Aturan Penyimpanan Kunci Kantor dan Ruang Produksi.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> 2016 - Aturan Penyimpanan
                                                Kunci
                                                Kantor dan Ruang Produksi <i class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.003 - Pengaturan PKWT untuk Karyawan yang Pensiun Normal Usia 55 Tahun.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.003 - Pengaturan PKWT
                                                untuk Karyawan yang Pensiun Normal Usia 55 Tahun <i
                                                    class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.007 - Ketentuan Tambahan Manfaat Rawat Inap Jaminan Kesehatan.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.007 - Ketentuan
                                                Tambahan
                                                Manfaat Rawat Inap Jaminan Kesehatan <i
                                                    class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.009 - Ketentuan Grade, Gol.Pekerjaan, Jenis Pekerjaan dan Jabatan.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.009 - Ketentuan Grade,
                                                Gol.Pekerjaan, Jenis Pekerjaan dan Jabatan <i
                                                    class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.010 - Ketentuan Promosi Grade dan Golongan Pekerjaan.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.010 - Ketentuan
                                                Promosi
                                                Grade dan Golongan Pekerjaan <i class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.014 - Ketentuan Tunjangan Jabatan.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.014 - Ketentuan
                                                Tunjangan
                                                Jabatan <i class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.016 - Ketentuan Sistem Pendidikan dan Pelatihan.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.016 - Ketentuan Sistem
                                                Pendidikan dan Pelatihan <i class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.019 - Ketentuan Hari Kerja dan Waktu Kerja.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.019 - Ketentuan Hari
                                                Kerja
                                                dan Waktu Kerja <i class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.020 - Ijin Meninggalkan Tempat Kerja dan Istirahat Bergilir Bagi Karyawan.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.020 - Ijin
                                                Meninggalkan
                                                Tempat Kerja dan Istirahat Bergilir Bagi Karyawan <i
                                                    class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.021 - Ketentuan Perjalanan Dinas.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.021 - Ketentuan
                                                Perjalanan
                                                Dinas <i class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.032 - Penggunaan Fasilitas Tempat Parkir (Mobil) R2.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.032 - Penggunaan
                                                Fasilitas
                                                Tempat Parkir (Mobil) R2 <i class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            <a target="_blank"
                                                href="{{ url('files/info/SKD No.035 - Ketentuan Fasilitas Kendaraan Antar Jemput Karyawan.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> SKD No.035 - Ketentuan
                                                Fasilitas
                                                Kendaraan Antar Jemput Karyawan <i class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="time-label">
                                    <span style="background-color: red; color: white;">
                                        20 April 2020
                                    </span>
                                </li>
                                <li>
                                    <i class="fa fa-info-circle" style="background-color: red; color: white;"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header" style="color: red; font-weight: bold;">Informasi
                                            Terkait Pelanggaran Kode Etik Power Harassment (Pelecehan Kekuasaan)</h3>
                                        <div class="timeline-body">
                                            Surat informasi dapat didownload melalui link di bawah ini:
                                            <br>
                                            <a href="{{ asset('\files\info\Pengumuman_Kasus_COC.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> Pengumuman Kasus COC <i
                                                    class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            Buku kepatuhan kode etik karyawan dapat didownload melalui link di bawah
                                            ini:
                                            <br>
                                            <a href="{{ asset('\files\info\Kode_Etik_Kepatuhan_rev4.pdf') }}"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> Kode Etik Kepatuhan Rev4.0 <i
                                                    class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                        </div>
                                    </div>
                                </li>
                                <li class="time-label">
                                    <span style="background-color: #00a65a; color: white;">
                                        24 January 2019
                                    </span>
                                </li>
                                <li>
                                    <i class="fa fa-info-circle" style="background-color: #00a65a; color: white;"></i>
                                    <div class="timeline-item">
                                        <h3 class="timeline-header" style="color: #00a65a; font-weight: bold;">Yamaha
                                            Group Helpline</h3>
                                        <div class="timeline-body">
                                            Karyawan dapat menyampaikan informasi terkait tindakan ketidaksesuaian
                                            terhadap
                                            Kode Etik Kepatuhan (Compliance Code of Conduct) pada link berikut:
                                            <br>
                                            <a href="http://ml.helpline.jp/yamahacompliance/"><i
                                                    class="fa fa-angle-double-right"></i><i
                                                    class="fa fa-angle-double-right"></i> Link Yamaha Helpline <i
                                                    class="fa fa-angle-double-left"></i><i
                                                    class="fa fa-angle-double-left"></i></a>
                                            <br>
                                            Username: <b>yamaha</b>
                                            <br>
                                            Password: <b>helpline</b>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <i class="fa fa-dot-circle-o bg-gray"></i>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


                <div class="box box-solid" id="question" style="display: none;">
                    <div class="box-header">
                        <button class="btn btn-default" onclick="kembali()" style="width: 20%;" id="btnKembali">
                            <i class="fa fa-angle-double-left"></i>&nbsp; Kembali
                        </button>
                    </div>
                    <div class="box-body">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2">
                                    <select class="form-control select2" style="width: 100%" id="category">
                                        <option disabled selected value="">Category</option>
                                        <option value="YMPI CO ID">YMPI CO ID</option>
                                        {{-- <option value="Great Day">Great Day</option> --}}
                                        <option value="Absensi">Absensi</option>
                                        <option value="Lembur">Lembur</option>
                                        <option value="Cuti">Cuti</option>
                                        <option value="PKB">PKB</option>
                                        <option value="Penggajian">Penggajian</option>
                                        <option value="BPJS Kes">BPJS Kes</option>
                                        <option value="BPJS TK">BPJS TK</option>
                                    </select>
                                </div>
                                <div class="col-xs-10">
                                    <div class="input-group input-group">
                                        <input type="text" class="form-control" id="msg"
                                            placeholder="Write a Message...">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-success btn-flat"
                                                onclick="posting()"><i class="fa fa-send-o"></i>&nbsp; Post</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <hr>
                            <div id="chat">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-solid" id="kaizen" style="display: none;">
                    <?php if(isset($profil[0]->position)){ 
                                               if(str_contains($profil[0]->position, 'Operator')) { ?>
                    <div class="box-header">
                        <h3 class="box-title">E-Kaizen</h3>
                        <?php
                        if (isset($profil[0]->group)) {
                            $grp = str_replace('/', ' ', $profil[0]->group);
                        } else {
                            $grp = '-';
                        }
                        ?>
                        <a class="btn btn-primary pull-right"
                            href="{{ url('create/ekaizen/' . $emp_id . '/' . $profil[0]->name . '/' . $profil[0]->section . '/' . $grp) }}"><i
                                class="fa fa-bullhorn"></i>&nbsp; Buat Kaizen</a>
                    </div>
                    <?php } } ?>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-1">
                                <label>Filter :</label>
                            </div>
                            <div class="col-xs-4">
                                <input type="text" id="bulanAwal" class="form-control datepicker"
                                    placeholder="Tanggal dari..">
                            </div>
                            <div class="col-xs-4">
                                <input type="text" id="bulanAkhir" class="form-control datepicker"
                                    placeholder="Tanggal sampai..">
                            </div>
                            <div class="col-xs-2">
                                <button class="btn btn-default" onclick="fill_kaizen()">Cari</button>
                            </div>
                        </div>
                        <hr>
                        <table class="table table-bordered" id="tableKaizen" width="100%">
                            <thead style="background-color: rgb(126,86,134); color: #FFD700;">
                                <tr>
                                    <th style="width: 900px">Id</th>
                                    <th>Tanggal</th>
                                    <th>Usulan</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Aplikasi</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <font style="color: red">* NB : Jika kategori "Terdapat Catatan" maka terdapat catatan dari
                            Foreman
                            / Manager, tekan tombol "details" untuk melihat catatan dan lakukan perubahan pada kaizen
                            teian
                        </font>
                    </div>
                </div>
            </div>
        </div>
        </div>

        <div class="modal fade" id="modalDetail">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <p style="font-size: 25px; font-weight: bold; text-align: center" id="kz_title"></p>
                                <table id="tabelDetail" width="100%">
                                    <tr>
                                        <th>NIK/Name </th>
                                        <td> : </td>
                                        <td id="kz_nik"></td>
                                        <th>Date</th>
                                        <td> : </td>
                                        <td id="kz_tanggal"></td>
                                    </tr>
                                    <tr>
                                        <th>Section</th>
                                        <td> : </td>
                                        <td id="kz_section"></td>
                                        <th>Area Kaizen</th>
                                        <td> : </td>
                                        <td id="kz_area"></td>
                                    </tr>
                                    <tr>
                                        <th>Leader</th>
                                        <td> : </td>
                                        <td id="kz_leader"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6">
                                            <hr style="margin: 5px 0px 5px 0px; border-color: black">
                                        </td>
                                    </tr>
                                </table>
                                <table width="100%" border="1" id="tabel_Kz">
                                    <tr>
                                        <th style="border-bottom: 1px solid black" width="50%">BEFORE :</th>
                                        <th style="border-bottom: 1px solid black; border-left: 1px" width="50%">AFTER
                                            :</th>
                                    </tr>
                                    <tr>
                                        <td id="kz_before"></td>
                                        <td id="kz_after"></td>
                                    </tr>
                                </table>
                                <table id="tableEstimasi" style="border: 1px solid black" width="100%"></table>
                                <table width="100%" id="tabel_note">
                                    <tr>
                                        <th colspan="2">Note :</th>
                                    </tr>
                                    <tr>
                                        <th style="border: 1px solid black;" width="50%">Foreman</th>
                                        <th style="border: 1px solid black;" width="50%">Manager</th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left; border: 1px solid" id="note_foreman"></td>
                                        <td style="text-align: left; border: 1px solid" id="note_manager"></td>
                                    </tr>
                                </table>
                                <br>
                                <table width="100%" border="1" id="tabel_assess">
                                    <tr>
                                        <th colspan="4">TABEL NILAI KAIZEN</th>
                                    </tr>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Kategori</th>
                                        <th>Foreman / Chief</th>
                                        <th>Manager</th>
                                    </tr>
                                    <tr>
                                        <th>1</th>
                                        <th>Estimasi Hasil</th>
                                        <td id="foreman_point1"></td>
                                        <td id="manager_point1"></td>
                                    </tr>
                                    <tr>
                                        <th>2</th>
                                        <th>Ide</th>
                                        <td id="foreman_point2"></td>
                                        <td id="manager_point2"></td>
                                    </tr>
                                    <tr>
                                        <th>3</th>
                                        <th>Implementasi</th>
                                        <td id="foreman_point3"></td>
                                        <td id="manager_point3"></td>
                                    </tr>
                                    <tr>
                                        <th colspan="2"> TOTAL</th>
                                        <td id="foreman_total" style="font-weight: bold;"></td>
                                        <td id="manager_total" style="font-weight: bold;"></td>
                                    </tr>
                                </table>
                                <br>
                                <table width="100%" id="tabel_nilai_all" border="1">
                                    <tr>
                                        <th>No</th>
                                        <th>Total Nilai</th>
                                        <th>Point</th>
                                        <th>Keterangan</th>
                                        <th>Reward Aplikasi</th>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <300< /td>
                                        <td>2</td>
                                        <td>Kurang</td>
                                        <td>Rp 2.000,-</td>
                                    </tr>

                                    <tr>
                                        <td>2</td>
                                        <td>300 - 350</td>
                                        <td>4</td>
                                        <td>Cukup</td>
                                        <td>Rp 5.000,-</td>
                                    </tr>

                                    <tr>
                                        <td>3</td>
                                        <td>351 - 400</td>
                                        <td>6</td>
                                        <td>Baik</td>
                                        <td>Rp 10.000,-</td>
                                    </tr>

                                    <tr>
                                        <td>4</td>
                                        <td>401 - 450</td>
                                        <td>8</td>
                                        <td>Sangat Baik</td>
                                        <td>Rp 25,000,-</td>
                                    </tr>

                                    <tr>
                                        <td>5</td>
                                        <td>> 450</td>
                                        <td>10</td>
                                        <td>Potensi Excellent</td>
                                        <td>Rp 50,000,-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal"><i
                                class="fa fa-close"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDelete">
            <div class="modal-dialog modal-md modal-danger">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title"><b>Apakah anda yakin ingin menghapus kaizen ?</b></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p id="kz_title_delete"></p>
                                <input type="hidden" id="id_delete">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success pull-left" data-dismiss="modal"
                            onclick="deleteKaizen()"><i class="fa fa-close"></i> YES</button>
                        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i
                                class="fa fa-close"></i> NO</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalAbsenceDetail">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">

                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered">
                                    <thead style="background-color: rgb(126,86,134); color: #FFD700;">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Cek Log Masuk</th>
                                            <th>Cek Log Pulang</th>
                                            <th>Keterangan</th>
                                        </tr>
                                        <tr id="laoding_absence">
                                            <th colspan="4"><i class="fa fa-spinner fa-pulse"></i> Loading</th>
                                        </tr>
                                    </thead>
                                    <tbody id="body_absence"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalBerita">
            <div class="modal-dialog modal-lg modal-default" style="width: 80%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <center>
                            <h4 class="modal-title"><b>YMPI Announcement</b></h4>
                        </center>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <div class="item active">
                                            <center>
                                                <img class="img-responsive"
                                                    src="{{ url('images/update_data_npwp.jpg') }}" alt="...">
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i
                                class="fa fa-close"></i> Close</button>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var chat = 0;
        var name = "";

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.select2').select2({
                language: {
                    noResults: function(params) {
                        return "There is no data";
                    }
                }
            });

            if ("{{ isset($profil[0]->name) }}" == 1) {
                name = "{{ $profil[0]->name }}";
            } else {
                name = '';
            }

            name = name.replace('&#039;', '');

            $("#phone_number").val("{{ isset($profil[0]->phone) }}");
            $("#wa_number").val("{{ isset($profil[0]->phone) }}");

            fill_chat();

            $('.datepicker').datepicker({
                autoclose: true,
                format: 'yyyy-mm-dd',
            })
        });

        function get_data(elem) {
            var thn = $(elem).val();
            window.location.href = '{{ url('index/employee/service?id=1') }}&tahun=' + thn;
            $("#loading").show();
        }

        $(window).on('pageshow', function() {
            $("#bulanAwal").val("");
            $("#bulanAkhir").val("");
            fill_kaizen();
        });

        function check_chart() {
            if (!$(".komen").is(':focus') && chat == 1) {
                fill_chat();
            }
        }

        function fill_chat() {
            var data = {
                employee_id: '{{ $emp_id }}_' + name.split(' ').slice(0, 2).join('-')
            }

            $.get('{{ url('fetch/chat/hrqa') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $("#chat").empty();
                    var xCategories2 = [];

                    for (var i = 0; i < result.chats.length; i++) {
                        ctg = result.chats[i].id + "_" + result.chats[i].message + "_" + result.chats[i].category +
                            "_" + result.chats[i].created_at_new;

                        if (xCategories2.indexOf(ctg) === -1) {
                            xCategories2[xCategories2.length] = ctg;
                        }
                    }


                    $.each(xCategories2, function(index, value) {
                        var chat_history = "";
                        var chats = value.split("_");
                        chat_history += '<div class="post">';
                        chat_history += '<div class="user-block">'
                        chat_history += '<img class="img-circle img-bordered-sm" src="' + result
                            .base_avatar + '/{{ $emp_id }}.jpg" alt="image">';
                        chat_history += '<span class="username">{{ $emp_id }}_' + name.split(' ')
                            .slice(0, 2).join('-') + '</span>';
                        chat_history += '<span class="description">' + chats[3] + '</span></div>';
                        chat_history += '<p>' + chats[1] + '</p>';

                        var stat = 0;
                        var rev = 0;

                        $.each(result.chats, function(index2, value2) {
                            if (chats[0] == value2.id) {
                                if (value2.message_detail) {
                                    if (stat == 0) {
                                        chat_history += '<div style="margin-left: 30px">';
                                    } else {
                                        chat_history += '<div>';
                                    }

                                    chat_history += '<div class="post">'
                                    chat_history += '<div class="user-block">';
                                    chat_history +=
                                        '<img class="img-circle img-bordered-sm" src="' + result
                                        .base_avatar + '/' + value2.avatar + '.jpg" alt="image">';
                                    chat_history += '<span class="username">' + value2.dari +
                                        ' &nbsp; ';
                                    chat_history += '<span style="color:#999; font-size:13px">' +
                                        value2.created_at_new + '</span></span>';
                                    chat_history +=
                                        '<span class="description" style="color:#666">' + value2
                                        .message_detail + '</span></div>';
                                    // chat_history += '<p>'+value2.message_detail+'</p>';

                                    stat = 1;

                                    if (typeof result.chats[index2 + 1] === 'undefined') {
                                        rev = 1;
                                        chat_history +=
                                            '<input class="form-control input-sm komen" type="text" placeholder="Type a comment" id="comment_' +
                                            value2.id + '"></div>';
                                    } else {
                                        if (result.chats[index2].id != result.chats[index2 + 1]
                                            .id) {
                                            rev = 1;
                                            chat_history +=
                                                '<input class="form-control input-sm komen" type="text" placeholder="Type a comment" id="comment_' +
                                                value2.id + '"></div>';
                                        }
                                    }
                                } else {
                                    if (rev == 0) {
                                        chat_history +=
                                            '<input class="form-control input-sm komen" type="text" placeholder="Type a comment" id="comment_' +
                                            value2.id + '">';
                                    }
                                }
                            }

                        })
                        chat_history += '</div>';

                        $("#chat").append(chat_history);
                    })

                    $(".komen").keypress(function() {
                        var keycode = (event.keyCode ? event.keyCode : event.which);
                        if (keycode == '13') {
                            if (this.value != "") {
                                var id2 = this.id.split("_")[1];
                                var data = {
                                    id: id2,
                                    message: this.value,
                                    from: "{{ $emp_id }}_" + name.split(' ').slice(0, 2).join(
                                        '-')
                                }

                                $.post('{{ url('post/chat/comment') }}', data, function(result, status,
                                    xhr) {
                                    fill_chat();
                                })
                            } else {
                                alert('Komentar tidak boleh kosong');
                            }
                        }
                    });
                }
            })
        }

        function posting() {
            var msg = $("#msg").val();
            var cat = $("#category").val();

            if (msg == "" && cat == "") {
                openErrorGritter('Error!', 'Pesan harus diisi');
                return false;
            }

            var data = {
                message: msg,
                category: cat,
                from: "{{ $emp_id }}_" + name.split(' ').slice(0, 2).join('-')
            }

            $.post('{{ url('post/hrqa') }}', data, function(result, status, xhr) {
                openSuccessGritter('Success', '');
                $("#msg").val("");
                fill_chat();
            })
        }

        function fill_kaizen() {
            if ($("#bulanAwal").val() != "" && $("#bulanAkhir").val() == "") {
                alert("Bulan Sampai harap diisi");
                return false;
            } else if ($("#bulanAwal").val() == "" && $("#bulanAkhir").val() != "") {
                alert("Bulan Dari harap diisi");
                return false;
            }

            var data = {
                employee_id: "{{ $emp_id }}",
                bulanAwal: $("#bulanAwal").val(),
                bulanAkhir: $("#bulanAkhir").val(),
            }
            $('#tableKaizen').DataTable().destroy();
            var table2 = $('#tableKaizen').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'order': [],
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "type": "get",
                    "url": "{{ url('fetch/report/kaizen') }}",
                    "data": data
                },
                "columns": [{
                        "data": "id"
                    },
                    {
                        "data": "propose_date"
                    },
                    {
                        "data": "title"
                    },
                    {
                        "data": "stat"
                    },
                    {
                        "data": "posisi"
                    },
                    {
                        "data": "application"
                    },
                    {
                        "data": "action"
                    }
                ],
            });

            $('#tableKaizen tfoot tr').appendTo('#tableKaizen thead');
        }

        function cekDetail(id) {
            data = {
                id: id
            }

            $.get('{{ url('fetch/kaizen/detail') }}', data, function(result) {
                $("#kz_title").text(result.datas[0].title);
                $("#kz_nik").text(result.datas[0].employee_id + " / " + result.datas[0].employee_name);
                $("#kz_section").text(result.datas[0].section);
                $("#kz_leader").text(result.datas[0].leader_name);
                $("#kz_tanggal").text(result.datas[0].date);
                $("#kz_area").text(result.datas[0].area);
                $("#kz_before").html(result.datas[0].condition);
                $("#kz_after").html(result.datas[0].improvement);
                $("#note_foreman").html(result.datas[0].foreman_note);
                $("#note_manager").html(result.datas[0].manager_note);

                $("#tableEstimasi").empty();
                bd = "";
                tot = 0;
                if (result.datas[0].cost_name) {
                    $.each(result.datas, function(index, value) {
                        bd += "<tr>";
                        var unit = "";

                        if (value.cost_name == "Manpower") {
                            unit = "menit";
                            sub_tot = (value.sub_total_cost * 20);
                            tot += parseInt(sub_tot);
                        } else if (value.cost_name == "Tempat") {
                            unit = value.unit + "<sup>2</sup>";
                            sub_tot = parseInt(value.sub_total_cost);
                            tot += sub_tot;
                        } else {
                            unit = value.frequency;
                            sub_tot = value.sub_total_cost;
                            tot += parseInt(sub_tot);
                        }

                        bd += "<th>" + value.cost_name + "</th>";
                        bd += "<td><b>" + value.cost + "</b> " + unit + " X <b>Rp " + value.std_cost +
                            ",-</b></td>";
                        bd += "<td><b>Rp " + sub_tot + ",- / bulan</b></td>";
                        bd += "</tr>";
                    });

                    bd += "<tr style='font-size: 18px;'>";
                    bd += "<th colspan='2' style='text-align: right;padding-right:5px'>Total</th>";
                    bd += "<td><b>Rp " + tot + ",-</b></td>";
                    bd += "</tr>";

                    $("#tableEstimasi").append(bd);
                }

                $("#foreman_point1").text(result.datas[0].foreman_point_1 * 40);
                $("#foreman_point2").text(result.datas[0].foreman_point_2 * 30);
                $("#foreman_point3").text(result.datas[0].foreman_point_3 * 30);
                $("#foreman_total").text((result.datas[0].foreman_point_1 * 40) + (result.datas[0].foreman_point_2 *
                    30) + (result.datas[0].foreman_point_3 * 30));
                $("#manager_point1").text(result.datas[0].manager_point_1 * 40);
                $("#manager_point2").text(result.datas[0].manager_point_2 * 30);
                $("#manager_point3").text(result.datas[0].manager_point_3 * 30);
                $("#manager_total").text((result.datas[0].manager_point_1 * 40) + (result.datas[0].manager_point_2 *
                    30) + (result.datas[0].manager_point_3 * 30));
                $("#modalDetail").modal('show');
            })
        }

        function load_leader() {
            $.get('{{ url('fetch/sub_leader') }}', function(result, status, xhr) {

                fill_chat();
            })
        }

        function cek(kode, period) {
            var data = {
                attend_code: kode,
                period: period
            }
            $("#modalAbsenceDetail").modal('show');
            $("#laoding_absence").show();
            $("#body_absence").empty();

            $.get('{{ url('fetch/absence/employee') }}', data, function(result, status, xhr) {
                $("#laoding_absence").hide();
                var body = "";

                $.each(result.datas, function(index2, value2) {
                    body += "<tr>";
                    body += "<td>" + value2.tanggal + "</td>";
                    body += "<td>" + value2.starttime + "</td>";
                    body += "<td>" + value2.endtime + "</td>";
                    body += "<td>" + value2.Attend_Code + "</td>";
                    body += "</tr>";
                })

                $("#body_absence").append(body);

            })
        }

        function deleteKaizen() {
            var ids = $("#id_delete").val();

            var data = {
                id: ids
            }

            $.get('{{ url('delete/kaizen') }}', data, function(result, status, xhr) {
                openSuccessGritter('Success', 'Kaizen Teian berhasil dihapus..');
                fill_kaizen();
            })
        }

        function openDeleteDialog(id, title, date) {
            $('#modalDelete').modal({
                backdrop: 'static',
                keyboard: false
            })

            $("#kz_title_delete").text('"' + title + '" ?');
            $("#id_delete").val(id);
        }

        function questionForm() {
            $("#boxing").hide();
            $("#question").show();
            // $("#btnTanya").hide();
            // $("#btnKembali").show();
            $("#btnKaizen").hide();
            chat = 1;
        }

        function kembali() {
            $("#boxing").show();
            $("#question").hide();
            $("#kaizen").hide();
            // $("#btnKembali").hide();
            $("#btnTanya").show();
            $("#btnKaizen").show();
            chat = 0;
        }

        function ekaizen() {
            $("#boxing").hide();
            $("#kaizen").show();
            // $("#btnTanya").hide();
            $("#btnKaizen").hide();
            // $("#btnKembali").show();
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
