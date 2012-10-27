<?php
/**
 * Homepage controller.
 */
class HomeController extends Controller {
	
	/**
	 * Default action: display the home page.
	 * @return void
	 */
	public function index() {
		
		
		$this->set('var1', 'value');
		$this->tpl->display('home.phtml');
	}
	
}
