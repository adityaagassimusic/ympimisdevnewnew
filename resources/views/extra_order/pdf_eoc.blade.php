<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{{ url('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
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
            border: none;
            border-bottom: 1px solid black !important;
            font-size: 12px;
            font-weight: bold;
            text-align: center;
        }

        table.table-material>tbody>tr>td {
            padding-top: 0px;
            padding-bottom: 0px;
            font-size: 12px;
            border: 1px solid white !important;
        }

        table.table-check>tbody>tr>td {
            padding: 10px;
            border: 1px solid black !important;
            font-size: 12px;
        }

        .vertical-middle {
            vertical-align: middle;
        }

        .no-padding {
            padding: 0px;
        }

        @page {
            margin-top: 2%;
            vertical-align: middle;
        }

        .page-break {
            page-break-after: always;
        }

        .footer {
            font-size: 12px;
            color: #4f4d56;
            position: fixed;
            left: 0px;
            bottom: -20px;
            right: 0px;
        }

        .footer .pagenum:before {
            content: counter(page);
        }

        .form-tittle {
            font-size: 24px;
            font-weight: bold;
        }

        .ympi-tittle {
            font-size: 14px;
        }

        .eo-number {
            font-size: 24px !important;
            font-weight: bold;
            text-align: right;
        }

        .approver-header {
            text-align: center !important;
            vertical-align: middle !important;
            font-weight: bold;
            font-size: 10px;
        }

        .approver-content {
            padding-right: 0px !important;
            padding-left: 0px !important;
            text-align: center !important;
            vertical-align: middle !important;
            font-size: 8px;
        }

        .approver-name {
            text-align: center !important;
            vertical-align: middle !important;
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>

<body>
    <?php
    
    $row = ceil(count($approval) / 7);
    $mod = count($approval) % 7;
    $note_span = 7 - $mod;
    
    ?>
    @if ($row == 3)
        <div class="footer" style="height: 350px;">
        @else
            <div class="footer" style="height: 240px;">
    @endif

    <table class="table" style="margin-bottom: 0px !important;">
        <?php
        
        $start = 0;
        $end = 0;
        for ($r = 1; $r <= $row; $r++) {
            $start = $end;
            $end += 7;
        
            print_r('<tr>');
            if ($r == $row) {
                $end = count($approval);
                print_r('<td colspan="' . $note_span . '" rowspan="3" style="font-weight: bold;">Note :</td>');
            }
        
            for ($i = $start; $i < $end; $i++) {
                if ($approval[$i]['remark'] == 'Foreman') {
                    print_r('<td class="approver-header">' . $approval[$i]['role'] . '</td>');
                } elseif (str_contains($approval[$i]['remark'], 'Buyer')) {
                    print_r('<td class="approver-header">Buyer</td>');
                } else {
                    print_r('<td class="approver-header">' . $approval[$i]['remark'] . '</td>');
                }
            }
            print_r('</tr>');
        
            print_r('<tr>');
            for ($i = $start; $i < $end; $i++) {
                if (!is_null($approval[$i]['status'])) {
                    print_r('<td class="approver-content"><br>' . $approval[$i]['status'] . '<br>' . str_replace(' ', '<br>', $approval[$i]['approved_at']) . '<br><br></td>');
                } else {
                    print_r('<td class="approver-content"><br><br><br></td>');
                }
            }
            print_r('</tr>');
        
            print_r('<tr>');
            for ($i = $start; $i < $end; $i++) {
                print_r('<td class="approver-name">' . $approval[$i]['approver_name']) . '</td>';
            }
            print_r('</tr>');
        }
        ?>
    </table>
    <br>
    <span>{{ date('l, F j, Y', strtotime($prepared_at)) }}</span>
    <span style="float: right; text-align: right;">Page <span class="pagenum"></span></span>
    </div>

    <table class="table-no-border" style="width: 100%;">
        <tr>
            <td style="width: 50%;">
                <span class="form-tittle">Extra Order Confirmation</span><br>
                <span class="ympi-tittle">PT. Yamaha Musical Products Indonesia</span>
            </td>
            <td class="eo-number" style="width: 50%;">
                <span>{{ $extra_order->eo_number }}</span>
            </td>
        </tr>
    </table>

    <table class="table" style="width: 100%;">
        <tbody>
            <tr>
                @php
                    $colspan = 1;
                    for ($i = 0; $i < count($approval_kage); $i++) {
                        $next_index = $i + 1;
                        if ($next_index != count($approval_kage)) {
                            if ($approval_kage[$i]['position'] == $approval_kage[$next_index]['position']) {
                                $colspan++;
                            } else {
                                if ($colspan > 1) {
                                    print_r('<td class="approver-header" colspan="' . $colspan . '">' . $approval_kage[$i]['position'] . '</td>');
                                    $colspan = 1;
                                } else {
                                    print_r('<td class="approver-header">' . $approval_kage[$i]['position'] . '</td>');
                                }
                            }
                        } else {
                            print_r('<td class="approver-header">' . $approval_kage[$i]['position'] . '</td>');
                        }
                    }
                @endphp
            </tr>

            <tr>
                @php
                    for ($i = 0; $i < count($approval_kage); $i++) {
                        if ($approval_kage[$i]['status'] == 'Approved') {
                            print_r('<td class="approver-content"><br>' . $approval_kage[$i]['status'] . '<br>' . str_replace(' ', '<br>', $approval_kage[$i]['approved_at']) . '<br><br></td>');
                        } else {
                            print_r('<td class="approver-content"><br><br><br></td>');
                        }
                    }
                @endphp
            </tr>

            <tr>
                @php
                    for ($i = 0; $i < count($approval_kage); $i++) {
                        print_r('<td class="approver-name">' . $approval_kage[$i]['approver_name'] . '</td>');
                    }
                @endphp
            </tr>
        </tbody>
    </table>

    @php
        
        if ($row == 3) {
            $min = 24;
            $max = 35;
        } else {
            $min = 30;
            $max = 42;
        }
        
        if (count($detail) <= $min) {
            $page = 1;
        } else {
            $page = ceil((count($detail) - $min) / $max) + 1;
        }
        $count = 0;
        
        for ($h = 1; $h <= $page; $h++) {
            print_r('<table class="table-material" style="width: 100%;">');
            print_r('<thead>');
            print_r('<tr>');
            print_r('<th style="width: 1%;">No.</th>');
            print_r('<th style="width: 15%;">GMC</th>');
            print_r('<th style="width: 49%; text-align: left;">Desc</th>');
            print_r('<th style="width: 10%;">Qty</th>');
            print_r('<th style="width: 5%;">Uom</th>');
            print_r('<th style="width: 10%;">Due Date Production</th>');
            print_r('<th style="width: 10%;">Shipped By</th>');
            print_r('</tr>');
            print_r('</thead>');
            print_r('<tbody>');
        
            $start = $count;
            if ($start == 0) {
                $end = $min;
            } else {
                $end = ($h - 1) * $max + $min;
            }
        
            if ($end >= count($detail)) {
                $end = count($detail);
            }
        
            for ($i = $start; $i < $end; $i++) {
                print_r('<tr>');
                print_r('<td style="text-align: center;">' . ++$count . '</td>');
                print_r('<td style="text-align: center;">' . $detail[$i]['material_number'] . '</td>');
                print_r('<td style="text-align: left;">' . $detail[$i]['description'] . '</td>');
                print_r('<td style="text-align: center;">' . $detail[$i]['quantity'] . '</td>');
                print_r('<td style="text-align: center;">' . $detail[$i]['uom'] . '</td>');
                print_r('<td style="text-align: center;">' . $detail[$i]['due_date'] . '</td>');
                print_r('<td style="text-align: center;">' . $detail[$i]['shipment_by'] . '</td>');
                print_r('</tr>');
            }
            print_r('</tbody>');
            print_r('</table>');
        
            if ($h != $page) {
                print_r('<div class="page-break"></div>');
            }
        }
        
    @endphp

    <br>
    <br>
    <table class="table-no-border" style="width: 100%;">
        <tbody>
            <tr>
                <td style="width: 55%; padding-right: 10px; padding-left: 10px; vertical-align: top;">
                    <table class="table-check" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 30%; vertical-align: middle;">Sample Check</td>
                                <td style="width: 70%;"></td>
                            </tr>
                            <tr>
                                <td style="width: 30%; vertical-align: middle;">Point Check</td>
                                <td style="width: 70%;"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 45%; padding-right: 10px; padding-left: 10px; vertical-align: top;">
                    <table class="table-no-border" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="vertical-align: top; width: 20%; font-weight: bold;">To</td>
                                <td style="vertical-align: top; font-weight: bold;">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td style="vertical-align: top; width: 79%;">{{ $extra_order->destination_shortname }}
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top; width: 20%; font-weight: bold;">Attention</td>
                                <td style="vertical-align: top; font-weight: bold;">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td style="vertical-align: top; width: 79%;">{{ $extra_order->attention }}</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: top; width: 20%; font-weight: bold;">Division</td>
                                <td style="vertical-align: top; font-weight: bold;">:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td style="vertical-align: top; width: 79%;">{{ $extra_order->division }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

</body>

</html>
