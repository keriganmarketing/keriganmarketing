<?php /*
 Admin Settings
 */
use KMAPaymentCenter\PluginConfig;
?>
<link rel="stylesheet" media="screen" href="<?php echo get_site_url(); ?>/wp-content/plugins/KMAPaymentCenter/css/admin-style.css" />
<?php
$kmapcIntroText        = get_option('kmapc_intro_text');
$kmapcTyText           = get_option('kmapc_ty_text');
$kmapcCurrency         = get_option('kmapc_currency');
$kmapcAdminEmail       = get_option('kmapc_admin_email');
$kmapcShowCommentField = get_option('kmapc_show_comment_field');
$kmapcShowDdText       = get_option('kmapc_show_dd_text');
$kmapcProcessor        = get_option('kmapc_processor');
$kmapcTest             = get_option('kmapc_test');

//form submitted
if(isset($_POST['kmapc_submit_settings']) && $_POST['kmapc_submit_settings'] == 'yes'){
    update_option('kmapc_intro_text', isset($_POST['kmapc_intro_text']) ? stripslashes($_POST['kmapc_intro_text']) : $kmapcIntroText);
    update_option('kmapc_ty_text', isset($_POST['kmapc_ty_text']) ? stripslashes($_POST['kmapc_ty_text']) : $kmapcTyText);
    update_option('kmapc_currency', isset($_POST['kmapc_currency']) ? $_POST['kmapc_currency'] : $kmapcCurrency);
    update_option('kmapc_admin_email', isset($_POST['kmapc_admin_email']) ? $_POST['kmapc_admin_email'] : $kmapcAdminEmail);
    update_option('kmapc_show_comment_field', isset($_POST['kmapc_show_comment_field']) ? $_POST['kmapc_show_comment_field'] : $kmapcShowCommentField);
    update_option('kmapc_show_dd_text', isset($_POST['kmapc_show_dd_text']) ? $_POST['kmapc_show_dd_text'] : $kmapcShowDdText);
    update_option('kmapc_processor', isset($_POST['kmapc_processor']) ? $_POST['kmapc_processor'] : $kmapcProcessor);
    update_option('kmapc_test', isset($_POST['kmapc_test'][$_POST['kmapc_processor']]) ? $_POST['kmapc_test'][$_POST['kmapc_processor']] : $_POST['AHAM']);
}

