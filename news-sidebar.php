<?php
/*
Plugin Name: News sidebar
Plugin URI:
description: Show recent news in a sidebar
Author: Emma Blixt
Author URI: https://standout.se
*/

defined('ABSPATH') or die('No script kiddies please!');
require_once('standoutnews.php');

class Standout_News_Widget extends WP_Widget {

    public function __construct() {
            parent::__construct(
            'my_custom_widget',
            __( 'Standout News Widget', 'text_domain' ),
            array(
                'customize_selective_refresh' => true,
            )
        );
    }

    /**
    * Create widget form for backend settings
    * @param $instance
    */
    public function form($instance)
    {
        $defaults = array(
            'title'    => '',
        );

        // Parse current settings with defaults
        extract(wp_parse_args((array) $instance, $defaults)); ?>

        <?php // Widget Title ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
        <?php
    }

    /**
    * Update widget settings
    * @param $new_instance, $old_instance
    * @return $intance that contains the user input
    */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title']    = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';
        return $instance;
    }

    /**
    * Display the widget
    * @param $args, $instance
    */
    public function widget($args, $instance)
    {
        extract($args );

        // Check the widget options
        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';

        // WordPress core before_widget hook (always include )
        echo $before_widget;

        // Display the widget
        echo '<div class="widget-text wp_widget_plugin_box">';

        // Display widget title if defined
        if ($title) :
            echo $before_title . $title . $after_title;
        endif;
        $news = new StandoutNews();

        echo '</div>';
        echo $after_widget;
    }
}

// Register the widget
function standout_register_news_widget() {
    register_widget( 'Standout_News_Widget' );
}
add_action( 'widgets_init', 'standout_register_news_widget' );

