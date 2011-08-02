<?php

require dirname(__FILE__).'/HTMLDom/dom.php';

/**
 * Extends the simple_html_dom class to support the zend naming conventions,
 * while allowing updates to be dropped in without modification
 */
class Search_Parser_SimpleHtmlDom extends simple_html_dom{}
