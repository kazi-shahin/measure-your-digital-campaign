<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       blubirdinteractive.com
 * @since      1.0.0
 *
 * @package    Bbilgcb
 * @subpackage Bbilgcb/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Bbilgcb
 * @subpackage Bbilgcb/admin
 * @author     BBIL <pubsajib@gmail.com>
 */
class Bbilgcb_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided adding required css files.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bbilgcb_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bbilgcb_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name.'-bootstrap', plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bbilgcb-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/HoldOn.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided adding required js files.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Bbilgcb_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Bbilgcb_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name.'-bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bbilgcb-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/HoldOn.min.js', array( 'jquery' ), $this->version, false );
	}

	/**
	 * Add menu items in admin area
	 *
	 * @since    1.0.0
	 */
	public function add_menus() {
		if (get_option('bbil_settingsSaved')) {
		//if (false) {
			// setup wizard complete
			add_menu_page('Google Campaign', 'Google Campaign', 'manage_options', 'bbil_gcb', function (){ $this->bbil_connection(); } ,'',10);
			add_submenu_page('bbil_gcb', 'Campaigns Data', 'Campaigns Data', 'manage_options', 'bbil_gcb' );
			add_submenu_page('bbil_gcb', 'Campaign Creator', 'Campaign Creator', 'manage_options', 'bbil-campaign_creator', function (){ $this->bbil_campaignCreator(); } );
			add_submenu_page('bbil_gcb', 'Campaign URLs', 'Campaign URLs', 'manage_options', 'bbil-short_urls', function (){ $this->bbil_short_urls(); } );
			add_submenu_page('bbil_gcb', 'Connection Info', 'Connection Info', 'manage_options', 'bbil-info', function (){ $this->bbil_connectionInformations(); } );
			// if  (get_option('bbil_analyticsConnected')) {}
		} else {
			add_menu_page('Google Campaign', 'Google Campaign', 'manage_options', 'bbil_gcb', function (){ $this->bbil_setup(); } ,'',10);
		}
	}

	/**
	 * Add campaign menu items in admin area
	 *
	 * @since    1.0.0
	 */
    public function bbil_connection(){
        include plugin_dir_path( dirname( __FILE__ ) ).'admin/partials/showAnalyticsData.php';
    }

	/**
	 * Add campaign menu items in admin area
	 *
	 * @since    1.0.0
	 */
    public function bbil_campaignCreator(){
        include plugin_dir_path( dirname( __FILE__ ) ).'admin/partials/createCampaignLink.php';
    }

	/**
	 * Add url short menu items in admin area
	 *
	 * @since    1.0.0
	 */
	public function bbil_short_urls(){
        include plugin_dir_path( dirname( __FILE__ ) ).'admin/partials/allCampaignUrls.php';
    }

	/**
	 * Google connection inforamtion
	 *
	 * @since    1.0.0
	 */
	public function bbil_connectionInformations(){
		include plugin_dir_path( dirname( __FILE__ ) ).'admin/partials/connectionInformations.php';
	}

	/**
	 * Setup menu item content
	 *
	 * @since    1.0.0
	 */
	public function bbil_setup(){
		include plugin_dir_path( dirname( __FILE__ ) ).'admin/partials/setup.php';
	}
}