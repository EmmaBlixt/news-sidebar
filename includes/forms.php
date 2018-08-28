<?php
defined('ABSPATH') or die('No script kiddies please!');
require_once('functions.php');

/**
* Create all filter form fields
* @return string $form_output will create a form field when echoed
*/
if (!function_exists('standout_news_form')) :
  function standout_news_form() {

    $form_output = '<h1>' . __('Add display filters', 'standout-news') . '</h1>';
    $form_output .= '<form method="post" action="">';
    $form_output .= '<label for="number">Number of news</label></br>';
    $form_output .= '<input type="number" name="number" placeholder="' . get_option('standout_news_number') .'"></input></br>';
    $form_output .= '<label for="categories">' . __('Show certain categories', 'standout-news') . '</label></br>';
    $form_output .= '<select multiple="multiple" name="categories[]" style="width: 200px; height: 100px;">';

    $form_output .= standout_get_category_form_options();

    $form_output .= '</select></br>';
    $form_output .= '</select></br>';
    $form_output .= '<label for="countries">' . __('Show from certain countries', 'standout-news') . '</label></br>';
    $form_output .= '<select multiple="multiple" name="countries[]" style="width: 200px; height: 300px;">';

    $form_output .= standout_get_country_form_options();

    $form_output .= '</select></br>';
    $form_output .= '<input type="submit" name="standout_news_settings"/>';
    $form_output .= '</form>';

    return $form_output;
  }
endif;




/**
* Save the input data to the database tables
* @param array[] $input catches input data from a form
*/
if (!function_exists('standout_add_news_filters')) :
    function standout_add_news_filters($input) {
        if(!empty($_POST['categories'])) :
            $data = array();
            foreach ($_POST['categories'] as $category) :
                $data[] = $category;
            endforeach;
            update_option('standout_news_categories', $data);
        endif;

        if(!empty($_POST['countries'])) :
            $data = array();
            foreach ($_POST['countries'] as $country) :
                $country_slug = explode(": ",$country);
                $country_slug2 = explode(", ",$country_slug[1]);
                $country_long = explode('long:',$country);
                $country_long2 = explode('}',$country_long[1]);
                $data[] = $country_long2[0] .','. $country_slug2[0];
            endforeach;

            update_option('standout_news_countries', $data);
        endif;

        if(!empty($_POST['number'])) :
            update_option('standout_news_number', $_POST['number']);
        endif;
    }
endif;

if(isset($_POST['standout_news_settings'])) :
    standout_add_news_filters($_POST);
endif;
