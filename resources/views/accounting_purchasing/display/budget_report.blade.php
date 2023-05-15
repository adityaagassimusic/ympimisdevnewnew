@extends('layouts.display')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
		<div>
			<center>
				<span style="font-size: 3vw; text-align: center; position: fixed; top: 45%; left: 42.5%;"><i class="fa fa-spin fa-hourglass-half"></i>&nbsp;&nbsp;&nbsp;Loading ...</span>
			</center>
		</div>
	</div>
	<div class="row">
		<div id="chart_title" class="col-xs-5" style="background-color: #673ab7;">
			<center>
				<span style="color: white; font-size: 2vw; font-weight: bold;" id="title_text"></span>
			</center>
		</div>
		<div class="col-xs-2" style="padding-top: 0.25%;">
			<div class="input-group date">
				<div class="input-group-addon bg-purple">
					<i class="fa fa-calendar-o" style="color:white"></i>
				</div>
				<select class="form-control select2" onchange="fetchChart()" name="fy" id='fy' data-placeholder="Select Fiscal Year" style="width: 100%;">
					<!-- <option value="">Select Fiscal Year</option> -->
					<option value="FY200">FY200</option>
					<option value="FY199">FY199</option>
					<option value="FY198">FY198</option>
					<option value="FY197">FY197</option>
				</select>
			</div>
		</div>

		<div class="col-xs-2" style="padding-top: 0.25%;">
            <div class="input-group">
              <div class="input-group-addon bg-blue">
                <i class="fa fa-search"></i>
              </div>
              <select class="form-control select2" onchange="fetchChart()" id="category" data-placeholder="Select Category" style="border-color: #605ca8;width: 100%;">
                  <!-- <option value=""></option> -->
                  <option value="Expenses">Expenses</option>
                  <option value="Fixed Asset">Fixed Asset</option>
                </select>
            </div>
        </div>

        <?php if(str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'ACC') || Auth::user()->role_code == "M" || Auth::user()->role_code == "JPN" || Auth::user()->role_code == "D") { ?>
            <div class="col-md-3" style="padding-top: 0.25%;">
              <div class="input-group">
                <div class="input-group-addon bg-blue">
                  <i class="fa fa-search"></i>
                </div>
                <select class="form-control select2" onchange="fetchChart()" id="department" data-placeholder="Select Department" style="border-color: #605ca8;width: 100%;">
                    <option value=""></option>
                    @foreach($department as $dept)
                      <option value="{{$dept->department}}">{{$dept->department}}</option>
                    @endforeach
                  </select>
              </div>
          </div>
            <?php } else { ?>
              <input type="hidden" name="department" id='department' data-placeholder="Select Department" style="width: 100%;" value="{{$emp_dept->department}}">
            <?php } ?>
		
		<div class="col-xs-12" id="chart1" style="margin-top: 1%; height: 40vh;"></div>
		<div class="col-xs-12" id="chart2" style="margin-top: 1%; height: 40vh;"></div>

		<div class="col-xs-12" style="margin-top: 1%;">
			<div class="col-xs-12">

				<div class="col-xs-2" style="padding-top: 1%;margin: 0;">
					<div class="input-group date">
						<input type="text" class="form-control pull-right" id="bulan" name="bulan" placeholder="Select Month" onchange="fetchTable()">
					</div>
				</div>
			</div>
			<div class="box box-solid">
				<div class="box-body">
					<div class="col-xs-6" style="padding-left: 0;">
						<center>
							<span style="font-weight: bold; font-size: 1.2vw;" id="title_text_detail"></span>
						</center>
						<table id="tablebudget" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgb(96, 92, 168); color: white;">
								<tr>
									<th style="width: 0.2%; text-align: center;vertical-align: middle;">Account</th>
									<th style="width: 0.1%; text-align: center;vertical-align: middle;">Budget ($)</th>
									<!-- <th style="width: 0.1%; text-align: center;vertical-align: middle;" rowspan="2">Forecast</th> -->
									<th style="width: 0.1%; text-align: center;vertical-align: middle;">Actual ($)</th>
									<!-- <th style="width: 0.2%; text-align: center;vertical-align: middle;" colspan="2">Diff (Budget)</th> -->
									<th style="width: 0.2%; text-align: center;vertical-align: middle;">Diff ($)</th>
									<th style="width: 0.2%; text-align: center;vertical-align: middle;">Percentage (%)</th>
									<th style="width: 0.05%; text-align: center;vertical-align: middle;">Action</th>
							</thead>
							<tbody id="tablebudgetBody">
							</tbody>
							<tfoot id="tablebudgetFoot" style="background-color: rgb(252, 248, 227);">
								<tr>
									<th style="text-align: center;">Total</th>
									<th style="text-align: right;" id="total_budget"></th>
									<th style="text-align: right;" id="total_budget_actual"></th>
									<th style="text-align: right;" id="total_budget_diff"></th>
									<th style="text-align: right;" id="total_budget_percentage"></th>
									<th style="text-align: right;"></th>
								</tr>
							</tfoot>
						</table>	
					</div>
					<div class="col-xs-6" style="padding-right: 0;">
						<center>
							<span style="font-weight: bold; font-size: 1.2vw;" id="title_text_detail_acc"></span>
						</center>
						<table id="tableaccbudget" class="table table-bordered table-striped table-hover">
							<thead style="background-color: rgb(96, 92, 168); color: white;">
								<tr>
									<th style="width: 0.2%; text-align: center;vertical-align: middle;">Account</th>
									<th style="width: 0.1%; text-align: center;vertical-align: middle;">Budget ($)</th>
									<!-- <th style="width: 0.1%; text-align: center;vertical-align: middle;" rowspan="2">Forecast</th> -->
									<th style="width: 0.1%; text-align: center;vertical-align: middle;">Actual ($)</th>
									<!-- <th style="width: 0.2%; text-align: center;vertical-align: middle;" colspan="2">Diff (Budget)</th> -->
									<th style="width: 0.2%; text-align: center;vertical-align: middle;">Diff ($)</th>
									<th style="width: 0.2%; text-align: center;vertical-align: middle;">Percentage (%)</th>
							</thead>
							<tbody id="tableaccbudgetBody">
							</tbody>
							<tfoot id="tableaccbudgetFoot" style="background-color: rgb(252, 248, 227);">
								<tr>
									<th style="text-align: center;">Total</th>
									<th style="text-align: right;" id="total_budget_acc"></th>
									<th style="text-align: right;" id="total_budget_acc_actual"></th>
									<th style="text-align: right;" id="total_budget_acc_diff"></th>
									<th style="text-align: right;" id="total_budget_acc_percentage"></th>
								</tr>
							</tfoot>
						</table>	
					</div>
				</div>
			</div>
		</div>
	</div>
