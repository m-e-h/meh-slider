<?php
/**
 * Plugin Name: MEH Slider
 * Plugin URI: https://github.com/m-e-h/meh-slider
 * Description: A slider plugin.
 * Version: 0.1.0
 * Author: Marty Helmick
 */

final class Slider_Load {

	/**
	 * Plugin setup.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public static function setup() {

		/* Set the constants needed by the plugin. */
		add_action( 'plugins_loaded', array( __CLASS__, 'constants' ), 1 );

		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( __CLASS__, 'i18n' ), 2 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( __CLASS__, 'includes' ), 3 );

		/* Load the admin files. */
		add_action( 'plugins_loaded', array( __CLASS__, 'admin' ), 4 );

		/* Enqueue scripts and styles. */
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		/* Filter current_theme_supports for easier conditionals. */
		add_filter( 'current_theme_supports-slider', array( __CLASS__, 'current_theme_supports' ), 10, 3 );

		/* Register activation hook. */
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
	}

	/**
	 * Defines constants used by the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public static function constants() {

		/* Set constant path to the plugin directory. */
		define( 'SLIDER_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

		/* Set the constant path to the plugin directory URI. */
		define( 'SLIDER_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public static function includes() {

		require_once( SLIDER_DIR . 'inc/post-types.php' );
		require_once( SLIDER_DIR . 'inc/taxonomies.php' );
		require_once( SLIDER_DIR . 'inc/class-slider-and-posts.php' );
		require_once( SLIDER_DIR . 'inc/class-meh-slider.php' );
		require_once( SLIDER_DIR . 'inc/functions.php' );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public static function i18n() {

		/* Load the translation of the plugin. */
		load_plugin_textdomain( 'slider', false, 'meh-slider/languages' );
	}

	/**
	 * Loads the admin functions and files.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public static function admin() {

		if ( is_admin() )
			require_once( SLIDER_DIR . 'admin/admin.php' );
	}

	/**
	 * Loads the stylesheet for the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public static function enqueue_scripts() {

		/* Use the .min stylesheet if SCRIPT_DEBUG is turned off. */
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

				/* Register the JS file. Load later if needed. */
		wp_register_script(
			'flickity',
			SLIDER_URI . 'js/flickity.pkgd.min.js',
			array(),
			null,
			true
		);

		/* Register the JS file. Load later if needed. */
		wp_register_script(
			'meh-slider',
			SLIDER_URI . 'js/mehslider.js',
			array(),
			null,
			true
		);

		wp_enqueue_script( 'flickity' );
		//wp_enqueue_script( 'meh-slider' );


		/* Enqueue the stylesheet. */
		wp_enqueue_style(
			'flickitycss',
			SLIDER_URI . "css/flickity.css"
		);

		if ( current_theme_supports( 'meh-slider', 'styles' ) )
			return;

		/* Enqueue the stylesheet. */
		wp_enqueue_style(
			'meh-slider',
			SLIDER_URI . "css/meh-slider.css"
		);

	}

	/**
	 * Filter on 'current_theme_supports-slider'.  This allows us to check if the theme supports specific
	 * parts of the slider plugins like 'scripts' and 'styles'.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  bool    $supports
	 * @param  array   $args
	 * @param  array   $feature
	 * @return bool
	 */
	public static function current_theme_supports( $supports, $args, $feature ) {

		if ( isset( $args[0] ) ) {

			$check = $args[0];

			if ( in_array( $check, array( 'scripts', 'styles' ) ) ) {

				if ( is_array( $feature[0] ) && isset( $feature[0][ $check ] ) && true === $feature[0][ $check ] )
					$supports = true;
				else
					$supports = false;
			}
		}

		return $supports;
	}

	/**
	 * Method that runs only when the plugin is activated.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public static function activation() {

		/* Get the administrator role. */
		$role = get_role( 'administrator' );

		/* If the administrator role exists, add required capabilities for the plugin. */
		if ( !empty( $role ) ) {

			$role->add_cap( 'manage_slider' );
			$role->add_cap( 'create_slider' );
			$role->add_cap( 'edit_slider'   );
		}
	}
}

Slider_Load::setup();

?>
