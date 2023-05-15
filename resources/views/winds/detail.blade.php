@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        input {
            line-height: 22px;
        }

        thead>tr>th {
            text-align: center;
        }

        tbody>tr>td {
            text-align: center;
            color: black;
        }

        tfoot>tr>th {
            text-align: center;
        }

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        .content-wrapper {
            color: white;
            font-weight: bold;
            background-color: #313132 !important;
        }

        #loading,
        #error {
            display: none;
        }

        .loading {
            margin-top: 8%;
            position: absolute;
            left: 50%;
            top: 50%;
            -ms-transform: translateY(-50%);
            transform: translateY(-50%);
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $title }} <span class="text-purple"> {{ $title_jp }} </span>
        </h1>
        <ol class="breadcrumb">
        </ol>
    </section>
@stop

@section('content')
    <section class="content" style="padding-top:0">
        <div class="row">
            <div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
                <p style="position: absolute; color: White; top: 45%; left: 35%;">
                    <span style="font-size: 40px">Waiting, Please Wait <i class="fa fa-spin fa-refresh"></i></span>
                </p>
            </div>


            <div class="col-xs-10">
                <div class="col-xs-12" style="padding-left: 0; padding-bottom: 4px;">
                    <h2 style="font-weight: bold;margin-top: 10px">&nbsp;&nbsp;&nbsp;DETAIL MATERIAL : <span style="color:#ff8f00;"> {{ $data[0]->gmc }} - {{ $data[0]->deskripsi }}</span></h2>
                </div>
            </div>


            <div class="col-xs-2">
                <a class="btn btn-success" href="{{ url('winds') }}" aria-label="Close" style="font-weight: bold; font-size: 15px; width: 100%;"><i class="fa fa-chevron-left"></i> Back to Dashboard <br><i class="fa fa-chevron-left"></i> ダッシュボードに戻る</a>
            </div>

            <div class="col-xs-12" style="padding: 10px;" align="center">

                <a href="{{ url('winds_file/ik') }}/IK-{{ $proses[0]->proses }}.pdf" class="btn btn-warning" style="margin: 5px;font-weight: bold; font-size: 20px; width: 20%;" target="_blank"><i class="fa fa-file-pdf-o"> </i> IK File<br><i class="fa fa-file-pdf-o"> </i> 作業手順書</a>
                <a href="{{ url('winds/index/cdm/' . $data[0]->gmc . '/' . $proses_status . '/' . $proses[0]->proses . '/' . Request::segment(6)) }}" class="btn btn-success" style="margin: 5px;font-weight: bold; font-size: 20px; width: 20%;"><i class="fa fa-folder-o"></i> CDM Form<br><i class="fa fa-folder-o"></i> CDMファイル</a>
                <!-- <a href="#" class="btn btn-default" style="margin: 5px;font-weight: bold; font-size: 15px; width: 20%;">QC Kouteihyo File<br>QC工程表 / QC Kouteihyo<br>Bagan Proses QC</a> -->
                <a href="{{ url('winds/index/grafik_trendline/' . $data[0]->gmc . '/' . $proses[0]->proses) }}" class="btn btn-primary" style="margin: 5px;font-weight: bold; font-size: 20px; width: 20%;" target="_blank"><i class="fa fa-line-chart"></i> CDM Trendline<br><i class="fa fa-line-chart"></i> CDM傾向線</a>
            </div>
        </div>

        <div class="row">
            <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
                <div class="box-body">
                    <div class="col-xs-6" style="text-align:center;">
                        <span style="font-size: 25px;color: black;width: 25%;">Material <span class="text-purple">材料</span></span>
                        <br>
                        @if ($user)
                            @if ($user->department == "Production Engineering Department" || $user->department == "Management Information System Department")
                            <div class="row" style="margin-bottom:3px;">
                                <div class="col-xs-2"></div>
                                <div class="col-xs-10" align="right">
                                    <div class="btn-material-container">
                                        <button id="editMaterial" class="btn btn-primary btn-sm" onclick="editMaterial()"><i class="fa fa-pencil"></i> Edit </button>                                        
                                        <button id="cancelMaterial" class="btn btn-danger btn-sm" onclick="cancelMaterial()" style="display: none;"><i class="fa fa-times"></i> Cancel </button>                                        
                                    </div>
                                </div>
                            </div>
                            @endif                            
                        @endif
                        <div id="table-material-container">
                            <table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
                                <tr>
                                    <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: rgb(126,86,134); text-align: center">Keterangan</th>
                                    <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #fc8568;text-align: center">#</th>
                                </tr>
                                <tr align="center">
                                    <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">GMC Material Awal <span class="text-purple">素材GMC</span></td>
                                    <td id="gmc_raw" style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $data[0]->gmc_raw }}</td>
                                </tr>
                                <tr align="center">
                                    <td style="border:1px solid black; font-size: 17px; width: 25%; font-weight: normal; background-color:  #e8daef; color: black;">Deskripsi Material Awal <span class="text-purple">素材の名称</span></td>
                                    <td id="deskripsi_raw" style="border:1px solid black; font-size: 17px; width: 25%; font-weight: bold; background-color:  #fcd068; color: black;">{{ $data[0]->deskripsi_raw }}</td>
                                </tr>
                            </table>
                        </div>
                        @if ($user)
                            @if ($user->department == "Production Engineering Department" || $user->department == "Management Information System Department")
                            <center>
                                <button class="btn btn-success" id="saveMaterial" onclick="saveMaterial()" style="width: 100%; height:40px; font-size:16px; display:none;"><i class="fa fa-save"></i> Save </button>
                            </center>
                            @endif
                        @endif    
                    </div>
                    <div class="col-xs-6" style="text-align:center;">
                        <span style="font-size: 25px;color: black;width: 25%;">Parameter <span class="text-purple">パラメータ</span></span>
                        <br>
                        @if ($user)
                            @if ($user->department == "Production Engineering Department" || $user->department == "Management Information System Department")
                                <div class="row" style="margin-bottom:3px;">
                                    <div class="col-xs-2"></div>
                                    <div class="col-xs-10" align="right">
                                        <div class="btn-parameter-container">                                                                        
                                            <button class="btn btn-danger btn-sm" id="cancelParameter" onclick="cancelParameter()" style="display: none;"><i class="fa fa-close"></i> Cancel </button>
                                            <button class="btn btn-warning btn-sm" id="editParameter" onclick="editParameter()"><i class="fa fa-pencil"></i> Edit </button>                                    
                                        </div>
                                    </div>
                                </div>
                            @endif                            
                        @endif                       
                        <div id="table-parameter-container">
                            <table class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
                                <thead id="head_param">
                                    <tr>
                                        <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: rgb(126,86,134); text-align: center">Keterangan</th>
                                        <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #fc8568;text-align: center">#</th>
                                    </tr>
                                </thead>
                                <tbody id="body_param">

                                </tbody>
                            </table>                                                        
                        </div>
                        @if ($user)
                            @if ($user->department == "Production Engineering Department" || $user->department == "Management Information System Department")
                            <center>
                                <button class="btn btn-success" id="saveParameter" onclick="saveParameter()" style="width: 100%; height:40px; font-size:16px; display:none;"><i class="fa fa-save"></i> Save </button>
                            </center>
                            @endif
                        @endif                 
                    </div>
                </div>
            </div>
        </div>


        <!-- <div class="col-xs-12">
                      <div class="box box-solid" style="margin-bottom: 0px;margin-left: 0px;margin-right: 0px;margin-top: 10px">
                        <div class="box-body">
                          <div class="col-xs-12" style="margin-top: 0px;padding-top: 10px;padding: 0px">
                            <div class="col-xs-12" style="background-color: #e8daef;padding-left: 5px;padding-right: 5px;height:30px;vertical-align: middle;" align="center">
                              <span style="font-size: 25px;color: black;width: 25%;">PARAMETER パラメータ</span>
                            </div>
                            <br><br>
                            <table id="table_lot" class="table table-bordered table-striped" style="margin-bottom: 0;margin-top: 0px;padding-top: 0px;font-size: 17px">
                              <thead style="background-color: rgb(126,86,134);">
                                <tr>
                                  <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 60%">Title</th>
                                  <th style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 40%">Content</th>
                                </tr>
                              </thead>
                              <tbody id="parameter" style="text-align:center;">
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div> -->

        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    {{-- <script src="{{ url("js/pdfmake.min.js")}}"></script> --}}
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var material = [];
        var parameter = [];

        jQuery(document).ready(function() {            
            indexBodyParam();
            foreachBodyParam();                                                

        });

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '4000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '4000'
            });
        }

        function editMaterial() {            
            $('#gmc_raw').html("");
            $('#deskripsi_raw').html("");
            $('#gmc_raw').append('<input type="text" class="form-control" id="gmc_raw_input" value="{{ $data[0]->gmc_raw }}">');
            $('#deskripsi_raw').append('<input type="text" class="form-control" id="deskripsi_raw_input" value="{{ $data[0]->deskripsi_raw }}">');

            $('#editMaterial').hide();            
            $('#cancelMaterial').show();
            $('#saveMaterial').show();
        }

        function cancelMaterial() {            
            $('#gmc_raw').html("");
            $('#deskripsi_raw').html("");
            $('#gmc_raw').append('{{ $data[0]->gmc_raw }}');
            $('#deskripsi_raw').append('{{ $data[0]->deskripsi_raw }}');

            $('#editMaterial').show();
            $('#cancelMaterial').hide();
            $('#saveMaterial').hide();
        }

        function saveMaterial() {
            var gmc = '{{ $data[0]->gmc }}';
            var gmc_raw = $('#gmc_raw_input').val();
            var deskripsi_raw = $('#deskripsi_raw_input').val();

            var data = {
                gmc: gmc,
                gmc_raw: gmc_raw,
                deskripsi_raw: deskripsi_raw
            }

            $.post('{{ url("winds/index/description_item/update/material") }}', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter('Success', result.message);
                    window.location.reload();
                } else {
                    openErrorGritter('Error', result.message);
                }
            });
        }


        function indexBodyParam() {
            @foreach ($data as $parameter)
                parameter.push({
                    id: "{{ $parameter->id }}",
                    gmc : "{{ $parameter->gmc }}",
                    id_proses : "{{ $parameter->id_proses }}",
                    parameter_ind: "{{ $parameter->parameter_ind }}",
                    parameter_jpn: "{{ $parameter->parameter_jpn }}",
                    value: "{{ $parameter->value }}"
                });
            @endforeach
        }


        function foreachBodyParam() {
            var body_parameter = $('#body_param');
            body_parameter.html("");
            var table_body_parameter = "";
            for (var i = 0; i < parameter.length; i++) {                
                table_body_parameter += '<tr>';
                table_body_parameter += '<input type="hidden" id="id" value="' + parameter[i].id + '">';
                table_body_parameter += '<td style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #E8DAEF; text-align: center">' + parameter[i].parameter_ind + ' <span class="text-purple" style=>' + parameter[i].parameter_jpn + '</span>';
                table_body_parameter += '</td>';
                table_body_parameter += '<td style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #FCD068;text-align: center">' + parameter[i].value + '</td></tr>';
            }
            body_parameter.append(table_body_parameter);
        }

        function editParameter() {
            var body_parameter = $('#body_param');
            body_parameter.html("");            


            var table_edit_body_parameter = "";
            for (let i = 0; i < parameter.length; i++) {              
                table_edit_body_parameter += '<tr><td style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #E8DAEF; text-align: center">';
                table_edit_body_parameter += '<input type="text" class="form-control ArrayUpdate" value="'+parameter[i].parameter_ind+'" id="parameter_ind_'+i+'">';
                table_edit_body_parameter += '<input type="text" class="form-control ArrayUpdate" value="'+parameter[i].parameter_jpn+'" id="parameter_jpn_'+i+'"></td>';
                table_edit_body_parameter += '<td style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #FCD068;text-align: center">';
                table_edit_body_parameter += '<input type="text" class="form-control ArrayUpdate" value="'+parameter[i].value+'" id="value_'+i+'">';
                table_edit_body_parameter += '<button class="btn btn-danger" style="float:right;" onclick="deleteParameter('+parameter[i].id+')"><i class="fa fa-trash"></i></button>';
                table_edit_body_parameter += '</td>';                              
                table_edit_body_parameter += '</tr>';
            }
            // table_edit_body_parameter += '<tr><td style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #E8DAEF; text-align: center"><input type="text" class="form-control" id="parameter_ind_new"><input type="text" class="form-control" id="parameter_jpn_new"></td>';
            // table_edit_body_parameter += '<td style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #FCD068;text-align: center">';              
            table_edit_body_parameter += '<tr><td style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #E8DAEF; text-align: center">';            
            table_edit_body_parameter += '<input type="text" class="form-control" id="parameter_ind_new" placeholder="ind">';
            table_edit_body_parameter += '<input type="text" class="form-control" id="parameter_jpn_new" placeholder="jpn">';
            table_edit_body_parameter += '</td>';
            table_edit_body_parameter += '<td style="border: 1px solid black; font-size: 1.2vw; padding-top: 2px; padding-bottom: 2px; width: 25%; background-color: #FCD068;text-align: center">';
            table_edit_body_parameter += '<input type="text" class="form-control" id="value_new" placeholder="value">';
            table_edit_body_parameter += '<button class="btn btn-large btn-primary" style="width:100%; height:100%; color:#fff;" onclick="addRowParameter()"><i class="fa fa-plus"></i></button>';                        
            table_edit_body_parameter += '</td>';
            table_edit_body_parameter += '</tr>';

            body_parameter.append(table_edit_body_parameter);
            
            $('#editParameter').hide();            
            $('#cancelParameter').show();
            $('#saveParameter').show();


        }

        function cancelParameter() {
            foreachBodyParam();
            $('#addParameter').show();
            $('#editParameter').show();            
            $('#cancelParameter').hide();
            $('#saveParameter').hide();
        }

        function addRowParameter() {
            $('#parameter_ind_new').val();
            $('#parameter_jpn_new').val();
            $('#value_new').val();

            parameter.push({
                id: null,
                gmc : '{{ $gmc }}',
                id_proses : '{{ $proses_status }}',
                parameter_ind: $('#parameter_ind_new').val(),
                parameter_jpn: $('#parameter_jpn_new').val(),
                value: $('#value_new').val()
            });

            editParameter();            
        }

        function saveParameter() {                     

            for (let i = 0; i < parameter.length; i++) {
                parameter[i] = {
                    id: parameter[i].id,
                    gmc : '{{ $gmc }}',
                    id_proses : '{{ $proses_status }}',
                    parameter_ind: $('#parameter_ind_'+i).val(),
                    parameter_jpn: $('#parameter_jpn_'+i).val(),
                    value: $('#value_'+i).val(),
                    created_by: '{{ $proses[0]->proses }}'
                }
            }

            var data = {
                parameter: parameter
            }  

            $.post('{{ url("winds/index/description_item/update/parameter") }}', data, function(result, status, xhr) {
                if (result.status) {
                    openSuccessGritter('Success', result.message);                    
                } else {
                    openErrorGritter('Error', result.message);
                }
            });
        }

        function deleteParameter(index) {
            if (confirm('Anda yakin ingin menghapus parameter ini?')) {                       
    
                var data = {
                    id : index
                }
    
                $.post('{{ url("winds/index/description_item/delete") }}', data, function(result, status, xhr) {
                    if (result.status) {
                        openSuccessGritter('Success', result.message);                    
                        window.location.reload();
                    } else {
                        openErrorGritter('Error', result.message);
                    }
                });
            }

        }
    </script>

@endsection
