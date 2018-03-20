<?php
/**
 * Created by PhpStorm.
 * User: bbair
 * Date: 1/22/2018
 * Time: 11:22 AM
 */

namespace KMAPaymentCenter;


class FormShortcode
{
    public $pluginDir;
    public $pluginSlug;
    public $pluginName;
    protected $processorConfig;

    public function __construct()
    {
        $config           = new PluginConfig();
        $this->pluginDir  = $config->getVar('pluginDir');
        $this->pluginSlug = $config->getVar('pluginSlug');
        $this->pluginName = $config->getVar('pluginName');
        $this->pluginName = $config->getVar('processorConfig');

        $this->addShortcode();
    }

    protected function showForm($atts)
    {
        //echo '<pre>',print_r($atts),'</pre>';
        $imageDir = plugin_dir_url(dirname(__FILE__)) . 'forms/images';

        $service         = (isset($_REQUEST["service"]) ? $_REQUEST["service"] : null);
        $invoiceNumber   = (isset($_REQUEST["invoice_number"]) ? $_REQUEST["invoice_number"] : null);
        $serviceAmount   = (isset($_REQUEST["service_amount"]) ? $_REQUEST["service_amount"] : null);
        $serviceTerm     = (isset($_REQUEST["service_term"]) ? $_REQUEST["service_term"] : null);
        $serviceTermType = (isset($_REQUEST["service_term_type"]) ? $_REQUEST["service_term_type"] : null);
        $whatToPay       = (isset($_REQUEST["what_to_pay"]) ? $_REQUEST["what_to_pay"] : null);
        $invoiceAmount   = (isset($_REQUEST["invoice_amount"]) ? $_REQUEST["invoice_amount"] : null);
        $firstName       = (isset($_REQUEST["first_name"]) ? $_REQUEST["first_name"] : null);
        $lastName        = (isset($_REQUEST["last_name"]) ? $_REQUEST["last_name"] : null);
        $company         = (isset($_REQUEST["company"]) ? $_REQUEST["company"] : null);
        $emailAddress    = (isset($_REQUEST["email_address"]) ? $_REQUEST["email_address"] : null);
        $cardNumber      = (isset($_REQUEST["card_number"]) ? $_REQUEST["card_number"] : null);
        $billingName     = (isset($_REQUEST["billing_name"]) ? $_REQUEST["billing_name"] : null);
        $expirationMonth = (isset($_REQUEST["expiration_month"]) ? $_REQUEST["expiration_month"] : null);
        $expirationYear  = (isset($_REQUEST["expiration_year"]) ? $_REQUEST["expiration_year"] : null);
        $cardCvv         = (isset($_REQUEST["card_cvv"]) ? $_REQUEST["card_cvv"] : null);
        $billingAddress  = (isset($_REQUEST["billing_address"]) ? $_REQUEST["billing_address"] : null);
        $billingCity     = (isset($_REQUEST["billing_city"]) ? $_REQUEST["billing_city"] : null);
        $billingCountry  = (isset($_REQUEST["billing_country"]) ? $_REQUEST["billing_country"] : null);
        $billingState    = (isset($_REQUEST["billing_state"]) ? $_REQUEST["billing_state"] : null);
        $billingZip      = (isset($_REQUEST["billing_zip"]) ? $_REQUEST["billing_zip"] : null);

        $formSec       = (isset($_REQUEST["form_sec"]) ? $_REQUEST["form_sec"] : null);
        $formSubmitted = (isset($_REQUEST["form_submitted"]) ? $_REQUEST["form_submitted"] : null);

        if ($formSec == '' && $formSubmitted == 'yes') {
            new ProcessSubmission();
        }

        $format = (isset($atts['format']) ? $atts['format'] : 'standard');

        ob_start();
        include($this->pluginDir . '/forms/payment-form-' . $format . '.php');

        return ob_get_clean();
    }

    protected function addShortcode()
    {
        add_shortcode('payment_form', function ($atts) {
            return $this->showForm($atts);
        });
    }
}