<?php
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

/**
 * Default search form on the index page
 *
 */
class Default_Form_Index extends Zend_Form {

    public function init() {
        $general = $this->createElement('text', 'general');
        $general->addValidator('stringLength', false, array(0, 32))
            ->setRequired(true)->setLabel("Search:");
        $this->addElement($general);

        $s = new Zend_Form_Element_Select('game');
        $s->addMultiOption('MW', 'Morrowind');
        $s->addMultiOption('OB', 'Oblivion');
        $this->addElement($s);

        $this->setMethod('get');
        $this->addElement('submit', 'act', array(
            'ignore'   => true,
            'label'    => 'Search',
        ));

        $this->setDecorators(array('FormElements','Form'));

        $this->setElementDecorators(array('ViewHelper', 'Errors'));

    }
}
