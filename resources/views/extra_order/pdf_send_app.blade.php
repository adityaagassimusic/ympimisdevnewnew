<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{{ url('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/font-awesome/css/font-awesome.min.css') }}">
    <style type="text/css">
        table tr td {
            border-collapse: collapse;
            vertical-align: middle;
        }

        table.table>tbody>tr>td {
            padding-top: 0px;
            padding-bottom: 0px;
            border: 1px solid black;
            font-size: 10px;
        }

        table.table-no-border>tbody>tr>td {
            padding-top: 0px;
            padding-bottom: 0px;
            font-size: 12px;
            border: 1px solid white;
        }

        table.table-material>thead>tr>th {
            padding-top: 0px;
            padding-bottom: 0px;
            padding-left: 3px;
            padding-right: 3px;
            border: none;
            border-bottom: 1px solid black !important;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        table.table-material>tbody>tr>td {
            padding-top: 0px;
            padding-bottom: 0px;
            border: 1px solid white !important;
        }

        table.table-border>tbody>tr>td {
            padding: 2px;
            border: 1px solid black !important;
        }

        table.table-left>tbody>tr {
            padding-top: 0px;
            padding-bottom: 0px;
            font-size: 12px;
            border: 1px solid black !important;
        }

        table.table-right>tbody>tr {
            padding-top: 0px;
            padding-bottom: 0px;
            font-size: 12px;
            border: 1px solid black !important;
        }

        .row-border {
            border: 1px solid black !important;
        }

        .no-padding {
            padding: 0px;
        }

        @page {
            margin-top: 2%;
            vertical-align: middle;
        }

        .footer {
            font-size: 12px;
            color: #4f4d56;
            position: fixed;
            left: 0px;
            bottom: -20px;
            right: 0px;
            height: 160px;
        }

        .footer .pagenum:before {
            content: counter(page);
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    @php
        
        $submission_date = '';
        for ($i = 0; $i < count($send_app_log); $i++) {
            if ($send_app_log[$i]->status == 1) {
                $submission_date = date('F d, Y', strtotime($send_app_log[$i]->created_at));
                break;
            }
        }
        
        $eo_number = '';
        $eo_numbers = json_decode($send_app->document_number);
        for ($i = 0; $i < count($eo_numbers); $i++) {
            $eo_number .= $eo_numbers[$i];
            if ($i != count($eo_numbers) - 1) {
                $eo_number .= $eo_numbers[$i];
            }
        }
        
        $applicant = '';
        $applicant_at = '';
        
        $check_1 = '';
        $check_1_at = '';
        
        $check_2 = '';
        $check_2_at = '';
        
        $shipment = '';
        $shipment_at = '';
        
        for ($i = 0; $i < count($send_app_log); $i++) {
            if ($send_app_log[$i]->status == 1) {
                $applicant = $send_app_log[$i]->name;
                $applicant_at = $send_app_log[$i]->created_at;
            }
        
            if ($send_app_log[$i]->status == 2) {
                $check_1 = $send_app_log[$i]->name;
                $check_1_at = $send_app_log[$i]->created_at;
            }
        
            if ($send_app_log[$i]->status == 3) {
                $check_2 = $send_app_log[$i]->name;
                $check_2_at = $send_app_log[$i]->created_at;
            }
        
            if ($send_app_log[$i]->status == 4) {
                $shipment = $send_app_log[$i]->name;
                $shipment_at = $send_app_log[$i]->created_at;
            }
        }
        
    @endphp

    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td width="10%">Applicant</td>
                <td width="2%"></td>
                <td width="10%">Packing Check 1</td>
                <td width="2%"></td>
                <td width="10%">Packing Check 2</td>
                <td width="2%"></td>
                <td width="10%">Shipment Sch.</td>
                <td width="10%"></td>
                <td width="5%">Note</td>
                <td width="1%">:</td>
                <td width="30%" style="vertical-align: top !important;"><i class="fa fa-caret-right"></i> Attach
                    supporting document</td>
            </tr>
            <tr>
                <td style="width: 10%">
                    <table width="100%" class="table-border">
                        <tr>
                            <td style="height: 60px !important; text-align: center;"><br>{{ $applicant_at }}<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 35px !important;">{{ $applicant }}</td>
                        </tr>
                    </table>
                </td>
                <td
                    style="text-align: center !important; vertical-align: top; font-size: 16px; font-weight: bold; padding-top: 20px;">
                    <center><i class="fa fa-arrow-right"></i></center>
                </td>
                <td style="width: 10%">
                    <table width="100%" class="table-border">
                        <tr>
                            <td style="height: 60px !important; text-align: center;"><br>{{ $check_1_at }}<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 35px !important;">{{ $check_1 }}</td>
                        </tr>
                    </table>
                </td>
                <td
                    style="text-align: center !important; vertical-align: top; font-size: 16px; font-weight: bold; padding-top: 20px;">
                    <center><i class="fa fa-arrow-right"></i></center>
                </td>
                <td style="width: 10%">
                    <table width="100%" class="table-border">
                        <tr>
                            <td style="height: 60px !important; text-align: center;"><br>{{ $check_2_at }}<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 35px !important;">{{ $check_2 }}</td>
                        </tr>
                    </table>
                </td>
                <td
                    style="text-align: center !important; vertical-align: top; font-size: 16px; font-weight: bold; padding-top: 20px;">
                    <center><i class="fa fa-arrow-right"></i></center>
                </td>
                <td style="width: 10%">
                    <table width="100%" class="table-border">
                        <tr>
                            <td style="height: 60px !important; text-align: center;"><br>{{ $shipment_at }}<br><br>
                            </td>
                        </tr>
                        <tr>
                            <td style="height: 35px !important;">{{ $shipment }}</td>
                        </tr>
                    </table>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td style="vertical-align: top !important;"><i class="fa fa-caret-right"></i> Describe condition of
                    goods in details</td>
            </tr>
            <tr>
                <td>Prod. Control</td>
                <td></td>
                <td>Warehouse</td>
                <td></td>
                <td>Warehouse</td>
                <td></td>
                <td>EXIM</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
        <span style="float: right; text-align: right;">Page <span class="pagenum"></span></span>
    </div>

    <table class="table-no-border" style="width:100%;">
        <tr>
            <th style="max-width: 30%;">
                <table style="width: 100%; font-size: 10pt;" class="table-border">
                    <tr class="row-border">
                        <td
                            style="padding: 2px !important; border-right: 1px solid white !important; vertical-align: top;">
                            Ship To</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; border-right: 1px solid white !important; vertical-align: top;">
                            :</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; vertical-align: top;">
                            {{ $destination->destination_shortname }}</td>
                    </tr>
                    <tr class="row-border">
                        <td
                            style="padding: 2px !important; border-right: 1px solid white !important; vertical-align: top;">
                            Attention</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; border-right: 1px solid white !important; vertical-align: top;">
                            :</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; vertical-align: top;">
                            {{ $send_app->attention }}</td>
                    </tr>
                    <tr class="row-border">
                        <td
                            style="padding: 2px !important; border-right: 1px solid white !important; vertical-align:  top;">
                            Division</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; border-right: 1px solid white !important; vertical-align: top;">
                            :</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; vertical-align: top;">
                            {{ $send_app->division }}</td>
                    </tr>
                    <tr class="row-border">
                        <td style="padding: 2px !important; border-right: 1px solid white !important; text-align: top;">
                            Date</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; border-right: 1px solid white !important; vertical-align: top;">
                            :</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; vertical-align: top;">
                            {{ $submission_date }}</td>
                    </tr>
                </table>
            </th>
            <th style="width: 5%;"></th>
            <th style="width: 30%; vertical-align: top;">
                <table style="width: 100%;">
                    <tr>
                        <td class="title">
                            EXTRA ORDER<br>SENDING APPLICATION
                        </td>
                    </tr>
                </table>
            </th>
            <th style="width: 5%;"></th>
            <th style="width: 30%;">
                <table style="width: 100%; font-size: 10pt;" class="table-border">
                    <tr class="row-border">
                        <td
                            style="padding: 2px !important; border-right: 1px solid white !important; vertical-align: top;">
                            No. </td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; border-right: 1px solid white !important; vertical-align: top;">
                            :</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; vertical-align: top;">
                            {{ $eo_number }}</td>
                    </tr>
                    <tr class="row-border">
                        <td
                            style="padding: 2px !important; border-right: 1px solid white !important; vertical-align: top;">
                            No. Invoice</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; border-right: 1px solid white !important; vertical-align: top;">
                            :</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; vertical-align: top;">
                            {{ $send_app->invoice_number }}</td>
                    </tr>
                    <tr class="row-border">
                        <td
                            style="padding: 2px !important; border-right: 1px solid white !important; vertical-align: top;">
                            Note</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; border-right: 1px solid white !important; vertical-align: top;">
                            :</td>
                        <td
                            style="padding: 2px !important; border-left: 1px solid white !important; vertical-align: top;">
                            {{ $send_app->note }}<br><br><br></td>
                    </tr>
                </table>
            </th>
        </tr>
    </table>

    <table class="table-no-border" style="width:100%;">
        <tr>
            <th style="vertical-align: bottom !important; width: 33%; text-align: left;"><span
                    style="font-weight: bold;">PAYMENT TERM</span> : {{ $send_app->payment_term }}</th>
            <th style="vertical-align: bottom !important; width: 34%; text-align: center;"><span
                    style="font-weight: bold;">FREIGHT</span> : {{ $send_app->freight }}</th>
            <th style="vertical-align: bottom !important; width: 33%; text-align: center;">BY : <span
                    style="font-weight: bold; font-size: 30pt">{{ $send_app->shipment_by }}</span></th>
        </tr>
    </table>
    <br>

    @php
        $count = 0;
        $current_pkg = '';
        
        $sum_qty = 0;
        $sum_amount = 0;
        
        $page = 0;
        if (count($send_app_detail) <= 9) {
            $page = 1;
        } else {
            $page = ceil((count($send_app_detail) - 9) / 13) + 1;
        }
        
        $count = 0;
        
        for ($h = 1; $h <= $page; $h++) {
            print_r('<table class="table-material" style="width: 100%; font-size: 8pt;">');
            print_r('<thead>');
            print_r('<tr>');
            print_r('<th style="width: 2%;">No.</th>');
            print_r('<th style="width: 10%;">No. PO</th>');
            print_r('<th style="width: 30%; text-align: left;">GMC Desc</th>');
            print_r('<th style="width: 5%;">Qty</th>');
            print_r('<th style="width: 5%;">Uom</th>');
            print_r('<th style="width: 5%;">Sales Price</th>');
            print_r('<th style="width: 5%;">Amount</th>');
            print_r('<th style="width: 5%;">Pkg No</th>');
            print_r('<th style="width: 15%;">Measurement</th>');
            print_r('<th style="width: 5%;">Pkg Type</th>');
            print_r('<th style="width: 13%;">EO (Sequence)</th>');
            print_r('</tr>');
            print_r('</thead>');
        
            $start = $count;
            $end = 0;
            if ($h == 1) {
                $end = 9;
            } else {
                $end = ($h - 1) * 13 + 9;
            }
        
            if ($end >= count($send_app_detail)) {
                $end = count($send_app_detail);
            }
        
            print_r('<tbody>');
            for ($i = $start; $i < $end; $i++) {
                print_r('<tr>');
                print_r('<td style="padding: 2px; text-align: center;">' . ++$count . '</td>');
                print_r('<td style="padding: 2px; text-align: center;">' . $send_app_detail[$i]['po_number'] . '</td>');
                print_r('<td style="padding: 2px; text-align: left;">' . $send_app_detail[$i]['material_number'] . '<br>' . $send_app_detail[$i]['description'] . '</td>');
                print_r('<td style="padding: 2px; text-align: center;">' . $send_app_detail[$i]['quantity'] . '</td>');
                $sum_qty += floatval($send_app_detail[$i]['quantity']);
                print_r('<td style="padding: 2px; text-align: center;">' . $send_app_detail[$i]['uom'] . '</td>');
                print_r('<td style="padding: 2px; text-align: center;">' . $send_app_detail[$i]['sales_price'] . '</td>');
                $amount = $send_app_detail[$i]['quantity'] * $send_app_detail[$i]['sales_price'];
                $sum_amount += floatval($amount);
                print_r('<td style="padding: 2px; text-align: center;">' . $amount . '</td>');
                print_r('<td style="padding: 2px; text-align: center;">(' . $send_app_detail[$i]['package_no'] . ')</td>');
                if ($current_pkg == $send_app_detail[$i]['package_no']) {
                    print_r('<td style="padding: 2px; text-align: center;"></td>');
                } else {
                    print_r('<td style="padding: 2px; text-align: center;">P x L x T = ' . $send_app_detail[$i]['length'] . ' x ' . $send_app_detail[$i]['width'] . ' x ' . $send_app_detail[$i]['height'] . '<br>GW = ' . $send_app_detail[$i]['weight'] . ' KG</td>');
                }
                $current_pkg = $send_app_detail[$i]['package_no'];
                print_r('<td style="padding: 2px; text-align: center;">' . $send_app_detail[$i]['package_type'] . '</td>');
                print_r('<td style="padding: 2px; text-align: center;">' . $send_app_detail[$i]['sequence'] . '</td>');
                print_r('</tr>');
            }
        
            if ($h != $page) {
                print_r('</tbody>');
                print_r('</table>');
        
                print_r('<div class="page-break"></div>');
            } else {
                print_r('<tr>');
                print_r('<td style="font-weight: bold; padding: 2px; text-align: right;" colspan="3">Total Qty :</td>');
                print_r('<td style="font-weight: bold; padding: 2px; text-align: center;">' . $sum_qty . '</td>');
                print_r('<td style="font-weight: bold; padding: 2px; text-align: right;" colspan="2">Total Amount :</td>');
                print_r('<td style="font-weight: bold; padding: 2px; text-align: center;">' . $sum_amount . '</td>');
                print_r('<td style="font-weight: bold; padding: 2px; text-align: center;" colspan="4"></td>');
                print_r('</tr>');
        
                print_r('</tbody>');
                print_r('</table>');
            }
        }
        
    @endphp


</body>

</html>
