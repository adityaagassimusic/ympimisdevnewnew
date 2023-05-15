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

        .overdue {
            -webkit-animation: overdue 1s infinite;
            -moz-animation: overdue 1s infinite;
            -o-animation: overdue 1s infinite;
            animation: overdue 1s infinite;
        }

        @-webkit-keyframes overdue {

            0%,
            49% {
                background: rgba(0, 0, 0, 0);
                color: #ffe360;

            }

            50%,
            100% {
                background-color: #ffe360;
                color: black;
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
                <div class="col-xs-6 col-xs-offset-3" style="vertical-align: middle;">
                    <div class="col-xs-2" style="padding: 0.5%; vertical-align: middle;">
                        <a href="javascript:void(0)"
                            style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: #90ed7d;"
                            id="btn_ALL" onclick="btnCategory('ALL')" class="btn btn-sm">ALL</a>
                    </div>
                    <div class="col-xs-2" style="padding: 0.5%; vertical-align: middle;">
                        <a href="javascript:void(0)"
                            style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                            id="btn_EI" onclick="btnCategory('EI')" class="btn btn-sm">EDIN</a>
                    </div>
                    <div class="col-xs-2" style="padding: 0.5%; vertical-align: middle;">
                        <a href="javascript:void(0)"
                            style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                            id="btn_KPP" onclick="btnCategory('KPP')" class="btn btn-sm">KPP</a>
                    </div>
                    <div class="col-xs-2" style="padding: 0.5%; vertical-align: middle;">
                        <a href="javascript:void(0)"
                            style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                            id="btn_BPP" onclick="btnCategory('BPP')" class="btn btn-sm">BPP</a>
                    </div>
                    <div class="col-xs-2" style="padding: 0.5%; vertical-align: middle;">
                        <a href="javascript:void(0)"
                            style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                            id="btn_WELDING" onclick="btnCategory('WELDING')" class="btn btn-sm">WELDING</a>
                    </div>
                    <div class="col-xs-2" style="padding: 0.5%; vertical-align: middle;">
                        <a href="javascript:void(0)"
                            style="font-weight: bold; width: 100%; border-color: black; color: black; background-color: white;"
                            id="btn_ASSEMBLY" onclick="btnCategory('ASSEMBLY')" class="btn btn-sm">FINAL ASSY</a>
                    </div>
                </div>
                <div class="col-xs-12" style="margin-top: 10px;">
                    <table class="table table-bordered" id="tableResume">
                        <thead>
                            <tr>
                                <th style="width: 2.5%; text-align: center">#</th>
                                <th style="width: 7.5%; text-align: center">TARGET</th>
                                <th style="width: 10%; text-align: center">LOKASI</th>
                                <th style="width: 10%; text-align: center">EO NO.</th>
                                <th style="width: 30%; text-align: center">MATERIAL</th>
                                <th style="width: 5%; text-align: center">UOM</th>
                                <th style="width: 10%; text-align: center">TARGET</th>
                                <th style="width: 10%; text-align: center">ACTUAL</th>
                                <th style="width: 10%; text-align: center">DIFF</th>
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


            intervalTable();
            setInterval(intervalTable, 60 * 1000);

        });

        var storage_locations = <?php echo json_encode($storage_locations); ?>;
        var category = "{{ $_GET['area'] }}";


        function btnCategory(cat) {
            $('#btn_ALL').css('background-color', 'white');
            $('#btn_EI').css('background-color', 'white');
            $('#btn_KPP').css('background-color', 'white');
            $('#btn_BPP').css('background-color', 'white');
            $('#btn_WELDING').css('background-color', 'white');
            $('#btn_ASSEMBLY').css('background-color', 'white');

            $('#btn_ALL').css('font-size', '12px');
            $('#btn_EI').css('font-size', '12px');
            $('#btn_KPP').css('font-size', '12px');
            $('#btn_BPP').css('font-size', '12px');
            $('#btn_WELDING').css('font-size', '12px');
            $('#btn_ASSEMBLY').css('font-size', '12px');

            $('#btn_' + cat).css('font-size', '16px');
            $('#btn_' + cat).css('background-color', '#90ed7d');

            category = cat;
            showTable(category);

        }

        function intervalTable() {


            if ('{{ isset($_GET['area']) }}' != "") {
                category = '{{ $_GET['area'] }}';
                var lists = ['ALL', 'EI', 'KPP', 'BPP', 'WELDING', 'ASSEMBLY'];

                if (!lists.includes(category)) {
                    category = 'ALL';
                }


            } else {
                if (category == '') {
                    category = 'ALL';
                }
            }

            $('#btn_ALL').css('background-color', 'white');
            $('#btn_EI').css('background-color', 'white');
            $('#btn_KPP').css('background-color', 'white');
            $('#btn_BPP').css('background-color', 'white');
            $('#btn_WELDING').css('background-color', 'white');
            $('#btn_ASSEMBLY').css('background-color', 'white');

            $('#btn_ALL').css('font-size', '12px');
            $('#btn_EI').css('font-size', '12px');
            $('#btn_KPP').css('font-size', '12px');
            $('#btn_BPP').css('font-size', '12px');
            $('#btn_WELDING').css('font-size', '12px');
            $('#btn_ASSEMBLY').css('font-size', '12px');

            $('#btn_' + category).css('font-size', '16px');
            $('#btn_' + category).css('background-color', '#90ed7d');

            showTable(category);
        }

        function showTable(cat) {

            // $('#loading').show();

            $.get('{{ url('fetch/extra_order/shortage_monitoring') }}', function(result, status, xhr) {
                if (result.status) {

                    if (category == '') {
                        category = 'ALL';
                    }

                    $('#tableResume').DataTable().clear();
                    $('#tableResume').DataTable().destroy();
                    $('#tableBodyResume').html("");
                    $('#tableBodyResume').empty();
                    var tableData = '';
                    var count = 0;
                    for (let i = 0; i < result.target.length; i++) {

                        var area = '';
                        for (let j = 0; j < storage_locations.length; j++) {
                            if (storage_locations[j].storage_location == result.target[i].storage_location) {
                                area = storage_locations[j].area;
                            }
                        }

                        if (category != 'ALL') {
                            if (area != cat) {
                                continue;
                            }
                        }

                        tableData += '<tr>';

                        tableData += '<td style="text-align: center;">';
                        tableData += (++count);
                        tableData += '</td>';

                        if (result.target[i].overdue > -5) {
                            if (result.target[i].quantity == result.target[i].production_quantity) {
                                tableData += '<td style="text-align: center;">';
                            } else {
                                tableData += '<td style="text-align: center;" class="overdue">';
                            }
                        } else {
                            tableData += '<td style="text-align: center;">';
                        }
                        tableData += result.target[i].due_date;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += area;
                        tableData += '<br>';
                        tableData += result.target[i].storage_location;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.target[i].eo_number;
                        tableData += '<br>';
                        tableData += result.target[i].destination_shortname;
                        tableData += '</td>';

                        tableData += '<td>';
                        tableData += result.target[i].material_number;
                        tableData += '<br>';
                        tableData += result.target[i].description;
                        tableData += '</td>';

                        tableData += '<td style="text-align: center;">';
                        tableData += result.target[i].uom;
                        tableData += '</td>';


                        tableData += '<td style="text-align: right; font-size: 24pt; font-weight: bold;">';
                        tableData += result.target[i].quantity;
                        tableData += '</td>';


                        if (result.target[i].production_quantity > 0) {
                            tableData +=
                                '<td style="text-align: right; color: #9cffcc; font-size: 24pt; font-weight: bold;">';
                        } else {
                            tableData +=
                                '<td style="text-align: right; color: #ffccff; font-size: 24pt; font-weight: bold;">';
                        }
                        tableData += result.target[i].production_quantity;
                        tableData += '</td>';


                        if (result.target[i].target == 0) {
                            tableData +=
                                '<td style="text-align: right; color: #9cffcc; font-size: 24pt; font-weight: bold;">';
                            tableData += result.target[i].target;
                            tableData += '</td>';
                        } else {
                            tableData +=
                                '<td style="text-align: right; background-color: #ffccff; color: black; font-size: 24pt; font-weight: bold;">';
                            tableData += result.target[i].target;
                            tableData += '</td>';
                        }


                        tableData += '</tr>';

                    }


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

                    // $('#loading').hide();

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
