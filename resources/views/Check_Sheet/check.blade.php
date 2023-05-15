@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css//bootstrap-toggle.min.css') }}" rel="stylesheet">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="viewport" content="initial-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

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

        .radio {
            display: inline-block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 16px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Hide the browser's default radio button */
        .radio input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        /* Create a custom radio button */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #ccc;
            border-radius: 50%;
        }

        /* On mouse-over, add a grey background color */
        .radio:hover input~.checkmark {
            background-color: #ccc;
        }

        /* When the radio button is checked, add a blue background */
        .radio input:checked~.checkmark {
            background-color: #2196F3;
        }

        /* Create the indicator (the dot/circle - hidden when not checked) */
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the indicator (dot/circle) when checked */
        .radio input:checked~.checkmark:after {
            display: block;
        }

        /* Style the indicator (dot/circle) */
        .radio .checkmark:after {
            top: 9px;
            left: 9px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@stop
@section('stylesheets')
    <link href="{{ url('css//bootstrap-toggle.min.css') }}" rel="stylesheet">
    <style type="text/css">
        tfoot input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $page }}
            <small style="font-weight: bold;"><i class="glyphicon glyphicon-chevron-right"></i></small>
            <span style="font-weight: bold;">{{ $time->id_checkSheet }}</span>
        </h1>
    </section>
