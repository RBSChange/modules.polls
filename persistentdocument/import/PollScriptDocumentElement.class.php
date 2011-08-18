<?php
/**
 * polls_PollScriptDocumentElement
 * @package modules.polls.persistentdocument.import
 */
class polls_PollScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return polls_persistentdocument_poll
     */
    protected function initPersistentDocument()
    {
    	return polls_PollService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_polls/poll');
	}
}