@extends('layouts.master')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<style type="text/css">
    .highlight-cs {
        font-size: 24px;
        font-weight: bold;
        color: red;
        text-shadow: 1px 1px 5px #ccff90;
    }

    .highlight-gms {
        font-size: 24px;
        font-weight: bold;
        color: greenyellow;
        text-shadow: 1px 1px 5px black;
    }
</style>
@stop
@section('header')
<section class="content-header">
    <h1>
        Sanding Material Process <small><span class="text-purple"> </span></small>
    </h1>
</section>
@stop
@section('content')
<section class="content">
    <div class="row">
        <div class="col-xs-4" style="text-align: center;">
            <span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Proccess <i
                class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/repair/sanding') }}" class="btn btn-default btn-block"
                style="font-size: 24px; border-color: green;">Sanding
            Monitoring</a>
            <a href="{{ url('index/sanding/comparison') }}" class="btn btn-default btn-block"
            style="font-size: 24px; border-color: green;">Sanding Comparison</a>

            <a href="{{ url('index/material_check/sanding') }}" class="btn btn-default btn-block"
            style="font-size: 24px; border-color: green;">Visual Check Material </a>

        </div>
        <div class="col-xs-4" style="text-align: center; color: red;">
            <span style="font-size: 30px;"><i class="fa fa-angle-double-down"></i> Display <i
                class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/monitoring/material_check/sanding') }}" class="btn btn-default btn-block"
                style="font-size: 24px; border-color: red;">Monitoring Visual Check Material</a>
            </div>
        </div>
    </section>
    @endsection
    @section('scripts')
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
