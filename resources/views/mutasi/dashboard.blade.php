@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

table.table-bordered{
  border:1px solid rgba(150, 150, 150, 0);
}
table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56);
  text-align: center;
  background-color: #f0f0ff;  
  color:black;
}
table.table-bordered > tbody > tr > td{
  border-collapse: collapse !important;
  border:1px solid rgb(54, 59, 56);
  background-color: #f0f0ff;
  color: black;
  vertical-align: middle;
  text-align: center;
  padding:3px;
}
table.table-condensed > thead > tr > th{   
  color: black
}
table.table-bordered > tfoot > tr > th{
  border:1px solid rgb(150,150,150);
  padding:0;
}
table.table-bordered > tbody > tr > td > p{
  color: #abfbff;
}

table.table-striped > thead > tr > th{
  border:1px solid black;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  border: 1px solid #eeeeee;
  border-collapse: collapse;
  color: black;
  padding: 3px;
  vertical-align: middle;
  text-align: center;
  background-color: white;
}

thead input {
  width: 100%;
  padding: 3px;
  box-sizing: border-box;
}
thead>tr>th{
  text-align:center;
}
tfoot>tr>th{
  text-align:center;
}
td:hover {
  overflow: visible;
}
table > thead > tr > th{
  border:1px solid #f4f4f4;
  color: white;
}
#tabelmonitor{
  font-size: 1vw;
}

.zoom{
   -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  -webkit-animation: zoomin 5s ease-in infinite;
  animation: zoomin 5s ease-in infinite;
  transition: all .5s ease-in-out;
  overflow: hidden;
}
@-webkit-keyframes zoomin {
  0% {transform: scale(0.7);}
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
}
@keyframes zoomin {
  0% {transform: scale(0.7);}   
  50% {transform: scale(1);}
  100% {transform: scale(0.7);}
} /*End of Zoom in Keyframes */

/* Zoom out Keyframes */
@-webkit-keyframes zoomout {
  0% {transform: scale(0);}
  50% {transform: scale(0.5);}
  100% {transform: scale(0);}
}
@keyframes zoomout {
    0% {transform: scale(0);}
  50% {transform: scale(0.5);}
  100% {transform: scale(0);}
}/*End of Zoom out Keyframes */


#loading, #error { display: none; }

</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    <span class="text-purple">Mutasi Satu Departemen Monitoring & Control</span>
  </h1>
  <br>
