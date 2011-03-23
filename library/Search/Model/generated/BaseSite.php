<?php

/**
 * BaseSite
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $mod_source_id
 * @property string $host
 * @property string $base_url
 * @property string $mod_url_prefix
 * @property integer $byte_limit
 * @property integer $bytes_used
 * @property integer $bytes_last_updated
 * @property integer $next_update
 * @property boolean $enabled
 * @property ModSource $ModSource
 * @property Doctrine_Collection $Pages
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseSite extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('site');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'autoincrement' => true,
             ));
        $this->hasColumn('mod_source_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('host', 'string', 255, array(
             'primary' => true,
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('base_url', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('mod_url_prefix', 'string', null, array(
             'type' => 'string',
             'notnull' => true,
             ));
        $this->hasColumn('byte_limit', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             ));
        $this->hasColumn('bytes_used', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             'notnull' => true,
             ));
        $this->hasColumn('bytes_last_updated', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             ));
        $this->hasColumn('next_update', 'integer', null, array(
             'type' => 'integer',
             'default' => 0,
             'notnull' => true,
             ));
        $this->hasColumn('enabled', 'boolean', null, array(
             'type' => 'boolean',
             'default' => true,
             'notnull' => true,
             ));


        $this->index('id', array(
             'fields' => 
             array(
              0 => 'id',
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

        $this->hasMany('Page as Pages', array(
             'local' => 'id',
             'foreign' => 'site_id'));
    }
}