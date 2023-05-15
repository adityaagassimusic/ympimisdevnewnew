@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
 p > img{
  max-width: 300px;
  height: auto !important;
}
</style>
@stop
@section('header')
<section class="content-header">
<!-- 	<h1>
		List Patrol<small class="text-purple"></small>
	</h1> -->
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
        <div class="col-md-6" style="padding-right: 0">
          <div id="chart_kategori" style="width: 99%; height: 300px;"></div>
        </div>

        <div class="col-md-6" style="padding-left: 3px">
        	<div class="col-lg-6">
						<div class="small-box bg-aqua">
							<a href="{{url('files/patrol_guide/Gueidelines S-UP Step I.pdf')}}" target="_blank" class="small-box-footer" style="height:140px">
							<div class="icon" style="top:-1px;right: 8.5vw;font-size: 70px;color: white;">
								<i class="fa fa-file-pdf-o"></i>
							</div>
							<div class="inner" style="margin-top:90px">
								<h4>Guidelines S-Up Step I</h4>
							 </div>
							</a>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="small-box bg-purple">
							<a href="{{url('files/patrol_guide/Gueidelines S-UP Step II.pdf')}}" target="_blank" class="small-box-footer" style="height:140px">
							<div class="icon" style="top:-1px;right: 8.5vw;font-size: 70px;color: white;">
								<i class="fa fa-file-pdf-o"></i>
							</div>
							<div class="inner" style="margin-top:90px">
								<h4>Guidelines Step II</h4>
							 </div>
							</a>
						</div>
					</div>

					<div class="col-lg-6">
						<div class="small-box bg-red">
							<a href="{{url('files/patrol_guide/Gueidelines S-UP Step III.pdf')}}" target="_blank" class="small-box-footer" style="height:140px">
							<div class="icon" style="top:-1px;right: 8.5vw;font-size: 70px;color: white;">
								<i class="fa fa-file-pdf-o"></i>
							</div>
							<div class="inner" style="margin-top:90px">
								<h4>Guidelines Step III</h4>
							 </div>
							</a>
						</div>
					</div>

					<div class="col-lg-6">
						<div class="small-box bg-blue">
							<a href="{{url('files/patrol_guide/Gueidelines S-UP Step IV.pdf')}}" target="_blank" class="small-box-footer" style="height:140px">
							<div class="icon" style="top:-1px;right: 8.5vw;font-size: 70px;color: white;">
								<i class="fa fa-file-pdf-o"></i>
							</div>
							<div class="inner" style="margin-top:90px">
								<h4>Guidelines Step IV</h4>
							 </div>
							</a>
						</div>
					</div>
          <!-- <div id="chart_type" style="width: 99%; height: 300px;"></div> -->
        </div>

		<div class="col-xs-12" style="text-align: center;margin-bottom: 10px">
			<h3 class="box-title" style="color: white;margin-top: 10px;font-size: 28px;font-weight: bold;background-color: purple;padding: 10px">List Patrol<span class="text-purple"></span>
			</h3>
		</div>
		
		<div class="col-xs-6" style="text-align: center;padding-right: 0">
			<span style="font-size: 30px; color: green;"><i class="fa fa-angle-double-down"></i> Process <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/audit_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: green;">5S Patrol GM & Presdir (社長パトロール)</a>
			<a href="{{ url('index/audit_patrol_std') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: green;">EHS & 5S Monthly Patrol</a>
			<a href="{{ url('index/audit_patrol_daily') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: green;">Daily Patrol Shift 1 dan Shift 2</a>
			<a href="{{ url('index/audit_patrol_covid') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: green;">Covid-19 Patrol</a>
			<a href="{{ url('index/audit_patrol_outside') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: green;">YMPI Outside Factory Patrol</a>
			<a href="{{ url('index/audit_patrol_energy') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: green;">Patrol Penghematan Energi</a>	
			<!-- <a href="{{ url('index/audit_patrol_washing') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: green;">Patrol Washing Treatment</a> -->
			<a href="{{ url('index/audit_patrol_hrga') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: green;">Patrol HR-GA</a>

			<a href="{{ url('index/audit_patrol_vendor') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: green;">Patrol Vendor</a>
			<!-- <hr style="border: 1px solid red"> -->
			
		</div>
		<div class="col-xs-6" style="text-align: center;padding-left: 5px">
			<span style="font-size: 30px; color: red;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>
			<a href="{{ url('index/audit_patrol/monitoring') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;">GM & Presdir Patrol Monitoring (パトロール監視)</a>
			<a href="{{ url('index/audit_patrol_monitoring/monthly_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 65%;display: inline-block;">Monthly Patrol Monitoring & Response</a>  
			<a href="{{ url('index/patrol_resume/monthly_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 34%;display: inline-block;">Monthly By Location</a>

			<a href="{{ url('index/audit_patrol_monitoring/daily_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 65%;display: inline-block;">Daily Patrol Monitoring & Response</a>  
			<a href="{{ url('index/patrol_resume/daily_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 34%;display: inline-block;">Daily By Location</a>

			<a href="{{ url('index/audit_patrol_monitoring/covid_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 65%;display: inline-block;">Patrol Covid Monitoring & Response</a>  
			<a href="{{ url('index/patrol_resume/covid_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 34%;display: inline-block;">Covid By Location</a>

			<a href="{{ url('index/audit_patrol_monitoring/outside_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 65%;display: inline-block;">Outside Patrol Monitoring & Response</a>  
			<a href="{{ url('index/patrol_resume/outside_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 34%;display: inline-block;">Outside By Location</a>

			<a href="{{ url('index/audit_patrol_monitoring/energy_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 65%;display: inline-block;">Patrol Energy Monitoring & Response</a>  
			<a href="{{ url('index/patrol_resume/energy_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 34%;display: inline-block;">Energy By Location</a>
			
			<!-- <a href="{{ url('index/audit_patrol_monitoring/washing_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 65%;display: inline-block;">Patrol Washing Monitoring & Response</a>   -->
			<!-- <a href="{{ url('index/patrol_resume/washing_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 34%;display: inline-block;">Washing By Location</a> -->

			<a href="{{ url('index/audit_patrol_monitoring/hrga_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 65%;display: inline-block;">Patrol HR-GA Monitoring & Response</a>  
			<a href="{{ url('index/patrol_resume/hrga_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 34%;display: inline-block;">HR-GA By Location</a>

			<a href="{{ url('index/audit_patrol_monitoring/vendor_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 65%;display: inline-block;">Patrol Vendor Monitoring & Response</a>  
			<a href="{{ url('index/patrol_resume/vendor_patrol') }}" class="btn btn-default btn-block" style="font-size: 1.7vw; border-color: red;width: 34%;display: inline-block;">Vendor By Location</a>
		</div>
	</div>

	<div class="modal fade" id="myModalCategory">
    <div class="modal-dialog modal-lg" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table_category"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example3" class="table table-striped table-bordered table-hover" style="width: 100%;color: black"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Auditor</th>
                    <th>Auditee</th>
                    <th>Foto</th>
                    <th>Penanganan</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="myModalType">
    <div class="modal-dialog modal-lg" style="width:1250px;">
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="float: right;" id="modal-title"></h4>
          <h4 class="modal-title"><b>PT. YAMAHA MUSICAL PRODUCTS INDONESIA</b></h4>
          <br><h4 class="modal-title" id="judul_table_type"></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <table id="example4" class="table table-striped table-bordered table-hover" style="width: 100%;color: black"> 
                <thead style="background-color: rgba(126,86,134,.7);">
                  <tr>
                    <th>Auditor</th>
                    <th>Auditee</th>
                    <th>Foto</th>
                    <th>Penanganan</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-right" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        </div>
      </div>
    </div>
  </div>

