<?php
defined('ABSPATH') or die('No script kiddies please!');
$number_of_news = (new StandoutNews)->get_number_of_news();
$counter = 0;

$html = '<div class="news-container">';
if ((new StandoutNews)->standout_news_content() != null) :
    foreach ((new StandoutNews)->standout_news_content() as $news) :
        if (!empty($news)) :
            $counter++;
            $html .= '<div class="news-info">';
            if (!empty($news['urlToImage'])) :
                $html .= '<div class="news-image" style="background-image: url(' . $news['urlToImage'] . ');">';
            endif;
            $html .= '<div class="news-color-overlay">';
            $html .= '<a href="' . $news['url'] . '" target="_blank">';
            $html .= '<h1>' . $news['title'] . '</h1>';
            $html .= '</a>';
            $html .= '<p>Published at ' . $news['publishedAt'] . ' by '  . $news['author'] . '</p>';
            $html .= '<div class="news-content">';
            $html .= '<p>' . $news['description'] . '</p>';
            $html .= '<p>Source: ' . $news['source']['source'];
            $html .= '</div>';
            $html .= '</div>';
            if (!empty($news['urlToImage'])) :
                $html .= '</div>';
            endif;
            $html .= '</div>';
            if($counter == $number_of_news) :
              break;
            endif;
        endif;
    endforeach;
else:
    $html .= "<h1>Sorry, we didn't find any news.<h1>";
endif;
$html .= '</div>';

echo $html;
