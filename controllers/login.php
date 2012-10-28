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
		if (isset($_GET['logout'])) {
			unset($_SESSION['user']);
			redirect('');
		}
		if (isset($this->user)) 
			$this->redirect('');
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
				redirect('');
			}
		}
		$this->index();
	}
	
	/**
	 * Login action - handles the login form.
	 * @return void
	 */
	public function register() {
		global $db;
		if (isset($_POST['email'])) {
			if (empty($_POST['name'])) {
				$this->set('error', 'No name specified!');
			} elseif (empty($_POST['email'])) {
				$this->set('error', 'No email specified!');
			} elseif (empty($_POST['password'])) {
				$this->set('error', 'No password specified!');
			} elseif ($_POST['password'] != $_POST['password2']) {
				$this->set('error', 'Passwords do not match!');
			} else {
				$old_user = $db->fetch("SELECT * FROM `users` WHERE `email` = '".$db->escape($_POST['email'])."'");
				if ($old_user) {
					$this->set('error', 'The specified email already exists!');
					
				} else {
					$db->qinsert('users', array(
							'name' => $_POST['name'],
							'email' => $_POST['email'],
							'password' => md5($_POST['password']),
						));
					$this->set('success', 'User successfully created!');
				}
			}
		}
		$this->index();
	}
	
}
