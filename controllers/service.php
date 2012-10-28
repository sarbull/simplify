<?php
/**
 * Service dispatcher.
 */
class ServiceController extends Controller {
	
	/**
	 * List of allowed/existent services.
	 * @var array
	 */
	private $allowedServices = array(
			'facebook', 'googleplus'
		);
	
	/**
	 * Dispatch the response to the specified service class.
	 * @return void
	 */
	public function index($service = null) {
		if (!isset($service)) 
			redirect('error');
		
		if (!in_array($service, $this->allowedServices)) {
			redirect('errcontror');
		}
		
		$serviceObj = BaseService::loadService($service);
		$clientData = $serviceObj->getClientData();
		if (!$clientData) {
			$this->set('error', true);
			$this->tpl->display("service-configured.phtml");
		}
		
		global $db;
		$record = array(
				'service' => $serviceObj->getServiceName(), 
				'user_id' => $this->user['id'], 
				'auth_data' => serialize($clientData), 
				'last_update' => mysqldate(), 
			);
		$db->qinsert('user_feeds', $record);
		
		$this->tpl->display("service-configured.phtml");
	}
	
	/**
	 * Magic method call for the services' authentication responses.
	 * 
	 * @param  string $name Method's name. 
	 * @param  array $arguments Method's arguments. 
	 * @return void
	 */
	public function __call($name, $arguments) {
		return index($name);
	}
	
}
