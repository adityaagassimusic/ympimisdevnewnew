<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        thead>tr>th{
            border:1px solid black;
        }
        tbody>tr>td{
            border:1px solid black;
        }
        tfoot>tr>th{
            border:1px solid black;
        }
        
    </style>
</head>
<body>
    @if(isset($datas) && count($datas) > 0)
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold;">No.</th>
                <th style="font-weight: bold;">ID</th>
                <th style="font-weight: bold;">Loc</th>
                <th style="font-weight: bold;">Date</th>
                <th style="font-weight: bold;">Time</th>
                <th style="font-weight: bold;">Inspector</th>
                <th style="font-weight: bold;">Inspection Level</th>
                <th style="font-weight: bold;">Material</th>
                <th style="font-weight: bold;">Desc</th>
                <th style="font-weight: bold;">HPL</th>
                <th style="font-weight: bold;">Qty Rec</th>
                <th style="font-weight: bold;">Qty Check</th>
                <th style="font-weight: bold;">Defect</th>
                <th style="font-weight: bold;">Repair</th>
                <th style="font-weight: bold;">Scrap</th>
                <th style="font-weight: bold;">Note NG</th>
                <th style="font-weight: bold;">Note All</th>
                <th style="font-weight: bold;">NG Ratio</th>
            </tr>
        </thead>
        <tbody>
            <?php $index = 1; ?>
            @foreach($datas as $datas)
            <?php
            $jumlah = 0;
            if ($datas->ng_name != null) {
                $ng_name = explode("_", $datas->ng_name);
                $ng_qty = explode("_", $datas->ng_qty);
                $status_ng = explode("_", $datas->status_ng);
                if ($datas->note_ng != null) {
                    $note_ng = explode("_", $datas->note_ng);
                }else{
                    $note_ng = "";
                }
                $jumlah = count($ng_name);
            }else{
                $jumlah = 1;
            }
            ?>
            <?php for($i = 0; $i < $jumlah ; $i++){ ?>
                <tr>
                    <td>{{ $index++ }}</td>
                    <td> {{$datas->id_log}}</td>
                    <td> {{$datas->location}}</td>
                    <td> {{explode(' ', $datas->created)[0] }} </td>
                    <td> {{explode(' ', $datas->created)[1] }} </td>
                    <td> {{$datas->employee_id}} - {{$datas->name}} </td>
                    <td> {{$datas->inspection_level}} </td>
                    <td> {{$datas->material_number}} </td>
                    <td> {{$datas->material_description}} </td>
                    <td> {{$datas->hpl}} </td>
                    <td> {{$datas->qty_production}} </td>
                    <td> {{$datas->qty_check}} </td>
                    <?php if ($datas->ng_name != null && count($ng_name) > 0) { ?>
                        <td>{{$ng_name[$i]}}</td>
                        <?php if ($status_ng[$i] == 'Repair') { ?>
                            <td>{{$ng_qty[$i]}}</td>
                            <td></td>
                        <?php }else if ($status_ng[$i] == 'Scrap') { ?>
                            <td></td>
                            <td>{{$ng_qty[$i]}}</td>
                        <?php } ?>

                        <?php if ($datas->note_ng != null) { ?>
                            <?php if (count($note_ng) > $i){ ?>
                                <td>{{$note_ng[$i]}}</td>
                            <?php }else{ ?>
                                <td></td>
                            <?php } ?>
                        <?php }else{ ?>
                            <td></td>
                        <?php } ?>
                    <?php } else { ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    <?php } ?>

                    <td>{{$datas->note_all}}</td>
                    <td style="vertical-align:middle">{{round($datas->ng_ratio,2)}}</td>
                </tr>
            <?php } ?>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>