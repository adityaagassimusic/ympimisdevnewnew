@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
    thead>tr>th{
        text-align:center;
    }
    tbody>tr>td{
        text-align:center;
    }
    tfoot>tr>th{
        text-align:center;
    }
    td:hover {
        overflow: visible;
    }
    table.table-bordered{
        border:1px solid black;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid rgb(211,211,211);
        padding-top: 0;
        padding-bottom: 0;
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid rgb(211,211,211);
    }
    #loading, #error { display: none; }
</style>
@endsection

@section('header')
<section class="content-header">
    <h1>
        {{ $page }}
    </h1>
</section>
@endsection


@section('content')

<section class="content">
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
        <p style="position: absolute; color: White; top: 45%; left: 45%;">
            <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
        </p>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="col-xs-8 col-xs-offset-2">
                        <input type="text" id="id" value="{{ $data->id }}" hidden>

                        <div class="col-xs-12" style="padding-bottom: 1%;">
                            <div class="col-xs-3" align="right" style="padding: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Date<span class="text-red">*</span></span>
                            </div>
                            <div class="col-xs-5">
                                <div class="input-group date">
                                    <div class="input-group-addon" style="color:white; background-color: rgba(126,86,134,.7)">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control datepicker" id="date" value="{{ $data->date }}" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12" style="padding-bottom: 1%;">
                            <div class="col-xs-3" align="right" style="padding: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Material<span class="text-red">*</span></span>
                            </div>
                            <div class="col-xs-5">
                                <input class="form-control" type="text" id="material_number" value="{{ $data->material_number }}" readonly>
                            </div>
                        </div>

                        <div class="col-xs-12" style="padding-bottom: 1%;">
                            <div class="col-xs-3" align="right" style="padding: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Material Description<span class="text-red">*</span></span>
                            </div>
                            <div class="col-xs-8">
                                <input class="form-control" type="text" id="material_description" value="{{ $data->material_description }}" readonly>
                            </div>
                        </div>

                        <div class="col-xs-12" style="padding-bottom: 1%;">
                            <div class="col-xs-3" align="right" style="padding: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Bun<span class="text-red">*</span></span>
                            </div>
                            <div class="col-xs-8">
                                <input class="form-control" type="text" id="bun" value="{{ $data->bun }}" readonly>
                            </div>
                        </div>

                        <div class="col-xs-12" style="padding-bottom: 1%;">
                            <div class="col-xs-3" align="right" style="padding: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Plan Usage<span class="text-red">*</span></span>
                            </div>
                            <div class="col-xs-5">
                                <input class="form-control" type="text" id="plan" value="{{ $data->usage }}" readonly>
                            </div>
                        </div>

                        <div class="col-xs-12" style="padding-bottom: 1%;">
                            <div class="col-xs-3" align="right" style="padding: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Actual Usage<span class="text-red">*</span></span>
                            </div>
                            <div class="col-xs-5">
                                <input class="form-control" type="text" id="actual" value="{{ $data->quantity }}" readonly>
                            </div>
                        </div>

                        <div class="col-xs-12" style="padding-bottom: 1%;">
                            <div class="col-xs-3" align="right" style="padding: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Reason<span class="text-red">*</span></span>
                            </div>
                            <div class="col-xs-5">
                                <select class="form-control select2" data-placeholder="Pilih Reason" id="reason" style="width: 100% height: 35px; font-size: 15px;" required>
                                    <option value=""></option>
                                    @foreach($reasons as $reason)
                                    <option value="{{ $reason }}">{{ $reason }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-12" style="padding-bottom: 1%;">
                            <div class="col-xs-3" align="right" style="padding: 0px;">
                                <span style="font-weight: bold; font-size: 16px;">Uraian Reason<span class="text-red">*</span></span>
                            </div>
                            <div class="col-xs-8">
                                <textarea class="form-control" rows='3' name="detail" id="detail" placeholder="Detail reason ..." style="width: 100%; font-size: 15px;" required></textarea>
                            </div>
                        </div>
                        <div class="col-xs-12" style="padding-right: 12%;">
                            <button type="submit" class="btn btn-success pull-right" onclick="saveReason()"><i class="fa fa-save"></i>&nbsp;&nbsp;&nbsp;Simpan</button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@stop

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function() {
        $('body').toggleClass("sidebar-collapse");

        $('.select2').select2();
        
    });

    function saveReason() {
        var id = $('#id').val();
        var reason = $('#reason').val();
        var detail = $('#detail').val();

        if(id == '' || reason == '' || detail == ''){
            openErrorGritter('Error!', 'Tanda (*) harus diisi');
            return false;
        }

        var data = {
            id:id,
            reason:reason,
            detail:detail,
        }

        $.post('{{ url("save/material/reason_over_usage") }}',data, function(result, status, xhr) {
            if(result.status){
                window.open('{{ url("home") }}', '_self');

            }else{
                openErrorGritter('Error!', result.message);
            }
        });
    }

    function openSuccessGritter(title, message){
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-success',
            image: '{{ url("images/image-screen.png") }}',
            sticky: false,
            time: '2000'
        });
    }

    function openErrorGritter(title, message) {
        jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-danger',
            image: '{{ url("images/image-stop.png") }}',
            sticky: false,
            time: '2000'
        });
    }


</script>

@stop