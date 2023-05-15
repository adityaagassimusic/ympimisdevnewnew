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
		color: #3c3c3c;
	}
}

</style>
@endsection
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div class="row">
		<div class="col-xs-12">
			<div class="row" id="cont">
				<!-- <center><h4 style="font-weight: bold;font-size: 35px;padding: 10px;;background-color: #42d4f5;color: black">MOLDING TERPASANG</h4></center> -->
			</div>
		</div>
</section>
@endsection
@section('scripts')
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-more.js")}}"></script>
<script src="{{ url("js/solid-gauge.js")}}"></script>
<script src="{{ url("js/accessibility.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function(){
		fillChart();
		setInterval(fillChart, 600000);
	});

	Highcharts.createElement('link', {
		href: '{{ url("fonts/UnicaOne.css")}}',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

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

		$.get('{{ url("fetch/molding_schedule") }}', function(result, status, xhr) {
			if(xhr.status == 200){
				if(result.status){

					$('#cont').html("");

					var schedule = [];

					for(var i = 0; i < result.schedules.length; i++){
						// if (result.schedules[i].schedule_isi != null) {
							if ('{{date("Y-m-d")}}' == result.schedules[i].date) {
								var background = '#8affb9';
								var today = 'TODAY : ';
							}else{
								var background = '#8ab7ff';
								var today = '';
							}
							schedule += '<div class="col-xs-2" style="padding-left:5px;padding-right:5px;min-height:20vw">';
							schedule += '<table class="table table-bordered tableDate">';
							schedule += '<tr>';
							schedule += '<td colspan="3" style="text-align:center;background-color:'+background+';font-size:1vw;padding:2px;color:black">'+today+result.schedules[i].date_name.toUpperCase()+'</td>';
							schedule += '</tr>';
							if (result.schedules[i].schedule_isi != null) {
								var schedule_isi = result.schedules[i].schedule_isi.split(',');
								if (schedule_isi.length > 0) {
									for(var j = 0; j < schedule_isi.length;j++){
										schedule += '<tr>';
										schedule += '<td style="text-align:left;background-color:#7c54cc;font-size:0.9vw">'+schedule_isi[j].split('_')[0]+'</td>';
										schedule += '<td style="text-align:left;background-color:#7c54cc;font-size:0.9vw;cursor:pointer" title="'+schedule_isi[j].split('_')[4]+'">'+schedule_isi[j].split('_')[1]+' - '+schedule_isi[j].split('_')[2]+'</td>';
										schedule += '<td style="text-align:right;background-color:#7c54cc;font-size:0.9vw">'+schedule_isi[j].split('_')[3]+'</td>';
										schedule += '</tr>';
									}
								}else{
									schedule += '<tr>';
									schedule += '<td colspan="3" style="text-align:center;font-size:0.9vw">Tidak Ada Schedule</td>';
									schedule += '</tr>';
								}
							}else{
								schedule += '<tr>';
								schedule += '<td colspan="3" style="text-align:center;font-size:0.9vw">Tidak Ada Schedule</td>';
								schedule += '</tr>';
							}
							schedule += '</table>';
							schedule += '</div>';
						// }
					}

					$('#cont').append(schedule);
				}
			}
		});

	}


</script>
@endsection