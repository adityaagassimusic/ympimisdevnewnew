@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        .tab-master {
            font-size: 17px;
            border-color: black;
            color: black;
        }

        .tab-master-block {
            font-size: 17px;
            border-color: black;
            color: white;
            background-color: #808080;
        }

        .tab-master:hover {
            font-weight: bold;
            color: black;
            background-color: #E7E7E7;
            border-color: black;
        }

        .tab-master-block:hover {
            font-weight: bold;
            color: black;
            background-color: #bfbfbf;
            border-color: black;
            color: white;
        }

        .tab-process {
            font-size: 17px;
            border-color: green;
            color: black;
        }

        .tab-process-body {
            font-size: 17px;
            border-color: green;
            color: black;
            background-color: #ccff90;
        }

        .highlight-process {
            font-size: 20px;
            font-weight: bold;
            color: green;
            text-shadow: 1px 1px 5px #ccff90;
        }

        .tab-process:hover {
            font-weight: bold;
            color: black;
            background-color: #E7E7E7;
            border-color: green;
        }

        .tab-process-body:hover {
            font-weight: bold;
            color: black;
            background-color: #e1ffbd;
            border-color: green;
        }

        .tab-display {
            font-size: 17px;
            border-color: red;
            color: black;
        }

        .tab-display-body {
            font-size: 17px;
            border-color: red;
            color: black;
            background-color: #ff5757;
        }

        .tab-display:hover {
            font-weight: bold;
            color: black;
            background-color: #E7E7E7;
            border-color: purple;
        }

        .tab-display-body:hover {
            font-weight: bold;
            color: black;
            background-color: #ff9999;
            border-color: red;
        }

        .highlight-display {
            font-size: 20px;
            font-weight: bold;
            color: #C51C5A;
            text-shadow: 1px 1px 5px #ff9999;
        }

        .tab-report {
            font-size: 17px;
            border-color: purple;
            color: black;
        }

        .tab-report-body {
            font-size: 17px;
            border-color: purple;
            color: black;
            background-color: #a6a3f0;
        }

        .tab-report:hover {
            font-weight: bold;
            color: black;
            background-color: #E7E7E7;
            border-color: purple;
        }

        .tab-report-body:hover {
            font-weight: bold;
            color: black;
            background-color: #c1bff2;
            border-color: purple;
        }

        .highlight-report {
            font-size: 20px;
            font-weight: bold;
            color: purple;
            text-shadow: 1px 1px 5px #c1bff2;
        }

        .highlight-black {
            font-size: 20px;
            font-weight: bold;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            Surface Treatment<span class="text-purple"> 表面処理</span>
            <small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
        </h1>
    </section>
@stop
@section('content')
    <section class="content">
        @foreach (Auth::user()->role->permissions as $perm)
            @php
                $navs[] = $perm->navigation_code;
            @endphp
        @endforeach

        <div class="row">
            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" style="text-align: center;">
                <span style="font-size: 22px; color: black;"><i class="fa fa-angle-double-down"></i> Master <i
                        class="fa fa-angle-double-down"></i></span>
            </div>

            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" style="text-align: center;">
                <span style="font-size: 22px; color: green;"><i class="fa fa-angle-double-down"></i> Stocktaking <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/stocktaking/silver', 'fl_middle') }}"
                    class="btn btn-default btn-block tab-process">Silver</a>

                <br>
                <span style="font-size: 24px; color: green;"><i class="fa fa-angle-double-down"></i> Process Plating <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/process_middle_kensa', 'plt-incoming-fl') }}"
                    class="btn btn-default btn-block tab-process">Incoming Check <span
                        class="highlight-process">Key</span></a>
                <a href="{{ url('index/process_middle_kensa', 'plt-kensa-fl') }}"
                    class="btn btn-default btn-block tab-process">Kensa <span class="highlight-process">Key</span></a>

                <a href="{{ url('index/body/kensa/plt-incoming-body-fl') }}"
                    class="btn btn-default btn-block tab-process tab-process-body">Incoming Check <span
                        class="highlight-black">Body</span></a>
                <a href="{{ url('index/enthol/plt-enthol-body') }}"
                    class="btn btn-default btn-block tab-process tab-process-body">Cuci Enthole <span
                        class="highlight-black">Body</span></a>
                <a href="{{ url('index/body/kensa/plt-kensa-body-fl') }}"
                    class="btn btn-default btn-block tab-process tab-process-body">Kensa <span
                        class="highlight-black">Body</span></a>
            </div>

            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" style="text-align: center;">
                <span style="font-size: 22px; color: red;"><i class="fa fa-angle-double-down"></i> Display <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="#" class="btn btn-default btn-block tab-display" disabled>Production Result <span
                        class="highlight-display">Key</span></a>
                <a href="#" class="btn btn-default btn-block tab-display" disabled>Operator Kensa ΣTime <span
                        class="highlight-display">Key</span></a>
                <a href="{{url('index/body/resume_ng/fl')}}" class="btn btn-default btn-block tab-display tab-display-body">Production Result <span
                        class="highlight-black">Body</span></a>
            </div>

            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" style="text-align: center;">
                <span style="font-size: 22px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/report_ng') }}" class="btn btn-default btn-block tab-report">Not Good</a>
                <a href="{{ url('index/middle/report_production_result') }}"
                    class="btn btn-default btn-block t-block tab-report" disabled>Production Result</a>
                <a href="{{ url('index/body/report_ng') }}" class="btn btn-default btn-block tab-report-body">Not Good <span
                        class="highlight-black">Body</span></a>
                <a href="{{ url('index/body/prod_result') }}" class="btn btn-default btn-block tab-report-body">Production
                    Result <span class="highlight-black">Body</span></a>
                <a href="{{ url('index/body/rework_result') }}" class="btn btn-default btn-block tab-report-body">Rework
                    <span class="highlight-black">Body</span></a>

                <br>
                <span style="font-size: 22px; color: purple;"><i class="fa fa-angle-double-down"></i> Report Buffing <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/report_buffing_ng/fl?bulan=&fy=') }}"
                    class="btn btn-default btn-block tab-report">Resume <span class="highlight-report">Key</span></a>

                <br>
                <span style="font-size: 22px; color: purple;"><i class="fa fa-angle-double-down"></i> Report Plating <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/report_plt_ng_flute?bulan=&fy=') }}"
                    class="btn btn-default btn-block tab-report">Resume <span class="highlight-report">Key</span></a>
                <!-- <a href="index/body/resume/plt-sx" class="btn btn-default btn-block tab-report-body">Resume <span class="highlight-black">Body</span></a> -->

            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
        });
    </script>
@endsection
