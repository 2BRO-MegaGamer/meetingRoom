@extends('layouts.layout')
@php
    $title = 'profile';
    $room_ids = array_keys($room_detail);
    // dd($user_information);
@endphp
@section('content')
<div class="row w-100 m-0 p-0 mt-3">
    <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12">
        <ul class="list-group">
            <a class="list-group-item btn text-light bg-dark border border-success border-3 mx-2 my-1 " visible id="profile_menu">
                <div class="row w-100 h-100">
                    <div class="col-1 m-auto">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="col">
                        Public manage
                    </div>
                </div>
            </a>
            <a class="list-group-item btn btn-outline-dark mx-2 my-1" id="room_menu">
                <div class="row w-100 h-100">
                    <div class="col-1 m-auto">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="col">
                        Room manage
                    </div>
                </div>
            </a>
        </ul>
    </div>
    <div class="col mx-auto">
        <div class="w-100 h-100 my-3" id="profile">
            <div class="card bg-dark text-light">
                <div class="p-1 m-2 border-0 text-center">
                    <div class="input-group my-4 ">
                        <div class="form-control rounded-start">{{ auth()->user()->lastName }}</div>
                        <span class="input-group-text border border-2 border-bottom-0 border-top-0 border-secondary"><i class="bi bi-person"></i></span>
                        <div class="form-control rounded-end">{{ auth()->user()->firstName }}</div>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text border border-2 border-bottom-0 border-top-0 border-start-0 border-secondary" ><i class="bi bi-person"></i></span>
                        <div class="form-control text-center">{{auth()->user()->userName}}</div>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text border border-2 border-bottom-0 border-top-0 border-start-0 border-secondary" >@</span>
                        <div class="form-control text-center">{{$user_information[1]}}</div>
                        @if(auth()->user()->email_verified_at == null)
                            <a href="/email/verify" class="input-group-text btn btn-warning text-light" ><i class="bi bi-exclamation-diamond"></i></a>
                        @endif
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text border border-2 border-bottom-0 border-top-0 border-start-0 border-secondary" ><i class="bi bi-telephone"></i></span>
                        <div class="form-control text-center">{{$user_information[0]}}</div>
                        @if(auth()->user()->phone_number_verified_at == null)
                            <a href="/phone/verify" class="input-group-text btn btn-warning text-light" ><i class="bi bi-exclamation-diamond"></i></a>
                        @endif
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text border border-2 border-bottom-0 border-top-0 border-start-0 border-secondary" ><i class="bi bi-gender-ambiguous"></i></span>
                        <div class="form-control text-center">{{auth()->user()->gender}}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 h-100 my-3 d-none" id="rooms">
            @for ($i = 0; $i < count($room_ids); $i++)
                <div class="card bg-dark mb-2 text-light">
                    <div class="p-1 m-2 border-0">
                        <div class="w-100 mb-1 text-center fw-bold">
                            <small>{{$room_ids[$i]}}</small>
                        </div>
                        <div class="input-group my-4 text-center">
                            <div class="form-control rounded-start bg-dark text-light d-flex justify-content-center align-items-center">
                                <div class="w-100">
                                    Creator ID 
                                </div>
                            </div>
                            <span class="input-group-text bg-dark text-light"><i class="bi bi-person"></i></span>
                            <div class="form-control rounded-end text-break">
                                {{ $room_detail[$room_ids[$i]]->creator_id }}
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
                                {{ $room_detail[$room_ids[$i]]->room_name }}
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
                                {{ $room_detail[$room_ids[$i]]->room_uuid }}
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
                                {{ $room_detail[$room_ids[$i]]->type }}
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
                                {{ $room_detail[$room_ids[$i]]->status }}
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
                                {{ $room_detail[$room_ids[$i]]->created_at }}
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>
@vite(['resources/js/auth/profile.js'])


@endsection
