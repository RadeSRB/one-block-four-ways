<?php
/**
 * Plugin Name: One Block Four Ways: PHP-only
 * Description: Testimonial block registered only in PHP. WordPress 7.0+ builds the editor controls from the attributes.
 * Version: 1.0.0
 * Requires at least: 7.0
 * Requires PHP: 7.4
 * Author: Rade Erić
 * License: GPL-2.0-or-later
 * Text Domain: ofw
 * 
 * @package OneBlockFourWays
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the block.
 * No block.json
 * No JS
 * No build step
 */
function ofw_php_register_block() {
	wp_register_style(
		'ofw-php-testimonial',
		plugins_url( 'style.css', __FILE__ ),
		array(),
		'1.0.0'
	);

	register_block_type(
		'ofw/testimonial-php',
		array(
			'title'       => __( 'Testimonial (PHP-only)', 'ofw' ),
			'description' => __( 'Testimonial card registered only in PHP.', 'ofw' ),
			'category'    => 'text',
			'icon'        => 'format-quote',
			'attributes'  => array(
				'quote'   => array(
					'label'   => __( 'Quote', 'ofw' ),
					'type'    => 'string',
					'default' => '',
				),
				'author'  => array(
					'label'   => __( 'Author', 'ofw' ),
					'type'    => 'string',
					'default' => '',
				),
				'role'    => array(
					'label'   => __( 'Role', 'ofw' ),
					'type'    => 'string',
					'default' => '',
				),
				'photoUrl' => array(
					'label'   => __( 'Photo URL', 'ofw' ),
					'type'    => 'string',
					'default' => '',
				),
				'variant' => array(
					'label'   => __( 'Variant', 'ofw' ),
					'type'    => 'string',
					'enum'    => array(
						'light',
						'dark',
					),
					'default' => 'light',
				),
			),
			'api_version' => 3,
			'render_callback' => 'ofw_php_render_testimonial',
			'style_handles' => array( 'ofw-php-testimonial' ),
			'supports' => array(
				'autoRegister' => true,
				'anchor' => true,
			),
		)		
	);
}
add_action( 'init', 'ofw_php_register_block' );

/**
 * Renders the testimonial card on the front end and in the editor preview
 * 
 * @param array $attributes The block attributes
 * @return string Block HTML
 */
function ofw_php_render_testimonial( $attributes ) {
	$quote    = $attributes['quote'];
	$author   = $attributes['author'];
	$photoUrl = $attributes['photoUrl'];
	
	$variant = in_array( $attributes['variant'], array( 'light', 'dark' ), true ) ? $attributes['variant'] : 'light';

	$role = $attributes['role'] ? ', <span>' . esc_html( $attributes['role'] ) . '</span>' : '';

	$photo = '';
	if ( $photoUrl) {
		$photo = sprintf( 
			'<img src="%s" alt="%s" />', 
			esc_url( $photoUrl ), 
			esc_attr( $author ) 
		);
	}

	return sprintf(
		'<figure %1$s>%2$s<blockquote><p>%3$s</p></blockquote><figcaption><strong>%4$s</strong>%5$s</figcaption></figure>',
		get_block_wrapper_attributes( array( 'class' => 'testimonial is-' . $variant ) ),
		$photo,
		esc_html( $quote ),
		esc_html( $author ),
		$role
	);
}