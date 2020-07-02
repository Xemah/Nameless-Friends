<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

class FriendsModule extends Module {

	public function __construct($language, $user, $pages, $navigation, $queries, $smarty, $cache) {
		
		$module = [
			'name' => 'Friends',
			'author' => '<a href="https://xemah.com" target="_blank">Xemah</a>',
			'version' => '2.0',
			'nameless_version' => '2.0.0-pr7'
		];

		parent::__construct($this, $module['name'], $module['author'], $module['version'], $module['nameless_version']);

		$this->_module = $module;
		$this->_queries = $queries;

	}

	public function onInstall() {

		try {
			$this->_queries->alterTable('friends', 'accepted', 'tinyint(1) NOT NULL DEFAULT 0');
		} catch(Exception $e) {
			// ...
		}
		
	}

	public function onUninstall() {
		// ...
	}

	public function onEnable() {
		// ...
	}

	public function onDisable() {
		// ...
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template) {
		// ...
	}

}
