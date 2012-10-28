<?php
/**
 * Login controller.
 */
class LoginController extends Controller {
	
	/**
	 * Don't require authentication.
	 * @var boolean
	 */
	protected $authorize = false;
	
	/**
	 * Default action: display the login and register forms.
	 * @return void
	 */
	public function index() {
		$this->tpl->display('login.phtml');
	}
	
	/**
	 * Login action - handles the login form.
	 * @return void
	 */
	public function login() {
		global $db;
		if (isset($_POST['email'])) {
			$user = $db->fetch("SELECT * FROM `users` WHERE `email` = '".$db->escape($_POST['email'])."'");
			if (!$user || (md5($_POST['password'])!=$user['password']) ) {
				// login failed
				$this->set('error', "Login failed!");
			} else {
				// login succeeded
				$_SESSION['user'] = array( 'id' => $user['id'] );
				redirect(WEBROOT);
			}
		}
		$this->index();
	}
	
	/**
	 * Login action - handles the login form.
	 * @return void
	 */
	public function register() {
		if (isset($_POST['email'])) {
			
		}
		$this->index();
	}
	
}
