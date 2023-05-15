<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{{ url('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <style type="text/css">
        table tr td {
            padding-left: 2px;
            padding-right: 2px;
            border-collapse: collapse;
            vertical-align: middle;
        }

        table.table-material>thead>tr>th {
            padding-left: 2px;
            padding-right: 2px;
            border: 1px solid black !important;
            font-size: 10px;
            font-weight: bold;
            text-align: center;
        }

        table.table-material>tbody>tr>td {
            padding-left: 2px;
            padding-right: 2px;
            font-size: 10px;
            border: 1px solid black !important;
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
            height: 240px;
        }

        .footer .pagenum:before {
            content: counter(page);
        }

        .form-tittle {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <?php
    
    function call_name($name)
    {
        $new_name = '';
        $blok_m = ['M.', 'Moch.', 'Mochammad', 'Moh.', 'Mohamad', 'Mokhamad', 'Much.', 'Muchammad', 'Muhamad', 'Muhammaad', 'Muhammad', 'Mukammad', 'Mukhamad', 'Mukhammad'];
    
        if (str_contains($name, ' ')) {
            $name = explode(' ', $name);
    
            if (in_array($name[0], $blok_m)) {
                $new_name = 'M.';
                for ($i = 1; $i < count($name); $i++) {
                    if ($i == 1) {
                        $new_name .= ' ';
                        $new_name .= $name[$i];
                    } else {
                        $new_name .= ' ';
                        $new_name .= substr($name[$i], 0, 1) . '.';
                    }
                }
            } else {
                for ($i = 0; $i < count($name); $i++) {
                    if ($i == 0) {
                        $new_name .= ' ';
                        $new_name .= $name[$i];
                    } else {
                        $new_name .= ' ';
                        $new_name .= substr($name[$i], 0, 1) . '.';
                    }
                }
            }
        } else {
            $new_name = $name;
        }
    
        return $new_name;
    }
    
    ?>

    <table class="table-no-border" style="width: 100%;">
        <tr>
            <td style="width: 100%;" style="vertical-align: middle; text-align: center;">
                <span class="form-tittle">Stocktaking Slip Period {{ $st->text }}</span><br>
            </td>
        </tr>
    </table>
    <table class="table-no-border" style="width: 100%;">
        <tr>
            <td style="width: 17%;">Stocktaking Date</td>
            <td style="width: 2%;">:</td>
            <td style="width: 58%;">{{ $st->date }}</td>
            <td style="width: 23%; text-align: right;">Creation Date : {{ $report[0]->created_at }}</td>
        </tr>
        <tr>
            <td style="width: 17%;">Group</td>
            <td style="width: 2%;">:</td>
            <td style="width: 58%;">{{ $report[0]->area }}</td>
            <td style="width: 23%;" rowspan="3">
                <table class="table-material" style="width: 100%;">
                    <thead>
                        <tr>
                            <th><br><br><br><br><br></th>
                        </tr>
                        <tr>
                            <th><br><br></th>
                        </tr>
                    </thead>
                </table>
            </td>
        </tr>
        <tr>
            <td style="width: 17%;">Storage Location</td>
            <td style="width: 2%;">:</td>
            <td style="width: 58%;">{{ $report[0]->storage_location }}</td>
        </tr>
        <tr>
            <td style="width: 17%;">YMES Slip No.</td>
            <td style="width: 2%;">:</td>
            <td style="width: 58%;">{{ $report[0]->list_no }}</td>
        </tr>


    </table>
    <br>

    @php
        print_r('<table class="table-material" style="width: 100%;">');
        print_r('<thead>');
        print_r('<tr>');
        print_r('<th style="width: 4%;">No.</th>');
        print_r('<th style="width: 11%;">Loc. Name</th>');
        print_r('<th style="width: 5%;">ValCl</th>');
        print_r('<th style="width: 7%;">GMC</th>');
        print_r('<th style="width: 38%;">Description</th>');
        print_r('<th style="width: 5%;">Uom</th>');
        print_r('<th style="width: 6%;">Status</th>');
        print_r('<th style="width: 5%;">Qty</th>');
        print_r('<th style="width: 5%;">Qty Revise</th>');
        print_r('<th style="width: 14%;">Audit</th>');
        print_r('</tr>');
        print_r('</thead>');
        print_r('<tbody>');
        
        for ($i = 0; $i < count($report); $i++) {
            print_r('<tr>');
            print_r('<td style="text-align: center;">' . $report[$i]->slip_no . '</td>');
            print_r('<td style="text-align: center;">' . $report[$i]->location . '</td>');
            print_r('<td style="text-align: center;">' . $report[$i]->valcl . '</td>');
            print_r('<td style="text-align: center;">' . $report[$i]->material_number . '</td>');
            print_r('<td style="text-align: left;">' . $report[$i]->material_description . '</td>');
            print_r('<td style="text-align: center;">' . $report[$i]->uom . '</td>');
            print_r('<td style="text-align: center;">' . $report[$i]->category . '</td>');
            print_r('<td style="text-align: right;">' . $report[$i]->quantity . '</td>');
            if ($report[$i]->final == $report[$i]->quantity) {
                print_r('<td style="text-align: right;"></td>');
            } else {
                print_r('<td style="text-align: right;">' . $report[$i]->final . '</td>');
            }
            print_r('<td style="text-align: left;">' . $report[$i]->auditor . '</td>');
            print_r('</tr>');
        }
        print_r('</tbody>');
        print_r('</table>');
        
    @endphp


</body>

</html>
