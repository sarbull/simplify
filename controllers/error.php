<?php
/**
 * Error controller - handles exceptional conditions in a user-friendly way.
 */
class ErrorController extends Controller {
	
	/**
	 * Default error action.
	 * @return void
	 */
	public function index() {
		$this->not_found();
	}
	
	/**
	 * Page not found error action.
	 * @return void
	 */
	public function not_found() {
		$this->tpl->display('error.phtml');
	}
	
}