</section>



<div class="modal fade" id="modalCatatan" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title" id="myModalLabel">Tambahkan Catatan</h4>
            </div>
            <div class="modal-body">
              <div class="box-body">
                <input type="hidden" value="{{csrf_token()}}" name="_token" />
                <div class="row">
                  <div class="col-xs-12">
                    <label for="catatan">Catatan Budget</label>
                    <textarea class="form-control" style="width: 100%;height: 250px;" id="catatan" name="catatan" placeholder="Catatan Terkait Budget" required></textarea>
                    </select>
                  </div>
                  
                </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
            <input type="hidden" id="dept">
            <input type="hidden" id="account">
            <input type="hidden" id="monthnote">
            <button type="button" id="btn_note_submit" onclick="submit_note()" class="btn btn-success" data-dismiss="modal"> Submit</button>
          </div>
        </div>
    </div>
  </div>

<div class="modal fade" id="modalDetail">
	<div class="modal-dialog" style="width: 40%;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="modalDetailTitle"></h4>
				<div class="modal-body table-responsive no-padding" style="min-height: 100px">
					<table class="table table-hover table-bordered table-striped" id="tableDetail">
						<thead style="background-color: rgba(126,86,134,.7);">
							<tr>
								<th style="width: 10%; text-align: center; vertical-align: middle;" colspan="5">Month</th>
							</tr>
							<tr>
								<th style="width: 5%; text-align: center; vertical-align: middle;">Account</th>
								<th style="width: 1%; text-align: center; vertical-align: middle;">Budget</th>
								<th style="width: 1%; text-align: center; vertical-align: middle;">Forecast</th>
								<th style="width: 1%; text-align: center; vertical-align: middle;">Actual</th>
								<th style="width: 1%; text-align: center; vertical-align: middle;">Diff</th>
								<th style="width: 1%; text-align: center; vertical-align: middle;">Presentage</th>
							</tr>
						</thead>
						<tbody id="BodyDetail">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('scripts')
