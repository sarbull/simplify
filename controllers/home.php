<?php
/**
 * Homepage controller.
 */
class HomeController extends Controller {
	
	/**
	 * Require authentication.
	 * @var boolean
	 */
	protected $authorize = true;
	
	/**
	 * Default action: display the home page.
	 * @return void
	 */
	public function index() {
		global $db;
		
		// fetch all user feeds from database
		$db_feeds = $db->fetch_all("SELECT id FROM `user_feeds` WHERE `user_id`='".
				$db->escape($this->user['id'])."'");
		$feeds = array();
		foreach ($db_feeds as $f) $feeds[] = $f['id'];
		
		ServiceUpdater::updateFeeds($this->user['id']);
		
		// fetch all items
		$items = array();
		$where = array(
				'`feed_id` IN ('.implode(',', $feeds).')'
			);
		if (isset($_POST['filter']))
			$where[] = '(`content` LIKE \'%'.$db->escape($_POST['filter']).'%\')';
		
		if (!empty($feeds))
			$items = BaseService::retrieveFromDb($where);
		
		$this->set('items', $items);
		$this->tpl->display('home.phtml');
	}
	
}
