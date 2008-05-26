<?php

class Row_Solution extends Zend_Db_Table_Row_Abstract 
{
	/**
	 * Decline problem
	 * 
	 * * NEED outer Begin -> Commit
	 *
	 * @param string $addComment
	 * @param string $commentTitle
	 * @param string $commentBody
	 */
	public function decline($addComment = false, $commentTitle = null, $commentBody = null, $note = null)
	{
		// change state
		$this->State = Solution::STATE_DELETED;
		$this->save();
			
		// save comment
		if ($addComment) {
			$cTable = new Comment();
			$comment = $cTable->createRow(array(
				'ProblemId' 	=> $this->ProblemId,
				'SolutionId'	=> $this->Id,
				'UserId'		=> Zend_Registry::get('User')->Id,
				'Title'			=> $commentTitle,
				'System'		=> 1,
				'Event'			=> Solution::EVENT_DECLINE,	
				'Body'			=> $commentBody,
				'Created'		=> new Zend_Db_Expr('NOW()')
			));
			$comment->save();
		}

		// save history
		$hTable = new SolutionHistory();
		$history = $hTable->createRow(array(
			'SolutionId'		=> $this->Id,
			'Event'				=> Solution::EVENT_DECLINE, 
			'Note'				=> $note,
			'Created'			=> new Zend_Db_Expr('NOW()'),
			'CreatedBy'			=> Zend_Registry::get('User')->Id
		));
		$history->save();
	}
}

?>