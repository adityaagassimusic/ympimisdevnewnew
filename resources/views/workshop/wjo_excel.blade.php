<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($workshop_job_orders) && count($workshop_job_orders) > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nomor WJO</th>
                <th>Tanggal WJO</th>
                <th>Bagian</th>
                <th>Jenis Pekerjaan</th>
                <th>Nama Drawing</th>
                <th>Drawing No.</th>
                <th>Part No.</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Prioritas</th>
                <th>Material Awal</th>
                <th>Dekripsi Masalah</th>
                <th>Progress</th>
                <th>Target Selesai</th>
                <th>Actual Selesai</th>
                <th>Tingkat Kesulitan</th>
                <th>Proses Utama</th>
                <th>Rating</th>
                <th>Catatan</th>
                <th>Approver</th>
                <th>PIC</th>
                <th>Time (min)</th>
            </tr>
        </thead>
        <tbody>
            <?php $index = 1; ?>
            @foreach($workshop_job_orders as $workshop_job_order)
            <tr>
                <td>{{ $index++ }}</td>
                <td>{{ $workshop_job_order->order_no }}</td>
                <td>{{ $workshop_job_order->created_at }}</td>
                <td>{{ $workshop_job_order->sub_section }}</td>
                <td>{{ $workshop_job_order->type }}</td>
                <td>{{ $workshop_job_order->drawing_name }}</td>
                <td>{{ $workshop_job_order->item_number }}</td>
                <td>{{ $workshop_job_order->part_number }}</td>
                <td>{{ $workshop_job_order->item_name }}</td>
                <td>{{ $workshop_job_order->quantity }}</td>
                <td>{{ $workshop_job_order->priority }}</td>
                <td>{{ $workshop_job_order->material }}</td>
                <td>{{ $workshop_job_order->problem_description }}</td>
                <td>{{ $workshop_job_order->process_name }}</td>
                <td>{{ $workshop_job_order->target_date }}</td>
                <td>{{ $workshop_job_order->finish_date }}</td>
                <td>{{ $workshop_job_order->difficulty }}</td>
                <td>{{ $workshop_job_order->main_process }}</td>
                <td>{{ $workshop_job_order->rating }}</td>
                <td>{{ $workshop_job_order->note }}</td>
                <td>{{ $workshop_job_order->approver_name }}</td>
                <td>{{ $workshop_job_order->pic_name }}</td>
                <td>{{ $workshop_job_order->work_time }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>