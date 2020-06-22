<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

$friendsLanguage = new Language(__DIR__ . '/language', LANGUAGE);

require_once(__DIR__ . '/module.php');
$module = new FriendsModule($language, $friendsLanguage, $user, $pages, $navigation, $queries, $smarty, $cache);
