@extends('layouts.notification')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <style type="text/css">
        #tableDetail>tbody>tr:hover {
            background-color: #7dfa8c !important;
        }

        tbody>tr>td {
            padding: 10px 5px 10px 5px;
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
            height: 40px;
            padding: 2px 5px 2px 5px;
        }

        .contr #loading {
            display: none;
        }

        .label-status {
            color: black;
            font-size: 0.8vw;
            border-radius: 4px;
            padding: 3px 10px 5px 10px;
            border: 1.5px solid black;
            vertical-align: middle;
        }

        .radio {
            display: inline-block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 16px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        /* Hide the browser's default radio button */
        .radio input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        /* Create a custom radio button */
        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 25px;
            width: 25px;
            background-color: #ccc;
            border-radius: 50%;
        }

        /* On mouse-over, add a grey background color */
        .radio:hover input~.checkmark {
            background-color: #ccc;
        }

        /* When the radio button is checked, add a blue background */
        .radio input:checked~.checkmark {
            background-color: #2196F3;
        }

        /* Create the indicator (the dot/circle - hidden when not checked) */
        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        /* Show the indicator (dot/circle) when checked */
        .radio input:checked~.checkmark:after {
            display: block;
        }

        /* Style the indicator (dot/circle) */
        .radio .checkmark:after {
            top: 9px;
            left: 9px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: white;
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

        <div class="row">
            <div class="col-xs-8 col-xs-offset-2" style="margin-top: 1%; padding:0px;">
                <h1 style="text-align: center;">CHECKLIST PENGECEKAN TRUCK CONTAINER</h1>
                <h3>A. IDENTITAS</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <input type="text" id="checklist_id" value="{{ $checklist_id }}" hidden>
                        <thead>
                            <tr>
                                <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
                                    STATUS
                                </th>
                                <th style="font-size: 16px; width: 70%;">
                                    <input style="font-size: 16px; height: 40px; width: 100%;" type="text" id="status"
                                        value="{{ strtoupper($status) }}" readonly>
                                </th>
                            </tr>
                            <tr>
                                <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
                                    PIC Check
                                </th>
                                <th style="font-size: 16px; width: 70%;">
                                    <select class="form-control select2" data-placeholder="Pilih PIC" id="pic"
                                        style="width: 100%; font-size: 16px">
                                        <option value=""></option>
                                        @foreach ($security as $row)
                                            <option value="{{ $row->employee_id }}">
                                                {{ $row->employee_id }} - {{ $row->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                            </tr>
                            <tr>
                                <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
                                    Kategori
                                </th>
                                <th style="font-size: 16px; width: 70%;">
                                    <select class="form-control select2" data-placeholder="Pilih Kategori" id="category"
                                        style="width: 100%; font-size: 16px">
                                        <option value=""></option>
                                        <option value="EXPORT">EXPORT</option>
                                        <option value="IMPORT">IMPORT</option>
                                    </select>
                                </th>
                            </tr>
                            <tr>
                                <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
                                    Nama Driver
                                </th>
                                <th style="font-size: 16px; width: 70%;">
                                    <input style="font-size: 16px; height: 40px; width: 100%;" type="text"
                                        id="driver_name" placeholder="Masukkan Nama Driver ...">
                                </th>
                            </tr>
                            <tr>
                                <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
                                    Nomor Kendaraan
                                </th>
                                <th style="font-size: 16px; width: 70%;">
                                    <input style="font-size: 16px; height: 40px; width: 100%;" type="text"
                                        id="vehicle_registration_number"
                                        placeholder="Masukkan Nomor Kendaraan (Contoh : L1987UW)...">
                                </th>
                            </tr>
                            <tr>
                                <th style="background-color: #cddc39; font-size: 16px; width: 30%;">
                                    Nomor Container
                                </th>
                                <th style="font-size: 16px; width: 70%;">
                                    <input style="font-size: 16px; height: 40px; width: 100%;" type="text"
                                        id="container_number" placeholder="Masukkan Nomor Container ...">
                                </th>
                            </tr>

                        </thead>
                    </table>
                </div>

                <h3>B. POIN PENGECEKAN</h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" style="margin-bottom:0.5%;">
                        <thead>
                            <tr>
                                <th style="text-align: center;">
                                    <img src="{{ url('files/checksheet/guidelines/container_checklist.jpg') }}"
                                        width="100%">
                                    </td>
                                </th>
                            </tr>
                        </thead>
                    </table>
                    <table class="table table-bordered table-striped" style="">
                        <thead>
                            @php
                                $count = 0;
                            @endphp
                            @for ($i = 0; $i < count($checklist); $i++)
                                <tr>
                                    <th style="font-size: 16px; background-color: white;">
                                        <div class="col-xs-12">
                                            <div class="row">
                                                <div class="col-xs-2" style="padding-top: 2%;">
                                                    <span>{{ ++$count }}. {{ $checklist[$i]->point_check }}</span>
                                                </div>
                                                <div class="col-xs-6" style="padding-top: 2%;">
                                                    <span>@php echo $checklist[$i]->guidelines; @endphp</span>
                                                </div>
                                                <div class="col-xs-1">
                                                    <label class="radio" style="">
                                                        <span style="font-weight: bold; color: green;">OK</span>
                                                        <input type="radio"
                                                            id="result_{{ str_replace(' ', '', $checklist[$i]->point_check) }}"
                                                            name="result_{{ str_replace(' ', '', $checklist[$i]->point_check) }}"
                                                            value="OK">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                    <label class="radio" style="">
                                                        <span style="font-weight: bold; color: red">NG</span>
                                                        <input type="radio"
                                                            id="result_{{ str_replace(' ', '', $checklist[$i]->point_check) }}"
                                                            name="result_{{ str_replace(' ', '', $checklist[$i]->point_check) }}"
                                                            value="NG">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                                <div class="col-xs-3">
                                                    <center>
                                                        <input type="file" class="file" style="display:none"
                                                            onchange="readURL(this);"
                                                            id="input_photo_{{ str_replace(' ', '', $checklist[$i]->point_check) }}">
                                                        <button class="btn btn-primary btn-lg"
                                                            id="btnImage_{{ str_replace(' ', '', $checklist[$i]->point_check) }}"
                                                            value="Photo" onclick="buttonImage(this)"
                                                            style="font-size: 1.5vw; width: 80%; height: 100px;"><i
                                                                class="fa fa-camera"></i>
                                                            &nbsp;&nbsp;&nbsp;Evidence
                                                        </button>
                                                        <img width="150px"
                                                            id="img_{{ str_replace(' ', '', $checklist[$i]->point_check) }}"
                                                            src="" onclick="buttonImage(this)"
                                                            style="display: none; width: 80%; height: 100px;"
                                                            alt="your image" />
                                                    </center>
                                                    <input type="text" style="display:none"
                                                        id="input_file_src_{{ str_replace(' ', '', $checklist[$i]->point_check) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </th>
                                </tr>
                            @endfor

                            <tr>
                                <th style="font-size: 16px;">
                                    <span>Detail Temuan (<i>Jika ada <span class="text-red">*</span></i>)</span>
                                    <textarea style="width:100%;" id="note" rows="3" placeholder="Masukkan Detail Temuan ..."></textarea>
                                    <i><span class="text-red">*</span> Segera laporkan temuan ke Staff/Chief Logistic dan
                                        atau atasan bagian HR</i>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="col-xs-8 col-xs-offset-2" style="margin-bottom: 10%;">
                    <button class="btn btn-lg btn-success" id="submit_checklist"
                        style="width: 100%; font-weight: bold; font-size: 25px;" onclick="submitChecklist()">
                        <i class="fa fa-save"></i>&nbsp;&nbsp;Submit Checklist
                    </button>
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
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            $('.select2').select2();

            checkCategory();

        });

        var data = <?php echo json_encode($data); ?>;
        var status = <?php echo json_encode($status); ?>;
        var checklist = <?php echo json_encode($checklist); ?>;

        function checkCategory() {
            if (status.toUpperCase() != 'CHECK-IN') {
                $("#checklist_id").val(data.checklist_id);
                $('#status').val('CHECK-OUT');
                $('#category').val(data.category).trigger('change.select2');
                $('#driver_name').val(data.driver_name);
                $('#vehicle_registration_number').val(data.vehicle_registration_number);
                $('#container_number').val(data.container_number);
            }
        }

        function buttonImage(elem) {
            $(elem).closest("center").find("input").click();
        }

        const compressImage = async (file, {
            quality = 1,
            type = file.type
        }) => {

            const imageBitmap = await createImageBitmap(file);

            const canvas = document.createElement('canvas');
            canvas.width = imageBitmap.width;
            canvas.height = imageBitmap.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(imageBitmap, 0, 0);

            const blob = await new Promise((resolve) =>
                canvas.toBlob(resolve, type, quality)
            );

            return new File([blob], file.name, {
                type: blob.type,
            });
        };

        const input = document.querySelector('.file');
        input.addEventListener('change', async (e) => {
            const {
                files
            } = e.target;

            if (!files.length) return;

            const dataTransfer = new DataTransfer();

            for (const file of files) {

                if (!file.type.startsWith('image')) {
                    dataTransfer.items.add(file);
                    continue;
                }

                const compressedFile = await compressImage(file, {
                    quality: 0.3,
                    type: 'image/jpeg',
                });

                dataTransfer.items.add(compressedFile);

                readURL(compressedFile);
            }

            e.target.files = dataTransfer.files;
        });

        function readURL(compressedFile) {
            var reader = new FileReader();
            var img = $(compressedFile).closest("center").find("img");
            reader.onload = function(e) {
                $(img).show();
                $(img).attr('src', e.target.result);
            };
            reader.readAsDataURL(compressedFile.files[0]);
            $(compressedFile).closest("center").find("button").hide();
            saveImageEvidence(compressedFile);
        }

        function saveImageEvidence(input) {
            var checklist_id = $("#checklist_id").val();
            var status = $("#status").val();

            var formData = new FormData();
            formData.append('file_datas', $(input).prop('files')[0]);
            var file = $(input).val().replace(/C:\\fakepath\\/i, '').split(".");
            var photo_id = input.id.replaceAll('input_photo_', '');

            formData.append('checklist_id', checklist_id);
            formData.append('status', status);
            formData.append('photo_id', photo_id);
            formData.append('extension', file[1]);
            formData.append('photo_name', file[0]);

            $.ajax({
                url: "{{ url('input/checklist_evidence_security') }}",
                method: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(result, status, xhr) {
                    if (result.status) {
                        if (result.filename.length > 0) {
                            $("#input_file_src_" + photo_id).val(result.filename);
                            openSuccessGritter("Success", "Photo Submitted");
                        } else {
                            $("#input_file_src_" + photo_id).val('');

                            $("#img_" + photo_id).attr('src', '');
                            $("#img_" + photo_id).css('display', 'none');
                            $("#btnImage_" + photo_id).css('display', 'block');
                            alert("Foto tidak masuk, ulangi pengambilan foto");
                        }

                    } else {
                        $("#img_" + photo_id).attr('src', '');
                        $("#img_" + photo_id).css('display', 'none');
                        $("#btnImage_" + photo_id).css('display', 'block');
                        alert("Foto tidak masuk, ulangi pengambilan foto");

                        openErrorGritter("Error", result.message);
                    }

                },
                error: function(result, status, xhr) {
                    $("#img_" + photo_id).attr('src', '');
                    $("#img_" + photo_id).css('display', 'none');
                    $("#btnImage_" + photo_id).css('display', 'block');
                    alert("Foto tidak masuk, ulangi pengambilan foto");

                    openErrorGritter("Error", "Koneksi wifi terputus");
                },
            })
        }

        function submitChecklist() {

            var status = $("#status").val();
            var checklist_id = $("#checklist_id").val();
            var pic = $("#pic").val();
            var category = $("#category").val();
            var driver_name = $("#driver_name").val();
            var vehicle_registration_number = $("#vehicle_registration_number").val();
            var container_number = $("#container_number").val();
            var note = $("#note").val();

            if (status == '' || pic == '' || category == '' || driver_name == '' || vehicle_registration_number == '' ||
                container_number == '') {
                openErrorGritter("Error!", "Semua isian identitas harus diisi");
                return false;
            }

            var formData = new FormData();
            formData.append('status', status);
            formData.append('checklist_id', checklist_id);
            formData.append('pic', pic);
            formData.append('category', category);
            formData.append('driver_name', driver_name);
            formData.append('vehicle_registration_number', vehicle_registration_number);
            formData.append('container_number', container_number);
            formData.append('note', note);

            var checklist_answer = [];
            var ng_status = false;
            for (let i = 0; i < checklist.length; i++) {
                if ($('input[name="result_' + checklist[i].point_check.replaceAll(' ', '') + '"]:checked').val() == 'OK' ||
                    $('input[name="result_' + checklist[i].point_check.replaceAll(' ', '') + '"]:checked').val() == 'NG') {

                    if ($('input[name="result_' + checklist[i].point_check.replaceAll(' ', '') + '"]:checked').val() ==
                        'NG') {
                        ng_status = true;
                    }

                    if (document.getElementById("input_photo_" + checklist[i].point_check.replaceAll(' ', '')).files
                        .length == 0) {
                        openErrorGritter("Error!", "Foto pengecekan harus disertakan");
                        return false;
                    }

                    var source = $("#input_file_src_" + checklist[i].point_check.replaceAll(' ', '')).val();

                    checklist_answer.push({
                        'point_check': checklist[i].point_check,
                        'guidelines': checklist[i].guidelines,
                        'result': $('input[name="result_' + checklist[i].point_check.replaceAll(' ', '') +
                            '"]:checked').val(),
                        'source': source,
                    });

                } else {
                    openErrorGritter("Error", 'Masukan hasil pengecekan');
                    return false;
                }
            }

            if (ng_status) {
                if (note == '') {
                    openErrorGritter("Error!", "Hasil pengecekan NG, Detail temuaan wajib diuraikan");
                    return false;
                }
            }

            formData.append('checklist_answer', JSON.stringify(checklist_answer));

            $('#loading').show();
            $.ajax({
                url: "{{ url('input/checklist_container_security') }}",
                method: "POST",
                data: formData,
                contentType: false,
                cache: false,
                processData: false,
                success: function(result, status, xhr) {
                    if (result.status) {
                        $('#loading').hide();
                        openSuccessGritter("Success", result.message);
                        setTimeout(function() {
                            window.open('{{ url('index/checklist_container_security') }}', '_self');
                        }, 2000);

                    } else {
                        $('#loading').hide();
                        openErrorGritter("Error!", result.message);
                    }

                },
                error: function(result, status, xhr) {
                    $('#loading').hide();
                    openErrorGritter("Error!", result.message);
                    console.log(result.message);
                },
            })

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
    </script>
@endsection
