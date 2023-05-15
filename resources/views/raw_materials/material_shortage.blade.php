@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        .table {
            width: 100%;
            max-width: 100%;
        }

        table {
            background-color: transparent;
        }

        .icon {
            padding-top: 8.5%;
            font-weight: bold;
            color: #d88e3b !important;
        }

        .content-tittle {
            color: #4d4a86 !important;
        }

        .sup {
            font-size: 5vh;
        }

        .small-box:hover {
            color: #4d4a86;
            cursor: pointer;
        }

        .table-detail {
            font-size: 2.75vh;
        }

        .text-ann {
            color: white;
            font-weight: bold;
        }

        @-webkit-keyframes sparkling {

            0%,
            49% {
                border-color: #3c3c3c;
            }

            50%,
            100% {
                border-color: #e6973f;
            }
        }

        .sparkling {
            -webkit-animation: sparkling 1s infinite;
            -moz-animation: sparkling 1s infinite;
            -o-animation: sparkling 1s infinite;
            animation: sparkling 1s infinite;
        }
    </style>
@stop
@section('header')
@endsection
<style type="text/css">
</style>
@section('content')
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div id="period_title" class="col-xs-10" style="background-color: rgba(248,161,63,0.9);">
                    <center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center>
                </div>
                <div class="col-xs-2">
                    <div class="input-group date">
                        <div class="input-group-addon" style="background-color: rgba(248,161,63,0.9);">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="datepicker" name="datepicker"
                            onchange="getData()" placeholder="Select Date">
                    </div>
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 1%;">
            <div class="col-xs-2" id="buyer-container">
            </div>
            <div class="col-xs-10">
                <div id="container" class="row">
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetail" data-keyboard="false">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3>DETAIL MATERIAL</h3>
                    </center>
                </div>
                <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                    <div style="padding: 10px 10px 10px 10px;">
                        <table id="tableDetail"
                            style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 12px;">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop
