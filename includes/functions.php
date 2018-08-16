<?php
defined('ABSPATH') or die('No script kiddies please!');
/**
* Fetch all countries
* @return array[] $countries that contain all countries
*/
if (!function_exists('standout_get_all_countries')) :
    function standout_get_all_countries() {

        $countries = array(
                    'gb' => __('Great Britain', 'standout-news'),
                    'ae' => __('United Arab Emirates', 'standout-news'),
                    'ar' => __('Argentina', 'standout-news'),
                    'at' => __('Austria', 'standout-news'),
                    'au' => __('Australia', 'standout-news'),
                    'be' => __('Belgium', 'standout-news'),
                    'bg' => __('Bulgaria', 'standout-news'),
                    'br' => __('Brazil', 'standout-news'),
                    'ca' => __('Canada', 'standout-news'),
                    'ch' => __('Switzerland', 'standout-news'),
                    'cn' => __('China', 'standout-news'),
                    'co' => __('Columbia', 'standout-news'),
                    'cu' => __('Cuba', 'standout-news'),
                    'cz' => __('Czech Republic', 'standout-news'),
                    'de' => __('Germany', 'standout-news'),
                    'eg' => __('Egypt', 'standout-news'),
                    'fr' => __('France', 'standout-news'),
                    'gr' => __('Greece', 'standout-news'),
                    'hk' => __('Hong Kong', 'standout-news'),
                    'hu' => __('Hungary', 'standout-news'),
                    'id' => __('Indonesia', 'standout-news'),
                    'ie' => __('Ireland', 'standout-news'),
                    'il' => __('Israel', 'standout-news'),
                    'in' => __('India', 'standout-news'),
                    'it' => __('Italy', 'standout-news'),
                    'jp' => __('Japan', 'standout-news'),
                    'kr' => __('South Korea', 'standout-news'),
                    'lt' => __('Lithuania', 'standout-news'),
                    'lv' => __('Latvia', 'standout-news'),
                    'ma' => __('Morocco', 'standout-news'),
                    'mx' => __('Mexico', 'standout-news'),
                    'my' => __('Malaysia', 'standout-news'),
                    'ng' => __('Nigeria', 'standout-news'),
                    'no' => __('Norway', 'standout-news'),
                    'ru' => __('Russia', 'standout-news'),
                    'se' => __('Sweden', 'standout-news'),
                    'us' => __('United States', 'standout-news')
                    );

        return $countries;
    }
endif;

/**
* Fetch all categories
* @return array[] $categories that contain all categories
*/
if (!function_exists('standout_get_all_categories')) :
    function standout_get_all_categories() {

        $categories = array(
                'entertainment' => __('Entertainment', 'standout-news'),
                'health'        => __('Health', 'standout-news'),
                'science'       => __('Science', 'standout-news'),
                'sports'        => __('Sports', 'standout-news'),
                'technology'    => __('Technology', 'standout-news')
                    );

        return $categories;
    }
endif;


/**
* Fetch all blocked categories
* @return array[] $output that contain all categories in the blacklist
*/
if (!function_exists('standout_get_unchosen_categories')) :
    function standout_get_unchosen_categories() {
        $categories = standout_get_all_categories();
        $chosen = standout_get_chosen_categories();
        $output = array();

        foreach ($categories as $key => $category) :
            $found = false;
            foreach ($chosen as $chosen_category) :
                if ($category != $chosen_category->category) :
                     $output[] .= $category;
                     break;
                endif;
            endforeach;
        endforeach;

        return $output;
    }
endif;

/**
* Fetch all blocked categories
* @return array[] $blocked_categories that contain all categories in the blacklist
*/
if (!function_exists('standout_get_chosen_categories')) :
    function standout_get_chosen_categories() {
        global $wpdb;
        $table = $wpdb->prefix . 'standout_news_categories';
        $blocked_categories = $wpdb->get_results("SELECT * FROM $table");
        return $blocked_categories;
    }
endif;

/**
* Fetch all unblocked categories
* @return array[] $output that contain all categories that are not in the blacklist
*/
if (!function_exists('standout_get_unchosen_categories')) :
    function standout_get_unchosen_categories() {
        $categories = standout_get_all_categories();
        $chosen = standout_get_chosen_categories();
        $output = array();

        foreach ($categories as $key => $category) :
            $found = false;
            foreach ($chosen as $chosen_category) :
                if ($key != $chosen_category->category) :
                    $output[] .= $key;
                    break;
                endif;
            endforeach;
        endforeach;

        return $output;
    }
endif;

if (!function_exists('standout_get_category_form_options')) :
    function standout_get_unchosen_category_form_options() {

        $form_output = array();
        foreach (standout_get_all_categories() as $key => $category) :
            $found = false;
            foreach (standout_get_chosen_categories() as $blocked_category) :
                if ($key == $blocked_category->category) :
                    $found = true;
                endif;
            endforeach;
            if (!$found)
                $form_output[] .= '<option value="' . $key . '""> ' . $category . '</option>';
        endforeach;

        return $form_output;
    }
endif;


/**
* Fetch all blocked categories
* @return array[] $countries that contain all categories in the blacklist
*/
if (!function_exists('standout_get_chosen_countries')) :
    function standout_get_chosen_countries() {
        global $wpdb;
        $table = $wpdb->prefix . 'standout_news_countries';
        $countries = $wpdb->get_results("SELECT * FROM $table");

        return $countries;
    }
endif;


if (!function_exists('standout_get_country_form_options')) :
    function standout_get_unchosen_country_form_options(){
        $form_output = array();
        foreach (standout_get_all_countries() as $key => $country) :
            $found = false;
            foreach (standout_get_chosen_countries() as $chosen_country) :
                if ($country == $chosen_country->country) :
                    $found = true;
                endif;
            endforeach;
            if(!$found) :
                $form_output[] .= '<option value="{short: '. $key .', long:'. $country .'}">' . $country . '</option>';
            endif;
        endforeach;

        return $form_output;
    }
endif;

/**
* Adds form in admin panel to choose which subcategories to hide from the sidebar
*/
if (!function_exists('standout_news_settings')) :
    function standout_news_settings() {
        $html = '<div id="wpbody">';
        $html .= '<div id="wpbody-content">';
        $html .= '<div class="wrap">';
        $html .= '<h1>News settings</h1>';
        $html .= '<p>' . __("Hi, here you'll have some settings for your news feed! Exciting, isn't it?", 'standout_news') . '</p>';
        echo $html;
        echo standout_news_form();
        $html2 = '</div>';
        $html2 .= '</div>';
        $html2 .= '</div>';

        echo $html2;
    }

    add_action('admin_menu', function() {
      add_options_page(__('Standout News Settings', 'standout-news'), __('Standout News', 'standout-news'), 'manage_options', 'news-sidebar', 'standout_news_settings');
    });

endif;
