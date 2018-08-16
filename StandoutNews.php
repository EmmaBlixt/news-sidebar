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
        add_shortcode('standout_display_news', array($this, 'standout_display_news'));
    }

    /**
    * Get the news from a selected country
    * @return $country string with the selected countries
    */
    private function sort_by_country()
    {
        $country = '';
        $countries = standout_get_chosen_countries();
        foreach ($countries as $count) :
            if ($count->country_slug != '') :
                $country .= 'country=' . $count->country_slug . '&';
            endif;
        endforeach;

        return $country;
    }


    /**
    * Get selected categories
    * @return $category string with the categories
    */
    public function sort_by_category()
    {
        $category = '';
        $categories = standout_get_chosen_categories();
        foreach ($categories as $cat) :
            if ($cat->category != '') :
                $category .= 'category=' . $cat->category . '&';
            endif;
        endforeach;

        return $category;
    }


    /**
    * Fetch data from the api
    * @return object $news that contains all the news
    */
    private function standout_fetch_data()
    {
        $data = 'https://newsapi.org/v2/top-headlines?' . $this->sort_by_country() . $this->sort_by_category() . 'apiKey=7318db02d38d4027b31eb8a830a156d9';

        try {
            $response = wp_remote_get($data);
            $news = json_decode($response['body']);

        } catch (Exception $ex) {
            $news = null;
            return $news;
        }
        return $news->articles;
    }

    /**
    * Get the amount of news set in the admin options
    * @return $data containing the number of news displayed
    */
    public function get_number_of_news() {
        global $wpdb;
        $number_table = $wpdb->prefix . 'standout_news_number';
        $data = $wpdb->get_results("SELECT number_of_news FROM $number_table");
        return $data[0]->number_of_news;
    }


    /**
    * Set the news values
    * @param object[] $news
    */
    private function standout_set_news_values($news)
    {
        $this->source      =   $this->get_source($news);
        $this->title       =   $news->title;
        $this->author      =   $news->author;
        $this->description =   $news->description;
        $this->url         =   $news->url;
        $this->urlToImage  =   $news->urlToImage;
        $this->publishedAt =   $news->publishedAt;
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
    * Fetch the news source from the api
    * @param object[] $news
    * @return array() $output containing the source info from $news
    */
    private function get_source($news)
    {
        $output = array();

        if(!empty($news->source)) :
            $source = get_object_vars($news->source);
            $output = array(
                'id'       =>  'Id: '  . $source["id"],
                'source'   =>  $source["name"]
                );
            else :
            $output = array('status' => __('Not available', 'standout-news'));
        endif;

        return $output;
    }

    /**
    * Get all the news content
    * @return array() $output containing the news
    */
    public function standout_news_content()
    {
        $json_response = $this->standout_fetch_data();
        $output[] = '';
        if ($json_response == null) :
            $output = null;
        else :
            foreach ($json_response as $news) :
                $this->standout_set_news_values($news);
                $output[] = array(
                    'source'       =>  $this->get_source($news),
                    'title'        =>  $news->title,
                    'description'  =>  $news->description,
                    'author'       =>  $news->author,
                    'url'          =>  $news->url,
                    'urlToImage'   =>  $news->urlToImage,
                    'publishedAt'  =>  $news->publishedAt
                );
            endforeach;
        endif;

        return $output;
    }


    public function standout_display_news()
    {
        display_template('show-news.php');
    }
}

new StandoutNews();
