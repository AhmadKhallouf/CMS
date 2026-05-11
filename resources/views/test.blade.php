<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>test</h1>
</br>
 @php
        $l1 = 555;
        $l2 = 333;
    @endphp

<form action="{{ route('formTest',[$l1,$l2]) }}" method="GET">
    @csrf
    <input type="text" placeholder="enter any thing" name="n1">
    <input type="number" placeholder="enter any thing" name="n2">
    <input type="number" name="t1">
   
    <button type="submit">submit</button>

</form>
</body>
</html>