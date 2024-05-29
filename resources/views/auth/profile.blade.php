@extends('layouts.layout')
@php
    $title = 'profile';
@endphp
@section('content')
<div class="row w-100 m-0 p-0 mt-3">
    <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
        <ul class="list-group">
            <a class="list-group-item btn border-1 mx-2 my-1" style="background: {{env("BG_COLOR","#60A091")}}" id="profile_menu" active onclick="change_overview('profile',this)" >
                <div class="row w-100 h-100">
                    <div class="col-1 m-auto">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="col">
                        public manage
                    </div>
                </div>
            </a>
        </ul>
    </div>
    <div class="col mx-auto">
        <div class="w-100 h-100 my-3" id="profile">
            <div class="card bg-dark text-light">
                <div class="p-1 m-2 border-0">
                    <div class="input-group my-4">
                        <input type="text" class="form-control rounded-start text-end" maxlength="64" placeholder="{{ auth()->user()->lastName }}" disabled>
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control rounded-end text-end" maxlength="64" placeholder="{{ auth()->user()->firstName }} "disabled>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" ><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control text-center" placeholder="{{auth()->user()->userName}}" aria-label="Username" disabled>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" >@</span>
                        <input type="text" class="form-control text-center" placeholder="{{auth()->user()->email}}" disabled>
                        @if(auth()->user()->email_verified_at == null)
                            <a href="/email/verify" class="input-group-text btn btn-warning text-light" ><i class="bi bi-exclamation-diamond"></i></a>
                        @endif
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" ><i class="bi bi-telephone"></i></span>
                        <input type="text" class="form-control text-center" placeholder="{{auth()->user()->phone_number}}" disabled>
                        @if(auth()->user()->phone_number_verified_at == null)
                            <a href="/phone/verify" class="input-group-text btn btn-warning text-light" ><i class="bi bi-exclamation-diamond"></i></a>
                        @endif
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text" ><i class="bi bi-gender-ambiguous"></i></span>
                        <input type="text" class="form-control text-center" placeholder="{{auth()->user()->gender}}" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
