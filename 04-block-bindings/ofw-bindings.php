<?php
/**
 * Plugin Name:       One Block Four Ways: Block Bindings
 * Description:       No custom block. Core blocks read the testimonial from post meta through the Block Bindings API.
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Rade Erić
 * License:           GPL-2.0-or-later
 * Text Domain:       ofw
 * 
 * @package           OneBlockFourWays
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns the card markup, core blocks with bindings to post meta
 * 
 * @return string Block markup
 */
function ofw_bindings_card_markup() {
	return (string) file_get_contents( __DIR__ . '/patterns/testimonial-card.html' );
}

/**
 * Returns parsed blocks into the array format as expected by post type templates
 * 
 * @param array $blocks Blocks from parse_blocks()
 * @return array Template array
 */
function ofw_bindings_to_template( $blocks ) {
	$template = array();

	foreach ( $blocks as $block ) {
		if (  empty( $block['blockName'] ) ) {
			continue; // Skip whitespace between blocks
		}

		$template[] = array(
			$block['blockName'],
			$block['attrs'],
			ofw_bindings_to_template( $block['innerBlocks'] ),
		);
	}

	return $template;
}

/**
 * Registers the testimonial post type, its meta fields and the pattern
 */
function ofw_bindings_register() {
	register_post_type(
		'ofw_testimonial',
		array(
			'labels' => array(
				'name'          => __( 'Testimonials', 'ofw' ),
				'singular_name' => __( 'Testimonial', 'ofw' ),
				'add_new_item'  => __( 'Add Testimonial', 'ofw' ),
			),
			'public' => true,
			'show_in_rest' => true,
			'menu_icon' => 'dashicons-format-quote',
			'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'template' => ofw_bindings_to_template( parse_blocks( ofw_bindings_card_markup() ) ),
			'template_lock' => 'all',
		)
	);

	$fields = array(
		'ofw_quote'  => __( 'Quote', 'ofw' ),
		'ofw_author' => __( 'Author', 'ofw' ),
		'ofw_role'   => __( 'Role', 'ofw' ),
	);

	foreach ( $fields as $key => $label ) {
		register_post_meta(
			'ofw_testimonial',
			$key,
			array(
				'label'             => $label,
				'type'              => 'string',
				'single'            => true,
				'default'           => '',
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
	}

	wp_register_style(
		'ofw-bindings-testimonial',
		plugins_url( 'style.css', __FILE__ ),
		array(),
		'1.0.0'
	);

	/**
	 * We register a style for the block pattern to have option to change to dark mode
	 */
	register_block_style(
		'core/group',
		array(
			'name' => 'testimonial-dark',
			'label' => __( 'Testimonial Dark', 'ofw' ),
		)
	);

	register_block_pattern(
		'ofw/testimonials',
		array(
			'title'       => __( 'Testimonial (Block Bindings)', 'ofw' ),
			'description' => __( 'Query Loop that shows testimonial posts as cards.', 'ofw' ),
			'categories'  => array( 'text' ),
			'content'     => '<!-- wp:query {"query":{"postType":"ofw_testimonial","perPage":3,"inherit":false,"order":"desc","orderBy":"date"}} -->'
				. '<div class="wp-block-query"><!-- wp:post-template -->'
				. ofw_bindings_card_markup()
				. '<!-- /wp:post-template --></div><!-- /wp:query -->',
		)
	);
}
add_action( 'init', 'ofw_bindings_register' );

/**
 * Enqueues styles for both front end and editor
 */
function ofw_bindings_enqueue_style() {
	wp_enqueue_style( 'ofw-bindings-testimonial' );
}
add_action( 'enqueue_block_assets', 'ofw_bindings_enqueue_style' );