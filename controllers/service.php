<?php
/**
 * Service dispatcher.
 */
class ServiceController extends Controller {
	
	/**
	 * Require authentication.
	 * @var boolean
	 */
	protected $authorize = true;
	
	/**
	 * Dispatch the response to the specified service class.
	 * @return void
	 */
	public function index($service = null) {
		global $config;
		if (!isset($service)) 
			redirect('error');
		
		if (!in_array($service, $config['services'])) {
			redirect('error');
		}
		
		$serviceObj = BaseService::loadService($service);
		$serviceAuth = $serviceObj->getAuthenticator();
		$clientData = $serviceAuth->getClientData();
		if (!$clientData) {
			$this->set('error', true);
			$this->tpl->display("service-connected.phtml");
			die();
		}
		
		global $db;
		$record = array(
				'service' => $serviceObj->getServiceName(), 
				'user_id' => $this->user['id'], 
				'auth_data' => serialize($clientData), 
				'last_update' => mysqldate(), 
			);
		$db->qinsert('user_feeds', $record);
		
		$this->tpl->display("service-connected.phtml");
	}
	
	/**
	 * Magic method call for the services' authentication responses.
	 * 
	 * @param  string $name Method's name. 
	 * @param  array $arguments Method's arguments. 
	 * @return void
	 */
	public function __call($name, $arguments) {
		return $this->index($name);
	}
	
}
