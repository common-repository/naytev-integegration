<?php

class Naytev_Post_Metas {

	var $meta_slug;

	public function __construct() {

				$this->meta_slug = 'variant_meta';
        #Stuff
		#add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
				add_action( 'add_meta_boxes', array($this, 'experiment_id_box') );
        add_action( 'add_meta_boxes', array($this, 'social_box_fb') );
        add_action( 'add_meta_boxes', array($this, 'social_box_twt') );
				add_action( 'add_meta_boxes', array($this, 'meta_template_box') );
        #add_action( 'save_post', array($this, 'fb_meta_box_checker') );
        #add_action( 'save_post', array($this, 'twt_meta_box_checker') );
				add_action( 'save_post', array($this, 'save_and_update') );
    }

		public function experiment_id_schema($key = false){
			$args = wp_naytev()->meta_boxes->build_meta_box_argument(array(
					'meta_slug'    				=> $this->meta_slug,
					'label'         			=> 'Naytev Experiment ID: ',
					'field_name'    			=> 'experiment_id',
					'input'         			=> 'text',
					'descript'						=> 'Experiment ID',
					'post_type'     			=> array('post', 'page'),
					'size'          			=> 36,
					'the_field'     			=> array()
				));
				if (!$key){
					return $args;
				}
				return $args[$key];
		}

		function experiment_id_box($post_id){
			$args = self::experiment_id_schema();
					#var_dump($post_id);
					#var_dump($_POST); die();
			foreach($args['post_type'] as $post_type){
				add_meta_box( WP_NAYTEV_SLUG.'_experiment_id_box', 'Naytev Experiment ID', array($this, 'meta_box_maker'), $post_type, 'normal', 'high', $args );
			}
					#wp_naytev()->meta_boxes->meta_box_checker($post_id, $args);
		}

    public function social_box_fb_schema($key = false){
        $args = wp_naytev()->meta_boxes->build_meta_box_argument(array(
            'meta_slug'    				=> $this->meta_slug,
            'label'         			=> 'Facebook Naytev Variants',
            'field_name'    			=> 'fb',
            'input'         			=> 'repeating_text_group',
						'descript'						=> 'Facebook Variants for Naytev testing',
            'post_type'     			=> array('post', 'page'),
            'size'          			=> 4, #4 fields, no need to use this for this mechanism right now.
            'the_field'     			=> array(),
						'repeated_fields' 		=> array(
							'Title'				=>	'fb_title',
							'Caption'			=>	'fb_caption',
							'Description'	=>	'fb_description',
							'Image'				=>	'fb_image__uploadfield',
							'Variant ID'	=>	'variant_id'
						),
						'repeated_fields_defaults'	=> array( 0 => array(
							'fb_title' 							=> 'Post Title',
							'fb_caption'						=> 'Caption here',
							'fb_description'				=> '',
							'fb_image__uploadfield'	=> '',
							'variant_id'						=> ''
							# @todo Replace this with a user option default
						)
			)
		));
        if (!$key){
          return $args;
        }
        return $args[$key];
    }

	function social_box_fb($post_id){
       $args = self::social_box_fb_schema();
        #var_dump($post_id);
        #var_dump($_POST); die();
		foreach($args['post_type'] as $post_type){
			add_meta_box( WP_NAYTEV_SLUG.'_fb_social_box', 'Facebook Variants', array($this, 'meta_box_maker'), $post_type, 'normal', 'high', $args );
		}
        #wp_naytev()->meta_boxes->meta_box_checker($post_id, $args);
	}

    public function social_box_twt_schema($key = false){
        $args = wp_naytev()->meta_boxes->build_meta_box_argument(array(
            'meta_slug'    				=> $this->meta_slug,
            'label'         			=> 'Twitter Naytev Variants',
            'field_name'    			=> 'twt',
            'input'         			=> 'repeating_text_group',
						'descript'						=> 'Twitter Variants for Naytev testing',
            'post_type'     			=> array('post', 'page'),
            'size'          			=> 1,
            'the_field'     			=> array(),
						'repeated_fields' 		=> array(
							'Title'				=>	'twt_text',
							'Variant ID'	=>	'variant_id'
						),
						'repeated_fields_defaults'	=> array( 0 => array(
							'twt_text' 							=> 'Tweet Text',
							'variant_id'						=> ''
							# @todo Replace this with a user option default
						)
						)
				));
        if (!$key){
          return $args;
        }
        return $args[$key];
  }

	function meta_template_box($post_id){
			$args =array('post_type'     			=> array('post', 'page'));
				#var_dump($post_id);
				#var_dump($_POST); die();
		foreach($args['post_type'] as $post_type){
			add_meta_box( WP_NAYTEV_SLUG.'_template_box', 'Variants Templates', array($this, 'template_box'), $post_type, 'normal', 'low', $args );
		}
				#wp_naytev()->meta_boxes->meta_box_checker($post_id, $args);
	}

