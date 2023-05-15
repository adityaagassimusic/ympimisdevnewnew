<!DOCTYPE html>
<html>
<head>
	<title>YMPI 情報システム</title>
	<!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> -->
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, user-scalable=yes, initial-scale=1.0" name="viewport">
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

		/*@font-face {
	      font-family: 'Firefly Sung';
	      font-style: normal;
	      font-weight: 400;
	    }
	    * {
	      font-family: Firefly Sung, DejaVu Sans, sans-serif;
	    }*/

	    * {
	      font-family: arial;
	    }

	    .page-break {
			page-break-after: always;
		}

		@page { }
        .footer { position: fixed; left: 0px; bottom: 100px; right: 0px; height: 130px;text-align: center;}
        .footer .pagenum:before { content: counter(page); }
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
          <td colspan="4" style="text-align: left;font-size: 11px">Phone : (0343) 740290</td>
        </tr>
        <tr>
          <td colspan="4" style="text-align: left;font-size: 11px">Kawasan Industri PIER - Pasuruan</td>
          <td colspan="4" style="text-align: left;font-size: 11px">Fax : (0343) 740291</td>
        </tr>
        <tr>
          <td colspan="8" style="text-align: left;font-size: 11px">Jawa Timur Indonesia</td>
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
          <td colspan="10" style="text-align:center;font-size: 20px;font-weight: bold;font-style: italic">
            <div class="line">
              <span>
                File Approval Automatically
              </span>
            <div>
          </td>
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
          <td colspan="1" style="padding-left: 15px; font-size: 14px;font-weight: bold; border: 1px solid black">Application</td>
          <td colspan="4" style="text-align: center; font-size: 14px;font-weight: bold; border: 1px solid black">{{ $detail->aplication }}</td>
          <td colspan="1" style="padding-left: 15px; font-size: 14px; border: 1px solid black">Application No</td>
          <td colspan="4" style="text-align: center; font-size: 14px; border: 1px solid black">{{ $detail->no_transaction }}</td>
        </tr>
        <tr>
          <?php
            $identitas = explode("/",$detail->nik);
          ?>
          <td colspan="1" style="padding-left: 15px; font-size: 14px; border: 1px solid black">Applicant</td>
          <td colspan="4" style="text-align: center; font-size: 14px; border: 1px solid black">{{ $identitas[1] }}</td>
          <td colspan="1" style="padding-left: 15px; font-size: 14px; border: 1px solid black">Application Date</td>
          <td colspan="4" style="text-align: center; font-size: 14px; border: 1px solid black">{{ $detail->date }}</td>
        </tr>
        <tr>
          <td colspan="1" style="padding-left: 15px; font-size: 14px; border: 1px solid black">Department Of Applicant</td>
          <td colspan="9" style="text-align: center; font-size: 14px; border: 1px solid black">{{ $detail->department }}</td>
        </tr>
        <tr>
          <td colspan="1" style="padding-left: 15px; font-size: 14px; border: 1px solid black">Category</td>
          <td colspan="9" style="text-align: center; font-size: 14px; border: 1px solid black">{{ $detail->category }}</td>
        </tr>
        <tr>
          <td colspan="1" style="padding-left: 15px; font-size: 14px; border: 1px solid black">Description </td>
          <td colspan="9" style="text-align: justify; font-size: 14px; border: 1px solid black">{{ $detail->summary }}</td>
        </tr>
        <!-- <tr>
          <td colspan="1" style="padding-left: 15px; font-size: 14px; border: 1px solid black">Description (JPN)</td>
          <td colspan="9" style="text-align: justify; font-size: 14px; border: 1px solid black">{{ $detail->summary_jpn }}</td>
        </tr> -->
        <tr>
          <td colspan="1" style="padding-left: 15px; font-size: 14px; border: 1px solid black">Attachment File</td>
          <td colspan="9" style="padding-left: 15px; font-size: 14px; border: 1px solid black">{{ $detail->file }}</td>
        </tr>
        <tr>
          <td colspan="10"><br></td>
        </tr>
        <tr>
          <td colspan="10"><br></td>
        </tr>
      </thead>
    </table>
        <table style="width: 100%; font-family: TimesNewRoman; border-collapse: collapse; text-align: left;" >
      <thead>
        <tr>
          <td colspan="10"><br></td>
        </tr>
        <tr>
          <?php if($detail->approve1 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center;">Checked By</td>
          <?php } ?>
          <?php if($detail->approve2 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center;">Checked By</td>
          <?php } ?>
          <?php if($detail->approve3 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center;">Checked By</td>
          <?php } ?>
          <?php if($detail->approve4 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center;">Checked By</td>
          <?php } ?>
          <?php if($detail->approve5 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center;">Checked By</td>
          <?php } ?>
          <?php if($detail->approve6 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center;">Checked By</td>
          <?php } ?>
          <?php if($detail->approve7 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center;">Checked By</td>
          <?php } ?>
          <?php if($detail->approve8 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center;">Checked By</td>
          <?php } ?>
          <?php if($detail->approve9 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center;">Checked By</td>
          <?php } ?>
          <?php if($detail->approve10 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center;">Checked By</td>
          <?php } ?>
        </tr>
        <tr>
          <?php if($detail->approve1 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <?php
                $approve1 = explode("/",$detail->approve1);
              ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 14px;font-weight: bold">{{ $approve1[1] }}</td>
          <?php } ?>
          <?php if($detail->approve2 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <?php
                $approve2 = explode("/",$detail->approve2);
              ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 14px;font-weight: bold">{{ $approve2[1] }}</td>
          <?php } ?>
          <?php if($detail->approve3 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <?php
                $approve3 = explode("/",$detail->approve3);
              ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 14px;font-weight: bold">{{ $approve3[1] }}</td>
          <?php } ?>
          <?php if($detail->approve4 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <?php
                $approve4 = explode("/",$detail->approve4);
              ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 14px;font-weight: bold">{{ $approve4[1] }}</td>
          <?php } ?>
          <?php if($detail->approve5 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <?php
                $approve5 = explode("/",$detail->approve5);
              ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 14px;font-weight: bold">{{ $approve5[1] }}</td>
          <?php } ?>
          <?php if($detail->approve6 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <?php
                $approve6 = explode("/",$detail->approve6);
              ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 14px;font-weight: bold">{{ $approve6[1] }}</td>
          <?php } ?>
          <?php if($detail->approve7 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <?php
                $approve7 = explode("/",$detail->approve7);
              ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 14px;font-weight: bold">{{ $approve7[1] }}</td>
          <?php } ?>
          <?php if($detail->approve8 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <?php
                $approve8 = explode("/",$detail->approve8);
              ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 14px;font-weight: bold">{{ $approve8[1] }}</td>
          <?php } ?>
          <?php if($detail->approve9 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <?php
                $approve9 = explode("/",$detail->approve9);
              ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 14px;font-weight: bold">{{ $approve9[1] }}</td>
          <?php } ?>
          <?php if($detail->approve10 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
              <?php
                $approve10 = explode("/",$detail->approve10);
              ?>
              <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 14px;font-weight: bold">{{ $approve10[1] }}</td>
          <?php } ?>
        </tr>
        <tr>
          <?php if($detail->approve1 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
                <?php if($detail->date1 == null){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
                <?php } else  { ?>
                    <?php
                      $date1 = explode("/",$detail->date1);
                    ?>
                <?php if($date1[1] == 'Rejected'){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/rejected.jpg' }}" alt="" style="padding: 0"></td>
                    <?php } else  { ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/approved.jpg' }}" alt="" style="padding: 0"></td>
                <?php } ?>
              <?php } ?>
          <?php } ?>
          <?php if($detail->approve2 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
                <?php if($detail->date2 == null){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
                <?php } else  { ?>
                    <?php
                      $date2 = explode("/",$detail->date2);
                    ?>
                <?php if($date2[1] == 'Rejected'){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/rejected.jpg' }}" alt="" style="padding: 0"></td>
                    <?php } else  { ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/approved.jpg' }}" alt="" style="padding: 0"></td>
                <?php } ?>
              <?php } ?>
          <?php } ?>
          <?php if($detail->approve3 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
                <?php if($detail->date3 == null){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
                <?php } else  { ?>
                    <?php
                      $date3 = explode("/",$detail->date3);
                    ?>
                <?php if($date3[1] == 'Rejected'){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/rejected.jpg' }}" alt="" style="padding: 0"></td>
                    <?php } else  { ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/approved.jpg' }}" alt="" style="padding: 0"></td>
                <?php } ?>
              <?php } ?>
          <?php } ?>
          <?php if($detail->approve4 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
                <?php if($detail->date4 == null){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
                <?php } else  { ?>
                    <?php
                      $date4 = explode("/",$detail->date4);
                    ?>
                <?php if($date4[1] == 'Rejected'){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/rejected.jpg' }}" alt="" style="padding: 0"></td>
                    <?php } else  { ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/approved.jpg' }}" alt="" style="padding: 0"></td>
                <?php } ?>
              <?php } ?>
          <?php } ?>
          <?php if($detail->approve5 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
                <?php if($detail->date5 == null){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
                <?php } else  { ?>
                    <?php
                      $date5 = explode("/",$detail->date5);
                    ?>
                <?php if($date5[1] == 'Rejected'){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/rejected.jpg' }}" alt="" style="padding: 0"></td>
                    <?php } else  { ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/approved.jpg' }}" alt="" style="padding: 0"></td>
                <?php } ?>
              <?php } ?>
          <?php } ?>
          <?php if($detail->approve6 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
                <?php if($detail->date6 == null){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
                <?php } else  { ?>
                    <?php
                      $date6 = explode("/",$detail->date6);
                    ?>
                <?php if($date6[1] == 'Rejected'){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/rejected.jpg' }}" alt="" style="padding: 0"></td>
                    <?php } else  { ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/approved.jpg' }}" alt="" style="padding: 0"></td>
                <?php } ?>
              <?php } ?>
          <?php } ?>
          <?php if($detail->approve7 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
                <?php if($detail->date7 == null){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
                <?php } else  { ?>
                    <?php
                      $date7 = explode("/",$detail->date7);
                    ?>
                <?php if($date7[1] == 'Rejected'){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/rejected.jpg' }}" alt="" style="padding: 0"></td>
                    <?php } else  { ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/approved.jpg' }}" alt="" style="padding: 0"></td>
                <?php } ?>
              <?php } ?>
          <?php } ?>
          <?php if($detail->approve8 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
                <?php if($detail->date8 == null){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
                <?php } else  { ?>
                    <?php
                      $date8 = explode("/",$detail->date8);
                    ?>
                <?php if($date8[1] == 'Rejected'){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/rejected.jpg' }}" alt="" style="padding: 0"></td>
                    <?php } else  { ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/approved.jpg' }}" alt="" style="padding: 0"></td>
                <?php } ?>
              <?php } ?>
          <?php } ?>
          <?php if($detail->approve10 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
                <?php if($detail->date10 == null){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
                <?php } else  { ?>
                    <?php
                      $date10 = explode("/",$detail->date10);
                    ?>
                <?php if($date10[1] == 'Rejected'){ ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/rejected.jpg' }}" alt="" style="padding: 0"></td>
                    <?php } else  { ?>
                    <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"><img width="60" src="{{ public_path() . '/files/file_approval/approved.jpg' }}" alt="" style="padding: 0"></td>
                <?php } ?>
              <?php } ?>
          <?php } ?>
        </tr>
        <tr>
          <?php if($detail->approve1 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
            <?php if($detail->date1 != null){ ?>
                <?php
                  $date1 = explode("/",$detail->date1);
                ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px">{{ $date1[0] }}</td>
                <?php } else  { ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
            <?php } ?>
          <?php } ?>
          <?php if($detail->approve2 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
            <?php if($detail->date2 != null){ ?>
                <?php
                  $date2 = explode("/",$detail->date2);
                ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px">{{ $date2[0] }}</td>
                <?php } else  { ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
            <?php } ?>
          <?php } ?>
          <?php if($detail->approve3 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
            <?php if($detail->date3 != null){ ?>
                <?php
                  $date3 = explode("/",$detail->date3);
                ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px">{{ $date3[0] }}</td>
                <?php } else  { ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
            <?php } ?>
          <?php } ?>
          <?php if($detail->approve4 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
            <?php if($detail->date4 != null){ ?>
                <?php
                  $date4 = explode("/",$detail->date4);
                ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px">{{ $date4[0] }}</td>
                <?php } else  { ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
            <?php } ?>
          <?php } ?>
          <?php if($detail->approve5 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
            <?php if($detail->date5 != null){ ?>
                <?php
                  $date5 = explode("/",$detail->date5);
                ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px">{{ $date5[0] }}</td>
                <?php } else  { ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
            <?php } ?>
          <?php } ?>
          <?php if($detail->approve6 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
            <?php if($detail->date6 != null){ ?>
                <?php
                  $date6 = explode("/",$detail->date6);
                ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px">{{ $date6[0] }}</td>
                <?php } else  { ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
            <?php } ?>
          <?php } ?>
          <?php if($detail->approve7 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
            <?php if($detail->date7 != null){ ?>
                <?php
                  $date7 = explode("/",$detail->date7);
                ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px">{{ $date7[0] }}</td>
                <?php } else  { ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
            <?php } ?>
          <?php } ?>
          <?php if($detail->approve8 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
            <?php if($detail->date8 != null){ ?>
                <?php
                  $date8 = explode("/",$detail->date8);
                ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px">{{ $date8[0] }}</td>
                <?php } else  { ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
            <?php } ?>
          <?php } ?>
          <?php if($detail->approve9 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
            <?php if($detail->date9 != null){ ?>
                <?php
                  $date9 = explode("/",$detail->date9);
                ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px">{{ $date9[0] }}</td>
                <?php } else  { ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
            <?php } ?>
          <?php } ?>
          <?php if($detail->approve10 == null){ ?>
              <td colspan="1"></td>
          <?php } else  { ?>
            <?php if($detail->date10 != null){ ?>
                <?php
                  $date10 = explode("/",$detail->date10);
                ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px">{{ $date10[0] }}</td>
                <?php } else  { ?>
                <td colspan="1" style="font-size: 10px; border: 1px solid black; text-align: center; font-size: 12px"></td>
            <?php } ?>
          <?php } ?>
        </tr>
        <tr>
          <td colspan="10"><br></td>
        </tr>
        <tr>
          <td colspan="10"><br></td>
        </tr>
      </thead>
    </table>
  </header>
</body>
</html>
