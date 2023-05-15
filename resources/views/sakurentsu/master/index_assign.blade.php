<!DOCTYPE html>
<html>
<head>
  <link rel="shortcut icon" type="image/x-icon" href="{{ url("logo_mirai.png")}}" />
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <title>YMPI 情報システム</title>
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap/dist/css/bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/font-awesome/css/font-awesome.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/Ionicons/css/ionicons.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")}}">
  <link rel="stylesheet" href="{{ url("plugins/iCheck/all.css")}}">
  <link rel="stylesheet" href="{{ url("bower_components/select2/dist/css/select2.min.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/AdminLTE.min.css")}}">
  <link rel="stylesheet" href="{{ url("dist/css/skins/skin-purple.css")}}">
  <link rel="stylesheet" href="{{ url("fonts/SourceSansPro.css")}}">
  <link rel="stylesheet" href="{{ url("css/buttons.dataTables.min.css")}}">

  <link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
  
  @yield('stylesheets')
</head>


<body class="hold-transition skin-purple layout-top-nav">
  <div class="wrapper">
    <header class="main-header" >
      <nav class="navbar navbar-static-top">
        {{-- <div class="container"> --}}
          <div class="navbar-header">
            <a href="{{ url("/home") }}" class="logo">
              <span style="font-size: 35px"><img src="{{ url("images/logo_mirai_bundar.png")}}" height="45px" style="margin-bottom: 6px;">&nbsp;<b>M I R A I</b></span>
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
        <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
          <p style="position: absolute; color: White; top: 45%; left: 35%;">
            <span style="font-size: 40px">Loading, please wait <i class="fa fa-spin fa-refresh"></i></span>
          </p>
        </div>
        <?php if ($category == 'manager') { ?>
          <div id="assign">
            <div style="text-align: center;" id="inputan">
              <h1><i class="fa fa-file-text-o"></i> {{ $sk->sakurentsu_number }}</h1>
              <p>
                <h2>
                  {{ $sk->title }}<br>
                </h2>
                <h3>{{ $sk->category }}</h3><br>
              </p>
              <p>Please Assign PIC Staff</p>
              <center><select id="emp_staff" class="form-control select2"  style="width: 60%" data-placeholder="select staff">
                <option></option>
                <?php foreach ($staff_list as $emp) { ?>
                  <option value="{{ $emp->employee_id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
                <?php } ?>
              </select></center><br><br>
              <?php if ($sk->category == '3M') { ?>
                <button class="btn btn-success" onclick="assign('3M')"><i class="fa fa-check"></i>&nbsp; Send Email to Assigned PIC</button>
              <?php } else { ?>
                <button class="btn btn-success" onclick="assign('Trial')"><i class="fa fa-check"></i>&nbsp; Receive Trial Request & Send Email to Assigned PIC</button>
              <?php } ?>
            </div>

            <div id="pesan" style="display: none; text-align: center;">
              <h1><i class="fa fa-file-text-o"></i> {{ $sk->sakurentsu_number }}</h1>
              <p>
                <h2 class="text-green">
                  <i class="fa fa-check-circle fa-lg"></i> Sakurentsu successfully received and assigned<br>
                </h2>
              </p>
            </div>
          </div>
        <?php } else if($sk->position == 'interpreter') { ?>
          <div id="interpreter">
            <div style="text-align: center;" id="inputan_interpreter">
              <h1><i class="fa fa-file-text-o"></i> {{ $sk->sakurentsu_number }}</h1>
              <p>
                <h2>
                  {{ $sk->title }}<br>
                </h2>
                <h3>Sakurentsu {{ $sk->category }}</h3><br>
              </p>
              <p>Please Assign PIC to Interpreter Staff</p>
              <center><select id="emp_staff_interpreter" class="form-control select2" style="width: 60%" data-placeholder="select staff">
                <option></option>
                <?php foreach ($staff_list as $emp) { 
                  if($emp->group == 'Interpreter Group') {
                    ?>
                    <option value="{{ $emp->employee_id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
                  <?php } } ?>
                </select></center><br><br>

                <button class="btn btn-success" onclick="assign('Interpreter')"><i class="fa fa-check"></i>&nbsp; Assigned PIC</button>
              </div>
            </div>

            <div id="pesan_interpreter" style="display: none; text-align: center;">
              <h1><i class="fa fa-file-text-o"></i> {{ $sk->sakurentsu_number }}</h1>
              <p>
                <h2 class="text-green">
                  <i class="fa fa-check-circle fa-lg"></i> Sakurentsu successfully assigned<br>
                </h2>
              </p>
            </div>
          <?php } else if($category == 'interpreter_tiga_em') { ?>
            <div id="interpreter_tiga_em">
              <div style="text-align: center;" id="inputan_interpreter_tiga_em">
                <h1><i class="fa fa-file-text-o"></i> {{ $sk->sakurentsu_number }}</h1>
                <p>
                  <h3>3M Request - {{ $sk->category }}</h3><br>
                </p>
                <center>
                  <table style="font-size: 25px; font-weight: bold;">
                    <tr>
                      <td>Title  </td>
                      <td>: {{ $sk->title }}</td>
                    </tr>
                    <tr>
                      <td>Created By &nbsp;</td>
                      <td>: {{ $sk->name }}</td>
                    </tr>
                  </table>
                </center>
                <br>
                <p>Please Assign PIC to Interpreter Staff</p>
                <center><select id="emp_staff_interpreter_tiga_em" class="form-control select2" style="width: 60%" data-placeholder="select staff">
                  <option></option>
                  <?php foreach ($staff_list as $emp) { 
                    if($emp->group == 'Interpreter Group') {
                      ?>
                      <option value="{{ $emp->employee_id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
                    <?php } } ?>
                  </select></center><br><br>

                  <button class="btn btn-success" onclick="assign('Interpreter 3M')"><i class="fa fa-check"></i>&nbsp; Assigned PIC</button>
                </div>
              </div>

              <div id="pesan_interpreter_tiga_em" style="display: none; text-align: center;">
                <h1><i class="fa fa-file-text-o"></i> {{ $sk->sakurentsu_number }}</h1>
                <p>
                  <h2 class="text-green">
                    <i class="fa fa-check-circle fa-lg"></i> 3M request translator successfully assigned<br>
                  </h2>
                </p>
              </div>
            <?php } else if($category == 'Trial') { ?>
              <div id="interpreter">
                <div style="text-align: center;" id="inputan_trial">
                  <h1><i class="fa fa-file-text-o"></i> {{ $sk->sakurentsu_number }}</h1>
                  <p>
                    <h2>
                      {{ $sk->title }}<br>
                    </h2>
                    <h3>Sakurentsu {{ $sk->category }}</h3><br>
                  </p>
                  <p>Please Assign PIC Staff</p>
                  <center><select id="emp_staff" class="form-control select2" style="width: 60%" data-placeholder="select staff">
                    <option></option>
                    <?php foreach ($staff_list as $emp) { 
                      ?>
                      <option value="{{ $emp->employee_id }}">{{ $emp->employee_id }} - {{ $emp->name }}</option>
                    <?php } ?>
                  </select></center><br><br>

                  <button class="btn btn-success" onclick="assign('Trial')"><i class="fa fa-check"></i>&nbsp; Assigned PIC</button>
                </div>
              </div>

              <div id="pesan_trial" style="display: none; text-align: center;">
                <h1><i class="fa fa-file-text-o"></i> {{ $sk->sakurentsu_number }}</h1>
                <p>
                  <h2 class="text-green">
                    <i class="fa fa-check-circle fa-lg"></i> Sakurentsu successfully received and assigned<br>
                  </h2>
                </p>
              </div>
            <?php } ?>
          </section>
        </div>
        @include('layouts.footer')
      </div>
      <script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
      <script src="{{ url("bower_components/bootstrap/dist/js/bootstrap.min.js")}}"></script>
      <script src="{{ url("dist/js/adminlte.min.js")}}"></script>
      <script src="{{ url("dist/js/demo.js")}}"></script>
      <script src="{{ url("js/jquery.gritter.min.js") }}"></script>
      <script src="{{ url("bower_components/select2/dist/js/select2.full.min.js")}}"></script>

      @yield('scripts')
      <script>
        $('.select2').select2();

        function assign(category) {
          if (category == "Interpreter") {
            var pic = $("#emp_staff_interpreter").val();
          } else if(category == "3M" || category == "Trial"){
            var pic = $("#emp_staff").val();
          } else if(category == "Interpreter 3M"){
            var pic = $("#emp_staff_interpreter_tiga_em").val();
          }

          if (pic == "") {
            openErrorGritter('Error', 'Please choose PIC First');
            return false;
          }

          $("#loading").show();

          var data = {
            sakurentsu_number : "{{ $sk->sakurentsu_number }}",
            category : category,
            pic : pic
          }

          $.get('{{ url("assign/sakurentsu") }}', data, function(result, status, xhr){
            if (result.status) {
              $("#loading").hide();
              openSuccessGritter('Success', 'PIC has been Assigned');

              if (category == "Interpreter") {
                $("#pesan_interpreter").show();
                $("#inputan_interpreter").hide();
              } else if(category == "3M" || category == "Trial") {
                $("#pesan").show();
                $("#pesan_trial").show();

                $("#inputan").hide();
                $("#inputan_trial").hide();
              } else if(category == "Interpreter 3M") {
                $("#pesan_interpreter_tiga_em").show();
                $("#inputan_interpreter_tiga_em").hide();
              }

            } else {
              openErrorGritter('Error', result.message);
            }
          })
        }

        var audio_error = new Audio('{{ url("sounds/error.mp3") }}');

        function openErrorGritter(title, message) {
          jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-danger',
            image: '{{ url("images/image-stop.png") }}',
            sticky: false,
            time: '2000'
          });
        }

        function openSuccessGritter(title, message){
          jQuery.gritter.add({
            title: title,
            text: message,
            class_name: 'growl-success',
            image: '{{ url("images/image-screen.png") }}',
            sticky: false,
            time: '2000'
          });
        }
      </script>
    </body>
    </html>
