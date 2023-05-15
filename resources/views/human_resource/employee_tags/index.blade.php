@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">


    <style type="text/css">
        .content {
            background-color: #F3f3f3;
        }

        .content-header {
            background-color: #F3f3f3 !important;
            color: black;
        }

        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            /*text-align:center;*/
            overflow: hidden;
        }

        tbody>tr>td {
            /*text-align:center;*/
            padding-left: 5px !important;
        }

        tfoot>tr>th {
            /*text-align:center;*/
        }

        th:hover {
            overflow: visible;
        }

        td:hover {
            overflow: visible;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            vertical-align: middle;
            padding: 0;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            padding: 0;
        }

        td {
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #loading,
        #error {
            display: none;
        }

        .containers {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 15px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            padding-top: 6px;
        }

        /* Hide the browser's default checkbox */
        .containers input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .tableTags {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }

        .tableTags td {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        .tableTags th {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        .tableTags tr:nth-child(even) {
            background-color: #F3f3f3;
        }

        .tableTags tr:nth-child(odd) {
            background-color: #FFFFFF;
        }

        .tableTags tr:hover {
            background-color: #F3f3f3;
        }

        #tableUnregistered {
            background-color: #F3f3f3;
            padding: 10px 2px;
        }

        .dataTables_filter {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }} <small><span class="text-purple">{{ $title_jp }}</span></small>            
            <button class="btn btn-primary btn-sm pull-right" style="margin-right: 5px" onclick="refresh()">
                <i class="fa fa-refresh"></i>&nbsp;&nbsp;Refresh
            </button>
        </h1>
    </section>
@stop
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
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
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>

        <div class="row">
            <div class="col-xs-4"></div>
            <div class="col-xs-4">
                <div class="input-group">
                    <span class="input-group-addon" id="label-tags1" style="width: 100px; font-size:30px;"><i class="fa fa-credit-card"></i></span>
                    <input id="tags_scan" type="text" class="form-control" placeholder="SCAN RFID HERE" aria-describedby="label-tags1" style="height: 70px; font-size:28px;">
                </div>
            </div>
            <div class="col-xs-4"></div>
        </div>
        <br>
        <div class="row">
            <div class="col-xs-6">
                <div class="box box-solid" style="background-color: #3f51b5; color:white;padding: 5px;text-align: center;margin-bottom: 8px; border-radius: 2px;">
                    <span style="font-weight: bold;font-size: 30px">UNREGISTERED</span>
                </div>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><i class="fa fa-search"></i></span>
                    <input id="searchEmp" type="search" class="form-control" placeholder="Search Here" aria-describedby="basic-addon1">
                </div>
                <table id="tableUnregistered" class="tableTags">
                    <thead>
                        <tr>
                            <th style="width: 1%">No</th>
                            <th style="width: 1%">Dept</th>
                            <th style="width: 5%">Sect</th>
                            <th style="width: 1%">Emp ID</th>
                            <th style="width: 5%">Name</th>
                            <th style="width: 10%">RFID</th>
                            <th style="width: 5%">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableUnregisteredBody">
                    </tbody>                    
                </table>
            </div>
            <div class="col-xs-6 dropShadow">
                <div class="row">
                    <div class="box box-solid" style="background-color: #32a852; color: white;padding: 5px;text-align: center;margin-bottom: 8px; border-radius: 2px;">
                        <span style="font-weight: bold;font-size: 30px">CHECK REGISTERED</span>
                    </div>
                    <table id="tableRegistered" class="tableTags">
                        <thead>
                            <tr>
                                <th style="width: 1%">No</th>
                                <th style="width: 1%">Dept</th>
                                <th style="width: 5%">Sect</th>
                                <th style="width: 1%">Emp ID</th>
                                <th style="width: 5%">Name</th>
                                <th style="width: 10%">Tag</th>
                                <th style="width: 5%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableRegisteredBody">
                        </tbody>
                    </table>
                </div>
                <i class="fa fa-arrow-circle-down" style="font-size: 50px; margin-left: 45%; margin-bottom:3%; margin-top:3%; color: #32a852"></i>
                <div class="row" >
                    <div class="box box-solid" style="background-color: #3C8DBC; color: white;padding: 5px;text-align: center;margin-bottom: 8px; border-radius: 2px;">
                        <span style="font-weight: bold;font-size: 30px">ASSIGN TAGS TO</span>                        
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1"><i class="fa fa-user"></i></span>
                        <div align="left" id="selectEmp">                                                    
                            <select class="form-control selectEmp" data-placeholder="Pilih Karyawan" name="other_employees" id="assignEmployee" style="width: 100%">
                                <option value=""></option>                                
                            </select>
                        </div>
                    </div>                                        
                    <button class="btn btn-success" id="btnAssign" style="height: 50px; font-size: 20px; width: 100%; margin-top: 1%;" onclick="assignTag()"><i class="fa fa-check"></i> ASSIGN</button>
                </div>
            </div>
        </div>


    </section>
