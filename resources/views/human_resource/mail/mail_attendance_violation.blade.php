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
            This is an automatic notification. Please do not reply to this address.
            <br>
            <br>
            <table style="border-collapse: collapse;" width="95%">
                <tbody>
                    <tr>
                        <td style="background-color: #F49CBB; text-align: center; font-weight: bold;">
                            Indikasi Pelanggaran Kehadiran
                            &nbsp;
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <span>Catatan: Dalam 30 hari ({{ date('d M Y', strtotime($data['first_30'])) }} s/d
                {{ date('d M Y', strtotime($data['last_30'])) }})</span>
            <table style="border:1px solid black; border-collapse: collapse; width: 80%; padding-top: 0px">
                <thead style="background-color: #BDD5EA; color: black;">
                    <tr>
                        <th width="0.1%" style="border:1px solid black; text-align: center; font-size: 12px;">#</th>
                        <th width="1%" style="border:1px solid black; text-align: left; font-size: 12px;">NIK</th>
                        <th width="5%" style="border:1px solid black; text-align: left; font-size: 12px;">Nama</th>
                        <th width="1%" style="border:1px solid black; text-align: center; font-size: 12px;">
                            Sakit
                        </th>
                        <th width="1%" style="border:1px solid black; text-align: center; font-size: 12px;">
                            Mangkir
                        </th>
                        <th width="1%" style="border:1px solid black; text-align: center; font-size: 12px;">
                            Terlambat/<br>Pulang Cepat
                        </th>
                        <th width="1%" style="border:1px solid black; text-align: center; font-size: 12px;">
                            Keluar<br>Perusahaan
                        </th>
                        {{-- <th width="1%" style="border:1px solid black; text-align: center; font-size: 12px;">
                            Tidak<br>Ceklog
                        </th> --}}
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $index = 1;
                    foreach ($data['violations'] as $row) {
                        $color_sakit = '';
                        $color_mangkir = '';
                        $color_terlambat = '';
                        $color_izin = '';
                        // $color_absen = '';
                        if ($row['sakit'] > 0) {
                            $color_sakit = 'background-color: #F49CBB;';
                        }
                        if ($row['mangkir'] > 0) {
                            $color_mangkir = 'background-color: #F49CBB;';
                        }
                        if ($row['terlambat'] > 0) {
                            $color_terlambat = 'background-color: #F49CBB;';
                        }
                        if ($row['izin'] > 0) {
                            $color_izin = 'background-color: #F49CBB;';
                        }
                        // if ($row['absen'] > 0) {
                        //     $color_absen = 'background-color: #F49CBB;';
                        // }
                    
                        print_r('<tr>');
                        print_r('<td style="border:1px solid black; font-size: 12px; width: 0.1%; height: 25px; text-align: center;">' . $index . '</td>');
                        print_r('<td style="border:1px solid black; font-size: 12px; width: 1%; height: 25px; text-align: left;">' . $row['employee_id'] . '</td>');
                        print_r('<td style="border:1px solid black; font-size: 12px; width: 5%; height: 25px; text-align: left;">' . $row['name'] . '</td>');
                        print_r('<td style="border:1px solid black; font-size: 12px; width: 1%; height: 25px; text-align: center; ' . $color_sakit . '">' . $row['sakit'] . '</td>');
                        print_r('<td style="border:1px solid black; font-size: 12px; width: 1%; height: 25px; text-align: center; ' . $color_mangkir . '">' . $row['mangkir'] . '</td>');
                        print_r('<td style="border:1px solid black; font-size: 12px; width: 1%; height: 25px; text-align: center; ' . $color_terlambat . '">' . $row['terlambat'] . '</td>');
                        print_r('<td style="border:1px solid black; font-size: 12px; width: 1%; height: 25px; text-align: center; ' . $color_izin . '">' . $row['izin'] . '</td>');
                        // print_r('<td style="border:1px solid black; font-size: 12px; width: 1%; height: 25px; text-align: center; ' . $color_absen . '">' . $row['absen'] . '</td>');
                        print_r('</tr>');
                        $index++;
                    }
                    ?>
                </tbody>
                <tfoot>
                </tfoot>
            </table>
            <br>
            <br>
            <br>
            <br>
        </center>
    </div>
</body>

</html>
