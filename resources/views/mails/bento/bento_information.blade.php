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
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
                alt=""><br>
            <p>返信不要の自動通知です。</p>
            <?php
            if ($data['remark'] == 'reminder') {
                print_r('<span style="font-weight: bold; color: green; font-size: 24px;">本メールは毎週末に送られる和食弁当の注文催促メールとなります。</span>');
            } else {
                print_r('<span style="font-weight: bold; color: green; font-size: 24px;">あなたのご注文はYMPIにより確認済み。</span>');
            }
            ?>
            <br>
            <br>
            <span style="font-weight: bold;">*備考:</span><br>
            <span style="">&nbsp;&nbsp;&#9744; = 注文なし&nbsp;&nbsp;</span>
            <span style="background-color: #ccff90;">&nbsp;&nbsp;&#9745; = 承認済み&nbsp;&nbsp;</span>
            <br>
            <br>
            <center>
                <span style="font-weight: bold; font-size: 2vw;">{{ date('Y', strtotime($data['month'])) }}年
                    {{ date('m', strtotime($data['month'])) }}月</span>
            </center>
            <br>
            <table style="border:1px solid black;border-collapse: collapse;" width="95%">
                <thead style="background-color: #63ccff">
                    <tr>
                        <th style="border:1px solid black; width: 9%; font-size: 11px; padding: 0px 0px 0px 0px;">氏名
                        </th>
                        <?php
                        for ($i = 0; $i < count($data['calendars']); $i++) {
                            if ($data['calendars'][$i]['remark'] == 'H') {
                                print_r('<th style="border:1px solid black; background-color: grey; width: 1%; font-size:11px; padding: 0px 0px 0px 0px;">' . $data['calendars'][$i]['header'] . '<br>休日</th>');
                            } else {
                                print_r('<th style="border:1px solid black; width: 1%; font-size:11px; padding: 0px 0px 0px 0px;">' . $data['calendars'][$i]['header'] . '</th>');
                            }
                        } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    for ($i = 0; $i < count($data['japaneses']); $i++) {
                        print_r('<tr>');
                    
                        print_r('<td style="border: 1px solid black; padding: 0px 0px 0px 0px; font-size:11px; text-align: center;">' . $data['japaneses'][$i]->employee_name . '<br>' . $data['japaneses'][$i]->employee_name_jp . '</td>');
                    
                        for ($j = 0; $j < count($data['calendars']); $j++) {
                            $inserted = false;
                    
                            for ($k = 0; $k < count($data['bento_lists']); $k++) {
                                if ($data['calendars'][$j]['due_date'] == $data['bento_lists'][$k]->due_date && $data['bento_lists'][$k]->employee_id == $data['japaneses'][$i]->employee_id && $data['bento_lists'][$k]->status == 'Approved') {
                                    print_r('<td style="border: 1px solid black; text-align: center; background-color: #ccff90; padding: 0px 0px 0px 0px; font-size:16px;">&#9745;</td>');
                                    $inserted = true;
                                }
                            }
                            if (!$inserted) {
                                if ($data['calendars'][$j]['remark'] == 'H') {
                                    print_r('<td style="border: 1px solid black; background-color: #5b5b5b; text-align: center; padding: 0px 0px 0px 0px; font-size:16px;">&#9744;</td>');
                                } else {
                                    print_r('<td style="border: 1px solid black; text-align: center; padding: 0px 0px 0px 0px; font-size:16px;">&#9744;</td>');
                                }
                            }
                        }
                        print_r('</tr>');
                    }
                    ?>
                </tbody>
            </table>
            <br>
            <br>
            注文は遅くても前日となります。
            <br>
            <a
                href="http://10.109.52.4/mirai/public/index/ga_control/bento_japanese/{{ date('F Y', strtotime($data['month'])) }}">&#10148;
                注文を作成又は変更したい場合はこちらをクリック</a>
        </center>
    </div>
</body>

</html>
