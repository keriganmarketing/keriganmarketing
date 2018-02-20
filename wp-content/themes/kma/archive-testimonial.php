<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Seriously_Creative
 */

get_header(); ?>
    <div id="mast">

	</div>
	<div id="scrollbg" class="hide"></div>
</div>
<div id="mid">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
        <?php
            $post = get_post(37);
            $article_id = $post->ID;
            $thumb_id = get_post_thumbnail_id( $article_id );
            $thumb = wp_get_attachment_image_src( $thumb_id, 'large'); 
            $thumb_url = $thumb[0];

            if(get_field('headline')!=''){
                $headline = get_field('headline');
            }else{
                $headline = 'Client Comments';
            }

            $isArchive = $article_id;
        ?>
         <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header" <?php if($thumb_url != ''){ ?> style="background-image:url('<?php echo $thumb_url; ?>');" <?php } ?> >
                <div class="header-wrapper-noheight">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col" >
                                <?php //echo get_the_title(); ?>
                                <?php
                                if($headline!=''){
                                    echo '<h1>'.$headline.'</h1>';
                                }
                                ?>
                                <p class=headline"></p>
                                <?php echo apply_filters('the_content', $post->post_content);
                                    wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </header><!-- .entry-header -->
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 offset-lg-1" >
                        <div class="entry-content">

                            <div class="row justify-content-center align-items-center text-center ">
		                        <?php
		                        $args = array(
			                        'numberposts' => -1,
			                        'offset' => 0,
			                        'category' => 0,
			                        'orderby' => 'menu_order',
			                        'order' => 'ASC',
			                        'post_type' => 'testimonial',
			                        'post_status' => 'publish',
			                        'suppress_filters' => true
		                        );

		                        $testimonials = get_posts( $args, ARRAY_A );

		                        foreach($testimonials as $testimonial) {
			                        $testimonial_id = $testimonial->ID;
			                        $copy = $testimonial->post_content;
			                        $author = get_post_meta($testimonial_id,author_info_name, true);
			                        $company = get_post_meta($testimonial_id,author_info_company, true);
			                        $featured = get_post_meta($testimonial_id,author_info_featured, true);
			                        $shorttext = get_post_meta($testimonial_id,author_info_short_version, true);

			                        //if($shorttext!=''){ $copy = $shorttext; }

			                        ?>
                                    <div id="testimonial-<?php echo $testimonial_id; ?>" class="full-quotes col-xs-12">
                                        <p class="quote-content"><?php echo $copy; ?></p>
                                        <p class="quote-author" ><?php echo $author.', '.$company; ?></p>
                                        <hr>
                                    </div>



		                        <?php } ?>
                            </div>



                        </div><!-- .entry-content -->
                    </div>
                </div>
            </div>
            </article><!-- #post-## --> 
		 </main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php wp_reset_query(); ?>
<?php
get_footer();
