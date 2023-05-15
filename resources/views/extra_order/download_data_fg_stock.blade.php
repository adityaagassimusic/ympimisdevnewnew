<!DOCTYPE html>
<html>

<head>
</head>

<body>
    @if (isset($resume_new) && count($resume_new) > 0)
        @php
            $id = 0;
        @endphp
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>GMC</th>
                    <th>DESCRIPTION</th>
                    <th>LOCATION</th>
                    <th>SERIAL NUMBER</th>
                    <th>MIRAI</th>
                    <th>YMES</th>
                    <th>CONDITION</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resume_new as $tr)
                    <tr>
                        <td>{{ ++$id }}</td>
                        <td>{{ $tr->material_number }}</td>
                        <td>{{ $tr->material_description }}</td>
                        <td>{{ $tr->storage_location }}</td>
                        <td>{{ $tr->serial_number }}</td>
                        <td>{{ $tr->mirai }}</td>
                        <td>{{ $tr->ymes }}</td>
                        @if ($tr->mirai == $tr->ymes)
                            <td>OK</td>
                        @else
                            <td>UNMACTH</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
















</html>
