<?php

namespace WordCamp\Blocks\Sessions;
use WP_Post;
use function WordCamp\Blocks\Shared\Content\{ get_all_the_content, array_to_human_readable_list, render_item_title, render_item_content, render_item_permalink };
use function WordCamp\Blocks\Shared\Components\{ render_featured_image };

defined( 'WPINC' ) || die();

/** @var array   $attributes */
/** @var array   $speakers */
/** @var WP_Post $session */

setup_postdata( $session );
?>

<div class="wordcamp-session wordcamp-session-<?php echo sanitize_html_class( $session->post_name ); ?>">
	<?php echo wp_kses_post(
		render_item_title(
			get_the_title( $session ),
			get_permalink( $session ),
			3,
			[ 'wordcamp-session-title' ]
		)
	); ?>

	<?php if ( true === $attributes['show_speaker'] && ! empty( $speakers[ $session->ID ] ) ) :
		$speaker_linked_names = array_map(
			function( $speaker ) {
				return sprintf(
					'<a href="%s">%s</a>',
					get_permalink( $speaker ),
					get_the_title( $speaker )
				);
			},
			$speakers[ $session->ID ]
		);
		?>

		<div class="wordcamp-item-meta wordcamp-session-speakers">
			<?php
			printf(
				/* translators: %s is a list of names. */
				wp_kses_post( __( 'Presented by %s', 'wordcamporg' ) ),
				wp_kses_post( array_to_human_readable_list( $speaker_linked_names ) )
			);
			?>
		</div>
	<?php endif; ?>

	<?php if ( true === $attributes['show_images'] ) : ?>
		<?php echo wp_kses_post(
			render_featured_image(
				$session,
				$attributes['featured_image_width'],
				[ 'wordcamp-session-featured-image', 'align-' . esc_attr( $attributes['image_align'] ) ],
				get_permalink( $session )
			)
		); ?>
	<?php endif; ?>

	<?php if ( 'none' !== $attributes['content'] ) : ?>
		<?php echo wp_kses_post(
			render_item_content(
				'excerpt' === $attributes['content']
					? apply_filters( 'the_excerpt', get_the_excerpt( $session ) )
					: get_all_the_content( $session ),
				[ 'wordcamp-session-content-' . $attributes['content'] ]
			)
		); ?>
	<?php endif; ?>

	<?php if ( $attributes['show_meta'] || $attributes['show_category'] ) : ?>
		<div class="wordcamp-item-meta wordcamp-session-details">
			<?php if ( $attributes['show_meta'] ) : ?>
				<?php $tracks = get_the_terms( $session, 'wcb_track' ); ?>

				<div class="wordcamp-session-time-location">
					<?php if ( ! is_wp_error( $tracks ) && ! empty( $tracks ) ) :
						printf(
							/* translators: 1: A date; 2: A time; 3: A location; */
							esc_html__( '%1$s at %2$s in %3$s', 'wordcamporg' ),
							esc_html( date_i18n( get_option( 'date_format' ), $session->_wcpt_session_time ) ),
							esc_html( date_i18n( get_option( 'time_format' ), $session->_wcpt_session_time ) ),
							sprintf(
								'<span class="wordcamp-session-track wordcamp-session-track-%s">%s</span>',
								esc_attr( $tracks[0]->slug ),
								esc_html( $tracks[0]->name )
							)
						);

					else :
						printf(
							/* translators: 1: A date; 2: A time; */
							esc_html__( '%1$s at %2$s', 'wordcamporg' ),
							esc_html( date_i18n( get_option( 'date_format' ), $session->_wcpt_session_time ) ),
							esc_html( date_i18n( get_option( 'time_format' ), $session->_wcpt_session_time ) )
						);
					endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $attributes['show_category'] && has_term( null, 'wcb_session_category', $session ) ) :
				$categories = array_map(
					function( $category ) {
						return sprintf(
							'<span class="wordcamp-session-category wordcamp-session-category-%s">%s</span>',
							esc_attr( $category->slug ),
							esc_html( $category->name )
						);
					},
					get_the_terms( $session, 'wcb_session_category' )
				);
				?>

				<div class="wordcamp-session-categories">
					<?php
					/* translators: used between list items, there is a space after the comma */
					echo wp_kses_post( implode( __( ', ', 'wordcamporg' ), $categories ) );
					?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if ( 'full' === $attributes['content'] ) : ?>
		<?php echo wp_kses_post(
			render_item_permalink(
				get_permalink( $session ),
				__( 'Visit session page', 'wordcamporg' ),
				[ 'wordcamp-session-permalink' ]
			)
		); ?>
	<?php endif; ?>
</div>

<?php
wp_reset_postdata();