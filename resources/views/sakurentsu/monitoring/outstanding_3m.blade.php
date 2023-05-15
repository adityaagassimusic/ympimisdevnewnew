@extends('layouts.display')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<style type="text/css">
    table.table-bordered {
        border: 1px solid black;
    }

    table.table-bordered>thead>tr>th {
        border: 1px solid black;
        background-color: rgb(126, 86, 134);
        color: white;
        vertical-align: middle;
        font-size: 15pt;
    }

    table.table-bordered>tbody>tr>td {
        border: 1px solid black;
        color: white;
        vertical-align: middle;
        padding: 0% 1% 0% 1%;
        font-size: 14pt;
    }

    table.table-bordered>tfoot>tr>th {
        border: 1px solid black;
    }

    .non-active {
        font-weight: bold;
    }

    #loading,
    #error {
        display: none;
    }

    .alarm {
        -webkit-animation: alarm_ani 2s infinite;  /* Safari 4+ */
        -moz-animation: alarm_ani 2s infinite;  /* Fx 5+ */
        -o-animation: alarm_ani 2s infinite;  /* Opera 12+ */
        animation: alarm_ani 2s infinite;  /* IE 10+, Fx 29+ */
        font-weight: bold;
    }

    @-webkit-keyframes alarm_ani {
        0%, 30% {
            background-color: rgba(255, 112, 102, 255);
        }
        31%, 60% {
            background-color: rgba(255, 112, 102, 100);
        }
        61%, 100% {
            background-color: rgba(255, 112, 102, 0);
        }
    }
</style>
@endsection

@section('header')
<section class="content-header">
    <h1>
        {{ $title }}
        <small><span class="text-purple">{{ $title_jp }}</span></small>
    </h1>
</section>
@stop

@section('content')
<section class="content" style="font-size: 0.8vw;">
    <div id="loading"
    style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
    <p style="position: absolute; color: White; top: 45%; left: 40%;">
        <span style="font-size: 40px">Please Wait <i class="fa fa-spin fa-refresh"></i></span>
    </p>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="col-xs-12" style="margin: 0% 0% 1% 0%; padding: 0px;">
            <div class="col-xs-1" style="margin: 0.5%; padding: 0px;">
                <a href="javascript:void(0)"
                style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: #90ed7d;"
                id="btn_ALL" onclick="btnCategory('ALL')" class="btn btn-sm">ALL</a>
            </div>
            <div class="col-xs-1" style="margin: 0.5%; padding: 0px;">
                <a href="javascript:void(0)"
                style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                id="btn_LOG" onclick="btnCategory('LOG')" class="btn btn-sm">LOG</a>
            </div>
            <div class="col-xs-1" style="margin: 0.5%; padding: 0px;">
                <a href="javascript:void(0)"
                style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                id="btn_FINAL_ASSY" onclick="btnCategory('FINAL_ASSY')" class="btn btn-sm">FINAL ASSY</a>
            </div>
            <div class="col-xs-1" style="margin: 0.5%; padding: 0px;">
                <a href="javascript:void(0)"
                style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                id="btn_EDIN" onclick="btnCategory('EDIN')" class="btn btn-sm">EDIN</a>
            </div>
            <div class="col-xs-1" style="margin: 0.5%; padding: 0px;">
                <a href="javascript:void(0)"
                style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                id="btn_SURFACE_TREATMENT" onclick="btnCategory('SURFACE_TREATMENT')" class="btn btn-sm">SURFACE TREATMENT</a>
            </div>
            <div class="col-xs-1" style="margin: 0.5%; padding: 0px;">
                <a href="javascript:void(0)"
                style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                id="btn_WELDING" onclick="btnCategory('WELDING')" class="btn btn-sm">WELDING</a>
            </div>
            <div class="col-xs-1" style="margin: 0.5%; padding: 0px;">
                <a href="javascript:void(0)"
                style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                id="btn_PP" onclick="btnCategory('PP')" class="btn btn-sm">PP</a>
            </div>
            <div class="col-xs-1" style="margin: 0.5%; padding: 0px;">
                <a href="javascript:void(0)"
                style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                id="btn_QA" onclick="btnCategory('QA')" class="btn btn-sm">QA</a>
            </div>
        </div>
        <div class="col-xs-12" style="margin-top: 10px;">
            <table class="table table-bordered" id="tableResume">
                <thead>
                    <tr>
                        <th style="width: 1%; text-align: center">#</th>
                        <th style="width: 10%; text-align: center">SAKURENTSU NUMBER</th>
                        <th style="width: 20%; text-align: center">3M TITLE</th>
                        <th style="width: 20%; text-align: center">DEPT</th>
                        <th style="width: 20%; text-align: center">MATERIAL</th>
                        <th style="width: 10%; text-align: center">PROCCESS</th>
                        <th style="width: 10%; text-align: center">TARGET IMPLEMENTASI</th>
                        <!-- <th style="width: 10%; text-align: center">NOTE</th> -->
                    </tr>
                </thead>
                <tbody id="tableBodyResume">
                </tbody>
            </table>
        </div>
    </div>
</div>

</section>


@endsection

