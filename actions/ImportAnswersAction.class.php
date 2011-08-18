<?php
/**
 * polls_ImportAnswersAction
 * @package modules.polls.actions
 */
class polls_ImportAnswersAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();

		$questionId = $request->getParameter('questionId');
		$sourceId = $request->getParameter('sourceId');
		polls_QuestionService::getInstance()->importAnswers($questionId, $sourceId);

		return $this->sendJSON($result);
	}
}