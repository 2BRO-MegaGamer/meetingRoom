@extends('layouts.layout')
@php
    $title = 'join Room';
    if ($roomID == 'null') {
        $roomID = '';
    }
@endphp
@section('content')
<div class="container rounded" style="max-width: 34rem;">
    <div class="">
        <form method="get" id="form_join_req" action="/mR/Room/{{$roomID}}">
            @if (isset($message))
            <div class="text-warning fs-3 text-center">
                <p>{{$message}}</p>
            </div>
            @endif
            <div class="form-floating">
                <input type="text" name="my_custom_name" class="form-control rounded-0 rounded-top text-center" value="{{auth()->user()->userName}}" autocomplete="off" required>
                <label>your Name</label>
            </div>
            <div class="form-floating">
                <input type="text" id="room_uuid" name="room_uuid" onchange="change_room_id()" onkeyup="change_room_id()" class="form-control rounded-0 text-center opacity-50" value="{{$roomID}}" autocomplete="off" required>
                <label>Room Id</label>
            </div>
            <button class="btn btn-primary w-100 py-2 rounded-0 rounded-bottom " type="submit" >Join Room</button>
        </form>
    </div>
</div>
<script defer>
    function change_room_id() {
        const room_id = document.getElementById("room_uuid")
        const form_join_req = document.getElementById("form_join_req")
        form_join_req.action = '/mR/Room/'+room_id.value;
    }
</script>
@endsection