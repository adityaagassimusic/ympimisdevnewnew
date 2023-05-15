@extends('layouts.notification')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <style type="text/css">
        #tableDetail>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        tbody>tr>td {
            padding: 10px 5px 10px 5px;
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
            height: 40px;
            padding: 2px 5px 2px 5px;
        }

        .contr #loading {
            display: none;
        }

        .label-status {
            color: black;
            font-size: 0.8vw;
            border-radius: 4px;
            padding: 3px 10px 5px 10px;
            border: 1.5px solid black;
            vertical-align: middle;
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
    </style>
@endsection

@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>

        <div class="row">
            <div class="col-xs-10 col-xs-offset-1" style="margin-top: 1%; padding:0px;">
                <h1 style="text-align: center;">CHECKLIST PENGECEKAN TRUCK CONTAINER</h1>
                <h3>A. IDENTITAS</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="background-color: #7570ce; font-size: 16px; width: 30%; color: white;">
                                    Kategori
                                </th>
                                <th style="font-size: 16px; width: 70%;">
                                    {{ strtoupper($checklist_result->category) }}

                                </th>
                            </tr>
                            <tr>
                                <th style="background-color: #7570ce; font-size: 16px; width: 30%; color: white;">
                                    Nama Driver
                                </th>
                                <th style="font-size: 16px; width: 70%;">
                                    {{ strtoupper($checklist_result->driver_name) }}
                                </th>
                            </tr>
                            <tr>
                                <th style="background-color: #7570ce; font-size: 16px; width: 30%; color: white;">
                                    Nomor Kendaraan
                                </th>
                                <th style="font-size: 16px; width: 70%;">
                                    {{ strtoupper($checklist_result->vehicle_registration_number) }}
                                </th>
                            </tr>
                            <tr>
                                <th style="background-color: #7570ce; font-size: 16px; width: 30%; color: white;">
                                    Nomor Container
                                </th>
                                <th style="font-size: 16px; width: 70%;">
                                    {{ strtoupper($checklist_result->container_number) }}
                                </th>
                            </tr>

                        </thead>
                    </table>
                </div>

                <h3>B. POIN PENGECEKAN</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" style="margin-bottom:0.5%;">
                        <thead>
                            <tr>
                                <th style="text-align: center;">
                                    <img src="{{ url('files/checksheet/guidelines/container_checklist.jpg') }}"
                                        width="100%">
                                    </td>
                                </th>
                            </tr>
                        </thead>
                    </table>

                    <table class="table table-bordered table-striped" style="">
                        <thead>
                            <tr>
                                <th style="font-size: 16px; background-color: white; width: 50%;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <th colspan="2">
                                                <h3 style="margin-top: 0px;">CHECK IN</h3>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%;">
                                                <span style="margin: 0px; font-weight: bold;">
                                                    PIC Check
                                                </span>
                                            </th>
                                            <th style="width: 80%;">
                                                <span style="margin: 0px; font-weight: bold;">
                                                    : {{ $checklist_result->check_in_by }} - {{ $check_in_name }}
                                                </span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%;">
                                                <span style="margin: 0px; font-weight: bold;">
                                                    Check at
                                                </span>
                                            </th>
                                            <th style="width: 80%;">
                                                <span style="margin: 0px; font-weight: bold;">
                                                    : {{ $checklist_result->check_in_at }}
                                                </span>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                                <th style="font-size: 16px; background-color: white; width: 50%;">
                                    <table style="width: 100%;">
                                        <tr>
                                            <th colspan="2">
                                                <h3 style="margin-top: 0px;">CHECK IN</h3>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%;">
                                                <span style="margin: 0px; font-weight: bold;">
                                                    PIC Check
                                                </span>
                                            </th>
                                            <th style="width: 80%;">
                                                <span style="margin: 0px; font-weight: bold;">
                                                    : {{ $checklist_result->check_out_by }} - {{ $check_out_name }}
                                                </span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%;">
                                                <span style="margin: 0px; font-weight: bold;">
                                                    Check at
                                                </span>
                                            </th>
                                            <th style="width: 80%;">
                                                <span style="margin: 0px; font-weight: bold;">
                                                    : {{ $checklist_result->check_out_at }}
                                                </span>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                            </tr>
                            <tr>
                                <th>
                                    <table style="width: 100%;">
                                        @php
                                            $count = 0;
                                        @endphp
                                        @for ($i = 0; $i < count($checklist_point); $i++)
                                            <tr>
                                                <td width="5%" style="vertical-align: top;">{{ ++$count }}.</td>
                                                <td width="55%" style="vertical-align: top;">
                                                    {{ $checklist_point[$i]->point_check }}<br>
                                                    <span style="font-weight: normal;">@php echo $checklist_point[$i]->guidelines; @endphp</span>
                                                </td>
                                                @if ($checklist_point[$i]->check_in_result == 'OK')
                                                    <td width="10%"
                                                        style="vertical-align: middle; font-size: 30px; font-weight: bold; color: green;">
                                                        {{ $checklist_point[$i]->check_in_result }}
                                                    </td>
                                                @elseif($checklist_point[$i]->check_in_result == 'NG')
                                                    <td width="10%"
                                                        style="vertical-align: middle; font-size: 30px; font-weight: bold; color: red;">
                                                        {{ $checklist_point[$i]->check_in_result }}
                                                    </td>
                                                @else
                                                    <td width="10%"
                                                        style="vertical-align: middle; font-size: 30px; font-weight: bold; color: black;">
                                                        {{ $checklist_point[$i]->check_in_result }}
                                                    </td>
                                                @endif

                                                <td width="30%" style="vertical-align: top;">
                                                    <center>
                                                        @php
                                                            print_r('<img style="font-size: 20px; width: 175px; height: 100px; text-align: center;" src="' . url('files/checksheet/checklist_security/' . $checklist_point[$i]->check_in_source) . '" class="user-image">');
                                                        @endphp
                                                    </center>
                                                </td>
                                            </tr>
                                        @endfor
                                    </table>
                                </th>
                                <th>
                                    <table style="width: 100%;">
                                        @php
                                            $count = 0;
                                        @endphp
                                        @for ($i = 0; $i < count($checklist_point); $i++)
                                            <tr>
                                                <td width="5%" style="vertical-align: top;">{{ ++$count }}.</td>
                                                <td width="55%" style="vertical-align: top;">
                                                    {{ $checklist_point[$i]->point_check }}<br>
                                                    <span style="font-weight: normal;">@php echo $checklist_point[$i]->guidelines; @endphp</span>
                                                </td>
                                                @if ($checklist_point[$i]->check_out_result == 'OK')
                                                    <td width="10%"
                                                        style="vertical-align: middle; font-size: 30px; font-weight: bold; color: green;">
                                                        {{ $checklist_point[$i]->check_out_result }}
                                                    </td>
                                                @elseif($checklist_point[$i]->check_out_result == 'NG')
                                                    <td width="10%"
                                                        style="vertical-align: middle; font-size: 30px; font-weight: bold; color: red;">
                                                        {{ $checklist_point[$i]->check_out_result }}
                                                    </td>
                                                @else
                                                    <td width="10%"
                                                        style="vertical-align: middle; font-size: 30px; font-weight: bold; color: black;">
                                                        {{ $checklist_point[$i]->check_out_result }}
                                                    </td>
                                                @endif

                                                <td width="30%" style="vertical-align: top;">
                                                    <center>
                                                        @php
                                                            print_r('<img style="font-size: 20px; width: 175px; height: 100px; text-align: center;" src="' . url('files/checksheet/checklist_security/' . $checklist_point[$i]->check_out_source) . '" class="user-image">');
                                                        @endphp
                                                    </center>
                                                </td>
                                            </tr>
                                        @endfor
                                    </table>
                                </th>
                            </tr>
                            <tr>
                                <th style="font-size: 16px;" width="50%">
                                    <span>Catatan :</span>
                                    <textarea style="width:100%;" id="note" rows="4" disabled>{{ $checklist_result->check_in_note }}</textarea>
                                </th>
                                <th style="font-size: 16px;" width="50%">
                                    <span>Catatan :</span>
                                    <textarea style="width:100%;" id="note" rows="4" disabled>{{ $checklist_result->check_out_note }}</textarea>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $('.select2').select2();

        });


        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '5000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '5000'
            });
        }
    </script>
@endsection