@endsection
@section('scripts')

    <script src="{{ url('js/moment.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var employees = [];

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.datepicker').datepicker({
                <?php $tgl_max = date('Y-m-d'); ?>
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true,
                startDate: '<?php echo $tgl_max; ?>'
            });

            $('.timepicker').timepicker({
                showInputs: false,
                showMeridian: false,
                defaultTime: '0:00',
            });

            fetchTags();

            $('#tags_scan').focus();


        });

        $(function() {


            $('.selectEmp').select2({
                dropdownParent: $('#selectEmp'),
                allowClear:true
            });
        });

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

        $('#searchEmp').keyup(function() {
            var table = $('#tableUnregistered').DataTable();
            table.search($(this).val()).draw();
        });

        function registerTag(employee_id) {
            $('#loading').show();            
            var tag = $('#inputTag_' + employee_id).val();

            var data = {
                employee_id: employee_id,
                tag: tag
            }                        

            $.post('{{ url('scan/tags/employee/register') }}', data, function(result, status, xhr) {
                if (result.status) {                    
                    $('#tags_scan').val("");
                    $('#tags_scan').focus();         
                    $('#inputTag_' + employee_id).val("");
                    $('#loading').hide();
                    openSuccessGritter('Success', result.message);
                    fetchTags();

                } else {                    
                    $('#inputTag_' + employee_id).val("");
                    $('#loading').hide();
                    openErrorGritter('Error', result.message);
                    
                }
            });            
        }
        

        $('#tags_scan').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                scanTag(this.value);                
            }
        });

        function scanTag(tag) {                        
            $('#loading').show();
            var data = {
                tag: tag
            }

            $.get('{{ url('scan/tags/employee') }}', data, function(result, status, xhr) {
                if (result.status) {                    
                    $('#tags_scan').val("");
                    $('#tags_scan').focus();         
                    $('#loading').hide();
                    openSuccessGritter('Success', result.message);
                    renderScannedTag(result.employee)

                } else {                    
                    $('#tags_scan').val("");
                    $('#tags_scan').focus();
                    $('#loading').hide();
                    openErrorGritter('Error', result.message);
                    
                }
            });            
        }      
        
        function renderScannedTag(employee) {            
            $('#tableRegisteredBody').html("");
                        
                var row = "";                
                row += '<tr>';
                row += '<td>' + '1' + '</td>';
                row += '<td>' + employee.department + '</td>';
                row += '<td>' + employee.section + '</td>';
                row += '<td id="CurrentEmployeeID">' + employee.employee_id + '</td>';
                row += '<td>' + employee.name + '</td>';
                row += '<td id="employeeRegisteredTag">' + employee.tag + '</td>';                    
                row += '<td><button class="btn btn-danger btn-sm" onclick="deleteTag(\'' + employee.employee_id + '\',\'' + employee.tag + '\')"><i class="fa fa-trash"></i> Delete Tag</button></td>';
                // replace tag
                row += '<td'
                row += '</tr>';                

            $('#tableRegisteredBody').append(row);
            row = "";
        }

        function assignTag() {
            var prev_employee_id = $('#CurrentEmployeeID').text();
            var employee_id = $('#assignEmployee').val();
            var tag = $('#employeeRegisteredTag').text();

            var data = {
                prev_employee_id: prev_employee_id,
                new_employee_id: employee_id,
                tag: tag
            }

            $.post('{{ url('scan/tags/employee/assign') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#tags_scan').val("");
                    $('#tags_scan').focus();
                    openSuccessGritter('Success', result.message);
                    $('selectEmp').val(null).trigger('change');
                    fetchTags();
                } else {
                    $('#tags_scan').val("");
                    $('#tags_scan').focus();
                    openErrorGritter('Error', result.message);
                }
            });
        }

        function fetchTags() {
            $('#loading').show();

            $.get('{{ url('fetch/tags/employee') }}', function(result, status, xhr) {
                if (result.status) {
                    
                    var select = '';

                    $.each(result.employee_unregistered, function(key, value) {
                        select += '<option value="' + value.employee_id + '">' + value.employee_id + ' - ' + value.name + '</option>';
                    });

                    $('.selectEmp').append(select);

                    $('#tableUnregistered').DataTable().clear();
                    $('#tableUnregistered').DataTable().destroy();

                    employees = result.employees;
                    $('#tableUnregisteredBody').html("");
                    $('#tableRegisteredBody').html("");
                    var index = 1;
                    var row = "";

                    $.each(result.employee_unregistered, function(key, value) {
                        row += '<tr>';
                        row += '<td>' + index + '</td>';
                        row += '<td>' + value.department + '</td>';
                        row += '<td>' + value.section + '</td>';
                        row += '<td>' + value.employee_id + '</td>';
                        row += '<td>' + value.name + '</td>';
                        row += '<td><input type="text" style="width:100%;" class="form-control" id="inputTag_' + value.employee_id + '" placeholder="SCAN HERE!"></td>';
                        row += '<td><button class="btn btn-success btn-sm" onclick="registerTag(\'' + value.employee_id + '\')"><i class="fa fa-check"></i> Register</button></td>';
                        row += '</tr>';
                        index++;
                    });

                    $('#tableUnregisteredBody').append(row);
                    
                    var table = $('#tableUnregistered').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [10, 25, 50, -1],
                            ['10 rows', '25 rows', '50 rows', 'Show all']
                        ],
                        'buttons': {
                            buttons: []
                        },
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': true,
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
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function deleteTag(employee_id, tag) {
            if (confirm("Are you sure you want to delete this tag?")) {
                $('#loading').show();
    
                var data = {
                    employee_id: employee_id,
                    tag: tag
                }
                console.log(data);
    
                $.post('{{ url('delete/tags/employee') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        $('#loading').hide();
                        openSuccessGritter('Success', result.message);
                        $('#tableRegisteredBody').html("");
                        fetchTags();
                    } else {
                        $('#loading').hide();
                        openErrorGritter('Error', result.message);
                    }
                });
            }
        }

        function refresh() {
            $('#tableRegisteredBody').html("");
            fetchTags();            
        }
    </script>
@endsection
