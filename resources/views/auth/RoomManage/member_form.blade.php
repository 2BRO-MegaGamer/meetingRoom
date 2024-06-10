@extends('layouts.layout')
<?php
$title="Member list";
$Members_ids = array_keys($Members);
?>
@section('content')
<script>const room_id = {{ $room_id }};</script>

<h2 class="w-100 text-center text-light mt-4">Member List</h2>
<div class="table-responsive mt-5">
    <table class="table table-dark w-100" id="memberlist">
        <thead>
            <tr class="text-center">
                <th scope="col">#</th>
                <th scope="col">First Name</th>
                <th scope="col">Last Name</th>
                <th scope="col">Username</th>
                <th scope="col">Gender</th>
                <th scope="col">Status</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @for ($i = 0; $i < count($Members_ids); $i++)
                <tr class="text-center" id="{{ $Members_ids[$i] }}_row">
                    <th scope="row"><small>{{ $Members_ids[$i] }}</small></th>
                    <td>{{ $Members[$Members_ids[$i]]->firstName }}</td>
                    <td>{{ $Members[$Members_ids[$i]]->lastName }}</td>
                    <td>{{ $Members[$Members_ids[$i]]->userName  }}</td>
                    <td>{{ $Members[$Members_ids[$i]]->gender }}</td>
                    <td>{{ $Members[$Members_ids[$i]]->status }}</td>
                    <td>
                        <div class="btn-group ">
                            <button class="btn fs-5 btn-danger p-0 px-1 m-0" title="ban" user_id="{{ $Members_ids[$i] }}"  id="ban_member"><i class="bi bi-exclamation-triangle"></i></button>
                            <button class="btn fs-5 btn-secondary p-0 px-1 m-0" title="warn" data-bs-toggle="modal" data-bs-target="#warn_modal_{{ $Members_ids[$i] }}" id="warn_member"><i class="bi bi-exclamation-lg"></i></button>
                        </div>
                        <div class="modal fade" id="warn_modal_{{ $Members_ids[$i] }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content bg-dark">
                                    <div class="modal-header d-flex flex-column">
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="{{ $Members_ids[$i] }}_close_btn"></button>
                                        <div class="w-100 d-block">
                                            {{ $Members[$Members_ids[$i]]->userName }}
                                        </div>
                                        <small class="w-100 d-block text-secondary">
                                            {{ $Members_ids[$i] }}
                                        </small>
                                    </div>
                                    <div class="modal-body">
                                        <div class="input-group ">
                                            <span class="input-group-text bg-dark text-light" >text warning :</span>
                                            <textarea class="form-control" name="" id="{{ $Members_ids[$i] }}_textarea_warning"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn w-100 btn-outline-warning" user_id="{{ $Members_ids[$i] }}" id="submit_warn">submit</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
@vite(['resources/js/auth/ban_mod_list.js'])

@endsection