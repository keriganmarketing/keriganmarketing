<?php

namespace KMAPaymentCenter;

class PluginSetup
{
    protected $processorConfig;
    public $pluginDir;
    public $pluginSlug;
    public $pluginName;

    public function __construct()
    {
        $config           = new PluginConfig();
        $this->pluginDir  = $config->getVar('pluginDir');
        $this->pluginSlug = $config->getVar('pluginSlug');
        $this->pluginName = $config->getVar('pluginName');
        $this->pluginName = $config->getVar('processorConfig');
    }

    public function installPlugin()
    {
        global $wpdb;

        $table = $wpdb->prefix."kmapc_transactions";
        $charset_collate = $wpdb->get_charset_collate();

        $structure = "CREATE TABLE $table (
					kmapc_id int(20) NOT NULL auto_increment,
					kmapc_date_created datetime default '0000-00-00 00:00:00',
					kmapc_amount double NOT NULL,
					kmapc_payer_email varchar(255) default NULL,
					kmapc_comment longtext,
					kmapc_transaction_id varchar(255) default NULL,
					kmapc_status tinyint(5) default '1',
					kmapc_payer_name varchar(255) NOT NULL,
					kmapc_serviceID int(20) NOT NULL default '0',
					kmapc_service_name  varchar(255) NOT NULL,
					kmapc_bill_cycle  varchar(255) NOT NULL,
					kmapc_recurring tinyint(5) default '0',
					kmapc_recurring_cancelled tinyint(5) default '0',
					UNIQUE KEY kmapc_transaction_id (kmapc_transaction_id),
					PRIMARY KEY  (kmapc_id)
					) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $structure );

        //now create services table
        $table = $wpdb->prefix."kmapc_services";
        $structure = "CREATE TABLE $table (
					kmapc_services_id INT(20) NOT NULL AUTO_INCREMENT,
					kmapc_services_title VARCHAR(255) NOT NULL,
					kmapc_services_price DOUBLE NOT NULL,
					kmapc_services_recurring BOOLEAN default 0,
					kmapc_services_recurring_period_type varchar(10),
					kmapc_services_recurring_period_number INT(20) not null default 0,
					kmapc_services_recurring_trial BOOLEAN default 0,
					kmapc_services_recurring_trial_days INT(20) not null default 0,
					kmapc_services_descr MEDIUMTEXT NULL,
					PRIMARY KEY  (kmapc_services_id)
					) $charset_collate;";

        dbDelta( $structure );

        update_option('kmapc_processor',"1");
        update_option('kmapc_currency',"USD");
        update_option('kmapc_ty_title',"Thank You!");
        update_option('kmapc_ty_text',"<p>Your payment has been completed. Thank you.</p>");
        update_option('kmapc_intro_text',"<p>Our online payment form is included below.</p>");
        update_option('kmapc_admin_email',"support@kerigan.com");
        update_option('kmapc_admin_send',"1");
        update_option('kmapc_show_comment_field',"1");
        update_option('kmapc_show_dd_text',"2"); //show drop down with services 1 or show text box for input 2
        update_option('kmapc_test',"1");
    }

    public function uninstallPlugin()
    {
        $config = new PluginConfig();
        $config->setTerminalState($this->processorConfig,'uninstall');

        delete_option('kmapc_processor');
        delete_option('kmapc_currency');
        delete_option('kmapc_ty_title');
        delete_option('kmapc_ty_text');
        delete_option('kmapc_intro_text');
        delete_option('kmapc_admin_email');
        delete_option('kmapc_admin_send');
        delete_option('kmapc_show_comment_field');
        delete_option('kmapc_show_dd_text');
        delete_option('kmapc_license');
        delete_option('kmapc_test');
    }

    public function updatePlugin()
    {
        new PluginUpdater( [
            'slug'               => $this->pluginSlug,
            'proper_folder_name' => $this->pluginName,
            'api_url'            => 'https://api.github.com/keriganmarketing/KMAPaymentCenter',
            'raw_url'            => 'https://raw.github.com/keriganmarketing/KMAPaymentCenter/master',
            'github_url'         => 'https://github.com/keriganmarketing/KMAPaymentCenter',
            'zip_url'            => 'https://github.com/keriganmarketing/KMAPaymentCenter/archive/master.zip',
            'sslverify'          => true,
            'requires'           => '3.0',
            'tested'             => '3.3',
            'readme'             => 'README.md',
            'access_token'       => '',
        ] );
    }
}