<?php
/**
 * Solution for problems
 * 
 * 
 */
class UserController extends Colla_Controller_Action
{
	public function findAction()
	{
		$this->checkAllowed('USERS', 'SEARCH');
		
		// zoznam roli
		$role = new AclRole();
		$select = $role->select()
			->order('position');
		$this->view->roles = $role->fetchAll($select);
		
		
		if ($this->getRequest()->isPost()) {
			$searchField = $this->getRequest()->getParam('username');
			$searchRole = $this->getRequest()->getParam('role');
			$user = new User(); 
			$select = $user->select()
				->where("
					Username LIKE '%".addslashes($searchField)."%' OR 
					FullName LIKE '%".addslashes($searchField)."%'
				");
			if ($searchRole != '') {
				$select->where('RoleId = ?', $searchRole);
			}	
			$users = $user->fetchAll($select);
			$this->view->searchField = $searchField;
			$this->view->foundUsers = $users;
			$this->view->isRequest = true;
			
		} else {
			$this->view->isRequest = false;
		}
	}
	
	public function rolesAction()
	{
		$this->checkAllowed('PERMISSIONS', 'EDIT');
		
		// read roles
		$role = new AclRole();
		$select = $role->select()
			->where('configurable = 1')
			->order('position');
		$this->view->roles = $role->fetchAll($select);
				
		// read resources
		$resource = new AclResource();
		$select = $resource->select()
			->where('configurable = 1')
			->order(array('id', 'position'));
		$this->view->resources = $resource->fetchAll($select);

		
		// save 
		if ($this->getRequest()->isPost()) {
			
			// post param
			$values = $this->getRequest()->getParam('allow', array());
			
			// loop all resources and compare
			$changed = 0;
			foreach ($this->view->resources as $resource) {
				foreach ($this->view->roles as $role) {
					if ($resource->isAllowedBy($role->id)) {
						if (!isset($values[$resource->id][$resource->privilege][$role->id])) {
							$changed++;
							$role->removeResource($resource);
						}						
					} else {
						if (isset($values[$resource->id][$resource->privilege][$role->id])) {
							$changed++;
							$role->addResource($resource);
						}						
					}
				}
			}
			// something changed
			if ($changed) {
				$this->_helper->FlashMessenger->addMessage('Prístupové práva boli zmenené. Táto zmena sa prejaví okamžite.');
			} else {
				$this->_helper->FlashMessenger->addMessage('Práva ostali také isté.');
			}
			$this->_redirect('/user/roles');
			exit();
		}
	}
	
	
	public function viewAction()
	{
		$userTable = new User();
		$rows = $userTable->find($this->getRequest()->getParam('Id'));
		if (count($rows) != 1) {
			$this->_helper->FlashMessenger->addMessage('No such user!');
			$this->_redirect('/user/find');
		}
		$this->view->usr = $rows->current();
	}
	
	public function registerAction()
	{
		$form = new Form_Register();
		
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {

				// insert new User
				$userTable = new User();
				$userTable->getAdapter()->beginTransaction();
				$user = $userTable->createRow($form->getValues());
				$user->Password = md5($user->Password);
				$user->Active = 0;
				$user->Verified = 0;
				$user->VerificationKey = substr(md5(rand(0,999)), 10, 8);
				$user->RoleId = 'user';
				$userId = $user->save(); 
				
				
				
				// send verification e-mail
				$body = str_replace('%Username%', $user->Username, $this->appConfig->activation->email->body);
				$body = str_replace('%VerificationKey%', $user->VerificationKey, $body);
				$body = str_replace('%Id%', $userId, $body);
				
				$mail = new Zend_Mail('utf-8');
				$mail->setFrom($this->appConfig->email->from, $this->appConfig->email->fromName);
				$mail->addTo($user->EMail);
				$mail->setBodyText($body);
				$mail->setSubject($this->appConfig->activation->email->subject);
				$mail->send();					
							
				$userTable->getAdapter()->commit();
				$this->render('register-info');
			}
		}
		$this->view->form = $form;
		$this->view->hideLoginBox = true;
	}
	
