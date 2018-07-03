<?php @session_start();



/**

 * The admin-specific Campaign data display page

 *

 * @link       blubirdinteractive.com

 * @since      1.0.0

 *

 * @package    Bbilgcb

 * @subpackage Bbilgcb/admin

 */

?>

<script> var ajaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>"; </script>

<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">

<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.4.1/css/simple-line-icons.min.css">



<?php

$initializationUrl = pluginLibUrl();

if (!get_option('bbil_analyticsCredentialsSaved')) :

    if ($_SESSION['refresh_token'] && $_SESSION['access_token']) : ?>
        <div class="container m-t-30 m-b-30 p-wrapper">
            <div class="row">
                <div class="col-sm-12">
                    <section class="content">
                        <div class="t-card d-block">
                            <!-- <h2 class="analytics-title">Connection informations</h2> -->
                            <p class="text-center">Please select Google Analytics property you want to view.</p>
                        </div>

                        <div class="p-card d-block">

                            <div class="card-body">

                                <div id="bbilPropertyArea" class="">

                                    <div class="row">

                                        <div class="col-md-12">

                                            <div class="form-group">

                                                <select class="form-control" name="bbilGaProperty" id="bbilGaProperty">

                                                    <option value="">Select Property</option>

                                                </select>

                                            </div>

                                        </div>



                                        <div class="col-md-12">

                                            <div class="form-group">

                                                <select class="form-control" name="bbilGaPropertyView" id="bbilGaPropertyView" class="">

                                                    <option value="">Select Property View</option>

                                                </select>

                                            </div>

                                        </div>

                                </div>

                                <div class="bbilGapiDisconnect" style="display:none">

                                    <div class="row">

                                        <div class="col-md-6">

                                            <button  class="btn btn-md btn-primary" href="#" onclick="bbilSignOut();">Disconnect</button>

                                        </div>

                                        <div class="col-md-6">

                                            <button  class="btn btn-md btn-primary" href="#" onclick="bbilNext();">Next</button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </section>

                </div>

            </div>

        </div>



        <script>

            //Step 1



            (function(w,d,s,g,js,fs){

                g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};

                js=d.createElement(s);fs=d.getElementsByTagName(s)[0];

                js.src='https://apis.google.com/js/platform.js';

                fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};

            }(window,document,'script'));



            // Step3



            gapi.analytics.ready(function() {



                /**

                 * Authorize the user immediately if the user has already granted access.

                 * If no access has been created, render an authorize button inside the

                 * element with the ID "bbilEmbedApiAuthContainer".

                 */



                var clientID = "<?php echo get_option('bbil_clientId'); ?>";



                bbilAutoSignOut();



                // Authentication first time

                <?php if(!isset($_SESSION['refresh_token']) || $_SESSION['refresh_token'] ==''){ ?>

                gapi.analytics.auth.authorize({

                    container: 'bbilEmbedApiAuthContainer',

                    clientid: clientID

                });

                <?php }

                else{ ?>

                gapi.analytics.auth.authorize({

                    container: 'bbilEmbedApiAuthContainer',

                    clientid: clientID,

                    serverAuth: {

                        access_token: '<?php echo $_SESSION['access_token']?>',

                        refresh_token: '<?php echo $_SESSION['refresh_token']?>'

                    }

                });

                <?php } ?>



                var bbilIsAuthorized = gapi.analytics.auth.isAuthorized();

                //console.log(bbilIsAuthorized);



                bbilUpdateSigninStatus(bbilIsAuthorized);

            });



            /*

             * ########################### Connection status checking area ###################################333

             */



            function check_connection_status(){

                jQuery('.bbilGapiDisconnect').show();

                jQuery('.bbilGapiConnect').hide();

                jQuery('#bbilPropertyArea').removeClass('hidden'); // Show property area

                bbilListAccounts();

            }



            function bbilUpdateSigninStatus(isSignedIn) {

                // When signin status changes, this function is called.

                // If the signin status is changed to signedIn, we make an API call.

                jQuery('#bbilEmbedApiAuthContainer').hide();

                jQuery('#bbilViewSelectorOneContainer table').hide();

                if (isSignedIn) {

                    jQuery('.bbilGapiDisconnect').show();

                    jQuery('.bbilGapiConnect').hide();

                    //jQuery('#bbilPropertyArea').addClass('hidden'); // Show property area



                    bbilListAccounts();

                }

                else {

                    jQuery('.bbilGapiDisconnect').hide();

                    jQuery('.bbilGapiConnect').show();

                    //jQuery('#bbilPropertyArea').addClass('hidden');

                }

            }



            function signIn() {

                var clientID = "<?php echo get_option('bbil_clientId'); ?>";

                var bbilScopes = 'https://www.googleapis.com/auth/analytics.readonly';

                var bbilAuthResult = gapi.auth.authorize({

                    client_id: clientID,

                    scope:bbilScopes

                });



                console.log(bbilAuthResult);



                var bbilToken = gapi.auth.getToken();

                if (bbilToken) {

                    var bbilAccessToken = gapi.auth.getToken().access_token;

                    if (bbilAccessToken) {

                        // make http get request towards: 'https://accounts.google.com/o/oauth2/revoke?token=' + bbilAccessToken

                    }

                }

                //console.log('access Token ='+bbilAccessToken);



                if (bbilAuthResult && !bbilAuthResult.error) {

                    jQuery.ajax({

                        type:"post",

                        url: "<?echo $initializationUrl; ?>",

                        data: {access_token:bbilAccessToken,},

                        success: function(data) {

                            if (data.status==200) {

                                location.reload();

                            }

                            else{

                                alert('Analytics not connected');

                            }

                        },

                        error: function(data) {

                            errormessage = 'Error';

                            //alert(errormessage);

                            alert('error');

                        },

                    });



                } else {

                    // User has not Authenticated and Authorized

                    alert('Unauthorized');

                }

            }



            function bbilAutoSignOut() {

                gapi.auth.signOut();

            }



            function bbilNext() {

                var bbilGaProperty = jQuery('#bbilGaProperty').val();

                var rslt = bbilGaProperty.split("#");

                var bbilGaPropertyView = jQuery('#bbilGaPropertyView').val();

                var bbilGaPropertyName = jQuery('#bbilGaPropertyView option:selected').text();

                var ajaxData = '';

                var validate = '';

                if(bbilGaProperty.trim()=='' && bbilGaPropertyView.trim()=='') { validate = 'Both fields are required.'; }

                else {

                    if(bbilGaProperty.trim()==''){ validate = validate+"Select a property"; }

                    if(bbilGaPropertyView.trim()==''){ validate = validate+"Select a property view"; }

                }



                if(validate==''){

                    ajaxData = {action: 'bbil_saveAnalyticsData', bbilGaProperty:rslt[0], bbilGaPropertyName:rslt[1],bbilGaPropertyView:bbilGaPropertyView, bbilGaPropertyViewName:bbilGaPropertyName};

                    //console.log(ajaxData); return false;

                    jQuery.ajax({

                        type:"post",

                        url: ajaxUrl,

                        data: ajaxData,

                        success: function(data) {

                            if (data==200) {

                                //location.reload();

                                window.location.href="<?php echo $_SESSION['redirectUrl']; ?>";

                            }

                            else{

                                alert('Analytics not updated');

                            }

                        },

                        error: function(data) {

                            errormessage = 'Error';

                            alert(errormessage);

                        },

                    });

                }

                else{

                    alert(validate);

                }

            }



            /*

             * Requests a list of all accounts for the authorized user.

             */

            function bbilListAccounts() {

                var request = gapi.client.analytics.management.accounts.list();

                request.execute(bbilPrintAccounts);

            }



            /*

             * The results of the list method are passed as the results object.

             * The following code shows how to iterate through them.

             */

            function bbilPrintAccounts(results) {

                if (results && !results.error) {

                    var accounts = results.items;

                    var account_ids = [];

                    for (var i = 0, account; account = accounts[i]; i++) {

                        /*console.log('Account Id: ' + account.id);

                         console.log('Account Kind: ' + account.kind);

                         console.log('Account Name: ' + account.name);

                         console.log('Account Created: ' + account.created);

                         console.log('Account Updated: ' + account.updated);*/



                        account_ids.push(account.id);

                        bbilListProperties(account_ids);

                    }

                }

            }



            /*

             * Requests a list of all properties for the authorized user.

             */

            function bbilListProperties(account_ids) {

                /*var request = gapi.client.analytics.management.webproperties.list({

                 'accountId': account_ids

                 });

                 request.execute(bbilPrintProperties);*/



                for ( var i = 0, l = account_ids.length; i < l; i++ ) {

                    var request = gapi.client.analytics.management.webproperties.list({

                        'accountId': account_ids[i]

                    });

                    request.execute(bbilPrintProperties);

                }

                //jQuery('#bbilGaProperty').selectpicker('refresh');

            }



            /*

             * The results of the list method are passed as the results object.

             * The following code shows how to iterate through them.

             */

            function bbilPrintProperties(results) {

                if (results && !results.error) {

                    var properties = results.items;

                    //var options = '<option value="">Select your tracking ID</option>';

                    var options = '';

                    for (var i = 0, property; property = properties[i]; i++) {

                        options +='<option value="'+property.accountId+'#'+property.id+'">'+property.name+'('+property.id+')'+'</option>';

                    }

                    if(options!=''){

                        jQuery('#bbilGaProperty').append(options);

                    } else {

                    }

                    //jQuery('#bbilGaPropertyView').append('<option value="">Select Property View</option>');



                }

            }



            /*

             * Requests a list of all View (Profiles) for the authorized user.

             */

            jQuery(document).on('change','#bbilGaProperty',function(){

                var data = jQuery(this).val();

                if (data) {

                    var res = data.split("#");



                    var request = gapi.client.analytics.management.profiles.list({

                        'accountId': res[0],

                        'webPropertyId': res[1]

                    });

                    request.execute(bbilPrintViews);

                } else {

                    jQuery('#bbilGaPropertyView').html('<option value="">Select Property View</option>');

                }

            });



            /*

             * The results of the list method are passed as the results object.

             * The following code shows how to iterate through them.

             */

            function bbilPrintViews(results='') {

                var options = '';

                if (results && !results.error) {

                    var profiles = results.items;

                    for (var i = 0, profile; profile = profiles[i]; i++) {

                        options +='<option value="'+profile.id+'">'+profile.name+'</option>';

                    }

                } else {

                    options += '<option value="">Select Property View</option>';

                }

                jQuery('#bbilGaPropertyView').html(options);

            }

        </script>

    <?php else :

        $_SESSION['redirectUrl'] = redirectUri();

        $_SESSION['intializeUrl'] = $initializationUrl;

        ?>



        <div class="container m-t-30 m-b-30 p-wrapper">

            <div class="row">

                <div class="col-sm-12">

                    <div class="t-card d-block">

                        <h2 class="analytics-title">Connect Google Analytics</h2>

                        <div class="messageContainer"></div>

                    </div>

                    

                    <div class="p-card d-block">

                        <div class="card-body">

                            <div class="bbilGapiConnect text-center" style="display:block">

                                <p>Your google analytics campaign data will display here. Please connect first.</p>

                                <a href="<?php echo $initializationUrl; ?>"><button class="btn btn-md btn-primary" href="#"><i class="icon-login icons"></i> Connect</button></a>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>



    <?php endif; ?>

