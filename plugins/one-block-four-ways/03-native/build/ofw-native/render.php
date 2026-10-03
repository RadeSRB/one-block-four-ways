<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * The following variables are exposed to the file:
 *     $attributes (array): The block attributes.
 *     $content (string): The block default content.
 *     $block (WP_Block): The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
$variant = in_array( $attributes['variant'], array( 'light', 'dark' ), true ) ? $attributes['variant'] : 'light';
?>
<figure <?php echo get_block_wrapper_attributes( array( 'class' => 'testimonial is-' . $variant ) ); ?>>
	<?php if ( $attributes['photoId'] ) : ?>
		<?php echo wp_get_attachment_image( $attributes['photoId'], 'thumbnail' ); ?>
	<?php endif; ?>
	<blockquote>
		<p><?php echo wp_kses_post( $attributes['quote'] ); ?></p>
	</blockquote>
	<figcaption>
		<strong>
			<?php echo esc_html( wp_strip_all_tags(	$attributes['author'] ) ); ?>
			<?php if ( $attributes['role'] ) : ?>
				, <span><?php echo esc_html( wp_strip_all_tags( $attributes['role'] ) ); ?></span>
			<?php endif; ?>
		</strong>
	</figcaption>
</figure>
