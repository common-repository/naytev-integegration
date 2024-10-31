<?php
/**
 * Plugin Name: Naytev for WordPress
 * Plugin URI: http://naytev.com
 * Description: Naytev integration
 * Version: 2.2.4
 * Author: Naytev
 * Author URI: https://www.naytev.com
 */

# Establish Constants. Must be unique enough that they do not conflict with others.
# Plugin directory (relative)
define('SB_NAYTEV_PLUGIN_DIR', dirname(__FILE__));
# URL path to plugin (absolute)
define('SB_NAYTEV_PLUGIN_URL', WP_PLUGIN_URL . '/' . basename(SB_NAYTEV_PLUGIN_DIR));
# Slug for plugin.
# @todo remove this, used classed constant instead. Take up less load in PHP.
define('WP_NAYTEV_SLUG', 'wp_naytev');
# Version (used to control 'on upgrade' actions.)
define('WP_NAYTEV_VER', 2.2);
class SB_Naytev
{
	#Establish class constants
	var $slash;
	var $title;
	var $slug;
	var $util;
	var $transports;
	var $meta_boxes;
	var $admin_boxes;
	var $template;
	var $post_metas;
	var $json;

	# Only initalize this plugin through this function. It forces STATIC behaviour.
  public static function init() {
		static $instance;

		if ( ! is_a( $instance, 'SB_Naytev' ) ) {
			$instance = new self();
		}

		return $instance;
	}

	private function __construct()
	{
		#Constants
		$this->slash = DIRECTORY_SEPARATOR;
		$this->title = 'Naytev';
		$this->slug = 'wp_naytev';
		$this->includes();

		#Builders and utilities.
		$this->util();
		$this->meta_boxes();
		$this->admin_boxes();
		$this->json();
		$this->transports();

		# Activates
		$this->post_metas();
		$this->template();

		#The other guy's stuff.
		$this->AddActions();
		add_action( 'wp_enqueue_scripts', array($this, 'AddScripts'));
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
	}

	/**
	 * Include necessary files
	 *
	 * @since 0.0.1
	 */
	function includes() {

        require_once( SB_NAYTEV_PLUGIN_DIR . self::build_path(array('lib','util.php')));
        require_once( SB_NAYTEV_PLUGIN_DIR . self::build_path(array('lib', 'meta-boxes.php')));
        require_once( SB_NAYTEV_PLUGIN_DIR . self::build_path(array('lib', 'admin-boxes.php') ));
				require_once( SB_NAYTEV_PLUGIN_DIR . self::build_path(array('includes', 'naytev-post-metas.php') ));
        require_once( SB_NAYTEV_PLUGIN_DIR . self::build_path(array('includes', 'template-tags.php') ));
				require_once( SB_NAYTEV_PLUGIN_DIR . self::build_path(array('lib', 'json_handlers', 'json_workers.php') ));
				require_once( SB_NAYTEV_PLUGIN_DIR . self::build_path(array('includes', 'transports.php') ));

    }

		# This section will initalize all client classes.

   function util(){
 		if ( empty( $this->util ) ) {
			$this->util = new Naytev_Util;
		}
    }

   function meta_boxes(){
 		if ( empty( $this->meta_boxes ) ) {
			$this->meta_boxes = new Naytev_Meta_Boxes;
		}
    }

   function admin_boxes(){
 		if ( empty( $this->admin_boxes ) ) {
			$this->admin_boxes = new Naytev_Admin_Boxes;
		}
    }

   function post_metas(){
 		if ( empty( $this->post_metas ) ) {
			$this->post_metas = new Naytev_Post_Metas;
		}
    }

   function json(){
 		if ( empty( $this->json ) ) {
			# Subtreed via https://github.com/AramZS/wp_internal_json_handlers
			# In order to have maximum ease in deployment maintain via git subtree.
			$this->json = ZS_JSON_Workers::init();
		}
    }

		/**
		 * This is a placeholder function for handling template tags.
		 * Template tags are set up to enhance theme functions for
		 * people developing or modifying themes.
		 *
		 */

   function template(){
 		if ( empty( $this->template ) ) {
			#$this->template = new Naytev_Template_Tags;
		}
    }


   function transports(){
 		if ( empty( $this->transports ) ) {
			$this->transports = Naytev_Transports::init();
		}
    }

	/*
	 * Remaining actions added by the older version
	 * of the plugin. They should eventually be moved
	 * to a client class.
	*/
	public function AddActions()
	{
		if( is_admin() )
		{
			add_action('admin_menu', array($this, 'action_admin_menu'));
		}
		else
		{

		}
	}

	/*
	 * Remaining script embed commands.
	 * They should be moved elsewhere.
	 */

	public function AddScripts()
	{
		if( is_admin() )
		{

		}
		else
		{
			$id = get_option('naytev_embed_id');
			if( !empty($id) )
			{
				$url = sprintf("//naytev.global.ssl.fastly.net/js/embed/%s.js", $id);
				if (WP_DEBUG){
					$url = sprintf("//naytev-stage.herokuapp.com/js/embed/%s.js", $id);
				}
				wp_enqueue_script('sb-naytev-js', $url);
			}

		}
	}

	/*
	 * Set up styles and scripts for the backend.
	 */

	public function add_admin_scripts(){

		wp_enqueue_script('naytev-admin-ui', SB_NAYTEV_PLUGIN_URL . wp_naytev()->build_path(array('assets','js','naytev-admin-ui.js'),true,true));
		wp_enqueue_style('naytev-admin-styles', SB_NAYTEV_PLUGIN_URL . wp_naytev()->build_path(array('assets','css','css','style.css'),true,true));

	}

	/*
	 * This is no longer the best way to deal with settings.
	 * We should use the settings API.
	 */

	public function action_admin_menu()
	{
		add_options_page(__('Naytev Settings'), __('Naytev Settings'), 'manage_options', 'sb-naytev-menu', array($this, 'naytev_settings_page'));
	}
	public function naytev_settings_page()
	{
		require_once SB_NAYTEV_PLUGIN_DIR . self::build_path(array('pages','admin','settings.php'));
	}


	/**
	 * Build file paths.
	 *
	 * Build paths with arrays Call out of static function wp_naytev->build_path
	 * or self::build_path. Use like:
	 *
	 * 		build_path(array("home", "alice", "Documents", "example.txt"));
	 *
	 * @since 2.0.0
	 *
	 * @see http://php.net/manual/en/dir.constants.php
	 * @global string DIRECTORY_SEPARATOR Called from class definition, system variable
	 *
	 * @param array $segments The pieces of the URL, should be array of strings. Default null Accepts string.
	 * @param bool $leading Optional If the returned path should have a leading slash. Default true.
	 * @param bool $url Optional If the returned path should use web URL style pathing or system style. Default false
	 * @return string The composed path.
	 *
	 */
	public function build_path($segments=array(), $leading = true, $url = false) {
		#var_dump($segments); var_dump($this->slash); var_dump($url); var_dump($leading);
		if ($url){
            $slash = '/';
        } else {
            $slash = $this->slash;
        }
        $string = join($slash, $segments);
		#var_dump($string); die();
		if ($leading){
			$string = $slash . $string;
		}
		return $string;
	}

}

/**
 * Bootstrap
 *
 * You can also use this to get a value out of the global, eg
 *
 *    $foo = wp_naytev()->bar;
 *
 * @since 2.0
 */
function wp_naytev() {
	return SB_Naytev::init();
}

// Start me up!
wp_naytev();
