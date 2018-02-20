<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
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
                $article_id = 17;
                $thumb_id = get_post_thumbnail_id( $article_id );
                $thumb = wp_get_attachment_image_src( $thumb_id, 'large'); 
                $thumb_url = $thumb[0];

                if(get_field('headline', $article_id)!=''){
                    $headline = get_field('headline', $article_id);
                }else{
                    $headline = get_the_title($article_id);
                }
            ?>

            <article id="post-<?php echo $article_id; ?>" <?php post_class(); ?>>
                <header class="entry-header" <?php if($thumb_url != ''){ ?> style="background-image:url('<?php echo $thumb_url; ?>');" <?php } ?> >
                    <div class="header">
                        <div class="container">
                            <div class="row">
                                <div class="col text-center" >
                                    <h1>News</h1>
                                    <?php
                                    if($headline!=''){
                                        echo '<p class="headline" ></p>';
                                    }
                                    ?>
                                    <?php 
                                        $content_post = get_post($article_id);
                                        $content = $content_post->post_content;
                                        echo apply_filters('the_content', $content);
                                        wp_reset_postdata();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </header><!-- .entry-header -->
            </article><!-- #post-## -->
          <div id="news-section">
			<div class="container-fluid">
				<div class="row">
						
					<?php
					$args = array(
						'numberposts' => -1,
						'offset' => 0,
						'category' => 0,
						'orderby' => 'post_date',
						'order' => 'DESC',
						'include' => '',
						'exclude' => '',
						'meta_key' => '',
						'meta_value' =>'',
						'post_type' => 'post',
						'post_status' => 'publish',
						'suppress_filters' => true
					);

					$recent_posts = wp_get_recent_posts( $args, ARRAY_A );

					foreach($recent_posts as $article){ 
						$article_id = $article['ID'];
						$post_date = $article['post_date'];
						$thumb_id = get_post_thumbnail_id( $article_id );
						$thumb = wp_get_attachment_image_src( $thumb_id, 'medium'); 
						$thumb_url = $thumb[0];

						//echo $article_id;
						//print_r($article);
					?>

                        <div class="col-sm-6 col-lg-4 col-xl-3" >
                            <div class="article-box text-center" >
                                <div class="embed-responsive embed-responsive-16by9" >
                                    <?php if($thumb_url != ''){ ?>
                                        <img src="<?php echo $thumb_url; ?>" alt="<?php echo $article["post_title"]; ?>" class="img-fluid embed-responsive-item" >
                                    <?php }else{ ?>
                                        <img src="<?php echo get_template_directory_uri().'/img/blog-placeholder.jpg'; ?>" alt="<?php echo $article["post_title"]; ?>" class="img-fluid embed-responsive-item" >
                                    <?php } ?>
                                </div>
                                <div class="article-intro" >
                                    <p class="title"><?php echo wp_trim_words( $article["post_title"], 9, '...' ); ?> <span class="article-tile-date"><?php echo mysql2date( get_option( 'date_format' ), $post_date); ?></span></p>
                                    <p class="intro"><?php echo wp_trim_words( $article['post_content'], 18, '...' ); ?></p>
                                    <p class="read-more">Read More</p>
                                </div>
                                <a href="<?php echo get_permalink($article_id); ?>" class="article-box-link"></a>
                            </div>
                        </div>

					<?php } ?>


				</div>
            </div></div>

		</main><!-- #main -->
	</div><!-- #primary -->
    <?php get_sidebar(); ?>
</div>
<?php get_footer();
