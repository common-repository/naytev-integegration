<?php

/**
 * A generalized class for creating post metaboxes of different types.
 *
 * @since 2.0.0
 * @access private
 *
 */
class Naytev_Meta_Boxes {

	# This is not yet a static class, Don't use this.
	public function init() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}

	public function __construct() {

        #Stuff
        add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts' ));
    }


    /**
     * Standardize your argument for the meta box.
		 *
		 * This function takes an array with named keys and attempts to
		 * put it into a standard form, logging those elements that are
		 * not in place. It also prepends the argument meta_slug with the
		 * project's slug to avoid conflicts with others extending this class.
		 *
		 * @since 2.0.0
		 *
		 * @param array $arg {
		 *
     * 		Array should contain all the arguments needed to describe a metabox.
		 *
		 * 		@type string 	$meta_slug string, slug to save post_meta. Can be shared across boxes
		 *		@type string 	$label string The user-visible label attached to the entry field.
     * 		@type string 	$field_name The HTML for= and name= value.
     * 		@type string 	$input Field type, supports 'text', 'date', 'repeating_text_group'
     * 		@type array 	$post_type Qualified post types.
     * 		@type int 		$size Field size, should be count for repeating groups.
     * 		@type string	$the_field Override any auto-generation with manual HTML. Useful if $input type is unsupported. Optional.
		 * 		@type array  	$repeated_fields Array of fields. Key is Title, value is slug (this setup is stupid, I know). Optional.
		 * 		@type array 	$repeated_fields_defaults Array of defaults Key is slug, value is default. Optional.
     * }
     */
    public function build_meta_box_argument($arg = array()){
        $arg = self::meta_box_default_parser($arg);
				extract($arg);

		#if (!$post_type) { $post_type = wp_naytev()->schema->post_type; }
        $args = array(
            'meta_slug'    							=> $meta_slug,
            'label'         						=> $label,
            'field_name'    						=> $field_name,
            'input'         						=> $input,
						'descript'									=> $descript,
            'post_type'     						=> $post_type,
            'size'          						=> $size,
            'the_field'     						=> $the_field,
						'repeated_fields' 					=> $repeated_fields,
						'repeated_fields_defaults'	=> $repeated_fields_defaults
        );
        foreach ($args as $key=>$arg){
            if (!$arg && ('the_field' != $key)){
                wp_naytev()->util->logger('The ' . wp_naytev()->title . ' meta box field ' . $key . ' was left unset.');
            }
        }
        #$args['field_name'] = WP_NAYTEV_SLUG . '_' . $args['field_name'];
				$args['meta_slug'] = wp_naytev()->slug . '_' . $args['meta_slug'];
        return $args;
    }

		/**
		 * Provides default metabox values where needed.
		 *
		 * You don't need to use this.
		 *
		 */
    public function meta_box_default_parser($args){
         $default = array(
                'meta_slug'     						=> false,
                'label'         						=> wp_naytev()->title . ' Meta Field',
                'field_name'    						=> false,
                'input'         						=> 'text',
                'descript'      						=> "",
                'post_type'     						=> array('post', 'page'),
                'size'          						=> 25,
                'the_field'     						=> '',
				'repeated_fields' 						=> array(),
				'repeated_fields_defaults'  			=> array()

        );
        #var_dump($args); die(0);
        $args = wp_parse_args( $args, $default );

        return $args;

    }

		/**
		 * Provides meta_slug.
		 *
		 * Should be depreciated, but haven't gotten around to it.
		 *
		 */
    public function meta_slug($args){

        return $args['meta_slug'];

    }

		/**
		 * Provides meta_box name from standardized arguments
		 *
		 * This finds the argument for the metabox's name
		 * and prepends it with the slug to avoid namespace
		 * conflicts. It has a weird name.
		 *
		 */

    public function meta_box_box_name($args){

        return $args['meta_slug'] . '_' . $args['field_name'] . '_box';

    }

		/**
		 * Provides the nonce name for the metabox.
		 *
		 * WordPress uses nonces to secure submitted data.
		 * It needs to know the name of the nonce to secure it correctly.
		 * This generates a name via a standard method from the meta box
		 * arguments.
		 */

    public function meta_box_nonce_name($args){
		#var_dump($args['meta_slug']);
        return $args['meta_slug'] . '_' . $args['field_name'] . '_box_nonce';

    }

		/**
		 * Get setting saved into a repeater group.
		 *
		 * Allows you to get data out of a repeater group's saved post_option.
		 * You can get the whole option, the subset option set or a member
		 * of the subset option set.
		 *
		 * @since 2.0.0
		 *
		 * @param int $id The ID of the post you want to pull.
		 * @param array $args The arguments to specifiy the post_meta you want.
		 * @param array $default The defaults to return if nothing is found.
		 *
		 */
    public function repeater_group_setting($id, $args, $default = array()){
          # Once we're sure that we've enforced singleton, we'll take care of it that way.
          if (empty($current_metadata)){
            $settings = get_post_meta($id, $args['meta_slug'], true );
			#var_dump($settings);die();
          } else {
		  			$settings = $current_metadata;
		  		}
        if (empty($settings)) {
			$r = array();


        } elseif (empty($settings[$args['field_name']])){
            $r = '';
        } elseif (!empty($args['field_name'])){
            $r = $settings[$args['field_name']];
        } elseif (!empty($settings)) {
            $r = $settings;
        } else {
          $r = '';
        }

        if (empty($r)){
            #$default = array($args['parent_element'] => array($args['element'] => ''));
			#var_dump($default); die();
            return $default;
        } else {
						$r['default'] = $default;
            return $r;
        }
    }

	public function is_this_valid_for_post_type($post_type, $arg_post_type){
		if (in_array($post_type, (array) $arg_post_type)){
			return true;
		} else {
			return false;
		}
	}

		/**
		 * Get post meta out of a repeater group.
		 *
		 * More selective than its little brother repeater_group_setting.
		 *
		 * @since 2.0.0
		 * @see repeater_group_setting
		 *
		 */
    public function get_repeater_group_setting($id, $args, $default = array()){
          # Once we're sure that we've enforced singleton, we'll take care of it that way.
				$r = '';
		 		$default_args = array(
                'meta_slug'     			=> false,
                'field_name'       			=> false,
                'count'    					=> false,
                'sub_field'        			=> false

        );
        #var_dump($args); die(0);
        $args = wp_parse_args( $args, $default_args );
				if (empty($args) || empty($args['meta_slug'])){
					return false;
				}
				if (empty($current_metadata)){
		            $current_metadata = get_post_meta($id, self::meta_slug($args), true );
		    }

		    if (empty($current_metadata)) {
					$r = array();

				} elseif(empty($args['field_name'])){
						$r = $current_metadata;
		    } elseif (!empty($args['count']) && !empty($args['field_name']) && empty($args['sub_field'])) {
						if (!empty($current_metadata[$args['field_name']]['element-num-'.$args['count']])){
							$r = $current_metadata[$args['field_name']]['element-num-'.$args['count']];
						}
				} elseif(!empty($args['field_name']) && empty($args['count'])){
						if (!empty($current_metadata[$args['field_name']])){
							$r = $current_metadata[$args['field_name']];
							#var_dump($r); die();
						}
				} elseif (empty($current_metadata[$args['field_name']]) || empty($current_metadata[$args['field_name']]['element-num-'.$args['count']]) || empty($current_metadata[$args['field_name']]['element-num-'.$args['count']][$args['field_name']])){
		            $r = '';
		    } elseif (!empty($args['field_name']) && !empty($args['count']) && !empty($args['field'])){ #remember false == empty
		            $r = $current_metadata[$args['field_name']]['element-num-'.$args['count']][$args['field']];
		    }
				#var_dump($r); die();
		    if (empty($r)){
		      #$default = array($args['parent_element'] => array($args['element'] => ''));
		      return $default;
		    } else {
		      return $r;
		    }
    }

	/**
	 * Make post meta boxes.
	 */
	public function meta_box_maker($post, $metabox){
        global $post;
        $theseArgs = self::meta_box_default_parser($metabox['args']);
        #var_dump($theseArgs);
        if (self::is_this_valid_for_post_type($post->post_type, $theseArgs['post_type'])){

            $current_metadata = get_post_meta( $post->ID, self::meta_slug($theseArgs), true );

            wp_nonce_field( self::meta_box_box_name($theseArgs), self::meta_box_nonce_name($theseArgs) );

           printf('<label for="%1$s">%2$s</label>', $theseArgs['field_name'], $theseArgs['label']);
           switch ($theseArgs['input']){
			   case 'text':
					#var_dump(self::get_repeater_group_setting($post->ID, $theseArgs)); die();
					printf('<input type="text" id="%1$s" name="%1$s[%2$s]" value="%3$s" size="%4$u" />',
							self::meta_slug($theseArgs),
							$theseArgs['field_name'],
							esc_attr(self::get_repeater_group_setting($post->ID, $theseArgs, '')),
							$theseArgs['size']
						   );
			   		break;
			   case 'date':
			   		# from: https://github.com/Automattic/Edit-Flow/blob/master/modules/editorial-metadata/editorial-metadata.php
						// TODO: Move this to a function
						if ( !empty( $current_metadata ) ) {
							// Turn timestamp into a human-readable date
							$current_metadata = $this->show_date( intval( $current_metadata ) );
						}
						if ( !empty($theseArgs['descript']) )
				            echo '<label for="'.$theseArgs['field_name'].'"></label>';
						echo '<input id="'.$theseArgs['field_name'].'" class="'.$theseArgs['input'].' date-time-pick-zs-util" name="'.$theseArgs['field_name'].'" type="text" value="'.$current_metadata.'" />';
                        echo '<br />'.$theseArgs['descript'];
						break;
				case 'repeating_text_group':
					# Should be an array with the class name and the function that defines the fields.
			   		#var_dump($theseArgs);
			   		$fields = $theseArgs['repeated_fields'];
			   		$meta_slug = self::meta_slug($theseArgs);
			   		$default = $theseArgs['repeated_fields_defaults'];
					$parent_element = self::meta_slug($theseArgs);
					$element = $theseArgs['field_name'];
					$c = 0;
			   		#var_dump($theseArgs); die();
					$group = self::repeater_group_setting($post->ID, $theseArgs, $default);
			   		#echo '<pre>'; var_dump($group); die();
					if (count($group) > 1){
						unset($group['default']);
					}
					?>
					<ul class="naytev repeater-container" for="repeat-element-<?php echo $parent_element; ?>-<?php echo $element; ?>" id="repeater-<?php echo $parent_element; echo '-'; echo $element; ?>">
						<?php
						foreach ($group as $variant){
							if ($c > 0) { $id_c = '-'.$c; } else { $id_c = ''; }
							if ((!array_filter($variant)) && ($c > 0)){ continue; }
						?>
							<li class="repeat-element repeat-element-<?php echo $element; echo ' '; echo $element; echo ' '; echo $parent_element; ?> " id="repeat-element-<?php echo $parent_element.'-'.$element . $id_c; ?>">
								<h3 class="repeat-meta"><span class="expandState">- </span>Variant: <span class="heading-content"></span></h3>
								<?php

									foreach ($fields as $f_label => $field){
										#$default = array_flip($default);
										#var_dump($default[0][$field]); die();
										self::the_repeater_field($meta_slug, $f_label, $field, $parent_element, $element, $c, $variant, $default[0][$field]);
									}

								if ($c>0){
								  echo '<a class="repeat-element-remover" href="#">Remove</a><br /><br />';
								} else {
								  echo '<a class="repeat-element-remover" style="display:none;" href="#">Remove</a><br /><br />';
								}
								?>
							</li>
						<?php
						$c++;

						}
						echo '<input type="hidden" class="repeater_count" id="counter-for-repeat-element-'.$parent_element.'-'.$element.'" name="element-max-id-'.$parent_element.'-'.$element.'" value="'.$c.'">';
						?>
						<a href="#" class="add-repeater">Add another variant.</a>
					</ul>
					<?php
					break;
				default:
					echo $theseArgs['the_field'];

            }
        }


    }

	/**
	 * Make meta box fields for repeater sets.
	 */
	public function the_repeater_field($meta_slug, $f_label, $field, $parent_element, $element, $c, $variant, $default){
		if (strpos($field, '__uploadfield') !== FALSE){
			self::the_repeater_upload_field($meta_slug, $f_label, $field, $parent_element, $element, $c, $variant, $default);
		} else {
			self::the_repeater_text_field($meta_slug, $f_label, $field, $parent_element, $element, $c, $variant, $default);
		}
	}

	/**
	 * Return the field designation for a repeater set field.
	 */
	public function get_the_field($parent_element,$element,$c,$field,$meta_slug){

		return $parent_element.'['.$element.'][element-num-'.$c.']['.$field.']';

	}

	/**
	 * Echo the field designation for a repeater set field.
	 */
	public function the_field($parent_element,$element,$c,$field,$meta_slug){

		echo self::get_the_field($parent_element,$element,$c,$field,$meta_slug);

	}

	/**
	 * Generate a repeater set upload field.
	 */
	public function the_repeater_upload_field($meta_slug, $f_label, $field, $parent_element, $element, $c, $variant, $default){
		$value = '';
		if (isset($variant[$field])){
			$value = esc_attr($variant[$field]);
		}
		?>
		<br /><input class="<?php echo $field . " "; echo $meta_slug; ?>_img_uploader naytev_img_upload_field regular-text" type="text" wpn_subfield="<?php echo $field; ?>" name="<?php  self::the_field($parent_element,$element,$c,$field,$meta_slug); ?>" value="<?php echo $value; ?>" placeholder="URL link here, or use the upload button." />
        <br />
		<div class="input_box <?php echo $meta_slug; ?>_img_uploader">
			<a class="button-primary naytev_img_upload naytev_img_uploader" ><?php _e('Upload Image', 'naytev'); ?></a>
			<label class="description <?php echo $meta_slug; ?>_img_uploader" for="<?php  self::the_field($parent_element,$element,$c,$field,$meta_slug); ?>"><?php _e('*Upload image here.', 'naytev'); ?></label>
		</div>
		<?php
	}

	/**
	 * Generate a repeater set text field.
	 */
	public function the_repeater_text_field($meta_slug, $f_label, $field = '', $parent_element, $element, $c, $variant = array(), $default){
		#var_dump($variant); die();
		$value = '';
		if (isset($variant[$field])){
			$value = esc_attr($variant[$field]);
		}
		#var_dump($variant);
		$placeholder = $default;
		if (empty($default)){
			$placeholder = $f_label;
		}
		echo '<br /><label class="'.$field.' '.$parent_element.'" for="'.self::get_the_field($parent_element,$element,$c,$field,$meta_slug).'">' . $f_label . '</label><br />
					<input class="'.$field.' '.$parent_element.'" wpn_subfield="'.$field.'" type="text" name="'.self::get_the_field($parent_element,$element,$c,$field,$meta_slug).'" value="'.$value.'" placeholder="'.$placeholder.'" />';
	}

	/**
	 * Generate a date for frontend.
	 */
	private function show_date( $current_date ) {

		  return date('m/d/Y', $current_date);

	}

	/**
	 * Verify meta_box data.
	 *
	 * Verify meta_box data sent on user save
	 * as being both valid and secure.
	 */
    public function meta_box_checker($post_id, $args){
        #var_dump($post_id); die();
        $args = self::meta_box_default_parser($args);
        #var_dump($_POST['wp_naytev_variant_meta']); die();

        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST[self::meta_box_nonce_name($args)] ) ) {
            wp_naytev()->util->logger('Nonce is not set.');
            return false;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST[self::meta_box_nonce_name($args)], self::meta_box_box_name($args) ) ) {
            wp_naytev()->util->logger('Nonce is not valid.');
            return false;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return false;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && $args['post_type'] == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                wp_naytev()->util->logger('User cannot edit_page.');
                return false;
            }

        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                wp_naytev()->util->logger('User cannot edit_post.');
                return false;
            }
        }

        /* OK, it's safe for us to save the data now. */

        // Make sure that it is set.
        if ( ! isset( $_POST[$args['meta_slug']] ) ) {
            wp_naytev()->util->logger('field_name is not set.');
            return false;
        }

        $data = $_POST[$args['meta_slug']];
        #var_dump($data); die();
        if ('text' == $args['input']){
            // Sanitize user input.
            $data = sanitize_text_field( $data );
        }

        if ('date' == $args['input']){
            $unix_date = strtotime($data);
            $data = $unix_date;
        }

        return $data;

    }

		/**
		 * Is that value empty?
		 */
		public function check_meta_for_empty($data, $key, $parent){
			if (is_array($data)){
				foreach($data as $datum){
					$parent = $this->check_meta_for_empty($datum, $key, $data);
				}
			} else {
				if (empty($data)){
					unset($parent[$key]);
				}
			}
			return $parent;
		}

		/**
		 * Save meta box value.
		 */
		public function meta_box_updater($post_id, $args, $data){
			// Update the meta field in the database.
			//foreach ($data as $key => $datum){
			//	$data = $this->check_meta_for_empty($datum, $key, $data);
			//}
			$r = update_post_meta( $post_id, self::meta_slug($args), $data );
			return $r;
		}

		/**
		 * Hook scripts needed for meta boxes.
		 */
    public function admin_scripts(){
        $screen = get_current_screen();
        #var_dump($screen); die();
        #wp_register_script('naytev-datepicker', SB_NAYTEV_PLUGIN_URL . 'assets/js/datepicker-imp.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'));
        wp_register_style('jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.0/themes/smoothness/jquery-ui.css');
        if (('post' || 'edit' ) == $screen->base){
            wp_enqueue_style('jquery-ui-style');
            wp_enqueue_script('jquery-ui-core');
            #wp_enqueue_script('jquery-ui-datepicker');
            #wp_enqueue_script('naytev-datepicker');

        }

    }

}
