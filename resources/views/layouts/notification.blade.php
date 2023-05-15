<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('logo_mirai.png') }}" />
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {{-- <meta content="width=device-width, user-scalable=yes, initial-scale=1.0" name="viewport"> --}}
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>
        @if (isset($title) && isset($title_jp))
            {{ $title }} {{ $title_jp }}
        @else
            YMPI 情報システム
        @endif
    </title>
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
    @yield('stylesheets')
    <style>
        aside {
            font-size: 12px;
        }

        .crop {
            overflow: hidden;
        }

        .crop img {
            margin: -10% 0 -10% 0;
        }

        .sidebar-menu>li>a {
            display: none;
        }

        .sidebar-toggle {
            display: none;
        }

        .treeview-menu>li>a {
            padding: 3px 5px 3px 15px;
            display: block;
            font-size: 12px;
        }

        #searchNav {
            position: relative;
        }

        #searchNav i {
            position: absolute;
            left: 8px;
            top: 8px;
            z-index: 500;
            color: rgba(128, 128, 128, 0.800);
        }

        #searchNavInput {
            padding-left: 30px;
            border-radius: 40px;
        }

        .searchNavResult {
            display: none;
            position: absolute !important;
            background-color: white;
            z-index: 5000;
            margin-top: 30px;
            padding: 5px;

            width: 500px;
            border-radius: 5px;
            text-transform: capitalize;
        }

        .searchNavResult ol {
            padding: 2px 10px !important;
            list-style: none;
        }

        .searchNavResult li {
            border-bottom: 1px solid rgba(51, 51, 51, 0.305);
            padding: 2px 5px;
        }
    </style>
</head>

<body class="hold-transition skin-purple">
    <div class="wrapper">
        @include('layouts.header')
        <div class="content-wrapper" style="margin : 0px;">
            @yield('header')
            @yield('content')
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
    @yield('scripts')
</body>

</html>
