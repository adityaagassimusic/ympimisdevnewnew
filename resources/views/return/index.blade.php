@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link href="{{ url('css/jquery.numpad.css') }}" rel="stylesheet">
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            font-size: 16px;
        }

        #tableBodyList>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c;
        }

        #tableBodyResume>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .nmpd-grid {
            border: none;
            padding: 20px;
        }

        .nmpd-grid>tbody>tr>td {
            border: none;
        }

        #loading {
            display: none;
        }

        #qr_item:hover {
            color: #ffffff
        }

        #ng_list {
            font-size: 20px;
            width: 100%;
            height:60px; 
            padding: 0 30px;		
        }	

        .select2-selection--single {
            font-size: 26px;
            height: 60px !important;
            text-align: center;
            color: #3c3c3c !important;
            background-color: #ffee00 !important;
        }

        #select2-ng_list-container{
            margin-top: 10px !important;
        }

        .select2-selection__arrow{
            margin-top: 15px !important;
        }

        .select2-dropdown{
            font-size: 20px;			
        }	

    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a data-toggle="modal" data-target="#modalQr" class="btn btn-primary btn-lg" style="color:white;">
                    &nbsp;<i class="glyphicon glyphicon-qrcode"></i>&nbsp;&nbsp;&nbsp;Scan Scanner&nbsp;
                </a>

                <a data-toggle="modal" data-target="#modalScan" class="btn btn-success btn-lg" style="color:white;">
                    &nbsp;<i class="fa fa-camera"></i>&nbsp;&nbsp;&nbsp;Scan Camera&nbsp;
                </a>                
            </li>
        </ol>
    </section>
@endsection

