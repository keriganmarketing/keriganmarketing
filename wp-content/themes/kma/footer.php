<?php

use Includes\Modules\Social\SocialSettingsPage;

/**
 * @package Seriously_Creative
 */
if ($isArchive != '' || get_queried_object_id() == 0) {
    $continueLinks = get_field('buttons', 11);
} else {
    $continueLinks = get_field('buttons');
}
?>
<div id="continue-section">
    <div class="container">
        <div class="row">
            <div class="offset-md-2 col-md-8">
                <?php if (is_array($continueLinks)) { ?>
                    <div class="continue-container text-xs-center">
                        <div class="overflow-text">
                            <p class="text-lg">Continue Exploring</p>
                        </div>
                        <div class="row justify-content-center">
                            <?php foreach ($continueLinks as $link) { ?>
                                <div class="col-md-4">
                                    <a class="btn btn-block btn-clear "
                                       href="<?php echo get_permalink($link); ?>"><?php echo $link->post_title; ?></a>
                                </div>
                            <?php } ?>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div id="bot" class="unstick fadein">
    <div class="container-fluid no-gutter">
        <div class="row text-center justify-content-center">
            <div class="col-xs-5 col-sm-4 col-md-3 col-lg-2 col-xl-1">
                <svg id="kma" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12.31 8.42">
                    <path class="cls-1" style="opacity: 0.38;"
                          d="M6.33,0H6A12.49,12.49,0,0,1,4,4.37,20.67,20.67,0,0,1,0,8l.29.42A18.36,18.36,0,0,1,6.15,7.25,18.36,18.36,0,0,1,12,8.42L12.31,8a20,20,0,0,1-4-3.64A11.72,11.72,0,0,1,6.33,0Z"/>
                </svg>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="footer-nav-container text-center">
                    <?php wp_nav_menu(['theme_location' => 'footer', 'menu_id' => 'footer-menu']); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col text-center">
                <div class="contact-container">
                    <p>161 Good Morning Street, Ste 101<br>
                        Port St. Joe, Florida 32456<br>
                        <a style="color: #FFF" href="tel:850-648-4560">850-648-4560</a> or <a style="color: #FFF" href="tel:850-807-4123">850-807-4123</a></p>
                    <div class="g-partner-container fadein">
                        <script src="https://apis.google.com/js/platform.js" async></script>
                        <div class="g-partnersbadge" data-agency-id="2550211180"></div>
                    </div>
                    <div class="social">
                        <?php
                        $socialLinks = new SocialSettingsPage();
                        $socialIcons = $socialLinks->getSocialLinks('svg', 'shape');
                        if (is_array($socialIcons)) {
                            foreach ($socialIcons as $socialId => $socialLink) {
                                echo '<a class="' . $socialId . '" href="' . $socialLink[0] . '" target="_blank" >' . $socialLink[1] . '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="bot-bot" class="unstick fadein">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <footer id="colophon" class="site-footer" role="contentinfo">
                    <div class="site-info text-center">
                        <p class="copyright">&copy;<?php echo date('Y'); ?> Kerigan Marketing Associates | <a style="color: #FFF;" href="/privacy-policy/" >Privacy Policy</a></p>
                    </div><!-- .site-info -->
                </footer><!-- #colophon -->
            </div>
        </div>
    </div>
</div>

</div>
<?php wp_footer(); ?>
<script>
    $(document).ready(function () {
        <?php if(is_front_page()){ ?>
            loadVideo(); //LOAD VIDEO
            rotateTestimonials(); //ROTATE TESTIMONIALS
            handleStickyFooter();
        <?php } ?>
        <?php if( $post->post_parent == 8){ ?>
            handleClientPage();
        <?php } elseif(is_front_page()){ ?>
            handleHomeHeader();
        <?php } else { ?>
            $("#scrollbg").addClass("show").removeClass("hide");
            handleStickyFooter();
        <?php } ?>

        <?php if(is_front_page() || $post->post_parent == 8){ //home or client page ?>
            handleClickDown();
        <?php } ?>

        <?php if($post->ID == 351){ // Hosting page ?>
            $('[data-toggle="tooltip"]').tooltip();
        <?php } ?>

        <?php if($post->ID == 13){ // Team page ?>
            handleTeam();
        <?php } ?>

        <?php if($post->ID == 437){ //Tyndall page ?>
            $('a.toggler').on('click', function (event) {
                event.preventDefault();
                let frame = $('.ad-container iframe'),
                    link = $(this).attr('data-source');

                frame.attr('src', link);
                $('a.toggler').removeClass('active');
                $(this).addClass('active');
            });
        <?php } ?>
    });
</script>
</body>
</html>