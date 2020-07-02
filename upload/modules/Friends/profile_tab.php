<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

$profile_user = explode('/', trim($_GET['route'], '/'));
$profile_user = $profile_user[1];
$profile_user = $queries->getWhere('users', ['username', '=', $profile_user]);
$profile_user = $profile_user[0];

require(__DIR__ . '/classes/Friends.php');
$friends = new Friends($user, $profile_user, $language, $friends_language, $smarty);
$friends->processPost();

$friends_list = [];
$friends_query = $friends->query($profile_user->id);
if (!empty($friends_query)) {
	foreach ($friends_query as $friend) {
		$friend_id = (($friend->user_id == $profile_user->id) ? $friend->friend_id : $friend->user_id);
		$friends_list[] = [
			'id' => $friend_id,
			'avatar' => $user->getAvatar($friend_id),
			'username' => $user->IdToName($friend_id),
			'nickname' => $user->IdToNickname($friend_id),
			'style' => $user->getGroupClass($friend_id),
			'profile' => URL::build('/profile/' . $user->IdToName($friend_id)),
		];
	}
}

$smarty->assign('FRIENDS', [
	'title' => $friends_language->get('general', 'title'),
	'button' => $friends->generateButton(),
	'list' => $friends_list,
	'no_friends' => $friends_language->get('general', 'no_friends'),
]);