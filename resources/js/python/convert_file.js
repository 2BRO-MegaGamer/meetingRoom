
function apply_event(result,type) {
    type = (type == undefined)?"":type;
    var run_python = true;
    if (result != undefined) {
        var type_of_convert_file = document.getElementById("type_of_convert_file");
        $(type_of_convert_file).removeClass("d-none")
        type_of_convert_file.innerText = result.innerText;
        $("#btn_select_to").addClass("d-none");
        var convert_btn =document.getElementById("convert_btn");
        convert_btn.disabled = false;
        convert_btn.addEventListener("click",()=>{
            if (run_python) {
                run_python_file(result.innerText);
                convert_btn.disabled = true;
                run_python = false;
            }
        })
        
    }else{
        var r = new Resumable({
            target:'/python/convert_file/upload_file',
            query:{_token:$('meta[name="csrf-token"]').attr('content')},
            headers:{
                'Accept':'application/json',
            },
            testChunks:false,
            throttleProgressCallbacks:1
        });
        r.assignBrowse($("#upload_file_btn")[0]);
        r.on('fileAdded', function (file) {
            r.upload();
        });
        r.on('fileProgress', function (file) {
            var percent = Math.floor(file.progress() * 100);
            $('#upload_file_progress').width(percent + '%');
        });
        r.on('fileSuccess', function (file, response) {
            var show_file_uploaded = document.getElementById("show_file_uploaded");
            var filename_tag = document.createElement("div");
            $(filename_tag).addClass("w-100");
            var file_name = (file.file).name;
            filename_tag.innerText = (file.file).name;
            show_file_uploaded.append(filename_tag);
            $('#upload_file_progress').width(100 + '%');
            var from_type = file_name.split(".");
            from_type = from_type[(from_type.length-1)]
            document.getElementById("type_of_your_file").innerText = from_type;
            document.getElementById("btn_select_to").disabled = false;
            $("#btn_select_to").removeClass("d-none");
            document.getElementById("upload_file_btn").disabled = true;
            document.getElementById("btn_select_to").addEventListener("click",()=>{
                make_modal_ready(from_type);
            });
        });
    }
}
function make_modal_ready(type) {
    var type_support = {
        audio:['8svx','aac','ac3','aiff','amb','amr','ape','au','avr','caf','cdda','cvs','cvsd','cvu','dss','dts','dvms','fap','flac','fssd','gsm','gsrt','hcom','htk','ima','ircam','m4a','m4r','maud','mp2','mp3','nist','oga','ogg','opus','paf','prc','pvf','ra','sd2','shn','sln','smp','snd','sndr','sndt','sou','sph','spx','tak','tta','txw','vms','voc','vox','vqf','w64','wav','wma','wv','wve','xa'],
        video:['3g2','3gp','aaf','asf','av1','avchd','avi','cavs','divx','dv','f4v','flv','hevc','m2ts','m2v','m4v','mjpeg','mkv','mod','mov','mp4','mpeg','mpeg-2','mpg','mts','mxf','ogv','rm','rmvb','swf','tod','ts','vob','webm','wmv','wtv','xvid'],
        image:['3fr','arw','avif','bmp','cr2','crw','cur','dcm','dcr','dds','dng','erf','exr','fax','fts','g3','g4','gif','gv','hdr','heic','heif','hrz','ico','iiq','ipl','jbg','jbig','jfi','jfif','jif','jnx','jp2','jpe','jpeg','jpg','jps','k25','kdc','mac','map','mef','mng','mrw','mtv','nef','nrw','orf','otb','pal','palm','pam','pbm','pcd','pct','pcx','pdb','pef','pes','pfm','pgm','pgx','picon','pict','pix','plasma','png','pnm','ppm','psd','pwp','raf','ras','rgb','rgba','rgbo','rgf','rla','rle','rw2','sct','sfw','sgi','six','sixel','sr2','srf','sun','svg','tga','tiff','tim','tm2','uyvy','viff','vips','wbmp','webp','wmz','wpg','x3f','xbm','xc','xcf','xpm','xv','xwd','yuv'],
        document:['abw','aw','csv','dbk','djvu','doc','docm','docx','dot','dotm','dotx','html','kwd','odt','oxps','pdf','rtf','sxw','txt','wps','xls','xlsx','xps'],
        archiv:['7z','ace','alz','arc','arj','cab','cpio','deb','jar','lha','rar','rpm','tar','tar.7z','tar.bz','tar.lz','tar.lzma','tar.lzo','tar.xz','tar.z','tbz2','tgz','zip'],
    };

    var all_type = Object.keys(type_support);
    var witch_format;
    for (let i = 0; i < all_type.length; i++) {
        var found = type_support[all_type[i]].find((type_1) => type_1 == type);
        if (found != undefined) {
            witch_format = all_type[i];
        }
    }
    if (witch_format != undefined) {
        switch (witch_format) {
            case "audio":
                delete type_support.video;
                delete type_support.image;
                delete type_support.document;
                delete type_support.archiv;
                break
            case "video":
                delete type_support.document;
                delete type_support.archiv;
                break
            case "image":
                delete type_support.video;
                delete type_support.audio;
                delete type_support.document;
                delete type_support.archiv;
                break
            case "document":
                break
            case "archiv":
                delete type_support.video;
                delete type_support.audio;
                delete type_support.image;
                delete type_support.document;
                break
        }
    }
    all_type = Object.keys(type_support);
    var all_btns = [];
    var accordion_select_type =document.getElementById("accordion_select_type");
    if ((accordion_select_type.children).length == 0) {
        for (let i = 0; i < all_type.length; i++) {
            var key_btn = document.createElement("button");
            $(key_btn).addClass("accordion-button collapsed bg-dark text-light border-bottom ");
            key_btn.type = "button";
            key_btn.setAttribute("data-bs-toggle","collapse");
            key_btn.setAttribute("data-bs-target","#"+all_type[i]+"_select");
            key_btn.setAttribute("aria-expanded","true");
            key_btn.setAttribute("aria-controls",all_type[i]+"_select");
            key_btn.innerText = (all_type[i]);
            var first_div = document.createElement("div");
            $(first_div).addClass("accordion-item row  bg-dark border-0 p-0 m-0");
            first_div.innerHTML = `
                <div class="accordion-header col p-0 m-0"></div>
                <div class="col overflow-y-auto" style="max-height:40vh">
                    <div id="`+all_type[i]+`_select" data-bs-parent="#accordion_select_type" class="accordion-collapse collapse" >
                        <div class="w-100 p-0 m-0 row"></div>
                    </div>
                </div>
            `;
            for (let y = 0; y < type_support[all_type[i]].length; y++) {
                var select_btn = document.createElement("button");
                all_btns.push(select_btn);
                select_btn.innerText = type_support[all_type[i]][y];
                $(select_btn).addClass("w-100 h-100 m-0 btn btn-outline-light text-uppercase");
                var col_div = document.createElement("div");
                $(col_div).addClass("col p-0 m-0");
                col_div.innerHTML = '<div class="p-1 badge fw-bold"></div>';
                (col_div.children[0]).append(select_btn);

                var btn_position = first_div.children[1].children[0].children[0];
                (btn_position).append(col_div);
            }
            first_div.children[0].append(key_btn);
            document.getElementById("accordion_select_type").append(first_div);
        }
    }
    for (let i = 0; i < all_btns.length; i++) {
        var select_btn = all_btns[i];
        select_btn.select_btn = select_btn
        select_btn.all_btns = all_btns
        select_btn.addEventListener("click",send_result);
        
    }
    function send_result(event) {
        var btn = event.currentTarget.select_btn;
        var all_btns = event.currentTarget.all_btns;
        apply_event(btn);
        for (let i = 0; i < all_btns.length; i++) {
            all_btns[i].removeEventListener("click",send_result)
        }
        $("#close_select_type_modal_btn")[0].click();
        $("#upload_file_modal_btn")[0].click();
        document.getElementById("accordion_select_type").innerHTML = "";
    }
    type_select_modal_btn.click()
}
function run_python_file(to_type) {
    $.ajax({
        type: "POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/python/convert_file/run_python_file',
        data: {
            _token : $('meta[name="csrf-token"]').attr('content'),
            to_type:to_type
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
                if (data[all_file[i]].message == "done") {
                    var download_btn = document.createElement("button");
                    $(download_btn).addClass("btn btn-outline-success");
                    download_btn.innerText = "download";
                    (first_div.children[1]).append(download_btn);
                    download_btn.addEventListener("click",()=>{
                        var download_popout = window.open(("convert_file/file/"+data[all_file[i]].url), "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top=0,left=0,width=10,height=10");
                        download_popout.onblur=()=>{
                            download_popout.onfocus=()=>{
                                download_popout.close();
                            }
                        }
                    })
                }else{
                    (first_div.children[1]).innerText = "ERROR";
                }
            }
            document.getElementById("upload_file_modal_btn").removeAttribute("data-bs-toggle");
            document.getElementById("upload_file_modal_btn").innerText = "Refresh";
            $("#upload_file_modal_btn").removeClass("btn-outline-info");
            $("#upload_file_modal_btn").addClass("btn-outline-warning");
            document.getElementById("upload_file_modal_btn").addEventListener("click",(e)=>{
                location.reload();
            })
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
    };``
})