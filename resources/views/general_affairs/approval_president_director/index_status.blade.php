<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="shortcut icon" type="image/x-icon" href="{{ url('logo_mirai.png') }}" />
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>{{ $title }} | {{ $title_jp }}</title>
    <link rel="stylesheet" href="{{ url('bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/Ionicons/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
    <link rel="stylesheet" href="{{ url('plugins/iCheck/all.css') }}">
    <link rel="stylesheet" href="{{ url('bower_components/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ url('dist/css/skins/skin-purple.css') }}">
    <link rel="stylesheet" href="{{ url('fonts/SourceSansPro.css') }}">
    <link rel="stylesheet" href="{{ url('css/buttons.dataTables.min.css') }}">
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    {{-- <link rel="stylesheet" href="{{ url("plugins/pace/pace.min.css")}}"> --}}
    @yield('stylesheets')
    <style type="text/css">
        #loading,
        #error {
            display: none;
        }

        .status {
            font-size: 3vw;
            font-weight: bold;
        }

        .message {
            font-size: 2vw;
        }
    </style>
</head>

<body>
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
        </h1>
    </section>

    <section class="content">
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>

        </div>
        <div class="row">
            <div class="error" style="text-align: center;">
                <p>
                <h2>
                    

                </h2>
                </p>

            </div>
        </div>
    </section>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {

        });

        function detailExtraOrder(eo_number) {
            window.open('{{ url('index/extra_order/detail') }}' + '/' + eo_number, '_self');
        }
    </script>

</body>

</html>
