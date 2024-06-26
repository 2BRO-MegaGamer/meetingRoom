
function apply_event() {
    var run_python = true;
    var file_type = ['wav','mp3','m4a','ogg'];
    var r = new Resumable({
        target:'/python/volume_changer',
        query:{_token:$('meta[name="csrf-token"]').attr('content')},
        fileType:file_type,
        headers:{
            'Accept':'application/json',
        },
        testChunks:false,
        throttleProgressCallbacks:1
    });
    r.assignBrowse($("#upload_file_btn")[0]);
    $("#upload_file_btn")[0].click();
    r.on('fileAdded', function (file) {
        r.upload();
    });
    r.on('fileProgress', function (file) {
        var percent = Math.floor(file.progress() * 100);
        $('#upload_file_progress').width(percent + '%');
        document.getElementById("volume_changer_btn").disabled=true;
    });
    r.on('fileSuccess', function (file, response) {
        var show_file_uploaded = document.getElementById("show_file_uploaded");
        var audio_tag = document.createElement("audio");
        audio_tag.setAttribute("controls","")
        $(audio_tag).addClass("w-100");
        audio_tag.src = URL.createObjectURL(file.file);
        show_file_uploaded.append(audio_tag);
        $('#upload_file_progress').width(100 + '%');
        document.getElementById("volume_changer_btn").disabled=false;
        if (run_python) {
            document.getElementById("upload_file_btn").disabled = true;
            document.getElementById("volume_changer_btn").addEventListener("click",()=>{
                document.getElementById("show_result").innerText = '';
                run_python_file();
                document.getElementById("volume_changer_btn").disabled=true;
            });
            run_python =false;
        }
    });
    r.on('fileError', function (file, response) {
        console.log('file uploading error.' ,file, response);
    });
}
function run_python_file() {
    $.ajax({
        type: "POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/python/volume_changer/run_python_file',
        data: {
            _token : $('meta[name="csrf-token"]').attr('content'),volume:document.getElementById("volume_changer_input").value
        },
        success: function(data) {
            var all_file = Object.keys(data);
            $("#progress_close_btn")[0].click();
            for (let i = 0; i < all_file.length; i++) {
                var first_div = document.createElement("div");
                $(first_div).addClass("w-100 row mb-3 rounded border border-1 border-light p-0 m-0");
                first_div.innerHTML = `
                    <div class="col m-auto">
                        `+all_file[i]+`
                    </div>
                    <div class="col"></div>
                `;
                document.getElementById("show_result").append(first_div);
                var download_btn = document.createElement("button");
                $(download_btn).addClass("btn btn-outline-success");
                download_btn.innerText = "download";
                (first_div.children[1]).append(download_btn);
                download_btn.addEventListener("click",download_file);
                function download_file() {
                    window.open(("volume_changer/file/"+data[all_file[i]].url), "_blank");
                    download_btn.disabled = true;
                    $(download_btn).removeClass("btn-outline-success");
                    $(download_btn).addClass("btn-danger");
                    download_btn.innerText = "Removed";
                    download_btn.removeEventListener("click",download_file);
                }
            }
            $("#card_footer").removeClass("d-none");
            var show_file_uploaded = document.getElementById("show_file_uploaded");
            show_file_uploaded.innerHTML = '';
            $('#upload_file_progress').width(0 + '%');
            document.getElementById("volume_changer_btn").disabled=true;
            document.getElementById("upload_file_btn").disabled = false;
        },
        error: function(e){
            console.log(e);
        }
    });
    
}

window.addEventListener("DOMContentLoaded",()=>{
    apply_event();
    window.onbeforeunload = function() {
        return "Data will be lost if you leave the page, are you sure?";
    };
})