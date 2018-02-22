<?php
/**
 * Created by PhpStorm.
 * User: Bryan
 * Date: 2/21/2018
 * Time: 4:56 PM
 */

namespace KMAPaymentCenter;


class Notifications
{
    public function __construct($emailData)
    {
        $this->sendEmail(
            [
                'to'      => $emailData['to'],
                'from'    => $emailData['from'],
                'subject' => $emailData['subject'],
                'cc'      => $emailData['cc'],
                'bcc'     => $emailData['bcc'],
                'replyto' => $emailData['replyto'],
                'content' => $emailData['content']
            ]
        );
    }

    protected function createEmailTemplate($emailData)
    {
        $eol           = "\r\n";
        $emailTemplate = file_get_contents(wp_normalize_path(dirname(dirname(__FILE__)) . '/templates/emailtemplate.php'));
        $emailTemplate = str_replace('{content}', $eol . $emailData['content'] . $eol, $emailTemplate);

        return $emailTemplate;
    }

    public function sendEmail($emailData = [])
    {
        $eol           = "\r\n";
        $emailTemplate = $this->createEmailTemplate($emailData);
        $headers       = 'From: ' . $emailData['from'] . $eol;
        $headers       .= (isset($emailData['cc']) ? 'Cc: ' . $emailData['cc'] . $eol : '');
        $headers       .= (isset($emailData['bcc']) ? 'Bcc: ' . $emailData['bcc'] . $eol : '');
        $headers       .= (isset($emailData['replyto']) ? 'Reply-To: ' . $emailData['replyto'] . $eol : '');
        $headers       .= 'MIME-Version: 1.0' . $eol;
        $headers       .= 'Content-type: text/html; charset=utf-8' . $eol;

        wp_mail($emailData['to'], $emailData['subject'], $emailTemplate, $headers);
    }
}