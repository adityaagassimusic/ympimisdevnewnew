<header class="main-header">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <a href="{{ url('/home') }}" class="logo">
        <span class="logo-mini"><img src="{{ url('images/logo_mirai_bundar.png') }}" height="45px"
                style="margin-bottom: 2px;"></span>

        <?php if(Auth::check()) {
                if(Auth::user()->role_code == 'emp-srv') { ?>
        <span class="logo-lg" style="font-size: 37px"><b>HR Q</b></span>
        <?php } else { ?>
        <span class="logo-lg" style="font-size: 37px"><b>M I R A I</b></span>
        <?php } } else { ?>
        <span class="logo-lg" style="font-size: 37px"><b>M I R A I</b></span>
        <?php } ?>
    </a>
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <form id="searchNav" style="display: inline-block !important; margin-left: 1%;">
            <div class="input-group input-group-sm" style="margin-top: 10px !important">
                <i class="fa fa-search" aria-hidden="true"></i>
                <input id="searchNavInput" class="form-control form-control-navbar" type="search"
                    placeholder="Search Here ..." aria-label="Search">
                <div class="searchNavResult" style="position: relative">
                    <ol>

                    </ol>
                </div>
            </div>
        </form>

        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        @php
                            $foto = strtoupper(Auth::user()->avatar);
                            $avatar = 'images/avatar/' . $foto;
                        @endphp
                        <img src="{{ url($avatar) }}" class="user-image" alt="User image">
                        <span class="hidden-xs">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <div class="col-xs-12 crop">
                                <img src="{{ url($avatar) }}" style="width: 45%;">
                            </div>
                            <p>
                                <small>{{ Auth::user()->username }} ({{ Auth::user()->role_code }})<br>
                                    {{ Auth::user()->name }}<br>
                                    {{ Auth::user()->email }}</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="row">
                                <div class="col-xs-4 pull-left">
                                    <a class="btn btn-info btn-flat" href="{{ url('setting/user') }}">Setting</a>
                                </div>
                                <div class="col-xs-4 pull-right">
                                    <a class="btn btn-danger btn-flat" href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        style="display: none;">
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
    </nav>
</header>
