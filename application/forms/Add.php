<?php
class Default_Form_Add extends Zend_Form {

    public function init(){
        $e = $this->createElement('text', 'url')
                  ->setRequired(false)
                  ->setLabel('Url')
                  ->setValue('http://')
                  ->setAttrib('size', '50');
        $this->addElement($e);

        $e = $this->createElement('submit', 'add')
                ->setIgnore(true)
                ->setLabel('Add Mod');
        $this->addElement($e);


        $this->setDecorators(array('FormElements','Form'));
        $this->setElementDecorators(array('ViewHelper', 'Errors'));

        $this->setMethod('post');
    }
}