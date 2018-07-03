<?php

/**
 * Fired during plugin deactivation
 *
 * @link       blubirdinteractive.com
 * @since      1.0.0
 *
 * @package    Bbilgcb
 * @subpackage Bbilgcb/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Bbilgcb
 * @subpackage Bbilgcb/includes
 * @author     BBIL <pubsajib@gmail.com>
 */
class Bbilgcb_Deactivator {

	/**
	 * On deactivation actions
	 *
	 * remove the tabale created by this plugin
	 * remove the additional values added in options table
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		resetThemeOptions();
        deleteTable();
	}

}
