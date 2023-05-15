<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use PDF;
use Excel;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\AuditAllResult;
use App\StandarisasiAuditIso;
use App\AuditExternalClaimSchedule;
use App\AuditExternalClaimPoint;
use App\AuditExternalClaim;
use App\EmployeeSync;
use App\Employee;
use App\User;
use App\LogProcess;
use App\LabelInformation;
use App\LabelEvidence;
// use Intervention\Image\ImageManagerStatic as Image;

class AuditController extends Controller
{


	public function __construct()
	{
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
			if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
			{
				die();
			}
		}      
		$this->middleware('auth');

		$this->location = ['Assembly','Accounting','Body Process','Exim','Material Process','Surface Treatment','Educational Instrument','Standardization','QA Process','Chemical Process Control','Human Resources','General Affairs','Workshop and Maintenance Molding','Production Engineering','Maintenance','Procurement','Production Control','Warehouse Material','Warehouse Finished Goods','Welding Process','Case Tanpo CL Body 3D Room', 'WWT', 'Halte dan Trotoar', 'Area Parkir Motor', 'Area Ceklog LCQ – Plating', 'Area Ceklog Buffing ', 'Area Ceklog Assy – Soldering ', 'Area Ceklog Bpro – Pianica', 'Area Lobby dan Office', 'Area Ceklog Recorder', 'Area Ceklog Key Part Process', 'Klinik', 'Area Loker Produksi','Kantin','OMI', 'Oil Storage (Barat Pianica)','Oil Storage (Barat KPP)','Flammable Storage','Security','Pump Room', 'Gym Room'];

    $this->point_sup = [
      'Jalan - Lantai - Tempat Kerja - Tembok - Atap', 
      'Kontrol Lemari Dokumen, Jig, Penyimpanan, Alat Kebersihan', 
      'Meja Kerja - Meja Office', 
      'Material, WIP', 
      'Mesin & Tools',
      'Pencegahan Kebakaran - Pencegahan Bencana - Barang Berbahaya - Barang Beracun',
      'Tempat Istirahat, Meeting Room, Lobby, Di Dalam Ruangan, Kantin',
      'Kedisiplinan'
    ];

    $this->point_1 = [
      'Pada koridor umum, lebar koridor dipastikan lebih dari 80cm dan garis pembatas tidak ada yang rusak atau terkelupas', 
      'Pastikan selalu melakukan pengecekan di dalam ruangan apakah ada sarang laba-laba, yogore atau debu yang tersisa', 
      'Barang yang tidak diperlukan tidak ada di tiang dan sekitar tembok. Diberi label pembagian tempat, label nama yang ditempatkan, label PIC kontrol', 
      'Sebagai prinsipnya dilarang menempel informasi selain di papan informasi. Selain itu ditetapkan label PIC untuk papan informasi. Papan informasi di letakkan  lurus, sejajar , siku siku dan diberi 2 stopper atau lebih agar posisinya fix', 
      'Permukaan lantai tidak ada yang rusak, selalu dibersihkan dan tidak ada yang melebihi garis pembatas sampai ke koridor/jalan. Maintenance jika ada kerusakan cat koridor dll',
      '',
      '',
      'Untuk menempel informasi di papan informasi selain IK dll  menggunakan kertas "seminimum mungkin", lalu dimasukkan ke hardcase.  di tempel lurus, sejajar , siku siku dan diberi 2 stopper atau lebih agar posisinya fix '
    ];
    
    $this->point_2 = [
      'Tidak menaruh barang di bagian atas almari/rak', 
      'Dokumen yang disimpan dipilah(seiri) dan dirapikan(seiton), pastikan saat filling terpasang insert paper di punggung file.', 
      'Pintu lemari, pintu lemari jig, pintu lemari penyimpanan, pintu lemari alat kebersihan semua harus ditutup. Pintu yang rusak di repair. Dilakukan maintenance agar pintu bisa dibuka tutup dengan lancar', 
      'Pada lemari dokumen,lemari jig, lemari alat kebersihan dan lemari yang terkunci saja diperjelas dan ditetapkan PIC kontrolnya ,dilakukan pengontrolan agar tidak membawa  dan memindahkan kunci tanpa ijin ke tempat lain.', 
      'Memberi label PIC 5S lemari, lemari jig, lemari penyimpanan, lemari alat kebersihan ', 
      'Peletakkan lemari, lemari jig, lemari penyimpanan, lemari alat kebersihan di letakkan  lurus, sejajar dan siku-siku.', 
      'Harus menggantungkan sapu, pengepel, cikrak dll. Bila ember diletakkan di dalam lemari harus diberi batas dengan jelas dan papan nama.', 
      'Melakukan seiton dan semua diberi label untuk lemari buku, lemari jig dan lemari penyimpanan  agar barang yang diperlukan bisa langsung diambil tanpa perlu mencari-cari.'
    ];

    $this->point_3 = [
      'Melakukan SEIRI barang di atas meja kerja dan meja office,dijaga kebersihannya, kursi juga ditempatkan di tempat yang telah ditentukan', 
      'Dokumen yang digunakan tidak hanya disimpan tapi juga harus disusun secara jelas dengan menggunakan clip, tray, clear file agar bisa dibedakan dan agar bisa disimpan dengan lurus dan sejajar selama bekerja (termasuk ketika meninggalkan tempat). jangan menyimpan dokumen di atas meja', 
      'Saat pulang diatas meja kerja, hanya diletakkan benda benda yang sedang dikerjakan. Semua tools dikembalikan ke sugata oki. Sugata oki harus tepat tidak boleh lebih atau kurang saat dikembalikan', 
      'Diatas meja kerja sebisa mungkin hanya diletakkan dokumen yang dibutuhkan saja dan agar mudah diambil diberi sugata oki untuk tools. Prinsip pada saat bekerja adalah setiap setelah menggunakan tools langsung dikembalikan ke tempat semula.', 
      'Untuk semua meja kerja dan meja office diberi label PIC 5S yang telah ditentukan', 
      'Barang pribadi di letakkan di loker yang sudah ditetapkan ( tas, barang bawaan, pakaian), jangan diletakkan di sekitar meja kerja.', 
      'Dokumen yang disimpan meja kerja dan meja office disimpan dipilah(seiri) dan dirapikan(seiton), pastikan saat filling terpasang inset paper di punggung file.',
      'Meja kerja dan isi di laci di meja kerja adalah benda benda yang dibutuhkan untuk bekerja saja. Tools di meja kerja diberi sugata oki dan di laci hanya benda benda yang dibutuhkan untuk kerja.  dipilah(seiri) dan dirapikan(seiton) harus selalu dilakukan.'
    ];

    $this->point_4 = [
      'Perhatikan ketinggian saat menumpuk barang, ditumpuk sesuai dengan jangkauan tangan pekerja. Jangan ada yang miring atau keluar batas pada saat menumpuk.', 
      'Tempat peletakan returnable box dan pallet yang kosong di tetapkan PIC dan diberi label keduanya  ', 
      'Tempat menaruh barang bergerak/berpindah-pindah seperti daisha, dsb ditentukan dan diberi label. Untuk barang yang tidak ditentukan tempatnya, diberi label Temporary place/tempat menaruh sementara diperjelas, ditentukan  PIC  dan batas waktu/sampai kapan penempatannya', 
      'Penerapan langsung 3T dan pemberian label  (Tetap posisi・・menentukan tempat peletakan barang, Tetap jumlah・・menentukan jumlah yang diletakkan, Tetap barang・・menentukan barang yang diletakkan).', 
      'Di tempat penyimpanan material KD diberi visual control/ label jumlah material existing, MAX material, MIN material, kapan harus order dll. Selain itu harus mematuhi jumlah tersebut ', 
      'Tidak ada kerusakan pada palet dan returnable box yang dapat merusak produk, dan terjaga dengan baik dan bersih. sebagai prinsipnya selain yang tidak ada label nama perusahaan atau departemen yang bersangkutan tidak boleh dibawa.', 
      '',
      ''
    ];

    $this->point_5 = [
      'Ketika meninggalkan tempat duduk, pastikan kondisi display laptop/PC dan lampu dalam keadaan OFF', 
      'Keyboard dan monitor PC dirawat dengan baik agar tidak ada debu atau sidik jari yang tertinggal', 
      'Panjang dan jenis kabel tools OA sesuai & tidak berdebu. Disekitar stop kontak tidak ada debu.prinsip OA tools di letakkan  lurus, sejajar dan siku-siku.', 
      'Telepon diberi label nomor, kabel telepon tidak melilit, selalu terawat dan dalam kondisi bersih.  Letak telepon pada dasarnya diletakkan urus, sejajar dan siku-siku.', 
      'Seluruh mesin dan fasilitas, dibersihkan sampai ke ujung/sudut-sudutnya, kontrol oiling, daily maintenance. Dan ditampilkan record yang paling baru. (jangan ada sarang laba-laba atau debu yang terlihat)', 
      'Pipa dan kabel diatur agar panjangnya secukupnya tidak diletakkan di lantai. dipatenkan(dibuat fix) agar tidak membuat tersandung. Sebagai prinsipnya di letakkan  lurus, sejajar dan siku-siku.', 
      'Memberi tanda di sekitar mesin dan di mesin itu sendiri agar mudah dimengerti saat mesin sedang beroperasi seperti lampu atau kalimat pengumuman. untuk gas,air, air RO listrik dll diberi tanda arah aliran nya, lalu untuk benda yang berputar diberi label arah putaran dan sisa putaran.',
      'Seluruh mesin dan fasilitas diberi label PIC. Lalu, kunci di posisi yang benar (buka tutup, ON/OFF dll) dilepas dari mesin dan disimpan ditempat yang sudah ditentukan.'
    ];

    $this->point_6 = [
      'Lorong evakuasi terjaga dengan baik, tanda penunjuk dijaga agar tetap jelas.', 
      'Agar bisa segera diambil dari tempatnya (hydrant, APAR, tandu),usahakan tidak ada barang lain yang menghalangi. ', 
      'Lokasi alat pemadam dan tandu,dll diberi tanda agar bisa ditemukan walaupun dari kejauhan.', 
      'Benda beracun dan berbahaya disimpan di gudang yang ditetapkan dan diberi label nama dan quantity dan ada buku besar keluar masuknya barang, serta kunci gudangnya dikontrol.'
    ];

    $this->point_7 = [
      'Ditempatkan dispenser atau tempat minum , ditunjuk PIC nya untuk menjaga kebersihannya. Lalu, pastikan tidak ada bekas ciprtatan air di pantry, wastafel dan toilet agar terjaga keindahan nya. ', 
      'Tempat istirahat dan loker harus dijaga keindahan nya. Dilarang membawa barang barang yang tidak diperlukan. Isi loker harus dijaga jangan memasukkan barang yang tidak digunakan serta  dijaga kebersihan nya agar tetep indah . sudah menjadi tugas kita untuk menyimpan dan menjaga kebersihan barang barang perusahaan di loker', 
      'Penetapan penanggung jawab kontrol suhu AC (kontrol suhu artinya bukan untuk setting suhu, tetapi suhu aktual dalam ruangan). Meletakkan termometer di beberapa titik. Peletakan hydrometer dengan tepat.suhu dikontrol jangan sampai kurang dari 28 derajat celcius', 
      'Menentukan seluruh PIC jendela untuk dijiaga keindahan dan kebersihan nya sampai seperti tidak ada sidik jari yang menempel. Jangan menempel informasi di kaca. Lalu lemari atau fasilitas jangan menghalangi kaca ', 
      'Jam selalu menunjukkan waktu yang tepat', 
      'Menjaga kebersihan setelah memakai smoking room, buang sampah pada tempatnya, rapihkan kursi kembali setelah dipakai.', 
      '', 
      '', 
    ];

