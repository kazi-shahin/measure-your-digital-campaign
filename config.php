<?php/** * The config file of the plugin * * This file contain most of the static resorces ie. all global variables, autoloaded values etc * * - Values shoud be added using define() method * - Check if request is actually coming from the site * - Run an admin referrer check to make sure it goes through authentications * * @link       blubirdinteractive.com * @since      1.0.0 * * @package    Bbilgcb */if ( ! defined( 'WPINC' ) ) { die; }define( 'BBIL_PLUGINDIR', basename(plugin_dir_path( __FILE__ )) );define('BBIL_SETTINGS_SAVED', get_option('bbil_settingsSaved'));define('BBIL_TABLE', 'shorturls');define('BBIL_ROOTPATH', plugin_dir_path( __FILE__ ));