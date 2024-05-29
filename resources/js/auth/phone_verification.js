const inputs_codes =  document.querySelectorAll("[verification_input]");
inputs_codes.forEach((element)=>{
    if (element != null) {
        element.addEventListener("input",(e)=>{
            is_input_full();
            if (e.data == "-" || e.data == "+") {
                element.value = (element.value).slice(0,1);
            }
            if ((element.value).length == 1) {

                var next_input =("[verification_input|='"+(parseInt(element.getAttribute('verification_input'))+1)+"']");
                if (document.querySelector(next_input) != null) {
                    document.querySelector(next_input).focus();
                }
            }else if ((element.value).length > 1) {
                element.value = (element.value).slice(0,1);

            }
        })
        element.addEventListener("keydown",(e)=>{
            if ((element.value).length == 0 && e.key == "Backspace") {
                var prevent_input =("[verification_input|='"+(parseInt(element.getAttribute('verification_input'))-1)+"']");
                if (document.querySelector(prevent_input) != null) {
                    document.querySelector(prevent_input).focus();
                }
            }
        })
    }
})

function is_input_full() {
    var bool_test = true;
    inputs_codes.forEach((element)=>{

        if((element.value).length == 0){
            bool_test = false;
        }
    });
    if (bool_test) {
        document.getElementById("verify_btn").disabled=false;
    }
}

