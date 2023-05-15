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
            color: #63ccff;
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
            <p class="status"></i>NEW PO FOR EXTRA ORDER!</p>
            <p class="message">Please prepare the production schedules</p>
        </center>
        <br>
        <div style="width: 87%; margin: auto;">
            <table style="border:1px solid black; border-collapse: collapse; width: 100%; font-size: 10pt;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #aee571;">EO Number</th>
                        <th style="border:1px solid black; background-color: #aee571;">Dest.</th>
                        <th style="border:1px solid black; background-color: #aee571;">Material</th>
                        <th style="border:1px solid black; background-color: #aee571;">Description</th>
                        <th style="border:1px solid black; background-color: #aee571;">SLoc</th>
                        <th style="border:1px solid black; background-color: #aee571;">Prod. Date</th>
                        <th style="border:1px solid black; background-color: #aee571;">St. Date</th>
                        <th style="border:1px solid black; background-color: #aee571;">Qty</th>
                        <th style="border:1px solid black; background-color: #aee571;">BOM</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($data['extra_order_detail']); $i++) {
                        print_r('<tr>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['extra_order_detail'][$i]->eo_number . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['extra_order']->destination_shortname . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['extra_order_detail'][$i]->material_number . '</td>');
                        print_r('<td style="border: 1px solid black; width: 15%; text-align: left;">' . $data['extra_order_detail'][$i]->description . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['extra_order_detail'][$i]->storage_location . '</td>');
                        print_r('<td style="border: 1px solid black; width: 5%; text-align: center;">' . $data['extra_order_detail'][$i]->due_date . '</td>');
                        print_r('<td style="border: 1px solid black; width: 5%; text-align: center;">' . $data['extra_order_detail'][$i]->request_date . '</td>');
                        print_r('<td style="border: 1px solid black; width: 2%; text-align: right;">' . $data['extra_order_detail'][$i]->quantity . '</td>');
                        if ($data['extra_order_detail'][$i]->is_completion == '1') {
                            print_r('<td style="border: 1px solid black; width: 5%; text-align: center;"><a style="background-color: #ffe84e; text-decoration: none; color: black;" href="' . 'http://10.109.52.4/mirai/public/index/extra_order/bom_multi_level' . '/' . $data['extra_order_detail'][$i]->material_number . '">&#128065; BOM</a></td>');
                        } else {
                            print_r('<td style="border: 1px solid black; width: 5%; text-align: center;">-</td>');
                        }
                        print_r('</tr>');
                    } ?>
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <center>
            <a href="{{ url('index/extra_order') }}">&#10148; Click here to check the request</a>
        </center>
    </div>
</body>

</html>
