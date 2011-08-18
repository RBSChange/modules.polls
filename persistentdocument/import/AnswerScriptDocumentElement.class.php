<?php
/**
 * polls_AnswerScriptDocumentElement
 * @package modules.polls.persistentdocument.import
 */
class polls_AnswerScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return polls_persistentdocument_answer
     */
    protected function initPersistentDocument()
    {
    	return polls_AnswerService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_polls/answer');
	}
}