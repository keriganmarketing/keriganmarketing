<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
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
            <header class="entry-header" <?php if($thumb_url != ''){ ?> style="background-image:url('<?php echo $thumb_url; ?>');" <?php } ?> >
                <div class="header-wrapper">
                    <div class="container">
                        <div class="row">
                            <div class="col text-center" >
                                <h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'kma' ); ?></h1>
                                <p class=headline"></p>
                                <p><?php esc_html_e( 'It looks like nothing was found at this location...', 'kma' ); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </header><!-- .entry-header -->

		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php
get_footer();
