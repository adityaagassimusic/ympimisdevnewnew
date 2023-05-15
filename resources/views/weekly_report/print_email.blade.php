@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    <!-- <a style="margin-right: 10px" class="btn btn-info pull-right" href="{{url('index/weekly_report/print_weekly_report/'.$id.'/'.$month)}}">Cetak / Save PDF</a> -->
  </h1>
  <ol class="breadcrumb">
  </ol>
</section>
<style type="text/css">
	@media print {
	.table {-webkit-print-color-adjust: exact;}
	#approval1 {
	    display: none;
	  }
	  #approval2 {
	    display: none;
	  }
	  #approval3 {
	    display: none;
	  }
</style>
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
	@if (session('error'))
		<div class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4> Warning!</h4>
			{{ session('error') }}
		</div>   
	@endif
  <div class="box box-solid">
      <div class="box-body">
      	<table class="table">
			<tbody>
				<tr>
					<td colspan="10" style="padding-top: 0px;padding-bottom: 0px;border: 1px solid black">
						<img style="width: 80px" src="{{ asset('images/logo_yamaha2.png') }}" alt="">
					</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;padding-top: 0px;padding-bottom: 0px;" colspan="10" class="head">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Department</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($departments) }}</td>
					<td class="head" rowspan="3" colspan="2" style="padding: 15px;vertical-align: middle"><center><b>{{ $activity_name }}</b></center></td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif
						<br>
						{{ $foreman }}<br>Foreman</center>
					</td>
					<td class="head" rowspan="3" style="padding-top: 0px;padding-bottom: 0px;"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif
						<br>
						{{ $leader }}<br>Leader</center>
					</td>
					@if($jml_null > 0 && $role_code != 'M')
						<td rowspan="4" id="approval1"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Section</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ strtoupper($subsection) }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">Month</td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;"><center><b>Tanggal</b></center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;"><center><b>Tinjauan 4M</b></center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;"><center><b>Problem / Activity</b></center></td>
					<td class="head" style="padding-top: 0px;padding-bottom: 0px;"><center><b>Report Action</b></center></td>
					<td class="head" colspan="2" style="padding-top: 0px;padding-bottom: 0px;"><center><b>Foto Aktual</b></center></td>
				</tr>
				<form role="form" method="post" action="{{url('index/weekly_report/approval/'.$id.'/'.$month)}}">
				@foreach($weekly_report as $weekly_report)
				<?php $type = [] ?>
				<tr>
					<td class="head" style="vertical-align: middle"><center>{{ $weekly_report->date }}</center></td>
					<td style="vertical-align: middle" class="head"><center><?php $tinjauan = explode(',', $weekly_report->report_type);
						for ($i = 0; $i < count($tinjauan); $i++) {
						 	if($tinjauan[$i] == 1){
						 		$type[] = 'Man';
						 	}elseif ($tinjauan[$i] == 2) {
						 		$type[] = 'Machine';
						 	}elseif ($tinjauan[$i] == 3) {
						 		$type[] = 'Material';
						 	}elseif ($tinjauan[$i] == 4) {
						 		$type[] = 'Method';
						 	}elseif ($tinjauan[$i] == 5) {
						 		$type[] = 'Other';
						 	}
						 }
						 echo implode(' , ', $type);
						 ?></center></td>
					<td style="vertical-align: middle" class="head"><?php echo $weekly_report->problem ?></td>
					<td style="vertical-align: middle" class="head"><?php echo $weekly_report->action ?></td>
					<td style="vertical-align: middle" class="head" colspan="2"><?php echo  $weekly_report->foto_aktual ?></td>
					@if($jml_null > 0 && $role_code != 'M')
					<td id="approval2" style="vertical-align: middle;">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($weekly_report->approval == Null)
						<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="{{ $weekly_report->id_weekly_report }}">
						    Approve</label>
						@endif
					</td>
					@endif
				</tr>
				@endforeach
				@if($jml_null > 0 && $role_code != 'M')
				<tr id="approval3">
					<td align="right" colspan="10"><button class="btn btn-success" type="submit">Approve</button></td>
				</tr>
				@endif
				</form>
			</tbody>
		</table>
	</div>
  </div>
  @endsection
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
  vertical-align:middle;
}
@media print {
	body {-webkit-print-color-adjust: exact;}
}
</style>
<script src="{{ url("bower_components/jquery/dist/jquery.min.js")}}"></script>
<script>
    // setTimeout(function () { window.print(); }, 200);
    function myFunction() {
	  window.print();
	}
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
</script>