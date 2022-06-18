<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

require_once(__DIR__ . '/module.php');

if (!isset($profile_tabs)) {
	$profile_tabs = [];
}

$profile_tabs['friends'] = [
	'title' => FriendsModule::getLanguage('general', 'title'),
	'smarty_template' => 'friends/profile_tab.tpl',
	'require' => __DIR__ . '/profile_tab.php',
];

$module = new FriendsModule();