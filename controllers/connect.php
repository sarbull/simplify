<?php
/**
 * Homepage controller.
 */
class ConnectController extends Controller {
	
	/**
	 * Require authentication.
	 * @var boolean
	 */
	protected $authorize = true;
	
	/**
	 * Default action: display the home page.
	 * @return void
	 */
	public function index() {
		global $config, $db;
		
		$services = array();
		foreach ($config['services'] as $service) {
			$services[$service]['obj'] = BaseService::loadService($service);
			
		}
		$db_services = $db->fetch_all("SELECT * FROM `user_feeds` WHERE `user_id`='".
			$db->escape($this->user['id'])."'");
		foreach ($db_services as $dbservice) {
			if (!isset($services[$dbservice['service']]))
				continue;
			$services[$dbservice['service']]['record'] = $dbservice;
		}
		$this->set('services', $services);
		
		$this->tpl->display('connect-your-accounts.phtml');
	}
}
