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
            $headshot = get_field('headshot',$member_id);
            $headshotroll = get_field('headshot_rollover',$member_id);
            $title = get_field('title',$member_id);
            $linkedinlink = 'https://www.linkedin.com/'.get_field('linkedin_link',$member_id);
            $emailaddress = get_field('email_address',$member_id).'@kerigan.com';
            $phonenumber = get_field('phone',$member_id);

            ?>

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header blog-post" >
                    <div class="header-wrapper">
                        <div class="container">
                            <div class="row align-items-center">
                                <div class="offset-lg-1 col-lg-3 col-md-4">
                                    <img src="<?php echo $headshot['url']; ?>" alt="<?php echo get_the_title(); ?>" class="img-fluid" >
                                </div>
                                <div class="col">
                                    <h1><?php echo get_the_title(); ?> <span class="title"><?php echo $title; ?></span></h1>
                                    <ul class="contact">
                                        <li class="email"><a href="mailto:<?php echo $emailaddress; ?>" ><?php echo $emailaddress; ?></a></li>
                                        <li class="phone"><a href="tel:<?php echo $phonenumber; ?>" ><?php echo $phonenumber; ?></a></li>
                                        <li class="linkedin"><a target="_blank" href="<?php echo $linkedinlink; ?>" >Connect on LinkedIn</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </header><!-- .entry-header -->
                <div class="container">
                    <div class="row">
                        <div class="col-lg-10 offset-lg-1" >
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
