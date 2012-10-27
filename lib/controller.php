<?php
/**
 * Controller class.
 */
class Controller {
	/**
	 * Template engine object.
	 * @var PSTTemplate
	 */
	protected $tpl = null;
	
	/**
	 * Default class constructor.
	 * Initializes controller objects (e.g. template engine).
	 */
	public function __construct() {
		global $config;
		
		$this->tpl = new PSTTemplate();
		$this->tpl->config['dir_templates'] = $config['template']['dir'];
		$this->tpl->config['dir_cache'] = $config['template']['cache'];
	}
	
	/**
	 * Sets a template variable (wrapper method).
	 * 
	 * @param mixed|array $var   The variable to set / array of key-value associations
	 * @param mixed $value Value to set.
	 */
	public function set($var, $value) {
		if (is_array($var)) {
			foreach ($var as $k=>$v)
				$this->tpl->set($k, $v);
		} else {
			$this->tpl->set($var, $value);
		}
	}
	
	/**
	 * Dispatches a URL to a controller and action.
	 * @param  string $url The target URL (without the query string!).
	 * @return void
	 */
	public static function dispatch($url) {
		global $routes;
		
		$components = explode('/', $url);
		$controller_segment = $routes[''];
		$action_segment = 'index';
		
		if (!empty($components[0])) {
			$controller_segment = $components[0];
			if (!empty($components[1])) {
				$action_segment = $components[1];
			}
		}
		
		if (!isset($routes[$controller_segment])) {
			$controller_segment = 'error';
			$action_segment = 'not_found';
		}
		
		$controller = $routes[$controller_segment];
		// load the specified controller
		$file = SITE_ROOT.'/controllers/' . $controller . '.php';
		include $file;
		
		$class = ucfirst($controller).'Controller';
		$obj = new $class();
		
		if (method_exists($obj, $action_segment)) {
			$obj->$action_segment($components);
			return;
			
		} else {
			self::dispatch('error/not_found');
		}
		
	}
	
}
