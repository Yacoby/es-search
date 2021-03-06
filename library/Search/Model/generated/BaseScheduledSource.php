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
             'default' => 0,
             ));
        $this->hasColumn('last_run_time', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             ));


        $this->index('mod_source_id_idx', array(
             'fields' => 
             array(
              0 => 'mod_source_id',
             ),
             ));
        $this->option('encoding', 'utf8');
        $this->option('charset', 'utf8');
        $this->option('collate', 'utf8_unicode_ci');
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