	function template_box($args){
			$twt = '
				<div class="tw naytev_template" id="download">
						<h5><small>Example Tweet</small></h5>
						<div class="post">
								<div class="img">
										<img src="http://api.randomuser.me/portraits/men/2.jpg" width="50" height="50" id="profile" class="img-rounded">
								</div>
								<div class="name">
										<div class="username"><span id="js-twitter-name" class="text-name" style="text-transform: capitalize;">troy carr</span></div>
										<div class="at">@<span id="js-twitter-handle" class="text-at">crazydog875</span></div>
								</div>
								<div class="follow">
										<img src="http://www.naytev.com/assets/images/twitter/profile_dropdown.jpg" width="42" height="30" alt="profile"><img src="http://www.naytev.com/assets/images/twitter/follow_no.jpg" width="86" height="30" alt="Follow" id="follow-img">
								</div>
								<div class="clearfix"></div>
								<div class="message">
										<span class="text-message"><span class="js-tweetText">What is your social post follow through percentage? Do you even know?</span> <a href="#">nyv.me/l/cstm</a></span>
								</div>
								<ul id="options">
										<li><img src="http://www.naytev.com/assets/images/twitter/reply.jpg" width="22" height="15"> <span class="text-reply">Reply</span></li>
										<li><img src="http://www.naytev.com/assets/images/twitter/retweet.jpg" width="22" height="15"> <span class="text-retweet">Retweet</span></li>
										<li><img src="http://www.naytev.com/assets/images/twitter/favorite.jpg" width="22" height="15"> <span class="text-favorite">Favorite</span></li>
										<li><img src="http://www.naytev.com/assets/images/twitter/more.jpg" width="22" height="15"> <span class="text-more">More</span></li>
								</ul>
								<div class="date">
										<span class="text-date">3:41 PM - 20 Jan 13</span> · <span class="text-embed">Embed this Tweet</span>
								</div>
						</div>

				</div>
			';

			$fb = '
		<div class="fb naytev_template">
				<h5><small>Example Post</small></h5>
				<div class="classic" style="display: block;">

						<div class="previewContainer">

								<div class="topLine"></div>

								<div class="statusPic">

										<img id="js-user-pic" src="http://api.randomuser.me/portraits/women/46.jpg" alt="Fake Facebook Profile Picture">
								</div>
								<div class="statusContent">
										<p class="statusName" id="js-user-name" style="text-transform: capitalize;">peyton brooks</p>
										<p class="facebookMessage">This is the share text users add to their posts in the editor. It is not customizable.</p>

										<p class="statusAddition"></p>
										<div class="image">
												<img class="js-facebookPostImage" src="http://images.naytev.com/53c82126e4b0e8923d866cfb.png">
												<div class="content">
														<p class="title facebookName js-facebookTitle">The Hidden Social Metric: Your sites social post follow through rating.</p>
														<p class="facebookCaption caption js-facebookCaption"></p>
														<p class="facebookDescription description js-facebookDescription">You didn\'t realize how much traffic you are losing.</p>
												</div>
										</div>
										<p class="statusLinks"><span class="statusLikeWord">Like</span> · Comment · <span class="statusDate">Today</span></p>
										<p class="statusComments"> <img src="http://www.naytev.com/assets/images/thumb.png"> <span>Abe Changecause</span> and <span>10 others</span> like this.</p>
										<div class="likeBar">
												<p class="statusLikes"></p>
												<p class="statusOtherLikes"></p>


												<div class="borderLine"></div>

										</div><!-- End likeBar -->


										<div class="allComments" id="allComments">

										</div>

										<div class="write">

												<div class="boxWrap">
														<div class="box">
																<p>Write a comment...</p>
														</div>
												</div>

												<div class="borderLine"></div>

										</div><!-- End write -->

								</div><!-- End statusContent -->

								<div style="clear:both"></div>

						</div><!-- End previewContainer -->

				</div><!-- End classic -->
		</div>
			';
			echo $fb;
			echo $twt;
			return;
	}

	function social_box_twt($post_id){
       $args = self::social_box_twt_schema();
        #var_dump($post_id);
        #var_dump($_POST); die();
			foreach($args['post_type'] as $post_type){
				add_meta_box( WP_NAYTEV_SLUG.'_twt_social_box', 'Twitter Variants', array($this, 'meta_box_maker'), $post_type, 'normal', 'high', $args );
			}
        #wp_naytev()->meta_boxes->meta_box_checker($post_id, $args);
	}

    public function meta_box_maker($post, $args){
        #var_dump('<pre>');
        #var_dump($args);
        #var_dump('bob');
        #die();
         wp_naytev()->meta_boxes->meta_box_maker($post, $args);

    }