</section>
@endsection

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<section class="content" style="padding-top: 0; padding-bottom: 0">
  <div class="row">
    <div class="col-md-12" style="padding: 1px !important">
        <div class="col-xs-2">
          <div class="input-group">
            <a href="javascript:void(0)" onclick="openModalCreate()" class="btn btn-success btn-md" style="color:white"><i class="fa fa-plus"></i> Buat Mutasi</a>
          </div>
        </div>
         <div class="col-xs-2">
          <div class="input-group date">
            <div class="input-group-addon bg-green" style="border: none;">
              <i class="fa fa-calendar"></i>
            </div>
            <input type="text" class="form-control datepicker" id="dateto" placeholder="Pilih Bulan" onchange="drawChart()">
          </div>
        </div>  
    </div>

      <div class="col-md-12">
        <div class="col-md-12" style="margin-top: 5px; padding:0 !important">
            <div id="chart" style="width: 100%"></div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="col-md-12" style="margin-top: 5px; padding:0 !important; overflow-y:hidden; overflow-x:scroll;">
          <table id="tableResume" class="table table-bordered" style="width: 100%;margin-top: 0px">
            <thead style="background-color: rgb(255,255,255); color: rgb(0,0,0); font-size: 12px;font-weight: bold">
              <tr>
                <th style="background-color: black; font-weight: bold; padding: 10px; color:white ; font-size: 16px" colspan="13">Progress Approval</th>
              </tr>
              <tr>
                <th style="padding: 0;vertical-align: middle;font-size: 16px;background-color: black;color:white; border-right: 5px solid red !important;" rowspan="3">NIK-Name</th>
                <th style="padding: 0;vertical-align: middle;font-size: 16px;background-color: black;color:white; border-right: 5px solid red !important;" rowspan="3">Created By</th>
              </tr>
              <tr>
                <th style="background-color: black; font-size: 16px; font-weight: bold; padding: 2px; border-right: 5px solid red !important; color:white">Asal</th>
                <th style="background-color: black; font-size: 16px; font-weight: bold; padding: 2px; border-right: 5px solid red !important; color:white">Tujuan</th>
                <th style="background-color: black; font-size: 16px; font-weight: bold; padding: 2px; border-right: 5px solid red !important; color:white">Department</th>
                <th style="background-color: #448aff; font-size: 16px; font-weight: bold; padding: 2px;  color:white">HR</th>
                <th style="background-color: black; font-size: 16px; font-weight: bold; padding: 2px;  color:white; border-left: 5px solid red !important">Action</th>
              </tr>
              <tr>
                <th style="background-color: black; font-weight: bold; padding: 2px; color:white; border-right: 5px solid red !important;">Chief/Foreman</th>
                <!-- <th style="background-color: black; font-weight: bold; padding: 2px; color:white">Manager</th>
                <th style="background-color: black; font-weight: bold; padding: 2px; color:white">DGM</th>
                <th style="background-color: black; font-weight: bold; padding: 2px; border-right: 5px solid red !important; color:white">GM</th> -->
                <th style="background-color: black; font-weight: bold; padding: 2px; color:white; border-right: 5px solid red !important;">Chief/Foreman</th>
                <th style="background-color: black; font-weight: bold; padding: 2px; color:white; border-right: 5px solid red !important">Manager</th>
                <!-- <th style="background-color: black; font-weight: bold; padding: 2px; color:white;">DGM</th>
                <th style="background-color: black; font-weight: bold; padding: 2px; color:white; border-right: 5px solid red !important;">GM</th> -->
                <!-- <th style="background-color: black; font-weight: bold; padding: 2px; border-right: 5px solid red !important; color:white">GM</th> -->
                <th style="background-color: black; font-weight: bold; padding: 2px; color:white"></th>
                <th style="background-color: black; font-weight: bold; padding: 2px; color:white; border-left: 5px solid red !important"></th>
                <!-- <th style="background-color: black; font-weight: bold; padding: 2px; color:white">HR</th> -->
              </tr>
            </thead>
            <tbody id="tableResumeBody"></tbody>
            <tfoot>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
  <form id ="importForm" name="importForm" method="post" action="{{ url('create/mutasi') }}">
  <input type="hidden" value="{{csrf_token()}}" name="_token" />
  <div class="modal fade" id="modalCreate">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Satu Department</h4>
          <br>
          <div class="nav-tabs-custom tab-danger">
            <ul class="nav nav-tabs">
              <li class="vendor-tab active disabledTab"><a href="#tab_1" data-toggle="tab" id="tab_header_1">Data Karyawan</a></li>
              <li class="vendor-tab disabledTab"><a href="#tab_2" data-toggle="tab" id="tab_header_2">Masukkan Alasan</a></li>
            </ul>
          </div>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_1">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>NIK<span class="text-red">*</span></label>
                      <select class="form-control select2" id="employee_id" name="employee_id" data-placeholder='Pilih NIK Atau Nama' style="width: 100%" onchange="checkEmp(this.value)">
                          <option value="">&nbsp;</option>
                          @foreach($user as $row)
                          <option value="{{$row->employee_id}}">{{$row->employee_id}} - {{$row->name}}</option>
                          @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label id="label_section">Sub Group<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="sub_group" name="sub_group" readonly>
                    </div>
                    <div class="form-group">
                      <label id="label_group">Group<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="group" name="group" readonly>
                    </div>
                    <div class="form-group">
                      <label id="label_section">Seksi<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="section" name="section" readonly>
                    </div>
                    <div class="form-group">
                      <label id="labeldept">Department<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="department" name="department" readonly>
                    </div>
                    <div class="form-group">
                      <label id="labelposition">Jabatan<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="position" name="position" readonly>
                    </div>
                    <div class="form-group" hidden="hidden">
                      <label id="labelposition">Grade<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="grade" name="grade" readonly>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group" hidden="hidden">
                      <label id="labelnama">Nama<span class="text-red">*</span></label>
                      <input type="text" class="form-control pull-right" id="name" name="name" readonly>
                    </div>
                    <div class="form-group">
                      <label>Tanggal Mutasi<span class="text-red">*</span></label>
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <!-- <input type="text" class="form-control pull-right datepicker" value="<?= date('Y-m-d') ?>" placeholder="Date Mutation" disabled> -->
                        <input type="text" class="form-control pull-right" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" placeholder="Date Mutation">
                      </div>
                    </div>
                    <div class="form-group">
                      <label id="label_ke_sub_group">Ke Sub Group<span class="text-red">*</span></label>
                       <select class="form-control select2" id="ke_sub_group" name="ke_sub_group" data-placeholder='Pilih Sub Group' style="width: 100%" onchange="checkSubGroup(this.value)">
                          <option value="">&nbsp;</option>
                          <!-- <option value="Kosong">Kosong</option> -->
                          @foreach($sub_group as $row)
                          <option value="{{$row->sub_group}}">{{$row->sub_group}}</option>
                          @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label id="label_ke_roup">Ke Group<span class="text-red">*</span></label>
                      <!-- <input type="text" class="form-control" id="ke_group" name="ke_group"> -->
                      <select class="form-control select2" id="ke_group" name="ke_group" data-placeholder='Pilih Group' style="width: 100%" onchange="checkGroup(this.value)">
                          <option value="">&nbsp;</option>
                          <!-- <option value="Kosong">Kosong</option> -->
                          @foreach($group as $row)
                          <option value="{{$row->group}}">{{$row->group}}</option>
                          @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label id="label_ke_section">Ke Seksi<span class="text-red">*</span></label>
                      <select class="form-control select2" id="ke_section" name="ke_section" data-placeholder='Pilih Seksi' style="width: 100%" onchange="checkSection(this.value)">
                          <option value="">&nbsp;</option>
                          <!-- <option value="Kosong">Kosong</option> -->
                          @foreach($section as $row)
                          <option value="{{$row->section}}">{{$row->section}}</option>
                          @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label id="label_ke_dept">Ke Department<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="department1" name="department1" readonly>
                    </div>
                    <!-- <div class="form-group">
                      <label id="labelposition">Ke Jabatan<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="position1" name="position1" readonly>
                    </div> -->
                    <div class="form-group">
                      <label id="label_ke_section">Ke Jabatan<span class="text-red">*</span></label>
                      <select class="form-control select2" id="position1" name="position1" data-placeholder='Pilih Jabatan' style="width: 100%">
                          <!-- <option value="">&nbsp;</option> -->
                          <!-- <option value="Kosong">Kosong</option> -->
                          
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-11">
                  <a class="btn btn-primary btnNext pull-right">Lanjut</a>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab_2">
              <div class="row">
                <div class="col-md-12" style="margin-bottom : 5px">
                  <div class="form-group">
                      <label id="labelposition">Rekomendasi Atasan<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="rekom" name="rekom" value="MUTASI INTERN (DALAM SATU SEKSI)" readonly>
                  </div>
                  <div class="form-group">
                      <label id="labelposition">Alasan<span class="text-red">*</span></label>
                      <textarea class="form-control" id="alasan" name="alasan" rows="3" required></textarea>
                  </div>
                <div id="tambah"></div>
                <div class="col-md-12">
                  <br>
                  <button class="btn btn-success pull-right" onclick="$('[name=importForm]').submit();">Konfirmasi</button>
                  <span class="pull-right">&nbsp;</span>
                  <a class="btn btn-primary btnPrevious pull-right">Sebelum</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</form>

