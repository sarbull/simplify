<?php

/**
 * Class that encapsulates all common feed item's properties.
 * They can be returned by the services or stored and retrieved from the database.
 */
class FeedObject {
	
	/**
	 * Unique object identifier.
	 * @var string
	 */
	public $id = null;
	
	/**
	 * The database ID of the associated feed.
	 * @var string
	 */
	public $feed_id = null;
	
	/**
	 * The name of the web service the object was fetched from.
	 * @var string
	 */
	public $service = null;
	
	/**
	 * An unique timestamp of the item.
	 * Mysql DATETIME format.
	 * @var string
	 */
	public $timestamp = null;
	
	/**
	 * Stores the author's name.
	 * 
	 * @var array
	 */
	public $author = '';
	
	/**
	 * Stores the author's service-unique ID.
	 * 
	 * @var string
	 */
	public $author_id = '';
	
	/**
	 * Stores additional data about the author such as profile URL and avatar.
	 * 
	 * @var array
	 */
	public $author_data = array();
	
	/**
	 * The item's title.
	 * @var string
	 */
	public $title = '';
	
	/**
	 * The item's description / content.
	 * @var string|array
	 */
	public $content = '';
	
	/**
	 * Item's link.
	 * @var string
	 */
	public $link = '';
	
	/**
	 * Other feed data, as an array (will be serialized before storage).
	 * @var array
	 */
	public $data = array();
	
	/**
	 * Fills the current instance's fields from the specified database entry.
	 * 
	 * @param  array $item The database item (as an associative array).
	 * @return void
	 */
	public function convertFromDb($item) {
		$this->id = $item['item_id'];
		$this->feed_id = $item['feed_id'];
		$this->service = $item['service'];
		$this->timestamp = $item['timestamp'];
		$this->author = $item['author'];
		$this->author_id = $item['author_id'];
		$this->author_data = @unserialize($item['author_data']);
		$this->link = $item['link'];
		
		$this->title = $item['title'];
		$this->content = $item['content'];
		$this->data = @unserialize($item['data']);
	}
	
	/**
	 * Saves the current feed item into the database.
	 * If the item is already there, the record is updated.
	 * 
	 * @return boolean Success status.
	 */
	public function saveToDb() {
		global $db;
		
		$item = array(
			'item_id' => $this->id,
			'feed_id' => $this->feed_id,
			'service' => $this->service,
			'timestamp' => $this->timestamp,
			'author' => $this->author,
			'author_id' => $this->author_id,
			'author_data' => serialize($this->author_data),
			'link' => $this->link,
			
			'title' => $this->title,
			'content' => $this->content,
			'data' => serialize($this->data),
		);
		
		$existing = $db->fetch("SELECT `id` FROM `feed_items` WHERE `service`='".$db->escape($item['service'])."' 
				AND `item_id`='".$db->escape($item['item_id'])."'");
		if ($existing) {
			return $db->qupdate('feed_items', $item, '`id`=\''.$db->escape($existing['id']).'\'');
		} else {
			return $db->qinsert('feed_items', $item);
		}
		
	}
	
}
