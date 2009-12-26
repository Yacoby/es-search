/* l-b
 * This file is part of ES Search.
 *
 * Copyright (c) 2009 Jacob Essex
 *
 * Foobar is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ES Search is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with ES Search. If not, see <http://www.gnu.org/licenses/>.
 * l-b */

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
});

$(function(){
    $('#game').live('change', function(){
        $.cookie('SelectedGame', $(this).val());
    });
});