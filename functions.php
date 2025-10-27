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

/**
 * Theme Constants 
 */ 

if ( ! defined( 'WIREFRAME_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( 'WIREFRAME_VERSION', '1.0.0' );
}

/**
 * Post Formats
 */
// Adds theme support for post formats.
if ( ! function_exists( 'theme_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	function theme_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'theme_post_format_setup' );

/**
 * Assets 
 */ 
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
            'the-new-normal.min.css',
            'https://cdn.jsdelivr.net/gh/sarahschopick/the-new-normal.css@main/the-new-normal.min.css'
        );
        
        enqueue_css_with_fallback(
            'normalize-wordpress',
            'normalize-wordpress.min.css',
            'https://cdn.jsdelivr.net/gh/sarahschopick/normalize-wordpress@main/normalize-wordpress.min.css'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_theme_styles' );

// Enqueue CSS files from subfolders
if ( ! function_exists( 'enqueue_additional_css_files' ) ) {
    function enqueue_additional_css_files() {
        $css_folder = get_stylesheet_directory() . '/assets/css/';
        
        // Check if folder exists
        if ( ! is_dir( $css_folder ) ) {
            return;
        }
        
        try {
            // Get all CSS files including subfolders
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator( $css_folder, RecursiveDirectoryIterator::SKIP_DOTS ),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ( $iterator as $file ) {
                // Only process CSS files
                if ( $file->isFile() && $file->getExtension() === 'css' ) {
                    $file_path = $file->getPathname();
                    
                    // Get relative path from assets/css/
                    $relative_path = str_replace( $css_folder, '', $file_path );
                    $relative_path = str_replace( '\\', '/', $relative_path ); // Windows compatibility
                    
                    // Skip files in the root directory (only process files in subfolders)
                    if ( strpos( $relative_path, '/' ) === false ) {
                        continue;
                    }
                    
                    // Create unique handle from the relative path
                    $handle = 'custom-css-' . sanitize_title( str_replace( '/', '-', $relative_path ) );
                    
                    // Use theme_asset_url() for consistency
                    $file_url = theme_asset_url( 'css/' . $relative_path );
                    
                    wp_enqueue_style(
                        $handle,
                        $file_url,
                        array(), // dependencies
                        filemtime( $file_path ) // version based on file modification time
                    );
                }
            }
        } catch ( Exception $e ) {
            // Log error if directory iteration fails
            error_log( 'Error enqueuing CSS files: ' . $e->getMessage() );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'enqueue_additional_css_files', 20 );

/** 
 * Extensions
 */
if ( file_exists( get_stylesheet_directory() . '/src/Extensions/DarkMode.php' ) ) {
    require_once get_stylesheet_directory() . '/src/Extensions/DarkMode.php';
}

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
