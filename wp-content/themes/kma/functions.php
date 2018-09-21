<?php
/**
 * Seriously Creative functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Seriously_Creative
 */

use Includes\Modules\Helpers\CleanWP;
use KeriganSolutions\CPT\CustomPostType;
use Includes\Modules\Social\SocialSettingsPage;
use Includes\Modules\KMAFacebook\FacebookController;

require('vendor/autoload.php');

$facebook = new FacebookController();
$facebook->setupAdmin();

require_once('inc/leads.php'); //Include kmaLeads class
require_once('inc/honeypot.php'); //Include Akismet class

$socialLinks = new SocialSettingsPage();
if (is_admin()) {
    $socialLinks->createPage();
}

if ( ! function_exists('kma_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function kma_setup()
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on Seriously Creative, use a find and replace
         * to change 'kma' to the name of your theme in all the template files.
         */
        load_theme_textdomain('kma', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus([
            'primary' => esc_html__('Primary', 'kma'),
            'footer'  => esc_html__('Footer', 'kma'),
        ]);

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', [
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ]);

        // Set up the WordPress core custom background feature.
        add_theme_support('custom-background', apply_filters('kma_custom_background_args', [
            'default-color' => 'ffffff',
            'default-image' => '',
        ]));

        ///CREATE PORTFOLIO CPT
        $work = new CustomPostType(
            'Work',
            [
                'supports'           => ['title', 'editor', 'thumbnail', 'revisions'],
                'menu_icon'          => 'dashicons-images-alt2',
                'has_archive'        => true,
                'menu_position'      => null,
                'public'             => true,
                'publicly_queryable' => true,
            ]
        );

        $work->addTaxonomy('Work Category');
        $work->addTaxonomy('Client');

        $success = new CustomPostType(
            'Success Story',
            [
                'supports'           => ['title', 'editor', 'thumbnail', 'revisions'],
                'menu_icon'          => 'dashicons-lightbulb',
                'has_archive'        => true,
                'menu_position'      => null,
                'public'             => true,
                'publicly_queryable' => true,
                'rewrite'            => ['slug' => 'case-studies', 'with_front' => false],
            ]
        );
        register_taxonomy_for_object_type('client', 'success_story');

        ///CREATE SERVICE CPT
        $work = new CustomPostType(
            'Service',
            [
                'supports'           => ['title', 'editor', 'thumbnail', 'revisions'],
                'menu_icon'          => 'dashicons-admin-customizer',
                'rewrite'            => ['slug' => 'services'],
                'hierarchical'       => true,
                'has_archive'        => true,
                'menu_position'      => null,
                'public'             => true,
                'publicly_queryable' => true,
            ]
        );

        $work->addTaxonomy('Service Category');

        ///CREATE TEAM CPT
        $team = new CustomPostType(
            'Team Member',
            [
                'supports'           => ['title', 'editor', 'thumbnail', 'revisions'],
                'menu_icon'          => 'dashicons-groups',
                'hierarchical'       => true,
                'has_archive'        => false,
                'menu_position'      => null,
                'public'             => true,
                'publicly_queryable' => true,
                'rewrite'            => ['slug' => 'team', 'with_front' => false],
            ]
        );


        ///CREATE TEAM CPT
        $client = new CustomPostType(
            'Client',
            [
                'supports'           => ['title'],
                'menu_icon'          => 'dashicons-image-filter',
                'hierarchical'       => false,
                'has_archive'        => false,
                'menu_position'      => null,
                'public'             => false,
                'publicly_queryable' => false,
            ]
        );


        ///CREATE TESTIMONIAL CPT
        $quote = new CustomPostType(
            'Testimonial',
            [
                'supports'           => ['title', 'editor', 'revisions'],
                'menu_icon'          => 'dashicons-format-quote',
                'rewrite'            => ['slug' => 'testimonials'],
                'has_archive'        => true,
                'menu_position'      => null,
                'public'             => true,
                'publicly_queryable' => true,
            ]
        );

        $quote->addTaxonomy('Testimonial Category');

        $quote->addMetaBox(
            'Author Info',
            [
                'Name'          => 'text',
                'Company'       => 'text',
                'Short Version' => 'longtext',
                'Featured'      => 'boolean'
            ]
        );


        //CREATE LEAD MGMT SYS
        $leads = new CustomPostType(
            'Lead',
            [
                'supports'           => ['title'],
                'menu_icon'          => 'dashicons-star-empty',
                'has_archive'        => false,
                'menu_position'      => null,
                'public'             => false,
                'publicly_queryable' => false,
            ]
        );

        $leads->addMetaBox(
            'Lead Info',
            [
                'Lead Type'     => 'locked',
                'Name'          => 'locked',
                'Date'          => 'locked',
                'Phone Number'  => 'locked',
                'Email Address' => 'locked',
                'Message'       => 'locked',
                'Interests'     => 'locked'
            ]
        );

        $leads->addTaxonomy('Type');

        //Adds fields to users
        function modify_contact_methods($profile_fields)
        {

            $profile_fields['businesstitle'] = 'Title';

            return $profile_fields;
        }

        add_filter('user_contactmethods', 'modify_contact_methods');

    }
