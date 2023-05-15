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

    .color-palette {
      height: 35px;
      line-height: 35px;
      text-align: center;
  }

  .color-palette-set {
      margin-bottom: 15px;
  }

  .color-palette span {
      display: none;
      font-size: 12px;
  }

  .color-palette:hover span {
      display: block;
  }

  .color-palette-box h4 {
      position: absolute;
      top: 100%;
      left: 25px;
      margin-top: -40px;
      color: rgba(255, 255, 255, 0.8);
      font-size: 12px;
      display: block;
      z-index: 7;
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

<div class="box box-default color-palette-box">
    <div class="box-header with-border">
        <h3 class="box-title">{{ $title }} ({{$title_jp}})</h3>
    </div>
    <div class="box-body">
        <form method="GET" action="{{ url('excel/qa/report/kensa') }}">
            <h4>Filter</h4>
            <div class="row">
                <div class="col-md-4 col-md-offset-2">
                    <span style="font-weight: bold;">Date From</span>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon bg-white">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Select Date From" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <span style="font-weight: bold;">Date To</span>
                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon bg-white">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control datepicker" id="date_to"name="date_to" placeholder="Select Date To" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">                   
                <div class="col-md-4 col-md-offset-2">
                    <span style="font-weight: bold;">Inspection Level</span>
                    <div class="form-group">
                        <select class="form-control select2" multiple="multiple" id='inspectionLevelSelect' onchange="changeInspectionLevel()" data-placeholder="Select Inspection Level" style="width: 100%;color: black !important">
                            <option value="Test">Test</option>
                        </select>
                        <input type="text" name="inspection_level" id="inspection_level" style="color: black !important" hidden>
                    </div>
                </div>

                <div class="col-md-4">
                    <span style="font-weight: bold;">Material</span>
                    <div class="form-group">
                        <select class="form-control select2" multiple="multiple" id='materialSelect' onchange="changeMaterial()" data-placeholder="Select Material" style="width: 100%;color: black !important">
                         <option value="Test">Test</option>
                     </select>
                     <input type="text" name="material" id="material" style="color: black !important" hidden>
                 </div>
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
