<!DOCTYPE html>
<html>

<head>
    <title>ABC.com</title>
</head>

<body>
    @if ($user != 0)

    <p>Total users created today: {{ $user }}</p>

    @else

    <p>Don't new users today</p>

    @endif

    <p>Thank you</p>
</body>

</html>
