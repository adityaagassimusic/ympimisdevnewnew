<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>
    @if(isset($title) && isset($title_jp))
    {{$title}} {{$title_jp}}
    @else 
    YMPI 情報システム
    @endif
  </title>
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/Ionicons/css/ionicons.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">
  <link rel="stylesheet" href="{{ url("plugins/iCheck/all.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/select2/dist/css/select2.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap-daterangepicker/daterangepicker.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/AdminLTE.min.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/skins/skin-purple.css")}}">
  <link rel="stylesheet" href="{{ url("fonts/SourceSansPro.css")}}">
  <link rel="stylesheet" href="{{ url("css/buttons.dataTables.min.css")}}">
  <meta name="apple-mobile-web-app-capable" content="yes" />
  {{-- <link rel="stylesheet" href="{{ url("plugins/pace/pace.min.css")}}"> --}}
  @yield('stylesheets')

  <style>
    .crop {
      overflow: hidden;
    }
    .crop img {
      margin: -10% 0 -10% 0;
    }
  </style>
</head>


<body class="hold-transition skin-purple layout-top-nav">
  <div class="wrapper">
    <header class="main-header">
      <nav class="navbar navbar-static-top">
        {{-- <div class="container"> --}}
          <div class="navbar-header">
            <a href="{{ url("/home") }}" class="logo">
              @if(isset($page))
              @if($page == "Employment Services")
              <span style="font-size: 35px"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="45px" style="margin-bottom: 6px;">&nbsp;<b>HR-Qu</b></span>
              @else
              <span style="font-size: 35px"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="45px" style="margin-bottom: 6px;">&nbsp;<b>M I R A I</b></span>
              @endif
              @else
              <span style="font-size: 35px"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="45px" style="margin-bottom: 6px;">&nbsp;<b>M I R A I</b></span>
              @endif
            </a>
          </div>
          <div class="collapse navbar-collapse pull-left" id="navbar-collapse">
            <ul class="nav navbar-nav">
              <li>
                <a style="font-size: 20px; font-weight: bold;" class="text-yellow">
                  <?php if (isset($title)) {
                   echo $title." (".$title_jp.")";
                 } ?>
               </a>
             </li>
           </ul>
         </div>
         @if(Auth::user() != "")
         <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li class="dropdown user user-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                @php
                $foto = strtoupper(Auth::user()->avatar);
                $avatar = 'images/avatar/'.$foto;
                $alt = 'images/avatar/image-user.png';
                @endphp
                <img src="{{ url($avatar) }}" class="user-image" alt="User Image">
                <span class="hidden-xs">{{Auth::user()->name}}</span>
              </a>
              <ul class="dropdown-menu">
                <li class="user-header">
                  <div class="col-xs-12 crop">
                    <img src="{{ url($avatar) }}" style="width: 45%;">
                  </div>
                  <p>
                    {{Auth::user()->name}}
                    <small>{{Auth::user()->email}}</small>
                  </p>
                </li>
                <li class="user-footer">
                  <div class="row">
                    <div class="col-xs-4 pull-left">
                      <a class="btn btn-info btn-flat" href="{{ url("setting/user") }}">Setting</a>
                    </div>
                    <div class="col-xs-4 pull-right">
                      <a class="btn btn-danger btn-flat" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                      </a>
                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                      </form>
                    </div>
                  </div>
                </li>
              </ul>
            </li>
            
            @if (Auth::user()->name != 'Display')
            <li class="dropdown notifications-menu">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-file-o"></i>
                <span class="label label-danger" id="notif_count"></span>
              </a>
              <ul class="dropdown-menu" id="notif_list">

              </ul>
            </li>
            @endif

          </ul>
        </div>
        @endif
      </nav>
    </header>
    <div class="content-wrapper" style="background-color: rgb(60,60,60); padding-top: 10px;">
      @yield('content')
    </div>
    @include('layouts.footer')
  </div>
  <script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
  <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
  <script src="{{ url("bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>
  <script src="{{ url("bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")}}"></script>
  <script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>
  <script src="{{ url("bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js")}}"></script>
  <script src="{{ url("bower_components/jquery-slimscroll/jquery.slimscroll.min.js")}}"></script>
  <script src="{{ url("plugins/iCheck/icheck.min.js")}}"></script>
  <script src="{{ url("bower_components/fastclick/lib/fastclick.js")}}"></script>
  {{-- <script src="{{ url("bower_components/PACE/pace.min.js")}}"></script> --}}
  <script src="{{ url("dist/js/adminlte.min.js")}}"></script>
  <script src="{{ url("dist/js/demo.js")}}"></script>
  {{-- <script>$(document).ajaxStart(function() { Pace.restart(); });</script> --}}
  <script>
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    jQuery(document).ready(function() {
      fetchNotification();
    });

    function fetchNotification(){
      var data = {

      }
      $.get('{{ url("fetch/notification") }}', data, function(result, status, xhr){
        if(result.status){
          var notif_list = "";
          var notif_count = 0;
          $('#notif_list').html("");

          notif_list += '<li>';
          notif_list += '<ul class="menu">';

          $.each(result.notifications, function(key, value){
            notif_count += parseInt(value.count);
            if(value.count > 0){
              notif_list += '<li>';
              notif_list += '<a href="{{ url('') }}/'+value.url+'">';
              notif_list += '<div class="col-xs-1 pull-left" style="padding: 0;">';
              notif_list += '<i class="fa fa-caret-right"></i>';
              notif_list += '</div>';
              notif_list += '<div class="col-xs-9 pull-left" style="padding: 0;">';
              notif_list += value.title+'<br><span style="color: purple">'+value.title_jp+'</span>';
              notif_list += '</div>';
              notif_list += '<div class="col-xs-2 pull-right">';
              notif_list += '<span class="label label-danger">'+value.count+'</span>';
              notif_list += '</div>';
              notif_list += '</a>';
              notif_list += '</li>';            
            }
          });

          notif_list += '</ul>';
          notif_list += '</li>';

          $('#notif_count').text(notif_count);
          $('#notif_list').append('<li class="header">You have '+notif_count+' notifications</li>');
          $('#notif_list').append(notif_list); 
        }
        else{
          alert(result.message);
        }
      });
    }
  </script>
  @yield('scripts')
</body>
</html>
