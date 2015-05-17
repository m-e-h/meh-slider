<?php
/**
 * Admin functions for the plugin.
 *
 * @package    Slider
 * @subpackage Admin
 * @since      0.1.0
 * @author     Marty Helmick
 * @copyright  Copyright (c) 2013, Marty Helmick
 * @link       https://github.com/m-e-h/slider
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Set up the admin functionality. */
add_action( 'admin_menu', 'slider_admin_menu' );

/* Fixes the parent file. */
add_filter( 'parent_file', 'slider_parent_file' );

/* Adds a custom media button on the post editor. */
add_action( 'media_buttons', 'slider_media_buttons', 11 );

/* Loads media button popup content in the footer. */
add_action( 'admin_footer-post-new.php', 'slider_editor_shortcode_popup' );
add_action( 'admin_footer-post.php',     'slider_editor_shortcode_popup' );

/**
 * Creates admin sub-menu items under the "Appearance" screen in the admin.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function slider_admin_menu() {

	/* Get the slide post type object. */
	$post_type = get_post_type_object( 'slide' );

	/* Add the slide post type admin sub-menu. */
	add_theme_page(
		$post_type->labels->name,
		$post_type->labels->menu_name,
		$post_type->cap->edit_posts,
		'edit.php?post_type=slide'
	);

	/* Get the slide group taxonomy object. */
	$taxonomy = get_taxonomy( 'slide_group' );

	/* Add the slide group sub-menu page. */
	add_theme_page(
		$taxonomy->labels->name,
		$taxonomy->labels->menu_name,
		$taxonomy->cap->manage_terms,
		'edit-tags.php?taxonomy=slide_group&amp;post_type=slide'
	);
}

/**
 * Corrects the parent menu item in the admin menu since we're displaying our admin screens in a custom area.
 *
 * @since  0.1.0
 * @access public
 * @param  string  $parent_file
 * @global object  $current_screen
 * @return string
 */
function slider_parent_file( $parent_file ) {
	global $current_screen, $self;

	/* Fix the parent file when viewing the Slider or New Slide screen in the admin. */
	if ( in_array( $current_screen->base, array( 'post', 'edit' ) ) && 'slide' === $current_screen->post_type ) {
		$parent_file = 'themes.php';
	}

	/* Fix the parent and self file when viewing the Slide Groups screen in the admin. */
	elseif ( 'slide_group' === $current_screen->taxonomy ) {
		$parent_file = 'themes.php';
		$self        = 'edit-tags.php?taxonomy=slide_group&amp;post_type=slide';
	}

	return $parent_file;
}

/**
 * Displays a link to the Thickbox popup containing the shortcode config popup on the edit post screen.
 *
 * @since  0.1.0
 * @access public
 * @param  string  $editor_id
 * @return void
 */
function slider_media_buttons( $editor_id ) {
	global $post;

	if ( !current_user_can( 'edit_slider' ) )
		return;

	if ( is_object( $post ) && !empty( $post->post_type ) && 'slide' !== $post->post_type )
		echo '<a href="#TB_inline?width=200&amp;height=530&amp;inlineId=slider-shortcode-popup" class="button-secondary thickbox" data-editor="' . esc_attr( $editor_id ) . '" title="' . esc_attr__( 'Add Slider' ) . '">' . __( 'Add Slider' ) . '</a>';
}

/**
 * Shortcode config popup when the "Add Slider" media button is clicked.
 *
 * @since  0.1.0
 * @access public
 * @return void
 */
