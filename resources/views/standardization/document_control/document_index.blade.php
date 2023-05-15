@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">
    <style type="text/css">
        .table>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        table.table-bordered {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            vertical-align: middle;
            height: 30px;
            padding: 2px 5px 2px 5px;
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

        #loading {
            display: none;
        }
        html {
          scroll-behavior: smooth;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        @foreach (Auth::user()->role->permissions as $perm)
            @php
                $navs[] = $perm->navigation_code;
            @endphp
        @endforeach
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
            <!-- <a href="{{ url('index/standardization/document_publish') }}" class="btn btn-success pull-right"
                style="margin-left: 5px; width: 15%; background-color: #BA55D3; border-color: black;"> Penerbitan IK DM DL <i
                    class="fa fa-file-pdf-o"></i></a> -->
            @if (in_array('A15', $navs))
                <button class="btn btn-success pull-right"
                    style="margin-left: 5px; width: 10%; background-color: #BA55D3; border-color: black;"
                    onclick="modalCreate();"> Baru <i class="fa fa-file-pdf-o"></i></button>
            @endif
        </h1>
    </section>
@endsection

@section('content')
    <section class="content" style="font-size: 0.9vw;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row">
            <input type="hidden" id="document_id">
            <input type="hidden" id="category">
            <input type="hidden" id="document_category">
            <div class="col-xs-4" style="padding-right: 5px">
                <div class="box box-solid" style="border: 2px solid #3c8dbc">
                    <div class="box-body">
                        <div class="col-xs-12" style="font-weight: normal; font-size: 1.0vw;padding: 0px;">
                            <span style="font-weight: normal; font-size: 1.2vw;font-weight: bold;">Instruksi Kerja (IK)</span><br>
                            <div class="col-xs-6" style="text-align: left;padding: 0px;">
                                Jumlah IK Active - <span
                                style="font-weight: bold;font-style: italic;" id="total_ik_active">1098</span>
                            </div>
                            <div class="col-xs-6" style="text-align: right;padding: 0px;">
                                Jumlah IK Obsolete - <span
                                style="font-weight: bold;font-style: italic;" id="total_ik_obsolete">1098</span>
                            </div>
                        </div>
                        <div class="col-xs-12 no-padding">
                            <div id="container1" style="height: 30vh;width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-4" style="padding-left: 5px;padding-right: 5px;">
                <div class="box box-solid" style="border: 2px solid #00a65a">
                    <div class="box-body">
                        <div class="col-xs-12" style="font-weight: normal; font-size: 1.0vw;padding: 0px;">
                            <div class="col-xs-12" style="padding-left: 0px;padding-right: 0px;">
                                <span style="font-weight: normal; font-size: 1.2vw;font-weight: bold;">Dokumen Mutu (DM)</span>
                            </div>
                            
                            <br>
                            <div class="col-xs-6" style="text-align: left;padding: 0px;">
                                Jumlah DM Active - <span
                                style="font-weight: bold;font-style: italic;" id="total_dm_active">1098</span>
                            </div>
                            <div class="col-xs-6" style="text-align: right;padding: 0px;">
                                Jumlah DM Obsolete - <span
                                style="font-weight: bold;font-style: italic;" id="total_dm_obsolete">1098</span>
                            </div>
                        </div>
                        <div class="col-xs-12 no-padding">
                            <div id="container2" style="height: 30vh;width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-4" style="padding-left: 5px;">
                <div class="box box-solid" style="border: 2px solid #f39c12">
                    <div class="box-body">
                        <div class="col-xs-12" style="font-weight: normal; font-size: 1.0vw;padding: 0px;">
                            <span style="font-weight: normal; font-size: 1.2vw;font-weight: bold;">Dokumen Lingkungan (DL)</span><br>
                            <div class="col-xs-6" style="text-align: left;padding: 0px;">
                                Jumlah DL Active - <span
                                style="font-weight: bold;font-style: italic;" id="total_dl_active">1098</span>
                            </div>
                            <div class="col-xs-6" style="text-align: right;padding: 0px;">
                                Jumlah DL Obsolete - <span
                                style="font-weight: bold;font-style: italic;" id="total_dl_obsolete">1098</span>
                            </div>
                        </div>
                        <div class="col-xs-12 no-padding">
                            <div id="container3" style="height: 30vh;width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12" id="divDetail">
                <div class="box box-solid">
                    <div class="box-body">
                        <table style="width: 100%;">
                            <tbody>
                                <tr style="">
                                    <td style="text-align: center; width: 0.1%; font-weight: bold;" rowspan="3"><img
                                            src="{{ asset('images/catatan_mutu.jpg') }}" style="height: 50px;">
                                    </td>
                                    <td style="text-align: center; width: 5%; font-weight: bold;" rowspan="3">CATATAN
                                        INDUK DAN MATRIK DISTRIBUSI DOKUMEN</td>
                                    <td style="text-align: right; width: 0.5%;">Dokumen No</td>
                                    <td style="text-align: center; width: 0.1%;">:</td>
                                    <td style="text-align: left; width: 0.5%;">YMPI/PGA/FL/024</td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: right; width: 0.5%;">Revisi No</td>
                                    <td style="text-align: center; width: 0.1%;">:</td>
                                    <td style="text-align: left; width: 0.5%;">08</td>
                                </tr>
                                <tr style="">
                                    <td style="text-align: right; width: 0.5%;">Tanggal</td>
                                    <td style="text-align: center; width: 0.1%;">:</td>
                                    <td style="text-align: left; width: 0.5%;">1 September 2004</td>
                                </tr>
                            </tbody>
                        </table>
                        <hr style="margin-top: 10px; margin-bottom: 10px;">
                        <button class="btn btn-success pull-right" style="margin-bottom: 5px;font-weight: bold;" onclick="fetchData();">Reset Filter</button>
                        <div class="pull-right" style="padding: 0px;" id="button_dm">
                            
                        </div>
                        <table id="tableDocument" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #9932CC; color: white;" id="tableDocumentHead">
                            </thead>
                            <tbody id="tableDocumentBody">
                            </tbody>
                            <tfoot id="tableDocumentFoot">
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalCreate" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #BA55D3; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Tambahkan Dokumen Baru<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Departemen<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" id="createDepartment"
                                            data-placeholder="Select Department" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($departments as $department)
                                                <option
                                                    value="{{ $department->department_name }}||{{ $department->department_shortname }}">
                                                    {{ $department->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Kategori<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <a href="javascript:void(0)" style="border-color: black; color: black;"
                                            id="btn_IK" onclick="btnCategory('IK')" class="btn btn-sm">IK - Instruksi
                                            Kerja</a>
                                        <a href="javascript:void(0)" style="border-color: black; color: black;"
                                            id="btn_DM" onclick="btnCategory('DM')" class="btn btn-sm">DM - Dokumen
                                            Mutu</a>
                                        <a href="javascript:void(0)" style="border-color: black; color: black;"
                                            id="btn_DL" onclick="btnCategory('DL')" class="btn btn-sm">DL - Dokumen
                                            Lingkungan</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Detail Kategori<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_1" onclick="btnCategory2('Instruksi Kerja',this.id)" class="btn btn-sm">Instruksi Kerja</a>
                                        <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_2" onclick="btnCategory2('Dokumen Lingkungan',this.id)" class="btn btn-sm">Dokumen
                                            Lingkungan</a>
                                        <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_3" onclick="btnCategory2('QC Koteihyo',this.id)" class="btn btn-sm">QC Koteihyo</a>
                                        <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_4" onclick="btnCategory2('Procedure',this.id)" class="btn btn-sm">Procedure</a>
                                        <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_5" onclick="btnCategory2('Spec Product',this.id)" class="btn btn-sm">Spec Product</a>
                                            <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_6" onclick="btnCategory2('Koteizu',this.id)" class="btn btn-sm">Koteizu</a>
                                            <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_7" onclick="btnCategory2('Parameter Process',this.id)" class="btn btn-sm">Parameter Process</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">No.
                                        Dokumen<span class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="Enter Document Number"
                                            id="createDocumentNumber">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Judul
                                        Dokumen<span class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Document Title"
                                            id="createTitle">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">No.
                                        Revisi<span class="text-red">*</span> :</label>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <input type="text" class="numpad form-control" placeholder="Revise"
                                                id="createVersion">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Tanggal<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control datepicker" id="createVersionDate"
                                            placeholder="   Select Date">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Status<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <select class="form-control select2" id="createStatus"
                                            data-placeholder="Select Status" style="width: 100%;">
                                            <option value="Active">Active</option>
                                            <option value="Obsolete">Obsolete</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">PDF<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <input type="file" id="createAttachmentPDF">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">EXCEL<span class="text-red"></span> :</label>
                                    <div class="col-sm-5">
                                        <input type="file" id="createAttachmentXLS">
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="inputDocument()">SIMPAN</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #BA55D3; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Perbaharui Data Dokumen<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Departemen<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <select class="form-control select2" id="editDepartment"
                                            data-placeholder="Select Department" style="width: 100%;">
                                            <option value=""></option>
                                            @foreach ($departments as $department)
                                                <option
                                                    value="{{ $department->department_name }}||{{ $department->department_shortname }}">
                                                    {{ $department->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Kategori<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <a href="javascript:void(0)" style="border-color: black; color: black;"
                                            id="btn_edit_IK" onclick="btnCategory('IK')" class="btn btn-sm">IK -
                                            Instruksi Kerja</a>
                                        <a href="javascript:void(0)" style="border-color: black; color: black;"
                                            id="btn_edit_DM" onclick="btnCategory('DM')" class="btn btn-sm">DM - Dokumen
                                            Mutu</a>
                                        <a href="javascript:void(0)" style="border-color: black; color: black;"
                                            id="btn_edit_DL" onclick="btnCategory('DL')" class="btn btn-sm">DL - Dokumen
                                            Lingkungan</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Detail Kategori<span class="text-red">*</span> :</label>
                                    <div class="col-sm-7">
                                        <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_edit_1" onclick="btnCategory2('Instruksi Kerja',this.id)" class="btn btn-sm">Instruksi Kerja</a>
                                        <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_edit_2" onclick="btnCategory2('Dokumen Lingkungan',this.id)" class="btn btn-sm">Dokumen
                                            Lingkungan</a>
                                        <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_edit_3" onclick="btnCategory2('QC Koteihyo',this.id)" class="btn btn-sm">QC Koteihyo</a>
                                        <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_edit_4" onclick="btnCategory2('Procedure',this.id)" class="btn btn-sm">Procedure</a>
                                        <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_edit_5" onclick="btnCategory2('Spec Product',this.id)" class="btn btn-sm">Spec Product</a>
                                            <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_edit_6" onclick="btnCategory2('Koteizu',this.id)" class="btn btn-sm">Koteizu</a>
                                            <a href="javascript:void(0)" style="margin-bottom:5px;border-color: black; color: black;"
                                            id="btn_edit_7" onclick="btnCategory2('Parameter Process',this.id)" class="btn btn-sm">Parameter Process</a>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">No.
                                        Dokumen<span class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="Enter Document Number"
                                            id="editDocumentNumber">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Judul
                                        Dokumen<span class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Document Title"
                                            id="editTitle">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Status<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <select class="form-control select2" id="editStatus"
                                            data-placeholder="Select Status" style="width: 100%;">
                                            <option value="Active">Active</option>
                                            <option value="Obsolete">Obsolete</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12" style="padding-bottom: 10px;">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="editDocument()">SIMPAN</button>
                        </div>
                        <span style="font-weight: bold; font-size: 1.2vw;">Tabel Distribusi</span>
                        <table id="tableDistribution" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #9932CC; color: white;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">ACC</th>
                                    <th style="width: 0.1%; text-align: center;">JPN</th>
                                    <th style="width: 0.1%; text-align: center;">GA</th>
                                    <th style="width: 0.1%; text-align: center;">HR</th>
                                    <th style="width: 0.1%; text-align: center;">LOG</th>
                                    <th style="width: 0.1%; text-align: center;">PC</th>
                                    <th style="width: 0.1%; text-align: center;">PROC</th>
                                    <th style="width: 0.1%; text-align: center;">FA</th>
                                    <th style="width: 0.1%; text-align: center;">EI</th>
                                    <th style="width: 0.1%; text-align: center;">MTC</th>
                                    <th style="width: 0.1%; text-align: center;">MIS</th>
                                    <th style="width: 0.1%; text-align: center;">KP</th>
                                    <th style="width: 0.1%; text-align: center;">PE</th>
                                    <th style="width: 0.1%; text-align: center;">QA</th>
                                    <th style="width: 0.1%; text-align: center;">WLD</th>
                                    <th style="width: 0.1%; text-align: center;">PCH</th>
                                    <th style="width: 0.1%; text-align: center;">BP</th>
                                    <th style="width: 0.1%; text-align: center;">ST</th>
                                </tr>
                            </thead>
                            <tbody id="tableDistributionBody">
                                <tr>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                    <td style="width: 0.1%; text-align: center;">&check;</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVersion" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #BA55D3; font-weight: bold; padding: 3px; margin-top: 0; color: white;">
                            Daftar Versi Dokumen<br>
                        </h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <form class="form-horizontal">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">No.
                                        Dokumen<span class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <input type="text" class="form-control" placeholder="Enter Document Number"
                                            id="versionDocumentNumber" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">Judul
                                        Dokumen<span class="text-red">*</span> :</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Enter Document Title"
                                            id="versionTitle" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">No.
                                        Revisi<span class="text-red">*</span> :</label>
                                    <div class="col-sm-2">
                                        <div class="input-group">
                                            <input type="text" class="numpad form-control" placeholder="Revise"
                                                id="versionVersion">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">Tanggal<span class="text-red">*</span> :</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control datepicker" id="versionVersionDate"
                                            placeholder="   Select Date">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for="" class="col-sm-3 control-label">PDF<span
                                            class="text-red">*</span> :</label>
                                    <div class="col-sm-5">
                                        <input type="file" id="versionAttachmentPDF">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label style="padding-top: 0;" for=""
                                        class="col-sm-3 control-label">EXCEL<span class="text-red"></span> :</label>
                                    <div class="col-sm-5">
                                        <input type="file" id="versionAttachmentXLS">
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-md-12" style="padding-bottom: 10px;padding-left: 0px;padding-right: 0px;">
                            <button class="btn btn-danger pull-left" data-dismiss="modal" aria-label="Close"
                                style="font-weight: bold; font-size: 1.3vw; width: 30%;">BATAL</button>
                            <button class="btn btn-success pull-right"
                                style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                                onclick="versionDocument()">TAMBAH REVISI</button>
                        </div>
                        <table id="tableVersion" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #9932CC; color: white;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">Version</th>
                                    <th style="width: 0.1%; text-align: right;">Tanggal</th>
                                    <th style="width: 0.4%; text-align: center;">PDF</th>
                                    <th style="width: 0.4%; text-align: center;">Excel</th>
                                    <th style="width: 0.2%; text-align: left;">Created By</th>
                                    <th style="width: 0.1%; text-align: center;">Updated At</th>
                                    <th style="width: 0.1%; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableVersionBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/jquery.numpad.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.fn.numpad.defaults.gridTpl = '<table class="table modal-content" style="width: 40%; z-index: 9999;"></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
        $.fn.numpad.defaults.buttonNumberTpl =
            '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl =
            '<button type="button" class="btn" style="font-size:2vw; width: 100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function() {
            $(this).find('.done').addClass('btn-primary');
        };

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });
            fetchData();
        });

        $(function() {
            $('#createDepartment').select2({
                dropdownParent: $('#modalCreate')
            });
            $('#createStatus').select2({
                dropdownParent: $('#modalCreate')
            });
            $('#createVersionDate').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            $('#editDepartment').select2({
                dropdownParent: $('#modalEdit')
            });
            $('#editStatus').select2({
                dropdownParent: $('#modalEdit')
            });
            $('#editVersionDate').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            $('#versionVersionDate').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var departments = <?php echo json_encode($departments); ?>;
        var documents = [];
        var document_attachments = [];

        function remDocument(document_id) {
            if (confirm("Semua data dan versi document ini akan hilang.\n Apakah anda yakin akan menghapus data ini? ")) {
                var data = {
                    document_id: document_id
                }
                $.post("{{ url('delete/standardization/document') }}", data, function(result, status, xhr) {
                    if (result.status) {
                        $('#rem_' + result.document_id).closest("tr").remove();
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

        function modalCreate() {
            $('#createDepartment').prop('selectedIndex', 0).change();
            $('#btn_IK').css('background-color', 'white');
            $('#btn_DM').css('background-color', 'white');
            $('#btn_DL').css('background-color', 'white');
            for(var i = 1; i < 8;i++){
                $('#btn_'+i).css('background-color', 'white');
            }
            for(var i = 1; i < 8;i++){
                $('#btn_edit_'+i).css('background-color', 'white');
            }
            $('#category').val("");
            $('#createDocumentNumber').val("");
            $('#createTitle').val("");
            $('#createVersion').val("");
            $('#createVersionDate').val("");
            $('#createStatus').val("").trigger('change');
            $('#createAttachmentPDF').val("");
            $('#createAttachmentXLS').val("");
            $('#modalCreate').modal('show');
        }

        function btnCategory(cat) {
            $('#btn_IK').css('background-color', 'white');
            $('#btn_DM').css('background-color', 'white');
            $('#btn_DL').css('background-color', 'white');
            // console.log(cat);
            $('#btn_' + cat).css('background-color', '#90ed7d');
            $('#btn_edit_IK').css('background-color', 'white');
            $('#btn_edit_DM').css('background-color', 'white');
            $('#btn_edit_DL').css('background-color', 'white');
            $('#btn_edit_' + cat).css('background-color', '#90ed7d');

            for(var i = 1; i < 8;i++){
                $('#btn_'+i).css('background-color', 'white');
            }

            for(var i = 1; i < 8;i++){
                $('#btn_edit_'+i).css('background-color', 'white');
            }

            if (cat == 'IK') {
                $('#btn_1').css('background-color', '#90ed7d');
                $('#btn_edit_1').css('background-color', '#90ed7d');
                $('#document_category').val('Instruksi Kerja');
            }

            if (cat == 'DL') {
                $('#btn_2').css('background-color', '#90ed7d');
                $('#btn_edit_2').css('background-color', '#90ed7d');
                $('#document_category').val('Dokumen Lingkungan');
            }

            $('#category').val(cat);
        }

        function btnCategory2(cat,id) {
            for(var i = 1; i < 8;i++){
                $('#btn_'+i).css('background-color', 'white');
            }
            $('#'+id).css('background-color', '#90ed7d');

            for(var i = 1; i < 8;i++){
                $('#btn_edit_'+i).css('background-color', 'white');
            }
            $('#'+id).css('background-color', '#90ed7d');

            $('#document_category').val(cat);
        }

        function versionDocument() {
            $('#loading').show();

            var document_id = $('#document_id').val();
            var version = $('#versionVersion').val();
            var version_date = $('#versionVersionDate').val();
            var attachment_pdf = $('#versionAttachmentPDF').prop('files')[0];
            var file_pdf = $('#versionAttachmentPDF').val().replace(/C:\\fakepath\\/i, '').split(".");
            var attachment_xls = $('#versionAttachmentXLS').prop('files')[0];
            var file_xls = $('#versionAttachmentXLS').val().replace(/C:\\fakepath\\/i, '').split(".");

            if (version == "" || version_date == "" || attachment_pdf == "") {
                $('#loading').hide();
                openErrorGritter('Error!', 'Semua data dengan tanda bintang harus terisi.');
                audio_error.play();
                return false;
            }

            var formData = new FormData();
            formData.append('document_id', document_id);
            formData.append('version', version);
            formData.append('version_date', version_date);
            formData.append('attachment_pdf', attachment_pdf);
            formData.append('extension_pdf', file_pdf[1]);
            formData.append('file_name_pdf', file_pdf[0]);
            formData.append('attachment_xls', attachment_xls);
            formData.append('extension_xls', file_xls[1]);
            formData.append('file_name_xls', file_xls[0]);

            $.ajax({
                url: "{{ url('version/standardization/document') }}",
                method: "POST",
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    if (data.status) {
                        fetchData();
                        $('#loading').hide();
                        openSuccessGritter('Success!', data.message);
                        audio_ok.play();
                        $('#modalVersion').modal('hide');
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error!', data.message);
                        audio_error.play();
                    }

                }
            });
        }

        function editDocument() {
            $('#loading').show();

            var document_id = $('#document_id').val();
            var department = $('#editDepartment').val().split('||');
            var status_document = $('#editStatus').val();
            var category = $('#category').val();
            var document_category = $('#document_category').val();
            var document_number = $('#editDocumentNumber').val();
            var title = $('#editTitle').val();

            if (department == "" || category == "" || document_category == "" || document_number == "" || title == "") {
                $('#loading').hide();
                openErrorGritter('Error!', 'Semua data dengan tanda bintang harus terisi.');
                audio_error.play();
                return false;
            }

            var status = true;
            $.each(documents, function(key, value) {
                if (value.document_id == document_id) {
                    if (
                        value.department_shortname == department[1] &&
                        value.category == category &&
                        value.document_category == document_category &&
                        value.document_number == document_number &&
                        value.title == title &&
                        value.status == status_document) {
                        status = false;
                    }
                }
            });

            if (status == false) {
                $('#loading').hide();
                openErrorGritter('Error!', "Tidak ada perubahan pada dokumen.");
                audio_error.play();
                return false;
            }

            var data = {
                document_id: document_id,
                department_name: department[0],
                department_shortname: department[1],
                category: category,
                document_category: document_category,
                document_number: document_number,
                title: title,
                status_document: status_document
            }

            $.post('{{ url("edit/standardization/document") }}', data, function(result, status, xhr) {
                if (result.status) {
                    fetchData();
                    $('#modalEdit').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success!', result.message);
                    audio_ok.play();
                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                    audio_error.play();
                }
            });
        }

        function inputDocument() {
            $('#loading').show();

            var department = $('#createDepartment').val().split('||');
            var category = $('#category').val();
            var document_category = $('#document_category').val();
            var document_number = $('#createDocumentNumber').val();
            var title = $('#createTitle').val();
            var version = $('#createVersion').val();
            var version_date = $('#createVersionDate').val();
            var status = $('#createStatus').val();
            var attachment_pdf = $('#createAttachmentPDF').prop('files')[0];
            var file_pdf = $('#createAttachmentPDF').val().replace(/C:\\fakepath\\/i, '').split(".");
            var attachment_xls = $('#createAttachmentXLS').prop('files')[0];
            var file_xls = $('#createAttachmentXLS').val().replace(/C:\\fakepath\\/i, '').split(".");

            if (department == "" || category == "" || document_category == "" || document_number == "" || title == "" || version == "" ||
                version_date == "" || attachment_pdf == "" || status == "") {
                $('#loading').hide();
                openErrorGritter('Error!', 'Semua data dengan tanda bintang harus terisi.');
                audio_error.play();
                return false;
            }

            var formData = new FormData();
            formData.append('department_name', department[0]);
            formData.append('department_shortname', department[1]);
            formData.append('category', category);
            formData.append('document_category', document_category);
            formData.append('document_number', document_number);
            formData.append('title', title);
            formData.append('version', version);
            formData.append('version_date', version_date);
            formData.append('status', status);
            formData.append('attachment_pdf', attachment_pdf);
            formData.append('extension_pdf', file_pdf[1]);
            formData.append('file_name_pdf', file_pdf[0]);
            formData.append('attachment_xls', attachment_xls);
            formData.append('extension_xls', file_xls[1]);
            formData.append('file_name_xls', file_xls[0]);

            $.ajax({
                url: "{{ url('input/standardization/document') }}",
                method: "POST",
                data: formData,
                dataType: 'JSON',
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
                    if (data.status) {
                        fetchData();
                        $('#loading').hide();
                        openSuccessGritter('Success!', data.message);
                        audio_ok.play();
                        $('#modalCreate').modal('hide');
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error!', data.message);
                        audio_error.play();
                    }

                }
            });

        }

        function modalEdit(document_id) {
            $('#document_id').val(document_id);

            $.each(documents, function(key, value) {
                if (value.document_id == document_id) {
                    $('#editDepartment').val(value.department_name + '||' + value.department_shortname).change();
                    $('#editStatus').val(value.status).change();
                    $('#btn_edit_IK').css('background-color', 'white');
                    $('#btn_edit_DM').css('background-color', 'white');
                    $('#btn_edit_DL').css('background-color', 'white');
                    for(var i = 1; i < 8;i++){
                        if (value.document_category == $('#btn_edit_' + i).text()) {
                            $('#btn_edit_' + i).css('background-color', '#90ed7d');
                        }else{
                            $('#btn_edit_'+i).css('background-color', 'white');
                        }
                    }
                    $('#btn_edit_' + value.category).css('background-color', '#90ed7d');
                    $('#category').val(value.category);
                    $('#editDocumentNumber').val(value.document_number);
                    $('#editTitle').val(value.title);
                }
            });

            $('#modalEdit').modal('show');
        }

        function modalVersion(document_id,status) {
            $('#document_id').val(document_id);

            $('#tableVersionBody').html("");
            var tableVersionBody = "";
            var version = [];

            $.each(document_attachments, function(key, value) {
                if (value.document_id == document_id) {
                    version.push(value.version);
                    tableVersionBody += '<tr>';
                    tableVersionBody += '<td style="text-align: center;">' + value.version + '</td>';
                    tableVersionBody += '<td style="text-align: right;">' + value.version_date + '</td>';
                    if ('{{$role}}'.match(/STD/gi) || '{{$role}}'.match(/MIS/gi)) {
                        tableVersionBody +=
                            '<td style="text-align: center;"><a href="{{ asset('files/standardization/documents') }}/' +
                            value.file_name_pdf + '" target="_blank"><i class="fa fa-file-pdf-o"></i>' + value
                            .file_name_pdf + '</a></td>';
                        if (value.file_name_xls != "") {
                            tableVersionBody +=
                                '<td style="text-align: center;"><a href="{{ asset('files/standardization/documents') }}/' +
                                value.file_name_xls + '" target="_blank"><i class="fa fa-file-excel-o"></i>' + value
                                .file_name_xls + '</a></td>';
                        } else {
                            tableVersionBody += '<td style="text-align: center;">-</td>';
                        }
                    }else{
                        if (status == 'Obsolete') {
                            tableVersionBody += '<td style="text-align: center;">-</td>';
                            tableVersionBody += '<td style="text-align: center;">-</td>';
                        }else{
                            tableVersionBody +=
                                '<td style="text-align: center;"><a href="{{ asset('files/standardization/documents') }}/' +
                                value.file_name_pdf + '" target="_blank"><i class="fa fa-file-pdf-o"></i>' + value
                                .file_name_pdf + '</a></td>';
                            if (value.file_name_xls != "") {
                                tableVersionBody +=
                                    '<td style="text-align: center;"><a href="{{ asset('files/standardization/documents') }}/' +
                                    value.file_name_xls + '" target="_blank"><i class="fa fa-file-excel-o"></i>' + value
                                    .file_name_xls + '</a></td>';
                            } else {
                                tableVersionBody += '<td style="text-align: center;">-</td>';
                            }
                        }
                    }
                    tableVersionBody += '<td style="text-align: left;">' + value.created_by_name + '</td>';
                    tableVersionBody += '<td style="text-align: right;">' + value.created_at + '</td>';
                    tableVersionBody += '<td style="text-align: center;">';
                    if ('{{$role}}'.match(/STD/gi) || '{{$role}}'.match(/MIS/gi)) {
                        tableVersionBody += '<button class="btn btn-danger btn-sm" onclick="deleteVersion(\'' +
                            value.id + '\',\'' +
                            document_id + '\',\'' +
                            status + '\')"><i class="fa fa-trash"></i></button>';
                    }
                    tableVersionBody += '</td>';
                    tableVersionBody += '</tr>';
                }
            });
            $('#tableVersionBody').append(tableVersionBody);

            $.each(documents, function(key, value) {
                if (value.document_id == document_id) {
                    $('#versionDocumentNumber').val(value.document_number);
                    $('#versionTitle').val(value.title);
                    $('#versionVersion').val(Math.max.apply(Math, version) + 1);
                }
            });

            $('#modalVersion').modal('show');
        }

        function deleteVersion(id,document_id,status) {
            if (confirm('Apakah Anda yakin?')) {
                $('#loading').show();
                var data = {
                    id:id
                }

                $.post("{{ url('delete/standardization/document/version') }}", data, function(result, status, xhr) {
                    if (result.status) {
                        $('#modalVersion').modal('hide');
                        openSuccessGritter('Success!','Success Delete Version');
                        fetchData();
                        $('#loading').hide();
                    }else{
                        $('#loading').hide();
                        openErrorGritter('Error!',result.message);
                    }
                });
            }
        }

        function fetchFilter(dep, cat) {
            $('#tableDocumentHead').html("");
            var tableDocumentHead = '';
            $('#tableDocumentFoot').html("");
            var tableDocumentFoot = '';

            tableDocumentHead += '<tr>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: center;">ID</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: left;">Dept</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: left;">Kat</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: left;">Detail Kat</th>';
                tableDocumentHead += '<th style="width: 0.3%; text-align: left;">No. Dokumen</th>';
                tableDocumentHead += '<th style="width: 1%; text-align: left;">Judul</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: center;">Rev.</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: right;">Tgl Rev.</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: center;">Dokumen <i class="fa fa-paperclip"></i></th>';
                tableDocumentHead += '<th style="width: 0.3%; text-align: left;">Updated By</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: center;">#</th>';
            tableDocumentHead += '</tr>';

            tableDocumentFoot += '<tr>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '</tr>';

            $('#tableDocumentHead').append(tableDocumentHead);
            $('#tableDocumentFoot').append(tableDocumentFoot);

            $('#tableDocumentBody').html("");
            var tableDocumentBody = "";
            var detail_category = [];

            $.each(documents, function(key, value) {
                var re = new RegExp(value.category, 'g');
                if (value.department_shortname == dep && cat.match(re)) {
                    if (cat.match(/Active/gi)) {
                        if (value.status == null || value.status == 'Active') {
                            tableDocumentBody += '<tr>';
                            tableDocumentBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'' +
                                value.document_id + '\')">' + value.document_id + '</a></td>';
                            tableDocumentBody += '<td style="width: 0.1%; text-align: left;">' + value
                                .department_shortname + '</td>';
                            tableDocumentBody += '<td style="width: 0.1%; text-align: left;">' + value.category
                                .toUpperCase() + '</td>';
                            tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + (value.document_category || value.category
                                .toUpperCase()) + '</td>';
                            tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + value.document_number
                                .toUpperCase() + '</td>';
                            tableDocumentBody += '<td style="width: 1%; text-align: left;">' + value.title + '</td>';
                            tableDocumentBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalVersion(\'' +
                            value.document_id + '\',\'' +
                            value.status + '\')"><div style="height: 100%; width: 100%;">' + value.version +
                                '</div></a></td>';
                            tableDocumentBody += '<td style="width: 0.1%; text-align: right;">' + value.version_date +
                                '</td>';
                            if ('{{$role}}'.match(/STD/gi) || '{{$role}}'.match(/MIS/gi)) {
                                tableDocumentBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="{{ asset('files/standardization/documents') }}/' +
                                    value.file_name_pdf +
                                    '" target="_blank"><div style="height: 100%; width: 100%;"><i class="fa fa-file-pdf-o"></i></div></a></td>';
                            }else{
                                if (value.status == 'Obsolete') {
                                    tableDocumentBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold;"></td>';
                                }else {
                                    tableDocumentBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="{{ asset('files/standardization/documents') }}/' +
                                    value.file_name_pdf +
                                    '" target="_blank"><div style="height: 100%; width: 100%;"><i class="fa fa-file-pdf-o"></i></div></a></td>';
                                }
                            }
                            tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + value.created_by_name +
                                '</td>';
                            if ('{{$role}}'.match(/STD/gi) || '{{$role}}'.match(/MIS/gi)) {
                                tableDocumentBody +=
                                    '<td style="width: 0.01%; text-align: center;"><button class="btn btn-danger btn-xs" onclick="remDocument(\'' +
                                    value.document_id + '\')" id="rem_' + value.document_id +
                                    '"><i class="fa fa-trash"></i></button></td>';
                            }else{
                                tableDocumentBody +=
                                    '<td style="width: 0.01%; text-align: center;"></td>'; 
                            }
                            // tableDocumentBody += '<td style="width: 0.2%; text-align: right;">'+value.updated_at+'</td>';
                            tableDocumentBody += '</tr>';
                            if (value.document_category != null) {
                                detail_category.push(value.document_category);
                            }
                        }
                    }
                    if (cat.match(/Obsolete/gi)) {
                        if (value.status == 'Obsolete') {
                            tableDocumentBody += '<tr>';
                            tableDocumentBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'' +
                                value.document_id + '\')">' + value.document_id + '</a></td>';
                            tableDocumentBody += '<td style="width: 0.1%; text-align: left;">' + value
                                .department_shortname + '</td>';
                            tableDocumentBody += '<td style="width: 0.1%; text-align: left;">' + value.category
                                .toUpperCase() + '</td>';
                            tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + (value.document_category || value.category
                                .toUpperCase()) + '</td>';
                            tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + value.document_number
                                .toUpperCase() + '</td>';
                            tableDocumentBody += '<td style="width: 1%; text-align: left;">' + value.title + '</td>';
                            tableDocumentBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalVersion(\'' +
                            value.document_id + '\',\'' +
                            value.status + '\')"><div style="height: 100%; width: 100%;">' + value.version +
                                '</div></a></td>';
                            tableDocumentBody += '<td style="width: 0.1%; text-align: right;">' + value.version_date +
                                '</td>';
                            if ('{{$role}}'.match(/STD/gi) || '{{$role}}'.match(/MIS/gi)) {
                                tableDocumentBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="{{ asset('files/standardization/documents') }}/' +
                                    value.file_name_pdf +
                                    '" target="_blank"><div style="height: 100%; width: 100%;"><i class="fa fa-file-pdf-o"></i></div></a></td>';
                            }else{
                                if (value.status == 'Obsolete') {
                                    tableDocumentBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold;"></td>';
                                }else {
                                    tableDocumentBody +=
                                    '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="{{ asset('files/standardization/documents') }}/' +
                                    value.file_name_pdf +
                                    '" target="_blank"><div style="height: 100%; width: 100%;"><i class="fa fa-file-pdf-o"></i></div></a></td>';
                                }
                            }
                            tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + value.created_by_name +
                                '</td>';
                            if ('{{$role}}'.match(/STD/gi) || '{{$role}}'.match(/MIS/gi)) {
                                tableDocumentBody +=
                                '<td style="width: 0.01%; text-align: center;"><button class="btn btn-danger btn-xs" onclick="remDocument(\'' +
                                value.document_id + '\')" id="rem_' + value.document_id +
                                '"><i class="fa fa-trash"></i></button></td>';
                            }else{
                                tableDocumentBody +=
                                '<td style="width: 0.01%; text-align: center;"></td>';
                            }
                            // tableDocumentBody += '<td style="width: 0.2%; text-align: right;">'+value.updated_at+'</td>';
                            tableDocumentBody += '</tr>';
                            if (value.document_category != null) {
                                detail_category.push(value.document_category);
                            }
                        }
                    }
                }
            });

            $('#tableDocument').DataTable().clear();
            $('#tableDocument').DataTable().destroy();

            $('#tableDocumentBody').append(tableDocumentBody);

            // var detail_category_unik = detail_category.filter(onlyUnique);
            // detail_category_unik.sort();
            // $('#button_dm').html('');
            // var button_dm = '';
            // for(var i = 0; i < detail_category_unik.length;i++){
            //     var margin = '';
            //     if (i == 0) {
            //         margin = 'margin-left:5px;';
            //     }
            //     button_dm += '<button class="btn btn-primary" onclick="fetchFilter2(\''+detail_category_unik[i]+'\')" style="'+margin+'margin-right:5px;font-weight:bold;">'+detail_category_unik[i]+'</button>';
            // }
            // $('#button_dm').append(button_dm);

            // $('#tableDocument').DataTable({
            //     'dom': 'Bfrtip',
            //     'responsive': true,
            //     'lengthMenu': [
            //         [10, 25, 50, -1],
            //         ['10 rows', '25 rows', '50 rows', 'Show all']
            //     ],
            //     'buttons': {
            //         buttons: [{
            //                 extend: 'pageLength',
            //                 className: 'btn btn-default',
            //             },
            //             {
            //                 extend: 'copy',
            //                 className: 'btn btn-success',
            //                 text: '<i class="fa fa-copy"></i> Copy',
            //                 exportOptions: {
            //                     columns: ':not(.notexport)'
            //                 }
            //             },
            //             {
            //                 extend: 'excel',
            //                 className: 'btn btn-info',
            //                 text: '<i class="fa fa-file-excel-o"></i> Excel',
            //                 exportOptions: {
            //                     columns: ':not(.notexport)'
            //                 }
            //             },
            //             {
            //                 extend: 'print',
            //                 className: 'btn btn-warning',
            //                 text: '<i class="fa fa-print"></i> Print',
            //                 exportOptions: {
            //                     columns: ':not(.notexport)'
            //                 }
            //             },
            //         ]
            //     },
            //     'paging': true,
            //     'lengthChange': true,
            //     'searching': true,
            //     'ordering': true,
            //     'order': [],
            //     'info': true,
            //     'autoWidth': true,
            //     "sPaginationType": "full_numbers",
            //     "bJQueryUI": true,
            //     "bAutoWidth": false,
            //     "processing": true
            // });
            $('#tableDocument tfoot th').each( function () {
                var title = $(this).text();
                $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
            } );

            var table = $('#tableDocument').DataTable({
            'dom': 'Bfrtip',
                'responsive':true,
                'lengthMenu': [
                [ 15, 25, -1 ],
                [ '15 rows', '25 rows', 'Show all' ]
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
                "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true,
            'ordering' :false,
                initComplete: function() {
                this.api()
                    .columns([1, 2, 3])
                    .every(function(dd) {
                        var column = this;
                        var theadname = $("#tableDocument th").eq([dd]).text();
                        var select = $(
                                '<select style="color:black;"><option value="" style="font-size:11px;">All</option></select>'
                            )
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                column.search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });
                        column
                            .data()
                            .unique()
                            .sort()
                            .each(function(d, j) {
                                var vals = d;
                                if ($("#tableDocument th").eq([dd]).text() == 'Category') {
                                    vals = d.split(' ')[0];
                                }
                                select.append('<option style="font-size:11px;" value="' +
                                    d + '">' + vals + '</option>');
                            });
                    });
                },
            });

            table.columns().every( function () {
                var that = this;
                $( '#search', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                        .search( this.value )
                        .draw();
                    }
                } );
            } );

            $('#tableDocument tfoot tr').appendTo('#tableDocument thead');

            location.href='#divDetail';

        }

        function fetchFilter2(cat) {
            $('#tableDocumentHead').html("");
            var tableDocumentHead = '';
            $('#tableDocumentFoot').html("");
            var tableDocumentFoot = '';

            tableDocumentHead += '<tr>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: center;">ID</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: left;">Dept</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: left;">Kat</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: left;">Detail Kat</th>';
                tableDocumentHead += '<th style="width: 0.3%; text-align: left;">No. Dokumen</th>';
                tableDocumentHead += '<th style="width: 1%; text-align: left;">Judul</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: center;">Rev.</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: right;">Tgl Rev.</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: center;">Dokumen <i class="fa fa-paperclip"></i></th>';
                tableDocumentHead += '<th style="width: 0.3%; text-align: left;">Updated By</th>';
                tableDocumentHead += '<th style="width: 0.1%; text-align: center;">#</th>';
            tableDocumentHead += '</tr>';

            tableDocumentFoot += '<tr>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '<th></th>';
            tableDocumentFoot += '</tr>';

            $('#tableDocumentHead').append(tableDocumentHead);
            $('#tableDocumentFoot').append(tableDocumentFoot);

            $('#tableDocumentBody').html("");
            var tableDocumentBody = "";

            // console.log(dep);
            // console.log(cat);

            var detail_category = [];

            $.each(documents, function(key, value) {
                var re = new RegExp(value.document_category, 'g');
                if (cat.match(re)) {
                    tableDocumentBody += '<tr>';
                    tableDocumentBody +=
                        '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'' +
                        value.document_id + '\')">' + value.document_id + '</a></td>';
                    tableDocumentBody += '<td style="width: 0.1%; text-align: left;">' + value
                        .department_shortname + '</td>';
                    tableDocumentBody += '<td style="width: 0.1%; text-align: left;">' + value.category
                        .toUpperCase() + '</td>';
                    tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + (value.document_category || value.category
                        .toUpperCase()) + '</td>';
                    tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + value.document_number
                        .toUpperCase() + '</td>';
                    tableDocumentBody += '<td style="width: 1%; text-align: left;">' + value.title + '</td>';
                    tableDocumentBody +=
                        '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalVersion(\'' +
                    value.document_id + '\',\'' +
                    value.status + '\')"><div style="height: 100%; width: 100%;">' + value.version +
                        '</div></a></td>';
                    tableDocumentBody += '<td style="width: 0.1%; text-align: right;">' + value.version_date +
                        '</td>';
                    if ('{{$role}}'.match(/STD/gi) || '{{$role}}'.match(/MIS/gi)) {
                        tableDocumentBody +=
                            '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="{{ asset('files/standardization/documents') }}/' +
                            value.file_name_pdf +
                            '" target="_blank"><div style="height: 100%; width: 100%;"><i class="fa fa-file-pdf-o"></i></div></a></td>';
                    }else{
                        if (value.status == 'Obsolete') {
                            tableDocumentBody +=
                            '<td style="width: 0.1%; text-align: center; font-weight: bold;"></td>';
                        }else {
                            tableDocumentBody +=
                            '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="{{ asset('files/standardization/documents') }}/' +
                            value.file_name_pdf +
                            '" target="_blank"><div style="height: 100%; width: 100%;"><i class="fa fa-file-pdf-o"></i></div></a></td>';
                        }
                    }
                    tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + value.created_by_name +
                        '</td>';
                    if ('{{$role}}'.match(/STD/gi) || '{{$role}}'.match(/MIS/gi)) {
                        tableDocumentBody +=
                            '<td style="width: 0.01%; text-align: center;"><button class="btn btn-danger btn-xs" onclick="remDocument(\'' +
                            value.document_id + '\')" id="rem_' + value.document_id +
                            '"><i class="fa fa-trash"></i></button></td>';
                    }else{
                        tableDocumentBody +=
                            '<td style="width: 0.01%; text-align: center;"></td>'; 
                    }
                    // tableDocumentBody += '<td style="width: 0.2%; text-align: right;">'+value.updated_at+'</td>';
                    tableDocumentBody += '</tr>';
                    if (value.document_category != null) {
                        detail_category.push(value.document_category);
                    }
                }
            });

            $('#tableDocument').DataTable().clear();
            $('#tableDocument').DataTable().destroy();

            $('#tableDocumentBody').append(tableDocumentBody);

            $('#tableDocument tfoot th').each( function () {
                var title = $(this).text();
                $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
            } );

            var table = $('#tableDocument').DataTable({
            'dom': 'Bfrtip',
                'responsive':true,
                'lengthMenu': [
                [ 15, 25, -1 ],
                [ '15 rows', '25 rows', 'Show all' ]
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
                "bJQueryUI": true,
            "bAutoWidth": false,
            "processing": true,
            'ordering' :false,
                initComplete: function() {
                this.api()
                    .columns([1, 2, 3])
                    .every(function(dd) {
                        var column = this;
                        var theadname = $("#tableDocument th").eq([dd]).text();
                        var select = $(
                                '<select style="color:black;"><option value="" style="font-size:11px;">All</option></select>'
                            )
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                column.search(val ? '^' + val + '$' : '', true, false)
                                    .draw();
                            });
                        column
                            .data()
                            .unique()
                            .sort()
                            .each(function(d, j) {
                                var vals = d;
                                if ($("#tableDocument th").eq([dd]).text() == 'Category') {
                                    vals = d.split(' ')[0];
                                }
                                select.append('<option style="font-size:11px;" value="' +
                                    d + '">' + vals + '</option>');
                            });
                    });
                },
            });

            table.columns().every( function () {
                var that = this;
                $( '#search', this.footer() ).on( 'keyup change', function () {
                    if ( that.search() !== this.value ) {
                        that
                        .search( this.value )
                        .draw();
                    }
                } );
            } );

            $('#tableDocument tfoot tr').appendTo('#tableDocument thead');

            location.href='#divDetail';

        }

        function onlyUnique(value, index, self) {
          return self.indexOf(value) === index;
        }

        function fetchData() {
            $('#loading').show();
            var data = {

            }
            $.get('{{ url("fetch/standardization/document") }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#tableDocumentHead').html("");
                    var tableDocumentHead = '';
                    $('#tableDocumentFoot').html("");
                    var tableDocumentFoot = '';

                    tableDocumentHead += '<tr>';
                        tableDocumentHead += '<th style="width: 0.1%; text-align: center;">ID</th>';
                        tableDocumentHead += '<th style="width: 0.1%; text-align: left;">Dept</th>';
                        tableDocumentHead += '<th style="width: 0.1%; text-align: left;">Kat</th>';
                        tableDocumentHead += '<th style="width: 0.1%; text-align: left;">Detail Kat</th>';
                        tableDocumentHead += '<th style="width: 0.3%; text-align: left;">No. Dokumen</th>';
                        tableDocumentHead += '<th style="width: 1%; text-align: left;">Judul</th>';
                        tableDocumentHead += '<th style="width: 0.1%; text-align: center;">Rev.</th>';
                        tableDocumentHead += '<th style="width: 0.1%; text-align: right;">Tgl Rev.</th>';
                        tableDocumentHead += '<th style="width: 0.1%; text-align: center;">Dokumen <i class="fa fa-paperclip"></i></th>';
                        tableDocumentHead += '<th style="width: 0.3%; text-align: left;">Updated By</th>';
                        tableDocumentHead += '<th style="width: 0.1%; text-align: center;">#</th>';
                    tableDocumentHead += '</tr>';

                    tableDocumentFoot += '<tr>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '<th></th>';
                    tableDocumentFoot += '</tr>';

                    $('#tableDocumentHead').append(tableDocumentHead);
                    $('#tableDocumentFoot').append(tableDocumentFoot);

                    $('#tableDocumentBody').html("");
                    var tableDocumentBody = "";
                    documents = result.documents;
                    document_attachments = result.document_attachments;

                    var detail_category = [];

                    $.each(result.documents, function(key, value) {
                        tableDocumentBody += '<tr>';
                        tableDocumentBody +=
                            '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalEdit(\'' +
                            value.document_id + '\')">' + value.document_id + '</a></td>';
                        tableDocumentBody += '<td style="width: 0.1%; text-align: left;">' + value
                            .department_shortname + '</td>';
                        tableDocumentBody += '<td style="width: 0.1%; text-align: left;">' + value.category
                            .toUpperCase() + '</td>';
                        tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + (value.document_category || value.category
                                .toUpperCase()) + '</td>';
                        if (value.status == 'Obsolete') {
                            tableDocumentBody +=
                                '<td style="width: 0.3%; text-align: left; color: red;">(Obsolete)</br>' +
                                value.document_number.toUpperCase() + '</td>';
                        } else {
                            tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + value
                                .document_number.toUpperCase() + '</td>';
                        }
                        tableDocumentBody += '<td style="width: 1%; text-align: left;">' + value.title +
                            '</td>';
                        tableDocumentBody +=
                            '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="javascript:void(0)" onclick="modalVersion(\'' +
                            value.document_id + '\',\'' +
                            value.status + '\')"><div style="height: 100%; width: 100%;">' + value
                            .version + '</div></a></td>';
                        tableDocumentBody += '<td style="width: 0.1%; text-align: right;">' + value
                            .version_date + '</td>';
                        if ('{{$role}}'.match(/STD/gi) || '{{$role}}'.match(/MIS/gi)) {
                            tableDocumentBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="{{ asset('files/standardization/documents') }}/' +
                                value.file_name_pdf +
                                '" target="_blank"><div style="height: 100%; width: 100%;"><i class="fa fa-file-pdf-o"></i></div></a></td>';
                        }else{
                            if (value.status == 'Obsolete') {
                                tableDocumentBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;"></td>';
                            }else{
                                tableDocumentBody +=
                                '<td style="width: 0.1%; text-align: center; font-weight: bold;"><a href="{{ asset('files/standardization/documents') }}/' +
                                value.file_name_pdf +
                                '" target="_blank"><div style="height: 100%; width: 100%;"><i class="fa fa-file-pdf-o"></i></div></a></td>';
                            }
                        }
                        tableDocumentBody += '<td style="width: 0.3%; text-align: left;">' + value
                            .created_by_name + '</td>';
                        if ('{{$role}}'.match(/STD/gi) || '{{$role}}'.match(/MIS/gi)) {
                            tableDocumentBody +=
                            '<td style="width: 0.01%; text-align: center;"><button class="btn btn-danger btn-xs" onclick="remDocument(\'' +
                            value.document_id + '\')" id="rem_' + value.document_id +
                            '"><i class="fa fa-trash"></i></button></td>';
                        }else{
                            tableDocumentBody +=
                            '<td style="width: 0.01%; text-align: center;"></td>';
                        }
                        
                        // tableDocumentBody += '<td style="width: 0.2%; text-align: right;">'+value.updated_at+'</td>';
                        tableDocumentBody += '</tr>';

                        if (value.document_category != null) {
                            detail_category.push(value.document_category);
                        }
                    });

                    $('#tableDocument').DataTable().clear();
                    $('#tableDocument').DataTable().destroy();

                    $('#tableDocumentBody').append(tableDocumentBody);

                    var detail_category_unik = detail_category.filter(onlyUnique);
                    detail_category_unik.sort();
                    $('#button_dm').html('');
                    var button_dm = '';
                    for(var i = 0; i < detail_category_unik.length;i++){
                        var margin = '';
                        if (i == 0) {
                            margin = 'margin-left:5px;';
                        }
                        button_dm += '<button class="btn btn-primary" onclick="fetchFilter2(\''+detail_category_unik[i]+'\')" style="'+margin+'margin-right:5px;font-weight:bold;">'+detail_category_unik[i]+'</button>';
                    }
                    $('#button_dm').append(button_dm);

                    var result = [];
                    documents.reduce(function(res, value) {
                        if (!res[value.department_shortname]) {
                            res[value.department_shortname] = {
                                department_shortname: value.department_shortname,
                                category: value.category,
                                count_ik_active: 0,
                                count_ik_obsolete: 0,
                                count_dm_active: 0,
                                count_dm_obsolete: 0,
                                count_dl_active: 0,
                                count_dl_obsolete: 0
                            };
                            result.push(res[value.department_shortname]);
                        }
                        if (value.category == 'IK') {
                            if (value.status == 'Active') {
                                res[value.department_shortname].count_ik_active += 1;
                            }else if (value.status == 'Obsolete') {
                                res[value.department_shortname].count_ik_obsolete += 1;
                            }else{
                                res[value.department_shortname].count_ik_active += 1;
                            }
                        }
                        if (value.category == 'DM') {
                            if (value.status == 'Active') {
                                res[value.department_shortname].count_dm_active += 1;
                            }else if (value.status == 'Obsolete') {
                                res[value.department_shortname].count_dm_obsolete += 1;
                            }else{
                                res[value.department_shortname].count_dm_active += 1;
                            }
                        }
                        if (value.category == 'DL') {
                            if (value.status == 'Active') {
                                res[value.department_shortname].count_dl_active += 1;
                            }else if (value.status == 'Obsolete') {
                                res[value.department_shortname].count_dl_obsolete += 1;
                            }else{
                                res[value.department_shortname].count_dl_active += 1;
                            }
                        }
                        return res;
                    }, {});

                    var categories = [];
                    var series_ik_obsolete = [];
                    var series_ik_active = [];
                    var series_dl_obsolete = [];
                    var series_dl_active = [];
                    var series_dm_obsolete = [];
                    var series_dm_active = [];
                    var total_ik_active = 0;
                    var total_ik_obsolete = 0;
                    var total_dm_active = 0;
                    var total_dm_obsolete = 0;
                    var total_dl_active = 0;
                    var total_dl_obsolete = 0;

                    $.each(result, function(key, value) {
                        categories.push(value.department_shortname);
                        series_ik_active.push(value.count_ik_active);
                        series_ik_obsolete.push(value.count_ik_obsolete);
                        series_dm_active.push(value.count_dm_active);
                        series_dm_obsolete.push(value.count_dm_obsolete);
                        series_dl_active.push(value.count_dl_active);
                        series_dl_obsolete.push(value.count_dl_obsolete);

                        total_ik_active += value.count_ik_active;
                        total_ik_obsolete += value.count_ik_obsolete;
                        total_dm_active += value.count_dm_active;
                        total_dm_obsolete += value.count_dm_obsolete;
                        total_dl_active += value.count_dl_active;
                        total_dl_obsolete += value.count_dl_obsolete;
                    });

                    $('#total_ik_active').text(total_ik_active);
                    $('#total_ik_obsolete').text(total_ik_obsolete);
                    $('#total_dm_active').text(total_dm_active);
                    $('#total_dm_obsolete').text(total_dm_obsolete);
                    $('#total_dl_active').text(total_dl_active);
                    $('#total_dl_obsolete').text(total_dl_obsolete);

                    Highcharts.chart('container1', {
                        chart: {
                            type: 'column',
                            borderRadius: 10,
                            borderColor: 'black',
                            borderWidth: 1,
                            backgroundColor: {
                                linearGradient: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 1,
                                    y2: 1
                                },
                                stops: [
                                    [0, '#BA55D3'],
                                    [1, '#9932CC']
                                ]
                            }
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            categories: categories,
                            labels: {
                                style: {
                                    color: 'white'
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: null
                            },
                            labels: {
                                style: {
                                    color: 'white'
                                }
                            }
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 0,
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: 0,
                                        color: 'white'
                                    }
                                }
                            },
                            column: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            fetchFilter(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        exporting: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true,
                            backgroundColor: '#fff',
                            fontSize:'13px'
                        },
                        series: [{
                            name: 'IK Obsolete',
                            data: series_ik_obsolete,
                            color: '#A61111',
                            stacking:'normal'
                        },{
                            name: 'IK Active',
                            data: series_ik_active,
                            color: '#3CB371',
                            stacking:'normal',
                        }]
                    });

                    Highcharts.chart('container2', {
                        chart: {
                            type: 'column',
                            borderRadius: 10,
                            borderColor: 'black',
                            borderWidth: 1,
                            backgroundColor: {
                                linearGradient: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 1,
                                    y2: 1
                                },
                                stops: [
                                    [0, '#BA55D3'],
                                    [1, '#9932CC']
                                ]
                            }
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            categories: categories,
                            labels: {
                                style: {
                                    color: 'white'
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: null
                            },
                            labels: {
                                style: {
                                    color: 'white'
                                }
                            }
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 0,
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: 0,
                                        color: 'white'
                                    }
                                }
                            },
                            column: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            fetchFilter(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        exporting: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true,
                            backgroundColor: '#fff',
                            fontSize:'13px'
                        },
                        series: [{
                            name: 'DM Obsolete',
                            data: series_dm_obsolete,
                            color: '#A61111',
                            stacking:'normal'
                        },{
                            name: 'DM Active',
                            data: series_dm_active,
                            color: '#3CB371',
                            stacking:'normal'
                        }]
                    });

                    Highcharts.chart('container3', {
                        chart: {
                            type: 'column',
                            borderRadius: 10,
                            borderColor: 'black',
                            borderWidth: 1,
                            backgroundColor: {
                                linearGradient: {
                                    x1: 0,
                                    y1: 0,
                                    x2: 1,
                                    y2: 1
                                },
                                stops: [
                                    [0, '#BA55D3'],
                                    [1, '#9932CC']
                                ]
                            }
                        },
                        title: {
                            text: null
                        },
                        xAxis: {
                            categories: categories,
                            labels: {
                                style: {
                                    color: 'white'
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: null
                            },
                            labels: {
                                style: {
                                    color: 'white'
                                }
                            }
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 0,
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y}',
                                    style: {
                                        textOutline: 0,
                                        color: 'white'
                                    }
                                }
                            },
                            column: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            fetchFilter(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        exporting: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true,
                            backgroundColor: '#fff',
                            fontSize:'13px'
                        },
                        series: [{
                            name: 'DL Obsolete',
                            data: series_dl_obsolete,
                            color: '#A61111',
                            stacking:'normal'
                        },{
                            name: 'DL Active',
                            data: series_dl_active,
                            color: '#3CB371',
                            stacking:'normal'
                        }]
                    });

                    // $('#tableDocument').DataTable({
                    //     'dom': 'Bfrtip',
                    //     'responsive': true,
                    //     'lengthMenu': [
                    //         [10, 25, 50, -1],
                    //         ['10 rows', '25 rows', '50 rows', 'Show all']
                    //     ],
                    //     'buttons': {
                    //         buttons: [{
                    //                 extend: 'pageLength',
                    //                 className: 'btn btn-default',
                    //             },
                    //             {
                    //                 extend: 'copy',
                    //                 className: 'btn btn-success',
                    //                 text: '<i class="fa fa-copy"></i> Copy',
                    //                 exportOptions: {
                    //                     columns: ':not(.notexport)'
                    //                 }
                    //             },
                    //             {
                    //                 extend: 'excel',
                    //                 className: 'btn btn-info',
                    //                 text: '<i class="fa fa-file-excel-o"></i> Excel',
                    //                 exportOptions: {
                    //                     columns: ':not(.notexport)'
                    //                 }
                    //             },
                    //             {
                    //                 extend: 'print',
                    //                 className: 'btn btn-warning',
                    //                 text: '<i class="fa fa-print"></i> Print',
                    //                 exportOptions: {
                    //                     columns: ':not(.notexport)'
                    //                 }
                    //             },
                    //         ]
                    //     },
                    //     'paging': true,
                    //     'lengthChange': true,
                    //     'searching': true,
                    //     'ordering': true,
                    //     'order': [],
                    //     'info': true,
                    //     'autoWidth': true,
                    //     "sPaginationType": "full_numbers",
                    //     "bJQueryUI": true,
                    //     "bAutoWidth": false,
                    //     "processing": true
                    // });
                    $('#tableDocument tfoot th').each( function () {
                        var title = $(this).text();
                        $(this).html( '<input id="search" style="text-align: center;color:black; width: 100%" type="text" placeholder="Search '+title+'" size="10"/>' );
                    } );

                    var table = $('#tableDocument').DataTable({
                    'dom': 'Bfrtip',
                        'responsive':true,
                        'lengthMenu': [
                        [ 15, 25, -1 ],
                        [ '15 rows', '25 rows', 'Show all' ]
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
                        "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true,
                    'ordering' :false,
                        initComplete: function() {
                        this.api()
                            .columns([1, 2, 3])
                            .every(function(dd) {
                                var column = this;
                                var theadname = $("#tableDocument th").eq([dd]).text();
                                var select = $(
                                        '<select style="color:black;"><option value="" style="font-size:11px;">All</option></select>'
                                    )
                                    .appendTo($(column.footer()).empty())
                                    .on('change', function() {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());

                                        column.search(val ? '^' + val + '$' : '', true, false)
                                            .draw();
                                    });
                                column
                                    .data()
                                    .unique()
                                    .sort()
                                    .each(function(d, j) {
                                        var vals = d;
                                        if ($("#tableDocument th").eq([dd]).text() == 'Category') {
                                            vals = d.split(' ')[0];
                                        }
                                        select.append('<option style="font-size:11px;color:black;" value="' +
                                            d + '">' + vals + '</option>');
                                    });
                            });
                        },
                    });

                    table.columns().every( function () {
                        var that = this;
                        $( '#search', this.footer() ).on( 'keyup change', function () {
                            if ( that.search() !== this.value ) {
                                that
                                .search( this.value )
                                .draw();
                            }
                        } );
                    } );

                    $('#tableDocument tfoot tr').appendTo('#tableDocument thead');
                    $('#loading').hide();
                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!',result.message);
                }
            });
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
