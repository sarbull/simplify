<?php
/**
 * Facebook service implementation.
 */
class FacebookService extends BaseService {
	
	/**
	 * Authentication token: used by fetchLatest() to authenticate to facebook.
	 * @var string
	 */
	public $token = null;

	public function __construct() {
		
	}
	
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
		
		$url_fetch = "https://graph.facebook.com/me/home?access_token=" . $this->token;
		$news_feed = json_decode(file_get_contents($url_fetch));
		
		foreach ($news_feed->data as $value) {
			$item = new FeedObject();
			$item->id = $value->id;
			$item->service = "facebook";
			$content = array();
			if(isset($value->message))
				$item->content = $value->message;
			elseif (isset($value->story)) {
				$item->content = $value->story;
			}
			
			$item->timestamp = mysqldate(strtotime($value->updated_time));
			$item->author = $value->from->name;
			$item->author_id = $value->from->id;
			//http://graph.facebook.com/silviu.simon/picture
			$item->author_data['avatar'] = "http://graph.facebook.com/".$item->author_id. "/picture";
			$item->link = $value->actions[0]->link;
			//$url_test = "https://graph.facebook.com/me/home?access_token=" . $_SESSION['access_token'];
			$items[] = $item;
     	}
     	
		return $items;
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
	
	private $app_id = "422551301126797";
	private $app_secret = "74c2dcc8e4ac0a39151c505d11352e70";
	private $my_url = null;
	
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
		$this->my_url = WEBROOT . "service/facebook";
	}
	
	/**
	 * Returns authenticator's action HTML string.
	 * Can be a form or a popup-windowed link.
	 * Can contain javascript code.
	 * 
	 * @return string Action's URL.
	 */
	public function getAuthAction() {
		$_SESSION['facebook_state'] = md5(uniqid(rand(), TRUE)); // CSRF protection
		$dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" 
			. $this->app_id . "&redirect_uri=" . rawurlencode($this->my_url) . "&state="
			. $_SESSION['facebook_state'] . "&scope=user_birthday,read_stream";

		return "<a href=\"".ehtml($dialog_url)."\" onclick=\"window.open(this.href, '_blank', 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,scrollbars=no');return false;\">+ Conectare cont</a>";
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
		if($_REQUEST['state'] !=  $_SESSION['facebook_state'])
			return null;
		return $_REQUEST["code"];
	}
	
	/**
	 * Authenticates the user with the specified data.
	 * Client data is the data that was returned (and subsequently stored into the db)
	 * by the getClientData() method.
	 * 
	 * @return boolean Whether the authenticated succeeded.
	 */
	public function authenticate($clientData) {
		// TODO: fetch the associated token and set the $this->service->token variable 
		// with its value
		$token_url = "https://graph.facebook.com/oauth/access_token?"
			. "client_id=" . rawurlencode($this->app_id). "&redirect_uri=" . rawurlencode($this->my_url)
			. "&client_secret=" . rawurlencode($this->app_secret) . "&code=" . $clientData;

		$response = file_get_contents($token_url);
		$params = null;

		if(!$response)
			return false;

		parse_str($response, $params);

		$this->service->token = $params['access_token'];

		return true;
	}
	
}
