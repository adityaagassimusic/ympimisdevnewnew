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
                <th style="font-weight: bold;" rowspan="2">No.</th>
                <th style="font-weight: bold;" rowspan="2">Loc</th>
                <th style="font-weight: bold;" rowspan="2">Date</th>
                <th style="font-weight: bold;" rowspan="2">Time</th>
                <th style="font-weight: bold;" rowspan="2">Inspector</th>
                <th style="font-weight: bold;" rowspan="2">Inspection Level</th>
                <th style="font-weight: bold;" rowspan="2">Material</th>
                <th style="font-weight: bold;" rowspan="2">Desc</th>
                <th style="font-weight: bold;" rowspan="2">HPL</th>
                <th style="font-weight: bold;" rowspan="2">Qty Rec</th>
                <th style="font-weight: bold;" rowspan="2">Qty Check</th>
                <th style="font-weight: bold;" rowspan="2">Defect</th>
                <th style="font-weight: bold;" colspan="2">Jumlah NG</th>
                <th style="font-weight: bold;" rowspan="2">Note</th>
                <th style="font-weight: bold;" rowspan="2">NG Ratio</th>
            </tr>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th style="font-weight: bold;">Repair</th>
                <th style="font-weight: bold;">Scrap</th>
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
                if ($datas->ng_qty != null) {
                    $note_ng = explode("_", $datas->note_ng);
                }else{
                    $note_ng = "";
                }
                $jumlah = count($ng_name);
            }else{
                $jumlah = 1;
            }
            ?>
            <tr>
                <td rowspan="{{$jumlah}}">{{ $index++ }}</td>
                <td rowspan="{{$jumlah}}"> {{$datas->location}}</td>
                <td rowspan="{{$jumlah}}"> {{explode(' ', $datas->created)[0]}} </td>
                <td rowspan="{{$jumlah}}"> {{explode(' ', $datas->created)[1]}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->employee_id}} - {{$datas->name}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->inspection_level}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->material_number}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->material_description}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->hpl}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->qty_production}} </td>
                <td rowspan="{{$jumlah}}"> {{$datas->qty_check}} </td>
                <?php if ($datas->ng_name != null) { ?>
                    <td>{{$ng_name[0]}}</td>
                    <?php if ($status_ng[0] == 'Repair') {?>
                        <td>{{$ng_qty[0]}}</td>
                        <td></td>
                    <?php }else if ($status_ng[0] == 'Scrap') {?>
                        <td></td>
                        <td>{{$ng_qty[0]}}</td>
                    <?php }
                    if ($note_ng != '') {?>
                        <td>{{$note_ng[0]}}</td>
                    <?php }else{ ?>
                        <td>{{$datas->note_all}}</td>
                    <?php } ?>
                <?php }else{ ?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{$datas->note_all}}</td>
                <?php } ?>
                <td style="vertical-align:middle" rowspan="{{$jumlah}}">{{round($datas->ng_ratio,2)}}</td>
            </tr>
            <?php if ($datas->ng_name != null && count($ng_name) > 1) { 
                for($i = 1 ;$i < $jumlah; $i++){ ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$ng_name[$i]}}</td>
                        <?php if ($status_ng[$i] == 'Repair') {?>
                            <td>{{$ng_qty[$i]}}</td>
                            <td></td>
                        <?php } else if ($status_ng[$i] == 'Scrap') {?>
                            <td></td>
                            <td>{{$ng_qty[$i]}}</td>
                        <?php }
                        if (ISSET($note_ng[$i])) {?>
                            <td>{{$note_ng[$i]}}</td>
                        <?php }else{ ?>
                            <td>{{$datas->note_all}}</td>
                        <?php } ?>
                    </tr>
                <?php } } ?>
                @endforeach
            </tbody>
        </table>
        @endif
    </body>
    </html>