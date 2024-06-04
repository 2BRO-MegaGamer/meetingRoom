@extends('layouts.layout')
@php
    $title = 'create Room';
@endphp
@section('content')

<div class="container rounded" style="max-width: 34rem;">
    <div class="">
        @if (isset($message))
        <div class="text-warning fs-3 text-center">
            <p>{{$message}}</p>
        </div>
        @endif
        <form method="post" action="/mR/create_room">
            @csrf
            <div class="form-floating">
                <input type="text" name="room_name" class="form-control rounded-0 rounded-top" value="{{auth()->user()->UserName}} Room">
                <label>Name</label>
            </div>
            <div class="form-floating">
                <input type="text" id="room_uuid" name="room_uuid" class="form-control rounded-0 text-center opacity-50" value="{{$roomID}}" readonly>
                <label>Room Id</label>
            </div>
            <div>
                <select name="type_Room" class="form-select bg-secondary border-0 rounded-0">
                    <option value="private" selected>private</option>
                    <option value="public">public</option>
                </select>
            </div>

            <button class="btn btn-primary w-100 py-2 rounded-0 rounded-bottom " type="submit" >Create Room</button>
        </form>
    </div>
</div>


@endsection