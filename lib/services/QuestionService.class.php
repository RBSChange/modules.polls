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
	 * @param Integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
//	protected function preSave($document, $parentNodeId)
//	{
//
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preInsert($document, $parentNodeId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postInsert($document, $parentNodeId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function preUpdate($document, $parentNodeId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postUpdate($document, $parentNodeId)
//	{
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @param Integer $parentNodeId Parent node ID where to save the document.
	 * @return void
	 */
//	protected function postSave($document, $parentNodeId)
//	{
//	}

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
	 * @param polls_persistentdocument_question $document
	 * @return void
	 */
//	protected function preDeleteLocalized($document)
//	{
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @return void
	 */
//	protected function postDelete($document)
//	{
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @return void
	 */
//	protected function postDeleteLocalized($document)
//	{
//	}

	/**
	 * @param polls_persistentdocument_question $document
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
	 * @param polls_persistentdocument_question $document
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
	 * @param polls_persistentdocument_question $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagAdded($document, $tag)
//	{
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @param String $tag
	 * @return void
	 */
//	public function tagRemoved($document, $tag)
//	{
//	}

	/**
	 * @param polls_persistentdocument_question $fromDocument
	 * @param f_persistentdocument_PersistentDocument $toDocument
	 * @param String $tag
	 * @return void
	 */
//	public function tagMovedFrom($fromDocument, $toDocument, $tag)
//	{
//	}

	/**
	 * @param f_persistentdocument_PersistentDocument $fromDocument
	 * @param polls_persistentdocument_question $toDocument
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
	 * @param polls_persistentdocument_question $document
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

	/**
	 * @param website_UrlRewritingService $urlRewritingService
	 * @param polls_persistentdocument_question $document
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
	 * @param polls_persistentdocument_question $document
	 * @return integer | null
	 */
//	public function getWebsiteId($document)
//	{
//		return parent::getWebsiteId($document);
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @return integer[] | null
	 */
//	public function getWebsiteIds($document)
//	{
//		return parent::getWebsiteIds($document);
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @return website_persistentdocument_page | null
	 */
//	public function getDisplayPage($document)
//	{
//		return parent::getDisplayPage($document);
//	}

	/**
	 * @param polls_persistentdocument_question $document
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
	 * @param polls_persistentdocument_question $document
	 * @param string $bockName
	 * @return array with entries 'module' and 'template'. 
	 */
//	public function getSolrsearchResultItemTemplate($document, $bockName)
//	{
//		return array('module' => 'polls', 'template' => 'Polls-Inc-QuestionResultDetail');
//	}

	/**
	 * @param polls_persistentdocument_question $document
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
//	public function addTreeAttributes($document, $moduleName, $treeType, &$nodeAttributes)
//	{
//	}
	
	/**
	 * @param polls_persistentdocument_question $document
	 * @param String[] $propertiesName
	 * @param Array $datas
	 */
//	public function addFormProperties($document, $propertiesName, &$datas)
//	{
//	}
		
}