<?php else : // Analytics credentials is saved

	$currentTime = time();

	$expireTime  = get_option('bbil_analyticsAccessTokenInvalidTime');

	if ($currentTime < $expireTime) $AccessToken = get_option('bbil_analyticsAccessToken');

	else $AccessToken = getNewAccessToken();



	$RefreshToken = get_option('bbil_analyticsRefreshToken');

	$PropertyId = get_option('bbil_analyticsPropertyId');

	$ViewId = get_option('bbil_analyticsViewId');

	$startDate = isset($_SESSION['analyticsStartDate']) && trim($_SESSION['analyticsStartDate']) ? trim($_SESSION['analyticsStartDate']): '30daysAgo';



    //echo "<br> AccessToken == $AccessToken";

    //echo "<br> RefreshToken == $RefreshToken";

    //echo "<br> PropertyId == $PropertyId";

    //echo "<br> ViewId == $ViewId";



    ?>

    <div class="p-wrapper">

        <section class="content">

            <div class="container m-t-30 m-b-30 p-wrapper">

                <div class="cards">

                    <div class="header">

                        <div class="row">

                            <div class="col-md-6 col-sm-6 col-md-offset-3 col-sm-offset-3">

                                <div class="t-card d-block">

                                    <h2 class="analytics-title">Campaigns</h2>

                                </div>

                            </div>

                            <div class="col-md-3 col-sm-3">

                                <button style="position: absolute;top: 15px; right: 15px;" class="btn btn-md btn-primary disconnect_btn" href="#" onclick="bbilSignOut();"><i class="icon-logout icons"></i>&nbsp; Disconnect</button>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-sm-12">

                                <div class="well">

                                    <div class="col-md-9 col-sm-9">

                                        <h4 class="well-title" style="margin:0">Overview: Pageviews</h4>

                                    </div>



                                    <div class="col-md-3 col-sm-3 text-right">

                                        <select class="form-control" name="analyticsTime" id="analyticsTime">

                                            <option value="today" <?php echo $startDate == 'today'? 'selected' : ''; ?>>Today</option>

                                            <option value="yesterday" <?php echo $startDate == 'yesterday'? 'selected' : ''; ?>>Yesterday</option>

                                            <option value="7daysAgo" <?php echo $startDate == '7daysAgo'? 'selected' : ''; ?>>Last 7 Days</option>

                                            <option value="30daysAgo" <?php echo $startDate == '30daysAgo'? 'selected' : ''; ?>>Last 30 Days</option>

                                            <option value="90daysAgo" <?php echo $startDate == '90daysAgo'? 'selected' : ''; ?>>Last 90 Days</option>

                                        </select>

                                    </div>

                                    <div class="clearfix"></div>

                                </div>

                            </div>

                        </div>

                    </div>



                    <div class="contentBody">

                        <section id="viewSelector"></section>

                        <section id="bbiltimeline"></section>

                    </div>

                </div>



                <div class="cards">

                    <div class="contentBody">

                        <div >

                            <section id="bbiltimeline2"></section>

                        </div>

                        <div class="clearfix" style="visibility: hidden; margin-top:50px">

                            <section id="viewSelector2" style=" display:none"></section>

                        </div>

                    </div>

                </div>



            </div>

        </section>

    </div>

    <div class="loaderWrapper">

        <div class="loader"></div>

    </div>

    <script>

        //Step 1

        (function(w,d,s,g,js,fjs){

            g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(cb){this.q.push(cb)}};

            js=d.createElement(s);fjs=d.getElementsByTagName(s)[0];

            js.src='https://apis.google.com/js/platform.js';

            fjs.parentNode.insertBefore(js,fjs);js.onload=function(){g.load('analytics')};

        }(window,document,'script'));



        // Step2

        gapi.analytics.ready(function() {

            var startDate = "<?php echo $startDate; ?>";



            // Step 3: Authorize the user.

            var clientID = "<?php echo get_option('bbil_clientId'); ?>";



            bbilAutoSignOut();



            /*gapi.analytics.auth.authorize({

             container: 'bbilEmbedApiAuthContainer',

             clientid: clientID

             });*/



            gapi.analytics.auth.authorize({

                container: 'bbilEmbedApiAuthContainer',

                clientid: clientID,

                serverAuth: {

                    access_token: '<?php echo $AccessToken; ?>',

                    refresh_token: '<?php echo $RefreshToken; ?>'

                }

            });



            var bbilIsAuthorized = gapi.analytics.auth.isAuthorized();

            bbilUpdateSigninStatus(bbilIsAuthorized);





            // Step 4: Create the view selector.



            /*var viewSelector = new gapi.analytics.ViewSelector({

                container: 'viewSelector'

            });*/



            var viewSelector2 = new gapi.analytics.ViewSelector({

                container: 'viewSelector2'

            });



            // Step 5: Create the bbiltimeline chart.



            var bbiltimeline = new gapi.analytics.googleCharts.DataChart({

                reportType: 'ga',

                query: {

                    'ids': 'ga:<?php echo $ViewId; ?>',

                    'dimensions': 'ga:campaign',

                    'metrics': 'ga:users',

                    'start-date': startDate,

                    'end-date': 'today',

                },

                chart: {

                    type: 'LINE',

                    container: 'bbiltimeline',

                    'options': {

                        'width': '90%',

                    }

                }

            });



            /*gapi.analytics.auth.on('success', function(response) {

                viewSelector.execute();

            });*/



            bbiltimeline.execute();



            /*viewSelector.on('change', function(ids) {

             var newIds = {

             query: {

             ids: ids

             }

             }

             bbiltimeline.set(newIds).execute();

             });*/



            var bbiltimeline2 = new gapi.analytics.googleCharts.DataChart({

                reportType: 'ga',

                query: {

                    'ids': 'ga:<?php echo $ViewId; ?>',

                    'dimensions': 'ga:campaign',

                    'metrics': 'ga:users,ga:newUsers,ga:sessions,ga:bounceRate,ga:impressions,ga:CPC,ga:pageviews,ga:goalValueAll',

                    'start-date': startDate,

                    'end-date': 'today',

                },

                chart: {

                    type: 'TABLE',

                    container: 'bbiltimeline2',

                    'options': {

                        'width': '100%',

                    }

                }

            });



            // Step 6: Hook up the components to work together.



            gapi.analytics.auth.on('success', function(response) {

                viewSelector2.execute();

            });

            bbiltimeline2.execute();



            /*viewSelector2.on('change', function(ids) {

                var newIds = {

                    query: {

                        ids: ids

                    }

                }

                bbiltimeline2.set(newIds).execute();

            });*/

        });







        /*

         * ########################### Connection status checking area ###################################333

         */

        function bbilUpdateSigninStatus(isSignedIn) {

            // When signin status changes, this function is called.

            // If the signin status is changed to signedIn, we make an API call.

            jQuery('#bbilEmbedApiAuthContainer').hide();

            jQuery('#bbilViewSelectorOneContainer table').hide();

        }



        function bbilAutoSignOut() {

            gapi.auth.signOut();

        }



        jQuery(document).on('change','#analyticsTime',function(){

            var date = jQuery(this).val();

            jQuery.ajax({

                type:"post",

                url: ajaxUrl,

                data: { action: 'bbil_analyticsDateChange', date: date },

                beforeSend: function () { jQuery(this).attr('disabled', true); jQuery('.loaderWrapper').addClass('open'); },

                success: function(data) {

                    data = JSON.parse(data);

                    if (data.status==200) { location.reload(); }

                    else{ alert(data); }

                },

                error: function(data) {

                    alert('Error');

                },

            });

        });

    </script>

<?php endif; ?>

<script>

    function bbilSignOut() {

        gapi.auth.signOut();



        jQuery.ajax({

            type:"post",

            url: ajaxUrl,

            data: { action: 'bbil_analyticsDisconnect' },

            beforeSend: function () {

                jQuery('.loaderWrapper').addClass('open');

            },

            success: function(data) {

                if (data==200) { location.reload(); }

                else{ alert(data); }

            },

            error: function(data) {

                alert('Error');

            },

        });

    }

</script>