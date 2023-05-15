<!DOCTYPE html>
<html>

<head>
    <style type="text/css">
        td {
            padding-right: 5px;
            padding-left: 5px;
            padding-top: 0px;
            padding-bottom: 0px;
        }

        th {
            padding-right: 5px;
            padding-left: 5px;
        }

        .status {
            font-size: 20pt;
            font-weight: bold;
            color: red;
            margin-bottom: 0px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>
    <div>
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"><br>
            <p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>
            <span style="font-weight: bold; color: black; font-size: 24px;"></span><br>
            <p class="status"></i>negative findings on checking the truck container condition in security!</p>
        </center>
        <br>
        <div style="width: 60%; margin: auto;">
            <table style="border:1px solid black; border-collapse: collapse; width: 100%; font-size: 10pt;">
                <thead>
                    <tr>
                        <th style="border:1px solid black; background-color: #cddc39;">Category</th>
                        <th style="text-align: left;">{{ strtoupper($data['checklist']->category) }}</th>
                    </tr>
                    <tr>
                        <th style="border:1px solid black; background-color: #cddc39;">Status</th>
                        <th style="text-align: left;">{{ strtoupper($data['status']) }}</th>
                    </tr>
                    @if ($data['status'] == 'CHECK-IN')
                        <tr>
                            <th style="border:1px solid black; background-color: #cddc39;">Check By</th>
                            <th style="text-align: left;">{{ strtoupper($data['checklist']->check_in_by) }} -
                                {{ strtoupper($data['check_by']) }}
                            </th>
                        </tr>
                        <tr>
                            <th style="border:1px solid black; background-color: #cddc39;">Check At</th>
                            <th style="text-align: left;">{{ strtoupper($data['checklist']->check_in_at) }}</th>
                        </tr>
                    @elseif($data['status'] == 'CHECK-IN')
                        <tr>
                            <th style="border:1px solid black; background-color: #cddc39;">Check By</th>
                            <th style="text-align: left;">{{ strtoupper($data['checklist']->check_out_by) }} -
                                {{ strtoupper($data['check_by']) }}
                            </th>
                        </tr>
                        <tr>
                            <th style="border:1px solid black; background-color: #cddc39;">Check At</th>
                            <th style="text-align: left;">{{ strtoupper($data['checklist']->check_out_at) }}</th>
                        </tr>
                    @endif

                    <tr>
                        <th style="border:1px solid black; background-color: #cddc39;">Driver Name</th>
                        <th style="text-align: left;">{{ strtoupper($data['checklist']->driver_name) }}</th>
                    </tr>
                    <tr>
                        <th style="border:1px solid black; background-color: #cddc39;">Vehicle No.</th>
                        <th style="text-align: left;">{{ strtoupper($data['checklist']->vehicle_registration_number) }}
                        </th>
                    </tr>
                    <tr>
                        <th style="border:1px solid black; background-color: #cddc39;">Container No.</th>
                        <th style="text-align: left;">{{ strtoupper($data['checklist']->container_number) }}</th>
                    </tr>
                    <tr>
                        <th style="border:1px solid black; background-color: #cddc39;">Note</th>
                        <th style="text-align: left;">{{ strtoupper($data['checklist']->note) }}</th>
                    </tr>
                </thead>
            </table>
        </div>
        <br>
        <br>
        <br>
        <br>
        <center>
            <span style="font-weight: bold;">&#8650; <i>Click Here For</i> &#8650;</span><br>
            <a style="background-color: #25b2fd; text-decoration: none; color: black;"
                href="{{ url('index/security_check_report/' . $data['checklist_id']) }}">
                &nbsp;Checklist Report&nbsp;
            </a>
        </center>
        <br>
        <br>
    </div>
</body>

</html>
