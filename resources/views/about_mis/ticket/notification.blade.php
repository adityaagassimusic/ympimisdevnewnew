@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
        </h1>
    </section>
@endsection

@section('content')
    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>

        </div>
        <div class="row">
            <div class="error" style="text-align: center;">
                {{-- <h1><i class="fa fa-file-text-o"></i> {{ $head }}</h1> --}}
                <p>
                <h2>
                    @if ($code == 1)
                        <p>{{ $ticket_approver->ticket_id }}</p>
                        <p>Already Approved/Rejected at {{ $ticket_approver->approved_at }}</p>
                        <!-- <p>Failed!</p>					 -->
                    @elseif($code == 2)
                        <p>{{ $ticket_approver->ticket_id }}</p>
                        <p>Successfully {{ $ticket_approver->status }} By {{ $ticket_approver->approver_name }}</p>
                        <p>{{ $ticket_approver->approved_at }}</p>
                        <!-- <p style="font-weight: bold;">{{ $ticket_approver->status }}!</p> -->
                    @elseif($code == 3)
                        <p>{{ $ticket_approver->ticket_id }}</p>
                        <p>Previous approver has not approved or rejected</p>
                        <!-- <p>Failed!</p> -->
                    @else
                        <p>{{ $ticket_approver->ticket_id }}</p>
                        <p>You don't have authorized to approve ticket</p>
                        <!-- <p>Failed!</p> -->
                    @endif
                </h2>
                </p>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {

        });
    </script>
@endsection
