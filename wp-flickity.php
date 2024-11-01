<?php
/**
 *

 * Plugin Name: WP Flickity
 * Plugin URI: http://blog.paolofalomo.it/wp-flickity/
 * Description: Wordpress Flickity Plugin
 * Version: 0.5.1
 * Author: PaoloFalomo
 * Author URI: http://www.paolofalomo.it/
 * Requires at least: 3.5.1
 * Tested up to: 4.7.4
 * Stable tag: 4.7.4
 * 
 * Text Domain: wp-flickity
 * Domain Path: /languages/ (CURRENTLY UNSUPPORTED)
 *
 * See more CHANGELOG at GITLAB https://gitlab.com/paolofalomo/wp-flickity
 * You can pull requests there!
 */


/*=========================== MAIN DEFINITIONS ===========================*/
/**
 * @since 0.1
 * @since 0.5 added WP_FLICKITY_PLUGIN_PATH, WP_FLICKITY_PLUGIN_URL, WP_FLICKITY_VERSION
 */
define('WP_FLICKITY_NAME','WP Flickity');
define('WP_FLICKITY_DOMAIN','wp-flickity');
define('WP_FLICKITY_MENUPOSITION',1);
define('WP_FLICKITY_UNIQUE_IDENTIFIER','wpflkty_');
define('WP_FLICKITY_PLUGIN_FILE', __FILE__ );
define('WP_FLICKITY_PLUGIN_PATH',dirname( WP_FLICKITY_PLUGIN_FILE ) . DIRECTORY_SEPARATOR );
define('WP_FLICKITY_PLUGIN_URL',plugins_url('', WP_FLICKITY_PLUGIN_FILE ) . '/' );
define('WP_FLICKITY_DEFAULT_QUERY_POST','posts_per_page=5');
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
$wp_flickity_pluign_data = get_plugin_data(WP_FLICKITY_PLUGIN_FILE);
define('WP_FLICKITY_VERSION',$wp_flickity_pluign_data['Version']);
//init & config
global $wpdb;
$flickity_db_charset_collate = $wpdb->get_charset_collate();
$wp_flickity_db_version = '2';
$installed_ver = get_option( "wp_flickity_db_version" );
$wp_flickity_table_name = $wpdb->prefix . 'wp_flickity';

$wp_flickity_db_structure = "CREATE TABLE $wp_flickity_table_name (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	created_on datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	name tinytext NOT NULL,
	status varchar(20) DEFAULT 'publish' NOT NULL,
	flickity_metadata MEDIUMBLOB DEFAULT '' NOT NULL,
	UNIQUE KEY id (id)
) $flickity_db_charset_collate;";

require_once WP_FLICKITY_PLUGIN_PATH . 'wp-flickity-functions.php';

wp_flickity_delta_db();//check if is necessary to upgrade the db and makes the delta changes

/**
 * Define the admin User Interface
 * @since 0.5 
 */
require_once WP_FLICKITY_PLUGIN_PATH . 'wp-flickity-admin-ui.php';

require_once WP_FLICKITY_PLUGIN_PATH . 'wp-flickity-frontend.php';



/*
                                                                                                                          
		                                                                                                                      dddddddd
		TTTTTTTTTTTTTTTTTTTTTTThhhhhhh                                                                                        d::::::d
		T:::::::::::::::::::::Th:::::h                                                                                        d::::::d
		T:::::::::::::::::::::Th:::::h                                                                                        d::::::d
		T:::::TT:::::::TT:::::Th:::::h                                                                                        d:::::d 
		TTTTTT  T:::::T  TTTTTT h::::h hhhhh           eeeeeeeeeeee             eeeeeeeeeeee    nnnn  nnnnnnnn        ddddddddd:::::d 
		        T:::::T         h::::hh:::::hhh      ee::::::::::::ee         ee::::::::::::ee  n:::nn::::::::nn    dd::::::::::::::d 
		        T:::::T         h::::::::::::::hh   e::::::eeeee:::::ee      e::::::eeeee:::::een::::::::::::::nn  d::::::::::::::::d 
		        T:::::T         h:::::::hhh::::::h e::::::e     e:::::e     e::::::e     e:::::enn:::::::::::::::nd:::::::ddddd:::::d 
		        T:::::T         h::::::h   h::::::he:::::::eeeee::::::e     e:::::::eeeee::::::e  n:::::nnnn:::::nd::::::d    d:::::d 
		        T:::::T         h:::::h     h:::::he:::::::::::::::::e      e:::::::::::::::::e   n::::n    n::::nd:::::d     d:::::d 
		        T:::::T         h:::::h     h:::::he::::::eeeeeeeeeee       e::::::eeeeeeeeeee    n::::n    n::::nd:::::d     d:::::d 
		        T:::::T         h:::::h     h:::::he:::::::e                e:::::::e             n::::n    n::::nd:::::d     d:::::d 
		      TT:::::::TT       h:::::h     h:::::he::::::::e               e::::::::e            n::::n    n::::nd::::::ddddd::::::dd
		      T:::::::::T       h:::::h     h:::::h e::::::::eeeeeeee        e::::::::eeeeeeee    n::::n    n::::n d:::::::::::::::::d
		      T:::::::::T       h:::::h     h:::::h  ee:::::::::::::e         ee:::::::::::::e    n::::n    n::::n  d:::::::::ddd::::d
		      TTTTTTTTTTT       hhhhhhh     hhhhhhh    eeeeeeeeeeeeee           eeeeeeeeeeeeee    nnnnnn    nnnnnn   ddddddddd   ddddd
                                                                                                                              
                Thanks to metafizzy
                http://metafizzy.co

                Original Script framework made by metafizzy
                Visit http://flickity.metafizzy.co for more informations                                                                                                                                                               
*/
//