<?php
/**
 * BaseCronk
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $cronk_id
 * @property string $cronk_uid
 * @property string $cronk_name
 * @property string $cronk_description
 * @property string $cronk_xml
 * @property timestamp $cronk_created
 * @property string $cronk_modified
 * @property integer $cronk_user_id
 * @property Doctrine_Collection $NsmUser
 * @property Doctrine_Collection $CronkCategoryCronk
 * @property Doctrine_Collection $CronkPrincipalCronk
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCronk extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('cronk');
        
        $this->hasColumn('cronk_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('cronk_uid', 'string', 45, array(
             'type' => 'string',
             'length' => 45,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cronk_name', 'string', 45, array(
             'type' => 'string',
             'length' => 45,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cronk_description', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cronk_xml', 'string', null, array(
             'type' => 'string',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cronk_user_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cronk_created', 'timestamp', null, array(
             'type' => 'datetime',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cronk_modified', 'string', 45, array(
             'type' => 'datetime',
             'length' => 45,
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
             'local' => 'cronk_id',
             'foreign' => 'ccc_cronk_id'));

        $this->hasMany('CronkPrincipalCronk', array(
             'local' => 'cronk_id',
             'foreign' => 'cpc_cronk_id'));        
        $this->hasOne('NsmUser', array(
             'local' => 'cronk_user_id',
             'foreign' => 'user_id'));
    }
}