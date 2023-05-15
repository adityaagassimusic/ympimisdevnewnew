<!DOCTYPE html>
<html>

<head>
    <style type="text/css">
        * {
            font-family: Sans-serif;
        }

        td {
            padding-right: 5px;
            padding-left: 5px;
            padding-top: 0px;
            padding-bottom: 0px;
        }

        th {
            padding-right: 5px;
            padding-left: 5px;
        }

        .eo_number {
            font-size: 4vw;
            font-weight: bold;
        }

        .status {
            font-size: 20pt;
            font-weight: bold;
            color: red;
            margin-bottom: 0px;
        }

        .message {
            font-size: 16pt;
            margin-top: 0px;
        }
    </style>
</head>

<body>
    <div>
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
                alt=""><br>
            <p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>
            <br>
            <p class="status"></i>REMINDER EXTRA ORDER SENDING APPLICATION!</p>
            <p class="message">Please execute it as soon as possible</p>
        </center>
        <br>
        <div style="width: 87%; margin: auto;">
            <table style="border:1px solid black; border-collapse: collapse; width: 100%; font-size: 10pt;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #63ccff;">Send App No.</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Status</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Attention</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Condition</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Ship By</th>
                        <th style="border:1px solid black; background-color: #63ccff;">PIC</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Waiting From</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($data['waiting']); $i++) {
                        print_r('<tr>');
                        print_r('<td style="border: 1px solid black; width: 2%; text-align: center;">' . $data['waiting'][$i]->send_app_no . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['waiting'][$i]->status . '</td>');
                        print_r('<td style="border: 1px solid black; width: 2%; text-align: left;">' . $data['waiting'][$i]->attention . '<br>' . $data['waiting'][$i]->destination_shortname . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['waiting'][$i]->condition . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['waiting'][$i]->shipment_by . '</td>');
                        print_r('<td style="border: 1px solid black; width: 2%; text-align: center;">' . $data['waiting'][$i]->pic . '</td>');
                        print_r('<td style="border: 1px solid black; width: 2%; text-align: center;">' . $data['waiting'][$i]->created_at . '</td>');
                        print_r('</tr>');
                    } ?>
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <center>
            <a href="{{ url('index/extra_order/sending_application') }}">
                &#10148; Click here to check the sending application
            </a>
        </center>
    </div>
</body>

</html>
