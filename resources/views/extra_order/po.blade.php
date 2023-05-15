<!DOCTYPE html>
<html>

<head>
</head>

<body>
    @if (isset($lists) && count($lists) > 0)
        @php
            $id = 0;
        @endphp
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Material Buyer</th>
                    <th>Material YMPI</th>
                    <th>Description</th>
                    <th>ETD</th>
                    <th>Ship By</th>
                    <th>Qty</th>
                    <th>UoM</th>
                    <th>Price (USD)</th>
                    <th>Amount (USD)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lists as $tr)
                    <tr>
                        <td>{{ ++$id }}</td>
                        <td>{{ $tr->material_number_buyer }}</td>
                        <td>{{ $tr->material_number }}</td>
                        <td>{{ $tr->description }}</td>
                        <td>{{ $tr->request_date }}</td>
                        <td>{{ $tr->shipment_by }}</td>
                        <td>{{ $tr->quantity }}</td>
                        <td>{{ $tr->uom }}</td>
                        <td>{{ $tr->sales_price }}</td>
                        <td>{{ $tr->amount }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>
