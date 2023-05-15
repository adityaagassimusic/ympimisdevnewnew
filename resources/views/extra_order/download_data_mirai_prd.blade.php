<!DOCTYPE html>
<html>

<head>
</head>

<body>
    @if (isset($data_pr) && count($data_pr) > 0)
        @php
            $id = 0;
        @endphp
        <table>
            <thead>
                <tr>
                     <th>#</th>
                    <th>category</th>
                    <th>Posting</th>
                    <th>Entry</th>
                    <th>Slip No.</th>
                    <th>Serial No.</th>
                    <th>material</th>
                    <th>Description</th>
                    <th>issue</th>
                    <th>Receive</th>
                    <th>Quantity</th>
                    <th>created_by</th>
                    <th>created_by_name</th>
                    <th>synced</th>
                    <th>synced by</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($data_pr as $tr)
                    <tr>
                        <td>{{ ++$id }}</td>
                        <td>{{ $tr->category }}</td>
                        <td>{{ $tr->result_date }}</td>
                        <td>{{ $tr->created_at }}</td>
                        <td>{{ $tr->slip_number }}</td>
                        <td>{{ $tr->serial_number }}</td>
                        <td>{{ $tr->material_number }}</td>
                        <td>{{ $tr->material_description }}</td>
                        <td>{{ $tr->issue_location }}</td>
                        <td>-</td>
                        <td>{{ $tr->quantity }}</td>
                        <td>{{ $tr->created_by }}</td>
                        <td>{{ $tr->created_by_name }}</td>
                        <td>{{ $tr->synced }}</td>
                        <td>{{ $tr->synced_by }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