@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('js/buttons.flash.min.js') }}"></script>
<script src="{{ url('js/jszip.min.js') }}"></script>
<script src="{{ url('js/vfs_fonts.js') }}"></script>
<script src="{{ url('js/buttons.html5.min.js') }}"></script>
<script src="{{ url('js/buttons.print.min.js') }}"></script>
<script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
<script src="{{ url('js/highcharts.js') }}"></script>
<script src="{{ url('js/highcharts-3d.js') }}"></script>

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function() {

        $('body').toggleClass("sidebar-collapse");

        $('.select2').select2({
            allowClear: true
        });

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            autoclose: true,
        });

        showTable();
        setInterval(showTable, 1000*60*120);

    });

    var storage_locations = [];


    function btnCategory(cat) {
        window.location.replace("{{ url('index/sakurentsu/display/monitoring/') }}/"+cat);
    }

    function showTable() {
        $('#loading').show();

        $('#btn_ALL').css('background-color', 'white');
        $('#btn_EDIN').css('background-color', 'white');
        $('#btn_KPP').css('background-color', 'white');
        $('#btn_PP').css('background-color', 'white');
        $('#btn_WELDING').css('background-color', 'white');
        $('#btn_SURFACE_TREATMENT').css('background-color', 'white');
        $('#btn_FINAL_ASSY').css('background-color', 'white');
        $('#btn_LOG').css('background-color', 'white');
        $('#btn_QA').css('background-color', 'white');

        $('#btn_{{Request::segment(5)}}').css('background-color', '#90ed7d');

        if ('{{Request::segment(5)}}' == 'EDIN') {
            var dep = 'Educational Instrument (EI) Department';
        }  else if ('{{Request::segment(5)}}' == 'PP') {
            var dep = 'Woodwind Instrument - Parts Process (WI-PP) Department';
        } else if ('{{Request::segment(5)}}' == 'WELDING') {
            var dep = 'Woodwind Instrument - Welding Process (WI-WP) Department';
        } else if ('{{Request::segment(5)}}' == 'SURFACE_TREATMENT') {
            var dep = 'Woodwind Instrument - Surface Treatment (WI-ST) Department';
        } else if ('{{Request::segment(5)}}' == 'FINAL_ASSY') {
            var dep = 'Woodwind Instrument - Assembly (WI-A) Department';
        } else if ('{{Request::segment(5)}}' == 'LOG') {
            var dep = 'Logistic Department';
        } else if ('{{Request::segment(5)}}' == 'QA') {
            var dep = 'Quality Assurance Department';
        }

        // var data = {
        //     'category' : '{{Request::segment(5)}}'
        // }

        $.get('{{ url("fetch/sakurentsu/display/monitoring") }}', function(result, status, xhr) {
            if (result.status) {

                $('#tableResume').DataTable().clear();
                $('#tableResume').DataTable().destroy();
                $('#tableBodyResume').html("");
                $('#tableBodyResume').empty();
                var tableData = '';

                $.each(result.datas, function(index, value){
                    var dpt = value.related_department.split(',');
                    
                    if ('{{Request::segment(5)}}' != 'ALL') {
                        stat_filter = false;

                        $.each(dpt, function(index2, value2){
                            if (value2 == dep) {
                                stat_filter = true;
                            }
                        })
                    } else {
                        stat_filter = true;
                    }
                    
                    if (stat_filter) {
                        tableData += '<tr>';

                        tableData += '<td style="text-align: center;">';
                        tableData += (index+1);
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += value.sakurentsu_number;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += value.title;
                        tableData += '</td>';


                        tableData += '<td>';
                        $.each(dpt, function(index4, value4){
                            $.each(result.depts, function(index3, value3){                    
                                if (value4 == value3.department_name) {
                                    tableData += value3.department_shortname_2+'<br>';
                                }
                            })
                        })
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += value.product_name;
                        tableData += '</td>';

                        tableData += '<td>';
                        tableData += value.proccess_name;
                        tableData += '</td>';

                        var now = new Date("{{ date('Y-m-d',strtotime('+ 2 days', strtotime(date('Y-m-d')))) }}");

                        var target = new Date(value.started_date);

                        var cls = '';

                        if( target <= now){
                            cls = 'class="alarm"';
                        }

                        tableData += '<td '+cls+' style="text-align: right; font-size: 20pt; font-weight: bold;">';
                        tableData += value.started_date;
                        tableData += '</td>';

                        tableData += '</tr>';
                    }


                })


                $('#tableBodyResume').append(tableData);

                var tableResume = $('#tableResume').DataTable({
                    'dom': 'Bfrtip',
                    'responsive': true,
                    'lengthMenu': [
                        [-1],
                        ['Show all']
                        ],
                    'buttons': {
                        buttons: [{
                            extend: 'pageLength',
                            className: 'btn btn-default',
                        }]
                    },
                    'paging': false,
                    'lengthChange': true,
                    'pageLength': 10,
                    'searching': true,
                    'ordering': false,
                    'order': [],
                    'info': true,
                    'autoWidth': true,
                    "sPaginationType": "full_numbers",
                    "bJQueryUI": true,
                    "bAutoWidth": false,
                    "processing": true
                });

                $('#loading').hide();


            } else {
                openErrorGritter('Error!', result.message);
            }
        });
}


function openSuccessGritter(title, message) {
    jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-success',
        image: '{{ url("images/image-screen.png") }}',
        sticky: false,
        time: '4000'
    });
}

function openErrorGritter(title, message) {
    jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-danger',
        image: '{{ url("images/image-stop.png") }}',
        sticky: false,
        time: '4000'
    });
}
</script>

@endsection
