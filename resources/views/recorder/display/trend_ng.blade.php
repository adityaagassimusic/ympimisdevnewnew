@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	thead > tr > th{
		padding-right: 3px;
		padding-left: 3px;
	}
	tbody > tr > td{
		padding-right: 3px;
		padding-left: 3px;
	}
	#loading, #error { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<div class="row">
		<div id="period_title" class="col-xs-10" style="background-color: rgba(248,161,63,0.9);padding-left: 5px;padding-right: 5px;"><center><span style="color: black; font-size: 1.6vw; font-weight: bold;" id="title_text">TREND NG BY MESIN</span></center></div>
		<div class="col-xs-2" style="padding-left: 5px;padding-right: 0px;">
				<select class="form-control select2" style="width: 100%" id="mesin" data-placeholder="Pilih Mesin" onchange="fetchData()">
					<option value=""></option>
					@foreach($mesin as $mesin)
					<option value="{{$mesin->mesin}}">{{$mesin->mesin}}</option>
					@endforeach
				</select>
		</div>
		<!-- <div id="period_title" class="col-xs-3" style="background-color: rgba(63, 97, 248,0.9);padding-left: 5px;padding-right: 5px;"><center><span style="color: white; font-size: 1.6vw; font-weight: bold;" id="title_text">TREND NG BY Mesin</span></center></div> -->
		<div class="col-xs-12">
			<div class="row" id="monitoring">
			</div>
		</div>
		<!-- <div class="col-xs-6">
			<div class="row" id="monitoring2">
			</div>
		</div> -->
	</div>

	<div class="modal fade" id="modalImage">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header"><center> <b style="font-size: 2vw"></b> </center>
					<div class="modal-body table-responsive no-padding">
						<div class="col-xs-12" id="monitoring2" style="padding-top: 20px">
							
						</div>
						<div class="col-xs-12" style="padding-top: 20px">
							<div class="modal-footer">
								<div class="row">
									<button class="btn btn-danger btn-block pull-right" data-dismiss="modal" aria-hidden="true" style="font-size: 15px;font-weight: bold;">
										CLOSE
									</button>
								</div>
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
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/dataTables.buttons.min.js")}}"></script>
<script src="{{ url("js/buttons.flash.min.js")}}"></script>
<script src="{{ url("js/jszip.min.js")}}"></script>
<script src="{{ url("js/vfs_fonts.js")}}"></script>
<script src="{{ url("js/buttons.html5.min.js")}}"></script>
<script src="{{ url("js/buttons.print.min.js")}}"></script>
<script src="{{ url("js/bootstrap-toggle.min.js") }}"></script>
<script src="https://code.highcharts.com/stock/highstock.js"></script>
<script src="https://code.highcharts.com/stock/modules/data.js"></script>
<script src="https://code.highcharts.com/stock/modules/accessibility.js"></script>
<!-- <script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script> -->
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('.select2').select2({
			allowClear:true
		});
		$('.datepicker').datepicker({
			autoclose: true,
			format: "yyyy-mm-dd"
		});
		fetchData();
		// setInterval(fetchData, 1000*60*60);
	});

	function fetchData(){
		$('#loading').show();
		var data = {
			mesin:$('#mesin').val(),
		}

		var colorArray = ['#FF6633',  '#FF33FF', '#00B3E6', 
		  '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
		  '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A', 
		  '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
		  '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC', 
		  '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
		  '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680', 
		  '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
		  '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3', 
		  '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF','#FFB399'];
		$.get('{{ url("fetch/recorder/display/ng") }}',data,  function(result, status, xhr){
			if(result.status){
				// $('#monitoring').html('');
				var monitoring = '';
				for(var i = 0; i < result.part.length;i++){
					monitoring += '<div class="col-xs-4" id="div_'+result.part[i].part_comb.split('_')[0]+'_'+result.part[i].part_comb.split('_')[1]+'" style="height: 25vw; margin-bottom: 40px;margin-top:5px;padding-left:10px;padding-right:10px;"><div id="'+result.part[i].part_comb.split('_')[0]+'_'+result.part[i].part_comb.split('_')[1]+'" style="width: 100%;margin-top:10px;"></div></div>';
				}
				$('#monitoring').append(monitoring);

				for(var k = 0; k < result.part.length;k++){
					var ng_rate = [];
					var category = [];
					var shot = [];
					var perbaikan = [];

					var a = 95654400;
					var series = [];
					var moldss = [];
					var molds = result.part[k].molding.split(',');
					for(var j = 0; j < molds.length;j++){
						var data = [];
						for(var i = 0; i < result.date.length; i++){
							var date = new Date(result.date[i].date);
	                        var milliseconds = date.getTime();
	                        for(var l = 0; l < result.part_all[k].length;l++){
								var date = new Date(result.date[i].date);
		                        var milliseconds = date.getTime();
		                        if (result.date[i].date == result.part_all[k][l].date && molds[j] == result.part_all[k][l].molding) {
		                        	if (parseInt(result.part_all[k][l].ng) == 0) {
		                        		data.push([milliseconds,0]);
		                        		// dats = 0;
		                        	}else{
		                        		data.push([milliseconds,parseInt(result.part_all[k][l].ng)]);
		                        	}
		                        }
							}
						// if (data.length > 0) {
							// console.log(data[0]);
							
						// }
						}
						moldss.push(data);
					}
					// console.log(moldss);
					var molds = result.part[k].molding.split(',');
					for(var u = 0; u < molds.length;u++){
						series.push({
				            // enableMouseTracking:datalabel,
				            name: molds[u],
				            data: moldss[u],
				            type:'spline',
				            colorByPoint: false,
				            color:colorArray[u],
				            cursor: 'pointer',
				            point: {
				                events: {
				                    click: function () {
				                        showModalMolding(this.series.name);
				                    }
				                }
				            }
				            
				        });
					}

					// console.log(series);

					// console.log(moldss);

					// console.log(series);
					// if (series.length > 0) {
						document.getElementById('div_'+result.part[k].part_comb.split('_')[0]+'_'+result.part[k].part_comb.split('_')[1]).style.display = "block";
					    Highcharts.stockChart(result.part[k].part_comb.split('_')[0]+'_'+result.part[k].part_comb.split('_')[1], {
					    	chart:{
					    		height:'370px'
					    	},
					        rangeSelector: {
					            selected: 1
					        },

					        title: {
					            text: result.part[k].part_comb.split('_')[0]+' - '+result.part[k].part_comb.split('_')[1],
					            style:{
					            	fontSize:'15px',
					            	fontWeight:'bold'
					            }
					        },
					        legend:{
					        	enabled:true
					        },
					        tooltip: {
						        split: false,
						        headerFormat: '<span style="color:#000;font-weight: bold;">Tanggal </span>: <b>{point.x:%d-%b-%Y}</b><br/><span style="color:#000;font-weight: bold;">Mesin </span>: <b>'+result.part[k].part_comb.split('_')[0]+'</b><br/><span style="color:#000;font-weight: bold;">Produk </span>: <b>'+result.part[k].part_comb.split('_')[1]+'</b><br/>',
						        pointFormat: '<span style="color:#000;font-weight: bold;">Molding </span>: <b>{series.name}</b><br/><span style="color:#000;font-weight: bold;">NG </span>: <b>{point.y} PC(s)</b><br/>',
						    },
					        credits:{
					        	enabled:false
					        },
					        series:series
					    });
					// }else{
					// 	document.getElementById('div_'+result.part[k].part_comb.split('_')[0]+'_'+result.part[k].part_comb.split('_')[1]).style.display = "none";
					// }
				}

				$('#loading').hide();
			}
			else{
				alert('Attempt to retrieve data failed.');
				$('#loading').hide();
			}
		});
}

