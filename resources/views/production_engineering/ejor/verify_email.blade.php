<!DOCTYPE html>
<html>

<head>
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <title>APPROVAL ENGINEERING JOB REQUEST (EJOR)</title>

    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            text-align: center;
        }

        tbody>tr>td {
            text-align: center;
            border: 1px solid black;
        }

        tfoot>tr>th {
            text-align: center;
        }

        td:hover {
            overflow: visible;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 0;
            padding-bottom: 0;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        #loading,
        #error {
            display: none;
        }
    </style>
</head>

<body>
    <center>
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
            alt=""><br>
        <?php if ($data['position'] == 'User') { ?>
        <p style="font-size: 22px; font-weight: bold;">
            ENGINEERING JOB REQUEST (EJOR) REPORT - {{ $data['datas']->form_id }}
        </p>
        <p style="font-size: 18px; font-weight: bold;">
            Your ENGINEERING JOB REQUEST (EJOR) Has Been Finished
        </p>
        <?php } else { ?>
        <p style="font-size: 22px; font-weight: bold;">
            VERIFICATION ENGINEERING JOB REQUEST (EJOR) EVIDENCE(S) - {{ $data['datas']->form_id }}
        </p>
        <?php } ?>

        <?php if ($data['position'] == 'Hold'){ ?>
        <p style="font-size: 20px; font-weight: bold; color: #4f98c3">Your EJOR Evidence Has Been HOLDED</p>
        <?php } elseif ($data['position'] == 'Reject') { ?>
        <p style="font-size: 20px; font-weight: bold; color: #ed6f6f">Your EJOR Evidence Has Been REJECTED</p>
        <?php } ?>
        <p>This is an automatic notification. Please do not reply to this address.<br>返信不要の自動通知です。</p>

        <?php 
		if ($data['position'] == 'Reject') {
			$hld = [];
			// foreach ($data['appr'] as $apr) {
				// if ($apr->status == 'OnHold') {
			$hld['by'] = $data['evidence']->approve_by;
			$hld['comment'] = $data['evidence']->remark;
				// }
			// }
			?>

        <table style="width: 80%; border: 1px solid black">
            <tr>
                <th style="width: 30%; background-color: #ed6f6f; padding: 2px">Rejected by</th>
                <th style="background-color: #ed6f6f; padding: 2px">Comment</th>
            </tr>
            <tr>
                <td style="padding: 5px">{{ $hld['by'] }}</td>
                <td style="text-align: left; padding: 5px"><?php print_r(nl2br($hld['comment'])); ?> </td>
            </tr>
        </table>
        <?php } ?>
        <br>

        <table style="width: 80%; border: 1px solid black">
            <tr>
                <th style="width: 30%; background-color: #9e83a7; text-align: left; padding: 2px;">Title</th>
                <td style="text-align: left; padding: 2px">{{ $data['datas']->title }}</td>
            </tr>

            <tr>
                <th style="background-color: #9e83a7; text-align: left; padding: 2px">Requested By</th>
                <td style="text-align: left; padding: 2px">{{ $data['datas']->name }}</td>
            </tr>

            <tr>
                <th style="background-color: #9e83a7; text-align: left; padding: 2px">Section</th>
                <td style="text-align: left; padding: 2px">{{ $data['datas']->section }}</td>
            </tr>

            <tr>
                <th style="background-color: #9e83a7; text-align: left; padding: 2px">Request Date</th>
                <td style="text-align: left; padding: 2px">{{ $data['datas']->req_date }}</td>
            </tr>

            <tr>
                <th style="background-color: #9e83a7; text-align: left; padding: 2px">Priority</th>
                <?php if ($data['datas']->priority == 'Urgent') {
                    echo "<td style='text-align: left; padding: 2px; color : red'>" . $data['datas']->priority . '</td>';
                } else {
                    echo "<td style='text-align: left; padding: 2px'>" . $data['datas']->priority . '</td>';
                }
                ?>
            </tr>

            <?php if ($data['datas']->priority == 'Urgent') { ?>
            <tr>
                <th style="background-color: #9e83a7; text-align: left; padding: 2px">Priority Reason</th>
                <td style="text-align: left; padding: 2px">{{ $data['datas']->priority_reason }}</td>
            </tr>
            <?php } ?>

            <tr>
                <th style="background-color: #9e83a7; text-align: left; padding: 2px">Reason</th>
                <td style="text-align: left; padding: 2px">{{ $data['datas']->reason }}</td>
            </tr>

            <tr>
                <th style="background-color: #9e83a7; text-align: left; padding: 2px">Category</th>
                <td style="text-align: left; padding: 2px">{{ $data['datas']->job_type }} -
                    {{ $data['datas']->job_category }}</td>
            </tr>

            <tr>
                <th style="background-color: #9e83a7; text-align: left; padding: 2px">Target Date</th>
                <td style="text-align: left; padding: 2px">{{ $data['datas']->target_date }}</td>
            </tr>
        </table>
        <br>
        <table style="width: 80%; border: 1px solid black">
            <tr>
                <th style="width: 30%; background-color: #9e83a7; text-align: left; padding: 2px;">Evidence by</th>
                <td style="text-align: left; padding: 2px">{{ $data['evidence']->uploaded_by }} (
                    {{ $data['evidence']->uploaded_at }} )</td>
            </tr>
            <tr>
                <th style="width: 30%; background-color: #9e83a7; text-align: left; padding: 2px;">Evidence Note</th>
                <td style="text-align: left; padding: 2px"><?php print_r($data['evidence']->note); ?></td>
            </tr>
        </table>
        <br>
        <table>
            <tr>
                <td colspan="2" style="border: 0px !important">
                    <?php if ($data["position"] != 'Hold' && $data["position"] != 'Reject' && $data["position"] != 'User'): ?>
                    <a style="background-color: #4ecc4b; width: 50px;text-decoration: none;color: white;font-size:20px;"
                        href='{{ url('verify/ejor/' . $data['datas']->form_id . '/Approved') }}'>&nbsp;&nbsp;&nbsp;
                        Approve
                        &nbsp;&nbsp;&nbsp;</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                    <a style="background-color: red; width: 50px;text-decoration: none;color: white;font-size: 20px;"
                        href='{{ url('verify/ejor/' . $data['datas']->form_id . '/Evidence_Rejected') }}'>&nbsp; Reject
                        &nbsp;</a>

                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td style="border: 0px !important">
                    <br>
                    <a style="background-color: #cc7efc; width: 50px;text-decoration: none;color: white;font-size: 20px;"
                        href='{{ url('index/ejor/monitoring') }}'>&nbsp; EJOR Monitoring &nbsp;</a>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table style="width: 80%">
            <tr>
                <td style="border: 1px solid black; width: 16%; background-color: #9e83a7;">Created by</td>
                <td style="border: 1px solid black; width: 16%; background-color: #9e83a7;">Known by</td>
                <td style="border: 1px solid black; width: 16%; background-color: #9e83a7;">Known by</td>
                <td style="border: 1px solid black; width: 16%; background-color: #9e83a7;">Approved by</td>
                <td style="border: 1px solid black; width: 16%; background-color: #9e83a7;">Known by</td>
                <td style="border: 1px solid black; width: 16%; background-color: #9e83a7;">Received by</td>
            </tr>
            <tr>
                <td style="border: 1px solid black; background-color: #9e83a7;">Leader / Staff</td>
                <td style="border: 1px solid black; background-color: #9e83a7;">Chief / Foreman</td>
                <td style="border: 1px solid black; background-color: #9e83a7;">Manager</td>
                <td style="border: 1px solid black; background-color: #9e83a7;">Manager PE</td>
                <td style="border: 1px solid black; background-color: #9e83a7;">Chief PE</td>
                <td style="border: 1px solid black; background-color: #9e83a7;">Staff PE</td>
            </tr>
            <tr>
                <?php if ($data['appr'][0]->status){ ?>
                <td style="border: 1px solid black;">{{ strtoupper($data['appr'][0]->status) }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black">&nbsp;</td>
                <?php } ?>

                <?php if ($data['appr'][1]->status){ ?>
                <td style="border: 1px solid black;">{{ strtoupper($data['appr'][1]->status) }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black">&nbsp;</td>
                <?php } ?>

                <?php if ($data['appr'][2]->status){ ?>
                <td style="border: 1px solid black;">{{ strtoupper($data['appr'][2]->status) }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black">&nbsp;</td>
                <?php } ?>

                <?php if ($data['appr'][3]->status){ ?>
                <td style="border: 1px solid black;">{{ strtoupper($data['appr'][3]->status) }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black">&nbsp;</td>
                <?php } ?>

                <?php if ($data['appr'][4]->status){ ?>
                <td style="border: 1px solid black;">{{ strtoupper($data['appr'][4]->status) }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black">&nbsp;</td>
                <?php } ?>

                <?php if ($data['datas']->pic_date){ ?>
                <td style="border: 1px solid black;">RECEIVED</td>
                <?php } else { ?>
                <td style="border: 1px solid black">&nbsp;</td>
                <?php } ?>
            </tr>
            <tr>
                <td style="border: 1px solid black; background-color: #9e83a7;">{{ $data['appr'][0]->approver_name }}
                </td>
                <td style="border: 1px solid black; background-color: #9e83a7;">{{ $data['appr'][1]->approver_name }}
                </td>
                <td style="border: 1px solid black; background-color: #9e83a7;">{{ $data['appr'][2]->approver_name }}
                </td>
                <td style="border: 1px solid black; background-color: #9e83a7;">{{ $data['appr'][3]->approver_name }}
                </td>
                <td style="border: 1px solid black; background-color: #9e83a7;">{{ $data['appr'][4]->approver_name }}
                </td>

                @if ($data['datas']->pic)
                    <td style="border: 1px solid black; background-color: #9e83a7;">
                        {{ explode('/', $data['datas']->pic)[1] }}</td>
                @else
                    <td style="border: 1px solid black; background-color: #9e83a7;"></td>
                @endif

            </tr>
            <tr>
                <?php if ($data['appr'][0]->status){ ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. {{ $data['appr'][0]->appr_at }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. </td>
                <?php } ?>

                <?php if ($data['appr'][1]->status){ ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. {{ $data['appr'][1]->appr_at }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. </td>
                <?php } ?>

                <?php if ($data['appr'][2]->status){ ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. {{ $data['appr'][2]->appr_at }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. </td>
                <?php } ?>

                <?php if ($data['appr'][3]->status){ ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. {{ $data['appr'][3]->appr_at }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. </td>
                <?php } ?>

                <?php if ($data['appr'][4]->status){ ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. {{ $data['appr'][4]->appr_at }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. </td>
                <?php } ?>

                <?php if ($data['datas']->pic_date){ ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. {{ $data['datas']->pic_date }}</td>
                <?php } else { ?>
                <td style="border: 1px solid black; text-align: left;">Tgl. </td>
                <?php } ?>
            </tr>
        </table>

        <?php
        if (isset($data['versi'])) {
            if ($data['versi'] == 'web') {
                print_r('<br><br><a style="background-color: #ddd; border: 1px solid orange; width: 50px;text-decoration: none;color: white;font-size: 20px; color: black;" href="' . url('files/ejor/form/' . $data['datas']->form_id . '.pdf') . '" target="_blank">&nbsp;&#9993; ' . $data['datas']->form_id . '.pdf&nbsp;</a>&nbsp;');
				$att2 = explode(',', $data['evidence']->attachment);

				foreach ($att2 as $ev) {
					print_r('<a style="background-color: #ddd; border: 1px solid orange; width: 50px;text-decoration: none;color: white;font-size: 20px; color: black;" href="' . url('files/ejor/evidence/' . $ev) . '" target="_blank">&nbsp;&#9993; ' . $ev . '&nbsp;</a>&nbsp;');
				}
            }
        }
        ?>
    </center>
</body>

</html>
