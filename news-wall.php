<?php
/*
 * Plugin Name:		News Wall
 * Description:		Notify your users of the latest site news!
 * Version:		1.1.0
 * Requires at least:	5.2
 * Requires PHP:	7.2
 * Author:	tsina
 * Author URI:		https://profiles.wordpress.org/tsina/
 * License:		GPL v2 or later
 * License URI:		https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:		news-wall
 * Domain Path:		/languages
*/
// Translate
function nwwp_language_init() {
    load_plugin_textdomain( 'news-wall', false, 'news-wall/languages');
}
add_action('init', 'nwwp_language_init');
__('Notify your users of the latest site news!', 'news-wall');
// Plugin Constants
const NW_NEWS_TABLE_NAME = "newswall";
const NW_OPTIONS_TABLE_NAME = "newswalloptions";
// Functions
include_once(plugin_dir_path(__FILE__) . '/funcs.php');
// Start Coding
if (!defined('ABSPATH')) {
    die('Error!');
}

function nwwp_activation(){
    global $wpdb;
    $table_name1 = $wpdb->prefix . NW_NEWS_TABLE_NAME;
    $sql1 =
        "CREATE TABLE `$table_name1` 
		( `id` INT NULL AUTO_INCREMENT ,
		`title` VARCHAR(50) NULL ,
		`body` TEXT NULL ,
		`DATE` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ,
		`private` INT NULL ,
		PRIMARY KEY (`id`))";
    include_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql1);
    $table_name2 = $wpdb->prefix . NW_OPTIONS_TABLE_NAME;
    $sql2 =
        "CREATE TABLE `$table_name2`(
        `id` INT NOT NULL AUTO_INCREMENT,
        `option` VARCHAR(50) NOT NULL,
        `value` INT(1) NOT NULL,
        `strvalue` VARCHAR(64),
        PRIMARY KEY  (id)
    )";
    dbDelta($sql2);
    $wpdb->insert($table_name2, ['option' => 'nwbtnstyles', 'value' => '1']);
    $wpdb->insert($table_name2, ['option' => 'nwbtnnewsnum', 'value' => 3]);
    $wpdb->insert($table_name2, ['option' => 'nwbtntitle', 'value' => 0, 'strvalue' => __('latest news' , 'news-wall')]);
    $wpdb->insert($table_name2, ['option' => 'nwsectitle', 'value' => 0, 'strvalue' => __('latest news', 'news-wall')]);
    $wpdb->insert($table_name2, ['option' => 'nonewstitle', 'value' => 0, 'strvalue' => __('There is no news', 'news-wall')]);
};
register_activation_hook( __FILE__ , 'nwwp_activation');
function nwwp_uninstall(){
    global $wpdb;
    $table_name = $wpdb->prefix . NW_NEWS_TABLE_NAME;

    $wpdb->query("DROP TABLE `$table_name`");
    $table_name = $wpdb->prefix . NW_OPTIONS_TABLE_NAME;

    $wpdb->query("DROP TABLE `$table_name`");
}
register_uninstall_hook(__FILE__ , 'nwwp_uninstall');
// plugin menu and panel
include_once(plugin_dir_path(__FILE__) . '/plugin-panel.php');
// Shortcode
include_once(plugin_dir_path(__FILE__) . '/shortcode.php');