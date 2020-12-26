<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

$profileUser = explode('/', trim($_GET['route'], '/'));
$profileUser = $profileUser[1];
$profileUser = $queries->getWhere('users', ['username', '=', $profileUser]);
$profileUser = $profileUser[0];

require(__DIR__ . '/classes/Friends.php');
$friends = new Friends($user, $profileUser, $language, $friendsLanguage, $smarty);
$friends->processPost();

$friendsList = [];
$friendsQuery = $friends->query($profileUser->id);
if (!empty($friendsQuery)) {
	foreach ($friendsQuery as $friend) {
		$friendID = (($friend->user_id == $profileUser->id) ? $friend->friend_id : $friend->user_id);
		$friendUser = new User($friendID);
		$friendsList[] = [
			'id' => $friendUser->data()->id,
			'uuid' => $friendUser->data()->uuid,
			'avatar' => $friendUser->getAvatar(),
			'profile' => $friendUser->getProfileURL(),
			'username' => $friendUser->getDisplayname(true),
			'nickname' => $friendUser->getDisplayname(),
			'style' => $friendUser->getGroupClass(),
			'title' => Output::getClean($friendUser->data()->user_title),
		];
	}
}

$smarty->assign('FRIENDS', [
	'title' => $friendsLanguage->get('general', 'title'),
	'button' => $friends->generateButton(),
	'list' => $friendsList,
	'no_friends' => $friendsLanguage->get('general', 'no_friends'),
]);