</section>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script src="{{ url("js/pattern-fill.js")}}"></script>

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
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
		drawChart();
	});

	function drawChart() {    

	    var datefrom = $('#datefrom').val();
	    var dateto = $('#dateto').val();
	    var status = $('#status').val();

	    var data = {
	      datefrom: datefrom,
	      dateto: dateto,
	      status: status,
	    };

	    $.get('{{ url("fetch/patrol") }}', data, function(result, status, xhr) {
	      if(result.status){

	        var kategori = [];
	        var belum_ditangani_all = [];
	        var progress_ditangani_all = [];
	        var sudah_ditangani_all = [];

	        var point_judul = [];
	        var belum_ditangani_type = [];
	        var progress_ditangani_type = [];
	        var sudah_ditangani_type = [];

	        $.each(result.data_all, function(key, value) {
	        	if (value.kategori == "EHS & 5S Patrol"){
	        		value.kategori = "EHS 5S Monthly Patrol"
	        	}
	          kategori.push(value.kategori);
	          belum_ditangani_all.push(parseInt(value.jumlah_belum));
	          progress_ditangani_all.push(parseInt(value.jumlah_progress));
	          sudah_ditangani_all.push(parseInt(value.jumlah_sudah));
	        });

	        $.each(result.data_type_all, function(key, value) {
	          point_judul.push(value.point_judul);
	          belum_ditangani_type.push(parseInt(value.jumlah_belum));
	          progress_ditangani_type.push(parseInt(value.jumlah_progress));
	          sudah_ditangani_type.push(parseInt(value.jumlah_sudah));
	        });


	        $('#chart_kategori').highcharts({
	          chart: {
	            type: 'column',
	            backgroundColor: "#fff"
	          },
	          title: {
	            text: "Resume Patrol By Cases",
	            style:{
	              	color : '#000',
	                fontWeight:'Bold'
	            }
	          },
	          xAxis: {
	            type: 'category',
	            categories: kategori,
	            lineWidth:2,
	            lineColor:'#9e9e9e',
	            gridLineWidth: 1,
	            labels: {
	              style: {
	              	color : '#000',
	              	fontSize : '14px',
	                fontWeight:'Bold'
	              }
	            }
	          },
	          yAxis: {
	            lineWidth:2,
	            lineColor:'#fff',
	            type: 'linear',
	            title: {
	              text: 'Total Temuan',
	              style : {
	              	color : '#000'
	              }
	            },
	            stackLabels: {
	              enabled: true,
	              style: {
	                fontWeight: 'bold',
	                color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
	              }
	            }
	          },
	          legend: {
	            itemStyle:{
	              color: "#111",
	              fontSize: "12px"
	            }
	          },
	          plotOptions: {
	            series: {
	              cursor: 'pointer',
	              point: {
	                events: {
	                  click: function () {
	                    ShowModalCategory(this.category,this.series.name);
	                  }
	                }
	              },
	              dataLabels: {
	                enabled: false,
	                format: '{point.y}',
	                style:{
	                	color:"#000"
	                }
	              }
	            },
	            column: {
	              color:  Highcharts.ColorString,
	              stacking: 'percent',
	              pointPadding: 0.93,
	              groupPadding: 0.93,
	              borderWidth: 1,
	              dataLabels: {
	                enabled: true
	              }
	            }
	          },
	          credits: {
	            enabled: false
	          },

	          tooltip: {
	            formatter:function(){
	              return this.series.name+' : ' + this.y;
	            }
	          },
	          series: [
	          {
	            name: 'Temuan Belum Ditangani',
	            data: belum_ditangani_all,
	            color:"#dd4b39"
	          },
	          {
	            name: 'Temuan Progress',
	            data: progress_ditangani_all,
	            color:"#f39c12"
	          }
	          // ,
	          // {
	          //   name: 'Temuan Sudah Ditangani',
	          //   data: sudah_ditangani_all,
	          //   color: "#357a38"
	          // }
	          ]
	        })

	        $('#chart_type').highcharts({
	          chart: {
	            type: 'column',
	            backgroundColor: "#fff"
	          },
	          title: {
	            text: "Resume Patrol By Type",
	            style:{
	              	color : '#000',
	                fontWeight:'Bold'
	            }
	          },
	          xAxis: {
	            type: 'category',
	            categories: point_judul,
	            lineWidth:2,
	            lineColor:'#9e9e9e',
	            gridLineWidth: 1,
	            labels: {
	              style: {
	              	color : '#000',
	              	fontSize : '14px',
	                fontWeight:'Bold'
	              }
	            }
	          },
	          yAxis: {
	            lineWidth:2,
	            lineColor:'#fff',
	            type: 'linear',
	            title: {
	              text: 'Total Temuan',
	              style : {
	              	color : '#000'
	              }
	            },
	            stackLabels: {
	              enabled: true,
	              style: {
	                fontWeight: 'bold',
	                color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
	              }
	            }
	          },
	          legend: {
	            itemStyle:{
	              color: "#111",
	              fontSize: "12px"
	            }
	          },
	          plotOptions: {
	            series: {
	              cursor: 'pointer',
	              point: {
	                events: {
	                  click: function () {
	                    ShowModalType(this.category,this.series.name);
	                  }
	                }
	              },
	              dataLabels: {
	                enabled: false,
	                format: '{point.y}',
	                style:{
	                	color:"#000"
	                }
	              }
	            },
	            column: {
	              color:  Highcharts.ColorString,
	              stacking: 'percent',
	              pointPadding: 0.93,
	              groupPadding: 0.93,
	              borderWidth: 1,
	              dataLabels: {
	                enabled: true
	              }
	            }
	          },
	          credits: {
	            enabled: false
	          },

	          tooltip: {
	            formatter:function(){
	              return this.series.name+' : ' + this.y;
	            }
	          },
	          series: [
	          {
	            name: 'Temuan Belum Ditangani',
	            data: belum_ditangani_type,
	            color:"#dd4b39"
	          },
	          {
	            name: 'Temuan Progress',
	            data: progress_ditangani_type,
	            color: "#357a38"
	          }
	          // ,
	          // {
	          //   name: 'Temuan Sudah Ditangani',
	          //   data: sudah_ditangani_type,
	          //   color: "#357a38"
	          // }
	          ]
	        })
	      } else{
	        alert('Attempt to retrieve data failed');
	      }
	    })
	  }


	  function ShowModalCategory(kategori, status) {
	    tabel = $('#example3').DataTable();
	    tabel.destroy();

	    $("#myModalCategory").modal("show");

	    var table = $('#example3').DataTable({
	      'dom': 'Bfrtip',
	      'responsive': true,
	      'lengthMenu': [
	      [ 10, 25, 50, -1 ],
	      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
	      ],
	      'buttons': {
	        buttons:[
	        {
	          extend: 'pageLength',
	          className: 'btn btn-default',
	          // text: '<i class="fa fa-print"></i> Show',
	        },
	        {
	          extend: 'copy',
	          className: 'btn btn-success',
	          text: '<i class="fa fa-copy"></i> Copy',
	          exportOptions: {
	            columns: ':not(.notexport)'
	          }
	        },
	        {
	          extend: 'excel',
	          className: 'btn btn-info',
	          text: '<i class="fa fa-file-excel-o"></i> Excel',
	          exportOptions: {
	            columns: ':not(.notexport)'
	          }
	        },
	        {
	          extend: 'print',
	          className: 'btn btn-warning',
	          text: '<i class="fa fa-print"></i> Print',
	          exportOptions: {
	            columns: ':not(.notexport)'
	          }
	        },
	        ]
	      },
	      'paging': true,
	      'lengthChange': true,
	      'searching': true,
	      'ordering': true,
	      'order': [],
	      'info': true,
	      'autoWidth': true,
	      "sPaginationType": "full_numbers",
	      "bJQueryUI": true,
	      "bAutoWidth": false,
	      "processing": true,
	      "serverSide": true,
	      "ajax": {
	        "type" : "get",
	        "url" : "{{ url("index/audit_patrol/detail_category") }}",
	        "data" : {
	          kategori : kategori,
	          status : status
	        }
	      },
	      "columns": [
		      {"data": "auditor_name", "width": "20%"},
		      {"data": "auditee_name" , "width": "20%"},
		      {"data": "foto", "width": "30%"},
		      {"data": "penanganan", "width": "30%"}
	      ]    
	    });

	    $('#judul_table_category').append().empty();
	    $('#judul_table_category').append('<center><b>'+status+' '+kategori+'</b></center>'); 
	  }

	  function ShowModalType(type, status) {
	    tabel = $('#example4').DataTable();
	    tabel.destroy();

	    $("#myModalType").modal("show");

	    var table = $('#example4').DataTable({
	      'dom': 'Bfrtip',
	      'responsive': true,
	      'lengthMenu': [
	      [ 10, 25, 50, -1 ],
	      [ '10 rows', '25 rows', '50 rows', 'Show all' ]
	      ],
	      'buttons': {
	        buttons:[
	        {
	          extend: 'pageLength',
	          className: 'btn btn-default',
	          // text: '<i class="fa fa-print"></i> Show',
	        },
	        {
	          extend: 'copy',
	          className: 'btn btn-success',
	          text: '<i class="fa fa-copy"></i> Copy',
	          exportOptions: {
	            columns: ':not(.notexport)'
	          }
	        },
	        {
	          extend: 'excel',
	          className: 'btn btn-info',
	          text: '<i class="fa fa-file-excel-o"></i> Excel',
	          exportOptions: {
	            columns: ':not(.notexport)'
	          }
	        },
	        {
	          extend: 'print',
	          className: 'btn btn-warning',
	          text: '<i class="fa fa-print"></i> Print',
	          exportOptions: {
	            columns: ':not(.notexport)'
	          }
	        },
	        ]
	      },
	      'paging': true,
	      'lengthChange': true,
	      'searching': true,
	      'ordering': true,
	      'order': [],
	      'info': true,
	      'autoWidth': true,
	      "sPaginationType": "full_numbers",
	      "bJQueryUI": true,
	      "bAutoWidth": false,
	      "processing": true,
	      "serverSide": true,
	      "ajax": {
	        "type" : "get",
	        "url" : "{{ url("index/audit_patrol/detail_type") }}",
	        "data" : {
	          type : type,
	          status : status
	        }
	      },
	      "columns": [
		      {"data": "auditor_name", "width": "20%"},
		      {"data": "auditee_name" , "width": "20%"},
		      {"data": "foto", "width": "30%"},
		      {"data": "penanganan", "width": "30%"}
	      ]    
	    });

	    $('#judul_table_type').append().empty();
	    $('#judul_table_type').append('<center><b>Patrol Kategori '+type+' '+status+'</b></center>'); 
	  }


</script>
@endsection