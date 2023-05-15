<!DOCTYPE html>
<html>
<head>
    <style type="text/css">

    </style>
</head>
<body>
    @if(isset($detail) && count($detail) > 0)
    <table>
        <thead>
            <tr style="vertical-align: middle; ">
                <th colspan="11" style="text-align: center;">
                    List Data Overtime Food & Transport MIRAI
                </th>
            </tr>

            <tr></tr>
        </thead>
        <tbody>

            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>ID</th>
                <th>Nama</th>
                <th>Section</th>
                <th>Shift</th>
                <th>Makan</th>
                <th>Attend Date</th>
                <th>Status</th>

             <!--    <th>Extra Food</th>
                <th>Transport</th> -->

            </tr>

            <?php 
                $num = 1;
                $amount=0; 
            ?>

            @foreach($detail as $overtime)

            <tr>
                <td style="vertical-align: middle;text-align: left;width: 5">{{ $num++ }}</td>
                <td style="vertical-align: middle;text-align: left;width: 10"><?php echo date('d-m-y', strtotime($overtime->tanggal)) ?></td>
                <td style="vertical-align: middle;text-align: left;width: 10">{{ $overtime->employee_id }}</td>
                <td style="vertical-align: middle;text-align: left;width: 25">{{ $overtime->name }}</td>
                <td style="vertical-align: middle;text-align: left;width: 15">{{ $overtime->section }}</td>
                <td style="vertical-align: middle;text-align: left;width: 10">{{ $overtime->shift }}</td>


                @if($overtime->food == "1")
                    <td style="vertical-align: middle;text-align: left;width: 10">Ya</td>
                @else
                    <td style="vertical-align: middle;text-align: left;width: 10">Tidak</td>
                @endif
                <td style="vertical-align: middle;text-align: left;width: 15">{{ $overtime->attend_date }}</td>
                @if($overtime->attend_date == '')
                    <td style="vertical-align: middle;text-align: left;width: 10">Belum Hadir</td>
                @else
                    <td style="vertical-align: middle;text-align: left;width: 10">Hadir</td>
                @endif
                  <!--  @if($overtime->ext_food == "1")
                    <td style="vertical-align: middle;text-align: left;width: 15">Ya</td>
                @else
                    <td style="vertical-align: middle;text-align: left;width: 15">Tidak</td>
                @endif
                @if($overtime->transport == null)
                    <td style="vertical-align: middle;text-align: left;width: 10">Tidak</td>
                @else
                <td style="vertical-align: middle;text-align: left;width: 10">{{ $overtime->transport }}</td>
                @endif -->

                
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>