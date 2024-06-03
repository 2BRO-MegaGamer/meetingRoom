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
@php
    $output ='';
    if ($errors->any()) {
        $errors_detail = $errors->get('*');
        $errors_key = array_keys($errors_detail);
        $t_error = 'show_notification("warning",(("'.$errors_key[0].'").replaceAll("_"," ")),"'.$errors_detail[$errors_key[0]][0].'",false);';
        $output = 'window.addEventListener("load",()=>{'.$t_error.'})';
    }
@endphp
<script type="text/javascript" defer>
    {!! $output !!}
</script>
@endsection