<?php
/**
 * Plugin Name:       One Block Four Ways: SCF
 * Description:       Testimonial block built with Secure Custom Fields (SCF), the free plugin fron wordpress.org (ACF PRO alternative)
 * Vesrion:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Requires Plugins:  secure-custom-fields
 * Author:            Rade Erić
 * License:           GPL-2.0-or-later
 * Text Domain:       ofw
 * 
 * SCF is a fork of ACF and keeps ACF's functions and hook names.
 * The same code should run on ACF PRO 6.0+, just remove Requires Plugins line
 * 
 * @package OneBlockFourWays
 */

defined( 'ABSPATH' ) || exit;

/**
 * We register the block from blocks/block.json
 * SCF accepts and reads the "acf" key in the file
 */
function ofw_scf_register_block() {
	// We check if SCF or ACF is not active
	if( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	register_block_type( __DIR__ . '/blocks/testimonial' );
}
add_action( 'init', 'ofw_scf_register_block' );

/**
 * Registers the block fields in PHP, this way we can use it after plugin activation
 * Same can be done in the Admin UI and exported to JSON
 */
function ofw_scf_register_block_fields() {
	acf_add_local_field_group( 
		array(
			'key'    => 'group_ofw_testimonial',
			'title'  => 'Testimonial (SCF block)',
			'fields' => array(
				array(
					'key'           => 'field_ofw_quote',
					'label'         => __( 'Quote', 'ofw' ),
					'name'          => 'quote',
					'type'          => 'textarea',
					'rows'          => 3,
					'new_lines'     => ''
				),
				array(
					'key'           => 'field_ofw_author',
					'label'         => __( 'Author', 'ofw' ),
					'name'          => 'author',
					'type'          => 'text'
				),
				array(
					'key'           => 'field_ofw_role',
					'label'         => __( 'Role', 'ofw' ),
					'name'          => 'role',
					'type'          => 'text'
				),
				array(
					'key'           => 'field_ofw_photo',
					'label'         => __( 'Photo', 'ofw' ),
					'name'          => 'photo',
					'type'          => 'image',
					'return_format' => 'id',
					'preview_size'  => 'thumbnail'
				),
				array(
					'key'           => 'field_ofw_variant',
					'label'         => __( 'Variant', 'ofw' ),
					'name'          => 'variant',
					'type'          => 'select',
					'choices'       => array(
						'light' => __( 'Light', 'ofw' ),
						'dark'  => __( 'Dark', 'ofw' )
					),
					'default_value' => 'light'
				)
			),
			'location' => array(
				array(
					array(
						'param'     => 'block',
						'operator'  => '==',
						'value'     => 'ofw/testimonial-scf'
					),
				),
			),
		)
	);
}
add_action( 'acf/include_fields', 'ofw_scf_register_block_fields' );