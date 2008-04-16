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
		if ($this->getRequest()->isPost()) {
			$searchField = $this->getRequest()->getParam('username');
			$user = new User(); 
			$users = $user->fetchAll($user->select()
				->where("Username LIKE '%".addslashes($searchField)."%'")
				->orWhere("FullName LIKE '%".addslashes($searchField)."%'")
			);
			$this->view->searchField = $searchField;
			$this->view->foundUsers = $users;
			$this->view->isRequest = true;
			
		} else {
			$this->view->isRequest = false;
		}
	}
	
	public function address()
	{
		
	}
}
?>