@extends('layouts.master')
@section('header')
<section class="content-header">
  <h1>
    Approval {{ $activity_name }} - {{ $leader }}
    <a style="margin-right: 10px" class="btn btn-info pull-right" href="{{url('index/production_audit/print_audit/'.$id.'/'.$date.'/'.$product.'/'.$proses)}}">Cetak / Save PDF</a>
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
						<td style="border: 1px solid black;" colspan="7">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
					</tr>
					<tr>
						<td rowspan="5" style="width: 20px;vertical-align: middle;"><center><img width="175px" src="{{ asset('images/logo_yamaha2.png') }}" alt=""></center></td>
					</tr>
					<tr>
						<td style="border: 1px solid black;">Department</td>
						<td style="border: 1px solid black;">{{ $departments }}</td>
						<td rowspan="4" colspan="3" style="padding: 15px;border: 1px solid black;vertical-align: middle;"><center><b>{{ $activity_name }}</b></center></td>
						<td rowspan="4" style="border: 1px solid black;vertical-align: middle;"><center>Mengetahui<br>
							@if($jml_null == 0)
								<b style='color:green'>Approved</b><br>
								<b style='color:green'>{{ $approved_date }}</b>
							@endif
							<br>{{ $foreman }}
							<br>Foreman</center>
						</td>
						@if($jml_null > 0 && $role_code != 'M')
						<td rowspan="5" id="approval1"><center>Approval</center></td>
						@endif
					</tr>
					<tr>
						<td>Product</td>
						<td>{{ $product }}</td>
					</tr>
					<tr>
						<td>Proses</td>
						<td>{{ $proses }}</td>
					</tr>
					<tr>
						<td>Date</td>
						<td>{{ $date_audit }}</td>
					</tr>
					<tr>
						<td>Point Check</td>
						<td>Cara Cek</td>
						<td>Date</td>
						<td>Foto Kondisi Aktual</td>
						<td>Kondisi</td>
						<td>PIC</td>
						<td>Auditor</td>
					</tr>
					<form role="form" method="post" action="{{url('index/production_audit/approval/'.$id)}}">
					@foreach($production_audit as $production_audit)
					<tr>
						<td><?php echo $production_audit->point_check ?></td>
						<td><?php echo $production_audit->cara_cek ?></td>
						<td><?php echo $production_audit->date ?></td>
						<td><img width="200px" src="{{ url('/data_file/'.$production_audit->foto_kondisi_aktual) }}"></td>
						<td>@if($production_audit->kondisi == "Good")
				              <label class="label label-success">{{$production_audit->kondisi}}</label>
				            @else
				              <label class="label label-danger">{{$production_audit->kondisi}}</label>
				            @endif
			        	</td>
						<td>{{ $production_audit->pic_name }}</td>
						<td>{{ $production_audit->auditor_name }}</td>
						@if($jml_null > 0 && $role_code != 'M')
						<td id="approval2">
							<input type="hidden" value="{{csrf_token()}}" name="_token" />
							@if($production_audit->approval == Null)
							<label class="label label-success"><input type="checkbox" class="minimal-red" name="approve[]" value="{{ $production_audit->id_production_audit }}">
							    Approve</label>
							@endif
						</td>
						@endif
					</tr>
					@endforeach
					@if($jml_null > 0 && $role_code != 'M')
					<tr id="approval3">
						<td align="right" colspan="8"><button class="btn btn-success" type="submit">Approve</button></td>
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
