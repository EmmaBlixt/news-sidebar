<?php
/*
Plugin Name: News sidebar
Plugin URI:
description: Show recent news in a sidebar
Author: Emma Blixt
Author URI: https://standout.se
*/

defined('ABSPATH') or die('No script kiddies please!');
require_once('StandoutNewsWidget.php');
require_once('StandoutNews.php');

class Standout_News_Widget extends WP_Widget {

    public function __construct() {
            parent::__construct(
            'standout_news_widget',
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

        $countries = standout_get_all_countries();
        foreach ($countries as $country) :

        $defaults = array(
            'title'          => '',
            'number_of_news' => '',
            'category' => $categories =
                            array(
                                'Entertainment',
                                'General',
                                'Health',
                                'Science',
                                'Sports',
                                'Technology'
                            ),
            'country' => $country =
                        array($country)
        );
    endforeach;

        extract(wp_parse_args((array) $instance, $defaults)); ?>

            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Widget Title', 'text_domain'); ?></label>
                <input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
            </p>
            <p>
                <label for="<?php echo esc_attr( $this->get_field_id('number_of_news')); ?>"><?php _e('Number of news', 'text_domain'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('number_of_news')); ?>" name="<?php echo esc_attr($this->get_field_name('number_of_news')); ?>" type="number" value="<?php echo esc_attr($number_of_news); ?>" />
            </p>

            <p>
                <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php _e('Category', 'text_domain'); ?></label></br>

                <?php
                    printf (
                            '<select multiple="multiple" name="%s[]" id="%s" class="widefat">',
                            $this->get_field_name('category'),
                            $this->get_field_id('category')
                        );
                    foreach ($categories as $category) :
                        printf(
                                '<option value="%s" %s> %s </option>',
                                $category,
                                in_array($category, $instance['category']) ? 'selected="selected"' : '',
                                $category
                            );
                    endforeach;
                    echo '</select>';
                ?>
            </p>

            <p>
                <label for="<?php echo esc_attr($this->get_field_id('country')); ?>"><?php _e('Country', 'text_domain'); ?></label></br>

                <?php
                    printf (
                            '<select multiple="multiple" name="%s[]" id="%s" class="widefat">',
                            $this->get_field_name('country'),
                            $this->get_field_id('country')
                        );
                    foreach ($countries as $key => $country) :
                        printf(
                                '<option value='.$key.'>' . $country . ' </option>', $country
                            );
                    endforeach;
                    echo '</select>';
                ?>
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
        $instance['title'] = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';
        $instance['number_of_news'] = isset($new_instance['number_of_news']) ? wp_strip_all_tags($new_instance['number_of_news']) : '';
        $instance['country'] = esc_sql($new_instance['country']);
        $instance['category'] = esc_sql($new_instance['category']);

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
        $title = isset($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';

        // WordPress core before_widget hook (always include)
        echo $before_widget;

        // Display the widget
        echo '<div class="widget-text wp_widget_plugin_box standout-news-widget">';

        // Display widget title if defined
        if ($title) :
            echo $before_title . $title . $after_title;
        endif;

        echo do_shortcode('[standout_news_widget]');

        echo '</div>';
        echo $after_widget;
    }
}

function standout_register_news_widget() {
    register_widget('Standout_News_Widget');
}
add_action('widgets_init', 'standout_register_news_widget');

