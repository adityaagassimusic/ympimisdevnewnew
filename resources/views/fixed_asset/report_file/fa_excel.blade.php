<!DOCTYPE html>
<html>
<head>
    <style type="text/css">
        table{
            border:1px solid black;
        }
        table > thead > tr > th{
            border:1px solid black;
        }
        table > tbody > tr > td{
            border:1px solid rgb(211,211,211);
            vertical-align: middle;
        }
    </style>
</head>
<body>
    @if(isset($assets) && count($assets) > 0)
    <table border="1">
        <thead>
            <tr>     
                <th>Period</th>
                <th>Category</th>
                <th>Location</th>
                <th>Asset Number</th>
                <th>Asset Name</th>
                <th>Asset Image</th>
                <th>Asset Check Image</th>
                <th>Asset Section</th>
                <th>Note</th>
                <th>Availability</th>
                <th>Asset Condition</th>
                <th>Label Condition</th>
                <th>Usable Condition</th>
                <th>Map Condition</th>
                <th>Asset Image Condition</th>
                <th>Check1 by</th>
                <th>Check1 Date</th>
                <th>Check2 by</th>
                <th>Check2 Date</th>
            </tr>
        </thead>
        <tbody>
            <?php $index = 1; ?>
            @foreach($assets as $fa)
            <tr>
                <td style="vertical-align: middle;">{{ $fa->period }}</td>
                <td>{{ $fa->category }}</td>
                <td>{{ $fa->location }}</td>
                <td>{{ $fa->sap_number }}</td>
                <td>{{ $fa->asset_name }}</td>

                <td>
                    <?php if ($fa->asset_images) { if (file_exists(public_path('files/fixed_asset/asset_picture/'.$fa->asset_images))) { ?>
                        <img src="{{ public_path('files/fixed_asset/asset_picture/'.$fa->asset_images) }}" width="100px">
                    <?php } } ?>
                </td>
                <td>
                    <?php if ($fa->result_images) { if (file_exists(public_path('files/fixed_asset/asset_check/'.$fa->result_images))) { ?>
                        <img src="{{ public_path('files/fixed_asset/asset_check/'.$fa->result_images) }}" width="100px">
                    <?php } } ?>
                </td>

                <td>{{ $fa->asset_section }}</td>
                <?php if ($fa->result_images) { ?>
                    <td style="background-color: #65fc88">{{ $fa->note }}</td>
                    <td style="background-color: #65fc88">{{ $fa->availability }}</td>
                    <td style="background-color: #65fc88">{{ $fa->asset_condition }}</td>
                    <td style="background-color: #65fc88">{{ $fa->label_condition }}</td>
                    <td style="background-color: #65fc88">{{ $fa->usable_condition }}</td>
                    <td style="background-color: #65fc88">{{ $fa->map_condition }}</td>
                    <td style="background-color: #65fc88">{{ $fa->asset_image_condition }}</td>
                <?php } else { ?>
                    <td style="background-color: #65fc88"></td>
                    <td style="background-color: #65fc88"></td>
                    <td style="background-color: #65fc88"></td>
                    <td style="background-color: #65fc88"></td>
                    <td style="background-color: #65fc88"></td>
                    <td style="background-color: #65fc88"></td>
                    <td style="background-color: #65fc88"></td>
                <?php } ?>

                <td style="background-color: #65fc88"></td>
                <td style="background-color: #65fc88"></td>
                <td style="background-color: #65fc88"></td>
                <td style="background-color: #65fc88"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>