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
 * Default search form on the search
 *
 */
class Default_Form_Search extends Zend_Form {

    /**
     * @return void
     */
    protected function addTextElem($name, $lab) {
        $e = $this->createElement('text', $name)
                ->addValidator('stringLength', false, array(0, 32))
                ->setRequired(false)
                ->setLabel($lab);
        $this->addElement($e);
    }

    public function init() {
        $this->addTextElem('name', 'Name:');
        $this->addTextElem('author', 'Author:');
        $this->addTextElem('description', 'Description:');

        $e = $this->createElement('select', 'game')
                ->addMultiOption('MW', 'Morrowind')
                ->addMultiOption('OB', 'Oblivion')
                ->setLabel('Game:')
                ->setRequired(true);
        if ( isset($_COOKIE['SelectedGame']) ) {
            $e->setValue($_COOKIE['SelectedGame']);
        }
        $this->addElement($e);
        

        $e = $this->createElement('submit', 'act')
                ->setLabel('Search')
                ->setIgnore(true);
        $this->addElement($e);

        $this->setMethod('get');

        $this->setDefaulDec();
    }

    public function setDefaulDec() {
        $this->setDecorators(array(
                'FormElements',
                array(
                        'HtmlTag',
                        array('tag' => 'table')
                ),
                'Form'
        ));

        $this->setElementDecorators(array(
                'ViewHelper',
                'Errors',
                array(
                        'decorator' => array('td' => 'HtmlTag'),
                        'options' => array('tag' => 'td')
                ),
                array(
                        'label',
                        array('tag' => 'td')
                ),
                array(
                        'decorator' => array('tr' => 'HtmlTag'),
                        'options' => array('tag' => 'tr')
                ),
        ));

    }
}
