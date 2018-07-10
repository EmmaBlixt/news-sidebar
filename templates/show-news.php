<?php
$html = '<div class="news-container">';
foreach ((new StandoutNews)->standout_news_content() as $news) :
    if (!empty($news)) :
        $html .= '<div class="news-info">';
        if (!empty($news['urlToImage'])) :
            $html .= '<div class="news-image" style="background-image: url(' . $news['urlToImage'] . ');">';
        endif;
        $html .= '<div class="news-color-overlay">';
        $html .= '<a href="' . $news['url'] . '">';
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
    endif;
endforeach;
$html .= '</div>';

echo $html;
