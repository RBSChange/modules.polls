<?php
/**
 * polls_AnswerService
 * @package modules.polls
 */
class polls_AnswerService extends f_persistentdocument_DocumentService
{
	/**
	 * @var polls_AnswerService
	 */
	private static $instance;

	/**
	 * @return polls_AnswerService
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
	 * @return polls_persistentdocument_answer
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_polls/answer');
	}

	/**
	 * Create a query based on 'modules_polls/answer' model.
	 * Return document that are instance of modules_polls/answer,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_polls/answer');
	}
	
	/**
	 * Create a query based on 'modules_polls/answer' model.
	 * Only documents that are strictly instance of modules_polls/answer
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_polls/answer', false);
	}
	
	/**
	 * this method is call before saving the duplicate document.
	 * If this method not override in the document service, the document isn't duplicable.
	 * An IllegalOperationException is so launched.
	 *
	 * @param polls_persistentdocument_answer $newDocument
	 * @param polls_persistentdocument_answer $originalDocument
	 * @param Integer $parentNodeId
	 *
	 * @throws IllegalOperationException
	 */
	protected function preDuplicate($newDocument, $originalDocument, $parentNodeId)
	{
		$newDocument->setVotes(0);
	}
}