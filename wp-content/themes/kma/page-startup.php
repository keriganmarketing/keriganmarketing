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
define(ADMIN_EMAIL,'project@kerigan.com');
define(DOMAIN_NAME,'kerigan.com');
$passCheck = TRUE;

$leads = new kmaLeads();
$honeypot = new Akismet( site_url(),'16d52e09a262');

//OK... form was submitted and it's not a bot... probably
if($_POST['sec'] == '' && $_POST['formId'] == 'Start a Project' ){

    //assign vars to our post items
    $fName          = $_POST['fname'];
    $lName          = $_POST['lname'];
    $cName          = $_POST['cname'];
    $wName          = $_POST['wname'];
    $phone1         = $_POST['phone1'];
    $phone2         = $_POST['phone2'];
    $phone3         = $_POST['phone3'];
	$fullphone      = $_POST['fullphone'];
    $emailAddress   = $_POST['emailaddress'];
    $rServices      = $_POST['rservices'];
    $startMo        = $_POST['startmo'];
    $startYr        = $_POST['startyr'];
    $endMo          = $_POST['endmo'];
    $endYr          = $_POST['endyr'];
    $math           = $_POST['math'];
    $projectDetails =  htmlentities( stripslashes( $_POST['projectdetails'] ) );
    $fullNumber     = '('.$phone1.') '.$phone2.'-'.$phone3;
    $formType       = $_POST['formId'];

    $startDate = $startMo.' '.$startYr;
    $endDate = $endMo.' '.$endYr;

    //Run our own checks on submitted data

    $adderror = array(); //make array of error data so we can loop it later

    if($fName.$lName == ''){
        $passCheck = FALSE;
        $adderror[] = 'First and last name are required. How else will we know who you are?';
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

	if(is_array($rServices)) {
		$rServiceList = '<ul>';
		foreach ( $rServices as $rService ) {
			$rServiceList .= '<li>' . $rService . '</li>';
		}
		$rServiceList .= '</ul>';
	}else{
		$passCheck = FALSE;
		$adderror[] = 'Please check at least one service.';
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
                'subject' => 'Start a Project submission from website',
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
                'Organization' => $cName,
                'Website' => $wName,
                'Email Address' => $emailAddress,
                'Phone Number' => $fullphone,
                'Requested Services' => $rServiceList,
                'Project Details' => $projectDetails,
                'Start Date' => $startDate,
                'Launch Date' => $endDate
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
                    'post_title' => $fName . ' ' . $lName . ' on ' . date('M j, Y'),
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'meta_input' => array( //POST META
                        'lead_info_lead_type' => $formType,
                        'lead_info_name' => $fName . ' ' . $lName,
                        'lead_info_date' => date('M j, Y') . ' @ ' . date('g:i a e'),
                        'lead_info_phone_number' => $fullphone,
                        'lead_info_email_address' => $emailAddress,
                        'lead_info_interests' => $rServiceList,
                        'lead_info_message' => $projectDetails,
                    )
                ), true
            );

            $successmessage .= '<strong>Thank you for your interest in Kerigan Marketing Associates. Your project is important to us and you can expect to hear back within 24 hours.</strong>';
            $showAlert = '<div class="alert alert-success" role="alert">'.$successmessage.'</div>';

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
                        <div class="header-wrapper">
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col text-center" >
                                        <h1><?php echo get_the_title(); ?></h1>
                                        <?php
                                        if($headline!=''){
                                            echo '<p class="headline" ></p>';
                                        }
                                        ?>
                                        <?php echo apply_filters('the_content', $post->post_content);
                                        wp_reset_postdata();
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header><!-- .entry-header -->
					<div class="container-fluid">
						<div class="entry-content" style="padding-bottom: 60px;">
                            <?php if( $showAlert != '' ){
                                echo '<div class="row justify-content-center"><div class="col-lg-10 col-xl-8 text-center" >'.$showAlert.'</div></div>';
                            } ?>
                            <form class="form" action="" method="post" >
                                <section id="step1" class="form-section">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-10 col-xl-8 text-center" >
                                            <h2>Step 1</h2>
                                            <h3>Contact Info</h3>

                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <input class="form-control fill <?php if( !$passCheck && $fName == ''){ echo 'has-danger'; } ?>" name="fname" placeholder="First Name" value="<?php if(!$passCheck && $fName != ''){ echo $fName; } ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-control fill <?php if( !$passCheck && $lName == ''){ echo 'has-danger'; } ?>" name="lname" placeholder="Last Name" value="<?php if(!$passCheck && $lName != ''){ echo $lName; } ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <input class="form-control fill <?php if( !$passCheck && $cName == ''){ echo 'has-danger'; } ?>" name="cname" placeholder="Organization" value="<?php if(!$passCheck && $cName != ''){ echo $cName; } ?>" required>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-4">
                                                    <input class="form-control fill <?php if( !$passCheck && $wName == ''){ echo 'has-danger'; } ?>" name="wname" placeholder="Website (if you have one)" value="<?php if(!$passCheck && $wName != ''){ echo $wName; } ?>" >
                                                </div>
                                                <div class="col-md-4 <?php if( !$passCheck && $emailAddress == '' || $emailFormattedBadly ){ echo 'has-danger'; } ?>">
                                                    <input class="form-control fill" name="emailaddress" placeholder="Email Address" value="<?php if(!$passCheck && $emailAddress != ''){ echo $emailAddress; } ?>" required>
                                                </div>
                                                <div class="col-md-4 <?php if( !$passCheck && $fullphone == '' || $phoneFormattedBadly ){ echo 'has-danger'; } ?>">
                                                    <input class="form-control fill" name="fullphone" placeholder="Phone" value="<?php if(!$passCheck && $fullphone!=''){ echo $fullphone; } ?>" required>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </section>
                                <section id="step2" class="form-section">
                                    <div class="row justify-content-center">
                                        <div class="col-md-11 col-lg-11 col-xl-8 text-center" >
                                            <h2>Step 2</h2>
                                            <h3>Project Details</h3>

                                            <div class="form-group row justify-content-center">
                                                <div class="col-sm-6 col-lg-5">
                                                <p class="directions">What services are you looking for?</p>
                                                </div>
                                            </div>
                                            <div class="form-group row justify-content-left">

                                                <?php

                                                    $servItems = array(
                                                        'website-design' => 'Website Design',
                                                        'sem' => 'Search Engine Marketing',
                                                        'seo' => 'SEO',
                                                        'media' => 'Media Placement',
                                                        'social-media-mgmt' => 'Social Media Management',
                                                        'tv-radio' => 'TV & Radio',
                                                        'logo-branding' => 'Logo Design',
                                                        'other' => 'Other'
                                                    );

                                                    foreach($servItems as $serviceLink => $serviceName) { ?>

                                                        <div class="col-sm-6 col-lg-4 text-left <?php if( !$passCheck && !is_array($rServices) ){ echo 'has-danger'; } ?>">
                                                            <label class="custom-control custom-checkbox <?php echo $serviceLink; ?>">
                                                                <input type="checkbox" class="custom-control-input form-control form-control-lg" <?php
                                                                if(is_array($rServices)) {
	                                                                if ( in_array( $serviceName, $rServices ) ) {
		                                                                echo 'checked';
	                                                                }
                                                                }
                                                                ?> name="rservices[]" value="<?php echo $serviceName; ?>" >
                                                                <span class="custom-control-indicator"></span>
                                                                <span class="custom-control-description"><?php echo $serviceName; ?></span>
                                                            </label>
                                                        </div>

                                                <?php } ?>

                                            </div>

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Tell us about your project.</label>
                                                    <textarea class="form-control fill" style="min-height:100px;" name="projectdetails" placeholder="" required ><?php if(!$passCheck && $projectDetails != ''){ echo $projectDetails; } ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section id="step3" class="form-section">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-10 col-xl-8 text-center" >
                                            <h2>Step 3</h2>
                                            <h3>Project Timeline</h3>

                                            <div class="form-group row justify-content-center">
                                                <div class="col-md-6">
                                                    <p class="directions">When would you like to start?</p>
                                                    <div class="row form-group justify-content-center">
                                                        <div class="col">
                                                            <select name="startmo" class="custom-select form-control-lg fill" aria-invalid="false">
                                                                <?php
                                                                for($i=0;$i<11;$i++){
                                                                    echo '<option value="'.date('F',strtotime('+'.$i.' months')).'"';
                                                                    if(!$passCheck && $startMo == date('F',strtotime('+'.$i.' months'))){ echo ' selected '; }
                                                                    echo '>'.date('F',strtotime('+'.$i.' months')).'</option>';
                                                                }
                                                                ?>
                                                            </select>

                                                            <select name="startyr" class="custom-select form-control-lg fill" aria-invalid="false">
                                                            <?php
                                                                for($i=0;$i<5;$i++){
                                                                    echo '<option value="'.date('Y',strtotime('+'.$i.' years')).'"';
                                                                    if(!$passCheck && $startYr == date('Y',strtotime('+'.$i.' years'))){ echo ' selected '; }
                                                                    echo '>'.date('Y',strtotime('+'.$i.' years')).'</option>';
                                                                }
                                                            ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="directions">Desired completion date</p>
                                                    <div class="row form-group justify-content-center">
                                                        <div class="col">
                                                            <select name="endmo" class="custom-select form-control-lg fill" aria-invalid="false">
                                                                <?php
                                                                for($i=1;$i<12;$i++){
                                                                    echo '<option value="'.date('F',strtotime('+'.$i.' months')).'"';
                                                                    if(!$passCheck && $endMo == date('F',strtotime('+'.$i.' months'))){ echo ' selected '; }
                                                                    echo '>'.date('F',strtotime('+'.$i.' months')).'</option>';
                                                                }
                                                                ?>
                                                            </select>

                                                            <select name="endyr" class="custom-select form-control-lg fill" aria-invalid="false">
                                                                <?php
                                                                for($i=0;$i<5;$i++){
                                                                    echo '<option value="'.date('Y',strtotime('+'.$i.' years')).'"';
                                                                    if(!$passCheck && $endYr == date('F',strtotime('+'.$i.' years'))){ echo ' selected '; }
                                                                    echo '>'.date('Y',strtotime('+'.$i.' years')).'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <section id="step4" class="form-section">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-10 col-xl-8 text-center" >
                                             <h2>Step 4</h2>
                                             <h3>Are You Human?</h3>
                                             <div class="form-group row justify-content-center">
                                                 <div class="col-md-4 text-center">
                                                     <label>4 + 2 = ?</label>
                                                     <input class="form-control fill<?php if( !$passCheck && $math == ''){ echo 'has-danger'; } ?>" name="math" placeholder="" value="<?php if(!$passCheck && $math != ''){ echo $math; } ?>" required>
                                                 </div>
                                             </div>
                                        </div>
                                    </div>
                                </section>
                                <section id="step5" class="form-section end">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-7 col-xl-5 text-center" >
                                            <!--<div class="form-group row text-center justify-content-center">
                                                <div class="col">
                                                    <div class="g-recaptcha" data-sitekey="6LcwNxQUAAAAANUji96UxBvziKoMjCw4A0fZdsrM"></div>
                                                </div>
                                            </div>-->
                                            <div class="form-group row">
                                                <div class="col">
                                                    <div class="g-recaptcha" data-sitekey="6LcwNxQUAAAAANUji96UxBvziKoMjCw4A0fZdsrM"></div>
                                                    <input type="text" value="" class="sec" name="sec" style="position:absolute; height:1px; width:1px; visibility:hidden; top:-1px; left: -1px;" >
                                                    <input type="hidden" value="Start a Project" name="formId" >
                                                    <button class="btn btn-primary btn-block btn-lg" type="submit" >Let's Do This!</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </form>
                        </div><!-- .entry-content -->

                    </div>

                </article><!-- #post-## -->

			<?php endwhile; // End of the loop. ?>
					

		</main><!-- #main -->
	</div><!-- #primary -->
    <?php get_sidebar(); ?>
</div>
<?php get_footer();
