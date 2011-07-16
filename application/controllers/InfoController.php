<?php

class InfoController extends Zend_Controller_Action {
    public function creditsAction() {
        $this->view->title = "Credits";
    }
    public function aboutAction() {
        $this->view->title = "About";
    }
    public function bugAction() {
        $this->view->title = "Report Bug";
    }
    public function featureAction() {
        $this->view->title = "Request Feature";
    }
    public function faqAction() {
        $this->view->title = "FAQ";
    }
}