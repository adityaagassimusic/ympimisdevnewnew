<!DOCTYPE html>
<html>

<head>
    <style type="text/css">
    </style>
</head>

<body>
    <div style="width: 100%">
        <center>
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('mirai.jpg'))) }}"
                alt="">
            <br>
            <span style="font-size: 24px">YMPI Daily Attendance Summary on {{ $data['now'] }}</span>
            <br>
            <span style="font-size: 24px">YMPI日常出勤まとめ 日付: {{ $data['now'] }}</span>
        </center>
        <br>
        @php
            $total_shift_1 = 0;
            $total_shift_2 = 0;
            $total_shift_3 = 0;
            $total_off = 0;
            
            $cuti_tidak_masuk_shift_1 = 0;
            $cuti_tidak_masuk_shift_2 = 0;
            $cuti_tidak_masuk_shift_3 = 0;
            $cuti_tidak_masuk_off = 0;
            
            $sakit_tidak_masuk_shift_1 = 0;
            $sakit_tidak_masuk_shift_2 = 0;
            $sakit_tidak_masuk_shift_3 = 0;
            $sakit_tidak_masuk_off = 0;
            
            $isoman_tidak_masuk_shift_1 = 0;
            $isoman_tidak_masuk_shift_2 = 0;
            $isoman_tidak_masuk_shift_3 = 0;
            $isoman_tidak_masuk_off = 0;
            
            $covid_tidak_masuk_shift_1 = 0;
            $covid_tidak_masuk_shift_2 = 0;
            $covid_tidak_masuk_shift_3 = 0;
            $covid_tidak_masuk_off = 0;
            
            $libur_tidak_masuk_shift_1 = 0;
            $libur_tidak_masuk_shift_2 = 0;
            $libur_tidak_masuk_shift_3 = 0;
            $libur_tidak_masuk_off = 0;
            
            $absen_tidak_masuk_shift_1 = 0;
            $absen_tidak_masuk_shift_2 = 0;
            $absen_tidak_masuk_shift_3 = 0;
            $absen_tidak_masuk_off = 0;
            
            $tidak_masuk_shift_1 = 0;
            $tidak_masuk_shift_2 = 0;
            $tidak_masuk_shift_3 = 0;
            $tidak_masuk_off = 0;
            
            $ofc_total_wfo = 0;
            $ofc_total_wfh = 0;
            $ofc_total_cuti = 0;
            $ofc_total_sakit = 0;
            $ofc_total_covid = 0;
            $ofc_total_isoman = 0;
            $ofc_total_libur = 0;
            $ofc_total_absen = 0;
            $ofc_total = 0;
            
            $ofc_shift_1_wfo = 0;
            $ofc_shift_1_wfh = 0;
            $ofc_shift_1_cuti = 0;
            $ofc_shift_1_sakit = 0;
            $ofc_shift_1_covid = 0;
            $ofc_shift_1_isoman = 0;
            $ofc_shift_1_libur = 0;
            $ofc_shift_1_absen = 0;
            $ofc_shift_1_total = 0;
            
            $ofc_shift_2_wfo = 0;
            $ofc_shift_2_wfh = 0;
            $ofc_shift_2_cuti = 0;
            $ofc_shift_2_sakit = 0;
            $ofc_shift_2_covid = 0;
            $ofc_shift_2_isoman = 0;
            $ofc_shift_2_libur = 0;
            $ofc_shift_2_absen = 0;
            $ofc_shift_2_total = 0;
            
            $ofc_shift_3_wfo = 0;
            $ofc_shift_3_wfh = 0;
            $ofc_shift_3_cuti = 0;
            $ofc_shift_3_sakit = 0;
            $ofc_shift_3_covid = 0;
            $ofc_shift_3_isoman = 0;
            $ofc_shift_3_libur = 0;
            $ofc_shift_3_absen = 0;
            $ofc_shift_3_total = 0;
            
            $ofc_off_wfo = 0;
            $ofc_off_wfh = 0;
            $ofc_off_cuti = 0;
            $ofc_off_sakit = 0;
            $ofc_off_covid = 0;
            $ofc_off_isoman = 0;
            $ofc_off_libur = 0;
            $ofc_off_absen = 0;
            $ofc_off_total = 0;
            
            $prd_total_wfo = 0;
            $prd_total_wfh = 0;
            $prd_total_cuti = 0;
            $prd_total_sakit = 0;
            $prd_total_covid = 0;
            $prd_total_isoman = 0;
            $prd_total_libur = 0;
            $prd_total_absen = 0;
            $prd_total = 0;
            
            $prd_shift_1_wfo = 0;
            $prd_shift_1_wfh = 0;
            $prd_shift_1_cuti = 0;
            $prd_shift_1_sakit = 0;
            $prd_shift_1_covid = 0;
            $prd_shift_1_isoman = 0;
            $prd_shift_1_libur = 0;
            $prd_shift_1_absen = 0;
            $prd_shift_1_total = 0;
            
            $prd_shift_2_wfo = 0;
            $prd_shift_2_wfh = 0;
            $prd_shift_2_cuti = 0;
            $prd_shift_2_sakit = 0;
            $prd_shift_2_covid = 0;
            $prd_shift_2_isoman = 0;
            $prd_shift_2_libur = 0;
            $prd_shift_2_absen = 0;
            $prd_shift_2_total = 0;
            
            $prd_shift_3_wfo = 0;
            $prd_shift_3_wfh = 0;
            $prd_shift_3_cuti = 0;
            $prd_shift_3_sakit = 0;
            $prd_shift_3_covid = 0;
            $prd_shift_3_isoman = 0;
            $prd_shift_3_libur = 0;
            $prd_shift_3_absen = 0;
            $prd_shift_3_total = 0;
            
            $prd_off_wfo = 0;
            $prd_off_wfh = 0;
            $prd_off_cuti = 0;
            $prd_off_sakit = 0;
            $prd_off_covid = 0;
            $prd_off_isoman = 0;
            $prd_off_libur = 0;
            $prd_off_absen = 0;
            $prd_off_total = 0;
            
            $total_japanese = 0;
            $total_tetap = 0;
            $total_kontrak = 0;
            $total = 0;
            
            foreach ($data['attendances'] as $row) {
                if ($row->grade_code == 'J0-') {
                    $total_japanese += 1;
                } elseif ($row->employ_code == 'PERMANENT') {
                    $total_tetap += 1;
                } else {
                    $total_kontrak += 1;
                }
            }
            
            foreach ($data['result'] as $row) {
                if ($row['location'] == 'office') {
                    $ofc_total_wfo += $row['hadir'];
                    $ofc_total_wfh += $row['wfh'];
                    $ofc_total_cuti += $row['cuti'] + $row['izin'];
                    $ofc_total_sakit += $row['sakit'];
                    $ofc_total_covid += $row['covid'];
                    $ofc_total_isoman += $row['isoman'];
                    $ofc_total_libur += $row['libur'];
                    $ofc_total_absen += $row['absen'];
                    $ofc_total += $row['hadir'] + $row['wfh'] + $row['cuti'] + $row['izin'] + $row['sakit'] + $row['covid'] + $row['isoman'] + $row['libur'] + $row['absen'];
            
                    if ($row['shift'] == 'shift_1') {
                        $ofc_shift_1_wfo += $row['hadir'];
                        $ofc_shift_1_wfh += $row['wfh'];
                        $ofc_shift_1_cuti += $row['cuti'] + $row['izin'];
                        $ofc_shift_1_sakit += $row['sakit'];
                        $ofc_shift_1_covid += $row['covid'];
                        $ofc_shift_1_isoman += $row['isoman'];
                        $ofc_shift_1_libur += $row['libur'];
                        $ofc_shift_1_absen += $row['absen'];
            
                        $ofc_shift_1_total += $row['hadir'] + $row['wfh'] + $row['cuti'] + $row['izin'] + $row['sakit'] + $row['covid'] + $row['isoman'] + $row['libur'] + $row['absen'];
                    }
                    if ($row['shift'] == 'shift_2') {
                        $ofc_shift_2_wfo += $row['hadir'];
                        $ofc_shift_2_wfh += $row['wfh'];
                        $ofc_shift_2_cuti += $row['cuti'] + $row['izin'];
                        $ofc_shift_2_sakit += $row['sakit'];
                        $ofc_shift_2_covid += $row['covid'];
                        $ofc_shift_2_isoman += $row['isoman'];
                        $ofc_shift_2_libur += $row['libur'];
                        $ofc_shift_2_absen += $row['absen'];
            
                        $ofc_shift_2_total += $row['hadir'] + $row['wfh'] + $row['cuti'] + $row['izin'] + $row['sakit'] + $row['covid'] + $row['isoman'] + $row['libur'] + $row['absen'];
                    }
                    if ($row['shift'] == 'shift_3') {
                        $ofc_shift_3_wfo += $row['hadir'];
                        $ofc_shift_3_wfh += $row['wfh'];
                        $ofc_shift_3_cuti += $row['cuti'] + $row['izin'];
                        $ofc_shift_3_sakit += $row['sakit'];
                        $ofc_shift_3_covid += $row['covid'];
                        $ofc_shift_3_isoman += $row['isoman'];
                        $ofc_shift_3_libur += $row['libur'];
                        $ofc_shift_3_absen += $row['absen'];
            
                        $ofc_shift_3_total += $row['hadir'] + $row['wfh'] + $row['cuti'] + $row['izin'] + $row['sakit'] + $row['covid'] + $row['isoman'] + $row['libur'] + $row['absen'];
                    }
                    if ($row['shift'] == 'off') {
                        $ofc_off_wfo += $row['hadir'];
                        $ofc_off_wfh += $row['wfh'];
                        $ofc_off_cuti += $row['cuti'] + $row['izin'];
                        $ofc_off_sakit += $row['sakit'];
                        $ofc_off_covid += $row['covid'];
                        $ofc_off_isoman += $row['isoman'];
                        $ofc_off_libur += $row['libur'];
                        $ofc_off_absen += $row['absen'];
            
                        $ofc_off_total += $row['hadir'] + $row['wfh'] + $row['cuti'] + $row['izin'] + $row['sakit'] + $row['covid'] + $row['isoman'] + $row['libur'] + $row['absen'];
                    }
                }
                if ($row['location'] == 'production') {
                    $prd_total_wfo += $row['hadir'];
                    $prd_total_wfh += $row['wfh'];
                    $prd_total_cuti += $row['cuti'] + $row['izin'];
                    $prd_total_sakit += $row['sakit'];
                    $prd_total_covid += $row['covid'];
                    $prd_total_isoman += $row['isoman'];
                    $prd_total_libur += $row['libur'];
                    $prd_total_absen += $row['absen'];
                    $prd_total += $row['hadir'] + $row['wfh'] + $row['cuti'] + $row['izin'] + $row['sakit'] + $row['covid'] + $row['isoman'] + $row['libur'] + $row['absen'];
            
                    if ($row['shift'] == 'shift_1') {
                        $prd_shift_1_wfo += $row['hadir'];
                        $prd_shift_1_wfh += $row['wfh'];
                        $prd_shift_1_cuti += $row['cuti'] + $row['izin'];
                        $prd_shift_1_sakit += $row['sakit'];
                        $prd_shift_1_covid += $row['covid'];
                        $prd_shift_1_isoman += $row['isoman'];
                        $prd_shift_1_libur += $row['libur'];
                        $prd_shift_1_absen += $row['absen'];
            
                        $prd_shift_1_total += $row['hadir'] + $row['wfh'] + $row['cuti'] + $row['izin'] + $row['sakit'] + $row['covid'] + $row['isoman'] + $row['libur'] + $row['absen'];
                    }
                    if ($row['shift'] == 'shift_2') {
                        $prd_shift_2_wfo += $row['hadir'];
                        $prd_shift_2_wfh += $row['wfh'];
                        $prd_shift_2_cuti += $row['cuti'] + $row['izin'];
                        $prd_shift_2_sakit += $row['sakit'];
                        $prd_shift_2_covid += $row['covid'];
                        $prd_shift_2_isoman += $row['isoman'];
                        $prd_shift_2_libur += $row['libur'];
                        $prd_shift_2_absen += $row['absen'];
            
                        $prd_shift_2_total += $row['hadir'] + $row['wfh'] + $row['cuti'] + $row['izin'] + $row['sakit'] + $row['covid'] + $row['isoman'] + $row['libur'] + $row['absen'];
                    }
                    if ($row['shift'] == 'shift_3') {
                        $prd_shift_3_wfo += $row['hadir'];
                        $prd_shift_3_wfh += $row['wfh'];
                        $prd_shift_3_cuti += $row['cuti'] + $row['izin'];
                        $prd_shift_3_sakit += $row['sakit'];
                        $prd_shift_3_covid += $row['covid'];
                        $prd_shift_3_isoman += $row['isoman'];
                        $prd_shift_3_libur += $row['libur'];
                        $prd_shift_3_absen += $row['absen'];
            
                        $prd_shift_3_total += $row['hadir'] + $row['wfh'] + $row['cuti'] + $row['izin'] + $row['sakit'] + $row['covid'] + $row['isoman'] + $row['libur'] + $row['absen'];
                    }
                    if ($row['shift'] == 'off') {
                        $prd_off_wfo += $row['hadir'];
                        $prd_off_wfh += $row['wfh'];
                        $prd_off_cuti += $row['cuti'] + $row['izin'];
                        $prd_off_sakit += $row['sakit'];
                        $prd_off_covid += $row['covid'];
                        $prd_off_isoman += $row['isoman'];
                        $prd_off_libur += $row['libur'];
                        $prd_off_absen += $row['absen'];
            
                        $prd_off_total += $row['hadir'] + $row['wfh'] + $row['cuti'] + $row['izin'] + $row['sakit'] + $row['covid'] + $row['isoman'] + $row['libur'] + $row['absen'];
                    }
                }
            
                if ($row['shift'] == 'shift_1') {
                    $total_shift_1 += $row['isoman'] + $row['covid'] + $row['sakit'] + $row['izin'] + $row['cuti'] + $row['wfh'] + $row['hadir'] + $row['libur'] + $row['absen'];
            
                    $cuti_tidak_masuk_shift_1 += $row['izin'] + $row['cuti'];
                    $sakit_tidak_masuk_shift_1 += $row['sakit'];
                    $isoman_tidak_masuk_shift_1 += $row['isoman'];
                    $covid_tidak_masuk_shift_1 += $row['covid'];
                    $libur_tidak_masuk_shift_1 += $row['libur'];
                    $absen_tidak_masuk_shift_1 += $row['absen'];
            
                    $tidak_masuk_shift_1 += $row['isoman'] + $row['covid'] + $row['sakit'] + $row['izin'] + $row['cuti'] + $row['libur'] + $row['absen'];
                }
                if ($row['shift'] == 'shift_2') {
                    $total_shift_2 += $row['isoman'] + $row['covid'] + $row['sakit'] + $row['izin'] + $row['cuti'] + $row['wfh'] + $row['hadir'] + $row['libur'] + $row['absen'];
            
                    $cuti_tidak_masuk_shift_2 += $row['izin'] + $row['cuti'];
                    $sakit_tidak_masuk_shift_2 += $row['sakit'];
                    $isoman_tidak_masuk_shift_2 += $row['isoman'];
                    $covid_tidak_masuk_shift_2 += $row['covid'];
                    $libur_tidak_masuk_shift_2 += $row['libur'];
                    $absen_tidak_masuk_shift_2 += $row['absen'];
            
                    $tidak_masuk_shift_2 += $row['isoman'] + $row['covid'] + $row['sakit'] + $row['izin'] + $row['cuti'] + $row['libur'] + $row['absen'];
                }
                if ($row['shift'] == 'shift_3') {
                    $total_shift_3 += $row['isoman'] + $row['covid'] + $row['sakit'] + $row['izin'] + $row['cuti'] + $row['wfh'] + $row['hadir'] + $row['libur'] + $row['absen'];
            
                    $cuti_tidak_masuk_shift_3 += $row['izin'] + $row['cuti'];
                    $sakit_tidak_masuk_shift_3 += $row['sakit'];
                    $isoman_tidak_masuk_shift_3 += $row['isoman'];
                    $covid_tidak_masuk_shift_3 += $row['covid'];
                    $libur_tidak_masuk_shift_3 += $row['libur'];
                    $absen_tidak_masuk_shift_3 += $row['absen'];
            
                    $tidak_masuk_shift_3 += $row['isoman'] + $row['covid'] + $row['sakit'] + $row['izin'] + $row['cuti'] + $row['libur'] + $row['absen'];
                }
                if ($row['shift'] == 'off') {
                    $total_off += $row['isoman'] + $row['covid'] + $row['sakit'] + $row['izin'] + $row['cuti'] + $row['wfh'] + $row['hadir'] + $row['libur'] + $row['absen'];
            
                    $cuti_tidak_masuk_off += $row['izin'] + $row['cuti'];
                    $sakit_tidak_masuk_off += $row['sakit'];
                    $isoman_tidak_masuk_off += $row['isoman'];
                    $covid_tidak_masuk_off += $row['covid'];
                    $libur_tidak_masuk_off += $row['libur'];
                    $absen_tidak_masuk_off += $row['absen'];
            
                    $tidak_masuk_off += $row['isoman'] + $row['covid'] + $row['sakit'] + $row['izin'] + $row['cuti'] + $row['libur'] + $row['absen'];
                }
            }
            
            $total = $total_shift_1 + $total_shift_2 + $total_shift_3 + $total_off;
            $total_tidak_masuk = $cuti_tidak_masuk_shift_1 + $sakit_tidak_masuk_shift_1 + $isoman_tidak_masuk_shift_1 + $covid_tidak_masuk_shift_1 + $cuti_tidak_masuk_shift_2 + $sakit_tidak_masuk_shift_2 + $isoman_tidak_masuk_shift_2 + $covid_tidak_masuk_shift_2 + $cuti_tidak_masuk_shift_3 + $sakit_tidak_masuk_shift_3 + $isoman_tidak_masuk_shift_3 + $covid_tidak_masuk_shift_3 + $cuti_tidak_masuk_off + $sakit_tidak_masuk_off + $isoman_tidak_masuk_off + $covid_tidak_masuk_off + $libur_tidak_masuk_shift_1 + $libur_tidak_masuk_shift_2 + $libur_tidak_masuk_shift_3 + $libur_tidak_masuk_off + $absen_tidak_masuk_shift_1 + $absen_tidak_masuk_shift_2 + $absen_tidak_masuk_shift_3 + $absen_tidak_masuk_off;
            
        @endphp
        <table style="width: 100%;">
            <tr>
                <td style="width: 60%;">
                    <table style="border-collapse: collapse;">
                        <tbody>
                            <tr>
                                <td style="width: 60%; font-weight: bold;">WFO Total (工場出勤)</td>
                                <td style="width: 0.1%; font-weight: bold;">:</td>
                                <td style="width: 20%; text-align: right; font-weight: bold;">
                                    {{ $ofc_total_wfo + $prd_total_wfo }}
                                    ({{ round((($ofc_total_wfo + $prd_total_wfo) / $total) * 100, 1) }}%)</td>
                            </tr>
                            <tr>
                                <td style="width: 60%; font-weight: bold;">WFO Office (事務系の出勤比率)</td>
                                <td style="width: 0.1%; font-weight: bold;">:</td>
                                <td style="width: 20%; text-align: right; font-weight: bold;">{{ $ofc_total_wfo }}
                                    ({{ round(($ofc_total_wfo / ($ofc_total_wfo + $ofc_total_wfh + $ofc_total_cuti + $ofc_total_sakit + $ofc_total_covid + $ofc_total_isoman + $ofc_total_libur + $ofc_total_absen)) * 100, 1) }}%)
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 60%; font-weight: bold;">WFO Production (生産系の出勤比率)</td>
                                <td style="width: 0.1%; font-weight: bold;">:</td>
                                <td style="width: 20%; text-align: right; font-weight: bold;">{{ $prd_total_wfo }}
                                    ({{ round(($prd_total_wfo / ($prd_total_wfo + $prd_total_wfh + $prd_total_cuti + $prd_total_sakit + $prd_total_covid + $prd_total_isoman + $prd_total_libur + $prd_total_absen)) * 100, 1) }}%)
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 60%;">WFO Production Shift 1</td>
                                <td style="width: 0.1%;">:</td>
                                <td style="width: 20%; text-align: right;">{{ $prd_shift_1_wfo }}
                                    ({{ round(($prd_shift_1_wfo / ($prd_total_wfo + $prd_total_wfh + $prd_total_cuti + $prd_total_sakit + $prd_total_covid + $prd_total_isoman + $prd_total_libur + $prd_total_absen)) * 100, 1) }}%)
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 60%;">WFO Production Shift 2</td>
                                <td style="width: 0.1%;">:</td>
                                <td style="width: 20%; text-align: right;">{{ $prd_shift_2_wfo }}
                                    ({{ round(($prd_shift_2_wfo / ($prd_total_wfo + $prd_total_wfh + $prd_total_cuti + $prd_total_sakit + $prd_total_covid + $prd_total_isoman + $prd_total_libur + $prd_total_absen)) * 100, 1) }}%)
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 60%;">WFO Production Shift 3</td>
                                <td style="width: 0.1%;">:</td>
                                <td style="width: 20%; text-align: right;">{{ $prd_shift_3_wfo }}
                                    ({{ round(($prd_shift_3_wfo / ($prd_total_wfo + $prd_total_wfh + $prd_total_cuti + $prd_total_sakit + $prd_total_covid + $prd_total_isoman + $prd_total_libur + $prd_total_absen)) * 100, 1) }}%)
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 60%; font-weight: bold;">Masuk WFH/SBH (自宅待機)</td>
                                <td style="width: 0.1%; font-weight: bold;">:</td>
                                <td style="width: 20%; text-align: right; font-weight: bold;">
                                    {{ $ofc_total_wfh + $prd_total_wfh }}
                                    ({{ round((($ofc_total_wfh + $prd_total_wfh) / $total) * 100, 1) }}%)</td>
                            </tr>
                            <tr>
                                <td style="width: 60%; font-weight: bold;">Tidak Masuk (欠勤)</td>
                                <td style="width: 0.1%; font-weight: bold;">:</td>
                                <td style="width: 20%; text-align: right; font-weight: bold;">{{ $total_tidak_masuk }}
                                    ({{ round(($total_tidak_masuk / $total) * 100, 1) }}%)</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
                <td style="width: 10%;">
                </td>
                <td style="width: 30%; vertical-align: top;">
                    <table style="border-collapse: collapse;">
                        <tbody>
                            <tr>
                                <td style="font-weight: bold;" colspan="3">Jumlah Karyawan 従業員人数</td>
                            </tr>
                            <tr>
                                <td style="width: 50%;">Japanese (駐在員)</td>
                                <td style="width: 0.1%;">:</td>
                                <td style="width: 20%; text-align: right;">{{ $total_japanese }} 人</td>
                            </tr>
                            <tr>
                                <td style="width: 50%;">Tetap (正社員)</td>
                                <td style="width: 0.1%;">:</td>
                                <td style="width: 20%; text-align: right;">{{ $total_tetap }} 人</td>
                            </tr>
                            <tr>
                                <td style="width: 50%;">Kontrak (契約社員)</td>
                                <td style="width: 0.1%;">:</td>
                                <td style="width: 20%; text-align: right;">{{ $total_kontrak }} 人</td>
                            </tr>
                            <tr>
                                <td style="width: 50%;">Total (総人数)</td>
                                <td style="width: 0.1%;">:</td>
                                <td style="width: 20%; text-align: right;">
                                    {{ $total_japanese + $total_tetap + $total_kontrak }} 人</td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black;">
            <thead style="background-color: rgb(126,86,134);">
                <tr style="color: white; background-color: #7e5686">
                    <th style="width: 1%; border:1px solid black;" colspan="3">#</th>
                    <th style="width: 0.1%; border:1px solid black;">Shift 1 <br>1直</th>
                    <th style="width: 0.1%; border:1px solid black;">Shift 2 <br>2直</th>
                    <th style="width: 0.1%; border:1px solid black;">Shift 3 <br>3直</th>
                    <th style="width: 0.1%; border:1px solid black;">OFF <br>オフ</th>
                    <th style="width: 0.1%; border:1px solid black;">Total <br>全部</th>
                    <th style="width: 0.1%; border:1px solid black;">Ratio<br>確率</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border: 1px solid black;" rowspan="8">Office<br>事務所</td>
                    <td style="border: 1px solid black;" rowspan="3">Masuk<br>出勤</td>
                    <td style="text-align: left; border: 1px solid black;">WFO 出社</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_1']['hadir']) ? $data['result']['officeshift_1']['hadir'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_2']['hadir']) ? $data['result']['officeshift_2']['hadir'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_3']['hadir']) ? $data['result']['officeshift_3']['hadir'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeoff']['hadir']) ? $data['result']['officeoff']['hadir'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_total_wfo }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($ofc_total_wfo / $total) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;">WFH 在宅勤務</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_1']['wfh']) ? $data['result']['officeshift_1']['wfh'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_2']['wfh']) ? $data['result']['officeshift_2']['wfh'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_3']['wfh']) ? $data['result']['officeshift_3']['wfh'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeoff']['wfh']) ? $data['result']['officeoff']['wfh'] : 0 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_total_wfh }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($ofc_total_wfh / $total) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;">WFO Ratio 出社の比率</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ @round(((isset($data['result']['officeshift_1']['hadir']) ? $data['result']['officeshift_1']['hadir'] : 0) /((isset($data['result']['officeshift_1']['wfh']) ? $data['result']['officeshift_1']['wfh'] : 0) + (isset($data['result']['officeshift_1']['hadir']) ? $data['result']['officeshift_1']['hadir'] : 0) + (isset($data['result']['officeshift_1']['sakit']) ? $data['result']['officeshift_1']['sakit'] : 0) + (isset($data['result']['officeshift_1']['izin']) ? $data['result']['officeshift_1']['izin'] : 0) + (isset($data['result']['officeshift_1']['cuti']) ? $data['result']['officeshift_1']['cuti'] : 0) + (isset($data['result']['officeshift_1']['covid']) ? $data['result']['officeshift_1']['covid'] : 0) + (isset($data['result']['officeshift_1']['isoman']) ? $data['result']['officeshift_1']['isoman'] : 0) + (isset($data['result']['officeshift_1']['libur']) ? $data['result']['officeshift_1']['libur'] : 0) + (isset($data['result']['officeshift_1']['absen']) ? $data['result']['officeshift_1']['absen'] : 0))) *100,1) }}%
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ @round(((isset($data['result']['officeshift_2']['hadir']) ? $data['result']['officeshift_2']['hadir'] : 0) /((isset($data['result']['officeshift_2']['wfh']) ? $data['result']['officeshift_2']['wfh'] : 0) + (isset($data['result']['officeshift_2']['hadir']) ? $data['result']['officeshift_2']['hadir'] : 0) + (isset($data['result']['officeshift_2']['sakit']) ? $data['result']['officeshift_2']['sakit'] : 0) + (isset($data['result']['officeshift_2']['izin']) ? $data['result']['officeshift_2']['izin'] : 0) + (isset($data['result']['officeshift_2']['cuti']) ? $data['result']['officeshift_2']['cuti'] : 0) + (isset($data['result']['officeshift_2']['covid']) ? $data['result']['officeshift_2']['covid'] : 0) + (isset($data['result']['officeshift_2']['isoman']) ? $data['result']['officeshift_2']['isoman'] : 0) + (isset($data['result']['officeshift_2']['libur']) ? $data['result']['officeshift_2']['libur'] : 0) + (isset($data['result']['officeshift_2']['absen']) ? $data['result']['officeshift_2']['absen'] : 0))) *100,1) }}%
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ @round(((isset($data['result']['officeshift_3']['hadir']) ? $data['result']['officeshift_3']['hadir'] : 0) /((isset($data['result']['officeshift_3']['wfh']) ? $data['result']['officeshift_3']['wfh'] : 0) + (isset($data['result']['officeshift_3']['hadir']) ? $data['result']['officeshift_3']['hadir'] : 0) + (isset($data['result']['officeshift_3']['sakit']) ? $data['result']['officeshift_3']['sakit'] : 0) + (isset($data['result']['officeshift_3']['izin']) ? $data['result']['officeshift_3']['izin'] : 0) + (isset($data['result']['officeshift_3']['cuti']) ? $data['result']['officeshift_3']['cuti'] : 0) + (isset($data['result']['officeshift_3']['covid']) ? $data['result']['officeshift_3']['covid'] : 0) + (isset($data['result']['officeshift_3']['isoman']) ? $data['result']['officeshift_3']['isoman'] : 0) + (isset($data['result']['officeshift_3']['libur']) ? $data['result']['officeshift_3']['libur'] : 0) + (isset($data['result']['officeshift_3']['absen']) ? $data['result']['officeshift_3']['absen'] : 0))) *100,1) }}%
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ @round(((isset($data['result']['officeoff']['hadir']) ? $data['result']['officeoff']['hadir'] : 0) / ((isset($data['result']['officeoff']['wfh']) ? $data['result']['officeoff']['wfh'] : 0) + (isset($data['result']['officeoff']['hadir']) ? $data['result']['officeoff']['hadir'] : 0) + (isset($data['result']['officeoff']['sakit']) ? $data['result']['officeoff']['sakit'] : 0) + (isset($data['result']['officeoff']['izin']) ? $data['result']['officeoff']['izin'] : 0) + (isset($data['result']['officeoff']['cuti']) ? $data['result']['officeoff']['cuti'] : 0) + (isset($data['result']['officeoff']['covid']) ? $data['result']['officeoff']['covid'] : 0) + (isset($data['result']['officeoff']['isoman']) ? $data['result']['officeoff']['isoman'] : 0) + (isset($data['result']['officeoff']['libur']) ? $data['result']['officeoff']['libur'] : 0) + (isset($data['result']['officeoff']['absen']) ? $data['result']['officeoff']['absen'] : 0))) * 100, 1) }}%
                    </td>
                    <td style="border: 1px solid black; text-align: center;"></td>
                    <td style="border: 1px solid black; text-align: center;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;" rowspan="4">Tidak Masuk<br>不在</td>
                    <td style="text-align: left; border: 1px solid black;">Izin/Cuti 休暇・有休</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_1']['cuti']) ? $data['result']['officeshift_1']['cuti'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_2']['cuti']) ? $data['result']['officeshift_2']['cuti'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_3']['cuti']) ? $data['result']['officeshift_3']['cuti'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeoff']['cuti']) ? $data['result']['officeoff']['cuti'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_total_cuti }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($ofc_total_cuti / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;">Sakit 病欠</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_1']['sakit']) ? $data['result']['officeshift_1']['sakit'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_2']['sakit']) ? $data['result']['officeshift_2']['sakit'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_3']['sakit']) ? $data['result']['officeshift_3']['sakit'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeoff']['sakit']) ? $data['result']['officeoff']['sakit'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_total_sakit }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($ofc_total_sakit / $total) * 100, 1) }}%</td>
                </tr>
                {{-- <tr>
                    <td style="color: red; text-align: left; border: 1px solid black;">Covid (PCR)<br>コロナ（PCR）</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_1']['covid']) ? $data['result']['officeshift_1']['covid'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_2']['covid']) ? $data['result']['officeshift_2']['covid'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_3']['covid']) ? $data['result']['officeshift_3']['covid'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeoff']['covid']) ? $data['result']['officeoff']['covid'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_total_covid }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($ofc_total_covid / $total) * 100, 1) }}%</td>
                </tr> --}}
                {{-- <tr>
                    <td style="color: red; text-align: left; border: 1px solid black;">Isoman (Non
                        Covid)<br>自主隔離（コロナ以外）</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_1']['isoman']) ? $data['result']['officeshift_1']['isoman'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_2']['isoman']) ? $data['result']['officeshift_2']['isoman'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_3']['isoman']) ? $data['result']['officeshift_3']['isoman'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeoff']['isoman']) ? $data['result']['officeoff']['isoman'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_total_isoman }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($ofc_total_isoman / $total) * 100, 1) }}%</td>
                </tr> --}}
                <tr>
                    <td style="text-align: left; border: 1px solid black;">Libur 休日</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_1']['libur']) ? $data['result']['officeshift_1']['libur'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_2']['libur']) ? $data['result']['officeshift_2']['libur'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_3']['libur']) ? $data['result']['officeshift_3']['libur'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeoff']['libur']) ? $data['result']['officeoff']['libur'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_total_libur }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($ofc_total_libur / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;">Belum Konfirmasi 未確認</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_1']['absen']) ? $data['result']['officeshift_1']['absen'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_2']['absen']) ? $data['result']['officeshift_2']['absen'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeshift_3']['absen']) ? $data['result']['officeshift_3']['absen'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['officeoff']['absen']) ? $data['result']['officeoff']['absen'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_total_absen }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($ofc_total_absen / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;" colspan="2">Total 全部</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_shift_1_total }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_shift_2_total }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_shift_3_total }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_off_total }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $ofc_total }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($ofc_total / $total) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;" rowspan="8">Produksi<br>生産職場</td>
                    <td style="border: 1px solid black;" rowspan="3">Masuk<br>出勤</td>
                    <td style="text-align: left; border: 1px solid black;">WFO 出社</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_1']['hadir']) ? $data['result']['productionshift_1']['hadir'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_2']['hadir']) ? $data['result']['productionshift_2']['hadir'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_3']['hadir']) ? $data['result']['productionshift_3']['hadir'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionoff']['hadir']) ? $data['result']['productionoff']['hadir'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_total_wfo }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($prd_total_wfo / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;">SBH 自宅待機</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_1']['wfh']) ? $data['result']['productionshift_1']['wfh'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_2']['wfh']) ? $data['result']['productionshift_2']['wfh'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_3']['wfh']) ? $data['result']['productionshift_3']['wfh'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionoff']['wfh']) ? $data['result']['productionoff']['wfh'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_total_wfh }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($prd_total_wfh / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;">WFO Ratio 出社の比率</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ @round(((isset($data['result']['productionshift_1']['hadir']) ? $data['result']['productionshift_1']['hadir'] : 0) /((isset($data['result']['productionshift_1']['wfh']) ? $data['result']['productionshift_1']['wfh'] : 0) + (isset($data['result']['productionshift_1']['hadir']) ? $data['result']['productionshift_1']['hadir'] : 0) + (isset($data['result']['productionshift_1']['sakit']) ? $data['result']['productionshift_1']['sakit'] : 0) + (isset($data['result']['productionshift_1']['izin']) ? $data['result']['productionshift_1']['izin'] : 0) + (isset($data['result']['productionshift_1']['cuti']) ? $data['result']['productionshift_1']['cuti'] : 0) + (isset($data['result']['productionshift_1']['covid']) ? $data['result']['productionshift_1']['covid'] : 0) + (isset($data['result']['productionshift_1']['isoman']) ? $data['result']['productionshift_1']['isoman'] : 0) + (isset($data['result']['productionshift_1']['libur']) ? $data['result']['productionshift_1']['libur'] : 0) + (isset($data['result']['productionshift_1']['absen']) ? $data['result']['productionshift_1']['absen'] : 0))) *100,1) }}%
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ @round(((isset($data['result']['productionshift_2']['hadir']) ? $data['result']['productionshift_2']['hadir'] : 0) /((isset($data['result']['productionshift_2']['wfh']) ? $data['result']['productionshift_2']['wfh'] : 0) + (isset($data['result']['productionshift_2']['hadir']) ? $data['result']['productionshift_2']['hadir'] : 0) + (isset($data['result']['productionshift_2']['sakit']) ? $data['result']['productionshift_2']['sakit'] : 0) + (isset($data['result']['productionshift_2']['izin']) ? $data['result']['productionshift_2']['izin'] : 0) + (isset($data['result']['productionshift_2']['cuti']) ? $data['result']['productionshift_2']['cuti'] : 0) + (isset($data['result']['productionshift_2']['covid']) ? $data['result']['productionshift_2']['covid'] : 0) + (isset($data['result']['productionshift_2']['isoman']) ? $data['result']['productionshift_2']['isoman'] : 0) + (isset($data['result']['productionshift_2']['libur']) ? $data['result']['productionshift_2']['libur'] : 0) + (isset($data['result']['productionshift_2']['absen']) ? $data['result']['productionshift_2']['absen'] : 0))) *100,1) }}%
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ @round(((isset($data['result']['productionshift_3']['hadir']) ? $data['result']['productionshift_3']['hadir'] : 0) /((isset($data['result']['productionshift_3']['wfh']) ? $data['result']['productionshift_3']['wfh'] : 0) + (isset($data['result']['productionshift_3']['hadir']) ? $data['result']['productionshift_3']['hadir'] : 0) + (isset($data['result']['productionshift_3']['sakit']) ? $data['result']['productionshift_3']['sakit'] : 0) + (isset($data['result']['productionshift_3']['izin']) ? $data['result']['productionshift_3']['izin'] : 0) + (isset($data['result']['productionshift_3']['cuti']) ? $data['result']['productionshift_3']['cuti'] : 0) + (isset($data['result']['productionshift_3']['covid']) ? $data['result']['productionshift_3']['covid'] : 0) + (isset($data['result']['productionshift_3']['isoman']) ? $data['result']['productionshift_3']['isoman'] : 0) + (isset($data['result']['productionshift_3']['libur']) ? $data['result']['productionshift_3']['libur'] : 0) + (isset($data['result']['productionshift_3']['absen']) ? $data['result']['productionshift_3']['absen'] : 0))) *100,1) }}%
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ @round(((isset($data['result']['productionoff']['hadir']) ? $data['result']['productionoff']['hadir'] : 0) /((isset($data['result']['productionoff']['wfh']) ? $data['result']['productionoff']['wfh'] : 0) + (isset($data['result']['productionoff']['hadir']) ? $data['result']['productionoff']['hadir'] : 0) + (isset($data['result']['productionoff']['sakit']) ? $data['result']['productionoff']['sakit'] : 0) + (isset($data['result']['productionoff']['izin']) ? $data['result']['productionoff']['izin'] : 0) + (isset($data['result']['productionoff']['cuti']) ? $data['result']['productionoff']['cuti'] : 0) + (isset($data['result']['productionoff']['covid']) ? $data['result']['productionoff']['covid'] : 0) + (isset($data['result']['productionoff']['isoman']) ? $data['result']['productionoff']['isoman'] : 0) + (isset($data['result']['productionoff']['libur']) ? $data['result']['productionoff']['libur'] : 0) + (isset($data['result']['productionoff']['absen']) ? $data['result']['productionoff']['absen'] : 0))) *100,1) }}%
                    </td>
                    <td style="border: 1px solid black; text-align: center;"></td>
                    <td style="border: 1px solid black; text-align: center;"></td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;" rowspan="4">Tidak Masuk<br>不在</td>
                    <td style="text-align: left; border: 1px solid black;">Izin/Cuti 休暇・有休</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_1']['cuti']) ? $data['result']['productionshift_1']['cuti'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_2']['cuti']) ? $data['result']['productionshift_2']['cuti'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_3']['cuti']) ? $data['result']['productionshift_3']['cuti'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionoff']['cuti']) ? $data['result']['productionoff']['cuti'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_total_cuti }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($prd_total_cuti / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;">Sakit 病欠</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_1']['sakit']) ? $data['result']['productionshift_1']['sakit'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_2']['sakit']) ? $data['result']['productionshift_2']['sakit'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_3']['sakit']) ? $data['result']['productionshift_3']['sakit'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionoff']['sakit']) ? $data['result']['productionoff']['sakit'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_total_sakit }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($prd_total_sakit / $total) * 100, 1) }}%</td>
                </tr>
                {{-- <tr>
                    <td style="color: red; text-align: left; border: 1px solid black;">Covid (PCR)<br>コロナ（PCR）</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_1']['covid']) ? $data['result']['productionshift_1']['covid'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_2']['covid']) ? $data['result']['productionshift_2']['covid'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_3']['covid']) ? $data['result']['productionshift_3']['covid'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionoff']['covid']) ? $data['result']['productionoff']['covid'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_total_covid }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($prd_total_covid / $total) * 100, 1) }}%</td>
                </tr> --}}
                {{-- <tr>
                    <td style="color: red; text-align: left; border: 1px solid black;">Isoman (Non
                        Covid)<br>自主隔離（コロナ以外）</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_1']['isoman']) ? $data['result']['productionshift_1']['isoman'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_2']['isoman']) ? $data['result']['productionshift_2']['isoman'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_3']['isoman']) ? $data['result']['productionshift_3']['isoman'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionoff']['isoman']) ? $data['result']['productionoff']['isoman'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_total_isoman }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($prd_total_isoman / $total) * 100, 1) }}%</td>
                </tr> --}}
                <tr>
                    <td style="text-align: left; border: 1px solid black;">Libur 休日</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_1']['libur']) ? $data['result']['productionshift_1']['libur'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_2']['libur']) ? $data['result']['productionshift_2']['libur'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_3']['libur']) ? $data['result']['productionshift_3']['libur'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionoff']['libur']) ? $data['result']['productionoff']['libur'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_total_libur }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($prd_total_libur / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;">Belum Konfirmasi 未確認</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_1']['absen']) ? $data['result']['productionshift_1']['absen'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_2']['absen']) ? $data['result']['productionshift_2']['absen'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionshift_3']['absen']) ? $data['result']['productionshift_3']['absen'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ isset($data['result']['productionoff']['absen']) ? $data['result']['productionoff']['absen'] : 0 }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_total_absen }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($prd_total_absen / $total) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;" colspan="2">Total 全部</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_shift_1_total }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_shift_2_total }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_shift_3_total }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_off_total }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $prd_total }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round(($prd_total / $total) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid black;" rowspan="5">All<br>全部</td>
                    <td style="border: 1px solid black;" rowspan="4">Tidak Masuk<br>不在</td>
                    <td style="text-align: left;">Izin/Cuti 休暇・有休</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $cuti_tidak_masuk_shift_1 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $cuti_tidak_masuk_shift_2 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $cuti_tidak_masuk_shift_3 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $cuti_tidak_masuk_off }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $cuti_tidak_masuk_shift_1 + $cuti_tidak_masuk_shift_2 + $cuti_tidak_masuk_shift_3 + $cuti_tidak_masuk_off }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round((($cuti_tidak_masuk_shift_1 + $cuti_tidak_masuk_shift_2 + $cuti_tidak_masuk_shift_3 + $cuti_tidak_masuk_off) / $total) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;">Sakit 病欠</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $sakit_tidak_masuk_shift_1 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $sakit_tidak_masuk_shift_2 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $sakit_tidak_masuk_shift_3 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $sakit_tidak_masuk_off }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $sakit_tidak_masuk_shift_1 + $sakit_tidak_masuk_shift_2 + $sakit_tidak_masuk_shift_3 + $sakit_tidak_masuk_off }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round((($sakit_tidak_masuk_shift_1 + $sakit_tidak_masuk_shift_2 + $sakit_tidak_masuk_shift_3 + $sakit_tidak_masuk_off) / $total) * 100, 1) }}%
                    </td>
                </tr>
                {{-- <tr>
                    <td style="color: red; text-align: left; border: 1px solid black;">Covid (PCR)<br>コロナ（PCR）</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $covid_tidak_masuk_shift_1 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $covid_tidak_masuk_shift_2 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $covid_tidak_masuk_shift_3 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $covid_tidak_masuk_off }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $covid_tidak_masuk_shift_1 + $covid_tidak_masuk_shift_2 + $covid_tidak_masuk_shift_3 + $covid_tidak_masuk_off }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round((($covid_tidak_masuk_shift_1 + $covid_tidak_masuk_shift_2 + $covid_tidak_masuk_shift_3 + $covid_tidak_masuk_off) / $total) * 100, 1) }}%
                    </td>
                </tr> --}}
                {{-- <tr>
                    <td style="color: red; text-align: left; border: 1px solid black;">Isoman (Non
                        Covid)<br>自主隔離（コロナ以外）</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $isoman_tidak_masuk_shift_1 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $isoman_tidak_masuk_shift_2 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $isoman_tidak_masuk_shift_3 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $isoman_tidak_masuk_off }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $isoman_tidak_masuk_shift_1 + $isoman_tidak_masuk_shift_2 + $isoman_tidak_masuk_shift_3 + $isoman_tidak_masuk_off }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round((($isoman_tidak_masuk_shift_1 + $isoman_tidak_masuk_shift_2 + $isoman_tidak_masuk_shift_3 + $isoman_tidak_masuk_off) / $total) * 100, 1) }}%
                    </td>
                </tr> --}}
                <tr>
                    <td style="text-align: left; border: 1px solid black;">Libur 休日</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $libur_tidak_masuk_shift_1 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $libur_tidak_masuk_shift_2 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $libur_tidak_masuk_shift_3 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $libur_tidak_masuk_off }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $libur_tidak_masuk_shift_1 + $libur_tidak_masuk_shift_2 + $libur_tidak_masuk_shift_3 + $libur_tidak_masuk_off }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round((($libur_tidak_masuk_shift_1 + $libur_tidak_masuk_shift_2 + $libur_tidak_masuk_shift_3 + $libur_tidak_masuk_off) / $total) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;">Belum Konfirmasi 未確認</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $absen_tidak_masuk_shift_1 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $absen_tidak_masuk_shift_2 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $absen_tidak_masuk_shift_3 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $absen_tidak_masuk_off }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $absen_tidak_masuk_shift_1 + $absen_tidak_masuk_shift_2 + $absen_tidak_masuk_shift_3 + $absen_tidak_masuk_off }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round((($absen_tidak_masuk_shift_1 + $absen_tidak_masuk_shift_2 + $absen_tidak_masuk_shift_3 + $absen_tidak_masuk_off) / $total) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td style="text-align: left; border: 1px solid black;" colspan="2">Total 全部</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $tidak_masuk_shift_1 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $tidak_masuk_shift_2 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $tidak_masuk_shift_3 }}</td>
                    <td style="border: 1px solid black; text-align: center;">{{ $tidak_masuk_off }}</td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ $tidak_masuk_shift_1 + $tidak_masuk_shift_2 + $tidak_masuk_shift_3 + $tidak_masuk_off }}
                    </td>
                    <td style="border: 1px solid black; text-align: center;">
                        {{ round((($tidak_masuk_shift_1 + $tidak_masuk_shift_2 + $tidak_masuk_shift_3 + $tidak_masuk_off) / $total) * 100, 1) }}%
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr style="background-color: RGB(252, 248, 227);">
                    <th style="border: 1px solid black;" colspan="3">TOTAL</th>
                    <th style="border: 1px solid black;">{{ $total_shift_1 }}</th>
                    <th style="border: 1px solid black;">{{ $total_shift_2 }}</th>
                    <th style="border: 1px solid black;">{{ $total_shift_3 }}</th>
                    <th style="border: 1px solid black;">{{ $total_off }}</th>
                    <th style="border: 1px solid black;">{{ $total }}</th>
                    <th style="border: 1px solid black;"></th>
                </tr>
            </tfoot>
        </table>
        <br>
        <center>
            <span style="font-weight: bold; background-color: orange;">&#8650; <i>Click Here For</i>
                &#8650;</span><br><br>
            <a style="background-color: green; width: 50px;text-decoration: none;color: white;font-size: 20px;"
                href="http://10.109.52.4/mirai/public/index/report/absence">&nbsp;&nbsp;&nbsp; MIRAI Daily Attendance (
                MIRAI日常出勤情報 ) &nbsp;&nbsp;&nbsp;</a>
        </center>
        <br>
    </div>
</body>

</html>
