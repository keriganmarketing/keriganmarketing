<?php

namespace Includes\Modules\KMAFacebook;

use KeriganSolutions\FacebookFeed\FacebookFeed;
use KeriganSolutions\FacebookFeed\FacebookReviews;
use KeriganSolutions\FacebookFeed\Fetchers\FacebookRequest;

class FacebookController
{
    protected $facebookPageID;
    protected $facebookToken;

    public function __construct()
    {
        $this->facebookPageID = get_option('facebook_page_id');
        $this->facebookToken = get_option('facebook_token');
    }

    public function getReviews($num = 1)
    {
        $feed = new FacebookReviews($this->facebookPageID,$this->facebookToken);
        return $feed->fetch($num);
    }

    public function getFeed($num = 1)
    {
        $feed = new FacebookFeed($this->facebookPageID,$this->facebookToken);
        return $feed->fetch($num);
    }

    public function setupAdmin()
    {
        add_action('admin_menu', function(){
            $this->addMenus();
        });
    }

    protected function loadCss()
    {
        wp_enqueue_style('bluma_admin_css', 'https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.2/css/bulma.min.css',false, '1.0.0');
    }

    public function addMenus()
    {
        add_menu_page("Facebook Settings", "Facebook Settings", "administrator", 'kma-facebook', function () {
            $this->loadCss();
            include(wp_normalize_path( dirname(__FILE__) . '/templates/AdminOverview.php'));
        }, "dashicons-admin-generic");
    }


}
