<!DOCTYPE html>
<html>

<head>
    <style type="text/css">
        * {
            font-family: Sans-serif;
        }

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

        .eo_number {
            font-size: 4vw;
            font-weight: bold;
        }

        .status {
            font-size: 20pt;
            font-weight: bold;
            color: red;
            margin-bottom: 0px;
            text-transform: uppercase;
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
            <p class="status"></i>Minus Work Order Notification for Extra Order!</p>
        </center>
        <br>
        <div style="width: 80%; margin: auto;">
            <table style="border:1px solid black; border-collapse: collapse; width: 100%; font-size: 10pt;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #63ccff;">Material</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Description</th>
                        <th style="border:1px solid black; background-color: #63ccff;">SLoc</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Qty</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Avail. WO</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Minus</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < count($data['minus_wo']); $i++)
                        <tr>
                            <td style="border: 1px solid black; width: 10%; text-align: center;">
                                {{ $data['minus_wo'][$i]['material_number'] }}
                            </td>
                            <td style="border: 1px solid black; width: 50%; text-align: left;">
                                {{ $data['minus_wo'][$i]['description'] }}
                            </td>
                            <td style="border: 1px solid black; width: 10%; text-align: center;">
                                {{ $data['minus_wo'][$i]['storage_location'] }}
                            </td>
                            <td style="border: 1px solid black; width: 10%; text-align: right;">
                                {{ $data['minus_wo'][$i]['qty'] * -1 }}
                            </td>
                            <td style="border: 1px solid black; width: 10%; text-align: right;">
                                {{ round($data['minus_wo'][$i]['available_quantity'], 0) }}
                            </td>
                            <td style="border: 1px solid black; width: 10%; text-align: right;">
                                {{ round($data['minus_wo'][$i]['minus'], 0) }}
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <br>
        <br>

    </div>
</body>

</html>
