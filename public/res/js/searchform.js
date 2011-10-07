$(document).ready(function() {

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

    $('#formSwapLink').click(function(){
        tf.html(bf.attr('value'));

        if ( $.cookie('SelectedGame') ){
            $('#tempForm #game').val($.cookie('SelectedGame'));
        }

        af.toggle('blind', {}, 500);
        tf.toggle('blind', {}, 500, function(){
            bf.attr('value', af.html());
            af.html(tf.html());
            af.show();
            tf.hide();

            //needs to be here as well as it reverts when swapping html
            if ( $.cookie('SelectedGame') ){
                $('#game').val($.cookie('SelectedGame'));
            }
        });

        $.cookie('CurrentSearchType', $(this).html());

        //swap the data for the link names over
        var t = $(this).data('other');
        $(this).data('other', $(this).html());
        $(this).html(t);
    });

    $('#game').live('change', function(){
        $.cookie('SelectedGame', $(this).val());
    });
});
