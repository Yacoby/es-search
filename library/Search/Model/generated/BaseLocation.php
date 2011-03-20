<?php

/**
 * BaseLocation
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $modification_id
 * @property string $mod_url_suffix
 * @property string $description
 * @property string $version
 * @property integer $int_version
 * @property integer $site_id
 * @property integer $category_id
 * @property Modification $Modification
 * @property Site $Site
 * @property Category $Category
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseLocation extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('location');
        $this->hasColumn('modification_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
        $this->hasColumn('mod_url_suffix', 'string', 255, array(
             'type' => 'string',
             'notblank' => true,
             'primary' => true,
             'length' => '255',
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('version', 'string', null, array(
             'type' => 'string',
             ));
        $this->hasColumn('int_version', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('site_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('category_id', 'integer', null, array(
             'type' => 'integer',
             ));


        $this->index('modification_id', array(
             'fields' => 
             array(
              0 => 'modification_id',
             ),
             ));
        $this->index('int_version', array(
             'fields' => 
             array(
              0 => 'int_version',
             ),
             ));
        $this->option('type', 'InnoDB');
        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Modification', array(
             'local' => 'modification_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('Site', array(
             'local' => 'site_id',
             'foreign' => 'id'));

        $this->hasOne('Category', array(
             'local' => 'category_id',
             'foreign' => 'id'));
    }
}