<script src="{{ url("js/jquery.gritter.min.js") }}"></script>
<script src="{{ url("js/highcharts.js")}}"></script>
<script src="{{ url("js/highcharts-3d.js")}}"></script>
<script src="{{ url("js/exporting.js")}}"></script>
<script src="{{ url("js/export-data.js")}}"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		fetchChart();

		$('#bulan').datepicker({
	        format: "yyyy-mm",
	        startView: "months", 
	        minViewMode: "months",
	        autoclose: true
	      });

		$('.select2').select2({
			allowClear : true,
		});
	});

	var budget_global = [];
	var sales_global = [];

	$.date = function(dateObject) {
		var d = new Date(dateObject);
		var day = d.getDate();
		var month = d.getMonth() + 1;
		var year = d.getFullYear();
		if (day < 10) {
			day = "0" + day;
		}
		if (month < 10) {
			month = "0" + month;
		}
		var date = year + "-" + month + "-" + day;

		return date;
	};

	function fetchTable(){
		var bulan = $('#bulan').val();
		var fy = $('#fy').val();
		var category = $('#category').val();
		var department = $('#department').val();
		
		var data = {
			bulan:bulan,
			fy:fy,
			category:category,
			department:department
		}

		$('#loading').show();

		$.get('{{ url("fetch/budget/report/detail") }}', data, function(result, status, xhr) {
			if(result.status){

				$('#loading').hide();

				$('#title_text_detail').text('Detail Budget Report '+result.title);
				$('#title_text_detail_acc').text('Detail Budget Report Accumulation (Apr - '+result.title+')');

				var forecast = [];
				var total_budget = 0;
				var total_budget_actual = 0;
				var total_budget_diff = 0;
				var total_budget_percentage = 0;

				var tablebudgetBody = "";
				$('#tablebudgetBody').html("");

				$.each(result.cost_center, function(key, value) {
					tablebudgetBody += '<tr>';
					tablebudgetBody += '<td style="text-align: left;">'+value.account_name+'</td>';

					var budget = 0;
					var bulan = result.bulan.split('-');

					$.each(result.resume_forecast_cost_center, function(key3, value3){
						if(value3.account_name == value.account_name) {
							if (bulan[1] == "04") {
								budget = value3.apr_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "05") {
								budget = value3.may_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "06") {
								budget = value3.jun_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "07") {
								budget = value3.jul_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "08") {
								budget = value3.aug_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "09") {
								budget = value3.sep_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "10") {
								budget = value3.oct_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "11") {
								budget = value3.nov_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "12") {
								budget = value3.dec_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "01") {
								budget = value3.jan_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "02") {
								budget = value3.feb_simulasi;
								total_budget += budget;
							}else if (bulan[1] == "03") {
								budget = value3.mar_simulasi;
								total_budget += budget;
							}
						}

					});

					tablebudgetBody += '<td style="text-align: right;">'+budget.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+'</td>';

					var amount = 0;

					$.each(result.data, function(key2, value2) {
						if(result.bulan == value2.bulan && value2.account_name == value.account_name) {
							amount = value2.amount;
							total_budget_actual += amount;
						}
					});

					tablebudgetBody += '<td style="text-align: right;">'+amount.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+'</td>';

					var hasil = amount - budget;
					total_budget_diff += hasil;
					var persen = amount/budget*100;

					var back_color = "";

					if (hasil <= 0) {
						if (hasil < -1000 && (persen < 90 || persen > 110)) {
							back_color = 'background-color:red;color:white';
						}else{
							back_color = 'color:green';
						}

						tablebudgetBody += '<td style="text-align: right;'+back_color+'">'+hasil.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+'</td>';
					}else{
						if (hasil > 1000 && (persen < 90 || persen > 110)) {
							back_color = 'background-color:red;color:white';
						}else{
							back_color = 'color:red';
						}
						tablebudgetBody += '<td style="text-align: right;'+back_color+'">'+hasil.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+'</td>';
					}


					if (persen > 100) {
						if (hasil > 1000 && (persen < 90 || persen > 110)) {
							back_color = 'background-color:red;color:white';
						}else{
							back_color = 'color:red';
						}
						tablebudgetBody += '<td style="text-align: right;'+back_color+'">'+persen.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+' %</td>';
					}
					else{
						if (hasil < -1000 && (persen < 90 || persen > 110)) {
							back_color = 'background-color:red;color:white';
						}else{
							back_color = 'color:green';
						}

						tablebudgetBody += '<td style="text-align: right;'+back_color+'">'+persen.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+' %</td>';
					}

					var stat = 0;

					$.each(result.cost_center_note, function(key4, value4) {
						if(result.bulan == value4.month_date && value4.account_name == value.account_name && (value4.department == department || department == '' || department == null) )  {
								stat = 1;
						}
					});

					if (stat == 1) {
						tablebudgetBody += '<td style="text-align: center;"><a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-success" class="btn btn-green btn-sm" onClick="addNote(\''+department+'\',\''+value.account_name+'\',\''+result.bulan+'\')"><i class="fa fa-edit"></i></a></td>';	
					}else{
						tablebudgetBody += '<td style="text-align: center;"><a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" class="btn btn-primary btn-sm" onClick="addNote(\''+department+'\',\''+value.account_name+'\',\''+result.bulan+'\')"><i class="fa fa-edit"></i></a></td>';
					}

					


					tablebudgetBody += '</tr>';

				});

				var persen_total = total_budget_actual/total_budget*100;

				$('#total_budget').text(''+total_budget.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }));	
				$('#total_budget_actual').text(''+total_budget_actual.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }));	
				$('#total_budget_diff').text(''+total_budget_diff.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }));
				$('#total_budget_percentage').text(persen_total.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+' %');

				$('#tablebudgetBody').append(tablebudgetBody);

				var total_budget_acc = 0;
				var total_budget_acc_actual = 0;
				var total_budget_acc_diff = 0;
				var total_budget_acc_percentage = 0;

				var tableaccbudgetBody = "";
				$('#tableaccbudgetBody').html("");

				$.each(result.cost_center, function(key, value) {
					tableaccbudgetBody += '<tr>';
					tableaccbudgetBody += '<td style="text-align: left;">'+value.account_name+'</td>';

					var budget_acc = 0;
					var bulan = result.bulan.split('-');

					$.each(result.resume_forecast_cost_center, function(key3, value3){
						if(value3.account_name == value.account_name) {
							if (bulan[1] == "04") {
								budget_acc = value3.apr_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "05") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "06") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi + value3.jun_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "07") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi + value3.jun_simulasi + value3.jul_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "08") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi + value3.jun_simulasi + value3.jul_simulasi + value3.aug_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "09") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi + value3.jun_simulasi + value3.jul_simulasi + value3.aug_simulasi + value3.sep_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "10") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi + value3.jun_simulasi + value3.jul_simulasi + value3.aug_simulasi + value3.sep_simulasi + value3.oct_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "11") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi + value3.jun_simulasi + value3.jul_simulasi + value3.aug_simulasi + value3.sep_simulasi + value3.oct_simulasi + value3.nov_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "12") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi + value3.jun_simulasi + value3.jul_simulasi + value3.aug_simulasi + value3.sep_simulasi + value3.oct_simulasi + value3.nov_simulasi + value3.dec_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "01") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi + value3.jun_simulasi + value3.jul_simulasi + value3.aug_simulasi + value3.sep_simulasi + value3.oct_simulasi + value3.nov_simulasi + value3.dec_simulasi + value3.jan_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "02") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi + value3.jun_simulasi + value3.jul_simulasi + value3.aug_simulasi + value3.sep_simulasi + value3.oct_simulasi + value3.nov_simulasi + value3.dec_simulasi + value3.jan_simulasi + value3.feb_simulasi;
								total_budget_acc += budget_acc;
							}else if (bulan[1] == "03") {
								budget_acc = value3.apr_simulasi + value3.may_simulasi + value3.jun_simulasi + value3.jul_simulasi + value3.aug_simulasi + value3.sep_simulasi + value3.oct_simulasi + value3.nov_simulasi + value3.dec_simulasi + value3.jan_simulasi + value3.feb_simulasi + value3.mar_simulasi;
								total_budget_acc += budget_acc;
							}
						}

					});
					tableaccbudgetBody += '<td style="text-align: right;">'+budget_acc.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+'</td>';

					var amount_acc = 0;
					$.each(result.data, function(key2, value2) {
						var bulan_now = result.bulan.split('-');
						var bulan_acc = value2.bulan.split('-');

						if( parseInt(bulan_now[1]) >= parseInt(bulan_acc[1])) { //1 bulan
							if ( value2.account_name == value.account_name) {
								amount_acc += value2.amount;							
							}
						}
					});	

					total_budget_acc_actual += amount_acc;

					tableaccbudgetBody += '<td style="text-align: right;">'+amount_acc.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+'</td>';
					var hasil_acc = amount_acc - budget_acc;
					total_budget_acc_diff += hasil_acc;

					var persen_acc = amount_acc/budget_acc*100;

					if (hasil_acc <= 0) {
						tableaccbudgetBody += '<td style="text-align: right;color:green">'+hasil_acc.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+'</td>';
					}else{
						tableaccbudgetBody += '<td style="text-align: right;color:red">'+hasil_acc.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+'</td>';
					}
					if (persen_acc > 100) {
						tableaccbudgetBody += '<td style="text-align: right;color:red">'+persen_acc.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+' %</td>';
					}
					else{
						tableaccbudgetBody += '<td style="text-align: right;color:green">'+persen_acc.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+' %</td>';
					}
					tableaccbudgetBody += '</tr>';

				});

				var persen_total_acc = total_budget_acc_actual/total_budget_acc*100;

				$('#total_budget_acc').text(''+total_budget_acc.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }));	
				$('#total_budget_acc_actual').text(''+total_budget_acc_actual.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }));	
				$('#total_budget_acc_diff').text(''+total_budget_acc_diff.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }));
				$('#total_budget_acc_percentage').text(persen_total_acc.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 })+' %');

				$('#tableaccbudgetBody').append(tableaccbudgetBody);	


			}else{
				alert('Attempt to retrieve data failed');				
			}
		});		
	}

	function fetchChart(){
		var fy = $('#fy').val();
		var category = $('#category').val();
		var department = $('#department').val();

		var data = {
			fy:fy,
			category:category,
			department:department
		}

		$('#loading').show();

		$.get('{{ url("fetch/budget/report") }}', data, function(result, status, xhr) {
			if(result.status){
				$('#loading').hide();

				fetchTable();
				$('#title_text').text('BUDGET REPORT ON ' + result.fy);
				var h = $('#chart_title').height();
				$('.select').css('height', h);

				var xCategories = [];
				var forecast = [];
				// var budget = [];
				// var sales = [];

    			// var budget_plan = 0;
				// var akum_budget_plan = [];
          		var forecast_plan = 0;
				var akum_forecast_plan = [];
				var actual = [];
				var actual_fix = [];
				var value_actual = 0;
				var akum_actual = [];

				// $.each(result.resume_budget, function(key, value){
		  //           budget.push(value.apr_budget/1000,value.may_budget/1000,value.jun_budget/1000,value.jul_budget/1000,value.aug_budget/1000,value.sep_budget/1000,value.oct_budget/1000,value.nov_budget/1000,value.dec_budget/1000,value.jan_budget/1000,value.feb_budget/1000,value.mar_budget/1000);
				// });

		  //       $.each(budget, function(key, value) {
		  //           budget_plan += value;
		  //           akum_budget_plan.push(budget_plan);
		  //       })

				$.each(result.resume_forecast, function(key, value){
		            forecast.push(value.apr_simulasi/1000,value.may_simulasi/1000,value.jun_simulasi/1000,value.jul_simulasi/1000,value.aug_simulasi/1000,value.sep_simulasi/1000,value.oct_simulasi/1000,value.nov_simulasi/1000,value.dec_simulasi/1000,value.jan_simulasi/1000,value.feb_simulasi/1000,value.mar_simulasi/1000);
				});

				$.each(forecast, function(key, value) {
		            forecast_plan += value;
		            akum_forecast_plan.push(forecast_plan);
		        })

				for (var i = 0; i < result.months.length; i++) {
					xCategories.push(result.months[i].text);					
				}	

				$.each(result.act, function(key, value) {
					// actual += value.amount;
					actual_fix.push(value.amount/1000);
					value_actual += value.amount/1000;
					akum_actual.push(value_actual);
		        })				



				Highcharts.chart('chart1', {
					chart: {
						type: 'column',
						options3d: {
							enabled: true,
							alpha: 0,
							beta: 0,
							viewDistance: 20,
							depth: 80
						},
						backgroundColor	: null
					},
					title: {
						text: ''
					},
					credits: {
						enabled: false
					},
					legend:{
						enabled: true
					},
					xAxis: {
						categories: xCategories,
					},
					yAxis: {
						title: {
							text: 'x 1000 USD'
						}
					},
					tooltip: {
              headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
              pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0"><b>{point.y:.1f}k</b></td></tr>',
              footerFormat: '</table>',
              shared: true,
              useHTML: true
            },
					plotOptions: {
						column: {
							pointPadding: 0.05,
							groupPadding: 0.1,
							borderWidth: 0
						},
						series: {
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							// cursor : 'pointer',
							point: {
								events: {
									click: function (event) {
										// showDetail(event.point.category);

									}
								}
							},
						},
					},

					series: [{
						name: 'Budget ($)',
						data: forecast,
						color: '#ffeb3b'
					},
					// ,{
					// 	name: 'Forecast ($)',
					// 	data: forecast,
					// 	color: '#2b908f'

					// }
					{
						name: 'Actual ($)',
						data: actual_fix,
						color: '#90ee7e'
					}]
				});

				Highcharts.chart('chart2', {
					chart: {
						type: 'areaspline',
						backgroundColor	: null
					},
					title: {
						text: 'BUDGET REPORT Accumulation',
						style: {
							fontSize: '24px',
							fontWeight: 'bold'
						}
					},
					yAxis: {
						title: {
							text: 'x 1000 USD'
						}
					},
					xAxis: {
						categories: xCategories,
					},
					tooltip: {
              headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
              pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0"><b>{point.y:.1f}k</b></td></tr>',
              footerFormat: '</table>',
              shared: true,
              useHTML: true
          },
					credits: {
						enabled:false
					},
					legend : {
						enabled: true,
					},
					plotOptions: {
						depth: 100,
						series:{
							dataLabels: {
								enabled: true,
								format: '{point.y:.0f}',
								style:{
									fontSize: '12px;'
								}
							},
							animation: false,
							cursor: 'pointer'
						},
						areaspline: {
							fillOpacity: 0.5
						}
					},
					series: [{
						name:'Budget ($)',
						data: akum_forecast_plan,
						color: '#ffeb3b'
					}
					// ,{
					// 	name:'Forecast (USD)',
					// 	data: akum_forecast_plan,
					// 	color: '#2b908f'
					// }
					,{
						name:'Actual ($)',
						data: akum_actual,
						color: '#90ee7e'
					}]

				});	

				$('#loading').hide();
			}else{
				$('#loading').hide();
				alert('Attempt to retrieve data failed');				
			}
		});		
}

 function addNote(dept,account,bulan){
    $('#modalCatatan').modal("show");
    $("#dept").val(dept);
    if (dept == "" || dept == null) {
    	$("#btn_note_submit").hide();
    }else{
    	$("#btn_note_submit").show();
    }
    $("#account").val(account);
    $("#monthnote").val(bulan);

    var data = {
      dept:dept,
      account:account,
      bulan:bulan
    };

    $.get('{{ url("index/budget/catatan") }}', data, function(result, status, xhr){  
    	if (result.note == "" || result.note == null) {
    		$("#catatan").val("");
    	}else{
    		if (dept == "" || dept == null) {
    			var text = "";

    			$.each(result.note, function(key, value) {
    				text += value.department+' : \n'+value.note+'\n\n';
		      })				
    			
    			$("#catatan").val(text);
    		
    		}
    		else{
      		$("#catatan").val(result.note.note);
    		}
    	}
    });
  }

  function submit_note() {
      if($("#catatan").val() == ""){
        openErrorGritter('Error!', 'Catatan Budget Harus Diisi.');
        return false;
      }

      var data = {
        dept: $("#dept").val(),
        account: $("#account").val(),
        monthnote: $("#monthnote").val(),
        catatan : $("#catatan").val()
      };

      $.post('{{ url("post/budget/catatan") }}', data, function(result, status, xhr){
        if (result.status == true) {
          openSuccessGritter("Success","Catatan Berhasil di Update");
        } else {
          openErrorGritter("Error",result.message);
        }
      })
    }

