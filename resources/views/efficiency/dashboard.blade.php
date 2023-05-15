@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">

    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
        </h1>
    </section>
@stop
@section('content')
    <section class="content">
        <div class="row">
            <div class="col-xs-2">
                <div class="box box-solid" style="border: 1px solid black;">
                    <div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(161,134,190);">
                        <center><span style="font-weight: bold;">Educational Instrument (EI)</span></center>
                    </div>
                    <div class="box-body" style="display: block;">
                        <a href="{{ url('index/efficiency/material/EDUCATIONAL INSTRUMENT') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(161,134,190);">Material & Std
                            Time</a>
                        <a href="{{ url('index/efficiency/input/EDUCATIONAL INSTRUMENT') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(161,134,190);">Input</a>
                        <a href="{{ url('index/efficiency/output/EDUCATIONAL INSTRUMENT') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(161,134,190);">Output</a>
                        <a href="{{ url('index/efficiency/monitoring_detail/EDUCATIONAL INSTRUMENT') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(161,134,190);">Monitoring</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="box box-solid" style="border: 1px solid black;">
                    <div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(187,230,228);">
                        <center><span style="font-weight: bold;">Parts Process (WI-PP)</span></center>
                    </div>
                    <div class="box-body" style="display: block;">
                        <a href="{{ url('index/efficiency/material/PARTS PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(187,230,228);">Material & Std Time</a>
                        <a href="{{ url('index/efficiency/input/PARTS PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(187,230,228);">Input</a>
                        <a href="{{ url('index/efficiency/output/PARTS PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(187,230,228);">Output</a>
                        <a href="{{ url('index/efficiency/monitoring_detail/PARTS PROCESS') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(187,230,228);">Monitoring</a>
                    </div>
                </div>
            </div>
            {{-- <div class="col-xs-2">
                <div class="box box-solid" style="border: 1px solid black;">
                    <div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(187,230,228);">
                        <center><span style="font-weight: bold;">Key Parts Process (WI-KPP)</span></center>
                    </div>
                    <div class="box-body" style="display: block;">
                        <a href="{{ url('index/efficiency/material/KEY PARTS PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(187,230,228);">Material & Std Time</a>
                        <a href="{{ url('index/efficiency/input/KEY PARTS PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(187,230,228);">Input</a>
                        <a href="{{ url('index/efficiency/output/KEY PARTS PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(187,230,228);">Output</a>
                        <a href="{{ url('index/efficiency/monitoring_detail/KEY PARTS PROCESS') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(187,230,228);">Monitoring</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="box box-solid" style="border: 1px solid black;">
                    <div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(66,191,221);">
                        <center><span style="font-weight: bold;">Body Parts Process (WI-BPP)</span></center>
                    </div>
                    <div class="box-body" style="display: block;">
                        <a href="{{ url('index/efficiency/material/BODY PARTS PROCESS') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(66,191,221);">Material & Std Time</a>
                        <a href="{{ url('index/efficiency/input/BODY PARTS PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(66,191,221);">Input</a>
                        <a href="{{ url('index/efficiency/output/BODY PARTS PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(66,191,221);">Output</a>
                        <a href="{{ url('index/efficiency/monitoring_detail/BODY PARTS PROCESS') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(66,191,221);">Monitoring</a>
                    </div>
                </div>
            </div> --}}
            <div class="col-xs-2">
                <div class="box box-solid" style="border: 1px solid black;">
                    <div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(201,117,16);">
                        <center><span style="font-weight: bold;">Welding Process (WI-WP)</span></center>
                    </div>
                    <div class="box-body" style="display: block;">
                        {{-- <a href="{{ url('index/efficiency/manpower/WP') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(201,117,16);">Manpower</a> --}}
                        <a href="{{ url('index/efficiency/material/WELDING PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(201,117,16);">Material & Std Time</a>
                        <a href="{{ url('index/efficiency/input/WELDING PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(201,117,16);">Input</a>
                        <a href="{{ url('index/efficiency/output/WELDING PROCESS') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(201,117,16);">Output</a>
                        <a href="{{ url('index/efficiency/monitoring_detail/WELDING PROCESS') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(201,117,16);">Monitoring</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="box box-solid" style="border: 1px solid black;">
                    <div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(255,210,63);">
                        <center><span style="font-weight: bold;">Surface Treatment (WI-ST)</span></center>
                    </div>
                    <div class="box-body" style="display: block;">
                        <a href="{{ url('index/efficiency/material/SURFACE TREATMENT') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">Material & Std Time</a>
                        <a href="{{ url('index/efficiency/input/SURFACE TREATMENT') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(255,210,63);">Input</a>
                        <a href="{{ url('index/efficiency/output/SURFACE TREATMENT') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(255,210,63);">Output</a>
                        <a href="{{ url('index/efficiency/monitoring_detail/SURFACE TREATMENT') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(255,210,63);">Monitoring</a>
                    </div>
                </div>
            </div>
            <div class="col-xs-2">
                <div class="box box-solid" style="border: 1px solid black;">
                    <div class="box-header" style="border-bottom: 1px solid black; background-color: rgb(238,66,102);">
                        <center><span style="font-weight: bold;">Final Assembly (WI-FA)</span></center>
                    </div>
                    <div class="box-body" style="display: block;">
                        <a href="{{ url('index/efficiency/material/ASSEMBLY') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(238,66,102);">Material & Std Time</a>
                        <a href="{{ url('index/efficiency/input/ASSEMBLY') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(238,66,102);">Input</a>
                        <a href="{{ url('index/efficiency/output/ASSEMBLY') }}" class="btn btn-default btn-block"
                            style="border-color: rgb(238,66,102);">Output</a>
                        <a href="{{ url('index/efficiency/monitoring_detail/ASSEMBLY') }}"
                            class="btn btn-default btn-block" style="border-color: rgb(238,66,102);">Monitoring</a>
                    </div>
                </div>
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

        });
    </script>
@endsection
