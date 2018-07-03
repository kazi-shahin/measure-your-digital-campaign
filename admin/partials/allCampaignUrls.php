<?php
/**
* The admin-specific All short urls page
*
* @link       blubirdinteractive.com
* @since      1.0.0
*
* @package    Bbilgcb
* @subpackage Bbilgcb/admin
*/

global $wpdb;
$tableName = $wpdb->prefix .BBIL_TABLE;
$myrows = $wpdb->get_results( "SELECT * FROM $tableName ORDER BY id DESC" );
?>

<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">

<div class="container m-t-30 m-b-30 p-wrapper">
    <div class="row">
        <div class="col-sm-12">
            <div class="t-card d-block">
                <h2 class="analytics-title">All Campaign Link</h2>
                <div class="messagesContainer text-center"></div>
            </div>

            <?php if ($myrows) : ?>
                <table class="table table-hover table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="w60">Campaign Link</th>
                            <th class="text-center">Created Date</th>
                            <th class="w20 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($myrows) {
                            foreach ($myrows as $key => $myrow) {
                                $link = $myrow->is_shorted == 'true' ? $myrow->short_link : $myrow->url;
                                // $editLink = str_replace('http://', '', $myrow->url);
                                // $editLink = str_replace('https://', '', $editLink);
                                // $editLink = explode('?', $editLink);
                                $editLink = explode('?', $myrow->url);
                                $website = $editLink[0];
                                $editLink = pluginPageUrl("bbil-campaign_creator").'&website='. $website .'&'. $editLink[1] .'&id='. $myrow->id;

                                echo '<tr id="'. $myrow->id .'">';
                                echo '<td class="text-center">'. ++$key .'</td>';
                                echo '<td>'. $link .'</td>';

                                echo '<td  class="text-center">'. date('F j, Y', strtotime($myrow->created_at)) .'</td>';
                                echo '<td width="160px" class="actionBtnWrapper text-center">';
                                echo '<input type="text" class="shortLink invisibeElement" value="'. $link .'">';
                                echo '<button title="Copy Link" class="btn btn-primary btn-xs btnCopyLink"><i class="fa fa-link" aria-hidden="true"></i></button>';
                                if ($myrow->is_shorted == 'true') {
                                    echo '<a title="Link Count" href="'. $myrow->short_link .'.info" type="button" target="_blank" class="btn btn-success btn-xs btnLinkCount"><i class="fa fa-circle-o-notch" aria-hidden="true"></i></a>';
                                }
                                echo '<button title="Delete" rowID="'. $myrow->id .'" class="btn btn-danger btn-xs btnDeleteLink"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                                echo '<a title="Edit" href="'. $editLink .'" type="button" target="_blank" class="btn btn-info btn-xs btnEditLink"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
                                echo '</td>';
                                // echo '<td>'. $editLink .'</td>';
                                echo '</tr>';
                            }
                        } ?>
                    </tbody>
                </table>
            <?php else: ?>
            <div class="alert alert-danger single-elem" role="alert"> No link found. </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    jQuery(function($) {
        var ajaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>";
        function showMessage(message, error = false) {
            var messageClass = '';
            if (error)  messageClass = 'text-danger redBorder';
            else  messageClass = 'text-success greenBorder'; 

            $('.messagesContainer').html('<p class="text-bold ' + messageClass + '">' + message + '</p>');
            setTimeout(function() {
                $('.messagesContainer p').hide(500);
            }, 2000);
        }

        $(document).on('click', '.btnDeleteLink', function(event) {
            event.preventDefault();
            //var rowID
            var rowID = $(this).attr('rowID');
            var button = $(this);
            if (rowID) {
                $.ajax({
                    type: "post",
                    url: ajaxUrl,
                    data: {action: 'bbil_deleteCampaignLink', rowID: rowID },
                    beforeSend: function() {button.attr('disabled', true); },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status == 200) {
                            $('tr#' + rowID).remove();
                            showMessage(response.message);
                        } else {
                            showMessage(response.message, true);
                        }
                        button.attr('disabled', false);
                    },
                    error: function(e) {
                        console.log(e);
                        button.attr('disabled', false);
                    },
                });
            } else {
                showMessage('Error deleting cmapaing link.', true);
            }
        });

        $(document).on('click', '.btnCopyLink', function(event) {
            var copyText = jQuery(this).parents('td').find('.shortLink');
            copyText.select();
            document.execCommand("Copy");
            var message = "Copied : " + copyText.val();
            showMessage(message);
        });
    }); 
</script>