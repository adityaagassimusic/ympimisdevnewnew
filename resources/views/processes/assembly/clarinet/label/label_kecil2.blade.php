<!DOCTYPE html>
<html>

<head>
    <title></title>
</head>

<body>
    <style type="text/css">
        .product {
            font-size: 9pt;
            font-family: 'arial';
            margin: 0px;
            position: fixed;
            top: 6px;
            left: -12px;
            right: 0px;
            height: 50px;

        }

        .crop {
            position: fixed;
            top: 20px;
            left: 4px;
        }

        .kiri {
            font-weight: bold;
            font-size: 8pt;
            font-family: 'arial';
            position: fixed;
            top: 59px;
            left: -12px;
            right: 0px;
            height: 50px;
            margin: 0px;
        }

        .bawah {
            font-size: 9.3pt;
            font-family: 'arial';
            margin: 0px;
            position: fixed;
            top: 73px;
            left: 70px;
            right: 0px;
            height: 50px;
        }

        td {
            padding: 0px;
            margin: 0px;
            vertical-align: text-bottom;
        }
    </style>


    @php
        include app_path() . '\barcode\barcode.php';
    @endphp


    <table border="0" style="margin: 0px">
        <input type="text" name="rem" id="rem" value="{{ $remark }}" hidden="">
        <input type="text" name="codemodel" id="codemodel" value="{{ $sn }}" hidden="">


        @foreach ($barcode as $nomor => $barcode)
            <input type="text" name="codeday" id="codeday" value="{{ $barcode->date_code }}" hidden="">
        @endforeach

        @foreach ($des as $nomor => $des)
            <input type="text" name="des" id="des" value="{{ $des->model }}" hidden="">
        @endforeach
        <tr>
            <td align="left" colspan="2">
                <p class="product">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a style="font-weight: bold">CL</a></p>
                <img class="crop" id="128" src="">
                <p class="kiri">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SER.&nbsp;&nbsp;<a id="serial">N85550</a></p>
            </td>

        </tr>
        <tr>
            <td width="57%"></td>
            <td width="48%">
                <p class="bawah" id="day"></p>
            </td>
        </tr>

    </table>

</body>