@endsection
@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Uploading, please wait <i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>

        @if ($errors->has('password'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i
                            class="fa  fa-paper-plane-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">CONSIGNEE & ADDRESS</span>
                        <span class="info-box-number">{{ $time->destination }}</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 ">
                <div class="info-box">
                    <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i
                            class="fa fa-shopping-cart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">SHIPPED FROM </span>
                        <span class="info-box-number">{{ $time->shipped_from }}</span>
                        <span class="info-box-text">SHIPPED TO </span>
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
                        <span
                            class="info-box-number">{{ strtoupper(date('d-M-Y', strtotime($time->Stuffing_date))) }}</span>
                        <span class="info-box-text">PAYMENT </span>
                        <span class="info-box-number">{{ $time->payment }}</span>
                        <span class="info-box-text">SHIPPER </span>
                        <span class="info-box-number">PT YMPI</span>
                    </div>
                </div>
            </div>

            <div class="col-md-3 ">
                <div class="info-box">
                    <span class="info-box-icon "style="background-color: rgba(126,86,134,.7);"><i
                            class="fa fa-user"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">INPUTOR </span>
                        <span class="info-box-number">
                            @if (isset($time->user2->name))
                                {{ strtoupper($time->user2->name) }}
                            @else
                                Not registered
                            @endif
                        </span>
                        <span class="info-box-text">DATE </span>
                        <span class="info-box-number">{{ strtoupper(date('d-M-Y')) }}</span><br>

                        <form method="post" action="{{ url('save/CheckSheet') }}" name="kirim" id="kirim">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <input type="text" name="id" value="{{ $time->id_checkSheet }}" hidden>
                            <input type="text" name="status" value="1" hidden>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <P class="pull-right">
                    <a href="{{ url('index/CheckSheet') }}">
                        <button class="btn btn-warning btn-lg" style="color:white"><i class=" fa fa-backward "></i>
                            Back</button> &nbsp;
                    </a>
                    <button class="btn btn-success btn-lg" style="color:white" onclick="save()"><i
                            class="fa fa-save "></i>
                        Save</button>&nbsp;&nbsp;&nbsp;
                </P>
            </div>
        </div>

        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs pull-left">
                <li class="active">
                    <a href="#seal" data-toggle="tab">
                        <b>1. SEAL AND DRIVER</b>
                    </a>
                </li>
                <li>
                    <a href="#container" data-toggle="tab">
                        <b>2. CONDITION OF CONTAINER </b>
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
            <input type="hidden" id="seal_number_hidden" value="{{ $time->seal_number }}">
            <input type="hidden" id="countainer_number_hidden" value="{{ $time->countainer_number }}">
            <input type="hidden" id="shipment_condition" value="{{ $time->carier }}">

            <div class="tab-content no-padding">

                <div class="chart tab-pane active" id="seal" style="position: relative;">
                    <div class="box-body">
                        <div class="col-xs-8 col-xs-offset-2" id="driver_sea">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead style="background-color: #cddc39;">
                                        <tr>
                                            <th colspan="2">SEAL INSPECTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="vertical-align: middle;">DRIVER NAME</th>
                                            <th>
                                                <input type="text" name="driver_name" id="driver_name"
                                                    class="form-control" value="{{ $time->driver_name }}"
                                                    onchange="nomor('driver_name',this.value)">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">DRIVER PHOTO</th>
                                            <th>
                                                <input type="file" class="file" style="display:none"
                                                    onchange="readURL(this);" id="input_photo">
                                                <button class="btn btn-primary btn-lg" id="btnImage" value="Photo"
                                                    onclick="buttonImage(this)"
                                                    style="font-size: 1.5vw; width: 300px; height: 200px;"><i
                                                        class="fa  fa-file-image-o"></i>&nbsp;&nbsp;&nbsp;Photo
                                                    Driver</button>
                                                <img width="150px" id="driver_photo" src=""
                                                    onclick="buttonImage(this)"
                                                    style="display: none; width: 300px; height: 200px;"
                                                    alt="your image" />
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">SEAL NO.</th>
                                            <th>
                                                <input type="text" name="seal_number" id="seal_number"
                                                    class="form-control" value="{{ $time->seal_number }}"
                                                    onchange="nomor('seal_number',this.value)">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">CONTAINER NO.</th>
                                            <th>
                                                <input type="text" name="countainer_number" id="countainer_number"
                                                    class="form-control" value="{{ $time->countainer_number }}"
                                                    onchange="nomor('countainer_number',this.value)">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">NO POL</th>
                                            <th>
                                                <input type="text" name="no_pol" id="no_pol" class="form-control"
                                                    value="{{ $time->no_pol }}" onchange="nomor('no_pol',this.value)">
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
                        <div class="col-xs-12" id="checklist_sea">
                            <div class="col-xs-4 col-xs-offset-2">
                                <div class="input-group input-group-lg">
                                    <div class="input-group-addon" id="icon-serial"
                                        style="font-weight: bold; border-color: none; font-size: 18px;">
                                        <i class="fa fa-qrcode"></i>
                                    </div>
                                    <input type="text" class="form-control" placeholder="ID CARD PIC CHECK"
                                        id="pic_id" readonly>
                                    <span class="input-group-btn">
                                        <button style="font-weight: bold;" class="btn btn-primary btn-flat"
                                            id="btn_pic_id" onclick="openScanner('pic_id')"><i class="fa fa-camera"></i>
                                            &nbsp;Scan ID Card
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="input-group input-group-lg">
                                    <div class="input-group-addon" id="icon-serial"
                                        style="font-weight: bold; border-color: none; font-size: 18px;">
                                        <i class="fa fa-qrcode"></i>
                                    </div>
                                    <input type="text" class="form-control" placeholder="ID CARD LEADER"
                                        id="leader_id" readonly>
                                    <span class="input-group-btn">
                                        <button style="font-weight: bold;" class="btn btn-primary btn-flat"
                                            id="btn_leader_id" onclick="openScanner('leader_id')"><i
                                                class="fa fa-camera"></i>
                                            &nbsp;Scan ID Card
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-xs-10 col-xs-offset-1" style="margin-top: 1%; padding:0px;">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead style="background-color: #cddc39;">
                                            <tr>
                                                <th style="font-size: 16px; border: 1.5px solid #333333;" colspan="3">
                                                    AREA OF INSPECTION
                                                </th>
                                                <th style="font-size: 16px; border: 1.5px solid #333333;" colspan="2">
                                                    RESULT
                                                </th>
                                            </tr>
                                            <tr>
                                                <th style="font-size: 14px; border: 1.5px solid #333333;">AREA</th>
                                                <th style="font-size: 14px; border: 1.5px solid #333333;">CHECKLIST</th>
                                                <th style="font-size: 14px; border: 1.5px solid #333333;">GUIDELINES</th>
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
                                                            <br><b>{{ $count_area }}. {{ $ck->area }}</b><br>&nbsp;
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

                                                    @if ($ck->guidelines_setting != 'hidden')
                                                        <td height="50" style="text-align: center;" width="22.5%"
                                                            rowspan="{{ $ck->guidelines_setting }}">
                                                            {{-- <br>@php echo $ck->guidelines; @endphp<br>&nbsp; --}}
                                                            <button class="btn btn-default"
                                                                style="width: 100%; font-weight: bold;"
                                                                onclick="showHint('{{ $ck->area }}')">

                                                                <span style="font-size: 70px; color:deepskyblue;">
                                                                    <i class="fa fa-lightbulb-o"></i>
                                                                </span>
                                                                <br>
                                                                <span style="font-size: 20px;">HINT</span>
                                                                <br>
                                                            </button>
                                                        </td>
                                                    @else
                                                        <td height="50" style="text-align: left; display: none;"
                                                            width="15%" hidden>
                                                            <br>{{ $ck->guidelines }}<br>&nbsp;
                                                        </td>
                                                    @endif

                                                    <td height="50" style="text-align: center;" width="5%">
                                                        <label class="radio" style="">
                                                            <span style="font-weight: bold; color: green;">OK</span>
                                                            <input type="radio" id="result_{{ $ck->checklist_id }}"
                                                                name="result_{{ $ck->checklist_id }}" value="OK">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <label class="radio" style="">
                                                            <span style="font-weight: bold; color: red">NG</span>
                                                            <input type="radio" id="result_{{ $ck->checklist_id }}"
                                                                name="result_{{ $ck->checklist_id }}" value="NG">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                    </td>

                                                    <td height="50"
                                                        style="text-align: left; border-right: 1px solid #333333;"
                                                        width="20%">
                                                        <textarea class="form-control" type="text" rows="3" id="note_{{ $ck->checklist_id }}"
                                                            placeholder="Note ..."></textarea>
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
                                                        @if ($ck->area == 'PINTU, CORNER CASTING DAN FRAME LUAR')
                                                            <img width="125px"
                                                                src="{{ url('files/checksheet/guidelines/pintu.jpg') }}"
                                                                style="height: 125px; width: 150px;" />
                                                        @endif
                                                        @php
                                                            $count_foto = 0;
                                                        @endphp
                                                        @for ($i = 0; $i < $photo_requirment; $i++)
                                                            @php
                                                                $count_foto++;
                                                            @endphp

                                                            @if ($ck->area == 'PINTU, CORNER CASTING DAN FRAME LUAR')
                                                                @if ($count_foto == 2)
                                                                    <br>
                                                                    <img width="125px"
                                                                        src="{{ url('files/checksheet/guidelines/corner.jpg') }}"
                                                                        style="height: 125px; width: 250px;" />
                                                                    <input type="file" class="file"
                                                                        style="display:none"
                                                                        onchange="readURLEvidence(id);"
                                                                        id="input_evidence__{{ str_replace(',', 'ime', str_replace(' ', '-', $ck->area)) }}_{{ $count_foto }}">
                                                                    <button class="btn btn-primary btn-lg"
                                                                        id="btn_evidence__{{ str_replace(',', 'ime', str_replace(' ', '-', $ck->area)) }}_{{ $count_foto }}"
                                                                        value="Photo" onclick="buttonImageEvidence(id)"
                                                                        style="font-size: 20px; width: 200px; height: 125px;"><i
                                                                            class="fa fa-camera"></i>&nbsp;&nbsp;&nbsp;Evidence<br>
                                                                        {{ $count_foto }} / {{ $photo_requirment }}
                                                                        Photo</button>
                                                                    <img width="125px"
                                                                        id="img_evidence__{{ str_replace(',', 'ime', str_replace(' ', '-', $ck->area)) }}_{{ $count_foto }}"
                                                                        src="" onclick="buttonImageEvidence(id)"
                                                                        style="display: none; width: 200px; height: 125px;"
                                                                        alt="your image" />
                                                                    <div class="col-xs-12" style="margin-bottom: 1%;">
                                                                    </div>
                                                                @else
                                                                    <input type="file" class="file"
                                                                        style="display:none"
                                                                        onchange="readURLEvidence(id);"
                                                                        id="input_evidence__{{ str_replace(',', 'ime', str_replace(' ', '-', $ck->area)) }}_{{ $count_foto }}">
                                                                    <button class="btn btn-primary btn-lg"
                                                                        id="btn_evidence__{{ str_replace(',', 'ime', str_replace(' ', '-', $ck->area)) }}_{{ $count_foto }}"
                                                                        value="Photo" onclick="buttonImageEvidence(id)"
                                                                        style="font-size: 20px; width: 200px; height: 125px;"><i
                                                                            class="fa fa-camera"></i>&nbsp;&nbsp;&nbsp;Evidence<br>
                                                                        {{ $count_foto }} / {{ $photo_requirment }}
                                                                        Photo</button>
                                                                    <img width="125px"
                                                                        id="img_evidence__{{ str_replace(',', 'ime', str_replace(' ', '-', $ck->area)) }}_{{ $count_foto }}"
                                                                        src="" onclick="buttonImageEvidence(id)"
                                                                        style="display: none; width: 200px; height: 125px;"
                                                                        alt="your image" />
                                                                    <div class="col-xs-12" style="margin-bottom: 1%;">
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <input type="file" class="file" style="display:none"
                                                                    onchange="readURLEvidence(id);"
                                                                    id="input_evidence__{{ str_replace(' ', '-', $ck->area) }}_{{ $count_foto }}">
                                                                <button class="btn btn-primary btn-lg"
                                                                    id="btn_evidence__{{ str_replace(' ', '-', $ck->area) }}_{{ $count_foto }}"
                                                                    value="Photo" onclick="buttonImageEvidence(id)"
                                                                    style="font-size: 20px; width: 200px; height: 125px;"><i
                                                                        class="fa fa-camera"></i>&nbsp;&nbsp;&nbsp;Evidence<br>
                                                                    {{ $count_foto }} / {{ $photo_requirment }}
                                                                    Photo</button>
                                                                <img width="125px"
                                                                    id="img_evidence__{{ str_replace(' ', '-', $ck->area) }}_{{ $count_foto }}"
                                                                    src="" onclick="buttonImageEvidence(id)"
                                                                    style="display: none; width: 200px; height: 125px;"
                                                                    alt="your image" />
                                                                <div class="col-xs-12" style="margin-bottom: 1%;">
                                                                </div>
                                                            @endif
                                                        @endfor
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

                                <div class="col-xs-8 col-xs-offset-2">
                                    <button class="btn btn-lg btn-success" id="submit_checklist"
                                        style="width: 100%; font-weight: bold; font-size: 25px;"
                                        onclick="submitChecklist()">
                                        <i class="fa fa-check"></i>&nbsp;&nbsp;Submit Checklist
                                    </button>
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
                    </div>
                </div>

                <div class="chart tab-pane" id="cargo" style="position: relative;">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-4 col-xs-offset-4">
                                <div class="input-group margin">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-info btn-lg"><i
                                                class="fa fa-search"></i></button>
                                    </div>
                                    <input type="text" class="form-control input-lg" name="myInput" id="myInput"
                                        onkeyup="cari()" placeholder="Search ...">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table no-margin table-bordered table-striped" id="tabel1">
                                <thead style="background-color: #cddc39;">
                                    <tr>
                                        <th style="vertical-align: middle;">DEST</th>
                                        <th style="vertical-align: middle;">INVOICE</th>
                                        <th style="vertical-align: middle;">GMC</th>
                                        <th style="vertical-align: middle;">DESCRIPTION OF GOODS</th>
                                        <th style="vertical-align: middle;">FSTK</th>
                                        <th style="vertical-align: middle;">MARKING NO.</th>
                                        <th style="vertical-align: middle;" colspan="2">PACKAGE</th>
                                        <th style="vertical-align: middle;" colspan="2">QUANTITY</th>
                                        <th style="vertical-align: middle;">QTY BOX OR PACKAGE</th>
                                        <th style="vertical-align: middle;" colspan="2">Check</th>
                                        <th style="vertical-align: middle;">Total</th>
                                        <th style="vertical-align: middle;">Confirm</th>
                                        <th style="vertical-align: middle;" colspan="3">Diff</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach ($detail as $nomor => $detail)
                                            <input type="text" id="countdetail" value="{{ $loop->count }}"
                                                hidden></input>
                                            <td width="5%">{{ $detail->destination }}</td>
                                            <td width="8%">{{ $detail->invoice }}</td>
                                            <td width="5%">{{ $detail->gmc }}</td>
                                            <td>{{ $detail->goods }}</td>
                                            <td>{{ $detail->stock }}</td>
                                            <td width="2%">{{ $detail->marking }}</td>
                                            @if ($detail->package_set == 'PL')
                                                <td class="PLT" width="5%">{{ $detail->package_qty }}</td>
                                                <td class="PLTT" hidden>{{ $detail->confirm }}</td>
                                            @elseif($detail->package_set == 'C/T')
                                                <td class="CTN" width="5%">{{ $detail->package_qty }}</td>
                                                <td class="CTNT" hidden>{{ $detail->confirm }}</td>
                                            @else
                                                <td class="{{ $detail->package_qty }}" width="5%">
                                                    {{ $detail->package_qty }}</td>
                                            @endif
                                            <td width="2%">{{ $detail->package_set }}</td>
                                            <td class="{{ $detail->qty_set }}" width="5%">{{ $detail->qty_qty }}
                                            </td>
                                            <td width="2%">{{ $detail->qty_set }} </td>
                                            <td width="2%">{{ $detail->box }} </td>

                                            <td width="8%">
                                                @if ($detail->package_set == '-')
                                                    <button class="btn btn-block btn-primary btn-sm"
                                                        id="like{{ $nomor + 1 }}"
                                                        onclick="okbara({{ $nomor + 1 }}); masuk('0','{{ $detail->id }}');totalconfirm()"
                                                        style="display: block;"> Check</button>
                                                @else
                                                    <button class="btn btn-block btn-primary btn-sm"
                                                        id="like{{ $nomor + 1 }}"
                                                        onclick="add({{ $nomor + 1 }}); minusdata({{ $nomor + 1 }}); hide({{ $nomor + 1 }}); update({{ $nomor + 1 }},{{ $detail->id }}); totalconfirm()"
                                                        style="display: block;"> Check</button>
                                                @endif
                                            </td>
                                            <td width="8%">
                                                @if ($detail->package_set == '-')
                                                    <button class="btn btn-block btn-warning btn-sm"
                                                        id="like{{ $nomor + 1 }}"
                                                        onclick="ngbara({{ $nomor + 1 }});  masuk('1','{{ $detail->id }}');totalconfirm()"
                                                        style="display: block;"> Uncheck</button>
                                                @else
                                                    <button class="btn btn-block btn-warning btn-sm"
                                                        id="like{{ $nomor + 1 }}"
                                                        onclick="minus({{ $nomor + 1 }}); minusdata({{ $nomor + 1 }}); hide({{ $nomor + 1 }}); update({{ $nomor + 1 }},{{ $detail->id }}); totalconfirm();"
                                                        style="display: block;"> Uncheck</button>
                                                @endif
                                            </td>
                                            <td width="2%">
                                                <p id="total{{ $nomor + 1 }}">{{ $detail->package_qty }}</p>
                                            </td>
                                            @if ($detail->package_set == 'PL')
                                                <td width="2%" class="PLTTT">
                                                    <p id="inc{{ $nomor + 1 }}">{{ $detail->confirm }}</p>
                                                </td>
                                            @elseif($detail->package_set == 'C/T')
                                                <td width="2%" class="CTNTT">
                                                    <p id="inc{{ $nomor + 1 }}">{{ $detail->confirm }}</p>
                                                </td>
                                            @else
                                                <td width="2%">
                                                    <p id="inc{{ $nomor + 1 }}">{{ $detail->confirm }}</p>
                                                </td>
                                            @endif
                                            <td width="5%">
                                                <p id="diff{{ $nomor + 1 }}">{{ $detail->diff }}</p>
                                            </td>
                                            @if ($detail->package_set == '-')
                                                <td width="5%">
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
                                                    <td width="5%">
                                                        <span data-toggle="tooltip" class="badge bg-green"
                                                            id="y{{ $nomor + 1 }}" style="display: block;">
                                                            <i class="fa fa-fw fa-check"></i>
                                                        </span>
                                                        <span data-toggle="tooltip" class="badge bg-red"
                                                            id="n{{ $nomor + 1 }}" style="display: none;">
                                                            <i class="fa fa-fw  fa-close"></i>
                                                        </span>
                                                    </td>
                                                @else
                                                    <td width="5%">
                                                        <span data-toggle="tooltip" class="badge bg-red"
                                                            id="n{{ $nomor + 1 }}" style="display: block;">
                                                            <i class="fa fa-fw  fa-close"></i>
                                                        </span>
                                                        <span data-toggle="tooltip" class="badge bg-green"
                                                            id="y{{ $nomor + 1 }}" style="display: none;">
                                                            <i class="fa fa-fw fa-check"></i>
                                                        </span>
                                                    </td>
                                                @endif
                                            @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="background-color: RGB(252, 248, 227);">
                                    <tr>
                                        <th colspan="6" rowspan="2">
                                            <CENTER>REMAIN PALLET & CTN</CENTER>
                                        </th>
                                        <th>
                                            <p id="plte"></p>
                                        </th>
                                        <th>PL</th>
                                        <th>
                                            <p id="sete"></p>
                                        </th>
                                        <th>SET</th>
                                        <th colspan="2" rowspan="2">Confirm</th>
                                        <th colspan="2">PL</th>
                                        <th>
                                            <p id="pltet"></p>
                                        </th>
                                        <th rowspan="2">Diff</th>
                                        <th>
                                            <p id="pltem"></p>
                                        </th>
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
                                        <th colspan="2">C/T</th>
                                        <th>
                                            <p id="ctnet"></p>
                                        </th>
                                        <th>
                                            <p id="ctntem"></p>
                                        </th>
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
                                    <thead style="background-color: #cddc39;">
                                        <tr>
                                            <th colspan="2">CLOSURE CONTAINER INSPECTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th style="vertical-align: middle;">CONTAINER NO.</th>
                                            <th>
                                                <input type="text" name="closure_countainer_number"
                                                    id="closure_countainer_number" class="form-control"
                                                    onchange="closureNomor('closure_countainer_number',this.value)">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">SEAL NO.</th>
                                            <th>
                                                <input type="text" name="closure_seal_number" id="closure_seal_number"
                                                    class="form-control"
                                                    onchange="closureNomor('closure_seal_number',this.value)">
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">SEAL PHOTO</th>
                                            <th>
                                                <input type="file" class="file" style="display:none"
                                                    onchange="readSeal(this);" id="input_seal">
                                                <button class="btn btn-primary btn-lg" id="btnSeal"
                                                    onclick="buttonImage(this)"
                                                    style="font-size: 1.5vw; width: 300px; height: 200px;"><i
                                                        class="fa  fa-file-image-o"></i>&nbsp;&nbsp;&nbsp;Photo
                                                    Seal</button>
                                                <img width="150px" id="seal_photo" src=""
                                                    onclick="buttonImage(this)"
                                                    style="display: none; width: 300px; height: 200px;"
                                                    alt="your image" />
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="vertical-align: middle;">CONTAINER PHOTO</th>
                                            <th>
                                                <input type="file" class="file" style="display:none"
                                                    onchange="readContainer(this);" id="input_container">
                                                <button class="btn btn-primary btn-lg" id="btnContainer"
                                                    onclick="buttonImage(this)"
                                                    style="font-size: 1.5vw; width: 300px; height: 200px;"><i
                                                        class="fa  fa-file-image-o"></i>&nbsp;&nbsp;&nbsp;Photo
                                                    Container</button>
                                                <img width="150px" id="container_photo" src=""
                                                    onclick="buttonImage(this)"
                                                    style="display: none; width: 300px; height: 200px;"
                                                    alt="your image" />
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-xs-8 col-xs-offset-2" id="seal_non_sea"
                            style="margin-bottom: 100px; margin-top: 100px;">
                            <center>
                                <h1 style="text-transform: uppercase;">Closure information is only for shipping
                                    condition
                                    by Sea</h1>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal modal-default fade" id="scanModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center"><b>SCAN QR CODE HERE</b></h4>
                </div>
                <div class="modal-body">
                    <div id='scanner' class="col-xs-12">
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

                    <p style="visibility: hidden;">camera</p>
                    <input type="hidden" id="code">
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-warning fade" id="ALERT">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Warning</h4>
                </div>
                <div class="modal-body">
                    <p>Data Not Match</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline pull-right" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-ukuran" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">GUIDELINES AREA "UKURAN"</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">PENGECEKAN</h4>
                            <span>
                                JIKA UKURAN KONTAINER TIDAK SESUAI, SEGERA HUBUNGI PIHAK TERKAIT (GROUP EKSPOR)
                            </span>
                        </div>

                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">FOTO</h4>
                            <img src="{{ url('files/checksheet/guidelines/ukuran.jpg') }}"
                                style="height: 150px; width: 300px;" />

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-kiri" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">GUIDELINES AREA "DINDING KIRI"</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">PENGECEKAN</h4>
                            <b style="font-size: 20px;" class="text-green">OK</b>
                            <span>
                                KONTAINER PESOK DAN KONDISINYA TIDAK BERLUBANG
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/pesok.jpg') }}"
                                style="height: 125px; width: 265px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN DALAM
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_dalam.jpg') }}"
                                style="height: 130px; width: 250px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN LUAR
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_luar.jpg') }}"
                                style="height: 130px; width: 225px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                APABILA DITEMUKAN SERANGGA
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/serangga.jpg') }}"
                                style="height: 130px; width: 225px;" />
                        </div>

                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">FOTO</h4>
                            <img src="{{ url('files/checksheet/guidelines/kiri.jpg') }}"
                                style="height: 225px; width: 250px;" />

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-depan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">GUIDELINES AREA "DEPAN"</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">PENGECEKAN</h4>
                            <b style="font-size: 20px;" class="text-green">OK</b>
                            <span>
                                KONTAINER PESOK DAN KONDISINYA TIDAK BERLUBANG
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/pesok.jpg') }}"
                                style="height: 125px; width: 265px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN DALAM
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_dalam.jpg') }}"
                                style="height: 130px; width: 250px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN LUAR
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_luar.jpg') }}"
                                style="height: 130px; width: 225px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                APABILA DITEMUKAN SERANGGA
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/serangga.jpg') }}"
                                style="height: 130px; width: 225px;" />
                        </div>

                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">FOTO</h4>
                            <img src="{{ url('files/checksheet/guidelines/depan.jpg') }}"
                                style="height: 225px; width: 200px;" />

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-kanan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">GUIDELINES AREA "DINDING KANAN"</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">PENGECEKAN</h4>
                            <b style="font-size: 20px;" class="text-green">OK</b>
                            <span>
                                KONTAINER PESOK DAN KONDISINYA TIDAK BERLUBANG
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/pesok.jpg') }}"
                                style="height: 125px; width: 265px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN DALAM
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_dalam.jpg') }}"
                                style="height: 130px; width: 250px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN LUAR
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_luar.jpg') }}"
                                style="height: 130px; width: 225px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                APABILA DITEMUKAN SERANGGA
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/serangga.jpg') }}"
                                style="height: 130px; width: 225px;" />


                        </div>

                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">FOTO</h4>
                            <img src="{{ url('files/checksheet/guidelines/kanan.jpg') }}"
                                style="height: 225px; width: 250px;" />

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-lantai" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">GUIDELINES AREA "LANTAI"</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">PENGECEKAN</h4>
                            <b style="font-size: 20px;" class="text-green">OK</b>
                            <span>
                                KONTAINER PESOK DAN KONDISINYA TIDAK BERLUBANG
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/pesok.jpg') }}"
                                style="height: 125px; width: 265px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN DALAM
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_dalam.jpg') }}"
                                style="height: 130px; width: 250px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN LUAR
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_luar.jpg') }}"
                                style="height: 130px; width: 225px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                APABILA DITEMUKAN SERANGGA
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/serangga.jpg') }}"
                                style="height: 130px; width: 225px;" />
                        </div>

                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">FOTO</h4>
                            <img src="{{ url('files/checksheet/guidelines/lantai.jpg') }}"
                                style="height: 225px; width: 300px;" />

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-atap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">GUIDELINES AREA "ATAP"</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">PENGECEKAN</h4>
                            <b style="font-size: 20px;" class="text-green">OK</b>
                            <span>
                                KONTAINER PESOK DAN KONDISINYA TIDAK BERLUBANG
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/pesok.jpg') }}"
                                style="height: 125px; width: 265px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN DALAM
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_dalam.jpg') }}"
                                style="height: 130px; width: 250px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN LUAR
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_luar.jpg') }}"
                                style="height: 130px; width: 225px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                APABILA DITEMUKAN SERANGGA
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/serangga.jpg') }}"
                                style="height: 130px; width: 225px;" />
                        </div>

                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">FOTO</h4>
                            <img src="{{ url('files/checksheet/guidelines/atap.jpg') }}"
                                style="height: 225px; width: 300px;" />

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-pintu" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">GUIDELINES AREA "PINTU, CORNER CASTING DAN FRAME LUAR"</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">PENGECEKAN</h4>
                            <b style="font-size: 20px;" class="text-green">OK</b>
                            <span>
                                KONTAINER PESOK DAN KONDISINYA TIDAK BERLUBANG
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/pesok.jpg') }}"
                                style="height: 125px; width: 265px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN DALAM
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_dalam.jpg') }}"
                                style="height: 130px; width: 250px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                KONTAINER LUBANG DI BAGIAN LUAR
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/lubang_luar.jpg') }}"
                                style="height: 130px; width: 225px;" />

                            <br>
                            <br>
                            <br>

                            <b style="font-size: 20px;" class="text-red">NG</b>
                            <span>
                                APABILA DITEMUKAN SERANGGA
                            </span>
                            <img src="{{ url('files/checksheet/guidelines/serangga.jpg') }}"
                                style="height: 130px; width: 225px;" />
                        </div>

                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">FOTO</h4>
                            <img src="{{ url('files/checksheet/guidelines/pintu.jpg') }}"
                                style="height: 225px; width: 300px;" />
                            <br>
                            <img src="{{ url('files/checksheet/guidelines/pintu_2.jpg') }}"
                                style="height: 400px; width: 300px;" />

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-serangga" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" style="width: 50%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">GUIDELINES AREA "SERANGGA"</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">PENGECEKAN</h4>
                            <ul style="padding-left: 20px;">
                                <li>CEK DILAKUKAN SECARA VISUAL</li>
                                <li>GUNAKAN LAMPU SAAT PENGECEKAN</li>
                                <li>PERIKSA MULAI DARI PINTU KONTAINER SAMPAI SUDUT PALING DALAM (SUDUT, LANTAI DAN DINDING
                                    KONTAINER)</li>
                                <li>CEK LANTAI DENGAN CARA DIKETUK-KETUK UNTUK MENGETAHUI ADA TIDAKNYA SERANGGA YANG
                                    BERSARANG</li>
                                <li>JIKA DITEMUKAN SERANGGA, FOTO SEBAGAI BUKTI DAN LANGSUNG LAPORKAN KE ATASAN</li>
                            </ul>
                        </div>

                        <div class="col-xs-6">
                            <h4 style="margin-top: 0px;">FOTO</h4>
                            <img src="{{ url('files/checksheet/guidelines/serangga_1.jpg') }}"
                                style="height: 225px; width: 300px;" />
                            <br>
                            <img src="{{ url('files/checksheet/guidelines/serangga_2.jpg') }}"
                                style="height: 225px; width: 300px;" />
                            <br>
                            <img src="{{ url('files/checksheet/guidelines/serangga_3.jpg') }}"
                                style="height: 225px; width: 300px;" />
                            <br>
                            <img src="{{ url('files/checksheet/guidelines/serangga_4.jpg') }}"
                                style="height: 225px; width: 300px;" />

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>





@endsection
@section('scripts')
    <script src="{{ url('js/bootstrap-toggle.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/jsQR.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            showDriverPhoto();
            showSealPhoto();
            showContainerPhoto();
            shipmentCondition();

            checkChecklistData();

            $('#rows1').removeAttr('hidden');
            var plt = 0;
            var ctn = 0;
            var set = 0;
            var pcs = 0;
            var pltt = 0;
            var ctnt = 0;
            $(".PLT").each(function() {
                plt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });

            $('#plte').html("" + plt);

            $(".CTN").each(function() {
                ctn += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });

            $('#ctne').html("" + ctn);

            $(".PLTT").each(function() {
                pltt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });

            $('#pltet').html("" + pltt);

            $(".CTNT").each(function() {
                ctnt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });

            $('#ctnet').html("" + ctnt);

            var pltem = pltt - plt;
            var ctntem = ctnt - ctn;
            $('#pltem').html("" + pltem);
            $('#ctntem').html("" + ctntem);

            $(".SET").each(function() {
                set += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });

            $('#sete').html("" + set);

            $(".PC").each(function() {
                pcs += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });

            $('#pcse').html("" + pcs);

        });

        var video;
        var target;

        var asset_route = <?php echo json_encode($asset_route); ?>;
        var time = <?php echo json_encode($time); ?>;
        var checklist = <?php echo json_encode($checklist); ?>;
        var checklist_photo = <?php echo json_encode($checklist_photo); ?>;
        var employees = <?php echo json_encode($employees); ?>;

        function stopScan() {
            $('#scanModal').modal('hide');
        }

        function videoOff() {
            video.pause();
            video.src = "";
            video.srcObject.getTracks()[0].stop();
        }

        function openScanner(id) {
            $("#scanModal").modal('show');
            openInfoGritter('Scan ID Card', 'Scanning ' + id.replace("_", " ").toUpperCase() + '...');
            showCheck(id);
        }
        $("#scanModal").on('shown.bs.modal', function() {});

        $('#scanModal').on('hidden.bs.modal', function() {
            videoOff();
        });

        function showCheck(id) {
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
                        outputMessage.hidden = true;
                        videoOff();

                        var found = false;
                        for (let i = 0; i < employees.length; i++) {
                            if (employees[i].employee_id == code.data) {
                                found = true;
                                break
                            }
                        }

                        if (found) {
                            $('#' + id).val(code.data);
                            openSuccessGritter("Success", "Your ID has been successfully verified");
                        } else {
                            $('#' + id).val('');
                            openErrorGritter("Error!", "Verification failed");
                        }

                        $('#scanner').hide();
                        $('#scanModal').modal('hide');
                        $(".modal-backdrop").remove();

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
            var seal = $("#seal_number_hidden").val();
            var countainer = $("#countainer_number_hidden").val();

            if (photo != '') {
                $("#seal_photo").show();
                $("#btnSeal").hide();
                $("#seal_photo").attr('src', photo);

                $("#closure_seal_number").val(seal);
                $("#closure_countainer_number").val(countainer);
            } else {
                $("#seal_photo").hide();
                $("#btnSeal").show();

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

        function saveContainer() {
            var id_checkSheet = document.getElementById("id_checkSheet_master").innerHTML;

            var formData = new FormData();
            formData.append('id_checkSheet', id_checkSheet);


            formData.append('file_datas', $('#input_container').prop('files')[0]);
            var file = $('#input_container').val().replace(/C:\\fakepath\\/i, '').split(".");
            formData.append('extension', file[1]);
            formData.append('photo_name', file[0]);

            $.ajax({
                url: "{{ url('import/container_photo') }}",
                method: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(result, status, xhr) {
                    $("#container_photo_hidden").val(result.photo);
                    openSuccessGritter("Success", "Closure Photo Saved Successfully");
                },
                error: function(result, status, xhr) {
                    console.log(result.message);
                },
            })

        }

        function readContainer(input) {
            var insert = true;

            if ($('#input_container').prop('files')[0] == undefined) {
                insert = false;
            }

            if ($('#closure_seal_number').val() == '') {
                insert = false;
            }

            if ($('#closure_countainer_number').val() == '') {
                insert = false;
            }

            if (insert) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        var img = $(input).closest("th").find("img");
                        $(img).show();
                        $(img).attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);

                }

                $(input).closest("th").find("button").hide();
                saveContainer();
            } else {
                openErrorGritter("Error", "Complete Field Before Take Photo");
            }

        }

        function saveSeal() {

            var id_checkSheet = document.getElementById("id_checkSheet_master").innerHTML;

            var formData = new FormData();
            formData.append('id_checkSheet', id_checkSheet);

            formData.append('file_datas', $('#input_seal').prop('files')[0]);
            var file = $('#input_seal').val().replace(/C:\\fakepath\\/i, '').split(".");
            formData.append('extension', file[1]);
            formData.append('photo_name', file[0]);

            $.ajax({
                url: "{{ url('import/seal_photo') }}",
                method: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(result, status, xhr) {
                    $("#seal_photo_hidden").val(result.photo);
                    openSuccessGritter("Success", "Closure Photo Saved Successfully");
                },
                error: function(result, status, xhr) {
                    console.log(result.message);
                },
            })

        }

        function readSeal(input) {
            var insert = true;

            if ($('#input_seal').prop('files')[0] == undefined) {
                insert = false;
            }

            if ($('#closure_seal_number').val() == '') {
                insert = false;
            }

            if ($('#closure_countainer_number').val() == '') {
                insert = false;
            }

            if (insert) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        var img = $(input).closest("th").find("img");
                        $(img).show();
                        $(img).attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);

                }

                $(input).closest("th").find("button").hide();
                saveSeal();
            } else {
                openErrorGritter("Error", "Complete Field Before Take Photo");
            }

        }

        function buttonImage(elem) {
            $(elem).closest("th").find("input").click();
        }

        function readURL(input) {

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var img = $(input).closest("th").find("img");
                    $(img).show();
                    $(img).attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);

            }

            $(input).closest("th").find("button").hide();
            saveImage(input);
        }

        function buttonImageEvidence(param) {
            var id = param.split("__")[1];

            console.log(id);

            $('#input_evidence__' + id).trigger('click');
        }

        function readURLEvidence(param) {

            var id = param.split("__")[1];
            var input = document.getElementById(param);

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#img_evidence__' + id).show();
                    $('#img_evidence__' + id).attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);

            }

            $('#btn_evidence__' + id).hide();

            var id = param.split("__")[1];

            saveImageEvidence(input);
        }


        function saveImageEvidence(input) {
            var id_checkSheet = document.getElementById("id_checkSheet_master").innerHTML;

            var formData = new FormData();
            formData.append('file_datas', $(input).prop('files')[0]);
            var file = $(input).val().replace(/C:\\fakepath\\/i, '').split(".");

            formData.append('id_checkSheet', id_checkSheet);
            formData.append('photo_id', input.id);
            formData.append('extension', file[1]);
            formData.append('photo_name', file[0]);


            $.ajax({
                url: "{{ url('import/checklist_evidence') }}",
                method: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter("Success", "Photo Submitted");
                    } else {
                        openErrorGritter("Error", result.message);
                    }

                },
                error: function(result, status, xhr) {
                    openErrorGritter("Error", result.message);
                },
            })
        }

        function submitChecklist() {

            var id_checkSheet = document.getElementById("id_checkSheet_master").innerHTML;
            var pic_id = $("#pic_id").val();
            var leader_id = $("#leader_id").val();

            if (pic_id == '' || leader_id == '') {
                openErrorGritter("Error!", "Scan ID Card PIC and Leader");
                return false;
            }

            if (pic_id == leader_id) {
                openErrorGritter("Error!", "Scan another ID Card");
                return false;
            }

            for (let i = 0; i < checklist_photo.length; i++) {
                if ($('#img_evidence__' + checklist_photo[i].area.replaceAll(' ', '-') +
                        '_' + checklist_photo[i].area_photo_id).attr('src') == '') {
                    openErrorGritter("Error!", "Photo evidence is not complete");
                    return false;
                }
            }

            var checklist_answer = [];
            console.log(checklist);
            for (let i = 0; i < checklist.length; i++) {
                if ($('input[name="result_' + checklist[i].checklist_id + '"]:checked').val() == 'OK' ||
                    $('input[name="result_' + checklist[i].checklist_id + '"]:checked').val() == 'NG') {

                    checklist_answer.push({
                        'id': checklist[i].checklist_id,
                        'result': $('input[name="result_' + checklist[i].checklist_id + '"]:checked').val(),
                        'note': $("#note_" + checklist[i].checklist_id).val(),
                    });

                } else {
                    console.log(checklist[i].checklist_id);
                    openErrorGritter("Error", 'Input all fields');
                    return false;
                }
            }

            var data = {
                id_checkSheet,
                pic_id,
                leader_id,
                checklist_answer,
            }

            $.post('{{ url('input/checklist_container') }}', data, function(result, status, xhr) {
                if (result.status) {

                    for (let i = 0; i < checklist.length; i++) {
                        $('input[name="result_' + checklist_answer[i].checklist_id + '"][value="' +
                                checklist_answer[i].result +
                                '"]')
                            .prop("checked", true);
                        $('#note_' + checklist_answer[i].checklist_id).val(checklist_answer[i].note);

                        $('#result_' + checklist_answer[i].checklist_id + '[value="OK"]').prop('disabled', true);
                        $('#result_' + checklist_answer[i].checklist_id + '[value="NG"]').prop('disabled', true);

                        $('#note_' + checklist_answer[i].checklist_id).prop('readonly', true);

                    }

                    $("#submit_checklist").prop('disabled', true);
                    $("#submit_checklist").css('display', 'none');
                    openSuccessGritter('Success', result.message);

                } else {
                    openErrorGritter('Error!', result.message);

                }
            });

        }


        function checkChecklistData() {

            if (time.checklist_checked == 1) {
                for (let i = 0; i < checklist.length; i++) {
                    $('input[name="result_' + checklist[i].checklist_id + '"][value="' + checklist[i].result + '"]')
                        .prop("checked", true);
                    $('#note_' + checklist[i].checklist_id).val(checklist[i].note);

                    $('#result_' + checklist[i].checklist_id + '[value="OK"]').prop('disabled', true);
                    $('#result_' + checklist[i].checklist_id + '[value="NG"]').prop('disabled', true);

                    $('#note_' + checklist[i].checklist_id).prop('readonly', true);

                }


                $('#pic_id').val(time.checklist_pic_by);
                $('#leader_id').val(time.checklist_known_by);

                $("#btn_pic_id").prop('disabled', true);
                $("#btn_leader_id").prop('disabled', true);

                $("#submit_checklist").prop('disabled', true);
                $("#submit_checklist").css('display', 'none');

            } else {
                $("#btn_pic_id").prop('disabled', false);
                $("#btn_leader_id").prop('disabled', false);

                $("#submit_checklist").prop('disabled', false);
                $("#submit_checklist").css('display', 'block');

            }

            for (let i = 0; i < checklist_photo.length; i++) {

                var id_photo = checklist_photo[i].area.replaceAll(' ', '-') + '_' + checklist_photo[i].area_photo_id;
                id_photo = id_photo.replaceAll(',', 'ime');

                if (checklist_photo[i].source != null) {
                    $('#img_evidence__' + id_photo).show();
                    $('#btn_evidence__' + id_photo).hide();

                    $('#img_evidence__' + id_photo).attr('src', asset_route + '/' + checklist_photo[i].source);
                } else {
                    $('#img_evidence__' + id_photo).hide();
                    $('#btn_evidence__' + id_photo).show();
                }

            }

        }

        function saveImage(input) {
            var id_checkSheet = document.getElementById("id_checkSheet_master").innerHTML;

            var formData = new FormData();
            formData.append('file_datas', $(input).prop('files')[0]);
            var file = $(input).val().replace(/C:\\fakepath\\/i, '').split(".");

            formData.append('id_checkSheet', id_checkSheet);
            formData.append('extension', file[1]);
            formData.append('photo_name', file[0]);


            $.ajax({
                url: "{{ url('import/driver_photo') }}",
                method: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(result, status, xhr) {
                    $("#driver_photo_hidden").val(result.photo);
                    openSuccessGritter("Success", "Driver's Photo Saved Successfully");
                },
                error: function(result, status, xhr) {
                    console.log(result.message);
                },
            })
        }

        function cari() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("tabel1");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[3];
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        function add(id) {
            var a = id;
            var total = parseInt(document.getElementById("total" + a).innerHTML);
            var i = parseInt(document.getElementById("inc" + a).innerHTML);
            var aa = i + 1;
            if (aa <= total) {
                document.getElementById('inc' + a).innerHTML = aa;
            }
        }

        function minus(id) {
            var a = id;
            var i = parseInt(document.getElementById("inc" + a).innerHTML);
            var aa = i - 1;
            if (i > 0) {
                document.getElementById('inc' + a).innerHTML = aa;
            }

        }

        function minusdata(id) {
            var a = id;
            var total = parseInt(document.getElementById("total" + a).innerHTML);
            var confirm = parseInt(document.getElementById("inc" + a).innerHTML);
            var aa = confirm - total;
            var aaa = 0;
            if (aa > 0) {
                aaa = " + " + aa;
            } else {
                aaa = " " + aa;
            }
            document.getElementById('diff' + a).innerHTML = aaa;
        }

        function hide(id) {
            var a = id;
            var confirm = parseInt(document.getElementById("diff" + a).innerHTML);
            var y = document.getElementById("y" + a);
            var n = document.getElementById("n" + a);
            if (confirm == 0) {
                y.style.display = "block";
                n.style.display = "none";
            } else {
                y.style.display = "none";
                n.style.display = "block";
            }
        }

        function okbara(id) {
            var a = id;
            var confirm = parseInt(document.getElementById("diff" + a).innerHTML);
            var y = document.getElementById("y" + a);
            var n = document.getElementById("n" + a);

            y.style.display = "block";
            n.style.display = "none";

        }

        function ngbara(id) {
            var a = id;
            var confirm = parseInt(document.getElementById("diff" + a).innerHTML);
            var y = document.getElementById("y" + a);
            var n = document.getElementById("n" + a);

            y.style.display = "none";
            n.style.display = "block";

        }

        function update(id, id2) {
            var a = id;
            var id_detail = id2;
            var confirm = parseInt(document.getElementById("inc" + a).innerHTML);
            var diff = document.getElementById("diff" + a).innerHTML;
            var data = {
                id_detail: id_detail,
                confirm: confirm,
                diff: diff,
            }

            $.post('{{ url('update/CheckSheet') }}', data, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
            });
        }

        function good(id) {
            var a = id;
            document.getElementById('inspection' + a).innerHTML = "0";
            document.getElementById("good" + a).style.display = "none";
            document.getElementById("ng" + a).style.display = "block";
            var confirm = parseInt(document.getElementById("inspection" + a).innerHTML);
            var inspection = "inspection" + a;
            var id = document.getElementById("id_checkSheet_master").innerHTML;
            var data = {
                confirm: confirm,
                inspection: inspection,
                id: id,
            }

            $.post('{{ url('addDetail/CheckSheet') }}', data, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
            });
        }


        function ng(id) {
            var a = id;
            document.getElementById('inspection' + a).innerHTML = "1";
            document.getElementById("good" + a).style.display = "block";
            document.getElementById("ng" + a).style.display = "none";
            var confirm = parseInt(document.getElementById("inspection" + a).innerHTML);
            var inspection = "inspection" + a;
            var id = document.getElementById("id_checkSheet_master").innerHTML;
            var data = {
                confirm: confirm,
                inspection: inspection,
                id: id,
            }

            $.post('{{ url('addDetail/CheckSheet') }}', data, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
            });
        }

        function addInspection() {
            var id = document.getElementById("id_checkSheet_master").innerHTML;
            var data = {
                id: id,
            }

            $.post('{{ url('add/CheckSheet') }}', data, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
            });
        }


        function addInspection2(id) {
            var a = id;
            var text = document.getElementById("remark" + a).value;
            var id = document.getElementById("id_checkSheet_master").innerHTML;
            var remark = "remark" + a;
            var data = {
                remark: remark,
                text: text,
                id: id,
            }
            $.post('{{ url('addDetail2/CheckSheet') }}', data, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
            });

        }

        function nomor(id, nama) {
            var kolom = id;
            var isi = nama;
            var id = document.getElementById("id_checkSheet_master").innerHTML;

            var data = {
                kolom: kolom,
                isi: isi,
                id: id,
            }

            $.post('{{ url('nomor/CheckSheet') }}', data, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
                if (result.status) {
                    $('#' + result.id).val(result.value);
                }
            });

        }

        function masuk(isi, id) {

            var isi = isi;
            var id = id;
            var id_master = document.getElementById("id_checkSheet_master").innerHTML;

            var data = {
                isi: isi,
                id: id,
                id_master: id_master,
            }

            $.post('{{ url('bara/CheckSheet') }}', data, function(result, status, xhr) {
                console.log(status);
                console.log(result);
                console.log(xhr);
            });

        }

        function save() {

            if (confirm("Apa anda yakin menyimpan checkSheet ini?\nData yang sudah disimpan tidak bisa dikembalikan")) {
                $("#loading").show();

                var count = document.getElementById("countdetail").value;
                var ctn = 0;
                var plt = 0;

                $(".CTN").each(function() {
                    ctn += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
                });

                $(".PLT").each(function() {
                    plt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
                });

                var jumlah = ctn + plt;
                var semua = 0;

                for (var i = 1; i <= count; i++) {
                    var idt = "inc" + i;
                    var a = document.getElementById("inc" + i).innerHTML;
                    semua += parseInt(a);
                }

                if (jumlah != semua) {
                    openErrorGritter("Error", 'Condition of Cargo Not Match');
                    $('#loading').hide();
                    return false;
                }

                var shipment_condition = $("#shipment_condition").val();
                if (shipment_condition == 'C1') {
                    var driver_name = $("#driver_name").val();
                    if (driver_name == '') {
                        openErrorGritter("Error", "Driver's Name Empty");
                        $('#loading').hide();
                        return false;
                    }

                    var photo_driver = $("#driver_photo_hidden").val();
                    if (photo_driver == '') {
                        openErrorGritter("Error", "Driver's Photo Empty");
                        $('#loading').hide();
                        return false;
                    }

                    var photo_seal = $("#seal_photo_hidden").val();
                    if (photo_seal == '') {
                        openErrorGritter("Error", "Seal's Photo Empty");
                        $('#loading').hide();
                        return false;
                    }

                    var photo_container = $("#container_photo_hidden").val();
                    if (photo_container == '') {
                        openErrorGritter("Error", "Container's Photo Empty");
                        $('#loading').hide();
                        return false;
                    }
                }

                document.getElementById("kirim").submit();
            }

        }

        function totalconfirm() {
            var pltt = 0;
            var ctnt = 0;
            var plt = 0;
            var ctn = 0;

            $(".PLTTT").each(function() {
                pltt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });

            $('#pltet').html("" + pltt);

            $(".CTNTT").each(function() {
                ctnt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });

            $('#ctnet').html("" + ctnt);

            $(".PLT").each(function() {
                plt += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });

            $(".CTN").each(function() {
                ctn += parseFloat($(this).text().replace(/[^0-9\.-]+/g, ""));
            });

            var pltem = pltt - plt;
            var ctntem = ctnt - ctn;
            $('#pltem').html("" + pltem);
            $('#ctntem').html("" + ctntem);

        }

        function closureNomor(id, nama) {
            var kolom = id;
            var isi = nama;
            var id = document.getElementById("id_checkSheet_master").innerHTML;

            var data = {
                kolom: kolom,
                isi: isi,
                id: id,
            }

            $.post('{{ url('closure/check_checksheet') }}', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter("Success", '');
                } else {
                    $('#' + kolom).val('');
                    openErrorGritter("Error", result.message);
                }
            });

        }


        function showHint(area) {

            if (area == 'UKURAN') {
                $('#modal-ukuran').modal('show');
            } else if (area == 'DINDING KIRI') {
                $('#modal-kiri').modal('show');
            } else if (area == 'DEPAN') {
                $('#modal-depan').modal('show');
            } else if (area == 'DINDING KANAN') {
                $('#modal-kanan').modal('show');
            } else if (area == 'LANTAI') {
                $('#modal-lantai').modal('show');
            } else if (area == 'ATAP') {
                $('#modal-atap').modal('show');
            } else if (area == 'PINTU, CORNER CASTING DAN FRAME LUAR') {
                $('#modal-pintu').modal('show');
            } else if (area == 'SERANGGA') {
                $('#modal-serangga').modal('show');
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

        function openInfoGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-info',
                image: '{{ url('images/image-unregistered.png') }}',
                sticky: false,
                time: '5000'
            });
        }
    </script>
@stop
