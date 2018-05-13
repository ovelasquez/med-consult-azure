$(document).ready(function () {
    $('.proyectos li').hover(showMore, hideMore )
});

function showMore(event){
    event.preventDefault();
    $(this).children(".more_info").slideDown("slow");
}

function hideMore(){
    event.preventDefault();
    $(this).children(".more_info").slideUp("slow");
}