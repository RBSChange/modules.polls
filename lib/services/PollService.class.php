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
}