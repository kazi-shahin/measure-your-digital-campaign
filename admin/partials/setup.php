<link href="https://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

<div class="container m-t-30 m-b-30 p-wrapper">
	<div class="row">
		<div class="col-sm-12">
			<div class="t-card d-block">
				<h2 class="analytics-title">Connection informations</h2>
				<div class="messageContainer"></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="p-card d-block">
			  	<div class="card-body">
				    <p>Please put this url as redirect url</p>
					<p class="well" style="color:#337ab7;"><?php echo pluginLibUrl(); ?></p>

					<form class="" action="" method="POST" role="form">
						<div class="form-group m-t-40 custom_input_form_group mendatory-form group">
							<p class="highlight-text"><a target="_blank" href="<?php echo plugins_url(BBIL_PLUGINDIR.'/download.pdf'); ?>">How to get Id and secret?</a></p>
					        <input type="text" class="form-control custom_input_text mendatory-field" id="clientID" value="<?php echo get_option('bbil_clientId'); ?>" required>
					        <span class="form-highlight"></span>
					        <span class="form-bar"></span>
					        <label class="float-label" for="clientID">Client ID</label>
                            <!-- <span class='tooltipContent' data-toggle="tooltip" data-html="true" title="<a href='' target='blank' >Click here and follow this tutorial</a>">?</span> -->
					    </div>

					    <div class="form-group custom_input_form_group mendatory-form group">
					        <input type="text" class="form-control custom_input_text mendatory-field" id="clientSecret" value="<?php echo get_option('bbil_clientISecret'); ?>" required>
					        <span class="form-highlight"></span>
					        <span class="form-bar"></span>
					        <label class="float-label" for="clientSecret">Client Secret</label>
					    </div>
						<div class="text-right">
							<button type="submit" id="submit" class="btn btn-primary d-block"><i class="icon-check icons"></i>Submit</button>
						</div>
					</form>
			  	</div>
			</div>

		</div>
	</div>
</div>
<div class="loaderWrapper">
    <div class="loader"></div>
</div>

<script>
	(function ($) {
		var ajaxUrl = "<?php echo esc_url(admin_url('admin-ajax.php')); ?>"
		function showMessage(container, message, isSuccess=false) {
		 	container.html(message);
		 	setTimeout(function() {
		 		container.hide('slow');
		 		if (isSuccess) { location.reload(); }
		 	}, 5000);
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
			} 
			else {
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

        // Tooltip

        var originalLeave = $.fn.tooltip.Constructor.prototype.leave;
        $.fn.tooltip.Constructor.prototype.leave = function(obj) {
            var self = obj instanceof this.constructor ?
                obj : $(obj.currentTarget)[this.type](this.getDelegateOptions()).data('bs.' + this.type)
            var container, timeout;

            originalLeave.call(this, obj);

            if (obj.currentTarget) {
                container = $(obj.currentTarget).siblings('.tooltip')
                timeout = self.timeout;
                container.one('mouseenter', function() {
                    //We entered the actual popover – call off the dogs
                    clearTimeout(timeout);
                    //Let's monitor popover content instead
                    container.one('mouseleave', function() {
                        $.fn.tooltip.Constructor.prototype.leave.call(self, self);
                    });
                })
            }
        };


        $('body').tooltip({
            selector: '[data-toggle]',
            trigger: 'click hover',
            placement: 'auto',
            delay: {
                show: 50,
                hide: 400
            }
        });
	}(jQuery))
</script>