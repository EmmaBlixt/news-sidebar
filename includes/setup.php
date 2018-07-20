<?php
defined('ABSPATH') or die('No script kiddies please!');
require_once('forms.php');

// Enqueue plugin styles
function register_standout_news_styles()
{
    wp_register_style('news', plugins_url('../css/news.css',__FILE__));
    wp_enqueue_style('news');
}

add_action('wp_enqueue_scripts','register_standout_news_styles');

/**
* Find and call plugin page template
* @param $template
*/
function display_template($template)
{
    $file_name = $template;

    if (locate_template($file_name)) :
        $template = locate_template($file_name);
    else :
        $template = dirname(__FILE__) . '/../templates/' . $file_name;
    endif;

    if ($template) :
        load_template($template, false);
    endif;

}
