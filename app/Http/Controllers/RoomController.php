<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Libraries\ActMLEasyIf;
use App\User;
use App\Plc;
use Response;

class RoomController extends Controller
{

	public function fetchToilet(Request $request)
	{
		$datas2 = array();

		$location = $request->get('location');

		if($location == 'buffing'){
			$plc = new ActMLEasyIf(1);
			for ($i=1; $i < 12 ; $i++) {
				array_push($datas, $plc->read_data('D'.$i, 1));
			}
		}
		if($location == 'office'){
			$suhus = Plc::where('location', '=', 'TOILET OFFICE')->orderBy('id', 'asc')->get();
			$list_suhu = array();

			$datas2 = [];

			foreach ($suhus as $suhu) {
				$cpu = new ActMLEasyIf($suhu->station);
				$datas = $cpu->read_data($suhu->address, 1);
				$data = $datas[0];

				array_push($datas2, $data);

				// array_push($list_suhu, [
				// 	'location' => $suhu->location,
				// 	'remark' => $suhu->remark,
				// 	'value' => $data,
				// 	'upper_limit' => $suhu->upper_limit,
				// 	'lower_limit' => $suhu->lower_limit,
				// ]);
			}
			// for ($i=0; $i < 7 ; $i++) {
			// 	array_push($datas, $plc->read_data('D0', $i));
			// }
		}

		$response = array(
			'status' => true,
			'datas' => $datas2
		);
		return Response::json($response);
	}

	public function indexToilet(){
		return view('rooms.index_toilet')->with('page', 'Toilet');
	}

	public function indexRoomToilet($id)
	{
		if($id == 'buffing'){
			$title = 'Buffing Toilet Information';
			$title_jp = '??';

			return view('rooms.buffingToilet', array(
				'title' => $title,
				'title_jp' => $title_jp
			))->with('page', 'toilet');			
		}
		if($id == 'office'){
			$title = 'Office Toilet Information';
			$title_jp = '??';

			return view('rooms.officeToilet', array(
				'title' => $title,
				'title_jp' => $title_jp
			))->with('page', 'toilet');

		}
	}
}
