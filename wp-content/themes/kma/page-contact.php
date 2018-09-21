<?php
/**
 * The template for the contact page
 * @package Seriously_Creative
 */

if(get_field('headline')!=''){
    $headline = get_field('headline');
}else{
    $headline = get_the_title();
}

date_default_timezone_set("America/Chicago");

//default settings
define(ADMIN_EMAIL,'info@kerigan.com');
define(DOMAIN_NAME,'kerigan.com');
$passCheck = TRUE;

$leads = new kmaLeads();
$honeypot = new Akismet( site_url(),'16d52e09a262');

//OK... form was submitted and it's not a bot... probably
if($_POST['sec'] == '' && $_POST['formId'] == 'Contact Form' ){

    //assign vars to our post items
    $fName          = $_POST['fname'];
    $lName          = $_POST['lname'];
	$company        = $_POST['company'];
    $phone1         = $_POST['phone1'];
    $phone2         = $_POST['phone2'];
    $phone3         = $_POST['phone3'];
	$fullphone      = $_POST['fullphone'];
	$howdidyouhear  = $_POST['howdidyouhear'];
    $math           = $_POST['math'];
    $emailAddress   = $_POST['emailaddress'];
    $emailMessage   =  htmlentities( stripslashes( $_POST['emailmessage'] ) );
    $fullNumber     = '('.$phone1.') '.$phone2.'-'.$phone3;
    $formType       = $_POST['formId'];

    //Run our own checks on submitted data

    $adderror = array(); //make array of error data so we can loop it later

    if($fName.$lName == ''){
        $passCheck = FALSE;
        $adderror[] = 'First and last name are required. How else will we know who you are?';
    }
	if($company == ''){
		$passCheck = FALSE;
		$adderror[] = 'Is there a company or business name we can look for?';
	}
	if($howdidyouhear == ''){
		$passCheck = FALSE;
		$adderror[] = 'How did you hear about us?';
	}
    if($emailAddress == ''){
        $passCheck = FALSE;
        $adderror[] = 'Please include your email address. You have one don\'t you?';
    }elseif(!filter_var($emailAddress, FILTER_VALIDATE_EMAIL) && !preg_match('/@.+\./', $emailAddress)) {
        $passCheck = FALSE;
        $emailFormattedBadly = TRUE;
        $adderror[] = 'The email address you entered doesn\'t look quite right. Better take another look.';
    }
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

	if($fullphone == ''){
		$passCheck = FALSE;
		$adderror[] = 'How would you like us to call you? We promise not to give your number to telemarketers.';
	}elseif(!validatePhone($fullphone)){
		$passCheck = FALSE;
		$phoneFormattedBadly = TRUE;
		$adderror[] = 'Please use the standard 10-digit phone number format.';
	}

    if($math != 6 ){
        $passCheck = FALSE;
        $adderror[] = 'Either you aren\'t very good at math or you aren\'t human...';
    }

    //assign vars to honeypot submission
    $honeypot->setCommentAuthor( $fName.' '.$lName );
    $honeypot->setCommentAuthorEmail( $emailAddress );
    $honeypot->setCommentContent( $emailMessage );
    $honeypot->setPermalink(strtolower(str_replace(' ','-',$_POST['fullname']).'-'.date('Y-m-d')));

    $successmessage = '<span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span><span class="sr-only">Success:</span> ';
    $errormessage = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span><span class="sr-only">Error:</span> ';

    if($honeypot->isCommentSpam()){
        //THIS IS SPAM
        //TODO: insert post marked as spam... how do we do that again?

        $passCheck = FALSE; //Why not?
        $errormessage .= 'Your message was flagged by our state-of-the-art spam checker. If you are selling SEO, I doubt you could do better than we can. If you are selling fake watches or purses, we don\'t want any. Otherwise, try adjusting your message to be less spammy.';

    } else { //NOT SPAM

        //Passed all checks
        if($passCheck) {

            //SET UP AND SEND LEAD VIA EMAIL
            //Set up headers
            $sendadmin = array(
                'to' => ADMIN_EMAIL,
                'from' => get_bloginfo() . ' <noreply@' . DOMAIN_NAME . '>',
                'subject' => 'Contact form submission from website',
                'bcc' => 'support@kerigan.com',
                'replyto' => $emailAddress
            );
            $sendreceipt = array(
                'to' => $emailAddress,
                'from' => get_bloginfo() . ' <noreply@' . DOMAIN_NAME . '>',
                'subject' => 'Thanks for contacting us',
                'bcc' => 'support@kerigan.com'
            );

            //datafields for email
            $postvars = array(
                'Name' => $fName . ' ' . $lName,
                'Email Address' => $emailAddress,
                'Phone Number' => $fullphone,
                'Organization' => $company,
                'How did you hear about us?' => $howdidyouhear,
                'Message' => $emailMessage,
            );

            $fontstyle = 'font-family: sans-serif;';
            $headlinestyle = 'style="font-size:20px; ' . $fontstyle . ' color:#000; text-align:center;"';
            $copystyle = 'style="font-size:16px; ' . $fontstyle . ' color:#333; text-align:center; margin:20px;"';
            $labelstyle = 'style="padding:4px 8px; background:#eaeaea; border:1px solid #fff; font-weight:bold; ' . $fontstyle . ' font-size:14px; color:#333; width:150px;"';
            $datastyle = 'style="padding:4px 8px; background:#eaeaea; border:1px solid #fff; ' . $fontstyle . ' font-size:14px; color:#333; "';

            $adminintrocopy = '<p ' . $copystyle . '>You have received a contact form submission from the website. Details are below:</p>';
            $receiptintrocopy = '<p ' . $copystyle . '>Thank you for contacting Kerigan Marketing Associates. We really appreciate your interest and you can expect to hear back within 24 hours.
 What you submitted is below:</p>';
            $dateofemail = '<p style="font-size:12px; ' . $fontstyle . ' color:#000; text-align:center; margin:20px 0 0;">Date Submitted: ' . date('M j, Y') . ' @ ' . date('g:i a') . '</p>';


            $followups = '<p style="font-size:16px; margin:20px 20px 15px; ' . $fontstyle . ' color:#333; text-align: center;" >Ready for more marketing?</p>
            <table cellspacing="0" cellpadding="0" border="0" align="center" style="margin-top:10px;"  ><tr><td style="background-color: #000; padding:10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius:10px; border-bottom:3px solid #999;"><a style="'.$fontstyle.' display: block; color:#FFF; text-decoration:none;" href="https://keriganmarketing.com/newsroom/">Get free news on marketing tips and info.</a></td></tr></table><br>
            <table cellspacing="0" cellpadding="0" border="0" align="center" style="margin-top:10px;"  ><tr><td style="background-color: #000; padding:10px; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius:10px; border-bottom:3px solid #999;"><a style="'.$fontstyle.' display: block; color:#FFF; text-decoration:none;" href="https://keriganmarketing.com/startup/">Get started on a project.</a></td></tr></table>';


            $submittedData = '<table cellpadding="0" cellspacing="0" border="0" style="width:100%" ><tbody>';
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
                    'post_title' => $fName . ' ' . $lName . ' on ' . date('M j, Y'),
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'meta_input' => array( //POST META
                        'lead_info_lead_type' => $formType,
                        'lead_info_name' => $fName . ' ' . $lName,
                        'lead_info_date' => date('M j, Y') . ' @ ' . date('g:i a e'),
                        'lead_info_phone_number' => $fullphone,
                        'lead_info_company' => $company,
                        'howdidyouhear' => $howdidyouhear,
                        'lead_info_email_address' => $emailAddress,
                        'lead_info_message' => $emailMessage,
                    )
                ), true
            );

            $successmessage .= '<strong>Thank you for contacting Kerigan Marketing Associates. We really appreciate your interest and you can expect to hear back within 24 hours.</strong>';
            $showAlert = '<div class="alert alert-success contact-form" role="alert">'.$successmessage.'</div>';

        } else { // Pass failed. Let's show an error message.

            $listErrors = '';
            foreach($adderror as $errorDirection) {
                $listErrors .= '<br>• '.$errorDirection;
            }
            $errormessage .= '<strong>Errors were found in your submission. Please correct the indicated fields below and try again.</strong>';
            $showAlert = '<div class="alert alert-danger" role="alert">'.$errormessage.$listErrors.'</div>';

        }
    }
}

