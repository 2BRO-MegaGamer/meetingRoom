<!DOCTYPE html>
<html lang="en">
<?php
$t_error="";
if ($errors->any()) {
    $errors_detail = $errors->get('*');
    $errors_key = array_keys($errors_detail);
    $t_error = 'window.addEventListener("load",()=>{show_notification("warning","'.$errors_key[0].'","'.$errors_detail[$errors_key[0]][0].'",true)});';
}
?>
<head>
    @vite(['resources/sass/app.scss'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ Session::token() }}"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" href="{{asset('/imgs/home/icon_naranji.png')}}">
    <style>
        *::-webkit-scrollbar-track
        {
            -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
            background-color: #F5F5F5;
        }
        *::-webkit-scrollbar
        {
            width: 6px;
            background-color: #F5F5F5;
        }
        *::-webkit-scrollbar-thumb
        {
            background-color: #000000;
        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
    <title>{{$title}}</title>
</head>
<body style="background-color: rgb(0, 0, 0)">
    <div class="w-100 h-100 position-relative">
        <div id="header" class="z-3 sticky-top w-100" style="min-height: 10%;height:10%;">
            @include('layouts.header_nav')
        </div>
        <div id="main" style="min-height: 90%;height: 90vh;">
            @yield('content')
        </div>
    </div>
    <div id="toasts_div"></div>
    @vite(['resources/js/homepage.js','resources/js/bootstrap.js','resources/js/notification_Toasts.js'])
</body>
<script>{!! $t_error !!}</script>
</html>