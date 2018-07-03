<?php
/**
 * The admin-specific Campaign Creator page
 * 
 * @link       blubirdinteractive.com
 * @since      1.0.0
 *
 * @package    Bbilgcb
 * @subpackage Bbilgcb/admin
 */

$ID         = isset($_GET['id']) && !empty($_GET['id']) ? trim($_GET['id']) : 0;
$website    = isset($_GET['website']) && !empty($_GET['website']) ? trim($_GET['website']) : false;
$source     = isset($_GET['utm_source']) && !empty($_GET['utm_source']) ? trim($_GET['utm_source']) : false;
$medium     = isset($_GET['utm_medium']) && !empty($_GET['utm_medium']) ? trim($_GET['utm_medium']) : false;
$name       = isset($_GET['utm_campaign']) && !empty($_GET['utm_campaign']) ? trim($_GET['utm_campaign']) : false;

$link       = $website.'?utm_source='. $source .'&utm_medium='. $medium .'&utm_campaign='. $name;
?>
<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

<div class="container m-t-30 m-b-30 p-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <div class="t-card d-block">
                <h2 class="analytics-title text-capitalize">Create campaign link</h2>
                <div class="messageContainer"></div>
            </div>
            <div class="p-card d-block">
                <div class="card-body">
                    <div id="contenteContainer">
                        <div class="form-group custom_input_form_group mendatory-form group">
                            <input type="text" class="form-control custom_input_text mendatory-field" id="campaignName" value="<?php echo $name; ?>" required>
                            <span class="form-highlight"></span>
                            <span class="form-bar"></span>
                            <label class="float-label" for="campaignName">Campaign Name(e.g.Sign up, newsletter, contact us, etc.)</label>
                        </div>
                        <div class="form-group custom_input_form_group mendatory-form group">
                            <input type="text" class="form-control custom_input_text mendatory-field" id="website" value="<?php echo $website; ?>" required>
                            <span class="form-highlight"></span>
                            <span class="form-bar"></span>
                            <span class="redColor fieldValidationError"></span>
                            <label class="float-label" for="website">Website URL(e.g. https://www.example.com)</label>
                        </div>
                        <div class="form-group custom_input_form_group mendatory-form group">
                            <input type="text" class="form-control custom_input_text mendatory-field" id="campaignSource" value="<?php echo $source; ?>" required>
                            <span class="form-highlight"></span>
                            <span class="form-bar"></span>
                            <label class="float-label" for="campaignSource">Campaign Source(e.g. google, newsletter)</label>
                        </div>
                        <div class="form-group custom_input_form_group mendatory-form group">
                            <input type="text" class="form-control custom_input_text mendatory-field" id="campaignMedium" value="<?php echo $medium; ?>" required>
                            <span class="form-highlight"></span>
                            <span class="form-bar"></span>
                            <label class="float-label" for="campaignMedium">Campaign Medium(e.g. cpc, banner, email)</label>
                        </div>
                        <?php if ($link): ?>
                            <div class="form-group custom_input_form_group"> 
                                <label id="campaignLinkText"><?php echo $link; ?></label>
                                <input type="hidden" class="form-control" id="campaignLink" value="<?php echo $link; ?>">
                                <input type="hidden" class="form-control" id="shortLink" value="">
                                <input type="hidden" class="form-control" id="isShorted" value="false">
                                <input type="hidden" class="form-control" id="linkID" value="<?php echo $ID; ?>">
                            </div>
                        <?php else: ?>
                            <div class="form-group custom_input_form_group"> 
                                <label id="campaignLinkText">Fill out all the required fields above and a URL will be automatically generated here.</label>
                                <input type="hidden" class="form-control" id="campaignLink">
                                <input type="hidden" class="form-control" id="shortLink" value="">
                                <input type="hidden" class="form-control" id="isShorted" value="true">
                                <input type="hidden" class="form-control" id="linkID" value="<?php echo $ID; ?>">
                            </div>
                        <?php endif ?>
                        <div class="text-center">
                            <button type="button" class="btn btn-primary" id="url_shortner">Short the url</button>
                            <button type="button" class="btn btn-primary" id="saveShortLink" style="margin-left: 15px;">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="loaderWrapper"> <div class="loader"></div> </div>

<script>
    (function ($) {
        var ajaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>";
        var shortUrlsPage = "<?php echo admin_url( 'admin.php?page=bbil-short_urls'); ?>";
        function showErrorMessage (message) {
            $('.messageContainer').html('<p class="error-message redBorder">'+ message +'</p>');
            setTimeout(function(){
                $('.messageContainer p').hide(500);
                $('.loaderWrapper').removeClass('open');
            }, 2000);
        }
        function isUrlValid(url) {
            return /^(?:(ftp|http|https)?:\/\/)?(?:[\w-]+\.)+([a-z]|[A-Z]|[0-9]){2,6}$/gi.test(url);
        }
        function showValidationMessage(message, container) {
            container.html(message);
        }
        function removeValidationMessage(container) {
            container.html('');
        }
        function formatedUrl(url) {
            if (!/^(?:f|ht)tps?\:\/\//.test(url)) {
                url = "http://" + url;
            }
            return url;
        }
        // Create long url
        $(document).on('keyup','#campaignName,#website,#campaignSource,#campaignMedium',function() {
            var campaignName = $('#campaignName').val();
            campaignName = campaignName.toLowerCase().replace(/[^a-z]/g,' ').replace(/[ ][ ]*/g, '-');
            var website = $('#website').val();
            var campaignSource = $('#campaignSource').val();
            var campaignMedium = $('#campaignMedium').val();
            var campaignLink = '';
            formatedUrl(website);
            $('#url_shortner').removeClass('hidden');
            $('#shortLink').val('');
            $('#isShorted').val('false');
            if(website !=''){
                if (isUrlValid(website)) {
                    campaignLink = formatedUrl(website);
                    removeValidationMessage($('.fieldValidationError'));
                } else {
                    website = '';
                    showValidationMessage('Invalid website url', $('.fieldValidationError'));
                }
            }
            if(website !='' && campaignSource!=''){
                campaignLink += '?utm_source='+campaignSource;
            }
            if(website !='' && campaignMedium!=''){
                campaignLink += '&utm_medium='+campaignMedium;
            }
            if(website !='' && campaignName!=''){
                campaignLink += '&utm_campaign='+campaignName;
            }
            if(campaignSource !=''){
                $('#campaignLinkText').text(campaignLink);
                $('#campaignLink').val(campaignLink);
            }
            else {
                $('#campaignLinkText').text('Fill out all the required fields above and a URL will be automatically generated for you here.');
                $('#campaignLink').val('');
            }
        });
        // Short the url
        $(document).on ('click', '#url_shortner', function (event) {
            event.preventDefault();
            var url = $('#campaignLink').val();
            if (url) {
                $.ajax({
                    type:"post",
                    url: ajaxUrl,
                    data: { action: 'bbil_shortUrl', url: url },
                    beforeSend: function () { $('.loaderWrapper').addClass('open'); },
                    success: function(response) {
                        // alert(response); return false;
                        response = JSON.parse(response);
                        $('.loaderWrapper').removeClass('open');
                        if (response.status == 200) {
                            $('#url_shortner').addClass('hidden');
                            $('#shortLink').val(response.url);
                            $('#isShorted').val('true');
                            $('#campaignLinkText').html(response.url);
                        } else {
                            showErrorMessage (response.message);
                        }
                    },
                    error: function(e) {
                        $('.loaderWrapper').removeClass('open');
                        console.log(e);
                    },
                });
            } else {
                showErrorMessage ('Please insert required fields first.');
            }
        });
        // Save link
        $(document).on ('click', '#saveShortLink', function (event) {
            event.preventDefault();
            var oldLink = "<?php echo $link; ?>";
            var url = $('#campaignLink').val();
            var shortLink = $('#shortLink').val();
            var isShorted = $('#isShorted').val();
            var linkID = $('#linkID').val();

            if (url != oldLink || isShorted == 'true') {
                var ajaxData = { action: 'bbil_saveShortLink', url: url, shortLink: shortLink, isShorted: isShorted, id: linkID };
                // console.log(ajaxData); return false;
                if (url) {
                    $.ajax({
                        type:"post",
                        url: ajaxUrl,
                        data: ajaxData,
                        beforeSend: function () { $('.loaderWrapper').addClass('open'); },
                        success: function(response) {
                            response = JSON.parse(response);
                            // console.log(response); return false;
                            if (response.status == 200) {
                                window.location.href = shortUrlsPage;
                            } else {
                                showErrorMessage (response.message);
                            }
                        },
                        error: function(e) {
                            console.log(e);
                        },
                    });
                } else {
                    // Show error message
                    showErrorMessage ('Please insert required fields first.');
                }
            } else {
                // Both links are same
                showErrorMessage ('Please change the fields and save again.');
            }
        });
    }(jQuery));
</script>