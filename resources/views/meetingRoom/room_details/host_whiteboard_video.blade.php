
<div class="w-100 h-100">
    <div class="w-100 d-flex justify-content-center align-items-center" style="min-height: 95%">
        <div class="m-auto text-center d-none">
            <div class="spinner-grow m-auto" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div id="host_video_div" class="w-100 " >
            <video id="host_video_tag" src="" class="w-100 placeholder" preload ></video>
        </div>
    </div>
    <div class="w-100" style="min-height: 5%;max-height:5%;height:5%;">
        <div class="w-100 h-100">
            <div class="w-50 h-100 m-auto">
                <div class="row p-0 m-0 h-100" style="background: rgba(0, 0, 0, 0.267)">
                    <button use_WMSR="webcam" class="btn col text-light fs-4"><i class="bi bi-camera-video"></i></button>
                    <button use_WMSR="audio" class="btn col text-light fs-4" id="use_mic_btn" mute="true"><i class="bi bi-mic-mute"></i></button>
                    <button use_WMSR="video" class="btn col fs-4 text-light"><i class="bi bi-display"></i></button>
                    <button use_WMSR="raisehand" class="btn col fs-4 text-light"><i class="bi bi-hand-index"></i></button>
                    
                </div>
            </div>
        </div>
    </div>
</div>