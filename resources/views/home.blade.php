@extends('layouts.layout')
@php
    $title = 'Home';
@endphp
@section('content')
<div class="text-light ">
    <div class="container position-relative py-4">
        <div class="w-100 h-100 position-relative">
            <div class="w-25 z-3 m-auto text-center  bg-light p-3 rounded-5  position-absolute start-50 translate-middle-x " style="rotate: -15deg"><img src="{{asset('imgs/bigone.jpg')}}" class="w-50 " id="home_page_image" style="rotate: +15deg"></div>
        </div>
    </div>
</div>
@endsection