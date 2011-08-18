<?php
/**
 * polls_BlockPollAction
 * @package modules.polls.lib.blocks
 */
class polls_BlockPollAction extends website_BlockAction
{
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		if ($this->isInBackofficeEdition())
		{
			return website_BlockView::NONE;
		}
		
		$poll = $this->getDocumentParameter();
		if (!($poll instanceof polls_persistentdocument_poll) || !$poll->isPublished())
		{
			return website_BlockView::NONE;
		}
		$request->setAttribute('poll', $poll);
	
		$ps = $poll->getDocumentService();
		$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		if (!$request->getParameter('viewResults') && $ps->canVote($poll, $user))
		{
			return website_BlockView::INPUT;
		}
		
		if ($user === null && !$poll->isFinished() && !$poll->getAllowAnonymousVote())
		{
			$this->addError(LocaleService::getInstance()->transFO('m.polls.fo.must-login-to-vote', array('ucf')));
		}
		return website_BlockView::SUCCESS;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function executeVote($request, $response)
	{
		$poll = $this->getDocumentParameter();
		if (!($poll instanceof polls_persistentdocument_poll) || !$poll->isPublished())
		{
			return website_BlockView::NONE;
		}
		/* @var $poll polls_persistentdocument_poll */
		$request->setAttribute('poll', $poll);
		
		$ps = $poll->getDocumentService();
		$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		if (!$ps->canVote($poll, $user))
		{
			return website_BlockView::ERROR;
		}
		
		$hasErrors = false;
		$votedAnswers = array();
		foreach ($poll->getChildrenQuestions() as $question)
		{
			/* @var $question polls_persistentdocument_question */
			$key = 'question' . $question->getId();
			if (!$request->hasNonEmptyParameter($key))
			{
				$this->addError(LocaleService::getInstance()->transFO('m.polls.fo.question-without-answer', array('ucf')), $key);
				$hasErrors = true;
				continue;
			}
		
			$maxAnswers = $question->getMaxAnswers();
			$vote = $request->getParameter($key);
			if (!is_array($vote))
			{
				$vote = array($vote);
			}
			
			if ($maxAnswers >= 1 && count($vote) > $maxAnswers)
			{
				$this->addError(LocaleService::getInstance()->transFO('m.polls.fo.question-do-not-allow-more-than-n-answers', array('ucf'), array('maxAnswers' => $maxAnswers)), $key);
				$hasErrors = true;
				continue;
			}
			
			foreach ($vote as $answerId)
			{
				$answer = polls_persistentdocument_answer::getInstanceById($answerId);
				if ($answer->getQuestion()->getId() !== $question->getId())
				{
					$this->addError(LocaleService::getInstance()->transFO('m.polls.fo.do-not-cheat', array('ucf'), array('maxAnswers' => $maxAnswers)), $key);
					$hasErrors = true;
					continue;
				}				
				$votedAnswers[] = $answer;
			}
		}
		
		if ($hasErrors)
		{
			return website_BlockView::INPUT;
		}
		
		$ps->vote($poll, $votedAnswers, $user);
			
		HttpController::getInstance()->redirectToUrl(LinkHelper::getCurrentUrl());
	}
}