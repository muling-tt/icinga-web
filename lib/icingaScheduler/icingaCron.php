<?php

define("CRON_DEFAULT_LOG_FILE","/var/log/icingaCron/cron.log");
include "IcingaCronJobInterface.php";

class icingaCron implements IcingaCronJobInterface {
	protected $action = "";
	protected $verbose = false;
	protected $parser;
	protected $logfile = CRON_DEFAULT_LOG_FILE;
	
	public function getAction() {
		return $this->action;
	}
	public function isVerbose() {
		return $this->verbose;
	}
	public function getParser() {
		return $this->parser;
	}
	public function getLogfile() {
		return $this->logfile;
	}
	
	public function setAction($action) {
		$this->action = $action;
	}
	public function setVerbose($bool) {
		$this->verbose = $bool;
	}
	public function setParser(CronJobParser $parser) {
		$this->parser = $parser;
	}
	public function setLogfile($logfile) {
		$this->logfile = $logfile;
	}
	
	static public function __autoload($class) {
		$base = dirname(__FILE__);
		if(file_exists($base."/".str_replace("_","/",$class).".class.php"))		
			require_once ($base."/".str_replace("_","/",$class).".class.php");
	}
	
	static public function printHelpToConsole($argv) {
		echo "\nUSAGE: ".$argv[0]." [OPTIONS]\n".
			  "\t--verbose\t\tUse verbose logging\n".
			  "\t--exec \t\t\tExecute schedule\n".
			  "\t--print \t\tPrint schedule\n".
		      "\t--useXML [FILE]\t\tParse and execute jobs in [FILE]\n".
			  "\t--useDB [Modelname]\t\tParse information from table [TABLE]\n". 
			  "\t--useAgavi \t\tParse information from schedules.xml\n".
			  "\t\t\t\t(only works with Agavi)\n".
			  "\n\t--useAgavi, --useXML and --useDB are mutually exclusive\n";
	}
	
	public function __construct(array $params = array(),$verbose = false) {	
		$this->setVerbose($verbose);
		$this->setAction($params["action"]);
		try {
			switch($params["type"]) {
				case 'XML':
					$this->setParser(CronJobParser::createParser("XML",$params["src"],$this->isVerbose()));
					break;
				case 'table':
					$this->setParser(CronJobParser::createParser("DB",$params["src"],$this->isVerbose()));
					break;
				case 'agavi':
					$this->setParser(CronJobParser::createParser("Agavi","",$this->isVerbose()));	
					break;
			}
			if($this->getAction() == "exec")
					$this->execute();
		} catch(Exception $e) {
			$this->log($e->getMessage(),true);
		}
	}
	
	static public function consoleInterface($argv) { 
		$pos;
		$action = "";
		$verbose = false;
		$params = array();
		if(in_array("--exec",$argv))
			$params["action"] = "exec";
		if(in_array("--print",$argv))
			$params["action"] = "print";
		if(in_array("--verbose",$argv))
			$verbose = true;
		if($pos = array_search("--useXML",$argv))	{
			$params["type"] = "XML";
			$params["src"]  = $argv[$pos+1];
		}
		if($pos = array_search("--useTable",$argv))	{
			$params["type"] = "table";
			$params["src"]  = $argv[$pos+1];
		}
		if($pos = array_search("--useAgavi",$argv))	{
			$params["type"] = "agavi";
		}

		return new self($params,$verbose);
	}
	
	static public function parseXML($xml) {
		new self($false,$xml);
	}
	
	static public function parseTable($table) {
		new self($false,null,$table);
	}
	
	
	public function execute() {
		CronJobMetaProvider::getInstance()->processStarted();
		$parser = $this->getParser();

		if(!$parser instanceof CronJobParser)
			throw new RuntimeException("No parser found!");
		$jobs = $parser->getCronJobs();
		if(!empty($jobs)) {
			foreach($parser->getCronJobs() as $cron)  {
				try {
					$cron->execute();
				} catch(Exception $e) {
					$this->log($e->getMessage()." \n".$e->getLine()."\n Trace: \n".$e->getTrace(),true);
				}
			}
		}
		CronJobMetaProvider::getInstance()->processFinished();
	}	
	
	public function log($logMsg,$isError = false) {
		$msg = "\n";
		if($isError)
			$msg .= "[ERROR] ";
		else 
			$msg .= "[INFO] ";

		$msg .= $logMsg." (".date("c").")";
		file_put_contents($this->getLogFile(),$msg,FILE_APPEND);
		if($this->isVerbose())
			echo $msg."\n";
	}
	
}
spl_autoload_register("icingaCron::__autoload");

if($argv)
	icingaCron::consoleInterface($argv);