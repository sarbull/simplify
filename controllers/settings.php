<?php
/**
 * Homepage controller.
 */
class SettingsController extends Controller {
	
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
		$this->tpl->display('settings.phtml');
	}
}
