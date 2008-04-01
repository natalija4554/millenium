<?php
class CategoryController extends Colla_Controller_Action
{
	
	/**
	 * List all categories
	 *
	 */
	public function listAction()
	{
		$categoryTable = new Colla_Db_Table_Category();
   		$this->view->categories = $categoryTable->getParentRows();
	}
	
	/**
	 * Create new main category
	 */
	public function newAction()
	{
		$form = new Colla_Form_Category();
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				$categoryTable = new Colla_Db_Table_Category();
				$categoryTable->newCategory($form->getValues());
				$this->_helper->FlashMessenger->addMessage('New category has been created.');
    			$this->_redirect('/category/list');	
			}
		}	
		$this->view->form = $form;
	}
	
	/**
	 * Create new Subcategory
	 *
	 */
	public function newsubcategoryAction()
	{
		// param Id is required
		if (!($parentId = $this->getRequest()->getParam('Id'))) {
			$this->_helper->FlashMessenger->addMessage('Please specify Id.');
    		$this->_redirect('/category/list');
		}
		$form = new Colla_Form_SubCategory($parentId);
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				$categoryTable = new Colla_Db_Table_Category();
				$categoryTable->newSubCategory($form->getValues());
				$this->_helper->FlashMessenger->addMessage('New subcategory has been created.');
    			$this->_redirect('/category/list');	
			}
		}
		$this->view->form = $form;
	}
	
	public function removeAction()
	{
		// param Id is required
		if (!($categoryId = $this->getRequest()->getParam('Id'))) {
			$this->_helper->FlashMessenger->addMessage('Please specify Id.');
    		$this->_redirect('/category/list');
		}
		// find info about category:
		// - count of subcategories
		// @todo - count of problems in this category and subcategories
		$categoryTable = new Colla_Db_Table_Category();
		$category = $categoryTable->findCategory($categoryId);
		$childs = $category->getChilds();
		
		// A) if 0 subcategories and 0 problems -> Dorect deletion
		if (count($childs) == 0) {
			$category->delete();
			$this->_helper->FlashMessenger->addMessage('Category has been deleted!');
			$this->_redirect('/category/list');	
		}
		
		// B) category contain subcategories
		$this->view->categories = $childs;
		$this->view->categoryId = $categoryId;
	}
	public function forceremoveAction()
	{
		// param Id is required
		if (!($categoryId = $this->getRequest()->getParam('Id'))) {
			$this->_helper->FlashMessenger->addMessage('Please specify Id.');
    		$this->_redirect('/category/list');
		}
		
		// odstran rekurzivne 
		$categoryTable = new Colla_Db_Table_Category();
		$category = $categoryTable->findCategory($categoryId);
		$category->deleteRecursively();
		$this->_helper->FlashMessenger->addMessage('Category with subcategories has been deleted!');
		$this->_redirect('/category/list');
	}
}
