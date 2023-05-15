@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <style type="text/css">
        thead>tr>th {
            vertical-align: middle !important;
            text-align: center !important;
        }

        tbody>tr>td {}

        tfoot>tr>th {}

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        #loading {
            display: none;
        }

        #alert {
            background-color: #66D9F5;
            color: #060606;
            display: none;
        }

        #btn-download:hover {
            font-weight: bold;
            color: white;
            background-color: #d73925;
            border-color: grey;
        }

        #btn-edit:hover {
            font-weight: bold;
            color: white;
            background-color: #008d4c;
            border-color: grey;
        }

        #btn-att:hover {
            font-weight: bold;
            color: white;
            background-color: #00acd6;
            border-color: grey;
        }

        .label-approval {
            color: black;
            font-size: 12px;
            border-radius: 4px;
            padding: 1px 5px 2px 5px;
            border: 1px solid black;
            min-width: 120px;
        }

        .unapproved {
            cursor: pointer;
            color: #faff00;
        }

        .holdandcomment {
            cursor: pointer;
        }
    </style>
@endsection
@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>

        <input id="attachment" value="{{ $extra_order->attachment }}" hidden>
        <input id="role_code" value="{{ Auth::user()->role_code }}" hidden>

        <div class="row">
            <div class="col-xs-12">
                <div class="alert alert-dismissible" id="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-info"></i> Information !</h4>
                    Data has been updated, refresh the page to see the update.&nbsp;&nbsp;&nbsp;
                    <button class="btn btn-success btn-xs" onclick="refreshAll()"><i
                            class="fa  fa-refresh"></i>&nbsp;&nbsp;Refresh</button>&nbsp;&nbsp;&nbsp;
                    <button data-dismiss="alert"
                        class="btn btn-xs">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Later&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
                </div>
                <div class="box box-solid" style="border: 1px solid #777777;">
                    <div class="box-header" style="border-bottom: 1px solid #777777;">
                        <h3 class="box-title">{{ $title }} (<span class="text-purple">{{ $title_jp }}</span>)
                        </h3>

                        @if (!is_null($extra_order->attachment))
                            <button onclick="downloadAtt()" class="btn btn-sm btn-info pull-right" id="btn-att"
                                style="margin-left: 5px; width: 12%;"><i class="fa fa-download"></i>&nbsp;&nbsp;&nbsp;Open
                                Attachment</button>
                        @endif

                        @if (count($approval) > 0 && Auth::user()->role_code != 'BUYER')
                            <button onclick="downloadEoc('{{ $extra_order->eo_number }}')"
                                class="btn btn-sm btn-danger pull-right" id="btn-download"
                                style="margin-left: 5px; width: 12%;"><i
                                    class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;&nbsp;Download EOC</button>
                        @endif
                    </div>
                    <div class="box-body" style="display: block;">
                        <div class="col-xs-9" style="padding-left: 0;">
                            <label style="font-size: 1.2vw;">Request List :</label>
                            <table class="table table-bordered cell-border" width="100%" id="table-main">
                                <thead style="background-color: rgba(126,86,134,.5);">
                                    <tr>
                                        <th style="text-align: center;">GMC Buyer</th>
                                        <th style="text-align: center;">GMC YMPI</th>
                                        <th style="text-align: center;">Description</th>
                                        <th style="text-align: center;">ETD</th>
                                        <th style="text-align: center;">Ship By</th>
                                        <th style="text-align: center;">Qty</th>
                                        <th style="text-align: center;">UoM</th>
                                        <th style="text-align: center;">Price (USD)</th>
                                        <th style="text-align: center;">Amount (USD)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                        $bom_complete = 0;
                                        $price_complete = 0;
                                    @endphp
                                    @foreach ($detail as $row)
                                        <tr>
                                            @php
                                                $css = '';
                                                if ($row->material_number != 'NEW') {
                                                    $bom_complete++;
                                                } else {
                                                    $css = 'background-color: #ffccff;';
                                                }
                                            @endphp
                                            <td style="text-align: center;">{{ $row->material_number_buyer }}</td>
                                            <td style="text-align: center; {{ $css }}">
                                                {{ $row->material_number }}</td>
                                            <td style="text-align: left;">{{ $row->description }}</td>
                                            <td style="text-align: center;">{{ $row->request_date }}</td>
                                            <td style="text-align: center;">{{ $row->shipment_by }}</td>
                                            <td style="text-align: right;">{{ $row->quantity }}</td>
                                            <td style="text-align: center;">{{ $row->uom }}</td>
                                            @php
                                                $css = '';
                                                if ($row->sales_price != 0) {
                                                    $price_complete++;
                                                } else {
                                                    $css = 'background-color: #ffccff;';
                                                }
                                            @endphp
                                            <td style="text-align: right; {{ $css }}">{{ $row->sales_price }}
                                            </td>

                                            <td style="text-align: right;">{{ $row->sales_price * $row->quantity }}</td>
                                            @php
                                                $amount = $row->sales_price * $row->quantity;
                                                $total = $total + $amount;
                                            @endphp

                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="background-color: rgba(252, 248, 227, .5);">
                                    <tr>
                                        <th style="text-align: center;" colspan="8">Total Amount</th>
                                        <th style="text-align: right;">{{ $total }}</th>
                                    </tr>
                                </tfoot>
                            </table>


                            @if (Auth::user()->role_code != 'BUYER')
                                <label style="font-size: 1.2vw;">Approver List :</label>
                                @if (
                                    (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'PC')) &&
                                        count($approval) == 0)
                                    @if (count($detail) == $bom_complete && count($detail) == $price_complete)
                                        @if (count($approval) == 0)
                                            <a data-toggle="modal" data-target="#modalSendApproval"
                                                class="btn btn-primary pull-right"
                                                style="margin-bottom: 5px; margin-left: 5px; width: 15%;">Send Approval</a>
                                        @endif
                                    @endif
                                @endif


                                <table class="table table-bordered table-responsive" width="100%"
                                    style="margin-bottom: 0.5%;">
                                    <thead style="background-color: rgba(126,86,134,.5);">
                                        <th style="text-align: center; width: 20%;">Buyer</th>
                                        <th style="text-align: center; width: 20%;">Foreman & Chief</th>
                                        <th style="text-align: center; width: 20%;">Manager</th>
                                        <th style="text-align: center; width: 20%;">Deputy General Manager</th>
                                        <th style="text-align: center; width: 20%;">General Manager</th>
                                    </thead>
                                    <tbody>
                                        @if (count($approval) > 0)
                                            <tr>
                                                <td style="text-align: center; vertical-align: middle;">
                                                    @foreach ($approval as $row)
                                                        @if (str_contains($row->remark, 'Buyer'))
                                                            @if ($row->status == 'Approved')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval"
                                                                        style="background-color: #aee571;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">
                                                                            {{ $row->approved_at }}</p>
                                                                    </label>
                                                                </div>
                                                            @elseif ($row->status == 'Rejected')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval unapproved"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #f25450;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">Rejected
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @elseif ($row->status == 'Hold & Comment')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval holdandcomment"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #65b7fc;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">
                                                                            Hold & Comment
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @else
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval unapproved"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #f25450;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">Waiting
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>

                                                <td style="text-align: center; vertical-align: middle;">
                                                    @foreach ($approval as $row)
                                                        @if ($row->remark == 'Foreman' || $row->remark == 'Chief')
                                                            @if ($row->status == 'Approved')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval"
                                                                        style="background-color: #aee571;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">
                                                                            {{ $row->approved_at }}</p>
                                                                    </label>
                                                                </div>
                                                            @elseif ($row->status == 'Rejected')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval unapproved"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #f25450;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">Rejected
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @elseif ($row->status == 'Hold & Comment')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval holdandcomment"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #65b7fc;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">
                                                                            Hold & Comment
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @else
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval unapproved"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #f25450;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">Waiting
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>

                                                <td style="text-align: center; vertical-align: middle;">
                                                    @foreach ($approval as $row)
                                                        @if ($row->remark == 'Manager')
                                                            @if ($row->status == 'Approved')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval"
                                                                        style="background-color: #aee571;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">
                                                                            {{ $row->approved_at }}</p>
                                                                    </label>
                                                                </div>
                                                            @elseif ($row->status == 'Rejected')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval unapproved"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #f25450;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">Rejected
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @elseif ($row->status == 'Hold & Comment')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval holdandcomment"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #65b7fc;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">
                                                                            Hold & Comment
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @else
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval unapproved"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #f25450;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">Waiting
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>

                                                <td style="text-align: center; vertical-align: middle;">
                                                    @foreach ($approval as $row)
                                                        @if ($row->remark == 'Deputy General Manager')
                                                            @if ($row->status == 'Approved')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval"
                                                                        style="background-color: #aee571;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">
                                                                            {{ $row->approved_at }}</p>
                                                                    </label>
                                                                </div>
                                                            @elseif ($row->status == 'Rejected')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval unapproved"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #f25450;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">Rejected
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @elseif ($row->status == 'Hold & Comment')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval holdandcomment"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #65b7fc;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">
                                                                            Hold & Comment
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @else
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval unapproved"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #f25450;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">Waiting
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>

                                                <td style="text-align: center; vertical-align: middle;">
                                                    @foreach ($approval as $row)
                                                        @if ($row->remark == 'General Manager')
                                                            @if ($row->status == 'Approved')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval"
                                                                        style="background-color: #aee571;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">
                                                                            {{ $row->approved_at }}</p>
                                                                    </label>
                                                                </div>
                                                            @elseif ($row->status == 'Rejected')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval unapproved"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #f25450;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">Rejected
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @elseif ($row->status == 'Hold & Comment')
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval holdandcomment"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #65b7fc;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">
                                                                            Hold & Comment
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @else
                                                                <div class="row" style="margin-bottom : 1%;">
                                                                    <label class="label-approval unapproved"
                                                                        onclick="detailApproval({{ $row->id }})"
                                                                        style="background-color: #f25450;">
                                                                        <p style="margin: 0px;">{{ $row->approver_name }}
                                                                        </p>
                                                                        <p style="font-size: 10px; margin: 0px;">Waiting
                                                                        </p>
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @else
                                            <td style="text-align: center;" colspan="5">Not submitted yet</td>
                                        @endif
                                    </tbody>
                                </table>

                                <table style="width: 100%; border: none !important;">
                                    <tr style="border: none !important;">
                                        <thead style="border: none !important;">
                                            <th style="border: none !important;">
                                                <div style="vertical-align: middle;">
                                                    <span class="label"
                                                        style="padding-bottom: 0px; background-color: #aee571; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                                    <span> = Approved</span>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <span class="label"
                                                        style="padding-bottom: 0px; background-color: #f25450; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                                    <span> = Waiting</span>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                    <span class="label"
                                                        style="padding-bottom: 0px; background-color: #65b7fc; border: 1px solid black; font-size: 9px;">&nbsp;</span>
                                                    <span> = Hold & Comment</span>
                                                </div>
                                            </th>
                                        </thead>
                                    </tr>
                                </table>

                            @endif



                        </div>

                        @php $approved = 0; @endphp
                        @foreach ($approval as $row)
                            @if ($row->status == 'Approved')
                                @php $approved++; @endphp
                            @endif
                        @endforeach

                        <div class="col-xs-3" style="padding: 0px;">

                            <div class="col-xs-12"
                                style="border: 1px solid #777777; padding-bottom: 15px; margin-bottom: 1%; border-radius: 5px;">
                                <div class="box-header with-border" style="padding-left: 0px;">
                                    <h4 class="box-title">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                            aria-expanded="true" class="">
                                            <h4 class="text-primary"
                                                style="margin-top: 10px; margin-bottom: 0px; font-size: 20px;">
                                                <i class="fa fa-th-list"></i>&nbsp;{{ $extra_order->eo_number }}
                                            </h4>
                                        </a>
                                    </h4>
                                </div>

                                <div id="collapseOne" class="panel-collapse collapse in" aria-expanded="true">
                                    <div class="text-muted" style="">
                                        @php
                                            $order_by = '';
                                            for ($i = 0; $i < count($user); $i++) {
                                                if (strtoupper($user[$i]->username) == strtoupper($extra_order->order_by)) {
                                                    $order_by = ucwords($user[$i]->name);
                                                    break;
                                                }
                                            }
                                        @endphp

                                        <p class="text-sm">Order By<br>
                                            <b>{{ $order_by }}</b>
                                        </p>
                                        <p class="text-sm">Recipient<br>
                                            <b>{{ $extra_order->attention }}</b>
                                        </p>
                                        <p class="text-sm">Division<br>
                                            <b>{{ $extra_order->division }}</b>
                                        </p>
                                        <p class="text-sm">Destination<br>
                                            <b>{{ $extra_order->destination_code }} - {{ $extra_order->destination_name }}
                                                ({{ $extra_order->destination_shortname }})</b>
                                        </p>

                                        <div class="col-xs-12" style="padding-left: 0px;">
                                            <p class="text-sm" style="margin-bottom: 0px;">BOM Progress
                                            <div class="progress progress-sm active" style="margin-bottom: 0;">
                                                <div class="progress-bar progress-bar-success progress-bar-striped"
                                                    role="progressbar"
                                                    aria-volumenow="{{ ($progress[0]->new_bom / $progress[0]->total) * 100 }}"
                                                    aria-volumemin="0" aria-volumemax="100"
                                                    style="width: {{ round(($progress[0]->new_bom / $progress[0]->total) * 100) }}%">
                                                </div>
                                            </div>
                                            <b class="d-block">{{ round(($progress[0]->new_bom / $progress[0]->total) * 100, 2) }}%
                                                Complete</b>
                                            </p>
                                        </div>

                                        <div class="col-xs-12" style="padding-left: 0px;">
                                            <p class="text-sm" style="margin-bottom: 0px;">Price Progress
                                            <div class="progress progress-sm active" style="margin-bottom: 0;">
                                                <div class="progress-bar progress-bar-success progress-bar-striped"
                                                    role="progressbar"
                                                    aria-volumenow="{{ ($progress[0]->new_price / $progress[0]->total) * 100 }}"
                                                    aria-volumemin="0" aria-volumemax="100"
                                                    style="width: {{ round(($progress[0]->new_price / $progress[0]->total) * 100) }}%">
                                                </div>
                                            </div>
                                            <b class="d-block">{{ round(($progress[0]->new_price / $progress[0]->total) * 100, 2) }}%
                                                Complete</b>
                                            </p>
                                        </div>

                                        <p class="text-sm">Approval Extra Order Confirmation<br>
                                            @if (count($approval) > 0)
                                                @if (count($approval) == $approved)
                                                    <b class="d-block">Fully approved</b>
                                                @elseif($approved > 0)
                                                    <b class="d-block">Partially approved</b>
                                                @else
                                                    <b class="d-block">Waiting for approval</b>
                                                @endif
                                            @else
                                                <b class="d-block">Not submitted yet</b>
                                            @endif
                                        </p>

                                        @php
                                            $po_by = '';
                                            for ($i = 0; $i < count($user); $i++) {
                                                if (strtoupper($user[$i]->username) == strtoupper($extra_order->po_by)) {
                                                    $po_by = ucwords($user[$i]->name);
                                                    break;
                                                }
                                            }
                                        @endphp

                                        <p class="text-sm">PO By<br>
                                            <b>{{ $po_by }}</b>
                                        </p>

                                        <p class="text-sm">PO Number<br>
                                            @if (is_null($extra_order->po_number))
                                                <b class="d-block">-</b>
                                            @else
                                                @php
                                                    $content = '';
                                                    $style = 'style="font-weight: bold; cursor: pointer;"';
                                                    
                                                    $obj = json_decode($extra_order->po_number);
                                                    
                                                    for ($i = 0; $i < count($obj); $i++) {
                                                        $content .= '<a ' . $style . ' id="' . $obj[$i] . '" onclick="downloadPo(id)">';
                                                        $content .= str_replace($extra_order->eo_number . '__', '', $obj[$i]);
                                                        $content .= '</a>';
                                                    
                                                        if ($i != count($obj) - 1) {
                                                            $content .= '<br>';
                                                        }
                                                    }
                                                    
                                                    print_r($content);
                                                @endphp
                                            @endif
                                        </p>

                                        <p class="text-sm">Status<br>
                                            <b class="d-block">{{ $extra_order->status }}</b>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'PC'))
                                <div class="col-xs-12"
                                    style="border: 1px solid #777777; padding-bottom: 15px; margin-bottom: 1%; border-radius: 5px;">
                                    <div class="box-header with-border" style="padding-left: 0px;">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"
                                                aria-expanded="false" class="collapsed">
                                                <h4 class="text-primary"
                                                    style="margin-top: 10px; margin-bottom: 0px; font-size: 20px;">
                                                    <i class="fa fa-gears"></i>&nbsp;Administrator Panel
                                                </h4>
                                            </a>
                                        </h4>
                                    </div>

                                    <div id="collapseTwo" class="panel-collapse collapse" aria-expanded="false">
                                        <div class="text-muted">
                                            @if (count($detail) != $bom_complete)
                                                <button style="margin-bottom: 2%;" onclick="sendTrialRequest()"
                                                    class="btn btn-xs btn-default">
                                                    <i class="fa fa-send"></i>&nbsp;&nbsp;&nbsp;Send Trial Request
                                                </button>
                                            @endif
                                            @if (count($detail) != $price_complete && $bom_complete > 0)
                                                <button style="margin-bottom: 2%;" onclick="sendPriceRequest()"
                                                    class="btn btn-xs btn-default">
                                                    <i class="fa fa-send"></i>&nbsp;&nbsp;&nbsp;Send Price Request
                                                </button>
                                            @endif

                                            <button style="margin-bottom: 2%;" id="btn-edit"
                                                onclick="showEdit('{{ $extra_order->eo_number }}')"
                                                class="btn btn-xs btn-success">
                                                <i class="fa fa-pencil"></i>&nbsp;&nbsp;&nbsp;Edit Order Data
                                            </button>

                                            <button style="margin-bottom: 2%;" onclick="generateSmbmr()"
                                                class="btn btn-xs btn-default">
                                                <i class="fa fa-gears"></i>&nbsp;&nbsp;&nbsp;Generate SMBMR
                                            </button>

                                            @if (count($approval) == $approved && count($approval) > 0)
                                                <button style="margin-bottom: 2%;" onclick="uploadPoPage('send')"
                                                    class="btn btn-xs btn-primary">
                                                    <i class="fa fa-send"></i>&nbsp;&nbsp;&nbsp;Resend PO Mail
                                                </button>
                                                <button style="margin-bottom: 2%;" onclick="uploadPoPage('open')"
                                                    class="btn btn-xs btn-primary">
                                                    &nbsp;&nbsp;&nbsp;<i class="fa fa-upload"></i>&nbsp;&nbsp;&nbsp;Upload
                                                    PO&nbsp;&nbsp;&nbsp;
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (
                                (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'PC')) &&
                                    $extra_order->eo_number >= 'EO202212003')
                                <div class="col-xs-12"
                                    style="border: 1px solid #777777; padding-bottom: 15px; margin-bottom: 1%; border-radius: 5px;">
                                    <div class="box-header with-border" style="padding-left: 0px;">
                                        <h4 class="box-title">
                                            <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"
                                                aria-expanded="false" class="collapsed">
                                                <h4 class="text-primary"
                                                    style="margin-top: 10px; margin-bottom: 0px; font-size: 20px;">
                                                    <i class="fa fa-search"></i>&nbsp;Tracing
                                                </h4>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapseThree" class="panel-collapse collapse" aria-expanded="false">
                                        <div class="box-body" style="padding-left: 0px; padding-right: 0px;">

                                            <ul class="timeline">
                                                <li class="time-label">
                                                    <span class="bg-gray"
                                                        style="padding-left: 5%; padding-right: 5%; color: #666666;">
                                                        {{ $extra_order->eo_number }}
                                                    </span>
                                                </li>

                                                @foreach ($timeline as $tl)
                                                    <li style="margin-right: 0px;">
                                                        @php print_r($tl->timeline_item_icon); @endphp

                                                        <div class="timeline-item"
                                                            style="border: 1px solid #d2d6de; margin-right: 0px;">
                                                            <span class="time">
                                                                <i class="fa fa-clock-o"></i> {{ $tl->time }}
                                                            </span>
                                                            <h4 class="timeline-header">
                                                                <span class="text-primary"
                                                                    style="font-weight: bold; font-size: 14px;">
                                                                    {{ $tl->timeline_header }}
                                                                </span>
                                                            </h4>
                                                            <div class="timeline-body"
                                                                style="font-size: 12px; text-align: justify;">
                                                                {{ $tl->timeline_body }}
                                                            </div>
                                                            <div class="timeline-footer" style="padding-top: 0px;">
                                                                @php echo $tl->timeline_footer; @endphp
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach

                                                @if ($extra_order->status == 'Complete')
                                                    <li>
                                                        <i class="fa fa-flag-checkered"></i>
                                                    </li>
                                                @else
                                                    <li style="margin-right: 0px;">
                                                        <div class="timeline-item"
                                                            style="border: 1px solid #d2d6de; margin-right: 0px;">
                                                            <h4 class="timeline-header">
                                                                <span class="text-primary"
                                                                    style="font-weight: bold; font-size: 14px;">
                                                                    In Progress ...
                                                                </span>
                                                            </h4>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <i class="fa fa-spin fa-spinner"></i>
                                                    </li>
                                                @endif

                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalSendApproval">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-xs-12" style="background-color: #00a65a;">
                        <h2 style="text-align: center; margin: 2%; font-weight: bold;">Approval List</h2>
                    </div>
                    <div class="col-xs-12" style="margin-top: 3%; padding: 0px;">
                        <div class="col-xs-4" style="padding: 0px;">
                            <div class="form-group">
                                <label>Select Related Production PIC</label><br>
                                <label>
                                    <input type="checkbox" class="minimal" id="kpp">
                                    &nbsp;&nbsp;Key Parts Process
                                </label><br>
                                <label>
                                    <input type="checkbox" class="minimal" id="bpp">
                                    &nbsp;&nbsp;Body Parts Process
                                </label><br>
                                <label>
                                    <input type="checkbox" class="minimal" id="solder">
                                    &nbsp;&nbsp;Koshuha Solder Process
                                </label><br>
                                <label>
                                    <input type="checkbox" class="minimal" id="handatsuke">
                                    &nbsp;&nbsp;Handatsuke Process
                                </label><br>
                                <label>
                                    <input type="checkbox" class="minimal" id="buffing">
                                    &nbsp;&nbsp;Buffing Key Process
                                </label><br>
                                <label>
                                    <input type="checkbox" class="minimal" id="st">
                                    &nbsp;&nbsp;Surface Treatment
                                </label><br>
                                <label>
                                    <input type="checkbox" class="minimal" id="assembly_sax">
                                    &nbsp;&nbsp;Assembly Sax Process
                                </label><br>
                                <label>
                                    <input type="checkbox" class="minimal" id="assembly_fl">
                                    &nbsp;&nbsp;Assembly FL Process
                                </label><br>
                                <label>
                                    <input type="checkbox" class="minimal" id="assembly_cl">
                                    &nbsp;&nbsp;Assembly CL Tanpo Case Process
                                </label><br>
                                <label>
                                    <input type="checkbox" class="minimal" id="edin">
                                    &nbsp;&nbsp;Educational Instrument
                                </label><br>
                            </div>
                        </div>
                        <div class="col-xs-8" style="padding-right: 0px;">
                            <div class="form-group">
                                <label>Note :</label><br>
                                <textarea class="form-control" id="send_message"></textarea>
                            </div>
                            <div class="form-group">
                                <label> Attachment :</label><br>
                                <div class="col-xs-8" style="padding-left: 0px;">
                                    <input type="file" id="send_attachment" class="form-control btn btn-default"
                                        style="text-align: left;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <center>
                        <button class="btn btn-success" onclick="sendApproval('{{ $extra_order->eo_number }}')">
                            <i class="fa fa-send-o"></i>&nbsp;&nbsp;Send
                        </button>
                    </center>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalApprovalMenu">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-xs-12" style="background-color: #66D9F5;">
                        <h2 style="text-align: center; margin: 2%; font-weight: bold;">Approval Menu</h2>
                    </div>
                    <div class="col-xs-12" style="margin-top: 3%;">

                        <input id="approval_id" hidden>

                        <div class="col-xs-4">
                            <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                onclick="resetApproval()">
                                <i class="fa fa-refresh" style="font-size: 5vw;"></i>
                                <br>
                                <br>
                                <span>Reset</span>
                            </button>
                        </div>
                        <div class="col-xs-4">
                            <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                onclick="resendApproval()">
                                <i class="fa fa-send-o" style="font-size: 5vw;"></i>
                                <br>
                                <br>
                                <span>Resend</span>
                            </button>
                        </div>
                        <div class="col-xs-4">
                            <button class="btn btn-default" style="width: 100%; font-weight: bold;"
                                onclick="viewApproval()">
                                <i class="fa fa-eye" style="font-size: 5vw;"></i>
                                <br>
                                <br>
                                <span>View</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" aria-hidden="true" data-keyboard="false" data-backdrop="static"
        style="overflow-y: auto;">
        <div class="modal-dialog modal-lg" style="width: 85%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3
                            style="background-color: #605ca8; font-weight: bold; padding: 10px 0px 10px 0px; margin-top: 0; color: white;">
                            Edit Extra Order
                        </h3>
                    </center>
                </div>
                <div class="modal-body">
                    <div class="form-group row" align="right">
                        <label class="col-xs-4">EO Number</label>
                        <div class="col-sm-5" style="padding-left: 0px;">
                            <input class="form-control" type="text" id="edit_eo_number" disabled>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-xs-4">Recipient</label>
                        <div class="col-sm-5" style="padding-left: 0px;">
                            <input class="form-control" type="text" id="edit_buyer" disabled>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-xs-4">Destination</label>
                        <div class="col-xs-5" style="padding-left: 0px;">
                            <input class="form-control" type="text" id="edit_destination" disabled>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-xs-4">Division</label>
                        <div class="col-xs-5" style="padding-left: 0px;">
                            <textarea class="form-control" type="text" rows="2" id="edit_division" disabled></textarea>
                        </div>
                    </div>
                    <div class="form-group row" align="right">
                        <label class="col-xs-4">Remark</label>
                        <div class="col-xs-5" style="padding-left: 0px;">
                            <textarea class="form-control" type="text" rows="2" id="edit_remark"></textarea>
                        </div>
                    </div>

                    <table class="table table-hover table-bordered" id="tableEdit">
                        <thead style="background-color: rgba(126,86,134,.7);">
                            <tr>
                                <th style="width: 5%;">Urgent</th>
                                <th style="width: 10%;">GMC Buyer</th>
                                <th style="width: 10%;">GMC YMPI</th>
                                <th style="width: 25%;">Description</th>
                                <th style="width: 6.5%;">UoM</th>
                                <th style="width: 6.5%;">Price<br>(USD)</th>
                                <th style="width: 12%;">ETD</th>
                                <th style="width: 6.5%;">Ship By</th>
                                <th style="width: 6.5%;">Qty</th>
                                <th style="width: 6.5%;">Amount<br>(USD)</th>
                                <th style="width: 4.5%;">Delete</th>
                            </tr>
                        </thead>
                        <tbody id="tableEditBody">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <center>
                        <button class="btn btn-warning pull-left" data-dismiss="modal" aria-label="Close"
                            style="font-weight: bold; font-size: 1.3vw; width: 30%;">Back<br>戻る</button>
                        <button class="btn btn-success pull-right"
                            style="font-weight: bold; font-size: 1.3vw; width: 68%;"
                            onclick="updateOrder()">UPDATE<br>アップデート</button>
                    </center>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadResult">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Error Message</h4>
                </div>
                <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                    <span style="font-size:1.5vw;">Error:
                        <span id="error-count" style="font-style:italic; font-weight:bold; color: red;"></span>
                        Raw material(s)
                    </span>

                    <table id="tableError" style="border: none;">
                        <tbody id="bodyError">
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/icheck.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('input[type="checkbox"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue'
            });

            $('#table-main').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [10, 25, 50, -1],
                    ['10 rows', '25 rows', '50 rows', 'Show all']
                ],
                'buttons': {
                    buttons: [{
                        extend: 'pageLength',
                        className: 'btn btn-default',
                    }, {
                        extend: 'copy',
                        className: 'btn btn-success',
                        text: '<i class="fa fa-copy"></i> Copy',
                        exportOptions: {
                            columns: ':not(.notexport)'
                        }
                    }]
                },
                'ordering': false,
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true,
                'columnDefs': [{
                        "targets": [2],
                        "className": "text-left",
                    },
                    {
                        "targets": [5, 7, 8],
                        "className": "text-right",
                    }
                ]
            });
        });

        function reset() {
            $("#send_message").html(CKEDITOR.instances.question.setData(""));
        }

        CKEDITOR.replace('send_message', {
            filebrowserImageBrowseUrl: '{{ url('kcfinder_master') }}',
            height: '150px'
        });

        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            todayHighlight: true,
            endDate: '{{ date('Y-m-d') }}'
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var update_list = [];



        function checkMaterial(id, value) {
            console.log(id);
            console.log(value);

            var key = id.split('_');
            id = key[3];

            if (value == '-') {
                return false;
            } else {
                if (value == 'NEW') {
                    if (key[2] == 'ympi') {
                        if ($('#edit_material_buyer_' + id).val() != 'NEW') {
                            $('#edit_material_buyer_' + id).val('NEW').trigger('change.select2');
                        }
                    } else {
                        if ($('#edit_material_ympi_' + id).val() != 'NEW') {
                            $('#edit_material_ympi_' + id).val('NEW').trigger('change.select2');
                        }
                    }

                    $('#edit_description_' + id).val('');
                    $('#row_' + id).find('td').eq(4).text('');
                    $('#row_' + id).find('td').eq(5).text('');
                    $('#row_' + id).find('td').eq(9).text('');

                    return false;
                } else {
                    var content = value.split('!');

                    var material_number_ympi = content[0];
                    var material_number_buyer = content[1];
                    var description = content[2];
                    var uom = content[3];
                    var sales_price = parseFloat(content[4]);
                    var storage_location = content[5];

                    var material_key = material_number_ympi + '!' + material_number_buyer + '!' + description + '!' + uom +
                        '!' + sales_price + '!' + storage_location;

                    console.log(material_key);
                    console.log($('#edit_material_buyer_' + id).val());


                    if (key[2] == 'ympi') {
                        if ($('#edit_material_buyer_' + id).val() != material_key) {
                            $('#edit_material_buyer_' + id).val(material_key).trigger('change.select2');
                        }
                    } else {
                        if ($('#edit_material_ympi_' + id).val() != material_key) {
                            $('#edit_material_ympi_' + id).val(material_key).trigger('change.select2');
                        }
                    }


                    $('#edit_description_' + id).val(description);
                    $('#row_' + id).find('td').eq(4).text(uom);
                    $('#row_' + id).find('td').eq(5).text(sales_price.toFixed(2));

                    var quantity = $('#edit_quantity_' + id).val();
                    var amount = sales_price * quantity;

                    $('#row_' + id).find('td').eq(9).text(amount.toFixed(2));

                    return false;
                }
            }
        }

        function checkQuantity(id) {

            if ($('#' + id).val().match(/^((\d+(\.\d *)?)|((\d*\.)?\d+))$/)) {
                var row = id.split('_');
                var sales_price = $('#row_' + row[2]).find('td').eq(5).text();
                var quantity = $('#' + id).val();
                var amount = sales_price * quantity;

                console.log(sales_price);
                console.log(quantity);

                $('#row_' + row[2]).find('td').eq(9).text(amount.toFixed(2));

            } else if ($('#' + id).val() == "") {
                return false;

            } else {
                $('#' + id).val('');
                audio_error.play();
                openErrorGritter('Please Enter Numeric Value.');
                return false;

            }
        }

        function deleteOrder(params) {

            var id = params.split('_')[1];
            var material_description = $('#edit_description_1520').val();

            if (confirm("Are you sure to delete ``" + material_description + "`` from this extra order request?")) {
                $("#loading").show();

                var x = {
                    id: id
                }

                $.post('{{ url('delete/extra_order_detail') }}', x, function(result, status, xhr) {
                    if (result.status) {
                        $("#loading").hide();
                        $("#alert").show();
                        $("#modalEdit").modal('hide');

                        openSuccessGritter('Success', 'Selected material successfully deleted');

                    } else {
                        openErrorGritter('Error!', 'Attempt to retrieve data failed');
                    }
                });
            }

        }

        function updateOrder() {

            var data = [];

            for (var i = 0; i < update_list.length; i++) {
                var content = $('#edit_material_ympi_' + update_list[i]).val().split('!');

                var material_number_ympi = content[0];
                var material_number_buyer = content[1];
                var description = content[2];
                var uom = content[3];
                var sales_price = parseFloat(content[4]);
                var storage_location = content[5];

                var etd = $('#edit_etd_' + update_list[i]).val();
                var shipment = $('#edit_shipment_' + update_list[i]).val();
                var quantity = $('#edit_quantity_' + update_list[i]).val();

                data.push({
                    'id': update_list[i],
                    'material_number_ympi': material_number_ympi,
                    'material_number_buyer': material_number_buyer,
                    'description': description,
                    'uom': uom,
                    'sales_price': sales_price,
                    'storage_location': storage_location,
                    'uom': uom,
                    'etd': etd,
                    'shipment': shipment,
                    'quantity': quantity,
                });
            }

            var x = {
                data: data
            }

            if (confirm("Are you sure to update this extra order request?")) {
                $("#loading").show();

                $.post('{{ url('update/extra_order') }}', x, function(result, status, xhr) {
                    if (result.status) {
                        $("#loading").hide();
                        $("#alert").show();
                        $("#modalEdit").modal('hide');

                        openSuccessGritter('Success', 'Extra order successfully updated');

                    } else {
                        openErrorGritter('Error!', 'Attempt to retrieve data failed');
                    }
                });
            }
        }

        function downloadEoc(eo_number) {
            window.open('{{ url('index/extra_order/eoc_pdf') }}' + '/' + eo_number, '_blank');
        }

        function downloadSendApp(eo_number) {
            window.open('{{ url('index/extra_order/send_app_pdf') }}' + '/' + eo_number, '_blank');
        }

        function downloadAtt() {

            var data = {
                attachment: $('#attachment').val()
            }

            $.get('{{ url('index/extra_order/attachment/') }}', data, function(result, status, xhr) {
                if (result.status) {
                    window.open(result.file_path);
                } else {
                    openErrorGritter('Error!', 'Attempt to retrieve data failed');
                }
            });

        }

        function downloadPo(po_number) {

            var data = {
                po_number: po_number
            }

            $.get('{{ url('index/extra_order/po_number/') }}', data, function(result, status, xhr) {
                if (result.status) {
                    window.open(result.file_path);
                } else {
                    openErrorGritter('Error!', 'Attempt to retrieve data failed');
                }
            });

        }

        $("#modalSendApproval").on("hidden.bs.modal", function() {
            $('#buyer_pc').iCheck("uncheck");
            $('#buyer_procurement').iCheck("uncheck");

            $('#kpp').iCheck("uncheck");
            $('#bpp').iCheck("uncheck");
            $('#solder').iCheck("uncheck");
            $('#handatsuke').iCheck("uncheck");
            $('#buffing').iCheck("uncheck");
            $('#st').iCheck("uncheck");
            $('#assembly_sax').iCheck("uncheck");
            $('#assembly_fl').iCheck("uncheck");
            $('#assembly_cl').iCheck("uncheck");
            $('#edin').iCheck("uncheck");

            $('#send_message').val("");
            $('#send_attachment').val("");
        });

        $("#modalSendApproval").on("shown.bs.modal", function() {
            var msg = '<?php echo $note; ?>';
            $("#send_message").html(CKEDITOR.instances.send_message.setData(msg));
        });


        function sendApproval(eo_number) {

            if (confirm('Apakah anda yakin untuk mengirim email approval ?')) {
                $("#loading").show();
                var group = [];

                if ($('#kpp').is(":checked")) {
                    group.push('NC Process Section');
                }
                if ($('#bpp').is(":checked")) {
                    group.push('Body Parts Process Section');
                }
                if ($('#solder').is(":checked")) {
                    group.push('Koshuha Solder Process Section');
                }
                if ($('#handatsuke').is(":checked")) {
                    group.push('Handatsuke . Support Process Section');
                }
                if ($('#buffing').is(":checked")) {
                    group.push('Buffing Key Process Section');
                }
                if ($('#st').is(":checked")) {
                    group.push('SurfaceTreatment Section');
                }
                if ($('#assembly_sax').is(":checked")) {
                    group.push('Assembly Sax Process Section');
                }
                if ($('#assembly_fl').is(":checked")) {
                    group.push('Assembly FL Process Section');
                }
                if ($('#assembly_cl').is(":checked")) {
                    group.push('Assembly CL . Tanpo . Case Process Section');
                }
                if ($('#edin').is(":checked")) {
                    group.push('Pianica Process Section');
                }


                if (group.length > 0) {
                    var formData = new FormData();
                    var message = CKEDITOR.instances.send_message.getData();
                    var attachment = $('#send_attachment').prop('files')[0];
                    var file = $('#send_attachment').val().replace(/C:\\fakepath\\/i, '').split(".");

                    formData.append('eo_number', eo_number);
                    formData.append('group', JSON.stringify(group));

                    formData.append('message', message);

                    formData.append('attachment', attachment);
                    formData.append('extension', file[1]);
                    formData.append('file_name', file[0]);

                    $.ajax({
                        url: "{{ url('input/extra_order/send_eoc') }}",
                        method: "POST",
                        data: formData,
                        dataType: 'JSON',
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(result) {
                            if (result.status) {
                                $("#loading").hide();
                                $("#alert").show();
                                $("#modalSendApproval").modal('hide');

                                $('#buyer_pc').iCheck("uncheck");
                                $('#buyer_procurement').iCheck("uncheck");

                                $('#kpp').iCheck("uncheck");
                                $('#bpp').iCheck("uncheck");
                                $('#welding').iCheck("uncheck");
                                $('#buffing').iCheck("uncheck");
                                $('#st').iCheck("uncheck");
                                $('#assembly_sax').iCheck("uncheck");
                                $('#assembly_fl').iCheck("uncheck");
                                $('#edin').iCheck("uncheck");

                                openSuccessGritter('Success', 'Approval EOC Successfully Sent');
                            } else {

                                if (result.message == 'Check Buyer Procurement NG') {
                                    openErrorGritter('Error', result.message);
                                    $('#error-count').text(result.count);
                                    $('#bodyError').html("");
                                    var tableData = "";
                                    var css = "padding: 0px 5px 0px 5px;";

                                    $.each(result.undefined, function(key, value) {
                                        tableData += '<tr>';

                                        tableData += '<td style="' + css +
                                            ' width:10%; text-align:left;">';
                                        tableData += value.material_number;
                                        tableData += '</td>';

                                        tableData += '<td style="' + css +
                                            ' width:50%; text-align:left;">';
                                        tableData += value.material_description;
                                        tableData += '</td>';

                                        tableData += '<td style="' + css +
                                            ' width:40%; text-align:left;">';
                                        tableData += value.message;
                                        tableData += '</td>';

                                        tableData += '</tr>';
                                    });
                                    $('#bodyError').append(tableData);

                                    $("#modalSendApproval").modal('hide');
                                    $("#uploadResult").modal('show');

                                } else {
                                    openErrorGritter('Error', result.message);
                                }

                                $("#loading").hide();

                            }
                        }
                    });
                } else {
                    $("#loading").hide();
                    openErrorGritter('Error', 'Select Related Production PIC');
                    return false;
                }
            }
        }

        function detailApproval(approval_id) {
            var role_code = $('#role_code').val();
            $('#approval_id').val(approval_id);
            var role = role_code.split('-')[1];

            if (role == 'MIS' || role == 'PC') {
                $("#modalApprovalMenu").modal('show');
            } else {
                viewApproval()
            }
        }

        function viewApproval() {
            var approval_id = $('#approval_id').val();
            window.open('{{ url('index/extra_order/view_approval') }}' + '/' + approval_id, '_self');
        }

        function resendApproval() {
            var approval_id = $('#approval_id').val();
            $('#loading').show();

            $.get('{{ url('index/extra_order/resend_eoc') }}' + '/' + approval_id, function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();
                    openSuccessGritter('Success', 'EOC Approval Sent Successfully');
                } else {
                    $("#loading").hide();
                    openErrorGritter('Error!', 'Approval EOC Failed to Sent');
                }
            });
        }

        function resetApproval() {
            var approval_id = $('#approval_id').val();
            $('#loading').show();

            $.get('{{ url('index/extra_order/reset_eoc') }}' + '/' + approval_id, function(result, status, xhr) {
                if (result.status) {
                    $("#loading").hide();
                    openSuccessGritter('Success', 'EOC Approval Reset Successfully');
                    $("#alert").show();

                } else {
                    $("#loading").hide();
                    openErrorGritter('Error!', 'Approval EOC Failed to Reset');
                }
            });
        }

        function sendTrialRequest() {

            if (confirm("Are you sure to send trial request notification?")) {
                $("#loading").show();
                var eo_number = '{{ $extra_order->eo_number }}';
                $.get('{{ url('index/extra_order/send_trial_request') }}' + '/' + eo_number, function(result, status,
                    xhr) {
                    if (result.status) {
                        $("#loading").hide();
                        openSuccessGritter('Success', 'Trial Request Notification Send Successfully');
                    }
                });
            }

        }

        function generateSmbmr() {

            if (confirm(
                    "For the smoothness of the mirai system, when generating SMBMR data use MIRAI server 6 (http://10.109.52.6/mirai/public)\nAre you sure to generate SMBMR?"
                )) {
                $("#loading").show();
                var eo_number = '{{ $extra_order->eo_number }}';
                var data = {
                    eo_number: eo_number
                }
                $.get('{{ url('index/extra_order/generate_smbmr') }}', data, function(result, status, xhr) {
                    if (result.status) {
                        $("#loading").hide();
                        openSuccessGritter('Success', 'SMBMR Generated Successfully');
                    }
                });
            }

        }

        function sendPriceRequest() {

            if (confirm("Are you sure to send sales price request notification?")) {
                $("#loading").show();
                var eo_number = '{{ $extra_order->eo_number }}';
                $.get('{{ url('index/extra_order/send_price_request') }}' + '/' + eo_number, function(result, status,
                    xhr) {
                    if (result.status) {
                        $("#loading").hide();
                        openSuccessGritter('Success', 'Sales Price Request Notification Send Successfully');
                    }
                });
            }

        }

        function uploadPoPage(remark) {

            if (remark == 'send') {
                if (confirm("Are you sure to send PO notification?")) {
                    $("#loading").show();
                    var eo_number = '{{ $extra_order->eo_number }}';
                    $.get('{{ url('index/extra_order/resend_po') }}' + '/' + eo_number, function(result, status, xhr) {
                        if (result.status) {
                            $("#loading").hide();
                            openSuccessGritter('Success', 'PO Notification Resend Successfully');
                        }
                    });
                }
            } else if (remark == 'open') {
                var eo_number = '{{ $extra_order->eo_number }}';
                window.open('{{ url('index/extra_order/upload_po/?eo_number=') }}' + eo_number, '_self');
            }
        }

        function showEdit(eo_number) {
            var data = {
                eo_number: eo_number
            }

            $('#loading').show();

            $.get('{{ url('fetch/show_extra_order') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#edit_eo_number').val(result.extra_order.eo_number);
                    $('#edit_buyer').val(result.extra_order.attention);
                    $('#edit_destination').val(result.extra_order.destination_code + ' - ' + result.extra_order
                        .destination_name + ' (' + result.extra_order.destination_shortname + ')');
                    $('#edit_division').val(result.extra_order.division);
                    $('#edit_remark').val((result.extra_order.remark || ''));


                    $('#tableEditBody').html("");
                    update_list = [];
                    var background_color = 'background-color: #eeeeee;';
                    var body = '';
                    for (var i = 0; i < result.detail.length; i++) {
                        update_list.push(result.detail[i].id);
                        body += '<tr id="row_' + result.detail[i].id + '">';

                        //URGENT
                        body +=
                            '<td style="padding: 3px 5px 3px 5px; text-align: center; vertical-align: middle; color: red; font-weight: bold; ' +
                            background_color + '">';
                        if (result.detail[i].urgent == 1) {
                            body += '<span><i class="fa fa-check-square-o"></i></span>';
                        }
                        body += '</td>';


                        //GMC BUYER
                        body += '<td style="padding: 3px 5px 3px 5px; ' + background_color +
                            '" id="td_edit_material_buyer_' + result.detail[i].id + '">';
                        body += '<select style= "width: 100%;" class="select2" id="edit_material_buyer_' + result
                            .detail[i].id + '" onchange="checkMaterial(id, value)">';
                        body += '<option value="NEW">NEW</option>';
                        $.each(result.material, function(key, value) {
                            if (value.material_number != "" && value.material_number != null && value
                                .material_number != "-") {
                                body += '<option value="' + value.material_number + '!' + value
                                    .material_number_buyer +
                                    '!' + value.description + '!' + value.uom + '!' +
                                    value.sales_price +
                                    '!' + value.storage_location + '">' + value.material_number_buyer +
                                    '</option>';
                            }
                        });
                        body += '</select>';
                        body += '</td>';


                        //GMC YMPI
                        body += '<td style="padding: 3px 5px 3px 5px; ' + background_color +
                            '" id="td_edit_material_ympi_' + result.detail[i].id + '">';
                        body += '<select style= "width: 100%;" class="select2" id="edit_material_ympi_' + result
                            .detail[i].id + '" onchange="checkMaterial(id, value)">';
                        body += '<option value="NEW">NEW</option>';
                        $.each(result.material, function(key, value) {
                            if (value.material_number != "" && value.material_number != null && value
                                .material_number != "-") {
                                body += '<option value="' + value.material_number + '!' + value
                                    .material_number_buyer +
                                    '!' + value.description + '!' + value.uom + '!' +
                                    value.sales_price +
                                    '!' + value.storage_location + '">' + value.material_number +
                                    '</option>';
                            }
                        });
                        body += '</select>';
                        body += '</td>';

                        //DESCRIPTION
                        body += '<td style="padding: 3px 5px 3px 5px; ' + background_color + '">';
                        body += '<input type="text" class="form-control" id="edit_description_' + result.detail[i]
                            .id + '" placeholder="Input Description" value="' + result.detail[i].description +
                            '">';
                        body += '</td>';

                        body += '<td style="' + background_color + '">' + result.detail[i].uom + '</td>';
                        body += '<td style="text-align: right; ' + background_color + '">' + result.detail[i]
                            .sales_price.toFixed(2) + '</td>';


                        //ETD
                        body += '<td style="padding: 3px 5px 3px 5px; ' + background_color + '">';
                        body += '<div class="input-group date">';
                        body +=
                            '<div class="input-group-addon" style="background-color: #ccff90; padding: 8px;"><i class="fa fa-calendar"></i></div>';
                        body +=
                            '<input style="text-align: center;" type="text" class="form-control datepicker" id="edit_etd_' +
                            result.detail[i].id + '" placeholder="Select Date" value="' + result.detail[i]
                            .request_date + '">';
                        body += '</div>';
                        body += '</td>';


                        // SHIP BY
                        body += '<td style="padding: 3px 5px 3px 5px; ' + background_color +
                            '" id="td_edit_shipment_' + result.detail[i].id + '">';
                        body +=
                            '<select style= "width: 100%; text-align-last: center;" class="select2" id="edit_shipment_' +
                            result.detail[i].id + '">';
                        body += '<option></option>';
                        body += '<option value="SEA">SEA</option>';
                        body += '<option value="AIR">AIR</option>';
                        body += '<option value="TRUCK">TRUCK</option>';
                        body += '</select>';
                        body += '</td>';


                        //QUANTITY
                        body += '<td style="padding: 3px 5px 3px 5px; ' + background_color + '">';
                        body +=
                            '<input style="text-align: right;" type="text" class="form-control" id="edit_quantity_' +
                            result.detail[i].id + '" placeholder="Input Quantity" value="' + result.detail[i]
                            .quantity + '" onkeyup="checkQuantity(id)">';
                        body += '</td>';

                        //AMOUNT
                        body += '<td style="text-align: right; ' + background_color + '">' + (result.detail[i]
                            .quantity * result.detail[i].sales_price).toFixed(2) + '</td>';

                        //DELETE
                        body +=
                            '<td style="padding: 3px 5px 3px 5px; text-align: center; vertical-align: middle; color: red; font-weight: bold; ' +
                            background_color + '">';
                        body += '<button class="btn btn-xs btn-danger" id="delete_' + result.detail[i].id +
                            '" onclick="deleteOrder(id)"><i class="fa fa-trash"></i></button>';
                        body += '</td>';

                        body += '</tr>';


                        $('#edit_etd_' + result.detail[i].id).datepicker({
                            autoclose: true,
                            format: "yyyy-mm-dd",
                            todayHighlight: true
                        });

                    }
                    $('#tableEditBody').append(body);
                    $('#modalEdit').modal('show');

                    for (var i = 0; i < result.detail.length; i++) {

                        $('#edit_material_ympi_' + result.detail[i].id).select2({
                            dropdownParent: $('#td_edit_material_ympi_' + result.detail[i].id)
                        });

                        $('#edit_material_buyer_' + result.detail[i].id).select2({
                            dropdownParent: $('#td_edit_material_buyer_' + result.detail[i].id)
                        });

                        $('#edit_shipment_' + result.detail[i].id).select2({
                            dropdownParent: $('#td_edit_shipment_' + result.detail[i].id)
                        });

                        var new_material = true;
                        var sales_price = 0;
                        for (var j = 0; j < result.material.length; j++) {
                            if (result.detail[i].material_number == result.material[j].material_number) {
                                new_material = false;
                                sales_price = result.material[j].sales_price;
                                break;
                            }
                        }

                        if (new_material) {
                            $('#edit_material_buyer_' + result.detail[i].id).val('NEW').trigger('change.select2');
                            $('#edit_material_ympi_' + result.detail[i].id).val('NEW').trigger('change.select2');
                        } else {
                            var material_key = result.detail[i].material_number + '!' + result.detail[i]
                                .material_number_buyer + '!' + result.detail[i].description + '!' + result.detail[i]
                                .uom + '!' + sales_price + '!' + result.detail[i]
                                .storage_location;

                            console.log('IN : ' + material_key);


                            $('#edit_material_buyer_' + result.detail[i].id).val(material_key).trigger(
                                'change.select2');
                            $('#edit_material_ympi_' + result.detail[i].id).val(material_key).trigger(
                                'change.select2');

                        }

                        $('#edit_shipment_' + result.detail[i].id).val(result.detail[i].shipment_by).trigger(
                            'change.select2');

                        $('#edit_description_' + result.detail[i].id).val(result.detail[i].description);


                        var sales_price = parseFloat(result.detail[i].sales_price);
                        var quantity = parseFloat(result.detail[i].quantity);
                        var amount = sales_price * quantity;
                        $('#row_' + result.detail[i].id).find('td').eq(4).text(result.detail[i].uom);
                        $('#row_' + result.detail[i].id).find('td').eq(5).text(sales_price.toFixed(2));
                        $('#row_' + result.detail[i].id).find('td').eq(9).text(amount.toFixed(2));


                        $('#edit_etd_' + result.detail[i].id).datepicker({
                            autoclose: true,
                            format: "yyyy-mm-dd",
                            todayHighlight: true
                        });
                    }

                    $('#loading').hide();

                } else {
                    openErrorGritter('Error!', 'Attempt to retrieve data failed');
                }
            });
        }

        function downloadIv(invoice_number) {

            var data = {
                invoice_number: invoice_number
            }

            $.get('{{ url('index/extra_order/invoice_number/') }}', data, function(result, status, xhr) {
                if (result.status) {
                    window.open(result.file_path);
                } else {
                    openErrorGritter('Error!', 'Attempt to retrieve data failed <br>データ取得が失敗');
                }
            });

        }

        function downloadWayBill(way_bill) {

            var data = {
                way_bill: way_bill
            }

            $.get('{{ url('index/extra_order/way_bill/') }}', data, function(result, status, xhr) {
                if (result.status) {
                    window.open(result.file_path);
                } else {
                    openErrorGritter('Error!', 'Attempt to retrieve data failed <br>データ取得が失敗');
                }
            });

        }

        function refreshAll() {
            location.reload(true);
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '5000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '5000'
            });
        }

        function truncate(str, n) {
            return (str.length > n) ? str.substr(0, n - 1) + '&hellip;' : str;
        };

        function replaceNull(s) {
            return s == null ? "-" : s;
        }
    </script>
@endsection
