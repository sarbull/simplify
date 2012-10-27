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
	public $id;
	
	/**
	 * The name of the web service the object was fetched from.
	 * @var string
	 */
	public $service;
	
	/**
	 * An unique timestamp of the item.
	 * @var double
	 */
	public $timestamp;
	
	/**
	 * The item's title.
	 * @var string
	 */
	public $title;
	
	/**
	 * The item's description / content.
	 * @var string|array
	 */
	public $content;
	
	/**
	 * Other feed data, as an array (will be serialized before storage).
	 * @var array
	 */
	public $data;
	
}