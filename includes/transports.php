<?php
/**
 * Class to move data to and from the Naytev API.
 *
 * Requires the JSON Workers library.
 *
 * @since 2.0.0
 */
class Naytev_Transports {

	# Establish static endpoints.
	public static $base_api_url = 'https://api.naytev.com';
	public static $base_stage_api_url = 'http://naytev.dev'; #https://naytev-stage.herokuapp.com
	public static $api_ver = 'v1';
	public static $site_endpoint = 'site';
	public static $experiment_endpoint = 'experiment';
	public static $variant_endpoint = 'variant';
	public static $stats_endpoint = 'stats';
	public static $status_endpoint = 'status';
	public static $fb_network_key = 'facebookPost';
	public static $twt_network_key = 'twitterStatus';
	public static $experiment_activate_endpoint = 'status';
	public static $experiment_activate_keyword = 'active';
	# Establish class variables.
	var $siteid;

	/**
	 * Initalize the function
	 *
	 * This is a static class, use this method to call it.
	 */
  public static function init() {
		static $instance;

		if ( ! is_a( $instance, 'Naytev_Transports' ) ) {
        	$instance = new self();
        }

        return $instance;

  }

	private function __construct() {
		$this->embedID = get_option('naytev_embed_id');
		$this->siteID = get_option('naytev_site_id');#'53e77838e4b0a68fb839366f'; #get_option();
		$this->apiKey = get_option('naytev_api_key');#'6JOaLZbCsNhmahcwujs3ZyKSdANZyzNU'; #get_option();
		add_action( 'wp_ajax_get_naytev_stats_via_ajax', array($this, 'get_naytev_stats_via_ajax'));
		add_action( 'wp_ajax_nopriv_get_naytev_stats_via_ajax', array($this, 'get_naytev_stats_via_ajax'));
		add_action( 'wp_ajax_get_naytev_stat_via_ajax', array($this, 'get_naytev_stat_via_ajax'));
		add_action( 'wp_ajax_naytev_variant_status_is', array($this, 'naytev_variant_status_is'));
		add_action( 'wp_ajax_nopriv_get_naytev_stat_via_ajax', array($this, 'get_naytev_stat_via_ajax'));
		add_action( 'wp_ajax_nopriv_naytev_variant_status_is', array($this, 'naytev_variant_status_is'));

  }

	public function base_url(){
		if (WP_DEBUG){
			$api_url = self::$base_stage_api_url;
		} else {
			$api_url = self::$base_api_url;
		}
		$path = wp_naytev()->build_path(array($api_url, self::$api_ver, self::$site_endpoint, $this->siteID),false,true);
		#return substr($path, 0, -1);
		return $path;
	}

	public function experiment_url(){
		$e_url = wp_naytev()->build_path(array(self::base_url(), self::$experiment_endpoint),false,true);
		return $e_url;
	}

	public function variant_url($experiment_id){
		$v_url = wp_naytev()->build_path(array(self::experiment_url(), $experiment_id, self::$variant_endpoint),false,true);
		return $v_url;
	}

	public function experiment_activate_url($experiment_id){
		$ea_url = wp_naytev()->build_path(array(self::experiment_url(), $experiment_id, self::$experiment_activate_endpoint),false,true);
		return $ea_url;
	}

	public function stats_url($variantid){
		$ea_url = wp_naytev()->build_path(array(self::base_url(), self::$variant_endpoint, $variantid, self::$stats_endpoint),false,true);
		return $ea_url;
	}

	public function status_url($variantid){
		$status_url = wp_naytev()->build_path(array(self::base_url(), self::$variant_endpoint, $variantid, self::$status_endpoint), false, true);
		return $status_url;
	}

	public function header_settings(){
		return array("headers" => array("Naytev-Key" => $this->apiKey, "Content-Type" => "application/json", "Naytev-Platform" => "WordPress", "Naytev-Platform-Version" => WP_NAYTEV_VER));
	}

	public function get_response_id($response){
		$parsed_response = wp_naytev()->json->take($response);
		#var_dump($parsed_response);
		$id = $parsed_response->id;
		return $id;
	}

