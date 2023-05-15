@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<style type="text/css">
thead>tr>th{
	text-align:center;
}
tbody>tr>td{
	text-align:center;
}
tfoot>tr>th{
	text-align:center;
}
td:hover {
	overflow: visible;
}
table.table-bordered{
	border:1px solid black;
}
table.table-bordered > thead > tr > th{
	border:1px solid black;
}
table.table-bordered > tbody > tr > td{
	border:1px solid rgb(211,211,211);
	padding-top: 0px;
	padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
	border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }

.containers {
  display: block;
  position: relative;
  /*padding-left: 20px;*/
  margin-bottom: 6px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.containers input {
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
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.containers:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.containers input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.containers input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.containers .checkmark:after {
 	top: 9px;
	left: 9px;
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: white;
}

#tableCheck > tbody > tr > td > p > img {
	width: 250px !important;
}
</style>
@stop
@section('header')
<section class="content-header" >
	<h1>
		{{ $page }}<span class="text-purple"> {{ $title_jp }}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content" >
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<p style="position: absolute; color: white; top: 45%; left: 35%;">
			<span style="font-size: 40px">Please wait <i class="fa fa-spin fa-refresh"></i></span>
		</p>
	</div>
	<div class="row">
		<div class="col-xs-8 col-xs-offset-2">
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-12" style="margin-top: 10px;padding-right: 0px;padding-left: 0px;background-color: lightskyblue;text-align: center;border: 1px solid black;">
						<span style="font-weight: bold;font-size: 20px;">PENGADUAN JARINGAN MIS</span>
					</div>
					<table style="width: 100%;border:1px solid black">
						<tr>
							<td style="font-weight: bold;border:1px solid black;width: 2%;font-size: 15px;background-color: #cddc39;text-align: left;padding-left: 7px;">Employee ID</td>
							<td style="border:1px solid black;font-size: 18px;width: 5%;text-align: left;padding-left: 7px;" id="complaint_id">{{$emp->employee_id}}</td>
						</tr>
						<tr>
							<td style="font-weight: bold;border:1px solid black;width: 2%;font-size: 15px;background-color: #cddc39;text-align: left;padding-left: 7px;">Nama</td>
							<td style="border:1px solid black;font-size: 18px;text-align: left;padding-left: 7px;" id="complaint_name">{{$emp->name}}</td>
						</tr>
						<tr>
							<td style="font-weight: bold;border:1px solid black;width: 2%;font-size: 15px;background-color: #cddc39;text-align: left;padding-left: 7px;">Lokasi</td>
							<td style="border:1px solid black;font-size: 18px;text-align: left;padding-left: 7px;" id="location">{{strtoupper(str_replace('_',' ',$loc))}}</td>
						</tr>
					</table>
					<div class="col-xs-12" style="margin-top: 10px;padding-right: 0px;padding-left: 0px">
						<label>Detail Pengaduan</label><br>
						<textarea id="detail_complaint"></textarea>
					</div>
					<div class="col-xs-12" style="margin-top: 10px;padding-right: 0px;padding-left: 0px">
						<label>Image Evidence</label> <b><i class="text-red">(Optional)</i></b><br>
						<input type="file" name="evidence" id="evidence" class="form-control" accept="image/*" capture="environment" style="width: 100%" onchange="readURL(this);">
						<br>
						<img width="100%" id="blah" src="" style="display: none" alt="your image" />
					</div>
					<div class="col-xs-6" style="margin-top: 10px;padding-right: 5px;padding-left: 0px">
						<button class="btn btn-danger" onclick="cancelAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
							CANCEL
						</button>
					</div>
					<div class="col-xs-6" style="margin-top: 10px;padding-right: 0px;padding-left: 5px">
						<button class="btn btn-success" onclick="confirmAll()" style="width: 100%;font-size: 25px;font-weight: bold;">
							SAVE
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="scanModal">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<div class="modal-header no-padding">
					<h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
						SCAN LOKASI
					</h4>
				</div>
				<div class="modal-body table-responsive">
					<div id='scanner' class="col-xs-12">
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

                    <p style="visibility: hidden;">camera</p>
                    <input type="hidden" id="code">
				</div>
			</div>
		</div>
	</div>

</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url('js/jsQR.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	$.fn.modal.Constructor.prototype.enforceFocus = function() {
      modal_this = this
      $(document).on('focusin.modal', function (e) {
        if (modal_this.$element[0] !== e.target && !modal_this.$element.has(e.target).length 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_select') 
        && !$(e.target.parentNode).hasClass('cke_dialog_ui_input_text')) {
          modal_this.$element.focus()
        }
      })
    };

    var count_point = 0;

    function readURL(input) {
      if (input.files && input.files[0]) {
          var reader = new FileReader();

          reader.onload = function (e) {
            $('#blah').show();
              $('#blah')
                  .attr('src', e.target.result);
          };

          reader.readAsDataURL(input.files[0]);
      }
    }

	jQuery(document).ready(function() {
		if ('{{$loc}}' == '000') {
			$('#scanModal').modal({
				backdrop: 'static',
				keyboard: false
			});
		}
		$('.select2').select2({
	    	allowClear:true
	    });
	    CKEDITOR.replace('detail_complaint' ,{
	        filebrowserImageBrowseUrl : '{{ url('kcfinder_master') }}',
	        height: '200px',
	        toolbar:'MA'
	    });
		CKEDITOR.config.toolbar_MA=[ ['Bold','Italic','Underline','Image'] ];

      $('body').toggleClass("sidebar-collapse");
	});

	function stopScan() {
        $('#scanModal').modal('hide');
    }

    function videoOff() {
        video.pause();
        video.src = "";
        video.srcObject.getTracks()[0].stop();
    }

    $("#scanModal").on('shown.bs.modal', function() {
        showCheck('123');
    });

    $('#scanModal').on('hidden.bs.modal', function() {
        videoOff();
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
                    // document.getElementById('qr_code').value = code.data;
                    console.log(code.data);
                    window.location.replace(code.data);

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

	function cancelAll() {
		if (confirm('Apakah Anda yakin membatalkan pengisian?')) {
			window.location.replace("{{url('')}}");
		}
	}

    const monthNames = ["January", "February", "March", "April", "May", "June",
	  "July", "August", "September", "October", "November", "December"
	];

	function confirmAll() {
		if (confirm('Apakah Anda yakin menyelesaikan penanganan?')) {
			$('#loading').show();
			
			var complaint_id = $('#complaint_id').text();
			var complaint_name = $('#complaint_name').text();
			var detail_complaint = CKEDITOR.instances['detail_complaint'].getData();
			var location = '{{$loc}}';

			var file = '';

			var fileData = null;

			fileData = $('#evidence').prop('files')[0];

			file=$('#evidence').val().replace(/C:\\fakepath\\/i, '').split(".");

			var formData = new FormData();
			formData.append('complaint_id',complaint_id);
			formData.append('complaint_name',complaint_name);
			formData.append('detail_complaint',detail_complaint);
			formData.append('location',location);
			formData.append('fileData', fileData);
			formData.append('extension', file[1]);
			formData.append('foto_name', file[0]);

			$.ajax({
				url:"{{ url('input/mis/complaint') }}",
				method:"POST",
				data:formData,
				dataType:'JSON',
				contentType: false,
				cache: false,
				processData: false,
				success:function(data)
				{
					if (data.status) {
						openSuccessGritter('Success!',"Pengaduan Berhasil Disimpan");
						$('#loading').hide();
						alert('Pengaduan Berhasil Disimpan');
						window.location.replace("{{url('')}}");
					}else{
						openErrorGritter('Error!',data.message);
						audio_error.play();
						$('#loading').hide();
					}

				}
			});
		}
	}

	var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

	function openErrorGritter(title, message) {
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-danger',
			image: '{{ url("images/image-stop.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function openSuccessGritter(title, message){
		jQuery.gritter.add({
			title: title,
			text: message,
			class_name: 'growl-success',
			image: '{{ url("images/image-screen.png") }}',
			sticky: false,
			time: '2000'
		});
	}

	function addZero(number) {
		return number.toString().padStart(2, "0");
	}
</script>
@endsection