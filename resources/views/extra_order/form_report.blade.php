@extends('layouts.notification')
@section('stylesheets')
<link href="{{ url("css/jquery.gritter.css") }}" rel="stylesheet">
<style type="text/css">
	.eo_number {
		font-size: 4vw;
		font-weight: bold;
		cursor: pointer;
		color: #3c8dbc;
	}

	.status {
		font-size: 3vw;
		font-weight: bold;
	}

	.message {
		font-size: 2vw;
	}

</style>
@endsection

@section('header')
<section class="content-header">
	<h1>
		{{ $title }}
		<small><span class="text-purple">{{ $title_jp }}</span></small>
	</h1>
</section>
@endsection

@section('content')
<section class="content">
	<div id="loading" style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
		<p style="position: absolute; color: White; top: 45%; left: 45%;">
			<span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
		</p>

	</div>
	<div class="row">
		<div class="error" style="text-align: center;">
			<p>
				<h2>
					{{-- Sudah di Reject --}}
					@if($code == 0)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					@if($approval->remark == 'Buyer Procurement' && !is_null($approval->note))
					<p class="message">
						Already {{ $approval->note }}<br>
						<span class="text-purple">すでに拒否されました</span><br>
						{{ $approval->approved_at }}
					</p>
					@else
					<p class="message">
						Already Rejected by {{ $approval->approver_name }}<br>
						<span class="text-purple">{{ $approval->approver_name }} が却下した</span><br>
						{{ $approval->approved_at }}<br>
					</p>
					@endif
					<p class="status"><i style="color : grey;" class="fa fa-info-circle"></i>&nbsp;&nbsp;ALREADY REJECTED!</p>


					{{-- Berhasil --}}
					@elseif($code == 1)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					@if($approval->remark == 'Buyer Procurement' && !is_null($approval->note))
					<p class="message">
						{{ $approval->note }}<br>
						<span class="text-purple">承認完了</span><br>
						{{ $approval->approved_at }}
					</p>
					@else
					<p class="message">
						{{ $approval->status }} by {{ $approval->approver_name }}<br>
						<span class="text-purple">承認完了</span><br>
						{{ $approval->approved_at }}
					</p>
					@endif
					<p class="status"><i style="color : #0bb13d;" class="fa fa-check-square-o"></i>&nbsp;&nbsp;&nbsp;{{ $approval->status }}!</p>


					{{-- Sudah di Approve --}}
					@elseif($code == 2)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					@if($approval->remark == 'Buyer Procurement' && !is_null($approval->note))
					<p class="message">
						Already {{ $approval->note }}<br>
						<span class="text-purple">承認済み</span><br>
						{{ $approval->approved_at }}
					</p>
					@else
					<p class="message">
						Already Approved by {{ $approval->approver_name }}<br>
						<span class="text-purple">承認済み</span><br>
						{{ $approval->approved_at }}
					</p>
					@endif
					<p class="status"><i style="color : #68cae6" class="fa fa-info-circle"></i>&nbsp;&nbsp;ALREADY APPROVED!</p>


					{{-- Gagal --}}
					@elseif($code == 3)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					<p class="message">
						You don't have authorization<br>
						<span class="text-purple">権限がありません</span>

					</p>
					<p class="status"><i style="color : #c51c42" class="fa fa-remove"></i>&nbsp;&nbsp;FAILED!</p>


					{{-- Approver sebelumnya belum approve --}}
					@elseif($code == 4)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					<p class="message">
						The previous approver not approve yet<br>
						<span class="text-purple">前の承認担当者がまだ処理していません</span>
					</p>
					<p class="status"><i style="color : #c51c42" class="fa fa-remove"></i>&nbsp;&nbsp;FAILED!</p>


					{{-- Reject --}}
					@elseif($code == 5)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					<p class="message">
						Approval has been Rejected<br>
						<span class="text-purple">申請が却下されました</span>
					</p>
					<p class="status"><i style="color : #c51c42" class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;REJECTED!</p>


					{{-- Comment --}}
					@elseif($code == 6)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					<p class="message">
						Comment sent successfully<br>
						<span class="text-purple">コメント送信完了</span>
					</p>
					<p class="status"><i style="color : #0bb13d" class="fa fa-check-square"></i>&nbsp;&nbsp;SUCCESS!</p>

					{{-- Answer --}}
					@elseif($code == 7)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					<p class="message">
						Answer sent successfully<br>
						<span class="text-purple">返答送信完了</span>
					</p>
					<p class="status"><i style="color : #0bb13d" class="fa fa-check-square"></i>&nbsp;&nbsp;SUCCESS!</p>



					{{-- Approve PO --}}
					@elseif($code == 101)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					<p class="message">
						New PO from Buyer apporved successfully
					</p>
					<p class="status"><i style="color : #0bb13d" class="fa fa-check-square"></i>&nbsp;&nbsp;SUCCESS!</p>

					{{-- Approve PO tidak sesuai flow --}}
					@elseif($code == 102)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					<p class="message">
						PO already approved or Buyer has not uploaded PO 
					</p>
					<p class="status"><i style="color : #c51c42" class="fa fa-remove"></i>&nbsp;&nbsp;WRONG FLOW!</p>					

					{{-- Reject PO --}}
					@elseif($code == 103)
					<p class="eo_number" onclick="detailExtraOrder('{{ $approval->eo_number }}')">{{ $approval->eo_number }}</p>
					<p class="message">
						PO rejected successfully and notification was sent to Buyer 
					</p>
					<p class="status"><i style="color : #0bb13d" class="fa fa-check-square"></i>&nbsp;&nbsp;REJECT PO SUCCESSFULLY!</p>
					@endif

				</h2>
			</p>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	jQuery(document).ready(function() {

	});

	function detailExtraOrder(eo_number) {
		window.open('{{ url('index/extra_order/detail') }}' + '/' + eo_number, '_self');
	}


</script>
@endsection