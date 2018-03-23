<?php

use Includes\Modules\KMAFacebook\FacebookController;

$facebook = new FacebookController();
//$reviews  = $facebook->getReviews(4);
//echo '<pre>',print_r($reviews),'</pre>';

$feed     = $facebook->getFeed(4);

//echo '<pre>',print_r($feed),'</pre>';
?>
<div class="columns is-multiline" >
<?php foreach ($feed->posts as $fbPost) {
    $isVideo  = ($fbPost->type == 'video');
    $hasImage = ($fbPost->full_picture != '' && $isVideo == false);
    $date     = date('M j') . ' at ' . date('g:i a');
    ?>
    <div class="column is-6-tablet is-4-desktop is-3-fullhd">
        <div class="card social-module facebook has-text-centered <?= ($hasImage == true ? 'has-image' : 'no-image'); ?>" style="padding:0 0 1rem;">
            <?php if ($hasImage == true) { ?>
                <div class="card-image">
                    <img src="<?= $fbPost->full_picture; ?>">
                </div>
            <?php } ?>
            <?php if ($isVideo == true) { ?>
                <div class="card-video">
                    <iframe
                            src="<?= $fbPost->link; ?>"
                            style="border:none;overflow:hidden"
                            scrolling="no"
                            frameborder="0"
                            allowTransparency="true"
                            allowFullScreen="true"
                            width="100%"
                            height="225">
                    </iframe>
                </div>
            <?php } ?>
            <div class="card-content">
                <p class="post-text"><?= $fbPost->message; ?></p>
                <p class="posted-on">Posted on <?= $date; ?></p>
            </div>
            <div class="card-bottom">
                <a class="button is-primary is-rounded" href="<?= $fbPost->permalink_url; ?>">Read more on Facebook</a>
            </div>
        </div>
    </div>
<?php } ?>
</div>

