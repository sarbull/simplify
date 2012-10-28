<?php
/**
 * Homepage controller.
 */
class ConnectController extends Controller {
	
	/**
	 * Default action: display the home page.
	 * @return void
	 */
	public function index() {
		$this->tpl->display('connect-your-accounts.phtml');
	}
}
