@extends('layouts.display')
@section('stylesheets')
<style type="text/css">
	.dot {
		height: 5%;
		width: 5%;
		position: absolute;
		z-index: 10;
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
	.content-wrapper {
		background-color: white !important;
	}
	#loading { display: none; }
</style>
@stop
@section('header')
@endsection
@section('content')
<input type="hidden" id="location" value="{{ $location }}">
<input type="hidden" id="default_language" value="{{ $default_language }}">
<section class="content" style="padding-top: 0;">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: white; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>
	</div>
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
		fetchPoint();
		$(document).bind("contextmenu",function(e){
			return false;
		}); 
	});

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

	function modalImage(id, count){
		var he = $('.content-wrapper').height()*0.45+'px';
		var image_body = '';

		$('#modalImageBody').html('');

		image_body += '<center>';
		if(count == 1){
			image_body += '<img src="{{ asset('images/pointing_calls') }}/'+id+'_id.jpg" style="width:100%;">';
		}
		else{
			image_body += '<img src="{{ asset('images/pointing_calls') }}/'+id+'_id.jpg" style="max-height: '+he+'; max-width:100%;">';
			image_body += '<img src="{{ asset('images/pointing_calls') }}/'+id+'_id.jpg" style="max-height: '+he+'; max-width:100%;">';
		}
		image_body += '</center>';
		console.log("{{ asset('images/pointing_calls') }}/'+id+'_id.jpg");

		$('#modalImageBody').append(image_body);

		$('#modalImage').modal('show');
	}

	function fetchPoint(){
		var location = $('#location').val();
		var data = {
			location: location
		}

		$.get('{{ url("fetch/general/pointing_call") }}', data, function(result, status, xhr){
			if(result.status){
				$('.content').html('');

				// var pic_data = '';
				// pic_data += '<div class="col-xs-12" style="padding-bottom: 10px;" id="pic_cok">';
				// pic_data += '<center>';
				// $.each(result.pics, function(key, value){
				// 	if(value.remark == 1){
				// 		pic_data += '<button onClick="editPIC(\''+value.id+'\''+','+'\''+value.point_title+'\''+','+'\''+value.location+'\')" class="btn btn-lg" style="border-color: black; width: 18%; font-weight: bold; background-color: orange; padding: 2px 5px 2px 5px; margin-left: 5px;">'+value.point_description+'<br>'+value.point_description_jp+'</button>';
				// 	}
				// 	else{
				// 		pic_data += '<button onCLick="editPIC(\''+value.id+'\''+','+'\''+value.point_title+'\''+','+'\''+value.location+'\')" class="btn btn-lg" style="border-color: black; width: 18%; font-weight: bold; background-color: white; padding: 2px 5px 2px 5px; margin-left: 5px;">'+value.point_description+'<br>'+value.point_description_jp+'</button>';
				// 	}
				// });
				// pic_data += '</center>';
				// pic_data += '</div>';
				// $('.content').append(pic_data);

				var h = $('#pic_cok').height()+$('.navbar-header').height();

				var count = 1;
				var image_data = '';

				$.each(result.pointing_calls, function(key, value){
					if(value.point_title != 'janji_safety'){
						image_data += '<div class="row" id="'+value.point_title+'" name="'+count+'" tabindex="1">';
						image_data += '<img src="{{ asset('images/pointing_calls') }}/'+value.point_title+'_id.jpg" style="width: 100%;">';
						image_data += '</div>';
					}
					else{
						image_data += '<div id="'+value.point_title+'" name="'+count+'" tabindex="1">';
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
						// $.each(result.pics, function(key, value){
						// 	if(value.remark == 1){
						// 		image_data += '<tr style="background-color: orange; font-weight: bold; font-size: 1.7vw;">';
						// 	}
						// 	else{
						// 		image_data += '<tr>';								
						// 	}
						// 	image_data += '<td>'+value.point_description_jp+'</td>';
						// 	image_data += '<td>'+value.safety_riding+'</td>';
						// 	image_data += '</tr>';							
						// });						
						image_data += '</tbody>';
						image_data += '</table>';
						image_data += '</div>';
					}

					count += 1;
				});
				$('.content').append(image_data);

				// var navigation_data = '';

				// navigation_data += '<div class="col-xs-12" style="position:absolute; font-size: 1vw; left: 0px; top: 1650px;"><center>';
				// navigation_data += '<div class="row">';
				// navigation_data += '<div class="col-xs-12">';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'iso9001\''+','+'\'2\');" class="btn btn-lg btn-success">Target Kualitas FY197<br>197期　品質目標</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'susunan_organisasi\''+','+'\'1\');" class="btn btn-lg btn-success">Safety Comitee<br>安全委員会</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'zero_complain\''+','+'\'2\');" class="btn btn-lg btn-success">Zero Complain<br>ゼロ・クレーム</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'kecelakaan_kerja\''+','+'\'2\');" class="btn btn-lg btn-success">Kecelakaan Kerja<br>労働災害発生時の連絡系統</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'kecelakaan_lalulintas\''+','+'\'2\');" class="btn btn-lg btn-success">Kecelakaan Lalu Lintas<br>交通災害発生時の連絡系統</a>';
				// navigation_data += '</div>';

				// navigation_data += '<div class="col-xs-12">';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'alur_pasca_kecelakaan\''+','+'\'1\');" class="btn btn-lg btn-success">Alur Pelaporan Pasca Kecelakaan<br>交通事故の報告のフロー</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'petunjuk_alarm_kebakaran\''+','+'\'2\');" class="btn btn-lg btn-success">Panduan Kondisi Alarm Kebakaran<br>火災警報が鳴った場合のガイドライン</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'petunjuk_keadaan_emergency\''+','+'\'2\');" class="btn btn-lg btn-success">Panduan Kondisi Darurat<br>火災発生のガイドライン</a>';
				// navigation_data += '</div>';

				// navigation_data += '<div class="col-xs-12">';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'diamond\''+','+'\'2\');" class="btn btn-lg btn-warning">Yamaha Diamond<br>ヤマハ・ダイヤモンド</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'k3\''+','+'\'1\');" class="btn btn-lg btn-warning">Aturan K3 Yamaha<br>ヤマハ安全衛生心得</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'6_pasal\''+','+'\'1\');" class="btn btn-lg btn-warning">6 Pasal<br>ヤマハ交通安全６々条</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'budaya\''+','+'\'2\');" class="btn btn-lg btn-warning">Budaya Kerja<br>YMPI取組姿勢</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 19%;" href="javascript:void(0)" onclick="modalImage(\'slogan_mutu\''+','+'\'2\');" class="btn btn-lg btn-warning">Slogan Mutu<br>品質スローガン</a>';				
				// navigation_data += '</div>';

				
				// navigation_data += '<div class="col-xs-12">';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'10_komitmen\''+','+'\'1\');" class="btn btn-lg btn-warning">10 Komitmen<br>交通安全のための１０の掟</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'janji\''+','+'\'1\');" class="btn btn-lg btn-warning">Janji Tindakan Dasar<br>ホテルコンセプト達成ための基本行動の約束</a>';
				// navigation_data += '<a style="margin: 5px; padding: 0 10px 0 10px; width: 32.25%;" href="javascript:void(0)" onclick="modalImage(\'komitmen\''+','+'\'1\');" class="btn btn-lg btn-warning">Komitmen Hotel Konsep<br>YMPI従業員　ホテルコンセプトへの誓い</a>';	
				// navigation_data += '</div>';
				// navigation_data += '</div>';
				// navigation_data += '</center></div>';

				// $('.content').append(navigation_data);

				$.each(result.pointing_calls, function(key, value){
					var point_data = '';

					if(value.point_title == 'diamond'){
						if (value.point_no == 1) {
							point_data += '<div id="dot_diamond" class="dot">';
							point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
							point_data += '</div>';
						}else{
							point_data += '<div id="dot_diamond" class="dot">';
							point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
							point_data += '</div>';
						}
						$('#diamond').append(point_data);

						if(value.point_no == 1){
							var x = 190;
							var y = 777;
						}

						if(value.point_no == 2){
							var x =218;
							var y = 802;
						}

						if(value.point_no == 3){
							var x = 245;
							var y = 395;
						}

						if(value.point_no == 4){
							var x = 250;
							var y = 428;
						}

						if(value.point_no == 5){
							var x = 250;
							var y = 461;
						}

						if(value.point_no == 6){
							var x = 250;
							var y = 493;
						}

						if(value.point_no == 7){
							var x = 65;
							var y = 585;
						}

						if(value.point_no == 8){
							var x = 80;
							var y = 625;
						}

						if(value.point_no == 9){
							var x = 90;
							var y = 665;
						}

						if(value.point_no == 10){
							var x = 430;
							var y = 570;
						}

						if(value.point_no == 11){
							var x = 420;
							var y = 605;
						}

						if(value.point_no == 12){
							var x = 430;
							var y = 643;
						}

						if(value.point_no == 13){
							var x = 425;
							var y = 678;
						}

						if(value.point_no == 14){
							var x = 422;
							var y = 713;
						}

						var div = document.getElementById('dot_diamond');
						div.style.left = x + 'px';
						div.style.top = y + 'px';
					}

					if(value.point_title == '10_komitmen'){
						point_data += '<div id="dot_10_komitmen" class="dot">';
						point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
						point_data += '</div>';
						$('#10_komitmen').append(point_data);

						if(value.point_no == 1){
							var x = 60;
							var y = 390;
						}

						if(value.point_no == 2){
							var x = 60;
							var y = 452;
						}

						if(value.point_no == 3){
							var x = 60;
							var y = 515;
						}

						if(value.point_no == 4){
							var x = 60;
							var y = 578;
						}

						if(value.point_no == 5){
							var x = 60;
							var y = 638;
						}

						if(value.point_no == 6){
							var x = 60;
							var y = 725;
						}

						if(value.point_no == 7){
							var x = 60;
							var y = 805;
						}

						if(value.point_no == 8){
							var x = 60;
							var y = 885;
						}

						if(value.point_no == 9){
							var x = 60;
							var y = 985;
						}

						if(value.point_no == 10){
							var x = 60;
							var y = 1070;
						}

						var div = document.getElementById('dot_10_komitmen');
						div.style.left = x + 'px';
						div.style.top = y + 'px';
					}

					if(value.point_title == 'k3'){
						point_data += '<div id="dot_k3" class="dot">';
						point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
						point_data += '</div>';
						$('#k3').append(point_data);

						if(value.point_no == 1){
							var x = 45;
							var y = 325;
						}

						if(value.point_no == 2){
							var x = 45;
							var y = 385;
						}

						if(value.point_no == 3){
							var x = 45;
							var y = 460;
						}

						if(value.point_no == 4){
							var x = 45;
							var y = 515;
						}

						if(value.point_no == 5){
							var x = 45;
							var y = 575;
						}

						if(value.point_no == 6){
							var x = 45;
							var y = 650;
						}

						if(value.point_no == 7){
							var x = 45;
							var y = 705;
						}

						if(value.point_no == 8){
							var x = 45;
							var y = 765;
						}

						if(value.point_no == 9){
							var x = 45;
							var y = 820;
						}

						if(value.point_no == 10){
							var x = 45;
							var y = 875;
						}

						if(value.point_no == 11){
							var x = 45;
							var y = 935;
						}

						if(value.point_no == 12){
							var x = 45;
							var y = 990;
						}

						var div = document.getElementById('dot_k3');
						div.style.left = x + 'px';
						div.style.top = y + 'px';
					}

					if(value.point_title == '6_pasal'){
						point_data += '<div id="dot_6_pasal" class="dot">';
						point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
						point_data += '</div>';
						$('#6_pasal').append(point_data);

						if(value.point_no == 1){
							var x = 90;
							var y = 390;
						}

						if(value.point_no == 2){
							var x = 90;
							var y = 515;
						}

						if(value.point_no == 3){
							var x = 90;
							var y = 680;
						}

						if(value.point_no == 4){
							var x = 90;
							var y = 845;
						}

						if(value.point_no == 5){
							var x = 90;
							var y = 1015;
						}

						if(value.point_no == 6){
							var x = 90;
							var y = 1180;
						}

						var div = document.getElementById('dot_6_pasal');
						div.style.left = x + 'px';
						div.style.top = y + 'px';
					}

					if(value.point_title == 'komitmen'){
						point_data += '<div id="dot_komitmen" class="dot">';
						point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
						point_data += '</div>';
						$('#komitmen').append(point_data);

						if(value.point_no == 1){
							var x = 0;
							var y = 620;
						}

						if(value.point_no == 2){
							var x = 0;
							var y = 715;
						}

						if(value.point_no == 3){
							var x = 0;
							var y = 810;
						}

						if(value.point_no == 4){
							var x = 0;
							var y = 915;
						}

						if(value.point_no == 5){
							var x = 0;
							var y = 1010;
						}

						if(value.point_no == 6){
							var x = 0;
							var y = 1110;
						}

						if(value.point_no == 7){
							var x = 0;
							var y = 1210;
						}

						if(value.point_no == 8){
							var x = 0;
							var y = 1305;
						}

						if(value.point_no == 9){
							var x = 0;
							var y = 1405;
						}

						if(value.point_no == 10){
							var x = 0;
							var y = 1505;
						}

						var div = document.getElementById('dot_komitmen');
						div.style.left = x + 'px';
						div.style.top = y + 'px';
					}

					if(value.point_title == 'janji'){
						point_data += '<div id="dot_janji" class="dot">';
						point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
						point_data += '</div>';
						$('#janji').append(point_data);

						if(value.point_no == 1){
							var x = -5;
							var y = 500;
						}

						if(value.point_no == 2){
							var x = -5;
							var y = 595;
						}

						if(value.point_no == 3){
							var x = -5;
							var y = 670;
						}

						if(value.point_no == 4){
							var x = -5;
							var y = 780;
						}

						if(value.point_no == 5){
							var x = -5;
							var y = 870;
						}

						if(value.point_no == 6){
							var x = -5;
							var y = 960;
						}

						if(value.point_no == 7){
							var x = -5;
							var y = 1050;
						}

						if(value.point_no == 8){
							var x = -5;
							var y = 1140;
						}

						if(value.point_no == 9){
							var x = -5;
							var y = 1230;
						}

						if(value.point_no == 10){
							var x = -5;
							var y = 1320;
						}

						if(value.point_no == 11){
							var x = -5;
							var y = 1410;
						}

						if(value.point_no == 12){
							var x = -5;
							var y = 1490;
						}

						var div = document.getElementById('dot_janji');
						div.style.left = x + 'px';
						div.style.top = y + 'px';
					}

					if(value.point_title == 'budaya'){
						point_data += '<div id="dot_budaya" class="dot">';
						point_data += '<img src="{{url("/images/pointing_calls/arrow.gif")}}" width="100%">';
						point_data += '</div>';
						$('#budaya').append(point_data);

						if(value.point_no == 1){
							var x = -5;
							var y = 185;
						}

						if(value.point_no == 2){
							var x = -5;
							var y = 225;
						}

						if(value.point_no == 3){
							var x = -5;
							var y = 265;
						}

						if(value.point_no == 4){
							var x = -5;
							var y = 310;
						}

						if(value.point_no == 5){
							var x = -5;
							var y = 355;
						}

						if(value.point_no == 6){
							var x = -5;
							var y = 400;
						}

						if(value.point_no == 7){
							var x = -5;
							var y = 470;
						}

						if(value.point_no == 8){
							var x = -5;
							var y = 515;
						}

						if(value.point_no == 9){
							var x = -5;
							var y = 560;
						}

						if(value.point_no == 10){
							var x = -5;
							var y = 600;
						}

						if(value.point_no == 11){
							var x = -5;
							var y = 680;
						}

						if(value.point_no == 12){
							var x = -5;
							var y = 755;
						}

						if(value.point_no == 13){
							var x = -5;
							var y = 800;
						}

						if(value.point_no == 14){
							var x = -5;
							var y = 870;
						}

						var div = document.getElementById('dot_budaya');
						div.style.left = x + 'px';
						div.style.top = y + 'px';
					}

					for (var i = 2; i <= count; i++) {
						$("[name='"+i+"']").hide();	
					}
				});

var curr = 1;
$(function() {
	$(document).keydown(function(e) {
		switch(e.which) {
			case 39:

			// var c = curr+1;

			// for (var i = 1; i <= count; i++) {
			// 	$("[name='"+i+"']").hide();	
			// }

			// $("[name='"+c+"']").show();	

			// curr += 1;

			// if(curr >= count-1){
			// 	curr = 0;
			// }

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

			console.log('tampil = '+curr+'; jml = '+count);
			break;

		}
	});
});

}
else{
	$('#loading').show();
}
});
}

</script>
@endsection