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
            <p class="status"></i>REMINDER EXTRA ORDER SHORTAGE!</p>
            <p class="message">Please produce it as soon as possible</p>
        </center>
        <br>
        <div style="width: 90%; margin: auto;">
            <table style="border:1px solid black; border-collapse: collapse; width: 100%; font-size: 10pt;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #63ccff;">EO Number</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Dest.</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Material</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Description</th>
                        <th style="border:1px solid black; background-color: #63ccff;">SLoc</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Prod. Date</th>
                        <th style="border:1px solid black; background-color: #63ccff;">St. Date</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Qty</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Act. Prod.</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Minus</th>
                        <th style="border:1px solid black; background-color: #63ccff;">BOM</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($data['minus']); $i++) {
                        print_r('<tr>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['minus'][$i]->eo_number . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['minus'][$i]->destination_shortname . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['minus'][$i]->material_number . '</td>');
                        print_r('<td style="border: 1px solid black; width: 15%; text-align: left;">' . $data['minus'][$i]->description . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['minus'][$i]->storage_location . '</td>');
                        print_r('<td style="border: 1px solid black; width: 5%; text-align: center;">' . $data['minus'][$i]->due_date . '</td>');
                        print_r('<td style="border: 1px solid black; width: 5%; text-align: center;">' . $data['minus'][$i]->request_date . '</td>');
                        print_r('<td style="border: 1px solid black; width: 2%; text-align: right;">' . $data['minus'][$i]->quantity . '</td>');
                        print_r('<td style="border: 1px solid black; width: 2%; text-align: right;">' . $data['minus'][$i]->production_quantity . '</td>');
                        print_r('<td style="border: 1px solid black; width: 2%; text-align: right;">' . $data['minus'][$i]->minus . '</td>');
                        if ($data['minus'][$i]->is_completion == '1') {
                            print_r('<td style="border: 1px solid black; width: 5%; text-align: center;"><a style="background-color: #ffe84e; text-decoration: none; color: black;" href="' . 'http://10.109.52.4/mirai/public/index/extra_order/bom_multi_level' . '/' . $data['minus'][$i]->material_number . '">&#128065; BOM</a></td>');
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
