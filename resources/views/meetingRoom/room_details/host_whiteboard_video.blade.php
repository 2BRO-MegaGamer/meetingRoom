
<div class="w-100 h-100 position-relative">
    {{-- <div class="position-absolute start-0 d-block d-xxl-none d-xl-none d-lg-none">
        <button class="btn btn-outline-light rounded-0 rounded-end-5 " id="responsive_active_side_btn"><i class="bi bi-arrow-bar-right display-3"></i></button>
    </div> --}}
    <div class="w-100 d-xxl-flex d-xl-flex d-lg-flex justify-content-center align-items-center" style="min-height: 95%;max-height:95%;height:95%">
        <div id="host_video_div" class="w-100 h-50 d-flex justify-content-center align-items-center" >
            <video id="host_video_tag" src="" class="w-100 placeholder" preload ></video>
        </div>
        <div class="w-100 h-50 d-block d-xxl-none d-xl-none d-lg-none ">
            <div class="overflow-y-auto overflow-x-hidden  row row-cols-lg-1 row-cols-xl-1 row-cols-xxl-1 row-cols-md-4 row-cols-sm-2 row-cols-1 align-items-center p-0 m-0 h-100"  id="webcam_or_voice_2">
            </div>
        </div>
    </div>
    <div class="w-100" style="min-height: 5%;max-height:5%;height:5%;">
        <div class="w-100 h-100">
            <div class="col-lg-4 col-md-4 h-100 m-auto">
                <div class="row p-0 m-0 h-100" style="background: rgba(0, 0, 0, 0.267)">
                    <button use_WMSR="webcam" class="btn col text-light fs-4"><i class="bi bi-camera-video"></i></button>
                    <button use_WMSR="audio" class="btn col text-light fs-4" id="use_mic_btn" mute="true"><i class="bi bi-mic-mute"></i></button>
                    <button use_WMSR="video" class="btn col fs-4 text-light"><i class="bi bi-display"></i></button>
                    <button use_WMSR="raisehand" class="btn col fs-4 text-light" status="false"><i class="bi bi-hand-index"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>