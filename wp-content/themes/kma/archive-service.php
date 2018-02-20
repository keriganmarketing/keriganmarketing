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
            $post = get_post(11);
            $article_id = $post->ID;
            $thumb_id = get_post_thumbnail_id( $article_id );
            $thumb = wp_get_attachment_image_src( $thumb_id, 'large'); 
            $thumb_url = $thumb[0];

            if(get_field('headline')!=''){
                $headline = get_field('headline');
            }else{
                $headline = 'Services';
            }

            $isArchive = $article_id;
        ?>
         <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header" <?php if($thumb_url != ''){ ?> style="background-image:url('<?php echo $thumb_url; ?>');" <?php } ?> >
                <div class="header-wrapper">
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

                            
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2>Website Design & Hosting</h2>
                                    <?php //echo term_description( 50, 'service_category' ) ?>
                                </div>
                                <div class="col-md-6">   
                                    <ul>
                                    <?php
                                    $args = array(
                                        'post_type' => 'service',
                                        'orderby' => 'menu_order',
                                        'order' => 'ASC',
                                        'tax_query' => array(
                                            array(
                                                'taxonomy' => 'service_category',
                                                'field'    => 'slug',
                                                'terms'    => 'website-design-hosting',
                                            ),
                                        ),
                                    );


                                    $query1 = new WP_Query( $args );    
                                    if ( $query1->have_posts() ) {
                                        // The Loop
                                        while ( $query1->have_posts() ) {
                                            $query1->the_post();
                                            //echo '<li>' . get_the_title() . '</li>';

                                            $isEnabled = get_field('enable_link',$query1->post->ID);
                                            $serviceName = $query1->post->post_title;
                                            $serviceLink = get_permalink($query1->post->ID);	

                                            if($isEnabled === TRUE){ ?>
                                                <li class="service-link col-sm-6 col-md-12"><a href="<?php echo $serviceLink; ?>" class="service-link" ><?php echo $serviceName; ?></a></li>

                                            <?php }else{ ?>

                                        <li class="service-link col-sm-6 col-md-12" ><span><?php echo $serviceName; ?></span></li>

                                            <?php } 

                                        }

                                        /* Restore original Post Data 
                                         * NB: Because we are using new WP_Query we aren't stomping on the 
                                         * original $wp_query and it does not need to be reset with 
                                         * wp_reset_query(). We just need to set the post data back up with
                                         * wp_reset_postdata().
                                         */
                                        wp_reset_postdata();
                                    }
                                    ?>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                            
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2>Brand Development</h2> 
                                    <?php //echo term_description( 51, 'service_category' ) ?>
                                </div>
                                <div class="col-md-6">
                                    <ul>
                                    <?php  
                                    $args2 = array(
                                        'post_type' => 'service',
                                        'orderby' => 'menu_order',
                                        'order' => 'ASC',
                                        'tax_query' => array(
                                            array(
                                                'taxonomy' => 'service_category',
                                                'field'    => 'slug',
                                                'terms'    => 'brand-development',
                                            ),
                                        ),
                                    );

                                    $query2 = new WP_Query( $args2 );
                                    if ( $query2->have_posts() ) {
                                        // The 2nd Loop
                                        while ( $query2->have_posts() ) {
                                            $query2->the_post();
                                            //echo '<li>' . get_the_title( $query2->post->ID ) . '</li>';

                                            $isEnabled = get_field('enable_link',$query2->post->ID);
                                            $serviceName = $query2->post->post_title;
                                            $serviceLink = get_permalink($query2->post->ID);	
                                            
                                            if($isEnabled === TRUE){ ?>
                                                <li class="service-link col-sm-6 col-md-12"><a href="<?php echo $serviceLink; ?>" class="service-link" ><?php echo $serviceName; ?></a></li>

                                            <?php }else{ ?>

                                                <li class="service-link col-sm-6 col-md-12" ><span><?php echo $serviceName; ?></span></li>

                                            <?php } 

                                        }

                                        // Restore original Post Data
                                        wp_reset_postdata();
                                    }
                                    ?>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                            
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h2>Media & Production</h2>
                                    <?php //echo term_description( 52, 'service_category' ) ?>
                                </div>
                                <div class="col-md-6">
                                    <ul>
                                    <?php
                                    $args3 = array(
                                        'post_type' => 'service',
                                        'orderby' => 'menu_order',
                                        'order' => 'ASC',
                                        'tax_query' => array(
                                            array(
                                                'taxonomy' => 'service_category',
                                                'field'    => 'slug',
                                                'terms'    => 'media-production',
                                            ),
                                        ),
                                    );

                                    $query3 = new WP_Query( $args3 );
                                    if ( $query3->have_posts() ) {
                                        // The 2nd Loop
                                        while ( $query3->have_posts() ) {
                                            $query3->the_post();
                                            //echo '<li>' . get_the_title( $query3->post->ID ) . '</li>';

                                            $isEnabled = get_field('enable_link',$query3->post->ID);
                                            $serviceName = $query3->post->post_title;
                                            $serviceLink = get_permalink($query3->post->ID);	

                                            if($isEnabled === TRUE){ ?>
                                                <li class="service-link col-sm-6 col-md-12"><a href="<?php echo $serviceLink; ?>" class="service-link" ><?php echo $serviceName; ?></a></li>

                                            <?php }else{ ?>

                                                <li class="service-link col-sm-6 col-md-12" ><span><?php echo $serviceName; ?></span></li>

                                            <?php } 

                                        }

                                        // Restore original Post Data
                                        wp_reset_postdata();
                                    }
                                    ?>
                                    </ul>
                                </div>
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
