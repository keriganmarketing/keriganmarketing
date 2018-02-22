<?php

namespace KMAPaymentCenter;

class FormValidation
{
    protected $requiredFields;
    protected $inputFields;
    public $valid;
    public $errors;

    public function __construct()
    {
        $this->valid                             = true;
        $this->inputFields['service']            = (isset($_REQUEST["service"]) ? $_REQUEST["service"] : null);
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

        $this->checkAmount();
        $this->checkBlanks();
        $this->cardIsNumber();
        $this->cvvIsNumber();
        $this->checkCardDates();
        $this->validateCardNumber();

        return $this->valid;
    }

    protected function checkAmount()
    {
        if($this->inputFields['whatToPay'] == ''){
            $this->valid    = false;
            $this->errors[] = 'Please tell us what you are paying for.';
        }elseif($this->inputFields['whatToPay'] == 'invoice'){
            if ($this->inputFields['invoiceAmount'] == '') {
                $this->valid    = false;
                $this->errors[] = 'You must enter an amount when paying an invoice.';
            }
            if ($this->inputFields['invoiceNumber'] == '') {
                $this->valid    = false;
                $this->errors[] = 'You must enter an invoice number when paying an invoice.';
            }
        }elseif($this->inputFields['whatToPay'] == 'recurring-service'){
            if ($this->inputFields['service'] == '') {
                $this->valid    = false;
                $this->errors[] = 'You must select a service.';
            }
        }
    }

    protected function checkBlanks()
    {
        foreach ($this->requiredFields as $fieldName => $fieldValue) {
            if ($fieldValue == '') {
                $this->valid    = false;
                $this->errors[] = 'One or more required fields were blank.';
                break;
            }
        }
    }

    protected function cardIsNumber()
    {
        if ( ! is_numeric($this->requiredFields['cardNumber'])) {
            $this->valid    = false;
            $this->errors[] = 'Credit Card number can contain numbers only.';
        }
    }

    protected function cvvIsNumber()
    {
        if ( ! is_numeric($this->requiredFields['cardCvv'])) {
            $this->valid    = false;
            $this->errors[] = 'CID/CCV number can contain numbers only.';
        }
    }

    protected function checkCardDates()
    {
        if (date("Y-m-d",
                strtotime($this->requiredFields['expirationYear'] . "-" . $this->requiredFields['expirationMonth'] . "-01")) < date("Y-m-d")) {
            $this->valid    = false;
            $this->errors[] = 'Your credit card has expired.';
        }
    }

    //Use Luhn algorithm to check card number
    protected function validateCardNumber()
    {
        $number        = preg_replace('/\D/', '', $this->requiredFields['cardNumber']);
        $number_length = strlen($number);
        $parity        = $number_length % 2;
        $total         = 0;
        for ($i = 0; $i < $number_length; $i++) {
            $digit = $number[$i];
            if ($i % 2 == $parity) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $total += $digit;
        }

        if ($total % 10 != 0) {
            $this->valid    = false;
            $this->errors[] = 'Your credit card number is invalid.';
        }
    }

}