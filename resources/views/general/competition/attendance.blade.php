@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<script src="{{ asset('/ckeditor/ckeditor.js') }}"></script>
<link href="<?php echo e(url("css/jquery.numpad.css")); ?>" rel="stylesheet">
<style type="text/css">
thead>tr>th{
    text-align:center;
}
tbody>tr>td{
    text-align:center;
}
tfoot>tr>th{
    text-align:center;
}
td:hover {
    overflow: visible;
}
table.table-bordered{
    border:1px solid black;
}
table.table-bordered > thead > tr > th{
    border:1px solid black;
}
table.table-bordered > tbody > tr > td{
    border:1px solid rgb(211,211,211);
    padding-top: 0px;
    padding-bottom: 0px;
}
table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
}
#loading, #error { display: none; }

.containers {
  display: block;
  position: relative;
  /*padding-left: 20px;*/
  margin-bottom: 6px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.containers input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.containers:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.containers input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the indicator (dot/circle) when checked */
.containers input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.containers .checkmark:after {
    top: 9px;
    left: 9px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: white;
}

.container_checkmark {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 13px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container_checkmark input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark_checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #999999;
}

/* On mouse-over, add a grey background color */
.container_checkmark:hover input ~ .checkmark_checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container_checkmark input:checked ~ .checkmark_checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark_checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container_checkmark input:checked ~ .checkmark_checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container_checkmark .checkmark_checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}

#tableCheck > tr > th, #tableCheck > tr > td,{
    padding: 2px;
}
#tableCheck > tr > td > input:hover{
    background-color: #7dfa8c !important;
}

input[type=number] {
        -moz-appearance:textfield; /* Firefox */
    }

</style>
@stop
@section('header')
<section class="content-header" >
    <h1>
        {{ $page }}<span class="text-purple"> {{ $title_jp }}</span>
    </h1>
