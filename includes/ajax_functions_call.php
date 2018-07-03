<?php



class ajax_functions {



	/**

	 * ajax_functions constructor.

	 */

	function __construct() {



        // analytics disconnect

        add_action('wp_ajax_bbil_analyticsDisconnect', array( $this, 'bbil_analyticsDisconnect'));

        add_action('wp_ajax_nopriv_bbil_analyticsDisconnect', array( $this, 'bbil_analyticsDisconnect'));



        // save analytics data

        add_action('wp_ajax_bbil_saveAnalyticsData', array( $this, 'bbil_saveAnalyticsData'));

        add_action('wp_ajax_nopriv_bbil_saveAnalyticsData', array( $this, 'bbil_saveAnalyticsData'));



        // Short the url

        add_action('wp_ajax_bbil_shortUrl', array( $this, 'bbil_shortUrl'));

        add_action('wp_ajax_nopriv_bbil_shortUrl', array( $this, 'bbil_shortUrl'));



        // Save short link into DB

        add_action('wp_ajax_bbil_saveShortLink', array( $this, 'bbil_saveShortLink'));

        add_action('wp_ajax_nopriv_bbil_saveShortLink', array( $this, 'bbil_saveShortLink'));



        // Save setup data

        add_action('wp_ajax_bbil_saveSetupData', array( $this, 'bbil_saveSetupData'));

        add_action('wp_ajax_nopriv_bbil_saveSetupData', array( $this, 'bbil_saveSetupData'));



		// Analytics date change

		add_action('wp_ajax_bbil_analyticsDateChange', array( $this, 'bbil_analyticsDateChange'));

		add_action('wp_ajax_nopriv_bbil_analyticsDateChange', array( $this, 'bbil_analyticsDateChange'));



		// Delete campaign link

		add_action('wp_ajax_bbil_deleteCampaignLink', array( $this, 'bbil_deleteCampaignLink'));

		add_action('wp_ajax_nopriv_bbil_deleteCampaignLink', array( $this, 'bbil_deleteCampaignLink'));

	}



	/**

	 * Disconnect from analytics API

	 *

	 * Remove API related data from DB and unset SESSION data

	 */

	function bbil_analyticsDisconnect() {



        if (!session_id()) @session_start();

        try {

            unset($_SESSION['access_token']);

            unset($_SESSION['refresh_token']);



	        delete_option('bbil_analyticsAccessTokenInvalidTime');

	        delete_option('bbil_analyticsCredentialsSaved');

	        delete_option('bbil_analyticsRefreshToken');

	        delete_option('bbil_analyticsAccessToken');

	        delete_option('bbil_analyticsPropertyId');

	        delete_option('bbil_analyticsViewId');



            delete_option('bbil_analyticsEmail');

            delete_option('bbil_analyticsPropertyName');

            delete_option('bbil_analyticsViewName');

            delete_option('bbil_analyticsConnected');

            echo "200";

        } catch (Exception $exception) {

            echo $exception;

        }

        wp_die();

    }



	/**

	 * Save analytics data into DB

	 */

	function bbil_saveAnalyticsData() {

        if (!session_id()) @session_start();

        try {



        	// save into DB

            saveOnOptionTable('bbil_analyticsRefreshToken', $_SESSION['refresh_token']);

            saveOnOptionTable('bbil_analyticsAccessToken', $_SESSION['access_token']);

	        saveOnOptionTable('bbil_analyticsEmail', $_SESSION['email']);



            saveOnOptionTable('bbil_analyticsPropertyId', $_POST['bbilGaProperty']);

            saveOnOptionTable('bbil_analyticsViewId', $_POST['bbilGaPropertyView']);

	        saveOnOptionTable('bbil_analyticsPropertyName', $_POST['bbilGaPropertyName']);

	        saveOnOptionTable('bbil_analyticsViewName', $_POST['bbilGaPropertyViewName']);

            saveOnOptionTable('bbil_analyticsAccessTokenInvalidTime', time() + 3600 - 5);

            saveOnOptionTable('bbil_analyticsCredentialsSaved', 1);

            saveOnOptionTable('bbil_analyticsConnected', 1);



            // set api variables into SESSION

            unset($_SESSION['refresh_token']);

            unset($_SESSION['access_token']);

            unset($_SESSION['analytics_property_id']);

            unset($_SESSION['analytics_view_id']);

            unset($_SESSION['analyticsCredentialsSaved']);



            unset($_SESSION['email']);



            // set urls into SESSION

            unset($_SESSION['redirectUrl']);

            unset($_SESSION['intializeUrl']);



            // return data

	        //echo json_encode($_SESSION);

            echo "200";

        } catch (Exception $exception) {



			// Error

            echo $exception;

        }



        wp_die();

    }



	/**

	 * Short the long url into short using google API

	 *

	 * @param @url long link

	 *

	 * @return array status & message

	 */

    function bbil_shortUrl() {



        $url = isset($_POST['url']) && !empty($_POST['url']) ? $_POST['url'] : false;

        $data = [];



        if ($url) {

	        $shortUrl = getShortUrl($url);

	        if ($shortUrl) {

		        // Success

		        $data['status'] = 200;

		        $data['url'] = $shortUrl;

	        }

	        else {

		        // Error

		        $data['status'] = 203;

		        //$data['message'] = 'Google response  Error';

		        $data['message'] = 'Please change url and submit again.';

	        }

        } else {

        	// Error

        	$data['status'] = 201;

        	$data['message'] = 'URL is empty.';

        }



        echo json_encode($data);

        wp_die();

    }



