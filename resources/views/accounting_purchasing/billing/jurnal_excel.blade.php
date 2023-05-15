<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($jurnal_detail) && count($jurnal_detail) > 0)
    <table>
        <thead>
            <tr></tr>
            <tr></tr>
            <tr>
                <!-- <th>Nomor</th> -->
                <th>Settle Acc No</th>
                <th>Reference</th>
                <th>Beneficiary Name</th>
                <th>Beneficiary Address</th>
                <th>Beneficiary City</th>
                <th>Beneficiary Country</th>
                <th>Beneficiary No</th>
                <th>Currency</th>
                <th>Sector Select</th>
                <th>Exchange Method</th>
                <th>Contract Number</th>
                <th>Bank Name</th>
                <th>Bank Branch</th>
                <th>Bank City Country</th>
                <th>Bank Charge Account</th>
                <th>Info To Bank</th>
                <th>IBAN</th>
                <th>RTGS</th>
                <th>Resident</th>
                <th>Citizenship</th>
                <th>Relation</th>
                <th>Email Address</th>
                <th>Purpose Remit</th>
                <th>Bank Charge</th>
                <th>Amount</th>
                <th>Date Payment</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
            <!-- <?php 
                $num = 1;
            ?> -->
            @foreach($jurnal_detail as $jurnal)
            <tr>
                <!-- <td>{{ $num++ }}</td> -->
                <td>{{ $jurnal->settle_acc_no }}</td>
                <td>{{ $jurnal->supplier_name }}</td>
                <td>{{ $jurnal->rekening_nama }}</td>
                <td>{{ $jurnal->address_vendor }}</td>
                <td>{{ $jurnal->city }}</td>
                <td>{{ $jurnal->country }}</td>
                <td>{{ $jurnal->rekening_no }}</td>
                <td>{{ $jurnal->currency }}</td>
                <td>{{ $jurnal->sector_select }}</td>
                <td>{{ $jurnal->exchange_method }}</td>
                <td>{{ $jurnal->contract_number }}</td>
                <td>{{ $jurnal->bank_name }}</td>
                <td>{{ $jurnal->bank_branch }}</td>
                <td>{{ $jurnal->bank_city_country }}</td>
                <td></td>
                <td>{{ $jurnal->swift_code }}</td>
                <td>{{ $jurnal->iban }}</td>
                <td></td>
                <td>{{ $jurnal->resident }}</td>
                <td>{{ $jurnal->citizenship }}</td>
                <td>{{ $jurnal->relation }}</td>
                <td></td>
                <td>{{ $jurnal->purpose_remit }}</td>
                <td>{{ $jurnal->bank_charge }}</td>
                <td>{{ $jurnal->amount }}</td>
                <td><?php echo date('ymd', strtotime($jurnal->jurnal_date)) ?></td>
                <td>{{ $jurnal->remark }}</td>
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>