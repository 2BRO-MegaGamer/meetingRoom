@extends('layouts.layout')
@php
    $title = 'Forget Password';
@endphp
@section('content')
<div class="container  col-lg-5 col-md-5 col-sm" >
    <div class="" style="max-height: fit-content">
        <form class="text-light text-center mt-5"  method="POST" action="/password/reset">
            @csrf
            <div class="mb-3">
                <label for="reset_email_input" class="form-label">Email address</label>
                <input type="email" class="form-control text-center" name="reset_email_input" id="reset_email_input" aria-describedby="emailHelp" required autocomplete="off">
            </div>
            <div class="mb-3">
                <label for="last_password_remmember" class="form-label">Last password remmember</label>
                <input type="password" class="form-control text-center" name="last_password_remmember" id="last_password_remmember" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary">send link</button>
        </form>
    </div>
</div>

@endsection