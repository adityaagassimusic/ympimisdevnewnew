<?php

namespace App\Http\Controllers;

use App\Destination;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DestinationController extends Controller
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
        $destinations = Destination::whereNull('deleted_at')
            ->where('destination_shortname', '<>', 'ITM')
            ->select(
                'destinations.*',
                db::raw('coalesce(destinations.priority, "") AS prt'),
                db::raw('IF(destinations.priority IS NULL, "INACTIVE", "ACTIVE") AS status')
            )
            ->orderBy('status', 'ASC')
            ->orderBy('priority', 'ASC')
            ->get();

        return view('destinations.index', array(
            'destinations' => $destinations,
        ))->with('page', 'Destination')->with('head2', 'Master Data')->with('head', 'Shipment');
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('destinations.create')->with('page', 'Destination');
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
        try {

            $id = Auth::id();
            $destination = new Destination([
                'destination_code' => $request->get('destination_code'),
                'destination_name' => $request->get('destination_name'),
                'destination_shortname' => $request->get('destination_shortname'),
                'priority' => $request->get('priority'),
                'created_by' => $id,
            ]);

            $destination->save();
            return redirect('/index/destination')->with('status', 'New destination has been created.')->with('page', 'Destination');

        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return back()->with('error', 'Destination code or destination name already exist.')->with('page', 'Destination');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'Destination');
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
        $destination = Destination::find($id);
        return view('destinations.show', array(
            'destination' => $destination,
        ))->with('page', 'Destination');
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
        $destination = Destination::find($id);
        return view('destinations.edit', array(
            'destination' => $destination,
        ))->with('page', 'Destination');
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

        $check = Destination::where('priority', $request->get('priority'))->first();
        if ($check && strlen($request->get('priority') > 0)) {
            return back()->with('error', 'Priority already exist.')->with('page', 'Destination');
        }

        try {

            $destination = Destination::find($id);
            $destination->destination_code = $request->get('destination_code');
            $destination->destination_name = $request->get('destination_name');
            $destination->destination_shortname = $request->get('destination_shortname');
            $destination->priority = $request->get('priority');
            $destination->save();

            return redirect('/index/destination')->with('status', 'Destination data has been edited.')->with('page', 'Destination');

        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return back()->with('error', 'Destination code or destination name already exist.')->with('page', 'Destination');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'Destination');
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
        $destination = Destination::find($id);
        $destination->forceDelete();

        return redirect('/index/destination')->with('status', 'Destination has been deleted.')->with('page', 'Destination');
        //
    }

    public function import(Request $request)
    {
        if ($request->hasFile('destination')) {
            Destination::truncate();

            $id = Auth::id();

            $file = $request->file('destination');
            $data = file_get_contents($file);

            $rows = explode("\r\n", $data);
            foreach ($rows as $row) {
                if (strlen($row) > 0) {
                    $row = explode("\t", $row);
                    $destination = new Destination([
                        'destination_code' => $row[0],
                        'destination_name' => $row[1],
                        'destination_shortname' => $row[2],
                        'created_by' => $id,
                    ]);

                    $destination->save();
                }
            }
            return redirect('/index/destination')->with('status', 'New destinations has been imported.')->with('page', 'Destination');

        } else {
            return redirect('/index/destination')->with('error', 'Please select a file.')->with('page', 'Destination');
        }
    }
}
