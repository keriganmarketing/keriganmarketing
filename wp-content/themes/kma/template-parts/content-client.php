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

$selectedWork = get_field('work_select');
$clientLogo = get_field('client_logo');

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div id="client-content" class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10 ">
				<div class="entry-content client-page">
		
					<?php
						the_content();
					?>
					
				</div><!-- .entry-content -->
				
			</div>
		</div>
	
	</div>
</article><!-- #post-## -->
<div id="client-work">
	<div class="container-fluid">
	<?php 
		foreach($selectedWork as $workItem){
			$workId = $workItem->ID;
			$workType = get_the_terms($workId,'work_category');
			if(get_field('background_color',$workId)!=''){
				$bgColor = get_field('background_color',$workId);
			}else{
				$bgColor = '#ddd';
			}
            if(get_field('text_color',$workId)!=''){
                $textColor = get_field('text_color',$workId);
            }else{
                $textColor = '#2a2a2a';
            }
            if(get_field('container_color',$workId)!=''){
                $containerColor = get_field('container_color',$workId);
            }else{
                $containerColor = 'transparent';
            }
            if(get_field('container_padding',$workId)!=''){
                $containerPadding = get_field('container_padding',$workId);
            }else{
                $containerPadding = '0px';
            }
            if(get_field('container_image',$workId)!=''){
                $bgImage = get_field('container_image',$workId);
                $containerImage = 'url('.$bgImage['url'].')';
            }else{
                $containerImage = 'none';
            }
			//print_r($workType);
			?>
			<div class="work-item-container <?php echo $workType[0]->slug; ?>" >

			<?php if($workType[0]->slug == 'tv-video'){ ?>

                <a name="tv-section" class="pad-anchor"></a>
                <div id="<?php echo $workId; ?>" class="video-container" style="background-color: <?php echo $containerColor; ?>;  background-image: <?php echo $containerImage; ?>; padding: <?php echo $containerPadding; ?>; background-size:cover;">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <?php if($workItem->post_content != ''){ ?>
                            <div class="text-container-video" style="color:<?php echo $textColor; ?> !important; padding:10px 10px 0;">
                                <?php if($workItem->post_content != ''){
                                    echo apply_filters( 'the_content', $workItem->post_content );
                                } ?>
                            </div>
                            <?php } ?>
                            <div class="embed-responsive embed-responsive-16by9" style="border-color: <?php echo $containerColor; ?>;">
                                <iframe class="embed-responsive-item"  src="https://www.youtube.com/embed/<?php echo get_field('ytcode',$workId); ?>?rel=0&amp;showinfo=0"></iframe>
                            </div>
                        </div>
                    </div>
                </div>
			<?php } ?>
                
            <?php if($workType[0]->slug == 'simple-graphic'){ 
                    $thumb_id = get_post_thumbnail_id( $workId );
                    $thumb = wp_get_attachment_image_src( $thumb_id, 'large'); 
                    $thumb_url = $thumb[0];
                ?>
                <div id="<?php echo $workId; ?>" class="text-container">
				    <div class="row justify-content-center">
                        <div class="col-lg-8" >
                            <?php if($workItem->post_content != ''){
                                echo apply_filters( 'the_content', $workItem->post_content );
                            } ?>
                        </div>
                    </div>
				</div>
                <div class="row justify-content-center">
                    <div class="graphic-container" style="background-color: <?php echo $bgColor; ?>; width:100%;">
                        <img src="<?php echo $thumb_url; ?>" alt="<?php echo $workItem->post_name; ?>" class="img-fluid" style="width:100%" >
                    </div>
                </div>
			<?php } ?>

			<?php if($workType[0]->slug == 'web'){ 
				$screenshot = get_field('screenshot',$workId);
                $desktopmockup = get_field('responsive_mockup_desktop',$workId);
                $mobilemockup = get_field('responsive_mockup_mobile',$workId);
				?>
                <a name="web-section" class="pad-anchor"></a>
                <div id="<?php echo $workId; ?>" class="web-container" style="background-color: <?php echo $containerColor; ?>; background-image: <?php echo $containerImage; ?>; padding: <?php echo $containerPadding; ?>; background-size:cover;">
				    <div class="row text-center justify-content-center">
                        <div class="col hidden-sm-down" >
                            <img src="<?php echo $desktopmockup['url']; ?>" class="img-fluid" alt="<?php echo $desktopmockup['alt']; ?>" >
                        </div>
                        <div class="col hidden-md-up" >
                            <img src="<?php echo $mobilemockup['url']; ?>" class="img-fluid" alt="<?php echo $mobilemockup['alt']; ?>" >
                        </div>
                    </div>
                    <div class="row text-center justify-content-center">
                        <div class="col" >
                            <?php if(get_field('website_url',$workId) != ''){ ?>
                                <h3 class="webaddress"><?php echo get_field('website_url',$workId); ?></h3>
                                <a class="website-link-out" target="_blank" href="//<?php echo get_field('website_url',$workId); ?>" >Visit the site</a>
                            <?php } ?>
                        </div>
                    </div>

				</div>
			<?php } ?>
				
			<?php if($workType[0]->slug == 'custom-content'){ ?>
                <div id="<?php echo $workId; ?>" class="custom-container" style="background-color: <?php echo $containerColor; ?>; background-image: <?php echo $containerImage; ?>; padding: <?php echo $containerPadding; ?>; background-size:cover;">
                    <div class="row justify-content-center no-gutters">
				        <?php echo $workItem->post_content; ?>
                    </div>
                </div>
			<?php } ?>

			</div>

			<?php
			//print_r($workItem);
		}
	?>
	</div>
</div>