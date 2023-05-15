<style type="text/css">
  td{
    padding-right: 5px;
    padding-left: 5px;
    padding-top: 0px;
    padding-bottom: 0px;
  }
  th{
    padding-right: 5px;
    padding-left: 5px;      
  }
</style>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
  <div>
    <center>

      <div style="width: 100%">
        <table style="width: 100%; font-family: helvetica; border-collapse: collapse; text-align: left;" >
          <thead>
            <tr>
              <td colspan="10" style="font-weight: bold;font-size: 13px">PT. YAMAHA MUSICAL PRODUCTS INDONESIA</td>
            </tr>
            <tr>
              <td colspan="6" style="text-align: left;font-size: 11px">Jl. Rembang Industri I/36 Kawasan Industri PIER - Pasuruan</td>
            </tr>
            <tr>
              <td colspan="6" style="text-align: left;font-size: 11px">Phone : (0343) 740290 Fax : (0343) 740291</td>
            </tr>
            <tr>
              <td colspan="10" style="text-align: left;font-size: 11px">Jawa Timur Indonesia</td>
            </tr>
          </thead>
        </table>
        <br>
        <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: helvetica; border-collapse: collapse; text-align: left;" cellspacing="0">
          <tbody align="center">
           <!--  <tr>
              <td colspan="2" style="border:1px solid black; font-size: 13; font-weight: bold; width: 50%; height: 30; background-color:  #e8daef ">MIRAI APPROVAL</td>
            </tr> -->
            <tr>
              <td colspan="2" style="border:1px solid black; font-size: 13; font-weight: bold; width: 50%; height: 30;">Approval No : YMPI / MIRAI APPROVAL / {{ $file->no_transaction }}</td>
            </tr>
            <tr>
              <td colspan="2" style="border:1px solid black; font-size: 10; font-weight: bold; width: 50%; height: 30; background-color:  #e8daef ">Nama Dokumen : {{ $file->judul }}</td>
            </tr>
          </tbody>            
        </table><br>
        <table class="table table-bordered" style="border:1px solid black; width: 100%; font-family: helvetica; border-collapse: collapse; text-align: left;" cellspacing="0">
          <tbody>
            <tr>
              <td colspan="1" style="border:1px solid black; font-size: 13px; width: 20%; font-weight: bold; background-color:  #e8daef ; height: 25;">Pemohon</td>
              <?php
              $nama = explode("/", $file->nik);
              ?>
              <td colspan="3"style="border:1px solid black; font-size: 12px; width: 80%">{{ $nama[0] }} - {{ $nama[1] }}</td>
            </tr>
            <tr>
              <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25">Department</td>
              <td colspan="3"style="border:1px solid black; font-size: 12px;">{{ $file->department }}</td>
            </tr>
            <tr>
              <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25">No Dokumen</td>
              <td colspan="3"style="border:1px solid black; font-size: 12px;">{{ $file->no_dokumen }}</td>
            </tr>
            <!-- <tr>
              <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25">Kategori Dokumen</td>
              <td colspan="3"style="border:1px solid black; font-size: 12px;">{{ $file->judul }}</td>
            </tr> -->
            <tr>
              <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25">Tanggal Dibuat</td>
              <td colspan="3"style="border:1px solid black; font-size: 12px;">{{ $file->created_at }}</td>
            </tr>
            @if($file->remark == 'Send Aplicant Reject')
            <tr>
              <td colspan="1" style="border:1px solid black; font-size: 13px; font-weight: bold; background-color:  #e8daef ; height: 25">Alasan Di Reject</td>
              <td colspan="3"style="border:1px solid black; font-size: 12px;">{{ $file->reason }}</td>
            </tr>
            @endif
          </tbody>            
        </table><br>

        <?php for ($i=0; $i < count($isi); $i++) { 
          if ($i == 0 || ($i+1) % 5 == 1) {
            ?>
            <table class="table table-bordered" style="width: 100%; font-family: helvetica; border-collapse: collapse; text-align: center;" cellspacing="0">
              <tbody align="center">
                <?php
              }
              for ($z=0; $z < count($isi); $z++) {
                if ($z == $i) {
                  if ($z == 0 || ($z+1) % 5 == 1) 
                    echo '<tr>';
                    print_r('<th style="width: 1%">
                      <table class="table table-bordered" style="width: 100%; font-family: helvetica; border-collapse: collapse; text-align: center;" cellspacing="0">
                      <tr>
                        <th style="border:1px solid black; font-size: 13px; font-weight: bold; height: 40; background-color:  #e8daef; width: 1% ">'.explode('/', $isi[$i]->header)[0].'<br>'.$isi[$i]->remark.'</th>
                      </tr>
                      <tr>
                        <td style="border:1px solid black; font-size: 13px; font-weight: bold; height: 50; width: 1%">'.$isi[$i]->status.'<br>'.$isi[$i]->approved_at.'</td>
                      </tr>
                      <tr>
                        <td style="border:1px solid black; font-size: 12px; font-weight: bold; height: 30; background-color:  #e8daef">'.$isi[$i]->approver_name.'</td>
                      </tr>
                      </table>
                      </th>');
                  if (($z+1) == count($isi)) { echo '</tr>'; }
                }
              }
              if (($i+1) % 5 == 0 || ($i+1) == count($isi)) { ?>
              </tbody>            
            </table>
            <br>
          <?php  } } ?>
          <br><br>
          <table style="width: 100%; font-family: helvetica; border-collapse: collapse; text-align: center;">
            <thead>
              <tr>
                <td colspan="10" style="font-weight: bold;font-size: 10px">---------- <?php
                echo "Update : " . date("Y-m-d h:i");
                ?> ----------</td>
              </tr>
            </table>
          </div>
        </center>
      </div>
    </body>
    </html>