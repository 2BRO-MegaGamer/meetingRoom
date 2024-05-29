@extends('layouts.layout')
@php
    $title = 'login';
@endphp
@section('content')
<div class="mx-auto rounded-top py-1 my-4 w-100" id="div_first_kol" style="max-width: 30rem; background:{{env("BG_COLOR_FORM", "#7CCFBC")}};border-bottom-left-radius: 10%;border-bottom-right-radius: 10%;">
    <form class=" mx-auto my-4 p-3 text-light" action="{{ route('login') }}"  method="POST" id="form_login_asl">
        @csrf
        <h1 class="text-center">ورود به حساب</h1>
        <div class="mb-3" id="error_div">
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus id="email_login" class="form-control text-center @error('email') is-invalid @enderror" placeholder="ایمیل" aria-describedby="emailHelp">
            @error('email')
            <span class="invalid-feedback w-100 text-center" role="alert">
                <strong>این اطلاعات ها با سوابق ما مطابقت ندارد</strong>
            </span>
            @enderror
        </div>
        <div class="mb-3 ">
            <div id="group_pass_eye" class="input-group mb-1">
                <input type="password" placeholder="پسورد" class="form-control text-center @error('password') is-invalid @enderror" name="password" id="password_login" >
                <span class="input-group-text btn btn-light" is_showed="false" for_input="password_login" onclick="show_hide_pass(this)"><i class="bi bi-eye"></i></span>

            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 offset-md-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        من را به خاطر داشته باش
                    </label>
                </div>
            </div>
        </div>
        <div class="d-grid gap-2 col-6 mx-auto">
            <button type="submit" id="btn_sub_err" class="btn btn-primary" >ورود به حساب</button>
        </div>
    </form>
    <div class="row w-75 m-auto">
        <a href="password/reset" class="col btn btn-light mx-2"><small class="fw-lighter">رمزتان را فراموش کرده اید؟</small></a>
        <a href="{{ route('register') }}" class="col btn btn-light my-x" >ثبت نام</a>
    </div>
</div>
@vite(['resources/js/auth/login.js'])

@endsection