function showModalMolding(molding) {
	$('#loading').show();
	var data = {
		molding:molding,
	}
	$.get('{{ url("fetch/recorder/display/ng/mesin") }}',data,  function(result, status, xhr){
		if(result.status){
			$('#monitoring2').html('');
			var monitoring = '';
			for(var i = 0; i < result.molding.length;i++){
				monitoring += '<div class="col-xs-12" id="div_'+result.molding[i].molding+'" style="height: 25vw; margin-bottom: 30px;margin-top:5px;padding-left:10px;padding-right:10px;"><div id="'+result.molding[i].molding+'" style="width: 100%;"></div></div>';
			}
			var colorArray = [ '#00B3E6', 
			  '#E6B333', '#3366E6', '#999966', '#99FF99', '#B34D4D',
			  '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A', 
			  '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
			  '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC', 
			  '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
			  '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680', 
			  '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
			  '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3', 
			  '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF','#FFB399','#FF6633',  '#FF33FF'];
			$('#monitoring2').append(monitoring);

			for(var k = 0; k < result.molding.length;k++){
				var ng_rate = [];
				var category = [];
				var shot = [];
				var perbaikan = [];

				var series = [];
				var a = 95654400;
				var mesinss = [];
					// var datas = [];
					var mesins = result.molding[k].mesin.split(',');
					for(var j = 0; j < mesins.length;j++){
						var data = [];
						for(var i = 0; i < result.date.length; i++){
							var date = new Date(result.date[i].date);
	                        var milliseconds = date.getTime();
	                        var datalabel = true;
	                        for(var l = 0; l < result.part_all[k].length;l++){
								var date = new Date(result.date[i].date);
		                        var milliseconds = date.getTime();
		                        if (result.date[i].date == result.part_all[k][l].date && mesins[j] == result.part_all[k][l].mesin) {
		                        	// if (parseInt(result.part_all[k][l].ng) == 0) {
		                        	// 	datalabel = false;
		                        	// }else{
		                        	// 	data.push([milliseconds,parseInt(result.part_all[k][l].ng)]);
		                        	// }
		                        	data.push([milliseconds,parseInt(result.part_all[k][l].ng)]);
		                        }
							}
						}
						mesinss.push(data);
					}
						// if (data.length > 0) {
						// 	series.push({
					 //            type: 'column',
					 //            enableMouseTracking:datalabel,
					 //            name: mesins[j].mesin,
					 //            data: data,
					 //        });
						// }
				// console.log(mesinss);
				var mesins = result.molding[k].mesin.split(',');
				for(var u = 0; u < mesins.length;u++){
					series.push({
			            // enableMouseTracking:datalabel,
			            name: mesins[u],
			            data: mesinss[u],
			            type:'spline',
			            colorByPoint: false,
			            color:colorArray[u],
			            cursor: 'pointer',
			    //         tooltip:{
							// pointFormat: '<span style="color:#fff;font-weight: bold;">Tanggal </span>: <b>{point.x:%d-%b-%Y}</b><br/><span style="color:#fff;font-weight: bold;">Molding </span>: <b>'+molds[u]+'</b><br/><span style="color:{point.color};font-weight: bold;">NG </span>: <b>{point.y} PC(s)</b><br/>',
			    //         }
			        });
				}
				if (series.length > 0) {
					document.getElementById('div_'+result.molding[k].molding).style.display = "block";
					Highcharts.stockChart(result.molding[k].molding, {
				        chart: {
				            alignTicks: false,
				        },

				        rangeSelector: {
				            selected: 1
				        },

				        title: {
				            text: result.molding[k].molding,
				            style:{
				            	fontSize:'15px',
				            	fontWeight:'bold'
				            }
				        },
				        legend:{
				        	enabled:true,
				        },
				        tooltip: {
					        split: false,
					        headerFormat: '<span style="color:#000;font-weight: bold;">Tanggal </span>: <b>{point.x:%d-%b-%Y}</b><br/>',
						        pointFormat: '<span style="color:#000;font-weight: bold;">Mesin </span>: <b>{series.name}</b><br/><span style="color:#000;font-weight: bold;">Molding </span>: <b>'+result.molding[k].molding+'</b><br/><span style="color:#000;font-weight: bold;">NG </span>: <b>{point.y} PC(s)</b><br/>',
					    },
				        credits:{
				        	enabled:false
				        },

				        series: series
				    });
				}else{
					document.getElementById('div_'+result.molding[k].molding).style.display = "none";
				}
			}
			$('#loading').hide();
			$('#modalImage').modal('show');
		}
		else{
			alert('Attempt to retrieve data failed.');
			$('#loading').hide();
		}
	});
}

function randomDate(start, end) {
    return new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
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
@endsection

