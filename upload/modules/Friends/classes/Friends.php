<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/
 
class Friends
{
	public function __construct($user, $profileUser, $language, $friendsLanguage, $smarty)
	{
		$this->_db = DB::getInstance();
		$this->_user = $user;
		$this->_profileUser = $profileUser;
		$this->_language = $language;
		$this->_friendsLanguage = $friendsLanguage;
		$this->_smarty = $smarty;
		$this->_modulePath = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'); 
	}
	
	public function query($user)
	{
		$query = $this->_db->query("SELECT * FROM nl2_friends WHERE (user_id = $user AND accepted = 1) OR (friend_id = $user AND accepted = 1)");
		$data = $query->results();

		return ($data ? $data : []);
	}

	public function queryFriend($user1, $user2, $reversible = 1)
	{
		$query = $this->_db->query("SELECT * FROM nl2_friends WHERE user_id = $user1 AND friend_id = $user2");
		$data = $query->results();

		if (empty($data) && $reversible) {
			$query = $this->_db->query("SELECT * FROM nl2_friends WHERE user_id = $user2 AND friend_id = $user1");
			$data = $query->results();
		}

		return (!empty($data) ? $data[0] : null);
	}

	private function addFriend($user1, $user2)
	{
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

		return true;
	}

	private function removeFriend($user1, $user2)
	{
		$data = $this->queryFriend($user1, $user2);
		if (!$data) {
			return;
		}
		
		try {
			$this->_db->query("DELETE FROM nl2_friends WHERE id = $data->id");
		} catch (Exception $e) {
			// ...
		}

		return true;
	}

	private function acceptRequest($user1, $user2)
	{
		$data = $this->queryFriend($user1, $user2);
		if ((!$data) || ($data->accepted)) {
			return false;
		}

		try {
			$this->_db->query("UPDATE nl2_friends SET accepted = 1 WHERE id = $data->id");
		} catch (Exception $e) {
			// ...
		}

		Alert::create(
			$user1, 'friend_request',
			['path' => $this->_modulePath . '/language', 'file' => 'general', 'term' => 'accepted_friend_request', 'replace' => ['{x}'], 'replace_with' => [Output::getClean($this->_user->idToName($user2))]],
			['path' => $this->_modulePath . '/language', 'file' => 'general', 'term' => 'accepted_friend_request', 'replace' => ['{x}'], 'replace_with' => [Output::getClean($this->_user->idToName($user2))]],
			URL::build('/profile/' . $this->_user->idToName($user2))
		);

		return true;
	}
	
	private function cancelRequest($user1, $user2)
	{
		$data = $this->queryFriend($user1, $user2);
		if ((!$data) || ($data->accepted)) {
			return;
		}

		try {
			$this->_db->query("DELETE FROM nl2_friends WHERE id = $data->id");
		} catch (Exception $e) {
			// ...
		}

		return true;
	}

	private function isFriends($user1, $user2)
	{
		$data = $this->queryFriend($user1, $user2);
		return ((($data) && ($data->accepted)) ? true : false);
	}
	
	private function isRequested($user1, $user2)
	{
		$data = $this->queryFriend($user1, $user2, false);
		return ((($data) && (!$data->accepted)) ? true : false);
	}

	public function processPost()
	{
		if ((!$this->_user->isLoggedIn()) || (!$this->_profileUser) || ($this->_user->data()->id == $this->_profileUser->id)) {
			return false;
		}

		if (!Input::exists()) {
			return false;
		}

		if (!Token::check(Input::get('token'))) {
			$this->_smarty->assign('ERROR', $this->_language->get('general', 'invalid_token'));
			return false;
		}

		if (!Input::get('action')) {
			return false;
		}

		switch (Input::get('action')) {
		
			case 'add_friend':
				if ($this->addFriend($this->_user->data()->id, $this->_profileUser->id)) {
					$this->_smarty->assign('SUCCESS', str_replace('{x}', $this->_profileUser->username, $this->_friendsLanguage->get('general', 'friend_request_sent')));
				}
				break;

			case 'remove_friend':
				if ($this->removeFriend($this->_user->data()->id, $this->_profileUser->id)) {
					$this->_smarty->assign('SUCCESS', str_replace('{x}', $this->_profileUser->username, $this->_friendsLanguage->get('general', 'friend_removed')));
				}
				break;
				
			case 'accept_request':
				if ($this->acceptRequest($this->_profileUser->id, $this->_user->data()->id)) {
					$this->_smarty->assign('SUCCESS', str_replace('{x}', $this->_profileUser->username, $this->_friendsLanguage->get('general', 'friend_request_accepted')));
				}
				break;
				
			case 'cancel_request':
				if ($this->cancelRequest($this->_user->data()->id, $this->_profileUser->id)) {
					$this->_smarty->assign('SUCCESS', str_replace('{x}', $this->_profileUser->username, $this->_friendsLanguage->get('general', 'friend_request_canceled')));
				}
				break;

		}
	}

	public function generateButton()
	{
		if ((!$this->_user->isLoggedIn()) || (!$this->_profileUser) || ($this->_user->data()->id == $this->_profileUser->id)) {
			return null;
		}
		
		if ($this->isFriends($this->_user->data()->id, $this->_profileUser->id)) {

			$friend_button = [
				'text' => $this->_friendsLanguage->get('general', 'remove_friend'),
				'action' => 'remove_friend',
			];

		} else if ($this->isRequested($this->_user->data()->id, $this->_profileUser->id)) {

			$friend_button = [
				'text' => $this->_friendsLanguage->get('general', 'cancel_friend_request'),
				'action' => 'cancel_request',
			];

		} else if ($this->isRequested($this->_profileUser->id, $this->_user->data()->id)) {

			$friend_button = [
				'text' => $this->_friendsLanguage->get('general', 'accept_friend_request'),
				'action' => 'accept_request',
			];

		} else {

			$friend_button = [
				'text' => $this->_friendsLanguage->get('general', 'add_friend'),
				'action' => 'add_friend',
			];

		}
	
		return $friend_button;
	}
}