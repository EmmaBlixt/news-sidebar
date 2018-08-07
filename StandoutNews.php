<?php
defined('ABSPATH') or die('No script kiddies please!');
require_once('includes/setup.php');

class StandoutNews {
    private $source;
    private $title;
    private $author;
    private $description;
    private $url;
    private $urlToImage;
    private $publishedAt;
    global $wpdb;
    private $number_table = $wpdb->prefix . 'standout_news_number';
    private $category_table = $wpdb->prefix . 'standout_news_categories';
    private $country_table = $wpdb->prefix . 'standout_news_countries';

    function __construct()
    {
        register_activation_hook(__FILE__, array( $this, 'activate_standout_news'));
        return $this->init();
    }

    /**
    * Initialize the class & add WP hooks
    */
    public function init()
    {
        add_shortcode('standout_display_news', array($this, 'get_chosen_categories'));
    }

    /**
    * Get the news from a selected category
    */
    private function get_chosen_categories()
    {
        $cats = $wpdb->get_var("SELECT * FROM $category_table");
    }


    /**
    * Create databases when plugin is activated
    */
    private function activate_standout_news()
    {
        $charset = $wpdb->get_charset_collate();
        $sql = '';

        if($wpdb->get_var("SHOW TABLES LIKE '$number_table'") != $number_table) :
            $sql = "CREATE TABLE $number_table (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                number_of_news int NOT NULL,
                PRIMARY KEY (id)
                ) $charset;";
        endif;

        if($wpdb->get_var("SHOW TABLES LIKE '$category_table'") != $category_table) :
            $sql = "CREATE TABLE $category_table (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                category varchar(100) NOT NULL,
                PRIMARY KEY (id)
                ) $charset;";
        endif;

        if($wpdb->get_var("SHOW TABLES LIKE '$country_table'") != $country_table) :
            $sql = "CREATE TABLE $country_table (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                country_name varchar(55) NOT NULL,
                country_slug varchar(10) NOT NULL,
                PRIMARY KEY (id)
                ) $charset;";
        endif;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql);
    }

    /**
    * Fetch data from the api
    * @return object $news that contains all the news
    */
    private function standout_fetch_data()
    {
        $category = '';
        foreach ($this->sort_by_category() as $cat) :
            $category .= 'category=' . $cat . '&';
        endforeach;

        $country = '';
        foreach ($this->sort_by_country() as $count) :
            $country .= 'country=' . $count . '&';
        endforeach;

        $data = 'https://newsapi.org/v2/top-headlines?' . $country . strtolower($category) .'apiKey=7318db02d38d4027b31eb8a830a156d9';
        try {
            $response = wp_remote_get($data);
            $news = json_decode($response['body']);

        } catch (Exception $ex) {
            $news = null;
            return $news;
        }
        return $news->articles;
    }


    public function standout_display_news()
    {
        $this->get_chosen_categories();
        display_template('show-news.php');
    }
}

new StandoutNews();
