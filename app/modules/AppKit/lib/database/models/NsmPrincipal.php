<?php

/**
 * NsmPrincipal
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6401 2009-09-24 16:12:04Z guilhermeblanco $
 */
class NsmPrincipal extends BaseNsmPrincipal
{
	const TYPE_ROLE = 'role';
	const TYPE_USER = 'user';
	
	public function setUp() {
		parent::setUp();
		
		$this->hasMany('Cronk', array (
			'local'		=> 'cpc_principal_id',
			'foreign'	=> 'cpc_cronk_id',
			'refClass'	=> 'CronkPrincipalCronk'
		));
	}
}