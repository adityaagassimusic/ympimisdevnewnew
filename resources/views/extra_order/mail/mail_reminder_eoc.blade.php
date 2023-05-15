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

        * {
            font-family: Sans-serif;
        }

        .eo_number {
            font-size: 4vw;
            font-weight: bold;
        }

        .status {
            font-size: 20pt;
            font-weight: bold;
            color: red;
            margin-bottom: 0px;
        }

        .message {
            font-size: 16pt;
            margin-top: 0px;
        }
    </style>
</head>

<body>
    <div>
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
                alt=""><br>
            <p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>
            <br>
            <p class="status"></i>REMINDER APPROVAL EXTRA ORDER CONFIRMATION!</p>
        </center>
        <br>
        <div style="width: 100%; margin: auto;">
            <center>
                <table style="border:1px solid black; border-collapse: collapse; width: 50%; font-size: 10pt;">
                    <thead>
                        <tr>
                            <th style="border:1px solid black; background-color: #63ccff;">Employee ID</th>
                            <th style="border:1px solid black; background-color: #63ccff;">Name</th>
                            <th style="border:1px solid black; background-color: #63ccff;">Count EO</th>
                            <th style="border:1px solid black; background-color: #63ccff;">Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < count($data['outstanding']); $i++)
                            <tr>
                                <td style="border: 1px solid black; width: 20%; text-align: center;">
                                    {{ $data['outstanding'][$i]->approver_id }}
                                </td>
                                <td style="border: 1px solid black; width: 50%; text-align: left;">
                                    {{ $data['outstanding'][$i]->approver_name }}
                                </td>
                                <td style="border: 1px solid black; width: 2%; text-align: right;">
                                    {{ $data['outstanding'][$i]->qty }}
                                </td>
                                <td style="border: 1px solid black; width: 10%; text-align: center;">
                                    <a
                                        href='{{ url('http://10.109.52.4/mirai/public/index/extra_order/approval_monitoring?submit_from=&submit_to=&approver_id=' . $data['outstanding'][$i]->approver_id) }}'>
                                        check
                                    </a>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </center>
        </div>
        <br>
        <br>

    </div>
</body>

</html>
