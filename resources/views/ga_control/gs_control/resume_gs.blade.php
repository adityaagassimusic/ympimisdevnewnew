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
        table > tfoot > tr > th{
            border: 1px solid rgb(211,211,211);
        }

        .nethink {
            background-color: #FFCCFF;
        }
        .posthink {
            background-color: #CCFFFF;
        }
        .putih{
            background-color: #FFF;
        }
        img {
            transform: rotate(90deg)
        }

    </style>
</head>
<body>
    @if(isset($detail) && count($detail) > 0)
    <table>
        <thead>
            <tr style="background-color: #ddebf7; vertical-align: middle; ">
                <th colspan="9" style="text-align: center;">PT. Yamaha Musical Products Indonesia</th>
            </tr>

            <tr style="vertical-align: middle; ">
                <th colspan="9" style="text-align: center;">
                  RESUME DAILY JOB GS
              </th>
          </tr>

          <tr></tr>
<!--             
            <tr style="vertical-align: middle;">
                <th colspan="2" style="text-align:left">Nama Petugas<br>パトロール担当者</th> 
            </tr>
 --><!-- 
            <tr style="vertical-align: middle;">
                <th colspan="2">Tanggal<br>日付</th> 
                
            </tr>

            <tr></tr> -->
        </thead>
        <tbody>

            <tr>
                <th style="border: 1px solid black;">No</th>
                <th style="border: 1px solid black;">Tanggal</th>
                <th style="border: 1px solid black;">Nama</th>
                <th style="border: 1px solid black;">Lokasi</th>
                <th style="border: 1px solid black;">Pekerjaan</th>
                <th style="border: 1px solid black;">Start</th>
                <th style="border: 1px solid black;">Finish</th>
                <th style="border: 1px solid black;">Foto Before</th>
                <th style="border: 1px solid black;">Foto After</th>
            </tr>

            <?php 
            $num = 1;
            $amount=0; 
            ?>

            @foreach($detail as $audit)


            <tr>
                <td style="vertical-align: middle;text-align: left;width: 10" class="putih">{{ $num++ }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20" class="putih"><?php echo date('d-m-Y', strtotime($audit->datess)) ?></td>
                <td style="vertical-align: middle;text-align: left;width: 20" class="putih">{{ $audit->name_gs }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20" class="putih">{{ $audit->category }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20" class="putih">{{ $audit->list_job }}</td>
                <td style="vertical-align: middle;text-align: left;width: 20" class="putih"><b>{{ $audit->request_at }}</b></td>
                <td style="vertical-align: middle;text-align: left;width: 20" class="putih"><b>{{ $audit->finished_at }}</b></td>
                <?php 

                $potos = explode(', ', $audit->img_before);

                if ($audit->img_before) {
                    foreach ($potos as $phs) {
                        echo "<td>";
                        echo "<img src='".public_path("images/ga/gs_control").'/'.$phs."' width='200px' style='transform: rotate(90deg);'>";
                        echo "</td>";
                    }
                } else {
                    echo "<td>";
                    echo "tidak ada foto";
                    echo "</td>";
                }
                ?>

                <?php 

                $poto = explode(', ', $audit->img_after);

                if ($audit->img_after) {
                    foreach ($poto as $ph) {
                        echo "<td>";
                        echo "<img src='".public_path("images/ga/gs_control").'/'.$ph."' width='200px' style='transform: rotate(90deg);'>";
                        echo "</td>";
                    }
                } else {
                    echo "<td>";
                    echo "tidak ada foto";
                    echo "</td>";
                }

                ?>

            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>