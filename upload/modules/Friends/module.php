<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

class FriendsModule extends Module {

	public function __construct($language, $friendsLanguage, $user, $pages, $navigation, $queries, $smarty, $cache) {
		
		$module = [
			'name' => 'Friends',
			'author' => '<a href="https://xemah.com" target="_blank">Xemah</a>',
			'version' => '1.0',
			'namelessVersion' => '2.0.0-pr7'
		];

		parent::__construct($this, $module['name'], $module['author'], $module['version'], $module['namelessVersion']);

		$this->_module = $module;
		$this->_language = $language;
		$this->_friendsLanguage = $friendsLanguage;
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

		if ((PAGE !== 'profile') || (isset($_GET['error']))) {
			return;
		}

		$profileUser = explode('/', trim($_GET['route'], '/'));
		$profileUser = $profileUser[1];
		$profileUser = $this->_queries->getWhere('users', ['username', '=', $profileUser]);
		$profileUser = $profileUser[0];

		require(__DIR__ . '/classes/Friends.php');
		$friends = new Friends($user, $profileUser, $this->_friendsLanguage, $smarty);

		if (($user->isLoggedIn()) && ($user->data()->id !== $profileUser->id)) {
			$friends->processPost();
		}

		$smarty->assign('FRIENDS', $this->_friendsLanguage->get('general', 'friends'));
		$friends->generateTemplate();
		
	}
}
