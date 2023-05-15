@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    @if($jml_null > 0 && $role_code != 'M')
    <label class="label label-success pull-right"><input type="checkbox" onclick="checkAll(this.checked)">Approve All</label>
    @endif
    <a class="btn btn-info pull-right" style="margin-right: 10px" href="{{url('index/daily_check_fg/print_daily_check/'.$id.'/'.$month)}}">Cetak / Save PDF</a>
    <!-- <small></small> -->
    <!-- <button class="btn btn-primary pull-right" onclick="myFunction()">Print</button> -->
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
  <!-- SELECT2 EXAMPLE -->
  <div class="box box-solid">
      <div class="box-body">
		<table class="table" style="border: 1px solid black;">
			<tbody>
				<tr>
					<td colspan="12" style="border: 1px solid black;"><img width="80px" src="{{ public_path('images/logo_yamaha2.png') }}" alt=""></td>
				</tr>
				<tr>
					<td style="border: 1px solid black;vertical-align: middle;" colspan="12">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Department</td>
					<td style="border: 1px solid black;">{{ $departments }}</td>
					<td rowspan="3" colspan="2" style="border: 1px solid black;padding: 15px;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="3"><center>Checked<br>
						@if($jml_null == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date }}</b>
						@endif<br>
					{{ $foreman }}<br>Foreman</center></td>
					<td style="border: 1px solid black;vertical-align: middle;" rowspan="3"><center>Prepared<br>
						@if($jml_null_leader == 0)
							<b style='color:green'>Approved</b><br>
							<b style='color:green'>{{ $approved_date_leader }}</b>
						@endif<br>
						{{ $leader }}<br>Leader</center></td>
					@if($jml_null > 0 && $role_code != 'M')
					<td rowspan="4" id="approval1" style="border: 1px solid black;vertical-align: middle"><center>Approval</center></td>
					@endif
				</tr>
				<tr>
					<td style="border: 1px solid black;">Product</td>
					<td style="border: 1px solid black;">{{ $product }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;">Month</td>
					<td style="border: 1px solid black;">{{ $monthTitle }}</td>
				</tr>
				<tr>
					<td style="border: 1px solid black;"><center>No.</center></td>
					<td style="border: 1px solid black;"><center>Production Date</center></td>
					<td style="border: 1px solid black;"><center>Check Date</center></td>
					<td style="border: 1px solid black;"><center>Serial Number</center></td>
					<td style="border: 1px solid black;"><center>Condition</center></td>
					<td style="border: 1px solid black;"><center>Keterangan</center></td>
				</tr>
				<?php $no = 1; ?>
				<form role="form" method="post" action="{{url('index/daily_check_fg/approval/'.$id.'/'.$month)}}">
				@foreach($daily_check as $daily_check)
				<tr>
					<td style="border: 1px solid black;"><center>{{ $no }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->production_date }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->check_date }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->serial_number }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->condition }}</center></td>
					<td style="border: 1px solid black;"><center>{{ $daily_check->keterangan }}</center></td>
					@if($jml_null > 0 && $role_code != 'M')
					<td id="approval2" class="head" style="border: 1px solid black;vertical-align: middle">
						<input type="hidden" value="{{csrf_token()}}" name="_token" />
						@if($daily_check->approval == Null)
						<label class="label label-success"><input type="checkbox" id="customCheck" name="approve[]" value="{{ $daily_check->id_daily_check }}">Approve</label>
						@endif
					</td>
					@endif
				</tr>
				<?php $no++ ?>
				@endforeach
				@if($jml_null > 0 && $role_code != 'M')
				<tr class="head" id="approval3">
					<td style="border: 1px solid black;" align="right" colspan="7"><button class="btn btn-success" type="submit">Approve</button></td>
				</tr>
				@endif
				</form>
			</tbody>
		</table>
	</div>
  </div>
  @endsection
<style>
.table {
  border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
}
#table2 {
	border: 1px solid black;
  border-collapse: collapse;
  font-family:"Arial";
  padding: 5px;
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
	function checkAll(isChecked){
		if(isChecked){
			$(':checkbox').attr('checked',true);
		}
		else{
			$(':checkbox').attr('checked',false);
		}
	}
</script>