	/**
	 * Save the short link into the DB
	 *
	 * @param @url short link
	 *
	 * @return array status & message
	 */
	function bbil_saveShortLink() {

        $id = isset($_POST['id']) && !empty($_POST['id']) ? (int) $_POST['id'] : 0;
        $url = isset($_POST['url']) && !empty($_POST['url']) ? $_POST['url'] : false;
		$shortLink = isset($_POST['shortLink']) && !empty($_POST['shortLink']) ? $_POST['shortLink'] : false;
		$isShorted = isset($_POST['isShorted']) && !empty($_POST['isShorted']) ? $_POST['isShorted'] : 'false';
        $data = [];

        if ($url) {
        	// Short the link first
        	if ($isShorted == 'false') $shortLink = getShortUrl($url); 

	        if ($shortLink) {
		        global $wpdb;
		        $tableName = $wpdb->prefix .BBIL_TABLE;
		        $linkData = array('url' => $url, 'short_link' => $shortLink, 'is_shorted' => $isShorted );
		        if ($id > 0) {
		        	// update the link
		        	$qry_result = $wpdb->update($tableName, $linkData, array('id'=>$id));
		        } else {
		        	// create new link
		        	$qry_result = $wpdb->insert( $tableName, $linkData, array('%s', '%s', '%s') );
		        }
		        if ($qry_result) {
			        // success
			        $data['status'] = 200;
			        $data['message'] = 'Inserted successfully.';
		        } else {

			        // error
			        $data['status'] = 202;
			        $data['message'] = 'MySQL query failed.';
		        }

	        } else {

		        // error
		        $data['status'] = 203;
		        $data['message'] = 'Automated shortlink failed.';
	        }

        } else {

			// Empty url given
            $data['status'] = 201;
            $data['message'] = 'URL is empty.';
        }



        //echo json_encode(array('url' => $url, 'short_link' => $shortLink, 'is_shorted' => $isShorted ));
        echo json_encode($data);
        wp_die();
    }



	/**

	 * Save the setup data into DB

	 *

	 * @param $clientId Google API client ID

	 * @param $clientISecret Google API client secret

	 * @param $urlShorterApiKey Google url shortner API key

	 *

	 * @return array status & message

	 */

	function bbil_saveSetupData() {

        $clientId = isset($_POST['clientId']) && !empty($_POST['clientId']) ? $_POST['clientId'] : false;

        $clientISecret = isset($_POST['clientISecret']) && !empty($_POST['clientISecret']) ? $_POST['clientISecret'] : false;

        // $urlShorterApiKey = isset($_POST['urlShorterApiKey']) && !empty($_POST['urlShorterApiKey']) ? $_POST['urlShorterApiKey'] : false;

        if ($clientId && $clientISecret) {

            try {

                // save into DB

                saveOnOptionTable('bbil_clientId', getPostField($_POST['clientId'], ''));

                saveOnOptionTable('bbil_clientISecret', getPostField($_POST['clientISecret'], ''));

                // saveOnOptionTable('bbil_urlShorterApiKey', getPostField($_POST['urlShorterApiKey'], ''));

                saveOnOptionTable('bbil_settingsSaved', 1);



                // success response

                $response['status'] = 200;

                $response['message'] = 'Successfully saved';



            } catch (Exception $e) {

                // Error response

                $response['status'] = 201;

                $response['message'] = 'Failed';

            }

        } else {

            $response['status'] = 401;

            $response['message'] = 'All fields are required.';

        }



        echo json_encode($response);

        wp_die();

    }



	/**

	 * Change analytics date to show date ranged data

	 *

	 * @param $date Google API start date

	 *

	 * @return array status & message

	 */

	function bbil_analyticsDateChange() {

		if (!session_id()) @session_start();

		$date = isset($_POST['date']) && !empty($_POST['date']) ? $_POST['date'] : '';

		if ($date) {

			$_SESSION['analyticsStartDate'] = $date;

			$response['status'] = 200;

			$response['message'] = 'Successfully added in `SESSION` variable';

		} else {

			$response['status'] = 401;

			$response['message'] = 'Could not be saved. Please try again.';

		}



		echo json_encode($response);

		wp_die();

	}



	/**

	 * Delete selected campaign link form DB

	 *

	 * @param rowID

	 *

	 * @return array status & message

	 */

	function bbil_deleteCampaignLink() {

		global $wpdb;

		$table_name = $wpdb->prefix .BBIL_TABLE;

		$rowID = isset($_POST['rowID']) && !empty($_POST['rowID']) ? $_POST['rowID'] : 0;

		if ($rowID) {

			if ($wpdb->delete( $table_name, array( 'id' => $rowID ) )) {

				$response['status'] = 200;

				$response['message'] = 'Successfully deleted';

			} else {

				$response['status'] = 201;

				$response['message'] = 'Could not be deleted';

			}

		} else {

			$response['status'] = 401;

			$response['message'] = 'Could not be deleted. Please try again.';

		}

		echo json_encode($response);

		wp_die();

	}

}

$var = new ajax_functions();