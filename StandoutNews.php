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
        return $this->init();
    }

    /**
    * Initialize the class & add WP hooks
    */
    public function init()
    {
        add_shortcode('standout_display_news', array($this, 'standout_display_news'));
        add_option('standout_news_number', 0, '', 'yes' );
        add_option('standout_news_countries', '', '', 'yes' );
        add_option('standout_news_categories', '', '', 'yes' );
    }

    /**
    * Get the news from a selected country
    * @return $country string with the selected countries
    */
    private function sort_by_country()
    {
        $output = '';
        foreach (get_option('standout_news_countries') as $country) :
            $country_split = explode(",", $country);
            $output .= 'country=' . $country_split[1] . '&';
        endforeach;

        return $output;
    }


    /**
    * Get selected categories
    * @return $category string with the categories
    */
    public function sort_by_category()
    {
        $category = '';
        $categories = get_option('standout_news_categories');
        foreach ($categories as $cat) :
            if ($cat != '') :
                $category .= 'category=' . $cat . '&';
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
    * @return string with the set number
    */
    public function get_number_of_news() {
        return get_option('standout_news_number');
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
