<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

class FriendsModule extends Module {
	private $_friendsLanguage;

	public function __construct($friendsLanguage, $pages, $user, $queries, $navigation, $cache, $smarty) {
		
		$module = array(
			'name' => 'Friends',
			'author' => '<a href="https://xemah.com" target="_blank">Xemah</a>',
			'version' => '1.0',
			'namelessVersion' => '2.0.0-pr7'
		);

		parent::__construct($this, $module['name'], $module['author'], $module['version'], $module['namelessVersion']);

		$this->_friendsLanguage = $friendsLanguage;
		$this->_queries = $queries;
		$this->_navigation = $navigation;
		$this->_cache = $cache;
		$this->_smarty = $smarty;

	}

	public function onInstall(){

		try {
			$this->_queries->alterTable('friends', 'accepted', 'tinyint(1) NOT NULL DEFAULT 0');
		}
		catch(Exception $e) {
		}
		
	}

	public function onUninstall(){
	}

	public function onEnable(){
	}

	public function onDisable(){
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template){

		$queries = $this->_queries;
		$smarty = $this->_smarty;
		$friendsLanguage = $this->_friendsLanguage;

		$user = new User();

		if (PAGE == 'profile' && !isset($_GET['error'])) {

			$profile = explode('/', rtrim($_GET['route'], '/'));
			$profile = $profile[count($profile) - 1];
			$profile = $queries->getWhere('users', array('username', '=', $profile));
			
			$smarty->assign('FRIENDS', $friendsLanguage->get('friends', 'friends'));

			require_once('includes/functions.php');
			require_once('includes/post.php');
			require_once('includes/template.php');

		}
	}
}
