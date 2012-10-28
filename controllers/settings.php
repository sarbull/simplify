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
	
	/**
	 * Change details action.
	 * @return void
	 */
	public function details() {
		global $db;
		
		if (isset($_POST['email'])) {
			if (empty($_POST['name'])) {
				$this->set('details_error', "Invalid email!");
				
			} elseif (empty($_POST['email'])) {
				$this->set('details_error', "Invalid name!");
				
			} else {
				$db->qupdate('users', array(
						'email' => $_POST['email'], 
						'name' => $_POST['name'], 
					), '`id`=\''.$db->escape($this->user['id']).'\'');
				$this->set('details_success', "Data successfully saved!");
			}
		}
		$this->index();
	}
	
	/**
	 * Change password action.
	 * @return void
	 */
	public function password() {
		global $db;
		
		if (isset($_POST['new_password'])) {
			
			if (empty($_POST['new_password'])) {
				$this->set('password_error', "New password is empty!");
				
			} elseif (md5($_POST['old_password']) != $this->user['password']) {
				$this->set('password_error', "Old password is incorrect!");
				
			} elseif ($_POST['new_password'] != $_POST['repeat_password']) {
				$this->set('password_error', "The passwords do not match!");
				
			} else {
				$db->qupdate('users', array(
						'password' => md5($_POST['new_password']), 
					), '`id`=\''.$db->escape($this->user['id']).'\'');
				$this->set('password_success', "Data successfully saved!");
			}
			
		}
		$this->index();
	}
	
}
