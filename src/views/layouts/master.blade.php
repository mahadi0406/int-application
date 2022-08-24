<!DOCTYPE html />
<html class="no-js" lang="en" />
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="keywords" content="HTML5 Template created by Elias" />
    <title>Easy Project Installer</title>

    <link rel="icon" type="image/png" href="{{ asset('https://cdn.jsdelivr.net/gh/uncannyMBM/installer/images/title-icon.png') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('https://cdn.jsdelivr.net/gh/uncannyMBM/installer/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('https://cdn.jsdelivr.net/gh/uncannyMBM/installer/css/font-awesome.min.css') }}" />
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('https://cdn.jsdelivr.net/gh/uncannyMBM/installer/css/styles.css') }}"/>
</head>

<body>

<section id="main">
    <div class="wrapper">
        <div class="content">
            @yield('content')
        </div>
    </div>
</section>

<script type="application/javascript" src="{{ asset('https://cdn.jsdelivr.net/gh/uncannyMBM/installer/js/jquery-3.5.1.min.js') }}"></script>
<script type="application/javascript" src="{{ asset('https://cdn.jsdelivr.net/gh/uncannyMBM/installer/js/bootstrap.min.js') }}"></script>
</body>
</html>
