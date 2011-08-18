<?php
/**
 * Class where to put your custom methods for document polls_persistentdocument_answer
 * @package modules.polls.persistentdocument
 */
class polls_persistentdocument_answer extends polls_persistentdocument_answerbase 
{
	/**
	 * @var polls_persistentdocument_question
	 */
	private $question;
		
	/**
	 * @return polls_persistentdocument_question
	 */
	public function getQuestion()
	{
		if ($this->question === null)
		{
			$this->question = $this->getDocumentService()->getParentOf($this);
		}
		return $this->question;
	}
	
	/**
	 * @return polls_persistentdocument_poll
	 */
	public function getPoll()
	{
		return $this->getQuestion()->getPoll();
	}
	
	/**
	 * @return integer
	 */
	public function getPercentage()
	{
		if ($this->getVotes() < 1)
		{
			return 0;
		}
		return ($this->getVotes() * 100) / $this->getPoll()->getVoterCount();
	}
	
	/**
	 * @param integer $decimals
	 * @return string
	 */
	public function getFormattedPercentage($decimals = 0)
	{
		return LocaleService::getInstance()->transFO('m.polls.fo.percentage-pattern', array(), array('value' => number_format($this->getPercentage(), $decimals)));
	}
}