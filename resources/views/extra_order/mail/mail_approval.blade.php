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
    </style>
</head>

<body>
    <div>
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
                alt=""><br>
            <p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>
            <span style="font-weight: bold; color: green; font-size: 24px;">
                Extra Order Confirmation<br>エキストラオーダー確認
            </span>
        </center>
        <br>
        <div style="width: 90%; margin: auto;">
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">EO Number <span class="jp">(EO番号)</span></td>
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
                    {{-- <tr>
                        <td style="font-weight: bold;">Attachment <span class="jp">(添付)</span></td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['extra_order']->attachment }}</td>
                    </tr> --}}
                    <tr>
                        <td style="font-weight: bold; vertical-align: top;">Note <span class="jp">(備考)</span></td>
                        <td style="font-weight: bold; vertical-align: top;">:</td>
                        <td style="vertical-align: top;">@php echo $data['extra_order']->remark @endphp</td>
                    </tr>
                </tbody>
            </table>
            <br>
            <span style="font-weight: bold;">Request List:</span>
            <table style="border:1px solid black; border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #aee571;">#</th>
                        <th style="border:1px solid black; background-color: #aee571;">Material Buyer</th>
                        <th style="border:1px solid black; background-color: #aee571;">Material YMPI</th>
                        <th style="border:1px solid black; background-color: #aee571;">Description</th>
                        <th style="border:1px solid black; background-color: #aee571;">ValCl</th>
                        <th style="border:1px solid black; background-color: #aee571;">ETD</th>
                        <th style="border:1px solid black; background-color: #aee571;">Ship By</th>
                        <th style="border:1px solid black; background-color: #aee571;">Qty</th>
                        <th style="border:1px solid black; background-color: #aee571;">UoM</th>
                        <th style="border:1px solid black; background-color: #aee571;">Price (USD)</th>
                        <th style="border:1px solid black; background-color: #aee571;">Amount (USD)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_amount = 0;
                    $count = 0;
                    for ($i = 0; $i < count($data['lists']); $i++) {
                        print_r('<tr>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . ++$count . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['lists'][$i]['material_number_buyer'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['lists'][$i]['material_number'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 10%;">' . $data['lists'][$i]['description'] . '</td>');
                    
                        $valcl = '-';
                        for ($j = 0; $j < count($data['valcl']); $j++) {
                            if ($data['lists'][$i]['material_number'] == $data['valcl'][$j]->material_number) {
                                $valcl = $data['valcl'][$j]->valcl;
                                break;
                            }
                        }
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $valcl . '</td>');
                    
                        print_r('<td style="border: 1px solid black; width: 3%; text-align: center;">' . $data['lists'][$i]['request_date'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['lists'][$i]['shipment_by'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: right;">' . $data['lists'][$i]['quantity'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . $data['lists'][$i]['uom'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: right;">' . $data['lists'][$i]['sales_price'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: right;">' . $data['lists'][$i]['amount'] . '</td>');
                        print_r('</tr>');
                        $total_amount += $data['lists'][$i]['amount'];
                    } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="10">Total Amount</th>
                        <th style="border: 1px solid black; width: 1%; text-align: right;">{{ $total_amount }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br>
        <br>
        <center>
            Do you aggree with this EOC ?
            <br>
            <table style="width: 60%">
                <tr>
                    <th style="width: 25%; font-weight: bold; color: black;">
                        <a style="background-color: #ccff90; text-decoration: none; color: black;"
                            href="{{ url('index/extra_order/approval_eoc/?status=Approved&approval_id=' . $data['approval_id']) }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Approve&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(承認)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                    </th>
                    <th style="width: 5%;">
                        &nbsp;
                    </th>
                    <th style="width: 40%; font-weight: bold; color: black;">
                        <a style="background-color: #25b2fd; text-decoration: none; color: black;"
                            href="{{ url('index/extra_order/approval_eoc/?status=Hold&approval_id=' . $data['approval_id']) }}">
                            &nbsp;Hold & Comment&nbsp;<br>&nbsp;&nbsp; (保留・コメント)&nbsp;&nbsp;
                        </a>
                    </th>
                    <th style="width: 5%;">
                        &nbsp;
                    </th>
                    <th style="width: 25%; font-weight: bold; color: black;">
                        <a style="background-color: #ff6090; text-decoration: none; color: black;"
                            href="{{ url('index/extra_order/approval_eoc/?status=Rejected&approval_id=' . $data['approval_id']) }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Reject&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(
                            却下 )&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                    </th>
                </tr>
            </table>
        </center>
        <br>
        <br>
        <center>
            <a href="{{ url('index/extra_order/detail/' . $data['extra_order']->eo_number) }}">&#10148; Click here to
                check the request </a>
        </center>
        <br>
        <br>
        <br>

        <center>
            @php
                $loop = ceil((count($data['approval']) + 1) / 5);
                $mod = (count($data['approval']) + 1) % 5;
                
                print_r('<table class="approval-table" style="margin-bottom: 2%; border: none;">');
                for ($i = 0; $i < $loop; $i++) {
                    // START HEAD
                    print_r('<tr>');
                    if ($i == 0) {
                        print_r('<td class="approval-body" style="background-color: #d5e167; font-weight: bold; height: 30px;">Prepared by</td>');
                    }
                
                    $start = $i * 5;
                    if ($start != 0) {
                        $start--;
                    }
                    $end = $start + 5;
                    if ($end >= count($data['approval'])) {
                        $end = count($data['approval']);
                    }
                    if ($i == 0) {
                        $end--;
                    }
                
                    for ($j = $start; $j < $end; $j++) {
                        print_r('<td class="approval-body" style="background-color: #d5e167; font-weight: bold; height: 30px;">' . $data['approval'][$j]['remark'] . '</td>');
                    }
                    print_r('</tr>');
                    // END HEAD
                
                    // START BODY
                    print_r('<tr>');
                    if ($i == 0) {
                        print_r('<td class="approval-body""><br>Prepared at<br>' . $data['approval'][0]['created_at'] . '<br><br></td>');
                    }
                
                    $start = $i * 5;
                    if ($start != 0) {
                        $start--;
                    }
                    $end = $start + 5;
                    if ($end >= count($data['approval'])) {
                        $end = count($data['approval']);
                    }
                    if ($i == 0) {
                        $end--;
                    }
                
                    for ($j = $start; $j < $end; $j++) {
                        if ($data['approval'][$j]['status'] == 'Approved') {
                            print_r('<td class="approval-body"><br>' . $data['approval'][$j]['status'] . ' at<br>' . $data['approval'][$j]['approved_at'] . '<br><br></td>');
                        } else {
                            print_r('<td class="approval-body"><br><br><br><br><br></td>');
                        }
                    }
                    print_r('</tr>');
                    // END BODY
                
                    // START FOOT
                    print_r('<tr>');
                    if ($i == 0) {
                        print_r('<td class="approval-body" style="background-color: #d5e167; font-weight: bold; height: 30px;">' . $data['prepared_by']['name'] . '</td>');
                    }
                
                    $start = $i * 5;
                    if ($start != 0) {
                        $start--;
                    }
                    $end = $start + 5;
                    if ($end >= count($data['approval'])) {
                        $end = count($data['approval']);
                    }
                    if ($i == 0) {
                        $end--;
                    }
                
                    for ($j = $start; $j < $end; $j++) {
                        print_r('<td class="approval-body" style="background-color: #d5e167; font-weight: bold; height: 30px;">' . $data['approval'][$j]['approver_name'] . '</td>');
                    }
                    print_r('</tr>');
                    // END FOOT
                
                    if ($i != $loop - 1) {
                        print_r('<tr>');
                        print_r('<th colspan="5" style="border-left: none; border-right: none; height: 20px;"></th>');
                        print_r('</tr>');
                    }
                }
                print '</table>';
            @endphp
        </center>

    </div>
</body>

</html>
