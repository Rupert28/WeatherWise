<?php
// Add an admin menu item for WeatherWise settings

function weatherwise_add_admin_menu()
{
    add_menu_page(
        'WeatherWise',
        'WeatherWise',
        'manage_options',
        'weatherwise-settings',
        'weatherwise_settings_page',
        'data:image/svg+xml;base64,' . base64_encode(get_svg_icon_content()),
    );
}
add_action('admin_menu', 'weatherwise_add_admin_menu');

function get_svg_icon_content()
{
    // Read the SVG file content
    $svg_path = plugin_dir_path(__FILE__) . 'css/icon.svg';
    $svg_content = file_get_contents($svg_path);

    // Return the SVG content
    return $svg_content;
}


// Function to display the settings page
function weatherwise_settings_page()
{

    $settings = weatherwise_get_settings();


    // $settings['location'] = isset($settings['location']) ? $settings['location'] : '';
    // $settings['api_key'] = isset($settings['api_key']) ? $settings['api_key'] : '';

    ?>
    <div class="wrap">
        <h1>WeatherWise Settings</h1>
        <p>Thanks for installing the WeatherWise WordPress Plugin!<br>To use the plugin, add the shortcode
            <b><code>[display_weather]</code></b>
            to your site and configure the options below.
        </p>
        <form method="post" action="options.php">
            <?php
            settings_fields('weatherwise_settings');
            do_settings_sections('weatherwise_settings');

            //$settings = weatherwise_get_settings(); // Retrieve settings
            //$latitude_value = isset($settings['latitude']) ? esc_attr($settings['latitude']) : '';
            //$longitude_value = isset($settings['longitude']) ? esc_attr($settings['longitude']) : '';
            //$api_key_value = isset($settings['api_key']) ? esc_attr($settings['api_key']) : '';
            //$display_location = isset($settings['display_location']) ? esc_attr($settings['display_location']) : '';
            //$background_colour = isset($settings['background_colour']) ? esc_attr($settings['background_colour']) : '';


            $settings_keys = array(
                'latitude',
                'longitude',
                'api_key',
                'display_location',
                'background_colour',
                'text_colour'
            );

            $sanitized_settings = array();

            $settings = weatherwise_get_settings();

            foreach ($settings_keys as $key) {
                $sanitized_settings[$key] = isset($settings[$key]) ? esc_attr($settings[$key]) : '';
            }



            ?>
            <h2>Location settings</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Location</th>
                    <td>
                        <input type="text" name="weatherwise_settings[latitude]" value="<?php echo $sanitized_settings['latitude']; ?>" />
                        <p class="description">Enter your desired latitude.</p>
                    </td>
                    <td>
                        <input type="text" name="weatherwise_settings[longitude]" value="<?php echo $sanitized_settings['longitude']; ?>" />
                        <p class="description">Enter your desired longitude.</p>
                    </td>
                </tr>
            </table>


            <h2>Display Settings</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Display Location</th>
                    <td>
                        <input type="text" name="weatherwise_settings[display_location]"
                            value="<?php echo $sanitized_settings['display_location']; ?>" />
                        <p class="description">Enter the location you would like to display on the widget.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Background Colour</th>
                    <td>
                        <input type="text" name="weatherwise_settings[background_colour]"
                            value="<?php echo $sanitized_settings['background_colour']; ?>" />
                        <p class="description">Use hex including the # symbol.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Text Colour</th>
                    <td>
                        <input type="text" name="weatherwise_settings[text_colour]" value="<?php echo $sanitized_settings['text_colour']; ?>" />
                        <p class="description">Use hex including the # symbol.</p>
                    </td>
                </tr>
            </table>





            <h2>API Settings</h2>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API Key</th>
                    <td>
                        <input type="text" name="weatherwise_settings[api_key]" value="<?php echo $sanitized_settings['api_key']; ?>" />
                        <p class="description">To access the hourly forecast you'll need to subscribe
                            to OpenWeather's One Call API 3.0. This API allows for 1000 free daily API calls. The plugin is
                            setup to stay
                            below this limit. Once you've subscribed to this service, enter your API key above.<br><a
                                href='https://openweathermap.org/api/one-call-3'>
                                https://openweathermap.org/api/one-call-3</a>
                        </p>
                    </td>
                </tr>
            </table>


            <?php submit_button('Save Settings', 'primary', 'submit', false);


            ?>

            <?php settings_errors('weatherwise-notices'); ?>

        </form>
    </div>
    <?php
}

// Function to get plugin settings
function weatherwise_get_settings()
{
    $settings = get_option('weatherwise_settings');
    return $settings ? $settings : array();
}





// Function to save/update plugin settings
function weatherwise_save_settings()
{
    if (isset($_POST['weatherwise_settings']) && current_user_can('manage_options')) {
        $new_settings = $_POST['weatherwise_settings'];
        update_option('weatherwise_settings', $new_settings);
        add_settings_error('weatherwise-notices', 'weatherwise-saved', 'Settings saved', 'updated');

    }
}
add_action('admin_init', 'weatherwise_save_settings');





// Hook to set default settings upon plugin activation
function weatherwise_plugin_activated()
{
    $default_settings = array();
    add_option('weatherwise_settings', $default_settings);
}
register_activation_hook(__FILE__, 'weatherwise_plugin_activated');
