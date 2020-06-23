<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/
 
class Friends {

	public function __construct($user, $profileUser, $friendsLanguage, $smarty) {

		$this->_db = DB::getInstance();
		$this->_user = $user;
		$this->_profileUser = $profileUser;
		$this->_friendsLanguage = $friendsLanguage;
		$this->_smarty = $smarty;
		$this->_modulePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'); 

	}

	
	private function query($user) {

		$query = $this->_db->query("SELECT * FROM nl2_friends WHERE (user_id = $user AND accepted = 1) OR (friend_id = $user AND accepted = 1)");
		$data = $query->results();

		return ($data ? $data : []);

	}

	private function queryFriend($user1, $user2, $reversible = 1) {

		$query = $this->_db->query("SELECT * FROM nl2_friends WHERE user_id = $user1 AND friend_id = $user2");
		$data = $query->results();

		if (empty($data) && $reversible) {
			$query = $this->_db->query("SELECT * FROM nl2_friends WHERE user_id = $user2 AND friend_id = $user1");
			$data = $query->results();
		}

		return (!empty($data) ? $data[0] : null);

	}

	private function addFriend($user1, $user2) {

		$data = $this->queryFriend($user1, $user2);
		if ($data) {
			return;
		}

		try {
			$this->_db->query("INSERT INTO nl2_friends (user_id, friend_id) VALUES ($user1, $user2)");
		} catch (Exception $e) {
			// ...
		}

		Alert::create(
			$user2, 'friend_request',
			['path' => $this->_modulePath . '/language', 'file' => 'general', 'term' => 'received_friend_request', 'replace' => ['{x}'], 'replace_with' => [Output::getClean($this->_user->idToName($user1))]],
			['path' => $this->_modulePath . '/language', 'file' => 'general', 'term' => 'received_friend_request', 'replace' => ['{x}'], 'replace_with' => [Output::getClean($this->_user->idToName($user1))]],
			URL::build('/profile/' . $this->_user->idToName($user1))
		);

	}

	private function removeFriend($user1, $user2) {

		$data = $this->queryFriend($user1, $user2);
		if (!$data) {
			return;
		}
		
		try {
			$this->_db->query("DELETE FROM nl2_friends WHERE id = $data->id");
		} catch (Exception $e) {
			// ...
		}
	
	}

	private function acceptRequest($user1, $user2) {

		$data = $this->queryFriend($user1, $user2);
		if ((!$data) || ($data->accepted)) {
			return;
		}

		try {
			$this->_db->query("UPDATE nl2_friends SET accepted = 1 WHERE id = $data->id");
		} catch (Exception $e) {
			// ...
		}

		Alert::create(
			$user2, 'friend_request',
			['path' => $this->_modulePath . '/language', 'file' => 'general', 'term' => 'accepted_friend_request', 'replace' => ['{x}'], 'replace_with' => [Output::getClean($this->_user->idToName($user1))]],
			['path' => $this->_modulePath . '/language', 'file' => 'general', 'term' => 'accepted_friend_request', 'replace' => ['{x}'], 'replace_with' => [Output::getClean($this->_user->idToName($user1))]],
			URL::build('/profile/' . $this->_user->idToName($user1))
		);
	
	}
	
	private function cancelRequest($user1, $user2) {

		$data = $this->queryFriend($user1, $user2);
		if ((!$data) || ($data->accepted)) {
			return;
		}

		try {
			$this->_db->query("DELETE FROM nl2_friends WHERE id = $data->id");
		} catch (Exception $e) {
			// ...
		}
	
	}

	private function isFriends($user1, $user2) {

		$data = $this->queryFriend($user1, $user2);
		return ((($data) && ($data->accepted)) ? true : false);

	}
	
	private function isRequested($user1, $user2) {

		$data = $this->queryFriend($user1, $user2, false);
		return ((($data) && (!$data->accepted)) ? true : false);

	}

