<?php
/**
 * @package modules.polls.lib.services
 */
class polls_ModuleService extends ModuleBaseService
{
	/**
	 * Singleton
	 * @var polls_ModuleService
	 */
	private static $instance = null;

	/**
	 * @return polls_ModuleService
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = self::getServiceClassInstance(get_class());
		}
		return self::$instance;
	}
}