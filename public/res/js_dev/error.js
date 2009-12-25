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


function errorMessageBox(msg, title){
    title = title != null ? title : 'Error'
    jQuery(document.body).append('<div title="'+title+'" id="tmp-error"></div>');
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