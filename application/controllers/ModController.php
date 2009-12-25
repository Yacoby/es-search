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

class ModController extends Zend_Controller_Action {

    protected function nameToUrlName($str) {
        return str_replace(
                ' ',
                '-',
                preg_replace('/[^a-zA-Z0-9 ]/', '', (string)$str)
        );

    }

    public function detailsAction() {
        $request = $this->getRequest();

        $id = $request->getParam("id", -1);
        if ( !is_numeric($id) || $id === -1 ) {
            throw new Exception("Wrong mod id");
        }


        $mod = new Default_Model_Mod($id);

        $urlReqName = $request->getParam('name', '');
        $urlActName = $this->nameToUrlName($mod->getName());

        if ( $urlActName != $urlReqName ) {

            $goto = array(
                    'controller'    =>'mod',
                    'action'        => 'details',
                    'id'            => $id,
                    'name'          => $urlActName
            );

            $redirector = $this->_helper->getHelper('Redirector');
            $redirector->setCode(301)
                    ->gotoRoute($goto, 'mod')
                    ->redirectAndExit();
        }




        $this->view->Name = $this->view->title = $mod->getName();
        $this->view->Author = $mod->getAuthor();

        $this->view->Locations = $mod->getLocations();
    }

}