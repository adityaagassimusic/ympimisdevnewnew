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
                <th style="font-weight: bold;">No Slip</th>
                <th style="font-weight: bold;">GMC</th>
                <th style="font-weight: bold;">Material</th>
                <th style="font-weight: bold;">Issue Loc</th>
                <th style="font-weight: bold;">Category</th>
                <th style="font-weight: bold;">Type</th>
                <th style="font-weight: bold;">Receive_Loc</th>
                <th style="font-weight: bold;">Qty Slip</th>
                <th style="font-weight: bold;">Status</th>
                <th style="font-weight: bold;">Reason</th>
                <th style="font-weight: bold;">Diffect</th>
                <th style="font-weight: bold;">Qty Diffect</th>
                <th style="font-weight: bold;">No Invoice</th>
                <th style="font-weight: bold;">Printed At</th>
                <th style="font-weight: bold;">Printed By</th>
                <th style="font-weight: bold;">WH Receive At</th>
                <th style="font-weight: bold;">WH Receive By</th>
                <th style="font-weight: bold;">Canceled At</th>
                <th style="font-weight: bold;">Canceled By</th>
            </tr>
        </thead>

        <tbody>
            <?php $index = 1; ?>
            @foreach($datas as $datas)
            <?php $diffect = explode('/', $datas->summary)?>

            <?php
            $jumlah = 0;
            if ($datas->summary != null) {
                $diffect = explode("/", $datas->summary);
                $jumlah = count($diffect);
            }else{
                $jumlah = 1;
            }
            ?>

            <?php for($i = 0; $i < $jumlah ; $i++){ ?>
                <?php $pp = explode('_', $diffect[$i])?>
                <tr>
                    <td> {{ $index++ }} </td>
                    <td> {{$datas->order_no}}</td>
                    <td> {{$datas->material_number}}</td>
                    <td> {{$datas->material_description}} </td>
                    <td> {{$datas->issue_location}} </td>
                    <td> {{$datas->category}} </td>
                    <td> {{$datas->jenis}} </td>
                    <td> {{$datas->receive_location}} </td>
                    <td> {{$datas->quantity}} </td>
                    <td> {{$datas->remark}} </td>
                    <td> {{$datas->reason}} </td>
                    <td> {{$pp[0]}} </td>
                    <td> {{$pp[1]}} </td>
                    <td> {{$datas->no_invoice}} </td>
                    <td> {{$datas->printed_at}} </td>
                    <td> {{$datas->printed_by}} </td>
                    <td> {{$datas->received_at}} </td>
                    <td> {{$datas->received_by}} </td>
                    <td> {{$datas->canceled_at}} </td>
                    <td> {{$datas->canceled_by}} </td>
                </tr>
            <?php } ?>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>