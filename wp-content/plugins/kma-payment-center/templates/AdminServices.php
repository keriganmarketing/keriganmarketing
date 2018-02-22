<?php /*
 Admin Services
 */

use KMAPaymentCenter\PluginConfig;
use KMAPaymentCenter\PaymentServices;

$paymentServices = new PaymentServices();
?>
<link rel="stylesheet" media="screen"
      href="<?php echo get_site_url(); ?>/wp-content/plugins/KMAPaymentCenter/css/admin-style.css"/>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('.toggle-recurring').click(function () {
            if ($(this).val() == '1') {
                $('#recurringDiv')[0].style.display = 'block'
            } else {
                $('#recurringDiv')[0].style.display = 'none'
            }
        })
    })
</script>
<div class="page-wrapper" style="margin-left:-20px;">
    <div class="hero is-dark">
        <div class="hero-body">
            <div class="columns is-vcentered">
                <div class="column">
                    <p class="title">
                        Services
                    </p>
                </div>
                <div class="column is-narrow">
                    <?php include('KMASig.php'); ?>
                </div>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="container is-fluid">
            <div class="content">
                <?php _e("Here you can create and manage a basic list of products, services or events you'd like to accept payments for."); ?>
                <?php
                $paymentServices->handleState($_GET);
                $editingService = $paymentServices->getEditFromState($_GET);

                $kmapcServicesTitle                 = (isset($_GET['change']) && $_GET['change'] == 1 ? $editingService->kmapc_services_title : '');
                $kmapcServicesDesc                  = (isset($_GET['change']) && $_GET['change'] == 1 ? $editingService->kmapc_services_descr : '');
                $kmapcServicesPrice                 = (isset($_GET['change']) && $_GET['change'] == 1 ? $editingService->kmapc_services_price : '');
                $kmapcServicesRecurring             = (isset($_GET['change']) && $_GET['change'] == 1 ? $editingService->kmapc_services_recurring : '');
                $kmapcServicesRecurringPeriodType   = (isset($_GET['change']) && $_GET['change'] == 1 ? $editingService->kmapc_services_recurring_period_type : '');
                $kmapcServicesRecurringPeriodNumber = (isset($_GET['change']) && $_GET['change'] == 1 ? $editingService->kmapc_services_recurring_period_number : '');
                $kmapcServicesRecurringTrial        = (isset($_GET['change']) && $_GET['change'] == 1 ? $editingService->kmapc_services_recurring_trial : '');
                $kmapcServicesRecurringTrialDays    = (isset($_GET['change']) && $_GET['change'] == 1 ? $editingService->kmapc_services_recurring_trial_days : '');

                //form submitted
                if ((isset($_POST['kmapc_submit_service']) && $_POST['kmapc_submit_service'] == 'yes')) {
                    echo '<article class="message"><div class="message-body">
                        <button class="delete is-pulled-right" aria-label="delete"></button>' . $paymentServices->handleSubmit($_GET,
                            $_POST) . '</div>
                    </article>';
                }

                ?>
                <form name="kmapc_form" method="post"
                      action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                    <input type="hidden" name="kmapc_submit_service" value="yes">
                    <input type="hidden" name="kmapc_form_state" value="<?php echo $paymentServices->state; ?>">
                    <input type="hidden" name="kmapc_edit_id" value="<?php echo $editingService->kmapc_services_id; ?>">
                    <div class="editor-wrapper">
                        <h4 class="wp-heading-inline"><?php echo($paymentServices->state == 'change' ? 'Editing ' . $kmapcServicesTitle . '<a class="button is-pulled-right" href="/wp-admin/admin.php?page=payment-services" >Add a new service instead</a>' : 'Add New Service'); ?></h4>
                        <p class="subtitle">This is what customers will see in the services dropdown to select from when
                            they decide to pay.</p><br>
                        <div class="control">
                            <input type="text" class="input" name="kmapc_services_title"
                                   value="<?php echo $kmapcServicesTitle; ?>" placeholder="Service Name">
                        </div>
                        <br>

                        <label class="label" for="kmapc_services_recurring">Is recurring service?</label>
                        <div class="control">
                            <label class="radio">
                                <input type="radio" class="toggle-recurring" value="1"
                                       name="kmapc_services_recurring" <?php echo $kmapcServicesRecurring == 1 ? 'checked' : ''; ?>>
                                Yes
                            </label>
                            <label class="radio">
                                <input type="radio" class="toggle-recurring" value="0"
                                       name="kmapc_services_recurring" <?php echo $kmapcServicesRecurring == 0 ? 'checked' : ''; ?>>
                                No
                            </label>
                        </div>
                        <br>

                        <div id="recurringDiv"
                             style="<?php echo $kmapcServicesRecurring == 0 ? 'display:none' : 'block'; ?>">
                            <label class="label">Billing period</label>
                            <div class="columns is-multiline">
                                <div class="column is-narrow">
                                    <div class="control">
                                        <div class="select">
                                            <select name="kmapc_services_recurring_period_number" autocomplete="off">
                                                <?php
                                                for ($i = 1; $i < 31; $i++) {
                                                    ?>
                                                    <option value="<?php echo $i; ?>" <?php echo($kmapcServicesRecurringPeriodNumber == $i ? 'selected' : ''); ?>><?php echo $i; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-narrow">
                                    <div class="control">
                                        <div class="select">
                                            <select name="kmapc_services_recurring_period_type" autocomplete="off">
                                                <option value="">Select Period</option>
                                                <?php
                                                $periodOptions = [
                                                    'days',
                                                    'months'
                                                ];
                                                foreach ($periodOptions as $option) { ?>
                                                    <option value="<?php echo $option; ?>" <?php echo($option == $kmapcServicesRecurringPeriodType ? 'selected' : ''); ?>><?php echo $option; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>

                        <label class="label" for="kmapc_services_price">Amount to charge (numbers only)</label>
                        <div class="field has-addons">
                            <p class="control">
                                <a class="button is-static">
                                    $
                                </a>
                            </p>
                            <p class="control">
                                <input type="text" class="input" name="kmapc_services_price"
                                       value="<?php echo $kmapcServicesPrice; ?>"/>
                            </p>
                        </div>
                        <br>

                        <label class="label" for="kmapc_services_descr">Service Description</label>
                        <div class="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>">
                            <?php wp_editor($kmapcServicesDesc, $editor_id = 'kmapc_services_descr',
                                $settings = [
                                    'textarea_name' => 'kmapc_services_descr',
                                    'media_buttons' => false,
                                    'editor_height' => 150
                                ]
                            ); ?>
                        </div>
                        <br>

                        <div class="field">
                            <div class="control">
                                <button type="submit" class="button is-primary">Submit</button>
                            </div>
                        </div>

                    </div>

                    <div class="editor-wrapper">
                        <h4 class="wp-heading-inline">Current Services</h4>
                        <?php
                        $currentServices = $paymentServices->getServices();
                        //echo '<pre>',print_r($currentServices[0]),'</pre>';
                        ?>
                        <div class="columns is-multiline is-level">
                            <?php foreach ($currentServices as $service) { ?>
                                <div class="column is-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <p class="card-header-title"><?php echo $service->kmapc_services_title; ?></p>
                                        </div>
                                        <div class="card-content">
                                            <p>
                                                <strong class="is-size-4">$<?php echo number_format($service->kmapc_services_price); ?></strong> <?php
                                                if ($service->kmapc_services_recurring == 1) { ?>
                                                    every <?php echo $service->kmapc_services_recurring_period_number . ' ' .
                                                                     $service->kmapc_services_recurring_period_type; ?>
                                                <?php } ?>
                                            </p>
                                            <?php if ($service->kmapc_services_descr != '') { ?>
                                                <p><?php echo $service->kmapc_services_descr; ?></p>
                                            <?php } ?>
                                        </div>
                                        <div class="card-footer">
                                            <a class="card-footer-item is-primary"
                                               href="?page=payment-services&change=1&id=<?php echo $service->kmapc_services_id; ?>">Change</a>
                                            <a class="card-footer-item is-danger"
                                               href="?page=payment-services&delete=1&id=<?php echo $service->kmapc_services_id; ?>">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="is-clearfix"></div>
    </section>
    <div class="is-clearfix"></div>
</div>