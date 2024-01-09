# WeatherWise Plugin

The WeatherWise plugin displays the next 24 hours forecast for your location using data from OpenWeather's API.

## Installation

### WordPress Dashboard
1. Go to `Plugins` > `Add New`.
2. Search `WeatherWise` in the install menu.
3. Install, activate and configure the plugin.

## Usage

- **WeatherWise Settings**: Navigate to `WeatherWise` in the WordPress admin menu.
- **Location and Settings**: Configure your preferred location and customize display location.
- **Shortcode**: Use the provided shortcode `[display_weather]` within your pages or posts to display weather information.

## Configuration

- **Weather API Key**: Add your API key for weather data retrieval.
  To access the hourly forecast you'll need to subscribe
  to OpenWeather's One Call API 3.0. This API allows for 1000 free daily API calls. The plugin is setup to stay
  below this limit. Once you've subscribed to this service, enter your API key above.
  https://openweathermap.org/api/one-call-3.
- **Customization**: Customize the location and display name of the widget using the options in the admin menu.

## Features (v1.0.0)

- Display 24 hour weather forecasts on your WordPress site.
- Configure location for accurate weather data from OpenWeather's API.
- Shortcode integration for easy placement within content.

## FAQ

### How do I update the weather information?

Weather information updates automatically every 10 minutes. Ensure your API key is up-to-date for accurate data retrieval.

### Can I customize the appearance of the weather information?

At the moment, you can customise the display name on the widget for the location. In later versions customisation options will be added for other parts of the widget.

## Changelog

### Version 1.0.0 (Initial Release - 09/01/2024)

- Initial release.
- Added support for displaying weather information.
- Configurable settings for location and display options.

## License

This plugin is licensed under the [GNU General Public License v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).

For the full text of the GPL license, please visit [GNU GPL v2](https://www.gnu.org/licenses/gpl-2.0.html).