<?php
/**
 * Seriously Creative functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Seriously_Creative
 */

require_once('inc/cpt.php'); //Include CPT class
require_once('inc/leads.php'); //Include kmaLeads class
require_once('inc/honeypot.php'); //Include Akismet class

if ( ! function_exists( 'kma_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function kma_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Seriously Creative, use a find and replace
	 * to change 'kma' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'kma', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary', 'kma' ),
		'footer' => esc_html__( 'Footer', 'kma' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'kma_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );

	///CREATE PORTFOLIO CPT
	$work = new Custom_Post_Type(
		'Work',
		array(
			'supports'			 => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			'menu_icon'			 => 'dashicons-images-alt2',
			'has_archive' 		 => true,
			'menu_position'      => null,
			'public'             => true,
			'publicly_queryable' => true,
		)
	);

	$work->add_taxonomy( 'Work Category' );
	$work->add_taxonomy( 'Client' );

    $success = new Custom_Post_Type(
        'Success Story',
        array(
            'supports'			 => array( 'title', 'editor', 'thumbnail', 'revisions' ),
            'menu_icon'			 => 'dashicons-lightbulb',
            'has_archive' 		 => true,
            'menu_position'      => null,
            'public'             => true,
            'publicly_queryable' => true,
            'rewrite'            => array( 'slug' => 'case-studies', 'with_front' => FALSE ),
        )
    );
    register_taxonomy_for_object_type( 'client', 'success_story' );

    ///CREATE SERVICE CPT
	$work = new Custom_Post_Type(
		'Service',
		array(
			'supports'			 => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			'menu_icon'			 => 'dashicons-admin-customizer',
            'rewrite'            => array( 'slug' => 'services' ),
            'hierarchical'       => true,
			'has_archive' 		 => true,
			'menu_position'      => null,
			'public'             => true,
			'publicly_queryable' => true,
		)
	);

	$work->add_taxonomy( 'Service Category' );

    ///CREATE TEAM CPT
	$team = new Custom_Post_Type(
		'Team Member',
		array(
			'supports'			 => array( 'title', 'editor', 'thumbnail', 'revisions' ),
			'menu_icon'			 => 'dashicons-groups',
            'hierarchical'       => true,
			'has_archive' 		 => false,
			'menu_position'      => null,
			'public'             => true,
			'publicly_queryable' => true,
            'rewrite'            => array( 'slug' => 'team', 'with_front' => FALSE ),
		)
	);


    ///CREATE TEAM CPT
	$client = new Custom_Post_Type(
		'Client',
		array(
			'supports'			 => array( 'title' ),
			'menu_icon'			 => 'dashicons-image-filter',
            'hierarchical'       => false,
			'has_archive' 		 => false,
			'menu_position'      => null,
			'public'             => false,
			'publicly_queryable' => false,
		)
	);


	///CREATE TESTIMONIAL CPT
	$quote = new Custom_Post_Type(
		'Testimonial',
		array(
			'supports'			 => array( 'title', 'editor', 'revisions' ),
			'menu_icon'			 => 'dashicons-format-quote',
            'rewrite'            => array( 'slug' => 'testimonials' ),
			'has_archive' 		 => true,
			'menu_position'      => null,
			'public'             => true,
			'publicly_queryable' => true,
		)
	);

	$quote->add_taxonomy( 'Testimonial Category' );

	$quote->add_meta_box(
		'Author Info',
		array(
			'Name' 			=> 'text',
			'Company' 		=> 'text',
            'Short Version' => 'longtext',
            'Featured'      => 'boolean'
		)
	);


	//CREATE LEAD MGMT SYS
	$leads = new Custom_Post_Type(
		'Lead',
		array(
			'supports'			 => array( 'title' ),
			'menu_icon'			 => 'dashicons-star-empty',
			'has_archive' 		 => false,
			'menu_position'      => null,
			'public'             => false,
			'publicly_queryable' => false,
		)
	);

	$leads->add_meta_box(
		'Lead Info',
		array(
			'Lead Type' 	=> 'locked',
			'Name' 			=> 'locked',
			'Date' 			=> 'locked',
			'Phone Number'	=> 'locked',
			'Email Address'	=> 'locked',
			'Message' 		=> 'locked',
			'Interests' 	=> 'locked'
		)
	);

	$leads->add_taxonomy( 'Type' );

	/*$leads->add_meta_box(
		'Notification',
		array(
			'Preview' => 'preview'
		)
	);

	$leads->columns(
		array(
			'cb' => '<input type="checkbox" />',
			'lead_info_name' => __('Name'),
			'lead_info_phone_number' => __('Phone Number'),
			'lead_info_email_address' => __('Email Address'),
			'date' => __('Date')
		)
	);

	$leads->populate_column('lead_info_phone_number', function($column, $post) {

		echo get_post_meta($post_id, 'lead_info_phone_number', true);

	});*/

    //Adds fields to users
    function modify_contact_methods($profile_fields) {

        $profile_fields['businesstitle'] = 'Title';
        return $profile_fields;
    }
    add_filter('user_contactmethods', 'modify_contact_methods');

}
endif;
add_action( 'after_setup_theme', 'kma_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function kma_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'kma_content_width', 640 );
}
add_action( 'after_setup_theme', 'kma_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function kma_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'kma' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'kma' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'kma_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function kma_scripts() {
	//wp_enqueue_style( 'kma-style', get_stylesheet_uri() );
	//wp_enqueue_script( 'kma-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20160907', true );
	//wp_enqueue_script( 'kma-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20160907', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'kma_scripts' );

function disable_wp_emojicons() {

  // all actions related to emojis
  remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}
add_action( 'init', 'disable_wp_emojicons' );

function disable_emojicons_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}

add_action('nav_menu_css_class', 'add_current_nav_class', 10, 2 );

function add_current_nav_class($classes, $item) {

	// Getting the current post details
	global $post;

	// Getting the post type of the current post
	$current_post_type = get_post_type_object(get_post_type($post->ID));
	$current_post_type_slug = $current_post_type->rewrite[slug];

	// Getting the URL of the menu item
	$menu_slug = strtolower(trim($item->url));

	// If the menu item URL contains the current post types slug add the current-menu-item class
	if (strpos($menu_slug,$current_post_type_slug) !== false) {

		$classes[] = 'current-menu-item';

	}

	// Return the corrected set of classes to be added to the menu item
	return $classes;

}

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