	public function get_response_variant_id($response){
		$parsed_response = wp_naytev()->json->take($response);
		#var_dump($response); die();
		$id = $parsed_response->id;
		return $id;
	}

	public function get_response_status($response){
		$parsed_response = wp_naytev()->json->take($response);
		return $parsed_response->status;
	}

	public function naytev_put($url, $args){
		#var_dump($url); die();
		return wp_naytev()->json->post($url, $args, $this->header_settings());
	}

	public function naytev_get($url, $args = array()){

		return wp_naytev()->json->post($url, $args, $this->header_settings(), true);
	}

	public function naytev_patch($url, $args = array()){
		$headers = $this->header_settings();
		$headers['method'] = 'PATCH';
		return wp_naytev()->json->post($url, $args, $headers);
	}

	# The API can technically take more than one experiment at once, but we are not supporting that yet.
	# Eventually there will be a put_experiments function.
	public function put_experiment($name, $target){
		$put = array(
			"name" 			=> $name,
			"targets"		=> array(
								array(
									"url"		=> $target,
									"type"	=> "simple"
								)
			)
		);
		#var_dump($this->naytev_put($this->experiment_url(), $put)); die();
		return $this->naytev_put($this->experiment_url(), $put);
	}

	public function put_variant( $variant, $experiment_id, $network ){
		$put = array(
			"network"	=>	$network
		);
		$checked = true;
		switch ($network){
			case "twitter":
				$network_key = self::$twt_network_key;
				$ready_variant = array(
					"text"	=> $variant["twt_text"]
				);
				if (empty($ready_variant["text"])){
					$checked = false;
				}
				break;
			case "facebook":
				$network_key = self::$fb_network_key;
				$ready_variant = array(
					"name"				=> $variant["fb_title"],
					"image"				=> $variant["fb_image__uploadfield"],
					"description"	    => $variant["fb_description"],
					"caption"			=> $variant["fb_caption"],
				);
				#var_dump($variant); die();
				if (empty($ready_variant['name'])){
					$checked = false;
				}
				break;
			default:
				$network_key = self::$twt_network_key;
				$ready_variant = $variant;
				break;
		}
		# needs per network normalizer.
		if ($checked){
			$put[$network_key] = $ready_variant;			
			return $this->send_variant_fields_to_api($this->variant_url($experiment_id), $put);
		} else {
			return false;
		}
	}

	public function put_experiment_active($experiment_id, $activate = true){
		if ($activate){
			#var_dump($this->experiment_activate_url($experiment_id));
			return $this->naytev_patch($this->experiment_activate_url($experiment_id), array("status" => self::$experiment_activate_keyword));
		} else {
			return false;
		}
	}

	public function send_variant_fields_to_api($url, $variant){
		if (empty($variant['variant_id'])){
			$result = $this->naytev_put($url, $variant);
			$v_id = $this->get_response_variant_id($result);
			return $v_id;
		} else {
			return false;
		}
	}

	public function get_stats($varientid){
		$statJSON = $this->naytev_get(self::stats_url($varientid));
		#return $statJSON;
		#var_dump($statJSON);
		$statObj = wp_naytev()->json->take($statJSON);
		if(is_object($statObj)){
			$statObj->stats->statusOf = $statObj->status;
			return $statObj->stats;
		} else {
			return null;
		}
	}

	public function get_stat($varientid, $stat = 'clicks'){

		$obj = self::get_stats($varientid);
		switch ($stat){
			case 'plays':
				$num = $obj->plays;
				break;
			case 'bails':
				$num = $obj->bails;
				break;
			case 'clicks':
				$num = $obj->clicks;
				break;
			case 'retweets':
				$num = $obj->retweets;
				break;
			case 'favorites':
				$num = $obj->favorites;
				break;
			case 'totalInteractions':
				$num = $obj->totalInteractions;
				break;
			case 'clicksPerShare':
				$num = $obj->clicksPerShare;
				break;
			}
			return $num;
	}

