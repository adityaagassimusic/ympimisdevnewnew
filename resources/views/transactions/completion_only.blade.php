@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
    </style>
@stop
@section('header')
    <section class="content-header">
    </section>
@endsection
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content">
        <div class="row">
            <div class="col-md-offset-4 col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Completion Only</h3>
                    </div>
                    <div class="panel-body">
                        <center>
                            <input type="text" style="width: 30%; text-align: center;" id="employee_id" disabled>
                            <input type="text" style="width: 68.2%; text-align: center;" id="employee_name" disabled>
                        </center>
                        <input style="margin-bottom: 10px; margin-top: 10px;" id="barcode" type="text"
                            placeholder="Barcode Number" class="form-control input-lg" name="barcode_number" disabled>
                        <button id="submit" class="btn btn-lg btn-success btn-block">&#9655;&nbsp;Mulai</button>
                        <button id="finish" class="btn btn-lg btn-danger btn-block">&#9723;&nbsp;Selesai</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div id="modalHistory" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                    <h3 class="modal-title">Completion List</h3>
                </div>
                <div class="modal-body">
                    <div class="row" id="histories">
                        <div class="col-md-12">
                            <h3 id="totalCompletion" style="font-weight: bold; font-size: 1.5vw; color: purple;">30</h3>
                            <div class="table-responsive">
                                <table class="table table-bordered table-stripped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Barcode</th>
                                            <th>Desc</th>
                                            <th>Lot</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableHistoryBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEmployee">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body table-responsive no-padding">
                        <center>
                            <br>
                            <br>
                            <br>
                            <br>
                            <span style="font-weight: bold; font-size: 2vw;">SCAN/TAP ID CARD</span>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </center>
                        <input type="text" style="text-align: center; width: 100%; font-size: 1.5vw;" id="employee_tag">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $("#finish").hide();
            $("#submit").show();
            $('#employee_id').val("");
            $('#employee_name').val("");
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var enabled = false;
        var histories = [];
        var employees = <?php echo json_encode($employees); ?>;

        $('#barcode').keydown(function(event) {
            if (event.keyCode == 13) {
                scanBarcode();
                return false;
            }
        });

        $('#employee_tag').keydown(function(event) {
            var found = false;
            if (event.keyCode == 13 || event.keyCode == 9) {
                tag = $('#employee_tag').val().toUpperCase();
                if (tag.length == 9) {
                    $.each(employees, function(key, value) {
                        if (value.employee_id == tag) {
                            $('#employee_id').val(value.employee_id);
                            $('#employee_name').val(value.name);
                            found = true;
                        }
                    });
                }
                if (tag.length == 10) {
                    $.each(employees, function(key, value) {
                        if (value.tag == tag) {
                            $('#employee_id').val(value.employee_id);
                            $('#employee_name').val(value.name);
                            found = true;
                        }
                    });
                }

                if (tag.length < 9 && tag.length > 10) {
                    $('#employee_tag').val("");
                    $('#employee_tag').focus();
                    openErrorGritter("Gagal!", "Tag karyawan tidak sesuai");
                    var audio = new Audio('{{ url('sound/error.mp3') }}');
                    audio.play();
                    return false;
                }

                if (found) {
                    $('#modalEmployee').modal('hide');
                    enabled = true;
                    $("#barcode").prop('disabled', !enabled);
                    $('#barcode').focus();
                    $("#finish").show();
                    $("#submit").hide();
                    histories = [];
                    $("#tableHistoryBody").empty();
                } else {
                    $('#employee_tag').val("");
                    $('#employee_tag').focus();
                    openErrorGritter("Gagal!", "Tag karyawan tidak ditemukan");
                    var audio = new Audio('{{ url('sound/error.mp3') }}');
                    audio.play();
                }
            }
        });

        $('#submit').on('click', function() {
            $('#modalEmployee').modal('show');
            $('#employee_tag').val("");
        });

        $('#modalEmployee').on('shown.bs.modal', function() {
            $('#employee_tag').focus();
        });

        $('#finish').on('click', function() {
            enabled = false;
            $('#barcode').val("");
            $("#barcode").blur();
            $("#barcode").prop('disabled', !enabled);
            $("#finish").hide();
            $('#employee_id').val("");
            $('#employee_name').val("");
            $("#submit").show();
            if (histories.length > 0) {
                console.table(histories);
                var html = "";
                for (var i = 0; i < histories.length; i++) {
                    var history = JSON.parse(histories[i]);
                    html += "<tr>";
                    html += "<td>" + (i + 1) + "</td>";
                    html += "<td>" + history.barcode_number + "</td>";
                    html += "<td>" + history.description + "</td>";
                    html += "<td>" + history.lot + "</td>";
                    html += "</tr>";
                }
                $("#tableHistoryBody").append(html);
                $("#totalCompletion").text("Total Completion: " + histories.length);
                $('#modalHistory').modal('show');
            }
        });

        function scanBarcode() {
            $("#barcode").prop('disabled', true);
            var employee_id = $('#employee_id').val();
            var employee_name = $('#employee_name').val();
            var barcode = $("#barcode").val();
            var data = {
                barcode_number: barcode,
                employee_id: employee_id,
                employee_name: employee_name
            }
            $.post('{{ url('input/completion_only') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $("#barcode").prop('disabled', false);
                    openSuccessGritter('Success', result.message);
                    audio_ok.play();
                    $('#barcode').val("");
                    $('#barcode').focus();
                    histories.push(result.data);
                } else {
                    $("#barcode").prop('disabled', false);
                    openErrorGritter('Error', result.message);
                    audio_error.play();
                    $('#barcode').val("");
                    $('#barcode').focus();
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
@stop
