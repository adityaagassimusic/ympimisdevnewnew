<!DOCTYPE html>
<html>

<head>
</head>

<body>
    @if (isset($slip_data) && count($slip_data) > 0)
        @php
            $id = 0;
        @endphp
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>List No.</th>
                    <th>Sloc</th>
                    <th>GMC</th>
                    <th>Description</th>
                    <th>Slip No.</th>
                    <th>Jenis Slip</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($slip_data as $tr)
                    <tr>
                        <td>{{ ++$id }}</td>
                        <td>{{ $tr->list_no }}</td>
                        <td>{{ $tr->location }}</td>
                        <td>{{ $tr->material_number }}</td>
                        <td>{{ $tr->material_description }}</td>
                        <td>{{ $tr->slip_no }}</td>
                        <td>{{ $tr->category }}</td>
                        <td>{{ $tr->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>
