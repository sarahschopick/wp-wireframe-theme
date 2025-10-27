<?php
/**
 * Wireframe bootstrap
 *
 * @package Wireframe
 *
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Default Color Mode
if ( ! function_exists( 'theme_default_color_mode' ) ) {
	function theme_default_color_mode() {
		return 'system';
	}
}
add_filter( 'plover_theme_default_color_mode', 'theme_default_color_mode' );

// Extend dark mode color mappings
function theme_extend_dark_mode_mappings() {
    /**
     * Default mappings from Plover
     * 
     * @var array
     *
     * $map = [
     *  'primary' => [
     *      'color'  => 'active',
     *      'active' => 'color',
     *  ],
     *  'neutral' => [
     *      950 => 0,
     *      800 => 200,
     *      600 => 400,
     *      400 => 600,
     *      200 => 800,
     *      0   => 950,
     *  ]
     * ];
     */

    add_filter('plover_theme_editor_data', function($data) {
        // Check if we have the data we need
        if (empty($data['darkMode']['colors'])) {
            return $data;
        }

        // Define how colors should swap in dark mode
        $mapping = [
            'color'  => 'active',
            'active' => 'color',
            'light'  => 'dark',
            'dark' => 'light',
            950 => 0,
            800 => 200,
            600 => 400,
            400 => 600,
            200 => 800,
            0   => 950,
        ];

        // Apply mapping to each color in the palette
        foreach ($data['darkMode']['colors'] as $slug => $color_value) {
            // Get color name (e.g., "secondary" from "secondary-400")
            $color_name = explode('-', $slug)[0];
            
            // Add mapping if it doesn't exist yet
            if (!isset($data['darkMode']['shadeMap'][$color_name])) {
                $data['darkMode']['shadeMap'][$color_name] = $mapping;
            }
        }

        return $data;
    }, 20);
}

add_action('after_setup_theme', 'theme_extend_dark_mode_mappings');