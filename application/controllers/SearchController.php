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


class SearchController extends Zend_Controller_Action {

	private function setTitle($request) {
		$params = $request->getParams();
		if ( get_magic_quotes_gpc() ) {
			$params = array_map('stripslashes', $params);
		}
		
		$searchString = isset($params['general']) ? $params['general'] : null;

		if ( !$searchString ) {
			$searchString = array();
			foreach ( array('name', 'author', 'description') as $k ) {
				if ( isset($params[$k]) && trim($params[$k]) != '' ) {
					$searchString[] = $params[$k];
				}
			}

			$searchString = implode(' & ', $searchString);
		}

		$searchString .= ' : Page ' . (isset($_GET['page']) ? $_GET['page'] : 1);
		$this->view->title = $searchString;
	}

	/**
	 * Should google index this page?
	 */
	private function setGoogleIndex() {
		$page = isset($_GET['page']) ? $_GET['page'] : 1;
		if ( $page > 1 ) {
			$this->view->index = false;
		}
	}

	/**
	 * @todo consider what would happen if neither form was valid
	 */
	public function resultsAction() {
		$this->setGoogleIndex();

		$this->view->paginator = Zend_Paginator::factory(array());
		$this->view->searchForm = new Default_Form_Combined();

		$request = $this->getRequest();
		if ($request->isGet()) {
			$this->setTitle($request);

			$params = $request->getParams();
			if ( get_magic_quotes_gpc() ) {
				$params = array_map('stripslashes', $params);
			}
			//look through possible forms
			foreach ($this->view->searchForm->getSubForms() as $f) {
				if ( $f->isValid($params) ) {

					$this->view->searchForm->setActiveSubForm($f->getName());

					$page = isset($_GET['page']) ? $_GET['page'] : 1;

					if ( !is_numeric($page) || $page < 1 ) {
						throw new Exception('Invalid page');
					}

					//TODO magic number. Maybe have it in a session...
                    $numPerPage = 10;
					$si = new Default_Model_Search($f->getValues(),
                                                   $numPerPage*($page-1),
                                                   $numPerPage);

					if ( $page != 1 && $page > $si->count() ) {
						throw new Exception('Invalid page');
					}

					$paginator = new Zend_Paginator(
						new SearchResults_Paginator($si)
					);
					$paginator->setItemCountPerPage($numPerPage);

					$paginator->setCurrentPageNumber($page);

					$this->view->paginator = $paginator;

					break;
				}else {
					/*
                     * This is a bad fix for an issue. After trying isvalid, the forms
                     * then show errors. As we want to avoid it on forms we aren't using
                     * we must create a new form if it isn't valid.
                     *
                     * This has one huge downside that I need to look at, what happens
                     * if neither form is valid?
					*/
					$cname = get_class($f);
					$this->view->searchForm->addSubForm(new $cname, $f->getName());
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
		$this->_search  = $s;
		$this->_results = $this->_search->getResults();
	}

	public function count() {
		return $this->_search->count();
	}
	public function getItems($offset, $itemCountPerPage) {
		return $this->_results;
	}
}