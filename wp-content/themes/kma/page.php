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

get_header();
while ( have_posts() ) : the_post(); ?>
	<div id="mast">

	</div>
	<div id="scrollbg" class="hide"></div>
</div>
    <?php if(wp_get_post_parent_id( $post_ID ) == 8) {
        $article_id = $post->ID;
        $thumb_id = get_post_thumbnail_id( $article_id );
        $thumb = wp_get_attachment_image_src( $thumb_id, 'large');
        $thumb_url = $thumb[0];

        $clientLogo = get_field('client_logo');

        $clientpage = get_page_by_title( $post->post_title,OBJECT,'work' );
        $work_id = $clientpage->ID;

        $types = get_the_terms( $work_id, 'work_category' );
        $workTypes = '';
        $w = 1;

        foreach($types as $type){
            $w++;
            if($type->slug!='client'){
	            $link = '#';
                if($type->name == 'SEM' || $type->name == 'SEO'){
                    $link = '#sem-section';
                }
                if($type->slug == 'tv-video' || $type->name == 'Radio'){
                    $link = '#tv-section';
                }
                if($type->name == 'Web'){
                    $link = '#web-section';
                }
                if($type->name == 'Print'){
                    $link = '#print-section';
                }
                if($type->name == 'Identity'){
                    $link = '#identity-section';
                }
                if($type->name == 'Outdoor'){
                    $link = '#outdoor-section';
                }
                if($type->name == 'Campaigns'){
                    $link = '#campaigns-section';
                }

	            $workTypes .= '<a href="'.$link.'" >'.$type->name.'</a>';
                if($w <= count($types)){ $workTypes .= '<span class="seperator">/</span>'; }
            }
        }

        ?>
        <header class="entry-header client" <?php if($thumb_url != ''){ ?> style="background-image:url('<?php echo $thumb_url; ?>');" <?php } ?> >
            <div class="header-wrapper">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-sm-10 col-md-8 col-lg-5 col-xl-3 text-center" >
                            <h1 class="client"><?php echo get_the_title(); ?></h1>
                            <p class="client-logo text-center"><img src="<?php echo $clientLogo['url']; ?>" alt="<?php echo $clientLogo['alt']; ?>" class="img-fluid " ></p>
                            <p></p>
                        </div>

                    </div>
                </div>
            </div>
        </header><!-- .entry-header -->
        <div class="client-work-types text-center" >
            <p class="types"><?php echo $workTypes; ?></p>
        </div>
        <div id="hider"></div>
        <div id="clickdown"><a class="circlebutton" href="#clickdown"><svg id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 50.38 14.24"><defs><style>.cls-1{fill:#fff;opacity:0.8;}</style></defs><polygon class="cls-1" points="0 0 0 5.09 25.19 14.24 50.38 5.09 50.38 0 25.19 9 0 0"/></svg></a></div>

    <?php } ?>
<div id="mid">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
				
			if(wp_get_post_parent_id( $post_ID ) == 8){
				get_template_part( 'template-parts/content', 'client' );
			}elseif( is_page( 8 ) || get_field('show_work') ){
				get_template_part( 'template-parts/content', 'workgallery' );
			}else{
				get_template_part( 'template-parts/content', 'page' );
			}
			
			?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php 
endwhile;
get_footer();
