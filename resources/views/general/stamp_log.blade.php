@extends('layouts.display')
@section('stylesheets')
<link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
<style type="text/css">
    thead>tr>th {
        text-align: center;
    }

    tbody>tr>td {
        background-color: #f9f9f9;
        text-align: center;
    }

    tfoot>tr>th {
        text-align: center;
    }

    td:hover {
        overflow: visible;
    }

    table.table-bordered {
        border: 1px solid black;
    }

    table.table-bordered>thead>tr>th {
        border: 1px solid black;
        vertical-align: middle;
    }

    table.table-bordered>tbody>tr>td {
        border: 1px solid black;
        padding-top: 5px;
        padding-bottom: 5px;
        vertical-align: middle;
    }

    table.table-bordered>tfoot>tr>th {
        border: 1px solid black;
        vertical-align: middle;
    }

    #loading,
    #error {
        display: none;
    }
    .dataTables_info,.dataTables_filter{
        color: white !important;
    }
</style>
@stop
@section('content')
<section class="content" style="padding-top: 0;">
    <div id="loading"
    style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
    <p style="position: absolute; color: White; top: 45%; left: 45%;">
        <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
    </p>
</div>
<div class="row">
    <div class="col-xs-12">
        <center>
            <input type="text" style="text-align: center; width: 50%;" class="form-control" id="employee_tag"
            name="employee_tag" placeholder="Scan ID Card...">
        </center>
    </div>
    <div class="col-xs-4" style="margin-top: 10px;">
        <div style="background-color: #00a65a; color: white; padding: 5px; text-align: center; margin-bottom: 8px;">
            <span style="font-weight: bold; font-size: 20px;">Currently Used By</span>
        </div>
        <table id="" class="table table-bordered table-striped table-hover">
            <tbody>
                <tr>
                    <td style="width: 0.2%; font-weight: bold; font-size: 1.2vw; text-align: left;">Employee ID</td>
                    <td id="editEmployeeId"
                    style="width: 1%; text-align: left; font-weight: bold; font-size: 1.2vw;"></td>
                </tr>
                <tr>
                    <td style="width: 0.2%; font-weight: bold; font-size: 1.2vw; text-align: left;">Employee Name
                    </td>
                    <td id="editEmployeeName"
                    style="width: 1%; text-align: left; font-weight: bold; font-size: 1.2vw;"></td>
                </tr>
                <tr>
                    <td style="width: 0.2%; font-weight: bold; font-size: 1.2vw; text-align: left;">Department</td>
                    <td id="editDepartment"
                    style="width: 1%; text-align: left; font-weight: bold; font-size: 1.2vw;"></td>
                </tr>
                <tr>
                    <td style="width: 0.2%; font-weight: bold; font-size: 1.2vw; text-align: left;">Purpose</td>
                    <td id="editCategory" style="width: 1%; text-align: left; font-weight: bold; font-size: 1.2vw;">
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.2%; font-weight: bold; font-size: 1.2vw; text-align: left;">Used From</td>
                    <td id="editDateFrom" style="width: 1%; text-align: left; font-weight: bold; font-size: 1.2vw;">
                    </td>
                </tr>
                <tr>
                    <td style="width: 0.2%; font-weight: bold; font-size: 1.2vw; text-align: left;">Duration</td>
                    <td id="editDuration" style="width: 1%; text-align: left; font-weight: bold; font-size: 1.2vw;">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-xs-8" style="margin-top: 10px;">
        <div style="background-color: #605ca8; color: white; padding: 5px; text-align: center; margin-bottom: 8px;">
            <span style="font-weight: bold; font-size: 20px;">Stamp Usage Records</span>
        </div>

        <table id="tableRecord" class="table table-bordered table-hover">
            <thead style="">
                <tr>
                    <th style="width: 1%; text-align: left; background-color: #605ca8; color: white;">Department
                    </th>
                    <th style="width: 1%; text-align: left; background-color: #605ca8; color: white;">Employee</th>
                    <th style="width: 1%; text-align: left; background-color: #605ca8; color: white;">Purpose</th>
                    <th style="width: 0.5%; text-align: center; background-color: #605ca8; color: white;">From</th>
                    <th style="width: 0.5%; text-align: center; background-color: #605ca8; color: white;">To</th>
                </tr>
            </thead>
            <tbody id="tableRecordBody">
            </tbody>
        </table>
    </div>
