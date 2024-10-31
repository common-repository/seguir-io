<?php
/**
 * @package Seguirio_Widget
 * @version 1.7.2
 */
/*
Plugin Name: Seguir.io
Plugin URI: https://seguir.io/
Description: Add a widget with your Instagram into your site. The widget displays your username, number of followers and button to follow your Instagram.
Author: seguir.io
Text Domain: seguir-io
Domain Path: /languages
Version: 1.0.1
*/

class Seguirio_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'seguirio-widget',
			__( 'Seguir.io Widget', 'seguir-io' ),
			array(
				'customize_selective_refresh' => true,
				'description' => __('Add a widget with your Instagram into your site.', 'seguir-io')
			)
		);
	}

	public function form($instance) {	
		$defaults = array(
			'username' => '',
			'width' => '',
			'theme' => '',
		);
	
    extract(wp_parse_args((array) $instance, $defaults));
    ?>

    <p>
      <label for="<?php echo esc_attr($this->get_field_id('username')); ?>"><?php _e('@ Instagram', 'seguir-io'); ?>:</label>
      <input class="widefat" id="<?php echo esc_attr($this->get_field_id('username')); ?>" name="<?php echo esc_attr($this->get_field_name('username')); ?>" type="text" value="<?php echo esc_attr($username); ?>" />
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width', 'seguir-io' ); ?>:</label>
      <select name="<?php echo $this->get_field_name('width'); ?>" id="<?php echo $this->get_field_id('width'); ?>" class="widefat">
      <?php
      $options = array(
        '326px' => __( 'Default (326px)', 'seguir-io' ),
        '100%' => __( 'Responsive (100%)', 'seguir-io' ),
      );
      foreach ($options as $key => $name) {
        echo '<option value="'.esc_attr($key).'" id="'.esc_attr($key).'" '.selected($width, $key, false).'>'.$name.'</option>';
      }
      ?>
      </select>
    </p>

    <p>
      <label for="<?php echo $this->get_field_id('theme'); ?>"><?php _e('Theme', 'seguir-io' ); ?>:</label>
      <select name="<?php echo $this->get_field_name('theme'); ?>" id="<?php echo $this->get_field_id('theme'); ?>" class="widefat">
      <?php
      $options = array(
        'light' => __( 'Default', 'seguir-io' ),
        'dark' => __( 'Dark', 'seguir-io' ),
      );
      foreach ($options as $key => $name) {
        echo '<option value="'.esc_attr($key).'" id="'.esc_attr($key).'" '.selected($theme, $key, false).'>'.$name.'</option>';
      }
      ?>
      </select>
    </p>
  <?php
  }

  public function update($new_instance, $old_instance) {
    $instance = $old_instance;
    $instance['username'] = !empty($new_instance['username']) ? wp_strip_all_tags($new_instance['username']) : '';
    $instance['width'] = !empty($new_instance['width']) ? wp_strip_all_tags($new_instance['width']) : '';
    $instance['theme'] = !empty($new_instance['theme']) ? wp_strip_all_tags($new_instance['theme']) : '';
    return $instance;
  }

	public function widget($args, $instance) {
	  extract($args);
    $username = !empty($instance['username']) ? $instance['username'] : '';
    $width = !empty($instance['width']) ? $instance['width'] : '326px';
    $theme = !empty($instance['theme']) ? $instance['theme'] : 'light';
    $div = "sg-io-wg-wordpress-".random_int(0, 10000);
    wp_enqueue_script('seguirio-widget', 'https://seguir.io/w.js');
    wp_add_inline_script('seguirio-widget', 'sgio("init", { username: "'.$username.'", div: "'.$div.'", width: "'.$width.'", theme: "'.$theme.'" });');

    echo $before_widget;
    echo '<div class="widget-text wp_widget_plugin_box">';
    echo '<div id="'.$div.'"></div>';
    echo '</div>';
    echo $after_widget;
	}
}

function seguirio_register_widget() {
	register_widget('Seguirio_Widget');
}

add_action('widgets_init', 'seguirio_register_widget');

function seguirio_widget_add_shortcode($a) {
	$atts = shortcode_atts(array(
		'username' => '',
		'width' => '326px',
		'theme' => 'light'
	), $a);
	
	if (!$atts['username']) {
		return null;
  }
  
  $div = "sg-io-wg-wordpress-".random_int(0, 10000);
  wp_enqueue_script('seguirio-widget', 'https://seguir.io/w.js');
  wp_add_inline_script('seguirio-widget', 'sgio("init", { username: "'.$atts['username'].'", div: "'.$div.'", width: "'.$atts['width'].'", theme: "'.$atts['theme'].'" });');
	return '<div id="'.$div.'"></div>';
}

add_shortcode('seguirio-widget', 'seguirio_widget_add_shortcode');

function seguirio_plugin_init() {
  load_plugin_textdomain('seguir-io', false, dirname(plugin_basename(__FILE__)).'/languages/');
}

add_action('plugins_loaded', 'seguirio_plugin_init');
 