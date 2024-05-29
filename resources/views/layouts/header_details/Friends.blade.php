<div class="modal fade" id="see_freind_list" tabindex="-1" aria-labelledby="see_freind_list_Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content text-light" style="height:800px;background:rgba(0, 0, 0, 0.779)">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="see_freind_list_Label">Friend List</h1>
          <input type="search" class="form-control form-control-dark text-bg-info" placeholder="Search..." aria-label="Search" style="width: 40%;">
          <a href="/findFriends" class="btn btn-secondary" >find friend</a>
        </div>
        <div class="modal-body" style="height: 70%;">
          <p>Friends</p>
          <ol class="list-group " id="Friend_both_req">
            <p class="mx-auto">You don't have friends yet</p>
          </ol>
          
        </div>
        <div class="modal-body" id="test_height" style="height: 200px;margin-top:20px">
          <p>add Freind</p>
          <ol class="list-group" id="Friend_get_req">
            <p class="mx-auto">You have no friend requests yet</p>
          </ol>
          <p>Friend request send</p>
          <ol class="list-group" id="Friend_send_req">
            <p class="mx-auto"> You have not sent a friend request to anyone</p>
          </ol>
        </div>
      </div>
    </div>
  </div>
