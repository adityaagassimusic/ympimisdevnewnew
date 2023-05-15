<!DOCTYPE html>
<html>

<head>
    <style type="text/css">
        td{
            padding-right: 5px;
            padding-left: 5px;
            padding-top: 0px;
            padding-bottom: 0px;
        }
        th{
            padding-right: 5px;
            padding-left: 5px;          
        }
    </style>
</head>

<body>
    <div style="width: 100%">
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
                alt="">
            <br><br>
            <span style="font-size: 24px">Resume MIS Ticket {{ $data['now'] }}</span>
        </center>
        <br>
        <table style="width: 100%;">
            <tr>
                <td style="width: 40%;">
                    <table style="border-collapse: collapse;">
                        <tbody>
                            <?php
                                $total = 0;
                                $open = 0;
                                $progress = 0;
                                $close = 0;
                                $hold = 0;

                                foreach($data['ticket_data'] as $ticket){
                                    $open = $ticket->jumlah_belum;
                                    $progress = $ticket->jumlah_progress;
                                    $close = $ticket->jumlah_sudah;
                                    $hold = $ticket->jumlah_tunda;
                                    $total = $ticket->jumlah_belum + $ticket->jumlah_progress + $ticket->jumlah_sudah + $ticket->jumlah_tunda;
                                }
                            ?>
                            <tr>
                                <td style="width: 60%; font-weight: bold;">Open</td>
                                <td style="width: 0.1%; font-weight: bold;">:</td>
                                <td style="width: 20%; text-align: right; font-weight: bold;"><?= $open ?> Tiket</td>
                            </tr>
                            <tr>
                                <td style="width: 60%; font-weight: bold;">In Progress</td>
                                <td style="width: 0.1%; font-weight: bold;">:</td>
                                <td style="width: 20%; text-align: right; font-weight: bold;"><?= $progress ?> Tiket</td>
                            </tr>
                            <tr>
                                <td style="width: 60%; font-weight: bold;">Close</td>
                                <td style="width: 0.1%; font-weight: bold;">:</td>
                                <td style="width: 20%; text-align: right; font-weight: bold;"><?= $close ?> Tiket</td>
                            </tr>
                            <tr>
                                <td style="width: 60%; font-weight: bold;">On Hold</td>
                                <td style="width: 0.1%; font-weight: bold;">:</td>
                                <td style="width: 20%; text-align: right; font-weight: bold;"><?= $hold ?> Tiket</td>
                            </tr>

                            <tr>
                                <td style="width: 60%; font-weight: bold;">Total</td>
                                <td style="width: 0.1%; font-weight: bold;">:</td>
                                <td style="width: 20%; text-align: right; font-weight: bold;"><?= $total ?> Tiket</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 60%;"></td>
            </tr>
        </table>
        <br>
        <br>
        <center>
            <span style="font-weight: bold; color: purple; font-size: 24px;width: 100%;">Resume Ticket Progress</span>
        </center>
        <br>
        <br>
        <table style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black;">
            <thead style="background-color: rgb(126,86,134);">
                <tr style="color: white; background-color: #7e5686">
                    <th style="width: 0.1%; border:1px solid black;">No</th>
                    <th style="width: 0.1%; border:1px solid black;">Dept</th>
                    <th style="width: 0.1%; border:1px solid black;">Judul</th>
                    <th style="width: 0.1%; border:1px solid black;">PIC</th>
                    <th style="width: 0.1%; border:1px solid black;">Presentase</th>
                    <th style="width: 0.1%; border:1px solid black;">Tanggal Mulai</th>
                    <th style="width: 0.1%; border:1px solid black;">Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                @foreach($data['ticket_resume'] as $resume)
                <tr>
                    <td style="border: 1px solid black;width: 1%;text-align: center;">{{ $resume->ticket_id }}</td>
                    <td style="border: 1px solid black;width: 1%;text-align: left;">{{ $resume->department_shortname }}</td>
                    <td style="border: 1px solid black;width: 4%;text-align: left">{{ $resume->case_title }}</td>
                    <td style="border: 1px solid black;width: 1%;text-align: left">{{ $resume->pic_shortname }}</td>
                    <td style="border: 1px solid black;width: 1%;text-align: center;color: red">{{ $resume->progress }} %</td>
                    <td style="border: 1px solid black;width: 1%;text-align: center"><?= date('d-m-Y', strtotime($resume->timeline_date)) ?></td>
                    @if($resume->timeline_day == 0)
                        <td style="border: 1px solid black;width: 1%;text-align: center">{{ $resume->timeline_day }} Hari</td>
                    @else
                        <td style="border: 1px solid black;width: 1%;text-align: center">{{ $resume->timeline_month }} Bulan</td>
                    @endif
                </tr>
                <?php $no++ ?>
                @endforeach
            </tbody>
        </table>

        <br>
        <br>
        <center>
            <span style="font-weight: bold; color: green; font-size: 24px;text-align: center;">Resume Ticket Finished (Last 2 Month)</span>
        </center>
        <br><br>
        <table style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black;">
            <thead style="background-color: rgb(126,86,134);">
                <tr style="color: white; background-color: #7e5686">
                    <th style="width: 0.1%; border:1px solid black;">No</th>
                    <th style="width: 0.1%; border:1px solid black;">Dept</th>
                    <th style="width: 0.1%; border:1px solid black;">Judul</th>
                    <th style="width: 0.1%; border:1px solid black;">PIC</th>
                    <th style="width: 0.1%; border:1px solid black;">Tanggal Mulai</th>
                    <th style="width: 0.1%; border:1px solid black;">Tanggal Selesai</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['ticket_finish'] as $resume_fin)
                <tr>
                    <td style="border: 1px solid black;width: 1%;text-align: center;">{{ $resume_fin->ticket_id }}</td>
                    <td style="border: 1px solid black;width: 1%;text-align: center;">{{ $resume_fin->department_shortname }}</td>
                    <td style="border: 1px solid black;width: 4%;text-align: left">{{ $resume_fin->case_title }}</td>
                    <td style="border: 1px solid black;width: 1%;text-align: left">{{ $resume_fin->pic_shortname }}</td>
                    <td style="border: 1px solid black;width: 1%;text-align: center"><?= date('d-m-Y', strtotime($resume_fin->timeline_date)) ?></td>
                    <td style="border: 1px solid black;width: 1%;text-align: center"><?= date('d-m-Y', strtotime($resume_fin->due_date_to)) ?></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <br>
        <center>
            <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i>
                &#8650;</span><br><br>
            <a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;"
                href="http://10.109.52.4/mirai/public/index/ticket/monitoring/mis">&nbsp;&nbsp;&nbsp; MIS Ticket Data&nbsp;&nbsp;&nbsp;</a>
        </center>
        <br>
    </div>
</body>

</html>
