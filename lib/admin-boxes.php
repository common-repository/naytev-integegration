<?php
/**
 * A class to generate admin settings fields.
 *
 * @todo Switch to using this.
 */
class Naytev_Admin_Boxes {

	var $naytev_options;

	public function init() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new self;
		}

		return $instance;
	}

	public function __construct() {
		$this->option_name = 'naytev_options';
        #Stuff
		add_action( 'admin_enqueue_scripts', array( $this, 'add_admin_scripts' ) );
    }

    public function setting($args, $default = array()){
          # Once we're sure that we've enforced singleton, we'll take care of it that way.
          if (empty($settings)){
            $settings = get_option( $this->option_name, array() );
          }
        if (empty($settings)) {



        } elseif (empty($settings[$args['parent_element']]) || empty($settings[$args['parent_element']][$args['element']])){
            $r = '';
        } elseif (!empty($args['parent_element']) && !empty($args['element'])){
            $r = $settings[$args['parent_element']][$args['element']];
        } elseif (!empty($args['parent_element'])) {
            $r = $settings[$args['parent_element']];
        } else {
          $r = '';
        }

        if (empty($r)){
            #$default = array($args['parent_element'] => array($args['element'] => ''));
            return $default;
        } else {
            return $r;
        }
    }

    # Method from http://wordpress.stackexchange.com/questions/21256/how-to-pass-arguments-from-add-settings-field-to-the-callback-function
    public function option_generator($args){
       #echo '<pre>'; var_dump($args); echo '</pre>';  return;
      $parent_element = $args['parent_element'];
      $element = $args['element'];
      $type = $args['type'];
      $label = $args['label_for'];
      $default = $args['default'];
      switch ($type) {
          case 'checkbox':
            $check = self::setting($args, $default);
            if ('true' == $check){
                $mark = 'checked';
            } else {
                $mark = '';
            }
            echo '<input type="checkbox" name="settings['.$parent_element.']['.$element.']" value="true" '.$mark.' class="'.$args['parent_element'].' '.$args['element'].'" />  <label for="settings['.$parent_element.']['.$element.']" class="'.$args['parent_element'].' '.$args['element'].'" >' . $label . '</label>';
            break;
          case 'text':
            echo "<input type='text' name='settings[".$parent_element."][".$element."]' value='".esc_attr(self::setting($args, $default))."' class='".$args['parent_element']." ".$args['element']."' /> <label for='settings[".$parent_element."][".$element."]' class='".$args['parent_element']." ".$args['element']."' >" . $label . "</label>";
            break;
          case 'select':
            $field = '<select ';
            $field .= 'multiple ';
            $field .= '>';
            if (isset($args['repeated_fields'])){
                foreach ( $args['repeated_fields'] as $key => $selectable){
                    $field .= sprintf ('<option value="%1$s">%2$s</option>',
                                            $key,
                                            $selectable
                                      );
                }
            }
          $field .= '</select>';
          echo $field;
      }

    }

    public function select_a_post_type(){
        $post_types = get_post_types( array('public' => true), 'objects' );
        $select_rdy_array = array();
        foreach($post_types as $post_type_slug => $post_type){
            $select_rdy_array[$post_type_slug] = $post_type->labels->name;
        }
        return $select_rdy_array;
    }

    public function add_admin_scripts($hook){
        global $pagenow;

    }

    public function validator($input){
        $output = get_option( $this->option_name );
        #var_dump($input); die();
        return $input;
    }

}
