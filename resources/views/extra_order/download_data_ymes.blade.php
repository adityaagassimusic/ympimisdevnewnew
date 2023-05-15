<!DOCTYPE html>
<html>

<head>
</head>

<body>
    @if (isset($data_ymes) && count($data_ymes) > 0)
        @php
            $id = 0;
        @endphp
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>move_type</th>
                    <th>inout_date</th>
                    <th>item_code</th>
                    <th>issue_loc_code</th>
                    <th>in_loc_code</th>
                    <th>inout_qty</th>
                    <th>sap_if_status</th>
                    <th>sap_if_date</th>
                    <th>Instid</th>
                    <th>Instdt</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data_ymes as $tr)
                    <tr>
                        <td>{{ ++$id }}</td>
                        <td>{{ $tr->move_type }}</td>
                        <td>{{ $tr->inout_date }}</td>
                        <td>{{ $tr->item_code }}</td>
                        <td>{{ $tr->issue_loc_code }}</td>
                        <td>{{ $tr->in_loc_code }}</td>
                        <td>{{ $tr->inout_qty }}</td>
                        <td>{{ $tr->sap_if_status }}</td>
                        <td>{{ $tr->sap_if_date }}</td>
                        <td>{{ $tr->instid }}</td>
                        <td>{{ $tr->instdt }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>

</html>
