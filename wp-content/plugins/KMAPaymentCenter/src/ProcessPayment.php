<?php

namespace KMAPaymentCenter;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

date_default_timezone_set('America/Chicago');
define("AUTHORIZENET_LOG_FILE", "phplog");

class ProcessPayment
{
    protected $requiredFields;
    protected $inputFields;
    public    $valid;
    public    $errors;
    public    $return;

    public function __construct()
    {
        $this->inputFields['service_name']            = (isset($_REQUEST['service_name']) ? $_REQUEST['service_name'] : null);
        $this->inputFields['invoiceNumber']      = (isset($_REQUEST["invoice_number"]) ? $_REQUEST["invoice_number"] : null);
        $this->inputFields['serviceAmount']      = (isset($_REQUEST["service_amount"]) ? $_REQUEST["service_amount"] : null);
        $this->inputFields['serviceTerm']        = (isset($_REQUEST["service_term"]) ? $_REQUEST["service_term"] : null);
        $this->inputFields['serviceTermType']    = (isset($_REQUEST["service_term_type"]) ? $_REQUEST["service_term_type"] : null);
        $this->inputFields['whatToPay']          = (isset($_REQUEST["what_to_pay"]) ? $_REQUEST["what_to_pay"] : null);
        $this->inputFields['invoiceAmount']      = (isset($_REQUEST["invoice_amount"]) ? $_REQUEST["invoice_amount"] : null);
        $this->requiredFields['firstName']       = (isset($_REQUEST["first_name"]) ? $_REQUEST["first_name"] : null);
        $this->requiredFields['lastName']        = (isset($_REQUEST["last_name"]) ? $_REQUEST["last_name"] : null);
        $this->requiredFields['company']         = (isset($_REQUEST["company"]) ? $_REQUEST["company"] : null);
        $this->requiredFields['emailAddress']    = (isset($_REQUEST["email_address"]) ? $_REQUEST["email_address"] : null);
        $this->requiredFields['cardNumber']      = (isset($_REQUEST["card_number"]) ? $_REQUEST["card_number"] : null);
        $this->requiredFields['billingName']     = (isset($_REQUEST["billing_name"]) ? $_REQUEST["billing_name"] : null);
        $this->requiredFields['expirationMonth'] = (isset($_REQUEST["expiration_month"]) ? $_REQUEST["expiration_month"] : null);
        $this->requiredFields['expirationYear']  = (isset($_REQUEST["expiration_year"]) ? $_REQUEST["expiration_year"] : null);
        $this->requiredFields['cardCvv']         = (isset($_REQUEST["card_cvv"]) ? $_REQUEST["card_cvv"] : null);
        $this->requiredFields['billingAddress']  = (isset($_REQUEST["billing_address"]) ? $_REQUEST["billing_address"] : null);
        $this->requiredFields['billingCity']     = (isset($_REQUEST["billing_city"]) ? $_REQUEST["billing_city"] : null);
        $this->requiredFields['billingCountry']  = (isset($_REQUEST["billing_country"]) ? $_REQUEST["billing_country"] : null);
        $this->requiredFields['billingState']    = (isset($_REQUEST["billing_state"]) ? $_REQUEST["billing_state"] : null);
        $this->requiredFields['billingZip']      = (isset($_REQUEST["billing_zip"]) ? $_REQUEST["billing_zip"] : null);

        $this->controlPaymentType();

        return $this->return;
    }

    protected function controlPaymentType()
    {
        $merchantAuthentication = $this->getMerchantCredentials();

        $response = null;
        if ($this->inputFields['whatToPay'] == 'invoice') {
            $response = $this->payInvoice($merchantAuthentication);
        }
        if ($this->inputFields['whatToPay'] == 'recurring-service') {
            $response = $this->createRecurringPayment($merchantAuthentication);
        }

        $this->return = $response;
    }

    protected function getMerchantCredentials()
    {
        $pluginConfig           = new PluginConfig();
        $processorConfig        = $pluginConfig->setTerminalState($pluginConfig->getVar('processorConfig'), 'default');
        $TestMode               = (get_option('kmapc_test') != 2 ? 'TEST' : 'LIVE'); // 1=on; 2=off;
        $merchantCredentials    = $processorConfig['AUTHORIZE'][$TestMode];
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($merchantCredentials['API Login ID']);
        $merchantAuthentication->setTransactionKey($merchantCredentials['API Transaction Key']);

        return $merchantAuthentication;
    }

