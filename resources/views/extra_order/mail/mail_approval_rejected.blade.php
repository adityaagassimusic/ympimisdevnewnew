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
        }

        .message {
            font-size: 16pt;
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
            <p class="status"></i>REJECTED!</p>
            <p class="message">Approval has been Rejected</p>
        </center>
        <br>
        <div style="width: 90%; margin: auto;">
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">EO Number</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->eo_number }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Recipient</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->attention }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Division</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->division }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Destination</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->destination_name }}
                            ({{ $data['extra_order']->destination_shortname }})</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Attachment</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->attachment }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Remark</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->remark }}</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <span style="font-weight: bold;">Request List:</span>
            <table style="border:1px solid black; border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #aee571;">Material_Buyer</th>
                        <th style="border:1px solid black; background-color: #aee571;">Material_YMPI</th>
                        <th style="border:1px solid black; background-color: #aee571;">Description</th>
                        <th style="border:1px solid black; background-color: #aee571;">ETD</th>
                        <th style="border:1px solid black; background-color: #aee571;">Ship_By</th>
                        <th style="border:1px solid black; background-color: #aee571;">Qty</th>
                        <th style="border:1px solid black; background-color: #aee571;">UoM</th>
                        <th style="border:1px solid black; background-color: #aee571;">Price (USD)</th>
                        <th style="border:1px solid black; background-color: #aee571;">Amount (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_amount = 0;
                    for ($i = 0; $i < count($data['lists']); $i++) {
                        print_r('<tr>');
                        print_r('<td style="border: 1px solid black; width: 1%;">' . $data['lists'][$i]['material_number_buyer'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%;">' . $data['lists'][$i]['material_number'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 10%;">' . $data['lists'][$i]['description'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 3%; text-align: center;">' . $data['lists'][$i]['request_date'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%;">' . $data['lists'][$i]['shipment_by'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: right;">' . $data['lists'][$i]['quantity'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%;">' . $data['lists'][$i]['uom'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: right;">' . $data['lists'][$i]['sales_price'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: right;">' . $data['lists'][$i]['amount'] . '</td>');
                        print_r('</tr>');
                        $total_amount += $data['lists'][$i]['amount'];
                    } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="8">Total Amount</th>
                        <th style="border: 1px solid black; width: 1%; text-align: right;">{{ $total_amount }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br>
        <br>
        <center>
            <a href="{{ url('index/extra_order/detail/' . $data['extra_order']->eo_number) }}">&#10148; Click here to
                check the request</a>
        </center>
    </div>
</body>

</html>
