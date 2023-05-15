@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
  .content-wrapper {
    background-color: #00b785 !important;
    background-image: url("{{ url("images/birthday2.jpg")}}");
    height: 100%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
  }

  .skin-purple .main-header .navbar {
    background-color: #00b785 !important;
    display: none !important;
  }

  .navbar-header {
    visibility: hidden;
  }


  .navbar-custom-menu {
    visibility: hidden;
  }

  @font-face {
    font-family: YPY;
    src: url("{{ url("fonts/Yippie-Yeah-Sans.ttf")}}");
  }

  @font-face {
    font-family: KMK;
    src: url("{{ url("fonts/Wash Your Hand.ttf")}}");
  }

  h2 {
    font-family: KMK, sans-serif;
    color: white; 
    text-align: center;
    font-size: 120pt;
    margin-top: 200px;
  }

  h1 {
    font-family: YPY, sans-serif;
    color: #ffc727; 
    text-align: center;
    font-size: 100pt;
    margin-top: 50px;
  }

  h3 {
    font-family: YPY, sans-serif;
    color: #ff963b; 
    text-align: center;
    font-size: 50pt;
    margin-top: 20px;
  }

  .strokeme {
    text-shadow: -5px -5px 0 #aa5600, 5px -5px 0 #aa5600, -5px 5px 0 #aa5600, 5px 5px 0 #aa5600;
  }

  .blink{
    animation:blinkingText 1s infinite;
  }
  @keyframes blinkingText{
    0%{     color: #f23f9c;    }
    49%{    color: #f23f9c; }
    60%{    color: transparent; }
    80%{    color: transparent;  }
    100%{   color: #f23f9c;    }
  }

  .blink2{
    animation:blinkingTexts 1s infinite;
  }
  @keyframes blinkingTexts{
    0%{    color: transparent; }
    49%{    color: transparent; }
    60%{     color: #f23f9c;    }
    80%{   color: #f23f9c;    }
    100%{    color: transparent;  }
  }

  .blink_head {
    animation: blinker 1s linear infinite;
  }

  @keyframes blinker {
    50% {
      opacity: 0;
    }
  }
</style>
@endsection
@section('header')

@endsection
@section('content')
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <!-- <marquee style="color: white; font-size: 20px; font-weight: bold"> --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  --  Happy Birthday  --  </marquee> -->
      <img src="{{ url("images/biru.gif")}}" style="position: absolute; top: 10px; left: 500px;" width="400">
      <h2 class="strokeme blink_head">Happy Birthday</h2>
      <h1><blink class="blink"><  </blink><blink class="blink2"> < </blink>Mr. Hiromichi Ichimura<blink class="blink2"> > </blink><blink class="blink"> ></blink></h1>
      <img src="{{ url("images/biru.gif")}}" style="position: absolute; top: 200px; right: 5px">
      <img src="{{ url("images/hijau.gif")}}" style="position: absolute; top: 150px; left: 50px;">
      <img src="{{ url("images/merah center.gif")}}" style="position: absolute; top: 450px; right: 20px" width="400">

      <!-- <img src="{{ url("images/flare_1.png")}}" style="position: absolute; top: 620px; left: 250px;" width="300"> -->
      <!-- <img src="{{ url("images/flare_2.png")}}" style="position: absolute; top: 620px; right: 250px;" width="300"> -->
      <h3>~ 25 Januari 2O22 ~</h3>
    <!--   <audio controls loop="loop" id="music">
        <source src="{{ url("sounds/backsound.mp3")}}" type="audio/mpeg">
        </audio> -->
      </div>
    </section>
    @stop

    @section('scripts')
    <script src="{{ url("js/jszip.min.js")}}"></script>
    <script src="{{ url("js/vfs_fonts.js")}}"></script>
    <script>  
      var audio = new Audio('{{ url("hbd.mp3")}}');
      audio.play();
      audio.loop = true;

      jQuery(document).ready(function() {
        // $("#music").get(0).play();
      })
    </script>

    @stop