	public function get_the_object_value_or_not($obj, $val){
		if(isset($obj->$val)){
			return $obj->$val;
		} else {
			return 0;
		}
	}

	public function get_status($variantid){
		$statusJSON = $this->naytev_patch(self::status_url($variantid));
		$statusObj = wp_naytev()->json->take($statusJSON);
		return $statusObj->status;
	}

	public function set_status($variantid, $statusArray){
		$statusJSON = $this->naytev_patch(self::status_url($variantid), $statusArray);
		$statusObj = wp_naytev()->json->take($statusJSON);
		return $statusObj->status;
	}

	# AJAX Functions

	public function get_naytev_stats_via_ajax(){
		ob_start();
		$variant = $_POST['naytev_variant_id'];
		$userObj = wp_get_current_user();
		$user_id = $userObj->ID;
		$statsObj = self::get_stats($variant);
		//var_dump($statsObj);
		if (null != $statsObj){
		$response = array(
			'what' => 'naytev_stats',
			'action' => 'get_naytev_stats_via_ajax',
			'id' => $variant,
			'data' => wp_naytev()->json->create($statsObj),
			'supplemental' => array(
					'user' => $user_id,
					'buffered' => ob_get_contents(),
					'plays'	=> self::get_the_object_value_or_not($statsObj,'plays'),
					'bails'	=> self::get_the_object_value_or_not($statsObj,'bails'),
					'clicks'	=> self::get_the_object_value_or_not($statsObj,'clicks'),
					'retweets'	=> self::get_the_object_value_or_not($statsObj,'retweets'),
					'favorites'	=> self::get_the_object_value_or_not($statsObj,'favorites'),
					'totalInteractions'	=> self::get_the_object_value_or_not($statsObj,'totalInteractions'),
					'clicksPerShare'	=> self::get_the_object_value_or_not($statsObj,'clicksPerShare'),
					'statusOf'				=>	self::get_the_object_value_or_not($statsObj,'statusOf')
				)
			);
		} else {
			$response = array(
				'what' => 'naytev_stats',
				'action' => 'get_naytev_stats_via_ajax',
				'id' => $variant,
				'data' => null,
				'supplemental' => array(
						'user' => $user_id,
						'buffered' => ob_get_contents()
					)
				);
		}

		$xmlResponse = new WP_Ajax_Response($response);
		$xmlResponse->send();
		ob_end_flush();
		die();
	}

	public function get_naytev_stat_via_ajax(){
		ob_start();
		$variant = $_POST['naytev_variant_id'];
		$stat = $_POST['naytev_stat'];
		$userObj = wp_get_current_user();
		$user_id = $userObj->ID;
		$stat_result = self::get_stat($variant, $stat);
		$response = array(
			'what' => 'naytev_stat',
			'action' => 'get_naytev_stat_via_ajax',
			'id' => $variant,
			'data' => $stat_result,
			'supplemental' => array(
					'user' => $user_id,
					'buffered' => ob_get_contents()
				)
			);

		$xmlResponse = new WP_Ajax_Response($response);
		$xmlResponse->send();
		ob_end_flush();
		die();
	}

	public function naytev_variant_status_is(){
		ob_start();
		$variant = $_POST['naytev_variant_id'];

		$userObj = wp_get_current_user();
		$user_id = $userObj->ID;
		if (isset($_POST['naytev_status_set'])){
			$variant_status = $_POST['naytev_status_set'];
			$statusArray = array('status' => $variant_status);
			$status_result = self::set_status($variant, $statusArray);
		} else {
			$status_result = self::get_status($variant);
		}
		$response = array(
			'what' => 'naytev_status',
			'action' => 'get_naytev_status_via_ajax',
			'id' => $variant,
			'data' => $status_result,
			'supplemental' => array(
					'user' => $user_id,
					'buffered' => ob_get_contents(),
					'url'	=>	self::status_url($variant)
				)
			);

		$xmlResponse = new WP_Ajax_Response($response);
		$xmlResponse->send();
		ob_end_flush();
		die();
	}



}
