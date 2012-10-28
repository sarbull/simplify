<?php
require_once(SITE_ROOT.'/lib/simplify/ServiceAuthenticator.php');

/**
 * Abstract class that will be implemented by all service modules.
 * Defines the common service access interface.
 */
abstract class BaseService {
	
	/**
	 * Returns the service's name.
	 * For example: => 'facebook'
	 * 
	 * @return string
	 */
	abstract public function getServiceName();
	
	/**
	 * Fetches all new feed items from the service API since the given timestamp.
	 * If timestamp is null, there is no time span limit for the items to be retrieved.
	 * TODO: implement a realtime update feature
	 * 
	 * @param  string $timestamp The timestamp to compare the results with (Mysql format).
	 * @return array An array of FeedObject items.
	 */
	abstract public function fetchLatest($timestamp = null);
	
	/**
	 * Returns service's authenticator object.
	 * 
	 * @return string Service's authenticator.
	 */
	abstract public function getAuthenticator();
	
	/**
	 * Retrieves the stored feed items from the database.
	 * 
	 * @param  array $where SQL WHERE conditions.
	 * @return array An array of retrieved FeedObjects.
	 */
	public static function retrieveFromDb($where) {
		global $db;
		
		$dbitems = $db->fetch_all("SELECT * FROM feed_items WHERE " . 
			implode(' AND ', $where)." ORDER BY `timestamp` DESC");
		
		$items = array();
		foreach ($dbitems as $dbitem) {
			$item = new FeedObject();
			$item->convertFromDb($dbitem);
			$items[] = $item;
		}
		return $items;
	}
	
	/**
	 * Factory method: returns a requested service's instance.
	 * 
	 * @param  [type] $service The name of the service to load.
	 * @return BaseService The requested service's instance.
	 */
	public static function loadService($service) {
		$class = ucfirst($service).'Service';
		require(SITE_ROOT.'/services/'.$service.'/'.$class.'.php');
		$serviceObj = new $class();
		return $serviceObj;
	}
	
}
