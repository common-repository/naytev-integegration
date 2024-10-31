<?php

/**
 * Utility class
 *
 * Registers some basic functions for use by other classes.
 * Also adds some libraries to be used elsewhere in the project.
 *
 * @since 2.0.0
 *
 */

class Naytev_Util {

	public function init() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}

	public function __construct() {
        #Stuff
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
    }

    # via http://www.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/
    public function logger($message){
        #var_dump($message); die();
        if (WP_DEBUG == true) {
            if (is_array($message) || is_object($message)) {
                error_log(print_r($message, true));
            } else {
                error_log($message);
            }
        }
        #var_dump('<pre>'); var_dump($message); die();

    }


    public function add_admin_scripts($hook){
        global $pagenow;

        wp_register_script(WP_NAYTEV_SLUG . '-repeater', SB_NAYTEV_PLUGIN_URL . wp_naytev()->build_path(array('assets','js','repeater-imp.js'),true,true), array( 'jquery' ));
				wp_register_script(WP_NAYTEV_SLUG . '-uploader', SB_NAYTEV_PLUGIN_URL . wp_naytev()->build_path(array('assets','js','media-query-imp.js'), true, true), array( 'jquery', 'thickbox', 'media-upload'  ));

        #if ('-menu' == $hook){
				# @todo This should be better in deciding scope.
        wp_enqueue_script(WP_NAYTEV_SLUG.'-repeater');
				# This enqueue's the scripts to build the media uploader.
				wp_enqueue_media();
				wp_enqueue_script(WP_NAYTEV_SLUG.'-uploader');
        #}

    }

}
