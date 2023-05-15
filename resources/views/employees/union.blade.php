@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        .table>tbody>tr>td {
            padding-bottom: 0px;
            padding-top: 0px;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
    </section>
@endsection
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
                    <div class="box box-solid" style="width: 60%;">
                        <div class="box-body">
                            <input type="hidden" id="createCategory">
                            <input type="hidden" id="createUnion">
                            <span style="font-weight: bold; font-size: 1.2vw;">Pilih Keperluan:</span><br>
                            <a href="javascript:void(0)"
                                style="width: 30%; border-color: black; color: black; font-size: 1vw; font-weight: bold;"
                                id="btn_join" onclick="btnCategory('join')" class="btn btn-sm">BERGABUNG</a>
                            <a href="javascript:void(0)"
                                style="width: 30%; border-color: black; color: black; font-size: 1vw; font-weight: bold;"
                                id="btn_leave" onclick="btnCategory('leave')" class="btn btn-sm">MENGUNDURKAN DIRI</a>
                            <br>
                            <br>
                            <span style="font-weight: bold; font-size: 1.2vw;">Pilih Serikat:</span><br>
                            <a href="javascript:void(0)"
                                style="width: 32%; border-color: black; color: black; font-size: 1vw; font-weight: bold;"
                                id="btn_SBM" onclick="btnUnion('SBM')" class="btn btn-sm"><img style="max-height: 100px;"
                                    src="{{ url('images/logo_sbm.png') }}"><br>SARIKAT BURUH MUSLIMIN
                                INDONESIA<br>(SARBUMUSI)</a>
                            <a href="javascript:void(0)"
                                style="width: 32%; border-color: black; color: black; font-size: 1vw; font-weight: bold;"
                                id="btn_SPMI" onclick="btnUnion('SPMI')" class="btn btn-sm"><img style="max-height: 100px;"
                                    src="{{ url('images/logo_spmi.png') }}"><br>SERIKAT PEKERJA
                                METAL
                                INDONESIA<br>(FSPMI)</a>
                            <a href="javascript:void(0)"
                                style="width: 32%; border-color: black; color: black; font-size: 1vw; font-weight: bold;"
                                id="btn_SPSI" onclick="btnUnion('SPSI')" class="btn btn-sm"><img style="max-height: 100px;"
                                    src="{{ url('images/logo_spsi.png') }}"><br>SERIKAT PEKERJA
                                SELURUH
                                INDONESIA<br>(SPSI)</a>
                            <br>
                            <br>
                            <span style="font-weight: bold; font-size: 1.2vw;">Pernyataan:</span>
                            <br>
                            <br>
                            <div class="col-xs-8 col-xs-offset-2" style="text-align: left;" id="statement_letter">

                            </div>
                        </div>
                    </div>
                </center>
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
                $('#createCategory').val("");
                $('#createUnion').val("");
            });

            var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
            var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
            var employee = <?php echo json_encode($employee); ?>;

            function createUnion(category, union) {
                if (confirm("Apakah anda yakin akan mengajukan pernyataan ini?")) {
                    $('#btnCreate').prop('disabled', true);
                    $('#btnFalse').prop('disabled', true);
                    var data = {
                        category: category,
                        union: union,
                        employee: employee,
                    }
                    $.post('{{ url('input/employee/union') }}', data, function(result, status, xhr) {
                        if (result.status) {
                            audio_ok.play();
                            openSuccessGritter('Sukses!', result.message);
                            $(this).delay(5000).queue(function() {
                                window.open('{{ route('emp_service', ['id' => '1']) }}', '_self');
                            });
                        } else {
                            $('#btnCreate').prop('disabled', false);
                            $('#btnFalse').prop('disabled', false);
                            audio_error.play();
                            openErrorGritter('Gagal!', result.message);
                            return false;
                        }
                    });
                } else {
                    return false;
                }
            }

            function fetchStatement() {
                var category = $('#createCategory').val();
                var union = $('#createUnion').val();

                $('#statement_letter').html("");
                var statement_letter = "";


                if (category == "" || union == "") {
                    return false;
                }
                statement_letter += '<p>Bahwa yang bertanda tangan dibawah ini:</p>';
                statement_letter += '<table class="table" id="tableStatement">';
                statement_letter += '<tbody>';
                statement_letter += '<tr>';
                statement_letter += '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NIK</td>';
                statement_letter += '<td>:</td>';
                statement_letter += '<td>' + employee.employee_id + '</td>';
                statement_letter += '</tr>';
                statement_letter += '<tr>';
                statement_letter += '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nama</td>';
                statement_letter += '<td>:</td>';
                statement_letter += '<td>' + employee.name + '</td>';
                statement_letter += '</tr>';
                statement_letter += '<tr>';
                statement_letter += '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tempat & Tanggal Lahir</td>';
                statement_letter += '<td>:</td>';
                statement_letter += '<td>' + employee.birth_place +
                    ', {{ date('d F Y', strtotime($employee->birth_date)) }}</td>';
                statement_letter += '</tr>';
                statement_letter += '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Alamat</td>';
                statement_letter += '<td>:</td>';
                statement_letter += '<td>' + employee.address + '</td>';
                statement_letter += '</tr>';
                statement_letter += '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No. Telp</td>';
                statement_letter += '<td>:</td>';
                statement_letter += '<td>' + employee.phone + '</td>';
                statement_letter += '</tr>';
                statement_letter += '<tr>';
                statement_letter += '<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bagian</td>';
                statement_letter += '<td>:</td>';
                statement_letter += '<td>' + employee.department + ' - ' + employee.section + '</td>';
                statement_letter += '</tr>';
                statement_letter += '</tbody>';
                statement_letter += '</table>';

                if (category == 'join') {
                    statement_letter +=
                        '<p>Menyatakan <span style="font-weight: bold; color: #00a65a;">BERGABUNG</span> dengan serikat pekerja</p>';
                }
                if (category == 'leave') {
                    statement_letter +=
                        '<p>Menyatakan <span style="font-weight: bold; color: #dd4b39;">MENGUNDURKAN DIRI</span> dari serikat pekerja</p>';
                }

                if (union == 'SBM') {
                    statement_letter +=
                        '<p style="font-weight: bold; font-size: 1.1vw;">SARIKAT BURUH MUSLIMIN INDONESIA (SARBUMUSI)</p>';
                    if (category == 'join') {
                        statement_letter +=
                            '<p>Saya bersedia membayar Check Of System / COS setiap bulan.<br>Dan bersedia bersikap loyal serta mentaati dan mematuhi kebijakan yang di tetapkan oleh Pengurus SARBUMUSI PT. YAMAHA MUSICAL PRODUCTS INDONESIA.</p>';
                    }
                }
                if (union == 'SPMI') {
                    statement_letter +=
                        '<p style="font-weight: bold; font-size: 1.1vw;">FEDERASI SERIKAT PEKERJA METAL INDONESIA (FSPMI)</p>';
                    if (category == 'join') {
                        statement_letter +=
                            '<p>Saya bersedia membayar Check Of System / COS PUK SPEE FSPMI sebesar 1% dari gaji pokok per bulan (UMK Kab. Pasuruan).<br>Dan bersedia bersikap loyal serta mentaati dan mematuhi kebijakan yang di tetapkan oleh Pengurus FSPMI PT. YAMAHA MUSICAL PRODUCTS INDONESIA.</p>';
                    }
                }
                if (union == 'SPSI') {
                    statement_letter +=
                        '<p style="font-weight: bold; font-size: 1.1vw;">SERIKAT PEKERJA SELURUH INDONESIA (SPSI)</p>';
                    if (category == 'join') {
                        statement_letter +=
                            '<p>Saya bersedia membayar Check Of System / COS sebesar 1% dari gaji pokok per bulan (UMK Kab. Pasuruan), sesuai dengan keputusan AD / ART FSP LEM SPSI.<br>Dan bersedia bersikap loyal serta mentaati dan mematuhi kebijakan yang di tetapkan oleh Pengurus SPSI PT. YAMAHA MUSICAL PRODUCTS INDONESIA.</p>';
                    }
                }

                statement_letter +=
                    '<p>Demikian pernyataan ini dibuat dengan sebenar-benarnya dan tanpa ada paksaan dari pihak manapun.</p>';

                statement_letter += '<p style="font-weight: bold;">Pasuruan, {{ date('d F Y') }}. </p><br><br>';

                statement_letter +=
                    '<a href="{{ route('emp_service', ['id' => '1']) }}" class="btn btn-danger pull-left" id="btnCancel" style="width: 49%;">BATAL</a>';
                statement_letter +=
                    '<button class="btn btn-success pull-right" id="btnCreate" onclick="createUnion(\'' + category + '\',\'' +
                    union +
                    '\')" style="width: 49%;">KONFIRMASI</button>';

                $('#statement_letter').append(statement_letter);
            }

            function btnCategory(category) {
                $('#btn_join').css('background-color', 'white');
                $('#btn_leave').css('background-color', 'white');
                $('#btn_' + category).css('background-color', '#90ed7d');

                $('#createCategory').val(category);
                fetchStatement();
            }

            function btnUnion(union) {
                $('#btn_SBM').css('background-color', 'white');
                $('#btn_SPMI').css('background-color', 'white');
                $('#btn_SPSI').css('background-color', 'white');
                $('#btn_' + union).css('background-color', '#90ed7d');

                $('#createUnion').val(union);
                fetchStatement();
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
