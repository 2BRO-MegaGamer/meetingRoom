@extends('layouts.layout')
<?php
$title="Select Job";
?>
@section('content')
<div class="container w-100 mt-4" style="height:100%">
    <div class="w-100 h-100 row row-cols-1 row-cols-md-2 row-cols-lg-2 row-cols-xl-3 row-cols-xxl-4 overflow-auto">
        @foreach ($job as $key => $value)
        <div class="col p-0 m-0">
            <div class="card  text-light  m-4 p-0 bg-dark">
                <div class="card-header text-center">
                    {{$key}}
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div>{{$value["detail"]}}</div>
                </div>
                <div class="card-footer p-0 m-0">
                    <a class="w-100 h-100 text-center btn btn-outline-success" href="{{$value["href"]}}">select</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
