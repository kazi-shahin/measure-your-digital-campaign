<?php
/**
 * The admin-specific connection information page
 *
 * @link       blubirdinteractive.com
 * @since      1.0.0
 *
 * @package    Bbilgcb
 * @subpackage Bbilgcb/admin
 */
?>
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

<div class="container m-t-30 m-b-30 p-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <div class="t-card d-block">
                <h2 class="analytics-title">Connection Info</h2>
                <div class="messageContainer"></div>
            </div>

            <div class="p-card d-block">
                <div class="card-body">
                    <div id="tabs">
                        <ul>
                            <li><a href="#tabs-1">Analytics Info</a></li>
                            <li><a href="#tabs-2">Secret key Info</a></li>
                        </ul>
                        <div id="tabs-1">
                            <?php if (get_option('bbil_analyticsConnected')) {
                                echo '<p> <strong> Connected Email : </strong> '. get_option('bbil_analyticsEmail') .' </p> ';
                                echo '<p> <strong> Tracking ID : </strong> '. get_option('bbil_analyticsPropertyName') .' </p>';
                                echo '<p> <strong> View Name : </strong> '. get_option('bbil_analyticsViewName') .' </p>';
                            } else {
                                echo '<h4 class="text-danger text-center">Account disconnected.</h4>';
                                echo '<p class="text-center">Please connect form <a href="'. esc_url(admin_url('admin.php?page=bbil_gcb')) .'">here.</a></p>';
                            } ?>
                        </div>

                        <div id="tabs-2">
                            <form class="highlight-froms">
                                <div class="form-group custom_input_form_group mendatory-form group">
                                    <div class="input-group">
                                        <input type="text" class="form-control custom_input_text mendatory-field" id="clientID" value="<?php echo get_option('bbil_clientId');?>" readonly="">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary editBtn" type="button">Edit</button>
                                        </span>
                                    </div>
                                    <label class="float-label" for="campaignName">Client ID</label>
                                </div>
                                <div class="form-group custom_input_form_group mendatory-form group">
                                    <div class="input-group">
                                        <input type="text" class="form-control custom_input_text mendatory-field" id="clientSecret" value="<?php echo get_option('bbil_clientISecret');?>" readonly="">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary editBtn" type="button">Edit</button>
                                        </span>
                                    </div>
                                    <label class="float-label" for="campaignName">Client Secret</label>
                                </div>
                                <button id="submit" type="submit" class="btn btn-info btn-lg saveBtn" style="width: 100%;" disabled="">Save</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="loaderWrapper"> <div class="loader"></div> </div>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
    (function(w,d,s,g,js,fs){
        g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
        js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
        js.src='https://apis.google.com/js/platform.js';
        fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
    }(window,document,'script'));
    var ajaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>";
    var redirectUrl = "<?php echo esc_url(admin_url('admin.php?page=bbil_gcb')); ?>";
    function signOut() {
        gapi.auth.signOut();
        jQuery.ajax({
            type:"post",
            url: ajaxUrl,
            data: { action: 'bbil_analyticsDisconnect' },
            success: function(data) {
                if (data==200) { jQuery('.messageContainer').html('<p class="text-success">Google analytics is disconnected. </p>'); }
                else{ jQuery('.messageContainer').html('<p class="text-danger">Google analytics is disconnected. </p>'); }
                setTimeout(function () {
                    jQuery('.messageContainer').html('');
                    window.location.href = redirectUrl;
                }, 2000)
            },
            error: function(e) {alert(e); },
        });
    }

    //Jquery ui Tabs
    jQuery( function($) {
        $( "#tabs" ).tabs();
        $(document).on('click', '.editBtn', function (event) {
            event.preventDefault();
            $(this).parents('.input-group').find('input').attr('readonly', false).css('border', '1px solid red');
            $(this).attr('disabled', true);
            $('#submit').attr('disabled', false);
        });
        function showMessage(container, message, isSuccess=false) {
            container.html(message);
            setTimeout(function() {
                container.hide('slow');
                if (isSuccess) { location.href = redirectUrl; }
                //if (isSuccess) { location.reload(); }
            }, 3000);
        }
        $(document).on('submit', 'form', function (event) {
            event.preventDefault();
            var message 		= $('.messageContainer');
            var submitBtn 		= $('#submit');
            var clientID 		= $('#clientID').val().trim();
            var clientSecret 	= $('#clientSecret').val().trim();

            // Validation
            if (clientID.length < 1 || clientSecret.length < 1 ) {
                // error
                showMessage(message, '<p class="text-error">All fields are required.</p>');
            } else {
                // signout form google analytics
                signOut();
                // Submit via ajax
                var formData = {action: 'bbil_saveSetupData', clientId: clientID, clientISecret: clientSecret };
                $.ajax({
                    url: ajaxUrl,
                    method: 'post',
                    data: formData,
                    beforeSend : function() {
                        submitBtn.attr('disabled', true).text('Saving ...');
                        $('.loaderWrapper').addClass('open');
                    },
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status == 200) {
                            showMessage(message, '<div class="alert alert-success no-margin" role="alert"> Successfully saved. </div>', true);
                        } else {
                            showMessage(message, '<p class="text-error">Something went wrong. Please submit again.</p>');
                        }
                        submitBtn.attr('disabled', false).text('Submit');
                    },
                    error: function (e) {
                        console.log(e);
                        submitBtn.attr('disabled', false).text('Submit');
                    }
                });
            }
        });
    });
</script>