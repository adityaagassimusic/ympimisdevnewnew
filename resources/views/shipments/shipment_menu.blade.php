@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        #loading,
        #error {
            display: none;
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
                        style="background-color: #8b85f4; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 2%; color: black; border: 1px solid darkgrey; border-radius: 5px;">
                        SHIPMENT CONTROL (出荷管理)
                    </h1>
                </center>
                <div class="row">
                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 24px; color: black; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/material_volume') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-cube"></i>
                            Material Volumes
                        </a>

                        <a target="_blank" href="{{ url('index/shipment_condition') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-ship"></i>
                            Shipment Condition
                        </a>

                        <a target="_blank" href="{{ url('index/destination') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-globe"></i>
                            Destination
                        </a>

                        <a target="_blank" href="{{ url('index/psi_calendar') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-calendar"></i>
                            PSI Calendar
                        </a>
                    </div>

                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 26px; color: purple; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/sales_order') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-cart-plus"></i>
                            Sales Order
                        </a>

                        <a target="_blank" href="{{ url('index/ending_stock') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-cubes"></i>
                            Ending Stock
                        </a>

                        <a target="_blank" href="{{ url('index/back_order_sales') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-tasks"></i>
                            Back Order
                        </a>

                        <a target="_blank" href="{{ url('index/generate_shipment_schedule/fg') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-refresh"></i>
                            Generate <b>FG</b>
                        </a>

                        <a target="_blank" href="{{ url('index/generate_shipment_schedule/kd') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-refresh"></i>
                            Generate <b>KD</b> & <b>SP</b>
                        </a>

                        <a target="_blank" href="{{ url('index/generate_shipment_cubication') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-refresh"></i>
                            Shipment Cubication
                        </a>

                        <a target="_blank" href="{{ url('index/shipment_unmatch') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-exchange"></i>
                            Shipment Unmatch
                        </a>

                    </div>

                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 26px; color: green; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Shipment <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/shipment_schedule') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-th-list"></i>
                            Shipment Data
                        </a>

                        <a target="_blank" href="{{ url('index/fg_shipment_schedule') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-trophy"></i>
                            Shipment Achievement
                        </a>

                    </div>

                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 26px; color: #00a7d0; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/display/stuffing_monitoring') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-tv"></i>
                            Stuffing Monitoring
                        </a>

                        <a target="_blank" href="{{ url('index/display/shipment_progress_all') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-bar-chart"></i>
                            Shipment Progress
                        </a>

                        <a target="_blank" href="{{ url('index/shipping_amount') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-dollar"></i>
                            Shipping Amount
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
