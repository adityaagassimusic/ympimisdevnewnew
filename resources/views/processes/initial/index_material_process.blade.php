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
                        style="background-color: #a1887f; font-weight: bold; padding: 1%; margin-top: 0%; margin-bottom: 2%; color: white; border: 1px solid darkgrey; border-radius: 5px;">
                        MATERIAL PROCESS
                    </h1>
                </center>
                <div class="row">
                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 24px; color: black; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Master <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/material_process/material') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-th-list"></i>
                            Master Material
                        </a>

                        <a target="_blank" href="{{ url('index/material_process/operator') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-users"></i>
                            Master Operator
                        </a>

                        <a target="_blank" href="{{ url('index/material_process/kanban') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-calendar"></i>
                            Master Kanban
                        </a>

                        <a target="_blank" href="{{ url('index/material_process/kanban_flow') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-recycle"></i>
                            Master Flow Material
                        </a>

                        <span style="font-size: 26px; color: #827717; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Kanban Control <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/tpro/resume_kanban') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-cubes"></i>
                            Resume Kanban Edar
                        </a>

                        <a target="_blank" href="{{ url('index/tpro/check_kanban') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-files-o"></i>
                            Check Aktual Kanban
                        </a>
                    </div>


                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 26px; color: green; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/material_process/kensa/machining') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-codepen"></i>
                            Kensa Machining
                        </a>

                        <a target="_blank" href="{{ url('index/material_process/kensa/senban') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-codepen"></i>
                            Kensa Senban
                        </a>

                        <a target="_blank" href="{{ url('index/material_process/kensa/sanding') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-codepen"></i>
                            Kensa Sanding
                        </a>

                        <a target="_blank" href="{{ url('index/material_process/kensa/press') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-codepen"></i>
                            Kensa Press
                        </a>

                        <a target="_blank" href="{{ url('index/qa/kensa_check/KPP') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-codepen"></i>
                            Kensa QA
                        </a>

                        
                    </div>


                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 26px; color: purple; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Stock <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('/index/initial/stock_monitoring', 'mpro') }}" class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-cubes"></i>
                            Stock Monitoring
                        </a>

                        <a target="_blank" href="{{ url('/index/initial/stock_trend', 'mpro') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-bar-chart"></i>
                            Stock Trend
                        </a>

                        <span style="font-size: 26px; color: red; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Antrian <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/kpp_board',['Lathe','1']) }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            Kanban Queue <span class="highlight-display">Lathe Process 1</span>
                        </a>

                        <a target="_blank" href="{{ url('index/kpp_board',['Lathe','2']) }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            Kanban Queue <span class="highlight-display">Lathe Process 2</span>
                        </a>

                        <a target="_blank" href="{{ url('index/kpp_board',['Lathe','3']) }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            Kanban Queue <span class="highlight-display">Lathe Process 3</span>
                        </a>
                        <a target="_blank" href="{{ url('index/kpp_board',['MC1', '1']) }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            Kanban Queue <span class="highlight-display">Machining 1 Process</span>
                        </a>

                        <a target="_blank" href="{{ url('index/kpp_board',['MC2', '1']) }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            Kanban Queue <span class="highlight-display">Machining 2 Process</span>
                        </a>
                        <a target="_blank" href="{{ url('index/kpp_board','annealing_process') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            Kanban Queue <span class="highlight-display">Annealing Process</span>
                        </a>
                        <a target="_blank" href="{{ url('index/kpp_board',['Press', '1']) }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            Kanban Queue <span class="highlight-display">Press Process</span>
                        </a>
                        <a target="_blank" href="{{ url('index/kpp_board','sanding_process') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            Kanban Queue <span class="highlight-display">Sanding Process</span>
                        </a>
                        <a target="_blank" href="{{ url('index/kpp_board','cuci_process') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            Kanban Queue <span class="highlight-display">Cuci Process</span>
                        </a>
                    </div>


                    <div class="col-xs-12 col-md-3 col-lg-3" style="text-align: center;">
                        <span style="font-size: 26px; color: #00a7d0; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/qa/report/kensa') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-codepen"></i>
                            Report Kensa
                        </a>
                        <a target="_blank" href="{{ url('index/kpp_report','perolehan') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-codepen"></i>
                            Perolehan
                        </a>
                        <a target="_blank" href="{{ url('index/qa/report/kensa') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-codepen"></i>
                            Efesiensi
                        </a>
                        <a target="_blank" href="{{ url('index/qa/report/kensa') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-codepen"></i>
                            Antrian
                        </a>


                        <span style="font-size: 26px; color: red; font-weight :bold;">
                            <i class="fa fa-angle-double-down"></i> Display <i class="fa fa-angle-double-down"></i>
                        </span>

                        <a target="_blank" href="{{ url('index/material_process/ng_rate?tanggal=&location=') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            NG Rate</span>
                        </a>

                        <!-- <a target="_blank" href="{{ url('index/welding/op_ng/sx?tanggal=&location') }}"
                            class="btn btn-default btn-social"
                            style="border: 1.5px solid black; font-size: 16px; border-radius: 10px; width: 100%; margin-bottom: 2%;">
                            <i style="font-size: 16px;" class="fa fa-television"></i>
                            Operator NG Rate</span>
                        </a> -->
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
