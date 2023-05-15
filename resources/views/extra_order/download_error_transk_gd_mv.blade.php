<!DOCTYPE html>
<html>

<head>
</head>
<body>
    @if (isset($lists_error_gmv) && count($lists_error_gmv) > 0)
        @php
            $id = 0;
        @endphp
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>category</th>
                    <th>result date</th>
                    <th>slip number</th>
                    <th>serial number</th>
                    <th>material number</th>
                    <th>material description</th>
                    <th>issue location</th>
                    <th>receive location</th>
                    <th>quantity</th>
                    <th>synced</th>
                    <th>synced by</th>
                    <th>created by</th>
                    <th>created by_name</th>
                    <th>created at</th>
                    <th>updated at</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lists_error_gmv as $tr)
                    <tr>
                        <td>{{ ++$id }}</td>
                        <td>{{ $tr['category'] }}</td>
                        <td>{{ $tr['result_date'] }}</td>
                        <td>{{ $tr['slip_number'] }}</td>
                        <td>{{ $tr['serial_number'] }}</td>
                        <td>{{ $tr['material_number'] }}</td>
                        <td>{{ $tr['material_description'] }}</td>
                        <td>{{ $tr['issue_location'] }}</td>
                        <td>{{ $tr['receive_location'] }}</td>
                        <td>{{ $tr['quantity'] }}</td>
                        <td>{{ $tr['synced'] }}</td>
                        <td>{{ $tr['synced_by'] }}</td>
                        <td>{{ $tr['created_by'] }}</td>
                        <td>{{ $tr['created_by_name'] }}</td>
                        <td>{{ $tr['created_at'] }}</td>
                        <td>{{ $tr['updated_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
















</html>
