@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        .highlight-cs {
            font-size: 20px;
            font-weight: bold;
            color: red;
            text-shadow: 1px 1px 5px #ccff90;
        }

        .highlight-gms {
            font-size: 20px;
            font-weight: bold;
            color: greenyellow;
            text-shadow: 1px 1px 5px black;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            Press Material Process <small><span class="text-purple"> プレスマテリアルプロセス</span></small>
        </h1>
    </section>
@stop
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-4" style="text-align: center;">
                <span style="font-size: 25px; color: green;"><i class="fa fa-angle-double-down"></i> Master <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/press/master_kanagata') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: green;">Master Kanagata</a>
                <span style="font-size: 25px; color: green;"><i class="fa fa-angle-double-down"></i> Press Machine <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/press/create') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: green;">Input Press Machine</a>
                    <a href="{{ url('index/press/transaction') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: green;">Kanagata Transaction</a>
                <!-- <a href="{{ url('index/press/maintenance') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: green;">Kanagata Maintenance</a> -->

                <span style="font-size: 25px; color: green;"><i class="fa fa-angle-double-down"></i> Reed Synthetic <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/reed/transfer') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: green; background-color: #ff6c5c;">Transfer Laser Material (<span
                        class="highlight-gms">GMS</span>)</a>
                <a href="{{ url('index/reed/trimming_verification') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: green;">Trimming Verification</a>
                <a href="{{ url('index/reed/delivery/trimming') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: green; background-color: #ccff90;">After Trimming Delivery (<span
                        class="highlight-process">CS</span>)</a>

            </div>
            <div class="col-xs-4" style="text-align: center; color: red;">
                <span style="font-size: 25px;"><i class="fa fa-angle-double-down"></i> Display <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/press/monitoring') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: red;">Press Machine Monitoring</a>
                    <a href="{{ url('index/press/kanagata_lifetime') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: red;">Kanagata Lifetime Monitoring</a>
            </div>
            <div class="col-xs-4" style="text-align: center; color: purple;">
                <span style="font-size: 25px;"><i class="fa fa-angle-double-down"></i> Report <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/press/report_trouble') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: purple;">Press Machine Trouble Report</a>
                <a href="{{ url('index/press/report_prod_result') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: purple;">Press Machine Production Result</a>
                <a href="{{ url('index/press/report_kanagata_lifetime') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: purple;">Press Machine Kanagata Lifetime</a>
                    <a href="{{ url('index/press/transaction_report') }}" class="btn btn-default btn-block"
                    style="font-size: 20px; border-color: purple;">Kanagata Transaction Report</a>
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
