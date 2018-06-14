<?php
wp_enqueue_script('payment-form-scripts', '/wp-content/plugins/KMAPaymentCenter/forms/js/form.js', [], '0.1', true);
wp_enqueue_style('payment-form-css', '/wp-content/plugins/KMAPaymentCenter/css/form-style.css', [], null, 'all');
?>
<a name="form-submit-anchor" class="pad-anchor top"></a>
<form method="post" enctype="multipart/form-data" style="padding: 0 1rem;">
    <div class="row justify-content-center align-items-center">

        <div class="pane col-lg-10 col-xl-8">
            <h2 class="title current">Payment Information</h2>
            <div class="row justify-content-center align-items-center">
                <div class="col-md-6">
                    <label class="label" for="what_to_pay">What would you like to pay for?</label>
                    <div class="form-group">
                        <select name="what_to_pay" id="what_to_pay" class="select form-control" required>
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
            <div id="recurringservice" class="row justify-content-center align-items-center" style="display: none;">
                <?php
                $paymentServices = new KMAPaymentCenter\PaymentServices();
                $currentServices = $paymentServices->getServices();
                //echo '<pre>',print_r($currentServices[0]),'</pre>';

                $serviceOptions = '';
                for ($key = 0; $key < count($currentServices); $key++) {
                    $serviceOptions .= '<option data-id="' . $currentServices[$key]->kmapc_services_id . '" value="' . ((int)$key + 1) . '"
                data-title="' . $currentServices[$key]->kmapc_services_title . '"
	            data-term="' . $currentServices[$key]->kmapc_services_recurring_period_number . '"
	            data-term-type="' . $currentServices[$key]->kmapc_services_recurring_period_type . '"
	            data-price="' . $currentServices[$key]->kmapc_services_price . '" >' . $currentServices[$key]->kmapc_services_title . '</option>';
                }

                ?>
                <div class="col-md-6 text-left">
                    <label class="label" for="service_name">Service:</label>
                    <div class="form-group">
                        <div class="select">
                            <select id="service_name" name="service_name" class="select form-control">
                                <option value="">Select one</option>
                                <?php echo $serviceOptions; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-left">
                    <label class="label" for="service_amount">Amount:</label>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            <input name="service_amount" id="service_amount" type="text"
                                   class="input small-field form-control"
                                   readonly value=""/>
                            <div class="input-group-addon" id="service_term_display"></div>
                        </div>
                    </div>
                    <input type="hidden" name="service_title" id="service_title" value="">
                    <input type="hidden" name="service_term" id="service_term" value="">
                    <input type="hidden" name="service_term_type" id="service_term_type" value="">
                </div>
            </div>
            <div id="payinvoice" class="row justify-content-center align-items-center" style="display: none;">
                <div class="col-md-6 text-left">
                    <label class="label" for="invoice_number">Invoice number:</label>
                    <div class="form-group">
                        <input name="invoice_number" id="invoice_number" type="text"
                               class="input small-field form-control"
                               value="<?php echo $invoiceNumber; ?>"/>
                    </div>
                </div>
                <div class="col-md-6 text-left">
                    <label class="label" for="invoice_amount">Amount:</label>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            <input name="invoice_amount" id="invoice_amount" type="text"
                                   class="input small-field form-control"
                                   value="<?php echo $invoiceAmount; ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pane col-lg-10 col-xl-8">
            <h2 class="title">Credit Card Information</h2>
            <p class="text-center"><img src="<?php echo $imageDir; ?>/card-icons.png"></p>
            <div class="row justify-content-center align-items-center">
                <div class="col-md-5 text-left">
                    <label class="label" for="card_number">Card Number: </label>
                    <div class="form-group">
                        <input name="card_number" id="card_number" type="text" class="input long-field form-control"
                               maxlength="16" required/>
                    </div>
                </div>
                <div class="col text-left">
                    <label class="label">Expiration Date: </label>
                    <div class="row">
                        <label for="expiration_month"></label>
                        <div class="form-group col-6">
                            <select name="expiration_month" id="expiration_month" class="small-field form-control"
                                    required>
                                <?php for ($i = 1; $i <= 12; $i++) {
                                    $month = (strlen($i) == 1 ? '0' . $i : $i); ?>
                                    <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <label for="expiration_year"></label>
                        <div class="form-group col-6">
                            <select name="expiration_year" id="expiration_year" class="small-field form-control"
                                    required>
                                <?php for ($i = date("Y"); $i < date("Y", strtotime(date("Y") . " +10 years")); $i++) {
                                    echo '<option value="' . $i . '" >' . $i . '</option>';
                                } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-left">
                    <label class="label" for="card_cvv">CID/CVV:
                        <span class="tooltip-button" type="button" data-width="300" data-toggle="tooltip"
                              data-html="true" data-placement="right"
                              title="<div class='large-tooltip'><img class='img-fluid' src='<?php echo $imageDir; ?>/credit-card-cvv.png' ></div>">
                            ?</span>
                    </label>
                    <div class="form-group">
                        <input name="card_cvv" id="card_cvv" type="text" maxlength="5"
                               class="input small-field form-control" required/>
                    </div>
                </div>
                <div class="col-md-12 text-left">
                    <label class="label" for="billing_name">Name on Card: </label>
                    <div class="form-group">
                        <input name="billing_name" id="billing_name" type="text" class="input long-field form-control"
                               value="<?php echo $billingName; ?>" required/>
                    </div>
                </div>

            </div>

        </div>

        <div class="pane col-lg-10 col-xl-8">
            <h2 class="title">Billing Information</h2>
            <div class="row justify-content-center align-items-center">
                <div class="col-md-6 text-left">
                    <div class="form-group">
                        <label class="label" for="first_name">First Name: </label>
                        <input name="first_name" id="first_name" type="text" class="input long-field form-control"
                               value="<?php echo $firstName; ?>" required/>
                    </div>
                </div>
                <div class="col-md-6 text-left">
                    <div class="form-group">
                        <label class="label" for="last_name">Last Name: </label>
                        <input name="last_name" id="last_name" type="text" class="input long-field form-control"
                               value="<?php echo $lastName; ?>" required/>
                    </div>
                </div>
                <div class="col-md-6 text-left">
                    <div class="form-group">
                        <label class="label" for="email_address">E-mail: </label>
                        <input name="email_address" id="email_address" type="text" class="input long-field form-control"
                               value="<?php echo $emailAddress; ?>" required/>
                    </div>
                </div>
                <div class="col-md-6 text-left">
                    <div class="form-group">
                        <label class="label" for="company">Company Name: (optional)</label>
                        <input name="company" id="company" type="text" class="input long-field form-control"
                               value="<?php echo $company; ?>" required/>
                    </div>
                </div>

                <div class="col-md-8 text-left">
                    <div class="form-group">
                        <label class="label" for="billing_address">Address: </label>
                        <input name="billing_address" id="billing_address" type="text"
                               class="input long-field form-control"
                               value="<?php echo $billingAddress; ?>" required/>
                    </div>
                </div>

                <div class="col-md-4 text-left">
                    <div class="form-group">
                        <label class="label" for="billing_city">City: </label>
                        <input name="billing_city" id="billing_city" type="text" class="input long-field form-control"
                               value="<?php echo $billingCity; ?>" required/>
                    </div>
                </div>
                <div class="col-md-4 text-left">
                    <label class="label" for="billing_state">State/Province: </label>
                    <div class="form-group">
                        <select name="billing_state" id="billing_state"
                                class="long-field form-control" required>
                            <?php include('inc/state-select.php'); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 text-left">
                    <div class="form-group">
                        <label class="label" for="billing_zip">ZIP/Postal Code: </label>
                        <input name="billing_zip" id="billing_zip" type="text" class="input small-field form-control"
                               value="<?php echo $billingZip; ?>" required/>
                    </div>
                </div>
                <div class="col-md-4 text-left">
                    <label class="label" for="billing_country">Country: </label>
                    <div class="form-group">
                        <select name="billing_country" id="billing_country"
                                class="long-field form-control" required>
                            <?php include('inc/country-select.php'); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12" style="padding-bottom:2rem;">
            <div class="row justify-content-center align-items-center">
                <div class="col-md-12">
                    <input type="hidden" name="form_submitted" value="yes"/>
                    <input type="hidden" name="form_sec" value=""/>
                    <div class="submit-btn">
                        <button type="submit" name="submit" class="btn btn-primary">Submit Payment</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
