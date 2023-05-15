@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/dropzone.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .btn-upload {
            margin-top: 2%;
            width: 30%;
            margin-left: 35%;
        }

        /* table {
                                        border: 1px solid black !important;
                                    } */

        thead>tr>th {
            vertical-align: middle !important;
            text-align: center !important;
            /*border: 1px solid black !important;*/
        }

        tbody>tr>td {
            /*border: 1px solid black !important;*/
        }

        tfoot>tr>th {
            /*border: 1px solid black !important;*/
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        .table-po {
            border: 1px solid #f1f1f1 !important;
            background-color: #f4f4f4;
        }

        #loading {
            display: none;
        }

        #alert {
            display: none;
        }

        .attach_file>input {
            display: none;
        }
    </style>
@endsection


@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>

        <input id="attachment" value="{{ $extra_order->attachment }}" hidden>

        <div class="row">
            <div class="col-xs-12">
                <div class="box box-solid" style="border: 1px solid black;">
                    <div class="box-header" style="border-bottom: 1px solid black;">
                        <h3 class="box-title">{{ $title }}
                            <span class="text-purple">{{ $title_jp }}</span>
                        </h3>
                    </div>
                    <div class="box-body" style="display: block;">
                        <div class="col-xs-9" style="padding-left: 0;">
                            <label style="font-size: 1.2vw;">Request List :</label>
                            <table class="table table-bordered table-responsive" width="100%" id="table-main">
                                <thead style="background-color: rgba(126,86,134,.5);">
                                    <tr>
                                        <th style="text-align: center;">GMC Buyer</th>
                                        <th style="text-align: center;">GMC YMPI</th>
                                        <th style="text-align: center;">Description</th>
                                        <th style="text-align: center;">ETD</th>
                                        <th style="text-align: center;">Ship By</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">UoM</th>
                                        <th style="text-align: center;">Price (USD)</th>
                                        <th style="text-align: center;">Amount (USD)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $total = 0; @endphp
                                    @foreach ($detail as $row)
                                        <tr>
                                            <td style="text-align: center;">{{ $row->material_number_buyer }}</td>
                                            <td style="text-align: center;">{{ $row->material_number }}</td>
                                            <td style="text-align: left;">{{ $row->description }}</td>
                                            <td style="text-align: center;">{{ $row->request_date }}</td>
                                            <td style="text-align: center;">{{ $row->shipment_by }}</td>
                                            <td style="text-align: right;">{{ $row->quantity }}</td>
                                            <td style="text-align: center;">{{ $row->uom }}</td>
                                            <td style="text-align: right;">{{ $row->sales_price }}</td>
                                            <td style="text-align: right;">{{ $row->sales_price * $row->quantity }}</td>
                                            @php
                                                $amount = $row->sales_price * $row->quantity;
                                                $total = $total + $amount;
                                            @endphp

                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="background-color: rgba(252, 248, 227, .5);">
                                    <tr>
                                        <th style="text-align: center;" colspan="8">Total Amount</th>
                                        <th style="text-align: right;">{{ $total }}</th>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="col-xs-10 col-xs-offset-1" style="margin-top: 3%;">
                                <label>
                                    <label style="font-size: 1.2vw;">Upload PO <span class="text-purple"
                                            style="font-weight: normal;">POをアップロード</span></label> <br>
                                    <span style="color: red;"><sup>*)</sup></span> Note <span class="text-purple"
                                        style="font-weight: normal;">備考</span> : <br>
                                    <span>
                                        If the PO submission exceeds 10 working days since the PO upload notification email
                                        was sent, the system will automatically reschedule the YMPI ETD according to the
                                        number of days of delay.
                                    </span>
                                    <span class="text-purple" style="font-weight: normal;">
                                        PO通知メールを送信してから10日間以上経過すると、システムが遅れる日数に合わせてYMPIに着荷する日程を自動的に再調整する。
                                    </span>
                                </label>

                                <table class="table-po table-bordered" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>
                                                PO Number
                                            </th>
                                            <th>
                                                Attachment
                                            </th>
                                            <th style="padding: 3px 5px 3px 5px; text-align: center;">
                                                <button class="btn btn-success" onclick='addMorePo();'><i
                                                        class='fa fa-plus'></i></button>
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody id='po-field'>
                                        <tr id="add_po_1">
                                            <td style="padding: 3px 5px 3px 5px;">
                                                <input type="text" class="form-control" id="add_po_no_1"
                                                    placeholder="Input PO Number ... " required>
                                            </td>
                                            <td style="padding: 3px 5px 3px 5px; text-align: center;">
                                                <p class="attach_file" style="margin: 0px;">
                                                    <label for="add_po_file_1">
                                                        <a class="btn btn-default" rel="nofollow"
                                                            style="background-color: #c1c1c1;">
                                                            &nbsp;<span class="glyphicon glyphicon-paperclip"></span>&nbsp;
                                                        </a>
                                                    </label>
                                                    <input type="file" name="add_po_file_1" id="add_po_file_1"
                                                        required>&nbsp;
                                                    <label id="label_attach_po_1" for="add_po_file_1"></label>
                                                </p>
                                            </td>
                                            <td style="padding: 3px 5px 3px 5px; text-align: center;">
                                                <button class="btn btn-danger" onclick="removePo(1)"><i
                                                        class="fa fa-close"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <button class="btn btn-success btn-md btn-upload" onclick="uploadPo()"
                                    style="font-size: 1.25vw; padding-top: 0px; padding-bottom: 0px;">
                                    SUBMIT<br>提出&nbsp;&nbsp;<i class="fa fa-upload"></i>
                                </button>

                            </div>

                        </div>
                        <div class="col-xs-3" style="border: 1px solid black; padding-right: 0;">
                            <h3 class="text-primary" id="eo_number">{{ $extra_order->eo_number }}</h3>
                            <div class="text-muted">

                                @php
                                    $order_by = '';
                                    for ($i = 0; $i < count($user); $i++) {
                                        if (strtoupper($user[$i]->username) == strtoupper($extra_order->order_by)) {
                                            $order_by = ucwords($user[$i]->name);
                                            break;
                                        }
                                    }
                                @endphp

                                <p class="text-sm">Order By<br>
                                    <b>{{ $order_by }}</b>
                                </p>
                                <p class="text-sm">Recipient<br>
                                    <b>{{ $extra_order->attention }}</b>
                                </p>
                                <p class="text-sm">Division<br>
                                    <b>{{ $extra_order->division }}</b>
                                </p>
                                <p class="text-sm">Destination<br>
                                    <b>{{ $extra_order->destination_code }} - {{ $extra_order->destination_name }}
                                        ({{ $extra_order->destination_shortname }})</b>
                                </p>

                                <div class="col-xs-12" style="padding-left: 0px;">
                                    <p class="text-sm" style="margin-bottom: 0px;">BOM Progress
                                    <div class="progress progress-sm active" style="margin-bottom: 0;">
                                        <div class="progress-bar progress-bar-success progress-bar-striped"
                                            role="progressbar"
                                            aria-volumenow="{{ ($progress[0]->new_bom / $progress[0]->total) * 100 }}"
                                            aria-volumemin="0" aria-volumemax="100"
                                            style="width: {{ round(($progress[0]->new_bom / $progress[0]->total) * 100) }}%">
                                        </div>
                                    </div>
                                    <b class="d-block">{{ round(($progress[0]->new_bom / $progress[0]->total) * 100, 2) }}%
                                        Complete</b>
                                    </p>
                                </div>

                                <div class="col-xs-12" style="padding-left: 0px;">
                                    <p class="text-sm" style="margin-bottom: 0px;">Price Progress
                                    <div class="progress progress-sm active" style="margin-bottom: 0;">
                                        <div class="progress-bar progress-bar-success progress-bar-striped"
                                            role="progressbar"
                                            aria-volumenow="{{ ($progress[0]->new_price / $progress[0]->total) * 100 }}"
                                            aria-volumemin="0" aria-volumemax="100"
                                            style="width: {{ round(($progress[0]->new_price / $progress[0]->total) * 100) }}%">
                                        </div>
                                    </div>
                                    <b class="d-block">{{ round(($progress[0]->new_price / $progress[0]->total) * 100, 2) }}%
                                        Complete</b>
                                    </p>
                                </div>


                                @php $approved = 0; @endphp
                                @foreach ($approval as $row)
                                    @if ($row->status == 'Approved')
                                        @php $approved++; @endphp
                                    @endif
                                @endforeach

                                <p class="text-sm">Approval Extra Order Confirmation<br>
                                    @if (count($approval) > 0)
                                        @if (count($approval) == $approved)
                                            <b class="d-block">Fully approved</b>
                                        @elseif($approved > 0)
                                            <b class="d-block">Partially approved</b>
                                        @else
                                            <b class="d-block">Waiting for approval</b>
                                        @endif
                                    @else
                                        <b class="d-block">Not submitted yet</b>
                                    @endif
                                </p>


                                @php
                                    $po_by = '';
                                    for ($i = 0; $i < count($user); $i++) {
                                        if (strtoupper($user[$i]->username) == strtoupper($extra_order->po_by)) {
                                            $po_by = ucwords($user[$i]->name);
                                            break;
                                        }
                                    }
                                @endphp

                                <p class="text-sm">PO By<br>
                                    <b>{{ $po_by }}</b>
                                </p>

                                {{-- <p class="text-sm">PO Number<br>
                                    @if (is_null($extra_order->po_number))
                                        <b class="d-block">-</b>
                                    @else
                                        <b class="d-block">Not submitted yet</b>
                                    @endif
                                </p> --}}

                                <p class="text-sm">Status<br>
                                    <b class="d-block">{{ $extra_order->status }}</b>
                                </p>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </section>

