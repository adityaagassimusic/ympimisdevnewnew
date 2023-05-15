<!DOCTYPE html>
<html>

<head>
    <style type="text/css">
        table {
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        td {
            padding-top: 0px;
            padding-bottom: 0px;
            padding-left: 3px;
            padding-right: 3px;
        }
    </style>
</head>

<body>
    <div>
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
                alt=""><br>
            <p style="font-size: 20px;">Stock Alert < Stock Policy<br>{{ $data['date_text'] }}</p>
            <p style="font-size: 20px;">{{ count($data['material']) }} Item(s) Under Stock Policy<br></p>
            <p style="font-size: 20px;">Buyer : {{ $data['user']->name }}</p>

            This is an automatic notification. Please do not reply to this address.
            <table style="border:1px solid black; border-collapse: collapse; font-size: 9pt;" width="99%">
                <thead style="background-color: rgb(126,86,134);">
                    <tr>
                        <th rowspan="2" style="width: 23%; border:1px solid black;">Material</th>
                        <th rowspan="2" style="width: 23%; border:1px solid black;">Vendor</th>
                        <th colspan="3" style="width: 13%; border:1px solid black;">Stock</th>
                        <th rowspan="2" style="width: 8%; border:1px solid black;">Policy</th>
                        <th rowspan="2" style="width: 5%; border:1px solid black;">Stock Condition</th>
                        <th rowspan="2" style="width: 10%; border:1px solid black;">Stock Out Date Plan</th>
                        <th rowspan="2" style="width: 10%; border:1px solid black;">Plan Next Delivery</th>
                    </tr>
                    <tr>
                        <th style="width: 7%; border:1px solid black; background-color: rgb(126,86,134);">WIP</th>
                        <th style="width: 7%; border:1px solid black; background-color: rgb(126,86,134);">WH</th>
                        <th style="width: 7%; border:1px solid black; background-color: rgb(126,86,134);">All</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['material'] as $col)
                        <tr>
                            <td style="border:1px solid black; text-align: left;">
                                <b>{{ $col['material_number'] }}</b><br>{{ $col['material_description'] }}
                            </td>
                            <td style="border:1px solid black; text-align: left;">
                                <b>{{ $col['vendor_code'] }}</b><br>{{ $col['vendor_name'] }}
                            </td>
                            <td style="border:1px solid black; text-align: right;">
                                {{ number_format(ceil($col['stock_wip']), 0, '.', ',') }} {{ $col['bun'] }}
                            </td>
                            <td style="border:1px solid black; text-align: right;">
                                {{ number_format(ceil($col['stock_wh']), 0, '.', ',') }} {{ $col['bun'] }}
                            </td>
                            <td style="border:1px solid black; text-align: right;">
                                <b>{{ number_format(ceil($col['stock']), 0, '.', ',') }}</b> {{ $col['bun'] }}
                            </td>
                            <td style="border:1px solid black; text-align: center;">
                                {{ $col['day'] }} Days
                                <br>
                                {{ number_format(ceil($col['policy']), 0, '.', ',') }} {{ $col['bun'] }}
                            </td>
                            <td style="border:1px solid black; text-align: right;">
                                {{ $col['percentage'] }}%
                            </td>
                            <td style="border:1px solid black; text-align: center;">
                                {{ is_null($col['stock_out_date']) ? '-' : $col['stock_out_date'] }}
                            </td>
                            <td style="border:1px solid black; text-align: center;">
                                {{ is_null($col['plan_delivery']) ? '-' : $col['plan_delivery'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <br>

        </center>
    </div>
</body>

</html>
