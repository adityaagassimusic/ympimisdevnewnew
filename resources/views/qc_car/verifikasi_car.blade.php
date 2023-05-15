@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
  body{
    font-size: 16px;
  }
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    text-align:left;
    background-color: #9c27b0;
    color: white;
  }
  input[type=checkbox] {
    transform: scale(1.5);
  }
  tbody>tr>td{
    text-align:left;
  }
  tfoot>tr>th{
    text-align:left;
  }
  td:hover {
    overflow: visible;
  }
  table.table-hover > tbody > tr > td{
    border:1px solid #eeeeee;
  }
  table.table-bordered{
    border:1px solid black;
  }
  table.table-bordered > thead > tr > th{
    border:1px solid black;
  }
  table.table-bordered > tbody > tr > td{
    border:1px solid rgb(211,211,211);
    padding-top: 0;
    padding-bottom: 0;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid rgb(211,211,211);
  }
  .isi{
    background-color: #f5f5f5;
    color: black;
    padding: 10px;
  }
  #verifikasi > tbody > tr >  td {
    text-align: center
  }
  #loading, #error { display: none; }
</style>
@endsection
@section('header')
<section class="content-header">
  <h1>
    Verifikasi {{ $page }}
    <small>Verifikasi CAR</small>
  </h1>
  <ol class="breadcrumb">
   {{--  <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
   <li><a href="#">Examples</a></li>
   <li class="active">Blank page</li> --}}
 </ol>
</section>

