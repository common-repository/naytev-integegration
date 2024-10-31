jQuery(window).load(function() {

	jQuery('body').on('click', '.naytev_img_upload', function(e) {
			//e.preventDefault();
            var parentE  =   jQuery(this).parents('li.repeat-element');
			//console.log(parentE);
            var parentID =  parentE.attr('id');
			//alert(parentID);
			// via http://stackoverflow.com/questions/13847714/wordpress-3-5-custom-media-upload-for-your-theme-options?cachebusterTimestamp=1405277969630
			var custom_uploader = wp.media({
				title: 'Upload Image',
				button: {
					text: 'Add Image'
				},
				multiple: false  // Set this to true to allow multiple files to be selected
			})
			.on('select', function() {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				//$('.custom_media_image').attr('src', attachment.url);
				//alert(parentID);
				jQuery('#'+parentID+' .naytev_img_upload_field').val(attachment.url);
				//$('.custom_media_id').val(attachment.id);
			})
			.open();
		return false;       
	});

});