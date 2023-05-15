@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
  .content-wrapper {
    background-color: #4b1e78 !important;
    /*background-image: url("{{ url("images/birthday3.jpg")}}");*/
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
    color: white; 
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
      <!-- <h1 style="margin-top: 600px; color : #9800aa"><blink class="blink"><  </blink><blink class="blink2"> < </blink>Mr. Muhammad Dzulkifli<blink class="blink2"> > </blink><blink class="blink"> ></blink></h1> -->

      <h1 id="jam" style="margin-top: 100px;padding-top: 30px;font-size: 10em;font-weight: bold;text-align: center;margin-bottom: -70px">SELAMAT DATANG <br> PESERTA SPI YIIP</h1>

      <!-- <audio controls loop="loop" id="music">
        <source src="{{ url("sounds/backsound.mp3")}}" type="audio/mpeg">
        </audio> -->
      </div>
    </section>
    @stop

    @section('scripts')
    <script src="{{ url("js/jszip.min.js")}}"></script>
    <script src="{{ url("js/vfs_fonts.js")}}"></script>
    <script>  

      jQuery(document).ready(function() {
        // $("#music").get(0).play();
      })
    </script>

    @stop