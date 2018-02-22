<?php

namespace KMAPaymentCenter;

class AdminPages
{
    public $pluginDir;
    public $pluginSlug;
    public $pluginName;
    protected $processorConfig;

    public function __construct()
    {
        $config = new PluginConfig();
        $this->pluginDir = $config->getVar('pluginDir');
        $this->pluginSlug = $config->getVar('pluginSlug');
        $this->pluginName = $config->getVar('pluginName');
        $this->pluginName = $config->getVar('processorConfig');

        add_action('admin_menu', function(){
            $this->addMenus();
        });
    }

    protected function loadCss()
    {
        //echo wp_normalize_path($this->pluginDir . '/css/admin-style.css');

        wp_enqueue_style('bluma_admin_css', 'https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.2/css/bulma.min.css',false, '1.0.0');
        //wp_enqueue_style('kmapc_admin_css', wp_normalize_path($this->pluginDir . '/css/admin-style.css'),false, '1.0.1');
    }

    public function addMenus()
    {
        add_menu_page("Payment Center", "Payment Center", "administrator", 'kma-payments', function () {
            $this->loadCss();
            include(wp_normalize_path($this->pluginDir . '/templates/AdminOverview.php'));
        }, "dashicons-feedback");

        add_submenu_page( 'kma-payments', 'Payment Center Settings', 'Settings', 'administrator', 'payment-settings', function() {
            $this->loadCss();
            include(wp_normalize_path($this->pluginDir . '/templates/AdminSettings.php'));
        });

        add_submenu_page( 'kma-payments', 'Payment Center Services', 'Services', 'administrator', 'payment-services', function() {
            $this->loadCss();
            include(wp_normalize_path($this->pluginDir . '/templates/AdminServices.php'));
        });

        add_submenu_page( 'kma-payments', 'Payment Center Transactions', 'Transactions', 'administrator', 'payment-transactions', function() {
            $this->loadCss();
            include(wp_normalize_path($this->pluginDir . '/templates/AdminTransactions.php'));
        });
    }
}