<?php

/**
 * The basic layout of how the scraper should look
 *
 * The key function here is the scrape function, which should be overridden
 * in subclasses
 */
abstract class Search_Parser_AbstractScraper {

    private $_options = array();

    public function setOptions(array $options) {
        foreach ( $options as $key => $value ) {
            $this->_options[$key] = $value;
        }
    }
    public function setOption($key, $value) {
        $this->_options[$key] = $value;
    }
    public function getOption($key) {
        return $this->_options[$key];
    }
    public function hasOption($key){
        return array_key_exists($key, $this->_options);
    }

    /**
     * This is the entry point to your scraper
     */
    public abstract function scrape();

}
