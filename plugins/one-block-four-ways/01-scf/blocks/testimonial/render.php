<?php
/**
 * SCF block template for the testimonial block
 */

$quote   = get_field( 'quote' );
$author  = get_field( 'author' );
$role    = get_field( 'role' );
$photo   = get_field( 'photo' );
$variant = get_field( 'variant' );

if ( ! in_array( $variant, array( 'light', 'dark' ), true ) ) {
	$variant = 'light';
}

$classes = 'testimonial is-' . $variant;
if ( ! empty( $block['className'] ) ) {
	$classes .= ' ' . $block['className'];
}
?>

<figure class="<?php echo esc_attr( $classes ); ?>"<?php echo ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : ''; ?>>
	<?php echo wp_get_attachment_image( (int) $photo, 'thumbnail' ); ?>	
	<blockquote>
		<p>
			<?php echo esc_html( $quote ); ?>
		</p>
	</blockquote>
	<figcaption>
		<strong>
			<?php echo esc_html( $author ); ?>
		</strong>
		<?php if ( $role ) : ?>, 
			<span>
				<?php echo esc_html( $role ); ?>
			</span>
		<?php endif; ?>
	</figcaption>
</figure>