get_header(); ?>
	<div id="mast">

	</div>
	<div id="scrollbg" class="hide"></div>
</div>
<div id="mid">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			
			<?php while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header" <?php if($thumb_url != ''){ ?> style="background-image:url('<?php echo $thumb_url; ?>');" <?php } ?> >
                        <div class="header-wrapper" style="background-color: rgba(255,255,255,.7);">
                            <div class="container-fluid">
                                <div class="row">

                                    <div id="map"></div>
                                    <div class="col text-center" style="position:absolute;" >
                                        <h1><?php echo get_the_title(); ?></h1>
                                        <?php
                                        if($headline!=''){
                                            echo '<p class="headline" ></p>';
                                        }
                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </header><!-- .entry-header -->
					<div class="container">
						<div class="row">
							<div class="col-md-6 fill-height" >
                                <?php
                                the_content();
                                ?>
                            </div>
							<div class="col-md-6" >
								<div class="entry-content pad" style="padding-bottom: 60px;">

                                    <?php if( $showAlert != '' ){ echo $showAlert; ?>
                                        <!-- Google Code for Completed Contact Form Conversion Page -->
                                        <script type="text/javascript">
                                            /* <![CDATA[ */
                                            var google_conversion_id = 851550142;
                                            var google_conversion_language = "en";
                                            var google_conversion_format = "3";
                                            var google_conversion_color = "ffffff";
                                            var google_conversion_label = "Y1ZFCJeJ73IQvr-GlgM";
                                            var google_remarketing_only = false;
                                            /* ]]> */
                                        </script>
                                        <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
                                        <noscript>
                                            <div style="display:inline;">
                                                <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/851550142/?label=Y1ZFCJeJ73IQvr-GlgM&amp;guid=ON&amp;script=0"/>
                                            </div>
                                        </noscript>
                                    <?php } ?>

									<form class="form" action="" method="post" >
										<div class="form-group row">
											<div class="col-md-6">
												<input id="fname" class="form-control fill <?php if( !$passCheck && $fName == ''){ echo 'has-danger'; } ?>" name="fname" placeholder="First Name" value="<?php if(!$passCheck){ echo $fName; } ?>" required>
											</div>
											<div class="col-md-6">
												<input class="form-control fill <?php if( !$passCheck && $lName == ''){ echo 'has-danger'; } ?>" name="lname" placeholder="Last Name" value="<?php if(!$passCheck){ echo $lName; } ?>" required>
											</div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-6 <?php if( !$passCheck && $emailAddress == '' || $emailFormattedBadly ){ echo 'has-danger'; } ?>">
                                                <input class="form-control fill" name="emailaddress" placeholder="Email" value="<?php if(!$passCheck){ echo $emailAddress; } ?>" required>
                                            </div>
                                            <div class="col-md-6 <?php if( !$passCheck && $fullphone == '' || $phoneFormattedBadly ){ echo 'has-danger'; } ?>">
                                                <input class="form-control fill" name="fullphone" placeholder="Phone" value="<?php if(!$passCheck && $fullphone!=''){ echo $fullphone; } ?>" required>
                                            </div>

                                        </div>
                                        <div class="form-group row">
                                            <div class="col-md-6 <?php if( !$passCheck && $company == '' ){ echo 'has-danger'; } ?>">
                                                <input class="form-control fill" name="company" placeholder="Organization" value="<?php if(!$passCheck){ echo $company; } ?>" required>
                                            </div>
                                            <div class="col-md-6 <?php if( !$passCheck && $howdidyouhear == '' ){ echo 'has-danger'; } ?>">
                                                <input class="form-control fill" name="howdidyouhear" placeholder="How did you hear about us?" value="<?php if(!$passCheck){ echo $howdidyouhear; } ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
											<div class="col <?php if(!$passCheck && $emailMessage == ''){ echo 'has-danger'; } ?>">
												<textarea class="form-control fill" style="min-height:100px;" name="emailmessage" placeholder="How can we help?" required ><?php if(!$passCheck && $emailMessage != ''){ echo $emailMessage; } ?></textarea>
											</div>
                                        </div>

                                        <div class="form-group row justify-content-center">
                                            <div class="input-group col-md-4 text-center">
                                                <div class="input-group-addon">4 + 2 =</div>
                                                <input class="form-control fill<?php if( !$passCheck && $math == ''){ echo 'has-danger'; } ?>" name="math" placeholder="?" value="<?php if(!$passCheck){ echo $math; } ?>" required>
                                            </div>

											<div class="col">
                                                <input type="text" value="" class="sec" name="sec" style="position:absolute; height:1px; width:1px; visibility:hidden; top:-1px; left: -1px;" >
                                                <input type="hidden" value="Contact Form" name="formId" >
												<button class="btn btn-block btn-primary" type="submit" >Submit</button>
											</div>
										</div>
									</form>
								</div><!-- .entry-content -->

							</div>
						</div>

					</div>
				</article><!-- #post-## -->

			<?php endwhile; // End of the loop. ?>
					

		</main><!-- #main -->
	</div><!-- #primary -->
    <?php get_sidebar(); ?>