	public function verifyAgainAction()
	{
		$userId 	= $this->getRequest()->getParam('id');
		
		// read user id 
		$userTable = new User();
		$rows = $userTable->find((int)$userId);
		if (count($rows) != 1) {
			throw new Exception('No such user');
		}
		$user = $rows->current();
		
		// send the e-mail
		$body = str_replace('%Username%', $user->Username, $this->appConfig->activation->email->body);
		$body = str_replace('%VerificationKey%', $user->VerificationKey, $body);
		$body = str_replace('%Id%', $userId, $body);
		
		$mail = new Zend_Mail('utf-8');
		$mail->setFrom($this->appConfig->email->from, $this->appConfig->email->fromName);
		$mail->addTo($user->EMail);
		$mail->setBodyText($body);
		$mail->setSubject($this->appConfig->activation->email->subject);
		$mail->send();
	}
	
	public function verifyAction()
	{
		$userId 	= $this->getRequest()->getParam('id');
		$key 		= $this->getRequest()->getParam('key');
		
		// read user id 
		$userTable = new User();
		$rows = $userTable->find((int)$userId);
		if (count($rows) != 1) {
			throw new Exception('No such user');
		}
		$user = $rows->current();
		$this->view->usr = $user;
		
		// allready verified
		if ($user->Verified) {
			$this->render('verify-allready');
			return;
		}
		
		// bad token
		if ($user->VerificationKey != $key) {
			$this->render('verify-fail');
			return;
		}
		
		// verify the string 
		$user->Verified = 1;
		$user->Active = 1;
		$user->VerificationKey = null;
		$user->save();
		$this->view->hideLoginBox = true;
	}
	
	public function activateAction()
	{
		$this->checkAllowed('PERMISSIONS', 'EDIT');
		$userId 	= $this->getRequest()->getParam('id');
		
		// read user 
		$userTable = new User();
		$rows = $userTable->find((int)$userId);
		if (count($rows) != 1) {
			throw new Exception('No such user');
		}
		$user = $rows->current();
		
		// activate
		$user->Active = 1;
		$user->save();
		$this->_redirect('/user/view/Id/'.$user->Id);
	}
	
	public function deactivateAction()
	{
		$this->checkAllowed('PERMISSIONS', 'EDIT');
		$userId 	= $this->getRequest()->getParam('id');
		
		// read user 
		$userTable = new User();
		$rows = $userTable->find((int)$userId);
		if (count($rows) != 1) {
			throw new Exception('No such user');
		}
		$user = $rows->current();
		
		// activate
		$user->Active = 0;
		$user->save();
		$this->_redirect('/user/view/Id/'.$user->Id);
	}
	
	public function changeRoleAction()
	{
		$this->checkAllowed('PERMISSIONS', 'EDIT');
		$userId 	= $this->getRequest()->getParam('id');
		
		// read user 
		$userTable = new User();
		$rows = $userTable->find((int)$userId);
		if (count($rows) != 1) {
			throw new Exception('No such user');
		}
		$user = $rows->current();
		
		// nemoze menit svoj ucet
		if ($this->view->user->Id == $user->Id) {
			throw new Exception('Nemozete menit svoj ucet');
		}
		
		if ($this->getRequest()->isPost()) {
			$user->RoleId = $this->getRequest()->getParam('role_id');
			$user->save();
			$this->_helper->FlashMessenger->addMessage('Používateľská rola bola zmenená.');
			$this->_redirect('/user/view/Id/'.$user->Id);
			exit();
		}
		
		// read all existing roles
		$role = new AclRole();
		$select = $role->select()->order('position');
		$this->view->roles = $role->fetchAll($select);
		$this->view->usr = $user;
	}
}
?>