    protected function payInvoice($merchantAuthentication)
    {
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($this->requiredFields['cardNumber']);
        $creditCard->setExpirationDate($this->requiredFields['expirationYear'] . "-" . $this->requiredFields['expirationMonth']);
        $creditCard->setCardCode($this->requiredFields['cardCvv']);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($this->inputFields['invoiceNumber']);
        $order->setDescription("Invoice Payment");

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($this->requiredFields['firstName']);
        $customerAddress->setLastName($this->requiredFields['lastName']);
        $customerAddress->setCompany($this->requiredFields['company']);
        $customerAddress->setAddress($this->requiredFields['billingAddress']);
        $customerAddress->setCity($this->requiredFields['billingCity']);
        $customerAddress->setState($this->requiredFields['billingState']);
        $customerAddress->setZip($this->requiredFields['billingZip']);
        $customerAddress->setCountry($this->requiredFields['billingCountry']);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        //$customerData->setType("individual");
        //$customerData->setId("99999456654");
        $customerData->setEmail($this->requiredFields['emailAddress']);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($this->inputFields['invoiceAmount']);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);
        if($TestMode == 'TEST'){
            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        }else{
            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        }

        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == 'Ok') {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != '') {
                    $message['RESPONSE'] = 'OK';
                    $message['type']     = 'single payment';
                    $message['details']  = [
                            'transaction_id' => $tresponse->getTransId(),
                            'response_code'  => $tresponse->getResponseCode(),
                            //'message_code'   => $tresponse->getMessages()->getCode(),
                            'auth_code'      => $tresponse->getAuthCode(),
                            //'description'    => $tresponse->getMessages()->getDescription(),
                        ];
                    $message['payment_info'] = [
                        'requiredFields' => $this->requiredFields,
                        'inputFields'    => $this->inputFields
                    ];
                    return $message;
                } else {
                    if ($tresponse->getErrors() != null) {
                        return [
                            'RESPONSE' => 'ERROR',
                            'type'     => 'single payment',
                            'details'  => [
                                'error_code'    => $tresponse->getErrors()[0]->getErrorCode(),
                                'error_message' => $tresponse->getErrors()[0]->getErrorText()
                            ]
                        ];
                    }
                }
                // Or, print errors if the API request wasn't successful
            } else {
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return [
                        'RESPONSE' => 'ERROR',
                        'type'     => 'single payment',
                        'details'  => [
                            'error_code'    => $tresponse->getErrors()[0]->getErrorCode(),
                            'error_message' => $tresponse->getErrors()[0]->getErrorText()
                        ]
                    ];
                } else {
                    return [
                        'RESPONSE' => 'ERROR',
                        'type'     => 'single payment',
                        'details'  => [
                            'error_code'    => $response->getMessages()->getMessage()[0]->getCode(),
                            'error_message' => $response->getMessages()->getMessage()[0]->getText()
                        ]
                    ];
                }
            }
        } else {
            return [
                'RESPONSE' => 'ERROR',
                'type'     => 'single payment',
                'details'  => [
                    'error_code'    => 0,
                    'error_message' => 'No response returned.'
                ]
            ];
        }
    }

    protected function createRecurringPayment($merchantAuthentication)
    {
        $refId = 'ref' . time();

        // Subscription Type Info
        $subscription = new AnetAPI\ARBSubscriptionType();
        $subscription->setName($this->inputFields['service_name']);
        $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
        $interval->setLength($this->inputFields['serviceTerm']);
        $interval->setUnit($this->inputFields['serviceTermType']);
        $paymentSchedule = new AnetAPI\PaymentScheduleType();
        $paymentSchedule->setInterval($interval);
        $paymentSchedule->setStartDate(new \DateTime(date('Y-m-d')));
        $paymentSchedule->setTotalOccurrences("999"); //HOW LONG?
        $paymentSchedule->setTrialOccurrences("0"); //TRIAL?
        $subscription->setPaymentSchedule($paymentSchedule);
        $subscription->setAmount($this->inputFields['serviceAmount']);
        $subscription->setTrialAmount("0.00");

        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($this->requiredFields['cardNumber']);
        $creditCard->setExpirationDate($this->requiredFields['expirationYear'] . "-" . $this->requiredFields['expirationMonth']);
        $payment = new AnetAPI\PaymentType();
        $payment->setCreditCard($creditCard);
        $subscription->setPayment($payment);
        $order = new AnetAPI\OrderType();
        //$order->setInvoiceNumber("1234354");
        $order->setDescription($this->inputFields['service_name']);
        $subscription->setOrder($order);

        $billTo = new AnetAPI\NameAndAddressType();
        $billTo->setFirstName($this->requiredFields['firstName']);
        $billTo->setLastName($this->requiredFields['lastName']);
        $subscription->setBillTo($billTo);
        $request = new AnetAPI\ARBCreateSubscriptionRequest();
        $request->setmerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setSubscription($subscription);
        $controller = new AnetController\ARBCreateSubscriptionController($request);

        if($TestMode == 'TEST'){
            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        }else{
            $response   = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        }

        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok")) {
            return [
                'RESPONSE' => 'OK',
                'type'     => 'recurring payment',
                'details'  => [
                    'description'     => $response->getMessages()->getMessage()[0]->getText(),
                    'subscription_id' => $response->getSubscriptionId()
                ]
            ];
        } else {
            $errorMessages = $response->getMessages()->getMessage();

            return [
                'RESPONSE' => 'ERROR',
                'type'     => 'recurring payment',
                'details'  => [
                    'error_code' => $errorMessages[0]->getCode(),
                    'message'    => $errorMessages[0]->getText()
                ]
            ];
        }
    }
}