</div>
</section>

<div class="modal fade" id="modalCategory">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-body table-responsive no-padding">
                    <input type="hidden" id="createEmployeeId">
                    <input type="hidden" id="createEmployeeName">
                    <input type="hidden" id="createDepartment">
                    <table id="tableCategory" class="table table-bordered table-striped table-hover"
                    style="margin-bottom: 0;">
                    <thead style="">
                        <tr>
                            <th style="width: 0.1%; text-align: center;">#</th>
                            <th style="width: 10%;">Purpose</th>
                            <th style="width: 1%; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tableCategoryBody">
                    </tbody>
                </table>
            </div>
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
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    jQuery(document).ready(function() {
        $('#employee_tag').focus();
        fetchData();
    });

    var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
    var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
    var categories = <?php echo json_encode($categories); ?>;
    var employees = <?php echo json_encode($employees); ?>;
    var general_stamps = "";
    var general_stamp_logs = [];

    $('#employee_tag').keydown(function(event) {
        if (event.keyCode == 13 || event.keyCode == 9) {
            if ($("#employee_tag").val().length >= 9 && $("#employee_tag").val().length <= 10) {
                var found = false;
                if ($("#editEmployeeId").text() == "") {
                    if ($("#employee_tag").val().length == 9) {
                        $.each(employees, function(key, value) {
                            if (value.employee_id == $('#employee_tag').val()) {
                                clearModal();
                                var tableCategoryBody = "";
                                $('#tableCategoryBody').html('');
                                var cnt = 0;
                                for (var i = 0; i < categories.length; i++) {
                                    if (categories[i].department == value.department) {
                                        cnt += 1;
                                        tableCategoryBody += '<tr>';
                                        tableCategoryBody +=
                                        '<td style="width: 0.1%; text-align: center;">' + cnt +
                                        '</td>';
                                        tableCategoryBody += '<td style="width: 10%;">' + categories[i]
                                        .category + '</td>';
                                        tableCategoryBody +=
                                        '<td style="width: 1%; text-align: center;"><button class="btn btn-success" onclick="createLog(\'' +
                                        categories[i].category + '\')">Select</button></td>';
                                        tableCategoryBody += '</tr>';
                                    }
                                }
                                $('#createEmployeeId').val(value.employee_id);
                                $('#createEmployeeName').val(value.name);
                                $('#createDepartment').val(value.department);
                                $('#tableCategoryBody').append(tableCategoryBody);
                                $('#modalCategory').modal('show');
                                found = true;
                                return false;
                            }
                        });
                    }
                    if ($("#employee_tag").val().length == 10) {
                        $.each(employees, function(key, value) {
                            if (value.tag == $('#employee_tag').val()) {
                                clearModal();
                                var tableCategoryBody = "";
                                $('#tableCategoryBody').html('');
                                var cnt = 0;
                                for (var i = 0; i < categories.length; i++) {
                                    if (categories[i].department == value.department) {
                                        cnt += 1;
                                        tableCategoryBody += '<tr>';
                                        tableCategoryBody +=
                                        '<td style="width: 0.1%; text-align: center;">' + cnt +
                                        '</td>';
                                        tableCategoryBody += '<td style="width: 10%;">' + categories[i]
                                        .category + '</td>';
                                        tableCategoryBody +=
                                        '<td style="width: 1%; text-align: center;"><button class="btn btn-success" onclick="createLog(\'' +
                                        categories[i].category + '\')">Select</button></td>';
                                        tableCategoryBody += '</tr>';
                                    }
                                }
                                $('#createEmployeeId').val(value.employee_id);
                                $('#createEmployeeName').val(value.name);
                                $('#createDepartment').val(value.department);
                                $('#tableCategoryBody').append(tableCategoryBody);
                                $('#modalCategory').modal('show');
                                found = true;
                                return false;
                            }
                        });
                    }
                    if(found == false){
                        $("#employee_tag").val("");
                        $("#employee_tag").val("");
                        $('#loading').hide();
                        openErrorGritter('Error!', 'Employee data not found.');
                        audio_error.play();
                        return false;
                    }
                } else {
                    var tag = $('#employee_tag').val();
                    var data = {
                        tag: tag,
                    }

                    $.post('{{ url('update/YMPI_stamp_log') }}', data, function(result, status, xhr) {
                        if (result.status) {
                            $('#editEmployeeId').text("");
                            $('#editEmployeeName').text("");
                            $('#editDepartment').text("");
                            $('#editCategory').text("");
                            $('#editDateFrom').text("");
                            $('#editDuration').html("");
                            in_time = null;

                            var tableRecordBody = "";

                            tableRecordBody += '<tr>';
                            tableRecordBody += '<td style="width: 1%; text-align: left;">' + result
                            .department + '</td>';
                            tableRecordBody += '<td style="width: 1%; text-align: left;">' + result
                            .created_by + '<br>' + result.created_by_name + '</td>';
                            tableRecordBody += '<td style="width: 1%; text-align: left;">' + result
                            .category + '</td>';
                            tableRecordBody += '<td style="width: 0.5%; text-align: center;">' + result
                            .date_from + '</td>';
                            tableRecordBody += '<td style="width: 0.5%; text-align: center;">' + result
                            .date_to + '</td>';
                            tableRecordBody += '</tr>';

                            $('#tableRecordBody').prepend(tableRecordBody);

                            clearModal();
                            $('#loading').hide();
                            openSuccessGritter('Success!', result.message);
                            audio_ok.play();
                        } else {
                            clearModal();
                            $('#loading').hide();
                            openErrorGritter('Error!', result.message);
                            audio_error.play();
                            return false;
                        }
                    });
                }
            }
        }
    });

