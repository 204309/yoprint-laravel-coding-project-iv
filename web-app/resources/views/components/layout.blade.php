<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Dashboard</title>
</head>

<body class="bg-gray-700 text-white">
        {{ $slot }}
</body>
</html>
