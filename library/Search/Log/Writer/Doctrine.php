<?php
/**
 * Taken From:
 * https://github.com/mlurz71/parables/blob/master/Parables/Log/Writer/Doctrine.php
 *
 * Modifications made
 */
class Search_Log_Writer_Doctrine  extends Zend_Log_Writer_Abstract {
    protected $_modelClass = null;
    protected $_columnMap = array();

    /**
     *
     * @param   string $modelClass
     * @param   array $columnMap
     * @throws  Zend_Log_Exception
     */
    public function __construct($modelClass, $columnMap = null) {
        if (!is_string($modelClass)) {
            throw new Zend_Log_Exception('Invalid model class.');
        }
        if (!class_exists($modelClass)) {
            throw new Zend_Log_Exception('Invalid model class.');
        }

        $this->_modelClass = $modelClass;
        $this->_columnMap  = $columnMap;
    }

    /**
     * Disable formatting
     *
     * @param   mixed $formatter
     * @return  void
     * @throws  Zend_Log_Exception
     */
    public function setFormatter(Zend_Log_Formatter_Interface $formatter) {
        throw new Zend_Log_Exception('Formatting is not supported.');
    }

    /**
     * Write a message to the log
     *
     * @param   array $event
     * @return  void
     */
    protected function _write($event) {
        $dataToInsert = array();

        if ( $this->_columnMap === null ) {
            $dataToInsert = $event;
        } else {
            foreach ($this->_columnMap as $columnName => $fieldKey) {
                $dataToInsert[$columnName] = $event[$fieldKey];
            }
        }

        $entry = new $this->_modelClass();
        $entry->fromArray($dataToInsert);
        $entry->save();
        $entry->free(true);
        unset($entry);
    }

    static public function factory($config){
        throw new Zend_Log_Exception('Constructing via factory not supported.');
    }
}
