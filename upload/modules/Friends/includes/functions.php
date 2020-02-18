<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

function queryFriends($user1, $user2, $irreversible = 0)  {

	$data = DB::getInstance()->query('
		SELECT * FROM nl2_friends
		WHERE user_id = ' . $user1 . ' AND friend_id = ' . $user2
	, array())->results();

	if ($irreversible !== 1 && !count($data)) {
		$data = DB::getInstance()->query('
			SELECT * FROM nl2_friends
			WHERE user_id = ' . $user2 . ' AND friend_id = ' . $user1
		, array())->results();
	}

	return $data;
}

function addFriend($user1, $user2) {
	$data = queryFriends($user1, $user2);
	global $user;

	if (!count($data)) {
		try {
			DB::getInstance()->query('
				INSERT INTO nl2_friends (user_id, friend_id)
				VALUES (' . $user1 . ', ' . $user2 . ')
			', array());

		}
		catch (Exception $e) {
		}

		Alert::create(
			$user2, 'friend_request',
			array('path' => ROOT_PATH . '/modules/Friends/language', 'file' => 'friends', 'term' => 'received_friend_request', 'replace' => array('{x}'), 'replace_with' => array(Output::getClean($user->IdToNickname($user1)))),
			array('path' => ROOT_PATH . '/modules/Friends/language', 'file' => 'friends', 'term' => 'received_friend_request', 'replace' => array('{x}'), 'replace_with' => array(Output::getClean($user->IdToNickname($user1)))),
			URL::build('/profile/' . $user->IdToName($user1))
		);
	}

}

function removeFriend($user1, $user2) {
	$data = queryFriends($user1, $user2);

	if (count($data) && $data[0]->accepted == 1) {
		try {
			DB::getInstance()->query('
				DELETE FROM nl2_friends
				WHERE id = ' . $data[0]->id
			, array());
		}
		catch (Exception $e) {
		}
	}

}

function acceptRequest($user1, $user2) {
	$data = queryFriends($user1, $user2);
	global $user;

	if (count($data) && $data[0]->accepted == 0) {
		try {
			DB::getInstance()->query('
				UPDATE nl2_friends
				SET accepted = 1 WHERE id = ' . $data[0]->id
			, array());
		}
		catch (Exception $e) {
		}

		Alert::create(
			$user1, 'friend_request',
			array('path' => ROOT_PATH . '/modules/Friends/language', 'file' => 'friends', 'term' => 'accepted_friend_request', 'replace' => array('{x}'), 'replace_with' => array(Output::getClean($user->IdToNickname($user2)))),
			array('path' => ROOT_PATH . '/modules/Friends/language', 'file' => 'friends', 'term' => 'accepted_friend_request', 'replace' => array('{x}'), 'replace_with' => array(Output::getClean($user->IdToNickname($user2)))),
			URL::build('/profile/' . $user->IdToName($user2))
		);
	}

}

function cancelRequest($user1, $user2) {
	$data = queryFriends($user1, $user2);

	if (count($data) && $data[0]->accepted == 0) {
		try {
			DB::getInstance()->query('
				DELETE FROM nl2_friends
				WHERE id = ' . $data[0]->id
			, array());
		}
		catch (Exception $e) {
		}
	}

}

function isFriends($user1, $user2) {
	$data = queryFriends($user1, $user2);
	return ((count($data) && $data[0]->accepted == 1) ? 1 : 0);
}

function isRequested($user1, $user2) {
	$data = queryFriends($user1, $user2, 1);
	return ((count($data) && $data[0]->accepted == 0) ? 1 : 0);
}

function listFriends($user) {
	$data = DB::getInstance()->query('
		SELECT * FROM nl2_friends
		WHERE (user_id = ' . $user . ' AND accepted = 1) OR (friend_id = ' . $user . ' AND accepted = 1)
	', array())->results();
	return $data;
}