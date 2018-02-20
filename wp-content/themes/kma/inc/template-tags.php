<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Seriously_Creative
 */

if ( ! function_exists( 'kma_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function kma_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = sprintf(
		esc_html_x( '%s', 'post date', 'kma' ), $time_string );

	$byline = sprintf(
		esc_html_x( '%s', 'post author', 'kma' ),
		'<span class="author vcard"><a class="url fn n" >' . esc_html( get_the_author() ) . '</a></span>'
	);

	$authorEmail = get_the_author_meta( 'user_email' );

    $authorInfo = get_posts(array(
        'post_type' => 'team_member',
        'meta_key' => 'email_address',
        'meta_value' => str_replace('@kerigan.com', '', $authorEmail),
        'post_status' => 'publish'
    ));

	$authorThumbnail = get_field('headshot', $authorInfo[0]->ID);

	echo '<span class="thumbnail"><img src="' . $authorThumbnail['url'] . '" alt="' . strip_tags($byline) . ': '.get_the_author_meta( 'businesstitle' ).'" style="width:96px" ></span><span class="text"><span class="byline"> ' . $byline . '</span> <span class="title">'.get_the_author_meta( 'businesstitle' ).'</span><br><span class="posted-on">' . $posted_on . '</span></span>'; // WPCS: XSS OK.

}
endif;

if ( ! function_exists( 'kma_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function kma_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'kma' ) );
		if ( $categories_list && kma_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'kma' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'kma' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'kma' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}

	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		/* translators: %s: post title */
		comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'kma' ), array( 'span' => array( 'class' => array() ) ) ), get_the_title() ) );
		echo '</span>';
	}

	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'kma' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function kma_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'kma_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'kma_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so kma_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so kma_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in kma_categorized_blog.
 */
function kma_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'kma_categories' );
}
add_action( 'edit_category', 'kma_category_transient_flusher' );
add_action( 'save_post',     'kma_category_transient_flusher' );
