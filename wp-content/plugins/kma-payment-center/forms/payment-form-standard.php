<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.2/css/bulma.min.css">
<style>
    .pane {
        padding: 2rem 0;
        border-bottom: 1px solid #DDD;
        margin-bottom: 2rem;
    }

    #service_amount {
        min-width: unset;
        max-width: unset;
        width: 85px;
        font-weight: bold;
        text-align: right;
    }

    #invoice_amount {
        font-weight: bold;
        text-align: right;
    }
</style>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        let whattopay = $('#what_to_pay'),
          payinvoice = $('#payinvoice'),
          recurringservice = $('#recurringservice'),
          serviceinput = $('#service')

        if (whattopay.val() !== '') {
            changeType(whattopay)
        }
        whattopay.change(function () {
            if (whattopay.val() !== '') {
                changeType(whattopay)
            }
        })

        function changeType (input) {
            if (input.val() === 'invoice') {
                payinvoice[0].style.display = 'flex'
                recurringservice[0].style.display = 'none'
            }
            if (input.val() === 'recurring-service') {
                payinvoice[0].style.display = 'none'
                recurringservice[0].style.display = 'flex'
            }
        }

        if (serviceinput.val() !== '') {
            setTermValues()
        }
        serviceinput.change(function () {
            if (serviceinput.val() !== '') {
                setTermValues()
            }
        })

        function setTermValues () {
            let source = $('#service')[0],
              key = source.value

            let id = source.options[key].dataset.id,
              price = source.options[key].dataset.price,
              term = source.options[key].dataset.term,
              termtype = source.options[key].dataset.termType

            $('#service_amount').val(price)
            $('#service_term').val(term)
            $('#service_term_type').val(termtype)

            let service_term = $('#service_term_display')
            if (term === 1) {
                if (termtype === 'months') {
                    service_term.html('per month')
                }
                if (termtype === 'days') {
                    service_term.html('per day')
                }
                if (termtype === 'years') {
                    service_term.html('per year')
                }
            } else {
                service_term.html('every ' + term + ' ' + termtype)
            }
        }

    })
