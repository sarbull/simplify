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
	 * @param  double $timestamp The timestamp
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
			implode(' AND ', $where));
		
		$items = array();
		foreach ($dbitems as $dbitem) {
			$item = new FeedObject();
			$item->convertFromDb($dbitem);
			$items[] = $item;
		}
		return $items;
	}
	
	/**
	 * Stores the given items into the database.
	 * If the item already exists, it is updated to the latest version.
	 * 
	 * @param  array $items Array of FeedObjects to store.
	 * @return boolean True if the save operation succeeded.
	 */
	public function storeToDb($items) {
		foreach ($items as $item) {
			$item->saveToDb();
		}
	}
	
}
