<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Seriously_Creative
 */
if($isArchive != '' || get_queried_object_id() == 0 ){
    $continueLinks = get_field('buttons',11);
}else{
    $continueLinks = get_field('buttons');
}
?>
	<div id="continue-section">

		<div class="container">
			<div class="row">
				<div class="offset-md-2 col-md-8">
                    <?php if(is_array($continueLinks)){ ?>
					<div class="continue-container text-xs-center">
                        <div class="overflow-text">
						    <p class="text-lg">Continue Exploring</p>
                        </div>
						<div class="row justify-content-center">
                            <?php foreach($continueLinks as $link){ ?>
                                <div class="col-md-4">
                                    <a class="btn btn-block btn-clear " href="<?php echo get_permalink($link); ?>" ><?php echo $link->post_title; ?></a>
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
                    <svg id="kma" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12.31 8.42"><defs><style>.cls-1{opacity:0.38;}</style></defs><title>kma-triangle2</title><path class="cls-1" d="M6.33,0H6A12.49,12.49,0,0,1,4,4.37,20.67,20.67,0,0,1,0,8l.29.42A18.36,18.36,0,0,1,6.15,7.25,18.36,18.36,0,0,1,12,8.42L12.31,8a20,20,0,0,1-4-3.64A11.72,11.72,0,0,1,6.33,0Z"/></svg>
                </div>
            </div>
			<div class="row">
				<div class="col">
					<div class="footer-nav-container text-center">
						<?php wp_nav_menu( array( 'theme_location' => 'footer', 'menu_id' => 'footer-menu' ) ); ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col text-center">
					<div class="contact-container">
						<p>3706 Hwy 98, Suite 102<br>
						    Mexico Beach, FL 32456<br>
                            850-648-4560</p>
						<div class="g-partner-container fadein">
						<script src="https://apis.google.com/js/platform.js" async></script>
						<div class="g-partnersbadge" data-agency-id="2550211180"></div>
						</div>
						<div class="social">
						<?php
							//print_r(getSocialLinks());
                            $socialLinks = getSocialLinks();
                            foreach($socialLinks as $socialId => $socialLink){
                            echo '<a class="'.$socialId.'" href="'.$socialLink.'" target="_blank" ></a>';
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
							<p class="copyright">&copy;<?php echo date('Y'); ?> Kerigan Marketing Associates</p>
						</div><!-- .site-info -->
					</footer><!-- #colophon -->
				</div>
			</div>
		</div>
	</div>

</div>

<?php 

wp_footer(); 

?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css" >
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous" async ></script>

<script>

    function isScrolledIntoView(elem){
        var docViewTop = $(window).scrollTop();
        var docViewBottom = docViewTop + $(window).height();
        var elemTop = $(elem).offset().top;
        var elemBottom = elemTop + $(elem).height();
        return ((elemBottom >= docViewTop) && (elemTop <= docViewBottom) && (elemBottom <= docViewBottom) );
    }		
      
    <?php if(is_front_page()){ ?>
    $(window).load(function(){
      
        //LOAD VIDEO
        var windowsize = $(window).width(); 
        var hasTouch = 'ontouchstart' in window;
        var videoContent = '<div id="big-video-wrap">' +
                '<div id="big-video-vid">' +
                    '<video width="100%" height="auto" autoplay loop muted >' +
                        '<source src="<?php echo get_template_directory_uri() ?>/vid/kma3.mp4" type="video/mp4" >' +
                    '</video>' +
                '</div>' +
            '</div>';
        $('#video-container').html(videoContent); 
      
        //ROTATE TESTIMONIALS
        (function() {
			var quotes = $(".quotes");
			var quoteIndex = -1;
			function showNextQuote() {
				++quoteIndex;
				quotes.eq(quoteIndex % quotes.length)
                  .fadeIn(0)
                  .delay(5000)
                  .fadeOut(0, showNextQuote);
			}
			showNextQuote();
		})();
        
        $(window).scroll(function() {

			if(isScrolledIntoView($('#mid')) || isScrolledIntoView($('#continue-section'))){
				$( "#bot" ).addClass( "stick" ).removeClass( "unstick" );
				$( "#bot-bot" ).addClass( "stick" ).removeClass( "unstick" );
			}else{
				$( "#bot" ).addClass( "unstick" ).removeClass( "stick" );
				$( "#bot-bot" ).addClass( "unstick" ).removeClass( "stick" );
			}
			
		});
      
    });
    <?php } ?>
		
    <?php if( $post->post_parent == 8){ ?>
    $(window).load(function(){

        if(isScrolledIntoView($('#hider'))){
            $( "#scrollbg" ).removeClass( "show" ).addClass( "hide" );
        }else{
            $( "#scrollbg" ).addClass( "show" ).removeClass( "hide" );
        }

        $(window).scroll(function() {

            if($(window).scrollTop() == 0){
                $( "#scrollbg" ).removeClass( "show" ).addClass( "hide" );
            }
            if($(window).scrollTop() > 0){
                $( "#scrollbg" ).addClass( "show" ).removeClass( "hide" );
            }
        });

    });
    <?php } elseif(is_front_page()){ ?>
    $(window).load(function(){

        $(window).scroll(function() {

            if($(window).scrollTop() == 0){
                $( "#scrollbg" ).removeClass( "show" ).addClass( "hide" );
            }
            if($(window).scrollTop() > 0){
                $( "#scrollbg" ).addClass( "show" ).removeClass( "hide" );
            }
        });

    });
    <?php } else { ?>
    $(window).load(function(){

        $( "#scrollbg" ).addClass( "show" ).removeClass( "hide" );
    
    });
    <?php } ?>
      
    $(window).load(function(){
      
        $( "#bot" ).addClass( "stick" );
        $( "#bot-bot" ).addClass( "stick" );
        //$( "#bot" ).removeClass( "unstick" );
        //$( "#bot-bot" ).removeClass( "unstick" );
    });
  
  
		/*$(window).scroll(function() {
			
			
			if(isScrolledIntoView($('#mid'))){
				$( "#bot" ).addClass( "stick" ).removeClass( "unstick" );
				$( "#bot-bot" ).addClass( "stick" ).removeClass( "unstick" );
			}else{
				$( "#bot" ).addClass( "unstick" ).removeClass( "stick" );
				$( "#bot-bot" ).addClass( "unstick" ).removeClass( "stick" );
			}
          
            
			
		});*/
		
		<?php //} else { ?>
		
		//$( "#scrollbg" ).addClass( "show" ).removeClass( "hide" );
		//$( "#bot" ).addClass( "stick" ).removeClass( "unstick" );
		//$( "#bot-bot" ).addClass( "stick" ).removeClass( "unstick" );
        
		<?php //} ?>

</script>
<?php if(is_front_page() || $post->post_parent == 8){ //home or client page ?>
<script>
$('#clickdown a').on('click', function(e){
    e.preventDefault();
    var target = $(this).attr('href');
    $('html, body').stop().animate({
       scrollTop: $(target).offset().top
    }, 1000);
});
</script>
<?php } ?>
<?php if($post->ID == 351){ // Hosting page ?>
<script>
(function($) {
        $(document).ready(function() {
            $(function () {
                $('[data-toggle="tooltip"]').tooltip()
            });
        });
})(jQuery);
</script>
<?php } ?>
<?php if($post->ID == 13){ // Team page ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/grayjs/js/jquery.gray.min.js"></script>
<script>
(function($) {
    $(document).ready(function() {
        $(".grayscale").hover(
            function() {
                $(this).addClass('grayscale-off');
            }, function() {
                $(".grayscale").gray();
            }

        );
        $(function () {
            $(".grayscale").gray();
        });

    });
})(jQuery);
</script>
<?php } ?>
<?php if($post->ID == 437){ //Tyndall page ?>
<script>
(function($) {
    $(document).ready(function() {

        $('a.toggler').on('click', function(event) {
            event.preventDefault();
            var frame = $('.ad-container iframe'),
            link = $(this).attr('data-source');

            frame.attr('src', link);
            $('a.toggler').removeClass('active');
            $(this).addClass('active');
        });

    });
})(jQuery);
</script>
<?php } ?>
<?php if($post->ID == 15){ //Contact page ?>
<script>

    $(document).ready(function () {
        $('#fname').on("change", function () {
            var res = $('#fname').val();
            console.log(res);
            if (res == 'dragondrop') {
                console.log('the dragon has dropped!')
                document.body.style.cursor = "url('/images/dragon.png'), auto";

                $(document).click(function(e) {
                    var xClick = e.clientX;
                    var yClick = e.clientY;
                    $("body").pottytime({
                        origin:{
                            x:xClick+49,
                            y:yClick+38
                        },
                        particleClass:"poo"
                    });
                });

                (function($){

                    $.fn.pottytime = function(options){

                        var settings = $.extend({
                            particleClass: "poo",
                            origin: {x:0,y:0},
                            particles: 1,
                            radius: 100,
                            complete: function() {}
                        },options);

                        return this.each(function() {
                            for(i=0;i<settings.particles;i++){
                                var particle = $("<div />").addClass(settings.particleClass);
                                $(particle).css("position","absolute");
                                $(this).append($(particle));
                                $(particle).offset({
                                    top:settings.origin.y-$(particle).height()/2,
                                    left:settings.origin.x-$(particle).width()/2
                                });
                                $(particle).animate(
                                    {
                                        "margin-top": "1200px"
                                    },
                                    {
                                        "duration": 3000,
                                        "complete": function (){
                                            $(this).remove();
                                        }
                                    }
                                );
                            }
                            settings.complete.call(this);
                        });

                    };
                }(jQuery));


            }
            if (res == 'killa') {
                document.body.style.cursor = 'crosshair';

                $(document).click(function(e) {
                    var xClick = e.clientX;
                    var yClick = e.clientY;
                    $("body").explosion({
                        origin:{
                            x:xClick,
                            y:yClick
                        },
                        particleClass:"particle"
                    });
                });

                (function($){

                    $.fn.explosion = function(options){

                        var settings = $.extend({
                            particleClass: "particle",
                            origin: {x:0,y:0},
                            particles: 30,
                            radius: 500,
                            complete: function() {}
                        },options);

                        return this.each(function() {
                            for(i=0;i<settings.particles;i++){
                                var particle = $("<div />").addClass(settings.particleClass);
                                $(particle).css("position","absolute");
                                $(this).append($(particle));
                                $(particle).offset({
                                    top:settings.origin.y-$(particle).height()/2,
                                    left:settings.origin.x-$(particle).width()/2
                                });
                                $(particle).animate(
                                    {
                                        "margin-top": (Math.floor(Math.random()*settings.radius)-settings.radius/2)+"px",
                                        "margin-left": (Math.floor(Math.random()*settings.radius)-settings.radius/2)+"px",
                                        "opacity": 0
                                    },
                                    {
                                        "duration": Math.floor(Math.random()*200)+200,
                                        "complete": function (){
                                            $(this).remove();
                                        }
                                    }
                                );
                            }
                            settings.complete.call(this);
                        });

                    };
                }(jQuery));
                
            }
        });
    });

</script>
<style>
    .particle {
        width:8px;
        height:8px;
        border-radius:50%;
        background-color:orange;
        z-index: 99999999999999;
    }
    .poo {
        width:8px;
        height:15px;
        border-radius:50%;
        background-color:saddlebrown;
        z-index: 99999999999999;
    }
</style>
<?php } ?>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-15421411-1', 'keriganmarketing.com');
    ga('send', 'pageview');

</script>
<script type="text/javascript">
    var _mfq = _mfq || [];
    (function() {
        var mf = document.createElement("script");
        mf.type = "text/javascript"; mf.async = true;
        mf.src = "//cdn.mouseflow.com/projects/fecb17e6-699d-4885-9c51-b3d901b50aba.js";
        document.getElementsByTagName("head")[0].appendChild(mf);
    })();
</script>
<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/4026727.js"></script>
</body>
</html>
