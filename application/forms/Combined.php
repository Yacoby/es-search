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



class Default_Form_Combined extends Zend_Form {

    protected function getActiveSubFormName() {
        if ( isset($_COOKIE['CurrentSearchType']) ) {
            if ( in_array($_COOKIE['CurrentSearchType'], array('Simple', 'Advanced')) ) {
                return $_COOKIE['CurrentSearchType'];
            }
        }
        return 'Simple';
    }

    protected function getActiveSubForm() {
        return $this->getSubForm($this->getActiveSubFormName());
    }
    protected function getInactiveSubFormName() {
        $conv = array(
                'Simple'    => 'Advanced',
                'Advanced'  => 'Simple'
        );
        return $conv[$this->getActiveSubFormName()];
    }

    protected function getInactiveSubForm() {
        return $this->getSubForm($this->getInactiveSubFormName());
    }


    public function init() {
        $this->addSubForm(new Default_Form_Index(), 'Simple');
        $this->addSubForm(new Default_Form_Search(), 'Advanced');

    }

    public function render(Zend_View_Interface $view = null) {
        if ($view !== null) {
            $this->setView($view);
        }
        return  "<div id='activeForm' name='{$this->getActiveSubFormName()}'>"
                . "{$this->getActiveSubForm()->render()}</div>"
                . "<input id='inactiveForm' type='hidden' "
                . "name='{$this->getInactiveSubFormName()}' "
                . "value='{$this->getInactiveSubForm()->render()}'</input>"
                . "<a href='#' id='formSwapLink'></a>";
    }

    public function __toString() {
        try {
            $return = $this->render();
            return $return;
        } catch (Exception $e) {
            $message = "Exception caught by form: " . $e->getMessage()
                    . "\nStack Trace:\n" . $e->getTraceAsString();
            trigger_error($message, E_USER_WARNING);
            return '';
        }
    }

    public function setAction($action) {
        foreach ( $this->getSubForms() as $sf ){
            $sf->setAttrib('action', (string) $action);
        }
    }

}