@endsection
@section('content')
<section class="content">
  @if (session('status'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
    {{ session('status') }}
  </div>   
  @endif
  @if ($errors->has('password'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Alert!</h4>
    {{ $errors->first() }}
  </div>   
  @endif
  @if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-ban"></i> Error!</h4>
    {{ session('error') }}
  </div>   
  @endif
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-primary">
      <div class="box-body">

        <?php $user = STRTOUPPER(Auth::user()->username)?>
        
        <?php foreach ($cars as $cars): ?>

        <!-- <br> To : {{ $cars->posisi }} -->

        <a data-toggle="modal" data-target="#statusmodal{{$cars->id}}" class="btn btn-primary btn-sm pull-right">Cek Status Verifikasi</a>
        
        <a href="{{url('index/qc_report/print_cpar', $cars['id_cpar'])}}" data-toggle="tooltip" class="btn btn-warning btn-sm pull-right" style="margin-right: 5px;" title="Lihat Report CPAR"  target="_blank">Preview CPAR Report</a>

        <a href="{{url('index/qc_car/print_car_new', $cars['id'])}}" data-toggle="tooltip" class="btn btn-warning btn-sm pull-right" style="margin-right: 5px;" title="Lihat Report CAR"  target="_blank">Preview CAR Report</a>        

        <!-- Email Chief -->

        @if($cars->email_status == "SentChief" && $cars->checked_chief == "Checked")
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Manager" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke Manager</a>

        @elseif($user == $cars->verifikatorchief && $cars->email_status == "SentManager")
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
        @endif

        <!-- Email Foreman -->

        @if($cars->email_status == "SentForeman2" && $cars->checked_foreman == "Checked")
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Manager" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke Manager</a>

        @elseif($user == $cars->verifikatorforeman && $cars->email_status == "SentManager") <!-- Jika yang login Foreman dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
        @endif

        <!-- Email Coordinator -->

        @if($cars->email_status == "SentCoordinator" && $cars->checked_coordinator == "Checked")
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke Manager" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke Manager</a>

        @elseif($user == $cars->verifikatorcoordinator && $cars->email_status == "SentManager") <!-- Jika yang login Coordinator dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>
        @endif

        <!-- Email Manager -->        

        @if($cars->email_status == "SentManager" && $cars->checked_manager == "Checked") <!-- Manager -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke DGM" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke DGM</a>

        @elseif($user == $cars->manager && $cars->email_status == "SentDGM") <!-- Jika yang login Manager dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>

        @endif

        <!-- Email DGM -->

        @if($cars->email_status == "SentDGM" && $cars->approved_dgm == "Checked") <!-- DGM -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke GM" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke GM</a>

        @elseif($user == $cars->dgm && $cars->email_status == "SentGM") <!-- Jika yang login DGM dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim</label>

        @endif

        <!-- Email GM --> 

        @if($cars->email_status == "SentGM" && $cars->approved_gm == "Checked") <!-- GM -->
          <a class="btn btn-sm btn-primary pull-right" data-toggle="tooltip" title="Send Email Ke QA" onclick="sendemail({{ $cars->id }})" style="margin-right: 5px">Send Email Ke QA</a>

          <!-- <a href="{{url('index/qc_car/sendemail/'.$cars['id'].'/'.$cars['posisi'])}}" class="btn btn-sm ">Email </a> -->

        @elseif($user == $cars->gm && $cars->email_status == "SentQA") <!-- Jika yang login GM dan status-->
          <label class="label label-success pull-right" style="margin-right: 5px; margin-top: 8px">Email Sudah Terkirim Ke QA</label>

        @endif

        <br/><br/>

        @if($user == $cars->verifikatorchief || $user == $cars->verifikatorforeman || $user == $cars->verifikatorcoordinator || $user == $cars->employee_id || $user == $cars->dgm || $user == $cars->gm || Auth::user()->role_code == "S" || Auth::user()->role_code == "MIS" || Auth::user()->role_code == "QA" || Auth::user()->role_code == "QA-SPL" || $user == $cars->car_cpar->staff || $user == $cars->car_cpar->leader || $user == "PI9707010") 

        <table class="table table-hover">
          <form role="form" method="post" action="{{url('index/qc_car/checked/'.$cars->id)}}">
          	 <thead>
              <tr>
                <th colspan="6" style="background-color: #ef6c00; color: white; font-size: 24px;border: none;text-align: center;width: 100%"><b>VERIFIKASI CAR</b></th>
              </tr>
              <tr>
                <th colspan="1" style="width: 10%;border: none;text-align: center;font-size: 18px"><b>Point</b></th>
                <th colspan="4" style="width: 70%;border: none;text-align: center;font-size: 18px"><b>Content</b></th>
                <th colspan="1" style="width: 20%;border: none;text-align: center;font-size: 18px"><b>Checked</b></th>
              </tr>
            </thead>
            <tbody>
              <input type="hidden" value="{{csrf_token()}}" name="_token" />  
                <tr>
                  <td colspan="1" style="font-size: 20px;font-weight: bold;text-align: center">Deskripsi</td>
                  <td colspan="4"><?= $cars->deskripsi ?></td>
                  <td colspan="1">
                      @if($user == $cars->verifikatorchief || $user == $cars->verifikatorforeman || $user == $cars->verifikatorcoordinator || $user == "PI9707010")
                        @if($cars->posisi == "chief" || $cars->posisi == "foreman2" || $cars->posisi == "coordinator")
                          @if($cars->checked_chief == NULL || $cars->checked_coordinator == NULL || $cars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif($user == $cars->employee_id && $user != "PI0109004")
                        @if ($cars->posisi == "manager")
                          @if($cars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif($user == $cars->dgm)
                        @if ($cars->posisi == "dgm")
                          @if($cars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Diverifikasi</span>
                        @endif

                      @elseif($user == $cars->gm)
                        @if ($cars->posisi == "gm")
                          @if($cars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Departemen QA</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="1" style="font-size: 20px;font-weight: bold;text-align: center">Tindakan</td>
                  <td colspan="4"><?= $cars->tindakan ?></td>
                  <td colspan="1">
                  	@if($user == $cars->verifikatorchief || $user == $cars->verifikatorforeman || $user == $cars->verifikatorcoordinator || $user == "PI9707010")
                        @if($cars->posisi == "chief" || $cars->posisi == "foreman2" || $cars->posisi == "coordinator")
                          @if($cars->checked_chief == NULL || $cars->checked_coordinator == NULL || $cars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif($user == $cars->employee_id && $user != "PI0109004")
                        @if ($cars->posisi == "manager")
                          @if($cars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif($user == $cars->dgm)
                        @if ($cars->posisi == "dgm")
                          @if($cars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Diverifikasi</span>
                        @endif

                      @elseif($user == $cars->gm)
                        @if ($cars->posisi == "gm")
                          @if($cars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center" >
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Departemen QA</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="1" style="font-size: 20px;font-weight: bold;text-align: center">Penyebab</td>
                  <td colspan="4"><?= $cars->penyebab ?></td>
                  <td colspan="1">
                  	@if($user == $cars->verifikatorchief || $user == $cars->verifikatorforeman || $user == $cars->verifikatorcoordinator || $user == "PI9707010")
                        @if($cars->posisi == "chief" || $cars->posisi == "foreman2" || $cars->posisi == "coordinator")
                          @if($cars->checked_chief == NULL || $cars->checked_coordinator == NULL || $cars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif($user == $cars->employee_id && $user != "PI0109004")
                        @if ($cars->posisi == "manager")
                          @if($cars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif($user == $cars->dgm)
                        @if ($cars->posisi == "dgm")
                          @if($cars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Diverifikasi</span>
                        @endif

                      @elseif($user == $cars->gm)
                        @if ($cars->posisi == "gm")
                          @if($cars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Departemen QA</span>
                        @endif
                      @endif
                  </td>
                </tr>
                <tr>
                  <td colspan="1" style="font-size: 20px;font-weight: bold;text-align: center;">Perbaikan</td>
                  <td colspan="4"><?= $cars->perbaikan ?></td>
                  <td colspan="1">
                  	@if($user == $cars->verifikatorchief || $user == $cars->verifikatorforeman || $user == $cars->verifikatorcoordinator || $user == "PI9707010")
                        @if($cars->posisi == "chief" || $cars->posisi == "foreman2" || $cars->posisi == "coordinator")
                          @if($cars->checked_chief == NULL || $cars->checked_coordinator == NULL || $cars->checked_foreman == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Manager</span>
                        @endif

                      @elseif($user == $cars->employee_id && $user != "PI0109004")
                        @if ($cars->posisi == "manager")
                          @if($cars->checked_manager == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke DGM</span>
                        @endif

                      @elseif($user == $cars->dgm)
                        @if ($cars->posisi == "dgm")
                          @if($cars->approved_dgm == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Diverifikasi</span>
                        @endif

                      @elseif($user == $cars->gm)
                        @if ($cars->posisi == "gm")
                          @if($cars->approved_gm == NULL)
                            <div class="custom-control custom-checkbox" style="text-align: center">
                            <input type="checkbox" class="custom-control-input" id="customCheck" name="checked[]" value="">
                            </div>
                          @else
                            <span class="label label-success">Sudah Diverifikasi</span>
                          @endif
                        @elseif($cars->posisi == "staff" || $cars->posisi == "foreman")
                          <span class="label label-danger">Sudah Dikirim Ke Staff / Foreman</span>
                        @else
                          <span class="label label-danger">Sudah Dikirim Ke Departemen QA</span>
                        @endif
                      @endif
                  </td>
                </tr>
            </tbody>
        </table>

        <br>        

        <?php if ($cars->file != null){ ?>

        <div class="box box-warning box-solid">
          <div class="box-header with-border">
            <h3 class="box-title">File Terlampir</h3>

            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
            </div>
            <!-- /.box-tools -->
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <?php $data = json_decode($cars->file);
              for ($i = 0; $i < count($data); $i++) { ?>
                <div class="col-md-12">
                  <div class="col-md-4">
                    <div class="isi">
                      <?= $data[$i] ?>
                    </div>
                  </div>
                  <div  class="col-md-2">
                      <a href="{{ url('/files/car/'.$data[$i]) }}" target="_blank" class="btn btn-primary">Download / Preview</a>
                  </div>
                </div>
            <?php } ?>                       
          </div>
        </div> 
          
        <?php } ?>
        
        <div class="col-sm-12">
          
          @if(($cars->posisi == "chief" && $cars->checked_chief == null) || ($cars->posisi == "foreman2" && $cars->checked_foreman == null) || ($cars->posisi == "coordinator" && $cars->checked_coordinator == null) || ($cars->posisi == "manager" && $cars->checked_manager == null) || ($cars->posisi == "dgm" && $cars->approved_dgm == null) || ($cars->posisi == "gm" && $cars->approved_gm == null))
          <button type="submit" class="btn btn-success col-sm-14" style="width: 100%; font-weight: bold; font-size: 20px">Verifikasi</button>
           <a data-toggle="modal" data-target="#notapproved{{$cars->id}}" class="btn btn-danger col-sm-12" href="" style="width: 100%; font-weight: bold; font-size: 20px;margin-top: 10px">Reject CAR</a>
          @endif

        </div>

        
        @endif
        <?php endforeach ?>

      </div>
    </form>
  </div>

  <div class="modal modal-danger fade" id="notapproved{{$cars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form role="form" method="post" action="{{url('index/qc_car/unchecked/'.$cars->id)}}">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title" id="myModalLabel">Not Approved</h4>
            </div>
            <div class="modal-body">
              <div class="box-body">
                  <input type="hidden" value="{{csrf_token()}}" name="_token" />
                  <h4>Berikan alasan tidak menyetujui CAR ini</h4>
                  <textarea class="form-control" required="" name="alasan"></textarea>
                  *CAR Akan Dikirim kembali lagi ke Foreman / Staff masing - masing departemen
              </div>    
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-outline">Not Approved</a>
            </div>
          </form>
        </div>
      </div>
    </div>

  <div class="modal fade" id="statusmodal{{$cars->id}}" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Status CAR Sekarang</h4>
      </div>
      <div class="modal-body">
        <div class="box-body">
          <table class="table table-hover" id="verifikasi">
            <tbody>
              <input type="hidden" value="{{csrf_token()}}" name="_token" />  
                <tr style="background-color: #4caf50;color: white">
                    <td colspan="2" style="width: 33%"><b>Position</b></td>
                    <td colspan="2" style="width: 33%"><b>Action</b></td>
                    <td colspan="2" style="width: 33%"><b>Email</b></td>
                </tr>
                <tr>
                    <td colspan="2"><b>
                      @if($cars->car_cpar->kategori == "Internal") 
                            Staff / Leader
                        @elseif($cars->car_cpar->kategori == "Eksternal" || $cars->car_cpar->kategori == "Supplier") 
                            Staff
                        @endif
                    </b></td>
                    @if(($cars->email_status == "SentStaff" && $cars->posisi == "staff") || ($cars->email_status == "SentForeman" && $cars->posisi == "foreman")) 
                    <td colspan="2"><b><span class="label label-success">On Progress</span></b></td>
                    <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @else
                    <td colspan="2"><b><span class="label label-warning">Verification</span></b></td>
                    <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @endif
                </tr>
                <tr>
                    <td colspan="2">
                      <b>
                        @if($cars->car_cpar->kategori == "Internal") 
                            Chief / Foreman
                        @elseif($cars->car_cpar->kategori == "Eksternal") 
                            Chief
                        @elseif($cars->car_cpar->kategori == "Supplier")
                            Coordinator
                        @endif
                      </b>
                    </td>
                    <td colspan="2"><b>
                      @if($cars->checked_chief == "Checked" || $cars->checked_foreman == "Checked" || $cars->checked_coordinator == "Checked")
                      <span class="label label-success">Checked</span>
                      @else
                      <span class="label label-danger">Not Checked</span>
                      @endif</b>
                    </td>
                    @if(($cars->email_status == "SentManager" || $cars->email_status == "SentDGM" || $cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "manager" || $cars->posisi == "dgm" || $cars->posisi == "gm" || $cars->posisi == "qa"))
                    <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @else
                    <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @endif
                </tr>
                <tr>
                    <td colspan="2"><b>Manager</b></td>
                    <td colspan="2"><b>
                      @if($cars->checked_manager == "Checked")
                      <span class="label label-success">Checked</span>
                      @else
                      <span class="label label-danger">Not Checked</span>
                      @endif</b>
                    </td>
                    @if(($cars->email_status == "SentDGM" || $cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "dgm" || $cars->posisi == "gm" || $cars->posisi == "qa"))
                    <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @else
                    <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @endif
                </tr>
                <tr>
                    <td colspan="2"><b>DGM</b></td>
                    <td colspan="2"><b>
                      @if($cars->approved_dgm == "Checked")
                      <span class="label label-success">Checked</span>
                      @else
                      <span class="label label-danger">Not Checked</span>
                      @endif</b>
                    </td>
                    @if(($cars->email_status == "SentGM" || $cars->email_status == "SentQA") && ($cars->posisi == "gm" || $cars->posisi == "qa"))
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @else
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @endif
                </tr>
                <tr>
                    <td colspan="2"><b>GM</b></td>
                    <td colspan="2"><b>
                      @if($cars->approved_gm == "Checked")
                      <span class="label label-success">Checked</span>
                      @else
                      <span class="label label-danger">Not Checked</span>
                      @endif</b>
                    </td>
                    @if($cars->email_status == "SentQA" && $cars->posisi == "qa")
                      <td colspan="2"><b><span class="label label-success">Sent</span></b></td>
                    @else
                      <td colspan="2"><b><span class="label label-danger">Not Sent</span></b></td>
                    @endif
                </tr>
            </tbody>
        </table>
        </div>    
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

@endsection


@section('scripts')

<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script>
  $(document).ready(function() {

    $("body").on("click",".btn-danger",function(){ 
      $(this).parents(".control-group").remove();
    });
    $('body').toggleClass("sidebar-collapse");
    $("#navbar-collapse").text('');
  });

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  function sendemail(id) {
      var data = {
        id: id,
      };

      if (!confirm("Apakah anda yakin ingin mengirim CAR ini?")) {
        return false;
      }

      $.get('{{ url("index/qc_car/sendemail/$cars->id/$cars->posisi") }}', data, function(result, status, xhr){
        openSuccessGritter("Success","Email Has Been Sent");
        window.location.reload();
      })
    }

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


  </script>
  @stop