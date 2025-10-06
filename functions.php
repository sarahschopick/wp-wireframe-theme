<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme Constants 
 */ 

// Assets 
if ( ! function_exists( 'theme_asset_url' ) ) {
    function theme_asset_url( $path ) {
        return trailingslashit( get_stylesheet_directory_uri() ) . 'assets/' . ltrim( $path, '/' );
    }
}

if ( ! function_exists( 'the_theme_asset_url' ) ) {
    function the_theme_asset_url( $path ) {
        echo esc_url( theme_asset_url( $path ) );
    }
}

/**
 * Styles 
 */ 

// Enqueue CSS with local/remote fallback
if ( ! function_exists( 'enqueue_css_with_fallback' ) ) {
    function enqueue_css_with_fallback( $handle, $local_path, $remote_url ) {
        $local_file = get_stylesheet_directory() . '/assets/css/' . $local_path;
        
        if ( file_exists( $local_file ) ) {
            wp_enqueue_style( 
                $handle, 
                theme_asset_url( 'css/' . $local_path ), 
                [], 
                filemtime( $local_file )
            );
        } else {
            wp_enqueue_style( 
                $handle, 
                $remote_url, 
                [], 
                floor( time() / 3600 )
            );
        }
    }
}

if ( ! function_exists( 'enqueue_theme_styles' ) ) {
    function enqueue_theme_styles() {
        enqueue_css_with_fallback(
            'the-new-normal',
            'the-new-normal.css',
            'https://cdn.jsdelivr.net/gh/sarahschopick/the-new-normal.css@main/the-new-normal.min.css'
        );
        
        enqueue_css_with_fallback(
            'normalize-wordpress',
            'normalize-wordpress.css',
            'https://cdn.jsdelivr.net/gh/sarahschopick/normalize-wordpress@main/normalize-wordpress.min.css'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_theme_styles' );

// Default Color Mode
if ( ! function_exists( 'theme_default_color_mode' ) ) {
	function theme_default_color_mode() {
		return 'system';
	}
}
add_filter( 'plover_theme_default_color_mode', 'theme_default_color_mode' );

// Extend dark mode color mappings
function theme_extend_dark_mode_mappings() {
    add_filter('plover_theme_editor_data', function($data) {
        if (!isset($data['darkMode']['shadeMap'])) {
            return $data;
        }
        
        // Add your custom color mappings here
        $custom_mappings = [
            'secondary' => [
                'color'  => 'active',
                'active' => 'color',
            ],
            'hover' => [
                'color'  => 'active',
                'active' => 'color',
            ],
        ];
        
        $data['darkMode']['shadeMap'] = array_merge(
            $data['darkMode']['shadeMap'], 
            $custom_mappings
        );
        
        return $data;
    });
}

add_action('after_setup_theme', 'theme_extend_dark_mode_mappings');

/**
 * Theme block patterns
 */
if ( ! function_exists( 'theme_register_block_pattern_category' ) ) {
	/**
	 * Register theme pattern category
	 * 
	 * @return void
	 */
	function theme_register_block_pattern_category() {
		if ( function_exists( 'register_block_pattern_category' ) ) {
			register_block_pattern_category( 'wireframe', array(
				'label' => __( 'Wireframe', 'wireframe' )
			) );
		}
	}
}

add_action( 'init', 'theme_register_block_pattern_category' );


/**
 * Theme template areas
 */
function theme_template_part_areas( array $areas ) {
	$areas[] = array(
		'area' => 'posts',
		'area_tag' => 'section',
		'label' => __( 'Posts', 'wireframe' ),
		'description' => __( 'Displaying posts.', 'wireframe' ),
		'icon' => 'layout'
	);

	$areas[] = array(
		'area' => 'sidebar',
		'area_tag' => 'aside',
		'label' => __( 'Sidebar', 'wireframe' ),
		'description' => __( 'Sidebar', 'wireframe' ),
		'icon' => 'layout'
	);

	return $areas;
}

add_filter( 'default_wp_template_part_areas', 'theme_template_part_areas' );

/** 
 * Theme dashboard hooks 
 */

// Theme screenshot
if ( ! function_exists( 'theme_screenshot' ) ) {
    function theme_screenshot() {
        return trailingslashit( get_stylesheet_directory_uri() ) . 'screenshot.png';
    }
}
add_filter( 'plover_welcome_theme_screenshot', 'theme_screenshot' );