@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
    <script src="{{ url('js/dropzone.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {

            $('body').toggleClass("sidebar-collapse");

            // $('form input').change(function () {
            //     $('.form-text').html(this.files.length + " file(s) selected");
            // });

            $('#add_po_file_1').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $('#label_attach_po_1').text(fileName);
            })

            $('#add_po_no_1').focus();

            po = 1;
            list_po = [1];

            $('#table-main').DataTable({
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
                        "targets": [2],
                        "className": "text-left",
                    },
                    {
                        "targets": [5, 7, 8],
                        "className": "text-right",
                    }
                ]
            });

        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');

        var po = 1;
        var list_po = [1];

        function addMorePo() {
            ++po;
            list_po.push(po);

            var add = '';
            add += '<tr id="add_po_' + po + '">';
            add += '<td style="padding: 3px 5px 3px 5px;">';
            add += '<input type="text" class="form-control" id="add_po_no_' + po +
                '" placeholder="Input PO Number ... " required>';
            add += '</td>';
            add += '<td style="padding: 3px 5px 3px 5px; text-align: center;">';
            add += '<p class="attach_file" style="margin: 0px;">';
            add += '<label for="add_po_file_' + po + '">';
            add += '<a class="btn btn-default" rel="nofollow" style="background-color: #c1c1c1;">';
            add += '&nbsp;<span class="glyphicon glyphicon-paperclip"></span>&nbsp;';
            add += '</a>';
            add += '</label>';
            add += '<input type="file" name="add_po_file_' + po + '" id="add_po_file_' + po + '" required>&nbsp;';
            add += '<label id="label_attach_po_' + po + '" for="add_po_file_' + po + '"></label>';
            add += '</p>';
            add += '</td>';
            add += '<td style="padding: 3px 5px 3px 5px; text-align: center;">';
            add += '<button class="btn btn-danger" onclick="removePo(' + po + ')"><i class="fa fa-close"></i></button>';
            add += '</td>';
            add += '</tr>';

            $('#po-field').append(add);

            var now = po;
            $('#add_po_file_' + now).on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $('#label_attach_po_' + now).text(fileName);
            });



        }

        function removePo(id) {
            $("#add_po_" + id).remove();

            var index = list_po.indexOf(id);
            if (index !== -1) {
                list_po.splice(index, 1);
            }

        }


        function uploadPo() {

            if (confirm('Apakah Anda yakin untuk upload PO ?')) {
                var insert = true;



                for (var i = 0; i < list_po.length; i++) {
                    if ($('#add_po_file_' + list_po[i]).prop('files')[0] == undefined) {
                        insert = false;
                    }

                    if ($('#add_po_no_' + list_po[i]).val() == '') {
                        insert = false;
                    } else {
                        var check_filename = $('#add_po_no_' + list_po[i]).val();
                        var illegal = ["/", ":", '*', '?', '"', '<', '>', '|', '\\'];

                        for (let h = 0; h < check_filename.length; h++) {
                            for (let i = 0; i < illegal.length; i++) {
                                if (check_filename[h] == illegal[i]) {
                                    openErrorGritter("Error",
                                        'PO file name cannot contain any of the following characters \/:*?"<>|<br>POの名前に「/、\、:、*、?、"、<、>、|」という記号の記入は不可能'
                                    );
                                    return false;
                                }
                            }
                        }
                    }
                }

                if (insert) {
                    var len = list_po.length;

                    var formData = new FormData();
                    formData.append('eo_number', $('#eo_number').text());
                    formData.append('count_files', list_po.length);

                    for (var i = 0; i < list_po.length; i++) {
                        formData.append('file_data_' + i, $('#add_po_file_' + list_po[i]).prop('files')[0]);
                        var file = $('#add_po_file_' + list_po[i]).val().replace(/C:\\fakepath\\/i, '').split(".");

                        formData.append('extension_' + i, file[1]);
                        formData.append('file_name_' + i, file[0]);
                        formData.append('po_number_' + i, $('#add_po_no_' + list_po[i]).val());
                    }

                    $("#loading").show();

                    $.ajax({
                        url: "{{ url('input/extra_order_po') }}",
                        method: "POST",
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(result, status, xhr, response) {
                            if (result.status) {
                                window.open('{{ url('index/extra_order/po_notification') }}' + '/' +
                                    result
                                    .eo_number, '_self');
                            } else {
                                $("#loading").hide();
                                openErrorGritter("Error!", result.message);
                            }

                        },
                        error: function(result, status, xhr, response) {
                            $("#loading").hide();
                            openErrorGritter("Error!");
                            console.log(response);
                        },
                    })

                } else {
                    openErrorGritter("Error",
                        "Please complete all PO number fields and attach PO file<br>全てのPO番号を記入し、資料を添付してください");
                }
            }
        }

        function refreshAll() {
            location.reload(true);
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '5000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '5000'
            });
        }

        function truncate(str, n) {
            return (str.length > n) ? str.substr(0, n - 1) + '&hellip;' : str;
        };

        function replaceNull(s) {
            return s == null ? "-" : s;
        }
    </script>
@endsection
