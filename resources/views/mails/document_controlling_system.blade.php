<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            font-family: Calibri, 'Trebuchet MS', sans-serif;
            font-size: 18px;
        }

        .center {
            text-align: center;
        }

        table {
            border-collapse: collapse;            
            text-align: center;
        }

        table tr th {
            background-color: #605CA8;
            color: #fff;
            text-align: center;
            border: 1px solid #000;
        }

        table tr {
            padding: 2px 5px;
        }

        table tr td {
            padding: 5px;
            border: 1px solid #000;
        }        

        .buttonHref {
            background-color: #55bb55;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>    
    <p>
        {{ $data['email_body'] }}
    </p>           
</body>
</html>