</script>
<form id="ff1" name="ff1" method="post" action="" enctype="multipart/form-data" class="anpt_form">
    <h2 class="title current">Payment Information</h2>
    <div class="pane">
        <div class="columns is-multiline">
            <div class="column is-12">
                <label class="label" for="what_to_pay">What would you like to pay for?</label>
                <div class="field">
                    <div class="control">
                        <div class="select">
                            <select name="what_to_pay" id="what_to_pay" class="select">
                                <option value="" <?php echo($whatToPay == '' ? 'selected' : ''); ?> >Select one</option>
                                <option value="invoice" <?php echo($whatToPay == 'invoice' ? 'selected' : ''); ?> >Pay
                                    an invoice
                                </option>
                                <option value="recurring-service" <?php echo($whatToPay == 'recurring-service' ? 'selected' : ''); ?> >
                                    Recurring services
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="recurringservice" class="columns is-multiline" style="display: none;">
            <?php
            $paymentServices = new KMAPaymentCenter\PaymentServices();
            $currentServices = $paymentServices->getServices();
            //echo '<pre>',print_r($currentServices[0]),'</pre>';

            $serviceOptions = '';
            for ($key = 0; $key < count($currentServices); $key++) {
                $serviceOptions .= '<option data-id="' . $currentServices[$key]->kmapc_services_id . '" value="' . ((int)$key + 1) . '" 
	            data-term="' . $currentServices[$key]->kmapc_services_recurring_period_number . '"
	            data-term-type="' . $currentServices[$key]->kmapc_services_recurring_period_type . '"
	            data-price="' . $currentServices[$key]->kmapc_services_price . '" >' . $currentServices[$key]->kmapc_services_title . '</option>';
            }

            ?>
            <div class="column is-6">
                <label class="label" for="service">Service:</label>
                <div class="field">
                    <div class="control">
                        <div class="select">
                            <select id="service" name="service" class="select">
                                <option value="">Select one</option>
                                <?php echo $serviceOptions; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column is-6">
                <label class="label" for="service_amount">Amount:</label>
                <div class="field has-addons">
                    <p class="control">
                        <a class="button is-static">$</a>
                    </p>
                    <p class="control">
                        <input name="service_amount" id="service_amount" type="text" class="input small-field" readonly
                               value=""/>
                    </p>
                    <p class="control">
                        <a class="button is-static" id="service_term_display"></a>
                    </p>
                </div>
                <input type="hidden" name="service_term" id="service_term" value="">
                <input type="hidden" name="service_term_type" id="service_term_type" value="">
            </div>
        </div>
        <div id="payinvoice" class="columns is-multiline" style="display: none;">
            <div class="column is-6">
                <label class="label" for="invoice_number">Invoice number:</label>
                <div class="field">
                    <p class="control">
                        <input name="invoice_number" id="invoice_number" type="text" class="input small-field"
                               value="<?php echo $invoiceNumber; ?>"/>
                    </p>
                </div>
            </div>
            <div class="column is-6">
                <label class="label" for="invoice_amount">Amount:</label>
                <div class="field has-addons">
                    <p class="control">
                        <a class="button is-static">$</a>
                    </p>
                    <p class="control">
                        <input name="invoice_amount" id="invoice_amount" type="text" class="input small-field"
                               value="<?php echo $invoiceAmount; ?>"/>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="title">Credit Card Information</h2>
    <div class="pane">
        <div class="columns is-multiline">
            <div class="column is-5">
                <label class="label" for="card_number">Card Number: *</label>
                <div class="control">
                    <input name="card_number" id="card_number" type="text" class="input long-field" maxlength="16"/>
                </div>
            </div>
            <div class="column is-narrow">
                <label class="label">Expiration Date: *</label>
                <div class="control">
                    <div class="select">
                        <label for="expiration_month">
                            <select name="expiration_month" id="expiration_month" class="small-field">
                                <?php for ($i = 1; $i <= 12; $i++) {
                                    $month = (strlen($i) == 1 ? '0' . $i : $i); ?>
                                    <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                                <?php } ?>
                            </select>
                        </label>
                    </div>
                    <div class="select">
                        <label for="expiration_year">
                            <select name="expiration_year" id="expiration_year" class="small-field">
                                <?php for ($i = date("Y"); $i < date("Y", strtotime(date("Y") . " +10 years")); $i++) {
                                    echo '<option value="' . $i . '" >' . $i . '</option>';
                                } ?>
                            </select>
                        </label>
                    </div>
                </div>
            </div>
            <div class="column is-3">
                <label class="label" for="card_cvv">CVV: *</label>
                <div class="field">
                    <div class="control">
                        <input name="card_cvv" id="card_cvv" type="text" maxlength="5" class="input small-field"/>
                    </div>
                </div>
            </div>
            <div class="column is-12">
                <label class="label" for="billing_name">Name on Card: *</label>
                <div class="control">
                    <input name="billing_name" id="billing_name" type="text" class="input long-field"
                           value="<?php echo $billingName; ?>"/>
                </div>
                <p>&nbsp;</p>
            </div>

        </div>

    </div>

    <h2 class="title">Billing Information</h2>
    <div class="pane">
        <div class="columns is-multiline">
            <div class="column is-6">
                <label class="label" for="first_name">First Name: *</label>
                <input name="first_name" id="first_name" type="text" class="input long-field"
                       value="<?php echo $firstName; ?>"/>
            </div>
            <div class="column is-6">
                <label class="label" for="last_name">Last Name: *</label>
                <input name="last_name" id="last_name" type="text" class="input long-field"
                       value="<?php echo $lastName; ?>"/>
            </div>
            <div class="column is-6">
                <label class="label" for="email_address">E-mail: *</label>
                <input name="email_address" id="email_address" type="text" class="input long-field"
                       value="<?php echo $emailAddress; ?>"/>
            </div>
            <div class="column is-6">
                <label class="label" for="company">Company Name:</label>
                <input name="company" id="company" type="text" class="input long-field"
                       value="<?php echo $company; ?>"/>
            </div>

            <div class="column is-8">
                <label class="label" for="billing_address">Address: *</label>
                <input name="billing_address" id="billing_address" type="text" class="input long-field"
                       value="<?php echo $billingAddress; ?>"/>
            </div>

            <div class="column is-4">
                <label class="label" for="billing_city">City: *</label>
                <input name="billing_city" id="billing_city" type="text" class="input long-field"
                       value="<?php echo $billingCity; ?>"/>
            </div>
            <div class="column is-4">
                <label class="label" for="billing_state">State/Province: *</label>
                <div class="control">
                    <div class="select is-fullwidth">
                        <select style="width:250px;" name="billing_state" id="billing_state" class="long-field">
                            <?php include('inc/state-select.php'); ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="column is-4">
                <label class="label" for="billing_zip">ZIP/Postal Code: *</label>
                <input name="billing_zip" id="billing_zip" type="text" class="input small-field"
                       value="<?php echo $billingZip; ?>"/>
            </div>
            <div class="column is-4">
                <label class="label" for="billing_country">Country: *</label>
                <div class="control">
                    <div class="select is-fullwidth">
                        <select style="width:250px;" name="billing_country" id="billing_country" class="long-field">
                            <?php include('inc/country-select.php'); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <div class="columns is-multiline">
            <div class="column is-12">
                <input type="hidden" name="form_submitted" value="yes"/>
                <input type="hidden" name="form_sec" value=""/>
                <div class="submit-btn">
                    <button type="submit" name="submit" class="button is-primary">Submit Payment</button>
                </div>
            </div>
        </div>
    </div>
</form>