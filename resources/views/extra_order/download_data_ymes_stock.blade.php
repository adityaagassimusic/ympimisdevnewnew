<!DOCTYPE html>
<html>

<head>
</head>

<body>
    @if (isset($ymes_stock) && count($ymes_stock) > 0)
        <table>
            <thead>
                <tr>
                    <th>GMC</th>
                    <th>LOCATION</th>
                    <th>STOCK QTY</th>
                    <th>INSPECT QTY</th>
                    <th>KEEP QTY</th>
                    <th>UOM</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ymes_stock as $tr)
                    <tr>
                        <td>{{ $tr->item_code }}</td>
                        <td>{{ $tr->location_code }}</td>
                        <td>{{ $tr->stockqty }}</td>
                        <td>{{ $tr->inspect_qty }}</td>
                        <td>{{ $tr->keep_qty }}</td>
                        <td>{{ $tr->unit_code }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
















</html>
