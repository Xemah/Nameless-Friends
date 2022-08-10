<?php

/**
 *	FRIENDS MODULE
 *	By Xemah | https://xemah.com
 *
**/

class FriendsModule extends Module
{
	private static Language $_language;

	public function __construct()
	{
		$module = [
			'name' => 'Friends',
			'author' => '<a href="https://xemah.com" target="_blank">Xemah</a>',
			'version' => '2.6',
			'namelessVersion' => '2.0.1'
		];

		parent::__construct($this, $module['name'], $module['author'], $module['version'], $module['namelessVersion']);
	}

	public function onInstall()
	{
		try {
			DB::getInstance()->createTable('friends', '
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`user_id` int(11) NOT NULL,
				`friend_id` int(11) NOT NULL,
				`accepted` tinyint(1) NOT NULL DEFAULT 0,
				PRIMARY KEY (`id`)
			');
		} catch(Exception $e) {
			// ...
		}
	}

	public function onUninstall()
	{
		// ...
	}

	public function onEnable()
	{
		try {
			DB::getInstance()->addColumn('friends', 'accepted', 'tinyint(1) NOT NULL DEFAULT 0');
		} catch(Exception $e) {
			// ...
		}
	}

	public function onDisable()
	{
		// ...
	}

	public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template)
	{
		// ...
	}

	public static function getLanguage(string $file = null, string $term = null, array $variables = [])
	{
		if (!isset(self::$_language)) {
			self::$_language = new Language(__DIR__ . '/language');
		}

		if (!$file && !$term) {
			$language = file_get_contents(__DIR__ . '/language/' . self::$_language->getActiveLanguage() . '.json');
			if (!$language) $language = file_get_contents(__DIR__ . '/language/en_UK.json');
			$language = json_decode($language, true);

			$languageArr = [];
			foreach ($language as $key => $value) {
				$term = explode('/', $key)[1];
				$languageArr[$term] = $value;
			}

			return $languageArr;
		}
		
		return self::$_language->get($file, $term, $variables);
	}

	public function getDebugInfo(): array
	{
		return [];
	}
}
