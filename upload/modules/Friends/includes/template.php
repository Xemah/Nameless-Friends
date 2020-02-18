<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

if (isset($user->data()->id) && $user->data()->id !== $profile[0]->id) {

	if (isFriends($user->data()->id, $profile[0]->id)) {
		$friend = array(
			'text' => $friendsLanguage->get('friends', 'remove_friend'),
			'icon' => '<i class="fas fa-user-times"></i>',
			'action' => 'removeFriend',
		);
	}

	else if (isRequested($user->data()->id, $profile[0]->id)) {
		$friend = array(
			'text' => $friendsLanguage->get('friends', 'cancel_friend_request'),
			'icon' => '<i class="fas fa-user-minus"></i>',
			'action' => 'cancelRequest',
		);
	}

	else if (isRequested($profile[0]->id, $user->data()->id)) {
		$friend = array(
			'text' => $friendsLanguage->get('friends', 'accept_friend_request'),
			'icon' => '<i class="fas fa-user-plus"></i>',
			'action' => 'acceptRequest',
		);
	}

	else {
		$friend = array(
			'text' => $friendsLanguage->get('friends', 'add_friend'),
			'icon' => '<i class="fas fa-user-plus"></i>',
			'action' => 'addFriend',
		);
	}

	$smarty->assign('FRIEND', $friend);

}

if (count(listFriends($profile[0]->id))) {

	foreach (listFriends($profile[0]->id) as $friend) {

		if ($friend->user_id == $profile[0]->id) {

			$friends[] = array(
				'id' => $friend->user_id,
				'avatar' => $user->getAvatar($friend->friend_id),
				'username' => $user->IdToName($friend->friend_id),
				'nickname' => $user->IdToNickname($friend->friend_id),
				'profile' => URL::build('/profile/' . $user->IdToName($friend->friend_id))
			);

		}

		else {

			$friends[] = array(
				'id' => $friend->user_id,
				'avatar' => $user->getAvatar($friend->user_id),
				'username' => $user->IdToName($friend->user_id),
				'nickname' => $user->IdToNickname($friend->user_id),
				'profile' => URL::build('/profile/' . $user->IdToName($friend->user_id))
			);

		}

	}
	
	$smarty->assign('FRIENDS_LIST', $friends);

}

else
	$smarty->assign('NO_FRIENDS', $friendsLanguage->get('friends', 'no_friends'));