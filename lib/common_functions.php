<?php
/**
 * Create table
 *
 * @since    1.0.0
 */
function createTable() {
    global $wpdb;
    $table_name = $wpdb->prefix .BBIL_TABLE;

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
      url varchar(256) DEFAULT '' NOT NULL,
      short_link varchar(256) DEFAULT '' NOT NULL,
      is_shorted varchar(8) DEFAULT 'false' NOT NULL,
      `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
/**
 * Delete table
 *
 * @since    1.0.0
 */
function deleteTable() {
    global $wpdb;
    $tableName = $wpdb->prefix .BBIL_TABLE;
    $wpdb->query( "DROP TABLE IF EXISTS $tableName" );
    delete_option("my_plugin_db_version");
}

/**
 * Save data to option table
 *
 * Save or update data into the option table
 *
 * @since    1.0.0
 *
 * @param $name
 * @param $value
 */
function saveOnOptionTable($name, $value) {
    $isExists = trim(get_option($name));
    if( $isExists ) update_option( $name, $value,'no' );
    else add_option( $name, $value, '', 'no' );
}

/**
 * Sanitize the posted data for future use
 *
 * @since    1.0.0
 *
 * @param $value
 * @param bool $default
 *
 * @return bool|string
 */
function getPostField($value, $default=false) {
    return isset($value) && trim($value) ? trim($value) : $default;
}

/**
 * redirectUri
 *
 * Generate the redirect url used by the google API
 *
 * @since    1.0.0
 *
 * @return  string
 */
function redirectUri(){
    $myUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] && !in_array(strtolower($_SERVER['HTTPS']),array('off','no'))) ? 'https' : 'http';
    $myUrl .= '://'.$_SERVER['HTTP_HOST'];
    $myUrl .= $_SERVER['REQUEST_URI'];
    return $myUrl;
}

/**
 * pluginLibUrl
 *
 * The plugin `lib` directory url
 *
 * @since    1.0.0
 *
 * @param string $fileUrl
 *
 * @return string
 */
function pluginLibUrl($fileUrl='analytics/connect.php'){
    return str_replace('admin/partials/', '', plugins_url('/', __FILE__ )).$fileUrl;
}

/**
 * setDefatltPreferences
 *
 * Set some default preferences and settings data into options table
 *
 * @since    1.0.0
 */
function setDefatltPreferences(){
    // Set Default options
    // add_option('bbil_clientId', '878335149629-3k5c3s5e7fnc2ke9rsaohj1rf87o7vv4.apps.googleusercontent.com');
    // add_option('bbil_clientISecret', 'N34aS00Z8N-P2aA1szVemiNc');
    // add_option('bbil_settingsSaved', 1);
    add_option('bbil_urlShorterApiKey', 'AIzaSyC0n96SEN7yoSQMSog2V0VjNt8GD8oQ698');
    add_option('bbil_analyticsAccessTokenInvalidTime', 0);
}

/**
 * resetThemeOptions
 *
 * Remove default preferences and settings data into options table
 *
 * @since    1.0.0
 */
function resetThemeOptions() {
    delete_option('bbil_clientId');
    delete_option('bbil_clientISecret');
    delete_option('bbil_urlShorterApiKey');
    delete_option('bbil_settingsSaved');

    delete_option('bbil_analyticsRefreshToken');
    delete_option('bbil_analyticsAccessToken');
    delete_option('bbil_analyticsPropertyId');
    delete_option('bbil_analyticsViewId');
    delete_option('bbil_analyticsAccessTokenInvalidTime');
    delete_option('bbil_analyticsCredentialsSaved');

	delete_option('bbil_analyticsEmail');
	delete_option('bbil_analyticsPropertyName');
	delete_option('bbil_analyticsViewName');
	delete_option('bbil_analyticsConnected');
}

/**
 * get the table name created by the plugin
 *
 * @return string
 */
function getTableName() {
    global $wpdb;
    return $wpdb->prefix .BBIL_TABLE;
}

/**
 * get new access token
 *
 * @return string
 */
function getNewAccessToken() {
	$postdata = http_build_query(
		array(
			'client_id' => get_option('bbil_clientId'),
			'client_secret' => get_option('bbil_clientISecret'),
			'refresh_token' => get_option('bbil_analyticsRefreshToken'),
			'grant_type' => 'refresh_token'
		)
	);

	$opts = array(
		'http' => array(
			'method' => 'POST',
			'header' => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata
		),
		'ssl' => array(
			'verify_peer' => false,
		),
	);

	$context = stream_context_create($opts);

	$result = @file_get_contents('https://www.googleapis.com/oauth2/v4/token', false, $context);
	$tokens = json_decode($result,true);
	$tokens['resurve_time'] = 5; // 5 seconds
	$tokens['current_timestamp'] = time();
	$tokens['invalid_timestamp'] = time() + $tokens['expires_in'] - $tokens['resurve_time'];

	update_option( 'bbil_analyticsAccessToken' , $tokens['access_token'], '', 'no' );
	saveOnOptionTable('bbil_analyticsAccessTokenInvalidTime', $tokens['invalid_timestamp']);

	return get_option('bbil_analyticsAccessToken');
}

function pluginPageUrl($page) {
	return esc_url(admin_url('admin.php?page='. $page));
}

function getShortUrl($url){
	$apiKey = "AIzaSyC0n96SEN7yoSQMSog2V0VjNt8GD8oQ698";
	if ($url) {
		$context = ['http' => ['method' => 'post', 'header'=>'Content-Type:application/json', 'content' => json_encode(['longUrl' => $url]) ] ];

		$context = stream_context_create($context);
		$result = file_get_contents('https://www.googleapis.com/urlshortener/v1/url?key='.$apiKey, false, $context);
		$json = json_decode($result,true);

		if ($json['id']) return $json['id'];
		else return false;
	} else return false;
}