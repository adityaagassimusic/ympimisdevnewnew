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

        #tableModalBody>tr:hover {
            background-color: #7dfa8c;
        }

        #tableModalDetailBody>tr:hover {
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
            <div class="col-xs-3">
                <table id="resumeTable" class="table table-bordered table-striped table-hover"
                    style="margin-bottom: 5%; height: 17vh;">
                    <thead style="background-color: rgba(126,86,134,.7);">
                        <tr>
                            <th style="text-align: center; width: 50%; font-size: 0.9vw;">Status</th>
                            <th style="text-align: center; width: 50%; font-size: 0.9vw;">Jumlah Kedatangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 1%; font-weight: bold; font-size: 0.9vw;">All</td>
                            <td id="count_all" style="width: 1%; text-align: right; font-weight: bold; font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr> 
                        <tr>
                            <td
                                style="width: 1%; background-color: #ccffff; font-weight: bold; font-size: 0.9vw;">
                                BC Uploaded</td>
                            <td id="count_document"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: #ccffff;  font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 1%; background-color: rgb(254, 204, 254); font-weight: bold; font-size: 0.9vw;">
                                BC Not Uploaded</td>
                            <td id="count_no_document"
                                style="width: 1%; text-align: right; font-weight: bold; background-color: rgb(254, 204, 254); font-size: 1.2vw; padding-right: 4%;">
                            </td>
                        </tr>

                     
                    </tbody>
                </table>
            </div>

            <div class="col-xs-9">
                <div id="chart1" style="height: 30vh; width: 100%;"></div>
            </div>

            
            <div class="col-xs-12">
                <div class="nav-tabs-custom" style="margin-top: 1%;">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                        <li class="vendor-tab active"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Barang Modal</a>
                        </li>
                        <li class="vendor-tab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Detail Barang Modal</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1" style="overflow-x: auto;">
                            <table id="tableModal" class="table table-bordered" style="width: 100%;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width:4%;">Surat Jalan / Invoice</th>
                                        <th style="width:4%;">Nomor PO</th>
                                        <th style="width:4%;">Vendor</th>
                                        <th style="width:2%;">Tanggal Kedatangan</th>
                                        <th style="width:2%;">Status</th>
                                        <th style="width:2%;">Upload Dokumen</th>
                                    </tr>
                                </thead>
                                <tbody id="tableModalBody" style="vertical-align: middle; text-align: center;">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
                            <table id="tableModalDetail" class="table table-bordered"
                                style="width: 100%; ">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width:2%;">Surat Jalan</th>
                                        <th style="width:6%;">Vendor</th>
                                        <th style="width:8%;">Nama Item</th>
                                        <th style="width:3%;">Nomor PR / Inv</th>
                                        <th style="width:3%;">Nomor PO</th>
                                        <th style="width:2%;">Tanggal Kedatangan</th>
                                        <th style="width:1%;">Qty</th>
                                        <th style="width:1%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="tableModalDetailBody" style="vertical-align: middle; text-align: center;">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDokumen" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="myModalLabel">Upload Dokumen BC <br>Surat Jalan <b><span id="surat"></span></b></h4>
        </div>
        <div class="modal-body">
          <div class="box-body">
            <input type="hidden" value="{{csrf_token()}}" name="_token" />
            <div class="row">
              <div class="col-md-12">
                <input type="file" required="" id="dokumen_bc" name="dokumen_bc[]" accept="application/pdf" multiple="">
              </div>  
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
          <input type="hidden" id="surat_jalan">
          <button type="button" onclick="update_dokumen()" class="btn btn-success"><i class="fa fa-upload"></i> Upload</button>
        </div>
      </div>
    </div>
  </div>

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

            fetchSuratJalan();
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var countAddItem = 0;
        var countAddItems = [];

    
        $('#modalUpload').on('hidden.bs.modal', function() {
            $('#modalCreate').modal('show');
        });

        function clearAll() {
            $('#modalCreate').modal('hide');
            $("#addBuyer").prop('selectedIndex', 0).change();
            $('#addDestination').val("");
            $('#addDestinationName').val("");
            $('#addDestinationShortname').val("");
            $('#addDivision').val("");
            $('#addRemark').val("");
            $('#addCurrency').val("");
            $('#addAttachment').val("");
            countAddItem = 0;
            countAddItems = [];
            $('#tableAddItemBody').html("");
        }

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


        function fetchModal(bulan, status) {
            $('#tableDetailModal').DataTable().clear();
            $('#tableDetailModal').DataTable().destroy();
            $('#tableDetailModalBody').html("");

            var tableDetailModalBody = "";
            $('#tableDetailModalBody').append(tableDetailModalBody);

            $('#tableDetailModal').DataTable({
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
                'ordering': false,
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
                        "targets": 2,
                        "className": "text-left",
                    },
                    {
                        "targets": 3,
                        "className": "text-right",
                    }
                ]
            });

            $('#modalDetail').modal('show');


        }


        function fetchSuratJalan() {
            $("#loading").show();

            var data = {
            }

            $.get('{{ url("fetch/kedatangan/dokumen_bc") }}', data, function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();
                    $('#tableModal').DataTable().clear();
                    $('#tableModal').DataTable().destroy();

                    $('#tableModalBody').html("");

                    var tableModalBody = "";

                    var dokumen = 0;
                    var no_dokumen = 0;

                    $.each(result.surat_jalan, function(key, value) {
                        tableModalBody += '<tr>';
                        tableModalBody += '<td style="width: 10%;text-align: left;padding-left: 0.3%; padding-right: 0.3%;">';
                        tableModalBody += value.surat_jalan;
                        tableModalBody += '</td>';


                        tableModalBody += '<td style="width: 10%;text-align: left;padding-left: 0.3%; padding-right: 0.3%;">';
                        tableModalBody += value.no_po;
                        tableModalBody += '</td>';

                        tableModalBody += '<td style="width: 10%;text-align: left;padding-left: 0.3%; padding-right: 0.3%;">';
                        tableModalBody += value.supplier_code+ " - "+value.supplier_name;
                        tableModalBody += '</td>';

                        tableModalBody += '<td style="width: 5%;padding-left: 0.3%; padding-right: 0.3%;">';
                        tableModalBody += getFormattedDate(new Date(value.date_receive));
                        tableModalBody += '</td>';

                        if (value.dokumen == null) {
                            tableModalBody += '<td style="width: 5%;padding-left: 0.3%; padding-right: 0.3%;background-color: rgb(254, 204, 254);">';
                            tableModalBody += 'Not Yet Upload';
                            tableModalBody += '</td>';
                            no_dokumen++;
                        }else{
                            tableModalBody += '<td style="width: 5%;padding-left: 0.3%; padding-right: 0.3%;background-color: #ccffff;">';
                            tableModalBody += 'Uploaded';
                            tableModalBody += '</td>';
                            dokumen++;
                        }


                        if (value.dokumen == null) {
                            tableModalBody += '<td style="width: 5%;padding-left: 0.3%; padding-right: 0.3%;">';
                            tableModalBody += '<button style="height: 100%;" onclick="penanganan(\''+value.surat_jalan+'\')" class="btn btn-md btn-warning form-control"><i class="fa fa-file-pdf-o"></i> Upload File</button>';
                            tableModalBody += '</td>';
                        }else{
                            tableModalBody += '<td style="width: 5%;padding-left: 0.3%; padding-right: 0.3%;">';
                            for (var i = 0; i < value.dokumen.split(',').length; i++) {
                                
                            tableModalBody += '<a href="{{url('files/dokumen_bc')}}/'+value.dokumen.split(",")[i]+'" target="_blank" class="fa fa-paperclip"></a>';
                            }

                            tableModalBody += '</td>';
                        }


                        tableModalBody += '</tr>';
                    });



                    $('#count_all').text((dokumen + no_dokumen));
                    $('#count_document').text(dokumen);
                    $('#count_no_document').text(no_dokumen);

                    $('#tableModalBody').append(tableModalBody);

                    $('#tableModal').DataTable({
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
                        'ordering': false,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });

                    $('#tableModalDetail').DataTable().clear();
                    $('#tableModalDetail').DataTable().destroy();

                    $('#tableModalDetailBody').html("");
                    var tableModalDetailBody = "";

                    // $.each(result.surat_jalan_all, function(key, value) {

                    //     tableModalDetailBody += '<tr>';

                    //     tableModalDetailBody += '<td style="width: 7.5%;text-align:left;padding-left: 0.3%; padding-right: 0.3%;">';
                    //     tableModalDetailBody += value.surat_jalan;
                    //     tableModalDetailBody += '</td>';

                    //     tableModalDetailBody += '<td style="width: 7.5%;text-align:left;padding-left: 0.3%; padding-right: 0.3%;">';
                    //     tableModalDetailBody += value.supplier_code +' - '+value.supplier_name;
                    //     tableModalDetailBody += '</td>';

                    //     tableModalDetailBody += '<td style="width: 10%;text-align:left; padding-left: 0.5%; padding-right: 0.5%;">';
                    //     tableModalDetailBody += value.nama_item;
                    //     tableModalDetailBody += '</td>';

                    //     tableModalDetailBody += '<td style="width: 7.5%; padding-left: 0.5%; padding-right: 0.5%;">';
                    //     tableModalDetailBody += value.no_pr;
                    //     tableModalDetailBody += '</td>';

                    //     tableModalDetailBody += '<td style="width: 7.5%; padding-left: 0.5%; padding-right: 0.5%;">';
                    //     tableModalDetailBody += value.no_po;
                    //     tableModalDetailBody += '</td>';

                    //     tableModalDetailBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                    //     tableModalDetailBody += getFormattedDate(new Date(value.date_receive));
                    //     tableModalDetailBody += '</td>';

                    //     tableModalDetailBody += '<td style="width: 2.5%;padding-left: 0.3%; padding-right: 0.3%;">';
                    //     tableModalDetailBody += value.qty_receive;
                    //     tableModalDetailBody += '</td>';

                    //     if (value.dokumen == null) {
                    //         tableModalDetailBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;background-color: rgb(254, 204, 254);">';
                    //         tableModalDetailBody += 'Not Yet Upload';
                    //         tableModalDetailBody += '</td>';
                    //     }else{
                    //         tableModalDetailBody += '<td style="width: 7.5%;padding-left: 0.3%; padding-right: 0.3%;background-color: #ccffff;">';
                    //         tableModalDetailBody += 'Uploaded';
                    //         tableModalDetailBody += '</td>';
                    //     }

                    //     tableModalDetailBody += '</tr>';
                    // });

                    $('#tableModalDetailBody').append(tableModalDetailBody);

                    $('#tableModalDetail').DataTable({
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
                        'ordering': false,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });

                    var xCategories = [];
                    var dokumen = [];
                    var no_dokumen = [];


                    for (var i = 0; i < result.calendars.length; i++) {
                        xCategories.push(result.calendars[i].month_text);
                        dokumen.push(0);
                        no_dokumen.push(0);
                    }

                    for (var i = 0; i < result.calendars.length; i++) {
                        for (var j = 0; j < result.surat_jalan.length; j++) {
                            if (result.calendars[i].month == result.surat_jalan[j].date_receive.substr(0, 7)) {
                                var status = '';
                                if (result.surat_jalan[j].dokumen == null) {
                                    dokumen[i] += 1;
                                } else if (result.surat_jalan[j].dokumen != null) {
                                    no_dokumen[i] += 1;
                                } 
                            }
                        }
                    }

                    Highcharts.chart('chart1', {
                        chart: {
                            backgroundColor: null,
                            type: 'column',
                        },
                        title: {
                            text: ''
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            title: {
                                text: 'Kontrol Dokumen BC Per Bulan',
                                style: {
                                    fontWeight: 'bold',
                                },
                            },
                            tickInterval: 1,
                            gridLineWidth: 1,
                            categories: xCategories,
                            crosshair: true
                        },
                        yAxis: [{
                            title: {
                                text: 'Jumlah',
                                style: {
                                    fontWeight: 'bold',
                                },
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                                    fontSize: '0.8vw'
                                }
                            },
                        }],
                        exporting: {
                            enabled: false
                        },
                        legend: {
                            enabled: true,
                            borderWidth: 1
                        },
                        tooltip: {
                            enabled: true
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: 'black'
                            },
                            series: {
                                dataLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return (this.y != 0) ? this.y : "";
                                    },
                                    style: {
                                        textOutline: false
                                    }
                                },
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            fetchModal(this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Dokumen',
                            data: dokumen,
                            color: '#feccfe'
                        }, {
                            name: 'Not Yet Upload',
                            data: no_dokumen,
                            color: '#ccffff'
                        }]
                    });


                } else {
                    alert('Attempt to retrieve data failed');
                }
            });
        }


        function update_dokumen() {
            if ($("#dokumen_bc").val() == "") {
              openErrorGritter("Error","Dokumen BC Harus Diisi");
              return false;
            }

            var formData = new FormData();
            formData.append('surat_jalan', $("#surat_jalan").val());

            var att_count = 0;
            for (var i = 0; i < $('#dokumen_bc').prop('files').length; i++) {
                formData.append('dokumen_bc_'+i, $('#dokumen_bc').prop('files')[i]);
                att_count++;
            }
            formData.append('att_count', att_count);

            // formData.append('dokumen_bc', $('#dokumen_bc').prop('files')[0]);

            $.ajax({
              url:"{{ url('post/kedatangan/dokumen_bc') }}",
              method:"POST",
              data:formData,
              dataType:'JSON',
              contentType: false,
              cache: false,
              processData: false,
              success: function (response) {
                openSuccessGritter("Success","Dokumen BC Berhasil Di Update");
                $('#modalDokumen').modal("hide");
                fetchSuratJalan();
              },
              error: function (response) {
                openErrorGritter("Error",result.datas);
                $('#modalDokumen').modal("hide");
              },
            });
        }


        function penanganan(surat_jalan) {
            $('#modalDokumen').modal("show");
            $("#surat_jalan").val(surat_jalan);
            $("#surat").text(surat_jalan);
            $("#dokumen_bc").val("");
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