@section('scripts')
    <script src="{{ url('js/jquery.marquee.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script>
        var audio_error = new Audio('{{ url('sounds/alarm_error.mp3') }}');

        var error_machine = [];
        var trouble_machine_list = [];
        var proses = 0;
        var trouble = 0;
        var idle1 = 0;
        var idle2 = 0;
        var setup = 0;
        var off = 0;

        jQuery(document).ready(function() {
            $('.select2').select2();

            $('#datepicker').datepicker({
                <?php $tgl_max = date('Y-m-d'); ?>
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true,
                endDate: '<?php echo $tgl_max; ?>'
            });

            selected_buyer = 'ALL';
            getData();
            setInterval(getData, 10 * 60 * 1000);

        });

        var selected_buyer = '';
        var materials = [];
        var buyers = [];

        function changeBuyer(id) {
            $('#box-icon-ALL').css('color', 'white');
            $('#box-icon-ALL').css('font-size', '45px');
            $('#box-icon-ALL').css('padding-top', '8.5%');

            for (let j = 0; j < buyers.length; j++) {
                $('#box-icon-' + buyers[j].employee_id).css('color', 'white');
                $('#box-icon-' + buyers[j].employee_id).css('font-size', '45px');
                $('#box-icon-' + buyers[j].employee_id).css('padding-top', '8.5%');
            }

            $('#box-icon-' + id).css('color', '#90ed7d');
            $('#box-icon-' + id).css('font-size', '60px');
            $('#box-icon-' + id).css('padding-top', '6.5%');


            selected_buyer = id;
            showData();
        }


        function showDetail(material_number) {

            $('#tableDetail').html('');

            var style = 'border: 1px solid black; padding: 5px; text-align: center;';

            var detail = '';
            detail += '<thead style="background-color: #2a2628; height: 40px; color: orange;">';
            detail += '<tr>';
            detail += '<th style="' + style + ' width: 15%;">Material</th>';
            detail += '<th style="' + style + ' width: 15%;">Vendor</th>';
            detail += '<th style="' + style + ' width: 7.5%;">MOQ</th>';
            detail += '<th style="' + style + ' width: 7.5%;">Days Policy</th>';
            detail += '<th style="' + style + ' width: 7.5%;">Qty Policy<br><span class="text-ann">A</span></th>';
            detail += '<th style="' + style + ' width: 7.5%;">Stock WH<br><span class="text-ann">C</span></th>';
            detail += '<th style="' + style + ' width: 7.5%;">Stock WIP<br><span class="text-ann">B</span></th>';
            detail += '<th style="' + style + ' width: 7.5%;">';
            detail += 'Qty Avg Usage Per Day<br><span class="text-ann">D</span>';
            detail += '</th>';
            detail += '<th style="' + style + ' width: 7.5%;">';
            detail += 'Days Availability<br><span class="text-ann">(B+C)/D</span>';
            detail += '</th>';
            detail += '<th style="' + style + ' width: 7.5%;">Percentage<br><span class="text-ann">(B+C)/A</span></th>';
            detail += '<th style="' + style + ' width: 10%;">Delivery Plan</th>';
            detail += '</tr>';
            detail += '</thead>';

            for (let i = 0; i < materials.length; i++) {
                if (materials[i].material_number == material_number) {
                    detail += '<tbody>';
                    detail += '<tr>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    detail += materials[i].material_number + '<br>' + materials[i].material_description;
                    detail += '</td>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    detail += materials[i].vendor_code + '<br>' + materials[i].vendor_name;
                    detail += '</td>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    detail += (materials[i].minumum_order || '-');
                    detail += '</td>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    detail += materials[i].policy_day;
                    detail += '</td>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    detail += materials[i].policy;
                    detail += '</td>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    detail += materials[i].stock_wh;
                    detail += '</td>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    detail += materials[i].stock_wip;
                    detail += '</td>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    detail += materials[i].avg_usage;
                    detail += '</td>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    detail += materials[i].availability_days;
                    detail += '</td>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    detail += materials[i].stock_condition;
                    detail += '</td>';

                    detail += '<td style="border: 1px solid black; padding: 5px;">';
                    if (materials[i].plan_deliveries.length > 0) {
                        for (let j = 0; j < materials[i].plan_deliveries.length; j++) {
                            detail += materials[i].plan_deliveries[j].due_date + ' = ';
                            detail += materials[i].plan_deliveries[j].quantity;
                            detail += '<br>';
                        }

                    } else {
                        detail += '-';
                    }
                    detail += '</td>';

                    detail += '</tr>';
                    detail += '</tbody>';
                }
            }


            $('#tableDetail').html(detail);
            $('#modalDetail').modal('show');


        }

        function showData() {

            $('#container').html('');

            for (let j = 0; j < buyers.length; j++) {
                buyers[j].quantity = 0;
            }

            if (materials.length > 0) {

                var body = '';
                for (let i = 0; i < materials.length; i++) {
                    for (let j = 0; j < buyers.length; j++) {
                        if (materials[i].buyer_id == buyers[j].employee_id) {
                            buyers[j].quantity += 1;
                            break;
                        }
                    }

                    if (selected_buyer != 'ALL') {
                        if (materials[i].buyer_id != selected_buyer) {
                            continue;
                        }
                    }

                    body += '<div class="col-xs-4">';
                    body +=
                        '<div class="small-box sparkling" style="background-color: lightgray; border: 2px solid;" ';
                    body += 'onclick="showDetail(id)" id="' + materials[i].material_number + '">';
                    body += '<div class="inner">';
                    body += '<h3 class="content-tittle">' + materials[i].nickname + '</h3>';
                    body += '<table style="margin-bottom: 2.5%;" class="table-detail">';

                    body += '<tr>';
                    body += '<td style="width: 20%">Stock</td>';
                    body += '<td style="width: 80%">: ' + materials[i].stock + ' ';
                    body += materials[i].bun + '</td>';
                    body += '</tr>';

                    body += '<tr>';
                    body += '<td style="width: 20%">Policy</td>';
                    body += '<td style="width: 80%">: ' + materials[i].policy + ' ';
                    body += materials[i].bun + '</td>';
                    body += '</tr>';

                    body += '<tr>';
                    body += '<td style="width: 20%">Availability</td>';
                    body += '<td style="width: 80%">: ';
                    body += materials[i].availability_days + ' Day(s)';
                    body += '</td>';
                    body += '</tr>';

                    body += '<tr>';
                    body += '<td style="width: 20%">Buyer</td>';
                    body += '<td style="width: 80%">: ' + materials[i].buyer + '</td>';
                    body += '</tr>';

                    body += '</table>';
                    body += '</div>';
                    body += '<div class="icon">';
                    body += '<span>' + materials[i].stock_condition + '<sup class="sup">%</sup></span>';
                    body += '</div>';
                    body += '</div>';
                    body += '</div>';
                }

                for (let i = 0; i < buyers.length; i++) {
                    $('#total_' + buyers[i].employee_id).text(buyers[i].quantity);
                }

                $('#container').append(body);

            } else {

                var body = '';
                body += '<div class="col-xs-12" style="padding-left: 0px">';
                body += '<table style="width: 100%">';
                body += '<tr>';
                body +=
                    '<td width="100%" style="border-top:0; color: #37ff00; padding-right: 5px; font-size: 5vw; padding-top: 10vh">';
                body +=
                    '<center><i class="fa fa-check-circle" style="font-size: 9vw"></i><br>ALL RAW MATERIALS<br>CONDITION ARE SAFE<br>素材管理状況が安全です</center>';
                body += '</td>';
                body += '</tr>';
                body += '</table>';

                body += '</div>';

                $('#container').append(body);
            }

        }

        function getData() {

            var date = $('#datepicker').val();
            var data = {
                date: date
            }

            $.get('{{ url('fetch/material/shortage_material_availability') }}', data, function(result, status, xhr) {

                $('#title_text').text('Shortage of Materials Availability (' + result.now + ')');
                var h = $('#period_title').height();
                $('#datepicker').css('height', h);
                $('#buyer-container').html('');

                materials = result.materials;
                buyers = [];
                var buyer_txt = '';
                buyer_txt += '<table style="width: 100%;">';
                buyer_txt += '<tr id="ALL" onclick="changeBuyer(id)">';
                buyer_txt += '<th style="cursor: pointer;">';
                buyer_txt += '<div class="info-box" style="background-color: #605ca8; color: white;">';
                buyer_txt += '<span class="info-box-icon" style="padding-top: 10%; color: #90ed7d; ';
                buyer_txt += 'font-size: 60px; padding-top: 6.5%;" ';
                buyer_txt += 'id="box-icon-ALL">';
                buyer_txt += '<i class="fa fa-users"></i>';
                buyer_txt += '</span>';
                buyer_txt += '<div class="info-box-content">';
                buyer_txt += '<span class="info-box-text" style="font-size: 18px; font-weight: bold;">';
                buyer_txt += 'ALL';
                buyer_txt += '</span>';
                buyer_txt += '<span class="info-box-number" style="font-size: 38px;" id="total_all"></span>';
                buyer_txt += '</div>';
                buyer_txt += '</div>';
                buyer_txt += '</th>';
                buyer_txt += '</tr>';

                $.each(result.buyers, function(key, value) {
                    buyers.push({
                        'employee_id': value.employee_id,
                        'name': value.name,
                        'quantity': 0,
                    });
                    buyer_txt += '<tr id="' + value.employee_id + '" onclick="changeBuyer(id)">';
                    buyer_txt += '<th style="cursor: pointer;">';
                    buyer_txt += '<div class="info-box" style="background-color: #605ca8; color: white;">';
                    buyer_txt += '<span class="info-box-icon" style="padding-top: 10%;" ';
                    buyer_txt += 'id="box-icon-' + value.employee_id + '">';
                    buyer_txt += '<i class="fa fa-user"></i>';
                    buyer_txt += '</span>';
                    buyer_txt += '<div class="info-box-content">';
                    buyer_txt += '<span class="info-box-text" style="font-size: 18px; font-weight: bold;">';
                    buyer_txt += value.name;
                    buyer_txt += '</span>';
                    buyer_txt += '<span class="info-box-number" style="font-size: 38px;" ';
                    buyer_txt += 'id="total_' + value.employee_id + '"></span>';
                    buyer_txt += '</div>';
                    buyer_txt += '</div>';
                    buyer_txt += '</th>';
                    buyer_txt += '</tr>';
                });
                buyer_txt += '</table>';
                $('#buyer-container').append(buyer_txt);
                $('#total_all').text(materials.length)

                showData();

            })
        }


        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '2000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '2000'
            });
        }
    </script>
@endsection