</section>
@stop
@section('content')
<section class="content" >
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
        <p style="position: absolute; color: White; top: 45%; left: 45%;">
            <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
        </p>
    </div>
    <div class="row">
        <div class="col-xs-12" style="text-align: center;padding-right: 5px">
            <input type="text" name="tag" id="tag" style="width: 100%;font-size: 25px;text-align: center;margin-bottom: 10px;" placeholder="Scan ID Card / Ketik NIK">
            <table style="width: 100%;border:1px solid black">
                <thead>
                  <tr>
                      <!-- <td style="background-color: #c7c7c7;font-weight: bold;border:1px solid black;width: 2%" colspan="2">Asesor (Leader QA)</td> -->
                      <th style="width: 1%;background-color: #c6b3ff;border: 1px solid black;border-bottom: 2px solid black;">#</th>
                      <th style="width: 3%;background-color: #c6b3ff;border: 1px solid black;border-bottom: 2px solid black;">Team</th>
                      <th style="width: 2%;background-color: #c6b3ff;border: 1px solid black;border-bottom: 2px solid black;">Phone No</th>
                      <th style="width: 2%;background-color: #c6b3ff;border: 1px solid black;border-bottom: 2px solid black;">Player</th>
                      <th style="width: 4%;background-color: #c6b3ff;border: 1px solid black;border-bottom: 2px solid black;">Emp</th>
                      <th style="width: 4%;background-color: #c6b3ff;border: 1px solid black;border-bottom: 2px solid black;">Photo</th>
                      <th style="width: 4%;background-color: #c6b3ff;border: 1px solid black;border-bottom: 2px solid black;">Bagian</th>
                      <th style="width: 4%;background-color: #c6b3ff;border: 1px solid black;border-bottom: 2px solid black;">Lain-lain</th>
                      <th style="width: 4%;background-color: #c6b3ff;border: 1px solid black;border-bottom: 2px solid black;">Attend Date</th>
                  </tr>
                </thead>
                <tbody id="bodyTableAttendance">
                  
                </tbody>
            </table>
        </div>
    </div>


    <div class="modal fade" id="modalCode">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header no-padding">
                    <h4 style="background-color: #CE93D8; text-align: center; font-weight: bold; padding-top: 3%; padding-bottom: 3%;" class="modal-title">
                        PILIH LOMBA DAN TEAM
                    </h4>
                </div>
                <div class="modal-body table-responsive">
                    <div class="col-xs-12">
                        <div class="row">
                            <div class="col-xs-12">
                                <center><span style="font-weight: bold; font-size: 18px;">Cabang Lomba</span></center>
                            </div>
                            <div class="col-xs-12" style="padding-top: 10px">
                                <select class="form-control select2" id="category" style="width: 100%" data-placeholder="Pilih Cabang Lomba" onchange="changeCategory(this.value)">
                                    <option value=""></option>
                                    @foreach($category as $ct)
                                    <option value="{{$ct->category}}">{{$ct->category}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row" style="padding-top: 10px">
                            <div class="col-xs-12">
                                <center><span style="font-weight: bold; font-size: 18px;">Pilih Team</span></center>
                            </div>
                            <div class="col-xs-12" style="padding-top: 10px">
                                <select class="form-control select3" id="team_name" style="width: 100%" data-placeholder="Pilih Team">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12" style="padding-top: 20px">
                        <div class="modal-footer">
                            <div class="row">
                                <button onclick="saveTeam()" class="btn btn-success btn-block pull-right" style="font-size: 20px;font-weight: bold;">
                                    CONFIRM
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/moment.min.js")}}"></script>
<script src="{{ url("plugins/timepicker/bootstrap-timepicker.min.js")}}"></script>
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="<?php echo e(url("js/jquery.numpad.js")); ?>"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var category = <?php echo json_encode($category); ?>;
    var team_name = <?php echo json_encode($team_name); ?>;

    jQuery(document).ready(function() {
      $('#category').val('');
      $('#tag').val('');
        $('.select2').select2({
            allowClear:true,
            ropdownParent: $('#modalCode')
        });

        $('.select3').select2({
            allowClear:true,
            ropdownParent: $('#modalCode')
        });

        $('#modalCode').modal({
            backdrop: 'static',
            keyboard: false
        });

      $('body').toggleClass("sidebar-collapse");

    });

    function changeCategory(values) {
        $('#team_name').html('');
        var team_names = '';
        if (values != '') {
            team_names += '<option value=""></option>';
            for(var i = 0; i < team_name.length;i++){
                if (team_name[i].category == values) {
                    if (values == 'Karaoke') {
                        team_names += '<option value="'+team_name[i].team_name+'">'+team_name[i].team_name.split('_')[0]+' - '+team_name[i].team_name.split('_')[1]+' - '+team_name[i].team_name.split('_')[2]+'</option>';
                    }else{
                        team_names += '<option value="'+team_name[i].team_name+'">'+team_name[i].team_name+'</option>';
                    }
                }
            }
            $('#team_name').append(team_names);
        }
    }

    $('#tag').keydown(function(event) {
      if (event.keyCode == 13 || event.keyCode == 9) {
        $('#loading').show();
        var data = {
          tag:$('#tag').val(),
          category:$('#category').val(),
          team_name:$('#team_name').val(),
        }

        $.post('{{ url("scan/competition/attendance") }}', data, function(result, status, xhr){
            if(result.status){
              $('#loading').hide();
              openSuccessGritter('Success!', 'Scan Success');
              $("#tag").val('');
              $("#tag").focus();
              fetchTeam();
            }
            else{
              $('#loading').hide();
              $("#tag").val('');
              $("#tag").focus();
              audio_error.play();
              openErrorGritter('Error', result.message);
            }
        });
      }
    });

    $('#modalCode').on('shown.bs.modal', function () {
    });

    function saveTeam() {
      $('#loading').show();
      var category = $('#category').val();
      var team_name = $('#team_name').val();

      var data = {
        category:category,
        team_name:team_name
      }

      $.get('{{ url("update/competition/attendance") }}', data, function(result, status, xhr){
          if(result.status){
            $('#bodyTableAttendance').html('');
            var bodyTable = '';
            var index = 1;
            for(var i = 0; i < result.teams.length;i++){
              bodyTable += '<tr style="background-color:white;">';
              bodyTable += '<td style="border:1px solid black;text-align:right;padding-right:5px;">'+ index +'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ result.teams[i].team_name +'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:right;padding-right:5px;">'+ result.teams[i].phone_no +'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ result.teams[i].player_name +'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ result.teams[i].employee_id +'<br>'+result.teams[i].name+'</td>';
              var url = '{{url("images/avatar/")}}/'+result.teams[i].employee_id+'.jpg';
              bodyTable += '<td style="border:1px solid black;text-align:center;padding:4px;"><img src="'+ url +'" style="width:100px;"></td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ result.teams[i].department +'<br>'+result.teams[i].section+'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ (result.teams[i].song || '') +'<br>'+(result.teams[i].singer || '')+'<br>'+(result.teams[i].location || '')+'<br>'+(result.teams[i].attribute || '')+'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ (result.teams[i].attendance || '') +'</td>';
              bodyTable += '</tr>';
              index++;
            }

            $('#bodyTableAttendance').append(bodyTable);

            openSuccessGritter('Success!', 'Success Get Data');
            $('#modalCode').modal('hide');
            $('#loading').hide();

            $('#tag').val('');
            $('#tag').focus();
          }
          else{
            $('#loading').hide();
            audio_error.play();
            openErrorGritter('Error', result.message);
          }
      });
    }

    function fetchTeam() {
      $('#loading').show();
      var category = $('#category').val();
      var team_name = $('#team_name').val();

      var data = {
        category:category,
        team_name:team_name
      }

      $.get('{{ url("fetch/competition/attendance") }}', data, function(result, status, xhr){
          if(result.status){
            $('#bodyTableAttendance').html('');
            var bodyTable = '';
            var index = 1;
            for(var i = 0; i < result.teams.length;i++){
              if (result.teams[i].attendance != null) {
                var color = '#d2ffa1';
              }else{
                var color = 'white';
              }
              bodyTable += '<tr style="background-color:'+color+'">';
              bodyTable += '<td style="border:1px solid black;text-align:right;padding-right:5px;">'+ index +'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ result.teams[i].team_name +'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:right;padding-right:5px;">'+ result.teams[i].phone_no +'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ result.teams[i].player_name +'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ result.teams[i].employee_id +'<br>'+result.teams[i].name+'</td>';
              var url = '{{url("images/avatar/")}}/'+result.teams[i].employee_id+'.jpg';
              bodyTable += '<td style="border:1px solid black;text-align:center;padding:4px;"><img src="'+ url +'" style="width:100px;"></td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ result.teams[i].department +'<br>'+result.teams[i].section+'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ (result.teams[i].song || '') +'<br>'+(result.teams[i].singer || '')+'<br>'+(result.teams[i].location || '')+'<br>'+(result.teams[i].attribute || '')+'</td>';
              bodyTable += '<td style="border:1px solid black;text-align:left;padding-left:5px;">'+ (result.teams[i].attendance || '') +'</td>';
              bodyTable += '</tr>';
              index++;
            }

            $('#bodyTableAttendance').append(bodyTable);
            $('#loading').hide();
            $('#tag').val('');
            $('#tag').focus();
          }
          else{
            audio_error.play();
            $('#loading').hide();
            openErrorGritter('Error', result.message);
          }
      });
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
@endsection
