<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

if (isset($user->data()->id) && $user->data()->id !== $profile[0]->id) {

	if (Input::exists()) {
		
		if (Token::check(Input::get('token'))) {

			switch (Input::get('action')) {

				case 'addFriend':
					if (!isFriends($user->data()->id, $profile[0]->id) && !isRequested($user->data()->id, $profile[0]->id)) {
						addFriend($user->data()->id, $profile[0]->id);
						$success = str_replace('{x}', $profile[0]->nickname, $friendsLanguage->get('friends', 'friend_request_sent'));
					}
					break;

				case 'removeFriend':
					if (isFriends($user->data()->id, $profile[0]->id)) {
						removeFriend($user->data()->id, $profile[0]->id);
						$success = str_replace('{x}', $profile[0]->nickname, $friendsLanguage->get('friends', 'friend_removed'));
					}
					break;
					
				case 'acceptRequest':
					if (!isFriends($user->data()->id, $profile[0]->id) && isRequested($profile[0]->id, $user->data()->id)) {
						acceptRequest($profile[0]->id, $user->data()->id);
						$success = str_replace('{x}', $profile[0]->nickname, $friendsLanguage->get('friends', 'friend_request_accepted'));
					}
					break;
					
				case 'cancelRequest':
					if (isRequested($user->data()->id, $profile[0]->id)) {
						cancelRequest($user->data()->id, $profile[0]->id);
						$success = str_replace('{x}', $profile[0]->nickname, $friendsLanguage->get('friends', 'friend_request_canceled'));
					}
					break;
			}
			
			if (isset($success))
			    $smarty->assign('SUCCESS', $success);

		}

		else
			$smarty->assign('ERROR', $language->get('general', 'invalid_token'));
	}
}