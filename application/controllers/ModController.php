<?php

class ModController extends Zend_Controller_Action {

    protected function nameToUrlName($str) {
        return str_replace(
                ' ',
                '-',
                preg_replace('/[^a-zA-Z0-9 ]/', '', (string)$str)
        );

    }

    public function addAction(){
        $form = new Default_Form_Add();
        $this->view->form = $form;

        $req = $this->getRequest();

        $params = $req->getParams();
        if ( get_magic_quotes_gpc() ) {
            $params = array_map('stripslashes', $params);
        }
        if ( $req->isPost() && $form->isValid($params) ){
            $url = new Search_Url($params['url']);
            if ( $url->isValid() ){

            }else{
                $this->view->error = 'The url isn\'t valid';
            }
        }
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
                    'controller'    => 'mod',
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
        $this->view->IsAdult = $mod->isAdult();

        $this->view->Locations = $mod->getLocations();
    }

}