    $this->point_8 = [
      'Memakai name tag di dada sebelah kiri agar dapat dibaca dengan baik oleh orang lain.selalu memakai "seragam yang tepat" (untuk pria tutup kancing sampai atas . Untuk wanita resleting dari bawah sampai atas ), potong kuku, kaos dalam jangan keluar dari seragam, celana panjang, sepatu dll)', 
      'mengawali hari kerja dengan perasaan yang bersemangat. Harus mengucapkan salam di pagi, isang & sore hari di area kerja, serta senam harus dilakukan diluar ruangan dengan semangat dan tidak berbincang - bincang ', 
      'Tidak memasukkan tangan di saku pada saat berjalan, jangan berlarian di pabrik pada saat bekerja, pindah lokasi, istirahat siang. Pada saat menuruni tangga harus memegang pegangan tangga.', 
      'Pada tempat penyimpanan alat komunikasi seperti HP diberi label PIC, dan di  simpan. Space tempat penyimpanan yang kosong diberi label alasan "cuti:, "shift 2", "tidak digunakan"', 
      'Memastikan sekali lagi isi 5S dan 3 Tei (3 ketetapan), dilakukan training dan seluruhnya harus dihafal', 
      'Setiap hari pada saat pointing call hafal Filosofi Yamaha, Aturan K3 Yamaha, 6 Pasal Keselamatan Lalu Lintas Yamaha, dan 10 Komitmen Berkendara, dll', 
      'Pada saat bel pertama cepat segera kembali ke tempat kerja, bersamaan dengan bunyi bel langsung segera bekerja kembali',
      'Memahami dan mengaplikasikan untuk terus membersihkan ketika menyadari bahwa ada yang kotor di meja kerja, equipment, peralatan, kotak obat P3K, lemari, bola lampu, jendela, sampah yang terjatuh, dll. '
    ];

  }

  function compress($source_image, $compress_image)
  {
    $image_info = getimagesize($source_image);
    if ($image_info['mime'] == 'image/jpeg') {
      $source_image = imagecreatefromjpeg($source_image);
          imagejpeg($source_image, $compress_image, 20);             //for jpeg or gif, it should be 0-100
        } elseif ($image_info['mime'] == 'image/png') {
          $source_image = imagecreatefrompng($source_image);
          imagepng($source_image, $compress_image, 3);
        }
        return $compress_image;
      }

      public function index()
      {
        $title = "YMPI Internal Patrol";
        $title_jp = "内部パトロール";

        return view('audit.index_patrol', array(
          'title' => $title,
          'title_jp' => $title_jp
        ))->with('page', 'YMPI Patrol'); 
      }

      public function index_audit()
      {
        $title = "YMPI Internal Audit";
        $title_jp = "内部監査";

        return view('audit.index_audit', array(
          'title' => $title,
          'title_jp' => $title_jp
        ))->with('page', 'YMPI Patrol'); 
      }

      public function index_patrol()
      {
        $title = "5S Patrol GM & Presdir";
        $title_jp = "社長、部長の5Sパトロール";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
         where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.patrol', array(
         'title' => $title,
         'title_jp' => $title_jp,
         'employee' => $emp,
         'auditee' => $auditee,
         'location' => $this->location,
         'poin' => $this->point_sup,
         'point_1' => $this->point_1,
         'point_2' => $this->point_2,
         'point_3' => $this->point_3,
         'point_4' => $this->point_4,
         'point_5' => $this->point_5,
         'point_6' => $this->point_6,
         'point_7' => $this->point_7,
         'point_8' => $this->point_8
       ))->with('page', 'Audit Patrol');
      }

      public function index_patrol_daily()
      {
        $title = "Patrol Daily Shift 1 & 2";
        $title_jp = "1・2直パトロール";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.patrol_daily', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee,
          'location' => $this->location
        ))->with('page', 'Patrol Daily');
      }

      public function index_patrol_covid()
      {
        $title = "Patrol Covid";
        $title_jp = "コロナ対策パトロール";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.patrol_covid', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee,
          'location' => $this->location
        ))->with('page', 'Patrol Covid');
      }

      public function index_patrol_outside()
      {
        $title = "YMPI Outside Factory Patrol";
        $title_jp = "YMPI工場外側のパトロール";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.patrol_outside', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee
        ))->with('page', 'Outside Factory Patrol');
      }

      public function index_patrol_energy()
      {
        $title = "Patrol Penghematan Energy";
        $title_jp = "";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.patrol_energy', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee,
          'location' => $this->location
        ))->with('page', 'Patrol Energy');
      }

      public function index_patrol_washing()
      {
        $title = "Patrol Washing Treatment";
        $title_jp = "";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.patrol_washing', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee,
          'location' => $this->location
        ))->with('page', 'Patrol Washing');
      }

      public function index_patrol_hrga()
      {
        $title = "Patrol HR-GA";
        $title_jp = "";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.patrol_hrga', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee,
          'location' => $this->location
        ))->with('page', 'Patrol HR-GA');
      }


      public function index_patrol_vendor()
      {
        $title = "Patrol Vendor";
        $title_jp = "";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.patrol_vendor', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee
        ))->with('page', 'Patrol Vendor');
      }

      public function fetch_patrol(Request $request){
        $data_all = db::select("
         SELECT
         kategori,
         sum( CASE WHEN status_ditangani IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
         sum( CASE WHEN status_ditangani = 'progress' THEN 1 ELSE 0 END ) AS jumlah_progress,
         sum( CASE WHEN status_ditangani = 'close' THEN 1 ELSE 0 END ) AS jumlah_sudah
         FROM
         audit_all_results 
         WHERE jenis = 'Patrol'
         and (remark <> 'Positive Finding' OR remark is null)
         GROUP BY
         kategori
         ORDER BY jumlah_belum ASC
         ");

        $data_type_all = db::select("
          SELECT
          point_judul,
          sum( CASE WHEN status_ditangani IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
          sum( CASE WHEN status_ditangani = 'progress' THEN 1 ELSE 0 END ) AS jumlah_progress,
          sum( CASE WHEN status_ditangani = 'close' THEN 1 ELSE 0 END ) AS jumlah_sudah
          FROM
          audit_all_results 
          WHERE point_judul is not null
          and jenis = 'Patrol'
          and point_judul <> 'Negative Finding'
          and point_judul <> 'Positive Finding'
          GROUP BY
          point_judul
          ORDER BY point_judul ASC
          ");

        $response = array(
          'status' => true,
          'data_all' => $data_all,
          'data_type_all' => $data_type_all
        );

        return Response::json($response);
      }

      public function index_mis()
      {
        $title = "Audit MIS";
        $title_jp = "MIS監査";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.patrol_mis', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee,
          'location' => $this->location
        ))->with('page', 'Audit Patrol MIS');
      }

      public function index_std()
      {
        $title = "EHS & 5S Monthly Patrol";
        $title_jp = "EHS・5S月次パトロール";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.patrol_std', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee,
          'location' => $this->location
        ))->with('page', 'EHS dan 5S Bulanan');
      }

      public function index_audit_stocktaking()
      {
        $title = "Audit Stocktaking";
        $title_jp = "棚卸監査";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%' or position like '%Leader%')");

        return view('audit.audit_stocktaking', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee,
          'location' => $this->location
        ))->with('page', 'Audit Stocktaking');
      }

      public function index_audit_mis()
      {
        $title = "Audit MIS";
        $title_jp = "MIS監査";

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
        ->select('employee_id', 'name', 'position', 'department')->first();

        $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
          where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

        return view('audit.audit_mis', array(
          'title' => $title,
          'title_jp' => $title_jp,
          'employee' => $emp,
          'auditee' => $auditee,
          'location' => $this->location
        ))->with('page', 'Audit MIS');
      }


      public function post_audit(Request $request)
      {
        $audit = $request->get("audit");
        $datas = [];

        for ($i=0; $i < count($request->get('patrol_lokasi')); $i++) { 
         $patrol = new AuditAllResult;
         $patrol->tanggal = date('Y-m-d');
         $patrol->jenis = 'Patrol';
         $patrol->kategori = $request->get('category');
         $patrol->auditor_id = $request->get('auditor_id') ;
         $patrol->auditor_name = $request->get('auditor_name');
         $patrol->lokasi = $request->get('patrol_lokasi')[$i];
         $patrol->auditee_name = $request->get('patrol_pic')[$i];
         $patrol->point_judul = $request->get('patrol_detail')[$i];
         $patrol->note = $request->get('note')[$i];
         $patrol->created_by = Auth::id();
         $patrol->save();
       }

       $response = array(
         'status' => true,
       );
       return Response::json($response);
     }



     public function post_audit_file(Request $request)
     {
      try {
       $id_user = Auth::id();
       $tujuan_upload = 'files/patrol';

       $poin_sup = "";
       $detail_poin_sup = "";

       if ($request->input('poin_fix') == "" || $request->input('poin_fix') == null || $request->input('poin_fix') == "#0") {
        $poin_sup = null;
      }
      else{
        $poin_sup = $request->input('poin_fix');
      }

      if ($request->input('isi_poin_fix') == "" || $request->input('isi_poin_fix') == null || $request->input('isi_poin_fix') == "#1") {
        $detail_poin_sup = null;
      }
      else{
        $detail_poin_sup = $request->input('isi_poin_fix');
      }

      if ($request->input('chemical') == 'chemical') {
        for ($i = 1; $i <= 6; $i++) {
          if ($request->file('chemical_file_'.$i) != null) {
            $file_chemical = $request->file('chemical_file_'.$i);
            $nama_chemical = $file_chemical->getClientOriginalName();
            $filename_chemical = pathinfo($nama_chemical, PATHINFO_FILENAME);
            $chemical_extension = pathinfo($nama_chemical, PATHINFO_EXTENSION);
            $filename_chemical = md5($filename_chemical.date('YmdHisa')).'.'.$chemical_extension;
            $file_chemical->move($tujuan_upload,$filename_chemical);

            $dt = date('Y-m-d');

            $audit_all = AuditAllResult::create([
              'jenis' => 'Patrol',
              'tanggal' => $dt,
              'kategori' => $request->input('category'),
              'lokasi' => $request->input('chemical_location_'.$i),
              'auditor_id' => $request->input('auditor_id'),
              'auditor_name' => $request->input('auditor_name'),
              'auditee_name' => $request->input('chemical_patrol_pic_'.$i),
              'point_judul' => $request->input('chemical_patrol_detail_'.$i),
              'note' => $request->input('chemical_patrol_note_'.$i),
              'foto' => $filename_chemical,
              'remark' => 'chemical',
              'created_by' => $id_user
            ]);

            // $id = $audit_all->id;

            // $mails = "select distinct email from users where name = '".$request->input('chemical_patrol_pic_'.$i)."'";
            // $mailtoo = DB::select($mails);

            // $isimail = "select * from audit_all_results where id = ".$id;

            // $auditdata = db::select($isimail);

            // Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));
          }
        }
      }
      else {
        $dt = date('Y-m-d');
        if ($request->input('category') == 'Patrol Bangunan') {
          $dt = $request->input('date');
        }

        for ($i=0; $i < $request->input('jumlah'); $i++) { 
          $file = $request->file('file_datas_'.$i);
          $nama = $file->getClientOriginalName();
          $filename = pathinfo($nama, PATHINFO_FILENAME);
          $extension = pathinfo($nama, PATHINFO_EXTENSION);
          $filename = md5($filename.date('YmdHisa')).'.'.$extension;
          $file->move($tujuan_upload,$filename);

      // $width = 600; // max width
      // $height = 600; // max height

      // $image_resize = Image::make($file->getRealPath());   
      // $image_resize->height() > $image_resize->width() ? $width=null : $height=null;
      // $image_resize->resize($width, $height, function ($constraint) {
      //     $constraint->aspectRatio();
      // });
      // $image_resize->save(public_path('files/patrol/' .$filename));

      // $directory_name = public_path('/files/patrol/');
      // $names = $directory_name . $filename;

      // $compress_file = "compress_" . $filename;
      // $compressed_img = $directory_name . $compress_file;
      // $compress_image = $this->compress($names, $compressed_img);
      // unlink($names);

          if ($request->input('category') == 'Patrol Vendor') {
           $audit_all = AuditAllResult::create([
            'jenis' => 'Patrol',
            'tanggal' => $dt,
            'kategori' => $request->input('category'),
            'lokasi' => $request->input('location'),
            'auditor_id' => $request->input('auditor_id'),
            'auditor_name' => $request->input('auditor_name'),
            'auditee_name' => $request->input('patrol_pic_'.$i),
            'point_judul' => $request->input('patrol_detail_'.$i),
            'auditee' => $request->input('patrol_vendor_'.$i),
            'note' => $request->input('note_'.$i),
            'tanggal_target' => $request->input('due_date_target'),
            'poin_sup' => $poin_sup,
            'detail_poin_sup' => $detail_poin_sup,
            'foto' => $filename,
            'remark' => $request->input('patrol_type_'.$i),
            'created_by' => $id_user
          ]);
         }else{
           $audit_all = AuditAllResult::create([
            'jenis' => 'Patrol',
            'tanggal' => $dt,
            'kategori' => $request->input('category'),
            'lokasi' => $request->input('location'),
            'auditor_id' => $request->input('auditor_id'),
            'auditor_name' => $request->input('auditor_name'),
            'auditee_name' => $request->input('patrol_pic_'.$i),
            'point_judul' => $request->input('patrol_detail_'.$i),
            'note' => $request->input('note_'.$i),
            'poin_sup' => $poin_sup,
            'detail_poin_sup' => $detail_poin_sup,
            'foto' => $filename,
            'remark' => $request->input('patrol_type_'.$i),
            'created_by' => $id_user
          ]);
         }



         $id = $audit_all->id;



          // $mails = "select distinct email from users where name = '".$request->input('patrol_pic_'.$i)."'";
          // $mailtoo = DB::select($mails);

          // $isimail = "select * from audit_all_results where id = ".$id;

          // $auditdata = db::select($isimail);

          // if ($request->input('category') == "Patrol Daily" || $request->input('category') == "Patrol Covid") {
          //   $mailscc = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where section = 'Secretary Admin Section' and employee_id <> 'PI9704001'";  
          //   $mailtoocc = DB::select($mailscc);

          //   if ($request->input('patrol_type_'.$i) != "Positive Finding") {
          //     Mail::to($mailtoo)->cc($mailtoocc)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));
          //   } else{

          //   }
          // } 
          // else{
          // }
            // Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));

       }
     }

     $response = array(
      'status' => true,
    );
     return Response::json($response);
   } 

   catch (\Exception $e) {
     $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
     return Response::json($response);
   }
 }

 public function post_audit_stocktaking(Request $request)
 {
  try {
    $id_user = Auth::id();
    $tujuan_upload = 'files/patrol';

    for ($i=0; $i < $request->input('jumlah'); $i++) { 

      $file = $request->file('file_datas_'.$i);
      $nama = $file->getClientOriginalName();

      $filename = pathinfo($nama, PATHINFO_FILENAME);
      $extension = pathinfo($nama, PATHINFO_EXTENSION);

      $filename = md5($filename.date('YmdHisa')).'.'.$extension;

      $file->move($tujuan_upload,$filename);

      $audit_all = AuditAllResult::create([
        'jenis' => 'Audit',
        'tanggal' => date('Y-m-d'),
        'kategori' => $request->input('category'),
        'lokasi' => $request->input('location'),
        'auditor_id' => $request->input('auditor_id'),
        'auditor_name' => $request->input('auditor_name'),
        'auditee_name' => $request->input('patrol_pic_'.$i),
        'point_judul' => $request->input('patrol_detail_'.$i),
        'note' => $request->input('note_'.$i),
        'foto' => $filename,
        'created_by' => $id_user
      ]);

        // $id = $audit_all->id;

        // $mails = "select distinct email from users where name = '".$request->input('patrol_pic_'.$i)."'";
        // $mailtoo = DB::select($mails);

        // $isimail = "select * from audit_all_results where id = ".$id;

        // $auditdata = db::select($isimail);

        // Mail::to($mailtoo)->bcc(['rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'patrol'));
    }

    $response = array(
      'status' => true,
    );
    return Response::json($response);
  } 

  catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}


public function fetch_audit(Request $request)
{
  try {

   $kategori = $request->get("category");

   $query = 'SELECT * FROM standarisasi_audit_checklists where point_question is not null and deleted_at is null and kategori = "'.$kategori.'" order by id asc';
   $detail = db::select($query);

   $response = array(
    'status' => true,
    'lists' => $detail
  );

   return Response::json($response);

 } catch (\Exception $e) {
   $response = array(
    'status' => false,
    'message'=> $e->getMessage()
  );

   return Response::json($response); 
 }
}


public function indexMonitoring(){

  $location = AuditAllResult::whereNull('deleted_at')
  ->select('lokasi')
  ->where('kategori','S-Up And EHS Patrol Presdir')
  ->Orwhere('kategori','5S Patrol GM')
  ->distinct()
  ->get();

  return view('audit.patrol_monitoring',  
   array(
     'title' => 'Patrol Monitoring', 
     'title_jp' => 'パトロール監視',
     'location' => $location
   )
 )->with('page', 'Audit Patrol');
}

