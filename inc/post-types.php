<?php
/**
 * File for registering custom post types.
 *
 * @package    Slider
 * @subpackage Includes
 * @since      0.1.0
 * @author     Marty Helmick
 * @copyright  Copyright (c) 2013, Marty Helmick
 * @link       https://github.com/m-e-h/slider
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register custom post types on the 'init' hook. */
add_action( 'init', 'slider_register_post_types' );

/**
 * Registers post types needed by the plugin.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function slider_register_post_types() {

	/* Set up the arguments for the portfolio item post type. */
	$args = array(
		'description'         => '',
		'public'              => true,
		'publicly_queryable'  => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-align-center',
		'menu_position'       => 20,
		'can_export'          => true,
		'delete_with_user'    => false,
		'hierarchical'        => false,
		'has_archive'         => false,
		'query_var'           => 'slide',
		'capability_type'     => 'slide',
		'map_meta_cap'        => true,

		/* Only 3 caps are needed: 'manage_slider', 'create_slider', and 'edit_slider'. */
		'capabilities' => array(

			// meta caps (don't assign these to roles)
			'edit_post'              => 'edit_slide',
			'read_post'              => 'read_slide',
			'delete_post'            => 'delete_slide',

			// primitive/meta caps
			'create_posts'           => 'create_slider',

			// primitive caps used outside of map_meta_cap()
			'edit_posts'             => 'edit_slider',
			'edit_others_posts'      => 'manage_slider',
			'publish_posts'          => 'manage_slider',
			'read_private_posts'     => 'read',

			// primitive caps used inside of map_meta_cap()
			'read'                   => 'read',
			'delete_posts'           => 'manage_slider',
			'delete_private_posts'   => 'manage_slider',
			'delete_published_posts' => 'manage_slider',
			'delete_others_posts'    => 'manage_slider',
			'edit_private_posts'     => 'edit_slider',
			'edit_published_posts'   => 'edit_slider'
		),

		/* The rewrite handles the URL structure. */
		'rewrite' => false,

		/* What features the post type supports. */
		'supports' => array(
			'title',
			'editor',
			'excerpt',
			'thumbnail',
		),

		/* Labels used when displaying the posts. */
		'labels' => array(
			'name'               => __( 'Slider',                   'slider' ),
			'singular_name'      => __( 'Slider',                    'slider' ),
			'menu_name'          => __( 'Slider',                   'slider' ),
			'name_admin_bar'     => __( 'Slider',                    'slider' ),
			'add_new'            => __( 'Add New',                    'slider' ),
			'add_new_item'       => __( 'Add New Slider',            'slider' ),
			'edit_item'          => __( 'Edit Slider',               'slider' ),
			'new_item'           => __( 'New Slider',                'slider' ),
			'view_item'          => __( 'View Slider',               'slider' ),
			'search_items'       => __( 'Search Slider',            'slider' ),
			'not_found'          => __( 'No slider found',          'slider' ),
			'not_found_in_trash' => __( 'No slider found in trash', 'slider' ),
			'all_items'          => __( 'Slider',                   'slider' ),
		)
	);

	/* Register the portfolio item post type. */
	register_post_type( 'slide', $args );
}

?>
