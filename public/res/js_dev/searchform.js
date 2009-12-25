$(function() {

    if ( $('#formSwapLink').length !== 1 ){
        return;
    }


    //set the html for the button
    $("#formSwapLink").show()
    .data('other', $('#activeForm').attr('name'))
    .html($('#inactiveForm').attr('name'));

    var af = $('#activeForm');
    var bf = $('#inactiveForm');
    af.after('<div id="tempForm"></div>');

    var tf = $('#tempForm').hide();

    //center the advanced form table
    //TODO FIX IN CSS
    $('#activeForm table').attr('style', 'margin:auto');
    $('#inactiveForm table').attr('style', 'margin:auto');

    $('#formSwapLink').click(function(){
        tf.html(bf.attr('value'));

        $('#tempForm table').attr('style', 'margin:auto');


        af.toggle('blind', {}, 500);
        tf.toggle('blind', {}, 500, function(){
            bf.attr('value', af.html());
            af.html(tf.html());
            af.show();
            tf.hide();
        });

        $.cookie('CurrentSearchType', $(this).html());

        //swap the data for the link names over
        var t = $(this).data('other');
        $(this).data('other', $(this).html());
        $(this).html(t);
    });

});