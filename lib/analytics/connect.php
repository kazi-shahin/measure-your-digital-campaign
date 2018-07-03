<?php
session_start();
// Load the Google API PHP Client Library.
require_once '../../../../../wp-load.php';
require_once __DIR__ . '/vendor/autoload.php';
$redirectUrl = esc_url(admin_url('admin.php?page=bbil_gcb'));

$_SESSION['redirectUrl'] = $redirectUrl;
$_SESSION['intializeUrl'] = pluginLibUrl();

$client = initializeAnalytics();
if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $token = $client->getAccessToken();
    $client->setAccessToken(json_encode($token));
    $_SESSION['access_token'] = $token['access_token'];
    $_SESSION['refresh_token'] = $token['refresh_token'];

	try {
		$plus = new \Google_Service_Plus($client);
		$google_user = $plus->people->get('me');
		$_SESSION['email'] = $google_user['emails'][0]['value'];
	} catch (Exception $exception) {
		echo '<p> There is an error occured. Please check you API credentials and enabled services</p>';
		//echo '<pre>'. print_r($exception, true) . '</pre>';
		wp_die();
	}

	header('Location: '. $_SESSION['redirectUrl']);
} else {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
}

/**
 * Initializes an Analytics Reporting API V4 service object.
 *
 * @return An authorized Analytics Reporting API V4 service object.
 */
function initializeAnalytics() {
    // Create and configure a new client object.
    $client = new Google_Client();
    $client->setApplicationName("Amazon Analytics Reporting");
    $client->setClientId(get_option('bbil_clientId'));
    $client->setClientSecret(get_option('bbil_clientISecret'));
    $client->setRedirectUri($_SESSION['intializeUrl']);

	$client->addScope('https://www.googleapis.com/auth/analytics.readonly');
	$client->addScope("https://www.googleapis.com/auth/userinfo.email");
    $client->setAccessType('offline');
    $client->setApprovalPrompt('force');
    return $client;
}
