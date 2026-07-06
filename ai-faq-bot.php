<?php
/**
 * Plugin Name:  AI FAQ Bot
 * Version:      1.0.0
 * Description:  Wtyczka WordPress - AI asystent FAQ oparty na Claude API
 * Author:       Pawel Szymczyk
 * License:      GPL v2 or later
 */

if(!defined('ABSPATH'))exit;
define('AI_FAQ_BOT_VERSION','1.0.0');
define('AI_FAQ_BOT_DIR',plugin_dir_path(__FILE__));
define('AI_FAQ_BOT_URL',plugin_dir_url(__FILE__));


require_once AI_FAQ_BOT_DIR.'admin/settings-page.php';

add_action('plugins_loaded',function(){
    new AI_FAQ_Admin_Settings();
});