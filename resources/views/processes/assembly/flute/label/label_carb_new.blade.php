<!DOCTYPE html>
<html>
<head>
</head>
<body >
	<style type="text/css">
		table {
			border-collapse: collapse;
			font-family: 'arial';
			font-size: 7.4pt;
			font-weight: bold;
			/*-moz-transform : scale(0.85, 1);*/
		}

		table, tr, td {
			padding: 0px;
		}

		#case{
			border: 1.5px solid black;
			-moz-transform : scale(1,0.9);
			padding-top: 2%;		
			padding-bottom: 2%;		
			padding-right: 2%;		
			padding-left: 2%;		
		}
	</style>

	@php
	include(app_path() . '\barcode\barcode.php');
	@endphp

	@foreach($date as $dt) 
	<input type="text" name="tgl" id="tgl" value="{{$dt->tgl}}" hidden="">
	@endforeach 

	<table border="0">
		<tr>
			<td align="left">Yamaha Corporation</td>
		</tr>
		<tr>
			<td align="left">Complies with California 93120 Phase 2 and TSCA Title VI</td>
		</tr>
		<tr>
			<td align="left">Conforme en California 93120 Phase 2 et TSCA Titre VI</td>
		</tr>
		<tr>
			<td align="left" id="tgl_text">Date: 07-2020</td>
		</tr>
		<tr>
			<td align="left">Fabricator / Fabricant: 0000042</td>
		</tr>
	</table>

	<table>
		<tr>
			<td style="position: fixed; left: 135px; top: 75px;" id='case'>
				<span style="font-weight: normal;">FOR CASE ONLY</span><br>
				<span style="font-weight: normal;">POUR ÉTUIS SEULEMENT</span>
			</td>
		</tr>
	</table>

</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {		
		setAttr();
		
		defineCustomPaperSize();
		setTimeout(function() {
			printWindow(window, 'Label Besar');
		}, 3000)


	});

	
	function setAttr() {
		var tgl = $('#tgl').val();
		$('#tgl_text').text("Date: " + tgl);
	}
	
	var printSettings = {
		"printSilent": true,
		"shrinkToFit": true,
		"unwriteableMarginLeft": 0,
		"unwriteableMarginRight": 0,
		"unwriteableMarginTop": 0,
		"unwriteableMarginBottom": 0,
		"edgeLeft": 0,
		"edgeRight": 0,
		"edgeTop": 0,
		"edgeBottom": 0,
		"marginLeft": -2.3,
		"marginRight": 0,
		"marginTop": 0.5,
		"marginBottom": 0,
		"title": "",
		"docURL": "",
		"headerStrLeft": "",
		"headerStrCenter": "",
		"headerStrRight": "",
		"footerStrLeft": "",
		"footerStrCenter": "",
		"footerStrRight": "",
		"printerName" : "SATO CG408DT (Copy 1)"
	};

	function tutup() {
		window.close();
	}

	function defineCustomPaperSize() {
		console.log("Define custom paper size", false);
		jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 76, 35, jsPrintSetup.kPaperSizeInches);
		console.log(JSON.stringify(jsPrintSetup.getPaperSizeDataByID(101), null, "\t"), true);
	}

	function printWindow(win, what) {
		function jspListener(event) {
			console.log('event.data:'+JSON.stringify(event.data));
			if (event.source == win 
				&& event.data.source && event.data.source == "jsPrintSetup"
				) {
				if (event.data.message == "job_start") {  
					console.log(what+" Job "+event.data.jobId+" started");
					console.log(what+" Job "+event.data.jobId+" started", true);
				} else if (event.data.message == "job_progress") {
					console.log(what+" Job "+event.data.jobId+" progress:"+event.data.progress);
					console.log(what+" Job "+event.data.jobId+" progress:"+event.data.progress, true);
				} else if (event.data.message == "job_error") {
					console.log(what+" Job "+event.data.jobId+" error:"+event.data.statusMessage);
					console.log(what+" Job "+event.data.jobId+" error:"+event.data.statusMessage, true);
				} else if (event.data.message == "job_rejected") {
					console.log(what+" Job "+event.data.jobId+" rejected.");
					console.log(what+" Job "+event.data.jobId+" rejected.", true);
				} else if (event.data.message == "job_submited") {
					console.log(what+" Job "+event.data.jobId+" submited.");
					console.log(what+" Job "+event.data.jobId+" submited.", true);
				} else if (event.data.message == "job_complete") {
					console.log(what+" Job "+event.data.jobId+" completed.");
					console.log(what+" Job "+event.data.jobId+" completed.", true);
				} else if (event.data.message == "jsp_permission") {
					console.log(what+" jsPrintSetup accessEnabled:"+event.data.accessEnabled+" permission:"+event.data.permission);
					console.log(what+" jsPrintSetup accessEnabled:"+event.data.accessEnabled+" permission:"+event.data.permission, true);
				} else {
					console.log(what+" Unknown message:"+event.data.message);
					console.log(what+" Unknown message:"+event.data.message, true);
				}
			}
		}

		if (typeof(win.privListenersAdded) === "undefined") {
			win.privListenersAdded = true;    
			win.addEventListener("message", jspListener);

			win.addEventListener("beforeprint", function(event) {
				setAttr();

				console.log("before print: "+what, true);
			});

			win.addEventListener("afterprint", function(event) {
				console.log("after print: "+what, true);
				var sn = $('#codesn').val();
				var rem = $('#rem').val();

				console.log(rem);

				window.open('{{ url("index/fl_label_carb2") }}'+'/{{$sn}}', '_blank');

				setTimeout(tutup,1000);	
				

			});
		}

		win.jsPrintSetup.print(printSettings).then(
			(jobId) => {
				console.log(what+" Print job for submitted with id:"+jobId);
				console.log(what+" Print job for submitted with id:"+jobId, true);
				checkJobInfo(what, win,jobId);
				setTimeout(() => {checkJobInfo(what, win, jobId);}, 5000);
			},
			(err) => {
				console.log(what+" Pint job rejected:"+err);
				console.log(what+" Pint job rejected:"+err, true);
			}
			);
	} 

	function checkJobInfo(what, win, jobId) {
		var jobInfo = win.jsPrintSetup.getJobInfo(jobId);
		console.log(what+ " Async Checking Ifo for Job:"+jobId, true);
		if (jobInfo) {
			console.log("----- job info:"+JSON.stringify(jobInfo));
			console.log(JSON.stringify(jobInfo, null, "\t"), true);
		} else {
			console.log("----- Can't find jobInfo for jobId:"+jobId);
			console.log("Can't find jobInfo for jobId:"+jobId, true);
		}
	}

</script>