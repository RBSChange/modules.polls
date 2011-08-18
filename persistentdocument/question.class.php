<?php
/**
 * Class where to put your custom methods for document polls_persistentdocument_question
 * @package modules.polls.persistentdocument
 */
class polls_persistentdocument_question extends polls_persistentdocument_questionbase 
{
	/**
	 * @var polls_persistentdocument_poll
	 */
	private $poll;
	
	/**
	 * @return polls_persistentdocument_poll
	 */
	public function getPoll()
	{
		if ($this->poll === null)
		{
			$this->poll = $this->getDocumentService()->getParentOf($this);
		}
		return $this->poll;
	}
	
	/**
	 * @return boolean
	 */
	public function allowMultipleAnswers()
	{
		return $this->getMaxAnswers() == -1 || $this->getMaxAnswers() > 1;
	}
}