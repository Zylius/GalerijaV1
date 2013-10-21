
$(document).ready(function(){
    $("#profile_button,#overlay").click(function(){
    showDiv();
    })
});

function showDiv() {

    div = document.getElementById('overlay_wrapper');
    if(div.style.display == "block")
        div.style.display = "none"
    else
        div.style.display = "block";
}