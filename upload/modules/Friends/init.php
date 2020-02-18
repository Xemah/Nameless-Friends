<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

$friendsLanguage = new Language(__DIR__ . '/language', LANGUAGE);

require_once(__DIR__ . '/module.php');
$module = new FriendsModule($friendsLanguage, $pages, $user, $queries, $navigation, $cache, $smarty);
