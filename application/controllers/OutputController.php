<?php
function sortIt ($a, $b) {
	if ($a['Importance'] == $b['Importance']) {
        return 0;
    }
    return ($a['Importance'] < $b['Importance']) ? 1 : -1;
}
class OutputController extends Colla_Controller_Action 
{
	/**
	 * Vypocita optimalnu postupnost
	 * 
	 * - v pripade viacerych problemov prerobit ordering dynamicky a nevytvarat nove pole odznova.
	 * @todo
	 */
	public function optimalOrderAction()
	{
		$QUEUE = array();
		$PROBLEMS = array();
		$WARNING_ZV = false;
		
		// 1. Find all problems with their solutions and dependend problems
		$pTable = new Problem();
		$sTable = new Solution();
		$dTable = new SolutionDependency();
		$iTable = new ProblemImportance();
		
		// get problems -> solutions -> dependencies
		$_problems = $pTable->fetchAll($pTable->select()
			->from(array('p' => 'problems'), array('Id', 'State', 'Name', 'Created'))
			->where('State = ?', Problem::STATE_SOLVED)			
		);
		
		// empty field
		if (count($_problems) == 0) {
			$this->render('optimal-order-empty');
			return;
		}
		$pkeys = array();
		foreach ($_problems as $p) {
			$pkeys[] = $p->Id;
		}
		
		//
		foreach ($_problems as $p) {
			
			// importance
			$_row = $iTable->fetchRow($iTable->select()
				->from(array('i' => 'problem_importance'), array())
				->columns(array(
					'total' => new Zend_Db_Expr('COUNT(*)'),
					'sum'	=> new Zend_Db_Expr('SUM(Importance)')
				))
				->where('ProblemId = ?', $p->Id)
			);
			$_importance = $_row->total != 0 ? $_row->sum / $_row->total :	0; 
			
			// dependencies
			$_solutions = $sTable->fetchAll($sTable->select()
				->from(array('s' => 'solutions'), array('Id', 'State', 'ProblemId'))
				->where('ProblemId = ?', $p->Id)
				->where('State = ?', Solution::STATE_APPROVED)
			);
			$_pdeps = array();
			foreach ($_solutions as $s) {
				$_dependencies = $dTable->fetchAll($dTable->select()
					->where('SolutionId = ?', $s->Id)
				);
				foreach ($_dependencies as $d) {
					if (in_array($d->ProblemId, $pkeys)) {
						$_pdeps[] = $d->ProblemId;
					} else {
						$WARNING_ZV = true;
					}
				}
			}
						
			// output format
			$PROBLEMS[] = array(
				'Id' => $p->Id,
				'Name'	=> $p->Name,
				'Created' => $p->Created,
				'Dependence' => $_pdeps,
				'Importance' => $_importance
			);
		}
		
		// order the list by importance
		usort($PROBLEMS, 'sortIt');
		
		// get points without dependence
		$QUEUE = array();
		foreach ($PROBLEMS as $p) {
			if (empty($p['Dependence'])) {
				$QUEUE[] = $p;
			}
		}
		if (empty($QUEUE)) {
			throw new Exception('EMPTY Queue !');
		}
		
		// ALgorithm
		$i = 0;
		while (count($QUEUE) > 0) {
			$i++;

			// 1. current problem
			$current = array_shift($QUEUE);
			
			// 2. presort by this rule
			// 2.1 najdem vsetky ktore maju v zavislosti tento bod
			$zavisiace = array(); 
			foreach ($PROBLEMS as $px) {
				if (in_array($current['Id'], $px['Dependence'])) {
					$zavisiace[] = $px;
				}
			}
			
			// 2.2 ak sa nachadzaju nizsie v poli ako aktualny, tak ich posuniem hore.
			foreach ($zavisiace as $z) {
				// ak ma zavisiace taku istu vahu ako nasledujuci, vymen ho s nasledujucim
				{
					// poradie
					for ($i=0; $i < count($PROBLEMS); $i++) {
						if ($PROBLEMS[$i]['Id'] == $z['Id']) {
							$por_zavisiace = $i;
							break;
						}
					}
					while (1) {
						// ak je na konci
						if ($por_zavisiace + 2 >= count($PROBLEMS)) {
							break;
						}
						if ($PROBLEMS[$por_zavisiace]['Importance'] == $PROBLEMS[$por_zavisiace+1]['Importance']) {
							// vymen
							$suc = $PROBLEMS[$por_zavisiace];
							$nasl = $PROBLEMS[$por_zavisiace + 1];
							$PROBLEMS[$por_zavisiace] = $nasl;
							$PROBLEMS[$por_zavisiace + 1] = $suc;
							$por_zavisiace ++;
						} else {
							break;
						}
					}
				}
				
				// - poradie current
				$por_current = 0;
				for ($i=0; $i<count($PROBLEMS); $i++) {
					if ($PROBLEMS[$i]['Id'] == $current['Id']) {
						$por_current = $i;
						break;
					}
				}
				
				
				// - poradie zavisiace 
				$por_zavisiace = 0;
				for ($i=0; $i<count($PROBLEMS); $i++) {
					if  ($PROBLEMS[$i]['Id'] == $z['Id']) {
						$por_zavisiace = $i;
						break;
					}
				}
				// ak je zavisiace nizsie (je vacsie poradie) tak zorad
				if ($por_current > $por_zavisiace) {

					
					
					
					
					// predsun aktualny pred toto poradie !
					$nPROBLEMS = array();
					foreach ($PROBLEMS as $key => $p) {
						
						// vynechaj zavisiace (to bude presunute pri aktualnom kluci)
						if ($key == $por_current) {
							continue;
						}
						
						// predsun zavisiace
						if ($key == $por_zavisiace) {
							$nPROBLEMS[] = $PROBLEMS[$por_current];
						}
						$nPROBLEMS[] = $p;
					}
					$PROBLEMS = $nPROBLEMS;
				}	
			}
						
			// 3. Pokracuj v spracovani zavisiacich  
			foreach ($zavisiace as $z) {
				$QUEUE[] = $z;
			}
		}
		$this->view->WARNING_ZV = $WARNING_ZV;
		$this->view->problems = $PROBLEMS;
	}
}
?>