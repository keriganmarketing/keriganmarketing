<?php
/**
 * Template part for displaying posts.
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
	<header class="entry-header blog-post" >
        <div class="header-wrapper">
            <div class="container">
                <div class="row">
                    <div class="offset-lg-1 col-lg-10">
                        <h1><?php echo get_the_title(); ?></h1>
                        <?php if ( 'post' === get_post_type() ) : ?>
                            <div class="entry-meta">
                                <?php kma_posted_on(); ?>
                                <div class="social share">
                                    <h3>Share this:</h3>
                                    <?php $currentUrl = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>
                                    <a class="facebook" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($currentUrl); ?>" target="_blank">
                                        <img src="<?php echo get_template_directory_uri() . '/img/facebook.svg'; ?>" width="30" >
                                    </a>
                                    <a class="twitter" href="https://twitter.com/home?status=<?php echo urlencode($currentUrl); ?>" target="_blank"></a>
                                    <a class="linkedin" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($currentUrl); ?>&title=<?php echo urlencode(strip_tags(get_the_title())); ?>&summary=<?php echo urlencode(strip_tags($post->post_content)); ?>&source=https%3A//keriganmarketing.com" target="_blank"></a>
                                    <a class="googleplus" href="https://plus.google.com/share?url=<?php echo urlencode($currentUrl); ?>" target="_blank"></a>
                                </div>
                            </div><!-- .entry-meta -->
                        <?php endif; ?>	
                    </div>
                </div>
            </div>
        </div>
    </header><!-- .entry-header -->
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1" >  
                <div class="entry-content blog-post">
                <?php echo apply_filters('the_content', $post->post_content); ?>
                </div>
            </div>
        </div>
    </div>
</article><!-- #post-## -->