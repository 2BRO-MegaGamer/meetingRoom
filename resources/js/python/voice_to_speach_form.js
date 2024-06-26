
function apply_event() {
    var language_support = ['af-ZA','sq-AL','am-ET','ar-DZ','ar-BH','ar-EG','ar-IQ','ar-IL','ar-JO','ar-KW','ar-LB','ar-MR','ar-MA','ar-OM','ar-QA','ar-SA','ar-PS','ar-SY','ar-TN','ar-AE','ar-YE','hy-AM','az-AZ','eu-ES','bn-BD','bn-IN','bs-BA','bg-BG','my-MM','ca-ES','yue-Hant-HK','zh (cmn-Hans-CN)','zh-TW (cmn-Hant-TW)','hr-HR','cs-CZ','da-DK','nl-BE','nl-NL','en-AU','en-CA','en-GH','en-HK','en-IN','en-IE','en-KE','en-NZ','en-NG','en-PK','en-PH','en-SG','en-ZA','en-TZ','en-GB','en-US','et-EE','fil-PH','fi-FI','fr-BE','fr-CA','fr-FR','fr-CH','gl-ES','ka-GE','de-AT','de-DE','de-CH','el-GR','gu-IN','iw-IL','hi-IN','hu-HU','is-IS','id-ID','it-IT','it-CH','ja-JP','jv-ID','kn-IN','kk-KZ','km-KH','rw-RW','ko-KR','lo-LA','lv-LV','lt-LT','mk-MK','ms-MY','ml-IN','mr-IN','mn-MN','ne-NP','no-NO','fa-IR','pl-PL','pt-BR','pt-PT','pa-Guru-IN','ro-RO','ru-RU','sr-RS','si-LK','sk-SK','sl-SI','st-ZA','es-AR','es-BO','es-CL','es-CO','es-CR','es-DO','es-EC','es-SV','es-GT','es-HN','es-MX','es-NI','es-PA','es-PY','es-PE','es-PR','es-ES','es-US','es-UY','es-VE','su-ID','sw-KE','sw-TZ','ss-Latn-ZA','sv-SE','ta-IN','ta-MY','ta-SG','ta-LK','te-IN','th-TH','ts-ZA','tn-Latn-ZA','tr-TR','uk-UA','ur-IN','ur-PK','uz-UZ','ve-ZA','vi-VN','xh-ZA','zu-ZA',]
    language_support.forEach(lang=>{
        if ((lang.split('-')).length < 3 && (lang.split(' ')).length < 2) {
            document.getElementById("language_select").innerHTML += '<option class="bg-dark text-center text-light" value="'+lang+'">'+lang+'</option>'
        }
    })
    var run_python = true;
    var lang_selected = false;
    document.getElementById("upload_file_btn").addEventListener("click",()=>{
        if (document.getElementById("language_select").value == '') {
            $("#language_select").addClass("border border-1 border-danger");
            r = undefined;
        }else{
            if (!lang_selected) {
                lang_selected = true;
                var file_type = ['wav','mp3','m4a','ogg'];
                var r = new Resumable({
                    target:'/python/voice_to_text',
                    query:{_token:$('meta[name="csrf-token"]').attr('content'),language:document.getElementById("language_select").value},
                    fileType:file_type,
                    headers:{
                        'Accept':'application/json',
                    },
                    testChunks:false,
                    throttleProgressCallbacks:1
                });
                $("#language_select").removeClass("border border-1 border-danger");
                r.assignBrowse($("#upload_file_btn")[0]);
                $("#upload_file_btn")[0].click();
                r.on('fileAdded', function (file) {
                    r.upload();
                });
            
                r.on('fileProgress', function (file) {
                    var percent = Math.floor(file.progress() * 100);
                    $('#upload_file_progress').width(percent + '%');
                    document.getElementById("to_text_btn").disabled=true;
                });
            
                r.on('fileSuccess', function (file, response) {
                    var show_file_uploaded = document.getElementById("show_file_uploaded");
                    var audio_tag = document.createElement("audio");
                    audio_tag.setAttribute("controls","")
                    $(audio_tag).addClass("w-100");
                    audio_tag.src = URL.createObjectURL(file.file);
                    show_file_uploaded.append(audio_tag);

                    $('#upload_file_progress').width(100 + '%');
                    document.getElementById("to_text_btn").disabled=false;
                    if (run_python) {
                        document.getElementById("upload_file_btn").disabled = true;
                        document.getElementById("to_text_btn").addEventListener("click",()=>{
                            document.getElementById("show_result").innerText = '';
                            $("#card_footer").addClass("d-none")
                            run_python_file();
                            document.getElementById("to_text_btn").disabled=true;
                        });
                        run_python =false;
                    }
                });
            
                r.on('fileError', function (file, response) {
                    console.log('file uploading error.' ,file, response);
                });
            }
        }
    })

}
function run_python_file() {
    $.ajax({
        type: "POST",
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url:'/python/voice_to_text/run_python_file',
        data: {
            _token : $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(data) {
            if (data[0] == "done") {
                $.ajax({
                    type: "POST",
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:'/python/voice_to_text/get_result',
                    data: {
                        _token : $('meta[name="csrf-token"]').attr('content'),
                    },
                    success: function(data) {
                        document.getElementById("language_select").value = '';
                        document.getElementById("upload_file_btn").disabled = false;
                        document.getElementById("show_file_uploaded").innerHTML = '';
                        var card_footer = document.getElementById("card_footer");
                        $(card_footer).removeClass("d-none")
                        var result = data;
                        document.getElementById("copy_result").addEventListener("click",()=>{
                            navigator.clipboard.writeText(result);
                            $("#copy_result").removeClass("text-light");
                            var old_value = document.getElementById("copy_result").innerHTML;
                            document.getElementById("copy_result").innerHTML = '<i class="bi bi-clipboard-check-fill"></i>';
                            $("#copy_result").addClass("text-success");
                            setTimeout(()=>{
                                $("#copy_result").addClass("text-light");
                                document.getElementById("copy_result").innerHTML = old_value;
                                $("#copy_result").removeClass("text-success");
                            },2000)
                        })
                        document.getElementById("upload_file_progress").style.width = "0%";
                        document.getElementById("progress_close_btn").click();
                        document.getElementById("show_result").innerText = result;
                    },
                });
            }
        },
        error: function(e){
            console.log(e);
        }
    });
    
}

window.addEventListener("DOMContentLoaded",()=>{
    apply_event();
})