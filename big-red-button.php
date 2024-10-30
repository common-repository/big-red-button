<?php

/*
  Plugin Name: Big Red Button
  Plugin URI: http://www.genvejen.dk/big-red-button/
  Description: A simple widget that displays a big red button. A message will be shown when someone clicks the button.
  Author: Mads Phikamphon
  Version: 1.1
  Author URI: http://www.genvejen.dk/wordpress/
  License: GPLv2
 */

require("/includes/ago.php");

add_action( 'widgets_init', 'mp_brb_register_widgets');

function mp_brb_register_widgets() {
    register_widget('mp_brb_widget_big_red_button');
}

class mp_brb_widget_big_red_button extends WP_Widget {
    
    function mp_brb_widget_big_red_button() {
        // For storing widget options
        $widget_ops = array('classname' => 'mp_brb_widget_class',
            'description' => 'Displays a big red button that can be clicked'
            );
        
        $this->WP_Widget('mp_brb_widget_big_red_button', 'Big Red Button', $widget_ops);
    }
    
    // For displaying the administration form
    function form($instance) {
        $defaults = array('title' => 'Big Red Button', 'above_button_text' => 'Press the button if you dare!', 
            'below_button_text' => 'You pressed the button...', 'display_time_since_last_click' => 'on',
            'display_time_text' => 'It\'s been %TIME% since the button was last clicked');
        $instance = wp_parse_args((array) $instance, $defaults);
        
        $title = $instance['title'];
        $above_button_text = $instance['above_button_text'];
        $below_button_text = $instance['below_button_text'];
        $display_time_since_last_click = $instance['display_time_since_last_click'];
        $display_time_text = $instance['display_time_text'];
        ?>
            <p>Title: <input class="widefat" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
            <p>Text above the button: <input class="widefat" name="<?php echo $this->get_field_name('above_button_text'); ?>" type="text" value="<?php echo esc_attr($above_button_text); ?>" /></p>
            <p>Text below the button: <input class="widefat" name="<?php echo $this->get_field_name('below_button_text'); ?>" type="text" value="<?php echo esc_attr($below_button_text); ?>" /></p>
            <p><input class="checkbox" type="checkbox" <?php checked( $instance['display_time_since_last_click'], "on" ); ?> name="<?php echo $this->get_field_name( 'display_time_since_last_click' ); ?>" /> 
                <label for="<?php echo $this->get_field_name( 'display_time_since_last_click' ); ?>">Show time since last button click?</label>
		</p>
            <p>Text when showing time: <input class="widefat" name="<?php echo $this->get_field_name('display_time_text'); ?>" type="text" value="<?php echo esc_attr($display_time_text); ?>" />
                <label>Code for showing the time: %TIME% </label>
                </p>
        <?php
    }
    
    // For updating the widget options
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['above_button_text'] = strip_tags($new_instance['above_button_text']);
        $instance['below_button_text'] = strip_tags($new_instance['below_button_text']);
        $instance['display_time_since_last_click'] = $new_instance['display_time_since_last_click'];
        $instance['display_time_text'] = strip_tags($new_instance['display_time_text']);
        
        return $instance;
    }
    
    // For displaying the widget
    function widget($args, $instance) {
        extract($args);
        
        echo $before_widget;
        
        $title = apply_filters('widget_title', $instance['title']);
                
        $above_button_text = empty($instance['above_button_text']) ? '&nbsp;' : $instance['above_button_text'];
        $below_button_text = empty($instance['below_button_text']) ? '&nbsp;' : $instance['below_button_text'];
        $display_time_text = empty($instance['display_time_text']) ? '&nbsp;' : $instance['display_time_text'];
        
        if(!empty($title)) { echo $before_title . $title . $after_title; };
        
        echo '<br/><div align="center"><p>' . $above_button_text . '</p>';
                
        ?>
        <form action="" method="post">
        <input type="image" alt="A big red button" src="<?php echo plugin_dir_path(__FILE__); ?>big-red-button.png" name="big_red_button" />
        <!-- <input type="image" alt="A big red button" src="/wp-content/plugins/big-red-button/big-red-button.png" name="big_red_button" /> -->
        </form>
        
        <?php        
        if(isset($_POST['big_red_button_x']) && isset($_POST['big_red_button_y'])) {            
            echo '<br/><p>' . $below_button_text;
        
            if($instance['display_time_since_last_click'] == "on") {
                if(get_option('mp_brb_last_click_time')) {            
                    $last_click_time =  get_option('mp_brb_last_click_time');
                    $last_click_ago = ago($last_click_time);

                    $display_time_text = str_replace("%TIME%", $last_click_ago, $display_time_text);
                    
                    //echo '<br/><br/>It\'s been ' . $last_click_ago . ' since the button was last clicked';
                    echo '<br/><br/>' . $display_time_text;
                }
                else
                {
                    echo '<br/><br/>The button was clicked for the first time ever!';
                }
                
                // Save the current time for later
                update_option('mp_brb_last_click_time', new DateTime("now"));
            }
        }
            
        echo '</p></div>';
        
        echo $after_widget;
    }
}

?>