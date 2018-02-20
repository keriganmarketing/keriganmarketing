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
$custom_code = get_field('custom_code',$article_id);

if(get_field('headline')!=''){
	$headline = get_field('headline');
}

$path = substr($_SERVER['REQUEST_URI'], 0, -1);
$pathParts = explode('/',$path);
$worktype = array_pop((array_slice($pathParts, -1)));
if($worktype == 'client'){
	header('Location: /portfolio/');
}
if($worktype == 'portfolio'){
	$worktype = 'client';
}else{
	$worktype = get_field('work_category');
	$worktype = $worktype->slug;
}

$workTypeLinks = wp_list_pages( array(
	'child_of' => 8, // Only pages that are children of the current page
	'depth' => 1 ,   // Only show one level of hierarchy
	'sort_order' => 'asc',
	'echo' => false,
	'title_li' => ''
));

$modalInfo = '';

?>

<article id="post-<?php the_ID(); ?>" class="<?php echo $worktype; ?>">
	<header class="entry-header" <?php if($thumb_url != ''){ ?> style="background-image:url('<?php echo $thumb_url; ?>');" <?php } ?> >
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
                    <p class=headline"></p>
                    <?php echo apply_filters('the_content', $post->post_content);
                        wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>
        </div>
	</header><!-- .entry-header -->
