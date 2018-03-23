<?php

$FacebookPageID = (isset($_POST['facebook_page_id']) ? sanitize_text_field($_POST['facebook_page_id']) : get_option('facebook_page_id'));
$FacebookToken  = (isset($_POST['facebook_token']) ? sanitize_text_field($_POST['facebook_token']) : get_option('facebook_token'));

if (isset($_POST['facebook_submit_settings']) && $_POST['facebook_submit_settings'] == 'yes') {
    update_option('facebook_page_id',
        isset($_POST['facebook_page_id']) ? sanitize_text_field($_POST['facebook_page_id']) : $FacebookPageID);
    update_option('facebook_token',
        isset($_POST['facebook_token']) ? sanitize_text_field($_POST['facebook_token']) : $FacebookToken);
}

?>
<div class="page-wrapper" style="margin-left:-20px;">
    <div class="hero is-dark">
        <div class="hero-body">
            <div class="columns is-vcentered">
                <div class="column">
                    <p class="title">
                        Facebook Settings
                    </p>
                </div>
                <div class="column is-narrow">
                    <?php include(wp_normalize_path(dirname(dirname(__FILE__)) . '/includes/KMASig.php')); ?>
                </div>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="container is-fluid">
            <div class="content">
                <form enctype="multipart/form-data" name="facebook_settings" id="facebook_settings" method="post"
                      action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                    <input type="hidden" name="facebook_submit_settings" value="yes">

                    <div class="columns is-multiline">
                        <div class="column is-6">
                            <div class="editor-wrapper">
                                <h4 class="wp-heading-inline">Facebook Page ID</h4>
                                <div class="control">
                                    <input type="text" class="input" name="facebook_page_id"
                                           value="<?= $FacebookPageID; ?>" size="40">
                                </div>
                            </div>
                        </div>
                        <div class="column is-6">
                            <div class="editor-wrapper">
                                <h4 class="wp-heading-inline">Facebook Access Token</h4>
                                <div class="control">
                                    <input type="text" class="input" name="facebook_token"
                                           value="<?= $FacebookToken; ?>" size="40">
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="submit"><input class="button is-primary" type="submit" name="Submit"
                                             value="<?php _e('Update Settings') ?>"/></p>

                </form>
                <hr>
                <?php
                $facebookPageID = get_option('facebook_page_id');
                $facebookToken = get_option('facebook_token');
                if($facebookPageID!='' && $facebookToken!='') {
                    include('facebook-module.php');
                }
                ?>
            </div>
        </div>
    </section>
</div>