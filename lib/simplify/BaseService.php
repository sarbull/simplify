<?php

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
	 * Fetches all new feed items since the given timestamp.
	 * If timestamp is null, there is no time span limit for the items to be retrieved.
	 * TODO: add realtime features
	 * 
	 * @param  double $timestamp The timestamp
	 * @return array An array of FeedObject items.
	 */
	abstract public function fetchFromAPI($timestamp = null);
	
	/**
	 * Retrieves the stored feed items from the database.
	 * 
	 * @param  array $where SQL WHERE conditions.
	 * @return array An array of retrieved FeedObjects.
	 */
	public function retrieveFromDb($where) {
		global $db;
		
		$dbitems = $db->fetch_all("SELECT * FROM feed_items WHERE 
			`service`='".$db->escape($this->getServiceName())."' AND ".
			implode(' AND ', $where));
		
		$items = array();
		foreach ($dbitems as $dbitem)	{
			$item = new FeedObject();
			$item->fromDb($dbitem);
			$items[] = $item;
		}
		return $items;
	}
	
	/**
	 * Stores the given items into the database.
	 * 
	 * @param  array $items Array of FeedObjects to store.
	 * @return boolean True if the save operation succeeded.
	 */
	public function storeToDb($items) {
		// TODO
	}
	
}
