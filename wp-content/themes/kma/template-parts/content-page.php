<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Seriously_Creative
 */

$article_id = $post->ID;
$thumb_id = get_post_thumbnail_id( $article_id );
$thumb = wp_get_attachment_image_src( $thumb_id, 'large'); 
$thumb_url = $thumb[0];

if(get_field('headline')!=''){
	$headline = get_field('headline');
}else{
	$headline = get_the_title();
}

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header" <?php if($thumb_url != ''){ ?> style="background-image:url('<?php echo $thumb_url; ?>');" <?php } ?> >
        <div class="header-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col text-center" >
                        <?php //echo get_the_title(); ?>
                        <?php 
                            if($headline!=''){
                                echo '<h1>'.$headline.'</h1>';
                            } 
                        ?>
                        <p class="headline"></p>
                        <?php echo apply_filters('the_content', $post->post_content);
                            wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </header><!-- .entry-header -->
</article><!-- #post-## -->
