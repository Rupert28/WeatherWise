<?php
/*
Plugin Name: WeatherWise
Description: Show an hourly forecast for the next 24 hours on your website.
Version: 1.0.0
Author: Rupert Morgan
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

include_once(plugin_dir_path(__FILE__) . 'settings.php');

function weatherwise_register_settings()
{
    register_setting('weatherwise_settings', 'weatherwise_settings');
    add_option('weatherwise_settings');
}
add_action('admin_init', 'weatherwise_register_settings');
function weatherwise_init()
{
    add_shortcode('display_weather', 'weatherwise_insert_weather_content');
}
add_action('init', 'weatherwise_init');

function weatherwise_enqueue_scripts()
{
    wp_enqueue_style('weatherwise-style', plugins_url('css/weatherwise-style.css', __FILE__));
    wp_enqueue_style('google-font', 'https://fonts.googleapis.com/css2?family=Nunito+Sans:opsz,wght@6..12,700&display=swap');
}
add_action('wp_enqueue_scripts', 'weatherwise_enqueue_scripts');

$weather_icons = [
    200 => 'https://openweathermap.org/img/wn/11d@2x.png',
    300 => 'https://openweathermap.org/img/wn/09d@2x.png',
    500 => 'https://openweathermap.org/img/wn/10d@2x.png',
    600 => 'https://openweathermap.org/img/wn/13d@2x.png',
    700 => 'https://openweathermap.org/img/wn/50d@2x.png',
    800 => 'https://openweathermap.org/img/wn/01d@2x.png',
    801 => 'https://openweathermap.org/img/wn/02d@2x.png',
    802 => 'https://openweathermap.org/img/wn/03d@2x.png',
    803 => 'https://openweathermap.org/img/wn/04d@2x.png',
    804 => 'https://openweathermap.org/img/wn/04d@2x.png',
];

function weatherwise_display_weather()
{
    $cached_data = get_transient('weather_data');
    $output = '';

    if ($cached_data) {
        $output .= display_weather_data($cached_data);
    } else {
        global $weather_icons;
        $settings = get_option('weatherwise_settings');
        //test api key: 1745f6b02837f52e1cbc9d8defc3a2e4
        $api_key = isset($settings['api_key']) ? $settings['api_key'] : '';
        //$latitude = '-32.246380';
        //$longitude = '148.591260';
        $latitude = isset($settings['latitude']) ? $settings['latitude'] : '';
        $longitude = isset($settings['longitude']) ? $settings['longitude'] : '';
        $api_url = "https://api.openweathermap.org/data/3.0/onecall?lat=$latitude&lon=$longitude&appid=$api_key&units=metric";

        $api_response = wp_remote_get($api_url);

        //echo '<script>console.log("API request made");</script>';

        if (!is_wp_error($api_response)) {
            $body = wp_remote_retrieve_body($api_response);
            $weather_data = json_decode($body);

            if ($weather_data && isset($weather_data->hourly)) {
                set_transient('weather_data', $weather_data, 1); // Caching for 10 minutes at 600

                $output .= display_weather_data($weather_data);
            } else {
                $current_user = wp_get_current_user();
                if (in_array('administrator', $current_user->roles)) {
                    $output .= '<div class="error-message">Failed to fetch weather data. Please check the API Key, Latitude, and Longitude in the options menu.
                    <br>This message is only visible to site administrators.</div>';
                }
            }
        } else {
            $current_user = wp_get_current_user();
            if (in_array('administrator', $current_user->roles)) {

                $output .= 'Failed to retrieve data from the API.';
            }
        }
    }
    return $output;
}

function display_weather_data($weather_data)
{
    $output = '';
    global $weather_icons;
    $settings = get_option('weatherwise_settings');

    $display_location = isset($settings['display_location']) ? $settings['display_location'] : '';

    $current_time = time();
    $output .= '<div class="weatherwise-wrapper">';
    $output .= '<h2>24 Hour Forecast</h2>';
    $output .= "<h4>$display_location</h4>";
    
    $output .= '<div class="weatherinfo">';

    $output .= '<div class="currentconditionswrapper">';


    //Current weather
    $current_time = $weather_data->current->dt;
    $current_temperature = $weather_data->current->temp;
    $current_conditions = $weather_data->current->weather[0]->id;
    $current_main = $weather_data->current->weather[0]->main;
    $current_clouds = $weather_data->current->clouds;
    $timezone = $weather_data->timezone;

    date_default_timezone_set($timezone);


    $formatted_current_time = date('h:i A', $current_time);


    $output .= "<div class='current-conditions'>";
    $output .= "<div class='time'>$formatted_current_time</div>";
    $output .= "<div class='main'>$current_main</div>";
    $output .= "<div class='temp'>$current_temperature&deg;C</div>";
    $output .= "<div class='clouds'>$current_clouds% Cloud Cover</div>";


    $output .= "<div class='current-image'>";
    if (array_key_exists($current_conditions, $weather_icons)) {
        $icon_url = $weather_icons[$current_conditions];
        $output .= "<div class='image-container'><img src='$icon_url' alt='Weather Icon'></div>";
    }
    $output .= "</div>";
    $output .= "</div>";
    $output .= "</div>";

    $output .= '<div class="weather-carousel">';

    //Translate API response for forecasts
    foreach ($weather_data->hourly as $hour) {
        $forecast_time = $hour->dt;
        $forecast_temperature = $hour->temp;
        $forecast_conditions = $hour->weather[0]->id;
        $forecast_main = $hour->weather[0]->main;
        $forecast_clouds = $hour->clouds;

        $formatted_time = date('h:i A', $forecast_time);

        date_default_timezone_set('Australia/Sydney');



        //Future weather forecast
        if ($forecast_time >= $current_time && $forecast_time <= $current_time + 86400) {
            $output .= "<div class='future-forecast'>";
            $output .= "<div class='time'>$formatted_time</div>";
            $output .= "<div class='main'>$forecast_main</div>";
            $output .= "<div class='temp'>$forecast_temperature&deg;C</div>";
            $output .= "<div class='clouds'>$forecast_clouds% Cloud Cover</div>";

            $output .= "<div class='condition-image'>";
            if (array_key_exists($forecast_conditions, $weather_icons)) {
                $icon_url = $weather_icons[$forecast_conditions];
                $output .= "<div class='image-container'><img src='$icon_url' alt='Weather Icon'></div>";
            }
            $output .= "</div>";
            $output .= "</div>";
        }
    }
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';
    return $output;
}


function weatherwise_insert_weather_content()
{
    $weather_content = weatherwise_display_weather(); // Display weather content
    return '<div id="weather-forecast">' . $weather_content . '</div>';
}
