@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.content-wrapper {
		background-color: white !important;
		padding-top: 0 !important;
	}
	table.table-bordered{
		border:1px solid black;
	}
	table.table-bordered > thead > tr > th{
		border:1px solid black;
	}
	table.table-bordered > tbody > tr > td{
		border:1px solid rgb(211,211,211);
	}
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<input type="hidden" id="location" value="{{ $location }}">
<input type="hidden" id="default_language" value="{{ $default_language }}">
{{-- <div id="pic"></div> --}}
<section class="content" style="padding-top: 10px;" id="coba">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
	<input type="hidden" id="location" value="{{ $location }}">
	<div id="container" style="padding-top: 50px;"></div>	
</section>

<div class="modal fade" id="modalImage">
	<div class="modal-dialog" style="width: 90%;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">x</span>
				</button>
			</div>
			<div class="modal-body" id="modalImageBody">

			</div>
		</div>
	</div>
</div>

@endsection
@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {
		$('body').addClass('fixed');
		fetchPoint();
		// slide();
		setInterval(slide, 1000*60*15);
	});

	function slide(){

		var dt = new Date();
		var hour = dt.getHours();

		if(hour >= 8 && hour < 15){
			var c;

			if(curr == count-1){
				curr = 1;
				c = 1;
			}else{
				c = curr++;
			}

			for (var i = 1; i <= count; i++) {
				$("[name='"+i+"']").hide();	
			}

			$("[name='"+curr+"']").show();

			if($("[name='inp_"+curr+"']").val() > 0.5){
				window.scrollTo(0, document.body.scrollHeight);	
			}
			if($("[name='inp_"+curr+"']").val() <= 0.5){
				window.scrollTo(0, 0);
			}

			console.log('tampil = '+curr+'; jml = '+count);
		}
	}

	var curr = 1;
	var count = 1;

	$(function() {
		$(document).keydown(function(e) {
			switch(e.which) {
				case 40:
				if(curr == 1){
					curr = count;
				}

				if(curr <= 0){
					curr = 1;
				}

				var c = curr--;

				for (var i = 1; i <= count; i++) {
					$("[name='"+i+"']").hide();	
				}

				$("[name='"+curr+"']").show();


				curr = curr--;

				if($("[name='inp_"+curr+"']").val() > 0.5){
					window.scrollTo(0, document.body.scrollHeight);	
				}
				if($("[name='inp_"+curr+"']").val() <= 0.5){
					window.scrollTo(0, 0);
				}

				console.log('tampil = '+curr+'; jml = '+count);
				break;


				case 39:

				var c;

				if(curr == count-1){
					curr = 1;
					c = 1;
				}else{
					c = curr++;
				}

				for (var i = 1; i <= count; i++) {
					$("[name='"+i+"']").hide();	
				}

				$("[name='"+curr+"']").show();

				if($("[name='inp_"+curr+"']").val() > 0.5){
					window.scrollTo(0, document.body.scrollHeight);	
				}
				if($("[name='inp_"+curr+"']").val() <= 0.5){
					window.scrollTo(0, 0);
				}

				console.log('tampil = '+curr+'; jml = '+count);
				break;


				case 38:

				var c;

				if(curr == count-1){
					curr = 1;
					c = 1;
				}else{
					c = curr++;
				}

				for (var i = 1; i <= count; i++) {
					$("[name='"+i+"']").hide();	
				}

				$("[name='"+curr+"']").show();

				if($("[name='inp_"+curr+"']").val() > 0.5){
					window.scrollTo(0, document.body.scrollHeight);	
				}
				if($("[name='inp_"+curr+"']").val() <= 0.5){
					window.scrollTo(0, 0);
				}

				console.log('tampil = '+curr+'; jml = '+count);
				break;


				case 37:
				if(curr == 1){
					curr = count;
				}

				if(curr <= 0){
					curr = 1;
				}

				var c = curr--;

				for (var i = 1; i <= count; i++) {
					$("[name='"+i+"']").hide();	
				}

				$("[name='"+curr+"']").show();


				curr = curr--;

				if($("[name='inp_"+curr+"']").val() > 0.5){
					window.scrollTo(0, document.body.scrollHeight);	
				}
				if($("[name='inp_"+curr+"']").val() <= 0.5){
					window.scrollTo(0, 0);
				}

				console.log('tampil = '+curr+'; jml = '+count);
				break;

			}
		});
	});

	function modalImage(id, count){
		var he = $('.content-wrapper').height()*0.45+'px';
		var image_body = '';

		$('#modalImageBody').html('');

		image_body += '<center>';
		if(count == 1){
			image_body += '<img src="{{ asset('images/pointing_calls') }}/'+id+'_jp.jpg" style="width:100%;">';
		}
		else{
			image_body += '<img src="{{ asset('images/pointing_calls') }}/'+id+'_jp.jpg" style="max-height: '+he+'; max-width:100%;">';
			image_body += '<img src="{{ asset('images/pointing_calls') }}/'+id+'_id.jpg" style="max-height: '+he+'; max-width:100%;">';
		}
		image_body += '</center>';

		$('#modalImageBody').append(image_body);

		$('#modalImage').modal('show');
	}

	function clearConfirmation(){
		location.reload(true);
	}

	function editPIC(id, point_title, location){
		$('#loading').show();
		var data = {
			id:id,
			point_title:point_title,
			location:location
		}
		$.post('{{ url("edit/general/pointing_call_pic") }}', data, function(result, status, xhr){
			if(result.status){
				$('#loading').hide();
				clearConfirmation();
			}
			else{
				$('#loading').hide();
				alert(result.message);
			}
		});
	}

	function fetchPoint(){
		var location = $('#location').val();
		var data = {
			location:location
		}
		$.get('{{ url("fetch/general/pointing_call") }}', data, function(result, status, xhr){
			if(result.status){
				$('#container').html("");
				var image_data = "";
				var h = "";

				if(window.innerHeight > window.innerWidth){
					h = "100%";
				}
				else{
					h = "150vh";
				}

				var pic_data = '';
				pic_data += '<div class="col-xs-12" style="padding-bottom: 10px;" id="pic_cok">';
				pic_data += '<center>';
				$.each(result.pics, function(key, value){
					if(value.remark == 1){
						pic_data += '<button onClick="editPIC(\''+value.id+'\''+','+'\''+value.point_title+'\''+','+'\''+value.location+'\')" class="btn btn-lg" style="border-color: black; width: 16%; font-weight: bold; background-color: orange; padding: 2px 5px 2px 5px; margin-left: 5px;">'+value.point_description+'<br>'+value.point_description_jp+'</button>';
					}
					else{
						pic_data += '<button onCLick="editPIC(\''+value.id+'\''+','+'\''+value.point_title+'\''+','+'\''+value.location+'\')" class="btn btn-lg" style="border-color: black; width: 16%; font-weight: bold; background-color: white; padding: 2px 5px 2px 5px; margin-left: 5px;">'+value.point_description+'<br>'+value.point_description_jp+'</button>';
					}
				});
				pic_data += '</center>';
				pic_data += '</div>';
				$('#container').append(pic_data);

				$.each(result.pointing_calls, function(key, value){
					image_data += '<div class="row" id="'+value.point_title+'" name="'+count+'" tabindex="1" style="height: 100%;">';
					image_data += '<input type="hidden" name="inp_'+count+'" value="'+value.point_no/value.point_max+'">';
					if(value.point_title == 'slogan_mutu'){
						image_data += '<div style="font-weight:bold; font-size: 30px; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; bottom:0; text-align:center;">'+value.point_description+'</div>';
						image_data += '<center><img src="{{ asset('images/pointing_calls/japanese') }}/'+value.point_title+'_'+value.point_no+'.gif" style="width: 100vw;"></center>';
						count += 1;						
					}
					else if(value.point_title == 'diamond'){
						image_data += '<div style="font-weight:bold; font-size: 30px; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; bottom:0; text-align:center;">'+value.point_description+'</div>';

						image_data += '<center><img src="{{ asset('images/pointing_calls/japanese') }}/'+value.point_title+'_'+value.point_no+'.gif" style="width: 100vw;"></center>';
						count += 1;	
					}
					else if(value.point_title == 'k3'){
						image_data += '<div style="font-weight:bold; font-size: 30px; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; bottom:0; text-align:center;">'+value.point_description+'</div>';

						image_data += '<center><img src="{{ asset('images/pointing_calls/japanese') }}/'+value.point_title+'_'+value.point_no+'.gif" style="width: 100vw;"></center>';
						count += 1;	
					}

					else if(value.point_title == 'janji_safety'){
						image_data += '<div id="'+value.point_title+'" name="'+count+'" tabindex="1" style="padding-left: 15px; padding-right: 15px;">';
						image_data += '<center><span style="font-weight: bold; font-size: 3vw;">『安全運転宣言』　実践記録表</span></center><br><br>';
						image_data += '<span style="font-weight: bold; font-size: 2vw;">①時間と気持ちにゆとりを持って安全に目的地に着くことを考えよう。（余裕のある出勤をしよう。）</span><br>';
						image_data += '<span style="font-weight: bold; font-size: 2vw;">②あなたの大切な人の為にも交通マナーを守りましょう。</span><br><br><br>';
						image_data += '<span style="font-weight: bold; font-size: 1.8vw;">部門: 駐在員グループ</span><br>';
						image_data += '<table class="table table-bordered">';
						image_data += '<thead>';
						image_data += '<tr style="background-color: rgba(126,86,134,.7); font-size:1.7vw;">';
						image_data += '<th>氏名</th>';
						image_data += '<th>私の安全運転宣言</th>';
						image_data += '</tr>';
						image_data += '</thead>';
						image_data += '<tbody>';
						$.each(result.pics, function(key, value){
							if(value.remark == 1){
								image_data += '<tr style="background-color: orange; font-weight: bold; font-size: 1.7vw;">';
							}
							else{
								image_data += '<tr>';								
							}
							image_data += '<td>'+value.point_description_jp+'</td>';
							image_data += '<td>'+value.safety_riding+'</td>';
							image_data += '</tr>';							
						});						
						image_data += '</tbody>';
						image_data += '</table>';
						image_data += '<span class="dot" style="left: 20px; top 20px; font-weight: bold; font-size: 1vw;">'+count+'</span>';
						image_data += '</div>';
						count += 1;
					}
					else if(value.point_no > 0){
						image_data += '<div style="font-weight:bold; font-size: 2.5vw; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; bottom:0; text-align:center;">'+value.point_description+'</div>';

						image_data += '<center><img src="{{ asset('images/pointing_calls/japanese') }}/'+value.point_title+'_'+value.point_no+'.gif" style="width: 100vw;"></center>';
						count += 1;
					}
					else{
						image_data += '<div style="font-weight:bold; font-size: 2.5vw; background-color: rgba(255,255,0,0.85); width:100%; position: fixed; bottom:0; text-align:center;">'+value.point_description+'</div>';

						image_data += '<center><img src="{{ asset('images/pointing_calls') }}/'+value.point_title+'_jp.jpg" style="width: 100vw;"></center>';
						count += 1;
					}
					image_data += '</div>';
				});

$('#container').append(image_data);

var navigation_data = '';

navigation_data += '<div class="col-xs-12" style="position:absolute; font-size: 1vw; left: 0px; bottom: 30px;"><center>';
navigation_data += '<div class="row">';
navigation_data += '<div class="col-xs-12">';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'iso9001\''+','+'\'2\');" class="btn btn-lg btn-success">Target Kualitas FY197<br>197期　品質目標</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'susunan_organisasi\''+','+'\'1\');" class="btn btn-lg btn-success">Safety Comitee<br>安全委員会</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'zero_complain\''+','+'\'2\');" class="btn btn-lg btn-success">Zero Complain<br>ゼロ・クレーム</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'kecelakaan_kerja\''+','+'\'2\');" class="btn btn-lg btn-success">Kecelakaan Kerja<br>労働災害発生時の連絡系統</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'kecelakaan_lalulintas\''+','+'\'2\');" class="btn btn-lg btn-success">Kecelakaan Lalu Lintas<br>交通災害発生時の連絡系統</a>';
navigation_data += '</div>';

navigation_data += '<div class="col-xs-12">';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'alur_pasca_kecelakaan\''+','+'\'1\');" class="btn btn-lg btn-success">Alur Pelaporan Pasca Kecelakaan<br>交通事故の報告のフロー</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'petunjuk_alarm_kebakaran\''+','+'\'2\');" class="btn btn-lg btn-success">Panduan Kondisi Alarm Kebakaran<br>火災警報が鳴った場合のガイドライン</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'petunjuk_keadaan_emergency\''+','+'\'2\');" class="btn btn-lg btn-success">Panduan Kondisi Darurat<br>火災発生のガイドライン</a>';
navigation_data += '</div>';

navigation_data += '<div class="col-xs-12">';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'diamond\''+','+'\'2\');" class="btn btn-lg btn-warning">Yamaha Diamond<br>ヤマハ・ダイヤモンド</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'k3\''+','+'\'1\');" class="btn btn-lg btn-warning">Aturan K3 Yamaha<br>ヤマハ安全衛生心得</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'6_pasal\''+','+'\'1\');" class="btn btn-lg btn-warning">6 Pasal<br>ヤマハ交通安全６々条</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'budaya\''+','+'\'2\');" class="btn btn-lg btn-warning">Budaya Kerja<br>YMPI取組姿勢</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'slogan_mutu\''+','+'\'2\');" class="btn btn-lg btn-warning">Slogan Mutu<br>品質スローガン</a>';				
navigation_data += '</div>';


navigation_data += '<div class="col-xs-12">';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'10_komitmen\''+','+'\'1\');" class="btn btn-lg btn-warning">10 Komitmen<br>交通安全のための１０の掟</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'janji\''+','+'\'1\');" class="btn btn-lg btn-warning">Janji Tindakan Dasar<br>ホテルコンセプト達成ための基本行動の約束</a>';
navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'komitmen\''+','+'\'1\');" class="btn btn-lg btn-warning">Komitmen Hotel Konsep<br>YMPI従業員　ホテルコンセプトへの誓い</a>';	
navigation_data += '</div>';
navigation_data += '</div>';
navigation_data += '</center></div>';

$('#container').append(navigation_data);

for(var i = 2; i <= count; i++){
	$("[name='"+i+"']").hide();	
}

}
else{
	alert('Unidentified ERROR!')
}
});


}

</script>
@endsection