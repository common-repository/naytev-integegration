<?php
$message = '';
if( isset($_POST['task']) && $_POST['task'] == 'save_naytev_ops' )
{

	update_option('naytev_site_id', trim($_POST['naytev_site_id']));
	update_option('naytev_api_key', trim($_POST['naytev_api_key']));
	update_option('naytev_embed_id', trim($_POST['naytev_embed_id']));
	$message = __('Settings updated');
}
?>
<div class="wrap">
	<h2><?php _e('NAYTEV Settings'); ?></h2>
	<?php if(!empty($message)): ?>
	<div class="updated below-h2" id="message"><p><?php print $message; ?>.</p></div>
	<?php endif; ?>
	<p>
		<?php _e('Connect your Wordpress site to NAYTEV by adding your Site, API and Embed ID here.'); ?>
		<?php _e('We\'ll automatically add the share functionality to your website.'); ?>
	</p>
	<p>
		In your Naytev dashboard <a href="https://www.naytev.com/dashboard">(https://www.naytev.com/dashboard)</a> you can turn the share bar on or off, and add experiments to the share messaging of any page.
	</p>
	<form action="" method="post">
		<input type="hidden" name="task" value="save_naytev_ops" />
		<p>
			<input type="text" name="naytev_site_id" value="<?php print get_option('naytev_site_id'); ?>" placeholder="<?php _e('Input your Naytev Site ID'); ?>" style="width:300px;" />
			<input type="text" name="naytev_api_key" value="<?php print get_option('naytev_api_key'); ?>" placeholder="<?php _e('Input your Naytev API Key'); ?>" style="width:300px;" />
			<input type="text" name="naytev_embed_id" value="<?php print get_option('naytev_embed_id'); ?>" placeholder="<?php _e('Input your Naytev Embed ID'); ?>" style="width:300px;" />
			<button type="submit" class="button-primary"><?php _e('Save'); ?></button>
		</p>
	</form>
	<h4><?php _e('Where do I get my Naytev Embed ID?'); ?></h4>
	<p>
		<?php _e('Your Naytev Embed ID is a combination of numbers and letters, and can be found in your NAYTEV dashboard, The ID is the sequence of numbers and letters directly following "embed/" and preceding ".js". See below for an example.'); ?>
	</p>
	<p>
		<img src="<?php print SB_NAYTEV_PLUGIN_URL ?>/images/embed.png" alt="" style="width:50%;" />
	</p>
</div>
