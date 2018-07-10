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
        add_shortcode('standout_display_news', array($this, 'display_news'));
    }

    /**
    * Fetch data from the api
    * @return object $news that contains all the news
    */
    private function standout_fetch_data()
    {
        try {
        $response = wp_remote_get('https://newsapi.org/v2/everything?q=bitcoin&apiKey=7318db02d38d4027b31eb8a830a156d9');
        $news = json_decode($response['body']);

        } catch (Exception $ex) {
            $news = null;
            return $news;
        }
        return $news->articles;
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
            $output = array('status' => 'Not available');
        endif;

        return $output;
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
    * Get all the news content
    * @return array() $output containing the news
    */
    public function standout_news_content()
    {
        $json_response = $this->standout_fetch_data();
        $output[] = '';
        if ($json_response == null) :
            $output = "<h1>Sorry, I couldn't find the api</h1>";
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

    public function display_news()
    {
        display_template('show-news.php');
    }
}

new StandoutNews();
