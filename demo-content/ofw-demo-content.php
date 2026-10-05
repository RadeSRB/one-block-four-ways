<?php
/**
 * Plugin Name: One Block Four Ways: Demo Content
 * Description: On activation, creates a page that shows the same testimonial built four ways, plus three testimonial posts for the Block Bindings version. Activate it after the other four plugins.
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

register_activation_hook( __FILE__, 'ofw_demo_create_content' );

/**
 * Serializes a block without inner content
 * 
 * @param string $name Block name
 * @param array $attrs Block attributes
 * @return string Block markup
 */
function ofw_demo_block( $name, $attrs ) {
	return serialize_block( 
		array( 
			'blockName'    => $name,
			'attrs'        => $attrs,
			'innerBlocks'  => array(),
			'innerHTML'    => '',
			'innerContent' => array(),
		)
	);
}

/**
 * Returns a core heading block
 * 
 * @param string $text Heading text
 * @return string Block markup
 */
function ofw_demo_heading( $text ) {
	return '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html( $text ) . '</h2><!-- /wp:heading -->';
}

/**
 * Returns a core paragraph block, may contain <strong> and <code>
 * 
 * @param string $html Paragraph content
 * @return string Block markup
 */
function ofw_demo_paragraph( $html ) {
	return '<!-- wp:paragraph --><p>' . wp_kses( $html, array( 'code' => array(), 'strong' => array() ) ) . '</p><!-- /wp:paragraph -->';
}

/**
 * Copies a demo images into the media library
 * 
 * @param string $file Image file name in this plugin folder
 * @param string $name Person's name, used for title and alt text
 * @return int Attachment ID
 */
function ofw_demo_add_photo( $file, $name ) {
	$upload   = wp_upload_bits( sanitize_title( $name ) . '.png', null, file_get_contents( __DIR__ . '/' . $file ) );
	$photo_id = wp_insert_attachment( 
		array(
			'post_mime_type' => 'image/png',
			'post_title'     => sanitize_text_field( $name ), 
			'post_status'    => 'inherit',
		),
		$upload['file']
	);
	wp_update_attachment_metadata( $photo_id, wp_generate_attachment_metadata( $photo_id, $upload['file'] ) );
	update_post_meta( $photo_id, '_wp_attachment_image_alt', 'Portrait of ' . sanitize_text_field( $name ) );

	return $photo_id;
}

/**
 * Creates the photos, three testimonial posts and the page
 */
function ofw_demo_create_content() {
	// We only want to do this once
	$page_id = (int) get_option( 'ofw_demo_page_id' );
	if ( $page_id && get_post( $page_id ) ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	
	// Demo people
	$people = array(
		array(
			'quote'    => 'Our editors now update the homepage on their own, without waiting for a developer.',
			'author'   => 'Jane Doe',
			'role'     => 'Content Lead, Example Co.',
			'photo_id' => ofw_demo_add_photo( 'avatar.png', 'Jane Doe' ),
		),
		array(
			'quote'    => 'We publish a new landing page in an afternoon instead of a week.',
			'author'   => 'John Smith',
			'role'     => 'Marketing Manager, Sample Ltd.',
			'photo_id' => ofw_demo_add_photo( 'avatar-2.png', 'John Smith' ),
		),
		array(
			'quote'    => 'The blocks look the same in the editor and on the site, so there are no surprises.',
			'author'   => 'Alex Example',
			'role'     => 'Editor, Demo Media',
			'photo_id' => ofw_demo_add_photo( 'avatar-3.png', 'Alex Example' ),
		),
	);

	foreach ( $people as $i => $person ) {
		$testimonial_id = wp_insert_post(
			array(
				'post_type'   => 'ofw_testimonial',
				'post_status' => 'publish',
				'post_title'  => $person['author'],
				'post_date'   => wp_date( 'Y-m-d H:i:s', time() - ( $i + 1 ) * 60 * 60 ),
				'post_content' => function_exists( 'ofw_bindings_card_markup' ) ? ofw_bindings_card_markup() : '',
				'meta_input'   => array(
					'ofw_quote'  => $person['quote'],
					'ofw_author' => $person['author'],
					'ofw_role'   => $person['role'],
				),
			)
		);
		set_post_thumbnail( $testimonial_id, $person['photo_id'] );
	}

	$jane      = $people[0];
	$photo_url = wp_get_attachment_image_url( $jane['photo_id'], 'thumbnail' );

	$content = array(
		ofw_demo_paragraph( 'The same testimonial built four ways. Open this page in the editor and click each card to see how it is edited.' ),

		ofw_demo_heading( '1. SCF' ),
		ofw_demo_paragraph( 'A custom block registered from <code>block.json</code> with an <code>acf</code> key. The fields come from Secure Custom Fields. Text can be edited right in the card, the photo and the variant in the expanded editor. The values are saved in the block comment of this page.' ),
		ofw_demo_block(
			'ofw/testimonial-scf',
			array(
				'name' => 'ofw/testimonial-scf',
				'data' => array(
					'quote'    => $jane['quote'],
					'_quote'   => 'field_ofw_quote',
					'author'   => $jane['author'],
					'_author'  => 'field_ofw_author',
					'role'     => $jane['role'],
					'_role'    => 'field_ofw_role',
					'photo'    => $jane['photo_id'],
					'_photo'   => 'field_ofw_photo',
					'variant'  => 'light',
					'_variant' => 'field_ofw_variant',
				),
				'mode' => 'preview',
			)
		),

		ofw_demo_heading( '2. PHP-only' ),
		ofw_demo_paragraph( 'One <code>register_block_type()</code> call in PHP with <code>autoRegister</code>. No JavaScript and no build step. WordPress generates the sidebar controls from the attributes. There is no media picker, so the photo is a URL field.' ),
		ofw_demo_block(
			'ofw/testimonial-php',
			array(
				'quote'    => $jane['quote'],
				'author'   => $jane['author'],
				'role'     => $jane['role'],
				'photoUrl' => $photo_url,
			)
		),
		
		ofw_demo_heading( '3. Native' ),
		ofw_demo_paragraph( 'React in the editor (<code>edit.js</code>), PHP on the front end (<code>render.php</code>). Everything is edited in place: type into the card, click the photo to pick another one from the media library.' ),
		ofw_demo_block(
			'ofw/testimonial-native',
			array(
				'quote'    => $jane['quote'],
				'author'   => $jane['author'],
				'role'     => $jane['role'],
				'photoId'  => $jane['photo_id'],
				'photoUrl' => $photo_url
			)
			
		),

		ofw_demo_heading( '4. Block Bindings' ),
		ofw_demo_paragraph( 'No custom block. Each testimonial is a post in the <strong>Testimonials</strong> menu. The cards below are a Query Loop over those posts, and core blocks read the quote, author and role from post meta. Add a new testimonial and it shows up here without editing this page. For the dark version, select the cards, click <strong>Edit pattern</strong>, select a card and pick <strong>Testimonial dark</strong> under Styles.' ),
		ofw_demo_block(
			'core/pattern',
			array(
				'slug'    => 'ofw/testimonials',
			)
		),
	);

	$page_id = wp_insert_post(
		array(
			'post_type'   => 'page',
			'post_status' => 'publish',
			'post_title'  => 'Testimonials Demo',
			'post_name'   => 'testimonials-demo',
			'post_content' => implode( "\n\n", $content ),
		)
	);

	update_option( 'ofw_demo_page_id', $page_id );
}