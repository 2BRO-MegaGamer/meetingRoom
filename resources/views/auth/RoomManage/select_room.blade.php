@extends('layouts.layout')
<?php
$title="Select Room";
// $room_ids = array_keys($room_info);
?>
@section('content')
<div class="container w-100 overflow-auto mt-4" style="max-height: 80%">
    @if (count($room_info) == 0)
        <h1 class="w-100 text-center text-light"> You have no room</h1>
        <p class="w-100 text-light text-center">You can make it <a href="/mR/create">here</a></p>
    @endif
    @for ($i = 0; $i < count($room_info); $i++)
        <div class="card bg-dark mb-2 text-light">
            <div class="p-1 m-2 border-0">
                <div class="w-100 mb-1 text-center fw-bold">
                    <small>{{$room_info[$i]->id}}</small>
                </div>
                <div class="input-group my-4 text-center">
                    <div class="form-control rounded-start bg-dark text-light d-flex justify-content-center align-items-center">
                        <div class="w-100">
                            Creator ID 
                        </div>
                    </div>
                    <span class="input-group-text bg-dark text-light"><i class="bi bi-person"></i></span>
                    <div class="form-control rounded-end text-break">
                        {{ $room_info[$i]->creator_id }}
                    </div>
                </div>
                <div class="input-group my-4 text-center">
                    <div class="form-control rounded-start bg-dark text-light d-flex justify-content-center align-items-center">
                        <div class="w-100">
                            Room name
                        </div>
                    </div>
                    <span class="input-group-text bg-dark text-light"><i class="bi bi-card-heading"></i></span>
                    <div class="form-control rounded-end text-break">
                        {{ $room_info[$i]->room_name }}
                    </div>
                </div>
                <div class="input-group my-4 text-center">
                    <div class="form-control rounded-start bg-dark text-light d-flex justify-content-center align-items-center">
                        <div class="w-100">
                            Room UUID
                        </div>
                    </div>
                    <span class="input-group-text bg-dark text-light"><i class="bi bi-card-text"></i></span>
                    <div class="form-control rounded-end text-break">
                        {{ $room_info[$i]->room_uuid }}
                    </div>
                </div>
                <div class="input-group my-4 text-center">
                    <div class="form-control rounded-start bg-dark text-light d-flex justify-content-center align-items-center">
                        <div class="w-100">
                            Type
                        </div>
                    </div>
                    <span class="input-group-text bg-dark text-light"><i class="bi bi-file-earmark-lock"></i></span>
                    <div class="form-control rounded-end text-break">
                        {{ $room_info[$i]->type }}
                    </div>
                </div>
                <div class="input-group my-4 text-center">
                    <div class="form-control rounded-start bg-dark text-light d-flex justify-content-center align-items-center">
                        <div class="w-100">
                            Status
                        </div>
                    </div>
                    <span class="input-group-text bg-dark text-light"><i class="bi bi-globe2"></i></span>
                    <div class="form-control rounded-end text-break">
                        {{ $room_info[$i]->status }}
                    </div>
                </div>
                <div class="input-group my-4 text-center">
                    <div class="form-control rounded-start bg-dark text-light d-flex justify-content-center align-items-center">
                        <div class="w-100">
                            Created at
                        </div>
                    </div>
                    <span class="input-group-text bg-dark text-light"><i class="bi bi-plus-circle-dotted"></i></span>
                    <div class="form-control rounded-end text-break">
                        {{ $room_info[$i]->created_at }}
                    </div>
                </div>
                <div class="w-100 btn-group">
                    <a class="w-100 btn btn-info rounded-0" href="/manageRoom/modlist/{{$room_info[$i]->id}}">
                        Manage moderator list
                    </a>
                    <a class="w-100 btn btn-danger rounded-0 rounded-start-1" href="/manageRoom/banlist/{{$room_info[$i]->id}}">
                        Manage ban list
                    </a>
                    @if ($room_info[$i]->type == "private")
                    <a type="button" href="/manageRoom/allowedMemmber/{{$room_info[$i]->id}}" class="btn btn-warning w-100 rounded-0 " >
                        Manage allowed members
                    </a>
                    @endif
                    <a type="button" href="/manageRoom/manageMember/{{$room_info[$i]->id}}" class="btn w-100 btn-info rounded-0 rounded-end-1" >
                        Manage members
                    </a>
                </div>
                <div class="input-group mt-4 d-flex justify-content-center">
                    <button type="button" class="btn btn-danger rounded" data-bs-toggle="modal" data-bs-target="#remove_room_{{$room_info[$i]->id}}">
                        Remove Room
                    </button>
                    <div class="modal fade" id="remove_room_{{$room_info[$i]->id}}" tabindex="-1" aria-labelledby="remove_room_{{$room_info[$i]->id}}_lable" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content bg-dark">
                                <form action="/manageRoom/remvoe_room" method="POST">
                                    <div class="modal-header">
                                        Remove Rome
                                    </div>
                                    <div class="modal-body text-center">
                                        @csrf
                                        <input type="hidden" name="room_info" class="d-none" value="{{$room_info[$i]->id}}">
                                        <small inert>pls type 'remove room with id {{$room_info[$i]->id}}'</small>
                                        <input type="text" name="confirm_text" class="form-control text-center" autocomplete="off" placeholder="remove room with id {{$room_info[$i]->id}}" required>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-center">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-danger">Remove Room</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endfor
</div>
@endsection
