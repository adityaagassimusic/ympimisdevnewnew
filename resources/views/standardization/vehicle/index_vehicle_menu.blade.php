@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        #loading,
        #error {
            display: none;
        }

        .highlight-display {
            font-size : 20px;
            font-weight: bold;
            color: #C51C5A;
            text-shadow: 1px 1px 5px #ff9999;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <div class="row">

        </div>
    </section>
@stop
@section('content')
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="text-align: center; position: absolute; color: white; top: 45%; left: 40%;">
                <span style="font-size: 50px;">Please wait ... </span><br>
                <span style="font-size: 50px;"><i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <center>
                    <h1
                        style="background-color: #B3C2F2; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 2%; color: black; border: 1px solid darkgrey; border-radius: 5px;">
                        Vehicle Menu
                    </h1>
                </center>
                <div class="row">

                    <div class="col-xs-12 col-md-4 col-lg-4" style="text-align: center;">
                        <span style="font-size: 26px; color: green; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Input <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/standardization/form/roda_2') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid green; font-size: 18px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-motorcycle"></i>
                            Cek kendaraan Roda 2
                        </a>

                        <a target="_blank" href="{{ url('index/standardization/form/roda_4') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid green; font-size: 18px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-car"></i>
                            Cek kendaraan Roda 4
                        </a>

                        <a target="_blank" href="{{ url('index/standardization/vehicle_attedance') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid green; font-size: 18px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-file-text"></i>
                            Absensi Razia Kendaraan
                        </a>

                        <a target="_blank" href="{{ url('index/vehicle/attendance/motor') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid green; font-size: 18px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-motorcycle"></i>
                            Pembagian Stiker Motor
                        </a>

                        <a target="_blank" href="{{ url('index/vehicle/attendance/mobil') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid green; font-size: 18px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-car"></i>
                            Pembagian Stiker Mobil
                        </a>
                    </div>

                    <div class="col-xs-12 col-md-4 col-lg-4" style="text-align: center;">
                        <span style="font-size: 26px; color: #00a7d0; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/standardization/vehicle_report') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid blue; font-size: 18px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-list-alt"></i>
                            Report Temuan Razia Kendaraan
                        </a>

                        <a target="_blank" href="{{ url('index/vehicle/report') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid blue; font-size: 18px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-list-alt"></i>
                            Report Data Kendaraan Roda 2
                        </a>
                    </div>


                    <div class="col-xs-12 col-md-4 col-lg-4" style="text-align: center;">
                        <span style="font-size: 26px; color: red; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/standardization/vehicle_monitoring') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid red; font-size: 18px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-bar-chart"></i>
                            Temuan Razia Kendaraan</span>
                        </a>

                        <a target="_blank" href="{{ url('index/standardization/attendance_monitoring') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid red; font-size: 18px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-file-text"></i>
                            Absensi Razia Kendaraan
                        </a>

                    </div>



                   

                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
        });

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
    </script>
@endsection
