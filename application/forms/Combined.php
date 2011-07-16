<?php

/**
 * Combines two forms, holding them both as subforms.
 */
class Default_Form_Combined extends Zend_Form {

    private $_active;

    public function  __construct() {
        $this->_active = 'Simple';
        
        if ( isset($_COOKIE['CurrentSearchType']) ) {
            if ( in_array($_COOKIE['CurrentSearchType'], array('Simple', 'Advanced')) ) {
                $this->_active = (string)$_COOKIE['CurrentSearchType'];
            }
        }
        
        $this->addSubForm(new Default_Form_Simple(), 'Simple');
        $this->addSubForm(new Default_Form_Advanced(), 'Advanced');
    }

    public function setActiveSubForm($name){
        assert(in_array($name, array('Simple', 'Advanced')));
        $this->_active = $name;
    }

    /**
     * Gets the name of the active sub form. This is based on a cookie or
     * if the cookies doesn't exist, it defaults to 'Simple'.
     *
     * The cookie value has to be either 'Advanced' or 'Simple' else it reverts
     * to the default value
     *
     * @return string the active subforms name.
     */
    protected function getActiveSubFormName() {
        return $this->_active;
    }

    /**
     * Gets the subform with the active subforms name
     *
     * @return Zend_Form
     */
    protected function getActiveSubForm() {
        return $this->getSubForm($this->getActiveSubFormName());
    }


    /**
     * Gets the name of the inactive subform.
     *
     * @return string
     */
    protected function getInactiveSubFormName() {
        $conv = array(
                'Simple'    => 'Advanced',
                'Advanced'  => 'Simple'
        );
        return $conv[$this->getActiveSubFormName()];
    }
    /**
     * Gets the subform with the inactive subforms name
     *
     * @return Zend_Form
     */
    protected function getInactiveSubForm() {
        return $this->getSubForm($this->getInactiveSubFormName());
    }

    public function init() {
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

    /**
     * Unlike Zend_Form::setAction, this propagates all actions to subforms.
     */
    public function setAction($action) {
        foreach ( $this->getSubForms() as $sf ) {
            $sf->setAttrib('action', (string) $action);
        }
    }

}