<?php
/**
 * BaseCronkCategory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $cc_id
 * @property string $cc_name
 * @property integer $cc_visible
 * @property integer $cc_position
 * @property timestamp $cc_created
 * @property timestamp $cc_modified
 * @property Doctrine_Collection $CronkCategoryCronk
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCronkCategory extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('cronk_category');
        $this->hasColumn('cc_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('cc_name', 'string', 45, array(
             'type' => 'string',
             'length' => 45,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cc_visible', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cc_position', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cc_created', 'timestamp', null, array(
             'type' => 'datetime',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cc_modified', 'timestamp', null, array(
             'type' => 'datetime',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('CronkCategoryCronk', array(
             'local' => 'cc_id',
             'foreign' => 'ccc_cc_id'));
    }
}