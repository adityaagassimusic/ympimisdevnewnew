@extends('layouts.display')
@section('stylesheets')
<style type="text/css">

	table.table-bordered{
  border:1px solid black;
}
/*table.table-bordered > thead > tr > th{
  border:1px solid rgb(54, 59, 56) !important;
  text-align: center;
  background-color: #212121;  
  color:white;
}
table.table-bordered > tbody > tr > td{
  border:1px solid rgb(54, 59, 56);
  background-color: #212121;
  color: white;
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
  border:1px solid black !important;
  text-align: center;
  background-color: rgba(126,86,134,.7) !important;  
}

table.table-striped > tbody > tr > td{
  border: 1px solid #eeeeee !important;
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
  border:2px solid #f4f4f4;
  color: white;
}*/
	.content{
		color: white;
		font-weight: bold;
	}
	#loading, #error { display: none; }

	.loading {
		margin-top: 8%;
		position: absolute;
		left: 50%;
		top: 50%;
		-ms-transform: translateY(-50%);
		transform: translateY(-50%);
	}

  .gambar {
    width: 300px;
    background-color: none;
    border-radius: 5px;
    margin-left: 5px;
    margin-top: 15px;
    display: inline-block;
    border: 2px solid white;
  }

  #table-count{
  	border: 1px solid #000 !important;
  	padding: 5px;
  }

  #sedang {
	/*width: 50px;
	height: 50px;*/
	-webkit-animation: sedang 1s infinite;  /* Safari 4+ */
	-moz-animation: sedang 1s infinite;  /* Fx 5+ */
	-o-animation: sedang 1s infinite;  /* Opera 12+ */
	animation: sedang 1s infinite;  /* IE 10+, Fx 29+ */
}

@-webkit-keyframes sedang {
	0%, 49% {
		background-color: #3c3c3c;
		color: white;
	}
	50%, 100% {
		background-color: #91ff5e;
		color: #3c3c3c;
	}
}

#warning {
	/*width: 50px;
	height: 50px;*/
	-webkit-animation: warning 1s infinite;  /* Safari 4+ */
	-moz-animation: warning 1s infinite;  /* Fx 5+ */
	-o-animation: warning 1s infinite;  /* Opera 12+ */
	animation: warning 1s infinite;  /* IE 10+, Fx 29+ */
}

@-webkit-keyframes warning {
	0%, 49% {
		background-color: #3c3c3c;
		color: white;
	}
	50%, 100% {
		background-color: #ffea5e;
		color: #3c3c3c;
	}
}

#danger {
	/*width: 50px;
	height: 50px;*/
	-webkit-animation: danger 1s infinite;  /* Safari 4+ */
	-moz-animation: danger 1s infinite;  /* Fx 5+ */
	-o-animation: danger 1s infinite;  /* Opera 12+ */
	animation: danger 1s infinite;  /* IE 10+, Fx 29+ */
}

@-webkit-keyframes danger {
	0%, 49% {
		background-color: #3c3c3c;
		color: white;
	}
	50%, 100% {
		background-color: #ff5e5e;
		color: white;
	}
}

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12" style="margin-bottom: 10px;">
			<div class="row">
				<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
					<select class="form-control select2" id="product" data-placeholder="Pilih Product">
						<option value=""></option>
						@foreach($product as $product)
						<option value="{{$product->origin_group_name}}">{{$product->origin_group_name}}</option>
						@endforeach
					</select>
				</div>
				<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
					<select class="form-control select2" id="process" data-placeholder="Pilih Process">
						<option value=""></option>
						<option value="Forging">Forging</option>
						<option value="Bending">Bending</option>
						<option value="Trimming">Trimming</option>
						<option value="Blank Nuki">Blank Nuki</option>
						<option value="Hiraoshi">Hiraoshi</option>
						<option value="Trimming">Trimming</option>
						<option value="Blank Nuki">Blank Nuki</option>
						<option value="Nukishibori">Nukishibori</option>
					</select>
				</div>
				<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
					<select class="form-control select2" id="statuses" data-placeholder="Pilih Status">
						<option value=""></option>
						<option value="lifetime">OVER LIFETIME</option>
						<option value="periodik">OVER PERIODIK</option>
						<option value="ready">READY TO USE</option>
					</select>
				</div>
				<div class="col-xs-2" style="padding-left: 5px;padding-right: 5px;">
					<button class="btn btn-success" onclick="fillChart()">
						Search
					</button>
				</div>
			</div>
		</div>
		<div class="col-xs-12" id="div_periodik">
			<center style="background-color: #ffcc5e;width: 100%;color: black;padding:10px;margin-bottom: 5px;">
				<span style="font-size: 20px;font-weight: bold;">OVER PERIODIK</span>
			</center>
			<!-- <div class="row"> -->
				<div id="cont"></div>
			<!-- </div> -->
		</div>
		<div class="col-xs-12" id="div_lifetime">
			<center style="background-color: #ffa3a3;width: 100%;color: black;padding:10px;margin-bottom: 5px;">
				<span style="font-size: 20px;font-weight: bold;">OVER LIFETIME</span>
			</center>
			<!-- <div class="row"> -->
				<div id="cont3"></div>
			<!-- </div> -->
		</div>
		<div class="col-xs-12" id="div_ready">
			<center style="background-color: lightgreen;width: 100%;color: black;padding:10px;margin-bottom: 5px;">
				<span style="font-size: 20px;font-weight: bold;">READY TO USE</span>
			</center>
			<!-- <div class="row"> -->
				<div id="cont2"></div>
			<!-- </div> -->
		</div>
