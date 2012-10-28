<?php
/**
 * Homepage controller.
 */
class SettingsController extends Controller {
	
	/**
	 * Default action: display the home page.
	 * @return void
	 */
	public function index() {
		$this->tpl->display('settings.phtml');
	}
}
