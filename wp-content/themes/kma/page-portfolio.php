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

$path = $_SERVER['REQUEST_URI'];
$path = explode('?', $path);
$worktype = str_replace('/','',$path[0]);

if($worktype == 'portfolio'){
	$worktype = 'client';
}

get_header(); ?>
	<div id="mast">

	</div>
	<div id="scrollbg" class="hide"></div>
</div>
<div id="mid">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php
			
			while ( have_posts() ) : the_post();
				
			if(wp_get_post_parent_id( $post_ID ) == 8){
				get_template_part( 'template-parts/content', 'client' );
			}else{
				get_template_part( 'template-parts/content', 'page' );
			}
			
			endwhile; // End of the loop.
			
			//echo str_replace('/','',$path);
			
			?>
			
		</main><!-- #main -->
	</div><!-- #primary -->
    <?php get_sidebar(); ?>
</div>
<div id="work-section">
	<div class="container-fluid no-gutter">
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
					'post_type' => 'work',
					'tax_query' => array(
						array(
						  'taxonomy' => 'work_category',
						  'field' => 'slug',
						  'terms' => $worktype, 
						  'include_children' => true
						)
					),
					'post_status' => 'publish',
					'suppress_filters' => true,
				);

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
				foreach($types as $type){
					$w++;
					if($type->slug!='client'){

						$workTypes .= $type->name;
						if($w <= count($types)){ $workTypes .= '<span class="seperator work-type-separator">/</span>'; }
					}
				}

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

                <div class="col-lg-6">
                    <div class="work-box" <?php if($thumb_url != ''){ ?>style="background-image:url('<?php echo $thumb_url; ?>');"<?php } ?> >
                        <div class="button-overlay align-items-center">
                            <div class="work-content">
                                <h2><?php echo $clientName->name; ?></h2>
                                <p><?php echo $workContent; ?></p>
                                <p class="types"><?php echo $workTypes; ?></p>
                            </div>
                            <a href="<?php echo $link; ?>" class="work-link" ></a>
                        </div>
                        <div class="work-box-hover <?php echo $work->slug; ?>" style="background-color: <?php echo $bgColor; ?>;"></div>
                    </div>
                </div>

			<?php } ?>

            <?php if ( count($featured_work) & 1 ) { ?>
                <div class="col-lg-6">
                    <div class="work-box your-ad" style="background-color: #555;" >
                        <div class="button-overlay">
                            <h2>Your Organization Here</h2>
                            <p>Start a project.</p>
                            <p class="types"></p>
                            <a href="/startup/" class="work-link" ></a>
                        </div>
                        <div class="work-box-hover <?php echo $work->slug; ?>" style="background-color: #000;"></div>
                    </div>
                </div>
            <?php } ?>
		</div>
	</div>
</div>
<?php get_footer();