var in_time;

function setTime() {
    if (in_time != null) {
        var duration = diff_seconds(new Date(), in_time);
        document.getElementById("hours").innerHTML = pad(parseInt(duration / 3600));
        document.getElementById("minutes").innerHTML = pad(parseInt((duration % 3600) / 60));
        document.getElementById("seconds").innerHTML = pad(duration % 60);
    }
}

function diff_seconds(dt2, dt1) {
    var diff = (dt2.getTime() - dt1.getTime()) / 1000;
    return Math.abs(Math.round(diff));
}

function pad(val) {
    var valString = val + "";
    if (valString.length < 2) {
        return "0" + valString;
    } else {
        return valString;
    }
}

function createLog(category) {
    var employee_id = $('#createEmployeeId').val();
    var employee_name = $('#createEmployeeName').val();
    var department = $('#createDepartment').val();
    var data = {
        employee_id: employee_id,
        employee_name: employee_name,
        department: department,
        category: category
    }
    $.post('{{ url('input/YMPI_stamp_log') }}', data, function(result, status, xhr) {
        if (result.status) {
            $('#editEmployeeId').text(result.created_by);
            $('#editEmployeeName').text(result.created_by_name);
            $('#editDepartment').text(result.department);
            $('#editCategory').text(result.category);
            $('#editDateFrom').text(result.created_at);

            var editDuration = '';
            var date_in_time = result.created_at.replace(/-/g, '/');
            in_time = new Date(date_in_time);
            editDuration += '<label id="hours">' + pad(parseInt(diff_seconds(new Date(), in_time) / 3600)) +
            '</label>:';
            editDuration += '<label id="minutes">' + pad(parseInt((diff_seconds(new Date(), in_time) %
                3600) / 60)) + '</label>:';
            editDuration += '<label id="seconds">' + pad(diff_seconds(new Date(), in_time) % 60) +
            '</label>';

            $('#editDuration').html(editDuration);

            setInterval(setTime, 1000);
            clearModal();
            $('#loading').hide();
            openSuccessGritter('Success!', result.message);
            audio_ok.play();
        } else {
            $('#loading').hide();
            openErrorGritter('Error!', result.message);
            audio_error.play();
        }
    });
}

