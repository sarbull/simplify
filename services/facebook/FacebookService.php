<?php
/**
 * Facebook service implementation.
 */
class FacebookService extends BaseService {
	
	/**
	 * Authentication token: used by fetchLatest() to authenticate to facebook.
	 * @var string
	 */
	protected $token = null;
	
	/**
	 * Returns the service's name (facebook).
	 * 
	 * @return string
	 */
	public function getServiceName() {
		return 'facebook';
	}
	
	/**
	 * Fetches all new feed items from the service API since the given timestamp.
	 * If timestamp is null, there is no time span limit for the items to be retrieved.
	 * TODO: implement a realtime update feature
	 * 
	 * @param  double $timestamp The timestamp
	 * @return array An array of FeedObject items.
	 */
	public function fetchLatest($timestamp = null) {
		if (!$this->token)
			return null; // not authenticated
		
		$items = array();
		// TODO: fetch items from server and parse them into an array of FeedObjects
	}
	
	/**
	 * Returns service's authenticator object.
	 * 
	 * @return string Service's authenticator.
	 */
	public function getAuthenticator() {
		return new FacebookAuthenticator($this);
	}
	
	
}

/**
 * Facebook service OAuth wrapper.
 */
class FacebookAuthenticator implements ServiceAuthenticator {
	/**
	 * Parent service reference.
	 * @var FacebookService
	 */
	protected $service = null;
	
	/**
	 * Authenticator constructor.
	 * @param  $service Associated service instance.
	 */
	public function __construct($service) {
		$this->service = $service;
	}
	
	/**
	 * Returns authenticator's action HTML string.
	 * Can be a form or a popup-windowed link.
	 * Can contain javascript code.
	 * 
	 * @return string Action's URL.
	 */
	public function getAuthAction() {
		// return redirection link that opens a popup window
	}
	
	/**
	 * Returns authenticated client's data.
	 * If the client's authentication failed, will return null.
	 * The data will be serialized and stored into the database for further use. 
	 * (example data: form-supplied credentials / authentication tokens / authenticated client ID )
	 * 
	 * @return mixed Authenticated client / failure.
	 */
	public function getClientData() {
		// return the client ID (used to generate the token)
	}
	
	/**
	 * Authenticates the user with the specified data.
	 * Client data is the data that was returned (and subsequently stored into the db)
	 * by the getClientData() method.
	 * 
	 * @return boolean Whether the authenticated succeeded.
	 */
	public function authenticate($clientData) {
		$clientID = $clientData['clientID'];
		// TODO: fetch the associated token and set the $this->service->token variable 
		// with its value
	}
	
}
