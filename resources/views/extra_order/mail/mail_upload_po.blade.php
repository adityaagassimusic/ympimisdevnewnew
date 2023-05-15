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
        <center>
            Please upload your extra order PO by clicking the link below
            <br>
            <table style="width: 80%">
                <tr>
                    <th style="width: 1%; font-weight: bold; color: black;">
                        <a
                            href="{{ url('index/extra_order/upload_po/?eo_number=' . $data['extra_order']->eo_number) }}">{{ 'http://10.109.52.4/mirai/public/index/extra_order/upload_po/?eo_number=' . $data['extra_order']->eo_number }}</a>
                    </th>
                </tr>
            </table>
        </center>
        <br>
        <div style="width: 90%; margin: auto;">
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">EO Number <span class="jp">(エキストラオーダー番号)</span></td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->eo_number }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Order By <span class="jp">(予約者)</span></td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ ucwords($data['order_by']->name) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Recipient <span class="jp">(送り先)</span></td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->attention }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">PO By <span class="jp">(発注書作成者 )</span></td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ ucwords($data['po_by']->name) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Division <span class="jp">(部門)</span></td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->division }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Destination <span class="jp">(仕向け)</span></td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->destination_name }}
                            ({{ $data['extra_order']->destination_shortname }})</td>
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
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th style="border: 1px solid black; width: 1%; text-align: right;">{{ $total_amount }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>

</html>
