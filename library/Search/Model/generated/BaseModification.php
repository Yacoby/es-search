<?php

/**
 * BaseModification
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $name
 * @property string $author
 * @property Doctrine_Collection $Games
 * @property Doctrine_Collection $GameMods
 * @property Doctrine_Collection $Locations
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
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('Game as Games', array(
             'refClass' => 'GameMods',
             'local' => 'modification_id',
             'foreign' => 'game_id'));

        $this->hasMany('GameMods', array(
             'local' => 'id',
             'foreign' => 'modification_id'));

        $this->hasMany('Location as Locations', array(
             'local' => 'id',
             'foreign' => 'modification_id'));
    }
}