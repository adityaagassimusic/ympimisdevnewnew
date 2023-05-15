<!DOCTYPE html>
<html>
<head>
</head>
<body>
    @if(isset($resumes))
    <table>
        <thead>
            <tr>
                <th>EMPLOYEE_NAME</th>
                <th>ANSWER_1</th>
                <th>ANSWER_2</th>
                <th>ANSWER_3</th>
                <th>ANSWER_4</th>
                <th>ANSWER_5</th>
                <th>ANSWER_6</th>
                <th>ANSWER_7</th>
                <th>ANSWER_8</th>
                <th>ANSWER_9</th>
                <th>ANSWER_10</th>
                <th>ANSWER_11</th>
                <th>ANSWER_12</th>
            </tr>
        </thead>
        <tbody>
            @foreach($resumes as $resumes)
            <?php
                $b = $resumes->address;
                $a = explode("/", $b);
            ?>
            <tr>
                <td>{{ $resumes->name }}</td>
                <td>{{ $resumes->answer1 }}</td>
                <td>{{ $resumes->answer2 }}</td>
                <td>{{ $resumes->answer3 }}</td>
                <td>{{ $resumes->answer4 }}</td>
                <td>{{ $resumes->answer5 }}</td>
                <td>{{ $resumes->answer6 }}</td>
                <td>{{ $resumes->answer7 }}</td>
                <td>{{ $resumes->answer8 }}</td>
                <td>{{ $resumes->answer9 }}</td>
                <td>{{ $resumes->answer10 }}</td>
                <td>{{ $resumes->answer11 }}</td>
                <td>{{ $resumes->answer12 }}</td>
            </tr>
            
            @endforeach
        </tbody>
    </table>
    @endif
</body>
</html>