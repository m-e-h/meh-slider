<?php
/**
 * Base class for creating displaying sets of slider (post type) on the front end.  This class isn't meant
 * to be used directly.  You should extend it was a sub-class.  Your sub-class must overwrite the format()
 * method.
 *
 * @package    Slider
 * @subpackage Includes
 * @since      0.1.0
 * @author     Marty Helmick
 * @copyright  Copyright (c) 2013, Marty Helmick
 * @link       https://github.com/m-e-h/slider
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Slider_And_Posts {

	/**
	 * Arguments passed in for getting slider.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array
	 */
	public $args = array();

	/**
	 * Gadget posts found from the query and formatted into an array.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    array
	 */
	public $slider = array();

	/**
	 * Formatted output of the set of slider.
	 *
	 * @since  0.1.0
	 * @access public
	 * @var    string
	 */
	public $markup = '';

	/**
	 * Constructor method.  Sets up everything.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array   $args
	 * @return void
	 */
	public function __construct( $args = array() ) {
		global $wp_embed;

		/* Use same default filters as 'the_content' with a little more flexibility. */
		add_filter( 'slide_content', array( $wp_embed, 'run_shortcode' ),   5 );
		add_filter( 'slide_content', array( $wp_embed, 'autoembed'     ),   5 );
		add_filter( 'slide_content',                   'wptexturize',       10 );
		add_filter( 'slide_content',                   'convert_smilies',   15 );
		add_filter( 'slide_content',                   'convert_chars',     20 );
		add_filter( 'slide_content',                   'wpautop',           25 );
		add_filter( 'slide_content',                   'do_shortcode',      30 );
		add_filter( 'slide_content',                   'shortcode_unautop', 35 );

		/* Set up the default arguments. */
		$defaults = array(
			'group'   => '',         // 'slide_group' term slug or term ID.
			'limit'   => -1,         // Display specific number of slider from group.
			'order'   => 'DESC',
			'orderby' => 'post_date',
		);

		$this->args = wp_parse_args( $args, $defaults );

		/* Set up the slider. */
		$this->set_slider();

		/* If there are any slider, set the HTML them. */
		if ( !empty( $this->slider ) )
			$this->markup = $this->set_markup( $this->slider );
	}

	/**
	 * Method for grabbing the array of slider queried.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return array
	 */
	public function get_slider() {
		return $this->slider;
	}

	/**
	 * Runs a posts query to grab the slider by the given group (required).  If slider are found, sets
	 * them up in an array of "array( 'id' => $post_id, 'title' => $post_title, 'content' => $post_content )".
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function set_slider() {

		/* If no group was given, don't set any slider. */
		if ( empty( $this->args['group'] ) )
			return;

		/* Query the slider by slide group. */
		$loop = new WP_Query(
			array(
				'post_type'      => 'slide',
				'posts_per_page' => $this->args['limit'],
				'order'          => $this->args['order'],
				'orderby'        => $this->args['orderby'],
				'tax_query'      => array(
					array(
						'taxonomy' => 'slide_group',
						'field'    => is_int( $this->args['group'] ) ? 'id' : 'slug',
						'terms'    => array( $this->args['group'] )
					)
				),
			)
		);

		while ( $loop->have_posts() ) {

			$loop->the_post();


			$thumb_id = get_post_thumbnail_id();
			$thumb_url = wp_get_attachment_image_src($thumb_id, 'slider-jumbo');

			$this->slider[] = array(
				'id'        => get_the_ID(),
				'title'     => get_the_title(),
				'excerpt'   => get_the_excerpt(),
				'link'      => get_permalink(),
				'thumbnail' => get_the_post_thumbnail(get_the_ID(), 'large' ),
				'thumb_url' => $thumb_url[0],
				'thumbnail_square' => get_the_post_thumbnail(get_the_ID(), 'slide-square' ),
				'content'   => apply_filters( 'slide_content', get_post_field( 'post_content', get_the_ID() ) )
			);
		}

		/* Reset the original post data. */
		wp_reset_postdata();
	}

	/**
	 * Return the HTML markup for display.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return string
	 */
	public function get_markup() {
		return $this->markup;
	}


	/**
	 * Sets the HTML markup for display.  Expects the $slider property to be passed in.
	 *
	 * Important!  This method must be overwritten in a sub-class.  Your sub-class should return an
	 * HTML-formatted string of the $slider array.
	 *
	 * @since  0.1.0
	 * @access public
	 * @param  array  $slider
	 * @return string
	 */
	public function set_markup( $slider ) {
		wp_die( sprintf( __( 'The %s method must be overwritten in a sub-class.', 'slider' ), '<code>' . __METHOD__ . '</code>' ) );
	}
}

?>
