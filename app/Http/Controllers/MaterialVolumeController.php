<?php

namespace App\Http\Controllers;

use App\Material;
use App\MaterialVolume;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialVolumeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $material_volumes = MaterialVolume::leftJoin('materials', 'materials.material_number', '=', 'material_volumes.material_number')
            ->select(
                'material_volumes.*',
                db::raw('materials.category AS cat'),
                'materials.hpl'
            )
            ->orderBy('category', 'ASC')
            ->orderBy('material_number', 'ASC')
            ->get();

        return view('material_volumes.index', array(
            'material_volumes' => $material_volumes,
        ))->with('page', 'Material Volume');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $materials = Material::orderBy('material_number', 'ASC')->get();
        return view('material_volumes.create', array(
            'materials' => $materials,
        ))->with('page', 'Material Volume');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try
        {
            $id = Auth::id();
            $material_volume = new MaterialVolume([
                'material_number' => $request->get('material_number'),
                'category' => $request->get('category'),
                'lot_completion' => $request->get('lot_completion'),
                'lot_transfer' => $request->get('lot_transfer'),
                'lot_flo' => $request->get('lot_flo'),
                'lot_row' => $request->get('lot_row'),
                'lot_pallet' => $request->get('lot_pallet'),
                'lot_carton' => $request->get('lot_carton'),
                'length' => $request->get('length'),
                'width' => $request->get('width'),
                'height' => $request->get('height'),
                'created_by' => $id,
            ]);

            $material_volume->save();
            return redirect('/index/material_volume')->with('status', 'New material volume has been created.')->with('page', 'Material Volume');

        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return back()->with('error', 'Material volume already exist.')->with('page', 'Material Volume');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'Material Volume');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $material_volume = MaterialVolume::find($id);
        return view('material_volumes.show', array(
            'material_volume' => $material_volume,
        ))->with('page', 'Material Volume');
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $material_volume = MaterialVolume::find($id);
        return view('material_volumes.edit', array(
            'material_volume' => $material_volume,
        ))->with('page', 'Material Volume');
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $material_volume = MaterialVolume::find($id);
            $material_volume->category = $request->get('category');

            $material_volume->lot_pallet = $request->get('lot_pallet');
            $material_volume->length_pallet = $request->get('length_pallet');
            $material_volume->width_pallet = $request->get('width_pallet');
            $material_volume->height_pallet = $request->get('height_pallet');
            $material_volume->cubic_meter_pallet = $request->get('height_pallet') * $request->get('width_pallet') * $request->get('height_pallet');

            $material_volume->lot_carton = $request->get('lot_carton');
            $material_volume->length = $request->get('length');
            $material_volume->width = $request->get('width');
            $material_volume->height = $request->get('height');
            $material_volume->cubic_meter = $request->get('height') * $request->get('width') * $request->get('height');

            $material_volume->created_by = Auth::id();

            $material_volume->save();

            return redirect('/index/material_volume')->with('status', 'Material volume data has been edited.')->with('page', 'Material Volume');

        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return back()->with('error', 'Material volume already exist.')->with('page', 'Material Volume');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'Material Volume');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $material_volume = MaterialVolume::find($id);
        $material_volume->forceDelete();

        return redirect('/index/material_volume')->with('status', 'Material volume has been deleted.')->with('page', 'Material Volume');
        //
    }

    /**
     * Import material volume from textfile.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        if ($request->hasFile('material_volume')) {
            MaterialVolume::truncate();

            $id = Auth::id();

            $file = $request->file('material_volume');
            $data = file_get_contents($file);

            $rows = explode("\r\n", $data);
            foreach ($rows as $row) {
                if (strlen($row) > 0) {
                    $row = explode("\t", $row);
                    $material_volume = new MaterialVolume([
                        'material_number' => $row[0],
                        'category' => $row[1],
                        'lot_completion' => $row[2],
                        'lot_transfer' => $row[3],
                        'lot_flo' => $row[4],
                        'lot_row' => $row[5],
                        'lot_pallet' => $row[6],
                        'lot_carton' => $row[7],
                        'length' => $row[8],
                        'width' => $row[9],
                        'height' => $row[10],
                        'created_by' => $id,
                    ]);

                    $material_volume->save();
                }
            }
            return redirect('/index/material_volume')->with('status', 'New material volumes has been imported.')->with('page', 'Material Volume');

        } else {
            return redirect('/index/material_volume')->with('error', 'Please select a file.')->with('page', 'Material Volume');
        }
        //
    }
}
