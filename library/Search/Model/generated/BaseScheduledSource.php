<?php

/**
 * BaseScheduledSource
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property string $name
 * @property integer $mod_source_id
 * @property integer $hours_delta
 * @property integer $last_run_time
 * @property string $url_prefix
 * @property boolean $enabled
 * @property ModSource $ModSource
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseScheduledSource extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('scheduled_source');
        $this->hasColumn('name', 'string', 32, array(
             'type' => 'string',
             'primary' => true,
             'length' => '32',
             ));
        $this->hasColumn('mod_source_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('hours_delta', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('last_run_time', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('url_prefix', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('enabled', 'boolean', null, array(
             'type' => 'boolean',
             'default' => true,
             'notnull' => true,
             ));


        $this->index('mod_source_id', array(
             'fields' => 
             array(
              0 => 'mod_source_id',
             ),
             ));
        $this->option('type', 'InnoDB');
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('ModSource', array(
             'local' => 'mod_source_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}