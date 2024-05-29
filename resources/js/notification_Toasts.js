function show_notification(type,header,body,autohide) {
    var basic_toasts = `
<div class="toast-container position-fixed top-0 start-50 w-75 translate-middle-x p-3" style="z-index:100000">
  <div class="toast z-3 m-auto rounded" role="alert" aria-live="assertive" aria-atomic="true" `+((autohide!=true)?`data-bs-autohide="false"`:"")+` style="background-color:none;min-width:75%;z-index: 9999999;">
    <div class="toast-header bg-`+type+`">
      <img src="/imgs/icon.png" class="rounded me-2" width="25px" alt="...">
      <div class="w-100 text-center"><strong class="">`+header+`</strong></div>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  <div class="toast-body text-center">`+body+`</div>
  </div>
</div>

    `;
    document.getElementById("toasts_div").innerHTML =(basic_toasts);
    new bootstrap.Toast(document.querySelector('.toast')).show();
}