public function fetchMonitoring(Request $request){

  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $first = date("Y-m-d", strtotime('-30 days'));
  $location = "";

  $last = AuditAllResult::whereIn('status_ditangani',['progress',null])
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(tanggal) as tanggal'))
  ->where('kategori','S-Up And EHS Patrol Presdir')
  // ->Orwhere('kategori','5S Patrol GM')
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
  }else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
      }
    }
  }

  if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  }

  if(strlen($request->get('location')) > 0){
    $location = 'and lokasi = "'.$request->get("location").'"';
  }

  $data = db::select("SELECT
    date_format(tanggal, '%a, %d %b %Y') AS tanggal,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_belum_gm,
    sum( CASE WHEN status_ditangani = 'progress' AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_progress_gm,
    sum( CASE WHEN status_ditangani = 'close' AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_sudah_gm,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_belum_presdir,
    sum( CASE WHEN status_ditangani = 'progress' AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_progress_presdir, 
    sum( CASE WHEN status_ditangani = 'close' AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_sudah_presdir 
    FROM
    audit_all_results 
    WHERE
    tanggal >= '".$datefrom."' and tanggal <= '".$dateto."'
    and kategori in ('S-Up And EHS Patrol Presdir','5S Patrol GM')
    GROUP BY
    tanggal");


  $data_kategori = db::select("
    SELECT
    MONTHNAME(tanggal) as bulan,
    year(tanggal) as tahun,
    sum( CASE WHEN point_judul_sub = 'Seiri' THEN 1 ELSE 0 END) AS Seiri,
    sum( CASE WHEN point_judul_sub = 'Seiton' THEN 1 ELSE 0 END) AS Seiton,
    sum( CASE WHEN point_judul_sub = 'Seiso' THEN 1 ELSE 0 END) AS Seiso,
    sum( CASE WHEN point_judul_sub = 'Safety' THEN 1 ELSE 0 END) AS Safety,
    sum( CASE WHEN point_judul_sub = 'Environment' THEN 1 ELSE 0 END) AS Environment,
    sum( CASE WHEN point_judul_sub = 'Health' THEN 1 ELSE 0 END) AS Health
    FROM
    audit_all_results 
    WHERE
    kategori IN ( 'S-Up And EHS Patrol Presdir', '5S Patrol GM' ) 
    and point_judul_sub is not null
    GROUP BY
    tahun,monthname(tanggal)
    order by tahun, month(tanggal) ASC
    ");

  // $data_kategori = db::select("
  //   SELECT
  //   kategori,
  //   sum( CASE WHEN status_ditangani IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
  //   sum( CASE WHEN status_ditangani = 'progress' THEN 1 ELSE 0 END ) AS jumlah_progress,
  //   sum( CASE WHEN status_ditangani = 'close' THEN 1 ELSE 0 END ) AS jumlah_sudah
  //   FROM
  //   audit_all_results 
  //   WHERE
  //   kategori IN ( 'S-Up And EHS Patrol Presdir', '5S Patrol GM' ) 
  //   GROUP BY
  //   kategori");

  $data_bulan = db::select("
    SELECT
    MONTHNAME(tanggal) as bulan,
    year(tanggal) as tahun,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_belum_gm,
    sum( CASE WHEN status_ditangani = 'progress' AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_progress_gm,
    sum( CASE WHEN status_ditangani = 'close' AND kategori = '5S Patrol GM' THEN 1 ELSE 0 END ) AS jumlah_sudah_gm,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_belum_presdir,
    sum( CASE WHEN status_ditangani = 'progress' AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_progress_presdir, 
    sum( CASE WHEN status_ditangani = 'close' AND kategori = 'S-Up And EHS Patrol Presdir' THEN 1 ELSE 0 END ) AS jumlah_sudah_presdir
    FROM
    audit_all_results 
    WHERE
    kategori in ('S-Up And EHS Patrol Presdir','5S Patrol GM')
    ".$location." 
    GROUP BY tahun,monthname(tanggal)
    order by tahun, month(tanggal) ASC"
  );

  $year = date('Y');

  $response = array(
    'status' => true,
    'datas' => $data,
    'data_kategori' => $data_kategori,
    'data_bulan' => $data_bulan,
    'year' => $year
  );

  return Response::json($response);
}

public function detailMonitoring(Request $request){

  $tgl = date('Y-m-d', strtotime($request->get("tgl")));

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
  }

  if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  }

  $status = $request->get('status');

  if ($status != null) {

    if ($status == "Temuan GM Open") {
      $stat = 'and audit_all_results.status_ditangani is null and kategori = "5S Patrol GM"';
    }
    else if ($status == "Temuan Presdir Open"){
      $stat = 'and audit_all_results.status_ditangani is null and kategori = "S-Up And EHS Patrol Presdir"';
    }
    if ($status == "Temuan GM Progress") {
      $stat = 'and audit_all_results.status_ditangani = "progress" and kategori = "5S Patrol GM"';
    }
    else if ($status == "Temuan Presdir Progress"){
      $stat = 'and audit_all_results.status_ditangani = "progress" and kategori = "S-Up And EHS Patrol Presdir"';
    }
    else if ($status == "Temuan GM Close") {
      $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "5S Patrol GM"';
    }
    else if ($status == "Temuan Presdir Close") {
      $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "S-Up And EHS Patrol Presdir"';
    }
  } else{
    $stat = '';
  }

  $datefrom = $request->get('datefrom');
  $dateto = $request->get('dateto');

  if ($datefrom != null && $dateto != null) {
    $df = 'and audit_all_results.tanggal between "'.$datefrom.'" and "'.$dateto.'"';
  }else{
    $df = '';
  }

  $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and tanggal = '".$tgl."' ".$stat." and (remark <> 'Positive Finding' OR remark is null)";

  $detail = db::select($query);

  return DataTables::of($detail)

  ->editColumn('auditor_name', function($detail){
    $kategori = '';

    if($detail->kategori == "S-Up And EHS Patrol Presdir"){
      $kategori = "Presdir";
    }else if ($detail->kategori == "5S Patrol GM"){
      $kategori = "GM";
    }else{
      $kategori = $detail->kategori;
    }

    $tgl = date('d-M-Y', strtotime($detail->tanggal));

    return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
  })


  ->editColumn('foto', function($detail){
    return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
  })

  ->editColumn('auditee_name', function($detail){
    return $detail->point_judul.'<br>'.$detail->auditee_name;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
  ->make(true);
}


public function detailMonitoringCategory(Request $request){

  $kategori = $request->get('kategori');
  $status = $request->get('status');

  if ($status != null) {

    if ($status == "Temuan Belum Ditangani") {
      $stat = 'and audit_all_results.status_ditangani is null';
    }
    else if ($status == "Temuan Progress"){
      $stat = 'and audit_all_results.status_ditangani = "progress"';
    }
    else if ($status == "Temuan Sudah Ditangani"){
      $stat = 'and audit_all_results.status_ditangani = "close"';
    }

  } else{
    $stat = '';
  }

  if ($kategori == "EHS 5S Monthly Patrol") {
    $kategori = "EHS & 5S Patrol";
  }

  $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and kategori = '".$kategori."' ".$stat." and (remark <> 'Positive Finding' OR remark is null)";

  $detail = db::select($query);

  return DataTables::of($detail)

  ->editColumn('auditor_name', function($detail){
    $kategori = '';

    if($detail->kategori == "S-Up And EHS Patrol Presdir"){
      $kategori = "Presdir";
    }else if ($detail->kategori == "5S Patrol GM"){
      $kategori = "GM";
    }else{
      $kategori = $detail->kategori;
    }

    $tgl = date('d-M-Y', strtotime($detail->tanggal));

    return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
  })


  ->editColumn('foto', function($detail){
    return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
  })

  ->editColumn('auditee_name', function($detail){
    return $detail->point_judul.'<br>'.$detail->auditee_name;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
  ->make(true);
}

public function detailMonitoringBulan(Request $request){

  $bulan = $request->get('bulan');
  $status = $request->get('status');

  if ($status != null) {

    if ($status == "Temuan GM Open") {
      $stat = 'and audit_all_results.status_ditangani is null and kategori = "5S Patrol GM"';
    }
    else if ($status == "Temuan Presdir Open"){
      $stat = 'and audit_all_results.status_ditangani is null and kategori = "S-Up And EHS Patrol Presdir"';
    }
    if ($status == "Temuan GM Progress") {
      $stat = 'and audit_all_results.status_ditangani = "progress" and kategori = "5S Patrol GM"';
    }
    else if ($status == "Temuan Presdir Progress"){
      $stat = 'and audit_all_results.status_ditangani = "progress" and kategori = "S-Up And EHS Patrol Presdir"';
    }
    else if ($status == "Temuan GM Close") {
      $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "5S Patrol GM"';
    }
    else if ($status == "Temuan Presdir Close") {
      $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "S-Up And EHS Patrol Presdir"';
    }

  } else{
    $stat = '';
  }

  $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and monthname(tanggal) = '".$bulan."' ".$stat." and (remark <> 'Positive Finding' OR remark is null)";

  $detail = db::select($query);

  return DataTables::of($detail)

  ->editColumn('auditor_name', function($detail){
    $kategori = '';

    if($detail->kategori == "S-Up And EHS Patrol Presdir"){
      $kategori = "Presdir";
    }else if ($detail->kategori == "5S Patrol GM"){
      $kategori = "GM";
    }else{
      $kategori = $detail->kategori;
    }

    $tgl = date('d-M-Y', strtotime($detail->tanggal));

    return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
  })


  ->editColumn('foto', function($detail){
    return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
  })

  ->editColumn('auditee_name', function($detail){
    return $detail->point_judul.'<br>'.$detail->auditee_name;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
  ->make(true);
}

public function detailMonitoringType(Request $request){

  $type = $request->get('type');
  $status = $request->get('status');

  if ($status != null) {
    if ($status == "Temuan Belum Ditangani") {
      $stat = 'and audit_all_results.status_ditangani is null';
    }
    else if ($status == "Temuan Progress"){
      $stat = 'and audit_all_results.status_ditangani = "progress"';
    }
    else if ($status == "Temuan Sudah Ditangani"){
      $stat = 'and audit_all_results.status_ditangani = "close"';
    }

  } else{
    $stat = '';
  }

  $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and point_judul = '".$type."' ".$stat." and (remark <> 'Positive Finding' OR remark is null)";

  $detail = db::select($query);

  return DataTables::of($detail)

  ->editColumn('auditor_name', function($detail){
    $kategori = '';

    if($detail->kategori == "S-Up And EHS Patrol Presdir"){
      $kategori = "Presdir";
    }else if ($detail->kategori == "5S Patrol GM"){
      $kategori = "GM";
    }else{
      $kategori = $detail->kategori;
    }

    $tgl = date('d-M-Y', strtotime($detail->tanggal));

    return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
  })


  ->editColumn('foto', function($detail){
    return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
  })

  ->editColumn('auditee_name', function($detail){
    return $detail->point_judul.'<br>'.$detail->auditee_name;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
  ->make(true);
}

public function fetchtable_audit(Request $request)
{

  $datefrom = date("Y-m-d",  strtotime('-60 days'));
  $dateto = date("Y-m-d");

  $last = AuditAllResult::whereIn('status_ditangani',['progress',null])
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(tanggal) as audit_date'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
  }else{
    if($last){
      $tanggal = date_create($last->audit_date);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->audit_date));
      }
    }
  }


  if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  }

  $status = $request->get('status');

  if ($status != null) {
    $cat = json_encode($status);
    $kat = str_replace(array("[","]"),array("(",")"),$cat);

    $kate = 'and audit_all_results.status_ditangani in'.$kat;
  }else{
    $kate = 'and (audit_all_results.status_ditangani is null or audit_all_results.status_ditangani = "Progress")';
  }


  $data = db::select("select * from audit_all_results where audit_all_results.deleted_at is null and kategori in ('S-Up And EHS Patrol Presdir','5S Patrol GM') and tanggal between '".$datefrom."' and '".$dateto."' ".$kate." ");

  $response = array(
    'status' => true,
    'datas' => $data
  );

  return Response::json($response); 
}


public function detailPenanganan(Request $request){
  $audit = db::select("SELECT * from audit_all_results where id = ". $request->get('id'));

  $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
    where end_date is null and (position like '%Staff%' or position like '%Chief%' or position like '%Foreman%' or position like '%Manager%' or position like '%Coordinator%')");

  if ($request->get('patrol') == 'patrol_bangunan') {
    $auditee = db::select("select DISTINCT employee_id, name, section, position from employee_syncs
      where end_date is null and employee_id in ('PI9906003', 'PI2102025', 'PI0302001')");
  }


  $response = array(
   'status' => true,
   'audit' => $audit,
   'location' => $this->location,
   'auditee' => $auditee
 );
  return Response::json($response);
}

public function editAudit(Request $request)
{
  try{
    $audit = AuditAllResult::find($request->get("id"));
    $audit->tanggal = $request->get('tanggal');
    $audit->point_judul = $request->get('poin');
    $audit->auditee_name = $request->get('pic');
    $audit->lokasi = $request->get('lokasi');
    $audit->note = $request->get('note');
    $audit->remark = $request->get('remark');
    $audit->save();

    $response = array(
      'status' => true,
      'datas' => "Berhasil",
    );
    return Response::json($response);
  }
  catch (QueryException $e){
    $error_code = $e->errorInfo[1];
    if($error_code == 1062){
     $response = array(
      'status' => false,
      'datas' => "Audit Already Exist",
    );
     return Response::json($response);
   }
   else{
     $response = array(
      'status' => false,
      'datas' => $e->getMessage(),
    );
     return Response::json($response);
   }
 }
}

public function postPenanganan(Request $request)
{
  try{
    $audit = AuditAllResult::find($request->get("id"));
    $audit->penanganan = $request->get('penanganan');
    $audit->tanggal_penanganan = date('Y-m-d');
    $audit->status_ditangani = $request->input('btn_status');
    // $audit->status_ditangani = 'close';
    $audit->save();

    $response = array(
      'status' => true,
      'datas' => "Berhasil",
    );
    return Response::json($response);
  }
  catch (QueryException $e){
    $error_code = $e->errorInfo[1];
    if($error_code == 1062){
     $response = array(
      'status' => false,
      'datas' => "Audit Already Exist",
    );
     return Response::json($response);
   }
   else{
     $response = array(
      'status' => false,
      'datas' => $e->getMessage(),
    );
     return Response::json($response);
   }
 }
}

public function postPenangananNew(Request $request)
{
  try{
    $id_user = Auth::id();
    $tujuan_upload = 'files/patrol';

    $file = $request->file('bukti_penanganan');
    $nama = $file->getClientOriginalName();
    $filename = pathinfo($nama, PATHINFO_FILENAME);
    $extension = pathinfo($nama, PATHINFO_EXTENSION);
    $filename = md5($filename.date('YmdHisa')).'.'.$extension;
    $file->move($tujuan_upload,$filename);

    $audit = AuditAllResult::find($request->input("id"));
    $audit->penanganan = $request->input('penanganan');
    $audit->bukti_penanganan = $filename;
    $audit->tanggal_penanganan = date('Y-m-d');
    $audit->status_ditangani = $request->input('btn_status');
    // $audit->status_ditangani = 'close';
    $audit->save();

    $response = array(
      'status' => true,
    );
    return Response::json($response);
  }
  catch (QueryException $e){
    $error_code = $e->errorInfo[1];
    if($error_code == 1062){
     $response = array(
      'status' => false,
      'datas' => "Audit Already Exist",
    );
     return Response::json($response);
   }
   else{
     $response = array(
      'status' => false,
      'datas' => $e->getMessage(),
    );
     return Response::json($response);
   }
 }
}

public function exportPatrol(Request $request){
  $time = date('d-m-Y H;i;s');

  $tanggal = "";
  $status = "";

  if (strlen($request->get('date')) > 0)
  {
    $date = date('Y-m-d', strtotime($request->get('date')));
    $tanggal = "and tanggal = '" . $date . "'";
  }

  if (strlen($request->get('status')) > 0)
  {
    if($request->get('status') == 'Temuan GM Close') {
      $status = "and kategori = '5S Patrol GM' and status_ditangani = 'close'";
    }
    else if ($request->get('status') == 'Temuan GM Open') {
      $status = "and kategori = '5S Patrol GM' and status_ditangani is null";
    }
    else if ($request->get('status') == 'Temuan GM Progress') {
      $status = "and kategori = '5S Patrol GM' and status_ditangani = 'progress'";
    }
    else if ($request->get('status') == 'Temuan Presdir Close') {
      $status = "and kategori = 'S-Up And EHS Patrol Presdir' and status_ditangani = 'close'";
    }
    else if ($request->get('status') == 'Temuan Presdir Open') {
      $status = "and kategori = 'S-Up And EHS Patrol Presdir' and status_ditangani is null";
    }
    else if ($request->get('status') == 'Temuan Presdir Progress') {
      $status = "and kategori = 'S-Up And EHS Patrol Presdir' and status_ditangani = 'progress'";
    }
  }

  $detail = db::select(
    "SELECT DISTINCT audit_all_results.* from audit_all_results WHERE audit_all_results.deleted_at IS NULL ".$tanggal." ".$status." order by id ASC");

  $data = array(
    'detail' => $detail
  );

  ob_clean();

  Excel::create('Audit List '.$time, function($excel) use ($data){
    $excel->sheet('Data', function($sheet) use ($data) {
      return $sheet->loadView('audit.audit_excel', $data);
    });
  })->export('xlsx');
}

public function exportPatrolAll(Request $request){
  $time = date('d-m-Y H;i;s');

  $tanggal = "";
  $kategori = "";

  if (strlen($request->get('date_from')) > 0)
  {

    $date_from = date('Y-m-d', strtotime($request->get('date_from')));
    $tanggal = "and tanggal = '".$date_from."'";

    if (strlen($request->get('date_to')) > 0) {

      $date_from = date('Y-m-d', strtotime($request->get('date_from')));
      $date_to = date('Y-m-d', strtotime($request->get('date_to')));

      $tanggal = "and tanggal >= '".$date_from."'";
      $tanggal = $tanggal . "and tanggal  <= '" .$date_to."'";
    }
  }


  if (strlen($request->get('category_export')) > 0)
  {

    if ($request->get('category_export') == "monthly_patrol") {
      $category = "EHS & 5S Patrol";
    }
    else if ($request->get('category_export') == "daily_patrol") {
      $category = "Patrol Daily";
    }
    else if ($request->get('category_export') == "covid_patrol") {
      $category = "Patrol Covid";
    }
    else if ($request->get('category_export') == "energy_patrol") {
      $category = "Patrol Energy";
    }
    else if ($request->get('category_export') == "washing_patrol") {
      $category = "Patrol Washing";
    }
    else if ($request->get('category_export') == "hrga_patrol") {
      $category = "Patrol HRGA";
    }
    else if ($request->get('category_export') == "vendor_patrol") {
      $category = "Patrol Vendor";
    }
    else if ($request->get('category_export') == "outside_patrol") {
      $category = "Patrol Outside";
    }
    else if ($request->get('category_export') == "stocktaking") {
      $category = "Audit Stocktaking";
    }
    else if ($request->get('category_export') == "mis") {
      $category = "Audit MIS";
    }

    $kategori = "and kategori = '".$category."'";
  }

  $detail = db::select("SELECT DISTINCT audit_all_results.* from audit_all_results WHERE audit_all_results.deleted_at IS NULL ".$tanggal." ".$kategori." order by id ASC");

  $data = array(
    'detail' => $detail
  );

  ob_clean();

  Excel::create('Report '.$category.' '.$request->get('date_from'), function($excel) use ($data){
    $excel->sheet('Data', function($sheet) use ($data) {
      return $sheet->loadView('audit.audit_excel', $data);
    });

    $lastrow = $excel->getActiveSheet()->getHighestRow();    
    $excel->getActiveSheet()->getStyle('A1:G'.$lastrow)->getAlignment()->setWrapText(true); 
          // $excel->getActiveSheet()->getColumnDimension('A:F')->setAutoSize(false);

  })->export('xlsx');
}




    // Audit & Patrol Monitoring All

public function indexMonitoringAll($id){

  if ($id == "monthly_patrol") {
    $category = "EHS & 5S Patrol";
  }
  else if ($id == "daily_patrol") {
    $category = "Patrol Daily";
  }
  else if ($id == "covid_patrol") {
    $category = "Patrol Covid";
  }
  else if ($id == "energy_patrol") {
    $category = "Patrol Energy";
  }
  else if ($id == "washing_patrol") {
    $category = "Patrol Washing";
  }
  else if ($id == "hrga_patrol") {
    $category = "Patrol HRGA";
  }
  else if ($id == "vendor_patrol") {
    $category = "Patrol Vendor";
  }
  else if ($id == "outside_patrol") {
    $category = "Patrol outside";
  }
  else if ($id == "stocktaking") {
    $category = "Audit Stocktaking";
  }
  else if ($id == "mis") {
    $category = "Audit MIS";
  }
  else if ($id == "patrol_bangunan") {
    $category = "Patrol Bangunan";
  }

  $auditor = db::select("SELECT DISTINCT auditor_name FROM audit_all_results WHERE deleted_at is null AND kategori IN ( '".$category."' ) AND (remark <> 'Positive Finding' OR remark is null) and (audit_all_results.status_ditangani is null OR status_ditangani = 'Progress')");

  $auditee = db::select("SELECT DISTINCT auditee_name FROM audit_all_results WHERE deleted_at is null AND kategori IN ( '".$category."' ) AND (remark <> 'Positive Finding' OR remark is null) and (audit_all_results.status_ditangani is null OR status_ditangani = 'Progress')");

  $remark = db::select("SELECT DISTINCT remark FROM audit_all_results WHERE deleted_at is null AND kategori IN ( '".$category."' ) and (audit_all_results.status_ditangani is null OR status_ditangani = 'Progress') and remark is not null");

  return view('audit.patrol_monitoring_all',  
   array(
     'title' => 'Audit & Patrol Monitoring', 
     'title_jp' => '監査・パトロールの表示',
     'category' => $id,
     'auditors' => $auditor,
     'auditees' => $auditee,
     'remark' => $remark
   )
 )->with('page', 'Audit Patrol Monitoring');
}

public function fetchMonitoringAll(Request $request){

  // $first = date("Y-m-d", strtotime('-30 days'));

  // $tanggal = "";
  $remark = "";

  // if (strlen($request->get('date_from')) > 0)
  // {

  //   $date_from = date('Y-m-d', strtotime($request->get('date_from')));
  //   $tanggal = "and tanggal = '".$date_from."'";

  //   if (strlen($request->get('date_to')) > 0) {

  //     $date_from = date('Y-m-d', strtotime($request->get('date_from')));
  //     $date_to = date('Y-m-d', strtotime($request->get('date_to')));

  //     $tanggal = "and tanggal >= '".$date_from."'";
  //     $tanggal = $tanggal . "and tanggal  <= '" .$date_to."'";
  //   }
  // }

  // $check = AuditAllResult::where('status_ditangani', '=', 'close')
  // ->orderBy('tanggal', 'asc')
  // ->select(db::raw('date(tanggal) as audit_date'))
  // ->first();

  // if($first > date("Y-m-d", strtotime($check->tanggal))){
  //   $first = date("Y-m-d", strtotime($check->tanggal));
  // }

  if ($request->get('category') == "monthly_patrol") {
    $category = "EHS & 5S Patrol";
  }
  else if ($request->get('category') == "daily_patrol") {
    $category = "Patrol Daily";
  }
  else if ($request->get('category') == "covid_patrol") {
    $category = "Patrol Covid";
  }
  else if ($request->get('category') == "energy_patrol") {
    $category = "Patrol Energy";
  }
  else if ($request->get('category') == "washing_patrol") {
    $category = "Patrol Washing";
  }
  else if ($request->get('category') == "hrga_patrol") {
    $category = "Patrol HRGA";
  }
  else if ($request->get('category') == "vendor_patrol") {
    $category = "Patrol Vendor";
  }
  else if ($request->get('category') == "outside_patrol") {
    $category = "Patrol Outside";
  }
  else if ($request->get('category') == "stocktaking") {
    $category = "Audit Stocktaking";
  }
  else if ($request->get('category') == "mis") {
    $category = "Audit MIS";
  }
  else if ($request->get('category') == "patrol_bangunan") {
    $category = "Patrol Bangunan";
  }

  if (strlen($request->get('remark')) > 0) {
    $remark = "and remark = '".$request->get('remark')."'";
  }

  $datefrom = date("Y-m-d",  strtotime('-30 days'));
  $dateto = date("Y-m-d");

  $first = date("Y-m-d", strtotime('-30 days'));
  $location = "";

  $last = AuditAllResult::whereIn('status_ditangani',['progress',null])
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(tanggal) as tanggal'))
  ->where('kategori','=', $category)
  ->first();

  if(strlen($request->get('date_from')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('date_from')));
  }else{
    if($last){
      $tanggal = date_create($last->tanggal);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->tanggal));
      }
    }
  }

  if(strlen($request->get('date_to')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('date_to')));
  }


  $data = db::select("SELECT
    date_format(tanggal, '%a, %d %b %Y') AS tanggal,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
    sum( CASE WHEN status_ditangani = 'progress' AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_progress,
    sum( CASE WHEN status_ditangani = 'close' AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
    FROM
    audit_all_results 
    WHERE
    tanggal >= '".$datefrom."' and tanggal <= '".$dateto."'
    and kategori in ('".$category."')
    and (remark <> 'Positive Finding' OR remark is null)
    ".$remark."
    GROUP BY
    tanggal");

  if ($request->get('category') != "vendor_patrol") {
    $data_bulan = db::select("
      SELECT
      MONTHNAME(tanggal) as bulan,
      year(tanggal) as tahun,
      sum( CASE WHEN status_ditangani IS NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
      sum( CASE WHEN status_ditangani = 'progress' AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_progress,
      sum( CASE WHEN status_ditangani = 'close' AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
      FROM
      audit_all_results 
      WHERE
      tanggal >= '".$datefrom."' and tanggal <= '".$dateto."'
      and kategori in ('".$category."')
      and (remark <> 'Positive Finding' OR remark is null)
      ".$remark."
      GROUP BY
      tahun,monthname(tanggal)
      order by tahun, month(tanggal) ASC"
    );
  }else{
    $data_bulan = db::select("
      SELECT
      MONTHNAME(tanggal) as bulan,
      year(tanggal) as tahun,
      sum( CASE WHEN (status_ditangani IS NULL and remark != 'Positive Finding') AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
      sum( CASE WHEN status_ditangani = 'progress' AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_progress,
      sum( CASE WHEN (status_ditangani = 'close' or remark = 'Positive Finding') AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
      FROM
      audit_all_results 
      WHERE
      tanggal >= '".$datefrom."' and tanggal <= '".$dateto."'
      and kategori in ('".$category."')
      ".$remark."
      GROUP BY
      tahun,monthname(tanggal)
      order by tahun, month(tanggal) ASC"
    );
  }



  $data_kategori = db::select("
   SELECT
   MONTHNAME(tanggal) as bulan,
   year(tanggal) as tahun,
   sum( CASE WHEN point_judul = 'S-Up and 5S' AND kategori = '".$category."' THEN 1 ELSE 0 END) AS Sup,
   sum( CASE WHEN point_judul = 'Safety' AND kategori = '".$category."' THEN 1 ELSE 0 END) AS Safety,
   sum( CASE WHEN point_judul = 'Environment' AND kategori = '".$category."' THEN 1 ELSE 0 END) AS Environment,
   sum( CASE WHEN point_judul = 'Health' AND kategori = '".$category."' THEN 1 ELSE 0 END) AS Health
   FROM
   audit_all_results 
   WHERE
   kategori in ('".$category."')
   and (remark <> 'Positive Finding' OR remark is null)
   and point_judul is not null
   GROUP BY
   tahun,monthname(tanggal)
   order by tahun, month(tanggal) ASC
   ");

  $response = array(
    'status' => true,
    'datas' => $data,
    'data_bulan' => $data_bulan,
    'data_kategori' => $data_kategori,
    'category' => $category,
    'remark' => $remark
  );

  return Response::json($response);
}

public function fetchTableAuditAll(Request $request)
{
  $tanggal = "";
  $auditor = "";
  $auditee = "";
  $remark = "";

  if (strlen($request->get('date_from')) > 0)
  {

    $date_from = date('Y-m-d', strtotime($request->get('date_from')));
    $tanggal = "and tanggal = '".$date_from."'";

    if (strlen($request->get('date_to')) > 0) {

      $date_from = date('Y-m-d', strtotime($request->get('date_from')));
      $date_to = date('Y-m-d', strtotime($request->get('date_to')));

      $tanggal = "and tanggal >= '".$date_from."'";
      $tanggal = $tanggal . "and tanggal  <= '" .$date_to."'";
    }
  }

  if (strlen($request->get('auditor')) > 0) {
    $auditor = "and auditor_name = '".$request->get('auditor')."'";
  }

  if (strlen($request->get('auditee')) > 0) {
    $auditee = "and auditee_name = '".$request->get('auditee')."'";
  }

  if (strlen($request->get('remark')) > 0) {
    $remark = "and remark = '".$request->get('remark')."'";
  }

  $last = AuditAllResult::whereIn('status_ditangani',['progress',null])
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(tanggal) as audit_date'))
  ->first();

  $status = $request->get('status');

  if ($status != null) {
    $cat = json_encode($status);
    $kat = str_replace(array("[","]"),array("(",")"),$cat);

    $kate = 'and audit_all_results.status_ditangani in'.$kat;
  }else{
    $kate = 'and (audit_all_results.status_ditangani is null or audit_all_results.status_ditangani = "Progress")';
  }


  if ($request->get('category') == "monthly_patrol") {
    $category = "EHS & 5S Patrol";
  }
  else if ($request->get('category') == "daily_patrol") {
    $category = "Patrol Daily";
  }
  else if ($request->get('category') == "covid_patrol") {
    $category = "Patrol Covid";
  }
  else if ($request->get('category') == "energy_patrol") {
    $category = "Patrol Energy";
  }
  else if ($request->get('category') == "washing_patrol") {
    $category = "Patrol Washing";
  }
  else if ($request->get('category') == "hrga_patrol") {
    $category = "Patrol HRGA";
  }
  else if ($request->get('category') == "vendor_patrol") {
    $category = "Patrol Vendor";
  }
  else if ($request->get('category') == "outside_patrol") {
    $category = "Patrol Outside";
  }
  else if ($request->get('category') == "stocktaking") {
    $category = "Audit Stocktaking";
  }
  else if ($request->get('category') == "mis") {
    $category = "Audit MIS";
  }
  else if ($request->get('category') == "patrol_bangunan") {
    $category = "Patrol Bangunan";
  }

  $data = db::select("select * from audit_all_results where audit_all_results.deleted_at is null and kategori in ('".$category."') ".$kate." and (remark <> 'Positive Finding' OR remark is null) ".$tanggal." ".$auditor." ".$auditee." ".$remark." ");

  $response = array(
    'status' => true,
    'datas' => $data
  );

  return Response::json($response); 
}

public function detailMonitoringAll(Request $request){

  $tgl = date('Y-m-d', strtotime($request->get("tgl")));

  $status = $request->get('status');

  if ($status != null) {

    if ($status == "Temuan Open") {
      $stat = 'and audit_all_results.status_ditangani is null and kategori = "'.$request->get('category').'"';
    }
    else if ($status == "Temuan Progress") {
      $stat = 'and audit_all_results.status_ditangani = "progress" and kategori = "'.$request->get('category').'"';
    }
    else if ($status == "Temuan Close") {
      $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "'.$request->get('category').'"';
    }

  } else{
    $stat = '';
  }

  $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and tanggal = '".$tgl."' ".$stat."";

  $detail = db::select($query);

  return DataTables::of($detail)

  ->editColumn('auditor_name', function($detail){
    $kategori = '';

    if($detail->kategori == "S-Up And EHS Patrol Presdir"){
      $kategori = "Presdir";
    }else if ($detail->kategori == "5S Patrol GM"){
      $kategori = "GM";
    }else{
      $kategori = $detail->kategori;
    }

    $tgl = date('d-M-Y', strtotime($detail->tanggal));

    return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
  })


  ->editColumn('foto', function($detail){
    return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
  })

  ->editColumn('auditee_name', function($detail){
    return $detail->point_judul.'<br>'.$detail->auditee_name;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
  ->make(true);
}

public function detailMonitoringBulanAll(Request $request){

  $bulan = $request->get('bulan');
  $tahun = $request->get('tahun');
  $status = $request->get('status');
  $remark = $request->get('remark');

  if ($status != null) {

    if ($status == "Temuan Open") {
      $stat = 'and audit_all_results.status_ditangani is null and kategori = "'.$request->get('category').'" and (remark <> "Positive Finding" OR remark is null) ';
    }
    else if ($status == "Temuan Progress") {
      $stat = 'and audit_all_results.status_ditangani = "progress" and kategori = "'.$request->get('category').'"';
    }
    else if ($status == "Temuan Close") {
      $stat = 'and (audit_all_results.status_ditangani = "close" or remark = "Positive Finding") and kategori = "'.$request->get('category').'"';
    }

  } else{
    $stat = '';
  }

  if ($remark != null) {
    $rem = $remark; 
  }

  else{
    $remark = '';
  }

  $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and monthname(tanggal) = '".$bulan."' and year(tanggal) = '".$tahun."' ".$stat." ".$remark." ";

  $detail = db::select($query);

  return DataTables::of($detail)

  ->editColumn('auditor_name', function($detail){
    $kategori = '';

    if($detail->kategori == "S-Up And EHS Patrol Presdir"){
      $kategori = "Presdir";
    }else if ($detail->kategori == "5S Patrol GM"){
      $kategori = "GM";
    }else{
      $kategori = $detail->kategori;
    }

    if ($detail->kategori == "Patrol Daily") {
      $tgl = date('d-M-Y ', strtotime($detail->created_at));
      $tgl .= '<br> Pukul : '. date('H:i:s', strtotime($detail->created_at));
    }else{
      $tgl = date('d-M-Y', strtotime($detail->tanggal));
    }


    return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
  })


  ->editColumn('foto', function($detail){
    return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
  })

  ->editColumn('auditee_name', function($detail){
    return $detail->point_judul.'<br>'.$detail->auditee_name.'<br>'.$detail->auditee;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    if($detail->remark == "Positive Finding"){
      $bukti = "Temuan Positif";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
  ->make(true);
}

    // Audit & Patrol By Team Monthly Patrol

public function indexPatrolResume($id){

  return view('audit.patrol_monthly_team',  
   array(
     'title' => 'Monthly Patrol Resume', 
     'title_jp' => '月次パトロールめとめ',
     'category' => $id
   )
 )->with('page', 'Monthly Patrol Resume');
}

public function fetchPatrolResume(Request $request){

  $first = date("Y-m-d", strtotime('-30 days'));

  $check = AuditAllResult::where('status_ditangani', '=', 'close')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(tanggal) as audit_date'))
  ->first();

  if($first > date("Y-m-d", strtotime($check->tanggal))){
    $first = date("Y-m-d", strtotime($check->tanggal));
  }

  if ($request->get('month') != "") {
    $month = "and DATE_FORMAT(tanggal,'%Y-%m') = '".$request->get('month')."'";
  }else{
    $month = "";
  }

  if ($request->get('category') == "monthly_patrol") {
    $category = "EHS & 5S Patrol";
  }
  else if ($request->get('category') == "daily_patrol") {
    $category = "Patrol Daily";
  }
  else if ($request->get('category') == "covid_patrol") {
    $category = "Patrol Covid";
  }
  else if ($request->get('category') == "energy_patrol") {
    $category = "Patrol Energy";
  }
  else if ($request->get('category') == "washing_patrol") {
    $category = "Patrol Washing";
  }
  else if ($request->get('category') == "hrga_patrol") {
    $category = "Patrol HRGA";
  }
  else if ($request->get('category') == "vendor_patrol") {
    $category = "Patrol Vendor";
  }
  else if ($request->get('category') == "outside_patrol") {
    $category = "Patrol Outside";
  }
  else if ($request->get('category') == "stocktaking") {
    $category = "Audit Stocktaking";
  }
  else if ($request->get('category') == "mis") {
    $category = "Audit MIS";
  }

  $data_bulan = db::select("
    SELECT
    auditor_name,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
    sum( CASE WHEN status_ditangani = 'progress' AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_progress,
    sum( CASE WHEN status_ditangani = 'close' AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
    FROM
    audit_all_results 
    WHERE
    kategori in ('".$category."')
    and (remark <> 'Positive Finding' OR remark is null)
    ".$month."
    GROUP BY
    auditor_name ASC
    "
  );

  $data_lokasi = db::select("
    SELECT
    lokasi,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_belum,
    sum( CASE WHEN status_ditangani = 'progress' AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_progress,
    sum( CASE WHEN status_ditangani = 'close' AND kategori = '".$category."' THEN 1 ELSE 0 END ) AS jumlah_sudah
    FROM
    audit_all_results 
    WHERE
    kategori in ('".$category."')
    and (remark <> 'Positive Finding' OR remark is null)
    ".$month."
    GROUP BY
    lokasi ASC
    "
  );

  $response = array(
    'status' => true,
    'data_bulan' => $data_bulan,
    'data_lokasi' => $data_lokasi,
    'category' => $category
  );

  return Response::json($response);
}

public function detailPatrolResume(Request $request){

  $auditor = $request->get('auditor');
  $status = $request->get('status');

  if ($request->get('month') != "") {
    $month = "and DATE_FORMAT(tanggal,'%Y-%m') = '".$request->get('month')."'";
  }else{
    $month = "";
  }

  if ($status != null) {

    if ($status == "Temuan Open") {
      $stat = 'and audit_all_results.status_ditangani is null and kategori = "'.$request->get('category').'"';
    }
    else if ($status == "Temuan Progress") {
      $stat = 'and audit_all_results.status_ditangani = "progress" and kategori = "'.$request->get('category').'"';
    }
    else if ($status == "Temuan Close") {
      $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "'.$request->get('category').'"';
    }

  } else{
    $stat = '';
  }

  $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and auditor_name = '".$auditor."' ".$stat." ".$month." and (remark <> 'Positive Finding' OR remark is null)";

  $detail = db::select($query);

  return DataTables::of($detail)

  ->editColumn('auditor_name', function($detail){
    $kategori = '';

    if($detail->kategori == "S-Up And EHS Patrol Presdir"){
      $kategori = "Presdir";
    }else if ($detail->kategori == "5S Patrol GM"){
      $kategori = "GM";
    }else{
      $kategori = $detail->kategori;
    }

    $tgl = date('d-M-Y', strtotime($detail->tanggal));

    return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
  })


  ->editColumn('foto', function($detail){
    return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
  })

  ->editColumn('auditee_name', function($detail){
    return $detail->point_judul.'<br>'.$detail->auditee_name;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
  ->make(true);
}

public function detailLokasiPatrolResume(Request $request){

  $lokasi = $request->get('lokasi');
  $status = $request->get('status');

  if ($request->get('month') != "") {
    $month = "and DATE_FORMAT(tanggal,'%Y-%m') = '".$request->get('month')."'";
  }else{
    $month = "";
  }

  if ($status != null) {

    if ($status == "Temuan Open") {
      $stat = 'and audit_all_results.status_ditangani is null and kategori = "'.$request->get('category').'"';
    }
    else if ($status == "Temuan Progress") {
      $stat = 'and audit_all_results.status_ditangani = "progress" and kategori = "'.$request->get('category').'"';
    }
    else if ($status == "Temuan Close") {
      $stat = 'and audit_all_results.status_ditangani = "close" and kategori = "'.$request->get('category').'"';
    }

  } else{
    $stat = '';
  }

  $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and lokasi = '".$lokasi."' ".$stat." ".$month." and (remark <> 'Positive Finding' OR remark is null)";

  $detail = db::select($query);

  return DataTables::of($detail)

  ->editColumn('auditor_name', function($detail){
    $kategori = '';

    if($detail->kategori == "S-Up And EHS Patrol Presdir"){
      $kategori = "Presdir";
    }else if ($detail->kategori == "5S Patrol GM"){
      $kategori = "GM";
    }else{
      $kategori = $detail->kategori;
    }

    $tgl = date('d-M-Y', strtotime($detail->tanggal));

    return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
  })


  ->editColumn('foto', function($detail){
    return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
  })

  ->editColumn('auditee_name', function($detail){
    return $detail->point_judul.'<br>'.$detail->auditee_name;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
  ->make(true);
}

public function ExportMonthlyPatrolResume(){
  $query = "select audit_all_results.* FROM audit_all_results where audit_all_results.deleted_at is null and audit_all_results.status_ditangani = 'close' and kategori = 'EHS & 5S Patrol'";

  $detail = db::select($query);

  return view('audit.patrol_monthly_team_export',  
   array(
     'title' => 'Monthly Patrol By Location List', 
     'title_jp' => '場所別の月次パトロール',
     'data' => $detail
   )
 )->with('page', 'Monthly Patrol By Location List');

}


public function index_packing_documentation()
{ 
  $title = "Packing Documentation";
  $title_jp = "梱包作業の書類化";

  return view('documentation.index_packing_documentation', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Packing Documentation'); 
}


public function packing_documentation($loc)
{ 
  if ($loc == 'fl') {
    $loc = 'Flute';
  } 
  else if ($loc == 'cl') {
    $loc = 'Clarinet';
  } 
  else if ($loc == 'sx') {
    $loc = 'Saxophone';
  }
  else{
    $loc = $loc;
  }

  $title = "Packing Documentation ".$loc;
  $title_jp = "梱包作業の書類化";

  $employees = EmployeeSync::whereNull('end_date')->get();

  return view('documentation.packing_documentation', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'employees' => $employees,
      // 'data' => $data,
    'loc' => $loc
  ))->with('page', 'Packing Documentation'); 
}

public function documentation_data(Request $request){

  try{

        // if ($request->get('loc') == "Flute") {
        // $sn = LogProcess::select('log_processes.*')
        // ->where('serial_number','=',$request->get('tag'))
        // ->where('origin_group_code','=','041')
        // ->where('process_code','=','6')
        // ->whereNull('log_processes.deleted_at')
        // ->first();
        // }
        // else if ($request->get('loc') == "Saxophone"){
        //    $sn = LogProcess::select('log_processes.*')
        //   ->where('serial_number','=',$request->get('tag'))
        //   ->where('origin_group_code','=','043')
        //   ->where('process_code','=','4')
        //   ->whereNull('log_processes.deleted_at')
        //   ->first();
        // }


    if (count($sn) > 0) {
      $response = array(
        'status' => true,
        'message' => 'Base Data Ditemukan',
        'sn' => $sn
      );
      return Response::json($response);
    }
    else{
      $response = array(
        'status' => false,
        'message' => 'Data Tidak Ditemukan',
      );
      return Response::json($response);
    }

  }catch(\Exception $e){
    $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
    return Response::json($response);
  }
}

public function gmc_documentation(Request $request){
  try{

    $sn = LogProcess::select('log_processes.model','materials.material_description','materials.material_number')
    ->join('materials','materials.material_description', '=','log_processes.model')
    ->where('log_processes.serial_number','=',$request->get('serial_number'))
    ->where('log_processes.origin_group_code','=','043')
    ->where('log_processes.process_code','=','4')
    ->whereNull('log_processes.deleted_at')
    ->first();

    if (count($sn) > 0) {
      if ($sn->material_number == "WZ00190" || $sn->material_number == "WZ00200" || $sn->material_number == "VDT8450" || $sn->material_number == "WZ00420" || $sn->material_number == "VDT8440") {

        $lbl_img = '';

        $label = LabelInformation::where('material_number', '=', $sn->material_number)->first();

        if (count($label) > 0) {
          $lbl_img = $label->label_picture;
        }

        $response = array(
          'status' => true,
          'message' => 'GMC Latch Special Acceptance Ditemukan',
          'label_image' => $lbl_img,
          'sn' => $sn
        );

        return Response::json($response);
      }else{
       $response = array(
        'status' => false,
        'message' => 'Data Tidak Ditemukan'
      );
       return Response::json($response);
     } 
   }
   else{
    $response = array(
      'status' => false,
      'message' => 'Data Tidak Ditemukan',
    );
    return Response::json($response);
  }

}catch(\Exception $e){
  $response = array(
    'status' => false,
    'message' => $e->getMessage(),
  );
  return Response::json($response);
}
}

public function gmc_documentation_fl(Request $request){
  try{

    $sn = LogProcess::select('log_processes.model','materials.material_description','materials.material_number')
    ->join('materials','materials.material_description', '=','log_processes.model')
    ->where('log_processes.serial_number','=',$request->get('serial_number'))
    ->where('log_processes.origin_group_code','=','041')
    ->where('log_processes.process_code','=','6')
    ->whereNull('log_processes.deleted_at')
    ->first();

    if (count($sn) > 0) {
      if ($sn->material_number == "ZS98340") {
        $lbl_img = '';

        $label = LabelInformation::where('material_number', '=', $sn->material_number)->first();

        if (count($label) > 0) {
          $lbl_img = $label->label_picture;
        }

        $response = array(
          'status' => true,
          'message' => 'GMC Special Acceptance Ditemukan',
          'label_image' => $lbl_img,
          'sn' => $sn
        );
        return Response::json($response);
      }else{
       $response = array(
        'status' => false,
        'message' => 'Data Tidak Ditemukan'
      );
       return Response::json($response);
     } 
   }
   else{
    $response = array(
      'status' => false,
      'message' => 'Data Tidak Ditemukan',
    );
    return Response::json($response);
  }

}catch(\Exception $e){
  $response = array(
    'status' => false,
    'message' => $e->getMessage(),
  );
  return Response::json($response);
}
}

public function gmc_documentation_cl(Request $request){
  try{
    if ($request->get('location') == 'Saxophone') {
      $group_code = '043';
      $code = '4';
    } else if ($request->get('location') == 'Flute') {
      $group_code = '041';
      $code = '6';
    } else if ($request->get('location') == 'Clarinet') {
      $group_code = '042';
      $code = '4';
    }

    $sn = LogProcess::select('log_processes.model','materials.material_description','materials.material_number')
    ->join('materials','materials.material_description', '=','log_processes.model')
    ->where('log_processes.serial_number','=',$request->get('serial_number'))
    ->where('log_processes.origin_group_code','=', $group_code)
    ->where('log_processes.process_code','=',$code)
    ->whereNull('log_processes.deleted_at')
    ->first();

    if (count($sn) > 0) {
      $lbl_img = '';

      $label = LabelInformation::where('material_number', '=', $sn->material_number)->first();

      if (count($label) > 0) {
        $lbl_img = $label->label_picture;
      }

      if (($sn->material_number == "VAM5150" || $sn->material_number == "WZ00120" || $sn->material_number == "WZ00160" || $sn->material_number == "WZ00180" || $sn->material_number == "WZ44910") && $request->get('location') == 'Clarinet')  {
        $response = array(
          'status' => true,
          'message' => 'Pengecekan Reed Baru',
          'label_image' => $lbl_img,
          'sn' => $sn
        );
        return Response::json($response);
      } else{
        $response = array(
          'status' => false,
          'message' => 'Data Tidak Ditemukan',
          'label_image' => $lbl_img,
        );
        return Response::json($response);
      }
    } else{
      $response = array(
        'status' => false,
        'label_image' => '',
        'message' => 'Data Tidak Ditemukan',
      );
      return Response::json($response);
    }

  }catch(\Exception $e){
    $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
    return Response::json($response);
  }
}

public function documentation_post(Request $request)
{
  try{

    $tujuan_upload = 'images/packing';
    $z = 0;

    for ($i=0; $i < count($request->file('file_datas')); $i++) { 
      $file = $request->file('file_datas')[$i];
      $nama = $file->getClientOriginalName();
            // $filename = pathinfo($nama, PATHINFO_FILENAME);
      $extension = pathinfo($nama, PATHINFO_EXTENSION);
      $filename = $request->input('serial_number').' ('.date('d-M-y H-i-s').')['.$i.'].'.$extension;
      $file->move($tujuan_upload,$filename);

      $data[]=$filename;      
      $z++;
    }

    $file_upload = json_encode($data);     

    $documentation = db::connection('ympimis_2')
    ->table('packing_documentations')
    ->insertGetId([
      'location' => $request->input('location'),
      'employee_id' => $request->input('employee_id'),
      'employee_name' => $request->input('employee_name'),
      'serial_number' => $request->input('serial_number'),
      'photo' => $file_upload,
      'created_by' => Auth::id(),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ]);

    if ($request->input('gmc_latch') != null || $request->input('gmc_latch') != "") {
      $latch = db::connection('ympimis_2')
      ->table('packing_latchs')
      ->insert([
        'location' => $request->input('location'),
        'employee_id' => $request->input('employee_id'),
        'employee_name' => $request->input('employee_name'),
        'serial_number' => $request->input('serial_number'),
        'material_number' => $request->input('gmc_latch'),
        'material_description' => $request->input('desc_latch'),
        'latch' => $request->input('latch_information'),
        'created_by' => Auth::id(),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ]);
    }

    if (count($request->file('file_evidence')) > 0) {
      $origin = '';

      if ($request->input('location') == 'Saxophone') {
        $origin = '043';
      } else if ($request->input('location') == 'Flute') {
        $origin = '041';
      } else if ($request->input('location') == 'Clarinet') {
        $origin = '042';
      }

      $sn = LogProcess::select('log_processes.model','materials.material_description','materials.material_number')
      ->join('materials','materials.material_description', '=','log_processes.model')
      ->where('log_processes.serial_number','=',$request->input('serial_number'))
      ->where('log_processes.origin_group_code','=', $origin)
      ->whereNull('log_processes.deleted_at')
      ->first();

      $tujuan_upload2 = 'files/label/three_man_eff';

      $file2 = $request->file('file_evidence');
      $nama2 = $file2->getClientOriginalName();
      $extension2 = pathinfo($nama2, PATHINFO_EXTENSION);
      $filename2 = $sn->material_number.' ('.date('d-M-y H-i-s').').'.$extension2;
      $file2->move($tujuan_upload2,$filename2);

      if ($request->input('location') == 'Clarinet') {
        $search = db::connection('ympimis_2')->table('packing_documentations')->where('id', '=', $documentation)->first();
        $photo = json_decode($search->photo);

        $filename3 = $request->input('serial_number').' ('.date('d-M-y H-i-s').')['.$z.'].'.$extension2;

        copy($tujuan_upload2.'/'.$filename2, $tujuan_upload.'/'.$filename3);

        array_push($photo, $filename3);

        DB::connection('ympimis_2')
        ->table('packing_documentations')
        ->where('id', $documentation)
        ->update(['photo' => json_encode($photo)]);

      }

      $labels = new LabelEvidence([
        'material_number' => $sn->material_number,
        'material_description' => $sn->material_description,
        'product' => $request->input('location'),
        'remark' => 'Three Man',
        'evidence' => $filename2,
        'note' => $request->input('serial_number'),
        'created_by' => Auth::user()->username
      ]);
      $labels->save();
    }


    $response = array(
      'status' => true,
      'message' => 'Data Dokumentasi Berhasil Dimasukkan'
    );
    return Response::json($response);

  }
  catch(\Exception $e){
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function packing_outer_documentation($loc)
{ 
  if ($loc == 'fl') {
    $loc = 'Flute';
  } 
  else if ($loc == 'cl') {
    $loc = 'Clarinet';
  } 
  else if ($loc == 'sx') {
    $loc = 'Saxophone';
  }
  else{
    $loc = $loc;
  }

  $title = "Packing Outer Documentation ".$loc;
  $title_jp = "梱包作業の書類化";

  $employees = EmployeeSync::whereNull('end_date')->get();

  return view('documentation.packing_outer_documentation', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'employees' => $employees,
        // 'data' => $data,
    'loc' => $loc
  ))->with('page', 'Packing Documentation'); 
}

public function documentation_outer_post(Request $request)
{
  try{
    $tujuan_upload = 'images/packing_outer';

    for ($i=0; $i < count($request->file('file_datas')); $i++) { 
      $file = $request->file('file_datas')[$i];
      $nama = $file->getClientOriginalName();
            // $filename = pathinfo($nama, PATHINFO_FILENAME);
      $extension = pathinfo($nama, PATHINFO_EXTENSION);
      $filename = $request->input('material_number').' ('.date('d-M-y H-i-s').')['.$i.'].'.$extension;
      $file->move($tujuan_upload,$filename);

      $data[]=$filename;      
    }

    $file_upload = json_encode($data);     

    $documentation = db::connection('ympimis_2')
    ->table('packing_outer_documentations')
    ->insert([
      'location' => $request->input('location'),
      'employee_id' => $request->input('employee_id'),
      'employee_name' => $request->input('employee_name'),
      'material_number' => $request->input('material_number'),
      'photo' => $file_upload,
      'created_by' => Auth::id(),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ]);

    $response = array(
      'status' => true,
      'message' => 'Data Dokumentasi Berhasil Dimasukkan'
    );
    return Response::json($response);

  }
  catch(\Exception $e){
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function report_packing_documentation($location){
  $title = 'Report Dokumentasi Packing FG';
  $title_jp = '';

  return view('documentation.report_packing_documentation', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'location' => $location
  ))->with('page', 'Report Packing Documentation');
}

function does_url_exists($url) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_NOBODY, true);
  curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

  if ($code == 200) {
    $status = true;
  } else {
    $status = false;
  }
  curl_close($ch);
  return $status;
}
public function fetch_packing_documentation(Request $request)
{
  $tanggal = "";

  $loc = "AND location = '".$request->get('location')."'";  

  if ($request->get('tanggal') == '') {
    $tanggal = "AND DATE(created_at) = '".date('Y-m-d')."'";
  }else{
    $datefrom = date('Y-m-d', strtotime($request->get('tanggal')));
    $tanggal = "and DATE(created_at) >= '" . $datefrom . "'";

    if (strlen($request->get('tanggal_ke')) > 0)
    {
      $dateto = date('Y-m-d', strtotime($request->get('tanggal_ke')));
      $tanggal = $tanggal . "and DATE(created_at) <= '" . $dateto . "' ";
    }

    // $tanggal = "AND DATE(created_at) = '".$request->get('tanggal')."'";
  }

  $lists = db::connection('ympimis_2')
  ->select("SELECT
        *
    FROM
    packing_documentations 
    WHERE
    deleted_at IS NULL
    ".$loc."
    ".$tanggal."
    ORDER BY id desc
    "); 

  $data = [];

  for ($i = 0; $i < count($lists); $i++) {
    if ($lists[$i]->location == 'Saxophone') {
      $log = LogProcess::select('*')
      ->where('log_processes.origin_group_code','=','043')
      ->where('log_processes.process_code','=','4')
      ->where('log_processes.serial_number','=',$lists[$i]->serial_number)
      ->whereNull('log_processes.deleted_at')
      ->first();

    } else if ($lists[$i]->location == 'Clarinet'){
      $log = LogProcess::select('*')
      ->where('log_processes.origin_group_code','=','042')
      ->where('log_processes.process_code','=','4')
      ->whereNull('log_processes.deleted_at')
      ->where('log_processes.serial_number','=',$lists[$i]->serial_number)
      ->first();
    } else if ($lists[$i]->location == 'Flute'){
      $log = LogProcess::select('*')
      ->where('log_processes.origin_group_code','=','041')
      ->where('log_processes.process_code','=','6')
      ->whereNull('log_processes.deleted_at')
      ->where('log_processes.serial_number','=',$lists[$i]->serial_number)
      ->first();
    }

  //   $photos = json_decode($lists[$i]->photo);

  //   $photoss = [];

  //   for ($j=0; $j < count($photos); $j++) {
  //   	$file = 'http://10.109.52.4/mirai/public/images/packing/'.$photos[$j];
		// $file_headers = @get_headers($file);
		// if($file_headers[0] == 'HTTP/1.1 400 Bad Request') {
		//     $exists = false;
		// }
		// else {
		//     $exists = true;
		// }
  //   	if ($exists) {
	 //    	array_push($photoss, 'http://10.109.52.4/mirai/public/images/packing/'.$photos[$j]);
	 //    }else{
	 //    	$file = 'http://10.109.52.3/mirai/public/images/packing/'.$photos[$j];
		// 	$file_headers = @get_headers($file);
		// 	if($file_headers[0] == 'HTTP/1.1 400 Bad Request') {
		// 	    $exists = false;
		// 	}
		// 	else {
		// 	    $exists = true;
		// 	}
	 //    	if ($exists) {
		//     	array_push($photoss, 'http://10.109.52.3/mirai/public/images/packing/'.$photos[$j]);
		//     }else{
		//     	$file = 'http://10.109.52.6/mirai/public/images/packing/'.$photos[$j];
		// 		$file_headers = @get_headers($file);
		// 		if($file_headers[0] == 'HTTP/1.1 400 Bad Request') {
		// 		    $exists = false;
		// 		}
		// 		else {
		// 		    $exists = true;
		// 		}
		//     	if ($exists) {
		// 	    	array_push($photoss, 'http://10.109.52.6/mirai/public/images/packing/'.$photos[$j]);
		// 	    }
		//     }
	 //    }
  //   }

    // var_dump($photoss);
    // die();

    if ($log) {
     array_push($data, [
      'location' => $lists[$i]->location,
      'serial_number' => $lists[$i]->serial_number,
      'employee_id' => $lists[$i]->employee_id,
      'employee_name' => $lists[$i]->employee_name,
      'model' => $log->model,
      'photo' => $lists[$i]->photo,
      'created_at' => $lists[$i]->created_at,
      'id' => $lists[$i]->id
    ]);
   }else{
    array_push($data, [
      'location' => $lists[$i]->location,
      'serial_number' => $lists[$i]->serial_number,
      'employee_id' => $lists[$i]->employee_id,
      'employee_name' => $lists[$i]->employee_name,
      'model' => '',
      'photo' => $lists[$i]->photo,
      'created_at' => $lists[$i]->created_at,
      'id' => $lists[$i]->id
    ]);
  }



}

$response = array(
  'status' => true,
  'data' => $data
);
return Response::json($response);
}

public function report_packing_outer_documentation($location){
  $title = 'Report Dokumentasi Packing Outer FG';
  $title_jp = '';

  return view('documentation.report_packing_outer_documentation', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'location' => $location
  ))->with('page', 'Report Packing Documentation');
}

public function fetch_packing_outer_documentation(Request $request)
{
  $tanggal = "";

  $loc = "AND location = '".$request->get('location')."'";  

  if ($request->get('tanggal') == '') {
    $tanggal = "";
  }else{
    $tanggal = "AND DATE(created_at) = '".$request->get('tanggal')."'";
  }

  $lists = db::connection('ympimis_2')
  ->select("SELECT
        *
    FROM
    packing_outer_documentations 
    WHERE
    deleted_at IS NULL
    ".$loc."
    ".$tanggal."
    ORDER BY id desc
    "); 

  $response = array(
    'status' => true,
    'lists' => $lists,
  );
  return Response::json($response);
}

public function report_latch($location){
  $title = 'Report Latch Special Acceptance';
  $title_jp = '';

  return view('documentation.report_latch', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'location' => $location
  ))->with('page', 'Report Special Acceptance');
}

public function fetch_latch(Request $request)
{

  $tanggal = "";

  $loc = "AND location = '".$request->get('location')."'";  

  if ($request->get('tanggal') == '') {
    $tanggal = "";
  }else{
    $tanggal = "AND DATE(created_at) = '".$request->get('tanggal')."'";
  }

  $lists = db::connection('ympimis_2')
  ->select("SELECT
        *
    FROM
    packing_latchs 
    WHERE
    deleted_at IS NULL
    AND latch = 'Ya'
    ".$loc."
    ".$tanggal."
    ORDER BY id desc
    "); 

  $response = array(
    'status' => true,
    'lists' => $lists,
  );
  return Response::json($response);
}


public function delete_packing_documentation(Request $request)
{
  try
  {
    $lists = db::connection('ympimis_2')
    ->select("DELETE
      FROM
      packing_documentations 
      WHERE
      id = ".$request->get('id')."
      "); 

    $response = array(
      'status' => true,
    );

    return Response::json($response);
  }
  catch(QueryException $e)
  {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );

    return Response::json($response);
  }

}

public function delete_packing_outer_documentation(Request $request)
{
  try
  {
    $lists = db::connection('ympimis_2')
    ->select("DELETE
      FROM
      packing_outer_documentations 
      WHERE
      id = ".$request->get('id')."
      "); 

    $response = array(
      'status' => true,
    );

    return Response::json($response);
  }
  catch(QueryException $e)
  {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );

    return Response::json($response);
  }

}

public function indexAuditNgJelas()
{
  $title = "QA Audit NG Jelas";
  $title_jp = "品保の明らか不良検査";

  return view('audit.index_audit_ng_jelas', array(
    'title' => $title,
    'title_jp' => $title_jp,
  ))->with('page', 'Audit NG Jelas'); 
}

public function indexQaNgJelasMonitoring()
{
  $title = 'AUDIT NG JELAS MONITORING';
  $title_jp = '明らか不良監査の監視';

  $loc = 'QA';

  $fiscal = DB::SELECT("SELECT DISTINCT
    fiscal_year 
    FROM
    weekly_calendars
    ORDER BY week_date");

        // return view('production_report.audit_ng_jelas', array(
  return view('production_report.audit_ng_jelas2', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'fiscal' => $fiscal,
    'loc' => $loc,
  ))->with('page', 'Audit NG Jelas Monitoring');
}

public function indexNgJelas()
{
  $title = "Audit NG Jelas";
  $title_jp = "明らか不良監査";

  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $month_now = date('Y-m');
  $schedule = AuditExternalClaimSchedule::select(DB::RAW('DISTINCT(audit_external_claim_schedules.audit_id)'),'audit_external_claim_points.audit_title','audit_external_claim_schedules.id',DB::RAW('DATE_FORMAT(audit_external_claim_schedules.schedule_date,"%b %Y") as schedule_date'))->where('employee_id',$emp->employee_id)->join('audit_external_claim_points','audit_external_claim_points.audit_id','audit_external_claim_schedules.audit_id')->where('audit_external_claim_schedules.schedule_status','Belum Dikerjakan')->where('audit_external_claim_schedules.remark','ng_jelas')->get();

  if (count($schedule) > 0) {
    return view('audit.index_ng_jelas', array(
      'title' => $title,
      'emp' => $emp,
      'month_now' => $month_now,
      'schedule' => $schedule,
      'title_jp' => $title_jp,
    ))->with('page', 'Audit NG Jelas'); 
  }else{
    return redirect('/index/qa/audit_ng_jelas')
    ->with('error', 'Anda tidak memiliki schedule bulan ini.')
    ->with('page', 'Audit NG Jelas');
  }
}

public function scanNgJelas(Request $request)
{
  try {
    $emp = Employee::where('tag',$request->get('employee_id'))->first();
    if (count($emp) > 0) {
      $month_now = date('Y-m');
      $schedule = AuditExternalClaimSchedule::select(DB::RAW('DISTINCT(audit_external_claim_schedules.audit_id)'),'audit_external_claim_points.audit_title','audit_external_claim_schedules.id')->where('employee_id',$emp->employee_id)->where(DB::RAW('DATE_FORMAT(schedule_date,"%Y-%m")'),$month_now)->join('audit_external_claim_points','audit_external_claim_points.audit_id','audit_external_claim_schedules.audit_id')->get();
      $response = array(
        'status' => true,
        'schedule' => $schedule,
        'employee' => $emp
      );
      return Response::json($response);
    }else{
      $response = array(
        'status' => false,
        'message' => 'Tag Invalid'
      );
      return Response::json($response);
    }
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function fetchNgJelasPoint(Request $request)
{
  try {
    $id = $request->get('audit_id');

    $audit_details = DB::SELECT("SELECT
      audit_external_claim_points.*,
      departments.department_shortname 
      FROM
      audit_external_claim_points
      LEFT JOIN departments ON departments.department_name = audit_external_claim_points.department 
      WHERE
      audit_id = '".$id."'");

    $auditee = DB::SELECT("SELECT
            * 
      FROM
      approvers 
      WHERE
      department = '".$audit_details[0]->department."' 
      AND remark IN ( 'Manager', 'Chief', 'Foreman' );");

    $response = array(
      'status' => true,
      'audit' => $audit_details,
      'auditee' => $auditee,
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

public function inputNgJelas(Request $request)
{
  try {
        // if (count($request->file('fileData')) > 0) {
        //   $tujuan_upload = 'data_file/qa/ng_jelas';
        //   $file = $request->file('fileData');
        //   $filename = md5($request->get('audit_index').'-'.$request->input('audit_id').date('YmdHisa')).'.'.$request->input('extension');
        //   $file->move($tujuan_upload,$filename);

    $mail_to = [];
    $cc = [];

    if (str_contains($request->get('chief_foreman'), ',')) {
      $mailto = explode(',', $request->get('chief_foreman'));
      for ($i=0; $i < count($mailto); $i++) { 
        array_push($mail_to, $mailto[$i]);
      }
    }else{
      array_push($mail_to, $request->get('chief_foreman'));
    }

    if (str_contains($request->get('manager'), ',')) {
      $ccs = explode(',', $request->get('manager'));
      for ($i=0; $i < count($ccs); $i++) { 
        array_push($cc, $ccs[$i]);
      }
    }else{
      array_push($cc, $request->get('manager'));
    }

    $auditdata = AuditExternalClaim::create([
      'schedule_id' => $request->get('schedule_id'),
      'audit_id' => $request->get('audit_id'),
      'audit_title' => $request->get('audit_title'),
      'periode' => $request->get('periode'),
      'email_date' => $request->get('email_date'),
      'incident_date' => $request->get('incident_date'),
      'origin' => $request->get('origin'),
      'department' => $request->get('department'),
      'area' => $request->get('area'),
      'product' => $request->get('product'),
      'audit_index' => $request->get('audit_index'),
      'audit_point' => $request->get('audit_point'),
      'audit_images' => $request->get('audit_images'),
      'auditor' => $request->get('auditor'),
      'result_check' => $request->get('result_check'),
      'chief_foreman' => $request->get('chief_foreman'),
      'manager' => $request->get('manager'),
      'note' => $request->get('note'),
      'remark' => 'ng_jelas',
      'result_image' => $request->get('filenames'),
      'created_by' => Auth::user()->id
    ]);

    $update_schedule = AuditExternalClaimSchedule::where('id',$request->get('schedule_id'))->first();
    $old_schedule = AuditExternalClaimSchedule::where('id',$request->get('schedule_id'))->first();
    $update_schedule->schedule_status = 'Sudah Dikerjakan';

    $priority = AuditExternalClaimPoint::where('audit_id',$update_schedule->audit_id)->first();
    $priority_all = AuditExternalClaimPoint::select('audit_priority')->distinct()->orderby('audit_priority')->get();

    $date_belum = DB::select("SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS date_belum,
      ( SELECT count( audit_id ) AS audit_ids FROM audit_external_claim_schedules WHERE DATE_FORMAT( schedule_date, '%Y-%m' ) = date_belum ) AS jumlah_audit 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = 'FY199' 
      AND DATE_FORMAT( week_date, '%Y-%m' ) NOT IN (
      SELECT
      a.dates 
      FROM
      ( SELECT DISTINCT ( DATE_FORMAT( schedule_date, '%Y-%m' )) dates, count( audit_id ) AS audit_ids FROM audit_external_claim_schedules GROUP BY dates ) a 
      WHERE
      a.audit_ids > 1 
      ) 
      GROUP BY
      date_belum 
      ORDER BY
      week_date");

    $date_belum_all = DB::SELECT("SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS date_belum 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = 'FY199' 
      GROUP BY
      date_belum 
      ORDER BY
      week_date");

    $schedules = [];

    $is = 0;
    $iss = 2;

    for ($i=0; $i < count($date_belum_all); $i++) { 
      for ($j=$is; $j < $iss; $j++) { 
        $schedule = array(
          'month' => $date_belum_all[$i]->date_belum,
          'priority' => $priority_all[$j]->audit_priority,
        );
        array_push($schedules, $schedule);
      }
              // if ($i % 2 == 0) {
      $iss = $iss+2;
      $is = $is+2;
              // }
      if ($iss == 18) {
        $is = 0;
        $iss = 2;
      }
    }

    $month_audit = '';

    for ($i=0; $i < count($schedules); $i++) { 
      if ($schedules[$i]['priority'] == $priority->audit_priority) {
        for ($j=0; $j < count($date_belum); $j++) { 
          if ($date_belum[$j]->date_belum == $schedules[$i]['month']) {
            $month_audit = $schedules[$i]['month'];
            break;
          }
        }
      }
    }

    $update_schedule->save();

          // if ($request->get('result_check') == 'NG' || $request->get('result_check') == 'NS') {
          //   Mail::to($mail_to)->cc($cc)->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'audit_ng_jelas'));
          // }

    $next_month = $month_audit;

    if ($next_month != '') {
      $menu = AuditExternalClaimSchedule::updateOrCreate(
        [
          'audit_id' => $old_schedule->audit_id,
          'schedule_date' => $next_month.'-01',
        ],
        [
          'audit_id' => $old_schedule->audit_id,
          'schedule_date' => $next_month.'-01',
          'employee_id' => $old_schedule->employee_id,
          'schedule_status' => 'Belum Dikerjakan',
          'created_by' => Auth::id()
        ]
      );
      $menu->save();
    }

    $response = array(
      'status' => true,
      'next_month' => $month_audit,
      'old_schedule' => $old_schedule,
    );
    return Response::json($response);
        // }else{
        //   $response = array(
        //       'status' => false,
        //       'message' => 'Upload Photo on Point '.$request->input('audit_index')
        //   );
        //   return Response::json($response);
        // }
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function sendEmailNgJelas(Request $request)
{
  try {
    $id = $request->get('id');
    $chief_foreman = $request->get('chief_foreman');
    $manager = $request->get('manager');
    $audit = AuditExternalClaim::where('id',$id)->where('remark','ng_jelas')->first();
    $auditdata = array(
      'schedule_id' => $audit->schedule_id,
      'audit_id' => $audit->audit_id,
      'audit_title' => $audit->audit_title,
      'periode' => $audit->periode,
      'email_date' => $audit->email_date,
      'incident_date' => $audit->incident_date,
      'origin' => $audit->origin,
      'department' => $audit->department,
      'area' => $audit->area,
      'product' => $audit->product,
      'audit_index' => $audit->audit_index,
      'audit_point' => $audit->audit_point,
      'audit_images' => $audit->audit_images,
      'auditor' => $audit->auditor,
      'result_check' => $audit->result_check,
      'chief_foreman' => $audit->chief_foreman,
      'manager' => $audit->manager,
      'note' => $audit->note,
      'result_image' => $audit->result_image,
      'remark' => $audit->remark,
    );

    $mail_to = [];
    if (str_contains($chief_foreman,',')) {
      $chief_foremans = explode(',', $chief_foreman);
      for ($i=0; $i < count($chief_foremans); $i++) { 
        array_push($mail_to, $chief_foremans[$i]);
      }
    }else{
      array_push($mail_to, $chief_foreman);
    }
    Mail::to($mail_to)->cc($manager)->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'audit_ng_jelas'));
    $audit->send_status = 'Terkirim_'.date('Y-m-d H:i:s');
    $audit->save();
    $response = array(
      'status' => true,
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

public function uploadFileNGJelas(Request $request)
{
  try {
    if (count($request->file('fileData')) > 0) {
      $tujuan_upload = 'data_file/qa/ng_jelas';
      $file = $request->file('fileData');
      $filename = $request->get('filename');
      $file->move($tujuan_upload,$filename);

      $response = array(
        'status' => true,
        'filename' => $filename
      );
      return Response::json($response);
    }else{
      $response = array(
        'status' => false,
        'message' => 'Upload Photo on Point '.$request->input('audit_index')
      );
      return Response::json($response);
    }
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function indexQaNgJelasReport()
{
  $title = "Report Audit NG Jelas";
  $title_jp = "明らか不良監査";

  $audit_title = AuditExternalClaimPoint::DISTINCT('audit_id')->select('audit_id','audit_title')->get();

  return view('audit.report_ng_jelas', array(
    'title' => $title,
    'audit_title' => $audit_title,
    'title_jp' => $title_jp,
  ))->with('page', 'Report Audit NG Jelas'); 
}

public function fetchQaNgJelasReport(Request $request)
{
  try {

    $date_from = $request->get('tanggal_from');
    $date_to = $request->get('tanggal_to');
    if ($date_from == "") {
     if ($date_to == "") {
      $first = "'".date('Y-m-01')."'";
      $last = "'".date('Y-m-t')."'";
      $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
      $dateTitleLast = date('d M Y',strtotime(date('Y-m-t')));
    }else{
      $first = "'".date('Y-m-01')."'";
      $last = "'".$date_to."'";
      $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
      $dateTitleLast = date('d M Y',strtotime($date_to));
    }
  }else{
   if ($date_to == "") {
    $first = "'".$date_from."'";
    $last = "'".date('Y-m-t')."'";
    $dateTitleFirst = date('d M Y',strtotime($date_from));
    $dateTitleLast = date('d M Y',strtotime(date('Y-m-t')));
  }else{
    $first = "'".$date_from."'";
    $last = "'".$date_to."'";
    $dateTitleFirst = date('d M Y',strtotime($date_from));
    $dateTitleLast = date('d M Y',strtotime($date_to));
  }
}

if ($request->get('audit_title') == '') {
  $audit_title = "";
}else{
  $audit_title = "AND audit_external_claims.audit_id = '".$request->get('audit_title')."'";
}

if ($request->get('condition') == '') {
  $condition = "";
}else{
  $condition = "AND audit_external_claims.result_check = '".$request->get('condition')."'";
}

$audit = DB::SELECT("SELECT
  audit_external_claims.*,
  DATE( audit_external_claims.created_at ) AS created,
  audit_external_claim_points.proses,
  employee_syncs.`name`,
  departments.department_shortname,
  handled.`name` AS handled_by_name 
  FROM
  audit_external_claims
  JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
  AND audit_external_claim_points.audit_index = audit_external_claims.audit_index
  JOIN employee_syncs ON employee_syncs.employee_id = audit_external_claims.auditor
  LEFT JOIN employee_syncs handled ON handled.employee_id = audit_external_claims.handled_by
  JOIN departments ON departments.department_name = audit_external_claims.department
  WHERE
  DATE( audit_external_claims.created_at ) <= ".$last." AND DATE( audit_external_claims.created_at ) >= ".$first."
  AND audit_external_claims.remark  = 'ng_jelas'
  ".$audit_title."".$condition."");
$response = array(
  'status' => true,
  'audit' => $audit,
  'dateTitleFirst' => $dateTitleFirst,
  'dateTitleLast' => $dateTitleLast
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

public function indexQaNgJelasHandling($schedule_id)
{
  $title = "Penanganan Audit NG Jelas";
  $title_jp = "";

  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

  $audit = AuditExternalClaim::select('audit_external_claims.*','departments.department_shortname')->where('schedule_id',$schedule_id)->where('result_check','!=','OK')->where('handling',null)->join('departments','department','department_name')->where('audit_external_claims.remark','ng_jelas')->get();

      // $audit_title = AuditExternalClaimPoint::DISTINCT('audit_id')->select('audit_id','audit_title')->get();

  return view('audit.handling_ng_jelas', array(
    'title' => $title,
    'emp' => $emp,
    'audit' => $audit,
    'title_jp' => $title_jp,
  ))->with('page', 'Report Audit NG Jelas'); 
}

public function inputQaNgJelasHandling(Request $request)
{
  try {
    $id = $request->get('id_handling');
    $handling = $request->get('handling');
    $handled_by = $request->get('handled_by');

    for ($i=0; $i < count($id); $i++) { 
      $audit = AuditExternalClaim::where('id',$id[$i])->first();
      $audit->handling = $handling[$i];
      $audit->handled_by = $handled_by[$i];
      $audit->handled_at = date('Y-m-d H:i:s');

      $auditor = User::where('username',$audit->auditor)->first();

      $mail_to = [];
      $cc = [];

      array_push($mail_to, $auditor->email);
      array_push($mail_to, 'ratri.sulistyorini@music.yamaha.com');
      array_push($mail_to, 'agustina.hayati@music.yamaha.com');

      if (str_contains($audit->chief_foreman, ',')) {
        $mailto = explode(',', $audit->chief_foreman);
        for ($j=0; $j < count($mailto); $j++) { 
          array_push($mail_to, $mailto[$j]);
        }
      }else{
        array_push($mail_to, $audit->chief_foreman);
      }

      if (str_contains($audit->manager, ',')) {
        $ccs = explode(',', $audit->manager);
        for ($k=0; $k < count($ccs); $k++) { 
          array_push($cc, $ccs[$k]);
        }
      }else{
        array_push($cc, $audit->manager);
      }

      array_push($cc, 'yayuk.wahyuni@music.yamaha.com');

      $audit->save();

      $auditdata = AuditExternalClaim::select('audit_external_claims.*','handled_bys.name as handled_name')->join('employee_syncs as handled_bys','handled_bys.employee_id','audit_external_claims.handled_by')->where('id',$id[$i])->first();

          // Mail::to($mail_to)->cc($cc)->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','rio.irvansyah@music.yamaha.com'])->send(new SendEmail($auditdata, 'audit_ng_jelas_handling'));

    }
    $response = array(
      'status' => true,
      'message' => 'Input Penanganan Sukses',
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

public function indexNgJelasSchedule(Request $request)
{
  $title = "Schedule Audit NG Jelas";
  $title_jp = "";

  $audit = DB::table('audit_external_claim_points')->select('audit_id','audit_title')->distinct()->orderby('audit_id')->get();

  $emp = EmployeeSync::where('end_date',null)->get();

  return view('audit.schedule_ng_jelas', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'audit' => $audit,
    'audit2' => $audit,
    'audit3' => $audit,
    'emp' => $emp,
    'emp2' => $emp,
  ))->with('page', 'Schedule Audit NG Jelas');
}

public function fetchNgJelasSchedule(Request $request)
{
  try {
    $awal_fiscal = DB::SELECT("SELECT DISTINCT
      (
      DATE_FORMAT( week_date, '%Y-%m' )) AS months,
      DATE_FORMAT( week_date, '%b %Y' ) AS `month_name`,
      fiscal_year 
      FROM
      weekly_calendars 
      WHERE
      fiscal_year = (
      SELECT
      fiscal_year 
      FROM
      weekly_calendars 
      WHERE
      week_date = DATE(
      NOW())) 
      ORDER BY
      week_date");
    $date_from = $request->get('tanggal_from');
    $date_to = $request->get('tanggal_to');

    if ($date_from == null) {
     if ($date_to == null) {
      $first = "";
      $last = "";
      $dateTitleFirst = "";
      $dateTitleLast = "";
    }else{
      $first = "";
      $last = " WHERE DATE_FORMAT(schedule_date,'%Y-%m') <= '".$date_to."'";
      $dateTitleFirst = "";
      $dateTitleLast = "Hingga Bulan ".date('F Y',strtotime($date_to));
    }
  }else{
   if ($date_to == null) {
    $first = " WHERE DATE_FORMAT(schedule_date,'%b-%Y') >= '".$date_from."'";
    $last = "";
    $dateTitleFirst = "Mulai Bulan ".date('F Y',strtotime($date_from));
    $dateTitleLast = "";
  }else{
    $first = " WHERE DATE_FORMAT(schedule_date,'%Y-%m') >= '".$date_from."'";
    $last = " AND DATE_FORMAT(schedule_date,'%Y-%m') <= '".$date_to."'";
    $dateTitleFirst = date('F Y',strtotime($date_from))." - ";
    $dateTitleLast = date('F Y',strtotime($date_to));
  }
}
$audit_id = "";
if ($request->get('audit_id') != '') {
  $audit_id = "AND audit_external_claim_schedules.audit_id = '".explode('_', $request->get('audit_id'))[0]."'";
}

$schedule = DB::SELECT("SELECT DISTINCT
  ( audit_external_claim_schedules.audit_id ),
  audit_external_claim_points.audit_title,
  audit_external_claim_points.department,
  audit_external_claim_points.area,
  audit_external_claim_points.product,
  audit_external_claim_schedules.schedule_date,
  DATE_FORMAT(schedule_date,'%b-%Y') as schedule_dates,
  employee_syncs.employee_id,
  employee_syncs.`name`,
  schedule_status,
  `audit_external_claim_schedules`.id as schedule_id
  FROM
  `audit_external_claim_schedules`
  JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id
  JOIN employee_syncs ON employee_syncs.employee_id = audit_external_claim_schedules.employee_id 
  ".$first."
  ".$last."
  ".$audit_id."
  AND audit_external_claim_schedules.remark = 'ng_jelas'
  ORDER BY
  audit_external_claim_schedules.schedule_date DESC");

$response = array(
  'status' => true,
  'schedule' => $schedule,
  'dateTitleFirst' => $dateTitleFirst,
  'dateTitleLast' => $dateTitleLast,
  'audit_id' => $audit_id,
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

public function updateNgJelasSchedule(Request $request)
{
  try {
    $id = $request->get('id');
    $audit_id = $request->get('audit_id');
    $employee_id = $request->get('employee_id');
    $schedule_date = $request->get('schedule_date').'-01';

    $update = DB::table('audit_external_claim_schedules')->where('id',$id)->update([
      'audit_id' => $audit_id,
      'employee_id' => $employee_id,
      'schedule_date' => $schedule_date,
      'updated_at' => date('Y-m-d H:i:s')
    ]);
    $response = array(
      'status' => true,
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

public function inputNgJelasSchedule(Request $request)
{
  try {
    $audit_id = $request->get('audit_id');
    $employee_id = $request->get('employee_id');
    $schedule_date = $request->get('schedule_date').'-01';

    $update = DB::table('audit_external_claim_schedules')->insert([
      'audit_id' => $audit_id,
      'employee_id' => $employee_id,
      'schedule_date' => $schedule_date,
      'schedule_status' => 'Belum Dikerjakan',
      'remark' => 'ng_jelas',
      'created_by' => Auth::user()->id,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $response = array(
      'status' => true,
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

public function deleteNgJelasSchedule(Request $request)
{
  try {
    $id = $request->get('id');
    $delete = DB::table('audit_external_claim_schedules')->where('id',$id)->delete();
    $response = array(
      'status' => true,
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

public function downloadNgJelasSchedule()
{
  $file_path = public_path('data_file/qa/TemplateScheduleAuditNGJelas.xlsx');
  return response()->download($file_path);
}

public function uploadNgJelasSchedule(Request $request)
{
  $filename = "";
  $file_destination = 'data_file/qa';

  if (count($request->file('newAttachment')) > 0) {
    try{
      $file = $request->file('newAttachment');
      $filename = 'schedule_'.date('YmdHisa').'.'.$request->input('extension');
      $file->move($file_destination, $filename);

      $excel = 'data_file/qa/' . $filename;
      $rows = Excel::load($excel, function($reader) {
        $reader->noHeading();
        $reader->skipRows(1);

        $reader->each(function($row) {
        });
      })->toObject();

      for ($i=0; $i < count($rows); $i++) {
        $schedule = AuditExternalClaimSchedule::updateOrCreate(
          [
            'schedule_date' => $rows[$i][3],
            'audit_id' => $rows[$i][0],
          ],
          [
            'audit_id' => $rows[$i][0],
            'employee_id' => $rows[$i][2],
            'schedule_date' => date('Y-m-d', strtotime($rows[$i][3])),
            'schedule_status' => 'Belum Dikerjakan',
            'remark' => 'ng_jelas',
            'created_by' => Auth::id()
          ]
        );
        $schedule->save();
      }

      $response = array(
        'status' => true,
        'message' => 'Schedule succesfully uploaded'
      );
      return Response::json($response);
    }
    catch(\Exception $e){
      $response = array(
        'status' => false,
        'message' => $e->getMessage(),
      );
      return Response::json($response);
    }
  }
  else{
    $response = array(
      'status' => false,
      'message' => 'Please select file to attach'
    );
    return Response::json($response);
  }
}

public function editNgJelas(Request $request)
{
  try {
    $audit = AuditExternalClaim::select('audit_external_claims.*','employee_syncs.name')->join('employee_syncs','employee_syncs.employee_id','audit_external_claims.auditor')->where('audit_external_claims.id',$request->get('id'))->first();
    $response = array(
      'status' => true,
      'audit' => $audit
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

public function updateNgJelas(Request $request)
{
  try {
    $audit = AuditExternalClaim::where('id',$request->get('audit_id'))->first();
    if (str_contains($request->get('filenames'),'undefined')) {

    }else{
      $audit->result_image = $request->get('filenames');
    }
    $audit->result_check = $request->get('result_check');
    $audit->note = $request->get('note');
    $audit->save();
    $response = array(
      'status' => true,
      'message' => 'Upload Success'
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

public function monitoring_packing_documentation()
{
  $title = 'Dokumentasi Packing Monitoring';
  $title_jp = '';

  return view('documentation.monitoring_packing_documentation', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Packing Documentation Monitoring');   
}

public function fetch_monitoring_packing_documentation(Request $request)
{
  try {
    $doc = db::connection('ympimis_2')->select("
     SELECT
     MONTHNAME( created_at ) AS bulan,
     YEAR ( created_at ) AS tahun,
     sum( CASE WHEN packing_documentations.`location` = 'Clarinet' THEN 1 ELSE 0 END ) AS `Clarinet`,
     sum( CASE WHEN packing_documentations.`location` = 'Saxophone' THEN 1 ELSE 0 END ) AS `Saxophone`,
     sum( CASE WHEN packing_documentations.`location` = 'Flute' THEN 1 ELSE 0 END ) AS `Flute` 
     FROM
     `packing_documentations` 
     GROUP BY
     bulan,
     tahun 
     ORDER BY
     tahun,
     MONTH ( created_at ) ASC
     ");

    $documentations = db::connection('ympimis_2')
    ->select("SELECT
              *
      FROM
      packing_documentations 
      WHERE
      deleted_at IS NULL
      ORDER BY id DESC
      "
    ); 

    $response = array(
      'status' => true,
      'doc' => $doc,
      'documentation' => $documentations
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

public function monitoring_packing_outer_documentation()
{
  $title = 'Dokumentasi Packing Outer Monitoring';
  $title_jp = '';

  return view('documentation.monitoring_packing_outer_documentation', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Packing Documentation Monitoring');   
}

public function fetch_monitoring_packing_outer_documentation(Request $request)
{
  try {
    $doc = db::connection('ympimis_2')->select("
     SELECT
     MONTHNAME( created_at ) AS bulan,
     YEAR ( created_at ) AS tahun,
     sum( CASE WHEN packing_outer_documentations.`location` = 'Clarinet' THEN 1 ELSE 0 END ) AS `Clarinet`,
     sum( CASE WHEN packing_outer_documentations.`location` = 'Saxophone' THEN 1 ELSE 0 END ) AS `Saxophone`,
     sum( CASE WHEN packing_outer_documentations.`location` = 'Flute' THEN 1 ELSE 0 END ) AS `Flute` 
     FROM
     `packing_outer_documentations` 
     GROUP BY
     bulan,
     tahun 
     ORDER BY
     tahun,
     MONTH ( created_at ) ASC
     ");

    $documentations = db::connection('ympimis_2')
    ->select("SELECT
              *
      FROM
      packing_outer_documentations 
      WHERE
      deleted_at IS NULL
      ORDER BY id DESC
      "
    ); 

    $response = array(
      'status' => true,
      'doc' => $doc,
      'documentation' => $documentations
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

public function indexMonitoringLive(){

  return view('audit.hr_live',  
   array(
     'title' => 'Live Monitoring', 
     'title_jp' => 'LIVE（従業員の声）の表示',
     'location' => $this->location
   )
 )->with('page', 'Live');
}


public function fetchMonitoringLive(Request $request){

  // $datefrom = date("Y-m-d",  strtotime('-30 days'));
  // $dateto = date("Y-m-d");

  // $first = date("Y-m-d", strtotime('-30 days'));

  // $last = db::connection('ympimis_2')->table('hr_lives')
  // ->whereNull('status_ditangani')
  // ->orderBy('tanggal', 'asc')
  // ->select(db::raw('date(tanggal) as tanggal'))
  // ->where('kategori','live')
  // ->first();
  $tanggal = "";

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
    $tanggal = "and tanggal >= '" . $datefrom . "'";
    if (strlen($request->get('dateto')) > 0)
    {
      $dateto = date('Y-m-d', strtotime($request->get('dateto')));
      $tanggal = $tanggal . "and tanggal  <= '" . $dateto . "'";
    }
  }
  // else{
  
  // }

    // if($last){
    //   $tanggal = date_create($last->tanggal);
    //   $now = date_create(date('Y-m-d'));
    //   $interval = $now->diff($tanggal);
    //   $diff = $interval->format('%a%');

    //   if($diff > 30){
    //     $datefrom = date('Y-m-d', strtotime($last->tanggal));
    //   }
    // }

  if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  }

  $data_kategori = db::connection('ympimis_2')->select("
    SELECT
    point_judul,
    sum( CASE WHEN status_ditangani IS NULL THEN 1 ELSE 0 END ) AS jumlah_belum,
    sum( CASE WHEN status_ditangani = 'penjelasan' THEN 1 ELSE 0 END ) AS jumlah_penjelasan,
    sum( CASE WHEN status_ditangani = 'close' THEN 1 ELSE 0 END ) AS jumlah_sudah
    FROM
    hr_lives 
    WHERE
    kategori IN ('live') 
    " . $tanggal . "
    GROUP BY
    point_judul
    ");

  $data_bulan = db::connection('ympimis_2')->select("
    SELECT
    MONTHNAME(tanggal) as bulan,
    year(tanggal) as tahun,
    sum( CASE WHEN status_ditangani IS NULL AND kategori = 'live' THEN 1 ELSE 0 END ) AS jumlah_belum,
    sum( CASE WHEN status_ditangani = 'penjelasan' AND kategori = 'live' THEN 1 ELSE 0 END ) AS jumlah_penjelasan,
    sum( CASE WHEN status_ditangani = 'close' AND kategori = 'live' THEN 1 ELSE 0 END ) AS jumlah_sudah
    FROM
    hr_lives 
    WHERE
    kategori in ('live')
    GROUP BY tahun,monthname(tanggal)
    order by tahun, month(tanggal) ASC
    ");

  $year = date('Y');

  $response = array(
    'status' => true,
    'data_kategori' => $data_kategori,
    'data_bulan' => $data_bulan,
    'year' => $year
  );

  return Response::json($response);
}

public function detailMonitoringLive(Request $request){

  $tgl = date('Y-m-d', strtotime($request->get("tgl")));

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
  }

  if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  }

  $status = $request->get('status');

  if ($status != null) {

    if ($status == "Open") {
      $stat = 'and hr_lives.status_ditangani is null and kategori = "live"';
    }
    if ($status == "Penjelasan") {
      $stat = 'and hr_lives.status_ditangani = "penjelasan" and kategori = "live"';
    }
    else if ($status == "Close") {
      $stat = 'and hr_lives.status_ditangani = "close" and kategori = "live"';
    }
  } else{
    $stat = '';
  }

  $datefrom = $request->get('datefrom');
  $dateto = $request->get('dateto');

  if ($datefrom != null && $dateto != null) {
    $df = 'and hr_lives.tanggal between "'.$datefrom.'" and "'.$dateto.'"';
  }else{
    $df = '';
  }

  $query = "select hr_lives.* FROM hr_lives where hr_lives.deleted_at is null and tanggal = '".$tgl."' ".$stat." and (remark <> 'Positive Finding' OR remark is null)";

  $detail = db::connection('ympimis_2')->select($query);

  return DataTables::of($detail)

  ->editColumn('auditor_name', function($detail){
    $kategori = '';

    if($detail->kategori == "live"){
      $kategori = "HR Live";
    }else{
      $kategori = $detail->kategori;
    }

    $tgl = date('d-M-Y', strtotime($detail->tanggal));

    return 'Patrol '.$kategori.'<br>Auditor '.$detail->auditor_name.'<br>'.$tgl.'<br>Lokasi '.$detail->lokasi;
  })


  ->editColumn('foto', function($detail){
    return $detail->note.'<br><img src="'.url('files/patrol').'/'.$detail->foto.'" width="250">';
  })

  ->editColumn('auditee_name', function($detail){
    return $detail->point_judul.'<br>'.$detail->auditee_name;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->rawColumns(['auditor_name' => 'auditor_name', 'auditee_name' => 'auditee_name', 'foto' => 'foto','penanganan' => 'penanganan'])
  ->make(true);
}


public function fetchTableLive(Request $request)
{

  $datefrom = date("Y-m-d",  strtotime('-60 days'));
  $dateto = date("Y-m-d");

  $last = db::connection('ympimis_2')->table('hr_lives')
  ->whereNull('status_ditangani')
  ->orderBy('tanggal', 'asc')
  ->select(db::raw('date(tanggal) as audit_date'))
  ->first();

  if(strlen($request->get('datefrom')) > 0){
    $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
  }else{
    if($last){
      $tanggal = date_create($last->audit_date);
      $now = date_create(date('Y-m-d'));
      $interval = $now->diff($tanggal);
      $diff = $interval->format('%a%');

      if($diff > 30){
        $datefrom = date('Y-m-d', strtotime($last->audit_date));
      }
    }
  }

  if(strlen($request->get('dateto')) > 0){
    $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  }

  // $status = $request->get('status');

  // if ($status != null) {
  //   $cat = json_encode($status);
  //   $kat = str_replace(array("[","]"),array("(",")"),$cat);

  //   $kate = 'and hr_lives.status_ditangani in'.$kat;
  // }else{
  //   $kate = 'and hr_lives.status_ditangani is null';
  // }


  $data = db::connection('ympimis_2')->select("select * from hr_lives where hr_lives.deleted_at is null and kategori in ('live') and hr_lives.status_ditangani is null and tanggal between '".$datefrom."' and '".$dateto."' ");

  $data_close = db::connection('ympimis_2')->select("select * from hr_lives where hr_lives.deleted_at is null and kategori in ('live') and hr_lives.status_ditangani = 'close' and tanggal between '".$datefrom."' and '".$dateto."' ");

  $data_penjelasan = db::connection('ympimis_2')->select("select * from hr_lives where hr_lives.deleted_at is null and kategori in ('live') and hr_lives.status_ditangani = 'penjelasan' and tanggal between '".$datefrom."' and '".$dateto."' ");

  $response = array(
    'status' => true,
    'datas' => $data,
    'data_close' => $data_close,
    'data_penjelasan' => $data_penjelasan
  );

  return Response::json($response); 
}

public function postLive(Request $request)
{
  try {
    $documentation = db::connection('ympimis_2')
    ->table('hr_lives')
    ->insert([
      'jenis' => 'Patrol',
      'tanggal' => date('Y-m-d'),
      'kategori' => 'live',
      'lokasi' => $request->input('lokasi'),
      'point_judul' => $request->input('point_judul'),
      'note' => $request->input('note'),
      'created_by' => Auth::id(),
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ]);

    $response = array(
      'status' => true,
      'message' => 'Data LIVE Berhasil Ditambahkan',
    );
    return Response::json($response);
  } catch (\Exception$e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage(),
    );
    return Response::json($response);
  }
}


public function detailMonitoringLiveCategory(Request $request){

  $kategori = $request->get('kategori');
  $status = $request->get('status');

  if ($status != null) {

    if ($status == "Belum Ditangani") {
      $stat = 'and hr_lives.status_ditangani is null';
    }
    else if ($status == "Sudah Dijelaskan"){
      $stat = 'and hr_lives.status_ditangani = "penjelasan"';
    }
    else if ($status == "Sudah Ditangani"){
      $stat = 'and hr_lives.status_ditangani = "close"';
    }

  } else{
    $stat = '';
  }

  $query = "select hr_lives.* FROM hr_lives where hr_lives.deleted_at is null and point_judul = '".$kategori."' ".$stat."";

  $detail = db::connection('ympimis_2')->select($query);

  return DataTables::of($detail)

  ->editColumn('tanggal', function($detail){
    $kategori = '';

    $tgl = date('d-M-Y', strtotime($detail->tanggal));

    return $tgl;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->addColumn('status', function($detail){

    if ($detail->status_ditangani == null)
    {
      return '<label class="label label-danger">Open</label>';
    }
    else if($detail->status_ditangani == "penjelasan"){
      return '<label class="label label-info">Sudah Diberi Penjelasan</label>';
    }
    else if($detail->status_ditangani == "close"){
      return '<label class="label label-success">Close</label>';
    }
  })

  ->rawColumns(['tanggal' => 'tanggal','penanganan' => 'penanganan','status' => 'status'])
  ->make(true);
}


public function detailMonitoringLiveBulan(Request $request){

  $bulan = $request->get('bulan');
  $status = $request->get('status');

  if ($status != null) {
    if ($status == "Belum Ditangani") {
      $stat = 'and hr_lives.status_ditangani is null';
    }
    else if ($status == "Sudah Dijelaskan"){
      $stat = 'and hr_lives.status_ditangani = "penjelasan"';
    }
    else if ($status == "Sudah Ditangani"){
      $stat = 'and hr_lives.status_ditangani = "close"';
    }
  } else{
    $stat = '';
  }

  $query = "select hr_lives.* FROM hr_lives where hr_lives.deleted_at is null and monthname(tanggal) = '".$bulan."' ".$stat." ";

  $detail = db::connection('ympimis_2')->select($query);

  return DataTables::of($detail)

  ->editColumn('tanggal', function($detail){
    $kategori = '';

    $tgl = date('d-M-Y', strtotime($detail->tanggal));

    return $tgl;
  })

  ->editColumn('penanganan', function($detail){

    $bukti = "";

    if ($detail->bukti_penanganan != null) {
      $bukti = '<br><img src="'.url('files/patrol').'/'.$detail->bukti_penanganan.'" width="250">';
    }else{
      $bukti = "";
    }

    return $detail->penanganan.''.$bukti;
  })

  ->addColumn('status', function($detail){

    if ($detail->status_ditangani == null)
    {
      return '<label class="label label-danger">Open</label>';
    }
    else if($detail->status_ditangani == "penjelasan"){
      return '<label class="label label-info">Sudah Diberi Penjelasan</label>';
    }
    else if($detail->status_ditangani == "close"){
      return '<label class="label label-success">Close</label>';
    }
  })

  ->rawColumns(['tanggal' => 'tanggal','penanganan' => 'penanganan', 'status' => 'status'])
  ->make(true);
}

public function detailPenangananLive(Request $request){
  $audit = db::connection('ympimis_2')->select("SELECT * from hr_lives where id = ". $request->get('id'));

  $response = array(
   'status' => true,
   'audit' => $audit,
   'location' => $this->location
 );
  return Response::json($response);
}


public function postPenangananLive(Request $request)
{
  try{
    $id_user = Auth::id();
    $tujuan_upload = 'files/patrol';
    $filename = null;

    if (count($request->file('bukti_penanganan')) > 0) {
      $file = $request->file('bukti_penanganan');
      $nama = $file->getClientOriginalName();
      $filename = pathinfo($nama, PATHINFO_FILENAME);
      $extension = pathinfo($nama, PATHINFO_EXTENSION);
      $filename = md5($filename.date('YmdHisa')).'.'.$extension;
      $file->move($tujuan_upload,$filename);
    }

    // $audit = db::connection('ympimis_2')
    // ->table('hr_lives')
    // ->where('id','=',$request->input("id"))
    // ->first();


    $audit = db::connection('ympimis_2')
    ->table('hr_lives')
    ->where('id', '=', $request->input('id'))
    ->update([
      'penanganan' => $request->input('penanganan'),
      'bukti_penanganan' => $filename,
      'tanggal_penanganan' => date('Y-m-d'),
      'status_ditangani' => $request->input('btn_status')
    ]);

    $response = array(
      'status' => true,
    );
    return Response::json($response);
  }
  catch (QueryException $e){
    $error_code = $e->errorInfo[1];
    if($error_code == 1062){
     $response = array(
      'status' => false,
      'datas' => "Audit Already Exist",
    );
     return Response::json($response);
   }
   else{
     $response = array(
      'status' => false,
      'datas' => $e->getMessage(),
    );
     return Response::json($response);
   }
 }
}


}