<input type="hidden" value="{{csrf_token()}}" name="_token" />
  <div class="modal fade" id="modalEdit">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title">Edit Satu Department</h4>
          <br>
          <div class="nav-tabs-custom tab-danger">
            <ul class="nav nav-tabs">
              <li class="vendor-tab active disabledTab"><a href="#tab_1_edit" data-toggle="tab" id="tab_header_1">Data Karyawan</a></li>
              <li class="vendor-tab disabledTab"><a href="#tab_2_edit" data-toggle="tab" id="tab_header_2">Masukkan Alasan</a></li>
            </ul>
          </div>
          <div class="tab-content">
            <div class="tab-pane active" id="tab_1_edit">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-6">
                    <div class="form-group" hidden="hidden">
                      <input type="text" class="form-control pull-right" id="id" name="id" readonly>
                    </div>
                    <div class="form-group">
                      <label>NIK<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="employee_id_edit" name="employee_id_edit" readonly>
                    </div>
                    <div class="form-group">
                      <label id="label_section">Sub Group<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="sub_group_edit" name="sub_group_edit" readonly>
                    </div>
                    <div class="form-group">
                      <label id="label_group">Group<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="group_edit" name="group_edit" readonly>
                    </div>
                    <div class="form-group">
                      <label id="label_section">Seksi<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="section_edit" name="section_edit" readonly>
                    </div>
                    <div class="form-group">
                      <label id="labeldept">Department<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="department_edit" name="department_edit" readonly>
                    </div>
                    <div class="form-group">
                      <label id="labelposition">Jabatan<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="position_edit" name="position_edit" readonly>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group" hidden="hidden">
                      <label id="labelnama">Nama<span class="text-red">*</span></label>
                      <input type="text" class="form-control pull-right" id="name_edit" name="name_edit" readonly>
                    </div>
                    <div class="form-group">
                      <label>Tanggal Mutasi<span class="text-red">*</span></label>
                      <div class="input-group date">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <!-- <input type="text" class="form-control pull-right datepicker" value="<?= date('Y-m-d') ?>" placeholder="Date Mutation" disabled> -->
                        <input type="text" class="form-control pull-right" id="tanggal_edit" name="tanggal_edit" value="<?= date('Y-m-d') ?>" placeholder="Date Mutation">
                      </div>
                    </div>
                    <div class="form-group">
                      <label id="label_ke_sub_group">Ke Sub Group<span class="text-red">*</span></label>
                       <select class="form-control select2" id="ke_sub_group_edit" name="ke_sub_group_edit" data-placeholder='Pilih Sub Group' style="width: 100%" onchange="checkSubGroup(this.value)">
                          <option value="">&nbsp;</option>
                          <!-- <option value="Kosong">Kosong</option> -->
                          @foreach($sub_group as $row)
                          <option value="{{$row->sub_group}}">{{$row->sub_group}}</option>
                          @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label id="label_ke_roup">Ke Group<span class="text-red">*</span></label>
                      <!-- <input type="text" class="form-control" id="ke_group" name="ke_group"> -->
                      <select class="form-control select2" id="ke_group_edit" name="ke_group_edit" data-placeholder='Pilih Group' style="width: 100%" onchange="checkGroup(this.value)">
                          <option value="">&nbsp;</option>
                          <!-- <option value="Kosong">Kosong</option> -->
                          @foreach($group as $row)
                          <option value="{{$row->group}}">{{$row->group}}</option>
                          @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label id="label_ke_section">Ke Seksi<span class="text-red">*</span></label>
                      <select class="form-control select2" id="ke_section_edit" name="ke_section_edit" data-placeholder='Pilih Seksi' style="width: 100%" onchange="checkSection(this.value)">
                          <option value="">&nbsp;</option>
                          <!-- <option value="Kosong">Kosong</option> -->
                          @foreach($section as $row)
                          <option value="{{$row->section}}">{{$row->section}}</option>
                          @endforeach
                      </select>
                    </div>
                    <div class="form-group">
                      <label id="label_ke_dept">Ke Department<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="department1_edit" name="department1_edit" readonly>
                    </div>
                    <div class="form-group">
                      <label id="labelposition">Ke Jabatan<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="position1_edit" name="position1_edit" readonly>
                    </div>
                  </div>
                </div>
                <div class="col-md-11">
                  <a class="btn btn-primary btnNextEdit pull-right">Lanjut</a>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab_2_edit">
              <div class="row">
                <div class="col-md-12" style="margin-bottom : 5px">
                  <div class="form-group">
                      <label id="labelposition">Rekomendasi Atasan<span class="text-red">*</span></label>
                      <input type="text" class="form-control" id="rekom" name="rekom" value="MUTASI INTERN (DALAM SATU SEKSI)" readonly>
                  </div>
                  <div class="form-group">
                      <label id="labelposition">Alasan<span class="text-red">*</span></label>
                      <textarea class="form-control" id="alasan_edit" name="alasan_edit" rows="3" required></textarea>
                  </div>
                <div id="tambah"></div>
                <div class="col-md-12">
                  <br>
                  <button class="btn btn-success pull-right" onclick="update()">Update</button>
                  <span class="pull-right">&nbsp;</span>
                  <a class="btn btn-primary btnPrevious pull-right">Sebelum</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="modalDetail">
    <div class="modal-dialog modal-lg" style="width: 80%">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="head_modal"></h4>
          <div class="modal-body table-responsive no-padding" style="min-height: 100px">
            <br><br>
            <table class="table table-hover table-bordered table-striped" id="tableDetail">
              <thead>
                <tr style="background-color: #a488aa; text-align: center;">
                  <td><b>NIK</b></td>
                  <td><b>Name</b></td>
                  <td><b>Sub Group</b></td>
                  <td><b>Group</b></td>
                  <td><b>Section</b></td>
                  <td><b>To Sub Group</b></td>
                  <td><b>To Group</b></td>
                  <td><b>To Section</b></td>
                  <td><b>Department</b></td>
                  <td><b>Position</b></td>
                  <td><b>Reason</b></td>
                  <td><b>Date Mutasi</b></td>
                </tr>
              </thead>
              <tbody id='bodyDetail'></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>
@endsection

