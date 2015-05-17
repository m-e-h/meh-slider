<?php
/**
 * File for registering custom taxonomies.
 *
 * @package    Slider
 * @subpackage Includes
 * @since      0.1.0
 * @author     Marty Helmick
 * @copyright  Copyright (c) 2013, Marty Helmick
 * @link       https://github.com/m-e-h/slider
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Register taxonomies on the 'init' hook. */
add_action( 'init', 'slider_register_taxonomies' );

/**
 * Register taxonomies for the plugin.
 *
 * @since  0.1.0
 * @access public
 * @return void.
 */
function slider_register_taxonomies() {

	/* Set up the arguments for the portfolio taxonomy. */
	$args = array(
		'public'            => false,
		'show_ui'           => true,
		'show_in_nav_menus' => false,
		'show_tagcloud'     => true,
		'show_admin_column' => true,
		'hierarchical'      => false,
		'query_var'         => 'slide_group',

		/* Only 2 caps are needed: 'manage_portfolio' and 'edit_portfolio_items'. */
		'capabilities' => array(
			'manage_terms' => 'manage_slider',
			'edit_terms'   => 'manage_slider',
			'delete_terms' => 'manage_slider',
			'assign_terms' => 'edit_slider',
		),

		/* The rewrite handles the URL structure. */
		'rewrite' => false,

		/* Labels used when displaying taxonomy and terms. */
		'labels' => array(
			'name'                       => __( 'Slide Groups',                           'slider' ),
			'singular_name'              => __( 'Slide Group',                            'slider' ),
			'menu_name'                  => __( 'Slide Groups',                           'slider' ),
			'name_admin_bar'             => __( 'Slide Group',                            'slider' ),
			'search_items'               => __( 'Search Slide Groups',                    'slider' ),
			'popular_items'              => __( 'Popular Slide Groups',                   'slider' ),
			'all_items'                  => __( 'All Slide Groups',                       'slider' ),
			'edit_item'                  => __( 'Edit Slide Group',                       'slider' ),
			'view_item'                  => __( 'View Slide Group',                       'slider' ),
			'update_item'                => __( 'Update Slide Group',                     'slider' ),
			'add_new_item'               => __( 'Add New Slide Group',                    'slider' ),
			'new_item_name'              => __( 'New Slide Group Name',                   'slider' ),
			'separate_items_with_commas' => __( 'Separate slide groups with commas',      'slider' ),
			'add_or_remove_items'        => __( 'Add or remove slide groups',             'slider' ),
			'choose_from_most_used'      => __( 'Choose from the most used slide groups', 'slider' ),
		)
	);

	/* Register the 'portfolio' taxonomy. */
	register_taxonomy( 'slide_group', array( 'slide' ), $args );
}

?>
