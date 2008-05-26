<?php
class CommentController extends Colla_Controller_Action 
{
	public function addAction()
	{
		$this->checkAllowed('PROBLEM', 'ADD_COMMENT');
		$problemId = $this->getRequest()->getParam('problemId');
		$solutionId = $this->getRequest()->getParam('solutionId');
		$pTable = new Problem();
		$rows = $pTable->find($problemId);
		$problem = $rows->current();
		
		if ($problem->State == Problem::STATE_DELETED) {
			$this->_helper->FlashMessenger->addMessage('Nemožno pridávať komentáre k problému v stave "vymazaný"');
			$this->_redirect('/problem/view/Id/'.$problemId);	
		}
		
		
		if ($this->getRequest()->isPost()) {
			
			// input data
			$filter = new Zend_Filter_Input(array(), array(
				'Title' => array(
					array('StringLength', 0, 255),
					'messages' => array('Prosím vyplňte nadpis')
				),
				'Body'	=> array(
					'NotEmpty',
					'messages' => array(
						'isEmpty' => 'text2'
					)
				)
			), $_POST);
			
			// check validity
			if ($filter->isValid()) {
				// save data
				$cTable = new Comment();
				$comment = $cTable->createRow(array(
					'ProblemId' => 	$problemId,
					'SolutionId' => $solutionId,
					'UserId' => 	Zend_Registry::get('User')->Id,
					'System' => 	0,
					'Title'	=> 		$filter->getUnescaped('Title'),
					'Body' => 		$filter->getUnescaped('Body'),
					'Created' => 	new Zend_Db_Expr('NOW()')
				));
				$comment->save();
				if ($solutionId) {
					$this->_redirect('/solution/view/id/'.$solutionId);
				} else {
					$this->_redirect('/problem/view/Id/'.$problemId);
				}
			}
			$this->view->filter = $filter;
		}
		if ($solutionId) {
			$this->_forward('view', 'solution', null, array('id'=>$solutionId));	
		} else {
			$this->_forward('view', 'problem', null, array('Id'=>$problemId));
		}
		
	}
}
?>