function slider_editor_shortcode_popup() {

	if ( !current_user_can( 'edit_slider' ) )
		return;

	$type = slider_get_allowed_types();

	$terms = get_terms( 'slide_group' );

	if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
		$all_terms = $terms;
		$default_term = array_shift( $all_terms );
		$default_term = $default_term->slug;
	} else {
		$default_term = '';
	}

	/* Create an array of order options. */
	$order = array(
		'ASC'  => esc_attr__( 'Ascending',  'slider' ),
		'DESC' => esc_attr__( 'Descending', 'slider' )
	);

	/* Create an array of orderby options. */
	$orderby = array(
		'author' => esc_attr__( 'Author', 'slider' ),
		'date'   => esc_attr__( 'Date',   'slider' ),
		'ID'     => esc_attr__( 'ID',     'slider' ),
		'rand'   => esc_attr__( 'Random', 'slider' ),
		'name'   => esc_attr__( 'Slug',   'slider' ),
		'title'  => esc_attr__( 'Title',  'slider' ),
	);

	?>
	<script>
		jQuery( document ).ready(

			function() {

				jQuery( '#slider-submit' ).attr(
					'value',
					'<?php echo esc_js( __( 'Insert', 'slider' ) ); ?> ' + jQuery( 'input:radio[name=slider-type]:checked + label' ).text()
				);

				jQuery( 'input:radio[name=slider-type]' ).change(
					function() {
						jQuery( '#slider-submit' ).attr(
							'value',
							'<?php echo esc_js( __( 'Insert', 'slider' ) ); ?> ' + jQuery( this ).next( 'label' ).text()
						);
					}
				);
			}
		);

		function slider_insert_shortcode(){
			var type    = jQuery( 'input:radio[name=slider-type]:checked' ).val();
			var group   = jQuery( 'select#slider-id-group option:selected' ).val();
			var order   = jQuery( 'select#slider-id-order option:selected' ).val();
			var orderby = jQuery( 'select#slider-id-orderby option:selected' ).val();
			var limit   = jQuery( 'input#slider-id-limit' ).val();

			window.send_to_editor(
				'[slider type="' + type + '" group="' + group + '" order="' + order + '" orderby="' + orderby + '" limit="' + limit + '"]'
			);
		}
	</script>

	<div id="slider-shortcode-popup" style="display:none;">

		<div class="wrap">

		<?php if ( empty( $terms ) ) { ?>
			<p>
				<?php _e( 'You need at least one slide group to display slider.', 'slider' ); ?>
				<?php if ( current_user_can( 'manage_slider' ) ) { ?>
					<a href="<?php echo admin_url( 'edit-tags.php?taxonomy=slide_group&post_type=slide' ); ?>"><?php _e( 'Slide Groups &rarr;', 'slider' ); ?></a>
				<?php } ?>
			</p>
			<p class="submitbox">
				<a class="button-secondary" href="#" onclick="tb_remove(); return false;"><?php _e( 'Cancel', 'slider' ); ?></a>
			</p>
		<?php } else { ?>
			<p>
				<?php _e( 'Type', 'slider' ); ?>
				<?php foreach ( $type as $option_value => $option_label ) { ?>
					<br />
					<input type="radio" name="slider-type" id="<?php echo esc_attr( 'slider-id-type-' . $option_value ); ?>" value="<?php echo esc_attr( $option_value ); ?>" <?php checked( 'tabs', $option_value ); ?> />
					<label for="<?php echo esc_attr( 'slider-id-type-' . $option_value ); ?>"><?php echo esc_html( $option_label ); ?></label>
				<?php } ?>
			</p>

			<p>
				<label for="<?php echo esc_attr( 'slider-id-group' ); ?>"><?php _e( 'Group', 'slider' ); ?></label>
				<br />
				<select class="widefat" id="<?php echo esc_attr( 'slider-id-group' ); ?>" name="<?php echo esc_attr( 'slider-name-group' ); ?>">
					<?php foreach ( $terms as $term ) { ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $default_term, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
					<?php } ?>
				</select>
			</p>

			<p>
				<label for="<?php echo esc_attr( 'slider-id-limit' ); ?>"><?php _e( 'Number of slider to display', 'slider' ); ?></label>
				<input type="text" maxlength="3" size="3" class="code" id="<?php echo esc_attr( 'slider-id-limit' ); ?>" name="<?php echo esc_attr( 'slider-name-limit' ); ?>" value="-1" />
			</p>
			<p>
				<label for="<?php echo esc_attr( 'slider-id-order' ); ?>"><?php _e( 'Order', 'slider' ); ?></label>
				<br />
				<select class="widefat" id="<?php echo esc_attr( 'slider-id-order' ); ?>" name="<?php echo esc_attr( 'slider-name-order' ); ?>">
					<?php foreach ( $order as $option_value => $option_label ) { ?>
						<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( 'DESC', $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
					<?php } ?>
				</select>
			</p>
			<p>
				<label for="<?php echo esc_attr( 'slider-id-orderby' ); ?>"><?php _e( 'Order By', 'slider' ); ?></label>
				<br />
				<select class="widefat" id="<?php echo esc_attr( 'slider-id-orderby' ); ?>" name="<?php echo esc_attr( 'slider-name-orderby' ); ?>">
					<?php foreach ( $orderby as $option_value => $option_label ) { ?>
						<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( 'date', $option_value ); ?>><?php echo esc_html( $option_label ); ?></option>
					<?php } ?>
				</select>
			</p>

			<p class="submitbox">
				<input type="submit" id="slider-submit" value="<?php esc_attr_e( 'Insert Slider', 'slider' ); ?>" class="button-primary" onclick="slider_insert_shortcode();" />
				<a class="button-secondary" href="#" onclick="tb_remove(); return false;"><?php _e( 'Cancel', 'slider' ); ?></a>
			</p>
		<?php } ?>

		</div>
	</div>
<?php
}

?>
