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
    <?php $material = $data['materials']; ?>
    <div>
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
                alt=""><br>
            <p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>
            <span style="font-weight: bold; color: green; font-size: 24px;">New Item Request
                Notification<br>新アイテムリクエストの通知</span>
            <?php if ($data['position'] == 'Complete BOM') { ?>
            <span style="font-weight: bold; color: green; font-size: 24px;">Item Request has been Successfully Registered
                <br> Please update Extra Order Form</span>
            <?php } if($data['position'] == 'Request Sales Price') { ?>
            <span style="font-weight: bold; color: green; font-size: 24px;">Item Request has been Successfully Registered
                <br> Please upload Sales Price</span>
            <?php } ?>
        </center>
        <br>
        <div style="width: 50%; margin: auto;">
            <br>
            <table style="border:1px solid black; border-collapse: collapse; width: 100%;">
                <tbody>
                    <tr>
                        <th style="border:1px solid black; background-color: #aee571;" colspan="2">Description
                            {{ $data['position'] }}</th>
                    </tr>
                    <tr>
                        <th style="border:1px solid black; background-color: #fffcb7; text-align: left;">No Approval
                        </th>
                        <td style="border:1px solid black">{{ $material->remark }}</td>
                    </tr>
                    <tr>
                        <th style="border:1px solid black; background-color: #fffcb7; text-align: left;">Extra Order
                            Number</th>
                        <td style="border:1px solid black">{{ $material->eo_number }}</td>
                    </tr>
                    <tr>
                        <th style="border:1px solid black; background-color: #fffcb7; text-align: left;">Material_Buyer
                        </th>
                        <td style="border:1px solid black">{{ $material->material_number_buyer }}</td>
                    </tr>
                    <tr>
                        <th style="border:1px solid black; background-color: #fffcb7; text-align: left;">Material_YMPI
                        </th>
                        <td style="border:1px solid black">{{ $material->material_number }}</td>
                    </tr>
                    <tr>
                        <th style="border:1px solid black; background-color: #fffcb7; text-align: left;">Description
                        </th>
                        <td style="border:1px solid black">{{ $material->description }}</td>
                    </tr>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <center>
            <?php if ($data['position'] == 'Complete BOM') { ?>
            <a href="{{ url('index/extra_order/detail/' . $material->eo_number) }}">&#10148; Click here to Update Extra
                Order Progress</a>
            <?php } if($data['position'] == 'Request Sales Price') { ?>
            <a href="{{ url('index/sakurentsu/list_material/') }}">&#10148; Click here to Upload Sales Price</a>
            <?php } ?>
        </center>
    </div>
</body>

</html>
