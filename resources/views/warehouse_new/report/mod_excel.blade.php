<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($mod_detail) && count($mod_detail) > 0)
    <table>
        <thead>
            <tr>
                <th style="background-color: #ffeb3b;">No</th>
                <th style="background-color: #ffeb3b;">Tanggal</th>
                <th style="background-color: #ffeb3b;">Nomor Slip</th>
                <th style="background-color: #ffeb3b;">GMC</th>
                <th style="background-color: #ffeb3b;">Description</th>
                <th style="background-color: #ffeb3b;">Quantity</th>
                <th style="background-color: #ffeb3b;">Lokasi</th>
                <th style="background-color: #ffeb3b;">Rcvg Log</th>
                <th style="background-color: #ffeb3b;">Created_at</th>

            </tr>
        </thead>
        <tbody>
            <?php 
                $num = 1;
            ?>

            @foreach($mod_detail as $mod)

            <tr>
                <td>{{ $num++ }}</td>
                <td>{{ $mod->date_request }}</td>
                <td>{{ $mod->loc.$mod->kode_request }}</td>
                <td>{{ $mod->gmc }}</td>
                <td>{{ $mod->description }}</td>
                <td>{{ $mod->quantity_total }}</td>
                <td>{{ $mod->loc }}</td>
                <td>{{ $mod->sloc_name }}</td>
                <td>{{ $mod->created_at }}</td>

            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>