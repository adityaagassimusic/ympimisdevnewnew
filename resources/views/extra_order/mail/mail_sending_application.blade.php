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
            <p class="msg">New Sending Application Already Submitted<br>Check it as soon as possible!</p>
        </center>
        <br>
        <div style="width: 90%; margin: auto;">
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Send App No.</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['send_app']->send_app_no }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Recipient</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['send_app']->attention }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Destination</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['send_app']->destination_name }} ({{ $data['send_app']->destination_shortname }})
                        </td>
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
                    <tr>
                        <td style="font-weight: bold;">Payment Term</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['send_app']->payment_term }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Freight</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['send_app']->freight }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Condition</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['send_app']->condition }}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table style="border:1px solid black; border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #aee571;">EO Number</th>
                        <th style="border:1px solid black; background-color: #aee571;">Material</th>
                        <th style="border:1px solid black; background-color: #aee571;">Description</th>
                        <th style="border:1px solid black; background-color: #aee571;">Qty</th>
                        <th style="border:1px solid black; background-color: #aee571;">UoM</th>
                        <th style="border:1px solid black; background-color: #aee571;">Price (USD)</th>
                        <th style="border:1px solid black; background-color: #aee571;">Amount (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_amount = 0;
                    for ($i = 0; $i < count($data['send_app_detail']); $i++) {
                        print_r('<tr>');
                        print_r('<td style="border: 1px solid black; width: 5%; text-align: center;">' . $data['send_app_detail'][$i]['sequence'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['send_app_detail'][$i]['material_number'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 10%;">' . $data['send_app_detail'][$i]['description'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: right;">' . $data['send_app_detail'][$i]['quantity'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['send_app_detail'][$i]['uom'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: right;">' . $data['send_app_detail'][$i]['sales_price'] . '</td>');
                        $amount = $data['send_app_detail'][$i]['quantity'] * $data['send_app_detail'][$i]['sales_price'];
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: right;">' . round($amount, 2) . '</td>');
                        print_r('</tr>');
                        $total_amount += $amount;
                    } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">Total Amount</th>
                        <th style="border: 1px solid black; width: 1%; text-align: right;">
                            {{ round($total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br>
        <br>
        <center>
            <a href="{{ url('index/extra_order/sending_application') }}">&#10148; Click here to check the send app </a>
        </center>
        <br>
        <br>
        <br>


    </div>
</body>

</html>
