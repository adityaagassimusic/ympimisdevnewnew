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
            <p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
            <span style="font-weight: bold; color: purple; font-size: 24px;">MIS TICKETING SYSTEM (MISチケット依頼)</span><br>
            <span style="font-weight: bold; font-size: 26px;">{{ $data['ticket']->ticket_id }}</span>
            @if ($data['code'] == 'fully_approved')
                <span style="font-weight: bold; font-size: 26px; color: green;">
                    (Fully Approved)
                </span>
            @endif
            @if ($data['code'] == 'rejected')
                <span style="font-weight: bold; font-size: 26px; color: red;">
                    (Rejected)
                    <br>
                    @if($data['ticket']->reject_reason != null || $data['ticket']->reject_reason != '')
                        Reason Reject : {{$data['ticket']->reject_reason}}
                    @endif
                </span>
            @endif
        </center>
        <br>
        <div style="width: 90%; margin: auto;">
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td style="padding: 0; vertical-align: top;">
                            <table>
                                <tbody>
                                    <tr>
                                        <td style="font-weight: bold;">Pemohon</td>
                                        <td>:</td>
                                        <td>{{ $data['ticket']->user->username }} - {{ $data['ticket']->user->name }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">Department</td>
                                        <td>:</td>
                                        <td>{{ $data['ticket']->department }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td style="padding: 0; vertical-align: top; text-align: right;">
                            <table>
                                <tbody>
                                    <tr>
                                        <td style="font-weight: bold;">No Dok.</td>
                                        <td style="font-weight: bold;">:</td>
                                        <td>YMPI/MIS/FM/002</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">No Rev.</td>
                                        <td>:</td>
                                        <td>01</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold;">Tanggal</td>
                                        <td style="font-weight: bold;">:</td>
                                        <td>08 April 2021</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Jenis Permintaan</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['ticket']->category }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Prioritas</td>
                        <td style="font-weight: bold;">:</td>
                        <td>
                            @if ($data['ticket']->priority == 'Very High')
                                <span
                                    style="background-color: #ff6090; font-weight: bold;">&nbsp;&nbsp;&nbsp;{{ $data['ticket']->priority }}&nbsp;&nbsp;&nbsp;</span>
                            @elseif($data['ticket']->priority == 'High')
                                <span
                                    style="background-color: #ffee58; font-weight: bold;">&nbsp;&nbsp;&nbsp;{{ $data['ticket']->priority }}&nbsp;&nbsp;&nbsp;</span>
                            @else
                                <span
                                    style="background-color: #ccff90; font-weight: bold;">&nbsp;&nbsp;&nbsp;{{ $data['ticket']->priority }}&nbsp;&nbsp;&nbsp;</span>
                            @endif
                        </td>
                    </tr>
                    @if ($data['ticket']->priority == 'High' || $data['ticket']->priority == 'Very High')
                        <tr>
                            <td style="font-weight: bold;">Alasan Prioritas</td>
                            <td style="font-weight: bold;">:</td>
                            <td>{{ $data['ticket']->priority_reason }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td style="font-weight: bold;">Judul</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['ticket']->case_title }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Penjelasan</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['ticket']->case_description }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">Target Penyelesaian</td>
                        <td style="font-weight: bold;">:</td>
                        <!-- {{ date('d F Y', strtotime($data['ticket']->due_date_from)) }} s/d -->
                        <td>
                            {{ date('d F Y', strtotime($data['ticket']->due_date_to)) }}
                        </td>
                    </tr>
                    @if ($data['ticket']->document != '-')
                        <tr>
                            <td style="font-weight: bold;">Digitalisasi Dokumen</td>
                            <td style="font-weight: bold;">:</td>
                            <td>{{ $data['ticket']->document }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <br>
            <table style="border:1px solid black;border-collapse: collapse;" width="100%">
                <thead>
                    <tr>
                        <th style="border:1px solid black; width: 50%;">Kondisi Sekarang</th>
                        <th style="border:1px solid black; width: 50%;">Kondisi Yang Diharapkan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border:1px solid black; vertical-align: top;"><?= $data['ticket']->case_before ?>
                        </td>
                        <td style="border:1px solid black; vertical-align: top;"><?= $data['ticket']->case_after ?></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <span style="font-weight: bold;">TARGET</span>
            <table style="border:1px solid black; border-collapse: collapse;" width="100%">
                <thead>
                    <tr>
                        <th style="border:1px solid black; width: 1%;">Kategori</th>
                        <th style="border:1px solid black; width: 6%;">Penjelasan</th>
                        <th style="border:1px solid black; width: 1%;">Nominal</th>
                        <!-- <th style="border:1px solid black; width: 1%;">Satuan</th> -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_amount = 0;
                    for ($i = 0; $i < count($data['costdown']); $i++) {
                        print_r('<tr>');
                        print_r('<td style="border: 1px solid black;">' . $data['costdown'][$i]['category'] . '</td>');
                        print_r('<td style="border: 1px solid black;">' . $data['costdown'][$i]['cost_description'] . '</td>');
                        print_r('<td style="border: 1px solid black; text-align: right;">' . $data['costdown'][$i]['cost_amount'] . '  ' . $data['costdown'][$i]['remark'] . '</td>');
                        // print_r('<td style="border: 1px solid black;"></td>');
                        print_r('</tr>');
                        // $total_amount += $data['costdown'][$i]['cost_amount'];
                    }
                    ?>
                </tbody>
                <!-- <tfoot>
                    <tr>
                        <th style="border: 1px solid black;">Total</th>
                        <th style="border: 1px solid black;"></th>
                        <th style="border: 1px solid black; text-align: right;"><?php echo $total_amount; ?></th>
                    </tr>
                </tfoot> -->
            </table>
            <br>
            <table style="border: 1px solid black; border-collapse: collapse; width: 40%;" align="right">
                <thead>
                    <tr>
                        <?php
                        for ($i = 0; $i < count($data['approver']); $i++) {
                            print_r('<th style="border: 1px solid black; width: 1%;">' . $data['approver'][$i]['remark'] . '</th>');
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php
                        for ($i = 0; $i < count($data['approver']); $i++) {
                            print_r('<th style="border: 1px solid black; width: 1%; height: 50px;">' . $data['approver'][$i]['status'] . '<br>' . $data['approver'][$i]['approved_at'] . '</th>');
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php
                        for ($i = 0; $i < count($data['approver']); $i++) {
                            print_r('<th style="border: 1px solid black; width: 1%;">' . $data['approver'][$i]['approver_name'] . '</th>');
                        }
                        ?>
                    </tr>
                </thead>
            </table>
            <br>
            <br>
            @if ($data['code'] != 'fully_approved') 
                @if ($data['code'] != 'rejected')
                <center>
                    @if ($data['ticket']->priority == 'Very High' || $data['ticket']->priority == 'High')
                        Permintaan ini dengan <span style="color: red;">prioritas tinggi</span> apakah anda akan
                        menyetujui permintaan ini?
                    @else
                        Apakah anda akan menyetujui permintaan ini?
                    @endif
                    <br>
                    <table style="width: 50%">
                        <tr>
                            @if ($data['ticket']->priority == 'Very High' || $data['ticket']->priority == 'High')
                                <th style="width: 1%; font-weight: bold; color: black;">
                                    <a style="text-align: center; background-color: #ccff90; text-decoration: none; color: black;"
                                        href="{{ url('approval/ticket?status=Approved&ticket_id=' . $data['ticket']->ticket_id . '&code=' . $data['code']) }}">Approve<br>As
                                        <i style="color: red;">High</i> Priority</a>
                                </th>
                                <th style="width: 1%; font-weight: bold; color: black;">
                                    <a style="text-align: center; background-color: #ccff90; text-decoration: none; color: black;"
                                        href="{{ url('approval/ticket?status=Approved&ticket_id=' . $data['ticket']->ticket_id . '&code=' . $data['code'] . '&priority=normal') }}">Approve<br>As
                                        <i style="color: green;">Normal</i> Priority</a>
                                </th>
                            @else
                                <th style="width: 1%; font-weight: bold; color: black;">
                                    <a style="text-align: center; background-color: #ccff90; text-decoration: none; color: black;"
                                        href="{{ url('approval/ticket?status=Approved&ticket_id=' . $data['ticket']->ticket_id . '&code=' . $data['code']) }}">Approve</a>
                                </th>
                            @endif
                            <th style="width: 1%; font-weight: bold; color: black;">
                                <a style="text-align: center; background-color: #ff6090; text-decoration: none; color: black;"
                                    href="{{ url('approval/ticket_reject?status=Rejected&ticket_id=' . $data['ticket']->ticket_id . '&code=' . $data['code']) }}">Reject</a>
                            </th>
                        </tr>
                    </table>
                </center>
                @endif
            @else
                <center>
                    Cek pada link berikut untuk melakukan pengaturan.
                    <br>
                    <a href="{{ url('index/ticket/detail/' . $data['ticket']->ticket_id) }}">MIS Ticketing System</a>
                </center>
            @endif
        </div>
    </div>
</body>

</html>
