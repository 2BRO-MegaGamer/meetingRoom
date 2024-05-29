<!DOCTYPE html>
<html lang="en">
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
    <div id="header" class="z-3">
        @include('layouts.header_nav')
    </div>
    <div id="main">
        @yield('content')
    </div>
    {{-- <div id="footer" class="z-3">
        <footer>
            <ul class="nav justify-content-center w-100">
                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Home</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Features</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Pricing</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">FAQs</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">About</a></li>
            </ul>
            <div class="row align-items-center p-0 m-0">
                <div class="col border-bottom"></div>
                <div class="col-1 fw-bold text-center "><i class="bi bi-airplane-fill" style="font-size:70px;"></i></div>
                <div class="col border-bottom"></div>
            </div>
            <p class="text-center text-body-secondary">© 2023 Company, Inc</p>
        </footer>
    </div> --}}
    <div id="toasts_div"></div>
    @vite(['resources/js/homepage.js','resources/js/bootstrap.js','resources/js/notification_Toasts.js'])


</body>
</html>