    public function fb_meta_box_checker($post_id){
        $args = self::social_box_fb_schema();
        #var_dump($post_id);
        #var_dump($_POST); die();
        $this->meta_updater($post_id, $args);
				return $post_id;
    }

    public function twt_meta_box_checker($post_id){
        $args = self::social_box_twt_schema();
        #var_dump($post_id);
        #var_dump($_POST); die();
				$this->meta_updater($post_id, $args);
				return $post_id;

    }

	public function save_and_update($post_id){
			$args = self::social_box_fb_schema();
			#var_dump($post_id);
			#var_dump($_POST); die();
			$this->meta_updater($post_id, $args);
			return $post_id;

	}

		public function meta_updater($post_id, $args){
			#var_dump($_POST); die();
			$data = wp_naytev()->meta_boxes->meta_box_checker($post_id, $args);
			#var_dump($data); die();
#			var_dump('<pre>');
			if (!empty($data['fb'])){
				foreach ($data['fb'] as $key => $fb){
					$elementState = true;
					$eC = 0;
					foreach ($fb as $field){
						if ((empty($field)) || ($field == '')){
							$eC++;
	#						var_dump($eC);
						}
					}
					if (empty($fb['fb_title']) || ($fb['fb_title'] == 'Post Title') || ($fb['fb_title'] == '')){
						unset($data['fb'][$key]);
						#var_dump($fb); die();
					}
				}
			}
			if (!empty($data['twt'])){
				foreach ($data['twt'] as $key => $twt){
					$elementState = true;
					$eC = 0;
					foreach ($twt as $field){
						if ((empty($field)) || ($field == '')){
							$eC++;
	#						var_dump($eC);
						}
					}
					if (empty($twt['twt_text'])){
						unset($data['twt'][$key]);
	#					var_dump($key);
					}
				}
			}
#			var_dump($data);
#			die();
			if (false != $data){
				$update_result = wp_naytev()->meta_boxes->meta_box_updater($post_id, $args, $data);
				$data = $this->push_to_api_on_publish($data);
				#Update again with variant IDs.
				$update_result = wp_naytev()->meta_boxes->meta_box_updater($post_id, $args, $data);
			}

		}

		public function push_to_api_on_publish($data){
			
			if('publish' == $_POST['post_status']){
				$args = self::experiment_id_schema();
				$data = $_POST[wp_naytev()->meta_boxes->meta_slug($args)];
				$first_variant = false;
				$post_id = $_POST['post_ID'];
				$post_uri = get_permalink($post_id);
				$post_title = $_POST['post_title'];
				#var_dump($data); die();
				$experiment_id_op = false;
				if (!empty($data["experiment_id"])){
					$experiment_id_op = $data['experiment_id'];
					$_POST['naytev_experiment_id'] = $experiment_id_op;
				} elseif (!empty($_POST['naytev_experiment_id'])){
					$experiment_id_op = $_POST["naytev_experiment_id"];
				}
				if (!isset($experiment_id_op) || empty($experiment_id_op) || !$experiment_id_op){
					$result = wp_naytev()->transports->put_experiment($post_title, $post_uri);
					$experiment_id = wp_naytev()->transports->get_response_id($result);
					#var_dump($experiment_id); die();
					$_POST['naytev_experiment_id'] = $experiment_id;
					#update_post_meta( $post_id, 'naytev_experiment_status', $experiment_id );
					$first_variant = true;
				} else {
					$experiment_id = $experiment_id_op;
				}
				if (!empty($data["fb"])){
					foreach ($data["fb"] as $key => $variant){
						if ((empty($variant['variant_id'])) && (!empty($variant['fb_title']))){
							$v_id = wp_naytev()->transports->put_variant($variant, $experiment_id, "facebook");
							if (false != $v_id){
								$data["fb"][$key]['variant_id'] = $v_id;
							}
						}
					}
				}
				if (!empty($data["twt"])){
					foreach ($data["twt"] as $key => $variant){
						
						if ((empty($variant['variant_id'])) && (!empty($variant['twt_text']))){
							$v_id = wp_naytev()->transports->put_variant($variant, $experiment_id, "twitter");
							if (false != $v_id){
								$data["twt"][$key]['variant_id'] = $v_id;
							}
						}
					}
				}
				if ($first_variant){
					$experiment_activate_result = wp_naytev()->transports->put_experiment_active($experiment_id);
					$data['experiment_status'] = wp_naytev()->transports->get_response_status($experiment_activate_result);
				}
				$data['experiment_id'] = $experiment_id;
			}

			$_POST[wp_naytev()->meta_boxes->meta_slug($args)] = $data;
			return $data;
		}

}