	public function processPost() {

		if ((!$this->_user->isLoggedIn()) || (!$this->_profileUser)) {
			return;
		}

		if (!Input::exists()) {
			return;
		}

		if (!Token::check(Input::get('token'))) {
			$smarty->assign('ERROR', $language->get('general', 'invalid_token'));
			return;
		}

		if (!Input::get('action')) {
			return;
		}

		switch (Input::get('action')) {
		
			case 'addFriend':
				if ((!$this->isFriends($this->_user->data()->id, $this->_profileUser->id)) && (!$this->isRequested($this->_user->data()->id, $this->_profileUser->id))) {
					$this->addFriend($this->_user->data()->id, $this->_profileUser->id);
					$success = str_replace('{x}', $this->_profileUser->username, $this->_friendsLanguage->get('general', 'friend_request_sent'));
				}
				break;

			case 'removeFriend':
				if ($this->isFriends($this->_user->data()->id, $this->_profileUser->id)) {
					$this->removeFriend($this->_user->data()->id, $this->_profileUser->id);
					$success = str_replace('{x}', $this->_profileUser->username, $this->_friendsLanguage->get('general', 'friend_removed'));
				}
				break;
				
			case 'acceptRequest':
				if ((!$this->isFriends($this->_user->data()->id, $this->_profileUser->id)) && ($this->isRequested($this->_profileUser->id, $this->_user->data()->id))) {
					$this->acceptRequest($this->_profileUser->id, $this->_user->data()->id);
					$success = str_replace('{x}', $this->_profileUser->username, $this->_friendsLanguage->get('general', 'friend_request_accepted'));
				}
				break;
				
			case 'cancelRequest':
				if ($this->isRequested($this->_user->data()->id, $this->_profileUser->id)) {
					$this->cancelRequest($this->_user->data()->id, $this->_profileUser->id);
					$success = str_replace('{x}', $this->_profileUser->username, $this->_friendsLanguage->get('general', 'friend_request_canceled'));
				}
				break;

		}

		if (isset($success)) {
			$this->_smarty->assign('SUCCESS', $success);
		}

	}

	public function generateTemplate() {

		if (($this->_user->isLoggedIn()) && ($this->_profileUser)) {
		
			if ($this->isFriends($this->_user->data()->id, $this->_profileUser->id)) {

				$friend = [
					'text' => $this->_friendsLanguage->get('general', 'remove_friend'),
					'icon' => '<i class="fas fa-user-times fa-fw"></i>',
					'action' => 'removeFriend',
				];

			} else if ($this->isRequested($this->_user->data()->id, $this->_profileUser->id)) {

				$friend = [
					'text' => $this->_friendsLanguage->get('general', 'cancel_friend_request'),
					'icon' => '<i class="fas fa-user-minus fa-fw"></i>',
					'action' => 'cancelRequest',
				];

			} else if ($this->isRequested($this->_profileUser->id, $this->_user->data()->id)) {

				$friend = [
					'text' => $this->_friendsLanguage->get('general', 'accept_friend_request'),
					'icon' => '<i class="fas fa-user-check fa-fw"></i>',
					'action' => 'acceptRequest',
				];

			} else {

				$friend = [
					'text' => $this->_friendsLanguage->get('general', 'add_friend'),
					'icon' => '<i class="fas fa-user-plus fa-fw"></i>',
					'action' => 'addFriend',
				];

			}
		
			$this->_smarty->assign('FRIEND', $friend);

		}

		$friendsQuery = $this->query($this->_profileUser->id);

		if (empty($friendsQuery)) {

			$this->_smarty->assign('NO_FRIENDS', $this->_friendsLanguage->get('general', 'no_friends'));

		} else {

			$friends = [];
			foreach ($friendsQuery as $friend) {
				$friendID = (($friend->user_id == $this->_profileUser->id) ? $friend->friend_id : $friend->user_id);
				$friends[] = [
					'id' => $friendID,
					'avatar' => $this->_user->getAvatar($friendID),
					'username' => $this->_user->IdToName($friendID),
					'nickname' => $this->_user->IdToNickname($friendID),
					'profile' => URL::build('/profile/' . $this->_user->IdToName($friendID)),
				];
			}

			$this->_smarty->assign('FRIENDS_LIST', $friends);
			
		}
		
	}

}