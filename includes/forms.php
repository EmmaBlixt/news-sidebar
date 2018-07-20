<?php
defined('ABSPATH') or die('No script kiddies please!');
/**
* Fetch all blocked categories and print them into a miltiple select form
* @return string $form_output will create a form field when echoed
*/
if (!function_exists('standout_number_of_news_form')) :
  function standout_number_of_news_form()
  {
    $form_output = '<form method="post" action="">';
    $form_output .= '<label for="number">Number of news</label></br>';
    $form_output .= '<input type="number" name="number" placeholder="'. standout_get_news_number() .'"></input></br>';
    $form_output .= '<label for="categories">Sort by categories</label></br>';
    $form_output .= '<select multiple="multiple" name="categories[]" style="width: 200px; height: 100px;">';
    $form_output .= '<option value="entertainment">Entertainment</option>';
    $form_output .= '<option value="health">Health</option>';
    $form_output .= '<option value="science">Science</option>';
    $form_output .= '<option value="sports">Sports</option>';
    $form_output .= '<option value="technology">Technology</option>';
    $form_output .= '</select></br>';
    $form_output .= '<label for="countries">Sort by countries</label></br>';
    $form_output .= '<select multiple="multiple" name="countries[]" style="width: 200px; height: 300px;">';
    $form_output .= '<option value="gb">Great Britain</option>';
    $form_output .= '<option value="ae">United Arab Emirates</option>';
    $form_output .= '<option value="ar">Argentina</option>';
    $form_output .= '<option value="at">Austria</option>';
    $form_output .= '<option value="au">Australia</option>';
    $form_output .= '<option value="be">Belgium</option>';
    $form_output .= '<option value="bg">Bulgaria</option>';
    $form_output .= '<option value="br">Brazil</option>';
    $form_output .= '<option value="ca">Canada</option>';
    $form_output .= '<option value="ch">Switzerland</option>';
    $form_output .= '<option value="cn">China</option>';
    $form_output .= '<option value="co">Colombia</option>';
    $form_output .= '<option value="cu">Cuba</option>';
    $form_output .= '<option value="cz">Czech Republic</option>';
    $form_output .= '<option value="de">Germany</option>';
    $form_output .= '<option value="eg">Egypt</option>';
    $form_output .= '<option value="fr">France</option>';
    $form_output .= '<option value="gr">Greece</option>';
    $form_output .= '<option value="hk">Hong Kong</option>';
    $form_output .= '<option value="hu">Hungary</option>';
    $form_output .= '<option value="id">Indonesia</option>';
    $form_output .= '<option value="ie">Ireland</option>';
    $form_output .= '<option value="il">Israel</option>';
    $form_output .= '<option value="in">India</option>';
    $form_output .= '<option value="it">Italy</option>';
    $form_output .= '<option value="jp">Japan</option>';
    $form_output .= '<option value="kr">South Korea</option>';
    $form_output .= '<option value="lt">Lithuania</option>';
    $form_output .= '<option value="lv">Latvia</option>';
    $form_output .= '<option value="ma">Morocco</option>';
    $form_output .= '<option value="mx">Mexico</option>';
    $form_output .= '<option value="my">Malaysia</option>';
    $form_output .= '<option value="ng">Nigeria</option>';
    $form_output .= '<option value="no">Norway</option>';
    $form_output .= '<option value="ru">Russia</option>';
    $form_output .= '<option value="se">Sweden</option>';
    $form_output .= '<option value="us">United States</option>';
    $form_output .= '</select></br>';
    $form_output .= '<input type="submit" name="standout_news_settings"/>';
    $form_output .= '</form>';

    return $form_output;
  }
endif;

/**
* Adds form in admin panel to choose which subcategories to hide from the sidebar
*/
if (!function_exists('standout_news_settings')) :
    function standout_news_settings()
    {
        $html = '<div id="wpbody">';
        $html .= '<div id="wpbody-content">';
        $html .= '<div class="wrap">';
        $html .= '<h1>News settings</h1>';
        $html .= "<p>Hi, here you'll have some settings for your news feed! Exciting, isn't it?</p>";
        echo $html;
        echo standout_number_of_news_form();
        $html2 = '</div>';
        $html2 .= '</div>';
        $html2 .= '</div>';

        echo $html2;
    }

    add_action('admin_menu', function()
    {
      add_options_page('Standout News Settings', 'Standout News', 'manage_options', 'news-sidebar', 'standout_news_settings');
    });

endif;

/**
* Save the input data to the wp_hidden_categories table
* @param array[] $input catches input data from a select multiple form
*/
if (!function_exists('standout_get_categories')) :
    function standout_get_categories($input)
    {
        $output[] = '';
        foreach ($_POST['categories'] as $category) :
            $output[] .= $category;
        endforeach;

        return $output;
    }
endif;

/**
* Get the amount of news set in the admin options
* @return $data containing the number of news displayed
*/
if (!function_exists('standout_get_news_number')) :
    function standout_get_news_number()
    {
        global $wpdb;
        $number_table = $wpdb->prefix . 'standout_news_number';
        $data = $wpdb->get_results("SELECT number_of_news FROM $number_table");
        return $data[0]->number_of_news;
    }
endif;

/**
* Save the input data to the wp_hidden_categories table
* @param array[] $input catches input data from a form
*/
if (!function_exists('standout_add_news_filters')) :
    function standout_add_news_filters($input)
    {
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
                $wpdb->insert($country_table, array(
                    'country' => $country
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

if(isset($_POST['standout_news_settings'])) :
    standout_add_news_filters($_POST);
endif;
