<?php

class Web_Icinga_ApiSearchAction extends IcingaWebBaseAction
{
	/**
	 * Returns the default view if the action does not serve the request
	 * method used.
	 *
	 * @return     mixed <ul>
	 *                     <li>A string containing the view name associated
	 *                     with this action; or</li>
	 *                     <li>An array with two indices: the parent module
	 *                     of the view to be executed and the view to be
	 *                     executed.</li>
	 *                   </ul>
	 */
	static	public $defaultColumns = array(
			"host" => array("HOST_ID",'HOST_OBJECT_ID','HOST_INSTANCE_ID',"HOST_NAME","HOST_ALIAS","HOST_DISPLAY_NAME","HOST_ADDRESS","HOST_IS_ACTIVE"), 
			"service" => array("SERVICE_ID","SERVICE_OBJECT_ID","SERVICE_IS_ACTIVE","SERVICE_INSTANCE_ID","SERVICE_NAME","SERVICE_DISPLAY_NAME","SERVICE_OUTPUT","SERVICE_PERFDATA"), 
			"hostgroup" => array("HOSTGROUP_ID","HOSTGROUP_OBJECT_ID","HOSTGROUP_INSTANCE_ID","HOSTGROUP_NAME","HOSTGROUP_ALIAS"), 
			"servicegroup" => array("SERVICEGROUP_ID","SERVICEGROUP_OBJECT_ID","SERVICEGROUP_INSTANCE_ID","SERVICEGROUP_NAME","SERVICEGROUP_ALIAS"), 
			"customvariable" => array("CUSTOMVARIABLE_ID","CUSTOMVARIABLE_OBJECT_ID","CUSTOMVARIABLE_INSTANCE_ID","CUSTOMVARIABLE_NAME","CUSTOMVARIABLE_VALUE","CUSTOMVARIABLE_MODIFIED","CUSTOMVARIABLE_UPDATETIME"), 
			"contact" => array("CONTACT_NAME","CONTACT_CUSTOMVARIABLE_NAME","CONTACT_CUSTOMVARIABLE_VALUE"), 
			"contactgroup" => array("CONTACTGROUP_ID","CONTACTGROUP_OBJECT_ID","CONTACTGROUP_INSTANCE_ID","CONTACTGROUP_NAME","CONTACTGROUP_ALIAS"), 
			"timeperiod" => array("TIMEPERIOD_ID","TIMEPERIOD_OBJECT_ID","TIMEPERIOD_INSTANCE_ID","TIMEPERIOD_NAME","TIMEPERIOD_ALIAS","TIMEPERIOD_DAY","TIMEPERIOD_STARTTIME","TIMEPERIOD_ENDTIME"), 
			"hoststatus" => array(), 
			"servicestatus" => array(), 
			"hosttimes" => array(),
			"servicetimes" => array(), 
			"config" => array("CONFIG_VAR_ID","CONFIG_VAR_INSTANCE_ID","CONFIG_VAR_NAME","CONFIG_VAR_VALUE"), 
			"program" => array(), 
			"log" => array("LOG_ID","LOG_INSTANCE_ID","LOG_TIME","LOG_ENTRY_TIME","LOG_ENTRY_TIME_USEC","LOG_TYPE","LOG_DATA","LOG_REALTIME_DATA","LOG_INFERRED_DATA"), 
			"host_status_summary" => array("HOST_ID",'HOST_STATUS_ALL','HOST_STATE','HOST_STATE_COUNT','HOST_LAST_CHECK'), 
			"service_status_summary" => array("SERVICE_ID",'SERVICE_STATUS_ALL','SERVICE_OUTPUT','SERVICE_LONG_OUTPUT','SERVICE_PERFDATA','SERVICE_LAST_CHECK'), 
			"host_status_history" => array("STATEHISTORY_ID","STATEHISTORY_INSTANCE_ID","STATEHISTORY_STATE_TIME","STATEHISTORY_OBJECT_ID","STATEHISTORY_STATE_CHANGE","STATEHISTORY_STATE","STATEHISTORY_OUTPUT","STATEHISTORY_LONG_OUTPUT"), 
			"service_status_history" => array("STATEHISTORY_ID","STATEHISTORY_INSTANCE_ID","STATEHISTORY_STATE_TIME","STATEHISTORY_OBJECT_ID","STATEHISTORY_STATE_CHANGE","STATEHISTORY_STATE","STATEHISTORY_OUTPUT","STATEHISTORY_LONG_OUTPUT"), 
			"host_parents" => array("HOST_ID",'HOST_OBJECT_ID',"HOST_NAME","HOST_PARENT_OBJECT_ID","HOST_PARENT_NAME"), 
			"notifications" => array("NOTIFICATION_ID","NOTIFICATION_TYPE","NOTIFICATION_REASON","NOTIFICATION_STARTTIME","NOTIFICATION_ENDTIME","NOTIFICATION_OUTPUT","NOTIFICATION_OBJECT_ID","NOTIFICATION_OBJECTTYPE_ID"), 
			"hostgroup_summary" => array('HOSTGROUP_SUMMARY_COUNT',"HOSTGROUP_ID","HOSTGROUP_OBJECT_ID","HOSTGROUP_NAME"), 
			"servicegroup_summary" => array('SERVICEGROUP_SUMMARY_COUNT',"SERVICEGROUP_ID","SERVICEGROUP_OBJECT_ID","SERVICEGROUP_NAME")
	);

