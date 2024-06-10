@extends('layouts.layout')
<?php
$title="Allowed member list";
$accept_m_ids = array_keys($accept_m);
?>
@section('content')
<script>const room_id = {{ $room_id }};</script>

<h2 class="w-100 text-center text-light mt-4">Allowed member List</h2>
<div class="table-responsive mt-5">
    <table class="table table-dark w-100" id="allowedlist">
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
            @for ($i = 0; $i < count($accept_m_ids); $i++)
                <tr class="text-center" id="{{ $accept_m_ids[$i] }}_row">
                    <th scope="row"><small>{{ $accept_m_ids[$i] }}</small></th>
                    <td>{{ $accept_m[$accept_m_ids[$i]]->firstName }}</td>
                    <td>{{ $accept_m[$accept_m_ids[$i]]->lastName }}</td>
                    <td>{{ $accept_m[$accept_m_ids[$i]]->userName  }}</td>
                    <td>{{ $accept_m[$accept_m_ids[$i]]->gender }}</td>
                    <td>{{ $accept_m[$accept_m_ids[$i]]->status }}</td>
                    <td><button class="btn btn-danger p-0 px-1 m-0" user_id="{{ $accept_m_ids[$i] }}"  id="remove_from_list">Remove</button></td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
@vite(['resources/js/auth/ban_mod_list.js'])

@endsection