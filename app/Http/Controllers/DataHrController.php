<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Excel;
use File;
use DataTables;
use Response;
use DateTime;
use App\User;
use App\Employee;
use App\EmployeeUpdate;
use App\EmployeeAnswer;
use Session;
use Carbon\Carbon;

class DataHrController extends Controller
{
  //   public function __construct(){
  //     $this->middleware('auth');
  // }
  public function __construct(){
    $this->darurat = [
      'SAUDARA KANDUNG',
      'IPAR',
      'ANAK KANDUNG',
      'SEPUPU',
      'KAKEK NENEK',
      'TETANGGA',
      'KEPONAKAN',
      'ORANG TUA KANDUNG',
      'MERTUA KANDUNG',
      'PASANGAN',
      'PAMAN BIBI'
    ];

    $this->pilihan_seragam = [
      'Laki-laki (Lengan Pendek)',
      'Perempuan (Lengan Panjang)',
      'Perempuan (Lengan Pendek)'
    ];

    $this->ukuran_seragam = [
      'S',
      'M',
      'L',
      'XL',
      'XXL',
      'XXXL'
    ];
  }

  public function TrialData(Request $request, $nik){

    // $isi = Recruitment::where('request_id', $request_id)
    // ->select('request_id', 'position', 'department', 'create_section', 'create_group', 'create_sub_group', 'employment_status', 'quantity_male', 'quantity_female', 'reason', 'start_date', 'min_age', 'max_age', 'marriage_status', 'recruitments.major', 'status_at', 'status_req', 'recruitments.created_at', 'users.name')
    // ->leftJoin('users', 'users.id', '=', 'recruitments.created_by')
    // ->first();
    $isi = EmployeeUpdate::where('nik', $nik)
    ->select('id', 'employee_id', 'name', 'nik', 'npwp', 'gender', 'birth_place', 'birth_date', 'religion', 'mariage_status', 'address', 'current_address', 'telephone', 'handphone', 'email', 'bpjskes', 'faskes', 'bpjstk', 'f_ayah', 'f_ibu', 'f_saudara1', 'f_saudara2', 'f_saudara3', 'f_saudara4', 'f_saudara5', 'f_saudara6', 'f_saudara7', 'f_saudara8', 'f_saudara9', 'f_saudara10', 'f_saudara11', 'f_saudara12', 'm_pasangan', 'm_anak1', 'm_anak2', 'm_anak3', 'm_anak4', 'm_anak5', 'm_anak6', 'm_anak7', 'sd', 'smp', 'sma', 's1', 's2', 's3', 'emergency1', 'emergency2', 'emergency3', 'created_by')
    ->first();

    $answer = EmployeeAnswer::where('id_emp', $isi->id)
    ->select('id_emp','answer1','answer2','answer3','answer4','answer5','answer6','answer7','answer8','answer9','answer10','answer11','answer12','created_at','updated_at')
    ->first();

    return view('hr_data.trial', array(
      'isi' => $isi,
      'answer' => $answer
   ));
  }
  
    public function InputCalonKaryawan(Request $request){
      $nama = db::table('employee_updates')->orderBy('name', 'ASC')->get();

      $title = 'Prospective Employee Data';
      $title_jp = '従業員候補のデータ';

      return view('hr_data.input', array(
       'title' => $title,
       'title_jp' => $title_jp,
       // 'nama' => $nama,
       'darurats' => $this->darurat,
       'pilihan_seragam' => $this->pilihan_seragam,
       'ukuran_seragam' => $this->ukuran_seragam
     ));
  }

    public function EditCalonKaryawan(Request $request){
      $nama = db::table('employee_updates')->orderBy('name', 'ASC')->get();

      $title = 'Prospective Employee Data';
      $title_jp = '従業員候補のデータ';

      return view('hr_data.edit_calon_karyawan', array(
       'title' => $title,
       'title_jp' => $title_jp,
       // 'nama' => $nama,
       'darurats' => $this->darurat,
     ));
  }

    public function IndexCalonKaryawan(Request $request){

      $title = 'Prospective Employee Data Report';
      $title_jp = '';
      $username = strtoupper(Auth::user()->username);

      return view('hr_data.index_calon', array(
       'title' => $title,
       'title_jp' => $title_jp,
       'username' => $username
   ));
  }

