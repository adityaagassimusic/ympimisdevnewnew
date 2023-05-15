<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($resumes))
    <table>
        <thead>
            <tr>
                <th>EMP_NO</th>
                <th>USERNAME</th>
                <th>EMPLOYEE_NAME</th>
                <th>POSITION</th>
                <th>ORGANIZATION_UNIT</th>
                <th>JOIN_DATE</th>
                <th>GENDER</th>
                <th>BIRTH_DATE</th>
                <th>GRADE</th>
                <th>COST_CENTER</th>
                <th>STATUS</th>
                <th>USERTYPE</th>
                <th>USER_STATUS</th>
                <th>WORK_LOCATION</th>
                <th>CURRENCY_CODE</th>
                <th>PAY_FREQUENCY</th>
                <th>TAX_TYPE</th>
                <th>TAX_STATUS</th>
                <th>SALARY</th>
                <th>TAXED</th>
                <th>SALARY_RECEIVED</th>
                <th>BANK</th>
                <th>BANK_BRANCH</th>
                <th>ACCOUNT_NO</th>
                <th>ACCOUNT_NAME</th>
                <th>PAYPERIOD</th>
                <th>EMPLOYMENT_START_DATE</th>
                <th>EMPLOYMENT_END_DATE</th>
                <th>PERMANENT_DATE</th>
                <th>TERMINATION_DATE</th>
                <th>RESIGN_TYPE</th>
                <th>RESIGN_REASON</th>
                <th>EMAIL</th>
                <th>TAXCODE_NUMBER</th>
                <th>IDCARD_NUMBER</th>
                <th>BIRTH_PLACE</th>
                <th>RELIGION</th>
                <th>MARITAL_STATUS</th>
                <th>ADDRESS</th>
                <th>CITY</th>
                <th>PHONE</th>
                <th>MOBILE_PHONE</th>
                <th>SUPERVISOR</th>
                <th>MANAGER</th>
                <th>Group</th>
                <th>NUMBER_DEPENDENT</th>
                <th>JAMSOSTEK</th>
                <th>BPJS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumes as $resumes)
            <?php
                $b = $resumes->address;
                $a = explode("/", $b);
            ?>
            <tr>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ $resumes->name }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ $resumes->gender }}</td>
                <td><?php echo date('d-M-Y', strtotime($resumes->birth_date)) ?></td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ 'Passive' }}</td>
                <td>{{ 'Inactive' }}</td>
                <td>{{ 'PT YMPI' }}</td>
                <td>{{ 'IDR' }}</td>
                <td>{{ 'MONTH' }}</td>
                <td>{{ 'L' }}</td>
                <td>{{ $resumes->mariage_status }}</td>
                <td>{{ '' }}</td>
                <td>{{ 'Yes' }}</td>
                <td>{{ 'Net' }}</td>
                <td>{{ 'Mandiri' }}</td>
                <td>{{ 'MDR_PASURUAN' }}</td>
                <td>{{ '000000000000' }}</td>
                <td>{{ $resumes->name }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ $resumes->email }}</td>
                <td>{{ '' }}</td>
                <td>{{ $resumes->nik }}</td>
                <td>{{ $resumes->birth_place }}</td>
                <td>{{ $resumes->religion }}</td>
                <td>{{ $resumes->mariage_status }}</td>
                <td>{{ $a[0].' RT 0'.$a[1].' RW 0'.$a[2].' KELURAHAN '.$a[3].' KECAMATAN '.$a[4].' KOTA/KAB '.$a[5] }}</td>
                <td>{{ $a[5] }}</td>
                <td>{{ '' }}</td>
                <td>{{ $resumes->handphone }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
                <td>{{ '' }}</td>
            </tr>
            
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>