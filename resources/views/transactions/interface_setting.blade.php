@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.tagsinput.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <style type="text/css">
        .table>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        table.table-bordered {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            vertical-align: middle;
            padding: 2px 5px 2px 5px;
        }

        #loading {
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
    <section class="content" style="font-size: 0.9vw;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Success!</h4>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                {{ session('error') }}
            </div>
        @endif
        <div class="row">
            <div class="col-xs-6" style="padding-right: 7.5px;">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-header">
                        <center>
                            <h4 style="font-weight: bold; color: green;">Transaction</h4>
                        </center>
                    </div>
                    <div class="box-body">
                        <form role="form" class="form-horizontal" method="post"
                            action="{{ url('update/ymes/setting') }}">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <input type="hidden" name="saveRemark" value="transaction">
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-sm-4 control-label">Interface<span
                                        class="text-red"></span> :</label>
                                <div class="col-sm-4">
                                    <select class="form-control select2" name="saveInterface"
                                        data-placeholder="Select Material" style="width: 100%;">
                                        @if ($transaction->interface == 1)
                                            <option value="1" selected>ON</option>
                                            <option value="0">OFF</option>
                                        @else
                                            <option value="1">ON</option>
                                            <option value="0" selected>OFF</option>
                                        @endif
                                    </select>
                                </div>
                                @if ($transaction->interface == 1)
                                    <span class="fa fa-check" style="color: green; font-size: 2vw;"></span>
                                @else
                                    <span class="fa fa-ban" style="color: red; font-size: 2vw;"></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-sm-4 control-label">Freq<span
                                        class="text-red"></span> :</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2" name="saveInterfaceFrequency"
                                        data-placeholder="Select Material" style="width: 100%;">
                                        @foreach ($interface_frequencies as $interface_frequency)
                                            @if ($transaction->interface_frequency == $interface_frequency)
                                                <option value="{{ $interface_frequency }}" selected>
                                                    {{ $interface_frequency }}</option>
                                            @else
                                                <option value="{{ $interface_frequency }}">{{ $interface_frequency }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                    Storage Location<span class="text-red"></span> :</label>
                                <div class="col-sm-8">
                                    <textarea id="storageLocationArea" class="form-control" rows="4" placecholder="Paste location from excel here"></textarea>
                                    <input id="storageLocationTags" type="text" placeholder="Material Number"
                                        class="form-control tags" name="storageLocationTags" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                    Material Number<span class="text-red"></span> :</label>
                                <div class="col-sm-8">
                                    <textarea id="materialNumberArea" class="form-control" rows="4" placecholder="Paste location from excel here"></textarea>
                                    <input id="materialNumberTags" type="text" placeholder="Material Number"
                                        class="form-control tags" name="materialNumberTags" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-sm-4 control-label">
                                    Max Posting Date<span class="text-red"></span> :</label>
                                <div class="col-sm-8">
                                    <div class="col-sm-5 no-padding">
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control datepicker" id="maxPostingDate"
                                                name="maxPostingDate" placeholder="Select Date"
                                                style="text-align:center;">
                                        </div>
                                    </div>
                                    <div class="col-sm-4" style="padding-right: 0px;">
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                            <input type="text" class="form-control timepicker" id="maxPostingTime"
                                                name="maxPostingTime" placeholder="Time" style="text-align:center;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger pull-right" style="width: 30%;">Save</button>
                            <a style="margin-right: 5px;" class="btn btn-warning pull-right"
                                href="{{ url('index/ymes/setting') }}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-5" style="padding-right: 7.5px;">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-header">
                        <center>
                            <h4 style="font-weight: bold; color: red;">Error</h4>
                        </center>
                    </div>
                    <div class="box-body">
                        <form role="form" class="form-horizontal" method="post"
                            action="{{ url('update/ymes/setting') }}">
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <input type="hidden" name="saveRemark" value="error">
                            <div class="form-group">
                                <label style="padding-top: 0;" for=""
                                    class="col-sm-4 control-label">Interface<span class="text-red"></span> :</label>
                                <div class="col-sm-4">
                                    <select class="form-control select2" name="saveInterface"
                                        data-placeholder="Select Material" style="width: 100%;">
                                        @if ($error->interface == 1)
                                            <option value="1" selected>ON</option>
                                            <option value="0">OFF</option>
                                        @else
                                            <option value="1">ON</option>
                                            <option value="0" selected>OFF</option>
                                        @endif
                                    </select>
                                </div>
                                @if ($error->interface == 1)
                                    <span class="fa fa-check" style="color: green; font-size: 2vw;"></span>
                                @else
                                    <span class="fa fa-ban" style="color: red; font-size: 2vw;"></span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label style="padding-top: 0;" for="" class="col-sm-4 control-label">Freq<span
                                        class="text-red"></span> :</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2" name="saveInterfaceFrequency"
                                        data-placeholder="Select Material" style="width: 100%;">
                                        @foreach ($error_interface_frequencies as $error_interface_frequency)
                                            @if ($error->interface_frequency == $error_interface_frequency)
                                                <option value="{{ $error_interface_frequency }}" selected>
                                                    {{ $error_interface_frequency }}</option>
                                            @else
                                                <option value="{{ $error_interface_frequency }}">
                                                    {{ $error_interface_frequency }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger pull-right" style="width: 30%;">Save</button>
                            <a style="margin-right: 5px;" class="btn btn-warning pull-right"
                                href="{{ url('index/ymes/setting') }}">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-body">
                        <table id="tableLog" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #dd4b39; color: white;">
                                <tr>
                                    <th style="width: 5%; text-align: center;">#</th>
                                    <th style="width: 15%; text-align: center;">Interface</th>
                                    <th style="width: 5%; text-align: center;">Status</th>
                                    <th style="width: 45%; text-align: center;">Setting OFF</th>
                                    <th style="width: 10%; text-align: center;">Frequency</th>
                                    <th style="width: 10%; text-align: center;">Updated By</th>
                                    <th style="width: 10%; text-align: center;">Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($setting_logs as $setting_log)
                                    <tr>
                                        <td style="width: 5%; text-align: center;">{{ $i++ }}</td>
                                        <td style="width: 15%; text-align: center;">{{ strtoupper($setting_log->remark) }}
                                        </td>
                                        @if ($setting_log->interface == 1)
                                            <td
                                                style="width: 5%; text-align: center; background-color: rgb(204, 255, 255);">
                                                ON</td>
                                        @else
                                            <td
                                                style="width: 5%; text-align: center; background-color: rgb(255, 204, 255);">
                                                OFF</td>
                                        @endif
                                        @if (strlen($setting_log->excludes) > 0)
                                            @php
                                                $set = json_decode($setting_log->excludes);
                                                $material_number = $set->material_number;
                                                $storage_location = $set->storage_location;
                                            @endphp

                                            <td style="width: 45%; text-align: left; word-wrap: break-word;">
                                                <b>GMC</b> : {{ str_replace(',', ', ', $material_number) }}
                                                <br>
                                                <b>SLOC</b> : {{ str_replace(',', ', ', $storage_location) }}
                                                <br>
                                                <b>Max Posting Date : </b> {{ $setting_log->max_result_date }}
                                            </td>
                                        @else
                                            <td style="width: 40%; text-align: center;">-</td>
                                        @endif
                                        <td style="width: 10%; text-align: center;">
                                            {{ $setting_log->interface_frequency }}
                                        </td>
                                        <td style="width: 10%; text-align: center;">
                                            {{ $setting_log->updated_by }}<br>
                                            @php
                                                if (isset($employees[$setting_log->updated_by])) {
                                                    $exp = explode(' ', $employees[$setting_log->updated_by]->name);
                                                    if (count($exp) >= 2) {
                                                        echo $exp[0] . ' ' . $exp[1];
                                                    } else {
                                                        echo $employees[$setting_log->updated_by]->name;
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                            @endphp
                                        </td>
                                        <td style="width: 10%; text-align: center;">{{ $setting_log->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {

            $('.datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: "yyyy-mm-dd"
            });

            $('.timepicker').timepicker({
                showInputs: false,
                showMeridian: false,
                minuteStep: 1
            });

            checkTags();
            jQuery('.tags').tagsInput({
                width: 'auto'
            });
            $('#storageLocationTags').hide();
            $('#storageLocationTags_tagsinput').hide();
            $('#materialNumberTags').hide();
            $('#materialNumberTags_tagsinput').hide();
            initKeyDown();


            $('.select2').select2({
                minimumResultsForSearch: -1
            });
            var table = $('#tableLog').DataTable({
                "order": [],
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
                        },
                        {
                            extend: 'copy',
                            className: 'btn btn-success',
                            text: '<i class="fa fa-copy"></i> Copy',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        },
                        {
                            extend: 'excel',
                            className: 'btn btn-info',
                            text: '<i class="fa fa-file-excel-o"></i> Excel',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        },
                        {
                            extend: 'print',
                            className: 'btn btn-warning',
                            text: '<i class="fa fa-print"></i> Print',
                            exportOptions: {
                                columns: ':not(.notexport)'
                            }
                        },
                    ]
                }
            });
        });

        function checkTags() {

            $.get('{{ url('fetch/ymes/setting_exclude') }}', function(result, status, xhr) {
                if (result.status) {

                    // Material Number
                    for (let i = 0; i < result.exclude.length; i++) {
                        if (result.exclude[i].type == 'material_number') {
                            $('#materialNumberTags').addTag(result.exclude[i].exculde_point);
                        }
                    }
                    $('#materialNumberTags').hide();
                    $('#materialNumberTags_tagsinput').show();
                    $('#materialNumberArea').hide();

                    // Storage Location
                    for (let i = 0; i < result.exclude.length; i++) {
                        if (result.exclude[i].type == 'storage_location') {
                            $('#storageLocationTags').addTag(result.exclude[i].exculde_point);
                        }
                    }
                    $('#storageLocationTags').hide();
                    $('#storageLocationTags_tagsinput').show();
                    $('#storageLocationArea').hide();

                    // Result Date
                    var is_found = false;
                    for (let i = 0; i < result.exclude.length; i++) {
                        if (result.exclude[i].type == 'result_date') {

                            var datetime = result.exclude[i].exculde_point;

                            var date = datetime.split(' ')[0];
                            var time = datetime.split(' ')[1];
                            var hour = time.split(':')[0];
                            var minute = time.split(':')[1];

                            $('#maxPostingDate').val(date);
                            $('#maxPostingTime').val(hour + ':' + minute);

                            is_found = true;

                        }
                    }
                    if (!is_found) {
                        $('#maxPostingDate').val('');
                        $('#maxPostingTime').val('');
                    }

                }

            });

        }

        function initKeyDown() {
            $('#storageLocationArea').keydown(function(event) {
                if (event.keyCode == 13) {
                    convertReceiveStoreToTags();
                    return false;
                }
            });
            $('#materialNumberArea').keydown(function(event) {
                if (event.keyCode == 13) {
                    convertReceiveStoreToTags();
                    return false;
                }
            });
        }

        function convertReceiveStoreToTags() {
            var data = $('#storageLocationArea').val();
            if (data.length > 0) {
                var rows = data.split('\n');
                if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                        var barcode = rows[i].trim();
                        if (barcode.length > 0) {
                            $('#storageLocationTags').addTag(barcode);
                        }
                    }
                    $('#storageLocationTags').hide();
                    $('#storageLocationTags_tagsinput').show();
                    $('#storageLocationArea').hide();
                }
            }

            var data = $('#materialNumberArea').val();
            if (data.length > 0) {
                var rows = data.split('\t');
                if (rows.length > 0) {
                    for (var i = 0; i < rows.length; i++) {
                        var barcode = rows[i].trim();
                        if (barcode.length > 0) {
                            $('#materialNumberTags').addTag(barcode);
                        }
                    }
                    $('#materialNumberTags').hide();
                    $('#materialNumberTags_tagsinput').show();
                    $('#materialNumberArea').hide();
                }
            }
        }
    </script>
@endsection