</html>
<script src="{{ url('bower_components/jquery/dist/jquery.min.js') }}"></script>
<script>
    jQuery(document).ready(function() {
        jan();
        defineCustomPaperSize();
        setTimeout(function() {
            printWindow(window, 'Label Kecil');
        }, 2000)
    });

    function jan() {
        var jan = $('#codemodel').val();

        if (jan[0] + '' + jan[1] == '21') {
            var sn = jan.substr(2, 6);
        } else {
            var sn = jan;
        }

        var url1 = "{{ url('/app/barcode/') }}";
        var url2 = "/barcode.php?f=svg&s=code-128&w=110&h=40&p=0&wq=0";
        var code = "&d=" + jan;
        var janfix = url1.replace("/public", "");
        $("#128").attr("src", janfix + url2 + code);
        var day = $('#codeday').val();
        var des = $('#des').val();
        $('#day').text(day);
        $('#dest').text(des);
        if (des == 'YFL-312YTBL-JT1935') {
            document.getElementById('dest').style.fontSize = '8pt';
        }
        if (des == 'YFL-272SLVDHM//ID') {
            document.getElementById('dest').style.fontSize = '8.5pt';
        }
        if (des == 'YFL-212SLVDHM//ID') {
            document.getElementById('dest').style.fontSize = '8.5pt';
        }

        if (jan[0] + '' + jan[1] == '21') {
            $('#serial').text('(21)' + sn.toUpperCase());
        } else {
            $('#serial').text(sn.toUpperCase());
        }
    }

    function defineCustomPaperSize() {
        console.log("Define custom paper size", false);
        jsPrintSetup.definePaperSize(101, 101, 'Custom Size 1', 'Custom Size 1', 'My Test Custom Size 1', 33, 25,
            jsPrintSetup.kPaperSizeInches);
        console.log(JSON.stringify(jsPrintSetup.getPaperSizeDataByID(101), null, "\t"), true);
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
        "marginLeft": -0.5,
        "marginRight": 0,
        "marginTop": -0.5,
        "marginBottom": 0,
        // "numCopies": 2,
        // "scaling": 1,
        "title": "",
        "docURL": "",
        "headerStrLeft": "",
        "headerStrCenter": "",
        "headerStrRight": "",
        "footerStrLeft": "",
        "footerStrCenter": "",
        "footerStrRight": "",
        "printerName": "SATO CG408DT"
    };

    function tutup() {
        window.close();
    }

    function label_carb() {
        var sn = $('#codemodel').val();
        window.open('{{ url('index/assembly/clarinet/label_carb') }}' + '/' + sn, '_blank');
        window.close();
    }


    function printWindow(win, what) {
        function jspListener(event) {
            console.log('event.data:' + JSON.stringify(event.data));
            if (event.source == win &&
                event.data.source && event.data.source == "jsPrintSetup"
            ) {
                if (event.data.message == "job_start") {
                    console.log(what + " Job " + event.data.jobId + " started");
                    console.log(what + " Job " + event.data.jobId + " started", true);
                } else if (event.data.message == "job_progress") {
                    console.log(what + " Job " + event.data.jobId + " progress:" + event.data.progress);
                    console.log(what + " Job " + event.data.jobId + " progress:" + event.data.progress, true);
                } else if (event.data.message == "job_error") {
                    console.log(what + " Job " + event.data.jobId + " error:" + event.data.statusMessage);
                    console.log(what + " Job " + event.data.jobId + " error:" + event.data.statusMessage, true);
                } else if (event.data.message == "job_rejected") {
                    console.log(what + " Job " + event.data.jobId + " rejected.");
                    console.log(what + " Job " + event.data.jobId + " rejected.", true);
                } else if (event.data.message == "job_submited") {
                    console.log(what + " Job " + event.data.jobId + " submited.");
                    console.log(what + " Job " + event.data.jobId + " submited.", true);
                } else if (event.data.message == "job_complete") {
                    console.log(what + " Job " + event.data.jobId + " completed.");
                    console.log(what + " Job " + event.data.jobId + " completed.", true);
                } else if (event.data.message == "jsp_permission") {
                    console.log(what + " jsPrintSetup accessEnabled:" + event.data.accessEnabled + " permission:" +
                        event.data.permission);
                    console.log(what + " jsPrintSetup accessEnabled:" + event.data.accessEnabled + " permission:" +
                        event.data.permission, true);
                } else {
                    console.log(what + " Unknown message:" + event.data.message);
                    console.log(what + " Unknown message:" + event.data.message, true);
                }
            }
        }
        if (typeof(win.privListenersAdded) === "undefined") {
            win.privListenersAdded = true;
            win.addEventListener("message", jspListener);

            win.addEventListener("beforeprint", function(event) {
                jan();
                console.log("before print: " + what, true);
            });
            win.addEventListener("afterprint", function(event) {
                console.log("after print: " + what, true);

                var model = $('#des').val();
                var rem = $('#rem').val();

                // VAM5150	YCL-255ES//ID
                // WZ00120	YCL-200ADII//ID
                // WZ00160	YCL-255//U ID
                // WZ44910	YCL-255//ID
                // WZ00180	YCL-255S//ID
                // WZ00170	YCL-255E//ID

                const carb_model = ["YCL-255ES//ID", "YCL-255S//ID", "YCL-255//ID"];

                if (rem == "P" && carb_model.includes(model)) {
                    setTimeout(label_carb, 1000);
                } else {
                    setTimeout(tutup, 1000);
                }

                setTimeout(tutup, 1000);
            });
        }

        win.jsPrintSetup.print(printSettings).then(
            (jobId) => {
                console.log(what + " Print job for submitted with id:" + jobId);
                console.log(what + " Print job for submitted with id:" + jobId, true);
                checkJobInfo(what, win, jobId);

                setTimeout(() => {
                    checkJobInfo(what, win, jobId);
                }, 5000);
            }, (err) => {
                console.log(what + " Pint job rejected:" + err);
                console.log(what + " Pint job rejected:" + err, true);
            }
        );
    }

    function checkJobInfo(what, win, jobId) {
        var jobInfo = win.jsPrintSetup.getJobInfo(jobId);
        console.log(what + " Async Checking Ifo for Job:" + jobId, true);
        if (jobInfo) {
            console.log("----- job info:" + JSON.stringify(jobInfo));
            console.log(JSON.stringify(jobInfo, null, "\t"), true);
        } else {
            console.log("----- Can't find jobInfo for jobId:" + jobId);
            console.log("Can't find jobInfo for jobId:" + jobId, true);
        }
    }
</script>