</article><!-- #post-## -->
<?php echo $custom_code; ?>
<div id="work-section" class="support <?php echo $worktype; ?>">
	<div class="container-fluid no-gutter">
		<div class="row">

			<?php
				
				$taxQuery = 
					array(
						array(
						  'taxonomy' => 'work_category',
						  'field' => 'slug',
						  'terms' => $worktype, 
						  'include_children' => true
						)
					);
			
				$notClient = array(
					array(
						'taxonomy' => 'work_category',
						'field' => 'slug',
						'terms' => 'client', 
						'operator' => 'NOT IN'
					)
				);
			
				if($worktype != 'client'){
					$taxQuery = array_merge($taxQuery,$notClient);
				}
			
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
					'post_type' => 'work',
					'tax_query' => $taxQuery,
					'post_status' => 'publish',
					'suppress_filters' => true,
				);

			
				//print_r($args);

				$featured_work = get_posts( $args, ARRAY_A );

				foreach($featured_work as $work){ 
					$work_id = $work->ID;
					$thumb_id = get_post_thumbnail_id( $work_id );
					$thumb = wp_get_attachment_image_src( $thumb_id, 'medium'); 
					$thumb_url = $thumb[0];
                    $workContent = $work->post_content;
					$tax = get_the_terms( $work_id, 'client' );
					$types = get_the_terms( $work_id, 'work_category' );
					$clientName = $tax[0];
					$workTypes = '';
					$w = 1;
					//print_r($types);
					foreach($types as $type){
						$w++;
						if($type->slug!='client'){
							$workTypes .= $type->name;
							if($w <= count($types)){ $workTypes .= '<span class="seperator work-type-separator">/</span>'; }
						}
					}
					//print_r($clientName);

					if(get_field('link',$work_id) != ''){
						$link = get_field('link',$work_id);
					}else{
						$link = get_permalink($work_id);	
					}
                    if(get_field('background_color',$work_id)!=''){
                        $bgColor = get_field('background_color',$work_id);
                    }else{
                        $bgColor = '#DDDDDD';
                    }

				?>

                <?php if($worktype == 'client' ){ ?>
				<div class="col-sm-6">
					<div class="work-box <?php echo $worktype; ?>" <?php if($thumb_url != ''){ ?>style="background-image:url('<?php echo $thumb_url; ?>');"<?php } ?> >
						<div class="button-overlay">
							<h2><?php echo $clientName->name; ?></h2>
							<p><?php echo $workContent; ?></p>
							<p class="types"><?php echo $workTypes; ?></p>
							<a href="<?php echo $link; ?>" class="work-link" ></a>
						</div>
                        <div class="work-box-hover <?php echo $work->slug; ?>" style="background-color: <?php echo $bgColor; ?>;"></div>
					</div>
                    
				</div>
                <?php } elseif($worktype == 'identity' ){ ?>
                <div class="col-sm-4">
					<div class="work-box <?php echo $worktype; ?>" <?php if($thumb_url != ''){ ?>style="background-image:url('<?php echo $thumb_url; ?>');"<?php } ?> >
						<div class="button-overlay">
							<!--<a href="<?php echo $link; ?>" class="work-link" ></a>-->
						</div>
                    </div>
                </div>
                <?php }elseif($worktype == 'web' ){ 
                    $weburl = get_field('website_url',$work_id);
                ?>
                <div class="col-sm-6">
					<div class="work-box <?php echo $worktype; ?>" <?php if($thumb_url != ''){ ?>style="background-image:url('<?php echo $thumb_url; ?>');"<?php } ?> >
						<div class="button-overlay">
							<h2><?php echo $clientName->name; ?></h2>
                            <p class="sr-only"><?php echo $workContent; ?></p>
                            <p class="types"><?php echo $weburl; ?></p>
							<a target="_blank" href="http://<?php echo $weburl; ?>" class="work-link" ></a>
						</div>
                        <div class="work-box-hover <?php echo $work->slug; ?>" style="background-color: <?php echo $bgColor; ?>;"></div>
                    </div>
                </div>   
                <?php }elseif($worktype == 'tv-video' ){ 
                    $tvcode = get_field('ytcode',$work_id);
                ?>
                <div class="col-sm-6">
					<div class="work-box <?php echo $worktype; ?>" <?php if($thumb_url != ''){ ?>style="background-image:url('<?php echo $thumb_url; ?>');"<?php } ?> >
						<div class="button-overlay">
							<h2><?php echo $work->post_title; ?></h2>
                            <p><?php echo $clientName->name; ?></p>
							<a href="#" class="work-link" id="<?php echo $work->post_name; ?>-link" data-toggle="modal" data-target="#<?php echo $work->post_name; ?>" ></a>
						</div>
                        <div class="work-box-hover <?php echo $work->slug; ?>" style="background-color: <?php echo $bgColor; ?>;"></div>
                    </div>
                </div>
                <?php 
                    $modalInfo .= '
                    <div class="modal fade row align-items-center" id="'.$work->post_name.'" tabindex="-1" role="dialog" aria-labelledby="'.$clientName->name.'Label">
                      <div class="modal-dialog modal-lg col" role="document">
                        <div class="modal-content">
                          <div class="modal-body">

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/'.$tvcode.'?rel=0&amp;showinfo=0"></iframe> 
                            </div>
                            <p class="modal-title" id="'.$clientName->name.'Label">'.$work->post_title.'</p> 
                          </div>
                        </div>
                      </div>
                    </div>

                    ';
                ?>
                <?php }else{ ?>
                <div class="col-sm-6">
					<div class="work-box <?php echo $worktype; ?>" <?php if($thumb_url != ''){ ?>style="background-image:url('<?php echo $thumb_url; ?>');"<?php } ?> >
						<div class="button-overlay">
							<h2><?php echo $clientName->name; ?></h2>
							<p><?php echo $workContent; ?></p>
							<p class="types"><?php echo $workTypes; ?></p>
							<a href="<?php echo $link; ?>" class="work-link" ></a>
						</div>
                        <div class="work-box-hover <?php echo $work->slug; ?>" style="background-color: <?php echo $bgColor; ?>;"></div>
					</div>
				</div>
                <?php } ?>
			<?php } ?>

			<?php if ( (count($featured_work) & 1) && $worktype != 'identity' ) { ?>
                <div class="col-sm-6">
                    <div class="work-box your-ad" >
                        <div class="button-overlay">
                            <h2>Your Organization Here</h2>
                            <p>Start a project.</p>
                            <p class="types"></p>
                            <a href="/startup/" class="work-link" ></a>
                        </div>
                    </div>
                </div>
			<?php } ?>

		</div>
	</div>
</div>
<?php

if($modalInfo != ''){  
    echo '<div class="multi-modal-content">'.$modalInfo.'</div>';
}

?>