endif;
add_action('after_setup_theme', 'kma_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function kma_content_width()
{
    $GLOBALS['content_width'] = apply_filters('kma_content_width', 640);
}

add_action('after_setup_theme', 'kma_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function kma_widgets_init()
{
    register_sidebar([
        'name'          => esc_html__('Sidebar', 'kma'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'kma'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ]);
}

add_action('widgets_init', 'kma_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function kstrap_inline()
{
    ?>
    <style type="text/css">
        <?php echo file_get_contents(get_template_directory() . '/style.css'); ?>
    </style>
    <?php
}

add_action('wp_head', 'kstrap_inline');

function kma_scripts()
{
    wp_register_script('scripts', get_template_directory_uri() . '/app.js', [], '0.0.1', true);
    wp_enqueue_script('scripts');
}

add_action('wp_enqueue_scripts', 'kma_scripts');

function isa_remove_jquery_migrate(&$scripts)
{
    if ( ! is_admin()) {
        $scripts->remove('jquery');
    }
}

add_filter('wp_default_scripts', 'isa_remove_jquery_migrate');

function disable_wp_emojicons()
{

    // all actions related to emojis
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');

    // filter to remove TinyMCE emojis
    add_filter('tiny_mce_plugins', 'disable_emojicons_tinymce');
}

add_action('init', 'disable_wp_emojicons');

function disable_emojicons_tinymce($plugins)
{
    if (is_array($plugins)) {
        return array_diff($plugins, ['wpemoji']);
    } else {
        return [];
    }
}

add_action('nav_menu_css_class', 'add_current_nav_class', 10, 2);

function add_current_nav_class($classes, $item)
{

    // Getting the current post details
    global $post;

    // Getting the post type of the current post
    $current_post_type      = get_post_type_object(get_post_type($post->ID));
    $current_post_type_slug = $current_post_type->rewrite[slug];

    // Getting the URL of the menu item
    $menu_slug = strtolower(trim($item->url));

    // If the menu item URL contains the current post types slug add the current-menu-item class
    if (strpos($menu_slug, $current_post_type_slug) !== false) {

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

function validatePhone($string) {
    $numbersOnly = str_replace("+", "", $string);
    $numbersOnly = str_replace("(", "", $numbersOnly);
    $numbersOnly = str_replace(")", "", $numbersOnly);
    $numbersOnly = str_replace(" ", "", $numbersOnly);
    $numbersOnly = str_replace("-", "", $numbersOnly);
    $numberOfDigits = strlen($numbersOnly);
    if ($numberOfDigits <= 15 && $numberOfDigits >= 10) {
        return true;
    } else {
        return false;
    }
}

add_shortcode( 'consult_form', function($atts){
    $a = shortcode_atts( [], $atts );

    ob_start();

    $passCheck = TRUE;
    $leads = new kmaLeads();
    $honeypot = new Akismet( site_url(),'16d52e09a262');

    //OK... form was submitted and it's not a bot... probably
    if($_POST['sec'] == '' && $_POST['formId'] == 'Digital Consult' ){

        //assign vars to our post items
        $website = $_POST['your_website'];
        $name    = $_POST['your_name'];
        $email   = $_POST['your_email'];
        $phone   = $_POST['your_phone'];
        $budget  = $_POST['your_budget'];

        $adderror = array(); //make array of error data so we can loop it later

        if($name == ''){
            $passCheck = FALSE;
            $adderror[] = 'First and last name are required. How else will we know who you are?';
        }
        if($email == ''){
            $passCheck = FALSE;
            $adderror[] = 'Please include your email address. You have one don\'t you?';
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match('/@.+\./', $email)) {
            $passCheck = FALSE;
            $emailFormattedBadly = TRUE;
            $adderror[] = 'The email address you entered doesn\'t look quite right. Better take another look.';
        }

        $successmessage = '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span><span class="sr-only">Success:</span> ';
        $errormessage = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ';

        if($passCheck) {

            //SET UP AND SEND LEAD VIA EMAIL
            //Set up headers
            $sendadmin = array(
                'to' => 'project@kerigan.com',
                'from' => get_bloginfo() . ' <noreply@kerigan.com>',
                'subject' => 'Digital Consultation submission from website',
                'bcc' => 'support@kerigan.com',
                'replyto' => $email
            );
            $sendreceipt = array(
                'to' => $email,
                'from' => get_bloginfo() . ' <noreply@kerigan.com>',
                'subject' => 'Your Digital Consultation',
                'bcc' => 'support@kerigan.com'
            );

            //datafields for email
            $postvars = array(
                'Name'           => $name,
                'Website'        => $website,
                'Email Address'  => $email,
                'Phone Number'   => $phone,
                'Monthly Budget' => $budget
            );

            $fontstyle = 'font-family: sans-serif;';
            $headlinestyle = 'style="font-size:20px; ' . $fontstyle . ' color:#000;"';
            $copystyle = 'style="font-size:16px; ' . $fontstyle . ' color:#333;"';
            $labelstyle = 'style="padding:4px 8px; background:#eaeaea; border:1px solid #fff; font-weight:bold; ' . $fontstyle . ' font-size:14px; color:#333; width:150px;"';
            $datastyle = 'style="padding:4px 8px; background:#eaeaea; border:1px solid #fff; ' . $fontstyle . ' font-size:14px; color:#333; "';

            $adminintrocopy = '<p ' . $copystyle . '>Details are below:</p>';
            $receiptintrocopy = '<p ' . $copystyle . '>Thank you for your interest in Kerigan Marketing Associates. Your project is important to us and you can expect to hear back within 24 hours. What you submitted is below:</p>';
	        $dateofemail = '<p style="font-size:12px; ' . $fontstyle . ' color:#000; text-align:center; margin:20px 0 0;">Date Submitted: ' . date('M j, Y') . ' @ ' . date('g:i a') . '</p>';

            $submittedData = '<table cellpadding="0" cellspacing="0" border="0" style="width:100%" ><tbody>';

	        $followups = '<p style="font-size:16px; margin:20px 20px 15px; ' . $fontstyle . ' color:#333; text-align: center;" >Ready for more marketing?</p>
            <table cellspacing="0" cellpadding="0" border="0" align="center" style="margin-top:10px;"  ><tr><td style="background-color: #000; padding:10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius:10px; border-bottom:3px solid #999;"><a style="'.$fontstyle.' display: block; color:#FFF; text-decoration:none;" href="https://keriganmarketing.com/about/">Meet our talented team.</a></td></tr></table><br>
            <table cellspacing="0" cellpadding="0" border="0" align="center" style="margin-top:10px;"  ><tr><td style="background-color: #000; padding:10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius:10px; border-bottom:3px solid #999;"><a style="'.$fontstyle.' display: block; color:#FFF; text-decoration:none;" href="https://keriganmarketing.com/newsroom/">Get free news on marketing tips and info.</a></td></tr></table>';

	        foreach ($postvars as $key => $var) {
                if (!is_array($var)) {
                    $submittedData .= '<tr><td ' . $labelstyle . ' >' . $key . '</td><td ' . $datastyle . '>' . $var . '</td></tr>';
                } else {
                    $submittedData .= '<tr><td ' . $labelstyle . ' >' . $key . '</td><td ' . $datastyle . '>';
                    foreach ($var as $k => $v) {
                        $submittedData .= '<span style="display:block;width:100%;">' . $v . '</span><br>';
                    }
                    $submittedData .= '</ul></td></tr>';
                }
            }
            $submittedData .= '</tbody></table>';

            $emaildata = array(
                'headline' => '<h2 ' . $headlinestyle . '>' . $sendadmin['subject'] . '</h2>',
                'introcopy' => $adminintrocopy . $submittedData . $dateofemail,
            );
            $receiptdata = array(
                'headline' => '<h2 ' . $headlinestyle . '>' . $sendreceipt['subject'] . '</h2>',
                'introcopy' => $receiptintrocopy . $submittedData . $followups . $dateofemail,
            );

            $leads->sendEmail($sendadmin, $emaildata);
            $leads->sendEmail($sendreceipt, $receiptdata);

            //Insert Post based on form submission
            $leads->wp_insert_post(
                array( //POST INFO
                    'post_content' => '',
                    'post_status' => 'publish',
                    'post_type' => 'lead',
                    'post_title' => $name . ' on ' . date('M j, Y'),
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'meta_input' => array( //POST META
                        'lead_info_lead_type' => $_POST['formId'],
                        'lead_info_name' => $name,
                        'lead_info_date' => date('M j, Y') . ' @ ' . date('g:i a e'),
                        'lead_info_phone_number' => $phone,
                        'lead_info_email_address' => $email,
                        'lead_info_interests' => $rServiceList,
                        'lead_info_message' => 'Digital Consult: ' . $website . ', ' . $budget,
                    )
                ), true
            );

            $successmessage .= '<strong>Thank you for your interest in Kerigan Marketing Associates. Your project is important to us and you can expect to hear back within 24 hours.</strong>';
            $showAlert = '<div class="alert alert-success digital-marketing" role="alert">'.$successmessage.'</div>';

        } else { // Pass failed. Let's show an error message.

            $listErrors = '';
            foreach($adderror as $errorDirection) {
                $listErrors .= '<br>• '.$errorDirection;
            }
            $errormessage .= '<strong>Errors were found in your submission. Please correct the indicated fields below and try again.</strong>';
            $showAlert = '<div class="alert alert-danger" role="alert">'.$errormessage.$listErrors.'</div>';

        }

    }

    if( $showAlert != '' ){
        echo $showAlert;
    }
    ?>
    <form class="form" method="post" >
        <div class="form-group">
            <input type="text" name="your_website" placeholder="Your Website *" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="text" name="your_name" placeholder="Your Name *" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="text" name="your_email" placeholder="Your Email *" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="text" name="your_phone" placeholder="Your Phone" class="form-control">
        </div>
        <div class="form-group">
            <select class="custom-select" name="your_budget" required style="width:100%;" >
                <option value="">Monthly Budget *</option>
                <option value="$1,000 - $1,499">$1,000 - $1,499</option>
                <option value="$1,500 - $2,499">$1,500 - $2,499</option>
                <option value="$2,500 - $4,999">$2,500 - $4,999</option>
                <option value="$5,000 - $7,499">$5,000 - $7,499</option>
            </select>
        </div>
        <div class="g-recaptcha" data-sitekey="6LcwNxQUAAAAANUji96UxBvziKoMjCw4A0fZdsrM"></div>
        <input type="text" value="" class="sec" name="sec" style="position:absolute; height:1px; width:1px; visibility:hidden; top:-1px; left: -1px;" >
        <input type="hidden" value="Digital Consult" name="formId" >
        <button type="submit" class="btn btn-block btn-primary btn-rounded consult-btn" >Get My Free Consultation</button>
    </form>
    <?php
    return ob_get_clean();
} );
