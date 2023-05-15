@extends('layouts.master')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">

</style>
@stop
@section('header')
<section class="content-header">
	<h1>
		{{ $title }}<span class="text-purple"> {{ $title_jp}}</span>
	</h1>
</section>
@stop
@section('content')
<section class="content">
	<div class="row">
		<div class="col-xs-4" style="text-align: center;color: red;">
			<span style="font-size: 3vw; color: red;"><i class="fa fa-angle-double-down"></i> Report <i class="fa fa-angle-double-down"></i></span>
			
			<a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/machine/part_list") }}">
				Machine Part List
			</a>
		</div>
		<div class="col-xs-4" style="text-align: center; color: red;">
            <span style="font-size: 3vw;"><i class="fa fa-angle-double-down"></i> Monitoring <i class="fa fa-angle-double-down"></i></span>

            <a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/machine/part_graph") }}">
            	Machine Part Graph
            </a>

            <a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="http://10.109.52.7/zed/dashboard/awal" target="_blank">
            	Overall Equipment Efficiency (OEE)
            	<!-- (稼働率) (OEE) -->
            </a>

            <a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("machinery_monitoring?mesin=") }}">
            	Machinery Monitoring 
            	<!-- (機械監視) -->
            </a>

            <a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="http://10.109.52.7/mtnc/login/log" target="_blank">
            	Planned Activity Finding
            </a>

            <a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/machine_report/list") }}">
            	Machine Breakdown Report
            </a>

            <a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/machine_report/graph") }}">
            	Machine Breakdown Graph
            </a>

            <a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/operator/position") }}">
            	Maintenance Operator Location
            	<!-- (保全班作業者の位置) -->
            </a>

            <a style="font-size: 2vw; border-color: red;" class="btn btn-default btn-block" href="{{ url("index/maintenance/machine_report/report") }}">
            	Maintenance Trouble Report
            </a>

        </div>
    </div>
</section>
@endsection
@section('scripts')
<script src="{{ url('js/jquery.gritter.min.js') }}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	jQuery(document).ready(function() {
		$('body').toggleClass("sidebar-collapse");
	});
</script>
@endsection