?>
<div class="page-wrapper" style="margin-left:-20px;">
    <div class="hero is-dark">
        <div class="hero-body">
            <div class="columns is-vcentered">
                <div class="column">
                    <p class="title">
                        Settings
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
                <?php _e("Here you define your Payment Terminal settings such as your e-mail address associated with your merchant account, currency, thank you message and other settings." ); ?>
                <form enctype="multipart/form-data" name="kmapc_form" id="kmapc_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
                    <input type="hidden" name="kmapc_submit_settings" value="yes">

                    <div class="editor-wrapper">
                        <h4 class="wp-heading-inline"><?php _e("Authorize.net API Credentials: " ); ?></h4>
                        <p class="subtitle"><i>TIP:</i> if you don't know where to get them, <a href="https://account.authorize.net/" target="_blank">click here</a></p>
                        <input type="hidden" name="kmapc_processor" id="BW-change-kmapc_processor" value="<?php echo (isset($_GET['active_terminal']) ? $_GET['active_terminal'] : $kmapcProcessor); ?>" />
                        <INPUT TYPE="HIDDEN" NAME="AHAM" VALUE="<?php echo $kmapcTest; ?>" />

                        <?php

                        $config = new PluginConfig();
                        $paymentTerminals = $config->setTerminalState($config->getVar('processorConfig'), 'default');

                        $i=1;
                        foreach($paymentTerminals as $terminal => $details)
                        {
                            if($terminal != "CUSTOM_ERROR")
                            {
                                ?>
                                <div id="terminal_<?php echo $i; ?>" class="terminal-holder<?php echo $config->displaySelectedCondition($i,$kmapcProcessor); ?>" >
                                    <div class="columns is-multiline">
                                    <?php
                                    foreach($details as $case => $fields) { ?>
                                        <div class="column is-6">
                                        <?php if ($case == "LIVE" || $case == "TEST") { ?>
                                            <div class="card kmapc-terminal-<?php echo strtolower($case); ?> " >
                                                <header class="card-header">
                                                    <p class="card-header-title"><?php echo $case." MODE"; ?></p>
                                                </header>
                                                <div class="card-content kmapc-terminal-field-wrap<?php if($kmapcTest == "2" && $case == "LIVE") : echo " active"; elseif($kmapcTest == "1" && $case == "TEST") : echo " active"; endif; ?>">
                                                    <?php foreach($fields as $field_name => $field_value) { ?>

                                                        <?php
                                                        $input_id = strtolower(str_replace(array(' ','"','\'','.'), '_', $terminal."_".$case."_".$field_name));
                                                        $filter = $config->isPluginFilter($field_name);
                                                        if($filter != null)
                                                        {
                                                            $field_name = $filter;
                                                            $input_id = $config->isPluginFilter($input_id);
                                                            ?>
                                                            <div class="field">
                                                                <label class="label" for="<?php echo $input_id; ?>"><?php echo __(strtoupper($field_name)); ?>:</label>
                                                                <div class="control">
                                                                    <input onchange="jQuery('#trigger_<?php echo $input_id; ?>').addClass('button-disabled').text('File Selected !');" class="input" style="display:none;width: 185px !important;height: 30px !important;" type="file" autocomplete="off" id="<?php echo $input_id; ?>" name="<?php echo $input_id; ?>"  />
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <div class="field">
                                                            <label for="<?php echo $input_id; ?>"><?php echo __(strtoupper($field_name)); ?>:</label>
                                                                <div class="control">
                                                                    <input type="text" autocomplete="off" id="<?php echo $input_id; ?>" name="<?php echo $input_id; ?>" value="<?php echo $field_value; ?>" />
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                                <div class="card-footer" >
                                                    <button type="submit" class="card-footer-item" name="kmapc_test[<?php echo $i; ?>]" value="<?php echo ($case == "TEST" ? 1 : 2); ?>" title="<?php if(($kmapcTest == "2" && $case == "LIVE") || ($kmapcTest == "1" && $case == "TEST")){echo $case." Settings are ON !";} ?>" >
                                                        <?php if($kmapcTest != "2" && $case == "LIVE"){ _e(" Turn "); } elseif($kmapcTest != "1" && $case == "TEST"){ _e(" Turn  "); } ?><?php echo __($case); ?><?php _e(' Settings ');?><?php if($kmapcTest == "2" && $case == "LIVE"){ _e(" Are "); } elseif($kmapcTest == "1" && $case == "TEST") {_e("Are"); } ?><?php _e(' On '); ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    </div>
                                </div>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                    </div>

                    <div class="editor-wrapper">
                        <h4 class="wp-heading-inline">Currency</h4>
                        <p class="subtitle">Only Authorize.net accepted currencies are shown.</p><br>
                        <div class="control">
                            <div class="select">
                            <select name="kmapc_currency">
								<option value="CZK" <?php echo $kmapcCurrency=="CZK"?"selected":""?>>Czech Koruna (CZK)</option>
								<option value="DKK" <?php echo $kmapcCurrency=="DKK"?"selected":""?>>Danish Krone (DKK)</option>
								<option value="HKD" <?php echo $kmapcCurrency=="HKD"?"selected":""?>>Hong Kong Dollar (HKD)</option>
								<option value="HUF" <?php echo $kmapcCurrency=="HUF"?"selected":""?>>Hungarian Forint (HUF)</option>
								<option value="JPY" <?php echo $kmapcCurrency=="JPY"?"selected":""?>>Japanese Yen  (JPY)</option>
								<option value="NOK" <?php echo $kmapcCurrency=="NOK"?"selected":""?>>Norwegian Krone (NOK)</option>
								<option value="PLN" <?php echo $kmapcCurrency=="PLN"?"selected":""?>>Polish Zloty (PLN)</option>
								<option value="SGD" <?php echo $kmapcCurrency=="SGD"?"selected":""?>>Singapore Dollar (SGD)</option>
								<option value="SEK" <?php echo $kmapcCurrency=="SEK"?"selected":""?>>Swedish Krona (SEK)</option>
								<option value="CHF" <?php echo $kmapcCurrency=="CHF"?"selected":""?>>Swiss Franc (CHF)</option>
                                <option value="USD" <?php echo $kmapcCurrency=="USD" || $kmapcCurrency =="" ? "selected":""?>>United States Dollar (USD)</option>
                                <option value="CAD" <?php echo $kmapcCurrency=="CAD"?"selected":""?>>Canadian Dollar (CAD)</option>
                                <option value="GBP" <?php echo $kmapcCurrency=="GBP"?"selected":""?>>British Pound (GBP)</option>
                                <option value="EUR" <?php echo $kmapcCurrency=="EUR"?"selected":""?>>Euro (EUR)</option>
                                <option value="AUD" <?php echo $kmapcCurrency=="AUD"?"selected":""?>>Australian Dollar (AUD)</option>
                                <option value="NZD" <?php echo $kmapcCurrency=="NZD"?"selected":""?>>New Zealand Dollar (NZD)</option>
                            </select>
                            </div>
                        </div>
                    </div>

                    <div class="editor-wrapper">
                        <h4 class="wp-heading-inline">Admin Notification Email</h4>
                        <p class="subtitle">Who should be notified when a payment is completed on the website?</p><br>
                        <div class="control">
                            <input type="text"  class="input" name="kmapc_admin_email" value="<?php echo $kmapcAdminEmail; ?>" size="40">
                        </div>
                    </div>

                    <div class="editor-wrapper">
                        <h4 class="wp-heading-inline">Form Intro Text</h4>
                        <p class="subtitle">Shows above the payment form before it is submitted.</p>
                        <div class="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" >
                            <?php wp_editor($kmapcIntroText, $editor_id = 'kmapc_intro_text',
                                $settings = [
                                    'media_buttons' => false,
                                    'editor_height' => 150
                                ]
                            ); ?>
                        </div>
                    </div>

                    <div class="editor-wrapper">
                        <h4 class="wp-heading-inline">Form Submitted Text</h4>
                        <p class="subtitle">Shows after the payment form has been submitted.</p>
                        <div class="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" >
                            <?php wp_editor($kmapcTyText, $editor_id = 'kmapc_ty_text',
                                $settings = [
                                    'media_buttons' => false,
                                    'editor_height' => 150
                                ]
                            ); ?>
                        </div>
                    </div>

                    <p class="submit"><input class="button is-primary" type="submit" name="Submit" value="<?php _e('Update Settings') ?>" /></p>
                </form>
            </div>
        </div>
    </section>
    <p style="clear:both;"></p>
</div>