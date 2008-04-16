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
			->order('position');
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
}
?>