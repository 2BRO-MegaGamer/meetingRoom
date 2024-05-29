<div class="d-flex flex-column flex-shrink-0 h-100 w-100 position-relative" >
  <div class="position-absolute top-0 w-100" id="sidebar_btns" style="height: 93%">
    <div class="w-100 h-100 d-flex justify-content-center align-items-center ">
      <ul class="nav nav-pills flex-column w-100" >
        <li class="nav-item">
          <button sidebar="members" class="btn btn-dark w-100 text-center fs-2"><i class="bi bi-people"></i></button>
        </li>
        <li class="nav-item">
          <button sidebar="chatmessage" class="btn btn-dark w-100 text-center fs-2"><i class="bi bi-chat-right-dots"></i></button>
        </li>
        <li class="nav-item">
          <button sidebar="announcement" class="btn btn-dark w-100 text-center fs-2"><i class="bi bi-megaphone"></i></button>
        </li>
      </ul>
    </div>
  </div>
  <div class="w-100 position-absolute" style="bottom: 0%" id="sidebar_report_activity_leave">
    <div class="w-100 h-100 p-0 m-0 d-flex justify-content-center">
      <div class="w-100" >
        <button type="button" class="btn btn-warning d-block w-100 rounded-0"><i class="bi bi-flag"></i></button>
        <button type="button" class="btn btn-danger d-block w-100 rounded-0" onclick="location.replace('/')"><i class="bi bi-box-arrow-right"></i></button>
      </div>
    </div>
  </div>
  @include('meetingRoom.room_details.m_c_list')
</div>
