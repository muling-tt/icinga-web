<?php
/**
 * Tests if the cache folders are accessible for the web user
 * 
 */
/**
* @depends testBootstrap 
*/	
class userRightsTest extends PHPUnit_Framework_TestCase {
	
	protected function setUp() {
		$core = AgaviConfig::get("core.root_dir");
		$this->sharedFixture = parse_ini_file($core."/etc/tests/test.properties");
	}
	
	/**
	* @depends testBootstrap 
	*/	
	public function testCacheDirsExist() {
		info("Running post installation checks \n");
		info("\tChecking if cache folders exist\n");
		$root = AgaviConfig::get("core.root_dir");
		$cacheFolders = array($root."/app/cache");
		$missingFolders = array();
		foreach($cacheFolders as $folder) {
			if(!file_exists($folder))
				$missingFolders[] = $folder;
		}

		if(!empty($missingFolders)) {
			$this->fail("Check if cache folders exist failed");
		} else 
			success("\tCache folders exist\n");
	}

	
	/**
	 * @depends testCacheDirsExist
	 */
	public function testCacheDirsAreWriteable() {
		info("\tTesting if web user can write to cache\n");
		$root = AgaviConfig::get("core.root_dir");
		$cacheFolders = array($root."/app/cache");

		$wwwUser = $this->sharedFixture['www-user'];
		$wwwGroup = $this->sharedFixture['www-group'];
		$nonWriteable = array();

		if(!$wwwUser)
			$this->markTestSkipped("No www-user specified in test.properties!");
		foreach($cacheFolders as $folder) {
			 
			exec("su ".$wwwUser." -c 'touch ".$folder."/testfile.txt'");
			if(!file_exists($folder."/testfile.txt")) {
				error("Web user ".$wwwUser." couldn't write to cache ".$folder.
					  "Please check that the either the user ".$wwwUser." or the group ".$wwwGroup." has ".
		 			  "write access to this folder - otherwise icinga-web won't work\n");
				$nonWriteable[] = $folder; 
			} else {
				unlink($folder."/testfile.txt");
			}
		}		
		if(!empty($nonWriteable)) {
			$this->fail("Cache permission check failed");
		}
		success("\tCache is writeable for web user\n");
	}
	
	/**
	 * Check if logs are writeable
	 */
	public function testLogDir() {
		info("\tTesting log write-access for web user\n");
		$root = AgaviConfig::get("core.root_dir");
		$logDir = $root."/app/data/log/";
		
		$wwwUser = $this->sharedFixture['www-user'];
		$wwwGroup = $this->sharedFixture['www-group'];
		
		exec("su ".$wwwUser." -c 'touch ".$logDir."/testfile.txt'");
		if(!file_exists($logDir."/testfile.txt")) {
			error("Web user ".$wwwUser." couldn't create logfile in ".$logDir.
				  "Please check that the either the user ".$wwwUser." or the group ".$wwwGroup." has ".
				  "write access to this folder - otherwise icinga-web won't work\n");
			$this->fail("Couldn't write log files");
						
		} else {
			success("\tLog-directory is writeable for wwwGroup!\n");
		}
	}	
	
}
