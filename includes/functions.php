<?php
defined('ABSPATH') or die('No script kiddies please!');
/**
* Fetch all countries
* @return array[] $countries that contain all countries
*/
if (!function_exists('standout_get_all_countries')) :
    function standout_get_all_countries() {

        $countries = array(
                    'gb' => 'Great Britain',
                    'ae' => 'United Arab Emirates',
                    'ar' => 'Argentina',
                    'at' => 'Austria',
                    'au' => 'Australia',
                    'be' => 'Belgium',
                    'bg' => 'Bulgaria',
                    'br' => 'Brazil',
                    'ca' => 'Canada',
                    'ch' => 'Switzerland',
                    'cn' => 'China',
                    'co' => 'Columbia',
                    'cu' => 'Cuba',
                    'cz' => 'Czech Republic',
                    'de' => 'Germany',
                    'eg' => 'Egypt',
                    'fr' => 'France',
                    'gr' => 'Greece',
                    'hk' => 'Hong Kong',
                    'hu' => 'Hungary',
                    'id' => 'Indonesia',
                    'ie' => 'Ireland',
                    'il' => 'Israel',
                    'in' => 'India',
                    'it' => 'Italy',
                    'jp' => 'Japan',
                    'kr' => 'South Korea',
                    'lt' => 'Lithuania',
                    'lv' => 'Latvia',
                    'ma' => 'Morocco',
                    'mx' => 'Mexico',
                    'my' => 'Malaysia',
                    'ng' => 'Nigeria',
                    'no' => 'Norway',
                    'ru' => 'Russia',
                    'se' => 'Sweden',
                    'us' => 'United States'
                    );

        return $countries;
    }
endif;


/**
* Fetch all blocked categories
* @return array[] $output that contain all categories in the blacklist
*/
if (!function_exists('standout_get_unchosen_categories')) :
    function standout_get_unchosen_categories() {
        $categories = array(
                'entertainment',
                'health',
                'science',
                'sports',
                'technology'
                    );
        $chosen = standout_get_chosen_categories();
        $output = array();

        foreach ($categories as $key => $category) :
            $found = false;
            foreach ($chosen as $chosen_category) :
                if ($category == $chosen_category->category) :
                    $found = true;
                endif;
            endforeach;
            if (!$found)
                $output[] .= $category;
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
        $categories = array(
                'entertainment',
                'health',
                'science',
                'sports',
                'technology'
                    );
        $chosen = standout_get_chosen_categories();
        $output = array();

        foreach ($categories as $category) :
            $found = false;
            foreach ($chosen as $chosen_category) :
                if ($category == $chosen_category->category) :
                    $found = true;
                endif;
            endforeach;
            if (!$found)
                $output[] .= $category;
        endforeach;
        return $output;
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

/**
* Adds form in admin panel to choose which subcategories to hide from the sidebar
*/
if (!function_exists('standout_news_settings')) :
    function standout_news_settings() {
        $html = '<div id="wpbody">';
        $html .= '<div id="wpbody-content">';
        $html .= '<div class="wrap">';
        $html .= '<h1>News settings</h1>';
        $html .= "<p>Hi, here you'll have some settings for your news feed! Exciting, isn't it?</p>";
        echo $html;
        echo standout_news_form();
        $html2 = '</div>';
        $html2 .= '</div>';
        $html2 .= '</div>';

        echo $html2;
    }

    add_action('admin_menu', function() {
      add_options_page('Standout News Settings', 'Standout News', 'manage_options', 'news-sidebar', 'standout_news_settings');
    });

endif;
