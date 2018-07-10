<?php
/*
Plugin Name: News sidebar
Plugin URI:
description: Show recent news in a sidebar
Author: Emma Blixt
Author URI: https://standout.se
*/

defined('ABSPATH') or die('No script kiddies please!');
require_once('StandoutNews.php');

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
            'number_of_news' => ''
        );

        // Parse current settings with defaults
        extract(wp_parse_args((array) $instance, $defaults)); ?>

        <?php // Widget Title ?>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'text_domain' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id( 'number_of_news' ) ); ?>"><?php _e( 'Number of news', 'text_domain' ); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number_of_news' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_news' ) ); ?>" type="number" value="<?php echo esc_attr( $number_of_news ); ?>" />
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
        $instance['number_of_news']    = isset($new_instance['number_of_news']) ? wp_strip_all_tags($new_instance['number_of_news']) : '';
        return $instance;
    }

    /**
    * Display the widget
    * @param $args, $instance
    */
    public function widget($args, $instance)
    {
        extract($args);

        // Check the widget options
        $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
        $number_of_news = isset( $instance['number_of_news'] ) ? apply_filters( 'widget_number_of_news', $instance['number_of_news'] ) : '';

        // WordPress core before_widget hook (always include)
        echo $before_widget;

        // Display the widget
        echo '<div class="widget-text wp_widget_plugin_box standout-news-widget">';

        // Display widget title if defined
        if ($title) :
            echo $before_title . $title . $after_title;
        endif;

        echo do_shortcode('[standout_display_news]');

        echo '</div>';
        echo $after_widget;
    }
}

// Register the widget
function standout_register_news_widget() {
    register_widget( 'Standout_News_Widget' );
}
add_action( 'widgets_init', 'standout_register_news_widget' );

