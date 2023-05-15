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

        #loading,
        #error {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            Detail {{ $page }}
            <small style="font-weight: bold;"><i class="glyphicon glyphicon-chevron-right"></i></small>
            <span style="font-weight: bold;">{{ $time->id_checkSheet }}</span>
        </h1>
    </section>
@endsection
@section('content')
    <section class="content">
        @if ($errors->has('password'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                {{ $errors->first() }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon " style="background-color: rgba(126,86,134,.7);"><i
                            class="fa  fa-paper-plane-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">CONSIGNEE & ADDRESS</span>
                        <span class="info-box-number">{{ $time->destination }}</span>
                        <span class="info-box-text">STATUS</span>
                        @if ($time->status != null)
                            <span class=" label label-success ">Checked</span>
                        @else
                            <span class="label label-warning">Unchecked</span>
                        @endif
                        @if ($time->status != null)
                            <span class="info-box-text">INSPECTOR</span>
                            <span class="info-box-number">
                                @if (isset($time->user3->name))
                                    {{ $time->user3->name }}
                                @else
                                    Not registered
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i
                            class="fa fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">SIHHPED FROM </span>
                        <span class="info-box-number">{{ $time->shipped_from }}</span>
                        <span class="info-box-text">SIHHPED TO </span>
                        <span class="info-box-number">{{ $time->shipped_to }}</span>
                        <span class="info-box-text">CARRIER </span>
                        <span class="info-box-number">
                            @if (isset($time->shipmentcondition->shipment_condition_name))
                                {{ $time->shipmentcondition->shipment_condition_name }}
                            @else
                                -
                            @endif
                        </span>
                        <span class="info-box-text">ON OR ABOUT </span>
                        <span class="info-box-number">{{ date('d-M-Y', strtotime($time->etd_sub)) }}</span>
                        <span class="info-box-text">CARIER</span>
                        <span class="info-box-number">{{ $time->ct_size }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i
                            class="fa fa-envelope-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">INVOICE NO </span>
                        <span class="info-box-number">{{ $time->invoice }}</span>
                        <span class="info-box-text">DATE </span>
                        <span class="info-box-number">{{ date('d-M-Y', strtotime($time->invoice_date)) }}</span>
                        <span class="info-box-text">PAYMENT </span>
                        <span class="info-box-number">{{ $time->payment }}</span>
                        <span class="info-box-text">SHIPPER </span>
                        <span class="info-box-number">PT YMPI</span>
                    </div>
                </div>
            </div>

            <div class="col-md-1 ">
                <a href="{{ url("print/CheckSheet/{$time->id}") }}" class="btn btn-primary btn-lg" style="color:white"><i
                        class="fa fa-print"></i> Print {{ $page }}</a><br><br>
                <a target="_blank" href="{{ url("printsurat/CheckSheet/{$time->id}") }}" class="btn btn-warning btn-lg"
                    style="color:white"><i class="fa fa-print"></i> Print Surat Jalan</a><br><br>
                <a data-toggle="modal" data-target="#importModal" class="btn btn-success btn-lg" style="color:white"><i
                        class="fa fa-folder-open-o"></i> Upload {{ $page }}</a>
            </div><br>
        </div>

        <div class="box box-solid">
            <div class="box-body">
                <div class="progress" style="height: 100px; margin: 0 auto">
                    <div class="progress-bar progress-bar-yellow progress-bar-striped active" id="progress_bar_delivery">
                    </div>
                </div>
            </div>
        </div>



        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-left ">
                <li class="active">
                    <a href="#seal" data-toggle="tab">
                        <b>1. SEAL INFORMATION</b>
                    </a>
                </li>
                <li>
                    <a href="#container" data-toggle="tab">
                        <b>2. CONDITION OF CONTAINER</b>
                    </a>
                </li>
                <li>
                    <a href="#cargo" data-toggle="tab">
                        <b>3. CONDITION OF CARGO</b>
                    </a>
                </li>
                <li>
                    <a href="#closure" data-toggle="tab">
                        <b>4. CONTAINER CLOSURE </b>
                    </a>
                </li>
            </ul>

            <p id="id_checkSheet_master" hidden>{{ $time->id_checkSheet }}</p>
            <p id="id_checkSheet_master_id" hidden>{{ $time->id }}</p>
            <input type="hidden" id="driver_photo_hidden" value="{{ $photo }}">
            <input type="hidden" id="seal_photo_hidden" value="{{ $seal_photo }}">
            <input type="hidden" id="container_photo_hidden" value="{{ $container_photo }}">
            <input type="hidden" id="shipment_condition" value="{{ $time->carier }}">
            <input type="hidden" id="checksheet_id" value="{{ $time->id }}">

            <div class="tab-content no-padding">
                <div class="chart tab-pane active" id="seal" style="position: relative;">
                    <div class="box-body">
                        <div class="col-xs-8 col-xs-offset-2" id="driver_sea">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead style="background-color: rgba(126,86,134,.7);">
                                        <tr>
                                            <th colspan="2">SEAL INSPECTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="vertical-align: middle;">DRIVER NAME</th>
                                            <th>
                                                <input type="text" name="driver_name" id="driver_name"
                                                    class="form-control" value="{{ $time->driver_name }}" disabled>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">DRIVER PHOTO</th>
                                            <th>
                                                <button class="btn btn-primary btn-lg" id="btnImage"
                                                    style="font-size: 1.5vw; width: 300px; height: 200px;" disabled><i
                                                        class="fa  fa-file-image-o"></i>&nbsp;&nbsp;&nbsp;Photo Not
                                                    Found</button>
                                                <img width="150px" id="driver_photo" src=""
                                                    style="display: none; width: 300px; height: 200px;"
                                                    alt="your image" />
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">SEAL NO.</th>
                                            <th>
                                                <input type="text" name="seal_number" id="seal_number"
                                                    class="form-control" value="{{ $time->seal_number }}" disabled>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">CONTAINER NO.</th>
                                            <th>
                                                <input type="text" name="countainer_number" id="countainer_number"
                                                    class="form-control" value="{{ $time->countainer_number }}" disabled>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">NO POL</th>
                                            <th>
                                                <input type="text" name="no_pol" id="no_pol" class="form-control"
                                                    value="{{ $time->no_pol }}" disabled>
                                            </th>
                                        </tr>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <div class="col-xs-8 col-xs-offset-2" id="driver_non_sea"
                            style="margin-bottom: 100px; margin-top: 100px;">
                            <center>
                                <h1 style="text-transform: uppercase;">Seal information is only for shipping condition by
                                    Sea</h1>
                            </center>
                        </div>
                    </div>
                </div>
                <div class="chart tab-pane" id="container" style="position: relative;">
                    <div class="box-body">
                        @if ($time->id >= 2233)
                            <div class="col-xs-12" id="checklist_sea">
                                <div class="col-xs-10 col-xs-offset-1" style="margin-top: 1%; padding:0px;">
                                    <div class="col-xs-4">
                                        <table style="border: none; width: 100%">
                                            <tr>
                                                <th style="width: 30%; font-size: 16px">Check By</th>
                                                @php
                                                    $check_by = '-';
                                                    for ($i = 0; $i < count($employees); $i++) {
                                                        if ($employees[$i]->employee_id == $time->checklist_pic_by) {
                                                            $check_by = $employees[$i]->name;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                <th style="width: 70%; font-size: 16px">: {{ $check_by }}</th>
                                            </tr>
                                            <tr>
                                                <th style="width: 30%; font-size: 16px">Known By</th>
                                                @php
                                                    $known_by = '-';
                                                    for ($i = 0; $i < count($employees); $i++) {
                                                        if ($employees[$i]->employee_id == $time->checklist_known_by) {
                                                            $known_by = $employees[$i]->name;
                                                            break;
                                                        }
                                                    }
                                                @endphp
                                                <th style="width: 70%; font-size: 16px">: {{ $known_by }}</th>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-xs-10 col-xs-offset-1" style="margin-top: 1%; padding:0px;">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead style="background-color: #a488aa;">
                                                <tr>
                                                    <th style="font-size: 16px; border: 1.5px solid #333333;"
                                                        colspan="2">
                                                        AREA OF INSPECTION
                                                    </th>
                                                    <th style="font-size: 16px; border: 1.5px solid #333333;"
                                                        colspan="2">
                                                        RESULT
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="font-size: 14px; border: 1.5px solid #333333;">AREA</th>
                                                    <th style="font-size: 14px; border: 1.5px solid #333333;">CHECKLIST
                                                    </th>
                                                    <th style="font-size: 14px; border: 1.5px solid #333333;">RESULT</th>
                                                    <th style="font-size: 14px; border: 1.5px solid #333333;">NOTE</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $count_area = 0;
                                                    $count_area_ck = 0;
                                                    $count = 0;
                                                    $row = 0;
                                                    
                                                    $photo_requirment;
                                                    
                                                @endphp
                                                @foreach ($checklist as $ck)
                                                    @php
                                                        ++$count;
                                                        
                                                        if ($ck->area_setting != 'hidden') {
                                                            $row += $ck->area_setting;
                                                        }
                                                        
                                                        if ($ck->area_setting != 'hidden') {
                                                            $count_area++;
                                                            $count_area_ck = 1;
                                                        
                                                            $photo_requirment = $ck->photo_requirment;
                                                        } else {
                                                            $count_area_ck++;
                                                        }
                                                        
                                                    @endphp
                                                    <tr>
                                                        @if ($ck->area_setting != 'hidden')
                                                            <td height="50"
                                                                style="text-align: center; font-size: 20px; border-bottom: 1px solid #333333 !important; border-left: 1px solid #333333 !important;"
                                                                rowspan="{{ $ck->area_setting + 1 }}" width="5%">
                                                                <br><b>{{ $count_area }}.
                                                                    {{ $ck->area }}</b><br>&nbsp;
                                                            </td>
                                                        @else
                                                            <td height="50" hidden>
                                                                <br><b>{{ $ck->area }}</b><br>&nbsp;
                                                            </td>
                                                        @endif

                                                        <td height="50" style="text-align: left;" width="27.5%">
                                                            <br>{{ $count_area }}.{{ $count_area_ck }}.
                                                            {{ $ck->point_check }}
                                                            @if ($ck->point_check == 'KONTAINER SESUAI KEBUTUHAN')
                                                                ({{ $time->ct_size }})
                                                            @endif
                                                            <br>&nbsp;
                                                        </td>


                                                        <td height="50" style="text-align: center;" width="5%">
                                                            @if ($ck->result == 'OK')
                                                                <br>
                                                                <span
                                                                    style="font-size: 20px; font-weight: bold; color: green;">OK</span>
                                                            @elseif($ck->result == 'NG')
                                                                <br>
                                                                <span
                                                                    style="font-size: 20px; font-weight: bold; color: red;">NG</span>
                                                            @else
                                                                <br>
                                                                <span
                                                                    style="font-size: 20px; font-weight: bold; color: black;">-</span>
                                                            @endif
                                                        </td>

                                                        <td height="50"
                                                            style="text-align: left; border-right: 1px solid #333333;"
                                                            width="20%">
                                                            <textarea class="form-control" type="text" rows="3" id="note_{{ $ck->id }}" placeholder="Note ..."
                                                                disabled></textarea>
                                                        </td>



                                                        @if ($count == $row)
                                                    <tr>
                                                        <td style="text-align: center; border-bottom: 1px solid #333333 !important; border-right: 1px solid #333333 !important;"
                                                            colspan="4" height="150">
                                                            @if ($ck->area == 'SERANGGA')
                                                                <img width="125px"
                                                                    src="{{ url('files/checksheet/guidelines/container_head_right.png') }}"
                                                                    style="height: 125px; width: 100px;" />
                                                            @endif
                                                            @if ($ck->area == 'DEPAN')
                                                                <img width="125px"
                                                                    src="{{ url('files/checksheet/guidelines/depan.jpg') }}"
                                                                    style="height: 125px; width: 100px;" />
                                                            @endif
                                                            @if ($ck->area == 'PINTU')
                                                                <img width="125px"
                                                                    src="{{ url('files/checksheet/guidelines/pintu.jpg') }}"
                                                                    style="height: 125px; width: 150px;" />
                                                            @endif
                                                            @php
                                                                $count_foto = 0;
                                                            @endphp
                                                            @php
                                                                for ($h = 0; $h < count($checklist_photo); $h++) {
                                                                    if ($checklist_photo[$h]->area == $ck->area) {
                                                                        if ($checklist_photo[$h]->area == 'PINTU, CORNER CASTING DAN FRAME LUAR') {
                                                                            if ($checklist_photo[$h]->area_photo_id == 1) {
                                                                                print_r('<img width="125px" src="' . url('files/checksheet/guidelines/pintu.jpg') . '" style="height: 125px; width: 250px;"/>');
                                                                            }
                                                                
                                                                            if ($checklist_photo[$h]->area_photo_id == 2) {
                                                                                print_r('<img width="125px" src="' . url('files/checksheet/guidelines/corner.jpg') . '" style="height: 125px; width: 250px;"/>');
                                                                                if ($checklist_photo[$h]->source != null) {
                                                                                    print_r('<img style="margin: 0.25%; font-size: 20px;  width: 200px; height: 125px" src="' . url('files/checksheet/checklist_evidence/' . $checklist_photo[$h]->source) . '" class="user-image">');
                                                                                } else {
                                                                                    print_r('<button class="btn btn-default btn-lg" style="margin: 0.25%; font-size: 20px;  width: 200px; height: 125px" disabled><i class="fa  fa-unlink"></i>&nbsp;&nbsp;&nbsp;Evidence<br>Not Found</button>');
                                                                                }
                                                                                print_r('<div class="col-xs-12" style="margin-bottom: 1%;"></div>');
                                                                            } else {
                                                                                if ($checklist_photo[$h]->source != null) {
                                                                                    print_r('<img style="margin: 0.25%; font-size: 20px;  width: 200px; height: 125px" src="' . url('files/checksheet/checklist_evidence/' . $checklist_photo[$h]->source) . '" class="user-image">');
                                                                                } else {
                                                                                    print_r('<button class="btn btn-default btn-lg" style="margin: 0.25%; font-size: 20px;  width: 200px; height: 125px" disabled><i class="fa  fa-unlink"></i>&nbsp;&nbsp;&nbsp;Evidence<br>Not Found</button>');
                                                                                }
                                                                                print_r('<div class="col-xs-12" style="margin-bottom: 1%;"></div>');
                                                                                print_r('<br>');
                                                                            }
                                                                        } else {
                                                                            if ($checklist_photo[$h]->source != null) {
                                                                                print_r('<img style="margin: 0.25%; font-size: 20px;  width: 200px; height: 125px" src="' . url('files/checksheet/checklist_evidence/' . $checklist_photo[$h]->source) . '" class="user-image">');
                                                                            } else {
                                                                                print_r('<button class="btn btn-default btn-lg" style="margin: 0.25%; font-size: 20px;  width: 200px; height: 125px" disabled><i class="fa  fa-unlink"></i>&nbsp;&nbsp;&nbsp;Evidence<br>Not Found</button>');
                                                                            }
                                                                            print_r('<div class="col-xs-12" style="margin-bottom: 1%;"></div>');
                                                                        }
                                                                    }
                                                                }
                                                            @endphp
                                                            @if (
                                                                $ck->area == 'DINDING KIRI' ||
                                                                    $ck->area == 'DINDING KANAN' ||
                                                                    $ck->area == 'UKURAN' ||
                                                                    $ck->area == 'LANTAI' ||
                                                                    $ck->area == 'ATAP')
                                                                <img width="125px"
                                                                    src="{{ url('files/checksheet/guidelines/container_head_left.png') }}"
                                                                    style="height: 125px; width: 100px;" />
                                                            @endif
                                                        </td>
                                                @endif
                        @endforeach
                        </tr>
                        </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="col-xs-8 col-xs-offset-2" id="checklist_non_sea"
                style="margin-bottom: 100px; margin-top: 100px;">
                <center>
                    <h1 style="text-transform: uppercase;">container condition Checklist is only for
                        shipping
                        condition by Sea</h1>
                </center>
            </div>
        @else
            <div class="col-xs-8">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead style="background-color: rgba(126,86,134,.7);">
                            <tr>
                                <th colspan="2">AREA OF INSPECTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $svn_chk = 0; @endphp
                            @foreach ($container as $nomor => $container)
                                @php $svn_chk++; @endphp

                                <tr>
                                    <input type="text" id="count" value="{{ $loop->count }}" hidden></input>
                                    @if ($svn_chk == 1)
                                        <td rowspan="9">
                                            @php
                                                $p = 'images/7poin.png';
                                            @endphp
                                            <img src="{{ url($p) }}" class="user-image" alt="7 Poin"
                                                align="middle" width="300">
                                        </td>
                                    @endif
                                    <td height="100"><br>{{ $container->area }}<br>&nbsp;</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-xs-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead style="background-color: rgba(126,86,134,.7);">
                            <th>ACCEPTABLE</th>
                            <th>REMARK</th>
                        </thead>
                        @foreach ($inspection as $nomor => $inspection)
                            <tr>
                                <td height="100">
                                    @if ($inspection->inspection1 == 1)
                                        <button type="button" id="good1" class="btn btn-block btn-success btn-sm"
                                            onclick="good(1)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng1" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(1)" style="display: none;">NOT GOOD</button>
                                    @else
                                        <button type="button" id="ng1" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(1)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good1" class="btn btn-block btn-success btn-sm"
                                            onclick="good(1)" style="display: none;">GOOD</button>
                                    @endif
                                    <p id="inspection1" hidden></p>
                                </td>
                                <td>
                                    @if ($inspection->remark1 != '')
                                        <TEXTAREA id="remark1" onchange="addInspection2(1)"readonly>{{ $inspection->remark1 }}</TEXTAREA>
                                    @else
                                        <TEXTAREA id="remark1" onchange="addInspection2(1)"readonly></TEXTAREA>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td height="100">
                                    @if ($inspection->inspection2 == 1)
                                        <button type="button" id="good2" class="btn btn-block btn-success btn-sm"
                                            onclick="good(2)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng2" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(2)" style="display: none;">NOT GOOD</button>
                                    @else
                                        <button type="button" id="ng2" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(2)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good2" class="btn btn-block btn-success btn-sm"
                                            onclick="good(2)" style="display: none;">GOOD</button>
                                    @endif
                                    <p id="inspection2" hidden></p>
                                </td>
                                <td>
                                    @if ($inspection->remark2 != '')
                                        <TEXTAREA id="remark2" onchange="addInspection2(2)"readonly>{{ $inspection->remark2 }}</TEXTAREA>
                                    @else
                                        <TEXTAREA id="remark2" onchange="addInspection2(2)"readonly></TEXTAREA>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td height="100">
                                    @if ($inspection->inspection3 == 1)
                                        <button type="button" id="good3" class="btn btn-block btn-success btn-sm"
                                            onclick="good(3)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng3" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(3)" style="display: none;">NOT GOOD</button>
                                    @else
                                        <button type="button" id="ng3" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(3)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good3" class="btn btn-block btn-success btn-sm"
                                            onclick="good(3)" style="display: none;">GOOD</button>
                                    @endif
                                    <p id="inspection3" hidden></p>
                                </td>
                                <td>
                                    @if ($inspection->remark3 != '')
                                        <TEXTAREA id="remark3" onchange="addInspection2(3)"readonly>{{ $inspection->remark3 }}</TEXTAREA>
                                    @else
                                        <TEXTAREA id="remark3" onchange="addInspection2(3)"readonly></TEXTAREA>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td height="100">
                                    @if ($inspection->inspection4 == 1)
                                        <button type="button" id="good4" class="btn btn-block btn-success btn-sm"
                                            onclick="good(4)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng4" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(4)" style="display: none;">NOT GOOD</button>
                                    @else
                                        <button type="button" id="ng4" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(4)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good4" class="btn btn-block btn-success btn-sm"
                                            onclick="good(4)" style="display: none;">GOOD</button>
                                    @endif
                                    <p id="inspection4" hidden></p>
                                </td>
                                <td>
                                    @if ($inspection->remark4 != '')
                                        <TEXTAREA id="remark4" onchange="addInspection2(4)"readonly>{{ $inspection->remark4 }}</TEXTAREA>
                                    @else
                                        <TEXTAREA id="remark4" onchange="addInspection2(4)"readonly></TEXTAREA>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td height="100">
                                    @if ($inspection->inspection5 == 1)
                                        <button type="button" id="good5" class="btn btn-block btn-success btn-sm"
                                            onclick="good(5)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng5" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(5)" style="display: none;">NOT GOOD</button>
                                    @else
                                        <button type="button" id="ng5" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(5)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good5" class="btn btn-block btn-success btn-sm"
                                            onclick="good(5)" style="display: none;">GOOD</button>
                                    @endif
                                    <p id="inspection5" hidden></p>
                                </td>
                                <td>
                                    @if ($inspection->remark5 != '')
                                        <TEXTAREA id="remark5" onchange="addInspection2(5)"readonly>{{ $inspection->remark5 }}</TEXTAREA>
                                    @else
                                        <TEXTAREA id="remark5" onchange="addInspection2(5)"readonly></TEXTAREA>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td height="100">
                                    @if ($inspection->inspection6 == 1)
                                        <button type="button" id="good6" class="btn btn-block btn-success btn-sm"
                                            onclick="good(6)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng6" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(6)" style="display: none;">NOT GOOD</button>
                                    @else
                                        <button type="button" id="ng6" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(6)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good6" class="btn btn-block btn-success btn-sm"
                                            onclick="good(6)" style="display: none;">GOOD</button>
                                    @endif
                                    <p id="inspection6" hidden></p>
                                </td>
                                <td>
                                    @if ($inspection->remark6 != '')
                                        <TEXTAREA id="remark6" onchange="addInspection2(6)"readonly>{{ $inspection->remark6 }}</TEXTAREA>
                                    @else
                                        <TEXTAREA id="remark6" onchange="addInspection2(6)"readonly></TEXTAREA>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td height="100">
                                    @if ($inspection->inspection7 == 1)
                                        <button type="button" id="good7" class="btn btn-block btn-success btn-sm"
                                            onclick="good(7)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng7" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(7)" style="display: none;">NOT GOOD</button>
                                    @else
                                        <button type="button" id="ng7" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(7)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good7" class="btn btn-block btn-success btn-sm"
                                            onclick="good(7)" style="display: none;">GOOD</button>
                                    @endif
                                    <p id="inspection7" hidden></p>
                                </td>
                                <td>
                                    @if ($inspection->remark7 != '')
                                        <TEXTAREA id="remark7" onchange="addInspection2(7)"readonly>{{ $inspection->remark7 }}</TEXTAREA>
                                    @else
                                        <TEXTAREA id="remark7" onchange="addInspection2(7)"readonly></TEXTAREA>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td height="100">
                                    @if ($inspection->inspection8 == 1)
                                        <button type="button" id="good8" class="btn btn-block btn-success btn-sm"
                                            onclick="good(8)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng8" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(8)" style="display: none;">NOT GOOD</button>
                                    @else
                                        <button type="button" id="ng8" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(8)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good8" class="btn btn-block btn-success btn-sm"
                                            onclick="good(8)" style="display: none;">GOOD</button>
                                    @endif
                                    <p id="inspection8" hidden></p>
                                </td>
                                <td>
                                    @if ($inspection->remark8 != '')
                                        <TEXTAREA id="remark8" onchange="addInspection2(8)" readonly>{{ $inspection->remark8 }}</TEXTAREA>
                                    @else
                                        <TEXTAREA id="remark8" onchange="addInspection2(8)" readonly></TEXTAREA>
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td height="100">
                                    @if ($inspection->inspection9 == 1)
                                        <button type="button" id="good9" class="btn btn-block btn-success btn-sm"
                                            onclick="good(9)" style="display: block;">GOOD</button>
                                        <button type="button" id="ng9" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(9)" style="display: none;">NOT GOOD</button>
                                    @else
                                        <button type="button" id="ng9" class="btn btn-block btn-danger btn-sm"
                                            onclick="ng(9)" style="display: block;">NOT GOOD</button>
                                        <button type="button" id="good9" class="btn btn-block btn-success btn-sm"
                                            onclick="good(9)" style="display: none;">GOOD</button>
                                    @endif
                                    <p id="inspection9" hidden></p>
                                </td>
                                <td>
                                    @if ($inspection->remark9 != '')
                                        <TEXTAREA id="remark9" onchange="addInspection2(9)" readonly>{{ $inspection->remark9 }}</TEXTAREA>
                                    @else
                                        <TEXTAREA id="remark9" onchange="addInspection2(9)" readonly></TEXTAREA>
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                    </table>
                </div>
            </div>
            @endif


        </div>
        </div>
        <div class="chart tab-pane " id="cargo" style="position: relative;">
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table no-margin table-bordered table-striped">
                        <thead>
                            <tr style="background-color: rgba(126,86,134,.7);">
                                <th style="vertical-align: middle;">DEST</th>
                                <th style="vertical-align: middle;">INVOICE</th>
                                <th style="vertical-align: middle;">GMC</th>
                                <th style="vertical-align: middle;">DESCRIPTION OF GOODS </th>
                                <th style="vertical-align: middle;">MARKING NO.</th>
                                <th style="vertical-align: middle;" colspan="2">PACKAGE</th>
                                <th style="vertical-align: middle;" colspan="2">QUANTITY</th>
                                <th style="vertical-align: middle;">QTY BOX OR PACKAGE</th>
                                <th style="vertical-align: middle;">Total</th>
                                <th style="vertical-align: middle;">Confirm</th>
                                <th style="vertical-align: middle;" colspan="2">Diff</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach ($detail as $nomor => $detail)
                                    <td width="5%">{{ $detail->destination }}</td>
                                    <td width="8%">{{ $detail->invoice }}</td>
                                    <td width="8%">{{ $detail->gmc }}</td>
                                    <td>{{ $detail->goods }}</td>
                                    <td width="2%">{{ $detail->marking }}</td>
                                    @if ($detail->package_set == 'PL')
                                        <td class="PLT" width="5%">{{ $detail->package_qty }}</td>
                                    @elseif($detail->package_set == 'C/T')
                                        <td class="CTN" width="5%">{{ $detail->package_qty }}</td>
                                    @else
                                        <td class="{{ $detail->package_qty }}" width="5%">
                                            {{ $detail->package_qty }}</td>
                                    @endif
                                    <td width="2%">{{ $detail->package_set }}</td>
                                    <td class="{{ $detail->qty_set }}" width="5%">{{ $detail->qty_qty }}
                                    </td>
                                    <td width="2%">{{ $detail->qty_set }} </td>
                                    <td width="2%">{{ $detail->box }} </td>

                                    <td width="2%">
                                        <p id="total{{ $nomor + 1 }}">{{ $detail->package_qty }}</p>
                                    </td>
                                    <td width="2%">
                                        <p id="inc{{ $nomor + 1 }}">{{ $detail->confirm }}</p>
                                    </td>
                                    <td width="2%">
                                        <p id="diff{{ $nomor + 1 }}">{{ $detail->diff }}</p>
                                    </td>
                                    @if ($detail->package_set == '-')
                                        <td width="2%">
                                            @if ($detail->bara == '1')
                                                <span data-toggle="tooltip" class="badge bg-green"
                                                    id="y{{ $nomor + 1 }}" style="display: none;"><i
                                                        class="fa fa-fw fa-check"></i></span>
                                                <span data-toggle="tooltip" class="badge bg-red"
                                                    id="n{{ $nomor + 1 }}" style="display: block;"><i
                                                        class="fa fa-fw  fa-close"></i></span>
                                            @elseif($detail->bara == '0')
                                                <span data-toggle="tooltip" class="badge bg-green"
                                                    id="y{{ $nomor + 1 }}" style="display: block;"><i
                                                        class="fa fa-fw fa-check"></i></span>
                                                <span data-toggle="tooltip" class="badge bg-red"
                                                    id="n{{ $nomor + 1 }}" style="display: none;"><i
                                                        class="fa fa-fw  fa-close"></i></span>
                                            @endif
                                        </td>
                                    @else
                                        @if ($detail->diff == '0')
                                            <td width="2%">
                                                <span data-toggle="tooltip" class="badge bg-green"
                                                    id="y{{ $nomor + 1 }}" style="display: block;"><i
                                                        class="fa fa-fw fa-check"></i></span>
                                                <span data-toggle="tooltip" class="badge bg-red"
                                                    id="n{{ $nomor + 1 }}" style="display: none;"><i
                                                        class="fa fa-fw  fa-close"></i></span>
                                            </td>
                                        @else
                                            <td>
                                                <span data-toggle="tooltip" class="badge bg-red"
                                                    id="n{{ $nomor + 1 }}" style="display: block;"><i
                                                        class="fa fa-fw  fa-close"></i></span>
                                                <span data-toggle="tooltip" class="badge bg-green"
                                                    id="y{{ $nomor + 1 }}" style="display: none;"><i
                                                        class="fa fa-fw fa-check"></i></span>
                                            </td>
                                        @endif
                                    @endif
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="background-color: RGB(252, 248, 227);">
                            <tr>
                                <th colspan="5" rowspan="2">
                                    <CENTER>REMAIN PALLET & C/T</CENTER>
                                </th>
                                <th>
                                    <p id="plte"></p>
                                </th>
                                <th>PL</th>
                                <th>
                                    <p id="sete"></p>
                                </th>
                                <th>SET</th>
                                <th colspan="5"></th>
                            </tr>
                            <tr>
                                <th>
                                    <p id="ctne"></p>
                                </th>
                                <th>C/T</th>
                                <th>
                                    <p id="pcse"></p>
                                </th>
                                <th>PC</th>
                                <th colspan="5"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="chart tab-pane" id="closure" style="position: relative;">
            <div class="box-body">
                <div class="col-xs-8 col-xs-offset-2" id="seal_sea">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th colspan="2">CLOSURE CONTAINER INSPECTION</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th style="vertical-align: middle;">CONTAINER NO.</th>
                                    <th>
                                        <input type="text" name="closure_countainer_number"
                                            id="closure_countainer_number" class="form-control" disabled>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="vertical-align: middle;">SEAL NO.</th>
                                    <th>
                                        <input type="text" name="closure_seal_number" id="closure_seal_number"
                                            class="form-control" disabled>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="vertical-align: middle;">SEAL PHOTO</th>
                                    <th>
                                        <button class="btn btn-primary btn-lg" id="btnSeal"
                                            style="font-size: 1.5vw; width: 300px; height: 200px;" disabled><i
                                                class="fa  fa-file-image-o"></i>&nbsp;&nbsp;&nbsp;Photo Not
                                            Found</button>
                                        <img width="150px" id="seal_photo" src=""
                                            style="display: none; width: 300px; height: 200px;" alt="your image" />
                                    </th>
                                </tr>
                                <tr>
                                    <th style="vertical-align: middle;">CONTAINER PHOTO</th>
                                    <th>
                                        <button class="btn btn-primary btn-lg" id="btnContainer"
                                            style="font-size: 1.5vw; width: 300px; height: 200px;" disabled><i
                                                class="fa  fa-file-image-o"></i>&nbsp;&nbsp;&nbsp;Photo Not
                                            Found</button>
                                        <img width="150px" id="container_photo" src=""
                                            style="display: none; width: 300px; height: 200px;" alt="your image" />
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xs-8 col-xs-offset-2" id="seal_non_sea" style="margin-bottom: 100px; margin-top: 100px;">
                    <center>
                        <h1 style="text-transform: uppercase;">Closure information is only for shipping condition
                            by Sea</h1>
                    </center>
                </div>
            </div>
        </div>
        </div>

        </div>

        <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="importForm" method="post" action="{{ url('importDetail/CheckSheet') }}"
                        enctype="multipart/form-data">
                        <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Import Confirmation</h4>
                            Format: [Destination][Invoice][Container Number][GMC][Goods][Marking No][Package Qty][Package
                            Set][Qty Qty][Qty Set]<br>
                            Sample: <a
                                href="{{ url('download/manual/import_check_sheet_detail.txt') }}">import_check_sheet_detail.txt</a>
                            Code: #Add
                        </div>
                        <div class="modal-body">
                            <center><input type="file" name="check_sheet_import" id="InputFile" accept="text/plain">
                            </center>
                            <input type="text" name="master_id" value="{{ $time->id_checkSheet }}" hidden>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                            <button id="modalImportButton" type="submit" class="btn btn-success">Import</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>

@endsection

@section('scripts')
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            persen();

            var checklist_id = $("#checklist_id").val();
            if (checklist_id < 2207) {
                var count = document.getElementById("count").value;
                document.getElementById("rows1").rowSpan = count;
            }
            $('#rows1').removeAttr('hidden');

            var plt = 0;
            var ctn = 0;
            var set = 0;
            var pcs = 0;
            $(".PLT").each(function() {
                plt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });
            $('#plte').html("" + plt);

            $(".CTN").each(function() {
                ctn += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });
            $('#ctne').html("" + ctn);

            $(".SET").each(function() {
                set += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });
            $('#sete').html("" + set);

            $(".PC").each(function() {
                pcs += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });
            $('#pcse').html("" + pcs);

            showDriverPhoto();
            showSealPhoto();
            showContainerPhoto();
            shipmentCondition();

        });

        function shipmentCondition() {
            var shipment_condition = $("#shipment_condition").val();
            console.log(shipment_condition);
            if (shipment_condition == 'C1') {
                $('#driver_sea').show();
                $('#driver_non_sea').hide();

                $('#checklist_sea').show();
                $('#checklist_non_sea').hide();

                $('#seal_sea').show();
                $('#seal_non_sea').hide();
            } else {
                $('#driver_sea').hide();
                $('#driver_non_sea').show();

                $('#checklist_sea').hide();
                $('#checklist_non_sea').show();

                $('#seal_sea').hide();
                $('#seal_non_sea').show();
            }
        }

        function showContainerPhoto() {
            var photo = $("#container_photo_hidden").val();

            if (photo != '') {
                $("#container_photo").show();
                $("#btnContainer").hide();
                $("#container_photo").attr('src', photo);
            } else {
                $("#container_photo").hide();
                $("#btnContainer").show();
            }
        }

        function showSealPhoto() {
            var photo = $("#seal_photo_hidden").val();
            var seal = $("#seal_number").val();
            var countainer = $("#countainer_number").val();

            if (photo != '') {
                $("#seal_photo").show();
                $("#btnSeal").hide();
                $("#seal_photo").attr('src', photo);

                $("#closure_seal_number").val(seal);
                $("#closure_countainer_number").val(countainer);
            } else {
                $("#seal_photo").hide();
                $("#btnSeal").show();

                $("#closure_seal_number").val('');
                $("#closure_countainer_number").val('');
            }
        }

        function showDriverPhoto() {
            var photo = $("#driver_photo_hidden").val();

            if (photo != '') {
                $("#driver_photo").show();
                $("#btnImage").hide();
                $("#driver_photo").attr('src', photo);
            } else {
                $("#driver_photo").hide();
                $("#btnImage").show();
            }

        }

        function persen() {
            var id = document.getElementById("id_checkSheet_master").innerHTML;

            $.get('{{ url('persen/CheckSheet/') }}' + "/" + id, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
                if (xhr.status == 200) {
                    if (result.status) {
                        var jumlah = result.cek.toLocaleString() + '/' + result.total.toLocaleString();
                        var persen = ((result.cek / result.total) * 100).toFixed(2) + '%';
                        $('#progress_bar_delivery').html(jumlah + '(' + persen + ')');
                        $('#progress_bar_delivery').css('width', (result.cek / result.total) * 100 + '%');
                        $('#progress_bar_delivery').css('color', 'black');
                        $('#progress_bar_delivery').css('font-weight', 'bold');
                        $('#progress_bar_delivery').css('font-size', '50pt');
                        $('#progress_bar_delivery').css('line-height', '100px');

                        if (((result.cek / result.total) * 100).toFixed(0) <= 30) {
                            $('#progress_bar_delivery').removeClass('progress-bar-yellow').addClass(
                                'progress-bar-yellow');
                        } else if (((result.cek / result.total) * 100).toFixed(0) >= 31 && ((result.cek / result
                                .total) * 100).toFixed(0) <= 75) {
                            $('#progress_bar_delivery').removeClass('progress-bar-yellow').addClass(
                                'progress-bar-aqua');
                        } else {
                            $('#progress_bar_delivery').removeClass('progress-bar-yellow').addClass(
                                'progress-bar-green');
                        }

                    } else {
                        alert('Attempt to receive data failed');
                    }
                } else {
                    alert('Disconnected from server');
                }
            });
        }
    </script>
@stop
