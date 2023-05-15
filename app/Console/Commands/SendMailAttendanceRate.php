<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class SendMailAttendanceRate extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'email:attendance';

/**
* The console command description.
*
* @var string
*/
protected $description = 'Command description';

/**
* Create a new command instance.
*
* @return void
*/
public function __construct()
{
    parent::__construct();
}

public function handle()
{
    $date = date('Y-m-d');
    $now = date('Y-m-d',(strtotime ( '-1 day' , strtotime ( $date) ) ));

    $yesterday = date('w',(strtotime ( '-1 day' , strtotime ( $date) ) ));

    $tgl = date('d F Y',(strtotime ( '-1 day' , strtotime ( $date) ) ));

    if ($yesterday == '0') {
        $now = date('Y-m-d',(strtotime ( '-3 day' , strtotime ( $date) ) ));
        $tgl = date('d F Y',(strtotime ( '-3 day' , strtotime ( $date) ) ));
    }

    $total_shift_1 = 0;
    $total_shift_2 = 0;
    $total_shift_3 = 0;
    $total_off = 0;

    $attendances = db::connection('sunfish')->select("SELECT
        a.emp_no,
        a.shiftdaily_code,
        e.employ_code,
        e.grade_code,
        IIF(a.shiftdaily_code like '%shift_1%', 'shift_1', IIF(a.shiftdaily_code like '%shift_2%', 'shift_2', IIF(a.shiftdaily_code like '%shift_3%', 'shift_3', IIF(a.shiftdaily_code like '%off%', 'off', 'shift_1')))) as shift,
        Attend_Code,
        SUM(IIF( a.remark LIKE '%isoman%', 1, 0 ) ) AS isoman,

        SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 1, 0))) AS covid,

        SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 1, 0)))) AS sakit,

        0 as izin,

        SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 1, 0))))) +

        SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 0, IIF( (a.Attend_Code like '%CK%') or (a.Attend_Code like '%CUTI%') or (a.Attend_Code like '%UPL%'), 1, 0)))))) AS cuti,

        SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 0, IIF( (a.Attend_Code like '%CK%') or (a.Attend_Code like '%CUTI%') or (a.Attend_Code like '%UPL%'), 0, IIF( a.Attend_Code like '%WFH%', 1, 0))))))) AS wfh,

        SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 0, IIF( (a.Attend_Code like '%CK%') or (a.Attend_Code like '%CUTI%') or (a.Attend_Code like '%UPL%'), 0, IIF( a.Attend_Code like '%WFH%', 0, IIF( a.Attend_Code like '%PRS%', 1, 0)))))))) AS hadir,

        SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 0, IIF( (a.Attend_Code like '%CK%') or (a.Attend_Code like '%CUTI%') or (a.Attend_Code like '%UPL%'), 0, IIF( a.Attend_Code like '%WFH%', 0, IIF( a.Attend_Code like '%PRS%', 0, IIF( a.Attend_Code like '%ABS%', 1, 0))))))))) AS absen,

        SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 0, IIF( (a.Attend_Code like '%CK%') or (a.Attend_Code like '%CUTI%') or (a.Attend_Code like '%UPL%'), 0, IIF( a.Attend_Code like '%WFH%', 0, IIF( a.Attend_Code like '%PRS%', 0, IIF( a.Attend_Code like '%ABS%', 0, 1))))))))) AS libur

        FROM
        VIEW_YMPI_ATTENDANCE AS a
        LEFT JOIN VIEW_YMPI_Emp_OrgUnit AS e ON a.emp_no = e.Emp_no
        WHERE
        format ( a.shiftstarttime, 'yyyy-MM-dd' ) = '".$now."'
        AND (e.end_date IS NULL or e.end_date >= '".$now."')
        GROUP BY
        a.emp_no,
        a.shiftdaily_code,
        a.Attend_Code,
        e.employ_code,
        e.grade_code");

    $employees = db::select("SELECT * from employees");

    $result1 = array();

    foreach($attendances as $attendance){
        $location = "production";

        foreach($employees as $employee){
            if($employee->employee_id == $attendance->emp_no){
                if(substr($employee->employee_id, 0, 2) == 'OS'){
                    $location = "outsource";
                }
                else if($employee->remark == 'OFC' || $employee->remark == 'Jps'){
                    $location = "office";
                }
            }
        }

        array_push($result1, [
            'location' => $location,
            'employee_id' => $attendance->emp_no,
            'shiftdaily_code' => $attendance->shiftdaily_code,
            'shift' => $attendance->shift,
            'attend_code' => $attendance->Attend_Code,
            'isoman' => $attendance->isoman,
            'covid' => $attendance->covid,
            'sakit' => $attendance->sakit,
            'izin' => $attendance->izin,
            'cuti' => $attendance->cuti,
            'wfh' => $attendance->wfh,
            'hadir' => $attendance->hadir,
            'libur' => $attendance->libur,
            'absen' => $attendance->absen
        ]);
    }

    $result2 = array();

    foreach($result1 as $row){
        $key = $row['location'].$row['shift'];
        if (!array_key_exists($key, $result2)) {
            $result2[$key] = array(
                'location' => $row['location'],
                'shift' => $row['shift'],
                'isoman' => $row['isoman'],
                'covid' => $row['covid'],
                'sakit' => $row['sakit'],
                'izin' => $row['izin'],
                'cuti' => $row['cuti'],
                'wfh' => $row['wfh'],
                'hadir' => $row['hadir'],
                'libur' => $row['libur'],
                'absen' => $row['absen'],
                'total' => $row['isoman']+$row['covid']+$row['sakit']+$row['izin']+$row['cuti']+$row['wfh']+$row['hadir']+$row['libur']+$row['absen']
            );
        }
        else{
            $result2[$key]['isoman'] = $result2[$key]['isoman']+$row['isoman'];
            $result2[$key]['covid'] = $result2[$key]['covid']+$row['covid'];
            $result2[$key]['sakit'] = $result2[$key]['sakit']+$row['sakit'];
            $result2[$key]['izin'] = $result2[$key]['izin']+$row['izin'];
            $result2[$key]['cuti'] = $result2[$key]['cuti']+$row['cuti'];
            $result2[$key]['wfh'] = $result2[$key]['wfh']+$row['wfh'];
            $result2[$key]['hadir'] = $result2[$key]['hadir']+$row['hadir'];
            $result2[$key]['libur'] = $result2[$key]['libur']+$row['libur'];
            $result2[$key]['absen'] = $result2[$key]['absen']+$row['absen'];
            $result2[$key]['total'] = $result2[$key]['total']+$row['covid']+$row['sakit']+$row['izin']+$row['cuti']+$row['wfh']+$row['hadir']+$row['libur']+$row['absen'];
        }
    }

    $data = array(
        'result' => $result2,
        'attendances' => $attendances,
        'now' => $tgl
    );

    $mail_to = ['ympi-japanese-ML@music.yamaha.com', 'ympi-manager-ML@music.yamaha.com'];

    $bcc = [
        'ympi-mis-ML@music.yamaha.com',
        'mahendra.putra@music.yamaha.com',
        'dicky.kurniawan@music.yamaha.com',
        'achmad.riski.bayu@music.yamaha.com',
    ];

    Mail::to($mail_to)->bcc($bcc)->send(new SendEmail($data, 'daily_attendance'));
}
}
