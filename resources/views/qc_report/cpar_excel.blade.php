<!DOCTYPE html>
<html>
<head>
    <style type="text/css">

    </style>
</head>
<body>
    @if(isset($detail) && count($detail) > 0)
    <table>
        <thead>
            <tr style="vertical-align: middle; ">
                <th colspan="8" style="text-align: center;">
                    List Data CPAR MIRAI
                </th>
            </tr>

            <tr></tr>
        </thead>
        <tbody>

            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>No CPAR</th>
                <th>Judul Komplain</th>
                <th>Departemen</th>
                <th>Jenis Komplain</th>
                <th>Kategori CPAR</th>
                <th>Status Meeting</th>
            </tr>

            <?php 
                $num = 1;
                $amount=0; 
            ?>

            @foreach($detail as $cpar)

            <tr>
                <td style="vertical-align: middle;text-align: left;width: 5">{{ $num++ }}</td>
                <td style="vertical-align: middle;text-align: left;width: 10"><?php echo date('d-m-y', strtotime($cpar->tgl_permintaan)) ?></td>
                <td style="vertical-align: middle;text-align: left;width: 20">{{ $cpar->cpar_no }}</td>
                <td style="vertical-align: middle;text-align: left;width: 40">{{ $cpar->judul_komplain }}</td>
                <td style="vertical-align: middle;text-align: left;width: 40">{{ $cpar->department_name }}</td>
                @if($cpar->kategori_komplain == "Non YMMJ")
                    <td style="vertical-align: middle;text-align: left;width: 25">Supplier</td>
                @elseif($cpar->kategori_komplain == "Ketidaksesuaian Kualitas")
                    <td style="vertical-align: middle;text-align: left;width: 25">Internal</td>
                @elseif($cpar->kategori_komplain == "NG Jelas")
                    <td style="vertical-align: middle;text-align: left;width: 25">Eksternal - NG Jelas</td>
                @elseif($cpar->kategori_komplain == "KD Parts")
                    <td style="vertical-align: middle;text-align: left;width: 25">Eksternal - KD Parts</td>
                @elseif($cpar->kategori_komplain == "FG")
                    <td style="vertical-align: middle;text-align: left;width: 25">Eksternal - FG</td>
                @elseif($cpar->kategori_komplain == "Market Claim")
                    <td style="vertical-align: middle;text-align: left;width: 25">Market Claim</td>
                @elseif($cpar->kategori_komplain == "Check Day")
                    <td style="vertical-align: middle;text-align: left;width: 25">Check Day</td>
                @else
                    <td style="vertical-align: middle;text-align: left;width: 25">None</td>
                @endif
                <td style="vertical-align: middle;text-align: left;width: 25">{{ $cpar->kategori_approval }}</td>

                @if($cpar->kategori_meeting == "Open")
                    <td style="vertical-align: middle;text-align: left;width: 25">Meeting Open</td>
                @elseif($cpar->kategori_meeting == "CloseRevised")
                    <td style="vertical-align: middle;text-align: left;width: 25">Close Dengan Revisi</td>
                @elseif($cpar->kategori_meeting == "Close")
                    <td style="vertical-align: middle;text-align: left;width: 25">Meeting Closed</td>
                @else
                    <td style="vertical-align: middle;text-align: left;width: 25">Belum Meeting</td>
                @endif
                
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>