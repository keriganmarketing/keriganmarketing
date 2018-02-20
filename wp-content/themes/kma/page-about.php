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
<div id="mid">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
				get_template_part( 'template-parts/content', 'page' );
			?>
          
          <div id="team-section-container">
              <div id="team-section">
              <div class="container">
                  <div class="row">
                      <div class="col-lg-10 offset-lg-1" >
                          <div class="entry-content" style="padding:40px 0 60px;">
                              <div class="row align-items-center">
                                  <div class="col-md-3" >
                                      <h2>Our Team</h2>
                                      <p>&nbsp;</p>
                                  </div>
                                  <div class="col">
                                      <p>With a resolute commitment to integrity and Christian values, each member of the KMA team shares a passion for delivering the very best work for our clients in web, creative and media solutions.</p>
                                  </div>
                              </div>
                          </div><!-- .entry-content -->
                      </div>
                  </div>
              </div>
              <div class="container-fluid">
                  <div class="row">
                      <div class="offset-xl-1 col-xl-10">
                          <div class="row align-items-center">
                            
                            <?php
                            $args = array(
                                'numberposts' => -1,
                                'offset' => 0,
                                'category' => 0,
                                'orderby' => 'menu_order',
                                'order' => 'ASC',
                                'include' => '',
                                'exclude' => '',
                                'meta_key' => '',
                                'meta_value' =>'',
                                'post_type' => 'team_member',
                                'post_status' => 'publish',
                                'suppress_filters' => true
                            );

                            $team = get_posts( $args, ARRAY_A );

                            foreach($team as $member){ 
                                $member_id = $member->ID;
                                $name = $member->post_title;
                                $headshot = get_field('headshot',$member_id);
                                $headshotroll = get_field('headshot_rollover',$member_id);
                                $title = get_field('title',$member_id);
                                $linkedinlink = 'https://www.linkedin.com/'.get_field('linkedin_link',$member_id);
                                $emailaddress = get_field('email_address',$member_id).'@kerigan.com';
                            ?>
                            <div class="team-member col-sm-6 col-md-4 col-lg-3">
                                <div class="image-container">
                                  <div class="image"><a href="<?php echo get_permalink($member_id); ?>" ><img src="<?php echo $headshot['url']; ?>" alt="<?php echo $headshot['alt']; ?>" class="grayscale img-fluid" ></a></div>
                                </div>
                                <div class="info">
                                    <h3><?php echo $name; ?></h3>
                                    <h4><?php echo $title; ?></h4>
                                </div>
                                <div class="link text-xs-center">
                                    <a href="<?php echo get_permalink($member_id); ?>" >View Bio</a>
                                </div>
                            </div>
                            <?php } ?>
                            
                          </div>
                      </div>
                  </div>
              </div>
              </div>
          </div>
          <div id="clients-section">
              <div class="container">
                  <div class="row">
                      <div class="col-lg-10 offset-lg-1" >
                          <div class="entry-content">
                              <div class="row align-items-center">
                                  <div class="col-md-3" >
                                      <h2>Happy Clients</h2>
                                      <p>&nbsp;</p>
                                  </div>
                                  <div class="col">
                                      <p>We have delivered web design and marketing results for clients in Panama City and across the Southeast, many of whom have been with us since 2000, when Kerigan Marketing Associates opened its doors.</p>
                                  </div>
                              </div>
                          </div><!-- .entry-content -->
                      </div>
                  </div>
              </div>
              <div class="container-fluid">
                  <div class="row no-gutter">
                      <div class="offset-xl-1 col-xl-10">
                          <div class="row align-items-center justify-content-center">
                            
                            <?php
                            $args = array(
                                'numberposts' => -1,
                                'offset' => 0,
                                'category' => 0,
                                'orderby' => 'menu_order',
                                'order' => 'ASC',
                                'include' => '',
                                'exclude' => '',
                                'meta_key' => '',
                                'meta_value' =>'',
                                'post_type' => 'client',
                                'post_status' => 'publish',
                                'suppress_filters' => true
                            );

                            $clients = get_posts( $args, ARRAY_A );

                            foreach($clients as $client){ 
                                $client_id = $client->ID;
                                $name = $client->post_title; 
                                $clientlogo = get_field('client_logo',$client_id);
                                $clientlink = get_field('client_link',$client_id);
                            ?>
                            <div class="client col-sm-6 col-md-4 col-lg-3 col-xl-2">
                                <?php if($clientlink!=''){ ?><a href="<?php echo $clientlink; ?>" ><?php } ?>
                                
                                <div class="logo-container">
                                    <div class="logo text-center"><img src="<?php echo $clientlogo['url']; ?>" alt="<?php echo $name; ?>" class="img-fluid grayscale" ></div>
                                </div>
     
                                <?php if($clientlink!=''){ ?></a><?php } ?>
                            </div>
                            <?php } ?>
                            
                          </div>
                      </div>
                  </div>
              </div>         
          </div>

		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php 
endwhile;
get_footer();
