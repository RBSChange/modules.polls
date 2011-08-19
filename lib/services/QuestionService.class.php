<?php
/**
 * polls_QuestionService
 * @package modules.polls
 */
class polls_QuestionService extends f_persistentdocument_DocumentService
{
	/**
	 * @var polls_QuestionService
	 */
	private static $instance;

	/**
	 * @return polls_QuestionService
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
	 * @return polls_persistentdocument_question
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_polls/question');
	}

	/**
	 * Create a query based on 'modules_polls/question' model.
	 * Return document that are instance of modules_polls/question,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->pp->createQuery('modules_polls/question');
	}
	
	/**
	 * Create a query based on 'modules_polls/question' model.
	 * Only documents that are strictly instance of modules_polls/question
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->pp->createQuery('modules_polls/question', false);
	}
	
	/**
	 * @param integer $questionId
	 * @param integer $sourceId
	 */
	public function importAnswers($questionId, $sourceId)
	{
		$desctination = polls_persistentdocument_question::getInstanceById($questionId);
		$source = DocumentHelper::getDocumentInstance($sourceId);
		if ($source instanceof polls_persistentdocument_question)
		{
			foreach ($source->getChildrenAnswers() as $answer)
			{
				/* @var $answer polls_persistentdocument_answer */
				$answer->getDocumentService()->duplicate($answer->getId(), $desctination->getId());
			}
		}
		elseif ($source instanceof list_persistentdocument_list)
		{
			$as = polls_AnswerService::getInstance();
			foreach ($source->getItems() as $item)
			{
				/* @var $answer list_Item */
				$answer = $as->getNewDocumentInstance();
				$answer->setLabel($item->getLabel());
				$answer->save($desctination->getId());
			}
		}
		else 
		{
			throw new Excexption('Unexpected source container type: ' . get_class($source));
		}
	}
	
	/**
	 * @param polls_persistentdocument_question $document
	 * @return void
	 */
	protected function preDelete($document)
	{
		// Cascade delete all answers.
		foreach ($this->getChildrenOf($document) as $answer)
		{
			$answer->delete();
		}
	}

	/**
	 * this method is call before saving the duplicate document.
	 * If this method not override in the document service, the document isn't duplicable.
	 * An IllegalOperationException is so launched.
	 *
	 * @param polls_persistentdocument_question $newDocument
	 * @param polls_persistentdocument_question $originalDocument
	 * @param Integer $parentNodeId
	 *
	 * @throws IllegalOperationException
	 */
	protected function preDuplicate($newDocument, $originalDocument, $parentNodeId)
	{
	}

	/**
	 * this method is call after saving the duplicate document.
	 * $newDocument has an id affected.
	 * Traitment of the children of $originalDocument.
	 *
	 * @param polls_persistentdocument_question $newDocument
	 * @param polls_persistentdocument_question $originalDocument
	 * @param Integer $parentNodeId
	 *
	 * @throws IllegalOperationException
	 */
	protected function postDuplicate($newDocument, $originalDocument, $parentNodeId)
	{
		foreach ($originalDocument->getChildrenAnswers() as $answer)
		{
			$answer->getDocumentService()->duplicate($answer->getId(), $newDocument->getId());
		}
	}
}