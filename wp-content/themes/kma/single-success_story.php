<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Seriously_Creative
 */

get_header(); ?>
<div id="mast">

	</div>
	<div id="scrollbg"></div>
</div>
<div id="mid">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php while ( have_posts() ) : the_post(); ?>

            <?php

            $member_id = $post->ID;
            $name = $post->post_title;
            $tax = get_the_terms( $member_id, 'client' );
            //print_r($tax);
            $title = $tax[0]->name;
            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header blog-post" >
                    <div class="header-wrapper">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="col-lg-10" >
                                    <h1><?php echo get_the_title(); ?> <span class="title"><?php echo $title; ?></span></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </header><!-- .entry-header -->
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-10" >
                            <div class="entry-content bio">
                                <?php echo apply_filters('the_content', $post->post_content); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </article><!-- #post-## -->

		<?php endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php
get_footer();
