<?php
class CategoryController extends Colla_Controller_Action
{
	
	/**
	 * List all categories
	 *
	 */
	public function listAction()
	{
		$categoryTable = new Category();
   		$this->view->categories = $categoryTable->getParentRows();
	}
	
	/**
	 * Create new main category
	 */
	public function newAction()
	{
		$form = new Form_Category();
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				$categoryTable = new Category();
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
		$form = new Form_SubCategory($parentId);
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($_POST)) {
				$categoryTable = new Category();
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
		$categoryTable = new Category();
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
		$categoryTable = new Category();
		$category = $categoryTable->findCategory($categoryId);
		$category->deleteRecursively();
		$this->_helper->FlashMessenger->addMessage('Category with subcategories has been deleted!');
		$this->_redirect('/category/list');
	}
	
	public function ajaxListAction()
	{
		// check post param
		$parentId = $this->getRequest()->getParam('node');
		if (!$parentId) {
			throw new Exception('No node specified!');
		}
		
		if ($parentId == 'null') {
			$parentId = null;
		}
		
		// find all childrens
		$output = array();
		$category = new Category();
		$childs = $category->getChilds($parentId);
		foreach ($childs as $c) {
			$item = array();
			$item['text'] 	= $c->Name;
			$item['id']		= $c->Id;
			$item['cls'] = 'subor';
			$subChilds = $c->getChilds();
			if (count($subChilds) > 0) {
				$item['leaf'] = false;
			} else {
				$item['leaf'] = true;
			}
			$output[] = $item;
		}
		$this->view->data = $output;
	}
	public function ajaxComboAction()
	{
		$category = new Category();

		$data = $category->getThreadedList();
		$data = array_reverse($data);
		$data[] = array('', 'Všetky kategórie');
		$data = array_reverse($data);
		$this->view->data = $data;
	}
}
