<?php

/**
 *    FRIENDS MODULE
 *    By Xemah | https://xemah.com
 *
**/

class FriendsModule extends Module {
    public function __construct() {
        $module = [
            'name' => 'Friends',
            'author' => '<a href="https://xemah.com" target="_blank">Xemah</a>',
            'version' => '3.0',
            'namelessVersion' => '2.0.0-pr13'
        ];

        parent::__construct($this, $module['name'], $module['author'], $module['version'], $module['namelessVersion']);

    }

    public function onInstall() {
        try {
            DB::getInstance()->createTable("friends", " `id` int(11) NOT NULL AUTO_INCREMENT, `user_id` int(11) NOT NULL, `friend_id` int(11) NOT NULL, `notify` tinyint(1) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)");
        } catch(Exception $e) {
            // ...
        }
    }

    public function onUninstall() {}

    public function onEnable() {
        try {
            DB::getInstance()->addColumn("friends", "accepted", "tinyint(1) NOT NULL DEFAULT 0");
        } catch(Exception $e) {
            // ...
        }
    }

    public function onDisable() {}

    public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template) {}

    public function getDebugInfo(): array {
        return [];
    }
}