@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");
    $('#labelnama').show();
    $('#name').show();
    $('#label_sub_group').show();
    $('#sub_group').show();
    $('#label_group').show();
    $('#group').show();
    $('#label_section').show();
    $('#section').show();
    $('#labeldept').show();
    $('#department').show();
    $('#labelposition').show();
    $('#position').show();
    $('#grade').show();

    $('#label_ke_sub_group').show();
    $('#ke_sub_group').show();
    $('#label_ke_group').show();
    $('#ke_group').show();
    $('#label_ke_section').show();
    $('#ke_section').show();
    $('#label_ke_dept').show();
    $('#ke_department').show();
    

    section = <?php echo json_encode($section); ?>;
    group = <?php echo json_encode($group); ?>;
    


    $('#myModal').on('hidden.bs.modal', function () {
      $('#example2').DataTable().clear();
    });


    $('.select2').select2({
      dropdownParent: $('#modalCreate'),
      allowClear : true
    });

    $('.hideselect').next(".select2-container").hide();

    drawChart();
    fillTable();
  });

  function getSection(elem){

    value = $(elem).val();

    // console.log(value);
    select = "";
    $("#ke_seksi").empty();

      $(section).each(function(index2, value2) {
        if (value == value2.department) {
          select += "<option value='"+value2.section+"'>"+value2.section+"</option>";
        }
      })

      $("#ke_seksi").append(select);
  }

  function getGroup(elem){
    value = $(elem).val();

    // console.log(value);
    select = "";
    $("#ke_sub_seksi").empty();

      $(group).each(function(index2, value2){
        if (value == value2.section){
          select += "<option value='"+value2.group+"'>"+value2.group+"</option>";
        }
      })
      $("ke_sub_seksi").append(select);
  }

  // var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  function checkEmp(value) {
        if (value.length == 9) {
            var data = {
                employee_id:$('#employee_id').val()
              }

              $.get('{{ url("dashboard/mutasi/get_employee") }}',data, function(result, status, xhr){
                  if(result.status){
                    $('#labelnama').show();
                    $('#name').show();
                    $('#labeldept').show();
                    $('#department').show();
                    $('#labelposition').show();
                    $('#position').show();
                    $('#group').show();
                    $('#section').show();
                    $('#sub_group').show();
                    $('#grade').show();

                    $.each(result.employee, function(key, value) {
                        $('#name').val(value.name);
                        $('#department').val(value.department);
                        $('#position').val(value.position);
                        $('#group').val(value.group);
                        $('#section').val(value.section);
                        $('#sub_group').val(value.sub_group);
                        $('#position1').val(value.position);
                        $('#department1').val(value.department);
                        $('#section1').val(value.section);
                        $('#grade').val(value.grade_code);
                    });

                    var data2  = {
                      jabatan:$('#grade').val()
                    }

                    $.get('{{ url("dashboard/mutasi/get_grade") }}',data2, function(result, status, xhr){
                        if(result.status){
                          $('#position1').html("");
                          var opbfsel = "";
                          opbfsel += '<option value="">Pilih Jabatan</option>';
                          $.each(result.position, function(key, value) {
                            opbfsel += '<option value="'+value.position+'">'+value.position+'</option>';
                          });
                          $('#position1').append(opbfsel);

                          // $('#grade').show();

                          // console.log(result.position);
                          

                        }
                    });
                  }
                  // else{
                  //   alert('NIK Tidak ditemukan');
                  // }
              });

              

        }else{
            $('#labelnama').show();
            $('#name').show();
            $('#labeldept').show();
            $('#department').show();
            $('#labelposition').show();
            $('#position').show();
            $('#group').show();
            $('#section').show();
            $('#sub_group').show();
            $('#grade').show();
        }
    }
 
    function checkSubGroup(value) {
          
          // if (value === 'Kosong') {
          //     $('#ke_group').val("").trigger('change');
          //     $('#ke_section').val("").trigger('change');
          //     // $('#ke_department').val("");
          // }else{
            var data = {
            ke_sub_group:$('#ke_sub_group').val(),
          }
          

          $.get('{{ url("dashboard/mutasi/get_tujuan") }}',data, function(result, status, xhr){
              if(result.status){
                $('#label_ke_sub_group').show();
                $('#ke_sub_group').show();
                $('#label_ke_group').show();
                $('#ke_group').show();
                $('#label_ke_section').show();
                $('#ke_section').show();
                $('#label_ke_dept').show();
                $('#ke_department').show();

                $.each(result.employee, function(key, value) {
                  $('#ke_group').val(value.group).trigger('change');
                  $('#ke_section').val(value.section).trigger('change');
                  $('#ke_department').val(value.department);


                  // if ($('#ke_sub_group').val() == value.sub_group) {
                      
                      // $('#ke_group').val(value.group).trigger('change');
                      // $('#ke_department').val(value.department);
                    // $('#ke_sub_group').val(value.sub_group);
                  // }
                  // if ($('#ke_group').val() != value.group) {
                  //   $('#ke_group').val(value.group).trigger('change');
                  // }

                  // if ($('#ke_section').val() != value.section) {
                  //   $('#ke_section').val(value.section).trigger('change');
                  // }
                });
              }
              // else{
              //   alert('Sub Group Tidak ditemukan');
              // }
          });
          }
    // }
    function checkGroup(value) {
          
          // if (value === 'Kosong') {
          //     $('#ke_section').val("").trigger('change');
          //      // $('#ke_department').val("");
          // }else{
              var data = {
            group:$('#ke_group').val(),
          }
           

          $.get('{{ url("dashboard/mutasi/get_group") }}',data, function(result, status, xhr){
              if(result.status){
                $('#label_ke_sub_group').show();
                $('#ke_sub_group').show();
                $('#label_ke_group').show();
                $('#ke_group').show();
                $('#label_ke_section').show();
                $('#ke_section').show();
                $('#label_ke_dept').show();
                $('#ke_department').show();

                $.each(result.employee, function(key, value) {
                  if ($('#ke_group').val() != value.group) {
                    $('#ke_department').val(value.department);
                    // $('#ke_group').val(value.group);
                    
                  }

                  if ($('#ke_section').val() != value.section) {
                    $('#ke_section').val(value.section).trigger('change');
                  }
                    
                    // $('#ke_sub_group').val(value.sub_group).trigger('change');
                });
              }
              // else{
              //   alert('Sub Group Tidak ditemukan');
              // }
          });
          }
    // }
    function checkSection(value) {
          var data = {
            section:$('#ke_section').val(),
          }
           

          $.get('{{ url("dashboard/mutasi/get_section") }}',data, function(result, status, xhr){
              if(result.status){
                $('#label_ke_sub_group').show();
                $('#ke_sub_group').show();
                $('#label_ke_group').show();
                $('#ke_group').show();
                $('#label_ke_section').show();
                $('#ke_section').show();
                $('#label_ke_dept').show();
                $('#ke_department').show();

                $.each(result.employee, function(key, value) {
                  if ($('#ke_section').val() != value.section) {
                    $('#ke_department').val(value.department);
                  }
                  // if ($('#ke_section').val() == ) {
                    
                    // $('#ke_group').val(value.group).trigger('change');
                    // $('#ke_section').val(value.section).trigger('change');
                  //   // $('#ke_sub_group').val(value.sub_group);
                  // }
                });
              }
              // else{
              //   alert('Sub Group Tidak ditemukan');
              // }
          });
    }

    



  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $('.datepicker').datepicker({
    autoclose: true,
    format: "yyyy-mm",
    todayHighlight: true,
    startView: "months", 
    minViewMode: "months",
    autoclose: true,
   });
  $('#tanggal').datepicker({
    format: "yyyy-mm-dd",
    daysOfWeekDisabled: "0",
    autoclose:true
    });
  $('#tanggal_edit').datepicker({
    format: "yyyy-mm-dd",
    daysOfWeekDisabled: "0",
    autoclose:true
    });
  $('.btnNext').click(function(){
      var employee_id = $('#employee_id').val();
      var department = $('#department').val();
      var section = $('#section').val();
      var position = $('#position').val();
      var name = $('#name').val();
      var tanggal = $('#tanggal').val();
      var ke_departemen = $('#ke_departemen').val();
      var ke_seksi = $('#ke_seksi').val();
      var ke_sub_seksi = $('#ke_sub_seksi').val();
      var ke_jabatan = $('#ke_jabatan').val();
      if(employee_id == '' || department == '' || section == '' ||  position == '' || name == '' || tanggal == ''|| ke_departemen == '' || ke_seksi == '' || ke_sub_seksi == '' || ke_jabatan == ''){
        alert('Semua Data Harus Diisi Dahulu.');  
      }
      else{
        $('.nav-tabs > .active').next('li').find('a').trigger('click');
        console.log('lanjut create');
      }
    });

    $('.btnNextEdit').click(function(){
      $('.nav-tabs > .active').next('li').find('a').trigger('click');
        // console.log('lanjut edit');
    });

    $('.btnPrevious').click(function(){
      $('.nav-tabs > .active').prev('li').find('a').trigger('click');
    });

  function openModalCreate(){
      $('#modalCreate').modal('show');
    }
  // function openModalEdit(){
  //   }

  function openModalDetail(id){
    var data = {id:id};
      $.get('<?php echo e(url("fetch/mutasi")); ?>', data, function(result, status, xhr){
        $("#modalDetail").modal("show");

        var body = '';
              $('#bodyDetail').empty();

        $.each(result.resumes, function(key, value) {
          body += '<tr>';
          body += '<td>'+ value.nik +'</td>';
          body += '<td>'+ value.nama +'</td>';
          body += '<td>'+ (value.sub_group || 'None') +'</td>';
          body += '<td>'+ (value.group || 'None')+'</td>';
          body += '<td>'+ (value.seksi || 'None')+'</td>';
          body += '<td>'+ (value.ke_sub_group || 'None') +'</td>';
          body += '<td>'+ (value.ke_group || 'None') +'</td>';
          body += '<td>'+ (value.ke_seksi || 'None') +'</td>';
          body += '<td>'+ value.departemen +'</td>';
          body += '<td>'+ value.jabatan +'</td>';
          body += '<td>'+ (value.alasan || 'None') +'</td>';
          body += '<td>'+ value.tanggal +'</td>';
          body += '</tr>';
      });

      $('#bodyDetail').append(body);
    });
  }

  function viewModalDetail(bulan, status, dateto, tahun){
    // var dateto = $('#dateto').val();

    // console.log(bulan);
    // console.log(status);
    // console.log(dateto);

    var data = {
      bulan: bulan,
      status: status,
      dateto: dateto,
      tahun: tahun
    };
      $.get('<?php echo e(url("view/mutasi")); ?>', data, function(result, status, xhr){
        $("#modalDetail").modal("show");
        $('#tableDetail').DataTable().clear();
        $('#tableDetail').DataTable().destroy();
        var body = '';
              $('#bodyDetail').empty();
              $('#bodyDetail').html("");

        var urlreport = '{{ url("mutasi/report") }}';

        // <a href="'+urlverifikasi+'/'+value.id+'" style="color:white">'+chief_tujuan+'</a>

        $.each(result.resumes, function(key, value) {
          body += '<tr>';
          if (value.status == "All Approved") {
                    body += '<td><span class="label label-success"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+ value.nik +'</a></span></td>';
              }
          else if (value.status == "Rejected") {
                     body += '<td><span class="label label-danger"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+ value.nik +'</a></span></td>';
              }
          else{
                  body += '<td>'+ value.nik +'</td>';
              }
          body += '<td>'+ value.nama +'</td>';
          body += '<td>'+ (value.sub_group || 'None') +'</td>';
          body += '<td>'+ (value.group || 'None')+'</td>';
          body += '<td>'+ (value.seksi || 'None')+'</td>';
          body += '<td>'+ (value.ke_sub_group || 'None') +'</td>';
          body += '<td>'+ (value.ke_group || 'None') +'</td>';
          body += '<td>'+ (value.ke_seksi || 'None') +'</td>';
          body += '<td>'+ value.departemen +'</td>';
          body += '<td>'+ value.jabatan +'</td>';
          body += '<td>'+ (value.alasan || 'None') +'</td>';
          body += '<td>'+ value.tanggal +'</td>';
          body += '</tr>';
      });

      $('#bodyDetail').append(body);

      var body = $('#tableDetail').DataTable({
          'dom': 'Bfrtip',
          'lengthMenu': [
          [ 5, 10, 25, 50, -1 ],
          [ '5 rows', '10 rows', '25 rows', '50 rows', 'Show all' ]
          ],
          'buttons': {
              buttons:[
              {
                  extend: 'pageLength',
                  className: 'btn btn-default',
              }
              ]
          },
          'paging': true,
          'lengthChange': true,
          'pageLength': 5,
          'searching'     : true,
          'ordering'      : true,
          'order': [],
          'info'          : true,
          'autoWidth'     : true,
          "sPaginationType": "full_numbers",
          "bJQueryUI": true,
          "bAutoWidth": false,
      });
    });
  }

   

  function drawChart() {
    
    fillTable();
    var dateto = $('#dateto').val();

    var data = {
      dateto: dateto
    };

    $.get('{{ url("fetch/mutasi/monitoring") }}', data, function(result, status, xhr) {
      if(xhr.status == 200){
        if(result.status){

          var bulan = [], jml = [], dept = [], jml_dept = [], not_sign = [], sign = [], proces = [];

          $.each(result.datas, function(key, value) {
            // bulan.push(value.bulan);
            bulan.push(value.bulan +' '+ value.tahun);
            // not_sign.push(parseInt(value.NotSigned));
            // sign.push(parseInt(value.Signed));
            // proces.push(parseInt(value.Proces));
            sign.push({y:parseInt(value.Signed), key: value.bulan, key2 : value.tahun});
            proces.push({y:parseInt(value.Proces), key: value.bulan, key2 : value.tahun});
          });
          // console.log(bulan);
          var date = new Date();
          
          $('#chart').highcharts({


            chart: {
            type: 'column'
            },
            title: {
                text: 'Monitoring Mutasi Satu Department'
            },
            subtitle: {
                // text: date.getFullYear()
                text: 'FY199'
            }, 
            xAxis: {
              type: 'category',
              categories: bulan
            },
            credits: {
                enabled: false
            },
            plotOptions: {
              series: {
                cursor: 'pointer',
                point: {
                  events: {
                    click: function () {
                      viewModalDetail(this.options.key, this.series.name, result.dateto, this.options.key2);
                    }
                  }
                },
                borderWidth: 0,
                dataLabels: {
                  enabled: true,
                  format: '{point.y}'
                }
              },
              column: {
                  color:  Highcharts.ColorString,
                  // stacking: 'normal',
                  borderRadius: 1,
                  dataLabels: {
                      enabled: true
                  }
              }
            },
            credits: {
              enabled: false
            },
            series: [{
                name: 'Proses',
                data: proces
            }, {
                name: 'Disetujui',
                data: sign
            }
            // , {
            //     name: 'Tidak Disetujui',
            //     data: not_sign
            // }
            ]
          })
        } else{
          alert('Attempt to retrieve data failed');
        }
      }
    })
  }

