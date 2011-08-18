<?php
/**
 * polls_PollService
 * @package modules.polls
 */
class polls_PollService extends f_persistentdocument_DocumentService
{
	/**
	 * @var polls_PollService
	 */
	private static $instance;

	/**
	 * @return polls_PollService
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}

	/**
	 * @return polls_persistentdocument_poll
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_polls/poll');
	}

	/**
	 * Create a query based on 'modules_polls/poll' model.
	 * Return document that are instance of modules_polls/poll,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_polls/poll');
	}
	
	/**
	 * Create a query based on 'modules_polls/poll' model.
	 * Only documents that are strictly instance of modules_polls/poll
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_polls/poll', false);
	}
	
	/**
	 * @param polls_persistentdocument_poll $poll
	 * @param users_persistentdocument_websitefrontenduser $user
	 */
	public function canVote($poll, $user = null)
	{
		if ($poll->isFinished())
		{
			return false;
		}
		elseif ($user !== null)
		{
			return !in_array($user->getId(), $poll->getVoterIds());
		}
		return $poll->getAllowAnonymousVote() && !$this->hasCookie($poll);
	}
	
	/**
	 * @param polls_persistentdocument_poll $poll
	 * @param polls_persistentdocument_answers[] $answers
	 * @param users_persistentdocument_websitefrontenduser $user
	 */
	public function vote($poll, $answers, $user = null)
	{
		$tm = f_persistentdocument_TransactionManager::getInstance();
		try 
		{
			$tm->beginTransaction();
			
			foreach ($answers as $answer)
			{
				/* @var $answer polls_persistentdocument_answer */
				$answer->setVotes($answer->getVotes()+1);
				$answer->save();
			}
			
			$poll->setVoterCount($poll->getVoterCount()+1);
			if ($user !== null)
			{ 
				$poll->addVoterId($user->getId());
			}
			$poll->save();
			$this->setCookie($poll);
			
			$tm->commit();
		}
		catch (Exception $e)
		{
			$tm->rollback($e);
		}
	}
	
	/**
	 * @param polls_persistentdocument_poll $poll
	 */
	private function setCookie($poll)
	{
		$id = $poll->getId();
		$date = date_Calendar::getInstance()->add(date_Calendar::YEAR, 1);
		setcookie('rbschangevote' . $id, $id, $date->getTimestamp());
	}
	
	/**
	 * @param poll_persistentdocument_poll $poll
	 * @return Boolean
	 */
	private function hasCookie($poll)
	{
		return isset($_COOKIE['rbschangevote' . $poll->getId()]);
	}
	
	/**
	 * @param polls_persistentdocument_poll $document
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
//	protected function preSave($document, $parentNodeId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preInsert($document, $parentNodeId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postSave($document, $parentNodeId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @return void
	 */
	protected function preDelete($document)
	{
		// Cascade delete all questions.
		foreach ($this->getChildrenOf($document) as $question)
		{
			$question->delete();
		}
	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @return void
	 */
//	protected function postDelete($document)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @return boolean true if the document is publishable, false if it is not.
	 */
//	public function isPublishable($document)
//	{
//		$result = parent::isPublishable($document);
//		return $result;
//	}


	/**
	 * Methode à surcharger pour effectuer des post traitement apres le changement de status du document
	 * utiliser $document->getPublicationstatus() pour retrouver le nouveau status du document.
	 * @param polls_persistentdocument_poll $document
	 * @param String $oldPublicationStatus
	 * @param array<"cause" => String, "modifiedPropertyNames" => array, "oldPropertyValues" => array> $params
	 * @return void
	 */
//	protected function publicationStatusChanged($document, $oldPublicationStatus, $params)
//	{
//	}

	/**
	 * Correction document is available via $args['correction'].
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Array<String=>mixed> $args
	 */
//	protected function onCorrectionActivated($document, $args)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $fromDocument
	 * @param f_persistentdocument_PersistentDocument $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedFrom($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * @param f_persistentdocument_PersistentDocument $fromDocument
	 * @param polls_persistentdocument_poll $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedTo($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * Called before the moveToOperation starts. The method is executed INSIDE a
	 * transaction.
	 *
	 * @param f_persistentdocument_PersistentDocument $document
	 * @param Integer $destId
	 */
//	protected function onMoveToStart($document, $destId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param Integer $destId
	 * @return void
	 */
//	protected function onDocumentMoved($document, $destId)
//	{
//	}

	/**
	 * this method is call before saving the duplicate document.
	 * If this method not override in the document service, the document isn't duplicable.
	 * An IllegalOperationException is so launched.
	 *
	 * @param polls_persistentdocument_poll $newDocument
	 * @param polls_persistentdocument_poll $originalDocument
	 * @param Integer $parentNodeId
	 *
	 * @throws IllegalOperationException
	 */
	protected function preDuplicate($newDocument, $originalDocument, $parentNodeId)
	{
		$newDocument->setVoterCount(0);
		$newDocument->setVoterIdsSerialized(null);
	}

	/**
	 * this method is call after saving the duplicate document.
	 * $newDocument has an id affected.
	 * Traitment of the children of $originalDocument.
	 *
	 * @param polls_persistentdocument_poll $newDocument
	 * @param polls_persistentdocument_poll $originalDocument
	 * @param Integer $parentNodeId
	 *
	 * @throws IllegalOperationException
	 */
	protected function postDuplicate($newDocument, $originalDocument, $parentNodeId)
	{
		foreach ($originalDocument->getChildrenQuestions() as $question)
		{
			$question->getDocumentService()->duplicate($question->getId(), $newDocument->getId());
		}
	}

	/**
	 * @param website_UrlRewritingService $urlRewritingService
	 * @param polls_persistentdocument_poll $document
	 * @param website_persistentdocument_website $website
	 * @param string $lang
	 * @param array $parameters
	 * @return f_web_Link | null
	 */
//	public function getWebLink($urlRewritingService, $document, $website, $lang, $parameters)
//	{
//		return null;
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @return integer | null
	 */
//	public function getWebsiteId($document)
//	{
//		return parent::getWebsiteId($document);
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @return integer[] | null
	 */
//	public function getWebsiteIds($document)
//	{
//		return parent::getWebsiteIds($document);
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @return website_persistentdocument_page | null
	 */
//	public function getDisplayPage($document)
//	{
//		return parent::getDisplayPage($document);
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param string $forModuleName
	 * @param array $allowedSections
	 * @return array
	 */
//	public function getResume($document, $forModuleName, $allowedSections = null)
//	{
//		$resume = parent::getResume($document, $forModuleName, $allowedSections);
//		return $resume;
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param string $bockName
	 * @return array with entries 'module' and 'template'. 
	 */
//	public function getSolrsearchResultItemTemplate($document, $bockName)
//	{
//		return array('module' => 'polls', 'template' => 'Polls-Inc-PollResultDetail');
//	}

	/**
	 * @param polls_persistentdocument_poll $document
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
//	public function addTreeAttributes($document, $moduleName, $treeType, &$nodeAttributes)
//	{
//	}
	
	/**
	 * @param polls_persistentdocument_poll $document
	 * @param String[] $propertiesName
	 * @param Array $datas
	 */
//	public function addFormProperties($document, $propertiesName, &$datas)
//	{
//	}
		
}