@section('content')
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 35%;">
                <span style="font-size: 40px">Sedang memproses, tunggu sebentar <i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>
        <input type="hidden" id="location">
        <div class="row">
            <div class="col-xs-5">
                <div class="box">
                    <div class="box-body">
                        <span style="font-size: 20px; font-weight: bold;">DAFTAR ITEM:</span>
                        <table class="table table-hover table-striped" id="tableList" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th style="width: 1%;">Material</th>
                                    <th style="width: 7%;">Description</th>
                                    <th style="width: 1%;">Kirim</th>
                                    <th style="width: 1%;">Terima</th>
                                    <th style="width: 1%;">Lot</th>
                                </tr>
                            </thead>
                            <tbody id="tableBodyList">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xs-7">
                <div class="row">
                    <div class="col-xs-12">
                        <span style="font-weight: bold; font-size: 16px;">Material:</span>
                        <input type="text" id="material_number"
                            style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
                    </div>
                    <div class="col-xs-12">
                        <span style="font-weight: bold; font-size: 16px;">Description:</span>
                        <input type="text" id="material_description"
                            style="width: 100%; height: 50px; font-size: 24px; text-align: center;" disabled>
                    </div>
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-6">
                                <span style="font-weight: bold; font-size: 16px;">Issue Location:</span>
                                <input type="text" id="issue"
                                    style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
                            </div>
                            <div class="col-xs-6">
                                <span style="font-weight: bold; font-size: 16px;">Receive Location:</span>
                                <input type="text" id="receive"
                                    style="width: 100%; height: 50px; font-size: 30px; text-align: center;" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <span style="font-weight: bold; font-size: 16px;">Select NG:</span>
                    </div>
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-4">							
                                <div class="form-group ng_list d-flex justify-content-center">
                                    <select class="form-control" id="ng_list" data-placeholder="Select NG" style="width: 100%;">
                                        <option value="">Select NG</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-danger" style="font-size: 35px; height: 60px; text-align: center;" onclick="minusCount()">
                                            <span class="fa fa-minus"></span>
                                        </button>
                                    </div>
                                    <input id="quantity" style="font-size: 3vw; height: 60px; text-align: center;" type="number" class="form-control numpad" value="0">
    
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-success" style="font-size: 35px; height: 60px; text-align: center;" onclick="plusCount()">
                                            <span class="fa fa-plus"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-4" style="padding-bottom: 10px;">
                                <button class="btn btn-success" style="font-size: 28px; width: 100%; height:60px; font-weight: bold; padding: 0;" onclick="addNGList()">
                                    <i class="fa fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-body" style="overflow-x: scroll">
                                <span style="font-size: 20px; font-weight: bold;">DAFTAR NG:</span>
                                <table class="table table-hover table-striped table-bordered" id="tableNG">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%;">List</th>
                                            <th style="width: 1%;">GMC</th>
                                            <th style="width: 6%;">Description</th>
                                            <th style="width: 1%;">Qty</th>
                                            <th style="width: 1%;">Defect</th>										
                                            <th style="width: 1%;">#</th>										
                                        </tr>
                                    </thead>
                                    <tbody id="tableBodyNG">
                                    </tbody>								
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="pull-right" style="margin-bottom:2%;">
                            <button class="btn btn-primary" style="font-size:26px; height: 60px; width:220px;" onclick="printReturn()">						
                                <i class="fa fa-print"></i> Cetak
                            </button>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-body" style="overflow-x: scroll;">
                                <span style="font-size: 20px; font-weight: bold;">RETURN BELUM DI KONFIRMASI
                                    (<?php echo e(date('d-M-Y')); ?>)</span>
                                <table class="table table-hover table-striped table-bordered" id="tableResume">
                                    <thead>
                                        <tr>
                                            <th style="width: 1%;">#</th>
                                            <th style="width: 1%;">Material</th>
                                            <th style="width: 6%;">Description</th>
                                            <th style="width: 1%;">Issue</th>
                                            <th style="width: 1%;">Receive</th>
                                            <th style="width: 1%;">Qty</th>
                                            <th style="width: 1%;">Creator</th>
                                            <th style="width: 1%;">Created</th>
                                            <th style="width: 1%;">Delete</th>
                                            <th style="width: 1%;">Reprint</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBodyResume">
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalLocation">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3 style="background-color: #00a65a; font-weight: bold;">Return Material<br>Pilih Lokasi Anda</h3>
                    </center>
                    <div class="modal-body">
                        <div class="form-group">
                            <center>
                                @foreach ($storage_locations as $storage_location)
                                    <div class="col-lg-2">
                                        <div class="row">
                                            <button style="margin-top: 20px; font-weight: bold; width: 80%;"
                                                class="btn btn-success"
                                                onclick="fetchReturnList('{{ $storage_location }}')">{{ $storage_location }}</button>
                                        </div>
                                    </div>
                                @endforeach
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalScan">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3 style="background-color: #00a65a; padding-top: 2%; padding-bottom: 2%; font-weight: bold;">Scan
                            Slip Return</h3>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id='scanner' class="col-xs-12">
                            <div class="col-xs-12">
                                <center>
                                    <div id="loadingMessage">
                                        🎥 Unable to access video stream
                                        (please make sure you have a webcam enabled)
                                    </div>
                                    <video autoplay muted playsinline id="video"></video>
                                    <div id="output" hidden>
                                        <div id="outputMessage">No QR code detected.</div>
                                    </div>
                                </center>
                            </div>
                        </div>
                        <div class="receiveReturn" style="width:100%; padding-left: 2%; padding-right: 2%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalQr">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3 style="background-color: #00a65a; padding-top: 2%; padding-bottom: 2%; font-weight: bold;">Scan
                            Slip Return</h3>
                    </center>
                </div>
                <div class="modal-body" style="padding-bottom: 75px;">
                    <div class="row">
                        <div class="col-xs-12">
                            <center>
                                <div id="div_qr_item">
                                    <input id="qr_item" type="text"
                                        style="border:0; width: 100%; text-align: center; color: #3c3c3c; font-size: 2vw;">
                                </div>
                            </center>
                        </div>
                        <div class="receiveReturn" style="width:100%; padding-left: 2%; padding-right: 2%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')
    <script src="<?php echo e(url('js/jquery.gritter.min.js')); ?>"></script>
    <script src="<?php echo e(url('js/dataTables.buttons.min.js')); ?>"></script>
    <script src="<?php echo e(url('js/buttons.flash.min.js')); ?>"></script>
    <script src="<?php echo e(url('js/jszip.min.js')); ?>"></script>
    <script src="<?php echo e(url('js/vfs_fonts.js')); ?>"></script>
    <script src="<?php echo e(url('js/buttons.html5.min.js')); ?>"></script>
    <script src="<?php echo e(url('js/buttons.print.min.js')); ?>"></script>
    <script src="<?php echo e(url('js/jquery.numpad.js')); ?>"></script>
    <script src="<?php echo e(url('js/jsQR.js')); ?>"></script>
    <script src="<?php echo e(url('js/exporting.js')); ?>"></script>
    <script src="<?php echo e(url('js/export-data.js')); ?>"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const ng_list_data = [];
	    const ngList = [];

        $.fn.numpad.defaults.gridTpl =
            '<table class="table modal-content" style="width: 37.5%; z-index: 1000; border: 2px solid grey;"></table>';
        $.fn.numpad.defaults.backgroundTpl = '<div class="modal-backdrop in"></div>';
        $.fn.numpad.defaults.displayTpl = '<input type="text" class="form-control" style="font-size:2vw; height: 50px;"/>';
        $.fn.numpad.defaults.buttonNumberTpl =
            '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
        $.fn.numpad.defaults.buttonFunctionTpl =
            '<button type="button" class="btn btn-default" style="font-size:2vw; width:100%;"></button>';
        $.fn.numpad.defaults.onKeypadCreate = function() {
            $(this).find('.del').addClass('btn-default');
            $(this).find('.clear').addClass('btn-default');
            $(this).find('.cancel').addClass('btn-default');
            $(this).find('.done').addClass('btn-success');
            $(this).find('.neg').addClass('btn-default');
            $('.neg').css('display', 'block');
        };

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
            
            ng_list_data.length = 0;

            fetchNGList();

            $('.select2').select2({
                allowClear: true,
            });
            $('#modalLocation').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('.numpad').numpad({
                hidePlusMinusButton: true,
                decimalSeparator: '.'
            });

            $('#ng_list').select2({
                placeholder : 'Select NG List',
                dropdownParent: $('.ng_list')			
            }).on('select2:open', function(){			
                $('.select2-dropdown--above').attr('id','s2dropdown');
                $('#s2dropdown').removeClass('select2-dropdown--above');
                $('#s2dropdown').addClass('select2-dropdown--below');
            });
        });

        $('#modalLocation').on('hidden.bs.modal', function() {
            $(".modal-backdrop").remove();
        });

        var video;

        function stopScan() {
            $('#modalScan').modal('hide');
        }

        function videoOff() {
            video.pause();
            video.src = "";
            video.srcObject.getTracks()[0].stop();
        }

        $("#modalScan").on('shown.bs.modal', function() {
            showCheck('123');
        });

        $('#modalScan').on('hidden.bs.modal', function() {
            videoOff();
            $('.receiveReturn').html("");
        });

        $('#modalQr').on('shown.bs.modal', function() {
            $('#qr_item').show();
            $('#qr_item').val('');
            $('#qr_item').focus();
        });

        $('#modalQr').on('hidden.bs.modal', function() {
            $('.receiveReturn').html("");
        });

        function showCheck(kode) {
            $(".modal-backdrop").add();
            $('#scanner').show();

            var vdo = document.getElementById("video");
            video = vdo;
            var tickDuration = 200;
            video.style.boxSizing = "border-box";
            video.style.position = "absolute";
            video.style.left = "0px";
            video.style.top = "0px";
            video.style.width = "400px";
            video.style.zIndex = 1000;

            var loadingMessage = document.getElementById("loadingMessage");
            var outputContainer = document.getElementById("output");
            var outputMessage = document.getElementById("outputMessage");

            navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: "environment"
                }
            }).then(function(stream) {
                video.srcObject = stream;
                video.play();
                setTimeout(function() {
                    tick();
                }, tickDuration);
            });

            function tick() {
                loadingMessage.innerText = "⌛ Loading video..."

                try {

                    loadingMessage.hidden = true;
                    video.style.position = "static";

                    var canvasElement = document.createElement("canvas");
                    var canvas = canvasElement.getContext("2d");
                    canvasElement.height = video.videoHeight;
                    canvasElement.width = video.videoWidth;
                    canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                    var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                    var code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert"
                    });
                    if (code) {
                        outputMessage.hidden = true;
                        videoOff();
                        receiveReturn(video, code.data);

                    } else {
                        outputMessage.hidden = false;
                    }
                } catch (t) {
                    console.log("PROBLEM: " + t);
                }

                setTimeout(function() {
                    tick();
                }, tickDuration);
            }
        }

        function plusCount() {
            $('#quantity').val(parseInt($('#quantity').val()) + 1);
        }

        function minusCount() {
            if ($('#quantity').val() > 0) {
                $('#quantity').val(parseInt($('#quantity').val()) - 1);
            } else {
                $('#quantity').val(0);
            }

            // $('#quantity').val(parseInt($('#quantity').val()) - 1);
        }

        $('#qr_item').keydown(function(event) {
            if (event.keyCode == 13 || event.keyCode == 9) {
                var qr_item = $('#qr_item').val();
                receiveReturn(video, qr_item);
            }
        });

        function receiveReturn(video, data) {
            $('#scanner').hide();
            $(".modal-backdrop").remove();

            var x = {
                id: data
            }
            $.get('<?php echo e(url('fetch/return')); ?>', x, function(result, status, xhr) {
                if (result.status) {
                    var location = $('#location').val();

                    if (result.return.issue_location == location) {
                        var re = "";

                        $('.receiveReturn').html("");
                        re += '<table style="text-align: center; width:100%;"><tbody>';

                        if (result.image_exist) {
                            re += '<tr>';
                            re += '<center>';
                            re += '<img style="max-width: 80%;" src="' + result.image +
                                '" onerror="this.onerror=null; this.src=""" alt="image_material">';
                            re += '</center>';
                            re += '</tr>';
                        }


                        re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">' + result.return
                            .material_number + '</td></tr>';
                        re += '<tr><td style="font-size: 36px; font-weight: bold;" colspan="2">' + result.return
                            .receive_location + ' -> ' + result.return.issue_location + '</td></tr>';
                        re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">' + result.return.material_description + '</td></tr>';                                                                     
                        
                        var ng_list = result.return.ng.split(',');

                        const ng_data = ng_list.map(ng => {
                            const [name, qty] = ng.split('_');
                            return `${name}(${qty})`;
                        });

                        const ng_lists = ng_data.join(', ');                    

                        re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">' + ng_lists + '</td></tr>';

                        re +=
                            '<tr><td style="font-size: 50px; font-weight: bold; background-color:black; color:white;" colspan="2">' +
                            result.return.quantity + ' PC(s)</td></tr>';
                        re += '<tr><td style="font-size: 26px; font-weight: bold;" colspan="2">' + result.return
                            .name + '</td></tr>';
                        re += '<tr>';
                        re += '<td><button id="reject+' + result.return.id +
                            '" class="btn btn-danger" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReceive(id)">TOLAK</button></td>';
                        re += '<td><button id="receive+' + result.return.id +
                            '" class="btn btn-success" style="width: 95%; font-size: 30px; font-weight:bold;" onclick="confirmReceive(id)">TERIMA</button></td>';
                        re += '</tr>';
                        re += '</tbody></table>';

                        $('.receiveReturn').append(re);
                        $('#qr_item').val('');
                        $('#qr_item').hide();
                    } else {
                        $('.receiveReturn').html("");
                        $('#qr_item').val('');
                        $('#qr_item').focus();

                        showCheck();
                        $('#loading').hide();
                        openErrorGritter('Error!', 'Lokasi Return Salah');
                    }

                } else {
                    $('.receiveReturn').html("");
                    $('#qr_item').val('');
                    $('#qr_item').focus();

                    showCheck();
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function confirmReceive(id) {
            $('#loading').show();
            var data = {
                id: id
            }
            $.post('<?php echo e(url('confirm/return')); ?>', data, function(result, status, xhr) {
                if (result.status) {
                    $('.receiveReturn').html("");
                    showCheck();

                    $('#qr_item').show();
                    $('#qr_item').focus();

                    $('#loading').hide();
                    openSuccessGritter('Success!', result.message);
                } else {
                    $('#qr_item').show();
                    $('#qr_item').focus();

                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        // function printReturn() {
        //     $('#loading').show();
        //     var material = $('#material_number').val();
        //     var issue = $('#issue').val();
        //     var receive = $('#receive').val();
        //     var description = $('#material_description').val();
        //     var quantity = $('#quantity').val();

        //     if (material == '') {
        //         $('#loading').hide();
        //         openErrorGritter('Error!', 'Pilih material yang akan di return');
        //         return false;
        //     }
        //     if (quantity == '' || quantity < 1) {
        //         $('#loading').hide();
        //         openErrorGritter('Error!', 'Isikan quantity yang akan di return');
        //         return false;
        //     }

        //     var data = {
        //         material: material,
        //         issue: issue,
        //         receive: receive,
        //         quantity: quantity,
        //         description: description
        //     }
        //     $.post('<?php echo e(url('print/return')); ?>', data, function(result, status, xhr) {
        //         if (result.status) {
        //             fetchResume(receive);
        //             $('#material_number').val("");
        //             $('#issue').val("");
        //             $('#receive').val("");
        //             $('#material_description').val("");
        //             $('#quantity').val(0);

        //             $('#loading').hide();
        //             openSuccessGritter('Success', result.message);
        //         } else {
        //             $('#loading').hide();
        //             openErrorGritter('Error!', result.message);
        //         }
        //     });
        // }

        function printReturn() {
           $('#loading').show();

            var material = $('#material_number').val();
            var description = $('#material_description').val();

            var issue = $('#issue').val();
            var receive = $('#receive').val();

            if(material == ''){
                $('#loading').hide();
                openErrorGritter('Error!', 'Pilih material yang akan di return');
                return false;
            }

            if(ngList.length == 0){
                $('#loading').hide();
                openErrorGritter('Error!', 'Pilih ng list yang akan di return');
                return false;
            }
            
            var ngList_data = ngList;
            var data = {
                material:material,
                description:description,
                issue:issue,
                receive:receive,
                return_list:ngList_data
            }

		$.post('<?php echo e(url("print/return")); ?>', data, function(result, status, xhr){
			if(result.status){
				fetchResume(receive);
				$('#material_number').val("");
				$('#issue').val("");
				$('#receive').val("");
				$('#material_description').val("");
				$('#ng_list').val('').trigger('change');
				$('#quantity').val(0);				
				ngList.splice(0, ngList.length);
				renderNgList();
				

				$('#loading').hide();
				openSuccessGritter('Success', result.message);
			}
			else{
				$('#loading').hide();
				openErrorGritter('Error!', result.message);
			}
		});
        }

        function fetchNGList(){
            $.get('{{ url('/fetch/return/ng_list') }}' , function(result, status, xhr){
                if(result.status){				
                    $.each(result.ng_lists, function(key, value){
                        ng_list_data.push(value);
                    });
                }			
            });		
        }

        function fetchReturn(id) {

            var material = $('#' + id).find('td').eq(1).text();
            var description = $('#' + id).find('td').eq(2).text();
            var issue = $('#' + id).find('td').eq(4).text();
            var receive = $('#' + id).find('td').eq(3).text();
            var quantity = $('#' + id).find('td').eq(5).text();

            $('#material_number').val(material);
            $('#material_description').val(description);
            $('#issue').val(issue);
            $('#receive').val(receive);
            if (quantity > 0) {
                $('#quantity').val(quantity);
            } else {
                $('#quantity').val(0);
            }

            var issue_location = issue.toString();
            var receive_location = receive.toString();

            $('#ng_list').html('<option value="">Select NG</option>');

            $('#ng_list').append('<option value="OK">OK</option>');
            // if (issue_location == receive_location){
            // }

            ng_list_data.forEach(function(value, key){
                // if(value.storage_location && value.storage_location.includes(issue_location)){                                
                    $('#ng_list').append('<option value="'+value.ng_name+'">'+value.ng_name+'</option>');
                // }
            });
        }

        function reprint(id) {
            var data = {
                id: id
            }
            $.get('<?php echo e(url('reprint/return')); ?>', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter('Success!', result.message);
                } else {
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function fetchResume(loc) {
            var data = {
                loc: loc
            }
            $.get('<?php echo e(url('fetch/return/resume')); ?>', data, function(result, status, xhr) {
                $('#tableBodyResume').html("");
                var tableData = "";
                var count = 1;
                $.each(result.resumes, function(key, value) {
                    tableData += '<tr>';
                    tableData += '<td>' + count + '</td>';
                    tableData += '<td>' + value.material_number + '</td>';
                    tableData += '<td>' + value.material_description + '</td>';
                    tableData += '<td>' + value.issue_location + '</td>';
                    tableData += '<td>' + value.receive_location + '</td>';
                    tableData += '<td>' + value.quantity + '</td>';
                    tableData += '<td>' + value.name + '</td>';
                    tableData += '<td>' + value.created_at + '</td>';
                    tableData += '<td><center><button class="btn btn-danger" onclick="deleteReturn(' + value
                        .id + ')"><i class="fa fa-trash"></i></button></center></td>';
                    tableData += '<td><center><button class="btn btn-primary" onclick="reprint(' + value
                        .id + ')"><i class="fa fa-print"></i></button></center></td>';
                    tableData += '</tr>';

                    count += 1;
                });
                $('#tableBodyResume').append(tableData);
            });
        }

        function deleteReturn(id) {

            if (confirm("Apa Anda yakin anda akan mendelete slip return?")) {
                var data = {
                    id: id
                }
                $.post('{{ url('delete/return') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        fetchResume(result.receive);
                        openSuccessGritter('Success!', result.message);
                    } else {
                        openErrorGritter('Error!', result.message);
                    }

                });
            } else {
                return false;
            }
        }

        function fetchReturnList(loc) {
            fetchResume(loc);
            $('#location').val(loc);
            var data = {
                loc: loc
            }
            $.get('<?php echo e(url('fetch/return/list')); ?>', data, function(result, status, xhr) {
                if (result.status) {
                    $('#tableList').DataTable().clear();
                    $('#tableList').DataTable().destroy();
                    $('#tableBodyList').html("");
                    var tableData = "";
                    var count = 1;
                    $.each(result.lists, function(key, value) {
                        var str = value.description;
                        var desc = str.replace("'", "");

                        var css = '';
                        if (value.receive_location == value.issue_location) {
                            css = 'style="background-color: #ccffff;"';
                        }
                        tableData += '<tr id="' + value.material_number + '_' + value.receive_location +
                            '_' + value.issue_location + '" onclick="fetchReturn(id)">';
                        tableData += '<td ' + css + '>' + count + '</td>';
                        tableData += '<td ' + css + '>' + value.material_number + '</td>';
                        tableData += '<td ' + css + '>' + desc + '</td>';
                        tableData += '<td ' + css + '>' + value.receive_location + '</td>';
                        tableData += '<td ' + css + '>' + value.issue_location + '</td>';
                        if (value.lot > 0) {
                            tableData += '<td ' + css + '>' + value.lot + '</td>';
                        } else {
                            tableData += '<td ' + css + '>-</td>';
                        }
                        tableData += '</tr>';

                        count += 1;
                    });
                    $('#tableBodyList').append(tableData);

                    $('#tableList tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="4"/>');
                    });

                    var tableList = $('#tableList').DataTable({
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
                        'pageLength': 20,
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

                    tableList.columns().every(function() {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('#tableList tfoot tr').appendTo('#tableList thead');
                    
                    $('#tableResume tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="4"/>');
                    });

                    var tableResume = $('#tableResume').DataTable({
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
                        'pageLength': 20,
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

                    tableResume.columns().every(function() {
                        var that = this;

                        $('input', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });

                    $('#tableResume tfoot tr').appendTo('#tableResume thead');


                    openSuccessGritter('Success!', result.message);
                    $('#modalLocation').modal('hide');
                } else {
                    openErrorGritter('Error!', result.message);
                }
            });

        }

        function addNGList(){
            var gmc = $('#material_number').val();
            var desc = $('#material_description').val();
            var qty = $('#quantity').val();
            var ng = $('#ng_list').val();

            if(gmc == "" || desc == "" || qty == "" || ng == ""){
                openErrorGritter('Error!', 'Please fill all fields.');
                return false;
            }
                    
            if(ngList.length > 0){
                if(ngList[0].gmc != gmc){
                    openErrorGritter('Error!', 'GMC harus sama.');
                    return false;
                }
            }
                    
            if(qty <= 0){
                openErrorGritter('Error!', 'Quantity harus lebih dari 0.');
                return false;
            }

            if(ngList.length > 0){
                var check = ngList.filter(function(value, key){
                    return value.gmc == gmc && value.ng == ng;
                });

                if(check.length > 0){				
                    var index = ngList.indexOf(check[0]);
                    var newQty = parseInt(check[0].qty) + parseInt(qty);
                    ngList[index].qty = newQty;
                    renderNgList();
                    $('#ng_list').val('').trigger('change');
                    $('#quantity').val(0);
                    return false;
                }
            }

            ngList.push({
                gmc:gmc,
                desc:desc,
                qty:qty,
                ng:ng
            });		

            $('#ng_list').val('').trigger('change');
            $('#quantity').val(0);

            renderNgList();
        }

        function renderNgList(){	
            var tableData = "";
            var index = 1;
            ngList.forEach(function(value, key){
                tableData += '<tr>';
                tableData += '<td>'+index+'</td>';
                tableData += '<td>'+value.gmc+'</td>';
                tableData += '<td>'+value.desc+'</td>';
                tableData += '<td><b>'+value.qty+'</b></td>';
                tableData += '<td><b>'+value.ng+'</b></td>';
                tableData += '<td><button class="btn btn-danger btn-xs" onclick="deleteNgList(\''+key+'\')"><i class="fa fa-trash"></i> Delete</button></td>';
                tableData += '</tr>';	
                index++;
            });

            $('#tableBodyNG').html(tableData);
        }

        function deleteNgList(key){
            ngList.splice(key, 1);
            renderNgList();
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '<?php echo e(url('images/image-screen.png')); ?>',
                sticky: false,
                time: '2000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '<?php echo e(url('images/image-stop.png')); ?>',
                sticky: false,
                time: '5000'
            });
        }
    </script>
@stop
