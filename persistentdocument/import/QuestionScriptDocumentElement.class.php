<?php
/**
 * polls_QuestionScriptDocumentElement
 * @package modules.polls.persistentdocument.import
 */
class polls_QuestionScriptDocumentElement extends import_ScriptDocumentElement
{
    /**
     * @return polls_persistentdocument_question
     */
    protected function initPersistentDocument()
    {
    	return polls_QuestionService::getInstance()->getNewDocumentInstance();
    }
    
    /**
	 * @return f_persistentdocument_PersistentDocumentModel
	 */
	protected function getDocumentModel()
	{
		return f_persistentdocument_PersistentDocumentModel::getInstanceFromDocumentModelName('modules_polls/question');
	}
}