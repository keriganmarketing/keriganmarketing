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

		<?php
		while ( have_posts() ) : the_post();

            if( get_field('show_work') ){
				get_template_part( 'template-parts/content', 'workgallery' );
			}else{


	            $article_id = $post->ID;
	            $thumb_id = get_post_thumbnail_id( $article_id );
	            $thumb = wp_get_attachment_image_src( $thumb_id, 'large');
	            $thumb_url = $thumb[0];
	            $custom_code = get_field('custom_code',$article_id);

	            if(get_field('headline')!=''){
		            $headline = get_field('headline');
	            }else{
		            $headline = get_the_title();
	            }

	            ?>

                <article id="post-<?php the_ID(); ?>" class="service">
                    <header class="entry-header" <?php if($thumb_url != ''){ ?> style="background-image:url('<?php echo $thumb_url; ?>'); background-position-x: center;" <?php } ?> >
                        <div class="header-wrapper">
                            <div class="container">
                                <div class="row">
                                    <div class="col" >
							            <?php //echo get_the_title(); ?>
							            <?php
							            if($headline!=''){
								            echo '<h1>'.$headline.'</h1>';
							            }
							            ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header><!-- .entry-header -->
                    <div class="container">
                        <div class="row justify-content-center align-items-center">
                            <div class="col-lg-10 text-center" >
                                <div class="entry-content service">
                                    <?php echo apply_filters('the_content', $post->post_content); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </article><!-- #post-## -->
              <?php
	            echo $custom_code;
            }



		endwhile; // End of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php
get_footer();
