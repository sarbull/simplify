<?php
require_once dirname(__FILE__).'/google-api-php-client/src/apiClient.php';
require_once dirname(__FILE__).'/google-api-php-client/src/contrib/apiPlusService.php';

/**
 * Google+ service implementation.
 */
class GoogleplusService extends BaseService {
	/**
	 * Authentication token: used by fetchLatest() to authenticate to facebook.
	 * @var string
	 */
	public $token = null;
	
	/**
	 * Returns the service's name (google).
	 * 
	 * @return string
	 */
	public function getServiceName() {
		return 'googleplus';
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
		
		$optParams = array('maxResults' => 100);
		// should do this for all friends, but google has no API for retrieving them
		$activities = $this->plus->activities->listActivities('me', 'public', $optParams);
		foreach($activities['items'] as $activity) {
			$url = filter_var($activity['url'], FILTER_VALIDATE_URL);
			$title = filter_var($activity['title'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
			$content = filter_var($activity['object']['content'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
			
			$item = new FeedObject();
			$item->id = $activity['id'];
			$item->service = "googleplus";
			
			$item->title = $title;
			$item->content = $content;
			$item->url = $url;
			
			$item->timestamp = mysqldate(strtotime($activity['updated']));
			$item->author = $activity['actor']['displayName'];
			$item->author_id = $activity['actor']['id'];
			$item->author_data['avatar'] = $activity['actor']['image']['url'];
			
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
		return new GoogleplusAuthenticator($this);
	}
	
}

/**
 * Google+ service OAuth wrapper.
 */
class GoogleplusAuthenticator implements ServiceAuthenticator {
	
	private $client_id = "67114061491-hmoim1sho9unqoj5elb5sjvlc0ceg8sq.apps.googleusercontent.com";
	private $client_secret = "vAWgv9IXTVQZsetYvFWFLQDU";
	private $developer_key = "AIzaSyAppLPZ8LeT02slnxAoShQI1QBRFAJ7b-U";
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
		$this->my_url = WEBROOT . "service/googleplus";
		
		$client = new apiClient();
		$client->setApplicationName("Simplify");
		$client->setClientId($this->client_id);
		$client->setClientSecret($this->client_secret);
		$client->setRedirectUri($this->my_url);
		$client->setDeveloperKey($this->developer_key);
		$client->setScopes(array('https://www.googleapis.com/auth/plus.me'));
		$plus = new apiPlusService($client);
		$this->client = $client;
		$this->plus = $plus;
	}
	
	/**
	 * Returns authenticator's action HTML string.
	 * Can be a form or a popup-windowed link.
	 * Can contain javascript code.
	 * 
	 * @return string Action's URL.
	 */
	public function getAuthAction() {
		$authUrl = $this->client->createAuthUrl();
		
		return "<a href=\"".ehtml($authUrl)."\" onclick=\"window.open(this.href, '_blank', 'height=500,width=700,status=yes,toolbar=no,menubar=no,location=no,scrollbars=no');return false;\">+ Conectare cont</a>";
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
		$this->client->authenticate();
		return $this->client->getAccessToken();
	}
	
	/**
	 * Authenticates the user with the specified data.
	 * Client data is the data that was returned (and subsequently stored into the db)
	 * by the getClientData() method.
	 * 
	 * @return boolean Whether the authenticated succeeded.
	 */
	public function authenticate($clientData) {
		$this->client->setAccessToken($clientData);
		$this->service->token = $this->client->getAccessToken();
		$this->service->plus = $this->plus;
		return true;
	}
	
	
}
