<?php

namespace App\Http\Controllers;

use App\Files;
use App\EmployeeSync;
use App\CodeGenerator;
use App\Mail\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileManagerController extends Controller
{
    private $newDocumentName;

    public $FOLDER_PATH = '/files/documents';        

    public $MIS_EMAIL = 'ympi-mis-ML@music.yamaha.com';

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {                
                                
        $emp = DB::table('employee_syncs')
        ->leftJoin('users', 'employee_syncs.employee_id', '=', 'users.username')
        ->select('users.username', 'employee_syncs.name', 'users.email')
        ->where('email', '!=', '')
        ->where('end_date', '=', null)
        ->get();


        $calendar = db::table('weekly_calendars');        
        
        $fiscal_year = $calendar        
        ->select('fiscal_year')        
        ->groupBy('fiscal_year')
        ->get();

        $fiscal_year_details = $calendar
        // ->selectRaw("DATE_FORMAT(MIN(week_date), '%d %b %Y') as start_date")
        // ->selectRaw("DATE_FORMAT(MAX(week_date), '%d %b %Y') as end_date")
        ->select('fiscal_year')
        ->selectRaw("CONCAT(DATE_FORMAT(MIN(week_date), '%d %b %Y'), ' - ', DATE_FORMAT(MAX(week_date), '%d %b %Y')) as start_end_date")
        ->groupBy('fiscal_year')
        ->get();


        $year = $calendar        
        ->select(DB::raw("YEAR(week_date) as year"))
        ->orderBy('year', 'desc')
        ->groupBy('year')
        ->distinct()
        ->get();

        $categories = db::connection('ympimis_2')->table('file_category')
        ->select('category_name')
        ->get();

        $pic = [
            'EY',
            'A/C Bank-MUFG-USD',
            'A/C Bank-MUFG-USD SDA',
            'A/C Bank-MUFG-IDR',
            'A/C Bank-Mizuho-USD',
            'A/C Bank-Mizuho-IDR',
            'A/C Bank-Mandiri-IDR',
        ];
        
        return view('file_manager.index', [
            'title' => 'Document Controlling System',
            'title_jp' => '書類管理システム',                        
            'fiscal_year' => $fiscal_year,
            'fiscal_year_details' => $fiscal_year_details,
            'year' => $year,
            'pics' => $pic,
            'categories' => $categories,
            'employees' => $emp,
        ]);
    }

    public function indexDocumentControl() {
            
        $calendar = db::table('weekly_calendars');        
        
        $fiscal_year = $calendar        
        ->select('fiscal_year')        
        ->groupBy('fiscal_year')
        ->get();

        $fiscal_year_details = $calendar            
        ->select('fiscal_year')
        ->selectRaw("CONCAT(DATE_FORMAT(MIN(week_date), '%d %b %Y'), ' - ', DATE_FORMAT(MAX(week_date), '%d %b %Y')) as start_end_date")
        ->groupBy('fiscal_year')
        ->get();

        $year = $calendar
        ->select(DB::raw("YEAR(week_date) as year"))
        ->orderBy('year', 'desc')
        ->groupBy('year')
        ->distinct()
        ->get();            
        
        return view('file_manager.index_document_control', [
            'title' => 'Document Control Monitoring',
            'title_jp' => '??',                        
            'fiscal_year' => $fiscal_year,
            'fiscal_year_details' => $fiscal_year_details,
            'year' => $year
        ]);
    }

    // getAllFiles
    public function getAllFiles(Request $request) {                                 
        
        $file_category = $request->get('file_category');
        // $fiscal_year = $request->get('fiscal_year');
        $query = db::connection('ympimis_2')->table('file_manager')
        ->select('file_manager.id','file_manager.fiscal_year', 'file_manager.period', 'file_manager.file_name', 'file_manager.file_category', 'file_manager.file_pic', 'file_manager.letter_no','file_manager.letter_date','file_manager.received_date','file_manager.due_date','file_manager.remark','file_manager.created_at', DB::raw('GROUP_CONCAT(docs_name_origin SEPARATOR "|") as docs_name_origin') , DB::raw('GROUP_CONCAT(docs_name SEPARATOR "|") as docs_name'), DB::raw('GROUP_CONCAT(file_url SEPARATOR "|") as file_url'))
        ->leftJoin('file_documents', 'file_manager.id', '=', 'file_documents.file_id')
        ->groupBy('file_manager.id','file_manager.fiscal_year', 'file_manager.period', 'file_manager.file_name', 'file_manager.file_category', 'file_manager.file_pic', 'file_manager.letter_no','file_manager.letter_date','file_manager.received_date','file_manager.due_date','file_manager.remark','file_manager.created_at')
        ->orderBy('file_manager.id', 'desc');
        
        $fiscal_year = array();
        if(strlen($request->get('fiscal_year')) > 0){
            $fiscal_year = explode(',', $request->get('fiscal_year'));
        }
        $years = $request->get('year');
        $year = explode(',', $years);        

        $start_year = $request->get('start_year');
        $end_year = $request->get('end_year');

        if ($year[0] != '') {                               
            $calendar = DB::table('weekly_calendars')
            ->select(DB::raw('DISTINCT fiscal_year'))        
            ->whereRaw("DATE_FORMAT(week_date,'%Y') = " . $year[0])
            ->get();
            
            foreach ($calendar as $key => $value) {
                array_push($fiscal_year, $value->fiscal_year);
            }                                    
        }

        if (count($start_year) > 0) {
            
            $start_year = date('Y', strtotime('01-' . $start_year));                 
            $end_year = date('Y', strtotime('01-' . $end_year));                        

            $calendar = DB::table('weekly_calendars')
            ->select(DB::raw('DISTINCT fiscal_year'))
            ->whereRaw("DATE_FORMAT(week_date,'%Y') >= " . $start_year)
            ->whereRaw("DATE_FORMAT(week_date,'%Y') <= " . $end_year)
            ->get();            
            
            foreach ($calendar as $key => $value) {
                array_push($fiscal_year, $value->fiscal_year);
            }
        }        
        
        if($file_category != 'All'){
            $query->where('file_manager.file_category', $file_category);
        }

        $due_date_filter = $request->get('due_date_filter');                                
        
        if($due_date_filter != ''){
            $due_date_query = DB::connection('ympimis_2')->table(DB::raw("(
                SELECT file_name, due_date, 
                  CASE 
                    WHEN DATEDIFF(due_date, NOW()) > 30 THEN 'More than 30 days'                
                    WHEN DATEDIFF(due_date, NOW()) <= 30 AND DATEDIFF(due_date, NOW()) > 15 THEN 'Less than 30 days'
                    WHEN DATEDIFF(due_date, NOW()) <= 15 THEN 'Less than 15 days'
                  END AS time_difference
                FROM file_manager
              ) AS subquery"))
              ->where('time_difference', $due_date_filter)
              ->pluck('file_name');
              
              $due_date_query = db::connection('ympimis_2')->table('file_manager')
              ->select('file_manager.id','file_manager.fiscal_year', 'file_manager.period', 'file_manager.file_name', 'file_manager.file_category', 'file_manager.file_pic', 'file_manager.letter_no','file_manager.due_date','file_manager.remark','file_manager.created_at', DB::raw('GROUP_CONCAT(docs_name_origin) as docs_name_origin') , DB::raw('GROUP_CONCAT(docs_name) as docs_name'), DB::raw('GROUP_CONCAT(file_url) as file_url'))
              ->leftJoin('file_documents', 'file_manager.id', '=', 'file_documents.file_id')
            //   ->groupBy('file_manager.fiscal_year', 'file_manager.period', 'file_manager.file_category', 'file_manager.file_pic')            
              ->groupBy('file_manager.id','file_manager.fiscal_year', 'file_manager.period', 'file_manager.file_name', 'file_manager.file_category', 'file_manager.file_pic', 'file_manager.letter_no','file_manager.due_date','file_manager.remark','file_manager.created_at')
              ->whereIn('file_manager.file_name', $due_date_query);
        }                                

        if (count($fiscal_year) > 0) {            
            $query->whereIn('file_manager.fiscal_year', $fiscal_year);
        }
                

        if($due_date_filter != ''){
            $files = $due_date_query->get();
        }else{
            $files = $query->get();
        }
        
        $respond = array(
            'status' => 'success',
            'file_category' => $request->get('file_category'),
            'data' => $files            
        );
        return response()->json($respond);
    }

    public function getAllCategory(Request $request)
    {        

        $file_category = DB::connection('ympimis_2')->table('file_category')
        ->orderBy('category_name', 'asc')        
        ->where('parent_id', '=', 0)
        ->get();                

        $file_category_edit = DB::connection('ympimis_2')->table('file_category')       
        ->orderBy('category_name', 'asc') 
        ->get();

        $file_count_by_category = DB::connection('ympimis_2')->table('file_manager')
        ->select('file_category.category_name', DB::raw('count(*) as total'))
        ->leftJoin('file_category', 'file_manager.file_category', '=', 'file_category.category_name')
        ->groupBy('file_category.category_name')
        ->get();

        $file_category_sub = DB::connection('ympimis_2')->table('file_category')
        ->where('parent_id', '!=', 0)
        ->get();

        $respond = array(
            'status' => 'success',
            'data' => $file_category,
            'data_edit' => $file_category_edit,
            'data_count' => $file_count_by_category,
            'data_sub' => $file_category_sub
        );

        return response()->json($respond);

    }

    public function getChart()
    {
        $fiscal_year = db::table('weekly_calendars')
        ->select('fiscal_year')        
        ->orderBy('fiscal_year', 'asc')
        ->groupBy('fiscal_year')
        ->get();                

        $categories = DB::connection('ympimis_2')->table('file_category')
        ->select('category_name')
        // ->where('parent_id', '=', 0)
        ->orderBy('category_name', 'asc')
        ->get();

        // $total_docs = DB::connection('ympimis_2')->table('file_documents')
        // ->select('file_id', 'file_category','fiscal_year', DB::raw('count(*) as total'))
        // ->groupBy('fiscal_year', 'file_category')
        // ->get();

        $total_docs = DB::connection('ympimis_2')->table('file_manager')
        ->select('file_category','fiscal_year', DB::raw('count(*) as total'))            

        // ->groupBy('id', 'file_category','fiscal_year')
        ->groupBy('fiscal_year', 'file_category')

        ->get();

        // Backup
        // WHEN COALESCE(DATEDIFF(due_date, NOW()), 0) <= 30 AND COALESCE(DATEDIFF(due_date, NOW()), 0) > 15 THEN 'Less than 30 days, more than 15 days'

        $due_date = DB::connection('ympimis_2')->table(DB::raw("(
            SELECT 
              CASE 
                WHEN DATEDIFF(due_date, NOW()) > 30 THEN 'More than 30 days'                
                WHEN DATEDIFF(due_date, NOW()) <= 30 AND DATEDIFF(due_date, NOW()) > 15 THEN 'Less than 30 days'
                WHEN DATEDIFF(due_date, NOW()) <= 15 THEN 'Less than 15 days'
              END AS time_difference
            FROM file_manager   
          ) AS subquery"))
        //   ->groupBy('time_difference')
        // group by all selected above 
        ->selectRaw('time_difference, count(*) as total')
        ->groupBy('time_difference')
        ->get();


        $respond = array(
            'status' => 'success',

            'fiscal_year' => $fiscal_year,
            'file_categories' => $categories,
            'total_docs' => $total_docs,                        
            'due_date' => $due_date

        );

        return response()->json($respond);


    }

    public function viewAttachment(Request $request) {
        $file_id = $request->get('file_id');
        $file = db::connection('ympimis_2')->table('file_documents')
        ->where('file_id', $file_id)
        ->get();

        $respond = array(
            'status' => 'success',
            'data' => $file
        );
        return response()->json($respond);
    }

    public function uploadAttachment(Request $request)
    {           
        $files = array();
        $file = new Files();
        $arr_files = [];            

        DB::connection('ympimis_2')->beginTransaction();
        $file_id = $request->get('file_id');        
        $fy = $request->get('fiscal_year');
        $filename = $request->get('file_name');
        $file_category = $request->get('file_category');
        $file_destination = $this->FOLDER_PATH;
        $docsnames = array();        
        

        if($request->get('att_count') > 0) {         
            try {                                
                for ($i=0; $i < $request->get('att_count'); $i++) { 
                    $file = $request->file('file_upload_' . $i);    

                    $name_origin = $file->getClientOriginalName();
                    // $name = str_replace(',', '-', $file->getClientOriginalName());
                    $name = preg_replace('/[^A-Za-z0-9\s](?=\.[^.]+$)/', ' ',  $name_origin);
                    $extension = $file->getClientOriginalExtension();                    

                    $filename = str_replace(',', ' ', $filename);                    
                    
                    // $docsname = $fy . '-' . $file_id . '-' . $file_category .'-'. strtoupper($filename) . '-' . $i . '-' . $name;                    
                    
                                        
                    $check_file_exist = db::connection('ympimis_2')->table('file_documents')
                    ->where('file_id', $file_id)
                    ->where('docs_name_origin', $name_origin)
                    ->count();

                    if($check_file_exist > 0) {
                        $increment = $check_file_exist + 1;
                    } else {
                        $increment = 1;
                    }
                                                                
                    $docsname = $file_id . '_[' . $increment . ']_' . $file_category . '_' . $name;

                    

                    $file = $file->move(public_path($file_destination), $docsname);
                    array_push($docsnames, $docsname);
                                                                 

                    $docs_attachments = db::connection('ympimis_2')->table('file_documents')
                    ->insert([
                        'file_id' => $file_id,
                        'docs_name_origin' => $name_origin,
                        'docs_name' => $docsname,
                        'docs_size' => $file->getSize(),                            
                        'file_name' => $filename,
                        'file_category' => $file_category,
                        'file_pic' => $request->get('file_pic'),                            
                        'file_url' => $file_destination . '/' . $docsname,
                        'fiscal_year' => $fy,
                        'period' => $request->get('period'),
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);                                                                            
                }                    
                $arr_files = json_encode($docsnames);                

                DB::connection('ympimis_2')->commit();

            } catch (\Throwable $th) {
                DB::connection('ympimis_2')->rollback();
                return response()->json([
                    'status' => 'error',
                    'message' => 'File Upload Failed'                    
                ]);                
            }
        } else {
            $arr_files = null;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'File Uploaded Successfully'            
            
        ]);        

    }

    public function deleteAttachment(Request $request)
    {        
        try {
            $file_docs = db::connection('ympimis_2')->table('file_documents')
            ->where('file_id', $request->get('id'))
            ->where ('docs_name_origin' , $request->get('docs_name_origin'));
            
            $docs_delete = $file_docs
            ->select('docs_name')
            ->first();

            $docs_title = $docs_delete->docs_name;            

            $delete_local_docs = Storage::delete($this->FOLDER_PATH .'/'. $docs_title);
            $file_delete = $file_docs->delete();

            $respond = array(
                'status' => 'success',
                'message' => 'File Deleted',                
            );
        } catch (\Exception $e) {
            $respond = array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }

        return response()->json($respond);
        
    }

    public function dueDateControll(Request $request)
    {
        try {
            $id = $request->get('id');
            $newDueDate = $request->get('due_date');
            $status = $request->get('status');            

            if($status == 'Done') {
                $newDueDate = null;
            } else if ($status == 'Extend'){
                $newDueDate = $newDueDate;
            }            

            $file = db::connection('ympimis_2')->table('file_manager')
            ->where('id', $id)
            ->update([
                'due_date' => $newDueDate,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $respond = array(
                'status' => 'success',
                'message' => 'Due Date Updated'
            );
        } catch (\Exception $e) {
            $respond = array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
        }

        return response()->json($respond);
    }

    public function editFile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'file_category' => 'required',
                'file_name' => 'required',
                'file_pic' => 'required',
                'fiscal_year' => 'required',                                  
            ]);

            if ($validator->fails()) {
                $respond = array(
                    'status' => false,
                    'message' => $validator->errors()->all()
                );                
                return response()->json($respond);
            }            

            $id = $request->get('id');
            $fiscal_year = $request->get('fiscal_year');
            $file_category = $request->get('file_category');
            $file_name = $request->get('file_name');
            $file_pic = $request->get('file_pic');
            $period = $request->get('period');
            $letter_no = $request->get('letter_no');
            $letter_date = $request->get('letter_date');
            $received_date = $request->get('received_date');
            $due_date = $request->get('due_date');
            $remark = $request->get('remark');

            $file = db::connection('ympimis_2')->table('file_manager')
            ->where('id', $id)
            ->update([
                'fiscal_year' => $fiscal_year,
                'file_category' => $file_category,
                'file_name' => $file_name,
                'file_pic' => $file_pic,
                'period' => $period,
                'letter_no' => $letter_no,
                'letter_date' => $letter_date,
                'received_date' => $received_date,
                'due_date' => $due_date,
                'remark' => $remark,
                'updated_at' => date('Y-m-d H:i:s')
            ]);            

            $respond = array(
                'status' => 'error',
                'message' => 'File '.$id.' Updated'
            );

            return response()->json($respond);


        } catch (\Exception $e) {
            $respond = array(
                'status' => 'error',
                'message' => $e->getMessage()
            );
            return response()->json($respond);
        }
    }

    public function uploadFile(Request $request){                

        $validator = Validator::make($request->all(), [
            'file_category' => 'required',                
            'file_name' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
            'file_pic' => 'required',
            'fiscal_year' => 'required',                
            'file_upload_*' => 'required',
            // 'file_upload_*' => 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480'
        ]);

        if ($validator->fails()) {
            $respond = array(
                'status' => 'error',
                'message' => $validator->errors()->all()
            );
            // return response()->json($respond); with 500 error
            return response()->json($respond, 400);            
        }
        
        DB::beginTransaction();
        try {            
            $files = array();
            $file = new Files();
            $arr_files = [];                        
                        
            $filename = str_replace(',', '-', $request->get('file_name'));

            $code_generator = CodeGenerator::where('note', 'file_manager')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $file_id = $code_generator->prefix . $number;        

            $file_destination = $this->FOLDER_PATH;
            $docsnames = array();

            $file_category = $request->get('file_category');
            $fy = $request->get('fiscal_year');
            $period = $request->get('period');                           

            if($request->get('att_count') > 0) {                         
                    $files_attachment = db::connection('ympimis_2')->table('file_manager')
                    ->insert([                        
                        'file_name' => $filename,
                        'file_category' => $file_category,                                                
                        'file_pic' => $request->get('file_pic'),                        
                        'fiscal_year' => $fy,
                        'period' => $period,
                        'letter_no' => $request->get('letter_no'),
                        'due_date' => $request->get('due_date'),
                        'remark' => $request->get('remark'),
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);    
                    
                    for ($i=0; $i < $request->get('att_count'); $i++) { 
                        $file = $request->file('file_upload_' . $i);    

                        $name_origin = $file->getClientOriginalName();
                        // $name = str_replace(' ', '', $file->getClientOriginalName());                    
                        $extension = $file->getClientOriginalExtension();

                        $file_id = db::connection('ympimis_2')->table('file_manager')
                        ->latest('id')->value('id');   

                        // $name = str_replace(',', '-', $file->getClientOriginalName());                        
                        // $filename = str_replace(',', '-', $filename);                                    
                        // $docsname = $fy . '-' . $file_id . '-' . $file_category .'-'. strtoupper($filename) . '-' . $i . '-' . $name;                        
                        // $name = str_replace(',', '-', $file->getClientOriginalName());

                        $name = preg_replace('/[^A-Za-z0-9\s](?=\.[^.]+$)/', ' ',  $name_origin);

                        $check_file_exist = db::connection('ympimis_2')->table('file_documents')
                        ->where('file_id', $file_id)
                        ->where('docs_name_origin', $name_origin)
                        ->count();

                        if($check_file_exist > 0) {
                            $increment = $check_file_exist + 1;
                        } else {
                            $increment = 1;
                        }
                                                                    
                        $docsname = $file_id . '_[' . $increment . ']_' . $file_category . '_' . $name;

                        $file = $file->move(public_path($file_destination), $docsname);
                        array_push($docsnames, $docsname);
                                                                     

                        $docs_attachments = db::connection('ympimis_2')->table('file_documents')
                        ->insert([
                            'file_id' => $file_id,
                            'docs_name_origin' => $name_origin,
                            'docs_name' => $docsname,
                            'docs_size' => $file->getSize(),
                            'file_name' => $filename,
                            'file_category' => $file_category,
                            'file_pic' => $request->get('file_pic'),                            
                            'file_url' => $file_destination . '/' . $docsname,
                            'fiscal_year' => $fy,
                            'period' => $period,                            
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);                                                                            
                    }                    
                    $arr_files = json_encode($docsnames);
                    $code_generator->index = $code_generator->index + 1;                
            } else {
                $arr_files = null;
            }

            DB::commit();
        return response()->json([
            'status' => 'success',
            'message' => 'File Uploaded Successfully'            
            
        ]);        
        
        } catch (\Exception $th) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage()
            ]);
        }                        
    	

    }

    public function addCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'category_class' => 'required',
        ]);        

        if ($validator->fails()) {
            $respond = array(
                'status' => '500',
                'message' => $validator->errors()->all()
            );
            return response()->json($respond);
        }

        $parent_id = $request->get('parent_id');
        $category_class = $request->get('category_class');        

        if ($parent_id == null) {
            $parent_id = 0;
        }        

        if($category_class == '') {
            $category_class = 'CONFIDENTIAL';
        }

        $category = db::connection('ympimis_2')->table('file_category')->updateOrInsert([
            'category_name' => $request->get('category_name'),
            'category_class' => $category_class,
            'parent_id' => $parent_id,
            'created_by' => Auth::id(),            
        ]);

        $respond = array(
            'status' => 'success',
            'message' => 'Category Added Successfully'
        );
        
        return response()->json($respond);
    }

    public function updateSubCategory(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'parent_id' => 'required',
        ]);

        if ($validator->fails()) {
            $respond = array(
                'status' => '500',
                'message' => $validator->errors()->all()
            );
            return response()->json($respond);
        }

        $id = $request->get('id');
        $parent_id = $request->get('parent_id');

        $category = db::connection('ympimis_2')->table('file_category')->where('id', $id)->update([
            'parent_id' => $parent_id,            
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $respond = array(
            'status' => 'success',
            'message' => 'Category Updated Successfully'
        );
        
        return response()->json($respond);
    }

    public function deleteCategory(Request $request)
    {
        $id = $request->get('id');
        $category = db::connection('ympimis_2')->table('file_category')->where('id', $id)->delete(); 
        
        $respond = array(
            'status' => 'success',
            'message' => 'Category Deleted Successfully'
        );

        return response()->json($respond);
    }


    public function deleteFile(Request $request)
    {
        $id = $request->get('id');
        $file = db::connection('ympimis_2')->table('file_manager')->where('id', $id)->delete(); 
        $docs = db::connection('ympimis_2')->table('file_documents')->where('file_id', $id)->delete();        
        
        $respond = array(
            'status' => 'success',
            'message' => 'File Deleted Successfully'
        );

        return response()->json($respond);
    }

    public function fetchDocumentControl()
    {        
        // $total_monitor = DB::connection('ympimis_2')->table('file_manager')
        // ->select('file_category','fiscal_year', DB::raw('count(*) as total'))            
        // // ->groupBy('id', 'file_category','fiscal_year')
        // ->groupBy('fiscal_year', 'file_category')

        // ->get();        
                
        $directories = Storage::disk('ftp')->allFiles('/');

        dd($directories);
        
        
        $respond = array(
            'status' => 'success',
            // 'file_monitor' => $total_monitor
            'storage_files' => $storage_files
        );

        return response()->json($respond);
    }

    public function sendEmail(Request $request) {
        // dd($request->all());            

        // validator
        $validate = Validator::make($request->all(), [
            'file_id' => 'required',
            'recipient' => 'required',
            'email_subject' => 'required',
            'email_body' => 'required',
        ]);

        if ($validate->fails()) {
            $respond = array(
                'status' => false,
                'message' => $validate->errors()->all()[0]
            );
            return response()->json($respond);
        }

        try {
            $id = $request->get('file_id');        
            $recipient = $request->get('recipient');
            $email_subject = $request->get('email_subject');
            $email_body = $request->get('email_body');

            $data_file = DB::connection('ympimis_2')->table('file_manager')
            ->where('id', $id)
            ->get();

            $data_docs = DB::connection('ympimis_2')->table('file_documents')
            ->where('file_id', $id)
            ->get();

            $data_all = array(
                'title' => 'Attachment File',
                'email_subject' => $email_subject,
                'email_body' => $email_body,
                'data_file' => $data_file,
                'data_docs' => $data_docs
            );                    

            Mail::to($recipient)
            ->bcc(['fakhrizal.ihza.mahendra@music.yamaha.com',])  
            ->send(new SendEmail($data_all, 'document_control_system'));

            $respond = array(
                'status' => true,
                'message' => 'Email Sent Successfully'
            );

            return response()->json($respond);           
        } catch (\Throwable $th) {
            $respond = array(
                'status' => false,
                'message' => $th->getMessage()
            );
            return response()->json($respond);            
        }        

    }



    // end of class
}