function fetchData() {
    var data = {

    }
    $.get('{{ url('fetch/YMPI_stamp_log') }}', data, function(result, status, xhr) {
        if (result.status) {
            general_stamps = result.general_stamps;
            general_stamp_logs = result.general_stamp_logs;

            if (result.general_stamps) {
                $('#editEmployeeId').text(result.general_stamps.created_by);
                $('#editEmployeeName').text(result.general_stamps.created_by_name);
                $('#editDepartment').text(result.general_stamps.department);
                $('#editCategory').text(result.general_stamps.category);
                $('#editDateFrom').text(result.general_stamps.created_at);

                var editDuration = '';
                var date_in_time = result.general_stamps.created_at.replace(/-/g, '/');
                in_time = new Date(date_in_time);
                editDuration += '<label id="hours">' + pad(parseInt(diff_seconds(new Date(), in_time) /
                    3600)) + '</label>:';
                editDuration += '<label id="minutes">' + pad(parseInt((diff_seconds(new Date(), in_time) %
                    3600) / 60)) + '</label>:';
                editDuration += '<label id="seconds">' + pad(diff_seconds(new Date(), in_time) % 60) +
                '</label>';

                $('#editDuration').html(editDuration);

                setInterval(setTime, 1000);
            }

            var tableRecordBody = "";
            $('#tableRecordBody').html('');
            $('#tableRecord').DataTable().clear();
            $('#tableRecord').DataTable().destroy();
            $.each(result.general_stamp_logs, function(key, value) {
                tableRecordBody += '<tr>';
                tableRecordBody += '<td style="width: 1%; text-align: left;">' + value.department +
                '</td>';
                tableRecordBody += '<td style="width: 1%; text-align: left;">' + value.created_by +
                '<br>' + value.created_by_name + '</td>';
                tableRecordBody += '<td style="width: 1%; text-align: left;">' + value.category +
                '</td>';
                tableRecordBody += '<td style="width: 0.5%; text-align: center;">' + value
                .date_from + '</td>';
                tableRecordBody += '<td style="width: 0.5%; text-align: center;">' + value.date_to +
                '</td>';
                tableRecordBody += '</tr>';
            });
            $('#tableRecordBody').append(tableRecordBody);

            $('#tableRecord').DataTable({
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

            clearModal();
        } else {
            $('#loading').hide();
            openErrorGritter('Error!', result.message);
            audio_error.play();
        }
    });
}

$('#modalCategory').on('hidden.bs.modal', function() {
    $('#employee_tag').val('');
    $('#employee_tag').focus();
});

function clearModal() {
    $('#createEmployeeId').val('');
    $('#createEmployeeName').val('');
    $('#createDepartment').val('');
    $('#tableCategoryBody').html('');
    $('#modalCategory').modal('hide');
    $('#employee_tag').val('');
    $('#employee_tag').focus();
}

function openSuccessGritter(title, message) {
    jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-success',
        image: '{{ url('images/image-screen.png') }}',
        sticky: false,
        time: '3000'
    });
}

function openErrorGritter(title, message) {
    jQuery.gritter.add({
        title: title,
        text: message,
        class_name: 'growl-danger',
        image: '{{ url('images/image-stop.png') }}',
        sticky: false,
        time: '3000'
    });
}
</script>
@endsection
