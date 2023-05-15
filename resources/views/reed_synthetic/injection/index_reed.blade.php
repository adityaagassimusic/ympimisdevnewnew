@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        .highlight-process {
            font-size: 24px;
            font-weight: bold;
            color: red;
            text-shadow: 1px 1px 5px #ccff90;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}<span class="text-purple"> {{ $title_jp }}</span>
        </h1>
    </section>
@stop
@section('content')
    <section class="content">
        <div class="row">

            <div class="col-xs-4" style="text-align: center; color: red;">

                <span style="font-size: 30px; color: green;">
                    <i class="fa fa-angle-double-down"></i> Warehouse <i class="fa fa-angle-double-down"></i>
                </span>

                <a href="{{ url('index/reed/resin_receive') }}" class="btn btn-default btn-block"
                    style="font-size: 24px; border-color: green;">Resin Reception</a>
                <!-- <a href="{{ url('index/reed/label_verification') }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Label Verification</a> -->
                <a href="{{ url('index/reed/store_verification') }}" class="btn btn-default btn-block"
                    style="font-size: 24px; border-color: green;">Store Verification</a>
                <a href="{{ url('index/reed/warehouse_delivery') }}" class="btn btn-default btn-block"
                    style="font-size: 24px; border-color: green;">Resin Delivery</a>

                <span style="font-size: 30px; color: green;">
                    <i class="fa fa-angle-double-down"></i> Molding <i class="fa fa-angle-double-down"></i>
                </span>
                <a href="{{ url('index/reed/molding_verification') }}" class="btn btn-default btn-block"
                    style="font-size: 24px; border-color: green;">Setup Molding Verification</a>


                <span style="font-size: 30px; color: green;">
                    <i class="fa fa-angle-double-down"></i> Injection Process <i class="fa fa-angle-double-down"></i>
                </span>
                <a href="{{ url('index/reed/injection_resin_receive') }}" class="btn btn-default btn-block"
                    style="font-size: 24px; border-color: green;">Resin Reception</a>
                <a href="{{ url('index/reed/injection_order') }}" class="btn btn-default btn-block"
                    style="font-size: 24px; border-color: green;">Create Injection Order</a>
                <a href="{{ url('index/reed/injection_verification') }}" class="btn btn-default btn-block"
                    style="font-size: 24px; border-color: green;">Injection Verification</a>
                <a href="{{ url('index/reed/delivery/injection') }}" class="btn btn-default btn-block"
                    style="font-size: 24px; border-color: green; background-color: #ccff90;">After Injection Delivery
                    (<span class="highlight-process">CS</span>)</a>

            </div>






            <div class="col-xs-4" style="text-align: center; color: red;">
                <span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">After
                    Injection Stock Monitoring</a>
            </div>






            <div class="col-xs-4" style="text-align: center; color: purple;">
                <span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Report <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/reed/injection_report/approval') }}" class="btn btn-default btn-block"
                    style="font-size: 24px; border-color: purple;">Approval Report</a>
                <a href="{{ url('index/reed/injection_report/check-dimensi') }}" class="btn btn-default btn-block"
                    style="font-size: 24px; border-color: purple;">Check Dimensi Injection</a>
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
