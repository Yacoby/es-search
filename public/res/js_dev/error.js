
function errorMessageBox(msg){
    jQuery(document.body).append('<div title="Error" id="tmp-error"></div>');
    $('#tmp-error')
        .html(msg)
        .dialog({
        autoOpen: true,
        width: 450,
        buttons: {
            "Ok": function() {
                $(this).dialog("close").remove();
            }
        }
    });

}