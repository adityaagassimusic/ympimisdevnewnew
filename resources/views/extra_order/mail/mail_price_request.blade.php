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
    </style>
</head>

<body>
    <div>
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
                alt=""><br>
            <p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>
            <span style="font-weight: bold; color: purple; font-size: 24px;">New Sales Price Request
                Notification<br>新しい販売価格リクエストの通知</span>
        </center>
        <br>
        <center>
            There is a new item request without sales price or expired sales price from EO with number
            <b>{{ $data['eo_number'] }}</b><br>
            Please process the sales price as soon as possible.<br>
        </center>
        <div style="width: 60%; margin: auto;">
            <br>
            <span style="font-weight: bold;">Request List:</span>
            <table style="border:1px solid black; border-collapse: collapse; width: 100%;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #aee571;">#</th>
                        <th style="border:1px solid black; background-color: #aee571;">Material_Buyer</th>
                        <th style="border:1px solid black; background-color: #aee571;">Material_YMPI</th>
                        <th style="border:1px solid black; background-color: #aee571;">Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    for ($i = 0; $i < count($data['extra_order']); $i++) {
                        print_r('<tr>');
                        print_r('<td style="border: 1px solid black; width: 1%; text-align: center;">' . ++$count . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%;">' . $data['extra_order'][$i]['material_number_buyer'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 1%;">' . $data['extra_order'][$i]['material_number'] . '</td>');
                        print_r('<td style="border: 1px solid black; width: 10%;">' . $data['extra_order'][$i]['description'] . '</td>');
                        print_r('</tr>');
                    } ?>
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <center>
            <span style="font-weight: bold; background-color: orange;">&#8650;<i>Click here to</i>&#8650;</span><br>
            <a href="{{ url('index/extra_order/detail/' . $data['eo_number']) }}">&#10148; Check the Extra Order</a><br>
            <a href="{{ url('index/sakurentsu/list_material') }}">&#10148; Update Sales Price</a>
        </center>
    </div>
</body>

</html>
