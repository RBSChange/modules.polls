<?php
/**
 * Class where to put your custom methods for document polls_persistentdocument_poll
 * @package modules.polls.persistentdocument
 */
class polls_persistentdocument_poll extends polls_persistentdocument_pollbase 
{
	/**
	 * @return integer[]
	 */
	public function getVoterIds()
	{
		$data = $this->getVoterIdsSerialized();
		return $data !== null ? unserialize($data) : array();
	}
		
	/**
	 * @param integer[] $ids
	 */
	public function setVoterIds($ids)
	{
		if (is_array($ids) && f_util_ArrayUtils::isNotEmpty($ids))
		{
			parent::setVoterIdsSerialized(serialize($ids));
		}
		else 
		{
			parent::setVoterIdsSerialized(null);
		}
	}

	/**
	 * @return integer[]
	 */
	public function addVoterId($id)
	{
		$ids = $this->getVoterIds();
		$ids[] = $id;
		$this->setVoterIds($ids);
	}
		
	/**
	 * @return boolean
	 */
	public function isFinished()
	{
		$endDate = $this->getEndDate();
		return ($endDate !== null && date_Calendar::getInstance($endDate)->belongsToPast());
	}
	
	/**
	 * @return boolean
	 */
	public function canShowResults()
	{
		return $this->getShowResultsBeforeEnd() || $this->isFinished();
	}
	
	/**
	 * @return boolean
	 */
	public function canCurrentUserVote()
	{
		$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		return $this->getDocumentService()->canVote($this, $user);
	}
}