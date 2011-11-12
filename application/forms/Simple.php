<?php

/**
 * Default search form on the index page
 *
 */
class Default_Form_Simple extends Zend_Form {

    public function init() {
        $e = $this->createElement('text', 'general')
                ->addValidator('stringLength', false, array(0, 32))
                ->setRequired(true)
                ->setLabel("Search:");
        $this->addElement($e);

        $e = $this->createElement('select', 'game')
                ->addMultiOption('3', 'Morrowind')
                ->addMultiOption('4', 'Oblivion')
                //->addMultiOption('5', 'Skyrim')
                ->setRequired(true);
        if ( isset($_COOKIE['SelectedGame']) ) {
            $e->setValue($_COOKIE['SelectedGame']);
        }
        $this->addElement($e);

        $e = $this->createElement('submit', 'act')
                ->setLabel('Search')
                ->setIgnore(true);
        $this->addElement($e);

        $e = $this->createElement('hidden', 'page')
                ->setValue('1');
        $this->addElement($e);


        $this->setMethod('get');

        $this->setDecorators(array('FormElements','Form'));
        $this->setElementDecorators(array('ViewHelper', 'Errors'));

    }
}
