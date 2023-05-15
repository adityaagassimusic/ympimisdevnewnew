<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($budget_detail) && count($budget_detail) > 0)
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Periode</th>
                <th>Budget No</th>
                <th>Department</th>
                <th>Description</th>
                <th>Amount</th>
                <th>Env</th>
                <th>Purpose</th>
                <th>PIC</th>
                <th>Account Name</th>
                <th>Category</th>
                <th>April Budget Awal</th>
                <th>May Budget Awal</th>
                <th>Juni Budget Awal</th>
                <th>Juli Budget Awal</th>
                <th>Agustus Budget Awal</th>
                <th>September Budget Awal</th>
                <th>Oktober Budget Awal</th>
                <th>November Budget Awal</th>
                <th>Desember Budget Awal</th>
                <th>Januari Budget Awal</th>
                <th>Februari Budget Awal</th>
                <th>Maret Budget Awal</th>
                <th>Adjustment Forecast</th>
                <th>April Budget Adjustment</th>
                <th>May Budget Adjustment</th>
                <th>Juni Budget Adjustment</th>
                <th>Juli Budget Adjustment</th>
                <th>Agustus Budget Adjustment</th>
                <th>September Budget Adjustment</th>
                <th>Oktober Budget Adjustment</th>
                <th>November Budget Adjustment</th>
                <th>Desember Budget Adjustment</th>
                <th>Januari Budget Adjustment</th>
                <th>Februari Budget Adjustment</th>
                <th>Maret Budget Adjustment</th>
                <th>April Sisa Budget</th>
                <th>May Sisa Budget</th>
                <th>Juni Sisa Budget</th>
                <th>Juli Sisa Budget</th>
                <th>Agustus Sisa Budget</th>
                <th>September Sisa Budget</th>
                <th>Oktober Sisa Budget</th>
                <th>November Sisa Budget</th>
                <th>Desember Sisa Budget</th>
                <th>Januari Sisa Budget</th>
                <th>Februari Sisa Budget</th>
                <th>Maret Sisa Budget</th>
                <th>Created By</th>
                <th>Deleted At</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>

            @foreach($budget_detail as $budget)

            <tr>
                <td>{{ $budget->id }}</td>
                <td>{{ $budget->periode }}</td>
                <td>{{ $budget->budget_no }}</td>
                <td>{{ $budget->department }}</td>
                <td>{{ $budget->description }}</td>
                <td>{{ $budget->amount }}</td>
                <td>{{ $budget->env }}</td>
                <td>{{ $budget->purpose }}</td>
                <td>{{ $budget->pic }}</td>
                <td>{{ $budget->account_name }}</td>
                <td>{{ $budget->category }}</td>
                <td>{{ $budget->apr_budget_awal }}</td>
                <td>{{ $budget->may_budget_awal }}</td>
                <td>{{ $budget->jun_budget_awal }}</td>
                <td>{{ $budget->jul_budget_awal }}</td>
                <td>{{ $budget->aug_budget_awal }}</td>
                <td>{{ $budget->sep_budget_awal }}</td>
                <td>{{ $budget->oct_budget_awal }}</td>
                <td>{{ $budget->nov_budget_awal }}</td>
                <td>{{ $budget->dec_budget_awal }}</td>
                <td>{{ $budget->jan_budget_awal }}</td>
                <td>{{ $budget->feb_budget_awal }}</td>
                <td>{{ $budget->mar_budget_awal }}</td>
                <td>{{ $budget->adj_frc }}</td>
                <td>{{ $budget->apr_after_adj }}</td>
                <td>{{ $budget->may_after_adj }}</td>
                <td>{{ $budget->jun_after_adj }}</td>
                <td>{{ $budget->jul_after_adj }}</td>
                <td>{{ $budget->aug_after_adj }}</td>
                <td>{{ $budget->sep_after_adj }}</td>
                <td>{{ $budget->oct_after_adj }}</td>
                <td>{{ $budget->nov_after_adj }}</td>
                <td>{{ $budget->dec_after_adj }}</td>
                <td>{{ $budget->jan_after_adj }}</td>
                <td>{{ $budget->feb_after_adj }}</td>
                <td>{{ $budget->mar_after_adj }}</td>
                <td>{{ $budget->apr_sisa_budget }}</td>
                <td>{{ $budget->may_sisa_budget }}</td>
                <td>{{ $budget->jun_sisa_budget }}</td>
                <td>{{ $budget->jul_sisa_budget }}</td>
                <td>{{ $budget->aug_sisa_budget }}</td>
                <td>{{ $budget->sep_sisa_budget }}</td>
                <td>{{ $budget->oct_sisa_budget }}</td>
                <td>{{ $budget->nov_sisa_budget }}</td>
                <td>{{ $budget->dec_sisa_budget }}</td>
                <td>{{ $budget->jan_sisa_budget }}</td>
                <td>{{ $budget->feb_sisa_budget }}</td>
                <td>{{ $budget->mar_sisa_budget }}</td>
                <td>{{ $budget->created_by }}</td>
                <td>{{ $budget->deleted_at }}</td>
                <td>{{ $budget->created_at }}</td>
                <td>{{ $budget->updated_at }}</td>
            </tr>

            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>