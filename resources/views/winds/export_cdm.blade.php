<!DOCTYPE html>
<html>
<head> 
    <style type="text/css">
        table{
            border: 2px solid black;
            vertical-align: middle;
        }
        table > thead > tr > th{
            border: 2px solid black;
            height: 30;
        }
        table > tbody > tr > td{
            border: 1px solid rgb(211,211,211);
        }
    </style>
</head>
<body>
    @if(isset($cdm_detail) && count($cdm_detail) > 0)
    <table>
        <thead>
            <tr style="background-color: #ddebf7; vertical-align: middle; ">
                <th colspan="4">PT. Yamaha Musical Products Indonesia</th>
            </tr>

            <tr style="vertical-align: middle; ">
                <th colspan="4">
                    CDM REPORT
                </th>
            </tr>

            <tr style="vertical-align: middle; ">
                <th colspan="4">
                    {{$cdm_detail[0]->gmc}} - {{$cdm_detail[0]->proses}}
                </th>
            </tr>
            <tr>
                <th></th>
            </tr>

        </thead>
        <tbody>
            <tr>
                <th colspan="4" style="border: 1px solid black"></th>
                @foreach($point_check as $point)
                <th colspan="6" style="text-align: center; border: 1px solid black">{{ $point->poin_cek }}</th>
                @endforeach
            </tr>
            <tr>
                <th style="border: 1px solid black">No</th>
                <th style="border: 1px solid black">Date</th>
                <th style="border: 1px solid black">Inputor</th>
                <th style="border: 1px solid black">Note</th>
                @foreach($point_check as $point)
                <th style="text-align: center; border: 1px solid black">Batas Bawah</th>
                <th style="text-align: center; border: 1px solid black">Batas Atas</th>
                <th style="text-align: center; border: 1px solid black">Awal</th>
                <th style="text-align: center; border: 1px solid black">Tengah</th>
                <th style="text-align: center; border: 1px solid black">Akhir</th>
                <th style="text-align: center; border: 1px solid black">Penilaian</th>
                @endforeach
            </tr>

            <?php
            $no = 1;
            for ($i=1; $i <= count($cdm_detail); $i++) { 
                if ($i == 1) {
                    echo '<tr>';
                    echo '<td>'.$no.'</td>';
                    echo '<td>'.$cdm_detail[$i-1]->tanggal.'</td>';
                    echo '<td>'.$cdm_detail[$i-1]->inputor_name.'</td>';
                    echo '<td>'.$cdm_detail[$i-1]->note.'</td>';
                } else if(($i-1) % count($point_check) == 0) {
                    echo '<tr>';
                    echo '<td>'.$no.'</td>';
                    echo '<td>'.$cdm_detail[$i-1]->tanggal.'</td>';
                    echo '<td>'.$cdm_detail[$i-1]->inputor_name.'</td>';
                    echo '<td>'.$cdm_detail[$i-1]->note.'</td>';
                }

                echo '<td>batas bawah</td>';
                echo '<td>batas atas</td>';
                echo '<td>'.$cdm_detail[$i-1]->awal.'</td>';
                echo '<td>'.$cdm_detail[$i-1]->tengah.'</td>';
                echo '<td>'.$cdm_detail[$i-1]->akhir.'</td>';
                echo '<td>'.$cdm_detail[$i-1]->penilaian.'</td>';

                if ($i % count($point_check) == 0) {
                    echo '</tr>';
                    $no++;
                }
            }
            ?>
        </tr>
    </tbody>
</table>
@endif
</body>
</html>