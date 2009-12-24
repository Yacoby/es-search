$(document).ready(function(){
    $('#icons li').hover(
        function() {
            if ( $(this).children().get(0).tagName.toString().toLowerCase() == 'a'){
                $(this).addClass('ui-state-hover');
            }
        },
        function() {
            $(this).removeClass('ui-state-hover');
        }
    )
});