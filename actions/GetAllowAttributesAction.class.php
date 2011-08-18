<?php
/**
 * polls_GetAllowAttributesAction
 * @package modules.polls.actions
 */
class polls_GetAllowAttributesAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();

		$result['containers'] = DocumentHelper::expandAllowAttribute('[modules_polls_question],[modules_list_list]');

		return $this->sendJSON($result);
	}
}