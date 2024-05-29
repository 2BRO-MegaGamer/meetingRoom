@extends('layouts.layout')
@php
use Illuminate\Support\Facades\Auth;

    $title = 'Home';
    if (!Auth::check()) {
        // dd(Auth::check());
    }
@endphp
@section('content')
<div class="w-100 m-0 p-0">
    <div class="position-relative bg-warning " style="width: 100%;overflow: hidden;height:870px">
        <div class="position-absolute mx-4 top-50  w-50  z-1">
            <h1 class="fw-bold">You're always welcome here</h1>
            <p class="lead text-dark fw-bolder " >Hi, I'm Monsieur. We made a server for you to enjoy. You can tell us your <a href="#sabt_nazar" class="btn p-0" >suggestions</a>. If you have a complaint against someone, you can get help <a href="#report_player" class="btn p-0">from us</a>. You can get points by <a href="/register" class="btn p-0">signing up</a> on our site, which will surely benefit you in the future. We are still working on our server... </p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                <button type="button" class="btn btn-primary btn-lg px-4 me-md-2">Need help?</button>
                <a href="#about_us" type="button" class="btn btn-outline-secondary btn-lg px-4">about us</a>
            </div>
        </div>
        <div class="d-none d-lg-block z-0 h-100">
            <img src="./imgs/home/20226-7-grand-theft-auto-v.png" class="position-absolute top-50 end-0 translate-middle-y " style="opacity:0.9" width="400" height="400">
            <img id="first_div" src="./imgs/home/charac2.png" height="870px" class="position-absolute opacity-50"  style="width:100%">
        </div>
    </div>
</div>

<div>
    <div id ="sabt_nazar" class="mx-5" style="width: 70%;">
        <div class="position-relative ">
            <div class="row align-items-center  py-5 rounded"style="background-color:red">
                <div class="col-lg-7 text-center text-lg-start">
                    <h1 class="display-4 fw-bold lh-1 mb-3">Tell us anything you think is good</h1>
                    <p class="col-lg-10 fs-4">We will do whatever is good for you. You can find out about this server by joining our Discord.</p>
                </div>
                <div class="col-md-10 mx-auto col-lg-5">
                    <form class="p-4 p-md-5 border rounded-3 bg-light" method="POST">
                        @csrf
                    <div class="form-floating mb-3">
                        <input  type="email" class="form-control" id="floatingInput1" placeholder="name@example.com">
                        <label for="floatingInput1">Email address</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="floatingPassword" placeholder="info">
                        <label for="floatingPassword">text</label>
                    </div>
                    <button class="w-100 btn btn-lg btn-primary" type="submit">send</button>
                    <hr class="my-4">
                    <small class="text-muted">By clicking send, you agree to the <button class="btn">terms of use.</button></small>
                    </form>
                </div>
            </div>
            <div class="position-absolute start-100 top-0">
                <img class="position-absolute start-0" src="./imgs/home/Trevor-Philips.png"  height="477">
            </div>
        </div>
    </div>
</div>

<div>
    <div id="about_us" class="m-5">
        <div class="container align-items-center rounded-3 bg-info shadow-lg">
            <div class="row">
                <div class="col">
                    <h1 class="display-4 fw-bold lh-1">Everything about us</h1>
                    <p class="lead">We are a group that wants to create a good leisure time for you. And we have created competitions for you so that you can compete with your friends.
                        You can follow us on social media</p>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start mb-4 mb-lg-3">
                        <a href="#discord" type="button" style="background-color:rgb(88, 101, 242)" class="btn btn-outline-light btn-lg px-4 "><i class="bi bi-discord"></i></a>
                        <a href="#instagram" type="button" style="background-color: rgba(214,41,118);" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-instagram"></i></a>
                        <a href="#twitch" type="button" style="background-color: rgba(100, 65, 164);" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-twitch"></i></a>
                        <a href="#steam" type="button" style="background-color: rgb(42, 71, 94);" class="btn btn-outline-dark btn-lg px-4"><i class="bi bi-steam"></i></a>
                        <a href="#twitter" type="button" style="background-color: rgba(29,161,242);" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-twitter"></i></a>
                        <a href="#whatsapp" type="button" style="background-color: rgba(37,211,102);" class="btn btn-outline-light btn-lg px-4"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col position-relative w-100 text-center">
                    <img class="position-absolute p-0 m-0" src="./imgs/home/FiveM-Logo.png"  height="100%">
                </div>
            </div>
        </div>
    </div>
</div>

<div>
    <div id="report_player">
        <div class="d-inline ">
            <div class="p-0 m-0 " style="background-color: lawngreen;">
                <img  id="frankil" class="position-absolute p-0 m-0 opacity-25 z-0" height="451" src="./imgs/home/GTA-V-Transparent.png">
                <div class="row align-items-center m-0 z-1" style="z-index: 1000;">
                    <div class="col-lg-7 text-center text-lg-start">
                    <h1 class="display-4 fw-bold mb-3">Has someone made a problem for you?</h1>
                    <p class="col-lg-10 fs-4">Tell us what made you upset. We will do our best to solve your problem.<button style="position:relative; z-index: 10000;" class="btn">Is it our problem?</button> </p>
                    </div>
                    <div class="col-md-10 mx-auto col-lg-5">
                        <form class="p-4 p-md-5 border rounded-3 bg-light">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="floatingInput2" placeholder="name@example.com">
                                <label for="floatingInput2">Email address</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingInput3" placeholder="hername or id">
                                <label for="floatingInput3">name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingInput4" placeholder="hername or id">
                                <label for="floatingInput4">tell us</label>
                            </div>
                                <button class="w-100 btn btn-lg btn-primary" type="submit">Send</button>
                                <hr class="my-4">
                                <small class="text-muted">By clicking Send, you agree to the <a class="btn" href="#">terms of use.</a></small>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection