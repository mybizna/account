<?php

namespace Modules\Account\Classes;

use Illuminate\Support\Facades\DB;

/**
 * WP ERP countries
 *
 * The WP ERP countries class stores country/state data.
 *
 * @category    i18n
 */
class Countries
{

    /** @var array Array of locales */
    public $locale;

    /** @var array Array of address formats for locales */
    public $address_formats;

    /**
     * Initializes the Countries() class
     *
     * Checks for an existing Countries() instance
     * and if it doesn't find one, creates it.
     */
    public static function instance()
    {
        static $instance = false;

        if (!$instance) {
            $instance = new self();
        }

        return $instance;
    }


    /**
     * Get all countries
     *
     * @return array
     */
    public function get_countries($default = '')
    {
        $countries = DB::table('core_country')->get();

        return $countries;
    }

    /**
     * Load the states
     */
    public function load_country_states($cc = null)
    {
        $states = [];

        if ($cc) {
            $states = DB::table('core_state')->where('country_code', $cc)->get();
        }

        return $states;
    }

    /**
     * Get the states for a country.
     *
     * @param string $cc country code
     *
     * @return array of states
     */
    public function get_states($cc = null)
    {
        $states = [];

        if ($cc) {
            $states = DB::table('core_state')->where('country_code', $cc)->get();
        }

        return $states;
    }

    /**
     * Get the base country for the store.
     *
     * @return string
     */
    public function get_core_country()
    {
        $cc = config('account.core_country');

        $country = DB::table('core_country')->where('country_code', $cc)->frst();

        return apply_filters('countries_core_country', $country);
    }

    /**
     * Generate a country dropdown
     *
     * @param  string  selected country
     *
     * @return string the country dropdown
     */
    public function country_dropdown($selected = '')
    {
        $dropdown  = sprintf('<option value="-1">%s</option>', __('- Select -'));
        $countries = $this->get_countries();
        $selected  = empty($selected) ? $this->get_core_country() : $selected;

        foreach ($countries as $key => $value) {
            $select = ($key == $selected) ? ' selected="selected"' : '';
            $dropdown .= sprintf("<option value='%s'%s>%s</option>\n", $key, $select, $value);
        }

        return $dropdown;
    }

    /**
     * Outputs the list of countries and states for use in dropdown boxes.
     *
     * @param string $selected_country (default: '')
     * @param string $selected_state   (default: '')
     * @param bool   $escape           (default: false)
     * @param bool   $escape           (default: false)
     */
    public function country_dropdown_options($selected_country = '', $selected_state = '', $escape = false)
    {
        $html = '';

        if ($this->countries) {
            foreach ($this->countries as $key => $value) {
                if ($states = $this->get_states($key)) {
                    $html .= '<optgroup label="' . esc_attr($value) . '">';
                    $html .= '<option value="' . esc_attr($key) . '">' . $value . ' &mdash; ' . __('Any State') . '</option>';

                    foreach ($states as $state_key => $state_value) {
                        $html .= '<option value="' . esc_attr($key) . ':' . $state_key . '"';

                        if ($selected_country == $key && $selected_state == $state_key) {
                            $html .= ' selected="selected"';
                        }

                        $html .= '>' . $value . ' &mdash; ' . ($escape ? $state_value : $state_value) . '</option>';
                    }
                    $html .= '</optgroup>';
                } else {
                    $html .= '<option';

                    if ($selected_country == $key && $selected_state == '*') {
                        $html .= ' selected="selected"';
                    }
                    $html .= ' value="' . esc_attr($key) . '">' . ($escape ? $value : $value) . '</option>';
                }
            }
        }

        return $html;
    }

    /**
     * Get country address formats
     *
     * @return array
     */
    public function get_address_formats()
    {
        if (!$this->address_formats) {
            // Common formats
            $postcode_before_city = "{address_1}\n{address_2}\n{postcode} {city}\n{country}";

            // Define address formats
            $this->address_formats = apply_filters('woocommerce_localisation_address_formats', [
                'default' => "{address_1}\n{address_2}\n{city}\n{state}\n{postcode}\n{country}",
                'AU'      => "{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
                'AT'      => $postcode_before_city,
                'BE'      => $postcode_before_city,
                'CA'      => "{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
                'CH'      => $postcode_before_city,
                'CN'      => "{country} {postcode}\n{state}, {city}, {address_2}, {address_1}\n{name}",
                'CZ'      => $postcode_before_city,
                'DE'      => $postcode_before_city,
                'EE'      => $postcode_before_city,
                'FI'      => $postcode_before_city,
                'DK'      => $postcode_before_city,
                'FR'      => "{address_1}\n{address_2}\n{postcode} {city_upper}\n{country}",
                'HK'      => "{first_name} {last_name_upper}\n{address_1}\n{address_2}\n{city_upper}\n{state_upper}\n{country}",
                'HU'      => "{city}\n{address_1}\n{address_2}\n{postcode}\n{country}",
                'IS'      => $postcode_before_city,
                'IT'      => "{address_1}\n{address_2}\n{postcode}\n{city}\n{state_upper}\n{country}",
                'JP'      => "{postcode}\n{state}{city}{address_1}\n{address_2}\n{last_name} {first_name}\n {country}",
                'TW'      => "{postcode}\n{city}{address_2}\n{address_1}\n{last_name} {first_name}\n {country}",
                'LI'      => $postcode_before_city,
                'NL'      => $postcode_before_city,
                'NZ'      => "{address_1}\n{address_2}\n{city} {postcode}\n{country}",
                'NO'      => $postcode_before_city,
                'PL'      => $postcode_before_city,
                'SK'      => $postcode_before_city,
                'SI'      => $postcode_before_city,
                'ES'      => "{address_1}\n{address_2}\n{postcode} {city}\n{state}\n{country}",
                'SE'      => $postcode_before_city,
                'TR'      => "{address_1}\n{address_2}\n{postcode} {city} {state}\n{country}",
                'US'      => "{address_1}\n{address_2}\n{city}, {state_code} {postcode}\n{country}",
                'VN'      => "{address_1}\n{city}\n{country}",
            ]);
        }

        return $this->address_formats;
    }

    /**
     * Get country address format
     *
     * @param array $args (default: array())
     *
     * @return string address
     */
    public function get_formatted_address($args = [])
    {
        $args = array_map('trim', $args);

        extract($args);

        // Get all formats
        $formats      = $this->get_address_formats();

        // Get format for the address' country
        $format       = ($country && isset($formats[$country])) ? $formats[$country] : $formats['default'];

        // Handle full country name
        $full_country = (isset($this->countries[$country])) ? $this->countries[$country] : $country;

        // Handle full state name
        $full_state   = ($country && $state && isset($this->states[$country][$state])) ? $this->states[$country][$state] : $state;

        // Substitute address parts into the string
        $replace = array_map('esc_html', apply_filters(
            'formatted_address_replacements',
            [
                '{address_1}'        => $address_1,
                '{address_2}'        => $address_2,
                '{city}'             => $city,
                '{state}'            => $full_state,
                '{postcode}'         => $postcode,
                '{country}'          => $full_country,
                '{address_1_upper}'  => strtoupper($address_1),
                '{address_2_upper}'  => strtoupper($address_2),
                '{city_upper}'       => strtoupper($city),
                '{state_upper}'      => strtoupper($full_state),
                '{state_code}'       => strtoupper($state),
                '{postcode_upper}'   => strtoupper($postcode),
                '{country_upper}'    => strtoupper($full_country),
            ],
            $args
        ));

        $formatted_address = str_replace(array_keys($replace), $replace, $format);

        // Clean up white space
        $formatted_address = preg_replace('/  +/', ' ', trim($formatted_address));
        $formatted_address = preg_replace('/\n\n+/', "\n", $formatted_address);

        // Break newlines apart and remove empty lines/trim commas and white space
        $formatted_address = array_filter(array_map([$this, 'trim_formatted_address_line'], explode("\n", $formatted_address)));

        // Add html breaks
        $formatted_address = implode('<br/>', $formatted_address);

        // We're done!
        return $formatted_address;
    }

    /**
     * trim white space and commans off a line
     *
     * @param  string
     *
     * @return string
     */
    private function trim_formatted_address_line($line)
    {
        return trim($line, ', ');
    }
}
