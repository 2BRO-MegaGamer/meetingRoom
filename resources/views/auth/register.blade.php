@extends('layouts.layout')
@php
    $title = 'Register';
@endphp
@section('content')
<div class="mx-auto rounded-top py-1 my-4 w-100"  style="max-width: 600px; background:{{env("BG_COLOR", "#7CCFBC")}};border-bottom-left-radius: 10%;border-bottom-right-radius: 10%;">
    <form class="p-3 text-light " action="{{ route('register') }}" method="POST" id="form_singup" style="">
        @csrf
        <h1 class="text-center ">صفحه ثبت نام</h1>
        <div class="input-group my-4">
            <input type="text" autocomplete="off" name="lastName" id="lastname_singup" class="form-control rounded-start text-center" maxlength="64" placeholder="نام خانوادگی" required aria-label="last" value="{{ old('lastName') }}">
            <span class="input-group-text"><i class="bi bi-person"></i></span>
            <input type="text" autocomplete="off" name="firstName" id="firstname_singup" class="form-control rounded-end text-center" maxlength="64" placeholder="نام" required aria-label="first" value="{{ old('firstName') }}">
        </div>
        <div class="input-group my-4">
            <span class="input-group-text " ><i class="bi bi-person-square"></i></span>
            <input type="text" autocomplete="off" name="userName" id="username_singup" class="form-control rounded-end text-center" maxlength="64" placeholder="نام کاربری"  required aria-label="Username" aria-describedby="username_singup_span" value="{{ old('userName') }}">
        </div>
        <div class="input-group my-4">
            <input type="email" autocomplete="off" name="email" id="email_singup" class="form-control rounded text-center" placeholder="آدرس ایمیل" required aria-describedby="emailHelp" value="{{ old('email') }}">
        </div>
        <div class="input-group my-4">
            <span class="input-group-text bg-info border-0" >+98</span>
            <input type="text" autocomplete="off" name="phone_number" id="phone_number" class="form-control rounded-end text-center" placeholder="0912 345 6789" required value="{{ old('phone_number') }}" >
        </div>
        <div class=" input-group my-4">
            <div id="group_pass_eye" class="input-group mb-1">
                <input type="password" name="password" id="password_0_singup" class="form-control text-center" placeholder="پسورد" required autocomplete="off" value="{{old('password')}}">
                <span class="input-group-text btn btn-light rounded-end" for_input="password_0_singup" is_showed ="false"  onclick="show_hide_pass(this)"><i class="bi bi-eye position-absolute start-50 top-50 translate-middle"></i></span>
            </div>
            <div class="mx-auto p-0 my-0">
                <small >بیشتر از هشت رقم باشد</small>
            </div>
        </div>
        <div class=" input-group my-4">
            <div id="group_pass_eye" class="input-group mb-1 position-relative">
                <input type="password" name="password_confirmation" id="password_1_singup" placeholder="پسورد دوباره" class="form-control text-center" required autocomplete="off">
                <span class="input-group-text btn btn-light rounded-end" for_input="password_1_singup" is_showed ="false" onclick="show_hide_pass(this)"><i class="bi bi-eye position-absolute start-50 top-50 translate-middle"></i></span>
            </div>
        </div>
        <div class="input-group mb-5 col-3 mx-auto w-50" >
            <select class="form-select rounded-start text-center" name="gender" id="gender_option_singup" required>
                <option value="" selected>انتخاب کنید</option>
                <option value="male">مرد</option>
                <option value="women">زن</option>
            </select>
            <label class="input-group-text rounded-end text-center" for="gender_option_singup">جنسیت</label>
        </div>
        <div class="d-grid" style="margin-bottom: 20px;">
            <button type="submit" class="btn btn-primary text-center" id="logbtn_submit_singup" >ثبت نام</button>
        </div>
    </form>
    <div class="mx-auto w-100 text-center">
        <button class="btn btn-light"><a class=" nav-link fw-lighter" href="{{ route('login') }}">ورود به حساب</a></button>
    </div>
</div>
@php
    $t_error = '';
    if ($errors->any()) {
        $t_error = 'window.addEventListener("load",()=>{show_hide_error_messages(';

        $errors_detail = $errors->get('*');
        $errors_key = array_keys($errors_detail);
        switch ($errors_key[0]) {
            case 'userName':
                $t_error = $t_error . '"username_not_uniqe"';
                break;
            case 'email':
                $t_error = $t_error . '"email_not_uniqe"';
                break;
            case 'phone_number':
                $t_error = $t_error . '"phone_not_uniqe"';
                break;
        }
        $t_error = $t_error.")})";
        if ($t_error == 'window.addEventListener("load",()=>{show_hide_error_messages()})') {
            $t_error = "";
        }
    }
@endphp
<script type="text/javascript" defer>
    function show_hide_error_messages(error_message) {
        var error_messages_info = {
            email_not_valid : 'ایمیلتان معتبر نیست',
            email_not_uniqe : 'ایمیلتان تکراری است',
            username_not_uniqe : 'نام کاربری تکراری است',
            password_not_enough : 'لطفا رمزتان بیشتر از هشت رقم باشد',
            password_not_same : 'لطفا رمزتان را درست وارد کنید',
            phone_not_valid : 'لطفا شماره تلفنتان را درست وارد کنید',
            phone_not_uniqe : 'شماره تان تکراری است',
        }
        show_notification("warning",(error_message.replaceAll("_"," ")),error_messages_info[error_message],false)
    }
    {!! $t_error!!}
</script>
@vite(['resources/js/auth/register.js'])
@endsection
