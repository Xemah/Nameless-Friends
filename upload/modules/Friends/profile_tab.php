<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

$profileUser = explode('/', trim($_GET['route'], '/'));
$profileUser = $profileUser[1];
$profileUser = DB::getInstance()->get('users', ['username', '=', $profileUser])->results();
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
            'style' => $friendUser->getGroupStyle(),
            'title' => Output::getClean($friendUser->data()->user_title),
        ];
    }
}

$smarty->assign('FRIENDS', [
    'title' => FriendsModule::getLanguage('general', 'title'),
    'button' => $friends->generateButton(),
    'list' => $friendsList,
    'no_friends' => FriendsModule::getLanguage('general', 'no_friends'),
]);