Highcharts.createElement('link', {
          href: '{{ url("fonts/UnicaOne.css")}}',
          rel: 'stylesheet',
          type: 'text/css'
        }, null, document.getElementsByTagName('head')[0]);

        Highcharts.theme = {
          colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
          '#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
          chart: {
            backgroundColor: {
              linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
              stops: [
              [0, '#2a2a2b']
              ]
            },
            style: {
              fontFamily: 'sans-serif'
            },
            plotBorderColor: '#606063'
          },
          title: {
            style: {
              color: '#E0E0E3',
              textTransform: 'uppercase',
              fontSize: '20px'
            }
          },
          subtitle: {
            style: {
              color: '#E0E0E3',
              textTransform: 'uppercase'
            }
          },
          xAxis: {
            gridLineColor: '#707073',
            labels: {
              style: {
                color: '#E0E0E3'
              }
            },
            lineColor: '#707073',
            minorGridLineColor: '#505053',
            tickColor: '#707073',
            title: {
              style: {
                color: '#A0A0A3'

              }
            }
          },
          yAxis: {
            gridLineColor: '#707073',
            labels: {
              style: {
                color: '#E0E0E3'
              }
            },
            lineColor: '#707073',
            minorGridLineColor: '#505053',
            tickColor: '#707073',
            tickWidth: 1,
            title: {
              style: {
                color: '#A0A0A3'
              }
            }
          },
          tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.85)',
            style: {
              color: '#F0F0F0'
            }
          },
          plotOptions: {
            series: {
              dataLabels: {
                color: 'white'
              },
              marker: {
                lineColor: '#333'
              }
            },
            boxplot: {
              fillColor: '#505053'
            },
            candlestick: {
              lineColor: 'white'
            },
            errorbar: {
              color: 'white'
            }
          },
          legend: {
            itemStyle: {
              color: '#E0E0E3'
            },
            itemHoverStyle: {
              color: '#FFF'
            },
            itemHiddenStyle: {
              color: '#606063'
            }
          },
          credits: {
            style: {
              color: '#666'
            }
          },
          labels: {
            style: {
              color: '#707073'
            }
          },

          drilldown: {
            activeAxisLabelStyle: {
              color: '#F0F0F3'
            },
            activeDataLabelStyle: {
              color: '#F0F0F3'
            }
          },

          navigation: {
            buttonOptions: {
              symbolStroke: '#DDDDDD',
              theme: {
                fill: '#505053'
              }
            }
          },

          rangeSelector: {
            buttonTheme: {
              fill: '#505053',
              stroke: '#000000',
              style: {
                color: '#CCC'
              },
              states: {
                hover: {
                  fill: '#707073',
                  stroke: '#000000',
                  style: {
                    color: 'white'
                  }
                },
                select: {
                  fill: '#000003',
                  stroke: '#000000',
                  style: {
                    color: 'white'
                  }
                }
              }
            },
            inputBoxBorderColor: '#505053',
            inputStyle: {
              backgroundColor: '#333',
              color: 'silver'
            },
            labelStyle: {
              color: 'silver'
            }
          },

          navigator: {
            handles: {
              backgroundColor: '#666',
              borderColor: '#AAA'
            },
            outlineColor: '#CCC',
            maskFill: 'rgba(255,255,255,0.1)',
            series: {
              color: '#7798BF',
              lineColor: '#A6C7ED'
            },
            xAxis: {
              gridLineColor: '#505053'
            }
          },

          scrollbar: {
            barBackgroundColor: '#808083',
            barBorderColor: '#808083',
            buttonArrowColor: '#CCC',
            buttonBackgroundColor: '#606063',
            buttonBorderColor: '#606063',
            rifleColor: '#FFF',
            trackBackgroundColor: '#404043',
            trackBorderColor: '#404043'
          },

          legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
          background2: '#505053',
          dataLabelsColor: '#B0B0B3',
          textColor: '#C0C0C0',
          contrastTextColor: '#F0F0F3',
          maskColor: 'rgba(255,255,255,0.3)'
        };
        Highcharts.setOptions(Highcharts.theme);
      

  function openSuccessGritter(title, message){
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-success',
      image: '{{ url("images/image-screen.png") }}',
      sticky: false,
      time: '3000'
    });
  }

  function openErrorGritter(title, message) {
    jQuery.gritter.add({
      title: title,
      text: message,
      class_name: 'growl-danger',
      image: '{{ url("images/image-stop.png") }}',
      sticky: false,
      time: '3000'
    });
  }

  function editForm(id) {
    
    $('#modalEdit').modal('show');

    var data = {
      id:id
    }

    $.get('{{ url("fetch/mutasi") }}',data, function(result, status, xhr){
      if(result.status){
        $.each(result.resumes, function(key, value) {
          $("#id").val(value.id);
          $("#employee_id_edit").val(value.nik+'-'+value.nama);
          $("#sub_group_edit").val(value.sub_group);
          $("#group_edit").val(value.group);
          $("#section_edit").val(value.seksi);
          $("#department_edit").val(value.departemen);
          $("#position_edit").val(value.jabatan);
          $("#tanggal_edit").val(value.tanggal);
          $("#ke_sub_group_edit").val(value.ke_sub_group).trigger('change.select2');
          $("#ke_group_edit").val(value.ke_group).trigger('change.select2');
          $("#ke_section_edit").val(value.ke_seksi).trigger('change.select2');
          $("#department1_edit").val(value.departemen);
          $("#position1_edit").val(value.jabatan);
          $("#alasan_edit").val(value.alasan);
        });
      }
      else{
        alert('No Data');
      }
    });
  }

  function update() {

    var id = $('#id').val();
    var tanggal = $('#tanggal_edit').val();
    var ke_sub_group = $('#ke_sub_group_edit').val();
    var ke_group = $('#ke_group_edit').val();
    var ke_seksi = $('#ke_section_edit').val();
    var alasan = $('#alasan_edit').val();
    var data = {
      id:id,
      tanggal:tanggal,
      ke_sub_group:ke_sub_group,
      ke_group:ke_group,
      ke_seksi:ke_seksi,
      alasan:alasan
    }

    // console.log(data);

    $.post('{{ url("edit/mutasi") }}',data, function(result, status, xhr){
        $('#modalEdit').modal('hide');
        drawChart();
        fillTable();
      // if(result.status){
      //   window.location.reload();
      //   openSuccessGritter('Success','Update Data Done');
      // }
      // else{
      //   audio_error.play();
      //   openErrorGritter('Error',result.message);
      // }
    });
  }

  function fillTable(){
    var dateto = $('#dateto').val();
    var data = {
      dateto: dateto
    };
        $.get('{{ url("fetch/mutasi/resume") }}', data, function(result, status, xhr){
          if(xhr.status == 200){
              if(result.status){
                  $('#tableResume').DataTable().clear();
                  $('#tableResume').DataTable().destroy();
                  
                  // $("#tableResumeBody").find("td").remove();
                  $('#tableResumeBody').html("");
                  
                  var bodyResume = "";
                  var chf_asal = "";
                  var mgr_asal = "";
                  var dgm_asal = "";
                  var gm_asal = "";

                  var chf_tujuan = "";
                  var mgr_tujuan = "";
                  var dgm_tujuan = "";
                  var gm_tujuan = "";
                  var mgr_hrga = "";
                  var dir = "";


              $.each(result.resumes, function(key, value) {

              var nama = value.nama;
              var nama2 = nama.split(' ').slice(0,2).join(' ');

              var name = value.name;
              var name2 = name.split(' ').slice(0,2).join(' ');              

              
            
            if (value.nama_chief_asal != null) {
              var nama_chief_asal = value.nama_chief_asal;
              var chief_asal = nama_chief_asal.split(' ').slice(0,2).join(' ');              
            }
            if (value.nama_manager_asal != null) {
              var nama_manager_asal = value.nama_manager_asal;
              var manager_asal = nama_manager_asal.split(' ').slice(0,2).join(' ');              
            }
            if (value.nama_chief_tujuan != null) {
              var nama_chief_tujuan = value.nama_chief_tujuan;
              var chief_tujuan = nama_chief_tujuan.split(' ').slice(0,2).join(' ');
            }

            if (value.nama_manager_tujuan != null) {
              var nama_manager_tujuan = value.nama_manager_tujuan;
              var manager_tujuan = nama_manager_tujuan.split(' ').slice(0,2).join(' ');
            }

            var urlreport = '{{ url("mutasi/report/") }}';
            var urlverifikasi = '{{ url("mutasi/verifikasi/") }}';
            var finish = '{{ url("mutasi/finish/")}}';
            var email = '{{ url("mutasi/email/")}}';
            var edit = '{{ url("mutasi/edit/")}}';

            bodyResume  += '<tr>';
            bodyResume  += '<td style="border-right: 5px solid red !important;"><a href="javascript:void(0)" id="'+value.id+'" onclick="openModalDetail('+value.id+')" style="color:black">'+value.nik+' - '+nama2+'</a></td>';
            if (value.remark == null) {
              bodyResume  += '<td style="border-right: 5px solid red !important; background-color:black"><span class="label label-warning">'+name2+'</span></td>';
            }else{
              bodyResume  += '<td style="border-right: 5px solid red !important; background-color:black"><span class="label label-success">'+name2+'</span></td>';
            }
            // bodyResume  += '<td style="border-right: 5px solid red !important; background-color:black"><span class="label label-warning">'+name2+'</span></td>';
            // jika chief asal
            if (value.remark != 2) {
              bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;">'+("")+'</td>';
            }
            else{
              if (value.nama_chief_asal != null) {
              //chief asal
                if (value.app_ca == "Approved") {
                  bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span class="label label-success"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+chief_asal+'</a></span></td>';
                }
                else if(value.status == 'Rejected') {
                 bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span class="label label-danger"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+chief_asal+'</a></span></td>';
                }
                else{
                    bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span class="label label-danger"><a href="'+urlverifikasi+'/'+value.id+'" style="color:white">'+chief_asal+'</a></span></td>';
                }
              }
              else if(chief_asal == null){
                  bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;">'+("")+'</td>';
              }
            };
            // jika chief tujuan
            if (value.nama_chief_tujuan != null) {
              //chief tujuan
              if (value.app_ct == "Approved") {
                    bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span class="label label-success"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+chief_tujuan+'</a></span></td>';           
              }
              else if(value.status == 'Rejected') {
               bodyResume  += '<td style="background-color:black"><span class="label label-danger"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+chief_tujuan+'</a></span></td>';
              }
              else{
                  bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span class="label label-danger"><a href="'+urlverifikasi+'/'+value.id+'" style="color:white">'+chief_tujuan+'</a></span></td>';
              }
            }
            else if(chief_tujuan == null){
                bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;">'+("")+'</td>';
            }
            else{
              bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span style="color:white">None</span></td>';
            }; 
            // jika manager tujuan
            if (value.remark != 2) {
              bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;">'+("")+'</td>';
            }else{
                if (value.nama_manager_tujuan != null) {
                //manager tujuan
                  if (value.app_mt == "Approved") {
                        bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span class="label label-success"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+manager_tujuan+'</a></span></td>';           
                  }
                  else if(value.status == 'Rejected') {
                   bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span class="label label-danger"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+manager_tujuan+'</a></span></td>';
                  }
                  else{
                      bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span class="label label-danger"><a href="'+urlverifikasi+'/'+value.id+'" style="color:white">'+manager_tujuan+'</a></span></td>';
                  }
              }
              else if(manager_tujuan == null){
                  bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;">'+("")+'</td>';
              }
              else{
                bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span style="color:white">None</span></td>';
              }; 
            }
            
            // jika dgm tujuan
            // if (value.nama_dgm_tujuan != null) {
              //dgm tujuan
            //   if (value.app_dt == "Approved") {
            //         bodyResume  += '<td style="background-color:black"><span class="label label-success"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+value.nama_dgm_tujuan+'</a></span></td>';
            //   }
            //   else if(value.status == 'Rejected') {
            //    bodyResume  += '<td style="background-color:black"><span class="label label-danger"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+value.nama_dgm_tujuan+'</a></span></td>';
            //   }
            //   else{
            //       bodyResume  += '<td style="background-color:black"><span class="label label-danger"><a href="'+urlverifikasi+'/'+value.id+'" style="color:white">'+value.nama_dgm_tujuan+'</a></span></td>';
            //   }
            // }
            // else if(value.nama_dgm_tujuan == null){
            //     bodyResume  += '<td style="background-color:black">'+("")+'</td>';
            // }
            // else{
            //   bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span style="color:white">None</span></td>';
            // }; 
            // jika gm tujuan
            // if (value.nama_gm_tujuan != null) {
              //gm tujuan
            //   if (value.app_gt == "Approved") {
            //         bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span class="label label-success"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+value.nama_gm_tujuan+'</a></span></td>';           
            //   }
            //   else if(value.status == 'Rejected') {
            //    bodyResume  += '<td style="background-color:black"><span class="label label-danger"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+value.nama_gm_tujuan+'</a></span></td>';
            //   }
            //   else{
            //       bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span class="label label-danger"><a href="'+urlverifikasi+'/'+value.id+'" style="color:white">'+value.nama_gm_tujuan+'</a></span></td>';
            //   }
            // }
            // else if(value.nama_gm_tujuan == null){
            //     bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;">'+("")+'</td>';
            // }
            // else{
            //   bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span style="color:white">None</span></td>';
            // }; 
            // jika manager hr
            if (value.nama_manager != null) {
              //manager hr
              // if (value.app_m == "Approved") {
              //       bodyResume  += '<td style="background-color:black"><span class="label label-success"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+value.nama_manager+'</a></span></td>';
              // }
              // else if(value.status == 'Rejected') {
              //  bodyResume  += '<td style="background-color:black"><span class="label label-danger"><a href="'+urlreport+'/'+value.id+'" style="color:white">'+value.nama_manager+'</a></span></td>';
              // }
              // else{
              //     bodyResume  += '<td style="background-color:black"><span class="label label-danger"><a href="'+urlverifikasi+'/'+value.id+'" style="color:white">'+value.nama_manager+'</a></span></td>';
              // }
              bodyResume  += '<td style="background-color:black"><span class="label label-success">'+value.nama_manager+'</span></td>';
            }
            else if(value.nama_manager == null){
              if (value.status == 'Rejected') {
                bodyResume  += '<td style="background-color:black"><span class="label label-danger">Rejected</span></td>';
              }
              else{
                bodyResume  += '<td style="background-color:black">'+("")+'</td>';
              }
            }
            // else if(value.status == 'Rejected' && value.nama_manager == null){
            //   bodyResume  += '<td style="background-color:black"><span class="label label-danger"><a href="'+urlverifikasi+'/'+value.id+'" style="color:white">Rejected</a></span></td>';
            
            else{
              bodyResume  += '<td style="background-color:black; border-right: 5px solid red !important;"><span style="color:white">None</span></td>';
            }; 
            // jika all approved hr akan
            // if ("{{ Auth::user()->role_code }}" == "HR" || "{{ Auth::user()->role_code }}" == "MIS") {
            //   if (value.status == "All Approved") {
            //         if (value.remark != null) {
            //         bodyResume  += '<td style="background-color:black"><span class="label label-warning">Finish</span></td>'; 
            //         }
            //         else{
            //         bodyResume  += '<td style="background-color:black"><span class="label label-danger"><a href="'+finish+'/'+value.id+'" style="color:white">Click To Finish</a></span></td>';
            //         }
            //       }
            //   else if(value.status == "Rejected"){
            //     bodyResume  += '<td style="background-color:black"><span class="label label-danger"><a href="'+urlreport+'/'+value.id+'" style="color:white">Rejected</a></span></td>';
            //   }
            //   else{
            //     bodyResume  += '<td style="background-color:black">'+("")+'</td>';
            //     }  
            //   }
            // else{
            //   if (value.status == 'Rejected') {
            //   bodyResume  += '<td style="background-color:black"><span class="label label-danger"><a href="'+urlreport+'/'+value.id+'" style="color:white">Rejected</a></span></td>';
            //   }
            //   else if(value.status == 'All Approved'){
            //   bodyResume  += '<td style="background-color:black"><span class="label label-success"><a href="'+urlreport+'/'+value.id+'" style="color:white">Approved</a></span></td>';
            //   }
            //   else{
            //   bodyResume  += '<td style="background-color:black">'+("")+'</td>';
            //   }
            // }
            if (value.remark != '2') {
            bodyResume  += '<td style="background-color:black; border-left: 5px solid red !important"><a class="btn btn-success btn-xs" href="'+email+'/'+value.id+'" style="color:white"><i class="fa fa-envelope"></i>Send Email</a><a class="btn btn-danger btn-xs" onclick="editForm(\''+value.id+'\');" style="color:white"><i class="fa fa-edit"></i>Edit</a></td>';  
            }
            else{
              bodyResume  += '<td style="background-color:black; border-left: 5px solid red !important"><a class="btn btn-warning btn-xs" href="'+urlreport+'/'+value.id+'" style="color:white"><i class="fa fa-edit"></i>Report</a></td>';
            }
            bodyResume  += '</tr>';
          })


          $('#tableResumeBody').append(bodyResume);

          var table = $('#tableResume').DataTable({
              'dom': 'Bfrtip',
              'lengthMenu': [
              [ 5, 10, 25, 50, -1 ],
              [ '5 rows', '10 rows', '25 rows', '50 rows', 'Show all' ]
              ],
              'buttons': {
                  buttons:[
                  {
                      extend: 'pageLength',
                      className: 'btn btn-default',
                  }
                  ]
              },
              'paging': true,
              'lengthChange': true,
              'pageLength': 10,
              'searching'     : true,
              'ordering'      : true,
              'order': [],
              'info'          : true,
              'autoWidth'     : true,
              "sPaginationType": "full_numbers",
              "bJQueryUI": true,
              "bAutoWidth": false,
          }); 
        }
      }
    })  
    }
</script>
@stop