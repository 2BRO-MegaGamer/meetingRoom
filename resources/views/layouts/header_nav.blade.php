<div id="header_users" class="z-3">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark ">
        <div class="container-fluid">
            <a class="navbar-brand" href="/home"><img src="/imgs/bigone.jpg" class="rounded" style="height:35px;width:35px"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 text-center">
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="/mR/create" >Create</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/mR/joinTo/null">Join</a>
                    </li>
                </ul>
                <div class="d-flex justify-content-center">
                    @if (Auth::check())
                        @include('layouts.header_details.user_acc_ch')
                    @else
                        <a class="btn btn-outline-light" href="/login">log in</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
</div>

