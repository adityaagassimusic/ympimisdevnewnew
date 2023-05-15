  <!DOCTYPE html>
<html>
<head>
  <title>YMPI ??????</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <style type="text/css">
    body{
      font-size: 10px;
    }

    #isi > thead > tr > td {
      text-align: center;
    }

    #isi > tbody > tr > td {
      text-align: left;
      padding-left: 5px;
    }

    .centera{
      text-align: center;
      vertical-align: middle !important;
    }

    .line{
      width: 100%; 
      text-align: center; 
      border-bottom: 1px solid #000; 
      line-height: 0.1em;
      margin: 10px 0 20px;  
    }

    .line span{
      background:#fff; 
      padding:0 10px;
    }

   .page_break { page-break-before: always; }

    @page { }
    .footer { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 200px;text-align: center;}
    .footer .pagenum:before { content: counter(page); }
    }
  </style>
</head>

<body>
  <header>
    <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
      <thead>
        <tr>
          <td colspan="2" rowspan="5" class="centera" style="padding : 0" width="30%">
            <img width="200" src="{{ public_path() . '/waves2.jpg' }}" alt="" style="padding: 0">
          </td>
          <td colspan="8" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA (PT. YMPI)</td>
        </tr>
        <tr>
          <td colspan="8">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: left;font-size: 11px">Jl. Rembang Industri I/36</td>
          <td colspan="4" style="text-align: right;font-size: 11px">Phone : (0343) 740290</td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: left;font-size: 11px">Kawasan Industri PIER - Pasuruan</td>
          <td colspan="4" style="text-align: right;font-size: 11px">Fax : (0343) 740291</td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: left;font-size: 11px">Jawa Timur Indonesia</td>
          <td colspan="4" style="text-align: right;font-size: 11px">No : {{ $slip_disposal }}</td>
        </tr>

        <tr>
          <td colspan="10"><br></td>
        </tr>

        <tr>
          <td colspan="10" style="text-align:center;font-size: 20px;font-weight: bold;font-style: italic">
            <div class="line">
            </div>
          </td>
        </tr>

        <tr>
          <td colspan="10"><br></td>
        </tr>

        <tr>
          <td colspan="4" style="font-size: 14px;font-weight: bold;"></td>
          <td colspan="2"></td>
          <td colspan="1" style="font-size: 12px;"></td>
          <td colspan="3" style="font-size: 12px;"></td>
        </tr>

        <tr>
          <td colspan="1" style="font-size: 11px;width: 10%"></td>
          <td colspan="2" style="font-size: 11px;width: 20%"></td>
          <td colspan="3"></td>
          <td colspan="4" style="text-align: right;font-size: 11px">Pasuruan, {{ $resumes[0]->tanggal_dibuat }}</td>
        </tr>

        <tr>
          <td colspan="1" style="font-size: 11px; width: 5%"><b>Attention</b></td>
          <td colspan="1" style="font-size: 11px; text-align: right">:</td>
          <td colspan="8" style="font-size: 11px; text-align: left">{{ $resumes[0]->nama }}<br>{{ $resumes[0]->alamat }}<br>{{ $resumes[0]->provinsi }}</td>
        </tr>

        <tr>
          <td colspan="10"><br></td>
        </tr>

        <tr>
          <td colspan="1" style="font-size: 11px; width: 5%"><b>FAX</b></td>
          <td colspan="1" style="font-size: 11px; text-align: right">:</td>
          <td colspan="8" style="font-size: 11px; text-align: left">{{ $resumes[0]->fax }}
          </tr>

          <tr>
            <td colspan="10"><br></td>
          </tr>

          <tr>
            <td colspan="10" style="font-size: 11px">
              Dengan Hormat,<br>
              Bersama ini kami informasikan perihal rencana loading Limbah B-3 di PT.Yamaha Musical Products Indonesia
              untuk hari <b>{{ $hari_indo }}, {{ $resumes[0]->dd }}</b> pukul 10:00 WIB dengan spesifikasi limbah sebagai berikut :
            </td>
          </tr>

          <tr>
            <td colspan="10"><br></td>
          </tr>

          <tr>
            <td colspan="10" style="font-size: 11px; text-align: center">
                <?php
              $jumlah_all = 0;
              $all = 0;
              for ($i=0; $i < count($resumes); $i++) { 
                $b = explode(',', $resumes[$i]->jenis);
                $c = explode(',', $resumes[$i]->quantity);
                $d = explode(',', $resumes[$i]->slip);
                $e = explode(',', $resumes[$i]->kode_limbah);
                $no = 1;

                if ($i == 0 || ($i+1) % 1 == 0) {
                  ?>
                  <table class="table table-bordered" style="width: 100%; border-collapse: collapse; text-align: center;" cellspacing="0">
                    <tbody align="center">
                      <?php
                    }
                    for ($z=0; $z < count($resumes); $z++) {
                      if ($z == $i) {
                        if ($z == 0 || ($z+1) % 1 == 0) 
                          echo '<tr>';
                        print_r('<td style="width: 1%; text-align: center">

                          <table style="border-collapse: collapse; width: 100%">
                          <thead>
                          <tr align="center">
                          <th colspan="3" style="border:1px solid black; font-size: 10px; background-color: #f6d965; height: 10; text-align: center;">Limbah '.$b[0].' ('.$e[0].')</th>
                          </tr>
                          <tr align="center"> 
                          <td style="border:1px solid black; width: 10%; height: 10;">NO. LIMBAH</td>
                          <td style="border:1px solid black; width: 30%; height: 10;">BERAT</td>
                          <td style="border:1px solid black; width: 30%; height: 10;">JML. JUMBO BAG</td>
                          </tr>
                          </thead>
                          <tbody>');
                          for ($a=0; $a < count($b); $a++) { ?>
                            <?php 
                            print_r('
                              <tr align="center"> 
                              <td style="border:1px solid black; height: 10;">'.$no++.'</td>
                              <td style="border:1px solid black; height: 10;">'.$c[$a].' KG</td>
                              <td style="border:1px solid black; height: 10;">1</td>
                              </tr>');
                            } ?>
                            <?php print_r('
                              <tr align="center"> 
                              <td style="border:1px solid black; height: 10;">JUMLAH</td>
                              <td style="border:1px solid black; height: 10;">'.$resumes[$i]->jumlah.' KG</td>
                              <td style="border:1px solid black; height: 10;">'.$resumes[$i]->banyak.'</td>
                              </tr>
                              </tbody>
                              </table>
                              </td>');
                            $jumlah_all += $resumes[$i]->jumlah;
                            $all += $resumes[$i]->banyak;

                            if (($z+1) == count($resumes)) { echo '</tr>'; }
                          }
                        }
                        if (($i+1) % 1 == 0 || ($i+1) == count($resumes)) { ?>
                        </tbody>            
                      </table>
                      <br>
                    <?php  } } ?>
            </td>
          </tr>
          <div class="page_break"></div>

          <tr>
            <td colspan="10"><br></td>
          </tr>

          <tr>
            <td colspan="10" style="font-size: 11px">
              Untuk kelancaran kegiatan loading mohon dikirim <b>{{ $resumes[0]->kendaraan }}</b> dengan kapasitas <b><?= $jumlah_all ?> KG (<?= $all ?> Jumbo Bag)</b><br>
              Demikian pemberitahuan ini, atas kerjasamanya yang baik kami menyampaikan banyak terima kasih.
            </td>
          </tr>

          <tr>
            <td colspan="10"><br></td>
          </tr>

          <tr>
            <td colspan="3" style="text-align: center;font-size: 11px">Hormat Kami,</td>
            <td colspan="3" style="text-align: left;font-size: 11px"></td>
            <td colspan="4" style="text-align: right;font-size: 11px">Disetujui oleh {{ $resumes[0]->vendor }},</td>
          </tr>

          <tr>
            <td colspan="10"><br></td>
          </tr>

          <tr>
            <td colspan="10"><br></td>
          </tr>

          <tr>
            <td colspan="10"><br></td>
          </tr>

          <tr>
            <td colspan="3" style="text-align: center;font-size: 11px">{{$isi_approval[0]->remark}}</td>
            <td colspan="3" style="text-align: center;font-size: 11px">{{$isi_approval[1]->remark}}</td>
            <td colspan="4" style="text-align: center;font-size: 11px"></td>
          </tr>

          <tr style="height: 70px">
            <td colspan="3" style="text-align: center;font-size: 11px">
              @if($isi_approval[0]->status == 'Approve')
              <img style="width: 150px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('data_file/wwt/PI1210001.png')))}}">
              @endif
              <br><span>{{ $isi_approval[0]->approved_at }}</span>
            </td>
            <td colspan="3" style="text-align: center;font-size: 11px">
             @if($isi_approval[1]->status == 'Approve')
             <img style="width: 150px" src="data:image/png;base64,{{base64_encode(file_get_contents(public_path('data_file/wwt/PI1404002.png')))}}">
             @endif
             <br><span>{{ $isi_approval[1]->approved_at }}</span>
           </td>
           <td colspan="4" style="text-align: center;font-size: 11px"></td>
         </tr>

         <tr>
          <td colspan="3" style="text-align: center;font-size: 11px">{{ $isi_approval[0]->approver_name }}</td>
          <td colspan="3" style="text-align: center;font-size: 11px">{{ $isi_approval[1]->approver_name }}</span></td>
          <td colspan="4" style="text-align: center;font-size: 11px">{{ $resumes[0]->nama }}</td>
        </tr>
      </thead>
    </table>
  </header>
</body>
</html>