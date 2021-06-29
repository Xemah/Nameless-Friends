<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

class FriendsModule extends Module
{
	public function __construct($language, $user, $pages, $navigation, $queries, $smarty, $cache)
	{
		$module = [
			'name' => 'Friends',
			'author' => '<a href="https://xemah.com" target="_blank">Xemah</a>',
			'version' => '2.3',
			'namelessVersion' => '2.0.0-pr10'
		];

		parent::__construct($this, $module['name'], $module['author'], $module['version'], $module['namelessVersion']);

		$this->_queries = $queries;
	}

	public function onInstall()
	{
		// ...
	}

	public function onUninstall()
	{
		// ...
	}

	public function onEnable()
	{
		try {
			$this->_queries->alterTable('friends', 'accepted', 'tinyint(1) NOT NULL DEFAULT 0');
		} catch(Exception $e) {
			// ...
		}
	}

	public function onDisable()
	{
		// ...
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template)
	{
		// ...
	}
}
