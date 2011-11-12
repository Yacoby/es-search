<?php

/**
 * Default search form on the search
 *
 */
class Default_Form_Advanced extends Zend_Form {

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
                  ->addMultiOption('3', 'Morrowind')
                  ->addMultiOption('4', 'Oblivion')
                  ->addMultiOption('5', 'Skyrim')
                  ->setLabel('Game:')
                  ->setRequired(true);
        if ( isset($_COOKIE['SelectedGame']) ) {
            $e->setValue($_COOKIE['SelectedGame']);
        }
        $this->addElement($e);

        $e = $this->createElement('hidden', 'page')
                ->setValue('1');
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
                        'options'   => array('tag' => 'td')
                ),
                array(
                        'label',
                        array('tag' => 'td')
                ),
                array(
                        'decorator' => array('tr' => 'HtmlTag'),
                        'options'   => array('tag' => 'tr')
                ),
        ));

    }
}
