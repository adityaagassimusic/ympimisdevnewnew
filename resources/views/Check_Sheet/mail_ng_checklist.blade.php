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

        .status {
            font-size: 20pt;
            font-weight: bold;
            color: red;
            margin-bottom: 0px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div>
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"><br>
            <p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>
            <span style="font-weight: bold; color: black; font-size: 24px;"></span><br>
            <p class="status"></i>negative findings on checking the container condition checklist!</p>
        </center>
        <br>
        <div style="width: 80%; margin: auto;">
            <table style="border:1px solid black; border-collapse: collapse; width: 100%; font-size: 10pt;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #63ccff;">Area</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Point Checklist</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Result</th>
                        <th style="border:1px solid black; background-color: #63ccff;">Evidence</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < count($data['ng']); $i++)
                        <tr>
                            <td style="border: 1px solid black; width: 10%; text-align: center;">
                                {{ $data['ng'][$i]->area }}
                            </td>
                            <td style="border: 1px solid black; width: 30%; text-align: left;">
                                {{ $data['ng'][$i]->point_check }}
                            </td>
                            <td
                                style="border: 1px solid black; width: 10%; text-align: center; color: red; font-weight: bold;">
                                {{ $data['ng'][$i]->result }}
                            </td>
                            <td style="border: 1px solid black; width: 50%; text-align: center;">
                                @for ($j = 0; $j < count($data['photo']); $j++)
                                    @if ($data['ng'][$i]->area == $data['photo'][$j]->area)
                                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/files/checksheet/checklist_evidence/' . $data['photo'][$j]->source))) }}"
                                            style="width: 150px; height: 70px; margin: 1%;" />
                                    @endif
                                @endfor
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <br>
        <br>
        <center>
            <span style="font-weight: bold;">&#8650; <i>Click Here For</i> &#8650;</span><br>
            <a style="background-color: #25b2fd; text-decoration: none; color: black;"
                href="{{ url('show/CheckSheet/' . $data['id']) }}">&nbsp;Checklist Detail&nbsp;</a>
        </center>
        <br>
        <br>
    </div>
</body>

</html>
