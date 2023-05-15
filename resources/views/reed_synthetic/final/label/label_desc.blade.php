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

    <table border="0" id="tabel">
        <input type="text" name="codemodel" id="codemodel" value="{{ str_replace(' ', '&nbsp;', $material->material_description) }}" hidden="">
        <tr>
            <td class="name" align="center" id="model" style="font-size: 40pt; -moz-transform:scale(1,1.6);">YFL412</td>
        </tr> 
    </table>
</body>
</html>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
    jQuery(document).ready(function() {   
        initializePage();    
        defineCustomPaperSize();
        printWindow(window, 'Label Besar');
    });

    function initializePage() {
        var models = $('#codemodel').val();
        $('#model').text(models);   
        var panjang = (models.length - 11)*2;
        var ukuran = 48;
        
        if (models.length <= 11) {
            $('#model').css({"font-size":"42pt", "-moz-transform":"scale(1.35,1.4)","padding-top":"5px","padding-left":"45px"});
        }

        if (models.length == 12) {
            $('#model').css({"font-size":"38pt", "-moz-transform":"scale(1,1)","padding-top":"5px","padding-left":"5px"});
        }

        if (models.length == 13) {
            $('#model').css({"font-size":"38pt", "-moz-transform":"scale(1,1)","padding-top":"5px"});
        }

        if (models.length == 14) {
            $('#model').css({"font-size":"34pt", "-moz-transform":"scale(1,1)","padding-top":"10px"});
        }

        if (models.length == 15) {
            if(models.includes("PROTO")){
                $('#model').css({"font-size":"32pt", "-moz-transform":"scale(0.9,1.1)","padding-top":"13px"});
            }else{
                $('#model').css({"font-size":"32pt", "-moz-transform":"scale(1,1)","padding-top":"10px"});
            }
        }

        if (models.length == 16) {
            $('#model').css({"font-size":"30pt", "-moz-transform":"scale(1,1)","padding-top":"10px"});
        }

        if (models.length == 17) {
            $('#model').css({"font-size":"28pt", "-moz-transform":"scale(1,1)","padding-top":"13px"});
        }

        if(models.includes("WOCC")){
            $('#model').css({"font-size":"36pt", "-moz-transform":"scale(0.7,1.1)","padding-top":"8px"});
            $('#model').css({"position":"fixed", "left":"-28px", "top":"10px"});
            $('#tabel').css({"position":"fixed", "left":"-25px", "top":"6px", "width":"28%"});
        }

        if(models.includes("WOB")){
            $('#model').css({"font-size":"36pt", "-moz-transform":"scale(0.9,1.1)","padding-top":"7px"});
            $('#tabel').css({"position":"fixed", "left":"0px", "top":"6px"});
        }

        if (models.length > 17) {
            $('#model').css({"font-size":"34pt", "-moz-transform":"scale(0.9,1.2)","padding-top":"8px"});
        }

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
        "marginLeft": 1,
        "marginRight": 0,
        "marginTop": 3,
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

    function tutup() {
        window.close();
    }

    function defineCustomPaperSize() {
        console.log("Define custom paper size", false);
        jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 99, 29, jsPrintSetup.kPaperSizeInches);
        console.log(JSON.stringify(jsPrintSetup.getPaperSizeDataByID(101), null, "\t"), true);
    }

    function printWindow(win, what) {
        function jspListener(event) {
            console.log('event.data:'+JSON.stringify(event.data));
            if (event.source == win && event.data.source && event.data.source == "jsPrintSetup") {
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
                console.log("before print: "+what, true);
            });
            win.addEventListener("afterprint", function(event) {
                console.log("after print: "+what, true);

                setTimeout(tutup,1000);
                
            });
        }

        win.jsPrintSetup.print(printSettings).then((jobId) => {
            console.log(what+" Print job for submitted with id:"+jobId);
            console.log(what+" Print job for submitted with id:"+jobId, true);
            checkJobInfo(what, win,jobId);

            setTimeout(() => {checkJobInfo(what, win, jobId);}, 5000);
        }, (err) => {
            console.log(what+" Pint job rejected:"+err);
            console.log(what+" Pint job rejected:"+err, true);
        });
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