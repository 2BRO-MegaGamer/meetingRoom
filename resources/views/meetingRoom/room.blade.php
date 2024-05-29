@extends('meetingRoom.layout.master')

@section('MR_room')
  <h1 id="room_id"></h1>
  <input id="another_id" type="text">
  <button id="share_screen">share screen</button>
  <button id="camera">camera</button>

  <div id="div_asli" class="">
    <div id="videos_connection" class="row bg-danger w-100">
      <video id="localVideo" class="col bg-dark w-50"></video>
      <div id="remoteVideos" class="col bg-dark w-50"></div>
    </div>
  </div>
@endsection