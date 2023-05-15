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
                        style="background-color: #fcf8e3; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 2%; color: black; border: 1px solid darkgrey; border-radius: 5px;">
                        RAW MATERIAL
                    </h1>
                </center>
                <div class="row">
                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 24px; color: black; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/raw_material/list') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-th-list"></i>
                            Material List
                        </a>

                        <a target="_blank" href="{{ url('index/raw_material/stock_policy') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-exclamation-triangle"></i>
                            Stock Policy
                        </a>

                        <a target="_blank" href="{{ url('index/material/vendor') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-users"></i>
                            Vendor
                        </a>

                    </div>

                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 26px; color: purple; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Direct & Subcont <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/material/smbmr') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-cubes"></i>
                            Direct Material BOM (SMBMR)
                        </a>

                        <a target="_blank" href="{{ url('index/material/plan_usage/monthly') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-calendar"></i>
                            Monthly Plan Usage
                        </a>

                        <a target="_blank" href="{{ url('index/material/plan_usage/daily') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-calendar-check-o"></i>
                            Daily Plan Usage
                        </a>
                    </div>

                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 26px; color: green; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Indirect <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/material/production_plan') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-codepen"></i>
                            Production Plan
                        </a>

                        <a target="_blank" href="{{ url('index/material/forecast_usage') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-calendar"></i>
                            Forecast Usage
                        </a>

                        <a target="_blank" href="{{ url('index/material/mrp/indirect') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-opencart"></i>
                            MRP Indirect Material
                        </a>

                    </div>


                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 26px; color: #00a7d0; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Control Delivery <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/material/control_delivery') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-calendar"></i>
                            Delivery Plan
                        </a>

                        <a target="_blank" href="{{ url('index/material/in_out') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-exchange"></i>
                            Material In Out
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
