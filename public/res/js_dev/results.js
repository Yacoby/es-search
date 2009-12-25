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

//change the href from a link to an onclick
$(function() {
    $(".modLink").each(function(){
        $(this).data('link', $(this).attr('href'));
        $(this).attr('href', '#'+$(this).attr('id'));
    });
});

//make the accordion an accordion
$(function() {
    $("#accordion").accordion();
});


$(function() {

    //With the element, e, it sets the accordian value with the data held in e
    function setDetails(e){
        var json = e.data('acc');
        var acc = $('#accordion');

        if ( !json.Location ){
            errorMessageBox(
                '<b>The monkeys have escaped</b><br />\
                This indicates an unknown error has occured when trying to get\
                the mod details. Men with bananas have been dispatched to\
                restore normality'
            );
            return;
        }

        acc.html('');
        for ( var i = 0; i < json.Location.length; i++ ){
            var l = json.Location[i]
            acc.append('\
                <h3><a href="#">'+l.Host+'</a></h3>\
                <div>\
                <strong>Download</strong>:\
                    <a href="'+l.URL+'" target="_blank">'+l.Host+'</a><br />\
                <strong>Version</strong>: '+l.Version+'<br />\
                <strong>Category</strong>: '+l.Category+'<br />\
                <strong>Description:</strong><div class="description">'
                +l.Description.toString().replace('\n', '<br />')+
                '</div></div>');
        }
        acc.accordion('destroy').accordion();
    }

    //gets information from the server and sets it
    $(".modLink").click(function(){
        var e = $(this);

        $('#link').html(
            '<a href="'+e.data('link')+'" target="_blank">Link to Details</a>'
            );

        if ( !e.data('acc') ){
            $('#accordion').html(
                'Dispatching infinate monkeys...<br />\
                Monkey 1............. Done<br />\
                Monkey 2............. Done<br />\
                Monkey 3............. Done<br />\
                Monkey 4.........'
            );
            server = jQuery.Zend.jsonrpc({
                url: 'json-rpc/ModInformation.php'
            });
            e.data('acc', server.getModDetails(e.attr('id')));
            setDetails(e);

        }else{
            setDetails(e);
        }
    });


});
