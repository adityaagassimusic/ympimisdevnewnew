<!DOCTYPE html>
<html>

<head>
    <style type="text/css">
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

        * {
            font-family: Sans-serif;
        }

        .approval-table {
            border: 1px solid black;
            border-collapse: collapse;
            width: auto;
            float: right;
            padding: 2px;
            margin-right: 5%;
        }

        .approval-body {
            border: 1px solid black;
            width: 150px !important;
            padding-bottom: 2px;
            padding-top: 2px;
            padding-left: 5px;
            padding-right: 5px;
            text-align: center;
            vertical-align: middle;
        }

        .jp {
            font-weight: normal;
        }

        .msg {
            font-weight: bold;
            font-size: 20pt;
        }
    </style>
</head>

<body>
    <div>
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
                alt=""><br>
            <p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>
        </center>
        <br>
        <div style="width: 90%; margin: auto;">
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Recipient</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['send_app']->attention }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Destination</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['send_app']->destination_name }}
                            ({{ $data['send_app']->destination_shortname }})</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Division</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['send_app']->division }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Ship By</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['send_app']->shipment_by }}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table style="border:1px solid black; border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #aee571;">#</th>
                        <th style="border:1px solid black; background-color: #aee571;">EO Number</th>
                        <th style="border:1px solid black; background-color: #aee571;">Material</th>
                        <th style="border:1px solid black; background-color: #aee571;">Description</th>
                        <th style="border:1px solid black; background-color: #aee571;">UoM</th>
                        <th style="border:1px solid black; background-color: #aee571;">St Date</th>
                        <th style="border:1px solid black; background-color: #aee571;">Bl Date</th>
                        <th style="border:1px solid black; background-color: #aee571;">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_amount = 0;
                    $count = 0;
                    for ($i = 0; $i < count($data['send_app_detail']); $i++) {
                        print_r('<tr>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . ++$count . '</td>');
                        print_r('<td style="border: 1px solid black; width: 3%; text-align: center;">' . $data['send_app_detail'][$i]->eo_number . '</td>');
                        print_r('<td style="border: 1px solid black; width: 3%; text-align: center;">' . $data['send_app_detail'][$i]->material_number . '</td>');
                        print_r('<td style="border: 1px solid black; width: 10%;">' . $data['send_app_detail'][$i]->description . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['send_app_detail'][$i]->uom . '</td>');
                        print_r('<td style="border: 1px solid black; width: 3%; text-align: right;">' . $data['send_app']['st_date'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 3%; text-align: right;">' . $data['send_app']['bl_date'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: right;">' . $data['send_app_detail'][$i]->quantity . '</td>');
                        print_r('</tr>');
                    } ?>
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <br>

    </div>
</body>

</html>
