<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('logo_mirai.png') }}" />
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>YMPI 情報システム</title>
    <link rel="stylesheet" href="{{ url('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet"
    href="{{ url('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ url('plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ url('dist/css/skins/skin-purple.css') }}">
    <link rel="stylesheet" href="{{ url('fonts/SourceSansPro.css') }}">
    <link rel="stylesheet" href="{{ url('css/buttons.dataTables.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ url("plugins/pace/pace.min.css")}}"> --}}
    @yield('stylesheets')
</head>


<body class="hold-transition skin-purple layout-top-nav">
    <div class="wrapper">
        <header class="main-header">
            <nav class="navbar navbar-static-top">
                {{-- <div class="container"> --}}
                    <div class="navbar-header">
                        <a href="{{ url('/home') }}" class="logo">
                            <span style="font-size: 35px"><img src="{{ url('images/logo_mirai_bundar.png') }}"
                                height="45px" style="margin-bottom: 6px;">&nbsp;<b>M I R A I</b></span>
                            </a>
                        </div>
                        <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
                            <ul class="nav navbar-nav">
                                <li>
                                    <a style="font-size: 20px; font-weight: bold;" class="text-yellow">
                                        <?php if (isset($title)) {
                                            echo $title;
                                        } ?>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </nav>
                </header>
                <div class="content-wrapper" style="background-color: #ecf0f5; padding-top: 10px;">
                    <section class="content">
                        <div id="loading"
                        style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display:none">
                        <p style="position: absolute; color: White; top: 45%; left: 35%;">
                            <span style="font-size: 40px">Please wait a moment...
                                <i class="fa fa-spin fa-refresh"></i>
                            </span>
                        </p>
                    </div>
                    <div class="error" style="text-align: center;">
                        <h1><i class="fa fa-file-text-o"></i> Approval EJOR - {{ $ejor->form_id }}</h1>
                        <p>
                            <?php
                            if ($status == 'Not Allowed' || $status == 'Already Approved') {
                                $color = '#f5655b';
                                $fa = 'fa-minus-circle';
                            } elseif ($status == 'Approved' || $status == 'Hold & Comment') {
                                $color = '#00a65a';
                                $fa = 'fa-check';
                            } elseif ($status == 'Received') {
                                $color = '#3c8dbc';
                                $fa = 'fa-send';
                            } elseif ($status == 'Hold') {
                                $fa = 'fa-hand-stop-o';
                                $color = '#4f98c3';
                            } else {
                                $fa = 'fa-close';
                                $color = '#f5655b';
                            }
                            ?>

                            <?php if ($status != 'Evidence_Rejected'): ?>
                                <span style="font-size: 30px; color: {{ $color }}"><i
                                    class="fa {{ $fa }}"></i> {{ $status }}</span><br>
                                <?php endif ?>

                                <span style="font-size: 25px; color: {{ $color }}"></i> {{ $message }}</span>
                            </p>
                        </div>
                        <center>
                            @if ($status == 'Received')
                            <form method="GET"
                            action="{{ url('approval/ejor') . '/' . $ejor->form_id . '/Approved/Staff_PE' }}">
                            <div id="select_pic" style="width: 50%">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">PIC</label>
                                    <div class="col-sm-10">
                                        <select class="form-control select2" name="pic" id="pic"
                                        placeholder="Select PIC" required>
                                        <option value=""></option>
                                        @foreach ($pics as $pic)
                                        <option value="{{ $pic->pic_id }}/{{ $pic->pic_name }}">
                                            {{ $pic->pic_id }}/{{ $pic->pic_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12" style="margin-top: 10px">
                                        <button class="btn btn-success" id="btn_pic"><i class="fa fa-check"></i>
                                        Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @elseif($status == 'Hold')
                        <form method="GET" action="{{ url('approval/ejor') . '/' . $ejor->form_id . '/Hold/Holded' }}">
                            <div id="write_comment" style="width: 50%">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Comment</label>
                                    <div class="col-sm-10">
                                        <textarea id="note" name="note" placeholder="write your comment" required style="width: 100%"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12" style="margin-top: 10px">
                                        <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @elseif($status == 'Reject')
                        <form method="GET" action="{{ url('approval/ejor') . '/' . $ejor->form_id . '/Rejected/Rejected' }}">
                            <div id="write_comment" style="width: 50%">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Comment</label>
                                    <div class="col-sm-10">
                                        <textarea id="note" name="note" placeholder="write your comment" required style="width: 100%"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12" style="margin-top: 10px">
                                        <button class="btn btn-success"><i class="fa fa-check"></i> Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        @elseif($status == 'Evidence_Rejected')
                        <form method="GET" action="{{ url('verify/ejor') . '/' . $ejor->form_id . '/Reject' }}"
                            >
                            <div id="write_comment" style="width: 50%">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Note</label>
                                    <div class="col-sm-10">
                                        <textarea id="note_ev" name="note_ev" placeholder="write your note" required style="width: 100%"></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-12" style="margin-top: 10px">
                                        <button class="btn btn-success" type="submit" onclick="loading2()"><i class="fa fa-check"></i> Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        @endif
                    </center>
                </section>
            </div>
            @include('layouts.footer')
        </div>
        <script src="{{ url('bower_components/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ url('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ url('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ url('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
        <script src="{{ url('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
        <script src="{{ url('bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ url('bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
        <script src="{{ url('plugins/iCheck/icheck.min.js') }}"></script>
        <script src="{{ url('bower_components/fastclick/lib/fastclick.js') }}"></script>
        <script src="{{ url('dist/js/adminlte.min.js') }}"></script>
        <script src="{{ url('dist/js/demo.js') }}"></script>
        @section('scripts')
        <script>
            $('.select2').select2();

            function loading2() {
                $("#loading").show();
            }
        </script>
        @endsection
    </body>

    </html>
