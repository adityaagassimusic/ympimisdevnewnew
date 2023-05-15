@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <link rel="stylesheet" href="{{ url('css/bootstrap-datetimepicker.min.css') }}">
    <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
    <link href="<?php echo e(url('css/jquery.numpad.css')); ?>" rel="stylesheet">
    <style type="text/css">            
    
        #appendAppendTable {
            display: flex;
            flex-wrap: wrap;
            flex-direction: column;

            width: 100%;
            overflow-x: scroll;
            overflow-y: scroll;
            height: 500px;
        }

        .tableMaster, .tableHeadTh, .tableBodyTd {        
            border: 1px solid black;            
        }

        .tableMaster {
            position:sticky;            
            border-collapse:collapse;
            margin: 5px 5px;
            overflow-x: scroll;
        }

        .tableHeadTh {            
            padding: 2px;
            background-color: rgb(126,86,134); 
            color: #FFD700;             
            
            justify-content: space-between;
            align-self: center;
        }

        .tableHeadTh button {
            float: right;
        }

        .tableBodyTd {
            padding: 2px;
            text-transform: uppercase !important;                     
        }

        .tableBodyTd:hover {
            background-color: rgba(126, 86, 134, 0.346);
            /* color: #FFD700; */
            cursor: pointer;
        }

        .tbdOrdering {
            width: 10%;
            text-align: center;
            background-color: rgb(126,86,134);
            color: #FFD700;
        }        

        .tbdAction {
            color: #fff;
            padding: 1px 5px;            
            background-color: #f0ad4e;
            border: 1px solid #eea236;
            border-radius: 2px;
        }

        .tbdAction:hover {
            padding: 1px 5px;            
            background-color: #eea236;
            border: 1px solid #eea236;
        }

        .btnAddProcess {
            text-align: center;            
        }

        .btnAddProcess {
            color: #fff ;            
            padding: 1px 5px;
            background-color: #5cb85c;
            border: 1px solid #4cae4c;
            border-radius: 2px;
        }

        .btnAddProcess:hover {
            color: #fff;
            background-color: #4cae4c;
            border: 1px solid #4cae4c;            
        }

    </style>

@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }} ~ {{ strtoupper($location) }} <small class="text-purple">{{ $title_jp }} <span
                    style="display: none">{{ $origin_group_code }}</span></small>

        </h1>
    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Please Wait...<i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>
        @if (session('status'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                {{ session('error') }}
            </div>
        @endif

        <div style="padding-right: 5px;">            
            <div class="row ms-5" style="background-color: #e7e7e7">
                <div id="appendAppendTable">                                        
                </div>            
            </div>
        </div>

    </section>

@endsection
@section('scripts')
    <script src="{{ url('js/moment.min.js') }}"></script>
    <script src="{{ url('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="<?php echo e(url('js/jquery.numpad.js')); ?>"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            fillList();
        });

        function onlyUnique(value, index, self) {
            return self.indexOf(value) === index;
        }

        function fillList() {
            $('#loading').show();

            var data = {
                location: '{{ $location }}',
                origin_group_code: '{{ $origin_group_code }}'
            }
            $.get('{{ url("fetch/body_parts_process/master_flow") }}', data, function(result, status, xhr) {
                if (result.status) {                    

                    var processing = [];                    

                    for (var i = 0; i < result.flow.length; i++) {
                        processing.push(result.flow[i].material_type);
                    }

                    var processing_unik = processing.filter(onlyUnique);         
                    
                    for (var i = 0; i < processing_unik.length; i++) {                        
                        var table = $("<table class='tableMaster table-striped'>");
                        var tableHead = $("<thead>");
                        var tableHeadRow = $("<tr>");
                        var tableHeadCell = $("<th class='tableHeadTh' colspan='2'>").html(processing_unik[i] +' <button class="tbdAction"><i class="fa fa-pencil-square-o"></button>');                        
                        tableHeadRow.append(tableHeadCell);                        
                        tableHead.append(tableHeadRow);
                        table.append(tableHead);
                                                
                        var tableBody = $("<tbody>");

                        for (var j = 0; j < result.flow.length; j++) {

                            if (result.flow[j].material_type === processing_unik[i]) {
                                var tableBodyRow = $("<tr>");
                                var tableBodyCell1 = $("<td class='tableBodyTd tbdOrdering'>").text(result.flow[j].ordering);
                                var tableBodyCell2 = $("<td class='tableBodyTd tbdFlow'>").text(result.flow[j].flow);                                
                                tableBodyRow.append(tableBodyCell1);
                                tableBodyRow.append(tableBodyCell2);                                
                                tableBody.append(tableBodyRow);
                            }
                        }
                        table.append(tableBody);
                        
                        $("#appendAppendTable").append(table);
                    }
                    
                    $('#loading').hide();
                } else {
                    $('#loading').hide();
                    alert('Attempt to retrieve data failed');
                }

            });            
        }
    </script>

@endsection
