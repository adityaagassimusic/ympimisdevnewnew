@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<link rel="stylesheet" href="{{ url("plugins/timepicker/bootstrap-timepicker.min.css")}}">

<style type="text/css">
  thead input {
    width: 100%;
    padding: 3px;
    box-sizing: border-box;
  }
  thead>tr>th{
    text-align:center;
    overflow:hidden;
  }
  tbody>tr>td{
    text-align:center;
  }
  tfoot>tr>th{
    text-align:center;
  }
  th:hover {
    overflow: visible;
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
    border:1px solid black;
    vertical-align: middle;
    padding:2px;
  }
  table.table-bordered > tfoot > tr > th{
    border:1px solid black;
    padding:2px;
  }
  td{
    overflow:hidden;
    text-overflow: ellipsis;
  }

  /*.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
    background-color: #ffd8b7;
    }*/

  /*.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
    background-color: #FFD700;
    }*/
    #loading, #error { display: none; }

    .containers {
      display: block;
      position: relative;
      padding-left: 35px;
      margin-bottom: 12px;
      cursor: pointer;
      font-size: 15px;
      -webkit-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
      padding-top: 6px;
    }

    /* Hide the browser's default checkbox */
    .containers input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
      height: 0;
      width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
      position: absolute;
      top: 0;
      left: 0;
      height: 25px;
      width: 25px;
      background-color: #eee;
      margin-top: 4px;
    }

    /* On mouse-over, add a grey background color */
    .containers:hover input ~ .checkmark {
      background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .containers input:checked ~ .checkmark {
      background-color: #2196F3;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }

    /* Show the checkmark when checked */
    .containers input:checked ~ .checkmark:after {
      display: block;
    }

    /* Style the checkmark/indicator */
    .containers .checkmark:after {
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
  </style>
  @stop
  @section('header')
  <section class="content-header">
    <h1 class="pull-left" style="padding: 0px; margin: 0px;">Detail Information Pelaporan Kanagata Retak<span class="text-purple"> (割れた金型報告の詳細)</span></h1>
    <br>
  </section>
  @stop
  @section('content')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <section class="content">
    @if (session('status'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
      {{ session('status') }}
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <h4><i class="icon fa fa-ban"></i> Error!</h4>
      {{ session('error') }}
    </div>   
    @endif
    <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
      <p style="position: absolute; color: White; top: 45%; left: 45%;">
        <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
      </p>
    </div>      
    <div class="row">
      <div class="col-xs-12" style="padding-right: 5px;">
        <div class="box box-solid">
          <div class="box-body">
            <div class="row">
              <div class="col-xs-12" align="center">
                <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
                  <div class="box-body">
                    <div class="col-xs-6" style="margin-top: 0px;padding-top: 10px;padding: 0px">
                      <div class="col-xs-12" style="padding-left: 5px;padding-right: 5px;vertical-align: middle;" >
                        <span style="font-size: 25px;color: black;width: 25%;">Kanagata Information <span class="text-purple"></span></span>
                      </div>
                      <input type="hidden" class="form-control" id="request_ids" value="{{ $kanagata->request_id }}" autocomplete="off">
                      <table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
                        <tr>
                          <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: rgb(126,86,134); text-align: center">Keterangan</th>
                          <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #fc8568;text-align: center">#</th>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Incident Date <span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->tanggal_kejadian }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Problem Decription<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->problem_desc }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Type Process<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->process_type }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">GMC Material<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->gmc_material }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Description Material<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->desc_material }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Description Product<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->part_name }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Type Die<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->type_die }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">No. Die<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->no_die }}</td>
                        </tr>

                        <?php if ($kanagata->process_type == "Forging"): ?>
                          <tr align="center">
                            <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Molding Production Date<span class="text-purple"></span></td>
                            <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->making_date }}</td>
                          </tr>
                        <?php endif ?>


                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Total Shoot<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->total_shoot }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Target Total Shoot<span class="text-purple"></span></td>
                          <?php if ($kanagata->lifetime == 0): ?>

                            <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">-</td>
                          <?php endif ?>
                          <?php if ($kanagata->lifetime != 0): ?>

                            <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->lifetime }}</td>
                          <?php endif ?>

                        </tr>

                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Status Total Shoot<span class="text-purple"></span></td>
                          <?php if ($kanagata->status_shoot == ''): ?>
                            <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color: #fcd068; color: black;">-</td>
                          <?php endif ?>
                          <?php if ($kanagata->status_shoot != ''): ?>
                            <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  rgb(243, 156, 18); color: black;">{{ $kanagata->status_shoot }}</td>
                          <?php endif ?>
                        </tr>


                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Spare Die<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->spare_die }}</td>
                        </tr>
                        <?php if ($kanagata->process_type == "Forging"): ?>

                          <tr align="center">
                            <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Forging Ke<span class="text-purple"></span></td>
                            <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->forging_ke }}</td>
                          </tr>
                        <?php endif ?>

                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Die High<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->die_high }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Limit Preasure / Peak<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{$kanagata->limit_preasure}} / {{$kanagata->peak}}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Cavity<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->cavity }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Retak Ke<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->retak_ke }}</td>
                        </tr>
                        <tr align="center" >
                          <td colspan="2" style="border-right: hidden; border-left: hidden; border-bottom: 1px solid black;font-size: 25px;color: black;width: 25%; color: black; padding-bottom: 10px">Repair Information<span class="text-purple"></span></td>
                        </tr>
                        <tr>
                          <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: rgb(126,86,134); text-align: center">Keterangan</th>
                          <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #fc8568;text-align: center">#</th>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">NG Pada Area Proses Sanding Normal<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->ng_sanding }}</td>
                        </tr>
                         <?php if ($kanagata->ng_sanding == "Tidak"): ?>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Bisa Repair<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->repair }}</td>
                        </tr>
                        <tr align="center">
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Waktu Repair<span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $kanagata->waktu_repair }} (detik)</td>
                        </tr>
                        <?php endif ?>

                      </tr>

                      <tr align="center" >

                        <td colspan="2" style="border-right: hidden; border-left: hidden; border-bottom: 1px solid black;font-size: 25px;color: black;width: 25%; color: black; padding-bottom: 10px">Comment<span class="text-purple"></span></td>
                      </tr>
                      <tr>
                        <th style="border: 1px solid black;  border-bottom: 1px solid black;font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: rgb(126,86,134);text-align: center">User Approval</th>
                        <th style="border: 1px solid black;  border-bottom: 1px solid black;font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #fc8568;text-align: center">#</th>
                      </tr>
                      
                      <tbody id="tabelisi">
                      </tbody>
                      


                    </table>
                  </div>
                  <div class="col-xs-6" align="center">
                    <div class="col-xs-12" style="padding-left: 5px;padding-right: 5px;vertical-align: middle;" >
                      <span style="font-size: 25px;color: black;width: 25%;">Photo Defect Kanagata <span class="text-purple"></span></span>
                    </div>
                    <input type="hidden" class="form-control" id="request_ids" value="{{ $kanagata->request_id }}" autocomplete="off">
                    <table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
                      <tr>
                        <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: rgb(126,86,134); text-align: center">Photo Kanagata</th>
                        <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #fc8568;text-align: center">Detail</th>
                      </tr>
                      <tr align="center">
                        <td style="border:1px solid black; font-size: 17px; font-weight: normal; background-color:  #e8daef; color: black;"><img src="{{url('images/pelaporan_kanagata/'.$kanagata->foto_kanagata)}}" style="width: 280px; cursor: pointer;" onclick="showImage('{{ $kanagata->foto_kanagata }}')">
                          <span class="text-purple"></span></td>
                          <td style="border:1px solid black; font-size: 17px; font-weight: bold; background-color:  #fcd068; color: black;"><img src="{{url('images/pelaporan_kanagata/'.$kanagata->detail_foto_kanagata)}}" style="width: 280px;cursor: pointer;" onclick="showImage('{{ $kanagata->detail_foto_kanagata }}')">
                          </td>
                        </tr>
                        <?php if ($kanagata->foto_defect_material != null || $kanagata->detail_foto_defect_material != null): ?>
                          <tr align="center">
                            <td colspan="2" style="border-right: hidden; border-left: hidden; border-bottom: 1px solid black;font-size: 25px;color: black;width: 25%; color: black; padding-bottom: 10px">Photo Defect Material<span class="text-purple"></span></td>

                          </tr>

                          <tr>
                            <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: rgb(126,86,134); text-align: center">Photo Material</th>
                            <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #fc8568;text-align: center">Detail</th>
                          </tr>
                          <tr align="center">
                            <?php if ($kanagata->foto_defect_material != null || $kanagata->detail_foto_defect_material != null): ?>

                              <td style="border:1px solid black !important; font-size: 17px; font-weight: normal; background-color:  #e8daef; color: black;"><img src="{{url('images/pelaporan_kanagata/'.$kanagata->foto_defect_material)}}" style="width: 280px; cursor: pointer;" onclick="showImage('{{ $kanagata->foto_defect_material }}')"></td>
                              <td style="border:1px solid black; font-size: 17px; font-weight: bold; color: black;"><img src="{{url('images/pelaporan_kanagata/'.$kanagata->detail_foto_defect_material)}}" style="width: 280px; cursor: pointer;" onclick="showImage('{{ $kanagata->detail_foto_defect_material }}')"></td>
                            <?php endif ?>

                          </tr>
                        <?php endif ?>


                        <?php if ($kanagata->condition_material_repair != null): ?>


                         <tr align="center">
                          <td colspan="2" style="border-right: hidden; border-left: hidden; border-bottom: 1px solid black;font-size: 25px;color: black;width: 25%; color: black; padding-bottom: 10px">Condition Material Repaired<span class="text-purple"></span></td>
                        </tr>
                        <tr>
                          <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: rgb(126,86,134); text-align: center">Material After Repair</th>
                          <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #fc8568;text-align: center">Detail</th>
                        </tr>

                        <tr align="center">
                          <!-- <td style="border:1px solid black !important; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;"><img src='../images/pelaporan_kanagata/condition_materiall_PK0378.png' width='18%'></td> -->
                          <?php if ($kanagata->detail_condition_material_repair != null): ?>

                            <td style="border:1px solid black; font-size: 17px; font-weight: bold; color: black;">
                              <img src="{{url('images/pelaporan_kanagata/'.$kanagata->condition_material_repair)}}" style="width: 280px; cursor: pointer;" onclick="showImage('{{ $kanagata->condition_material_repair }}')">
                            </td>
                            <td style="border:1px solid black; font-size: 17px; font-weight: bold; color: black;">
                              <img src="{{url('images/pelaporan_kanagata/'.$kanagata->detail_condition_material_repair)}}" style="width: 280px; cursor: pointer;" onclick="showImage('{{ $kanagata->detail_condition_material_repair }}')">
                            </td>
                          <?php endif ?>

                        </tr>
                      <?php endif ?>

                    </table>
                  </div>


                  <div class="col-md-12" style="padding-top: 20px; padding-bottom: 20px;">
                    <table id="tableLeave" class="table table-bordered table-striped table-hover" style="margin-bottom: 0;font-size: 0.9vw;">
                      <thead style="background-color: rgb(126,86,134); color: #fff;">
                        <tr>
                          <th width="1%">Request ID</th>
                          <th width="2%" style="background-color: #3064db">Applicant</th>
                          <th width="2%" style="background-color: #3064db">Staff Prod</th>
                          <th width="2%" style="background-color: #3064db">Staff PE</th>
                          <th width="2%" style="background-color: #3064db">Foreman</th>
                          <th width="2%" style="background-color: #3064db">Manager Prod</th>
                          <th width="2%" style="background-color: #3064db">Chief PE</th>
                          <th width="2%" style="background-color: #3064db">Manager PE</th>
                          <th width="3%" style="background-color: #3064db">Manager Japanese Speacialist PE</th>
                          <th width="1%" style="background-color: #f39c12">Descision</th>
                        </tr>
                      </thead>
                      <tbody id="bodyTableLeave">
                      </tbody>

                    </table>
                  </div>

                </div>
              </div>
            </div>


          </div>



        </div>
      </div>
    </div>

  </div>

  <div class="modal fade" id="modalImage">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <div class="form-group">
            <div  name="image_show" id="image_show"></div>

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
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  var audio_error = new Audio('{{ url("sounds/error.mp3") }}');
  var employees = [];
  var count = 0;
  var destinations = [];
  var countDestination = 0;

  jQuery(document).ready(function() {
    $('body').toggleClass("sidebar-collapse");

    fillList();

    // clearAll();

    $('.datepicker').datepicker({
      autoclose: true,
      format: "yyyy-mm-dd",
      todayHighlight: true,
    });

    $('.timepicker').timepicker({
      showInputs: false,
      showMeridian: false,
      defaultTime: '0:00',
    });
  });


  $(function () {
    $('.select2').select2({
      allowClear:true
    });
  });

  function clearAll() {
    $('#add_purpose_category').val('').trigger('change');
    $('#add_purpose_detail').val('').trigger('change');
    $('#add_detail').val('');
    $('#add_employees').val('').trigger('change');
    $('#add_destination').val('');
    $('#tableEmployeeBody').html('');

    $('#countTotal').html('0');
    employees = [];
    count = 0;
    destinations = [];
    countDestination = 0;

    $("input[name='add_return_or_not']").each(function (i) {
      $('#add_return_or_not')[i].checked = false;
    });

    $("input[name='add_driver']").each(function (i) {
      $('#add_driver')[i].checked = false;
    });
    $('#add_time_departure').val('0:00');
    $('#add_time_arrived').val('0:00');
    $('#add_time_arrived').show();
    $('#date').val(getActualFullDate());
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


  function showImage(id) {

    $('#modalImage').modal('show');

    var images_show = "";
    $("#image_show").html("");

    images_show += '<img style="cursor:zoom-in" src="{{ url("images/pelaporan_kanagata") }}/'+id+'" width="100%" >';

    $("#image_show").append(images_show);

  }

  var kata_confirm = 'Apakah Anda Yakin?';


  function fillList(){

    var request_id = $('#request_ids').val();

    var data = {
      request_id:request_id
    }

    $.get('{{ url("detail/approval/table") }}',data, function(result, status, xhr){
      if(result.status){


        $('#tabelisi').empty();
        var aktivitas = "";


        for (var i = 0; i < result.leave_approvals[0].length; i++) {
          if (result.leave_approvals[0][i].comment != null) {
            if (result.leave_approvals[0][i].status == "Rejected") {
              aktivitas += '<tr style="border-top:1px solid black; border-bottom: 1px solid black;">';
              aktivitas += '<td style="border-top:1px solid black; border-bottom: 1px solid black; font-weight: bold; background-color: #f39c12;color: black;font-size: 14px;" id="total_check_belum_pel">'+result.leave_approvals[0][i].approver_name.split(' ').slice(0,2).join(' ')+'</td>';
               
              if (result.leave_approvals[0][i].comment == "<span></span><br> " || result.leave_approvals[0][i].comment == null || result.leave_approvals[0][i].comment == "<span></span> ") {
              aktivitas += '<td style="border-bottom: 1px solid black; font-weight: bold; background-color: #f39c12;color: black;font-size: 14px;" id="total_check_belum_pel">-</td>';  
            }else{
              aktivitas += '<td style="border-bottom: 1px solid black; font-weight: bold; background-color: #f39c12;color: black;font-size: 14px;" id="total_check_belum_pel">'+result.leave_approvals[0][i].comment+'</td>';
            }
              aktivitas += '</tr>';
            }else{

              aktivitas += '<tr style="border-top:1px solid black; border-bottom: 1px solid black;">';
              aktivitas += '<td style="border-top:1px solid black; border-bottom: 1px solid black; font-weight: bold; background-color: #ffbd87;color: black;font-size: 14px;" id="total_check_belum_pel">'+result.leave_approvals[0][i].approver_name.split(' ').slice(0,2).join(' ')+'</td>';
              if (result.leave_approvals[0][i].comment == "<span></span><br> " || result.leave_approvals[0][i].comment == null) {
             aktivitas += '<td style="border-bottom: 1px solid black; font-weight: bold; background-color: #ffbd87;color: black;font-size: 14px;" id="total_check_belum_pel">-</td>';
            }else{
              aktivitas += '<td style="border-bottom: 1px solid black; font-weight: bold; background-color: #ffbd87;color: black;font-size: 14px;" id="total_check_belum_pel">'+result.leave_approvals[0][i].comment+'</td>';
            }
              aktivitas += '</tr>';
            }
          }

        }

        $('#tabelisi').append(aktivitas);

        $('#tableLeave').DataTable().clear();
        $('#tableLeave').DataTable().destroy();
        $('#bodyTableLeave').html("");
        var tableData = "";

        var tableDataComplete = "";
        $.each(result.leave_request, function(key, value) {
          tableData += '<tr>';
          tableData += '<td style="font-weight:bold;color:red">'+ value.request_id +'</td>';

          var last_approval = '';
          var approval_remark = [];
          var approval_remarks = [];
          for(var i = 0; i < result.leave_approvals.length;i++){
            if (result.leave_approvals[i][0].request_id == value.request_id) {
              for(var j = 0; j < result.leave_approvals[i].length;j++){
                approval_remark.push(result.leave_approvals[i][j].remark);
                approval_remarks.push({remark:result.leave_approvals[i][j].remark,keutamaan:result.leave_approvals[i][j].keutamaan});
              }
            }
          }
          if (approval_remark.indexOf("Applicant") != -1) {
            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Applicant') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Applicant') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Applicant') {
                      if (result.leave_approvals[i][j].keutamaan == 'belum') {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                      }else{
                        tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                      }
                    }
                  }
                }
              }
            }
          }else{
            tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
          }

          

          if (approval_remark.indexOf("Staff Prod") != -1) {
            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Staff Prod') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Staff Prod') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Staff Prod') {
                      if (value.remark == "Rejected") {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                      }else{
                        if ('' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'S-MIS' || '{{$role_code}}' == 'C-MIS') {
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            var url = "{{ url('kanagata/approval/') }}";
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;" >'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                          }
                        }else{
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }else{
            tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
          }

          if (approval_remark.indexOf("Staff PE") != -1) {
            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Staff PE') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Staff PE') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Staff PE') {
                      if (value.remark == "Rejected") {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                      }else{
                        if ('' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'S-MIS' || '{{$role_code}}' == 'C-MIS') {
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            var url = "{{ url('kanagata/approval/') }}";
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;" target="_blank" >'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                          }

                        }else{
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }else{
            tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
          }

          if (approval_remark.indexOf("Foreman") != -1) {
            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Foreman') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Foreman') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Foreman') {
                      if (value.remark == "Rejected") {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                      }else{
                        if ('' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'S-MIS' || '{{$role_code}}' == 'C-MIS') {
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            var url = "{{ url('kanagata/approval/') }}";
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;" >'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                          }
                        }else{
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }else{
            tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
          }

          if (approval_remark.indexOf("Manager Prod") != -1) {
            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Manager Prod') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Manager Prod') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Manager Prod') {
                      if (value.remark == "Rejected") {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                      }else{
                        if ('' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'S-MIS' || '{{$role_code}}' == 'C-MIS') {
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            var url = "{{ url('kanagata/approval/') }}";
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;" target="_blank" >'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                          }
                        }else{
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }else{
            tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
          }
          
          if (approval_remark.indexOf("Chief PE") != -1) {
            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Chief PE') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Chief PE') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Chief PE') {
                      if (value.remark == "Rejected") {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                      }else{
                        if ('' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'S-MIS' || '{{$role_code}}' == 'C-MIS') {
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            var url = "{{ url('kanagata/approval/') }}";
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;" target="_blank" >'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                          }
                        }else{
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }else{
            tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
          }
          if (approval_remark.indexOf("Manager PE") != -1) {
            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Manager PE') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Manager PE') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Manager PE') {
                      if (value.remark == "Rejected") {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                      }else{
                        if ('' == result.leave_approvals[i][j].approver_id || '{{$role_code}}' == 'MIS' || '{{$role_code}}' == 'S-MIS' || '{{$role_code}}' == 'C-MIS') {
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            var url = "{{ url('kanagata/approval/') }}";
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;" >'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                          }
                        }else{
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }else{
            tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
          }

          if (approval_remark.indexOf("Manager Japanese Speacialist PE") != -1) {
            for(var i = 0; i < result.leave_approvals.length;i++){
              if (result.leave_approvals[i][0].request_id == value.request_id) {
                for(var j = 0; j < result.leave_approvals[i].length;j++){
                  if (result.leave_approvals[i][j].status == 'Approved') {
                    if (result.leave_approvals[i][j].remark == 'Manager Japanese Speacialist PE') {
                      tableData += '<td style="background-color:#00a65a;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == 'Rejected'){
                    if (result.leave_approvals[i][j].remark == 'Manager Japanese Speacialist PE') {
                      tableData += '<td style="background-color:#f39c12;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>'+result.leave_approvals[i][j].approved_ats+'</td>';
                    }
                  }else if(result.leave_approvals[i][j].status == null){
                    if (result.leave_approvals[i][j].remark == 'Manager Japanese Speacialist PE') {
                      if (value.remark == "Rejected") {
                        tableData += '<td style="font-weight:bold;font-size:11px"></td>';               
                      }else{
                        if ('' == result.leave_approvals[i][j].approver_id || '' == 'MIS' || '{{$role_code}}' == 'S-MIS' || '{{$role_code}}' == 'C-MIS') {
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            var url = "{{ url('kanagata/approval/') }}";
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;"><a style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;" >'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</a></td>';
                          }
                        }else{
                          if (result.leave_approvals[i][j].keutamaan == 'belum') {
                            tableData += '<td style="font-weight:bold;font-size:11px"></td>';
                          }else{
                            tableData += '<td style="background-color:#dd4b39;color:white;font-weight:bold;font-size:11px;">'+result.leave_approvals[i][j].approver_name.split(' ').slice(0,2).join(' ')+'<br>Waiting</td>';
                          }
                        }
                      }
                      }
                    }
                  }
                }
              }
              }else{
                tableData += '<td style="background-color:#2b2b2b;color:white;font-size:11px;font-weight:bold;">None</td>';
              }

              for(var i = 0; i < result.leave_approvals.length;i++){
                if (result.leave_approvals[i][0].request_id == value.request_id) {
                  for(var j = 0; j < result.leave_approvals[i].length;j++){
                    if (result.leave_approvals[i][j].status != null) {
                      last_approval = result.leave_approvals[i][j].remark;
                    }
                  }
                }
              }
          // tableData += '</td>';
          if (value.decision == null) {
            tableData += '<td style="font-weight:bold;color:red">-</td>';
          }else{
            tableData += '<td style="font-weight:bold;color:red">'+ value.decision +'</td>';
          }


          tableData += '</tr>';
        });
$('#bodyTableLeave').append(tableData);




}
else{
  alert('Attempt to retrieve data failed');
}
});
}



function getActualFullDate() {
  var today = new Date();

  var date = today.getFullYear()+'-'+addZero(today.getMonth()+1)+'-'+addZero(today.getDate());
  return date;
}

function addZero(number) {
  return number.toString().padStart(2, "0");
}


</script>
@endsection