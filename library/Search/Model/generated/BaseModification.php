<?php

/**
 * BaseModification
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $author
 * @property integer $game_id
 * @property Game $Game
 * @property Doctrine_Collection $Locations
 * @property ModSource $ModSource
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseModification extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('modification');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('name', 'string', null, array(
             'type' => 'string',
             'notblank' => true,
             ));
        $this->hasColumn('author', 'string', null, array(
             'type' => 'string',
             'notblank' => true,
             ));
        $this->hasColumn('game_id', 'integer', null, array(
             'type' => 'integer',
             ));


        $this->index('game_id', array(
             'fields' => 
             array(
              0 => 'game_id',
             ),
             ));
        $this->option('encoding', 'utf8');
        $this->option('charset', 'utf8');
        $this->option('collate', 'utf8_unicode_ci');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Game', array(
             'local' => 'game_id',
             'foreign' => 'id'));

        $this->hasMany('Location as Locations', array(
             'local' => 'id',
             'foreign' => 'modification_id'));

        $this->hasOne('ModSource', array(
             'local' => 'id',
             'foreign' => 'modification_id'));
    }
}