</section>
@endsection
@section('scripts')
<!-- <script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script> -->
<!-- <script src="{{ url("js/solid-gauge.js")}}"></script> -->
<!-- <script src="{{ url("js/accessibility.js")}}"></script> -->
<!-- <script src="{{ url("js/exporting.js")}}"></script> -->

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fillChart();
		setInterval(fillChart, 300000);

		$('.select2').select2({
			allowClear:true
		});
	});

	function addZero(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}
	
	function getActualFullDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		var h = addZero(d.getHours());
		var m = addZero(d.getMinutes());
		var s = addZero(d.getSeconds());
		return day + "-" + month + "-" + year + " (" + h + ":" + m + ":" + s +")";
	}

	function getActualDate() {
		var d = new Date();
		var day = addZero(d.getDate());
		var month = addZero(d.getMonth()+1);
		var year = addZero(d.getFullYear());
		return day + "-" + month + "-" + year;
	}

	function fillChart() {
		var data = {
			product:$('#product').val(),
			process:$('#process').val(),
		}
		if ($('#statuses').val() != '') {
			$('#div_periodik').hide();
			$('#div_lifetime').hide();
			$('#div_ready').hide();
			$('#div_'+$('#statuses').val()).show();
		}else{
			$('#div_periodik').show();
			$('#div_lifetime').show();
			$('#div_ready').show();
		}
		$.get('{{ url("fetch/press/kanagata_lifetime") }}', data,function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					//OVER PERIODIK
					$('#cont').html("");

					var part = [];
					var material = [];
					var product = [];
					var part_number = [];
					var last_counter = [];
					var qty_check = [];
					var qty_check_limit = [];
					var color = [];
					var status_mesin = [];
					var data = [];
					var location = [];
					var limit = [];
					var body = '';

					for (var i = 0; i < result.kanagata.length; i++) {
						if (parseInt(result.kanagata[i].qty_check) >= parseInt(result.kanagata[i].qty_check_limit)) {
							part.push(result.kanagata[i].part);
							material.push(result.kanagata[i].material_name);
							product.push(result.kanagata[i].product);
							part_number.push(result.kanagata[i].punch_die_number);
							last_counter.push(parseInt(result.kanagata[i].lifetime));
							qty_check.push(parseInt(result.kanagata[i].qty_check));
							qty_check_limit.push(parseInt(result.kanagata[i].qty_check_limit));
							location.push(result.kanagata[i].location);
							limit.push(result.kanagata[i].lifetime_limit);
							var a = i+1;
							body += '<div class="gambar" style="margin-top:0px" id="container'+a+'"></div>';
						}
					}
					$('#cont').append(body);

					for (var j = 0; j < part.length; j++) {
						var a = j+1;
						var container = 'container'+a;
						var containerin = "";

						containerin += '<table style="text-align:center;width:100%">';

						containerin += '<tr><td colspan="3" style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:black;background-color:white;font-size:15px">'+part_number[j]+' - '+part[j]+' - '+material[j];
						containerin += '</td></tr>';

						containerin += '<tr>';
						containerin += '<td colspan="3" style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">';

						containerin += '<div class="progress-group" id="progress_div" style="padding-bottom:0px;margin-bottom:0px">';
						containerin += '<div class="progress" style="height: 30px; border-style: solid;border-width: 1px; border-color: #d3d3d3;padding-bottom:0px;margin-bottom:0px">';
						containerin += '<span class="progress-text" id="progress_text_pasang'+j+'" style="font-size: 20px;padding-top:20px;color:black"></span>';
						containerin += '<div id="progress_bar_pasang'+j+'" style="font-size: 2px;text-align:left;padding-left:7px;"></div>';
						containerin += '</div>';
						containerin += '</div>';

						containerin += '</td>';
						containerin += '</tr>';

		    			if (parseInt(last_counter[j]) > parseFloat(parseInt(limit[j]*0.7)) && parseInt(last_counter[j]) < parseInt(limit[j])) {
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Actual</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="warning" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
		    			}else if(parseInt(last_counter[j]) >= parseInt(limit[j])){
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Actual</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="danger" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
		    			}else{
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Actual</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
		    			}

		    			if (parseInt(qty_check[j]) > (0.7*parseInt(qty_check_limit[j])) && parseInt(qty_check[j]) < parseInt(qty_check_limit[j])) {
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Periodik</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="warning" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+qty_check[j]+'</td></tr>';
		    			}else if(parseInt(qty_check[j]) >= parseInt(qty_check_limit[j])){
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Periodik</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="danger" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+qty_check[j]+'</td></tr>';
		    			}else{
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Periodik</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+qty_check[j]+'</td></tr>';
		    			}

		            	containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Loc</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+location[j]+'</td></tr>';

		            	containerin += '</table>';

		            	$('#'+container).append(containerin);
					}

					for (var j = 0; j < part.length; j++) {
						persen = (parseFloat(last_counter[j]) / parseInt(limit[j])) * 100;
						$('#progress_bar_pasang'+j).html(parseInt(limit[j]));
						$('#progress_bar_pasang'+j).css('width', persen.toFixed(1)+'%');
						$('#progress_bar_pasang'+j).css('color', '#000');
						$('#progress_bar_pasang'+j).css('font-weight', 'bold');
						$('#progress_bar_pasang'+j).css('font-size', '20px');
						$('#progress_bar_pasang'+j).css('padding-top', '3px');
						if (last_counter[j] > 0 && last_counter[j] <= parseFloat(parseInt(limit[j]*0.7))) {
							$('#progress_bar_pasang'+j).attr('class', 'progress-bar-success progress-bar progress-bar-striped');
						}else if(last_counter[j] > parseFloat(parseInt(limit[j]*0.7)) && last_counter[j] <= parseInt(limit[j])){
							$('#progress_bar_pasang'+j).attr('class', 'progress-bar-warning progress-bar progress-bar-striped');
						}else{
							$('#progress_bar_pasang'+j).attr('class', 'progress-bar-danger progress-bar progress-bar-striped');
						}
					};

					//READY TO USE
					$('#cont2').html("");

					var part = [];
					var material = [];
					var product = [];
					var part_number = [];
					var last_counter = [];
					var qty_check = [];
					var qty_check_limit = [];
					var color = [];
					var status_mesin = [];
					var data = [];
					var limit = [];
					var location = [];
					var body = '';

					var index = 0;

					for (var i = 0; i < result.kanagata.length; i++) {
						if (parseInt(result.kanagata[i].qty_check) < parseInt(result.kanagata[i].qty_check_limit) && parseInt(result.kanagata[i].lifetime) < parseFloat(parseInt(result.kanagata[i].lifetime_limit)*0.7)) {
							part.push(result.kanagata[i].part);
							material.push(result.kanagata[i].material_name);
							product.push(result.kanagata[i].product);
							part_number.push(result.kanagata[i].punch_die_number);
							last_counter.push(parseInt(result.kanagata[i].lifetime));
							qty_check.push(parseInt(result.kanagata[i].qty_check));
							qty_check_limit.push(parseInt(result.kanagata[i].qty_check_limit));
							location.push(result.kanagata[i].location);
							limit.push(result.kanagata[i].lifetime_limit);
							body += '<div class="gambar" style="margin-top:0px" id="container_non'+(index+1)+'"></div>';
							index++;
						}
					}
					$('#cont2').append(body);
					for (var j = 0; j < part.length; j++) {
						var a = j+1;
						var container = 'container_non'+a;
						var containerin = "";

						containerin += '<table style="text-align:center;width:100%">';

						containerin += '<tr><td colspan="3" style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:black;background-color:white;font-size:15px">'+part_number[j]+' - '+part[j]+' - '+material[j];
						containerin += '</td></tr>';

						containerin += '<tr>';
						containerin += '<td colspan="3" style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">';

						containerin += '<div class="progress-group" id="progress_div_non" style="padding-bottom:0px;margin-bottom:0px">';
						containerin += '<div class="progress" style="height: 30px; border-style: solid;border-width: 1px; border-color: #d3d3d3;padding-bottom:0px;margin-bottom:0px">';
						containerin += '<span class="progress-text" id="progress_text_pasangnon'+j+'" style="font-size: 20px;padding-top:20px;color:black"></span>';
						containerin += '<div id="progress_bar_pasangnon'+j+'" style="font-size: 2px;text-align:left;padding-left:7px;"></div>';
						containerin += '</div>';
						containerin += '</div>';

						containerin += '</td>';
						containerin += '</tr>';

		    			if (parseInt(last_counter[j]) > parseFloat(parseInt(limit[j]*0.7)) && parseInt(last_counter[j]) < parseInt(limit[j])) {
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Actual</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="warning" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
		    			}else if(parseInt(last_counter[j]) >= parseInt(limit[j])){
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Actual</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="danger" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
		    			}else{
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Actual</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
		    			}

		    			if (parseInt(qty_check[j]) > (0.7*parseInt(qty_check_limit[j])) && parseInt(qty_check[j]) < parseInt(qty_check_limit[j])) {
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Periodik</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="warning" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+qty_check[j]+'</td></tr>';
		    			}else if(parseInt(qty_check[j]) >= parseInt(qty_check_limit[j])){
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Periodik</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="danger" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+qty_check[j]+'</td></tr>';
		    			}else{
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Periodik</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+qty_check[j]+'</td></tr>';
		    			}

		            	containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Loc</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+location[j]+'</td></tr>';

		            	containerin += '</table>';

		            	$('#'+container).append(containerin);
					}

					for (var j = 0; j < part.length; j++) {
						persen = (parseFloat(last_counter[j]) / parseInt(limit[j])) * 100;
						$('#progress_bar_pasangnon'+j).html(parseInt(limit[j]));
						$('#progress_bar_pasangnon'+j).css('width', persen.toFixed(1)+'%');
						$('#progress_bar_pasangnon'+j).css('color', '#000');
						$('#progress_bar_pasangnon'+j).css('font-weight', 'bold');
						$('#progress_bar_pasangnon'+j).css('font-size', '20px');
						$('#progress_bar_pasangnon'+j).css('padding-top', '3px');
						if (last_counter[j] > 0 && last_counter[j] <= parseFloat(parseInt(limit[j]*0.7))) {
							$('#progress_bar_pasangnon'+j).attr('class', 'progress-bar-success progress-bar progress-bar-striped');
						}else if(last_counter[j] > parseFloat(parseInt(limit[j]*0.7)) && last_counter[j] <= parseInt(limit[j])){
							$('#progress_bar_pasangnon'+j).attr('class', 'progress-bar-warning progress-bar progress-bar-striped');
						}else{
							$('#progress_bar_pasangnon'+j).attr('class', 'progress-bar-danger progress-bar progress-bar-striped');
						}
					};

					//OVER LIFETIME
					$('#cont3').html("");

					var part = [];
					var product = [];
					var part_number = [];
					var last_counter = [];
					var qty_check = [];
					var qty_check_limit = [];
					var color = [];
					var status_mesin = [];
					var data = [];
					var material = [];
					var limit = [];
					var location = [];
					var body = '';

					var index = 0;

					for (var i = 0; i < result.kanagata.length; i++) {
						if (parseInt(result.kanagata[i].lifetime) >= parseFloat(parseInt(result.kanagata[i].lifetime_limit)*0.7) && parseInt(result.kanagata[i].qty_check) < parseInt(result.kanagata[i].qty_check_limit)) {
							part.push(result.kanagata[i].part);
							material.push(result.kanagata[i].material_name);
							product.push(result.kanagata[i].product);
							part_number.push(result.kanagata[i].punch_die_number);
							last_counter.push(parseInt(result.kanagata[i].lifetime));
							qty_check.push(parseInt(result.kanagata[i].qty_check));
							qty_check_limit.push(parseInt(result.kanagata[i].qty_check_limit));
							location.push(result.kanagata[i].location);
							limit.push(result.kanagata[i].lifetime_limit);
							body += '<div class="gambar" style="margin-top:0px" id="container_limit'+(index+1)+'"></div>';
							index++;
						}
					}
					$('#cont3').append(body);
					for (var j = 0; j < part.length; j++) {
						var a = j+1;
						var container = 'container_limit'+a;
						var containerin = "";

						containerin += '<table style="text-align:center;width:100%">';

						containerin += '<tr><td colspan="3" style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:black;background-color:white;font-size:15px">'+part_number[j]+' - '+part[j]+' - '+material[j];
						containerin += '</td></tr>';

						containerin += '<tr>';
						containerin += '<td colspan="3" style="width:100%;border: 1px solid #fff !important;color:white;font-size:15px;text-align:center">';

						containerin += '<div class="progress-group" id="progress_div_limit" style="padding-bottom:0px;margin-bottom:0px">';
						containerin += '<div class="progress" style="height: 30px; border-style: solid;border-width: 1px; border-color: #d3d3d3;padding-bottom:0px;margin-bottom:0px">';
						containerin += '<span class="progress-text" id="progress_text_pasanglimit'+j+'" style="font-size: 20px;padding-top:20px;color:black"></span>';
						containerin += '<div id="progress_bar_pasanglimit'+j+'" style="font-size: 2px;text-align:left;padding-left:7px;"></div>';
						containerin += '</div>';
						containerin += '</div>';

						containerin += '</td>';
						containerin += '</tr>';

		    			if (parseInt(last_counter[j]) > parseFloat(parseInt(limit[j]*0.7)) && parseInt(last_counter[j]) < parseInt(limit[j])) {
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Actual</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="warning" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
		    			}else if(parseInt(last_counter[j]) >= parseInt(limit[j])){
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Actual</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="danger" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
		    			}else{
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Actual</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+last_counter[j]+'</td></tr>';
		    			}

		    			if (parseInt(qty_check[j]) > (0.7*parseInt(qty_check_limit[j])) && parseInt(qty_check[j]) < parseInt(qty_check_limit[j])) {
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Periodik</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="warning" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+qty_check[j]+'</td></tr>';
		    			}else if(parseInt(qty_check[j]) >= parseInt(qty_check_limit[j])){
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Periodik</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td id="danger" style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+qty_check[j]+'</td></tr>';
		    			}else{
		    				containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Shots Periodik</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+qty_check[j]+'</td></tr>';
		    			}

		            	containerin += '<tr><td style="border: 1px solid #fff !important;color:white;font-size:15px;text-align:left;padding-left:7px;">Loc</td><td style="border: 1px solid #fff !important;padding-left:10px;padding-right:10px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">:</td><td style="border: 1px solid #fff !important;padding-left:20px;padding-right:20px;padding-top:2px;padding-bottom:2px;color:white;font-size:15px">'+location[j]+'</td></tr>';

		            	containerin += '</table>';

		            	$('#'+container).append(containerin);
					}

					for (var j = 0; j < part.length; j++) {
						persen = (parseFloat(last_counter[j]) / parseInt(limit[j])) * 100;
						$('#progress_bar_pasanglimit'+j).html(parseInt(limit[j]));
						$('#progress_bar_pasanglimit'+j).css('width', persen.toFixed(1)+'%');
						$('#progress_bar_pasanglimit'+j).css('color', '#000');
						$('#progress_bar_pasanglimit'+j).css('font-weight', 'bold');
						$('#progress_bar_pasanglimit'+j).css('font-size', '20px');
						$('#progress_bar_pasanglimit'+j).css('padding-top', '3px');
						if (last_counter[j] > 0 && last_counter[j] <= parseFloat(parseInt(limit[j]*0.7))) {
							$('#progress_bar_pasanglimit'+j).attr('class', 'progress-bar-success progress-bar progress-bar-striped');
						}else if(last_counter[j] > parseFloat(parseInt(limit[j]*0.7)) && last_counter[j] <= parseInt(limit[j])){
							$('#progress_bar_pasanglimit'+j).attr('class', 'progress-bar-warning progress-bar progress-bar-striped');
						}else{
							$('#progress_bar_pasanglimit'+j).attr('class', 'progress-bar-danger progress-bar progress-bar-striped');
						}
					};
				}
			}
		});

	}


</script>
@endsection