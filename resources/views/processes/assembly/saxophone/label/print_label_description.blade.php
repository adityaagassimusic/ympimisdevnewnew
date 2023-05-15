<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body >
	<style type="text/css">
		table {
			border-collapse: collapse;
		}
		.name {
			
			font-family: 'arial';
			
		}


	</style>

@php
include(app_path() . '\barcode\barcode.php');
@endphp

<table border="0" >	
	@foreach($barcode as $nomor => $barcode)	
	<input type="text" name="codemodel" id="codemodel" value="{{$barcode->model}}" hidden="">
	@endforeach	
	<tr>
		<td class="name" align="center" id="model" style="font-size: 48pt; -moz-transform:scale(1,1.6);">YAS-280//ID </td>
	</tr>	
</table>
</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
	jQuery(document).ready(function() {		
		day();		
    defineCustomPaperSize();
		printWindow(window, 'Label Besar');

				
	});
	function day() {
		var models = $('#codemodel').val();
		$('#model').text(models);		
		var panjang = (models.length - 11)*2;
		var	ukuran = 48;

    if (models == "YAS-VDHMII//ID") {
      $('#model').css({"font-size":"34pt", "-moz-transform":"scale(1,2)","padding-top":"5px"});
      alert(models);
    }

    if (models == "YAS-PLU1II//02 ID") {
      $('#model').css({"font-size":"30pt", "-moz-transform":"scale(1,2.1)","padding-top":"5px"});
    }

    if (models == "YTS-PLU1II//02 ID") {
      $('#model').css({"font-size":"30pt", "-moz-transform":"scale(1,2.1)","padding-top":"8px","padding-left":"10px"});
    }

		if (models.length > 11 && models != "YAS-PLU1II//02 ID" && models != "YAS-VDHMII//ID" && models != "YTS-PLU1II//02 ID") {
			$('#model').css({"font-size":"38pt", "-moz-transform":"scale(1,2)","padding-top":"5px"});
		}		


	}
	


// default print settings
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
  "marginLeft": 1,
  "marginRight": 0,
  "marginTop": 4,
  "marginBottom": 0,
  "scaling": 1,
  "title": "",
  "docURL": "",
  "headerStrLeft": "",
  "headerStrCenter": "",
  "headerStrRight": "",
  "footerStrLeft": "",
  "footerStrCenter": "",
  "footerStrRight": "",
   "printerName" : "SATO LC408e" 
};

function defineCustomPaperSize() {
  console.log("Define custom paper size", false);
  jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 80.0, 10.0, jsPrintSetup.kPaperSizeInches);
  // w, h
  console.log(JSON.stringify(jsPrintSetup.getPaperSizeDataByID(101), null, "\t"), true);
}

function printWindow(win, what) {
  // jsPrintSetup messages  
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
	day();		
   	defineCustomPaperSize();
  		console.log("before print: "+what, true);
  	});
  	win.addEventListener("afterprint", function(event) {
  		console.log("after print: "+what, true);
  		
  		window.close();

  	});
  }
  
  win.jsPrintSetup.print(printSettings).then(
  	(jobId) => {
  		console.log(what+" Print job for submitted with id:"+jobId);
  		console.log(what+" Print job for submitted with id:"+jobId, true);
  		checkJobInfo(what, win,jobId);

  		setTimeout(() => {checkJobInfo(what, win, jobId);}, 5000);
  	}
  	, (err) => {
  		console.log(what+" Pint job rejected:"+err);
  		console.log(what+" Pint job rejected:"+err, true);
  	}
  	);
} 

function checkJobInfo(what, win, jobId) {
	var jobInfo = win.jsPrintSetup.getJobInfo(jobId);
	console.log(what+ " Async Checking Ifo for Job:"+jobId, true);
	if (jobInfo) {
  //  jobInfo_1 = JSON.parse(jobInfo);
  console.log("----- job info:"+JSON.stringify(jobInfo));
  console.log(JSON.stringify(jobInfo, null, "\t"), true);
} else {
	console.log("----- Can't find jobInfo for jobId:"+jobId);
	console.log("Can't find jobInfo for jobId:"+jobId, true);
}
}

</script>