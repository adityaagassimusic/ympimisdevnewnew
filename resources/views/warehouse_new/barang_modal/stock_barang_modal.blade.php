@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}" media="print">
    <style type="text/css">
        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
            text-align: center;
            padding-top: 5px;
            padding-bottom: 5px;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 3px;
            padding-bottom: 3px;
            padding-left: 2px;
            padding-right: 2px;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
            vertical-align: middle;
        }

        #tablePenerimaanBody>tr:hover {
            background-color: #7dfa8c;
        }

        #tablePenerimaanDetailBody>tr:hover {
            background-color: #7dfa8c;
        }

        #loading,
        #error {
            display: none;
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
@endsection

@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: white; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row" style="margin-top: 1%;">
            <div class="col-xs-12">
            <table id="tablePenerimaan" class="table table-bordered" style="width: 100%;margin-top: 10px;">
                <thead style="background-color: rgba(126,86,134,.7);">
                    <tr>
                        <th style="width:8%;">Nama Item</th>
                        <th style="width:6%;">Qty</th>
                        <th style="width:6%;">Location</th>
                    </tr>
                </thead>
                <tbody id="tablePenerimaanBody" style="vertical-align: middle;">
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
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
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/data.js') }}"></script>
    <script src="{{ url('js/exporting.js') }}"></script>
    <script src="{{ url('js/export-data.js') }}"></script>
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $(function() {
                $('.select2').select2({
                    dropdownParent: $('#modalCreate')
                });
            });

            fetchData();
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');


        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: "{{ url('images/image-screen.png') }}",
                sticky: false,
                time: '5000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: "{{ url('images/image-stop.png') }}",
                sticky: false,
                time: '5000'
            });
        }


        function fetchData() {
            $("#loading").show();
            $.get('{{ url("fetch/barang_modal/stock") }}', function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();
                    $('#tablePenerimaan').DataTable().clear();
                    $('#tablePenerimaan').DataTable().destroy();
                    $('#tablePenerimaanBody').html("");

                    var tablePenerimaanBody = "";

                    $.each(result.barang_modal, function(key, value) {

                        tablePenerimaanBody += '<tr>';
                        tablePenerimaanBody += '<td style="width: 15%;text-align; left;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += value.nama_item;
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += value.jumlah;
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tablePenerimaanBody += value.location;
                        tablePenerimaanBody += '</td>';

                        tablePenerimaanBody += '</tr>';

                    });

                    $('#tablePenerimaanBody').append(tablePenerimaanBody);

                    $('#tablePenerimaan').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [10, 25, 50, -1],
                            ['10 rows', '25 rows', '50 rows', 'Show all']
                        ],
                        'buttons': {
                            buttons: [{
                                extend: 'pageLength',
                                className: 'btn btn-default',
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        'ordering': true,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        'columnDefs': [{
                                "targets": 0,
                                "className": "text-left",
                            }
                        ]
                    });


                } else {
                    alert('Attempt to retrieve data failed');
                }
            });
        }

        function getFormattedDate(date) {
              var year = date.getFullYear();

              var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];

              var month = date.getMonth();

              var day = date.getDate().toString();
              day = day.length > 1 ? day : '0' + day;
              
              return day + '-' + monthNames[month] + '-' + year;
        }

        function getFormattedTime(date) {
              var year = date.getFullYear();

              var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                  "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
                ];

              var month = date.getMonth();

              var day = date.getDate().toString();
              day = day.length > 1 ? day : '0' + day;

              var hour = date.getHours();
              if (hour < 10) {
                    hour = "0" + hour;
                }

              var minute = date.getMinutes();
              if (minute < 10) {
                    minute = "0" + minute;
                }
              var second = date.getSeconds();
              
              return day + '-' + monthNames[month] + '-' + year +' '+ hour +':'+ minute +':'+ second;
        }
    </script>
@endsection