// function showDetail(category) {

// 	$('#BodyDetail').html("");
// 	var tableData = '';
// 	var sumSalesQty = 0;
// 	var sumSalesAmount = 0;
	
// 	$.each(sales_global, function(key, value){
// 		if(category == value.month_text){
// 			tableData += '<tr>';
// 			tableData += '<td style="width: 3%; text-align: center;">'+value.month+'</td>';
// 			tableData += '<td style="width: 3%;">'+value.hpl+'</td>';
// 			tableData += '<td style="width: 1%; text-align: right;">'+value.quantity+'</td>';
// 			tableData += '<td style="width: 1%; text-align: right;">'+value.amount.toFixed(0)+' K</td>';
// 			sumSalesQty += value.quantity;
// 			sumSalesAmount += value.amount;
// 		}		
// 	});
// 	tableData += '<tr style="background-color: rgb(252, 248, 227);">';
// 	tableData += '<td style="width: 3%; text-align: center;" colspan="2">TOTAL</td>';
// 	tableData += '<td style="width: 3%; text-align: right;">'+sumSalesQty+'</td>';
// 	tableData += '<td style="width: 3%; text-align: right;">'+sumSalesAmount.toFixed(0)+' K</td>';
// 	tableData += '</tr>';

// 	$('#BodyDetail').append(tableData);