</div>
<div id="enews-signup" class="enews-signup">
    <div class="container">
    <div class="row justify-content-center align-items-center" >

        <div class="col-md-10">

            <form action="https://keriganmarketing.us16.list-manage.com/subscribe/post?u=8ea4a7c6d74936f9af7e68314&amp;id=206b55a059" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="form validate" target="_blank" novalidate>
                <div class="row justify-content-center">
                    <div class="col-md-5 text-md-right align-self-center">
                        <p style="margin:0; color:#FFF;">Receive our newsletter with valuable marketing tips, updates from Google, and more.</p>
                    </div>
                    <div class="col-md-5 text-center align-self-center">
                        <input type="email" value="" name="EMAIL" class="form-control inline my-auto fill email" id="mce-EMAIL" placeholder="Email Address" required>
                        <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                        <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_8ea4a7c6d74936f9af7e68314_206b55a059" tabindex="-1" value=""></div>
                    </div>
                    <div class="col-md-2 text-center align-self-center">
                        <input type="submit" value="Sign Up" name="subscribe" id="mc-embedded-subscribe" class="btn btn-clear btn-block" />
                    </div>
            </form>

        </div>

    </div>
    </div>

</div>
</div>

<?php get_footer(); ?>
<script type="text/javascript">
    // When the window has finished loading create our google map below
    //google.maps.event.addDomListener(window, 'load', initMap);

    function initMap() {
        // Basic options for a simple Google Map
        // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
        var mapOptions = {
            // How zoomed in you want the map to start at (always required)
            zoom: 8,

            // The latitude and longitude to center the map (always required)
            center: new google.maps.LatLng(30.622739, -86.316544), // Mexico Beach
            disableDefaultUI: true,
            // How you would like to style the map.
            // This is where you would paste any style found on Snazzy Maps.
            styles: [
                {
                    "featureType": "administrative",
                    "elementType": "labels.text.fill",
                    "stylers": [
                        {
                            "color": "#444444"
                        }
                    ]
                },
                {
                    "featureType": "landscape",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#f2f2f2"
                        }
                    ]
                },
                {
                    "featureType": "poi",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "poi.business",
                    "elementType": "geometry.fill",
                    "stylers": [
                        {
                            "visibility": "on"
                        }
                    ]
                },
                {
                    "featureType": "road",
                    "elementType": "all",
                    "stylers": [
                        {
                            "saturation": -100
                        },
                        {
                            "lightness": 45
                        }
                    ]
                },
                {
                    "featureType": "road.highway",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "simplified"
                        }
                    ]
                },
                {
                    "featureType": "road.arterial",
                    "elementType": "labels.icon",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "transit",
                    "elementType": "all",
                    "stylers": [
                        {
                            "visibility": "off"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "all",
                    "stylers": [
                        {
                            "color": "#b4d4e1"
                        },
                        {
                            "visibility": "on"
                        }
                    ]
                },
                {
                    "featureType": "water",
                    "elementType": "geometry.fill",
                    "stylers": [
                        {
                            "color": "#075773"
                        }
                    ]
                }
            ]
        };

        // Get the HTML DOM element that will contain your map
        // We are using a div with id="map" seen below in the <body>
        var mapElement = document.getElementById('map');

        // Create the Google Map using our element and options defined above
        var map = new google.maps.Map(mapElement, mapOptions);

        // Let's also add a marker while we're at it
        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(29.951806, -85.423529),
            map: map,
            title: 'Kerigan Marketing Associates'
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZykYrI7XvRfH5lsEEYnC0aPiOJ4cFnVg&callback=initMap" async defer></script>
