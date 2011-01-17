<?php

/**
 * BaseCookeJar
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $domain
 * @property blob $data
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCookeJar extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('cooke_jar');
        $this->hasColumn('domain', 'string', 255, array(
             'type' => 'string',
             'primary' => true,
             'length' => '255',
             ));
        $this->hasColumn('data', 'blob', null, array(
             'type' => 'blob',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}