// 	$('#modalDetail').modal('show');
// }

Highcharts.createElement('link', {
	href: '{{ url("fonts/UnicaOne.css")}}',
	rel: 'stylesheet',
	type: 'text/css'
}, null, document.getElementsByTagName('head')[0]);

Highcharts.theme = {
	colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066',
	'#eeaaee', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
	chart: {
		backgroundColor: {
			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			stops: [
			[0, '#2a2a2b'],
			[1, '#3e3e40']
			]
		},
		style: {
			fontFamily: 'sans-serif'
		},
		plotBorderColor: '#606063'
	},
	title: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase',
			fontSize: '20px'
		}
	},
	subtitle: {
		style: {
			color: '#E0E0E3',
			textTransform: 'uppercase'
		}
	},
	xAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		title: {
			style: {
				color: '#A0A0A3'

			}
		}
	},
	yAxis: {
		gridLineColor: '#707073',
		labels: {
			style: {
				color: '#E0E0E3'
			}
		},
		lineColor: '#707073',
		minorGridLineColor: '#505053',
		tickColor: '#707073',
		tickWidth: 1,
		title: {
			style: {
				color: '#A0A0A3'
			}
		}
	},
	tooltip: {
		backgroundColor: 'rgba(0, 0, 0, 0.85)',
		style: {
			color: '#F0F0F0'
		}
	},
	plotOptions: {
		series: {
			dataLabels: {
				color: 'white'
			},
			marker: {
				lineColor: '#333'
			}
		},
		boxplot: {
			fillColor: '#505053'
		},
		candlestick: {
			lineColor: 'white'
		},
		errorbar: {
			color: 'white'
		}
	},
	legend: {
		itemStyle: {
			color: '#E0E0E3'
		},
		itemHoverStyle: {
			color: '#FFF'
		},
		itemHiddenStyle: {
			color: '#606063'
		}
	},
	credits: {
		style: {
			color: '#666'
		}
	},
	labels: {
		style: {
			color: '#707073'
		}
	},

	drilldown: {
		activeAxisLabelStyle: {
			color: '#F0F0F3'
		},
		activeDataLabelStyle: {
			color: '#F0F0F3'
		}
	},

	navigation: {
		buttonOptions: {
			symbolStroke: '#DDDDDD',
			theme: {
				fill: '#505053'
			}
		}
	},

	rangeSelector: {
		buttonTheme: {
			fill: '#505053',
			stroke: '#000000',
			style: {
				color: '#CCC'
			},
			states: {
				hover: {
					fill: '#707073',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				},
				select: {
					fill: '#000003',
					stroke: '#000000',
					style: {
						color: 'white'
					}
				}
			}
		},
		inputBoxBorderColor: '#505053',
		inputStyle: {
			backgroundColor: '#333',
			color: 'silver'
		},
		labelStyle: {
			color: 'silver'
		}
	},

	navigator: {
		handles: {
			backgroundColor: '#666',
			borderColor: '#AAA'
		},
		outlineColor: '#CCC',
		maskFill: 'rgba(255,255,255,0.1)',
		series: {
			color: '#7798BF',
			lineColor: '#A6C7ED'
		},
		xAxis: {
			gridLineColor: '#505053'
		}
	},

	scrollbar: {
		barBackgroundColor: '#808083',
		barBorderColor: '#808083',
		buttonArrowColor: '#CCC',
		buttonBackgroundColor: '#606063',
		buttonBorderColor: '#606063',
		rifleColor: '#FFF',
		trackBackgroundColor: '#404043',
		trackBorderColor: '#404043'
	},

	legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
	background2: '#505053',
	dataLabelsColor: '#B0B0B3',
	textColor: '#C0C0C0',
	contrastTextColor: '#F0F0F3',
	maskColor: 'rgba(255,255,255,0.3)'
};
Highcharts.setOptions(Highcharts.theme);


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
    time: '2000'
  });
}

</script>
@endsection