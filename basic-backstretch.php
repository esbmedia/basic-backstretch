<?php
/**
 * Basic Backstretch
 *
 * @package   BasicBackstretch
 * @author    Devin Price
 * @license   GPL-2.0+
 * @link      http://wptheming.com/
 *
 * @wordpress-plugin
 * Plugin Name: Basic Backstretch
 * Plugin URI:  http://wptheming.com/
 * Description: Enables full screen background images.
 * Version:     0.1
 * Author:      Devin Price
 * Author URI:  http://www.wptheming.com/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

/**
 * Adds theme support for background images if not already present.
 *
 * @link http://codex.wordpress.org/Custom_Backgrounds
 */

function basic_backstretch_setup() {
	if ( ! get_theme_support( 'custom-background' ) )
		add_theme_support( 'custom-background' );
}

add_action( 'after_setup_theme', 'basic_backstretch_setup', 100 );

/**
 * Checks if a background image is present and if full screen background option is set.
 * If so, required scripts are loaded.
 */
function basic_backstretch() {

	if ( get_background_image() && get_option( 'basic-backstretch' ) ) {

		// Registers the backstretch script
		wp_register_script( 'basic-backstretch-js', plugin_dir_url( __FILE__ ) . '/jquery.backstretch.js', array('jquery'), '20130930', true );

		// Enqueues the script
		wp_enqueue_script( 'basic-backstretch-js' );

		// Adds a javascript object with the background image URL
		// This is used to load the image after other images on page have finished
		wp_localize_script( 'basic-backstretch-js', 'basicbackstretch', array(
			'background' => esc_url( get_background_image() )
		) );

		// Remove the background image from being included in inline styles
		// Uses http://codex.wordpress.org/Plugin_API/Filter_Reference/theme_mod_$name
		add_filter( 'theme_mod_background_image', 'backstretch_background_image_mod' );

		// Add script to load backstretch in the footer
		add_action( 'wp_footer', 'basic_backstretch_inline_script', 100 );

	}
}

add_action( 'wp_enqueue_scripts', 'basic_backstretch' );

/**
 * Keeps inline styles for the background image from outputting in the header
 *
 * Works similar to the pre_option filter
 * http://codex.wordpress.org/Plugin_API/Filter_Reference/pre_option_(option_name)
 */

function backstretch_background_image_mod() {
	return false;
}

/**
 * Inline script will load the full screen background image after all other images
 * on the page have loaded.
 */
function basic_backstretch_inline_script() { ?>
<script>
	jQuery( window ).load( function() {
		jQuery.backstretch(basicbackstretch.background, {speed: 300});
	});
</script>
<?php }

/**
 * Adds an option to the theme customizer for full screen backgrounds.
 * Disabled by default.
 */
function basic_backstretch_customizer_register( $wp_customize ) {

	// Add full screen background option
	$wp_customize->add_setting( 'basic-backstretch', array(
    	'default' => 10,
    	'type' => 'option'
    ) );

	// This will be hooked into the default background_image section
    $wp_customize->add_control( 'basic-backstretch', array(
    	'settings' => 'basic-backstretch',
		'label'    => __( 'Full Screen Background', 'textdomain' ),
		'section'  => 'background_image',
		'type'     => 'checkbox'
	) );
}

add_action( 'customize_register', 'basic_backstretch_customizer_register' );