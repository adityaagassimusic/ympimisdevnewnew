<!DOCTYPE html>
<html>

<head>
    <style type="text/css">
        td {
            padding-right: 5px;
            padding-left: 5px;
            padding-top: 0px;
            padding-bottom: 0px;
        }

        th {
            padding-right: 5px;
            padding-left: 5px;
        }
    </style>
</head>

<body>    
    <div>
        <center>
            <p>This is an automatic notification. Please do not reply to this address. 返信不要の自動通知です。</p>
            <p>Date: {{ $data['data_detail']->updated_at }}</p>
            <p>Dokumen sudah <b>bisa</b> diteruskan ke tujuan penerima</p>

            @if ($data['status'] == 'Cancelled')
                <br />
                <span style="font-weight: bold; font-size: 36px; color: red;">REJECTED</span>
            @endif
            @if ($data['status'] == 'Completed')
                <br />
                <span style="font-weight: bold; font-size: 36px; color: green;">APPROVED</span>
            @endif
            <table>
                <tbody>
                    <tr>
                        <td style="font-weight: bold;" colspan="3">Submitted By</td>
                        <td style="font-weight: bold;">:</td>
                        <td>{{ $data['data_detail']->applicant }}</td>
                    </tr>
                </tbody>
            </table>
        </center>
        <br>
        <center style="padding-top: 0px">
            <div style="width: 60%" style="padding-top: 0px">
                <table style="border:1px solid black; border-collapse: collapse;">
                    <tbody align="center">
                        <tr>
                            <td colspan="2" style="border:1px solid black; font-size: 20px; width: 20%; height: 20; font-weight: bold; background-color: rgb(126,86,134);color: white">Detail Informasi Pengajuan</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                Request ID
                            </td>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="detail_request_id">
                                {{ $data['data_detail']->request_id }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                Penerima                                
                            </td>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="detail_recipient">
                                {{ $data['data_detail']->recipient }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                Status
                            </td>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; padding: 0 0 5px 0" id="detail_status">
                                {{ $data['data_detail']->status }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                Department
                            </td>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20;" id="detail_department">
                                {{ $data['data_detail']->department }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                Nama Dokumen
                            </td>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 50;" id="detail_purpose">
                                {{ $data['data_detail']->document_name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                Keperluan
                            </td>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 50;" id="detail_purpose">
                                {{ $data['data_detail']->purpose }}
                            </td>
                        </tr>                        
                        <tr>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 20; font-weight: bold;">
                                Jumlah Salinan
                            </td>
                            <td style="border:1px solid black; font-size: 13px; width: 20%; height: 50;" id="detail_purpose">
                                {{ $data['data_detail']->hardcopy_total }}
                            </td>
                        </tr>                        
                    </tbody>
                </table>
            </div>
        </center>
    </div>
</body>

</html>
