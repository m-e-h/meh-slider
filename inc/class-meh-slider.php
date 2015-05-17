<?php
/**
 * Slider_And_Sliders class.  Extends the Slider_And_Posts class to format the slide posts into
 * a group of sliders.
 *
 * @package    Slider
 * @subpackage Includes
 * @since      0.1.0
 * @author     Marty Helmick
 * @copyright  Copyright (c) 2013, Marty Helmick
 * @link       https://github.com/m-e-h/slider
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class Slider_And_Sliders extends Slider_And_Posts {

	/**
	 * Custom markup for the ouput of sliders.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array   $slider
	 * @return string
	 */
	public function set_markup( $slider ) {

		/* Load custom JavaScript for sliders unless the current theme is handling it. */
		if ( !current_theme_supports( 'slider', 'scripts' ) )
			wp_enqueue_script( 'slider' );

		/* Set up an empty string to return. */
		$output = '';

		/* If we have slider, let's roll! */
		if ( !empty( $slider ) ) {

			/* Open the slider wrapper. */

			$output .= '<div class="meh-slider js-flickity">';

			/* Loop through each of the slider and format the output. */
			foreach ( $slider as $slide ) {

				// $output .= '<li class="slide-title">' . $slide['title'] . '</li>';

				$output .= '<img src="' . $slide['thumb_url'] . '"/>';

				// $output .= '<div class="slide-content">' . $slide['content'] . '</div>';
			}

			/* Close the slider wrapper. */
			$output .= '</div>';
		}

		/* Return the formatted output. */
		return $output;
	}
}

?>