  public function FetchCalonKaryawan(Request $request)
  {
    try {
      $username = strtoupper(Auth::user()->username);
      $dateto   = $request->get('dateto');
      $datefrom = $request->get('datefrom');

      if (($dateto || $datefrom) == null) {
        $data = db::select('select id, employee_id, `name`, nik, npwp, birth_place, birth_date, religion, mariage_status, address, handphone, DATE_FORMAT( created_at, "%d-%m-%Y") as tanggal from employee_updates where employee_id is null');
      }else{
        $data = db::select('select id, employee_id, `name`, nik, npwp, birth_place, birth_date, religion, mariage_status, address, handphone, DATE_FORMAT( created_at, "%d-%m-%Y") as tanggal from employee_updates where employee_id is null AND DATE_FORMAT( created_at, "%Y-%m-%d") >= "'.$datefrom.'" AND DATE_FORMAT( created_at, "%Y-%m-%d") <= "'.$dateto.'"');
      }

      $years = date('y');
      $month = date('m');
      $a_nik = 'PI'.$years.$month;
      $response = array(
          'status' => true,
          'data' => $data,
          'username' => $username,
          'a_nik' => $a_nik
      );
      return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
  );
    return Response::json($response);
  }
 }

   public function InsertDataCalonKaryawan(Request $request){

    $nama_lengkap = $request->get('nama_lengkap');
    $employee_id = $request->get('employee_id');
    $nik = $request->get('nik');
    $npwp = $request->get('npwp');
    $tempat_lahir = $request->get('tempat_lahir');
    $tanggal_lahir = date('Y-m-d', strtotime($request->get('tanggal_lahir')));
    $agama = $request->get('agama');
    $status_perkawinan = $request->get('status_perkawinan');
    $telepon_rumah = $request->get('telepon_rumah');
    $hp = $request->get('hp');
    $email = $request->get('email');
    $bpjskes = $request->get('bpjskes');
    $faskes = $request->get('faskes');
    $bpjstk = $request->get('bpjstk');

    $nama_ayah = $request->get("nama_ayah");
    $kelamin_ayah = $request->get("kelamin_ayah");
    $tempat_lahir_ayah = $request->get("tempat_lahir_ayah");
    $tanggal_lahir_ayah = $request->get("tanggal_lahir_ayah");
    $pekerjaan_ayah = $request->get("pekerjaan_ayah");
    $f_ayah = $nama_ayah.'_'.$kelamin_ayah.'_'.$tempat_lahir_ayah.'_'.$tanggal_lahir_ayah.'_'.$pekerjaan_ayah;

    $nama_ibu = $request->get("nama_ibu");
    $kelamin_ibu = $request->get("kelamin_ibu");
    $tempat_lahir_ibu = $request->get("tempat_lahir_ibu");
    $tanggal_lahir_ibu = $request->get("tanggal_lahir_ibu");
    $pekerjaan_ibu = $request->get("pekerjaan_ibu");
    $f_ibu = $nama_ibu.'_'.$kelamin_ibu.'_'.$tempat_lahir_ibu.'_'.$tanggal_lahir_ibu.'_'.$pekerjaan_ibu;

    $nama_saudara1 = $request->get("nama_saudara1");
    $kelamin_saudara1 = $request->get("kelamin_saudara1");
    $tempat_lahir_saudara1 = $request->get("tempat_lahir_saudara1");
    $tanggal_lahir_saudara1 = $request->get("tanggal_lahir_saudara1");
    $pekerjaan_saudara1 = $request->get("pekerjaan_saudara1");
    $f_saudara1 = $nama_saudara1.'_'.$kelamin_saudara1.'_'.$tempat_lahir_saudara1.'_'.$tanggal_lahir_saudara1.'_'.$pekerjaan_saudara1;

    $nama_saudara2 = $request->get("nama_saudara2");
    $kelamin_saudara2 = $request->get("kelamin_saudara2");
    $tempat_lahir_saudara2 = $request->get("tempat_lahir_saudara2");
    $tanggal_lahir_saudara2 = $request->get("tanggal_lahir_saudara2");
    $pekerjaan_saudara2 = $request->get("pekerjaan_saudara2");
    $f_saudara2 = $nama_saudara2.'_'.$kelamin_saudara2.'_'.$tempat_lahir_saudara2.'_'.$tanggal_lahir_saudara2.'_'.$pekerjaan_saudara2;

    $nama_saudara3 = $request->get("nama_saudara3");
    $kelamin_saudara3 = $request->get("kelamin_saudara3");
    $tempat_lahir_saudara3 = $request->get("tempat_lahir_saudara3");
    $tanggal_lahir_saudara3 = $request->get("tanggal_lahir_saudara3");
    $pekerjaan_saudara3 = $request->get("pekerjaan_saudara3");
    $f_saudara3 = $nama_saudara3.'_'.$kelamin_saudara3.'_'.$tempat_lahir_saudara3.'_'.$tanggal_lahir_saudara3.'_'.$pekerjaan_saudara3;

    $nama_saudara4 = $request->get("nama_saudara4");
    $kelamin_saudara4 = $request->get("kelamin_saudara4");
    $tempat_lahir_saudara4 = $request->get("tempat_lahir_saudara4");
    $tanggal_lahir_saudara4 = $request->get("tanggal_lahir_saudara4");
    $pekerjaan_saudara4 = $request->get("pekerjaan_saudara4");
    $f_saudara4 = $nama_saudara4.'_'.$kelamin_saudara4.'_'.$tempat_lahir_saudara4.'_'.$tanggal_lahir_saudara4.'_'.$pekerjaan_saudara4;

    $nama_saudara5 = $request->get("nama_saudara5");
    $kelamin_saudara5 = $request->get("kelamin_saudara5");
    $tempat_lahir_saudara5 = $request->get("tempat_lahir_saudara5");
    $tanggal_lahir_saudara5 = $request->get("tanggal_lahir_saudara5");
    $pekerjaan_saudara5 = $request->get("pekerjaan_saudara5");
    $f_saudara5 = $nama_saudara5.'_'.$kelamin_saudara5.'_'.$tempat_lahir_saudara5.'_'.$tanggal_lahir_saudara5.'_'.$pekerjaan_saudara5;

    $nama_saudara6 = $request->get("nama_saudara6");
    $kelamin_saudara6 = $request->get("kelamin_saudara6");
    $tempat_lahir_saudara6 = $request->get("tempat_lahir_saudara6");
    $tanggal_lahir_saudara6 = $request->get("tanggal_lahir_saudara6");
    $pekerjaan_saudara6 = $request->get("pekerjaan_saudara6");
    $f_saudara6 = $nama_saudara6.'_'.$kelamin_saudara6.'_'.$tempat_lahir_saudara6.'_'.$tanggal_lahir_saudara6.'_'.$pekerjaan_saudara6;

    $nama_saudara7 = $request->get("nama_saudara7");
    $kelamin_saudara7 = $request->get("kelamin_saudara7");
    $tempat_lahir_saudara7 = $request->get("tempat_lahir_saudara7");
    $tanggal_lahir_saudara7 = $request->get("tanggal_lahir_saudara7");
    $pekerjaan_saudara7 = $request->get("pekerjaan_saudara7");
    $f_saudara7 = $nama_saudara7.'_'.$kelamin_saudara7.'_'.$tempat_lahir_saudara7.'_'.$tanggal_lahir_saudara7.'_'.$pekerjaan_saudara7;

    $nama_saudara8 = $request->get("nama_saudara8");
    $kelamin_saudara8 = $request->get("kelamin_saudara8");
    $tempat_lahir_saudara8 = $request->get("tempat_lahir_saudara8");
    $tanggal_lahir_saudara8 = $request->get("tanggal_lahir_saudara8");
    $pekerjaan_saudara8 = $request->get("pekerjaan_saudara8");
    $f_saudara8 = $nama_saudara8.'_'.$kelamin_saudara8.'_'.$tempat_lahir_saudara8.'_'.$tanggal_lahir_saudara8.'_'.$pekerjaan_saudara8;

    $nama_saudara9 = $request->get("nama_saudara9");
    $kelamin_saudara9 = $request->get("kelamin_saudara9");
    $tempat_lahir_saudara9 = $request->get("tempat_lahir_saudara9");
    $tanggal_lahir_saudara9 = $request->get("tanggal_lahir_saudara9");
    $pekerjaan_saudara9 = $request->get("pekerjaan_saudara9");
    $f_saudara9 = $nama_saudara9.'_'.$kelamin_saudara9.'_'.$tempat_lahir_saudara9.'_'.$tanggal_lahir_saudara9.'_'.$pekerjaan_saudara9;

    $nama_saudara10 = $request->get("nama_saudara10");
    $kelamin_saudara10 = $request->get("kelamin_saudara10");
    $tempat_lahir_saudara10 = $request->get("tempat_lahir_saudara10");
    $tanggal_lahir_saudara10 = $request->get("tanggal_lahir_saudara10");
    $pekerjaan_saudara10 = $request->get("pekerjaan_saudara10");
    $f_saudara10 = $nama_saudara10.'_'.$kelamin_saudara10.'_'.$tempat_lahir_saudara10.'_'.$tanggal_lahir_saudara10.'_'.$pekerjaan_saudara10;

    $nama_saudara11 = $request->get("nama_saudara11");
    $kelamin_saudara11 = $request->get("kelamin_saudara11");
    $tempat_lahir_saudara11 = $request->get("tempat_lahir_saudara11");
    $tanggal_lahir_saudara11 = $request->get("tanggal_lahir_saudara11");
    $pekerjaan_saudara11 = $request->get("pekerjaan_saudara11");
    $f_saudara11 = $nama_saudara11.'_'.$kelamin_saudara11.'_'.$tempat_lahir_saudara11.'_'.$tanggal_lahir_saudara11.'_'.$pekerjaan_saudara11;

    $nama_saudara12 = $request->get("nama_saudara12");
    $kelamin_saudara12 = $request->get("kelamin_saudara12");
    $tempat_lahir_saudara12 = $request->get("tempat_lahir_saudara12");
    $tanggal_lahir_saudara12 = $request->get("tanggal_lahir_saudara12");
    $pekerjaan_saudara12 = $request->get("pekerjaan_saudara12");
    $f_saudara12 = $nama_saudara12.'_'.$kelamin_saudara12.'_'.$tempat_lahir_saudara12.'_'.$tanggal_lahir_saudara12.'_'.$pekerjaan_saudara12;

    $nama_pasangan = $request->get("nama_pasangan");
    $kelamin_pasangan = $request->get("kelamin_pasangan");
    $tempat_lahir_pasangan = $request->get("tempat_lahir_pasangan");
    $tanggal_lahir_pasangan = $request->get("tanggal_lahir_pasangan");
    $pekerjaan_pasangan = $request->get("pekerjaan_pasangan");
    $m_pasangan = $nama_pasangan.'_'.$kelamin_pasangan.'_'.$tempat_lahir_pasangan.'_'.$tanggal_lahir_pasangan.'_'.$pekerjaan_pasangan;

    $nama_anak1 = $request->get("nama_anak1");
    $kelamin_anak1 = $request->get("kelamin_anak1");
    $tempat_lahir_anak1 = $request->get("tempat_lahir_anak1");
    $tanggal_lahir_anak1 = $request->get("tanggal_lahir_anak1");
    $pekerjaan_anak1 = $request->get("pekerjaan_anak1");
    $m_anak1 = $nama_anak1.'_'.$kelamin_anak1.'_'.$tempat_lahir_anak1.'_'.$tanggal_lahir_anak1.'_'.$pekerjaan_anak1;

    $nama_anak2 = $request->get("nama_anak2");
    $kelamin_anak2 = $request->get("kelamin_anak2");
    $tempat_lahir_anak2 = $request->get("tempat_lahir_anak2");
    $tanggal_lahir_anak2 = $request->get("tanggal_lahir_anak2");
    $pekerjaan_anak2 = $request->get("pekerjaan_anak2");
    $m_anak2 = $nama_anak2.'_'.$kelamin_anak2.'_'.$tempat_lahir_anak2.'_'.$tanggal_lahir_anak2.'_'.$pekerjaan_anak2;

    $nama_anak3 = $request->get("nama_anak3");
    $kelamin_anak3 = $request->get("kelamin_anak3");
    $tempat_lahir_anak3 = $request->get("tempat_lahir_anak3");
    $tanggal_lahir_anak3 = $request->get("tanggal_lahir_anak3");
    $pekerjaan_anak3 = $request->get("pekerjaan_anak3");
    $m_anak3 = $nama_anak3.'_'.$kelamin_anak3.'_'.$tempat_lahir_anak3.'_'.$tanggal_lahir_anak3.'_'.$pekerjaan_anak3;

    $nama_anak4 = $request->get("nama_anak4");
    $kelamin_anak4 = $request->get("kelamin_anak4");
    $tempat_lahir_anak4 = $request->get("tempat_lahir_anak4");
    $tanggal_lahir_anak4 = $request->get("tanggal_lahir_anak4");
    $pekerjaan_anak4 = $request->get("pekerjaan_anak4");
    $m_anak4 = $nama_anak4.'_'.$kelamin_anak4.'_'.$tempat_lahir_anak4.'_'.$tanggal_lahir_anak4.'_'.$pekerjaan_anak4;

    $nama_anak5 = $request->get("nama_anak5");
    $kelamin_anak5 = $request->get("kelamin_anak5");
    $tempat_lahir_anak5 = $request->get("tempat_lahir_anak5");
    $tanggal_lahir_anak5 = $request->get("tanggal_lahir_anak5");
    $pekerjaan_anak5 = $request->get("pekerjaan_anak5");
    $m_anak5 = $nama_anak5.'_'.$kelamin_anak5.'_'.$tempat_lahir_anak5.'_'.$tanggal_lahir_anak5.'_'.$pekerjaan_anak5;

    $nama_anak6 = $request->get("nama_anak6");
    $kelamin_anak6 = $request->get("kelamin_anak6");
    $tempat_lahir_anak6 = $request->get("tempat_lahir_anak6");
    $tanggal_lahir_anak6 = $request->get("tanggal_lahir_anak6");
    $pekerjaan_anak6 = $request->get("pekerjaan_anak6");
    $m_anak6 = $nama_anak6.'_'.$kelamin_anak6.'_'.$tempat_lahir_anak6.'_'.$tanggal_lahir_anak6.'_'.$pekerjaan_anak6;

    $nama_anak7 = $request->get("nama_anak7");
    $kelamin_anak7 = $request->get("kelamin_anak7");
    $tempat_lahir_anak7 = $request->get("tempat_lahir_anak7");
    $tanggal_lahir_anak7 = $request->get("tanggal_lahir_anak7");
    $pekerjaan_anak7 = $request->get("pekerjaan_anak7");
    $m_anak7 = $nama_anak7.'_'.$kelamin_anak7.'_'.$tempat_lahir_anak7.'_'.$tanggal_lahir_anak7.'_'.$pekerjaan_anak7;

    $sd_nama = $request->get("sd");
    $sd_masuk = $request->get("sd_masuk");
    $sd_lulus = $request->get("sd_lulus");
    $sd = $sd_nama.'_-_'.$sd_masuk.'_'.$sd_lulus;

    $smp_nama = $request->get("smp");
    $smp_masuk = $request->get("smp_masuk");
    $smp_lulus = $request->get("smp_lulus");
    $smp = $smp_nama.'_-_'.$smp_masuk.'_'.$smp_lulus;

    $sma_nama = $request->get("sma");
    $sma_jurusan = $request->get("sma_jurusan");
    $sma_masuk = $request->get("sma_masuk");
    $sma_lulus = $request->get("sma_lulus");
    $sma = $sma_nama.'_'.$sma_jurusan.'_'.$sma_masuk.'_'.$sma_lulus;

    $s1_nama = $request->get("s1");
    $s1_jurusan = $request->get("s1_jurusan");
    $s1_masuk = $request->get("s1_masuk");
    $s1_lulus = $request->get("s1_lulus");
    $s1 = $s1_nama.'_'.$s1_jurusan.'_'.$s1_masuk.'_'.$s1_lulus;

    $s2_nama = $request->get("s2");
    $s2_jurusan = $request->get("s2_jurusan");
    $s2_masuk = $request->get("s2_masuk");
    $s2_lulus = $request->get("s2_lulus");
    $s2 = $s2_nama.'_'.$s2_jurusan.'_'.$s2_masuk.'_'.$s2_lulus;

    $s3_nama = $request->get("s3");
    $s3_jurusan = $request->get("s3_jurusan");
    $s3_masuk = $request->get("s3_masuk");
    $s3_lulus = $request->get("s3_lulus");
    $s3 = $s3_nama.'_'.$s3_jurusan.'_'.$s3_masuk.'_'.$s3_lulus;

    $nama_darurat1 = $request->get("nama_darurat1");
    $telp_darurat1 = $request->get("telp_darurat1");
    $pekerjaan_darurat1 = $request->get("pekerjaan_darurat1");
    $hubungan_darurat1 = $request->get("hubungan_darurat1");
    $emergency1 = $nama_darurat1.'_'.$telp_darurat1.'_'.$pekerjaan_darurat1.'_'.$hubungan_darurat1;

    $nama_darurat2 = $request->get("nama_darurat2");
    $telp_darurat2 = $request->get("telp_darurat2");
    $pekerjaan_darurat2 = $request->get("pekerjaan_darurat2");
    $hubungan_darurat2 = $request->get("hubungan_darurat2");
    $emergency2 = $nama_darurat2.'_'.$telp_darurat2.'_'.$pekerjaan_darurat2.'_'.$hubungan_darurat2;

    $nama_darurat3 = $request->get("nama_darurat3");
    $telp_darurat3 = $request->get("telp_darurat3");
    $pekerjaan_darurat3 = $request->get("pekerjaan_darurat3");
    $hubungan_darurat3 = $request->get("hubungan_darurat3");
    $emergency3 = $nama_darurat3.'_'.$telp_darurat3.'_'.$pekerjaan_darurat3.'_'.$hubungan_darurat3;

    $seragam = $request->get("ukuran_seragam");

    $n1 = $request->get('1');
    $no1 = $request->get('no_1');
    $jawaban1 = $n1.'/'.$no1;

    $n2 = $request->get('2');
    $no2 = $request->get('no_2');
    $jawaban2 = $n2.'/'.$no2;

    $n3 = $request->get('3');
    $no3 = $request->get('no_3');
    $jawaban3 = $n3.'/'.$no3;

    $no4 = $request->get('no_4');
    $no5 = $request->get('no_5');

    $n6 = $request->get('6');
    $no6 = $request->get('no_6');
    $jawaban6 = $n6.'/'.$no6;

    $no7 = $request->get('no_7');
    $no8 = $request->get('no_8');
    $no9 = $request->get('no_9');
    $no10 = $request->get('no_10');
    $no11 = $request->get('no_11');
    $no12 = $request->get('no_12');

    $alamat_asal = $request->get('alamat_asal');
    $rt_asal = $request->get('rt_asal');
    $rw_asal = $request->get('rw_asal');
    $kelurahan_asal = $request->get('kelurahan_asal');
    $kecamatan_asal = $request->get('kecamatan_asal');
    $kota_asal = $request->get('kota_asal');
    $alt_asal = $alamat_asal.'/'.$rt_asal.'/'.$rw_asal.'/'.$kelurahan_asal.'/'.$kecamatan_asal.'/'.$kota_asal;

    $alamat_domisili = $request->get('alamat_domisili');
    $rt_domisili = $request->get('rt_domisili');
    $rw_domisili = $request->get('rw_domisili');
    $kelurahan_domisili = $request->get('kelurahan_domisili');
    $kecamatan_domisili = $request->get('kecamatan_domisili');
    $kota_domisili = $request->get('kota_domisili');
    $alt_domisili = $alamat_domisili.'/'.$rt_domisili.'/'.$rw_domisili.'/'.$kelurahan_domisili.'/'.$kecamatan_domisili.'/'.$kota_domisili;

    $gender = $request->get('gender');

    try {

   //   if($request->hasFile('attach')) {
   //    $empAtt = EmployeeAttachment::where('employee_id', strtoupper($nama_lengkap))->get();
   //    $count = count($empAtt);

   //    $files = $request->file('attach');
   //    foreach ($files as $file) {

   //     $file_name = $employee_id.'('.++$count.').'.$file->getClientOriginalExtension();
   //     $file->move(public_path('employee_files/'), $file_name);


   //     $attachment = new EmployeeAttachment([
   //      'employee_id' => strtoupper($nama_lengkap),
   //      'file_path' => "/employee_files/".$file_name,
   //      'created_by' => strtoupper(Auth::user()->username),
   //    ]);
   //     $attachment->save();
   //   } 
   // }

   $data = new EmployeeUpdate([
   // $data = EmployeeUpdate::where('nik', $nik)->updateOrCreate([
     'employee_id' => $employee_id,
     'name' => $nama_lengkap,
     'nik' => $nik,
     'npwp' => $npwp,
     'gender' => $gender,
     'birth_place' => $tempat_lahir,
     'birth_date' => $tanggal_lahir,
     'religion' => $agama,
     'mariage_status' => $status_perkawinan,
     'address' => $alt_asal,
     'current_address' => $alt_domisili,
     'telephone' => $telepon_rumah,
     'handphone' => $hp,
     'email' => strtolower($email),
     'bpjskes' => $bpjskes,
     'faskes' => $faskes,
     'bpjstk' => $bpjstk,
     'f_ayah' => $f_ayah,
     'f_ibu' => $f_ibu,
     'f_saudara1' => $f_saudara1,
     'f_saudara2' => $f_saudara2,
     'f_saudara3' => $f_saudara3,
     'f_saudara4' => $f_saudara4,
     'f_saudara5' => $f_saudara5,
     'f_saudara6' => $f_saudara6,
     'f_saudara7' => $f_saudara7,
     'f_saudara8' => $f_saudara8,
     'f_saudara9' => $f_saudara9,
     'f_saudara10' => $f_saudara10,
     'f_saudara11' => $f_saudara11,
     'f_saudara12' => $f_saudara12,
     'm_pasangan' => $m_pasangan,
     'm_anak1' => $m_anak1,
     'm_anak2' => $m_anak2,
     'm_anak3' => $m_anak3,
     'm_anak4' => $m_anak4,
     'm_anak5' => $m_anak5,
     'm_anak6' => $m_anak6,
     'm_anak7' => $m_anak7,
     'sd' => $sd,
     'smp' => $smp,
     'sma' => $sma,
     's1' => $s1,
     's2' => $s2,
     's3' => $s3,
     'emergency1' => $emergency1,
     'emergency2' => $emergency2,
     'emergency3' => $emergency3,
     'seragam' => $seragam,
     'created_by' => null,
     'created_by' => $nama_lengkap,
     'updated_at' => Carbon::now()
   ]);
   $data->save();

   $emp = EmployeeUpdate::where('nik', $nik)->first();

   $answer = new EmployeeAnswer([
    'id_emp' => $emp->id,
    'answer1' => strtoupper($jawaban1),
    'answer2' => strtoupper($jawaban2),
    'answer3' => strtoupper($jawaban3),
    'answer4' => strtoupper($no4),
    'answer5' => strtoupper($no5),
    'answer6' => strtoupper($jawaban6),
    'answer7' => strtoupper($no7),
    'answer8' => strtoupper($no8),
    'answer9' => strtoupper($no9),
    'answer10' => strtoupper($no10),
    'answer11' => strtoupper($no11),
    'answer12' => strtoupper($no12)
   ]);
   $answer->save();

   // $nama_file = 'HR'.$nik.'.pdf';
   // $files->move('calon_karyawan', $nama_file);

   $isi = EmployeeUpdate::where('nik', $nik)
   ->whereNull('employee_id')
   ->select('id', 'employee_id', 'name', 'nik', 'npwp', 'gender', 'birth_place', 'birth_date', 'religion', 'mariage_status', 'address', 'current_address', 'telephone', 'handphone', 'email', 'bpjskes', 'faskes', 'bpjstk', 'f_ayah', 'f_ibu', 'f_saudara1', 'f_saudara2', 'f_saudara3', 'f_saudara4', 'f_saudara5', 'f_saudara6', 'f_saudara7', 'f_saudara8', 'f_saudara9', 'f_saudara10', 'f_saudara11', 'f_saudara12', 'm_pasangan', 'm_anak1', 'm_anak2', 'm_anak3', 'm_anak4', 'm_anak5', 'm_anak6', 'm_anak7', 'sd', 'smp', 'sma', 's1', 's2', 's3', 'emergency1', 'emergency2', 'emergency3', 'created_by')
   ->first();

   $answer = EmployeeAnswer::where('id_emp', $isi->id)
   ->select('id_emp','answer1','answer2','answer3','answer4','answer5','answer6','answer7','answer8','answer9','answer10','answer11','answer12','created_at','updated_at')
   ->first();

   $pdf = \App::make('dompdf.wrapper');
   $pdf->getDomPDF()->set_option("enable_php", true);
   $pdf->setPaper('A4', 'potrait');
   $pdf->loadView('hr_data.report', array(
    'isi' => $isi,
    'answer' => $answer
   ));
   $pdf->save(public_path() . "/calon_karyawan/HR".$nik.".pdf");

   $response = array(
    'status' => true,
    'message' => 'Calon Karyawan Berhasil Ditambahkan',
  );
   return Response::json($response);
  } catch (Exception $e) {
   $response = array(
    'status' => false,
    'message' => $e->getMessage(),
  );
   return Response::json($response);
  }
  }

  public function UpdateDataCalonKaryawan(Request $request){

    $nama_lengkap = $request->get('nama_lengkap');
    $employee_id = $request->get('employee_id');
    $nik = $request->get('nik');
    $npwp = $request->get('npwp');
    $tempat_lahir = $request->get('tempat_lahir');
    $tanggal_lahir = date('Y-m-d', strtotime($request->get('tanggal_lahir')));
    $agama = $request->get('agama');
    $status_perkawinan = $request->get('status_perkawinan');
    $telepon_rumah = $request->get('telepon_rumah');
    $hp = $request->get('hp');
    $email = $request->get('email');
    $bpjskes = $request->get('bpjskes');
    $faskes = $request->get('faskes');
    $bpjstk = $request->get('bpjstk');

    $nama_ayah = $request->get("nama_ayah");
    $kelamin_ayah = $request->get("kelamin_ayah");
    $tempat_lahir_ayah = $request->get("tempat_lahir_ayah");
    $tanggal_lahir_ayah = $request->get("tanggal_lahir_ayah");
    $pekerjaan_ayah = $request->get("pekerjaan_ayah");
    $f_ayah = $nama_ayah.'_'.$kelamin_ayah.'_'.$tempat_lahir_ayah.'_'.$tanggal_lahir_ayah.'_'.$pekerjaan_ayah;

    $nama_ibu = $request->get("nama_ibu");
    $kelamin_ibu = $request->get("kelamin_ibu");
    $tempat_lahir_ibu = $request->get("tempat_lahir_ibu");
    $tanggal_lahir_ibu = $request->get("tanggal_lahir_ibu");
    $pekerjaan_ibu = $request->get("pekerjaan_ibu");
    $f_ibu = $nama_ibu.'_'.$kelamin_ibu.'_'.$tempat_lahir_ibu.'_'.$tanggal_lahir_ibu.'_'.$pekerjaan_ibu;

    $nama_saudara1 = $request->get("nama_saudara1");
    $kelamin_saudara1 = $request->get("kelamin_saudara1");
    $tempat_lahir_saudara1 = $request->get("tempat_lahir_saudara1");
    $tanggal_lahir_saudara1 = $request->get("tanggal_lahir_saudara1");
    $pekerjaan_saudara1 = $request->get("pekerjaan_saudara1");
    $f_saudara1 = $nama_saudara1.'_'.$kelamin_saudara1.'_'.$tempat_lahir_saudara1.'_'.$tanggal_lahir_saudara1.'_'.$pekerjaan_saudara1;

    $nama_saudara2 = $request->get("nama_saudara2");
    $kelamin_saudara2 = $request->get("kelamin_saudara2");
    $tempat_lahir_saudara2 = $request->get("tempat_lahir_saudara2");
    $tanggal_lahir_saudara2 = $request->get("tanggal_lahir_saudara2");
    $pekerjaan_saudara2 = $request->get("pekerjaan_saudara2");
    $f_saudara2 = $nama_saudara2.'_'.$kelamin_saudara2.'_'.$tempat_lahir_saudara2.'_'.$tanggal_lahir_saudara2.'_'.$pekerjaan_saudara2;

    $nama_saudara3 = $request->get("nama_saudara3");
    $kelamin_saudara3 = $request->get("kelamin_saudara3");
    $tempat_lahir_saudara3 = $request->get("tempat_lahir_saudara3");
    $tanggal_lahir_saudara3 = $request->get("tanggal_lahir_saudara3");
    $pekerjaan_saudara3 = $request->get("pekerjaan_saudara3");
    $f_saudara3 = $nama_saudara3.'_'.$kelamin_saudara3.'_'.$tempat_lahir_saudara3.'_'.$tanggal_lahir_saudara3.'_'.$pekerjaan_saudara3;

    $nama_saudara4 = $request->get("nama_saudara4");
    $kelamin_saudara4 = $request->get("kelamin_saudara4");
    $tempat_lahir_saudara4 = $request->get("tempat_lahir_saudara4");
    $tanggal_lahir_saudara4 = $request->get("tanggal_lahir_saudara4");
    $pekerjaan_saudara4 = $request->get("pekerjaan_saudara4");
    $f_saudara4 = $nama_saudara4.'_'.$kelamin_saudara4.'_'.$tempat_lahir_saudara4.'_'.$tanggal_lahir_saudara4.'_'.$pekerjaan_saudara4;

    $nama_saudara5 = $request->get("nama_saudara5");
    $kelamin_saudara5 = $request->get("kelamin_saudara5");
    $tempat_lahir_saudara5 = $request->get("tempat_lahir_saudara5");
    $tanggal_lahir_saudara5 = $request->get("tanggal_lahir_saudara5");
    $pekerjaan_saudara5 = $request->get("pekerjaan_saudara5");
    $f_saudara5 = $nama_saudara5.'_'.$kelamin_saudara5.'_'.$tempat_lahir_saudara5.'_'.$tanggal_lahir_saudara5.'_'.$pekerjaan_saudara5;

    $nama_saudara6 = $request->get("nama_saudara6");
    $kelamin_saudara6 = $request->get("kelamin_saudara6");
    $tempat_lahir_saudara6 = $request->get("tempat_lahir_saudara6");
    $tanggal_lahir_saudara6 = $request->get("tanggal_lahir_saudara6");
    $pekerjaan_saudara6 = $request->get("pekerjaan_saudara6");
    $f_saudara6 = $nama_saudara6.'_'.$kelamin_saudara6.'_'.$tempat_lahir_saudara6.'_'.$tanggal_lahir_saudara6.'_'.$pekerjaan_saudara6;

    $nama_saudara7 = $request->get("nama_saudara7");
    $kelamin_saudara7 = $request->get("kelamin_saudara7");
    $tempat_lahir_saudara7 = $request->get("tempat_lahir_saudara7");
    $tanggal_lahir_saudara7 = $request->get("tanggal_lahir_saudara7");
    $pekerjaan_saudara7 = $request->get("pekerjaan_saudara7");
    $f_saudara7 = $nama_saudara7.'_'.$kelamin_saudara7.'_'.$tempat_lahir_saudara7.'_'.$tanggal_lahir_saudara7.'_'.$pekerjaan_saudara7;

    $nama_saudara8 = $request->get("nama_saudara8");
    $kelamin_saudara8 = $request->get("kelamin_saudara8");
    $tempat_lahir_saudara8 = $request->get("tempat_lahir_saudara8");
    $tanggal_lahir_saudara8 = $request->get("tanggal_lahir_saudara8");
    $pekerjaan_saudara8 = $request->get("pekerjaan_saudara8");
    $f_saudara8 = $nama_saudara8.'_'.$kelamin_saudara8.'_'.$tempat_lahir_saudara8.'_'.$tanggal_lahir_saudara8.'_'.$pekerjaan_saudara8;

    $nama_saudara9 = $request->get("nama_saudara9");
    $kelamin_saudara9 = $request->get("kelamin_saudara9");
    $tempat_lahir_saudara9 = $request->get("tempat_lahir_saudara9");
    $tanggal_lahir_saudara9 = $request->get("tanggal_lahir_saudara9");
    $pekerjaan_saudara9 = $request->get("pekerjaan_saudara9");
    $f_saudara9 = $nama_saudara9.'_'.$kelamin_saudara9.'_'.$tempat_lahir_saudara9.'_'.$tanggal_lahir_saudara9.'_'.$pekerjaan_saudara9;

    $nama_saudara10 = $request->get("nama_saudara10");
    $kelamin_saudara10 = $request->get("kelamin_saudara10");
    $tempat_lahir_saudara10 = $request->get("tempat_lahir_saudara10");
    $tanggal_lahir_saudara10 = $request->get("tanggal_lahir_saudara10");
    $pekerjaan_saudara10 = $request->get("pekerjaan_saudara10");
    $f_saudara10 = $nama_saudara10.'_'.$kelamin_saudara10.'_'.$tempat_lahir_saudara10.'_'.$tanggal_lahir_saudara10.'_'.$pekerjaan_saudara10;

    $nama_saudara11 = $request->get("nama_saudara11");
    $kelamin_saudara11 = $request->get("kelamin_saudara11");
    $tempat_lahir_saudara11 = $request->get("tempat_lahir_saudara11");
    $tanggal_lahir_saudara11 = $request->get("tanggal_lahir_saudara11");
    $pekerjaan_saudara11 = $request->get("pekerjaan_saudara11");
    $f_saudara11 = $nama_saudara11.'_'.$kelamin_saudara11.'_'.$tempat_lahir_saudara11.'_'.$tanggal_lahir_saudara11.'_'.$pekerjaan_saudara11;

    $nama_saudara12 = $request->get("nama_saudara12");
    $kelamin_saudara12 = $request->get("kelamin_saudara12");
    $tempat_lahir_saudara12 = $request->get("tempat_lahir_saudara12");
    $tanggal_lahir_saudara12 = $request->get("tanggal_lahir_saudara12");
    $pekerjaan_saudara12 = $request->get("pekerjaan_saudara12");
    $f_saudara12 = $nama_saudara12.'_'.$kelamin_saudara12.'_'.$tempat_lahir_saudara12.'_'.$tanggal_lahir_saudara12.'_'.$pekerjaan_saudara12;

    $nama_pasangan = $request->get("nama_pasangan");
    $kelamin_pasangan = $request->get("kelamin_pasangan");
    $tempat_lahir_pasangan = $request->get("tempat_lahir_pasangan");
    $tanggal_lahir_pasangan = $request->get("tanggal_lahir_pasangan");
    $pekerjaan_pasangan = $request->get("pekerjaan_pasangan");
    $m_pasangan = $nama_pasangan.'_'.$kelamin_pasangan.'_'.$tempat_lahir_pasangan.'_'.$tanggal_lahir_pasangan.'_'.$pekerjaan_pasangan;

    $nama_anak1 = $request->get("nama_anak1");
    $kelamin_anak1 = $request->get("kelamin_anak1");
    $tempat_lahir_anak1 = $request->get("tempat_lahir_anak1");
    $tanggal_lahir_anak1 = $request->get("tanggal_lahir_anak1");
    $pekerjaan_anak1 = $request->get("pekerjaan_anak1");
    $m_anak1 = $nama_anak1.'_'.$kelamin_anak1.'_'.$tempat_lahir_anak1.'_'.$tanggal_lahir_anak1.'_'.$pekerjaan_anak1;

    $nama_anak2 = $request->get("nama_anak2");
    $kelamin_anak2 = $request->get("kelamin_anak2");
    $tempat_lahir_anak2 = $request->get("tempat_lahir_anak2");
    $tanggal_lahir_anak2 = $request->get("tanggal_lahir_anak2");
    $pekerjaan_anak2 = $request->get("pekerjaan_anak2");
    $m_anak2 = $nama_anak2.'_'.$kelamin_anak2.'_'.$tempat_lahir_anak2.'_'.$tanggal_lahir_anak2.'_'.$pekerjaan_anak2;

    $nama_anak3 = $request->get("nama_anak3");
    $kelamin_anak3 = $request->get("kelamin_anak3");
    $tempat_lahir_anak3 = $request->get("tempat_lahir_anak3");
    $tanggal_lahir_anak3 = $request->get("tanggal_lahir_anak3");
    $pekerjaan_anak3 = $request->get("pekerjaan_anak3");
    $m_anak3 = $nama_anak3.'_'.$kelamin_anak3.'_'.$tempat_lahir_anak3.'_'.$tanggal_lahir_anak3.'_'.$pekerjaan_anak3;

    $nama_anak4 = $request->get("nama_anak4");
    $kelamin_anak4 = $request->get("kelamin_anak4");
    $tempat_lahir_anak4 = $request->get("tempat_lahir_anak4");
    $tanggal_lahir_anak4 = $request->get("tanggal_lahir_anak4");
    $pekerjaan_anak4 = $request->get("pekerjaan_anak4");
    $m_anak4 = $nama_anak4.'_'.$kelamin_anak4.'_'.$tempat_lahir_anak4.'_'.$tanggal_lahir_anak4.'_'.$pekerjaan_anak4;

    $nama_anak5 = $request->get("nama_anak5");
    $kelamin_anak5 = $request->get("kelamin_anak5");
    $tempat_lahir_anak5 = $request->get("tempat_lahir_anak5");
    $tanggal_lahir_anak5 = $request->get("tanggal_lahir_anak5");
    $pekerjaan_anak5 = $request->get("pekerjaan_anak5");
    $m_anak5 = $nama_anak5.'_'.$kelamin_anak5.'_'.$tempat_lahir_anak5.'_'.$tanggal_lahir_anak5.'_'.$pekerjaan_anak5;

    $nama_anak6 = $request->get("nama_anak6");
    $kelamin_anak6 = $request->get("kelamin_anak6");
    $tempat_lahir_anak6 = $request->get("tempat_lahir_anak6");
    $tanggal_lahir_anak6 = $request->get("tanggal_lahir_anak6");
    $pekerjaan_anak6 = $request->get("pekerjaan_anak6");
    $m_anak6 = $nama_anak6.'_'.$kelamin_anak6.'_'.$tempat_lahir_anak6.'_'.$tanggal_lahir_anak6.'_'.$pekerjaan_anak6;

    $nama_anak7 = $request->get("nama_anak7");
    $kelamin_anak7 = $request->get("kelamin_anak7");
    $tempat_lahir_anak7 = $request->get("tempat_lahir_anak7");
    $tanggal_lahir_anak7 = $request->get("tanggal_lahir_anak7");
    $pekerjaan_anak7 = $request->get("pekerjaan_anak7");
    $m_anak7 = $nama_anak7.'_'.$kelamin_anak7.'_'.$tempat_lahir_anak7.'_'.$tanggal_lahir_anak7.'_'.$pekerjaan_anak7;

    $sd_nama = $request->get("sd");
    $sd_masuk = $request->get("sd_masuk");
    $sd_lulus = $request->get("sd_lulus");
    $sd = $sd_nama.'_-_'.$sd_masuk.'_'.$sd_lulus;

    $smp_nama = $request->get("smp");
    $smp_masuk = $request->get("smp_masuk");
    $smp_lulus = $request->get("smp_lulus");
    $smp = $smp_nama.'_-_'.$smp_masuk.'_'.$smp_lulus;

    $sma_nama = $request->get("sma");
    $sma_jurusan = $request->get("sma_jurusan");
    $sma_masuk = $request->get("sma_masuk");
    $sma_lulus = $request->get("sma_lulus");
    $sma = $sma_nama.'_'.$sma_jurusan.'_'.$sma_masuk.'_'.$sma_lulus;

    $s1_nama = $request->get("s1");
    $s1_jurusan = $request->get("s1_jurusan");
    $s1_masuk = $request->get("s1_masuk");
    $s1_lulus = $request->get("s1_lulus");
    $s1 = $s1_nama.'_'.$s1_jurusan.'_'.$s1_masuk.'_'.$s1_lulus;

    $s2_nama = $request->get("s2");
    $s2_jurusan = $request->get("s2_jurusan");
    $s2_masuk = $request->get("s2_masuk");
    $s2_lulus = $request->get("s2_lulus");
    $s2 = $s2_nama.'_'.$s2_jurusan.'_'.$s2_masuk.'_'.$s2_lulus;

    $s3_nama = $request->get("s3");
    $s3_jurusan = $request->get("s3_jurusan");
    $s3_masuk = $request->get("s3_masuk");
    $s3_lulus = $request->get("s3_lulus");
    $s3 = $s3_nama.'_'.$s3_jurusan.'_'.$s3_masuk.'_'.$s3_lulus;

    $nama_darurat1 = $request->get("nama_darurat1");
    $telp_darurat1 = $request->get("telp_darurat1");
    $pekerjaan_darurat1 = $request->get("pekerjaan_darurat1");
    $hubungan_darurat1 = $request->get("hubungan_darurat1");
    $emergency1 = $nama_darurat1.'_'.$telp_darurat1.'_'.$pekerjaan_darurat1.'_'.$hubungan_darurat1;

    $nama_darurat2 = $request->get("nama_darurat2");
    $telp_darurat2 = $request->get("telp_darurat2");
    $pekerjaan_darurat2 = $request->get("pekerjaan_darurat2");
    $hubungan_darurat2 = $request->get("hubungan_darurat2");
    $emergency2 = $nama_darurat2.'_'.$telp_darurat2.'_'.$pekerjaan_darurat2.'_'.$hubungan_darurat2;

    $nama_darurat3 = $request->get("nama_darurat3");
    $telp_darurat3 = $request->get("telp_darurat3");
    $pekerjaan_darurat3 = $request->get("pekerjaan_darurat3");
    $hubungan_darurat3 = $request->get("hubungan_darurat3");
    $emergency3 = $nama_darurat3.'_'.$telp_darurat3.'_'.$pekerjaan_darurat3.'_'.$hubungan_darurat3;

    $n1 = $request->get('1');
    $no1 = $request->get('no_1');
    $jawaban1 = $n1.'/'.$no1;

    $n2 = $request->get('2');
    $no2 = $request->get('no_2');
    $jawaban2 = $n2.'/'.$no2;

    $n3 = $request->get('3');
    $no3 = $request->get('no_3');
    $jawaban3 = $n3.'/'.$no3;

    $jawaban4 = $request->get('no_4');
    $jawaban5 = $request->get('no_5');

    $n6 = $request->get('6');
    $no6 = $request->get('no_6');
    $jawaban6 = $n6.'/'.$no6;

    $jawaban7 = $request->get('no_7');
    $jawaban8 = $request->get('no_8');
    $jawaban9 = $request->get('no_9');
    $jawaban10 = $request->get('no_10');
    $jawaban11 = $request->get('no_11');
    $jawaban12 = $request->get('no_12');

    $alamat_asal = $request->get('alamat_asal');
    $rt_asal = $request->get('rt_asal');
    $rw_asal = $request->get('rw_asal');
    $kelurahan_asal = $request->get('kelurahan_asal');
    $kecamatan_asal = $request->get('kecamatan_asal');
    $kota_asal = $request->get('kota_asal');
    $alt_asal = $alamat_asal.'/'.$rt_asal.'/'.$rw_asal.'/'.$kelurahan_asal.'/'.$kecamatan_asal.'/'.$kota_asal;

    $alamat_domisili = $request->get('alamat_domisili');
    $rt_domisili = $request->get('rt_domisili');
    $rw_domisili = $request->get('rw_domisili');
    $kelurahan_domisili = $request->get('kelurahan_domisili');
    $kecamatan_domisili = $request->get('kecamatan_domisili');
    $kota_domisili = $request->get('kota_domisili');
    $alt_domisili = $alamat_domisili.'/'.$rt_domisili.'/'.$rw_domisili.'/'.$kelurahan_domisili.'/'.$kecamatan_domisili.'/'.$kota_domisili;

    $gender = $request->get('gender');

    try {
     $data = EmployeeUpdate::where('nik','=', $nik)->update([
       'name' => $nama_lengkap,
       'nik' => $nik,
       'npwp' => $npwp,
       'gender' => $gender,
       'birth_place' => $tempat_lahir,
       'birth_date' => $tanggal_lahir,
       'religion' => $agama,
       'mariage_status' => $status_perkawinan,
       'address' => $alt_asal,
       'current_address' => $alt_domisili,
       'telephone' => $telepon_rumah,
       'handphone' => $hp,
       'email' => strtolower($email),
       'bpjskes' => $bpjskes,
       'faskes' => $faskes,
       'bpjstk' => $bpjstk,
       'f_ayah' => $f_ayah,
       'f_ibu' => $f_ibu,
       'f_saudara1' => $f_saudara1,
       'f_saudara2' => $f_saudara2,
       'f_saudara3' => $f_saudara3,
       'f_saudara4' => $f_saudara4,
       'f_saudara5' => $f_saudara5,
       'f_saudara6' => $f_saudara6,
       'f_saudara7' => $f_saudara7,
       'f_saudara8' => $f_saudara8,
       'f_saudara9' => $f_saudara9,
       'f_saudara10' => $f_saudara10,
       'f_saudara11' => $f_saudara11,
       'f_saudara12' => $f_saudara12,
       'm_pasangan' => $m_pasangan,
       'm_anak1' => $m_anak1,
       'm_anak2' => $m_anak2,
       'm_anak3' => $m_anak3,
       'm_anak4' => $m_anak4,
       'm_anak5' => $m_anak5,
       'm_anak6' => $m_anak6,
       'm_anak7' => $m_anak7,
       'sd' => $sd,
       'smp' => $smp,
       'sma' => $sma,
       's1' => $s1,
       's2' => $s2,
       's3' => $s3,
       'emergency1' => $emergency1,
       'emergency2' => $emergency2,
       'emergency3' => $emergency3,
       'created_by' => null,
       'created_by' => $nama_lengkap,
       'updated_at' => Carbon::now()
     ]);

     $emp = EmployeeUpdate::where('nik', '=', $nik)->first();

     $answer = EmployeeAnswer::where('id_emp','=', $emp->id)->update([
      'answer1' => strtoupper($jawaban1),
      'answer2' => strtoupper($jawaban2),
      'answer3' => strtoupper($jawaban3),
      'answer4' => strtoupper($jawaban4),
      'answer5' => strtoupper($jawaban5),
      'answer6' => strtoupper($jawaban6),
      'answer7' => strtoupper($jawaban7),
      'answer8' => strtoupper($jawaban8),
      'answer9' => strtoupper($jawaban9),
      'answer10' => strtoupper($jawaban10),
      'answer11' => strtoupper($jawaban11),
      'answer12' => strtoupper($jawaban12)
     ]);

     // $nama_file = 'HR'.$nik.'.pdf';
     // $files->move('calon_karyawan', $nama_file);

     $isi = EmployeeUpdate::where('nik', $nik)
     ->whereNull('employee_id')
     ->select('id', 'employee_id', 'name', 'nik', 'npwp', 'gender', 'birth_place', 'birth_date', 'religion', 'mariage_status', 'address', 'current_address', 'telephone', 'handphone', 'email', 'bpjskes', 'faskes', 'bpjstk', 'f_ayah', 'f_ibu', 'f_saudara1', 'f_saudara2', 'f_saudara3', 'f_saudara4', 'f_saudara5', 'f_saudara6', 'f_saudara7', 'f_saudara8', 'f_saudara9', 'f_saudara10', 'f_saudara11', 'f_saudara12', 'm_pasangan', 'm_anak1', 'm_anak2', 'm_anak3', 'm_anak4', 'm_anak5', 'm_anak6', 'm_anak7', 'sd', 'smp', 'sma', 's1', 's2', 's3', 'emergency1', 'emergency2', 'emergency3', 'created_by')
     ->first();

     $answer = EmployeeAnswer::where('id_emp', $isi->id)
     ->select('id_emp','answer1','answer2','answer3','answer4','answer5','answer6','answer7','answer8','answer9','answer10','answer11','answer12','created_at','updated_at')
     ->first();

     $pdf = \App::make('dompdf.wrapper');
     $pdf->getDomPDF()->set_option("enable_php", true);
     $pdf->setPaper('A4', 'potrait');
     $pdf->loadView('hr_data.report', array(
      'isi' => $isi,
      'answer' => $answer
     ));
     $pdf->save(public_path() . "/calon_karyawan/HR".$nik.".pdf");

     $response = array(
      'status' => true,
      'message' => 'Calon Karyawan Berhasil Ditambahkan',
    );
     return Response::json($response);
    } catch (Exception $e) {
     $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
     return Response::json($response);
    }

  }

  // public function ReportCalonKaryawan(Request $request, $nik){
  //   $path_ttd = '/adagio/ttd/' . $detail->file;            
  //   $file_path = asset($path);
  //   $file_path_ttd = asset($path_ttd);

  //   $pdf = \App::make('dompdf.wrapper');
  //   $pdf->getDomPDF()->set_option("enable_php", true);
  //   $pdf->setPaper('A4', 'potrait');

  //   return view('auto_approve.report.report', array(
  //     'title' => 'MIRAI Approval System', 
  //     'title_jp' => 'MIRAI 承認システム',

  //     'detail' => $detail,
  //     'file_path' => $file_path,
  //     'approver' => $approver,
  //     'file_path_ttd' => $file_path_ttd
  //   ))->with('page', 'Approval File');
  // }

  public function UpdateNikBaru(Request $request)
  {
    try {
      $id = $request->get('id');
      $nik = $request->get('nik');

      $employee_update = db::table('employee_updates')->where('id', $id)->get();

      $cek = db::select('select employee_id from employee_updates where employee_id = "'.$nik.'"');

      if(count($cek) > 0){
       $response = array(
        'status' => false,
        'message' => 'NIK Sudah Terdaftar, Harap Periksa Kembali NIK Baru',
      );
      return Response::json($response);
      }else{
        $update_nik = db::table('employee_updates')->where('id', $id)->update([
          'employee_id' => $nik]);

        $gender = '';
        if ($employee_update[0]->gender == 'Male') {
          $gender = 'MAN';
        }else{
          $gender = 'WOMAN';
        }

        $insert_uniform = db::table('uniform_attendances')->insert([
          'periode' => date('Y-m-d'),
          'employee_id' => $nik,
          'name' => $employee_update[0]->name,
          'gender' => $gender,
          'size' => $employee_update[0]->seragam,
          'created_by' => '1929',
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]);

        $response = array(
          'status' => true,
        );
        return Response::json($response);
      }
    } catch (Exception $e) {
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }

  public function getCalonKaryawan( Request $request)
  {
    try {
      $nik = $request->get('nik');

      $emp = DB::SELECT("SELECT
        *
        FROM
        employee_updates
        WHERE
        nik = '".$nik."'");

      $answer = db::select("select * from employee_answers where id_emp = '".$emp[0]->id."'");

      if (count($emp) > 0) {
        $response = array(
          'status' => true,
          'message' => 'NIK Ditemukan, Silahkan Edit Data, Dan Simpan Kembali.',
          'employee' => $emp,
          'answer' => $answer
        );
        return Response::json($response);
      }else{
        $response = array(
          'status' => false,
          'message' => 'NIK Tidak Ditemukan, Silahkan Isi Data Secara Lengkap Dulu.',
          'employee' => ''
        );
        return Response::json($response);
      }
    }   
    catch (\Exception $e) {
      $response = array(
        'status' => false,
        'message' => $e->getMessage()
      );
      return Response::json($response);
    }
  }

  public function DownloadExcelCalonKaryawan(Request $request){
    try{
      $time = date('d-m-Y H;i;s');
      $dateto   = $request->get('dateto');
      $datefrom = $request->get('datefrom');


      if (($datefrom && $dateto) == null) {
       $resumes = EmployeeUpdate::select('employee_updates.id', 'employee_id', 'name', 'nik', 'npwp', 'gender', 'birth_place', 'birth_date', 'religion', 'mariage_status', 'address', 'current_address', 'telephone', 'handphone', 'email', 'bpjskes', 'faskes', 'bpjstk', 'f_ayah', 'f_ibu', 'f_saudara1', 'f_saudara2', 'f_saudara3', 'f_saudara4', 'f_saudara5', 'f_saudara6', 'f_saudara7', 'f_saudara8', 'f_saudara9', 'f_saudara10', 'f_saudara11', 'f_saudara12', 'm_pasangan', 'm_anak1', 'm_anak2', 'm_anak3', 'm_anak4', 'm_anak5', 'm_anak6', 'm_anak7', 'sd', 'smp', 'sma', 's1', 's2','s3', 'emergency1', 'emergency2', 'emergency3', 'ea.id_emp', 'ea.answer1 as answer1', 'ea.answer2 as answer2', 'ea.answer3 as answer3', 'ea.answer4 as answer4', 'ea.answer5 as answer5', 'ea.answer6 as answer6', 'ea.answer7 as answer7', 'ea.answer8 as answer8', 'ea.answer9 as answer9', 'ea.answer10 as answer10', 'ea.answer11 as answer11', 'ea.answer12 as answer12')
       ->where('employee_id', null)
       ->leftJoin(db::raw('employee_answers as ea'), 'employee_updates.id', '=', 'ea.id_emp')
       ->orderBy('employee_updates.name', 'asc')
       ->get();
     }else{
      $resumes = EmployeeUpdate::select('employee_updates.id', 'employee_id', 'name', 'nik', 'npwp', 'gender', 'birth_place', 'birth_date', 'religion', 'mariage_status', 'address', 'current_address', 'telephone', 'handphone', 'email', 'bpjskes', 'faskes', 'bpjstk', 'f_ayah', 'f_ibu', 'f_saudara1', 'f_saudara2', 'f_saudara3', 'f_saudara4', 'f_saudara5', 'f_saudara6', 'f_saudara7', 'f_saudara8', 'f_saudara9', 'f_saudara10', 'f_saudara11', 'f_saudara12', 'm_pasangan', 'm_anak1', 'm_anak2', 'm_anak3', 'm_anak4', 'm_anak5', 'm_anak6', 'm_anak7', 'sd', 'smp', 'sma', 's1', 's2','s3', 'emergency1', 'emergency2', 'emergency3', 'ea.id_emp', 'ea.answer1 as answer1', 'ea.answer2 as answer2', 'ea.answer3 as answer3', 'ea.answer4 as answer4', 'ea.answer5 as answer5', 'ea.answer6 as answer6', 'ea.answer7 as answer7', 'ea.answer8 as answer8', 'ea.answer9 as answer9', 'ea.answer10 as answer10', 'ea.answer11 as answer11', 'ea.answer12 as answer12')
      ->where('employee_id', null)
      ->whereDate('employee_updates.created_at', '>=', $datefrom)
      ->whereDate('employee_updates.created_at', '<=', $dateto)
      ->leftJoin(db::raw('employee_answers as ea'), 'employee_updates.id', '=', 'ea.id_emp')
      ->orderBy('employee_updates.name', 'asc')
      ->get();
    }

    $data = array(
      'resumes' => $resumes
    );

    ob_clean();
    Excel::create('Prospective Employee Data '.$time, function($excel) use ($data){
      $excel->sheet('HR Data', function($sheet) use ($data) {
        return $sheet->loadView('hr_data.file_excel', $data);
      });
      $excel->sheet('Answer', function($sheet2) use ($data) {
        return $sheet2->loadView('hr_data.file_hr', $data);
      });
      $excel->sheet('Address', function($sheet3) use ($data) {
        return $sheet3->loadView('hr_data.file_hr_address', $data);
      });

    })->export('xls');

      $response = array(
        'status' => true,
        'message' => 'Calon Karyawan Berhasil Ditambahkan',
      );
      return Response::json($response);
    }catch (Exception $e) {
     $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
     return Response::json($response);
   }
 }

 public function CreatePDF(Request $request){
    try{
      $nik = $request->get('nik');

      $isi = EmployeeUpdate::where('nik', $nik)
      ->whereNull('employee_id')
      ->select('id', 'employee_id', 'name', 'nik', 'npwp', 'gender', 'birth_place', 'birth_date', 'religion', 'mariage_status', 'address', 'current_address', 'telephone', 'handphone', 'email', 'bpjskes', 'faskes', 'bpjstk', 'f_ayah', 'f_ibu', 'f_saudara1', 'f_saudara2', 'f_saudara3', 'f_saudara4', 'f_saudara5', 'f_saudara6', 'f_saudara7', 'f_saudara8', 'f_saudara9', 'f_saudara10', 'f_saudara11', 'f_saudara12', 'm_pasangan', 'm_anak1', 'm_anak2', 'm_anak3', 'm_anak4', 'm_anak5', 'm_anak6', 'm_anak7', 'sd', 'smp', 'sma', 's1', 's2', 's3', 'emergency1', 'emergency2', 'emergency3', 'created_by')
      ->first();

      $answer = EmployeeAnswer::where('id_emp', $isi->id)
      ->select('id_emp','answer1','answer2','answer3','answer4','answer5','answer6','answer7','answer8','answer9','answer10','answer11','answer12','created_at','updated_at')
      ->first();

      $pdf = \App::make('dompdf.wrapper');
      $pdf->getDomPDF()->set_option("enable_php", true);
      $pdf->setPaper('A4', 'potrait');
      $pdf->loadView('hr_data.report', array(
        'isi' => $isi,
        'answer' => $answer
      ));
      $pdf->save(public_path() . "/calon_karyawan/HR".$nik.".pdf");

      $response = array(
        'status' => true,
        'message' => 'Berhasil Create PDF',
      );
      return Response::json($response);
    }catch (Exception $e) {
     $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
     return Response::json($response);
   }
 }

 public function CekDataEmployeeUpdate(Request $request){
    try{
      $value = $request->get('value');

      $data = db::table('employee_updates')->where('nik', $value)->first();

      $response = array(
        'status' => true,
        'data' => $data
      );
      return Response::json($response);
    }catch (Exception $e) {
     $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
     return Response::json($response);
   }
 }

}