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
                <center>
                    <p id="employee_name"
                        style="font-size:18px; text-align: center; color: yellow; padding: 0px; margin: 0px; font-weight: bold; text-transform: uppercase;">
                    </p>
                </center>


                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Completion Only</h3>
                    </div>
                    <div class="panel-body">
                        <input style="margin-bottom: 10px;" id="employee_tag" type="text" placeholder="Tap ID Card"
                            class="form-control input-lg" name="employee_tag" disabled>
                        <input style="margin-bottom: 10px;" id="barcode" type="text" placeholder="Barcode Number"
                            class="form-control input-lg" name="barcode_number" disabled>
                        <button id="submit" style="display: none;"
                            class="btn btn-lg btn-success btn-block">&#9655;&nbsp;Mulai</button>
                        <button id="finish" style="display: none;"
                            class="btn btn-lg btn-danger btn-block">&#9723;&nbsp;Selesai</button>
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

    <div id="modalError" class="modal fade modal-danger" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>
                    <center>
                        <h3 class="modal-title">Error!</h3>
                    </center>
                </div>
                <div class="modal-body">
                    <center>
                        <h4 id="modalErrorMessage"></h4>
                    </center>
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
            $("#finish").css('display', 'none');
            $("#submit").css('display', 'block');
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var enabled = false;
        var histories = [];
        var employees = <?php echo json_encode($employees); ?>;

        $('#submit').on('click', function() {
            $('#employee_name').text('');

            enabled = true;
            $('#employee_tag').val('');
            $("#employee_tag").prop('disabled', !enabled);
            $('#employee_tag').focus();

            $('#barcode').val('');
            $("#barcode").prop('disabled', enabled);

            $("#finish").css('display', 'block');
            $("#submit").css('display', 'none');

            histories = [];
            $("#tableHistoryBody").empty();
        });



        $('#employee_tag').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                if ($("#employee_tag").val().length >= 9 && $("#employee_tag").val().length <= 10) {
                    var found = false;
                    var name = '';

                    if ($("#employee_tag").val().length == 9) {
                        $.each(employees, function(key, value) {
                            if (value.employee_id == $('#employee_tag').val()) {
                                found = true;
                                name = value.name;
                                return false;
                            }
                        });
                    }

                    if ($("#employee_tag").val().length == 10) {
                        $.each(employees, function(key, value) {
                            if (value.tag == $('#employee_tag').val()) {
                                found = true;
                                name = value.name;
                                return false;
                            }
                        });
                    }

                    if (found == false) {
                        $("#employee_tag").val("");
                        $("#employee_tag").focus();
                        openErrorGritter('Error!', 'Employee data not found.');
                        audio_error.play();
                        return false;
                    }

                    $('#employee_name').text('OPERATOR : ' + name);
                    enabled = true;
                    $("#employee_tag").prop('disabled', enabled);

                    $('#barcode').val('');
                    $("#barcode").prop('disabled', !enabled);
                    $('#barcode').focus();

                }
            }
        });

        $('#barcode').keydown(function(event) {
            if (event.keyCode == 13) {
                scanBarcode();
                return false;
            }
        });

        function scanBarcode() {

            histories.push($('#barcode').val());
            $('#barcode').val('');
            $('#barcode').focus();


            // $("#barcode").prop('disabled', true);
            // var barcode = $("#barcode").val();
            // var data = {
            //     tag: barcode
            // }
            // $.post('{{ url('input/completion') }}', data, function(result, status, xhr) {
            //     if (result.status) {
            //         $("#barcode").prop('disabled', false);
            //         openSuccessGritter('Success', result.message);
            //         audio_ok.play();
            //         $('#barcode').val("");
            //         $('#barcode').focus();
            //         histories.push(result.data);
            //     } else {
            //         $("#barcode").prop('disabled', false);
            //         $('#modalErrorMessage').text(result.message);
            //         $('#modalError').modal('show');
            //         audio_error.play();
            //         $('#barcode').val("");
            //         $('#barcode').focus();
            //     }
            // });
        }

        $('#finish').on('click', function() {
            enabled = false;

            $('#employee_tag').val("");
            $("#employee_tag").blur();
            $("#employee_tag").prop('disabled', !enabled);

            $('#barcode').val("");
            $("#barcode").blur();
            $("#barcode").prop('disabled', !enabled);

            $("#finish").hide();
            $("#submit").show();
            if (histories.length > 0) {
                var html = "";
                for (var i = 0; i < histories.length; i++) {
                    html += "<tr>";
                    html += "<td>" + (i + 1) + "</td>";
                    html += "<td>" + histories[i] + "</td>";
                    html += "<td>" + histories[i] + "</td>";
                    html += "<td>" + histories[i] + "</td>";
                    html += "</tr>";
                }
                $("#tableHistoryBody").append(html);
                $("#totalCompletion").text("Total Completion: " + histories.length);
                $('#modalHistory').modal('show');
            }

            $('#employee_name').text('');

        });

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
