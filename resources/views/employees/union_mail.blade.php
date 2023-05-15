<!DOCTYPE html>
<html>

<head>
    <style type="text/css">
        table>tbody>tr>td {
            padding: 10px 10px 10px 10px;
        }

        table>thead>tr>th {
            padding: 10px 10px 10px 10px;
        }
    </style>
</head>

<body>
    <div>
        <center>
            <p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
            <span style="font-weight: bold; color: purple; font-size: 18px;">Outstanding Kepesertaan Serikat
                Pekerja<br>(Data karyawan di sunfish vs pengajuan kepesertaan)</span><br>
            <table style="border:1px solid black; border-collapse: collapse; width: 80%; padding-top: 0px">
                <thead style="background-color: #BDD5EA; color: black;">
                    <tr>
                        <th width="0.5%" style="border:1px solid black; text-align: center; font-size: 12px;">#</th>
                        <th width="2%" style="border:1px solid black; text-align: center; font-size: 12px;">
                            Tanggal
                        </th>
                        <th width="1%" style="border:1px solid black; text-align: center; font-size: 12px;">ID
                        </th>
                        <th width="4%" style="border:1px solid black; text-align: left; font-size: 12px;">Nama
                        </th>
                        <th width="1%" style="border:1px solid black; text-align: center; font-size: 12px;">
                            Status
                        </th>
                        <th width="1%" style="border:1px solid black; text-align: center; font-size: 12px;">
                            Serikat
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index = 1;
                    foreach ($data['labor_unions'] as $row) {
                        $remark = '';
                        $color = '';
                        if ($row->remark == 'join') {
                            $color = 'color: #00a65a';
                            $remark = 'BERGABUNG';
                        } elseif ($row->remark == 'leave') {
                            $color = 'color: #e53935';
                            $remark = 'KELUAR';
                        } else {
                            $remark = 'UNDEFINED';
                        }
                        print_r('<tr>');
                        print_r('<td width="0.5%" style="border:1px solid black; text-align: center; font-size: 12px;">' . $index . '</td>');
                        print_r('<td width="2%" style="border:1px solid black; text-align: center; font-size: 12px;">' . $row->created_at . '</td>');
                        print_r('<td width="1%" style="border:1px solid black; text-align: center; font-size: 12px;">' . $row->created_by . '</td>');
                        print_r('<td width="4%" style="border:1px solid black; text-align: left; font-size: 12px;">' . $row->created_by_name . '</td>');
                        print_r('<td width="1%" style="border:1px solid black; text-align: center; font-size: 12px; font-weight: bold; ' . $color . '">' . $remark . '</td>');
                        print_r('<td width="1%" style="border:1px solid black; text-align: center; font-size: 12px;">' . $row->union_name . '</td>');
                        print_r('</tr>');
                        $index++;
                    }
                    ?>
                </tbody>
            </table>
        </center>
    </div>
</body>

</html>
