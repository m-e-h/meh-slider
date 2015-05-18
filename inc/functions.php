<?php
/**
 * Functions, filters, and actions for the plugin.
 *
 * @package    Slider
 * @subpackage Includes
 * @since      0.1.0
 * @author     Marty Helmick
 * @copyright  Copyright (c) 2013, Marty Helmick
 * @link       https://github.com/m-e-h/slider
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register shortcodes. */
add_action( 'init', 'slider_register_shortcodes' );

/* Register widgets. */
add_action( 'widgets_init', 'slider_register_widgets' );

/**
 * Function for returning the allowed slider types for display.
 *
 * @since  0.1.0
 * @access public
 * @return array
 */
function slider_get_allowed_types() {

	$allowed_types = array(
		'slider' 		=> __( 'Slider', 'slider' )
	);

	return apply_filters( 'slider_allowed_types', $allowed_types );
}


add_filter('image_size_names_choose', 'slider_show_image_sizes');

function slider_add_image_sizes() {
    add_image_size( 'slider-jumbo', 1920, 600, true );
}
add_action( 'init', 'slider_add_image_sizes' );

function slider_show_image_sizes($sizes) {
    $sizes['slider-jumbo'] = __( 'Slider', 'slider' );

    return $sizes;
}

/**
 * Wrapper function for outputting sliders.  You can call one of the classes directly, but it's best to use
 * this function if needed within a theme template.
 *
 * @since  0.1.0
 * @access public
 * @return string
 */
function slider_get_slider( $args = array() ) {

	/* Allow types other than 'slider' or 'toggle'. */
	$allowed = array_keys( slider_get_allowed_types() );

	/* Clean up the type and allow typos of 'slider' and 'toggle'. */
	$args['type'] = sanitize_key( strtolower( $args['type'] ) );

	if ( 'slider' === $args['type'] )
		$args['type'] = 'slider';

	elseif ( 'toggles' === $args['type'] )
		$args['type'] = 'toggle';

	/* ================================== */

	/* Only allow a 'type' from the $allowed_types array. */
	$type = $args['type'] = ( isset( $args['type'] ) && in_array( $args['type'], $allowed ) ) ? $args['type'] : 'slider';

	/**
	 * Developers can overwrite the slider object at this point.  This is basically to bypass the
	 * plugin's classes and use your own.  You must return an object, not a class name.  This object
	 * must also have a method named "get_markup()" for returning the HTML markup.  It's best to simply
	 * extend Slider_And_Posts and follow the structure outlined in that class.
	 */
	$slider_object = apply_filters( 'slider_object', null, $args );

	/* If no object was returned, use one of the plugin's defaults. */
	if ( !is_object( $slider_object ) ) {

		/* Accordion. */
		if ( 'slider' === $type )
			$slider_object = new Slider_And_Sliders( $args );
	}

	/* Return the HTML markup. */
	return $slider_object->get_markup();
}

/**
 * Registers the [slider] shortcode.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function slider_register_shortcodes() {
	add_shortcode( 'slider', 'slider_do_shortcode' );
}

/**
 * Regisers the "Slider" widget.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function slider_register_widgets() {

	require_once( SLIDER_DIR . 'inc/class-slider-widget.php' );

	register_widget( 'SLIDER_WIDGET' );
}

/**
 * Shortcode function.  This is just a wrapper for slider_get_slider().
 *
 * @since  0.1.0
 * @access public
 * @return string
 */
function slider_do_shortcode( $attr ) {
	return slider_get_slider( $attr );
}

?>
