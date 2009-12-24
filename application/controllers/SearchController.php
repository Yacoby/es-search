<?php /* l-b
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
 * l-b */ ?>

<?php

class SearchController extends Zend_Controller_Action {

    private function setTitle($request) {
        $searchString = $request->getParam('general', null);
        if ( !$searchString ) {
            $searchString = array();
            if ( $request->getParam('name', null) != null ) {
                $searchString[] = $request->getParam('name', '');
            }

            if ( $request->getParam('author', null) != null ) {
                $searchString[] = $request->getParam('author');
            }

            if ( $request->getParam('description', null) != null ) {
                $searchString[] = $request->getParam('description');
            }

            $searchString = implode(' & ', $searchString);
        }
        $this->view->title = $searchString;
    }

    public function resultsAction() {
        $this->view->paginator = Zend_Paginator::factory(array()); //default paginator
        $this->view->form = new Default_Form_Search(); //default form

        $request = $this->getRequest();
        if ($request->isGet()) {
            $this->setTitle($request);


            //possible forms
            $forms    = array(
                    new Default_Form_Index(),
                    new Default_Form_Search(),
            );

            //look through possible forms
            foreach ($forms as $f) {
                if ( $f->isValid($request->getParams())) {

                    $page = isset($_GET['page']) ? $_GET['page'] : 1;

                    if ( !is_numeric($page) || $page < 1 ) {
                        throw new Exception('Invalid page');
                    }


                    $si = new Default_Model_Search($f->getValues(), 15*($page-1), 15);

                    $paginator = new Zend_Paginator(
                            new SearchResults_Paginator($si)
                    );
                    $paginator->setItemCountPerPage(15);

                    $paginator->setCurrentPageNumber($page);

                    $this->view->paginator = $paginator;
                    $this->view->form = $f;

                    break;
                }
            }
        }
    }

}

class SearchResults_Paginator implements Zend_Paginator_Adapter_Interface {

    /**
     * @var Default_Model_Search
     */
    private $_search;
    /**
     * @var SearchResults
     */
    private $_results;
    public function __construct(Default_Model_Search $s) {
        $this->_search = $s;
        $this->_results = $this->_search->getResults();
    }

    public function count() {
        return $this->_results->count();
    }
    public function getItems($offset, $itemCountPerPage) {
        return $this->_results->results();
    }
}