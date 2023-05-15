<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::get('test_print_penarikan', 'TrialController@test_print_penarikan');
Route::get('testCOM', 'TrialController@testCOM');
Route::get('test_payslip', 'TrialController@testPaySlip');
Route::get('test_reverse', 'TrialController@testReverse');
Route::get('input/temp_solder', 'TrialController@tesSuhu');
Route::get('print_qr', 'TrialController@print_qr');

Route::get('generate_att', 'TrialController@generateAtt');
Route::get('ocr', 'TrialController@ocr');
Route::get('home_new', 'TrialController@home_new');
Route::get('adjust_shipment', 'TrialController@adjustShipment');
Route::get('mail_test', 'TrialController@testmailnew');
Route::get('ymes_function', 'TrialController@ymes_functions');
Route::get('update_st', 'TrialController@updateSt');

Route::get('/clear-cache', function () {
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    return "Cache is cleared";
});

Route::get('fetch/notification', 'NotificationController@fetchNotification');

Route::get('video_pak_ura', 'TrialController@indexVideoPakUra');
Route::get('trial_file', 'TrialController@indexFileProtected');
Route::post('create/trial_file', 'TrialController@createFileProtected');
Route::get('video/video_pak_ura', 'TrialController@fetchVideoPakUra');
Route::get('trial/check_password', 'TrialController@checkPassword');
Route::get('trial/whatsapp_api', 'TrialController@whatsapp_api');

Route::get('trial/sandbox', 'TrialController@IndexSandboxHentong');

Route::get('twibbon', function () {
    return view('trials.twibbon');
});

Route::get('let', function () {
    return view('trials.display_graduation');
});

Route::get('test_kanban', 'TrialController@test_kanban');
Route::get('trial_sunfish_api', 'TrialController@trialSfAPI');

Route::get('happybirthday', function () {
    return view('trials.birthday');
});

Route::get('custom_display', function () {
    return view('trials.custom_display');
});

Route::get('aeo_landscape', function () {
    return view('trials.aeo_landscape');
});

Route::get('aeo_potrait', function () {
    return view('trials.aeo_potrait');
});

Route::get('happybirthday2', function () {
    return view('trials.birthday2');
});

Route::get('jepang', function () {
    return view('trials.jepang');
});

Route::get('jepang2', function () {
    return view('trials.jepang2');
});

Route::get('indonesia', function () {
    return view('trials.indonesia');
});

Route::get('trialmail', 'TrialController@trialymes');
Route::get('trial', function () {
    return view('trial');
});
Route::get('/trial2', function () {
    return view('trial2');
});

Route::get('/index/apar/print', function () {
    return view('maintenance/apar/aparPrint');
});

Route::get('/qr', function () {
    return view('maintenance/apar/aparQr');
});

Route::get('/information_board', function () {
    return view('information_board')->with('title', 'INFORMATION BOARD')->with('title_jp', '情報板');
});

Auth::routes();

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role_code == 'emp-srv') {
            // return redirect()->action('EmployeeController@indexEmployeeService', ['id' => 1]);
            return \redirect()->route('emp_service', ['id' => 1, 'tahun' => date('Y')]);
            // return redirect()->route('index/employee/service/{ctg}', ['ctg' => 'home']);
        } else if (Auth::user()->role_code == 'WINDS') {
            return \redirect()->route('winds');
        } else if (Auth::user()->role_code == 'BUYER') {
            return \redirect()->route('index/extra_order');
        } else if (Auth::user()->role_code == 'GS') {
            return \redirect()->route('gscontrol');
        } else {
            // return redirect()->intended();
            return view('home');
        }
    } else {
        return view('auth.login');
    }
});

Route::get('404', function () {
    return view('404');
});

//PASSWORD
Route::get('forgot', 'PasswordController@forgotPassword');

//YMES
Route::get('index/ymes/inventory', 'YMESController@indexInventory');
Route::get('index/ymes/interface_error', 'YMESController@indexInterfaceError');
Route::get('fetch/ymes/inventory_check', 'YMESController@fetchInventoryCheck');

Route::group(['nav' => 'T1', 'middleware' => 'permission'], function () {
    Route::get('index/ymes/production_result', 'YMESController@indexProductionResult');
    Route::post('input/ymes/goods_movement', 'YMESController@inputGoodsMovement');
    Route::get('index/ymes/goods_movement', 'YMESController@indexGoodsMovement');
    Route::post('input/ymes/production_result', 'YMESController@inputProductionResult');

    Route::get('index/ymes/production_result_temporary', 'YMESController@indexProductionResultTemporary');
    Route::get('fetch/ymes/production_result_temporary', 'YMESController@fetchProductionResultTemporary');
    Route::post('sync/ymes/production_result_temporary', 'YMESController@syncTemporary');
    Route::post('sync/ymes/all_production_result_temporary', 'YMESController@syncAllTemporary');

    Route::get('ymes_error', function () {
        \Artisan::call('ymes:error');
        return view('transactions.error', array(
            'title' => "Transaction Error",
            'title_jp' => "",
        ))->with('page', 'Transaction Error')->with('head', 'Transaction');
    });

    Route::get('ymes_error_interface', function () {
        \Artisan::call('ymes:error_interface');
        return view('transactions.error', array(
            'title' => "Transaction Error",
            'title_jp' => "",
        ))->with('page', 'Transaction Error')->with('head', 'Transaction');
    });

    Route::get('index/ymes/error', 'YMESController@indexError');
    Route::get('fetch/ymes/error', 'YMESController@fetchError');

});

Route::group(['nav' => 'T2', 'middleware' => 'permission'], function () {
    Route::get('index/ymes/history', 'YMESController@indexHistory');
    Route::get('fetch/ymes/history', 'YMESController@fetchHistory');
    Route::get('index/ymes/history_shipment', 'YMESController@indexHistoryShipment');
    Route::get('fetch/ymes/history_shipment', 'YMESController@fetchHistoryShipment');
});

Route::group(['nav' => 'T3', 'middleware' => 'permission'], function () {

});

Route::group(['nav' => 'T4', 'middleware' => 'permission'], function () {

});

Route::get('fetch/bom_multi_level', 'YMESController@fetchBomMultilevel');

//STANDARDIZATION DOCUMENT CONTROL
Route::get('index/standardization/document_index', 'StandardizationController@indexDocument');
Route::get('fetch/standardization/document', 'StandardizationController@fetchDocument');
Route::get('index/standardization/document_publish', 'StandardizationController@indexDocumentPublish');

Route::get('index/standardization/emergency', 'StandardizationController@indexEmergency');
Route::get('fetch/standardization/emergency', 'StandardizationController@fetchEmergency');
Route::post('scan/standardization/emergency', 'StandardizationController@scanEmergency');

Route::group(['nav' => 'A15', 'middleware' => 'permission'], function () {
    // Route::post('input/standardization/document', 'StandardizationController@inputDocument');
    // Route::post('edit/standardization/document', 'StandardizationController@editDocument');
    // Route::post('version/standardization/document', 'StandardizationController@versionDocument');
    // Route::post('delete/standardization/document', 'StandardizationController@versionDocument');

});

//SMALL GROUP ACTIVITY
Route::get('index/sga', 'StandardizationController@indexSga');

Route::group(['nav' => 'S81', 'middleware' => 'permission'], function () {
    Route::get('index/sga/master', 'StandardizationController@indexSgaMaster');
    Route::get('fetch/sga/master', 'StandardizationController@fetchSgaMaster');
    Route::get('download/sga/master', 'StandardizationController@downloadSgaMaster');
    Route::post('upload/sga/master', 'StandardizationController@uploadSgaMaster');
    Route::get('delete/sga/master', 'StandardizationController@deleteSgaMaster');
    Route::get('edit/sga/master', 'StandardizationController@editSgaMaster');
    Route::post('update/sga/master', 'StandardizationController@updateSgaMaster');

    Route::get('index/sga/assessment', 'StandardizationController@indexSgaAssessment');
    Route::get('fetch/sga/point', 'StandardizationController@fetchSgaPoint');
    Route::post('input/sga/assessment/temp', 'StandardizationController@inputSgaAssessmentTemp');
    Route::post('input/sga/assessment/result', 'StandardizationController@inputSgaAssessmentResult');
    Route::post('input/sga/assessment', 'StandardizationController@inputSgaAssessment');
    Route::post('upload/sga/pdf', 'StandardizationController@uploadSgaPdf');
    Route::post('selection/sga/report', 'StandardizationController@selectionSgaReport');
    Route::get('approval/sga/report/{periode}/{remark}', 'StandardizationController@approvalSgaReport');
    Route::get('reject/sga/report/{periode}/{remark}', 'StandardizationController@rejectSgaReport');
    Route::get('reject/sga/report/reason', 'StandardizationController@rejectReasonSgaReport');
    Route::get('pdf/sga/report/{periode}', 'StandardizationController@pdfSgaReport');

    Route::get('index/sga/report', 'StandardizationController@indexSgaReport');
    Route::get('fetch/sga/report', 'StandardizationController@fetchSgaReport');

    Route::get('index/sga/point_check', 'StandardizationController@indexSgaPointCheck');
    Route::get('fetch/sga/point_check', 'StandardizationController@fetchSgaPointCheck');
    Route::post('input/sga/point_check', 'StandardizationController@inputSgaPointCheck');
    Route::post('update/sga/point_check', 'StandardizationController@updateSgaPointCheck');
    Route::get('delete/sga/point_check', 'StandardizationController@deleteSgaPointCheck');
});

Route::get('index/sga/monitoring', 'StandardizationController@indexSgaMonitoring');
Route::get('fetch/sga/monitoring', 'StandardizationController@fetchSgaMonitoring');

//ONLINE ATTENDANCE AND TRANSPORTAION
Route::get('fetch/general/online_transportation', 'GeneralController@fetchOnlineTransportation');
Route::post('input/general/online_transportation', 'GeneralController@inputOnlineTransportation');
Route::post('delete/general/online_transportation', 'GeneralController@deleteOnlineTransportation');
Route::get('fetch/general/online_transportation_data', 'GeneralController@fetchOnlineTransportationData');

//AGREEMENT
Route::get('index/general/agreement', 'GeneralController@indexAgreement');
Route::get('fetch/general/agreement', 'GeneralController@fetchAgreement');
Route::get('fetch/general/agreement_detail', 'GeneralController@fetchAgreementDetail');
Route::get('fetch/general/agreement_download', 'GeneralController@fetchAgreementDownload');
Route::get('download/general/agreement', 'GeneralController@downloadAgreement');
Route::post('create/general/agreement', 'GeneralController@createAgreement');
Route::post('edit/general/agreement', 'GeneralController@editAgreement');
Route::get('index/general/regulation', 'GeneralController@indexRegulation');

Route::post('create/general/regulation', 'GeneralController@createRegulation');
Route::get('fetch/general/regulation', 'GeneralController@fetchRegulation');
Route::post('edit/general/regulation', 'GeneralController@editRegulation');
Route::get('download/general/regulation', 'GeneralController@downloadRegulation');

//SURAT DOKTER
Route::get('fetch/general/surat_dokter', 'GeneralController@fetchSuratDokter');
Route::post('input/general/surat_dokter', 'GeneralController@inputSuratDokter');
Route::post('delete/general/surat_dokter', 'GeneralController@deleteSuratDokter');

//GENERAL

//SMART RECRUITMENT
Route::get('index/hr/request_manpower_update', 'HumanResourceController@indexRequestManpower');
Route::get('input/hr/request_manpower', 'HumanResourceController@inputRequestManpower');
Route::get('admin/hr/request_manpower', 'HumanResourceController@adminRequestManpower');
Route::get('monitoring/man_power', 'HumanResourceController@MonitoringManPower');
Route::get('fetch/man_power', 'HumanResourceController@FetchMonitoringManPower');
Route::post('import/calon/kryawan', 'HumanResourceController@ImportCalonKaryawan');
Route::get('input/veteran/request', 'HumanResourceController@VeteranRequestEmployeeInsert');

Route::get('monitoring/request/manpower', 'HumanResourceController@MonitoringRequestManpower');
Route::get('fetch/monitoring/request', 'HumanResourceController@fetchMonitoringReqManpower');

Route::get('index/smartrecruitment', 'HumanResourceController@UserRequest');
Route::get('fetch/user/grafik', 'HumanResourceController@fetchGrafikUser');

Route::get('fetch/grafik/tunjangan', 'HumanResourceController@fetchGrafikTunjangan');

Route::get('index/input/nilai/{department}', 'HumanResourceController@KaryawanEndContract');
Route::get('fetch/input/nilai/end/contract', 'HumanResourceController@FetchKaryawanEndContract');
Route::get('index/pengganti/{department}', 'HumanResourceController@IndexPenggantiMp');

Route::get('index/trial/grafik', 'HumanResourceController@indexTrialGrafik');
Route::get('fetch/trial/grafik', 'HumanResourceController@fetchTrialGrafik');

Route::get('index/veteran/employee', 'HumanResourceController@indexVeteranEmployee');
Route::get('fetch/veteran/employee', 'HumanResourceController@fetchVeteranEmployee');
Route::get('fetch/veteran/employee/detail', 'HumanResourceController@fetchVeteranEmployeeDetail');

Route::post('input/veteran/employee', 'HumanResourceController@inputVeteranEmployee');
Route::get('select/veteran/employee', 'HumanResourceController@SelectNikVeteran');
Route::get('select/section/new', 'HumanResourceController@SelectSectionNew');
Route::get('select/group/new', 'HumanResourceController@SelectGroupNew');
Route::get('select/sub_group/new', 'HumanResourceController@SelectSubGroupNew');

Route::get('monitoring/kebutuhan/manpower', 'HumanResourceController@IndexKebutuhanMp');
Route::get('fetch/kebutuhan/manpower', 'HumanResourceController@FetchChartKebutuhanMp');
Route::post('input/kebutuhan_mp', 'HumanResourceController@InputKebutuhanMp');
Route::post('upload/kebutuhan/mp', 'HumanResourceController@UploadKebutuhanMp');
Route::get('fetch/grafik/detail', 'HumanResourceController@fetchGrafikDetail');
Route::get('fetch/data/all', 'HumanResourceController@fetchDataAll');
Route::get('report/smart-recruitment/{request_id}', 'HumanResourceController@ReportPdfRecruitment');
Route::post('upload/calon/karyawan', 'HumanResourceController@UploadCalonKaryawan');
Route::post('upload/calon/karyawan/rekontrak', 'HumanResourceController@UploadCalonKaryawanRekontrak');
Route::post('upload/update/karyawan', 'HumanResourceController@UpdateKaryawanContracUpload');
Route::post('update/habis_kontrak', 'HumanResourceController@UpdateDurasiKontrak');

Route::get('karyawan/kontrak', 'HumanResourceController@IndexKaryawanKontrak');
Route::get('fetch/data/karyawan/kontrak', 'HumanResourceController@FetchKaryawanKontrak');
Route::get('update/karyawan/contract', 'HumanResourceController@UpdateKaryawanContrac');
Route::get('update/status/confirm', 'HumanResourceController@UpdateStatusConfirm');

Route::get('recruitment/approval/sign/{request_id}/{approver_id}', 'HumanResourceController@SignApproval');
Route::get('tunjangan/approval/sign/{request_id}/{approver_id}', 'HumanResourceController@SignApprovalTunjangan');
Route::get('input/request/magang', 'HumanResourceController@RequestMagang');

Route::post('upload/mp/interview', 'HumanResourceController@UploadMpInterview');
Route::get('fetch/mp/interview', 'HumanResourceController@fetchMpInterview');

Route::post('update/mp/magang', 'HumanResourceController@UpdateMpMagang');
Route::get('fetch/mp/magang', 'HumanResourceController@fetchMpMagang');

Route::post('update/mp/stock', 'HumanResourceController@UpdateMpStock');
Route::get('fetch/mp/stock', 'HumanResourceController@fetchMpStock');

Route::post('test/data/employee', 'HumanResourceController@indexCoba');

// Create Update TAG Karyawan
Route::get('index/tags/employee', 'HumanResourceController@indexTagsEmployee');
Route::get('fetch/tags/employee', 'HumanResourceController@fetchTagsEmployee');
Route::get('scan/tags/employee', 'HumanResourceController@scanTagsEmployee');
Route::post('scan/tags/employee/register', 'HumanResourceController@scanTagsEmployeeRegister');
Route::post('scan/tags/employee/assign', 'HumanResourceController@scanTagsEmployeeAssign');
Route::post('delete/tags/employee', 'HumanResourceController@deleteTagsEmployee');

// Route::get('/home', ['middleware' => 'permission', 'nav' => 'Dashboard', 'uses' => 'HomeController@index'])->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home2', 'HomeController@index2');
Route::get('/home3', 'HomeController@index3');

Route::get('/about_mis', 'HomeController@indexAboutMIS');
Route::get('/project_timeline', 'HomeController@indexProjectTimeline');
Route::get('/fetch/mis_investment', 'HomeController@fetch_mis_investment');
Route::get('download/manual/{reference_file}', 'HomeController@download');

//Vistor Controll
Route::get('visitor_index', 'VisitorController@index');
Route::get('visitor_registration', 'VisitorController@registration');
Route::get('simpan', 'VisitorController@simpanheader');
Route::get('visitor_list', 'VisitorController@receive');
Route::get('visitor_telpon', 'VisitorController@telpon');
Route::get('visitor_filllist/{nik}', 'VisitorController@filllist');
Route::get('visitor_getlist', 'VisitorController@editlist');
Route::post('visitor_inputtag', 'VisitorController@inputtag');
Route::get('visitor_confirmation', 'VisitorController@confirmation');
Route::get('visitor_confirmation/phone_list', 'VisitorController@confirmationPhoneList');
Route::post('visitor_updateremark', 'VisitorController@updateremark');
Route::post('visitor_updateremarkall', 'VisitorController@updateremarkall');
Route::get('visitor_confirm_manager/{id}', 'VisitorController@confirm_manager');
Route::get('visitor_leave', 'VisitorController@leave');
Route::get('visitor_getvisit', 'VisitorController@getvisit');
Route::post('visitor_out', 'VisitorController@out');
Route::get('visitor_getdata', 'VisitorController@getdata');
Route::get('visitor_display', 'VisitorController@display');
Route::get('visitor_filldisplay/{nik}', 'VisitorController@filldisplay');
Route::get('visitor_getchart', 'VisitorController@getchart');

Route::get('index/visitor/report', 'VisitorController@indexVisitorReport');
Route::get('fetch/visitor/report', 'VisitorController@fetchVisitorReport');

Route::get('visitor_getvisitSc', 'VisitorController@confirmation2');

Route::get('visitor_confirmation_manager', 'VisitorController@confirmation_manager');
Route::get('fetch/visitor/fetchVisitorByManager', 'VisitorController@fetchVisitorByManager');
Route::get('scan/visitor/lobby', 'VisitorController@scanVisitorLobby');

Route::get('index/visitor/emp_confirmation', 'VisitorController@indexEmpConfirmation');
Route::get('fetch/visitor/emp_confirmation', 'VisitorController@fetchEmpConfirmation');
Route::get('visitor_confirm_to_manager/{id}', 'VisitorController@confirm_to_manager');

//end visitor control

//HIKVISION TEMPERATURE
Route::group(['nav' => 'S57', 'middleware' => 'permission'], function () {
    Route::get('index/temperature/minmoe', 'TemperatureController@indexMinMoe');
    Route::get('fetch/temperature/minmoe', 'TemperatureController@fetchMinMoe');
    Route::post('import/temperature/minmoe', 'TemperatureController@importMinMoe');
});

Route::get('index/temperature/minmoe_monitoring/{location}', 'TemperatureController@indexMinMoeMonitoring');
Route::get('fetch/temperature/minmoe_monitoring', 'TemperatureController@fetchMinMoeMonitoring');
Route::get('fetch/temperature/detail_minmoe_monitoring', 'TemperatureController@fetchDetailMinMoeMonitoring');

//----- Start mesin injeksi
Route::get('scan/injeksi/operator', 'InjectionsController@scanInjectionOperator');
Route::get('index/injeksi', 'InjectionsController@index');
Route::get('index/injeksi/get_temp', 'InjectionsController@get_temp');
Route::post('index/injeksi/create_temp', 'InjectionsController@create_temp');
Route::post('index/injeksi/update_tag', 'InjectionsController@update_tag');
Route::post('index/injeksi/update_temp', 'InjectionsController@update_temp');
Route::post('index/injeksi/create_log', 'InjectionsController@create_log');
Route::post('input/injeksi/mesin_log', 'InjectionsController@inputMesinLog');
Route::post('index/injeksi/store_ng', 'InjectionsController@store_ng');
Route::post('index/injeksi/store_ng_temp', 'InjectionsController@store_ng_temp');
Route::post('index/injeksi/update_ng_temp', 'InjectionsController@update_ng_temp');
Route::post('index/injeksi/store_molding_log', 'InjectionsController@store_molding_log');
Route::get('index/injeksi/get_ng_temp', 'InjectionsController@get_ng_temp');
Route::get('index/injeksi/get_molding_log', 'InjectionsController@get_molding_log');
Route::post('index/injeksi/delete_ng_temp', 'InjectionsController@delete_ng_temp');
Route::get('input/reason_idle_trouble', 'InjectionsController@inputReasonIdleTrouble');
Route::get('change/reason_idle_trouble', 'InjectionsController@changeReasonIdleTrouble');
Route::get('input/reason_pause', 'InjectionsController@inputReasonPause');
Route::get('input/approval_cek', 'InjectionsController@inputApprovalCek');
Route::get('change/reason_pause', 'InjectionsController@changeReasonPause');

//in
Route::get('index/in', 'InjectionsController@in');
Route::post('scan/part_injeksi', 'InjectionsController@scanPartInjeksi');
Route::get('scan/new_tag_injeksi', 'InjectionsController@scanNewTagInjeksi');
Route::get('fetch/new_product', 'InjectionsController@getNewProductCavity');
Route::get('scan/part_molding', 'InjectionsController@scanPartMolding');
Route::get('send/Part', 'InjectionsController@sendPart');
Route::get('get/Inpart', 'InjectionsController@getDataIn');
//end in

// out
Route::get('index/out', 'InjectionsController@out');
Route::get('get/Outpart', 'InjectionsController@getDataOut');
//end out

//Transaction
Route::get('index/injection/transaction/{status}', 'InjectionsController@transaction');
Route::get('scan/tag_product', 'InjectionsController@scanProduct');
Route::get('fetch/injection/transaction', 'InjectionsController@fetchTransaction');
Route::get('fetch/injection/detail_transaction', 'InjectionsController@fetchDetailTransaction');
Route::get('fetch/injection/check_injections', 'InjectionsController@fetchCheckInjections');
Route::get('fetch/injection/check_ng', 'InjectionsController@fetchCheckNg');
Route::post('index/injection/completion', 'InjectionsController@completion');
Route::post('index/injection/cancel_completion', 'InjectionsController@cancelCompletion');
//end

// ---- dailyStock
Route::get('index/dailyStock', 'InjectionsController@dailyStock');
Route::get('fetch/dailyStock', 'InjectionsController@getDailyStock');
// ---- end dailyStock
Route::get('fetch/InOutpart', 'InjectionsController@getDataInOut');

//schedule
Route::get('index/Schedule', 'InjectionsController@schedule');
Route::get('fetch/Schedulepart', 'InjectionsController@getSchedule');
Route::get('fetch/getStatusMesin', 'InjectionsController@getStatusMesin');

Route::get('fetch/getDateWorking', 'InjectionsController@getDateWorking');
Route::post('save/Schedule', 'InjectionsController@saveSchedule');

Route::post('save/Scheduletmp', 'InjectionsController@saveScheduleTmp');

Route::get('fetch/getChartPlan', 'InjectionsController@getChartPlan');

Route::get('fetch/percenMesin', 'InjectionsController@percenMesin');

Route::get('fetch/mjblue', 'InjectionsController@detailPartMJBlue');
Route::get('fetch/headblue', 'InjectionsController@detailPartHeadBlue');
Route::get('fetch/footblue', 'InjectionsController@detailPartFootBlue');
Route::get('fetch/blockblue', 'InjectionsController@detailPartBlockBlue');
Route::get('fetch/injeksiVsAssyBlue', 'InjectionsController@injeksiVsAssyBlue');

Route::get('fetch/injeksiVsAssyGreen', 'InjectionsController@injeksiVsAssyGreen');
Route::get('fetch/injeksiVsAssyPink', 'InjectionsController@injeksiVsAssyPink');
Route::get('fetch/injeksiVsAssyRed', 'InjectionsController@injeksiVsAssyRed');
Route::get('fetch/injeksiVsAssyBrown', 'InjectionsController@injeksiVsAssyBrown');
Route::get('fetch/injeksiVsAssyIvory', 'InjectionsController@injeksiVsAssyIvory');
Route::get('fetch/injeksiVsAssyYrf', 'InjectionsController@injeksiVsAssyYrf');

Route::get('fetch/chartWorkingMachine', 'InjectionsController@chartWorkingMachine');

//end schedule

//report stock

Route::get('index/reportStock', 'InjectionsController@reportStock');
Route::get('fetch/getDataStock', 'InjectionsController@getDataStock');

Route::get('index/MonhtlyStock', 'InjectionsController@indexMonhtlyStock');
Route::get('fetch/MonhtlyStock', 'InjectionsController@MonhtlyStock');

Route::get('fetch/MonhtlyStockAllYrf', 'InjectionsController@MonhtlyStockAllYrf');
Route::get('fetch/MonhtlyStockAll', 'InjectionsController@MonhtlyStockAll');
Route::get('fetch/MonhtlyStockHead', 'InjectionsController@MonhtlyStockHead');
Route::get('fetch/MonhtlyStockFoot', 'InjectionsController@MonhtlyStockFoot');
Route::get('fetch/MonhtlyStockBlock', 'InjectionsController@MonhtlyStockBlock');

Route::get('index/dailyNG', 'InjectionsController@indexDailyNG');
Route::get('fetch/dailyNG', 'InjectionsController@dailyNG');
Route::get('fetch/detailDailyNG', 'InjectionsController@detailDailyNG');

Route::get('index/molding_monitoring/{condition}', 'InjectionsController@index_molding_monitoring');
Route::get('fetch/molding_monitoring', 'InjectionsController@molding_monitoring');
Route::get('fetch/detail_molding_monitoring', 'InjectionsController@detail_molding_monitoring');

Route::get('index/molding_schedule', 'InjectionsController@index_molding_schedule');
Route::get('fetch/molding_schedule', 'InjectionsController@molding_schedule');

Route::get('index/injection/machine_monitoring', 'InjectionsController@indexMachineMonitoring');
Route::get('fetch/injection/machine_monitoring', 'InjectionsController@fetchMachineMonitoring');

Route::get('index/injection/stock_monitoring', 'InjectionsController@indexStockMonitoring');
Route::get('fetch/injection/stock_monitoring', 'InjectionsController@fetchStockMonitoring');

Route::get('index/injection/stock_monitoring/daily', 'InjectionsController@indexStockMonitoringDaily');
Route::get('fetch/injection/stock_monitoring/daily', 'InjectionsController@fetchStockMonitoringDaily');

Route::get('index/injection/stock_monitoring/monthly', 'InjectionsController@indexStockMonitoringMonthly');
Route::get('fetch/injection/stock_monitoring/monthly', 'InjectionsController@fetchStockMonitoringMonthly');

Route::get('index/injection/report_setup_molding', 'InjectionsController@indexReportSetupMolding');
Route::get('fetch/injection/report_setup_molding', 'InjectionsController@fetchReportSetupMolding');
Route::get('fetch/injection/report_setup_molding/timeline', 'InjectionsController@fetchReportSetupMoldingTimeline');

Route::get('index/injection/report_maintenance_molding', 'InjectionsController@indexReportMaintenanceMolding');
Route::get('fetch/injection/report_maintenance_molding', 'InjectionsController@fetchReportMaintenanceMolding');

//end report

// mesin
Route::get('index/mesin', 'InjectionsController@mesin');
Route::get('fetch/getDataMenit', 'InjectionsController@getDataMenit');
Route::get('fetch/getDataMesinShoot', 'InjectionsController@getDataMesinShoot');

// end mesin

// operator
Route::get('index/injection_machine', 'InjectionsController@injection_machine');
Route::get('input/statusmesin', 'InjectionsController@inputStatusMesin');
Route::post('delete/statusmesin', 'InjectionsController@deleteStatusMesin');
Route::get('get/statusmesin', 'InjectionsController@getStatusMesin');

Route::post('input/logmesin', 'InjectionsController@logmesin');
Route::get('get/getDataMesinShootLog', 'InjectionsController@getDataMesinShootLog');
Route::get('get/getDataMesinStatusLog', 'InjectionsController@getDataMesinStatusLog');

// end operator

//report stock

Route::get('index/reportStockMonitoring', 'InjectionsController@reportStockMonitoring');
Route::get('fetch/getTargetWeek', 'InjectionsController@getTargetWeek');
//end report

//master machine
Route::get('index/masterMachine', 'InjectionsController@masterMachine');
Route::get('fetch/fillMasterMachine', 'InjectionsController@fillMasterMachine');
Route::get('fetch/editMasterMachine', 'InjectionsController@editMasterMachine');
Route::post('fetch/updateMasterMachine', 'InjectionsController@updateMasterMachine');
Route::post('fetch/addMasterMachine', 'InjectionsController@addMasterMachine');

Route::get('fetch/chartMasterMachine', 'InjectionsController@chartMasterMachine');

Route::get('index/masterCycleMachine', 'InjectionsController@masterCycleMachine');
Route::get('fetch/fillMasterCycleMachine', 'InjectionsController@fillMasterCycleMachine');
Route::get('fetch/chartMasterCycleMachine', 'InjectionsController@chartMasterCycleMachine');

Route::get('get/workingPartMesin', 'InjectionsController@workingPartMesin');
//end master machine

// ------------- start 3 hari

Route::get('index/indexPlanAll', 'InjectionsController@indexPlanAll');
Route::get('fetch/getPlanAll', 'InjectionsController@getPlanAll');

// ------------- end start 3 hari

//molding injection

Route::get('index/injection/molding', 'InjectionsController@molding');
Route::get('get/injeksi/get_molding', 'InjectionsController@get_molding');
Route::get('get/injeksi/get_molding_pasang', 'InjectionsController@get_molding_pasang');
Route::get('fetch/injeksi/fetch_molding', 'InjectionsController@fetch_molding');
Route::get('fetch/injeksi/fetch_molding_pasang', 'InjectionsController@fetch_molding_pasang');
Route::post('index/injeksi/store_history_temp', 'InjectionsController@store_history_temp');
Route::get('index/injeksi/get_history_temp', 'InjectionsController@get_history_temp');
Route::post('index/injeksi/update_history_temp', 'InjectionsController@update_history_temp');
Route::post('index/injeksi/store_history_molding', 'InjectionsController@store_history_molding');
Route::post('index/injeksi/cancel_history_molding', 'InjectionsController@cancel_history_molding');

//end molding injection

//maintenance molding injection

Route::get('index/injection/molding_maintenance', 'InjectionsController@molding_maintenance');
Route::get('get/injeksi/get_molding_master', 'InjectionsController@get_molding_master');
Route::get('fetch/injeksi/fetch_molding_master', 'InjectionsController@fetch_molding_master');
Route::post('index/injeksi/store_maintenance_temp', 'InjectionsController@store_maintenance_temp');
Route::get('index/injeksi/get_maintenance_temp', 'InjectionsController@get_maintenance_temp');
Route::post('index/injeksi/update_maintenance_temp', 'InjectionsController@update_maintenance_temp');
Route::post('index/injeksi/store_maintenance_molding', 'InjectionsController@store_maintenance_molding');
Route::post('input/injeksi/input_pause', 'InjectionsController@inputReasonPauseMaintenance');
Route::post('change/injeksi/change_pause', 'InjectionsController@changeReasonPauseMaintenance');

//end maintenance molding injection

//dryer injeksi

Route::get('index/injection/dryer_resin', 'InjectionsController@indexDryerResin');
Route::get('index/injection/fetch_resin', 'InjectionsController@fetchListResin');
Route::get('index/injection/fetch_resume_resin', 'InjectionsController@fetchResumeResin');
Route::post('input/injection/resin', 'InjectionsController@inputResin');
Route::get('index/injection/fetch_dryer', 'InjectionsController@fetchDryer');
Route::post('index/injection/update_dryer', 'InjectionsController@updateDryer');

//end dryer injeksi

//input stok

Route::get('index/input_stock', 'InjectionsController@indexInputStock');
Route::get('fetch/injection/stock', 'InjectionsController@fetchInputStock');
Route::post('input/injection/stock', 'InjectionsController@inputStock');

// injection schedule
Route::get('index/injection_schedule', 'InjectionsController@indexInjectionSchedule');
Route::get('fetch/injection_schedule', 'InjectionsController@fetchInjectionSchedule');
Route::get('fetch/injection_schedule/adjustment', 'InjectionsController@fetchInjectionScheduleAdjustment');
Route::get('adjust/injection_schedule/adjustment', 'InjectionsController@adjustInjectionScheduleAdjustment');

Route::group(['nav' => 'M31', 'middleware' => 'permission'], function () {
    Route::get('index/injection/tag', 'InjectionsController@indexInjectionTag');
    Route::get('fetch/injection/tag', 'InjectionsController@fetchInjectionTag');
    Route::get('fetch/injection/material', 'InjectionsController@fetchInjectionMaterial');
    Route::get('fetch/injection/material_edit', 'InjectionsController@fetchInjectionMaterialEdit');
    Route::post('input/injection/tag', 'InjectionsController@inputInjectionTag');
    Route::get('edit/injection/tag', 'InjectionsController@editInjectionTag');
    Route::post('update/injection/tag', 'InjectionsController@updateInjectionTag');
    Route::get('delete/injection/tag/{id}', 'InjectionsController@deleteInjectionTag');
});
Route::post('remove/injection/tag', 'InjectionsController@removeInjectionTag');
Route::get('fetch/injection/clean_kanban', 'InjectionsController@fetchInjectionCleanKanban');

Route::get('index/injection/traceability', 'InjectionsController@indexInjectionTraceability');
Route::get('fetch/injection/traceability', 'InjectionsController@fetchInjectionTraceability');

Route::get('index/injection/inventories/{loc}', 'InjectionsController@indexInjectionInventories');
Route::get('fetch/injection/inventories', 'InjectionsController@fetchInjectionInventories');
Route::get('update/injection/inventories', 'InjectionsController@updateInjectionInventories');

Route::get('index/injection/transactions', 'InjectionsController@indexInjectionTransaction');
Route::get('fetch/injection/transactions', 'InjectionsController@fetchInjectionTransaction');

Route::get('index/injection/ng_rate', 'InjectionsController@indexInjectionNgRate');
Route::get('fetch/injection/ng_rate', 'InjectionsController@fetchInjectionNgRate');
Route::get('input/injection/training_document', 'InjectionsController@inputInjectionDocument');
Route::get('input/injection/training_document/qa', 'InjectionsController@inputInjectionDocumentQa');
Route::post('input/injection/counceling', 'InjectionsController@inputInjectionCounceling');
Route::get('scan/injection/counceled_employee', 'InjectionsController@scanInjectionCounceledEmployee');
Route::get('scan/injection/counceled_by', 'InjectionsController@scanInjectionCounceledBy');

Route::get('index/injection/visual', 'InjectionsController@indexVisualCheck');
Route::get('fetch/injection/machine_work', 'InjectionsController@fetchMacineWork');
Route::post('input/injection/visual', 'InjectionsController@inputVisualCheck');
Route::get('index/injection/visual/monitoring', 'InjectionsController@indexVisualCheckMonitoring');
Route::get('fetch/injection/visual/monitoring', 'InjectionsController@fetchVisualCheckMonitoring');
Route::get('fetch/injection/visual/monitoring/detail', 'InjectionsController@fetchDetailVisualCheckMonitoring');
Route::get('approval/injection/visual/{id}', 'InjectionsController@approvalVisualCheck');

Route::get('index/injection/report_visual', 'InjectionsController@indexReportVisualCheck');
Route::get('fetch/injection/report_visual', 'InjectionsController@fetchReportVisualCheck');
Route::get('pdf/injection/report_visual/{date}/{part_type}', 'InjectionsController@pdfReportVisualCheck');

Route::get('index/injection/cleaning', 'InjectionsController@indexCleaning');
Route::get('fetch/injection/cleaning/point', 'InjectionsController@fetchCleaningPoint');
Route::get('fetch/injection/cleaning/point_detail', 'InjectionsController@fetchCleaningPointDetail');
Route::get('delete/injection/cleaning_timeline', 'InjectionsController@deleteCleaningTimeline');
Route::post('update/injection/cleaning_timeline', 'InjectionsController@updateCleaningTimeline');
Route::post('input/injection/cleaning', 'InjectionsController@inputCleaning');

Route::get('index/injection/cleaning/monitoring', 'InjectionsController@indexCleaningMonitoring');
Route::get('fetch/injection/cleaning/monitoring', 'InjectionsController@fetchCleaningMonitoring');
Route::get('fetch/injection/cleaning/monitoring/detail', 'InjectionsController@fetchCleaningMonitoringDetail');

Route::get('index/injection/report_cleaning', 'InjectionsController@indexReportCleaning');
Route::get('fetch/injection/report_cleaning', 'InjectionsController@fetchReportCleaning');

// end mesin injeksi

Route::group(['nav' => 'R5', 'middleware' => 'permission'], function () {
});

Route::group(['nav' => 'R6', 'middleware' => 'permission'], function () {
    Route::get('index/tr_completion', 'InventoryController@indexCompletion');
    Route::get('fetch/tr_completion', 'InventoryController@fetchCompletion');
    Route::get('download/tr_completion', 'InventoryController@downloadCompletion');

    Route::get('index/tr_transfer', 'InventoryController@indexTransfer');
    Route::get('fetch/tr_transfer', 'InventoryController@fetchTransfer');
    Route::get('download/tr_transfer', 'InventoryController@downloadTransfer');

    Route::get('index/ending_stock', 'InventoryController@indexEndingStock');
    Route::get('fetch/ending_stock', 'InventoryController@fetchEndingStock');
    Route::post('input/ending_stock', 'InventoryController@inputEndingStock');

    Route::get('index/back_order_sales', 'ShipmentController@indexBackOrder');
    Route::get('fetch/back_order_sales', 'ShipmentController@fetchBackOrder');
    Route::post('input/back_order_sales', 'ShipmentController@inputBackOrder');

});

Route::get('index/tr_history', 'InventoryController@indexHistory');
Route::get('fetch/tr_history', 'InventoryController@fetchHistory');

Route::group(['nav' => 'R7', 'middleware' => 'permission'], function () {
    Route::get('index/overtime_confirmation', 'OvertimeController@indexOvertimeConfirmation');
    Route::get('fetch/overtime_confirmation', 'OvertimeController@fetchOvertimeConfirmation');
    Route::post('confirm/overtime_confirmation', 'OvertimeController@confirmOvertimeConfirmation');
    Route::post('edit/overtime_confirmation', 'OvertimeController@editOvertimeConfirmation');
    Route::post('delete/overtime_confirmation', 'OvertimeController@deleteOvertimeConfirmation');
    // Route::get('index/ga_report/overtime', 'OvertimeController@create_overtime');

});

Route::get('export/overtime/list', 'OvertimeController@exportOvertimeAll');

// START OVERTIME MAKAN

Route::get('index/ga_report/order/makan', 'OvertimeController@create_overtime');
Route::post('create/order/puasa', 'OvertimeController@createOrderFood');
Route::get('fetch/report/overtime_food', 'OvertimeController@fetch_report_overtime');
Route::post('delete/overtime/request', 'OvertimeController@deleteOvertimeRequest');
Route::get('report/overtime/food', 'OvertimeController@fetchResumeFood');
Route::post('upload/overtime/eat', 'OvertimeController@uploadOvertimeEat');
Route::get('fetch/food/order_list', 'OvertimeController@fetchOrderFoodList');
Route::get('index/order/overtime/attendance', 'OvertimeController@indexOvertimeAttend');
Route::get('fetch/list/overtime/attendance', 'OvertimeController@fetchListOvertime');
Route::get('fetch/scan/overtime/attendance', 'OvertimeController@fetchOvertimeAttendance');
Route::get('fetch/scan/overtime/attendance2', 'OvertimeController@fetchOvertimeAttendance2');
Route::get('fetch/scan/overtime/attendance5', 'OvertimeController@fetchOvertimeAttendance5');
Route::post('create/extra/food', 'OvertimeController@createEmpExtraFood');
Route::get('fetch/list/extra/tambahan', 'OvertimeController@fetchListExtraTam');
Route::post('delete/extra/tam', 'OvertimeController@deleteDataExtraTam');
Route::get('index/overtime/stock', 'OvertimeController@indexOvertimeExtraStock');
Route::get('fetch/overtime/stock', 'OvertimeController@fetchStockOvLog');

//REPAIR FLUTE
Route::get('flute_repair', 'AdditionalController@indexFluteRepair');
Route::get('index/flute_repair/tarik', 'AdditionalController@indexTarik');
Route::get('fetch/flute_repair/tarik', 'AdditionalController@fetchTarik');
Route::post('scan/flute_repair/tarik', 'AdditionalController@scanTarik');
Route::get('index/flute_repair/selesai', 'AdditionalController@indexSelesai');
Route::get('fetch/flute_repair/selesai', 'AdditionalController@fetchSelesai');
Route::post('scan/flute_repair/selesai', 'AdditionalController@scanSelesai');
Route::get('index/flute_repair/kembali', 'AdditionalController@indexKembali');
Route::get('fetch/flute_repair/kembali', 'AdditionalController@fetchKembali');
Route::post('scan/flute_repair/kembali', 'AdditionalController@scanKembali');
Route::get('index/flute_repair/resume', 'AdditionalController@indexResume');
Route::get('fetch/flute_repair/by_status', 'AdditionalController@fetchByStatus');
Route::get('fetch/flute_repair/by_model', 'AdditionalController@fetchByModel');
Route::get('fetch/flute_repair/by_date', 'AdditionalController@fetchByDate');

//REPAIR RECORDER
Route::get('recorder_repair', 'AdditionalController@indexRecorderRepair');
Route::get('index/recorder_repair/tarik', 'AdditionalController@indexRecorderTarik');
Route::get('fetch/recorder_repair/tarik', 'AdditionalController@fetchRecorderTarik');
Route::post('scan/recorder_repair/tarik', 'AdditionalController@scanRecorderTarik');

Route::get('index/recorder_repair/selesai', 'AdditionalController@indexRecorderSelesai');
Route::get('fetch/recorder_repair/selesai', 'AdditionalController@fetchRecorderSelesai');
Route::post('scan/recorder_repair/selesai', 'AdditionalController@scanRecorderSelesai');

Route::get('index/recorder_repair/kembali', 'AdditionalController@indexRecorderKembali');
Route::get('fetch/recorder_repair/kembali', 'AdditionalController@fetchRecorderKembali');
Route::post('scan/recorder_repair/kembali', 'AdditionalController@scanRecorderKembali');

Route::get('index/recorder_repair/resume', 'AdditionalController@indexRecorderResume');
Route::get('fetch/recorder_repair/by_status', 'AdditionalController@fetchRecorderByStatus');
Route::get('fetch/recorder_repair/by_model', 'AdditionalController@fetchRecorderByModel');
Route::get('fetch/recorder_repair/by_date', 'AdditionalController@fetchRecorderByDate');

//EMPLOYEE
Route::group(['nav' => 'R10', 'middleware' => 'permission'], function () {
    Route::get('index/general/report_transportation', 'GeneralController@indexReportTransportation');
    Route::post('confirm/general/report_transportation/{id}', 'GeneralController@confirmReportTransportation');
    Route::get('fetch/general/online_transportation_report', 'GeneralController@fetchOnlineTransportationReport');
    Route::get('fetch/general/online_transportation_resume_report', 'GeneralController@fetchOnlineTransportationResumeReport');
    Route::post('confirm/general/online_transportation_report', 'GeneralController@confirmOnlineTransportationReport');
    Route::get('fetch/general/edit_online_transportation', 'GeneralController@editOnlineTransportation');
    Route::post('update/general/online_transportation', 'GeneralController@updateOnlineTransportation');
    Route::get('excel/general/online_transportation', 'GeneralController@excelOnlineTransportation');

    Route::get('index/general/report_surat_dokter', 'GeneralController@indexReportSuratDokter');
    Route::get('fetch/general/report_surat_dokter', 'GeneralController@fetchReportSuratDokter');
    Route::post('confirm/general/surat_dokter_report', 'GeneralController@confirmSuratDokterReport');
});

Route::get('index/update_emp_data/{employee_id}', 'EmployeeController@indexUpdateEmpData');
Route::get('fetch/fill_emp_data', 'EmployeeController@fetchFillEmpData');
Route::post('fetch/update_emp_data', 'EmployeeController@fetchUpdateEmpData');

Route::get('index/perpajakan/{employee_id}', 'EmployeeController@indexEmpDataPajak');
Route::get('fetch/fill_perpajakan_data', 'EmployeeController@fetchFillPerpajakanData');
Route::post('fetch/update_perpajakan_data', 'EmployeeController@fetchUpdatePerpajakanData');

Route::get('index/resume_pajak', 'EmployeeController@indexResumePajak');
Route::get('fetch/resume_pajak', 'EmployeeController@fetchResumePajak');
Route::get('fetch/resume_pajak/detail', 'EmployeeController@fetchResumePajakDetail');
Route::get('export/resume_pajak', 'EmployeeController@exportDataPajak');

Route::get('index/report/manpower', 'EmployeeController@indexReportManpower');
Route::get('fetch/report/manpower', 'EmployeeController@fetchReportManpower');
Route::get('fetch/report/manpower_detail', 'EmployeeController@fetchReportManpowerDetail');

Route::get('index/report/employee_resume', 'EmployeeController@indexEmployeeResume');
Route::get('fetch/report/employee_resume', 'EmployeeController@fetchEmployeeResume');

Route::get('index/report/gender', 'EmployeeController@indexReportGender');
Route::get('fetch/report/gender2', 'EmployeeController@fetchReportGender2');
Route::get('index/report/stat', 'EmployeeController@indexReportStatus');
Route::get('index/report/grade', 'EmployeeController@indexReportGrade');
Route::get('index/report/department', 'EmployeeController@indexReportDepartment');
Route::get('index/report/jabatan', 'EmployeeController@indexReportJabatan');
Route::get('fetch/report/stat', 'EmployeeController@fetchReport');
Route::get('fetch/report/detail_stat', 'EmployeeController@detailReport');
Route::get('index/report/leave_control', 'AbsenceController@indexReportLeaveControl');

//OVERTIME
Route::get('fetch/report/overtime_section', 'OvertimeController@fetchReportOvertimeSection');
Route::get('index/report/overtime_monthly_fq', 'OvertimeController@indexReportControlFq');
// Route::get('index/report/overtime_monthly', 'OvertimeController@indexReportControl');
Route::get('index/report/overtime_monthly_bdg', 'OvertimeController@indexReportControlBdg');
Route::get('index/report/overtime_section', 'OvertimeController@indexReportSection');
Route::get('fetch/report/overtime_report_section', 'OvertimeController@fetchReportSection');
Route::get('index/report/overtime_data', 'OvertimeController@indexOvertimeData');
Route::get('fetch/report/overtime_data', 'OvertimeController@fetchOvertimeData');
Route::get('index/report/overtime_outsource', 'OvertimeController@indexReportOutsouce');
Route::get('fetch/report/overtime_report_outsource', 'OvertimeController@fetchReportOutsource');
Route::get('fetch/report/overtime_detail_outsource', 'OvertimeController@fetchDetailOutsource');
Route::get('index/report/overtime_outsource_data', 'OvertimeController@indexOvertimeOutsource');
Route::get('fetch/report/overtime_data_outsource', 'OvertimeController@fetchOvertimeDataOutsource');
Route::get('index/report/overtime_by_employee', 'OvertimeController@indexOvertimeByEmployee');
Route::get('fetch/report/overtime_by_employee', 'OvertimeController@fetchOvertimeByEmployee');
Route::get('fetch/report/detail_ot_by_employee', 'OvertimeController@detailOvertimeByEmployee');
Route::get('index/report/overtime_resume', 'OvertimeController@indexMonthlyResume');
Route::get('fetch/report/overtime_resume', 'OvertimeController@fetchMonthlyResume');
Route::get('index/report/overtime_yearly', 'OvertimeController@indexYearlyResume');
Route::get('fetch/report/overtime_yearly', 'OvertimeController@fetchYearlyResume');

Route::get('index/report/ga_report', 'OvertimeController@indexGAReport');
Route::get('fetch/report/ga_report', 'OvertimeController@fetchGAReport');

Route::group(['nav' => 'R9', 'middleware' => 'permission'], function () {
    Route::get('index/report/overtime_check', 'OvertimeController@indexOvertimeCheck');
    Route::get('fetch/report/overtime_check', 'OvertimeController@fetchOvertimeCheck');
    Route::get('index/report/overtime_control', 'OvertimeController@indexOvertimeControl');
    Route::get('fetch/report/overtime_control', 'OvertimeController@fetchOvertimeControl');
});

Route::get('index/kaizen_teian', 'EmployeeController@indexEKaizen');

Route::get('fetch/overtime_report', 'OvertimeController@overtimeReport');
Route::get('fetch/overtime_report_detail', 'OvertimeController@overtimeReportDetail');
Route::get('fetch/report/attendance_rate/detail', 'EmployeeController@detailAttendanceRate');
Route::get('fetch/report/att_rate', 'EmployeeController@fetchAttendanceRate');
Route::get('fetch/report/gender', 'EmployeeController@fetchReportGender');
Route::get('fetch/report/status1', 'EmployeeController@fetchReportStatus');
Route::get('fetch/report/serikat', 'EmployeeController@reportSerikat');
Route::get('fetch/report/overtime_report_control', 'OvertimeController@overtimeControl');
Route::get('fetch/overtime_report_over', 'OvertimeController@overtimeOver');
Route::get('index/employee/service', 'EmployeeController@indexEmployeeService')->name('emp_service');
Route::get('fetch/report/kaizen', 'EmployeeController@fetchKaizen');
Route::get('fetch/sub_leader', 'EmployeeController@fetchSubLeader');
Route::get('create/ekaizen/{id}/{name}/{section}/{group}', 'EmployeeController@makeKaizen');
Route::get('create/ekaizen/{id}/{name}/{section}', 'EmployeeController@makeKaizen2');
Route::get('fetch/cost', 'EmployeeController@fetchCost');
Route::post('post/ekaizen', 'EmployeeController@postKaizen');
Route::post('update/ekaizen', 'EmployeeController@updateKaizen');
Route::get('get/ekaizen', 'EmployeeController@getKaizen');
Route::get('index/updateKaizen/{id}', 'EmployeeController@indexUpdateKaizenDetail');
Route::get('delete/kaizen', 'EmployeeController@deleteKaizen');
Route::get('fetch/chat/hrqa', 'EmployeeController@fetchChat');
Route::post('post/chat/comment', 'EmployeeController@postComment');
Route::post('post/hrqa', 'EmployeeController@postChat');
Route::get('index/employee_information', 'EmployeeController@indexEmployeeInformation2');
Route::get('fetch/cc/budget', 'OvertimeController@fetchCostCenterBudget');
Route::get('fetch/chart/control/detail', 'OvertimeController@overtimeDetail');
Route::get('update/employee/number', 'EmployeeController@editNumber');
// DailyAttendanceControl
Route::get('index/report/daily_attendance', 'EmployeeController@indexDailyAttendance');
Route::get('fetch/report/daily_attendance', 'EmployeeController@fetchDailyAttendance');
Route::get('fetch/report/detail_daily_attendance', 'EmployeeController@detailDailyAttendance');
Route::get('index/report/attendance_data', 'EmployeeController@attendanceData');
Route::get('fetch/report/attendance_data', 'EmployeeController@fetchAttendanceData');
// Presence
Route::get('index/report/presence', 'EmployeeController@indexPresence');
Route::get('fetch/report/presence', 'EmployeeController@fetchPresence');
Route::get('fetch/report/detail_presence', 'EmployeeController@detailPresence');
// Absence
Route::get('index/report/absence', 'EmployeeController@indexAbsence');
Route::get('fetch/report/absence', 'EmployeeController@fetchAbsence');
Route::get('fetch/report/detail_absence', 'EmployeeController@detailAbsence');
Route::get('fetch/absence/employee', 'EmployeeController@fetchAbsenceEmployee');

Route::get('index/report/absence_monitoring', 'EmployeeController@indexAbsenceMonitoring');
Route::get('fetch/report/absence_monitoring', 'EmployeeController@fetchAbsenceMonitoring');
Route::get('fetch/report/absence_monitoring_detail', 'EmployeeController@fetchAbsenceMonitoringDetail');

// Checklog
Route::get('index/report/checklog_data', 'EmployeeController@checklogData');
Route::get('fetch/report/checklog_data', 'EmployeeController@fetchChecklogData');

// Route::group(['nav' => 'R4', 'middleware' => 'permission'], function () {
// });

// Route::group(['nav' => 'R2', 'middleware' => 'permission'], function () {
Route::get('index/inventory', 'InventoryController@index');
Route::post('fetch/inventory', 'InventoryController@fetch');
// });

Route::group(['nav' => 'A8', 'middleware' => 'permission'], function () {
    Route::get('index/middle/barrel_adjustment', 'MiddleProcessController@indexBarrelAdjustment');
    Route::get('index/middle/wip_adjustment', 'MiddleProcessController@indexWIPAdjustment');
    Route::get('fetch/middle/barrel_adjustment', 'MiddleProcessController@fetchBarrelAdjustment');
    Route::get('fetch/middle/barrel_inactive/{id}', 'MiddleProcessController@fetchBarrelInactive');
    Route::get('fetch/middle/wip', 'MiddleProcessController@fetchWIP');
    Route::post('post/middle/barrel_inactive', 'MiddleProcessController@postInactive');
    Route::post('post/middle/barrel_inactive_wip', 'MiddleProcessController@postInactiveWIP');
    Route::post('post/middle/new/barrel_inactive', 'MiddleProcessController@CreateInactive');
    Route::post('import/barrel_inactive', 'MiddleProcessController@importInactive');

});

Route::group(['nav' => 'A9', 'middleware' => 'permission'], function () {
    Route::get('index/middle/buffing_adjustment', 'MiddleProcessController@indexBuffingAdjustment');
    Route::get('fetch/middle/buffing_adjustment', 'MiddleProcessController@fetchBuffingAdjustment');
    Route::post('post/middle/buffing_delete_queue', 'MiddleProcessController@deleteBuffingQueue');
    Route::post('post/middle/buffing_add_queue', 'MiddleProcessController@addBuffingQueue');

    Route::get('index/middle/buffing_canceled', 'MiddleProcessController@indexBuffingCanceled');
    Route::get('fetch/middle/buffing_canceled', 'MiddleProcessController@fetchBuffingCanceled');
    Route::post('delete/middle/buffing_canceled', 'MiddleProcessController@deleteBuffingCanceled');

    Route::get('index/middle/buffing_operator/{loc}', 'MiddleProcessController@indexBuffingOperator');
    Route::get('fetch/middle/buffing_operator/{loc}', 'MiddleProcessController@fetchBuffingOperator');
    Route::post('update/middle/buffing_operator', 'MiddleProcessController@updateBuffingOperator');
    Route::post('delete/middle/buffing_operator', 'MiddleProcessController@deleteBuffingOperator');
    Route::post('insert/middle/buffing_operator', 'MiddleProcessController@insertBuffingOperator');

    Route::get('index/middle/buffing_target/{loc}', 'MiddleProcessController@indexBuffingTarget');
    Route::get('fetch/middle/buffing_target/{loc}', 'MiddleProcessController@fetchBuffingTarget');
    Route::post('update/middle/buffing_target', 'MiddleProcessController@updateBuffingTarget');

    Route::post('update/middle/buffing_op_eff_check', 'MiddleProcessController@updateEffCheck');
    Route::post('update/middle/buffing_op_ng_check', 'MiddleProcessController@updateNgCheck');

    Route::get('index/middle/buffing_kanban/{sloc}', 'MiddleProcessController@indexBuffingKanban');
    Route::get('fetch/middle/buffing_kanban', 'MiddleProcessController@fetchBuffingKanban');
    Route::get('fetch/middle/buffing_check_kanban', 'MiddleProcessController@fetchCheckKanban');
    Route::post('update/middle/buffing_kanban', 'MiddleProcessController@updateKanban');
    Route::get('print/middle/buffing_kanban/{sloc}/{material_number}/{no_kanban}', 'MiddleProcessController@printKanban');

});

Route::get('index/middle/resume_kanban/{location}', 'MiddleProcessController@indexResumeKanban');
Route::get('fetch/middle/resume_kanban/{storage_location}', 'MiddleProcessController@fetchResumeKanban');

Route::get('index/buffing/resume_kanban/{location}', 'MiddleProcessController@indexResumeKanbanBuffing');
Route::get('fetch/buffing/resume_kanban/{storage_location}', 'MiddleProcessController@fetchResumeKanbanBuffing');

Route::get('setting/user', 'UserController@index_setting');
Route::post('setting/user', 'UserController@setting');
// Route::get('register', 'UserController@indexRegister');
// Route::post('register', 'UserController@register');
Route::post('register', 'RegisterController@register')->name('register');

Route::get('index/material/request', 'MaterialController@indexMaterialRequest');
Route::get('fetch/material/request_list', 'MaterialController@fetchMaterialRequestList');
Route::get('index/material/receive', 'MaterialController@indexMaterialReceive');
Route::get('index/material/data', 'MaterialController@indexMaterialData');

Route::group(['nav' => 'M2', 'middleware' => 'permission'], function () {
    Route::get('index/container_schedule', 'ContainerScheduleController@index');
    Route::get('create/container_schedule', 'ContainerScheduleController@create');
    Route::post('create/container_schedule', 'ContainerScheduleController@store');
    Route::get('destroy/container_schedule/{id}', 'ContainerScheduleController@destroy');
    Route::get('edit/container_schedule/{id}', 'ContainerScheduleController@edit');
    Route::post('edit/container_schedule/{id}', 'ContainerScheduleController@update');
    Route::get('show/container_schedule/{id}', 'ContainerScheduleController@show');
    Route::post('import/container_schedule', 'ContainerScheduleController@import');
});

Route::group(['nav' => 'M7', 'middleware' => 'permission'], function () {
    Route::get('index/production_schedule', 'ProductionScheduleController@index');
    Route::get('index/production_schedule_kd', 'ProductionScheduleController@indexKD');
    Route::get('fetch/production_schedule', 'ProductionScheduleController@fetchSchedule');
    Route::get('fetch/production_schedule_new', 'ProductionScheduleController@fetchScheduleNew');
    Route::get('fetch/production_schedule_kd', 'ProductionScheduleController@fetchScheduleKD');
    // Route::get('create/production_schedule', 'ProductionScheduleController@create');
    Route::post('create/production_schedule', 'ProductionScheduleController@store');
    Route::get('destroy/production_schedule', 'ProductionScheduleController@destroy');
    Route::post('delete/production_schedule', 'ProductionScheduleController@delete');
    Route::post('delete/production_schedule_kd', 'ProductionScheduleController@deleteKD');
    Route::get('edit/production_schedule', 'ProductionScheduleController@fetchEdit');
    Route::post('edit/production_schedule', 'ProductionScheduleController@edit');
    Route::post('edit/production_schedule_kd', 'ProductionScheduleController@editKD');
    Route::get('view/production_schedule', 'ProductionScheduleController@show');
    Route::post('import/production_schedule', 'ProductionScheduleController@import');

    //Trial FG
    Route::get('index/generate_production_schedule', 'ProductionScheduleController@indexGenerateSchedule');
    Route::get('fetch/generate_production_schedule', 'ProductionScheduleController@generateScheduleStepOne');

    //BI
    Route::get('fetch/view_production_schedule_bi', 'ProductionScheduleController@fetchViewScheduleBi');
    Route::post('generate/production_schedule_bi', 'ProductionScheduleController@generateScheduleBi');

    Route::get('fetch/view_production_schedule_ei', 'ProductionScheduleController@fetchViewScheduleEi');

    //KD
    Route::post('import/production_schedule_kd', 'ProductionScheduleController@importKd');
    Route::get('fetch/view_production_schedule_kd', 'ProductionScheduleController@fetchViewProductionScheduleKd');

    Route::get('fetch/view_generate_production_schedule_kd', 'ProductionScheduleController@fetchViewGenerateProductionScheduleKd');
    Route::post('fetch/generate_production_schedule_kd', 'ProductionScheduleController@fetchGenerateProductionScheduleKd');

    Route::get('fetch/view_generate_shipment_schedule_kd', 'ProductionScheduleController@fetchViewGenerateShipmentScheduleKd');
    Route::post('fetch/generate_shipment_schedule_kd', 'ProductionScheduleController@fetchGenerateShipmentScheduleKd');

    Route::get('fetch/adjusment_production_schedule_kd', 'ProductionScheduleController@updateAdjustmentScheduleNew');

    Route::get('fetch/achievement_schedule_kd', 'ProductionScheduleController@fetchAchievementScheduleKd');

    Route::get('index/generate_shipment_schedule/{category}', 'ShipmentController@indexShipmentSchedule');
    Route::get('fetch/generate_shipment_schedule', 'ShipmentController@fetchShipmentSchedule');
    Route::post('generate/shipment_schedule', 'ShipmentController@generateShipmentSchedule');

    Route::get('index/generate_shipment_cubication', 'ShipmentController@indexShipmentCubication');
    Route::get('fetch/generate_shipment_cubication', 'ShipmentController@fetchShipmentCubication');
    Route::post('generate/shipment_cubication', 'ShipmentController@generateShipmentCubication');
    Route::post('export/shipment_cubication', 'ShipmentController@exportShipmentCubication');

});

Route::group(['nav' => 'M7', 'middleware' => 'permission'], function () {

    Route::get('index/production_forecast', 'ForecastController@indexForecast');
    Route::post('upload/production_forecast', 'ForecastController@uploadForecast');
    Route::get('fetch/production_forecast', 'ForecastController@fetchForecast');

    Route::get('index/production_request', 'RequestController@indexRequest');
    Route::post('upload/production_request', 'RequestController@uploadRequest');
    Route::get('fetch/production_request', 'RequestController@fetchRequest');

});

Route::group(['nav' => 'M10', 'middleware' => 'permission'], function () {

    Route::get('index/psi_calendar', 'PsiController@indexPsiCalendar');
    Route::get('fetch/psi_calendar', 'PsiController@fetchPsiCalendar');

});

Route::get('index/psi', 'PsiController@indexPsi');
Route::get('psi', 'PsiController@generatePsi');

Route::group(['nav' => 'M9', 'middleware' => 'permission'], function () {
    Route::get('index/shipment_schedule', 'ShipmentScheduleController@index');
    Route::get('fetch/shipment_schedule', 'ShipmentScheduleController@fetchShipment');
    Route::post('input/shipment_schedule', 'ShipmentScheduleController@inputShipment');
    // Route::get('create/shipment_schedule', 'ShipmentScheduleController@create');
    Route::post('create/shipment_schedule', 'ShipmentScheduleController@create');
    Route::get('view/shipment_schedule', 'ShipmentScheduleController@view');
    Route::get('delete/shipment_schedule', 'ShipmentScheduleController@delete');
    Route::get('edit/shipment_schedule', 'ShipmentScheduleController@fetchEdit');
    Route::post('edit/shipment_schedule', 'ShipmentScheduleController@edit');
    // Route::get('show/shipment_schedule/{id}', 'ShipmentScheduleController@show');
    Route::post('import/shipment_schedule', 'ShipmentScheduleController@import');
});

Route::group(['nav' => 'M16', 'middleware' => 'permission'], function () {
    Route::get('index/MasterEmp', 'EmployeeController@index');
    Route::get('fetch/masteremp', 'EmployeeController@fetchMasterEmp');
    Route::get('fetch/masterempdetail', 'EmployeeController@fetchdetail');
    Route::post('create/empCreate', 'EmployeeController@empCreate');
    Route::post('update/empCreate', 'EmployeeController@updateEmpData');
    Route::get('index/MasterKaryawan', 'EmployeeController@index');
    Route::get('index/termination', 'EmployeeController@indexTermination');
    Route::get('index/bagian/export', 'EmployeeController@exportBagian');
    Route::get('fetch/cost_center', 'EmployeeController@getCostCenter');

    Route::get('index/emp_data', 'EmployeeController@indexEmpData');
    Route::get('fetch/emp_data', 'EmployeeController@fetchEmpData');
    Route::get('fetch/excel_emp_data', 'EmployeeController@fetchExcelEmpData');

    //insert
    Route::get('index/insertEmp', 'EmployeeController@insertEmp');
    Route::get('index/updateEmp/{nik}', 'EmployeeController@updateEmp');

    //import
    Route::post('import/importEmp', 'EmployeeController@importEmp');
    Route::post('import/bagian', 'EmployeeController@importBagian');
    Route::post('import/employee', 'EmployeeController@importKaryawan');
});

Route::group(['nav' => 'M17', 'middleware' => 'permission'], function () {
    Route::get('index/assy_schedule', 'AssyProcessController@indexSchedule');
    Route::get('fetch/assy_schedule', 'AssyProcessController@fetchSchedule');
    Route::post('create/assy_schedule', 'AssyProcessController@createSchedule');
    Route::post('delete/assy_schedule', 'AssyProcessController@delete');
    Route::post('edit/assy_schedule', 'AssyProcessController@edit');
    Route::get('edit/assy_schedule', 'AssyProcessController@fetchEdit');
    Route::get('destroy/assy_schedule', 'AssyProcessController@destroy');
    Route::get('view/assy_schedule', 'AssyProcessController@view');
    Route::post('import/assy_schedule', 'AssyProcessController@import');
});

Route::group(['nav' => 'M18', 'middleware' => 'permission'], function () {
    Route::get('index/safety_stock', 'InitialProcessController@indexStockMaster');
    Route::get('fetch/safety_stock', 'InitialProcessController@fetchStockMaster');
    Route::get('view/safety_stock', 'InitialProcessController@view');
    Route::post('edit/safety_stock', 'InitialProcessController@edit');
    Route::get('edit/safety_stock', 'InitialProcessController@fetchEdit');
    Route::post('import/safety_stock', 'InitialProcessController@import');
    Route::post('create/safety_stock', 'InitialProcessController@createInitial');
    Route::post('delete/safety_stock', 'InitialProcessController@delete');
    Route::get('destroy/safety_stock', 'InitialProcessController@destroy');
});

Route::group(['nav' => 'M29', 'middleware' => 'permission'], function () {

//DISPLAY RAW MATERIAL
});
Route::post('upload/material/material_monitoring', 'MaterialController@uploadMaterialMonitoring');
Route::post('delete/material/material_monitoring', 'MaterialController@deleteMaterialMonitoring');

Route::group(['nav' => 'M20', 'middleware' => 'permission'], function () {
    Route::get('index/user_document', 'UserDocumentController@index');
    Route::get('fetch/user_document', 'UserDocumentController@fetchUserDocument');
    Route::get('fetch/resume_user_document', 'UserDocumentController@fetchResumeUserDocument');
    Route::get('fetch/resume_user_document_detail', 'UserDocumentController@fetchResumeUserDocumentDetail');
    Route::get('fetch/user_document_detail', 'UserDocumentController@fetchUserDocumentDetail');
    Route::post('fetch/user_document_renew', 'UserDocumentController@fetchUserDocumentRenew');
    Route::post('fetch/user_document_update', 'UserDocumentController@fetchUserDocumentUpdate');
    Route::post('fetch/user_document_create', 'UserDocumentController@fetchUserDocumentCreate');
    Route::get('download/user_document', 'UserDocumentController@downloadUserDocument');
});

//RETURN MATERIAL
Route::get('index/return', 'TransactionController@indexReturn');
Route::get('index/return/data', 'TransactionController@indexReturnData');
Route::get('fetch/return/data', 'TransactionController@fetchReturnData');
Route::get('fetch/return/list', 'TransactionController@fetchReturnList');
Route::get('fetch/return', 'TransactionController@fetchReturn');
Route::get('fetch/return/resume', 'TransactionController@fetchReturnResume');
Route::get('index/return_logs', 'TransactionController@indexReturnLogs');
Route::get('fetch/return_logs', 'TransactionController@fetchReturnLogs');
Route::post('cancel/return', 'TransactionController@cancelReturn');

Route::get('fetch/return/ng_list', 'TransactionController@fetchNGList');
Route::get('index/return/monitoring', 'TransactionController@indexReturnMonitoring');

Route::get('fetch/return/logs', 'TransactionController@fetchMonitoringReturnLogs');

Route::group(['nav' => 'S37', 'middleware' => 'permission'], function () {
    Route::post('print/return', 'TransactionController@printReturn');
    Route::get('reprint/return', 'TransactionController@reprintReturn');
    Route::post('confirm/return', 'TransactionController@confirmReturn');
    Route::post('delete/return', 'TransactionController@deleteReturn');
});

//REPAIR MATERIAL
Route::get('index/repair', 'TransactionController@indexRepair');
Route::get('fetch/repair/resume', 'TransactionController@fetchRepairResume');
Route::get('fetch/repair/list', 'TransactionController@fetchRepairList');
Route::get('fetch/repair', 'TransactionController@fetchRepair');
Route::get('index/repair_logs', 'TransactionController@indexRepairLogs');
Route::get('fetch/repair_logs', 'TransactionController@fetchRepairLogs');
Route::post('cancel/repair', 'TransactionController@cancelRepair');

Route::group(['nav' => 'S37', 'middleware' => 'permission'], function () {
    Route::post('print/repair', 'TransactionController@printRepair');
    Route::get('reprint/repair', 'TransactionController@reprintRepair');
    Route::post('delete/repair', 'TransactionController@deleteRepair');
    Route::post('confirm/repair', 'TransactionController@confirmRepair');
});

//GA CONTROL
Route::group(['nav' => 'S39', 'middleware' => 'permission'], function () {
    Route::post('accept/ga_control/driver_request', 'GeneralAffairController@acceptDriverRequest');
    Route::post('edit/ga_control/driver_edit', 'GeneralAffairController@editDriverEdit');
    Route::post('import/ga_control/driver_duty', 'GeneralAffairController@importDriverDuty');
    Route::get('index/ga_control/driver_log', 'GeneralAffairController@indexDriverLog');
    Route::get('fetch/ga_control/driver_log', 'GeneralAffairController@fetchDriverLog');
    Route::post('create/ga_control/driver_duty', 'GeneralAffairController@createDriverDuty');
    // Route::get('approve/ga_control/bento/{id}', 'GeneralAffairController@approveBento');

    Route::post('approve/ga_control/bento', 'GeneralAffairController@approveBento');
    Route::post('approve/ga_control/bento_japanese', 'GeneralAffairController@approveBentoJapanese');
    Route::get('index/ga_control/bento_approve', 'GeneralAffairController@indexBentoApprove');
    Route::post('input/ga_control/bento_menu', 'GeneralAffairController@inputBentoMenu');

});

Route::get('approve/ga_control/driver/{id}', 'GeneralAffairController@approveRequest');
Route::get('reject/ga_control/driver/{id}', 'GeneralAffairController@rejectRequest');
Route::get('index/ga_control/driver', 'GeneralAffairController@indexDriver');
Route::get('fetch/ga_control/driver', 'GeneralAffairController@fetchDriver');
Route::get('fetch/ga_control/driver_duty', 'GeneralAffairController@fetchDriverDuty');
Route::get('fetch/ga_control/driver_edit', 'GeneralAffairController@fetchDriverEdit');
Route::get('fetch/ga_control/driver_request', 'GeneralAffairController@fetchDriverRequest');
Route::post('create/ga_control/driver_request', 'GeneralAffairController@createDriverRequest');
Route::get('fetch/ga_control/driver_detail', 'GeneralAffairController@fetchDriverDetail');

Route::get('index/ga_control/driver_monitoring', 'GeneralAffairController@indexDriverMonitoring');
Route::get('fetch/ga_control/driver_monitoring', 'GeneralAffairController@fetchDriverMonitoring');
Route::post('scan/ga_control/driver_monitoring', 'GeneralAffairController@scanDriverMonitoring');

//MCU
Route::group(['nav' => 'S76', 'middleware' => 'permission'], function () {
    Route::get('index/ga_control/mcu/physical/{inspector}', 'GeneralAffairController@indexPhysicalCheck');
    Route::get('scan/ga_control/mcu/physical', 'GeneralAffairController@scanPhysicalCheck');
    Route::post('input/ga_control/mcu/physical', 'GeneralAffairController@inputPhysicalCheck');
    Route::get('index/ga_control/mcu/report/physical', 'GeneralAffairController@indexReportPhysicalCheck');
    Route::get('fetch/ga_control/mcu/report/physical', 'GeneralAffairController@fetchReportPhysicalCheck');
    Route::get('download/ga_control/mcu/physical/schedule', 'GeneralAffairController@downloadSchedulePhysical');
    Route::get('upload/ga_control/mcu/physical/schedule', 'GeneralAffairController@uploadSchedulePhysical');
    Route::get('pdf/ga_control/mcu/report/physical/{id}', 'GeneralAffairController@pdfReportPhysicalCheck');

    Route::get('index/ga_control/mcu/report/physical/format', 'GeneralAffairController@indexReportPhysicalCheckFormat');
    Route::get('fetch/ga_control/mcu/report/physical/format', 'GeneralAffairController@fetchReportPhysicalCheckFormat');

    Route::get('index/ga_control/mcu/report/attendance', 'GeneralAffairController@indexReportMcuAttendance');
    Route::get('fetch/ga_control/mcu/report/attendance', 'GeneralAffairController@fetchReportMcuAttendance');
});

Route::get('index/ga_control/mcu/monitoring/physical', 'GeneralAffairController@indexPhysicalMonitoring');
Route::get('fetch/ga_control/mcu/monitoring/physical', 'GeneralAffairController@fetchPhysicalMonitoring');

Route::get('index/ga_control/mcu', 'GeneralAffairController@indexMcu');

Route::get('index/general/queue/{remark}', 'GeneralController@indexQueue');
Route::get('fetch/general/queue/{remark}', 'GeneralController@fetchQueue');

Route::get('index/ga_control/mcu/attendance/{periode}/{location}', 'GeneralAffairController@indexMcuAttendance');
Route::get('fetch/ga_control/mcu/attendance/{periode}/{location}', 'GeneralAffairController@fetchMcuAttendance');
Route::post('scan/ga_control/mcu/attendance', 'GeneralAffairController@scanMcuAttendance');

Route::get('index/ga_control/mcu/queue', 'GeneralAffairController@indexMcuQueue');
Route::get('fetch/ga_control/mcu/queue', 'GeneralAffairController@fetchMcuQueue');

Route::get('index/general/attendance_check', 'GeneralAttendanceController@indexGeneralAttendanceCheck');
Route::get('fetch/general/attendance_check', 'GeneralAttendanceController@fetchGeneralAttendanceCheck');
Route::post('scan/general/attendance_check', 'GeneralAttendanceController@scanGeneralAttendanceCheck');

//UNIFORM STOCK
Route::get('index/ga_control/uniform/stock', 'GeneralAffairController@indexUniformStock');
Route::get('fetch/ga_control/uniform/stock', 'GeneralAffairController@fetchUniformStock');
Route::get('fetch/ga_control/uniform/log', 'GeneralAffairController@fetchUniformLog');
Route::get('edit/ga_control/uniform/stock', 'GeneralAffairController@editUniformStock');
Route::post('update/ga_control/uniform/stock', 'GeneralAffairController@updateUniformStock');

Route::get('index/ga_control/uniform/attendance/old', 'GeneralAffairController@indexUniformAttendance');
Route::get('fetch/ga_control/uniform/attendance/old', 'GeneralAffairController@fetchUniformAttendance');
Route::get('fetch/ga_control/uniform/queue/old', 'GeneralAffairController@fetchUniformAttendanceQueue');

Route::post('input/ga_control/uniform/attendance/old', 'GeneralAffairController@inputUniformAttendance');

Route::get('scan/ga_control/uniform/operator', 'GeneralAffairController@scanUniformOperator');
Route::get('scan/ga_control/uniform/fix', 'GeneralAffairController@scanUniformOperatorFix');
Route::get('index/ga_control/uniform/attendance', 'GeneralAffairController@indexUniformAttendanceNew');
Route::get('fetch/ga_control/uniform/attendance', 'GeneralAffairController@fetchUniformAttendanceNew');
Route::get('fetch/ga_control/uniform/queue', 'GeneralAffairController@fetchUniformAttendanceQueueNew');
Route::post('input/ga_control/uniform/attendance', 'GeneralAffairController@inputUniformAttendanceNew');

// GA Secretary Admin Section
Route::get('index/ga_secretary/president_director/approval', 'GeneralAffairController@indexApprovalPresidentDirector')->name('indexApprovalPresidentDirector');
Route::post('fetch/ga_secretary/president_director/approval', 'GeneralAffairController@fetchApprovalPresidentDirector');

// Log Report Approval GA
Route::get('index/ga_secretary/president_director/approval/report', 'GeneralAffairController@indexApprovalPresidentDirectorReport')->name('indexApprovalPresidentDirectorReport');
Route::get('fetch/ga_secretary/president_director/approval/report', 'GeneralAffairController@fetchApprovalPresidentDirectorReport');

// Approval
// Route::get('index/ga_secretary/president_director/approval/{request_id}', 'GeneralAffairController@indexApprovalPresidentDirectorStatus')->name('indexApprovalPresidentDirectorStatus');
Route::post('input/ga_secretary/president_director/applicant', 'GeneralAffairController@inputApprovalPresidentDirector');
Route::get('approval/ga_secretary/president_director/{request_id}/{remark}/{status}', 'GeneralAffairController@approvalPresdir');
Route::post('input/ga_secretary/president_director/complete', 'GeneralAffairController@completePresdir');
Route::post('input/ga_secretary/president_director/edit', 'GeneralAffairController@editApprovalPresidentDirector');
Route::post('input/ga_secretary/president_director/cancel', 'GeneralAffairController@cancelRequest');

//STD CONTROL
Route::get('index/std_control/safety_shoes', 'GeneralController@indexSafetyShoes');
Route::get('fetch/std_control/safety_shoes', 'GeneralController@fetchSafetyShoes');
Route::get('fetch/std_control/safety_shoes_detail', 'GeneralController@fetchSafetyShoesDetail');
Route::get('fetch/std_control/request_safety_shoes', 'GeneralController@fetchRequestSafetyShoes');
Route::get('fetch/std_control/detail_safety_shoes', 'GeneralController@fetchDetailSafetyShoes');

// STD
Route::post('input/std_control/safety_shoes', 'GeneralController@inputSafetyShoes');
Route::post('input/std_control/safety_shoes_new', 'GeneralController@inputSafetyShoesNew');
Route::get('reprint/std_control/safety_shoes', 'GeneralController@reprintReqSafetyShoes');

//PRD
Route::post('input/std_control/req_safety_shoes', 'GeneralController@inputReqSafetyShoes');

//WH
Route::get('scan/std_control/safety_shoes', 'GeneralController@scanSafetyShoes');
Route::post('input/std_control/receive_safety_shoes', 'GeneralController@inputReceiveSafetyShoes');

//Log
Route::get('index/std_control/safety_shoes_log', 'GeneralController@indexSafetyShoesLog');
Route::get('fetch/std_control/safety_shoes_log', 'GeneralController@fetchSafetyShoesLog');

Route::get('index/flo_open', 'FloController@index_flo_open');
Route::get('fetch/flo_open', 'FloController@fetch_flo_open');

Route::group(['nav' => 'S1', 'middleware' => 'permission'], function () {
    Route::get('index/flo_view/bi', 'FloController@index_bi');
});

Route::group(['nav' => 'S2', 'middleware' => 'permission'], function () {
    Route::get('index/flo_view/ei', 'FloController@index_ei');
});

Route::group(['nav' => 'S3', 'middleware' => 'permission'], function () {
    Route::get('index/flo_view/delivery', 'FloController@index_delivery');
});

Route::group(['nav' => 'S4', 'middleware' => 'permission'], function () {
    Route::get('index/flo_view/stuffing', 'FloController@index_stuffing');
});

Route::group(['nav' => 'S5', 'middleware' => 'permission'], function () {
    Route::get('index/flo_view/shipment', 'FloController@index_shipment');
});

Route::group(['nav' => 'S6', 'middleware' => 'permission'], function () {
    Route::get('index/flo_view/lading', 'FloController@index_lading');
});

Route::group(['nav' => 'S7', 'middleware' => 'permission'], function () {
    Route::get('index/maedaoshi_bi', 'MaedaoshiController@index_bi');
    Route::get('index/after_maedaoshi_bi', 'MaedaoshiController@index_after_bi');
});

Route::group(['nav' => 'S8', 'middleware' => 'permission'], function () {
    Route::get('index/maedaoshi_ei', 'MaedaoshiController@index_ei');
    Route::get('index/after_maedaoshi_ei', 'MaedaoshiController@index_after_ei');
});

Route::group(['nav' => 'S9', 'middleware' => 'permission'], function () {
    Route::get('index/flo_view/deletion', 'FloController@index_deletion');
    Route::get('fetch/flo_deletion', 'FloController@fetch_flo_deletion');
    Route::post('destroy/flo_deletion', 'FloController@destroy_flo_deletion');
});

Route::group(['nav' => 'S10', 'middleware' => 'permission'], function () {
    Route::post('input/process_assy_fl', 'ProcessController@inputProcessAssyFL');
    Route::post('scan/serial_number_return_fl', 'ProcessController@scanSerialNumberReturnFl');

    Route::post('stamp/adjustSerial', 'ProcessController@adjustSerial');
    Route::get('stamp/adjust', 'ProcessController@adjust');
    Route::post('stamp/adjust', 'ProcessController@adjustUpdate');
    Route::get('edit/stamp', 'ProcessController@editStamp');
    Route::post('edit/stamp', 'ProcessController@updateStamp');
    Route::post('destroy/stamp', 'ProcessController@destroyStamp');
    // return sax
    Route::post('returnfg/stamp', 'ProcessController@returnfgStamp');
    Route::post('scan/serial_number_return_Sx', 'ProcessController@scanSerialNumberReturnSx');
    // end return sax

    // return cl
    Route::post('returncl/stamp', 'ProcessController@returnclStamp');
    Route::post('scan/serial_number_return_Cl', 'ProcessController@scanSerialNumberReturnCl');
    // end return cl

    // ng sax
    Route::post('ngfg/stamp', 'ProcessController@ngsxStamp');
    Route::post('scan/serial_number_ng_Sx', 'ProcessController@scanSerialNumberngSx');
    // end ng sax

    // ng FL
    Route::post('ngfgFL/stamp', 'ProcessController@ngFLStamp');
    Route::post('scan/serial_number_ng_FL', 'ProcessController@scanSerialNumberngFL');
    // end ng FL

    // NEW STAMP RFID
    Route::get('scan/assembly/tag_stamp', 'AssemblyProcessController@scanTagStamp');
    Route::post('stamp/assembly/flute', 'AssemblyProcessController@stampFlute');
    Route::post('stamp/assembly/adjust_serial', 'AssemblyProcessController@stampFluteAdjustSerial');

    Route::post('stamp/assembly/clarinet', 'AssemblyProcessController@stampClarinet');
    Route::post('fetch/assembly/clarinet/adjust', 'AssemblyProcessController@fetchStampClarinet');
    Route::post('stamp/assembly/clarinet/adjust', 'AssemblyProcessController@adjustStampClarinet');
    Route::get('edit/assembly/clarinet', 'AssemblyProcessController@editStampClarinet');
});
Route::get('fetch/assembly/stamp_result', 'AssemblyProcessController@fetchStampResult');
Route::get('fetch/assembly/serial', 'AssemblyProcessController@fetchSerialNumber');
Route::get('index/assembly/flute_stamp', 'AssemblyProcessController@indexFluteStamp');
Route::get('fetch/assembly/sn_ready', 'AssemblyProcessController@fetchSNReady');

Route::get('index/assembly/clarinet_stamp', 'AssemblyProcessController@indexClarinetStamp');

//sax new
Route::get('index/assembly/saxophone_registration', 'AssemblyProcessController@indexSaxophoneRegistration');
Route::get('fetch/assembly/model', 'AssemblyProcessController@fetchModel');
Route::post('edit/assembly/model', 'AssemblyProcessController@editModel');
Route::post('input/assembly/registration_process', 'AssemblyProcessController@inputRegistrationProcess');

Route::get('index/assembly/saxophone_print_label', 'AssemblyProcessController@indexSaxophonePrintLabel');
Route::get('index/assembly/saxophone/label_besar/{id}/{gmc}/{remark}/{operator_id}', 'AssemblyProcessController@labelBesarSx');
Route::get('index/assembly/saxophone/label_kecil/{id}/{remark}', 'AssemblyProcessController@labelKecilSx');
Route::get('index/assembly/saxophone/label_des/{id}', 'AssemblyProcessController@labelDesSx');
Route::get('index/assembly/saxophone/label_reprint/{id}/{gmc}/{remark}', 'AssemblyProcessController@labelReprintSx');

Route::get('index/assembly/eff/{product}', 'AssemblyProcessController@indexAssyEfficiency');
Route::get('fetch/assembly/eff', 'AssemblyProcessController@fetchAssyEfficiency');
Route::get('fetch/assembly/eff/detail', 'AssemblyProcessController@fetchAssyEfficiencyDetail');
Route::post('post/assembly/eff/training', 'AssemblyProcessController@postTrainingEfficiency');

Route::get('index/assembly/overall_eff/{product}', 'AssemblyProcessController@indexAssyAllEfficiency');
Route::get('fetch/assembly/overall_eff', 'AssemblyProcessController@fetchAssyAllEfficiency');

Route::get('index/assembly/group_balance/{origin_group}', 'AssemblyProcessController@indexAssemblyGroupBalance');
Route::get('fetch/assembly/group_balance', 'AssemblyProcessController@fetchAssemblyGroupBalance');

Route::get('index/assembly/line_balance/{origin_group}', 'AssemblyProcessController@indexAssemblyLineBalance');
Route::get('index/assembly/line_balance2/{origin_group}', 'AssemblyProcessController@indexAssemblyLineBalance2');
Route::get('fetch/assembly/line_balance', 'AssemblyProcessController@fetchAssemblyLineBalance');

//cl new
Route::get('index/assembly/clarinet_registration', 'AssemblyProcessController@indexClarinetRegistration');
Route::get('index/assembly/clarinet_print_label', 'AssemblyProcessController@indexClarinetPrintLabel');

Route::get('index/assembly/clarinet_print_label_outer', 'AssemblyProcessController@indexClarinetPrintLabelOuter');

Route::get('index/assembly/clarinet/label_outer/{id}/{gmc}/{codej}/{remark}', 'AssemblyProcessController@labelBesarOuterCl');
Route::get('index/assembly/clarinet/label_besar/{id}/{gmc}/{remark}/{employee_id}', 'AssemblyProcessController@labelBesarCl');
Route::get('index/assembly/clarinet/label_kecil/{id}/{remark}', 'AssemblyProcessController@labelKecilCl');
Route::get('index/assembly/clarinet/label_kecil2/{id}/{remark}', 'AssemblyProcessController@labelKecil2Cl');
Route::get('index/assembly/clarinet/label_deskripsi/{id}/{remark}', 'AssemblyProcessController@labelDeskripsiCl');
Route::get('index/assembly/clarinet/label_carb/{id}', 'AssemblyProcessController@labelCarbCl');
Route::get('index/assembly/clarinet/label_outer_alone/{date}/{model}/{japan}', 'AssemblyProcessController@labelBesarOuterClAlone');

Route::get('index/assembly/clear_card', 'AssemblyProcessController@clearKartu');

Route::get('index/assembly/operator/{origin_group}', 'AssemblyProcessController@indexAssemblyOperator');
Route::get('fetch/assembly/operator', 'AssemblyProcessController@fetchAssemblyOperator');
Route::get('input/assembly/operator', 'AssemblyProcessController@inputAssemblyOperator');
Route::get('update/assembly/operator', 'AssemblyProcessController@updateAssemblyOperator');

//welding process
Route::group(['nav' => 'S32', 'middleware' => 'permission'], function () {
    Route::post('input/welding/rework', 'WeldingProcessController@inputWeldingRework');
    Route::post('input/welding/kensa', 'WeldingProcessController@inputWeldingKensa');
});

Route::get('index/welding/operator/{loc}', 'WeldingProcessController@indexMasterOperator');
Route::get('fetch/welding/operator', 'WeldingProcessController@fetchMasterOperator');
Route::post('input/welding/operator', 'WeldingProcessController@inputOperator');
Route::get('delete/welding/operator', 'WeldingProcessController@deleteOperator');
Route::post('update/welding/operator', 'WeldingProcessController@updateOperator');

Route::get('index/welding/display_production_result/{loc}', 'WeldingProcessController@indexDisplayProductionResult');
// Route::get('index/welding/display_production_result', 'WeldingProcessController@indexDisplayProductionResult');

Route::get('fetch/welding/display_production_result', 'WeldingProcessController@fetchDisplayProductionResult');
Route::get('fetch/welding/display_production_result2', 'WeldingProcessController@fetchDisplayProductionResult2');
Route::get('index/welding/report_ng/{loc}', 'WeldingProcessController@indexReportNG');
Route::get('fetch/welding/report_ng', 'WeldingProcessController@fetchReportNG');
Route::get('index/welding/report_hourly', 'WeldingProcessController@indexReportHourly');
Route::get('fetch/welding/report_hourly', 'WeldingProcessController@fetchReportHourly');
Route::get('index/welding/ng_rate/{loc}', 'WeldingProcessController@indexNgRate');
Route::get('fetch/welding/ng_rate', 'WeldingProcessController@fetchNgRate');
Route::get('fetch/welding/ng_rate/detail', 'WeldingProcessController@fetchNgRateDetail');
Route::get('index/welding/op_ng/{loc}', 'WeldingProcessController@indexOpRate');
Route::get('fetch/welding/op_ng', 'WeldingProcessController@fetchOpRate');
Route::get('fetch/welding/op_ng_detail', 'WeldingProcessController@fetchOpRateDetail');
Route::get('index/welding/op_analysis', 'WeldingProcessController@indexOpAnalysis');
Route::get('fetch/welding/op_analysis', 'WeldingProcessController@fetchOpAnalysis');
Route::get('index/welding/welding_op_eff', 'WeldingProcessController@indexWeldingOpEff');
Route::get('fetch/welding/welding_op_eff', 'WeldingProcessController@fetchWeldingOpEff');
Route::get('index/welding/welding_eff', 'WeldingProcessController@indexWeldingEff');
Route::get('fetch/welding/welding_op_eff_ongoing', 'WeldingProcessController@fetchWeldingEffOngoing');
Route::get('fetch/welding/welding_op_eff_target', 'WeldingProcessController@fetchWeldingOpEffTarget');
Route::post('update/welding/welding_op_eff_check', 'WeldingProcessController@updateEffCheck');
Route::get('scan/welding/operator/rfid', 'WeldingProcessController@scanWeldingOperatorToHex');
Route::get('index/welding/production_result', 'WeldingProcessController@indexProductionResult');
Route::get('fetch/welding/production_result', 'WeldingProcessController@fetchProductionResult');
Route::get('index/welding/kensa/{id}', 'WeldingProcessController@indexWeldingKensa');
Route::get('scan/welding/operator', 'WeldingProcessController@scanWeldingOperator');
Route::get('scan/welding/kensa', 'WeldingProcessController@scanWeldingKensa');
Route::get('fetch/welding/kensa_result', 'WeldingProcessController@fetchKensaResult');
Route::get('index/welding/resume/{id}', 'WeldingProcessController@indexWeldingResume');
Route::get('fetch/welding/resume', 'WeldingProcessController@fetchWeldingResume');
Route::get('fetch/welding/key_resume', 'WeldingProcessController@fetchWeldingKeyResume');
Route::get('fetch/welding/ng_resume', 'WeldingProcessController@fetchWeldingKeyResume');
Route::get('index/welding/group_achievement', 'WeldingProcessController@indexWeldingAchievement');
Route::get('fetch/welding/group_achievement', 'WeldingProcessController@fetchGroupAchievement');
Route::get('fetch/welding/group_achievement_detail', 'WeldingProcessController@fetchGroupAchievementDetail');
Route::get('fetch/welding/accumulated_achievement', 'WeldingProcessController@fetchAccumulatedAchievement');
Route::get('index/welding/eff_handling', 'WeldingProcessController@indexEffHandling');
Route::get('fetch/welding/eff_handling', 'WeldingProcessController@fetchEffHandling');

Route::get('index/welding/ng_rate', 'WeldingProcessController@indexNgRate');
Route::get('index/welding/op_ng', 'WeldingProcessController@indexOpRate');

Route::get('index/welding/welding_adjustment', 'WeldingProcessController@indexWeldingAdjustment');
Route::get('fetch/welding/welding_queue', 'WeldingProcessController@fetchWeldingQueue');
Route::get('fetch/welding/welding_stock', 'WeldingProcessController@fetchWeldingStock');
Route::post('post/welding/welding_add_queue', 'WeldingProcessController@inputWeldingQueue');
Route::post('post/welding/welding_delete_queue', 'WeldingProcessController@deleteWeldingQueue');

Route::get('index/welding/welding_board/{loc}', 'WeldingProcessController@indexWeldingBoard');
Route::get('fetch/welding/welding_board', 'WeldingProcessController@fetchWeldingBoard');
Route::get('fetch/welding/welding_board_new', 'WeldingProcessController@fetchWeldingBoardNew');
Route::get('fetch/welding/fetch_detail', 'WeldingProcessController@fetchDetailWeldingBoard');
Route::get('index/welding/master_kanban/{loc}', 'WeldingProcessController@indexMasterKanban');
Route::get('fetch/welding/kanban', 'WeldingProcessController@fetchMasterKanban');
Route::post('update/welding/kanban', 'WeldingProcessController@updateKanban');
Route::post('input/welding/kanban', 'WeldingProcessController@inputKanban');
Route::get('delete/welding/kanban', 'WeldingProcessController@deleteKanban');

Route::get('index/welding/washing_waiting_time', 'WeldingProcessController@indexWashingWaitingTime');
Route::get('fetch/welding/washing_waiting_time', 'WeldingProcessController@fetchWashingWaitingTime');
Route::get('fetch/welding/kanban_history', 'WeldingProcessController@fetchKanbanHistory');

Route::get('index/welding/productivity/{location}', 'WeldingProcessController@indexWeldingProductivity');
Route::get('fetch/welding/productivity', 'WeldingProcessController@fetchWeldingProductivity');

Route::group(['nav' => 'A10', 'middleware' => 'permission'], function () {
    // Route::post('update/welding/op_ng_check', 'WeldingProcessController@updateNgCheck');
});
Route::post('update/welding/op_ng_check', 'WeldingProcessController@updateNgCheck');

Route::get('index/welding/current_welding', 'WeldingProcessController@indexCurrentWelding');
Route::get('fetch/welding/current_welding', 'WeldingProcessController@fetchCurrentWelding');
Route::get('index/welding/op_trend', 'WeldingProcessController@indexWeldingTrend');
Route::get('fetch/welding/op_trend', 'WeldingProcessController@fetchWeldingTrend');

Route::get('index/welding/resume_kanban', 'WeldingProcessController@indexResumeKanban');
Route::get('fetch/welding/resume_kanban', 'WeldingProcessController@fetchResumeKanban');

//JIG KENSA
Route::group(['nav' => 'M27', 'middleware' => 'permission'], function () {
    Route::get('index/welding/jig_bom', 'WeldingProcessController@indexWeldingJigBom');
    Route::get('fetch/welding/jig_bom', 'WeldingProcessController@fetchWeldingJigBom');
    Route::post('input/welding/jig_bom', 'WeldingProcessController@inputWeldingJigBom');
    Route::get('edit/welding/jig_bom', 'WeldingProcessController@editWeldingJigBom');
    Route::post('update/welding/jig_bom', 'WeldingProcessController@updateWeldingJigBom');
    Route::get('delete/welding/jig_bom/{id}', 'WeldingProcessController@deleteWeldingJigBom');

    Route::post('input/welding/jig_data', 'WeldingProcessController@inputWeldingJigData');
    Route::get('edit/welding/jig_data', 'WeldingProcessController@editWeldingJigData');
    Route::post('update/welding/jig_data', 'WeldingProcessController@updateWeldingJigData');
    Route::get('delete/welding/jig_data/{id}/{jig_id}/{jig_parent}', 'WeldingProcessController@deleteWeldingJigData');

    Route::get('edit/welding/jig_schedule', 'WeldingProcessController@editWeldingJigSchedule');
    Route::post('update/welding/jig_schedule', 'WeldingProcessController@updateWeldingJigSchedule');
    Route::get('delete/welding/jig_schedule', 'WeldingProcessController@deleteWeldingJigSchedule');

    Route::get('index/welding/kensa_point', 'WeldingProcessController@indexWeldingKensaPoint');
    Route::get('fetch/welding/kensa_point', 'WeldingProcessController@fetchWeldingKensaPoint');
    Route::post('input/welding/kensa_point', 'WeldingProcessController@inputWeldingKensaPoint');
    Route::get('edit/welding/kensa_point', 'WeldingProcessController@editWeldingKensaPoint');
    Route::post('update/welding/kensa_point', 'WeldingProcessController@updateWeldingKensaPoint');
    Route::get('delete/welding/kensa_point/{id}', 'WeldingProcessController@deleteWeldingKensaPoint');

    Route::post('input/welding/jig_part', 'WeldingProcessController@inputWeldingJigPart');
    Route::get('edit/welding/jig_part', 'WeldingProcessController@editWeldingJigPart');
    Route::post('update/welding/jig_part', 'WeldingProcessController@updateWeldingJigPart');
    Route::get('delete/welding/jig_part/{id}', 'WeldingProcessController@deleteWeldingJigPart');

    //JIG PROSES

    Route::get('index/welding/jig_bom_proses', 'WeldingProcessController@indexWeldingJigBomProcess');
    Route::get('fetch/welding/jig_bom_proses', 'WeldingProcessController@fetchWeldingJigBomProcess');
    Route::post('input/welding/jig_bom_proses', 'WeldingProcessController@inputWeldingJigBomProcess');
    Route::get('edit/welding/jig_bom_proses', 'WeldingProcessController@editWeldingJigBomProcess');
    Route::post('update/welding/jig_bom_proses', 'WeldingProcessController@updateWeldingJigBomProcess');
    Route::get('delete/welding/jig_bom_proses/{id}', 'WeldingProcessController@deleteWeldingJigBomProcess');

    Route::post('input/welding/jig_data_proses', 'WeldingProcessController@inputWeldingJigDataProcess');
    Route::get('edit/welding/jig_data_proses', 'WeldingProcessController@editWeldingJigDataProcess');
    Route::post('update/welding/jig_data_proses', 'WeldingProcessController@updateWeldingJigDataProcess');
    Route::get('delete/welding/jig_data_proses/{id}/{jig_id}/{jig_parent}', 'WeldingProcessController@deleteWeldingJigDataProcess');

    Route::get('edit/welding/jig_schedule_proses', 'WeldingProcessController@editWeldingJigScheduleProcess');
    Route::post('update/welding/jig_schedule_proses', 'WeldingProcessController@updateWeldingJigScheduleProcess');

    Route::post('input/welding/jig_part_proses', 'WeldingProcessController@inputWeldingJigPartProcess');
    Route::get('edit/welding/jig_part_proses', 'WeldingProcessController@editWeldingJigPartProcess');
    Route::post('update/welding/jig_part_proses', 'WeldingProcessController@updateWeldingJigPartProcess');
    Route::get('delete/welding/jig_part_proses/{id}', 'WeldingProcessController@deleteWeldingJigPartProcess');

    Route::get('index/welding/kensa_point_proses', 'WeldingProcessController@indexWeldingKensaPointProcess');
    Route::get('fetch/welding/kensa_point_proses', 'WeldingProcessController@fetchWeldingKensaPointProcess');
    Route::post('input/welding/kensa_point_proses', 'WeldingProcessController@inputWeldingKensaPointProcess');
    Route::get('edit/welding/kensa_point_proses', 'WeldingProcessController@editWeldingKensaPointProcess');
    Route::post('update/welding/kensa_point_proses', 'WeldingProcessController@updateWeldingKensaPointProcess');
    Route::get('delete/welding/kensa_point_proses/{id}', 'WeldingProcessController@deleteWeldingKensaPointProcess');
});
Route::get('index/welding_jig', 'WeldingProcessController@indexWeldingJig');

Route::get('index/welding/jig_data', 'WeldingProcessController@indexWeldingJigData');
Route::get('fetch/welding/jig_data', 'WeldingProcessController@fetchWeldingJigData');

Route::get('index/welding/jig_schedule', 'WeldingProcessController@indexWeldingJigSchedule');
Route::get('fetch/welding/jig_schedule', 'WeldingProcessController@fetchWeldingJigSchedule');

Route::get('index/welding/jig_part', 'WeldingProcessController@indexWeldingJigPart');
Route::get('fetch/welding/jig_part', 'WeldingProcessController@fetchWeldingJigPart');

Route::get('index/welding/kensa_jig', 'WeldingProcessController@indexWeldingKensaJig');
Route::get('scan/welding/jig', 'WeldingProcessController@scanWeldingJig');
Route::get('fetch/welding/jig_check', 'WeldingProcessController@fetchJigCheck');
Route::get('fetch/welding/drawing_list', 'WeldingProcessController@fetchDrawingList');
Route::post('input/welding/kensa_jig', 'WeldingProcessController@inputKensaJig');

Route::get('index/welding/repair_jig', 'WeldingProcessController@indexWeldingRepairJig');
Route::post('input/welding/repair_jig', 'WeldingProcessController@inputRepairJig');

Route::get('index/welding/monitoring_jig', 'WeldingProcessController@indexWldJigMonitoring');
Route::get('fetch/welding/monitoring_jig', 'WeldingProcessController@fetchWldJigMonitoring');
Route::get('fetch/welding/detail_monitoring_jig', 'WeldingProcessController@fetchWldDetailJigMonitoring');
Route::get('fetch/welding/detail_monitoring_jig_periode', 'WeldingProcessController@fetchWldDetailJigMonitoringPeriode');

Route::get('index/welding/kensa_jig_report', 'WeldingProcessController@indexKensaJigReport');
Route::get('fetch/welding/kensa_jig_report', 'WeldingProcessController@fetchKensaJigReport');
Route::get('fetch/welding/detail_kensa_jig_report', 'WeldingProcessController@fetchDetailKensaJigReport');

Route::get('index/welding/repair_jig_report', 'WeldingProcessController@indexRepairJigReport');
Route::get('fetch/welding/repair_jig_report', 'WeldingProcessController@fetchRepairJigReport');
Route::get('fetch/welding/detail_repair_jig_report', 'WeldingProcessController@fetchDetailRepairJigReport');

//END JIG KENSA

//JIG PROSES

Route::get('index/welding/kensa_jig_proses/{jig_id}/{from}', 'WeldingProcessController@indexWeldingKensaJigProcess');
Route::get('fetch/welding/jig_check_proses', 'WeldingProcessController@fetchJigCheckProcess');
Route::get('fetch/welding/drawing_list_proses', 'WeldingProcessController@fetchDrawingListProcess');
Route::post('input/welding/kensa_jig_proses', 'WeldingProcessController@inputKensaJigProcess');

Route::get('index/welding/repair_jig_proses/{jig_id}/{from}', 'WeldingProcessController@indexWeldingRepairJigProses');
Route::post('input/welding/repair_jig_proses', 'WeldingProcessController@inputRepairJigProses');

Route::get('index/welding/kensa_jig_report_proses', 'WeldingProcessController@indexKensaJigReportProcess');
Route::get('fetch/welding/kensa_jig_report_proses', 'WeldingProcessController@fetchKensaJigReportProcess');
Route::get('fetch/welding/detail_kensa_jig_report_proses', 'WeldingProcessController@fetchDetailKensaJigReportProcess');

Route::get('index/welding/repair_jig_report_proses', 'WeldingProcessController@indexRepairJigReportProcess');
Route::get('fetch/welding/repair_jig_report_proses', 'WeldingProcessController@fetchRepairJigReportProcess');
Route::get('fetch/welding/detail_repair_jig_report_proses', 'WeldingProcessController@fetchDetailRepairJigReportProcess');

Route::get('index/welding/monitoring_jig_proses', 'WeldingProcessController@indexWldJigMonitoringProcess');
Route::get('fetch/welding/monitoring_jig_proses', 'WeldingProcessController@fetchWldJigMonitoringProcess');
Route::get('fetch/welding/detail_monitoring_jig_proses', 'WeldingProcessController@fetchWldDetailJigMonitoringProcess');
Route::get('fetch/welding/detail_monitoring_jig_periode_proses', 'WeldingProcessController@fetchWldDetailJigMonitoringPeriodeProcess');

Route::get('index/welding/jig_data_proses', 'WeldingProcessController@indexWeldingJigDataProcess');
Route::get('fetch/welding/jig_data_proses', 'WeldingProcessController@fetchWeldingJigDataProcess');

Route::get('index/welding/jig_schedule_proses', 'WeldingProcessController@indexWeldingJigScheduleProcess');
Route::get('fetch/welding/jig_schedule_proses', 'WeldingProcessController@fetchWeldingJigScheduleProcess');

Route::get('index/welding/jig_part_proses', 'WeldingProcessController@indexWeldingJigPartProcess');
Route::get('fetch/welding/jig_part_proses', 'WeldingProcessController@fetchWeldingJigPartProcess');

Route::get('index/welding/jig_part_proses', 'WeldingProcessController@indexWeldingJigPartProcess');
Route::get('fetch/welding/jig_part_proses', 'WeldingProcessController@fetchWeldingJigPartProcess');

//END JIG PROSES

//Sakurentsu
Route::get('index/sakurentsu/upload_sakurentsu', 'SakurentsuController@upload_sakurentsu');
Route::post('index/sakurentsu/upload_sakurentsu', 'SakurentsuController@upload_file_sakurentsu');
Route::post('index/sakurentsu/update_sakurentsu', 'SakurentsuController@update_file_sakurentsu');
Route::get('index/sakurentsu/list_sakurentsu_translate', 'SakurentsuController@index_translate_sakurentsu');
Route::get('index/sakurentsu/upload_sakurentsu_translate/{id}', 'SakurentsuController@upload_sakurentsu_translate');
Route::post('index/sakurentsu/upload_sakurentsu_translate/{id}', 'SakurentsuController@upload_file_sakurentsu_translate');
Route::get('fetch/sakurentsu', 'SakurentsuController@fetch_sakuretsu');
Route::get('fetch/sakurentsu/translate', 'SakurentsuController@fetch_translate_sakurentsu');
Route::get('index/sakurentsu/monitoring', 'SakurentsuController@monitoring');
Route::get('index/sakurentsu/list_sakurentsu', 'SakurentsuController@index_sakurentsu');
Route::get('index/sakurentsu/detail/{id}', 'SakurentsuController@detail_sakurentsu');
Route::get('fetch/sakurentsu/type', 'SakurentsuController@fetch_sakurentsu');
Route::post('post/sakurentsu/type', 'SakurentsuController@post_sakurentsu_type');
Route::get('index/sakurentsu/message/{msg}', 'SakurentsuController@message');
Route::post('delete/sakurentsu', 'SakurentsuController@deleteSakurentsu');

//3M
Route::get('index/sakurentsu/list_3m', 'SakurentsuController@index_tiga_em');
Route::get('fetch/sakurentsu/list_3m', 'SakurentsuController@fetch_tiga_em');
Route::get('index/sakurentsu/3m/translate/{id}', 'SakurentsuController@index_translate_tiga_em');

Route::get('index/sakurentsu/3m', 'SakurentsuController@index_form_tiga_em_new');
Route::get('index/sakurentsu/3m_trial/{trial_id}', 'SakurentsuController@index_form_tiga_em_trial');
Route::get('index/sakurentsu/3m/{sakurentsu_number}', 'SakurentsuController@index_form_tiga_em');
Route::post('post/sakurentsu/3m_form', 'SakurentsuController@save_tiga_em_form');
Route::post('post/sakurentsu/3m/translate', 'SakurentsuController@save_tiga_em_translate');

Route::get('index/sakurentsu/3m/premeeting/{id_three_m}', 'SakurentsuController@index_tiga_em_premeeting');
Route::post('post/sakurentsu/3m/premeeting', 'SakurentsuController@post_tiga_em_premeeting');
Route::get('fetch/sakurentsu/3m/document', 'SakurentsuController@fetch_tiga_em_document');
Route::post('upload/sakurentsu/3m/document', 'SakurentsuController@upload_tiga_em_document');
Route::post('mail/sakurentsu/3m/document', 'SakurentsuController@mail_tiga_em_document');
Route::post('post/sakurentsu/3m/finalmeeting', 'SakurentsuController@post_tiga_em_finalmeeting');

Route::get('index/sakurentsu/3m/document/upload/{id_three_m}', 'SakurentsuController@index_tiga_em_upload');
Route::post('upload/sakurentsu/3m/document/upload', 'SakurentsuController@upload_tiga_em_upload');

Route::get('index/sakurentsu/3m/finalmeeting/{id_three_m}', 'SakurentsuController@index_tiga_em_finalmeeting');
Route::get('fetch/sakurentsu/3m/document/{id_three_m}', 'SakurentsuController@fetch_tiga_em_document_by_id');
Route::get('detail/sakurentsu/3m/{id_three_m}', 'SakurentsuController@index_tiga_em_detail');
Route::get('detail/sakurentsu/3m/{id_three_m}/{position}', 'SakurentsuController@index_tiga_em_detail2');
Route::get('pdf/sakurentsu/3m/{id_three_m}', 'SakurentsuController@index_tiga_em_pdf');
Route::get('generate/sakurentsu/3m/pdf/{id_three_m}', 'SakurentsuController@generate_tiga_em_pdf');
Route::get('get/sakurentsu/3m', 'SakurentsuController@get_employee_sign');
Route::post('post/sakurentsu/3m/sign', 'SakurentsuController@signing_tiga_em');
Route::get('email/sakurentsu/3m/unsigned', 'SakurentsuController@mail_unsigned_tiga_em');

Route::get('index/sakurentsu/list_3m_document', 'SakurentsuController@index_tiga_em_document');
Route::post('resend/sakurentsu/3m', 'SakurentsuController@resend_three_m');
Route::get('fetch/sakurentsu/3m/temp/special_items', 'SakurentsuController@fetch_special_item_tmp_files');
Route::get('fetch/sakurentsu/3m/temp/special_items/meeting', 'SakurentsuController@fetch_special_item_files');
Route::post('upload/sakurentsu/3m/special_items', 'SakurentsuController@upload_special_item_files');
Route::get('index/sakurentsu/3m/special_items/eviden/{form_number}', 'SakurentsuController@index_upload_special_item');
Route::post('update/sakurentsu/3m/special_items/', 'SakurentsuController@update_special_items');

//Approve dari email
Route::get('approve/sakurentsu/3m/sign/{id_three_m}/{position}', 'SakurentsuController@approve_tiga_em');
Route::get('approve/sakurentsu/3m/sign/pic/{id_three_m}/{position}', 'SakurentsuController@approve_tiga_em_pic');

//Tugaskan ke PIC
Route::get('index/sakurentsu/assign/{sk_num}/{cat}', 'SakurentsuController@index_assign_to_staff');
Route::get('assign/sakurentsu', 'SakurentsuController@assign_to_staff');

// 3M Implementasi
Route::post('post/sakurentsu/3m/receive_std', 'SakurentsuController@receive_tiga_em');
Route::get('index/sakurentsu/3m/implement/{id_three_m}', 'SakurentsuController@index_tiga_em_implement');
Route::post('post/sakurentsu/3m/implement', 'SakurentsuController@post_tiga_em_implement');

// Approval 3M MIRAI
Route::get('index/sakurentsu/sign/{id}/{cat}', 'SakurentsuController@index_approval_tiga_em');
Route::get('index/sakurentsu/3m/implementation/sign/{id}/{cat}', 'SakurentsuController@index_approval_implement_tiga_em');

// Approval Trial Request MIRAI
Route::get('index/sakurentsu/trial/sign/{id}/{cat}', 'TrialRequestController@index_approval_trial');

// Route::get('index/sakurentsu/3m/implement/{id_three_m}/{category}', 'SakurentsuController@index_tiga_em_implement2');
Route::get('approve/sakurentsu/3m/implementation/sign/{id_three_m}/{category}', 'SakurentsuController@signing_implement_tiga_em');
Route::get('reminder/sakurentsu/3m/{id_three_m}', 'SakurentsuController@index_reminder_tiga_em');
Route::post('post/reminder/sakurentsu/3m', 'SakurentsuController@reminder_tiga_em');

//3M Monitoring
Route::get('index/sakurentsu/monitoring/3m', 'SakurentsuController@tiga_3m_monitoring');
Route::get('fetch/sakurentsu/monitoring/3m', 'SakurentsuController@fetch_tiga_em_monitoring');
Route::get('index/sakurentsu/monitoring/3m/{category}', 'SakurentsuController@monitoring_notifikasi');
Route::get('fetch/sakurentsu/3m/dept_sign/{id_three_m}/{stat}', 'SakurentsuController@fetch_department_sign');
Route::get('fetch/sakurentsu/monitoring/chart_detail', 'SakurentsuController@fetch_monitoring_detail');
Route::get('index/sakurentsu/display/monitoring/{loc}', 'SakurentsuController@index3MDisplay');
Route::get('fetch/sakurentsu/display/monitoring', 'SakurentsuController@fetch3MDisplay');

//Retarget
Route::post('post/sakurentsu/3m/retarget', 'SakurentsuController@retarget_3m');

//Information
Route::get('index/sakurentsu/information/receive/{sk_num}', 'SakurentsuController@index_receive_information');
Route::post('post/sakurentsu/information/receive', 'SakurentsuController@post_receive_information');
Route::get('fetch/sakurentsu/information/detail/{sk_num}', 'SakurentsuController@detail_receive_information');

//summary
Route::get('index/sakurentsu/summary', 'SakurentsuController@index_summary');
Route::get('index/sakurentsu/summary/{cat}', 'SakurentsuController@index_summary_all');
Route::get('fetch/sakurentsu/summary/{cat}', 'SakurentsuController@fetch_summary_all');

//Trial Request
Route::get('index/trial_request', 'TrialRequestController@index_trial_request');
Route::get('fetch/trial_request', 'TrialRequestController@fetch_trial_request');
Route::get('fetch/trial_request/leader', 'TrialRequestController@fetch_trial_request_leader');
Route::post('create/trial_request', 'TrialRequestController@create_trial_request');
Route::get('approval/sakurentsu/trial_request/{id_trial}/{position}', 'TrialRequestController@approval_trial_request');
Route::get('approval/sakurentsu/trial_request/{stat}/{id_trial}/{position}', 'TrialRequestController@reject_trial_request');
Route::post('comment/sakurentsu/trial_request', 'TrialRequestController@comment_trial_request');
Route::get('receive/sakurentsu/trial_request/{id_trial}/{position}', 'TrialRequestController@receive_trial_request');
Route::get('approval/receive/sakurentsu/trial_request', 'TrialRequestController@approval_receive_trial_request');
Route::post('send/trial_request/trial_result', 'TrialRequestController@send_mail_trial_request_result');
Route::get('report/sakurentsu/trial_request/issue/{trial_id}', 'TrialRequestController@test_pdf');

Route::get('result/sakurentsu/trial_request/{form_number}/{section}', 'TrialRequestController@index_approval_trial_result');
Route::post('update/trial_request/trial_result', 'TrialRequestController@update_trial_result');
Route::post('upload/trial_request/qc_report', 'TrialRequestController@upload_qc_report');

Route::post('post/sakurentsu/trial_request/bom', 'TrialRequestController@save_bom');
Route::post('post/sakurentsu/trial_request/sales_price', 'ExtraOrderController@updateSalesPrice');
Route::post('post/trial_request/three_m_status', 'TrialRequestController@update_three_m_status');

Route::post('post/extra_order/existing_material', 'ExtraOrderController@updateExistingMaterial');
Route::post('post/extra_order/existing_description', 'ExtraOrderController@updateExistingDescription');

Route::get('index/trial_request_leader', 'TrialRequestController@index_trial_request_leader');
Route::get('final/sakurentsu/trial_request/{id_trial}/{position}', 'TrialRequestController@approval_final_trial_request');

//TRIAL (TEMP)
Route::post('upload/sakurentsu/trial_req/notulen', 'SakurentsuController@upload_trial_notulen');

Route::get('post/sakurentsu/trial_req/{sakurentsu_number}', 'SakurentsuController@post_trial_request');
Route::get('index/sakurentsu/list_trial_temp', 'SakurentsuController@index_trial_request_temp');
Route::get('fetch/sakurentsu/list_trial', 'SakurentsuController@fetch_trial_request2');
Route::post('upload/sakurentsu/trial', 'SakurentsuController@upload_trial_request');
Route::get('index/sakurentsu/trial/pss/{sk_num}', 'SakurentsuController@index_trial_pss');
Route::get('post/sakurentsu/pss', 'SakurentsuController@save_pss_desc');
Route::get('index/sakurentsu/pss/upload/{sk_num}', 'SakurentsuController@index_upload_pss');
Route::get('upload/sakurentsu/trial_req/pss', 'SakurentsuController@upload_pss');
Route::post('upload/sakurentsu/trial_req/pss/doc', 'SakurentsuController@upload_pss_doc');
Route::get('receive/sakurentsu/trial/meeting/{sk_id}', 'SakurentsuController@receive_trial_pc');
Route::get('fetch/sakurentsu/trial_request/approval/{form_num}', 'TrialRequestController@fetch_trial_detail_approval');
Route::get('fetch/sakurentsu/trial_request/receive/{form_num}', 'TrialRequestController@fetch_trial_detail_receive');
Route::get('fetch/sakurentsu/trial_request/result/{form_num}', 'TrialRequestController@fetch_trial_detail_result');
Route::post('resend/sakurentsu/trial_request/{form_num}', 'TrialRequestController@resendemail');
Route::post('resend/sakurentsu/trial_request_receive/{form_num}', 'TrialRequestController@resendemailReceive');

//TRIAL
Route::get('index/sakurentsu/list_trial', 'SakurentsuController@index_trial_request');
Route::get('index/sakurentsu/list_material', 'SakurentsuController@index_material');
Route::get('fetch/sakurentsu/list_material', 'SakurentsuController@fetch_material');

// ---------------------  NOTIFIKASI -----------------
Route::get('index/sakurentsu/3m/notifikasi/approval', 'SakurentsuController@tiga_m_approval');

//Supplier
Route::get('index/supplier', 'AccountingController@master_supplier');
Route::get('fetch/supplier', 'AccountingController@fetch_supplier');

//Purchase Item
Route::get('index/purchase_item', 'AccountingController@master_item');
Route::get('fetch/purchase_item', 'AccountingController@fetch_item');

//Purchase Item
Route::get('index/purchase_new_item', 'AccountingController@master_new_item');
Route::get('fetch/purchase_new_item', 'AccountingController@fetch_new_item');

//Item Category
Route::get('index/purchase_item/create_category', 'AccountingController@create_item_category');
Route::post('index/purchase_item/create_category', 'AccountingController@create_item_category_post');

Route::group(['nav' => 'S43', 'middleware' => 'permission'], function () {
    Route::get('index/supplier/create', 'AccountingController@create_supplier');
    Route::post('index/supplier/create_post', 'AccountingController@create_supplier_post');
    Route::get('index/supplier/update/{id}', 'AccountingController@update_supplier');
    Route::post('index/supplier/update', 'AccountingController@update_supplier_post');
    Route::get('index/supplier/delete/{id}', 'AccountingController@delete_supplier');

    Route::get('index/purchase_item/create', 'AccountingController@create_item');
    Route::post('index/purchase_item/create_post', 'AccountingController@create_item_post');
    Route::get('index/purchase_item/update/{id}', 'AccountingController@update_item');

    Route::get('index/purchase_item/delete/{id}', 'AccountingController@delete_item');
    Route::get('index/purchase_item/get_kode_item', 'AccountingController@get_kode_item');
});

//Exchange Rate

Route::group(['nav' => 'S44', 'middleware' => 'permission'], function () {
    Route::get('index/exchange_rate', 'AccountingController@exchange_rate');
    Route::get('fetch/exchange_rate', 'AccountingController@fetch_exchange_rate');
    Route::post('create/exchange_rate', 'AccountingController@create_exchange_rate');
    Route::post('delete/exchange_rate', 'AccountingController@delete_exchange_rate');

    //File Manager
    Route::get('index/filemanager', 'FileManagerController@index')->name('FileManagerIndex');
    Route::get('fetch/filemanager/', 'FileManagerController@getAllFiles');

    Route::post('index/filemanager/files/upload', 'FileManagerController@uploadFile');
    Route::post('index/filemanager/files/deleteFile', 'FileManagerController@deleteFile');
    Route::post('index/filemanager/files/editFile', 'FileManagerController@editFile');

    Route::get('fetch/filemanager/files/viewAttachment', 'FileManagerController@viewAttachment');
    Route::post('index/filemanager/files/uploadAttachment', 'FileManagerController@uploadAttachment');
    Route::post('index/filemanager/files/deleteAttachment', 'FileManagerController@deleteAttachment');
    Route::post('index/filemanager/files/dueDateControll', 'FileManagerController@dueDateControll');

    Route::get('fetch/filemanager/files/getCategory', 'FileManagerController@getAllCategory');
    Route::post('index/filemanager/files/addCategory', 'FileManagerController@addCategory');
    Route::post('index/filemanager/files/updateSubCategory', 'FileManagerController@updateSubCategory');
    Route::post('index/filemanager/files/deleteCategory', 'FileManagerController@deleteCategory');

    Route::get('fetch/filemanager/files/chart', 'FileManagerController@getChart');
    Route::post('send/filemanager/files/sendEmail', 'FileManagerController@sendEmail');
});

//nomor PR
Route::get('purchase_requisition/get_nomor_pr', 'AccountingController@get_nomor_pr');

//Purchase Requisition
Route::get('purchase_requisition', 'AccountingController@purchase_requisition');
Route::get('fetch/purchase_requisition', 'AccountingController@fetch_purchase_requisition');
Route::post('create/purchase_requisition', 'AccountingController@create_purchase_requisition');
Route::get('purchase_requisition/sendemail', 'AccountingController@pr_send_email');
Route::get('purchase_requisition/resendemail', 'AccountingController@pr_resend_email');
Route::get('fetch/purchase_requisition/itemlist', 'AccountingController@fetchItemList');
Route::get('purchase_requisition/get_detailitem', 'AccountingController@prgetitemdesc')->name('admin.prgetitemdesc');
Route::get('fetch/purchase_requisition/budgetlist', 'AccountingController@fetchBudgetList');
Route::get('purchase_requisition/get_detailbudget', 'AccountingController@prgetbudgetdesc')->name('admin.prgetbudgetdesc');
Route::get('purchase_requisition/detail/{id}', 'AccountingController@detail_purchase_requisition');
Route::get('purchase_requisition/get_exchange_rate', 'AccountingController@get_exchange_rate');
Route::get('edit/purchase_requisition', 'AccountingController@edit_purchase_requisition');
Route::get('tracing/purchase_requisition', 'AccountingController@tracing_purchase_requisition');
Route::get('detail/purchase_requisition/po', 'AccountingController@detail_pr_po');
Route::post('update/purchase_requisition', 'AccountingController@update_purchase_requisition');
Route::post('delete/purchase_requisition', 'AccountingController@delete_purchase_requisition');
Route::post('delete/purchase_requisition_item', 'AccountingController@delete_item_pr');
Route::get('purchase_requisition/report/{id}', 'AccountingController@report_purchase_requisition');
Route::get('purchase_requisition/check/{id}', 'AccountingController@check_purchase_requisition');
Route::post('purchase_requisition/checked/{id}', 'AccountingController@checked_purchase_requisition');

//Approval Purchase Requisition
Route::get('purchase_requisition/verifikasi/{id}', 'AccountingController@verifikasi_purchase_requisition');
Route::post('purchase_requisition/approval/{id}', 'AccountingController@approval_purchase_requisition');
Route::post('purchase_requisition/notapprove/{id}', 'AccountingController@reject_purchase_requisition');

//New Approval Purchase Requisition
Route::get('purchase_requisition/approvemanager/{id}', 'AccountingController@prapprovalmanager');
Route::get('purchase_requisition/approvedgm/{id}', 'AccountingController@prapprovaldgm');
Route::get('purchase_requisition/approvegm/{id}', 'AccountingController@prapprovalgm');
Route::get('purchase_requisition/reject/{id}', 'AccountingController@prreject');

Route::post('purchase_requisition/comment/{id}', 'AccountingController@comment_purchase_requisition');

//PR Monitoring & Control
Route::get('purchase_requisition/monitoring', 'AccountingController@monitoringPR');
Route::get('fetch/purchase_requisition/monitoring', 'AccountingController@fetchMonitoringPR');
Route::get('fetch/purchase_requisition/outstanding', 'AccountingController@fetchMonitoringPROutstanding');
Route::get('purchase_requisition/table', 'AccountingController@fetchtablePR');
Route::get('purchase_requisition/detail', 'AccountingController@detailMonitoringPR');
Route::get('purchase_requisition/detailPO', 'AccountingController@detailMonitoringPRPO');
Route::get('purchase_requisition/detailActual', 'AccountingController@detailMonitoringPRActual');

Route::get('purchase_requisition/monitoringpch', 'AccountingController@monitoringPrPch');
Route::get('fetch/purchase_requisition/monitoringpch', 'AccountingController@fetchMonitoringPRPch');
Route::get('purchase_requisition/tablepch', 'AccountingController@fetchtablePRPch');
Route::get('purchase_requisition/detailPch', 'AccountingController@detailMonitoringPRPch');

//PR Monitoring Canteen
Route::get('canteen/purchase_requisition/monitoring', 'AccountingController@monitoringPRCanteen');
Route::get('fetch/canteen/purchase_requisition/monitoring', 'AccountingController@fetchMonitoringPRCanteen');
Route::get('canteen/purchase_requisition/table', 'AccountingController@fetchtablePRCanteen');
Route::get('canteen/purchase_requisition/detail', 'AccountingController@detailMonitoringPRCanteen');
Route::get('canteen/purchase_requisition/detailPO', 'AccountingController@detailMonitoringPRPOCanteen');

Route::group(['nav' => 'S61', 'middleware' => 'permission'], function () {
    //Purchase Requisition
    Route::get('canteen/purchase_requisition', 'GeneralAffairController@canteen_purchase_requisition');
    Route::get('fetch/canteen/purchase_requisition', 'GeneralAffairController@fetch_canteen_purchase_requisition');
    Route::get('fetch/canteen/purchase_requisition/itemlist', 'GeneralAffairController@fetch_item_list');
    Route::get('canteen/purchase_requisition/get_detailitem', 'GeneralAffairController@prgetitemdesc')->name('getcanteenitem');
    Route::post('canteen/create/purchase_requisition', 'GeneralAffairController@create_purchase_requisition');

    //Master Item & Category
    Route::get('canteen/purchase_item', 'GeneralAffairController@master_item');
    Route::get('canteen/fetch/purchase_item', 'GeneralAffairController@fetch_item');

    Route::get('canteen/purchase_item/create', 'GeneralAffairController@create_item');
    Route::post('canteen/purchase_item/create_post', 'GeneralAffairController@create_item_post');
    Route::get('canteen/purchase_item/update/{id}', 'GeneralAffairController@update_item');
    Route::post('canteen/purchase_item/edit_post', 'GeneralAffairController@update_item_post');
    Route::get('canteen/purchase_item/delete/{id}', 'GeneralAffairController@delete_item');
    Route::get('canteen/purchase_item/get_kode_item', 'GeneralAffairController@get_kode_item');

    Route::get('canteen/purchase_item/create_category', 'GeneralAffairController@create_item_category');
    Route::post('canteen/purchase_item/create_category', 'GeneralAffairController@create_item_category_post');

    Route::get('canteen/edit/purchase_requisition', 'GeneralAffairController@edit_purchase_requisition');
    Route::get('canteen/tracing/purchase_requisition', 'GeneralAffairController@tracing_purchase_requisition');
    Route::post('canteen/update/purchase_requisition', 'GeneralAffairController@update_purchase_requisition');

    Route::post('canteen/delete/purchase_requisition', 'GeneralAffairController@delete_purchase_requisition');
    Route::post('canteen/delete/purchase_requisition_item', 'GeneralAffairController@delete_item_pr');
    Route::get('canteen/purchase_requisition/sendemail', 'GeneralAffairController@pr_send_email');

    Route::get('canteen/detail/purchase_requisition/po', 'AccountingController@detail_pr_po_canteen');

    //nomor PR
    Route::get('canteen_purchase_requisition/get_nomor_pr', 'GeneralAffairController@get_nomor_pr');
});

Route::get('canteen/cancel_item', 'AccountingController@canteen_cancel_item');

Route::get('canteen/purchase_requisition/report/{id}', 'GeneralAffairController@report_purchase_requisition');
Route::get('canteen/purchase_requisition/resendemail', 'GeneralAffairController@pr_resend_email');
//Approval Canteen Purchase Requisition
Route::get('canteen_purchase_requisition/approvemanager/{id}', 'AccountingController@canteenprapprovalmanager');
Route::get('canteen_purchase_requisition/approvegm/{id}', 'AccountingController@canteenprapprovalgm');
Route::get('canteen_purchase_requisition/reject/{id}', 'AccountingController@canteenprreject');
Route::get('canteen/purchase_requisition/check/{id}', 'AccountingController@canteen_check_purchase_requisition');
Route::post('canteen/purchase_requisition/checked/{id}', 'AccountingController@canteen_checked_purchase_requisition');

//Approval Purchase Requisition Canteen
Route::get('canteen/purchase_requisition/verifikasi/{id}', 'AccountingController@verifikasi_purchase_requisition_canteen');
Route::post('canteen/purchase_requisition/approval/{id}', 'AccountingController@approval_purchase_requisition_canteen');
Route::post('canteen/purchase_requisition/notapprove/{id}', 'AccountingController@reject_purchase_requisition_canteen');

//PO Monitoring & Control
Route::get('purchase_order/monitoring', 'AccountingController@monitoringPO');
Route::get('fetch/purchase_order/monitoring', 'AccountingController@fetchMonitoringPO');
Route::get('purchase_order/detail', 'AccountingController@detailMonitoringPO');
Route::get('purchase_order/table', 'AccountingController@fetchtablePO');

Route::get('canteen/purchase_order/monitoring', 'AccountingController@monitoringPOCanteen');
Route::get('fetch/canteen/purchase_order/monitoring', 'AccountingController@fetchMonitoringPOCanteen');
Route::get('canteen/purchase_order/detail', 'AccountingController@detailMonitoringPOCanteen');
Route::get('canteen/purchase_order/table', 'AccountingController@fetchtablePOCanteen');

//Approval Purchase Order Canteen
Route::get('purchase_order/verifikasi/{id}', 'AccountingController@verifikasi_purchase_order');
Route::post('purchase_order/approval/{id}', 'AccountingController@approval_purchase_order');
Route::post('purchase_order/notapprove/{id}', 'AccountingController@reject_purchase_order');

Route::get('canteen/purchase_order/verifikasi/{id}', 'AccountingController@verifikasi_purchase_order_canteen');
Route::post('canteen/purchase_order/approval/{id}', 'AccountingController@approval_purchase_order_canteen');
Route::post('canteen/purchase_order/notapprove/{id}', 'AccountingController@reject_purchase_order_canteen');

//New Approval Purchase Order
Route::get('purchase_order/approvemanager/{id}', 'AccountingController@poapprovalmanager');
Route::get('purchase_order/approvedgm/{id}', 'AccountingController@poapprovaldgm');
Route::get('purchase_order/approvegm/{id}', 'AccountingController@poapprovalgm');
Route::get('purchase_order/reject/{id}', 'AccountingController@poreject');
Route::post('purchase_order/notapprove/{id}', 'AccountingController@reject_purchase_order');

Route::get('purchase_order/get_detailsupplier', 'AccountingController@pogetsupplier')->name('admin.pogetsupplier');

//get List PR
Route::get('fetch/purchase_order/prlist', 'AccountingController@fetchPrList');
Route::get('fetch/purchase_order/pilih_pr', 'AccountingController@pilihPR');
Route::get('purchase_order/get_item', 'AccountingController@pogetitem');

Route::post('post/quotation', 'AccountingController@post_quotation');

//Purchase Order Khusus PR
Route::group(['nav' => 'S43', 'middleware' => 'permission'], function () {
    Route::get('purchase_order', 'AccountingController@purchase_order');
    Route::get('fetch/purchase_order', 'AccountingController@fetch_purchase_order');
    Route::get('fetch/purchase_order_pr', 'AccountingController@fetch_po_outstanding_pr');
    Route::post('create/purchase_order', 'AccountingController@create_purchase_order');
    Route::get('purchase_order/get_nomor_po', 'AccountingController@get_nomor_po');
    Route::get('purchase_order/get_detailname', 'AccountingController@pogetname')->name('admin.pogetname');
    Route::get('purchase_order/report/{id}', 'AccountingController@report_purchase_order');
    Route::get('purchase_order/sendemail', 'AccountingController@po_send_email');
    Route::get('purchase_order/resendemail', 'AccountingController@po_resend_email');
    Route::post('purchase_order/edit_sap', 'AccountingController@edit_sap');
    Route::get('edit/purchase_order', 'AccountingController@edit_purchase_order');
    Route::post('update/purchase_order', 'AccountingController@update_purchase_order');
    Route::post('delete/purchase_order_item', 'AccountingController@delete_item_po');

    Route::post('cancel/purchase_order', 'AccountingController@cancel_purchase_order');
    Route::get('export/purchase_order/list', 'AccountingController@exportPO');
    Route::post('update/purchase_requisition/po', 'AccountingController@update_purchase_requisition_po');

    Route::get('purchase_order/delivery_control', 'AccountingController@delivery_control');
    Route::get('purchase_order/jurnal_po', 'AccountingController@jurnal_po');
    Route::get('fetch/purchase_order/jurnal_po', 'AccountingController@fetchJurnal');

    //Purchase Order Khusus investment
    Route::get('purchase_order_investment', 'AccountingController@purchase_order_investment');
    Route::get('fetch/purchase_order_investment', 'AccountingController@fetch_purchase_order_investment');
    Route::get('fetch/po_investment_outstanding', 'AccountingController@fetch_po_outstanding_investment');
    Route::get('fetch/investment_item_detail', 'AccountingController@fetch_investment_detail');
    Route::get('fetch/purchase_order/pilih_investment', 'AccountingController@pilihInvestment');
    Route::get('fetch/purchase_order/invlist', 'AccountingController@fetchInvList');
    Route::get('purchase_order/investment_get_item', 'AccountingController@pogetiteminvest');

    Route::get('edit/investment', 'AccountingController@edit_investment_po');
    Route::post('update/investment/po', 'AccountingController@update_investment_po');

    //Purchase Order Khusus Kantin
    Route::get('purchase_order_canteen', 'AccountingController@purchase_order_canteen');
    Route::post('create/purchase_order_canteen', 'AccountingController@create_purchase_order_canteen');
    Route::get('fetch/purchase_order_canteen', 'AccountingController@fetch_purchase_order_canteen');
    Route::get('fetch/po_canteen_outstanding', 'AccountingController@fetch_po_outstanding_canteen');

    //Update PR / Pilih PR Di PO
    Route::post('update/purchase_requisition_canteen/po', 'AccountingController@update_purchase_requisition_canteen_po');
    Route::get('fetch/purchase_order/kantinlist', 'AccountingController@fetchPrKantinList');
    Route::get('fetch/purchase_order/pilih_prkantin', 'AccountingController@pilihPrKantin');
    Route::get('purchase_order/get_item_kantin', 'AccountingController@pogetitemkantin');

    //report & Approval
    Route::get('canteen/purchase_order/report/{id}', 'AccountingController@report_purchase_order_canteen');
    Route::get('purchase_order_canteen/sendemail', 'AccountingController@po_send_email_canteen');
    Route::get('purchase_order_canteen/resendemail', 'AccountingController@po_resend_email_canteen');
    Route::get('purchase_order_canteen/approvemanager/{id}', 'AccountingController@poapprovalmanagercanteen');
    Route::get('purchase_order_canteen/reject/{id}', 'AccountingController@porejectKatin');

    //edit PO SAP
    Route::post('purchase_order_canteen/edit_sap', 'AccountingController@edit_sap_canteen');

    //edit delete PO
    Route::get('edit/purchase_order_canteen', 'AccountingController@edit_purchase_order_canteen');
    Route::post('update/purchase_order_canteen', 'AccountingController@update_purchase_order_canteen');
    Route::post('delete/purchase_order_canteen_item', 'AccountingController@delete_item_po_canteen');
    Route::post('cancel/purchase_order_canteen', 'AccountingController@cancel_purchase_order_canteen');

    Route::get('export/purchase_order_canteen/list', 'AccountingController@exportPOKantin');

});

Route::get('fetch/purchase_order/log_pembelian', 'AccountingController@fetch_history_pembelian');
Route::get('fetch/purchase_order_canteen/log_pembelian', 'AccountingController@fetch_history_pembelian_canteen');

//investment
Route::get('investment', 'AccountingController@investment');
Route::get('fetch/investment', 'AccountingController@fetch_investment');
Route::get('investment/create', 'AccountingController@create_investment');
Route::post('investment/create_post', 'AccountingController@create_investment_post');
Route::get('investment/detail/{id}', 'AccountingController@detail_investment');
Route::post('investment/update_post', 'AccountingController@detail_investment_post');
Route::get('investment/sendemail', 'AccountingController@investment_send_email');
Route::get('investment/check/{id}', 'AccountingController@check_investment');
Route::get('investment/check_pch/{id}', 'AccountingController@check_investment_pch');
Route::post('investment/checked/{id}', 'AccountingController@checked_investment');
Route::get('investment/resendemail', 'AccountingController@investment_resend_email');

Route::post('investment/check_budget/{id}', 'AccountingController@check_investment_budget');
Route::post('delete/investment_budget', 'AccountingController@delete_investment_budget');
Route::get('investment/get_budget_name', 'AccountingController@get_budget_name')->name('admin.getbudget');
Route::post('delete/investment', 'AccountingController@delete_investment');

//Nomor Investment
Route::get('investment/get_nomor_investment', 'AccountingController@get_nomor_inv');
Route::get('fetch/investment/invbudgetlist', 'AccountingController@fetchInvBudgetList');

Route::get('export/investment/list', 'AccountingController@exportInvestment');

//Upload Adagio
Route::post('investment/adagio', 'AccountingController@post_adagio');

//investment item
Route::post('investment/create_investment_item', 'AccountingController@create_investment_item');
Route::get('investment/fetch_investment_item/{id}', 'AccountingController@fetch_investment_item');
Route::post('investment/edit_investment_item', 'AccountingController@edit_investment_item');
Route::get('investment/edit_investment_item', 'AccountingController@fetch_investment_item_edit');
Route::post('investment/delete_investment_item', 'AccountingController@delete_investment_item');
Route::get('investment/get_detailitem', 'AccountingController@getitemdesc')->name('admin.getitemdesc');
Route::get('investment/report/{id}', 'AccountingController@report_investment');
Route::get('investment/get_totalitem', 'AccountingController@gettotalamount')->name('admin.gettotalamount');

//Approval Investment
Route::get('investment/verifikasi/{id}', 'AccountingController@verifikasi_investment');
Route::post('investment/approval/{id}', 'AccountingController@approval_investment');
Route::post('investment/notapprove/{id}', 'AccountingController@reject_investment');

//New Investment
Route::get('investment/approvemanager/{id}', 'AccountingController@investment_approvalmanager');
Route::get('investment/approvedgm/{id}', 'AccountingController@investment_approvaldgm');
Route::get('investment/approvegm/{id}', 'AccountingController@investment_approvalgm');

Route::get('investment/approvemanageracc/{id}', 'AccountingController@investment_approvalmanageracc');
Route::get('investment/approvediracc/{id}', 'AccountingController@investment_approvaldiracc');
Route::get('investment/approvepresdir/{id}', 'AccountingController@investment_approvalpresdir');

Route::get('investment/comment/{id}', 'AccountingController@investment_comment');
Route::post('investment/comment/{id}', 'AccountingController@investment_comment_post');
Route::get('investment/comment_msg/{id}', 'AccountingController@investment_comment_msg');
Route::post('investment/reject_acc/{id}', 'AccountingController@investment_reject_acc');
Route::get('investment/reject/{id}', 'AccountingController@investment_reject');

//Investment Monitoring & Control
Route::get('investment/control', 'AccountingController@investmentControl');
Route::get('fetch/investment/control', 'AccountingController@fetchInvestmentControl');
Route::get('investment/table', 'AccountingController@fetchtableInv');
Route::get('investment/detail', 'AccountingController@detailMonitoringInv');
Route::get('investment/detailInv', 'AccountingController@detailMonitoringInvTable');
Route::get('investment/detailActual', 'AccountingController@detailMonitoringInvActual');

//Budget
Route::get('budget/info', 'AccountingController@budget_info');
Route::post('budget/edit', 'AccountingController@budget_edit');
Route::get('budget/report', 'AccountingController@budget_control');
Route::get('fetch/budget/info', 'AccountingController@fetch_budget_info');
Route::get('fetch/budget/table', 'AccountingController@fetch_budget_table');
Route::get('fetch/budget/summary', 'AccountingController@fetch_budget_summary');
Route::get('fetch/budget/detail_table', 'AccountingController@fetch_budget_detail');
Route::get('budget/detail', 'AccountingController@budget_detail');
Route::post('import/budget', 'AccountingController@import_budget');
Route::get('export/budget', 'AccountingController@exportBudget');

Route::get('budget/log', 'AccountingController@budget_log');
Route::get('fetch/budget/log', 'AccountingController@fetch_budget_log');

Route::get('index/budget/report', 'AccountingController@budget_report');
Route::get('fetch/budget/report', 'AccountingController@fetch_budget_report');
Route::get('fetch/budget/report/detail', 'AccountingController@fetch_budget_report_detail');

Route::get('index/budget/catatan', 'AccountingController@catatan');
Route::post('post/budget/catatan', 'AccountingController@post_catatan');

//Budget Monthly User
Route::get('budget/monthly', 'AccountingController@budget_monthly');
Route::get('fetch/budget/monthly', 'AccountingController@fetch_budget_monthly');
Route::get('fetch/budget_monthly/table', 'AccountingController@fetch_budget_monthly_table');
Route::get('export/budget_monthly', 'AccountingController@exportBudgetMonthly');

//Transfer Budget
Route::get('transfer/budget', 'AccountingController@transfer_budget');
Route::get('fetch/transfer', 'AccountingController@fetch_transfer_budget');
Route::post('transfer/budget', 'AccountingController@transfer_budget_post');
Route::post('transfer/budget/new', 'AccountingController@transfer_budget_post_new');
Route::get('transfer_budget/approvemanagerfrom/{id}', 'AccountingController@transfer_approvalfrom');
Route::get('transfer_budget/approvemanagerto/{id}', 'AccountingController@transfer_approvalto');

//Receive
Route::get('receive_goods', 'AccountingController@receive_goods');
Route::get('fetch/receive', 'AccountingController@fetch_receive');
Route::get('receive/detail', 'AccountingController@receive_detail');
Route::post('import/receive', 'AccountingController@import_receive');

Route::get('upload_transaksi', 'AccountingController@upload_transaksi');
Route::get('fetch/transaksi', 'AccountingController@fetch_upload_transaksi');
Route::post('import/transaksi', 'AccountingController@import_transaksi');
Route::post('delete/actual/transaksi', 'AccountingController@delete_transaksi');

Route::get('outstanding_all_equipment', 'AccountingController@outstanding_all_equipment');
Route::get('export/outstanding_purchase_requisition', 'AccountingController@exportOutstandingPR');
Route::get('export/outstanding_investment', 'AccountingController@exportOutstandingInvestment');
Route::get('export/outstanding_purchase_order', 'AccountingController@exportOutstandingPO');

//Receive Barang
Route::get('warehouse/receive_equipment', 'AccountingController@wh_receive_equipment');
Route::get('fetch/warehouse/equipment', 'AccountingController@fetch_receive_equipment');
Route::post('fetch/warehouse/update_receive', 'AccountingController@update_receive');
Route::post('fetch/warehouse/update_receive_ga', 'AccountingController@update_receive_ga');

//Receive Barang
Route::get('warehouse/cek_kedatangan', 'AccountingController@cek_kedatangan');
Route::get('fetch/warehouse/cek_kedatangan', 'AccountingController@fetch_kedatangan');

Route::get('ga/cek_kedatangan', 'AccountingController@cek_kedatangan_ga');
Route::get('fetch/ga/cek_kedatangan', 'AccountingController@fetch_kedatangan_ga');

Route::get('warehouse/receive_ga', 'AccountingController@wh_receive_ga');

Route::get('ga/receive_kantin', 'AccountingController@wh_receive_kantin');
Route::get('fetch/ga/receive_kantin', 'AccountingController@fetch_receive_kantin');
Route::post('fetch/ga/update_receive_kantin', 'AccountingController@update_receive_kantin');
Route::get('fetch/ga/outstanding_kedatangan_ga', 'AccountingController@fetch_outstanding_ga');

Route::get('ga/cek_kedatangan/kantin', 'AccountingController@cek_kedatangan_kantin');
Route::get('fetch/ga/cek_kedatangan/kantin', 'AccountingController@fetch_kedatangan_kantin');
Route::get('fetch/ga/outstanding_kedatangan_kantin', 'AccountingController@fetch_outstanding_kantin');

//Print Label Barang
Route::get('warehouse/print_equipment', 'AccountingController@wh_print_equipment');
Route::get('fetch/warehouse/print_equipment', 'AccountingController@fetch_print_equipment');
Route::get('print/warehouse/label/{id}', 'AccountingController@label_kedatangan');

//Cetak Bukti Kedatangan
Route::get('warehouse/cetak_bukti', 'AccountingController@wh_cetak_bukti');
Route::get('fetch/warehouse/cetak_bukti', 'AccountingController@fetch_cetak_bukti');
Route::post('fetch/warehouse/create_bukti', 'AccountingController@create_cetak_bukti');

Route::get('index/warehouse/report_bukti', 'AccountingController@bukti_penerimaan');
Route::get('index/warehouse/report_bukti/{id}', 'AccountingController@cetak_bukti_penerimaan');

Route::get('warehouse/penerimaan_barang/{id}', 'AccountingController@index_penerimaan_barang');
Route::get('fetch/warehouse/penerimaan_barang', 'AccountingController@fetch_penerimaan_barang');
Route::post('post/warehouse/penerimaan_barang', 'AccountingController@post_penerimaan_barang');
Route::get('scan/warehouse/penerimaan_barang', 'AccountingController@fetchEmployeeByTag');

//Invoice Receive Report
Route::get('invoice/receive_report', 'AccountingController@invoice_receive_report');
Route::get('invoice/fetch_receive', 'AccountingController@invoice_fetch_receive');
Route::get('invoice/fetch_receive_data', 'AccountingController@invoice_fetch_receive_data');
Route::post('invoice/import_receive', 'AccountingController@invoice_import_receive');

//Invoice Tanda Terima
Route::get('invoice/tanda_terima', 'AccountingController@index_invoice');
Route::get('fetch/invoice/tanda_terima', 'AccountingController@fetch_invoice');
Route::get('invoice/tanda_terima_detail', 'AccountingController@fetch_invoice_detail');
Route::post('create/invoice/tanda_terima', 'AccountingController@create_invoice');
Route::post('edit/invoice/tanda_terima', 'AccountingController@edit_invoice');
Route::get('invoice/report/{id}', 'AccountingController@report_invoice');
Route::get('export/invoice/tanda_terima', 'AccountingController@export_tanda_terima');

//Payment Request
Route::get('payment_request', 'AccountingController@IndexPaymentRequest');
Route::get('fetch/payment_request', 'AccountingController@fetchPaymentRequest');
Route::post('create/payment_request', 'AccountingController@createPaymentRequest');
Route::get('detail/payment_request', 'AccountingController@fetchPaymentRequestDetail');
Route::post('edit/payment_request', 'AccountingController@editPaymentRequest');
Route::get('report/payment_request/{id}', 'AccountingController@reportPaymentRequest');
Route::get('report/payment_list/{id}', 'AccountingController@reportPaymentList');
Route::get('email/payment_request', 'AccountingController@emailPaymentRequest');
Route::get('payment_request/resendemail', 'AccountingController@payment_resend_email');

//Approval Payment Request
Route::get('payment_request/approvemanager/{id}', 'AccountingController@paymentapprovalmanager');
Route::get('payment_request/approvedgm/{id}', 'AccountingController@paymentapprovaldgm');
Route::get('payment_request/approvegm/{id}', 'AccountingController@paymentapprovalgm');
Route::get('payment_request/receiveacc/{id}', 'AccountingController@paymentreceiveacc');
Route::get('payment_request/reject/{id}', 'AccountingController@paymentreject');
Route::post('payment_request/send_document', 'AccountingController@send_document');

//Cash Payment

//Request Suspend
Route::get('index/suspense', 'AccountingController@IndexSuspend');
Route::get('fetch/suspend', 'AccountingController@fetchSuspend');
Route::post('create/suspend', 'AccountingController@createSuspend');
Route::get('detail/suspend', 'AccountingController@fetchSuspendDetail');
Route::post('edit/suspend', 'AccountingController@editSuspend');
Route::get('report/suspend/{id}', 'AccountingController@reportSuspend');
Route::get('email/suspend', 'AccountingController@emailSuspend');
Route::get('fetch/suspend/monitoring', 'AccountingController@fetchSuspendMonitoring');

Route::get('fetch/suspend/pilih_pr', 'AccountingController@pilihPRSuspend');
Route::get('fetch/suspend/list', 'AccountingController@fetchSuspendList');
Route::get('fetch/suspend/pilih', 'AccountingController@suspendGetDetail');
Route::get('fetch/suspend/get_price', 'AccountingController@prgetprice');

Route::get('index/suspend/control', 'AccountingController@IndexSuspendControl');
Route::get('fetch/suspend/control', 'AccountingController@fetchSuspendControl');
Route::get('fetch/suspend/control/detail', 'AccountingController@fetchSuspendControlDetail');
Route::get('fetch/suspend/report/detail', 'AccountingController@fetchSuspendReportDetail');
Route::get('give/suspend', 'AccountingController@giveSuspend');

//Approval Suspend
Route::get('suspend/approvemanager/{id}', 'AccountingController@suspendapprovalmanager');
Route::get('suspend/approvestaffacc/{id}', 'AccountingController@suspendapprovalstaffacc');
Route::get('suspend/approvemanageracc/{id}', 'AccountingController@suspendapprovalmanageracc');
Route::get('suspend/approvedirektur/{id}', 'AccountingController@suspendapprovaldirektur');
Route::get('suspend/approvepresdir/{id}', 'AccountingController@suspendapprovalpresdir');
Route::get('suspend/receiveacc/{id}', 'AccountingController@suspendreceiveacc');
Route::get('suspend/reject/{id}', 'AccountingController@suspendreject');

//Create Settlement
Route::get('index/settlement', 'AccountingController@IndexSettlement');
Route::get('fetch/settlement', 'AccountingController@fetchSettlement');
Route::post('create/settlement', 'AccountingController@createSettlement');
Route::post('create/settlement/user', 'AccountingController@createSettlementUser');
Route::get('fetch/suspend/list/user', 'AccountingController@fetchSuspendListUser');
Route::get('fetch/suspend/pilih/user', 'AccountingController@suspendGetDetailUser');
Route::get('fetch/settlement/list', 'AccountingController@fetchSettlementList');
Route::get('fetch/settlement/pilih', 'AccountingController@pilihSettlement');
Route::get('fetch/settlement/pilih_item', 'AccountingController@settlementGetDetail');
Route::get('report/settlement/{id}', 'AccountingController@reportSettlement');
Route::get('email/settlement', 'AccountingController@emailSettlement');

//Log Settlement
Route::get('report/petty_cash', 'AccountingController@reportPettyCash');
Route::get('fetch/report/petty_cash', 'AccountingController@fetchReportPettyCash');
Route::get('fetch/report/petty_cash/settlement', 'AccountingController@fetchReportPettyCashSettlement');

//Approval Settlement
Route::get('settlement/approvemanager/{id}', 'AccountingController@settlementapprovalmanager');
Route::get('settlement/approvestaffacc/{id}', 'AccountingController@settlementapprovalstaffacc');
Route::get('settlement/approvemanageracc/{id}', 'AccountingController@settlementapprovalmanageracc');
Route::get('settlement/approvedirektur/{id}', 'AccountingController@settlementapprovaldirektur');
Route::get('settlement/approvepresdir/{id}', 'AccountingController@settlementapprovalpresdir');
Route::get('settlement/receiveacc/{id}', 'AccountingController@settlementreceiveacc');
Route::get('settlement/reject/{id}', 'AccountingController@settlementreject');

//Create Settlement User
Route::get('index/settlement/user', 'AccountingController@IndexSettlementUser');
Route::get('fetch/settlement/user', 'AccountingController@fetchSettlementUser');
Route::get('fetch/settlement/user/detail', 'AccountingController@fetchSettlementUserDetail');
Route::get('email/settlement/user', 'AccountingController@emailSettlementUser');

Route::get('scan/middle/operator', 'MiddleProcessController@scanMiddleOperator');
Route::group(['nav' => 'S12', 'middleware' => 'permission'], function () {
    Route::get('scan/middle/kensa', 'MiddleProcessController@ScanMiddleKensa');
    Route::post('input/middle/kensa', 'MiddleProcessController@inputMiddleKensa');
    Route::post('input/middle/rework', 'MiddleProcessController@inputMiddleRework');
    // Route::post('input/result_middle_kensa', 'MiddleProcessController@inputResultMiddleKensa');
    Route::post('print/middle/barrel', 'MiddleProcessController@printMiddleBarrel');
    Route::post('scan/middle/barrel', 'MiddleProcessController@scanMiddleBarrel');
    Route::post('post/middle_return/barrel_return', 'MiddleProcessController@postProcessMiddleReturn');
    Route::post('post/middle_return/return_inventory', 'MiddleProcessController@postReturnInventory');
    Route::get('print/middle/barrel_reprint', 'MiddleProcessController@printMiddleBarrelReprint');
});

//START EXTRA ORDER

Route::get('index/extra_order', 'ExtraOrderController@indexExtraOrder');
Route::get('fetch/extra_order', 'ExtraOrderController@fetchExtraOrder');
Route::post('fetch/extra_order/generate_upload_data', 'ExtraOrderController@fetchGenerateUploadData');
Route::get('index/extra_order/detail/{eo_number}', 'ExtraOrderController@indexExtraOrderDetail');
Route::get('index/extra_order/po_number', 'ExtraOrderController@downloadPo');
Route::get('index/extra_order/invoice_number', 'ExtraOrderController@downloadIv');
Route::get('index/extra_order/way_bill', 'ExtraOrderController@downloadWayBill');
Route::get('index/extra_order/attachment', 'ExtraOrderController@downloadAtt');
Route::get('index/extra_order/eoc_pdf/{eo_number}', 'ExtraOrderController@indexEocPdf');
Route::get('index/extra_order/send_app_pdf/{send_app}', 'ExtraOrderController@indexSendAppPdf');

Route::post('input/extra_order', 'ExtraOrderController@inputExtraOrder');
Route::post('update/extra_order', 'ExtraOrderController@updateExtraOrder');
Route::post('delete/extra_order_detail', 'ExtraOrderController@deleteExtraOrderDetail');
Route::get('fetch/show_extra_order', 'ExtraOrderController@fetchShowExtraOrder');
Route::get('index/extra_order/send_trial_request/{eo_number}', 'ExtraOrderController@sendTrialRequest');
Route::get('index/extra_order/send_price_request/{eo_number}', 'ExtraOrderController@sendPriceRequest');

//Approval
Route::post('input/extra_order/send_eoc', 'ExtraOrderController@sendApprovalEoc');
Route::get('index/extra_order/resend_eoc/{approval_id}', 'ExtraOrderController@sendEmailApproval');
Route::get('index/extra_order/view_approval/{approval_id}', 'ExtraOrderController@indexEmailApproval');
Route::get('index/extra_order/approval_eoc', 'ExtraOrderController@inputApprovalEoc');
Route::post('index/extra_order/comment_eoc', 'ExtraOrderController@inputCommentEoc');
Route::get('index/extra_order/reset_eoc/{approval_id}', 'ExtraOrderController@inputResetEoc');
Route::get('index/extra_order/generate_smbmr', 'ExtraOrderController@generateSmbmr');

//Upload PO
Route::get('index/extra_order/upload_po', 'ExtraOrderController@indexUploadPo');
Route::post('input/extra_order_po', 'ExtraOrderController@inputExtraOrderPoNew');
Route::get('index/extra_order/po_notification/{eo_number}', 'ExtraOrderController@indexPoNotification');
Route::get('index/extra_order/resend_po/{eo_number}', 'ExtraOrderController@resendUploadPo');

Route::get('index/extra_order/resend_approval_po/{eo_number}', 'ExtraOrderController@sendApprovalPo');
Route::get('input/extra_order/po_apparove', 'ExtraOrderController@inputApprovalPo');
Route::get('index/extra_order/po_reject', 'ExtraOrderController@indexRejectPo');
Route::post('input/extra_order/po_reject', 'ExtraOrderController@inputRejectPo');

//Completion
Route::get('index/extra_order/completion_page', 'ExtraOrderController@indexCompletionPage');
Route::get('fetch/extra_order/completion_target', 'ExtraOrderController@fetchCompletionPage');
Route::post('input/extra_order/completion', 'ExtraOrderController@inputCompletion');
Route::get('index/label_extra_order/{eo_number_sequence}', 'ExtraOrderController@indexLabelExtraOrder');
Route::get('fetch/extra_order_detail', 'ExtraOrderController@fetchExtraOrderDetail');
Route::get('index/extra_order/bom_multi_level/{material_number}', 'ExtraOrderController@indexBomMultiLevel');

//EO WH menu
Route::group(['nav' => 'S29', 'middleware' => 'permission'], function () {

    Route::get('index/extra_order/delivery_page', 'ExtraOrderController@indexDeliveryPage');
    Route::post('input/extra_order/delivery', 'ExtraOrderController@inputDelivery');

    Route::get('index/extra_order/stuffing_page', 'ExtraOrderController@indexStuffingPage');
    Route::post('input/extra_order/stuffing', 'ExtraOrderController@inputStuffing');

    Route::post('input/extra_order/cancel', 'ExtraOrderController@inputCancel');

    // SEND APP
    Route::get('index/extra_order/sending_application', 'ExtraOrderController@indexSendingApplication');
    Route::get('fetch/extra_order/sending_application', 'ExtraOrderController@fetchSendingApplication');
    Route::get('fetch/extra_order/warehouse_stock', 'ExtraOrderController@fetchWarehouseStock');

    Route::get('fetch/extra_order/detail_sending_application', 'ExtraOrderController@fetchDetailSendingApplication');
    Route::post('input/extra_order/input_sending_application', 'ExtraOrderController@inputSendingApplication');
    Route::post('edit/extra_order/input_sending_application', 'ExtraOrderController@editSendingApplication');
    Route::post('delete/extra_order/input_sending_application', 'ExtraOrderController@deleteSendingApplication');
    Route::get('input/extra_order/sending_application/approval_delete', 'ExtraOrderController@inputApprovalDeleteSendingApplication');

    Route::post('send/extra_order/sending_application', 'ExtraOrderController@sendApplication');
    Route::post('input/extra_order/measurement', 'ExtraOrderController@inputMeasurement');
    Route::post('input/extra_order/input_shipping_document', 'ExtraOrderController@inputShippingDocument');
    Route::post('input/extra_order/input_complete_sending', 'ExtraOrderController@inputCompleteSending');

});
Route::post('send/extra_order/complete_sending_application/{send_app_no}', 'ExtraOrderController@emailCompleteExtraOrder');

Route::get('index/extra_order/approval_monitoring', 'ExtraOrderController@indexApprovalMonitoring');
Route::get('fetch/extra_order/approval_monitoring', 'ExtraOrderController@fetchApprovalMonitoring');
Route::get('fetch/extra_order/approval_chart', 'ExtraOrderController@fetchApprovalChart');

Route::get('index/extra_order/data', 'ExtraOrderController@indexExtraOrderData');
Route::get('fetch/extra_order/data', 'ExtraOrderController@fetchExtraOrderData');

Route::get('index/extra_order/shortage_monitoring', 'ExtraOrderController@indexShortageMonitoring');
Route::get('fetch/extra_order/shortage_monitoring', 'ExtraOrderController@fetchShortageMonitoring');

//END EXTRA ORDER

//START KD
//MOUTHPIECE
Route::group(['nav' => 'S26', 'middleware' => 'permission'], function () {
    Route::get('scan/kd_mouthpiece/operator', 'MouthpieceController@scanKdMouthpieceOperator');

    Route::get('index/kd_mouthpiece/checksheet', 'MouthpieceController@indexKdMouthpieceChecksheet');
    Route::get('fetch/kd_mouthpiece/material', 'MouthpieceController@fetchKdMouthpieceMaterial');
    Route::get('fetch/kd_mouthpiece/checksheet', 'MouthpieceController@fetchKdMouthpieceChecksheet');
    Route::post('create/kd_mouthpiece/checksheet', 'MouthpieceController@createKdMouthpieceChecksheet');
    Route::post('delete/kd_mouthpiece/checksheet', 'MouthpieceController@deleteKdMouthpieceChecksheet');
    Route::get('reprint/kd_mouthpiece/checksheet', 'MouthpieceController@reprintKdMouthpieceChecksheet');

    Route::get('index/kd_mouthpiece/picking', 'MouthpieceController@indexKdMouthpiecePicking');
    Route::get('fetch/kd_mouthpiece/picking', 'MouthpieceController@fetchKdMouthpiecePicking');
    Route::post('scan/kd_mouthpiece/picking', 'MouthpieceController@scanKdMouthpiecePicking');
    Route::post('create/kd_mouthpiece/picking', 'MouthpieceController@createKdMouthpiecePicking');

    Route::get('index/kd_mouthpiece/packing', 'MouthpieceController@indexKdMouthpiecePacking');
    Route::get('fetch/kd_mouthpiece/packing', 'MouthpieceController@fetchKdMouthpiecePacking');
    Route::get('check/kd_mouthpiece/checksheet', 'MouthpieceController@checkKdMouthpieceChecksheet');
    Route::post('scan/kd_mouthpiece/packing', 'MouthpieceController@scanKdMouthpiecePacking');
    Route::post('create/kd_mouthpiece/packing', 'MouthpieceController@createKdMouthpiecePacking');

    Route::get('index/kd_mouthpiece/qa_check', 'MouthpieceController@indexKdMouthpieceQaCheck');
    Route::post('scan/kd_mouthpiece/qa_check', 'MouthpieceController@scanKdMouthpieceQaCheck');

    Route::get('index/kd_mouthpiece/log', 'MouthpieceController@indexKdMouthpieceLog');
    Route::get('fetch/kd_mouthpiece/log', 'MouthpieceController@fetchKdMouthpieceLog');

    Route::get('index/kd_mouthpiece/{id}', 'KnockDownController@indexKD');
    // Route::post('fetch/kd_print_mp', 'KnockDownController@printLabelNew');
    Route::post('fetch/kd_print_mp', 'KnockDownController@printLabelNewSingle');

    Route::get('index/print_label_mouthpiece/{id}', 'KnockDownController@indexPrintLabelSubassy');
});

//PIANICA
Route::group(['nav' => 'S55', 'middleware' => 'permission'], function () {
    Route::get('index/kd_pn_part/{id}', 'KnockDownController@indexKD');
    Route::post('fetch/kd_print_pn_part', 'KnockDownController@printLabelPnPart');

    Route::get('index/print_label_pn_part/{id}', 'KnockDownController@indexPrintLabelPnPart');
});

//RC ASSY
Route::group(['nav' => 'S80', 'middleware' => 'permission'], function () {
    Route::get('index/kd_rc_assy/{id}', 'KnockDownController@indexKD');
    Route::post('fetch/kd_print_rc_assy', 'KnockDownController@printLabelNewSingle');
    Route::get('index/print_label_rc_assy/{id}', 'KnockDownController@indexPrintLabelSubassy');
});

//VNENOVA
Route::group(['nav' => 'S56', 'middleware' => 'permission'], function () {
    Route::get('index/kd_vn_assy/{id}', 'KnockDownController@indexKD');
    Route::get('index/print_label_vn_assy/{id}', 'KnockDownController@indexPrintLabelSubassy');
    //REQUEST
    Route::post('fetch/kd_print_vn_assy', 'KnockDownController@printLabelNew');
    //FORECAST
    Route::post('fetch/kd_print_vn_assy_single', 'KnockDownController@printLabelNewSingle');

    Route::get('index/kd_vn_injection/{id}', 'KnockDownController@indexKD');
    Route::get('index/print_label_vn_injection/{id}', 'KnockDownController@indexPrintLabelSubassy');
    //REQUEST
    Route::post('fetch/kd_print_vn_injection', 'KnockDownController@printLabelNew');
    //FORECAST
    Route::post('fetch/kd_print_vn_injection_single', 'KnockDownController@printLabelNewSingle');

});

//CASE
Route::group(['nav' => 'S53', 'middleware' => 'permission'], function () {
    Route::get('index/kd_case/{id}', 'KnockDownController@indexKD');
    Route::post('fetch/kd_print_case', 'KnockDownController@printLabelCase');
    Route::get('index/print_label_case/{id}', 'KnockDownController@indexPrintLabelCase');
});

//CLBODY
Route::group(['nav' => 'S58', 'middleware' => 'permission'], function () {
    Route::get('index/kd_cl_body/{id}', 'KnockDownController@indexKD');
    Route::post('fetch/kd_print_cl_body', 'KnockDownController@printLabelNewSingle');

    Route::get('index/print_label_cl_body/{id}', 'KnockDownController@indexPrintLabelClBody');
});

//TANPO
Route::group(['nav' => 'S52', 'middleware' => 'permission'], function () {
    Route::get('index/kd_tanpo/{id}', 'KnockDownController@indexKD');
    Route::post('fetch/kd_print_tanpo', 'KnockDownController@printLabelTanpo');
    Route::post('fetch/kd_force_print_tanpo', 'KnockDownController@forcePrintLabel');

    Route::get('index/print_label_tanpo/{kd_number}', 'KnockDownController@indexPrintLabelTanpo');
});
Route::get('index/process/tanpo_stock_monitoring', 'AssemblyProcessController@indexTanpoStockMonitoring');
Route::get('fetch/process/tanpo_stock_monitoring', 'AssemblyProcessController@fetchTanpoStockMonitoring');

//ZPRO
Route::group(['nav' => 'S24', 'middleware' => 'permission'], function () {
    Route::get('index/kd_zpro/{id}', 'KnockDownController@indexKD');
    Route::post('fetch/kd_print_zpro', 'KnockDownController@printLabel');
    Route::post('fetch/kd_print_zpro_new', 'KnockDownController@printLabelNew');
    Route::post('fetch/kd_print_zpro_ending_stock', 'KnockDownController@printLabelTanpo');
    Route::post('fetch/kd_force_print_zpro', 'KnockDownController@forcePrintLabel');

    Route::get('index/print_label_zpro/{id}', 'KnockDownController@indexPrintLabelZpro');
    Route::get('index/print_label_zpro_direct/{material_number}/{quantity}', 'KnockDownController@indexPrintLabelZproDirect');
});

//MPRO
Route::group(['nav' => 'S51', 'middleware' => 'permission'], function () {
    Route::get('index/kd_mpro/{id}', 'KnockDownController@indexKD');
    //Based on shipment sch parsial
    Route::post('fetch/kd_print_mpro', 'KnockDownController@printLabelNewParsial');
    Route::get('index/print_label_mpro/{shipment_schedule_id}/{kd_number}', 'KnockDownController@indexPrintLabelMpro');
});

//BPRO
Route::group(['nav' => 'S60', 'middleware' => 'permission'], function () {
    Route::get('index/kd_bpro/{id}', 'KnockDownController@indexKD');
    Route::post('fetch/kd_print_bpro', 'KnockDownController@printLabelNewSingle');

    Route::get('index/print_label_bpro/{id}', 'KnockDownController@indexPrintLabelA6');
});

//WELDING
Route::group(['nav' => 'S54', 'middleware' => 'permission'], function () {
    Route::get('index/kd_welding/{id}', 'KnockDownController@indexKD');
    Route::post('fetch/kd_print_welding_body', 'KnockDownController@printLabelWeldingBody');
    Route::get('index/print_label_welding/{id}', 'KnockDownController@indexPrintLabelA6');

    Route::post('fetch/kd_print_welding_keypost', 'KnockDownController@printLabelNewSingle');
});

//SUBASSY
Route::group(['nav' => 'S25', 'middleware' => 'permission'], function () {
    Route::get('index/kd_subassy/{id}', 'KnockDownController@indexKD');

    //Based on production sch item>1
    Route::post('fetch/kd_print_subassy', 'KnockDownController@printLabel');
    //Based on shipment sch
    Route::post('fetch/kd_print_subassy_new', 'KnockDownController@printLabelNew');
    //Based on production sch item=1
    Route::post('fetch/kd_print_subassy_new_single', 'KnockDownController@printLabelSubassyNew');
    // Route::post('fetch/kd_print_subassy_new_single', 'KnockDownController@printLabelNewSingle');

    Route::get('index/print_label_subassy/{id}', 'KnockDownController@indexPrintLabelSubassy');

    Route::post('scan/kd_closure', 'KnockDownController@scanKDClosure');

});

//CLOSURE
// Route::group(['nav' => 'S27', 'middleware' => 'permission'], function(){
//     Route::get('index/kd_closure', 'KnockDownController@indexKDClosure');
// });

Route::group(['nav' => 'S29', 'middleware' => 'permission'], function () {
    Route::get('index/kd_delivery', 'KnockDownController@indexKdDelivery');
    Route::post('scan/kd_delivery', 'KnockDownController@scanKdDelivery');
    Route::get('index/kd_stuffing', 'KnockDownController@indexKdStuffing');
    Route::post('scan/kd_stuffing', 'KnockDownController@scanKdStuffing');
    Route::post('delete/kdo_stuffing', 'KnockDownController@deleteKdStuffing');
    Route::post('delete/kdo_delivery', 'KnockDownController@deleteKdDelivery');
    Route::post('delete/kdo', 'KnockDownController@deleteKd');
    Route::post('delete/kdo_case', 'KnockDownController@deleteKdCasePnPart');
    Route::post('delete/kdo_detail', 'KnockDownController@deleteKdDetail');
    Route::post('delete/kdo_mpro', 'KnockDownController@deleteKdMpro');

    Route::get('index/kd_splitter/{id}', 'KnockDownController@indexKdSplitter');
    Route::get('scan/kd_splitter', 'KnockDownController@scanKdSplitter');
    Route::post('fetch/kd_splitter', 'KnockDownController@fetchKdSplitter');
    Route::get('fetch/kdo_splitter_detail', 'KnockDownController@fetchKDOSplitterDetail');
    Route::get('index/print_label_split/{id}', 'KnockDownController@indexPrintLabelSplit');

    Route::get('index/reprint_wh/{id}', 'KnockDownController@indexPrintLabelSubassy');
});

Route::get('upload_kd', 'KnockDownController@indexUploadSch');

Route::get('fetch/kd/{id}', 'KnockDownController@fetchKd');
Route::get('fetch/kd_new/{id}', 'KnockDownController@fetchKdNew');
Route::get('fetch/kd_pack/{id}', 'KnockDownController@fetchKdPack');
Route::get('fetch/check_kd', 'KnockDownController@fetchCheckKd');
Route::get('fetch/check_export_kd', 'KnockDownController@fetchCheckExportKd');
Route::get('fetch/kd_delivery_closure', 'KnockDownController@fetchKdDeliveryClosure');
// Route::get('fetch/kd_print', 'KnockDownController@fetchKdPrint');
Route::get('fetch/kd_detail', 'KnockDownController@fetchKdDetail');
Route::get('index/kd_daily_production_result', 'KnockDownController@indexKdDailyProductionResult');
Route::get('fetch/kd_daily_production_result', 'KnockDownController@fetchKdDailyProductionResult');
Route::get('index/kd_production_schedule_data', 'KnockDownController@indexKdProductionScheduleData');
Route::get('fetch/kd_production_schedule_data', 'KnockDownController@fetchKdProductionScheduleData');
Route::get('index/kd_stock', 'KnockDownController@indexKdStock');
Route::get('fetch/kd_stock', 'KnockDownController@fetchKdStock');
Route::get('fetch/kd_stock_detail', 'KnockDownController@fetchKdStockDetail');
Route::get('index/kd_shipment_progress', 'KnockDownController@indexKdShipmentProgress');
Route::get('fetch/kd_shipment_progress', 'KnockDownController@fetchKdShipmentProgress');
Route::get('fetch/kd_shipment_progress_detail', 'KnockDownController@fetchKdShipmentProgressDetail');
Route::get('fetch/kdo_closure', 'KnockDownController@fetchKDOClosure');
Route::get('fetch/kdo', 'KnockDownController@fetchKDO');
Route::get('fetch/kdo_detail', 'KnockDownController@fetchKDODetail');
Route::get('fetch/kdo_detail_case', 'KnockDownController@fetchKDODetailCase');
Route::get('fetch/kd_reprint_kdo', 'KnockDownController@reprintKDO');
Route::get('fetch/container_resume', 'KnockDownController@fetchContainerResume');

Route::get('fetch/kdo_impraboard', 'KnockDownController@fetchKDOImpra');
Route::post('fetch/print/kdo_impraboard', 'KnockDownController@fetchPrintKDOImpra');

Route::get('index/kd_traceability', 'KnockDownController@indexKdTraceability');
Route::get('fetch/kd_traceability', 'KnockDownController@fetchKdTraceability');

//END KD

Route::group(['nav' => 'S30', 'middleware' => 'permission'], function () {
    Route::get('index/workshop/list_wjo', 'WorkshopController@indexListWJO');
    Route::post('update/workshop/wjo', 'WorkshopController@updateWJO');
    Route::post('edit/workshop/wjo', 'WorkshopController@editLeaderWJO');
    Route::post('check/workshop/wjo_rfid', 'WorkshopController@checkTag');
    Route::post('reject/workshop/wjo', 'WorkshopController@rejectWJO');
    Route::post('close/workshop/wjo', 'WorkshopController@closeWJO');
    Route::get('index/workshop/drawing', 'WorkshopController@indexDrawing');
    Route::post('create/workshop/drawing', 'WorkshopController@createDrawing');
    Route::post('edit/workshop/drawing', 'WorkshopController@editDrawing');
    Route::get('index/workshop/job_history', 'WorkshopController@indexJobHistory');
    Route::get('fetch/workshop/job_history', 'WorkshopController@fetchJobHistory');
    Route::get('excel/workshop/job_history', 'WorkshopController@exportJobHistory');
    Route::get('index/workshop/receipt', 'WorkshopController@indexWJOReceipt');
    Route::get('fetch/workshop/receipt', 'WorkshopController@fetchFinishedWJO');
    Route::get('fetch/workshop/receipt/after', 'WorkshopController@fetchReceivedWJO');
    Route::get('fetch/workshop/picked', 'WorkshopController@fetchPickedWJO');
    Route::get('scan/workshop/receipt', 'WorkshopController@scanWJOReceipt');

    Route::get('index/workshop/flow_master', 'WorkshopController@indexMasterFlow');
    Route::get('fetch/workshop/flow', 'WorkshopController@fetchMasterFlow');
    Route::post('post/workshop/flow', 'WorkshopController@postMasterFlow');
    Route::get('fetch/workshop/flow/by_flow', 'WorkshopController@fetchFlowByName');
    Route::get('delete/workshop/flow', 'WorkshopController@deleteMasterFlow');

    Route::get('index/workshop/pic_master', 'WorkshopController@indexMasterPic');
    Route::get('fetch/workshop/pic', 'WorkshopController@fetchMasterPic');
    Route::post('post/workshop/pic', 'WorkshopController@postMasterPic');
    Route::get('fetch/workshop/pic/by_proc', 'WorkshopController@fetchPicByProc');
    Route::get('delete/workshop/pic', 'WorkshopController@deleteMasterPic');

    Route::get('index/workshop/jig_master', 'WorkshopController@indexMasterJig');
    Route::post('post/workshop/jig', 'WorkshopController@postMasterJig');
    Route::post('delete/workshop/jig', 'WorkshopController@deleteMasterJig');
    Route::get('fetch/workshop/jig/by_id', 'WorkshopController@fetchMasterJigbyId');

    Route::get('ketercapaian/wjo', 'WorkshopController@IndexKetercapaianWjo');
    Route::get('fetch/ketercapaian/wjo', 'WorkshopController@FetchKetercapaianWjo');
});

Route::group(['nav' => 'S31', 'middleware' => 'permission'], function () {
    Route::get('index/workshop/wjo', 'WorkshopController@indexWJO');
});

Route::get('index/workshop', 'WorkshopController@indexWorkshop');

Route::get('index/workshop/workload', 'WorkshopController@indexWorkload');
Route::get('fetch/workshop/workload', 'WorkshopController@fetchWorkload');
Route::get('fetch/workshop/workload_operator_detail', 'WorkshopController@fetchWorkloadOperatorDetail');

Route::get('index/workshop/workload/machine', 'WorkshopController@indexMachineWorkload');
Route::get('fetch/workshop/workload/machine', 'WorkshopController@fetchWorkloadMachine');

Route::get('index/workshop/operatorload', 'WorkshopController@indexOperatorload');
Route::get('fetch/workshop/operatorload', 'WorkshopController@fetchOperatorload');

Route::get('fetch/workshop/machine', 'WorkshopController@scanMachine');
Route::get('index/workshop/create_wjo', 'WorkshopController@indexCreateWJO');
Route::post('create/workshop/wjo', 'WorkshopController@createWJO');
Route::get('cancel/workshop/wjo', 'WorkshopController@cancelWJO');
Route::get('index/workshop/edit_wjo', 'WorkshopController@fetch_item_edit');
Route::post('index/workshop/edit_wjo', 'WorkshopController@editWJO');
Route::get('update/workshop/approve_urgent/{id}', 'WorkshopNotificationController@approveUrgent');
Route::get('update/workshop/reject_urgent/{id}', 'WorkshopNotificationController@rejectUrgent');
Route::get('fetch/workshop/list_wjo', 'WorkshopController@fetchListWJO');
Route::get('fetch/workshop/assign_form', 'WorkshopController@fetchAssignForm');
Route::get('export/workshop/list_wjo', 'WorkshopController@exportListWJO');
Route::get('download/workshop/{id}', 'WorkshopController@downloadAttachment');
Route::get('scan/workshop/operator/rfid', 'WorkshopController@scanOperator');
Route::get('scan/workshop/tag/rfid', 'WorkshopController@scanTag');
Route::get('scan/workshop/leader/rfid', 'WorkshopController@scanLeader');
Route::post('create/workshop/tag/process_log', 'WorkshopController@createProcessLog');
Route::get('close/workshop/check_rfid', 'WorkshopController@checkCloseTag');
Route::get('fetch/workshop/drawing', 'WorkshopController@fetchDrawing');
Route::get('fetch/workshop/edit_drawing', 'WorkshopController@fetchEditDrawing');
Route::get('index/workshop/wjo_monitoring', 'WorkshopController@indexWJOMonitoring');
Route::get('index/workshop/wjo_monitoring/{status}', 'WorkshopController@indexWJOMonitoring2');
Route::get('fetch/workshop/wjo_monitoring', 'WorkshopController@fetchWJOMonitoring');
Route::get('index/workshop/productivity', 'WorkshopController@indexProductivity');
Route::get('fetch/workshop/productivity', 'WorkshopController@fetchProductivity');
Route::get('fetch/workshop/operator_detail', 'WorkshopController@fetchOperatorDetail');
Route::get('fetch/workshop/machine_detail', 'WorkshopController@fetchmachineDetail');
Route::get('fetch/workshop/process_detail', 'WorkshopController@fetchProcessDetail');
Route::get('fetch/workshop/drawingMaterial', 'WorkshopController@fetchDrawingMaterial');
Route::get('index/workshop/perolehan', 'WorkshopController@indexWJOPerolehan');
Route::get('fetch/workshop/perolehan', 'WorkshopController@fetchWJOPerolehan');
Route::get('detail/workshop/perolehan', 'WorkshopController@detailWJOPerolehan');
Route::get('resend/workshop/email', 'WorkshopController@resendWJO');

Route::get('fetch/workshop/operator/load_hour', 'WorkshopController@fetchOperatorloadMinute');
Route::get('fetch/workshop/operator/skill', 'WorkshopController@fetchOperatorSkill');

//WJO JIG
Route::get('index/workshop/create_jig_wjo', 'WorkshopController@indexWJOJig');
Route::get('index/workshop/monitoring/jig', 'WorkshopController@indexMonitoringJig');
Route::get('fetch/workshop/monitoring/jig', 'WorkshopController@fetchMonitoringJig');
Route::get('fetch/workshop/jig', 'WorkshopController@fetchMasterJig');
Route::post('post/workshop/jig/order', 'WorkshopController@postOrderJig');

// CEK MOLDING VENDOR
Route::get('index/workshop/check_molding_vendor', 'workshopController@indexCheckMolding');
Route::get('fetch/workshop/check_molding_vendor/monitoring', 'workshopController@fetchCheckMoldingMonitoring');
Route::get('index/workshop/check_molding_vendor/create', 'workshopController@indexCreateCheckMolding');
Route::post('post/workshop/check_molding_vendor', 'workshopController@postCheckMolding');
Route::get('fetch/workshop/check_molding_vendor/record', 'workshopController@fetchCheckMolding');
Route::post('post/workshop/check_molding_vendor/temuan', 'WorkshopController@postFindingMolding');
Route::get('fetch/workshop/check_molding_vendor/temuan', 'WorkshopController@fetchFindingMolding');
Route::get('fetch/workshop/check_molding_vendor/penanganan/log', 'WorkshopController@fetchHandlingLog');
Route::post('post/workshop/check_molding_vendor/penanganan', 'WorkshopController@postHandling');

Route::get('index/middle/op_analysis', 'MiddleProcessController@indexOpAnalysis');
Route::get('fetch/middle/op_analysis', 'MiddleProcessController@fetchOpAnalysis');
Route::get('fetch/middle/op_analysis_detail', 'MiddleProcessController@fetchOpAnalysisDetail');

Route::get('fetch/middle/kensa', 'MiddleProcessController@fetchMiddleKensa');
Route::get('scan/middle/buffing/kensa/material', 'MiddleProcessController@fetchBuffing');
Route::get('scan/middle/operator/rfid', 'MiddleProcessController@scanMiddleOperatorKensa');
Route::get('index/process_middle_sx', 'MiddleProcessController@indexProcessMiddleSX');
Route::get('index/middle/request/{id}', 'MiddleProcessController@indexRequest');
Route::get('index/middle/request/display/{id}', 'MiddleProcessController@indexRequestDisplay');
Route::get('fetch/middle/request', 'MiddleProcessController@fetchRequest');

Route::get('index/process_middle_acc', 'MiddleProcessController@indexProcessMiddleACC');

//FROM SOLDERING
Route::get('index/middle/requested', 'MiddleProcessController@indexRequestSolder');
//CLARINET
Route::get('index/process_middle_cl', 'MiddleProcessController@indexProcessMiddleCL');
Route::get('scan/middle/request', 'MiddleProcessController@scanRequestTag');
//FLUTE
Route::get('index/process_middle_fl', 'MiddleProcessController@indexProcessMiddleFL');
Route::get('index/middle/request_fl', 'MiddleProcessController@indexRequestFL');
Route::get('index/process_middle_kensa/{id}', 'MiddleProcessController@indexProcessMiddleKensa');
Route::get('index/process_middle_barrel/{id}', 'MiddleProcessController@indexProcessMiddleBarrel');
Route::get('fetch/middle/barrel', 'MiddleProcessController@fetchMiddleBarrel');
Route::get('fetch/middle/barrel_machine', 'MiddleProcessController@fetchMiddleBarrelMachine');
Route::get('index/middle/barrel_machine', 'MiddleProcessController@indexProcessBarrelMachine');
Route::get('index/middle/barrel_board/{id}', 'MiddleProcessController@indexProcessBarrelBoard');
Route::get('index/middle/buffing_board/{id}', 'MiddleProcessController@indexBuffingBoard');
Route::get('index/middle/buffing_board_reverse/{id}', 'MiddleProcessController@indexBuffingBoardReverse');
Route::get('fetch/middle/get_barrel_machine', 'MiddleProcessController@fetchProcessBarrelMachine');
Route::get('fetch/middle/get_barrel', 'MiddleProcessController@fetchProcessBarrel');
Route::get('fetch/middle/barrel_board', 'MiddleProcessController@fetchMiddleBarrelBoard');
Route::get('fetch/middle/barrel_machine_status', 'MiddleProcessController@fetchMachine');
Route::get('index/process_middle_return/{id}', 'MiddleProcessController@indexProcessMiddleReturn');
Route::get('index/process_middle_return/body/{id}', 'MiddleProcessController@indexProcessMiddleReturnBody');
Route::get('fetch/middle_return/barrel_return', 'MiddleProcessController@fetchProcessMiddleReturn');
Route::get('fetch/middle_return/surface_return', 'MiddleProcessController@fetchProcessMiddleReturnBody');
Route::get('fetch/middle/barrel_reprint', 'MiddleProcessController@fetchMiddleBarrelReprint');
Route::get('fetch/middle/barrel_result', 'MiddleProcessController@fetchBarrelBoardDetails');
Route::get('index/report_middle/{id}', 'MiddleProcessController@indexReportMiddle');
Route::get('fetch/middle/buffing_board', 'MiddleProcessController@fetchBuffingBoard');
Route::get('fetch/middle/buffing_board_cl', 'MiddleProcessController@fetchBuffingBoardCl');
Route::get('fetch/middle/buffing_board_cl_detail', 'MiddleProcessController@fetchBuffingBoardClDetail');
Route::get('fetch/middle/buffing_board_reverse', 'MiddleProcessController@fetchBuffingReverse');
Route::get('index/middle/barrel_log', 'MiddleProcessController@indexBarrelLog');
Route::get('fetch/middle/barrel_log', 'MiddleProcessController@fetchBarrelLog');
Route::get('index/middle/report_ng', 'MiddleProcessController@indexReportNG');
Route::get('index/middle/report_production_result', 'MiddleProcessController@indexReportProductionResult');
Route::get('fetch/middle/report_ng', 'MiddleProcessController@fetchReportNG');
Route::get('fetch/middle/report_production_result', 'MiddleProcessController@fetchReportProductionResult');
Route::get('index/middle/display_production_result', 'MiddleProcessController@indexDisplayProductionResult');
Route::get('fetch/middle/display_production_result', 'MiddleProcessController@fetchDisplayProductionResult');
Route::get('index/process_buffing_kensa/{id}', 'MiddleProcessController@indexProcessBuffingKensa');
Route::post('input/middle/buffing/kensa', 'MiddleProcessController@inputBuffingKensa');
Route::get('index/middle/display_picking', 'MiddleProcessController@indexDisplayPicking');
Route::get('fetch/middle/display_picking', 'MiddleProcessController@fetchDisplayPicking');
Route::get('index/middle/display_monitoring', 'MiddleProcessController@indexDisplayMonitoring');
Route::get('fetch/middle/display_monitoring', 'MiddleProcessController@fetchDisplayMonitoring');
Route::get('fetch/middle/detail_monitoring', 'MiddleProcessController@fetchDetailStockMonitoring');
Route::get('index/middle/detail_monitoring', 'MiddleProcessController@fetchDetailStockMonitoring');
Route::get('index/process_buffing_inout', 'MiddleProcessController@indexBuffingInOut');
Route::get('fetch/process_buffing_inout', 'MiddleProcessController@fetchScanBuffingInOut');
Route::get('fetch/process_buffing_store', 'MiddleProcessController@fetchBuffingStore');

Route::get('input/middle/body_return', 'MiddleProcessController@inputBodyReturn');

// Report Middle Global
Route::get('index/middle/display_kensa_time', 'MiddleProcessController@indexDisplayKensaTime');
Route::get('fetch/middle/display_kensa_time', 'MiddleProcessController@fetchDisplayKensaTime');

// Report Middle PLT Saxophone
Route::get('index/middle/report_plt_ng/{id}', 'MiddleProcessController@indexReportPltNg');
Route::get('fetch/middle/plt_ng_rate_monthly/{id}', 'MiddleProcessController@fetchPltNgRateMonthly');
Route::get('fetch/middle/plt_ng_rate_weekly/{id}', 'MiddleProcessController@fetchPltNgRateWeekly');
Route::get('fetch/middle/plt_ng/{id}', 'MiddleProcessController@fetchPltNg');
Route::get('fetch/middle/plt_ng_rate/{id}', 'MiddleProcessController@fetchPltNgRate');

// Report Middle Plating Clarinet
Route::get('index/middle/report_plt_ng_clarinet', 'MiddleProcessController@indexReportPltNgClarinet');
Route::get('fetch/middle/plt_ng_clarinet', 'MiddleProcessController@fetchPltNgClarinet');
Route::get('fetch/middle/plt_ng_rate_clarinet', 'MiddleProcessController@fetchPltNgRateClarinet');

// Report Middle Plating Flute
Route::get('index/middle/report_plt_ng_flute', 'MiddleProcessController@indexReportPltNgFlute');
Route::get('fetch/middle/plt_ng_flute', 'MiddleProcessController@fetchPltNgFlute');
Route::get('fetch/middle/plt_ng_rate_flute', 'MiddleProcessController@fetchPltNgRateFlute');

// Report Middle LCQ
Route::get('index/middle/report_lcq_ng', 'MiddleProcessController@indexReportLcqNg');
Route::get('fetch/middle/lcq_ng_rate_monthly', 'MiddleProcessController@fetchLcqNgRateMonthly');
Route::get('fetch/middle/lcq_ng_rate_weekly', 'MiddleProcessController@fetchLcqNgRateWeekly');
Route::get('fetch/middle/lcq_ng', 'MiddleProcessController@fetchLcqNg');
Route::get('fetch/middle/lcq_ng_rate', 'MiddleProcessController@fetchLcqNgRate');
Route::get('index/middle/report_hourly_lcq', 'MiddleProcessController@indexReportHourlyLcq');
Route::get('fetch/middle/report_hourly_lcq', 'MiddleProcessController@fetchReportHourlyLcq');
// Report Middle Buffing

Route::get('index/middle/report_buffing_ng', 'MiddleProcessController@indexReportBuffingNg');
Route::get('fetch/middle/bff_ng_rate_monthly', 'MiddleProcessController@fetchBuffingNgRateMonthly');
Route::get('fetch/middle/bff_ng_rate_weekly', 'MiddleProcessController@fetchBuffingNgRateWeekly');
Route::get('fetch/middle/bff_ng_monthly', 'MiddleProcessController@fetchBuffingNgMonthly');
Route::get('fetch/middle/bff_ng_key_monthly', 'MiddleProcessController@fetchBuffingNgKeyMonthly');
Route::get('fetch/middle/bff_ng_rate_daily', 'MiddleProcessController@fetchBuffingNgDaily');

Route::get('index/middle/report_buffing_ng/{id}', 'MiddleProcessController@indexReportBuffingNgAll');
Route::get('fetch/middle/bff_ng_rate_monthly/{id}', 'MiddleProcessController@fetchBuffingNgRateMonthlyAll');
Route::get('fetch/middle/bff_ng_rate_weekly/{id}', 'MiddleProcessController@fetchBuffingNgRateWeeklyAll');
Route::get('fetch/middle/bff_ng_monthly/{id}', 'MiddleProcessController@fetchBuffingNgMonthlyAll');
Route::get('fetch/middle/bff_ng_key_monthly/{id}', 'MiddleProcessController@fetchBuffingNgKeyMonthlyAll');
Route::get('fetch/middle/bff_ng_rate_daily/{id}', 'MiddleProcessController@fetchBuffingNgDailyAll');

Route::get('fetch/middle/bff_op_eff_monthly', 'MiddleProcessController@fetchBuffingOpEffMonthly');
Route::get('fetch/middle/bff_op_ng_monthly/{id}', 'MiddleProcessController@fetchBuffingOpNgMonthly');
Route::get('fetch/middle/bff_op_ng_monthly_detail', 'MiddleProcessController@fetchBuffingOpNgMonthlyDetail');
Route::get('fetch/middle/bff_op_work_monthly/{id}', 'MiddleProcessController@fetchBuffingOpWorkMonthly');
Route::get('fetch/middle/bff_op_work_monthly_detail', 'MiddleProcessController@fetchBuffingOpWorkMonthlyDetail');
Route::get('index/middle/report_buffing_operator_time', 'MiddleProcessController@indexReportOpTime');
Route::get('fetch/middle/report_buffing_operator_time', 'MiddleProcessController@fetchReportOpTime');
Route::get('fetch/middle/report_buffing_operator_time_qty', 'MiddleProcessController@fetchReportOpTimeQty');
Route::get('index/middle/report_buffing_canceled_log', 'MiddleProcessController@indexReportBuffingCancelled');
Route::get('fetch/middle/report_buffing_canceled_log', 'MiddleProcessController@fetchReportBuffingCancelled');
Route::get('index/middle/report_buffing_traing_ng_operator', 'MiddleProcessController@indexReportTrainingOpNg');
Route::get('fetch/middle/report_buffing_traing_ng_operator', 'MiddleProcessController@fetchReportTrainingOpNg');
Route::get('index/middle/report_buffing_traing_eff_operator', 'MiddleProcessController@indexReportTrainingOpEFf');
Route::get('fetch/middle/report_buffing_traing_eff_operator', 'MiddleProcessController@fetchReportTrainingOpEff');

//Display Buffing
Route::get('fetch/middle/buffing_hourly_ng', 'MiddleProcessController@fetchBuffingHourlyNg');
Route::get('index/middle/buffing_ng', 'MiddleProcessController@indexBuffingNg');
Route::get('fetch/middle/buffing_ng', 'MiddleProcessController@fetchBuffingNg');
Route::get('fetch/middle/buffing_ng_key', 'MiddleProcessController@fetchBuffingNgKey');
Route::get('index/middle/buffing_op_ng', 'MiddleProcessController@indexBuffingOpNg');
Route::get('fetch/middle/buffing_op_ng', 'MiddleProcessController@fetchBuffingOpNg');
Route::get('fetch/middle/buffing_op_ng_target', 'MiddleProcessController@fetchBuffingOpNgTarget');
Route::get('fetch/middle/buffing_detail_op_ng', 'MiddleProcessController@fetchBuffingDetailOpNg');
Route::get('index/middle/buffing_trend_op_eff', 'MiddleProcessController@indexTrendBuffingOpEff');
Route::get('index/middle/buffing_op_eff', 'MiddleProcessController@indexBuffingOpEff');
Route::get('fetch/middle/buffing_op_eff', 'MiddleProcessController@fetchBuffingOpEff');
Route::get('fetch/middle/buffing_op_eff_detail', 'MiddleProcessController@fetchBuffingOpEffDetail');
Route::get('fetch/middle/buffing_daily_op_eff', 'MiddleProcessController@fetchBuffingDailyOpEff');
Route::get('fetch/middle/buffing_op_working', 'MiddleProcessController@fetchBuffingOpWorking');
Route::get('fetch/middle/buffing_op_result', 'MiddleProcessController@fetchBuffingOpResult');
Route::get('fetch/middle/buffing_op_eff_target', 'MiddleProcessController@fetchBuffingOpEffTarget');
Route::get('index/middle/buffing_op_ranking', 'MiddleProcessController@indexBuffingOpRanking');
Route::get('index/middle/buffing_daily_ng_rate', 'MiddleProcessController@indexBuffingNgRate');
Route::get('fetch/middle/buffing_daily_ng_rate', 'MiddleProcessController@fetchBuffingNgRate');
Route::get('index/middle/buffing_daily_op_ng_rate', 'MiddleProcessController@indexBuffingOpNgRate');
Route::get('fetch/middle/buffing_daily_op_ng_rate', 'MiddleProcessController@fetchBuffingOpNgRate');
Route::get('index/middle/buffing_group_achievement', 'MiddleProcessController@indexBuffingGroupAchievement');
Route::get('fetch/middle/buffing_group_achievement', 'MiddleProcessController@fetchBuffingGroupAchievement');
Route::get('fetch/middle/buffing_accumulated_achievement', 'MiddleProcessController@fetchAccumulatedAchievement');
Route::get('fetch/middle/buffing_daily_group_achievement', 'MiddleProcessController@fetchDailyGroupAchievement');
Route::get('index/middle/buffing_group_balance', 'MiddleProcessController@indexBuffingGroupBalance');
Route::get('fetch/middle/buffing_group_balance', 'MiddleProcessController@fetchBuffingGroupBalance');
Route::get('index/middle/buffing_ic_atokotei', 'MiddleProcessController@indexBuffingIcAtokotei');
Route::get('fetch/middle/buffing_ic_atokotei', 'MiddleProcessController@fetchBuffingIcAtokotei');
Route::get('index/middle/buffing_work_order/{id}', 'MiddleProcessController@indexBuffingWorkOrder');
Route::get('fetch/middle/buffing_target', 'MiddleProcessController@fetchTarget');
Route::get('index/middle/buffing_operator_assesment', 'MiddleProcessController@indexOpAssesment');
Route::get('index/middle/buffing_resume_konseling', 'MiddleProcessController@indexResumeKonseling');
Route::get('fetch/middle/buffing_resume_konseling', 'MiddleProcessController@fetchResumeKonseling');

Route::get('index/middle/ic_atokotei_subassy', 'MiddleProcessController@indexIcAtokoteiSubassy');
Route::get('fetch/middle/ic_atokotei_subassy', 'MiddleProcessController@fetchIcAtokoteiSubassy');
Route::get('index/middle/ic_atokotei_subassy_op', 'MiddleProcessController@indexIcAtokoteiSubassyOp');
Route::get('fetch/middle/ic_atokotei_subassy_op', 'MiddleProcessController@fetchIcAtokoteiSubassyOp');

//MIZUSUMASHI
Route::get('index/middle/muzusumashi', 'MiddleProcessController@indexMizusumashi');
Route::get('fetch/middle/muzusumashi', 'MiddleProcessController@fetchMisuzumashi');

//WELDING
Route::get('index/process_welding_sx', 'WeldingProcessController@indexWeldingSX');
Route::get('index/process_welding_fl', 'WeldingProcessController@indexWeldingFL');
Route::get('index/process_welding_cl', 'WeldingProcessController@indexWeldingCL');

Route::group(['nav' => 'S20', 'middleware' => 'permission'], function () {
    Route::get('index/qnaHR', 'EmployeeController@indexHRQA');
    Route::get('fetch/hr/hrqa', 'EmployeeController@fetchMasterQuestion');
    Route::get('fetch/hr/hrqa/detail', 'EmployeeController@fetchDetailQuestion');

});
Route::get('index/qnaHR/resume', 'EmployeeController@indexHRQAResume');
Route::get('fetch/qnaHR/resume', 'EmployeeController@fetchHRQAResume');

//START CLINIC
Route::group(['nav' => 'S23', 'middleware' => 'permission'], function () {
    Route::get('index/diagnose', 'ClinicController@indexDiagnose');
    Route::get('fetch/diagnose', 'ClinicController@fetchDiagnose');
    Route::post('delete/diagnose', 'ClinicController@deleteVisitor');
    Route::post('input/diagnose', 'ClinicController@inputDiagnose');
    Route::get('index/clinic_visit_log', 'ClinicController@indexVisitLog');
    Route::get('fetch/clinic_visit_log', 'ClinicController@fetchVisitLog');
    Route::get('fetch/clinic_visit_log_excel', 'ClinicController@fetchVisitLogExcel');
    Route::get('fetch/clinic_visit_edit_detail', 'ClinicController@fetchVisitEdit');
    Route::post('edit/diagnose', 'ClinicController@editDiagnose');
    Route::get('fetch/display/clinic_disease_detail', 'ClinicController@fetchDiseaseDetail');
    Route::get('fetch/clinic_visit_detail', 'ClinicController@fetchClinicVisitDetail');

    Route::get('index/mask_visit_log', 'ClinicController@indexMaskLog');
    Route::get('fetch/mask_visit_log', 'ClinicController@fetchMaskLog');
    Route::get('fetch/clinic_masker_detail', 'ClinicController@fetchClinicMaskerDetail');

    Route::get('index/medicines', 'ClinicController@indexMedicines');
    Route::get('fetch/medicines', 'ClinicController@fetchMedicines');
    Route::post('edit/medicine_stock', 'ClinicController@editMedicineStock');

    Route::post('scan/clinic/register', 'ClinicController@scanRegister');

});
// Control Medicines new
Route::get('index/control/medicines', 'ClinicController@indexKontrolObat');
Route::get('test/print', 'ClinicController@printLabel');
Route::get('index/stocktaking/medicine/clinic', 'ClinicController@indexCountStocktaking');
Route::get('fetch/stocktaking/medicine/clinic', 'ClinicController@fetchCountStoctakingObat');

Route::get('index/display/clinic_monitoring', 'ClinicController@indexClinicMonitoring');
Route::get('index/display/clinic_visit', 'ClinicController@indexClinicVisit');
Route::get('index/display/clinic_disease', 'ClinicController@indexClinicDisease');
Route::get('fetch/display_patient', 'ClinicController@fetchPatient');
Route::get('fetch/daily_clinic_visit', 'ClinicController@fetchDailyClinicVisit');
Route::get('fetch/clinic_visit', 'ClinicController@fetchClinicVisit');
Route::get('fetch/display/clinic_disease', 'ClinicController@fetchDisease');
Route::get('fetch/clinic_masker', 'ClinicController@fetchClinicMasker');

//END CLINIC

//INITIAL

Route::get('index/initial/{id}', 'InitialProcessController@index');
Route::get('index/initial/stock_monitoring/{id}', 'InitialProcessController@indexStockMonitoring');
Route::get('index/initial/table_stock_monitoring/{id}', 'InitialProcessController@indexTableStockMonitoring');
Route::get('index/initial/stock_trend/{id}', 'InitialProcessController@indexStockTrend');
Route::get('fetch/initial/stock_monitoring', 'InitialProcessController@fetchStockMonitoring');
Route::get('fetch/initial/stock_trend', 'InitialProcessController@fetchStockTrend');
Route::get('fetch/initial/stock_monitoring_detail', 'InitialProcessController@fetchStockMonitoringDetail');
Route::get('fetch/initial/stock_monitoring_table', 'InitialProcessController@fetchStockTableMonitoring');
Route::get('fetch/initial/stock_trend_detail', 'InitialProcessController@fetchStockTrendDetail');

//KANBAN KPP
Route::get('index/material_process/material', 'InitialProcessController@indexMaterial');
Route::get('fetch/material_process/material', 'InitialProcessController@fetchMaterial');
Route::post('create/new/material', 'InitialProcessController@CreateNewMaterial');
Route::post('delete/material/material', 'InitialProcessController@DeleteMaterialKpp');

Route::get('index/material_process/operator', 'InitialProcessController@indexOperator');
Route::get('fetch/material_process/operator', 'InitialProcessController@fetchOperator');
Route::post('create/new/operator', 'InitialProcessController@CreateNewOperator');
Route::post('delete/operator', 'InitialProcessController@DeleteOperatorKpp');

Route::get('index/material_process/kanban', 'InitialProcessController@indexKanban');
Route::get('fetch/material_process/kanban', 'InitialProcessController@fetchKanban');
Route::post('create/new/operator', 'InitialProcessController@CreateNewOperator');

Route::get('index/material_process/kanban_flow', 'InitialProcessController@indexKanbanFlow');
Route::get('fetch/material_process/kanban_flow', 'InitialProcessController@fetchKanbanFlow');
Route::post('create/new/kanban', 'InitialProcessController@CreateNewKanban');
Route::post('update/kanban', 'InitialProcessController@UpdateKanban');

Route::get('index/material_process/kensa/{id}', 'InitialProcessController@indexMaterialProcessKensa');
Route::get('scan/material_process/kensa', 'InitialProcessController@scanMaterialProcessKensa');
Route::post('input/material_process/kensa', 'InitialProcessController@inputMaterialProcessKensa');
Route::get('fetch/material_process/kensa_result', 'InitialProcessController@fetchKensaResult');

Route::get('index/material_process/ng_rate', 'InitialProcessController@indexNgRate');
Route::get('fetch/material_process/ng_rate', 'InitialProcessController@fetchNgRate');
Route::get('fetch/material_process/ng_rate/detail', 'InitialProcessController@fetchNgRateDetail');

Route::get('index/material_process/op_ng/{loc}', 'WeldingProcessController@indexOpRate');
Route::get('fetch/material_process/op_ng', 'WeldingProcessController@fetchOpRate');
Route::get('fetch/material_process/op_ng_detail', 'WeldingProcessController@fetchOpRateDetail');

Route::get('index/kpp_board/{loc}/{num}', 'InitialProcessController@indexKppBoard');
Route::get('fetch/kpp_board', 'InitialProcessController@fetchKppBoard');
Route::get('index/kpp_report/{id}', 'InitialProcessController@ReportKpp');

Route::get('index/tpro/resume_kanban', 'InitialProcessController@indexResumeKanban');
Route::get('fetch/tpro/resume_kanban', 'InitialProcessController@fetchResumeKanban');

Route::get('index/tpro/check_kanban', 'InitialProcessController@indexCheckKanban');
Route::get('fetch/tpro/resume_check', 'InitialProcessController@fetchCheckKanban');

//KANBAN KONTROL
Route::get('fetch/tpro/scan_kanban', 'InitialProcessController@fetchScanKanban');
Route::get('fetch/tpro/status_kanban', 'InitialProcessController@fetchStatusKanban');

// Route::get('list/kategori/approval', 'AdagioAutoController@IndexKategori');
// Route::get('fetch/kategori/approval', 'AdagioAutoController@FetchKategori');
// Route::post('delete/kategori/approval', 'AdagioAutoController@DeleteKategori');
// Route::post('add/inject/approval', 'AdagioAutoController@AddListKategori');
// Route::post('pindah/posisi/approval', 'AdagioAutoController@MovePosition')

Route::group(['nav' => 'S13', 'middleware' => 'permission'], function () {
    Route::get('index/purchase_order/po_list', 'PurchaseOrderController@indexPoList');
    Route::get('fetch/purchase_order/po_list', 'PurchaseOrderController@fetchPoList');
    // Route::get('export/purchase_order/po_list', 'PurchaseOrderController@exportPoList');
    Route::post('import/purchase_order/po_list', 'PurchaseOrderController@importPoList');
    Route::get('index/purchase_order/po_create', 'PurchaseOrderController@indexPoCreate');
    Route::post('generate/purchase_order/po_create', 'PurchaseOrderController@generatePoCreate');
    Route::post('generate/purchase_order/po_create2', 'PurchaseOrderController@generatePoCreate2');
    Route::post('generate/purchase_order/po_create3', 'PurchaseOrderController@generatePoCreate3');
    Route::get('fetch/purchase_order/download_po', 'PurchaseOrderController@fetchDownloadPo');
    Route::get('download/purchase_order/download_po', 'PurchaseOrderController@downloadPo');
    Route::get('index/purchase_order/po_archive', 'PurchaseOrderController@indexPoArchive');
    Route::get('fetch/purchase_order/po_archive', 'PurchaseOrderController@fetchPoArchive');
    Route::get('index/purchase_order/po_revise', 'PurchaseOrderController@indexPoRevise');
    Route::post('generate/purchase_order/po_revise', 'PurchaseOrderController@generatePoRevise');
    Route::post('generate/purchase_order/po_revise2', 'PurchaseOrderController@generatePoRevise2');
    Route::post('generate/purchase_order/po_revise3', 'PurchaseOrderController@generatePoRevise2');
    Route::get('export/purchase_order/po_list', 'PurchaseOrderController@exportPoList');
    Route::get('export/purchase_order/po_list2', 'PurchaseOrderController@export')->name('export_excel.excel');
});

Route::group(['nav' => 'S14', 'middleware' => 'permission'], function () {
    Route::get('index/overtime/overtime_form', 'OvertimeController@indexOvertimeForm');
    Route::get('create/overtime/overtime_form', 'OvertimeController@createOvertimeForm');
    Route::get('select/overtime/division_hierarchy', 'OvertimeController@selectDivisionHierarchy');
    Route::get('fetch/overtime/employee', 'OvertimeController@fetchEmployee');
    Route::post('fetch/overtime/break', 'OvertimeController@fetchBreak');
    Route::post('save/overtime', 'OvertimeController@saveOvertimeHead');
    Route::post('save/overtime_detail', 'OvertimeController@saveOvertimeDetail');
    Route::post('edit/overtime_detail', 'OvertimeController@editOvertimeDetail');
    Route::get('index/overtime/print/{id}', 'OvertimeController@indexPrint');
    Route::get('print/overtime/group', 'OvertimeController@indexPrintHead');
    Route::post('fetch/report/overtime_graph', 'OvertimeController@graphPrint');
    Route::get('fetch/overtime', 'OvertimeController@fetchOvertime');
    Route::get('fetch/overtime/detail', 'OvertimeController@fetchOvertimeDetail');
    Route::get('fetch/overtime/head', 'OvertimeController@fetchOvertimeHead');
    Route::post('delete/overtime', 'OvertimeController@deleteOvertime');
    Route::get('index/overtime/edit/{id}', 'OvertimeController@fetchOvertimeEdit');

});

Route::group(['nav' => 'S15', 'middleware' => 'permission'], function () {
    Route::get('index/promotion', 'EmployeeController@indexPromotion');
    Route::get('fetch/promotion', 'EmployeeController@fetchPromotion');
    Route::get('change/promotion', 'EmployeeController@changePromotion');
});

Route::group(['nav' => 'S16', 'middleware' => 'permission'], function () {
    Route::get('index/mutation', 'EmployeeController@indexMutation');
    Route::get('fetch/mutation', 'EmployeeController@fetchMutation');
    Route::get('change/mutation', 'EmployeeController@changeMutation');
});

Route::group(['nav' => 'S17', 'middleware' => 'permission'], function () {
    Route::get('index/double', 'OvertimeController@indexOvertimeDouble');
    Route::post('fetch/double', 'OvertimeController@fetchDoubleSPL');
});

//pianica

Route::group(['nav' => 'S18', 'middleware' => 'permission'], function () {
    //---master code op
    Route::get('index/Op_Code', 'Pianica@opcode');
    Route::get('index/FillOpcode', 'Pianica@fillopcode');
    Route::get('edit/Opcode', 'Pianica@editopcode');
    Route::post('update/Opcode', 'Pianica@updateopcode');

    //-----master op
    Route::get('index/Op', 'Pianica@op');
    Route::get('index/FillOp', 'Pianica@fillop');
    Route::get('edit/Op', 'Pianica@editop');
    Route::post('update/Op', 'Pianica@updateop');
    Route::post('add/Op', 'Pianica@addop');
    Route::post('delete/Op', 'Pianica@deleteop');

    //----------bensuki

    Route::get('index/Bensuki', 'Pianica@bensuki');
    Route::post('index/Save', 'Pianica@input');
    Route::post('index/Incoming', 'Pianica@input2');
    Route::get('index/Otokensa', 'Pianica@otokensa');

    //------------pureto
    Route::get('index/Pureto', 'Pianica@pureto');
    Route::get('index/op_Pureto', 'Pianica@op_pureto');
    Route::post('index/SavePureto', 'Pianica@savepureto');
    Route::get('fetch/check_tag', 'Pianica@checkTag');
    Route::post('input/pn/audit_screw', 'Pianica@inputAuditScrew');

    //------------kensa awal
    Route::get('index/KensaAwal', 'Pianica@kensaawal');
    Route::get('index/model', 'Pianica@tag_model');
    Route::post('index/SaveKensaAwal', 'Pianica@savekensaawal');
    Route::get('index/TotalNg', 'Pianica@total_ng');
    Route::get('fetch/opTunning', 'Pianica@opTunning');

    //---------- Assembly
    Route::get('index/Assembly', 'Pianica@assembly');
    Route::post('post/pianica/Save_assembly', 'Pianica@saveAssembly');

    //-----------kensa akhir
    Route::get('index/KensaAkhir', 'Pianica@kensaakhir');
    Route::post('index/SaveKensaAkhir', 'Pianica@savekensaakhir');

    //------------ kakuning visual
    Route::get('index/KakuningVisual', 'Pianica@kakuningvisual');
    Route::post('index/SaveKakuningVisual', 'Pianica@saveKakuningVisual');
    Route::post('post/label/kakuningVisual', 'Pianica@saveLabelKakuning');

    //------------ kakuning visual Case
    Route::get('index/case_pn/KakuningVisual', 'Pianica@indexKakuningCase');
    Route::post('post/case_pn/KakuningVisual', 'Pianica@saveKakuningCase');
    Route::get('fetch/case_pn/total_ng', 'Pianica@fetchCaseNg');
    Route::get('index/case_pn/ng_trend', 'Pianica@indexDisplayNgCase');
    Route::get('fetch/case_pn/ng_trend', 'Pianica@fetchDisplayNgCase');
    Route::get('fetch/case_pn/ng_trend/detail', 'Pianica@fetchDisplayNgCaseDetail');
});

Route::get('index/pn/qa_audit', 'Pianica@indexQaAudit');
Route::get('fetch/pn/qa_audit', 'Pianica@fetchQaAudit');
Route::get('scan/pn/qa_audit', 'Pianica@scanQaAudit');
Route::post('input/pn/qa_audit', 'Pianica@inputQaAudit');
Route::post('input/pn/counceling', 'Pianica@inputPianicaCounceling');
Route::get('print/pn/qa_audit/{type}/{id}', 'Pianica@printQaAudit');
Route::get('approve/pn/qa_audit/{type}/{remark}/{id}', 'Pianica@approveQaAudit');
Route::post('reject/pn/qa_audit/{id}', 'Pianica@rejectQaAudit');
Route::post('reject_chief/pn/qa_audit/{id}', 'Pianica@rejectQaAuditChief');
Route::post('update/pn/counceling', 'Pianica@updatePianicaCounceling');
Route::get('input/pn/training_document/qa/{type}', 'Pianica@inputDocumentTrainingQa');

Route::get('index/pn/display/qa_audit', 'Pianica@indexDisplayQaAudit');
Route::get('fetch/pn/display/qa_audit', 'Pianica@fetchDisplayQaAudit');

Route::get('index/pn/display/kensa_awal', 'Pianica@indexDisplayKensaAwal');
Route::get('fetch/pn/display/kensa_awal', 'Pianica@fetchDisplayKensaAwal');

Route::get('index/Pianica', 'Pianica@index');
Route::get('index/Op_Code', 'Pianica@opcode');
Route::get('index/pianica/monitoring/pn_part', 'Pianica@indexDiplayPnPart');
Route::get('fetch/pianica/monitoring/pn_part', 'Pianica@fetchDiplayPnPart');
Route::post('input/pianica/counceling/pn_part', 'Pianica@inputPnPartCounceling');
Route::post('input/pianica/counceling/kensa_awal', 'Pianica@inputKensaAwalCounceling');
Route::get('input/pianica/kensa_awal/training_document', 'Pianica@inputKensaAwalDocument');
Route::get('fetch/pianica/sign_in/tuning', 'Pianica@fetchOPTuning');
Route::post('post/pianica/sign_in/tuning', 'Pianica@inputOPTuning');
Route::get('delete/pianica/sign_in/tuning', 'Pianica@deleteOPTuning');

//record
Route::get('index/record', 'Pianica@recordPianica');
Route::post('index/recordPianica', 'Pianica@recordPianica2');
//---------- report kakuning visual
Route::get('index/reportVisual', 'Pianica@reportVisual');
Route::get('index/getKensaVisualALL', 'Pianica@getKensaVisualALL');
Route::post('index/deleteInv', 'Pianica@deleteInv');
//-------- display
Route::get('index/DisplayPN', 'Pianica@display');
Route::get('index/TotalNgAll', 'Pianica@total_ng_all');
Route::get('index/TotalNgAllLine', 'Pianica@total_ng_all_line');
Route::get('index/getTarget', 'Pianica@getTarget');
Route::get('index/GetNgBensuki', 'Pianica@GetNgBensuki');
Route::get('index/GetNgBensukiAll', 'Pianica@GetNgBensukiAll');

//---------- display Pianica
Route::get('index/display_pn_ng_rate', 'Pianica@indexNgRate');
Route::get('fetch/pianica/ng_spot_welding', 'Pianica@fetchNgWelding');
Route::get('fetch/pianica/ng_bentsuki_benage', 'Pianica@fetchNgBentsukiBenage');
Route::get('fetch/pianica/ng_kensa_awal', 'Pianica@fetchNgKensaAwal');
// Route::get('index/display_pn_ng_trends', 'Pianica@indexTrendsNgRate');
Route::get('fetch/pianica/trend_ng_spot_welding', 'Pianica@fetchTrendNgWelding');
Route::get('fetch/pianica/trend_ng_bentsuki_benage', 'Pianica@fetchTrendNgBentsukiBenage');
Route::get('fetch/pianica/trend_ng_kensa_awal', 'Pianica@fetchTrendNgKensaAwal');
Route::get('index/display_daily_pn_ng', 'Pianica@indexDailyNg');
Route::get('fetch/pianica/ng_tuning', 'Pianica@fetchNgTuning');

Route::get('fetch/pianica/totalNgReed', 'Pianica@totalNgReed');
Route::get('fetch/pianica/detailReedTuning', 'Pianica@detailReedTuning');

Route::get('fetch/pianica/totalNgReedSpotWelding', 'Pianica@totalNgReedSpotWelding');

//---------- report bensuki
Route::get('index/reportBensuki', 'Pianica@reportBensuki');
Route::get('index/getTotalNG', 'Pianica@getTotalNG');
Route::get('index/getMesinNg', 'Pianica@getMesinNg');

//---------- report kensa awal
Route::get('index/reportAwal', 'Pianica@reportAwal');
Route::get('index/getKensaAwalALL', 'Pianica@getKensaAwalALL');

//---------- report kensa awal
Route::get('index/reportAwalLine', 'Pianica@reportAwalLine');
Route::get('index/getKensaAwalALLLine', 'Pianica@getKensaAwalALLLine');

//---------- report kensa awal
Route::get('index/reportAkhir', 'Pianica@reportAkhir');
Route::get('index/getKensaAkhirALL', 'Pianica@getKensaAkhirALL');

//---------- report kensa awal
Route::get('index/reportAkhirLine', 'Pianica@reportAkhirLine');
Route::get('index/getKensaAkhirALLLine', 'Pianica@getKensaAkhirALLLine');

//report per tanggal
Route::get('index/reportDayAwal', 'Pianica@reportDayAwal');
Route::post('index/reportDayAwalData', 'Pianica@reportDayAwalData');
Route::get('index/reportDayAwalDataGrafik', 'Pianica@reportDayAwalDataGrafik');

//detail chart
Route::get('index/getKensaVisualALL2', 'Pianica@getKensaVisualALL2');
Route::get('index/getKensaBensuki2', 'Pianica@getKensaBensuki2');
Route::get('index/getKensaBensuki3', 'Pianica@getKensaBensuki3');

//detail spot welding
Route::get('index/reportSpotWelding', 'Pianica@reportSpotWelding');
Route::get('fetch/reportSpotWeldingData', 'Pianica@reportSpotWeldingData');
Route::get('fetch/reportSpotWeldingDataDetail', 'Pianica@reportSpotWeldingDataDetail');

//detail spot getReportKensaAwalDaily
Route::get('index/reportKensaAwalDaily', 'Pianica@reportKensaAwalDaily');
Route::get('fetch/getReportKensaAwalDaily', 'Pianica@getReportKensaAwalDaily');
Route::get('fetch/detailKensaAwalDaily', 'Pianica@fetchDetailKensaAwalDaily');

//end pianica

//detail spot getReportKensaAkhirDaily
Route::get('index/reportKensaAkhirDaily', 'Pianica@reportKensaAkhirDaily');
Route::get('fetch/getReportKensaAkhirDaily', 'Pianica@getReportKensaAkhirDaily');
Route::get('fetch/detailKensaAkhirDaily', 'Pianica@fetchDetailKensaAkhirDaily');

//detail spot getReportVisualDaily
Route::get('index/reportVisualDaily', 'Pianica@reportVisualDaily');
Route::get('fetch/getReportVisualDaily', 'Pianica@getReportVisualDaily');
Route::get('fetch/detailKensaVisualDaily', 'Pianica@fetchDetailKensaVisualDaily');

Route::get('index/reportAssemblyDaily', 'Pianica@reportAssemblyDaily');
Route::get('fetch/getReportAssemblyDaily', 'Pianica@getReportAssemblyDaily');
Route::get('fetch/detailKensaAssemblyDaily', 'Pianica@fetchDetailKensaAssemblyDaily');

Route::get('index/pn/ng_trend', 'Pianica@indexNGTrend');
Route::get('fetch/pn/ng_trend', 'Pianica@fetchNGTrend');

Route::get('index/pn/audit_screw/report', 'Pianica@indexReportAuditScrew');
Route::get('fetch/pn/audit_screw/report', 'Pianica@fetchReportAuditScrew');

Route::get('index/pn/stock_monitoring', 'Pianica@indexStockMonitoring');
Route::get('fetch/pn/stock_monitoring', 'Pianica@fetchStockMonitoring');
Route::post('input/pn/target', 'Pianica@inputTarget');

Route::get('index/pn/board/{line}', 'Pianica@indexBoard');
Route::get('fetch/pn/board', 'Pianica@fetchBoard');
Route::get('fetch/pn/board/result', 'Pianica@fetchBoardResult');

Route::get('index/pn/card_cleaning', 'Pianica@indexCardCleaning');
Route::get('scan/pn/card_cleaning', 'Pianica@scanCardCleaning');

Route::get('index/pn/card_migration', 'Pianica@indexCardMigration');
Route::get('fetch/pn/card_migration', 'Pianica@fetchCardMigration');
Route::get('scan/pn/card_migration_check', 'Pianica@scanCardMigrationCheck');
Route::get('scan/pn/card_migration', 'Pianica@scanCardMigration');

Route::get('index/pn/pass_ratio', 'Pianica@indexPassRatio');
Route::get('fetch/pn/pass_ratio', 'Pianica@fetchPassRatio');

//end pianica

//START STOCKTAKING
Route::group(['nav' => 'M23', 'middleware' => 'permission'], function () {
    Route::get('index/bom_output', 'StockTakingController@bom_output');
    Route::get('fetch/bom_output', 'StockTakingController@fetch_bom_output');

    Route::post('import/material/bom', 'StockTakingController@importbom');
});

Route::group(['nav' => 'M24', 'middleware' => 'permission'], function () {
    Route::get('index/material_plant_data_list', 'StockTakingController@mpdl');
    Route::get('fetch/material_plant_data_list', 'StockTakingController@fetchmpdl');

    Route::post('import/material/mpdl', 'StockTakingController@importmpdl');
});

Route::group(['nav' => 'S22', 'middleware' => 'permission'], function () {
    //Silver
    Route::get('index/stocktaking/silver/{id}', 'StockTakingController@indexSilver');
    Route::get('fetch/stocktaking/silver_list', 'StockTakingController@fetchSilverList');
    Route::get('fetch/stocktaking/silver_count', 'StockTakingController@fetchSilverCount');
    Route::get('fetch/stocktaking/silver_resume', 'StockTakingController@fetchSilverResume');
    Route::post('input/stocktaking/silver_count', 'StockTakingController@inputSilverCount');
    Route::post('input/stocktaking/silver_final', 'StockTakingController@inputSilverFinal');

    //Daily
    Route::get('index/stocktaking/daily/{id}', 'StockTakingController@indexDaily');
    Route::get('fetch/stocktaking/daily_list', 'StockTakingController@fetchDailyList');
    Route::get('fetch/stocktaking/daily_resume', 'StockTakingController@fetchDailyResume');
    Route::get('fetch/stocktaking/daily_count', 'StockTakingController@fetchDailyCount');
    Route::post('input/stocktaking/daily_count', 'StockTakingController@inputDailyCount');
    Route::post('input/stocktaking/daily_final', 'StockTakingController@inputDailyFinal');
});
//Silver Report
Route::get('index/stocktaking/silver_report', 'StockTakingController@indexSilverReport');
Route::get('fetch/stocktaking/silver_report', 'StockTakingController@fetchSilverReport');
Route::get('fetch/stocktaking/silver_report_modal', 'StockTakingController@fetchSilverReportModal');

//Daily Report
Route::get('index/stocktaking/daily_report', 'StockTakingController@indexDailyReport');
Route::get('fetch/stocktaking/daily_report', 'StockTakingController@fetchDailyReport');
Route::get('fetch/stocktaking/daily_report_modal', 'StockTakingController@fetchDailyReportModal');

//Video Stocktaking
Route::get('index/stocktaking/video_tutorial', 'StockTakingController@indexVideoStocktaking');

Route::get('index/stocktaking/survey_report', 'StockTakingController@indexSurveyReport');
Route::get('fetch/stocktaking/survey_report', 'StockTakingController@fetchSurveyReport');

//Index Monthly
Route::get('index/stocktaking/menu', 'StockTakingController@indexMonthlyStocktaking');

Route::get('fetch/stocktaking/check_month', 'StockTakingController@fetchCheckMonth');
Route::get('export/stocktaking/inquiry', 'StockTakingController@exportInquiry');
Route::get('export/stocktaking/inquiry_new', 'StockTakingController@exportInquiryNew');
Route::get('export/stocktaking/variance', 'StockTakingController@exportVariance');
Route::get('export/stocktaking/official_variance', 'StockTakingController@exportOfficailVariance');

//Monitoring
Route::get('index/stocktaking/monitoring', 'StockTakingController@indexStocktakingMonitoring');
Route::get('fetch/stocktaking/filled_list', 'StockTakingController@fetchfilledList');
Route::get('fetch/stocktaking/filled_list_detail', 'StockTakingController@fetchfilledListDetail');
Route::get('fetch/stocktaking/audited_list', 'StockTakingController@fetchAuditedList');
Route::get('fetch/stocktaking/audited_list_detail', 'StockTakingController@fetchAuditedListDetail');
Route::get('fetch/stocktaking/variance', 'StockTakingController@fetchVariance');
Route::get('fetch/stocktaking/variance_detail', 'StockTakingController@fetchVarianceDetail');

Route::get('fetch/stocktaking/filled_list_new', 'StockTakingController@fetchfilledListNew');
Route::get('fetch/stocktaking/filled_list_detail_new', 'StockTakingController@fetchfilledListDetailNew');

Route::get('fetch/stocktaking/filled_list_by_store', 'StockTakingController@fetchfilledListbByStore');
Route::get('fetch/stocktaking/filled_list_detail_by_store', 'StockTakingController@fetchfilledListDetailBystore');

Route::get('fetch/stocktaking/filled_list_by_substore', 'StockTakingController@fetchfilledListbBySubstore');
Route::get('fetch/stocktaking/filled_list_detail_by_substore', 'StockTakingController@fetchfilledListDetailBySubstore');

Route::get('fetch/stocktaking/audited_list_new', 'StockTakingController@fetchAuditedListNew');
Route::get('fetch/stocktaking/audited_list_detail_new', 'StockTakingController@fetchAuditedListDetailNew');

//Manage Store
Route::get('index/stocktaking/manage_store', 'StockTakingController@indexManageStore');
Route::get('index/stocktaking/summary_new', 'StockTakingController@indexSummaryNew');

Route::get('fetch/stocktaking/store', 'StockTakingController@fetchStore');
Route::get('fetch/stocktaking/store_details', 'StockTakingController@fetchStoreDetail');
Route::post('fetch/stocktaking/delete_store', 'StockTakingController@deleteStore');
Route::post('fetch/stocktaking/delete_material', 'StockTakingController@deleteMaterial');
Route::post('fetch/stocktaking/add_material', 'StockTakingController@addMaterial');
Route::get('fetch/stocktaking/get_storage_location', 'StockTakingController@fetchGetStorageLocation');
Route::get('fetch/stocktaking/get_store', 'StockTakingController@fetchGetStore');
Route::get('fetch/stocktaking/check_material', 'StockTakingController@fetchCheckMaterial');

Route::get('print/stocktaking/print_store/{id}', 'StockTakingController@printStore');
Route::get('reprint/stocktaking/summary_of_counting_id', 'StockTakingController@reprintIdSoc');
Route::get('reprint/stocktaking/summary_of_counting_store', 'StockTakingController@reprintStoreSoc');

Route::get('fetch/stocktaking/new_store_details', 'StockTakingController@fetchStoreDetailNew');
Route::get('reprint/stocktaking/summary_of_counting_id_new/{id}', 'StockTakingController@reprintIdSubStore');

//Summary of Counting
Route::get('index/stocktaking/summary_of_counting', 'StockTakingController@indexSummaryOfCounting');
Route::get('fetch/stocktaking/summary_of_counting', 'StockTakingController@fetchSummaryOfCounting');
Route::get('print/stocktaking/summary_of_counting', 'StockTakingController@printSummaryOfCounting');

//No Use
Route::get('index/stocktaking/no_use', 'StockTakingController@indexNoUse');
Route::post('fetch/stocktaking/update_no_use', 'StockTakingController@updateNoUse');

//New No Use
Route::get('index/stocktaking/no_use_new', 'StockTakingController@indexNoUseNew');
Route::post('fetch/stocktaking/update_no_use_new', 'StockTakingController@updateNoUseNew');

//Count
Route::get('index/stocktaking/count', 'StockTakingController@indexCount');
Route::get('fetch/stocktaking/material_detail', 'StockTakingController@fetchMaterialDetail');
Route::get('fetch/stocktaking/material_detail_audit', 'StockTakingController@fetchMaterialDetailAudit');

Route::get('fetch/stocktaking/store_list', 'StockTakingController@fetchStoreList');
Route::post('fetch/stocktaking/update_count', 'StockTakingController@updateCount');

//Count New
Route::get('index/stocktaking/count_new', 'StockTakingController@indexCountNew');
Route::get('fetch/stocktaking/material_detail_new', 'StockTakingController@fetchMaterialDetailNew');
Route::get('fetch/stocktaking/store_list_new', 'StockTakingController@fetchStoreListNew');
Route::post('fetch/stocktaking/update_count_new', 'StockTakingController@updateCountNew');

Route::get('index/stocktaking/count_fstk', 'StockTakingController@indexCountFstk');
Route::get('fetch/stocktaking/count_fstk', 'StockTakingController@fetchCountFstk');
Route::get('fetch/stocktaking/fstk_stock', 'StockTakingController@fetchMiraiFstk');
Route::get('fetch/stocktaking/check_slip', 'StockTakingController@fetchCheckSlip');
Route::post('input/stocktaking/fstk_pi', 'StockTakingController@inputFstkPi');
Route::post('delete/stocktaking/fstk_pi', 'StockTakingController@deleteFstkPi');
Route::post('export/stocktaking/fstk_pi', 'StockTakingController@exportFstkPi');

Route::get('index/stocktaking/count_scrap', 'StockTakingController@indexCountScrap');
Route::get('fetch/stocktaking/count_scrap', 'StockTakingController@fetchCountScrap');
Route::get('fetch/stocktaking/scrap', 'StockTakingController@fetchCheckScrap');
Route::get('fetch/data/scrap', 'StockTakingController@fetchDataScrap');
Route::post('input/stocktaking/mstk_pi', 'StockTakingController@inputMstkPi');
Route::post('delete/stocktaking/mstk_pi', 'StockTakingController@deleteMstkPi');
Route::post('export/stocktaking/mstk_pi', 'StockTakingController@exportMstkPi');

//Audit
Route::get('index/stocktaking/audit/{id}', 'StockTakingController@indexAudit');
Route::get('index/stocktaking/audit_new/{id}', 'StockTakingController@indexAuditNew');
Route::get('fetch/stocktaking/audit_store_list', 'StockTakingController@fetchAuditStoreList');
Route::get('fetch/stocktaking/audit_store_list_new', 'StockTakingController@fetchAuditStoreListNew');
Route::get('fetch/stocktaking/check_confirm/{id}', 'StockTakingController@fetchCheckAudit');
Route::get('fetch/stocktaking/check_confirm_new/{id}', 'StockTakingController@fetchCheckAuditNew');
Route::post('fetch/stocktaking/update_audit/{id}', 'StockTakingController@updateAudit');
Route::post('fetch/stocktaking/update_audit_new/{audit}', 'StockTakingController@updateAuditNew');
Route::post('fetch/stocktaking/update_process/{id}', 'StockTakingController@updateProcessAudit');
Route::post('fetch/stocktaking/update_process_new/{id}', 'StockTakingController@updateProcessAuditNew');

//Unmatch
Route::get('index/stocktaking/unmatch/{month}', 'StockTakingController@indexUnmatch');
Route::get('fetch/stocktaking/unmatch', 'StockTakingController@fetchUnmatch');
Route::get('fetch/stocktaking/pi_vs_book', 'StockTakingController@fetchPiVsBook');
Route::get('fetch/stocktaking/book_vs_pi', 'StockTakingController@fetchBookVsPi');
Route::get('fetch/stocktaking/kitto_vs_pi', 'StockTakingController@fetchKittoVsPi');
Route::get('fetch/stocktaking/kitto_vs_book', 'StockTakingController@fetchKittoVsBook');
Route::get('fetch/stocktaking/pi_vs_lot', 'StockTakingController@fetchPiVsLot');

//Check
Route::get('index/stocktaking/check_flo_kdo', 'StockTakingController@indexCheckFloKdo');
Route::get('fetch/stocktaking/check_flo_kdo', 'StockTakingController@fetchCheckFloKdo');

//checkInput
Route::get('index/stocktaking/check_input_new', 'StockTakingController@indexCheckInput');
Route::get('fetch/stocktaking/check_input_store_list_new', 'StockTakingController@fetchCheckInputStoreListNew');
Route::post('update/stocktaking/no_use', 'StockTakingController@updateStoreNoUse');
Route::post('update/stocktaking/open_input', 'StockTakingController@updateOpenInput');

Route::get('index/stocktaking/material_forecast', 'StockTakingController@indexStocktakingMaterialForecast');
Route::get('fetch/stocktaking/material_forecast', 'StockTakingController@fetchStocktakingMaterialForecast');

//Revise user
Route::get('index/stocktaking/revise_user', 'StockTakingController@indexReviseUser');
Route::get('fetch/stocktaking/revise_user', 'StockTakingController@fetchReviseUser');
Route::post('input/stocktaking/input_revise_user', 'StockTakingController@inputReviseUser');
Route::post('input/stocktaking/check_revise_user', 'StockTakingController@inputReviseCheck');
Route::post('input/stocktaking/execute_revise_user', 'StockTakingController@inputReviseExecute');

Route::group(['nav' => 'S36', 'middleware' => 'permission'], function () {

    Route::post('add/stocktaking/material_forecast', 'StockTakingController@addMaterialForecast');

    Route::get('index/stocktaking/calendar', 'StockTakingController@indexStocktakingCalendar');
    Route::get('fetch/stocktaking/calendar', 'StockTakingController@fetchStocktakingCalendar');
    Route::get('add/stocktaking/calendar', 'StockTakingController@addStocktakingCalendar');

    Route::get('index/stocktaking/stocktaking_list', 'StockTakingController@indexMonthlyStocktakingList');
    Route::get('fetch/stocktaking/stocktaking_list', 'StockTakingController@fetchMonthlyStocktakingList');
    Route::post('delete/stocktaking/stocktaking_list', 'StockTakingController@deleteMonthlyStocktakingList');
    Route::post('edit/stocktaking/stocktaking_list', 'StockTakingController@editMonthlyStocktakingList');
    Route::post('upload/stocktaking/stocktaking_list', 'StockTakingController@uploadMonthlyStocktakingList');
    Route::get('export/stocktaking/error_upload_stocktaking_list', 'StockTakingController@exportErrorUpload');

    //Revise
    Route::get('index/stocktaking/revise', 'StockTakingController@indexRevise');
    Route::get('fetch/stocktaking/revise', 'StockTakingController@fetchRevise');
    Route::get('fetch/stocktaking/revise_by_id', 'StockTakingController@fetchReviseId');
    Route::post('fetch/stocktaking/update_revise', 'StockTakingController@updateRevise');
    Route::post('fetch/stocktaking/upload_revise', 'StockTakingController@uploadRevise');

    Route::get('index/stocktaking/revise_new', 'StockTakingController@indexReviseNew');
    Route::post('fetch/stocktaking/update_revise_new', 'StockTakingController@updateReviseNew');
    // Route::get('fetch/stocktaking/revise', 'StockTakingController@fetchRevise');
    // Route::get('fetch/stocktaking/revise_by_id', 'StockTakingController@fetchReviseId');
    // Route::post('fetch/stocktaking/update_revise', 'StockTakingController@updateRevise');

    Route::get('export/stocktaking/upload_sap', 'StockTakingController@exportUploadSAP');
    Route::get('export/stocktaking/log', 'StockTakingController@exportLog');

    //Count PI
    Route::post('index/stocktaking/count_pi', 'StockTakingController@indexCountPI');
    Route::post('index/stocktaking/count_pi_new', 'StockTakingController@indexCountPINew');
    Route::get('index/stocktaking/bypass', 'StockTakingController@byPassAudit');

    Route::post('import/inventory/kitto', 'StockTakingController@importInvKitto');

    //YMES
    Route::get('index/stocktaking/ymes_list', 'StockTakingController@indexYmesStocktakingList');
    Route::post('upload/stocktaking/ymes_list', 'StockTakingController@uploadYmesStocktakingList');
    Route::post('delete/stocktaking/ymes_list', 'StockTakingController@deleteYmesStocktakingList');
    Route::get('index/stocktaking/unmatch_ymes_list', 'StockTakingController@indexUnmatchYmesList');
    Route::get('fetch/stocktaking/unmatch_ymes_list', 'StockTakingController@fetchUnmatchYmesList');
    Route::get('fetch/stocktaking/export_ymes_list', 'StockTakingController@fetchExportYmesList');
    Route::get('fetch/stocktaking/print_ymes_list', 'StockTakingController@fetchPrintYmesList');
    Route::post('export/stocktaking/export_ymes_to_mirai', 'StockTakingController@uploadYmesToMirai');

});

//START INDIRECT REQUEST

//Stock
Route::get('index/indirect_material_stock', 'IndirectMaterialController@indexStock');
Route::post('import/indirect_material_stock', 'IndirectMaterialController@importStock');
Route::post('input/indirect_material_stock', 'IndirectMaterialController@inputStock');
Route::get('fetch/indirect_material_stock', 'IndirectMaterialController@fetchStock');
Route::get('fetch/indirect_material_new', 'IndirectMaterialController@fetchNew');
Route::get('fetch/indirect_material_out', 'IndirectMaterialController@fetchOut');

//Log
Route::get('index/indirect_material_log', 'IndirectMaterialController@indexIndirectMaterialLog');
Route::get('fetch/indirect_material_log', 'IndirectMaterialController@fetchIndirectMaterialLog');
Route::get('print/indirect_material_label/{qr_code}', 'IndirectMaterialController@printLabel');

//Monitoring
Route::get('index/indirect_material_monitoring', 'IndirectMaterialController@indexIndirectMaterialMonitoring');
Route::get('fetch/indirect_material_monitoring', 'IndirectMaterialController@fetchIndirectMaterialMonitoring');
Route::get('fetch/indirect_material_monitoring_detail', 'IndirectMaterialController@fetchIndirectMaterialMonitoringDetail');

//END INDIRECT REQUEST

//START CHEMICAL

//Control Chart
Route::get('index/chm_solution_control', 'IndirectMaterialController@indexSolutionControl');
Route::get('fetch/chm_solution_control', 'IndirectMaterialController@fetchSolutionControl');
Route::get('fetch/chm_check_result', 'IndirectMaterialController@fetchcheckResult');
Route::post('input/chm_production_result', 'IndirectMaterialController@inputProductionResult');
Route::get('fetch/chm_get_material', 'IndirectMaterialController@fetchGetMaterial');

//Request
Route::get('index/indirect_material_request/{id}', 'IndirectMaterialController@indexRequest');
Route::get('fetch/check_qr', 'IndirectMaterialController@fetchCheckQr');
Route::get('fetch/check_req_out', 'IndirectMaterialController@fetchCheckReqOut');
Route::get('fetch/check_out', 'IndirectMaterialController@fetchCheckOut');
Route::get('fetch/chm_picked', 'IndirectMaterialController@fetchChmPicked');
Route::post('delete/chm_picked', 'IndirectMaterialController@deleteChmPicked');
Route::post('delete/chm_out', 'IndirectMaterialController@deleteChmOut');
Route::post('input/chm_picked', 'IndirectMaterialController@inputChmPicked');
Route::post('confirm/chm_req_out', 'IndirectMaterialController@inputChmOut');

//Schedule
Route::get('index/chm_picking_schedule', 'IndirectMaterialController@indexPickingSchedule');
Route::get('fetch/chm_picking_schedule', 'IndirectMaterialController@fetchPickingSchedule');
Route::get('fetch/chm_picking_schedule_detail', 'IndirectMaterialController@fetchPickingScheduleDetail');
Route::get('fetch/get_addition_chm', 'IndirectMaterialController@fetchAdditionChm');

Route::post('change/chm_schedule', 'IndirectMaterialController@changeSchedule');
Route::post('change/chm_schedule_by_chm', 'IndirectMaterialController@changeScheduleByChm');

Route::group(['nav' => 'S41', 'middleware' => 'permission'], function () {
    Route::post('index/chm_input_addition', 'IndirectMaterialController@inputChmAddition');
    Route::post('index/chm_input_new', 'IndirectMaterialController@inputChmNew');
    Route::post('delete/chm_schedule', 'IndirectMaterialController@deleteSchedule');
});

//Larutan
Route::get('index/chm_larutan', 'IndirectMaterialController@indexLarutan');
Route::get('fetch/chm_larutan', 'IndirectMaterialController@fetchLarutan');
Route::get('fetch/chm_larutan_detail', 'IndirectMaterialController@fetchLarutanDetail');
Route::post('update/chm_larutan', 'IndirectMaterialController@updateLarutan');

//END CHEMICAL

Route::group(['nav' => 'S28', 'middleware' => 'permission'], function () {
    //Pesanan + master
    Route::get('index/pantry/pesanmenu', 'PantryController@pesanmenu');
    Route::get('index/pantry/menu', 'PantryController@daftarmenu');
    Route::get('index/pantry/pesanan', 'PantryController@daftarpesanan');
    Route::get('index/pantry/confirmation', 'PantryController@daftarkonfirmasi');

    //Pesanan
    Route::get('fetch/menu', 'PantryController@fetchmenu');
    Route::get('fetch/pesanan', 'PantryController@fetchpesanan');
    Route::post('fetch/pantry/pesanan', 'PantryController@filter');
    Route::get('fetch/konfirmasi/pesanan', 'PantryController@filterkonfirmasi');

    Route::post('index/pantry/inputmenu', 'PantryController@inputMenu');
    Route::post('index/pantry/deletemenu', 'PantryController@deleteMenu');
    Route::post('index/pantry/konfirmasipesanan', 'PantryController@konfirmasipesanan');

    //CRUD Menu
    Route::get('index/pantry/create_menu', 'PantryController@create_menu');
    Route::post('index/pantry/create_menu', 'PantryController@create_menu_action');
    Route::get('index/pantry/delete_menu/{id}', 'PantryController@delete_menu');
    Route::get('index/pantry/edit_menu/{id}', 'PantryController@edit_menu');
    Route::post('index/pantry/edit_menu/{id}', 'PantryController@edit_menu_action');

    //Konfirmasi Pesanan
    Route::post('index/pantry/konfirmasi', 'PantryController@konfirmasi');
    Route::post('index/pantry/selesaikan', 'PantryController@selesaikan');

    Route::get('fetch/pantry/pesan', 'PantryController@getPesanan');

    Route::get('index/display/pantry', 'PantryController@konfirmasiasd');

    Route::get('fetch/pantry/visitor_detail', 'PantryController@fetchPantryVisitorDetail');
});
Route::get('index/display/pantry_visit', 'PantryController@indexDisplayPantryVisit');
Route::get('fetch/pantry/realtime_visitor', 'PantryController@fetchPantryRealtimeVisitor');
Route::get('fetch/pantry/visitor', 'PantryController@fetchPantryVisitor');

Route::group(['nav' => 'S11', 'middleware' => 'permission'], function () {
    Route::get('index/CheckSheet', 'CheckSheet@index');
    Route::get('create/CheckSheet', 'CheckSheet@create');
    Route::post('import/CheckSheet', 'CheckSheet@import');
    Route::post('update/CheckSheet', 'CheckSheet@update');
    Route::get('show/CheckSheet/{id}', 'CheckSheet@show');
    Route::get('check/CheckSheet/{id}', 'CheckSheet@check');
    Route::get('checkmarking/CheckSheet/{id}', 'CheckSheet@checkmarking');
    Route::get('destroy/CheckSheet/{id}', 'CheckSheet@destroy');
    Route::post('save/CheckSheet', 'CheckSheet@save');
    Route::get('direct_if_shipment/CheckSheet/{ship_list_no}', 'CheckSheet@directIfShipment');
    Route::post('add/CheckSheet', 'CheckSheet@add');
    Route::post('addDetail/CheckSheet', 'CheckSheet@addDetail');
    Route::post('addDetail2/CheckSheet', 'CheckSheet@addDetail2');
    Route::post('nomor/CheckSheet', 'CheckSheet@nomor');
    Route::post('bara/CheckSheet', 'CheckSheet@bara');
    Route::post('edit/CheckSheet/{id}', 'CheckSheet@edit');
    Route::post('marking/CheckSheet', 'CheckSheet@marking');
    Route::post('importDetail/CheckSheet', 'CheckSheet@importDetail');
    Route::get('print/CheckSheet/{id}', 'CheckSheet@print_check');
    Route::get('printsurat/CheckSheet/{id}', 'CheckSheet@print_check_surat');
    Route::get('delete/CheckSheet/{id}', 'CheckSheet@delete');
    Route::get('persen/CheckSheet/{id}', 'CheckSheet@persen');
    Route::get('fill/reason', 'CheckSheet@getReason');

    Route::post('import/driver_photo', 'CheckSheet@importDriverPhoto');
    Route::post('import/seal_photo', 'CheckSheet@importSealPhoto');
    Route::post('import/container_photo', 'CheckSheet@importContainerPhoto');
    Route::post('closure/check_checksheet', 'CheckSheet@check_nomor');

    Route::get('fetch/CheckSheet/{id}', 'CheckSheet@fetch_checksheet');

    Route::get('delete/deleteReimport', 'CheckSheet@deleteReimport');

    Route::post('import/checklist_evidence', 'CheckSheet@importChecklistEvidence');
    Route::post('input/checklist_container', 'CheckSheet@inputChecklistContainer');

});

Route::get('index/checklist_container_security', 'CheckSheet@indexSecurityChecklist');
Route::get('index/security_check/{status}', 'CheckSheet@indexSecurityCheck');
Route::get('index/security_check_report/{status}', 'CheckSheet@indexSecurityCheckReport');
Route::get('fetch/checklist_container_security', 'CheckSheet@fetchSecurityChecklist');
Route::post('input/checklist_container_security', 'CheckSheet@inputSecurityChecklist');
Route::post('input/checklist_evidence_security', 'CheckSheet@inputSecurityEvidence');

Route::get('index/shipping_order', 'ContainerScheduleController@indexShippingOrder');
Route::get('fetch/shipping_order/get_carier', 'ContainerScheduleController@fetchCarier');
Route::get('fetch/shipping_order/ship_reservation', 'ContainerScheduleController@fetchShipReservation');
Route::get('fetch/shipping_order/excel_ship_reservation', 'ContainerScheduleController@excelShipReservation');

Route::get('index/resume_shipping_order', 'ContainerScheduleController@indexResumeShippingOrder');
Route::get('fetch/resume_shipping_order', 'ContainerScheduleController@fetchResumeShippingOrder');
Route::get('fetch/resume_shipping_order_detail', 'ContainerScheduleController@fetchResumeShippingOrderDetail');

Route::get('index/shipping_agency', 'ContainerScheduleController@indexShippingAgency');
Route::get('fetch/shipping_agency', 'ContainerScheduleController@fetchShippingAgency');
Route::get('fetch/shipping_agency_detail', 'ContainerScheduleController@fetchShippingAgencyDetail');

Route::get('fetch/get_ref_number', 'ContainerScheduleController@fetchGetRefNumber');

Route::group(['nav' => 'S11', 'middleware' => 'permission'], function () {

    Route::post('fetch/shipping_order/add_ship_reservation', 'ContainerScheduleController@addShipReservation');
    Route::post('fetch/shipping_order/edit_ship_reservation', 'ContainerScheduleController@editShipReservation');
    Route::post('fetch/shipping_order/delete_ship_reservation', 'ContainerScheduleController@deleteShipReservation');
    Route::post('fetch/shipping_order/upload_ship_reservation', 'ContainerScheduleController@uploadShipReservation');

    Route::post('fetch/add_shipping_agency', 'ContainerScheduleController@addShippingAgency');

    Route::post('delete/shipping_agency', 'ContainerScheduleController@deleteShippingAgency');
    Route::post('edit/shipping_agency', 'ContainerScheduleController@editShippingAgency');

});

Route::get('stamp/stamp', 'ProcessController@stamp');
Route::post('reprint/stamp', 'ProcessController@reprint_stamp');

Route::get('index/assy_monitoring', 'ProcessController@indexAssyMonitoring');

Route::get('index/process_assy_fl', 'ProcessController@indexProcessAssyFL');
Route::get('index/process_assy_fl_0', 'ProcessController@indexProcessAssyFL0');
// Route::get('index/process_assy_fl_1', 'ProcessController@indexProcessAssyFL1');
Route::get('index/process_assy_fl_2', 'ProcessController@indexProcessAssyFL2');
Route::get('index/process_assy_fl_3', 'ProcessController@indexProcessAssyFL3');
Route::get('index/process_assy_fl_4', 'ProcessController@indexProcessAssyFL4');
Route::get('index/displayWipFl', 'ProcessController@indexDisplayWipFl');

Route::get('index/process_assembly_kensa/{id}', 'ProcessController@indexProcessKensa');
Route::get('scan/process_assembly_kensa/kensa', 'ProcessController@scanAssemblyKensa');
Route::post('input/process_assembly_kensa/kensa', 'ProcessController@inputAssemblyKensa');

// return sax
Route::get('index/repairSx', 'ProcessController@indexRepairSx');
Route::get('fetch/returnTableSx', 'ProcessController@fetchReturnTableSx');
// end return sax

// ng sax
Route::get('index/ngSx', 'ProcessController@indexngSx');
Route::get('fetch/ngTableSx', 'ProcessController@fetchngTableSx');
// end ng sax

// ng sax
Route::get('index/ngFL', 'ProcessController@indexngFL');
Route::get('fetch/ngTableFL', 'ProcessController@fetchngTableFL');
// end ng sax

// return cl
Route::get('index/repairCl', 'ProcessController@indexRepairCl');
Route::get('fetch/returnTableCl', 'ProcessController@fetchReturnTableCl');
// end return cl

Route::get('fetch/wipflallstock', 'ProcessController@fetchwipflallstock');
Route::get('fetch/wipflallchart', 'ProcessController@fetchwipflallchart');
Route::get('fetch/returnTableFl', 'ProcessController@fetchReturnTableFl');
Route::get('fetch/logTableFl', 'ProcessController@fetchLogTableFl');

Route::get('fetch/process_assy_fl/actualChart', 'ProcessController@fetchProcessAssyFLActualChart');
Route::get('fetch/process_assy_fl_0/actualChart', 'ProcessController@fetchProcessAssyFL0ActualChart');
Route::get('fetch/process_assy_fl_2/actualChart', 'ProcessController@fetchProcessAssyFL2ActualChart');
Route::get('fetch/process_assy_fl_3/actualChart', 'ProcessController@fetchProcessAssyFL3ActualChart');
Route::get('fetch/process_assy_fl_4/actualChart', 'ProcessController@fetchProcessAssyFL4ActualChart');
Route::get('fetch/process_assy_fl_Display/actualChart', 'ProcessController@fetchProcessAssyFLDisplayActualChart');

Route::get('stamp/fetchPlan', 'ProcessController@fetchStampPlan');
Route::get('stamp/fetchSerialNumber', 'ProcessController@fetchSerialNumber');
Route::get('stamp/fetchResult', 'ProcessController@fetchResult');

Route::post('stamp/stamp_detail', 'ProcessController@filter_stamp_detail');
Route::get('stamp/resumes', 'ProcessController@indexResumes');
Route::get('stamp/log', 'ProcessController@indexLog');
// });

//tambah ali Saxophone & clarinet
Route::get('stamp/fetchResult/{id}', 'ProcessController@fetchResult');
Route::get('stamp/fetchPlan/{id}', 'ProcessController@fetchStampPlan');

Route::get('index/label_besar/{id}/{gmc}/{remark}', 'ProcessController@label_besar');
Route::get('index/label_kecil/{id}/{remark}', 'ProcessController@label_kecil');
Route::get('index/label_des/{id}', 'ProcessController@label_des');
Route::get('index/get_sn', 'ProcessController@getsnsax');
Route::get('index/get_sn2', 'ProcessController@getsnsax2');
Route::get('index/process_stamp_cl_1', 'ProcessController@indexProcessAssyFLCla1');
// Route::get('index/process_assy_fl_saxA_1', 'ProcessController@indexProcessAssyFLSaxA1');
Route::get('index/process_stamp_sx_1', 'ProcessController@indexProcessAssyFLSaxT1');
Route::get('index/process_stamp_sx_2', 'ProcessController@indexProcessAssyFLSaxT2');
Route::get('index/process_stamp_sx_3', 'ProcessController@indexProcessAssyFLSaxT3');
Route::post('index/print_sax', 'ProcessController@print_sax');
Route::post('index/print_sax2', 'ProcessController@print_sax2');
Route::get('stamp/fetchStampPlansax2/{id}', 'ProcessController@fetchStampPlansax2');
Route::get('stamp/fetchStampPlansax3/{id}', 'ProcessController@fetchStampPlansax3');
Route::post('reprint/stamp2', 'ProcessController@reprint_stamp2');
Route::get('index/getModel', 'ProcessController@getModel');
Route::get('edit/stampLabel', 'ProcessController@editStampLabel');
Route::post('edit/stampLabel', 'ProcessController@updateStampLabel');
Route::get('index/reprintLabel', 'ProcessController@getModelReprint');
Route::get('index/getdatareprintAll', 'ProcessController@getModelReprintAll');
Route::get('index/getdatareprintAll2', 'ProcessController@getModelReprintAll2');

Route::get('index/process_stamp_cl', 'ProcessController@indexProcessStampCl');
Route::get('index/process_stamp_sx', 'ProcessController@indexProcessStampSX');
Route::get('index/process_stamp_sx_assy', 'ProcessController@indexProcessStampSXassy');
Route::get('stamp/resumes_cl', 'ProcessController@indexResumesCl');
Route::post('stamp/stamp_detail_cl', 'ProcessController@filter_stamp_detail_cl');
Route::get('stamp/resumes_sx', 'ProcessController@indexResumesSX');
Route::post('stamp/stamp_detail_sx', 'ProcessController@filter_stamp_detail_sx');
Route::get('fetch/fetch_plan_labelsax/{id}', 'ProcessController@fetch_plan_labelsax');

//end tambah ali

// label flute

Route::get('index/label_fl', 'ProcessController@indexLabelFL');
Route::get('stamp/fetchResultFL5', 'ProcessController@fetchResultFL5');
Route::get('stamp/fetchStampPlanFL5', 'ProcessController@fetchStampPlanFL5');
Route::get('index/getModelfl', 'ProcessController@getModelfl');
Route::get('index/get_snfl', 'ProcessController@getsnsaxfl');

Route::post('index/print_FL', 'ProcessController@print_FL');
Route::get('edit/stampLabelFL', 'ProcessController@editStampLabelFL');
Route::post('update/stampLabelFL', 'ProcessController@updateStampLabelFL');
Route::get('index/getModelReprintAllFL', 'ProcessController@getModelReprintAllFL');

Route::get('index/fl_label_besar/{id}/{gmc}/{remark}', 'ProcessController@label_besar_fl');
Route::get('index/fl_label_kecil/{id}/{remark}', 'ProcessController@label_kecil_fl');
Route::get('index/fl_label_des/{id}/{remark}', 'ProcessController@label_des_fl');
Route::get('index/fl_label_kecil2/{id}/{remark}', 'ProcessController@label_kecil2_fl');

Route::get('index/fl_label_outer/{id}/{gmc}/{remark}', 'ProcessController@label_besar_outer_fl');
Route::get('index/fl_label_carb/{id}', 'ProcessController@label_carb_fl');
Route::get('index/fl_label_carb2/{id}', 'ProcessController@label_carb_fl2');
Route::get('fetch/check_carb', 'ProcessController@fetchCheckCarb');

Route::get('fetch/check_kd_gmc', 'ProcessController@fetchCheckKd');
Route::get('index/kd_label_besar_fl/{gmc}', 'ProcessController@kd_label_besar_fl');
Route::get('index/kd_label_besar_outer_fl/{gmc}', 'ProcessController@kd_label_besar_outer_fl');
Route::get('index/kd_label_des_fl/{gmc}', 'ProcessController@kd_label_des_fl');
Route::get('index/kd_label_carb_fl/{gmc}', 'ProcessController@kd_label_carb_fl');

//end label flute

// check sheet sax

Route::get('index/process_stamp_sx_4/{model}/{sn}', 'ProcessController@indexProcessAssyFLSaxT4');
Route::get('fetch/image_sax', 'ProcessController@fetchImageSax');

Route::get('index/process_stamp_sx_check', 'ProcessController@indexProcessAssyFLSaxTCheck');

Route::get('index/process_check_transaction', 'ProcessController@indexProcessCheckTransaction');
Route::get('fetch/today_transaction', 'ProcessController@fetchTodayTransaction');
Route::get('fetch/check_transaction', 'ProcessController@fetchCheckTransaction');
Route::get('fetch/history_transaction', 'ProcessController@fetchHistoryTransaction');
Route::post('scan/check_transaction', 'ProcessController@scanCheckTransaction');

// end check sheet sax

// new sax result

Route::get('index/fetchResultSaxnew', 'ProcessController@indexfetchResultSaxnew');
Route::get('fetch/fetchResultSaxnew', 'ProcessController@fetchResultSaxnew');
//end new sax result

// new fl result
Route::get('index/fetchResultFlnew', 'ProcessController@indexfetchResultFlStamp');
Route::get('fetch/fetchResultFlnew', 'ProcessController@fetchResultFlStamp');
//end new fl result

Route::get('scan/maedaoshi_material', 'MaedaoshiController@scan_maedaoshi_material');
Route::get('scan/maedaoshi_serial', 'MaedaoshiController@scan_maedaoshi_serial');
Route::get('fetch/maedaoshi', 'MaedaoshiController@fetch_maedaoshi');
Route::get('reprint/maedaoshi', 'MaedaoshiController@reprint_maedaoshi');
Route::post('destroy/maedaoshi', 'MaedaoshiController@destroy_maedaoshi');

Route::get('scan/after_maedaoshi_material', 'MaedaoshiController@scan_after_maedaoshi_material');
Route::get('scan/after_maedaoshi_serial', 'MaedaoshiController@scan_after_maedaoshi_serial');

Route::get('index/flo_view/detail', 'FloController@index_detail');
Route::get('filter/flo_detail', 'FloController@filter_flo_detail');

Route::group(['nav' => 'R1', 'middleware' => 'permission'], function () {

});

Route::post('index/flo_detail', 'FloController@index_flo_detail');
Route::post('index/flo_invoice', 'FloController@index_flo_invoice');
Route::post('index/flo', 'FloController@index_flo');
Route::get('index/flo_container', 'FloController@index_flo_container');
Route::post('scan/material_number', 'FloController@scan_material_number');
Route::post('scan/serial_number', 'FloController@scan_serial_number');
Route::post('scan/educational_instrument', 'FloController@scan_educational_instrument');
Route::post('destroy/serial_number', 'FloController@destroy_serial_number');
Route::post('destroy/flo_attachment', 'FloController@destroy_flo_attachment');
Route::post('scan/flo_settlement', 'FloController@flo_settlement');
Route::post('reprint/flo', 'FloController@reprint_flo');
Route::post('cancel/flo_settlement', 'FloController@cancel_flo_settlement');
Route::get('fetch/flo_container', 'FloController@fetch_flo_container');
Route::get('fetch/flo_lading', 'FloController@fetch_flo_lading');
Route::post('input/flo_lading', 'FloController@input_flo_lading');
Route::post('update/flo_container', 'FloController@update_flo_container');

//SHIPMENT REPORT

//BUDGET VS ACTUAL SALES

//DISPLAY SCRAP

//DISPLAY STOCK

//EFFICIENCY
Route::get('index/efficiency/operator_loss_time', 'EfficiencyController@indexOperatorLossTime');
Route::get('fetch/efficiency/operator_loss_time', 'EfficiencyController@fetchOperatorLossTime');
Route::get('fetch/efficiency/operator_loss_time_log', 'EfficiencyController@fetchOperatorLossTimeLog');
Route::post('input/efficiency/operator_loss_time', 'EfficiencyController@inputOperatorLossTime');
Route::get('scan/efficiency/employee', 'EfficiencyController@scanEmployee');
Route::get('index/efficiency/operator_loss_time_chart', 'EfficiencyController@indexOperatorLossTimeChart');

//EFFICIENCY LEADER
Route::get('index/efficiency/report_efficiency_hourly/{location}', 'EfficiencyController@indexReportEfficiencyHourly');
Route::get('fetch/efficiency/report_efficiency_hourly', 'EfficiencyController@fetchReportEfficiencyHourly');
Route::get('index/efficiency/leader', 'EfficiencyController@indexEfficiencyLeader');
Route::post('input/efficiency/non_production', 'EfficiencyController@inputNonProduction');
Route::post('delete/efficiency/non_production', 'EfficiencyController@deleteNonProduction');
Route::post('input/efficiency/manpower', 'EfficiencyController@inputManpower');
Route::post('delete/efficiency/manpower', 'EfficiencyController@deleteManpower');
Route::post('input/efficiency/target', 'EfficiencyController@inputTarget');

//DISPLAY RAW MATERIAL
Route::get('index/material/material_monitoring/{id}', 'MaterialController@indexMaterialMonitoring');
Route::get('fetch/material/material_monitoring', 'MaterialController@fetchMaterialMonitoring');
Route::get('fetch/material/material_monitoring_single', 'MaterialController@fetchMaterialMonitoringSingle');
Route::get('fetch/material/material_control', 'MaterialController@fetchMaterialControl');

Route::get('index/material/reason_over_usage/{date}/{material_number}', 'MaterialController@indexReasonOverUsage');
Route::post('save/material/reason_over_usage', 'MaterialController@saveReasonOverUsage');

Route::get('index/material/smbmr', 'MaterialController@indexSmbmr');
Route::get('fetch/material/list_smbmr', 'MaterialController@fetchSmbmr');
Route::get('fetch/material/breakdown_smbmr', 'MaterialController@breakdown');

Route::get('index/material/plan_usage/{id}', 'MaterialController@indexPlanUsage');
Route::get('fetch/material/plan_usage/monthly', 'MaterialController@fetchMonthlyPlanUsage');
Route::get('fetch/material/plan_usage/daily', 'MaterialController@fetchDailyPlanUsage');

Route::get('/index/material/report/grgi', 'MaterialController@indexReportGrgi');
Route::post('/fetch/material/upload/grgi', 'MaterialController@uploadReportGrgi');
Route::get('/fetch/material/report/grgi', 'MaterialController@fetchReportGrgi');

Route::get('/index/material/production_plan', 'MaterialController@indexProductionPlan');
Route::get('/fetch/material/production_plan', 'MaterialController@fetchProductionPlan');

Route::get('/index/material/forecast_usage', 'MaterialController@indexForecastUsage');
Route::get('/fetch/material/forecast_usage', 'MaterialController@fetchForecastUsage');

Route::get('/index/material/mrp/{category}', 'MaterialController@indexMrp');
Route::get('/fetch/material/mrp', 'MaterialController@fetchMrp');

Route::get('/index/material/control_delivery', 'MaterialController@indexControlDelivery');
Route::get('/fetch/material/control_delivery', 'MaterialController@fetchControlDelivery');

Route::get('/index/material/in_out', 'MaterialController@indexInOut');
Route::get('/fetch/material/in_out', 'MaterialController@fetchInOut');

Route::get('/index/material/vendor', 'MaterialController@indexVendor');
Route::get('/fetch/material/vendor', 'MaterialController@fetchVendor');

Route::get('/index/raw_material/list', 'MaterialController@indexMaterialList');
Route::get('/fetch/raw_material/list', 'MaterialController@fetchMaterialList');

Route::get('/index/raw_material/stock_policy', 'MaterialController@indexStockPolicy');
Route::get('/fetch/raw_material/stock_policy', 'MaterialController@fetchStockPolicy');

Route::get('/index/material/delivery_monitoring', 'MaterialController@indexDeliveryMonitoring');
Route::get('/fetch/material/delivery_monitoring', 'MaterialController@fetchDeliveryMonitoring');

//DISPLAY PRODUCTION RESUME

//ASSY PICKING
Route::get('index/display/sub_assy/{id}', 'AssyProcessController@indexDisplayAssy');
// Route::get('fetch/display/sub_assy/{id}', 'AssyProcessController@fetchPicking');
// Route::get('fetch/display/sub_assy_acc', 'AssyProcessController@fetchPickingAcc');
// Route::get('fetch/display/welding/{id}', 'AssyProcessController@fetchPickingWelding');
Route::get('index/display/body/{id}', 'AssyProcessController@indexDisplayBody');
// Route::get('fetch/display/body/{id}', 'AssyProcessController@fetchPickingBody');

Route::get('index/display/picking/body/{id}', 'AssyProcessController@indexPickingBody');

Route::get('fetch/chart/sub_assy', 'AssyProcessController@chartPicking');
Route::get('fetch/detail/sub_assy', 'AssyProcessController@fetchPickingDetail');

//Production Report
Route::get('index/production_report/index/{id}', 'ProductionReportController@index');
Route::get('index/production_report/activity/{id}', 'ProductionReportController@activity');
Route::get('index/production_report/report_all/{id}', 'ProductionReportController@report_all');
Route::get('index/production_report/report_by_task/{id}', 'ProductionReportController@report_by_task');
Route::get('index/production_report/fetchReportByTask/{id}', 'ProductionReportController@fetchReportByTask');
Route::get('index/production_report/fetchReport/{id}', 'ProductionReportController@fetchReport');
Route::get('index/production_report/fetchReportDaily/{id}', 'ProductionReportController@fetchReportDaily');
Route::get('index/production_report/fetchReportWeekly/{id}', 'ProductionReportController@fetchReportWeekly');
Route::get('index/production_report/fetchReportMonthly/{id}', 'ProductionReportController@fetchReportMonthly');
Route::get('index/production_report/fetchReportDetailMonthly/{id}', 'ProductionReportController@fetchReportDetailMonthly');
Route::get('index/production_report/fetchReportDetailConditional/{id}', 'ProductionReportController@fetchReportDetailConditional');
Route::get('index/production_report/fetchReportDetailWeekly/{id}', 'ProductionReportController@fetchReportDetailWeekly');
Route::get('index/production_report/fetchReportDetailDaily/{id}', 'ProductionReportController@fetchReportDetailDaily');
Route::get('index/production_report/fetchReportConditional/{id}', 'ProductionReportController@fetchReportConditional');
Route::get('index/production_report/fetchReportAudit/{id}', 'ProductionReportController@fetchReportAudit');
Route::get('index/production_report/fetchReportTraining/{id}', 'ProductionReportController@fetchReportTraining');
Route::get('index/production_report/fetchReportSampling/{id}', 'ProductionReportController@fetchReportSampling');
Route::get('index/production_report/fetchReportLaporanAktivitas/{id}', 'ProductionReportController@fetchReportLaporanAktivitas');
Route::get('index/production_report/fetchPlanReport/{id}', 'ProductionReportController@fetchPlanReport');
Route::get('fetch/production_report/detail_stat/{id}', 'ProductionReportController@detailProductionReport');
Route::get('fetch/production_report/detail_training/{id}', 'ProductionReportController@detailTraining');
Route::get('fetch/production_report/detail_sampling_check/{id}', 'ProductionReportController@detailSamplingCheck');
Route::get('index/production_report/report_by_act_type/{id}/{activity_type}', 'ProductionReportController@report_by_act_type');
Route::get('index/production_report/fetchReportByLeader/{id}', 'ProductionReportController@fetchReportByLeader');
Route::get('index/production_report/fetchDetailReportWeekly/{id}', 'ProductionReportController@fetchDetailReportWeekly');
Route::get('index/production_report/fetchPointCheck/{id}', 'ProductionReportController@fetchPointCheck');
Route::get('index/production_report/fetchDetailReportPrev/{id}', 'ProductionReportController@fetchDetailReportPrev');
Route::get('index/production_report/fetchDetailReportMonthly/{id}', 'ProductionReportController@fetchDetailReportMonthly');
Route::get('index/production_report/fetchDetailReportDaily/{id}', 'ProductionReportController@fetchDetailReportDaily');

//APPROVAL LEADER TASK
Route::get('index/production_report/approval/{id}', 'ProductionReportController@approval');
Route::get('index/production_report/approval_list/{id}/{leader_name}', 'ProductionReportController@approval_list');
Route::post('index/production_report/approval_list_filter/{id}/{leader_name}', 'ProductionReportController@approval_list_filter');
Route::get('index/production_report/approval_detail/{activity_list_id}/{month}', 'ProductionReportController@approval_detail');

//Activity List
Route::get('index/activity_list', 'ActivityListController@index');
Route::get('index/activity_list/resume/{id}', 'ActivityListController@resume');
Route::post('index/activity_list/resume_filter/{id}', 'ActivityListController@resume_filter');
Route::get('index/activity_list/create', 'ActivityListController@create');
Route::get('index/activity_list/create_by_department/{id}/{no}', 'ActivityListController@create_by_department');
Route::post('index/activity_list/store', 'ActivityListController@store');
Route::post('index/activity_list/store_by_department/{id}/{no}', 'ActivityListController@store_by_department');
Route::get('index/activity_list/show/{id}', 'ActivityListController@show');
Route::get('index/activity_list/destroy/{id}', 'ActivityListController@destroy');
Route::get('index/activity_list/destroy_by_department/{id}/{department_id}/{no}', 'ActivityListController@destroy_by_department');
Route::get('index/activity_list/edit/{id}', 'ActivityListController@edit');
Route::get('index/activity_list/edit_by_department/{id}/{department_id}/{no}', 'ActivityListController@edit_by_department');
Route::post('index/activity_list/update/{id}', 'ActivityListController@update');
Route::post('index/activity_list/update_by_department/{id}/{department_id}/{no}', 'ActivityListController@update_by_department');
Route::get('index/activity_list/filter/{id}/{no}/{frequency}', 'ActivityListController@filter');

//production audit
Route::get('index/production_audit/index/{id}/{product}/{proses}', 'ProductionAuditController@index');
Route::get('index/production_audit/details/{id}', 'ProductionAuditController@details');
Route::get('index/production_audit/show/{id}/{audit_id}', 'ProductionAuditController@show');
Route::get('index/production_audit/destroy/{id}/{audit_id}/{product}/{proses}', 'ProductionAuditController@destroy');
Route::post('index/production_audit/filter_audit/{id}/{product}/{proses}', 'ProductionAuditController@filter_audit');
Route::get('index/production_audit/create/{id}/{product}/{proses}', 'ProductionAuditController@create');
Route::get('index/production_audit/create_by_point_check/{id}/{product}/{proses}/{point_check_id}', 'ProductionAuditController@create_by_point_check');
Route::post('index/production_audit/store/{id}/{product}/{proses}', 'ProductionAuditController@store');
Route::get('index/production_audit/edit/{id}/{audit_id}/{product}/{proses}', 'ProductionAuditController@edit');
Route::post('index/production_audit/update/{id}/{audit_id}/{product}/{proses}', 'ProductionAuditController@update');
Route::get('cities/get_by_country', 'ProductionAuditController@get_by_country')->name('admin.cities.get_by_country');
Route::get('index/production_audit/print_audit/{id}/{date}/{product}/{proses}', 'ProductionAuditController@print_audit');
Route::get('index/production_audit/print_audit_email/{id}/{date}/{product}/{proses}', 'ProductionAuditController@print_audit_email');
Route::get('index/production_audit/print_audit_chart/{id}/{date}/{product}/{proses}', 'ProductionAuditController@print_audit_chart');
Route::get('index/production_audit/report_audit/{id}', 'ProductionAuditController@report_audit');
Route::get('index/production_audit/fetchReport/{id}', 'ProductionAuditController@fetchReport');
Route::get('fetch/production_audit/detail_stat/{id}', 'ProductionAuditController@detailProductionAudit');
Route::get('index/production_audit/signature', 'ProductionAuditController@signature');
Route::post('index/production_audit/save_signature', 'ProductionAuditController@save_signature');
Route::post('index/production_audit/sendemail/{id}', 'ProductionAuditController@sendemail');
Route::post('index/production_audit/approval/{id}', 'ProductionAuditController@approval');
Route::get('fetch/production_audit/point_check', 'ProductionAuditController@fetchPointCheck');

//point check master
Route::get('index/point_check_audit/index/{id}', 'PointCheckController@index');
Route::post('index/point_check_audit/filter_point_check/{id}', 'PointCheckController@filter_point_check');
Route::get('index/point_check_audit/show/{id}/{point_check_audit_id}', 'PointCheckController@show');
Route::get('index/point_check_audit/show2/{point_check_audit_id}', 'PointCheckController@show2');
Route::get('index/point_check_audit/destroy/{id}/{point_check_audit_id}', 'PointCheckController@destroy');
Route::get('index/point_check_audit/create/{id}', 'PointCheckController@create');
Route::post('index/point_check_audit/store/{id}', 'PointCheckController@store');
Route::get('index/point_check_audit/edit/{id}/{point_check_audit_id}', 'PointCheckController@edit');
Route::post('index/point_check_audit/update/{id}/{point_check_audit_id}', 'PointCheckController@update');

//training
Route::get('index/training_report/index/{id}', 'TrainingReportController@index');
Route::post('index/training_report/filter_training/{id}', 'TrainingReportController@filter_training');
Route::get('index/training_report/show/{id}/{training_id}', 'TrainingReportController@show');
Route::get('index/training_report/create/{id}', 'TrainingReportController@create');
Route::post('index/training_report/store/{id}', 'TrainingReportController@store');
Route::get('index/training_report/destroy/{id}/{training_id}', 'TrainingReportController@destroy');
Route::get('index/training_report/edit/{id}/{training_id}', 'TrainingReportController@edit');
Route::post('index/training_report/update/{id}/{training_id}', 'TrainingReportController@update');
Route::get('index/training_report/details/{id}/{session_training}', 'TrainingReportController@details');
Route::post('index/training_report/insertpicture/{id}/{sessions}', 'TrainingReportController@insertpicture');
Route::post('index/training_report/insertparticipant/{id}', 'TrainingReportController@insertparticipant');
Route::get('index/training_report/destroypicture/{id}/{picture_id}/{sessions}', 'TrainingReportController@destroypicture');
Route::get('index/training_report/destroyparticipant/{id}/{participant_id}/{sessions}', 'TrainingReportController@destroyparticipant');
Route::post('index/training_report/editpicture/{id}/{picture_id}/{sessions}', 'TrainingReportController@editpicture');
Route::post('index/training_report/editparticipant/{id}/{participant_id}/{sessions}', 'TrainingReportController@editparticipant');
Route::get('index/training_report/report_training/{id}', 'TrainingReportController@report_training');
Route::get('index/training_report/fetchReport/{id}', 'TrainingReportController@fetchReport');
Route::get('fetch/training_report/detail_stat/{id}', 'TrainingReportController@detailTraining');
Route::get('index/training_report/print/{id}', 'TrainingReportController@print_training');
Route::get('index/training_report/print_training_email/{id}', 'TrainingReportController@print_training_email');
Route::get('index/training_report/print_training_approval/{id}/{month}', 'TrainingReportController@print_training_approval');
Route::get('index/training_report/scan_employee/{id}', 'TrainingReportController@scan_employee');
Route::get('index/training_report/cek_employee/{nik}/{id}', 'TrainingReportController@cek_employee');
Route::post('index/training_report/cek_employee2/{nik}/{id}', 'TrainingReportController@cek_employee2');
Route::get('index/training_participant/edit', 'TrainingReportController@getparticipant')->name('admin.participantedit');
Route::get('index/training_report/sendemail/{id}', 'TrainingReportController@sendemail');
Route::post('index/training_report/approval/{id}', 'TrainingReportController@approval');
Route::post('index/training_report/importparticipant/{id}', 'TrainingReportController@importparticipant');
Route::get('index/training_report/fetch_participant', 'TrainingReportController@fetchParticipant');
Route::get('scan/training_report/participant', 'TrainingReportController@scanParticipant');

//sampling check
Route::get('index/sampling_check/index/{id}', 'SamplingCheckController@index');
Route::post('index/sampling_check/filter_sampling/{id}', 'SamplingCheckController@filter_sampling');
Route::get('index/sampling_check/create/{id}', 'SamplingCheckController@create');
Route::post('index/sampling_check/store/{id}', 'SamplingCheckController@store');
Route::get('index/sampling_check/show/{id}/{sampling_check_id}', 'SamplingCheckController@show');
Route::get('index/sampling_check/destroy/{id}/{sampling_check_id}', 'SamplingCheckController@destroy');
Route::get('index/sampling_check/edit/{id}/{sampling_check_id}', 'SamplingCheckController@edit');
Route::post('index/sampling_check/update/{id}/{sampling_check_id}', 'SamplingCheckController@update');
Route::get('index/sampling_check/details/{sampling_check_id}', 'SamplingCheckController@details');
Route::get('index/sampling_check/createdetails/{sampling_check_id}', 'SamplingCheckController@createdetails');
Route::post('index/sampling_check/storedetails/{sampling_check_id}', 'SamplingCheckController@storedetails');
Route::get('index/sampling_check/destroydetails/{sampling_id}/{sampling_check_id}', 'SamplingCheckController@destroydetails');
Route::get('index/sampling_check/createdetails/{sampling_check_id}', 'SamplingCheckController@createdetails');
Route::post('index/sampling_check/storedetails/{sampling_check_id}', 'SamplingCheckController@storedetails');
Route::get('index/sampling_check/editdetails/{id}/{sampling_check_details_id}', 'SamplingCheckController@editdetails');
Route::post('index/sampling_check/updatedetails/{id}/{sampling_check_details_id}', 'SamplingCheckController@updatedetails');
Route::get('index/sampling_check/report_sampling_check/{id}', 'SamplingCheckController@report_sampling_check');
Route::get('index/sampling_check/fetchReport/{id}', 'SamplingCheckController@fetchReport');
Route::get('fetch/sampling_check/detail_stat/{id}', 'SamplingCheckController@detail_sampling_check');
Route::get('index/sampling_check/print_sampling/{id}/{month}', 'SamplingCheckController@print_sampling');
Route::get('index/sampling_check/print_sampling_email/{id}/{month}', 'SamplingCheckController@print_sampling_email');
Route::get('index/sampling_check/print_sampling_chart/{id}/{subsection}/{month}', 'SamplingCheckController@print_sampling_chart');
Route::post('index/sampling_check/approval/{id}/{month}', 'SamplingCheckController@approval');
Route::post('index/sampling_check/send_email/{id}', 'SamplingCheckController@sendemail');

//Laporan AKtivitas Audit
Route::get('index/audit_report_activity/index/{id}', 'AuditReportActivityController@index');
Route::post('index/audit_report_activity/filter_audit_report/{id}', 'AuditReportActivityController@filter_audit_report');
Route::get('index/audit_report_activity/create/{id}', 'AuditReportActivityController@create');
Route::post('index/audit_report_activity/store/{id}', 'AuditReportActivityController@store');
Route::get('index/audit_report_activity/show/{id}/{audit_report_id}', 'AuditReportActivityController@show');
Route::get('index/audit_report_activity/destroy/{id}/{audit_report_id}', 'AuditReportActivityController@destroy');
Route::get('index/audit_report_activity/edit/{id}/{audit_report_id}', 'AuditReportActivityController@edit');
Route::post('index/audit_report_activity/update/{id}/{audit_report_id}', 'AuditReportActivityController@update');
Route::get('index/audit_report_activity/report_audit_activity/{id}', 'AuditReportActivityController@report_audit_activity');
Route::get('index/audit_report_activity/fetchReport/{id}', 'AuditReportActivityController@fetchReport');
Route::get('fetch/audit_report_activity/detail_laporan_aktivitas/{id}', 'AuditReportActivityController@detail_laporan_aktivitas');
Route::get('index/audit_report_activity/print_audit_report/{id}/{month}', 'AuditReportActivityController@print_audit_report');
Route::get('index/audit_report_activity/print_audit_report_chart/{id}/{subsection}/{month}', 'AuditReportActivityController@print_audit_report_chart');
Route::get('index/audit_report_activity/print_audit_report_email/{id}/{month}', 'AuditReportActivityController@print_audit_report_email');
Route::post('index/audit_report_activity/send_email/{id}', 'AuditReportActivityController@sendemail');
Route::post('index/audit_report_activity/approval/{id}', 'AuditReportActivityController@approval');
Route::get('index/getemployee', 'AuditReportActivityController@getemployee');
Route::get('scan/audit_report_activity/participant', 'AuditReportActivityController@scanEmployee');
Route::get('fetch/audit_report_activity/qc_koteihyo', 'AuditReportActivityController@fetchAuditIkQcKoteihyo');
Route::get('index/audit_report_activity/qa_verification/{status}/{id}', 'AuditReportActivityController@indexAuditQAVerification');
Route::post('input/audit_report_activity/qa_verification', 'AuditReportActivityController@inputAuditQAVerification');
Route::get('index/audit_report_activity/document', 'AuditReportActivityController@indexManageDocument');
Route::get('fetch/audit_report_activity/document', 'AuditReportActivityController@fetchManageDocument');
Route::post('input/audit_report_activity/document', 'AuditReportActivityController@inputManageDocument');
Route::post('update/audit_report_activity/document', 'AuditReportActivityController@updateManageDocument');
Route::get('delete/audit_report_activity/document/{id}', 'AuditReportActivityController@deleteManageDocument');

Route::get('index/audit_report_activity/unmatch', 'AuditReportActivityController@indexUnmatchDocument');
Route::get('fetch/audit_report_activity/unmatch', 'AuditReportActivityController@fetchUnmatchDocument');

//Interview
Route::get('index/interview/index/{id}', 'InterviewController@index');
Route::post('index/interview/filter_interview/{id}', 'InterviewController@filter_interview');
Route::get('index/interview/show/{id}/{interview_id}', 'InterviewController@show');
Route::get('index/interview/destroy/{id}/{interview_id}/{status}', 'InterviewController@destroy');
Route::get('index/interview/create/{id}', 'InterviewController@create');
Route::post('index/interview/store/{id}', 'InterviewController@store');
Route::get('index/interview/edit/{id}/{interview_id}', 'InterviewController@edit');
Route::post('index/interview/update/{id}/{interview_id}/{status}', 'InterviewController@update');
Route::get('index/interview/details/{interview_id}', 'InterviewController@details');
Route::post('index/interview/create_participant', 'InterviewController@create_participant');
Route::get('index/interview/getdetail', 'InterviewController@getdetail')->name('interview.getdetail');
Route::post('index/interview/edit_participant/{interview_id}/{detail_id}', 'InterviewController@edit_participant');
Route::get('index/interview/destroy_participant/{interview_id}/{detail_id}/{status}', 'InterviewController@destroy_participant');
Route::get('index/interview/print_interview/{interview_id}', 'InterviewController@print_interview');
Route::get('index/interview/print_email/{interview_id}', 'InterviewController@print_email');
Route::get('index/interview/print_approval/{activity_list_id}/{month}', 'InterviewController@print_approval');
Route::post('index/interview/approval/{interview_id}', 'InterviewController@approval');
Route::get('index/interview/sendemail/{interview_id}/{status}', 'InterviewController@sendemail');
Route::post('index/interview/insertpicture/{id}/{status}', 'InterviewController@insertpicture');
Route::get('index/interview/destroypicture/{id}/{picture_id}/{status}', 'InterviewController@destroypicture');
Route::post('index/interview/editpicture/{id}/{picture_id}/{status}', 'InterviewController@editpicture');
Route::get('index/interview/detail_nilai', 'InterviewController@detailNilai');

Route::get('index/interview/pointing_call', 'InterviewController@indexPointingCall');
Route::get('index/interview/pointing_call/details/{interview_id}', 'InterviewController@detailsPointingCall');
Route::get('index/interview/pointing_call/edit/{id}/{interview_id}', 'InterviewController@editPointingCall');

//DAILY CHECK FG
Route::get('index/daily_check_fg/product/{id}', 'DailyCheckController@product');
Route::get('index/daily_check_fg/index/{id}/{product}', 'DailyCheckController@index');
Route::post('index/daily_check_fg/filter_daily_check/{id}/{product}', 'DailyCheckController@filter_daily_check');
Route::get('index/daily_check_fg/show/{id}/{daily_check_id}', 'DailyCheckController@show');
Route::get('index/daily_check_fg/destroy/{id}/{daily_check_id}', 'DailyCheckController@destroy');
Route::get('index/daily_check_fg/create/{id}/{product}', 'DailyCheckController@create');
Route::post('index/daily_check_fg/store/{id}/{product}', 'DailyCheckController@store');
Route::get('index/daily_check_fg/getdetail', 'DailyCheckController@getdetail')->name('daily_check_fg.getdetail');
Route::post('index/daily_check_fg/update/{id}/{product}', 'DailyCheckController@update');
Route::get('index/daily_check_fg/print_daily_check/{id}/{month}', 'DailyCheckController@print_daily_check');
Route::post('index/daily_check_fg/sendemail/{id}', 'DailyCheckController@sendemail');
Route::get('index/daily_check_fg/print_daily_check_email/{id}/{month}', 'DailyCheckController@print_daily_check_email');
Route::post('index/daily_check_fg/approval/{id}/{month}', 'DailyCheckController@approval');

//LABELING
Route::get('index/labeling/index/{id}', 'LabelingController@index');
Route::post('index/labeling/filter_labeling/{id}', 'LabelingController@filter_labeling');
Route::get('index/labeling/show/{id}/{labeling_id}', 'LabelingController@show');
Route::get('index/labeling/destroy/{id}/{labeling_id}', 'LabelingController@destroy');
Route::get('index/labeling/create/{id}', 'LabelingController@create');
Route::post('index/labeling/store/{id}', 'LabelingController@store');
Route::get('index/labeling/edit/{id}/{labeling_id}', 'LabelingController@edit');
Route::post('index/labeling/update/{id}/{labeling_id}', 'LabelingController@update');
Route::get('index/labeling/print_labeling/{id}/{month}', 'LabelingController@print_labeling');
Route::get('index/labeling/print_labeling_email/{id}/{month}', 'LabelingController@print_labeling_email');
Route::post('index/labeling/sendemail/{id}', 'LabelingController@sendemail');
Route::post('index/labeling/approval/{id}/{month}', 'LabelingController@approval');

//AUDIT PROCESS
Route::get('index/audit_process/index/{id}', 'AuditProcessController@index');
Route::post('index/audit_process/filter_audit_process/{id}', 'AuditProcessController@filter_audit_process');
Route::get('index/audit_process/show/{id}/{audit_process_id}', 'AuditProcessController@show');
Route::get('index/audit_process/destroy/{id}/{audit_process_id}', 'AuditProcessController@destroy');
Route::get('index/audit_process/create/{id}', 'AuditProcessController@create');
Route::post('index/audit_process/store/{id}', 'AuditProcessController@store');
Route::get('index/audit_process/edit/{id}/{audit_process_id}', 'AuditProcessController@edit');
Route::post('index/audit_process/update/{id}/{audit_process_id}', 'AuditProcessController@update');
Route::get('index/audit_process/print_audit_process/{id}/{month}', 'AuditProcessController@print_audit_process');
Route::post('index/audit_process/sendemail/{id}', 'AuditProcessController@sendemail');
Route::get('index/audit_process/print_audit_process_email/{id}/{month}', 'AuditProcessController@print_audit_process_email');
Route::post('index/audit_process/approval/{id}/{month}', 'AuditProcessController@approval');

//FIRST PRODUCT AUDIT
Route::get('index/first_product_audit/index/{id}', 'FirstProductAuditController@index');
Route::get('index/first_product_audit/list_proses/{id}', 'FirstProductAuditController@list_proses');
Route::get('index/first_product_audit/show/{id}/{first_product_audit_id}', 'FirstProductAuditController@show');
Route::get('index/first_product_audit/destroy/{id}/{first_product_audit_id}', 'FirstProductAuditController@destroy');
Route::get('index/first_product_audit/create/{id}', 'FirstProductAuditController@create');
Route::post('index/first_product_audit/store/{id}', 'FirstProductAuditController@store');
Route::get('index/first_product_audit/edit/{id}/{first_product_audit_id}', 'FirstProductAuditController@edit');
Route::post('index/first_product_audit/update/{id}/{first_product_audit_id}', 'FirstProductAuditController@update');
Route::get('index/first_product_audit/details/{id}/{first_product_audit_id}', 'FirstProductAuditController@details');
Route::post('index/first_product_audit/filter_first_product_detail/{id}/{first_product_audit_id}', 'FirstProductAuditController@filter_first_product_detail');
Route::post('index/first_product_audit/store_details/{id}/{first_product_audit_id}', 'FirstProductAuditController@store_details');
Route::get('index/first_product_audit/getdetail', 'FirstProductAuditController@getdetail')->name('first_product_audit.getdetail');
Route::post('index/first_product_audit/update_details/{id}/{first_product_audit_detail_id}', 'FirstProductAuditController@update_details');
Route::get('index/first_product_audit/destroy_details/{id}/{first_product_audit_detail_id}', 'FirstProductAuditController@destroy_details');
Route::get('index/first_product_audit/print_first_product_audit/{id}/{first_product_audit_id}/{month}', 'FirstProductAuditController@print_first_product_audit');
Route::get('index/first_product_audit/print_first_product_audit_email/{id}/{first_product_audit_id}/{month}', 'FirstProductAuditController@print_first_product_audit_email');
Route::post('index/first_product_audit/sendemail/{id}/{first_product_audit_id}', 'FirstProductAuditController@sendemail');
Route::post('index/first_product_audit/approval/{id}/{first_product_audit_id}/{month}', 'FirstProductAuditController@approval');

//daily first product audit
Route::get('index/first_product_audit/daily/{id}/{first_product_audit_id}', 'FirstProductAuditController@daily');
Route::post('index/first_product_audit/filter_first_product_daily/{id}/{first_product_audit_id}', 'FirstProductAuditController@filter_first_product_daily');
Route::post('index/first_product_audit/store_daily/{id}/{first_product_audit_id}', 'FirstProductAuditController@store_daily');
Route::get('index/first_product_audit/getdaily', 'FirstProductAuditController@getdaily')->name('first_product_audit.getdaily');
Route::post('index/first_product_audit/update_daily/{id}/{first_product_audit_detail_id}', 'FirstProductAuditController@update_daily');
Route::get('index/first_product_audit/destroy_daily/{id}/{first_product_audit_detail_id}', 'FirstProductAuditController@destroy_daily');
Route::get('index/first_product_audit/print_first_product_audit_daily/{id}/{first_product_audit_id}/{month}', 'FirstProductAuditController@print_first_product_audit_daily');
Route::get('index/first_product_audit/print_first_product_audit_email_daily/{id}/{first_product_audit_id}/{month}', 'FirstProductAuditController@print_first_product_audit_email_daily');
Route::post('index/first_product_audit/sendemail_daily/{id}/{first_product_audit_id}', 'FirstProductAuditController@sendemail_daily');
Route::post('index/first_product_audit/approval_daily/{id}/{first_product_audit_id}/{month}', 'FirstProductAuditController@approval_daily');

//SCHEDULE AUDIT IK
Route::get('index/audit_guidance/index/{id}', 'AuditGuidanceController@index');
Route::post('index/audit_guidance/filter_guidance/{id}', 'AuditGuidanceController@filter_guidance');
Route::get('index/audit_guidance/show/{id}/{audit_guidance_id}', 'AuditGuidanceController@show');
Route::get('index/audit_guidance/destroy/{id}/{audit_guidance_id}', 'AuditGuidanceController@destroy');
Route::post('index/audit_guidance/store/{id}', 'AuditGuidanceController@store');
Route::get('index/audit_guidance/getdetail', 'AuditGuidanceController@getdetail')->name('audit_guidance.getdetail');
Route::post('index/audit_guidance/update/{id}/{audit_guidance_id}', 'AuditGuidanceController@update');
Route::get('fetch/audit_guidance/template', 'AuditGuidanceController@downloadTemplate');
Route::post('input/audit_guidance/template', 'AuditGuidanceController@uploadTemplate');
Route::get('send/audit_guidance/email', 'AuditGuidanceController@sendEmail');
Route::get('index/approval/audit_guidance/{id}/{periode}/{remark}', 'AuditGuidanceController@approvalAuditGuidance');

Route::post('index/audit_guidance/update_new/{id}/{audit_guidance_id}', 'AuditGuidanceController@updateNew');

//report leader tasks
Route::group(['nav' => 'M25', 'middleware' => 'permission'], function () {

});
Route::get('index/leader_task_report/index/{id}', 'LeaderTaskReportController@index');
Route::get('index/leader_task_report/leader_task_list/{id}/{leader_name}', 'LeaderTaskReportController@leader_task_list');
Route::post('index/leader_task_report/filter_leader_task/{id}/{leader_name}', 'LeaderTaskReportController@filter_leader_task');
Route::get('index/leader_task_report/leader_task_detail/{activity_list_id}/{month}', 'LeaderTaskReportController@leader_task_detail');
Route::get('index/leader_task_report/fetch_report', 'LeaderTaskReportController@fetchReport');
Route::get('index/leader_task_report/filter', 'LeaderTaskReportController@filter');
Route::get('index/leader_task_report/filter_detail', 'LeaderTaskReportController@filter_detail');

//AREA CHECK POINT
Route::get('index/area_check_point/index/{id}', 'AreaCheckPointController@index');
Route::get('index/area_check_point/show/{id}/{area_check_point_id}', 'AreaCheckPointController@show');
Route::get('index/area_check_point/destroy/{id}/{area_check_point_id}', 'AreaCheckPointController@destroy');
Route::post('index/area_check_point/store/{id}', 'AreaCheckPointController@store');
Route::get('index/area_check_point/getdetail', 'AreaCheckPointController@getdetail')->name('area_check_point.getdetail');
Route::post('index/area_check_point/update/{id}/{area_check_point_id}', 'AreaCheckPointController@update');

//AREA CHECK
Route::get('index/area_check/index/{id}', 'AreaCheckController@index');
Route::post('index/area_check/filter_area_check/{id}', 'AreaCheckController@filter_area_check');
Route::post('index/area_check/store/{id}', 'AreaCheckController@store');
Route::get('index/area_check/getareacheck', 'AreaCheckController@getareacheck')->name('area_check.getareacheck');
Route::post('index/area_check/update/{id}', 'AreaCheckController@update');
Route::get('index/area_check/destroy/{id}/{area_check_id}', 'AreaCheckController@destroy');
Route::get('index/area_check/print_area_check/{id}/{month}/{location}', 'AreaCheckController@print_area_check');
Route::get('index/area_check/print_area_check_email/{id}/{month}', 'AreaCheckController@print_area_check_email');
Route::post('index/area_check/sendemail/{id}', 'AreaCheckController@sendemail');
Route::post('index/area_check/approval/{id}/{month}', 'AreaCheckController@approval');

//JISHU HOZEN POINT
Route::get('index/jishu_hozen_point/index/{id}', 'JishuHozenPointController@index');
Route::get('index/jishu_hozen_point/show/{id}/{jishu_hozen_point_id}', 'JishuHozenPointController@show');
Route::get('index/jishu_hozen_point/destroy/{id}/{jishu_hozen_point_id}', 'JishuHozenPointController@destroy');
Route::post('index/jishu_hozen_point/store/{id}', 'JishuHozenPointController@store');
Route::get('index/jishu_hozen_point/getdetail', 'JishuHozenPointController@getdetail')->name('jishu_hozen_point.getdetail');
Route::post('index/jishu_hozen_point/update/{id}/{jishu_hozen_point_id}', 'JishuHozenPointController@update');

//JISHU HOZEN
Route::get('index/jishu_hozen/nama_pengecekan/{id}', 'JishuHozenController@nama_pengecekan');
Route::get('index/jishu_hozen/index/{id}/{jishu_hozen_point_id}', 'JishuHozenController@index');
Route::post('index/jishu_hozen/filter_jishu_hozen/{id}/{jishu_hozen_point_id}', 'JishuHozenController@filter_jishu_hozen');
Route::post('index/jishu_hozen/store/{id}/{jishu_hozen_point_id}', 'JishuHozenController@store');
Route::get('index/jishu_hozen/getjishuhozen', 'JishuHozenController@getjishuhozen')->name('jishu_hozen.getjishuhozen');
Route::post('index/jishu_hozen/update/{id}/{jishu_hozen_point_id}/{jishu_hozen_id}', 'JishuHozenController@update');
Route::get('index/jishu_hozen/destroy/{id}/{jishu_hozen_point_id}/{jishu_hozen_id}', 'JishuHozenController@destroy');
Route::get('index/jishu_hozen/print_jishu_hozen/{id}/{jishu_hozen_id}/{month}', 'JishuHozenController@print_jishu_hozen');
Route::get('index/jishu_hozen/print_jishu_hozen_email/{id}/{jishu_hozen_id}/{month}', 'JishuHozenController@print_jishu_hozen_email');
Route::get('index/jishu_hozen/print_jishu_hozen_approval/{activity_list_id}/{month}', 'JishuHozenController@print_jishu_hozen_approval');
Route::get('index/jishu_hozen/sendemail/{id}/{jishu_hozen_point_id}', 'JishuHozenController@sendemail');
Route::post('index/jishu_hozen/approval/{id}/{jishu_hozen_id}/{month}', 'JishuHozenController@approval');
Route::get('fetch/jishu_hozen_prod', 'JishuHozenController@fetchJishuHozenProd');

//MAINTENANCE JISHU HOZEN
Route::get('index/maintenance/jishu_hozen', 'JishuHozenController@indexJishuHozen');
Route::get('fetch/maintenance/jishu_hozen/title', 'JishuHozenController@fetchJishuHozenTitle');
Route::get('fetch/maintenance/jishu_hozen', 'JishuHozenController@fetchJishuHozen');
Route::post('input/maintenance/jishu_hozen', 'JishuHozenController@inputJishuHozen');
Route::get('edit/maintenance/jishu_hozen', 'JishuHozenController@editJishuHozen');
Route::post('update/maintenance/jishu_hozen', 'JishuHozenController@updateJishuHozen');

//MAINTENANCE JISHU HOZEN POINT CHECK
Route::get('index/maintenance/jishu_hozen_point', 'JishuHozenPointController@indexJishuHozenPoint');
Route::get('fetch/maintenance/jishu_hozen_point', 'JishuHozenPointController@fetchJishuHozenPoint');
Route::post('input/maintenance/jishu_hozen_point', 'JishuHozenPointController@inputJishuHozenPoint');

//DISPLAY JISHU HOZEN
Route::get('index/maintenance/display/jishu_hozen', 'JishuHozenController@indexDisplayJishuHozen');
Route::get('fetch/maintenance/display/jishu_hozen', 'JishuHozenController@fetchDisplayJishuHozen');

//APD CHECK
Route::get('index/apd_check/index/{id}', 'ApdCheckController@index');
Route::post('index/apd_check/filter_apd_check/{id}', 'ApdCheckController@filter_apd_check');
Route::post('index/apd_check/store/{id}', 'ApdCheckController@store');
Route::get('index/apd_check/getapdcheck', 'ApdCheckController@getapdcheck')->name('apd_check.getapdcheck');
Route::post('index/apd_check/update/{id}', 'ApdCheckController@update');
Route::get('index/apd_check/destroy/{id}/{area_check_id}', 'ApdCheckController@destroy');
Route::get('index/apd_check/print_apd_check/{id}/{month}', 'ApdCheckController@print_apd_check');
Route::get('index/apd_check/print_apd_check_email/{id}/{month}', 'ApdCheckController@print_apd_check_email');
Route::post('index/apd_check/sendemail/{id}', 'ApdCheckController@sendemail');
Route::post('index/apd_check/approval/{id}/{month}', 'ApdCheckController@approval');

//APD
Route::get('index/apd', 'APDController@indexAPD');
Route::get('fetch/apd', 'APDController@fetchAPD');
Route::get('fetch/apd_detail', 'APDController@fetchAPDDetail');
Route::post('input/apd', 'APDController@inputAPD');

//WEEKLY REPORT
Route::get('index/weekly_report/index/{id}', 'WeeklyActivityReportController@index');
Route::post('index/weekly_report/filter_weekly_report/{id}', 'WeeklyActivityReportController@filter_weekly_report');
Route::post('index/weekly_report/store/{id}', 'WeeklyActivityReportController@store');
Route::get('index/weekly_report/getweeklyreport', 'WeeklyActivityReportController@getweeklyreport')->name('weekly_report.getweeklyreport');
Route::post('index/weekly_report/update/{id}', 'WeeklyActivityReportController@update');
Route::get('index/weekly_report/destroy/{id}/{area_check_id}', 'WeeklyActivityReportController@destroy');
Route::get('index/weekly_report/print_weekly_report/{id}/{tgl_from}/{tgl_to}', 'WeeklyActivityReportController@print_weekly_report');
Route::get('index/weekly_report/print_weekly_report_email/{id}/{month}', 'WeeklyActivityReportController@print_weekly_report_email');
Route::post('index/weekly_report/sendemail/{id}', 'WeeklyActivityReportController@sendemail');
Route::post('index/weekly_report/approval/{id}/{month}', 'WeeklyActivityReportController@approval');

//NG FINDING
Route::get('index/ng_finding/index/{id}', 'NgFindingController@index');
Route::post('index/ng_finding/filter_ng_finding/{id}', 'NgFindingController@filter_ng_finding');
Route::post('index/ng_finding/store/{id}', 'NgFindingController@store');
Route::get('index/ng_finding/getngfinding', 'NgFindingController@getngfinding')->name('ng_finding.getngfinding');
Route::post('index/ng_finding/update/{id}/{ng_finding_id}', 'NgFindingController@update');
Route::get('index/ng_finding/destroy/{id}/{area_check_id}', 'NgFindingController@destroy');
Route::get('index/ng_finding/print_ng_finding/{id}/{month}', 'NgFindingController@print_ng_finding');
Route::get('index/ng_finding/print_ng_finding_email/{id}/{month}', 'NgFindingController@print_ng_finding_email');
Route::post('index/ng_finding/sendemail/{id}', 'NgFindingController@sendemail');
Route::post('index/ng_finding/approval/{id}/{month}', 'NgFindingController@approval');

//AUDIT KANBAN LEADER
Route::get('index/audit_kanban/index/{id}', 'AuditKanbanController@index');
Route::get('fetch/audit_kanban', 'AuditKanbanController@fetchAuditKanban');
Route::post('input/audit_kanban', 'AuditKanbanController@inputAuditKanban');
Route::get('email/audit_kanban', 'AuditKanbanController@emailAuditKanban');
Route::get('edit/audit_kanban', 'AuditKanbanController@editAuditKanban');
Route::post('update/audit_kanban', 'AuditKanbanController@updateAuditKanban');
Route::get('approval/audit_kanban/{activity_list}/{month}', 'AuditKanbanController@approvalAuditKanban');
Route::get('index/audit_kanban/print_audit_kanban/{activity_list}/{month}', 'AuditKanbanController@printAuditKanban');

//RECORDER PUSH BLOCK CHECK
Route::get('index/recorder_process', 'RecorderProcessController@index');
Route::get('index/recorder_process_push_block/{remark}', 'RecorderProcessController@index_push_block');
Route::get('index/fetch_push_block', 'RecorderProcessController@fetch_push_block');
Route::get('fetch/fetch_cavity', 'RecorderProcessController@fetch_cavity');
Route::get('fetch/cavity_detail', 'RecorderProcessController@fetch_cavity_detail');
Route::get('scan/recorder', 'RecorderProcessController@scan_tag');
Route::post('index/push_block_recorder/create', 'RecorderProcessController@create');
Route::post('index/push_block_recorder/create_temp', 'RecorderProcessController@create_temp');
Route::post('index/push_block_recorder/update_temp', 'RecorderProcessController@update_temp');
Route::get('index/push_block_recorder/get_temp', 'RecorderProcessController@get_temp');
Route::post('index/push_block_recorder_resume/create_resume', 'RecorderProcessController@create_resume');
Route::post('index/push_block_recorder/return_completion', 'RecorderProcessController@return_completion');
Route::get('index/fetchResume', 'RecorderProcessController@fetchResume');
Route::post('index/import_push_block', 'RecorderProcessController@import_push_block');
Route::get('index/recorder/report_push_block/{remark}', 'RecorderProcessController@report_push_block');
Route::post('index/recorder/filter_report_push_block/{remark}', 'RecorderProcessController@filter_report_push_block');
Route::get('index/recorder/resume_push_block/{remark}', 'RecorderProcessController@resume_push_block');
Route::post('index/recorder/filter_resume_push_block/{remark}', 'RecorderProcessController@filter_resume_push_block');
Route::get('index/recorder/push_block_check_monitoring/{remark}', 'RecorderProcessController@push_block_check_monitoring');
Route::get('fetch/recorder/push_block_check_monitoring/{remark}', 'RecorderProcessController@fetch_push_block_check_monitoring');
Route::get('fetch/recorder/height_check_monitoring/{remark}', 'RecorderProcessController@fetch_height_check_monitoring');
Route::get('index/recorder/detail_monitoring', 'RecorderProcessController@detail_monitoring');
Route::get('index/recorder/detail_monitoring2', 'RecorderProcessController@detail_monitoring2');
Route::post('index/recorder/print_report_push_block/{remark}', 'RecorderProcessController@print_report_push_block');
Route::get('index/recorder/get_push_pull', 'RecorderProcessController@get_push_pull')->name('recorder.get_push_pull');
Route::post('index/recorder/update/{id}', 'RecorderProcessController@update');
Route::get('index/recorder/get_resume', 'RecorderProcessController@get_resume');
Route::post('index/recorder/update_resume/{id}', 'RecorderProcessController@update_resume');
Route::get('index/recorder/clean_kanban', 'RecorderProcessController@indexCleanKanban');

Route::get('index/recorder/ng_box', 'RecorderProcessController@indexRcNgBox');
Route::get('fetch/recorder/report/ng_box', 'RecorderProcessController@fetchRcNgBox');

Route::get('index/recorder/ng_rate/data', 'RecorderProcessController@indexRcNgData');
Route::get('fetch/recorder/ng_rate/data', 'RecorderProcessController@fetchRcNgData');

//MACHINE PARAMETER
Route::get('index/machine_parameter/{remark}', 'RecorderProcessController@indexMachineParameter');
Route::post('index/filter_machine_parameter/{remark}', 'RecorderProcessController@filterMachineParameter');
Route::get('index/fetch_mesin_parameter', 'RecorderProcessController@fetch_mesin_parameter');
Route::get('index/fetch_mesin_parameter_new', 'RecorderProcessController@fetch_mesin_parameter_new');
Route::post('index/push_block_recorder/create_parameter', 'RecorderProcessController@create_parameter');
Route::get('index/push_block_recorder/get_parameter', 'RecorderProcessController@get_parameter')->name('recorder.get_parameter');
Route::post('index/push_block_recorder/update_parameter/{id}', 'RecorderProcessController@update_parameter');
Route::get('index/push_block_recorder/delete_parameter/{id}', 'RecorderProcessController@delete_parameter');

//RECORDER TORQUE CHECK FSA
Route::get('index/recorder_process_torque/{remark}', 'RecorderProcessController@index_torque');
Route::get('index/fetchResumeTorque', 'RecorderProcessController@fetchResumeTorque');
Route::post('index/push_block_recorder/create_torque', 'RecorderProcessController@create_torque');
Route::get('index/push_block_recorder/get_temp_torque', 'RecorderProcessController@get_temp_torque');
Route::post('index/push_block_recorder/create_temp_torque', 'RecorderProcessController@create_temp_torque');
Route::post('index/push_block_recorder/update_temp_torque', 'RecorderProcessController@update_temp_torque');
Route::get('index/recorder/report_torque_check/{remark}', 'RecorderProcessController@report_torque_check');
Route::post('index/recorder/filter_report_torque_check/{remark}', 'RecorderProcessController@filter_report_torque_check');
Route::get('index/recorder/get_torque', 'RecorderProcessController@get_torque')->name('recorder.get_torque');
Route::post('index/recorder/update_torque/{id}', 'RecorderProcessController@update_torque');
Route::get('index/recorder/get_torque_all', 'RecorderProcessController@get_torque_all');
Route::post('index/recorder/update_torque_all', 'RecorderProcessController@update_torque_all');

//RECORDER TORQUE CHECK AI
Route::get('index/recorder_process_torque_ai/{remark}', 'RecorderProcessController@index_torque_ai');
Route::get('index/fetchResumeTorqueAi', 'RecorderProcessController@fetchResumeTorqueAi');
Route::post('index/push_block_recorder/create_torque_ai', 'RecorderProcessController@create_torque_ai');

//RECORDER PUSH PULL CHECK
Route::get('index/recorder_push_pull_check', 'RecorderProcessController@index_push_pull');
Route::get('push_pull/fetchResult', 'RecorderProcessController@fetchResultPushPull');
Route::get('push_pull/fetchResultCamera', 'RecorderProcessController@fetchResultCamera');
Route::get('post/display/email/{value_check}/{judgement}/{model}/{checked_at}/{pic_check}/{remark}', 'RecorderProcessController@email');
Route::post('push_pull/store_push_pull', 'RecorderProcessController@store_push_pull');
Route::post('camera_kango/store_camera_kango', 'RecorderProcessController@store_camera');
Route::post('camera_kango/store_camera_kango2', 'RecorderProcessController@store_camera2');
Route::get('scan/push_pull/operator', 'RecorderProcessController@scanPushPullOperator');
Route::get('index/recorder/resume_assy_rc', 'RecorderProcessController@index_resume_assy_rc');
Route::post('recorder/filter_assy_rc', 'RecorderProcessController@filter_assy_rc');
Route::get('index/recorder/rc_picking_result', 'RecorderProcessController@index_rc_picking_result');
Route::get('fetch/recorder/rc_picking_result', 'RecorderProcessController@fetch_rc_picking_result');

//RETURN MATERIAL RC
Route::get('index/recorder/return', 'RecorderProcessController@indexReturn');
Route::get('fetch/recorder/return/product', 'RecorderProcessController@fetchProductReturn');
Route::get('fetch/recorder/return/resume', 'RecorderProcessController@fetchProductResume');
Route::get('delete/recorder/return/resume', 'RecorderProcessController@deleteProductResume');
Route::post('input/recorder/return', 'RecorderProcessController@inputReturn');

//RECORDER CDM
Route::get('index/recorder/cdm', 'RecorderProcessController@indexCdm');
Route::get('fetch/recorder/product', 'RecorderProcessController@fetchProduct');
Route::get('fetch/cavity', 'RecorderProcessController@fetchCavity');
Route::post('input/recorder/cdm', 'RecorderProcessController@inputCdm');
Route::get('index/recorder/fetch_resume_cdm', 'RecorderProcessController@fetchResumeCdm');
Route::get('fetch/recorder/cdm', 'RecorderProcessController@fetchCdm');
Route::get('index/recorder/cdm_report', 'RecorderProcessController@indexCdmReport');
Route::get('fetch/recorder/cdm_report', 'RecorderProcessController@fetchCdmReport');

//INISIALISAS NG RATE ASSY RC
Route::get('index/recorder/kensa/initial', 'RecorderProcessController@indexKensaInitial');
Route::get('fetch/recorder/kensa/initial', 'RecorderProcessController@fetchKensaInitial');
Route::post('input/recorder/kensa/initial', 'RecorderProcessController@inputKensaInitial');
Route::post('input/recorder/kensa/initial/product', 'RecorderProcessController@inputKensaInitialProduct');

//KENSA KAKUNING ASSY RC
Route::get('index/recorder/kensa', 'RecorderProcessController@indexKensa');
Route::get('fetch/recorder/kensa', 'RecorderProcessController@fetchKensa');
Route::get('scan/recorder/kensa/operator', 'RecorderProcessController@scanKensaRecorderOperator');
Route::get('scan/recorder/kensa', 'RecorderProcessController@scanKensa');
Route::post('input/recorder/kensa_product', 'RecorderProcessController@inputKensaProduct');
Route::post('input/recorder/kensa', 'RecorderProcessController@inputKensa');
Route::post('update/recorder/kensa', 'RecorderProcessController@updateKensa');
Route::get('index/recorder/kensa_report', 'RecorderProcessController@indexKensaReport');
Route::get('fetch/recorder/kensa_report', 'RecorderProcessController@fetchKensaReport');

//KENSA KAKUNING DISPLAY ASSY RC
Route::get('index/recorder/display/kensa/{line}', 'RecorderProcessController@indexDisplayKensa');
Route::get('fetch/recorder/display/kensa', 'RecorderProcessController@fetchDisplayKensa');
Route::get('index/recorder/display/ng_kensa', 'RecorderProcessController@indexNgRateKensa');
Route::get('fetch/recorder/display/ng_kensa', 'RecorderProcessController@fetchNgKensa');
Route::get('index/recorder/display/ng_trend', 'RecorderProcessController@indexNgTrend');
Route::get('fetch/recorder/display/ng_trend', 'RecorderProcessController@fetchNgTrend');
Route::post('input/recorder/display/ng_trend', 'RecorderProcessController@inputNgTrend');
Route::get('fetch/recorder/display/detail_ng_trend', 'RecorderProcessController@fetchDetailNgTrend');
Route::get('index/recorder/display/ng_rate', 'RecorderProcessController@indexNgRate');
Route::get('fetch/recorder/display/ng_rate', 'RecorderProcessController@fetchNgRate');

Route::get('index/recorder/qa_audit', 'RecorderProcessController@indexQaAudit');
Route::get('fetch/recorder/qa_audit', 'RecorderProcessController@fetchQaAudit');
Route::get('scan/recorder/qa_audit', 'RecorderProcessController@scanQaAudit');
Route::post('input/recorder/qa_audit', 'RecorderProcessController@inputQaAudit');
Route::get('print/recorder/qa_audit/{type}/{id}', 'RecorderProcessController@printQaAudit');
Route::get('approve/recorder/qa_audit/{type}/{remark}/{id}', 'RecorderProcessController@approveQaAudit');
Route::post('reject/recorder/qa_audit/{id}', 'RecorderProcessController@rejectQaAudit');
Route::post('reject_chief/recorder/qa_audit/{id}', 'RecorderProcessController@rejectQaAuditChief');
Route::get('index/recorder/display/qa_audit', 'RecorderProcessController@indexDisplayQaAudit');
Route::get('fetch/recorder/display/qa_audit', 'RecorderProcessController@fetchDisplayQaAudit');
Route::get('input/recorder/training_document', 'RecorderProcessController@inputRecorderDocument');
Route::post('input/recorder/counceling', 'RecorderProcessController@inputRecorderCounceling');
Route::post('update/recorder/counceling', 'RecorderProcessController@updateRecorderCounceling');

Route::get('index/recorder/display/traceability', 'RecorderProcessController@indexDisplayTraceability');
Route::get('fetch/recorder/display/traceability', 'RecorderProcessController@fetchDisplayTraceability');

Route::get('index/recorder/display/parameter', 'RecorderProcessController@indexDisplayParameter');
Route::get('fetch/recorder/display/parameter', 'RecorderProcessController@fetchDisplayParameter');

Route::get('index/recorder/display/parameter/ng', 'RecorderProcessController@indexDisplayParameterNg');
Route::get('fetch/recorder/display/parameter/ng', 'RecorderProcessController@fetchDisplayParameterNg');

Route::get('input/recorder/ng_box', 'GeneralAttendanceController@inputNgBox');
Route::get('fetch/recorder/ng_box', 'RecorderProcessController@fetchNgBox');
Route::get('reset/recorder/ng_box', 'RecorderProcessController@resetNgBox');

Route::get('index/recorder/display/ng', 'RecorderProcessController@indexDisplayNg');
Route::get('fetch/recorder/display/ng', 'RecorderProcessController@fetchDisplayNg');

Route::get('index/recorder/display/ng/mesin', 'RecorderProcessController@indexDisplayNgMesin');
Route::get('fetch/recorder/display/ng/mesin', 'RecorderProcessController@fetchDisplayNgMesin');
// Route::get('input/recorder/general/ng_box', 'GeneralAttendanceController@inputNgBox');

//WEBCAM
Route::get('index/webcam', 'WebcamController@index');
Route::post('index/webcam/create', 'WebcamController@create');

Route::get('index/qa_cpar', 'QualityAssuranceController@index_cpar');

Route::group(['nav' => 'M21', 'middleware' => 'permission'], function () {

    //CPAR
    Route::get('index/qc_report', 'QcReportController@index');
    Route::get('index/qc_report/get_meeting', 'QcReportController@get_meeting');
    Route::post('index/qc_report/edit_meeting', 'QcReportController@edit_meeting');
    Route::get('index/qc_report/create', 'QcReportController@create');
    Route::post('index/qc_report/create_action', 'QcReportController@create_action');
    Route::get('index/qc_report/update/{id}', 'QcReportController@update');
    Route::post('index/qc_report/update_action/{id}', 'QcReportController@update_action');
    Route::post('index/qc_report/update_deskripsi/{id}', 'QcReportController@update_deskripsi');
    Route::get('index/qc_report/delete/{id}', 'QcReportController@delete');
    Route::post('index/qc_report/create_item', 'QcReportController@create_item');
    Route::get('index/qc_report/fetch_item/{id}', 'QcReportController@fetch_item');
    Route::post('index/qc_report/edit_item', 'QcReportController@edit_item');
    Route::get('index/qc_report/edit_item', 'QcReportController@fetch_item_edit');
    Route::get('index/qc_report/view_item', 'QcReportController@view_item');
    Route::post('index/qc_report/delete_item', 'QcReportController@delete_item');
    Route::post('index/qc_report/deletefiles', 'QcReportController@deletefiles');
    Route::get('index/qc_report/print_cpar/{id}', 'QcReportController@print_cpar');
    Route::get('index/qc_report/print_cpar_new/{id}', 'QcReportController@print_cpar_new');
    Route::get('index/qc_report/sendemail/{id}/{posisi}', 'QcReportController@sendemail');

    Route::get('index/qc_report/verifikasigm/{id}', 'QcReportController@verifikasigm');
    Route::get('index/qc_report/sign', 'QcReportController@sign');
    Route::post('index/qc_report/save_sign', 'QcReportController@save_sign');

    //verifikasi CPAR
    Route::get('index/qc_report/statuscpar/{id}', 'QcReportController@statuscpar');
    Route::get('index/qc_report/verifikasicpar/{id}', 'QcReportController@verifikasicpar');
    Route::post('index/qc_report/checked/{id}', 'QcReportController@checked');
    Route::post('index/qc_report/unchecked/{id}', 'QcReportController@unchecked');
    Route::post('index/qc_report/uncheckedqa/{id}', 'QcReportController@uncheckedqa');

    Route::get('index/qc_report/getdepartemen', 'QcReportController@getDepartemen')->name('admin.getDepartemen');

    //CAR
    Route::get('index/qc_car', 'QcCarController@index');
    Route::post('index/qc_car/filter', 'QcCarController@filter_data');
    Route::get('index/qc_car/detail/{id}', 'QcCarController@detail');
    Route::post('index/qc_car/create_pic/{id}', 'QcCarController@create_pic');
    Route::post('index/qc_car/detail_action/{id}', 'QcCarController@detail_action');
    Route::post('index/qc_car/delete_document', 'QcCarController@delete_document');
    Route::get('index/qc_car/print_car/{id}', 'QcCarController@print_car');
    Route::get('index/qc_car/print_car_new/{id}', 'QcCarController@print_car2');
    Route::get('index/qc_car/coba_print/{id}', 'QcCarController@coba_print');
    Route::get('index/qc_car/sendemail/{id}/{posisi}', 'QcCarController@sendemail');
    Route::post('index/qc_car/deletefiles', 'QcCarController@deletefiles');
    Route::get('index/qc_car/verifikasigm/{id}', 'QcCarController@verifikasigm');
    Route::post('index/qc_car/save_sign', 'QcCarController@save_sign');

    //Verifikator CAR
    Route::get('index/qc_car/verifikator', 'QcCarController@verifikator');

    //Verifikasi CAR
    Route::get('index/qc_car/verifikasicar/{id}', 'QcCarController@verifikasicar');
    Route::post('index/qc_car/checked/{id}', 'QcCarController@checked');
    Route::post('index/qc_car/unchecked/{id}', 'QcCarController@unchecked');
    Route::post('index/qc_car/uncheckedGM/{id}', 'QcCarController@uncheckedGM');

    //Verifikasi QA
    Route::get('index/qc_report/verifikasiqa/{id}', 'QcReportController@verifikasiqa');
    Route::post('index/qc_report/close1/{id}', 'QcReportController@close1');
    Route::get('index/qc_report/emailverification/{id}', 'QcReportController@emailverification');
    Route::post('index/qc_report/close2/{id}', 'QcReportController@close2');
    Route::post('index/qc_report/deleteVerifikasi', 'QcReportController@deleteVerifikasi');

    // Form Ketidaksesuaian YMMJ
    Route::get('index/qa_ymmj_index', 'QualityAssuranceController@index_ymmj');
    Route::get('index/qa_ymmj', 'QcYmmjController@index');
    Route::post('index/qa_ymmj/form', 'QcYmmjController@filter');
    Route::get('index/qa_ymmj/create', 'QcYmmjController@create');
    Route::post('index/qa_ymmj/create_action', 'QcYmmjController@create_action');
    Route::get('index/qa_ymmj/update/{id}', 'QcYmmjController@update');
    Route::post('index/qa_ymmj/update_action/{id}', 'QcYmmjController@update_action');
    Route::post('index/qa_ymmj/deletefiles', 'QcYmmjController@deletefiles');
    Route::get('index/qa_ymmj/grafik_ymmj', 'QcYmmjController@grafik_ymmj');
    Route::get('index/qa_ymmj/fetchGrafik', 'QcYmmjController@fetchGrafik');
    Route::get('index/qa_ymmj/fetchtable', 'QcYmmjController@fetchTable');
    Route::get('index/qa_ymmj/detail', 'QcYmmjController@detail');
    Route::get('index/qa_ymmj/print/{id}', 'QcYmmjController@print_ymmj');

    Route::post('post/qa_ymmj/file', 'QcYmmjController@post_quotation');

    Route::get('index/market_claim', 'QcReportController@indexMarketClaim');
    Route::post('index/market_claim/filter', 'QcReportController@filterMarketClaim');
    Route::post('index/market_claim/create', 'QcReportController@createMarketClaim');

});

Route::get('export/cpar/list', 'QcReportController@exportCPARAll');

//CPAR
Route::get('index/cpar/resume', 'QcReportController@resume');
Route::get('fetch/cpar/resume', 'QcReportController@getResumeData');

Route::get('index/qc_report/get_fiscal_year', 'QcReportController@get_fiscal');
Route::get('index/qc_report/get_nomor_depan', 'QcReportController@get_nomor_depan');
Route::get('index/qc_report/grafik_cpar', 'QcReportController@grafik_cpar');
Route::get('index/qc_report/grafik_kategori', 'QcReportController@grafik_kategori');
Route::get('index/qc_report/grafik_tanggungan', 'QcReportController@grafik_tanggungan');
Route::get('fetch/qc_report/grafik_tanggungan', 'QcReportController@fetch_tanggungan');
Route::get('index/qc_report/market_claim/{id}', 'QcReportController@market_claim');
Route::get('fetch/qc_report/market_claim', 'QcReportController@fetch_market_claim');
Route::get('index/qc_report/cpar_meeting', 'QcReportController@grafik_meeting');
Route::get('fetch/qc_report/cpar_meeting', 'QcReportController@fetch_grafik_meeting');
Route::get('index/qc_report/komplain_monitoring', 'QcReportController@komplain_monitoring');
Route::get('index/qc_report/komplain_monitoring2', 'QcReportController@komplain_monitoring2');
Route::get('index/qc_report/komplain_monitoring3', 'QcReportController@komplain_monitoring3');
Route::get('index/qc_report/komplain_monitoring4', 'QcReportController@komplain_monitoring4');
Route::get('index/qc_report/komplain_monitoring5', 'QcReportController@komplain_monitoring5');
Route::get('index/qc_report/fetchReport', 'QcReportController@fetchReport');
Route::get('index/qc_report/fetchKategori', 'QcReportController@fetchKategori');
Route::get('index/qc_report/fetchSource', 'QcReportController@fetchSource');
Route::get('index/qc_report/fetchEksternal', 'QcReportController@fetchEksternal');
Route::get('index/qc_report/fetchSupplier', 'QcReportController@fetchSupplier');
Route::get('index/qc_report/detail_cpar', 'QcReportController@detail_cpar');
Route::get('index/qc_report/detail_kategori', 'QcReportController@detail_kategori');
Route::get('index/qc_report/detail_cpar_dept', 'QcReportController@detail_cpar_dept');
Route::get('index/qc_report/detail_monitoring', 'QcReportController@detail_monitoring');
Route::post('index/qc_report/filter_cpar', 'QcReportController@filter_cpar');
Route::get('index/qc_report/get_detailmaterial', 'QcReportController@getmaterialsbymaterialsnumber')->name('admin.getmaterialsbymaterialsnumber');
Route::get('index/qc_report/fetchtable', 'QcReportController@fetchtable');
Route::get('index/qc_report/fetchMonitoring', 'QcReportController@fetchMonitoring');
Route::get('index/qc_report/fetchGantt', 'QcReportController@fetchGantt');

// Request CPAR QA

Route::get('index/request_qa', 'QcRequestController@index');
Route::get('index/request_qa/create', 'QcRequestController@create');
Route::post('index/request_qa/create_action', 'QcRequestController@create_action');
Route::post('index/request_qa/update_action/{id}', 'QcRequestController@update_action');
Route::get('index/request_qa/detail/{id}', 'QcRequestController@detail');
Route::get('index/request_qa/fetchDataTable', 'QcRequestController@fetchDataTable');
Route::get('index/request_qa/fetch_item/{id}', 'QcRequestController@fetch_item');
Route::post('index/request_qa/create_item', 'QcRequestController@create_item');
Route::post('index/request_qa/edit_item', 'QcRequestController@edit_item');
Route::post('index/request_qa/update_detail/{id}', 'QcRequestController@update_detail');
Route::get('index/request_qa/edit_item', 'QcRequestController@fetch_item_edit');
Route::post('index/request_qa/delete_item', 'QcRequestController@delete_item');
Route::get('index/request_qa/print/{id}', 'QcRequestController@print_report');
Route::post('index/request_qa/approval/{id}', 'QcRequestController@approval');
Route::get('index/request_qa/verifikasi/{id}', 'QcRequestController@verifikasi');

// CPAR Antar Departemen & Bagian
Route::get('index/form_ketidaksesuaian', 'CparController@index');
Route::get('index/form_ketidaksesuaian/fetchDataTable', 'CparController@fetchDataTable');
Route::get('index/form_ketidaksesuaian/create', 'CparController@create');
Route::post('post/form_ketidaksesuaian/create', 'CparController@post_create');
Route::get('index/form_ketidaksesuaian/detail/{id}', 'CparController@detail');
Route::get('index/form_ketidaksesuaian/delete/{id}', 'CparController@delete_form');
Route::get('index/form_ketidaksesuaian/fetch_item/{id}', 'CparController@fetch_item');
Route::post('index/form_ketidaksesuaian/create_item', 'CparController@create_item');
Route::get('index/form_ketidaksesuaian/edit_item', 'CparController@fetch_item_edit');
Route::post('index/form_ketidaksesuaian/edit_item', 'CparController@edit_item');
Route::post('index/form_ketidaksesuaian/delete_item', 'CparController@delete_item');
Route::post('index/form_ketidaksesuaian/update_detail/{id}', 'CparController@update_detail');
Route::get('index/form_ketidaksesuaian/print/{id}', 'CparController@print_report');

Route::get('index/form_ketidaksesuaian/get_detailmaterial', 'CparController@getmaterialsbympdl')->name('admin.getmaterialsbympdl');
// Verifikasi CPAR Departemen
Route::get('index/form_ketidaksesuaian/verifikasicpar/{id}', 'CparController@verifikasicpar');
Route::post('index/form_ketidaksesuaian/approval/{id}', 'CparController@approval');
Route::post('index/form_ketidaksesuaian/notapprove/{id}', 'CparController@notapprove');
Route::get('index/form_ketidaksesuaian/sendemail/{id}', 'CparController@sendemail');
Route::get('index/form_ketidaksesuaian/sendemailqa/{id}', 'CparController@sendemailqa');
// CAR Antar Departemen
Route::get('index/form_ketidaksesuaian/response/{id}', 'CparController@response');
Route::post('index/form_ketidaksesuaian/update_car/{id}', 'CparController@update_car');
// Verifikasi CAR Departemen & Bagian
Route::get('index/form_ketidaksesuaian/verifikasicar/{id}', 'CparController@verifikasicar');
Route::post('index/form_ketidaksesuaian/approvalcar/{id}', 'CparController@approvalcar');
Route::post('index/form_ketidaksesuaian/notapprovecar/{id}', 'CparController@notapprovecar');
Route::get('index/form_ketidaksesuaian/sendemailcar/{id}', 'CparController@sendemailcar');
// Verifikasi Bagian
Route::get('index/form_ketidaksesuaian/verifikasibagian/{id}', 'CparController@verifikasibagian');
Route::post('index/form_ketidaksesuaian/close', 'CparController@closecar');
Route::post('index/form_ketidaksesuaian/reject', 'CparController@rejectcar');
//Monitoring CPAR
Route::get('index/form_ketidaksesuaian/monitoring', 'CparController@monitoring');
Route::get('fetch/form_ketidaksesuaian/monitoring', 'CparController@fetchMonitoring');
Route::get('index/form_ketidaksesuaian/detail', 'CparController@detailMonitoring');
Route::get('index/form_ketidaksesuaian/table', 'CparController@fetchTable');
//approve or Reject CPAR By QA
Route::get('index/form_ketidaksesuaian/approveqa/{id}', 'CparController@approveqa');
Route::get('index/form_ketidaksesuaian/rejectqa/{id}', 'CparController@rejectqa');

//Audit All

Route::get('index/audit_data', 'CparController@audit_data');
Route::get('index/audit_data/fetch', 'CparController@fetch_audit_all');
Route::get('index/audit/print/{id}', 'CparController@print_audit_all');

Route::get('index/audit', 'CparController@audit_kanban');
Route::get('index/audit_internal_mis', 'CparController@audit_mis');
Route::get('index/audit/point_check', 'CparController@audit_point_check');
Route::get('index/audit/fetch_kategori_lokasi', 'CparController@fetchKategoriLokasiAudit');
Route::get('index/audit/fetch_hasil_audit', 'CparController@fetchHasilAuditAll');

Route::get('index/audit/cek_report', 'CparController@check_audit_report_all');
Route::get('index/audit/cek_report/{kategori}/{lokasi}/{auditor}/{tanggal}', 'CparController@check_audit_report_new_all');
Route::get('fetch/audit/cek_report', 'CparController@fetch_audit_report_all');

Route::get('index/audit/create/{id}', 'CparController@audit_create_checklist_all');
Route::post('post/audit/create', 'CparController@audit_post_create_checklist_all');
Route::get('index/audit/response/{id}', 'CparController@audit_response_all');
Route::post('index/audit/update_response/{id}', 'CparController@update_response_all');
Route::get('index/audit/sendemailpenanganan/{id}', 'CparController@sendemailpenanganan_all');

Route::get('index/audit_kanban/monitoring', 'CparController@monitoring_audit_kanban');
Route::get('fetch/audit_kanban/monitoring', 'CparController@fetch_monitoring_audit_kanban');
Route::get('index/audit_kanban/detail', 'CparController@detail_monitoring_audit_kanban');
Route::get('index/audit_kanban/table', 'CparController@fetch_table_audit_kanban');

// 5S Patrol
Route::get('index/patrol', 'AuditController@index');
Route::get('fetch/patrol', 'AuditController@fetch_patrol');
Route::get('index/audit_internal', 'AuditController@index_audit');
Route::get('index/audit_patrol', 'AuditController@index_patrol');
Route::get('index/audit_patrol_std', 'AuditController@index_std');
Route::get('fetch/audit_patrol', 'AuditController@fetch_audit');
Route::post('post/audit_patrol', 'AuditController@post_audit');
Route::post('post/audit_patrol_file', 'AuditController@post_audit_file');

Route::get('index/audit_patrol_daily', 'AuditController@index_patrol_daily');
Route::get('index/audit_patrol_covid', 'AuditController@index_patrol_covid');
Route::get('index/audit_patrol_outside', 'AuditController@index_patrol_outside');
Route::get('index/audit_patrol_energy', 'AuditController@index_patrol_energy');
Route::get('index/audit_patrol_washing', 'AuditController@index_patrol_washing');
Route::get('index/audit_patrol_hrga', 'AuditController@index_patrol_hrga');
Route::get('index/audit_patrol_vendor', 'AuditController@index_patrol_vendor');

Route::get('index/audit_patrol_stocktaking', 'AuditController@index_audit_stocktaking');
Route::post('post/audit_patrol_stocktaking', 'AuditController@post_audit_stocktaking');

// Route::get('index/audit_patrol_mis', 'AuditController@index_mis');
Route::get('index/audit_patrol_mis', 'AuditController@index_audit_mis');

Route::get('index/audit_patrol/monitoring', 'AuditController@indexMonitoring');
Route::get('fetch/audit_patrol/monitoring', 'AuditController@fetchMonitoring');
Route::get('index/audit_patrol/detail', 'AuditController@detailMonitoring');
Route::get('index/audit_patrol/detail_category', 'AuditController@detailMonitoringCategory');
Route::get('index/audit_patrol/detail_bulan', 'AuditController@detailMonitoringBulan');
Route::get('index/audit_patrol/detail_type', 'AuditController@detailMonitoringType');
Route::get('index/audit_patrol/table', 'AuditController@fetchTable_audit');
Route::get('index/audit_patrol/detail_penanganan', 'AuditController@detailPenanganan');
Route::post('post/audit_patrol/penanganan', 'AuditController@postPenanganan');
Route::post('post/audit_patrol/penanganan_new', 'AuditController@postPenangananNew');
Route::post('post/audit_patrol/edit', 'AuditController@editAudit');

Route::get('index/audit_patrol_monitoring/{id}', 'AuditController@indexMonitoringAll');
Route::get('fetch/audit_patrol_monitoring/all', 'AuditController@fetchMonitoringAll');
Route::get('index/audit_patrol_monitoring_detail', 'AuditController@detailMonitoringAll');
Route::get('index/audit_patrol_monitoring_detail_bulan', 'AuditController@detailMonitoringBulanAll');
Route::get('index/audit_patrol_monitoring_table', 'AuditController@fetchTableAuditAll');

Route::get('index/patrol_resume/{id}', 'AuditController@indexPatrolResume');
Route::get('fetch/patrol_resume', 'AuditController@fetchPatrolResume');
Route::get('fetch/patrol_resume/detail', 'AuditController@detailPatrolResume');
Route::get('fetch/patrol_resume/detail_lokasi', 'AuditController@detailLokasiPatrolResume');
Route::get('fetch/patrol_resume/export', 'AuditController@ExportMonthlyPatrolResume');

Route::get('export/patrol/list', 'AuditController@exportPatrol');
Route::get('export/patrol_all/list', 'AuditController@exportPatrolAll');

//Audit Internal ISO
Route::get('index/audit_iso', 'CparController@audit');
Route::get('index/audit_iso/fetchDataTable', 'CparController@fetchDataAudit');
Route::get('index/audit_iso/create', 'CparController@audit_create');
Route::post('post/audit_iso/create', 'CparController@audit_post_create');
Route::get('index/audit_iso/get_nama', 'CparController@audit_get_nama');
Route::get('index/audit_iso/get_nomor_depan', 'CparController@audit_get_nomor');
Route::get('index/audit_iso/detail/{id}', 'CparController@audit_detail');
Route::post('post/audit_iso/detail', 'CparController@audit_post_detail');
Route::post('post/audit_iso/detail_last', 'CparController@audit_post_detail_last');
Route::get('index/audit_iso/verifikasistd/{id}', 'CparController@verifikasistd');
Route::post('index/audit_iso/approval/{id}', 'CparController@std_approval');
Route::post('index/audit_iso/comment/{id}', 'CparController@std_comment');
Route::post('index/audit_iso/reject/{id}', 'CparController@std_reject');
Route::get('index/audit_iso/response/{id}', 'CparController@std_response');
Route::post('index/audit_iso/update_response/{id}', 'CparController@update_response');
Route::get('index/audit_iso/sendemail', 'CparController@send_email_audit');
Route::get('index/audit_iso/sendemailpenanganan/{id}', 'CparController@sendemailpenanganan');
Route::get('index/audit_iso/print/{id}', 'CparController@print_audit');

Route::get('index/audit_iso/monitoring', 'CparController@monitoring_audit');
Route::get('fetch/audit_iso/monitoring', 'CparController@fetchMonitoring_audit');
Route::get('index/audit_iso/detail', 'CparController@detailMonitoring_audit');
Route::get('index/audit_iso/detail_kategori', 'CparController@detailMonitoring_audit_kategori');
Route::get('index/audit_iso/detail_klausul', 'CparController@detailMonitoring_audit_klausul');
Route::get('index/audit_iso/table', 'CparController@fetchTable_audit');

Route::get('index/audit_iso/monitoring2', 'CparController@monitoring_audit2');
Route::get('fetch/audit_iso/monitoring2', 'CparController@fetchMonitoring_audit2');
Route::get('index/audit_iso/detail2', 'CparController@detailMonitoring_audit2');
Route::get('index/audit_iso/detail_kategori2', 'CparController@detailMonitoring_audit_kategori2');
Route::get('index/audit_iso/detail_klausul2', 'CparController@detailMonitoring_audit_klausul2');
Route::get('index/audit_iso/table2', 'CparController@fetchTable_audit2');

//checklist
Route::get('index/audit_iso/check', 'CparController@check_audit');
Route::get('index/audit_iso/point_check/{kategori}/{lokasi}', 'CparController@indexPointCheck');
Route::get('index/audit_iso/fetch_kategori_lokasi', 'CparController@fetchKategoriLokasi');
Route::get('index/audit_iso/fetch_hasil_audit', 'CparController@fetchHasilAudit');
Route::get('index/audit_iso/destroy_point_check/{id}/{kategori}/{lokasi}', 'CparController@destroyPointCheck');
Route::get('index/audit_iso/create_audit', 'CparController@check_audit_create');
Route::get('fetch/audit_iso/create_audit', 'CparController@fetch_audit_create');
Route::post('input/audit_iso/point_check', 'CparController@inputPointCheck');
Route::get('fetch/audit_iso/get_point_check', 'CparController@getPointCheck');
Route::post('update/audit_iso/point_check', 'CparController@updatePointCheck');
Route::post('input/audit_iso/create_audit', 'CparController@inputAuditIso');

Route::get('index/audit_iso/fetch_point_audit', 'CparController@fetchPointAudit');
Route::get('index/audit_iso/report_point_audit', 'CparController@ReportHasilAudit');

Route::get('index/audit_iso/cek_report', 'CparController@check_audit_report');
Route::get('index/audit_iso/cek_report/{kategori}/{lokasi}/{auditor}/{tanggal}', 'CparController@check_audit_report_new');
Route::get('fetch/audit_iso/cek_report', 'CparController@fetch_audit_report');

//Audit AEO
Route::get('index/audit_aeo/point_check', 'CparController@indexPointCheckAeo');
Route::get('index/audit_aeo/hasil_audit', 'CparController@fetchHasilAuditAeo');
Route::get('index/audit_aeo/report/{kategori}/{lokasi}/{auditor}/{tanggal}', 'CparController@check_audit_aeo_report');
Route::get('fetch/audit_aeo/report', 'CparController@fetch_audit_aeo_report');
Route::get('index/audit_aeo', 'CparController@AuditAeo');
Route::get('fetch/audit_aeo', 'CparController@fetchAuditAeo');
Route::post('input/audit_aeo', 'CparController@inputAuditAeo');
Route::get('index/audit_aeo/monitoring', 'CparController@check_audit_create');
Route::post('post/audit_aeo/jawaban', 'CparController@postJawabanAeo');

//Form Laporan Ketidaksesuaian

Route::get('index/audit_iso/create/{id}', 'CparController@audit_create_checklist');
Route::post('post/audit_iso_checklist/create', 'CparController@audit_post_create_checklist');

//CUBEACON WAREHOUSE
Route::get('mqtt/publish/{topic}/{message}', 'TrialController@SendMsgViaMqtt');
Route::get('mqtt/publish/{topic}', 'TrialController@SubscribetoTopic');
Route::get('index/beacon', 'BeaconController@index');
Route::get('fetch/user/beacon', 'BeaconController@getUser');
Route::get('index/master_beacon', 'BeaconController@master_beacon');
Route::post('index/master_beacon/daftar', 'BeaconController@daftar');
Route::get('index/master_beacon/edit', 'BeaconController@edit')->name('admin.beaconedit');
Route::get('index/master_beacon/delete/{id}', 'BeaconController@delete');

//CUBEACON REEDPLATE
Route::get('index/reedplate/map', 'ReedplateController@index');
Route::get('index/reedplate/working_time', 'ReedplateController@reed');
Route::get('fetch/reedplate/user', 'ReedplateController@getUser');
Route::get('fetch/reedplate/log', 'ReedplateController@fetch_log');
Route::post('index/reedplate/reader', 'ReedplateController@inputTemp');

//DRIVER MANAGER
Route::get('index/driver_manager', 'ReedplateController@driver');
Route::get('fetch/driver_manager', 'ReedplateController@fetchDriver');

//TEMPERATURE / SUHU
Route::get('index/grafikServer', 'TemperatureController@grafikServer');
Route::get('index/log_map_server', 'TemperatureController@log_map_server');
Route::get('index/data_suhu_server', 'TemperatureController@data_suhu_server');
Route::get('index/index_map', 'TemperatureController@index_map');
Route::get('index/grafikOffice', 'TemperatureController@grafikOffice');
Route::get('index/data_suhu_office', 'TemperatureController@data_suhu_office');
Route::get('index/log_map_office', 'TemperatureController@log_map_office');
Route::get('index/standart_temperature', 'TemperatureController@standart');
Route::get('index/temperature/edit', 'TemperatureController@edit')->name('admin.temperaturedit');
Route::post('index/temperature/aksi_edit', 'TemperatureController@aksi_edit');
Route::get('index/temperature/delete/{id}', 'TemperatureController@delete');

// BUFFING TOILET
Route::get('index/toilet', 'RoomController@indexToilet');
Route::get('index/room/toilet/{id}', 'RoomController@indexRoomToilet');
Route::get('fetch/room/toilet', 'RoomController@fetchToilet');

//PRESS
Route::get('index/press/create', 'PressController@create');
Route::get('index/press/fl', 'PressController@index_fl');
Route::get('index/press/cl', 'PressController@index_cl');
Route::get('index/press/vn', 'PressController@index_vn');
Route::get('scan/press/operator', 'PressController@scanPressOperator');
Route::get('fetch/press/press_list', 'PressController@fetchPressList');
Route::get('fetch/press/trouble_list', 'PressController@fetchTroubleList');
Route::get('fetch/press/fetchMaterialList', 'PressController@fetchMaterialList');
Route::get('fetch/press/fetchPunch', 'PressController@fetchPunch');
Route::get('fetch/press/fetchDie', 'PressController@fetchDie');
Route::get('fetch/press/fetchPlate', 'PressController@fetchPlate');
Route::get('fetch/press/fetchPpl', 'PressController@fetchPpl');
Route::get('fetch/press/fetchDp', 'PressController@fetchDp');
Route::get('fetch/press/fetchDd', 'PressController@fetchDd');
Route::get('fetch/press/fetchSnap', 'PressController@fetchSnap');
Route::get('fetch/press/fetchLower', 'PressController@fetchLower');
Route::get('fetch/press/fetchUpper', 'PressController@fetchUpper');
Route::get('fetch/press/fetchHalf', 'PressController@fetchHalf');
Route::get('fetch/press/fetchdinsert', 'PressController@fetchdinsert');
Route::get('fetch/press/fetchProcess', 'PressController@fetchProcess');
Route::get('fetch/press/fetchMachine', 'PressController@fetchMachine');
Route::post('input/press/setup_molding', 'PressController@inputSetupMolding');
Route::post('index/press/store', 'PressController@store');
Route::post('index/press/store_fl', 'PressController@store_fl');
Route::post('index/press/store_cl', 'PressController@store_cl');
Route::post('index/press/store_vn', 'PressController@store_vn');
Route::post('index/press/store_kanagata', 'PressController@store_kanagata');
Route::post('index/press/store_trouble', 'PressController@store_trouble');
Route::post('index/press/finish_trouble', 'PressController@finish_trouble');
Route::post('input/press/kanagata_lifetime', 'PressController@create_kanagata_lifetime');
Route::get('fetch/press/kanagata', 'PressController@fetchKanagata');
Route::get('excel/press/kanagata_last_data', 'PressController@excelKanagataLastData');

Route::get('index/press/transaction', 'PressController@indexTransaction');
Route::post('input/press/transaction', 'PressController@inputTransaction');
Route::get('fetch/press/transaction', 'PressController@fetchTransaction');

Route::get('index/press/transaction_report', 'PressController@indexTransactionReport');
Route::get('fetch/press/transaction_report', 'PressController@fetchTransactionReport');

Route::get('index/press/maintenance', 'PressController@indexMaintenance');
Route::get('scan/press/maintenance', 'PressController@scanMaintenance');
Route::get('fetch/press/maintenance', 'PressController@fetchMaintenance');
Route::post('input/press/maintenance/temp', 'PressController@inputMaintenanceTemp');
Route::post('input/press/maintenance', 'PressController@inputMaintenance');
//Display Press
Route::get('index/press/monitoring', 'PressController@monitoring');
Route::get('fetch/press/monitoring', 'PressController@fetchMonitoring');
Route::get('index/press/detail_press', 'PressController@detail_press');
Route::get('index/press/detail_pic', 'PressController@detail_pic');
Route::get('index/press/monitoring2', 'PressController@monitoring2');

Route::get('index/press/kanagata_lifetime', 'PressController@indexKanagataLifetime');
Route::get('fetch/press/kanagata_lifetime', 'PressController@fetchKanagataLifetime');
//Report Press
Route::get('index/press/report_trouble', 'PressController@report_trouble');
Route::post('index/press/filter_report_trouble', 'PressController@filter_report_trouble');
Route::get('index/press/report_prod_result', 'PressController@report_prod_result');
Route::post('index/press/filter_report_prod_result', 'PressController@filter_report_prod_result');
Route::get('index/press/report_kanagata_lifetime', 'PressController@report_kanagata_lifetime');
Route::post('index/press/filter_report_kanagata_lifetime', 'PressController@filter_report_kanagata_lifetime');
Route::get('index/kanagata_lifetime/getkanagatalifetime', 'PressController@getkanagatalifetime')->name('kanagata_lifetime.getkanagatalifetime');
Route::post('index/kanagata/update/{id}', 'PressController@updateKanagataLifetime');
Route::post('index/kanagata/reset', 'PressController@reset');
Route::post('index/kanagata/reset/periodik', 'PressController@resetPeriodik');
Route::get('index/prod_result/getprodresult', 'PressController@getprodresult')->name('prod_result.getprodresult');
Route::post('index/prod_result/update/{id}', 'PressController@updateProdResult');
Route::get('index/prod_result/delete', 'PressController@deleteProdResult');

//Master Kanagata
Route::get('index/press/master_kanagata', 'PressController@indexMasterKanagata');
Route::get('fetch/press/master_kanagata', 'PressController@fetchMasterKanagata');
Route::post('post/press/add_kanagata', 'PressController@addKanagata');
Route::get('index/press/destroy_kanagata/{id}', 'PressController@destroyKanagata');
Route::get('fetch/press/get_kanagata', 'PressController@getKanagata');
Route::post('post/press/update_kanagata', 'PressController@updateKanagata');

//Form Failure
Route::get('index/form_experience', 'FormExperienceController@index');
Route::post('index/form_experience/filter', 'FormExperienceController@filter_form');
Route::get('index/form_experience/create', 'FormExperienceController@create');
Route::post('index/post/form_experience', 'FormExperienceController@post_form');
Route::get('index/form_experience/edit/{id}', 'FormExperienceController@update');
Route::post('index/update/form_experience', 'FormExperienceController@update_form');
Route::get('index/form_experience/print/{id}', 'FormExperienceController@print_form');
Route::get('index/form_experience/get_nama', 'FormExperienceController@get_nik');

Route::get('fetch/form_experience/chart', 'FormExperienceController@fetchChart');
Route::get('fetch/form_experience/detail_chart', 'FormExperienceController@fetchDetailChart');

Route::get('fetch/form_experience/attendance', 'FormExperienceController@fetchAttendance');
Route::post('scan/form_experience/attendance', 'FormExperienceController@scanEmployee');

//IP
Route::group(['nav' => 'S40', 'middleware' => 'permission'], function () {
    Route::get('index/display/ip', 'PingController@indexIpMonitoring');
    Route::get('fetch/display/ip', 'PingController@fetch');
    Route::get('fetch/display/fetch_hit/{ip}', 'PingController@fetch_hit');
    Route::post('post/display/ip_log', 'PingController@ip_log');
});

//OFFICECLOCK
Route::get('index/display/office_clock', 'OfficeClockController@index');
Route::get('index/display/office_clock/kosongan', 'OfficeClockController@kosongan');
Route::get('fetch/office_clock/visitor', 'OfficeClockController@fetchVisitor');
Route::get('index/display/office_clock2', 'OfficeClockController@index2');
Route::get('fetch/office_clock/visitor2', 'OfficeClockController@fetchVisitor2');
Route::get('index/display/office_clock3', 'OfficeClockController@index3');
Route::get('fetch/office_clock/visitor3', 'OfficeClockController@fetchVisitor3');
Route::get('index/display/office_clock4', 'OfficeClockController@index4');
Route::get('fetch/office_clock/visitor4', 'OfficeClockController@fetchVisitor4');
Route::get('index/display/office_clock5', 'OfficeClockController@index5');
Route::get('fetch/office_clock/visitor5', 'OfficeClockController@fetchVisitor5');
Route::get('index/display/guest_room', 'OfficeClockController@guest_room');
Route::get('index/display/guest_room2', 'OfficeClockController@guest_room2');
Route::get('fetch/office_clock/weather', 'OfficeClockController@fetchWeather');
Route::get('fetch/office_clock/batch', 'OfficeClockController@fetchBatch');

//MAINTENANCE

Route::get('index/maintenance/spk_monitoring', 'MaintenanceController@indexSPKMonitoring');
Route::get('index/maintenance/machine_monitoring', 'MaintenanceController@indexMachineMonitoring');
Route::get('index/maintenance/planned_monitoring', 'MaintenanceController@indexPlanMonitoring');

Route::get('fetch/maintenance/list_pm', 'MaintenanceController@fetchPM');
Route::get('fetch/maintenance/list_mc', 'MaintenanceController@fetchMachine');

Route::get('index/maintenance/pic/{category}', 'MaintenanceController@indexPIC');
Route::get('fetch/maintenance/pic', 'MaintenanceController@fetchMaintenanePic');

Route::get('index/maintenance/list/user', 'MaintenanceController@indexMaintenanceForm');
Route::get('fetch/maintenance/list_spk/user', 'MaintenanceController@fetchMaintenance');
Route::post('create/maintenance/spk', 'MaintenanceController@createSPK');
Route::post('edit/maintenance/spk', 'MaintenanceController@editSPK');
Route::get('fetch/maintenance/detail', 'MaintenanceController@fetchMaintenanceDetail');
Route::get('index/maintenance/spk/grafik', 'MaintenanceController@indexSPKGrafik');
Route::get('index/maintenance/spk/workload', 'MaintenanceController@indexSPKOperator');
Route::get('fetch/maintenance/spk/workload', 'MaintenanceController@fetchSPKOperatorWorkload');
Route::get('export/maintenance/list_spk', 'MaintenanceController@exportSPKList');

Route::get('verify/maintenance/spk/approve_urgent', 'MaintenanceController@verifySPK');
Route::get('verify/maintenance/spk/danger_note/{order_no}', 'MaintenanceController@indexDangerNote');

Route::post('verify/maintenance/spk/danger_note', 'MaintenanceController@addDangerNote');

Route::get('index/report/urgent_monitoring', 'MaintenanceController@indexSPKUrgentReport');
Route::get('fetch/maintenance/spk/monitoring/urgent', 'MaintenanceController@fetchSPKUrgentReport');

Route::get('index/maintenance/spk/monitoring', 'MaintenanceController@indexMaintenanceMonitoring');
Route::get('fetch/maintenance/spk/monitoring', 'MaintenanceController@fetchSPKProgress');
Route::get('fetch/maintenance/spk/monitoring/detail', 'MaintenanceController@fetchSPKProgressDetail');

Route::get('index/maintenance/spk/weekly', 'MaintenanceController@indexSPKWeekly');
Route::get('fetch/maintenance/spk/weekly_report', 'MaintenanceController@fetchSPKWeekly');

// ---------------------      MACHINE LOG      ------------------
Route::get('index/maintenance/machine/log', 'MaintenanceController@indexMachineHistory');
Route::get('fetch/maintenance/machine/history', 'MaintenanceController@fetchMachineHistory');
Route::post('post/maintenance/machine/history', 'MaintenanceController@postMachineHistory');

Route::get('index/maintenance/machine/part_list', 'MaintenanceController@indexMachinePartList');
Route::get('fetch/maintenance/machine/part_list', 'MaintenanceController@fetchMachinePartList');
Route::post('post/maintenance/machine/part_list', 'MaintenanceController@postMachinePartList');

Route::get('index/maintenance/machine/part_graph', 'MaintenanceController@indexMachinePartGraph');
Route::get('fetch/maintenance/machine/part_graph', 'MaintenanceController@fetchMachinePartGraph');

Route::post('update/maintenance/spk', 'MaintenanceController@updateSPK');

Route::group(['nav' => 'S34', 'middleware' => 'permission'], function () {
    Route::get('index/maintenance/spk/operator', 'MaintenanceController@indexOperatorMonitoring');

    Route::get('index/maintenance/list_spk', 'MaintenanceController@indexMaintenanceList');
    Route::get('fetch/maintenance/list_spk', 'MaintenanceController@fetchMaintenanceList');

    Route::get('fetch/maintenance/spk/operator', 'MaintenanceController@fetchSPKOperator');

    Route::post('post/maintenance/member', 'MaintenanceController@postMemberSPK');

    Route::post('post/maintenance/member/change', 'MaintenanceController@postNewMemberSPK');

    Route::post('post/maintenance/spk/open', 'MaintenanceController@openSPKPending');

    Route::get('index/maintenance/aparTool', 'MaintenanceController@indexAparTool');
    Route::post('post/maintenance/apar/insert', 'MaintenanceController@createTool');
    Route::post('post/maintenance/apar/update', 'MaintenanceController@updateTool');

    Route::get('index/maintenance/apar/orderList', 'MaintenanceController@indexAparOrderList');
    Route::post('post/maintenance/apar/order', 'MaintenanceController@apar_order');
    // Route::get('fetch/maintenance/spk/inv', 'MaintenanceController@fetchSPKPart');
});

Route::post('post/maintenance/spk/cancel', 'MaintenanceController@cancelSPK');
Route::post('post/maintenance/spk/scan_area', 'MaintenanceController@checkAreaSPK');

Route::group(['nav' => 'S47', 'middleware' => 'permission'], function () {
    Route::get('index/maintenance/spk', 'MaintenanceController@indexSPK');
    Route::get('fetch/maintenance/spk', 'MaintenanceController@fetchSPK');

    Route::get('work/maintenance/spk', 'MaintenanceController@startSPK');
    Route::get('rework/maintenance/spk', 'MaintenanceController@restartSPK');

    Route::post('report/maintenance/spk', 'MaintenanceController@reportingSPK');
    Route::post('report/maintenance/spk/pending', 'MaintenanceController@reportingSPKPending');
    Route::post('report/maintenance/spk/jeda', 'MaintenanceController@reportingSPKPause');
    Route::post('post/maintenance/spk/receipt', 'MaintenanceController@receiptSPK');
    Route::post('post/maintenance/spk/pending/vendor/action', 'MaintenanceController@closePendingVendor');
    Route::post('post/maintenance/spk/reject', 'MaintenanceController@rejectSPK');

    // -----------  APAR -----------
    Route::get('index/maintenance/apar', 'MaintenanceController@indexApar');
    Route::get('index/maintenance/aparCheck', 'MaintenanceController@indexAparCheck');
    Route::get('index/maintenance/apar/expire', 'MaintenanceController@indexAparExpire');
    Route::get('index/maintenance/apar/resume', 'MaintenanceController@indexAparResume');
    Route::get('index/maintenance/apar/uses', 'MaintenanceController@indexAparUses');
    Route::get('index/maintenance/apar/ng_list', 'MaintenanceController@indexAparNG');
    Route::get('index/maintenance/apar/map', 'MaintenanceController@indexAparMap');

    Route::get('fetch/maintenance/apar/list', 'MaintenanceController@fetchAparList');
    Route::get('fetch/maintenance/apar/history', 'MaintenanceController@fetchAparCheck');
    Route::get('fetch/maintenance/apar/expire', 'MaintenanceController@fetchAparExpire');
    Route::get('fetch/maintenance/apar/nglist', 'MaintenanceController@fetchAparNG');

    Route::get('fetch/maintenance/apar/list/check', 'MaintenanceController@fetchAparCheck2');
    Route::get('fetch/maintenance/apar/list/monitoring', 'MaintenanceController@fetch_apar_monitoring');
    Route::get('fetch/maintenance/hydrant/list/monitoring', 'MaintenanceController@fetch_hydrant_monitoring');
    Route::get('fetch/maintenance/apar/resume', 'MaintenanceController@fetch_apar_resume');
    Route::get('fetch/maintenance/apar/resumeWeek', 'MaintenanceController@fetch_apar_resume_week');
    Route::get('fetch/maintenance/apar/resume/detail/week', 'MaintenanceController@fetch_apar_resume_detail_week');
    Route::get('fetch/maintenance/apar/resume/detail', 'MaintenanceController@fetch_apar_resume_detail');

    Route::get('fetch/maintenance/apar/use/list', 'MaintenanceController@fetch_apar_use');
    Route::get('fetch/maintenance/apar/use/check', 'MaintenanceController@check_apar_use');

    Route::post('post/maintenance/apar/check', 'MaintenanceController@postCheck');
    Route::post('post/maintenance/apar/replace', 'MaintenanceController@replaceTool');
    Route::post('use/maintenance/apar', 'MaintenanceController@check_apar_use');
    Route::post('delete/maintenance/apar/history', 'MaintenanceController@delete_history');

    Route::get('print/apar/qr/{apar_id}/{apar_name}/{exp_date}/{last_check}/{last_check2}/{hasil_check}/{remark}', 'MaintenanceController@print_apar2');

    Route::get('fetch/maintenance/pm/history', 'MaintenanceController@getHistoryPlanned');
});

// ------------------ INVENTORY / SPARE PART ------------------

Route::get('index/maintenance/inven/list', 'MaintenanceController@indexInventory');
Route::get('fetch/maintenance/inven/list', 'MaintenanceController@fetchInventory');
Route::get('fetch/maintenance/inven/list/item', 'MaintenanceController@fetchInventoryPart');

Route::post('post/maintenance/inven/list/save', 'MaintenanceController@inventory_save');
Route::post('post/maintenance/inven/list/edit', 'MaintenanceController@inventory_edit');

Route::get('index/maintenance/inventory/{stat}', 'MaintenanceController@indexInventoryTransaction');
Route::get('fetch/maintenance/inven/code', 'MaintenanceController@fetchPartbyCode');
Route::post('post/maintenance/inven/code', 'MaintenanceController@postInventory');

Route::post('post/maintenance/inven/transaction', 'MaintenanceController@postInventoryStock');

Route::get('get/maintenance/inven/history', 'MaintenanceController@fetchSparePartHistory');

// -------------------------- PLANNED MAINTENANCE -----------------------

Route::get('index/maintenance/planned/form', 'MaintenanceController@indexPlannedForm');
// Route::get('index/maintenance/planned_monitor/{tgl}', 'MaintenanceController@indexPlanned');
Route::get('index/maintenance/planned/master', 'MaintenanceController@indexPlanMaster');
Route::post('import/maintenance/planned', 'MaintenanceController@importPM');
Route::get('fetch/maintenance/plan/checkList', 'MaintenanceController@fetchItemCheckList');

Route::post('post/maintenance/pm/check', 'MaintenanceController@postPlannedCheck');
Route::post('post/maintenance/pm/ng', 'MaintenanceController@postPlannedNotGood');
Route::get('get/maintenance/pm/ng', 'MaintenanceController@getPlannedNotGood');

Route::post('post/maintenance/pm/session', 'MaintenanceController@setSessionPlanned');
Route::get('get/maintenance/pm/session', 'MaintenanceController@getSessionPlanned');

Route::get('index/maintenance/pm/monitoring', 'MaintenanceController@indexPlannedMonitoring');
Route::get('index/maintenance/pm/schedule', 'MaintenanceController@indexPlannedSchedule');
Route::get('fetch/maintenance/pm/schedule', 'MaintenanceController@getPlannedSchedule');
Route::get('fetch/maintenance/pm/schedule/detail', 'MaintenanceController@getPlannedScheduleDetail');

Route::get('index/maintenance/pm/trendline', 'MaintenanceController@indexPlannedTrendline');
Route::get('fetch/maintenance/pm/trendline', 'MaintenanceController@fetchPlannedTrendline');

Route::get('index/maintenance/pm/resume', 'MaintenanceController@indexPlannedResume');

Route::get('index/maintenance/pm/finding', 'MaintenanceController@indexPlannedFinding');
Route::get('index/maintenance/pm/temp', 'MaintenanceController@indexPlannedTemp');
Route::get('fetch/maintenance/pm/finding', 'MaintenanceController@fetchPlannedFinding');
Route::get('fetch/maintenance/pm/finding/ByChart', 'MaintenanceController@fetchPlannedFindingbyChart');
Route::get('fetch/maintenance/pm/finding/byId', 'MaintenanceController@fetchPlannedFindingbyId');
Route::post('post/maintenance/pm/finding', 'MaintenanceController@postPlannedFinding');
Route::post('upload/maintenance/pm/finding', 'MaintenanceController@uploadPlannedFinding');

// ------------------------  OPERATOR POSITION  ----------------------
Route::get('index/maintenance/operator/position', 'MaintenanceController@indexOperatorPosition');
Route::get('fetch/maintenance/operator/position', 'MaintenanceController@fetchOperatorPosition');
Route::post('post/maintenance/operator/position', 'MaintenanceController@postOperatorPosition');

Route::get('index/maintenance/operator/workload', 'MaintenanceController@indexOperatorWorkload');
Route::get('fetch/maintenance/operator/workload', 'MaintenanceController@fetchOperatorWorkload');

Route::get('index/maintenance/operator', 'MaintenanceController@indexOperator');

// -------------------------      MTTBF         -------------------
Route::get('index/maintenance/machine_report/list', 'MaintenanceController@indexMttbf');
Route::get('index/maintenance/mttbf/report', 'MaintenanceController@indexMttbfReport');
Route::get('fetch/maintenance/mttbf/list', 'MaintenanceController@fetchMttbf');

Route::get('index/maintenance/machine_report/graph', 'MaintenanceController@indexMachineGraph');
Route::get('fetch/maintenance/machine_report/graph', 'MaintenanceController@fetchMachineBreakdownGraph');

// -------------------------      MTTR           -------------------
// Route::get('index/maintenance/mttr/list', 'MaintenanceController@indexMttr');
Route::get('fetch/maintenance/mttr/list', 'MaintenanceController@fetchMttr');

// -------------------------      MTTR           -------------------
Route::get('index/maintenance/tpm/dashboard', 'MaintenanceController@indextpm');
Route::get('index/maintenance/tpm/pressure', 'MaintenanceController@PressureControl');
Route::get('fetch/maintenance/tpm/pressure', 'MaintenanceController@fetchPressureControl');

Route::get('input/panel', 'MaintenanceController@inputMachinePower');

// ---------------------      V-BELT         ----------------------
Route::get('index/maintenance/tbm/{category}', 'MaintenanceController@indexTbm');
Route::get('fetch/maintenance/tbm', 'MaintenanceController@fetchTbm');

Route::get('index/maintenance/point_check/tbm/{category}/{mp_ut}', 'MaintenanceController@indexTbmPointCheck');
Route::get('fetch/maintenance/point_check/tbm/{category}', 'MaintenanceController@fetchTbmPointCheck');
Route::post('input/maintenance/point_check/tbm/{category}', 'MaintenanceController@inputTbmPointCheck');
Route::post('update/maintenance/point_check/tbm/{category}', 'MaintenanceController@updateTbmPointCheck');
Route::get('delete/maintenance/point_check/tbm/{category}', 'MaintenanceController@deleteTbmPointCheck');

Route::post('input/maintenance/tbm/schedule', 'MaintenanceController@inputTbmSchedule');
Route::get('delete/maintenance/tbm/schedule', 'MaintenanceController@deleteTbmSchedule');
Route::post('input/maintenance/tbm/doing', 'MaintenanceController@inputTbmDoing');
Route::get('fetch/maintenance/tbm/schedule', 'MaintenanceController@fetchTbmSchedule');

// ---------------------      PATROL BANGUNAN         ----------------------
Route::get('index/maintenance/patrol/building', 'MaintenanceController@indexPatrol');
Route::get('index/maintenance/tpm/patrol_building', 'MaintenanceController@indexPatrolBuilding');

// ---------------------      TROUBLE MACHINE           -------------------
Route::get('index/maintenance/machine_report/report', 'MaintenanceController@machineTroubleReport');
Route::get('fetch/maintenance/machine_report/report', 'MaintenanceController@fetchTroubleReport');

Route::get('machinery_monitoring', 'MaintenanceController@indexMachineryMonitoring');
Route::get('fetch/machinery_monitoring', 'MaintenanceController@fetchMachineryMonitoring');
Route::get('machinery_stop', 'MaintenanceController@indexMachineryStop');
Route::get('fetch/machinery_stop', 'MaintenanceController@fetchMachineryStop');
Route::post('edit/machinery_stop', 'MaintenanceController@editMachineryStop');

// ----------------------       WWT      ---------------------
Route::get('index/maintenance/wwt/waste_control', 'MaintenanceController@indexWWTLimbah');
Route::get('fetch/maintenance/wwt/waste_control', 'MaintenanceController@fetchLimbah');
Route::post('post/maintenance/wwt/waste_control', 'MaintenanceController@postLimbah');
Route::post('insert/logbook/update', 'MaintenanceController@UpdateLogBook');

Route::get('fetch/maintenance/wwt/daily', 'MaintenanceController@fetchWWTDailyForm');
Route::get('index/maintenance/wwt/daily', 'MaintenanceController@indexWWTDailyReport');

//update
Route::get('index/maintenance/wwt/waste_control/update', 'MaintenanceController@indexWWTLimbahUpdate');
Route::post('update/disposal', 'MaintenanceController@UpdateDisposal');
Route::post('update/qty', 'MaintenanceController@UpdateQty');
Route::post('delete/limbah/wwt', 'MaintenanceController@HapusSlipLimbah');
Route::get('index/maintenance/wwt/inventory', 'MaintenanceController@InventoryWWT');
Route::get('fetch/maintenance/wwt/inventory', 'MaintenanceController@FetchInventoryWWT');
Route::get('index/maintenance/wwt/monitoring', 'MaintenanceController@MonitoringWWT');
Route::get('fetch/maintenance/wwt/monitoring', 'MaintenanceController@FetchMonitoringWWT');
Route::get('fetch/wwt/monitoring/detail', 'MaintenanceController@FetchDetailMonitoring');
Route::get('fetch/all/monitoring/detail', 'MaintenanceController@FetchAllMonitoring');
Route::get('review/wwt/{slip}', 'MaintenanceController@reviewWwt');
Route::get('review/wwt/slip/{slip}', 'MaintenanceController@reviewWwtSlipDisposal');
Route::get('verivikasi/email/wwt/{category}/{slip_disposal}/{approver_id}', 'MaintenanceController@ConfirmationLoading');
Route::get('index/logs/wwt', 'MaintenanceController@IndexLogsWWT');
Route::get('fetch/logs/wwt', 'MaintenanceController@FetchLogsWWT');
Route::post('upload/dokumen/teknis', 'MaintenanceController@UploadDokumenTeknis');
Route::post('notifikasi/email', 'MaintenanceController@NotifikasiEmail');
Route::get('select/slip', 'MaintenanceController@SelectSlip');
Route::get('logbook/wwt/{slip}', 'MaintenanceController@LogBookIndex');
Route::get('fetch/logbook', 'MaintenanceController@FetchLogBook');
Route::get('get/limbah/keranjang', 'MaintenanceController@GetDataChecklist');
Route::get('index/chemical/wwt', 'MaintenanceController@ChemicalWWTMonitoring');
Route::get('fetch/monitoring/chemical/wwt', 'MaintenanceController@FetchChemicalWWTMonitoring');
Route::get('index/confirmation/limbah', 'MaintenanceController@IndexConfirmationLimbah');
Route::get('fetch/request/disposal', 'MaintenanceController@FetchRequestDisposal');
Route::get('fetch/detail/request/disposal', 'MaintenanceController@FetchDetailRequestDisposal');

Route::get('test/review/wwt', 'MaintenanceController@testreviewWwt');
Route::get('confirm/limbah/keluar/{slip_disposal}', 'MaintenanceController@ConfirmLimbahKeluar');
Route::post('confirm/save', 'MaintenanceController@SaveConfirm');
Route::get('review/confirm/{slip_disposal}', 'MaintenanceController@reviewConfirm');

Route::get('display/email/wwt/{slip_disposal}/{approver_id}', 'MaintenanceController@DisplayEmail');

Route::get('kirim_email/disposal', 'MaintenanceController@KirimEmailDisposal');

Route::post('index/maintenance/wwt/daily', 'MaintenanceController@indexWWTDailyReport');
Route::post('insert/date/disposal/limbah', 'MaintenanceController@InsertDateDisposalWWT');

// ------------------------  LISTRIK  ----------------------
Route::get('index/maintenance/electricity', 'MaintenanceController@indexElectricity');
Route::get('fetch/maintenance/electricity', 'MaintenanceController@fetchElectricity');
Route::post('input/maintenance/electricity_consumption', 'MaintenanceController@inputElectricityConsumption');
Route::post('delete/maintenance/electricity_consumption', 'MaintenanceController@deleteElectricityConsumption');

Route::get('index/maintenance/electricity/daily_consumption_ratio', 'MaintenanceController@indexElectricityDailyRatio');
Route::get('fetch/maintenance/electricity/daily_consumption_ratio', 'MaintenanceController@fetchElectricityDailyRatio');
Route::get('fetch/maintenance/electricity/target', 'MaintenanceController@fetchElectricityTarget');
Route::post('update/maintenance/electricity/target', 'MaintenanceController@inputElectricityTarget');

Route::get('index/maintenance/electricity/saving_monitor', 'MaintenanceController@indexElectricitySavingMonitor');
Route::get('fetch/maintenance/electricity/saving_monitor', 'MaintenanceController@fetchElectricitySavingMonitor');
Route::get('fetch/maintenance/electricity_pln', 'MaintenanceController@fetchElectricityPln');
Route::post('update/maintenance/electricity_pln', 'MaintenanceController@updateElectricityPln');

Route::get('index/maintenance/electricity/kaizen_monitor', 'MaintenanceController@indexElectricityKaizenMonitor');
Route::get('fetch/maintenance/electricity/kaizen_monitor', 'MaintenanceController@fetchElectricityKaizenMonitor');

Route::get('index/maintenance/electricity/consumption', 'MaintenanceController@indexElectricityConsumption');
Route::get('fetch/maintenance/electricity/consumption', 'MaintenanceController@fetchElectricityConsumption');

// ---------------------- WATER -------------------

Route::get('index/maintenance/domestic_pump', 'MaintenanceController@indexPumpMonitoring');
Route::get('fetch/maintenance/domestic_pump', 'MaintenanceController@fetchPumpMonitoring');

//Assemblies FL
Route::get('index/kensa/{location}', 'AssemblyProcessController@kensa');
Route::get('scan/assembly/operator', 'AssemblyProcessController@scanAssemblyOperator');
Route::get('scan/assembly/operator_kensa', 'AssemblyProcessController@scanAssemblyOperatorKensa');
Route::get('scan/assembly/kensa', 'AssemblyProcessController@scanAssemblyKensa');
Route::get('destroy/assembly/kensa', 'AssemblyProcessController@deleteAssemblyKensa');
Route::post('input/assembly/kensa', 'AssemblyProcessController@inputAssemblyKensa');
Route::post('input/assembly/seasoning', 'AssemblyProcessController@inputAssemblySeasoning');
Route::get('fetch/assembly/ng_detail', 'AssemblyProcessController@showNgDetail');
Route::get('fetch/assembly/onko', 'AssemblyProcessController@fetchOnko');
Route::get('fetch/assembly/ng_temp', 'AssemblyProcessController@fetchNgTemp');
Route::get('fetch/assembly/ng_temp_by_id', 'AssemblyProcessController@fetchNgTempById');
Route::get('fetch/assembly/ng_logs', 'AssemblyProcessController@fetchNgLogs');
Route::post('input/assembly/ng_temp', 'AssemblyProcessController@inputNgTemp');
Route::post('input/assembly/repair_process', 'AssemblyProcessController@inputRepairProcess');
Route::get('delete/assembly/delete_ng_temp', 'AssemblyProcessController@deleteNgTemp');
Route::post('input/assembly/ng_onko', 'AssemblyProcessController@inputNgOnko');
Route::post('input/assembly/ganti_kunci', 'AssemblyProcessController@inputGantiKunci');
Route::get('fetch/assembly/get_process_before', 'AssemblyProcessController@getProcessBefore');

Route::get('fetch/assembly', 'AssemblyProcessController@fetchAssembly');

Route::get('index/assembly/flute/print_label', 'AssemblyProcessController@indexFlutePrintLabel');
Route::get('fetch/assembly/flute/fillModelResult', 'AssemblyProcessController@fillModelResult');
Route::get('fetch/assembly/flute/fillResult', 'AssemblyProcessController@fillResult');
Route::get('fetch/assembly/fetchCheckTag', 'AssemblyProcessController@fetchCheckTag');
Route::get('fetch/assembly/flute/fetchCheckReprint', 'AssemblyProcessController@fetchCheckReprint');

Route::get('index/assembly/flute/label_outer/{id}/{gmc}/{remark}', 'AssemblyProcessController@labelBesarOuterFl');
Route::get('index/assembly/flute/label_besar/{id}/{gmc}/{remark}', 'AssemblyProcessController@labelBesarFl');
Route::get('index/assembly/flute/label_kecil/{id}/{remark}', 'AssemblyProcessController@labelKecilFl');
Route::get('index/assembly/flute/label_kecil2/{id}/{remark}', 'AssemblyProcessController@labelKecil2Fl');
Route::get('index/assembly/flute/label_deskripsi/{id}/{remark}', 'AssemblyProcessController@labelDeskripsiFl');
Route::get('fetch/check_carb_new', 'AssemblyProcessController@fetchCheckCarb');

Route::get('index/assembly/flute/kd_cleaning', 'AssemblyProcessController@indexKdCardCleaning');
Route::get('scan/assembly/flute/kd_cleaning', 'AssemblyProcessController@scanKdCardCleaning');
Route::get('fetch/assembly/flute/kd_cleaning', 'AssemblyProcessController@fetchKdCardCleaning');

Route::get('index/assembly/flute/card_cleaning', 'AssemblyProcessController@indexCardCleaning');
Route::get('scan/assembly/flute/card_cleaning', 'AssemblyProcessController@scanCardCleaning');
Route::get('fetch/assembly/flute/card_cleaning', 'AssemblyProcessController@fetchCardCleaning');

Route::get('index/board/{location}', 'AssemblyProcessController@indexAssemblyBoard');
Route::get('fetch/assembly/board', 'AssemblyProcessController@fetchAssemblyBoard');

Route::get('index/board/clarinet/{location}', 'AssemblyProcessController@indexAssemblyClarinetBoard');
Route::get('fetch/assembly/clarinet/board', 'AssemblyProcessController@fetchAssemblyClarinetBoard');

Route::get('index/board/saxophone/{location}', 'AssemblyProcessController@indexAssemblySaxophoneBoard');
Route::get('fetch/assembly/saxophone/board', 'AssemblyProcessController@fetchAssemblySaxophoneBoard');

Route::get('index/assembly/request/display/{id}', 'AssemblyProcessController@indexRequestDisplay');
Route::get('fetch/assembly/request', 'AssemblyProcessController@fetchRequest');

Route::get('index/assembly/ng_rate', 'AssemblyProcessController@indexNgRate');
Route::get('fetch/assembly/ng_rate', 'AssemblyProcessController@fetchNgRate');
Route::get('fetch/assembly/ng_rate_detail', 'AssemblyProcessController@fetchNgRateDetail');

Route::get('index/assembly/ng_trend/{origin_group}', 'AssemblyProcessController@indexNgTrend');
Route::get('fetch/assembly/ng_trend', 'AssemblyProcessController@fetchNgTrend');

Route::get('index/assembly/ongoing/{origin_group}/{line}', 'AssemblyProcessController@indexOngoing');
Route::get('fetch/assembly/ongoing', 'AssemblyProcessController@fetchOngoing');

Route::get('index/assembly/productivity/{origin_group}', 'AssemblyProcessController@indexAssyProductivity');
Route::get('fetch/assembly/productivity', 'AssemblyProcessController@fetchAssyProductivity');

Route::get('index/assembly/clarinet/ng_rate', 'AssemblyProcessController@indexNgClarinetRate');
Route::get('fetch/assembly/clarinet/ng_rate', 'AssemblyProcessController@fetchNgClarinetRate');
Route::get('fetch/assembly/clarinet/ng_rate_detail', 'AssemblyProcessController@fetchNgRateClarinetDetail');

Route::get('index/assembly/saxophone/ng_rate', 'AssemblyProcessController@indexNgSaxophoneRate');
Route::get('fetch/assembly/saxophone/ng_rate', 'AssemblyProcessController@fetchNgSaxophoneRate');
Route::get('fetch/assembly/saxophone/ng_rate/line', 'AssemblyProcessController@fetchNgSaxophoneRateLine');
Route::get('fetch/assembly/saxophone/ng_rate_detail', 'AssemblyProcessController@fetchNgRateSaxophoneDetail');

Route::get('index/assembly/op_ng', 'AssemblyProcessController@indexOpRate');
Route::get('fetch/assembly/op_ng', 'AssemblyProcessController@fetchOpRate');

Route::get('index/assembly/clarinet/op_ng', 'AssemblyProcessController@indexOpClarinetRate');
Route::get('fetch/assembly/clarinet/op_ng', 'AssemblyProcessController@fetchOpClarinetRate');

Route::get('index/assembly/saxophone/op_ng', 'AssemblyProcessController@indexOpSaxophoneRate');
Route::get('fetch/assembly/saxophone/op_ng', 'AssemblyProcessController@fetchOpSaxophoneRate');

Route::get('index/assembly/production_result', 'AssemblyProcessController@indexProductionResult');
Route::get('fetch/assembly/production_result', 'AssemblyProcessController@fetchProductionResult');

Route::get('index/assembly/stamp_record/{origin_group}', 'AssemblyProcessController@indexStampRecord');
Route::get('fetch/assembly/stamp_record', 'AssemblyProcessController@fetchStampRecord');

Route::get('index/assembly/ng_report/{process}/{origin_group}', 'AssemblyProcessController@indexNgReport');
Route::get('fetch/assembly/ng_report/{process}/{origin_group}', 'AssemblyProcessController@fetchNgReport');

Route::get('index/assembly/serial_number_report/{process}', 'AssemblyProcessController@indexSerialNumberReport');
Route::get('fetch/assembly/serial_number_report/{process}', 'AssemblyProcessController@fetchSerialNumberReport');

Route::get('index/assembly/sax/serial_number_report/{process}', 'AssemblyProcessController@indexSerialNumberReportSax');
Route::get('fetch/assembly/sax/serial_number_report/{process}', 'AssemblyProcessController@fetchSerialNumberReportSax');

Route::get('index/assembly/cl/serial_number_report/{process}', 'AssemblyProcessController@indexSerialNumberReportCl');
Route::get('fetch/assembly/cl/serial_number_report/{process}', 'AssemblyProcessController@fetchSerialNumberReportCl');

Route::get('index/assembly/serial_number_control/{origin_group}', 'AssemblyProcessController@indexSerialNumberControl');
Route::get('fetch/assembly/serial_number_control', 'AssemblyProcessController@fetchSerialNumberControl');

Route::get('index/assembly/report_qa_audit/{origin_group}', 'AssemblyProcessController@indexReportQAAudit');
Route::get('fetch/assembly/report_qa_audit', 'AssemblyProcessController@fetchReportQAAudit');

Route::get('index/assembly/status_material/{origin_group}', 'AssemblyProcessController@indexStatusMaterial');
Route::get('fetch/assembly/status_material', 'AssemblyProcessController@fetchStatusMaterial');

Route::get('index/assembly/report_spec_product', 'AssemblyProcessController@indexReportSpecProduct');
Route::get('fetch/assembly/report_spec_product', 'AssemblyProcessController@fetchReportSpecProduct');
Route::get('fetch/assembly/report_spec_product/detail', 'AssemblyProcessController@fetchReportSpecProductDetail');

Route::get('index/assembly/report_spec_product_process', 'AssemblyProcessController@indexReportSpecProductProcess');
Route::get('fetch/assembly/report_spec_product_process', 'AssemblyProcessController@fetchReportSpecProductProcess');

Route::get('index/assembly/return/{origin_group}', 'AssemblyProcessController@indexReturn');
Route::get('fetch/assembly/return', 'AssemblyProcessController@fetchReturn');
Route::post('scan/assembly/return', 'AssemblyProcessController@scanReturn');

Route::get('edit/assembly/stamp', 'AssemblyProcessController@editStamp');
Route::post('destroy/assembly/stamp', 'AssemblyProcessController@destroyStamp');
Route::post('update/assembly/stamp', 'AssemblyProcessController@updateStamp');
Route::get('adjust/assembly/stamp', 'AssemblyProcessController@adjustStamp');
Route::post('adjust/assembly/stamp_update', 'AssemblyProcessController@adjustStampUpdate');
Route::get('reprint/assembly/stamp', 'AssemblyProcessController@reprintStamp');

//Assembly Clarinet
Route::get('edit/assembly/stamp', 'AssemblyProcessController@editStamp');

Route::group(['nav' => 'M29', 'middleware' => 'permission'], function () {
    Route::get('index/sap_data', 'TransactionController@indexUploadSapData');
    Route::post('import/sap/completion', 'TransactionController@importCompletion');
    Route::post('delete/sap/completion', 'TransactionController@importCompletion');
    Route::post('import/sap/scrap', 'TransactionController@importScrap');
    Route::post('import/sap/std_time', 'TransactionController@importStdTime');
});

Route::get('index/mb51_transaction', 'TransactionController@indexMb51');
Route::post('upload/mb51_transaction', 'TransactionController@uploadMb51');
Route::get('fetch/mb51_transaction', 'TransactionController@fetchMb51');
Route::get('fetch/resume_mb51_transaction', 'TransactionController@fetchResumeMb51');
Route::get('download/mb51_transaction', 'TransactionController@downloadMb51');

Route::get('index/transfer_verification', 'TransactionController@indexTransferVerification');
Route::get('fetch/transfer_verification', 'TransactionController@fetchTransferVerification');
Route::get('fetch/transfer_verification_log', 'TransactionController@fetchTransferVerificationLog');
Route::post('input/transfer_verification', 'TransactionController@inputTransferVerification');

//Assembly SAX
Route::get('index/sax/kensa/{location}', 'AssemblyProcessController@indexSaxKensa');
Route::get('scan/assembly/operator_kensa/sax', 'AssemblyProcessController@scanAssemblyOperatorKensaSax');
Route::get('scan/assembly/kensa/sax', 'AssemblyProcessController@scanAssemblyKensaSax');
Route::get('fetch/assembly/ng_temp/sax', 'AssemblyProcessController@fetchAssemblyNgTempSax');
Route::post('input/assembly/kensa/sax', 'AssemblyProcessController@inputAssemblyKensaSax');
Route::post('input/assembly/ng_temp/sax', 'AssemblyProcessController@inputAssemblyNgTempSax');
Route::post('delete/assembly/ng_temp/sax', 'AssemblyProcessController@deleteAssemblyNgTempSax');
Route::post('input/assembly/repair/sax', 'AssemblyProcessController@inputAssemblyRepairSax');
Route::post('input/assembly/changekey/sax', 'AssemblyProcessController@inputAssemblyGantiKunciSax');
Route::post('input/assembly/kensa/confirmation', 'AssemblyProcessController@inputAssemblyKensaConfirmation');

//Assembly CL
Route::get('index/cl/kensa/{location}', 'AssemblyProcessController@indexClKensa');
Route::get('scan/assembly/operator_kensa/cl', 'AssemblyProcessController@scanAssemblyOperatorKensaCl');
Route::get('scan/assembly/kensa/cl', 'AssemblyProcessController@scanAssemblyKensaCl');
Route::get('scan/assembly/kensa/cl/upper', 'AssemblyProcessController@scanAssemblyKensaClUpper');
Route::get('fetch/assembly/ng_temp/cl', 'AssemblyProcessController@fetchAssemblyNgTempCl');
Route::post('input/assembly/kensa/cl', 'AssemblyProcessController@inputAssemblyKensaCl');
Route::post('delete/assembly/ng_temp/cl', 'AssemblyProcessController@deleteAssemblyNgTempCl');
Route::post('input/assembly/ng_temp/cl', 'AssemblyProcessController@inputAssemblyNgTempCl');
Route::post('input/assembly/repair/cl', 'AssemblyProcessController@inputAssemblyRepairCl');
Route::post('input/assembly/changekey/cl', 'AssemblyProcessController@inputAssemblyGantiKunciCl');

//Seasoning Assembly
Route::get('index/seasoning/{location}', 'AssemblyProcessController@indexSeasoning');
Route::get('fetch/seasoning', 'AssemblyProcessController@fetchSeasoning');
Route::get('scan/seasoning', 'AssemblyProcessController@scanSeasoning');
Route::post('input/seasoning', 'AssemblyProcessController@inputSeasoning');

Route::get('index/seasoning_in/{location}', 'AssemblyProcessController@indexSeasoningIn');
Route::get('fetch/seasoning_in', 'AssemblyProcessController@fetchSeasoningIn');
Route::post('input/seasoning_in', 'AssemblyProcessController@inputSeasoningIn');

Route::get('index/assembly/seasoning/report/{origin_group}', 'AssemblyProcessController@indexSeasoningReport');
Route::get('fetch/assembly/seasoning/report', 'AssemblyProcessController@fetchSeasoningReport');

Route::get('index/seasoning_progress/{location}', 'AssemblyProcessController@indexSeasoningProgress');
Route::get('fetch/seasoning_progress', 'AssemblyProcessController@fetchSeasoningProgress');
Route::post('input/seasoning_progress', 'AssemblyProcessController@inputSeasoningProgress');

//SKILL MAP
// Route::group(['nav' => 'M28', 'middleware' => 'permission'], function () {
Route::get('index/skill_map/{location}', 'SkillMapController@indexSkillMap');
Route::get('fetch/skill_map', 'SkillMapController@fetchSkillMap');
Route::get('fetch/skill_map_detail', 'SkillMapController@fetchSkillMapDetail');
Route::post('input/skill_adjustment', 'SkillMapController@inputSkillAdjustment');
Route::post('destroy/skill_maps', 'SkillMapController@destroySkillMaps');

Route::get('fetch/skill_master', 'SkillMapController@fetchSkillMaster');
Route::post('input/skill_master', 'SkillMapController@inputSkillMaster');
Route::post('destroy/skill_master', 'SkillMapController@destroySkillMaster');
Route::get('get/skill_master', 'SkillMapController@getSkillMaster');

Route::get('fetch/skill_value', 'SkillMapController@fetchSkillValue');
Route::post('input/skill_value', 'SkillMapController@inputSkillValue');
Route::post('destroy/skill_value', 'SkillMapController@destroySkillValue');
Route::get('get/skill_value', 'SkillMapController@getSkillValue');

Route::get('fetch/skill_employee', 'SkillMapController@fetchSkillEmployee');
Route::post('input/skill_employee', 'SkillMapController@inputSkillEmployee');
Route::post('destroy/skill_employee', 'SkillMapController@destroySkillEmployee');
Route::get('get/skill_employee', 'SkillMapController@getSkillEmployee');

Route::get('fetch/skill_resume', 'SkillMapController@fetchSkillResume');
Route::get('fetch/skill_resume_operator', 'SkillMapController@fetchSkillResumeOperator');

Route::get('fetch/skill_evaluation', 'SkillMapController@fetchSkillEvaluation');
Route::post('input/skill_evaluation', 'SkillMapController@inputSkillEvaluation');

Route::get('index/skill_map/{location}', 'SkillMapController@indexSkillMap');

Route::get('report/skill_map_evaluation/{location}', 'SkillMapController@reportSkillMapEvaluation');
Route::get('fetch/report/skill_map_evaluation', 'SkillMapController@fetchReportSkillMapEvaluation');
Route::get('print/report/skill_map_evaluation/{location}/{evaluation_code}', 'SkillMapController@printSkillMapEvaluation');
// });

//NG Jelas Report & Audit IK
Route::group(['nav' => 'M30', 'middleware' => 'permission'], function () {

});

Route::get('index/qa/audit_ng_jelas', 'AuditController@indexAuditNgJelas');
Route::get('index/audit_ng_jelas', 'AuditController@indexNgJelas');
Route::get('fetch/audit_ng_jelas/point', 'AuditController@fetchNgJelasPoint');
Route::get('scan/audit_ng_jelas', 'AuditController@scanNgJelas');
Route::post('input/audit_ng_jelas', 'AuditController@inputNgJelas');
Route::post('upload/file/audit_ng_jelas', 'AuditController@uploadFileNGJelas');
Route::get('index/qa/audit_ng_jelas_monitoring', 'AuditController@indexQaNgJelasMonitoring');
Route::get('edit/qa/audit_ng_jelas', 'AuditController@editNgJelas');
Route::post('update/qa/audit_ng_jelas', 'AuditController@updateNgJelas');
Route::get('send_email/qa/audit_ng_jelas', 'AuditController@sendEmailNgJelas');

Route::get('index/audit_ng_jelas/schedule', 'AuditController@indexNgJelasSchedule');
Route::get('fetch/audit_ng_jelas/schedule', 'AuditController@fetchNgJelasSchedule');
Route::get('download/audit_ng_jelas/schedule', 'AuditController@downloadNgJelasSchedule');
Route::post('upload/audit_ng_jelas/schedule', 'AuditController@uploadNgJelasSchedule');
Route::post('update/audit_ng_jelas/schedule', 'AuditController@updateNgJelasSchedule');
Route::post('input/audit_ng_jelas/schedule', 'AuditController@inputNgJelasSchedule');
Route::get('delete/audit_ng_jelas/schedule', 'AuditController@deleteNgJelasSchedule');

Route::get('index/qa/audit_ng_jelas_report', 'AuditController@indexQaNgJelasReport');
Route::get('fetch/qa/audit_ng_jelas_report', 'AuditController@fetchQaNgJelasReport');

Route::get('index/qa/audit_ng_jelas/handling/{schedule_id}', 'AuditController@indexQaNgJelasHandling');
Route::post('input/qa/audit_ng_jelas/handling', 'AuditController@inputQaNgJelasHandling');

Route::get('index/audit_ng_jelas_monitoring', 'ProductionReportController@indexNgJelasMonitoring');
Route::get('fetch/audit_ng_jelas_monitoring', 'ProductionReportController@fetchNgJelasMonitoring');
Route::get('fetch/audit_ng_jelas_monitoring2', 'ProductionReportController@fetchNgJelasMonitoring2');
Route::get('fetch/detail_audit_ng_jelas_monitoring', 'ProductionReportController@fetchDetailNgJelasMonitoring');
Route::get('fetch/detail_audit_ng_jelas_monitoring/claim', 'ProductionReportController@fetchDetailNgJelasMonitoringClaim');
Route::get('fetch/detail_audit_ng_jelas_monitoring/claim/detail', 'ProductionReportController@fetchDetailNgJelasMonitoringClaimDetail');
Route::get('print/pdf/audit_ng_jelas/{audit_title}/{date}/{type}', 'ProductionReportController@printPdfAuditNgJelas');
Route::get('fetch/audit_ng_jelas/detail_temuan', 'ProductionReportController@fetchDetailNgJelasTemuan');

Route::get('index/audit_ik_monitoring', 'ProductionReportController@indexAuditIKMonitoring');
Route::get('fetch/audit_ik_monitoring', 'ProductionReportController@fetchAuditIKMonitoring');
Route::get('fetch/detail_audit_ik_monitoring', 'ProductionReportController@fetchDetailAuditIKMonitoring');
Route::get('index/audit_ik_monitoring/handling/{id}', 'ProductionReportController@indexAuditIkHandling');
Route::post('input/audit_ik_monitoring/handling', 'ProductionReportController@inputAuditIkHandling');
Route::post('input/audit_ik_monitoring/audit_qa', 'ProductionReportController@inputAuditIkAuditQA');
Route::get('input/audit_ik_monitoring/send_email', 'AuditReportActivityController@sendEmailTemuan');
Route::get('input/audit_ik_monitoring/send_email_cek', 'AuditReportActivityController@sendEmailCekEfektifitas');
Route::get('index/audit_ik_monitoring/cek_efektifitas/{id}', 'AuditReportActivityController@indexCekEfektifitas');
Route::post('input/audit_ik_monitoring/cek_efektifitas', 'AuditReportActivityController@inputCekEfektifitas');

Route::get('index/daily_check_mesin', 'ProductionReportController@indexDailyCheckMesin');
Route::get('fetch/daily_check_mesin', 'ProductionReportController@fetchDailyCheckMesin');
Route::get('fetch/daily_check_mesin/detail', 'ProductionReportController@fetchDailyCheckMesinDetail');

//ROOMS
Route::get('/meetingroom1', function () {
    return view('rooms.meetingroom1');
});
Route::get('/fillingroom', function () {
    return view('rooms.fillingroom');
});
Route::get('/trainingroom1', function () {
    return view('rooms.trainingroom1');
});
Route::get('/trainingroom2', function () {
    return view('rooms.trainingroom2');
});
Route::get('/trainingroom3', function () {
    return view('rooms.trainingroom3');
});

Route::get('/welcome_trial', function () {
    return view('trials.welcome_trial');
});

Route::group(['nav' => 'M32', 'middleware' => 'permission'], function () {
    // MIRAI MOBILE
    Route::get('index/mirai_mobile/index', 'MiraiMobileController@index');

    //CORONA MAP
    Route::get('index/mirai_mobile/corona_map', 'MiraiMobileController@indexCoronaMap');

    // CORONA
    Route::get('index/corona_information', 'MiraiMobileController@indexCoronaInformation');
    Route::get('fetch/corona_information', 'MiraiMobileController@fetchCoronaInformation');

    //Display Health
    Route::get('index/mirai_mobile/healthy_report', 'MiraiMobileController@display_health');
    Route::get('fetch/mirai_mobile/healthy_report', 'MiraiMobileController@fetch_health');
    Route::get('index/mirai_mobile/detail', 'MiraiMobileController@fetch_detail');
    Route::get('index/mirai_mobile/detail_sakit', 'MiraiMobileController@fetch_detail_sakit');

    //report attendance
    Route::get('index/mirai_mobile/report_attendance', 'MiraiMobileController@health');
    Route::get('fetch/mirai_mobile/report_attendance', 'MiraiMobileController@fetchHealthData');
    Route::get('fetch/mirai_mobile/report_attendance/with_loc', 'MiraiMobileController@fetchHealthDataLoc');
    Route::get('fetch/location_employee', 'MiraiMobileController@fetchLocationEmployee');
    Route::get('index/mirai_mobile/report_attendance_sbh', 'MiraiMobileController@healthSbh');
    Route::get('fetch/mirai_mobile/report_attendance_sbh', 'MiraiMobileController@fetchHealthDataSbh');

    //report shift
    Route::get('index/mirai_mobile/report_shift', 'MiraiMobileController@shift');
    Route::get('fetch/mirai_mobile/report_shift', 'MiraiMobileController@fetchShiftData');

    //report location
    Route::get('index/mirai_mobile/report_location', 'MiraiMobileController@location');
    Route::get('fetch/mirai_mobile/report_location', 'MiraiMobileController@fetchLocation');
    Route::get('fetch/mirai_mobile/report_location/detail', 'MiraiMobileController@fetchLocationDetail');
    Route::get('fetch/mirai_mobile/report_location/detail_all', 'MiraiMobileController@fetchLocationDetailAll');
    Route::get('export/mirai_mobile/report_location', 'MiraiMobileController@exportList');

    //report shift
    Route::get('index/mirai_mobile/report_indication', 'MiraiMobileController@indication');
    Route::get('fetch/mirai_mobile/report_indication', 'MiraiMobileController@fetchIndicationData');

    //Peduli Lindungi
    Route::get('index/peduli_lindungi/report', 'MiraiMobileController@indexPeduliLindungiReport');
    Route::get('fetch/peduli_lindungi/report', 'MiraiMobileController@fetchPeduliLindungiReport');

    Route::get('index/survey_covid/report', 'SurveyController@indexSurveyCovidReport');
    Route::get('fetch/survey_covid/report', 'SurveyController@fetchSurveyCovidReport');
    Route::get('fetch/survey_covid/report/detail', 'SurveyController@fetchSurveyCovidReportDetail');

    //Guest Assessment Covid
    Route::get('index/guest_assessment/report', 'MiraiMobileController@indexGuestAssessmentReport');
    Route::get('fetch/guest_assessment/report', 'MiraiMobileController@fetchGuestAssessmentReport');
    Route::get('fetch/guest_assessment/report/detail', 'MiraiMobileController@fetchGuestAssessmentReportDetail');

    //WPOS
    Route::get('index/wpos/report', 'MiraiMobileController@indexWposReport');
    Route::get('fetch/wpos/report', 'MiraiMobileController@fetchWposReport');
    Route::get('fetch/wpos/report/detail', 'MiraiMobileController@fetchWposReportDetail');

    Route::get('index/wpos/monitoring', 'MiraiMobileController@indexVaksinMonitoring');
    Route::get('fetch/wpos/monitoring', 'MiraiMobileController@fetchVaksinMonitoring');

    //Vendor Assessment Covid
    Route::get('index/vendor_assessment/report', 'MiraiMobileController@indexVendorAssessmentReport');
    Route::get('fetch/vendor_assessment/report', 'MiraiMobileController@fetchVendorAssessmentReport');

    //Vaksin
    Route::get('index/vaksin/report', 'MiraiMobileController@indexVaksinReport');
    Route::get('fetch/vaksin/report', 'MiraiMobileController@fetchVaksinReport');

    //Family Day
    Route::get('index/family_day/report', 'MiraiMobileController@indexFamilyDayReport');
    Route::get('fetch/family_day/report', 'MiraiMobileController@fetchFamilyDayReport');

    //Family Day Attendance
    Route::get('index/family_day/attendance', 'MiraiMobileController@indexFamilyDayAttendance');
    Route::get('fetch/family_day/attendance', 'MiraiMobileController@fetchFamilyDayAttendance');
    Route::get('fetch/family_day/queue', 'MiraiMobileController@fetchFamilyDayAttendanceQueue');

    Route::get('index/vehicle/report', 'MiraiMobileController@indexVehicleReport');
    Route::get('fetch/vehicle/report', 'MiraiMobileController@fetchVehicleReport');

    Route::get('index/vehicle/attendance/{id}', 'MiraiMobileController@indexVehicleAttendance');
    Route::get('fetch/vehicle/attendance', 'MiraiMobileController@fetchVehicleAttendance');
    Route::get('fetch/vehicle/queue', 'MiraiMobileController@fetchVehicleAttendanceQueue');

    //Slogan Mutu

    Route::get('index/slogan/report', 'MiraiMobileController@indexSloganMutu');
    Route::get('fetch/slogan/report', 'MiraiMobileController@fetchSloganMutu');
    Route::post('input/slogan/report/selection', 'MiraiMobileController@inputSloganSelection');
    Route::post('input/slogan/report/final', 'MiraiMobileController@inputSloganFinal');

    //Pendaftaran Vaksin
    Route::get('index/vaksin/registration/report', 'MiraiMobileController@indexVaksinRegistrationReport');
    Route::get('fetch/vaksin/registration/report', 'MiraiMobileController@fetchVaksinRegistrationReport');
    Route::get('index/vaksin/monitoring', 'MiraiMobileController@indexVaksinMonitoring');
    Route::get('fetch/vaksin/monitoring', 'MiraiMobileController@fetchVaksinMonitoring');
    Route::get('fetch/vaksin/monitoring/detail', 'MiraiMobileController@fetchVaksinMonitoringDetail');
    Route::get('fetch/vaksin/monitoring/detailAll', 'MiraiMobileController@fetchVaksinMonitoringDetailAll');

    // PKB
    Route::get('index/master/pkb', 'MiraiMobileController@indexMasterPkb');
    Route::get('fetch/question/pkb', 'MiraiMobileController@fetchQuestionPkb');
    Route::post('update/question/pkb', 'MiraiMobileController@updateQuestionPkb');
    Route::post('delete/question/pkb', 'MiraiMobileController@deleteQuestionPkb');
    Route::post('add/question/pkb', 'MiraiMobileController@addQuestionPkb');
    Route::post('update/periode/pkb', 'MiraiMobileController@updatePeriodePkb');
    Route::post('delete/periode/pkb', 'MiraiMobileController@deletePeriodePkb');
    Route::post('add/periode/pkb', 'MiraiMobileController@addPeriodePkb');
    Route::get('index/pkb/report', 'MiraiMobileController@indexPkbReport');
    Route::get('fetch/pkb/report', 'MiraiMobileController@fetchPkbReport');
    Route::get('print/pkb/report/{id}', 'MiraiMobileController@printPkbReport');

});

Route::get('index/slogan', 'StandardizationController@indexSlogan');
Route::get('fetch/slogan', 'StandardizationController@fetchSlogan');

Route::get('index/slogan/assessment', 'StandardizationController@indexSloganAssessment');
Route::get('fetch/slogan/assessment', 'StandardizationController@fetchSloganAssessment');
Route::post('input/slogan/assessment', 'StandardizationController@inputSloganAssessment');

Route::get('download/slogan', 'StandardizationController@downloadSlogan');
Route::post('upload/slogan', 'StandardizationController@uploadSlogan');

//YMPI COMPETITION
Route::get('index/competition/registration/report', 'GeneralController@indexCompeitionRegistrationReport');
Route::get('fetch/competition/registration/report', 'GeneralController@fetchCompeitionRegistrationReport');

Route::get('index/competition/registration', 'GeneralController@indexCompetitionRegistration');
Route::get('fetch/competition/participant', 'GeneralController@fetchCompetitionParticipant');
Route::get('fetch/competition/registration/count', 'GeneralController@fetchCompetitionCountRegistration');
Route::post('input/competition/registration', 'GeneralController@inputCompetitionRegistration');

Route::get('index/competition/attendance', 'GeneralController@indexCompetitionAttendance');
Route::get('fetch/competition/attendance', 'GeneralController@fetchCompetitionAttendance');
Route::get('update/competition/attendance', 'GeneralController@updateCompetitionAttendance');
Route::post('scan/competition/attendance', 'GeneralController@scanCompetitionAttendance');

//Survey Covid
Route::get('index/survey_covid', 'SurveyController@indexSurveyCovid');
Route::get('fetch/survey_covid', 'SurveyController@fetchSurveyCovid');
Route::get('fetch/survey_covid/detail', 'SurveyController@fetchSurveyCovidDetail');

//Emergency Survey
Route::get('index/survey', 'SurveyController@indexSurvey');
Route::get('fetch/survey', 'SurveyController@fetchSurvey');
Route::get('fetch/survey/detail', 'SurveyController@fetchSurveyDetail');

//Peduli Lindungi
Route::get('index/peduli_lindungi', 'SurveyController@indexPeduliLindungi');
Route::get('fetch/peduli_lindungi', 'SurveyController@fetchPeduliLindungi');
Route::get('fetch/peduli_lindungi/detail', 'SurveyController@fetchSPeduliLindungiDetail');

// PKB
Route::get('index/pkb', 'SurveyController@indexPkb');
Route::get('fetch/pkb', 'SurveyController@fetchPkb');

//KodeEtik
Route::get('index/kode/etik', 'SurveyController@indexKodeEtik');
Route::get('fetch/kodeEtik', 'SurveyController@fetchkodeEtik');

//Data Komunikasi
Route::get('index/data_komunikasi', 'SurveyController@indexDataKomunikasi');
Route::get('fetch/data_komunikasi', 'SurveyController@fetchDataKomunikasi');
Route::get('fetch/data_komunikasi/detail', 'SurveyController@fetchDataKomunikasiDetail');
Route::get('fetch/data_komunikasi/detailAll', 'SurveyController@fetchDataKomunikasiDetailAll');

//Hasil MCU
Route::get('index/hasil_mcu', 'SurveyController@indexHasilMCU');
Route::get('fetch/hasil_mcu', 'SurveyController@fetchHasilMCU');
Route::get('fetch/hasil_mcu/detail', 'SurveyController@fetchHasilMCUDetail');
Route::get('fetch/hasil_mcu/detailAll', 'SurveyController@fetchHasilMCUDetailAll');

//Cool Finding
Route::get('index/cool_finding_monitoring', 'MiraiMobileController@indexMonitoringAll');
Route::get('fetch/cool_finding_monitoring', 'MiraiMobileController@fetchMonitoringAll');
// Route::get('index/cool_finding_monitoring_detail', 'MiraiMobileController@detailMonitoringAll');
// Route::get('index/cool_finding_monitoring_detail_bulan', 'MiraiMobileController@detailMonitoringBulanAll');
Route::get('index/cool_finding_monitoring_table', 'MiraiMobileController@fetchTableAuditAll');

//audit MIS
Route::get('index/audit_mis', 'DailyReportController@indexAuditMIS');
Route::get('fetch/audit_mis/check', 'DailyReportController@fetchAuditCheckList');
Route::post('post/audit_mis/check', 'DailyReportController@postCheckAudit');

//inventory MIS
Route::get('index/inventory_mis', 'DailyReportController@indexInventoryMIS');
Route::get('fetch/inventory_mis/list', 'DailyReportController@fetchInventoryMIS');
Route::post('post/inventory_mis/item', 'DailyReportController@createInventoryMIS');
Route::get('fetch/inventory_mis', 'DailyReportController@fetchInventoryMISbyId');
Route::post('update/inventory_mis/data', 'DailyReportController@updateInventoryMIS');
Route::post('delete/inventory_mis', 'DailyReportController@deleteInventoryMIS');

Route::get('print/inventory_mis/{id}', 'DailyReportController@printInventory');

Route::get('print2/inventory_mis/{id}', 'DailyReportController@printInventory2');

Route::get('index/cart_check/inventory_mis', 'DailyReportController@indexCartInventoryCheck');
Route::post('input/check/item/inventory', 'DailyReportController@inputInventoryChecklist');
Route::post('update/inventory/mis', 'DailyReportController@inputInventoryMIS');
Route::get('delete/list/item/mis', 'DailyReportController@deleteItemMIS');

Route::get('inventory/report/{id}', 'DailyReportController@reportInventoryMIS');
Route::get('index/history/report/mis', 'DailyReportController@indexHistoryInventory');
Route::get('fetch/history/inventory/mis', 'DailyReportController@fetchMisInventory');
Route::get('fetch/grafik/inventory/mis', 'DailyReportController@fetchMISMonitoringInventory');

Route::get('fetch/inventory/mis/edit', 'DailyReportController@fetchDataInventoryEdit');
Route::post('update/inventory/mis1', 'DailyReportController@updteMisInventory');

Route::get('index/print/asset/it/{id}', 'DailyReportController@printAssetIpad');

//mis inve

// =======
Route::get('fetch/data/grafik', 'DailyReportController@FetchGrafikCategory');
Route::get('fetch/data/detail', 'DailyReportController@FetchGrafikDetail');

Route::get('/radar_covid', function () {
    return view('mirai_mobile.radar_covid');
});

Route::get('fetch/notif/mutasi', 'MutasiController@getNotifMutasiSatu');

//Scrap
Route::group(['nav' => 'S37', 'middleware' => 'permission'], function () {
    Route::get('index/scrap', 'ScrapController@indexScrap');
    Route::get('select/scrap/type', 'ScrapController@SelectScrapType');
    Route::get('select/scrap/reason', 'ScrapController@SelectScrapReason');
    Route::get('index/scrap/create', 'ScrapController@createScrap');
    Route::get('fetch/scrap', 'ScrapController@fetchScrap');
    Route::get('fetch/scrap/list', 'ScrapController@fetchScrapList');
    Route::post('confirm/scrap', 'ScrapController@confirmScrap');
    Route::post('print/scrap', 'ScrapController@printScrap');
    Route::get('fetch/scrap/resume', 'ScrapController@fetchScrapResume');
    Route::post('delete/scrap', 'ScrapController@deleteScrap');
    Route::post('delete/penarikan/scrap', 'ScrapController@deletePenarikanScrap');

    Route::get('scrap/view/monitoring/wip', 'ScrapController@MonitoringWip');
    Route::get('scrap/resume/list/wip', 'ScrapController@ResumeListWip');
    Route::get('scrap/resume/list/wh', 'ScrapController@ResumeListWh');
    Route::get('scrap/data/monitoring/wip', 'ScrapController@fetchMonitoringScrap');
    Route::get('scrap/list/wip', 'ScrapController@ListWip');
    Route::get('scrap/resume/month', 'ScrapController@ResumeListMonth');

    Route::get('index/penarikan/scrap', 'ScrapController@PenarikanScrap');
    Route::get('fetch/penarikan/scrap', 'ScrapController@fetchPenarikanScrap');
    Route::get('fetch/penarikan/scrap/list', 'ScrapController@fetchPenarikanScrapList');
    Route::get('list/penarikan/scrap', 'ScrapController@listPenarikanScrap');
    // Route::get('print/penarikan', 'ScrapController@printScrapPenarikan');
});

// Route::group(['nav' => 'M35', 'middleware' => 'permission'], function(){ke

// });
// Route::get('index/scrap/data', 'ScrapController@indexScrapData');
// Route::get('fetch/scrap/data', 'ScrapController@fetchScrapData');
// Route::get('fetch/scrap/list/assy', 'ScrapController@fetchScrapListAssy');
Route::get('index/scrap/warehouse', 'ScrapController@indexWarehouse');
Route::get('scan/scrap_warehouse', 'ScrapController@scanScrapWarehouse');
Route::get('scrap/view/display/warehouse', 'ScrapController@displayScrapWarehouse');
Route::get('scrap/date/display/warehouse', 'ScrapController@fetchMonitoringScrapWarehouse');
Route::get('fetch/scrap_warehouse', 'ScrapController@fetchScrapWarehouse');
// Route::group(['nav' => 'M36', 'middleware' => 'permission'], function(){
//     Route::get('index/scrap/warehouse', 'ScrapController@indexWarehouse');
//     Route::get('scan/scrap_warehouse', 'ScrapController@scanScrapWarehouse');
//     Route::get('scrap/view/display/warehouse', 'ScrapController@displayScrapWarehouse');
//     Route::get('scrap/date/display/warehouse', 'ScrapController@fetchMonitoringScrapWarehouse');
//     Route::get('fetch/scrap_warehouse', 'ScrapController@fetchScrapWarehouse');
// });

Route::get('index/scrap_record', 'ScrapController@indexScrapRecord')->name('report_scrap_index');
Route::get('fetch/scrap/logs', 'ScrapController@fetchLogs');
Route::post('cancel/scrap', 'ScrapController@cancelScrap');
Route::get('cancel/scrap/user', 'ScrapController@CancelScrapUser');
Route::get('reprint/scrap', 'ScrapController@reprintScrap');
Route::get('fetch/scrap_record', 'ScrapController@fetchRecord');
Route::get('index/scrap/view', 'ScrapController@indexScrapView');
Route::post('update/scrap', 'ScrapController@updateScrap');
Route::get('index/scrap/resume', 'ScrapController@indexScrapResume');
Route::get('index/scrap/logs', 'ScrapController@indexLogs');
Route::get('fetch/scrap_detail', 'ScrapController@fetchScrapDetail');
Route::get('fetch/scrap_warehouse', 'ScrapController@fetchScrapWarehouse');
Route::get('fetch/kd_scrap_closure', 'ScrapController@fetchKdScrapClosure');
Route::get('scrap/data/monitoring', 'ScrapController@fatchMonitoringDisplayScrap');
Route::get('scrap/monitoring/display', 'ScrapController@MonitoringScrapDisplay');
Route::get('scrap/monitoring/display', 'ScrapController@MonitoringScrapDisplay');
Route::post('invoice/scrap', 'ScrapController@InputInvoiceQA');
Route::get('confirm/penarikan/scrap', 'ScrapController@ConfirmPenarikanScrap');
Route::get('scan/penarikan/scrap', 'ScrapController@ScanPenarikanScrap');
Route::get('fetch/scan/penarikan/scrap', 'ScrapController@FetchScanPenarikanScrap');
Route::get('reprint/penarikan/scrap', 'ScrapController@ReprintPenarikanScrap');
Route::get('log/penarikan/scrap', 'ScrapController@PenarikanScrapLog');
Route::get('fetch/penarikan/scrap/logs', 'ScrapController@FetchPenarikanScrapLogs');
Route::post('cancel/penarikan/scrap', 'ScrapController@BatalPenarikanScrap');
Route::post('add/penarikan/scrap', 'ScrapController@addPenarikanScrap');
Route::get('penarikan/scrap/setuju/{id}', 'ScrapController@accPenarikanScrap');
Route::get('penarikan/scrap/reject/{id}', 'ScrapController@rejectPenarikanScrap');
Route::post('print/scrap/penarikan', 'ScrapController@printScrapPenarikan');
// ============================================================================================
Route::get('penarikan/scrap/reject/{id}', 'ScrapController@rejectPenarikanScrap');
Route::get('index/upload/scrap/mirai', 'ScrapController@IndexUploadScrapMirai');
Route::get('fetch/upload/scrap/mirai', 'ScrapController@FetchUploadScrapMirai');
Route::post('upload/scrap/mirai', 'ScrapController@InputUploadScrapMirai');
Route::post('upload/daily/scrap/mirai', 'ScrapController@InputUploadDailyScrapMirai');
Route::get('excel/report/excel/scrap', 'ScrapController@ExcelReportScrap');

//Mutasi
Route::get('fetch/mutasi/resume', 'MutasiController@fetchResumeMutasi');
Route::get('fetch/mutasi/resume_ant', 'MutasiController@fetchResumeMutasiAnt');
Route::get('dashboard/mutasi/get_employee', 'MutasiController@get_employee');
Route::get('dashboard/mutasi/get_grade', 'MutasiController@get_grade');
Route::get('dashboard/mutasi/get_tujuan', 'MutasiController@get_tujuan');
Route::get('dashboard/mutasi/get_section', 'MutasiController@get_section');
Route::get('dashboard/mutasi/get_group', 'MutasiController@get_group');
Route::get('dashboard/mutasi/getPosition', 'MutasiController@getPosition');
//Create
Route::get('create/mutasi', 'MutasiController@create');
Route::post('create/mutasi', 'MutasiController@store');
Route::get('create_ant/mutasi', 'MutasiController@createAnt');
Route::post('create_ant/mutasi', 'MutasiController@storeAnt');
//Edit
Route::post('edit/mutasi', 'MutasiController@editMutasi');
Route::post('edit/mutasi_ant', 'MutasiController@editMutasiAnt');
//Rejected
Route::get('rejected/{id}', 'MutasiController@rejected');
Route::get('rejectedantar_departemen/{id}', 'MutasiController@rejectedAnt');
//Approval Satu Departemen
Route::get('approvechief_or_foreman_asal/{id}', 'MutasiController@mutasi_approvalchief_or_foreman_asal');
Route::get('approvechief_or_foreman_tujuan/{id}', 'MutasiController@mutasi_approvalchief_or_foreman_tujuan');
Route::get('approvemanager/{id}', 'MutasiController@mutasi_approvalmanager');
Route::get('approve_dgm/{id}', 'MutasiController@mutasi_approval_dgm');
Route::get('approve_gm/{id}', 'MutasiController@mutasi_approval_gm');
Route::get('approve_manager_hrga/{id}', 'MutasiController@mutasi_manager_hrga');
Route::get('approvegm/{id}', 'MutasiController@mutasi_approvalgm');
//Aproval Antar Departemen
Route::get('approvechief_or_foremanasalAntar/{id}', 'MutasiController@mutasi_approvalchief_or_foremanAsal');
Route::get('approve_manager_asal/{id}', 'MutasiController@mutasi_approval_managerAsal');
Route::get('approve_dgm_asal/{id}', 'MutasiController@mutasi_approval_dgmAsal');
Route::get('approve_gm_asal/{id}', 'MutasiController@mutasi_approval_gmAsal');
Route::get('approvechief_or_foremantujuan/{id}', 'MutasiController@mutasi_approvalchief_or_foremanTujuan');
Route::get('approve_manager_tujuan/{id}', 'MutasiController@mutasi_approval_managerTujuan');
Route::get('approve_dgm_tujuan/{id}', 'MutasiController@mutasi_approval_dgmTujuan');
Route::get('approve_gm_tujuan/{id}', 'MutasiController@mutasi_approval_gmTujuan');
Route::get('approvegm_division/{id}', 'MutasiController@mutasi_approvalGM_Division');
Route::get('approvemanager_hrga/{id}', 'MutasiController@mutasi_approvalManager_Hrga');
Route::get('approvepres_dir/{id}', 'MutasiController@mutasi_approvalPres_Dir');
Route::get('approvedirektur_hr/{id}', 'MutasiController@mutasi_approvalDirektur_Hr');
// ============================================================================
Route::get('mutasi_ant/verifikasi/{id}', 'MutasiController@verifikasi_mutasi_ant');
Route::get('mutasi/verifikasi/{id}', 'MutasiController@verifikasi_mutasi');
Route::post('mutasi_ant/approval/{id}', 'MutasiController@approval_mutasi_ant');
Route::get('mutasi_ant/report/{id}', 'MutasiController@report_mutasi_ant');
Route::get('mutasi/report/{id}', 'MutasiController@report_mutasi');
Route::get('mutasi_ant/finish/{id}', 'MutasiController@finish_ant');
Route::get('mutasi/finish/{id}', 'MutasiController@finish');
Route::get('mutasi/email/{id}', 'MutasiController@email');
Route::get('mutasi_ant/email/{id}', 'MutasiController@emailAnt');
//Show
Route::get('mutasi/show/{id}', 'MutasiController@showApproval');
Route::get('mutasi_ant/show/{id}', 'MutasiController@showAntApproval');
//Fetch Detail
Route::get('fetch/mutasi_ant', 'MutasiController@fetchMutasiDetail');
Route::get('fetch/mutasi', 'MutasiController@fetchMutasiSatuDetail');
Route::get('view/mutasi_ant', 'MutasiController@viewMutasiDetail');
Route::get('view/mutasi', 'MutasiController@viewMutasiSatuDetail');

Route::get('fetch/mutasi_ant/monitoringant', 'MutasiController@fetchMonitoringMutasiAnt');
Route::get('fetch/mutasi/monitoring', 'MutasiController@fetchMonitoringMutasi');

Route::get('mutasi/cek_email', 'MutasiController@viewCekEmail');
Route::get('kirim_ulang_report/mutasi', 'MutasiController@KirimReport');
Route::get('kirim_ulang_approval/mutasi', 'MutasiController@KirimUlangApproval');

Route::get('index/message/approval', 'MutasiController@ApprovalMessage');

//report HR
Route::get('mutasi/hr', 'MutasiController@HrExport');
Route::get('fetch/mutasi/hr', 'MutasiController@FetchHrExport');
Route::get('excel/mutasi/hr', 'MutasiController@HrExportExcel');
Route::get('mutasi_ant/hr', 'MutasiController@AntHrExport');
Route::get('fetch/mutasi_ant/hr', 'MutasiController@AntFetchHrExport');
Route::get('excel/mutasi_ant/hr', 'MutasiController@AntHrExportExcel');

Route::get('index/live/monitoring', 'AuditController@indexMonitoringLive');
Route::get('fetch/live/monitoring', 'AuditController@fetchMonitoringLive');
Route::get('index/live/detail', 'AuditController@detailMonitoringLive');
Route::get('index/live/detail_category', 'AuditController@detailMonitoringLiveCategory');
Route::get('index/live/detail_bulan', 'AuditController@detailMonitoringLiveBulan');
Route::get('index/live/table', 'AuditController@fetchTableLive');
Route::post('post/live', 'AuditController@postLive');
Route::get('index/live/detail_penanganan', 'AuditController@detailPenangananLive');
Route::post('post/live/penanganan', 'AuditController@postPenangananLive');

// Route::group(['nav' => 'R12', 'middleware' => 'permission'], function(){

// });

// Route::group(['nav' => 'R9', 'middleware' => 'permission'], function(){

// });

//Auto Approve Adagio
//input approval
Route::group(['nav' => 'AP01', 'middleware' => 'permission'], function () {
    Route::get('adagio/home/index', 'AdagioAutoController@IndexAdagioIndexHome');
    Route::post('adagio/home/create', 'AdagioAutoController@CreateAdagioIndexHome');
    Route::get('adagio/home/data', 'AdagioAutoController@DataAdagioaHome');
    Route::get('adagio/home/edit', 'AdagioAutoController@DataAdagioaEdit');
    Route::post('adagio/home/update', 'AdagioAutoController@DataAdagioaUpdate');
    Route::post('adagio/home/delete', 'AdagioAutoController@DataAdagioaDelete');
    Route::get('adagio/deletecategory', 'AdagioAutoController@DataAdagioaDeleteCategory');
});

Route::get('adagio/test/view', 'AdagioAutoController@TestView');
//input send file
Route::group(['nav' => 'AP01', 'middleware' => 'permission'], function () {
    Route::get('adagio/send/file', 'AdagioAutoController@AdagioIndexSendFile');
    Route::get('adagio/resume', 'AdagioAutoController@AdagioIndexResume');
    Route::get('adagio/nomor_file', 'AdagioAutoController@AdagioNoFile');
    Route::get('adagio/cek/nomor_file', 'AdagioAutoController@CekAdagioNoFile');
    Route::get('adagio/cek/nomor_file/eo', 'AdagioAutoController@AdagioNoFileEO');
    Route::get('adagio/select/user', 'AdagioAutoController@AdagioDataUser');
    Route::get('adagio/data/approval', 'AdagioAutoController@AdagioDataApproval');
    Route::get('adagio/data/resume', 'AdagioAutoController@AdagioDataResume');
    Route::get('adagio/data/report/{id}', 'AdagioAutoController@AdagioDataReport');
    Route::get('adagio/done/report/{no_transaction}', 'AdagioAutoController@AdagioDataDoneReport');
    Route::get('adagio/data/fetch/{id}', 'AdagioAutoController@AdagioDataFetch');
    Route::post('adagio/send', 'AdagioAutoController@AdagioSendFile');
    Route::get('adagio/create/ulang', 'AdagioAutoController@CreatePDFUlang');
    Route::post('adagio/delete', 'AdagioAutoController@AdagioDelete');
    Route::get('adagio/sendmail/{no_transaction}', 'AdagioAutoController@AdagioSendEmail');
    Route::get('adagio/verivikasi/{no_transaction}/{approver_id}', 'AdagioAutoController@AdagioConfirmation');
    Route::get('adagio/rejected/{no_transaction}/{approver_id}', 'AdagioAutoController@AdagioRejected');
    Route::get('adagio/verivikasi/done/{no_transaction}', 'AdagioAutoController@DoneConfimation');
    Route::get('adagio/rejected/{id}', 'AdagioAutoController@AdagioReject');
    Route::get('adagio/send_email/{id}', 'AdagioAutoController@AdagioEmail');
    Route::get('adagio/monitoring', 'AdagioAutoController@AdagioMonitoring');
    Route::get('adagio/fetch/monitoring', 'AdagioAutoController@AdagioFetchMonitoring');
    Route::get('adagio/report/{id}', 'AdagioAutoController@AdagiReport');
    Route::get('adagio/detail/{id}', 'AdagioAutoController@DetailFile');
    Route::get('index/mirai/approval', 'AdagioAutoController@IndexAdagio');
    Route::get('buat/dokumen/approval', 'AdagioAutoController@DokumenApproval');
    Route::post('delete/file/approval', 'AdagioAutoController@deleteFile');
    Route::get('resume/detail', 'AdagioAutoController@ResumeDetail');
    Route::post('adagio/reject/reason/{no_transaction}', 'AdagioAutoController@ReasonReject');
    Route::get('adagio/hold/{no_transaction}/{approver_id}', 'AdagioAutoController@AdagioHold');
    Route::post('adagio/hold/comment', 'AdagioAutoController@AdagioHoldPost');
    Route::post('adagio/post/reject', 'AdagioAutoController@AdagioRejectPost');
    Route::get('adagio/view/hold/{no_transaction}', 'AdagioAutoController@AdagioViewHold');
    Route::get('adagio/view/aplicant/hold/{no_transaction}', 'AdagioAutoController@AdagioViewAplicantHold');
    Route::post('adagio/send/comment/{no_transaction}', 'AdagioAutoController@AdagioSendPost');
    Route::get('adagio/view/sign/{no_transaction}/{approver_id}', 'AdagioAutoController@AdagioViewSign');
    Route::get('verivikasi/email/{no_transaction}', 'AdagioAutoController@ConfirmationEmail');
    Route::get('hold/email/{no_transaction}', 'AdagioAutoController@AdagioHoldEmail');
    Route::get('rejected/email/{no_transaction}', 'AdagioAutoController@AdagioRejectedEmail');
    Route::get('adagio/cobak/watermark', 'AdagioAutoController@Watermark');
    Route::get('list/kategori/approval', 'AdagioAutoController@IndexKategori');
    Route::get('fetch/kategori/approval', 'AdagioAutoController@FetchKategori');
    Route::post('delete/kategori/approval', 'AdagioAutoController@DeleteKategori');
    Route::post('add/inject/approval', 'AdagioAutoController@AddListKategori');
    Route::post('pindah/posisi/approval', 'AdagioAutoController@MovePosition');
    Route::post('simpan/judul', 'AdagioAutoController@SimpanJudul');
    Route::get('adagio/tanggapan/hold/{no_approval}', 'AdagioAutoController@AdagioIndexTanggapan');
    Route::post('simpan/tanggapan', 'AdagioAutoController@SimpanTanggapan');
    Route::get('cek/urutan/kategori', 'AdagioAutoController@CekUrutanKategori');
    // Route::get('adagio/cek/kirim_email', 'AdagioAutoController@CekKirimEmail');
    // Route::get('adagio/cobak/watermark', function () {
    //     return view('auto_approve.watermark');
    // });
});

//Audit stock ideal vs aktual
Route::group(['nav' => 'S63', 'middleware' => 'permission'], function () {
    Route::get('stock/aktual/monitoring', 'StockAktualController@IndexMonitoring');
    Route::get('stock/resume/aktual', 'StockAktualController@ResumeStockAktual');
    Route::get('stock/grafik/aktual', 'StockAktualController@FatchMonitoring');
    Route::get('stock/aktual/home', 'StockAktualController@AuditStockAktualHome');
    Route::get('stock/aktual/list', 'StockAktualController@FatchListStock');
    Route::get('stock/aktual/edit', 'StockAktualController@EditListStock');
    Route::post('stock/aktual/update', 'StockAktualController@UpdateStock');
    Route::post('stock/aktual/update/ideal', 'StockAktualController@UpdateStockIdeal');
    Route::get('stock/aktual/resume', 'StockAktualController@UpdateStockResume');
    Route::get('stock/ideal/stock', 'StockAktualController@UploadMasterIdeal');
    Route::post('stock/ideal/import', 'StockAktualController@ImportMasterIdeal');
    Route::get('stock/ideal/download', 'StockAktualController@DownloadMasterIdeal');
});

//Warehouse
// Route::get('index/warehouse', 'WarehouseController@index');
// Route::get('scan/data/warehouse', 'WarehouseController@scanInjectionOperator');
// Route::get('scan/data/update', 'WarehouseController@scanOperatorUpdate');
// Route::post('tambah/pekerjaan', 'WarehouseController@create');
// Route::post('warehouse/update_penerimaan', 'WarehouseController@update_Pen');
// Route::get('fetch/display_warehouse', 'WarehouseController@fetchWarehouse');
// Route::get('index/warehouse/productivity', 'WarehouseController@indexWarehouseProductivity');
// Route::get('fetch/warehouse/productivity', 'WarehouseController@warehouseProductivity');
// Route::get('fetch/warehouse/detail', 'WarehouseController@fetchDetail');
// Route::get('index/warehouse/record', 'WarehouseController@indexWarehouseRecord');
// Route::get('fetch/warehouse/record', 'WarehouseController@fetchRecord');

// //warehouse new
// Route::get('index/warehouse/jobs', 'WarehouseController@index_jobs');
// Route::get('index/warehouse/create', 'WarehouseController@index_create');
// Route::get('fetch/warehouse/location', 'WarehouseController@fetchLocation');
// Route::post('post/warehouse/operator/position', 'WarehouseController@postOpWarehousePosition');
// Route::get('fetch/display/rincian', 'WarehouseController@fetchWarehouseRincian');
// Route::get('index/create_job', 'WarehouseController@indexCreateJob');
// Route::post('create/employee/warehouse', 'WarehouseController@createEmployee');

// //Warehouse FIX
// Route::get('index/create_packinglist', 'WarehouseNewController@index');
// Route::post('import/packinglist', 'WarehouseNewController@importPackinglist');
// Route::post('create/suratjalan', 'WarehouseNewController@createNoSuratJalan');
// Route::get('fetch/packinglist/warehouse', 'WarehouseNewController@fetchPackinglist');
// // Route::get('fetch/material', 'WarehouseNewController@fetchMaterialName');

// Route::get('warehouse/internal/{invoice}', 'WarehouseNewController@index_internal');
// Route::get('fetch/list/internal', 'WarehouseNewController@fetchInternal');
// Route::get('fetch/detail/list', 'WarehouseNewController@fetch_detail_list');
// Route::post('post/detail/save', 'WarehouseNewController@createDetail');
// Route::post('post/job/save', 'WarehouseNewController@createJob');
// Route::get('fetch/finish/job', 'WarehouseNewController@finish_job');
// Route::get('fetch/detail/job', 'WarehouseNewController@createDetail');
// Route::get('index/display/job', 'WarehouseNewController@display_job');
// Route::get('fetch/import/job', 'WarehouseNewController@fetch_import');
// Route::post('post/save/penataan', 'WarehouseNewController@savePenataan');
// Route::get('index/drop/exim', 'WarehouseNewController@indexDropExim');
// Route::get('fetch/list/drop/exim', 'WarehouseNewController@fetchDropExim');
// Route::post('post/drop/exim', 'WarehouseNewController@postDropExim');
// Route::get('fetch/drop/exim', 'WarehouseNewController@fetchEximFinish');
// Route::post('post/finish_inter', 'WarehouseNewController@postFinishInter');
// Route::post('post/vendor', 'WarehouseNewController@postVendor');
// Route::get('create/vendor', 'WarehouseNewController@getGmc');
// Route::post('save/vendor', 'WarehouseNewController@updateVendor');
// Route::get('index/pelayanan', 'WarehouseNewController@indexPelayanan');
// Route::post('save/pelayanan', 'WarehouseNewController@postPelayanan');
// Route::get('fetch/pelayanan/job', 'WarehouseNewController@fetch_pelayanan');
// Route::get('index/detail/{kode_request}', 'WarehouseNewController@indexDetailPelayanan');
// Route::get('fetch/detail/request', 'WarehouseNewController@fetchDetailPelayanan');
// Route::post('post/pelayanan', 'WarehouseNewController@savepelayanan');
// Route::post('update/permintaan', 'WarehouseNewController@updatePermintaan');
// Route::get('fetch/history/request', 'WarehouseNewController@fetchRequest');
// Route::get('index/pengantaran/request', 'WarehouseNewController@index_pengantaran');
// Route::get('fetch/pengantaran', 'WarehouseNewController@fetchPengantaran');
// Route::get('fetch/history/pelayanan', 'WarehouseNewController@fetchHistoryPelayanan');
// Route::get('fetch/pengantaran/pelayanan', 'WarehouseNewController@fetchPeng');
// Route::post('update/pengantaran', 'WarehouseNewController@updatePengantaran');
// Route::get('index/import/get_job_now', 'WarehouseNewController@get_job_new');
// Route::get('fetch/lokasi/pengantaran', 'WarehouseNewController@fetchCekPengantaran');
// Route::get('index/pengecekan/request', 'WarehouseNewController@index_pengecekan');

// Route::post('post/pengantaran/lokasi', 'WarehouseNewController@updateLokasi');
// Route::get('get/lokasi', 'WarehouseNewController@getLokasi');
// Route::get('fetch/detail/pengantaran', 'WarehouseNewController@fetchDetailPengantaran');
// Route::get('fetch/detail/pelayanan', 'WarehouseNewController@fetchDetailMaterial');
// Route::post('update/pengecekan', 'WarehouseNewController@updatePengecekanPeng');
// Route::get('index/monitoring/internal', 'WarehouseNewController@index_monitoring');
// Route::get('fetch/status', 'WarehouseNewController@fetchStatus');
// Route::post('post/pengantaran/lokasi_awal', 'WarehouseNewController@updateLokasiAwalPengantaran');
// Route::get('fetch/display_internal', 'WarehouseNewController@fetch_internal_wr');
// Route::get('fetch/detail/penerimaan', 'WarehouseNewController@fetchInternalPenerimaan');
// Route::get('fetch/detail/pelayanan/internal', 'WarehouseNewController@fetchInternalPelayanan');
// Route::get('fetch/detail/import', 'WarehouseNewController@fetchInternalImport');
// Route::get('fetch/resume', 'WarehouseNewController@fetch_internal_wr');
// Route::get('fetch/finish/internal', 'WarehouseNewController@fetchHistoryFinish');
// Route::get('fetch/history/jobs', 'WarehouseNewController@detail_history');
// Route::get('index/shiff/operator/internal', 'WarehouseNewController@indexShiffOperator');
// Route::get('fetch/operator', 'WarehouseNewController@fetch_operator');
// Route::post('update/warehouse_operator', 'WarehouseNewController@updateOperatorWarehouse');
// Route::post('delete/warehouse_operator', 'WarehouseNewController@deleteOperatorWarehouse');
// Route::post('insert/warehouse_operator', 'WarehouseNewController@insertOperatorWarehouse');
// Route::get('fetch/detail/gmc', 'WarehouseNewController@fetchDetaiGmc');
// Route::post('update/status/operator', 'WarehouseNewController@StatusOperatorWarehouse');

// Route::post('fetch/scan', 'WarehouseNewController@ScanPortable');

// Route::get('index/request/produksi', 'WarehouseNewController@requestProd');
// Route::get('fetch/scan/Qrcode', 'WarehouseNewController@ScanQrMaterial');
// Route::post('confirm/request/produksi', 'WarehouseNewController@ConfirmRequestPrd');
// Route::get('fetch/history/request/prod', 'WarehouseNewController@fetchRequestProduksi');
// Route::get('fetch/count/request', 'WarehouseNewController@fetchCountPel');
// Route::get('fetch/count/import', 'WarehouseNewController@fetchCountImport');
// Route::get('fetch/detail/job', 'WarehouseNewController@fetchDetailJob');
// Route::get('fetch/detail/request/prd', 'WarehouseNewController@fetchDetailRequest');
// Route::post('update/pelayanan/proses1', 'WarehouseNewController@updatePelayananJob');
// Route::get('get/operator/joblist', 'WarehouseNewController@getOperatorJoblist');
// Route::post('update/pengecekan/import', 'WarehouseNewController@updatePengecekanImport');
// Route::post('update/pelayanan', 'WarehouseNewController@updatePelayanan');
// Route::get('check/gmc', 'WarehouseNewController@check_gmc');
// Route::get('fetch/count/pengambilan_mt', 'WarehouseNewController@fetchCountPengambilanMt');
// Route::get('fetch/import/request', 'WarehouseNewController@fetchImport');

// Route::post('delete/gmc/packinglist', 'WarehouseNewController@deletegmcPk');
// Route::get('fetch/material_list', 'WarehouseNewController@fetchMaterialRequest');
// Route::get('index/pengantaran', 'WarehouseNewController@index_pengantaran1');

// Route::get('fetch/request/produksi/{form_num}', 'WarehouseNewController@testPdf');
// Route::post('delete/request/prd', 'WarehouseNewController@deleteRqsPrd');

// Route::get('print/kanban/produksi', 'WarehouseNewController@indexPrintKanban');
// Route::get('fetch/list/material', 'WarehouseNewController@fetch_list_material');
// Route::get('reprint/kanban/material/{id}', 'WarehouseNewController@reprintIdKanban');
// Route::get('check/material/kirim', 'WarehouseNewController@indexCheckKirim');
// Route::post('import/kanban/mt', 'WarehouseNewController@importKanban');
// Route::get('approval/request/kanbanurgent/{kode_request}/{user}/{status_app}', 'WarehouseNewController@approvalMtUrgent');

// Route::get('fetch/check/material/import', 'WarehouseNewController@fetchCheckMt');
// Route::get('index/mod_delivery', 'WarehouseNewController@indexMODDelivery');
// Route::get('fetch/mod_delivery', 'WarehouseNewController@fetchMODDetail');
// Route::post('delete/mod_delivery', 'WarehouseNewController@deleteMODDelivery');

//Monitoring

// Route::get('index/warehouse/operatoraktual', 'WarehouseNewController@indexOperatorAktual');
// Route::get('fetch/internal/operatoraktual', 'WarehouseNewController@fetchOperatorAktual');
// Route::get('fetch/internal/detail', 'WarehouseNewController@fetchDetailOperator');

// Request Produksi
// Route::get('fetch/internal/edit/request', 'WarehouseNewController@fetchEditRequest');
// Route::post('post/warehouse/request', 'WarehouseNewController@postEditRequest');

//Create Material
// Route::get('fetch/internal/edit/material', 'WarehouseNewController@fetchEditMaterial');
// Route::post('post/warehouse/edit', 'WarehouseNewController@postEditSJ');
// Route::post('post/warehouse/edit/material', 'WarehouseNewController@postEditNoInv');

// Route::get('index/report/mod_file', 'WarehouseNewController@report_mod_file');
// Route::get('fetch/history/request1', 'WarehouseNewController@fetchHistoryRequest');
// Route::get('fetch/history/detail', 'WarehouseNewController@fetchDetailHistory');
// Route::get('fetch/history/request/subcont', 'WarehouseNewController@fetchHistoryRequestSubcont');

//monitoring operator
// Route::get('index/joblist/operator', 'WarehouseNewController@indexMonitoringOperator');
// Route::get('fetch/joblist/request', 'WarehouseNewController@fetchRqstJob');
// Route::post('post/revisi/mod', 'WarehouseNewController@postEditMOD');
// Route::post('delete/kanban/prd', 'WarehouseNewController@deleteKanbanPrd');
// Route::post('update/kanban/produksi', 'WarehouseNewController@updateKanbanMaterial');
// Route::post('create/kanban/produksi', 'WarehouseNewController@createKanban');
// Route::post('post/revisi/mod/plus', 'WarehouseNewController@postMtPlus');
// Route::get('history/resume/mod', 'WarehouseNewController@ReportResumeMod');
// Route::get('fetch/resume/mod', 'WarehouseNewController@fetchResumeMod');
// Route::get('export/request/mod/{id}', 'WarehouseNewController@exportMOD');
// Route::post('delete/material/request', 'WarehouseNewController@deleteMaterial');
// Route::get('index/joblist/operator/import', 'WarehouseNewController@indexMonitoringOperatorImport');
// Route::post('post/create/joblist', 'WarehouseNewController@SaveJoblistLain');
// Route::get('fetch/create/joblist', 'WarehouseNewController@FetchJoblistLain');
// Route::post('post/update/material', 'WarehouseNewController@UpdateMaterial');
// Route::post('post/update/operator', 'WarehouseNewController@UpdateOperatorJobLain');
// Route::get('fetch/create/joblist/lain', 'WarehouseNewController@fetchCreateJoblistLain');
// Route::get('index/history/check', 'WarehouseNewController@indexCheckHistory');
// Route::get('fetch/history/check', 'WarehouseNewController@fetchCheckHistory');
// Route::post('post/material/update', 'WarehouseNewController@postMaterialUpdate');
// Route::post('post/material/delete', 'WarehouseNewController@postMaterialDelete');

// Route::get('index/history/gmc', 'WarehouseNewController@historyGmcInOut');
// Route::get('fetch/history/gmc', 'WarehouseNewController@fetchGmc');
// Route::get('index/report/mod/all', 'WarehouseNewController@indexReportMOD');

//SUBCONT
// Route::get('index/request/produksi/subcont', 'WarehouseNewController@requestProdSubcont');
// Route::get('fetch/material_list/subcont', 'WarehouseNewController@fetchMaterialRequestSubcont');
// Route::get('fetch/scan/Qrcode/subcont', 'WarehouseNewController@ScanQrMaterialSubcont');
// Route::post('confirm/request/produksi/subcont', 'WarehouseNewController@ConfirmRequestPrdSubcont');
// Route::get('fetch/history/request/prod/subcont', 'WarehouseNewController@fetchRequestProduksiSubcont');
// Route::get('fetch/detail/request/prd/subcont', 'WarehouseNewController@fetchDetailRequestSubcont');
// Route::post('delete/request/prd/subcont', 'WarehouseNewController@deleteRqsPrdSubcont');
// Route::get('index/joblist/operator/subcont', 'WarehouseNewController@indexMonitoringOperatorSubcont');
// Route::get('fetch/display_internal/subcont', 'WarehouseNewController@fetchDetailRequestJoblistSubcont');
// Route::get('fetch/count/request/subcont', 'WarehouseNewController@fetchCountPelSubcont');
// Route::post('update/pelayanan/joblist', 'WarehouseNewController@updatePelayananJobSubcont');
// Route::get('index/detail/subcont/{kode_request}', 'WarehouseNewController@indexDetailPelayananSubcont');
// Route::get('fetch/detail/request/subcont', 'WarehouseNewController@fetchDetailPelayananSubcont');
// Route::post('update/pengecekan/subcont', 'WarehouseNewController@updatePengecekanSubcont');
// Route::get('fetch/count/pengambilanmaterial/subcont', 'WarehouseNewController@fetchCountPengambilanMtSubcont');
// Route::post('update/pengecekan/material/subcont', 'WarehouseNewController@updatePengecekanMaterial');
// Route::post('update/operator/subcont', 'WarehouseNewController@updateOpBan');
// Route::get('index/pengantaran/subcont', 'WarehouseNewController@indexPengantaranSubcont');
// Route::get('fetch/pengantaran/pelayanan/subcont', 'WarehouseNewController@fetchPengSubcont');
// Route::post('update/pengantaran/subcont', 'WarehouseNewController@updatePengantaranSubcont');
// Route::get('fetch/lokasi/pengantaran/subcont', 'WarehouseNewController@fetchCekPengantaranSubcont');
// Route::post('post/pengantaran/lokasi/subcont', 'WarehouseNewController@updateLokasiSubcont');
// Route::get('fetch/request/detail/subcont', 'WarehouseNewController@fetchRqstJobSubcont');
// Route::post('update/pengecekan/subcont/detail', 'WarehouseNewController@updatePengecekanPengSubcont');
// Route::get('fetch/detail/pelayanan/internal/subcont', 'WarehouseNewController@fetchInternalPelayananSubcont');
// Route::post('post/create/joblist/subcont', 'WarehouseNewController@SaveJoblistLainSubcont');
// Route::get('fetch/create/joblist/lain/subcont', 'WarehouseNewController@fetchCreateJoblistLainSubcont');
// Route::get('fetch/create/joblist/subcont', 'WarehouseNewController@fetchJoblistLainSubcont');
// Route::post('post/update/operator/subcont', 'WarehouseNewController@UpdateOperatorJobLainSubcont');
// Route::get('index/warehouse/operatorsubcont', 'WarehouseNewController@indexOperatorSubcont');
// Route::get('fetch/internal/operatoraktual/subcont', 'WarehouseNewController@fetchOperatorSubcont');
// Route::get('fetch/internal/detail/subcont', 'WarehouseNewController@fetchDetailOperatorSubcont');

//Pelaporan Kanagata
Route::get('kanagata/control', 'KanagataController@kanagataControl');
Route::get('kanagata/gmc', 'KanagataController@getGmc');
Route::post('create/pelaporan/kanagata', 'KanagataController@createPelaporanKanagata');
Route::get('kanagata/approval/{request_id}/{remark}', 'KanagataController@kanagataApproval');
Route::get('reject1/kanagata/approval/{request_id}', 'KanagataController@rejectKanagataRequest1');
Route::get('reject/kanagata/approval/{request_id}/{remark}', 'KanagataController@rejectKanagataRequest');
Route::get('kanagata/approval/table', 'KanagataController@fetchKanagataApproval');
Route::get('cancel/pelaporan/kanagata', 'KanagataController@cancelPelaporanKanagataRequest');
Route::get('fetch/kanagata/control', 'KanagataController@fetchMonitoringKanagata');
Route::get('detail/kanagata/{request_id}', 'KanagataController@detailKanagataControl');
Route::get('detail/approval/table', 'KanagataController@fetchKanagataDetail');
Route::get('decision/kanagata/approval/{request_id}/{remark}', 'KanagataController@decisionKanagataRequest');
Route::get('approval/comment/{request_id}/{remark}', 'KanagataController@approvalCommentKanagata');
Route::post('save/approval/comment', 'KanagataController@approvalCommentSave');
Route::get('approval/reject/{request_id}/{remark}', 'KanagataController@approvalRejectKanagata');
Route::post('save/approval/reject', 'KanagataController@approvalRejectSave');

Route::get('kanagata/status/{request_id}/{remark}', 'KanagataController@kanagatastatus');
Route::get('decision/pelaporan/kanagata', 'KanagataController@decisionPelaporanKanagata');
Route::get('history/lifeshoot', 'KanagataController@indexHistoryLifeshoot');
Route::get('fetch/history/lifeshoot', 'KanagataController@fetchHistoryKanagata');
Route::get('resend/kanagata/{request_id}/{remark}', 'KanagataController@ResendkanagataApproval');
Route::post('update/pelaporan/kanagata', 'KanagataController@updatePelaporanKanagata');

Route::get('index/history/kanagata', 'KanagataController@indexKanagataResume');
Route::get('fetch/history/kanagata', 'KanagataController@fetchHistoryKanagataResume');

//sending App
Route::get('index/sending/app', 'SendingAppController@indexSendingApp');
Route::get('sending/cek/nomor', 'SendingAppController@CekNoSendingApp');
Route::get('sending/nomor_file', 'SendingAppController@CekNoSendingApp2');
Route::get('fetch/get/gmc', 'SendingAppController@getGmcList');
Route::post('post/sending/app', 'SendingAppController@createSendingApp');
Route::get('fetch/sending/app', 'SendingAppController@fetchSendingApp');
Route::get('report/sending/{id}', 'SendingAppController@reportSending');

//Sanding
Route::get('index/repair/sanding', 'SandingController@index');
Route::get('scan/repair/operator', 'SandingController@scanInjectionOperator');
Route::get('index/repair/fetch_sanding', 'SandingController@fetchListSanding');
Route::post('input/repair/sanding', 'SandingController@inputSanding');
Route::get('index/repair/fetch_resume_sanding', 'SandingController@fetchResumeSanding');
Route::get('index/sanding/comparison', 'SandingController@indexSandingComparison');
Route::get('fetch/sanding/comparison', 'SandingController@fetchComparison');

//Catalog Item
Route::get('index/catalog_item', 'CatalogController@index_catalog');
Route::post('create/catalog/item', 'CatalogController@create_catalog');
Route::get('fetch/detail/catalog/item', 'CatalogController@fetch_catalog');
Route::get('fetch/edit/catalog', 'CatalogController@fetchEditCatalog');
Route::post('edit/save/catalog', 'CatalogController@editSaveCatalog');
Route::post('delete/catalog', 'CatalogController@deleteCatalog');
Route::get('show/image', 'CatalogController@showImage');
Route::get('fetch/search/item', 'CatalogController@CheckItem');
// Route::get('catalog/get_detail/supplier/{code}', 'CatalogController@cagetsupplier');

//Raw Material Menu
Route::get('index/raw_material_dashboard', 'RawMaterialController@indexRawMaterialDashboard');

//Master Kanban Tools
Route::get('index/tools', 'ToolsController@index_tools');
// Route::get('index/tools/operator', 'ToolsController@index_tools_operator');
Route::get('tools/master', 'ToolsController@master_tools');
Route::get('fetch/tools', 'ToolsController@fetch_tools');
Route::get('index/tools/create', 'ToolsController@create_tools');
Route::post('index/tools/create_post', 'ToolsController@create_tools_post');
Route::get('index/tools/update/{id}', 'ToolsController@update_tools');
Route::get('index/tools/delete/{id}', 'ToolsController@delete_tools');

Route::get('tools/bom', 'ToolsController@tools_bom');
Route::get('fetch/tools_bom', 'ToolsController@fetch_tools_bom');
Route::get('edit/tools_bom', 'ToolsController@edit_tools_bom');

Route::get('tools/request', 'ToolsController@indexRequest');
Route::get('fetch/tools/request', 'ToolsController@fetchRequest');

Route::get('tools/audit', 'ToolsController@indexToolsAudit');
Route::get('fetch/tools/audit', 'ToolsController@fetchToolsAudit');
// Route::post('fetch/case/audit/confirm', 'AssemblyProcessController@fetchCaseAuditConfirm');
// Route::post('fetch/case/audit/delete', 'AssemblyProcessController@fetchCaseAuditDelete');

//Process Pengurangan Stock Kanban Tools
Route::get('tools/stock_out', 'ToolsController@tools_stock_out');
Route::get('fetch/tools/data', 'ToolsController@fetch_tools_data');
Route::get('fetch/tools/order', 'ToolsController@fetch_tools_order');
Route::post('post/tools/stock_out', 'ToolsController@post_tools');

Route::get('tools/log', 'ToolsController@tools_log');
Route::get('fetch/tools/log', 'ToolsController@fetch_tools_log');
Route::get('fetch/tools/log/monitoring', 'ToolsController@fetch_tools_log_monitoring');

//Manage Kanban Tools
Route::get('tools/kanban', 'ToolsController@indexKanbanTools');
Route::get('fetch/tools/kanban', 'ToolsController@fetchKanbanTools');
Route::get('print/tools/kanban/{id}', 'ToolsController@printKanbanTools');

//Process Pengurangan Stock dies
// Route::get('dies/stock_out', 'ToolsController@dies_stock_out');
// Route::get('fetch/dies/data', 'ToolsController@fetch_dies_data');
// Route::post('post/dies/stock_out', 'ToolsController@post_dies');

Route::get('tools/scan/operator', 'ToolsController@scan_operator');

//Tools Calculation
Route::get('tools/calculation', 'ToolsController@tools_calculation');
Route::get('fetch/calculation', 'ToolsController@fetch_tools_calculation');
Route::post('tools/edit_pr', 'ToolsController@edit_pr');

//Tools Calculation OLD
Route::get('tools/calculation/temp', 'ToolsController@tools_calculation_temp');
Route::get('fetch/calculation/temp', 'ToolsController@fetch_tools_calculation_temp');

//Tools Need Order
Route::get('tools/need_order', 'ToolsController@tools_need_order');
Route::get('fetch/tools/need_order', 'ToolsController@fetch_tools_need_order');
Route::get('fetch/tools/itemlist', 'ToolsController@fetchItemList');
Route::get('fetch/tools/get_detailitem', 'ToolsController@toolsDetailItem');
Route::post('tools/create/purchase_requisition', 'ToolsController@create_purchase_requisition');

Route::get('tools/monitoring', 'ToolsController@indexToolsMonitoring');
Route::get('fetch/tools/monitoring', 'ToolsController@fetchToolsMonitoring');

Route::get('tools/bom_progress', 'ToolsController@indexToolsBomProgress');
Route::get('fetch/tools/bom_progress', 'ToolsController@fetchToolsBomProgress');

//Monitoring Stock Dies
Route::get('dies/stock_control', 'ToolsController@dies_control_stock');
Route::get('fetch/dies/stock_control', 'ToolsController@fetch_dies_control_stock');

// QA Check KPP
Route::get('index/qa/kensa_check/{location}', 'QualityAssuranceController@indexKensa');
Route::get('fetch/qa/kpp/check_material', 'QualityAssuranceController@fetchCheckMaterialKPP');
Route::post('input/qa/kensa_check/ng_temp', 'QualityAssuranceController@inputNgKensaTemp');
Route::get('fetch/qa/kensa/ng_temp', 'QualityAssuranceController@fetchNgKensaTemp');
Route::post('input/qa/kensa/ng_log', 'QualityAssuranceController@inputKensaNgLog');
Route::get('delete/qa/kensa/ng_temp', 'QualityAssuranceController@deleteKensaNgTemp');
Route::get('fetch/qa/kensa/detail_record', 'QualityAssuranceController@fetchKensaDetailRecord');

// QA Incoming Check
Route::get('index/qa', 'QualityAssuranceController@index')->name('qa_index');
Route::get('index/qa/incoming_check/{location}', 'QualityAssuranceController@indexIncomingCheck');
Route::get('fetch/qa/check_material', 'QualityAssuranceController@fetchCheckMaterial');
Route::get('fetch/qa/check_serial_number', 'QualityAssuranceController@fetchCheckSerialNumber');
Route::post('input/qa/ng_temp', 'QualityAssuranceController@inputNgTemp');
Route::get('fetch/qa/ng_temp', 'QualityAssuranceController@fetchNgTemp');
Route::get('delete/qa/ng_temp', 'QualityAssuranceController@deleteNgTemp');
Route::get('fetch/qa/ng_list', 'QualityAssuranceController@fetchNgList');
Route::post('input/qa/ng_log', 'QualityAssuranceController@inputNgLog');
Route::get('fetch/qa/detail_record', 'QualityAssuranceController@fetchDetailRecord');

// QA Display Incoming Check
Route::get('index/qa/display/incoming/lot_status', 'QualityAssuranceController@indexDisplayIncomingLotStatus');
Route::get('fetch/qa/display/incoming/lot_status', 'QualityAssuranceController@fetchDisplayIncomingLotStatus');
Route::get('index/qa/display/incoming/material_defect', 'QualityAssuranceController@indexDisplayIncomingMaterialDefect');
Route::get('fetch/qa/display/incoming/material_defect', 'QualityAssuranceController@fetchDisplayIncomingMaterialDefect');
Route::get('fetch/qa/display/incoming/material_select', 'QualityAssuranceController@fetchDisplayIncomingMaterialSelect');
Route::get('fetch/qa/display/incoming/material_defect/detail', 'QualityAssuranceController@fetchDisplayIncomingMaterialDefectDetail');
Route::get('fetch/qa/display/incoming/material_defect/ng_rate/detail', 'QualityAssuranceController@fetchDisplayIncomingMaterialNgRateDetail');
Route::get('index/qa/display/incoming/ng_rate', 'QualityAssuranceController@indexDisplayIncomingNgRate');
Route::get('fetch/qa/display/incoming/ng_rate', 'QualityAssuranceController@fetchDisplayIncomingNgRate');
Route::get('fetch/qa/display/incoming/ng_rate/detail', 'QualityAssuranceController@fetchDisplayIncomingNgRateDetail');

Route::get('index/qa/display/incoming/ng_rate_vendor/{vendor}', 'QualityAssuranceController@indexNgRateVendor');
Route::get('fetch/qa/display/incoming/ng_rate_vendor', 'QualityAssuranceController@fetchNgRateVendor');

Route::get('index/qa/display/incoming/ng_rate/monthly', 'QualityAssuranceController@indexDisplayIncomingNgRateMonthly');
Route::get('fetch/qa/display/incoming/ng_rate/monthly', 'QualityAssuranceController@fetchDisplayIncomingNgRateMonthly');
Route::get('fetch/qa/display/incoming/ng_rate/detail/vendor', 'QualityAssuranceController@fetchDisplayIncomingNgRateDetailVendor');

Route::get('index/qa/display/incoming/ympi', 'QualityAssuranceController@indexIncomingYmpi');
Route::get('fetch/qa/display/incoming/ympi', 'QualityAssuranceController@fetchIncomingYmpi');

Route::get('index/qa/display/incoming/vendor', 'QualityAssuranceController@indexIncomingVendor');
Route::get('fetch/qa/display/incoming/vendor', 'QualityAssuranceController@fetchIncomingVendor');

Route::get('index/qa/display/incoming/vendor/lot_out', 'QualityAssuranceController@indexLotOutVendor');
Route::get('fetch/qa/display/incoming/vendor/lot_out', 'QualityAssuranceController@fetchLotOutVendor');

Route::get('index/qa/display/incoming/qa_meeting', 'QualityAssuranceController@indexDisplayQaMeeting');
Route::get('fetch/qa_meeting/worst_vendor', 'QualityAssuranceController@fetchDisplayQaMeetingWorstVendor');
Route::get('fetch/qa_meeting/ng_rate', 'QualityAssuranceController@fetchDisplayQaMeetingNgRate');
Route::get('fetch/qa_meeting/worst_material', 'QualityAssuranceController@fetchDisplayQaMeetingWorstMaterial');

//QA Report Incoming Check
Route::get('index/qa/report/incoming', 'QualityAssuranceController@indexReportIncomingCheck')->name('report_incoming_qa');
Route::get('fetch/qa/report/incoming', 'QualityAssuranceController@fetchReportIncomingCheck');
Route::get('excel/qa/report/incoming', 'QualityAssuranceController@excelReportIncomingCheck');
Route::get('fetch/qa/report/incoming/edit', 'QualityAssuranceController@fetchReportIncomingCheckEdit');
Route::get('fetch/qa/report/incoming/delete', 'QualityAssuranceController@deleteReportIncomingCheck');
Route::post('update/qa/report/incoming', 'QualityAssuranceController@updateReportIncomingCheck');

Route::get('index/qa/report/outgoing/{vendor}', 'QualityAssuranceController@indexReportOutgoingVendor');
Route::get('fetch/qa/report/outgoing/{vendor}', 'QualityAssuranceController@fetchReportOutgoingVendor');

//KENSA KPP Report
Route::get('index/qa/report/kensa', 'QualityAssuranceController@indexReportKensaCheck')->name('report_kensa_qa');
Route::get('fetch/qa/report/kensa', 'QualityAssuranceController@fetchReportKensaCheck');
Route::get('excel/qa/report/kensa', 'QualityAssuranceController@excelReportKensaCheck');

Route::get('fetch/qa/report/kensa/edit', 'QualityAssuranceController@fetchReportKensaEdit');
Route::get('fetch/qa/report/kensa/delete', 'QualityAssuranceController@deleteReportKensaCheck');
Route::post('update/qa/report/kensa', 'QualityAssuranceController@updateReportKensaCheck');

//QA Report Lot Out Incoming Check
Route::group(['nav' => 'R11', 'middleware' => 'permission'], function () {
    Route::get('index/qa/report/incoming/lot_out', 'QualityAssuranceController@indexReportLotOut');
    Route::get('fetch/qa/report/incoming/lot_out', 'QualityAssuranceController@fetchReportLotOut');
    Route::post('input/qa/report/incoming/lot_out/evidence', 'QualityAssuranceController@inputReportLotOut');
    Route::get('send/qa/report/incoming/lot_out', 'QualityAssuranceController@sendReportLotOut');
});

//QA Kensa Certificate
Route::get('index/qa/certificate', 'QualityAssuranceController@indexCertificate');
Route::get('index/qa/certificate/index', 'QualityAssuranceController@indexCertificate');
Route::get('index/qa/certificate/code', 'QualityAssuranceController@indexCertificateCode');
Route::get('fetch/qa/certificate/code', 'QualityAssuranceController@fetchCertificateCode');
Route::get('resend/qa/certificate/code/{certificate_approval_id}/{remark}', 'QualityAssuranceController@resendCertificateCode');
Route::get('print/qa/certificate/{certificate_id}', 'QualityAssuranceController@printCertificate');
Route::get('renew/qa/certificate/{certificate_id}', 'QualityAssuranceController@renewCertificate');
Route::get('new/qa/certificate', 'QualityAssuranceController@indexNewCertificate');
Route::get('fetch/renew/qa/certificate', 'QualityAssuranceController@fetchRenewCertificate');
Route::post('input/renew/qa/certificate', 'QualityAssuranceController@inputRenewCertificate');
Route::post('input/new/qa/certificate', 'QualityAssuranceController@inputNewCertificate');
Route::get('review/qa/certificate/{certificate_id}/{remark}', 'QualityAssuranceController@reviewCertificate');
Route::get('approve/qa/certificate', 'QualityAssuranceController@certificateApproval');
Route::get('approval/qa/certificate/{remark}', 'QualityAssuranceController@approvalCertificate');
Route::get('approval_all/qa/certificate/{remark}/{certificate_id}', 'QualityAssuranceController@approvalAllCertificate');
Route::get('edit/qa/certificate', 'QualityAssuranceController@editCertificate');
Route::post('update/qa/certificate', 'QualityAssuranceController@updateCertificate');
Route::get('reject/qa/certificate/{remark}/{certificate_id}', 'QualityAssuranceController@rejectCertificate');
Route::get('deactivate/qa/certificate', 'QualityAssuranceController@deactivateCertificate');

Route::get('index/qa/certificate/schedule', 'QualityAssuranceController@indexCertificateSchedule');
Route::get('fetch/qa/certificate/schedule', 'QualityAssuranceController@fetchCertificateSchedule');

Route::get('index/qa/qr_code/certificate', 'QualityAssuranceController@indexCertificateQrCode');
Route::get('print/qa/qr_code/certificate/{certificate_id}', 'QualityAssuranceController@printCertificateQrCode');

Route::get('index/submission/qa/certificate', 'QualityAssuranceController@indexSubmissionCertificate')->name('subsmission_certificate_qa');
Route::get('fetch/submission/qa/certificate', 'QualityAssuranceController@fetchSubmissionCertificate');
Route::post('input/submission/qa/certificate', 'QualityAssuranceController@inputSubmissionCertificate');
Route::get('approval/submission/qa/certificate/new/{remark}/{request_id}', 'QualityAssuranceController@approvalSubmissionCertificateNew');
Route::get('delete/submission/qa/certificate/new/{request_id}', 'QualityAssuranceController@deleteSubmissionCertificateNew');
Route::get('edit/submission/qa/certificate/new/{request_id}', 'QualityAssuranceController@editSubmissionCertificateNew');
Route::get('update/submission/qa/certificate/new/', 'QualityAssuranceController@updateSubmissionCertificateNew');

Route::get('approval/submission/qa/certificate/non/{remark}/{request_id}', 'QualityAssuranceController@approvalSubmissionCertificateNon');
Route::get('delete/submission/qa/certificate/non/{request_id}', 'QualityAssuranceController@deleteSubmissionCertificateNon');
Route::get('edit/submission/qa/certificate/non/{request_id}', 'QualityAssuranceController@editSubmissionCertificateNon');
Route::get('update/submission/qa/certificate/non/', 'QualityAssuranceController@updateSubmissionCertificateNon');

//IN PROCESS
Route::get('new/qa/certificate/inprocess', 'QualityAssuranceController@indexNewCertificateInprocess');
Route::get('fetch/new/qa/certificate/inprocess', 'QualityAssuranceController@fetchNewCertificateInprocess');
Route::post('input/new/qa/certificate/inprocess', 'QualityAssuranceController@inputNewCertificateInprocess');
Route::get('review/qa/certificate/inprocess/{certificate_id}/{remark}', 'QualityAssuranceController@reviewCertificateInprocess');
Route::get('print/qa/certificate/inprocess/{certificate_id}', 'QualityAssuranceController@printCertificateInprocess');
Route::get('edit/qa/certificate/inprocess', 'QualityAssuranceController@editCertificateInprocess');
Route::post('update/qa/certificate/inprocess', 'QualityAssuranceController@updateCertificateInprocess');
Route::get('index/qa/certificate/code/inprocess', 'QualityAssuranceController@indexCertificateCodeInprocess');
Route::get('fetch/qa/certificate/code/inprocess', 'QualityAssuranceController@fetchCertificateCodeInprocess');
Route::get('approval/qa/certificate/inprocess/{remark}', 'QualityAssuranceController@approvalCertificateInprocess');
Route::get('approve/qa/certificate/inprocess', 'QualityAssuranceController@certificateApprovalInprocess');
Route::get('approval_all/qa/certificate/inprocess/{remark}/{certificate_id}', 'QualityAssuranceController@approvalAllCertificateInprocess');
Route::get('reject/qa/certificate/inprocess/{remark}/{certificate_id}', 'QualityAssuranceController@rejectCertificateInprocess');
Route::post('input/renew/qa/certificate/inprocess', 'QualityAssuranceController@inputRenewCertificateInprocess');

Route::get('renew/qa/certificate/inprocess/{certificate_id}', 'QualityAssuranceController@indexRenewCertificateInprocess');

Route::get('index/qa/qr_code/certificate/inprocess', 'QualityAssuranceController@indexCertificateQrCodeInprocess');
Route::get('print/qa/qr_code/certificate/inprocess/{certificate_id}', 'QualityAssuranceController@printCertificateQrCodeInprocess');

//Health Indicator
Route::get('index/health/{loc}', 'HealthController@index');
Route::post('upload/health', 'HealthController@uploadHealth');
Route::get('fetch/health', 'HealthController@fetchHealth');
Route::get('fetch/health/detail', 'HealthController@fetchDetailHealth');

//Oculus
Route::get('index/oculus/auth/{employee_id}', 'OculusController@indexAuth');
Route::get('index/oculus/result/{employee_id}/{answer}/{sub_answer}/{result}', 'OculusController@indexResult');
Route::get('index/oculus/fetch_score/{employee_id}', 'OculusController@fetchResult');

Route::get('index/oculus/user', 'OculusController@indexUser');
Route::get('fetch/oculus/user', 'OculusController@fetchUser');
Route::post('input/oculus/user', 'OculusController@inputUser');
Route::get('delete/oculus/user', 'OculusController@deleteUser');

Route::get('index/oculus/test/report', 'OculusController@indexTestReport');
Route::get('fetch/oculus/test/report', 'OculusController@fetchTestReport');

//Reed Project
Route::get('index/reed', 'ReedSyntheticController@indexReed');
Route::get('scan/reed/operator', 'ReedSyntheticController@scanReedOperator');

//Molding
Route::get('index/reed/molding_verification', 'ReedSyntheticController@indexMoldingVerification');
Route::post('fetch/reed/finish_setup_molding', 'ReedSyntheticController@fetchFinishMolding');
Route::post('fetch/reed/submit_approval', 'ReedSyntheticController@fetchSubmitApproval');
Route::post('fetch/reed/submit_approval_ng', 'ReedSyntheticController@fetchSubmitApprovalNg');
Route::get('index/reed/pre_approval_pdf/{id}', 'ReedSyntheticController@indexPreApprovalPdf');
Route::get('index/reed/approval_pdf/{id}', 'ReedSyntheticController@indexApprovalPdf');

//Injeksi
Route::get('index/reed/injection_order', 'ReedSyntheticController@indexInjectionOrder');
Route::get('fetch/reed/injection_material', 'ReedSyntheticController@fetchInjectionMaterial');
Route::get('fetch/reed/injection_order', 'ReedSyntheticController@fetchInjectionOrder');
Route::post('create/reed/injection_order', 'ReedSyntheticController@createInjectionOrder');
Route::get('index/reed/injection_verification', 'ReedSyntheticController@indexInjectionVerification');
Route::get('fetch/reed/injection_picking_list', 'ReedSyntheticController@fetchInjectionPickingList');
Route::post('fetch/reed/start_injection', 'ReedSyntheticController@fetchStartInjection');
Route::post('fetch/reed/finish_injection', 'ReedSyntheticController@fetchFinishInjection');
Route::post('scan/reed/injection_picking', 'ReedSyntheticController@scanInjectionPicking');
Route::get('index/reed/approval/{location}/{order_id}/{employee_id}', 'ReedSyntheticController@indexApproval');
Route::post('fetch/reed/submit_cdm', 'ReedSyntheticController@fetchSubmitCdm');
Route::get('index/reed/cdm_pdf/{id}', 'ReedSyntheticController@indexCdmPdf');

Route::get('index/reed/injection_report/{id}', 'ReedSyntheticController@indexInjectionReport');
Route::get('fetch/reed/injection_report', 'ReedSyntheticController@fetchInjectionReport');
Route::get('fetch/reed/injection_report_detail', 'ReedSyntheticController@fetchInjectionReportDetail');

Route::get('index/reed/print_label_injection', 'ReedSyntheticController@fetchPrintLabelInjection');
Route::get('index/reed/print_work_order', 'ReedSyntheticController@fetchPrintWorkOrder');

Route::get('index/reed/injection_resin_receive', 'ReedSyntheticController@indexInjectionResinReceive');
Route::get('fetch/reed/injection_resin_receive', 'ReedSyntheticController@fetchInjectionResinReceive');
Route::post('update/reed/injection_delivery', 'ReedSyntheticController@updateInjectionResinDelivery');

//Delivery
Route::get('index/reed/delivery/{loc}', 'ReedSyntheticController@indexDelivery');
Route::get('fetch/reed/inventory', 'ReedSyntheticController@fetchInventory');
Route::get('fetch/reed/check_kanban', 'ReedSyntheticController@fetchCheckKanban');
Route::post('scan/reed/delivery', 'ReedSyntheticController@scanDelivery');

Route::get('fetch/reed/injection_delivery', 'ReedSyntheticController@fetchInjectionDelivery');
Route::get('fetch/reed/update_injection_delivery', 'ReedSyntheticController@fetchUpdateInjectionDelivery');

//Transfer
Route::get('index/reed/transfer', 'ReedSyntheticController@indexTransfer');
Route::post('fetch/reed/transfer', 'ReedSyntheticController@fetchTransfer');

//Laser
Route::get('index/reed/laser_verification', 'ReedSyntheticController@indexLaserVerification');
Route::get('fetch/reed/laser_picking_list', 'ReedSyntheticController@fetchLaserPickingList');
Route::post('scan/reed/laser_picking', 'ReedSyntheticController@scanLaserPicking');
Route::post('fetch/reed/start_laser', 'ReedSyntheticController@fetchStartLaser');
Route::post('fetch/reed/finish_laser', 'ReedSyntheticController@fetchFinishLaser');

//Trimming
Route::get('index/reed/trimming_verification', 'ReedSyntheticController@indexTrimmingVerification');

//Annealing
Route::get('index/reed/annealing_verification', 'ReedSyntheticController@indexAnnealingVerification');

//Packing
Route::get('index/final/reed_synthetic', 'ReedSyntheticController@indexFinalReed');

Route::get('index/reed/packing_order', 'ReedSyntheticController@indexPackingOrder');
Route::get('fetch/reed/packing_material', 'ReedSyntheticController@fetchPackingMaterial');
Route::post('create/reed/packing_order', 'ReedSyntheticController@createPackingOrder');
Route::get('fetch/reed/packing_order', 'ReedSyntheticController@fetchPackingOrder');
Route::get('reprint/reed/packing_order', 'ReedSyntheticController@reprintPackingOrder');

Route::get('index/reed/picking_verification', 'ReedSyntheticController@indexPickingVerification');
Route::get('fetch/reed/packing_picking_list', 'ReedSyntheticController@fetchPackingPickingList');
Route::post('scan/reed/packing_picking', 'ReedSyntheticController@scanPackingPicking');
Route::post('fetch/reed/start_packing', 'ReedSyntheticController@fetchStartPacking');
Route::post('fetch/reed/finish_packing', 'ReedSyntheticController@fetchFinishPacking');

Route::get('index/reed/case_paper_verification', 'ReedSyntheticController@indexCaseSuportPaper');
Route::post('scan/reed/packing_reed_case', 'ReedSyntheticController@scanPackingReedCase');

Route::get('index/reed/packing_verification', 'ReedSyntheticController@indexPackingVerification');
Route::post('scan/reed/packing_box', 'ReedSyntheticController@scanPackingBox');

Route::get('index/final/print_label_item/{material_number}', 'ReedSyntheticController@indexPrintLabelIitem');
Route::get('index/final/print_label_shipment/{material_number}', 'ReedSyntheticController@indexPrintLabelShipment');
Route::get('index/final/print_label_other/{material_number}', 'ReedSyntheticController@fetchPrintOther');

Route::get('index/reed/closure', 'ReedSyntheticController@indexReedClosure');
Route::post('scan/reed/closure', 'ReedSyntheticController@scanReedClosure');

//Warehouse

Route::get('index/reed/label_verification', 'ReedSyntheticController@indexLabelVerification');
Route::get('fetch/reed/label_verification', 'ReedSyntheticController@fetchLabelVerification');
Route::post('post/reed/label_verification', 'ReedSyntheticController@postLabelVerification');

Route::get('index/reed/resin_receive', 'ReedSyntheticController@indexResinReceive');
Route::get('fetch/reed/resin_receive', 'ReedSyntheticController@fetchResinReceive');
Route::post('input/reed/resin_receive', 'ReedSyntheticController@inputResinReceive');
Route::get('print/reed/resin_receive', 'ReedSyntheticController@fetchPrintReceive');

Route::get('index/reed/store_verification', 'ReedSyntheticController@indexStoreVerification');
Route::post('scan/reed/store_verification', 'ReedSyntheticController@scanStoreVerification');

Route::get('index/reed/warehouse_delivery', 'ReedSyntheticController@indexResinDelivery');
Route::get('scan/reed/warehouse_delivery', 'ReedSyntheticController@scanWarehouseDelivery');
Route::post('update/reed/warehouse_delivery', 'ReedSyntheticController@updateWarehouseDelivery');

//End Reed Project

// --------------- SENSOR -------------------

Route::get('index/fibration/data', 'TrialController@indexFIbrationSensor');
Route::get('fetch/fibration/data2', 'TrialController@fetchFIbrationSensor');
Route::get('fetch/fibration/data', 'TrialController@fetchFIbrationSensorData');

Route::get('index/fibration/data/old', 'TrialController@indexFIbrationSensorOld');
Route::get('fetch/fibration/data2/old', 'TrialController@fetchFIbrationSensorOld');

Route::get('index/phpinfo', 'TrialController@indexPhpInfo');
Route::get('fetch/phpinfo', 'TrialController@fetchPhpInfo');

Route::get('index/ph/data', 'TrialController@indexPhSensor');
Route::get('fetch/ph/data', 'TrialController@fetchPhSensor');

Route::get('input/ph/data', 'GeneralAttendanceController@inputPhSensor');
Route::get('input/vibration/data', 'TrialController@inputVibrationSensor');

Route::get('input/Machine/status', 'TrialController@inputMachineSensor');

Route::get('index/maintenance/tpm/temperature', 'MaintenanceController@indexSuhuChiller');
Route::get('input/wtemp/data', 'MaintenanceController@inputSuhuSensor');
Route::get('fetch/maintenance/tpm/temperature', 'MaintenanceController@fetchSuhuChiller');

Route::get('index/maintenance/tpm/pump', 'MaintenanceController@indexPump');

Route::get('input/temp_hum_warehouse', 'MaintenanceController@inputTempHumWH');
Route::get('input/temp_hum', 'GeneralAttendanceController@inputTempHum');
Route::get('input/co2', 'GeneralAttendanceController@inputCo2');

Route::get('input/stamp_new', 'GeneralAttendanceController@inputStamp');

//Dokumentasi Packing

Route::get('index/packing_documentation', 'AuditController@index_packing_documentation');
Route::get('index/packing/documentation/{loc}', 'AuditController@packing_documentation');
Route::post('post/packing_documentation', 'AuditController@documentation_post');
Route::get('fetch/packing_documentation/data', 'AuditController@documentation_data');
Route::get('report/packing_documentation/{loc}', 'AuditController@report_packing_documentation');
Route::get('fetch/report/packing_documentation', 'AuditController@fetch_packing_documentation');
Route::post('delete/packing_documentation', 'AuditController@delete_packing_documentation');
Route::get('monitoring/packing_documentation', 'AuditController@monitoring_packing_documentation');
Route::get('fetch/monitoring/packing_documentation', 'AuditController@fetch_monitoring_packing_documentation');
Route::get('fetch/gmc/packing', 'AuditController@gmc_documentation');
Route::get('fetch/gmc/packing/fl', 'AuditController@gmc_documentation_fl');
Route::get('fetch/gmc/packing/cl', 'AuditController@gmc_documentation_cl');

Route::get('index/packing_outer/documentation/{loc}', 'AuditController@packing_outer_documentation');
Route::post('post/packing_outer_documentation', 'AuditController@documentation_outer_post');
Route::get('report/packing_outer_documentation/{loc}', 'AuditController@report_packing_outer_documentation');
Route::get('fetch/report/packing_outer_documentation', 'AuditController@fetch_packing_outer_documentation');
Route::post('delete/packing_outer_documentation', 'AuditController@delete_packing_outer_documentation');
Route::get('monitoring/packing_outer_documentation', 'AuditController@monitoring_packing_outer_documentation');
Route::get('fetch/monitoring/packing_outer_documentation', 'AuditController@fetch_monitoring_packing_outer_documentation');

Route::get('report/latch/{loc}', 'AuditController@report_latch');
Route::get('fetch/report/latch', 'AuditController@fetch_latch');

//End Dokumentasi Packing

//  -------------------------  FIXED ASSET -------------------------

Route::get('index/fixed_asset', 'AccountingController@indexFixedAsset');
Route::get('index/fixed_asset/map', 'AccountingController@indexFixedAssetMap');
Route::get('fetch/fixed_asset/map', 'AccountingController@fetchFixedAssetMap');
Route::get('index/fixed_asset/report', 'AccountingController@reportFixedAsset');
Route::get('fetch/fixed_asset/report', 'AccountingController@fetchReportFixedAsset');
Route::get('index/fixed_asset/report/detail', 'AccountingController@fetchReportFixedAssetDetail');
Route::post('post/approval/fixed_asset', 'AccountingController@approvalComment');

Route::post('send/fixed_asset/invoice_asset_form', 'AccountingController@assetSendInvoice');

Route::get('index/fixed_asset/monitoring', 'AccountingController@indexFixedAssetMonitoring');
Route::get('fetch/fixed_asset/monitoring', 'AccountingController@fetchFixedAssetMonitoring');
Route::get('fetch/fixed_asset/monitoring/detail', 'AccountingController@fetchFixedAssetMonitoringDetail');

Route::get('index/fixed_asset/monitoring_internal', 'AccountingController@indexFixedAssetMonitoring2');
Route::get('index/fixed_asset/monitoring_approval', 'AccountingController@indexFixedAssetMonitoringApproval');
Route::get('index/fixed_asset/monitoring_approval/{status}', 'AccountingController@indexFixedAssetMonitoringApproval');
Route::get('fetch/fixed_asset/monitoring_approval', 'AccountingController@fetchFixedAssetMonitoringApproval');

Route::get('index/fixed_asset/registration_asset_form', 'AccountingController@indexAssetRegistration');
Route::get('fetch/fixed_asset/registration_asset_form', 'AccountingController@fetchAssetRegistration');
Route::get('fetch/fixed_asset/registration_asset_form/by', 'AccountingController@fetchAssetRegistrationById');
Route::post('send/fixed_asset/registration_asset_form', 'AccountingController@assetRegistration');

Route::get('approval/fixed_asset/{id}/{user}/{stat}', 'AccountingController@approvalAsset');
Route::get('index/fixed_asset/{id}/{user}/{stat}', 'AccountingController@indexApprovalAsset');
Route::post('upload/approval/fixed_asset', 'AccountingController@uploadApprovalAsset');
Route::get('index/approval/fixed_asset/{id}/{stat}', 'AccountingController@indexApprovalAsset2');
Route::post('update/fixed_asset/registration_asset_form', 'AccountingController@updateAssetRegistration');

Route::get('index/fixed_asset/invoice_form', 'AccountingController@indexAssetInvoice');
Route::get('fetch/fixed_asset/invoice_form', 'AccountingController@fetchAssetInvoice');

Route::get('fetch/fixed_asset/testPdf/{form_num}', 'AccountingController@testPdf');

Route::get('index/fixed_asset/transfer_asset', 'AccountingController@indexAssetTransfer');
Route::get('fetch/fixed_asset/transfer_asset', 'AccountingController@fetchAssetTransfer');
Route::post('post/fixed_asset/transfer_asset', 'AccountingController@postAssetTransfer');
Route::post('edit/fixed_asset/transfer_asset', 'AccountingController@editAssetTransfer');
Route::get('approval/fixed_asset/transfer/{id}/{status}/{user}', 'AccountingController@approvalAssetTransfer');
Route::get('report/fixed_asset/missing/{form_number}', 'AccountingController@testPdf2');

Route::get('fetch/fixed_asset/transfer_asset/byId', 'AccountingController@fetchAssetTransferById');
Route::get('fetch/fixed_asset/transfer_asset/byform', 'AccountingController@fetchAssetTransferByForm');

Route::get('index/fixed_asset/label_asset', 'AccountingController@indexLabelAsset');
Route::get('fetch/fixed_asset/label_asset', 'AccountingController@fetchLabelAsset');
Route::post('post/fixed_asset/label_asset', 'AccountingController@postLabelAsset');
Route::post('edit/fixed_asset/label_asset', 'AccountingController@editLabelAsset');
Route::get('approval/fixed_asset/label/{form_number}/{status}/{posisi}', 'AccountingController@approvalLabelAsset');
Route::get('index/approval/fixed_asset_label/{form_number}', 'AccountingController@indexApprovalLabelAsset');

Route::get('index/fixed_asset/print_asset', 'AccountingController@indexPrintAssetList');
Route::get('print/fixed_asset/label/{id}', 'AccountingController@printAsset');
Route::get('print/fixed_asset/label_all/{id}', 'AccountingController@printAssetAll');
Route::post('post/fixed_asset/label_asset/receive_asset', 'AccountingController@receiveLabelAsset');

Route::get('index/fixed_asset/transfer_cip', 'AccountingController@indexTransferCIP');
Route::get('index/fixed_asset/transfer_cip/form/fa_control/{form_id}', 'AccountingController@indexTransferCIPForm');
Route::get('index/fixed_asset_cip/transfer_cip/form_user/{form_number}', 'AccountingController@indexTransferCIPUserForm');
Route::get('index/fixed_asset_cip/transfer_cip/form_user', 'AccountingController@indexTransferCIPUserForm2');
Route::get('fetch/fixed_asset/asset_list', 'AccountingController@fetchFixedAssetList');
Route::post('post/fixed_asset/transfer_cip', 'AccountingController@postFixedAssetCIP');
Route::get('fetch/fixed_asset/transfer_cip', 'AccountingController@fetchFixedAssetCIP');
Route::get('fetch/fixed_asset/asset_cip_list', 'AccountingController@fetchFixedAssetCIPList');
Route::post('post/fixed_asset/asset_cip/send_mail', 'AccountingController@MailFixedAssetCIP');
Route::post('post/fixed_asset/asset_cip/resend_mail', 'AccountingController@ResendReminderCIP');

Route::get('approval/fixed_asset/cip/{form_number}/{status}/{position}', 'AccountingController@approvalFixedAssetCIP');
Route::post('post/fixed_asset/cip/transfer', 'AccountingController@postFixedAssetTransferCIP');
Route::post('post/fixed_asset/cip/true_transfer', 'AccountingController@postFixedAssetCIPNormal');

// ----------- Fixed Asset Special Letter ----------
Route::get('index/fixed_asset_sp_letter/create/{form_number}', 'AccountingController@indexFixedAssetSpLetterForm');
Route::post('post/fixed_asset_sp_letter/create', 'AccountingController@postFixedAssetSpLetterForm');
Route::get('approval/fixed_asset_sp_letter/{status}/{position}/{form_number}', 'AccountingController@approvalFixedAssetSpLetterForm');

Route::get('index/fixed_asset/special_letter', 'AccountingController@indexFixedAssetSpLetter');

Route::get('approval/fixed_asset/transfer_cip/{form_number}/{status}/{position}', 'AccountingController@approvalFixedAssetTransferCIP');
Route::post('post/approval/fixed_asset_cip', 'AccountingController@uploadJurnalFixedAssetCIP');

Route::post('send/mail/fixed_asset', 'AccountingController@sendMailAsset');

Route::get('index/fixed_asset/disposal', 'AccountingController@indexDisposalAsset');
Route::get('fetch/fixed_asset/disposal/byId', 'AccountingController@fetchDisposalAssetById');
Route::get('fetch/fixed_asset/disposal', 'AccountingController@fetchDisposalAsset');
Route::post('post/fixed_asset/disposal', 'AccountingController@postDisposalAsset');
Route::post('fill/fixed_asset/disposal', 'AccountingController@fillDisposalAsset');
Route::post('edit/fixed_asset/disposal', 'AccountingController@EditDisposalAsset');
Route::get('approval/fixed_asset/disposal/{id}/{status}/{user}', 'AccountingController@approvalAssetDisposal');
Route::post('approval/fixed_asset/disposal/new_pic', 'AccountingController@approveDisposalPIC');
Route::post('approval/fixed_asset/disposal/payment', 'AccountingController@approveDisposalPayment');
Route::post('approval/fixed_asset/disposal_scrap/retire_date', 'AccountingController@approveDisposalRetire');
Route::post('approval/fixed_asset/disposal/disposal_date', 'AccountingController@approveDisposalUser');
Route::get('index/approval/fixed_asset_disposal/{id}', 'AccountingController@indexApprovalAssetDisposal');

Route::get('report/fixed_asset/disposal/{form_num}', 'AccountingController@reportPDFDisposal');
Route::get('report/fixed_asset/disposal_scrap/{form_num}', 'AccountingController@reportPDFDisposalScrap');

Route::get('index/fixed_asset/disposal/scrap', 'AccountingController@indexDisposalScrapAsset');
Route::get('fetch/fixed_asset/disposal/scrap', 'AccountingController@fetchDisposalScrapAsset');
Route::post('post/fixed_asset/disposal/scrap', 'AccountingController@postDisposalScrapAsset');
Route::get('approval/fixed_asset/scrap/{id}/{status}/{user}', 'AccountingController@approvalAssetScrap');
Route::get('fetch/fixed_asset/disposal/scrap/byId', 'AccountingController@fetchDisposalScrapAssetById');
Route::post('edit/fixed_asset/disposal/scrap', 'AccountingController@EditDisposalScrapAsset');

Route::get('monitoring/fixed_asset/disposal/scrap', 'AccountingController@MonitoringDisposalScrapAsset');
Route::get('fetch/monitoring/fixed_asset/disposal/scrap', 'AccountingController@fetchMonitoringDisposalScrapAsset');
Route::post('post/monitoring/fixed_asset/disposal/scrap', 'AccountingController@postEvidenceDisposalScrapAsset');
// Route::get('fetch/resume_shipping_order_detail', 'ContainerScheduleController@fetchResumeShippingOrderDetail');

Route::get('index/non_fixed_asset/disposal', 'AccountingController@indexDisposalNonAsset');
Route::get('fetch/non_fixed_asset/disposal/byId', 'AccountingController@fetchDisposalAssetById');
Route::get('fetch/non_fixed_asset/disposal', 'AccountingController@fetchDisposalNonAsset');
Route::get('index/approval/fixed_asset_transfer/{id}', 'AccountingController@indexApprovalAssetTransfer');

Route::post('post/non_fixed_asset/disposal', 'AccountingController@postDisposalNonAsset');

Route::get('index/fixed_asset/missing', 'AccountingController@indexMissingAsset');
Route::get('fetch/fixed_asset/missing', 'AccountingController@fetchMissingAsset');
Route::post('post/fixed_asset/missing', 'AccountingController@postMissingAsset');
Route::get('approval/fixed_asset/missing/{id}/{status}/{user}', 'AccountingController@approvalAssetMissing');
Route::post('fill/fixed_asset/missing', 'AccountingController@fillMissingAsset');
Route::get('fetch/fixed_asset/missing/byId', 'AccountingController@fetchMissingAssetById');
Route::post('edit/fixed_asset/missing', 'AccountingController@EditMissingAsset');
Route::post('approval/fixed_asset/missing/document', 'AccountingController@approveMissingDokumen');
Route::post('approval/fixed_asset/missing/manager', 'AccountingController@approveMissingDokumenManager');

Route::get('index/fixed_asset/audit/{section}/{location}/{period}', 'AccountingController@indexAssetAudit');
Route::get('fetch/fixed_asset/location/list', 'AccountingController@fetchAssetbyLocation');
Route::get('fetch/fixed_asset/byId', 'AccountingController@fetchAssetbyId');
Route::post('input/fixed_asset/audit', 'AccountingController@inputAssetAudit');
Route::post('input/fixed_asset/audit/temp', 'AccountingController@inputAssetAuditTemp');

Route::get('index/fixed_asset/audit/list', 'AccountingController@indexAssetAuditList');
Route::get('index/fixed_asset/auditor_audit/list', 'AccountingController@indexAssetAuditListAuditor');
Route::get('fetch/fixed_asset/audit/list', 'AccountingController@fetchAssetAuditList');
Route::get('fetch/fixed_asset/auditor_audit/list', 'AccountingController@fetchAssetAuditorAuditList');
Route::get('index/check/fixed_asset/{check_num}/{section}/{location}/{period}', 'AccountingController@indexAssetCheck');
Route::post('input/fixed_asset/check', 'AccountingController@inputAssetCheck');
Route::post('input/fixed_asset/check/temp', 'AccountingController@inputAssetCheckTemp');

Route::post('post/fixed_asset/audit/generate', 'AccountingController@generateFixedAssetAudit');
Route::post('update/fixed_asset/photo', 'AccountingController@updateFixedAssetPhoto');
Route::post('approval/fixed_asset/check', 'AccountingController@approvalFixedAsset');
Route::get('fetch/fixed_asset/section/location', 'AccountingController@fetchAssetLocation');
Route::get('fetch/fixed_asset/vendor', 'AccountingController@fetchAssetVendor');
Route::post('upload/fixed_asset/map', 'AccountingController@postAssetMap');
Route::post('upload/fixed_asset/vendor', 'AccountingController@uploadAssetVendor');
Route::post('download/fixed_asset/vendor', 'AccountingController@downloadAssetVendor');
Route::get('fetch/fixed_asset/vendor_type', 'AccountingController@fetchVendorType');

Route::get('approval/fixed_asset/audit/approval/{section}/{period}/{stat}/{position}', 'AccountingController@approvalFixedAssetCheck');
Route::get('report/fixed_asset/asset_check/pdf/{period}/{section}/{location}', 'AccountingController@reportPDFCheck');
Route::get('report/fixed_asset/asset_audit/pdf/{period}/{section}/{location}', 'AccountingController@reportPDFAudit');

Route::post('post/fixed_asset/summary', 'AccountingController@reportPDFSummary');
Route::get('get/fixed_asset/photo_vendor', 'AccountingController@updateVendorPhotoFixedAsset');

Route::get('index/detail/fixed_asset/{status}/{section}/{location}/{period}', 'AccountingController@indexDetailCheckAsset');
Route::get('index/check_report/fixed_asset/{section}/{period}', 'AccountingController@indexAssetCheckReport');
Route::get('fetch/fixed_asset/check/list', 'AccountingController@fetchAssetCheckReport');

Route::get('index/fixed_asset/cip/{asset_list}', 'AccountingController@indexDisposalAssetCip');
Route::post('post/fixed_asset/cip_disposal', 'AccountingController@postDisposalAssetCip');

//  -----------------------  END FIXED ASSET ------------------------

Route::group(['nav' => 'M33', 'middleware' => 'permission'], function () {
    //Server Room
    Route::get('index/server_room', 'PingController@ServerRoom');
    Route::get('index/server_room/{id}', 'PingController@ServerRoomPing');
    Route::get('post/server_room/ping/trend', 'PingController@ServerRoomPingTrend');
    Route::get('post/server_room/network_usage', 'PingController@PostNetworkUsage');
    Route::get('post/server_room/all_app_status', 'PingController@AllHardiskPingStatus');
    Route::get('post/server_room/speedtest', 'PingController@ServerRoomSpeedtest');
    //End Server Room
});

// ----------------------------- WINDS -------------------------

Route::get('winds', 'WindsController@index')->name('winds');
Route::get('winds/fetch/process_list', 'WindsController@fetchProcess');
Route::get('winds/fetch/process_list/antrian', 'WindsController@fetchProcessAntrian');
Route::get('winds/index/description_item/{gmc}/{id_process}/{no}', 'WindsController@IndexItemDetails');
Route::get('winds/master/fetch', 'WindsController@masterfetch');
Route::get('winds/index/cdm/{gmc}/{id_process}/{proc}/{no_proc}', 'WindsController@indexCDM');
Route::post('winds/index/cdm/input', 'WindsController@InsertCDM');
Route::get('winds/index/cdm/detail', 'WindsController@DetailCDM');
Route::get('winds/export/cdm/{gmc}/{proses}', 'WindsController@exportCDM');
Route::get('winds/index/grafik_trendline', 'WindsController@indexMainGrafikTrendline');
Route::get('winds/index/grafik_trendline/{gmc}/{proses}', 'WindsController@indexGrafikTrendline');
Route::get('winds/fetch/grafik_trendline/{gmc}/{proses}', 'WindsController@fetchGrafikTrendline');

Route::get('winds_mpro', 'WindsController@indexWindsMpro')->name('winds_mpro');
Route::get('winds_mpro/master/fetch', 'WindsController@masterfetchMpro');

Route::post('winds/index/description_item/update/{table}', 'WindsController@UpdateItemDetails');
Route::post('winds/index/description_item/delete', 'WindsController@deleteItemDetails');
Route::post('winds/insert/excel_master_checklists', 'WindsController@excelMasterChecklists');

//-------------------------- HR ---------------------------
Route::get('human_resource/gagal', 'HumanResourceController@IndexHrGagal');
Route::get('human_resource/get_employee', 'HumanResourceController@GetEmployee');
Route::get('human_resource/get_section', 'HumanResourceController@GetSection');
Route::post('human_resource/add/uang_pekerjaan', 'HumanResourceController@AddUangPekerjaan');
Route::post('human_resource/add/uang_simpati', 'HumanResourceController@AddUangSimpati');
Route::post('human_resource/add/uang_keluarga', 'HumanResourceController@AddUangKeluarga');
Route::get('human_resource/resume_uang_pekerjaan', 'HumanResourceController@ResumeUangPekerjaan');
Route::get('human_resource/resume_uang_simpati', 'HumanResourceController@ResumeApprovalTunjangan');
Route::get('human_resource/resume_uang_keluarga', 'HumanResourceController@ResumeUangKeluarga');
Route::get('human_resource/detail_pekerjaan/{department}/{bulan}', 'HumanResourceController@DetailUangPekerjaan');
Route::get('human_resource/resume_detail_uang_pekerjaan', 'HumanResourceController@ResumeDetailUangPekerjaan');
Route::get('human_resource/detail_simpati/{id}', 'HumanResourceController@DetailUangSimpati');
Route::get('human_resource/detail_keluarga/{id}', 'HumanResourceController@DetailUangKeluarga');
Route::get('human_resource/approve_simpati_1/{id}', 'HumanResourceController@App_Simpati_1');
Route::get('human_resource/approve_simpati_2/{id}', 'HumanResourceController@App_Simpati_2');
Route::get('human_resource/approve_keluarga_1/{id}', 'HumanResourceController@App_Keluarga_1');
Route::get('human_resource/approve_keluarga_2/{id}', 'HumanResourceController@App_Keluarga_2');
Route::get('human_resource/download/pekerjaan/{department}/{bulan}', 'HumanResourceController@DownloadPekerjaan');
Route::get('human_resource/download/simpati', 'HumanResourceController@DownloadSimpati');
Route::get('human_resource/download/keluarga', 'HumanResourceController@DownloadKeluarga');
Route::get('send/ulang/email/tunjangan/{request_id}', 'HumanResourceController@ResendEmailTunjangan');
Route::get('delete/permohonan/tunjangan/{request_id}', 'HumanResourceController@DeletePermohonanTunjangan');
Route::get('human_resource/tunjangan/kerja', 'HumanResourceController@IndexTunjanganKerja');
Route::get('human_resource/hari_kerja', 'HumanResourceController@GetHariKerja');

Route::get('human_resource/all_approve', 'HumanResourceController@AllApprove');
Route::get('human_resource/resume_request', 'HumanResourceController@ResumeRequest');

Route::get('fetch/karyawan/kontrak', 'HumanResourceController@DataKaryawanKontrak');
Route::get('human_resource/coba/{request_id}', 'HumanResourceController@indexCobaApprove');
// Route::get('human_resource/appproval/{request_id}', 'HumanResourceController@indexCobaApprove');
Route::get('human_resource/appproval/confirm/{request_id}/{approver_id}', 'HumanResourceController@Confirmation');
Route::get('human_resource/rejected/{request_id}', 'HumanResourceController@RejectedRequest');
Route::post('human_resource/rejected/{request_id}', 'HumanResourceController@RejectedRequest_Post');
Route::get('human_resource/comment/{request_id}', 'HumanResourceController@RequestComment');
Route::get('human_resource/comment/reply/{request_id}', 'HumanResourceController@RequestCommentReply');
Route::post('human_resource/comment/{request_id}', 'HumanResourceController@RequestComment_Post');
Route::get('human_resource/comment/msg/{request_id}', 'HumanResourceController@RequestCommentMsg');
Route::get('human_resource/manager/{request_id}', 'HumanResourceController@ApproveManager');
Route::get('human_resource/dgm/{request_id}', 'HumanResourceController@ApproveDGM');
Route::get('index/calon/karyawan/{request_id}', 'HumanResourceController@CalonKaryawan');
Route::get('calon/karyawan/baru', 'HumanResourceController@ResumeCalonKaryawan');
Route::get('index/calon/rekontrak/{request_id}', 'HumanResourceController@CalonRekontrak');
Route::get('calon/rekontrak', 'HumanResourceController@ResumeCalonRekontrak');
Route::post('save/nilai', 'HumanResourceController@saveNilai');
Route::post('acc/kontrak', 'HumanResourceController@accKontrak');

Route::get('input/nilai/peserta', 'HumanResourceController@InputNilaiPeserta');
Route::get('input/nilai/rekontrak', 'HumanResourceController@InputNilaiRekontrak');
// Route::get('index/calon/karyawan/baru/{request_id}', 'HumanResourceController@CalonRekontrak');
Route::get('resume/tunjangan/karyawan', 'HumanResourceController@IndexResumeTunjangan');
Route::get('fetch/resume/tunjangan/karyawan', 'HumanResourceController@FetchResumeTunjangan');
Route::get('download/resume/tunjangan', 'HumanResourceController@DownloadResumeTunjangan');
Route::get('tunjangan/confirm/{request_id}/{approver_id}', 'HumanResourceController@ConfirmationRequestTunjangan');
Route::get('tunjangan/reject/{request_id}/{approver_id}', 'HumanResourceController@RejectedRequestTunjangan');
Route::get('fetch/resume/tunjangan', 'HumanResourceController@FetchTabelResumeTunjangan');
Route::get('/human_resource/portal', function () {
    return view('human_resource.portal');
});

//LEAVE PEMRIT
Route::get('fetch/human_resource/leave_request', 'HumanResourceController@fetchLeaveRequest');
Route::get('fetch/human_resource/leave_request/detail', 'HumanResourceController@fetchLeaveRequestDetail');
Route::get('fetch/human_resource/leave_request/employees', 'HumanResourceController@fetchLeaveRequestEmployees');
Route::post('input/human_resource/leave_request', 'HumanResourceController@inputLeaveRequest');
Route::post('update/human_resource/leave_request', 'HumanResourceController@updateLeaveRequest');
Route::get('approval/human_resource/leave_request/{request_id}/{remark}', 'HumanResourceController@approvalLeaveRequest');
Route::get('reject/human_resource/leave_request/{request_id}/{remark}', 'HumanResourceController@rejectLeaveRequest');
Route::get('reason/human_resource/leave_request', 'HumanResourceController@reasonRejectLeaveRequest');
Route::get('confirm/human_resource/leave_request/driver', 'HumanResourceController@confirmLeaveRequestDriver');
Route::get('index/human_resource/leave_request/security', 'HumanResourceController@indexLeaveRequestSecurity');
Route::get('fetch/human_resource/leave_request/security', 'HumanResourceController@fetchLeaveRequestSecurity');
Route::get('fetch/human_resource/leave_request/security/detail', 'HumanResourceController@fetchLeaveRequestSecurityDetail');
Route::get('scan/human_resource/leave_request/security', 'HumanResourceController@scanLeaveRequestSecurity');
Route::get('confirm/human_resource/leave_request/security', 'HumanResourceController@confirmLeaveRequestSecurity');
Route::get('resend/human_resource/leave_request/{request_id}/{remark}', 'HumanResourceController@resendLeaveRequest');
Route::get('delete/human_resource/leave_request', 'HumanResourceController@deleteLeaveRequest');

Route::get('index/human_resource/leave_request_report', 'HumanResourceController@indexLeaveRequestReport');
Route::get('fetch/human_resource/leave_request_report', 'HumanResourceController@fetchLeaveRequestReport');

//BODY HTS-STOCKROOM
Route::get('index/body/display/board/{loc}', 'BodyController@indexBoard');
Route::get('fetch/body/display/board', 'BodyController@fetchBoard');
Route::get('index/body/kensa/{loc}', 'BodyController@indexKensa');
Route::get('scan/body/operator', 'BodyController@scanOperator');
Route::get('scan/body/kanban', 'BodyController@scanKanban');
Route::get('fetch/body/kensa', 'BodyController@fetchBodyKensa');
Route::post('input/body/kanban', 'BodyController@inputKensaBodySax');

Route::get('index/body/report_ng', 'BodyController@indexReportNG');
Route::get('fetch/body/report_ng', 'BodyController@fetchReportNG');

Route::get('index/body/prod_result', 'BodyController@indexProdResult');
Route::get('fetch/body/prod_result', 'BodyController@fetchProdResult');

Route::get('index/body/resume_ng/{product}', 'BodyController@indexResumeNG');
Route::get('fetch/body/resume_ng', 'BodyController@fetchResumeNG');
Route::get('fetch/body/pareto', 'BodyController@fetchPareto');

Route::get('index/body/rework_result', 'BodyController@indexReworkResult');
Route::get('fetch/body/rework_result', 'BodyController@fetchReworkResult');

Route::get('index/body/resume/{location}', 'BodyController@indexBodyResume');
Route::get('fetch/body/resume', 'BodyController@fetchBodyResume');

//ENTHOL PLATING
Route::get('index/enthol/{location}', 'BodyController@indexEnthol');
Route::get('fetch/enthol/log', 'BodyController@fetchEntolLogs');
Route::get('scan/enthol/kanban', 'BodyController@scanEntholKanban');

//AUDIT CLEAN ROOM LCQ
Route::get('index/daily/audit/{activity_list_id}/{category}', 'ProductionReportController@indexDailyAudit');
Route::get('fetch/daily/audit', 'ProductionReportController@fetchDailyAudit');
Route::post('input/daily/audit', 'ProductionReportController@inputDailyAudit');

Route::get('index/maintenance/compressor', 'ProductionReportController@indexHandlingCompressor');
Route::get('fetch/maintenance/compressor', 'ProductionReportController@fetchHandlingCompressor');
Route::get('doing/maintenance/compressor', 'ProductionReportController@doingHandlingCompressor');
Route::post('input/maintenance/compressor', 'ProductionReportController@inputHandlingCompressor');

Route::get('index/maintenance/steam', 'ProductionReportController@indexHandlingSteam');
Route::get('fetch/maintenance/steam', 'ProductionReportController@fetchHandlingSteam');
Route::get('doing/maintenance/steam', 'ProductionReportController@doingHandlingSteam');
Route::post('input/maintenance/steam', 'ProductionReportController@inputHandlingSteam');

Route::get('index/maintenance/compressor/monitoring', 'ProductionReportController@indexCompressorMonitoring');
Route::get('fetch/maintenance/compressor/monitoring', 'ProductionReportController@fetchCompressorMonitoring');

Route::get('index/maintenance/steam/monitoring', 'ProductionReportController@indexSteamMonitoring');
Route::get('fetch/maintenance/steam/monitoring', 'ProductionReportController@fetchSteamMonitoring');

//VAKSIN
Route::get('index/miraimobile/vaksin_attendance', 'MiraiMobileController@indexVaksinAttendance');
Route::get('fetch/miraimobile/vaksin_attendance', 'MiraiMobileController@fetchVaksinAttendance');
Route::get('fetch/miraimobile/vaksin_attendance_select', 'MiraiMobileController@fetchVaksinAttendanceSelect');
Route::get('fetch/miraimobile/vaksin_attendance/queue', 'MiraiMobileController@fetchVaksinAttendanceQueue');

//Kecelakaan Kerja
Route::get('index/kecelakaan', 'StandardizationController@index');
Route::get('index/kecelakaan/{id}', 'StandardizationController@indexKecelekaan');
Route::get('fetch/kecelakaan/{id}', 'StandardizationController@fetchKecelakaan');
Route::get('detail/kecelakaan', 'StandardizationController@detailKecelakaan');
Route::get('fetch/monitoring/kecelakaan/{id}', 'StandardizationController@fetchMonitoringKecelakaan');
Route::get('index/kecelakaan/report/{id}', 'StandardizationController@reportPDFKecelakaan');
Route::get('index/kecelakaan/sendemail/{id}', 'StandardizationController@sendEmailAll');

Route::group(['nav' => 'S79', 'middleware' => 'permission'], function () {
    Route::post('create/kecelakaan/{id}', 'StandardizationController@createKecelakaan');
    Route::post('edit/kecelakaan', 'StandardizationController@editKecelakaan');
});

Route::get('index/kecelakaan/sosialisasi/{id}', 'StandardizationController@indexKecelakaanSosialisasi');
Route::get('fetch/kecelakaan/sosialisasi/{id}/employee', 'EmployeeController@fetchEmployeeByTag');
Route::post('post/kecelakaan/sosialisasi', 'StandardizationController@postEmployeeData');
Route::get('fetch/kecelakaan_history', 'StandardizationController@fetchEmployeeHistory');
Route::get('chart/kecelakaan', 'StandardizationController@fetch_chart_sosialisasi');
Route::get('chart/kecelakaan/detail', 'StandardizationController@fetch_chart_sosialisasi_detail');
Route::get('chart/yokotenkai', 'StandardizationController@fetch_chart_sosialisasi_yokotenkai');

Route::get('index/yokotenkai/{id}', 'StandardizationController@indexYokotenkai');
Route::get('index/yokotenkai/pdf/{id}', 'StandardizationController@indexYokotenkaiPDF');
Route::post('post/yokotenkai/{id}', 'StandardizationController@postYokotenkai');
Route::get('fetch/yokotenkai', 'StandardizationController@fetchYokotenkai');
Route::get('fetch/yokotenkai/attendance', 'StandardizationController@fetchYokotenkaiAttendance');
Route::post('scan/yokotenkai/attendance', 'StandardizationController@scanEmployeeAttendance');

Route::get('monitoring/yokotenkai', 'StandardizationController@indexMonitoringYokotenkai');
Route::get('fetch/monitoring/yokotenkai', 'StandardizationController@fetchMonitoringYokotenkai');
Route::get('fetch/monitoring/yokotenkai/detail', 'StandardizationController@fetchMonitoringYokotenkaiDetail');

Route::get('monitoring/kecelakaan_kerja', 'StandardizationController@indexMonitoringKecelakaanKerja');
Route::get('fetch/monitoring/kecelakaan_kerja', 'StandardizationController@fetchMonitoringKecelakaanKerja');
Route::get('fetch/monitoring/kecelakaan_kerja/detail', 'StandardizationController@fetchMonitoringKecelakaanKerjaDetail');

// -------------------  FORM PENGECEKAN SAFETY LIBUR -------------

Route::get('index/safety_check', 'StandardizationController@indexSafetyCheck');
Route::get('index/safety_check/form', 'StandardizationController@indexSafetyCheckForm');
Route::post('post/safety_check/form', 'StandardizationController@postSafetyCheckForm');
Route::post('post/safety_check/form2', 'StandardizationController@postSafetyCheckForm2');
Route::get('approval/safety_check/{form_id}/Approve', 'StandardizationController@ApprovalSafetyCheck');
Route::get('index/safety_check/monitoring', 'StandardizationController@indexSafetyCheckMonitoring');
Route::get('fetch/safety_check/monitoring', 'StandardizationController@fetchSafetyCheckMonitoring');
Route::get('index/safety_check/detail/{location}/{param}/{date}', 'StandardizationController@indexSafetyCheckDetail');

// -------------------  CALON KARYAWAN -------------
Route::get('calon/karyawan', 'DataHrController@InputCalonKaryawan');
Route::get('edit/calon/karyawan', 'DataHrController@EditCalonKaryawan');
Route::get('fetch/calon/karyawan', 'DataHrController@IndexCalonKaryawan');
Route::get('fetch/data/calon/karyawan', 'DataHrController@FetchCalonKaryawan');
Route::post('insert/data/calon/karyawan', 'DataHrController@InsertDataCalonKaryawan');
Route::post('update/data/calon/karyawan', 'DataHrController@UpdateDataCalonKaryawan');
Route::post('update/nik/karyawan/baru', 'DataHrController@UpdateNikBaru');
Route::get('download/data/calon/karyawan', 'DataHrController@DownloadExcelCalonKaryawan');
// Route::get('download/data/calon/karyawan', 'DataHrController@DownloadExcelCalonKaryawan');
Route::get('create/pdf/calon/karyawan', 'DataHrController@CreatePDF');
Route::get('get/calon/karyawan', 'DataHrController@getCalonKaryawan');
// Route::get('report/calon/karyawan/{nik}', 'DataHrController@ReportCalonKaryawan');
Route::get('rename', 'TrialController@renameFile');
Route::get('cek/data/employee_update', 'DataHrController@CekDataEmployeeUpdate');

//SDS
Route::get('index/chemical/safety_data_sheet', 'QualityAssuranceController@indexDocumentSDS');
Route::post('input/sds/document', 'QualityAssuranceController@inputSdsDocument');
Route::get('fetch/chemical/document/sds', 'QualityAssuranceController@fetchDocumentSDS');
Route::post('version/chemical/document', 'QualityAssuranceController@versionDocumentSDS');
Route::post('edit/chemical/document', 'QualityAssuranceController@editDocument');
Route::get('index/chemical/sosialisasi', 'QualityAssuranceController@indexUploadSosialisasiData');
Route::post('upload/data/sosialisasi', 'QualityAssuranceController@uploadDataSosialisasi');
Route::post('edit/upload/sosialisasi', 'QualityAssuranceController@editDataSosialisasi');
Route::get('index/documents_sosialisasi/destroy', 'QualityAssuranceController@destroy');
Route::get('chart/sosialisasi/sds', 'QualityAssuranceController@fetch_chart_sosialisasi_sds');
Route::get('index/sosialisasi/data/{id}', 'QualityAssuranceController@indexSDSSosialisasi');
Route::get('scan/sds/participant', 'QualityAssuranceController@scanEmployeeSDS');
Route::post('post/sds/sosialisasi', 'QualityAssuranceController@postEmployeeDataSosil');
Route::get('fetch/employee/sosialisasi', 'QualityAssuranceController@fetchEmployeeSosil');
Route::get('fetch/sds/monitoring/detail', 'QualityAssuranceController@fetchSDSMonitoring');
Route::get('index/sosialisasi/shedule/sds', 'QualityAssuranceController@indexSosialisasiScheduleSDS');
Route::get('fetch/sosialisasi/shedule/sds', 'QualityAssuranceController@fetchSosialisasiScheduleSDS');
Route::post('update/sds/pch', 'QualityAssuranceController@updateDocumentSDSPCH');
Route::get('index/update/sds/{id}', 'QualityAssuranceController@indexSdsAsli');
Route::post('upload/sds/document', 'QualityAssuranceController@uploadSdsDocument');
Route::get('fetch/sds/upload/document', 'QualityAssuranceController@fetch_sds_document');
Route::get('update/sds/expaired', 'QualityAssuranceController@updateSDSExpaired');
Route::get('fetch/edit/sosialisasi', 'QualityAssuranceController@fetchEditSosialisasi');
Route::post('delete/audience/sosialisasi', 'QualityAssuranceController@DeleteAudienceSosialisasi');
Route::post('delete/sds', 'QualityAssuranceController@deleteSDS');
Route::get('get_dept/operator', 'QualityAssuranceController@dataOpSds');

//GS Control
Route::get('index/gs_control', 'GeneralAffairController@indexGSControl');
Route::get('fetch/gs/job', 'GeneralAffairController@fetchJoblistGS');
Route::get('fetch/data/gs', 'GeneralAffairController@fetchDataGS');
Route::get('index/gs_control/new', 'GeneralAffairController@indexGSControlNew');
Route::post('create/job/gs', 'GeneralAffairController@CreateJobsGS');

// gs control new
Route::get('index/process_gs', 'GeneralAttendanceController@indexProcesGS');
Route::get('gs/control', 'GeneralAttendanceController@indexGSNew')->name('gscontrol');
Route::get('fetch/check/op', 'GeneralAttendanceController@fetchCheckOP');
Route::get('fetch/joblist/index', 'GeneralAttendanceController@fetchjoblistIndex');
Route::post('update/gs/job', 'GeneralAttendanceController@updateJobGS');
Route::get('input/reason_pause/gs', 'GeneralAttendanceController@inputReasonPauseGS');
Route::get('update/pause/gs', 'GeneralAttendanceController@UpdatePauseGS');
Route::get('fetch/gs/aktual', 'GeneralAttendanceController@fetchGsAktual');
Route::get('index/monitoring/daily', 'GeneralAttendanceController@indexMonitoringGS');
Route::get('fetch/monitoring/gs/daily', 'GeneralAttendanceController@fetchMonitoringGSAll');
Route::get('index/schedule/gs', 'GeneralAttendanceController@indexScheduleGS');
Route::post('create/job/new', 'GeneralAttendanceController@createJobNewGS');
Route::get('index/gs/operator/job', 'GeneralAttendanceController@indexOpGSJob');
Route::post('create/gs/joblist', 'GeneralAttendanceController@inputJobListGS');
Route::get('fetch/gs/job/daily', 'GeneralAttendanceController@fetchJobGSProcess');
Route::get('delete/joblist/gs', 'GeneralAttendanceController@deleteDataGS');
Route::get('index/gs_resume', 'GeneralAttendanceController@indexResumeGS');
Route::get('fetch/resume/gs', 'GeneralAttendanceController@fetchResumeGS');
Route::get('index/aktual/gs', 'GeneralAttendanceController@indexAktualGS');
Route::get('export/gs/list', 'GeneralAttendanceController@exportGSAll');

//shipping instruction
Route::get('index/shipping/instruction', 'LogisticController@indexShipping');
Route::get('shipping/log/report', 'LogisticController@reportShippingLog');

//Case
Route::get('index/case/menu', 'AssemblyProcessController@indexCaseMenu');
Route::get('index/case', 'AssemblyProcessController@indexCase');
Route::get('fetch/case/list', 'AssemblyProcessController@fetchCaseList');
Route::get('fetch/case/resume', 'AssemblyProcessController@fetchCaseResume');
Route::post('confirm/case', 'AssemblyProcessController@confirmCase');

Route::get('index/case/audit', 'AssemblyProcessController@indexCaseAudit');
Route::get('fetch/case/audit', 'AssemblyProcessController@fetchCaseAudit');
Route::post('fetch/case/audit/confirm', 'AssemblyProcessController@fetchCaseAuditConfirm');
Route::post('fetch/case/audit/delete', 'AssemblyProcessController@fetchCaseAuditDelete');

Route::get('report/case', 'AssemblyProcessController@reportCase');
Route::get('fetch/report/case', 'AssemblyProcessController@fetchReportCase');

Route::get('report/case/audit', 'AssemblyProcessController@reportCaseAudit');
Route::get('fetch/report/case/audit', 'AssemblyProcessController@fetchReportCaseAudit');
Route::get('fetch/report/case/audit/detail', 'AssemblyProcessController@fetchReportCaseAuditDetail');

// --------------------- VISUAL CHECK SANDING ----------------------

Route::get('index/material_check/sanding', 'InitialProcessController@indexVisualCheck');
Route::get('fetch/material_check/sanding', 'InitialProcessController@fetchMaterialData');
Route::post('input/material_check/sanding', 'InitialProcessController@postMaterialCheck');
Route::post('input/material_check/sanding/finish', 'InitialProcessController@postMaterialCheckFinish');
Route::get('index/material_check/finish/sanding/{form_number}', 'InitialProcessController@indexVisualCheckFinish');
Route::get('index/monitoring/material_check/sanding', 'InitialProcessController@indexVisualCheckMonitoring');
Route::get('fetch/monitoring/material_check/sanding', 'InitialProcessController@fetchVisualCheckMonitoring');
Route::post('post/followup/material_check/sanding', 'InitialProcessController@postVisualCheckFollowUp');
Route::post('approval/followup/material_check/sanding', 'InitialProcessController@approvalVisualCheckFollowUp');
Route::get('index/monitoring/productivity/sanding', 'InitialProcessController@indexSandingProductivity');

// Route::get('send/material_check/sanding', 'InitialProcessController@sendMailSanding');

// JAN EAN UPC
Route::get('index/serial_number', 'AssemblyProcessController@indexSerialNumberJanEan');
Route::get('fetch/serial_number', 'AssemblyProcessController@fetchSerialNumberJanEan');
Route::post('input/serial_number', 'AssemblyProcessController@inputSerialNumberJanEan');
Route::post('update/serial_number', 'AssemblyProcessController@updateSerialNumberJanEan');

// ----------- HR PERFORMANCE APPRAISAL ------------------
Route::get('index/report/employee_appraisal', 'HumanResourceController@indexAppraisal');
Route::get('index/report/employee_appraisal/{category}', 'HumanResourceController@indexAppraisalBy');
Route::get('index/employee_appraisal/contract', 'HumanResourceController@indexAppraisalContract');
Route::get('index/employee_appraisal/contract/management/{position}', 'HumanResourceController@indexAppraisalContractManagement');
Route::get('fetch/employee_appraisal/contract', 'HumanResourceController@fetchAppraisalContract');
Route::get('index/employee_appraisal/contract_evaluation', 'HumanResourceController@indexEvaluationContract');
Route::get('index/employee_appraisal/evaluation_form/{employee_id}/{contract_status}/{period}', 'HumanResourceController@indexEvaluationForm');
Route::get('fetch/report/employee_appraisal', 'HumanResourceController@fetchAppraisal');
Route::get('generate/employee_appraisal/employee', 'HumanResourceController@generateEmployee');
Route::post('insert/employee_appraisal/employee', 'HumanResourceController@insertEmployee');
Route::post('post/employee_appraisal/contract/evaluation', 'HumanResourceController@postEvaluationForm');
Route::get('index/employee_appraisal/contract_report', 'HumanResourceController@indexContractReport');
Route::get('excel/employee_appraisal/contract', 'HumanResourceController@excelContractReport');
Route::get('fetch/employee_appraisal/contract_report', 'HumanResourceController@fetchAppraisalContractReport');
Route::get('mail/employee_appraisal/contract_evaluation', 'HumanResourceController@sendEmailEvaluationForm');

Route::post('post/employee_appraisal/contract/final_evaluation', 'HumanResourceController@postFinalEvaluation');
Route::get('index/employee_appraisal/monitoring/contract', 'HumanResourceController@indexMonitoringEvaluation');
Route::get('index/employee_appraisal/monitoring/approval/contract', 'HumanResourceController@indexMonitoringApprovalEvaluation');
Route::get('fetch/employee_appraisal/monitoring/contract', 'HumanResourceController@fetchMonitoringEvaluation');

Route::get('index/employee_appraisal/sp_employee', 'HumanResourceController@indexSPForm');

// Route::group(['nav' => 'S37', 'middleware' => 'permission'], function(){
//     Route::post('delete/case', 'AssemblyProcessController@deleteCase');
// });

// YPM MIS FY199
Route::get('index/warehouse/incoming_check/monitoring', 'WarehouseController@indexIncomingCheckMonitoring');
Route::get('fetch/warehouse/incoming_check/monitoring', 'WarehouseController@fetchIncomingCheckMonitoring');
Route::get('index/warehouse/incoming_check/list', 'WarehouseController@indexIncomingCheckList');
Route::get('fetch/warehouse/incoming_check/list', 'WarehouseController@fetchIncomingCheckList');
Route::post('input/warehouse/incoming_check/list', 'WarehouseController@inputIncomingCheckList');
Route::post('delete/warehouse/incoming_check/list', 'WarehouseController@deleteIncomingCheckList');
Route::get('index/warehouse/incoming_check/create', 'WarehouseController@indexIncomingCheckCreate');
Route::get('index/warehouse/incoming_check', 'WarehouseController@indexIncomingCheck');
Route::get('input/tag', 'TrialController@inputTag');

//Training Filosofi
Route::get('index/filosofi', 'HumanResourceController@indexMonitoringFilosofi');
Route::get('fetch/filosofi', 'HumanResourceController@fetchTrainingFilosofi');
Route::get('training_filosofi', 'HumanResourceController@indexTrainingFilosofi');
Route::get('update/status', 'HumanResourceController@updateTrFilososiOpen');
Route::get('training_filosofi/input', 'HumanResourceController@inpuTrFilosofi');

// ----------------------- EJOR -------------------
Route::get('index/ejor', 'ProductionEngineeringController@indexEjor');
Route::get('fetch/ejor', 'ProductionEngineeringController@fetchEjor');
Route::get('index/ejor/create', 'ProductionEngineeringController@indexEjorForm');
Route::post('input/ejor', 'ProductionEngineeringController@postEjorForm');
Route::post('edit/ejor', 'ProductionEngineeringController@editEjorForm');
Route::post('mail/ejor', 'ProductionEngineeringController@sendMailEjor');
// Route::get('detail/ejor/{form_id}', 'ProductionEngineeringController@detailEjorForm');
Route::get('index/approval/ejor/{form_id}', 'ProductionEngineeringController@indexApprovalEjor');
Route::get('generate/pdf/ejor/{form_id}', 'ProductionEngineeringController@generatePdfEjor');
Route::get('approval/ejor/{form_id}/{status}/{position}', 'ProductionEngineeringController@approvalEjor');
Route::get('index/ejor/monitoring', 'ProductionEngineeringController@indexEjorMonitoring');
Route::get('fetch/ejor/monitoring', 'ProductionEngineeringController@fetchEjorMonitoring');
Route::post('post/ejor/evidence', 'ProductionEngineeringController@postEjorEvidence');
Route::get('verify/ejor/{form_id}/{status}', 'ProductionEngineeringController@indexVerifyEjor');
Route::get('index/verify/ejor/{form_id}', 'ProductionEngineeringController@indexVerifyEjorPage');

// SYSTEM
Route::group(['nav' => 'CREATE_SYS', 'middleware' => 'permission'], function () {
    Route::get('generate/mis/stocktaking_account', 'TicketController@generateStocktakingAccount');
    Route::get('create/code_generator', 'CodeGeneratorController@create');
    Route::post('create/code_generator', 'CodeGeneratorController@store');
    Route::get('create/batch_setting', 'BatchSettingController@create');
    Route::post('create/batch_setting', 'BatchSettingController@store');
    Route::get('create/user', 'UserController@create');
    Route::post('create/user', 'UserController@store');
    Route::get('create/navigation', 'NavigationController@create');
    Route::post('create/navigation', 'NavigationController@store');
    Route::get('create/role', 'RoleController@create');
    Route::post('create/role', 'RoleController@store');
    Route::get('create/status', 'StatusController@create');
    Route::post('create/status', 'StatusController@store');
    Route::get('create/weekly_calendar', 'WeeklyCalendarController@create');
    Route::post('create/weekly_calendar', 'WeeklyCalendarController@store');
    Route::post('import/weekly_calendar', 'WeeklyCalendarController@import');
});
Route::group(['nav' => 'READ_SYS', 'middleware' => 'permission'], function () {
    Route::get('index/code_generator', 'CodeGeneratorController@index');
    Route::get('show/code_generator/{id}', 'CodeGeneratorController@show');
    Route::get('index/batch_setting', 'BatchSettingController@index');
    Route::get('show/batch_setting/{id}', 'BatchSettingController@show');
    Route::get('index/user', 'UserController@index');
    Route::get('show/user/{id}', 'UserController@show');
    Route::get('show/role/{id}', 'RoleController@show');
    Route::get('show/navigation/{id}', 'NavigationController@show');
    Route::get('index/role', 'RoleController@index');
    Route::get('index/navigation', 'NavigationController@index');
    Route::get('index/status', 'StatusController@index');
    Route::get('show/status/{id}', 'StatusController@show');
    Route::get('index/weekly_calendar', 'WeeklyCalendarController@index');
    Route::get('show/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@show');
});
Route::group(['nav' => 'UPDATE_SYS', 'middleware' => 'permission'], function () {
    Route::get('update/mis/stocktaking_account', 'TicketController@updateStocktakingAccount');
    Route::get('edit/code_generator/{id}', 'CodeGeneratorController@edit');
    Route::post('edit/code_generator/{id}', 'CodeGeneratorController@update');
    Route::get('edit/batch_setting/{id}', 'BatchSettingController@edit');
    Route::post('edit/batch_setting/{id}', 'BatchSettingController@update');
    Route::get('edit/user/{id}', 'UserController@edit');
    Route::post('edit/user/{id}', 'UserController@update');
    Route::get('edit/navigation/{id}', 'NavigationController@edit');
    Route::post('edit/navigation/{id}', 'NavigationController@update');
    Route::get('edit/role/{id}', 'RoleController@edit');
    Route::post('edit/role/{id}', 'RoleController@update');
    Route::get('edit/status/{id}', 'StatusController@edit');
    Route::post('edit/status/{id}', 'StatusController@update');
    Route::get('edit/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@edit');
    Route::post('edit/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@update');
});
Route::group(['nav' => 'DELETE_SYS', 'middleware' => 'permission'], function () {
    Route::get('destroy/code_generator/{id}', 'CodeGeneratorController@destroy');
    Route::get('destroy/batch_setting/{id}', 'BatchSettingController@destroy');
    Route::get('destroy/user/{id}', 'UserController@destroy');
    Route::get('destroy/navigation/{id}', 'NavigationController@destroy');
    Route::get('destroy/role/{id}', 'RoleController@destroy');
    Route::get('destroy/status/{id}', 'StatusController@destroy');
    Route::get('destroy/weekly_calendar/{week_name}/{fiscal_year}', 'WeeklyCalendarController@destroy');
});

// MANAGEMENT INFORMATION SYSTEM
Route::group(['nav' => 'CREATE_MIS', 'middleware' => 'permission'], function () {
    Route::post('upload/mis/guideline', 'TicketController@uploadGuideline');
    Route::post('input/ticket/timeline', 'TicketController@inputTicketTimeline');
    Route::post('create/daily_report', 'DailyReportController@create');

    Route::post('input/general/xibo', 'GeneralController@inputXibo');
    Route::post('copy/general/xibo', 'GeneralController@copyXibo');
});
Route::group(['nav' => 'READ_MIS', 'middleware' => 'permission'], function () {
    Route::get('approval/ticket/resend', 'TicketController@approvalTicketResend');
    Route::get('index/daily_report', 'TicketController@indexDailyReport');
    Route::get('fetch/daily_report', 'DailyReportController@fetchDailyReport');
    Route::get('download/daily_report', 'DailyReportController@downloadDailyReport');
    Route::get('fetch/daily_report_detail', 'DailyReportController@fetchDailyReportDetail');

    Route::get('index/general/xibo', 'GeneralController@indexXibo');
    Route::get('fetch/general/xibo', 'GeneralController@fetchXibo');

    Route::get('index/general/xibo/display/{code}', 'GeneralController@indexXiboDisplay');
    Route::get('fetch/general/xibo/display', 'GeneralController@fetchXiboDisplay');

    Route::get('index/member/mis', 'TicketController@MonitoringMember');
    Route::get('fetch/member/mis', 'TicketController@fetchMonitoringMember');

});
Route::group(['nav' => 'UPDATE_MIS', 'middleware' => 'permission'], function () {
    Route::post('edit/ticket', 'TicketController@editTicket');
    Route::post('update/daily_report', 'DailyReportController@update');
    Route::get('edit/daily_report', 'DailyReportController@edit');

    Route::post('update/general/xibo', 'GeneralController@updateXibo');
});
Route::group(['nav' => 'DELETE_MIS', 'middleware' => 'permission'], function () {
    Route::post('delete/daily_report', 'DailyReportController@delete');

    Route::get('delete/general/xibo', 'GeneralController@deleteXibo');
});

// FINANCE
Route::group(['nav' => 'CREATE_FIN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_FIN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_FIN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_FIN', 'middleware' => 'permission'], function () {

});

// LOGISTIC
Route::group(['nav' => 'CREATE_LOG', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_LOG', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_LOG', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_LOG', 'middleware' => 'permission'], function () {

});

// PURCHASING/PROCUREMENT
Route::group(['nav' => 'CREATE_PCH', 'middleware' => 'permission'], function () {
    Route::get('/mail/trade_agreement', 'LicenseController@mailTradeAgreement');
    Route::post('/create/trade_agreement', 'LicenseController@createTradeAgreement');
    Route::post('import/material/smbmr', 'RawMaterialController@importSmbmr');
    Route::post('calculate/material/usage', 'RawMaterialController@calculateUsage');

});
Route::group(['nav' => 'READ_PCH', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_PCH', 'middleware' => 'permission'], function () {
    Route::post('/update/trade_agreement/progress', 'LicenseController@updateTradeAgreementProgress');
    Route::post('/edit/trade_agreement', 'LicenseController@editTradeAgreement');
    Route::post('update/material/material_monitoring/availability_reason', 'RawMaterialController@updateAvailabilityReason');

});
Route::group(['nav' => 'DELETE_PCH', 'middleware' => 'permission'], function () {

});

// PRODUCTION CONTROL
Route::group(['nav' => 'CREATE_PC', 'middleware' => 'permission'], function () {
    Route::post('import/material', 'MaterialController@import');
    Route::post('create/material', 'MaterialController@create');
    Route::get('create/origin_group', 'OriginGroupController@create');
    Route::post('create/origin_group', 'OriginGroupController@store');
    Route::get('create/shipment_condition', 'ShipmentConditionController@create');
    Route::post('create/shipment_condition', 'ShipmentConditionController@store');
    Route::get('create/destination', 'DestinationController@create');
    Route::post('create/destination', 'DestinationController@store');
    Route::post('import/destination', 'DestinationController@import');
    Route::get('create/container', 'ContainerController@create');
    Route::post('create/container', 'ContainerController@store');
    Route::get('create/material_volume', 'MaterialVolumeController@create');
    Route::post('create/material_volume', 'MaterialVolumeController@store');
    Route::post('import/material_volume', 'MaterialVolumeController@import');
    Route::post('upload/sales_order', 'SalesOrderController@uploadSalesOrder');
    Route::post('import/material/storage', 'RawMaterialController@importStorage');

});
Route::group(['nav' => 'READ_PC', 'middleware' => 'permission'], function () {
    Route::get('index/material', 'MaterialController@index');
    Route::get('view/material', 'MaterialController@view');
    Route::get('fetch/material', 'MaterialController@fetchMaterial');
    Route::get('index/material', 'MaterialController@index');
    Route::get('index/origin_group', 'OriginGroupController@index');
    Route::get('show/origin_group/{id}', 'OriginGroupController@show');
    Route::get('show/shipment_condition/{id}', 'ShipmentConditionController@show');
    Route::get('index/shipment_condition', 'ShipmentConditionController@index');
    Route::get('index/destination', 'DestinationController@index');
    Route::get('show/destination/{id}', 'DestinationController@show');
    Route::get('index/container', 'ContainerController@index');
    Route::get('show/container/{id}', 'ContainerController@show');
    Route::get('index/material_volume', 'MaterialVolumeController@index');
    Route::get('show/material_volume/{id}', 'MaterialVolumeController@show');
    Route::get('index/sales_order', 'SalesOrderController@indexSalesOrder');
    Route::get('fetch/sales_order', 'SalesOrderController@fetchSalesOrder');
    Route::get('index/shipment_unmatch', 'ShipmentScheduleController@indexShipmentUnmatch');
    Route::get('fetch/shipment_unmatch', 'ShipmentScheduleController@fetchShipmentUnmatch');

    Route::get('index/shipment_menu', 'ShipmentController@indexShipmentMenu');

});
Route::group(['nav' => 'UPDATE_PC', 'middleware' => 'permission'], function () {
    Route::get('edit/material', 'MaterialController@fetchEdit');
    Route::post('edit/material', 'MaterialController@edit');
    Route::get('edit/origin_group/{id}', 'OriginGroupController@edit');
    Route::post('edit/origin_group/{id}', 'OriginGroupController@update');
    Route::get('edit/shipment_condition/{id}', 'ShipmentConditionController@edit');
    Route::post('edit/shipment_condition/{id}', 'ShipmentConditionController@update');
    Route::get('edit/destination/{id}', 'DestinationController@edit');
    Route::post('edit/destination/{id}', 'DestinationController@update');
    Route::get('edit/container/{id}', 'ContainerController@edit');
    Route::post('edit/container/{id}', 'ContainerController@update');
    Route::get('edit/material_volume/{id}', 'MaterialVolumeController@edit');
    Route::post('edit/material_volume/{id}', 'MaterialVolumeController@update');

    Route::post('sync/ymes/transaction', 'YMESController@syncTransaction');
    Route::post('sync/ymes/transaction_all', 'YMESController@syncTransactionAll');
    Route::post('sync/ymes/shipment', 'YMESController@syncShipment');

    Route::get('index/ymes/setting', 'YMESController@indexInterfaceSetting');
    Route::post('update/ymes/setting', 'YMESController@updateInterfaceSetting');
    Route::get('fetch/ymes/setting_exclude', 'YMESController@fetchSettingExclude');

});
Route::group(['nav' => 'DELETE_PC', 'middleware' => 'permission'], function () {
    Route::post('delete/material', 'MaterialController@delete');
    Route::get('destroy/origin_group/{id}', 'OriginGroupController@destroy');
    Route::get('destroy/shipment_condition/{id}', 'ShipmentConditionController@destroy');
    Route::get('destroy/destination/{id}', 'DestinationController@destroy');
    Route::get('destroy/container/{id}', 'ContainerController@destroy');
    Route::get('destroy/material_volume/{id}', 'MaterialVolumeController@destroy');
    Route::post('delete/ymes/transaction', 'YMESController@deleteTransaction');

});

//bpjskes
Route::get('penambahan/anggota/keluarga', 'HumanResourceController@IndexPendaftaranKeluargaTambahan');
Route::post('insert/bpjskes', 'HumanResourceController@InsertDataBpjskes');
Route::post('insert/bpjskes/detail', 'HumanResourceController@InsertTambahanBpjskes');
Route::post('send/pengajuan', 'HumanResourceController@SendPengajuan');
Route::get('fetch/data/bpjs', 'HumanResourceController@FetchDataBpjskes');
Route::get('index/report/bpjs', 'HumanResourceController@IndexReportBPJS');
Route::get('fetch/data/bpjs/all', 'HumanResourceController@FetchDataBpjskesAll');
Route::get('index/report/bpjs/{id}', 'HumanResourceController@ReportConfirmationHR');
Route::get('fetch/data/update/bpjs', 'HumanResourceController@FetchDataBpjskesUpdate');
Route::post('update/data/detail/bpjs', 'HumanResourceController@UpdateDetailDataBpjs');
Route::post('delete/data/tambahan', 'HumanResourceController@DeleteAnggotaTambahan');
Route::get('open/kk', 'HumanResourceController@OpenKK');
Route::get('berkas/tanda_tangan', 'HumanResourceController@BerkasTandaTangan');
Route::get('download/report/karyawan/bpjs', 'HumanResourceController@DownloadReportBpjs');

// HUMAN RESOURCES
Route::group(['nav' => 'CREATE_HR', 'middleware' => 'permission'], function () {
    Route::post('select/match/data', 'HumanResourceController@SelectMatchData');
    Route::post('email/karyawan_bermasalah', 'HumanResourceController@TestMailKaryawanBermasalah');
    Route::post('insert/employee/end_date', 'HumanResourceController@InsertEmployeeEndContrect');

    Route::post('insert/anak/karyawan', 'HumanResourceController@InsertAnakKaryawan');

    Route::post('input/human_resource/let/point_check', 'HumanResourceController@inputLetPointCheck');
    Route::post('input/human_resource/let/master', 'HumanResourceController@inputLetMaster');

});
Route::group(['nav' => 'READ_HR', 'middleware' => 'permission'], function () {
    //resume HR
    Route::get('index/resume_all_hr', 'HumanResourceController@IndexResumeAllHr');

    Route::get('index/shift_schedule/karyawan', 'HumanResourceController@IndexShiftSchedule');
    Route::get('fetch/data/shift_schedule', 'HumanResourceController@FetchShiftSchedule');

    //Pencabutan Tunjangan
    Route::get('index/pencabutan_tunjangan/karyawan', 'HumanResourceController@IndexPencabutanTunjangan');
    Route::get('fetch/data/pencabutan_tunjangan', 'HumanResourceController@FetchPencabutanTunjangan');
    Route::get('data/cutomfield/sunfish', 'HumanResourceController@CustomField');
    Route::get('fetch/detail/customfield', 'HumanResourceController@FetchCustomField');

    //Karyawan Bermasalah
    Route::get('index/karyawan_bermasalah', 'HumanResourceController@IndexKaryawanBermasalah');
    Route::get('fetch/data/karyawan/bermasalah', 'HumanResourceController@FetchDataKaryawan');
    Route::get('fetch/data/karyawan_bermasalah/detail', 'HumanResourceController@FetchKaryawanBermasalahDetail');
    Route::get('view/email/karyawan_bermasalah/detail', 'HumanResourceController@EmailKaryawanBermasalah');
    Route::get('fetch/data/indikasi/karyawan/bermasalah', 'HumanResourceController@FetchIndikasiPelanggaranKehadiran');

    //Karyawan Putus Kontrak
    Route::get('index/employee_end_contract', 'HumanResourceController@IndexOperatorEndDate');
    Route::get('fetch/employee_end_contract', 'HumanResourceController@FetchOperatorEndDate');
    Route::get('grafik/employee_end_contract', 'HumanResourceController@GrafikOperatorEndDate');

    //karyawan bermasalah
    Route::post('konseling/karyawan/bermasalah', 'HumanResourceController@InputKonseling');

    //Smart Recruitment

    //LET
    Route::get('index/human_resource/let/point_check', 'HumanResourceController@indexLetPointCheck');
    Route::get('fetch/human_resource/let/point_check', 'HumanResourceController@fetchLetPointCheck');

    Route::get('index/human_resource/let/master', 'HumanResourceController@indexLetMaster');
    Route::get('fetch/human_resource/let/master', 'HumanResourceController@fetchLetMaster');

    Route::get('index/human_resource/let/report', 'HumanResourceController@indexLetReport');
    Route::get('fetch/human_resource/let/report', 'HumanResourceController@fetchLetReport');
});
Route::group(['nav' => 'UPDATE_HR', 'middleware' => 'permission'], function () {
    //bpjskes
    Route::get('confirmation/pengajuan/anggota/bpjs', 'HumanResourceController@ConfirmationAnggotaBpjs');

    Route::post('update/human_resource/let/point_check', 'HumanResourceController@updateLetPointCheck');
    Route::post('update/human_resource/let/master', 'HumanResourceController@updateLetMaster');
});
Route::group(['nav' => 'DELETE_HR', 'middleware' => 'permission'], function () {
    Route::get('delete/human_resource/let/point_check', 'HumanResourceController@deleteLetPointCheck');
    Route::get('delete/human_resource/let/master', 'HumanResourceController@deleteLetMaster');
});

// GENERAL AFFAIRS
Route::group(['nav' => 'CREATE_GA', 'middleware' => 'permission'], function () {
    Route::post('input/translation_meeting', 'TranslationController@inputMeeting');
    Route::post('input/translation_pic', 'TranslationController@inputPIC');
    Route::post('input/translation_result', 'TranslationController@inputResult');
});
Route::group(['nav' => 'READ_GA', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_GA', 'middleware' => 'permission'], function () {
    Route::post('edit/translation', 'TranslationController@editTranslation');
    Route::post('edit/translation_meeting', 'TranslationController@editMeeting');
    Route::post('edit/ga_control/locker', 'GeneralAffairController@editLocker');
});
Route::group(['nav' => 'DELETE_GA', 'middleware' => 'permission'], function () {
    Route::post('delete/translation', 'TranslationController@deleteTranslation');
});

// PRODUCTION
Route::group(['nav' => 'CREATE_PRD', 'middleware' => 'permission'], function () {
    Route::post('input/efficiency/diversion', 'EfficiencyController@inputEfficiencyDiversion');
    Route::post('upload/efficiency/diversion', 'EfficiencyController@uploadEfficiencyDiversion');
    Route::post('upload/efficiency/material', 'EfficiencyController@uploadEfficiencyMaterial');
});
Route::group(['nav' => 'READ_PRD', 'middleware' => 'permission'], function () {
    Route::get('index/efficiency/dashboard', 'EfficiencyController@indexEfficiencyDashboard');
    Route::get('index/efficiency/manpower/{id}', 'EfficiencyController@indexEfficiencyManpower');
    Route::get('index/efficiency/material/{id}', 'EfficiencyController@indexEfficiencyMaterial');
    Route::get('index/efficiency/standard_time/{id}', 'EfficiencyController@indexEfficiencyStandardTime');
    Route::get('index/efficiency/input/{id}', 'EfficiencyController@indexEfficiencyInput');
    Route::get('index/efficiency/output/{id}', 'EfficiencyController@indexEfficiencyOutput');

    Route::get('fetch/efficiency/manpower', 'EfficiencyController@fetchEfficiencyManpower');
    Route::get('fetch/efficiency/material', 'EfficiencyController@fetchEfficiencyMaterial');
    Route::get('fetch/efficiency/standard_time', 'EfficiencyController@fetchEfficiencyStandardTime');
    Route::get('fetch/efficiency/input', 'EfficiencyController@fetchEfficiencyInput');
    Route::get('fetch/efficiency/output', 'EfficiencyController@fetchEfficiencyOutput');
    Route::get('fetch/efficiency/output_det', 'EfficiencyController@fetchEfficiencyOutput');

    Route::get('fetch/efficiency/diversion', 'EfficiencyController@fetchEfficiencyDiversion');
    Route::get('fetch/efficiency/output_detail', 'EfficiencyController@fetchEfficiencyOutputDetail');

});
Route::group(['nav' => 'UPDATE_PRD', 'middleware' => 'permission'], function () {
    Route::post('edit/efficiency/manpower_add', 'EfficiencyController@editEfficiencyManpowerAdd');
    Route::post('edit/efficiency/manpower_edit', 'EfficiencyController@editEfficiencyManpowerEdit');
    Route::post('edit/efficiency/material_add', 'EfficiencyController@editEfficiencyMaterialAdd');
    Route::post('edit/efficiency/material_edit', 'EfficiencyController@editEfficiencyMaterialEdit');
    Route::post('edit/efficiency/diversion', 'EfficiencyController@editEfficiencyDiversion');

});
Route::group(['nav' => 'DELETE_PRD', 'middleware' => 'permission'], function () {
    Route::post('edit/efficiency/manpower_remove', 'EfficiencyController@editEfficiencyManpowerRemove');
    Route::post('edit/efficiency/material_remove', 'EfficiencyController@editEfficiencyMaterialRemove');
    Route::post('delete/efficiency/diversion', 'EfficiencyController@deleteEfficiencyDiversion');
});

// QUALITY ASSURANCE
Route::group(['nav' => 'CREATE_QA', 'middleware' => 'permission'], function () {
    //Audit Proses Khusus
    Route::post('input/audit/qa/special_process', 'QualityAssuranceController@inputAuditSpecialProcess');
    Route::post('input/audit/qa/special_process/continue', 'QualityAssuranceController@inputAuditSpecialProcessContinue');
    Route::post('input/qa/special_process/point_check', 'QualityAssuranceController@inputSpecialProcessPointCheck');
    Route::post('input/qa/special_process/schedule', 'QualityAssuranceController@inputSpecialProcessSchedule');

    //Audit QC Koteihyo
    Route::post('input/qa/qc_koteihyo/point_check', 'QualityAssuranceController@inputQcKoteihyoPointCheck');
    Route::post('upload/qa/qc_koteihyo/point_check', 'QualityAssuranceController@uploadQcKoteihyoPointCheck');

    //Audit Packing
    Route::post('input/qa/packing/point_check', 'QualityAssuranceController@inputPackingPointCheck');
    Route::post('input/qa/packing/audit', 'QualityAssuranceController@inputPackingAudit');

    //Audit CPAR & CAR
    Route::post('input/qa/cpar_car/point_check', 'QualityAssuranceController@inputAuditCparCarPointCheck');
    Route::post('input/qa/cpar_car/schedule', 'QualityAssuranceController@inputAuditCparCarSchedule');

    Route::post('input/qa/cpar_car/audit', 'QualityAssuranceController@inputAuditCparCar');

    Route::post('input/qa/feeling/audit', 'QualityAssuranceController@inputAuditFeelingAudit');

    Route::post('input/qa/special_process/verification', 'QualityAssuranceController@inputSpecialProcessVerification');

    //AUDIT FG
    Route::post('input/qa/audit_fg/point_check', 'QualityAssuranceController@inputAuditFGPointCheck');
    Route::post('input/qa/audit_fg/audit', 'QualityAssuranceController@inputAuditFGAudit');

    Route::post('input/qa/ik/audit', 'QualityAssuranceController@inputAuditIKAudit');
});
Route::group(['nav' => 'READ_QA', 'middleware' => 'permission'], function () {
    //Audit Proses Khusus
    Route::get('index/qa/special_process/point_check/{category}', 'QualityAssuranceController@indexSpecialProcessPointCheck');
    Route::get('fetch/qa/special_process/point_check', 'QualityAssuranceController@fetchSpecialProcessPointCheck');

    Route::get('audit/qa/special_process/{id}', 'QualityAssuranceController@auditSpecialProcess');
    Route::get('audit/continue/qa/special_process/{id}', 'QualityAssuranceController@auditSpecialProcessContinue');

    Route::get('index/qa/special_process/schedule', 'QualityAssuranceController@indexSpecialProcessSchedule');
    Route::get('fetch/qa/special_process/schedule', 'QualityAssuranceController@fetchSpecialProcessSchedule');

    Route::get('index/qa/special_process/verification/{schedule_id}', 'QualityAssuranceController@indexSpecialProcessVerification');

    //Audit QC Koteihyo
    Route::get('index/qa/qc_koteihyo', 'QualityAssuranceController@indexQcKoteihyo');
    Route::get('fetch/qa/qc_koteihyo', 'QualityAssuranceController@fetchQcKoteihyo');

    Route::get('index/qa/qc_koteihyo/point_check', 'QualityAssuranceController@indexQcKoteihyoPointCheck');
    Route::get('fetch/qa/qc_koteihyo/point_check', 'QualityAssuranceController@fetchQcKoteihyoPointCheck');

    Route::get('download/qa/qc_koteihyo/point_check', 'QualityAssuranceController@downloadQcKoteihyoPointCheck');

    Route::get('index/assembly/resume_qa/{origin_group}', 'AssemblyProcessController@indexResumeQA');
    Route::get('fetch/assembly/resume_qa', 'AssemblyProcessController@fetchResumeQA');

    //Audit Packing
    Route::get('index/qa/packing/point_check', 'QualityAssuranceController@indexPackingPointCheck');
    Route::get('fetch/qa/packing/point_check', 'QualityAssuranceController@fetchPackingPointCheck');
    Route::get('fetch/qa/packing/serial_number', 'QualityAssuranceController@fetchPackingSerialNumber');
    Route::get('index/qa/packing/audit', 'QualityAssuranceController@indexPackingAudit');

    //Audit CPAR & CAR
    Route::get('index/qa/cpar_car/point_check', 'QualityAssuranceController@indexAuditCparCarPointCheck');
    Route::get('fetch/qa/cpar_car/point_check', 'QualityAssuranceController@fetchAuditCparCarPointCheck');

    Route::get('index/qa/cpar_car/schedule', 'QualityAssuranceController@indexAuditCparCarSchedule');
    Route::get('fetch/qa/cpar_car/schedule', 'QualityAssuranceController@fetchAuditCparCarSchedule');

    //PARETO QA
    Route::get('index/assembly/pareto/{origin_group}', 'AssemblyProcessController@indexAssemblyPareto');
    Route::get('fetch/assembly/pareto', 'AssemblyProcessController@fetchAssemblyPareto');

    //AUDIT FG
    Route::get('index/qa/audit_fg', 'QualityAssuranceController@indexAuditFG');
    Route::get('fetch/qa/audit_fg', 'QualityAssuranceController@fetchAuditFG');
    Route::get('index/qa/audit_fg/point_check', 'QualityAssuranceController@indexAuditFGPointCheck');
    Route::get('fetch/qa/audit_fg/point_check', 'QualityAssuranceController@fetchAuditFGPointCheck');

    Route::get('index/qa/audit_fg/audit', 'QualityAssuranceController@indexAuditFGAudit');

    Route::get('index/qa/audit_fg/edit/{audit_id}', 'QualityAssuranceController@indexAuditFGEdit');
    Route::get('index/qa/audit_fg/delete/{audit_id}', 'QualityAssuranceController@indexAuditFGDelete');
});
Route::group(['nav' => 'UPDATE_QA', 'middleware' => 'permission'], function () {
    //Audit Proses Khusus
    Route::get('edit/qa/special_process/point_check', 'QualityAssuranceController@editSpecialProcessPointCheck');
    Route::post('update/qa/special_process/point_check', 'QualityAssuranceController@updateSpecialProcessPointCheck');
    Route::post('update/qa/special_process/point_safety', 'QualityAssuranceController@updateSpecialProcessPointSafety');
    Route::post('update/qa/special_process/schedule', 'QualityAssuranceController@updateSpecialProcessSchedule');

    //Audit QC Koteihyo
    Route::get('edit/qa/qc_koteihyo/point_check', 'QualityAssuranceController@editQcKoteihyoPointCheck');
    Route::post('update/qa/qc_koteihyo/point_check', 'QualityAssuranceController@updateQcKoteihyoPointCheck');

    //Audit Packing
    Route::get('edit/qa/packing/point_check', 'QualityAssuranceController@editPackingPointCheck');
    Route::post('update/qa/packing/point_check', 'QualityAssuranceController@updatePackingPointCheck');

    //Audit CPAR & CAR
    Route::get('edit/qa/cpar_car/point_check', 'QualityAssuranceController@editAuditCparCarPointCheck');
    Route::post('update/qa/cpar_car/point_check', 'QualityAssuranceController@updateAuditCparCarPointCheck');
    Route::post('update/qa/cpar_car/schedule', 'QualityAssuranceController@updateAuditCparCarSchedule');

    //AUDIT FG
    Route::get('edit/qa/audit_fg/point_check', 'QualityAssuranceController@editAuditFGPointCheck');
    Route::post('update/qa/audit_fg/point_check', 'QualityAssuranceController@updateAuditFGPointCheck');

    Route::post('input/qa/audit_fg/update', 'QualityAssuranceController@indexAuditFGUpdate');
});
Route::group(['nav' => 'DELETE_QA', 'middleware' => 'permission'], function () {
    //Audit Proses Khusus
    Route::get('delete/qa/special_process/point_check', 'QualityAssuranceController@deleteSpecialProcessPointCheck');
    Route::get('delete/qa/special_process/schedule', 'QualityAssuranceController@deleteSpecialProcessSchedule');

    //Audit QC Koteihyo
    Route::get('delete/qa/qc_koteihyo/point_check', 'QualityAssuranceController@deleteQcKoteihyoPointCheck');

    //Audit Packing
    Route::get('delete/qa/packing/point_check', 'QualityAssuranceController@deletePackingPointCheck');

    //Audit CPAR & CAR
    Route::get('delete/qa/cpar_car/point_check', 'QualityAssuranceController@deleteAuditCparCarPointCheck');
    Route::get('delete/qa/cpar_car/schedule', 'QualityAssuranceController@deleteAuditCparCarSchedule');
});

Route::group(['nav' => 'S79', 'middleware' => 'permission'], function () {
});

// STANDARDIZATION
Route::group(['nav' => 'CREATE_STD', 'middleware' => 'permission'], function () {
    Route::post('input/standardization/document', 'StandardizationController@inputDocument');

    Route::post('input/standardization/ypm/evaluation', 'StandardizationController@inputYPMEvaluation');
    Route::post('input/standardization/ypm/contest', 'StandardizationController@inputYPMEvaluationContest');

    Route::post('input/standardization/ypm/point_check', 'StandardizationController@inputYPMPointCheck');
    Route::post('input/standardization/ypm/master', 'StandardizationController@inputYPMMaster');
    Route::post('upload/standardization/ypm/master', 'StandardizationController@uploadYPMMaster');
    Route::post('upload/standardization/ypm/pdf', 'StandardizationController@uploadYPMPDF');

    Route::post('input/standardization/ypm/hadiah', 'StandardizationController@inputYPMHadiah');
});
Route::group(['nav' => 'READ_STD', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_STD', 'middleware' => 'permission'], function () {
    // VEHICLE
    Route::post('edit/standardization/document', 'StandardizationController@editDocument');
    Route::post('version/standardization/document', 'StandardizationController@versionDocument');
    Route::post('update/standardization/vehicle_inspection', 'StandardizationController@updateVehicleInspection');

    Route::post('update/standardization/ypm/evaluation', 'StandardizationController@updateYPMEvaluation');

    Route::post('update/standardization/ypm/point_check', 'StandardizationController@updateYPMPointCheck');
    Route::post('update/standardization/ypm/master', 'StandardizationController@updateYPMMaster');
});
Route::group(['nav' => 'DELETE_STD', 'middleware' => 'permission'], function () {
    // VEHICLE
    Route::post('delete/standardization/document', 'StandardizationController@deleteDocument');
    Route::post('delete/standardization/vehicle_inspection', 'StandardizationController@deleteVehicleInspection');
    Route::post('delete/standardization/document/version', 'StandardizationController@deleteDocumentVersion');

    Route::post('delete/standardization/ypm/point_check', 'StandardizationController@deleteYPMPointCheck');
    Route::post('delete/standardization/ypm/master', 'StandardizationController@deleteYPMMaster');

    Route::post('delete/standardization/ypm/evaluation', 'StandardizationController@deleteYPMEvaluation');
});

// CHEMICAL
Route::group(['nav' => 'CREATE_CHM', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_CHM', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_CHM', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_CHM', 'middleware' => 'permission'], function () {

});

// PRODUCTION ENGINEERING
Route::group(['nav' => 'CREATE_PE', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_PE', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_PE', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_PE', 'middleware' => 'permission'], function () {

});

// MAINTENANCE
Route::group(['nav' => 'CREATE_MTC', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_MTC', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_MTC', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_MTC', 'middleware' => 'permission'], function () {

});

// STOCKTAKING
Route::group(['nav' => 'CREATE_ST', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_ST', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_ST', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_ST', 'middleware' => 'permission'], function () {

});

// LEADER CONTROL
Route::group(['nav' => 'CREATE_LEADER', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_LEADER', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_LEADER', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_LEADER', 'middleware' => 'permission'], function () {

});

// INJECTION
Route::group(['nav' => 'CREATE_WIP-INJ', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WIP-INJ', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_WIP-INJ', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-INJ', 'middleware' => 'permission'], function () {

});

// RECORDER
Route::group(['nav' => 'CREATE_WIP-RC', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WIP-RC', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_WIP-RC', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-RC', 'middleware' => 'permission'], function () {

});

// MOUTHPIECE
Route::group(['nav' => 'CREATE_WIP-MP', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WIP-MP', 'middleware' => 'permission'], function () {
    // Stock Mouthpiece Monitoring
    Route::get('index/mouthpiece_process', 'MouthpieceController@IndexMouthpieceProcess');
    Route::get('mouthpiece/process/in', 'MouthpieceController@IndexMouthpieceProcessIN');
    Route::get('mouthpiece/process/out', 'MouthpieceController@IndexMouthpieceProcessOUT');
    Route::get('index/monitoring/mouthpiece', 'MouthpieceController@IndexMonitoringMouthpiece');
    Route::get('fetch/monitoring/mouthpiece', 'MouthpieceController@FetchDataYmesStockMouthpiece');
    Route::get('fetch/mouthpiece/stock', 'MouthpieceController@FetchMouthpieceStock');
    Route::get('fetch/mouthpiece/stock/detail', 'MouthpieceController@FetchMouthpieceStockDetail');
    Route::get('mouthpiece/stock/kanban', 'MouthpieceController@IndexMouthpieceStockKanban');
    Route::get('cek/stock/kanban', 'MouthpieceController@CheckStockKanban');
    Route::get('list/cs/mouthpiece', 'MouthpieceController@ListBeforeCSMouthpiece');
    Route::get('request/mouthpiece/{loc}', 'MouthpieceController@IndexRequestMouthpiece');
    Route::get('fetch/request/mouthpiece/assy', 'MouthpieceController@FetchRequestMouthpieceAssy');
    Route::get('index/detail/request/{req_id}', 'MouthpieceController@IndexDetailRequest');
    Route::get('fetch/mouthpiece/log', 'MouthpieceController@FetchMouthpieceLog');

    Route::post('save/list/mouthpiece', 'MouthpieceController@SaveListMouthpiece');
    Route::post('save/stock/mouthpiece', 'MouthpieceController@SaveStockMouthpiece');
    Route::post('save/stock/kanban', 'MouthpieceController@SaveStockKanban');
    Route::post('update/stock/kanban', 'MouthpieceController@UpdateStockKanban');
    Route::post('update/qty/list/mouthpiece', 'MouthpieceController@UpdateListMouthpiece');
    Route::post('delete/stock/kanban', 'MouthpieceController@DeleteStockKanban');
    Route::post('delete/list/mouthpiece', 'MouthpieceController@DeleteListMouthpiece');
    Route::post('save/request/mouthpiece', 'MouthpieceController@SaveRequestMouthpiece');
    Route::post('update/request/mouthpiece', 'MouthpieceController@UpdateRequestMouthpiece');
    Route::post('scan/slip/request/mouthpiece', 'MouthpieceController@ScanSlipRequestMouthpiece');
    Route::post('scan/id_card/request/mouthpiece', 'MouthpieceController@ScanIdRequestMouthpiece');
    Route::post('update/persiapan/request', 'MouthpieceController@UpdatePersiapanRequest');
    Route::post('update/done/request', 'MouthpieceController@UpdateDoneRequest');
    Route::post('update/qty/received_by/mouthpiece', 'MouthpieceController@UpdateReceivedMouthpiece');

    Route::get('report/log/transaksi_in', 'MouthpieceController@IndexReportMouthpieceProcessIN');
    Route::get('fetch/report/log/transaksi_in', 'MouthpieceController@FetchReportMouthpieceProcessIN');
    Route::get('report/log/transaksi_out', 'MouthpieceController@IndexReportMouthpieceProcessOUT');
    Route::get('fetch/report/log/transaksi_out', 'MouthpieceController@FetchReportMouthpieceProcessOUT');

    Route::get('trial/upload/merger', 'TrialController@TrialMergerPdf');

    Route::get('index/operator/fukiage', 'MouthpieceController@IndexOperatorFukiage');
    Route::get('index/monitoring/ng_rate/mouthpiece', 'MouthpieceController@IndexNgRateMouthpiece');
    Route::get('fetch/monitoring/ng_rate/mouthpiece', 'MouthpieceController@FetchNgRateMouthpiece');

});
Route::group(['nav' => 'UPDATE_WIP-MP', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-MP', 'middleware' => 'permission'], function () {

});

// VENOVA
Route::group(['nav' => 'CREATE_WIP-VN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WIP-VN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_WIP-VN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-VN', 'middleware' => 'permission'], function () {

});

// PIANICA
Route::group(['nav' => 'CREATE_WIP-PN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WIP-PN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_WIP-PN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-PN', 'middleware' => 'permission'], function () {

});

// KEY PARTS PROCESS
Route::group(['nav' => 'CREATE_WIP-KPP', 'middleware' => 'permission'], function () {
    Route::post('scan/kanban/efisiensi/process', 'InitialProcessController@ScanInputOperator');
    Route::post('save/daily/efisiensi/process', 'InitialProcessController@SaveSendingDaily');
    Route::post('test/simpan/daily', 'InitialProcessController@TestSimpanDaily');
});
Route::group(['nav' => 'READ_WIP-KPP', 'middleware' => 'permission'], function () {
    Route::get('index/efisiensi/process', 'InitialProcessController@IndexInputOperator');
    Route::get('fetch/detail/efisiensi/process', 'InitialProcessController@FetchSendingDaily');
    Route::get('fetch/history/efisiensi/process', 'InitialProcessController@FetchSendingDailyHistory');
    Route::get('fetch/data/monitoring/efesiensi', 'InitialProcessController@MonitoringEfesiensi');
});
Route::group(['nav' => 'UPDATE_WIP-KPP', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-KPP', 'middleware' => 'permission'], function () {

});

// BODY PARTS PROCESS
Route::group(['nav' => 'CREATE_WIP-BPP', 'middleware' => 'permission'], function () {
    Route::post('upload/target/bpro', 'BproController@UploadTargetBpro');
});
Route::group(['nav' => 'READ_WIP-BPP', 'middleware' => 'permission'], function () {
    Route::get('index/monitoring/daily/bpro', 'BproController@IndexMonitoringBpro');
    Route::get('fetch/monitoring/daily/bpro', 'BproController@FetchMonitoringBpro');
    Route::get('fetch/daily/bpro', 'BproController@FetchProductionResultBpro');
});
Route::group(['nav' => 'UPDATE_WIP-BPP', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-BPP', 'middleware' => 'permission'], function () {

});

// CASE
Route::group(['nav' => 'CREATE_WIP-CASE', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WIP-CASE', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_WIP-CASE', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-CASE', 'middleware' => 'permission'], function () {

});

// CLARINET BODY
Route::group(['nav' => 'CREATE_WIP-CLBODY', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WIP-CLBODY', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_WIP-CLBODY', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-CLBODY', 'middleware' => 'permission'], function () {

});

// TANPO
Route::group(['nav' => 'CREATE_WIP-TNP', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WIP-TNP', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_WIP-TNP', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-TNP', 'middleware' => 'permission'], function () {

});

// WELDING PROCESS
Route::group(['nav' => 'CREATE_WIP-WP', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WIP-WP', 'middleware' => 'permission'], function () {
    Route::get('index/welding/hts_resume', 'WeldingProcessController@indexResumeHandatsuke');
    Route::get('fetch/welding/hts_resume', 'WeldingProcessController@fetchResumeHandatsuke');

    Route::get('index/welding/master_material/{loc}', 'WeldingProcessController@indexMasterMaterial');
    Route::get('fetch/welding/master_material', 'WeldingProcessController@fetchMasterMaterial');
});
Route::group(['nav' => 'UPDATE_WIP-WP', 'middleware' => 'permission'], function () {
    Route::post('update/welding/master_material', 'WeldingProcessController@updateMasterMaterial');
});
Route::group(['nav' => 'DELETE_WIP-WP', 'middleware' => 'permission'], function () {

});

// SURFACE TREATMENT
Route::group(['nav' => 'CREATE_WIP-ST', 'middleware' => 'permission'], function () {
    //LIFETIME
    Route::post('input/master/lifetime/{category}/{location}', 'LifetimeController@inputMasterLifetime');
    Route::post('input/record/lifetime/{category}/{location}', 'LifetimeController@inputRecordLifetime');
    Route::post('input/repair/lifetime/{category}/{location}', 'LifetimeController@inputRepairLifetime');

    Route::post('input/use/lifetime/{category}/{location}', 'LifetimeController@inputUseItem');
    Route::post('input/unuse/lifetime/{category}/{location}', 'LifetimeController@inputUnUseItem');
});
Route::group(['nav' => 'READ_WIP-ST', 'middleware' => 'permission'], function () {
    //LIFETIME
    Route::get('index/lifetime/{category}/{location}', 'LifetimeController@indexMonitoringLifetime');
    Route::get('fetch/lifetime/{category}/{location}', 'LifetimeController@fetchMonitoringLifetime');

    Route::get('index/master/lifetime/{category}/{location}', 'LifetimeController@indexMasterLifetime');
    Route::get('fetch/master/lifetime/{category}/{location}', 'LifetimeController@fetchMasterLifetime');

    Route::get('index/record/lifetime/{category}/{location}', 'LifetimeController@indexRecordLifetime');
    Route::get('fetch/record/lifetime/{category}/{location}', 'LifetimeController@fetchRecordLifetime');
    Route::get('scan/operator/record/lifetime/{category}/{location}', 'LifetimeController@scanOperatorRecordLifetime');
    Route::get('scan/record/lifetime/{category}/{location}', 'LifetimeController@scanRecordLifetime');
    Route::get('scan/kanban/lifetime/{category}/{location}', 'LifetimeController@scanKanbanLifetime');

    Route::get('index/repair/lifetime/{category}/{location}/{id}', 'LifetimeController@indexRepairLifetime');

    Route::get('fetch/report/lifetime/{category}/{location}', 'LifetimeController@fetchReportLifetime');
});
Route::group(['nav' => 'UPDATE_WIP-ST', 'middleware' => 'permission'], function () {
    //LIFETIME
    Route::post('update/master/lifetime/{category}/{location}', 'LifetimeController@updateMasterLifetime');
});
Route::group(['nav' => 'DELETE_WIP-ST', 'middleware' => 'permission'], function () {
    //LIFETIME
    Route::post('delete/master/lifetime/{category}/{location}', 'LifetimeController@deleteMasterLifetime');
});

// FINAL ASSY
Route::group(['nav' => 'CREATE_WIP-FA', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WIP-FA', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_WIP-FA', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WIP-FA', 'middleware' => 'permission'], function () {

});

// WAREHOUSE RAW MATERIAL
Route::group(['nav' => 'CREATE_WH-MSTK', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WH-MSTK', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_WH-MSTK', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WH-MSTK', 'middleware' => 'permission'], function () {

});

// WAREHOUSE FINISHED GOODS
Route::group(['nav' => 'CREATE_WH-FSTK', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_WH-FSTK', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_WH-FSTK', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_WH-FSTK', 'middleware' => 'permission'], function () {

});

// AUDIT & PATROL
Route::group(['nav' => 'CREATE_AUDIT', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_AUDIT', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_AUDIT', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_AUDIT', 'middleware' => 'permission'], function () {

});

// E-KAIZEN
Route::group(['nav' => 'CREATE_KAIZEN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_KAIZEN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_KAIZEN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_KAIZEN', 'middleware' => 'permission'], function () {

});

// PRUCHASE ORDER
Route::group(['nav' => 'CREATE_EO', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_EO', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_EO', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_EO', 'middleware' => 'permission'], function () {

});

// PURCHASE REQUISITION
Route::group(['nav' => 'CREATE_PR', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_PR', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_PR', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_PR', 'middleware' => 'permission'], function () {

});

// INVOICE
Route::group(['nav' => 'CREATE_INV', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_INV', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_INV', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_INV', 'middleware' => 'permission'], function () {

});

// EMPLOYEE SERVICES
Route::group(['nav' => 'CREATE_HRQ', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_HRQ', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_HRQ', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_HRQ', 'middleware' => 'permission'], function () {

});

// KANBAN
Route::group(['nav' => 'CREATE_KANBAN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'READ_KANBAN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'UPDATE_KANBAN', 'middleware' => 'permission'], function () {

});
Route::group(['nav' => 'DELETE_KANBAN', 'middleware' => 'permission'], function () {

});

// OTHER
Route::group(['nav' => 'CREATE_OTHER', 'middleware' => 'permission'], function () {
    // MEETING
    Route::post('create/meeting', 'MeetingController@createMeeting');

    // LICENSE
    Route::post('/input/license', 'LicenseController@inputLicense');
    Route::post('/mail/license', 'LicenseController@mailLicense');

    // SAFETY RIDING
    Route::post('input/general/safety_riding_record', 'GeneralController@inputSafetyRidingRecord');
    Route::post('approve/general/safety_riding_record', 'GeneralController@approveSafetyRidingRecord');
    Route::post('create/general/safety_riding', 'GeneralController@createSafetyRiding');

    // -------------------  Digitalisasi KY & HH -------------
    Route::post('upload/file', 'StandardizationController@uploadKyHh');
    Route::post('insert/jawaban', 'StandardizationController@InsertJawaban');
    Route::post('insert/tim', 'StandardizationController@InsertTim');
    Route::post('create/hiyarihatto', 'StandardizationController@CreateHiyariHatto');
    Route::post('create/tim/leader', 'StandardizationController@CreateTimLeader');
    Route::post('add/inject/tim', 'StandardizationController@InjectTimLeader');
    Route::post('send_email/reminder', 'StandardizationController@TestSendEmail');

    Route::post('index/purchase_item/edit_post', 'AccountingController@update_item_post');
    Route::post('index/purchase_item/upload/foto', 'AccountingController@UploadItem');
    // DOCUMENT ARCHIVE
    Route::post('input/document_archive', 'GeneralController@inputDocumentArchive');

    //GPC

});
Route::group(['nav' => 'READ_OTHER', 'middleware' => 'permission'], function () {
    // PENYIMPANAN
    Route::get('index/warehouse/temporary_storage', 'WarehouseController@indexTemporaryStorage');

    // MEETING
    Route::post('fetch/meeting/add_participant', 'MeetingController@fetchAddParticipant');
    Route::get('download/meeting', 'MeetingController@downloadMeeting');
    Route::get('index/meeting', 'MeetingController@indexMeeting');
    Route::get('index/meeting/attendance', 'MeetingController@indexMeetingAttendance');
    Route::get('fetch/meeting', 'MeetingController@fetchMeeting');
    Route::get('fetch/meeting/group', 'MeetingController@fetchMeetingGroup');
    Route::get('fetch/meeting/detail', 'MeetingController@fetchMeetingDetail');
    Route::get('fetch/meeting/chart', 'MeetingController@fetchMeetingChart');
    Route::get('fetch/meeting/chart_detail', 'MeetingController@fetchMeetingChartDetail');
    Route::get('fetch/meeting/attendance', 'MeetingController@fetchMeetingAttendance');
    Route::get('index/meeting/create', 'MeetingController@create');
    Route::get('index/meeting/list/{id}', 'MeetingController@list');

    // LICENSE
    Route::get('/index/license/{id}', 'LicenseController@indexLicense');
    Route::get('/fetch/license', 'LicenseController@fetchLicense');

    // SAFETY RIDING
    Route::get('index/safety_riding', 'GeneralController@indexSafetyRiding');
    Route::get('index/safety_riding_record/{id}', 'GeneralController@indexSafetyRidingRecord');
    Route::get('fetch/general/safety_riding_member', 'GeneralController@fetchSafetyRidingMember');
    Route::get('fetch/general/safety_riding', 'GeneralController@fetchSafetyRiding');
    Route::get('fetch/general/safety_riding_record', 'GeneralController@fetchSafetyRidingRecord');
    Route::get('fetch/general/safety_riding_pdf/{id}', 'GeneralController@fetchSafetyRidingPdf');

    // -------------------  Digitalisasi KY & HH -------------
    Route::get('index/ky_hh', 'StandardizationController@indexKyHh');
    Route::get('index/soal/ky/{nama_tim}/{periode}', 'StandardizationController@IndexSoal');
    Route::get('index/coba/report', 'StandardizationController@CobaViewReport');
    Route::get('index/log/soal/ky', 'StandardizationController@logKyHh');
    Route::get('fetch/log/soal/ky', 'StandardizationController@fatchLogKyHh');
    Route::get('fetch/detail/soal/ky', 'StandardizationController@detailLogKyHh');
    Route::get('fetch/soal/ky', 'StandardizationController@FetchSoalKy');
    Route::get('fetch/tim', 'StandardizationController@FetchTim');
    Route::get('fetch/resume/ky', 'StandardizationController@FetchResumeKY');
    Route::get('fetch/resume/hh', 'StandardizationController@FetchResumeHH');
    Route::get('fetch/resume/index', 'StandardizationController@FetchResumeIndex');
    Route::get('fetch/detail/jumlah/tim', 'StandardizationController@FetchDetailJumlahTim');
    Route::get('fetch/keywords', 'StandardizationController@FetchKeyword');
    Route::get('fetch/data/score', 'StandardizationController@FetchDataScore');
    Route::get('fetch/coba/email', 'StandardizationController@FetchCobaEmail');
    Route::get('index/penanganan/hiyarihatto/{request_id}/{id_ketua}', 'StandardizationController@IndexPenangananHh');
    Route::get('index/sosialisasi_ulang/kyt/{nama_tim}/{kode_soal}', 'StandardizationController@IndexSosialisasiUlangKyt');
    Route::get('fetch/sosialisasi_ulang/ky', 'StandardizationController@FetchSosialisasiUlangKyt');
    Route::get('fetch/data/presentase', 'StandardizationController@FetchDetailPresentasi');
    Route::get('fetch/home/leader', 'StandardizationController@FetchHomeLeader');
    Route::get('fetch/anggota/tim', 'StandardizationController@FetchAnggotaTim');
    Route::get('index/monitoring/ky_hh', 'StandardizationController@IndexMonitoringKyHH');
    Route::get('fetch/monitoring/ky_hh', 'StandardizationController@FetchMonitoringKyHH');
    Route::get('fetch/home/leader/all', 'StandardizationController@FetchResumeKYAll');
    Route::get('index/resume/ky/{id}', 'StandardizationController@IndexResumeKy');
    Route::get('fetch/monitoring/karyawan', 'StandardizationController@GrafikMonitoringKaryawan');
    Route::get('fetch/detail/monitoring/karyawan', 'StandardizationController@DetailKaryawanMonitoring');
    Route::get('report/pdf/kiken_yochi/{id}', 'StandardizationController@PrintReportKy');
    Route::get('fetch/hiyarihatto/all', 'StandardizationController@ReportHiyarihattoAll');
    Route::get('index/resume/hh/{request_id}', 'StandardizationController@IndexReportHiyarihatto');
    Route::post('update/tutup/soal', 'StandardizationController@UpdateTutupSoal');
    Route::post('update/buka/soal', 'StandardizationController@UpdateBukaSoal');
    Route::post('edit/tim', 'StandardizationController@EditTim');
    Route::post('update/penanganan/hiyarihatto', 'StandardizationController@UpdatePenangananHh');
    Route::post('delete/list/anggota', 'StandardizationController@DeleteListAnggota');
    Route::post('pindah/posisi/anggota', 'StandardizationController@MovePositionAnggota');
    Route::post('pindah/posisi/anggota', 'StandardizationController@MovePositionAnggota');
    Route::post('update/kode_soal/input', 'StandardizationController@UpdateKodeSoalInput');
    Route::get('fetch/detail/tim', 'StandardizationController@GetTimDetailMonitoring');
    Route::get('fetch/detail/hiyarihatto', 'StandardizationController@GetHiyarihattoDetailMonitoring');
    Route::get('get/detail/data', 'StandardizationController@GetDetailPenangananHH');

    //GPC
    Route::get('index/fude_meki', 'GeneralProcessController@IndexFudeMeki');
    Route::get('fetch/material/fude_meki', 'GeneralProcessController@FetchIndexFudeMeki');
    Route::post('input/fude_meki/list', 'GeneralProcessController@InputFudeMekiList');
    Route::get('detail/isi/kanban/fude_meki', 'GeneralProcessController@DetailIsiKanban');
    Route::get('fetch/detail/isi/kanban/fude_meki', 'GeneralProcessController@FetchDetailIsiKanban');
    Route::get('new/tab/detail/kanban/{id}', 'GeneralProcessController@IndexDetailKanbanFudeMeki');
});
Route::group(['nav' => 'UPDATE_OTHER', 'middleware' => 'permission'], function () {
    // MEETING
    Route::post('edit/meeting', 'MeetingController@editMeeting');
    Route::post('scan/meeting/attendance', 'MeetingController@scanMeetingAttendance');

    // DOCUMENT ARCHIVE
    Route::post('update/document_archive', 'GeneralController@updateDocumentArchive');
});
Route::get('confirm/kehadiran', 'StandardizationController@ConfirmKehadiran');
Route::get('confirm/sosialisasi_ulang', 'StandardizationController@ConfirmSosialisasiUlang');
Route::group(['nav' => 'DELETE_OTHER', 'middleware' => 'permission'], function () {
    // MEETING
    Route::post('delete/meeting', 'MeetingController@deleteMeeting');
    // KY & HH
    Route::post('delete/soal/kyt', 'StandardizationController@DeleteSoalKyt');
});

// FINISHED GOODS MONITORING
Route::group(['nav' => 'MONITOR_FG', 'middleware' => 'permission'], function () {
    Route::get('index/assembly/resume', 'AssemblyProcessController@indexAssemblyResume');
    Route::get('fetch/assembly/resume', 'AssemblyProcessController@fetchAssemblyResume');
    Route::get('index/production_resume', 'DisplayController@indexProductionResume');
    Route::get('fetch/production_warehouse', 'DisplayController@fetchProductionWarehouse');
    Route::get('index/production_warehouse', 'DisplayController@indexProductionWarehouse');
    Route::get('fetch/production_resume', 'DisplayController@fetchProductionResume');
    Route::get('index/fg_production', 'FinishedGoodsController@index_fg_production');
    Route::get('fetch/fg_production', 'FinishedGoodsController@fetch_fg_production');
    Route::get('index/fg_stock', 'FinishedGoodsController@index_fg_stock');
    Route::get('fetch/fg_stock', 'FinishedGoodsController@fetch_fg_stock');
    Route::get('index/fg_shipment_schedule', 'FinishedGoodsController@index_fg_shipment_schedule');
    Route::get('fetch/fg_shipment_schedule', 'FinishedGoodsController@fetch_fg_shipment_schedule');
    Route::get('index/fg_shipment_result', 'FinishedGoodsController@index_fg_shipment_result');
    Route::get('fetch/fg_shipment_result', 'FinishedGoodsController@fetch_fg_shipment_result');
    Route::get('index/fg_container_departure', 'FinishedGoodsController@index_fg_container_departure');
    Route::get('fetch/fg_container_departure', 'FinishedGoodsController@fetch_fg_container_departure');
    Route::get('index/fg_weekly_summary', 'FinishedGoodsController@index_fg_weekly_summary');
    Route::get('fetch/fg_weekly_summary', 'FinishedGoodsController@fetch_fg_weekly_summary');
    Route::get('index/fg_monthly_summary', 'FinishedGoodsController@index_fg_monthly_summary');
    Route::get('fetch/fg_monthly_summary', 'FinishedGoodsController@fetch_fg_monthly_summary');
    Route::get('fetch/tb_production', 'FinishedGoodsController@fetch_tb_production');
    Route::get('fetch/tb_stock', 'FinishedGoodsController@fetch_tb_stock');
    Route::get('fetch/tb_container_departure', 'FinishedGoodsController@fetch_tb_container_departure');
    Route::get('download/att_container_departure', 'FinishedGoodsController@download_att_container_departure');
    Route::get('fetch/tb_monthly_summary', 'FinishedGoodsController@fetch_tb_monthly_summary');
    Route::get('fetch/tb_shipment_result', 'FinishedGoodsController@fetch_tb_shipment_result');
    Route::get('index/fg_production_monitoring', 'ProductionScheduleController@indexProductionMonitoring');
    Route::get('index/fg_traceability', 'FinishedGoodsController@index_fg_traceability');
    Route::get('fetch/fg_traceability', 'FinishedGoodsController@fetch_fg_traceability');
    Route::get('index/fg_production_schedule', 'ProductionScheduleController@indexProductionData');
    Route::get('fetch/fg_production_schedule', 'ProductionScheduleController@fetchProductionData');
    Route::get('index/production_achievement', 'ChoreiController@indexProductionAchievement');
    Route::get('fetch/production_achievement', 'ChoreiController@fetchProductionAchievement');
    Route::get('index/ch_daily_production_result', 'ChoreiController@index_ch_daily_production_result');
    Route::get('index/ch_daily_production_result_kd', 'ChoreiController@index_ch_daily_production_result_kd');
    Route::get('fetch/daily_production_result_week', 'ChoreiController@fetch_daily_production_result_week');
    Route::get('fetch/daily_production_result_date', 'ChoreiController@fetch_daily_production_result_date');
    Route::get('fetch/daily_production_result', 'ChoreiController@fetch_daily_production_result');
    Route::post('update/reason_daily_production_result', 'ChoreiController@updateReason');
    Route::get('fetch/daily_production_result_kd', 'ChoreiController@fetch_daily_production_result_kd');
    Route::get('fetch/production_result_modal', 'ChoreiController@fetch_production_result_modal');
    Route::get('fetch/production_accuracy_modal', 'ChoreiController@fetch_production_accuracy_modal');
    Route::get('fetch/production_accuracy_modal_kd', 'ChoreiController@fetch_production_accuracy_modal_kd');
    Route::get('fetch/production_bl_modal', 'ChoreiController@fetch_production_bl_modal');
    Route::get('fetch/production_bl_modal_kd', 'ChoreiController@fetch_production_bl_modal_kd');
    Route::get('index/dp_production_result', 'DisplayController@index_dp_production_result');
    Route::get('fetch/dp_production_result', 'DisplayController@fetch_dp_production_result');
    Route::get('index/dp_fg_accuracy', 'DisplayController@index_dp_fg_accuracy');
    Route::get('fetch/dp_fg_accuracy', 'DisplayController@fetch_dp_fg_accuracy');
    Route::get('fetch/dp_fg_accuracy_detail', 'DisplayController@fetch_dp_fg_accuracy_detail');
    Route::get('index/assembly/resume_ng', 'AssemblyProcessController@indexAssemblyResumeNG');
    Route::get('fetch/assembly/resume_ng', 'AssemblyProcessController@fetchAssemblyResumeNG');
});
// SHIPMENT MONITORING
Route::group(['nav' => 'MONITOR_SHIP', 'middleware' => 'permission'], function () {
    Route::get('index/display/shipment_report', 'DisplayController@indexShipmentReport');
    Route::get('fetch/display/shipment_report', 'DisplayController@fetchShipmentReport');
    Route::get('fetch/display/shipment_report_detail', 'DisplayController@fetchShipmentReportDetail');
    Route::get('fetch/display/shipment_progress', 'DisplayController@fetchShipmentProgress');
    Route::get('fetch/display/modal_shipment_progress', 'DisplayController@fetchModalShipmentProgress');
    Route::get('index/display/shipment_progress', 'DisplayController@indexShipmentProgress');
    Route::get('index/display/stuffing_progress', 'DisplayController@indexStuffingProgress');
    Route::get('fetch/display/stuffing_progress', 'DisplayController@fetchStuffingProgress');
    Route::get('fetch/display/stuffing_detail', 'DisplayController@fetchStuffingDetail');
    Route::get('index/display/all_stock', 'DisplayController@indexAllStock');
    Route::get('fetch/display/all_stock', 'DisplayController@fetchAllStock');
    Route::get('index/display/shipment_progress_all', 'ShipmentController@indexShipmentProgress');
    Route::get('index/display/shipment_progress_all', 'ShipmentController@indexShipmentProgress');
    Route::get('index/display/stuffing_time', 'DisplayController@indexStuffingTime');
    Route::get('index/display/stuffing_monitoring', 'DisplayController@indexStuffingMonitoring');

});
// SALES MONITORING
Route::group(['nav' => 'MONITOR_SALES', 'middleware' => 'permission'], function () {
    Route::get('index/budget_vs_actual_sales', 'DisplayController@indexBudgetActualSales');
    Route::get('fetch/budget_vs_actual_sales', 'DisplayController@fetchBudgetActualSales');
    Route::get('index/shipping_amount', 'DisplayController@indexShippingAmount');
    Route::get('fetch/shipping_amount', 'DisplayController@fetchShippingAmount');
    Route::get('fetch/shipping_amount_resume', 'DisplayController@fetchShippingAmountResume');
    Route::post('update/shipping_amount_resume', 'DisplayController@updateShippingAmountResume');
    Route::get('index/shipping_production_amount', 'DisplayController@indexShippingProdAmount');
    Route::get('fetch/shipping_production_amount', 'DisplayController@fetchShippingProdAmount');

    Route::get('index/sales_by_destination', 'ShipmentController@indexSalesByDestination');
    Route::get('fetch/sales_by_destination', 'ShipmentController@fetchSalesByDestination');

});
// WIP MONITORING
Route::group(['nav' => 'MONITOR_WIP', 'middleware' => 'permission'], function () {
    Route::get('index/display/stockroom_monitoring', 'DisplayController@indexStockroomMonitoring');
    Route::get('fetch/display/stockroom_monitoring', 'DisplayController@fetchStockroomMonitoring');
    Route::get('index/wip_stock_assy', 'DisplayController@index_wip_stock_assy');
    Route::get('index/dp_stockroom_stock', 'DisplayController@index_dp_stockroom_stock');
    Route::get('fetch/dp_stockroom_stock', 'DisplayController@fetch_dp_stockroom_stock');

    Route::get('index/display/empty_stock', 'DisplayController@indexEmptyStock');
    Route::get('fetch/display/empty_stock', 'DisplayController@fetchEmptyStock');

});
// SCRAP MONITORING
Route::group(['nav' => 'MONITOR_SCRAP', 'middleware' => 'permission'], function () {
    Route::get('index/display/eff_scrap', 'DisplayController@indexEffScrap');
    Route::get('fetch/display/eff_scrap', 'DisplayController@fetchEffScrap');

});
// EFFICIENCY MONITORING
Route::group(['nav' => 'MONITOR_EFF', 'middleware' => 'permission'], function () {
    // Route::get('index/display/efficiency_monitoring', 'DisplayController@indexEfficiencyMonitoring');
    // Route::post('input/display/efficiency_monitoring_monthly', 'DisplayController@inputEfficiencyMonitoringMonthly');
    // Route::get('index/display/efficiency_monitoring_monthly', 'DisplayController@indexEfficiencyMonitoringMonthly');
    // Route::get('fetch/display/efficiency_monitoring', 'DisplayController@fetchEfficiencyMonitoring');
    // Route::get('fetch/display/efficiency_monitoring_monthly', 'DisplayController@fetchEfficiencyMonitoringMonthly');
    // Route::get('fetch/display/efficiency_monitoring_monthly_add', 'DisplayController@fetchEfficiencyMonitoringMonthlyAdd');

    // EFFICIENCY NEW
    Route::get('index/efficiency/monitoring', 'EfficiencyController@indexEfficiencyMonitoring');
    Route::get('index/efficiency/monitoring_detail/{id}', 'EfficiencyController@indexEfficiencyMonitoringDetail');

    Route::get('fetch/efficiency/monitoring', 'EfficiencyController@fetchEfficiencyMonitoring');
    Route::get('fetch/efficiency/monitoring_detail', 'EfficiencyController@fetchEfficiencyMonitoringDetail');
    Route::get('fetch/efficiency/monitoring_modal', 'EfficiencyController@fetchEfficiencyMonitoringModal');
});
// AUDIT & PATROL MONITORING
Route::group(['nav' => 'MONITOR_AUDIT', 'middleware' => 'permission'], function () {

});
// E-KAIZEN MONITORING
Route::group(['nav' => 'MONITOR_KAIZEN', 'middleware' => 'permission'], function () {

    Route::get('index/kaizen', 'EmployeeController@indexKaizen');
    Route::get('fetch/kaizen', 'EmployeeController@fetchDataKaizen');
    Route::get('fetch/kaizen/data', 'EmployeeController@fetchDataKaizenAll');
    Route::get('fetch/kaizen/applied', 'EmployeeController@fetchAppliedKaizen');
    Route::get('index/kaizen/detail/{id}/{ctg}', 'EmployeeController@indexKaizenAssessment');
    Route::post('input/kaizen/detail/note', 'EmployeeController@inputKaizenDetailNote');
    Route::get('index/kaizen/applied', 'EmployeeController@indexKaizenApplied');
    Route::post('assess/kaizen', 'EmployeeController@assessKaizen');
    Route::post('apply/kaizen', 'EmployeeController@applyKaizen');
    Route::get('index/kaizen/data', 'EmployeeController@indexKaizenData');
    Route::get('index/kaizen/resume', 'EmployeeController@indexKaizenDataResume');
    Route::get('index/upload_kaizen', 'EmployeeController@indexUploadKaizenImage');
    Route::post('post/upload_kaizen', 'EmployeeController@UploadKaizenImage');
    Route::get('fetch/kaizen/detail', 'EmployeeController@fetchDetailKaizen');
    Route::post('execute/kaizen/excellent', 'EmployeeController@executeKaizenExcellent');
    Route::get('index/kaizen/{section}', 'EmployeeController@indexKaizen2');
    Route::get('index/kaizen2/report', 'EmployeeController@indexKaizenReport');
    Route::get('fetch/kaizen/report', 'EmployeeController@fetchKaizenReport');
    Route::get('index/kaizen2/resume', 'EmployeeController@indexKaizenResume');
    Route::get('fetch/kaizen/resume', 'EmployeeController@fetchKaizenResume');
    Route::get('fetch/kaizen/resume_detail', 'EmployeeController@fetchKaizenResumeDetail');
    Route::get('index/kaizen/aproval/resume', 'EmployeeController@indexKaizenApprovalResume');
    Route::get('index/kaizen/aproval/grafik/resume', 'EmployeeController@indexKaizenApprovalResumeGraph');
    Route::get('index/kaizen/outstanding/grafik/resume', 'EmployeeController@indexKaizenApprovalResumeGraph2');
    Route::get('fetch/kaizen/aproval/grafik/resume', 'EmployeeController@fetchKaizenApprovalGraph');
    Route::get('fetch/kaizen/resume/grafik/resume', 'EmployeeController@fetchKaizenResumeGraph');
    Route::get('fetch/kaizen/aproval/grafik/resume/detail', 'EmployeeController@fetchKaizenApprovalDetail');
    Route::get('index/kaizen2/value', 'EmployeeController@indexKaizenReward');
    Route::get('fetch/kaizen/value', 'EmployeeController@getKaizenReward');
    Route::get('kaizen/session', 'EmployeeController@setSession');

});
// LEADER CONTROL MONITORING
Route::group(['nav' => 'MONITOR_LEADER', 'middleware' => 'permission'], function () {

});
// EXTRA ORDER MONITORING
Route::group(['nav' => 'MONITOR_EO', 'middleware' => 'permission'], function () {

});
// PURCHASE REQUISITION MONITORING
Route::group(['nav' => 'MONITOR_PR', 'middleware' => 'permission'], function () {

});
// PURCHASE ORDER MONITORING
Route::group(['nav' => 'MONITOR_PO', 'middleware' => 'permission'], function () {

});
// INVOICE MONITORING
Route::group(['nav' => 'MONITOR_INV', 'middleware' => 'permission'], function () {

});
// QUALITY MONITORING
Route::group(['nav' => 'MONITOR_QUALITY', 'middleware' => 'permission'], function () {

    //Audit Proses Khusus
    Route::get('index/qa/special_process', 'QualityAssuranceController@indexSpecialProcess');
    Route::get('fetch/qa/special_process', 'QualityAssuranceController@fetchSpecialProcess');
    Route::get('sendemail/qa/special_process/{id}', 'QualityAssuranceController@sendEmailAuditSpecialProcess');
    Route::get('sendemailcek/qa/special_process/{id}', 'QualityAssuranceController@sendEmailCekAuditSpecialProcess');
    Route::get('handling/qa/special_process/{id}', 'QualityAssuranceController@handlingAuditSpecialProcess');
    Route::post('input/handling/qa/special_process', 'QualityAssuranceController@inputHandlingSpecialProcess');
    Route::get('pdf/qa/special_process/{id}', 'QualityAssuranceController@pdfAuditSpecialProcess');

    Route::get('index/qa/special_process/report', 'QualityAssuranceController@indexAuditSpecialProcessReport');
    Route::get('fetch/qa/special_process/report', 'QualityAssuranceController@fetchAuditSpecialProcessReport');

    Route::get('index/qa/special_process/report_short/{schedule_id}', 'QualityAssuranceController@indexAuditSpecialProcessReportShort');

    //Audit QC Koteihyo
    Route::get('index/qa/qc_koteihyo/audit/{id_audit}', 'QualityAssuranceController@indexQcKoteihyoAudit');
    Route::get('fetch/qa/qc_koteihyo/audit', 'QualityAssuranceController@fetchQcKoteihyoAudit');
    Route::post('input/qa/qc_koteihyo/audit', 'QualityAssuranceController@inputQcKoteihyoAudit');
    Route::post('upload/qa/qc_koteihyo/audit', 'QualityAssuranceController@uploadQcKoteihyoAudit');
    Route::post('upload/handling/qa/qc_koteihyo/audit', 'QualityAssuranceController@uploadHandlingQcKoteihyoAudit');
    Route::get('pdf/qa/qc_koteihyo/{id}', 'QualityAssuranceController@pdfAuditQcKoteihyo');
    Route::get('sendemail/qa/qc_koteihyo/{id}', 'QualityAssuranceController@sendEmailAuditQcKoteihyo');
    Route::get('handling/qa/qc_koteihyo/{id}/{employee_id}', 'QualityAssuranceController@handlingAuditQcKoteihyo');
    Route::post('input/handling/qa/qc_koteihyo', 'QualityAssuranceController@inputHandlingQcKoteihyo');

    //Audit Packing
    Route::get('index/qa/packing', 'QualityAssuranceController@indexPacking');
    Route::get('fetch/qa/packing', 'QualityAssuranceController@fetchPacking');

    Route::get('index/qa/packing/handling/{audit_id}', 'QualityAssuranceController@indexPackingHandling');
    Route::post('input/qa/packing/handling', 'QualityAssuranceController@inputPackingHandling');
    Route::get('index/qa/packing/send_email/{audit_id}', 'QualityAssuranceController@indexPackingSendEmail');

    Route::get('index/qa/packing/pdf/{audit_id}', 'QualityAssuranceController@indexPackingPdf');

    Route::get('fetch/qa/packing/detail', 'QualityAssuranceController@fetchPackingDetail');

    Route::get('index/qa/packing/report', 'QualityAssuranceController@indexPackingReport');
    Route::get('fetch/qa/packing/report', 'QualityAssuranceController@fetchPackingReport');

    //Audit CPAR & CAR
    Route::get('index/qa/cpar_car', 'QualityAssuranceController@indexAuditCparCar');
    Route::get('fetch/qa/cpar_car', 'QualityAssuranceController@fetchAuditCparCar');

    Route::get('index/qa/cpar_car/audit/{schedule_id}', 'QualityAssuranceController@indexAuditCparCarAudit');
    Route::get('index/qa/cpar_car/handling/{schedule_id}', 'QualityAssuranceController@indexAuditCparCarHandling');
    Route::post('input/qa/cpar_car/handling', 'QualityAssuranceController@inputAuditCparCarHandling');
    Route::get('index/qa/cpar_car/send_email/{schedule_id}', 'QualityAssuranceController@indexAuditCparCarSendEmail');
    Route::get('index/qa/cpar_car/pdf/{schedule_id}', 'QualityAssuranceController@indexAuditCparCarPdf');

    Route::get('index/qa/cpar_car/report', 'QualityAssuranceController@indexAuditCparCarReport');
    Route::get('fetch/qa/cpar_car/report', 'QualityAssuranceController@fetchAuditCparCarReport');

    //Penyamaan Feeling
    Route::get('index/qa/feeling', 'QualityAssuranceController@indexAuditFeeling');
    Route::get('fetch/qa/feeling', 'QualityAssuranceController@fetchAuditFeeling');

    Route::get('index/qa/feeling/audit', 'QualityAssuranceController@indexAuditFeelingAudit');
    Route::get('fetch/qa/feeling/audit', 'QualityAssuranceController@fetchAuditFeelingAudit');
    Route::get('fetch/qa/feeling/employee', 'QualityAssuranceController@fetchAuditFeelingEmployee');
    Route::get('fetch/qa/feeling/claim', 'QualityAssuranceController@fetchFeelingClaim');

    //Audit IK
    Route::get('index/qa/ik', 'QualityAssuranceController@indexAuditIK');
    Route::get('fetch/qa/ik', 'QualityAssuranceController@fetchAuditIK');

    Route::get('index/qa/ik/audit', 'QualityAssuranceController@indexAuditIKAudit');
    Route::get('fetch/qa/ik/audit', 'QualityAssuranceController@fetchAuditIKAudit');

    Route::get('sendemail/qa/audit_ik', 'QualityAssuranceController@sendEmailAuditIK');

    //Audit FG
    Route::get('sendemail/qa/audit_fg/{id}', 'QualityAssuranceController@sendEmailAuditFG');
    Route::post('input/qa/audit_fg/handling', 'QualityAssuranceController@inputAuditFGHandling');
    Route::get('index/qa/audit_fg/pdf/{audit_id}', 'QualityAssuranceController@indexAuditFGPdf');
    Route::get('fetch/qa/audit_fg/detail', 'QualityAssuranceController@fetchAuditFGDetail');
    Route::get('index/qa/audit_fg/report/{product}', 'QualityAssuranceController@indexAuditFGReport');
    Route::get('fetch/qa/audit_fg/report', 'QualityAssuranceController@fetchAuditFGReport');

    Route::get('index/qa/feeling/report', 'QualityAssuranceController@indexAuditFeelingReport');
    Route::get('fetch/qa/feeling/report', 'QualityAssuranceController@fetchAuditFeelingReport');
});
// RAW MATERIAL MONITORING
Route::group(['nav' => 'MONITOR_MATERIAL', 'middleware' => 'permission'], function () {
    Route::get('index/material/usage', 'RawMaterialController@indexUsage');
    Route::get('index/material/raw_material_monitoring_index', 'RawMaterialController@rawMaterialMonitoringIndex');
    Route::get('index/material/material_monitoring_availability', 'RawMaterialController@indexAvailability');
    Route::get('fetch/material/material_monitoring_availability', 'RawMaterialController@fetchAvailability');
    Route::get('fetch/material/usage', 'RawMaterialController@fetchUsage');
    Route::get('fetch/material/smbmr', 'RawMaterialController@fetchSmbmr');
    Route::get('index/material/storage', 'RawMaterialController@indexStorage');
    Route::get('fetch/material/storage', 'RawMaterialController@fetchStorage');
    Route::get('index/material/monitoring', 'InitialProcessController@indexMonitoring');
    Route::get('index/scrap/upload', 'RawMaterialController@IndexUploadScrap');
    Route::get('index/material/shortage_material_availability', 'RawMaterialController@indexShortage');
    Route::get('fetch/material/shortage_material_availability', 'RawMaterialController@fetchShortage');

    // MATERIAL CHECK
    Route::get('index/material/check_monitoring', 'MaterialController@indexCheckMonitoring');
    Route::get('fetch/material/check', 'MaterialController@fetchCheckMaterial');
    Route::post('input/material/check_report', 'MaterialController@inputCheckReport');
    Route::get('index/material/check', 'MaterialController@indexCheckMaterial');
    Route::post('input/material/check', 'MaterialController@inputCheckMaterial');
    Route::post('edit/material/material_control', 'MaterialController@editMaterialCheck');

});
// EMPLOYEE SERVICES MONITORING
Route::group(['nav' => 'MONITOR_HRQ', 'middleware' => 'permission'], function () {
    Route::get('index/general/online_transportation', 'GeneralController@indexOnlineTransportation');
    Route::get('index/general/surat_dokter', 'GeneralController@indexSuratDokter');
    Route::get('index/human_resource/leave_request', 'HumanResourceController@indexLeaveRequest');
    Route::get('human_resource', 'HumanResourceController@IndexHr');
    Route::get('dashboard/mutasi', 'MutasiController@dashboard');
    Route::get('dashboard_ant/mutasi', 'MutasiController@dashboardAnt');
});
// MAINTENANCE MONITORING
Route::group(['nav' => 'MONITOR_MTC', 'middleware' => 'permission'], function () {

});
// SYSTEM MONITORING
Route::group(['nav' => 'MONITOR_SYS', 'middleware' => 'permission'], function () {
    Route::get('index/mis/stocktaking_account', 'TicketController@indexStocktakingAccount');
    Route::get('fetch/mis/stocktaking_account', 'TicketController@fetchStocktakingAccount');
    Route::post('update/mis/permission', 'TicketController@updatePermission');
});
// ATTENDANCE MONITORING
Route::group(['nav' => 'MONITOR_ATTEND', 'middleware' => 'permission'], function () {

});
// MANPOWER MONITORING
Route::group(['nav' => 'MONITOR_MP', 'middleware' => 'permission'], function () {

});
// OVERTIME MONITORING
Route::group(['nav' => 'MONITOR_OVT', 'middleware' => 'permission'], function () {

});
// HUMAN RESOURCES MONITORING
Route::group(['nav' => 'MONITOR_HR', 'middleware' => 'permission'], function () {
    Route::get('index/human_resource/let', 'HumanResourceController@indexLet');
    Route::get('fetch/human_resource/let', 'HumanResourceController@fetchLet');

    Route::post('input/human_resource/let/evaluation', 'HumanResourceController@inputLetEvaluation');

    // MANPOWER MONITORING
    Route::get('index/manpower/information', 'EmployeeController@indexManpowerInformation');
    Route::get('fetch/manpower/information', 'EmployeeController@fetchManpowerInformation');

    Route::get('index/report/total_meeting', 'EmployeeController@indexTotalMeeting');
    Route::get('fetch/report/total_meeting', 'EmployeeController@fetchTotalMeeting');
});
// GENERAL AFFAIRS MONITORING
Route::group(['nav' => 'MONITOR_GA', 'middleware' => 'permission'], function () {
    // LOCKER
    Route::get('index/ga_control/locker', 'GeneralAffairController@indexLocker');
    Route::get('fetch/ga_control/locker', 'GeneralAffairController@fetchLocker');
    Route::get('index/ga_control/locker_qr/{id}', 'GeneralAttendanceController@fetchLockerQr');

    // BENTO
    Route::get('index/ga_control/bento', 'GeneralAffairController@indexBento');
    Route::get('fetch/ga_control/bento_quota', 'GeneralAffairController@fetchBentoQuota');
    Route::get('fetch/ga_control/bento_order_list', 'GeneralAffairController@fetchBentoOrderList');
    Route::get('fetch/ga_control/bento_order_edit', 'GeneralAffairController@fetchBentoOrderEdit');
    Route::get('fetch/ga_control/bento_order_log', 'GeneralAffairController@fetchBentoOrderLog');
    Route::get('fetch/ga_control/bento_order_count', 'GeneralAffairController@fetchBentoOrderCount');
    Route::post('input/ga_control/bento_order', 'GeneralAffairController@inputBentoOrder');
    Route::post('edit/ga_control/bento_order', 'GeneralAffairController@editBentoOrder');
    Route::get('index/ga_control/bento_japanese/{id}', 'GeneralAffairController@indexBentoJapanese');
    Route::get('fetch/ga_control/bento_japanese', 'GeneralAffairController@fetchBentoJapanese');
    Route::post('input/ga_control/bento_japanese', 'GeneralAffairController@inputBentoJapanese');

    // LIVE COOKING
    Route::get('index/ga_control/live_cooking', 'GeneralAffairController@indexLiveCooking');
    Route::get('download/ga_control/live_cooking', 'GeneralAffairController@downloadFileExcelLiveCooking');
    Route::post('upload/ga_control/live_cooking_menu', 'GeneralAffairController@uploadLiveCookingMenu');
    Route::get('fetch/ga_control/live_cooking_order_list', 'GeneralAffairController@fetchLiveCookingOrderList');
    Route::get('live_cooking/menu/{periode}', 'GeneralAffairController@fetchLiveCookingMenu');
    Route::post('input/ga_control/live_cooking_order', 'GeneralAffairController@inputLiveCookingOrder');
    Route::post('input/ga_control/live_cooking_order/extra', 'GeneralAffairController@inputLiveCookingOrderExtra');
    Route::get('fetch/ga_control/live_cooking_employees', 'GeneralAffairController@fetchLiveCookingEmployees');
    Route::get('fetch/ga_control/cjeck/live_cooking_employees', 'GeneralAffairController@fetchLiveCookingCheckEmployees');
    Route::get('detail/ga_control/live_cooking', 'GeneralAffairController@detailLiveCooking');
    Route::post('edit/ga_control/live_cooking_order', 'GeneralAffairController@editLiveCookingOrder');
    Route::get('fetch/ga_control/live_cooking_randomize', 'GeneralAffairController@randomLiveCooking');
    Route::get('report/ga_control/live_cooking', 'GeneralAffairController@reportLiveCooking');
    Route::get('report/ga_control/live_cooking_pay', 'GeneralAffairController@reportLiveCookingPay');
    Route::get('index/ga_control/live_cooking/confirm', 'GeneralAffairController@indexLiveCookingConfirm');
    Route::post('approve/ga_control/live_cooking', 'GeneralAffairController@approveLiveCooking');

    //GYM
    Route::get('index/ga_control/gym', 'GeneralAffairController@indexGym');
    Route::get('fetch/ga_control/gym', 'GeneralAffairController@fetchGym');
    Route::post('input/ga_control/gym', 'GeneralAffairController@inputGym');
    Route::get('edit/ga_control/gym', 'GeneralAffairController@editGym');
    Route::get('delete/ga_control/gym', 'GeneralAffairController@deleteGym');

    Route::get('fetch/ga_control/gym/attendance', 'GeneralAffairController@fetchGymAttendance');
    Route::get('scan/ga_control/gym/attendance', 'GeneralAffairController@scanGymAttendance');
    Route::get('fetch/ga_control/gym/send_whatsapp', 'GeneralAffairController@sendGymWhatsapp');

    Route::post('input/ga_control/gym/progress', 'GeneralAffairController@inputGymProgress');

    Route::get('index/ga_control/gym/attendance', 'GeneralAffairController@indexGymAttendance');

    Route::get('index/ga_control/gym/schedule', 'GeneralAffairController@indexGymSchedule');
    Route::get('fetch/ga_control/gym/schedule', 'GeneralAffairController@fetchGymSchedule');

    Route::post('update/ga_control/gym/schedule', 'GeneralAffairController@updateGymSchedule');

    Route::get('index/ga_control/gym/regulation', 'GeneralAffairController@indexGymRegulation');
});
// LICENSE MONITORING
Route::group(['nav' => 'MONITOR_LICENSE', 'middleware' => 'permission'], function () {
    // MASTER TRADE AGREEMENT
    Route::get('/index/trade_agreement', 'LicenseController@indexTradeAgreement');
    Route::get('/index/trade_agreement/monitoring_cms', 'LicenseController@indexMonitoringCMS');
    Route::get('/fetch/trade_agreement/monitoring_cms', 'LicenseController@fetchMonitoringCMS');
    Route::get('/index/trade_agreement/monitoring', 'LicenseController@indexTradeAgreementMonitoring');
    Route::get('/index/trade_agreement/list', 'LicenseController@indexTradeAgreementList');
    Route::get('/fetch/trade_agreement', 'LicenseController@fetchTradeAgreement');
    Route::get('/fetch/trade_agreement_list', 'LicenseController@fetchTradeAgreementList');

});
// STANDARDIZATION MONITORING
Route::group(['nav' => 'MONITOR_STD', 'middleware' => 'permission'], function () {

    //Risk Assessment

    Route::get('index/standardization/risk_assessment', 'StandardizationController@indexRiskAssessment');

    // VEHICLE
    Route::get('index/standardization/vehicle_monitoring', 'StandardizationController@indexVehicleMonitoring');
    Route::get('fetch/standardization/vehicle_monitoring', 'StandardizationController@fetchVehicleMonitoring');
    Route::get('index/standardization/vehicle_menu', 'StandardizationController@indexVehicleMenu');
    Route::get('index/standardization/form/{id}', 'StandardizationController@indexVehicleForm');
    Route::post('input/standardization/form', 'StandardizationController@inputVehicleForm');
    Route::get('fetch/standardization/form', 'StandardizationController@fetchVehicleForm');
    Route::get('index/standardization/vehicle_report', 'StandardizationController@indexVehicleReport');
    Route::get('fetch/standardization/vehicle_report', 'StandardizationController@fetchVehicleReport');

    Route::get('index/standardization/vehicle_attedance', 'StandardizationController@indexVehicleAttendance');
    Route::get('fetch/standardization/vehicle_attedance', 'StandardizationController@fetchVehicleAttendance');
    Route::get('fetch/standardization/attendance_queue', 'StandardizationController@fetchVehicleAttendanceQueue');

    Route::get('index/standardization/attendance_monitoring', 'StandardizationController@indexVehicleMonitoringAttendance');
    Route::get('fetch/standardization/attendance_monitoring', 'StandardizationController@fetchVehicleMonitoringAttendance');

    // CALIBRATION
    Route::get('index/standardization/calibration', 'StandardizationController@indexCalibration');
    Route::get('fetch/standardization/calibration', 'StandardizationController@fetchCalibration');
    Route::post('input/standardization/calibration', 'StandardizationController@inputCalibration');
    Route::post('edit/standardization/calibration', 'StandardizationController@editCalibration');
    Route::post('delete/standardization/calibration', 'StandardizationController@deleteCalibration');

    Route::get('index/standardization/ypm', 'StandardizationController@indexYPM');
    Route::get('fetch/standardization/ypm', 'StandardizationController@fetchYPM');

    Route::get('index/standardization/ypm/point_check', 'StandardizationController@indexYPMPointCheck');
    Route::get('fetch/standardization/ypm/point_check', 'StandardizationController@fetchYPMPointCheck');

    Route::get('index/standardization/ypm/master', 'StandardizationController@indexYPMMaster');
    Route::get('fetch/standardization/ypm/master', 'StandardizationController@fetchYPMMaster');
    Route::get('download/standardization/ypm/master', 'StandardizationController@downloadYPMMaster');

    Route::get('index/standardization/ypm/report', 'StandardizationController@indexYPMReport');
    Route::get('fetch/standardization/ypm/report', 'StandardizationController@fetchYPMReport');

    Route::get('approval/standardization/ypm/{periode}/{remark}/{employee_id}', 'StandardizationController@approvalYPM');
    Route::get('reject/standardization/ypm/{periode}/{remark}/{employee_id}', 'StandardizationController@rejectYPM');
});
// FIXED ASSET MONITORING
Route::group(['nav' => 'MONITOR_ASSET', 'middleware' => 'permission'], function () {

});
// BUDGET MONITORING
Route::group(['nav' => 'MONITOR_BUDGET', 'middleware' => 'permission'], function () {

});
// PRODUCTION ENGINEERING MONITORING
Route::group(['nav' => 'MONITOR_PE', 'middleware' => 'permission'], function () {

});
// STOCKTAKING MONITORING
Route::group(['nav' => 'MONITOR_ST', 'middleware' => 'permission'], function () {

});
// CHEMICAL MONITORING
Route::group(['nav' => 'MONITOR_CHM', 'middleware' => 'permission'], function () {

});
// TRANSACTION MONITORING
Route::group(['nav' => 'MONITOR_TRANSACTION', 'middleware' => 'permission'], function () {

});
// ACTIVITY MONITORING
Route::group(['nav' => 'MONITOR_ACT', 'middleware' => 'permission'], function () {

});
// KANBAN MONITORING
Route::group(['nav' => 'MONITOR_KANBAN', 'middleware' => 'permission'], function () {

});
// OTHER MONITORING
Route::group(['nav' => 'MONITOR_OTHER', 'middleware' => 'permission'], function () {

    // BOM MULTILEVEL
    Route::get('/index/bom_multilevel', 'YmesController@indexBomMultilevel');

    // UNUSUAL SLIP
    Route::get('/index/transaction/slip_unusual', 'TransactionController@indexSlipUnusual');
    Route::post('/print/transaction/slip_unusual', 'TransactionController@printSlipUnusual');

    // MIS TICKET

    Route::get('/index/ticket/monitoring_new', 'TicketController@indexTicketMonitoringNew');
    Route::get('/input/ticket_new', 'TicketController@inputTicketNew');
    Route::get('/fetch/ticket/monitoring_new', 'TicketController@fetchTicketMonitoringNew');

    // APPROVAL
    Route::get('index/general/resume_approval', 'GeneralController@indexResumeApproval');
    Route::get('fetch/general/resume_approval', 'GeneralController@fetchResumeApproval');

    Route::get('index/general/resume_approval/new', 'GeneralController@indexResumeApprovalNew');
    // Route::get('fetch/general/resume_approval/new', 'GeneralController@fetchResumeApprovalNew');

    // VISITOR TEMPERATURE
    Route::get('index/temperature', 'TemperatureController@index');
    Route::get('index/temperature/body_temperature_report', 'TemperatureController@indexBodyTemperatureReport');
    Route::get('fetch/temperature/body_temp_report', 'TemperatureController@fetchBodyTemperatureReport');
    Route::get('index/temperature/body_temp_monitoring', 'TemperatureController@indexBodyTempMonitoring');
    Route::get('fetch/temperature/fetch_body_temp_monitoring', 'TemperatureController@fetchBodyTempMonitoring');

    // END VISITOR TEMPERATURE
    Route::get('index/temperature/omron/{id}', 'TemperatureController@indexOmron');
    Route::get('fetch/temperature/omron', 'TemperatureController@fetchOmron');
    Route::post('input/temperature/omron_operator', 'TemperatureController@inputOmronOperator');

    // ROOM Temperature
    // Route::get('index/temperature/room_temperature', 'TemperatureController@RoomTemperature');
    // Route::get('fetch/temperature/room_temperature', 'TemperatureController@fetchRoomTemperature');

    // Barang Modal
    Route::get('index/kedatangan/dokumen_bc', 'LogisticController@index_kedatangan_dokumen_bc');
    Route::get('fetch/kedatangan/dokumen_bc', 'LogisticController@fetch_kedatangan_dokumen_bc');
    Route::post('post/kedatangan/dokumen_bc', 'LogisticController@post_dokumen_bc');

    //Receive Barang
    Route::get('index/barang_modal', 'LogisticController@barang_modal');
    Route::get('produksi/cek_kedatangan/{id}', 'LogisticController@cek_kedatangan_produksi_all');
    Route::get('fetch/produksi/cek_kedatangan', 'LogisticController@fetch_kedatangan_produksi_all');
    Route::get('index/barang_modal/stock', 'LogisticController@barang_modal_stock');
    Route::get('fetch/barang_modal/stock', 'LogisticController@fetch_barang_modal_stock');

    Route::get('index/non_fixed_asset/transfer', 'LogisticController@indexNonAssetTransfer');
    Route::get('fetch/non_fixed_asset/transfer', 'LogisticController@fetchNonAssetTransfer');
    // Route::post('post/non_fixed_asset/transfer', 'LogisticController@postNonAssetTransfer');
    // Route::post('edit/non_fixed_asset/transfer', 'LogisticController@editNonAssetTransfer');

    Route::get('index/barang_modal/monitoring', 'LogisticController@indexBarangModalMonitoringApproval');
    Route::get('fetch/barang_modal/monitoring', 'LogisticController@fetchBarangModalMonitoringApproval');

    // NEW ROOM Temperature
    Route::get('index/temperature/room_temperature', 'TemperatureController@RoomTemperatureNew');
    Route::get('fetch/temperature/room_temperature', 'TemperatureController@fetchRoomTemperatureNew');
    Route::get('fetch/temperature/room_temperature/detail', 'TemperatureController@fetchRoomTemperatureDetail');
    Route::post('create/cuaca', 'TemperatureController@createWeather');
    Route::get('export/temperature', 'TemperatureController@exportLogData');

    // Monitoring Temperature and humidity
    Route::get('index/temperature/log', 'TemperatureController@RoomTemperatureLog');
    Route::get('fetch/temperature/log', 'TemperatureController@fetchRoomTemperatureLog');

    // MOSAIC
    Route::get('index/general/mosaic', 'GeneralController@indexMosaic');
    Route::get('fetch/general/mosaic', 'GeneralController@fetchMosaic');
    Route::get('fetch/general/mosaic_detail', 'GeneralController@fetchMosaicDetail');

    // POINTING CALL
    Route::get('index/general/pointing_call/{id}', 'GeneralController@indexGeneralPointingCall');
    Route::get('fetch/general/pointing_call', 'GeneralController@fetchGeneralPointingCall');
    Route::post('edit/general/pointing_call_pic', 'GeneralController@editGeneralPointingCallPic');

    // OXYMETER
    Route::get('index/general/oxymeter', 'GeneralController@indexOxymeterCheck');
    Route::get('fetch/general/oxymeter/employee', 'EmployeeController@fetchEmployeeByTag');
    Route::post('post/general/oxymeter', 'GeneralController@postOxymeterCheck');
    Route::get('fetch/general/oxymeter/history', 'GeneralController@fetchOxymeterHistory');
    Route::get('index/general/oxymeter/monitoring', 'GeneralController@indexOxymeterMonitoring');
    Route::get('fetch/general/oxymeter/data', 'GeneralController@fetchOxymeterMonitoring');
    Route::post('upload/general/oxymeter', 'GeneralController@UploadOxymeter');

    // AIR VISUAL
    Route::get('index/general/airvisual', 'GeneralController@indexAirVisual');
    Route::get('post/general/airvisual/data', 'GeneralController@postAirVisual');
    Route::get('fetch/general/airvisual/data', 'GeneralController@getAirVisual');

    // CO2 METER
    Route::get('index/general/co2', 'GeneralController@indexCo');
    Route::get('fetch/general/co2/data', 'GeneralController@fetchCo');

    // OMI VISITOR
    Route::get('index/general/omi_visitor', 'GeneralController@indexOmiVisitor');
    Route::get('fetch/general/omi_visitor', 'GeneralController@fetchOmiVisitor');
    Route::post('input/general/omi_visitor', 'GeneralController@inputOmiVisitor');

    // MIS FORM
    Route::get('index/mis/form', 'TicketController@indexForm');
    Route::get('fetch/mis/form', 'TicketController@fetchForm');
    Route::get('fetch/mis/form_security', 'TicketController@fetchFormSecurity');
    Route::post('input/mis/form', 'TicketController@inputForm');
    Route::get('approval/mis/form', 'TicketController@approvalForm');
    Route::post('confirm/mis/form', 'TicketController@confirmForm');
    Route::get('create/pdf/form_mis/{form_id}', 'TicketController@PdfMisForm');

    //MIS COMPLAINT

    Route::get('index/mis/complaint/create/{complaint}', 'TicketController@indexComplaintCreate');
    Route::post('input/mis/complaint', 'TicketController@inputComplaint');
    Route::get('index/mis/complaint/monitoring', 'TicketController@indexMonitoringComplaint');
    Route::get('fetch/mis/complaint/monitoring', 'TicketController@fetchMonitoringMISComplaint');

    // MIS TICKET
    Route::post('input/ticket', 'TicketController@inputTicket');
    Route::get('approval/ticket', 'TicketController@approvalTicket');
    Route::get('approval/ticket_reject', 'TicketController@approvalTicketReject');
    Route::get('approval/ticket/monitoring', 'TicketController@approvalTicketMonitoring');
    Route::get('/index/ticket/resume', 'TicketController@indexTicketResume');
    Route::get('/fetch/ticket/resume', 'TicketController@fetchTicketResume');
    Route::get('/index/ticket/detail/{id}', 'TicketController@indexTicketDetail');
    Route::get('/index/ticket/{id}', 'TicketController@indexTicket');
    Route::get('/index/ticket/monitoring/{id}', 'TicketController@indexTicketMonitoring');
    Route::get('/index/ticket_monitoring/category', 'TicketController@indexTicketMonitoringCategory');
    Route::get('/fetch/ticket_monitoring/category', 'TicketController@fetchTicketMonitoringCategory');
    Route::get('/fetch/ticket/monitoring', 'TicketController@fetchTicketMonitoring');
    Route::get('/fetch/ticket/monitoring/new', 'TicketController@fetchTicketMonitoringNew');
    Route::get('/fetch/ticket/monitoring/detail', 'TicketController@detailTicketBulan');
    Route::get('/fetch/ticket', 'TicketController@fetchTicket');
    Route::get('/fetch/ticket/timeline/{id}', 'TicketController@fetchTicketTimeline');
    Route::get('/fetch/ticket/timeline', 'TicketController@fetchTicketTimelineCategory');
    Route::get('/fetch/ticket/pdf/{id}', 'TicketController@fetchTicketPDF');
    Route::get('index/ticket_log', 'TicketController@indexTicketLog');
    Route::get('fetch/ticket_log', 'TicketController@fetchTicketLog');
    Route::get('fetch/detail/tiket', 'TicketController@fetchDetailTicket');
    Route::get('fetch/detail/tiket_category', 'TicketController@fetchDetailTicketCategory');
    Route::get('fetch/detail/tiket_pic', 'TicketController@fetchDetailTicketPic');
    Route::get('fetch/detail/tiket_perolehan', 'TicketController@fetchDetailTicketPerolehan');
    // TRANSLATION REQUEST

    Route::get('approval/translation', 'TranslationController@approvalTranslation');
    Route::post('input/translation', 'TranslationController@inputTranslation');
    Route::get('index/translation', 'TranslationController@indexTranslation');
    Route::get('index/translation_resume', 'TranslationController@indexResume');
    Route::get('fetch/translation', 'TranslationController@fetchTranslation');
    Route::get('fetch/translation_load', 'TranslationController@fetchLoad');

    // REPAIR ROOM
    Route::get('index/transaction/repair_room_monitoring', 'TransactionController@indexRepairRoomMonitoring');
    Route::get('fetch/transaction/repair_room_monitoring', 'TransactionController@fetchRepairRoomMonitoring');
    Route::get('fetch/transaction/repair_room_log', 'TransactionController@fetchRepairRoomLog');

    // E-Billing Modal
    Route::get('index/billing_menu', 'AccountingController@index_billing');
    Route::get('billing/receive_material', 'AccountingController@billing_receive_material');
    Route::get('fetch/billing/receive_material', 'AccountingController@fetch_billing_receive_material');
    Route::get('billing/receive_non_material', 'AccountingController@billing_receive_non_material');
    Route::get('fetch/billing/receive_non_material', 'AccountingController@fetch_billing_receive_non_material');

    Route::get('index/bank', 'AccountingController@index_bank');
    Route::get('fetch/bank', 'AccountingController@fetch_bank');

    Route::get('index/gl_account', 'AccountingController@index_gl_account');
    Route::get('fetch/gl_account', 'AccountingController@fetch_gl_account');

    Route::get('index/cost_center', 'AccountingController@index_cost_center');
    Route::get('fetch/cost_center', 'AccountingController@fetch_cost_center');

    //Tanda Terima E-Billing
    Route::get('billing/tanda_terima/{id}', 'AccountingController@index_billing_tanda_terima');
    Route::get('fetch/billing/tanda_terima', 'AccountingController@fetch_billing_tanda_terima');
    Route::get('fetch/billing/tanda_terima_ymes', 'AccountingController@fetch_billing_tanda_terima_ymes');
    Route::post('delete/tanda_terima', 'AccountingController@delete_tanda_terima');
    // Route::get('billing/tanda_terima_detail', 'AccountingController@fetch_billing_tanda_terima_detail');
    // Route::post('create/billing/tanda_terima', 'AccountingController@create_billing_tanda_terima');
    // Route::post('edit/billing/tanda_terima', 'AccountingController@edit_billing_tanda_terima');
    // Route::get('tanda_terima/report/{id}', 'AccountingController@report_billing_tanda_terima');
    // Route::get('export/billing/tanda_terima', 'AccountingController@export_billing_tanda_terima');

    Route::get('billing/jurnal', 'AccountingController@indexJurnalPayment');
    Route::get('fetch/jurnal', 'AccountingController@fetchJurnalPayment');
    Route::get('fetch/bank/data', 'AccountingController@getBank');
    Route::get('fetch/bank/id_payment', 'AccountingController@getIDPayment');
    Route::get('fetch/invoice/verification', 'AccountingController@fetchInvoiceVerification');
    Route::get('fetch/jurnal_type', 'AccountingController@get_jurnal_type');
    Route::get('fetch/gl_account/data', 'AccountingController@get_gl_account');
    Route::get('fetch/cost_center/data', 'AccountingController@get_cost_center');
    Route::post('create/jurnal', 'AccountingController@createJurnal');
    Route::get('report/jurnal/{id}', 'AccountingController@reportJurnal');

    Route::get('billing/index/list_bank', 'AccountingController@indexReportJurnal');
    Route::get('fetch/list_bank', 'AccountingController@fetchReportJurnal');
    Route::get('export/bank/list', 'AccountingController@exportJurnal');

    Route::get('final_payment/verifikasi/{id}', 'AccountingController@verifikasi_final_payment');

    Route::get('billing/upload_jurnal', 'AccountingController@indexUploadJurnal');
    Route::get('fetch/upload_jurnal', 'AccountingController@fetchUploadJurnal');
    Route::get('export/upload_jurnal', 'AccountingController@exportUploadJurnal');

    //Payment Request E-Billing
    Route::get('billing/payment_request/{id}', 'AccountingController@IndexBillingPaymentRequest');
    Route::get('fetch/billing/payment_request', 'AccountingController@fetchBillingPaymentRequest');
    Route::get('fetch/billing/payment_request/detail', 'AccountingController@fetchBillingPaymentRequestDetail');
    Route::post('billing/create/payment_request', 'AccountingController@createBillingPaymentRequest');
    Route::get('detail/billing/payment_request', 'AccountingController@fetchPaymentRequestDetail');
    Route::post('delete/billing/payment_request', 'AccountingController@deletePaymentRequest');

    //PO Monitoring & Control
    Route::get('index/payment_request/monitoring', 'AccountingController@indexPaymentRequestMonitoring');
    Route::get('fetch/payment_request/monitoring', 'AccountingController@fetchPaymentRequestMonitoring');
    Route::get('fetch/payment_request/detail', 'AccountingController@detailPaymentRequestMonitoring');
    Route::get('fetch/payment_request/table', 'AccountingController@fetchtablePaymentRequest');

    Route::get('payment_request/verifikasi/{id}', 'AccountingController@verifikasi_payment_request');
    Route::post('payment_request/approval/{id}', 'AccountingController@approval_payment_request');
    Route::post('payment_request/notapprove/{id}', 'AccountingController@reject_payment_request');

    Route::get('index/tanda_terima/monitoring/{id}', 'AccountingController@indexInvoiceMonitoring');
    Route::get('fetch/tanda_terima/monitoring', 'AccountingController@fetchInvoiceMonitoring');

    // Route::post('create/billing/payment_request', 'AccountingController@createBillingPaymentRequest');
    // Route::get('detail/billing/payment_request', 'AccountingController@fetchBillingPaymentRequestDetail');
    // Route::post('edit/billing/payment_request', 'AccountingController@editBillingPaymentRequest');
    // Route::get('report/billing/payment_request/{id}', 'AccountingController@reportBillingPaymentRequest');
    // Route::get('email/billing/payment_request', 'AccountingController@emailBillingPaymentRequest');

    //Approval Payment Request E-Billing
    // Route::get('billing/payment_request/approvemanager/{id}', 'AccountingController@billingpaymentapprovalmanager');
    // Route::get('billing/payment_request/approvedgm/{id}', 'AccountingController@billingpaymentapprovaldgm');
    // Route::get('billing/payment_request/approvegm/{id}', 'AccountingController@billingpaymentapprovalgm');
    // Route::get('billing/payment_request/receiveacc/{id}', 'AccountingController@billingpaymentreceiveacc');
    // Route::get('billing/payment_request/reject/{id}', 'AccountingController@billingpaymentreject');

    Route::get('check/payment_request', 'AccountingController@checkBillingPaymentRequest');
    Route::get('fetch/check_payment_request', 'AccountingController@fetchCheckBillingPaymentRequest');
    Route::get('fetch/check_payment_request_id', 'AccountingController@fetchCheckBillingPaymentRequestId');
    Route::get('fetch/check_payment_request_after', 'AccountingController@fetchCheckBillingPaymentRequestAfter');
    Route::post('post/accounting/payment_request', 'AccountingController@postAccountingPaymentRequest');

    // DOCUMENT ARCHIVE
    Route::get('index/document_archive', 'GeneralController@indexDocumentArchive');
    Route::get('fetch/document_archive', 'GeneralController@fetchDocumentArchive');

    Route::get('billing/list_payment', 'AccountingController@indexBillingListPayment');
    Route::get('fetch/billing/list_payment', 'AccountingController@fetchBillingListPayment');
    Route::post('billing/create/list_payment', 'AccountingController@createBillingListPayment');
});

// OVERTIME MONITORING
Route::get('index/overtime/monitoring', 'OvertimeController@indexOvertimeMonitoring');

Route::get('index/manpower/information_management', 'EmployeeController@indexManpowerInformationManagement');
Route::get('fetch/manpower/information_management', 'EmployeeController@fetchManpowerInformationManagement');

// REPAIR ROOM
Route::get('index/transaction/repair_room', 'TransactionController@indexRepairRoom');
Route::post('input/transaction/repair_room', 'TransactionController@inputRepairRoom');

// TRANSACTION
Route::get('index/completion_only', 'TransactionController@indexCompletionOnly');
Route::post('input/completion_only', 'TransactionController@inputCompletionOnly');
Route::get('index/kanban/completion', 'TransactionController@indexCompletion');
Route::post('input/kanban/completion', 'TransactionController@inputCompletion');

// IN OUT
// Route::get('index/material_in_out', 'InOutController@indexMaterialInOut');
Route::get('fetch/material_in_out/tag_out', 'InOutController@fetchMaterialTagOut');
Route::post('input/material_in_out/tag_out', 'InOutController@inputMaterialTagOut');
Route::post('input/material_in_out/tag_in', 'InOutController@inputMaterialTagIn');
Route::post('update/material_in_out/exchange', 'InOutController@updateMaterialTagExchange');

Route::get('index/material_in_out', 'InOutController@indexInOut');
Route::get('fetch/material_in_out', 'InOutController@fetchInOutTag');
Route::post('input/material_in_out', 'InOutController@inputInOut');
Route::post('delete/material_in_out', 'InOutController@deleteIntransit');
Route::get('fetch/material_in_out/intransit', 'InOutController@fetchInOutIntransit');
Route::get('fetch/material_in_out/intransit_khusus', 'InOutController@fetchInOutIntransitKhusus');

Route::get('index/in_out_monitoring', 'InOutController@indexInOutMonitoring');
Route::get('index/in_out_log', 'InOutController@indexInOutLog');
Route::get('index/in_out_stock', 'InOutController@indexInOutStock');
Route::get('index/in_out_compare', 'InOutController@indexInOutCompare');
Route::get('fetch/in_out_monitoring', 'InOutController@fetchInOutMonitoring');
Route::get('fetch/in_out_log', 'InOutController@fetchInOutLog');
Route::get('fetch/in_out_stock', 'InOutController@fetchInOutStock');
Route::get('fetch/in_out_compare', 'InOutController@fetchInOutCompare');

Route::get('index/material_in_out_store', 'InOutController@indexInOutStore');
Route::get('fetch/material_in_out_store', 'InOutController@fetchInOutStoreMaterial');
Route::post('input/material_in_out_store', 'InOutController@inputInOutStore');

Route::get('index/stocktaking_in_out_store', 'InOutController@indexStocktakingInOutStore');
Route::get('fetch/material_in_out/inventory', 'InOutController@fetchInOutStoreInventory');
Route::post('input/stocktaking_in_out_store', 'InOutController@inputStocktakingInOutStore');

Route::get('index/stocktaking_store_monitoring', 'InOutController@indexStocktakingStoreMonitoring');
Route::get('fetch/stocktaking_store_monitoring', 'InOutController@fetchStocktakingStoreMonitoring');

Route::get('index/inventory_store_monitoring', 'InOutController@indexInventoryStoreMonitoring');
Route::get('fetch/inventory_store_monitoring', 'InOutController@fetchInventoryStoreMonitoring');

// STAMP LOG
Route::post('input/YMPI_stamp_log', 'GeneralController@inputYMPIStampLog');
Route::post('update/YMPI_stamp_log', 'GeneralController@updateYMPIStampLog');
Route::get('index/YMPI_stamp_log', 'GeneralController@indexYMPIStampLog');
Route::get('fetch/YMPI_stamp_log', 'GeneralController@fetchYMPIStampLog');

// UNION
Route::get('index/employee/union', 'EmployeeController@indexUnion');
Route::post('input/employee/union', 'EmployeeController@inputUnion');

// TEAM ANALYST
Route::get('post/sensor/temperature', 'TrialController@postTemperature');

// S-UP
Route::get('index/sup', 'GeneralAffairController@indexAuditSup');
Route::get('fetch/sup/audit', 'GeneralAffairController@fetchCheckDataSup');
Route::get('audit/sup', 'GeneralAffairController@auditSupIndex');
Route::get('index/audit/sup', 'GeneralAffairController@auditSup');
Route::get('fetch/audit/sup', 'GeneralAffairController@fetchAuditPoint');
Route::post('upload/evidence/sup', 'GeneralAffairController@inputSupEvidence');

Route::get('test/tap', 'TrialController@tapStamp');

//bodyPartsProcess Routess
Route::get('index/process_body_parts_process_fl', 'BodyPartsProcessController@indexFL')->name('bodyPartsProcessFLIndex');
Route::get('index/process_body_parts_process_sx', 'BodyPartsProcessController@indexSX')->name('bodyPartsProcessSXIndex');

//Master Operator
Route::get('index/body_parts_process/operator/{loc}', 'BodyPartsProcessController@indexMasterOperator')->name('bodyPartsProcessMasterOperator');
Route::get('fetch/body_parts_process/operator', 'BodyPartsProcessController@fetchMasterOperator');
Route::post('input/body_parts_process/operator', 'BodyPartsProcessController@inputOperator');
Route::get('delete/body_parts_process/operator', 'BodyPartsProcessController@deleteOperator');
Route::post('update/body_parts_process/operator', 'BodyPartsProcessController@updateOperator');

//Master Kanban
Route::get('index/body_parts_process/master_kanban/{loc}', 'BodyPartsProcessController@indexMasterKanban')->name('bodyPartsProcessMasterKanban');
Route::get('fetch/body_parts_process/kanban', 'BodyPartsProcessController@fetchMasterKanban');
Route::post('update/body_parts_process/kanban', 'BodyPartsProcessController@updateKanban');
Route::post('input/body_parts_process/kanban', 'BodyPartsProcessController@inputKanban');
Route::get('delete/body_parts_process/kanban', 'BodyPartsProcessController@deleteKanban');

//Master Flow
Route::get('index/body_parts_process/master_flow/{loc}', 'BodyPartsProcessController@indexMasterFlows')->name('bodyPartsProcessMasterFlow');
Route::get('index/body_parts_process/master_allflow/{loc}', 'BodyPartsProcessController@indexMasterFlow');
Route::get('fetch/body_parts_process/master_flow', 'BodyPartsProcessController@fetchMasterFlow');
Route::get('fetch/body_parts_process/singleFlow', 'BodyPartsProcessController@fetchSingleFlow');
Route::post('update/body_parts_process/changeFlow', 'BodyPartsProcessController@changeFlow');

Route::post('update/body_parts_process/kanban', 'BodyPartsProcessController@updateFlow');
Route::post('input/body_parts_process/kanban', 'BodyPartsProcessController@inputFlow');
Route::get('delete/body_parts_process/kanban', 'BodyPartsProcessController@deleteFlow');

//Master Target
Route::get('index/body_parts_process/bpro_target/{loc}', 'BodyPartsProcessController@indexBproTarget')->name('bodyPartsProcessMasterTarget');
Route::get('fetch/body_parts_process/bpro_target', 'BodyPartsProcessController@fetchBproTarget');
Route::post('update/body_parts_process/bpro_target', 'BodyPartsProcessController@updateBproTarget');

//Kensa
Route::get('index/body_parts_process/kensa/{id}', 'BodyPartsProcessController@indexBproKensa')->name('bodyPartsProcessKensa');
Route::get('scan/body_parts_process/operator', 'BodyPartsProcessController@scanBproOperator');
Route::get('scan/body_parts_process/kensa', 'BodyPartsProcessController@scanBproKensa');
Route::get('fetch/body_parts_process/kensa_result', 'BodyPartsProcessController@fetchKensaResult');

//Production Result
Route::get('index/body_parts_process/display_production_result/{loc}', 'BodyPartsProcessController@indexDisplayProductionResult')->name('bodyPartsProcessProductionResult');
Route::get('fetch/body_parts_process/display_production_resultt', 'BodyPartsProcessController@fetchDisplayProductionResult');
Route::get('fetch/body_parts_process/display_production_result', 'BodyPartsProcessController@fetchDisplayProductionResult2');

//Display Board
Route::get('index/body_parts_process/bpro_board/{loc}', 'BodyPartsProcessController@indexBproBoard')->name('bodyPartsProcessBoard');
Route::get('fetch/body_parts_process/bpro_board/', 'BodyPartsProcessController@fetchBproBoard');

Route::get('index/body_parts_process/ng_rate/{loc}', 'BodyPartsProcessController@indexNgRate')->name('bodyPartsProcessNgRate');
Route::get('fetch/body_parts_process/ng_rate', 'BodyPartsProcessController@fetchNgRate');
Route::get('fetch/body_parts_process/ng_rate/detail', 'BodyPartsProcessController@fetchNgRateDetail');

//NG_Operator
Route::get('index/body_parts_process/op_ng/{loc}', 'BodyPartsProcessController@indexOpRate')->name('bodyPartsProcessNgOperator');
Route::get('fetch/body_parts_process/op_ng', 'BodyPartsProcessController@fetchOpRate');
Route::get('fetch/body_parts_process/op_ng_detail', 'BodyPartsProcessController@fetchOpRateDetail');

//Report
Route::get('index/body_parts_process/report_ng/{loc}', 'BodyPartsProcessController@indexReportNG')->name('bodyPartsProcessNgRecord');
Route::get('fetch/body_parts_process/report_ng', 'BodyPartsProcessController@fetchReportNG');

//Resume
Route::get('index/body_parts_process/report_resume_ng/{loc}', 'BodyPartsProcessController@indexReportResumeNg')->name('bodyPartsProcessNgResume');

Route::get('fetch/body_parts_process/ng_rate_monthly', 'BodyPartsProcessController@fetchNgRateMonthly');
Route::get('fetch/body_parts_process/ng_rate_weekly', 'BodyPartsProcessController@fetchNgRateWeekly');
Route::get('fetch/body_parts_process/ng_rate_daily', 'BodyPartsProcessController@fetchNgDaily');
Route::get('fetch/body_parts_process/ng_monthly', 'BodyPartsProcessController@fetchNgMonthly');
Route::get('fetch/body_parts_process/ng_key_monthly', 'BodyPartsProcessController@fetchNgKeyMonthly');

//bpro_target routes
Route::get('index/body_parts_process/bpro_target', 'BodyPartsProcessController@indexBproTarget')->name('bodyPartsProcessBproTarget');
Route::get('fetch/body_parts_process/bpro_target', 'BodyPartsProcessController@fetchBproTarget');
Route::post('update/body_parts_process/bpro_target', 'BodyPartsProcessController@updateBproTarget');
Route::post('input/body_parts_process/bpro_target', 'BodyPartsProcessController@inputBproTarget');
Route::get('delete/body_parts_process/bpro_target', 'BodyPartsProcessController@deleteBproTarget');

Route::get('index/body_parts_process/master_material/{loc}', 'BodyPartsProcessController@indexMasterMaterial')->name('bodyPartsProcessMasterMaterials');
Route::get('fetch/body_parts_process/master_material', 'BodyPartsProcessController@fetchMasterMaterial');

Route::post('input/body_parts_process/rework', 'BodyPartsProcessController@inputBproRework');
Route::post('input/body_parts_process/kensa', 'BodyPartsProcessController@inputBproKensa');

Route::post('update/body_parts_process/master_material', 'BodyPartsProcessController@updateMasterMaterial');

Route::get('index/calendar', 'WeeklyCalendarController@indexCalendar');
Route::get('fetch/calendar', 'WeeklyCalendarController@fetchCalendar');

//End BPRO
