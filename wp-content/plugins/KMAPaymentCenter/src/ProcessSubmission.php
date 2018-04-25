<?php

namespace KMAPaymentCenter;

class ProcessSubmission
{
    public function __construct()
    {
        $validator = new FormValidation();
        if ($validator->valid === false) {
            $this->showError($validator->errors);
        } else {
            $payment = new ProcessPayment();
            if ($payment->return['RESPONSE'] == 'OK') {
                $this->showSuccess($payment->return);
            } elseif ($payment->return['RESPONSE'] == 'ERROR') {
                $this->showError($payment->return['details']);
            }
        }
    }

    protected function showSuccess($message)
    {
        echo '<div class="alert alert-success text-left" role="alert" >';
        if ($message['type'] == 'single payment') {
            echo $message['details']['description'] . ' You will receive an email receipt and your transaction ID is ' . $message['details']['transaction_id'];
            $payment = 'invoice ' . $message['payment_info']['inputFields']['invoiceNumber'] . ' ($'. number_format($message['payment_info']['inputFields']['invoiceAmount']) . ')';
        }
        if ($message['type'] == 'recurring payment') {
            echo $message['details']['description'] . ' You will receive an email receipt and your subdcription ID is ' . $message['details']['subscription_id'];
            $payment = $message['payment_info']['inputFields']['service_name'] . ' ($'. number_format($message['payment_info']['inputFields']['serviceAmount']) . ' recurring)';
        }
        echo '</div>';

        $data = '<table style="width: 100%" border="0" class="data">
                    <tr><td width="20%"><strong>Paid For</strong></td><td>'.$payment.'</td></tr>
                    <tr><td><strong>First Name<strong></td><td>'.$message['payment_info']['requiredFields']['firstName'].'</td></tr>
                    <tr><td><strong>Last Name<strong></td><td>'.$message['payment_info']['requiredFields']['lastName'].'</td></tr>
                    <tr><td><strong>Company<strong></td><td>'.$message['payment_info']['requiredFields']['company'].'</td></tr>
                  </table>';

        new Notifications([
            'to'      => 'bryan@kerigan.com',
            'from'    => 'noreply@kerigan.com',
            'subject' => 'Website Payment Completed',
            'cc'      => '',
            'bcc'     => 'support@kerigan.com',
            'replyto' => $message['payment_info']['requiredFields']['emailAddress'],
            'content' => '<h2>There was a payment submitted on the website</h2>' . $data
        ]);

        new Notifications([
            'to'      => $message['payment_info']['requiredFields']['emailAddress'],
            'from'    => 'noreply@kerigan.com',
            'subject' => 'Payment Completed',
            'cc'      => '',
            'bcc'     => 'support@kerigan.com',
            'replyto' => 'accounting@kerigan.com',
            'content' => '<h2>Thank you for paying online. What you submitted is below.</h2>' . $data
        ]);

    }

    protected function showError($errors)
    {
        echo '<div class="alert alert-danger text-left" role="alert">There were errors in your submission. Please check the following and retry your payment.<ul>';
        foreach ($errors as $key => $error) {
            echo '<li>' . (! is_numeric($key) ? $key . ': ' : '') . $error . '</li>';
        }
        echo '</ul></div>';
    }
}