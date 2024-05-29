@php
use App\Models\Profile_img_bio;
use App\Http\Controllers\Profile;
use App\Http\Controllers\HeaderController;
use App\Http\Controllers\UserStatusController;
@endphp
<div class="dropdown d-inline">
    <div class="row text-light">
        <div class="col m-auto text-center">
            {{auth()->user()->userName}}
            <small class="p-0 m-0 opacity-50">{{auth()->user()->id}}</small>
        </div>
        <div class="col" style="max-width: fit-content">
            <button id="info_user_btn" class="btn btn-secondary dropdown-toggle text-truncate p-1" style="max-width:fit-content" data-bs-toggle="dropdown" aria-expanded="false"></button>
            <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="/seeprofile">Profile</a></li>
                <li><form action="/logout" method="post"> @csrf<button class="dropdown-item" >log out</button></form></li>
            </ul>
        </div>
    </div>
    </div>
<?php
$user_hash_id = auth()->user()->userName."_".auth()->user()->id;
$user_token = password_hash($user_hash_id,PASSWORD_DEFAULT);
?>
<script defer>
    const username = '{{auth()->user()->userName}}';
    const user_id = '{{auth()->id()}}';
    const user_token = '{{$user_token}}';
</script>
