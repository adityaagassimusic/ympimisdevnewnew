<!DOCTYPE html>
<html>

<head>
 <style type="text/css">
    table.table-material>thead>tr>th {
        border: 1px solid black !important;
    }
    table.table-material>thead>tr>td {
        border: 1px solid black !important;
    }
</style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th colspan="10">Stocktaking Slip Period {{ $st->text }}</th> 
            </tr>
        </thead>
    </table>

    <table>
        <tbody>
            <tr>
                <th colspan="3">Stocktaking Date</th>
                <td>: {{ $st->date }}</td>
                <td></td>
                <td></td>
                <th colspan="3">Creation Date</th>
                <td>: {{ $report[0]->created_at }}</td>
            </tr>
            <tr>
                <th colspan="3">Group</th>
                <td>: {{ $report[0]->area }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <th colspan="3">Storage Location</th>
                <td>: {{ $report[0]->storage_location }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="3"></td>
            </tr>
            <tr>
                <th colspan="3">YMES Slip No.</th>
                <td>: {{ $report[0]->list_no }}</td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="3"></td>
            </tr>
        </tbody>

    </table>

    @if (isset($report) && count($report) > 0)
    @php
    $id = 0;
    @endphp
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Loc. Name</th>
                <th>ValCl</th>
                <th>GMC</th>
                <th>Description</th>
                <th>Uom</th>
                <th>Status</th>
                <th>Qty</th>
                <th>Revise</th>
                <th>Audit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report as $tr)
            <tr>
                <td>{{ $tr->slip_no }}</td>
                <td>{{ $tr->location }}</td>
                <td>{{ $tr->valcl }}</td>
                <td>{{ $tr->material_number }}</td>
                <td>{{ $tr->material_description }}</td>
                <td>{{ $tr->uom }}</td>
                <td>{{ $tr->category }}</td>
                <td>{{ $tr->quantity }}</td>
                @if($tr->quantity == $tr->final)
                <td></td>                        
                @else
                <td>{{ $tr->final }}</td>
                @endif
                <td>{{ $tr->auditor }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>

</html>

