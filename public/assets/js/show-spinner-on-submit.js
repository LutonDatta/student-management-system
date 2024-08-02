/* Show spinner on all sumit form automatically. Just include this file on the form. */
document.addEventListener("DOMContentLoaded", function(){
    $("form").submit(function(){
        /* Do not make it disable right now, it will stop sending yes along with form. */
        $(this).find("button[type=submit]:focus").html('<span class="spinner-border spinner-border-sm"></span> Saving...');
        /* Form submitted, now disable it to prevent duplicate submit. */
        svBtnSbtObjToUseTimeout = $(this);
        setTimeout(function(){ svBtnSbtObjToUseTimeout.prop("disabled",true); },1000);
    });
});

document.addEventListener('DOMContentLoaded',function(){
    $(".btnShowSpinOnClk").click(function(e){
        var btn = $(this);
        var spin = '<span class="spinner-border spinner-border-sm"></span> ' + btn.text();
        btn.html(spin);
    });
});