<?php
defined('ABSPATH') or die('No script kiddies please!');
require_once('functions.php');

/**
* Create all filter form fields
* @return string $form_output will create a form field when echoed
*/
if (!function_exists('standout_number_of_news_form')) :
  function standout_number_of_news_form() {

    $form_output = '<h1>Add display filters</h1>';
    $form_output .= '<form method="post" action="">';
    $form_output .= '<label for="number">Number of news</label></br>';
    $form_output .= '<input type="number" name="number" placeholder="'. standout_get_news_number() .'"></input></br>';
    $form_output .= '<label for="categories">Show certain categories</label></br>';
    $form_output .= '<select multiple="multiple" name="categories[]" style="width: 200px; height: 100px;">';

    foreach (standout_get_unchosen_categories() as $blocked_category) :
        $form_output .= '<option value="' . $blocked_category . '""> ' . ucfirst($blocked_category) . '</option>';
    endforeach;

    $form_output .= '</select></br>';
    $form_output .= '</select></br>';
    $form_output .= '<label for="countries">Show from certain countries</label></br>';
    $form_output .= '<select multiple="multiple" name="countries[]" style="width: 200px; height: 300px;">';

    foreach (standout_get_all_countries() as $key => $country) :
        $found = false;
        foreach (standout_get_chosen_countries() as $chosen_country) :
            if ($country == $chosen_country->country) :
                $found = true;
            endif;
        endforeach;
        if (!$found)
            $form_output .= '<option value="{short: '. $key .', long:'. $country .'}">' . $country . '</option>';
    endforeach;

    $form_output .= '</select></br>';
    $form_output .= '<input type="submit" name="standout_news_settings"/>';
    $form_output .= '</form>';
    $form_output .= '</br>';
    $form_output .= '<h1>Remove display filters</h1>';
    $form_output .= '<form method="post" action="">';
    $form_output .= '<label for="remove_category">Remove category from display</label></br>';
    $form_output .= '<select multiple name="remove_category[]" style="width: 300px; min-height: 100px">';
    $blocked_categories = standout_get_chosen_categories();

    foreach ($blocked_categories as $blocked_category) :
        $form_output .= '<option value="' . $blocked_category->category . '""> ' . ucfirst($blocked_category->category) . '</option>';
    endforeach;

    $form_output .= '</select></br>';
    $form_output .= '<label for="remove_country">Remove countries from display</label></br>';
    $form_output .= '<select multiple name="remove_country[]" style="width: 300px; height: 150px">';

    foreach (standout_get_chosen_countries() as $chosen_country) :
        $form_output .= '<option value="' . $chosen_country->country . '""> ' . ucfirst($chosen_country->country) . '</option>';
    endforeach;

    $form_output .= '</select></br>';
    $form_output .= '<input type="submit" name="remove_from_display">';
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
        global $wpdb;
        $number_table = $wpdb->prefix . 'standout_news_number';
        $category_table = $wpdb->prefix . 'standout_news_categories';
        $country_table = $wpdb->prefix . 'standout_news_countries';

        if(!empty($_POST['categories'])) :
            foreach ($_POST['categories'] as $category) :
                $wpdb->insert($category_table, array(
                    'category' => $category
                ));
            endforeach;
        endif;

        if(!empty($_POST['countries'])) :
            foreach ($_POST['countries'] as $country) :
                $country_slug = explode(": ",$country);
                $country_slug2 = explode(", ",$country_slug[1]);
                $country_long = explode('long:',$country);
                $country_long2 = explode('}',$country_long[1]);

                $wpdb->insert($country_table, array(
                    'country'      => $country_long2[0],
                    'country_slug' => $country_slug2[0]
                ));
            endforeach;
        endif;

        if(!empty($_POST['number'])) :
            $data = $wpdb->get_results("SELECT id FROM $number_table");
            if(count($data) == 0) :
                $wpdb->insert($number_table, array(
                    'number_of_news' => $_POST['number'],
                    ));
            else:
                $wpdb->update($number_table, array('number_of_news' => $_POST['number']), array('id' => $data[0]->id));
            endif;
        endif;
    }
endif;

/**
* Delete selected filters from database
* @param array[] $input catches input data from a form
*/
if (!function_exists('standout_remove_news_filters')) :
    function standout_remove_news_filters($input) {
        global $wpdb;
        $category_table = $wpdb->prefix . 'standout_news_categories';
        $country_table = $wpdb->prefix . 'standout_news_countries';

        if(!empty($_POST['remove_category'])) :
            foreach ($_POST['remove_category'] as $category) :
                $wpdb->delete($category_table, array(
                    'category' => $category
                ));
            endforeach;
        endif;

        if(!empty($_POST['remove_country'])) :
            foreach ($_POST['remove_country'] as $country) :
                $wpdb->delete($country_table, array(
                    'country' => $country
                ));
            endforeach;
        endif;
    }
endif;

if(isset($_POST['remove_from_display'])) :
    standout_remove_news_filters($_POST);
endif;

if(isset($_POST['standout_news_settings'])) :
    standout_add_news_filters($_POST);
endif;