	public function getDefaultViewName()
	{
		return 'Success';
	}

	public function checkAuth(AgaviRequestDataHolder $rd) {
		$user = $this->getContext()->getUser();
		$authKey = $rd->getParameter("authkey");
		$validation = $this->getContainer()->getValidationManager();
				
		if(!$user->isAuthenticated() && $authKey) {
			try {
				$user->doAuthKeyLogin($authKey);
			} catch(Exception $e) {
				$validation->setError("Login error","Invalid Auth key!");
				return false;							
			}
		}
		
		if(!$user->isAuthenticated()) {			
			$validation->setError("Login error","Not logged in!");
			return false;			
		}
		
		if($user->hasCredential("appkit.api.access"))
			return true;
		
		$validation->setError("Error","Invalid credentials for api access!");			
		return false;
	}
	
	public function executeRead(AgaviRequestDataHolder $rd) {
		if(!$this->checkAuth($rd))
			return "Error";
		
		$context = $this->getContext();
		$API = $context->getModel("Icinga.ApiContainer","Web");
		$target = $rd->getParameter("target");

		$search = @$API->createSearch()->setSearchTarget($target);
		$this->addFilters($search,$rd);

		$this->setColumns($search,$rd);
		$this->setGrouping($search,$rd);
		$this->setOrder($search,$rd);
		$this->setLimit($search,$rd);

		$search->setResultType(IcingaApiSearch::RESULT_ARRAY);

		// Adding security principal targets to the query
		IcingaPrincipalTargetTool::applyApiSecurityPrincipals($search);

		$search = $search->fetch()->getAll();
		$rd->setParameter("searchResult",$search);
		return $this->getDefaultViewName();
	}

	protected function addFilters(IcingaApiSearchIdo $search,AgaviRequestDataHolder $rd) {
		// URL filter definitions
		$field = $rd->getParameter("filter",null);
		if(!empty($field))
			$this->buildFiltersFromArray($search,$field);
			
		// POST filter definitions
		$advFilter = $rd->getParameter("filters",array());

		foreach($advFilter as $fl) {
			$fl["value"] = str_replace("*","%",$fl["value"]);
			$search->setSearchFilter($fl["column"],$fl["value"],$fl["relation"]);
		}
	}

	public function buildFiltersFromArray(IcingaApiSearchIdo $search, array $filterdef) {
		$searchField = $filterdef;
		if(isset($filterdef["type"]))
			$searchField = $filterdef["field"];
		foreach($searchField as $element) {
			if($element["type"] == "atom")
				$search->setSearchFilter($element["field"][0],$element["value"][0],$element["method"][0]);
		}

	}

	public function setGrouping(IcingaApiSearchIdo $search,AgaviRequestDataHolder $rd) {
		$groups = $rd->getParameter("groups",array());
		if(!is_array($groups))
			$groups = array($groups);
		if(!empty($groups))
			$search->setSearchGroup($groups);
	}

	public function setOrder(IcingaApiSearchIdo $search,AgaviRequestDataHolder $rd) {
		$order_col = $rd->getParameter("order_col",null);
		$order_dir = $rd->getParameter("order_dir",'asc');
		if($order_col)
			$search->setSearchOrder($order_col,$order_dir);

		$order = $rd->getParameter("order",array());
		if(!is_array($order))
			$order = array($order);
		if(!empty($order))
			$search->setSearchOrder($order["column"],$order["dir"]);
	}

	public function setColumns(IcingaApiSearchIdo $search,AgaviRequestDataHolder $rd) {
		$columns = $rd->getParameter("columns",null);
		if(!is_null($columns))
			$search->setResultColumns($columns);
		else{
			$search->setResultColumns(self::$defaultColumns[$rd->getParameter("target")]);
		}
	}

	public function setLimit(IcingaApiSearchIdo $search,AgaviRequestDataHolder $rd) {
		$start = $rd->getParameter("limit_start",0);
		$limit = $rd->getParameter("limit",false);

		if($limit)
			$search->setSearchLimit($start,$limit);
	}

	public function executeWrite(AgaviRequestDataHolder $rd) {
		return $this->executeRead($rd);
	}
}

?>