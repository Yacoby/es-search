<?php

class IndexController extends Zend_Controller_Action{
    public function indexAction(){
        $this->view->searchForm = new Default_Form_Combined();
        $this->view->searchForm->init();
    }
}