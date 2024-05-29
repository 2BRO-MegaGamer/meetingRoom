@extends('layouts.layout')
@php
    $title = 'Verify phone Number';
    $all_html_style = [
        "no_code" => '
                <div class="m-2 text-center">
                    برای ثبت کردن شماره تلفنتان روی دکمه زیر کلیک کنید
                </div>
                <form class="text-center" method="POST" action="/phone/verify">
                    <input type="hidden" name="_token" value="'. csrf_token() .'" />
                    <button type="submit" class="btn btn-light">ارسال پیام</button>
                </form>
                ',

        "code_duplicate" =>'
                <div class="w-100 text-center" style="margin-top: 50px">
                    <small class=" fw-bold opacity-50">کاربر گرامی .اگر مشکلی دارید لطفا 5 دقیقه دیگر امتحان کنید</small>
                </div>
        ',
        "code_send"=>'
                <div class="m-2 p-2 fs-5 text-center rounded bg-info">
                    کد فعال سازی برای شما ارسال شده است
                </div>
                <form class="text-center" method="POST" action="/phone/verify">
                    <input type="hidden" name="_token" value="'. csrf_token() .'" />
                    <div class="input-group mb-3 m-auto">
                        <input type="number" min="0"  maxlength="1" minlength="1" autocomplete="off" class="form-control text-light text-center rounded-2 border-2 border-top-0" verification_input="1" name="code_1" style="background: '.env("THIRD_BG_COLOR","#005D9D").'">
                        <input type="number" min="0"  maxlength="1" minlength="1" autocomplete="off" class="form-control text-light text-center rounded-2 border-2 border-top-0" verification_input="2" name="code_2" style="background: '.env("THIRD_BG_COLOR","#005D9D").'">
                        <input type="number" min="0"  maxlength="1" minlength="1" autocomplete="off" class="form-control text-light text-center rounded-2 border-2 border-top-0" verification_input="3" name="code_3" style="background: '.env("THIRD_BG_COLOR","#005D9D").'">
                        <input type="number" min="0"  maxlength="1" minlength="1" autocomplete="off" class="form-control text-light text-center rounded-2 border-2 border-top-0" verification_input="4" name="code_4" style="background: '.env("THIRD_BG_COLOR","#005D9D").'">
                        <input type="number" min="0"  maxlength="1" minlength="1" autocomplete="off" class="form-control text-light text-center rounded-2 border-2 border-top-0" verification_input="5" name="code_5" style="background: '.env("THIRD_BG_COLOR","#005D9D").'">
                        <input type="number" min="0"  maxlength="1" minlength="1" autocomplete="off" class="form-control text-light text-center rounded-2 border-2 border-top-0" verification_input="6" name="code_6" style="background: '.env("THIRD_BG_COLOR","#005D9D").'">
                    </div>
                    <button type="submit" class="btn btn-light" id="verify_btn" disabled>تایید کد</button>
                </form>
        ',
        'outOf_attempt'=>'
                <div class="m-2 p-2 fs-5 text-center rounded bg-danger">
                    شما بیش از حد امتحان کرده اید. بعد از 5 دقیقه امتحان کنید
                </div>
        ',
        'outOf_time'=>'
                <div class="m-2 p-2 fs-5 text-center rounded bg-info">
                    زمان شما برای تایید شماره به اتمام رسیده است
                </div>
                <div class="m-2 text-center">
                    برای ثبت کردن شماره تلفنتان روی دکمه زیر کلیک کنید
                </div>
                <form class="text-center" method="POST" action="/phone/verify">
                    <input type="hidden" name="_token" value="'. csrf_token() .'" />
                    <button type="submit" class="btn btn-light">ارسال پیام</button>
                </form>
        '
    ];


    $html_for_page;
    switch ($message) {
        case 'no_code':
            $html_for_page = $all_html_style['no_code'];
            break;
        case 'code_send':
            $html_for_page = $all_html_style['code_send'];
            break;
        case 'code_duplicate':
            $html_for_page = $all_html_style['code_send'] . $all_html_style['code_duplicate'];
            break;
        case 'outOf_attempt':
            $html_for_page = $all_html_style['outOf_attempt'];
            break;
        case 'outOf_time':
            $html_for_page = $all_html_style['outOf_time'];
            break;
    }
@endphp
@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card text-light" style="background: {{env("BG_COLOR","#306357")}}">
                <div class="card-header text-end"> ثبت کردن شماره تلفن</div>
                <div class="card-body">
                    {!! $html_for_page !!}
                </div>
            </div>
        </div>
    </div>
</div>
@vite(['resources/js/auth/phone_verification.js'])

@endsection
